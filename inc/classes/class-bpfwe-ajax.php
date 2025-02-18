<?php
/**
 * Handles the AJAX Functions.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class BPFWE_Ajax
 *
 * Manages AJAX-related functionalities for the plugin.
 * Includes actions such as changing post status, pinning posts, and optimizing filters.
 */
class BPFWE_Ajax {

	/**
	 * Changes the status of a post via AJAX.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function change_post_status() {
		$nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;

		// Check if nonce is set and verify it.
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => 'Access Denied' ) );
		}

		// Get the current date and time.
		$current_date = current_time( 'mysql' );
		$post_status  = get_post_status( $post_id );
		$new_status   = ( 'publish' === $post_status ) ? 'draft' : 'publish';

		// Update post status and publication date.
		$result = wp_update_post(
			array(
				'ID'            => $post_id,
				'post_status'   => $new_status,
				'post_date'     => $current_date,
				'post_date_gmt' => get_gmt_from_date( $current_date ),
			)
		);

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => 'Failed to update post status' ) );
		}

		wp_die();
	}

	/**
	 * Bookmark posts via AJAX.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pin_post() {
		$nonce     = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		$pin_class = isset( $_POST['pin_class'] ) ? sanitize_text_field( wp_unslash( $_POST['pin_class'] ) ) : '';

		// Check if nonce is set and verify it.
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Access Denied' ) );
		}

		$post_id   = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
		$user_id   = get_current_user_id();
		$post_list = [];

		if ( ! empty( $user_id ) ) {
			$post_list = get_user_meta( $user_id, 'post_id_list', true );
			if ( ! is_array( $post_list ) ) {
				$post_list = array();
			}
		} elseif ( isset( $_COOKIE['post_id_list'] ) ) {
				$raw_cookie_data = isset( $_COOKIE['post_id_list'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['post_id_list'] ) ) : '';
				$post_list       = json_decode( $raw_cookie_data, true );

				// Check if json_decode failed or post_list is not an array.
			if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $post_list ) ) {
				$post_list = [];
			}
		}

		$post_list = array_map( 'absint', $post_list );

		$key = array_search( $post_id, $post_list, true );

		if ( str_contains( $pin_class, 'unpin' ) ) {
			if ( false !== $key ) {
				unset( $post_list[ $key ] );
			}
		} elseif ( ! in_array( $post_id, $post_list, true ) ) {
				$post_list[] = $post_id;
		}

		if ( ! empty( $user_id ) ) {
			update_user_meta( $user_id, 'post_id_list', $post_list );
		} else {
			setcookie( 'post_id_list', wp_json_encode( $post_list ), time() + ( 86400 * 30 ), '/' );
		}

		wp_die();
	}

	/**
	 * Deletes cached filter results stored as transients.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function delete_filter_transient() {
		delete_transient( 'bpfwe_filter_query' );
	}

	/**
	 * Recursively sanitize an array.
	 *
	 * @param array $data The array to sanitize.
	 * @param array $sanitization_callbacks Associative array defining the sanitization method per key.
	 * @return array The sanitized array.
	 */
	private function bpfwe_sanitize_nested_data( $data, $sanitization_callbacks ) {
		$sanitized_array = [];

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$sanitized_array[ sanitize_key( $key ) ] = $this->bpfwe_sanitize_nested_data( $value, $sanitization_callbacks );
			} else {
				$sanitized_array[ sanitize_key( $key ) ] = isset( $sanitization_callbacks[ $key ] ) && is_callable( $sanitization_callbacks[ $key ] )
					? call_user_func( $sanitization_callbacks[ $key ], $value )
					: sanitize_text_field( $value );
			}
		}

		return $sanitized_array;
	}

	/**
	 * Retrieves filtered post results based on the specified criteria.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function post_filter_results() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Access Denied' ) );
		}

		$page_id   = ! empty( $_POST['page_id'] ) ? absint( wp_unslash( $_POST['page_id'] ) ) : '';
		$widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_key( wp_unslash( $_POST['widget_id'] ) ) : '';

		if ( empty( $page_id ) || empty( $widget_id ) ) {
			wp_send_json_error( array( 'message' => 'A page and widget ID are recquired.' ) );
		}

		$document     = \Elementor\Plugin::$instance->documents->get( $page_id );
		$element_data = $document->get_elements_data();
		$widget_data  = \Elementor\Utils::find_element_recursive( $element_data, $widget_id );

		// Multidimensional array sanitization and validation.
		$taxonomy_sanitization_rules = [
			'taxonomy' => 'sanitize_text_field',
			'terms'    => function ( $terms ) {
				return array_map( 'absint', (array) $terms ); },
			'logic'    => 'sanitize_text_field',
		];

		$text_sanitization_rules = [
			'taxonomy' => 'sanitize_text_field',
			'terms'    => function ( $terms ) {
				return array_map( 'sanitize_text_field', (array) $terms ); },
			'logic'    => 'sanitize_text_field',
		];

		// Sanitize all arrays with bpfwe_sanitize_nested_data(), refer to function on line 133.
		$taxonomy_output          = ! empty( $_POST['taxonomy_output'] ) ? $this->bpfwe_sanitize_nested_data( wp_unslash( $_POST['taxonomy_output'] ), $taxonomy_sanitization_rules ) : [];  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$custom_field_output      = ! empty( $_POST['custom_field_output'] ) ? $this->bpfwe_sanitize_nested_data( wp_unslash( $_POST['custom_field_output'] ), $text_sanitization_rules ) : [];  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$custom_field_like_output = ! empty( $_POST['custom_field_like_output'] ) ? $this->bpfwe_sanitize_nested_data( wp_unslash( $_POST['custom_field_like_output'] ), $text_sanitization_rules ) : [];  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$numeric_output           = ! empty( $_POST['numeric_output'] ) ? $this->bpfwe_sanitize_nested_data( wp_unslash( $_POST['numeric_output'] ), $taxonomy_sanitization_rules ) : [];  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$group_logic       = ! empty( $_POST['group_logic'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['group_logic'] ) ) ) : '';
		$meta_key          = ! empty( $_POST['order_by_meta'] ) ? sanitize_key( wp_unslash( $_POST['order_by_meta'] ) ) : '';
		$order             = ! empty( $_POST['order'] ) && in_array( strtoupper( wp_unslash( $_POST['order'] ) ), [ 'DESC', 'ASC' ], true ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['order'] ) ) ) : 'ASC';
		$order_by          = ! empty( $_POST['order_by'] ) ? sanitize_key( wp_unslash( $_POST['order_by'] ) ) : 'date';
		$search_terms      = ! empty( $_POST['search_query'] ) ? sanitize_text_field( wp_unslash( $_POST['search_query'] ) ) : '';
		$dynamic_filtering = ! empty( $_POST['dynamic_filtering'] ) ? filter_var( wp_unslash( $_POST['dynamic_filtering'] ), FILTER_VALIDATE_BOOLEAN ) : false;
		$post_type         = ! empty( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : 'any';
		$paged             = ! empty( $_POST['paged'] ) ? max( 1, absint( wp_unslash( $_POST['paged'] ) ) ) : 1;

		$is_empty = true;

		set_query_var( 'paged', $paged );
		set_query_var( 'page', $paged );
		set_query_var( 'page_num', $paged );

		$args = apply_filters(
			'bpfwe_ajax_query_args',
			array(
				'order'     => $order,
				'orderby'   => $order_by,
				'post_type' => $post_type,
				'paged'     => $paged,
			)
		);

		if ( ! empty( $search_terms ) ) {
			$args['s'] = $search_terms;
		}

		if ( ! empty( $meta_key ) ) {
			$args['meta_key'] = $meta_key;
		}

		if ( $taxonomy_output ) {
			$query_and = [];
			$query_or  = [];

			foreach ( $taxonomy_output as $key => $value ) {
				// Check if terms is an array or not.
				$terms             = is_array( $value['terms'] ) ? array_map( 'absint', $value['terms'] ) : [ absint( $value['terms'] ) ];
				$grouped_terms_and = [];
				$grouped_terms_or  = [];

				foreach ( $terms as $term ) {
					$query = [
						'taxonomy'         => sanitize_key( $value['taxonomy'] ),
						'field'            => 'id',
						'terms'            => $term,
						'include_children' => true,
					];

					$row_logic = in_array( strtoupper( $value['logic'] ?? '' ), [ 'AND', 'OR' ], true ) ? strtoupper( $value['logic'] ) : '';

					// If the logic is 'AND', group the terms together.
					if ( 'AND' === $row_logic ) {
						$grouped_terms_and[] = $query;
					}
				}

				// Handle the 'OR' logic by combining terms using 'IN'.
				if ( 'OR' === $row_logic ) {
					$grouped_terms_or[] = [
						'taxonomy'         => sanitize_key( $value['taxonomy'] ),
						'field'            => 'id',
						'terms'            => $terms, // Combine all terms for IN comparison.
						'include_children' => true,
					];
				}

				// Ensure that each group of terms with 'AND' logic is a separate array.
				if ( ! empty( $grouped_terms_and ) ) {
					$query_and[] = $grouped_terms_and;
				}

				if ( ! empty( $grouped_terms_or ) ) {
					$query_or = array_merge( $query_or, $grouped_terms_or );
				}
			}

			// Set tax_query in $args, ensuring separate AND groups.
			if ( ! empty( $query_and ) || ! empty( $query_or ) ) {
				$args['tax_query'] = [];

				// If there's more than one group, set the parent relation.
				if ( ( count( $query_and ) + count( $query_or ) ) > 1 || $dynamic_filtering ) {
					$args['tax_query']['relation'] = $group_logic;
				}

				// Add the AND groups as separate subqueries.
				foreach ( $query_and as $group_and ) {
					if ( count( $group_and ) > 1 ) {
						$args['tax_query'][] = array_merge( [ 'relation' => 'AND' ], $group_and );
					} else {
						$args['tax_query'][] = $group_and[0];
					}
				}

				// Add the OR group using combined terms with IN comparison.
				if ( ! empty( $query_or ) && count( $query_or ) > 1 ) {
					$args['tax_query'][] = array_merge( [ 'relation' => 'OR' ], $query_or );
				} elseif ( ! empty( $query_or ) ) {
					$args['tax_query'][] = $query_or[0];
				}
			}

			$is_empty = false;
		}

		if ( $custom_field_output || $custom_field_like_output || $numeric_output ) {
			$meta_query_and   = [];
			$meta_query_or    = [];
			$meta_like_and    = [];
			$meta_like_or     = [];
			$meta_numeric_and = [];
			$meta_numeric_or  = [];

			// Add CUSTOM FIELD/ACF to query.
			if ( ! empty( $custom_field_output ) && is_array( $custom_field_output ) ) {
				foreach ( $custom_field_output as $value ) {
					// Ensure terms is an array, and sanitize its values.
					$terms             = ! empty( $value['terms'] ) && is_array( $value['terms'] ) ? array_map( 'sanitize_text_field', $value['terms'] ) : [ sanitize_text_field( $value['terms'] ) ];
					$grouped_terms_and = [];

					foreach ( $terms as $term ) {
						$query = [
							'key'     => sanitize_key( $value['taxonomy'] ),
							'value'   => $term,
							'compare' => '=',
						];

						$row_logic = in_array( strtoupper( $value['logic'] ?? '' ), [ 'AND', 'OR' ], true ) ? strtoupper( $value['logic'] ) : '';

						// If the logic is 'AND', group the terms together.
						if ( 'AND' === $row_logic ) {
							$grouped_terms_and[] = $query;
						}
					}

					// Handle 'OR' logic using 'IN'.
					if ( 'OR' === $row_logic ) {
						$meta_query_or[] = [
							'key'     => sanitize_key( $value['taxonomy'] ),
							'value'   => $terms,
							'compare' => 'IN',
						];
					}

					if ( ! empty( $grouped_terms_and ) ) {
						$meta_query_and[] = $grouped_terms_and;
					}
				}
			}

			// Add INPUT field to query.
			if ( ! empty( $custom_field_like_output ) && is_array( $custom_field_like_output ) ) {
				foreach ( $custom_field_like_output as $key => $value ) {
					$query = [
						'key'     => sanitize_key( $value['taxonomy'] ),
						'value'   => implode( ' ', array_map( 'sanitize_text_field', (array) $value['terms'] ) ),
						'compare' => 'LIKE',
					];

					$row_logic = in_array( strtoupper( $value['logic'] ?? '' ), [ 'AND', 'OR' ], true ) ? strtoupper( $value['logic'] ) : '';

					if ( 'AND' === $row_logic ) {
						$meta_like_and[] = $query;
					}

					if ( 'OR' === $row_logic ) {
						$meta_like_or[] = $query;
					}
				}
			}

			// Add NUMERIC value field to query.
			if ( ! empty( $numeric_output ) && is_array( $numeric_output ) ) {
				foreach ( $numeric_output as $key => $value ) {
					$query = [
						'key'     => sanitize_key( $value['taxonomy'] ),
						'value'   => ! empty( $value['terms'] ) && is_array( $value['terms'] ) ? array_map( 'sanitize_text_field', $value['terms'] ) : sanitize_text_field( $value['terms'] ),
						'type'    => 'numeric',
						'compare' => 'BETWEEN',
					];

					$row_logic = in_array( strtoupper( $value['logic'] ?? '' ), [ 'AND', 'OR' ], true ) ? strtoupper( $value['logic'] ) : '';

					if ( 'AND' === $row_logic ) {
						$meta_numeric_and[] = $query;
					}

					if ( 'OR' === $row_logic ) {
						$meta_numeric_or[] = $query;
					}
				}
			}

			// Initialize meta_query if there are any AND/OR groups or LIKE conditions.
			if ( ! empty( $meta_query_and ) || ! empty( $meta_query_or ) || ! empty( $meta_like_and ) || ! empty( $meta_like_or ) || ! empty( $meta_numeric_and ) || ! empty( $meta_numeric_or ) ) {
				$args['meta_query'] = [];

				if ( ( count( $meta_query_and ) + count( $meta_query_or ) + count( $meta_like_and ) + count( $meta_like_or ) + count( $meta_numeric_and ) + count( $meta_numeric_or ) ) > 1 || $dynamic_filtering ) {
					$args['meta_query']['relation'] = $group_logic;
				}

				foreach ( $meta_query_and as $group_and ) {
					$args['meta_query'][] = array_merge( [ 'relation' => 'AND' ], $group_and );
				}

				foreach ( $meta_query_or as $group_or ) {
					$args['meta_query'][] = $group_or;
				}

				if ( ! empty( $meta_like_and ) ) {
					$args['meta_query'][] = array_merge( [ 'relation' => 'AND' ], $meta_like_and );
				}

				if ( ! empty( $meta_like_or ) ) {
					$args['meta_query'][] = array_merge( [ 'relation' => 'OR' ], $meta_like_or );
				}

				if ( ! empty( $meta_numeric_and ) && count( $meta_numeric_and ) > 1 ) {
					$args['meta_query'][] = array_merge( [ 'relation' => 'AND' ], $meta_numeric_and );
				} elseif ( ! empty( $meta_numeric_and ) ) {
					$args['meta_query'][] = $meta_numeric_and[0];
				}

				if ( ! empty( $meta_numeric_or ) && count( $meta_numeric_or ) > 1 ) {
					$args['meta_query'][] = array_merge( [ 'relation' => 'OR' ], $meta_numeric_or );
				} elseif ( ! empty( $meta_numeric_or ) ) {
					$args['meta_query'][] = $meta_numeric_or[0];
				}
			}

			$is_empty = false;
		}

		if ( $dynamic_filtering ) {
			$archive_type     = isset( $_POST['archive_type'] ) ? sanitize_text_field( wp_unslash( $_POST['archive_type'] ) ) : '';
			$archive_taxonomy = isset( $_POST['archive_taxonomy'] ) ? sanitize_text_field( wp_unslash( $_POST['archive_taxonomy'] ) ) : '';
			$archive_id       = isset( $_POST['archive_id'] ) ? absint( wp_unslash( $_POST['archive_id'] ) ) : 0;

			// Add conditions based on the archive type.
			switch ( $archive_type ) {
				case 'author':
					$args['author__in'] = array( $archive_id );
					break;
				case 'date':
					break;
				case 'category':
				case 'taxonomy':
					$args['tax_query'][] = array(
						'taxonomy'         => $archive_taxonomy,
						'field'            => 'id',
						'terms'            => $archive_id,
						'include_children' => true,
					);
					break;
				case 'tag':
					$args['tag__in'] = array( $archive_id );
					break;
				case 'post_type':
					$args['post_type'] = isset( $_POST['archive_post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['archive_post_type'] ) ) : 'any';
					break;
				case 'search':
					$args['s'] = get_search_query();
					break;
			}
		}

		if ( ! empty( $order_by ) || ! empty( $search_terms ) ) {
			$is_empty = false;
		}

		if ( false === $is_empty ) {
			$widget_data['settings']['args'] = $args;
		}

		if ( true === $is_empty ) {
			delete_transient( 'bpfwe_filter_query' );
			return;
		}

		set_transient( 'bpfwe_filter_query', $args, 60 * 60 * 24 );
		// error_log( 'Debugging $args: ' . print_r( $args, true ) ); -- Enable for debugging.
		echo wp_json_encode(
			array(
				'html' => $document->render_element( $widget_data ),
			)
		);

		wp_die();
	}

	/**
	 * Handles AJAX requests to load page content and extract specified div elements.
	 *
	 * This function verifies the request, fetches the specified page content,
	 * extracts the required div elements based on the provided selectors,
	 * and returns the content as a JSON response.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_page_callback() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		// Verify the nonce.
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( [ 'message' => 'Access Denied' ], 403 );
		}

		// Fetch and sanitize the URL.
		$page_url = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';

		// Ensure the URL is complete and belongs to the same domain.
		if ( ! empty( $page_url ) && strpos( $page_url, 'http' ) !== 0 ) {
			$page_url = home_url( ltrim( $page_url, '/' ) );
		}

		// Validate the domain to prevent external requests.
		$parsed_url      = wp_parse_url( $page_url );
		$parsed_home_url = wp_parse_url( home_url() );
		if ( empty( $parsed_url['host'] ) || $parsed_url['host'] !== $parsed_home_url['host'] ) {
			wp_send_json_error( [ 'message' => 'Invalid URL' ], 403 );
		}

		// Filter cookies to only include authentication-related ones.
		$allowed_cookies = [ 'wordpress_logged_in_', 'wp-settings-', 'wp-settings-time-' ];
		$cookies         = [];

		foreach ( $_COOKIE as $name => $value ) {
			foreach ( $allowed_cookies as $allowed_cookie ) {
				if ( strpos( $name, $allowed_cookie ) === 0 ) {
					$cookies[] = new WP_Http_Cookie(
						[
							'name'  => sanitize_key( wp_unslash( $name ) ),
							'value' => sanitize_text_field( wp_unslash( $value ) ),
						]
					);
				}
			}
		}

		// Fetch page content securely.
		$response = wp_safe_remote_get(
			$page_url,
			[
				'cookies' => $cookies,
				'timeout' => 15,
			]
		);

		// Handle errors.
		if ( is_wp_error( $response ) ) {
			wp_send_json_error( [ 'message' => 'Failed to fetch content' ] );
		}

		// Retrieve the page content.
		$body = wp_remote_retrieve_body( $response );

		wp_send_json_success( [ 'html' => $body ] );
	}

	/**
	 * Modifies the query to filter posts based on custom parameters.
	 *
	 * Hooked to `pre_get_posts` for advanced query customization.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 *
	 * @return void
	 */
	public function pre_get_posts_filter( $query ) {
		$filter_data = get_transient( 'bpfwe_filter_query' );

		if ( $filter_data && ! $query->is_main_query() ) {
			foreach ( $filter_data as $key => $value ) {
				$query->set( $key, $value );
			}
		}
	}

	/**
	 * Constructor for the BPFWE_Ajax class.
	 *
	 * Initializes AJAX hooks and sets up the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'delete_filter_transient' ] );
		add_action( 'admin_init', [ $this, 'delete_filter_transient' ] );

		add_action( 'pre_get_posts', [ $this, 'pre_get_posts_filter' ] );

		add_action( 'wp_ajax_change_post_status', [ $this, 'change_post_status' ] );
		add_action( 'wp_ajax_nopriv_change_post_status', [ $this, 'change_post_status' ] );

		add_action( 'wp_ajax_pin_post', [ $this, 'pin_post' ] );
		add_action( 'wp_ajax_nopriv_pin_post', [ $this, 'pin_post' ] );

		add_action( 'wp_ajax_post_filter_results', [ $this, 'post_filter_results' ] );
		add_action( 'wp_ajax_nopriv_post_filter_results', [ $this, 'post_filter_results' ] );

		add_action( 'wp_ajax_load_page', [ $this, 'load_page_callback' ] );
		add_action( 'wp_ajax_nopriv_load_page', [ $this, 'load_page_callback' ] );
	}
}
new BPFWE_Ajax();

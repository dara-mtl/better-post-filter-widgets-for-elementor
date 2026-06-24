<?php
/**
 * Handles the AJAX Functions and REST API endpoints.
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
 * Manages AJAX-related functionalities and REST API endpoints for the plugin.
 * Includes actions such as changing post status, pinning posts, and optimizing filters.
 */
class BPFWE_Ajax {
	/**
	 * Holds the singleton instance.
	 *
	 * @var BPFWE_Ajax|null
	 */
	private static $instance = null;
	/**
	 * Holds the current filter query args.
	 *
	 * @var array|null
	 */
	private $filter_query;

	/**
	 * Holds the current filter post IDs for faceting.
	 *
	 * @var array|null
	 */
	private $filter_post_ids;

	/**
	 * Changes the status of a post via REST API.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function change_post_status_rest( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error(
				'rest_forbidden',
				'Invalid nonce',
				[ 'status' => 403 ]
			);
		}

		$post_id = absint( $request->get_param( 'post_id' ) );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new WP_Error(
				'forbidden',
				'You are not allowed to edit this post',
				[ 'status' => 403 ]
			);
		}

		// Get the current date and time.
		$current_date = current_time( 'mysql' );
		$post_status  = get_post_status( $post_id );
		$new_status   = ( 'publish' === $post_status ) ? 'draft' : 'publish';

		// Update post status and publication date.
		$result = wp_update_post(
			[
				'ID'            => $post_id,
				'post_status'   => $new_status,
				'post_date'     => $current_date,
				'post_date_gmt' => get_gmt_from_date( $current_date ),
			]
		);

		if ( is_wp_error( $result ) ) {
			return new WP_Error( 'update_failed', 'Failed to update post status', [ 'status' => 500 ] );
		}

		return new WP_REST_Response(
			[
				'success'    => true,
				'new_status' => $new_status,
			],
			200
		);
	}

	/**
	 * Bookmark/pin posts via REST API.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function pin_post_rest( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error(
				'rest_forbidden',
				'Invalid nonce',
				[ 'status' => 403 ]
			);
		}

		$post_id = absint( $request->get_param( 'post_id' ) );

		if ( empty( $post_id ) || ! get_post( $post_id ) ) {
			return new WP_Error(
				'invalid_post',
				'Invalid post ID.',
				[ 'status' => 400 ]
			);
		}

		$user_id   = get_current_user_id();
		$post_list = [];

		if ( ! empty( $user_id ) ) {
			$post_list = get_user_meta( $user_id, 'post_id_list', true );
			if ( ! is_array( $post_list ) ) {
				$post_list = [];
			}
		} elseif ( isset( $_COOKIE['post_id_list'] ) ) {
			$raw_cookie_data = sanitize_text_field( wp_unslash( $_COOKIE['post_id_list'] ) );
			$post_list       = json_decode( $raw_cookie_data, true );
			if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $post_list ) ) {
				$post_list = [];
			}
		}

		$post_list = array_map( 'absint', $post_list );
		$key       = array_search( $post_id, $post_list, true );

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
			$is_secure = is_ssl() || ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] );
			setcookie(
				'post_id_list',
				wp_json_encode( $post_list ),
				[
					'expires'  => time() + ( 86400 * 30 ),
					'path'     => '/',
					'secure'   => $is_secure,
					'httponly' => true,
					'samesite' => 'Lax',
				]
			);
		}

		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	/**
	 * Deletes cached filter results stored as transients.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function reset_filter_state() {
		$this->filter_query    = null;
		$this->filter_post_ids = null;
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
	 * Allowed REST API parameter keys for the filter endpoint.
	 *
	 * @var array
	 */
	private $allowed_keys = [
		'widget_id',
		'filter_widget',
		'template_id',
		'current_url',
		'page_id',
		'group_logic',
		'search_query',
		'date_query',
		'taxonomy_output',
		'dynamic_filtering',
		'custom_field_output',
		'custom_field_relational_output',
		'custom_field_like_output',
		'numeric_output',
		'post_type',
		'posts_per_page',
		'order',
		'order_by',
		'order_by_meta',
		'paged',
		'archive_type',
		'archive_post_type',
		'archive_taxonomy',
		'archive_id',
		'archive_search_query',
		'performance_settings',
		'enable_query_debug',
		'inject_id',
		'query_id',
	];

	/**
	 * Retrieves filtered post results based on the specified criteria via REST API.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function post_filter_results_rest( $request ) {
		$params = array_intersect_key(
			$request->get_params(),
			array_flip( $this->allowed_keys )
		);

		$template_id      = ! empty( $params['template_id'] ) ? absint( $params['template_id'] ) : '';
		$page_id          = ! empty( $params['page_id'] ) ? absint( $params['page_id'] ) : '';
		$current_url      = ! empty( $params['current_url'] ) ? sanitize_text_field( wp_unslash( $params['current_url'] ) ) : home_url( '/' );
		$widget_id        = ! empty( $params['widget_id'] ) ? sanitize_key( $params['widget_id'] ) : '';
		$filter_widget_id = ! empty( $params['filter_widget'] ) ? sanitize_text_field( $params['filter_widget'] ) : '';
		$inject_id        = ! empty( $params['inject_id'] ) ? sanitize_text_field( $params['inject_id'] ) : '';

		if ( empty( $template_id ) || empty( $widget_id ) ) {
			return new WP_Error(
				'missing_parameters',
				sprintf(
					'A valid template ID and widget ID are required. Provided template: %s, widget: %s',
					$template_id ? $template_id : '(empty)',
					$widget_id ? $widget_id : '(empty)'
				),
				[ 'status' => 400 ]
			);
		}

		$document = \Elementor\Plugin::$instance->documents->get( $template_id );
		if ( ! $document && ! empty( $page_id ) ) {
			$document = \Elementor\Plugin::$instance->documents->get( $page_id );
		}
		if ( ! $document ) {
			return new WP_Error(
				'invalid_template',
				sprintf(
					'
					The template ID %s does not exist.',
					$template_id
				),
				[ 'status' => 400 ]
			);
		}

		$post_status = get_post_status( $template_id );

		if ( 'publish' !== $post_status && ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				'You do not have permission to view this layout template.',
				[ 'status' => 403 ]
			);
		}

		$element_data = $document->get_elements_data();
		$widget_data  = \Elementor\Utils::find_element_recursive( $element_data, $widget_id );

		// If widget is not found in template_id, fallback to page_id document.
		if ( ! $widget_data && ! empty( $page_id ) ) {
			$document     = \Elementor\Plugin::$instance->documents->get( $page_id );
			$element_data = $document->get_elements_data();
			$widget_data  = \Elementor\Utils::find_element_recursive( $element_data, $widget_id );
		}

		if ( ! $widget_data ) {
			return new WP_Error(
				'widget_not_found',
				sprintf(
					'Widget ID "%s" not found in template ID %s or page ID %s.',
					$widget_id,
					$template_id,
					$page_id ? $page_id : '(none)'
				),
				[ 'status' => 400 ]
			);
		}

		$ele_widget_query_id   = '';
		$bpfwe_widget_query_id = '';

		if ( $inject_id ) {
			if ( ! empty( $widget_data['settings']['post_query_query_id'] ) ) {
				$ele_widget_query_id = $widget_data['settings']['post_query_query_id'];
			} elseif ( ! empty( $widget_data['settings']['posts_query_id'] ) ) {
				$ele_widget_query_id = $widget_data['settings']['posts_query_id'];
			}

			if ( ! empty( $widget_data['settings']['query_id'] ) ) {
				$bpfwe_widget_query_id = $widget_data['settings']['query_id'];
			}
		}

		$this->register_pre_get_posts_filter();

		// Multidimensional array sanitization and validation.
		$taxonomy_sanitization_rules = [
			'taxonomy' => 'sanitize_text_field',
			'terms'    => function ( $terms ) {
				return array_map( 'absint', (array) $terms );
			},
			'logic'    => 'sanitize_text_field',
		];

		$text_sanitization_rules = [
			'taxonomy' => 'sanitize_text_field',
			'terms'    => function ( $terms ) {
				return array_map( 'sanitize_text_field', (array) $terms );
			},
			'logic'    => 'sanitize_text_field',
		];

		$performance_sanitization_rules = [
			'optimize_query'   => 'sanitize_text_field',
			'no_found_rows'    => 'sanitize_text_field',
			'suppress_filters' => 'sanitize_text_field',
			'posts_per_page'   => 'intval',
		];

		// Get and sanitize all parameters from the REST request.
		$days_array                     = ! empty( $params['date_query'] ) ? array_map( 'trim', explode( ',', sanitize_text_field( $params['date_query'] ) ) ) : [];
		$taxonomy_output                = ! empty( $params['taxonomy_output'] ) ? $this->bpfwe_sanitize_nested_data( $params['taxonomy_output'], $taxonomy_sanitization_rules ) : [];
		$custom_field_output            = ! empty( $params['custom_field_output'] ) ? $this->bpfwe_sanitize_nested_data( $params['custom_field_output'], $text_sanitization_rules ) : [];
		$custom_field_relational_output = ! empty( $params['custom_field_relational_output'] ) ? $this->bpfwe_sanitize_nested_data( $params['custom_field_relational_output'], $text_sanitization_rules ) : [];
		$custom_field_like_output       = ! empty( $params['custom_field_like_output'] ) ? $this->bpfwe_sanitize_nested_data( $params['custom_field_like_output'], $text_sanitization_rules ) : [];
		$numeric_output                 = ! empty( $params['numeric_output'] ) ? $this->bpfwe_sanitize_nested_data( $params['numeric_output'], $taxonomy_sanitization_rules ) : [];
		$performance_settings_raw       = ! empty( $params['performance_settings'] ) ? $params['performance_settings'] : [];

		if ( is_string( $performance_settings_raw ) ) {
			$performance_settings_raw = json_decode( $performance_settings_raw, true );
		}

		$performance_settings = $this->bpfwe_sanitize_nested_data( (array) $performance_settings_raw, $performance_sanitization_rules );
		$group_logic          = ! empty( $params['group_logic'] ) ? strtoupper( sanitize_text_field( $params['group_logic'] ) ) : '';
		$meta_key             = ! empty( $params['order_by_meta'] ) ? sanitize_key( $params['order_by_meta'] ) : '';
		$order                = ! empty( $params['order'] ) && in_array( strtoupper( $params['order'] ), [ 'DESC', 'ASC' ], true ) ? strtoupper( sanitize_text_field( $params['order'] ) ) : '';
		$order_by             = ! empty( $params['order_by'] ) ? sanitize_text_field( $params['order_by'] ) : '';
		$search_terms         = ! empty( $params['search_query'] ) ? sanitize_text_field( $params['search_query'] ) : '';
		$archive_search_terms = ! empty( $params['archive_search_query'] ) ? sanitize_text_field( $params['archive_search_query'] ) : '';
		$dynamic_filtering    = ! empty( $params['dynamic_filtering'] ) ? filter_var( $params['dynamic_filtering'], FILTER_VALIDATE_BOOLEAN ) : false;
		$post_type            = ! empty( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : 'any';
		$posts_per_page       = ! empty( $params['posts_per_page'] ) ? max( 1, absint( $params['posts_per_page'] ) ) : 50;
		$paged                = ! empty( $params['paged'] ) ? max( 1, absint( $params['paged'] ) ) : 1;
		$enable_query_debug   = ! empty( $params['enable_query_debug'] ) ? sanitize_text_field( $params['enable_query_debug'] ) : '';
		$query_id             = ! empty( $params['query_id'] ) ? sanitize_key( $params['query_id'] ) : 'default';

		$performance_settings = [
			'optimize_query'   => isset( $performance_settings['optimize_query'] ) ? filter_var( $performance_settings['optimize_query'], FILTER_VALIDATE_BOOLEAN ) : null,
			'no_found_rows'    => isset( $performance_settings['no_found_rows'] ) ? filter_var( $performance_settings['no_found_rows'], FILTER_VALIDATE_BOOLEAN ) : null,
			'suppress_filters' => isset( $performance_settings['suppress_filters'] ) ? filter_var( $performance_settings['suppress_filters'], FILTER_VALIDATE_BOOLEAN ) : null,
			'posts_per_page'   => isset( $performance_settings['posts_per_page'] ) ? (int) $performance_settings['posts_per_page'] : null,
		];

		$final_posts_per_page = null !== $performance_settings['posts_per_page'] ? $performance_settings['posts_per_page'] : $posts_per_page;

		$is_empty = true;

		set_query_var( 'paged', $paged );
		set_query_var( 'page', $paged );
		set_query_var( 'page_num', $paged );

		if ( 'targeted_widget' === $post_type ) {
			$post_type = 'any';
		}

		$args = [
			'post_type' => $post_type,
			'paged'     => $paged,
		];

		if ( ! empty( $order ) ) {
			$args['order'] = $order;
		}

		if ( ! empty( $order_by ) ) {
			$args['orderby'] = $order_by;
		}

		if ( true === $performance_settings['optimize_query'] ) {
			$args['fields'] = 'ids';
		}

		if ( true === $performance_settings['no_found_rows'] ) {
			$args['no_found_rows'] = true;
		}

		if ( true === $performance_settings['suppress_filters'] ) {
			$args['suppress_filters'] = true;
		}

		if ( -1 !== $final_posts_per_page ) {
			$args['posts_per_page'] = $final_posts_per_page;
		}

		// Resolve final search term (priority: widget > archive).
		$final_search = '';

		if ( ! empty( $search_terms ) ) {
			$final_search = $search_terms;
		} elseif ( ! empty( $archive_search_terms ) && $dynamic_filtering ) {
			$final_search = $archive_search_terms;
		}

		if ( ! empty( $final_search ) ) {
			$args['s'] = $final_search;
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
				$row_logic         = in_array( strtoupper( $value['logic'] ?? '' ), [ 'AND', 'OR' ], true ) ? strtoupper( $value['logic'] ) : '';

				foreach ( $terms as $term ) {
					$query = [
						'taxonomy'         => sanitize_key( $value['taxonomy'] ),
						'field'            => 'id',
						'terms'            => $term,
						'include_children' => true,
					];

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
				if ( ! empty( $query_or ) ) {
					foreach ( $query_or as $or_filter ) {
						$args['tax_query'][] = $or_filter;
					}
				}
			}

			$is_empty = false;
		}

		if ( $custom_field_output || $custom_field_like_output || $numeric_output || $custom_field_relational_output ) {
			$meta_query_or   = [];
			$meta_like_or    = [];
			$meta_numeric_or = [];

			// Add CUSTOM FIELD/ACF to query.
			if ( ! empty( $custom_field_output ) && is_array( $custom_field_output ) ) {
				foreach ( $custom_field_output as $value ) {
					$terms = ! empty( $value['terms'] ) && is_array( $value['terms'] ) ? array_map( 'sanitize_text_field', $value['terms'] ) : [ sanitize_text_field( $value['terms'] ) ];
					$key   = sanitize_text_field( $value['taxonomy'] );

					if ( \BPFWE\Inc\Classes\BPFWE_Helper::acf_field_uses_serialized_storage( $key ) ) {
						if ( count( $terms ) > 1 ) {
							$serialized_group = [ 'relation' => 'OR' ];
							foreach ( $terms as $term ) {
								$serialized_group[] = [
									'key'     => $key,
									'value'   => '"' . $term . '"',
									'compare' => 'LIKE',
								];
							}
							$meta_query_or[] = $serialized_group;
						} else {
							$meta_query_or[] = [
								'key'     => $key,
								'value'   => '"' . $terms[0] . '"',
								'compare' => 'LIKE',
							];
						}
					} else {
						$meta_query_or[] = [
							'key'     => $key,
							'value'   => count( $terms ) > 1 ? $terms : $terms[0],
							'compare' => count( $terms ) > 1 ? 'IN' : '=',
						];
					}
				}
			}

			// Add INPUT field to query.
			if ( ! empty( $custom_field_like_output ) && is_array( $custom_field_like_output ) ) {
				foreach ( $custom_field_like_output as $value ) {
					$meta_like_or[] = [
						'key'     => sanitize_key( $value['taxonomy'] ),
						'value'   => implode( ' ', array_map( 'sanitize_text_field', (array) $value['terms'] ) ),
						'compare' => 'LIKE',
					];
				}
			}

			// Add RELATIONAL FIELD to query.
			if ( ! empty( $custom_field_relational_output ) && is_array( $custom_field_relational_output ) ) {
				foreach ( $custom_field_relational_output as $value ) {
					$taxonomy_key = sanitize_key( $value['taxonomy'] );
					$term_ids     = array_map( 'absint', (array) $value['terms'] );
					$row_logic    = in_array( strtoupper( $value['logic'] ?? '' ), [ 'AND', 'OR' ], true ) ? strtoupper( $value['logic'] ) : '';
					if ( empty( $term_ids ) ) {
						continue;
					}
					if ( count( $term_ids ) > 1 ) {
						$or_group = [ 'relation' => $row_logic ];
						foreach ( $term_ids as $term_id ) {
							$or_group[] = [
								'key'     => $taxonomy_key,
								'value'   => $term_id,
								'compare' => 'LIKE',
							];
						}
						$meta_like_or[] = $or_group;
					} else {
						$meta_like_or[] = [
							'key'     => $taxonomy_key,
							'value'   => $term_ids[0],
							'compare' => 'LIKE',
						];
					}
				}
			}

			// Add NUMERIC value field to query.
			if ( ! empty( $numeric_output ) && is_array( $numeric_output ) ) {
				foreach ( $numeric_output as $value ) {
					$terms             = ! empty( $value['terms'] ) && is_array( $value['terms'] ) ? array_slice( array_map( 'sanitize_text_field', $value['terms'] ), 0, 2 ) : [ sanitize_text_field( $value['terms'] ) ];
					$meta_numeric_or[] = [
						'key'     => sanitize_key( $value['taxonomy'] ),
						'value'   => count( $terms ) > 1 ? $terms : $terms[0],
						'type'    => 'NUMERIC',
						'compare' => count( $terms ) > 1 ? 'BETWEEN' : '>=',
					];
				}
			}

			// Initialize meta_query if there are any AND/OR groups or LIKE conditions.
			if ( ! empty( $meta_query_or ) || ! empty( $meta_like_or ) || ! empty( $meta_numeric_or ) ) {
				$args['meta_query'] = [];
				if ( ( count( $meta_query_or ) + count( $meta_like_or ) + count( $meta_numeric_or ) ) > 1 || $dynamic_filtering ) {
					$args['meta_query']['relation'] = $group_logic;
				}
				foreach ( $meta_query_or as $group_or ) {
					$args['meta_query'][] = $group_or;
				}
				foreach ( $meta_like_or as $like_query ) {
					$args['meta_query'][] = $like_query;
				}
				foreach ( $meta_numeric_or as $numeric_query ) {
					$args['meta_query'][] = $numeric_query;
				}
			}
			$is_empty = false;
		}

		// Add date_query.
		if ( ! empty( $days_array ) ) {
			$min_days = min( array_map( 'intval', $days_array ) );
			if ( $min_days > 0 ) {
				$args['date_query'] = [
					[
						'after'     => gmdate( 'Y-m-d', strtotime( "-{$min_days} days" ) ),
						'inclusive' => true,
					],
				];
			}
		}

		if ( $dynamic_filtering ) {
			$archive_type     = ! empty( $params['archive_type'] ) ? sanitize_text_field( $params['archive_type'] ) : '';
			$archive_taxonomy = ! empty( $params['archive_taxonomy'] ) ? sanitize_text_field( $params['archive_taxonomy'] ) : '';
			$archive_id       = ! empty( $params['archive_id'] ) ? absint( $params['archive_id'] ) : 0;

			// Add conditions based on the archive type.
			switch ( $archive_type ) {
				case 'author':
					$args['author__in'] = [ $archive_id ];
					break;
				case 'category':
				case 'taxonomy':
					$args['tax_query'][] = [
						'taxonomy'         => $archive_taxonomy,
						'field'            => 'id',
						'terms'            => $archive_id,
						'include_children' => true,
					];
					break;
				case 'tag':
					$args['tag__in'] = [ $archive_id ];
					break;
				case 'post_type':
					$args['post_type'] = ! empty( $params['archive_post_type'] ) ? sanitize_text_field( $params['archive_post_type'] ) : 'any';
					break;
				case 'search':
					$args['s'] = get_search_query();
					break;
			}
		}

		if ( ! empty( $order_by ) || ! empty( $search_terms ) ) {
			$is_empty = false;
		}

		// Capture Elementor query ID.
		if ( ! empty( $ele_widget_query_id ) || ! empty( $bpfwe_widget_query_id ) ) {
			$simulated_query = new class() {
				/**
				 * Holds query variables to be merged into main args.
				 *
				 * @var array
				 */
				public $query_vars = [];

				/**
				 * Set a query variable.
				 *
				 * @param string $key   The query var key.
				 * @param mixed  $value The value to set.
				 */
				public function set( $key, $value ) {
					$this->query_vars[ $key ] = $value;
				}
			};

			if ( $ele_widget_query_id ) {
				do_action( "elementor/query/{$ele_widget_query_id}", $simulated_query );
			} else {
				do_action( "bpfwe/query/{$bpfwe_widget_query_id}", $simulated_query );
			}

			if ( ! empty( $simulated_query->query_vars ) ) {
				$args = array_merge( $args, $simulated_query->query_vars );
			}
		}

		// Run global filter (affects all filter queries).
		$args = apply_filters( 'bpfwe_ajax_query_args', $args, $this );

		// Run specific filter (affects only one query instance).
		if ( ! empty( $query_id ) ) {
			$args = apply_filters( "bpfwe/filter_query_args/{$query_id}", $args, $this );
		}

		if ( false === $is_empty ) {
			$widget_data['settings']['args'] = $args;
		}

		if ( true === $is_empty ) {
			$this->filter_query = null;
		} else {
			$this->filter_query = $args;
		}

		if ( false === $is_empty && $filter_widget_id ) {
			$facet_args                   = $args;
			$facet_args['posts_per_page'] = -1;
			$facet_args['fields']         = 'ids';
			$facet_args['no_found_rows']  = true;
			$facet_args['orderby']        = 'none';

			unset(
				$facet_args['paged'],
				$facet_args['page'],
				$facet_args['offset'],
				$facet_args['order'],
				$facet_args['meta_key']
			);

			$facet_query  = new WP_Query( $facet_args );
			$all_post_ids = $facet_query->posts;

			$this->filter_post_ids = $all_post_ids;
		} else {
			$this->filter_post_ids = null;
		}

		// error_log( 'Debugging $args: ' . print_r( $args, true ) ); -- Enable for debugging.

		$widget_html = $document->render_element( $widget_data );

		// Clean AJAX endpoints in pagination links.
		$ajax_endpoints = array(
			admin_url( 'admin-ajax.php' ),
			rest_url( 'bpfwe/v1/filter' ),
		);

		foreach ( $ajax_endpoints as $endpoint ) {
			$widget_html = preg_replace_callback(
				'#((?:href|data-next-page)=["\'])' . preg_quote( $endpoint, '#' ) . '#',
				static fn( $matches ) => $matches[1] . untrailingslashit( home_url() ),
				$widget_html
			);
		}

		// Base URL for rebuilt pagination links: current page URL, stripped of any
		// existing pagination query args.
		$base_url = remove_query_arg( array( 'page_num', 'paged', 'page' ), $current_url );

		// Rebuild data-next-page (always paged + 1), discarding Elementor's own URL entirely.
		$widget_html = preg_replace_callback(
			'#(data-next-page=["\'])(.*?)(["\'])#',
			static function ( $matches ) use ( $base_url, $paged ) {
				$new_url = esc_url( add_query_arg( 'page_num', $paged + 1, $base_url ) );
				return $matches[1] . $new_url . $matches[3];
			},
			$widget_html
		);

		// Rebuild <a> pagination hrefs based on their visible page number / role,
		// discarding Elementor's own URL entirely.
		$widget_html = preg_replace_callback(
			'#<a\b([^>]*\bclass=["\'][^"\']*\bpage-numbers\b[^"\']*["\'][^>]*)>(.*?)</a>#s',
			static function ( $matches ) use ( $base_url, $paged ) {
				$attrs   = $matches[1];
				$inner   = $matches[2];
				$is_prev = (bool) preg_match( '#\bprev\b#', $attrs );
				$is_next = (bool) preg_match( '#\bnext\b#', $attrs );

				if ( $is_prev ) {
					$target_page = max( 1, $paged - 1 );
				} elseif ( $is_next ) {
					$target_page = $paged + 1;
				} else {
					// Numbered link: extract the trailing number from the link content,
					// ignoring any "Page" screen-reader-only label text.
					if ( preg_match( '#(\d+)\s*$#', $inner, $num_match ) ) {
						$target_page = (int) $num_match[1];
					} else {
						$target_page = $paged;
					}
				}

				$new_url = esc_url( add_query_arg( 'page_num', $target_page, $base_url ) );

				// Replace the existing href attribute entirely, or add one if missing.
				if ( preg_match( '#href=["\'][^"\']*["\']#', $attrs ) ) {
					$new_attrs = preg_replace( '#href=["\'][^"\']*["\']#', 'href="' . $new_url . '"', $attrs );
				} else {
					$new_attrs = $attrs . ' href="' . $new_url . '"';
				}

				return '<a' . $new_attrs . '>' . $inner . '</a>';
			},
			$widget_html
		);

		$response = [
			'html' => $widget_html,
		];

		if ( $filter_widget_id ) {
			$filter_data = \Elementor\Utils::find_element_recursive( $element_data, $filter_widget_id );
			if ( $filter_data ) {
				$response['filters'] = [
					$filter_widget_id => $document->render_element( $filter_data ),
				];
			}
		}

		if ( 'yes' === $enable_query_debug ) {
			$response['query'] = wp_json_encode( $args, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		}

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Handles frontend AJAX requests for loading posts.
	 *
	 * Verifies the AJAX action and nonce for security.
	 *
	 * @since 1.6.0
	 */
	public function bpfwe_handle_frontend_ajax() {
		if ( empty( $_POST['action'] ) || 'load_posts_ajax' !== $_POST['action'] ) {
			return;
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		// Verify the nonce.
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			status_header( 403 );
			wp_die( 'Invalid or missing nonce', 'Forbidden', [ 'response' => 403 ] );
		}
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
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		$filter_data = $this->filter_query;

		if ( ! $filter_data ) {
			return;
		}

		if ( empty( $query->query ) ) {
			return;
		}

		$post_type = (array) $query->get( 'post_type' );

		$exclude_post_types = [
			'attachment',
			'revision',
			'nav_menu_item',
			'custom_css',
			'customize_changeset',
			'oembed_cache',
			'user_request',
			'wp_block',
			'wp_template',
			'wp_template_part',
			'wp_navigation',
			'acf-field-group',
			'acf-field',
			'elementor_library',
			'elementor_font',
			'shop_order',
			'shop_coupon',
			'shop_order_refund',
			'et_pb_layout',
		];

		// If any post type in the query is in the exclusion list, bail.
		if ( array_intersect( $post_type, $exclude_post_types ) ) {
			return;
		}

		foreach ( $filter_data as $key => $value ) {
			$query->set( $key, $value );
		}
	}

	/**
	 * Handles search requests for related items via REST API (used in Elementor filter widget repeater).
	 *
	 * This function verifies the nonce, then searches both posts and users
	 * matching the provided query term. Results are returned as a combined JSON
	 * response compatible with Select2 for use within Elementor repeater fields.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function bpfwe_search_related_items_rest( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error(
				'rest_forbidden',
				'Invalid nonce',
				[ 'status' => 403 ]
			);
		}

		$search    = sanitize_text_field( $request->get_param( 'q' ) );
		$page      = $request->get_param( 'page' ) ? absint( $request->get_param( 'page' ) ) : 1;
		$per_page  = 6;
		$results   = [];
		$post_type = $request->get_param( 'post_type' ) ? sanitize_text_field( $request->get_param( 'post_type' ) ) : 'any';

		// Search posts.
		$post_query = new WP_Query(
			[
				's'              => $search,
				'post_type'      => $post_type,
				'posts_per_page' => $per_page,
				'paged'          => $page,
				'no_found_rows'  => true,
			]
		);

		foreach ( $post_query->posts as $post ) {
			$results[] = [
				'id'   => $post->ID,
				'text' => $post->post_title . ' (Post)',
			];
		}

		// Search users.
		$user_query = new WP_User_Query(
			[
				'search'         => '*' . $search . '*',
				'search_columns' => [ 'user_login', 'display_name', 'user_email' ],
				'number'         => $per_page,
				'paged'          => $page,
			]
		);

		foreach ( $user_query->get_results() as $user ) {
			$results[] = [
				'id'   => $user->ID,
				'text' => $user->display_name . ' (User)',
			];
		}

		return new WP_REST_Response(
			[
				'success' => true,
				'data'    => $results,
			],
			200
		);
	}

	/**
	 * Register REST API routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_rest_routes() {
		register_rest_route(
			'bpfwe/v1',
			'/filter',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'post_filter_results_rest' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'template_id'        => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'widget_id'          => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_key',
					],
					'page_id'            => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'filter_widget'      => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'inject_id'          => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'group_logic'        => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'search_query'       => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'date_query'         => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'dynamic_filtering'  => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'post_type'          => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'posts_per_page'     => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'order'              => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'order_by'           => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'order_by_meta'      => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'paged'              => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'enable_query_debug' => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'query_id'           => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Change Post Status - REST endpoint (replaces wp_ajax_change_post_status).
		register_rest_route(
			'bpfwe/v1',
			'/change-post-status',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'change_post_status_rest' ],
				'permission_callback' => function ( $request ) {
					$post_id = absint( $request->get_param( 'post_id' ) );
					return current_user_can( 'edit_post', $post_id );
				},
				'args'                => [
					'post_id' => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Pin Post - REST endpoint (replaces wp_ajax_pin_post / wp_ajax_nopriv_pin_post).
		register_rest_route(
			'bpfwe/v1',
			'/pin-post',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'pin_post_rest' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'post_id'   => [
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'pin_class' => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		// Search Related Items - REST endpoint (replaces wp_ajax_bpfwe_search_related_items).
		register_rest_route(
			'bpfwe/v1',
			'/search-related-items',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'bpfwe_search_related_items_rest' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => [
					'q'         => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'page'      => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'post_type' => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);
	}

	/**
	 * Returns the singleton instance.
	 *
	 * @return BPFWE_Ajax|null
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Returns the current filter query args.
	 *
	 * @return array|null
	 */
	public static function get_filter_query() {
		return self::$instance ? self::$instance->filter_query : null;
	}

	/**
	 * Returns the current filter post IDs for faceting.
	 *
	 * @return array|null
	 */
	public static function get_filter_post_ids() {
		return self::$instance ? self::$instance->filter_post_ids : null;
	}

	/**
	 * Constructor for the BPFWE_Ajax class.
	 *
	 * Initializes AJAX hooks, REST API routes, and sets up the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		self::$instance = $this;

		add_action( 'init', [ $this, 'reset_filter_state' ] );
		add_action( 'admin_init', [ $this, 'reset_filter_state' ] );

		// Register REST API routes.
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Registers the 'pre_get_posts' filter hook.
	 *
	 * @since 1.3.2
	 */
	public function register_pre_get_posts_filter() {
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts_filter' ] );
	}
}
new BPFWE_Ajax();

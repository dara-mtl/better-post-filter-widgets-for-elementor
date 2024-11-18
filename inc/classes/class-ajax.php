<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

class BPF_Ajax {
	
	public function change_post_status() {
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0; // Sanitize post ID

		// Check if nonce is set and verify it
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => 'Access Denied' ) );
		}

		// Get the current date and time
		$current_date = current_time( 'mysql' );
		$post_status = get_post_status( $post_id );
		$new_status = ( $post_status === 'publish' ) ? 'draft' : 'publish';

		// Update post status and publication date
		$result = wp_update_post( array(
			'ID'             => $post_id,
			'post_status'    => $new_status,
			'post_date'      => $current_date,
			'post_date_gmt'  => get_gmt_from_date( $current_date )
		));

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => 'Failed to update post status' ) );
		}
		
		wp_die();
		
	}
	
	public function pin_post() {
		// Check if nonce is set and verify it
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
			wp_send_json_error( array( 'message' => 'Access Denied' ) );
		}

		// Sanitize post_id and pin_class
		$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
		$pin_class = isset($_POST['pin_class']) ? sanitize_text_field($_POST['pin_class']) : '';

		// Ensure pin_class is either 'pin' or 'unpin'
		if ($pin_class !== 'pin' && $pin_class !== 'unpin') {
			wp_send_json_error( array( 'message' => 'Invalid operation' ) );
		}

		$user_id = get_current_user_id();
		$post_list = [];

		if (!empty($user_id)) {
			$post_list = get_user_meta($user_id, 'post_id_list', true);
			if (!is_array($post_list)) {
				$post_list = array();
			}
		} else {
			if (isset($_COOKIE['post_id_list'])) {
				$post_list = json_decode(stripslashes($_COOKIE['post_id_list']), true);

				// Check if json_decode failed or post_list is not an array
				if (json_last_error() !== JSON_ERROR_NONE || !is_array($post_list)) {
					$post_list = [];
				}
			}
		}

		// Handle pin/unpin action
		if ($pin_class === 'unpin') {
			if (($key = array_search($post_id, $post_list)) !== false) {
				unset($post_list[$key]);
			}
		} else {
			if (!in_array($post_id, $post_list)) {
				$post_list[] = $post_id;
			}
		}

		// Save post_list either to user_meta or cookies
		if (!empty($user_id)) {
			update_user_meta($user_id, 'post_id_list', $post_list);
		} else {
			setcookie('post_id_list', json_encode($post_list), time() + (86400 * 30), "/"); // 86400 = 1 day
		}

		wp_die();
	}
	
	public function delete_filter_transient() {
		delete_transient('filter_query');
	}
	
	public function post_filter_results() {
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
			wp_send_json_error( array( 'message' => 'Access Denied' ) );
		}
		
		if(empty(absint( $_POST['page_id'] ))) {
			return;
		}
		
		$document = \Elementor\Plugin::$instance->documents->get( absint( $_POST['page_id'] ) );
		$element_data = $document->get_elements_data();
		$widget_data = \Elementor\Utils::find_element_recursive( $element_data, sanitize_key( $_POST['widget_id'] ) );
		
		//$origin = sanitize_key($_POST['origin'] ?? '');
		//$base_url = esc_url($_POST['base'] ?? '');

		$taxonomy_output = $_POST['taxonomy_output'] ?? '';
		$custom_field_output = $_POST['custom_field_output'] ?? '';
		$custom_field_like_output = $_POST['custom_field_like_output'] ?? '';
		$numeric_output = $_POST['numeric_output'] ?? '';

		// Logical operators with default values
		$group_logic = strtoupper($_POST['group_logic'] ?? '') ?: '';
		$meta_key = sanitize_key($_POST['order_by_meta'] ?? '');
		$order = in_array(strtoupper($_POST['order'] ?? ''), array('DESC', 'ASC')) ? strtoupper($_POST['order']) : 'ASC';
		$order_by = sanitize_key($_POST['order_by'] ?? 'date');
		
		$search_terms = isset($_POST['search_query']) ? wp_kses_post($_POST['search_query']) : '';

		$dynamic_filtering = isset($_POST['dynamic_filtering']) ? $_POST['dynamic_filtering'] : false;

		$post_type = sanitize_text_field($_POST['post_type'] ?? 'any');
		//$post_status = isset($_POST['post_status']) && is_array($_POST['post_status']) ? array_map('sanitize_text_field', $_POST['post_status']) : array('publish');
	
		$is_empty = true;
		
		$paged = !empty( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
		set_query_var('paged', $paged);
		set_query_var('page', $paged);
		set_query_var('page_num', $paged);
		
		$args = apply_filters('bpf_ajax_query_args', array(
			'order' => $order,
			'orderby' => $order_by,
			'post_type' => $post_type,
			'paged' => $paged,
		));
		
		if (!empty($search_terms)) {
			$args['s'] = $search_terms;
		}
		
		//if (!empty($post_status)) {
		//	$args['post_status'] = $post_status;
		//}
		
		if ($dynamic_filtering) {
			$archive_type = isset($_POST['archive_type']) ? sanitize_text_field($_POST['archive_type']) : '';
			$archive_taxonomy = isset($_POST['archive_taxonomy']) ? sanitize_text_field($_POST['archive_taxonomy']) : '';
			$archive_id = isset($_POST['archive_id']) ? absint($_POST['archive_id']) : 0;

			// Add conditions based on the archive type
			switch ($archive_type) {
				case 'author':
					$args['author__in'] = array($archive_id);
					break;
				case 'date':
					break;
				case 'category':
				case 'taxonomy':
					$args['tax_query'][] = array(
						'taxonomy' => $archive_taxonomy,
						'field'    => 'id',
						'terms'    => $archive_id,
						'include_children' => true,
					);
					break;
				case 'tag':
					$args['tag__in'] = array($archive_id);
					break;
				case 'post_type':
					$args['post_type'] = sanitize_text_field($_POST['archive_post_type'] ?? 'any');
					break;
				case 'search':
					$args['s'] = get_search_query();
					break;
			}
		}
		
		if (!empty($meta_key)) {
			$args['meta_key'] = $meta_key;
		}
	
		if ($taxonomy_output) {
			$query_and = [];
			$query_or = [];

			foreach ($taxonomy_output as $key => $value) {
				// Check if terms is an array or not
				$terms = is_array($value['terms']) ? array_map('absint', $value['terms']) : [absint($value['terms'])];

				foreach ($terms as $term) {
					$query = [
						'taxonomy' => sanitize_key($value['taxonomy']),
						'field' => 'id',
						'terms' => $term,
						'include_children' => true,
					];

					$row_logic = in_array(strtoupper($value['logic'] ?? ''), array('AND', 'OR')) ? strtoupper($value['logic']) : '';

					if ($row_logic === 'AND') {
						$query_and[] = $query;
					}
					
					if ($row_logic === 'OR') {
						$query_or[] = $query;
					}
				}
			}

			// Set tax_query in $args
			if (!empty($query_and) && !empty($query_or)) {
				$args['tax_query']['relation'] = $group_logic;
				$args['tax_query'][] = array_merge(['relation' => 'AND'], $query_and);
				$args['tax_query'][] = array_merge(['relation' => 'OR'], $query_or);
			} elseif (!empty($query_and)) {
				$args['tax_query']['relation'] = 'AND';
				$args['tax_query'] = array_merge($args['tax_query'], $query_and);
			} elseif (!empty($query_or)) {
				$args['tax_query']['relation'] = 'OR';
				$args['tax_query'] = array_merge($args['tax_query'], $query_or);
			}

			$is_empty = false;
		}

		if ($custom_field_output || $custom_field_like_output || $numeric_output) {
			$meta_query_and = [];
			$meta_query_or = [];
			
			// Add CUSTOM FIELD/ACF to query
			foreach ($custom_field_output as $key => $value) {
				$query = array(
					'key' => sanitize_key($value['taxonomy']),
					'value' => array_map('sanitize_text_field', (array) $value['terms']),
					'compare' => 'IN',
				);
				
				$row_logic = in_array(strtoupper($value['logic'] ?? ''), array('AND', 'OR')) ? strtoupper($value['logic']) : '';

				if ($row_logic === 'AND') {
					$meta_query_and[] = $query;
				}
				
				if ($row_logic === 'OR') {
					$meta_query_or[] = $query;
				}
			}

			// Add INPUT field to query
			foreach ($custom_field_like_output as $key => $value) {
				$query = array(
					'key' => sanitize_key($value['taxonomy']),
					'value' => implode(' ', array_map('sanitize_text_field', (array) $value['terms'])),
					'compare' => 'LIKE',
				);
				
				$row_logic = in_array(strtoupper($value['logic'] ?? ''), array('AND', 'OR')) ? strtoupper($value['logic']) : '';

				if ($row_logic === 'AND') {
					$meta_query_and[] = $query;
				}
				
				if ($row_logic === 'OR') {
					$meta_query_or[] = $query;
				}
			}

			// Add NUMERIC value field to query
			foreach ($numeric_output as $key => $value) {
				$query = array(
					'key' => sanitize_key($value['taxonomy']),
					'value' => is_array($value['terms']) ? array_map('sanitize_text_field', $value['terms']) : sanitize_text_field($value['terms']),
					'type' => 'numeric',
					'compare' => 'BETWEEN',
				);
				
				$row_logic = in_array(strtoupper($value['logic'] ?? ''), array('AND', 'OR')) ? strtoupper($value['logic']) : '';

				if ($row_logic === 'AND') {
					$meta_query_and[] = $query;
				}
				
				if ($row_logic === 'OR') {
					$meta_query_or[] = $query;
				}
			}
			
			// Set meta_query in $args
			if (!empty($meta_query_and) && !empty($meta_query_or)) {
				$args['meta_query']['relation'] = $group_logic;
				$args['meta_query'][] = array_merge(['relation' => 'AND'], $meta_query_and);
				$args['meta_query'][] = array_merge(['relation' => 'OR'], $meta_query_or);
			} elseif (!empty($meta_query_and)) {
				$args['meta_query']['relation'] = 'AND';
				$args['meta_query'] = array_merge($args['meta_query'], $meta_query_and);
			} elseif (!empty($meta_query_or)) {
				$args['meta_query']['relation'] = 'OR';
				$args['meta_query'] = array_merge($args['meta_query'], $meta_query_or);
			}

			$is_empty = false;
		}

		if(!empty($order_by) || !empty($search_terms)) {
			$is_empty = false;
		}

		if($is_empty == false) {
			$widget_data['settings']['args'] = $args;
		}
		
		//if($is_empty == true && $origin === 'archive') {
		if($is_empty == true) {
			delete_transient('filter_query');
			return;
		}
		
		//Fix for Elementor Pro
		//$widget_data['settings']['posts_post_type'] = $post_type;
		
		set_transient('filter_query', $args, 60*60*24);
		error_log('Query Args: ' . print_r($args, true));
		echo wp_json_encode(array(
			//'arg' => $args,
			//'base' => $base_url,
			'html' => $document->render_element( $widget_data )
		));
		
		wp_die();
	}
	
	public function pre_get_posts_filter($query) {
		$filter_data = get_transient('filter_query');

		if ($filter_data && !$query->is_main_query()) {
			foreach($filter_data as $key => $value) {
				$query->set($key, $value);
			}
		}
	}
	
	public function ajax_optimization($plugins) {
		$allowed_ajax_actions = array(
			'change_post_status' => true,
			'pin_post' => true,
			'load_mega_menu' => true,
			'post_filter_results' => true,
		);

		// Early exit if not an AJAX request or not in the allowed actions
		if (!defined('DOING_AJAX') || !DOING_AJAX || !isset($allowed_ajax_actions[$_REQUEST['action']])) {
			return $plugins;
		}

		// Disable unnecessary plugins
		$plugins = array();
		
		// Disable unnecessary WordPress core functionalities
		add_filter('wp_headers', '__return_empty_array', 9999);
		add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');
		add_filter('xmlrpc_enabled', '__return_false');
		add_filter('rest_enabled', '__return_false');
		add_filter('rest_jsonp_enabled', '__return_false');
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'rest_output_link_wp_head');
		remove_action('wp_head', 'wp_shortlink_wp_head');
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
		remove_action('wp_head', 'feed_links', 2);
		remove_action('wp_head', 'feed_links_extra', 3);
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');
		
		return $plugins;
	}

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
		
		add_filter( 'option_active_plugins', [ $this, 'ajax_optimization' ] );
	}

}
new BPF_Ajax();
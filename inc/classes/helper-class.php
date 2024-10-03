<?php

namespace BPF\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

class BPF_Helper {

	public static function get_taxonomies_options() {
		$options = [];

		$taxonomies = get_taxonomies(array(
		), 'objects');

		if (empty($taxonomies)) {
			$options[''] = __('No taxonomies found', 'bpf-widget');
			return $options;
		}

		foreach ($taxonomies as $taxonomy) {
			$options[$taxonomy->name] = $taxonomy->label .' ('. $taxonomy->name .')';
		}

		return $options;
	}
	
	public static function cwm_get_post_types() {
		$post_lists = [];
		
		$post_type_args = array(
			'public'            => true,
			'show_in_nav_menus' => true
		);

		$post_types = get_post_types($post_type_args, 'objects');
		$post_lists['any'] = 'Any';
		
		foreach ($post_types as $post_type) {
			$post_lists[$post_type->name] = $post_type->labels->singular_name;
		}
		
		return $post_lists;
	}
	
	public static function cwm_retrieve_cf7() {
		if (function_exists('wpcf7')) {
			$options = [];
			
			$wpcf7_form_list = get_posts(array(
				'post_type' => 'wpcf7_contact_form',
				'showposts' => 999,
			));
			
			$options[0] = esc_html__('Select a Form', 'bpf-widget');
			
			if (!empty($wpcf7_form_list) && !is_wp_error($wpcf7_form_list)) {
				foreach ($wpcf7_form_list as $post) {
					$options[$post->ID] = $post->post_title;
				}
			} else {
				$options[0] = esc_html__('Create a Form First', 'bpf-widget');
			}
			
			return $options;
		}
	}
	
	public static function cwm_get_post_list($cpt = 'post', $posts_per_page = 100) {
		$options = [];
		
		$list = get_posts(array(
			'post_type'         => $cpt,
			'posts_per_page'    => $posts_per_page,
			'fields'            => 'ids'
		));

		if (!empty($list) && !is_wp_error($list)) {
			foreach ($list as $post_id) {
				$options[$post_id] = get_the_title($post_id);
			}
		}

		return $options;
	}
	
    public static function register_custom_query_var($widget_id, $query_var) {
        add_filter('elementor/query/get/query_vars', function ($query_vars) use ($widget_id, $query_var) {
            $query_vars[] = $query_var . '_' . $widget_id;
            return $query_vars;
        });
    }
	
	public static function cwm_get_icons($icon = '') {
		if (!empty($icon)) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
			return ob_get_clean();
		} else {
			return false;
		}
	}
	
	public static function get_elementor_templates() {
		$args = array(
			'post_type' => 'elementor_library',
			'post_status' => 'publish',
			'numberposts' => -1, // Set to -1 to get all templates
			'orderby' => 'title',
			'order' => 'ASC',
		);
		$elementor_templates = get_posts($args);
		$options = ['' => esc_html__('Select...', 'cwm-widget')];

		if(!empty($elementor_templates)) {
			foreach ($elementor_templates as $elementor_template) {
				if(is_object($elementor_template)) {
					$options[$elementor_template->ID] = $elementor_template->post_title;
				}
			}
		}

		return $options;
	}
	
	public static function get_all_user_roles() {
		$roles = wp_roles()->get_names();
		
		if (empty($roles)) {
			// Handle the case where roles are not available
			return [];
		}

		$options = [
			'' => esc_html__('Select...', 'cwm-widget'),
		];

		foreach ($roles as $role_key => $role_name) {
			$options[$role_key] = $role_name;
		}

		return $options;
	}
	
	public static function get_all_user_meta_keys() {
		$sample_user_id = get_current_user_ID();
		$user_meta_data = get_user_meta($sample_user_id);
		
		if(empty($user_meta_data)) {
			return;
		}
		
		$options = [
			'' => esc_html__('Select...', 'cwm-widget'),
		];

		foreach ($user_meta_data as $key => $value) {
			$options[$key] = $key;
		}

		return $options;
	}
	
	public static function time_elapsed_string($datetime, $full = false) {
		$now = new \DateTime();
		$ago = new \DateTime($datetime);
		$diff = $now->diff($ago);

		$weeks = (int) floor($diff->d / 7);
		$diff->d -= $weeks * 7;

		$string = [
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		];

		$string_values = [];
		foreach ($string as $k => $v) {
			if ($k == 'w') {
				if ($weeks) {
					$string_values[$k] = $weeks . ' ' . $v . ($weeks > 1 ? 's' : '');
				}
			} else {
				if ($diff->$k) {
					$string_values[$k] = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
				}
			}
		}

		if (!$full) {
			$string_values = array_slice($string_values, 0, 1);
		}

		return $string_values ? implode(', ', $string_values) . ' ago' : 'just now';
	}
	
	public static function sanitize_text_with_svg($input) {
        // Define allowed HTML tags and attributes
        $allowed_html = [
            'i' => [ 'class' => [] ], // Allow <i> tags
            'b' => [], // Allow <b> tags
            'strong' => [], // Allow <strong> tags
            'em' => [], // Allow <em> tags
            'u' => [], // Allow <u> tags
            'br' => [], // Allow <br> tags
            'svg' => [ // Allow SVG tags and attributes
                'xmlns' => [],
                'width' => [],
                'height' => [],
                'viewBox' => [],
                'preserveAspectRatio' => [],
                'fill' => [],
                'stroke' => [],
                'stroke-width' => [],
                'd' => [],
                'x' => [],
                'y' => [],
                'cx' => [],
                'cy' => [],
                'r' => [],
                'rx' => [],
                'ry' => [],
                'points' => [],
                'transform' => [],
            ],
            'path' => [ // Allow <path> tags and attributes
                'd' => [],
                'fill' => [],
                'stroke' => [],
                'stroke-width' => [],
                'transform' => [],
            ],
            'circle' => [ // Allow <circle> tags and attributes
                'cx' => [],
                'cy' => [],
                'r' => [],
                'fill' => [],
                'stroke' => [],
                'stroke-width' => [],
            ],
            'rect' => [ // Allow <rect> tags and attributes
                'x' => [],
                'y' => [],
                'width' => [],
                'height' => [],
                'rx' => [],
                'ry' => [],
                'fill' => [],
                'stroke' => [],
                'stroke-width' => [],
            ],
            'line' => [ // Allow <line> tags and attributes
                'x1' => [],
                'y1' => [],
                'x2' => [],
                'y2' => [],
                'stroke' => [],
                'stroke-width' => [],
            ],
            'polygon' => [ // Allow <polygon> tags and attributes
                'points' => [],
                'fill' => [],
                'stroke' => [],
                'stroke-width' => [],
            ],
            'polyline' => [ // Allow <polyline> tags and attributes
                'points' => [],
                'fill' => [],
                'stroke' => [],
                'stroke-width' => [],
            ],
            'text' => [ // Allow <text> tags and attributes
                'x' => [],
                'y' => [],
                'fill' => [],
                'font-size' => [],
                'font-family' => [],
                'text-anchor' => [],
            ],
            'tspan' => [ // Allow <tspan> tags and attributes
                'x' => [],
                'y' => [],
                'fill' => [],
                'font-size' => [],
                'font-family' => [],
            ],
        ];

        // Extract the original viewBox attribute value if present
        preg_match('/<svg[^>]*viewBox=["\']([^"\']*)["\'][^>]*>/', $input, $matches);
        $viewBox = isset($matches[1]) ? $matches[1] : '';

        // Sanitize the input using wp_kses
        $sanitized_input = wp_kses($input, $allowed_html);

        // Re-insert the viewBox attribute if it was present
        if ($viewBox) {
            $sanitized_input = preg_replace('/<svg([^>]*)>/', '<svg$1 viewBox="' . esc_attr($viewBox) . '">', $sanitized_input);
        }

        return $sanitized_input;
    }

}

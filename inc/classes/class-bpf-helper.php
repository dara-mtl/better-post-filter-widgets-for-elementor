<?php
/**
 * Handles the Helper Functions.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * BPF_Helper class
 *
 * This class contains helper functions used throughout the BPF widget plugin.
 * These functions provide various utilities such as retrieving taxonomies, post types,
 * Elementor templates, user roles, and more. Each function is intended to be static,
 * allowing them to be accessed without needing to instantiate the class.
 *
 * @since 1.0.0
 */
class BPF_Helper {

	/**
	 * Retrieves a list of taxonomies and formats them for use in an options dropdown.
	 *
	 * @return array Options array of taxonomies.
	 */
	public static function get_taxonomies_options() {
		$options = [];

		$taxonomies = get_taxonomies(
			array(),
			'objects'
		);

		if ( empty( $taxonomies ) ) {
			$options[''] = __( 'No taxonomies found', 'bpf-widget' );
			return $options;
		}

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label . ' (' . $taxonomy->name . ')';
		}

		return $options;
	}

	/**
	 * Retrieves a list of taxonomies based on given arguments.
	 *
	 * @param array  $args     Arguments to filter taxonomies.
	 * @param string $output   The format to return the taxonomies ('names' or full object).
	 * @param string $operator Logical operator to combine the arguments.
	 * @return array List of taxonomies.
	 */
	public static function cwm_get_taxonomies( $args = [], $output = 'names', $operator = 'and' ) {
		global $wp_taxonomies;

		$field = ( 'names' === $output ) ? 'name' : false;

		if ( isset( $args['object_type'] ) ) {
			$object_type = (array) $args['object_type'];
			unset( $args['object_type'] );
		}

		$taxonomies = wp_filter_object_list( $wp_taxonomies, $args, $operator );

		if ( $field ) {
			$taxonomies = wp_list_pluck( $taxonomies, $field );
		}

		return $taxonomies;
	}

	/**
	 * Retrieves a list of public post types that can be displayed in navigation menus.
	 *
	 * @return array Options array of post types.
	 */
	public static function cwm_get_post_types() {
		$post_lists = [];

		$post_type_args = array(
			'public'            => true,
			'show_in_nav_menus' => true,
		);

		$post_types        = get_post_types( $post_type_args, 'objects' );
		$post_lists['any'] = 'Any';

		foreach ( $post_types as $post_type ) {
			$post_lists[ $post_type->name ] = $post_type->labels->singular_name;
		}

		return $post_lists;
	}

	/**
	 * Retrieves a list of Contact Form 7 forms.
	 *
	 * @return array Options array of Contact Form 7 forms.
	 */
	public static function cwm_retrieve_cf7() {
		if ( function_exists( 'wpcf7' ) ) {
			$options = [];

			$wpcf7_form_list = get_posts(
				array(
					'post_type' => 'wpcf7_contact_form',
					'showposts' => 20,
				)
			);

			$options[0] = esc_html__( 'Select a Form', 'bpf-widget' );

			if ( ! empty( $wpcf7_form_list ) && ! is_wp_error( $wpcf7_form_list ) ) {
				foreach ( $wpcf7_form_list as $post ) {
					$options[ $post->ID ] = $post->post_title;
				}
			} else {
				$options[0] = esc_html__( 'Create a Form First', 'bpf-widget' );
			}

			return $options;
		}
	}

	/**
	 * Retrieves a list of posts from a given custom post type.
	 *
	 * @param string $cpt             Custom post type slug.
	 * @param int    $posts_per_page Number of posts to retrieve.
	 * @return array Options array of post titles.
	 */
	public static function cwm_get_post_list( $cpt = 'post', $posts_per_page = 20 ) {
		$options = [];

		$list = get_posts(
			array(
				'post_type'      => $cpt,
				'posts_per_page' => $posts_per_page,
				'fields'         => 'ids',
			)
		);

		if ( ! empty( $list ) && ! is_wp_error( $list ) ) {
			foreach ( $list as $post_id ) {
				$options[ $post_id ] = get_the_title( $post_id );
			}
		}

		return $options;
	}

	/**
	 * Registers a custom query variable for an Elementor widget.
	 *
	 * @param string $widget_id Widget ID.
	 * @param string $query_var Query variable name.
	 */
	public static function register_custom_query_var( $widget_id, $query_var ) {
		add_filter(
			'elementor/query/get/query_vars',
			function ( $query_vars ) use ( $widget_id, $query_var ) {
				$query_vars[] = $query_var . '_' . $widget_id;
				return $query_vars;
			}
		);
	}

	/**
	 * Retrieves the rendered HTML for an Elementor icon.
	 *
	 * @param string $icon Icon name.
	 * @return string|false The rendered icon HTML or false if no icon.
	 */
	public static function cwm_get_icons( $icon = '' ) {
		if ( ! empty( $icon ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
			return ob_get_clean();
		} else {
			return false;
		}
	}

	/**
	 * Retrieves a list of Elementor templates.
	 *
	 * @return array Options array of Elementor templates.
	 */
	public static function get_elementor_templates() {
		$args                = array(
			'post_type'   => 'elementor_library',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby'     => 'title',
			'order'       => 'ASC',
		);
		$elementor_templates = get_posts( $args );
		$options             = [ '' => esc_html__( 'Select...', 'bpf-widget' ) ];

		if ( ! empty( $elementor_templates ) ) {
			foreach ( $elementor_templates as $elementor_template ) {
				if ( is_object( $elementor_template ) ) {
					$options[ $elementor_template->ID ] = $elementor_template->post_title;
				}
			}
		}

		return $options;
	}

	/**
	 * Retrieves a list of all WordPress user roles.
	 *
	 * @return array Options array of user roles.
	 */
	public static function get_all_user_roles() {
		$roles = wp_roles()->get_names();

		if ( empty( $roles ) ) {
			// Handle the case where roles are not available.
			return [];
		}

		$options = [
			'' => esc_html__( 'Select...', 'bpf-widget' ),
		];

		foreach ( $roles as $role_key => $role_name ) {
			$options[ $role_key ] = $role_name;
		}

		return $options;
	}

	/**
	 * Retrieves a list of all user meta keys for the current user.
	 *
	 * @return array|null Options array of user meta keys or null if no data is found.
	 */
	public static function get_all_user_meta_keys() {
		$sample_user_id = get_current_user_ID();
		$user_meta_data = get_user_meta( $sample_user_id );

		if ( empty( $user_meta_data ) ) {
			return;
		}

		$options = [
			'' => esc_html__( 'Select...', 'bpf-widget' ),
		];

		foreach ( $user_meta_data as $key => $value ) {
			$options[ $key ] = $key;
		}

		return $options;
	}

	/**
	 * Returns a human-readable string for time elapsed.
	 *
	 * @param string $datetime The datetime string to compare.
	 * @param bool   $full     Whether to display the full elapsed time.
	 * @return string The time elapsed string.
	 */
	public static function time_elapsed_string( $datetime, $full = false ) {
		$now  = new \DateTime();
		$ago  = new \DateTime( $datetime );
		$diff = $now->diff( $ago );

		$weeks    = (int) floor( $diff->d / 7 );
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
		foreach ( $string as $k => $v ) {
			if ( 'w' === $k ) {
				if ( $weeks ) {
					$string_values[ $k ] = $weeks . ' ' . $v . ( $weeks > 1 ? 's' : '' );
				}
			} elseif ( $diff->$k ) {
					$string_values[ $k ] = $diff->$k . ' ' . $v . ( $diff->$k > 1 ? 's' : '' );
			}
		}

		if ( ! $full ) {
			$string_values = array_slice( $string_values, 0, 1 );
		}

		return $string_values ? implode( ', ', $string_values ) . ' ago' : 'just now';
	}

	/**
	 * Sanitizes a text input and strips any unsafe tags and attributes, but allows SVG.
	 *
	 * @param string $input The input string.
	 * @return string Sanitized input.
	 */
	public static function sanitize_and_escape_svg_input( $input ) {
		$allowed_html = [
			'i'        => [ 'class' => [] ],
			'b'        => [],
			'strong'   => [],
			'em'       => [],
			'u'        => [],
			'br'       => [],
			'svg'      => [
				'xmlns'               => [],
				'width'               => [],
				'height'              => [],
				'viewBox'             => [],
				'preserveAspectRatio' => [],
				'fill'                => [],
				'stroke'              => [],
				'stroke-width'        => [],
				'd'                   => [],
				'x'                   => [],
				'y'                   => [],
				'cx'                  => [],
				'cy'                  => [],
				'r'                   => [],
				'rx'                  => [],
				'ry'                  => [],
				'points'              => [],
				'transform'           => [],
				'dy'                  => [],
				'dx'                  => [],
			],
			'path'     => [
				'd'            => [],
				'fill'         => [],
				'stroke'       => [],
				'stroke-width' => [],
				'transform'    => [],
			],
			'circle'   => [
				'cx'           => [],
				'cy'           => [],
				'r'            => [],
				'fill'         => [],
				'stroke'       => [],
				'stroke-width' => [],
			],
			'rect'     => [
				'x'            => [],
				'y'            => [],
				'width'        => [],
				'height'       => [],
				'rx'           => [],
				'ry'           => [],
				'fill'         => [],
				'stroke'       => [],
				'stroke-width' => [],
			],
			'line'     => [
				'x1'           => [],
				'y1'           => [],
				'x2'           => [],
				'y2'           => [],
				'stroke'       => [],
				'stroke-width' => [],
			],
			'polygon'  => [
				'points'       => [],
				'fill'         => [],
				'stroke'       => [],
				'stroke-width' => [],
			],
			'polyline' => [
				'points'       => [],
				'fill'         => [],
				'stroke'       => [],
				'stroke-width' => [],
			],
			'text'     => [
				'x'           => [],
				'y'           => [],
				'fill'        => [],
				'font-size'   => [],
				'font-family' => [],
				'text-anchor' => [],
			],
			'tspan'    => [
				'x'           => [],
				'y'           => [],
				'fill'        => [],
				'font-size'   => [],
				'font-family' => [],
				'dy'          => [],
				'dx'          => [],
			],
		];

		preg_match( '/<svg[^>]*viewBox=["\']([^"\']*)["\'][^>]*>/', $input, $matches );
		$view_box = isset( $matches[1] ) ? $matches[1] : '';

		$sanitized_input = wp_kses( $input, $allowed_html );

		if ( $view_box ) {
			$sanitized_input = preg_replace( '/<svg([^>]*)>/', '<svg$1 viewBox="' . esc_attr( $view_box ) . '">', $sanitized_input );
		}

		$sanitized_input = preg_replace( '/(fill|stroke)=["\'](#[a-fA-F0-9]{3,6})["\']/', '$1="$2"', $sanitized_input );

		return $sanitized_input;
	}

	/**
	 * Sanitizes a CSS string by escaping CSS characters.
	 *
	 * @param string $css The CSS string.
	 * @return string Sanitized CSS.
	 */
	public static function sanitize_css( $css ) {
		$css = preg_replace( '/<.*?>/', '', $css );
		$css = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $css );
		return trim( $css );
	}

	/**
	 * Retrieves Elementor's breakpoints array for responsive settings.
	 *
	 * @return array Elementor breakpoints.
	 */
	public static function get_elementor_breakpoints() {
		if ( \Elementor\Plugin::$instance ) {
			$breakpoints_manager = \Elementor\Plugin::$instance->breakpoints;

			if ( $breakpoints_manager ) {
				$breakpoints           = $breakpoints_manager->get_breakpoints();
				$breakpoint_labels     = [];
				$breakpoint_labels[''] = __( 'None', 'bpf-widget' );

				foreach ( $breakpoints as $key => $breakpoint ) {
					$label                       = $breakpoint->get_label();
					$value                       = $breakpoint->get_value();
					$breakpoint_labels[ $value ] = $value . 'px';
				}

				return $breakpoint_labels;
			}
		}

		return [];
	}

	/**
	 * Determine if a meta is ACF.
	 *
	 * @param string $meta_key The meta key.
	 * @return true or false.
	 */
	public static function is_acf_field( $meta_key ) {
		return function_exists( 'get_field_object' ) && get_field_object( $meta_key ) !== false;
	}
}

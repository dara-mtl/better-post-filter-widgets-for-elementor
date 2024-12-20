<?php
/**
 * Pagination Variable Customization for Elementor Widgets
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom function to add 'page_num' to the list of query variables.
 *
 * This function adds a new query variable 'page_num' for pagination
 *
 * @since 1.0.0
 * @param array $query_vars The current list of query variables.
 * @return array Modified list of query variables including 'page_num'.
 */
function custom_elementor_query_vars( $query_vars ) {
	$query_vars[] = 'page_num';
	return $query_vars;
}

add_filter( 'query_vars', 'custom_elementor_query_vars' );

/**
 * Modify the main query for the search page based on the presence of 'post-type' in the URL.
 *
 * This function changes the post type on the search page if 'post-type' is found in the URL.
 *
 * @since 1.0.0
 * @param WP_Query $query The current WP_Query object.
 */
function custom_elementor_pre_get_posts( $query ) {
	if ( ! is_admin() && $query->is_main_query() && is_search() ) {
		if ( isset( $_GET['post-type'] ) && ! empty( $_GET['post-type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$post_type = sanitize_text_field( wp_unslash( $_GET['post-type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification -- Read-only action for search functionality.
			$query->set( 'post_type', $post_type );
		}
	}
}
add_action( 'pre_get_posts', 'custom_elementor_pre_get_posts' );

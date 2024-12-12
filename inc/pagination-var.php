<?php
/**
 * Pagination Variable Customization for Elementor Widgets
 *
 * @package BPF_Widgets
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom function to add 'page_num' to the list of query variables.
 *
 * This function adds a new query variable 'page_num' which can be used for custom pagination
 * in Elementor widgets.
 *
 * @param array $query_vars The current list of query variables.
 * @return array Modified list of query variables including 'page_num'.
 */
function custom_elementor_query_vars( $query_vars ) {
	$query_vars[] = 'page_num';
	return $query_vars;
}

add_filter( 'query_vars', 'custom_elementor_query_vars' );

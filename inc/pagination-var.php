<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function custom_elementor_query_vars( $query_vars ) {
    $query_vars[] = 'page_num';
    return $query_vars;
}
add_filter( 'query_vars', 'custom_elementor_query_vars' );
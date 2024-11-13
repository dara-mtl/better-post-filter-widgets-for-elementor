<?php
	
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function add_elementor_custom_widget_categories( $elements_manager ) {

    $elements_manager->add_category(
        'better-post-and-filter-widgets',
        [
            'title' => __( 'BPF Widgets', 'bpf-widget' )
        ]
    );

}

add_action( 'elementor/elements/categories_registered', 'add_elementor_custom_widget_categories' );
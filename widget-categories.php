<?php
/**
 * Custom Elementor Widget Category Registration.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Adds BPF widget category for Elementor.
 *
 * @param \Elementor\Elements_Manager $elements_manager The elements manager instance.
 */
function add_elementor_custom_widget_categories( $elements_manager ) {

	$elements_manager->add_category(
		'better-post-and-filter-widgets',
		[
			'title' => __( 'BPF Widgets', 'bpf-widget' ),
		]
	);
}

add_action( 'elementor/elements/categories_registered', 'add_elementor_custom_widget_categories' );

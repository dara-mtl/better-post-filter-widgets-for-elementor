<?php
/**
 * Custom Elementor Widget Category Registration.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Adds BPFWE widget category for Elementor.
 *
 * @param \Elementor\Elements_Manager $elements_manager The elements manager instance.
 */
function bpfwe_categories( $elements_manager ) {

	$elements_manager->add_category(
		'better-post-and-filter-widgets',
		[
			'title' => esc_html__( 'BPFWE Widgets', 'better-post-filter-widgets-for-elementor' ),
		]
	);
}

add_action( 'elementor/elements/categories_registered', 'bpfwe_categories' );

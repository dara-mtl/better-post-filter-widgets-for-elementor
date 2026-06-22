<?php
/**
 * BPFWE Shortcodes
 *
 * @package BPFWE_Widgets
 * @since 1.9.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mobile Filter Shortcode
 * Usage: [filter_mobile_view id="123abc"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bpfwe_shortcode_filter_mobile( $atts ) {
	$atts = shortcode_atts(
		[ 'id' => '' ],
		$atts,
		'filter_mobile_view'
	);

	if ( empty( $atts['id'] ) ) {
		return '';
	}

	// Split comma-separated IDs and trim whitespace.
	$raw_ids = array_map( 'trim', explode( ',', $atts['id'] ) );

	$output = '';
	foreach ( $raw_ids as $raw_id ) {
		$filter_id = sanitize_key( $raw_id );
		if ( empty( $filter_id ) ) {
			continue;
		}
		$output .= sprintf(
			'<div class="bpfwe-mobile-target" data-filter-id="%s"></div>',
			esc_attr( $filter_id )
		);
	}

	return $output;
}
add_shortcode( 'filter_mobile_view', 'bpfwe_shortcode_filter_mobile' );


/**
 * Selected Terms / Filter Options Shortcode
 * Usage: [filter_terms id="123abc"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bpfwe_shortcode_filter_terms( $atts ) {
	$atts = shortcode_atts(
		[ 'id' => '' ],
		$atts,
		'filter_terms'
	);

	$id = sanitize_text_field( $atts['id'] );
	if ( empty( $id ) ) {
		return '';
	}

	return '<div class="bpfwe-selected-terms" data-filter-id="' . esc_attr( $id ) . '"></div>';
}
add_shortcode( 'filter_terms', 'bpfwe_shortcode_filter_terms' );


/**
 * Filter Count Shortcode
 * Usage: [filter_count id="123abc"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bpfwe_shortcode_filter_count( $atts ) {
	$atts = shortcode_atts(
		[ 'id' => '' ],
		$atts,
		'filter_count'
	);

	$id = sanitize_text_field( $atts['id'] );
	if ( empty( $id ) ) {
		return '';
	}

	return '<div class="bpfwe-selected-count" data-filter-id="' . esc_attr( $id ) . '"></div>';
}
add_shortcode( 'filter_count', 'bpfwe_shortcode_filter_count' );


/**
 * Active Filters / Quick Deselect (Tags/Chips) Shortcode
 * Usage: [filter_tags id="123abc"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bpfwe_shortcode_filter_tags( $atts ) {
	$atts = shortcode_atts(
		[ 'id' => '' ],
		$atts,
		'filter_tags'
	);

	$id = sanitize_text_field( $atts['id'] );
	if ( empty( $id ) ) {
		return '';
	}

	return '<div class="bpfwe-quick-deselect" data-filter-id="' . esc_attr( $id ) . '"></div>';
}
add_shortcode( 'filter_tags', 'bpfwe_shortcode_filter_tags' );

/**
 * Feed Filtering Buttons Shortcode
 * Usage: [feed_filters id="123abc"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bpfwe_shortcode_feed_filters( $atts ) {
	$atts = shortcode_atts(
		[ 'id' => '' ],
		$atts,
		'feed_filters'
	);

	$id = sanitize_text_field( $atts['id'] );
	if ( empty( $id ) ) {
		return '';
	}

	return '<div class="bpfwe-feed-filters" data-feed-id="' . esc_attr( $id ) . '"></div>';
}
add_shortcode( 'feed_filters', 'bpfwe_shortcode_feed_filters' );


/**
 * Feed Anchor Filters Shortcode
 * Usage: [feed_anchor_filters id="123abc"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bpfwe_shortcode_feed_anchor_filters( $atts ) {
	$atts = shortcode_atts(
		[ 'id' => '' ],
		$atts,
		'feed_anchor_filters'
	);

	$id = sanitize_text_field( $atts['id'] );
	if ( empty( $id ) ) {
		return '';
	}

	return '<div class="bpfwe-feed-anchor-filters" data-feed-id="' . esc_attr( $id ) . '"></div>';
}
add_shortcode( 'feed_anchor_filters', 'bpfwe_shortcode_feed_anchor_filters' );

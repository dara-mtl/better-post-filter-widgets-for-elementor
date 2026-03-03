<?php
/**
 * Custom AJAX handler for BPFWE plugin.
 *
 * This file provides a dedicated endpoint for frontend filtering requests.
 *
 * @package Better_Post_Filter_Widgets_For_Elementor
 */

define( 'DOING_AJAX', true );

// Bail early before loading WordPress if no action is present.
if ( empty( $_POST['action'] ) ) {
	die( '-1' );
}

$bpfwe_bootstrap_path = dirname( __DIR__, 2 ) . '/';

if ( ! file_exists( $bpfwe_bootstrap_path . 'wp-load.php' ) ) {
	die( '-1' );
}

if ( ! defined( 'ABSPATH' ) ) {
	require_once $bpfwe_bootstrap_path . 'wp-load.php';
}

$bpfwe_allowed_actions = array(
	'change_post_status',
	'pin_post',
	'post_filter_results',
	'load_page_callback',
);

$bpfwe_requested_action = sanitize_text_field( wp_unslash( $_POST['action'] ) );

if ( ! in_array( $bpfwe_requested_action, $bpfwe_allowed_actions, true ) ) {
	wp_die( '0' );
}

// Send headers after WordPress is loaded so get_option() and send_nosniff_header() are available.
header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
header( 'X-Robots-Tag: noindex' );
send_nosniff_header();
header( 'Cache-Control: no-cache' );
header( 'Pragma: no-cache' );

// Verify nonce after WordPress is loaded and action is validated.
$bpfwe_nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

if ( ! $bpfwe_nonce || ! wp_verify_nonce( $bpfwe_nonce, 'ajax-nonce' ) ) {
	wp_send_json_error( array( 'message' => 'Access Denied' ), 403 );
	wp_die();
}

// Fire the appropriate action hook based on login status, mirroring how WordPress core admin-ajax.php works.
if ( is_user_logged_in() ) {
	do_action( 'wp_ajax_' . $bpfwe_requested_action );
} else {
	do_action( 'wp_ajax_nopriv_' . $bpfwe_requested_action );
}

wp_die( '0' );
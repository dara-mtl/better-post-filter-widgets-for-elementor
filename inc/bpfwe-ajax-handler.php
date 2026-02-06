<?php
/**
 * Custom AJAX handler for BPFWE plugin.
 *
 * This file provides a dedicated endpoint for frontend filtering requests.
 *
 * @package Better_Post_Filter_Widgets_For_Elementor
 */

define( 'DOING_AJAX', true );

if ( ! isset( $_POST['action'] ) ) {
	die( '-1' );
}

$bpfwe_bootstrap_path = preg_replace( '/wp-content(?!.*wp-content).*/', '', __DIR__ );

if ( ! defined( 'ABSPATH' ) ) {
	require_once $bpfwe_bootstrap_path . 'wp-load.php';
}

header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
header( 'X-Robots-Tag: noindex' );
send_nosniff_header();
header( 'Cache-Control: no-cache' );
header( 'Pragma: no-cache' );

if ( ! isset( $_REQUEST['action'] ) ) {
	die( '-1' );
}

$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

// Verify the nonce.
if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
	wp_send_json_error( [ 'message' => 'Access Denied' ], 403 );
	wp_die();
}

$bpfwe_allowed_actions = [];

$bpfwe_allowed_actions = array(
	'change_post_status',
	'pin_post',
	'post_filter_results',
	'load_page_callback',
);

$bpfwe_requested_action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );

if ( ! in_array( $bpfwe_requested_action, $bpfwe_allowed_actions, true ) ) {
	wp_die( '0' );
}

if ( is_user_logged_in() ) {
	do_action( 'wp_ajax_' . $bpfwe_requested_action );
} else {
	do_action( 'wp_ajax_nopriv_' . $bpfwe_requested_action );
}

die( '0' );

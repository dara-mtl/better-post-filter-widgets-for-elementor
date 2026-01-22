<?php
/**
 * Custom AJAX handler for BPFWE plugin.
 *
 * This file replicates the core AJAX functionality but loads minimal WordPress environment.
 *
 * @package Better_Post_Filter_Widgets_For_Elementor
 */

define( 'DOING_AJAX', true );

if ( ! isset( $_POST['action'] ) ) {
	die( '-1' );
}

$bootstrap_path = preg_replace( '/wp-content(?!.*wp-content).*/', '', __DIR__ );
require_once $bootstrap_path . 'wp-load.php';

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
}

$allowed_actions = [];

$allowed_actions = array(
	'change_post_status',
	'pin_post',
	'post_filter_results',
	'load_page_callback',
);

$requested_action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );

if ( in_array( $requested_action, $allowed_actions, true ) ) {
	if ( is_user_logged_in() ) {
		do_action( 'wp_ajax_' . $requested_action );
	} else {
		do_action( 'wp_ajax_nopriv_' . $requested_action );
	}
}

die( '0' );

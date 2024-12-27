<?php
/**
 * Post Content Dynamic Tag.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Elementor custom dynamic tag.
 *
 * A dynamic tag to fetch and display the post content of a page.
 *
 * @since 1.0.0
 */
class Post_Content extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Get the name of the dynamic tag.
	 *
	 * @return string The name of the dynamic tag.
	 */
	public function get_name() {
		return 'post-content-tag';
	}

	/**
	 * Get the title of the dynamic tag.
	 *
	 * @return string The title of the dynamic tag.
	 */
	public function get_title() {
		return __( 'Post Content', 'bpf-widget' );
	}

	/**
	 * Get the group that the dynamic tag belongs to.
	 *
	 * @return string The group name.
	 */
	public function get_group() {
		return 'post';
	}

	/**
	 * Get the categories for the dynamic tag.
	 *
	 * @return array The categories that the dynamic tag belongs to.
	 */
	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	/**
	 * Register the controls for the dynamic tag.
	 *
	 * This method defines the settings and options that can be customized in the Elementor editor.
	 */
	protected function register_controls() {
		$this->add_control(
			'max_length',
			[
				'label' => esc_html__( 'Content Length', 'bpf-widget' ),
				'type'  => Controls_Manager::NUMBER,
			]
		);
	}

	/**
	 * Render the post content.
	 *
	 * This method displays the content of the current post, potentially trimmed based on the `max_length` setting.
	 * If the page is in preview mode or in the admin area, a placeholder message is displayed instead of the content.
	 *
	 * @return void
	 */
	public function render() {
		$current_url = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$current_url = esc_url( 'http://' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		}

		$max_length      = absint( $this->get_settings( 'max_length' ) );
		$trimmed_content = wp_strip_all_tags( get_the_content() );

		if ( $max_length ) {
			$post_content = wp_trim_words( $trimmed_content, $max_length, '...' );
		}

		if ( strpos( $current_url, 'preview_nonce' ) !== false || is_admin() ) {
			echo esc_html__( 'This is the post content. The full content will only display on the live page.', 'bpf-widget' );
		} elseif ( ! empty( $post_content ) ) {
				echo wp_kses_post( $post_content );
		} else {
			$full_content = the_content();
			echo wp_kses_post( $full_content ?? '' );
		}
	}
}

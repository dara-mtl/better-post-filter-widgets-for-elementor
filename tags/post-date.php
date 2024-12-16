<?php
/**
 * Post Date Dynamic Tag.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Elementor custom dynamic tag.
 *
 * A dynamic tag to fetch and display the post date of a page.
 *
 * @since 1.0.0
 */
class Post_Date extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Get the name of the dynamic tag.
	 *
	 * @return string The name of the dynamic tag.
	 */
	public function get_name() {
		return 'post-date-tag';
	}

	/**
	 * Get the title of the dynamic tag.
	 *
	 * @return string The title of the dynamic tag.
	 */
	public function get_title() {
		return __( 'Post Date', 'bpf-widget' );
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
	 * Render the post date.
	 *
	 * This method retrieves and displays the publication date of the current post.
	 *
	 * @return void
	 */
	public function render() {
		$date = get_the_date();
		echo wp_kses_post( $date );
	}
}

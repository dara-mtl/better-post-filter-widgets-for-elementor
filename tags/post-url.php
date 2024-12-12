<?php
/**
 * Post URL Dynamic Tag.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Post_URL.
 *
 * Dynamic tag for retrieving the URL of the current post.
 *
 * @since 1.0.0
 */
class Post_URL extends Data_Tag {

	/**
	 * Get tag name.
	 *
	 * Retrieve the dynamic tag name for internal use.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Tag name.
	 */
	public function get_name() {
		return 'post-url-tag';
	}

	/**
	 * Get tag title.
	 *
	 * Retrieve the dynamic tag title displayed in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Tag title.
	 */
	public function get_title() {
		return esc_html__( 'Post URL', 'bpf-widget' );
	}

	/**
	 * Get tag group.
	 *
	 * Retrieve the group the tag belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Dynamic tag group.
	 */
	public function get_group() {
		return 'post';
	}

	/**
	 * Get tag categories.
	 *
	 * Retrieve the list of categories the tag belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Dynamic tag categories.
	 */
	public function get_categories() {
		return [ TagsModule::URL_CATEGORY ];
	}

	/**
	 * Get dynamic tag value.
	 *
	 * Generate and return the URL of the current post.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $options Optional. Additional options for generating the value.
	 * @return string URL of the current post.
	 */
	public function get_value( array $options = [] ) {
		return get_permalink();
	}
}

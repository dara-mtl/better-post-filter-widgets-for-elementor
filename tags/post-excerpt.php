<?php
/**
 * Post Excerpt Dynamic Tag.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Post_Excerpt.
 *
 * Dynamic tag for displaying the post excerpt.
 *
 * @since 1.0.0
 */
class Post_Excerpt extends Tag {

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
		return 'post-excerpt';
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
		return esc_html__( 'Post Excerpt', 'bpf-widget' );
	}

	/**
	 * Get dynamic tag group.
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
	 * Get dynamic tag categories.
	 *
	 * Retrieve the list of categories the tag belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Dynamic tag categories.
	 */
	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	/**
	 * Render the dynamic tag.
	 *
	 * Outputs the post excerpt, with optional trimming based on max length.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {
		$post = get_post();

		if ( ! $post || empty( $post->post_excerpt ) ) {
			return;
		}

		$post_excerpt = $post->post_excerpt;

		$max_length = (int) $this->get_settings( 'max_length' );

		if ( $max_length ) {
			$post_excerpt = wp_trim_words( $post_excerpt, $max_length, '...' );
		}

		echo wp_kses_post( $post_excerpt );
	}

	/**
	 * Register controls.
	 *
	 * Adds a control for specifying the maximum length of the post excerpt.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->add_control(
			'max_length',
			[
				'label' => esc_html__( 'Excerpt Length', 'bpf-widget' ),
				'type'  => Controls_Manager::NUMBER,
			]
		);
	}
}

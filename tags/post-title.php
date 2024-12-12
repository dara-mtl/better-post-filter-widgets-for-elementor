<?php
/**
 * Post Title Dynamic Tag.
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
 * Class Post_Title.
 *
 * Dynamic tag for displaying the post title with optional length control.
 *
 * @since 1.0.0
 */
class Post_Title extends \Elementor\Core\DynamicTags\Tag {

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
		return 'post-title-tag';
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
		return __( 'Post Title', 'bpf-widget' );
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
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	/**
	 * Register controls.
	 *
	 * Define the controls for the dynamic tag, such as title length.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->add_control(
			'max_length',
			[
				'label' => esc_html__( 'Title Length', 'bpf-widget' ),
				'type'  => Controls_Manager::NUMBER,
			]
		);
	}

	/**
	 * Render dynamic tag output.
	 *
	 * Generates the HTML output for the post title, with optional trimming based on length control.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {

		$title = get_the_title();

		$max_length = (int) $this->get_settings( 'max_length' );

		if ( $max_length ) {
			$title = wp_trim_words( $title, $max_length, '...' );
		}

		echo wp_kses_post( $title );
	}
}

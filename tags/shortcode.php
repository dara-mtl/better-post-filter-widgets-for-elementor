<?php
/**
 * Shortcode Dynamic Tag.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Shortcode.
 *
 * Dynamic tag for retrieving a shortcode content.
 *
 * @since 1.0.0
 */
class Shortcode extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Get the tag name.
	 *
	 * This method returns a unique identifier for the dynamic tag.
	 *
	 * @return string The tag name.
	 */
	public function get_name() {
		return 'shortcode-tag';
	}

	/**
	 * Get the title of the dynamic tag.
	 *
	 * This method returns the title of the tag as shown in the Elementor interface.
	 *
	 * @return string The title of the dynamic tag.
	 */
	public function get_title() {
		return __( 'Shortcode', 'bpf-widget' );
	}

	/**
	 * Get the group of the dynamic tag.
	 *
	 * This method determines the group in which the dynamic tag will appear in the Elementor interface.
	 *
	 * @return string The group name.
	 */
	public function get_group() {
		return 'bpf-dynamic-tags';
	}

	/**
	 * Get the categories of the dynamic tag.
	 *
	 * This method returns an array of categories the tag belongs to, allowing it to be grouped
	 * with other similar tags.
	 *
	 * @return array The categories of the dynamic tag.
	 */
	public function get_categories() {
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::GALLERY_CATEGORY,
			TagsModule::IMAGE_CATEGORY,
			TagsModule::MEDIA_CATEGORY,
			TagsModule::NUMBER_CATEGORY,
		];
	}

	/**
	 * Register controls for the dynamic tag.
	 *
	 * This method registers the control to allow users to input a shortcode within the Elementor editor.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->add_control(
			'shortcode',
			[
				'label'       => esc_html__( 'Shortcode', 'bpf-widget' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( '[your-shortcode]', 'bpf-widget' ),
			]
		);
	}

	/**
	 * Render the dynamic tag.
	 *
	 * This method processes and renders the shortcode. It executes the shortcode using `do_shortcode()`
	 * and safely outputs the result with `wp_kses_post()`.
	 *
	 * @return void
	 */
	public function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['shortcode'] ) ) {
			return;
		}

		$shortcode_string = $settings['shortcode'];
		$value            = do_shortcode( $shortcode_string );
		$value            = wp_kses_post( $value );

		echo $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $value is sanitized with wp_kses_post() before output.
	}
}

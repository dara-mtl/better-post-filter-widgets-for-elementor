<?php
/**
 * Featured Image Dynamic Tag.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

namespace BPFWE_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Post_Featured_Image.
 *
 * Dynamic tag for retrieving the featured image of a post.
 *
 * @since 1.0.0
 */
class Post_Featured_Image extends Data_Tag {

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
		return 'post-featured-image';
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
		return [ TagsModule::IMAGE_CATEGORY ];
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
		return esc_html__( 'Featured Image', 'better-post-filter-widgets-for-elementor' );
	}

	/**
	 * Get dynamic tag value.
	 *
	 * Retrieve the featured image data for the current post. If no featured image
	 * exists, the fallback value is returned.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $options Optional. Additional options.
	 * @return array Featured image data or fallback data.
	 */
	public function get_value( array $options = [] ) {
		$thumbnail_id = get_post_thumbnail_id();

		if ( $thumbnail_id ) {
			$image_url = wp_get_attachment_image_src( $thumbnail_id, 'full' );

			$image_data = [
				'id'  => $thumbnail_id,
				'url' => $image_url ? $image_url[0] : '',
			];
		} else {
			$image_data = $this->get_settings( 'fallback' );
		}

		return $image_data;
	}

	/**
	 * Register controls.
	 *
	 * Add controls for setting a fallback image if no featured image is available.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->add_control(
			'fallback',
			[
				'label' => esc_html__( 'Fallback', 'better-post-filter-widgets-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);
	}

	/**
	 * Initialize the filter to set background image.
	 *
	 * Hooks into 'elementor/frontend/before_render' to modify background image settings 
	 * dynamically before rendering Elementor elements on the frontend.
	 *
	 * @param array $data Optional element data array.
	 * @param mixed $args Optional additional arguments.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		add_filter( 'elementor/frontend/before_render', [ $this, 'set_background_image' ] );
	}

	/**
	 * Set background image on widget container.
	 *
	 * Modify the widget settings to include the featured image as a background image before rendering,
	 * only for elements using this dynamic tag.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param \Elementor\Element_Base $element The element being rendered.
	 * @return \Elementor\Element_Base The modified element.
	 */
	public function set_background_image( $element ) {
		if ( ! $element instanceof \Elementor\Element_Base ) {
			return;
		}

		$settings    = $element->get_settings_for_display();
		$dynamic_map = isset( $settings['__dynamic__'] ) ? $settings['__dynamic__'] : [];

		if ( ! is_array( $dynamic_map ) ) {
			return;
		}

		foreach ( $dynamic_map as $setting_key => $tag_data ) {
			if ( 0 !== strpos( $setting_key, 'background_' ) ) {
				continue;
			}

			$is_using_tag = (
				( is_array( $tag_data ) && isset( $tag_data['action'] ) && $tag_data['action'] === $this->get_name() ) ||
				( is_string( $tag_data ) && false !== strpos( $tag_data, $this->get_name() ) )
			);

			if ( ! $is_using_tag ) {
				continue;
			}

			$post_id      = get_the_ID();
			$thumbnail_id = get_post_thumbnail_id( $post_id );
			if ( ! $thumbnail_id ) {
				continue;
			}

			$image_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );

			if ( ! $image_url ) {
				continue;
			}

			$current_style = $element->get_render_attribute_string( '_wrapper' );

			if ( false === strpos( $current_style, $image_url ) ) {
				$element->add_render_attribute(
					'_wrapper',
					'style',
					sprintf(
						'background-image: url(%s);',
						esc_url( $image_url )
					)
				);
			}

			break;
		}
	}
}

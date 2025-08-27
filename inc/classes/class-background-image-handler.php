<?php
/**
 * Handles Background Image Functionality for Elementor Widgets.
 *
 * @package BPFWE_Widgets
 * @since 1.5.2
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to set background image on Elementor widget container.
 */
class BPFWE_Background_Image {
	/**
	 * Set background image on widget container.
	 *
	 * Modify the widget settings to include the featured image as a background image before rendering,
	 * only for elements using this dynamic tag.
	 *
	 * @since 1.5.2
	 * @access public
	 *
	 * @param \Elementor\Element_Base $element The element being rendered.
	 * @return void
	 */
	public function set_background_image( $element ) {
		static $cached_settings    = [];
		static $processed_elements = [];

		$bg_supported_widgets = [
			'section',
			'column',
			'container',
			'inner-section',
			'image',
			'video',
		];

		if ( ! $element instanceof \Elementor\Element_Base || ! in_array( $element->get_name(), $bg_supported_widgets, true ) ) {
			return;
		}

		// error_log( 'BPFWE Background Image handler ran for element ID: ' . $element->get_id() ); --Enable for debugging.

		$element_id = $element->get_id();

		// Check if element has already been fully processed.
		if ( isset( $processed_elements[ $element_id ] ) ) {
			if ( ! empty( $cached_settings[ $element_id ] ) ) {
				$this->apply_background_image( $element, $cached_settings[ $element_id ] );
			}
			return;
		}

		$settings    = $element->get_settings();
		$dynamic_map = $settings['__dynamic__'] ?? [];

		if ( empty( $dynamic_map ) ) {
			$processed_elements[ $element_id ] = true;
			return;
		}

		$element_keys = [];

		if ( is_array( $dynamic_map ) ) {
			foreach ( $dynamic_map as $setting_key => $tag_data ) {
				if ( strpos( $setting_key, 'background_' ) !== 0 ) {
					continue;
				}

				$tag_name = null;
				if ( is_array( $tag_data ) && isset( $tag_data['action'] ) ) {
					$tag_name = $tag_data['action'];
				} elseif ( is_string( $tag_data ) && preg_match( '/name="([^"]+)"/', $tag_data, $matches ) ) {
					$tag_name = $matches[1];
				}

				if ( ! $tag_name || ! in_array( $tag_name, [ 'image-custom-field', 'post-featured-image' ], true ) ) {
					continue;
				}

				if ( 'image-custom-field' === $tag_name ) {
					$custom_key   = null;
					$field_source = null;
					if ( is_string( $tag_data ) && preg_match( '/settings="([^"]+)"/', $tag_data, $matches ) ) {
						$json           = urldecode( $matches[1] );
						$settings_array = json_decode( $json, true );
						if ( is_array( $settings_array ) && isset( $settings_array['custom_key'] ) ) {
							$custom_key = sanitize_key( $settings_array['custom_key'] );
						}
						if ( is_array( $settings_array ) && isset( $settings_array['field_source'] ) ) {
							$field_source = sanitize_key( $settings_array['field_source'] );
						}
					} elseif ( is_array( $tag_data ) && isset( $tag_data['settings'] ) && is_array( $tag_data['settings'] ) ) {
						$settings_array = $tag_data['settings'];

						if ( isset( $settings_array['custom_key'] ) ) {
							$custom_key = sanitize_key( $settings_array['custom_key'] );
						}
						if ( isset( $settings_array['field_source'] ) ) {
							$field_source = sanitize_key( $settings_array['field_source'] );
						}
					}

					if ( $custom_key ) {
						$element_keys[ $setting_key ] = [
							'type'                => 'custom_field',
							'custom_key'          => $custom_key,
							'field_source'        => $field_source,
							'background_position' => $settings['background_position'] ?? '',
							'background_repeat'   => $settings['background_repeat'] ?? '',
							'background_size'     => $settings['background_size'] ?? '',
						];
					}
				} elseif ( 'post-featured-image' === $tag_name ) {
					$element_keys[ $setting_key ] = [
						'type'                => 'featured_image',
						'background_position' => $settings['background_position'] ?? '',
						'background_repeat'   => $settings['background_repeat'] ?? '',
						'background_size'     => $settings['background_size'] ?? '',
					];
				}
			}
		}

		$processed_elements[ $element_id ] = true;
		$cached_settings[ $element_id ]    = $element_keys;

		if ( empty( $element_keys ) ) {
			return;
		}

		$this->apply_background_image( $element, $element_keys );
	}

	/**
	 * Apply the background image to the element.
	 *
	 * @since 1.5.2
	 * @access private
	 *
	 * @param \Elementor\Element_Base $element The element being rendered.
	 * @param array                   $element_keys The background settings.
	 * @return void
	 */
	private function apply_background_image( $element, $element_keys ) {
		global $_bpfwe_context;

		$post_id = $_bpfwe_context ? $_bpfwe_context : get_the_ID();
		if ( ! $post_id || ! get_post( $post_id ) ) {
			return;
		}

		$element_id = $element->get_id();

		// Prevent duplicate application.
		if ( $element->get_settings( '_bpfwe_bg_applied' ) ) {
			return;
		}

		foreach ( $element_keys as $setting_key => $data ) {
			$image_url  = '';
			$image_meta = null;

			if ( 'custom_field' === $data['type'] ) {

				if ( 'post' === $data['field_source'] || ! $data['field_source'] ) {
					$image_meta = get_post_meta( $post_id, $data['custom_key'], true );
				}

				if ( 'tax' === $data['field_source'] ) {
					$terms      = get_the_terms( $post_id, $data['custom_key'] );
					$image_meta = '';
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						$term_id    = $terms[0]->term_id;
						$image_meta = get_term_meta( $term_id, $data['custom_key'], true );
					}
				}

				if ( is_array( $image_meta ) && isset( $image_meta['url'] ) ) {
					// ACF image field with array return format.
					$image_url = esc_url_raw( $image_meta['url'] );
				} elseif ( is_numeric( $image_meta ) ) {
					// Stored as attachment ID.
					$image_url = wp_get_attachment_image_url( $image_meta, 'full' );
				} elseif ( is_string( $image_meta ) && filter_var( $image_meta, FILTER_VALIDATE_URL ) ) {
					// Stored as URL string.
					$image_url = esc_url_raw( $image_meta );
				}
			} elseif ( 'featured_image' === $data['type'] ) {
				$image_url = get_the_post_thumbnail_url( $post_id, 'full' );
			}

			if ( ! $image_url ) {
				continue;
			}

			$css = sprintf( 'background-image: url(%s);', esc_url( $image_url ) );
			if ( ! empty( $data['background_position'] ) ) {
				$css .= 'background-position:' . esc_attr( $data['background_position'] ) . ';';
			}
			if ( ! empty( $data['background_repeat'] ) ) {
				$css .= 'background-repeat:' . esc_attr( $data['background_repeat'] ) . ';';
			}
			if ( ! empty( $data['background_size'] ) ) {
				$css .= 'background-size:' . esc_attr( $data['background_size'] ) . ';';
			}

			$lazy_loaded_class = 'e-lazyloaded';

			$element->add_render_attribute( '_wrapper', 'style', $css );
			$element->add_render_attribute( '_wrapper', 'class', $lazy_loaded_class );

			$element->set_settings( '_bpfwe_bg_applied', true );

			break;
		}
	}

	/**
	 * Constructor.
	 *
	 * @since 1.5.2
	 * @access public
	 */
	public function __construct() {
		add_filter( 'elementor/frontend/before_render', [ $this, 'set_background_image' ] );
	}
}

new BPFWE_Background_Image();

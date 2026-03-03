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
		static $cached_settings          = [];
		static $no_dynamic_elements      = [];
		static $applied_element_post_ids = [];

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

		// If we already know this element has no relevant dynamic tags, skip it.
		if ( isset( $no_dynamic_elements[ $element_id ] ) ) {
			return;
		}

		// Resolve the settings/dynamic map once per element ID and cache it.
		if ( ! isset( $cached_settings[ $element_id ] ) ) {
			$settings    = $element->get_settings();
			$dynamic_map = $settings['__dynamic__'] ?? [];

			if ( empty( $dynamic_map ) ) {
				$no_dynamic_elements[ $element_id ] = true;
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
						$field_source = 'post';

						if ( is_string( $tag_data ) && preg_match( '/settings="([^"]+)"/', $tag_data, $matches ) ) {
							$json           = urldecode( $matches[1] );
							$settings_array = json_decode( $json, true );
							if ( is_array( $settings_array ) ) {
								if ( isset( $settings_array['custom_key'] ) ) {
									$custom_key = sanitize_key( $settings_array['custom_key'] );
								}
								if ( isset( $settings_array['field_source'] ) ) {
									$field_source = sanitize_key( $settings_array['field_source'] );
								}
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
							'field_source'        => 'post',
							'background_position' => $settings['background_position'] ?? '',
							'background_repeat'   => $settings['background_repeat'] ?? '',
							'background_size'     => $settings['background_size'] ?? '',
						];
					}
				}
			}

			if ( empty( $element_keys ) ) {
				$no_dynamic_elements[ $element_id ] = true;
				return;
			}

			$cached_settings[ $element_id ] = $element_keys;
		}

		// Resolve the correct context ID based on field_source, mirroring what the dynamic tags themselves do.
		global $_bpfwe_context, $bpfwe_term_id, $bpfwe_user_id;

		$first_entry  = reset( $cached_settings[ $element_id ] );
		$field_source = $first_entry['field_source'] ?? 'post';

		$resolved_id = null;

		if ( 'tax' === $field_source ) {
			if ( ! empty( $bpfwe_term_id ) ) {
				$resolved_id = absint( $bpfwe_term_id );
			} elseif ( is_tax() || is_category() || is_tag() ) {
				$resolved_id = get_queried_object_id();
			}
		} elseif ( 'user' === $field_source ) {
			if ( ! empty( $bpfwe_user_id ) ) {
				$resolved_id = absint( $bpfwe_user_id );
			} else {
				$resolved_id = get_current_user_id();
			}
		} elseif ( 'author' === $field_source ) {
			if ( is_author() ) {
				$resolved_id = get_queried_object_id();
			} else {
				$resolved_id = absint( get_the_author_meta( 'ID' ) );
			}
		} else {
			// 'post', 'theme', or featured_image - fall back to post context.
			if ( ! empty( $_bpfwe_context ) ) {
				$resolved_id = absint( $_bpfwe_context );
			} else {
				$resolved_id = get_the_ID();
			}
		}

		if ( ! $resolved_id ) {
			return;
		}

		// For post-sourced entries, validate the ID is a real post.
		if ( in_array( $field_source, [ 'post', 'featured_image' ], true ) && ! get_post( $resolved_id ) ) {
			return;
		}

		// Prevent duplicate application for the same element + resolved ID combination.
		$application_key = $element_id . '_' . $resolved_id;
		if ( isset( $applied_element_post_ids[ $application_key ] ) ) {
			return;
		}
		$applied_element_post_ids[ $application_key ] = true;

		$this->apply_background_image( $element, $cached_settings[ $element_id ], $resolved_id );
	}

	/**
	 * Resolve the image URL from meta for a given source and resolved ID.
	 *
	 * @since 1.5.2
	 * @access private
	 *
	 * @param string $field_source The field source (post, tax, user, author).
	 * @param string $custom_key   The meta key to look up.
	 * @param int    $resolved_id  The resolved context ID.
	 * @return string The resolved image URL, or an empty string if none found.
	 */
	private function resolve_image_url_from_meta( $field_source, $custom_key, $resolved_id ) {
		if ( 'tax' === $field_source ) {
			$image_meta = get_term_meta( $resolved_id, $custom_key, true );
		} elseif ( 'user' === $field_source || 'author' === $field_source ) {
			$image_meta = get_user_meta( $resolved_id, $custom_key, true );
		} else {
			// 'post' or unrecognised - treat as post meta.
			$image_meta = get_post_meta( $resolved_id, $custom_key, true );
		}

		if ( is_array( $image_meta ) && isset( $image_meta['url'] ) ) {
			// ACF image field with array return format.
			return esc_url_raw( $image_meta['url'] );
		} elseif ( is_numeric( $image_meta ) && $image_meta > 0 ) {
			// Stored as attachment ID.
			return (string) wp_get_attachment_image_url( (int) $image_meta, 'full' );
		} elseif ( is_string( $image_meta ) && filter_var( $image_meta, FILTER_VALIDATE_URL ) ) {
			// Stored as URL string.
			return esc_url_raw( $image_meta );
		}

		return '';
	}

	/**
	 * Apply the background image to the element.
	 *
	 * @since 1.5.2
	 * @access private
	 *
	 * @param \Elementor\Element_Base $element      The element being rendered.
	 * @param array                   $element_keys The background settings.
	 * @param int                     $resolved_id  The resolved context ID (post, term, or user).
	 * @return void
	 */
	private function apply_background_image( $element, $element_keys, $resolved_id ) {
		foreach ( $element_keys as $setting_key => $data ) {
			$image_url    = '';
			$field_source = $data['field_source'] ?? 'post';

			if ( 'custom_field' === $data['type'] ) {
				$image_url = $this->resolve_image_url_from_meta( $field_source, $data['custom_key'], $resolved_id );
			} elseif ( 'featured_image' === $data['type'] ) {
				$image_url = (string) get_the_post_thumbnail_url( $resolved_id, 'full' );
			}

			if ( ! $image_url ) {
				$element->add_render_attribute( '_wrapper', 'style', 'background-image: none;' );
				return;
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

			$element->add_render_attribute( '_wrapper', 'style', $css );
			$element->add_render_attribute( '_wrapper', 'class', 'e-lazyloaded' );

			return;
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
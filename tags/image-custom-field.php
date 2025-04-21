<?php
/**
 * Image Custom Field Tag.
 *
 * This class defines a custom dynamic tag for Elementor that retrieves and displays image custom field values.
 * It supports multiple sources including post meta, taxonomy meta, user meta, author meta, and theme options.
 * It also includes compatibility with Advanced Custom Fields (ACF) and provides a fallback option for images.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

namespace BPFWE_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use BPFWE\Inc\Classes\BPFWE_Helper;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Image_Custom_Field
 *
 * A custom dynamic tag to display image custom field values in Elementor widgets.
 *
 * @package BPFWE_Dynamic_Tag\Tags
 */
class Image_Custom_Field extends Data_Tag {

	/**
	 * Get the tag name.
	 *
	 * Returns a unique identifier for the dynamic tag.
	 *
	 * @return string The tag name.
	 */
	public function get_name() {
		return 'image-custom-field';
	}

	/**
	 * Get the title of the dynamic tag.
	 *
	 * Returns the title of the tag as shown in the Elementor interface.
	 *
	 * @return string The title of the dynamic tag.
	 */
	public function get_title() {
		return esc_html__( 'Image Custom Field', 'better-post-filter-widgets-for-elementor' );
	}

	/**
	 * Get the group of the dynamic tag.
	 *
	 * Determines the group in which the dynamic tag will appear in the Elementor interface.
	 *
	 * @return string The group name.
	 */
	public function get_group() {
		return 'bpfwe-dynamic-tags';
	}

	/**
	 * Get the categories of the dynamic tag.
	 *
	 * Returns an array of categories the tag belongs to, allowing it to be grouped with other similar tags.
	 *
	 * @return array The categories of the dynamic tag.
	 */
	public function get_categories() {
		return [
			TagsModule::IMAGE_CATEGORY,
			TagsModule::TEXT_CATEGORY,
		];
	}

	/**
	 * Register controls for the dynamic tag.
	 *
	 * Registers control settings for the dynamic tag, including field source, IDs, custom keys, and fallback options.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->add_control(
			'field_source',
			[
				'label'   => esc_html__( 'Field Source', 'better-post-filter-widgets-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post'   => esc_html__( 'Post', 'better-post-filter-widgets-for-elementor' ),
					'tax'    => esc_html__( 'Taxonomy', 'better-post-filter-widgets-for-elementor' ),
					'user'   => esc_html__( 'User', 'better-post-filter-widgets-for-elementor' ),
					'author' => esc_html__( 'Author', 'better-post-filter-widgets-for-elementor' ),
					'theme'  => esc_html__( 'Theme Options', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'option_key',
			[
				'label'     => esc_html__( 'Theme Option Key', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'field_source' => 'theme',
				],
			]
		);

		$this->add_control(
			'post_id',
			[
				'label'       => esc_html__( 'Post ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => get_the_ID() ? esc_html( get_the_ID() ) : esc_html__( 'Current Post ID', 'better-post-filter-widgets-for-elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'field_source' => 'post',
				],
			]
		);

		$this->add_control(
			'term_id',
			[
				'label'       => esc_html__( 'Term ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => ( is_tax() || is_category() || is_tag() ) ? esc_html( get_queried_object_id() ) : esc_html__( 'Current Term ID', 'better-post-filter-widgets-for-elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'field_source' => 'tax',
				],
			]
		);

		$this->add_control(
			'user_id',
			[
				'label'       => esc_html__( 'User ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => get_current_user_id() ? esc_html( get_current_user_id() ) : esc_html__( 'Current User ID', 'better-post-filter-widgets-for-elementor' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'field_source' => 'user',
				],
			]
		);

		$this->add_control(
			'custom_key',
			[
				'label'   => esc_html__( 'Meta Key', 'better-post-filter-widgets-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
			'fallback',
			[
				'label' => esc_html__( 'Fallback Image', 'better-post-filter-widgets-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);
	}

	/**
	 * Get the value of the dynamic tag.
	 *
	 * Retrieves the image custom field value based on the selected source and returns it as an array
	 * containing the URL and ID. Supports ACF fields and falls back to default meta functions if ACF is not used.
	 *
	 * @param array $options Optional options for retrieving the value.
	 * @return array The image data array with 'url' and 'id' keys.
	 */
	public function get_value( array $options = [] ) {
		$key      = sanitize_key( $this->get_settings( 'key' ) );
		$source   = $this->get_settings( 'field_source' );
		$post_id  = absint( $this->get_settings( 'post_id' ) );
		$term_id  = absint( $this->get_settings( 'term_id' ) );
		$user_id  = absint( $this->get_settings( 'user_id' ) );
		$fallback = $this->get_settings( 'fallback' );

		$key = empty( $key ) ? sanitize_key( $this->get_settings( 'custom_key' ) ) : $key;

		if ( empty( $key ) ) {
			return [
				'url' => esc_url( $fallback['url'] ?? '' ),
				'id'  => absint( $fallback['id'] ?? '' ),
			];
		}

		// Add global support for loops.
		global $bpfwe_term_id, $bpfwe_user_id;

		if ( 'tax' === $source && empty( $term_id ) && ! empty( $bpfwe_term_id ) ) {
			$term_id = absint( $bpfwe_term_id );
		} elseif ( 'tax' === $source && empty( $term_id ) && ( is_tax() || is_category() || is_tag() ) ) {
			$term_id = get_queried_object_id();
		}

		if ( 'user' === $source && empty( $user_id ) && ! empty( $bpfwe_user_id ) ) {
			$user_id = absint( $bpfwe_user_id );
		} elseif ( 'user' === $source && empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		// Initialize value.
		$value = '';

		// Check if ACF is active and handle ACF-specific logic.
		if ( class_exists( 'ACF' ) && BPFWE_Helper::is_acf_field( $key ) ) {
			if ( 'post' === $source ) {
				$value = $post_id ? get_field( $key, $post_id ) : get_field( $key );
			} elseif ( 'tax' === $source ) {
				$value = $term_id ? get_field( $key, 'term_' . $term_id ) : get_field( $key, 'term_' . get_queried_object_id() );
			} elseif ( 'user' === $source ) {
				$value = $user_id ? get_field( $key, 'user_' . $user_id ) : get_field( $key, 'user_' . get_current_user_id() );
			} elseif ( 'author' === $source ) {
				$author_id = get_the_author_meta( 'ID' );
				if ( is_author() ) {
					$author_id = get_queried_object_id();
				}
				$value = get_field( $key, 'user_' . $author_id );
			}
		}

		// Fallback to default meta functions if ACF is not used or value is empty.
		if ( empty( $value ) ) {
			if ( 'post' === $source && empty( $post_id ) ) {
				$value = get_post_meta( get_the_ID(), $key, true );
			} elseif ( 'post' === $source && $post_id ) {
				$value = get_post_meta( $post_id, $key, true );
			} elseif ( 'tax' === $source && $term_id ) {
				$value = get_term_meta( $term_id, $key, true );
			} elseif ( 'user' === $source && $user_id ) {
				$value = get_user_meta( $user_id, $key, true );
			} elseif ( 'author' === $source ) {
				$author_id = get_the_author_meta( 'ID' );
				if ( is_author() ) {
					$author_id = get_queried_object_id();
				}
				$value = get_the_author_meta( $key, $author_id );
			} elseif ( 'theme' === $source ) {
				$option_key   = sanitize_key( $this->get_settings( 'option_key' ) );
				$theme_option = get_option( $option_key );
				$value        = isset( $theme_option[ $key ] ) ? $theme_option[ $key ] : '';
			}
		}

		// Handle the image value (could be URL or attachment ID).
		if ( $value ) {
			if ( is_numeric( $value ) ) {
				// If the value is an attachment ID, get the image URL.
				$image_url = wp_get_attachment_url( $value );
				return [
					'url' => esc_url( ! empty( $image_url ) ? $image_url : '' ),
					'id'  => absint( $value ),
				];
			} else {
				// Assume the value is a URL.
				return [
					'url' => esc_url( $value ),
					'id'  => '',
				];
			}
		}

		// Return fallback if no value is found.
		return [
			'url' => esc_url( $fallback['url'] ?? '' ),
			'id'  => absint( $fallback['id'] ?? '' ),
		];
	}
}

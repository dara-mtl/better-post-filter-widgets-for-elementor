<?php
/**
 * Custom Field Dynamic Tag.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use BPF\Inc\Classes\BPF_Helper;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Custom_Field
 *
 * A custom dynamic tag to display custom field values in Elementor widgets.
 * It retrieves custom field values from various sources such as post meta, taxonomy meta, user meta, author meta, or theme options.
 * It supports Advanced Custom Fields (ACF) for additional flexibility.
 *
 * @package BPF_Dynamic_Tag\Tags
 */
class Custom_Field extends Tag {

	/**
	 * Get the tag name.
	 *
	 * This method returns a unique identifier for the dynamic tag.
	 *
	 * @return string The tag name.
	 */
	public function get_name() {
		return 'post-custom-field-tag';
	}

	/**
	 * Get the title of the dynamic tag.
	 *
	 * This method returns the title of the tag as shown in the Elementor interface.
	 *
	 * @return string The title of the dynamic tag.
	 */
	public function get_title() {
		return esc_html__( 'Custom Field', 'bpf-widget' );
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
			TagsModule::NUMBER_CATEGORY,
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::COLOR_CATEGORY,
		];
	}


	/**
	 * Get the setting key for the panel template.
	 *
	 * This method returns the setting key used for identifying the template setting for the dynamic tag.
	 *
	 * @return string The setting key for the panel template.
	 */
	public function get_panel_template_setting_key() {
		return 'key';
	}

	/**
	 * Check if settings are required for the dynamic tag.
	 *
	 * This method returns a boolean value indicating if the dynamic tag requires settings input.
	 *
	 * @return bool True if settings are required, false otherwise.
	 */
	public function is_settings_required() {
		return true;
	}

	/**
	 * Register controls for the dynamic tag.
	 *
	 * This method registers the control settings for the dynamic tag, including field source, option keys,
	 * and custom keys, allowing users to select and configure the custom field sources.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->add_control(
			'field_source',
			[
				'label'   => esc_html__( 'Field Source', 'bpf-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post'   => esc_html__( 'Post', 'bpf-widget' ),
					'tax'    => esc_html__( 'Taxonomy', 'bpf-widget' ),
					'user'   => esc_html__( 'User', 'bpf-widget' ),
					'author' => esc_html__( 'Author', 'bpf-widget' ),
					'theme'  => esc_html__( 'Theme Option', 'bpf-widget' ),
				],
			]
		);

		$this->add_control(
			'option_key',
			[
				'label'     => esc_html__( 'Option Key', 'bpf-widget' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'field_source' => 'theme',
				),
			]
		);

		$this->add_control(
			'post_id',
			[
				'label'       => esc_html__( 'Post ID', 'bpf-widget' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => get_the_ID() ? esc_html( get_the_ID() ) : esc_html__( 'Current Post ID', 'bpf-widget' ),
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
				'label'       => esc_html__( 'Term ID', 'bpf-widget' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => get_queried_object_id() ? esc_html( get_queried_object_id() ) : esc_html__( 'Current Term ID', 'bpf-widget' ),
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
				'label'       => esc_html__( 'User ID', 'bpf-widget' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => get_current_user_id() ? esc_html( get_current_user_id() ) : esc_html__( 'Current User ID', 'bpf-widget' ),
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
				'label' => esc_html__( 'Meta Key', 'bpf-widget' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'autop',
			[
				'label'        => esc_html__( 'Add Paragraphs', 'bpf-widget' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'bpf-widget' ),
				'label_off'    => esc_html__( 'No', 'bpf-widget' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);
	}

	/**
	 * Render the dynamic tag output.
	 *
	 * This method retrieves the custom field value based on the selected source (post, taxonomy, user, author, or theme option).
	 * It then outputs the value, optionally adding paragraph tags if the "autop" setting is enabled.
	 *
	 * @return void
	 */
	public function render() {
		$key        = sanitize_key( $this->get_settings( 'key' ) );
		$need_autop = $this->get_settings( 'autop' );
		$source     = $this->get_settings( 'field_source' );
		$post_id    = absint( $this->get_settings( 'post_id' ) );
		$term_id    = absint( $this->get_settings( 'term_id' ) );
		$user_id    = absint( $this->get_settings( 'user_id' ) );

		$key = empty( $key ) ? $this->get_settings( 'custom_key' ) : $key;

		if ( empty( $key ) ) {
			return;
		}

		// Initialize $value.
		$value = '';

		// Check if ACF is active.
		if ( BPF_Helper::is_acf_field( $key ) ) {
			// ACF specific logic.
			if ( 'post' === $source ) {
				$value = $post_id ? get_field( $key, $post_id ) : get_field( $key );
			} elseif ( 'tax' === $source ) {
				$value = $term_id ? get_field( $key, 'term_' . $term_id ) : get_field( $key, 'term_' . get_queried_object()->term_id );
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

		// Fallback to default methods if ACF is not used.
		if ( empty( $value ) ) {
			if ( 'post' === $source && ! $post_id ) {
				$value = get_post_meta( get_the_ID(), $key, true );
			}

			if ( 'post' === $source && $post_id ) {
				$value = get_post_meta( $post_id, $key, true );
			}

			if ( 'tax' === $source && ! $term_id ) {
				$value = get_term_meta( get_queried_object()->term_id, $key, true );
			}

			if ( 'tax' === $source && $term_id ) {
				$value = get_term_meta( $term_id, $key, true );
			}

			if ( 'user' === $source && ! $user_id ) {
				$value = get_user_meta( get_current_user_id(), $key, true );
			}

			if ( 'user' === $source && $user_id ) {
				$value = get_user_meta( $user_id, $key, true );
			}

			if ( 'author' === $source ) {
				$author_id = get_the_author_meta( 'ID' );
				if ( is_author() ) {
					$author_id = get_queried_object_id();
				}
				$value = get_the_author_meta( $key, $author_id );
			}

			if ( 'theme' === $source ) {
				$option_key   = $this->get_settings( 'option_key' );
				$theme_option = get_option( $option_key );

				if ( isset( $theme_option[ $key ] ) ) {
					$value = $theme_option[ $key ];
				} else {
					return;
				}
			}
		}

		$value = wp_kses_post( $value );

		echo ( 'yes' === $need_autop ) ? wpautop( $value ) : $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $value is sanitized with wp_kses_post() before output.
	}
}

<?php
/**
 * Repeater Dynamic Tag.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

namespace BPFWE_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use BPFWE\Inc\Classes\BPFWE_Helper;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Repeater.
 *
 * Dynamic tag for displaying repeater custom fields.
 *
 * @since 1.0.0
 */
class Repeater extends Tag {

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
		return 'repeater-tag';
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
		return esc_html__( 'Repeater', 'better-post-filter-widgets-for-elementor' );
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
		return 'bpfwe-dynamic-tags';
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
		return [
			TagsModule::NUMBER_CATEGORY,
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::COLOR_CATEGORY,
		];
	}

	/**
	 * Determine if settings are required.
	 *
	 * Indicates whether the tag requires additional settings.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool True if settings are required, false otherwise.
	 */
	public function is_settings_required() {
		return true;
	}

	/**
	 * Register controls.
	 *
	 * Define the controls for the dynamic tag.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->add_control(
			'field_source',
			[
				'label'   => esc_html__( 'Field Source', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
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
				'condition' => array(
					'field_source' => 'theme',
				),
			]
		);

		$this->add_control(
			'post_id',
			[
				'label'       => esc_html__( 'Post ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Current Post ID', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => array(
					'field_source' => 'post',
				),
			]
		);

		$this->add_control(
			'term_id',
			[
				'label'       => esc_html__( 'Term ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Current Term ID', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => array(
					'field_source' => 'tax',
				),
			]
		);

		$this->add_control(
			'user_id',
			[
				'label'       => esc_html__( 'User ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Current User ID', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => array(
					'field_source' => 'user',
				),
			]
		);

		$this->add_control(
			'custom_key',
			[
				'label' => esc_html__( 'Parent Key', 'better-post-filter-widgets-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'child_key_1',
			[
				'label'     => esc_html__( 'Child Key 1', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'separator' => 'before',
				'condition' => array(
					'custom_key!' => '',
				),
			]
		);

		$this->add_control(
			'child_key_2',
			[
				'label'     => esc_html__( 'Child Key 2', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'child_key_1!' => '',
					'custom_key!'  => '',
				),
			]
		);

		$this->add_control(
			'child_key_3',
			[
				'label'     => esc_html__( 'Child Key 3', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'child_key_2!' => '',
					'custom_key!'  => '',
				),
			]
		);

		$this->add_control(
			'child_key_4',
			[
				'label'     => esc_html__( 'Child Key 4', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'child_key_3!' => '',
					'custom_key!'  => '',
				),
			]
		);

		$this->add_control(
			'child_key_5',
			[
				'label'     => esc_html__( 'Child Key 5', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'child_key_4!' => '',
					'custom_key!'  => '',
				),
			]
		);

		$this->add_control(
			'child_html_tag',
			[
				'label'     => esc_html__( 'HTML Tag', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none'   => esc_html__( 'No Tag', 'better-post-filter-widgets-for-elementor' ),
					'div'    => esc_html__( 'div', 'better-post-filter-widgets-for-elementor' ),
					'span'   => esc_html__( 'span', 'better-post-filter-widgets-for-elementor' ),
					'p'      => esc_html__( 'p', 'better-post-filter-widgets-for-elementor' ),
					'h1'     => esc_html__( 'h1', 'better-post-filter-widgets-for-elementor' ),
					'h2'     => esc_html__( 'h2', 'better-post-filter-widgets-for-elementor' ),
					'h3'     => esc_html__( 'h3', 'better-post-filter-widgets-for-elementor' ),
					'h4'     => esc_html__( 'h4', 'better-post-filter-widgets-for-elementor' ),
					'h5'     => esc_html__( 'h5', 'better-post-filter-widgets-for-elementor' ),
					'h6'     => esc_html__( 'h6', 'better-post-filter-widgets-for-elementor' ),
					'ul'     => esc_html__( 'ul', 'better-post-filter-widgets-for-elementor' ),
					'ol'     => esc_html__( 'ol', 'better-post-filter-widgets-for-elementor' ),
					'table'  => esc_html__( 'table', 'better-post-filter-widgets-for-elementor' ),
					'toggle' => esc_html__( 'toggle', 'better-post-filter-widgets-for-elementor' ),
					'tabs'   => esc_html__( 'tabs', 'better-post-filter-widgets-for-elementor' ),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'toggle_title_tag',
			[
				'label'     => esc_html__( 'Toggle Title Tag', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => [
					'h1'   => esc_html__( 'h1', 'better-post-filter-widgets-for-elementor' ),
					'h2'   => esc_html__( 'h2', 'better-post-filter-widgets-for-elementor' ),
					'h3'   => esc_html__( 'h3', 'better-post-filter-widgets-for-elementor' ),
					'h4'   => esc_html__( 'h4', 'better-post-filter-widgets-for-elementor' ),
					'h5'   => esc_html__( 'h5', 'better-post-filter-widgets-for-elementor' ),
					'h6'   => esc_html__( 'h6', 'better-post-filter-widgets-for-elementor' ),
					'span' => esc_html__( 'span', 'better-post-filter-widgets-for-elementor' ),
					'div'  => esc_html__( 'div', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'child_html_tag' => 'toggle',
				],
			]
		);

		$this->add_control(
			'tab_title_tag',
			[
				'label'     => esc_html__( 'Tab Title Tag', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'span',
				'options'   => [
					'h1'   => esc_html__( 'h1', 'better-post-filter-widgets-for-elementor' ),
					'h2'   => esc_html__( 'h2', 'better-post-filter-widgets-for-elementor' ),
					'h3'   => esc_html__( 'h3', 'better-post-filter-widgets-for-elementor' ),
					'h4'   => esc_html__( 'h4', 'better-post-filter-widgets-for-elementor' ),
					'h5'   => esc_html__( 'h5', 'better-post-filter-widgets-for-elementor' ),
					'h6'   => esc_html__( 'h6', 'better-post-filter-widgets-for-elementor' ),
					'span' => esc_html__( 'span', 'better-post-filter-widgets-for-elementor' ),
					'div'  => esc_html__( 'div', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'child_html_tag' => 'tabs',
				],
			]
		);

		$this->add_control(
			'list_output_mode',
			[
				'label'     => esc_html__( 'Output Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'grouped', // Legacy default to preserve existing behavior.
				'options'   => [
					'grouped' => esc_html__( 'Grouped', 'better-post-filter-widgets-for-elementor' ),
					'flat'    => esc_html__( 'Flat', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'child_html_tag' => [ 'ul','ol' ],
				],
			]
		);

		$this->add_control(
			'tabs_output_mode',
			[
				'label'     => esc_html__( 'Output Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'per_field', // Legacy default to preserve existing behavior.
				'options'   => [
					'per_field' => esc_html__( 'Grouped', 'better-post-filter-widgets-for-elementor' ),
					'per_entry' => esc_html__( 'Flat', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'child_html_tag' => 'tabs',
				],
			]
		);

		$this->add_control(
			'toggle_output_mode',
			[
				'label'     => esc_html__( 'Output Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'per_field', // Legacy default to preserve existing behavior.
				'options'   => [
					'per_field' => esc_html__( 'Grouped', 'better-post-filter-widgets-for-elementor' ),
					'per_entry' => esc_html__( 'Flat', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'child_html_tag' => 'toggle',
				],
			]
		);
	}

	/**
	 * Render dynamic tag output.
	 */
	public function render() {
		$settings           = $this->get_settings_for_display();
		$key                = sanitize_key( $settings['custom_key'] );
		$source             = $settings['field_source'];
		$post_id            = absint( $settings['post_id'] );
		$term_id            = absint( $settings['term_id'] );
		$user_id            = absint( $settings['user_id'] );
		$allowed_child_tags = [ 'none', 'div', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'table', 'toggle', 'tabs' ];
		$html_tag           = in_array( $settings['child_html_tag'], $allowed_child_tags, true ) ? esc_attr( $settings['child_html_tag'] ) : '';

		if ( empty( $key ) ) {
			return;
		}

		$is_acf_active = BPFWE_Helper::is_acf_field( $key );

		switch ( $source ) {
			case 'post':
				if ( $is_acf_active ) {
					$entries = $post_id ? get_field( $key, $post_id ) : get_field( $key, get_the_ID() );
				} else {
					$entries = $post_id ? get_post_meta( $post_id, $key, true ) : get_post_meta( get_the_ID(), $key, true );
				}
				break;
			case 'tax':
				if ( $is_acf_active ) {
					$entries = $term_id ? get_field( $key, 'term_' . $term_id ) : get_field( $key, 'term_' . get_queried_object()->term_id );
				} else {
					$entries = $term_id ? get_term_meta( $term_id, $key, true ) : get_term_meta( get_queried_object()->term_id, $key, true );
				}
				break;
			case 'user':
				if ( $is_acf_active ) {
					$entries = $user_id ? get_field( $key, 'user_' . $user_id ) : get_field( $key, 'user_' . get_current_user_id() );
				} else {
					$entries = $user_id ? get_user_meta( $user_id, $key, true ) : get_user_meta( get_current_user_id(), $key, true );
				}
				break;
			case 'author':
				$author_id = is_author() ? get_queried_object_id() : get_the_author_meta( 'ID' );
				if ( $is_acf_active ) {
					$entries = get_field( $key, 'user_' . $author_id );
				} else {
					$entries = get_the_author_meta( $key, $author_id );
				}
				break;
			case 'theme':
				$option_key   = $settings['option_key'];
				$theme_option = get_option( $option_key );
				if ( isset( $theme_option[ $key ] ) ) {
					$entries = $theme_option[ $key ];
				} else {
					$entries = null;
				}
				break;
			default:
				return;
		}

		if ( empty( $entries ) || ! is_array( $entries ) ) {
			return;
		}

		$max_child_keys = 5;
		$class_nb       = 0;

		if ( 'toggle' === $html_tag ) {
			if ( 'per_field' === $settings['toggle_output_mode'] ) {
				$toggle_title_tag = $settings['toggle_title_tag'];
				if ( empty( $toggle_title_tag ) ) {
					$toggle_title_tag = 'h3';
				}

				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					$child_key     = "child_key_{$counter}";
					$setting_value = $settings[ $child_key ];

					if ( ! empty( $setting_value ) ) {
						$value_parts     = explode( '|', $setting_value );
						$child_value_key = $value_parts[0];
						$custom_title    = isset( $value_parts[1] ) && ! empty( $value_parts[1] ) ? $value_parts[1] : 'Toggle ' . $counter;
						$before          = isset( $value_parts[2] ) ? $value_parts[2] : '';
						$after           = isset( $value_parts[3] ) ? $value_parts[3] : '';
						$toggle_content  = '';
						$has_content     = false;

						foreach ( $entries as $entry ) {
							$child_value = isset( $entry[ $child_value_key ] ) ? $entry[ $child_value_key ] : '';
							if ( ! empty( $child_value ) ) {
								$has_content     = true;
								$value           = wp_kses_post( $before . $child_value . $after );
								$toggle_content .= wpautop( str_replace( '#', $class_nb, $value ) );
							}
						}

						if ( $has_content ) {
							$toggle_id = 'toggle-' . uniqid();
							echo '<div class="toggle-wrapper"><input type="checkbox" class="repeater-toggle" id="' . esc_attr( $toggle_id ) . '" />';
							echo '<label for="' . esc_attr( $toggle_id ) . '">';
							echo '<' . esc_attr( $toggle_title_tag ) . ' class="toggle-title">' . esc_html( $custom_title ) . '</' . esc_attr( $toggle_title_tag ) . '>';
							echo '</label>';
							echo '<div class="toggle-content">' . wp_kses_post( $toggle_content ) . '</div></div>';
						}
					}
				}
			} else {
				$toggle_title_tag = $settings['toggle_title_tag'];
				if ( empty( $toggle_title_tag ) ) {
					$toggle_title_tag = 'h3';
				}

				$entries_count = count( $entries );
				for ( $i = 0; $i < $entries_count; $i++ ) {
					$entry = $entries[ $i ];

					// Get title field (assuming child_key_1).
					$setting_title = $settings['child_key_1'];
					$title_parts   = explode( '|', $setting_title );
					$title_key     = $title_parts[0];
					$before_title  = isset( $title_parts[2] ) ? $title_parts[2] : '';
					$after_title   = isset( $title_parts[3] ) ? $title_parts[3] : '';
					$title_value   = isset( $entry[ $title_key ] ) ? $entry[ $title_key ] : '';

					// Get content field (assuming child_key_2).
					$setting_content = $settings['child_key_2'];
					$content_parts   = explode( '|', $setting_content );
					$content_key     = $content_parts[0];
					$before_content  = isset( $content_parts[2] ) ? $content_parts[2] : '';
					$after_content   = isset( $content_parts[3] ) ? $content_parts[3] : '';
					$content_value   = isset( $entry[ $content_key ] ) ? $entry[ $content_key ] : '';

					if ( ! empty( $title_value ) && ! empty( $content_value ) ) {
						$toggle_id = 'toggle-' . uniqid();

						echo '<div class="toggle-wrapper">';
						echo '<input type="checkbox" class="repeater-toggle" id="' . esc_attr( $toggle_id ) . '" />';
						echo '<label for="' . esc_attr( $toggle_id ) . '">';
						echo '<' . esc_attr( $toggle_title_tag ) . ' class="toggle-title">' . esc_html( $before_title . $title_value . $after_title ) . '</' . esc_attr( $toggle_title_tag ) . '>';
						echo '</label>';
						echo '<div class="toggle-content">' . wp_kses_post( wpautop( $before_content . $content_value . $after_content ) ) . '</div>';
						echo '</div>';
					}
				}
			}
		} elseif ( 'tabs' === $html_tag ) {
			if ( 'per_field' === $settings['tabs_output_mode'] ) {
				echo '<div class="bpfwe-tabs-wrapper">';

				$group_name    = 'bpfwe-tab-group-' . uniqid();
				$first_tab     = true;
				$tab_title_tag = $settings['tab_title_tag'];
				if ( empty( $tab_title_tag ) ) {
					$tab_title_tag = 'span';
				}

				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					$child_key     = "child_key_{$counter}";
					$setting_value = $settings[ $child_key ];

					if ( ! empty( $setting_value ) ) {
						$value_parts     = explode( '|', $setting_value );
						$child_value_key = $value_parts[0];
						$custom_title    = isset( $value_parts[1] ) && ! empty( $value_parts[1] ) ? $value_parts[1] : 'Tab ' . $counter;
						$before          = isset( $value_parts[2] ) ? $value_parts[2] : '';
						$after           = isset( $value_parts[3] ) ? $value_parts[3] : '';
						$tab_content     = '';
						$has_content     = false;

						foreach ( $entries as $entry ) {
							$child_value = isset( $entry[ $child_value_key ] ) ? $entry[ $child_value_key ] : '';
							if ( ! empty( $child_value ) ) {
								$has_content  = true;
								$value        = wp_kses_post( $before . $child_value . $after );
								$tab_content .= wpautop( str_replace( '#', $class_nb, $value ) );
							}
						}

						if ( $has_content ) {
							$tab_id = 'bpfwe-tab-' . uniqid();
							echo '<div class="bpfwe-tab-item">';
							echo '<input type="radio" class="bpfwe-tab-toggle" name="' . esc_attr( $group_name ) . '" id="' . esc_attr( $tab_id ) . '"' . ( $first_tab ? ' checked' : '' ) . '>';
							echo '<label for="' . esc_attr( $tab_id ) . '"><' . esc_attr( $tab_title_tag ) . ' class="bpfwe-tab-label">' . esc_html( $custom_title ) . '</' . esc_attr( $tab_title_tag ) . '></label>';
							echo '<div class="bpfwe-tab-content">' . wp_kses_post( $tab_content ) . '</div>';
							echo '</div>';
							$first_tab = false;
						}
					}
				}

				echo '</div>';
			} else {
				echo '<div class="bpfwe-tabs-wrapper">';

				$group_name    = 'bpfwe-tab-group-' . uniqid();
				$first_tab     = true;
				$tab_title_tag = $settings['tab_title_tag'];
				if ( empty( $tab_title_tag ) ) {
					$tab_title_tag = 'span';
				}

				$entries_count = count( $entries );
				for ( $i = 0; $i < $entries_count; $i++ ) {
					$entry = $entries[ $i ];

					// Get title.
					$setting_title = $settings['child_key_1'];
					$title_parts   = explode( '|', $setting_title );
					$title_key     = $title_parts[0];
					$before_title  = isset( $title_parts[2] ) ? $title_parts[2] : '';
					$after_title   = isset( $title_parts[3] ) ? $title_parts[3] : '';
					$title_value   = isset( $entry[ $title_key ] ) ? $entry[ $title_key ] : '';

					// Get content.
					$setting_content = $settings['child_key_2'];
					$content_parts   = explode( '|', $setting_content );
					$content_key     = $content_parts[0];
					$before_content  = isset( $content_parts[2] ) ? $content_parts[2] : '';
					$after_content   = isset( $content_parts[3] ) ? $content_parts[3] : '';
					$content_value   = isset( $entry[ $content_key ] ) ? $entry[ $content_key ] : '';

					if ( ! empty( $title_value ) && ! empty( $content_value ) ) {
						$tab_id = 'bpfwe-tab-' . uniqid();

						echo '<div class="bpfwe-tab-item">';
						echo '<input type="radio" class="bpfwe-tab-toggle" name="' . esc_attr( $group_name ) . '" id="' . esc_attr( $tab_id ) . '"' . ( $first_tab ? ' checked' : '' ) . '>';
						echo '<label for="' . esc_attr( $tab_id ) . '"><' . esc_attr( $tab_title_tag ) . ' class="bpfwe-tab-label">' . esc_html( $before_title . $title_value . $after_title ) . '</' . esc_attr( $tab_title_tag ) . '></label>';
						echo '<div class="bpfwe-tab-content">' . wp_kses_post( wpautop( $before_content . $content_value . $after_content ) ) . '</div>';
						echo '</div>';

						$first_tab = false;
					}
				}

				echo '</div>';
			}
		} elseif ( 'table' === $html_tag ) {
			echo '<table class="repeater-table">';

			// Determine which columns have content and headers.
			$column_has_content = array_fill( 1, $max_child_keys, false );
			$has_headers        = false;
			$column_settings    = [];

			for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
				$child_key     = "child_key_{$counter}";
				$setting_value = $settings[ $child_key ];

				if ( ! empty( $setting_value ) ) {
					$value_parts                 = explode( '|', $setting_value );
					$child_value_key             = $value_parts[0];
					$column_settings[ $counter ] = $value_parts;

					// Check if this column has a header.
					if ( isset( $value_parts[1] ) && ! empty( $value_parts[1] ) ) {
						$has_headers = true;
					}

					// Check if this column has any content.
					foreach ( $entries as $entry ) {
						$child_value = isset( $entry[ $child_value_key ] ) ? $entry[ $child_value_key ] : '';
						if ( ! empty( $child_value ) ) {
							$column_has_content[ $counter ] = true;
							break;
						}
					}
				}
			}

			// Render headers if any are specified.
			if ( $has_headers ) {
				echo '<thead><tr>';
				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					if ( ! empty( $column_settings[ $counter ] ) && $column_has_content[ $counter ] ) {
						$value_parts = $column_settings[ $counter ];
						$header      = isset( $value_parts[1] ) && ! empty( $value_parts[1] ) ? $value_parts[1] : '';
						if ( ! empty( $header ) ) {
							echo '<th class="table-header cell-' . esc_attr( ++$class_nb ) . '">' . esc_html( $header ) . '</th>';
						} else {
							echo '<th class="table-header cell-' . esc_attr( ++$class_nb ) . '"></th>';
						}
					}
				}
				echo '</tr></thead>';
			}

			// Render table body.
			echo '<tbody>';
			foreach ( $entries as $entry ) {
				$row_content = '';
				$has_content = false;

				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					if ( ! empty( $column_settings[ $counter ] ) && $column_has_content[ $counter ] ) {
						$value_parts     = $column_settings[ $counter ];
						$child_value_key = $value_parts[0];
						$before          = isset( $value_parts[2] ) ? $value_parts[2] : '';
						$after           = isset( $value_parts[3] ) ? $value_parts[3] : '';
						$child_value     = isset( $entry[ $child_value_key ] ) ? $entry[ $child_value_key ] : '';
						if ( ! empty( $child_value ) ) {
							$has_content = true;
						}
						$value        = wp_kses_post( $before . $child_value . $after );
						$row_content .= '<td class="table-cell cell-' . esc_attr( ++$class_nb ) . '">' . str_replace( '#', $class_nb, $value ) . '</td>';
					}
				}

				if ( $has_content ) {
					echo '<tr>' . wp_kses_post( $row_content ) . '</tr>';
				}
			}
			echo '</tbody></table>';
		} elseif ( 'ul' === $html_tag || 'ol' === $html_tag ) {
			if ( 'grouped' === $settings['list_output_mode'] ) {
				echo "<{$html_tag} class='repeater-list'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 326 and in the final output.

				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					$child_key     = "child_key_{$counter}";
					$setting_value = $settings[ $child_key ];

					if ( ! empty( $setting_value ) ) {
						$value_parts     = explode( '|', $setting_value );
						$child_value_key = $value_parts[0];
						$before          = isset( $value_parts[1] ) ? $value_parts[1] : '';
						$after           = isset( $value_parts[2] ) ? $value_parts[2] : '';
						$list_content    = '';
						$has_content     = false;

						foreach ( $entries as $entry ) {
							$child_value = isset( $entry[ $child_value_key ] ) ? $entry[ $child_value_key ] : '';
							if ( ! empty( $child_value ) ) {
								$has_content   = true;
								$value         = wp_kses_post( $before . $child_value . $after );
								$list_content .= str_replace( '#', $class_nb, $value ) . ' ';
							}
						}

						if ( $has_content ) {
							echo '<li>' . wp_kses_post( trim( $list_content ) ) . '</li>';
						}
					}
				}

				echo "</{$html_tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 326 and in the final output.
			} else {
				echo "<{$html_tag} class='repeater-list'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 326 and in the final output.

				foreach ( $entries as $entry ) {
					for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
						$child_key     = "child_key_{$counter}";
						$setting_value = $settings[ $child_key ];

						if ( ! empty( $setting_value ) ) {
							$value_parts     = explode( '|', $setting_value );
							$child_value_key = $value_parts[0];
							$before          = isset( $value_parts[1] ) ? $value_parts[1] : '';
							$after           = isset( $value_parts[2] ) ? $value_parts[2] : '';

							$child_value = isset( $entry[ $child_value_key ] ) ? $entry[ $child_value_key ] : '';

							if ( ! empty( $child_value ) ) {
								$value = $before . $child_value . $after;
								echo '<li>' . wp_kses_post( str_replace( '#', $class_nb, $value ) ) . '</li>';
							}
						}
					}
				}

				echo "</{$html_tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 326 and in the final output.
			}
		} else {
			foreach ( $entries as $entry ) {
				$has_content = false;
				$output      = '';

				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					$child_key     = "child_key_{$counter}";
					$setting_value = $settings[ $child_key ];

					if ( ! empty( $setting_value ) ) {
						$value_parts     = explode( '|', $setting_value );
						$child_value_key = $value_parts[0];
						$before          = isset( $value_parts[1] ) ? $value_parts[1] : '';
						$after           = isset( $value_parts[2] ) ? $value_parts[2] : '';
						$child_value     = isset( $entry[ $child_value_key ] ) ? $entry[ $child_value_key ] : '';
						if ( ! empty( $child_value ) ) {
							$has_content = true;
							$value       = wp_kses_post( $before . $child_value . $after );
							$classes     = 'repeater-field field-' . esc_attr( ++$class_nb );
							if ( 'none' !== $html_tag ) {
								$output .= "<{$html_tag} class='{$classes}'>" . str_replace( '#', $class_nb, $value ) . "</{$html_tag}>";
							} else {
								$output .= str_replace( '#', $class_nb, $value ) . ' ';
							}
						}
					}
				}

				if ( $has_content ) {
					echo wp_kses_post( $output );
				}
			}
		}
	}
}

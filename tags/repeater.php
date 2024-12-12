<?php
/**
 * Repeater Dynamic Tag.
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
		return esc_html__( 'Repeater', 'bpf-widget' );
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
		return 'bpf-dynamic-tags';
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
				'label'   => esc_html__( 'Field Source', 'bpf-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post'   => esc_html__( 'Post', 'bpf-widget' ),
					'tax'    => esc_html__( 'Taxonomy', 'bpf-widget' ),
					'user'   => esc_html__( 'User', 'bpf-widget' ),
					'author' => esc_html__( 'Author', 'bpf-widget' ),
					'theme'  => esc_html__( 'Theme Options', 'bpf-widget' ),
				],
			]
		);

		$this->add_control(
			'option_key',
			[
				'label'     => esc_html__( 'Theme Option Key', 'bpf-widget' ),
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
				'placeholder' => esc_html__( 'Current Post ID', 'bpf-widget' ),
				'condition'   => array(
					'field_source' => 'post',
				),
			]
		);

		$this->add_control(
			'term_id',
			[
				'label'       => esc_html__( 'Term ID', 'bpf-widget' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Current Term ID', 'bpf-widget' ),
				'condition'   => array(
					'field_source' => 'tax',
				),
			]
		);

		$this->add_control(
			'user_id',
			[
				'label'       => esc_html__( 'User ID', 'bpf-widget' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Current User ID', 'bpf-widget' ),
				'condition'   => array(
					'field_source' => 'user',
				),
			]
		);

		$this->add_control(
			'custom_key',
			[
				'label' => esc_html__( 'Parent Key', 'bpf-widget' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'child_key_1',
			[
				'label'     => esc_html__( 'Child Key 1', 'bpf-widget' ),
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
				'label'     => esc_html__( 'Child Key 2', 'bpf-widget' ),
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
				'label'     => esc_html__( 'Child Key 3', 'bpf-widget' ),
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
				'label'     => esc_html__( 'Child Key 4', 'bpf-widget' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'child_key_3!' => '',
					'custom_key!'  => '',
				),
			]
		);

		$this->add_control(
			'child_html_tag',
			[
				'label'     => esc_html__( 'HTML Tag', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none'   => esc_html__( 'No Tag', 'bpf-widget' ),
					'div'    => esc_html__( 'div', 'bpf-widget' ),
					'span'   => esc_html__( 'span', 'bpf-widget' ),
					'p'      => esc_html__( 'p', 'bpf-widget' ),
					'h1'     => esc_html__( 'h1', 'bpf-widget' ),
					'h2'     => esc_html__( 'h2', 'bpf-widget' ),
					'h3'     => esc_html__( 'h3', 'bpf-widget' ),
					'h4'     => esc_html__( 'h4', 'bpf-widget' ),
					'h5'     => esc_html__( 'h5', 'bpf-widget' ),
					'h6'     => esc_html__( 'h6', 'bpf-widget' ),
					'ul'     => esc_html__( 'ul', 'bpf-widget' ),
					'ol'     => esc_html__( 'ol', 'bpf-widget' ),
					'table'  => esc_html__( 'table', 'bpf-widget' ),
					'toggle' => esc_html__( 'toggle', 'bpf-widget' ),
				],
				'separator' => 'before',
			]
		);

		// Add "Toggle Title Tag" field.
		$this->add_control(
			'toggle_title_tag',
			[
				'label'     => esc_html__( 'Title Tag', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => [
					'h1'   => esc_html__( 'h1', 'bpf-widget' ),
					'h2'   => esc_html__( 'h2', 'bpf-widget' ),
					'h3'   => esc_html__( 'h3', 'bpf-widget' ),
					'h4'   => esc_html__( 'h4', 'bpf-widget' ),
					'h5'   => esc_html__( 'h5', 'bpf-widget' ),
					'h6'   => esc_html__( 'h6', 'bpf-widget' ),
					'span' => esc_html__( 'span', 'bpf-widget' ),
					'div'  => esc_html__( 'div', 'bpf-widget' ),
				],
				'condition' => [
					'child_html_tag' => 'toggle',
				],
			]
		);
	}

	/**
	 * Render dynamic tag output.
	 *
	 * Generates the HTML output for the repeater.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {
		$key      = sanitize_key( $this->get_settings( 'custom_key' ) );
		$source   = $this->get_settings( 'field_source' );
		$post_id  = absint( $this->get_settings( 'post_id' ) );
		$term_id  = absint( $this->get_settings( 'term_id' ) );
		$user_id  = absint( $this->get_settings( 'user_id' ) );
		$html_tag = esc_attr( $this->get_settings( 'child_html_tag' ) );

		if ( empty( $key ) ) {
			return;
		}

		// Check if ACF is active.
		$is_acf_active = BPF_Helper::is_acf_field( $key );

		// Get the meta data based on the field source.
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
				$option_key   = $this->get_settings( 'option_key' );
				$theme_option = get_option( $option_key );
				$entries      = $theme_option[ $key ] ?? null;
				break;
			default:
				return;
		}

		if ( empty( $entries ) || ! is_array( $entries ) ) {
			return;
		}

		$max_child_keys = 4;
		$class_nb       = 0;

		if ( 'toggle' === $html_tag ) {
			// Render each entry with dynamic tags and toggling.
			foreach ( $entries as $entry ) {
				$toggle_id      = 'toggle-' . uniqid();
				$toggle_title   = '';
				$toggle_content = '';

				// Get the tag settings.
				$toggle_title_tag = ( $this->get_settings( 'toggle_title_tag' ) ) ? $this->get_settings( 'toggle_title_tag' ) : 'div';

				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					$child_key     = "child_key_{$counter}";
					$setting_value = $this->get_settings( $child_key );

					if ( ! empty( $setting_value ) ) {
						$value_parts     = explode( '|', $setting_value );
						$child_value_key = $value_parts[0];
						$before          = isset( $value_parts[1] ) ? $value_parts[1] : '';
						$after           = isset( $value_parts[2] ) ? $value_parts[2] : '';

						$child_value = $entry[ $child_value_key ] ?? '';

						if ( 1 === $counter ) {
							// Use the first child as the toggle title.
							$toggle_title = esc_html( $before . str_replace( '#', $class_nb, esc_html( $child_value ) ) . $after );
						} else {
							// Additional children as toggle content.
							$toggle_content .= wp_kses_post( $before . str_replace( '#', $class_nb, wpautop( wp_kses_post( $child_value ) ) ) . $after );
						}
					}
				}

				// Print each toggle item with dynamic content.
				echo '<div class="toggle-wrapper"><input type="checkbox" class="repeater-toggle" id="' . esc_attr( $toggle_id ) . '" />';
				echo '<label for="' . esc_attr( $toggle_id ) . '">';
				echo '<' . esc_attr( $toggle_title_tag ) . ' class="toggle-title">' . $toggle_title . '</' . esc_attr( $toggle_title_tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 307.
				echo '</label>';
				echo '<div class="toggle-content">' . $toggle_content . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 310.
			}
		} elseif ( 'table' === $html_tag ) {
			echo '<table class="repeater-table"><tr>';
			foreach ( $entries as $entry ) {
				echo '<tr>';
				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					$child_key     = "child_key_{$counter}";
					$setting_value = $this->get_settings( $child_key );

					if ( ! empty( $setting_value ) ) {
						$value_parts     = explode( '|', $setting_value );
						$child_value_key = $value_parts[0];
						$before          = isset( $value_parts[1] ) ? $value_parts[1] : '';
						$after           = isset( $value_parts[2] ) ? $value_parts[2] : '';

						if ( isset( $entry[ $child_value_key ] ) ) {
							$value = $before . $entry[ $child_value_key ] . $after;
							echo '<td class="table-cell cell-' . esc_attr( ++$class_nb ) . '">' . esc_html( str_replace( '#', $class_nb, $value ) ) . '</td>';
						}
					}
				}
				echo '</tr>';
			}
			echo '</table>';
		} elseif ( 'ul' === $html_tag || 'ol' === $html_tag ) {
			++$class_nb;
			echo "<{$html_tag} class='repeater-list'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 222.
			foreach ( $entries as $entry ) {
				echo '<li>';
				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					$child_key     = "child_key_{$counter}";
					$setting_value = $this->get_settings( $child_key );

					if ( ! empty( $setting_value ) ) {
						$value_parts     = explode( '|', $setting_value );
						$child_value_key = $value_parts[0];
						$before          = isset( $value_parts[1] ) ? $value_parts[1] : '';
						$after           = isset( $value_parts[2] ) ? $value_parts[2] : '';

						// Get the value with before and after texts.
						$value = $before . $entry[ $child_value_key ] . $after;
						echo esc_html( str_replace( '#', $class_nb, $value ) );
					}
				}
				echo '</li>';
			}
			echo "</{$html_tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 222.
		} else {
			foreach ( $entries as $entry ) {
				for ( $counter = 1; $counter <= $max_child_keys; $counter++ ) {
					$child_key     = "child_key_{$counter}";
					$setting_value = $this->get_settings( $child_key );

					if ( ! empty( $setting_value ) ) {
						$value_parts     = explode( '|', $setting_value );
						$child_value_key = $value_parts[0];
						$before          = isset( $value_parts[1] ) ? $value_parts[1] : '';
						$after           = isset( $value_parts[2] ) ? $value_parts[2] : '';
						if ( isset( $entry[ $child_value_key ] ) ) {
							$value   = $before . $entry[ $child_value_key ] . $after;
							$classes = 'repeater-field field-' . esc_attr( ++$class_nb );
							if ( 'none' !== $html_tag ) {
								echo "<{$html_tag} class='{$classes}'>" . esc_html( str_replace( '#', $class_nb, $value ) ) . "</{$html_tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped on line 222.
							} else {
								echo esc_html( str_replace( '#', $class_nb, $value ) ) . ' ';
							}
						}
					}
				}
			}
		}
	}
}

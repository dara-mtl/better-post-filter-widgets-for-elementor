<?php
/**
 * Taxonomy Meta Dynamic Tag.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

namespace BPFWE_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Taxonomy_Meta.
 *
 * Dynamic tag for displaying taxonomy/term meta.
 *
 * @since 1.0.0
 */
class Taxonomy_Meta extends Tag {

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
		return 'taxonomy-meta';
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
		return esc_html__( 'Taxonomy Meta', 'better-post-filter-widgets-for-elementor' );
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
		return 'taxonomy';
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
		];
	}

	/**
	 * Get the key for the panel template setting.
	 *
	 * This method returns the setting key used by Elementor's panel to identify the
	 * dynamic tag control. In this case, it returns 'key', which corresponds to the
	 * field control that selects the type of taxonomy information to display.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The setting key for the dynamic tag control.
	 */
	public function get_panel_template_setting_key() {
		return 'key';
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
			'key',
			[
				'label'   => esc_html__( 'Field', 'better-post-filter-widgets-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'        => esc_html__( 'Name', 'better-post-filter-widgets-for-elementor' ),
					'term_id'     => esc_html__( 'Term ID', 'better-post-filter-widgets-for-elementor' ),
					'description' => esc_html__( 'Description', 'better-post-filter-widgets-for-elementor' ),
					'slug'        => esc_html__( 'Slug', 'better-post-filter-widgets-for-elementor' ),
					'count'       => esc_html__( 'Post Count', 'better-post-filter-widgets-for-elementor' ),
					'term_link'   => esc_html__( 'Term URL', 'better-post-filter-widgets-for-elementor' ),
					'meta'        => esc_html__( 'Term Meta', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'meta_key',
			[
				'label'     => esc_html__( 'Meta Key', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'key' => 'meta',
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
			]
		);
	}

	/**
	 * Render dynamic tag output.
	 *
	 * Generates the HTML output for the taxonomy meta.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {
		$key      = $this->get_settings( 'key' );
		$meta_key = $this->get_settings( 'meta_key' );
		$term_id  = $this->get_settings( 'term_id' );

		if ( empty( $key ) && empty( $meta_key ) ) {
			return;
		}

		global $bpfwe_term_id;
		if ( empty( $term_id ) && ! empty( $bpfwe_term_id ) ) {
			$term_id = absint( $bpfwe_term_id );
		} elseif ( empty( $term_id ) && ( is_tax() || is_category() || is_tag() ) ) {
			$term_id = get_queried_object_id();
		}

		if ( ! $term_id ) {
			return;
		}

		$term = get_term( $term_id );
		if ( ! $term || is_wp_error( $term ) ) {
			return;
		}

		if ( 'term_link' === $key ) {
			$value = get_term_link( $term_id );
		} elseif ( 'meta' === $key && ! empty( $meta_key ) ) {
			$value = get_term_meta( $term_id, $meta_key, true );
		} elseif ( 'term_id' === $key ) {
			$value = $term_id;
		} else {
			$value = $term->$key;
		}

		echo wp_kses_post( $value );
	}
}

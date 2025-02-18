<?php
/**
 * Post Terms Dynamic Tag.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

namespace BPFWE_Dynamic_Tag\Tags;

use BPFWE\Inc\Classes\BPFWE_Helper;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Post_Terms.
 *
 * Dynamic tag for displaying terms (e.g., categories or tags) associated with the current post.
 *
 * @since 1.0.0
 */
class Post_Terms extends Tag {

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
		return 'post-terms';
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
		return esc_html__( 'Post Terms', 'better-post-filter-widgets-for-elementor' );
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
		return [ TagsModule::TEXT_CATEGORY ];
	}

	/**
	 * Register controls.
	 *
	 * Define the controls for the dynamic tag, such as taxonomy selection, separator,
	 * and display options.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$taxonomy_filter_args = [
			'show_in_nav_menus' => true,
			'object_type'       => [ get_post_type() ],
		];

		$taxonomy_filter_args = apply_filters( 'bpfwe_taxonomy_args', $taxonomy_filter_args );

		$taxonomies = BPFWE_Helper::bpfwe_get_taxonomies( $taxonomy_filter_args, 'objects' );

		$options = [];

		foreach ( $taxonomies as $taxonomy => $object ) {
			$options[ $taxonomy ] = $object->label;
		}

		$this->add_control(
			'taxonomy',
			[
				'label'   => esc_html__( 'Taxonomy', 'better-post-filter-widgets-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'post_tag',
			]
		);

		$this->add_control(
			'separator',
			[
				'label'   => esc_html__( 'Separator', 'better-post-filter-widgets-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => ', ',
			]
		);

		$this->add_control(
			'max_terms',
			[
				'label'   => esc_html__( 'Max. Terms', 'better-post-filter-widgets-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 1,
				'step'    => 1,
			]
		);

		$this->add_control(
			'parent_terms_only',
			[
				'label'     => esc_html__( 'Parent Terms Only', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'label_on'  => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off' => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label'     => esc_html__( 'Link', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'label_on'  => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off' => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
			]
		);
	}

	/**
	 * Render dynamic tag output.
	 *
	 * Generates the HTML output for the terms, with optional linking, separator, and display settings.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {
		$settings          = $this->get_settings();
		$taxonomy          = $settings['taxonomy'];
		$separator         = $settings['separator'];
		$parent_terms_only = 'yes' === $settings['parent_terms_only'];
		$max_terms         = $settings['max_terms'];
		$link_enabled      = 'yes' === $settings['link']; // Check if link is enabled.

		// Get the term list.
		$terms = get_the_terms( get_the_ID(), $taxonomy );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		// Handle max terms control.
		if ( $max_terms && count( $terms ) > $max_terms ) {
			$terms = array_slice( $terms, 0, $max_terms );
		}

		$output = [];

		foreach ( $terms as $term ) {
			$term_name = esc_html( $term->name );
			if ( $link_enabled ) {
				$term_link = get_term_link( $term );
				if ( ! is_wp_error( $term_link ) ) {
					$term_name = sprintf( '<a href="%s">%s</a>', esc_url( $term_link ), $term_name );
				}
			}
			$output[] = $term_name;
		}

		echo wp_kses_post( implode( $separator, $output ) );
	}
}

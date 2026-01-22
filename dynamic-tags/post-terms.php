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
	exit; // Exit if accessed directly.
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
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'post-terms';
	}

	/**
	 * Get tag title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Post Terms', 'better-post-filter-widgets-for-elementor' );
	}

	/**
	 * Get tag group.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_group() {
		return 'post';
	}

	/**
	 * Get tag categories.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array
	 */
	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	/**
	 * Register controls.
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

		$this->add_control(
			'list_style',
			[
				'label'   => esc_html__( 'List Style', 'better-post-filter-widgets-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'Inline', 'better-post-filter-widgets-for-elementor' ),
					'ul'   => esc_html__( 'Unordered List', 'better-post-filter-widgets-for-elementor' ),
					'ol'   => esc_html__( 'Ordered List', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);
	}

	/**
	 * Render dynamic tag output.
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
		$link_enabled      = 'yes' === $settings['link'];
		$list_style        = isset( $settings['list_style'] ) ? $settings['list_style'] : 'none';

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

		if ( 'none' !== $list_style ) {
			$items = '';
			foreach ( $output as $item ) {
				$items .= '<li>' . $item . '</li>';
			}
			$tag          = ( 'ol' === $list_style ) ? 'ol' : 'ul';
			$allowed_tags = in_array( $tag, array( 'ol', 'ul' ), true ) ? $tag : '';
			echo '<' . esc_attr( $allowed_tags ) . '>' . wp_kses_post( $items ) . '</' . esc_attr( $allowed_tags ) . '>';
		} else {
			echo wp_kses_post( implode( $separator, $output ) );
		}
	}
}

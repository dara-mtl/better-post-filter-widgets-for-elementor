<?php
/**
 * Sorting Widget.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use BPFWE\Inc\Classes\BPFWE_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Widget to handle sorting functionality in Better Post and Filter Widgets.
 *
 * Extends Elementor's Widget_Base class to implement custom sorting functionality.
 *
 * @since 1.0.0
 */
class BPFWE_Sorting_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Sorting Widget widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sorting-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Sorting Widget widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Sorting Widget', 'better-post-filter-widgets-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Sorting Widget widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-filter';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Sorting Widget widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'better-post-and-filter-widgets' ];
	}

	/**
	 * Returns the styles required by the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of style handles.
	 */
	public function get_style_depends() {
		return [
			'bpfwe-widget-style',
		];
	}

	/**
	 * Returns the scripts required by the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of script handles.
	 */
	public function get_script_depends() {
		return [
			'filter-widget-script',
		];
	}

	/**
	 * Register Sorting Widget widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'field_repeater' );

		$repeater->add_control(
			'sort_title',
			[
				'label'       => esc_html__( 'Title', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a title' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'sort_by',
			[
				'label'   => esc_html__( 'Sort By', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'title',
				'options' => [
					''               => esc_html__( 'Default', 'better-post-filter-widgets-for-elementor' ),
					'date'           => esc_html__( 'Date', 'better-post-filter-widgets-for-elementor' ),
					'modified'       => esc_html__( 'Last Modified', 'better-post-filter-widgets-for-elementor' ),
					'rand'           => esc_html__( 'Random', 'better-post-filter-widgets-for-elementor' ),
					'comment_count'  => esc_html__( 'Comment Count', 'better-post-filter-widgets-for-elementor' ),
					'title'          => esc_html__( 'Title', 'better-post-filter-widgets-for-elementor' ),
					'ID'             => esc_html__( 'Post ID', 'better-post-filter-widgets-for-elementor' ),
					'author'         => esc_html__( 'Author', 'better-post-filter-widgets-for-elementor' ),
					'menu_order'     => esc_html__( 'Menu Order', 'better-post-filter-widgets-for-elementor' ),
					'relevance'      => esc_html__( 'Relevance', 'better-post-filter-widgets-for-elementor' ),
					'meta_value'     => esc_html__( 'Custom Field', 'better-post-filter-widgets-for-elementor' ),
					'meta_value_num' => esc_html__( 'Custom Field (Numeric)', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'sort_by_meta',
			[
				'label'       => esc_html__( 'Field Key', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a meta key', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'sort_by' => [ 'meta_value', 'meta_value_num' ],
				],
			]
		);

		$repeater->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => esc_html__( 'ASC', 'better-post-filter-widgets-for-elementor' ),
					'DESC' => esc_html__( 'DESC', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$repeater->end_controls_tabs();

		$this->add_control(
			'target_selector',
			[
				'label'              => esc_html__( 'Post Widget Target', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::TEXT,
				'dynamic'            => [
					'active' => true,
				],
				'placeholder'        => esc_html__( '#id, .class', 'better-post-filter-widgets-for-elementor' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'filter_post_type',
			[
				'label'              => esc_html__( 'Post Type to Filter', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SELECT,
				'description'        => esc_html__( 'To combine filter, search, & sorting widgets, use the same target selector. When used together, the filter widget’s post type takes priority.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'post',
				'options'            => array_merge( [ 'targeted_widget' => esc_html__( 'Targeted Widget', 'better-post-filter-widgets-for-elementor' ) ], BPFWE_Helper::bpfwe_get_post_types() ),
				'separator'          => 'after',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'order_by_list',
			[
				'label'         => esc_html__( 'Sorting Options', 'better-post-filter-widgets-for-elementor' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'sort_title' => esc_html__( 'Sort by default', 'better-post-filter-widgets-for-elementor' ),
						'sort_by'    => '',
					],
					[
						'sort_title' => esc_html__( 'Sort by title: alphabetical', 'better-post-filter-widgets-for-elementor' ),
						'sort_by'    => 'title',
						'order'      => 'ASC',
					],
					[
						'sort_title' => esc_html__( 'Sort by title: reverse', 'better-post-filter-widgets-for-elementor' ),
						'sort_by'    => 'title',
						'order'      => 'DESC',
					],
					[
						'sort_title' => esc_html__( 'By date: newest first', 'better-post-filter-widgets-for-elementor' ),
						'sort_by'    => 'date',
						'order'      => 'DESC',
					],
					[
						'sort_title' => esc_html__( 'By date: oldest first', 'better-post-filter-widgets-for-elementor' ),
						'sort_by'    => 'date',
						'order'      => 'ASC',
					],
				],
				'prevent_empty' => true,
				'title_field'   => '{{{ sort_by }}} ({{{ order }}})',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_select_style',
			[
				'label' => esc_html__( 'Dropdown', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'select_width',
			[
				'label'      => esc_html__( 'Width', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px', 'em', 'rem', 'custom' ],
				'range'      => [
					'%'   => [
						'min' => 0,
						'max' => 100,
					],
					'px'  => [
						'min' => 0,
						'max' => 1000,
					],
					'em'  => [
						'min' => 0,
						'max' => 100,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors'  => [
					'{{WRAPPER}} .filter-sorting-wrapper select' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'select_typography',
				'selector' => '{{WRAPPER}} .filter-sorting-wrapper select',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'field_border',
				'label'    => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'selector' => '{{WRAPPER}} .filter-sorting-wrapper select',
			]
		);

		$this->add_responsive_control(
			'field_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .filter-sorting-wrapper select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'select_color',
			[
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-sorting-wrapper select' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'select_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-sorting-wrapper select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Outputs the widget content on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( $settings['order_by_list'] ) {
			echo '<div class="filter-sorting-wrapper"><form class="form-order-by" action="/" method="get" autocomplete="on">';
			echo '<select>';
			foreach ( $settings['order_by_list'] as $item ) {
				$sort_title = $item['sort_title'] ? $item['sort_title'] : 'Sort by: ' . $item['sort_by'] . ' (' . $item['order'] . ')';
				echo '<option data-order="' . esc_attr( $item['order'] ) . '" data-meta="' . esc_attr( $item['sort_by_meta'] ) . '" value="' . esc_attr( $item['sort_by'] ) . '">' . esc_html( $sort_title ) . '</option>';
			}
			echo '</select>';
			echo '</form></div>';
		}
	}
}

<?php
/**
 * Sorting Widget.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

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
class BPF_Sorting_Widget extends \Elementor\Widget_Base {

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
		return esc_html__( 'Sorting Widget', 'bpf-widget' );
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
			'bpf-widget-style',
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
				'label' => esc_html__( 'Content', 'bpf-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'field_repeater' );

		$repeater->add_control(
			'sort_title',
			[
				'label'       => esc_html__( 'Title', 'bpf-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => 'Enter a title',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'sort_by',
			[
				'label'   => esc_html__( 'Sort By', 'bpf-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'title',
				'options' => [
					''               => esc_html__( 'Default', 'bpf-widget' ),
					'date'           => esc_html__( 'Date', 'bpf-widget' ),
					'modified'       => esc_html__( 'Last Modified', 'bpf-widget' ),
					'rand'           => esc_html__( 'Random', 'bpf-widget' ),
					'comment_count'  => esc_html__( 'Comment Count', 'bpf-widget' ),
					'title'          => esc_html__( 'Title', 'bpf-widget' ),
					'ID'             => esc_html__( 'Post ID', 'bpf-widget' ),
					'author'         => esc_html__( 'Author', 'bpf-widget' ),
					'menu_order'     => esc_html__( 'Menu Order', 'bpf-widget' ),
					'relevance'      => esc_html__( 'Relevance', 'bpf-widget' ),
					'meta_value'     => esc_html__( 'Custom Field', 'bpf-widget' ),
					'meta_value_num' => esc_html__( 'Custom Field (Numeric)', 'bpf-widget' ),
				],
			]
		);

		$repeater->add_control(
			'sort_by_meta',
			[
				'label'       => esc_html__( 'Field Key', 'bpf-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a meta key', 'bpf-widget' ),
				'label_block' => true,
				'condition'   => [
					'sort_by' => [ 'meta_value', 'meta_value_num' ],
				],
			]
		);

		$repeater->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'bpf-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => esc_html__( 'ASC', 'bpf-widget' ),
					'DESC' => esc_html__( 'DESC', 'bpf-widget' ),
				],
			]
		);

		$repeater->end_controls_tabs();

		$this->add_control(
			'order_by_list',
			[
				'label'         => esc_html__( 'Sorting Options', 'bpf-widget' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'sort_title' => esc_html__( 'Sort by default', 'bpf-widget' ),
						'sort_by'    => '',
					],
					[
						'sort_title' => esc_html__( 'Sort by title: alphabetical', 'bpf-widget' ),
						'sort_by'    => 'title',
						'order'      => 'ASC',
					],
					[
						'sort_title' => esc_html__( 'Sort by title: reverse', 'bpf-widget' ),
						'sort_by'    => 'title',
						'order'      => 'DESC',
					],
					[
						'sort_title' => esc_html__( 'By date: newest first', 'bpf-widget' ),
						'sort_by'    => 'date',
						'order'      => 'DESC',
					],
					[
						'sort_title' => esc_html__( 'By date: oldest first', 'bpf-widget' ),
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
				'label' => esc_html__( 'Dropdown', 'bpf-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'select_width',
			[
				'label'      => esc_html__( 'Width', 'bpf-widget' ),
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
				'label'    => esc_html__( 'Border', 'bpf-widget' ),
				'selector' => '{{WRAPPER}} .filter-sorting-wrapper select',
			]
		);

		$this->add_responsive_control(
			'field_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bpf-widget' ),
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
				'label'     => esc_html__( 'Color', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-sorting-wrapper select' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'select_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bpf-widget' ),
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

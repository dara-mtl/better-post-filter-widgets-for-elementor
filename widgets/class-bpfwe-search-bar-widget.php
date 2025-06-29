<?php
/**
 * Search Bar Widget.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use BPFWE\Inc\Classes\BPFWE_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class BPFWE_Search_Bar_Widget.
 *
 * Handles the rendering and settings for the Search Bar Widget.
 *
 * @since 1.0.0
 */
class BPFWE_Search_Bar_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Search Bar Widget widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'search-bar-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Search Bar Widget widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Search Bar Widget', 'better-post-filter-widgets-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Search Bar Widget widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-search';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Search Bar Widget widget belongs to.
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
	 * Get the list of style dependencies for the widget.
	 *
	 * This method returns an array of stylesheets handles that are required to be loaded
	 * for this widget. Elementor uses this list to ensure that the necessary stylesheets
	 * are enqueued when the widget is rendered.
	 *
	 * @since 1.0.0
	 * @return string[] An array of style handles.
	 */
	public function get_style_depends() {
		return [
			'bpfwe-widget-style',
		];
	}

	/**
	 * Get the script dependencies for the widget.
	 *
	 * This function returns an array of scripts that need to be enqueued for the widget to function correctly.
	 * It may include front-end JavaScript libraries such as jQuery or custom scripts.
	 *
	 * @return array Array of script dependencies.
	 */
	public function get_script_depends() {
		return [
			'filter-widget-script',
		];
	}

	/**
	 * Register Search Bar Widget widget controls.
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
				'options'            => BPFWE_Helper::bpfwe_get_post_types(),
				'separator'          => 'after',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'search_placeholder_text',
			[
				'label'       => esc_html__( 'Placeholder', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Search by keywords...', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Search by keywords...', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'display_submit',
			[
				'label'        => esc_html__( 'Display Submit Button', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'search_button_text',
			[
				'label'       => esc_html__( 'Button Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => esc_html__( 'Search', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Search', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_control(
			'redirect_submission',
			[
				'label'        => esc_html__( 'Redirect After Submission', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'redirect_url',
			[
				'label'       => esc_html__( 'Redirect URL', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'options'     => false,
				'label_block' => true,
				'condition'   => [
					'redirect_submission' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'search_bar_style',
			[
				'label' => esc_html__( 'Search Bar', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'search_bar_typography',
				'selector' =>
					'{{WRAPPER}} form .search-container input[type="text"], {{WRAPPER}} form .search-container button
				',
			]
		);

		$this->add_control(
			'search_bar_input_color',
			[
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_bar_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container input::placeholder' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'search_bar_input_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_bar_input_title',
			[
				'label'     => esc_html__( 'Input Style', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'style_tabs_search_bar' );

		$this->start_controls_tab(
			'search_bar_style_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_responsive_control(
			'search_bar_width',
			[
				'label'      => esc_html__( 'Width', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} form .search-container input' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'search_bar_input_border',
				'label'    => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'selector' => '{{WRAPPER}} .search-container input',
			]
		);

		$this->add_control(
			'search_bar_input_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .search-container input' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'search_bar_style_style_focus',
			[
				'label' => esc_html__( 'Focus', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_responsive_control(
			'search_bar_width_focus',
			[
				'label'      => esc_html__( 'Width', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} form .search-container input:focus, {{WRAPPER}} form .search-container input:focus-visible' =>
						'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'search_bar_input_border_focus',
				'label'    => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'selector' => '{{WRAPPER}} .search-container input:focus, {{WRAPPER}} .search-container input:focus-visible',
			]
		);

		$this->add_control(
			'search_bar_input_border_radius_focus',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .search-container input:focus, {{WRAPPER}} .search-container input:focus-visible' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'search_bar_button_title',
			[
				'label'     => esc_html__( 'Button Style', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'search_bar_button_width',
			[
				'label'      => esc_html__( 'Width', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default'    => [
					'size' => 33.33,
					'unit' => '%',
				],
				'selectors'  => [
					'{{WRAPPER}} .search-container button' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_search_bar_button' );

		$this->start_controls_tab(
			'search_bar_button_style_normal',
			[
				'label'     => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_bar_button_color',
			[
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_bar_button_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'search_bar_button_border',
				'label'     => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'selector'  => '{{WRAPPER}} .search-container button',
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_bar_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .search-container button' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'search_bar_button_style_hover',
			[
				'label'     => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_bar_button_color_hover',
			[
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container button:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_bar_button_background_color_hover',
			[
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container button:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'search_bar_button_border_hover',
				'label'     => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'selector'  => '{{WRAPPER}} .search-container button:hover',
				'condition' => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->add_control(
			'search_bar_button_border_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .search-container button:hover' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'display_submit' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render the widget output.
	 *
	 * This function generates the HTML output for the widget, including content such as
	 * post listings, styling, and any dynamic data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$placeholder_text = isset( $settings['search_placeholder_text'] ) ? $settings['search_placeholder_text'] : '';
		$button_text      = isset( $settings['search_button_text'] ) ? $settings['search_button_text'] : 'Search';
		$action_url       = get_permalink();
		$input_name       = 's';
		$target_selector  = ! empty( $settings['target_selector'] ) ? $settings['target_selector'] : '';

		$form_classes = [ 'search-post' ];

		if ( $target_selector && ( ! $settings['redirect_submission'] || ( $settings['redirect_submission'] && ! $settings['redirect_url']['url'] ) ) ) {
			$form_classes[] = 'no-redirect';
		}

		if ( ! empty( $settings['redirect_url']['url'] ) ) {
			$action_url = esc_url( $settings['redirect_url']['url'] );
			$input_name = 'search';
		}

		// Render the form.
		if ( $settings['display_submit'] ) {
			echo '
		<form id="search-bar-' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( implode( ' ', $form_classes ) ) . '" action="' . esc_url( $action_url ) . '" method="get" autocomplete="on">
			<div class="search-container">
				<input type="text" name="' . esc_attr( $input_name ) . '" placeholder="' . esc_attr( $placeholder_text ) . '">
				<input type="hidden" name="post-type" value="' . esc_attr( $settings['filter_post_type'] ) . '">
				<button type="submit">' . esc_html( $button_text ) . '</button>
			</div>
		</form>';
		} else {
			echo '
		<form id="search-bar-' . esc_attr( $this->get_id() ) . '" class="' . esc_attr( implode( ' ', $form_classes ) ) . '" action="' . esc_url( $action_url ) . '" method="get" autocomplete="on">
			<div class="search-container">
				<input type="text" name="' . esc_attr( $input_name ) . '" placeholder="' . esc_attr( $placeholder_text ) . '">
				<input type="hidden" name="post-type" value="' . esc_attr( $settings['filter_post_type'] ) . '">
			</div>
		</form>';
		}
	}
}

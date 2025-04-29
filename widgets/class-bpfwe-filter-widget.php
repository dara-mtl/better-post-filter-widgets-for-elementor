<?php
/**
 * Filter Widget.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use BPFWE\Inc\Classes\BPFWE_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class BPFWE_Filter_Widget
 *
 * This class is responsible for rendering the BPFWE filter widget, which displays a list of filters
 * based on specific criteria. It includes methods for widget form rendering, output generation,
 * and script dependencies.
 */
class BPFWE_Filter_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Filter Widget widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'filter-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Filter Widget widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Filter Widget', 'better-post-filter-widgets-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Filter Widget widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-taxonomy-filter';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Filter Widget widget belongs to.
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
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return [ 'bpfwe-widget-style', 'bpfwe-select2-style' ];
		}

		$settings = $this->get_settings_for_display();

		foreach ( $settings['filter_list'] as $item ) {
			$filter_style    = $item['filter_style'] ?? '';
			$filter_style_cf = $item['filter_style_cf'] ?? '';

			if ( 'select2' === $filter_style || 'select2' === $filter_style_cf ) {
				return [ 'bpfwe-widget-style', 'bpfwe-select2-style' ];
			}
		}

		return [ 'bpfwe-widget-style' ];
	}

	/**
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return [ 'filter-widget-script', 'bpfwe-select2-script' ];
		}

		$settings = $this->get_settings_for_display();

		foreach ( $settings['filter_list'] as $item ) {
			$filter_style    = $item['filter_style'] ?? '';
			$filter_style_cf = $item['filter_style_cf'] ?? '';

			if ( 'select2' === $filter_style || 'select2' === $filter_style_cf ) {
				return [ 'filter-widget-script', 'bpfwe-select2-script' ];
			}
		}

		return [ 'filter-widget-script' ];
	}

	/**
	 * Register Filter Widget widget controls.
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
				'label' => esc_html__( 'Filter Content', 'better-post-filter-widgets-for-elementor' ),
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
				'default'            => 'post',
				'options'            => BPFWE_Helper::bpfwe_get_post_types(),
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'nb_columns',
			[
				'type'           => \Elementor\Controls_Manager::SELECT,
				'label'          => esc_html__( 'Columns', 'better-post-filter-widgets-for-elementor' ),
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
				],
				'default'        => '1',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'separator'      => 'after',
				'selectors'      => [
					'{{WRAPPER}} .elementor-grid' =>
						'grid-template-columns: repeat({{VALUE}},1fr)',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'field_repeater' );

		$repeater->start_controls_tab(
			'content',
			[
				'label' => esc_html__( 'Content', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$repeater->add_control(
			'filter_title',
			[
				'label'       => esc_html__( 'Group Label', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'New Filter', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'New Filter', 'better-post-filter-widgets-for-elementor' ),
				'separator'   => 'after',
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'select_filter',
			[
				'label'   => esc_html__( 'Data Source', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'Taxonomy',
				'options' => [
					'Taxonomy'     => esc_html__( 'Taxonomy', 'better-post-filter-widgets-for-elementor' ),
					'Custom Field' => esc_html__( 'Custom Field', 'better-post-filter-widgets-for-elementor' ),
					'Numeric'      => esc_html__( 'Custom Field (Numeric)', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'filter_by',
			[
				'label'     => esc_html__( 'Select a Taxonomy', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => BPFWE_Helper::get_taxonomies_options(),
				'condition' => [
					'select_filter' => 'Taxonomy',
				],
			]
		);

		$repeater->add_control(
			'meta_key',
			[
				'label'       => esc_html__( 'Field Key', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a meta key', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'select_filter' => [ 'Custom Field', 'Numeric' ],
				],
			]
		);

		$repeater->add_control(
			'filter_style_numeric',
			[
				'label'     => esc_html__( 'Filter Type', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'range',
				'options'   => [
					'range'      => esc_html__( 'Range', 'better-post-filter-widgets-for-elementor' ),
					'checkboxes' => esc_html__( 'Checkboxes', 'better-post-filter-widgets-for-elementor' ),
					'radio'      => esc_html__( 'Radio Buttons', 'better-post-filter-widgets-for-elementor' ),
					'list'       => esc_html__( 'Label List', 'better-post-filter-widgets-for-elementor' ),
				],
				'separator' => 'before',
				'condition' => [
					'select_filter' => 'Numeric',
				],
			]
		);

		$repeater->add_control(
			'insert_before_field',
			[
				'label'     => esc_html__( 'Before', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'select_filter'        => 'Numeric',
					'filter_style_numeric' => 'range',
				],
			]
		);

		$repeater->add_control(
			'filter_style_cf',
			[
				'label'     => esc_html__( 'Field Type', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'checkboxes',
				'options'   => [
					'checkboxes' => esc_html__( 'Checkboxes', 'better-post-filter-widgets-for-elementor' ),
					'radio'      => esc_html__( 'Radio Buttons', 'better-post-filter-widgets-for-elementor' ),
					'list'       => esc_html__( 'Label List', 'better-post-filter-widgets-for-elementor' ),
					'dropdown'   => esc_html__( 'Dropdown', 'better-post-filter-widgets-for-elementor' ),
					'select2'    => esc_html__( 'Select2', 'better-post-filter-widgets-for-elementor' ),
					'input'      => esc_html__( 'Input Field', 'better-post-filter-widgets-for-elementor' ),
				],
				'separator' => 'before',
				'condition' => [
					'select_filter' => 'Custom Field',
				],
			]
		);

		$repeater->add_control(
			'option_text_cf',
			[
				'label'       => esc_html__( 'Placeholder', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Choose an option', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Choose an option', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'select_filter'   => 'Custom Field',
					'filter_style_cf' => [ 'dropdown','select2' ],
				],
			]
		);

		$repeater->add_control(
			'multi_select2_cf',
			[
				'label'        => esc_html__( 'Enable Multiple Select', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'conditions'   => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'filter_style_cf',
							'operator' => '===',
							'value'    => 'select2',
						],
						[
							'name'     => 'select_filter',
							'operator' => '!==',
							'value'    => 'Numeric',
						],
						[
							'name'     => 'select_filter',
							'operator' => '!==',
							'value'    => 'Taxonomy',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'filter_style',
			[
				'label'     => esc_html__( 'Field Type', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'checkboxes',
				'options'   => [
					'checkboxes' => esc_html__( 'Checkboxes', 'better-post-filter-widgets-for-elementor' ),
					'radio'      => esc_html__( 'Radio Buttons', 'better-post-filter-widgets-for-elementor' ),
					'list'       => esc_html__( 'Label List', 'better-post-filter-widgets-for-elementor' ),
					'dropdown'   => esc_html__( 'Dropdown', 'better-post-filter-widgets-for-elementor' ),
					'select2'    => esc_html__( 'Select2', 'better-post-filter-widgets-for-elementor' ),
				],
				'separator' => 'before',
				'condition' => [
					'select_filter!' => [ 'Numeric','Custom Field' ],
				],
			]
		);

		$repeater->add_control(
			'layout_direction',
			[
				'label'                => esc_html__( 'Label Direction', 'better-post-filter-widgets-for-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'block'        => [
						'title' => esc_html__( 'Vertical', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-justify-start-h',
					],
					'inline-block' => [
						'title' => esc_html__( 'Horizontal', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-justify-end-v',
					],
				],
				'default'              => 'block',
				'separator'            => 'before',
				'selectors'            => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .taxonomy-filter, {{WRAPPER}} {{CURRENT_ITEM}} .taxonomy-filter li' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'block'        => 'display: block;',
					'inline-block' => 'display: inline-flex; align-items: flex-end;',
				],
				'conditions'           => [
					'relation' => 'and',
					'terms'    => [
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'select_filter',
									'operator' => '!==',
									'value'    => 'Numeric',
								],
								[
									'name'     => 'filter_style',
									'operator' => 'in',
									'value'    => [ 'checkboxes', 'radio' ],
								],
							],
						],
						[
							'relation' => 'and',
							'terms'    => [
								[
									'name'     => 'select_filter',
									'operator' => '!==',
									'value'    => 'Numeric',
								],
								[
									'name'     => 'filter_style_cf',
									'operator' => 'in',
									'value'    => [ 'checkboxes', 'radio' ],
								],
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'hide_input_swatch',
			[
				'label'        => esc_html__( 'Hide Input', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'hide-swatch-input',
				'condition'    => [
					'filter_style' => [ 'checkboxes','radio' ],
				],
			]
		);

		$repeater->add_control(
			'display_swatch',
			[
				'label'        => esc_html__( 'Display Swatch', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'select_filter' => 'Taxonomy',
					'filter_style'  => [ 'checkboxes','radio' ],
				],
			]
		);

		$repeater->add_control(
			'swatch_notice',
			[
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Add swatches under Taxonomy > Terms to display them', 'better-post-filter-widgets-for-elementor' ),
				'content_classes' => 'elementor-descriptor',
				'condition'       => [
					'display_swatch' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'hide_label_swatch',
			[
				'label'        => esc_html__( 'Hide Label', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'hide-swatch-label',
				'condition'    => [
					'display_swatch' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'option_text',
			[
				'label'       => esc_html__( 'Placeholder', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Choose an option', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Choose an option', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'select_filter' => 'Taxonomy',
					'filter_style'  => [ 'dropdown','select2' ],
				],
			]
		);

		$repeater->add_control(
			'multi_select2',
			[
				'label'        => esc_html__( 'Enable Multiple Select', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'conditions'   => [
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'filter_style',
							'operator' => '===',
							'value'    => 'select2',
						],
						[
							'name'     => 'select_filter',
							'operator' => '!==',
							'value'    => 'Custom Field',
						],
						[
							'name'     => 'select_filter',
							'operator' => '!==',
							'value'    => 'Numeric',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'text_input_placeholder',
			[
				'label'       => esc_html__( 'Placeholder', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Search by keywords...', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Search by keywords...', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'filter_style_cf' => 'input',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'field_style',
			[
				'label' => esc_html__( 'Advanced', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$repeater->add_control(
			'sort_terms',
			[
				'label'     => esc_html__( 'Sort By', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'name',
				'options'   => [
					'name'       => esc_html__( 'Name', 'better-post-filter-widgets-for-elementor' ),
					'slug'       => esc_html__( 'Slug', 'better-post-filter-widgets-for-elementor' ),
					'count'      => esc_html__( 'Count', 'better-post-filter-widgets-for-elementor' ),
					'term_group' => esc_html__( 'Term Group', 'better-post-filter-widgets-for-elementor' ),
					'term_order' => esc_html__( 'Term Order', 'better-post-filter-widgets-for-elementor' ),
					'term_id'    => esc_html__( 'Term ID', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'select_filter' => 'Taxonomy',
				],
			]
		);

		$repeater->add_control(
			'filter_logic',
			[
				'label'   => esc_html__( 'Group Logic', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'OR',
				'options' => [
					'OR'  => esc_html__( 'OR', 'better-post-filter-widgets-for-elementor' ),
					'AND' => esc_html__( 'AND', 'better-post-filter-widgets-for-elementor' ),
					// 'IN' => 'IN',
					// 'NOT IN' => 'NOT IN',
					// 'EXISTS' => 'EXISTS',
					// 'NOT EXISTS' => 'NOT EXISTS',
				],
			]
		);

		$repeater->add_control(
			'display_empty',
			[
				'label'        => esc_html__( 'Display Empty Terms', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'separator'    => 'before',
				'condition'    => [
					'select_filter' => 'Taxonomy',
				],
			]
		);

		$repeater->add_control(
			'show_counter',
			[
				'label'        => esc_html__( 'Show Post Count', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'separator'    => 'before',
				'condition'    => [
					'select_filter' => 'Taxonomy',
				],
			]
		);

		$repeater->add_control(
			'show_hierarchy',
			[
				'label'        => esc_html__( 'Show Hierarchy', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'filter_style!'  => [ 'list' ],
					'select_filter!' => [ 'Numeric','Custom Field' ],
				],
			]
		);

		$repeater->add_control(
			'toggle_child',
			[
				'label'        => esc_html__( 'Toggle Child Terms', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'filter_style!' => [ 'list','dropdown','select2' ],
					'select_filter' => 'Taxonomy',
				],
			]
		);

		$repeater->add_control(
			'show_toggle',
			[
				'label'        => esc_html__( 'More/Less', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'show-toggle',
				'condition'    => [
					'filter_style!'  => [ 'list','dropdown','select2' ],
					'select_filter!' => 'Numeric',
				],
			]
		);

		$repeater->add_control(
			'show_toggle_numeric',
			[
				'label'        => esc_html__( 'More/Less', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'show-toggle',
				'condition'    => [
					'filter_style_numeric!' => [ 'list','range' ],
					'select_filter'         => 'Numeric',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'filter_list',
			[
				'label'         => esc_html__( 'Filter List', 'better-post-filter-widgets-for-elementor' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => [
					[
						'filter_by' => esc_html__( 'category', 'better-post-filter-widgets-for-elementor' ),
					],
				],
				'prevent_empty' => true,
				'title_field'   => '{{{ filter_title }}}',
			]
		);

		$this->add_control(
			'group_options_title',
			[
				'label'     => esc_html__( 'Parent Options', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'group_logic',
			[
				'label'              => esc_html__( 'Parent Logic', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SELECT,
				'default'            => 'AND',
				'options'            => [
					'AND' => esc_html__( 'AND', 'better-post-filter-widgets-for-elementor' ),
					'OR'  => esc_html__( 'OR', 'better-post-filter-widgets-for-elementor' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'dynamic_filtering',
			[
				'label'              => esc_html__( 'Dynamic Archive Filtering', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'performance_section',
			[
				'label' => esc_html__( 'Performance Settings', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'optimize_query',
			[
				'label'              => __( 'Load Only Post ID', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'Off', 'better-post-filter-widgets-for-elementor' ),
				'description'        => __( 'Loads only post IDs. Best for ID-based widgets but may break those needing full post details. Default: Off. Impact on Speed: High.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'no_found_rows',
			[
				'label'              => __( 'Skip Pagination Count', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'Off', 'better-post-filter-widgets-for-elementor' ),
				'description'        => __( 'Skips counting total posts. Use only if pagination isn’t needed. Default: Off. Impact on Speed: Medium.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'suppress_filters',
			[
				'label'              => __( 'Bypass Query Modifications', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'Off', 'better-post-filter-widgets-for-elementor' ),
				'description'        => __( 'Ignores query tweaks. May break 3rd party features. Default: Off. Impact on Speed: Medium.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'              => __( 'Posts Per Page', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::NUMBER,
				'description'        => __( 'Limits the number of posts per page. Use -1 to use post widget’s default value, if accesible by the query. Default: -1. Impact on Speed: High.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => -1,
				'min'                => -1,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'transient_duration',
			[
				'label'       => __( 'Cache filter’s terms (s)', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Caches filter terms for faster loading. Set to 0 to disable (not recommended). Default: 86400 (1 day). Impact on Speed: High.', 'better-post-filter-widgets-for-elementor' ),
				'default'     => 86400,
				'min'         => 0,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'settings_section',
			[
				'label' => esc_html__( 'Additional Options', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_reset',
			[
				'label'        => esc_html__( 'Display Reset Button', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'separator'    => 'before',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'reset_text',
			[
				'label'     => esc_html__( 'Reset Button Text', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Reset', 'better-post-filter-widgets-for-elementor' ),
				'condition' => [
					'show_reset' => 'yes',
				],
			]
		);

		$this->add_control(
			'use_submit',
			[
				'label'        => esc_html__( 'Display Submit Button', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'separator'    => 'before',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'submit_text',
			[
				'label'     => esc_html__( 'Submit Button Text', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Submit', 'better-post-filter-widgets-for-elementor' ),
				'condition' => [
					'use_submit' => 'yes',
				],
			]
		);

		$this->add_control(
			'scroll_to_top',
			[
				'label'              => esc_html__( 'Scroll to top', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'separator'          => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'nothing_found_message',
			[
				'type'               => \Elementor\Controls_Manager::TEXTAREA,
				'label'              => esc_html__( 'Nothing Found Message', 'better-post-filter-widgets-for-elementor' ),
				'rows'               => 3,
				'separator'          => 'before',
				'default'            => esc_html__( 'It seems we can’t find what you’re looking for.', 'better-post-filter-widgets-for-elementor' ),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- SECTION: Style
		$this->start_controls_section(
			'section_container_style',
			[
				'label' => esc_html__( 'Filter Container', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_spacing',
			[
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Column Gap', 'better-post-filter-widgets-for-elementor' ),
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_spacing',
			[
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Row Gap', 'better-post-filter-widgets-for-elementor' ),
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title',
			array(
				'label' => esc_html__( 'Group Label', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'filter_heading_padding',
			array(
				'label'      => esc_html__( 'Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .filter-title' => 'margin-bottom: {{SIZE}}{{UNIT}}; display: block;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_title_default_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .filter-title',
			)
		);

		$this->add_control(
			'filter_title_color',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .filter-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label',
			array(
				'label' => esc_html__( 'Input Label', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'filter_label_spacing',
			array(
				'label'      => esc_html__( 'Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-tax label' => 'margin-bottom: {{SIZE}}{{UNIT}}; display: flex; align-items: center;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_label_default_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .form-tax label',
			)
		);

		$this->add_control(
			'filter_label_color',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .form-tax label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_swatch',
			array(
				'label' => esc_html__( 'Swatch', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'swatch_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .bpfwe-swatch',
			)
		);

		$this->add_control(
			'swatch_color',
			array(
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .bpfwe-swatch' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'swatch_background',
			array(
				'label'     => esc_html__( 'Swatch Background', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .bpfwe-swatch' => 'background: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'swatch_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-swatch' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'swatch_margin',
			array(
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-swatch' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'swatch_style_tabs'
		);

		$this->start_controls_tab(
			'swatch_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'swatch_opacity_normal',
			[
				'label'     => esc_html__( 'Opacity', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max'  => 1,
						'min'  => 0.1,
						'step' => 0.01,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .bpfwe-swatch' =>
						'opacity: {{SIZE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'swatch_border',
				'selector' => '{{WRAPPER}} .bpfwe-swatch',
			)
		);

		$this->add_responsive_control(
			'swatch_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-swatch' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'swatch_style_focus_tab',
			[
				'label' => esc_html__( 'Focus', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'swatch_opacity_focus',
			[
				'label'     => esc_html__( 'Hover Opacity', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max'  => 1,
						'min'  => 0.1,
						'step' => 0.01,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 0.7,
				],
				'selectors' => [
					'{{WRAPPER}}  input[type="checkbox"]:checked + span .bpfwe-swatch, {{WRAPPER}} input[type="radio"]:checked + span .bpfwe-swatch' =>
						'opacity: {{SIZE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'swatch_focus_border',
				'selector' => '{{WRAPPER}} input[type="checkbox"]:checked + span .bpfwe-swatch, {{WRAPPER}} input[type="radio"]:checked + span .bpfwe-swatch',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'group_separator_styling_title',
			[
				'label'     => esc_html__( 'Group Separator', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'group_separator_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .bpfwe-group-separator',
			]
		);

		$this->add_control(
			'group_separator_color',
			[
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bpfwe-group-separator' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'group_separator_border',
				'selector' => '{{WRAPPER}} .bpfwe-group-separator',
			]
		);

		$this->add_responsive_control(
			'group_separator_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bpfwe-group-separator' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'group_separator_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bpfwe-group-separator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_input',
			array(
				'label' => esc_html__( 'Input', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_field_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .form-tax input:not([type="radio"]):not([type="checkbox"]), {{WRAPPER}} .form-tax textarea',
			)
		);

		$this->add_control(
			'filter_field_color',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .form-tax input' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_placeholder_color',
			array(
				'label'     => esc_html__( 'Placeholder Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .form-tax ::-webkit-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .form-tax ::-moz-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .form-tax ::-ms-input-placeholder' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_input_background',
			array(
				'label'     => esc_html__( 'Field Background', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .form-tax textarea' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'filter_input_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .form-tax textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_input_margin',
			array(
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .form-tax textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'input_style_tabs'
		);

		$this->start_controls_tab(
			'input_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_input_border',
				'selector' => '{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]):not(:focus), {{WRAPPER}} .form-tax textarea',
			)
		);

		$this->add_responsive_control(
			'filter_input_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .form-tax textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'input_style_focus_tab',
			[
				'label' => esc_html__( 'Focus', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_input_focus_border',
				'selector' => '{{WRAPPER}} select:focus, {{WRAPPER}} .form-tax input:focus, {{WRAPPER}} .form-tax textarea:focus, {{WRAPPER}} .form-tax .cmb2-file:focus',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label_list',
			array(
				'label' => esc_html__( 'Label List', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_list_filter_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .list-style label span',
			)
		);

		$this->add_responsive_control(
			'filter_label_list_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .list-style label span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_label_list_margin',
			array(
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .list-style label span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'label_list_input_style_tabs'
		);

		$this->start_controls_tab(
			'label_list_input_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'label_list_filter_color',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .list-style label span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_label_list_background',
			array(
				'label'     => esc_html__( 'Field Background', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .list-style label span' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_label_list_border',
				'selector' => '{{WRAPPER}} .list-style label span',
			)
		);

		$this->add_responsive_control(
			'filter_label_list_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .list-style label span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_list_input_style_focus_tab',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'label_list_filter_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .list-style label:hover span, {{WRAPPER}} .list-style label input[type="checkbox"]:checked + span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_label_list_background_hover',
			array(
				'label'     => esc_html__( 'Field Background', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .list-style label:hover span, {{WRAPPER}} .list-style label input[type="checkbox"]:checked + span' => 'background-color: {{VALUE}} !important; background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_label_list_hover_border',
				'selector' => '{{WRAPPER}} .list-style label:hover span, {{WRAPPER}} .list-style label input[type="checkbox"]:checked + span',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_checkbox_radio',
			array(
				'label' => esc_html__( 'Checkbox/Radio', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'checkbox_radio_size',
			array(
				'label'      => esc_html__( 'Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-tax input[type="radio"], {{WRAPPER}} .form-tax input[type="checkbox"]' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'checkbox_radio_selected_color',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .form-tax input[type="radio"]:checked::before, {{WRAPPER}} .form-tax input[type="checkbox"]:checked::before' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'label'    => esc_html__( 'Checkbox/Radio Border', 'better-post-filter-widgets-for-elementor' ),
				'name'     => 'checkbox_radio_border',
				'selector' => '{{WRAPPER}} .form-tax input[type="radio"], {{WRAPPER}} .form-tax input[type="checkbox"]',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_select2',
			array(
				'label' => esc_html__( 'Select2', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'filter_select2_width',
			array(
				'label'      => esc_html__( 'Width', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-select2 .select2-selection, {{WRAPPER}} .bpfwe-select2 .select2-selection__rendered, {{WRAPPER}} .bpfwe-select2 .select2' => 'width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'filter_select2_height',
			array(
				'label'      => esc_html__( 'Height', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'separator'  => 'after',
				'default'    => [
					'unit' => 'px',
					'size' => 42,
				],
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-select2 .select2-selection, {{WRAPPER}} .bpfwe-select2 .select2-selection__rendered' => 'height: auto; line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'selection_select2_title',
			[
				'label' => esc_html__( 'Selection', 'better-post-filter-widgets-for-elementor' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'selection_select2_color',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .bpfwe-multi-select2 .select2-search input, {{WRAPPER}} .select2-selection--single .select2-selection__rendered, .select2-results__options, {{WRAPPER}} .bpfwe-multi-select2 .select2-selection__choice, {{WRAPPER}} .bpfwe-multi-select2 .select2-selection__choice__remove' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'selection_select2_background',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .select2-selection--single .select2-selection__rendered, {{WRAPPER}} .bpfwe-multi-select2 .select2-selection__choice, .select2-results__options' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'selection_select2_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-multi-select2 .select2-search, {{WRAPPER}} .bpfwe-multi-select2 .select2-selection__choice' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'selection_select2_margin',
			array(
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-multi-select2 .select2-search, {{WRAPPER}} .bpfwe-multi-select2 .select2-selection__choice' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'selection_select2_border',
				'selector' => '{{WRAPPER}} .bpfwe-multi-select2 .select2-selection__choice, {{WRAPPER}} .form-tax .bpfwe-select2 .select2-selection',
			)
		);

		$this->add_responsive_control(
			'selection_select2_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-multi-select2 .select2-selection__choice' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_control(
			'dropdown_select2_title',
			[
				'label'     => esc_html__( 'Dropdown', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_select2_border',
				'selector' => '{{WRAPPER}} .select2-selection, .select2-dropdown',
			)
		);

		$this->add_control(
			'filter_select2_focus',
			array(
				'label'     => esc_html__( 'Focus Border Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .select2-selection:focus' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'filter_select2_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .select2-selection, .select2-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_reset_button_styles',
			[
				'label'     => esc_html__( 'Reset Button', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_reset' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'reset_button_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} button.reset-form',
			)
		);

		$this->add_control(
			'reset_button_align',
			[
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'label'     => esc_html__( 'Alignment', 'better-post-filter-widgets-for-elementor' ),
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}}  button.reset-form' =>
						'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'reset_button_spacing',
			array(
				'label'     => esc_html__( 'Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} button.reset-form' => 'margin-top: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'reset_button_width',
			array(
				'label'      => esc_html__( 'Width', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1200,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} button.reset-form' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'reset_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} button.reset-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'reset_button_style_tabs' );

		$this->start_controls_tab(
			'reset_button_normal',
			array(
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'reset_button_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.reset-form' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_button_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} button.reset-form' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'reset_button_border',
				'selector' => '{{WRAPPER}} button.reset-form',
			)
		);

		$this->add_responsive_control(
			'reset_button_border_radius',
			array(
				'label'     => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} button.reset-form' => 'border-radius: {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'reset_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'reset_button_hover_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.reset-form:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_button_hover_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.reset-form:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_button_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.reset-form:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submit_button_styles',
			[
				'label'     => esc_html__( 'Submit Button', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'use_submit' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'submit_button_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} button.submit-form',
			)
		);

		$this->add_control(
			'submit_button_align',
			[
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'label'     => esc_html__( 'Alignment', 'better-post-filter-widgets-for-elementor' ),
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}}  button.submit-form' =>
						'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'submit_button_spacing',
			array(
				'label'     => esc_html__( 'Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} button.submit-form' => 'margin-top: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'submit_button_width',
			array(
				'label'      => esc_html__( 'Width', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1200,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} button.submit-form' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} button.submit-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'submit_button_style_tabs' );

		$this->start_controls_tab(
			'submit_button_normal',
			array(
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'submit_button_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.submit-form' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} button.submit-form' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'submit_button_border',
				'selector' => '{{WRAPPER}} button.submit-form',
			)
		);

		$this->add_responsive_control(
			'submit_button_border_radius',
			array(
				'label'     => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} button.submit-form' => 'border-radius: {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'submit_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'submit_button_hover_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.submit-form:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_hover_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.submit-form:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.submit-form:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Indicates whether the content is dynamic and should not be cached.
	 *
	 * This method should be overridden by widgets or dynamic tags that generate
	 * content which changes frequently, or is dependent on real-time data,
	 * ensuring that the content is not cached and is always re-rendered when requested.
	 *
	 * @return bool True if the content is dynamic and should not be cached, false otherwise.
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Render filter widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings           = $this->get_settings_for_display();
		$widget_id          = $this->get_id();
		$transient_duration = ( ! empty( $settings['transient_duration'] ) ) ? absint( $settings['transient_duration'] ) : 86400;
		$show_counter       = '';
		$toggleable_class   = '';
		$min_value          = '';
		$max_value          = '';

		if ( $settings['filter_list'] ) {
			$index = 0;
			echo '
			<div class="filter-container" data-target-post-widget="' . esc_attr( $settings['target_selector'] ) . '">
			<form id="filter-' . esc_attr( $widget_id ) . '" class="form-tax elementor-grid" action="/" method="get" autocomplete="on">
			<input type="hidden" name="bpf_filter_nonce" value="' . esc_attr( wp_create_nonce( 'nonce' ) ) . '">
			';

			if ( is_archive() ) {
				$queried_object = get_queried_object();
				$archive_type   = '';

				if ( $queried_object instanceof WP_User ) {
					$archive_type = 'author';
				} elseif ( $queried_object instanceof WP_Date_Query ) {
					$archive_type = 'date';
				} elseif ( $queried_object instanceof WP_Term ) {
					$archive_type = 'taxonomy';
				} elseif ( $queried_object instanceof WP_Post_Type ) {
					$archive_type = 'post_type';
				}

				echo '<input type="hidden" name="archive_type" value="' . esc_attr( $archive_type ) . '">';

				if ( 'taxonomy' === $archive_type && $queried_object instanceof WP_Term ) {
					echo '
					<input type="hidden" name="archive_id" value="' . esc_attr( $queried_object->term_id ) . '">
					<input type="hidden" name="archive_taxonomy" value="' . esc_attr( $queried_object->taxonomy ) . '">
					';
				} elseif ( 'post_type' === $archive_type && $queried_object instanceof WP_Post_Type ) {
					echo '<input type="hidden" name="archive_post_type" value="' . esc_attr( $queried_object->name ) . '">';
				} elseif ( $queried_object instanceof WP_User ) {
					echo '<input type="hidden" name="archive_id" value="' . esc_attr( $queried_object->ID ) . '">';
				}
			}

			foreach ( $settings['filter_list'] as $item ) {
				if ( 'Taxonomy' === $item['select_filter'] && ! taxonomy_exists( $item['filter_by'] ) ) {
					return;
				}
				if ( 'Custom Field' === $item['select_filter'] && empty( $item['meta_key'] ) ) {
					return;
				}
				if ( 'Numeric' === $item['select_filter'] && empty( $item['meta_key'] ) ) {
					return;
				}

				++$index;

				if ( 'Taxonomy' === $item['select_filter'] ) {

					// Check if transient exists.
					$transient_key = 'filter_widget_taxonomy_' . $item['filter_by'];

					$hiterms       = get_transient( $transient_key );
					$display_empty = 'yes' === $item['display_empty'] ? false : true;

					// Bypass transient for users with editing capabilities.
					if ( false === $hiterms || current_user_can( 'edit_posts' ) ) {
						$hiterms = get_terms(
							[
								'taxonomy'          => $item['filter_by'],
								'orderby'           => $item['sort_terms'],
								'hide_empty'        => $display_empty,
								'parent'            => 'yes' === $item['show_hierarchy'] ? 0 : null,
								'fields'            => 'all',
								'update_meta_cache' => false,
							]
						);

						if ( $transient_duration > 0 && ! current_user_can( 'edit_posts' ) ) {
							set_transient( $transient_key, $hiterms, $transient_duration );
						}
					}

					if ( 'checkboxes' === $item['filter_style'] || 'checkboxes' === $item['filter_style_cf'] ) {
						$term_index = 0;
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['filter_by'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-taxonomy-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $item['hide_label_swatch'] ) . ' ' . esc_attr( $item['hide_input_swatch'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';

						foreach ( $hiterms as $key => $hiterm ) {
							$show_counter   = 'yes' === $item['show_counter'] ? ' (' . intval( $hiterm->count ) . ')' : '';
							$swatches_type  = 'yes' === $item['display_swatch'] ? get_term_meta( $hiterm->term_id, 'bpfwe_swatches_type', true ) : '';
							$group_text     = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_group_text', true );
							$swatch_html    = '';
							$separator_html = '';

							if ( $group_text && 'yes' === $item['display_swatch'] ) {
								$separator_html = '<div class="bpfwe-group-separator" role="separator" aria-label="' . esc_attr( $group_text . ' Group Separator' ) . '">' . esc_html( $group_text ) . '</div>';
							}

							switch ( $swatches_type ) {
								case 'color':
									$swatches_color = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_color', true );
									if ( $swatches_color ) {
										$swatch_html = '<span style="background-color: ' . esc_attr( $swatches_color ) . '" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $hiterm->name . ' Color Swatch' ) . '" title="' . esc_attr( $hiterm->name ) . '"></span> ';
									}
									break;

								case 'image':
									$swatches_image = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_image', true );
									if ( $swatches_image ) {
										$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $hiterm->name . ' Image Swatch' ) . '" title="' . esc_attr( $hiterm->name ) . '"></span> ';
									}
									break;

								case 'product-cat-image':
									if ( class_exists( 'WooCommerce' ) ) {
										$thumbnail_id   = get_term_meta( $hiterm->term_id, 'thumbnail_id', true );
										$swatches_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
										if ( $swatches_image ) {
											$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $hiterm->name . ' Image Swatch' ) . '" title="' . esc_attr( $hiterm->name ) . '"></span> ';
										}
									}
									break;

								case 'button':
									$swatches_button_text = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_button_text', true );
									if ( $swatches_button_text ) {
										$swatch_html = '<span class="bpfwe-swatch bpfwe-swatch-button" role="button" aria-label="' . esc_attr( $hiterm->name . ' Button Swatch' ) . '" title="' . esc_attr( $swatches_button_text ) . '">' . esc_html( $swatches_button_text ) . '</span> ';
									}
									break;

								default:
									$swatch_html = '';
									break;
							}

							echo '
							<li class="parent-term">
								' . wp_kses_post( $separator_html ) . '
								<label for="' . esc_attr( $hiterm->slug ) . '-' . esc_attr( $widget_id ) . '">
								<input type="checkbox" id="' . esc_attr( $hiterm->slug ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['filter_by'] ) . '" data-taxonomy="' . esc_attr( $hiterm->taxonomy ) . '" value="' . esc_attr( $hiterm->term_id ) . '" />
								<span>' . wp_kses_post( $swatch_html ) . '<span class="label-text">' . esc_html( $hiterm->name ) . esc_html( $show_counter ) . '</span></span>
								<span class="low-group-trigger" role="button" aria-expanded="false">+</span>
								</label>
							</li>
							';

							if ( 'yes' === $item['show_hierarchy'] ) {
								$terms_stack            = array();
								$lowterms_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $hiterm->term_id;
								$lowterms               = get_transient( $lowterms_transient_key );

								if ( false === $lowterms || current_user_can( 'edit_posts' ) ) {
									$args     = array(
										'taxonomy'   => $item['filter_by'],
										'orderby'    => $item['sort_terms'],
										'parent'     => $hiterm->term_id,
										'hide_empty' => $display_empty,
										'fields'     => 'all',
										'update_meta_cache' => false,
									);
									$lowterms = get_terms( $args );

									if ( $transient_duration > 0 && ! current_user_can( 'edit_posts' ) ) {
										set_transient( $lowterms_transient_key, $lowterms, $transient_duration );
									}
								}

								if ( $lowterms ) {
									foreach ( $lowterms as $lowterm ) {
										$terms_stack[] = array(
											'term'  => $lowterm,
											'depth' => 1,
										);
									}

									$output   = 'yes' === $item['toggle_child'] ? '<span class="low-terms-group"><ul class="child-terms">' : '<ul class="child-terms">';
									$open_uls = 1;

									while ( ! empty( $terms_stack ) ) {
										$current = array_pop( $terms_stack );
										$term    = $current['term'];
										$depth   = $current['depth'];

										$next_depth = ! empty( $terms_stack ) ? $terms_stack[ count( $terms_stack ) - 1 ]['depth'] : 0;

										while ( $open_uls > $depth ) {
											$output .= '</ul>' . ( 'yes' === $item['toggle_child'] ? '</span>' : '' ) . '</li>';
											--$open_uls;
										}

										$show_counter   = 'yes' === $item['show_counter'] ? ' (' . intval( $term->count ) . ')' : '';
										$swatches_type  = 'yes' === $item['display_swatch'] ? get_term_meta( $term->term_id, 'bpfwe_swatches_type', true ) : '';
										$group_text     = get_term_meta( $term->term_id, 'bpfwe_swatches_group_text', true );
										$swatch_html    = '';
										$separator_html = '';

										if ( $group_text && 'yes' === $item['display_swatch'] ) {
											$separator_html = '<div class="bpfwe-group-separator" role="separator" aria-label="' . esc_attr( $group_text . ' Group Separator' ) . '">' . esc_html( $group_text ) . '</div>';
										}

										switch ( $swatches_type ) {
											case 'color':
												$swatches_color = get_term_meta( $term->term_id, 'bpfwe_swatches_color', true );
												if ( $swatches_color ) {
													$swatch_html = '<span style="background-color: ' . esc_attr( $swatches_color ) . '" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $term->name . ' Color Swatch' ) . '" title="' . esc_attr( $term->name ) . '"></span> ';
												}
												break;

											case 'image':
												$swatches_image = get_term_meta( $term->term_id, 'bpfwe_swatches_image', true );
												if ( $swatches_image ) {
													$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $term->name . ' Image Swatch' ) . '" title="' . esc_attr( $term->name ) . '"></span> ';
												}
												break;

											case 'product-cat-image':
												if ( class_exists( 'WooCommerce' ) ) {
													$thumbnail_id   = get_term_meta( $term->term_id, 'thumbnail_id', true );
													$swatches_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
													if ( $swatches_image ) {
														$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $term->name . ' Image Swatch' ) . '" title="' . esc_attr( $term->name ) . '"></span> ';
													}
												}
												break;

											case 'button':
												$swatches_button_text = get_term_meta( $term->term_id, 'bpfwe_swatches_button_text', true );
												if ( $swatches_button_text ) {
													$swatch_html = '<span class="bpfwe-swatch bpfwe-swatch-button" role="button" aria-label="' . esc_attr( $term->name . ' Button Swatch' ) . '" title="' . esc_attr( $swatches_button_text ) . '">' . esc_html( $swatches_button_text ) . '</span> ';
												}
												break;

											default:
												$swatch_html = '';
												break;
										}

										$child_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $term->term_id;
										$child_terms         = get_transient( $child_transient_key );

										if ( false === $child_terms || current_user_can( 'edit_posts' ) ) {
											$args        = array(
												'taxonomy' => $item['filter_by'],
												'orderby'  => $item['sort_terms'],
												'parent'   => $term->term_id,
												'hide_empty' => $display_empty,
												'fields'   => 'all',
												'update_meta_cache' => false,
											);
											$child_terms = get_terms( $args );

											if ( $transient_duration > 0 && ! current_user_can( 'edit_posts' ) ) {
												set_transient( $child_transient_key, $child_terms, $transient_duration );
											}
										}

										$output .= '
											<li class="child-term depth-' . $depth . '">
												' . wp_kses_post( $separator_html ) . '
												<label for="' . esc_attr( $term->slug ) . '-' . esc_attr( $widget_id ) . '">
													<input type="checkbox" 
														id="' . esc_attr( $term->slug ) . '-' . esc_attr( $widget_id ) . '" 
														class="bpfwe-filter-item" 
														name="' . esc_attr( $item['filter_by'] ) . '" 
														data-taxonomy="' . esc_attr( $term->taxonomy ) . '" 
														value="' . esc_attr( $term->term_id ) . '" />
													<span>' . $swatch_html . '<span class="label-text">' . esc_html( $term->name ) . esc_html( $show_counter ) . '</span></span>
													<span class="low-group-trigger" role="button" aria-expanded="false">+</span>
												</label>';

										if ( ! empty( $child_terms ) ) {
											$output .= 'yes' === $item['toggle_child'] ? '<span class="low-terms-group"><ul class="child-terms depth-' . $depth . '">' : '<ul class="child-terms depth-' . $depth . '">';
											++$open_uls;
											foreach ( array_reverse( $child_terms ) as $child_term ) {
												$terms_stack[] = array(
													'term' => $child_term,
													'depth' => $depth + 1,
												);
											}
										} else {
											$output .= '</li>';
										}
									}

									while ( $open_uls > 1 ) {
										$output .= '</ul>' . ( 'yes' === $item['toggle_child'] ? '</span>' : '' ) . '</li>';
										--$open_uls;
									}

									$output .= 'yes' === $item['toggle_child'] ? '</ul></span>' : '</ul>';

									echo wp_kses(
										$output,
										array(
											'ul'    => array( 'class' => array() ),
											'li'    => array( 'class' => array() ),
											'label' => array( 'for' => array() ),
											'input' => array(
												'type'  => array(),
												'id'    => array(),
												'class' => array(),
												'name'  => array(),
												'data-taxonomy' => array(),
												'value' => array(),
											),
											'span'  => array(
												'class' => array(),
												'style' => array(),
												'role'  => array(),
												'label' => array(),
												'title' => array(),
											),
											'div'   => array(
												'class' => array(),
												'style' => array(),
												'role'  => array(),
												'label' => array(),
												'title' => array(),
											),
										)
									);
								}
							}

							++$term_index;
						}
						echo ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) ? '<li class="more"><span class="label-more">' . esc_html__( 'More...', 'better-post-filter-widgets-for-elementor' ) . '</span><span class="label-less">' . esc_html__( 'Less...', 'better-post-filter-widgets-for-elementor' ) . '</span></li>' : '';
						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'radio' === $item['filter_style'] || 'radio' === $item['filter_style_cf'] ) {
						$term_index = 0;
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['filter_by'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-taxonomy-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $item['hide_label_swatch'] ) . ' ' . esc_attr( $item['hide_input_swatch'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';

						foreach ( $hiterms as $key => $hiterm ) {
							$show_counter   = 'yes' === $item['show_counter'] ? ' (' . intval( $hiterm->count ) . ')' : '';
							$swatches_type  = 'yes' === $item['display_swatch'] ? get_term_meta( $hiterm->term_id, 'bpfwe_swatches_type', true ) : '';
							$group_text     = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_group_text', true );
							$swatch_html    = '';
							$separator_html = '';

							if ( $group_text && 'yes' === $item['display_swatch'] ) {
								$separator_html = '<div class="bpfwe-group-separator" role="separator" aria-label="' . esc_attr( $group_text . ' Group Separator' ) . '">' . esc_html( $group_text ) . '</div>';
							}

							switch ( $swatches_type ) {
								case 'color':
									$swatches_color = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_color', true );
									if ( $swatches_color ) {
										$swatch_html = '<span style="background-color: ' . esc_attr( $swatches_color ) . '" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $hiterm->name . ' Color Swatch' ) . '" title="' . esc_attr( $hiterm->name ) . '"></span> ';
									}
									break;

								case 'image':
									$swatches_image = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_image', true );
									if ( $swatches_image ) {
										$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $hiterm->name . ' Image Swatch' ) . '" title="' . esc_attr( $hiterm->name ) . '"></span> ';
									}
									break;

								case 'product-cat-image':
									if ( class_exists( 'WooCommerce' ) ) {
										$thumbnail_id   = get_term_meta( $hiterm->term_id, 'thumbnail_id', true );
										$swatches_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
										if ( $swatches_image ) {
											$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $hiterm->name . ' Image Swatch' ) . '" title="' . esc_attr( $hiterm->name ) . '"></span> ';
										}
									}
									break;

								case 'button':
									$swatches_button_text = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_button_text', true );
									if ( $swatches_button_text ) {
										$swatch_html = '<span class="bpfwe-swatch bpfwe-swatch-button" role="button" aria-label="' . esc_attr( $hiterm->name . ' Button Swatch' ) . '" title="' . esc_attr( $swatches_button_text ) . '">' . esc_html( $swatches_button_text ) . '</span> ';
									}
									break;

								default:
									$swatch_html = '';
									break;
							}

							echo '
							<li class="parent-term">
								' . wp_kses_post( $separator_html ) . '
								<label for="' . esc_attr( $hiterm->slug ) . '-' . esc_attr( $widget_id ) . '">
								<input type="radio" id="' . esc_attr( $hiterm->slug ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['filter_by'] ) . '" data-taxonomy="' . esc_attr( $hiterm->taxonomy ) . '" value="' . esc_attr( $hiterm->term_id ) . '" />
								<span>' . wp_kses_post( $swatch_html ) . '<span class="label-text">' . esc_html( $hiterm->name ) . esc_html( $show_counter ) . '</span></span>
								<span class="low-group-trigger" role="button" aria-expanded="false">+</span>
								</label>
							</li>
							';

							if ( 'yes' === $item['show_hierarchy'] ) {
								$terms_stack            = array();
								$lowterms_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $hiterm->term_id;
								$lowterms               = get_transient( $lowterms_transient_key );

								if ( false === $lowterms || current_user_can( 'edit_posts' ) ) {
									$args     = array(
										'taxonomy'   => $item['filter_by'],
										'orderby'    => $item['sort_terms'],
										'parent'     => $hiterm->term_id,
										'hide_empty' => $display_empty,
										'fields'     => 'all',
										'update_meta_cache' => false,
									);
									$lowterms = get_terms( $args );

									if ( $transient_duration > 0 && ! current_user_can( 'edit_posts' ) ) {
										set_transient( $lowterms_transient_key, $lowterms, $transient_duration );
									}
								}

								if ( $lowterms ) {
									foreach ( $lowterms as $lowterm ) {
										$terms_stack[] = array(
											'term'  => $lowterm,
											'depth' => 1,
										);
									}

									$output   = 'yes' === $item['toggle_child'] ? '<span class="low-terms-group"><ul class="child-terms">' : '<ul class="child-terms">';
									$open_uls = 1;

									while ( ! empty( $terms_stack ) ) {
										$current = array_pop( $terms_stack );
										$term    = $current['term'];
										$depth   = $current['depth'];

										$next_depth = ! empty( $terms_stack ) ? $terms_stack[ count( $terms_stack ) - 1 ]['depth'] : 0;

										while ( $open_uls > $depth ) {
											$output .= '</ul>' . ( 'yes' === $item['toggle_child'] ? '</span>' : '' ) . '</li>';
											--$open_uls;
										}

										$show_counter   = 'yes' === $item['show_counter'] ? ' (' . intval( $term->count ) . ')' : '';
										$swatches_type  = 'yes' === $item['display_swatch'] ? get_term_meta( $term->term_id, 'bpfwe_swatches_type', true ) : '';
										$group_text     = get_term_meta( $term->term_id, 'bpfwe_swatches_group_text', true );
										$swatch_html    = '';
										$separator_html = '';

										if ( $group_text && 'yes' === $item['display_swatch'] ) {
											$separator_html = '<div class="bpfwe-group-separator" role="separator" aria-label="' . esc_attr( $group_text . ' Group Separator' ) . '">' . esc_html( $group_text ) . '</div>';
										}

										switch ( $swatches_type ) {
											case 'color':
												$swatches_color = get_term_meta( $term->term_id, 'bpfwe_swatches_color', true );
												if ( $swatches_color ) {
													$swatch_html = '<span style="background-color: ' . esc_attr( $swatches_color ) . '" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $term->name . ' Color Swatch' ) . '" title="' . esc_attr( $term->name ) . '"></span> ';
												}
												break;

											case 'image':
												$swatches_image = get_term_meta( $term->term_id, 'bpfwe_swatches_image', true );
												if ( $swatches_image ) {
													$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $term->name . ' Image Swatch' ) . '" title="' . esc_attr( $term->name ) . '"></span> ';
												}
												break;

											case 'product-cat-image':
												if ( class_exists( 'WooCommerce' ) ) {
													$thumbnail_id   = get_term_meta( $term->term_id, 'thumbnail_id', true );
													$swatches_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
													if ( $swatches_image ) {
														$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="' . esc_attr( $term->name . ' Image Swatch' ) . '" title="' . esc_attr( $term->name ) . '"></span> ';
													}
												}
												break;

											case 'button':
												$swatches_button_text = get_term_meta( $term->term_id, 'bpfwe_swatches_button_text', true );
												if ( $swatches_button_text ) {
													$swatch_html = '<span class="bpfwe-swatch bpfwe-swatch-button" role="button" aria-label="' . esc_attr( $term->name . ' Button Swatch' ) . '" title="' . esc_attr( $swatches_button_text ) . '">' . esc_html( $swatches_button_text ) . '</span> ';
												}
												break;

											default:
												$swatch_html = '';
												break;
										}

										$child_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $term->term_id;
										$child_terms         = get_transient( $child_transient_key );

										if ( false === $child_terms || current_user_can( 'edit_posts' ) ) {
											$args        = array(
												'taxonomy' => $item['filter_by'],
												'orderby'  => $item['sort_terms'],
												'parent'   => $term->term_id,
												'hide_empty' => $display_empty,
												'fields'   => 'all',
												'update_meta_cache' => false,
											);
											$child_terms = get_terms( $args );

											if ( $transient_duration > 0 && ! current_user_can( 'edit_posts' ) ) {
												set_transient( $child_transient_key, $child_terms, $transient_duration );
											}
										}

										$output .= '
											<li class="child-term depth-' . $depth . '">
												' . wp_kses_post( $separator_html ) . '
												<label for="' . esc_attr( $term->slug ) . '-' . esc_attr( $widget_id ) . '">
													<input type="radio" 
														id="' . esc_attr( $term->slug ) . '-' . esc_attr( $widget_id ) . '" 
														class="bpfwe-filter-item" 
														name="' . esc_attr( $item['filter_by'] ) . '" 
														data-taxonomy="' . esc_attr( $term->taxonomy ) . '" 
														value="' . esc_attr( $term->term_id ) . '" />
													<span>' . $swatch_html . '<span class="label-text">' . esc_html( $term->name ) . esc_html( $show_counter ) . '</span></span>
													<span class="low-group-trigger" role="button" aria-expanded="false">+</span>
												</label>';

										if ( ! empty( $child_terms ) ) {
											$output .= 'yes' === $item['toggle_child'] ? '<span class="low-terms-group"><ul class="child-terms depth-' . $depth . '">' : '<ul class="child-terms depth-' . $depth . '">';
											++$open_uls;
											foreach ( array_reverse( $child_terms ) as $child_term ) {
												$terms_stack[] = array(
													'term' => $child_term,
													'depth' => $depth + 1,
												);
											}
										} else {
											$output .= '</li>';
										}
									}

									while ( $open_uls > 1 ) {
										$output .= '</ul>' . ( 'yes' === $item['toggle_child'] ? '</span>' : '' ) . '</li>';
										--$open_uls;
									}

									$output .= 'yes' === $item['toggle_child'] ? '</ul></span>' : '</ul>';

									echo wp_kses(
										$output,
										array(
											'ul'    => array( 'class' => array() ),
											'li'    => array( 'class' => array() ),
											'label' => array( 'for' => array() ),
											'input' => array(
												'type'  => array(),
												'id'    => array(),
												'class' => array(),
												'name'  => array(),
												'data-taxonomy' => array(),
												'value' => array(),
											),
											'span'  => array(
												'class' => array(),
												'style' => array(),
												'role'  => array(),
												'label' => array(),
												'title' => array(),
											),
											'div'   => array(
												'class' => array(),
												'style' => array(),
												'role'  => array(),
												'label' => array(),
												'title' => array(),
											),
										)
									);
								}
							}

							++$term_index;
						}
						echo ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) ? '<li class="more"><span class="label-more">' . esc_html__( 'More...', 'better-post-filter-widgets-for-elementor' ) . '</span><span class="label-less">' . esc_html__( 'Less...', 'better-post-filter-widgets-for-elementor' ) . '</span></li>' : '';
						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'list' === $item['filter_style'] || 'list' === $item['filter_style_cf'] ) {
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['filter_by'] ) . '">
						<div class="filter-title">' . esc_attr( $item['filter_title'] ) . '</div>
						<div class="bpfwe-taxonomy-wrapper" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter">
						';
						foreach ( $hiterms as $key => $hiterm ) {
							$show_counter = 'yes' === $item['show_counter'] ? ' (' . intval( $hiterm->count ) . ')' : '';
							echo '
							<li class="list-style">
							<label for="' . esc_attr( $hiterm->slug ) . '-' . esc_attr( $widget_id ) . '">
							<input type="checkbox" id="' . esc_attr( $hiterm->slug ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['filter_by'] ) . '" data-taxonomy="' . esc_attr( $hiterm->taxonomy ) . '" value="' . esc_attr( $hiterm->term_id ) . '" />
							<span>' . esc_html( $hiterm->name ) . esc_html( $show_counter ) . '</span>
							</label>
							</li>
							';
						}
						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'dropdown' === $item['filter_style'] || 'select2' === $item['filter_style'] || 'dropdown' === $item['filter_style_cf'] || 'select2' === $item['filter_style_cf'] ) {
						$multi_select2_cf = $item['multi_select2_cf'];
						$multi_select2    = $item['multi_select2'];

						$select2_class = '';
						$option_text   = ! empty( $item['option_text'] ) ? $item['option_text'] : esc_html__( 'Choose an option', 'better-post-filter-widgets-for-elementor' );
						$default_val   = '<option value="">' . esc_html( $option_text ) . '</option>';

						if ( 'select2' === $item['filter_style'] || 'select2' === $item['filter_style_cf'] ) {
							$select2_class = 'bpfwe-select2';

							if ( 'yes' === $multi_select2_cf || 'yes' === $multi_select2 ) {
								$select2_class = 'bpfwe-multi-select2';
								$default_val   = '';
							}
						}

						echo '
						<div class="flex-wrapper ' . esc_attr( $item['filter_by'] ) . '">
						<div class="filter-title">' . esc_attr( $item['filter_title'] ) . '</div>
						<div class="bpfwe-taxonomy-wrapper ' . esc_attr( $select2_class ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<select id="' . esc_attr( $item['filter_by'] ) . '-' . esc_attr( $widget_id ) . '">' . wp_kses( $default_val, array( 'option' => array( 'value' => array() ) ) );

						if ( 'yes' === $item['show_hierarchy'] ) {
							$terms_stack = array();
							foreach ( $hiterms as $hiterm ) {
								$terms_stack[] = array(
									'term'  => $hiterm,
									'depth' => 0,
								);
							}

							while ( ! empty( $terms_stack ) ) {
								$current = array_pop( $terms_stack );
								$term    = $current['term'];
								$depth   = $current['depth'];

								$prefix       = str_repeat( '— ', $depth );
								$show_counter = ( 'yes' === $item['show_counter'] ) ? ' (' . intval( $term->count ) . ')' : '';

								echo '<option data-bold="true" data-category="' . esc_attr( $term->term_id ) . '" data-taxonomy="' . esc_attr( $term->taxonomy ) . '" value="' . esc_attr( $term->term_id ) . '">' . esc_html( $prefix . $term->name ) . esc_html( $show_counter ) . '</option>';

								$args        = array(
									'taxonomy'          => $item['filter_by'],
									'orderby'           => $item['sort_terms'],
									'parent'            => $term->term_id,
									'hide_empty'        => $display_empty,
									'fields'            => 'all',
									'update_meta_cache' => false,
								);
								$child_terms = get_terms( $args );

								if ( ! empty( $child_terms ) ) {
									foreach ( array_reverse( $child_terms ) as $child_term ) {
										$terms_stack[] = array(
											'term'  => $child_term,
											'depth' => $depth + 1,
										);
									}
								}
							}
						} else {
							foreach ( $hiterms as $hiterm ) {
								$show_counter = ( 'yes' === $item['show_counter'] ) ? ' (' . intval( $hiterm->count ) . ')' : '';
								echo '<option data-category="' . esc_attr( $hiterm->term_id ) . '" data-taxonomy="' . esc_attr( $hiterm->taxonomy ) . '" value="' . esc_attr( $hiterm->term_id ) . '">' . esc_html( $hiterm->name ) . esc_html( $show_counter ) . '</option>';
							}
						}

						echo '
						</select>
						</div>
						</div>
						';
					}
				}

				if ( 'Custom Field' === $item['select_filter'] ) {

					if ( 'input' === $item['filter_style_cf'] ) {
						$placeholder = esc_html( $item['text_input_placeholder'] ) ? esc_html( $item['text_input_placeholder'] ) : '';
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-custom-field-wrapper" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<input type="text" class="input-text" id="input-text-' . esc_attr( $item['meta_key'] ) . '-' . esc_attr( $widget_id ) . '" name="post_meta" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" placeholder="' . esc_html( $placeholder ) . '">
						</div>
						</div>
						';
					}

					if ( $item['meta_key'] ) {
						// Check if transient exists.
						$meta_terms_transient_key = 'filter_widget_meta_terms_' . $item['meta_key'];
						$terms                    = get_transient( $meta_terms_transient_key );

						// Bypass transient for users with editing capabilities.
						if ( false === $terms || current_user_can( 'edit_posts' ) ) {
							$all_posts_args = array(
								'posts_per_page'         => -1,
								'post_type'              => $settings['filter_post_type'],
								'orderby'                => $item['sort_terms'],
								'no_found_rows'          => true,
								'fields'                 => 'ids',
								'meta_key'               => $item['meta_key'],
								'update_post_meta_cache' => false,
								'update_post_term_cache' => false,
							);

							if ( $settings['dynamic_filtering'] ) {
								$queried_object = get_queried_object();
								$archive_type   = '';

								if ( $queried_object instanceof WP_User ) {
									$archive_type = 'author';
								} elseif ( $queried_object instanceof WP_Date_Query ) {
									$archive_type = 'date';
								} elseif ( $queried_object instanceof WP_Term ) {
									$archive_type = 'taxonomy';
								} elseif ( $queried_object instanceof WP_Post_Type ) {
									$archive_type = 'post_type';
								}

								// Modify the query for author archive.
								if ( 'author' === $archive_type && $queried_object instanceof WP_User ) {
									$all_posts_args['author'] = $queried_object->ID;
								}

								// Modify the query for taxonomy archive.
								if ( 'taxonomy' === $archive_type && $queried_object instanceof WP_Term ) {
									$all_posts_args['tax_query'] = array(
										array(
											'taxonomy' => $queried_object->taxonomy,
											'field'    => 'term_id',
											'terms'    => $queried_object->term_id,
										),
									);
								}
							}

							$all_posts = new WP_Query( $all_posts_args );

							if ( $all_posts->have_posts() ) {
								$terms_data = array();

								while ( $all_posts->have_posts() ) {
									$all_posts->the_post();
									$term                = get_post_meta( get_the_ID(), $item['meta_key'], true );
									$terms_data[ $term ] = true;
								}

								$terms_data = array_keys( $terms_data );
								wp_reset_postdata();

								if ( $transient_duration > 0 && ! current_user_can( 'edit_posts' ) ) {
									set_transient( $meta_terms_transient_key, $terms_data, $transient_duration );
								}

								$terms = $terms_data;
							}
						}
					}

					if ( 'checkboxes' === $item['filter_style'] || 'checkboxes' === $item['filter_style_cf'] ) {
						$term_index = 0;
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';
						foreach ( $terms as $result ) {
							$toggleable_class = ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) ? 'toggleable' : '';
							$show_counter     = 'yes' === $item['show_counter'] ? ' (' . intval( $result->count ) . ')' : '';

							echo '
							<li class="' . esc_attr( $toggleable_class ) . '">
							<label for="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '">
							<input type="checkbox" id="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $result ) . esc_html( $show_counter ) . '" />
							<span>' . esc_html( $result ) . '</span>
							</label>
							</li>
							';

							++$term_index;
						}
						echo ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) ? '<li class="more"><span class="label-more">' . esc_html__( 'More...', 'better-post-filter-widgets-for-elementor' ) . '</span><span class="label-less">' . esc_html__( 'Less...', 'better-post-filter-widgets-for-elementor' ) . '</span></li>' : '';
						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'radio' === $item['filter_style'] || 'radio' === $item['filter_style_cf'] ) {
						$term_index = 0;
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';
						foreach ( $terms as $result ) {
							$toggleable_class = ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) ? 'toggleable' : '';
							$show_counter     = 'yes' === $item['show_counter'] ? ' (' . intval( $result->count ) . ')' : '';

							echo '
							<li class="' . esc_attr( $toggleable_class ) . '">
							<label for="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '">
							<input type="radio" id="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $result ) . esc_html( $show_counter ) . '" />
							<span>' . esc_html( $result ) . '</span>
							</label>
							</li>
							';

							++$term_index;
						}
						echo ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) ? '<li class="more"><span class="label-more">' . esc_html__( 'More...', 'better-post-filter-widgets-for-elementor' ) . '</span><span class="label-less">' . esc_html__( 'Less...', 'better-post-filter-widgets-for-elementor' ) . '</span></li>' : '';
						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'list' === $item['filter_style'] || 'list' === $item['filter_style_cf'] ) {
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';
						foreach ( $terms as $result ) {
							$show_counter = 'yes' === $item['show_counter'] ? ' (' . intval( $result->count ) . ')' : '';
							echo '
							<li class="list-style">
							<label for="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '">
							<input type="checkbox" id="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $result ) . esc_html( $show_counter ) . '" />
							<span>' . esc_html( $result ) . '</span>
							</label>
							</li>
							';
						}
						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'dropdown' === $item['filter_style'] || 'select2' === $item['filter_style'] || 'dropdown' === $item['filter_style_cf'] || 'select2' === $item['filter_style_cf'] ) {
						$multi_select2_cf = $item['multi_select2_cf'];
						$multi_select2    = $item['multi_select2'];

						$select2_class = '';
						$option_text   = ! empty( $item['option_text_cf'] ) ? $item['option_text_cf'] : esc_html__( 'Choose an option', 'better-post-filter-widgets-for-elementor' );
						$default_val   = '<option value="">' . esc_html( $option_text ) . '</option>';

						if ( 'select2' === $item['filter_style'] || 'select2' === $item['filter_style_cf'] ) {
							$select2_class = 'bpfwe-select2';

							if ( 'yes' === $multi_select2_cf || 'yes' === $multi_select2 ) {
								$select2_class = 'bpfwe-multi-select2';
								$default_val   = '';
							}
						}

						echo '
						<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-custom-field-wrapper ' . esc_attr( $select2_class ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<select id="' . esc_attr( $item['meta_key'] ) . '-' . esc_attr( $widget_id ) . '">' . wp_kses( $default_val, array( 'option' => array( 'value' => array() ) ) );
						foreach ( $terms as $result ) {
							$show_counter = 'yes' === $item['show_counter'] ? ' (' . intval( $result->count ) . ')' : '';
							echo '<option data-category="' . esc_attr( $result ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $result ) . '">' . esc_html( $result ) . esc_html( $show_counter ) . '</option>';
						}
						echo '
						</select>
						</div>
						</div>
						';
					}
				}

				if ( 'Numeric' === $item['select_filter'] ) {

					$terms = array();

					if ( ! empty( $item['meta_key'] ) ) {
						$numeric_transient_key = 'filter_widget_numeric_' . $item['meta_key'];
						$all_posts_transient   = get_transient( $numeric_transient_key );

						// Bypass transient for users with editing capabilities or if transient doesn't exist.
						if ( false === $all_posts_transient || current_user_can( 'edit_posts' ) ) {
							$all_posts_args = array(
								'posts_per_page'         => -1,
								'post_type'              => $settings['filter_post_type'],
								'fields'                 => 'ids',
								'no_found_rows'          => true,
								'update_post_meta_cache' => false,
								'update_post_term_cache' => false,
							);

							if ( $settings['dynamic_filtering'] ) {
								$queried_object = get_queried_object();
								$archive_type   = '';

								if ( $queried_object instanceof WP_User ) {
									$archive_type = 'author';
								} elseif ( $queried_object instanceof WP_Date_Query ) {
									$archive_type = 'date';
								} elseif ( $queried_object instanceof WP_Term ) {
									$archive_type = 'taxonomy';
								} elseif ( $queried_object instanceof WP_Post_Type ) {
									$archive_type = 'post_type';
								}

								// Modify the query for author archive.
								if ( 'author' === $archive_type && $queried_object instanceof WP_User ) {
									$all_posts_args['author'] = $queried_object->ID;
								}

								// Modify the query for taxonomy archive.
								if ( 'taxonomy' === $archive_type && $queried_object instanceof WP_Term ) {
									$all_posts_args['tax_query'] = array(
										array(
											'taxonomy' => $queried_object->taxonomy,
											'field'    => 'term_id',
											'terms'    => $queried_object->term_id,
										),
									);
								}
							}

							$all_posts_query = new WP_Query( $all_posts_args );

							$all_posts_transient = $all_posts_query->posts;

							if ( $transient_duration > 0 && ! current_user_can( 'edit_posts' ) ) {
								set_transient( $numeric_transient_key, $all_posts_transient, $transient_duration );
							}
						}

						$terms = array(); // Initialize $terms array.

						foreach ( $all_posts_transient as $post_id ) {
							$term_values = get_post_custom_values( $item['meta_key'], $post_id );

							if ( ! empty( $term_values ) ) {
								foreach ( $term_values as $term ) {
									$terms[ $term ] = true; // use term as key.
								}
							}
						}
					}

					$terms = array_keys( $terms );

					if ( 'range' === $item['filter_style_numeric'] ) {
						if ( empty( $terms ) || ! is_array( $terms ) ) {
							$min_value = 0;
							$max_value = 0;
						} else {
							$min_value = floatval( min( $terms ) );
							$max_value = floatval( max( $terms ) );
						}

						echo '
						<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
							<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
							<div class="bpfwe-numeric-wrapper" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
								<span class="field-wrapper"><span class="before">' . esc_html( $item['insert_before_field'] ) . '</span><input type="number" class="bpfwe-filter-range-' . esc_attr( $index ) . '" name="min_price" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" data-base-value="' . esc_attr( $min_value ) . '" step="1" min="' . esc_attr( $min_value ) . '" max="' . esc_attr( $max_value ) . '" value="' . esc_attr( $min_value ) . '"></span>
								<span class="field-wrapper"><span class="before">' . esc_html( $item['insert_before_field'] ) . '</span><input type="number" class="bpfwe-filter-range-' . esc_attr( $index ) . '" name="max_price" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" data-base-value="' . esc_attr( $max_value ) . '" step="1" min="' . esc_attr( $min_value ) . '" max="' . esc_attr( $max_value ) . '" value="' . esc_attr( $max_value ) . '"></span>
							</div>
						</div>
						';
					}

					if ( 'checkboxes' === $item['filter_style_numeric'] ) {
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle_numeric'] ) . '">
						';
						foreach ( $terms as $result ) {
							echo '
							<li>
							<label for="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '">
							<input type="checkbox" id="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $result ) . '" />
							<span>' . esc_html( $result ) . '</span>
							</label>
							</li>
							';
						}
						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'radio' === $item['filter_style_numeric'] ) {
						echo '<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle_numeric'] ) . '">
						';
						foreach ( $terms as $result ) {
							echo '
							<li>
							<label for="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '">
							<input type="radio" id="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $result ) . '" />
							<span>' . esc_html( $result ) . '</span>
							</label>
							</li>
							';
						}
						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'list' === $item['filter_style_numeric'] ) {
						echo '
						<div class="flex-wrapper ' . esc_attr( $item['meta_key'] ) . '">
						<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle_numeric'] ) . '">
						';
						foreach ( $terms as $result ) {
							echo '
							<li class="list-style">
							<label for="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '">
							<input type="checkbox" id="' . esc_attr( $result ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $result ) . '" />
							<span>' . esc_html( $result ) . '</span>
							</label>
							</li>
							';
						}
						echo '
						</ul>
						</div>
						</div>
						';
					}
				}
			}
		}
			$submit_text = ! empty( $settings['submit_text'] ) ? $settings['submit_text'] : esc_html__( 'Submit', 'better-post-filter-widgets-for-elementor' );
			$reset_text  = ! empty( $settings['reset_text'] ) ? $settings['reset_text'] : esc_html__( 'Reset', 'better-post-filter-widgets-for-elementor' );

		if ( $settings['use_submit'] ) {
			echo '<button type="submit" value="submit" class="submit-form">' . esc_html( $submit_text ) . '</button>';
		}

		if ( $settings['show_reset'] ) {
			echo '<button type="reset" class="reset-form" value="reset" onclick="this.form.reset();">' . esc_html( $reset_text ) . '</button>';
		}

			echo '</form></div>';
	}
}

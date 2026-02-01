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
				'options'            => array_merge( [ 'targeted_widget' => esc_html__( 'Targeted Widget', 'better-post-filter-widgets-for-elementor' ) ], BPFWE_Helper::bpfwe_get_post_types() ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'filter_post_type_source_warning',
			[
				'type'        => \Elementor\Controls_Manager::NOTICE,
				'notice_type' => 'warning',
				'dismissible' => true,
				'content'     => esc_html__( 'Using the post type from the targeted post widget may produce inconsistent results. If unavailable, "Any" will be used as fallback.', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'filter_post_type' => 'targeted_widget',
				],
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
				'label'       => esc_html__( 'Group Title', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'New Filter', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'New Filter', 'better-post-filter-widgets-for-elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'filter_toggle',
			[
				'label'        => esc_html__( 'Enable Toggle Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'filter_title!' => '',
				],
			]
		);

		$repeater->add_control(
			'filter_toggle_initial_state',
			[
				'label'        => __( 'Start Expanded', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'filter_toggle' => 'yes',
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
					'Relational'   => esc_html__( 'Relational Field', 'better-post-filter-widgets-for-elementor' ),
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
					'select_filter' => [ 'Custom Field', 'Numeric', 'Relational' ],
				],
			]
		);

		$repeater->add_control(
			'relational_field_type',
			[
				'label'       => esc_html__( 'Relation Type', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => '',
				'options'     => [
					''     => esc_html__( 'Auto Detect', 'better-post-filter-widgets-for-elementor' ),
					'post' => esc_html__( 'Post', 'better-post-filter-widgets-for-elementor' ),
					'user' => esc_html__( 'User', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition'   => [
					'select_filter' => 'Relational',
				],
			]
		);

		$repeater->add_control(
			'format_type',
			[
				'label'     => esc_html__( 'Value Formatting', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none'           => esc_html__( 'None', 'better-post-filter-widgets-for-elementor' ),
					'date'           => esc_html__( 'Date/Time', 'better-post-filter-widgets-for-elementor' ),
					'number'         => esc_html__( 'Number', 'better-post-filter-widgets-for-elementor' ),
					'text'           => esc_html__( 'Text', 'better-post-filter-widgets-for-elementor' ),
					'custom_pattern' => esc_html__( 'Custom Pattern', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'select_filter' => [ 'Custom Field', 'Numeric' ],
				],
			]
		);

		$repeater->add_control(
			'date_format',
			[
				'label'     => esc_html__( 'Date Format', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'Y-m-d',
				'options'   => [
					'Y-m-d'        => esc_html__( 'Year-Month-Day', 'better-post-filter-widgets-for-elementor' ),
					'd/m/Y'        => esc_html__( 'Day/Month/Year', 'better-post-filter-widgets-for-elementor' ),
					'l, F j'       => esc_html__( 'Weekday, Month Day', 'better-post-filter-widgets-for-elementor' ),
					'F Y'          => esc_html__( 'Month Year', 'better-post-filter-widgets-for-elementor' ),
					'Y-m-d H:i'    => esc_html__( 'Year-Month-Day, 24H', 'better-post-filter-widgets-for-elementor' ),
					'd/m/Y H:i'    => esc_html__( 'Day/Month/Year, 24H', 'better-post-filter-widgets-for-elementor' ),
					'l, F j H:i'   => esc_html__( 'Weekday, Month Day, 24H', 'better-post-filter-widgets-for-elementor' ),
					'F j, Y g:i A' => esc_html__( 'Month Day, Year, 12H', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'select_filter' => [ 'Custom Field', 'Numeric' ],
					'format_type'   => 'date',
				],
			]
		);

		$repeater->add_control(
			'number_decimals',
			[
				'label'     => esc_html__( 'Decimals', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 6,
				'default'   => 0,
				'condition' => [
					'select_filter' => [ 'Custom Field', 'Numeric' ],
					'format_type'   => 'number',
				],
			]
		);

		$repeater->add_control(
			'number_suffix',
			[
				'label'       => esc_html__( 'After', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'seats, km, etc.', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'select_filter' => [ 'Custom Field', 'Numeric' ],
					'format_type'   => 'number',
				],
			]
		);

		$repeater->add_control(
			'text_case',
			[
				'label'     => esc_html__( 'Text Format', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'as_is',
				'options'   => [
					'as_is'      => esc_html__( 'As stored', 'better-post-filter-widgets-for-elementor' ),
					'uppercase'  => esc_html__( 'Uppercase', 'better-post-filter-widgets-for-elementor' ),
					'lowercase'  => esc_html__( 'Lowercase', 'better-post-filter-widgets-for-elementor' ),
					'capitalize' => esc_html__( 'Capitalize', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'select_filter' => [ 'Custom Field', 'Numeric' ],
					'format_type'   => 'text',
				],
			]
		);

		$repeater->add_control(
			'custom_pattern',
			[
				'label'       => esc_html__( 'Pattern', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Text before #VALUE# text after', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'select_filter' => [ 'Custom Field', 'Numeric' ],
					'format_type'   => 'custom_pattern',
				],
			]
		);

		$repeater->add_control(
			'filter_style_numeric',
			[
				'label'     => esc_html__( 'Field Type', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'range',
				'options'   => [
					'range'      => esc_html__( 'Range', 'better-post-filter-widgets-for-elementor' ),
					'checkboxes' => esc_html__( 'Checkboxes', 'better-post-filter-widgets-for-elementor' ),
					'radio'      => esc_html__( 'Radio Buttons', 'better-post-filter-widgets-for-elementor' ),
					'list'       => esc_html__( 'Label List', 'better-post-filter-widgets-for-elementor' ),
					'input'      => esc_html__( 'Input Field', 'better-post-filter-widgets-for-elementor' ),
				],
				'separator' => 'before',
				'condition' => [
					'select_filter' => 'Numeric',
				],
			]
		);

		$repeater->add_control(
			'visual_range',
			[
				'label'        => esc_html__( 'Visual Range', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'select_filter'        => 'Numeric',
					'filter_style_numeric' => 'range',
				],
			]
		);

		$repeater->add_control(
			'visual_range_inclusive',
			[
				'label'        => esc_html__( 'Inclusive Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'select_filter'        => 'Numeric',
					'filter_style_numeric' => 'range',
					'visual_range'         => 'yes',
				],
			]
		);

		$repeater->add_control(
			'visual_range_icon_normal',
			[
				'label'     => esc_html__( 'Icon (Normal)', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'far fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'select_filter'        => 'Numeric',
					'filter_style_numeric' => 'range',
					'visual_range'         => 'yes',
				],
			]
		);

		$repeater->add_control(
			'visual_range_icon_selected',
			[
				'label'     => esc_html__( 'Icon (Selected)', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'select_filter'        => 'Numeric',
					'filter_style_numeric' => 'range',
					'visual_range'         => 'yes',
				],
			]
		);

		$repeater->add_control(
			'visual_range_max_icons',
			[
				'label'     => esc_html__( 'Max. Range', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'default'   => 5,
				'condition' => [
					'select_filter'        => 'Numeric',
					'filter_style_numeric' => 'range',
					'visual_range'         => 'yes',
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
					'filter_style_numeric' => [ 'range','input' ],
					'visual_range!'        => 'yes',
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
					'filter_style_cf' => [ 'dropdown', 'select2' ],
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
				'conditions'   => [
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
			'swatch_notice',
			[
				'type'        => \Elementor\Controls_Manager::NOTICE,
				'notice_type' => 'info',
				'dismissible' => false,
				// translators: %s is an HTML link to the taxonomy settings page.
				'content'     => sprintf( wp_kses( __( 'Add swatches to your taxonomy terms to enable this feature. %s', 'better-post-filter-widgets-for-elementor' ), [ 'a' => [ 'href' => [], 'target' => [] ] ] ), '<a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=category' ) ) . '" target="_blank">' . esc_html__( 'Go to taxonomy settings.', 'better-post-filter-widgets-for-elementor' ) . '</a>' ),
				'condition'   => [
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
					'select_filter'   => 'Custom Field',
					'filter_style_cf' => 'input',
				],
			]
		);

		$repeater->add_control(
			'min_input_placeholder',
			[
				'label'       => esc_html__( 'Min. Placeholder', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Min.', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Min.', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'select_filter'        => 'Numeric',
					'filter_style_numeric' => 'input',
					'visual_range!'        => 'yes',
				],
			]
		);

		$repeater->add_control(
			'max_input_placeholder',
			[
				'label'       => esc_html__( 'Max. Placeholder', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Max.', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Max.', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'select_filter'        => 'Numeric',
					'filter_style_numeric' => 'input',
					'visual_range!'        => 'yes',
				],
			]
		);

		$repeater->add_control(
			'group_facet_mode',
			[
				'label'     => esc_html__( 'Group Facet Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'inherit',
				'options'   => [
					'inherit'       => esc_html__( 'Inherit', 'better-post-filter-widgets-for-elementor' ),
					'disable-facet' => esc_html__( 'Grey out', 'better-post-filter-widgets-for-elementor' ),
					'hide-facet'    => esc_html__( 'Hide', 'better-post-filter-widgets-for-elementor' ),
				],
				'separator' => 'before',
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
					''           => esc_html__( 'None', 'better-post-filter-widgets-for-elementor' ),
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
			'order',
			[
				'label'     => esc_html__( 'Order', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => [
					'DESC' => esc_html__( 'Descending', 'better-post-filter-widgets-for-elementor' ),
					'ASC'  => esc_html__( 'Ascending', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'sort_terms!' => '',
				],
			]
		);

		$repeater->add_control(
			'filter_logic',
			[
				'label'     => esc_html__( 'Group Logic', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'OR',
				'options'   => [
					'OR'  => esc_html__( 'OR', 'better-post-filter-widgets-for-elementor' ),
					'AND' => esc_html__( 'AND', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'select_filter' => [ 'Taxonomy', 'Relational' ],
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
				'condition'    => [
					'select_filter' => [ 'Taxonomy', 'Custom Field', 'Relational' ],
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

		$repeater->add_control(
			'select_all',
			[
				'label'        => esc_html__( 'Select All', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'filter_style!'  => [ 'radio','dropdown','select2' ],
					'select_filter!' => 'Numeric',
				],
			]
		);

		$repeater->add_control(
			'select_all_label',
			[
				'label'       => esc_html__( 'Select All Label', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Select All', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Select All', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'filter_style!' => [ 'radio','dropdown','select2' ],
					'select_all'    => 'yes',
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

		$this->add_control(
			'is_facetted',
			[
				'label'              => esc_html__( 'Enable Faceted Filter', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'facet_mode',
			[
				'label'     => esc_html__( 'Facet Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'disable-facet',
				'options'   => [
					'disable-facet' => esc_html__( 'Grey out', 'better-post-filter-widgets-for-elementor' ),
					'hide-facet'    => esc_html__( 'Hide', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'is_facetted' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'default_filter_section',
			[
				'label' => esc_html__( 'Default Filters', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$pre_filter_repeater = new \Elementor\Repeater();

		$pre_filter_repeater->add_control(
			'filter_type',
			[
				'label'       => esc_html__( 'Filter Type', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => [
					'term'            => esc_html__( 'Taxonomy Term', 'better-post-filter-widgets-for-elementor' ),
					'meta'            => esc_html__( 'Custom Field', 'better-post-filter-widgets-for-elementor' ),
					'meta_numeric'    => esc_html__( 'Custom Field (Numeric)', 'better-post-filter-widgets-for-elementor' ),
					'meta_relational' => esc_html__( 'Relational Field', 'better-post-filter-widgets-for-elementor' ),
					'date'            => esc_html__( 'Date', 'better-post-filter-widgets-for-elementor' ),
				],
				'default'     => 'term',
				'label_block' => true,
			]
		);

		$pre_filter_repeater->add_control(
			'taxonomy',
			[
				'label'       => esc_html__( 'Taxonomy', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => BPFWE_Helper::get_taxonomies_options(),
				'default'     => array_key_first( BPFWE_Helper::get_taxonomies_options() ),
				'label_block' => true,
				'condition'   => [ 'filter_type' => 'term' ],
			]
		);

		$taxonomies = get_taxonomies( [], 'objects' );
		$all_terms  = [];

		foreach ( $taxonomies as $index => $tax ) {
			$terms_transient_key = 'bpfwe_terms_' . $index;
			$terms               = get_transient( $terms_transient_key );

			if ( false === $terms ) {
				$terms = get_terms(
					[
						'taxonomy'   => $index,
						'hide_empty' => false,
					]
				);

				set_transient( $terms_transient_key, $terms, HOUR_IN_SECONDS );
			}

			$all_terms[ $index ] = $terms;

			$term_options = [];

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_options[ absint( $term->term_id ) ][0] = esc_html( $term->name );
				}
			}

			$pre_filter_repeater->add_control(
				'terms_' . $index,
				[
					'label'       => sprintf(
						// translators: %s is the taxonomy label.
						__( 'Include %s', 'better-post-filter-widgets-for-elementor' ),
						$tax->label
					),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => $term_options,
					'condition'   => [
						'filter_type' => 'term',
						'taxonomy'    => $index,
					],
				]
			);
		}

		$pre_filter_repeater->add_control(
			'meta_key',
			[
				'label'       => esc_html__( 'Meta Key', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => [ 'filter_type' => [ 'meta', 'meta_numeric', 'meta_relational' ] ],
			]
		);

		$pre_filter_repeater->add_control(
			'meta_value',
			[
				'label'       => esc_html__( 'Meta Value', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => [ 'filter_type' => 'meta' ],
			]
		);

		$pre_filter_repeater->add_control(
			'meta_value_relational',
			[
				'label'       => esc_html__( 'Meta Values', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => [],
				'condition'   => [ 'filter_type' => 'meta_relational' ],
			]
		);

		$pre_filter_repeater->add_control(
			'meta_value_relational_raw',
			[
				'label' => esc_html__( 'Meta Values (raw)', 'better-post-filter-widgets-for-elementor' ),
				'type'  => \Elementor\Controls_Manager::HIDDEN,
			]
		);

		$pre_filter_repeater->add_control(
			'meta_value_min',
			[
				'label'     => esc_html__( 'Minimum Value', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'condition' => [ 'filter_type' => 'meta_numeric' ],
			]
		);

		$pre_filter_repeater->add_control(
			'meta_value_max',
			[
				'label'     => esc_html__( 'Maximum Value', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'condition' => [ 'filter_type' => 'meta_numeric' ],
			]
		);

		$pre_filter_repeater->add_control(
			'max_days_old',
			[
				'label'     => esc_html__( 'Post Age Limit (days)', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'step'      => 1,
				'condition' => [ 'filter_type' => 'date' ],
			]
		);

		$this->add_control(
			'default_filters',
			[
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $pre_filter_repeater->get_controls(),
				'title_field'   => '{{{ 
					filter_type === "term" ? "Term: " + taxonomy : 
					filter_type === "meta" ? "Meta: " + meta_key : 
					filter_type === "meta_numeric" ? "Meta Numeric: " + meta_key : 
					filter_type === "meta_relational" ? "Meta Relational: " + meta_key : 
					"Post Age Limit: " + max_days_old + " Days" 
				}}}',
				'prevent_empty' => false,
				'default'       => [],
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
			'filter_custom_handler',
			[
				'label'              => esc_html__( 'Custom AJAX Handler', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'Off', 'better-post-filter-widgets-for-elementor' ),
				'description'        => esc_html__( 'Loads a minimal WP environment for faster query processing. Default: Off. Impact on Speed: High.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'optimize_query',
			[
				'label'              => esc_html__( 'Load Only Post ID', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'Off', 'better-post-filter-widgets-for-elementor' ),
				'description'        => esc_html__( 'Loads only post IDs. Best for ID-based widgets but may break those needing full post details. Default: Off. Impact on Speed: High.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'no_found_rows',
			[
				'label'              => esc_html__( 'Skip Pagination Count', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'Off', 'better-post-filter-widgets-for-elementor' ),
				'description'        => esc_html__( 'Skips counting total posts. Use only if pagination isn’t needed. Default: Off. Impact on Speed: Medium.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'suppress_filters',
			[
				'label'              => esc_html__( 'Bypass Query Modifications', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'On', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'Off', 'better-post-filter-widgets-for-elementor' ),
				'description'        => esc_html__( 'Ignores query tweaks. May break 3rd party features. Do not enable this option if you\'re using a translation plugin. Default: Off. Impact on Speed: Medium.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'no',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'              => esc_html__( 'Posts Per Page', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::NUMBER,
				'description'        => esc_html__( 'Limits the number of posts per page. Use -1 to use post widget’s default value, if accesible by the query. Default: -1. Impact on Speed: High.', 'better-post-filter-widgets-for-elementor' ),
				'default'            => -1,
				'min'                => -1,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'transient_duration',
			[
				'label'       => esc_html__( 'Cache filter’s terms (s)', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => esc_html__( 'Caches filter terms for faster loading. Set to 0 to disable (not recommended). Default: 86400 (1 day). Impact on Speed: High.', 'better-post-filter-widgets-for-elementor' ),
				'default'     => 86400,
				'min'         => 0,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'query_options_section',
			[
				'label' => esc_html__( 'Query', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'inject_query_id',
			[
				'label'              => esc_html__( 'Include Loop Grid Query ID', 'better-post-filter-widgets-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'inject_query_id_warning',
			[
				'type'        => \Elementor\Controls_Manager::NOTICE,
				'notice_type' => 'warning',
				'dismissible' => true,
				'content'     => esc_html__( 'This option may conflict with other Query ID. Ensure the filter still works as expected after turning this on.', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'inject_query_id!' => '',
				],
			]
		);

		$this->add_control(
			'filter_query_id',
			[
				'label'              => esc_html__( 'Filter Query ID', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::TEXT,
				'description'        => esc_html__( 'Give your filter\'s query a unique ID to allow server side filtering.', 'better-post-filter-widgets-for-elementor' ),
				'separator'          => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'enable_query_debug',
			[
				'label'              => esc_html__( 'Enable Query Debugging', 'better-post-filter-widgets-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'separator'          => 'before',
				'frontend_available' => true,
				'description'        => esc_html__( 'Displays the WP_Query arguments generated by the filter widget.', 'better-post-filter-widgets-for-elementor' ),
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
			'display_selected_terms',
			[
				'label'        => esc_html__( 'Display Selected Terms', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'separator'    => 'before',
				'default'      => '',
			]
		);

		$this->add_control(
			'selected_terms_description',
			[
				'type'        => \Elementor\Controls_Manager::NOTICE,
				'notice_type' => 'info',
				'dismissible' => false,
				'heading'     => esc_html__( 'How to Use', 'better-post-filter-widgets-for-elementor' ),
				'content'     => esc_html__( 'Add "selected-terms-FILTERID", "selected-count-FILTERID", or "quick-deselect-FILTERID" to a Heading or Text widget. The widget needs content (use a non-breaking space to keep it blank), otherwise it will not appear on the page.', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'display_selected_terms' => 'yes',
				],
			]
		);

		$this->add_control(
			'selected_terms_class',
			[
				'label'       => esc_html__( 'Display Terms Class', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'ui',
				'description' => '<script>
					jQuery(document).ready(function($) {
						var $input = $(".elementor-control-selected_terms_class input");
						var widgetID = "selected-terms-" + elementor.getCurrentElement().model.id;
						$input.val(widgetID).attr("readonly", true);

						$input.on("click", function() {
							this.select();
							document.execCommand("copy");
							var notice = elementor.notifications.showToast({
								message: "Copied to clipboard!",
								type: "success"
							});
							setTimeout(function() {
								notice.close();
							}, 1000);
						});
					});
				</script>',
				'condition'   => [
					'display_selected_terms' => 'yes',
				],
			]
		);

		$this->add_control(
			'selected_count_class',
			[
				'label'       => esc_html__( 'Display Count Class', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'ui',
				'description' => '<script>
					jQuery(document).ready(function($) {
						var $input = $(".elementor-control-selected_count_class input");
						var widgetID = "selected-count-" + elementor.getCurrentElement().model.id;
						$input.val(widgetID).attr("readonly", true);

						$input.on("click", function() {
							this.select();
							document.execCommand("copy");
							var notice = elementor.notifications.showToast({
								message: "Copied to clipboard!",
								type: "success"
							});
							setTimeout(function() {
								notice.close();
							}, 1000);
						});
					});
				</script>',
				'condition'   => [
					'display_selected_terms' => 'yes',
				],
			]
		);

		$this->add_control(
			'quick_deselect_class',
			[
				'label'       => esc_html__( 'Quick Deselect Class', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'ui',
				'description' => '<script>
					jQuery(document).ready(function($) {
						var $input = $(".elementor-control-quick_deselect_class input");
						var widgetID = "quick-deselect-" + elementor.getCurrentElement().model.id;
						$input.val(widgetID).attr("readonly", true);

						$input.on("click", function() {
							this.select();
							document.execCommand("copy");
							var notice = elementor.notifications.showToast({
								message: "Copied to clipboard!",
								type: "success"
							});
							setTimeout(function() {
								notice.close();
							}, 1000);
						});
					});
				</script>',
				'condition'   => [
					'display_selected_terms' => 'yes',
				],
			]
		);

		$this->add_control(
			'display_selected_before',
			[
				'label'              => esc_html__( 'Before/After', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::TEXT,
				'placeholder'        => esc_html__( 'Selected:', 'better-post-filter-widgets-for-elementor' ),
				'condition'          => [
					'display_selected_terms' => 'yes',
				],
				'frontend_available' => true,
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

		$this->add_control(
			'elementor_template_id',
			[
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label'       => esc_html__( 'Elementor Template ID', 'better-post-filter-widgets-for-elementor' ),
				'description' => esc_html__( 'Only use this field if this widget is rendered inside an Elementor Pro template (Single, Archive, etc.) and filtering returns AJAX 500. Only needed once per template. Leave empty for automatic detection.', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'e.g. 202', 'better-post-filter-widgets-for-elementor' ),
				'separator'   => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'filter_id',
			[
				'label'       => esc_html__( 'Filter ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'ui',
				'separator'   => 'before',
				'description' => '<script>
					jQuery(document).ready(function($) {
						var $input = $(".elementor-control-filter_id input");
						var widgetID = "filter-" + elementor.getCurrentElement().model.id;
						$input.val(widgetID).attr("readonly", true);

						$input.on("click", function() {
							this.select();
							document.execCommand("copy");
							var notice = elementor.notifications.showToast({
								message: "Filter ID copied!",
								type: "success"
							});
							setTimeout(function() {
								notice.close();
							}, 1000);
						});
					});
				</script>',
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- SECTION: Style.
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
				'label' => esc_html__( 'Group Title', 'better-post-filter-widgets-for-elementor' ),
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

		$this->add_responsive_control(
			'filter_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .filter-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} .filter-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'filter_title_style_tabs'
		);

		$this->start_controls_tab(
			'filter_title_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'filter_title_color',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_title_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-title' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_title_border',
				'selector' => '{{WRAPPER}} .filter-title',
			)
		);

		$this->add_responsive_control(
			'filter_title_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .filter-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_title_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'filter_title_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-title:hover, {{WRAPPER}} .filter-title.collapsible:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_title_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-title:hover, {{WRAPPER}} .filter-title.collapsible:hover' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_title_border_hover',
				'selector' => '{{WRAPPER}} .filter-title:hover, {{WRAPPER}} .filter-title.collapsible:hover',
			)
		);

		$this->add_responsive_control(
			'filter_title_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .filter-title:hover, {{WRAPPER}} .filter-title.collapsible:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_title_style_active_tab',
			[
				'label' => esc_html__( 'Active', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'filter_title_color_selected',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-title.collapsible.collapsed' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_title_bg_color_selected',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-title.collapsible.collapsed' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_title_border_selected',
				'selector' => '{{WRAPPER}} .filter-title.collapsible.collapsed',
			)
		);

		$this->add_responsive_control(
			'filter_title_border_radius_selected',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .filter-title.collapsible.collapsed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'toggle_content_padding',
			array(
				'label'      => esc_html__( 'Toggle Content Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-taxonomy-wrapper, {{WRAPPER}} .bpfwe-custom-field-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'filter_label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-tax label:not(.collapsible)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_label_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%', 'rem' ],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .form-tax label:not(.collapsible)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; display: flex; align-items: center;',
				],
			]
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
				'selector' => '{{WRAPPER}} .form-tax .label-text',
			)
		);

		$this->start_controls_tabs(
			'filter_label_style_tabs'
		);

		$this->start_controls_tab(
			'filter_label_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
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
					'{{WRAPPER}} .form-tax .label-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_label_border',
				'selector' => '{{WRAPPER}} .form-tax .label-text',
			)
		);

		$this->add_responsive_control(
			'filter_label_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-tax .label-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_label_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'filter_label_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .form-tax label:hover .label-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_label_border_hover',
				'selector' => '{{WRAPPER}} .form-tax label:hover .label-text',
			)
		);

		$this->add_responsive_control(
			'filter_label_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-tax label:hover .label-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_label_style_selected_tab',
			[
				'label' => esc_html__( 'Selected', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'filter_label_color_selected',
			array(
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .form-tax input:checked + span.label-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_label_border_selected',
				'selector' => '{{WRAPPER}} .form-tax input:checked + span.label-text',
			)
		);

		$this->add_responsive_control(
			'filter_label_border_radius_selected',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-tax input:checked + span.label-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
			'section_visual_range',
			array(
				'label' => esc_html__( 'Visual Range', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'visual_range_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bpfwe-visual-range-wrapper'                    => 'font-size: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}} .bpfwe-visual-range-wrapper .bpfwe-visual-icon' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .bpfwe-visual-range-wrapper svg'                => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'visual_range_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-visual-range-wrapper .bpfwe-visual-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'visual_range_margin',
			array(
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .bpfwe-visual-range-wrapper .bpfwe-visual-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'visual_range_style_tabs'
		);

		$this->start_controls_tab(
			'visual_range_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'visual_range_color_normal',
			array(
				'label'     => esc_html__( 'Icon Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .bpfwe-visual-range-wrapper .icon-normal, {{WRAPPER}} .bpfwe-visual-range-wrapper .icon-normal svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'visual_range_style_selected_tab',
			[
				'label' => esc_html__( 'Selected', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'visual_range_color_selected',
			array(
				'label'     => esc_html__( 'Icon Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .bpfwe-visual-range-wrapper .icon-selected, {{WRAPPER}} .bpfwe-visual-range-wrapper .icon-selected svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
			'section_dropdown',
			array(
				'label' => esc_html__( 'Select Dropdown', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'dropdown_typography',
				'selector' => '{{WRAPPER}} select',
			)
		);

		$this->add_control(
			'dropdown_color',
			array(
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} select'        => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} select option' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'dropdown_background',
			array(
				'label'     => esc_html__( 'Background Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} select'        => 'background-color !important; {{VALUE}}; appearance: none; -webkit-appearance: none; -moz-appearance: none;',
					'{{WRAPPER}} select option' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'dropdown_border',
				'selector' => '{{WRAPPER}} select',
			)
		);

		$this->add_responsive_control(
			'dropdown_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_padding',
			array(
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_margin',
			array(
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} select' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
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

		$this->add_control(
			'selection_select2_highlight_background',
			array(
				'label'     => esc_html__( 'Highlight Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .select2-container--default .select2-results__option--highlighted[aria-selected]' => 'background-color: {{VALUE}}',
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
			'reset_button_height',
			array(
				'label'      => esc_html__( 'Height', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 30,
						'max' => 100,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} button.reset-form' => 'height: {{SIZE}}{{UNIT}};',
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
			'submit_button_height',
			array(
				'label'      => esc_html__( 'Height', 'better-post-filter-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 30,
						'max' => 100,
					),
					'em' => array(
						'min' => 1,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} button.submit-form' => 'height: {{SIZE}}{{UNIT}};',
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
		$is_editor          = current_user_can( 'edit_posts' );
		$is_facetted        = $settings['is_facetted'] ? true : false;
		$facet_mode         = $settings['facet_mode'] ? $settings['facet_mode'] : '';
		$show_counter       = '';
		$toggleable_class   = '';
		$min_value          = '';
		$max_value          = '';
		$filter_query_id    = $settings['filter_query_id'];

		if ( $settings['filter_list'] ) {
			$index = 0;
			echo '
			<div class="filter-container" data-target-post-widget="' . esc_attr( $settings['target_selector'] ) . '">
			<form id="filter-' . esc_attr( $widget_id ) . '" class="form-tax elementor-grid ' . esc_attr( $facet_mode ) . '" action="/" method="get" autocomplete="on">
			<input type="hidden" name="bpf_filter_nonce" value="' . esc_attr( wp_create_nonce( 'nonce' ) ) . '">
			';

			$default_filters = $this->get_settings_for_display( 'default_filters' );

			if ( ! empty( $default_filters ) ) {
				echo '<div class="bpfwe-default-filters" style="display:none !important;">';

				foreach ( $default_filters as $index => $filter ) {
					$filter_type = isset( $filter['filter_type'] ) ? sanitize_key( $filter['filter_type'] ) : '';
					$logic       = 'AND';

					switch ( $filter_type ) {
						case 'term':
							if ( ! empty( $filter['taxonomy'] ) && ! empty( $filter[ 'terms_' . $filter['taxonomy'] ] ) ) {
								$taxonomy = sanitize_key( $filter['taxonomy'] );
								$terms    = (array) $filter[ 'terms_' . $taxonomy ];
								echo '<div class="bpfwe-taxonomy-wrapper" data-logic="' . esc_attr( $logic ) . '">';
								foreach ( $terms as $term_id ) {
									printf(
										'<input type="checkbox" class="bpfwe-filter-item" name="%1$s" data-taxonomy="%1$s" value="%2$d" checked>',
										esc_attr( $taxonomy ),
										absint( $term_id )
									);
								}
								echo '</div>';
							}
							break;

						case 'meta':
							$meta_key   = sanitize_key( $filter['meta_key'] ?? '' );
							$meta_value = $filter['meta_value'] ?? '';
							if ( '' !== $meta_value && $meta_key ) {
								echo '<div class="bpfwe-custom-field-wrapper" data-logic="' . esc_attr( $logic ) . '">';
								printf(
									'<input type="text" class="input-text bpfwe-filter-item" name="%1$s" data-taxonomy="%1$s" value="%2$s">',
									esc_attr( $meta_key ),
									esc_attr( $meta_value )
								);
								echo '</div>';
							}
							break;

						case 'meta_relational':
							$meta_key   = sanitize_key( $filter['meta_key'] ?? '' );
							$raw_values = $filter['meta_value_relational'] ?? '';
							if ( is_array( $raw_values ) ) {
								$raw_values = rtrim( implode( ',', $raw_values ), ',' );
							} else {
								$raw_values = rtrim( $raw_values, ',' );
							}
							if ( $meta_key && '' !== $raw_values ) {
								$values = array_filter( array_map( 'trim', explode( ',', $raw_values ) ), 'strlen' );
								echo '<div class="bpfwe-custom-field-relational-wrapper" data-logic="' . esc_attr( $logic ) . '">';
								foreach ( $values as $val ) {
									$val_clean = is_numeric( $val ) ? intval( $val ) : sanitize_text_field( $val );
									printf(
										'<input type="checkbox" class="bpfwe-filter-item" name="%1$s" data-taxonomy="%1$s" value="%2$s" checked>',
										esc_attr( $meta_key ),
										esc_attr( $val_clean )
									);
								}
								echo '</div>';
							}
							break;

						case 'meta_numeric':
							$meta_key = sanitize_key( $filter['meta_key'] ?? '' );
							$min      = isset( $filter['meta_value_min'] ) ? $filter['meta_value_min'] : '';
							$max      = isset( $filter['meta_value_max'] ) ? $filter['meta_value_max'] : '';

							if ( $meta_key && ( '' !== $min || '' !== $max ) ) {
								echo '<div class="bpfwe-numeric-wrapper" data-logic="' . esc_attr( $logic ) . '">';

								if ( '' !== $min ) {
									printf(
										'<input type="number" inputmode="numeric" pattern="[0-9]*" class="input-min bpfwe-filter-item" name="min_%1$s" data-taxonomy="%1$s" value="%2$s">',
										esc_attr( $meta_key ),
										esc_attr( $min )
									);
								}

								if ( '' !== $max ) {
									printf(
										'<input type="number" inputmode="numeric" pattern="[0-9]*" class="input-max bpfwe-filter-item" name="max_%1$s" data-taxonomy="%1$s" value="%2$s">',
										esc_attr( $meta_key ),
										esc_attr( $max )
									);
								}

								echo '</div>';
							}
							break;

						case 'date':
							if ( isset( $filter['max_days_old'] ) && is_numeric( $filter['max_days_old'] ) ) {
								$days = (int) $filter['max_days_old'];
								echo '<div class="bpfwe-custom-field-wrapper" data-logic="' . esc_attr( $logic ) . '">';
								printf(
									'<input type="hidden" class="bpfwe-filter-item" name="bpfwe_date_limit" data-taxonomy="post_date" value="%d">',
									absint( $days )
								);
								echo '</div>';
							}
							break;
					}
				}

				echo '</div>';
			}

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

				if ( 'Relational' === $item['select_filter'] && empty( $item['meta_key'] ) ) {
					return;
				}

				// Retrieve current filter's query.
				$filter_data          = get_transient( 'bpfwe_filter_query' );
				$allowed_term_ids     = [];
				$facetted_term_counts = [];
				$post_ids             = [];
				$group_facet_mode     = ( 'inherit' !== $item['group_facet_mode'] ) ? $item['group_facet_mode'] : '';
				$taxonomy_is_faceted  = false;

				$wrapper_classes_tax  = [ 'flex-wrapper', $item['filter_by'] ];
				$wrapper_classes_meta = [ 'flex-wrapper', $item['meta_key'] ];

				if ( '' !== $group_facet_mode ) {
					$wrapper_classes_tax[]  = $group_facet_mode;
					$wrapper_classes_meta[] = $group_facet_mode;
				}

				$wrapper_classes_tax  = implode( ' ', $wrapper_classes_tax );
				$wrapper_classes_meta = implode( ' ', $wrapper_classes_meta );

				if ( $is_facetted && is_array( $filter_data ) && 'Taxonomy' === $item['select_filter'] ) {

					$taxonomy = sanitize_key( $item['filter_by'] );
					$post_ids = get_transient( 'bpfwe_filter_post_ids' );

					if ( empty( $post_ids ) || ! is_array( $post_ids ) ) {
						return;
					}

					$taxonomy_is_faceted = true;

					foreach ( $post_ids as $post_id ) {

						$post_term_ids = wp_get_post_terms(
							absint( $post_id ),
							$taxonomy,
							[
								'fields'  => 'ids',
								'orderby' => 'none',
							]
						);

						if ( is_wp_error( $post_term_ids ) || empty( $post_term_ids ) ) {
							continue;
						}

						foreach ( $post_term_ids as $term_id ) {
							$term_id = absint( $term_id );

							$allowed_term_ids[ $term_id ] = true;

							if ( isset( $facetted_term_counts[ $term_id ] ) ) {
								++$facetted_term_counts[ $term_id ];
							} else {
								$facetted_term_counts[ $term_id ] = 1;
							}
						}
					}

					$allowed_term_ids = array_keys( $allowed_term_ids );
				}

				++$index;

				if ( 'Taxonomy' === $item['select_filter'] ) {

					// Check if transient exists.
					$transient_key = 'filter_widget_taxonomy_' . $item['filter_by'];

					$hiterms       = get_transient( $transient_key );
					$display_empty = 'yes' === $item['display_empty'] ? false : true;

					// Invalidate cache if editing.
					if ( $is_editor ) {
						delete_transient( $transient_key );
						$hiterms = false;
					}

					// Bypass transient for users with editing capabilities.
					if ( false === $hiterms || $is_editor ) {
						$args = [
							'taxonomy'          => sanitize_key( $item['filter_by'] ),
							'hide_empty'        => $display_empty,
							'parent'            => 'yes' === $item['show_hierarchy'] ? 0 : null,
							'fields'            => 'all',
							'update_meta_cache' => false,
						];

						$valid_orderby = [ '', 'name', 'slug', 'count', 'term_group', 'term_order', 'term_id' ];

						if ( ! empty( $item['sort_terms'] ) && in_array( $item['sort_terms'], $valid_orderby, true ) ) {
							$args['orderby'] = $item['sort_terms'];
							$args['order']   = in_array( $item['order'], [ 'ASC', 'DESC' ], true ) ? $item['order'] : 'ASC';
						}

						$hiterms = BPFWE_Helper::bpfwe_get_terms( $args, $filter_query_id, $this );

						if ( $transient_duration > 0 && ! $is_editor ) {
							set_transient( $transient_key, $hiterms, $transient_duration );
						}
					}

					// Extend allowed_term_ids with ancestors for hierarchy in faceted mode.
					if ( $is_facetted && 'yes' === $item['show_hierarchy'] && ! empty( $allowed_term_ids ) ) {
						$extended_allowed = $allowed_term_ids;
						foreach ( $allowed_term_ids as $term_id ) {
							$ancestors        = get_ancestors( $term_id, $item['filter_by'], 'taxonomy' );
							$extended_allowed = array_merge( $extended_allowed, $ancestors );
						}
						$allowed_term_ids = array_unique( $extended_allowed );
					}

					// Intersect base terms with allowed IDs.
					if ( $is_facetted && $taxonomy_is_faceted ) {
						$hiterms = array_filter(
							$hiterms,
							function ( $term ) use ( $allowed_term_ids ) {

								if ( empty( $allowed_term_ids ) ) {
									// Faceted, but no matches for this taxonomy -> remove all.
									return false;
								}

								return in_array( absint( $term->term_id ), $allowed_term_ids, true );
							}
						);
					}

					if ( 'checkboxes' === $item['filter_style'] || 'checkboxes' === $item['filter_style_cf'] ) {
						$term_index = 0;
						echo '
						<div class="' . esc_attr( $wrapper_classes_tax ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="bpfwe-taxonomy-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $item['hide_label_swatch'] ) . ' ' . esc_attr( $item['hide_input_swatch'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';

						if ( 'yes' === $item['select_all'] ) {
							$select_all_label = ! empty( $item['select_all_label'] ) ? $item['select_all_label'] : esc_html__( 'Select All', 'better-post-filter-widgets-for-elementor' );

							echo '
							<li class="parent-term select-all-term">
								<label>
									<span class="bpfwe-filter-item bpfwe-select-all" data-taxonomy="' . esc_attr( $item['filter_by'] ) . '">
										<span><span class="label-text">' . esc_html( $select_all_label ) . '</span></span>
									</span>
								</label>
							</li>
							';
						}

						foreach ( $hiterms as $key => $hiterm ) {
							$has_children = $is_facetted && 'yes' === $item['show_hierarchy'] && ! empty( get_term_children( $hiterm->term_id, $hiterm->taxonomy ) );

							if ( $is_facetted && $has_children ) {
								$show_counter = ( 'yes' === $item['show_counter'] ) ? ' (–)' : '';
							} else {
								$effective_count = isset( $facetted_term_counts[ $hiterm->term_id ] ) ? $facetted_term_counts[ $hiterm->term_id ] : $hiterm->count;
								$show_counter    = ( 'yes' === $item['show_counter'] ) ? ' (<span class="count" data-reset="' . $effective_count . '">' . $effective_count . '</span>)' : '';
							}

							$swatches_type  = 'yes' === $item['display_swatch'] ? get_term_meta( $hiterm->term_id, 'bpfwe_swatches_type', true ) : '';
							$group_text     = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_group_text', true );
							$swatch_html    = '';
							$separator_html = '';

							if ( $group_text && 'yes' === $item['display_swatch'] ) {
								$separator_html = '<div class="bpfwe-group-separator" role="separator" aria-label="Group Separator">' . esc_html( $group_text ) . '</div>';
							}

							switch ( $swatches_type ) {
								case 'color':
									$swatches_color = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_color', true );
									if ( $swatches_color ) {
										$swatch_html = '<span style="background-color: ' . esc_attr( $swatches_color ) . '" class="bpfwe-swatch" role="img" aria-label="Color Swatch" title="' . esc_attr( $hiterm->name ) . '"></span> ';
									}
									break;

								case 'image':
									$swatches_image = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_image', true );
									if ( $swatches_image ) {
										$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="Image Swatch" title="' . esc_attr( $hiterm->name ) . '"></span> ';
									}
									break;

								case 'product-cat-image':
									if ( 'product-cat-image' === $swatches_type ) {
										if ( class_exists( 'WooCommerce' ) ) {
											$thumbnail_id   = get_term_meta( $hiterm->term_id, 'thumbnail_id', true );
											$swatches_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
											if ( $swatches_image ) {
												$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="Image Swatch" title="' . esc_attr( $hiterm->name ) . '"></span> ';
											}
										} else {
											$swatch_html = '';
										}
									}
									break;

								case 'button':
									$swatches_button_text = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_button_text', true );
									if ( $swatches_button_text ) {
										$swatch_html = '<span class="bpfwe-swatch bpfwe-swatch-button" role="button" aria-label="Button Swatch" title="' . esc_attr( $swatches_button_text ) . '">' . esc_html( $swatches_button_text ) . '</span> ';
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
								<span>' . wp_kses_post( $swatch_html ) . '<span class="label-text">' . wp_kses_post( $hiterm->name . $show_counter ) . '</span></span>
								<span class="low-group-trigger" role="button" aria-expanded="false">+</span>
								</label>
							';

							if ( 'yes' === $item['show_hierarchy'] ) {
								$terms_stack            = array();
								$lowterms_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $hiterm->term_id;
								$lowterms               = get_transient( $lowterms_transient_key );

								// Invalidate cache if editing.
								if ( $is_editor ) {
									delete_transient( $lowterms_transient_key );
									$lowterms = false;
								}

								if ( false === $lowterms || $is_editor ) {
									$args = array(
										'taxonomy'   => sanitize_key( $item['filter_by'] ),
										'parent'     => $hiterm->term_id,
										'hide_empty' => $display_empty,
										'fields'     => 'all',
										'update_meta_cache' => false,
									);

									$valid_orderby = [ '', 'name', 'slug', 'count', 'term_group', 'term_order', 'term_id' ];
									if ( ! empty( $item['sort_terms'] ) && in_array( $item['sort_terms'], $valid_orderby, true ) ) {
										$args['orderby'] = $item['sort_terms'];
										$args['order']   = in_array( $item['order'], [ 'ASC', 'DESC' ], true ) ? $item['order'] : 'ASC';
									}

									$lowterms = BPFWE_Helper::bpfwe_get_terms( $args, $filter_query_id, $this );

									if ( $transient_duration > 0 && ! $is_editor ) {
										set_transient( $lowterms_transient_key, $lowterms, $transient_duration );
									}
								}

								// Intersect base terms with allowed IDs.
								if ( $is_facetted && $taxonomy_is_faceted ) {
									$lowterms = array_filter(
										$lowterms,
										function ( $term ) use ( $allowed_term_ids, $display_empty ) {

											// Faceted and this taxonomy participated, but no matches -> clear all.
											if ( empty( $allowed_term_ids ) ) {
												return false;
											}

											if ( ! in_array( absint( $term->term_id ), $allowed_term_ids, true ) ) {
												return false;
											}

											if ( ! $display_empty && 0 === (int) $term->count ) {
												return false;
											}

											return true;
										}
									);
								}

								if ( $lowterms ) {

									foreach ( array_reverse( $lowterms ) as $lowterm ) {
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

										$effective_count = isset( $facetted_term_counts[ $term->term_id ] ) ? $facetted_term_counts[ $term->term_id ] : $term->count;
										$show_counter    = ( 'yes' === $item['show_counter'] ) ? ' (<span class="count" data-reset="' . $effective_count . '">' . $effective_count . '</span>)' : '';
										$swatches_type   = 'yes' === $item['display_swatch'] ? get_term_meta( $term->term_id, 'bpfwe_swatches_type', true ) : '';
										$group_text      = get_term_meta( $term->term_id, 'bpfwe_swatches_group_text', true );
										$swatch_html     = '';
										$separator_html  = '';

										if ( $group_text && 'yes' === $item['display_swatch'] ) {
											$separator_html = '<div class="bpfwe-group-separator" role="separator" aria-label="Group Separator">' . esc_html( $group_text ) . '</div>';
										}

										switch ( $swatches_type ) {
											case 'color':
												$swatches_color = get_term_meta( $term->term_id, 'bpfwe_swatches_color', true );
												if ( $swatches_color ) {
													$swatch_html = '<span style="background-color: ' . esc_attr( $swatches_color ) . '" class="bpfwe-swatch" role="img" aria-label="Color Swatch" title="' . esc_attr( $term->name ) . '"></span> ';
												}
												break;

											case 'image':
												$swatches_image = get_term_meta( $term->term_id, 'bpfwe_swatches_image', true );
												if ( $swatches_image ) {
													$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="Image Swatch" title="' . esc_attr( $term->name ) . '"></span> ';
												}
												break;

											case 'product-cat-image':
												if ( 'product-cat-image' === $swatches_type ) {
													if ( class_exists( 'WooCommerce' ) ) {
														$thumbnail_id   = get_term_meta( $term->term_id, 'thumbnail_id', true );
														$swatches_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
														if ( $swatches_image ) {
															$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="Image Swatch" title="' . esc_attr( $term->name ) . '"></span> ';
														}
													} else {
														$swatch_html = '';
													}
												}
												break;

											case 'button':
												$swatches_button_text = get_term_meta( $term->term_id, 'bpfwe_swatches_button_text', true );
												if ( $swatches_button_text ) {
													$swatch_html = '<span class="bpfwe-swatch bpfwe-swatch-button" role="button" aria-label="Button Swatch" title="' . esc_attr( $swatches_button_text ) . '">' . esc_html( $swatches_button_text ) . '</span> ';
												}
												break;

											default:
												$swatch_html = '';
												break;
										}

										$child_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $term->term_id;
										$child_terms         = get_transient( $child_transient_key );

										// Invalidate cache if editing.
										if ( $is_editor ) {
											delete_transient( $child_transient_key );
											$child_terms = false;
										}

										if ( false === $child_terms || $is_editor ) {
											$args = array(
												'taxonomy' => sanitize_key( $item['filter_by'] ),
												'parent'   => $term->term_id,
												'hide_empty' => $display_empty,
												'fields'   => 'all',
												'update_meta_cache' => false,
											);

											$valid_orderby = [ '', 'name', 'slug', 'count', 'term_group', 'term_order', 'term_id' ];

											if ( ! empty( $item['sort_terms'] ) && in_array( $item['sort_terms'], $valid_orderby, true ) ) {
												$args['orderby'] = $item['sort_terms'];
												$args['order']   = in_array( $item['order'], [ 'ASC', 'DESC' ], true ) ? $item['order'] : 'ASC';
											}

											$child_terms = BPFWE_Helper::bpfwe_get_terms( $args, $filter_query_id, $this );

											if ( $transient_duration > 0 && ! $is_editor ) {
												set_transient( $child_transient_key, $child_terms, $transient_duration );
											}
										}

										// Intersect base terms with allowed IDs.
										if ( $is_facetted && $taxonomy_is_faceted ) {
											$child_terms = array_filter(
												$child_terms,
												function ( $term ) use ( $allowed_term_ids, $display_empty ) {

													// Faceted and this taxonomy participated, but no matches -> clear all.
													if ( empty( $allowed_term_ids ) ) {
														return false;
													}

													if ( ! in_array( absint( $term->term_id ), $allowed_term_ids, true ) ) {
														return false;
													}

													if ( ! $display_empty && 0 === (int) $term->count ) {
														return false;
													}

													return true;
												}
											);
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
													<span>' . wp_kses_post( $swatch_html ) . '<span class="label-text">' . wp_kses_post( $term->name . $show_counter ) . '</span></span>
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
												'data-reset' => array(),
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
							echo '</li>';
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
						<div class="' . esc_attr( $wrapper_classes_tax ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="bpfwe-taxonomy-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $item['hide_label_swatch'] ) . ' ' . esc_attr( $item['hide_input_swatch'] ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';

						foreach ( $hiterms as $key => $hiterm ) {
							$has_children = $is_facetted && 'yes' === $item['show_hierarchy'] && ! empty( get_term_children( $hiterm->term_id, $hiterm->taxonomy ) );

							if ( $is_facetted && $has_children ) {
								$show_counter = ( 'yes' === $item['show_counter'] ) ? ' (–)' : '';
							} else {
								$effective_count = isset( $facetted_term_counts[ $hiterm->term_id ] ) ? $facetted_term_counts[ $hiterm->term_id ] : $hiterm->count;
								$show_counter    = ( 'yes' === $item['show_counter'] ) ? ' (<span class="count" data-reset="' . $effective_count . '">' . $effective_count . '</span>)' : '';
							}

							$swatches_type  = 'yes' === $item['display_swatch'] ? get_term_meta( $hiterm->term_id, 'bpfwe_swatches_type', true ) : '';
							$group_text     = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_group_text', true );
							$swatch_html    = '';
							$separator_html = '';

							if ( $group_text && 'yes' === $item['display_swatch'] ) {
								$separator_html = '<div class="bpfwe-group-separator" role="separator" aria-label="Group Separator">' . esc_html( $group_text ) . '</div>';
							}

							switch ( $swatches_type ) {
								case 'color':
									$swatches_color = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_color', true );
									if ( $swatches_color ) {
										$swatch_html = '<span style="background-color: ' . esc_attr( $swatches_color ) . '" class="bpfwe-swatch" role="img" aria-label="Color Swatch" title="' . esc_attr( $hiterm->name ) . '"></span> ';
									}
									break;

								case 'image':
									$swatches_image = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_image', true );
									if ( $swatches_image ) {
										$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="Image Swatch" title="' . esc_attr( $hiterm->name ) . '"></span> ';
									}
									break;

								case 'product-cat-image':
									if ( 'product-cat-image' === $swatches_type ) {
										if ( class_exists( 'WooCommerce' ) ) {
											$thumbnail_id   = get_term_meta( $hiterm->term_id, 'thumbnail_id', true );
											$swatches_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
											if ( $swatches_image ) {
												$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="Image Swatch" title="' . esc_attr( $hiterm->name ) . '"></span> ';
											}
										} else {
											$swatch_html = '';
										}
									}
									break;

								case 'button':
									$swatches_button_text = get_term_meta( $hiterm->term_id, 'bpfwe_swatches_button_text', true );
									if ( $swatches_button_text ) {
										$swatch_html = '<span class="bpfwe-swatch bpfwe-swatch-button" role="button" aria-label="Button Swatch" title="' . esc_attr( $swatches_button_text ) . '">' . esc_html( $swatches_button_text ) . '</span> ';
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
								<span>' . wp_kses_post( $swatch_html ) . '<span class="label-text">' . wp_kses_post( $hiterm->name . $show_counter ) . '</span></span>
								<span class="low-group-trigger" role="button" aria-expanded="false">+</span>
								</label>
							';

							if ( 'yes' === $item['show_hierarchy'] ) {
								$terms_stack            = array();
								$lowterms_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $hiterm->term_id;
								$lowterms               = get_transient( $lowterms_transient_key );

								// Invalidate cache if editing.
								if ( $is_editor ) {
									delete_transient( $lowterms_transient_key );
									$lowterms = false;
								}

								if ( false === $lowterms || $is_editor ) {
									$args = array(
										'taxonomy'   => sanitize_key( $item['filter_by'] ),
										'parent'     => $hiterm->term_id,
										'hide_empty' => $display_empty,
										'fields'     => 'all',
										'update_meta_cache' => false,
									);

									$valid_orderby = [ '', 'name', 'slug', 'count', 'term_group', 'term_order', 'term_id' ];
									if ( ! empty( $item['sort_terms'] ) && in_array( $item['sort_terms'], $valid_orderby, true ) ) {
										$args['orderby'] = $item['sort_terms'];
										$args['order']   = in_array( $item['order'], [ 'ASC', 'DESC' ], true ) ? $item['order'] : 'ASC';
									}

									$lowterms = BPFWE_Helper::bpfwe_get_terms( $args, $filter_query_id, $this );

									if ( $transient_duration > 0 && ! $is_editor ) {
										set_transient( $lowterms_transient_key, $lowterms, $transient_duration );
									}
								}

								// Intersect base terms with allowed IDs.
								if ( $is_facetted && $taxonomy_is_faceted ) {
									$lowterms = array_filter(
										$lowterms,
										function ( $term ) use ( $allowed_term_ids, $display_empty ) {

											// Faceted and this taxonomy participated, but no matches -> clear all.
											if ( empty( $allowed_term_ids ) ) {
												return false;
											}

											if ( ! in_array( absint( $term->term_id ), $allowed_term_ids, true ) ) {
												return false;
											}

											if ( ! $display_empty && 0 === (int) $term->count ) {
												return false;
											}

											return true;
										}
									);
								}

								if ( $lowterms ) {

									foreach ( array_reverse( $lowterms ) as $lowterm ) {
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

										$effective_count = isset( $facetted_term_counts[ $term->term_id ] ) ? $facetted_term_counts[ $term->term_id ] : $term->count;
										$show_counter    = ( 'yes' === $item['show_counter'] ) ? ' (<span class="count" data-reset="' . $effective_count . '">' . $effective_count . '</span>)' : '';
										$swatches_type   = 'yes' === $item['display_swatch'] ? get_term_meta( $term->term_id, 'bpfwe_swatches_type', true ) : '';
										$group_text      = get_term_meta( $term->term_id, 'bpfwe_swatches_group_text', true );
										$swatch_html     = '';
										$separator_html  = '';

										if ( $group_text && 'yes' === $item['display_swatch'] ) {
											$separator_html = '<div class="bpfwe-group-separator" role="separator" aria-label="Group Separator">' . esc_html( $group_text ) . '</div>';
										}

										switch ( $swatches_type ) {
											case 'color':
												$swatches_color = get_term_meta( $term->term_id, 'bpfwe_swatches_color', true );
												if ( $swatches_color ) {
													$swatch_html = '<span style="background-color: ' . esc_attr( $swatches_color ) . '" class="bpfwe-swatch" role="img" aria-label="Color Swatch" title="' . esc_attr( $term->name ) . '"></span> ';
												}
												break;

											case 'image':
												$swatches_image = get_term_meta( $term->term_id, 'bpfwe_swatches_image', true );
												if ( $swatches_image ) {
													$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="Image Swatch" title="' . esc_attr( $term->name ) . '"></span> ';
												}
												break;

											case 'product-cat-image':
												if ( 'product-cat-image' === $swatches_type ) {
													if ( class_exists( 'WooCommerce' ) ) {
														$thumbnail_id   = get_term_meta( $term->term_id, 'thumbnail_id', true );
														$swatches_image = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';
														if ( $swatches_image ) {
															$swatch_html = '<span style="background-image: url(' . esc_url( $swatches_image ) . ');" class="bpfwe-swatch" role="img" aria-label="Image Swatch" title="' . esc_attr( $term->name ) . '"></span> ';
														}
													} else {
														$swatch_html = '';
													}
												}
												break;

											case 'button':
												$swatches_button_text = get_term_meta( $term->term_id, 'bpfwe_swatches_button_text', true );
												if ( $swatches_button_text ) {
													$swatch_html = '<span class="bpfwe-swatch bpfwe-swatch-button" role="button" aria-label="Button Swatch" title="' . esc_attr( $swatches_button_text ) . '">' . esc_html( $swatches_button_text ) . '</span> ';
												}
												break;

											default:
												$swatch_html = '';
												break;
										}

										$child_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $term->term_id;
										$child_terms         = get_transient( $child_transient_key );

										// Invalidate cache if editing.
										if ( $is_editor ) {
											delete_transient( $child_transient_key );
											$child_terms = false;
										}

										if ( false === $child_terms || $is_editor ) {
											$args = array(
												'taxonomy' => sanitize_key( $item['filter_by'] ),
												'parent'   => $term->term_id,
												'hide_empty' => $display_empty,
												'fields'   => 'all',
												'update_meta_cache' => false,
											);

											$valid_orderby = [ '', 'name', 'slug', 'count', 'term_group', 'term_order', 'term_id' ];
											if ( ! empty( $item['sort_terms'] ) && in_array( $item['sort_terms'], $valid_orderby, true ) ) {
												$args['orderby'] = $item['sort_terms'];
												$args['order']   = in_array( $item['order'], [ 'ASC', 'DESC' ], true ) ? $item['order'] : 'ASC';
											}

											$child_terms = BPFWE_Helper::bpfwe_get_terms( $args, $filter_query_id, $this );

											if ( $transient_duration > 0 && ! $is_editor ) {
												set_transient( $child_transient_key, $child_terms, $transient_duration );
											}
										}

										// Intersect base terms with allowed IDs.
										if ( $is_facetted && $taxonomy_is_faceted ) {
											$child_terms = array_filter(
												$child_terms,
												function ( $term ) use ( $allowed_term_ids, $display_empty ) {

													// Faceted and this taxonomy participated, but no matches -> clear all.
													if ( empty( $allowed_term_ids ) ) {
														return false;
													}

													if ( ! in_array( absint( $term->term_id ), $allowed_term_ids, true ) ) {
														return false;
													}

													if ( ! $display_empty && 0 === (int) $term->count ) {
														return false;
													}

													return true;
												}
											);
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
													<span>' . wp_kses_post( $swatch_html ) . '<span class="label-text">' . wp_kses_post( $term->name . $show_counter ) . '</span></span>
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
												'data-reset' => array(),
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
							echo '</li>';
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
						<div class="' . esc_attr( $wrapper_classes_tax ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="bpfwe-taxonomy-wrapper" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<ul class="taxonomy-filter">
						';

						if ( 'yes' === $item['select_all'] ) {
							$select_all_label = ! empty( $item['select_all_label'] ) ? $item['select_all_label'] : esc_html__( 'Select All', 'better-post-filter-widgets-for-elementor' );

							echo '
							<li class="parent-term select-all-term">
								<label>
									<span class="bpfwe-filter-item bpfwe-select-all" data-taxonomy="' . esc_attr( $item['filter_by'] ) . '">
										<span><span class="label-text">' . esc_html( $select_all_label ) . '</span></span>
									</span>
								</label>
							</li>
							';
						}

						foreach ( $hiterms as $key => $hiterm ) {
							$has_children = $is_facetted && 'yes' === $item['show_hierarchy'] && ! empty( get_term_children( $hiterm->term_id, $hiterm->taxonomy ) );

							if ( $is_facetted && $has_children ) {
								$show_counter = ( 'yes' === $item['show_counter'] ) ? ' (–)' : '';
							} else {
								$effective_count = isset( $facetted_term_counts[ $hiterm->term_id ] ) ? $facetted_term_counts[ $hiterm->term_id ] : $hiterm->count;
								$show_counter    = ( 'yes' === $item['show_counter'] ) ? ' (<span class="count" data-reset="' . $effective_count . '">' . $effective_count . '</span>)' : '';
							}

							echo '
							<li class="list-style">
							<label for="' . esc_attr( $hiterm->slug ) . '-' . esc_attr( $widget_id ) . '">
							<input type="checkbox" id="' . esc_attr( $hiterm->slug ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['filter_by'] ) . '" data-taxonomy="' . esc_attr( $hiterm->taxonomy ) . '" value="' . esc_attr( $hiterm->term_id ) . '" />
							<span>' . wp_kses_post( $hiterm->name . $show_counter ) . '</span>
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
						<div class="' . esc_attr( $wrapper_classes_tax ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="bpfwe-taxonomy-wrapper ' . esc_attr( $select2_class ) . '" data-logic="' . esc_attr( $item['filter_logic'] ) . '">
						<select id="' . esc_attr( $item['filter_by'] ) . '-' . esc_attr( $widget_id ) . '" name="' . esc_attr( $item['filter_by'] ) . '-' . esc_attr( $widget_id ) . '">' . wp_kses( $default_val, array( 'option' => array( 'value' => array() ) ) );

						if ( 'yes' === $item['show_hierarchy'] ) {
							$terms_stack = array();
							foreach ( $hiterms as $hiterm ) {
								$terms_stack[] = array(
									'term'  => $hiterm,
									'depth' => 0,
								);
							}

							$terms_stack = array_reverse( $terms_stack );

							while ( ! empty( $terms_stack ) ) {
								$current = array_pop( $terms_stack );
								$term    = $current['term'];
								$depth   = $current['depth'];

								$prefix       = str_repeat( '— ', $depth );
								$has_children = $is_facetted && 'yes' === $item['show_hierarchy'] && ! empty( get_term_children( $term->term_id, $term->taxonomy ) );

								if ( $is_facetted && $has_children ) {
									$show_counter = ( 'yes' === $item['show_counter'] ) ? ' (–)' : '';
								} else {
									$effective_count = isset( $facetted_term_counts[ $term->term_id ] ) ? $facetted_term_counts[ $term->term_id ] : $term->count;
									$show_counter    = ( 'yes' === $item['show_counter'] ) ? ' (<span class="count" data-reset="' . $effective_count . '">' . $effective_count . '</span>)' : '';
								}

								echo '<option data-bold="true" data-category="' . esc_attr( $term->term_id ) . '" data-taxonomy="' . esc_attr( $term->taxonomy ) . '" value="' . esc_attr( $term->term_id ) . '">' . wp_kses_post( $prefix . $term->name . $show_counter ) . '</option>';

								$args = array(
									'taxonomy'          => sanitize_key( $item['filter_by'] ),
									'parent'            => $term->term_id,
									'hide_empty'        => $display_empty,
									'fields'            => 'all',
									'update_meta_cache' => false,
								);

								$valid_orderby = [ '', 'name', 'slug', 'count', 'term_group', 'term_order', 'term_id' ];

								if ( ! empty( $item['sort_terms'] ) && in_array( $item['sort_terms'], $valid_orderby, true ) ) {
									$args['orderby'] = $item['sort_terms'];
									$args['order']   = in_array( $item['order'], [ 'ASC', 'DESC' ], true ) ? $item['order'] : 'ASC';
								}

								$child_terms = BPFWE_Helper::bpfwe_get_terms( $args, $filter_query_id, $this );

								// Intersect base terms with allowed IDs.
								if ( $is_facetted && $taxonomy_is_faceted ) {
									$child_terms = array_filter(
										$child_terms,
										function ( $term ) use ( $allowed_term_ids, $display_empty ) {

											// Faceted and this taxonomy participated, but no matches -> clear all.
											if ( empty( $allowed_term_ids ) ) {
												return false;
											}

											if ( ! in_array( absint( $term->term_id ), $allowed_term_ids, true ) ) {
												return false;
											}

											if ( ! $display_empty && 0 === (int) $term->count ) {
												return false;
											}

											return true;
										}
									);
								}

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
								$effective_count = isset( $facetted_term_counts[ $hiterm->term_id ] ) ? $facetted_term_counts[ $hiterm->term_id ] : $hiterm->count;
								$show_counter    = ( 'yes' === $item['show_counter'] ) ? ' (<span class="count" data-reset="' . $effective_count . '">' . $effective_count . '</span>)' : '';
								echo '<option data-count="' . absint( $effective_count ) . '" data-reset="' . absint( $effective_count ) . '" data-category="' . esc_attr( $hiterm->term_id ) . '" data-taxonomy="' . esc_attr( $hiterm->taxonomy ) . '" value="' . esc_attr( $hiterm->term_id ) . '">' . wp_kses_post( $hiterm->name . $show_counter ) . '</option>';
							}
						}

						echo '
						</select>
						</div>
						</div>
						';
					}
				}

				if ( 'Custom Field' === $item['select_filter'] || 'Relational' === $item['select_filter'] ) {

					$is_relational = 'Relational' === $item['select_filter'];

					if ( 'input' === $item['filter_style_cf'] ) {
						$placeholder = esc_html( $item['text_input_placeholder'] ) ? esc_html( $item['text_input_placeholder'] ) : '';
						echo '
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="bpfwe-custom-field-wrapper" data-logic="OR">
						<input type="text" class="input-text" id="input-text-' . esc_attr( $item['meta_key'] ) . '-' . esc_attr( $widget_id ) . '" name="post_meta" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" placeholder="' . esc_html( $placeholder ) . '">
						</div>
						</div>
						';
					}

					if ( ! empty( $item['meta_key'] ) ) {
						$meta_terms_transient_key = 'filter_widget_meta_terms_' . $item['meta_key'];
						$terms                    = get_transient( $meta_terms_transient_key );

						// Invalidate cache if editing.
						if ( $is_editor ) {
							delete_transient( $meta_terms_transient_key );
							$terms = false;
						}

						// Bypass transient for users with editing capabilities or if transient doesn't exist.
						if ( false === $terms || $is_editor || $is_facetted ) {

							$facet_post_ids = get_transient( 'bpfwe_filter_post_ids' );
							if ( $is_facetted && ! empty( $facet_post_ids ) && is_array( $facet_post_ids ) ) {
								$post_ids = $facet_post_ids;
							} else {
								$all_posts_args = array(
									'posts_per_page' => -1,
									'post_type'      => 'targeted_widget' === $settings['filter_post_type'] ? 'any' : $settings['filter_post_type'],
									'no_found_rows'  => true,
									'fields'         => 'ids',
									'meta_key'       => $item['meta_key'],
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

								$post_ids = get_posts( $all_posts_args );
							}

							if ( ! empty( $post_ids ) ) {
								global $wpdb;

								$meta_key   = $item['meta_key'];
								$terms_data = array();

								if ( ! $is_relational ) {
									$cache_group = 'bpfwe_meta_counts';
									$cache_key   = md5( $meta_key . '_' . implode( ',', $post_ids ) );

									// Try to get cached results, invalidate cache if editing.
									if ( $is_editor || $is_facetted ) {
										wp_cache_delete( $cache_key, $cache_group );
										$results = false;
									} else {
										$results = wp_cache_get( $cache_key, $cache_group );
									}

									if ( false === $results ) {
										$results = $wpdb->get_results(
											$wpdb->prepare(
												"SELECT meta_value, COUNT(*) as count 
												FROM {$wpdb->postmeta} 
												WHERE meta_key = %s 
												AND post_id IN (" . implode( ',', array_fill( 0, count( $post_ids ), '%d' ) ) . ")
												AND meta_value != '' 
												GROUP BY meta_value",
												array_merge( [ $meta_key ], $post_ids )
											)
										);

										if ( ! $is_editor || ! $is_facetted ) {
											wp_cache_set( $cache_key, $results, $cache_group, 12 * HOUR_IN_SECONDS );
										}
									}
									foreach ( $results as $result ) {
										$meta_value = $result->meta_value;

										if ( ! is_scalar( $meta_value ) || is_serialized( $meta_value ) ) {
											$terms_data = array(
												'This field appears to be relational. Please use the "Relation Field" option for this field.' => '',
											);
											break;
										}

										$terms_data[ $meta_value ] = (int) $result->count;
									}
								} else {
									$cache_group = 'bpfwe_relational_counts';
									$cache_key   = md5( $meta_key . '_' . implode( ',', $post_ids ) );

									// Try to get cached results, invalidate cache if editing.
									if ( $is_editor || $is_facetted ) {
										wp_cache_delete( $cache_key, $cache_group );
										$results = false;
									} else {
										$results = wp_cache_get( $cache_key, $cache_group );
									}

									if ( false === $results ) {
										$results = $wpdb->get_results(
											$wpdb->prepare(
												"SELECT meta_value 
												FROM {$wpdb->postmeta}
												WHERE meta_key = %s
												AND post_id IN (" . implode( ',', array_fill( 0, count( $post_ids ), '%d' ) ) . ")
												AND meta_value != ''",
												array_merge( array( $meta_key ), $post_ids )
											)
										);

										if ( ! $is_editor || ! $is_facetted ) {
											wp_cache_set( $cache_key, $results, $cache_group, 12 * HOUR_IN_SECONDS );
										}
									}

									foreach ( $results as $result ) {
										$meta_value = maybe_unserialize( $result->meta_value );

										// Normalize single or multiple related IDs.
										if ( is_array( $meta_value ) ) {
											$related_ids = array_filter( array_map( 'absint', $meta_value ) );
										} elseif ( is_numeric( $meta_value ) ) {
											$related_ids = array( absint( $meta_value ) );
										} else {
											continue;
										}

										$type = isset( $item['relational_field_type'] ) ? sanitize_text_field( $item['relational_field_type'] ) : '';

										foreach ( $related_ids as $related_id ) {
											if ( empty( $related_id ) ) {
												continue;
											}

											$related_id = absint( $related_id );
											$label      = (string) $related_id;

											switch ( $type ) {
												case 'post':
													$post_obj = get_post( $related_id );
													if ( $post_obj && ! is_wp_error( $post_obj ) ) {
														$label = $post_obj->post_title;
													}
													break;

												case 'user':
													$user_obj = get_userdata( $related_id );
													if ( $user_obj ) {
														$label = $user_obj->display_name;
													}
													break;

												default:
													// Auto detection fallback.
													$post_obj = get_post( $related_id );
													$user_obj = get_userdata( $related_id );

													if ( $post_obj && ! is_wp_error( $post_obj ) ) {
														$label = $post_obj->post_title;
													} elseif ( $user_obj ) {
														$label = $user_obj->display_name;
													}
													break;
											}

											// Add or increment count.
											if ( isset( $terms_data[ $related_id ] ) ) {
												$terms_data[ $related_id ]['count'] += 1;
											} else {
												$terms_data[ $related_id ] = array(
													'label' => $label,
													'count' => 1,
												);
											}
										}
									}
								}

								if ( empty( $terms_data ) ) {
									if ( ! $is_relational ) {
										$terms_data = array(
											'No terms found for this field.' => '',
										);
									} else {
										$terms_data = array(
											'none' => array(
												'label' => esc_html__( 'No related items found.', 'better-post-filter-widgets-for-elementor' ),
												'count' => 0,
											),
										);
									}
								} elseif ( ! $is_relational ) {
									if ( 'DESC' === strtoupper( $item['order'] ) ) {
										krsort( $terms_data );
									} else {
										ksort( $terms_data );
									}
								} elseif ( 'DESC' === strtoupper( $item['order'] ) ) {
										uasort(
											$terms_data,
											function ( $a, $b ) {
												return strcmp( $b['label'], $a['label'] );
											}
										);
								} else {
									uasort(
										$terms_data,
										function ( $a, $b ) {
											return strcmp( $a['label'], $b['label'] );
										}
									);
								}

								if ( ! $is_relational ) {
									$temp = array();
									foreach ( $terms_data as $value => $count ) {
										$temp[ $value ] = array(
											'label' => $value,
											'count' => $count,
										);
									}
									$terms_data = $temp;
								}

								$terms_data = apply_filters( 'bpfwe/get_' . ( $is_relational ? 'relational' : 'meta' ) . "_terms/{$filter_query_id}", $terms_data, $this, $item );

								if ( $transient_duration > 0 && ! $is_editor && ! $is_facetted ) {
									set_transient( $meta_terms_transient_key, $terms_data, $transient_duration );
								}

								$terms = $terms_data;
							}
						}
					}

					$custom_field_class = $is_relational ? 'bpfwe-custom-field-relational-wrapper' : 'bpfwe-custom-field-wrapper';

					if ( 'checkboxes' === $item['filter_style'] || 'checkboxes' === $item['filter_style_cf'] ) {
						$term_index = 0;
						echo '
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="' . esc_attr( $custom_field_class ) . ' elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $item['hide_label_swatch'] ) . ' ' . esc_attr( $item['hide_input_swatch'] ) . '" data-logic="' . esc_attr( ! empty( $item['filter_logic'] ) ? strtoupper( $item['filter_logic'] ) : 'OR' ) . '">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';

						if ( 'yes' === $item['select_all'] ) {
							$select_all_label = ! empty( $item['select_all_label'] ) ? $item['select_all_label'] : esc_html__( 'Select All', 'better-post-filter-widgets-for-elementor' );

							echo '
							<li class="parent-term select-all-term">
								<label>
									<span class="bpfwe-filter-item bpfwe-select-all" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '">
										<span><span class="label-text">' . esc_html( $select_all_label ) . '</span></span>
									</span>
								</label>
							</li>
							';
						}

						if ( ! empty( $terms ) ) {
							foreach ( $terms as $term_value => $term_data ) {
								$label = $term_data['label'];
								$count = $term_data['count'];

								$format_type = $item['format_type'] ?? 'none';
								$args        = array();

								if ( 'date' === $format_type ) {
									$args['date_format'] = $item['date_format'] ?? get_option( 'date_format' );
								} elseif ( 'number' === $format_type ) {
									$args['decimals'] = isset( $item['number_decimals'] ) ? (int) $item['number_decimals'] : 0;
									$args['suffix']   = $item['number_suffix'] ?? '';
								} elseif ( 'text' === $format_type ) {
									$args['text_case'] = $item['text_case'] ?? 'as_is';
								} elseif ( 'custom_pattern' === $format_type ) {
									$args['pattern'] = $item['custom_pattern'] ?? '{value}';
								}

								if ( $is_relational ) {
									$formatted_value = esc_html( $label );
								} else {
									$formatted_value = BPFWE_Helper::format_meta_value( $label, $format_type, $args );
								}

								if ( ! empty( $item['show_counter'] ) && 'yes' === $item['show_counter'] && is_numeric( $count ) ) {
									$formatted_value .= ' (<span class="count" data-reset="' . intval( $count ) . '">' . intval( $count ) . '</span>)';
								}

								$toggleable_class = ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) ? 'toggleable' : '';

								echo '
								<li class="' . esc_attr( $toggleable_class ) . '">
									<label for="' . esc_attr( $term_value ) . '-' . esc_attr( $widget_id ) . '">
										<input type="checkbox" id="' . esc_attr( $term_value ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $term_value ) . '" />
										<span class="label-text">' . wp_kses_post( $formatted_value ) . '</span>
									</label>
								</li>';

								++$term_index;
							}
						}

						if ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) {
							echo '<li class="more"><span class="label-more">' . esc_html__( 'More...', 'better-post-filter-widgets-for-elementor' ) . '</span><span class="label-less">' . esc_html__( 'Less...', 'better-post-filter-widgets-for-elementor' ) . '</span></li>';
						}

						echo '
						</ul>
						</div>
						</div>
						';
					}

					if ( 'radio' === $item['filter_style'] || 'radio' === $item['filter_style_cf'] ) {
						$term_index = 0;
						echo '
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="' . esc_attr( $custom_field_class ) . ' elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $item['hide_label_swatch'] ) . ' ' . esc_attr( $item['hide_input_swatch'] ) . '" data-logic="OR">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';

						if ( ! empty( $terms ) ) {
							foreach ( $terms as $term_value => $term_data ) {
								$label = $term_data['label'];
								$count = $term_data['count'];

								$format_type = $item['format_type'] ?? 'none';
								$args        = array();

								if ( 'date' === $format_type ) {
									$args['date_format'] = $item['date_format'] ?? get_option( 'date_format' );
								} elseif ( 'number' === $format_type ) {
									$args['decimals'] = isset( $item['number_decimals'] ) ? (int) $item['number_decimals'] : 0;
									$args['suffix']   = $item['number_suffix'] ?? '';
								} elseif ( 'text' === $format_type ) {
									$args['text_case'] = $item['text_case'] ?? 'as_is';
								} elseif ( 'custom_pattern' === $format_type ) {
									$args['pattern'] = $item['custom_pattern'] ?? '{value}';
								}

								if ( $is_relational ) {
									$formatted_value = esc_html( $label );
								} else {
									$formatted_value = BPFWE_Helper::format_meta_value( $label, $format_type, $args );
								}

								if ( ! empty( $item['show_counter'] ) && 'yes' === $item['show_counter'] && is_numeric( $count ) ) {
									$formatted_value .= ' (<span class="count" data-reset="' . intval( $count ) . '">' . intval( $count ) . '</span>)';
								}

								$toggleable_class = ( $term_index > 5 && 'show-toggle' === $item['show_toggle'] ) ? 'toggleable' : '';

								echo '
								<li class="' . esc_attr( $toggleable_class ) . '">
									<label for="' . esc_attr( $term_value ) . '-' . esc_attr( $widget_id ) . '">
										<input type="radio" id="' . esc_attr( $term_value ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $term_value ) . '" />
										<span class="label-text">' . wp_kses_post( $formatted_value ) . '</span>
									</label>
								</li>';

								++$term_index;
							}
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
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="' . esc_attr( $custom_field_class ) . ' elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" data-logic="OR">
						<ul class="taxonomy-filter ' . esc_attr( $item['show_toggle'] ) . '">
						';

						if ( 'yes' === $item['select_all'] ) {
							$select_all_label = ! empty( $item['select_all_label'] ) ? $item['select_all_label'] : esc_html__( 'Select All', 'better-post-filter-widgets-for-elementor' );

							echo '
							<li class="parent-term select-all-term">
								<label>
									<span class="bpfwe-filter-item bpfwe-select-all" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '">
										<span><span class="label-text">' . esc_html( $select_all_label ) . '</span></span>
									</span>
								</label>
							</li>';
						}

						if ( ! empty( $terms ) ) {
							foreach ( $terms as $term_value => $term_data ) {
								$label = $term_data['label'];
								$count = $term_data['count'];

								$format_type = $item['format_type'] ?? 'none';
								$args        = array();

								if ( 'date' === $format_type ) {
									$args['date_format'] = $item['date_format'] ?? get_option( 'date_format' );
								} elseif ( 'number' === $format_type ) {
									$args['decimals'] = isset( $item['number_decimals'] ) ? (int) $item['number_decimals'] : 0;
									$args['suffix']   = $item['number_suffix'] ?? '';
								} elseif ( 'text' === $format_type ) {
									$args['text_case'] = $item['text_case'] ?? 'as_is';
								} elseif ( 'custom_pattern' === $format_type ) {
									$args['pattern'] = $item['custom_pattern'] ?? '{value}';
								}

								if ( $is_relational ) {
									$formatted_value = esc_html( $label );
								} else {
									$formatted_value = BPFWE_Helper::format_meta_value( $label, $format_type, $args );
								}

								if ( ! empty( $item['show_counter'] ) && 'yes' === $item['show_counter'] && is_numeric( $count ) ) {
									$formatted_value .= ' (<span class="count" data-reset="' . intval( $count ) . '">' . intval( $count ) . '</span>)';
								}

								echo '
								<li class="list-style">
									<label for="' . esc_attr( $term_value ) . '-' . esc_attr( $widget_id ) . '">
										<input type="checkbox" id="' . esc_attr( $term_value ) . '-' . esc_attr( $widget_id ) . '" class="bpfwe-filter-item" name="' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $term_value ) . '" />
										<span>' . wp_kses_post( $formatted_value ) . '</span>
									</label>
								</li>';
							}
						}

						echo '
						</ul>
						</div>
						</div>';
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
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="' . esc_attr( $custom_field_class ) . ' ' . esc_attr( $select2_class ) . '" data-logic="OR">
						<select id="' . esc_attr( $item['meta_key'] ) . '-' . esc_attr( $widget_id ) . '" name="' . esc_attr( $item['meta_key'] ) . '-' . esc_attr( $widget_id ) . '">' . wp_kses( $default_val, array( 'option' => array( 'value' => array() ) ) );

						if ( ! empty( $terms ) ) {
							foreach ( $terms as $term_value => $term_data ) {
								$label = $term_data['label'];
								$count = $term_data['count'];

								$format_type = $item['format_type'] ?? 'none';
								$args        = array();

								if ( 'date' === $format_type ) {
									$args['date_format'] = $item['date_format'] ?? get_option( 'date_format' );
								} elseif ( 'number' === $format_type ) {
									$args['decimals'] = isset( $item['number_decimals'] ) ? (int) $item['number_decimals'] : 0;
									$args['suffix']   = $item['number_suffix'] ?? '';
								} elseif ( 'text' === $format_type ) {
									$args['text_case'] = $item['text_case'] ?? 'as_is';
								} elseif ( 'custom_pattern' === $format_type ) {
									$args['pattern'] = $item['custom_pattern'] ?? '{value}';
								}

								if ( $is_relational ) {
									$formatted_value = esc_html( $label );
								} else {
									$formatted_value = BPFWE_Helper::format_meta_value( $label, $format_type, $args );
								}

								if ( ! empty( $item['show_counter'] ) && 'yes' === $item['show_counter'] && is_numeric( $count ) ) {
									$formatted_value .= ' (<span class="count" data-reset="' . intval( $count ) . '">' . intval( $count ) . '</span>)';
								}

								echo '<option data-category="' . esc_attr( $term_value ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $term_value ) . '">' . wp_kses_post( $formatted_value ) . '</option>';
							}
						}

						echo '
						</select>
						</div>
						</div>';
					}
				}

				if ( 'Numeric' === $item['select_filter'] ) {
					$terms = array();

					if ( ! empty( $item['meta_key'] ) ) {
						$numeric_transient_key = 'filter_widget_numeric_' . $item['meta_key'];
						$terms                 = get_transient( $numeric_transient_key );

						// Invalidate cache if editing.
						if ( $is_editor ) {
							delete_transient( $numeric_transient_key );
							$terms = false;
						}

						// Bypass transient for users with editing capabilities or if transient doesn't exist.
						if ( false === $terms || $is_editor || $is_facetted ) {

							$facet_post_ids = get_transient( 'bpfwe_filter_post_ids' );
							if ( $is_facetted && ! empty( $facet_post_ids ) && is_array( $facet_post_ids ) ) {
								$post_ids = $facet_post_ids;
							} else {
								$all_posts_args = array(
									'posts_per_page' => -1,
									'post_type'      => 'targeted_widget' === $settings['filter_post_type'] ? 'any' : $settings['filter_post_type'],
									'no_found_rows'  => true,
									'fields'         => 'ids',
									'meta_key'       => $item['meta_key'],
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

									// Modify query for author archive.
									if ( 'author' === $archive_type && $queried_object instanceof WP_User ) {
										$all_posts_args['author'] = $queried_object->ID;
									}

									// Modify query for taxonomy archive.
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

								$post_ids = get_posts( $all_posts_args );
							}

							if ( ! empty( $post_ids ) ) {
								global $wpdb;
								$meta_key = $item['meta_key'];

								$cache_group = 'bpfwe_numeric_counts';
								$cache_key   = md5( $meta_key . '_' . implode( ',', $post_ids ) );

								// Try to get cached results, invalidate cache if editing.
								if ( $is_editor || $is_facetted ) {
									wp_cache_delete( $cache_key, $cache_group );
									$results = false;
								} else {
									$results = wp_cache_get( $cache_key, $cache_group );
								}

								if ( false === $results ) {
									$results = $wpdb->get_col(
										$wpdb->prepare(
											"SELECT DISTINCT CAST(meta_value AS DECIMAL(20,6)) as num_val
											FROM {$wpdb->postmeta}
											WHERE meta_key = %s
											AND post_id IN (" . implode( ',', array_fill( 0, count( $post_ids ), '%d' ) ) . ")
											AND meta_value REGEXP '^[0-9]+(\.[0-9]+)?$'
											ORDER BY num_val ASC",
											array_merge( [ $meta_key ], $post_ids )
										)
									);

									if ( ! $is_editor || ! $is_facetted ) {
										wp_cache_set( $cache_key, $results, $cache_group, 12 * HOUR_IN_SECONDS );
									}
								}

								$terms = array_filter( array_map( 'floatval', $results ) );

								if ( empty( $terms ) ) {
									$terms = array(
										0 => esc_html__( 'No numeric values found for this field.', 'better-post-filter-widgets-for-elementor' ),
									);
								}

								if ( 'DESC' === strtoupper( $item['order'] ) ) {
									rsort( $terms );
								} else {
									sort( $terms );
								}

								$terms = apply_filters( "bpfwe/get_numeric_meta_terms/{$filter_query_id}", $terms, $this, $item );

								if ( $transient_duration > 0 && ! $is_editor && ! $is_facetted ) {
									set_transient( $numeric_transient_key, $terms, $transient_duration );
								}
							}
						}
					}

					if ( 'range' === $item['filter_style_numeric'] ) {
						if ( empty( $terms ) || ! is_array( $terms ) ) {
							$min_value = 0;
							$max_value = 0;
						} else {
							$min_value = floatval( min( $terms ) );
							$max_value = floatval( max( $terms ) );
						}

						echo '
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
							' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>'
							);

						if ( ! empty( $item['visual_range'] ) && 'yes' === $item['visual_range'] ) {
							$max_icons = ! empty( $item['visual_range_max_icons'] ) ? absint( $item['visual_range_max_icons'] ) : 5;

							$inclusive = ! empty( $item['visual_range_inclusive'] );

							// Normalize actual values into [1, $max_icons].
							$min_value = floatval( $min_value );
							$max_value = floatval( $max_value );

							// Round actuals.
							$min_rounded = floor( $min_value );
							$max_rounded = ceil( $max_value );

							// If the real range is narrower than the number of icons, extend it.
							$visual_min = 1;
							$step       = ( $max_value - $visual_min ) / $max_icons;
							$fixed_min  = $inclusive ? min( 1, $min_value ) : $visual_min;

							$buckets = [];

							for ( $i = 0; $i < $max_icons; $i++ ) {
								$bucket_min = $visual_min + ( $i * $step );
								$bucket_max = $bucket_min + $step;

								$buckets[] = [
									'min'   => $bucket_min,
									'max'   => $bucket_max,
									'value' => $i + 1,
								];
							}

							echo '<div class="bpfwe-visual-range-wrapper" data-logic="OR" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" data-min="' . esc_attr( $visual_min ) . '" data-max="' . esc_attr( $bucket_max ) . '">';

							for ( $i = $max_icons - 1; $i >= 0; $i-- ) {
								$bucket = $buckets[ $i ];

								// Skip first bucket if its min < 1.
								if ( 0 === $i && 1 > $bucket['min'] ) {
									continue;
								}

								$icon_normal   = BPFWE_Helper::bpfwe_get_icons( $item['visual_range_icon_normal'] );
								$icon_selected = BPFWE_Helper::bpfwe_get_icons( $item['visual_range_icon_selected'] );

								if ( $inclusive ) {
									$bucket['min'] = $fixed_min;
								}

								echo '<input type="radio" id="' . esc_attr( $item['meta_key'] . '-' . $bucket['value'] ) . '" name="' . esc_attr( $item['meta_key'] ) . '" value="' . esc_attr( $bucket['value'] ) . '" data-min="' . esc_attr( $bucket['min'] ) . '" data-max="' . esc_attr( $bucket['max'] ) . '" />';

								echo '<label for="' . esc_attr( $item['meta_key'] . '-' . $bucket['value'] ) . '" class="bpfwe-visual-range-option">';
								echo '<span class="bpfwe-visual-icon">';
								echo '<span class="icon-normal">' . BPFWE_Helper::sanitize_and_escape_svg_input( $icon_normal ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo '<span class="icon-selected">' . BPFWE_Helper::sanitize_and_escape_svg_input( $icon_selected ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo '</span></label>';
							}

							echo '</div>';

						} else {
							echo '
							<div class="bpfwe-numeric-wrapper" data-logic="OR">
								<span class="field-wrapper"><span class="before">' . esc_html( $item['insert_before_field'] ) . '</span><input type="number" inputmode="numeric" pattern="[0-9]*" class="bpfwe-filter-range-' . esc_attr( $index ) . '" name="min_' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" data-base-value="' . esc_attr( $min_value ) . '" data-base-min="' . esc_attr( $min_value ) . '" data-base-max="' . esc_attr( $max_value ) . '" step="1" min="' . esc_attr( $min_value ) . '" max="' . esc_attr( $max_value ) . '" value="' . esc_attr( $min_value ) . '"></span>
								<span class="field-wrapper"><span class="before">' . esc_html( $item['insert_before_field'] ) . '</span><input type="number" inputmode="numeric" pattern="[0-9]*" class="bpfwe-filter-range-' . esc_attr( $index ) . '" name="max_' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" data-base-value="' . esc_attr( $max_value ) . '" data-base-min="' . esc_attr( $min_value ) . '" data-base-max="' . esc_attr( $max_value ) . '" step="1" min="' . esc_attr( $min_value ) . '" max="' . esc_attr( $max_value ) . '" value="' . esc_attr( $max_value ) . '"></span>
							</div>
							';
						}

						echo '</div>';
					}

					if ( 'input' === $item['filter_style_numeric'] ) {
						$min_placeholder = esc_html( $item['min_input_placeholder'] ) ? esc_html( $item['min_input_placeholder'] ) : '';
						$max_placeholder = esc_html( $item['max_input_placeholder'] ) ? esc_html( $item['max_input_placeholder'] ) : '';

						if ( empty( $terms ) || ! is_array( $terms ) ) {
							$min_value = 0;
							$max_value = 0;
						} else {
							$min_value = floatval( min( $terms ) );
							$max_value = floatval( max( $terms ) );
						}

						echo '
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
							' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>'
							);

						echo '
						<div class="bpfwe-numeric-wrapper" data-logic="OR">
							<span class="field-wrapper"><span class="before">' . esc_html( $item['insert_before_field'] ) . '</span><input type="number" inputmode="numeric" pattern="[0-9]*" class="input-val bpfwe-filter-range-' . esc_attr( $index ) . '" name="min_' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" data-base-value="' . esc_attr( $min_value ) . '" placeholder="' . esc_html( $min_placeholder ) . '"></span>
							<span class="field-wrapper"><span class="before">' . esc_html( $item['insert_before_field'] ) . '</span><input type="number" inputmode="numeric" pattern="[0-9]*" class="input-val bpfwe-filter-range-' . esc_attr( $index ) . '" name="max_' . esc_attr( $item['meta_key'] ) . '" data-taxonomy="' . esc_attr( $item['meta_key'] ) . '" data-base-value="' . esc_attr( $max_value ) . '" placeholder="' . esc_html( $max_placeholder ) . '"></span>
						</div>
						';

						echo '</div>';
					}

					if ( 'checkboxes' === $item['filter_style_numeric'] ) {
						echo '
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $item['hide_label_swatch'] ) . ' ' . esc_attr( $item['hide_input_swatch'] ) . '" data-logic="OR">
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
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $item['hide_label_swatch'] ) . ' ' . esc_attr( $item['hide_input_swatch'] ) . '" data-logic="OR">
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
						<div class="' . esc_attr( $wrapper_classes_meta ) . '">
						' . ( ! empty( $item['filter_toggle'] ) && 'yes' === $item['filter_toggle'] ? '<div class="filter-title collapsible' . ( ! empty( $item['filter_toggle_initial_state'] ) && 'yes' === $item['filter_toggle_initial_state'] ? ' start-open' : '' ) . '" data-toggle-id="' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['filter_title'] ) . '</div>' : '<div class="filter-title">' . esc_html( $item['filter_title'] ) . '</div>' ) . '
						<div class="bpfwe-custom-field-wrapper elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" data-logic="OR">
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

		if ( is_user_logged_in() && $is_editor && 'yes' === $settings['enable_query_debug'] ) {
			echo '<div class="query-debug-frame"></div>';
		}
	}
}

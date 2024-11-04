<?php
 
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use BPF\Inc\Classes\BPF_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

class BPF_Filter_Widget extends \Elementor\Widget_Base {

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
		return __( 'Filter Widget', 'bpf-widget' );
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
	
	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['bpf-widget-style', 'bpf-select2-style'];
		}

		$settings = $this->get_settings_for_display();

		foreach ($settings['filter_list'] as $item) {
			$filter_style = $item['filter_style'] ?? '';
			$filter_style_cf = $item['filter_style_cf'] ?? '';

			if ($filter_style === 'select2' || $filter_style_cf === 'select2') {
				return ['bpf-widget-style', 'bpf-select2-style'];
			}
		}

		return ['bpf-widget-style'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['filter-widget-script', 'bpf-select2-script'];
		}

		$settings = $this->get_settings_for_display();

		foreach ($settings['filter_list'] as $item) {
			$filter_style = $item['filter_style'] ?? '';
			$filter_style_cf = $item['filter_style_cf'] ?? '';

			if ($filter_style === 'select2' || $filter_style_cf === 'select2') {
				return ['filter-widget-script', 'bpf-select2-script'];
			}
		}

		return ['filter-widget-script'];
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
				'label' => __( 'Filter Content', 'bpf-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'target_selector',
			[
				'label' => esc_html__( 'Post Widget Target', 'bpf-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '#id, .class', 'bpf-widget' ),
				'frontend_available' => true,
			]
		);
		
        $this->add_control('filter_post_type', [
            'label' => esc_html__('Post Type to Filter', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'post',
            'options' => BPF_Helper::cwm_get_post_types(),
        ]);
		
        $this->add_responsive_control('nb_columns', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Columns', 'bpf-widget'),
            'options' => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
            ],
            'default' => '1',
            'tablet_default' => '1',
            'mobile_default' => '1',
			'separator' => 'after',
            'selectors' => [
                '{{WRAPPER}} .elementor-grid' =>
                    'grid-template-columns: repeat({{VALUE}},1fr)',
            ],
        ]);
		
        $repeater = new Repeater();

        $repeater->start_controls_tabs('field_repeater');
		
        $repeater->start_controls_tab('content', [
            'label' => esc_html__('Content', 'bpf-widget'),
        ]);
		
        $repeater->add_control('filter_title', [
            'label' => esc_html__('Filter Label', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'New Filter',
			'placeholder' => 'New Filter',
			'separator' => 'after',
			'dynamic' => [
				'active' => true,
			],
        ]);
		
        $repeater->add_control('select_filter', [
            'label' => esc_html__('Data Source', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'Taxonomy',
			'options' => [
				'Taxonomy' => esc_html__( 'Taxonomy', 'bpf-widget' ),
				'Custom Field'  => esc_html__( 'Custom Field', 'bpf-widget' ),
				'Numeric' => esc_html__( 'Custom Field (Numeric)', 'bpf-widget' ),
			],
        ]);

        $repeater->add_control('filter_by', [
            'label' => esc_html__('Select a Taxonomy', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'category',
            'options' => BPF_Helper::get_taxonomies_options(),
            'condition' => [
                'select_filter' => 'Taxonomy',
            ],
        ]);
		
        $repeater->add_control('meta_key', [
            'label' => esc_html__('Field Key', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'dynamic' => [
				'active' => false,
			],
            'placeholder' => 'Enter a meta key',
            'label_block' => true,
            'condition' => [
                'select_filter' => [ 'Custom Field', 'Numeric' ],
            ],
        ]);
		
        $repeater->add_control('insert_before_field', [
            'label' => esc_html__('Before', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => [
				'select_filter' => 'Numeric',
                'filter_style_numeric' => 'range',
            ],
        ]);
		
        $repeater->add_control('filter_style_numeric', [
            'label' => esc_html__('Filter Type', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'range',
            'options' => [
				'range' => 'Range',
                'checkboxes' => 'Checkboxes',
                'radio' => 'Radio Buttons',
                'list' => 'Label List',
				'dropdown' => 'Dropdown',
            ],
			'separator' => 'before',
            'condition' => [
                'select_filter' => 'Numeric',
            ],
        ]);
		
        $repeater->add_control('filter_style_cf', [
            'label' => esc_html__('Field Type', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'checkboxes',
            'options' => [
                'checkboxes' => 'Checkboxes',
                'radio' => 'Radio Buttons',
                'list' => 'Label List',
				'dropdown' => 'Dropdown',
				'select2' => 'Select2',
				'input' => 'Input Field',
            ],
			'separator' => 'before',
            'condition' => [
                'select_filter' => 'Custom Field',
            ],
        ]);
		
        $repeater->add_control('multi_select2_cf', [
            'label' => esc_html__('Enable Multiple Select', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'filter_style_cf' => 'select2',
            ],
        ]);
		
        $repeater->add_control('filter_style', [
            'label' => esc_html__('Field Type', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'checkboxes',
            'options' => [
                'checkboxes' => 'Checkboxes',
                'radio' => 'Radio Buttons',
                'list' => 'Label List',
				'dropdown' => 'Dropdown',
				'select2' => 'Select2',
            ],
			'separator' => 'before',
            'condition' => [
                'select_filter!' => ['Numeric','Custom Field'],
            ],
        ]);
		
        $repeater->add_control('multi_select2', [
            'label' => esc_html__('Enable Multiple Select', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'filter_style' => 'select2',
            ],
        ]);
		
		$repeater->add_control('text_input_placeholder', [
			'label' => esc_html__( 'Placeholder', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Search by keywords...', 'bpf-widget' ),
			'placeholder' => esc_html__( 'Search by keywords...', 'bpf-widget' ),
			'condition' => [
				'filter_style' => 'input',
			],
		]);
		
        $repeater->end_controls_tab();

        $repeater->start_controls_tab('field_style', [
            'label' => esc_html__('Advanced', 'bpf-widget'),
        ]);
		
        $repeater->add_control('sort_terms', [
            'label' => esc_html__('Sort By', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'name',
            'options' => [
                'name' => 'Name',
                'slug' => 'Slug',
                'count' => 'Count',
                'term_group' => 'Term Group',
                'term_order' => 'Term Order',
				'term_id' => 'Term ID',
            ],
            'condition' => [
                'select_filter' => 'Taxonomy',
            ],
        ]);
		
        $repeater->add_control('display_empty', [
            'label' => esc_html__('Display Empty Terms', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
			'separator' => 'after',
            'condition' => [
                'select_filter' => 'Taxonomy',
            ],
        ]);
		
        $repeater->add_control('filter_logic', [
            'label' => esc_html__('Group Logic', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'OR',
            'options' => [
				'OR' => 'OR',
                'AND' => 'AND',
				//'IN' => 'IN',
				//'NOT IN' => 'NOT IN',
				//'EXISTS' => 'EXISTS',
				//'NOT EXISTS' => 'NOT EXISTS',
            ],
        ]);
		
        $repeater->add_control('show_counter', [
            'label' => esc_html__('Show Post Count', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
			'separator' => 'before',
            'condition' => [
				'select_filter' => 'Taxonomy',
            ],
        ]);
		
        $repeater->add_control('show_hierarchy', [
            'label' => esc_html__('Show Hierarchy', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'filter_style!' => ['list','dropdown','select2'],
				'select_filter!' => ['Numeric','Custom Field'],
            ],
        ]);
		
        $repeater->add_control('toggle_child', [
            'label' => esc_html__('Toggle Child Terms', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
				'filter_style!' => ['list','dropdown','select2'],
				'show_hierarchy!' => '',
				'select_filter' => 'Taxonomy',
            ],
        ]);
		
        $repeater->add_control('show_toggle', [
            'label' => esc_html__('More/Less', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'filter_style!' => ['list','dropdown','select2'],
				'select_filter!' => 'Numeric',
            ],
        ]);
		
        $repeater->add_control('show_toggle_numeric', [
            'label' => esc_html__('More/Less', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'filter_style_numeric!' => ['list','dropdown','select2','range'],
				'select_filter' => 'Numeric',
            ],
        ]);
		
		$repeater->end_controls_tab();
		
		$repeater->end_controls_tabs();
		
        $this->add_control('filter_list', [
            'label' => esc_html__('Filter List', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{{ filter_title }}}',
        ]);
		
        $this->add_control('group_logic', [
            'label' => esc_html__('Parent Logic', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'AND',
            'options' => [
                'AND' => 'AND',
                'OR' => 'OR',
            ],
			'separator' => 'before',
			'frontend_available' => true,
        ]);
 
		$this->end_controls_section();
		
		//$this->start_controls_section(
		//	'pre_filter_section',
		//	[
		//		'label' => __( 'Pre-Filter ', 'bpf-widget' ),
		//		'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		//	]
		//);
		
        //$this->add_control('post_status', [
        //    'label' => esc_html__('Post Status', 'cwm-widget'),
        //    'type' => \Elementor\Controls_Manager::SELECT2,
        //    'multiple' => true,
        //    'default' => 'publish',
        //    'options' => [
        //        'publish' => 'Published',
        //        'pending' => 'Pending',
        //        'draft' => 'Draft',
        //        'private' => 'Private',
        //        'trash' => 'Trashed',
        //    ],
		//	'frontend_available' => true,
        //]);
		
		//$this->end_controls_section();

		$this->start_controls_section(
			'settings_section',
			[
				'label' => __( 'Additional Options', 'bpf-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
        $this->add_control('dynamic_filtering', [
            'label' => esc_html__('Dynamic Archive Filtering', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
			'separator' => 'before',
            'frontend_available' => true,
        ]);
		
        $this->add_control('show_reset', [
            'label' => esc_html__('Display Reset Button', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
        ]);
		
        $this->add_control('use_submit', [
            'label' => esc_html__('Display Submit Button', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
        ]);
		
        $this->add_control('display_animation', [
            'label' => esc_html__('Display Loading Animation', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
			'separator' => 'before',
			'frontend_available' => true,
        ]);
		
        $this->add_control('scroll_to_top', [
            'label' => esc_html__('Scroll to top', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
			'separator' => 'before',
            'frontend_available' => true,
        ]);
		
        $this->add_control('nothing_found_message', [
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'label' => esc_html__('Nothing Found Message', 'bpf-widget'),
            'rows' => 3,
			'separator' => 'before',
            'default' => __('It seems we can\'t find what you\'re looking for.', 'bpf-widget'),
        ]);
		
		$this->end_controls_section();
		
        // ------------------------------------------------------------------------- SECTION: Style
        $this->start_controls_section('section_container_style', [
            'label' => esc_html__('Filter Container', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
		
        $this->add_responsive_control('column_spacing', [
            'type' => \Elementor\Controls_Manager::SLIDER,
            'label' => esc_html__('Column Gap', 'bpf-widget'),
            'size_units' => ['px', '%', 'em', 'rem'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
            'selectors' => [
                '{{WRAPPER}}' => '--grid-column-gap: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('row_spacing', [
            'type' => \Elementor\Controls_Manager::SLIDER,
            'label' => esc_html__('Row Gap', 'bpf-widget'),
            'size_units' => ['px', '%', 'em', 'rem'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
            'selectors' => [
                '{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->end_controls_section();
		
		$this->start_controls_section(
			'section_title',
			array(
				'label' => __( 'Filter Label', 'bpf-widget' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		
		$this->add_responsive_control(
			'filter_heading_padding',
			array(
				'label'      => __( 'Filter Spacing', 'bpf-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .filter-title' => 'margin-bottom: {{SIZE}}{{UNIT}}; display: block;',
				),
			)
		);

		$this->add_control(
			'filter_title_default',
			array(
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Default Typography', 'bpf-widget' ),
			)
		);

		$this->add_control(
			'filter_title_color',
			array(
				'label'     => __( 'Default Font Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .filter-title' => 'color: {{VALUE}};',
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

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_label',
			array(
				'label' => __( 'Input Label', 'bpf-widget' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		
		$this->add_responsive_control(
			'filter_label_spacing',
			array(
				'label'      => __( 'Label Spacing', 'bpf-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .form-tax label' => 'margin-bottom: {{SIZE}}{{UNIT}}; display: block;',
				),
			)
		);

		$this->add_control(
			'filter_label_default',
			array(
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Default Typography', 'bpf-widget' ),
			)
		);

		$this->add_control(
			'filter_label_color',
			array(
				'label'     => __( 'Default Font Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .form-tax label' => 'color: {{VALUE}};',
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

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_input',
			array(
				'label' => __( 'Input', 'bpf-widget' ),
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
				'selector' => '{{WRAPPER}} .form-tax input, {{WRAPPER}} .form-tax textarea',
			)
		);

		$this->add_control(
			'filter_field_color',
			array(
				'label'     => __( 'Text Color', 'bpf-widget' ),
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
				'label'     => __( 'Placeholder Color', 'bpf-widget' ),
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
				'label'     => __( 'Field Background', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .form-tax textarea' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'filter_input_padding',
			array(
				'label'      => __( 'Padding', 'bpf-widget' ),
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
				'label'      => __( 'Margin', 'bpf-widget' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .form-tax textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_input_border',
				'selector' => '{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]):not(:focus), {{WRAPPER}} .form-tax textarea',
			)
		);
		
		$this->add_control(
			'filter_input_focus',
			array(
				'label'     => __( 'Focus Border Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} select:focus,  {{WRAPPER}} .form-tax input:focus, {{WRAPPER}} .form-tax input:focus, {{WRAPPER}} .form-tax textarea.cmb2-textarea:focus, {{WRAPPER}} .form-tax .cmb2-file:focus' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'filter_input_border_radius',
			array(
				'label'      => __( 'Border Radius', 'bpf-widget' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} select, {{WRAPPER}} .form-tax input:not([type=submit]):not([type=checkbox]):not([type=radio]), {{WRAPPER}} .form-tax textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_select2',
			array(
				'label' => __( 'Select2', 'bpf-widget' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		
		$this->add_responsive_control(
			'filter_select2_width',
			array(
				'label'      => __( 'Width', 'bpf-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => array(
					'{{WRAPPER}} .cwm-select2 .select2-selection, {{WRAPPER}} .cwm-select2 .select2-selection__rendered, {{WRAPPER}} .cwm-select2 .select2' => 'width: {{SIZE}}{{UNIT}} !important;',
				)
			)
		);
		
		$this->add_responsive_control(
			'filter_select2_height',
			array(
				'label'      => __( 'Height', 'bpf-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'separator'  => 'after',
				'default' => [
					'unit' => 'px',
					'size' => 42,
				],
				'selectors'  => array(
					'{{WRAPPER}} .cwm-select2 .select2-selection, {{WRAPPER}} .cwm-select2 .select2-selection__rendered' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				)
			)
		);

		$this->add_responsive_control(
			'filter_select2_padding',
			array(
				'label'      => __( 'Padding', 'bpf-widget' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .select2-selection' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		
		$this->add_responsive_control(
			'filter_select2_margin',
			array(
				'label'      => __( 'Margin', 'bpf-widget' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .select2-selection' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
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
				'label'     => __( 'Focus Border Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .select2-selection:focus' => 'border-color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'filter_select2_border_radius',
			array(
				'label'      => __( 'Border Radius', 'bpf-widget' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .select2-selection, .select2-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);
		
		$this->add_control(
			'selection_select2',
			[
				'label' => esc_html__( 'Selection', 'bpf-widget' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);
		
		$this->add_control(
			'selection_select2_color',
			array(
				'label'     => __( 'Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cwm-multi-select2 .select2-selection__choice, {{WRAPPER}} .cwm-multi-select2 .select2-selection__choice__remove' => 'color: {{VALUE}}',
				),
			)
		);
		
		$this->add_control(
			'selection_select2_background',
			array(
				'label'     => __( 'Background Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cwm-multi-select2 .select2-selection__choice' => 'background-color: {{VALUE}}',
				),
			)
		);
		
		$this->add_responsive_control(
			'selection_select2_padding',
			array(
				'label'      => __( 'Padding', 'bpf-widget' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .cwm-multi-select2 .select2-selection__choice' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'selection_select2_border',
				'selector' => '{{WRAPPER}} .cwm-multi-select2 .select2-selection__choice',
			)
		);
		
		$this->add_responsive_control(
			'selection_select2_border_radius',
			array(
				'label'      => __( 'Border Radius', 'bpf-widget' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .cwm-multi-select2 .select2-selection__choice' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->end_controls_section();
		
        $this->start_controls_section('loader_style', [
            'label' => esc_html__('Spinner', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('loader_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Inner Circle Color', 'bpf-widget'),
            'default' => '#0098C7',
            'selectors' => [
                '.filter-load:not(.load)::before' => 'border-color: 4px solid {{VALUE}}',
            ],
        ]);
		
        $this->add_control('loader_color_inner', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Outer Circle Color', 'bpf-widget'),
            'default' => 'transparent',
            'selectors' => [
                '.filter-load:not(.load)::before' => 'border-top-color: {{VALUE}}',
            ],
        ]);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_reset_button_styles', [
				'label' => __( 'Reset Button', 'bpf-widget' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_reset' => 'yes',
				],
		]);
		
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
		
        $this->add_control('reset_button_align', [
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'label' => esc_html__('Alignment', 'bpf-widget'),
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'bpf-widget'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'bpf-widget'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'bpf-widget'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
			'separator' => 'after',
            'selectors' => [
                '{{WRAPPER}}  button.reset-form' =>
                    'text-align: {{VALUE}};',
            ],
        ]);
		
		$this->add_responsive_control(
			'reset_button_spacing',
			array(
				'label'     => __( 'Spacing', 'bpf-widget' ),
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
				'label'      => __( 'Width', 'bpf-widget' ),
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
				'separator' => 'after',
				'selectors'  => array(
					'{{WRAPPER}} button.reset-form' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'reset_button_padding',
			array(
				'label'      => __( 'Padding', 'bpf-widget' ),
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
				'label' => __( 'Normal', 'bpf-widget' ),
			)
		);

		$this->add_control(
			'reset_button_text_color',
			array(
				'label'     => __( 'Text Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.reset-form' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_button_background_color',
			array(
				'label'     => __( 'Background Color', 'bpf-widget' ),
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
				'label'     => __( 'Border Radius', 'bpf-widget' ),
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
				'label' => __( 'Hover', 'bpf-widget' ),
			)
		);

		$this->add_control(
			'reset_button_hover_text_color',
			array(
				'label'     => __( 'Text Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.reset-form:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_button_hover_background_color',
			array(
				'label'     => __( 'Background Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.reset-form:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reset_button_hover_border_color',
			array(
				'label'     => __( 'Border Color', 'bpf-widget' ),
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
			'section_submit_button_styles', [
				'label' => __( 'Submit Button', 'bpf-widget' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'use_submit' => 'yes',
				],
		]);
		
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
		
        $this->add_control('submit_button_align', [
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'label' => esc_html__('Alignment', 'bpf-widget'),
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'bpf-widget'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'bpf-widget'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'bpf-widget'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
			'separator' => 'after',
            'selectors' => [
                '{{WRAPPER}}  button.submit-form' =>
                    'text-align: {{VALUE}};',
            ],
        ]);
		
		$this->add_responsive_control(
			'submit_button_spacing',
			array(
				'label'     => __( 'Spacing', 'bpf-widget' ),
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
				'label'      => __( 'Width', 'bpf-widget' ),
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
				'separator' => 'after',
				'selectors'  => array(
					'{{WRAPPER}} button.submit-form' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_button_padding',
			array(
				'label'      => __( 'Padding', 'bpf-widget' ),
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
				'label' => __( 'Normal', 'bpf-widget' ),
			)
		);

		$this->add_control(
			'submit_button_text_color',
			array(
				'label'     => __( 'Text Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.submit-form' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_background_color',
			array(
				'label'     => __( 'Background Color', 'bpf-widget' ),
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
				'label'     => __( 'Border Radius', 'bpf-widget' ),
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
				'label' => __( 'Hover', 'bpf-widget' ),
			)
		);

		$this->add_control(
			'submit_button_hover_text_color',
			array(
				'label'     => __( 'Text Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.submit-form:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_hover_background_color',
			array(
				'label'     => __( 'Background Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} button.submit-form:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_hover_border_color',
			array(
				'label'     => __( 'Border Color', 'bpf-widget' ),
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

	protected function render() {	
	$settings = $this->get_settings_for_display();
	
	if(!$settings['target_selector'] && strpos($current_url, 'preview_nonce') !== false || !$settings['target_selector'] && is_admin()) {
		echo 'Enter a post widget ID or class to enable the filter.';
		return;
	} elseif (!$settings['target_selector']) {
		return;
	}
	
	$widget_id = $this->get_id();
	
	//Declare var
	$show_counter = '';
	$toggleable_class = '';
	$toggle_li = '';
	
	if($settings['filter_list']) {
		$index = 0;
		echo '
		<div class="filter-container" data-target-post-widget="'. $settings['target_selector'] .'">
		<form id="filter-'. $widget_id .'" class="form-tax elementor-grid" action="/" method="get" autocomplete="on" data-post-type="'. $settings['filter_post_type'] .'">';

		if(is_archive()) {
			$queried_object = get_queried_object();
			$archive_type = '';

			if ($queried_object instanceof WP_User) {
				$archive_type = 'author';
			} elseif ($queried_object instanceof WP_Date_Query) {
				$archive_type = 'date';
			} elseif ($queried_object instanceof WP_Term) {
				$archive_type = 'taxonomy';
			} elseif ($queried_object instanceof WP_Post_Type) {
				$archive_type = 'post_type';
			}

			echo '<input type="hidden" name="archive_type" value="' . esc_attr($archive_type) . '">';

			if ($archive_type === 'taxonomy' && $queried_object instanceof WP_Term) {
				echo '
				<input type="hidden" name="archive_id" value="' . esc_attr($queried_object->term_id) . '">
				<input type="hidden" name="archive_taxonomy" value="' . esc_attr($queried_object->taxonomy) . '">
				';
			} elseif ($archive_type === 'post_type' && $queried_object instanceof WP_Post_Type) {
				echo '<input type="hidden" name="archive_post_type" value="' . esc_attr($queried_object->name) . '">';
			} elseif ($queried_object instanceof WP_User) {
				echo '<input type="hidden" name="archive_id" value="' . esc_attr($queried_object->ID) . '">';
			}
		}
		
		foreach ( $settings['filter_list'] as $item ) {
		$index++;
		
		if($item['select_filter'] === 'Taxonomy') {
		
		// Check if transient exists
        $transient_key = 'filter_widget_taxonomy_' . $item['filter_by'];
		
        $hiterms = get_transient($transient_key);		
		$display_empty = $item['display_empty'] === 'yes' ? false : true;
		
		if (false === $hiterms || current_user_can('edit_posts')) {
		$hiterms = get_terms([
			'taxonomy' => $item['filter_by'],
			'orderby' => $item['sort_terms'],
			'hide_empty' => $display_empty,
			'parent' => $item['show_hierarchy'] === 'yes' ? 0 : null,
			'fields' => 'all',
			'update_meta_cache' => false,
		]);
            // Set the transient with a defined expiration time (e.g., 1 hour)
            set_transient($transient_key, $hiterms, DAY_IN_SECONDS / 2); // Set expiration to twice a day
        }
		
		if($item['filter_style'] === 'checkboxes' || $item['filter_style_cf'] === 'checkboxes') {
			$term_index = 0;
			echo '
			<div class="flex-wrapper '. $item['filter_by'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-taxonomy-wrapper" data-logic="'. $item['filter_logic'] .'">
			<ul class="taxonomy-filter '. $item['show_toggle'] .'">
			';

			foreach ($hiterms as $key => $hiterms){
				$toggle_li = ($term_index > 5 && $item['show_toggle']) ? '<li class="more">More...</li>' : '';
				$show_counter = $item['show_counter'] === 'yes' ? ' ('. $hiterms->count .')' : '';
				
				echo '
				<li>
				<label for="'. $hiterms->slug .'-'. $widget_id .'">
				<input type="checkbox" id="'. $hiterms->slug .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['filter_by'] .'" data-taxonomy="'. $hiterms->taxonomy .'" value="'. $hiterms->term_id .'" />
				<span>'. $hiterms->name . $show_counter .'</span>
				</label>
				</li>
				';
				
				if($item['show_hierarchy'] === 'yes') {
				$lowterms_transient_key = 'filter_widget_lowterms_' . $item['filter_by'] . '_' . $hiterms->term_id;
				$lowterms = get_transient($lowterms_transient_key);

				// Bypass transient for users with editing capabilities
				if (false === $lowterms || current_user_can('edit_posts')) {
					$lowterms = get_terms($item['filter_by'], array(
						'orderby' => $item['sort_terms'],
						'parent' => $hiterms->term_id,
						'hide_empty' => $display_empty,
						'fields' => 'all',
						'update_meta_cache' => false,
					));
					// Set the transient with a defined expiration time (e.g., 1 hour)
					set_transient($lowterms_transient_key, $lowterms, DAY_IN_SECONDS / 2); // Set expiration to twice a day
				}
				
				if($lowterms) :
					$low_terms_group_start = $item['toggle_child'] ? '<span class="low-terms-group">' : '';
					$low_terms_group_end = $item['toggle_child'] ? '</span>' : '';
					echo $low_terms_group_start;
					foreach($lowterms as $key => $lowterms) :
					$show_counter = $item['show_counter'] === 'yes' ? ' ('. $lowterms->count .')' : '';
						echo '
						<li class="low-terms '. $toggleable_class .'">
						<label for="'. $lowterms->slug .'-'. $widget_id .'">
						<input type="checkbox" id="'. $lowterms->slug .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['filter_by'] .'" data-taxonomy="'. $lowterms->taxonomy .'" value="'. $lowterms->term_id .'" />
						<span>'. $lowterms->name . $show_counter .'</span>
						</label>
						</li>';
						if (!$item['toggle_child']) {
							$term_index++;
						}
					endforeach;
					echo $low_terms_group_end;
				endif;
				}
				
				$term_index++;
			}
			echo $toggle_li;
			echo '
			</ul>
			</div>
			</div>
			';
		}
		
		if($item['filter_style'] === 'radio' || $item['filter_style_cf'] === 'radio') {
			$term_index = 0;
			echo '
			<div class="flex-wrapper '. $item['filter_by'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-taxonomy-wrapper" data-logic="'. $item['filter_logic'] .'">
			<ul class="taxonomy-filter '. $item['show_toggle'] .'">
			';
			foreach ($hiterms as $key => $hiterms){
				$toggle_li = ($term_index > 5 && $item['show_toggle']) ? '<li class="more">More...</li>' : '';
				$show_counter = $item['show_counter'] === 'yes' ? ' ('. $hiterms->count .')' : '';
				
				echo '
				<li>
				<label for="'. $hiterms->slug .'-'. $widget_id .'">
				<input type="radio" id="'. $hiterms->slug .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['filter_by'] .'" data-taxonomy="'. $hiterms->taxonomy .'" value="'. $hiterms->term_id .'" />
				<span>'. $hiterms->name . $show_counter .'</span>
				</label>
				</li>
				';
				
				if($item['show_hierarchy'] === 'yes') {

					$lowterms = get_terms($item['filter_by'], array(
						'orderby' => $item['sort_terms'],
						'parent' => $hiterms->term_id,
						'hide_empty' => $display_empty,
						'fields' => 'all',
						'update_meta_cache' => false,
					));
				
				if($lowterms) :
					$low_terms_group_start = $item['toggle_child'] ? '<span class="low-terms-group">' : '';
					$low_terms_group_end = $item['toggle_child'] ? '</span>' : '';
					echo $low_terms_group_start;
					foreach($lowterms as $key => $lowterms) :
					$show_counter = $item['show_counter'] === 'yes' ? ' ('. $lowterms->count .')' : '';
						echo '
						<li class="low-terms '. $toggleable_class .'">
						<label for="'. $lowterms->slug .'-'. $widget_id .'">
						<input type="radio" id="'. $lowterms->slug .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['filter_by'] .'" data-taxonomy="'. $lowterms->taxonomy .'" value="'. $lowterms->term_id .'" />
						<span>'. $lowterms->name . $show_counter .'</span>
						</label>
						</li>';
						if (!$item['toggle_child']) {
							$term_index++;
						}
					endforeach;
					echo $low_terms_group_end;
				endif;
				}
				
				$term_index++;
			}
			echo $toggle_li;
			echo '
			</ul>
			</div>
			</div>
			';
		}
		
		if($item['filter_style'] === 'list' || $item['filter_style_cf'] === 'list') {
			echo '
			<div class="flex-wrapper '. $item['filter_by'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-taxonomy-wrapper" data-logic="'. $item['filter_logic'] .'">
			<ul class="taxonomy-filter">
			';
			foreach ($hiterms as $key => $hiterms){
				$show_counter = $item['show_counter'] === 'yes' ? ' ('. $hiterms->count .')' : '';
				echo '
				<li class="list-style">
				<label for="'. $hiterms->slug .'-'. $widget_id .'">
				<input type="checkbox" id="'. $hiterms->slug .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['filter_by'] .'" data-taxonomy="'. $hiterms->taxonomy .'" value="'. $hiterms->term_id .'" />
				<span>'. $hiterms->name . $show_counter .'</span>
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
		
		if($item['filter_style'] === 'dropdown' || $item['filter_style'] === 'select2' || $item['filter_style_cf'] === 'dropdown' || $item['filter_style_cf'] === 'select2') {
			$multi_select2_cf = $item['multi_select2_cf'];
			$multi_select2 = $item['multi_select2'];

			// Initialize select2_class and default_val
			$select2_class = '';
			$default_val = '<option value="">Choose an option</option>';

			// Determine the select2 class
			if ($item['filter_style'] === 'select2' || $item['filter_style_cf'] === 'select2') {
				$select2_class = 'cwm-select2';

				// Determine the multi-select class
				if ($multi_select2_cf === 'yes' || $multi_select2 === 'yes') {
					$select2_class = 'cwm-multi-select2';
					$default_val = '';
				}
			}
	
			echo '
			<div class="flex-wrapper '. $item['filter_by'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-taxonomy-wrapper '. $select2_class .'" data-logic="'. $item['filter_logic'] .'">
			<select id="'. $item['filter_by'] .'-'. $widget_id .'">'.
			$default_val;

			foreach ($hiterms as $key => $hiterms){
				$show_counter = $item['show_counter'] === 'yes' ? ' ('. $hiterms->count .')' : '';
				echo '<option data-category="'. $hiterms->term_id .'" data-taxonomy="'. $hiterms->taxonomy .'" value="'. $hiterms->term_id .'">'. $hiterms->name . $show_counter .'</option>';
			}
			echo '
			</select>
			</div>
			</div>
			';
		}
		}

		if($item['select_filter'] === 'Custom Field') {
			
		if($item['filter_style_cf'] === 'input') {
			$placeholder = $item['text_input_placeholder'] ? $item['text_input_placeholder'] : '';
			echo '
			<div class="flex-wrapper '. $item['meta_key'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-custom-field-wrapper" data-logic="'. $item['filter_logic'] .'">
			<input type="text" class="input-text" id="input-text-'. $item['meta_key'] .'-'. $widget_id .'" name="post_meta" data-taxonomy="'. $item['meta_key'] .'" placeholder="'. $placeholder .'">
			</div>
			</div>
			';
		}

		if ($item['meta_key']) {
			// Check if transient exists
			$meta_terms_transient_key = 'filter_widget_meta_terms_' . $item['meta_key'];
			$terms = get_transient($meta_terms_transient_key);

			// Bypass transient for users with editing capabilities
			if (false === $terms || current_user_can('edit_posts')) {
				$all_posts_args = array(
					'posts_per_page'            => -1,
					'post_type'                 => $settings['filter_post_type'],
					'orderby'                   => $item['sort_terms'],
					'no_found_rows'             => true,
					'fields'                    => 'ids',
					'meta_key'                  => $item['meta_key'],
					'update_post_meta_cache'    => false,
					'update_post_term_cache'    => false,
				);
				
				if($settings['dynamic_filtering']) {
				$queried_object = get_queried_object();
				$archive_type = '';

				if ($queried_object instanceof WP_User) {
					$archive_type = 'author';
				} elseif ($queried_object instanceof WP_Date_Query) {
					$archive_type = 'date';
				} elseif ($queried_object instanceof WP_Term) {
					$archive_type = 'taxonomy';
				} elseif ($queried_object instanceof WP_Post_Type) {
					$archive_type = 'post_type';
				}
				
				// Modify the query for author archive
				if ($archive_type === 'author' && $queried_object instanceof WP_User) {
					$all_posts_args['author'] = $queried_object->ID;
				}

				// Modify the query for taxonomy archive
				if ($archive_type === 'taxonomy' && $queried_object instanceof WP_Term) {
					$all_posts_args['tax_query'] = array(
						array(
							'taxonomy' => $queried_object->taxonomy,
							'field'    => 'term_id',
							'terms'    => $queried_object->term_id,
						),
					);
				}					
				}
				
				$all_posts = new WP_Query($all_posts_args);

				if ($all_posts->have_posts()) {
					$terms_data = array();

					while ($all_posts->have_posts()) {
						$all_posts->the_post();
						$term = get_post_meta(get_the_ID(), $item['meta_key'], true);
						$terms_data[$term] = true; // use term as key
					}

					$terms_data = array_keys($terms_data); // get unique terms
					wp_reset_postdata();

					// Set the transient with a defined expiration time (e.g., 1 hour)
					set_transient($meta_terms_transient_key, $terms_data, DAY_IN_SECONDS / 2); // Set expiration to twice a day

					$terms = $terms_data; // Set the terms variable for output
				}
			}
		}
		
		if($item['filter_style'] === 'checkboxes' || $item['filter_style_cf'] === 'checkboxes') {
			$term_index = 0;
			echo '
			<div class="flex-wrapper '. $item['meta_key'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-custom-field-wrapper" data-logic="'. $item['filter_logic'] .'">
			<ul class="taxonomy-filter '. $item['show_toggle'] .'">
			';
			foreach ($terms as $result){
				$toggleable_class = ($term_index > 5 && $item['show_toggle']) ? 'toggleable' : '';
				$toggle_li = ($term_index > 5 && $item['show_toggle']) ? '<li class="more">More...</li>' : '';
				$show_counter = $item['show_counter'] === 'yes' ? ' ('. $result->count .')' : '';
				
				echo '
				<li class="'. $toggleable_class .'">
				<label for="'. $result .'-'. $widget_id .'">
				<input type="checkbox" id="'. $result .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['meta_key'] .'" data-taxonomy="'. $item['meta_key'] .'" value="'. $result . $show_counter .'" />
				<span>'. $result .'</span>
				</label>
				</li>
				';
				
				$term_index++;
			}
			echo $toggle_li;
			echo '
			</ul>
			</div>
			</div>
			';
		}
		
		if($item['filter_style'] === 'radio' || $item['filter_style_cf'] === 'radio') {
			$term_index = 0;
			echo '
			<div class="flex-wrapper '. $item['meta_key'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-custom-field-wrapper" data-logic="'. $item['filter_logic'] .'">
			<ul class="taxonomy-filter '. $item['show_toggle'] .'">
			';
			foreach ($terms as $result){
				$toggleable_class = ($term_index > 5 && $item['show_toggle']) ? 'toggleable' : '';
				$toggle_li = ($term_index > 5 && $item['show_toggle']) ? '<li class="more">More...</li>' : '';
				$show_counter = $item['show_counter'] === 'yes' ? ' ('. $result->count .')' : '';
				
				echo '
				<li class="'. $toggleable_class .'">
				<label for="'. $result .'-'. $widget_id .'">
				<input type="radio" id="'. $result .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['meta_key'] .'" data-taxonomy="'. $item['meta_key'] .'" value="'. $result . $show_counter .'" />
				<span>'. $result .'</span>
				</label>
				</li>
				';
				
				$term_index++;
			}
			echo $toggle_li;
			echo '
			</ul>
			</div>
			</div>
			';
		}
		
		if($item['filter_style'] === 'list' || $item['filter_style_cf'] === 'list') {
			echo '
			<div class="flex-wrapper '. $item['meta_key'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-custom-field-wrapper" data-logic="'. $item['filter_logic'] .'">
			<ul class="taxonomy-filter '. $item['show_toggle'] .'">
			';
			foreach ($terms as $result){
				$show_counter = $item['show_counter'] === 'yes' ? ' ('. $result->count .')' : '';
				echo '
				<li class="list-style">
				<label for="'. $result .'-'. $widget_id .'">
				<input type="checkbox" id="'. $result .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['meta_key'] .'" data-taxonomy="'. $item['meta_key'] .'" value="'. $result . $show_counter .'" />
				<span>'. $result .'</span>
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
		
		if($item['filter_style'] === 'dropdown' || $item['filter_style'] === 'select2' || $item['filter_style_cf'] === 'dropdown' || $item['filter_style_cf'] === 'select2') {
			$multi_select2_cf = $item['multi_select2_cf'];
			$multi_select2 = $item['multi_select2'];

			// Initialize select2_class and default_val
			$select2_class = '';
			$default_val = '<option value="">Choose an option</option>';

			// Determine the select2 class
			if ($item['filter_style'] === 'select2' || $item['filter_style_cf'] === 'select2') {
				$select2_class = 'cwm-select2';

				// Determine the multi-select class
				if ($multi_select2_cf === 'yes' || $multi_select2 === 'yes') {
					$select2_class = 'cwm-multi-select2';
					$default_val = '';
				}
			}
			
			echo '
			<div class="flex-wrapper '. $item['meta_key'] .'">
			<div class="filter-title">'. $item['filter_title']. '</div>
			<div class="cwm-custom-field-wrapper '. $select2_class .'" data-logic="'. $item['filter_logic'] .'">
			<select id="'. $item['meta_key'] .'-'. $widget_id .'">'.
			$default_val;
			
			foreach ($terms as $result) {
				$show_counter = $item['show_counter'] === 'yes' ? ' ('. $result->count .')' : '';
				echo '<option data-category="'. $result .'" data-taxonomy="'. $item['meta_key'] .'" value="'.$result.'">'. $result . $show_counter .'</option>';
			}
			echo '
			</select>
			</div>
			</div>
			';
		}
		
		}
		
		if($item['select_filter'] === 'Numeric') {
			
			$terms = array();
			
			if (!empty($item['meta_key'])) {
				$numeric_transient_key = 'filter_widget_numeric_' . $item['meta_key'];
				$all_posts_transient = get_transient($numeric_transient_key);

				// Bypass transient for users with editing capabilities or if transient doesn't exist
				if (false === $all_posts_transient || current_user_can('edit_posts')) {
					$all_posts_args = array(
						'posts_per_page'         => -1,
						'post_type'              => $settings['filter_post_type'],
						'fields'                 => 'ids',
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
					);

					if ($settings['dynamic_filtering']) {
						$queried_object = get_queried_object();
						$archive_type = '';

						if ($queried_object instanceof WP_User) {
							$archive_type = 'author';
						} elseif ($queried_object instanceof WP_Date_Query) {
							$archive_type = 'date';
						} elseif ($queried_object instanceof WP_Term) {
							$archive_type = 'taxonomy';
						} elseif ($queried_object instanceof WP_Post_Type) {
							$archive_type = 'post_type';
						}

						// Modify the query for author archive
						if ($archive_type === 'author' && $queried_object instanceof WP_User) {
							$all_posts_args['author'] = $queried_object->ID;
						}

						// Modify the query for taxonomy archive
						if ($archive_type === 'taxonomy' && $queried_object instanceof WP_Term) {
							$all_posts_args['tax_query'] = array(
								array(
									'taxonomy' => $queried_object->taxonomy,
									'field'    => 'term_id',
									'terms'    => $queried_object->term_id,
								),
							);
						}
					}

					$all_posts_query = new WP_Query($all_posts_args);

					// Get post IDs from WP_Query results
					$all_posts_transient = $all_posts_query->posts;

					// Set the transient with a defined expiration time (e.g., 1 hour)
					set_transient($numeric_transient_key, $all_posts_transient, DAY_IN_SECONDS / 2); // Set expiration to twice a day
				}

				$terms = array(); // Initialize $terms array

				foreach ($all_posts_transient as $post_id) {
					$term_values = get_post_custom_values($item['meta_key'], $post_id);

					if (!empty($term_values)) {
						foreach ($term_values as $term) {
							$terms[$term] = true; // use term as key
						}
					}
				}
			}
			
			$terms = array_keys($terms); // get unique terms

			if($item['filter_style_numeric'] === 'range') {

			if (empty($terms) || !is_array($terms)) {
				$min_value = $max_value = 0;
			} else {
				$min_value = floatval(min($terms));
				$max_value = floatval(max($terms));
			}

			echo '
			<div class="flex-wrapper '. $item['meta_key'] .'">
				<div class="filter-title">'. $item['filter_title']. '</div>
				<div class="cwm-numeric-wrapper" data-logic="'. $item['filter_logic'] .'">
					<span class="field-wrapper"><span class="before">'.$item['insert_before_field'].'</span><input type="number" class="cwm-filter-range-'. $index .'" name="min_price" data-taxonomy="'. $item['meta_key'] .'" data-base-value="'. $min_value .'" step="1" min="'. $min_value .'" max="'. $max_value .'" value="'. $min_value .'"></span>
					<span class="field-wrapper"><span class="before">'.$item['insert_before_field'].'</span><input type="number" class="cwm-filter-range-'. $index .'" name="max_price" data-taxonomy="'. $item['meta_key'] .'" data-base-value="'. $max_value .'" step="1" min="'. $min_value .'" max="'. $max_value .'" value="'. $max_value .'"></span>
				</div>
			</div>
			';
			}
			
			if($item['filter_style_numeric'] === 'checkboxes') {
				echo '
				<div class="flex-wrapper '. $item['meta_key'] .'">
				<div class="filter-title">'. $item['filter_title']. '</div>
				<div class="cwm-custom-field-wrapper" data-logic="'. $item['filter_logic'] .'">
				<ul class="taxonomy-filter '. $item['show_toggle_numeric'] .'">
				';
				foreach ($terms as $result){
					echo '
					<li>
					<label for="'. $result .'-'. $widget_id .'">
					<input type="checkbox" id="'. $result .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['meta_key'] .'" data-taxonomy="'. $item['meta_key'] .'" value="'. $result .'" />
					<span>'. $result .'</span>
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
			
			if($item['filter_style_numeric'] === 'radio') {
				echo '<div class="flex-wrapper '. $item['meta_key'] .'">
				<div class="filter-title">'. $item['filter_title']. '</div>
				<div class="cwm-custom-field-wrapper" data-logic="'. $item['filter_logic'] .'">
				<ul class="taxonomy-filter '. $item['show_toggle_numeric'] .'">
				';
				foreach ($terms as $result){
					echo '
					<li>
					<label for="'. $result .'-'. $widget_id .'">
					<input type="radio" id="'. $result .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['meta_key'] .'" data-taxonomy="'. $item['meta_key'] .'" value="'. $result .'" />
					<span>'. $result .'</span>
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
			
			if($item['filter_style_numeric'] === 'list') {
				echo '
				<div class="flex-wrapper '. $item['meta_key'] .'">
				<div class="filter-title">'. $item['filter_title']. '</div>
				<div class="cwm-custom-field-wrapper" data-logic="'. $item['filter_logic'] .'">
				<ul class="taxonomy-filter '. $item['show_toggle_numeric'] .'">
				';
				foreach ($terms as $result){
					echo '
					<li class="list-style">
					<label for="'. $result .'-'. $widget_id .'">
					<input type="checkbox" id="'. $result .'-'. $widget_id .'" class="cwm-filter-item" name="'. $item['meta_key'] .'" data-taxonomy="'. $item['meta_key'] .'" value="'. $result .'" />
					<span>'. $result .'</span>
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
		
		if($settings['use_submit']) {
			echo '<button type="submit" value="submit" class="submit-form">Submit</button>';
		}
		
		if($settings['show_reset']) {
			echo '<button type="reset" class="reset-form" value="reset" onclick="this.form.reset();">Reset</button>';
		}
		
		if($settings['nothing_found_message']) {
			echo '<div class="no-post-message" data-target-post-widget="'. $settings['target_selector'] .'" style="display:none;">'. $settings['nothing_found_message'] .'</div>';
		}
		
		echo '</form></div>';
	}

	}

}
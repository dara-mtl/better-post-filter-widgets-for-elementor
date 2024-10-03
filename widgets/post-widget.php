<?php

use Elementor\Repeater;
use BPF\Inc\Classes\BPF_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

class BPF_Post_Widget extends \Elementor\Widget_Base {
    /**
     * Get widget name.
     *
     * Retrieve Post Widget widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'post-widget';
    }

    /**
     * Get widget title.
     *
     * Retrieve Post Widget widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Post Widget', 'bpf-widget');
    }

    /**
     * Get widget icon.
     *
     * Retrieve Post Widget widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-post-list';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the Post Widget widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['better-post-and-filter-widgets'];
    }

    /**
     * Register Post Widget widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
	 
	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['bpf-widget-style', 'swiper'];
		}

		$layout = $this->get_settings_for_display('classic_layout');

		if ($layout === 'carousel') {
			return ['bpf-widget-style', 'swiper'];
		}

		return ['bpf-widget-style'];
	}

	public function get_script_depends() {
		if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return ['swiper', 'post-widget-script'];
		}

		$layout = $this->get_settings_for_display('classic_layout');

		if ($layout === 'carousel') {
			return ['swiper', 'post-widget-script'];
		}

		return ['post-widget-script'];
	}

    // ------------------------------------------------------------------------- CONTROL: Layout
    protected function register_controls() {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('posts_per_page', [
            'type' => \Elementor\Controls_Manager::NUMBER,
            'label' => esc_html__('Posts Per Page', 'bpf-widget'),
            'default' => 3,
            'min' => -1,
            'max' => 100,
            'step' => 1,
			'frontend_available' => true,
        ]);

        $this->add_control('classic_layout', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Layout', 'bpf-widget'),
            'default' => 'grid',
            'options' => [
                'grid' => 'Grid',
				'masonry' => 'Masonry',
                'carousel' => 'Carousel',
            ],
            'frontend_available' => true,
        ]);

        $this->add_control('post_skin', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Post Skin', 'bpf-widget'),
            'default' => 'classic',
            'options' => [
                'classic' => 'Classic',
                'side' => 'On Side',
                'banner' => 'Banner',
                'template' => 'Loop Grid',
				'custom_html' => 'Custom HTML',
            ],
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
            'default' => '3',
            'tablet_default' => '2',
            'mobile_default' => '2',
            'selectors' => [
                '{{WRAPPER}} .elementor-grid' =>
                    'grid-template-columns: repeat({{VALUE}},1fr)',
            ],
			'frontend_available' => true,
            'condition' => [
                'classic_layout' => ['grid','masonry'],
            ],
        ]);

        $this->add_control('skin_template', [
            'label' => esc_html__('Template', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => BPF_Helper::get_elementor_templates(),
			'label_block' => true,
            'condition' => [
                'post_skin' => 'template',
            ],
        ]);
		
        $this->add_control('skin_template_alternate', [
            'label' => esc_html__('Add Extra Template', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
			'separator' => 'before',
            'condition' => [
                'post_skin' => 'template',
            ],
        ]);
		
		$template_repeater = new Repeater();
		
		$template_repeater->add_control('extra_template_id', [
			'label' => esc_html__('Choose a Template', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => BPF_Helper::get_elementor_templates(),
			'label_block' => true,
		]);
		
		$template_repeater->add_control('grid_position', [
			'label' => esc_html__( 'Position', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => 1,
			'step' => 1,
			'default' => 1,
		]);
		
        $template_repeater->add_control('apply_once', [
            'label' => esc_html__('Apply Once', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
        ]);
		
        $template_repeater->add_control('column_span', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Column Span', 'bpf-widget'),
            'default' => '1',
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
        ]);
		
        $template_repeater->add_control('row_span', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Row Span', 'bpf-widget'),
            'default' => '1',
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
        ]);
		
		$this->add_control('extra_skin_list', [
			'label' => esc_html__('Extra Templates', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $template_repeater->get_controls(),
			'condition' => [
				'post_skin' => 'template',
				'skin_template_alternate!' => '',
			],
			'title_field' => 'Alternate Template',
		]);
		
        $this->add_control('skin_custom_html', [
            'label' => esc_html__('Custom HTML', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
			'default' => '<div class="post-wrapper">
<div class="post-image">
<a href="#PERMALINK#">#IMAGE#</a>
</div>
<div class="inner-content">
<div class="post-title">
<a href="#PERMALINK#">#TITLE#</a>
</div>
<div class="post-content">#CONTENT#</div>
</div>
</div>',
            'options' => BPF_Helper::get_elementor_templates(),
            'condition' => [
                'post_skin' => 'custom_html',
            ],
        ]);
		
		$this->add_control(
			'available_tags', [
			'label' => esc_html__( 'Available Tags:', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'raw' => esc_html__( '#TITLE#, #CONTENT#, #EXCERPT#, #PERMALINK#, #IMAGE#', 'bpf-widget' ),
            'condition' => [
                'post_skin' => 'custom_html',
            ],
			]
		);

        $this->add_control('html_tag', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Title HTML Tag', 'bpf-widget'),
            'default' => 'div',
            'options' => [
                'h1' => 'h1',
                'h2' => 'h2',
                'h3' => 'h3',
				'h4' => 'h4',
                'h5' => 'h5',
                'h6' => 'h6',
                'div' => 'div',
                'span' => 'span',
                'p' => 'p',
            ],
			'separator' => 'before',
        ]);
		
        $this->add_control('post_html_tag', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Post HTML Tag', 'bpf-widget'),
            'default' => 'div',
            'options' => [
                'div' => 'div',
                'article' => 'article',
            ],
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Post Content

        $repeater = new Repeater();
		$user_repeater = new Repeater();

        $repeater->start_controls_tabs('field_repeater');

        $repeater->start_controls_tab('content', [
            'label' => esc_html__('Content', 'bpf-widget'),
        ]);
		
		if ( class_exists( 'WooCommerce' ) ) {
			$post_content_options = [
				'Title' => 'Title',
				'Content' => 'Content',
				'Excerpt' => 'Excerpt',
				'Custom Field' => 'Custom Field/ACF',
				'Taxonomy' => 'Taxonomy',
				'HTML' => 'HTML',
				'Post Meta' => 'Post Meta',
				'Read More' => 'Read More',
				'Pin Post' => 'Bookmark',
				'Edit Options' => 'Edit Options',
				'Product Price' => 'Product Price',
				'Product Rating' => 'Product Rating',
				'Buy Now' => 'Buy Now',
				'Product Bagde' => 'Product Bagde',
			];
		} else {
			$post_content_options = [
				'Title' => 'Title',
				'Content' => 'Content',
				'Excerpt' => 'Excerpt',
				'Custom Field' => 'Custom Field/ACF',
				'Taxonomy' => 'Taxonomy',
				'HTML' => 'HTML',
				'Post Meta' => 'Post Meta',
				'Read More' => 'Read More',
				'Pin Post' => 'Bookmark',
				'Edit Options' => 'Edit Options',
			];
		}
		
        $user_repeater->add_control('post_content', [
            'label' => esc_html__('User Details', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'Display Name',
            'options' => [
				'Username' => 'Username',
				'Display Name' => 'Display Name',
				'Full Name' => 'First/Last Name',
				'User Meta' => 'User Meta',
                'User Email' => 'User Email',
                'User Role' => 'User Role',
				'User ID' => 'User ID',
				'Visit Profile' => 'Visit Profile',
				'HTML' => 'HTML',
            ],
        ]);
		
		$post_content_options_json = json_encode($post_content_options);

		$repeater->add_control('post_content', [
			'label' => esc_html__('Post Content', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'Title',
			'options' => $post_content_options,
		]);

        $repeater->add_control('post_title_url', [
            'label' => esc_html__('Link to Post', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Title',
            ],
        ]);
		
        $user_repeater->add_control('display_name_url', [
            'label' => esc_html__('Link to User Profile', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'post_content' => ['Display Name', 'Full Name', 'Username'],
            ],
        ]);
		
        $repeater->add_control('post_meta_separator', [
            'label' => esc_html__('Meta Separator', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => ' | ',
            'condition' => [
                'post_content' => 'Post Meta',
            ],
        ]);
		
        $repeater->add_control('display_meta_author', [
            'label' => esc_html__('Display Post Author', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Post Meta',
            ],
        ]);
		
        $repeater->add_control('post_author_url', [
            'label' => esc_html__('Link to Author Page', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Post Meta',
				'display_meta_author' => 'yes',
            ],
        ]);
		
		$repeater->add_control('author_icon', [
			'label' => __( 'Author Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Post Meta',
				'display_meta_author' => 'yes',
            ],
		]);
		
        $repeater->add_control('display_meta_date', [
            'label' => esc_html__('Display Post Date', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Post Meta',
            ],
        ]);
		
        $repeater->add_control('display_date_format', [
            'label' => esc_html__('Date Format', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Y/m/d',
			'placeholder' => 'Y/m/d',
            'condition' => [
                'post_content' => 'Post Meta',
				'display_meta_date' => 'yes',
            ],
        ]);
		
		$repeater->add_control('date_icon', [
			'label' => __( 'Date Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Post Meta',
				'display_meta_date' => 'yes',
            ],
		]);
		
        $repeater->add_control('display_meta_comment', [
            'label' => esc_html__('Display Post Comment', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Post Meta',
            ],
        ]);
		
		$repeater->add_control('comment_icon', [
			'label' => __( 'Comment Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Post Meta',
				'display_meta_comment' => 'yes',
            ],
		]);
		
        $repeater->add_control('display_on_sale', [
            'label' => esc_html__('Display On Sale', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Product Bagde',
            ],
        ]);
		
        $repeater->add_control('on_sale_text', [
            'label' => esc_html__('On Sale Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Sale',
			'placeholder' => 'Sale',
            'separator' => 'after',
            'condition' => [
                'post_content' => 'Product Bagde',
				'display_on_sale' => 'yes',
            ],
        ]);
		
        $repeater->add_control('display_new_arrival', [
            'label' => esc_html__('Display New Arrival', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Product Bagde',
            ],
        ]);
		
        $repeater->add_control('new_arrival_text', [
            'label' => esc_html__('New Arrival Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'New Arrival',
			'placeholder' => 'New Arrival',
            'separator' => 'after',
            'condition' => [
                'post_content' => 'Product Bagde',
				'display_new_arrival' => 'yes',
            ],
        ]);
		
        $repeater->add_control('display_best_seller', [
            'label' => esc_html__('Display Best Seller', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Product Bagde',
            ],
        ]);
		
        $repeater->add_control('best_seller_text', [
            'label' => esc_html__('Best Seller Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Best Seller',
			'placeholder' => 'Best Seller',
            'separator' => 'after',
            'condition' => [
                'post_content' => 'Product Bagde',
				'display_best_seller' => 'yes',
            ],
        ]);

        $repeater->add_control('post_taxonomy', [
            'label' => esc_html__('Select Taxonomies', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple' => true,
            'options' => BPF_Helper::get_taxonomies_options(),
            'condition' => [
                'post_content' => 'Taxonomy',
            ],
        ]);

        $repeater->add_control('description_length', [
            'type' => \Elementor\Controls_Manager::NUMBER,
            'label' => esc_html__('Content Length', 'bpf-widget'),
            'placeholder' => '25',
            'min' => 0,
            'max' => 1000,
            'step' => 1,
            'default' => 12,
            'condition' => [
                'post_content' => ['Content', 'Excerpt'],
            ],
        ]);

        $repeater->add_control('title_length', [
            'type' => \Elementor\Controls_Manager::NUMBER,
            'label' => esc_html__('Title Length', 'bpf-widget'),
            'placeholder' => '25',
            'min' => 0,
            'max' => 1000,
            'step' => 1,
            'default' => 6,
            'condition' => [
                'post_content' => 'Title',
            ],
        ]);

        $repeater->add_control('post_taxonomy_nb', [
            'label' => esc_html__('Max. Terms to Show', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 5,
            'min' => 1,
            'max' => 100,
            'step' => 1,
            'condition' => [
                'post_content' => 'Taxonomy',
            ],
        ]);

        $repeater->add_control('post_field_key', [
            'label' => esc_html__('Field Key', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'dynamic' => [
				'active' => false,
			],
            'placeholder' => 'Enter a custom field',
            'label_block' => true,
            'condition' => [
                'post_content' => 'Custom Field',
            ],
        ]);
		
        $user_repeater->add_control('user_field_key', [
            'label' => esc_html__('Field Key', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'dynamic' => [
				'active' => false,
			],
            'placeholder' => 'Enter a custom field key',
            'label_block' => true,
            'condition' => [
                'post_content' => 'User Meta',
            ],
        ]);

        $repeater->add_control('post_html', [
            'label' => esc_html__('HTML', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
			'dynamic' => [
				'active' => false,
			],
            'label_block' => true,
            'condition' => [
                'post_content' => 'HTML',
            ],
        ]);
		
        $user_repeater->add_control('user_html', [
            'label' => esc_html__('HTML', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'dynamic' => [
				'active' => false,
			],
            'label_block' => true,
            'condition' => [
                'post_content' => 'HTML',
            ],
        ]);

        $repeater->add_control('post_read_more_text', [
            'label' => esc_html__('Read More Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Read More »',
            'condition' => [
                'post_content' => 'Read More',
            ],
        ]);
		
        $user_repeater->add_control('visit_profile_text', [
            'label' => esc_html__('Visit Profile Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Visit Profile »',
            'condition' => [
                'post_content' => 'Visit Profile',
            ],
        ]);
		
        $repeater->add_control('product_buy_now_text', [
            'label' => esc_html__('Buy Now Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Buy Now',
            'condition' => [
                'post_content' => 'Buy Now',
            ],
        ]);
		
        $repeater->add_control('post_pin_logged_out', [
            'label' => esc_html__('Hide for Logged-Out Users', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Pin Post',
            ],
        ]);
		
		$repeater->add_control('pin_icon', [
			'label' => __( 'Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Pin Post',
            ],
		]);
		
        $repeater->add_control('pin_text', [
            'label' => esc_html__('Bookmarked Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Bookmarked',
			'separator' => 'after',
            'condition' => [
                'post_content' => 'Pin Post',
            ],
        ]);
		
		$repeater->add_control('unpin_icon', [
			'label' => __( 'Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Pin Post',
            ],
		]);
		
        $repeater->add_control('unpin_text', [
            'label' => esc_html__('Unbookmarked Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Unbookmarked',
            'condition' => [
                'post_content' => 'Pin Post',
            ],
        ]);
		
        $repeater->add_control('display_republish_option', [
            'label' => esc_html__('Display Republish', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Edit Options',
            ],
        ]);
		
		$repeater->add_control('republish_icon', [
			'label' => __( 'Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Edit Options',
				'display_republish_option' => 'yes',
            ],
		]);
		
        $repeater->add_control('republish_option_text', [
            'label' => esc_html__('Republish Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Republish',
			'placeholder' => esc_html__('Republish', 'bpf-widget'),
            'condition' => [
                'post_content' => 'Edit Options',
				'display_republish_option' => 'yes',
            ],
        ]);
		
        $repeater->add_control('display_unpublish_option', [
            'label' => esc_html__('Display Unpublish', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Edit Options',
            ],
        ]);
		
		$repeater->add_control('unpublish_icon', [
			'label' => __( 'Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Edit Options',
				'display_unpublish_option' => 'yes',
            ],
		]);
		
        $repeater->add_control('unpublish_option_text', [
            'label' => esc_html__('Unpublish Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Unpublish',
			'placeholder' => esc_html__('Unpublish', 'bpf-widget'),
            'condition' => [
                'post_content' => 'Edit Options',
				'display_unpublish_option' => 'yes',
            ],
        ]);
		
        $repeater->add_control('display_edit_option', [
            'label' => esc_html__('Display Edit', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Edit Options',
            ],
        ]);
		
        $repeater->add_control('edit_url', [
            'label' => esc_html__('Edit URL', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'description' => esc_html__('Use #ID# to get the post ID', 'bpf-widget'),
			'placeholder' => esc_html__('/your-edit-page?#ID#', 'bpf-widget'),
			'label_block' => true,
            'condition' => [
                'post_content' => 'Edit Options',
				'display_edit_option' => 'yes',
            ],
        ]);
		
		$repeater->add_control('edit_icon', [
			'label' => __( 'Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Edit Options',
				'display_edit_option' => 'yes',
            ],
		]);
		
        $repeater->add_control('edit_option_text', [
            'label' => esc_html__('Edit Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Edit',
			'placeholder' => esc_html__('Edit', 'bpf-widget'),
            'condition' => [
                'post_content' => 'Edit Options',
				'display_edit_option' => 'yes',
            ],
        ]);
		
        $repeater->add_control('display_delete_option', [
            'label' => esc_html__('Display Delete', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
			'separator' => 'before',
            'return_value' => 'yes',
            'condition' => [
                'post_content' => 'Edit Options',
            ],
        ]);
		
		$repeater->add_control('delete_icon', [
			'label' => __( 'Icon', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::ICONS,
            'condition' => [
                'post_content' => 'Edit Options',
				'display_delete_option' => 'yes',
            ],
		]);
		
        $repeater->add_control('delete_option_text', [
            'label' => esc_html__('Delete Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Delete',
			'placeholder' => esc_html__('Republish', 'bpf-widget'),
            'condition' => [
                'post_content' => 'Edit Options',
				'display_delete_option' => 'yes',
            ],
        ]);

        $repeater->end_controls_tab();

        $repeater->start_controls_tab('field_style', [
            'label' => esc_html__('Style', 'bpf-widget'),
        ]);

        $repeater->add_control('custom_style', [
            'label' => esc_html__('Custom', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'description' => esc_html__('Set custom style that will only affect this specific row.', 'bpf-widget'),
        ]);
		
        $repeater->add_control('field_size', [
            'label' => esc_html__('Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}, {{WRAPPER}} {{CURRENT_ITEM}} a' => 'font-size: {{SIZE}}{{UNIT}} !important',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);
		
		$repeater->add_control('field_weight', [
			'label' => esc_html__( 'Weight', 'bpf-widget' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => [
				'100' => esc_html__( '100 (Thin)', 'bpf-widget' ),
				'200' => esc_html__( '200 (Extra Light)', 'bpf-widget' ),
				'300' => esc_html__( '300 (Light)', 'bpf-widget' ),
				'400' => esc_html__( '400 (Normal)', 'bpf-widget' ),
				'500' => esc_html__( '500 (Medium)', 'bpf-widget' ),
				'600' => esc_html__( '600 (Semi Bold)', 'bpf-widget' ),
				'700' => esc_html__( '700 (Bold)', 'bpf-widget' ),
				'800' => esc_html__( '800 (Extra Bold)', 'bpf-widget' ),
				'900' => esc_html__( '900 (Black)', 'bpf-widget' ),
				'' => esc_html__( 'Default', 'bpf-widget' ),
				'normal' => esc_html__( 'Normal', 'bpf-widget' ),
				'bold' => esc_html__( 'Bold', 'bpf-widget' ),
			],
			'separator' => 'after',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'font-weight: {{VALUE}} !important',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
		]);

        $repeater->add_control('field_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}, {{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}}',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->add_control('field_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Hover Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover {{CURRENT_ITEM}}, {{WRAPPER}} .post-wrapper:hover {{CURRENT_ITEM}} a' =>
                    'color: {{VALUE}}',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->add_control('field_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
            ],
            'separator' => 'before',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->add_control('field_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Hover Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover {{CURRENT_ITEM}}' =>
                    'background-color: {{VALUE}}',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'field_border',
                'label' => esc_html__('Border', 'bpf-widget'),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
                'separator' => 'before',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control('field_border_radius', [
            'label' => esc_html__('Border Radius', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' =>
                    'border-radius: {{SIZE}}{{UNIT}}',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->add_responsive_control('horizontal_position', [
            'label' => esc_html__('Horizontal Position', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'bpf-widget'),
                    'icon' => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'bpf-widget'),
                    'icon' => 'eicon-h-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'bpf-widget'),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => '{{VALUE}}',
            ],
            'selectors_dictionary' => [
                'left' => 'margin-right: auto !important',
                'center' => 'margin: 0 auto !important',
                'right' => 'margin-left: auto !important',
            ],
            'separator' => 'before',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->add_responsive_control('vertical_position', [
            'label' => esc_html__('Vertical Position', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'top' => [
                    'title' => esc_html__('Top', 'bpf-widget'),
                    'icon' => 'eicon-v-align-top',
                ],
                'middle' => [
                    'title' => esc_html__('Middle', 'bpf-widget'),
                    'icon' => 'eicon-v-align-middle',
                ],
                'bottom' => [
                    'title' => esc_html__('Bottom', 'bpf-widget'),
                    'icon' => 'eicon-v-align-bottom',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'align-items: {{VALUE}}',
            ],
            'selectors_dictionary' => [
                'top' => 'flex-start !important',
                'middle' => 'center !important',
                'bottom' => 'flex-end !important',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->add_responsive_control('content_size', [
            'label' => esc_html__('Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'none' => [
                    'title' => esc_html__('None', 'bpf-widget'),
                    'icon' => 'eicon-ban',
                ],
                'grow' => [
                    'title' => esc_html__('Grow', 'bpf-widget'),
                    'icon' => 'eicon-grow',
                ],
                'shrink' => [
                    'title' => esc_html__('Shrink', 'bpf-widget'),
                    'icon' => 'eicon-shrink',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'flex-grow: {{VALUE}}',
            ],
            'selectors_dictionary' => [
                'none' => 'unset',
                'grow' => '1',
                'shrink' => '0',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->add_control('field_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' =>
                    'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
			'separator' => 'before',
        ]);

        $repeater->add_control('field_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' =>
                    'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);
		
        $repeater->add_control('field_z_index', [
            'type' => \Elementor\Controls_Manager::NUMBER,
            'label' => esc_html__('Z-Index', 'bpf-widget'),
            'min' => 0,
            'max' => 999,
            'step' => 1,
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}' => 'z-index: {{STRING}};',
            ],
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'custom_style',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $repeater->end_controls_tab();

        $repeater->start_controls_tab('advanced', [
            'label' => esc_html__('Advanced', 'bpf-widget'),
        ]);

        $repeater->add_control('field_before', [
            'label' => esc_html__('Before', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'dynamic' => [
				'active' => true,
			],
        ]);

        $repeater->add_control('field_after', [
            'label' => esc_html__('After', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'dynamic' => [
				'active' => true,
			],
			'description' => esc_html__('Use # to get the row number.', 'bpf-widget'),
        ]);
		
        $repeater->add_control('pseudo_typography', [
            'label' => esc_html__('Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['em', 'px'],
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .pseudo' => 'font-size: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} {{CURRENT_ITEM}} .pseudo img' => 'width: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} {{CURRENT_ITEM}} .pseudo svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $repeater->add_control('pseudo_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .pseudo' => 'color: {{VALUE}}; fill: {{VALUE}};',
            ],
        ]);

        $repeater->add_control('pseudo_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Hover', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}}:hover .pseudo' => 'color: {{VALUE}}; fill: {{VALUE}};',
            ],
        ]);

        $repeater->add_control('pseudo_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} {{CURRENT_ITEM}} .pseudo' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
            ],
        ]);

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->start_controls_section('content_section', [
            'label' => esc_html__('Post Content', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_skin!' => ['template'],
            ],
        ]);

        $this->add_control('show_featured_image', [
            'label' => esc_html__('Show Featured Image', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);
		
		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(), [
				'name' => 'featured_img',
				'include' => [],
				'default' => 'thumbnail',
				'condition' => [
					'show_featured_image' => 'yes',
				],
			]
		);

        $this->add_control('post_image_url', [
            'label' => esc_html__('Link to Post', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'show_featured_image' => 'yes',
            ],
        ]);

		$this->add_control('post_list', [
				'label' => esc_html__('Post Content', 'bpf-widget'),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'separator' => 'before',
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'post_content' => 'Title',
					],
					[
						'post_content' => 'Content',
					],
					[
						'post_content' => 'Read More',
					],
				],
				'prevent_empty' => false,
				'title_field' => "<# let labels = $post_content_options_json; let label = labels[post_content]; #>{{{ label }}}",
				'condition' => [
					'query_type!' => 'user',
				],
			]
		);
		
        $this->add_control('user_list', [
            'label' => esc_html__('User Details', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $user_repeater->get_controls(),
            'default' => [
                [
                    'post_content' => 'Display Name',
                ],
                [
                    'post_content' => 'User Email',
                ],
            ],
			'prevent_empty' => false,
            'title_field' => '{{{ post_content }}}',
			'condition' => [
				'query_type' => 'user',
			],
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Query
        $this->start_controls_section('query_section', [
            'label' => esc_html__('Query', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        //get post type
        $post_type_args = [
            'public' => true,
        ];

        $output = 'names';
        $operator = 'and';
        $post_types = get_post_types($post_type_args, $output, $operator);
        $items_post_type = [];

        if ($post_types) {
			$items_post_type['any'] = 'Any';
            foreach ($post_types as $post_type) {
                $items_post_type[$post_type] = ucfirst($post_type);
            }
        }

        //get post id
        $post_ids = get_posts([
            'post_type' => get_post_types(),
            'post_status' => 'publish',
            'fields' => 'ids',
            'posts_per_page' => 1000,
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
        ]);

        $items_post_id = [];

        if ($post_ids) {
            foreach ($post_ids as $post_id) {
				if(!empty(get_the_title($post_id))) {
					$items_post_id[$post_id] = get_the_title($post_id);
				}
            }
        }

        $this->add_control('query_type', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Query Type', 'bpf-widget'),
            'default' => 'custom',
            'options' => [
                'custom' => 'Custom Query',
                'main' => 'Main Query',
				'user' => 'User Query',
            ],
        ]);

        $this->add_control('post_type', [
            'label' => esc_html__('Post Type', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'post',
            'options' => $items_post_type,
            'condition' => [
                'query_type' => 'custom',
            ],
        ]);

        $this->add_control('post_status', [
            'label' => esc_html__('Post Status', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'default' => 'publish',
            'options' => [
                'publish' => 'Published',
                'pending' => 'Pending',
                'draft' => 'Draft',
                'private' => 'Private',
                'trash' => 'Trashed',
            ],
			'frontend_available' => true,
            'condition' => [
                'query_type' => 'custom',
            ],
        ]);

        $this->add_control('order', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Order', 'bpf-widget'),
            'default' => 'DESC',
            'options' => [
                'DESC' => 'Descending',
                'ASC' => 'Ascending',
            ],
            'condition' => [
                'query_type' => [ 'custom', 'user' ],
            ],
        ]);

        $this->add_control('orderby', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Order By', 'bpf-widget'),
            'default' => 'date',
            'options' => [
                'date' => 'Date',
                'modified' => 'Last Modified Date',
                'rand' => 'Random',
                'comment_count' => 'Comment Count',
                'title' => 'Title',
                'ID' => 'Post ID',
                'author' => 'Post Author',
                'menu_order' => 'Menu Order',
                'relevance' => 'Relevance',
            ],
            'condition' => [
                'query_type' => [ 'custom', 'user' ],
            ],
        ]);

        $taxonomies = [];

        if ($post_types) {
            foreach ($post_types as $post_type) {
                $taxonomies = get_object_taxonomies($post_type, 'objects');

                if ($taxonomies) {
                    foreach ($taxonomies as $index => $tax) {
                        $tax_control_key = $index . '_' . $post_type;

                        if ($post_type == 'post') {
                            if ($index == 'post_tag') {
                                $tax_control_key = 'tags';
                            } elseif ($index == 'category') {
                                $tax_control_key = 'categories';
                            }
                        }

                        $terms = get_terms($index, [
                            'hide_empty' => false,
                        ]);

                        if ($terms) {
                            $items_cat_id = [];
                            $this->add_control(
                                $index . '_' . $post_type . '_filter_type',
                                [
                                    'label' => sprintf(
                                        __('%s Filter Type', 'bpf-widget'),
                                        $tax->label
                                    ),
                                    'type' =>
                                        \Elementor\Controls_Manager::SELECT,
                                    'default' => 'IN',
                                    'label_block' => true,
                                    'options' => [
                                        'IN' => sprintf(
                                            __('Include %s', 'bpf-widget'),
                                            $tax->label
                                        ),
                                        'NOT IN' => sprintf(
                                            __('Exclude %s', 'bpf-widget'),
                                            $tax->label
                                        ),
                                    ],
                                    'separator' => 'before',
                                    'condition' => [
                                        'query_type' => 'custom',
                                        'post_type' => $post_type,
                                    ],
                                ]
                            );

                            foreach ($terms as $term) {
                                $items_cat_id[$term->term_id][0] = $term->name;
                            }

                            $this->add_control($tax_control_key, [
                                'label' => $tax->label,
                                'type' => \Elementor\Controls_Manager::SELECT2,
                                'options' => $items_cat_id,
                                'label_block' => true,
                                'multiple' => true,
                                'condition' => [
                                    'query_type' => 'custom',
                                    'post_type' => $post_type,
                                ],
                            ]);
                        }
                    }
                }
				
				$this->add_control('post__in_'. $post_type, [
					'label' => esc_html__('Include Posts', 'bpf-widget'),
					'type' => \Elementor\Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => '',
					'options' => BPF_Helper::cwm_get_post_list($cpt = $post_type),
					'condition' => [
						'query_type' => 'custom',
						'post_type' => $post_type,
					],
				]);
				
				$this->add_control('post__not_in_'. $post_type, [
					'label' => esc_html__('Exclude Posts', 'bpf-widget'),
					'type' => \Elementor\Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'separator' => 'before',
					'default' => '',
					'options' => BPF_Helper::cwm_get_post_list($cpt = $post_type),
					'condition' => [
						'query_type' => 'custom',
						'post_type' => $post_type,
					],
				]);
            }		
        }
		
        $this->add_control('user_meta_key', [
            'label' => esc_html__('Include by User Meta', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
			'separator' => 'before',
			'options' => BPF_Helper::get_all_user_meta_keys(),
            'condition' => [
                'query_type' => 'user',
            ],
        ]);
		
        $this->add_control('user_meta_value', [
            'label' => esc_html__('Meta Value', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'options' => BPF_Helper::get_all_user_meta_keys(),
            'condition' => [
                'query_type' => 'user',
				'user_meta_key!' => '',
            ],
        ]);
		
        $this->add_control('selected_roles', [
            'label' => esc_html__('Include by Role', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT2,
			'label_block' => true,
			'multiple' => true,
			'options' => BPF_Helper::get_all_user_roles(),
            'condition' => [
                'query_type' => 'user',
            ],
        ]);
		
        $this->add_control('excluded_roles', [
            'label' => esc_html__('Exclude by Role', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT2,
			'label_block' => true,
			'multiple' => true,
            'default' => '',
			'options' => BPF_Helper::get_all_user_roles(),
            'condition' => [
                'query_type' => 'user',
            ],
        ]);

        $this->add_control('sticky_posts', [
            'label' => esc_html__('Sticky Posts', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'separator' => 'before',
            'condition' => [
                'query_type' => 'custom',
            ],
        ]);
		
        $this->add_control('related_posts', [
            'label' => esc_html__('Related Posts', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'query_type' => 'custom',
            ],
        ]);
		
        $this->add_control('related_post_taxonomy', [
            'label' => esc_html__('Select Related Taxonomy', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'label_block' => true,
			'default' => 'category',
            'options' => BPF_Helper::get_taxonomies_options(),
            'condition' => [
                'related_posts' => 'yes',
            ],
        ]);
		
        $this->add_control('pinned_posts', [
            'label' => esc_html__('Pinned Posts', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'bpf-widget'),
            'label_off' => __('No', 'bpf-widget'),
            'return_value' => 'yes',
            'condition' => [
                'query_type' => 'custom',
            ],
        ]);

        $this->add_control('query_id', [
            'label' => __('Query ID', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'description' => __('Give your Query a custom unique ID to allow server side filtering.', 'bpf-widget'),
            'separator' => 'before',
        ]);

        $this->add_control('nothing_found_message', [
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'label' => esc_html__('Nothing Found Message', 'bpf-widget'),
            'rows' => 3,
            'default' => __('It seems we can\'t find what you\'re looking for.', 'bpf-widget'),
			'separator' => 'before',
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Carousel
        $this->start_controls_section('carousel_section', [
            'label' => esc_html__('Carousel Options', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            'condition' => [
                'classic_layout' => 'carousel',
            ],
        ]);
		
        $this->add_responsive_control('post_slider_slides_per_view', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Slides Per View', 'bpf-widget'),
            'options' => [
                '1' => '1',
                '1.5' => '1.5',
                '2' => '2',
                '2.5' => '2.5',
                '3' => '3',
                '3.5' => '3.5',
                '4' => '4',
                '4.5' => '4.5',
				'5' => '5',
				'5.5' => '5.5',
				'6' => '6',
				'6.5' => '6.5',
				'7' => '7',
				'7.5' => '7.5',
				'8' => '8',
				'8.5' => '8.5',
            ],
            'default' => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'frontend_available' => true,
        ]);

        $this->add_responsive_control('post_slider_slides_to_scroll', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Slides to Scroll', 'bpf-widget'),
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
            'frontend_available' => true,
        ]);

        $this->add_responsive_control('post_slider_gap', [
            'type' => \Elementor\Controls_Manager::NUMBER,
            'label' => esc_html__('Slide Gap', 'bpf-widget'),
            'default' => 20,
            'min' => 0,
            'max' => 60,
            'step' => 1,
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_arrows', [
            'label' => esc_html__('Arrow', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_pagination', [
            'label' => esc_html__('Pagination', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_pagination_type', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Pagination Type', 'bpf-widget'),
            'default' => 'bullets',
            'options' => [
                'bullets' => 'Bullets',
                'fraction' => 'Fraction',
                'progressbar' => 'Progressbar',
            ],
            'condition' => [
                'post_slider_pagination' => 'yes',
            ],
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_transition_effect', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Transition Effect', 'bpf-widget'),
            'default' => 'slide',
            'options' => [
                'slide' => 'Slide',
                'fade' => 'Fade',
            ],
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_speed', [
            'type' => \Elementor\Controls_Manager::NUMBER,
            'label' => esc_html__('Animation Speed', 'bpf-widget'),
            'default' => 600,
            'min' => 1,
            'max' => 10000,
            'step' => 1,
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_autoplay', [
            'label' => esc_html__('Autoplay', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_autoplay_delay', [
            'type' => \Elementor\Controls_Manager::NUMBER,
            'label' => esc_html__('Autoplay Speed', 'bpf-widget'),
            'default' => 3000,
            'min' => 1,
            'max' => 10000,
            'step' => 1,
            'frontend_available' => true,
        ]);

        $this->add_control('pause_hover', [
            'label' => esc_html__('Pause on Hover', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_allow_touch_move', [
            'label' => esc_html__('Enable Touch Move', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_allow_mousewheel', [
            'label' => esc_html__('Enable Mousewheel', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_loop', [
            'label' => esc_html__('Infinite Loop', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_parallax', [
            'label' => esc_html__('Apply Parallax?', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_auto_h', [
            'label' => esc_html__('Adaptive Height', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
        ]);

        $this->add_responsive_control('post_slider_h', [
            'label' => esc_html__('Slider height', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
                'vh' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 500,
            ],
            'selectors' => [
                '{{WRAPPER}}.cwm-swiper' =>
                    'height: {{SIZE}}{{UNIT}} !important;',
                '{{WRAPPER}} .swiper-wrapper' =>
                    'height: {{SIZE}}{{UNIT}} !important;',
                '{{WRAPPER}} .swiper-wrapper .swiper-slide' =>
                    'height: {{SIZE}}{{UNIT}} !important;',
            ],
            'condition' => [
                'post_slider_auto_h!' => 'yes',
            ],
            'frontend_available' => true,
            'hide_in_inner' => true,
        ]);

        $this->add_control('post_slider_centered_slides', [
            'label' => esc_html__('Center Mode', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
        ]);

        $this->add_control('post_slider_slides_round_lenghts', [
            'label' => esc_html__('Centered Slide Bounds', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
            'frontend_available' => true,
        ]);
		
        $this->add_control('post_slider_lazy_load', [
            'label' => esc_html__('Lazy Load', 'cwm-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
			'separator' => 'before',
            'frontend_available' => true,
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Pagination
        $this->start_controls_section('pagination_section', [
            'label' => esc_html__('Pagination', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            'condition' => [
                'classic_layout!' => 'carousel',
            ],
        ]);

        $this->add_control('pagination', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Pagination', 'bpf-widget'),
            'default' => 'none',
            'options' => [
                'none' => 'None',
                'numbers' => 'Numbers',
                'numbers_and_prev_next' => 'Numbers + Previous/Next',
                'load_more' => 'Load More Button',
                'infinite' => 'Infinite',
            ],
            'frontend_available' => true,
        ]);
		
        $this->add_control('scroll_to_top', [
            'label' => esc_html__('Scroll to top', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                'pagination' => ['numbers','numbers_and_prev_next', 'load_more'],
            ],
        ]);
		
        $this->add_control('hide_infinite_load', [
            'label' => esc_html__('Hide Spinner', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
            'condition' => [
                'pagination' => 'infinite',
            ],
        ]);
		
        $this->add_control('scroll_threshold', [
            'label' => esc_html__('Scroll Threshold', 'cwm-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => [
                    'min' => -500,
                    'max' => 500,
                ],
                '%' => [
                    'min' => -100,
                    'max' => 100,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'condition' => [
                'pagination' => 'infinite',
            ],
            'frontend_available' => true,
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Additional Options
        $this->start_controls_section('additional_options_section', [
            'label' => esc_html__('Additional Options', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
		
        $this->add_control('use_ajax_search', [
            'label' => esc_html__('Use AJAX search', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
        ]);
		
        $this->add_control('search_button_text', [
            'label' => esc_html__('Button Text', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Search',
            'placeholder' => 'Search',
            'condition' => [
                'use_ajax_search' => 'yes',
            ],
        ]);
		
        $this->add_control('search_placeholder_text', [
            'label' => esc_html__('Placeholder', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Type & Hit Enter...',
            'placeholder' => 'Type & Hit Enter...',
            'condition' => [
                'use_ajax_search' => 'yes',
            ],
        ]);
		
        $this->add_control('external_url_new_tab', [
            'label' => esc_html__('Open Links in New Tab', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
            'condition' => [
				'post_skin!' => ['template'],
            ],
			'separator' => 'before',
        ]);

        $this->add_control('post_external_url', [
            'label' => esc_html__('External URL', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'Paste URL or use a custom field',
            'label_block' => true,
            'condition' => [
                'post_skin!' => ['template'],
            ],
			'description' => esc_html__('Use this option to replace all existing post URLs with a URL of your choice.', 'bpf-widget'),
			'separator' => 'before',
        ]);
		
        $this->add_control('post_external_if_empty', [
            'label' => esc_html__('Use Post URL as Fallback', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
            'condition' => [
                'post_external_url!' => '',
				'post_skin!' => ['template'],
            ],
        ]);
		
        $this->add_control('include_post_id', [
            'label' => esc_html__('Include Posts by ID', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'Comma Separated List',
            'label_block' => true,
			'separator' => 'before',
        ]);
		
        $this->add_control('exclude_post_id', [
            'label' => esc_html__('Exclude Posts by ID', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'Comma Separated List',
            'label_block' => true,
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- SECTION: Style

        // ------------------------------------------------------------------------- CONTROL: Box Style
        $this->start_controls_section('layout_style', [
            'label' => esc_html__('Layout', 'bpf-widget'),
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
		
        $this->add_control('banner_horizontal_position', [
            'label' => esc_html__('Content Horizontal Position', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'bpf-widget'),
                    'icon' => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'bpf-widget'),
                    'icon' => 'eicon-h-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'bpf-widget'),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .inner-content' => '{{VALUE}}',
            ],
            'selectors_dictionary' => [
                'left' => 'align-items: flex-start; text-align: left;',
                'center' => 'align-items: center; text-align: center;',
                'right' => 'align-items: flex-end; text-align: right;',
            ],
            'separator' => 'before',
            'condition' => [
				'post_skin' => 'banner',
            ],
        ]);

        $this->add_control('banner_vertical_position', [
            'label' => esc_html__('Content Vertical Position', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'top' => [
                    'title' => esc_html__('Top', 'bpf-widget'),
                    'icon' => 'eicon-v-align-top',
                ],
                'middle' => [
                    'title' => esc_html__('Middle', 'bpf-widget'),
                    'icon' => 'eicon-v-align-middle',
                ],
                'bottom' => [
                    'title' => esc_html__('Bottom', 'bpf-widget'),
                    'icon' => 'eicon-v-align-bottom',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .inner-content' => 'justify-content: {{VALUE}}',
            ],
            'selectors_dictionary' => [
                'top' => 'flex-start',
                'middle' => 'center',
                'bottom' => 'flex-end',
            ],
            'condition' => [
				'post_skin' => 'banner',
            ],
        ]);
		
        $this->start_controls_tabs('style_tabs_layout');

        $this->start_controls_tab('style_layout_normal_tab', [
            'label' => esc_html__('Normal', 'cwm-widget'),
        ]);

        $this->add_control('layout_color_normal', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'cwm-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('layout_padding_normal', [
            'label' => esc_html__('Padding', 'cwm-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('layout_margin_normal', [
            'label' => esc_html__('Margin', 'cwm-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'border_layout_normal',
            'label' => esc_html__('Border', 'cwm-widget'),
            'separator' => 'before',
            'selector' => '{{WRAPPER}} .post-wrapper',
        ]);

        $this->add_control('layout_border_layout_radius', [
            'label' => esc_html__('Border Radius', 'cwm-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' =>
                    'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('style_layout_hover_tab', [
            'label' => esc_html__('Hover', 'cwm-widget'),
        ]);

        $this->add_control('layout_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'cwm-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('layout_padding_hover', [
            'label' => esc_html__('Padding', 'cwm-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('layout_margin_hover', [
            'label' => esc_html__('Margin', 'cwm-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'border_layout_hover',
            'label' => esc_html__('Border', 'cwm-widget'),
            'separator' => 'before',
            'selector' => '{{WRAPPER}} .post-wrapper:hover ',
        ]);

        $this->add_control('layout_border_layout_radius_hover', [
            'label' => esc_html__('Border Radius', 'cwm-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover' =>
                    'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();
		
		$this->end_controls_section();
		
        $this->start_controls_section('box_style', [
            'label' => esc_html__('Post Content', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('box_height', [
            'type' => \Elementor\Controls_Manager::SLIDER,
            'label' => esc_html__('Height', 'bpf-widget'),
            'size_units' => ['px', '%', 'em', 'rem'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' => 'height: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('box_min_height', [
            'type' => \Elementor\Controls_Manager::SLIDER,
            'label' => esc_html__('Min. Height', 'bpf-widget'),
            'size_units' => ['px', '%', 'em', 'rem'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('box_max_height', [
            'type' => \Elementor\Controls_Manager::SLIDER,
            'label' => esc_html__('Max. Height', 'bpf-widget'),
            'size_units' => ['px', '%', 'em', 'rem'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' => 'max-height: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_control('box_transition_duration', [
            'label' => __('Transition Duration', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'default' => [
                'size' => 0.3,
            ],
            'range' => [
                'px' => [
                    'max' => 5,
                    'step' => 0.1,
                ],
            ],
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .post-wrapper,{{WRAPPER}} .post-wrapper .overlay,{{WRAPPER}} .post-wrapper a,{{WRAPPER}} .post-title,{{WRAPPER}} .post-content,{{WRAPPER}} .post-taxonomy,{{WRAPPER}} .post-read-more' =>
                    'transition-duration: {{SIZE}}s',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_box');

        $this->start_controls_tab('style_box_normal_tab', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('box_color_normal', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('box_padding_normal', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper .inner-content' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('box_margin_normal', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper .inner-content' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'border_normal',
            'label' => esc_html__('Border', 'bpf-widget'),
            'separator' => 'before',
            'selector' => '{{WRAPPER}} .post-wrapper .inner-content',
        ]);

        $this->add_control('box_border_radius', [
            'label' => esc_html__('Border Radius', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' =>
                    'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('style_box_hover_tab', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('box_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('box_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .inner-content' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_responsive_control('box_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .inner-content' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'border_hover',
            'label' => esc_html__('Border', 'bpf-widget'),
            'separator' => 'before',
            'selector' => '{{WRAPPER}} .post-wrapper:hover  .inner-content',
        ]);

        $this->add_control('box_border_radius_hover', [
            'label' => esc_html__('Border Radius', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover' =>
                    'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Featured Image Style
        $this->start_controls_section('img_style', [
            'label' => esc_html__('Featured Image', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'show_featured_image',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $this->add_control('post_default_image', [
            'label' => esc_html__('Fallback Image', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
        ]);

        $this->add_control('img_equal_height', [
            'label' => esc_html__('Image Equal Height', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_responsive_control('img_height', [
            'label' => esc_html__('Height', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['%', 'px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .post-image, {{WRAPPER}} .post-image a' => 'height: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .post-image img' => 'height: 100%;',
            ],
            'condition' => [
                'img_equal_height' => '',
            ],
        ]);
		
        $this->add_responsive_control('img_width', [
            'label' => esc_html__('Width', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['%', 'px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
			'default' => [
				'unit' => '%',
				'size' => 40,
			],
            'selectors' => [
                '{{WRAPPER}} .post-container.side .post-image' => 'width: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .post-container.side .inner-content' => 'width: calc(100% - {{SIZE}}{{UNIT}})',
            ],
            'condition' => [
				'post_skin' => 'side',
            ],
        ]);

        $this->add_responsive_control('img_horizontal_position', [
            'label' => esc_html__('Image Position', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'bpf-widget'),
                    'icon' => 'eicon-h-align-left',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'bpf-widget'),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'default' => 'left',
            'condition' => [
                'post_skin' => 'side',
            ],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' => 'flex-direction: {{VALUE}}',
            ],
            'selectors_dictionary' => [
                'left' => 'row',
                'right' => 'row-reverse',
            ],
        ]);

        $this->add_responsive_control('img_vertical_position', [
            'label' => esc_html__('Image Position', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'top' => [
                    'title' => esc_html__('Top', 'bpf-widget'),
                    'icon' => 'eicon-v-align-top',
                ],
                'bottom' => [
                    'title' => esc_html__('Bottom', 'bpf-widget'),
                    'icon' => 'eicon-v-align-bottom',
                ],
            ],
            'default' => 'top',
            'condition' => [
                'post_skin' => 'classic',
            ],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper' => 'flex-direction: {{VALUE}}',
            ],
            'selectors_dictionary' => [
                'top' => 'column',
                'bottom' => 'column-reverse',
            ],
        ]);
		
        $this->add_responsive_control('img-aspect-ratio', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'label' => esc_html__('Aspect Ratio', 'bpf-widget'),
            'default' => '3-2',
            'options' => [
                '3-2' => '3:2',
                '1-1' => '1:1',
				'4-3' => '4:3',
				'16-9' => '16:9',
				'191-1' => '19.1:1',
            ],
            'condition' => [
                'img_equal_height' => 'yes',
            ],
        ]);

        $this->add_control('img_border_radius', [
            'label' => esc_html__('Border Radius', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .post-image img' =>
                    'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'img_border',
            'selector' => '{{WRAPPER}} .post-image img',
        ]);

        $this->add_control('overlay', [
            'label' => esc_html__('Image Overlay', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'bpf-widget'),
            'label_off' => esc_html__('No', 'bpf-widget'),
            'return_value' => 'yes',
            'default' => '',
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Background::get_type(), [
                'name' => 'img_overlay_normal',
                'types' => ['classic', 'gradient'],
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .post-wrapper .overlay',
                'condition' => [
                    'overlay' => 'yes',
                ],
        ]);

        $this->add_control('img_overlay_hover', [
            'label' => esc_html__('Hover Opacity', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.1,
                    'step' => 0.01,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 0.5,
            ],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .overlay' =>
                    'opacity: {{SIZE}}',
            ],
            'condition' => [
                'overlay' => 'yes',
            ],
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Title Style
        $this->start_controls_section('title_style', [
            'label' => esc_html__('Title', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' =>
                    '{{WRAPPER}} .post-title, {{WRAPPER}} .post-title a',
            ]
        );

        $this->add_responsive_control('title_align', [
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
            'selectors' => [
                '{{WRAPPER}} .post-title' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_title');

        $this->start_controls_tab('title_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('title_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-title a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .post-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('title_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-title' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('title_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-title' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('title_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-title' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('title_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('title_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-title:hover a' =>
                    'color: {{VALUE}}',
                '{{WRAPPER}} .post-title:hover' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('title_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-title:hover' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('title_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-title:hover' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('title_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-title:hover' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Content/Excerpt Style
        $this->start_controls_section('content_style', [
            'label' => esc_html__('Content/Excerpt', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' =>
                    '{{WRAPPER}} .post-content, {{WRAPPER}} .post-content a, {{WRAPPER}} .post-excerpt, {{WRAPPER}} .post-excerpt a',
            ]
        );

        $this->add_responsive_control('content_align', [
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
                'justify' => [
                    'title' => esc_html__('Justify', 'bpf-widget'),
                    'icon' => 'eicon-text-align-justify',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_content');

        $this->start_controls_tab('content_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('content_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-content a, {{WRAPPER}} .post-excerpt a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('content_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('content_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('content_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('content_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('content_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-content a, {{WRAPPER}} .post-wrapper:hover .post-excerpt a' =>
                    'color: {{VALUE}}',
                '{{WRAPPER}} .post-wrapper:hover .post-content, {{WRAPPER}} .post-wrapper:hover .post-excerpt' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('content_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-content, {{WRAPPER}} .post-wrapper:hover .post-excerpt' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('content_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-content, {{WRAPPER}} .post-wrapper:hover .post-excerpt' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('content_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-content, {{WRAPPER}} .post-wrapper:hover .post-excerpt' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Taxonomy Style
        $this->start_controls_section('taxonomy_style', [
            'label' => esc_html__('Taxonomy', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'taxonomy_typography',
                'selector' =>
                    '{{WRAPPER}} ul.post-taxonomy li, {{WRAPPER}} ul.post-taxonomy li a',
            ]
        );

        $this->add_responsive_control('taxonomy_align', [
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
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_taxonomy');

        $this->start_controls_tab('taxonomy_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('taxonomy_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy li a' => 'color: {{VALUE}}',
                '{{WRAPPER}} ul.post-taxonomy li' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('taxonomy_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy li' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('taxonomy_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy li' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('taxonomy_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy li' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('taxonomy_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('taxonomy_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy li:hover a' => 'color: {{VALUE}}',
                '{{WRAPPER}} ul.post-taxonomy li:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('taxonomy_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy li:hover' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('taxonomy_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy li:hover' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('taxonomy_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} ul.post-taxonomy li:hover' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Custom Field Style
        $this->start_controls_section('custom_field_style', [
            'label' => esc_html__('Custom Field/ACF', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'custom_field_typography',
                'selector' =>
                    '{{WRAPPER}} .post-custom-field, {{WRAPPER}} .post-custom-field a',
            ]
        );

        $this->add_responsive_control('custom_field_align', [
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
            'selectors' => [
                '{{WRAPPER}} .post-custom-field' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_custom_field');

        $this->start_controls_tab('custom_field_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('custom_field_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-custom-field a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .post-custom-field' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('custom_field_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-custom-field' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('custom_field_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-custom-field' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('custom_field_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-custom-field' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('custom_field_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('custom_field_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-custom-field a' =>
                    'color: {{VALUE}}',
                '{{WRAPPER}} .post-wrapper:hover .post-custom-field' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('custom_field_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-custom-field' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('custom_field_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-custom-field' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('custom_field_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-custom-field' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Post Meta Style
        $this->start_controls_section('post_meta_style', [
            'label' => esc_html__('Post Meta', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'post_meta_typography',
                'selector' =>
                    '{{WRAPPER}} .post-meta, {{WRAPPER}} .post-meta a',
            ]
        );

        $this->add_responsive_control('post_meta_align', [
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
            'selectors' => [
                '{{WRAPPER}} .post-meta' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_post_meta');

        $this->start_controls_tab('post_meta_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('post_meta_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-meta a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .post-meta' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('post_meta_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-meta' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('post_meta_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-meta' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('post_meta_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
			'separator' => 'after',
            'selectors' => [
                '{{WRAPPER}} .post-meta' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
		
        $this->add_control('post_meta_icon_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Icon Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .post-meta i, {{WRAPPER}} .post-meta svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
            ],
        ]);
		
        $this->add_control('post_meta_icon_size', [
            'label' => esc_html__('Icon Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['em', 'px'],
            'selectors' => [
                '{{WRAPPER}} .post-meta i' => 'font-size: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .post-meta svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);
		
        $this->add_control('post_meta_icon_spacing', [
            'label' => esc_html__('Icon Spacing', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['em', 'px'],
			'default' => [
				'unit' => 'px',
				'size' => 4,
			],
            'selectors' => [
                '{{WRAPPER}} .post-meta i' => 'margin-right: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .post-meta svg' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('post_meta_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('post_meta_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-meta a' =>
                    'color: {{VALUE}}',
                '{{WRAPPER}} .post-wrapper:hover .post-meta' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('post_meta_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-meta' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('post_meta_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-meta' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('post_meta_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-meta' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Read More Style
        $this->start_controls_section('read_more_style', [
            'label' => esc_html__('Read More', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'read_more_typography',
                'selector' =>
                    '{{WRAPPER}} .post-read-more, {{WRAPPER}} .post-read-more a',
            ]
        );

        $this->add_responsive_control('read_more_align', [
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
            'selectors' => [
                '{{WRAPPER}} .post-read-more' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_read_more');

        $this->start_controls_tab('read_more_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('read_more_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-read-more a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .post-read-more' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('read_more_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-read-more' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('read_more_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-read-more' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('read_more_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-read-more' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('read_more_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('read_more_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-read-more a' =>
                    'color: {{VALUE}}',
                '{{WRAPPER}} .post-wrapper:hover .post-read-more' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('read_more_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-read-more' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('read_more_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-read-more' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('read_more_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .post-read-more' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Search Bar Style
        $this->start_controls_section('search_bar_style', [
            'label' => esc_html__('Search Bar', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [
				'use_ajax_search' => 'yes',
            ]
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'search_bar_typography',
                'selector' =>
                    '{{WRAPPER}} .search-container form input[type="text"], {{WRAPPER}} .search-container form button
				',
            ]
        );
		
        $this->add_responsive_control('search_bar_spacing', [
            'label' => esc_html__('Search Bar Spacing', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
            'selectors' => [
                '{{WRAPPER}} .search-container' =>
                    'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);
		
		$this->add_control(
			'search_bar_input_title',
			[
				'label' => esc_html__( 'Input Style', 'bpf-widget' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		
        $this->add_responsive_control('search_bar_width', [
            'label' => esc_html__('Width', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['%', 'px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
			'default' => [
				'unit' => '%',
				'size' => 100,
			],
            'selectors' => [
                '{{WRAPPER}} .search-container form' =>
                    'width: {{SIZE}}{{UNIT}}',
            ]
        ]);
		
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'search_bar_input_border',
            'label' => esc_html__('Border', 'bpf-widget'),
            'separator' => 'before',
            'selector' => '{{WRAPPER}} .search-container input',
        ]);

		$this->add_control(
			'search_bar_input_border_radius',
			[
				'label'     => __( 'Border Radius', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .search-container input' => 'border-radius: {{SIZE}}{{UNIT}}',
				]
			]
		);
		
		$this->add_control(
			'search_bar_button_title',
			[
				'label' => esc_html__( 'Button Style', 'bpf-widget' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		
		$this->add_responsive_control('search_bar_button_width', [
				'label'     => __( 'Width', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 33.33,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .search-container button' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $this->start_controls_tabs('style_tabs_search_bar_button');

        $this->start_controls_tab('search_bar_button_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);
		
		$this->add_control(
			'search_bar_button_color',
			[
				'label'     => __( 'Color', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container button' => 'color: {{VALUE}};',
				]
			]
		);
		
		$this->add_control(
			'search_bar_button_background_color',
			[
				'label'     => __( 'Background Color', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container button' => 'background-color: {{VALUE}};',
				]
			]
		);
		
		$this->end_controls_tab();
		
        $this->start_controls_tab('search_bar_button_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);
		
		$this->add_control(
			'search_bar_button_color_hover',
			[
				'label'     => __( 'Color', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container button:hover' => 'color: {{VALUE}};',
				]
			]
		);
		
		$this->add_control(
			'search_bar_button_background_color_hover',
			[
				'label'     => __( 'Background Color', 'bpf-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search-container button:hover' => 'background-color: {{VALUE}};',
				]
			]
		);		
		
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		
		// ------------------------------------------------------------------------- CONTROL: Username/Display Name Style
		$this->start_controls_section('user_name_style', [
			'label' => esc_html__('Username/Display Name', 'bpf-widget'),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'conditions' => [
                'terms' => [
                    [
                        'name' => 'query_type',
                        'value' => 'user',
                    ],
                ],
            ],
		]);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'user_name_typography',
				'selector' =>
					'{{WRAPPER}} .user-username, {{WRAPPER}} .user-username a, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-display-name a, {{WRAPPER}} .user-full-name, {{WRAPPER}} .user-full-name a',
			]
		);

		$this->add_responsive_control('user_name_align', [
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
			'selectors' => [
				'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' =>
					'text-align: {{VALUE}}; justify-content: {{VALUE}};',
			],
		]);

		$this->start_controls_tabs('style_tabs_user_name');

		$this->start_controls_tab('user_name_style_normal', [
			'label' => esc_html__('Normal', 'bpf-widget'),
		]);

		$this->add_control('user_name_color', [
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__('Color', 'bpf-widget'),
			'selectors' => [
				'{{WRAPPER}} .user-username a, {{WRAPPER}} .user-display-name a, {{WRAPPER}} .user-full-name a' => 'color: {{VALUE}}',
				'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('user_name_background_color', [
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__('Background', 'bpf-widget'),
			'selectors' => [
				'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' =>
					'background-color: {{VALUE}}',
			],
		]);

		$this->add_responsive_control('user_name_padding', [
			'label' => esc_html__('Padding', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em', 'rem'],
			'selectors' => [
				'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' =>
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('user_name_margin', [
			'label' => esc_html__('Margin', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em', 'rem'],
			'selectors' => [
				'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' =>
					'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('user_name_style_hover', [
			'label' => esc_html__('Hover', 'bpf-widget'),
		]);

		$this->add_control('user_name_color_hover', [
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__('Color', 'bpf-widget'),
			'selectors' => [
				'{{WRAPPER}} .post-wrapper:hover .user-username a, {{WRAPPER}} .post-wrapper:hover .user-display-name a, {{WRAPPER}} .post-wrapper:hover .user-full-name a' =>
					'color: {{VALUE}}',
				'{{WRAPPER}} .post-wrapper:hover .user-username, {{WRAPPER}} .post-wrapper:hover .user-display-name, {{WRAPPER}} .post-wrapper:hover .user-full-name' =>
					'color: {{VALUE}};',
			],
		]);

		$this->add_control('user_name_background_color_hover', [
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__('Background', 'bpf-widget'),
			'selectors' => [
				'{{WRAPPER}} .post-wrapper:hover .user-username, {{WRAPPER}} .post-wrapper:hover .user-display-name, {{WRAPPER}} .post-wrapper:hover .user-full-name' =>
					'background-color: {{VALUE}}',
			],
		]);

		$this->add_responsive_control('user_name_padding_hover', [
			'label' => esc_html__('Padding', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em', 'rem'],
			'selectors' => [
				'{{WRAPPER}} .post-wrapper:hover .user-username, {{WRAPPER}} .post-wrapper:hover .user-display-name, {{WRAPPER}} .post-wrapper:hover .user-full-name' =>
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('user_name_margin_hover', [
			'label' => esc_html__('Margin', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em', 'rem'],
			'selectors' => [
				'{{WRAPPER}} .post-wrapper:hover .user-username, {{WRAPPER}} .post-wrapper:hover .user-display-name, {{WRAPPER}} .post-wrapper:hover .user-full-name' =>
					'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Visit Profile Style
		$this->start_controls_section('visit_profile_style', [
			'label' => esc_html__('Visit Profile', 'bpf-widget'),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'conditions' => [
                'terms' => [
                    [
                        'name' => 'query_type',
                        'value' => 'user',
                    ],
                ],
            ],
		]);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'visit_profile_typography',
				'selector' =>
					'{{WRAPPER}} .visit-profile, {{WRAPPER}} .visit-profile a',
			]
		);

		$this->add_responsive_control('visit_profile_align', [
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
			'selectors' => [
				'{{WRAPPER}} .visit-profile' =>
					'text-align: {{VALUE}}; justify-content: {{VALUE}};',
			],
		]);

		$this->start_controls_tabs('style_tabs_visit_profile');

		$this->start_controls_tab('visit_profile_style_normal', [
			'label' => esc_html__('Normal', 'bpf-widget'),
		]);

		$this->add_control('visit_profile_color', [
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__('Color', 'bpf-widget'),
			'selectors' => [
				'{{WRAPPER}} .visit-profile a' => 'color: {{VALUE}}',
				'{{WRAPPER}} .visit-profile' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('visit_profile_background_color', [
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__('Background', 'bpf-widget'),
			'selectors' => [
				'{{WRAPPER}} .visit-profile' =>
					'background-color: {{VALUE}}',
			],
		]);

		$this->add_responsive_control('visit_profile_padding', [
			'label' => esc_html__('Padding', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em', 'rem'],
			'selectors' => [
				'{{WRAPPER}} .visit-profile' =>
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('visit_profile_margin', [
			'label' => esc_html__('Margin', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em', 'rem'],
			'selectors' => [
				'{{WRAPPER}} .visit-profile' =>
					'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('visit_profile_style_hover', [
			'label' => esc_html__('Hover', 'bpf-widget'),
		]);

		$this->add_control('visit_profile_color_hover', [
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__('Color', 'bpf-widget'),
			'selectors' => [
				'{{WRAPPER}} .post-wrapper:hover .visit-profile a' =>
					'color: {{VALUE}}',
				'{{WRAPPER}} .post-wrapper:hover .visit-profile' =>
					'color: {{VALUE}};',
			],
		]);

		$this->add_control('visit_profile_background_color_hover', [
			'type' => \Elementor\Controls_Manager::COLOR,
			'label' => esc_html__('Background', 'bpf-widget'),
			'selectors' => [
				'{{WRAPPER}} .post-wrapper:hover .visit-profile' =>
					'background-color: {{VALUE}}',
			],
		]);

		$this->add_responsive_control('visit_profile_padding_hover', [
			'label' => esc_html__('Padding', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em', 'rem'],
			'selectors' => [
				'{{WRAPPER}} .post-wrapper:hover .visit-profile' =>
					'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('visit_profile_margin_hover', [
			'label' => esc_html__('Margin', 'bpf-widget'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em', 'rem'],
			'selectors' => [
				'{{WRAPPER}} .post-wrapper:hover .visit-profile' =>
					'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		
		//WOOCOMMERCE SECTION
		if ( class_exists( 'WooCommerce') ) {
        // ------------------------------------------------------------------------- CONTROL: Product Price Style
        $this->start_controls_section('product_price_style', [
            'label' => esc_html__('Product Price', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'product_price_typography',
                'selector' =>
                    '{{WRAPPER}} .product-price, {{WRAPPER}} .product-price a',
            ]
        );

        $this->add_responsive_control('product_price_align', [
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
            'selectors' => [
                '{{WRAPPER}} .product-price' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);
		
        $this->add_control('sales_price_size', [
            'label' => esc_html__('Sales Price Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
            'default' => [
				'unit' => 'px',
                'size' => 16,
            ],
            'selectors' => [
                '{{WRAPPER}} .product-price del' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_product_price');

        $this->start_controls_tab('product_price_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('product_price_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-price' => 'color: {{VALUE}};',
            ],
        ]);
		
        $this->add_control('sales_price_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Sales Price Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-price del .woocommerce-Price-amount' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('product_price_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-price' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('product_price_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .product-price' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('product_price_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .product-price' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('product_price_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('product_price_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-price' =>
                    'color: {{VALUE}};',
            ],
        ]);
		
        $this->add_control('sales_price_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Sales Price Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-price del .woocommerce-Price-amount' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('product_price_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-price' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('product_price_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-price' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('product_price_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-price' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Buy Now Style
        $this->start_controls_section('buy_now_style', [
            'label' => esc_html__('Buy Now', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'buy_now_typography',
                'selector' =>
                    '{{WRAPPER}} .product-buy-now, {{WRAPPER}} .product-buy-now a',
            ]
        );

        $this->add_responsive_control('buy_now_align', [
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
            'selectors' => [
                '{{WRAPPER}} .product-buy-now' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_buy_now');

        $this->start_controls_tab('buy_now_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('buy_now_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-buy-now' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('buy_now_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-buy-now' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('buy_now_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .product-buy-now' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('buy_now_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .product-buy-now' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('buy_now_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('buy_now_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-buy-now' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('buy_now_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-buy-now' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('buy_now_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-buy-now' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('buy_now_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-buy-now' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Product Badge Style
        $this->start_controls_section('product_badge_style', [
            'label' => esc_html__('Product Badge', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'product_badge_typography',
                'selector' =>
                    '{{WRAPPER}} .product-badge, {{WRAPPER}} .product-badge a',
            ]
        );

        $this->add_responsive_control('product_badge_align', [
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
            'selectors' => [
                '{{WRAPPER}} .product-badge' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_product_badge');

        $this->start_controls_tab('product_badge_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('product_badge_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-badge' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('product_badge_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-badge' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('product_badge_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .product-badge' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('product_badge_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .product-badge' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('product_badge_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('product_badge_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-badge' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('product_badge_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-badge' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('product_badge_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-badge' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('product_badge_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-badge' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Product Rating Style
        $this->start_controls_section('product_rating_style', [
            'label' => esc_html__('Product Rating', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('product_rating_size', [
            'label' => esc_html__('Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'em', 'px' ],
			'range' => [
				'em' => [
					'min' => 1,
					'max' => 10,
					'step' => 1,
				],
				'px' => [
					'min' => 1,
					'max' => 50,
					'step' => 1,
				],
			],
            'default' => [
				'unit' => 'px',
                'size' => 20,
            ],
            'selectors' => [
                '{{WRAPPER}} .product-rating span' => 'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('product_rating_align', [
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
            'selectors' => [
                '{{WRAPPER}} .product-rating' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_product_rating');

        $this->start_controls_tab('product_rating_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('product_rating_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-rating .star-full' => 'color: {{VALUE}};',
            ],
        ]);
		
        $this->add_control('product_empty_star_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Empty Star Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-rating .star-empty' => 'color: {{VALUE}};',
            ],
        ]);


        $this->add_control('product_rating_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .product-rating span' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('product_rating_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .product-rating span' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('product_rating_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .product-rating span' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('product_rating_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('product_rating_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-rating .star-full' =>
                    'color: {{VALUE}};',
            ],
        ]);
		
        $this->add_control('product_empty_star_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Empty Star Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-rating .star-empty' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('product_rating_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-rating span' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('product_rating_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-rating span' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('product_rating_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .product-rating span' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

		} //END WOOCOMMERCE SECTION
        // ------------------------------------------------------------------------- CONTROL: Bullet Style
        $this->start_controls_section('dots_style', [
            'label' => esc_html__('Bullets Style', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'classic_layout' => 'carousel',
                'post_slider_pagination_type' => 'bullets',
            ],
        ]);

        $this->add_control('dot_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'default' => '#007194',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' =>
                    'background: {{VALUE}}',
            ],
        ]);

        $this->add_control('dot_active_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Active Color', 'bpf-widget'),
            'default' => '#0098c7',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet-active' =>
                    'background: {{VALUE}}',
            ],
        ]);

        $this->add_control('dot_size', [
            'label' => esc_html__('Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'unit' => 'px',
                'size' => 12,
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' =>
                    'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('dot_spacing', [
            'label' => esc_html__('Bullets Spacing', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'unit' => 'px',
                'size' => 3,
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-bullet' =>
                    'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('dot_spacing_wrapper', [
            'label' => esc_html__('Bullets Gap', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'unit' => 'px',
                'size' => 3,
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination' =>
                    'margin-top: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('dot_align', [
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
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination' => 'text-align: {{VALUE}}',
            ],
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Fraction Style
        $this->start_controls_section('fraction_style', [
            'label' => esc_html__('Fraction Style', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'classic_layout' => 'carousel',
                'post_slider_pagination_type' => 'fraction',
            ],
        ]);

        $this->add_control('fraction_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'default' => '#007194',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('fraction_active_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Active Color', 'bpf-widget'),
            'default' => '#0098c7',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-current' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('fraction_size', [
            'label' => esc_html__('Font Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'unit' => 'px',
                'size' => 12,
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination' =>
                    'font-size: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('fraction_spacing', [
            'label' => esc_html__('Spacing', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'unit' => 'px',
                'size' => 3,
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination' =>
                    'margin-top: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Progressbar Style
        $this->start_controls_section('progressbar_style', [
            'label' => esc_html__('Progressbar Style', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'classic_layout' => 'carousel',
                'post_slider_pagination_type' => 'progressbar',
            ],
        ]);

        $this->add_control('progressbar_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'default' => '#007194',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-progressbar' =>
                    'background: {{VALUE}}',
            ],
        ]);

        $this->add_control('progressbar_active_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Active Color', 'bpf-widget'),
            'default' => '#0098c7',
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination-progressbar-fill' =>
                    'background: {{VALUE}}',
            ],
        ]);

        $this->add_control('progressbar_size', [
            'label' => esc_html__('Height', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'unit' => 'px',
                'size' => 12,
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('progressbar_spacing', [
            'label' => esc_html__('Spacing', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'unit' => 'px',
                'size' => 3,
            ],
            'selectors' => [
                '{{WRAPPER}} .swiper-pagination' =>
                    'margin-top: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Load More Button Style
        $this->start_controls_section('load_more_style', [
            'label' => esc_html__('Load More Button', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'classic_layout!' => 'carousel',
                'pagination' => ['load_more'],
            ],
        ]);

        $this->start_controls_tabs('style_tabs');

        $this->start_controls_tab('style_normal_tab', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('load_more_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .load-more-wrapper a' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('load_more_bg_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'default' => '#0E4B65',
            'selectors' => [
                '{{WRAPPER}} .load-more-wrapper a' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'load_more_border_type',
            'label' => esc_html__('Border Type', 'bpf-widget'),
            'selector' => '{{WRAPPER}} .load-more-wrapper a',
        ]);

        $this->add_control('load_more_border_radius', [
            'label' => esc_html__('Border Radius', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .load-more-wrapper a' =>
                    'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('style_hover_tab', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('load_more_hover_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .load-more-wrapper:hover a' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('load_more_hover_bg_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .load-more-wrapper:hover a' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_control('load_more_hover_border_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Border Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .load-more-wrapper:hover a' =>
                    'border-color: {{VALUE}}',
            ],
            'condition' => [
                'load_more_border_type_border!' => '',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('load_more_gap', [
            'label' => esc_html__('Button Gap', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .load-more-wrapper' =>
                    'margin-top: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'load_more_typography',
                'selector' => '{{WRAPPER}} a.load-more',
            ]
        );

        $this->add_responsive_control('load_more_align', [
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
                'justify' => [
                    'title' => esc_html__('Justified', 'bpf-widget'),
                    'icon' => 'eicon-text-align-justify',
                ],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .load-more-wrapper' =>
                    '{{VALUE}}',
            ],
            'selectors_dictionary' => [
                'left' => 'text-align: left',
                'center' => 'text-align: center',
                'right' => 'text-align: right',
                'justify' => 'display: grid',
            ],
        ]);

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Arrow Style
        $this->start_controls_section('section_arrows_style', [
            'label' => __('Arrows Style', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'classic_layout' => 'carousel',
                'post_slider_arrows' => 'yes',
            ],
        ]);

		//OLD SWIPER VERSION
        //$this->add_control(
        //	'select_arrow',
        //	array(
        //		'label' => esc_html__( 'Choose Arrow', 'bpf-widget' ),
        //		'type' => \Elementor\Controls_Manager::ICONS,
        //		'default' => array(
        //			'value'   => 'fas fa-angle-right',
        //			'library' => 'fa-solid',
        //		),
        //		'recommended'            => array(
        //			'fa-solid'   => array(
        //				'angle-right',
        //				'angle-double-right',
        //				'chevron-right',
        //				'chevron-circle-right',
        //				'arrow-right',
        //				'long-arrow-alt-right',
        //				'caret-right',
        //				'caret-square-right',
        //				'arrow-circle-right',
        //				'arrow-alt-circle-right',
        //				'toggle-right',
        //			),
        //		),
        //	)
        //);

        $this->add_responsive_control('arrows_size', [
            'label' => __('Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'default' => ['size' => '22'],
            'range' => [
                'px' => [
                    'min' => 15,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
                    'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('arrows_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('arrows_position', [
            'label' => __('Arrows Position', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 50,
                    'step' => 1,
                ],
            ],
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->start_controls_tabs('tabs_arrows_style');

        $this->start_controls_tab('tab_arrows_normal', [
            'label' => __('Normal', 'bpf-widget'),
        ]);

        $this->add_control('arrows_bg_color_normal', [
            'label' => __('Background', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
                    'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('arrows_color_normal', [
            'label' => __('Color', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'arrows_border_normal',
            'label' => __('Border', 'bpf-widget'),
            'selector' =>
                '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after',
        ]);

        $this->add_responsive_control('arrows_border_radius_normal', [
            'label' => __('Border Radius', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_arrows_hover', [
            'label' => __('Hover', 'bpf-widget'),
        ]);

        $this->add_control('arrows_bg_color_hover', [
            'label' => __('Background', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-prev:hover:after' =>
                    'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('arrows_color_hover', [
            'label' => __('Color', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-prev:hover:after' =>
                    'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'arrows_border_hover',
            'label' => __('Border', 'bpf-widget'),
            'placeholder' => '1px',
            'default' => '1px',
            'selector' =>
                '{{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-prev:hover:after',
        ]);

        $this->add_responsive_control('arrows_border_radius_hover', [
            'label' => __('Border Radius', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-prev:hover:after' =>
                    'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // ------------------------------------------------------------------------- CONTROL: Pagination Style
        $this->start_controls_section('pagination_style', [
            'label' => esc_html__('Pagination', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [
                'pagination' => ['numbers', 'numbers_and_prev_next'],
                'classic_layout!' => 'carousel',
            ],
        ]);

        $this->add_control('pagination_gap', [
            'label' => esc_html__('Pagination Gap', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .pagination, {{WRAPPER}} .pagination-filter' => 'margin-top: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'selector' =>
                    '{{WRAPPER}} .pagination, {{WRAPPER}} .pagination-filter, {{WRAPPER}} .pagination a, {{WRAPPER}} .pagination-filter a',
            ]
        );

        $this->add_responsive_control('pagination_align', [
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
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .pagination, {{WRAPPER}} .pagination-filter' => 'text-align: {{VALUE}}',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_pagination');

        $this->start_controls_tab('style_pagination_normal_tab', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('pagination_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Pagination Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} a.page-numbers' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('pagination_bg_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} a.page-numbers' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'pagination_border_type',
            'label' => esc_html__('Border Type', 'bpf-widget'),
            'selector' =>
                '{{WRAPPER}} a.page-numbers, {{WRAPPER}} .page-numbers',
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('style_pagination_hover_tab', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('pagination_hover_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Pagination Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} a.page-numbers:hover' => 'color: {{VALUE}}',
                '{{WRAPPER}} .page-numbers:hover' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('pagination_bg_hover_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} a.page-numbers:hover' =>
                    'background-color: {{VALUE}}',
                '{{WRAPPER}} .page-numbers:hover' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_control('pagination_border_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Border Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} a.page-numbers:hover' => 'border-color: {{VALUE}}',
                '{{WRAPPER}} .page-numbers:hover' => 'border-color: {{VALUE}}',
            ],
            'condition' => [
                'pagination_border_type_border!' => '',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('style_pagination_active_tab', [
            'label' => esc_html__('Active', 'bpf-widget'),
        ]);

        $this->add_control('pagination_active_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Pagination Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .page-numbers.current' => 'color: {{VALUE}}',
                '{{WRAPPER}} .page-numbers.dots' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('pagination_bg_active_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .page-numbers.current' =>
                    'background-color: {{VALUE}}',
                '{{WRAPPER}} .page-numbers.dots' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_control('pagination_active_border_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Border Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .page-numbers.current' =>
                    'border-color: {{VALUE}}',
                '{{WRAPPER}} .page-numbers.dots' => 'border-color: {{VALUE}}',
            ],
            'condition' => [
                'pagination_border_type_border!' => '',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control('pagination_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .page-numbers' => 'padding: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_control('pagination_border_radius', [
            'label' => esc_html__('Border Radius', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .page-numbers' =>
                    'border-radius: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->add_control('pagination_space', [
            'label' => esc_html__('Space Between Numbers', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .page-numbers' =>
                    'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
            ],
        ]);

        $this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Spiner Style
        $this->start_controls_section('loader_style', [
            'label' => esc_html__('Spinner', 'cwm-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'conditions' => [
				'relation' => 'or',
				'terms' => [
					[
						'name' => 'post_skin',
						'operator' => 'in',
						'value' => ['banner', 'template', 'custom_html'],
					],
					[
						'name' => 'pagination',
						'operator' => '===',
						'value' => 'infinite',
					],
				],
			],
        ]);

        $this->add_control('loader_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Circle Color', 'bpf-widget'),
            'default' => '#0098C7',
            'selectors' => [
                '{{WRAPPER}} .preloader-inner .preloader-inner-half-circle, {{WRAPPER}} .load::before' => 'border-color: {{VALUE}}',
            ],
        ]);
		
		$this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Post Pin Style
        $this->start_controls_section('post_pin_style', [
            'label' => esc_html__('Bookmark', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
		
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pin_typography',
                'selector' =>
                    '{{WRAPPER}} .post-pin .text',
            ]
        );
		
		$this->start_controls_tabs('style_pins');
		
        $this->start_controls_tab('style_pinned_tab', [
            'label' => esc_html__('Bookmarked', 'bpf-widget'),
        ]);
		
        $this->add_control('post_pin_text_color_pinned', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Text Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .pin-text .text' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('post_pin_color_pinned', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Icon Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .pin-text i, {{WRAPPER}} .pin-text svg' => 'fill: {{VALUE}}',
            ],
        ]);
		
        $this->add_control('border_pin_color_pinned', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Stroke Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .pin-text svg' => 'stroke: {{VALUE}}',
            ],
        ]);
		
        $this->end_controls_tab();
		
        $this->start_controls_tab('style_unpinned_tab', [
            'label' => esc_html__('Unbookmarked', 'bpf-widget'),
        ]);
		
        $this->add_control('post_pin_text_color_unpinned', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Text Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .unpin-text .text' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('post_pin_color_unpinned', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Icon Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .unpin-text i, {{WRAPPER}} .unpin-text svg' => 'fill: {{VALUE}}',
            ],
        ]);
		
        $this->add_control('border_pin_color_unpinned', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Stroke Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .unpin-text svg' => 'stroke: {{VALUE}}',
            ],
        ]);
		
        $this->end_controls_tab();

        $this->end_controls_tabs();
		
        $this->add_control('pin_icon_size', [
            'label' => esc_html__('Icon Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['em', 'px'],
			'separator' => 'before',
            'selectors' => [
                '{{WRAPPER}} .post-pin i' => 'font-size: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .post-pin svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);
		
        $this->add_control('pin_icon_spacing', [
            'label' => esc_html__('Icon Spacing', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['em', 'px'],
            'default' => [
                'unit' => 'px',
                'size' => 6,
            ],
            'selectors' => [
                '{{WRAPPER}} .post-pin i' => 'margin-right: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .post-pin svg' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
        ]);
		
		$this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Edit Options Style
        $this->start_controls_section('edit_options_style', [
            'label' => esc_html__('Edit Options', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'edit_options_typography',
                'selector' =>
                    '{{WRAPPER}} .edit-options a',
            ]
        );

        $this->add_responsive_control('edit_options_align', [
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
            'selectors' => [
                '{{WRAPPER}} .edit-options' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);

        $this->start_controls_tabs('style_tabs_edit_options');

        $this->start_controls_tab('edit_options_style_normal', [
            'label' => esc_html__('Normal', 'bpf-widget'),
        ]);

        $this->add_control('edit_options_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .edit-options a' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('edit_options_background_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .edit-options a' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('edit_options_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .edit-options a' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('edit_options_margin', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
			'separator' => 'after',
            'selectors' => [
                '{{WRAPPER}} .edit-options a' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
		
        $this->add_control('edit_options_icon_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Icon Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .edit-options i, {{WRAPPER}} .edit-options svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
            ],
        ]);
		
        $this->add_control('edit_options_icon_size', [
            'label' => esc_html__('Icon Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['em', 'px'],
            'selectors' => [
                '{{WRAPPER}} .edit-options i' => 'font-size: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .edit-options svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);
		
        $this->add_control('edit_options_icon_spacing', [
            'label' => esc_html__('Icon Spacing', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['em', 'px'],
			'default' => [
				'unit' => 'px',
				'size' => 6,
			],
            'selectors' => [
                '{{WRAPPER}} .edit-options i' => 'margin-right: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .edit-options svg' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('edit_options_style_hover', [
            'label' => esc_html__('Hover', 'bpf-widget'),
        ]);

        $this->add_control('edit_options_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .edit-options a' =>
                    'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('edit_options_background_color_hover', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Background', 'bpf-widget'),
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .edit-options a' =>
                    'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_responsive_control('edit_options_padding_hover', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .edit-options a' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('edit_options_margin_hover', [
            'label' => esc_html__('Margin', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .post-wrapper:hover .edit-options a' =>
                    'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		
        // ------------------------------------------------------------------------- CONTROL: Nothing Found Message Style
        $this->start_controls_section('nothing_found_style', [
            'label' => esc_html__('Nothing Found Message', 'bpf-widget'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('nothing_found_color', [
            'type' => \Elementor\Controls_Manager::COLOR,
            'label' => esc_html__('Color', 'bpf-widget'),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .no-post' => 'color: {{VALUE}}',
            ],
        ]);
		
        $this->add_responsive_control('nothing_found_align', [
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
            'selectors' => [
                '{{WRAPPER}} .no-post' =>
                    'text-align: {{VALUE}}; justify-content: {{VALUE}};',
            ],
        ]);
		
        $this->add_control('nothing_found_font_size', [
            'label' => esc_html__('Font Size', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['em', 'px'],
            'selectors' => [
                '{{WRAPPER}} .no-post' => 'font-size: {{SIZE}}{{UNIT}}',
            ],
        ]);
		
        $this->add_responsive_control('nothing_found_padding', [
            'label' => esc_html__('Padding', 'bpf-widget'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .no-post' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
		
		$this->end_controls_section();
		
    }
	
	public function pre_get_posts_query_filter( $wp_query ) {
		$settings = $this->get_settings_for_display();
		$query_id = $settings['query_id'];
		do_action( "cwm/query/{$query_id}", $wp_query, $this);
	}
	
	function get_template_css_content($template_id) {
		$upload_dir = wp_upload_dir();
		$base_url = $upload_dir['baseurl'];

		$template_id = intval($template_id);

		$post_css_url = $base_url . '/elementor/css/post-' . $template_id . '.css';
		$loop_css_url = $base_url . '/elementor/css/loop-' . $template_id . '.css';

		$css_url = $post_css_url;
		$response = wp_remote_get($post_css_url);

		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
			$response = wp_remote_get($loop_css_url);
			if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
				$css_url = $loop_css_url;
			} else {
				$css_url = false;
			}
		}

		return $css_url ? wp_remote_retrieve_body($response) : '';
	}
	
    protected function render() {
		global $wp_query;
		$current_query_vars = $GLOBALS['wp_query']->query_vars;
        $settings = $this->get_settings_for_display();
		
		$overlay = $settings['overlay'] == 'yes' ? '<span class="overlay"></span>' : '';
		$lazy_load = $settings['post_slider_lazy_load'] == 'yes' ? 'swiper-lazy' : '';
		$class_swiper = 'elementor-grid';
		$image = '';
		if($settings['use_ajax_search']) {
			$search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';
			$placeholder_text = isset($settings['search_placeholder_text']) ? $settings['search_placeholder_text'] : '';
			$button_text = isset($settings['search_button_text']) ? $settings['search_button_text'] : 'Search';
			echo '<div class="search-container"><form action="/" method="get" autocomplete="on"><input type="text" name="s" placeholder="'. $placeholder_text .'" id="keyword" class="input_search" value="'. $search_query .'"><button type="submit">'. $button_text .'</button></form></div>';
		}	
        if ($settings['classic_layout'] == 'carousel') {
			$class_swiper = 'elementor-grid cwm-swiper';
        }
		if ($settings['classic_layout'] == 'masonry') {
			$class_swiper = 'elementor-grid cwm-masonry';
        }
		
		$skin = esc_attr($settings['post_skin']);
		$post_html_tag = esc_attr($settings['post_html_tag']);

		if ( get_query_var( 'page_num' ) ) {
			$paged = get_query_var( 'page_num' );
		} elseif ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$paged = get_query_var( 'page' );
		} else {
			$paged = 1;
		}
		
		$query_args = [
			'order' => in_array($settings['order'], ['ASC', 'DESC']) ? $settings['order'] : 'DESC',
			'orderby' => !empty($settings['orderby']) ? $settings['orderby'] : 'date',
			'post_status' => !empty($settings['post_status']) ? $settings['post_status'] : 'publish',
			'posts_per_page' => !empty($settings['posts_per_page']) ? $settings['posts_per_page'] : get_option('posts_per_page'),
			'ignore_sticky_posts' => 'yes' === $settings['sticky_posts'] ? 0 : 1,
			'fields' => 'ids',
		];

        if (!empty($settings['post_type'])) {
			$query_args['post_type'] = $settings['post_type'];
        }
		
        if ($settings['pagination'] != 'none') {
			$query_args['paged'] = $paged;
        }
		
		$post_in_id = 'post__in_'. $settings['post_type'];
		
		if($settings['include_post_id']) {
			$post_ids = explode(',', $settings['include_post_id']);
			$query_args['post__in'] = $post_ids;
		} elseif (isset($settings[$post_in_id]) && empty($settings['include_post_id'])) {
            $query_args['post__in'] = $settings[$post_in_id];
        }
		
		if ($settings['pinned_posts']) {
			$pinned_posts = get_user_meta( get_current_user_id(), 'post_id_list', true );
			if(empty($pinned_posts)) {
				if(isset($_COOKIE['post_id_list'])) {
					$pinned_posts = json_decode(stripslashes($_COOKIE['post_id_list']), true);
					if (is_null($pinned_posts)) {
						$pinned_posts = array();
					}
				}
			}
			if($pinned_posts) {
				$query_args['post__in'] = $pinned_posts;
			} else {
				$query_args['post__in'] = array(0);
			}
		}
		
		$pinned_post = $settings['pinned_posts'] ? 'pinned_post_query' : '';
		
		$post_not_in_id = 'post__not_in_'. $settings['post_type'];
		if($settings['exclude_post_id']) {
			$exclude_post_ids = explode(',', $settings['exclude_post_id']);
			$query_args['post__not_in'] = $exclude_post_ids;
		} elseif (isset($settings[$post_not_in_id]) && empty($settings['exclude_post_id'])) {
            $query_args['post__not_in'] = $settings[$post_not_in_id];
        }
		
        if ($settings['related_posts']) {
			if($settings['post_type'] === 'post') {
				$query_args['category__in'] = wp_get_post_categories( get_the_ID() );
				$query_args['post__not_in'] = array( get_the_ID() );
			} else {
				$tax = $settings['related_post_taxonomy'];
				$terms = get_the_terms( get_the_ID() , $tax, 'string');
				$term_ids = wp_list_pluck($terms,'term_id');
				
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => $tax,
						'field' => 'id',
						'terms' => $term_ids,
						'operator'=> 'IN'
					)
				);
				$query_args['post__not_in'] = array( get_the_ID() );
			}
        }

		$taxonomy = get_object_taxonomies($settings['post_type'], 'objects');
			$tax_cat_in     = '';
			$tax_cat_not_in = '';
			$tax_tag_in     = '';
			$tax_tag_not_in = '';

			if ( ! empty( $taxonomy ) ) {

				foreach ( $taxonomy as $index => $tax ) {

					$tax_control_key = $index .'_'. $settings['post_type'];

						if ( 'post' === $settings['post_type'] ) {
							if ( 'post_tag' === $index ) {
								$tax_control_key = 'tags';
							} elseif ( 'category' === $index ) {
								$tax_control_key = 'categories';
							}
						}

					if ( ! empty( $settings[ $tax_control_key ] ) ) {

						$operator = $settings[ $index .'_'. $settings['post_type'] .'_filter_type' ];
						
						$query_args['tax_query'][] = array(
							'taxonomy' => $index,
							'field'    => 'term_id',
							'terms'    => $settings[$tax_control_key],
							'operator' => $operator,
						);

						switch ( $index ) {
							case 'category':
								if ( 'IN' === $operator ) {
									$tax_cat_in = $settings[ $tax_control_key ];
								} elseif ( 'NOT IN' === $operator ) {
									$tax_cat_not_in = $settings[ $tax_control_key ];
								}
								break;

							case 'post_tag':
								if ( 'IN' === $operator ) {
									$tax_tag_in = $settings[ $tax_control_key ];
								} elseif ( 'NOT IN' === $operator ) {
									$tax_tag_not_in = $settings[ $tax_control_key ];
								}
								break;
						}
					}
				}
			}
		
			//CONTENT
			if ( !empty( $settings['query_id'] ) ) {
				add_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
			}
			
			if ($settings['query_type'] === 'main') {
				$cwm_query = new WP_Query($current_query_vars);
			} elseif ($settings['query_type'] === 'custom') {
				$cwm_query = new WP_Query($query_args);
			}
			
			remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
			
			if($settings['query_type'] === 'main' || $settings['query_type'] === 'custom') {
            if ($cwm_query->have_posts()) {
			
				$counter = 0;
				$extra_templates_by_position = [];
				$template_css_urls = [];
				
				if (isset($settings['extra_skin_list']) && is_array($settings['extra_skin_list'])) {
					foreach ($settings['extra_skin_list'] as $item) {
						$extra_templates_by_position[$item['grid_position']] = $item;
					}
				}

				// Collect CSS contents for the main template
				$combined_css = '';
				//$template_css_urls = []; // Array to store the URLs for debugging

				if ($settings['skin_template']) {
					$main_template_id = intval($settings['skin_template']);
					$main_css_content = $this->get_template_css_content($main_template_id);
					if ($main_css_content) {
						$combined_css .= $main_css_content;
						//$template_css_urls[$main_template_id] = $main_css_content; // Store content for debugging
					}
				}

				// Collect CSS contents for the extra templates
				foreach ($extra_templates_by_position as $extra_template) {
					$extra_template_id = intval($extra_template['extra_template_id']);
					$extra_css_content = $this->get_template_css_content($extra_template_id);
					if ($extra_css_content) {
						$combined_css .= $extra_css_content;
						//$template_css_urls[$extra_template_id] = $extra_css_content; // Store content for debugging
					}
				}

				// Output combined CSS
				if ($combined_css) {
					echo '<style id="elementor-combined-css">' . $combined_css . '</style>';
				}
				
                echo '
				<div class="post-container '. $settings['pagination'] .' '. $skin .' '. $pinned_post .'" data-total-post="'. $cwm_query->found_posts .'">
                <div class="post-container-inner">
				<div class="'. $class_swiper .'">
				';

                while ($cwm_query->have_posts()) :
					$counter++;
                    $cwm_query->the_post();
					
					$permalink = esc_url(get_permalink());
					$new_tab = '';
					$settings['external_url_new_tab'] ? $new_tab = 'target="_blank"' : $new_tab = '';
					
					if ($settings['post_external_url']) {
						$external_url = get_post_meta( get_the_ID(), $settings['post_external_url'], true );
						
						if(strpos($settings['post_external_url'],'http') !== false) {
							$external_url = $settings['post_external_url'];
						}
						if ($external_url) {
							$permalink = $external_url;
						} elseif ($settings['post_external_if_empty']) {
							$permalink = esc_url(get_permalink());
							$new_tab = '';
						} else {
							$permalink = '';
						}
					}

					// Check if the current position should have an extra template
					$use_extra_template = false;
					$extra_template_id = '';
					$column_span = 1;
					$row_span = 1;
					$column_span_style = '';
					$row_span_style = '';

					foreach ($extra_templates_by_position as $position => $extra_template) {
						// Check if the template should apply once or be repeated
						$apply_once = isset($extra_template['apply_once']) && $extra_template['apply_once'] === 'yes';

						if (($apply_once && $counter == $position) || (!$apply_once && $counter % $position == 0)) {
							$use_extra_template = true;
							$extra_template_id = intval($extra_template['extra_template_id']);
							$column_span = $extra_template['column_span'];
							$row_span = $extra_template['row_span'];
							$column_span_style = $column_span > 1 ? 'grid-column: span '.$column_span.';' : '';
							$row_span_style = $row_span > 1 ? 'grid-row: span '.$row_span.';' : '';
							break;
						}
					}

					$style = trim("$column_span_style $row_span_style");
					$style_attribute = $style ? 'style="'.$style.'"' : '';

					if ($settings['skin_template']) {
						if ($use_extra_template) {
							echo '<'. $post_html_tag .' class="post-wrapper" '.$style_attribute.'><div class="inner-content">';
							echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($extra_template_id);
							echo '</div></'. $post_html_tag .'>';
						} else {
							echo '<'. $post_html_tag .' class="post-wrapper"><div class="inner-content">';
							echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($settings['skin_template']);
							echo '</div></'. $post_html_tag .'>';
						}
					} elseif ($settings['skin_custom_html']) {
						$image = '<img style="background-image: url('. get_the_post_thumbnail_url($cwm_query->ID, 'full') .')" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Post Image Placeholder"/>';
						if (!get_the_post_thumbnail_url()) {
							$image = '<img style="background-image: url('. $settings['post_default_image']['url'] .')" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Post Image Placeholder"/>';
						}
						$html_content = $settings['skin_custom_html'];
						$html_content = str_replace("#TITLE#", esc_html(get_the_title()), $html_content);
						$html_content = str_replace("#PERMALINK#", esc_url(get_permalink()), $html_content);
						$html_content = str_replace("#CONTENT#", wp_kses_post(get_the_content()), $html_content);
						$html_content = str_replace("#EXCERPT#", esc_html(get_the_excerpt()), $html_content);
						$html_content = str_replace("#IMAGE#", $image, $html_content);
						echo '<' . $post_html_tag . ' class="post-wrapper"><div class="inner-content">';
						echo wp_kses_post($html_content);
						echo '</div></' . $post_html_tag . '>';
					} else {
					echo '<'. $post_html_tag .' class="post-wrapper">';
						if ($settings['show_featured_image'] == 'yes') {
							$image_size = $settings['featured_img_size'] ? $settings['featured_img_size'] : 'full';
							
							if ($settings['img_equal_height'] == 'yes') {
									if ($lazy_load) {
										$image = '<img class="swiper-lazy" data-background="'. get_the_post_thumbnail_url($cwm_query->ID, $image_size) .'" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Post Image Placeholder"/><div class="swiper-lazy-preloader"></div>';
									} else {
										$image = '<img style="background-image: url('. get_the_post_thumbnail_url($cwm_query->ID, $image_size) .')" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Post Image Placeholder"/>';
									}
								if (!get_the_post_thumbnail_url()) {
									if ($lazy_load) {
										$image = '<img class="swiper-lazy" data-background="'. $settings['post_default_image']['url'] .'" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Post Image Placeholder"/><div class="swiper-lazy-preloader"></div>';
									} else {
										$image = '<img style="background-image: url('. $settings['post_default_image']['url'] .')" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Post Image Placeholder"/>';
									}
								}
							} else {
								if ($lazy_load) {
									$post_thumbnail_url = get_the_post_thumbnail_url($cwm_query->ID, $image_size);
									$image = '<img class="swiper-lazy" data-src="'. $post_thumbnail_url .'"><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = get_the_post_thumbnail($cwm_query->ID, $image_size);
								}
								if (!$image) {
									if ($lazy_load) {
										$image = '<img class="swiper-lazy" data-src="'. $settings['post_default_image']['url'] .'" alt="Post Image Placeholder"/><div class="swiper-lazy-preloader"></div>';
									} else {
										$image = '<img src="'. $settings['post_default_image']['url'] .'" alt="Post Image Placeholder"/>';
									}
								}
							}
							
							if ($settings['post_image_url'] && !empty($permalink)) {
								echo '<div class="post-image"><a href="'. $permalink .'" '. $new_tab .'>'. $image . $overlay .'</a></div>';
							} else {
								echo '<div class="post-image">'. $image . $overlay .'</div>';
							}
						}
					echo '<div class="inner-content">';
					
					foreach ( $settings['post_list'] as $index => $item ):
						
						//Display Pseudo
						$before = '';
						$after = '';
						
						$item['field_before'] ? $before = '<span class="pseudo">'. str_replace("#", $counter, $item['field_before']) .'</span>' : $before = '';
						$item['field_after'] ? $after = '<span class="pseudo">'. str_replace("#", $counter, $item['field_after']) .'</span>' : $after = '';				
						
						//Display Title
						if ($item['post_content'] === 'Title') {
							if($item['post_title_url'] && !empty($permalink)) {
								echo '<'. $settings['html_tag'] .' class="post-title elementor-repeater-item-' . $item['_id'] .'"><a href="'. $permalink .'" '. $new_tab .'>'. $before . wp_trim_words( get_the_title(), $item['title_length'], '...' ) . $after .'</a></'. $settings['html_tag'] .'>';
							} else {
								echo '<'. $settings['html_tag'] .' class="post-title elementor-repeater-item-' . $item['_id'] .'">'. $before . wp_trim_words( get_the_title(), $item['title_length'], '...' ) . $after .'</'. $settings['html_tag'] .'>';
							}
						}
						
						//Display Content
						if ($item['post_content'] === 'Content') {
							$content = get_the_content();
							$content = apply_filters( 'the_content', $content );
							$content = str_replace( ']]>', ']]&gt;', $content );
							echo '<div class="post-content elementor-repeater-item-' . $item['_id'] .'"><p>'. $before . wp_trim_words( $content, $item['description_length'], '...' ) . $after .'</p></div>';
						}
						
						//Display Excerpt
						if ($item['post_content'] === 'Excerpt') {     
							echo '<div class="post-excerpt elementor-repeater-item-' . $item['_id'] .'"><p>'. $before . wp_trim_words( get_the_excerpt(), $item['description_length'], '...' ) . $after .'</p></div>';
						}
						
						//Display Custom Field
						if ($item['post_content'] === 'Custom Field') {
							$custom_field_val = get_post_meta( get_the_ID(), sanitize_key($item['post_field_key']), true );
							if ($custom_field_val) {
								echo '<div class="post-custom-field elementor-repeater-item-' . $item['_id'] .'">'. $before . $custom_field_val . $after .'</div>';
							}
						}
						
						//Display ACF
						if ($item['post_content'] === 'ACF' && class_exists('ACF')) {
							$custom_field_val = get_field(sanitize_key($item['post_field_key']), get_the_ID());
							if ($custom_field_val) {
								echo '<div class="post-custom-field elementor-repeater-item-' . $item['_id'] .'">'. $before . $custom_field_val . $after .'</div>';
							}
						}
						
						// Display HTML with Shortcode Support
						if ($item['post_content'] === 'HTML') {		
							$content = $before . wp_kses_post($item['post_html']) . $after;
							// Apply do_shortcode to the entire HTML content
							$content = do_shortcode($content);
							echo '<div class="post-html elementor-repeater-item-' . $item['_id'] .'">'. $content .'</div>';
						}
						
						//Display Post Meta
						if ($item['post_content'] === 'Post Meta') {
							$author_icon = isset($item['author_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['author_icon']) : '';
							$date_icon = isset($item['date_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['date_icon']) : '';
							$comment_icon = isset($item['comment_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['comment_icon']) : '';
							
							$item['post_author_url'] == 'yes' ? $author = get_the_author_posts_link() : $author = get_the_author();
							
							get_comments_number() <= 1 ? $comment = get_comments_number() .' comment' : $comment = get_comments_number() .' comments';
								
							$display_author = $item['display_meta_author'] == 'yes' ? $author_icon . $author : '';
							$date_format = $item['display_date_format'] ? $item['display_date_format'] : 'j M. Y';
							if($date_format === 'from_time') {
								$post_date = get_the_date('Y-m-d H:i:s');
								$display_date = $item['display_meta_date'] == 'yes' ? $item['post_meta_separator'] . $date_icon . BPF_Helper::time_elapsed_string($post_date) : '';
							} else {
								$display_date = $item['display_meta_date'] == 'yes' ? $item['post_meta_separator'] . $date_icon . get_the_date($date_format) : '';
							}
							$display_comment = $item['display_meta_comment'] == 'yes' ? $item['post_meta_separator'] . $comment_icon . $comment : '';
								
							echo '<div class="post-meta elementor-repeater-item-' . $item['_id'] .'">'. $before . $display_author . $display_date . $display_comment . $after .'</div>';
						}
						
						//Display Taxonomy
						if ($item['post_content'] === 'Taxonomy') {
							$terms_nb = $item['post_taxonomy_nb'];
							$terms = wp_get_object_terms(get_the_ID(), $item['post_taxonomy']);
							
						if ($terms) {
							echo '<ul class="post-taxonomy elementor-repeater-item-' . $item['_id'] .'">';
								$i = 0;
							foreach( $terms as $term ) {
							if ( $terms_nb > $i )
							if ( 0 == $term->count )
								echo '<li>'. $before . $term->name . $after .'</li>';
							elseif ( $term->count > 0 )
								echo '<li><a href="'. get_term_link( $term ) .'">'. $before . $term->name . $after . '</a></li>';
								$i++;
							}
								echo '</ul>';
							}

						}
						
						//Display Read More
						if ($item['post_content'] === 'Read More') {
							if(!empty($permalink)) {
								echo '<a class="post-read-more elementor-repeater-item-' . $item['_id'] .'" href="'. $permalink .'" '. $new_tab .'>'. $before . sanitize_text_field($item['post_read_more_text']) . $after .'</a>';
							} else {
								echo '<span class="post-read-more elementor-repeater-item-' . $item['_id'] .'">'. $before . sanitize_text_field($item['post_read_more_text']) . $after .'</span>';
							}
						}
						
						//Display Pin
						if ($item['post_content'] === 'Pin Post') {
							$pin_icon = isset($item['pin_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['pin_icon']) : '';
							$unpin_icon = isset($item['unpin_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['unpin_icon']) : '';
							
							$post_id = get_the_ID();
							$user_id = get_current_user_id();
							$post_list = array(); // Initialize as an empty array

							if (!empty($user_id)) {
								$user_post_list = get_user_meta($user_id, 'post_id_list', true);
								if (is_array($user_post_list)) {
									$post_list = $user_post_list;
								}
							} elseif (isset($_COOKIE['post_id_list'])) {
								$post_list = json_decode(stripslashes($_COOKIE['post_id_list']), true);
								if (is_null($post_list)) {
									$post_list = array();
								}
							}

							if (($item['post_pin_logged_out'] && !empty($user_id)) || empty($item['post_pin_logged_out'])) {
								$class = in_array($post_id, $post_list) ? 'unpin' : '';
								echo '<a class="post-pin elementor-repeater-item-' . $item['_id'] . ' ' . $class . '" href="#" data-postid="'. $post_id .'">'. $before . '<span class="pin-text">'. $pin_icon .'<span class="text">'. $item['pin_text'] .'</span></span><span class="unpin-text">'. $unpin_icon .'<span class="text">'. $item['unpin_text'] .'</span></span>' . $after .'</a>';
							}
						}
						
						//Display Edit Options
						if ($item['post_content'] === 'Edit Options') {
							$edit_icon = isset($item['edit_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['edit_icon']) : '';
							$delete_icon = isset($item['delete_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['delete_icon']) : '';
							$republish_icon = isset($item['republish_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['republish_icon']) : '';
							$unpublish_icon = isset($item['unpublish_icon']) ? BPF_Helper::cwm_get_icons($icon = $item['unpublish_icon']) : '';
							
							$current_user = wp_get_current_user();
							$post_id = get_the_ID();
							$edit_url = $item['edit_url'] ? str_replace('#ID#', $post_id, $item['edit_url']) : '#';
							if( get_post_status() == 'draft' ) {
								$item['display_republish_option'] == 'yes' ? $republish = '<a class="edit-button" data-postid="'.$post_id.'" href="#">'. $republish_icon . BPF_Helper::sanitize_text_with_svg($input = $item['republish_option_text']) .'</a>' : $republish = '';
							} else {
								$republish = '';
							}
							if( get_post_status() == 'publish' ) {
								$item['display_unpublish_option'] == 'yes' ? $unpublish = '<a class="unpublish-button" data-postid="'.$post_id.'" href="#">'. $unpublish_icon . BPF_Helper::sanitize_text_with_svg($input =$item['unpublish_option_text']) .'</a>' : $unpublish = '';
							} else {
								$unpublish = '';
							}
							$item['display_edit_option'] == 'yes' ? $edit = '<a class="edit-post" href="'. $edit_url .'">'. $edit_icon . $item['edit_option_text'] .'</a>' : $edit = '';
							$item['display_delete_option'] == 'yes' ? $delete = '<a class="delete-post" href="'. get_delete_post_link( $post_id ) .'">'. $delete_icon . $item['delete_option_text'] .'</a>' : $delete = '';
							if(current_user_can( 'edit_post', $post_id ) && (get_post_field( 'post_author', $post_id ) == $current_user->ID))  {
								echo '<div class="edit-options elementor-repeater-item-' . $item['_id'] .'">'. $republish . $edit . $unpublish . $delete .'</div>';
							}
						}
						
						//WOOCOMMERCE SECTION
						if ( class_exists( 'WooCommerce') ) {
						$product = wc_get_product( get_the_ID() );
						if($product) {
						//Display Product Price
						if ($item['post_content'] === 'Product Price') {
							if($product) {
								echo '<div class="product-price elementor-repeater-item-' . $item['_id'] .'">'. $before . $product->get_price_html() . $after .'</div>';
							}
						}
						
						//Display Product Rating
						if ($item['post_content'] === 'Product Rating') {
							if($product) {
								$product_rating = $product->get_average_rating();
								$stars = "";
								for ($i = 1; $i <= 5; $i++) { 
									$i <= $product_rating ? $stars .= "<span class='star-full'>&#9733;</span>" : $stars .= "<span class='star-empty'>&#9734;</span>";
								}
								echo '<div class="product-rating elementor-repeater-item-' . $item['_id'] .'">'. $before . $stars . $after .'</div>';
							}
						}
						
						//Display Buy Now Button
						if ($item['post_content'] === 'Buy Now') {
							if( $product->is_type( 'variable' ) ) {
								echo '<a class="product-buy-now variable elementor-repeater-item-' . $item['_id'] .'" href="'. get_the_permalink() .'" '. $new_tab .'>'. $before . 'Choose an option' . $after .'</a>';
							} 
							if( $product->is_type( 'simple' ) ) {
								echo '<a class="product-buy-now simple elementor-repeater-item-' . $item['_id'] .'" href="'. wc_get_checkout_url() .'?add-to-cart='. get_the_ID() .'" '. $new_tab .'>'. $before . $item['product_buy_now_text'] . $after .'</a>';
							}
						}
						
						//Display Product Bagde
						if ($item['post_content'] === 'Product Bagde') {							
							if($item['display_on_sale'] && $product->is_on_sale()) {
								echo '<div class="product-badge elementor-repeater-item-' . $item['_id'] .'">'. $before . $item['on_sale_text'] . $after .'</div>';
							} elseif($item['display_new_arrival'] && $item['display_best_seller']) {
								$newness_days = 30;
								$created = strtotime( $product->get_date_created() );
							if ( $product->is_featured() ) {
									echo '<div class="product-badge elementor-repeater-item-' . $item['_id'] .'">'. $before . $item['best_seller_text'] . $after .'</div>';
								}
							elseif ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
									echo '<div class="product-badge elementor-repeater-item-' . $item['_id'] .'">'. $before . $item['new_arrival_text'] . $after .'</div>';
							}
							} elseif(!$item['display_new_arrival'] && $item['display_best_seller']) {
								if ( $product->is_featured() ) {
									echo '<div class="product-badge elementor-repeater-item-' . $item['_id'] .'">'. $before . $item['best_seller_text'] . $after .'</div>';
								}
							} elseif($item['display_new_arrival'] && !$item['display_best_seller']) {
								if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
									echo '<div class="product-badge elementor-repeater-item-' . $item['_id'] .'">'. $before . $item['new_arrival_text'] . $after .'</div>';
								}
							}
						}
						
						}
						
						}
					
					endforeach;
			
					echo '</div>';
					echo '</'. $post_html_tag .'>';
					}
                endwhile;
                echo '</div>';

				if ('numbers' === $settings['pagination'] || 'numbers_and_prev_next' === $settings['pagination']) {
					$total_pages = $cwm_query->max_num_pages;

					if ($total_pages > 1) {
						// Set the base URL and current page number
						$base = '';
						$current_page = 1;

						if ($settings['query_type'] === 'main') {
							$link_unescaped = get_pagenum_link(1, false);
							$base = strtok($link_unescaped, '?') . '%_%';
							$current_page = max(1, get_query_var('paged'));	
						} elseif ($settings['query_type'] === 'custom') {
							$base = add_query_arg('paged', '%#%');
							$current_page = max(1, get_query_var('paged'));
								
							if (is_home() || is_archive()) {
								$base = trailingslashit(get_pagenum_link(1)) . '%_%';
								$current_page = max(1, get_query_var('paged'));
							}
							if (is_front_page()) {
								$link_unescaped = get_pagenum_link(1, false);
								$base = strtok($link_unescaped, '?') . '%_%';								
								$current_page = max(1, get_query_var('page'));
							}
							if (is_author()) {
								$base = add_query_arg('page', '%#%');
								$current_page = max(1, get_query_var('page'));
							}
							if (is_single()) {
								if (!is_page()) {
									$base = add_query_arg('page_num', '%#%');
									$current_page = max(1, get_query_var('page_num'));
								} else {
									$base = add_query_arg('page', '%#%');
									$current_page = max(1, get_query_var('page'));
								}
							}
							
						}

						$nav_start = '<nav class="pagination" role="navigation" data-page="'. $current_page .'" data-max-page="'. $total_pages .'" aria-label="Pagination">';
						
						echo $nav_start;

						echo paginate_links([
							'base' => $base,
							'current' => $current_page,
							'total' => $total_pages,
							'prev_text' => ('numbers_and_prev_next' === $settings['pagination']) ? __('« prev') : false,
							'next_text' => ('numbers_and_prev_next' === $settings['pagination']) ? __('next »') : false,
						]);

						echo '</nav>';
					}

				}

				if ('load_more' === $settings['pagination'] || 'infinite' === $settings['pagination']) {
					$total_pages = $cwm_query->max_num_pages;
					
					if ($total_pages > 1) {						
						// Set the base URL and current page number
						$base = '';
						$current_page = 1;

						if ($settings['query_type'] === 'main') {
							$link_unescaped = get_pagenum_link(1, false);
							$base = strtok($link_unescaped, '?') . '%_%';
							$current_page = max(1, get_query_var('paged'));
						} elseif ($settings['query_type'] === 'custom') {
							$base = add_query_arg('paged', '%#%');
							$current_page = max(1, get_query_var('paged'));
								
							if (is_home() || is_archive()) {
								$base = trailingslashit(get_pagenum_link(1)) . '%_%';
								$current_page = max(1, get_query_var('paged'));
							}
							if (is_front_page()) {
								$link_unescaped = get_pagenum_link(1, false);
								$base = strtok($link_unescaped, '?') . '%_%';								
								$current_page = max(1, get_query_var('page'));
							}
							if (is_author()) {
								$base = add_query_arg('page', '%#%');
								$current_page = max(1, get_query_var('page'));
							}
							if (is_single()) {
								if (!is_page()) {
									$base = add_query_arg('page_num', '%#%');
									$current_page = max(1, get_query_var('page_num'));
								} else {
									$base = add_query_arg('page', '%#%');
									$current_page = max(1, get_query_var('page'));
								}
							}
							
						}
						
						$nav_start = '<nav style="display: none;" class="pagination" role="navigation" data-page="'. $current_page .'" data-max-page="'. $total_pages .'" aria-label="Pagination">';
						
						echo $nav_start;

						echo paginate_links([
							'base' => $base,
							'current' => $current_page,
							'total' => $total_pages,
							'prev_text' => ('numbers_and_prev_next' === $settings['pagination']) ? __('« prev') : false,
							'next_text' => ('numbers_and_prev_next' === $settings['pagination']) ? __('next »') : false,
						]);

						echo '</nav>';
						
						if('infinite' === $settings['pagination'] && $settings['hide_infinite_load'] != 'yes') {
						echo '
						<div class="cwm-infinite-scroll-preloader">
						<span class="preloader-inner">
							  <span class="preloader-inner-gap"></span>
							  <span class="preloader-inner-left">
								  <span class="preloader-inner-half-circle"></span>
							  </span>
							  <span class="preloader-inner-right">
								  <span class="preloader-inner-half-circle"></span>
							  </span>
						 </span>
						 </div>
						';
						}
						
						if('load_more' === $settings['pagination']) {
						echo '
						<div class="elementor-button-wrapper load-more-wrapper">
							<a href="#" class="elementor-button load-more">Load More</a>
						</div>
						';
						}
					}
					
				}
				
                echo '
				</div>
				</div>
				';
				if ('infinite' === $settings['pagination']) {
					echo '
						<div class="e-load-more-anchor"></div>
					';
				}
            } else {
				echo '
				<div class="post-container '. $skin .' '. $pinned_post .'">
					<div class="post-container-inner">
						<div class="no-post">'. $settings['nothing_found_message'] .'</div>
					</div>
				</div>
				';
            }
			wp_reset_postdata();
			}
			
			if($settings['query_type'] === 'user') {
			$user_query = new WP_User_Query(
				array(
					'order'     => in_array($settings['order'], ['ASC', 'DESC']) ? $settings['order'] : 'DESC',
					'orderby'   => !empty($settings['orderby']) ? $settings['orderby'] : 'date',
					'number'    => $settings['posts_per_page'],
					'paged'     => $paged,
					'role__in'  => !empty($settings['selected_roles']) ? $settings['selected_roles'] : array(),
					'role__not_in' => !empty($settings['excluded_roles']) ? $settings['excluded_roles'] : array(),
					'meta_query' => array(
						!empty($settings['user_meta_key']) ? array(
							'key'     => $settings['user_meta_key'],
							'value'   => !empty($settings['user_meta_value']) ? $settings['user_meta_value'] : '',
							'compare' => 'LIKE',
						) : array(),
					),
				)
			);
				
			if (!empty($user_query->get_results())) {
				$before = '';
				$after = '';
                echo '
				<div class="loader" style="display:none;"><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div></div>
				<div class="post-container '. $settings['pagination'] .' '. $skin .' '. $pinned_post .'" data-nb-column="'. $settings['post_slider_slides_per_view'] .'">
                <div class="post-container-inner">
				<div class="'. $class_swiper .'">
				';
				// Loop through the users
				foreach ($user_query->get_results() as $user) {
					global $user_id;
					$user_id = $user->ID;
					$user_profile_url = get_author_posts_url($user_id);
					$new_tab = '';
					$permalink = esc_url($user_profile_url);
					
					echo '<div class="post-wrapper">';
					
					if ($settings['show_featured_image'] == 'yes') {
						// Get the user's profile picture URL
						$avatar_tag = str_contains(get_avatar($user_id, array('size' => 'full')), 'gravatar') ? $settings['post_default_image']['url'] : get_avatar($user_id, array('size' => 'full'));

						// Extract the URL from the HTML using a regular expression
						preg_match('/src=["\']?([^"\'>]+)["\']?/i', $avatar_tag, $matches);
						$profile_picture_url = isset($matches[1]) ? $matches[1] : '';

						if ($settings['img_equal_height'] == 'yes') {
							if ($profile_picture_url) {
								if($lazy_load) {
									$image = '<img class="swiper-lazy" data-background="'. $profile_picture_url .'" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Profile Picture Placeholder"/><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = '<img style="background-image: url('. $profile_picture_url .')" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Profile Picture Placeholder"/>';
								}
							} else {
								if($lazy_load) {
									$image = '<img class="swiper-lazy" data-background="'. $settings['post_default_image']['url'] .'" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Profile Picture Placeholder"/><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = '<img style="background-image: url('. $settings['post_default_image']['url'] .')" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/CWM-Placeholder-Image-'.$settings['img-aspect-ratio'].'.png" alt="Profile Picture Placeholder"/>';
								}
							}
						} else {
								if($lazy_load) {
									$image = '<img class="swiper-lazy" data-src="'. $profile_picture_url .'" alt="Profile Picture"/><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = '<img src="'. $profile_picture_url .'" alt="Profile Picture"/>';
								}
							if (!$image) {
								if($lazy_load) {
									$image = '<img class="swiper-lazy" data-src="'. $settings['post_default_image']['url'] .'" alt="Profile Picture Placeholder"/><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = '<img src="'. $settings['post_default_image']['url'] .'" alt="Profile Picture Placeholder"/>';
								}
							}
						}

						if ($settings['post_image_url'] && !empty($permalink)) {
							echo '<div class="post-image"><a href="'. $permalink .'" '. $new_tab .'>'. $image . $overlay .'</a></div>';
						} else {
							echo '<div class="post-image">'. $image . $overlay .'</div>';
						}
					}

					echo '<div class="inner-content">';
					
					foreach ( $settings['user_list'] as $index => $item ):	

						// WordPress Username
						if ($item['post_content'] === 'Username') {
							if ($item['display_name_url'] && !empty($permalink)) {
								echo '<'. $settings['html_tag'] .' class="user-username elementor-repeater-item-' . $item['_id'] .'"><a href="'. $permalink .'" '. $new_tab .'>'. $user->user_login .'</a></'. $settings['html_tag'] .'>';
							} else {
								echo '<'. $settings['html_tag'] .' class="user-username elementor-repeater-item-' . $item['_id'] .'">'. $before . $user->user_login . $after .'</'. $settings['html_tag'] .'>';
							}
						}					
						
						//Display Name
						if ($item['post_content'] === 'Display Name') {
							if($item['display_name_url'] && !empty($permalink)) {
								echo '<'. $settings['html_tag'] .' class="user-display-name elementor-repeater-item-' . $item['_id'] .'"><a href="'. $permalink .'" '. $new_tab .'>'. $user->display_name .'</a></'. $settings['html_tag'] .'>';
							} else {
								echo '<'. $settings['html_tag'] .' class="user-display-name elementor-repeater-item-' . $item['_id'] .'">'. $before . $user->display_name . $after .'</'. $settings['html_tag'] .'>';
							}
						}
						//Display Full Name
						if ($item['post_content'] === 'Full Name') {
							if($item['display_name_url'] && !empty($permalink)) {
								echo '<'. $settings['html_tag'] .' class="user-full-name elementor-repeater-item-' . $item['_id'] .'"><a href="'. $permalink .'" '. $new_tab .'>'. $user->first_name .' '. $user->last_name .'</a></'. $settings['html_tag'] .'>';
							} else {
								echo '<'. $settings['html_tag'] .' class="user-full-name elementor-repeater-item-' . $item['_id'] .'">'. $user->first_name .' '. $user->last_name .'</'. $settings['html_tag'] .'>';
							}
						}
						//Display User Meta
						if ($item['post_content'] === 'User Meta') {
							$custom_field_val = get_user_meta( $user_id, $item['user_field_key'], true );
							if ($custom_field_val) {
								echo '<div class="user-meta-field elementor-repeater-item-' . $item['_id'] .'">'. $custom_field_val .'</div>';
							}
						}
						//Display Email
						if ($item['post_content'] === 'User Email') {
							echo '<'. $settings['html_tag'] .' class="user-email elementor-repeater-item-' . $item['_id'] .'">'. $user->user_email .'</'. $settings['html_tag'] .'>';
						}
						//Display User Role
						if ($item['post_content'] === 'User Role') {
							echo '<'. $settings['html_tag'] .' class="user-role elementor-repeater-item-' . $item['_id'] .'">'. implode(', ', array_map('ucwords', $user->roles)) .'</'. $settings['html_tag'] .'>';
						}
						//Display User ID
						if ($item['post_content'] === 'User ID') {
							echo '<'. $settings['html_tag'] .' class="user-id elementor-repeater-item-' . $item['_id'] .'">'. $user_id .'</'. $settings['html_tag'] .'>';
						}
						//Display Visit Profile
						if ($item['post_content'] === 'Visit Profile') {
							if(!empty($permalink)) {
								echo '<a class="visit-profile elementor-repeater-item-' . $item['_id'] .'" href="'. $permalink .'" '. $new_tab .'>'. $item['visit_profile_text'] .'</a>';
							} else {
								echo '<span class="visit-profile elementor-repeater-item-' . $item['_id'] .'">'. $item['visit_profile_text'] .'</span>';
							}
						}
						// Display HTML with Shortcode Support
						if ($item['post_content'] === 'HTML') {
							$content = $before . wp_kses_post($item['user_html']) . $after;

							// Apply do_shortcode to the entire HTML content
							$content = do_shortcode($content);

							echo '<div class="post-html elementor-repeater-item-' . $item['_id'] .'">'. $content .'</div>';
						}
						
					endforeach;
					echo '
					</div>
					</div>
					';
				}
				echo '
				</div>
				';
				if ('numbers' === $settings['pagination'] || 'numbers_and_prev_next' === $settings['pagination']) {
					$total_users = $user_query->get_total();
		
					if ($total_users > 1) {						
						// Set the base URL and current page number
						$base = '';
						$current_page = '';

						$base = add_query_arg('paged', '%#%');
						$current_page = max(1, get_query_var('paged'));
								
						if (is_home() || is_archive()) {
							$base = trailingslashit(get_pagenum_link(1)) . '%_%';
							$current_page = max(1, get_query_var('paged'));
						}
						if (is_front_page()) {
							$base = trailingslashit(get_pagenum_link(1)) . '%_%';
							$current_page = max(1, get_query_var('page'));
						}
						if (is_author()) {
							$base = add_query_arg('page', '%#%');
							$current_page = max(1, get_query_var('page'));
						}
						if (is_single()) {
							if (is_single() && !is_page()) {
								$base = add_query_arg('page_num', '%#%');
								$current_page = max(1, get_query_var('page_num'));
							}
							if (is_page()) {
								$base = add_query_arg('page', '%#%');
								$current_page = max(1, get_query_var('page'));
							}
						}
						
						$nav_start = '<nav class="pagination" role="navigation" data-page="'. $current_page .'" data-max-page="'. ceil( $total_users / $settings['posts_per_page'] ) .'" aria-label="Pagination">';

						echo $nav_start;

						echo paginate_links([
							'base' => $base,
							'current' => $current_page,
							'total' => ceil( $total_users / $settings['posts_per_page'] ),
							'prev_text' => ('numbers_and_prev_next' === $settings['pagination']) ? __('« prev') : false,
							'next_text' => ('numbers_and_prev_next' === $settings['pagination']) ? __('next »') : false,
						]);

						echo '</nav>';
					}

				}
				if ('load_more' === $settings['pagination'] || 'infinite' === $settings['pagination']) {
					$total_users = $user_query->get_total();
					
					if ($total_users > 1) {					
						// Set the base URL and current page number
						$base = '';
						$current_page = '';

						// Set the base URL and current page number
						$base = '';
						$current_page = '';

						$base = add_query_arg('paged', '%#%');
						$current_page = max(1, get_query_var('paged'));
								
						if (is_home() || is_archive()) {
							$base = trailingslashit(get_pagenum_link(1)) . '%_%';
							$current_page = max(1, get_query_var('paged'));
						}
						if (is_front_page()) {
							$base = trailingslashit(get_pagenum_link(1)) . '%_%';
							$current_page = max(1, get_query_var('page'));
						}
						if (is_author()) {
							$base = add_query_arg('page', '%#%');
							$current_page = max(1, get_query_var('page'));
						}
						if (is_single()) {
							if (is_single() && !is_page()) {
								$base = add_query_arg('page_num', '%#%');
								$current_page = max(1, get_query_var('page_num'));
							}
							if (is_page()) {
								$base = add_query_arg('page', '%#%');
								$current_page = max(1, get_query_var('page'));
							}
						}
						
						$nav_start = '<nav style="display: none;" class="pagination" data-page="'. $current_page .'" data-max-page="'. ceil( $total_users / $settings['posts_per_page'] ) .'" role="navigation" aria-label="Pagination">';
						
						echo $nav_start;

						echo paginate_links([
							'base' => $base,
							'current' => $current_page,
							'total' => ceil( $total_users / $settings['posts_per_page'] ),
							'prev_text' => __('« prev'),
							'next_text' => __('next »'),
						]);

						echo '</nav>';	
						
						echo '
						<div class="elementor-button-wrapper load-more-wrapper">
							<a href="#" class="elementor-button load-more">Load More</a>
						</div>
						';
					}

				}
				echo '
				</div>
				</div>
				';
				if ('infinite' === $settings['pagination']) {
					echo '
						<div class="e-load-more-anchor"></div>
					';
				}
			} else {
				echo '
				<div class="post-container '. $skin .' '. $pinned_post .'">
					<div class="post-container-inner">
						<div class="no-post">'. $settings['nothing_found_message'] .'</div>
					</div>
				</div>
				';
            }
			}
    } //end of render function
}
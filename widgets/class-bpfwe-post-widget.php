<?php
/**
 * Post Widget.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

use Elementor\Repeater;
use BPFWE\Inc\Classes\BPFWE_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class BPFWE_Post_Widget
 *
 * This class is responsible for rendering the BPFWE post widget, which displays a list of posts
 * based on specific criteria. It includes methods for widget form rendering, output generation,
 * and script dependencies.
 */
class BPFWE_Post_Widget extends \Elementor\Widget_Base {
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
		return esc_html__( 'Post Widget', 'better-post-filter-widgets-for-elementor' );
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
			return [ 'bpfwe-widget-style', 'swiper' ];
		}

		$layout = $this->get_settings_for_display( 'classic_layout' );

		if ( 'carousel' === $layout ) {
			return [ 'bpfwe-widget-style', 'swiper' ];
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
			return [ 'swiper', 'post-widget-script' ];
		}

		$layout = $this->get_settings_for_display( 'classic_layout' );

		if ( 'carousel' === $layout ) {
			return [ 'swiper', 'post-widget-script' ];
		}

		return [ 'post-widget-script' ];
	}

	/**
	 * Register Post Widget widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__( 'Layout', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'type'               => \Elementor\Controls_Manager::NUMBER,
				'label'              => esc_html__( 'Posts Per Page', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 3,
				'min'                => -1,
				'max'                => 100,
				'step'               => 1,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'classic_layout',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Layout', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'grid',
				'options'            => [
					'grid'     => esc_html__( 'Grid', 'better-post-filter-widgets-for-elementor' ),
					'masonry'  => esc_html__( 'Masonry', 'better-post-filter-widgets-for-elementor' ),
					'carousel' => esc_html__( 'Carousel', 'better-post-filter-widgets-for-elementor' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'carousel_breakpoints',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Trigger Breakpoint', 'better-post-filter-widgets-for-elementor' ),
				'options'            => BPFWE_Helper::get_elementor_breakpoints(),
				'default'            => '',
				'frontend_available' => true,
				'condition'          => [
					'classic_layout' => 'carousel',
				],
			]
		);

		$this->add_control(
			'post_skin',
			[
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Post Skin', 'better-post-filter-widgets-for-elementor' ),
				'default' => 'classic',
				'options' => [
					'classic'     => esc_html__( 'Classic', 'better-post-filter-widgets-for-elementor' ),
					'side'        => esc_html__( 'On Side', 'better-post-filter-widgets-for-elementor' ),
					'banner'      => esc_html__( 'Banner', 'better-post-filter-widgets-for-elementor' ),
					'template'    => esc_html__( 'Template Grid', 'better-post-filter-widgets-for-elementor' ),
					'custom_html' => esc_html__( 'Custom HTML', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'keep_sideways',
			[
				'label'        => esc_html__( 'Keep sideways on mobile', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'keep-sideways',
				'prefix_class' => '',
				'default'      => '',
				'condition'    => [
					'post_skin' => 'side',
				],
			]
		);

		$this->add_responsive_control(
			'nb_columns',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Columns', 'better-post-filter-widgets-for-elementor' ),
				'options'            => [
					'1' => esc_html__( '1', 'better-post-filter-widgets-for-elementor' ),
					'2' => esc_html__( '2', 'better-post-filter-widgets-for-elementor' ),
					'3' => esc_html__( '3', 'better-post-filter-widgets-for-elementor' ),
					'4' => esc_html__( '4', 'better-post-filter-widgets-for-elementor' ),
					'5' => esc_html__( '5', 'better-post-filter-widgets-for-elementor' ),
					'6' => esc_html__( '6', 'better-post-filter-widgets-for-elementor' ),
					'7' => esc_html__( '7', 'better-post-filter-widgets-for-elementor' ),
					'8' => esc_html__( '8', 'better-post-filter-widgets-for-elementor' ),
				],
				'default'            => '3',
				'tablet_default'     => '3',
				'mobile_default'     => '1',
				'selectors'          => [
					'{{WRAPPER}} .elementor-grid' =>
						'grid-template-columns: repeat({{VALUE}},1fr)',
				],
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$this->add_control(
			'post_html_tag',
			[
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Post HTML Tag', 'better-post-filter-widgets-for-elementor' ),
				'default'   => 'article',
				'options'   => [
					'div'     => esc_html__( 'div', 'better-post-filter-widgets-for-elementor' ),
					'article' => esc_html__( 'article', 'better-post-filter-widgets-for-elementor' ),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'html_tag',
			[
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Title HTML Tag', 'better-post-filter-widgets-for-elementor' ),
				'default'   => 'h3',
				'options'   => [
					'h1'   => esc_html__( 'h1', 'better-post-filter-widgets-for-elementor' ),
					'h2'   => esc_html__( 'h2', 'better-post-filter-widgets-for-elementor' ),
					'h3'   => esc_html__( 'h3', 'better-post-filter-widgets-for-elementor' ),
					'h4'   => esc_html__( 'h4', 'better-post-filter-widgets-for-elementor' ),
					'h5'   => esc_html__( 'h5', 'better-post-filter-widgets-for-elementor' ),
					'h6'   => esc_html__( 'h6', 'better-post-filter-widgets-for-elementor' ),
					'div'  => esc_html__( 'div', 'better-post-filter-widgets-for-elementor' ),
					'span' => esc_html__( 'span', 'better-post-filter-widgets-for-elementor' ),
					'p'    => esc_html__( 'p', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'post_skin!' => [ 'template' ],
				],
			]
		);

		$skin_template_options_json = wp_json_encode( BPFWE_Helper::get_elementor_templates() );

		$this->add_control(
			'skin_template',
			[
				'label'       => esc_html__( 'Default Template', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => BPFWE_Helper::get_elementor_templates(),
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => [
					'post_skin' => 'template',
				],
			]
		);

		$template_repeater = new Repeater();

		$template_repeater->add_control(
			'extra_template_id',
			[
				'label'         => esc_html__( 'Choose a Template', 'better-post-filter-widgets-for-elementor' ),
				'type'          => \Elementor\Controls_Manager::SELECT,
				'options'       => BPFWE_Helper::get_elementor_templates(),
				'prevent_empty' => true,
				'label_block'   => true,
			]
		);

		$template_repeater->add_control(
			'grid_position',
			[
				'label'   => esc_html__( 'Position', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'step'    => 1,
				'default' => 1,
			]
		);

		$template_repeater->add_control(
			'apply_once',
			[
				'label'        => esc_html__( 'Apply Once', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
			]
		);

		$template_repeater->add_control(
			'column_span',
			[
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Column Span', 'better-post-filter-widgets-for-elementor' ),
				'separator' => 'before',
				'default'   => '1',
				'options'   => [
					'1' => esc_html__( '1', 'better-post-filter-widgets-for-elementor' ),
					'2' => esc_html__( '2', 'better-post-filter-widgets-for-elementor' ),
					'3' => esc_html__( '3', 'better-post-filter-widgets-for-elementor' ),
					'4' => esc_html__( '4', 'better-post-filter-widgets-for-elementor' ),
					'5' => esc_html__( '5', 'better-post-filter-widgets-for-elementor' ),
					'6' => esc_html__( '6', 'better-post-filter-widgets-for-elementor' ),
					'7' => esc_html__( '7', 'better-post-filter-widgets-for-elementor' ),
					'8' => esc_html__( '8', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$template_repeater->add_control(
			'row_span',
			[
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Row Span', 'better-post-filter-widgets-for-elementor' ),
				'default' => '1',
				'options' => [
					'1' => esc_html__( '1', 'better-post-filter-widgets-for-elementor' ),
					'2' => esc_html__( '2', 'better-post-filter-widgets-for-elementor' ),
					'3' => esc_html__( '3', 'better-post-filter-widgets-for-elementor' ),
					'4' => esc_html__( '4', 'better-post-filter-widgets-for-elementor' ),
					'5' => esc_html__( '5', 'better-post-filter-widgets-for-elementor' ),
					'6' => esc_html__( '6', 'better-post-filter-widgets-for-elementor' ),
					'7' => esc_html__( '7', 'better-post-filter-widgets-for-elementor' ),
					'8' => esc_html__( '8', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'create_grid',
			[
				'label'        => esc_html__( 'Add Extra Templates', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_skin'      => 'template',
					'skin_template!' => '',
				],
			]
		);

		$this->add_control(
			'extra_skin_list',
			[
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $template_repeater->get_controls(),
				'prevent_empty' => true,
				'default'       => [
					[
						'row_span' => esc_html__( '1', 'better-post-filter-widgets-for-elementor' ),
					],
				],
				'title_field'   => "<# let skin_labels = $skin_template_options_json; let skin_label = skin_labels[extra_template_id]; #>{{{ skin_label }}}",
				'condition'     => [
					'post_skin'   => 'template',
					'create_grid' => 'yes',
				],
			]
		);

		$this->add_control(
			'skin_custom_html',
			[
				'label'     => esc_html__( 'Custom HTML', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'default'   => '<div class="post-image">
<a href="#PERMALINK#">#IMAGE#</a>
</div>
<div class="inner-content">
<div class="post-title">
<a href="#PERMALINK#">#TITLE#</a>
</div>
<div class="post-excerpt">#EXCERPT#</div>
</div>',
				'options'   => BPFWE_Helper::get_elementor_templates(),
				'condition' => [
					'post_skin' => 'custom_html',
				],
			]
		);

		$this->add_control(
			'available_tags',
			[
				'label'     => esc_html__( 'Available Tags:', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::RAW_HTML,
				'raw'       => esc_html__( '#TITLE#, #CONTENT#, #EXCERPT#, #PERMALINK#, #IMAGE#', 'better-post-filter-widgets-for-elementor' ),
				'condition' => [
					'post_skin' => 'custom_html',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Post Content
		$repeater          = new Repeater();
		$user_repeater     = new Repeater();
		$taxonomy_repeater = new Repeater();

		$repeater->start_controls_tabs( 'field_repeater' );

		$repeater->start_controls_tab(
			'content',
			[
				'label' => esc_html__( 'Content', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$post_content_options = [
				'Title'          => esc_html__( 'Title', 'better-post-filter-widgets-for-elementor' ),
				'Content'        => esc_html__( 'Content', 'better-post-filter-widgets-for-elementor' ),
				'Excerpt'        => esc_html__( 'Excerpt', 'better-post-filter-widgets-for-elementor' ),
				'Custom Field'   => esc_html__( 'Custom Field/ACF', 'better-post-filter-widgets-for-elementor' ),
				'Taxonomy'       => esc_html__( 'Taxonomy', 'better-post-filter-widgets-for-elementor' ),
				'HTML'           => esc_html__( 'HTML/Shortcode', 'better-post-filter-widgets-for-elementor' ),
				'Post Meta'      => esc_html__( 'Post Meta', 'better-post-filter-widgets-for-elementor' ),
				'Read More'      => esc_html__( 'Read More', 'better-post-filter-widgets-for-elementor' ),
				'Pin Post'       => esc_html__( 'Bookmark', 'better-post-filter-widgets-for-elementor' ),
				'Edit Options'   => esc_html__( 'Edit Options', 'better-post-filter-widgets-for-elementor' ),
				'Product Price'  => esc_html__( 'Product Price', 'better-post-filter-widgets-for-elementor' ),
				'Product Rating' => esc_html__( 'Product Rating', 'better-post-filter-widgets-for-elementor' ),
				'Buy Now'        => esc_html__( 'Buy Now', 'better-post-filter-widgets-for-elementor' ),
				'Product Badge'  => esc_html__( 'Product Badge', 'better-post-filter-widgets-for-elementor' ),
			];
		} else {
			$post_content_options = [
				'Title'        => esc_html__( 'Title', 'better-post-filter-widgets-for-elementor' ),
				'Content'      => esc_html__( 'Content', 'better-post-filter-widgets-for-elementor' ),
				'Excerpt'      => esc_html__( 'Excerpt', 'better-post-filter-widgets-for-elementor' ),
				'Custom Field' => esc_html__( 'Custom Field/ACF', 'better-post-filter-widgets-for-elementor' ),
				'Taxonomy'     => esc_html__( 'Taxonomy', 'better-post-filter-widgets-for-elementor' ),
				'HTML'         => esc_html__( 'HTML/Shortcode', 'better-post-filter-widgets-for-elementor' ),
				'Post Meta'    => esc_html__( 'Post Meta', 'better-post-filter-widgets-for-elementor' ),
				'Read More'    => esc_html__( 'Read More', 'better-post-filter-widgets-for-elementor' ),
				'Pin Post'     => esc_html__( 'Bookmark', 'better-post-filter-widgets-for-elementor' ),
				'Edit Options' => esc_html__( 'Edit Options', 'better-post-filter-widgets-for-elementor' ),
			];
		}

		$user_repeater->add_control(
			'post_content',
			[
				'label'   => esc_html__( 'User Details', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'Display Name',
				'options' => [
					'Username'      => esc_html__( 'Username', 'better-post-filter-widgets-for-elementor' ),
					'Display Name'  => esc_html__( 'Display Name', 'better-post-filter-widgets-for-elementor' ),
					'Full Name'     => esc_html__( 'First/Last Name', 'better-post-filter-widgets-for-elementor' ),
					'User Meta'     => esc_html__( 'Custom Field/ACF', 'better-post-filter-widgets-for-elementor' ),
					'User Email'    => esc_html__( 'User Email', 'better-post-filter-widgets-for-elementor' ),
					'User Role'     => esc_html__( 'User Role', 'better-post-filter-widgets-for-elementor' ),
					'User ID'       => esc_html__( 'User ID', 'better-post-filter-widgets-for-elementor' ),
					'Visit Profile' => esc_html__( 'Visit Profile', 'better-post-filter-widgets-for-elementor' ),
					'HTML'          => esc_html__( 'HTML/Shortcode', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$taxonomy_repeater->add_control(
			'post_content',
			[
				'label'   => esc_html__( 'Term Details', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'Term Label',
				'options' => [
					'Term Label'       => esc_html__( 'Term Label', 'better-post-filter-widgets-for-elementor' ),
					'Term Description' => esc_html__( 'Term Description', 'better-post-filter-widgets-for-elementor' ),
					'Term Count'       => esc_html__( 'Term Count', 'better-post-filter-widgets-for-elementor' ),
					'Term Meta'        => esc_html__( 'Custom Field/ACF', 'better-post-filter-widgets-for-elementor' ),
					'Term ID'          => esc_html__( 'Term ID', 'better-post-filter-widgets-for-elementor' ),
					'Term URL'         => esc_html__( 'Term URL', 'better-post-filter-widgets-for-elementor' ),
					'HTML'             => esc_html__( 'HTML/Shortcode', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$taxonomy_repeater->add_control(
			'count_singular',
			[
				'label'       => esc_html__( 'Count Text (Singular)', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'input_type'  => 'text',
				'placeholder' => __( 'Post', 'cwm-widget' ),
				'default'     => 'Post',
				'condition'   => [
					'post_content' => 'Term Count',
				],
			]
		);

		$taxonomy_repeater->add_control(
			'count_plurial',
			[
				'label'       => esc_html__( 'Count Text (Plurial)', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'input_type'  => 'text',
				'placeholder' => __( 'Posts', 'cwm-widget' ),
				'default'     => 'Posts',
				'condition'   => [
					'post_content' => 'Term Count',
				],
			]
		);

		$post_content_options_json = wp_json_encode( $post_content_options );

		$repeater->add_control(
			'post_content',
			[
				'label'   => esc_html__( 'Post Content', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'Title',
				'options' => $post_content_options,
			]
		);

		$repeater->add_control(
			'post_title_url',
			[
				'label'        => esc_html__( 'Link to Post', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Title',
				],
			]
		);

		$user_repeater->add_control(
			'display_name_url',
			[
				'label'        => esc_html__( 'Link to User Profile', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => [ 'Display Name', 'Full Name', 'Username' ],
				],
			]
		);

		$taxonomy_repeater->add_control(
			'term_url',
			[
				'label'        => esc_html__( 'Link to Post', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => [ 'Term Label', 'Term URL' ],
				],
			]
		);

		$repeater->add_control(
			'post_meta_separator',
			[
				'label'     => esc_html__( 'Meta Separator', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => ' | ',
				'condition' => [
					'post_content' => 'Post Meta',
				],
			]
		);

		$repeater->add_control(
			'display_meta_author',
			[
				'label'        => esc_html__( 'Display Post Author', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'separator'    => 'before',
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Post Meta',
				],
			]
		);

		$repeater->add_control(
			'post_author_url',
			[
				'label'        => esc_html__( 'Link to Author Page', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content'        => 'Post Meta',
					'display_meta_author' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'author_icon',
			[
				'label'     => esc_html__( 'Author Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content'        => 'Post Meta',
					'display_meta_author' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'display_meta_date',
			[
				'label'        => esc_html__( 'Display Post Date', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'separator'    => 'before',
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Post Meta',
				],
			]
		);

		$repeater->add_control(
			'display_date_format',
			[
				'label'       => esc_html__( 'Date Format', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Y/m/d', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Y/m/d', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content'      => 'Post Meta',
					'display_meta_date' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'date_icon',
			[
				'label'     => esc_html__( 'Date Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content'      => 'Post Meta',
					'display_meta_date' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'display_meta_comment',
			[
				'label'        => esc_html__( 'Display Post Comment', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'separator'    => 'before',
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Post Meta',
				],
			]
		);

		$repeater->add_control(
			'comment_icon',
			[
				'label'     => esc_html__( 'Comment Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content'         => 'Post Meta',
					'display_meta_comment' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'display_on_sale',
			[
				'label'        => esc_html__( 'Display On Sale', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Product Badge',
				],
			]
		);

		$repeater->add_control(
			'on_sale_text',
			[
				'label'       => esc_html__( 'On Sale Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'On Sale', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'On Sale', 'better-post-filter-widgets-for-elementor' ),
				'separator'   => 'after',
				'condition'   => [
					'post_content'    => 'Product Badge',
					'display_on_sale' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'display_new_arrival',
			[
				'label'        => esc_html__( 'Display New Arrival', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Product Badge',
				],
			]
		);

		$repeater->add_control(
			'new_arrival_text',
			[
				'label'       => esc_html__( 'New Arrival Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'New Arrival', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'New Arrival', 'better-post-filter-widgets-for-elementor' ),
				'separator'   => 'after',
				'condition'   => [
					'post_content'        => 'Product Badge',
					'display_new_arrival' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'display_best_seller',
			[
				'label'        => esc_html__( 'Display Best Seller', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Product Badge',
				],
			]
		);

		$repeater->add_control(
			'best_seller_text',
			[
				'label'       => esc_html__( 'Best Seller Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Best Seller', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Best Seller', 'better-post-filter-widgets-for-elementor' ),
				'separator'   => 'after',
				'condition'   => [
					'post_content'        => 'Product Badge',
					'display_best_seller' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'description_length',
			[
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label'       => esc_html__( 'Content Length', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( '25', 'better-post-filter-widgets-for-elementor' ),
				'min'         => 0,
				'max'         => 1000,
				'step'        => 1,
				'default'     => 12,
				'condition'   => [
					'post_content' => [ 'Content', 'Excerpt' ],
				],
			]
		);

		$repeater->add_control(
			'title_length',
			[
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label'       => esc_html__( 'Title Length', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( '25', 'better-post-filter-widgets-for-elementor' ),
				'min'         => 0,
				'max'         => 1000,
				'step'        => 1,
				'default'     => 6,
				'condition'   => [
					'post_content' => 'Title',
				],
			]
		);

		$repeater->add_control(
			'post_taxonomy',
			[
				'label'       => esc_html__( 'Select Taxonomies', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => BPFWE_Helper::get_taxonomies_options(),
				'condition'   => [
					'post_content' => 'Taxonomy',
				],
			]
		);

		$repeater->add_control(
			'post_taxonomy_nb',
			[
				'label'     => esc_html__( 'Max. Terms to Show', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 5,
				'min'       => 1,
				'max'       => 100,
				'step'      => 1,
				'condition' => [
					'post_content' => 'Taxonomy',
				],
			]
		);

		$repeater->add_control(
			'post_field_key',
			[
				'label'       => esc_html__( 'Field Key', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a custom field', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'post_content' => 'Custom Field',
				],
			]
		);

		$user_repeater->add_control(
			'user_field_key',
			[
				'label'       => esc_html__( 'Field Key', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a user meta key', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'post_content' => 'User Meta',
				],
			]
		);

		$taxonomy_repeater->add_control(
			'term_field_key',
			[
				'label'       => esc_html__( 'Field Key', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a term meta key', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'post_content' => 'Term Meta',
				],
			]
		);

		$repeater->add_control(
			'post_html',
			[
				'label'       => esc_html__( 'HTML', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic'     => [
					'active' => false,
				],
				'label_block' => true,
				'condition'   => [
					'post_content' => 'HTML',
				],
			]
		);

		$user_repeater->add_control(
			'user_html',
			[
				'label'       => esc_html__( 'HTML', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic'     => [
					'active' => false,
				],
				'label_block' => true,
				'condition'   => [
					'post_content' => 'HTML',
				],
			]
		);

		$taxonomy_repeater->add_control(
			'term_html',
			[
				'label'       => esc_html__( 'HTML', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic'     => [
					'active' => false,
				],
				'label_block' => true,
				'condition'   => [
					'post_content' => 'HTML',
				],
			]
		);

		$repeater->add_control(
			'post_read_more_text',
			[
				'label'       => esc_html__( 'Read More Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More »', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Read More »', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content' => 'Read More',
				],
			]
		);

		$user_repeater->add_control(
			'visit_profile_text',
			[
				'label'       => esc_html__( 'Visit Profile Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Visit Profile »', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Visit Profile »', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content' => 'Visit Profile',
				],
			]
		);

		$taxonomy_repeater->add_control(
			'term_read_more_text',
			[
				'label'       => esc_html__( 'Read More', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More »', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Read More »', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content' => 'Term URL',
				],
			]
		);

		$repeater->add_control(
			'product_buy_now_text',
			[
				'label'       => esc_html__( 'Buy Now Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Buy Now', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Buy Now', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content' => 'Buy Now',
				],
			]
		);

		$repeater->add_control(
			'post_pin_logged_out',
			[
				'label'        => esc_html__( 'Hide for Logged-Out Users', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Pin Post',
				],
			]
		);

		$repeater->add_control(
			'pin_icon',
			[
				'label'     => esc_html__( 'Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content' => 'Pin Post',
				],
			]
		);

		$repeater->add_control(
			'pin_text',
			[
				'label'       => esc_html__( 'Bookmarked Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Pin', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Pin', 'better-post-filter-widgets-for-elementor' ),
				'separator'   => 'after',
				'condition'   => [
					'post_content' => 'Pin Post',
				],
			]
		);

		$repeater->add_control(
			'unpin_icon',
			[
				'label'     => __( 'Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content' => 'Pin Post',
				],
			]
		);

		$repeater->add_control(
			'unpin_text',
			[
				'label'       => esc_html__( 'Unbookmarked Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Unpin', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Unpin', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content' => 'Pin Post',
				],
			]
		);

		$repeater->add_control(
			'display_republish_option',
			[
				'label'        => esc_html__( 'Display Republish', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Edit Options',
				],
			]
		);

		$repeater->add_control(
			'republish_icon',
			[
				'label'     => esc_html__( 'Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content'             => 'Edit Options',
					'display_republish_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'republish_option_text',
			[
				'label'       => esc_html__( 'Republish Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Republish', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Republish', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content'             => 'Edit Options',
					'display_republish_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'display_unpublish_option',
			[
				'label'        => esc_html__( 'Display Unpublish', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'separator'    => 'before',
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Edit Options',
				],
			]
		);

		$repeater->add_control(
			'unpublish_icon',
			[
				'label'     => esc_html__( 'Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content'             => 'Edit Options',
					'display_unpublish_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'unpublish_option_text',
			[
				'label'       => esc_html__( 'Unpublish Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Unpublish', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Unpublish', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content'             => 'Edit Options',
					'display_unpublish_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'display_edit_option',
			[
				'label'        => esc_html__( 'Display Edit', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'separator'    => 'before',
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Edit Options',
				],
			]
		);

		$repeater->add_control(
			'edit_url',
			[
				'label'       => esc_html__( 'Edit URL', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Use #ID# to get the post ID', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( '/your-edit-page?#ID#', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'post_content'        => 'Edit Options',
					'display_edit_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'edit_icon',
			[
				'label'     => esc_html__( 'Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content'        => 'Edit Options',
					'display_edit_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'edit_option_text',
			[
				'label'       => esc_html__( 'Edit Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Edit', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Edit', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content'        => 'Edit Options',
					'display_edit_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'display_delete_option',
			[
				'label'        => esc_html__( 'Display Delete', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'separator'    => 'before',
				'return_value' => 'yes',
				'condition'    => [
					'post_content' => 'Edit Options',
				],
			]
		);

		$repeater->add_control(
			'delete_icon',
			[
				'label'     => esc_html__( 'Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'post_content'          => 'Edit Options',
					'display_delete_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'delete_option_text',
			[
				'label'       => esc_html__( 'Delete Text', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Delete', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => esc_html__( 'Delete', 'better-post-filter-widgets-for-elementor' ),
				'condition'   => [
					'post_content'          => 'Edit Options',
					'display_delete_option' => 'yes',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'field_style',
			[
				'label' => esc_html__( 'Style', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$repeater->add_control(
			'custom_style',
			[
				'label'       => esc_html__( 'Custom', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Set custom style that will only affect this specific row.', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$repeater->add_control(
			'field_size',
			[
				'label'      => esc_html__( 'Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}, {{WRAPPER}} {{CURRENT_ITEM}} a' => 'font-size: {{SIZE}}{{UNIT}} !important',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_weight',
			[
				'label'      => esc_html__( 'Weight', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SELECT,
				'default'    => '',
				'options'    => [
					'100'    => esc_html__( '100 (Thin)', 'better-post-filter-widgets-for-elementor' ),
					'200'    => esc_html__( '200 (Extra Light)', 'better-post-filter-widgets-for-elementor' ),
					'300'    => esc_html__( '300 (Light)', 'better-post-filter-widgets-for-elementor' ),
					'400'    => esc_html__( '400 (Normal)', 'better-post-filter-widgets-for-elementor' ),
					'500'    => esc_html__( '500 (Medium)', 'better-post-filter-widgets-for-elementor' ),
					'600'    => esc_html__( '600 (Semi Bold)', 'better-post-filter-widgets-for-elementor' ),
					'700'    => esc_html__( '700 (Bold)', 'better-post-filter-widgets-for-elementor' ),
					'800'    => esc_html__( '800 (Extra Bold)', 'better-post-filter-widgets-for-elementor' ),
					'900'    => esc_html__( '900 (Black)', 'better-post-filter-widgets-for-elementor' ),
					''       => esc_html__( 'Default', 'better-post-filter-widgets-for-elementor' ),
					'normal' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
					'bold'   => esc_html__( 'Bold', 'better-post-filter-widgets-for-elementor' ),
				],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'font-weight: {{VALUE}} !important',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_color',
			[
				'type'       => \Elementor\Controls_Manager::COLOR,
				'label'      => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}, {{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_color_hover',
			[
				'type'       => \Elementor\Controls_Manager::COLOR,
				'label'      => esc_html__( 'Hover Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper:hover {{CURRENT_ITEM}}, {{WRAPPER}} .post-wrapper:hover {{CURRENT_ITEM}} a' =>
						'color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_background_color',
			[
				'type'       => \Elementor\Controls_Manager::COLOR,
				'label'      => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				],
				'separator'  => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_background_color_hover',
			[
				'type'       => \Elementor\Controls_Manager::COLOR,
				'label'      => esc_html__( 'Hover Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper:hover {{CURRENT_ITEM}}' =>
						'background-color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'       => 'field_border',
				'label'      => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'selector'   => '{{WRAPPER}} {{CURRENT_ITEM}}',
				'separator'  => 'before',
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' =>
						'border-radius: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'horizontal_position',
			[
				'label'                => esc_html__( 'Horizontal Position', 'better-post-filter-widgets-for-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'margin-right: auto !important',
					'center' => 'margin: 0 auto !important',
					'right'  => 'margin-left: auto !important',
				],
				'separator'            => 'before',
				'conditions'           => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'vertical_position',
			[
				'label'                => esc_html__( 'Vertical Position', 'better-post-filter-widgets-for-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'    => 'flex-start !important',
					'middle' => 'center !important',
					'bottom' => 'flex-end !important',
				],
				'conditions'           => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'content_size',
			[
				'label'                => esc_html__( 'Size', 'better-post-filter-widgets-for-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'none'   => [
						'title' => esc_html__( 'None', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-ban',
					],
					'grow'   => [
						'title' => esc_html__( 'Grow', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-grow',
					],
					'shrink' => [
						'title' => esc_html__( 'Shrink', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-shrink',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'flex-grow: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'none'   => 'unset',
					'grow'   => '1',
					'shrink' => '0',
				],
				'conditions'           => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' =>
						'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
				'separator'  => 'before',
			]
		);

		$repeater->add_control(
			'field_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' =>
						'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_z_index',
			[
				'type'       => \Elementor\Controls_Manager::NUMBER,
				'label'      => esc_html__( 'Z-Index', 'better-post-filter-widgets-for-elementor' ),
				'min'        => 0,
				'max'        => 999,
				'step'       => 1,
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'z-index: {{STRING}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'custom_style',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'advanced',
			[
				'label' => esc_html__( 'Advanced', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$repeater->add_control(
			'pseudo_icon',
			[
				'label'     => esc_html__( 'Icon', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'display_republish_option' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'field_before',
			[
				'label'   => esc_html__( 'Before', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'field_after',
			[
				'label'       => esc_html__( 'After', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'description' => esc_html__( 'Use # to display the row number.', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$repeater->add_control(
			'pseudo_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 18,
				],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i'   => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'pseudo_icon_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Icon Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i, {{WRAPPER}} {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'pseudo_icon_spacing',
			[
				'label'      => esc_html__( 'Icon Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}} i'   => 'margin-right: {{SIZE}}{{UNIT}}; vertical-align: sub;',
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'margin-right: {{SIZE}}{{UNIT}}; vertical-align: sub;',
				],
			]
		);

		$repeater->add_control(
			'pseudo_typography',
			[
				'label'      => esc_html__( 'Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .pseudo' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .pseudo img' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .pseudo svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'pseudo_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .pseudo' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'pseudo_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover .pseudo' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'pseudo_padding',
			[
				'label'     => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .pseudo' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->start_controls_section(
			'content_section',
			[
				'label'     => esc_html__( 'Post Content', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'post_skin!' => [ 'template', 'template_list' ],
				],
			]
		);

		$this->add_control(
			'show_featured_image',
			[
				'label'        => esc_html__( 'Show Featured Image', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'      => 'featured_img',
				'include'   => [],
				'default'   => 'thumbnail',
				'condition' => [
					'show_featured_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'term_img_field_key',
			[
				'label'       => esc_html__( 'Term Image Key', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a meta key', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'show_featured_image' => 'yes',
					'query_type'          => 'taxonomy',
				],
			]
		);

		$this->add_control(
			'user_img_field_key',
			[
				'label'       => esc_html__( 'User Image Key', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => false,
				],
				'placeholder' => esc_html__( 'Enter a meta key', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'show_featured_image' => 'yes',
					'query_type'          => 'user',
				],
			]
		);

		$this->add_control(
			'post_image_url',
			[
				'label'        => esc_html__( 'Link to Post', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'show_featured_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'post_list',
			[
				'label'         => esc_html__( 'Post Content', 'better-post-filter-widgets-for-elementor' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'separator'     => 'before',
				'fields'        => $repeater->get_controls(),
				'default'       => [
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
				'title_field'   => "<# let labels = $post_content_options_json; let label = labels[post_content]; #>{{{ label }}}",
				'condition'     => [
					'query_type!' => [ 'user', 'taxonomy' ],
				],
			]
		);

		$this->add_control(
			'user_list',
			[
				'label'         => esc_html__( 'User Details', 'better-post-filter-widgets-for-elementor' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $user_repeater->get_controls(),
				'default'       => [
					[
						'post_content' => 'Display Name',
					],
					[
						'post_content' => 'User Email',
					],
				],
				'prevent_empty' => false,
				'title_field'   => '{{{ post_content }}}',
				'condition'     => [
					'query_type' => 'user',
				],
			]
		);

		$this->add_control(
			'taxonomy_list',
			[
				'label'         => esc_html__( 'Taxonomy Details', 'better-post-filter-widgets-for-elementor' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $taxonomy_repeater->get_controls(),
				'default'       => [
					[
						'post_content' => 'Term Label',
					],
					[
						'post_content' => 'Term Description',
					],
				],
				'prevent_empty' => false,
				'title_field'   => '{{{ post_content }}}',
				'condition'     => [
					'query_type' => 'taxonomy',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Query
		$this->start_controls_section(
			'query_section',
			[
				'label' => esc_html__( 'Query', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$post_type_args = [
			'public' => true,
		];

		$output     = 'names';
		$operator   = 'and';
		$post_types = get_post_types( $post_type_args, $output, $operator );

		$this->add_control(
			'query_type',
			[
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Query Type', 'better-post-filter-widgets-for-elementor' ),
				'default' => 'custom',
				'options' => [
					'custom'   => esc_html__( 'Post Query', 'better-post-filter-widgets-for-elementor' ),
					'main'     => esc_html__( 'Main Query', 'better-post-filter-widgets-for-elementor' ),
					'user'     => esc_html__( 'User Query', 'better-post-filter-widgets-for-elementor' ),
					'taxonomy' => esc_html__( 'Taxonomy Query', 'better-post-filter-widgets-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'post_type',
			[
				'label'       => esc_html__( 'Post Type', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'default'     => 'post',
				'multiple'    => true,
				'options'     => BPFWE_Helper::bpfwe_get_post_types(),
				'label_block' => true,
				'condition'   => [
					'query_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'select_taxonomy',
			[
				'label'       => esc_html__( 'Select a Taxonomy', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => BPFWE_Helper::get_taxonomies_options(),
				'default'     => array_key_first( BPFWE_Helper::get_taxonomies_options() ),
				'label_block' => true,
				'condition'   => [
					'query_type' => 'taxonomy',
				],
			]
		);

		$this->add_control(
			'add_empty_terms',
			[
				'label'     => esc_html__( 'Display Empty Terms?', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'query_type' => 'taxonomy',
				],
			]
		);

		$this->add_control(
			'filter_rule',
			[
				'label'     => esc_html__( 'Tax. Display Level', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'all'       => esc_html__( 'Show All', 'better-post-filter-widgets-for-elementor' ),
					'top_level' => esc_html__( 'Show Parent Only', 'better-post-filter-widgets-for-elementor' ),
					'child'     => esc_html__( 'Show Subcategory Only', 'better-post-filter-widgets-for-elementor' ),
				],
				'default'   => 'all',
				'condition' => [
					'query_type' => 'taxonomy',
				],
			]
		);

		$this->add_control(
			'post_status',
			[
				'label'              => esc_html__( 'Post Status', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SELECT2,
				'multiple'           => true,
				'default'            => 'publish',
				'options'            => [
					'publish' => esc_html__( 'Published', 'better-post-filter-widgets-for-elementor' ),
					'pending' => esc_html__( 'Pending', 'better-post-filter-widgets-for-elementor' ),
					'draft'   => esc_html__( 'Draft', 'better-post-filter-widgets-for-elementor' ),
					'private' => esc_html__( 'Private', 'better-post-filter-widgets-for-elementor' ),
					'trash'   => esc_html__( 'Trashed', 'better-post-filter-widgets-for-elementor' ),
				],
				'label_block'        => true,
				'frontend_available' => true,
				'condition'          => [
					'query_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Order By', 'better-post-filter-widgets-for-elementor' ),
				'default'   => 'date',
				'options'   => [
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
				'condition' => [
					'query_type' => [ 'custom', 'user', 'taxonomy' ],
				],
			]
		);

		$this->add_control(
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
					'orderby' => [ 'meta_value', 'meta_value_num' ],
				],
			]
		);

		$this->add_control(
			'order',
			[
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Order', 'better-post-filter-widgets-for-elementor' ),
				'default'   => 'DESC',
				'options'   => [
					'DESC' => esc_html__( 'Descending', 'better-post-filter-widgets-for-elementor' ),
					'ASC'  => esc_html__( 'Ascending', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition' => [
					'query_type' => [ 'custom', 'user', 'taxonomy' ],
				],
			]
		);

		$this->add_control(
			'post_offset',
			[
				'label'   => esc_html__( 'Offset', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'default' => '0',
			]
		);

		$taxonomies     = [];
		$all_terms      = [];
		$all_post_lists = [];

		if ( $post_types ) {
			foreach ( $post_types as $post_type ) {
				$taxonomies_transient_key = 'bpfwe_taxonomies_' . $post_type;
				$taxonomies[ $post_type ] = get_transient( $taxonomies_transient_key );

				if ( false === $taxonomies[ $post_type ] ) {
					$taxonomies[ $post_type ] = get_object_taxonomies( $post_type, 'objects' );
					set_transient( $taxonomies_transient_key, $taxonomies[ $post_type ], HOUR_IN_SECONDS );
				}

				$post_list_transient_key      = 'bpfwe_post_list_' . $post_type;
				$all_post_lists[ $post_type ] = get_transient( $post_list_transient_key );

				if ( false === $all_post_lists[ $post_type ] ) {
					$all_post_lists[ $post_type ] = BPFWE_Helper::bpfwe_get_post_list( $post_type );
					set_transient( $post_list_transient_key, $all_post_lists[ $post_type ], HOUR_IN_SECONDS );
				}
			}

			foreach ( $post_types as $post_type ) {
				if ( $taxonomies[ $post_type ] ) {
					foreach ( $taxonomies[ $post_type ] as $index => $tax ) {
						$tax_control_key = $index . '_' . $post_type;

						if ( 'post' === $post_type ) {
							if ( 'post_tag' === $index ) {
								$tax_control_key = 'tags';
							} elseif ( 'category' === $index ) {
								$tax_control_key = 'categories';
							}
						}

						$terms_transient_key = 'bpfwe_terms_' . $index;
						if ( ! isset( $all_terms[ $index ] ) ) {
							$all_terms[ $index ] = get_transient( $terms_transient_key );

							if ( false === $all_terms[ $index ] ) {
								$all_terms[ $index ] = get_terms(
									[
										'taxonomy'   => $index,
										'hide_empty' => false,
									]
								);
								set_transient( $terms_transient_key, $all_terms[ $index ], HOUR_IN_SECONDS );
							}
						}

						$terms = $all_terms[ $index ];

						if ( $terms ) {
							$items_cat_id = [];

							foreach ( $terms as $term ) {
								$items_cat_id[ $term->term_id ][0] = $term->name;
							}

							$this->add_control(
								$index . '_' . $post_type . '_filter_type',
								[
									'label'       => sprintf(
										// translators: %s is the taxonomy label.
										__( '%s Filter Type', 'better-post-filter-widgets-for-elementor' ),
										$tax->label
									),
									'type'        => \Elementor\Controls_Manager::SELECT,
									'default'     => 'IN',
									'label_block' => true,
									'options'     => [
										'IN'     => sprintf(
											// translators: %s is the taxonomy label.
											__( 'Include %s', 'better-post-filter-widgets-for-elementor' ),
											esc_html( $tax->label )
										),
										'NOT IN' => sprintf(
											// translators: %s is the taxonomy label.
											__( 'Exclude %s', 'better-post-filter-widgets-for-elementor' ),
											esc_html( $tax->label )
										),
									],
									'separator'   => 'before',
									'condition'   => [
										'query_type' => 'custom',
										'post_type'  => $post_type,
									],
								]
							);

							$this->add_control(
								$tax_control_key,
								[
									'label'       => $tax->label,
									'type'        => \Elementor\Controls_Manager::SELECT2,
									'options'     => $items_cat_id,
									'label_block' => true,
									'multiple'    => true,
									'condition'   => [
										'query_type' => 'custom',
										'post_type'  => $post_type,
									],
								]
							);

							$this->add_control(
								$index . '_filter_type',
								[
									'label'       => sprintf(
										// translators: %s is the taxonomy label.
										__( '%s Filter Type', 'better-post-filter-widgets-for-elementor' ),
										$tax->label
									),
									'type'        => \Elementor\Controls_Manager::SELECT,
									'default'     => 'IN',
									'label_block' => true,
									'options'     => [
										'IN'     => sprintf(
											// translators: %s is the taxonomy label.
											__( 'Include %s', 'better-post-filter-widgets-for-elementor' ),
											esc_html( $tax->label )
										),
										'NOT IN' => sprintf(
											// translators: %s is the taxonomy label.
											__( 'Exclude %s', 'better-post-filter-widgets-for-elementor' ),
											esc_html( $tax->label )
										),
									],
									'separator'   => 'before',
									'condition'   => [
										'query_type'      => 'taxonomy',
										'select_taxonomy' => $index,
									],
								]
							);

							$this->add_control(
								$index,
								[
									'label'       => $tax->label,
									'type'        => \Elementor\Controls_Manager::SELECT2,
									'options'     => $items_cat_id,
									'label_block' => true,
									'multiple'    => true,
									'condition'   => [
										'query_type'      => 'taxonomy',
										$index . '_filter_type!' => '',
										'select_taxonomy' => $index,
									],
								]
							);
						}
					}
				}

				$this->add_control(
					'post__in_' . $post_type,
					[
						'label'       => esc_html__( 'Include Posts', 'better-post-filter-widgets-for-elementor' ),
						'type'        => \Elementor\Controls_Manager::SELECT2,
						'label_block' => true,
						'multiple'    => true,
						'separator'   => 'before',
						'default'     => '',
						'options'     => $all_post_lists[ $post_type ],
						'condition'   => [
							'query_type' => 'custom',
							'post_type'  => $post_type,
						],
					]
				);

				$this->add_control(
					'post__not_in_' . $post_type,
					[
						'label'       => esc_html__( 'Exclude Posts', 'better-post-filter-widgets-for-elementor' ),
						'type'        => \Elementor\Controls_Manager::SELECT2,
						'label_block' => true,
						'multiple'    => true,
						'separator'   => 'before',
						'default'     => '',
						'options'     => $all_post_lists[ $post_type ],
						'condition'   => [
							'query_type' => 'custom',
							'post_type'  => $post_type,
						],
					]
				);
			}
		}

		$this->add_control(
			'user_meta_key',
			[
				'label'     => esc_html__( 'Include by User Meta', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'separator' => 'before',
				'options'   => BPFWE_Helper::get_all_user_meta_keys(),
				'condition' => [
					'query_type' => 'user',
				],
			]
		);

		$this->add_control(
			'user_meta_value',
			[
				'label'     => esc_html__( 'Meta Value', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'options'   => BPFWE_Helper::get_all_user_meta_keys(),
				'condition' => [
					'query_type'     => 'user',
					'user_meta_key!' => '',
				],
			]
		);

		$this->add_control(
			'selected_roles',
			[
				'label'       => esc_html__( 'Include by Role', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => BPFWE_Helper::get_all_user_roles(),
				'condition'   => [
					'query_type' => 'user',
				],
			]
		);

		$this->add_control(
			'excluded_roles',
			[
				'label'       => esc_html__( 'Exclude by Role', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'default'     => '',
				'options'     => BPFWE_Helper::get_all_user_roles(),
				'condition'   => [
					'query_type' => 'user',
				],
			]
		);

		$this->add_control(
			'sticky_posts',
			[
				'label'        => esc_html__( 'Sticky Posts', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'separator'    => 'before',
				'condition'    => [
					'query_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'related_posts',
			[
				'label'        => esc_html__( 'Related Posts', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'query_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'related_post_taxonomy',
			[
				'label'       => esc_html__( 'Select Related Taxonomy', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'category',
				'options'     => BPFWE_Helper::get_taxonomies_options(),
				'condition'   => [
					'related_posts' => 'yes',
				],
			]
		);

		$this->add_control(
			'pinned_posts',
			[
				'label'        => esc_html__( 'Pinned Posts', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'condition'    => [
					'query_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'query_id',
			[
				'label'       => esc_html__( 'Query ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Give your Query a custom unique ID to allow server side filtering.', 'better-post-filter-widgets-for-elementor' ),
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'nothing_found_message',
			[
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'label'     => esc_html__( 'Nothing Found Message', 'better-post-filter-widgets-for-elementor' ),
				'rows'      => 3,
				'default'   => esc_html__( 'It seems we can’t find what you’re looking for.', 'better-post-filter-widgets-for-elementor' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Carousel
		$this->start_controls_section(
			'carousel_section',
			[
				'label'     => esc_html__( 'Carousel Options', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'classic_layout' => 'carousel',
				],
			]
		);

		$this->add_responsive_control(
			'post_slider_slides_per_view',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Slides Per View', 'better-post-filter-widgets-for-elementor' ),
				'options'            => [
					'1'   => esc_html__( '1', 'better-post-filter-widgets-for-elementor' ),
					'1.5' => esc_html__( '1.5', 'better-post-filter-widgets-for-elementor' ),
					'2'   => esc_html__( '2', 'better-post-filter-widgets-for-elementor' ),
					'2.5' => esc_html__( '2.5', 'better-post-filter-widgets-for-elementor' ),
					'3'   => esc_html__( '3', 'better-post-filter-widgets-for-elementor' ),
					'3.5' => esc_html__( '3.5', 'better-post-filter-widgets-for-elementor' ),
					'4'   => esc_html__( '4', 'better-post-filter-widgets-for-elementor' ),
					'4.5' => esc_html__( '4.5', 'better-post-filter-widgets-for-elementor' ),
					'5'   => esc_html__( '5', 'better-post-filter-widgets-for-elementor' ),
					'5.5' => esc_html__( '5.5', 'better-post-filter-widgets-for-elementor' ),
					'6'   => esc_html__( '6', 'better-post-filter-widgets-for-elementor' ),
					'6.5' => esc_html__( '6.5', 'better-post-filter-widgets-for-elementor' ),
					'7'   => esc_html__( '7', 'better-post-filter-widgets-for-elementor' ),
					'7.5' => esc_html__( '7.5', 'better-post-filter-widgets-for-elementor' ),
					'8'   => esc_html__( '8', 'better-post-filter-widgets-for-elementor' ),
					'8.5' => esc_html__( '8.5', 'better-post-filter-widgets-for-elementor' ),
				],
				'default'            => '3',
				'tablet_default'     => '3',
				'mobile_default'     => '1',
				'frontend_available' => true,
				'condition'          => [
					'classic_layout' => 'carousel',
				],
			]
		);

		$this->add_responsive_control(
			'post_slider_slides_to_scroll',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Slides to Scroll', 'better-post-filter-widgets-for-elementor' ),
				'options'            => [
					'1' => esc_html__( '1', 'better-post-filter-widgets-for-elementor' ),
					'2' => esc_html__( '2', 'better-post-filter-widgets-for-elementor' ),
					'3' => esc_html__( '3', 'better-post-filter-widgets-for-elementor' ),
					'4' => esc_html__( '4', 'better-post-filter-widgets-for-elementor' ),
					'5' => esc_html__( '5', 'better-post-filter-widgets-for-elementor' ),
					'6' => esc_html__( '6', 'better-post-filter-widgets-for-elementor' ),
					'7' => esc_html__( '7', 'better-post-filter-widgets-for-elementor' ),
					'8' => esc_html__( '8', 'better-post-filter-widgets-for-elementor' ),
				],
				'default'            => '1',
				'tablet_default'     => '1',
				'mobile_default'     => '1',
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'post_slider_gap',
			[
				'type'               => \Elementor\Controls_Manager::NUMBER,
				'label'              => esc_html__( 'Slide Gap', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 20,
				'min'                => 0,
				'max'                => 60,
				'step'               => 1,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_auto_h',
			[
				'label'              => esc_html__( 'Adaptive Height', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'post_slider_h',
			[
				'label'              => esc_html__( 'Slider height', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SLIDER,
				'size_units'         => [ 'px', 'vh' ],
				'range'              => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 500,
				],
				'selectors'          => [
					'{{WRAPPER}}.bpfwe-swiper'    =>
						'height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .swiper-wrapper' =>
						'height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .swiper-wrapper .swiper-slide' =>
						'height: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'          => [
					'post_slider_auto_h!' => 'yes',
				],
				'frontend_available' => true,
				'hide_in_inner'      => true,
			]
		);

		$this->add_control(
			'heading_carousel_nav',
			[
				'label'     => __( 'Carousel Navigation', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'post_slider_arrows',
			[
				'label'              => esc_html__( 'Arrow', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'slide_to_clicked_slide',
			[
				'label'              => esc_html__( 'Slide to Clicked Slide', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_pagination',
			[
				'label'              => esc_html__( 'Pagination', 'cwm-widget' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'cwm-widget' ),
				'label_off'          => esc_html__( 'No', 'cwm-widget' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_pagination_type',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Pagination Type', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'bullets',
				'options'            => [
					'bullets'     => esc_html__( 'Bullets', 'better-post-filter-widgets-for-elementor' ),
					'fraction'    => esc_html__( 'Fraction', 'better-post-filter-widgets-for-elementor' ),
					'progressbar' => esc_html__( 'Progressbar', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition'          => [
					'post_slider_pagination' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'custom_pagination',
			[
				'label'        => esc_html__( 'Custom Navigation', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'custom_pagination_description',
			[
				'label'           => esc_html__( 'How to Use', 'better-post-filter-widgets-for-elementor' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Use WIDGETID-slide-N on any widget on your page to navigate to a specific slide, WIDGETID-slide-prev or WIDGETID-slide-next for previous/next.', 'better-post-filter-widgets-for-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'condition'       => [
					'custom_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_pagination_id',
			[
				'label'       => __( 'Pagination Class', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'ui',
				'description' => '<script>
					jQuery(document).ready(function($) {
						var $input = $(".elementor-control-custom_pagination_id input");
						var widgetID = elementor.getCurrentElement().model.id + "-slide-next";
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
					'custom_pagination' => 'yes',
				],
			]
		);

		$this->add_control(
			'heading_interaction',
			[
				'label'     => __( 'Autoplay & Interaction', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'post_slider_autoplay',
			[
				'label'              => esc_html__( 'Autoplay', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'enable_marquee',
			[
				'label'              => esc_html__( 'Marquee Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'condition'          => [
					'post_slider_autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_hover',
			[
				'label'              => esc_html__( 'Pause on Hover', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_allow_touch_move',
			[
				'label'              => esc_html__( 'Enable Touch Move', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_allow_mousewheel',
			[
				'label'              => esc_html__( 'Enable Mousewheel', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_transition_effect',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Transition Effect', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'slide',
				'options'            => [
					'slide' => esc_html__( 'Slide', 'better-post-filter-widgets-for-elementor' ),
					'fade'  => esc_html__( 'Fade', 'better-post-filter-widgets-for-elementor' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_speed',
			[
				'type'               => \Elementor\Controls_Manager::NUMBER,
				'label'              => esc_html__( 'Animation Speed', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 1200,
				'min'                => 1,
				'max'                => 10000,
				'step'               => 1,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_autoplay_delay',
			[
				'type'               => \Elementor\Controls_Manager::NUMBER,
				'label'              => esc_html__( 'Animation Delay', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 4000,
				'min'                => 1,
				'max'                => 10000,
				'step'               => 1,
				'condition'          => [
					'enable_marquee' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_loop',
			[
				'label'              => esc_html__( 'Infinite Loop', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'condition'          => [
					'enable_marquee' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'heading_advanced',
			[
				'label'     => __( 'Advanced Settings', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'post_slider_centered_slides',
			[
				'label'              => esc_html__( 'Center Mode', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_slides_round_lenghts',
			[
				'label'              => esc_html__( 'Centered Slide Bounds', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_lazy_load',
			[
				'label'              => esc_html__( 'Lazy Load', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_slider_parallax',
			[
				'label'              => esc_html__( 'Apply Parallax?', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'featured_img_bg',
			[
				'label'        => esc_html__( 'Use Featured Image as BG', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'hide_feature_image',
			[
				'label'        => esc_html__( 'Hide the Feature Image', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'featured_img_bg' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'bg_css_filters',
				'selector'  => '.bg-slide-{{ID}}::before, .bg-slide-{{ID}}::after',
				'condition' => [
					'featured_img_bg' => 'yes',
				],
			]
		);

		$this->add_control(
			'bg_class',
			[
				'label'       => __( 'Background Class', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'ui',
				'description' => '<script>
					jQuery(document).ready(function($) {
						var $input = $(".elementor-control-bg_class input");
						var widgetID = "bg-slide-" + elementor.getCurrentElement().model.id;
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
					'featured_img_bg' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_sync_class_description',
			[
				'label'           => esc_html__( 'Synchronize Carousel', 'better-post-filter-widgets-for-elementor' ),
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'To synchronize carousels, add the "sync-sliders" class to at least two post widgets on the same page.', 'better-post-filter-widgets-for-elementor' ),
				'content_classes' => 'elementor-control-field-description',
				'separator'       => 'before',
			]
		);

		$this->add_control(
			'custom_sync_class_id',
			[
				'label'       => __( 'Sync Class', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'ui',
				'description' => '<script>
					jQuery(document).ready(function($) {
						var $input = $(".elementor-control-custom_sync_class_id input");
						var syncClass = "sync-sliders";
						$input.val(syncClass).attr("readonly", true);

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
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Pagination
		$this->start_controls_section(
			'pagination_section',
			[
				'label' => esc_html__( 'Pagination', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pagination',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Pagination', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'none',
				'options'            => [
					'none'                  => esc_html__( 'None', 'better-post-filter-widgets-for-elementor' ),
					'numbers'               => esc_html__( 'Numbers', 'better-post-filter-widgets-for-elementor' ),
					'numbers_and_prev_next' => esc_html__( 'Numbers + Previous/Next', 'better-post-filter-widgets-for-elementor' ),
					'load_more'             => esc_html__( 'Load More Button', 'better-post-filter-widgets-for-elementor' ),
					'infinite'              => esc_html__( 'Infinite', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition'          => [
					'classic_layout!' => 'carousel',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pagination_carousel',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => esc_html__( 'Pagination', 'better-post-filter-widgets-for-elementor' ),
				'default'            => 'none',
				'options'            => [
					'none'                  => esc_html__( 'None', 'better-post-filter-widgets-for-elementor' ),
					'numbers'               => esc_html__( 'Numbers', 'better-post-filter-widgets-for-elementor' ),
					'numbers_and_prev_next' => esc_html__( 'Numbers + Previous/Next', 'better-post-filter-widgets-for-elementor' ),
				],
				'condition'          => [
					'classic_layout' => 'carousel',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'display_on_carousel',
			[
				'label'              => esc_html__( 'Display on Carousel', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value'       => 'display-on-carousel',
				'default'            => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'classic_layout'       => 'carousel',
					'pagination_carousel!' => 'none',
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
				'frontend_available' => true,
				'conditions'         => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'pagination',
							'operator' => 'in',
							'value'    => [ 'numbers', 'numbers_and_prev_next', 'load_more' ],
						],
						[
							'name'     => 'pagination_carousel',
							'operator' => 'in',
							'value'    => [ 'numbers', 'numbers_and_prev_next' ],
						],
					],
				],
			]
		);

		$this->add_control(
			'hide_infinite_load',
			[
				'label'        => esc_html__( 'Hide Spinner', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'pagination' => 'infinite',
				],
			]
		);

		$this->add_control(
			'scroll_threshold',
			[
				'label'              => esc_html__( 'Scroll Threshold', 'better-post-filter-widgets-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::SLIDER,
				'size_units'         => [ 'px', '%' ],
				'range'              => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
					'%'  => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default'            => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'          => [
					'pagination' => 'infinite',
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Additional Options
		$this->start_controls_section(
			'additional_options_section',
			[
				'label' => esc_html__( 'Additional Options', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'external_url_new_tab',
			[
				'label'        => esc_html__( 'Open Links in New Tab', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'post_skin!' => [ 'template' ],
				],
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_external_url',
			[
				'label'       => esc_html__( 'External URL', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Paste URL or use a custom field', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'condition'   => [
					'post_skin!' => [ 'template' ],
				],
				'description' => esc_html__( 'Use this option to replace all existing post URLs with a URL of your choice.', 'better-post-filter-widgets-for-elementor' ),
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'post_external_if_empty',
			[
				'label'        => esc_html__( 'Use Post URL as Fallback', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'post_external_url!' => '',
					'post_skin!'         => [ 'template' ],
				],
			]
		);

		$this->add_control(
			'include_post_id',
			[
				'label'       => esc_html__( 'Include Posts by ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Comma Separated List', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'exclude_post_id',
			[
				'label'       => esc_html__( 'Exclude Posts by ID', 'better-post-filter-widgets-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Comma Separated List', 'better-post-filter-widgets-for-elementor' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- SECTION: Style

		// ------------------------------------------------------------------------- CONTROL: Box Style
		$this->start_controls_section(
			'layout_style',
			[
				'label' => esc_html__( 'Layout', 'better-post-filter-widgets-for-elementor' ),
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

		$this->add_control(
			'banner_horizontal_position',
			[
				'label'                => esc_html__( 'Content Horizontal Position', 'better-post-filter-widgets-for-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} .inner-content' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'   => 'align-items: flex-start; text-align: left;',
					'center' => 'align-items: center; text-align: center;',
					'right'  => 'align-items: flex-end; text-align: right;',
				],
				'separator'            => 'before',
				'condition'            => [
					'post_skin' => 'banner',
				],
			]
		);

		$this->add_control(
			'banner_vertical_position',
			[
				'label'                => esc_html__( 'Content Vertical Position', 'better-post-filter-widgets-for-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__( 'Middle', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'            => [
					'{{WRAPPER}} .inner-content' => 'justify-content: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'condition'            => [
					'post_skin' => 'banner',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_layout' );

		$this->start_controls_tab(
			'style_layout_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'layout_color_normal',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-wrapper' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout_padding_normal',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout_margin_normal',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'border_layout_normal',
				'label'     => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .post-wrapper',
			]
		);

		$this->add_control(
			'layout_border_layout_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper' =>
						'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_layout_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'layout_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-wrapper:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'border_layout_hover',
				'label'     => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .post-wrapper:hover ',
			]
		);

		$this->add_control(
			'layout_border_layout_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper:hover' =>
						'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style',
			[
				'label' => esc_html__( 'Post Content', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_height',
			[
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Height', 'better-post-filter-widgets-for-elementor' ),
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_min_height',
			[
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Min. Height', 'better-post-filter-widgets-for-elementor' ),
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_max_height',
			[
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Max. Height', 'better-post-filter-widgets-for-elementor' ),
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper' => 'max-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'box_transition_duration',
			[
				'label'     => __( 'Transition Duration', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'default'   => [
					'size' => 0.3,
				],
				'range'     => [
					'px' => [
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .post-wrapper,{{WRAPPER}} .post-wrapper .overlay,{{WRAPPER}} .post-wrapper a,{{WRAPPER}} .post-title,{{WRAPPER}} .post-content,{{WRAPPER}} .post-taxonomy,{{WRAPPER}} .post-read-more' =>
						'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_box' );

		$this->start_controls_tab(
			'style_box_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'box_color_normal',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-wrapper' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding_normal',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper .inner-content' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_margin_normal',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper .inner-content' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'border_normal',
				'label'     => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .post-wrapper .inner-content',
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper' =>
						'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_box_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'box_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-wrapper:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper:hover .inner-content' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper:hover .inner-content' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'border_hover',
				'label'     => esc_html__( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .post-wrapper:hover  .inner-content',
			]
		);

		$this->add_control(
			'box_border_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post-wrapper:hover' =>
						'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Featured Image Style
		$this->start_controls_section(
			'img_style',
			[
				'label'      => esc_html__( 'Featured Image', 'better-post-filter-widgets-for-elementor' ),
				'tab'        => \Elementor\Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'show_featured_image',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'post_default_image',
			[
				'label'   => esc_html__( 'Fallback Image', 'better-post-filter-widgets-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'img_equal_height',
			[
				'label'        => esc_html__( 'Image Equal Height', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_responsive_control(
			'img_height',
			[
				'label'      => esc_html__( 'Height', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post-image, {{WRAPPER}} .post-image a' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .post-image img' => 'height: 100%;',
				],
				'condition'  => [
					'img_equal_height' => '',
				],
			]
		);

		$this->add_responsive_control(
			'img_width',
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
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .post-container.side .post-image' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .post-container.side .inner-content' => 'width: calc(100% - {{SIZE}}{{UNIT}})',
				],
				'condition'  => [
					'post_skin' => 'side',
				],
			]
		);

		$this->add_responsive_control(
			'img_horizontal_position',
			[
				'label'                => esc_html__( 'Image Position', 'better-post-filter-widgets-for-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Left', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'left',
				'condition'            => [
					'post_skin' => 'side',
				],
				'selectors'            => [
					'{{WRAPPER}} .post-wrapper' => 'flex-direction: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'  => 'row',
					'right' => 'row-reverse',
				],
			]
		);

		$this->add_responsive_control(
			'img_vertical_position',
			[
				'label'                => esc_html__( 'Image Position', 'better-post-filter-widgets-for-elementor' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'options'              => [
					'top'    => [
						'title' => esc_html__( 'Top', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'              => 'top',
				'condition'            => [
					'post_skin' => 'classic',
				],
				'selectors'            => [
					'{{WRAPPER}} .post-wrapper' => 'flex-direction: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top'    => 'column',
					'bottom' => 'column-reverse',
				],
			]
		);

		$this->add_responsive_control(
			'img-aspect-ratio',
			[
				'type'      => \Elementor\Controls_Manager::SELECT,
				'label'     => esc_html__( 'Aspect Ratio', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '3-2',
				'options'   => [
					'3-2'   => '3:2',
					'1-1'   => '1:1',
					'4-3'   => '4:3',
					'16-9'  => '16:9',
					'191-1' => '19.1:1',
				],
				'condition' => [
					'img_equal_height' => 'yes',
				],
			]
		);

		$this->add_control(
			'img_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .post-image img' =>
						'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'img_border',
				'selector' => '{{WRAPPER}} .post-image img',
			]
		);

		$this->add_control(
			'overlay',
			[
				'label'        => esc_html__( 'Image Overlay', 'better-post-filter-widgets-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'better-post-filter-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'better-post-filter-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'      => 'img_overlay_normal',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '{{WRAPPER}} .post-wrapper .overlay',
				'condition' => [
					'overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'img_overlay_hover',
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
					'size' => 0.5,
				],
				'selectors' => [
					'{{WRAPPER}} .post-wrapper:hover .overlay' =>
						'opacity: {{SIZE}}',
				],
				'condition' => [
					'overlay' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Title Style
		$this->start_controls_section(
			'title_style',
			[
				'label'     => esc_html__( 'Title', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => [ 'custom', 'main' ],
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' =>
					'{{WRAPPER}} .post-title, {{WRAPPER}} .post-title a',
			]
		);

		$this->add_responsive_control(
			'title_align',
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
				'selectors' => [
					'{{WRAPPER}} .post-title' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_title' );

		$this->start_controls_tab(
			'title_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-title a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .post-title'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-title' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-title' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-title' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-title:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .post-title:hover'   =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-title:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-title:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-title:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Content/Excerpt Style
		$this->start_controls_section(
			'content_style',
			[
				'label'     => esc_html__( 'Content/Excerpt', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => [ 'custom', 'main' ],
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' =>
					'{{WRAPPER}} .post-content, {{WRAPPER}} .post-content a, {{WRAPPER}} .post-excerpt, {{WRAPPER}} .post-excerpt a',
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'label'     => esc_html__( 'Alignment', 'better-post-filter-widgets-for-elementor' ),
				'options'   => [
					'left'    => [
						'title' => esc_html__( 'Left', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_content' );

		$this->start_controls_tab(
			'content_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'content_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-content a, {{WRAPPER}} .post-excerpt a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-content, {{WRAPPER}} .post-excerpt' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'content_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'content_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-content:hover a, {{WRAPPER}} .post-excerpt:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .post-content:hover, {{WRAPPER}} .post-excerpt:hover' =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-content:hover, {{WRAPPER}} .post-excerpt:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-content:hover, {{WRAPPER}} .post-excerpt:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-content:hover, {{WRAPPER}} .post-excerpt:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: HTML Style
		$this->start_controls_section(
			'html_style',
			[
				'label' => esc_html__( 'HTML/Shortcode', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'html_typography',
				'selector' =>
					'{{WRAPPER}} .post-html, {{WRAPPER}} .post-html a',
			]
		);

		$this->add_responsive_control(
			'html_align',
			[
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'label'     => esc_html__( 'Alignment', 'better-post-filter-widgets-for-elementor' ),
				'options'   => [
					'left'    => [
						'title' => esc_html__( 'Left', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .post-html' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_html' );

		$this->start_controls_tab(
			'html_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'html_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-html a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .post-html'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'html_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-html' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'html_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-html' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'html_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-html' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'html_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'html_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-html:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .post-html:hover'   =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'html_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-html:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'html_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-html:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'html_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-html:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Taxonomy Style
		$this->start_controls_section(
			'taxonomy_style',
			[
				'label'     => esc_html__( 'Taxonomy', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => [ 'custom', 'main' ],
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'taxonomy_typography',
				'selector' =>
					'{{WRAPPER}} ul.post-taxonomy li, {{WRAPPER}} ul.post-taxonomy li a',
			]
		);

		$this->add_responsive_control(
			'taxonomy_align',
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
				'selectors' => [
					'{{WRAPPER}} ul.post-taxonomy' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_taxonomy' );

		$this->start_controls_tab(
			'taxonomy_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'taxonomy_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} ul.post-taxonomy li a' => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.post-taxonomy li'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'taxonomy_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} ul.post-taxonomy li' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} ul.post-taxonomy li' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} ul.post-taxonomy li' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'taxonomy_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'taxonomy_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} ul.post-taxonomy li:hover a' => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.post-taxonomy li:hover'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'taxonomy_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} ul.post-taxonomy li:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} ul.post-taxonomy li:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'taxonomy_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} ul.post-taxonomy li:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Custom Field Style
		$this->start_controls_section(
			'custom_field_style',
			[
				'label' => esc_html__( 'Custom Field/ACF', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'custom_field_typography',
				'selector' =>
					'{{WRAPPER}} .post-custom-field, {{WRAPPER}} .post-custom-field a',
			]
		);

		$this->add_responsive_control(
			'custom_field_align',
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
				'selectors' => [
					'{{WRAPPER}} .post-custom-field' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_custom_field' );

		$this->start_controls_tab(
			'custom_field_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'custom_field_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-custom-field a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .post-custom-field'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'custom_field_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-custom-field' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'custom_field_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-custom-field' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'custom_field_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-custom-field' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'custom_field_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'custom_field_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-custom-field:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .post-custom-field:hover' =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'custom_field_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-custom-field:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'custom_field_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-custom-field:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'custom_field_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-custom-field:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Post Meta Style
		$this->start_controls_section(
			'post_meta_style',
			[
				'label'     => esc_html__( 'Post Meta', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => [ 'custom', 'main' ],
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'post_meta_typography',
				'selector' =>
					'{{WRAPPER}} .post-meta, {{WRAPPER}} .post-meta a',
			]
		);

		$this->add_responsive_control(
			'post_meta_align',
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
				'selectors' => [
					'{{WRAPPER}} .post-meta' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_post_meta' );

		$this->start_controls_tab(
			'post_meta_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'post_meta_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-meta a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .post-meta'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'post_meta_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-meta' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'post_meta_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-meta' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_meta_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .post-meta' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'post_meta_icon_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Icon Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .post-meta i, {{WRAPPER}} .post-meta svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'post_meta_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .post-meta i'   => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .post-meta svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'post_meta_icon_spacing',
			[
				'label'      => esc_html__( 'Icon Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors'  => [
					'{{WRAPPER}} .post-meta i'   => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .post-meta svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_meta_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'post_meta_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-meta:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .post-meta:hover'   =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'post_meta_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-meta:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'post_meta_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-meta:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_meta_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-meta:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Read More Style
		$this->start_controls_section(
			'read_more_style',
			[
				'label'     => esc_html__( 'Read More', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => [ 'custom', 'main' ],
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'read_more_typography',
				'selector' =>
					'{{WRAPPER}} .post-read-more, {{WRAPPER}} .post-read-more a',
			]
		);

		$this->add_responsive_control(
			'read_more_align',
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
				'selectors' => [
					'{{WRAPPER}} .post-read-more' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_read_more' );

		$this->start_controls_tab(
			'read_more_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'read_more_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-read-more a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .post-read-more'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-read-more' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'read_more_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-read-more' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'read_more_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-read-more' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'read_more_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'read_more_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-read-more:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .post-read-more:hover'   =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .post-read-more:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'read_more_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-read-more:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'read_more_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .post-read-more:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Username/Display Name Style
		$this->start_controls_section(
			'user_name_style',
			[
				'label'     => esc_html__( 'Username/Display Name', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => 'user',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'user_name_typography',
				'selector' =>
					'{{WRAPPER}} .user-username, {{WRAPPER}} .user-username a, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-display-name a, {{WRAPPER}} .user-full-name, {{WRAPPER}} .user-full-name a',
			]
		);

		$this->add_responsive_control(
			'user_name_align',
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
				'selectors' => [
					'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_user_name' );

		$this->start_controls_tab(
			'user_name_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'user_name_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .user-username a, {{WRAPPER}} .user-display-name a, {{WRAPPER}} .user-full-name a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'user_name_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'user_name_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'user_name_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .user-username, {{WRAPPER}} .user-display-name, {{WRAPPER}} .user-full-name' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'user_name_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'user_name_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .user-username:hover a, {{WRAPPER}} .user-display-name:hover a, {{WRAPPER}} .user-full-name:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .user-username:hover, {{WRAPPER}} .user-display-name:hover, {{WRAPPER}} .user-full-name:hover' =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'user_name_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .user-username:hover, {{WRAPPER}} .user-display-name:hover, {{WRAPPER}} .user-full-name:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'user_name_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .user-username:hover, {{WRAPPER}} .user-display-name:hover, {{WRAPPER}} .user-full-name:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'user_name_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .user-username:hover, {{WRAPPER}} .user-display-name:hover, {{WRAPPER}} .user-full-name:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Visit Profile Style
		$this->start_controls_section(
			'visit_profile_style',
			[
				'label'     => esc_html__( 'Visit Profile', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => 'user',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'visit_profile_typography',
				'selector' =>
					'{{WRAPPER}} .visit-profile, {{WRAPPER}} .visit-profile a',
			]
		);

		$this->add_responsive_control(
			'visit_profile_align',
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
				'selectors' => [
					'{{WRAPPER}} .visit-profile' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_visit_profile' );

		$this->start_controls_tab(
			'visit_profile_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'visit_profile_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .visit-profile a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .visit-profile'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'visit_profile_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .visit-profile' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'visit_profile_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .visit-profile' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'visit_profile_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .visit-profile' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'visit_profile_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'visit_profile_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .visit-profile:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .visit-profile:hover'   =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'visit_profile_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .visit-profile:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'visit_profile_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .visit-profile:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'visit_profile_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .visit-profile:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Term Label Style
		$this->start_controls_section(
			'term_label_style',
			[
				'label'     => esc_html__( 'Term Label', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => 'taxonomy',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'term_label_typography',
				'selector' =>
					'{{WRAPPER}} .term-label, {{WRAPPER}} .term-label a',
			]
		);

		$this->add_responsive_control(
			'term_label_align',
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
				'selectors' => [
					'{{WRAPPER}} .term-label' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_term_label' );

		$this->start_controls_tab(
			'term_label_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'term_label_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-label a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .term-label'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'term_label_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-label' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'term_label_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-label' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'term_label_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-label' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'term_label_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'term_label_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-label:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .term-label:hover'   =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'term_label_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-label:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'term_label_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-label:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'term_label_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-label:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Term Count Style
		$this->start_controls_section(
			'term_count_style',
			[
				'label'     => esc_html__( 'Term Count', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => 'taxonomy',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'term_count_typography',
				'selector' =>
					'{{WRAPPER}} .term-count',
			]
		);

		$this->add_responsive_control(
			'term_count_align',
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
				'selectors' => [
					'{{WRAPPER}} .term-count' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_term_count' );

		$this->start_controls_tab(
			'term_count_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'term_count_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'term_count_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-count' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'term_count_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-count' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'term_count_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-count' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'term_count_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'term_count_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-count:hover' =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'term_count_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-count:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'term_count_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-count:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'term_count_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-count:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Term URL Style
		$this->start_controls_section(
			'visit_term_style',
			[
				'label'     => esc_html__( 'Term URL', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => 'taxonomy',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'visit_term_typography',
				'selector' =>
					'{{WRAPPER}} .term-url, {{WRAPPER}} .term-url a',
			]
		);

		$this->add_responsive_control(
			'visit_term_align',
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
				'selectors' => [
					'{{WRAPPER}} .term-url' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_visit_term' );

		$this->start_controls_tab(
			'visit_term_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'visit_term_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-url a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .term-url'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'visit_term_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-url' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'visit_term_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-url' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'visit_term_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-url' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'visit_term_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'visit_term_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-url:hover a' =>
						'color: {{VALUE}}',
					'{{WRAPPER}} .term-url:hover'   =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'visit_term_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .term-url:hover' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'visit_term_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-url:hover' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'visit_term_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .term-url:hover' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// WOOCOMMERCE SECTION.
		if ( class_exists( 'WooCommerce' ) ) {
			// ------------------------------------------------------------------------- CONTROL: Product Price Style
			$this->start_controls_section(
				'product_price_style',
				[
					'label'     => esc_html__( 'Product Price', 'better-post-filter-widgets-for-elementor' ),
					'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'query_type' => [ 'custom', 'main' ],
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name'     => 'product_price_typography',
					'selector' =>
						'{{WRAPPER}} .product-price, {{WRAPPER}} .product-price a',
				]
			);

			$this->add_responsive_control(
				'product_price_align',
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
					'selectors' => [
						'{{WRAPPER}} .product-price' =>
							'text-align: {{VALUE}}; justify-content: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'sales_price_size',
				[
					'label'      => esc_html__( 'Original Price Size', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em' ],
					'default'    => [
						'unit' => 'px',
						'size' => 16,
					],
					'selectors'  => [
						'{{WRAPPER}} .product-price del' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'style_tabs_product_price' );

			$this->start_controls_tab(
				'product_price_style_normal',
				[
					'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
				]
			);

			$this->add_control(
				'product_price_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-price' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'sales_price_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Original Price Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-price del .woocommerce-Price-amount' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'product_price_background_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-price' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'product_price_padding',
				[
					'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-price' =>
							'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_price_margin',
				[
					'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-price' =>
							'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'product_price_style_hover',
				[
					'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
				]
			);

			$this->add_control(
				'product_price_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-price:hover' =>
							'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'sales_price_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Original Price Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-price:hover del .woocommerce-Price-amount' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'product_price_background_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-price:hover' =>
							'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'product_price_padding_hover',
				[
					'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-price:hover' =>
							'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_price_margin_hover',
				[
					'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-price:hover' =>
							'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			// ------------------------------------------------------------------------- CONTROL: Buy Now Style
			$this->start_controls_section(
				'buy_now_style',
				[
					'label'     => esc_html__( 'Buy Now', 'better-post-filter-widgets-for-elementor' ),
					'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'query_type' => [ 'custom', 'main' ],
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name'     => 'buy_now_typography',
					'selector' =>
						'{{WRAPPER}} .product-buy-now, {{WRAPPER}} .product-buy-now a',
				]
			);

			$this->add_responsive_control(
				'buy_now_align',
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
					'selectors' => [
						'{{WRAPPER}} .product-buy-now' =>
							'text-align: {{VALUE}}; justify-content: {{VALUE}};',
					],
				]
			);

			$this->start_controls_tabs( 'style_tabs_buy_now' );

			$this->start_controls_tab(
				'buy_now_style_normal',
				[
					'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
				]
			);

			$this->add_control(
				'buy_now_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-buy-now' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'buy_now_background_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-buy-now' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'buy_now_padding',
				[
					'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-buy-now' =>
							'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'buy_now_margin',
				[
					'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-buy-now' =>
							'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'buy_now_style_hover',
				[
					'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
				]
			);

			$this->add_control(
				'buy_now_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-buy-now:hover' =>
							'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'buy_now_background_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-buy-now:hover' =>
							'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'buy_now_padding_hover',
				[
					'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-buy-now:hover' =>
							'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'buy_now_margin_hover',
				[
					'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-buy-now:hover' =>
							'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			// ------------------------------------------------------------------------- CONTROL: Product Badge Style
			$this->start_controls_section(
				'product_badge_style',
				[
					'label'     => esc_html__( 'Product Badge', 'better-post-filter-widgets-for-elementor' ),
					'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'query_type' => [ 'custom', 'main' ],
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name'     => 'product_badge_typography',
					'selector' =>
						'{{WRAPPER}} .product-badge, {{WRAPPER}} .product-badge a',
				]
			);

			$this->add_responsive_control(
				'product_badge_align',
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
					'selectors' => [
						'{{WRAPPER}} .product-badge' =>
							'text-align: {{VALUE}}; justify-content: {{VALUE}};',
					],
				]
			);

			$this->start_controls_tabs( 'style_tabs_product_badge' );

			$this->start_controls_tab(
				'product_badge_style_normal',
				[
					'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
				]
			);

			$this->add_control(
				'product_badge_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-badge' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'product_badge_background_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-badge' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'product_badge_padding',
				[
					'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-badge' =>
							'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_badge_margin',
				[
					'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-badge' =>
							'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'product_badge_style_hover',
				[
					'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
				]
			);

			$this->add_control(
				'product_badge_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-badge:hover' =>
							'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'product_badge_background_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-badge:hover' =>
							'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'product_badge_padding_hover',
				[
					'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-badge:hover' =>
							'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_badge_margin_hover',
				[
					'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-badge:hover' =>
							'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			// ------------------------------------------------------------------------- CONTROL: Product Rating Style
			$this->start_controls_section(
				'product_rating_style',
				[
					'label'     => esc_html__( 'Product Rating', 'better-post-filter-widgets-for-elementor' ),
					'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'query_type' => [ 'custom', 'main' ],
					],
				]
			);

			$this->add_control(
				'product_rating_size',
				[
					'label'      => esc_html__( 'Size', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'em', 'px' ],
					'range'      => [
						'em' => [
							'min'  => 1,
							'max'  => 10,
							'step' => 1,
						],
						'px' => [
							'min'  => 1,
							'max'  => 50,
							'step' => 1,
						],
					],
					'default'    => [
						'unit' => 'px',
						'size' => 20,
					],
					'selectors'  => [
						'{{WRAPPER}} .product-rating span' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_rating_align',
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
					'selectors' => [
						'{{WRAPPER}} .product-rating' =>
							'text-align: {{VALUE}}; justify-content: {{VALUE}};',
					],
				]
			);

			$this->start_controls_tabs( 'style_tabs_product_rating' );

			$this->start_controls_tab(
				'product_rating_style_normal',
				[
					'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
				]
			);

			$this->add_control(
				'product_rating_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-rating .star-full' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'product_empty_star_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Empty Star Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-rating .star-empty' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'product_rating_background_color',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-rating span' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'product_rating_padding',
				[
					'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-rating span' =>
							'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_rating_margin',
				[
					'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-rating span' =>
							'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'product_rating_style_hover',
				[
					'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
				]
			);

			$this->add_control(
				'product_rating_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-rating:hover .star-full' =>
							'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'product_empty_star_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Empty Star Color', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-rating:hover .star-empty' =>
							'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'product_rating_background_color_hover',
				[
					'type'      => \Elementor\Controls_Manager::COLOR,
					'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
					'selectors' => [
						'{{WRAPPER}} .product-rating:hover span' =>
							'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'product_rating_padding_hover',
				[
					'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-rating:hover span' =>
							'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'product_rating_margin_hover',
				[
					'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'selectors'  => [
						'{{WRAPPER}} .product-rating:hover span' =>
							'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

		} //END WOOCOMMERCE SECTION
		// ------------------------------------------------------------------------- CONTROL: Bullet Style
		$this->start_controls_section(
			'dots_style',
			[
				'label'     => esc_html__( 'Bullets Style', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'classic_layout'              => 'carousel',
					'post_slider_pagination_type' => 'bullets',
				],
			]
		);

		$this->add_control(
			'dot_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#007194',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' =>
						'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'dot_active_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Active Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#0098c7',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' =>
						'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'dot_size',
			[
				'label'      => esc_html__( 'Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' =>
						'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dot_spacing',
			[
				'label'      => esc_html__( 'Bullets Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' =>
						'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dot_spacing_wrapper',
			[
				'label'      => esc_html__( 'Bullets Gap', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' =>
						'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_align',
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
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Fraction Style
		$this->start_controls_section(
			'fraction_style',
			[
				'label'     => esc_html__( 'Fraction Style', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'classic_layout'              => 'carousel',
					'post_slider_pagination_type' => 'fraction',
				],
			]
		);

		$this->add_control(
			'fraction_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#007194',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'fraction_active_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Active Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#0098c7',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-current' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'fraction_size',
			[
				'label'      => esc_html__( 'Font Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' =>
						'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'fraction_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' =>
						'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Progressbar Style
		$this->start_controls_section(
			'progressbar_style',
			[
				'label'     => esc_html__( 'Progressbar Style', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'classic_layout'              => 'carousel',
					'post_slider_pagination_type' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progressbar_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#007194',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar' =>
						'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'progressbar_active_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Active Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#0098c7',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar-fill' =>
						'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'progressbar_size',
			[
				'label'      => esc_html__( 'Height', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'progressbar_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'unit' => 'px',
					'size' => 3,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' =>
						'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Load More Button Style
		$this->start_controls_section(
			'load_more_style',
			[
				'label'     => esc_html__( 'Load More Button', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'classic_layout!' => 'carousel',
					'pagination'      => [ 'load_more' ],
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs' );

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'load_more_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .load-more-wrapper a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'load_more_bg_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#0E4B65',
				'selectors' => [
					'{{WRAPPER}} .load-more-wrapper a' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'load_more_border_type',
				'label'    => esc_html__( 'Border Type', 'better-post-filter-widgets-for-elementor' ),
				'selector' => '{{WRAPPER}} .load-more-wrapper a',
			]
		);

		$this->add_control(
			'load_more_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .load-more-wrapper a' =>
						'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'load_more_hover_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .load-more-wrapper:hover a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'load_more_hover_bg_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .load-more-wrapper:hover a' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'load_more_hover_border_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .load-more-wrapper:hover a' =>
						'border-color: {{VALUE}}',
				],
				'condition' => [
					'load_more_border_type_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'load_more_gap',
			[
				'label'      => esc_html__( 'Button Gap', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .load-more-wrapper' =>
						'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'load_more_typography',
				'selector' => '{{WRAPPER}} a.load-more',
			]
		);

		$this->add_responsive_control(
			'load_more_align',
			[
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'label'                => esc_html__( 'Alignment', 'better-post-filter-widgets-for-elementor' ),
				'options'              => [
					'left'    => [
						'title' => esc_html__( 'Left', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'better-post-filter-widgets-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'              => 'center',
				'selectors'            => [
					'{{WRAPPER}} .load-more-wrapper' =>
						'{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'    => 'text-align: left',
					'center'  => 'text-align: center',
					'right'   => 'text-align: right',
					'justify' => 'display: grid',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Arrow Style
		$this->start_controls_section(
			'section_arrows_style',
			[
				'label'     => __( 'Arrows Style', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'classic_layout'     => 'carousel',
					'post_slider_arrows' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'      => __( 'Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'default'    => [ 'size' => '22' ],
				'range'      => [
					'px' => [
						'min'  => 15,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
						'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_position',
			[
				'label'      => __( 'Arrows Position', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'max'  => 50,
						'step' => 1,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_arrows_style' );

		$this->start_controls_tab(
			'tab_arrows_normal',
			[
				'label' => __( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'arrows_bg_color_normal',
			[
				'label'     => __( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
						'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_color_normal',
			[
				'label'     => __( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'arrows_border_normal',
				'label'    => __( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'selector' =>
					'{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after',
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius_normal',
			[
				'label'      => __( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' =>
						'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			[
				'label' => __( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'arrows_bg_color_hover',
			[
				'label'     => __( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-prev:hover:after' =>
						'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_color_hover',
			[
				'label'     => __( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-prev:hover:after' =>
						'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'        => 'arrows_border_hover',
				'label'       => __( 'Border', 'better-post-filter-widgets-for-elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    =>
					'{{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-prev:hover:after',
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius_hover',
			[
				'label'      => __( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-button-next:hover:after, {{WRAPPER}} .swiper-button-prev:hover:after' =>
						'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Pagination Style
		$this->start_controls_section(
			'pagination_style',
			[
				'label'      => esc_html__( 'Pagination', 'better-post-filter-widgets-for-elementor' ),
				'tab'        => \Elementor\Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'pagination',
							'operator' => 'in',
							'value'    => [ 'numbers', 'numbers_and_prev_next' ],
						],
						[
							'name'     => 'pagination_carousel',
							'operator' => 'in',
							'value'    => [ 'numbers', 'numbers_and_prev_next' ],
						],
					],
				],
			]
		);

		$this->add_control(
			'pagination_gap',
			[
				'label'      => esc_html__( 'Pagination Gap', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .pagination, {{WRAPPER}} .pagination-filter' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'selector' =>
					'{{WRAPPER}} .pagination, {{WRAPPER}} .pagination-filter, {{WRAPPER}} .pagination a, {{WRAPPER}} .pagination-filter a',
			]
		);

		$this->add_responsive_control(
			'pagination_align',
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
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .pagination, {{WRAPPER}} .pagination-filter' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_pagination' );

		$this->start_controls_tab(
			'style_pagination_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Pagination Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} a.page-numbers' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_bg_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} a.page-numbers' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'pagination_border_type',
				'label'    => esc_html__( 'Border Type', 'better-post-filter-widgets-for-elementor' ),
				'selector' =>
					'{{WRAPPER}} a.page-numbers, {{WRAPPER}} .page-numbers',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_pagination_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Pagination Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} a.page-numbers:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .page-numbers:hover'  => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_bg_hover_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} a.page-numbers:hover' =>
						'background-color: {{VALUE}}',
					'{{WRAPPER}} .page-numbers:hover'  =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_border_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} a.page-numbers:hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .page-numbers:hover'  => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'pagination_border_type_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_pagination_active_tab',
			[
				'label' => esc_html__( 'Active', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'pagination_active_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Pagination Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .page-numbers.current' => 'color: {{VALUE}}',
					'{{WRAPPER}} .page-numbers.dots'    => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_bg_active_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .page-numbers.current' =>
						'background-color: {{VALUE}}',
					'{{WRAPPER}} .page-numbers.dots'    =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_active_border_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Border Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .page-numbers.current' =>
						'border-color: {{VALUE}}',
					'{{WRAPPER}} .page-numbers.dots'    => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'pagination_border_type_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pagination_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .page-numbers' => 'padding: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'pagination_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .page-numbers' =>
						'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'pagination_space',
			[
				'label'      => esc_html__( 'Space Between Numbers', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .page-numbers' =>
						'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Spiner Style
		$this->start_controls_section(
			'loader_style',
			[
				'label'      => esc_html__( 'Spinner', 'better-post-filter-widgets-for-elementor' ),
				'tab'        => \Elementor\Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'post_skin',
							'operator' => 'in',
							'value'    => [ 'banner', 'template', 'custom_html' ],
						],
						[
							'name'     => 'pagination',
							'operator' => '===',
							'value'    => 'infinite',
						],
					],
				],
			]
		);

		$this->add_control(
			'loader_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Circle Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '#0098C7',
				'selectors' => [
					'{{WRAPPER}} .preloader-inner .preloader-inner-half-circle, {{WRAPPER}} .load::before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Post Pin Style
		$this->start_controls_section(
			'post_pin_style',
			[
				'label'     => esc_html__( 'Bookmark', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => [ 'custom', 'main' ],
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'pin_typography',
				'selector' =>
					'{{WRAPPER}} .post-pin .text',
			]
		);

		$this->start_controls_tabs( 'style_pins' );

		$this->start_controls_tab(
			'style_pinned_tab',
			[
				'label' => esc_html__( 'Bookmarked', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'post_pin_text_color_pinned',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pin-text .text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'post_pin_color_pinned',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Icon Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pin-text i, {{WRAPPER}} .pin-text svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_pin_color_pinned',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Stroke Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pin-text svg' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_unpinned_tab',
			[
				'label' => esc_html__( 'Unbookmarked', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'post_pin_text_color_unpinned',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Text Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .unpin-text .text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'post_pin_color_unpinned',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Icon Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .unpin-text i, {{WRAPPER}} .unpin-text svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_pin_color_unpinned',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Stroke Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .unpin-text svg' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pin_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .post-pin i'   => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .post-pin svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pin_icon_spacing',
			[
				'label'      => esc_html__( 'Icon Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors'  => [
					'{{WRAPPER}} .post-pin i'   => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .post-pin svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Edit Options Style
		$this->start_controls_section(
			'edit_options_style',
			[
				'label'     => esc_html__( 'Edit Options', 'better-post-filter-widgets-for-elementor' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'query_type' => [ 'custom', 'main' ],
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'edit_options_typography',
				'selector' =>
					'{{WRAPPER}} .edit-options a',
			]
		);

		$this->add_responsive_control(
			'edit_options_align',
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
				'selectors' => [
					'{{WRAPPER}} .edit-options' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'style_tabs_edit_options' );

		$this->start_controls_tab(
			'edit_options_style_normal',
			[
				'label' => esc_html__( 'Normal', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'edit_options_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .edit-options a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'edit_options_background_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .edit-options a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'edit_options_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .edit-options a' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'edit_options_margin',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .edit-options a' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'edit_options_icon_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Icon Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .edit-options i, {{WRAPPER}} .edit-options svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'edit_options_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .edit-options i'   => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .edit-options svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'edit_options_icon_spacing',
			[
				'label'      => esc_html__( 'Icon Spacing', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors'  => [
					'{{WRAPPER}} .edit-options i'   => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .edit-options svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'edit_options_style_hover',
			[
				'label' => esc_html__( 'Hover', 'better-post-filter-widgets-for-elementor' ),
			]
		);

		$this->add_control(
			'edit_options_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .edit-options:hover a' =>
						'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'edit_options_background_color_hover',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background', 'better-post-filter-widgets-for-elementor' ),
				'selectors' => [
					'{{WRAPPER}} .edit-options:hover a' =>
						'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'edit_options_padding_hover',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .edit-options:hover a' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'edit_options_margin_hover',
			[
				'label'      => esc_html__( 'Margin', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .edit-options:hover a' =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------- CONTROL: Nothing Found Message Style
		$this->start_controls_section(
			'nothing_found_style',
			[
				'label' => esc_html__( 'Nothing Found Message', 'better-post-filter-widgets-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nothing_found_color',
			[
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'better-post-filter-widgets-for-elementor' ),
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .no-post' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'nothing_found_align',
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
				'selectors' => [
					'{{WRAPPER}} .no-post' =>
						'text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nothing_found_font_size',
			[
				'label'      => esc_html__( 'Font Size', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .no-post' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'nothing_found_padding',
			[
				'label'      => esc_html__( 'Padding', 'better-post-filter-widgets-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .no-post' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Filter the main query before it is executed.
	 *
	 * This function modifies the WordPress query by allowing customization of query parameters
	 * based on widget settings. It also triggers a custom action to allow further filtering
	 * by other parts of the code.
	 *
	 * @param WP_Query $wp_query The WordPress query object that is being filtered.
	 */
	public function pre_get_posts_query_filter( $wp_query ) {
		$settings = $this->get_settings_for_display();
		$query_id = $settings['query_id'];
		do_action( "bpfwe/query/{$query_id}", $wp_query, $this );
	}

	/**
	 * Retrieve the CSS content for the widget template.
	 *
	 * This function generates or retrieves the CSS styles that apply to the widget's template.
	 * The styles are either generated dynamically or loaded from a predefined stylesheet.
	 *
	 * @param int $template_id The ID of the template.
	 */
	public function enqueue_skin_css( $template_id ) {
		$template_id = intval( $template_id );

		$upload_dir = wp_upload_dir();
		$base_url   = trailingslashit( $upload_dir['baseurl'] );
		$base_path  = trailingslashit( $upload_dir['basedir'] );

		// Define possible CSS file paths and URLs.
		$post_css_path = $base_path . 'elementor/css/post-' . $template_id . '.css';
		$post_css_url  = $base_url . 'elementor/css/post-' . $template_id . '.css';
		$loop_css_path = $base_path . 'elementor/css/loop-' . $template_id . '.css';
		$loop_css_url  = $base_url . 'elementor/css/loop-' . $template_id . '.css';

		$deps = [ 'elementor-frontend' ];

		// Check if the post CSS file exists and enqueue it with dependencies.
		if ( file_exists( $post_css_path ) ) {
			wp_enqueue_style(
				'post-css-' . $template_id,
				$post_css_url,
				$deps,
				filemtime( $post_css_path )
			);
		}

		// Check if the loop CSS file exists and enqueue it with dependencies.
		if ( file_exists( $loop_css_path ) ) {
			wp_enqueue_style(
				'loop-css-' . $template_id,
				$loop_css_url,
				$deps,
				filemtime( $loop_css_path )
			);
		}
	}

	/**
	 * Retrieve the base URL for pagination with the current page.
	 *
	 * This function generates the base URL for pagination, incorporating the current page
	 * parameter, if necessary. It ensures that the pagination links are correctly generated
	 * based on the current page context.
	 *
	 * @since 1.0.0
	 * @param array $settings Settings array that includes query type and other parameters.
	 * @return string The base URL for pagination with the current page.
	 */
	private function get_pagination_base_current( $settings ) {
		global $wp;
		$base = '';

		$link_unescaped = get_pagenum_link( 1, false );
		$base           = strtok( $link_unescaped, '?' ) . '%_%';
		$current_page   = 1;

		if ( 'main' === $settings['query_type'] ) {
			$current_page = max( 1, get_query_var( 'paged' ) );
		} elseif ( 'custom' === $settings['query_type'] || 'user' === $settings['query_type'] ) {
			if ( is_home() || is_archive() || is_post_type_archive() ) {
				$base         = add_query_arg( 'page_num', '%#%' );
				$current_page = max( 1, get_query_var( 'page_num' ) );
			} elseif ( is_front_page() ) {
				$current_page = max( 1, get_query_var( 'page' ) );
			} elseif ( is_author() || is_single() || is_page() ) {
				$base         = add_query_arg( 'page_num', '%#%' );
				$current_page = max( 1, get_query_var( 'page_num' ) );
			} elseif ( is_search() ) {
				$base         = add_query_arg( 'page_num', '%#%', home_url( $wp->request ) );
				$current_page = max( 1, get_query_var( 'page_num' ) );
			} elseif ( is_tax() ) {
				$term         = get_queried_object();
				$base         = get_term_link( $term ) . 'page/%#%/';
				$current_page = max( 1, get_query_var( 'paged' ) );
			} else {
				$current_page = max( 1, get_query_var( 'paged' ) );
			}
		}

		return [ $base, $current_page ];
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
	 * Render post widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		global $wp_query;
		$current_query_vars = $GLOBALS['wp_query']->query_vars;
		$settings           = $this->get_settings_for_display();

		$overlay      = 'yes' === $settings['overlay'] ? '<span class="overlay"></span>' : '';
		$lazy_load    = 'yes' === $settings['post_slider_lazy_load'] ? 'swiper-lazy' : '';
		$class_swiper = 'elementor-grid';
		$image        = '';
		$pagination   = '';

		if ( isset( $settings['classic_layout'] ) && 'carousel' === $settings['classic_layout'] ) {
			$pagination = isset( $settings['pagination_carousel'] ) ? $settings['pagination_carousel'] : 'none';
		} else {
			// Use the regular pagination if not in carousel layout.
			$pagination = isset( $settings['pagination'] ) ? $settings['pagination'] : 'none';
		}

		if ( 'carousel' === $settings['classic_layout'] ) {
			$class_swiper = 'yes' === $settings['hide_feature_image'] ? 'elementor-grid hide-featured-image bpfwe-swiper' : 'elementor-grid bpfwe-swiper';
		}

		if ( 'masonry' === $settings['classic_layout'] ) {
			$class_swiper = 'elementor-grid bpfwe-masonry';
		}

		$skin          = $settings['post_skin'];
		$post_html_tag = $settings['post_html_tag'];

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
			'order'               => in_array( $settings['order'], [ 'ASC', 'DESC' ], true ) ? $settings['order'] : 'DESC',
			'orderby'             => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
			'post_status'         => ! empty( $settings['post_status'] ) ? $settings['post_status'] : 'publish',
			'posts_per_page'      => ! empty( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : get_option( 'posts_per_page' ),
			'ignore_sticky_posts' => 'yes' === $settings['sticky_posts'] ? 0 : 1,
			'fields'              => 'ids',
		];

		if ( ! empty( $settings['sort_by_meta'] ) ) {
			$query_args['meta_key'] = $settings['sort_by_meta'];
		}

		$post_types_array = [];

		if ( ! empty( $settings['post_type'] ) ) {
			$post_types_array = is_array( $settings['post_type'] ) ? $settings['post_type'] : [ $settings['post_type'] ];

			if ( in_array( 'any', $post_types_array, true ) ) {
				$query_args['post_type'] = 'any';
			} else {
				$post_types_array = array_filter( $post_types_array, 'post_type_exists' );
				if ( ! empty( $post_types_array ) ) {
					$query_args['post_type'] = $post_types_array;
				}
			}
		}

		if ( 'none' !== $pagination ) {
			$query_args['paged'] = $paged;
		}

		if ( ! empty( $settings['post_offset'] ) && 0 !== $settings['post_offset'] ) {
			$query_args['offset'] = $settings['post_offset'];
		}

		$post_in_id = 'post__in_' . implode( '_', $post_types_array );

		if ( $settings['include_post_id'] ) {
			$post_ids               = explode( ',', $settings['include_post_id'] );
			$query_args['post__in'] = $post_ids;
		} elseif ( isset( $settings[ $post_in_id ] ) && empty( $settings['include_post_id'] ) ) {
			$query_args['post__in'] = $settings[ $post_in_id ];
		}

		if ( $settings['pinned_posts'] ) {
			$pinned_posts = get_user_meta( get_current_user_id(), 'post_id_list', true );
			if ( empty( $pinned_posts ) ) {
				if ( isset( $_COOKIE['post_id_list'] ) ) {
					$raw_cookie_data = isset( $_COOKIE['post_id_list'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['post_id_list'] ) ) : '';
					$pinned_posts    = json_decode( $raw_cookie_data, true );

					if ( is_null( $pinned_posts ) ) {
						$pinned_posts = array();
					} elseif ( is_array( $pinned_posts ) ) {
						$pinned_posts = array_map( 'absint', $pinned_posts );
					}
				}
			}
			if ( $pinned_posts ) {
				$query_args['post__in'] = $pinned_posts;
			} else {
				$query_args['post__in'] = array( 0 );
			}
		}

		$pinned_post = $settings['pinned_posts'] ? 'pinned_post_query' : '';

		$post_not_in_id = 'post__not_in_' . implode( '_', $post_types_array );

		if ( $settings['exclude_post_id'] ) {
			$exclude_post_ids           = explode( ',', $settings['exclude_post_id'] );
			$query_args['post__not_in'] = $exclude_post_ids;
		} elseif ( isset( $settings[ $post_not_in_id ] ) && empty( $settings['exclude_post_id'] ) ) {
			$query_args['post__not_in'] = $settings[ $post_not_in_id ];
		}

		if ( $settings['related_posts'] ) {
			if ( in_array( 'post', $post_types_array, true ) ) {
				$query_args['category__in'] = wp_get_post_categories( get_the_ID() );
				$query_args['post__not_in'] = array( get_the_ID() );
			} else {
				$tax      = $settings['related_post_taxonomy'];
				$terms    = get_the_terms( get_the_ID(), $tax, 'string' );
				$term_ids = wp_list_pluck( $terms, 'term_id' );

				$query_args['tax_query']    = array(
					array(
						'taxonomy' => $tax,
						'field'    => 'id',
						'terms'    => $term_ids,
						'operator' => 'IN',
					),
				);
				$query_args['post__not_in'] = array( get_the_ID() );
			}
		}

		$taxonomy           = get_object_taxonomies( implode( '_', $post_types_array ), 'objects' );
			$tax_cat_in     = '';
			$tax_cat_not_in = '';
			$tax_tag_in     = '';
			$tax_tag_not_in = '';

		if ( ! empty( $taxonomy ) ) {

			foreach ( $taxonomy as $index => $tax ) {

				$tax_control_key = $index . '_' . implode( '_', $post_types_array );

				if ( 'post' === implode( '_', $post_types_array ) ) {
					if ( 'post_tag' === $index ) {
						$tax_control_key = 'tags';
					} elseif ( 'category' === $index ) {
						$tax_control_key = 'categories';
					}
				}

				if ( ! empty( $settings[ $tax_control_key ] ) ) {

					$operator = $settings[ $index . '_' . implode( '_', $post_types_array ) . '_filter_type' ];

					$query_args['tax_query'][] = array(
						'taxonomy' => $index,
						'field'    => 'term_id',
						'terms'    => $settings[ $tax_control_key ],
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

		if ( ! empty( $settings['query_id'] ) ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
		}

		if ( 'main' === $settings['query_type'] ) {
			$bpfwe_query = new WP_Query( $current_query_vars );
		} elseif ( 'custom' === $settings['query_type'] ) {
			$bpfwe_query = new WP_Query( $query_args );
		}

		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );

		if ( 'main' === $settings['query_type'] || 'custom' === $settings['query_type'] ) {
			if ( $bpfwe_query->have_posts() ) {

				$counter = 0;

				if ( $settings['skin_template'] ) {
					$extra_templates_by_position = [];
					$template_css_urls           = [];

					if ( isset( $settings['extra_skin_list'] ) && is_array( $settings['extra_skin_list'] ) ) {
						foreach ( $settings['extra_skin_list'] as $item ) {
							$extra_templates_by_position[ $item['grid_position'] ] = $item;
						}
					}

					$combined_css = '';

					// Collect CSS contents for the main template.
					if ( ! empty( $settings['skin_template'] ) && is_numeric( $settings['skin_template'] ) ) {
						$main_template_id = intval( $settings['skin_template'] );
						$this->enqueue_skin_css( $main_template_id );
					}

					// Collect CSS contents for the extra templates.
					foreach ( $extra_templates_by_position as $extra_template ) {
						if ( isset( $extra_template['extra_template_id'] ) && is_numeric( $extra_template['extra_template_id'] ) ) {
							$extra_template_id = intval( $extra_template['extra_template_id'] );
							$this->enqueue_skin_css( $extra_template_id );
						}
					}
				}

				echo '
				<div class="post-container ' . esc_attr( $pagination . ' ' . $skin . ' ' . $pinned_post ) . '" data-total-post="' . absint( $bpfwe_query->found_posts ) . '">
                <div class="post-container-inner">
				<div class="' . esc_attr( $class_swiper ) . '">
				';

				while ( $bpfwe_query->have_posts() ) :
					++$counter;
					$bpfwe_query->the_post();

					$permalink                                   = get_permalink();
					$new_tab                                     = '';
					$settings['external_url_new_tab'] ? $new_tab = 'target="_blank"' : $new_tab = '';

					if ( $settings['post_external_url'] ) {
						$external_url = get_post_meta( get_the_ID(), $settings['post_external_url'], true );

						if ( strpos( $settings['post_external_url'], 'http' ) !== false ) {
							$external_url = esc_url( $settings['post_external_url'] );
						}
						if ( $external_url ) {
							$permalink = $external_url;
						} elseif ( $settings['post_external_if_empty'] ) {
							$permalink = get_permalink();
							$new_tab   = '';
						} else {
							$permalink = '';
						}
					}

					if ( $settings['skin_template'] ) {

						// Check if the current position should have an extra template.
						$use_extra_template = false;
						$extra_template_id  = '';
						$column_span        = 1;
						$row_span           = 1;
						$column_span_style  = '';
						$row_span_style     = '';

						foreach ( $extra_templates_by_position as $position => $extra_template ) {
							// Check if the template should apply once or be repeated.
							$apply_once = isset( $extra_template['apply_once'] ) && 'yes' === $extra_template['apply_once'];

							if ( ( $apply_once && $counter === $position ) || ( ! $apply_once && 0 === $counter % $position ) ) {
								$use_extra_template = true;
								$extra_template_id  = $extra_template['extra_template_id'];
								$column_span        = $extra_template['column_span'];
								$row_span           = $extra_template['row_span'];
								$column_span_style  = $column_span > 1 ? 'grid-column: span ' . $column_span . ';' : '';
								$row_span_style     = $row_span > 1 ? 'grid-row: span ' . $row_span . ';' : '';
								break;
							}
						}

						$style           = trim( "$column_span_style $row_span_style" );
						$style_attribute = $style ? 'style="' . $style . '"' : '';
						$extra_class     = $style_attribute ? 'row-span-expand' : '';

						if ( $use_extra_template ) {
							echo '<' . esc_attr( $post_html_tag ) . ' class="post-wrapper ' . esc_attr( $extra_class ) . '" ' . wp_kses_post( $style_attribute ) . '><div class="inner-content">';
							echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( intval( $extra_template_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '</div></' . esc_attr( $post_html_tag ) . '>';
						} else {
							echo '<' . esc_attr( $post_html_tag ) . ' class="post-wrapper"><div class="inner-content">';
							echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( intval( $settings['skin_template'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '</div></' . esc_attr( $post_html_tag ) . '>';
						}
					} elseif ( $settings['skin_custom_html'] ) {
							$image = '<img style="background-image: url(' . get_the_post_thumbnail_url( $bpfwe_query->ID, 'full' ) . ')" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/BPFWE-Placeholder-Image-' . $settings['img-aspect-ratio'] . '.png" alt="Post Image Placeholder"/>';
						if ( ! get_the_post_thumbnail_url() ) {
							$image = '<img style="background-image: url(' . $settings['post_default_image']['url'] . ')" src="' . plugin_dir_url( __DIR__ ) . 'assets/images/BPFWE-Placeholder-Image-' . $settings['img-aspect-ratio'] . '.png" alt="Post Image Placeholder"/>';
						}

						$html_content = $settings['skin_custom_html'];
						$html_content = str_replace( '#TITLE#', esc_html( get_the_title() ), $html_content );
						$html_content = str_replace( '#PERMALINK#', esc_url( get_permalink() ), $html_content );
						$html_content = str_replace( '#CONTENT#', get_the_content(), $html_content );
						$html_content = str_replace( '#EXCERPT#', get_the_excerpt(), $html_content );
						$html_content = str_replace( '#IMAGE#', $image, $html_content );

						echo '<' . esc_attr( $post_html_tag ) . ' class="post-wrapper"><div class="inner-content">';
						echo wp_kses_post( $html_content );
						echo '</div></' . esc_attr( $post_html_tag ) . '>';
					} else {
						echo '<' . esc_attr( $post_html_tag ) . ' class="post-wrapper">';

						if ( 'yes' === $settings['show_featured_image'] ) {
							$image_size = $settings['featured_img_size'] ? $settings['featured_img_size'] : 'full';

							// Prepare escaped URLs.
							$image_url             = esc_url( get_the_post_thumbnail_url( $bpfwe_query->ID, $image_size ) );
							$image_url_id          = esc_url( get_the_post_thumbnail_url( $bpfwe_query->ID ) );
							$placeholder_image_url = esc_url( plugin_dir_url( __DIR__ ) . 'assets/images/BPFWE-Placeholder-Image-' . esc_attr( $settings['img-aspect-ratio'] ) . '.png' );
							$default_image_url     = esc_url( $settings['post_default_image']['url'] );
							$image_id              = attachment_url_to_postid( $image_url_id );
							$image_alt             = ! empty( $image_id ) ? get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
							$image_alt             = ! empty( $image_alt ) ? esc_attr( $image_alt ) : 'Post Image Placeholder';

							// Lazy load image.
							if ( 'yes' === $settings['img_equal_height'] ) {
								if ( $lazy_load ) {
									$image = '<img class="swiper-lazy" data-background="' . $image_url . '" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $image_url . '" alt="' . $image_alt . '"/><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = '<img style="background-image: url(' . $image_url . ')" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $image_url . '" alt="' . $image_alt . '"/>';
								}
								if ( ! $image_url ) {
									if ( $lazy_load ) {
										$image = '<img class="swiper-lazy" data-background="' . $default_image_url . '" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $default_image_url . '" alt="' . $image_alt . '"/><div class="swiper-lazy-preloader"></div>';
									} else {
										$image = '<img style="background-image: url(' . $default_image_url . ')" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $default_image_url . '" alt="' . $image_alt . '"/>';
									}
								}
							} else {
								if ( $lazy_load ) {
									$image = '<img class="swiper-lazy" data-src="' . $image_url . '" data-bpfwe-src="' . $image_url . '"><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = get_the_post_thumbnail( $bpfwe_query->ID, $image_size );
								}
								if ( ! $image ) {
									if ( $lazy_load ) {
										$image = '<img class="swiper-lazy" data-src="' . $default_image_url . '" data-bpfwe-src="' . $default_image_url . '" alt="' . $image_alt . '"/><div class="swiper-lazy-preloader"></div>';
									} else {
										$image = '<img src="' . $default_image_url . '" data-bpfwe-src="' . $default_image_url . '" alt="' . $image_alt . '"/>';
									}
								}
							}

							// Output HTML with escaped values.
							if ( $settings['post_image_url'] && ! empty( $permalink ) ) {
								echo '<div class="post-image"><a href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . wp_kses_post( $image . $overlay ) . '</a></div>';
							} else {
								echo '<div class="post-image">' . wp_kses_post( $image . $overlay ) . '</div>';
							}
						}
						echo '<div class="inner-content">';
						foreach ( $settings['post_list'] as $index => $item ) :
							$pseudo_icon = isset( $item['pseudo_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['pseudo_icon'] ) : '';
							$before      = $item['field_before'] ? '<span class="pseudo">' . str_replace( '#', $counter, $item['field_before'] ) . '</span>' : '';
							$after       = $item['field_after'] ? '<span class="pseudo">' . str_replace( '#', $counter, $item['field_after'] ) . '</span>' : '';

							// Display Title.
							if ( 'Title' === $item['post_content'] ) {
								if ( $item['post_title_url'] && ! empty( $permalink ) ) {
									echo '<' . esc_attr( $settings['html_tag'] ) . ' class="post-title elementor-repeater-item-' . esc_attr( $item['_id'] ) . '"><a href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . wp_trim_words( get_the_title(), absint( $item['title_length'] ), '...' ) . $after ) . '</a></' . esc_attr( $settings['html_tag'] ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									echo '<' . esc_attr( $settings['html_tag'] ) . ' class="post-title elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . wp_trim_words( get_the_title(), absint( $item['title_length'] ), '...' ) . $after ) . '</' . esc_attr( $settings['html_tag'] ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
							}

							// Display Content.
							if ( 'Content' === $item['post_content'] ) {
								$content = get_the_content();
								$content = apply_filters( 'the_content', $content );
								$content = str_replace( ']]>', ']]&gt;', $content );
								echo '<div class="post-content elementor-repeater-item-' . esc_attr( $item['_id'] ) . '"><p>' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . wp_trim_words( $content, absint( $item['description_length'] ), '...' ) . $after ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}

							// Display Excerpt.
							if ( 'Excerpt' === $item['post_content'] ) {
								echo '<div class="post-excerpt elementor-repeater-item-' . esc_attr( $item['_id'] ) . '"><p>' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . wp_trim_words( get_the_excerpt(), absint( $item['description_length'] ), '...' ) . $after ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}

							// Display Custom Field or ACF Field.
							if ( 'Custom Field' === $item['post_content'] ) {
								$custom_field_key = sanitize_key( $item['post_field_key'] );
								$custom_field_val = BPFWE_Helper::is_acf_field( $custom_field_key ) ? get_field( $custom_field_key, get_the_ID() ) : get_post_meta( get_the_ID(), $custom_field_key, true );

								if ( $custom_field_val && ! empty( $item['post_field_key'] ) ) {
									echo '<div class="post-custom-field elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $custom_field_val . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
							}

							// Display HTML with Shortcode Support.
							if ( 'HTML' === $item['post_content'] ) {
								$content = $item['post_html'];
								$content = do_shortcode( $content );

								echo '<div class="post-html elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $content . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}

							// Display Post Meta.
							if ( 'Post Meta' === $item['post_content'] ) {
								$author_icon  = isset( $item['author_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['author_icon'] ) : '';
								$date_icon    = isset( $item['date_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['date_icon'] ) : '';
								$comment_icon = isset( $item['comment_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['comment_icon'] ) : '';

								$author = 'yes' === $item['post_author_url'] ? '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>' : esc_html( get_the_author() );

								$display_author = 'yes' === $item['display_meta_author'] ? $author_icon . $author : '';
								$date_format    = $item['display_date_format'] ? $item['display_date_format'] : 'j M. Y';
								if ( 'from_time' === $date_format ) {
									$post_date    = get_the_date( 'Y-m-d H:i:s' );
									$display_date = 'yes' === $item['display_meta_date'] ? esc_html( $item['post_meta_separator'] ) . $date_icon . BPFWE_Helper::time_elapsed_string( $post_date ) : '';
								} else {
									$display_date = 'yes' === $item['display_meta_date'] ? esc_html( $item['post_meta_separator'] ) . $date_icon . esc_html( get_the_date( $date_format ) ) : '';
								}
								$comments_count  = get_comments_number();
								$display_comment = 'yes' === $item['display_meta_comment'] ? esc_html( $item['post_meta_separator'] ) . $comment_icon . intval( $comments_count ) . ( $comments_count <= 1 ? ' ' . __( 'comment', 'better-post-filter-widgets-for-elementor' ) : ' ' . __( 'comments', 'better-post-filter-widgets-for-elementor' ) ) : '';

								echo '<div class="post-meta elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon . $before . $display_author . $display_date . $display_comment . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}

							// Display Taxonomy.
							if ( 'Taxonomy' === $item['post_content'] ) {
								$terms_nb = $item['post_taxonomy_nb'];
								$terms    = wp_get_object_terms( get_the_ID(), $item['post_taxonomy'] );

								if ( $terms ) {
									echo '<ul class="post-taxonomy elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">';
									$i = 0;
									foreach ( $terms as $term ) {
										if ( $terms_nb > $i ) {
											if ( 0 === $term->count ) {
												echo '<li>' . wp_kses_post( $before . $term->name . $after ) . '</li>';
											} elseif ( $term->count > 0 ) {
												echo '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $term->name . $after ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											}
										}
										++$i;
									}
									echo '</ul>';
								}
							}

							// Display Read More.
							if ( 'Read More' === $item['post_content'] ) {
								if ( ! empty( $permalink ) ) {
									echo '<a class="post-read-more elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $item['post_read_more_text'] . $after ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									echo '<span class="post-read-more elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $item['post_read_more_text'] . $after ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
							}

							// Display Pin.
							if ( 'Pin Post' === $item['post_content'] ) {
								$pin_icon   = isset( $item['pin_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['pin_icon'] ) : '';
								$unpin_icon = isset( $item['unpin_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['unpin_icon'] ) : '';

								$post_id       = get_the_ID();
								$bpfwe_user_id = get_current_user_id();
								$post_list     = array(); // Initialize as an empty array.

								if ( ! empty( $bpfwe_user_id ) ) {
									$user_post_list = get_user_meta( $bpfwe_user_id, 'post_id_list', true );

									if ( is_array( $user_post_list ) ) {
										$post_list = array_map( 'absint', $user_post_list );
									}
								} elseif ( isset( $_COOKIE['post_id_list'] ) ) {
									$raw_cookie_data = isset( $_COOKIE['post_id_list'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['post_id_list'] ) ) : '';
									$post_list       = json_decode( $raw_cookie_data, true );

									if ( is_null( $post_list ) ) {
										$post_list = array();
									} elseif ( is_array( $post_list ) ) {
										$post_list = array_map( 'absint', $post_list );
									}
								}

								if ( ( $item['post_pin_logged_out'] && ! empty( $bpfwe_user_id ) ) || empty( $item['post_pin_logged_out'] ) ) {
									$class = in_array( $post_id, $post_list, true ) ? 'unpin' : '';
									echo '<a class="post-pin elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $class ) . '" href="#" data-postid="' . esc_attr( $post_id ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before ) . '<span class="pin-text">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pin_icon ) . ( ! empty( $item['pin_text'] ) ? '<span class="text">' . esc_html( $item['pin_text'] ) . '</span>' : '' ) . '</span><span class="unpin-text">' . BPFWE_Helper::sanitize_and_escape_svg_input( $unpin_icon ) . ( ! empty( $item['unpin_text'] ) ? '<span class="text">' . esc_html( $item['unpin_text'] ) . '</span>' : '' ) . '</span>' . wp_kses_post( $after ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
							}

							// Display Edit Options.
							if ( 'Edit Options' === $item['post_content'] ) {
								$edit_icon      = isset( $item['edit_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['edit_icon'] ) : '';
								$delete_icon    = isset( $item['delete_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['delete_icon'] ) : '';
								$republish_icon = isset( $item['republish_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['republish_icon'] ) : '';
								$unpublish_icon = isset( $item['unpublish_icon'] ) ? BPFWE_Helper::bpfwe_get_icons( $item['unpublish_icon'] ) : '';

								$current_user = wp_get_current_user();
								$post_id      = get_the_ID();
								$edit_url     = $item['edit_url'] ? str_replace( '#ID#', $post_id, $item['edit_url'] ) : '#';
								if ( get_post_status() === 'draft' ) {
									'yes' === $item['display_republish_option'] ? $republish = '<a class="unpublish-button" data-postid="' . esc_attr( $post_id ) . '" data-opposite-label="' . esc_attr( $item['unpublish_option_text'] ) . '" href="#">' . $republish_icon . '<span class="status-label">' . esc_html( $item['republish_option_text'] ) . '</span></a>' : $republish = '';
								} else {
									$republish = '';
								}
								if ( get_post_status() === 'publish' ) {
									'yes' === $item['display_unpublish_option'] ? $unpublish = '<a class="unpublish-button" data-postid="' . esc_attr( $post_id ) . '" data-opposite-label="' . esc_attr( $item['republish_option_text'] ) . '" href="#">' . $unpublish_icon . '<span class="status-label">' . esc_html( $item['unpublish_option_text'] ) . '</a>' : $unpublish = '';
								} else {
									$unpublish = '';
								}
								'yes' === $item['display_edit_option'] ? $edit     = '<a class="edit-post" href="' . esc_url( $edit_url ) . '">' . $edit_icon . esc_html( $item['edit_option_text'] ) . '</a>' : $edit = '';
								'yes' === $item['display_delete_option'] ? $delete = '<a class="delete-post" href="' . esc_url( get_delete_post_link( $post_id ) ) . '">' . $delete_icon . esc_html( $item['delete_option_text'] ) . '</a>' : $delete = '';
								if ( current_user_can( 'edit_post', $post_id ) && ( intval( get_post_field( 'post_author', $post_id ) ) === $current_user->ID ) ) {
									echo '<div class="edit-options elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon . $before . $edit . $republish . $unpublish . $delete . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
							}

							// WOOCOMMERCE SECTION.
							if ( class_exists( 'WooCommerce' ) ) {
								$product = wc_get_product( get_the_ID() );
								if ( $product ) {
									// Display Product Price.
									if ( 'Product Price' === $item['post_content'] ) {
										if ( $product ) {
											echo '<div class="product-price elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $product->get_price_html() . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
									}

									// Display Product Rating.
									if ( 'Product Rating' === $item['post_content'] ) {
										if ( $product ) {
											$product_rating = $product->get_average_rating();
											$stars          = '';

											for ( $i = 1; $i <= 5; $i++ ) {
												if ( $i <= floor( $product_rating ) ) {
													// Full star for every whole number in the rating.
													$stars .= "<span class='star-full'>&#9733;</span>";
												} else {
													$stars .= "<span class='star-empty'>&#9734;</span>";
												}
											}

											echo '<div class="product-rating elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $stars . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
									}

									// Display Buy Now Button.
									if ( 'Buy Now' === $item['post_content'] ) {
										if ( $product->is_type( 'variable' ) ) {
											echo '<a class="product-buy-now variable elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" href="' . esc_url( get_the_permalink() ) . '" ' . esc_attr( $new_tab ) . '>' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before ) . esc_html__( 'Choose an option', 'better-post-filter-widgets-for-elementor' ) . wp_kses_post( $after ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										if ( $product->is_type( 'simple' ) ) {
											echo '<a class="product-buy-now simple elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" href="' . esc_url( wc_get_checkout_url() . '?add-to-cart=' . get_the_ID() ) . '" ' . esc_attr( $new_tab ) . '>' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $item['product_buy_now_text'] . $after ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
									}

									// Display Product Badge.
									if ( 'Product Badge' === $item['post_content'] ) {
										if ( $item['display_on_sale'] && $product->is_on_sale() ) {
											echo '<div class="product-badge elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $item['on_sale_text'] . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										} elseif ( $item['display_new_arrival'] && $item['display_best_seller'] ) {
											$newness_days = 30;
											$created      = strtotime( $product->get_date_created() );
											if ( $product->is_featured() ) {
												echo '<div class="product-badge elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $item['best_seller_text'] . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											} elseif ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
												echo '<div class="product-badge elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $item['new_arrival_text'] . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											}
										} elseif ( ! $item['display_new_arrival'] && $item['display_best_seller'] ) {
											if ( $product->is_featured() ) {
												echo '<div class="product-badge elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $item['best_seller_text'] . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											}
										} elseif ( $item['display_new_arrival'] && ! $item['display_best_seller'] ) {
											if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
												echo '<div class="product-badge elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . BPFWE_Helper::sanitize_and_escape_svg_input( $pseudo_icon ) . wp_kses_post( $before . $item['new_arrival_text'] . $after ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											}
										}
									}
								}
							}

					endforeach;
						echo '</div>';
						echo '</' . esc_attr( $post_html_tag ) . '>';
					}
				endwhile;
				echo '</div>';

				if ( 'numbers' === $pagination || 'numbers_and_prev_next' === $pagination ) {
					$bpfwe_pagination = '';
					$total_pages      = absint( $bpfwe_query->max_num_pages );
					if ( $total_pages > 1 ) {
						list($base, $current_page) = $this->get_pagination_base_current( $settings );

						$current_page = absint( max( 1, min( $current_page, $total_pages ) ) );

						$nav_start = '<nav class="pagination ' . esc_attr( $settings['display_on_carousel'] ) . '" role="navigation" data-page="' . esc_attr( $current_page ) . '" data-max-page="' . esc_attr( $total_pages ) . '" aria-label="Pagination">';

						$bpfwe_pagination .= $nav_start;
						$pagination_args   = [
							'base'      => esc_url( $base ),
							'format'    => ( strpos( $base, '%#%' ) !== false ) ? 'page/%#%/' : '?paged=%#%',
							'current'   => $current_page,
							'total'     => $total_pages,
							'prev_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( '« prev', 'better-post-filter-widgets-for-elementor' ) : false,
							'next_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( 'next »', 'better-post-filter-widgets-for-elementor' ) : false,
						];
						$bpfwe_pagination .= paginate_links( $pagination_args );
						$bpfwe_pagination .= '</nav>';
						echo wp_kses_post( $bpfwe_pagination );
					}
				}

				if ( 'load_more' === $pagination || 'infinite' === $pagination ) {
					$bpfwe_pagination = '';
					$total_pages      = absint( $bpfwe_query->max_num_pages );

					if ( $total_pages > 1 ) {
						list($base, $current_page) = $this->get_pagination_base_current( $settings );

						$current_page = absint( max( 1, min( $current_page, $total_pages ) ) );

						$nav_start = '<nav class="pagination bpfwe-hidden" role="navigation" data-page="' . esc_attr( $current_page ) . '" data-max-page="' . esc_attr( $total_pages ) . '" aria-label="Pagination">';

						$bpfwe_pagination .= $nav_start;
						$pagination_args   = [
							'base'      => esc_url( $base ),
							'format'    => ( strpos( $base, '%#%' ) !== false ) ? 'page/%#%/' : '?paged=%#%',
							'current'   => $current_page,
							'total'     => $total_pages,
							'prev_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( '« prev', 'better-post-filter-widgets-for-elementor' ) : false,
							'next_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( 'next »', 'better-post-filter-widgets-for-elementor' ) : false,
						];
						$bpfwe_pagination .= paginate_links( $pagination_args );
						$bpfwe_pagination .= '</nav>';
						echo wp_kses_post( $bpfwe_pagination );

						if ( 'infinite' === $pagination && 'yes' !== $settings['hide_infinite_load'] ) {
							echo '
						<div class="bpfwe-infinite-scroll-preloader">
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

						if ( 'load_more' === $pagination ) {
							echo '
						<div class="elementor-button-wrapper load-more-wrapper">
							<a href="#" class="elementor-button load-more"><span class="label-load-more">' . esc_html__( 'Load More', 'better-post-filter-widgets-for-elementor' ) . '</span><span class="label-loading">' . esc_html__( 'Loading...', 'better-post-filter-widgets-for-elementor' ) . '</span></a>
						</div>
						';
						}
					}
				}

				echo '
				</div>
				</div>
				';
				if ( 'infinite' === $pagination ) {
					echo '
						<div class="e-load-more-anchor"></div>
					';
				}
			} else {
				echo '
				<div class="post-container ' . esc_attr( $skin . ' ' . $pinned_post ) . '">
					<div class="post-container-inner">
						<div class="no-post">' . esc_html( $settings['nothing_found_message'] ) . '</div>
					</div>
				</div>
				';
			}
			wp_reset_postdata();
		}

		if ( 'user' === $settings['query_type'] ) {
			$query_args = array(
				'order'        => in_array( $settings['order'], [ 'ASC', 'DESC' ], true ) ? $settings['order'] : 'DESC',
				'orderby'      => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
				'number'       => $settings['posts_per_page'],
				'paged'        => $paged,
				'role__in'     => ! empty( $settings['selected_roles'] ) ? $settings['selected_roles'] : array(),
				'role__not_in' => ! empty( $settings['excluded_roles'] ) ? $settings['excluded_roles'] : array(),
				'meta_query'   => array(
					! empty( $settings['user_meta_key'] ) ? array(
						'key'     => $settings['user_meta_key'],
						'value'   => ! empty( $settings['user_meta_value'] ) ? $settings['user_meta_value'] : '',
						'compare' => 'LIKE',
					) : array(),
				),
			);

			if ( ! empty( $settings['post_offset'] ) && 0 !== $settings['post_offset'] ) {
				$query_args['offset'] = $settings['post_offset'];
			}

			$user_query = new WP_User_Query( $query_args );

			if ( ! empty( $user_query->get_results() ) ) {
				echo '
				<div class="loader" style="display:none;"><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div></div>
				<div class="post-container ' . esc_attr( $pagination . ' ' . $skin . ' ' . $pinned_post ) . '" data-nb-column="' . esc_attr( $settings['post_slider_slides_per_view'] ) . '">
                <div class="post-container-inner">
				<div class="' . esc_attr( $class_swiper ) . '">
				';
				// Loop through the users.
				foreach ( $user_query->get_results() as $user ) {
					global $bpfwe_user_id;
					$bpfwe_user_id                               = absint( $user->ID );
					$permalink                                   = get_author_posts_url( $bpfwe_user_id );
					$new_tab                                     = '';
					$settings['external_url_new_tab'] ? $new_tab = 'target="_blank"' : $new_tab = '';

					if ( $settings['post_external_url'] ) {
						$external_url = get_post_meta( get_the_ID(), $settings['post_external_url'], true );

						if ( strpos( $settings['post_external_url'], 'http' ) !== false ) {
							$external_url = esc_url( $settings['post_external_url'] );
						}
						if ( $external_url ) {
							$permalink = $external_url;
						} elseif ( $settings['post_external_if_empty'] ) {
							$permalink = get_permalink();
							$new_tab   = '';
						} else {
							$permalink = '';
						}
					}

					echo '<div class="post-wrapper">';

					if ( 'yes' === $settings['show_featured_image'] ) {
						$image_size = $settings['featured_img_size'] ? $settings['featured_img_size'] : 'full';

						// Get the user image meta key.
						$user_img_field_key = $settings['user_img_field_key'];
						$custom_user_image  = ! empty( $user_img_field_key ) ? esc_url( get_user_meta( $bpfwe_user_id, $user_img_field_key, true ) ) : '';

						// Determine the final image URL.
						$profile_picture_url = $custom_user_image ? $custom_user_image : '';

						// Prepare escaped URLs.
						$placeholder_image_url = esc_url( plugin_dir_url( __DIR__ ) . 'assets/images/BPFWE-Placeholder-Image-' . esc_attr( $settings['img-aspect-ratio'] ) . '.png' );
						$default_image_url     = esc_url( $settings['post_default_image']['url'] );

						// Determine final image URL, falling back to placeholder if necessary.
						$final_image_url = $profile_picture_url ? $profile_picture_url : $default_image_url;
						$image_alt       = ! empty( $profile_picture_url ) ? 'User Profile Picture' : 'Profile Picture Placeholder';

						// Lazy load image.
						if ( 'yes' === $settings['img_equal_height'] ) {
							if ( $lazy_load ) {
								$image = '<img class="swiper-lazy" data-background="' . $final_image_url . '" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $final_image_url . '" alt="' . esc_attr( $image_alt ) . '"/><div class="swiper-lazy-preloader"></div>';
							} else {
								$image = '<img style="background-image: url(' . $final_image_url . ')" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $final_image_url . '" alt="' . esc_attr( $image_alt ) . '"/>';
							}
						} elseif ( $lazy_load ) {
								$image = '<img class="swiper-lazy" data-src="' . $final_image_url . '" data-bpfwe-src="' . $final_image_url . '" alt="' . esc_attr( $image_alt ) . '"/><div class="swiper-lazy-preloader"></div>';
						} else {
							$image = '<img src="' . $final_image_url . '" data-bpfwe-src="' . $final_image_url . '" alt="' . esc_attr( $image_alt ) . '"/>';
						}

						// Output HTML with escaped values.
						if ( $settings['post_image_url'] && ! empty( $permalink ) ) {
							echo '<div class="post-image"><a href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . wp_kses_post( $image . $overlay ) . '</a></div>';
						} else {
							echo '<div class="post-image">' . wp_kses_post( $image . $overlay ) . '</div>';
						}
					}

					echo '<div class="inner-content">';
					foreach ( $settings['user_list'] as $index => $item ) :

						// WordPress Username.
						if ( 'Username' === $item['post_content'] ) {
							if ( $item['display_name_url'] && ! empty( $permalink ) ) {
								echo '<' . esc_attr( $settings['html_tag'] ) . ' class="user-username elementor-repeater-item-' . esc_attr( $item['_id'] ) . '"><a href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . esc_html( $user->user_login ) . '</a></' . esc_attr( $settings['html_tag'] ) . '>';
							} else {
								echo '<' . esc_attr( $settings['html_tag'] ) . ' class="user-username elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . esc_html( $user->user_login ) . '</' . esc_attr( $settings['html_tag'] ) . '>';
							}
						}

						// Display Name.
						if ( 'Display Name' === $item['post_content'] ) {
							if ( $item['display_name_url'] && ! empty( $permalink ) ) {
								echo '<' . esc_attr( $settings['html_tag'] ) . ' class="user-display-name elementor-repeater-item-' . esc_attr( $item['_id'] ) . '"><a href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . esc_html( $user->display_name ) . '</a></' . esc_attr( $settings['html_tag'] ) . '>';
							} else {
								echo '<' . esc_attr( $settings['html_tag'] ) . ' class="user-display-name elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . esc_html( $user->display_name ) . '</' . esc_attr( $settings['html_tag'] ) . '>';
							}
						}

						// Display Full Name.
						if ( 'Full Name' === $item['post_content'] ) {
							if ( $item['display_name_url'] && ! empty( $permalink ) ) {
								echo '<' . esc_attr( $settings['html_tag'] ) . ' class="user-full-name elementor-repeater-item-' . esc_attr( $item['_id'] ) . '"><a href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . esc_html( $user->first_name . ' ' . $user->last_name ) . '</a></' . esc_attr( $settings['html_tag'] ) . '>';
							} else {
								echo '<' . esc_attr( $settings['html_tag'] ) . ' class="user-full-name elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . esc_html( $user->first_name . ' ' . $user->last_name ) . '</' . esc_attr( $settings['html_tag'] ) . '>';
							}
						}

						// Display User Meta.
						if ( 'User Meta' === $item['post_content'] ) {
							$user_field_key   = sanitize_key( $item['user_field_key'] );
							$custom_field_val = BPFWE_Helper::is_acf_field( $user_field_key ) ? get_field( $user_field_key, 'user_' . $bpfwe_user_id ) : get_user_meta( $bpfwe_user_id, $user_field_key, true );

							if ( $custom_field_val && ! empty( $item['user_field_key'] ) ) {
								echo '<div class="post-custom-field elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . wp_kses_post( $custom_field_val ) . '</div>';
							}
						}

						// Display Email.
						if ( 'User Email' === $item['post_content'] ) {
							echo '<div class="user-email elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . esc_url( $user->user_email ) . '</div>';
						}

						// Display User Role.
						if ( 'User Role' === $item['post_content'] ) {
							echo '<div class="user-role elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' .
								esc_html( implode( ', ', array_map( 'ucwords', $user->roles ) ) ) .
								'</div>';
						}

						// Display User ID.
						if ( 'User ID' === $item['post_content'] ) {
							echo '<div class="user-id elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . absint( $bpfwe_user_id ) . '</div>';
						}

						// Display Visit Profile.
						if ( 'Visit Profile' === $item['post_content'] ) {
							if ( ! empty( $permalink ) ) {
								echo '<a class="visit-profile elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . esc_html( $item['visit_profile_text'] ) . '</a>';
							} else {
								echo '<span class="visit-profile elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['visit_profile_text'] ) . '</span>';
							}
						}

						// Display HTML with Shortcode Support.
						if ( 'HTML' === $item['post_content'] ) {
							$content = $before . $item['user_html'] . $after;
							$content = do_shortcode( $content );

							echo '<div class="post-html elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . wp_kses_post( $content ) . '</div>';
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
				if ( 'numbers' === $pagination || 'numbers_and_prev_next' === $pagination ) {
					$bpfwe_pagination = '';
					$total_users      = $user_query->get_total();

					if ( $total_users > 1 ) {
						list($base, $current_page) = $this->get_pagination_base_current( $settings );

						$current_page = absint( max( 1, min( $current_page, ceil( $total_users / $settings['posts_per_page'] ) ) ) );

						$nav_start = '<nav class="pagination ' . esc_attr( $settings['display_on_carousel'] ) . '" role="navigation" data-page="' . esc_attr( $current_page ) . '" data-max-page="' . esc_attr( ceil( $total_users / $settings['posts_per_page'] ) ) . '" aria-label="Pagination">';

						$bpfwe_pagination .= $nav_start;
						$pagination_args   = [
							'base'      => esc_url( $base ),
							'current'   => $current_page,
							'total'     => ceil( $total_users / $settings['posts_per_page'] ),
							'prev_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( '« prev', 'better-post-filter-widgets-for-elementor' ) : false,
							'next_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( 'next »', 'better-post-filter-widgets-for-elementor' ) : false,
						];
						$bpfwe_pagination .= paginate_links( $pagination_args );
						$bpfwe_pagination .= '</nav>';
						echo wp_kses_post( $bpfwe_pagination );
					}
				}
				if ( 'load_more' === $pagination || 'infinite' === $pagination ) {
					$bpfwe_pagination = '';
					$total_users      = absint( $user_query->get_total() );

					if ( $total_users > 1 ) {
						list($base, $current_page) = $this->get_pagination_base_current( $settings );

						$current_page = absint( max( 1, min( $current_page, ceil( $total_users / $settings['posts_per_page'] ) ) ) );

						$nav_start = '<nav class="pagination bpfwe-hidden" role="navigation" data-page="' . esc_attr( $current_page ) . '" data-max-page="' . esc_attr( ceil( $total_users / $settings['posts_per_page'] ) ) . '" aria-label="Pagination">';

						$bpfwe_pagination .= $nav_start;
						$pagination_args   = [
							'base'      => esc_url( $base ),
							'current'   => $current_page,
							'total'     => ceil( $total_users / $settings['posts_per_page'] ),
							'prev_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( '« prev', 'better-post-filter-widgets-for-elementor' ) : false,
							'next_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( 'next »', 'better-post-filter-widgets-for-elementor' ) : false,
						];
						$bpfwe_pagination .= paginate_links( $pagination_args );
						$bpfwe_pagination .= '</nav>';
						echo wp_kses_post( $bpfwe_pagination );

						if ( 'infinite' === $pagination && 'yes' !== $settings['hide_infinite_load'] ) {
							echo '
						<div class="bpfwe-infinite-scroll-preloader">
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

						if ( 'load_more' === $pagination ) {
							echo '
						<div class="elementor-button-wrapper load-more-wrapper">
							<a href="#" class="elementor-button load-more"><span class="label-load-more">' . esc_html__( 'Load More', 'better-post-filter-widgets-for-elementor' ) . '</span><span class="label-loading">' . esc_html__( 'Loading...', 'better-post-filter-widgets-for-elementor' ) . '</span></a>
						</div>
						';
						}
					}
				}
				echo '
				</div>
				</div>
				';
				if ( 'infinite' === $pagination ) {
					echo '
						<div class="e-load-more-anchor"></div>
					';
				}
			} else {
				echo '
				<div class="post-container ' . esc_attr( $skin . ' ' . $pinned_post ) . '">
					<div class="post-container-inner">
						<div class="no-post">' . esc_html( $settings['nothing_found_message'] ) . '</div>
					</div>
				</div>
				';
			}
		}

		if ( 'taxonomy' === $settings['query_type'] ) {
			$offset     = ( $paged - 1 ) * $settings['posts_per_page'];
			$query_args = [];

			$query_args = array(
				'order'      => in_array( $settings['order'], [ 'ASC', 'DESC' ], true ) ? $settings['order'] : 'DESC',
				'orderby'    => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'name',
				'number'     => $settings['posts_per_page'],
				'offset'     => $offset,
				'hide_empty' => ( 'yes' === $settings['add_empty_terms'] ) ? false : true,
				'taxonomy'   => ! empty( $settings['select_taxonomy'] ) ? $settings['select_taxonomy'] : '',
			);

			if ( 'top_level' === $settings['filter_rule'] ) {
				$query_args['parent'] = 0;
			} elseif ( 'child' === $settings['filter_rule'] ) {
				$query_args['parent']  = '';
				$query_args['exclude'] = get_terms(
					[
						'taxonomy'   => $settings['select_taxonomy'],
						'parent'     => 0,
						'fields'     => 'ids',
						'hide_empty' => false,
					]
				);
			}

			if ( ! empty( $settings['select_taxonomy'] ) ) {
				$selected_taxonomies = (array) $settings['select_taxonomy'];

				foreach ( $selected_taxonomies as $index ) {
					$filter_type_key = $index . '_filter_type';
					$term_ids_key    = $index;

					if ( ! empty( $settings[ $term_ids_key ] ) ) {
						$term_ids = is_array( $settings[ $term_ids_key ] ) ? $settings[ $term_ids_key ] : explode( ',', $settings[ $term_ids_key ] );
						$operator = ( isset( $settings[ $filter_type_key ] ) && 'NOT IN' === $settings[ $filter_type_key ] ) ? 'NOT IN' : 'IN';

						if ( 'NOT IN' === $operator ) {
							$query_args['exclude'] = $term_ids;
						} else {
							$query_args['include'] = $term_ids;
						}
					}
				}
			}

			if ( ! empty( $settings['post_offset'] ) && 0 !== $settings['post_offset'] ) {
				$query_args['offset'] = $settings['post_offset'];
			}

			$taxonomy_query = new WP_Term_Query( $query_args );
			$terms          = $taxonomy_query->get_terms();

			if ( ! empty( $terms ) ) {
				echo '
				<div class="loader" style="display:none;"><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div><div class="loader-square"></div></div>
				<div class="post-container ' . esc_attr( $pagination . ' ' . $skin . ' ' . $pinned_post ) . '" data-nb-column="' . esc_attr( $settings['post_slider_slides_per_view'] ) . '">
                <div class="post-container-inner">
				<div class="' . esc_attr( $class_swiper ) . '">
				';
				// Loop through the terms.
				foreach ( $terms as $term ) {
					global $bpfwe_term_id;
					$bpfwe_term_id                               = absint( $term->term_id );
					$permalink                                   = get_term_link( $bpfwe_term_id );
					$term_name                                   = $term->name;
					$term_description                            = $term->description;
					$new_tab                                     = '';
					$settings['external_url_new_tab'] ? $new_tab = 'target="_blank"' : $new_tab = '';

					if ( $settings['post_external_url'] ) {
						$external_url = get_post_meta( get_the_ID(), $settings['post_external_url'], true );

						if ( strpos( $settings['post_external_url'], 'http' ) !== false ) {
							$external_url = esc_url( $settings['post_external_url'] );
						}
						if ( $external_url ) {
							$permalink = $external_url;
						} elseif ( $settings['post_external_if_empty'] ) {
							$permalink = get_permalink();
							$new_tab   = '';
						} else {
							$permalink = '';
						}
					}

					echo '<div class="post-wrapper">';

					if ( 'yes' === $settings['show_featured_image'] && 'taxonomy' === $settings['query_type'] ) {
						$image_size = ! empty( $settings['featured_img_size'] ) ? esc_attr( $settings['featured_img_size'] ) : 'full';

						// Get the taxonomy image meta key.
						$term_img_field_key = $settings['term_img_field_key'];
						$term_image_url     = '';

						if ( class_exists( 'WooCommerce' ) && 'product_cat' === $settings['select_taxonomy'] ) {
							$thumbnail_id = get_term_meta( $bpfwe_term_id, 'thumbnail_id', true );
							if ( $thumbnail_id ) {
								$term_image_url = esc_url( wp_get_attachment_url( $thumbnail_id ) );
							}
						} else {
							$term_image_url = ! empty( $term_img_field_key ) ? esc_url( get_term_meta( $bpfwe_term_id, $term_img_field_key, true ) ) : '';
						}

						// Prepare escaped URLs.
						$placeholder_image_url = esc_url( plugin_dir_url( __DIR__ ) . 'assets/images/BPFWE-Placeholder-Image-' . esc_attr( $settings['img-aspect-ratio'] ) . '.png' );
						$default_image_url     = esc_url( $settings['post_default_image']['url'] );
						$image_alt             = ! empty( $term_image_url ) ? esc_attr( 'Taxonomy Image' ) : esc_attr( 'Default Taxonomy Image' );

						// Lazy load image.
						if ( 'yes' === $settings['img_equal_height'] ) {
							if ( $lazy_load ) {
								$image = '<img class="swiper-lazy" data-background="' . $term_image_url . '" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $term_image_url . '" alt="' . $image_alt . '"/><div class="swiper-lazy-preloader"></div>';
							} else {
								$image = '<img style="background-image: url(' . $term_image_url . ')" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $term_image_url . '" alt="' . $image_alt . '"/>';
							}
							if ( ! $term_image_url ) {
								if ( $lazy_load ) {
									$image = '<img class="swiper-lazy" data-background="' . $default_image_url . '" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $default_image_url . '" alt="' . $image_alt . '"/><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = '<img style="background-image: url(' . $default_image_url . ')" src="' . $placeholder_image_url . '" data-bpfwe-src="' . $default_image_url . '" alt="' . $image_alt . '"/>';
								}
							}
						} else {
							if ( $lazy_load ) {
								$image = '<img class="swiper-lazy" data-src="' . $term_image_url . '" data-bpfwe-src="' . $term_image_url . '" alt="' . $image_alt . '"/><div class="swiper-lazy-preloader"></div>';
							} else {
								$image = '<img src="' . $term_image_url . '" data-bpfwe-src="' . $term_image_url . '" alt="' . $image_alt . '"/>';
							}
							if ( ! $term_image_url ) {
								if ( $lazy_load ) {
									$image = '<img class="swiper-lazy" data-src="' . $default_image_url . '" data-bpfwe-src="' . $default_image_url . '" alt="' . $image_alt . '"/><div class="swiper-lazy-preloader"></div>';
								} else {
									$image = '<img src="' . $default_image_url . '" data-bpfwe-src="' . $default_image_url . '" alt="' . $image_alt . '"/>';
								}
							}
						}

						// Output HTML with escaped values.
						if ( $settings['post_image_url'] && ! empty( $permalink ) ) {
							echo '<div class="post-image"><a href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . wp_kses_post( $image . $overlay ) . '</a></div>';
						} else {
							echo '<div class="post-image">' . wp_kses_post( $image . $overlay ) . '</div>';
						}
					}

					echo '<div class="inner-content">';
					foreach ( $settings['taxonomy_list'] as $index => $item ) :

						// Term Label.
						if ( 'Term Label' === $item['post_content'] ) {
							if ( $item['term_url'] && ! empty( $permalink ) ) {
								echo '<' . esc_attr( $settings['html_tag'] ) . ' class="term-label elementor-repeater-item-' . esc_attr( $item['_id'] ) . '"><a href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . esc_html( $term_name ) . '</a></' . esc_attr( $settings['html_tag'] ) . '>';
							} else {
								echo '<' . esc_attr( $settings['html_tag'] ) . ' class="term-label elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . esc_html( $term_name ) . '</' . esc_attr( $settings['html_tag'] ) . '>';
							}
						}

						// Term Description.
						if ( 'Term Description' === $item['post_content'] ) {
							$term_description = term_description( $term->term_id );
							if ( $term_description ) {
								echo '<div class="term-description elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . wp_kses_post( $term_description ) . '</div>';
							}
						}

						// Term Count.
						if ( 'Term Count' === $item['post_content'] ) {
							$display_count = absint( $term->count );

							if ( $term->count <= 1 ) {
								$display_count = absint( $term->count ) . ' ' . $item['count_singular'];
							} else {
								$display_count = absint( $term->count ) . ' ' . $item['count_plurial'];
							}

							echo '<div class="term-count elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . esc_html( $display_count ) . '</div>';
						}

						// Display Term Meta.
						if ( 'Term Meta' === $item['post_content'] ) {
							$term_field_key   = sanitize_key( $item['term_field_key'] );
							$custom_field_val = BPFWE_Helper::is_acf_field( $term_field_key ) ? get_field( $term_field_key, $term ) : get_term_meta( $term->term_id, $term_field_key, true );

							if ( $custom_field_val && ! empty( $item['term_field_key'] ) ) {
								echo '<div class="post-custom-field elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . wp_kses_post( $custom_field_val ) . '</div>';
							}
						}

						// Term ID.
						if ( 'Term ID' === $item['post_content'] ) {
							echo '<div class="term-id elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . absint( $term->term_id ) . '</div>';
						}

						// Term URL.
						if ( 'Term URL' === $item['post_content'] ) {
							if ( ! empty( $permalink ) ) {
								echo '<a class="term-url elementor-repeater-item-' . esc_attr( $item['_id'] ) . '" href="' . esc_url( $permalink ) . '" ' . esc_attr( $new_tab ) . '>' . esc_html( $item['term_read_more_text'] ) . '</a>';
							} else {
								echo '<span class="term-url elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . esc_html( $item['term_read_more_text'] ) . '</span>';
							}
						}

						// Display HTML with Shortcode Support.
						if ( 'HTML' === $item['post_content'] ) {
							$content = $before . $item['term_html'] . $after;
							$content = do_shortcode( $content );

							echo '<div class="post-html elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . wp_kses_post( $content ) . '</div>';
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
				if ( 'numbers' === $pagination || 'numbers_and_prev_next' === $pagination ) {
					$bpfwe_pagination = '';
					$parent_condition = ( 'top_level' === $settings['filter_rule'] ) ? 0 : '';

					$total_terms = wp_count_terms( $settings['select_taxonomy'] );

					if ( $total_terms > 1 ) {
						list($base, $current_page) = $this->get_pagination_base_current( $settings );

						$current_page = absint( max( 1, min( $current_page, ceil( $total_terms / $settings['posts_per_page'] ) ) ) );

						$nav_start = '<nav class="pagination ' . esc_attr( $settings['display_on_carousel'] ) . '" role="navigation" data-page="' . esc_attr( $current_page ) . '" data-max-page="' . esc_attr( ceil( $total_terms / $settings['posts_per_page'] ) ) . '" aria-label="Pagination">';

						$bpfwe_pagination .= $nav_start;

						$pagination_args = [
							'base'      => esc_url( $base ),
							'current'   => $paged,
							'total'     => ceil( $total_terms / $settings['posts_per_page'] ),
							'prev_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( '« prev', 'better-post-filter-widgets-for-elementor' ) : false,
							'next_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( 'next »', 'better-post-filter-widgets-for-elementor' ) : false,
						];

						$bpfwe_pagination .= paginate_links( $pagination_args );
						$bpfwe_pagination .= '</nav>';
						echo wp_kses_post( $bpfwe_pagination );
					}
				}
				if ( 'load_more' === $pagination || 'infinite' === $pagination ) {
					$bpfwe_pagination = '';
					$parent_condition = ( 'top_level' === $settings['filter_rule'] ) ? 0 : '';

					$total_terms = wp_count_terms( $settings['select_taxonomy'] );

					if ( $total_terms > 1 ) {
						list($base, $current_page) = $this->get_pagination_base_current( $settings );

						$current_page = absint( max( 1, min( $current_page, ceil( $total_terms / $settings['posts_per_page'] ) ) ) );

						$nav_start = '<nav class="pagination bpfwe-hidden" role="navigation" data-page="' . esc_attr( $current_page ) . '" data-max-page="' . esc_attr( ceil( $total_terms / $settings['posts_per_page'] ) ) . '" aria-label="Pagination">';

						$bpfwe_pagination .= $nav_start;

						$pagination_args = [
							'base'      => esc_url( $base ),
							'current'   => $paged,
							'total'     => ceil( $total_terms / $settings['posts_per_page'] ),
							'prev_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( '« prev', 'better-post-filter-widgets-for-elementor' ) : false,
							'next_text' => ( 'numbers_and_prev_next' === $pagination ) ? esc_html__( 'next »', 'better-post-filter-widgets-for-elementor' ) : false,
						];

						$bpfwe_pagination .= paginate_links( $pagination_args );
						$bpfwe_pagination .= '</nav>';
						echo wp_kses_post( $bpfwe_pagination );

						if ( 'infinite' === $pagination && 'yes' !== $settings['hide_infinite_load'] ) {
							echo '
						<div class="bpfwe-infinite-scroll-preloader">
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

						if ( 'load_more' === $pagination ) {
							echo '
						<div class="elementor-button-wrapper load-more-wrapper">
							<a href="#" class="elementor-button load-more"><span class="label-load-more">' . esc_html__( 'Load More', 'better-post-filter-widgets-for-elementor' ) . '</span><span class="label-loading">' . esc_html__( 'Loading...', 'better-post-filter-widgets-for-elementor' ) . '</span></a>
						</div>
						';
						}
					}
				}
				echo '
				</div>
				</div>
				';
				if ( 'infinite' === $pagination ) {
					echo '
						<div class="e-load-more-anchor"></div>
					';
				}
			} else {
				echo '
				<div class="post-container ' . esc_attr( $skin . ' ' . $pinned_post ) . '">
					<div class="post-container-inner">
						<div class="no-post">' . esc_html( $settings['nothing_found_message'] ) . '</div>
					</div>
				</div>
				';
			}
		}
	}
}

<?php
/**
 * Posts Found Widget.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * BPF_Posts_Found_Widget class
 *
 * Provides the functionality for the Posts Found widget in Elementor.
 * This widget displays the number of posts found according to the query.
 *
 * @since 1.0.0
 */
class BPF_Posts_Found_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Posts Found Widget widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'posts-found-widget';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Posts Found Widget widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Posts Found', 'bpf-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Posts Found Widget widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-counter';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Posts Found Widget widget belongs to.
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
	 * Get widget styles dependencies.
	 *
	 * Retrieve the list of stylesheets that the widget depends on.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array List of style dependencies.
	 */
	public function get_style_depends() {
		return [
			'bpf-widget-style',
		];
	}

	/**
	 * Get widget scripts dependencies.
	 *
	 * Retrieve the list of scripts that the widget depends on.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array List of script dependencies.
	 */
	public function get_script_depends() {
		return [
			'filter-widget-script',
		];
	}

	/**
	 * Register Posts Found Widget widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_container_style',
			[
				'label' => esc_html__( 'Posts Found', 'bpf-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'post_found_color',
			array(
				'label'     => __( 'Color', 'bpf-widget' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .filter-post-count' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'post_found_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .filter-post-count',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Outputs the Posts Found widget content, including the post count.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings   = $this->get_settings_for_display();
		$post_count = 0;

		$count_text = sprintf(
			// translators: %s is the number of results.
			esc_html__( '%s result(s) found', 'bpf-widget' ),
			'<span class="number">' . number_format_i18n( $post_count ) . '</span>'
		);

		echo '<div class="filter-post-count">' . wp_kses_post( $count_text ) . '</div>';
	}
}

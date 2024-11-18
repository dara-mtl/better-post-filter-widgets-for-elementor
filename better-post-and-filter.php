<?php
/**
Plugin Name: Better Post & Filter Widgets for Elementor
Description: Post and filter widgets for Elementor.
Plugin URI: https://cambodiawebmaster.com/
Author: WP Smart Widgets
Version: 1.0.3
Elementor tested up to: 3.25.4
Author URI: https://wpsmartwidgets.com/
Text Domain: bpf-widget
Domain Path: /lang
License: GPL v3.0
GitHub Plugin URI: https://github.com/dara-mtl/better-post-and-filter-widgets-for-elementor
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Add widget categories
require_once plugin_dir_path( __FILE__ ) . 'widget-categories.php';

/**
 * Main BPF Elementor Widgets Class
 *
 * @since 1.0.0
 */
final class BPF_Elementor {
	const VERSION = '1.0.3';
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
	const MINIMUM_PHP_VERSION = '7.2';
	private static $_instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return BPF_Elementor Instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * BPF_Elementor constructor.
	 */
	public function __construct() {
		require_once plugin_dir_path( __FILE__ ) . 'inc/pagination-var.php';
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}

	/**
	 * Load plugin textdomain.
	 */
	public function i18n() {
		load_plugin_textdomain( 'bpf-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Fires after all plugins are loaded.
	 */
	public function on_plugins_loaded() {
		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
		}
	}

	/**
	 * Compatibility checks.
	 */
	public function is_compatible() {
		// Check if Elementor is installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		$this->i18n();
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'backend_widget_styles' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'backend_widget_scripts' ] );

		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/helper-class.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/bpf-dynamic-tag.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-ajax.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-dynamic-group.php';
	}

	/**
	 * Enqueue frontend styles.
	 */
	public function widget_styles() {
		$swiper_version = get_option( 'elementor_experiment-e_swiper_latest' ) === 'active' ? '8.4.5' : '5.3.6';
		$swiper_path = ELEMENTOR_ASSETS_URL . 'lib/swiper/' . ( $swiper_version === '8.4.5' ? 'v8' : '' ) . '/css/swiper.min.css';
		
		wp_enqueue_style( 'swiper', $swiper_path, [], $swiper_version );
		wp_enqueue_style( 'bpf-select2-style', ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2.min.css' );
		wp_enqueue_style( 'bpf-widget-style', plugins_url( 'assets/css/elementor-cwm-widget.css', __FILE__ ), [], self::VERSION );
	}

	/**
	 * Enqueue backend styles.
	 */
	public function backend_widget_styles() {
		wp_enqueue_style( 'post-editor-style', plugins_url( 'assets/css/backend/post-widget-editor.css', __FILE__ ), [], self::VERSION );
	}

	/**
	 * Enqueue frontend scripts.
	 */
	public function widget_scripts() {
		$swiper_version = get_option( 'elementor_experiment-e_swiper_latest' ) === 'active' ? '8.4.5' : '5.3.6';
		$swiper_path = ELEMENTOR_ASSETS_URL . 'lib/swiper/' . ( $swiper_version === '8.4.5' ? 'v8' : '' ) . '/swiper.min.js';
		
		wp_register_script( 'swiper', $swiper_path, [], $swiper_version, true );
		wp_register_script( 'bpf-select2-script', ELEMENTOR_ASSETS_URL . 'lib/e-select2/js/e-select2.full.min.js', [ 'jquery' ], null, true );
		
		// Localize and enqueue plugin scripts
		$ajax_params = [ 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'ajax-nonce' ) ];
		
		wp_register_script( 'post-widget-script', plugins_url( 'assets/js/cwm-post-widget.js', __FILE__ ), [ 'jquery' ], self::VERSION, true );
		wp_localize_script( 'post-widget-script', 'ajax_var', $ajax_params );

		wp_register_script( 'filter-widget-script', plugins_url( 'assets/js/cwm-filter-widget.js', __FILE__ ), [ 'jquery' ], self::VERSION, true );
		wp_localize_script( 'filter-widget-script', 'ajax_var', $ajax_params );
	}

	/**
	 * Enqueue backend scripts.
	 */
	public function backend_widget_scripts() {
		wp_enqueue_script( 'post-editor-script', plugins_url( 'assets/js/backend/post-widget-editor.js', __FILE__ ), [], self::VERSION, true );
	}

	/**
	 * Register widgets.
	 */
	public function init_widgets() {
		require_once plugin_dir_path( __FILE__ ) . 'widgets/post-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/filter-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/filter-sorting.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/filter-posts-found.php';
		
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->register( new \BPF_Post_Widget() );
		$widgets_manager->register( new \BPF_Filter_Widget() );
		$widgets_manager->register( new \BPF_Sorting_Widget() );
		$widgets_manager->register( new \BPF_Posts_Found_Widget() );
	}
}

BPF_Elementor::instance();
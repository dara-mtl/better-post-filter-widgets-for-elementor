<?php
/**
 * Plugin Name: Better Post & Filter Widgets for Elementor
 * Requires Plugins: elementor
 * Description: Post and filter widgets for Elementor, designed for flexibility and performance.
 * Plugin URI: https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/
 * Author: WP Smart Widgets
 * Author URI: https://wpsmartwidgets.com/
 * Documentation URI: https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/
 * Version: 1.2.4
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Tested up to: 6.8
 * Elementor tested up to: 3.28.3
 * Text Domain: better-post-filter-widgets-for-elementor
 * Domain Path: /lang
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * GitHub Plugin URI: https://github.com/dara-mtl/better-post-and-filter-widgets-for-elementor
 *
 * @package BPFWE_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Add widget categories.
require_once plugin_dir_path( __FILE__ ) . 'widget-categories.php';

/**
 * Main BPFWE Elementor Widgets Class
 *
 * @since 1.0.0
 */
final class BPFWE_Elementor {
	const VERSION                   = '1.2.4';
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
	const MINIMUM_PHP_VERSION       = '7.4';

	/**
	 * Holds the single instance of the class.
	 *
	 * @since 1.0.0
	 * @var BPFWE_Elementor|null
	 */
	private static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return BPFWE_Elementor Instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * BPFWE_Elementor constructor.
	 */
	public function __construct() {
		require_once plugin_dir_path( __FILE__ ) . 'inc/query-var.php';
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
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
		// Check if Elementor is installed and activated.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check PHP version.
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
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'backend_widget_styles' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'backend_widget_scripts' ] );
		add_action( 'admin_enqueue_scripts', array( $this, 'bpfwe_swatches_scripts' ) );

		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-bpfwe-taxonomy-swatches.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-bpfwe-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-bpfwe-dynamic-tag.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-bpfwe-ajax.php';
	}

	/**
	 * Enqueue frontend styles.
	 */
	public function widget_styles() {
		// Define the Elementor assets base URL and directory.
		$elementor_assets_url = plugins_url( '/elementor/assets/lib/swiper/', WP_PLUGIN_DIR . '/elementor/' );
		$elementor_assets_dir = plugin_dir_path( WP_PLUGIN_DIR . '/elementor/' ) . 'assets/lib/swiper/';

		// Define paths for Swiper CSS files.
		$swiper_v8_path      = $elementor_assets_url . 'v8/css/swiper.min.css';
		$swiper_default_path = $elementor_assets_url . 'css/swiper.min.css';

		// Check for the existence of files dynamically.
		$swiper_css_path = file_exists( $elementor_assets_dir . 'v8/css/swiper.min.css' )
			? $swiper_v8_path
			: $swiper_default_path;

		// Enqueue Swiper CSS.
		wp_enqueue_style( 'swiper', $swiper_css_path, [], '8.4.5' );

		// Enqueue Select2 CSS.
		wp_enqueue_style( 'bpfwe-select2-style', plugins_url( 'elementor/assets/lib/e-select2/css/e-select2.min.css', WP_PLUGIN_DIR . '/elementor/' ), [], ELEMENTOR_VERSION );

		// Enqueue custom widget styles.
		wp_enqueue_style( 'bpfwe-widget-style', plugins_url( 'assets/css/bpfwe-widget.min.css', __FILE__ ), [], self::VERSION );
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
		// Determine paths dynamically using plugins_url() and plugin_dir_path().
		$elementor_assets_url = plugins_url( '/elementor/assets/lib/swiper/', WP_PLUGIN_DIR . '/elementor/' );
		$elementor_assets_dir = plugin_dir_path( WP_PLUGIN_DIR . '/elementor/' ) . 'assets/lib/swiper/';

		// Define paths for Swiper JS files.
		$swiper_v8_path      = $elementor_assets_url . 'v8/swiper.min.js';
		$swiper_default_path = $elementor_assets_url . 'swiper.min.js';

		// Check for the existence of files dynamically using plugin_dir_path().
		$swiper_path = file_exists( $elementor_assets_dir . 'v8/swiper.min.js' )
			? $swiper_v8_path
			: $swiper_default_path;

		// Register scripts.
		wp_register_script( 'swiper', $swiper_path, [], '8.4.5', true );

		wp_register_script( 'bpfwe-select2-script', plugins_url( 'elementor/assets/lib/e-select2/js/e-select2.full.min.js', WP_PLUGIN_DIR . '/elementor/' ), [ 'jquery' ], ELEMENTOR_VERSION, true );

		// Localize and enqueue plugin scripts.
		$ajax_params = [
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'ajax-nonce' ),
		];

		wp_register_script( 'post-widget-script', plugins_url( 'assets/js/bpfwe-post-widget.min.js', __FILE__ ), [ 'jquery' ], self::VERSION, true );
		wp_localize_script( 'post-widget-script', 'ajax_var', $ajax_params );

		wp_register_script( 'filter-widget-script', plugins_url( 'assets/js/bpfwe-filter-widget.min.js', __FILE__ ), [ 'jquery' ], self::VERSION, true );
		wp_localize_script( 'filter-widget-script', 'ajax_var', $ajax_params );
	}

	/**
	 * Enqueue backend scripts.
	 */
	public function backend_widget_scripts() {
		wp_enqueue_script( 'post-editor-script', plugins_url( 'assets/js/backend/post-widget-editor.js', __FILE__ ), [], self::VERSION, true );
	}

	/**
	 * Enqueue backend scripts for the taxonomy swatches.
	 */
	public function bpfwe_swatches_scripts() {
		$screen = get_current_screen();
		if ( $screen && ( strpos( $screen->id, 'edit-pa_' ) === 0 || in_array( $screen->base, [ 'term', 'edit-tags', 'product_page_product_attributes' ], true ) ) ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_media();
			wp_enqueue_script( 'taxonomy-editor-scripts', plugins_url( 'assets/js/backend/taxonomy-editor.js', __FILE__ ), [ 'jquery', 'wp-color-picker' ], self::VERSION, true );
		}
	}

	/**
	 * Register widgets.
	 */
	public function init_widgets() {
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-bpfwe-post-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-bpfwe-filter-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-bpfwe-search-bar-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-bpfwe-sorting-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'widgets/class-bpfwe-posts-found-widget.php';

		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->register( new \BPFWE_Post_Widget() );
		$widgets_manager->register( new \BPFWE_Filter_Widget() );
		$widgets_manager->register( new \BPFWE_Search_Bar_Widget() );
		$widgets_manager->register( new \BPFWE_Sorting_Widget() );
		$widgets_manager->register( new \BPFWE_Posts_Found_Widget() );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'better-post-filter-widgets-for-elementor' ),
			'<strong>' . esc_html__( 'Better Post and Filter Widgets for Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'better-post-filter-widgets-for-elementor' ),
			'<strong>' . esc_html__( 'Better Post and Filter Widgets for Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'better-post-filter-widgets-for-elementor' ),
			'<strong>' . esc_html__( 'Better Post and Filter Widgets for Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}
}

BPFWE_Elementor::instance();

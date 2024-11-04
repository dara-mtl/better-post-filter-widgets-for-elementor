<?php
/*
Plugin Name: Better Post & Filter Widgets for Elementor
Description: Post & filter widgets for Elementor.
Plugin URI: https://cambodiawebmaster.com/
Author: CWM
Version: 1.0.1
Elementor tested up to: 3.25.4
Author URI: https://cambodiawebmaster.com/
Text Domain: bpf-widget
Domain Path: /lang
License: GNU General Public License v3.0
GitHub Plugin URI: dara-mtl/better-post-and-filter-widgets-for-elementor
GitHub Plugin URI: https://github.com/dara-mtl/better-post-and-filter-widgets-for-elementor
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Add widget categories
include('widget-categories.php');

/**
 * Main Elementor CWM Elementor Widgets Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class BPF_Elementor {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.2';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var Custom_Elementor_Widget The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Custom_Elementor_Widget An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		
		// Include the file with custom query variable registration
		require_once( __DIR__ . '/inc/pagination-var.php' );
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
		
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

		load_plugin_textdomain( 'bpf-widget' );

	}

	/**
	 * On Plugins Loaded
	 *
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_plugins_loaded() {

		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
		}

	}

	/**
	 * Compatibility Checks
	 *
	 * Checks if the installed version of Elementor meets the plugin's minimum requirement.
	 * Checks if the installed PHP version meets the plugin's minimum requirement.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function is_compatible() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;

	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	
	public function init() {
		$this->i18n();

		// Add Plugin actions
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );
		
		// Register Widget Styles
        add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
        
        // Register Widget Scripts
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'widget_scripts' ] );
		
		//Register Scripts & Styles for the Elementor Editor
		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'backend_widget_styles' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'backend_widget_scripts' ] );

		include_once( 'inc/classes/helper-class.php' );
		//include_once( 'bpf-dynamic-tag.php' );
		include_once( 'inc/classes/ajax-class.php' );

    }
    
    /**
	 * Init Styles
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
    public function widget_styles() {
		
		$upgrade_swiper = get_option('elementor_experiment-e_swiper_latest');

		if ($upgrade_swiper == 'active') {
			wp_enqueue_style( 'swiper', ELEMENTOR_ASSETS_URL . 'lib/swiper/v8/css/swiper.min.css', '8.4.5' );
		} else {
			wp_enqueue_style( 'swiper', ELEMENTOR_ASSETS_URL . 'lib/swiper/css/swiper.min.css' );
		}
		
		wp_enqueue_style( 'bpf-select2-style', ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2.min.css' );
		
		//Enqueue Plugin Assets
        wp_enqueue_style( 'bpf-widget-style', plugins_url( 'assets/css/elementor-cwm-widget.css', __FILE__ ), '1.0.1' );
		
    }
	
    public function backend_widget_styles() {		
		wp_enqueue_style( 'post-editor-style', plugins_url( 'assets/css/backend/post-widget-editor.css', __FILE__ ), '1.0.0' );
	}
    
    /**
	 * Init Scripts
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
    public function widget_scripts() {
		
		$upgrade_swiper = get_option('elementor_experiment-e_swiper_latest');

		if ($upgrade_swiper == 'active') {
			wp_register_script( 'swiper', ELEMENTOR_ASSETS_URL . 'lib/swiper/v8/swiper.min.js', '8.4.5', false );
		} else {
			wp_register_script( 'swiper', ELEMENTOR_ASSETS_URL . 'lib/swiper/swiper.min.js', '5.3.6', false );
		}
		
		wp_register_script( 'bpf-select2-script', ELEMENTOR_ASSETS_URL . 'lib/e-select2/js/e-select2.full.min.js', ['jquery'], false, true );
		
		//Enqueue Plugin Assets
		wp_register_script( 'post-widget-script', plugins_url( 'assets/js/cwm-post-widget.js', __FILE__ ), '1.0.0' );
		wp_localize_script( 'post-widget-script', 'ajax_var', array('url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ajax-nonce')));
		
		wp_register_script( 'filter-widget-script', plugins_url( 'assets/js/cwm-filter-widget.js', __FILE__ ), '1.0.0' );
		wp_localize_script( 'filter-widget-script', 'ajax_var', array('url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ajax-nonce')));

	}
	
    public function backend_widget_scripts() {		
		wp_enqueue_script( 'post-editor-script', plugins_url( 'assets/js/backend/post-widget-editor.js', __FILE__ ), '1.0.0' );
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_widgets() {
		
		// Include Widget files
		require_once( __DIR__ . '/widgets/post-widget.php' );
		\Elementor\Plugin::instance()->widgets_manager->register( new \BPF_Post_Widget() );
		
		require_once( __DIR__ . '/widgets/filter-widget.php' );
		\Elementor\Plugin::instance()->widgets_manager->register( new \BPF_Filter_Widget() );
		
		require_once( __DIR__ . '/widgets/filter-sorting.php' );
		\Elementor\Plugin::instance()->widgets_manager->register( new \BPF_Sorting_Widget() );

		require_once( __DIR__ . '/widgets/filter-posts-found.php' );
		\Elementor\Plugin::instance()->widgets_manager->register( new \BPF_Posts_Found_Widget() );		
		
	}

	/**
	 * Init Controls
	 *
	 * Include controls files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_controls() {

		// Include Control files
		// require_once( __DIR__ . '/controls/test-control.php' );

		// Register control
		// \Elementor\Plugin::$instance->controls_manager->register_control( 'control-type-', new \Test_Control() );

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

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'bpf-widget' ),
			'<strong>' . esc_html__( 'Better Post & Filter Widgets for Elementor', 'bpf-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'bpf-widget' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

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

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'bpf-widget' ),
			'<strong>' . esc_html__( 'Better Post & Filter Widgets for Elementor', 'bpf-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'bpf-widget' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

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

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'bpf-widget' ),
			'<strong>' . esc_html__( 'Better Post & Filter Widgets for Elementor', 'bpf-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'bpf-widget' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

}
BPF_Elementor::instance();
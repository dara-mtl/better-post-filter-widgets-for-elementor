<?php
/**
 * Handles the registration of custom dynamic tags for Elementor.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF_Dynamic_Tag\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * BPF_Dynamic_Tag class
 *
 * Handles adding dynamic tags to Elementor.
 *
 * @since 1.0.0
 */
class BPF_Dynamic_Tag {
	const TAG_DIR       = __DIR__ . '/../../tags/';
	const TAG_NAMESPACE = 'BPF_Dynamic_Tag\\Tags\\';

	/**
	 * List of dynamic tags.
	 *
	 * @var array
	 */
	private $tags_list = array(
		'custom-field'        => 'Custom_Field',
		'repeater'            => 'Repeater',
		'post-content'        => 'Post_Content',
		'shortcode'           => 'Shortcode',
		'post-title'          => 'Post_Title',
		'post-date'           => 'Post_Date',
		'post-url'            => 'Post_URL',
		'pages-url'           => 'Pages_URL',
		'post-featured-image' => 'Post_Featured_Image',
		'post-excerpt'        => 'Post_Excerpt',
		'post-terms'          => 'Post_Terms',
		'author-meta'         => 'Author_Info_Meta',
		'user-meta'           => 'User_Meta',
	);

	/**
	 * Constructor to initialize the dynamic tag registration.
	 *
	 * Hooks into Elementor's dynamic tag registration action.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
			add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_tags' ) );
		} else {
			add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );
		}
	}

	/**
	 * Registers custom dynamic tags with Elementor.
	 *
	 * Dynamically includes and registers tag classes based on the tags list.
	 *
	 * @since 1.0.0
	 *
	 * @param \Elementor\DynamicTags_Manager $dynamic_tags Elementor's dynamic tags manager instance.
	 */
	public function register_tags( $dynamic_tags ) {
		\Elementor\Plugin::$instance->dynamic_tags->register_group( 'bpf-dynamic-tags', [ 'title' => esc_html__( 'Custom Dynamic Tags', 'bpf-widget' ) ] );
		\Elementor\Plugin::$instance->dynamic_tags->register_group( 'post', [ 'title' => esc_html__( 'Post', 'bpf-widget' ) ] );
		\Elementor\Plugin::$instance->dynamic_tags->register_group( 'author', [ 'title' => esc_html__( 'Author', 'bpf-widget' ) ] );
		\Elementor\Plugin::$instance->dynamic_tags->register_group( 'user', [ 'title' => esc_html__( 'User', 'bpf-widget' ) ] );

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			// Register custom-field, repeater and content tags for Pro users.
			$extra_tags = [ 'custom-field', 'repeater', 'post-content' ];

			foreach ( $extra_tags as $tag ) {
				$class_name      = $this->tags_list[ $tag ];
				$full_class_name = self::TAG_NAMESPACE . $class_name;
				$full_file       = self::TAG_DIR . $tag . '.php';

				if ( file_exists( $full_file ) ) {
					require_once $full_file;

					if ( class_exists( $full_class_name ) ) {
						if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
							$dynamic_tags->register_tag( new $full_class_name() );
						} else {
							$dynamic_tags->register( new $full_class_name() );
						}
					}
				}
			}
		} else {
			// Register all tags for free users.
			foreach ( $this->tags_list as $file => $class_name ) {
				$full_class_name = self::TAG_NAMESPACE . $class_name;
				$full_file       = self::TAG_DIR . $file . '.php';

				if ( ! file_exists( $full_file ) ) {
					continue;
				}

				require_once $full_file;

				if ( class_exists( $full_class_name ) ) {
					if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
						$dynamic_tags->register_tag( new $full_class_name() );
					} else {
						$dynamic_tags->register( new $full_class_name() );
					}
				}
			}
		}
	}
}

new BPF_Dynamic_Tag();

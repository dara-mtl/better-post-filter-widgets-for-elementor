<?php

namespace Custom_Dynamic_Tag;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Custom_Dynamic_Tag {
	const TAG_DIR = __DIR__ . '/tags/';
    const TAG_NAMESPACE = __NAMESPACE__ . '\\tags\\';

	private $tags_list = array(
		'custom-field' => 'Custom_Field',
		'repeater' => 'Repeater',
		'shortcode' => 'Shortcode',
		'post-title' => 'Post_Title',
		'post-date' => 'Post_Date',
		'post-url' => 'Post_URL',
		'post-content' => 'Post_Content',
		'post-featured-image' => 'Post_Featured_Image',
		'post-excerpt' => 'Post_Excerpt',
		'post-terms' => 'Post_Terms',
		'author-meta' => 'Author_Info_Meta',
	);
	
	public function __construct() {
		
		if (version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
			add_action('elementor/dynamic_tags/register_tags', array($this, 'register_tags'));
		} else {
			add_action('elementor/dynamic_tags/register', array($this, 'register_tags'));
		}
	}
	
	public function register_tags($dynamic_tags) {
		\Elementor\Plugin::$instance->dynamic_tags->register_group('custom-dynamic-tags', ['title' => esc_html__('Custom Dynamic Tags', 'cwm-extension')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('post', ['title' => esc_html__('Post', 'cwm-extension')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('archive', ['title' => esc_html__('Archive', 'cwm-extension')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('author', ['title' => esc_html__('Author', 'cwm-extension')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('site', ['title' => esc_html__('Site', 'cwm-extension')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('media', ['title' => esc_html__('Media', 'cwm-extension')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('url', ['title' => esc_html__('URL', 'cwm-extension')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('action', ['title' => esc_html__('Action', 'cwm-extension')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('comments', ['title' => esc_html__('Comments', 'cwm-extension')]);
		if (class_exists('WooCommerce')) {
		\Elementor\Plugin::$instance->dynamic_tags->register_group('woocommerce', ['title' => esc_html__('Woocommerce', 'cwm-extension')]);
		}
		
        foreach ($this->tags_list as $file => $className) {
			$fullClassName = self::TAG_NAMESPACE . $className;
			$fullFile = self::TAG_DIR . $file . '.php';
			
			if (! file_exists($fullFile)) {
				continue;
			}
			
			require_once $fullFile;
			include_once( 'inc/classes/helper-class.php' );
			
			if (class_exists($fullClassName)) {
				if (version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
					$dynamic_tags->register_tag(new $fullClassName());
				} else {
					$dynamic_tags->register(new $fullClassName());
				}
			}
        }
	}

}
new Custom_Dynamic_Tag();
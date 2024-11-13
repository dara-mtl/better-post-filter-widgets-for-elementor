<?php

namespace BPF_Dynamic_Tag;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class BPF_Dynamic_Tag {
	const TAG_DIR = __DIR__ . '/tags/';
    const TAG_NAMESPACE = __NAMESPACE__ . '\\tags\\';

	private $tags_list = array(
		'custom-field' => 'Custom_Field',
		'repeater' => 'Repeater',
		'post-content' => 'Post_Content',
		'shortcode' => 'Shortcode',
		'post-title' => 'Post_Title',
		'post-date' => 'Post_Date',
		'post-url' => 'Post_URL',
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
		\Elementor\Plugin::$instance->dynamic_tags->register_group('bpf-dynamic-tags', ['title' => esc_html__('Custom Dynamic Tags', 'bpf-widget')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('post', ['title' => esc_html__('Post', 'bpf-widget')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('author', ['title' => esc_html__('Author', 'bpf-widget')]);

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			// Register custom-field, repeater and content tags for Pro users
			$extra_tags = ['custom-field', 'repeater', 'post-content'];
			
			foreach ($extra_tags as $tag) {
				$className = $this->tags_list[$tag];
				$fullClassName = self::TAG_NAMESPACE . $className;
				$fullFile = self::TAG_DIR . $tag . '.php';

				if (file_exists($fullFile)) {
					require_once $fullFile;

					if (class_exists($fullClassName)) {
						if (version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
							$dynamic_tags->register_tag(new $fullClassName());
						} else {
							$dynamic_tags->register(new $fullClassName());
						}
					}
				}
			}
		} else {
			// Register all tags for free users
			foreach ($this->tags_list as $file => $className) {
				$fullClassName = self::TAG_NAMESPACE . $className;
				$fullFile = self::TAG_DIR . $file . '.php';

				if (!file_exists($fullFile)) {
					continue;
				}

				require_once $fullFile;

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

}

new BPF_Dynamic_Tag();
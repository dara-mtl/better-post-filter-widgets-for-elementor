<?php

namespace Custom_Dynamic_Tag\Tags;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Post_Content extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'post-content-tag';
	}

	public function get_title() {
		return __( 'Post Content', 'bpf-widget' );
	}

	public function get_group() {
		return 'post';
	}

	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}
	
	protected function register_controls() {
		$this->add_control(
			'max_length',
			[
				'label' => esc_html__( 'Content Length', 'bpf-widget' ),
				'type' => Controls_Manager::NUMBER,
			]
		);
	}

	public function render() {
		$current_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$max_length = (int) $this->get_settings('max_length');
		$trimmed_content = wp_strip_all_tags( get_the_content() );
		
		if($max_length){
			$post_content =  wp_trim_words($trimmed_content, $max_length, '...' ) ;
		} else {
			
		}

		if ( strpos($current_url, 'preview_nonce') !== false || is_admin() ) {
			echo 'This is the post content. The full content will only display on the live page.';
		} else {
			if ( !empty($post_content) ) {
				echo $post_content;
			} else {
				echo the_content();
			}
		}
	}

}

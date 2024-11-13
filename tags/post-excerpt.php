<?php

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Post_Excerpt extends Tag
{

	public function get_name() {
		return 'post-excerpt';
	}

	public function get_title() {
		return esc_html__('Post Excerpt', 'bpf-widget');
	}

	public function get_group() {
		return 'post';
	}

	public function get_categories() {
		return [TagsModule::TEXT_CATEGORY];
	}

	public function render() {
		$post = get_post();

		if (!$post || empty($post->post_excerpt)) {
			return;
		}
		
		$post_excerpt = $post->post_excerpt;
		
		$max_length = (int) $this->get_settings('max_length');

		if($max_length){
			$post_excerpt = wp_trim_words($post_excerpt, $max_length, '...' );
		}

		echo wp_kses_post($post_excerpt);
	}
	
	protected function register_controls() {
		$this->add_control(
			'max_length',
			[
				'label' => esc_html__( 'Excerpt Length', 'bpf-widget' ),
				'type' => Controls_Manager::NUMBER,
			]
		);
	}
}

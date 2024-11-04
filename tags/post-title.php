<?php

namespace Custom_Dynamic_Tag\Tags;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Post_Title extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'post-title-tag';
	}

	public function get_title() {
		return __( 'Post Title', 'bpf-widget' );
	}

	public function get_group() {
		return 'post';
	}

	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	public function render() {
		
		$title = get_the_title();
		
		$max_length = (int) $this->get_settings('max_length');

		if($max_length){
			$title = wp_trim_words($title, $max_length, '...' );
		}
		
		echo wp_kses_post($title);

	}
	protected function register_controls()
	{
		$this->add_control(
			'max_length',
			[
				'label' => esc_html__( 'Title Length', 'bpf-widget' ),
				'type' => Controls_Manager::NUMBER,
			]
		);
	}
}
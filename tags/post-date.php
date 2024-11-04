<?php

namespace Custom_Dynamic_Tag\Tags;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Post_Date extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'post-date-tag';
	}

	public function get_title() {
		return __( 'Post Date', 'bpf-widget' );
	}

	public function get_group() {
		return 'post';
	}

	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	public function render() {
		$date = get_the_date();	
		echo wp_kses_post($date);
	}
}
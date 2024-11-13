<?php

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Post_URL extends Data_Tag {

	public function get_name() {
		return 'post-url-tag';
	}

	public function get_title() {
		return esc_html__('Post URL', 'bpf-widget');
	}

	public function get_group() {
		return 'post';
	}

	public function get_categories() {
		return [TagsModule::URL_CATEGORY];
	}

	public function get_value(array $options = []) {
		return get_permalink();
	}
}
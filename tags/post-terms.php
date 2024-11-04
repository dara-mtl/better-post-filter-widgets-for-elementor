<?php

namespace Custom_Dynamic_Tag\Tags;

use BPF\Inc\Classes\BPF_Helper;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Post_Terms extends Tag {

	public function get_name() {
		return 'post-terms';
	}

	public function get_title() {
		return esc_html__('Post Terms', 'bpf-widget');
	}

	public function get_group() {
		return 'post';
	}

	public function get_categories() {
		return [TagsModule::TEXT_CATEGORY];
	}

	protected function register_controls() {
		$taxonomy_filter_args = [
			'show_in_nav_menus' => true,
			'object_type' => [get_post_type()],
		];

		$taxonomy_filter_args = apply_filters('cwm/dynamic_tags/post_terms/taxonomy_args', $taxonomy_filter_args);

		$taxonomies = BPF_Helper::cwm_get_taxonomies($taxonomy_filter_args, 'objects');

		$options = [];

		foreach ($taxonomies as $taxonomy => $object) {
			$options[$taxonomy] = $object->label;
		}

		$this->add_control(
			'taxonomy',
			[
				'label' => esc_html__('Taxonomy', 'bpf-widget'),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'post_tag',
			]
		);

		$this->add_control(
			'separator',
			[
				'label' => esc_html__('Separator', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'default' => ', ',
			]
		);
	}

    public function render() {
        $settings = $this->get_settings();
    
        $value = get_the_term_list(get_the_ID(), $settings['taxonomy'], '', $settings['separator']);
    
        if (is_wp_error($value)) {
            return;
        }
    
        if (!empty($value)) {
            echo wp_kses_post($value);
        } else {
            return;
        }
    }
}
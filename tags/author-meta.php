<?php

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Author_Info_Meta extends Tag {

	public function get_name() {
		return 'author-info-meta';
	}

	public function get_title() {
		return esc_html__('Author Info', 'bpf-widget');
	}

	public function get_group() {
		return 'author';
	}

	public function get_categories() {
		return [
			TagsModule::NUMBER_CATEGORY,
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::COLOR_CATEGORY
		];
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label' => esc_html__('Field', 'bpf-widget'),
				'type' => Controls_Manager::SELECT,
				'default' => 'ID',
				'options' => [
					'ID' => esc_html__('ID', 'bpf-widget'),
					'description' => esc_html__('Bio', 'bpf-widget'),
					'email' => esc_html__('Email', 'bpf-widget'),
					'url' => esc_html__('Website', 'bpf-widget'),
					'profile_url' => esc_html__('Profile URL', 'bpf-widget'),
					'meta' => esc_html__('Author Meta', 'bpf-widget'),
				],
			]
		);

		$this->add_control(
			'meta_key',
			[
				'label' => esc_html__('Meta Key', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'key' => 'meta',
				],
			]
		);
	}

	public function render() {
		$key = $this->get_settings('key');
		$meta_key = $this->get_settings('meta_key');

		if (empty($key) && empty($meta_key)) {
			return;
		}

		if ($key === 'profile_url') {
			$value = get_author_posts_url(get_the_author_meta('ID'));
		} else if ($key === 'meta' && !empty($meta_key)) {
			$value = get_the_author_meta($meta_key);
		} else {
			if ($key === 'ID') {
				$value = is_author() ? get_queried_object_id() : get_the_author_meta('ID');
			} else {
				$value = get_the_author_meta($key);
			}
		}

		echo wp_kses_post($value);
	}
}
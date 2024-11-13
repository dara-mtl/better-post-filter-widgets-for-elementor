<?php

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Custom_Field extends Tag {

	public function get_name() {
		return 'post-custom-field-tag';
	}

	public function get_title() {
		return esc_html__('Custom Field', 'bpf-widget');
	}

	public function get_group() {
		return 'bpf-dynamic-tags';
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

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {
		$this->add_control(
			'field_source',
			[
				'label' => esc_html__( 'Field Source', 'bpf-widget' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post'  => esc_html__( 'Post', 'bpf-widget' ),
					'tax' => esc_html__( 'Taxonomy', 'bpf-widget' ),
					'user' => esc_html__( 'User', 'bpf-widget' ),
					'author' => esc_html__( 'Author', 'bpf-widget' ),
					'theme' => esc_html__( 'Theme Option', 'bpf-widget' ),
				],
			]
		);
		
		$this->add_control(
			'option_key',
			[
				'label' => esc_html__('Option Key', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'condition'    => array(
					'field_source' => 'theme',
				)
			]
		);
		
		$this->add_control(
			'post_id',
			[
				'label' => esc_html__('Post ID', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Current Post ID', 'bpf-widget' ),
				'condition'    => array(
					'field_source' => 'post',
				)
			]
		);
		
		$this->add_control(
			'term_id',
			[
				'label' => esc_html__('Term ID', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Current Term ID', 'bpf-widget' ),
				'condition'    => array(
					'field_source' => 'tax',
				)
			]
		);
		
		$this->add_control(
			'user_id',
			[
				'label' => esc_html__('User ID', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Current User ID', 'bpf-widget' ),
				'condition'    => array(
					'field_source' => 'user',
				)
			]
		);
		
		$this->add_control(
			'custom_key',
			[
				'label' => esc_html__('Meta Key', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
			]
		);
		
		$this->add_control(
			'autop',
			[
				'label' => esc_html__( 'Add Paragraphs', 'bpf-widget' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'bpf-widget' ),
				'label_off' => esc_html__( 'No', 'bpf-widget' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
	}

	public function render() {
		$key = $this->get_settings('key');
		$need_autop = $this->get_settings('autop');
		$source = $this->get_settings('field_source');
		$post_id = $this->get_settings('post_id');
		$term_id = $this->get_settings('term_id');
		$user_id = $this->get_settings('user_id');
		
		$key = empty($key) ? $this->get_settings('custom_key') : $key;
		
		if (empty($key)) {
			return;
		}

		// Initialize $value
		$value = '';

		// Check if ACF is active
		if (function_exists('get_field')) {
			// ACF specific logic
			if ($source === 'post') {
				$value = $post_id ? get_field($key, $post_id) : get_field($key);
			} elseif ($source === 'tax') {
				$value = $term_id ? get_field($key, 'term_' . $term_id) : get_field($key, 'term_' . get_queried_object()->term_id);
			} elseif ($source === 'user') {
				$value = $user_id ? get_field($key, 'user_' . $user_id) : get_field($key, 'user_' . get_current_user_id());
			} elseif ($source === 'author') {
				$author_id = get_the_author_meta('ID');
				if (is_author()) {
					$author_id = get_queried_object_id();
				}
				$value = get_field($key, 'user_' . $author_id);
			}
		}

		// Fallback to default methods if ACF is not used
		if (empty($value)) {
			if($source === "post" && !$post_id) {
				$value = get_post_meta(get_the_ID(), $key, true);
			}
			
			if($source === "post" && $post_id) {
				$value = get_post_meta($post_id, $key, true);
			}
			
			if($source === "tax" && !$term_id) {
				$value = get_term_meta(get_queried_object()->term_id, $key, true);
			}
			
			if($source === "tax" && $term_id) {
				$value = get_term_meta($term_id, $key, true);
			}
			
			if($source === "user" && !$user_id) {
				$value = get_user_meta(get_current_user_id(), $key, true);
			}
			
			if($source === "user" && $user_id) {
				$value = get_user_meta($user_id, $key, true);
			}
			
			if($source === "author") {
				$author_id = get_the_author_meta('ID');
				if (is_author()){
					$author_id = get_queried_object_id();
				}
				$value = get_the_author_meta($key, $author_id);
			}
			
			if ($source === "theme") {
				$option_key = $this->get_settings('option_key');
				$theme_option = get_option($option_key);

				if (isset($theme_option[$key])) {
					$value = $theme_option[$key];
				} else {
					return;
				}
			}
		}
		
		echo ($need_autop == 'yes') ? wpautop(wp_kses_post_deep($value)) : wp_kses_post_deep($value);
	}
}
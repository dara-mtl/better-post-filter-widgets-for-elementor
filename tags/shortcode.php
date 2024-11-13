<?php
namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Shortcode extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'shortcode-tag';
	}

	public function get_title() {
		return __( 'Shortcode', 'bpf-widget' );
	}

	public function get_group() {
		return 'bpf-dynamic-tags';
	}

	public function get_categories() {
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::GALLERY_CATEGORY,
			TagsModule::IMAGE_CATEGORY,
			TagsModule::MEDIA_CATEGORY,
			TagsModule::NUMBER_CATEGORY,
		];
	}
	
	protected function register_controls() {
		$this->add_control(
			'shortcode',
			[
				'label' => esc_html__('Shortcode', 'bpf-widget' ),
				'type'  => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__('[your-shortcode]', 'bpf-widget' ),
			]
		);
	}

	public function render() {
		$settings = $this->get_settings();

		if (empty($settings['shortcode'])) {
			return;
		}

		$shortcode_string = $settings['shortcode'];
		$value = do_shortcode($shortcode_string);
		$value = wp_kses_post($value);
		
		echo $value;
	}
}
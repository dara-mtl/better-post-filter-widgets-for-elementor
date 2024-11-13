<?php

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Repeater extends Tag {

	public function get_name()
	{
		return 'repeater-tag';
	}

	public function get_title()
	{
		return esc_html__('Repeater', 'bpf-widget');
	}

	public function get_group()
	{
		return 'bpf-dynamic-tags';
	}

	public function get_categories()
	{
		return [
			TagsModule::NUMBER_CATEGORY,
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::COLOR_CATEGORY
		];
	}

	public function get_panel_template_setting_key()
	{
		return 'key';
	}

	public function is_settings_required()
	{
		return true;
	}

	protected function register_controls()
	{
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
					'theme' => esc_html__( 'Theme Options', 'bpf-widget' ),
				],
			]
		);
		
		$this->add_control(
			'option_key',
			[
				'label' => esc_html__('Theme Option Key', 'bpf-widget'),
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
				'label' => esc_html__('Parent Key', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
			]
		);
		
		$this->add_control(
			'child_key_1',
			[
				'label' => esc_html__('Child Key 1', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'separator' => 'before',
				'condition'    => array(
					'custom_key!' => '',
				)
			]
		);
		
		$this->add_control(
			'child_key_2',
			[
				'label' => esc_html__('Child Key 2', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'condition'    => array(
					'child_key_1!' => '',
					'custom_key!' => '',
				)
			]
		);
		
		$this->add_control(
			'child_key_3',
			[
				'label' => esc_html__('Child Key 3', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'condition'    => array(
					'child_key_2!' => '',
					'custom_key!' => '',
				)
			]
		);
		
		$this->add_control(
			'child_key_4',
			[
				'label' => esc_html__('Child Key 4', 'bpf-widget'),
				'type' => Controls_Manager::TEXT,
				'condition'    => array(
					'child_key_3!' => '',
					'custom_key!' => '',
				)
			]
		);
		
		$this->add_control(
			'child_html_tag',
			[
				'label' => esc_html__('HTML Tag', 'bpf-widget'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__('No Tag', 'bpf-widget'),
					'div' => esc_html__('div', 'bpf-widget'),
					'span' => esc_html__('span', 'bpf-widget'),
					'p' => esc_html__('p', 'bpf-widget'),
					'h1' => esc_html__('h1', 'bpf-widget'),
					'h2' => esc_html__('h2', 'bpf-widget'),
					'h3' => esc_html__('h3', 'bpf-widget'),
					'h4' => esc_html__('h4', 'bpf-widget'),
					'h5' => esc_html__('h5', 'bpf-widget'),
					'h6' => esc_html__('h6', 'bpf-widget'),
					'ul' => esc_html__('ul', 'bpf-widget'),
					'ol' => esc_html__('ol', 'bpf-widget'),
					'table' => esc_html__('table', 'bpf-widget'),
					'toggle' => esc_html__('toggle', 'bpf-widget'),
				],
				'separator' => 'before',
			]
		);

	}

	public function render() {
		$key = $this->get_settings('custom_key');
		$source = $this->get_settings('field_source');
		$post_id = $this->get_settings('post_id');
		$term_id = $this->get_settings('term_id');
		$user_id = $this->get_settings('user_id');
		$html_tag = $this->get_settings('child_html_tag');

		if (empty($key)) {
			return;
		}

		// Check if ACF is active
		$is_acf_active = function_exists('get_field');

		// Get the meta data based on the field source
		switch ($source) {
			case 'post':
				if ($is_acf_active) {
					$entries = $post_id ? get_field($key, $post_id) : get_field($key, get_the_ID());
				} else {
					$entries = $post_id ? get_post_meta($post_id, $key, true) : get_post_meta(get_the_ID(), $key, true);
				}
				break;
			case 'tax':
				if ($is_acf_active) {
					$entries = $term_id ? get_field($key, 'term_' . $term_id) : get_field($key, 'term_' . get_queried_object()->term_id);
				} else {
					$entries = $term_id ? get_term_meta($term_id, $key, true) : get_term_meta(get_queried_object()->term_id, $key, true);
				}
				break;
			case 'user':
				if ($is_acf_active) {
					$entries = $user_id ? get_field($key, 'user_' . $user_id) : get_field($key, 'user_' . get_current_user_id());
				} else {
					$entries = $user_id ? get_user_meta($user_id, $key, true) : get_user_meta(get_current_user_id(), $key, true);
				}
				break;
			case 'author':
				$author_id = is_author() ? get_queried_object_id() : get_the_author_meta('ID');
				if ($is_acf_active) {
					$entries = get_field($key, 'user_' . $author_id);
				} else {
					$entries = get_the_author_meta($key, $author_id);
				}
				break;
			case 'theme':
				$option_key = $this->get_settings('option_key');
				$theme_option = get_option($option_key);
				$entries = $theme_option[$key] ?? null;
				break;
			default:
				return;
		}

		if (empty($entries) || !is_array($entries)) {
			return;
		}

		// Get the number of child keys from settings or define max manually
		$max_child_keys = 4; // Define how many child keys you want to support
		$class_nb = 0;

		if ($html_tag === 'toggle') {
			echo '<style>
				.toggle-wrapper{border:1px solid #ddd;}.repeater-toggle{display:none}.repeater-toggle+label{cursor:pointer;display:block;padding:10px;background:#f5f5f5;position:relative;transition:background-color 0.3s ease}.repeater-toggle+label:hover{background-color:#e0e0e0}.repeater-toggle+label::after{content:"❯";position:absolute;right:10px;transition:transform 0.3s ease}.repeater-toggle:checked+label::after{transform:rotate(90deg)}.repeater-toggle:checked+label+.toggle-content{display:block;opacity:1;height:auto;padding:10px;overflow:hidden;transform:translateY(0)}.toggle-content{opacity:0;transform:translateY(-10px);height:0;padding:0;overflow:hidden;position:relative;transition:height 0.3s ease,opacity 0.15s,transform 0.3s ease}
			</style>';

			// Render each entry with dynamic toggling and proper HTML structure
			foreach ($entries as $entry) {
				$toggle_id = 'toggle-' . uniqid();
				$toggle_title = '';
				$toggle_content = '';

				// Assuming the first field is the title and the second is the content
				for ($counter = 1; $counter <= $max_child_keys; $counter++) {
					$child_key = "child_key_{$counter}";
					$setting_value = $this->get_settings($child_key);

					if (!empty($setting_value)) {
						$value_parts = explode('|', $setting_value);
						$child_value_key = $value_parts[0];
						$before = isset($value_parts[1]) ? $value_parts[1] : '';
						$after = isset($value_parts[2]) ? $value_parts[2] : '';

						$child_value = $entry[$child_value_key] ?? '';
						
						if ($counter === 1) {
							// Use the first child as the toggle title
							$toggle_title = $before . str_replace("#", $class_nb, $child_value) . $after;
						} else {
							// Additional children as toggle content
							$toggle_content .= $before . str_replace("#", $class_nb, wpautop(wp_kses_post_deep($child_value))) . $after;
						}
					}
				}

				// Print each toggle item with dynamic content
				echo '<div class="toggle-wrapper"><input type="checkbox" class="repeater-toggle" id="' . esc_attr($toggle_id) . '" />';
				echo '<label for="' . esc_attr($toggle_id) . '">' . $toggle_title . '</label>';
				echo '<div class="toggle-content">' . $toggle_content . '</div></div>';
			}
		} elseif ($html_tag === 'table') {
			echo '<table class="repeater-table"><tr>';
			foreach ($entries as $entry) {
				echo '<tr>';
				for ($counter = 1; $counter <= $max_child_keys; $counter++) {
					$child_key = "child_key_{$counter}";
					$setting_value = $this->get_settings($child_key);

					if (!empty($setting_value)) {
						$value_parts = explode('|', $setting_value);
						$child_value_key = $value_parts[0];
						$before = isset($value_parts[1]) ? $value_parts[1] : '';
						$after = isset($value_parts[2]) ? $value_parts[2] : '';

						if (isset($entry[$child_value_key])) {
							$value = $before . $entry[$child_value_key] . $after;
							echo '<td class="table-cell cell-' . esc_attr($class_nb++) . '">' . str_replace("#", $class_nb, esc_html($value)) . '</td>';
						}
					}
				}
				echo '</tr>';
			}
			echo '</table>';
		} elseif ($html_tag === 'ul' || $html_tag === 'ol') {
			$class_nb++;
			echo "<{$html_tag} class='repeater-list'>";
			foreach ($entries as $entry) {
				echo '<li>';
				for ($counter = 1; $counter <= $max_child_keys; $counter++) {
					$child_key = "child_key_{$counter}";
					$setting_value = $this->get_settings($child_key);

					if (!empty($setting_value)) {
						$value_parts = explode('|', $setting_value);
						$child_value_key = $value_parts[0];
						$before = isset($value_parts[1]) ? $value_parts[1] : '';
						$after = isset($value_parts[2]) ? $value_parts[2] : '';
						
						// Get the value with before and after texts
						$value = $before . $entry[$child_value_key] . $after;
						echo str_replace("#", $class_nb, esc_html($value));
					}
				}
				echo '</li>';
			}
			echo "</{$html_tag}>";
		} else {
			foreach ($entries as $entry) {
				for ($counter = 1; $counter <= $max_child_keys; $counter++) {
					$child_key = "child_key_{$counter}";
					$setting_value = $this->get_settings($child_key);

					if (!empty($setting_value)) {
						$value_parts = explode('|', $setting_value);
						$child_value_key = $value_parts[0];
						$before = isset($value_parts[1]) ? $value_parts[1] : '';
						$after = isset($value_parts[2]) ? $value_parts[2] : '';
						if (isset($entry[$child_value_key])) {
							$value = $before . $entry[$child_value_key] . $after;
							$classes = 'repeater-field field-' . esc_attr($class_nb++);
							if ($html_tag !== 'none') {
								echo "<{$html_tag} class='{$classes}'>" . str_replace("#", $class_nb, esc_html($value)) . "</{$html_tag}>";
							} else {
								echo str_replace("#", $class_nb, esc_html($value)) . ' ';
							}
						}
					}
				}
			}
		}
	}

}
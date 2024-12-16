<?php
/**
 * Page URL Dynamic Tag.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

namespace BPF_Dynamic_Tag\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Elementor custom dynamic tag.
 *
 * A dynamic tag to fetch and display a specific page URL.
 *
 * @since 1.0.0
 */
class Pages_URL extends Tag {

	/**
	 * Get tag name.
	 *
	 * Retrieve the tag name for internal use.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Tag name.
	 */
	public function get_name() {
		return 'pages-url-tag';
	}

	/**
	 * Get tag title.
	 *
	 * Retrieve the dynamic tag title to display in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Tag title.
	 */
	public function get_title() {
		return esc_html__( 'Pages URL', 'bpf-widget' );
	}

	/**
	 * Get dynamic tag group.
	 *
	 * Retrieve the dynamic tag group the tag belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Dynamic tag group.
	 */
	public function get_group() {
		return 'bpf-dynamic-tags';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the pages URL dynamic tag belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [
			TagsModule::URL_CATEGORY,
		];
	}

	/**
	 * Retrieve a list of pages for selection.
	 *
	 * This method retrieves all published pages and prepares them for use in a dropdown select list.
	 * It checks if the page is set as the front page and labels it as "Home Page" if it is.
	 * The list is returned as an associative array where the keys are page IDs and the values are page titles.
	 *
	 * @return array Associative array of page IDs and titles, with an empty option labeled "Select..." as the first item.
	 */
	public function get_pages_list() {

		$items   = [
			'' => esc_html__( 'Select...', 'bpf-widget' ),
		];
		$pages   = get_posts(
			array(
				'post_type'   => 'page',
				'numberposts' => -1,
			)
		);
		$home_id = get_option( 'page_on_front' );
		foreach ( $pages as $page ) {
			$page->post_title   = $home_id === $page->ID ? esc_html__( 'Home Page', 'bpf-widget' ) : $page->post_title;
			$items[ $page->ID ] = $page->post_title;
		}

		return $items;
	}

	/**
	 * Determine if settings are required.
	 *
	 * Indicates whether the tag requires additional settings.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool True if settings are required, false otherwise.
	 */
	public function is_settings_required() {
		return true;
	}

	/**
	 * Register controls.
	 *
	 * Add controls for the dynamic tag, allowing users to set the tag's settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label'   => esc_html__( 'Pages URL', 'bpf-widget' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_pages_list(),
				'default' => '',
			]
		);
	}

	/**
	 * Get page URL.
	 *
	 * Retrieve the URL of the selected page from settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return string Page URL.
	 */
	protected function get_page_url() {
		$key = $this->get_settings( 'key' );

		if ( ! empty( $key ) ) {
			return get_permalink( $key );
		}

		return '';
	}

	/**
	 * Get dynamic tag value.
	 *
	 * Return the value of the dynamic tag based on the settings.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $options Optional. Additional options.
	 * @return string Dynamic tag value.
	 */
	public function get_value( array $options = [] ) {
		return $this->get_page_url();
	}

	/**
	 * Render the dynamic tag.
	 *
	 * Output the value of the dynamic tag.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {
		echo esc_url_raw( $this->get_page_url() );
	}
}

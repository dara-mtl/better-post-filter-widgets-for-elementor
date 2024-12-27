<?php
/**
 * Handles dynamic group functionality for Elementor widgets.
 *
 * @package BPF_Widgets
 * @since 1.0.0
 */

use Elementor\Repeater;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class BPF_Dynamic_Group
 *
 * Handles dynamic group functionality for Elementor widgets.
 * Includes methods for rendering content and adding custom controls.
 *
 * @since 1.0.0
 */
class BPF_Dynamic_Group {

	/**
	 * Constructor for the BPF_Dynamic_Group class.
	 *
	 * Initializes the dynamic group functionality and sets up necessary hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'add_controls_section' ] );
		add_filter( 'elementor/widget/render_content', [ $this, 'render_content' ], 10, 2 );
	}

	/**
	 * Renders the content for the dynamic group.
	 *
	 * Processes the dynamic group data and returns the modified widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_content The original widget content.
	 * @param object $widget         The widget instance.
	 *
	 * @return string Modified widget content.
	 */
	public function render_content( $widget_content, $widget ) {
		$settings        = $widget->get_settings_for_display();
		$dynamic_content = [];
		$direct_content  = [];

		if ( empty( $settings['dynamic_list'] ) ) {
			return $widget_content;
		}

		if ( ! empty( $settings['dynamic_list'] ) ) {
			foreach ( $settings['dynamic_list'] as $item ) {
				$dynamic_text   = ! empty( $item['dynamic_fields'] ) ? $item['dynamic_fields'] : '';
				$dynamic_target = ! empty( $item['dynamic_target'] ) ? $item['dynamic_target'] : '';
				$dynamic_type   = ! empty( $item['dynamic_type'] ) ? $item['dynamic_type'] : 'content';

				if ( $dynamic_text ) {
					switch ( $dynamic_type ) {
						case 'content':
							if ( $dynamic_target ) {
								// Gather content by target for injection if target specified.
								$dynamic_content[ $dynamic_target ][] = esc_html( $dynamic_text );
							} else {
								// Direct content fallback if no target is specified.
								$direct_content[] = esc_html( $dynamic_text );
							}
							break;

						case 'id':
							$widget->add_render_attribute( '_wrapper', 'id', esc_attr( $dynamic_text ) );
							break;

						case 'class':
							$widget->add_render_attribute( '_wrapper', 'class', esc_attr( $dynamic_text ) );
							break;

						case 'data-attribute':
							$data_parts = explode( '|', $dynamic_text );

							if ( count( $data_parts ) === 2 ) {
								$data_key   = sanitize_title( trim( $data_parts[0] ) );
								$data_value = esc_attr( trim( $data_parts[1] ) );

								$blacklist = [
									'id',
									'class',
									'style', // Prevent inline styles.
									'onclick', // Prevent inline JS.
									'data-id',
									'data-settings',
									'data-element_type',
									'data-widget_type',
									'data-model-cid',
								];

								if ( ! in_array( $data_key, $blacklist, true ) ) {
									$widget->add_render_attribute( '_wrapper', $data_key, $data_value );
								}
							}
							break;
					}
				}
			}
		}

		// Inject combined dynamic content into specific targets within widget content.
		foreach ( $dynamic_content as $target => $texts ) {
			$combined_text  = implode( ' ', $texts );
			$is_class       = strpos( $target, '.' ) === 0;
			$is_id          = strpos( $target, '#' ) === 0;
			$pattern_target = ltrim( $target, '.#' );

			// Build pattern based on target type (class or ID).
			$pattern     = '/(<[^>]*(?:class|id)="[^"]*?' . preg_quote( $pattern_target, '/' ) . '[^"]*?"[^>]*>)(.*?)(<\/[^>]*>)/is';
			$replacement = '$1' . $combined_text . '$3';

			// Replace content in the widget's HTML structure.
			$widget_content = preg_replace( $pattern, $replacement, $widget_content );
		}

		if ( ! empty( $direct_content ) ) {
			if ( preg_match( '/^<([a-zA-Z0-9\-]+)([^>]*)>/', $widget_content, $matches ) ) {
				$first_tag      = $matches[0];
				$closing_tag    = "</{$matches[1]}>";
				$widget_content = preg_replace( '/^<([a-zA-Z0-9\-]+)([^>]*)>/', '', $widget_content );
				$widget_content = preg_replace( '/<\/([a-zA-Z0-9\-]+)>/', '', $widget_content );
				$widget_content = $first_tag . implode( ' ', $direct_content ) . $closing_tag;
			} else {
				$widget_content = implode( ' ', $direct_content );
			}
		}

		return $widget_content;
	}

	/**
	 * Adds a controls section to the Elementor widget.
	 *
	 * Includes options for configuring the dynamic group behavior and appearance.
	 *
	 * @since 1.0.0
	 *
	 * @param \Elementor\Widget_Base $element The widget instance.
	 *
	 * @return void
	 */
	public static function add_controls_section( $element ) {
		$element->start_controls_section(
			'dynamic_section',
			[
				'label' => esc_html__( 'Dynamic Group', 'bpf-widget' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		$repeater = new Repeater();
		$repeater->start_controls_tabs( 'field_repeater' );

		$repeater->start_controls_tab(
			'content',
			[
				'label' => esc_html__( 'Content', 'cwm-widget' ),
			]
		);

		$repeater->add_control(
			'dynamic_fields',
			[
				'label'       => esc_html__( 'Text/Dynamic Tag', 'bpf-widget' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter text or attach a dynamic tag', 'bpf-widget' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'advanced',
			[
				'label' => esc_html__( 'Advanced', 'cwm-widget' ),
			]
		);

		$repeater->add_control(
			'dynamic_type',
			[
				'label'       => esc_html__( 'Dynamic Field Output', 'bpf-widget' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'content',
				'label_block' => true,
				'options'     => [
					'content'        => esc_html__( 'Content', 'bpf-widget' ),
					'id'             => esc_html__( 'ID', 'bpf-widget' ),
					'class'          => esc_html__( 'Class', 'bpf-widget' ),
					'data-attribute' => esc_html__( 'Data Attribute', 'bpf-widget' ),
				],
			]
		);

		$repeater->add_control(
			'data_attr_note',
			[
				'type'      => \Elementor\Controls_Manager::RAW_HTML,
				'raw'       => sprintf(
					'<div class="elementor-control-field-description" style="margin:0">%s</div>',
					esc_html__( 'Set custom attributes for the wrapper element. Each attribute in a separate line. Separate attribute key from the value using Key|Value.', 'bpf-widget' )
				),
				'condition' => [
					'dynamic_type' => 'data-attribute',
				],
			]
		);

		$repeater->add_control(
			'dynamic_target',
			[
				'label'       => esc_html__( 'HTML Target', 'bpf-widget' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => '#id, .class',
				'description' => esc_html__( '(Optional) Specify a part of the widget’s HTML to replace.', 'bpf-widget' ),
				'condition'   => [
					'dynamic_type' => 'content',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$element->add_control(
			'dynamic_list',
			[
				'label'         => esc_html__( 'Repeater List', 'bpf-widget' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'prevent_empty' => true,
				'default'       => [
					'dynamic_fields' => '',
				],
				'title_field'   => '{{{ dynamic_fields }}}',
			]
		);

		$element->end_controls_section();
	}
}

new BPF_Dynamic_Group();

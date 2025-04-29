<?php
/**
 * Add swatches meta field to the taxonomies.
 *
 * @package BPFWE_Widgets
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * BPFWE_Taxonomy_Swatches
 */
class BPFWE_Taxonomy_Swatches {

	/**
	 * Initialize the class and set up hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_taxonomy_swatches' ) );
		add_action( 'edited_term', array( $this, 'save_term_swatches' ), 10, 1 );
		add_action( 'created_term', array( $this, 'save_term_swatches' ), 10, 1 );
	}

	/**
	 * Register swatches fields for all taxonomies.
	 */
	public function register_taxonomy_swatches() {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );

		if ( class_exists( 'WooCommerce' ) ) {
			$woo_taxonomies = array(
				'product_cat',
				'product_tag',
			);

			$attribute_taxonomies = wc_get_attribute_taxonomies();
			if ( ! empty( $attribute_taxonomies ) ) {
				foreach ( $attribute_taxonomies as $attribute ) {
					$taxonomy_name    = wc_attribute_taxonomy_name( $attribute->attribute_name );
					$woo_taxonomies[] = $taxonomy_name;
				}
			}

			$taxonomies = array_merge( $taxonomies, $woo_taxonomies );
			$taxonomies = array_unique( $taxonomies );
		}

		foreach ( $taxonomies as $taxonomy ) {
			add_action( "{$taxonomy}_edit_form_fields", array( $this, 'add_taxonomy_swatches_fields' ), 10, 2 );
			add_action( "{$taxonomy}_add_form_fields", array( $this, 'add_taxonomy_swatches_fields' ), 10, 1 );
		}
	}

	/**
	 * Add swatches type dropdown and conditional fields to taxonomy forms.
	 *
	 * @param WP_Term|string $term_or_taxonomy Term object or taxonomy name.
	 * @param string         $taxonomy         Taxonomy name (only for edit form).
	 */
	public function add_taxonomy_swatches_fields( $term_or_taxonomy, $taxonomy = '' ) {
		$is_edit  = is_object( $term_or_taxonomy );
		$term_id  = $is_edit ? $term_or_taxonomy->term_id : 0;
		$taxonomy = $is_edit ? $taxonomy : $term_or_taxonomy;

		$swatches_type         = $term_id ? get_term_meta( $term_id, 'bpfwe_swatches_type', true ) : 'none';
		$is_woocommerce_active = class_exists( 'WooCommerce' ) && in_array( $taxonomy, [ 'product_cat', 'product_brand' ], true );

		// Reset to 'none' if 'product-cat-image' is selected but WooCommerce is inactive.
		if ( 'product-cat-image' === $swatches_type && ! $is_woocommerce_active ) {
			$swatches_type = 'none';
			if ( $term_id ) {
				update_term_meta( $term_id, 'bpfwe_swatches_type', 'none' );
			}
		}

		wp_nonce_field( 'bpfwe_swatches_nonce', 'bpfwe_swatches_nonce_field' );

		if ( $is_edit ) {
			wp_localize_script(
				'taxonomy-editor-scripts',
				'bpfweSwatchesData',
				array(
					'color'      => get_term_meta( $term_id, 'bpfwe_swatches_color', true ) ? get_term_meta( $term_id, 'bpfwe_swatches_color', true ) : '#000000',
					'image'      => get_term_meta( $term_id, 'bpfwe_swatches_image', true ) ? get_term_meta( $term_id, 'bpfwe_swatches_image', true ) : '',
					'buttonText' => get_term_meta( $term_id, 'bpfwe_swatches_button_text', true ) ? get_term_meta( $term_id, 'bpfwe_swatches_button_text', true ) : '',
					'groupText'  => get_term_meta( $term_id, 'bpfwe_swatches_group_text', true ) ? get_term_meta( $term_id, 'bpfwe_swatches_group_text', true ) : '',
				)
			);
		}

		if ( $is_edit ) : ?>
			<tr class="form-field">
				<th scope="row"><label for="bpfwe_swatches_type"><?php esc_html_e( 'Swatches Type', 'better-post-filter-widgets-for-elementor' ); ?></label></th>
				<td>
					<select name="bpfwe_swatches_type" id="bpfwe_swatches_type">
						<option value="none" <?php selected( $swatches_type, 'none' ); ?>><?php esc_html_e( 'None', 'better-post-filter-widgets-for-elementor' ); ?></option>
						<option value="color" <?php selected( $swatches_type, 'color' ); ?>><?php esc_html_e( 'Color', 'better-post-filter-widgets-for-elementor' ); ?></option>
						<option value="image" <?php selected( $swatches_type, 'image' ); ?>><?php esc_html_e( 'Image', 'better-post-filter-widgets-for-elementor' ); ?></option>
						<option value="button" <?php selected( $swatches_type, 'button' ); ?>><?php esc_html_e( 'Button', 'better-post-filter-widgets-for-elementor' ); ?></option>
						<?php if ( $is_woocommerce_active ) : ?>
							<option value="product-cat-image" <?php selected( $swatches_type, 'product-cat-image' ); ?>><?php esc_html_e( 'WC Category Image', 'better-post-filter-widgets-for-elementor' ); ?></option>
						<?php endif; ?>
					</select>
				</td>
			</tr>
		<?php else : ?>
			<div class="form-field">
				<label for="bpfwe_swatches_type"><?php esc_html_e( 'Swatches Type', 'better-post-filter-widgets-for-elementor' ); ?></label>
				<select name="bpfwe_swatches_type" id="bpfwe_swatches_type">
					<option value="none" <?php selected( $swatches_type, 'none' ); ?>><?php esc_html_e( 'None', 'better-post-filter-widgets-for-elementor' ); ?></option>
					<option value="color" <?php selected( $swatches_type, 'color' ); ?>><?php esc_html_e( 'Color', 'better-post-filter-widgets-for-elementor' ); ?></option>
					<option value="image" <?php selected( $swatches_type, 'image' ); ?>><?php esc_html_e( 'Image', 'better-post-filter-widgets-for-elementor' ); ?></option>
					<option value="button" <?php selected( $swatches_type, 'button' ); ?>><?php esc_html_e( 'Button', 'better-post-filter-widgets-for-elementor' ); ?></option>
						<?php if ( $is_woocommerce_active ) : ?>
							<option value="product-cat-image" <?php selected( $swatches_type, 'product-cat-image' ); ?>><?php esc_html_e( 'WC Category Image', 'better-post-filter-widgets-for-elementor' ); ?></option>
						<?php endif; ?>
				</select>
			</div>
			<?php
		endif;

		$tax_obj    = get_taxonomy( $taxonomy );
		$group_text = $term_id ? get_term_meta( $term_id, 'bpfwe_swatches_group_text', true ) : '';

		if ( $is_edit ) :
			?>
			<tr class="form-field bpfwe-group-separator-wrap" style="<?php echo 'none' === $swatches_type ? 'display: none;' : ''; ?>">
				<th scope="row"><label for="bpfwe_swatches_group_text"><?php esc_html_e( 'Group Separator Text (Optional)', 'better-post-filter-widgets-for-elementor' ); ?></label></th>
				<td>
					<input type="text" name="bpfwe_swatches_group_text" id="bpfwe_swatches_group_text" placeholder="<?php esc_attr_e( 'Leave blank to skip the separator', 'better-post-filter-widgets-for-elementor' ); ?>" value="<?php echo esc_attr( $group_text ); ?>" />
					<p class="description"><?php esc_html_e( 'Enter text to use this term as a group separator in the filter widget.', 'better-post-filter-widgets-for-elementor' ); ?></p>
				</td>
			</tr>
		<?php else : ?>
			<div class="form-field bpfwe-group-separator-wrap" style="<?php echo 'none' === $swatches_type ? 'display: none;' : ''; ?>">
				<label for="bpfwe_swatches_group_text"><?php esc_html_e( 'Group Separator Text (Optional)', 'better-post-filter-widgets-for-elementor' ); ?></label>
				<input type="text" name="bpfwe_swatches_group_text" id="bpfwe_swatches_group_text" placeholder="<?php esc_attr_e( 'Leave blank to skip the separator', 'better-post-filter-widgets-for-elementor' ); ?>" value="<?php echo esc_attr( $group_text ? $group_text : '' ); ?>" />
				<p class="description"><?php esc_html_e( 'Enter text to use this term as a group separator in the filter widget.', 'better-post-filter-widgets-for-elementor' ); ?></p>
			</div>
			<?php
		endif;

		$this->render_swatches_fields( $term_id, $swatches_type, $is_edit );
	}

	/**
	 * Render conditional swatches fields based on selected swatches type.
	 *
	 * @param int    $term_id             Term ID.
	 * @param string $swatches_type       Selected swatches type.
	 * @param bool   $is_edit             Whether this is an edit form.
	 */
	private function render_swatches_fields( $term_id, $swatches_type, $is_edit ) {
		if ( 'color' === $swatches_type ) {
			$color_value = $term_id ? get_term_meta( $term_id, 'bpfwe_swatches_color', true ) : '#000000';
			if ( $is_edit ) :
				?>
				<tr class="form-field">
					<th scope="row"><label for="bpfwe_swatches_color"><?php esc_html_e( 'Swatch Color', 'better-post-filter-widgets-for-elementor' ); ?></label></th>
					<td>
						<input type="text" name="bpfwe_swatches_color" id="bpfwe_swatches_color" value="<?php echo esc_attr( $color_value ); ?>" class="bpfwe-swatches-color" />
					</td>
				</tr>
				<?php
			else :
				?>
				<div class="form-field">
					<label for="bpfwe_swatches_color"><?php esc_html_e( 'Swatch Color', 'better-post-filter-widgets-for-elementor' ); ?></label>
					<input type="text" name="bpfwe_swatches_color" id="bpfwe_swatches_color" value="<?php echo esc_attr( $color_value ); ?>" class="bpfwe-swatches-color" />
				</div>
				<?php
			endif;
		} elseif ( 'image' === $swatches_type ) {
			$image_value = $term_id ? get_term_meta( $term_id, 'bpfwe_swatches_image', true ) : '';
			if ( $is_edit ) :
				?>
				<tr class="form-field">
					<th scope="row"><label for="bpfwe_swatches_image"><?php esc_html_e( 'Swatch Image', 'better-post-filter-widgets-for-elementor' ); ?></label></th>
					<td>
						<input type="text" name="bpfwe_swatches_image" style="margin-bottom: 0.9rem;" id="bpfwe_swatches_image" value="<?php echo esc_attr( $image_value ); ?>" class="bpfwe-swatches-image" />
						<button type="button" class="button bpfwe-swatches-upload-button"><?php esc_html_e( 'Upload/Add image', 'better-post-filter-widgets-for-elementor' ); ?></button>
					</td>
				</tr>
				<?php
			else :
				?>
				<div class="form-field">
					<label for="bpfwe_swatches_image"><?php esc_html_e( 'Swatch Image', 'better-post-filter-widgets-for-elementor' ); ?></label>
					<input type="text" style="margin-bottom: 0.9rem;" name="bpfwe_swatches_image" id="bpfwe_swatches_image" value="<?php echo esc_attr( $image_value ); ?>" class="bpfwe-swatches-image" />
						<button type="button" class="button bpfwe-swatches-upload-button"><?php esc_html_e( 'Upload/Add image', 'better-post-filter-widgets-for-elementor' ); ?></button>
					</div>
				<?php
			endif;
		} elseif ( 'button' === $swatches_type ) {
			$button_text = $term_id ? get_term_meta( $term_id, 'bpfwe_swatches_button_text', true ) : '';
			if ( $is_edit ) :
				?>
				<tr class="form-field">
					<th scope="row"><label for="bpfwe_swatches_button_text"><?php esc_html_e( 'Swatch Button Text', 'better-post-filter-widgets-for-elementor' ); ?></label></th>
					<td>
						<input style="max-width:25%;" type="text" name="bpfwe_swatches_button_text" id="bpfwe_swatches_button_text" value="<?php echo esc_attr( $button_text ); ?>" />
					</td>
				</tr>
				<?php
			else :
				?>
				<div class="form-field">
					<label for="bpfwe_swatches_button_text"><?php esc_html_e( 'Swatch Button Text', 'better-post-filter-widgets-for-elementor' ); ?></label>
					<input style="max-width:25%;" type="text" name="bpfwe_swatches_button_text" id="bpfwe_swatches_button_text" value="<?php echo esc_attr( $button_text ); ?>" />
				</div>
				<?php
			endif;
		}
	}

	/**
	 * Save term swatches when term is created or edited.
	 *
	 * @param int $term_id Term ID.
	 */
	public function save_term_swatches( $term_id ) {
		$nonce = isset( $_POST['bpfwe_swatches_nonce_field'] ) ? sanitize_text_field( wp_unslash( $_POST['bpfwe_swatches_nonce_field'] ) ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'bpfwe_swatches_nonce' ) ) {
			return;
		}

		$swatches_type = isset( $_POST['bpfwe_swatches_type'] ) ? sanitize_key( wp_unslash( $_POST['bpfwe_swatches_type'] ) ) : 'none';
		update_term_meta( $term_id, 'bpfwe_swatches_type', $swatches_type );

		$group_text = isset( $_POST['bpfwe_swatches_group_text'] ) ? sanitize_text_field( wp_unslash( $_POST['bpfwe_swatches_group_text'] ) ) : '';
		if ( $group_text ) {
			update_term_meta( $term_id, 'bpfwe_swatches_group_text', $group_text );
		} else {
			delete_term_meta( $term_id, 'bpfwe_swatches_group_text' );
		}

		switch ( $swatches_type ) {
			case 'color':
				$color = isset( $_POST['bpfwe_swatches_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['bpfwe_swatches_color'] ) ) : '';
				update_term_meta( $term_id, 'bpfwe_swatches_color', $color );
				break;

			case 'image':
				$image = isset( $_POST['bpfwe_swatches_image'] ) ? esc_url_raw( wp_unslash( $_POST['bpfwe_swatches_image'] ) ) : '';
				update_term_meta( $term_id, 'bpfwe_swatches_image', $image );
				break;

			case 'button':
				$button_text = isset( $_POST['bpfwe_swatches_button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['bpfwe_swatches_button_text'] ) ) : '';
				update_term_meta( $term_id, 'bpfwe_swatches_button_text', $button_text );
				break;

			case 'product-cat-image':
				break;

			default:
				delete_term_meta( $term_id, 'bpfwe_swatches_group_text' );
				delete_term_meta( $term_id, 'bpfwe_swatches_color' );
				delete_term_meta( $term_id, 'bpfwe_swatches_image' );
				delete_term_meta( $term_id, 'bpfwe_swatches_button_text' );
				break;
		}
	}
}

new BPFWE_Taxonomy_Swatches();
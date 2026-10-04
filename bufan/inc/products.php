<?php
/**
 * Products: post type, categories, detail fields and admin screens.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the product post type and product categories.
 */
function bufan_register_products() {
	register_post_type(
		'bufan_product',
		array(
			'labels'        => array(
				'name'                  => __( 'Products', 'bufan' ),
				'singular_name'         => __( 'Product', 'bufan' ),
				'menu_name'             => __( 'Products', 'bufan' ),
				'all_items'             => __( 'All products', 'bufan' ),
				'add_new'               => __( 'Add product', 'bufan' ),
				'add_new_item'          => __( 'Add new product', 'bufan' ),
				'edit_item'             => __( 'Edit product', 'bufan' ),
				'new_item'              => __( 'New product', 'bufan' ),
				'view_item'             => __( 'View product', 'bufan' ),
				'view_items'            => __( 'View products', 'bufan' ),
				'search_items'          => __( 'Search products', 'bufan' ),
				'not_found'             => __( 'No products found.', 'bufan' ),
				'not_found_in_trash'    => __( 'No products found in Trash.', 'bufan' ),
				'featured_image'        => __( 'Main product photo', 'bufan' ),
				'set_featured_image'    => __( 'Set main product photo', 'bufan' ),
				'remove_featured_image' => __( 'Remove main product photo', 'bufan' ),
				'use_featured_image'    => __( 'Use as main product photo', 'bufan' ),
				'archives'              => __( 'Products', 'bufan' ),
				'items_list'            => __( 'Products list', 'bufan' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'has_archive'   => 'products',
			'rewrite'       => array(
				'slug'       => 'products',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-products',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'taxonomies'    => array( 'bufan_product_cat' ),
		)
	);

	register_taxonomy(
		'bufan_product_cat',
		'bufan_product',
		array(
			'labels'            => array(
				'name'          => __( 'Product categories', 'bufan' ),
				'singular_name' => __( 'Product category', 'bufan' ),
				'menu_name'     => __( 'Categories', 'bufan' ),
				'all_items'     => __( 'All categories', 'bufan' ),
				'edit_item'     => __( 'Edit category', 'bufan' ),
				'view_item'     => __( 'View category', 'bufan' ),
				'update_item'   => __( 'Update category', 'bufan' ),
				'add_new_item'  => __( 'Add new category', 'bufan' ),
				'new_item_name' => __( 'New category name', 'bufan' ),
				'parent_item'   => __( 'Parent category', 'bufan' ),
				'search_items'  => __( 'Search categories', 'bufan' ),
				'not_found'     => __( 'No categories found.', 'bufan' ),
				'back_to_items' => __( '← Back to categories', 'bufan' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => 'product-category',
				'with_front'   => false,
				'hierarchical' => true,
			),
		)
	);
}
add_action( 'init', 'bufan_register_products' );

/**
 * Specification fields shown in the product's spec table, in display order.
 * All are single-line text; empty fields are hidden on the site.
 *
 * @return array<string, array{label: string, placeholder: string}>
 */
function bufan_product_spec_fields() {
	return array(
		'model'       => array(
			'label'       => __( 'Item No.', 'bufan' ),
			'placeholder' => 'BF-1001',
		),
		'size'        => array(
			'label'       => __( 'Size', 'bufan' ),
			'placeholder' => __( '20 × 14 × 6 cm', 'bufan' ),
		),
		'material'    => array(
			'label'       => __( 'Material', 'bufan' ),
			'placeholder' => __( '12 oz cotton canvas, polyester lining', 'bufan' ),
		),
		'colors'      => array(
			'label'       => __( 'Colors', 'bufan' ),
			'placeholder' => __( 'Natural, black, navy, or Pantone matched', 'bufan' ),
		),
		'embroidery'  => array(
			'label'       => __( 'Embroidery', 'bufan' ),
			'placeholder' => __( 'Flat, 3D puff, chenille', 'bufan' ),
		),
		'moq'         => array(
			'label'       => __( 'MOQ', 'bufan' ),
			'placeholder' => __( '100 pcs per design', 'bufan' ),
		),
		'sample_time' => array(
			'label'       => __( 'Sample time', 'bufan' ),
			'placeholder' => __( '5–7 days', 'bufan' ),
		),
		'lead_time'   => array(
			'label'       => __( 'Production time', 'bufan' ),
			'placeholder' => __( '15–25 days after sample approval', 'bufan' ),
		),
		'packaging'   => array(
			'label'       => __( 'Packaging', 'bufan' ),
			'placeholder' => __( '1 pc per OPP bag, 100 pcs per carton', 'bufan' ),
		),
	);
}

/**
 * Every product meta key the theme stores, without the leading underscore prefix.
 *
 * @return string[]
 */
function bufan_product_meta_keys() {
	return array_merge( array_keys( bufan_product_spec_fields() ), array( 'custom_options', 'extra_specs', 'gallery', 'featured' ) );
}

/**
 * Read a product field.
 *
 * @param int    $post_id Product ID.
 * @param string $key     Field key from bufan_product_meta_keys().
 */
function bufan_product_meta( $post_id, $key ) {
	return (string) get_post_meta( $post_id, '_bufan_' . $key, true );
}

/**
 * Rows for the spec table: fixed fields plus free-form "Label: value" lines.
 *
 * @param int $post_id Product ID.
 * @return array<int, array{key: string, label: string, value: string}>
 */
function bufan_product_specs( $post_id ) {
	$rows = array();
	foreach ( bufan_product_spec_fields() as $key => $field ) {
		$value = bufan_product_meta( $post_id, $key );
		if ( '' !== $value ) {
			$rows[] = array(
				'key'   => $key,
				'label' => $field['label'],
				'value' => $value,
			);
		}
	}
	foreach ( bufan_lines( bufan_product_meta( $post_id, 'extra_specs' ) ) as $line ) {
		$parts = preg_split( '/\s*[:：]\s*/u', $line, 2 );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$rows[] = array(
				'key'   => '',
				'label' => $parts[0],
				'value' => $parts[1],
			);
		}
	}
	return $rows;
}

/**
 * Image IDs for a product: main photo first, then the gallery.
 *
 * @param int $post_id Product ID.
 * @return int[]
 */
function bufan_product_image_ids( $post_id ) {
	$ids   = array();
	$thumb = (int) get_post_thumbnail_id( $post_id );
	if ( $thumb ) {
		$ids[] = $thumb;
	}
	foreach ( explode( ',', bufan_product_meta( $post_id, 'gallery' ) ) as $id ) {
		$id = absint( $id );
		if ( $id && ! in_array( $id, $ids, true ) && wp_attachment_is_image( $id ) ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

/**
 * Main image markup for a product, or the placeholder when it has no photo yet.
 *
 * @param int    $post_id Product ID.
 * @param string $size    Image size.
 * @param array  $attr    Extra attributes.
 */
function bufan_product_image( $post_id, $size = 'bufan-card', $attr = array() ) {
	$ids = bufan_product_image_ids( $post_id );
	if ( $ids ) {
		return wp_get_attachment_image( $ids[0], $size, false, $attr );
	}
	return bufan_placeholder_image();
}

/**
 * Catalogue ordering: manual "Order" field first, newest first after that.
 *
 * @param WP_Query $query Query being prepared.
 */
function bufan_product_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'bufan_product' ) || $query->is_tax( 'bufan_product_cat' ) ) {
		$query->set( 'posts_per_page', 12 );
		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			)
		);
	}
}
add_action( 'pre_get_posts', 'bufan_product_archive_query' );

/*
 * ---------------------------------------------------------------------------
 * Admin: detail fields meta box.
 * ---------------------------------------------------------------------------
 */

/**
 * Products use the classic editor: the block editor folds meta boxes into a collapsed
 * panel, and product entry is mostly form fields (photos, specifications, options).
 *
 * @param bool   $use_block_editor Whether to use the block editor.
 * @param string $post_type        Post type.
 */
function bufan_product_classic_editor( $use_block_editor, $post_type ) {
	return 'bufan_product' === $post_type ? false : $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'bufan_product_classic_editor', 10, 2 );

/**
 * Register the product detail meta box.
 */
function bufan_product_meta_boxes() {
	add_meta_box(
		'bufan-product-details',
		__( 'Product details', 'bufan' ),
		'bufan_render_product_meta_box',
		'bufan_product',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'bufan_product_meta_boxes' );

/**
 * Render the product detail meta box.
 *
 * @param WP_Post $post Product being edited.
 */
function bufan_render_product_meta_box( $post ) {
	wp_nonce_field( 'bufan_save_product', 'bufan_product_nonce' );
	$gallery_ids = array_filter( array_map( 'absint', explode( ',', bufan_product_meta( $post->ID, 'gallery' ) ) ) );
	?>
	<div class="bufan-mb">
		<p class="bufan-mb__intro">
			<?php esc_html_e( 'Fill in what applies — empty fields are hidden on the website. Set the main photo in "Main product photo" on the right, write the full description in the editor above, and a one or two sentence summary in "Excerpt" below (shown under the product title).', 'bufan' ); ?>
		</p>

		<h3 class="bufan-mb__title"><?php esc_html_e( 'Product photos', 'bufan' ); ?></h3>
		<div class="bufan-gallery" data-bufan-gallery>
			<ul class="bufan-gallery__list">
				<?php
				foreach ( $gallery_ids as $id ) :
					$src = wp_get_attachment_image_url( $id, 'thumbnail' );
					if ( ! $src ) {
						continue;
					}
					?>
					<li class="bufan-gallery__item" data-id="<?php echo esc_attr( $id ); ?>">
						<img src="<?php echo esc_url( $src ); ?>" alt="">
						<button type="button" class="bufan-gallery__remove" aria-label="<?php esc_attr_e( 'Remove photo', 'bufan' ); ?>">×</button>
					</li>
				<?php endforeach; ?>
			</ul>
			<input type="hidden" name="bufan_product[gallery]" value="<?php echo esc_attr( implode( ',', $gallery_ids ) ); ?>" data-bufan-gallery-input>
			<button type="button" class="button button-secondary" data-bufan-gallery-add><?php esc_html_e( 'Add photos', 'bufan' ); ?></button>
			<span class="description"><?php esc_html_e( 'Extra photos shown under the main photo. Drag to reorder.', 'bufan' ); ?></span>
		</div>

		<h3 class="bufan-mb__title"><?php esc_html_e( 'Specifications', 'bufan' ); ?></h3>
		<div class="bufan-mb__grid">
			<?php foreach ( bufan_product_spec_fields() as $key => $field ) : ?>
				<p>
					<label for="bufan-<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $field['label'] ); ?></strong></label>
					<input type="text" class="widefat" id="bufan-<?php echo esc_attr( $key ); ?>" name="bufan_product[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( bufan_product_meta( $post->ID, $key ) ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>">
				</p>
			<?php endforeach; ?>
		</div>

		<p>
			<label for="bufan-extra_specs"><strong><?php esc_html_e( 'More specifications', 'bufan' ); ?></strong></label>
			<textarea class="widefat" rows="3" id="bufan-extra_specs" name="bufan_product[extra_specs]" placeholder="<?php echo esc_attr( __( "Zipper: YKK #5 metal\nWeight: 85 g", 'bufan' ) ); ?>"><?php echo esc_textarea( bufan_product_meta( $post->ID, 'extra_specs' ) ); ?></textarea>
			<span class="description"><?php esc_html_e( 'One per line, written as "Name: value".', 'bufan' ); ?></span>
		</p>

		<h3 class="bufan-mb__title"><?php esc_html_e( 'Customization options', 'bufan' ); ?></h3>
		<p>
			<textarea class="widefat" rows="5" id="bufan-custom_options" name="bufan_product[custom_options]" placeholder="<?php echo esc_attr( __( "Your logo embroidered on the front\nCustom fabric color\nPrinted lining\nWoven label and hang tag", 'bufan' ) ); ?>"><?php echo esc_textarea( bufan_product_meta( $post->ID, 'custom_options' ) ); ?></textarea>
			<span class="description"><?php esc_html_e( 'One option per line. Shown as a checklist on the product page.', 'bufan' ); ?></span>
		</p>

		<h3 class="bufan-mb__title"><?php esc_html_e( 'Homepage', 'bufan' ); ?></h3>
		<p>
			<label>
				<input type="checkbox" name="bufan_product[featured]" value="1" <?php checked( '1', bufan_product_meta( $post->ID, 'featured' ) ); ?>>
				<?php esc_html_e( 'Show this product in "Featured products" on the homepage', 'bufan' ); ?>
			</label>
		</p>
	</div>
	<?php
}

/**
 * Save the product detail fields.
 *
 * @param int $post_id Product ID.
 */
function bufan_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['bufan_product_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bufan_product_nonce'] ), 'bufan_save_product' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$input = isset( $_POST['bufan_product'] ) && is_array( $_POST['bufan_product'] ) ? wp_unslash( $_POST['bufan_product'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.

	foreach ( array_keys( bufan_product_spec_fields() ) as $key ) {
		bufan_update_meta( $post_id, $key, isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : '' );
	}
	foreach ( array( 'custom_options', 'extra_specs' ) as $key ) {
		bufan_update_meta( $post_id, $key, isset( $input[ $key ] ) ? sanitize_textarea_field( $input[ $key ] ) : '' );
	}

	$gallery = array();
	foreach ( explode( ',', isset( $input['gallery'] ) ? (string) $input['gallery'] : '' ) as $id ) {
		$id = absint( $id );
		if ( $id && 'attachment' === get_post_type( $id ) ) {
			$gallery[] = $id;
		}
	}
	bufan_update_meta( $post_id, 'gallery', implode( ',', array_unique( $gallery ) ) );
	bufan_update_meta( $post_id, 'featured', empty( $input['featured'] ) ? '' : '1' );
}
add_action( 'save_post_bufan_product', 'bufan_save_product_meta' );

/**
 * Store a product field, deleting it when empty so the database stays clean.
 *
 * @param int    $post_id Product ID.
 * @param string $key     Field key.
 * @param string $value   Sanitized value.
 */
function bufan_update_meta( $post_id, $key, $value ) {
	if ( '' === $value ) {
		delete_post_meta( $post_id, '_bufan_' . $key );
	} else {
		update_post_meta( $post_id, '_bufan_' . $key, $value );
	}
}

/**
 * Media library and gallery script on the product edit screen.
 *
 * @param string $hook Current admin page.
 */
function bufan_product_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || 'bufan_product' !== $screen->post_type ) {
		return;
	}
	bufan_enqueue_admin_assets( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) );
}
add_action( 'admin_enqueue_scripts', 'bufan_product_admin_assets' );

/**
 * Product list columns: photo, item number and homepage flag.
 *
 * @param string[] $columns Existing columns.
 */
function bufan_product_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['bufan_thumb'] = __( 'Photo', 'bufan' );
		}
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['bufan_model']    = __( 'Item No.', 'bufan' );
			$new['bufan_featured'] = __( 'Homepage', 'bufan' );
		}
	}
	return $new;
}
add_filter( 'manage_bufan_product_posts_columns', 'bufan_product_columns' );

/**
 * Output product list column values.
 *
 * @param string $column  Column key.
 * @param int    $post_id Product ID.
 */
function bufan_product_column_values( $column, $post_id ) {
	switch ( $column ) {
		case 'bufan_thumb':
			$ids = bufan_product_image_ids( $post_id );
			echo $ids ? wp_get_attachment_image( $ids[0], array( 56, 56 ) ) : '—'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;
		case 'bufan_model':
			echo esc_html( bufan_product_meta( $post_id, 'model' ) ?: '—' );
			break;
		case 'bufan_featured':
			echo bufan_product_meta( $post_id, 'featured' ) ? '★' : '';
			break;
	}
}
add_action( 'manage_bufan_product_posts_custom_column', 'bufan_product_column_values', 10, 2 );

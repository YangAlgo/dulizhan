<?php
/**
 * Homepage fields: the hero banner, key numbers and factory introduction.
 *
 * They are edited on the page set as the homepage (Pages → Home), so the English
 * and Chinese homepages each keep their own text.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default homepage text, used for any field left empty.
 *
 * @return array<string, string>
 */
function bufan_home_defaults() {
	return array(
		'hero_eyebrow' => __( 'Canvas embroidery factory · OEM & ODM', 'bufan' ),
		'hero_title'   => __( 'Custom embroidered canvas bags, made in our own factory', 'bufan' ),
		'hero_text'    => __( 'Cosmetic pouches, coin purses, pencil cases and totes — embroidered with your design and made to order for brands, shops and events in Europe and North America.', 'bufan' ),
		'stat1_value'  => __( 'OEM & ODM', 'bufan' ),
		'stat1_label'  => __( 'Your design or ours', 'bufan' ),
		'stat2_value'  => __( 'In-house', 'bufan' ),
		'stat2_label'  => __( 'Embroidery, cutting and sewing', 'bufan' ),
		'stat3_value'  => __( 'Sample first', 'bufan' ),
		'stat3_label'  => __( 'Approve a sample before bulk production', 'bufan' ),
		'stat4_value'  => __( 'EU & US', 'bufan' ),
		'stat4_label'  => __( 'Export packing and shipping', 'bufan' ),
		'intro_title'  => __( 'A family factory that lives and breathes embroidery', 'bufan' ),
	);
}

/**
 * Homepage field keys stored as post meta (with the _bufan_ prefix).
 *
 * @return string[]
 */
function bufan_home_meta_keys() {
	return array_merge( array_keys( bufan_home_defaults() ), array( 'hero_image', 'intro_image' ) );
}

/**
 * ID of the page shown as the homepage, in the current language. 0 when the site shows latest posts.
 */
function bufan_front_page_id() {
	if ( 'page' !== get_option( 'show_on_front' ) ) {
		return 0;
	}
	return bufan_translated_post_id( (int) get_option( 'page_on_front' ) );
}

/**
 * A homepage field for the current language, falling back to the default text.
 *
 * @param string $key Field key.
 */
function bufan_home_field( $key ) {
	$page_id = bufan_front_page_id();
	$value   = $page_id ? (string) get_post_meta( $page_id, '_bufan_' . $key, true ) : '';
	if ( '' === $value ) {
		$defaults = bufan_home_defaults();
		$value    = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}
	return $value;
}

/**
 * Whether a page is the homepage or a translation of it.
 *
 * @param int $post_id Page ID.
 */
function bufan_is_front_page_id( $post_id ) {
	$front = (int) get_option( 'page_on_front' );
	if ( ! $front || 'page' !== get_option( 'show_on_front' ) ) {
		return false;
	}
	if ( (int) $post_id === $front ) {
		return true;
	}
	if ( function_exists( 'pll_get_post_translations' ) ) {
		return in_array( (int) $post_id, array_map( 'intval', pll_get_post_translations( $front ) ), true );
	}
	return false;
}

/**
 * The homepage uses the classic editor so its banner and key-fact fields are visible
 * right under the text, instead of in the block editor's collapsed meta box panel.
 *
 * @param bool    $use_block_editor Whether to use the block editor.
 * @param WP_Post $post             Post being edited.
 */
function bufan_home_classic_editor( $use_block_editor, $post ) {
	return $post && 'page' === $post->post_type && bufan_is_front_page_id( $post->ID ) ? false : $use_block_editor;
}
add_filter( 'use_block_editor_for_post', 'bufan_home_classic_editor', 10, 2 );

/**
 * Add the homepage meta box when editing the homepage.
 *
 * @param WP_Post $post Page being edited.
 */
function bufan_home_meta_box( $post ) {
	if ( bufan_is_front_page_id( $post->ID ) ) {
		add_meta_box( 'bufan-home', __( 'Homepage sections', 'bufan' ), 'bufan_render_home_meta_box', 'page', 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes_page', 'bufan_home_meta_box' );

/**
 * Render the homepage meta box.
 *
 * @param WP_Post $post Homepage.
 */
function bufan_render_home_meta_box( $post ) {
	wp_nonce_field( 'bufan_save_home', 'bufan_home_nonce' );
	$defaults = bufan_home_defaults();
	$value    = function ( $key ) use ( $post ) {
		return (string) get_post_meta( $post->ID, '_bufan_' . $key, true );
	};
	?>
	<div class="bufan-mb">
		<p class="bufan-mb__intro"><?php esc_html_e( 'Leave a field empty to use the suggested text shown in grey. The text in the editor above appears in the "About us" part of the homepage.', 'bufan' ); ?></p>

		<h3 class="bufan-mb__title"><?php esc_html_e( 'Top banner', 'bufan' ); ?></h3>
		<p>
			<label for="bufan-hero_eyebrow"><strong><?php esc_html_e( 'Small line above the title', 'bufan' ); ?></strong></label>
			<input type="text" class="widefat" id="bufan-hero_eyebrow" name="bufan_home[hero_eyebrow]" value="<?php echo esc_attr( $value( 'hero_eyebrow' ) ); ?>" placeholder="<?php echo esc_attr( $defaults['hero_eyebrow'] ); ?>">
		</p>
		<p>
			<label for="bufan-hero_title"><strong><?php esc_html_e( 'Main title', 'bufan' ); ?></strong></label>
			<input type="text" class="widefat" id="bufan-hero_title" name="bufan_home[hero_title]" value="<?php echo esc_attr( $value( 'hero_title' ) ); ?>" placeholder="<?php echo esc_attr( $defaults['hero_title'] ); ?>">
		</p>
		<p>
			<label for="bufan-hero_text"><strong><?php esc_html_e( 'Text under the title', 'bufan' ); ?></strong></label>
			<textarea class="widefat" rows="3" id="bufan-hero_text" name="bufan_home[hero_text]" placeholder="<?php echo esc_attr( $defaults['hero_text'] ); ?>"><?php echo esc_textarea( $value( 'hero_text' ) ); ?></textarea>
		</p>
		<?php bufan_admin_image_field( 'bufan_home[hero_image]', $value( 'hero_image' ), __( 'Banner photo', 'bufan' ), __( 'A bright photo of your best products, at least 1200 px wide. Without a photo an illustration is shown.', 'bufan' ) ); ?>

		<h3 class="bufan-mb__title"><?php esc_html_e( 'Key facts (4 boxes under the banner)', 'bufan' ); ?></h3>
		<p class="description"><?php esc_html_e( 'Real numbers convince buyers: years in business, number of embroidery machines, monthly capacity, MOQ.', 'bufan' ); ?></p>
		<div class="bufan-mb__grid bufan-mb__grid--4">
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<p>
					<label for="bufan-stat<?php echo (int) $i; ?>_value"><strong>
						<?php
						/* translators: %d: box number 1-4 */
						echo esc_html( sprintf( __( 'Box %d — big text', 'bufan' ), $i ) );
						?>
					</strong></label>
					<input type="text" class="widefat" id="bufan-stat<?php echo (int) $i; ?>_value" name="bufan_home[stat<?php echo (int) $i; ?>_value]" value="<?php echo esc_attr( $value( 'stat' . $i . '_value' ) ); ?>" placeholder="<?php echo esc_attr( $defaults[ 'stat' . $i . '_value' ] ); ?>">
					<input type="text" class="widefat" name="bufan_home[stat<?php echo (int) $i; ?>_label]" value="<?php echo esc_attr( $value( 'stat' . $i . '_label' ) ); ?>" placeholder="<?php echo esc_attr( $defaults[ 'stat' . $i . '_label' ] ); ?>" aria-label="<?php esc_attr_e( 'Small text', 'bufan' ); ?>">
				</p>
			<?php endfor; ?>
		</div>

		<h3 class="bufan-mb__title"><?php esc_html_e( 'About us section', 'bufan' ); ?></h3>
		<p>
			<label for="bufan-intro_title"><strong><?php esc_html_e( 'Section title', 'bufan' ); ?></strong></label>
			<input type="text" class="widefat" id="bufan-intro_title" name="bufan_home[intro_title]" value="<?php echo esc_attr( $value( 'intro_title' ) ); ?>" placeholder="<?php echo esc_attr( $defaults['intro_title'] ); ?>">
		</p>
		<?php bufan_admin_image_field( 'bufan_home[intro_image]', $value( 'intro_image' ), __( 'Factory photo', 'bufan' ), __( 'A real photo of your workshop or embroidery machines.', 'bufan' ) ); ?>
	</div>
	<?php
}

/**
 * Image picker used in meta boxes.
 *
 * @param string $name  Input name.
 * @param string $value Attachment ID.
 * @param string $label Field label.
 * @param string $help  Help text.
 */
function bufan_admin_image_field( $name, $value, $label, $help = '' ) {
	$src = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
	?>
	<div class="bufan-image-field" data-bufan-image>
		<p><strong><?php echo esc_html( $label ); ?></strong></p>
		<img src="<?php echo esc_url( $src ); ?>" alt="" class="bufan-image-field__preview" <?php echo $src ? '' : 'hidden'; ?>>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
		<button type="button" class="button" data-bufan-image-select><?php esc_html_e( 'Choose image', 'bufan' ); ?></button>
		<button type="button" class="button-link button-link-delete" data-bufan-image-remove <?php echo $src ? '' : 'hidden'; ?>><?php esc_html_e( 'Remove', 'bufan' ); ?></button>
		<?php if ( $help ) : ?>
			<p class="description"><?php echo esc_html( $help ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Save homepage fields.
 *
 * @param int $post_id Page ID.
 */
function bufan_save_home_meta( $post_id ) {
	if ( ! isset( $_POST['bufan_home_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bufan_home_nonce'] ), 'bufan_save_home' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$input = isset( $_POST['bufan_home'] ) && is_array( $_POST['bufan_home'] ) ? wp_unslash( $_POST['bufan_home'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	foreach ( bufan_home_meta_keys() as $key ) {
		$raw = isset( $input[ $key ] ) ? $input[ $key ] : '';
		if ( in_array( $key, array( 'hero_image', 'intro_image' ), true ) ) {
			$value = absint( $raw ) ? (string) absint( $raw ) : '';
		} elseif ( 'hero_text' === $key ) {
			$value = sanitize_textarea_field( $raw );
		} else {
			$value = sanitize_text_field( $raw );
		}
		bufan_update_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post_page', 'bufan_save_home_meta' );

/**
 * Media picker on the homepage edit screen.
 *
 * @param string $hook Current admin page.
 */
function bufan_home_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id || ! bufan_is_front_page_id( $post_id ) ) {
		return;
	}
	bufan_enqueue_admin_assets( true );
}
add_action( 'admin_enqueue_scripts', 'bufan_home_admin_assets' );

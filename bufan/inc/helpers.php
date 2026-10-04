<?php
/**
 * Small helpers shared by templates and admin code.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether Polylang is active and has at least one language.
 */
function bufan_has_polylang() {
	return function_exists( 'pll_languages_list' ) && function_exists( 'pll_current_language' ) && pll_languages_list();
}

/**
 * Two-letter code of the language being displayed: 'en' or 'zh'.
 *
 * Falls back to the site locale when Polylang is not installed.
 */
function bufan_lang() {
	if ( bufan_has_polylang() ) {
		$lang = pll_current_language();
		if ( ! $lang && function_exists( 'pll_default_language' ) ) {
			$lang = pll_default_language();
		}
		if ( $lang ) {
			return 0 === strpos( $lang, 'zh' ) ? 'zh' : 'en';
		}
	}
	return 0 === strpos( determine_locale(), 'zh' ) ? 'zh' : 'en';
}

/**
 * Read one value from the Bufan settings page.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Value used when the setting is empty.
 */
function bufan_option( $key, $default = '' ) {
	$options = get_option( 'bufan_settings', array() );
	if ( isset( $options[ $key ] ) && '' !== $options[ $key ] ) {
		return $options[ $key ];
	}
	return $default;
}

/**
 * Read a setting that has an English and a Chinese version ("{$key}_en" / "{$key}_zh").
 * Uses the version for the current language, then falls back to English.
 *
 * @param string $key     Setting key without the language suffix.
 * @param mixed  $default Value used when both versions are empty.
 */
function bufan_option_i18n( $key, $default = '' ) {
	$value = bufan_option( $key . '_' . bufan_lang() );
	if ( '' === $value ) {
		$value = bufan_option( $key . '_en' );
	}
	return '' === $value ? $default : $value;
}

/**
 * Translate a post ID into the current language when Polylang is active.
 *
 * @param int $post_id Post ID in any language.
 */
function bufan_translated_post_id( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id && function_exists( 'pll_get_post' ) && bufan_has_polylang() ) {
		$translated = pll_get_post( $post_id );
		if ( $translated ) {
			return (int) $translated;
		}
	}
	return $post_id;
}

/**
 * ID of a page created by the setup wizard (about, customization, faq, contact, privacy, blog),
 * in the current language. Returns 0 when the page does not exist.
 *
 * @param string $key Page key.
 */
function bufan_page_id( $key ) {
	$pages = get_option( 'bufan_pages', array() );
	if ( empty( $pages[ $key ] ) ) {
		return 0;
	}
	$id = bufan_translated_post_id( $pages[ $key ] );
	return 'publish' === get_post_status( $id ) ? $id : 0;
}

/**
 * URL of a setup-wizard page, or '' when it does not exist.
 *
 * @param string $key Page key.
 */
function bufan_page_url( $key ) {
	$id = bufan_page_id( $key );
	return $id ? get_permalink( $id ) : '';
}

/**
 * Where "Get a quote" buttons point: the contact page, or the inquiry form on the current page.
 */
function bufan_quote_url() {
	$url = bufan_page_url( 'contact' );
	return $url ? $url . '#inquiry' : '#inquiry';
}

/**
 * URL of the product catalogue in the current language.
 */
function bufan_products_url() {
	$url = get_post_type_archive_link( 'bufan_product' );
	return $url ? $url : home_url( '/' );
}

/**
 * Home URL in the current language.
 */
function bufan_home_url() {
	return function_exists( 'pll_home_url' ) && bufan_has_polylang() ? pll_home_url() : home_url( '/' );
}

/**
 * WhatsApp click-to-chat link, or '' when no number is configured.
 *
 * @param string $text Optional pre-filled message.
 */
function bufan_whatsapp_url( $text = '' ) {
	$number = preg_replace( '/\D+/', '', (string) bufan_option( 'whatsapp' ) );
	if ( ! $number ) {
		return '';
	}
	$url = 'https://wa.me/' . $number;
	if ( $text ) {
		$url .= '?text=' . rawurlencode( $text );
	}
	return $url;
}

/**
 * Split a textarea value into trimmed, non-empty lines.
 *
 * @param string $text Raw text.
 * @return string[]
 */
function bufan_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
}

/**
 * Cache-busting version for a theme asset.
 *
 * @param string $path Path relative to the theme directory.
 */
function bufan_asset_version( $path ) {
	$file = BUFAN_DIR . '/' . $path;
	return file_exists( $file ) ? BUFAN_VERSION . '.' . filemtime( $file ) : BUFAN_VERSION;
}

/**
 * Markup for the built-in placeholder image used when a product has no photo yet.
 *
 * @param string $class Extra CSS class.
 */
function bufan_placeholder_image( $class = '' ) {
	return sprintf(
		'<img src="%s" alt="" class="bufan-placeholder %s" width="640" height="640" loading="lazy" decoding="async">',
		esc_url( BUFAN_URI . '/assets/img/placeholder.svg' ),
		esc_attr( $class )
	);
}

/**
 * Admin stylesheet and script (media pickers, product gallery).
 *
 * @param bool $media Also load the media library and sortable lists.
 */
function bufan_enqueue_admin_assets( $media = false ) {
	wp_enqueue_style( 'bufan-admin', BUFAN_URI . '/assets/css/admin.css', array(), bufan_asset_version( 'assets/css/admin.css' ) );
	if ( ! $media ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'bufan-admin', BUFAN_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), bufan_asset_version( 'assets/js/admin.js' ), true );
	wp_localize_script(
		'bufan-admin',
		'bufanAdmin',
		array(
			'galleryTitle'  => __( 'Product photos', 'bufan' ),
			'galleryButton' => __( 'Add to product', 'bufan' ),
			'remove'        => __( 'Remove photo', 'bufan' ),
			'imageTitle'    => __( 'Choose image', 'bufan' ),
			'imageButton'   => __( 'Use this image', 'bufan' ),
		)
	);
}

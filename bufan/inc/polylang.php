<?php
/**
 * Polylang integration: English / Chinese versions of products, categories and pages.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Always make products translatable (and never inquiries).
 *
 * @param string[] $post_types  Translatable post types.
 * @param bool     $is_settings True when Polylang builds its settings screen.
 */
function bufan_pll_post_types( $post_types, $is_settings ) {
	if ( ! $is_settings ) {
		$post_types['bufan_product'] = 'bufan_product';
	}
	unset( $post_types['bufan_inquiry'] );
	return $post_types;
}
add_filter( 'pll_get_post_types', 'bufan_pll_post_types', 10, 2 );

/**
 * Always make product categories translatable.
 *
 * @param string[] $taxonomies  Translatable taxonomies.
 * @param bool     $is_settings True when Polylang builds its settings screen.
 */
function bufan_pll_taxonomies( $taxonomies, $is_settings ) {
	if ( ! $is_settings ) {
		$taxonomies['bufan_product_cat'] = 'bufan_product_cat';
	}
	return $taxonomies;
}
add_filter( 'pll_get_taxonomies', 'bufan_pll_taxonomies', 10, 2 );

/**
 * Product fields copied when a translation is created, and kept in sync afterwards.
 *
 * When you add the Chinese version of a product, every field is copied from the English one
 * so only the words need changing. After that, photos, item number and the homepage flag stay
 * identical in both languages; text fields can differ.
 *
 * @param string[] $keys Meta keys.
 * @param bool     $sync True when synchronizing, false when copying to a new translation.
 */
function bufan_pll_copy_metas( $keys, $sync ) {
	$always = array( '_bufan_gallery', '_bufan_model', '_bufan_featured', '_bufan_hero_image', '_bufan_intro_image' );
	if ( $sync ) {
		return array_merge( $keys, $always );
	}
	$all = array();
	foreach ( array_merge( bufan_product_meta_keys(), bufan_home_meta_keys() ) as $key ) {
		$all[] = '_bufan_' . $key;
	}
	return array_merge( $keys, $all );
}
add_filter( 'pll_copy_post_metas', 'bufan_pll_copy_metas', 10, 2 );

/**
 * Languages for the switcher.
 *
 * @return array<int, array{name: string, short: string, url: string, current: bool}>
 */
function bufan_languages() {
	if ( ! bufan_has_polylang() || ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}
	$raw = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 0,
		)
	);
	if ( ! is_array( $raw ) || count( $raw ) < 2 ) {
		return array();
	}
	$languages = array();
	foreach ( $raw as $lang ) {
		$is_zh       = 0 === strpos( $lang['slug'], 'zh' );
		$languages[] = array(
			'name'    => $lang['name'],
			'short'   => $is_zh ? '中文' : strtoupper( substr( $lang['slug'], 0, 2 ) ),
			'locale'  => isset( $lang['locale'] ) ? str_replace( '_', '-', $lang['locale'] ) : $lang['slug'],
			'url'     => $lang['url'],
			'current' => ! empty( $lang['current_lang'] ),
		);
	}
	return $languages;
}

/**
 * Remind administrators to install Polylang on Bufan screens and the dashboard.
 */
function bufan_polylang_notice() {
	if ( function_exists( 'pll_languages_list' ) || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'toplevel_page_bufan', 'bufan_page_bufan-setup', 'themes' ), true ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'Bufan theme: install and activate the free "Polylang" plugin to get the English and Chinese versions of the site.', 'bufan' ),
		esc_url( admin_url( 'plugin-install.php?s=polylang&tab=search&type=term' ) ),
		esc_html__( 'Install Polylang', 'bufan' )
	);
}
add_action( 'admin_notices', 'bufan_polylang_notice' );

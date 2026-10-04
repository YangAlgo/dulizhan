<?php
/**
 * Theme supports, menus, assets and small front-end tweaks.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function bufan_setup() {
	load_theme_textdomain( 'bufan', BUFAN_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Main menu', 'bufan' ),
			'footer'  => __( 'Footer menu', 'bufan' ),
		)
	);

	add_image_size( 'bufan-card', 640, 640, true );
}
add_action( 'after_setup_theme', 'bufan_setup' );

/**
 * Content width for embeds.
 */
function bufan_content_width() {
	$GLOBALS['content_width'] = 760;
}
add_action( 'after_setup_theme', 'bufan_content_width', 0 );

/**
 * Front-end styles and scripts.
 */
function bufan_assets() {
	wp_enqueue_style( 'bufan', BUFAN_URI . '/assets/css/main.css', array(), bufan_asset_version( 'assets/css/main.css' ) );
	wp_enqueue_script(
		'bufan',
		BUFAN_URI . '/assets/js/main.js',
		array(),
		bufan_asset_version( 'assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_localize_script(
		'bufan',
		'bufanI18n',
		array(
			'menu'  => __( 'Menu', 'bufan' ),
			'close' => __( 'Close', 'bufan' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bufan_assets' );

/**
 * Preload the body and heading fonts so text renders without a visible swap.
 */
function bufan_preload_fonts() {
	foreach ( array( 'inter-var.woff2', 'fraunces-var.woff2' ) as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( BUFAN_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'bufan_preload_fonts', 2 );

// Emoji detection script is not needed on a catalogue site.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Close comments and pingbacks site-wide: a B2B catalogue gets only spam through them.
 */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

/**
 * Add a language class to <body> so styles can tune Chinese typography.
 *
 * @param string[] $classes Body classes.
 */
function bufan_body_class( $classes ) {
	$classes[] = 'lang-' . bufan_lang();
	return $classes;
}
add_filter( 'body_class', 'bufan_body_class' );

/**
 * Shorter excerpts with a plain ellipsis.
 */
add_filter(
	'excerpt_length',
	function () {
		return 'zh' === bufan_lang() ? 60 : 28;
	}
);
add_filter(
	'excerpt_more',
	function () {
		return '…';
	}
);

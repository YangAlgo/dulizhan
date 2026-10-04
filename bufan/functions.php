<?php
/**
 * Bufan theme bootstrap.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

define( 'BUFAN_VERSION', '1.0.0' );
define( 'BUFAN_DIR', get_template_directory() );
define( 'BUFAN_URI', get_template_directory_uri() );

require BUFAN_DIR . '/inc/helpers.php';
require BUFAN_DIR . '/inc/setup.php';
require BUFAN_DIR . '/inc/icons.php';
require BUFAN_DIR . '/inc/products.php';
require BUFAN_DIR . '/inc/inquiries.php';
require BUFAN_DIR . '/inc/settings.php';
require BUFAN_DIR . '/inc/home-fields.php';
require BUFAN_DIR . '/inc/polylang.php';
require BUFAN_DIR . '/inc/template-tags.php';

if ( is_admin() ) {
	require BUFAN_DIR . '/inc/starter-content.php';
	require BUFAN_DIR . '/inc/setup-wizard.php';
}

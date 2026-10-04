<?php
/**
 * Staff accounts: the "Product editor" role for employees who list products.
 *
 * Product editors can add and edit products, product categories and photos.
 * They cannot see inquiries, pages, blog posts, plugins or any settings.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bump when the capability list changes, so roles are updated once.
 */
const BUFAN_ROLES_VERSION = 1;

/**
 * Capabilities for managing products (post type capability_type "bufan_product").
 *
 * @return string[]
 */
function bufan_product_caps() {
	return array(
		'edit_bufan_products',
		'edit_others_bufan_products',
		'edit_published_bufan_products',
		'edit_private_bufan_products',
		'publish_bufan_products',
		'read_private_bufan_products',
		'delete_bufan_products',
		'delete_others_bufan_products',
		'delete_published_bufan_products',
		'delete_private_bufan_products',
	);
}

/**
 * Create the Product editor role and give administrators and editors the product capabilities.
 * Runs once per BUFAN_ROLES_VERSION.
 */
function bufan_install_roles() {
	if ( (int) get_option( 'bufan_roles_version' ) === BUFAN_ROLES_VERSION ) {
		return;
	}

	foreach ( array( 'administrator', 'editor' ) as $role_name ) {
		$role = get_role( $role_name );
		if ( $role ) {
			foreach ( bufan_product_caps() as $cap ) {
				$role->add_cap( $cap );
			}
		}
	}

	remove_role( 'bufan_product_editor' );
	add_role(
		'bufan_product_editor',
		'Product editor',
		array_merge(
			array(
				'read'         => true,
				'upload_files' => true,
			),
			array_fill_keys( bufan_product_caps(), true )
		)
	);

	update_option( 'bufan_roles_version', BUFAN_ROLES_VERSION );
}
add_action( 'init', 'bufan_install_roles', 5 );

/**
 * Show the role name in the admin language.
 *
 * @param string $name Role name.
 */
function bufan_translate_role_name( $name ) {
	return 'Product editor' === $name ? __( 'Product editor', 'bufan' ) : $name;
}
add_filter( 'translate_user_role', 'bufan_translate_role_name' );

/**
 * Whether the current user is a Product editor (not an administrator or editor).
 */
function bufan_is_product_editor() {
	$user = wp_get_current_user();
	return $user && in_array( 'bufan_product_editor', (array) $user->roles, true ) && ! current_user_can( 'edit_pages' );
}

/**
 * Send Product editors straight to the product list after logging in.
 *
 * @param string           $redirect_to Default destination.
 * @param string           $requested   Requested destination.
 * @param WP_User|WP_Error $user        User logging in.
 */
function bufan_product_editor_login_redirect( $redirect_to, $requested, $user ) {
	if ( $user instanceof WP_User && in_array( 'bufan_product_editor', (array) $user->roles, true ) && ! $user->has_cap( 'edit_pages' ) ) {
		return admin_url( 'edit.php?post_type=bufan_product' );
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'bufan_product_editor_login_redirect', 10, 3 );

/**
 * Keep the Product editor's admin menu to what they use: products, media and their profile.
 */
function bufan_product_editor_menu() {
	if ( ! bufan_is_product_editor() ) {
		return;
	}
	remove_menu_page( 'index.php' );
	remove_menu_page( 'tools.php' );
}
add_action( 'admin_menu', 'bufan_product_editor_menu', 999 );

/**
 * The dashboard is hidden from Product editors; send them to their products instead.
 */
function bufan_product_editor_dashboard_redirect() {
	global $pagenow;
	if ( 'index.php' === $pagenow && bufan_is_product_editor() ) {
		wp_safe_redirect( admin_url( 'edit.php?post_type=bufan_product' ) );
		exit;
	}
}
add_action( 'admin_init', 'bufan_product_editor_dashboard_redirect' );

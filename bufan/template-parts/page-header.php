<?php
/**
 * Page title band with breadcrumbs.
 *
 * Arguments: title (string), lead (string), eyebrow (string).
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

$bufan_args = wp_parse_args(
	isset( $args ) ? $args : array(),
	array(
		'title'   => '',
		'lead'    => '',
		'eyebrow' => '',
	)
);
?>
<header class="page-header">
	<div class="container">
		<?php bufan_breadcrumbs(); ?>
		<?php if ( $bufan_args['eyebrow'] ) : ?>
			<p class="eyebrow"><?php echo esc_html( $bufan_args['eyebrow'] ); ?></p>
		<?php endif; ?>
		<h1 class="page-header__title"><?php echo esc_html( $bufan_args['title'] ); ?></h1>
		<?php if ( $bufan_args['lead'] ) : ?>
			<p class="page-header__lead"><?php echo esc_html( $bufan_args['lead'] ); ?></p>
		<?php endif; ?>
	</div>
</header>

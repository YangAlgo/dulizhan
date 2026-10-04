<?php
/**
 * Page not found.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="section not-found">
	<div class="container container--narrow">
		<p class="not-found__code">404</p>
		<h1 class="section-title"><?php esc_html_e( 'This page has come unstitched', 'bufan' ); ?></h1>
		<p class="section-lead"><?php esc_html_e( 'The page you are looking for does not exist or has moved.', 'bufan' ); ?></p>
		<p class="not-found__actions">
			<a class="btn btn--primary" href="<?php echo esc_url( bufan_home_url() ); ?>"><?php esc_html_e( 'Back to homepage', 'bufan' ); ?></a>
			<a class="btn btn--outline" href="<?php echo esc_url( bufan_products_url() ); ?>"><?php esc_html_e( 'View products', 'bufan' ); ?></a>
		</p>
	</div>
</section>
<?php
get_footer();

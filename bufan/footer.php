<?php
/**
 * Site footer.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

$bufan_terms = get_terms(
	array(
		'taxonomy'   => 'bufan_product_cat',
		'hide_empty' => false,
		'parent'     => 0,
		'number'     => 8,
	)
);
$bufan_privacy = bufan_page_url( 'privacy' );
?>
</main>

<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<?php bufan_logo(); ?>
			<?php $bufan_tagline = bufan_option_i18n( 'tagline' ); ?>
			<?php if ( $bufan_tagline ) : ?>
				<p class="site-footer__tagline"><?php echo esc_html( $bufan_tagline ); ?></p>
			<?php endif; ?>
			<?php bufan_social_links(); ?>
		</div>

		<div class="site-footer__col">
			<h2 class="site-footer__title"><?php esc_html_e( 'Products', 'bufan' ); ?></h2>
			<ul class="footer-menu">
				<?php if ( ! is_wp_error( $bufan_terms ) ) : ?>
					<?php foreach ( $bufan_terms as $bufan_term ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $bufan_term ) ); ?>"><?php echo esc_html( $bufan_term->name ); ?></a></li>
					<?php endforeach; ?>
				<?php endif; ?>
				<li><a href="<?php echo esc_url( bufan_products_url() ); ?>"><?php esc_html_e( 'All products', 'bufan' ); ?></a></li>
			</ul>
		</div>

		<div class="site-footer__col">
			<h2 class="site-footer__title"><?php esc_html_e( 'Company', 'bufan' ); ?></h2>
			<?php bufan_nav( 'footer', 'footer-menu' ); ?>
		</div>

		<div class="site-footer__col">
			<h2 class="site-footer__title"><?php esc_html_e( 'Contact', 'bufan' ); ?></h2>
			<?php bufan_contact_list( 'contact-list contact-list--footer' ); ?>
			<a class="btn btn--light btn--sm" href="<?php echo esc_url( bufan_quote_url() ); ?>"><?php esc_html_e( 'Send an inquiry', 'bufan' ); ?></a>
		</div>
	</div>

	<div class="container site-footer__bottom">
		<p>
			&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
			<?php esc_html_e( 'All rights reserved.', 'bufan' ); ?>
		</p>
		<?php if ( $bufan_privacy ) : ?>
			<p><a href="<?php echo esc_url( $bufan_privacy ); ?>"><?php echo esc_html( get_the_title( bufan_page_id( 'privacy' ) ) ); ?></a></p>
		<?php endif; ?>
	</div>
</footer>

<?php
/* translators: %s: site name */
$bufan_wa_url = bufan_whatsapp_url( sprintf( __( 'Hello %s, I would like to ask about your products.', 'bufan' ), get_bloginfo( 'name' ) ) );
if ( $bufan_wa_url && '1' === bufan_option( 'whatsapp_button', '1' ) ) :
	?>
	<a class="wa-float" href="<?php echo esc_url( $bufan_wa_url ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'bufan' ); ?>">
		<?php echo bufan_icon( 'whatsapp', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>

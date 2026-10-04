<?php
/**
 * "Ready to start?" band shown at the end of pages.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cta-band">
	<div class="container cta-band__inner">
		<div>
			<h2 class="cta-band__title"><?php esc_html_e( 'Have a design in mind?', 'bufan' ); ?></h2>
			<p class="cta-band__text"><?php esc_html_e( 'Send us your artwork and quantity — we will reply with ideas, a sample plan and a quotation.', 'bufan' ); ?></p>
		</div>
		<div class="cta-band__actions">
			<a class="btn btn--primary btn--lg" href="<?php echo esc_url( bufan_quote_url() ); ?>"><?php esc_html_e( 'Get a quote', 'bufan' ); ?> <?php echo bufan_icon( 'arrow-right', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php $bufan_wa = bufan_whatsapp_url(); ?>
			<?php if ( $bufan_wa ) : ?>
				<a class="btn btn--outline btn--lg" href="<?php echo esc_url( $bufan_wa ); ?>" target="_blank" rel="noopener"><?php echo bufan_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> WhatsApp</a>
			<?php endif; ?>
		</div>
	</div>
</section>

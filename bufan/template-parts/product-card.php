<?php
/**
 * Product card used in grids.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

$bufan_id    = get_the_ID();
$bufan_terms = get_the_terms( $bufan_id, 'bufan_product_cat' );
$bufan_moq   = bufan_product_meta( $bufan_id, 'moq' );
$bufan_model = bufan_product_meta( $bufan_id, 'model' );
?>
<article class="product-card">
	<div class="product-card__media">
		<?php echo bufan_product_image( $bufan_id, 'bufan-card', array( 'sizes' => '(min-width: 1100px) 300px, (min-width: 700px) 33vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="product-card__body">
		<?php if ( $bufan_terms && ! is_wp_error( $bufan_terms ) ) : ?>
			<p class="product-card__cat"><?php echo esc_html( $bufan_terms[0]->name ); ?></p>
		<?php endif; ?>
		<h3 class="product-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $bufan_moq || $bufan_model ) : ?>
			<dl class="product-card__meta">
				<?php if ( $bufan_model ) : ?>
					<div><dt><?php esc_html_e( 'Item No.', 'bufan' ); ?></dt><dd><?php echo esc_html( $bufan_model ); ?></dd></div>
				<?php endif; ?>
				<?php if ( $bufan_moq ) : ?>
					<div><dt><?php esc_html_e( 'MOQ', 'bufan' ); ?></dt><dd><?php echo esc_html( $bufan_moq ); ?></dd></div>
				<?php endif; ?>
			</dl>
		<?php endif; ?>
		<span class="product-card__more" aria-hidden="true"><?php esc_html_e( 'View details', 'bufan' ); ?> <?php echo bufan_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	</div>
</article>

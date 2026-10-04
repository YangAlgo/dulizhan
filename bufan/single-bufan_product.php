<?php
/**
 * Single product.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$bufan_id      = get_the_ID();
	$bufan_images  = bufan_product_image_ids( $bufan_id );
	$bufan_specs   = bufan_product_specs( $bufan_id );
	$bufan_options = bufan_lines( bufan_product_meta( $bufan_id, 'custom_options' ) );
	$bufan_terms   = get_the_terms( $bufan_id, 'bufan_product_cat' );
	$bufan_model   = bufan_product_meta( $bufan_id, 'model' );
	/* translators: 1: product name, 2: product URL */
	$bufan_wa = bufan_whatsapp_url( sprintf( __( 'Hello, I am interested in %1$s: %2$s', 'bufan' ), get_the_title(), get_permalink() ) );
	?>
	<div class="product-top">
		<div class="container">
			<?php bufan_breadcrumbs(); ?>
			<div class="product-layout">
				<div class="product-gallery" data-gallery>
					<div class="product-gallery__main">
						<?php if ( $bufan_images ) : ?>
							<?php $bufan_first_full = wp_get_attachment_image_src( $bufan_images[0], 'full' ); ?>
							<button type="button" class="product-gallery__zoom" data-gallery-zoom data-full="<?php echo esc_url( $bufan_first_full ? $bufan_first_full[0] : '' ); ?>" aria-label="<?php esc_attr_e( 'Enlarge photo', 'bufan' ); ?>">
								<?php
								echo wp_get_attachment_image(
									$bufan_images[0],
									'large',
									false,
									array(
										'class'         => 'product-gallery__img',
										'loading'       => 'eager',
										'fetchpriority' => 'high',
										'sizes'         => '(min-width: 1000px) 600px, 100vw',
										'data-gallery-main' => '',
									)
								);
								?>
							</button>
						<?php else : ?>
							<?php echo bufan_placeholder_image( 'product-gallery__img' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>
					<?php if ( count( $bufan_images ) > 1 ) : ?>
						<ul class="product-gallery__thumbs">
							<?php foreach ( $bufan_images as $bufan_index => $bufan_image_id ) : ?>
								<?php
								$bufan_large  = wp_get_attachment_image_src( $bufan_image_id, 'large' );
								$bufan_full   = wp_get_attachment_image_src( $bufan_image_id, 'full' );
								$bufan_srcset = wp_get_attachment_image_srcset( $bufan_image_id, 'large' );
								?>
								<li>
									<button type="button" class="product-gallery__thumb<?php echo 0 === $bufan_index ? ' is-active' : ''; ?>"
										data-gallery-thumb
										data-src="<?php echo esc_url( $bufan_large ? $bufan_large[0] : '' ); ?>"
										data-srcset="<?php echo esc_attr( $bufan_srcset ? $bufan_srcset : '' ); ?>"
										data-full="<?php echo esc_url( $bufan_full ? $bufan_full[0] : '' ); ?>"
										aria-label="<?php echo esc_attr( sprintf( /* translators: %d: photo number */ __( 'Show photo %d', 'bufan' ), $bufan_index + 1 ) ); ?>"
										<?php echo 0 === $bufan_index ? 'aria-current="true"' : ''; ?>>
										<?php echo wp_get_attachment_image( $bufan_image_id, 'thumbnail', false, array( 'alt' => '' ) ); ?>
									</button>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<div class="product-info">
					<?php if ( $bufan_terms && ! is_wp_error( $bufan_terms ) ) : ?>
						<a class="eyebrow eyebrow--link" href="<?php echo esc_url( get_term_link( $bufan_terms[0] ) ); ?>"><?php echo esc_html( $bufan_terms[0]->name ); ?></a>
					<?php endif; ?>
					<h1 class="product-info__title"><?php the_title(); ?></h1>
					<?php if ( $bufan_model ) : ?>
						<p class="product-info__model"><?php esc_html_e( 'Item No.', 'bufan' ); ?> <?php echo esc_html( $bufan_model ); ?></p>
					<?php endif; ?>
					<?php if ( has_excerpt() ) : ?>
						<p class="product-info__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>

					<?php if ( $bufan_specs ) : ?>
						<table class="spec-table">
							<caption class="screen-reader-text"><?php esc_html_e( 'Specifications', 'bufan' ); ?></caption>
							<tbody>
								<?php foreach ( $bufan_specs as $bufan_row ) : ?>
									<?php
									if ( 'model' === $bufan_row['key'] ) {
										continue; // Shown under the title.
									}
									?>
									<tr><th scope="row"><?php echo esc_html( $bufan_row['label'] ); ?></th><td><?php echo esc_html( $bufan_row['value'] ); ?></td></tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>

					<div class="product-info__actions">
						<a class="btn btn--primary btn--lg" href="#inquiry" data-scroll><?php esc_html_e( 'Get a quote', 'bufan' ); ?> <?php echo bufan_icon( 'arrow-right', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<?php if ( $bufan_wa ) : ?>
							<a class="btn btn--outline btn--lg" href="<?php echo esc_url( $bufan_wa ); ?>" target="_blank" rel="noopener"><?php echo bufan_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> WhatsApp</a>
						<?php endif; ?>
					</div>
					<ul class="product-info__promise">
						<li><?php echo bufan_icon( 'pen-tool', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Your logo or artwork embroidered', 'bufan' ); ?></li>
						<li><?php echo bufan_icon( 'clipboard-check', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Sample approved before bulk production', 'bufan' ); ?></li>
						<li><?php echo bufan_icon( 'factory', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Made in our own factory', 'bufan' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<section class="section product-details">
		<div class="container product-details__grid">
			<div class="product-details__main">
				<?php if ( trim( get_the_content() ) ) : ?>
					<h2 class="product-details__title"><?php esc_html_e( 'Product description', 'bufan' ); ?></h2>
					<div class="prose"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>
			<?php if ( $bufan_options ) : ?>
				<aside class="product-details__aside">
					<div class="stitch-card">
						<h2 class="product-details__title"><?php esc_html_e( 'Customization options', 'bufan' ); ?></h2>
						<ul class="check-list">
							<?php foreach ( $bufan_options as $bufan_option ) : ?>
								<li><?php echo bufan_icon( 'check', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $bufan_option ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</aside>
			<?php endif; ?>
		</div>
	</section>

	<section class="section section--inquiry">
		<div class="container inquiry-layout">
			<div class="inquiry-layout__aside">
				<p class="eyebrow"><?php esc_html_e( 'Quotation', 'bufan' ); ?></p>
				<h2 class="section-title"><?php esc_html_e( 'Request a quote for this product', 'bufan' ); ?></h2>
				<p class="section-lead"><?php esc_html_e( 'Tell us your quantity, colors and artwork. We will reply with a price, a sample plan and the production time.', 'bufan' ); ?></p>
				<?php bufan_contact_list(); ?>
			</div>
			<div class="inquiry-layout__form">
				<?php get_template_part( 'template-parts/inquiry-form', null, array( 'product_id' => $bufan_id ) ); ?>
			</div>
		</div>
	</section>

	<?php
	$bufan_related_args = array(
		'post_type'      => 'bufan_product',
		'posts_per_page' => 4,
		'post__not_in'   => array( $bufan_id ),
		'no_found_rows'  => true,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
	);
	if ( $bufan_terms && ! is_wp_error( $bufan_terms ) ) {
		$bufan_related_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'bufan_product_cat',
				'terms'    => wp_list_pluck( $bufan_terms, 'term_id' ),
			),
		);
	}
	$bufan_related = new WP_Query( $bufan_related_args );
	if ( ! $bufan_related->have_posts() ) {
		unset( $bufan_related_args['tax_query'] );
		$bufan_related = new WP_Query( $bufan_related_args );
	}
	if ( $bufan_related->have_posts() ) :
		?>
		<section class="section section--tint">
			<div class="container">
				<?php bufan_section_head( '', __( 'You may also like', 'bufan' ) ); ?>
				<div class="product-grid">
					<?php
					while ( $bufan_related->have_posts() ) :
						$bufan_related->the_post();
						get_template_part( 'template-parts/product-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<dialog class="lightbox" data-lightbox aria-label="<?php esc_attr_e( 'Product photo', 'bufan' ); ?>">
		<button type="button" class="lightbox__close" data-lightbox-close aria-label="<?php esc_attr_e( 'Close', 'bufan' ); ?>"><?php echo bufan_icon( 'x', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		<img alt="<?php echo esc_attr( get_the_title() ); ?>" data-lightbox-img>
	</dialog>
	<?php
endwhile;

get_footer();

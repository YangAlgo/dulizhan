<?php
/**
 * Homepage.
 *
 * Texts and photos come from the page set as the homepage (Pages → Home → "Homepage sections").
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();

$bufan_front_id   = bufan_front_page_id();
$bufan_hero_image = (int) bufan_home_field( 'hero_image' );
$bufan_intro_img  = (int) bufan_home_field( 'intro_image' );

$bufan_featured = new WP_Query(
	array(
		'post_type'      => 'bufan_product',
		'posts_per_page' => 8,
		'meta_key'       => '_bufan_featured', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'no_found_rows'  => true,
	)
);
if ( ! $bufan_featured->have_posts() ) {
	$bufan_featured = new WP_Query(
		array(
			'post_type'      => 'bufan_product',
			'posts_per_page' => 8,
			'no_found_rows'  => true,
		)
	);
}
$bufan_categories = bufan_category_cards( 6 );
?>

<section class="hero">
	<div class="container hero__inner">
		<div class="hero__content">
			<p class="eyebrow"><?php echo esc_html( bufan_home_field( 'hero_eyebrow' ) ); ?></p>
			<h1 class="hero__title"><?php echo bufan_highlight( bufan_home_field( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?></h1>
			<p class="hero__text"><?php echo esc_html( bufan_home_field( 'hero_text' ) ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--sun btn--lg" href="<?php echo esc_url( bufan_quote_url() ); ?>"><?php esc_html_e( 'Get a free quote', 'bufan' ); ?> <?php echo bufan_icon( 'arrow-right', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<a class="btn btn--ghost-light btn--lg" href="<?php echo esc_url( bufan_products_url() ); ?>"><?php esc_html_e( 'View products', 'bufan' ); ?></a>
			</div>
			<ul class="hero__checks">
				<li><?php echo bufan_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Custom logo embroidery', 'bufan' ); ?></li>
				<li><?php echo bufan_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Samples before bulk', 'bufan' ); ?></li>
				<li><?php echo bufan_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Factory prices', 'bufan' ); ?></li>
			</ul>
		</div>
		<?php $bufan_has_photo = $bufan_hero_image && wp_attachment_is_image( $bufan_hero_image ); ?>
		<div class="hero__media <?php echo $bufan_has_photo ? 'hero__media--photo' : 'hero__media--art'; ?>">
			<div class="hero__frame">
				<?php
				if ( $bufan_has_photo ) {
					echo wp_get_attachment_image(
						$bufan_hero_image,
						'large',
						false,
						array(
							'class'         => 'hero__img',
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'sizes'         => '(min-width: 1000px) 520px, 100vw',
						)
					);
				} else {
					printf(
						'<img class="hero__img hero__img--illustration" src="%s" alt="%s" width="1120" height="1120" fetchpriority="high">',
						esc_url( BUFAN_URI . '/assets/img/hero.svg' ),
						esc_attr__( 'Illustration of embroidered canvas bags', 'bufan' )
					);
				}
				?>
			</div>
		</div>
	</div>
	<div class="container">
		<ul class="hero__stats" aria-label="<?php esc_attr_e( 'Key facts', 'bufan' ); ?>">
			<?php for ( $bufan_i = 1; $bufan_i <= 4; $bufan_i++ ) : ?>
				<li class="hero__stat">
					<strong><?php echo esc_html( bufan_home_field( 'stat' . $bufan_i . '_value' ) ); ?></strong>
					<span><?php echo esc_html( bufan_home_field( 'stat' . $bufan_i . '_label' ) ); ?></span>
				</li>
			<?php endfor; ?>
		</ul>
	</div>
</section>

<div class="ticker">
	<div class="ticker__track">
		<?php foreach ( array( false, true ) as $bufan_copy ) : ?>
			<ul class="ticker__list"<?php echo $bufan_copy ? ' aria-hidden="true"' : ''; ?>>
				<?php foreach ( array_merge( bufan_ticker_items(), bufan_ticker_items() ) as $bufan_item ) : ?>
					<li class="ticker__item"><?php echo esc_html( $bufan_item ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	</div>
</div>

<?php if ( $bufan_categories ) : ?>
	<section class="section">
		<div class="container">
			<?php bufan_section_head( __( 'Product range', 'bufan' ), __( 'Canvas bags, embroidered your way', 'bufan' ), __( 'Start from one of our styles and make it yours with custom embroidery, colors and labels.', 'bufan' ) ); ?>
			<ul class="cat-grid">
				<?php foreach ( $bufan_categories as $bufan_card ) : ?>
					<li class="cat-card">
						<div class="cat-card__media"><?php echo $bufan_card['image']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<div class="cat-card__body">
							<h3 class="cat-card__title"><a href="<?php echo esc_url( get_term_link( $bufan_card['term'] ) ); ?>"><?php echo esc_html( $bufan_card['term']->name ); ?></a></h3>
							<span class="cat-card__count">
								<?php
								/* translators: %d: number of products */
								echo esc_html( sprintf( _n( '%d product', '%d products', $bufan_card['count'], 'bufan' ), $bufan_card['count'] ) );
								?>
							</span>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php if ( $bufan_featured->have_posts() ) : ?>
	<section class="section section--tint">
		<div class="container">
			<div class="section-head-row">
				<?php bufan_section_head( __( 'Featured', 'bufan' ), __( 'Popular styles', 'bufan' ) ); ?>
				<a class="link-arrow" href="<?php echo esc_url( bufan_products_url() ); ?>"><?php esc_html_e( 'All products', 'bufan' ); ?> <?php echo bufan_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</div>
			<div class="product-grid">
				<?php
				while ( $bufan_featured->have_posts() ) :
					$bufan_featured->the_post();
					get_template_part( 'template-parts/product-card' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="section">
	<div class="container">
		<?php bufan_section_head( __( 'Why Bufan', 'bufan' ), __( 'A factory partner you can rely on', 'bufan' ) ); ?>
		<ul class="feature-grid">
			<?php foreach ( bufan_advantages() as $bufan_item ) : ?>
				<li class="feature">
					<span class="feature__icon"><?php echo bufan_icon( $bufan_item['icon'], 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3 class="feature__title"><?php echo esc_html( $bufan_item['title'] ); ?></h3>
					<p class="feature__text"><?php echo esc_html( $bufan_item['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section section--story">
	<div class="container story">
		<div class="story__media">
			<?php
			if ( $bufan_intro_img && wp_attachment_is_image( $bufan_intro_img ) ) {
				echo wp_get_attachment_image( $bufan_intro_img, 'large', false, array( 'sizes' => '(min-width: 1000px) 520px, 100vw' ) );
			} else {
				printf(
					'<img src="%s" alt="" width="1040" height="800" loading="lazy">',
					esc_url( BUFAN_URI . '/assets/img/factory.svg' )
				);
			}
			?>
		</div>
		<div class="story__content">
			<p class="eyebrow"><?php esc_html_e( 'About us', 'bufan' ); ?></p>
			<h2 class="section-title"><?php echo esc_html( bufan_home_field( 'intro_title' ) ); ?></h2>
			<div class="prose">
				<?php
				$bufan_intro = $bufan_front_id ? get_post_field( 'post_content', $bufan_front_id ) : '';
				if ( trim( wp_strip_all_tags( $bufan_intro ) ) ) {
					echo apply_filters( 'the_content', $bufan_intro ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core content filter.
				} else {
					echo '<p>' . esc_html__( 'We are a family-run factory dedicated to embroidered canvas bags. Cutting, embroidery and sewing all happen in our own workshop, so we control quality and deliver on time.', 'bufan' ) . '</p>';
				}
				?>
			</div>
			<?php $bufan_about = bufan_page_url( 'about' ); ?>
			<?php if ( $bufan_about ) : ?>
				<a class="link-arrow" href="<?php echo esc_url( $bufan_about ); ?>"><?php esc_html_e( 'More about our factory', 'bufan' ); ?> <?php echo bufan_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/process' ); ?>

<section class="section section--inquiry">
	<div class="container inquiry-layout">
		<div class="inquiry-layout__aside">
			<p class="eyebrow"><?php esc_html_e( 'Get in touch', 'bufan' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'Tell us about your project', 'bufan' ); ?></h2>
			<p class="section-lead"><?php esc_html_e( 'Share your design, quantity and timeline. We will come back with suggestions and a quotation.', 'bufan' ); ?></p>
			<?php bufan_contact_list(); ?>
		</div>
		<div class="inquiry-layout__form">
			<?php get_template_part( 'template-parts/inquiry-form' ); ?>
		</div>
	</div>
</section>

<?php
get_footer();

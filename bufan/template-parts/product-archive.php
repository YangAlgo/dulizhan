<?php
/**
 * Product catalogue: all products or one category.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

$bufan_current = is_tax( 'bufan_product_cat' ) ? get_queried_object() : null;
$bufan_title   = $bufan_current ? single_term_title( '', false ) : __( 'Products', 'bufan' );
$bufan_lead    = $bufan_current && $bufan_current->description
	? wp_strip_all_tags( $bufan_current->description )
	: __( 'Every style can be customized with your embroidery, colors, labels and packaging.', 'bufan' );
$bufan_terms   = get_terms(
	array(
		'taxonomy'   => 'bufan_product_cat',
		'hide_empty' => true,
		'parent'     => 0,
	)
);

get_template_part(
	'template-parts/page-header',
	null,
	array(
		'title'   => $bufan_title,
		'lead'    => $bufan_lead,
		'eyebrow' => __( 'Catalogue', 'bufan' ),
	)
);
?>
<section class="section section--catalogue">
	<div class="container">
		<?php if ( ! is_wp_error( $bufan_terms ) && $bufan_terms ) : ?>
			<nav class="filter-pills" aria-label="<?php esc_attr_e( 'Product categories', 'bufan' ); ?>">
				<a class="pill<?php echo $bufan_current ? '' : ' is-active'; ?>" href="<?php echo esc_url( bufan_products_url() ); ?>"<?php echo $bufan_current ? '' : ' aria-current="page"'; ?>><?php esc_html_e( 'All', 'bufan' ); ?></a>
				<?php foreach ( $bufan_terms as $bufan_term ) : ?>
					<?php $bufan_active = $bufan_current && (int) $bufan_current->term_id === (int) $bufan_term->term_id; ?>
					<a class="pill<?php echo $bufan_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $bufan_term ) ); ?>"<?php echo $bufan_active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $bufan_term->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="product-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/product-card' );
				endwhile;
				?>
			</div>
			<?php bufan_pagination(); ?>
		<?php else : ?>
			<div class="empty-state">
				<p><?php esc_html_e( 'Products are coming soon. Contact us for our latest catalogue.', 'bufan' ); ?></p>
				<a class="btn btn--primary" href="<?php echo esc_url( bufan_quote_url() ); ?>"><?php esc_html_e( 'Ask for the catalogue', 'bufan' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/cta-band' );

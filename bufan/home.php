<?php
/**
 * Blog index.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();

$bufan_blog_id = (int) get_option( 'page_for_posts' );
get_template_part(
	'template-parts/page-header',
	null,
	array(
		'title' => $bufan_blog_id ? get_the_title( bufan_translated_post_id( $bufan_blog_id ) ) : __( 'Blog', 'bufan' ),
		'lead'  => __( 'News from our factory, embroidery tips and ideas for custom canvas bags.', 'bufan' ),
	)
);
?>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				?>
			</div>
			<?php bufan_pagination(); ?>
		<?php else : ?>
			<div class="empty-state"><p><?php esc_html_e( 'No articles yet — check back soon.', 'bufan' ); ?></p></div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();

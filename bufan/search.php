<?php
/**
 * Search results.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part(
	'template-parts/page-header',
	null,
	array(
		/* translators: %s: search term */
		'title' => sprintf( __( 'Search results for “%s”', 'bufan' ), get_search_query() ),
	)
);
?>
<section class="section">
	<div class="container container--narrow">
		<?php get_search_form(); ?>
		<?php if ( have_posts() ) : ?>
			<ul class="search-results">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li>
						<a class="search-results__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php bufan_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing matched your search. Try other words, or contact us directly.', 'bufan' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();

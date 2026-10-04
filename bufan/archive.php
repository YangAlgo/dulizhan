<?php
/**
 * Blog archives (categories, tags, dates).
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part(
	'template-parts/page-header',
	null,
	array(
		'title' => wp_strip_all_tags( get_the_archive_title() ),
		'lead'  => wp_strip_all_tags( get_the_archive_description() ),
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
			<div class="empty-state"><p><?php esc_html_e( 'Nothing found.', 'bufan' ); ?></p></div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();

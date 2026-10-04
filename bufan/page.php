<?php
/**
 * Default page.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-header', null, array( 'title' => get_the_title() ) );
	?>
	<section class="section section--page">
		<div class="container container--narrow">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="page-figure"><?php the_post_thumbnail( 'large' ); ?></figure>
			<?php endif; ?>
			<div class="prose"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
endwhile;

get_template_part( 'template-parts/cta-band' );
get_footer();

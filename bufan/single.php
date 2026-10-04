<?php
/**
 * Single blog post.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'article' ); ?>>
		<header class="page-header page-header--article">
			<div class="container container--narrow">
				<?php bufan_breadcrumbs(); ?>
				<h1 class="page-header__title"><?php the_title(); ?></h1>
				<p class="article__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
			</div>
		</header>
		<div class="section section--page">
			<div class="container container--narrow">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="page-figure"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="prose"><?php the_content(); ?></div>
				<?php
				wp_link_pages(
					array(
						'before' => '<nav class="page-links">',
						'after'  => '</nav>',
					)
				);
				?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_template_part( 'template-parts/cta-band' );
get_footer();

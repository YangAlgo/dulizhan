<?php
/**
 * Template Name: Contact page (with inquiry form)
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-header', null, array( 'title' => get_the_title() ) );
	?>
	<section class="section section--inquiry section--flush-top">
		<div class="container inquiry-layout">
			<div class="inquiry-layout__aside">
				<div class="prose"><?php the_content(); ?></div>
				<?php bufan_contact_list(); ?>
				<?php bufan_social_links(); ?>
			</div>
			<div class="inquiry-layout__form">
				<?php get_template_part( 'template-parts/inquiry-form', null, array( 'title' => __( 'Send us an inquiry', 'bufan' ) ) ); ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();

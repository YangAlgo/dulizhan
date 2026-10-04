<?php
/**
 * Template Name: Customization page (with process and inquiry form)
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/page-header',
		null,
		array(
			'title'   => get_the_title(),
			'eyebrow' => __( 'OEM & ODM', 'bufan' ),
		)
	);
	?>
	<section class="section section--page">
		<div class="container container--narrow">
			<div class="prose prose--columns"><?php the_content(); ?></div>
		</div>
	</section>
	<?php get_template_part( 'template-parts/process' ); ?>
	<section class="section section--inquiry">
		<div class="container inquiry-layout">
			<div class="inquiry-layout__aside">
				<p class="eyebrow"><?php esc_html_e( 'Start a project', 'bufan' ); ?></p>
				<h2 class="section-title"><?php esc_html_e( 'Send us your design', 'bufan' ); ?></h2>
				<p class="section-lead"><?php esc_html_e( 'Describe your idea and quantity. You can email artwork files after we reply.', 'bufan' ); ?></p>
				<?php bufan_contact_list(); ?>
			</div>
			<div class="inquiry-layout__form">
				<?php get_template_part( 'template-parts/inquiry-form' ); ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();

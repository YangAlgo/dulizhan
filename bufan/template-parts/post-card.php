<?php
/**
 * Blog post card.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-card__media"><?php the_post_thumbnail( 'bufan-card', array( 'sizes' => '(min-width: 1000px) 380px, 100vw' ) ); ?></div>
	<?php endif; ?>
	<div class="post-card__body">
		<p class="post-card__date"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="post-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
	</div>
</article>

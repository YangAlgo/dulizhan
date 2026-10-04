<?php
/**
 * "How it works" steps.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section section--process">
	<div class="container">
		<?php bufan_section_head( __( 'How it works', 'bufan' ), __( 'From your idea to your warehouse', 'bufan' ), __( 'A clear process with a sample approved by you before bulk production.', 'bufan' ) ); ?>
		<ol class="process">
			<?php foreach ( bufan_process_steps() as $bufan_i => $bufan_step ) : ?>
				<li class="process__step">
					<span class="process__num"><?php echo esc_html( str_pad( (string) ( $bufan_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="process__icon"><?php echo bufan_icon( $bufan_step['icon'], 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3 class="process__title"><?php echo esc_html( $bufan_step['title'] ); ?></h3>
					<p class="process__text"><?php echo esc_html( $bufan_step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

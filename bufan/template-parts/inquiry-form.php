<?php
/**
 * Inquiry form.
 *
 * Arguments: product_id (int), title (string), lead (string).
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

$bufan_args       = wp_parse_args(
	isset( $args ) ? $args : array(),
	array(
		'product_id' => 0,
		'title'      => '',
		'lead'       => '',
	)
);
$bufan_product_id = (int) $bufan_args['product_id'];
$bufan_status     = bufan_inquiry_status();
$bufan_old        = bufan_inquiry_old_input();
$bufan_privacy    = bufan_page_url( 'privacy' );
$bufan_val        = function ( $key ) use ( $bufan_old ) {
	return isset( $bufan_old[ $key ] ) ? $bufan_old[ $key ] : '';
};
$bufan_message    = $bufan_val( 'message' );
if ( '' === $bufan_message && $bufan_product_id ) {
	/* translators: %s: product name */
	$bufan_message = sprintf( __( 'Hi, I am interested in "%s". Please send me a quotation.', 'bufan' ), get_the_title( $bufan_product_id ) );
}
?>
<div class="inquiry" id="inquiry">
	<?php if ( $bufan_args['title'] ) : ?>
		<h2 class="inquiry__title"><?php echo esc_html( $bufan_args['title'] ); ?></h2>
	<?php endif; ?>
	<?php if ( $bufan_args['lead'] ) : ?>
		<p class="inquiry__lead"><?php echo esc_html( $bufan_args['lead'] ); ?></p>
	<?php endif; ?>

	<?php if ( $bufan_status ) : ?>
		<div class="notice-box notice-box--<?php echo esc_attr( $bufan_status['type'] ); ?>" role="<?php echo 'error' === $bufan_status['type'] ? 'alert' : 'status'; ?>">
			<?php echo bufan_icon( 'success' === $bufan_status['type'] ? 'check' : 'message-circle', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p><?php echo esc_html( $bufan_status['text'] ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! $bufan_status || 'success' !== $bufan_status['type'] ) : ?>
		<form class="inquiry__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="bufan_inquiry">
			<input type="hidden" name="bufan_back" value="<?php echo esc_url( bufan_current_url() ); ?>">
			<input type="hidden" name="bufan_token" value="<?php echo esc_attr( bufan_inquiry_token() ); ?>">
			<?php if ( $bufan_product_id ) : ?>
				<input type="hidden" name="bufan_product_id" value="<?php echo esc_attr( $bufan_product_id ); ?>">
				<p class="inquiry__product">
					<?php echo bufan_icon( 'package', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php esc_html_e( 'Product', 'bufan' ); ?>: <strong><?php echo esc_html( get_the_title( $bufan_product_id ) ); ?></strong></span>
				</p>
			<?php endif; ?>

			<div class="form-grid">
				<p class="field">
					<label for="bufan-name"><?php esc_html_e( 'Your name', 'bufan' ); ?> <span class="req" aria-hidden="true">*</span></label>
					<input type="text" id="bufan-name" name="bufan_name" required autocomplete="name" value="<?php echo esc_attr( $bufan_val( 'name' ) ); ?>">
				</p>
				<p class="field">
					<label for="bufan-email"><?php esc_html_e( 'Email', 'bufan' ); ?> <span class="req" aria-hidden="true">*</span></label>
					<input type="email" id="bufan-email" name="bufan_email" required autocomplete="email" value="<?php echo esc_attr( $bufan_val( 'email' ) ); ?>">
				</p>
				<p class="field">
					<label for="bufan-company"><?php esc_html_e( 'Company', 'bufan' ); ?></label>
					<input type="text" id="bufan-company" name="bufan_company" autocomplete="organization" value="<?php echo esc_attr( $bufan_val( 'company' ) ); ?>">
				</p>
				<p class="field">
					<label for="bufan-country"><?php esc_html_e( 'Country', 'bufan' ); ?></label>
					<input type="text" id="bufan-country" name="bufan_country" autocomplete="country-name" value="<?php echo esc_attr( $bufan_val( 'country' ) ); ?>">
				</p>
				<p class="field">
					<label for="bufan-phone"><?php esc_html_e( 'Phone / WhatsApp', 'bufan' ); ?></label>
					<input type="tel" id="bufan-phone" name="bufan_phone" autocomplete="tel" value="<?php echo esc_attr( $bufan_val( 'phone' ) ); ?>">
				</p>
				<p class="field">
					<label for="bufan-quantity"><?php esc_html_e( 'Estimated quantity', 'bufan' ); ?></label>
					<input type="text" id="bufan-quantity" name="bufan_quantity" placeholder="<?php esc_attr_e( 'e.g. 500 pcs', 'bufan' ); ?>" value="<?php echo esc_attr( $bufan_val( 'quantity' ) ); ?>">
				</p>
				<p class="field field--full">
					<label for="bufan-message"><?php esc_html_e( 'Your message', 'bufan' ); ?> <span class="req" aria-hidden="true">*</span></label>
					<textarea id="bufan-message" name="bufan_message" rows="5" required placeholder="<?php esc_attr_e( 'Tell us about the product, size, colors, embroidery and delivery date you have in mind.', 'bufan' ); ?>"><?php echo esc_textarea( $bufan_message ); ?></textarea>
				</p>
			</div>

			<p class="field field--hp" aria-hidden="true">
				<label for="bufan-website">Website</label>
				<input type="text" id="bufan-website" name="bufan_website" tabindex="-1" autocomplete="off">
			</p>

			<p class="field field--check">
				<label>
					<input type="checkbox" name="bufan_consent" value="1" required>
					<span>
						<?php
						if ( $bufan_privacy ) {
							printf(
								/* translators: %s: link to the privacy policy */
								esc_html__( 'I agree that my details are used to answer this inquiry, as described in the %s.', 'bufan' ),
								'<a href="' . esc_url( $bufan_privacy ) . '" target="_blank">' . esc_html__( 'privacy policy', 'bufan' ) . '</a>'
							);
						} else {
							esc_html_e( 'I agree that my details are used to answer this inquiry.', 'bufan' );
						}
						?>
					</span>
				</label>
			</p>

			<p class="inquiry__submit">
				<button type="submit" class="btn btn--primary btn--lg"><?php esc_html_e( 'Send inquiry', 'bufan' ); ?> <?php echo bufan_icon( 'send', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<span class="inquiry__hint"><?php esc_html_e( 'We reply by email. Your details stay private.', 'bufan' ); ?></span>
			</p>
		</form>
	<?php endif; ?>
</div>

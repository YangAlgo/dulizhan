<?php
/**
 * "Bufan → Settings": company and contact details used across the site.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Setting fields grouped into sections. Keys ending in _en / _zh are shown per language.
 *
 * @return array<string, array{title: string, fields: array<string, array>}>
 */
function bufan_settings_schema() {
	return array(
		'brand'   => array(
			'title'  => __( 'Brand', 'bufan' ),
			'fields' => array(
				'logo'       => array(
					'label' => __( 'Logo', 'bufan' ),
					'type'  => 'image',
					'help'  => __( 'PNG or SVG with a transparent background works best. Without a logo the site name is shown as text.', 'bufan' ),
				),
				'tagline_en' => array(
					'label' => __( 'Footer introduction (English)', 'bufan' ),
					'type'  => 'textarea',
				),
				'tagline_zh' => array(
					'label' => __( 'Footer introduction (Chinese)', 'bufan' ),
					'type'  => 'textarea',
				),
			),
		),
		'contact' => array(
			'title'  => __( 'Contact details', 'bufan' ),
			'fields' => array(
				'email'         => array(
					'label' => __( 'Public email', 'bufan' ),
					'type'  => 'email',
					'help'  => __( 'Shown on the website. Use an address on your own domain, e.g. sales@bufanfactory.com.', 'bufan' ),
				),
				'inquiry_email' => array(
					'label' => __( 'Send inquiries to', 'bufan' ),
					'type'  => 'email',
					'help'  => __( 'New inquiries are emailed here. Leave empty to use the WordPress admin email.', 'bufan' ),
				),
				'phone'         => array(
					'label' => __( 'Phone', 'bufan' ),
					'type'  => 'text',
					'help'  => __( 'With country code, e.g. +86 138 0000 0000.', 'bufan' ),
				),
				'whatsapp'      => array(
					'label' => __( 'WhatsApp number', 'bufan' ),
					'type'  => 'text',
					'help'  => __( 'With country code, e.g. +86 138 0000 0000. Adds a WhatsApp chat button to every page.', 'bufan' ),
				),
				'wechat'        => array(
					'label' => __( 'WeChat ID', 'bufan' ),
					'type'  => 'text',
				),
				'address_en'    => array(
					'label' => __( 'Factory address (English)', 'bufan' ),
					'type'  => 'textarea',
				),
				'address_zh'    => array(
					'label' => __( 'Factory address (Chinese)', 'bufan' ),
					'type'  => 'textarea',
				),
				'hours_en'      => array(
					'label' => __( 'Business hours (English)', 'bufan' ),
					'type'  => 'text',
					'help'  => __( 'e.g. Mon–Sat, 9:00–18:00 (GMT+8)', 'bufan' ),
				),
				'hours_zh'      => array(
					'label' => __( 'Business hours (Chinese)', 'bufan' ),
					'type'  => 'text',
				),
			),
		),
		'social'  => array(
			'title'  => __( 'Social media', 'bufan' ),
			'fields' => array(
				'instagram' => array(
					'label' => 'Instagram',
					'type'  => 'url',
				),
				'facebook'  => array(
					'label' => 'Facebook',
					'type'  => 'url',
				),
				'pinterest' => array(
					'label' => 'Pinterest',
					'type'  => 'url',
				),
				'linkedin'  => array(
					'label' => 'LinkedIn',
					'type'  => 'url',
				),
				'youtube'   => array(
					'label' => 'YouTube',
					'type'  => 'url',
				),
				'tiktok'    => array(
					'label' => 'TikTok',
					'type'  => 'url',
				),
			),
		),
		'site'    => array(
			'title'  => __( 'Website', 'bufan' ),
			'fields' => array(
				'whatsapp_button' => array(
					'label' => __( 'Floating WhatsApp button', 'bufan' ),
					'type'  => 'checkbox',
					'text'  => __( 'Show a WhatsApp chat button in the bottom corner of every page', 'bufan' ),
				),
				'head_code'       => array(
					'label' => __( 'Code in <head>', 'bufan' ),
					'type'  => 'code',
					'help'  => __( 'For Google Analytics, Google Ads or Search Console verification tags. Only paste code from services you trust.', 'bufan' ),
				),
			),
		),
	);
}

/**
 * Admin menu: Bufan → Settings (and the setup wizard, added in setup-wizard.php).
 */
function bufan_settings_menu() {
	add_menu_page(
		__( 'Bufan settings', 'bufan' ),
		'Bufan',
		'manage_options',
		'bufan',
		'bufan_render_settings_page',
		'dashicons-store',
		59
	);
	add_submenu_page( 'bufan', __( 'Bufan settings', 'bufan' ), __( 'Settings', 'bufan' ), 'manage_options', 'bufan', 'bufan_render_settings_page' );
}
add_action( 'admin_menu', 'bufan_settings_menu' );

/**
 * Register the option with its sanitizer.
 */
function bufan_register_settings() {
	register_setting(
		'bufan_settings',
		'bufan_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'bufan_sanitize_settings',
			'default'           => array( 'whatsapp_button' => '1' ),
		)
	);
}
add_action( 'admin_init', 'bufan_register_settings' );

/**
 * Sanitize the settings form.
 *
 * @param mixed $input Submitted values.
 * @return array
 */
function bufan_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$clean = array();
	foreach ( bufan_settings_schema() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$value = isset( $input[ $key ] ) ? $input[ $key ] : '';
			switch ( $field['type'] ) {
				case 'image':
					$clean[ $key ] = absint( $value ) ? (string) absint( $value ) : '';
					break;
				case 'email':
					$clean[ $key ] = sanitize_email( $value );
					break;
				case 'url':
					$clean[ $key ] = esc_url_raw( trim( $value ) );
					break;
				case 'checkbox':
					$clean[ $key ] = empty( $value ) ? '0' : '1';
					break;
				case 'textarea':
					$clean[ $key ] = sanitize_textarea_field( $value );
					break;
				case 'code':
					// Tracking snippets need <script>; only administrators with unfiltered_html may save them.
					$clean[ $key ] = current_user_can( 'unfiltered_html' ) ? trim( (string) $value ) : wp_kses_post( $value );
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $value );
			}
		}
	}
	return $clean;
}

/**
 * Render the settings page.
 */
function bufan_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$options = get_option( 'bufan_settings', array() );
	?>
	<div class="wrap bufan-settings">
		<h1><?php esc_html_e( 'Bufan settings', 'bufan' ); ?></h1>
		<?php if ( ! get_option( 'bufan_setup_done' ) ) : ?>
			<div class="notice notice-info inline">
				<p>
					<?php esc_html_e( 'New site? Run the one-click setup first to create the pages, menus and example products.', 'bufan' ); ?>
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=bufan-setup' ) ); ?>"><?php esc_html_e( 'Open setup', 'bufan' ); ?></a>
				</p>
			</div>
		<?php endif; ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'bufan_settings' ); ?>
			<?php foreach ( bufan_settings_schema() as $section ) : ?>
				<h2 class="title"><?php echo esc_html( $section['title'] ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					foreach ( $section['fields'] as $key => $field ) :
						$value = isset( $options[ $key ] ) ? $options[ $key ] : '';
						if ( 'whatsapp_button' === $key && ! isset( $options[ $key ] ) ) {
							$value = '1';
						}
						$id   = 'bufan-setting-' . $key;
						$name = 'bufan_settings[' . $key . ']';
						?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
							<td>
								<?php
								switch ( $field['type'] ) {
									case 'image':
										$src = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
										?>
										<div class="bufan-image-field" data-bufan-image>
											<img src="<?php echo esc_url( $src ); ?>" alt="" class="bufan-image-field__preview" <?php echo $src ? '' : 'hidden'; ?>>
											<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
											<button type="button" class="button" data-bufan-image-select><?php esc_html_e( 'Choose image', 'bufan' ); ?></button>
											<button type="button" class="button-link button-link-delete" data-bufan-image-remove <?php echo $src ? '' : 'hidden'; ?>><?php esc_html_e( 'Remove', 'bufan' ); ?></button>
										</div>
										<?php
										break;
									case 'textarea':
										printf( '<textarea class="large-text" rows="3" id="%1$s" name="%2$s">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
										break;
									case 'code':
										printf( '<textarea class="large-text code" rows="5" id="%1$s" name="%2$s">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
										break;
									case 'checkbox':
										printf(
											'<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
											esc_attr( $id ),
											esc_attr( $name ),
											checked( '1', $value, false ),
											esc_html( $field['text'] )
										);
										break;
									default:
										printf(
											'<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s">',
											esc_attr( 'url' === $field['type'] ? 'url' : ( 'email' === $field['type'] ? 'email' : 'text' ) ),
											esc_attr( $id ),
											esc_attr( $name ),
											esc_attr( $value )
										);
								}
								if ( ! empty( $field['help'] ) ) {
									echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Settings page assets (media picker for the logo).
 *
 * @param string $hook Current admin page.
 */
function bufan_settings_assets( $hook ) {
	if ( 'toplevel_page_bufan' !== $hook && false === strpos( $hook, 'bufan-setup' ) ) {
		return;
	}
	bufan_enqueue_admin_assets( true );
}
add_action( 'admin_enqueue_scripts', 'bufan_settings_assets' );

/**
 * Print tracking code from the settings page.
 */
function bufan_print_head_code() {
	$code = bufan_option( 'head_code' );
	if ( $code ) {
		echo "\n" . $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- saved only by users with unfiltered_html.
	}
}
add_action( 'wp_head', 'bufan_print_head_code', 99 );

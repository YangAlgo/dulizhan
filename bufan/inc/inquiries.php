<?php
/**
 * Inquiry form: submission handling, email notification and the admin inbox.
 *
 * Every inquiry is saved in WordPress (menu "Inquiries") before the email is sent,
 * so nothing is lost when the server cannot send mail.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the private inquiry post type.
 */
function bufan_register_inquiries() {
	register_post_type(
		'bufan_inquiry',
		array(
			'labels'          => array(
				'name'               => __( 'Inquiries', 'bufan' ),
				'singular_name'      => __( 'Inquiry', 'bufan' ),
				'menu_name'          => __( 'Inquiries', 'bufan' ),
				'all_items'          => __( 'All inquiries', 'bufan' ),
				'edit_item'          => __( 'Inquiry', 'bufan' ),
				'search_items'       => __( 'Search inquiries', 'bufan' ),
				'not_found'          => __( 'No inquiries yet.', 'bufan' ),
				'not_found_in_trash' => __( 'No inquiries in Trash.', 'bufan' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 6,
			'supports'        => false,
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'bufan_register_inquiries' );

/**
 * Fields collected by the form: key => array( label, required ).
 *
 * @return array<string, array{label: string, required: bool}>
 */
function bufan_inquiry_fields() {
	return array(
		'name'     => array(
			'label'    => __( 'Your name', 'bufan' ),
			'required' => true,
		),
		'email'    => array(
			'label'    => __( 'Email', 'bufan' ),
			'required' => true,
		),
		'company'  => array(
			'label'    => __( 'Company', 'bufan' ),
			'required' => false,
		),
		'country'  => array(
			'label'    => __( 'Country', 'bufan' ),
			'required' => false,
		),
		'phone'    => array(
			'label'    => __( 'Phone / WhatsApp', 'bufan' ),
			'required' => false,
		),
		'quantity' => array(
			'label'    => __( 'Estimated quantity', 'bufan' ),
			'required' => false,
		),
		'message'  => array(
			'label'    => __( 'Your message', 'bufan' ),
			'required' => true,
		),
	);
}

/**
 * Signed timestamp placed in the form. Bots that post instantly, or post without
 * loading the form, fail the check in bufan_handle_inquiry().
 */
function bufan_inquiry_token() {
	$time = (string) time();
	return $time . '.' . wp_hash( 'bufan_inquiry|' . $time );
}

/**
 * Validate the signed timestamp.
 *
 * @param string $token Value from the form.
 */
function bufan_inquiry_token_ok( $token ) {
	$parts = explode( '.', (string) $token );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( wp_hash( 'bufan_inquiry|' . $parts[0] ), $parts[1] ) ) {
		return false;
	}
	return time() - (int) $parts[0] >= 3;
}

/**
 * Handle a form submission (logged in or not).
 */
function bufan_handle_inquiry() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form; protected by signed token, honeypot and rate limit instead of a nonce so it keeps working behind page caches.
	$back = isset( $_POST['bufan_back'] ) ? esc_url_raw( wp_unslash( $_POST['bufan_back'] ) ) : '';
	$back = wp_validate_redirect( $back, bufan_home_url() );
	$back = remove_query_arg( array( 'inquiry', 'inquiry_token' ), $back );

	// Honeypot: humans never see this field. Pretend success so bots move on.
	if ( ! empty( $_POST['bufan_website'] ) ) {
		bufan_inquiry_redirect( $back, 'sent' );
	}

	$data = array();
	foreach ( bufan_inquiry_fields() as $key => $field ) {
		$raw          = isset( $_POST[ 'bufan_' . $key ] ) ? wp_unslash( $_POST[ 'bufan_' . $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized next line.
		$data[ $key ] = 'message' === $key ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
	}
	$data['email']      = sanitize_email( $data['email'] );
	$data['product_id'] = isset( $_POST['bufan_product_id'] ) ? absint( $_POST['bufan_product_id'] ) : 0;
	$consent            = ! empty( $_POST['bufan_consent'] );
	$token              = isset( $_POST['bufan_token'] ) ? sanitize_text_field( wp_unslash( $_POST['bufan_token'] ) ) : '';
	// phpcs:enable

	$error = '';
	if ( ! bufan_inquiry_token_ok( $token ) ) {
		$error = 'expired';
	} elseif ( '' === $data['name'] || '' === $data['message'] || ! is_email( $data['email'] ) ) {
		$error = 'missing';
	} elseif ( ! $consent ) {
		$error = 'consent';
	} elseif ( bufan_inquiry_rate_limited() ) {
		$error = 'limit';
	}

	if ( $error ) {
		// Keep what the visitor typed for 15 minutes so the form can be refilled.
		$keep = wp_generate_password( 12, false );
		set_transient( 'bufan_inquiry_' . $keep, $data, 15 * MINUTE_IN_SECONDS );
		bufan_inquiry_redirect( add_query_arg( 'inquiry_token', $keep, $back ), 'error-' . $error );
	}

	$product = $data['product_id'] ? get_post( $data['product_id'] ) : null;
	if ( $product && 'bufan_product' !== $product->post_type ) {
		$product            = null;
		$data['product_id'] = 0;
	}

	$title = $data['name'];
	if ( $data['company'] ) {
		$title .= ' · ' . $data['company'];
	}
	if ( $product ) {
		$title .= ' · ' . $product->post_title;
	}

	$inquiry_id = wp_insert_post(
		array(
			'post_type'   => 'bufan_inquiry',
			'post_status' => 'publish',
			'post_title'  => $title,
		),
		true
	);
	if ( is_wp_error( $inquiry_id ) ) {
		bufan_inquiry_redirect( $back, 'error-server' );
	}

	foreach ( $data as $key => $value ) {
		update_post_meta( $inquiry_id, '_bufan_' . $key, $value );
	}
	update_post_meta( $inquiry_id, '_bufan_language', bufan_lang() );
	update_post_meta( $inquiry_id, '_bufan_page', $back );
	update_post_meta( $inquiry_id, '_bufan_unread', '1' );

	$sent = bufan_send_inquiry_email( $inquiry_id, $data, $product );
	update_post_meta( $inquiry_id, '_bufan_mail_sent', $sent ? '1' : '0' );

	bufan_inquiry_redirect( $back, 'sent' );
}
add_action( 'admin_post_nopriv_bufan_inquiry', 'bufan_handle_inquiry' );
add_action( 'admin_post_bufan_inquiry', 'bufan_handle_inquiry' );

/**
 * Redirect back to the form with a status flag and stop.
 *
 * @param string $url    Page to return to.
 * @param string $status Status flag read by the form template.
 */
function bufan_inquiry_redirect( $url, $status ) {
	wp_safe_redirect( add_query_arg( 'inquiry', $status, $url ) . '#inquiry' );
	exit;
}

/**
 * Allow at most 5 inquiries per visitor IP every 10 minutes.
 * Only a hash of the IP is kept, and only for those 10 minutes.
 * Behind Cloudflare the visitor's address is combined in, so visitors sharing
 * a Cloudflare edge address do not share one limit.
 */
function bufan_inquiry_rate_limited() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		$ip .= '|' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
	}
	$key   = 'bufan_rl_' . substr( wp_hash( $ip ), 0, 20 );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return true;
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
	return false;
}

/**
 * Email the inquiry to the address from Bufan → Settings (or the site admin email).
 *
 * @param int          $inquiry_id Saved inquiry ID.
 * @param array        $data       Sanitized form data.
 * @param WP_Post|null $product    Product the inquiry was sent from.
 * @return bool Whether WordPress handed the email to the mail server.
 */
function bufan_send_inquiry_email( $inquiry_id, $data, $product ) {
	$to = bufan_option( 'inquiry_email', get_option( 'admin_email' ) );

	/* translators: 1: visitor name, 2: site name */
	$subject = sprintf( __( 'New inquiry from %1$s — %2$s', 'bufan' ), $data['name'], wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );

	$lines = array();
	foreach ( bufan_inquiry_fields() as $key => $field ) {
		if ( 'message' !== $key && '' !== $data[ $key ] ) {
			$lines[] = $field['label'] . ': ' . $data[ $key ];
		}
	}
	if ( $product ) {
		$lines[] = __( 'Product', 'bufan' ) . ': ' . $product->post_title . ' (' . get_permalink( $product ) . ')';
	}
	$lines[] = '';
	$lines[] = __( 'Your message', 'bufan' ) . ':';
	$lines[] = $data['message'];
	$lines[] = '';
	$lines[] = '—';
	$lines[] = __( 'Reply to this email to answer the customer directly.', 'bufan' );
	$lines[] = __( 'View in WordPress', 'bufan' ) . ': ' . admin_url( 'post.php?post=' . $inquiry_id . '&action=edit' );

	$reply_name = str_replace( array( '"', "\r", "\n" ), '', $data['name'] );
	$headers    = array( 'Reply-To: "' . $reply_name . '" <' . $data['email'] . '>' );

	return wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
}

/**
 * Values to prefill after a failed submission.
 *
 * @return array
 */
function bufan_inquiry_old_input() {
	static $data = null;
	if ( null === $data ) {
		$data  = array();
		$token = isset( $_GET['inquiry_token'] ) ? sanitize_key( wp_unslash( $_GET['inquiry_token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $token ) {
			$stored = get_transient( 'bufan_inquiry_' . $token );
			$data   = is_array( $stored ) ? $stored : array();
		}
	}
	return $data;
}

/**
 * Status message for the form, based on the redirect flag.
 *
 * @return array{type: string, text: string}|null
 */
function bufan_inquiry_status() {
	$status = isset( $_GET['inquiry'] ) ? sanitize_key( wp_unslash( $_GET['inquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $status ) {
		return null;
	}
	$messages = array(
		'sent'           => array( 'success', __( 'Thank you! Your inquiry has been sent. We will reply by email soon.', 'bufan' ) ),
		'error-missing'  => array( 'error', __( 'Please fill in your name, a valid email address and your message.', 'bufan' ) ),
		'error-consent'  => array( 'error', __( 'Please tick the box to agree to our privacy policy.', 'bufan' ) ),
		'error-expired'  => array( 'error', __( 'The form could not be verified. Please submit it again.', 'bufan' ) ),
		'error-limit'    => array( 'error', __( 'Too many messages were sent from your network. Please try again in a few minutes, or email us directly.', 'bufan' ) ),
		'error-server'   => array( 'error', __( 'Something went wrong on our side. Please email us directly.', 'bufan' ) ),
	);
	if ( ! isset( $messages[ $status ] ) ) {
		return null;
	}
	return array(
		'type' => $messages[ $status ][0],
		'text' => $messages[ $status ][1],
	);
}

/**
 * Shortcode [bufan_inquiry_form] so the form can be placed in any page.
 *
 * @param array $atts Shortcode attributes: title.
 */
function bufan_inquiry_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'title' => '' ), $atts, 'bufan_inquiry_form' );
	ob_start();
	get_template_part( 'template-parts/inquiry-form', null, array( 'title' => $atts['title'] ) );
	return ob_get_clean();
}
add_shortcode( 'bufan_inquiry_form', 'bufan_inquiry_shortcode' );

/*
 * ---------------------------------------------------------------------------
 * Admin inbox.
 * ---------------------------------------------------------------------------
 */

/**
 * Number of unread inquiries (capped at 99).
 */
function bufan_unread_inquiry_count() {
	$ids = get_posts(
		array(
			'post_type'      => 'bufan_inquiry',
			'post_status'    => 'publish',
			'meta_key'       => '_bufan_unread', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'fields'         => 'ids',
			'posts_per_page' => 99,
			'no_found_rows'  => true,
		)
	);
	return count( $ids );
}

/**
 * Show the unread count as a bubble next to the "Inquiries" menu item.
 */
function bufan_inquiry_menu_bubble() {
	global $menu;
	$count = bufan_unread_inquiry_count();
	if ( ! $count || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $index => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=bufan_inquiry' === $item[2] ) {
			$menu[ $index ][0] .= sprintf( ' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>', $count ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			break;
		}
	}
}
add_action( 'admin_menu', 'bufan_inquiry_menu_bubble', 99 );

/**
 * Inquiry list columns.
 *
 * @param string[] $columns Existing columns.
 */
function bufan_inquiry_columns( $columns ) {
	return array(
		'cb'            => $columns['cb'],
		'title'         => __( 'From', 'bufan' ),
		'bufan_email'   => __( 'Email', 'bufan' ),
		'bufan_country' => __( 'Country', 'bufan' ),
		'bufan_product' => __( 'Product', 'bufan' ),
		'date'          => __( 'Received', 'bufan' ),
	);
}
add_filter( 'manage_bufan_inquiry_posts_columns', 'bufan_inquiry_columns' );

/**
 * Inquiry list column values.
 *
 * @param string $column  Column key.
 * @param int    $post_id Inquiry ID.
 */
function bufan_inquiry_column_values( $column, $post_id ) {
	switch ( $column ) {
		case 'bufan_email':
			$email = get_post_meta( $post_id, '_bufan_email', true );
			printf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $email ), esc_html( $email ) );
			if ( get_post_meta( $post_id, '_bufan_unread', true ) ) {
				echo ' <span class="bufan-badge">' . esc_html__( 'New', 'bufan' ) . '</span>';
			}
			if ( '0' === get_post_meta( $post_id, '_bufan_mail_sent', true ) ) {
				echo '<br><span class="bufan-warning">' . esc_html__( 'Email notification failed', 'bufan' ) . '</span>';
			}
			break;
		case 'bufan_country':
			echo esc_html( get_post_meta( $post_id, '_bufan_country', true ) );
			break;
		case 'bufan_product':
			$product_id = (int) get_post_meta( $post_id, '_bufan_product_id', true );
			echo $product_id ? esc_html( get_the_title( $product_id ) ) : '—';
			break;
	}
}
add_action( 'manage_bufan_inquiry_posts_custom_column', 'bufan_inquiry_column_values', 10, 2 );

/**
 * Inquiries are read-only: hide quick edit and the publish box, show the details box.
 */
function bufan_inquiry_meta_boxes() {
	remove_meta_box( 'submitdiv', 'bufan_inquiry', 'side' );
	add_meta_box( 'bufan-inquiry', __( 'Inquiry details', 'bufan' ), 'bufan_render_inquiry_box', 'bufan_inquiry', 'normal', 'high' );
	add_meta_box( 'bufan-inquiry-actions', __( 'Actions', 'bufan' ), 'bufan_render_inquiry_actions', 'bufan_inquiry', 'side', 'high' );
}
add_action( 'add_meta_boxes_bufan_inquiry', 'bufan_inquiry_meta_boxes' );

/**
 * Render the read-only inquiry details.
 *
 * @param WP_Post $post Inquiry.
 */
function bufan_render_inquiry_box( $post ) {
	echo '<table class="widefat striped bufan-inquiry-table"><tbody>';
	foreach ( bufan_inquiry_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, '_bufan_' . $key, true );
		if ( '' === $value ) {
			continue;
		}
		echo '<tr><th>' . esc_html( $field['label'] ) . '</th><td>';
		if ( 'email' === $key ) {
			printf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $value ), esc_html( $value ) );
		} else {
			echo nl2br( esc_html( $value ) );
		}
		echo '</td></tr>';
	}
	$product_id = (int) get_post_meta( $post->ID, '_bufan_product_id', true );
	if ( $product_id && get_post( $product_id ) ) {
		printf(
			'<tr><th>%1$s</th><td><a href="%2$s" target="_blank">%3$s</a></td></tr>',
			esc_html__( 'Product', 'bufan' ),
			esc_url( get_permalink( $product_id ) ),
			esc_html( get_the_title( $product_id ) )
		);
	}
	$page = get_post_meta( $post->ID, '_bufan_page', true );
	if ( $page ) {
		printf( '<tr><th>%1$s</th><td><a href="%2$s" target="_blank">%3$s</a></td></tr>', esc_html__( 'Sent from page', 'bufan' ), esc_url( $page ), esc_html( $page ) );
	}
	$language = get_post_meta( $post->ID, '_bufan_language', true );
	if ( $language ) {
		printf( '<tr><th>%1$s</th><td>%2$s</td></tr>', esc_html__( 'Site language', 'bufan' ), 'zh' === $language ? '中文' : 'English' );
	}
	printf( '<tr><th>%1$s</th><td>%2$s</td></tr>', esc_html__( 'Received', 'bufan' ), esc_html( get_the_date( 'Y-m-d H:i', $post ) ) );
	echo '</tbody></table>';
}

/**
 * Render the inquiry actions box (reply, delete).
 *
 * @param WP_Post $post Inquiry.
 */
function bufan_render_inquiry_actions( $post ) {
	$email   = get_post_meta( $post->ID, '_bufan_email', true );
	$product = (int) get_post_meta( $post->ID, '_bufan_product_id', true );
	/* translators: %s: site name */
	$subject = sprintf( __( 'Re: your inquiry to %s', 'bufan' ), get_bloginfo( 'name' ) );
	if ( $product ) {
		$subject .= ' — ' . get_the_title( $product );
	}
	printf(
		'<p><a class="button button-primary button-large" href="mailto:%1$s?subject=%2$s">%3$s</a></p>',
		esc_attr( $email ),
		rawurlencode( $subject ),
		esc_html__( 'Reply by email', 'bufan' )
	);
	$phone = preg_replace( '/\D+/', '', (string) get_post_meta( $post->ID, '_bufan_phone', true ) );
	if ( strlen( $phone ) >= 8 ) {
		printf( '<p><a class="button" href="%1$s" target="_blank" rel="noopener">%2$s</a></p>', esc_url( 'https://wa.me/' . $phone ), esc_html__( 'Open in WhatsApp', 'bufan' ) );
	}
	if ( '0' === get_post_meta( $post->ID, '_bufan_mail_sent', true ) ) {
		echo '<p class="bufan-warning">' . esc_html__( 'The email notification for this inquiry could not be sent. Install the "WP Mail SMTP" plugin so new inquiries reach your mailbox.', 'bufan' ) . '</p>';
	}
	if ( current_user_can( 'delete_post', $post->ID ) ) {
		printf( '<p><a class="submitdelete" href="%1$s">%2$s</a></p>', esc_url( get_delete_post_link( $post->ID ) ), esc_html__( 'Move to Trash', 'bufan' ) );
	}
}

/**
 * Mark an inquiry as read when it is opened in the admin.
 * Runs on admin_init so the menu bubble already shows the updated count.
 */
function bufan_mark_inquiry_read() {
	global $pagenow;
	if ( 'post.php' !== $pagenow ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $post_id && 'bufan_inquiry' === get_post_type( $post_id ) && current_user_can( 'edit_post', $post_id ) ) {
		delete_post_meta( $post_id, '_bufan_unread' );
	}
}
add_action( 'admin_init', 'bufan_mark_inquiry_read' );

/**
 * Remove "Quick Edit" from inquiry rows.
 *
 * @param string[] $actions Row actions.
 * @param WP_Post  $post    Row post.
 */
function bufan_inquiry_row_actions( $actions, $post ) {
	if ( 'bufan_inquiry' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'] );
		$actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_edit_post_link( $post->ID ) ), esc_html__( 'Open', 'bufan' ) );
	}
	return $actions;
}
add_filter( 'post_row_actions', 'bufan_inquiry_row_actions', 10, 2 );

/**
 * Admin styles for the inquiry screens.
 */
function bufan_inquiry_admin_assets() {
	$screen = get_current_screen();
	if ( $screen && 'bufan_inquiry' === $screen->post_type ) {
		wp_enqueue_style( 'bufan-admin', BUFAN_URI . '/assets/css/admin.css', array(), bufan_asset_version( 'assets/css/admin.css' ) );
	}
}
add_action( 'admin_enqueue_scripts', 'bufan_inquiry_admin_assets' );

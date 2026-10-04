<?php
/**
 * Template helpers that print markup.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Site logo: the logo from Bufan settings, then the Customizer logo, then the site name.
 */
function bufan_logo() {
	$logo_id = (int) bufan_option( 'logo' );
	if ( ! $logo_id ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
	}
	$name = get_bloginfo( 'name' );
	echo '<a class="site-logo" href="' . esc_url( bufan_home_url() ) . '" rel="home">';
	if ( $logo_id && wp_attachment_is_image( $logo_id ) ) {
		echo wp_get_attachment_image(
			$logo_id,
			'medium',
			false,
			array(
				'class'   => 'site-logo__img',
				'alt'     => $name,
				'loading' => 'eager',
			)
		);
	} else {
		echo '<span class="site-logo__mark" aria-hidden="true">' . bufan_logo_mark() . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="site-logo__text">' . esc_html( $name ) . '</span>';
	}
	echo '</a>';
}

/**
 * Small stitched-badge mark used next to the text logo.
 */
function bufan_logo_mark() {
	return '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><rect x="1" y="1" width="32" height="32" rx="10" fill="currentColor"/><rect x="4.5" y="4.5" width="25" height="25" rx="7" stroke="#FFC61A" stroke-width="1.6" stroke-dasharray="2.6 2.2"/><path d="M12.5 10v14M12.5 10h5a3.5 3.5 0 0 1 0 7h-5M12.5 17h6a3.5 3.5 0 0 1 0 7h-6" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/**
 * Main navigation: the menu assigned in Appearance → Menus, or an automatic menu.
 *
 * @param string $location Menu location.
 * @param string $class    CSS class for the <ul>.
 */
function bufan_nav( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 'primary' === $location ? 2 : 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	$items = bufan_default_nav_items( 'primary' === $location );
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as $item ) {
		$has_children = ! empty( $item['children'] );
		printf(
			'<li class="menu-item%1$s%2$s"><a href="%3$s"%4$s>%5$s</a>',
			$item['current'] ? ' current-menu-item' : '',
			$has_children ? ' menu-item-has-children' : '',
			esc_url( $item['url'] ),
			$item['current'] ? ' aria-current="page"' : '',
			esc_html( $item['label'] )
		);
		if ( $has_children ) {
			echo '<ul class="sub-menu">';
			foreach ( $item['children'] as $child ) {
				printf( '<li class="menu-item"><a href="%1$s">%2$s</a></li>', esc_url( $child['url'] ), esc_html( $child['label'] ) );
			}
			echo '</ul>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Items for the automatic menu, built from the pages made by the setup wizard.
 *
 * @param bool $with_children Include product categories under "Products".
 * @return array<int, array{label: string, url: string, current: bool, children?: array}>
 */
function bufan_default_nav_items( $with_children = true ) {
	$items = array(
		array(
			'label'   => __( 'Home', 'bufan' ),
			'url'     => bufan_home_url(),
			'current' => is_front_page(),
		),
	);

	$children = array();
	if ( $with_children ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'bufan_product_cat',
				'hide_empty' => false,
				'parent'     => 0,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$children[] = array(
					'label' => $term->name,
					'url'   => get_term_link( $term ),
				);
			}
		}
	}
	$items[] = array(
		'label'    => __( 'Products', 'bufan' ),
		'url'      => bufan_products_url(),
		'current'  => is_post_type_archive( 'bufan_product' ) || is_singular( 'bufan_product' ) || is_tax( 'bufan_product_cat' ),
		'children' => $children,
	);

	$pages = array(
		'customization' => __( 'Customization', 'bufan' ),
		'about'         => __( 'About us', 'bufan' ),
		'blog'          => __( 'Blog', 'bufan' ),
		'contact'       => __( 'Contact', 'bufan' ),
	);
	foreach ( $pages as $key => $label ) {
		$id = bufan_page_id( $key );
		if ( $id ) {
			$items[] = array(
				'label'   => $label,
				'url'     => get_permalink( $id ),
				'current' => get_queried_object_id() === $id,
			);
		}
	}
	return $items;
}

/**
 * Language switcher (EN / 中文).
 *
 * @param string $class Extra CSS class.
 */
function bufan_language_switcher( $class = '' ) {
	$languages = bufan_languages();
	if ( ! $languages ) {
		return;
	}
	echo '<ul class="lang-switch ' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'Language', 'bufan' ) . '">';
	foreach ( $languages as $lang ) {
		printf(
			'<li><a href="%1$s" lang="%2$s" hreflang="%2$s"%3$s title="%4$s">%5$s</a></li>',
			esc_url( $lang['url'] ),
			esc_attr( $lang['locale'] ),
			$lang['current'] ? ' aria-current="true" class="is-current"' : '',
			esc_attr( $lang['name'] ),
			esc_html( $lang['short'] )
		);
	}
	echo '</ul>';
}

/**
 * Breadcrumb trail for products, categories and pages.
 */
function bufan_breadcrumbs() {
	$crumbs = array(
		array( __( 'Home', 'bufan' ), bufan_home_url() ),
	);
	if ( is_singular( 'bufan_product' ) || is_tax( 'bufan_product_cat' ) || is_post_type_archive( 'bufan_product' ) ) {
		$crumbs[] = array( __( 'Products', 'bufan' ), bufan_products_url() );
	}
	if ( is_singular( 'bufan_product' ) ) {
		$terms = get_the_terms( get_the_ID(), 'bufan_product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$crumbs[] = array( $terms[0]->name, get_term_link( $terms[0] ) );
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_tax( 'bufan_product_cat' ) ) {
		$term = get_queried_object();
		foreach ( array_reverse( get_ancestors( $term->term_id, 'bufan_product_cat', 'taxonomy' ) ) as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, 'bufan_product_cat' );
			$crumbs[] = array( $ancestor->name, get_term_link( $ancestor ) );
		}
		$crumbs[] = array( single_term_title( '', false ), '' );
	} elseif ( is_singular( 'post' ) ) {
		$blog = bufan_page_id( 'blog' );
		if ( $blog ) {
			$crumbs[] = array( get_the_title( $blog ), get_permalink( $blog ) );
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_page() ) {
		$crumbs[] = array( get_the_title(), '' );
	}
	if ( count( $crumbs ) < 2 ) {
		return;
	}
	$last = count( $crumbs ) - 1;
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'bufan' ) . '"><ol>';
	foreach ( $crumbs as $i => $crumb ) {
		if ( $i === $last || '' === $crumb[1] ) {
			echo '<li><span aria-current="page">' . esc_html( $crumb[0] ) . '</span></li>';
		} else {
			echo '<li><a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a></li>';
		}
	}
	echo '</ol></nav>';
}

/**
 * Social profile links from Bufan settings.
 */
function bufan_social_links() {
	$networks = array( 'instagram', 'facebook', 'pinterest', 'linkedin', 'youtube', 'tiktok' );
	$links    = '';
	foreach ( $networks as $network ) {
		$url = bufan_option( $network );
		if ( $url ) {
			$links .= sprintf(
				'<li><a href="%1$s" target="_blank" rel="noopener">%2$s</a></li>',
				esc_url( $url ),
				bufan_icon( $network, 18, ucfirst( $network ) )
			);
		}
	}
	if ( $links ) {
		echo '<ul class="social-links">' . $links . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
	}
}

/**
 * Contact detail rows (email, phone, WhatsApp, WeChat, address, hours).
 *
 * @return array<int, array{icon: string, label: string, value: string, url: string}>
 */
function bufan_contact_items() {
	$items = array();
	$email = bufan_option( 'email' );
	if ( $email ) {
		$items[] = array(
			'icon'  => 'mail',
			'label' => __( 'Email', 'bufan' ),
			'value' => $email,
			'url'   => 'mailto:' . $email,
		);
	}
	$whatsapp = bufan_option( 'whatsapp' );
	if ( $whatsapp ) {
		$items[] = array(
			'icon'  => 'whatsapp',
			'label' => 'WhatsApp',
			'value' => $whatsapp,
			'url'   => bufan_whatsapp_url(),
		);
	}
	$phone = bufan_option( 'phone' );
	if ( $phone ) {
		$items[] = array(
			'icon'  => 'phone',
			'label' => __( 'Phone', 'bufan' ),
			'value' => $phone,
			'url'   => 'tel:' . preg_replace( '/[^\d+]/', '', $phone ),
		);
	}
	$wechat = bufan_option( 'wechat' );
	if ( $wechat ) {
		$items[] = array(
			'icon'  => 'wechat',
			'label' => __( 'WeChat', 'bufan' ),
			'value' => $wechat,
			'url'   => '',
		);
	}
	$address = bufan_option_i18n( 'address' );
	if ( $address ) {
		$items[] = array(
			'icon'  => 'map-pin',
			'label' => __( 'Factory', 'bufan' ),
			'value' => $address,
			'url'   => '',
		);
	}
	$hours = bufan_option_i18n( 'hours' );
	if ( $hours ) {
		$items[] = array(
			'icon'  => 'clock',
			'label' => __( 'Hours', 'bufan' ),
			'value' => $hours,
			'url'   => '',
		);
	}
	return $items;
}

/**
 * Print the contact detail list.
 *
 * @param string $class CSS class.
 */
function bufan_contact_list( $class = 'contact-list' ) {
	$items = bufan_contact_items();
	if ( ! $items ) {
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as $item ) {
		$value = nl2br( esc_html( $item['value'] ) );
		if ( $item['url'] ) {
			$external = 0 === strpos( $item['url'], 'http' ) ? ' target="_blank" rel="noopener"' : '';
			$value    = '<a href="' . esc_url( $item['url'], array( 'http', 'https', 'mailto', 'tel' ) ) . '"' . $external . '>' . $value . '</a>';
		}
		printf(
			'<li><span class="contact-list__icon">%1$s</span><span><span class="contact-list__label">%2$s</span>%3$s</span></li>',
			bufan_icon( $item['icon'], 18 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( $item['label'] ),
			$value // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		);
	}
	echo '</ul>';
}

/**
 * Escape a heading and turn *starred words* into a highlighted span with a stitched underline.
 *
 * @param string $text Plain text, e.g. "Custom *embroidered* canvas bags".
 */
function bufan_highlight( $text ) {
	$html = esc_html( $text );
	return preg_replace(
		'/\*([^*]+)\*/u',
		'<em class="hl">$1<svg class="hl__stitch" viewBox="0 0 200 12" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M2 8 C 30 2, 55 11, 85 6 S 140 2, 165 7 S 190 9, 198 5"/></svg></em>',
		$html
	);
}

/**
 * Text with the highlight stars removed (for places that cannot show the highlight).
 *
 * @param string $text Text that may contain *stars*.
 */
function bufan_strip_highlight( $text ) {
	return str_replace( '*', '', $text );
}

/**
 * Selling points scrolling in the yellow band under the homepage banner.
 *
 * @return string[]
 */
function bufan_ticker_items() {
	return array(
		__( 'Custom logo embroidery', 'bufan' ),
		__( 'OEM & ODM', 'bufan' ),
		__( 'Sample before bulk', 'bufan' ),
		__( 'Made in our own factory', 'bufan' ),
		__( 'Export to Europe & North America', 'bufan' ),
		__( 'Cotton canvas', 'bufan' ),
	);
}

/**
 * Section heading with a small "stitched" label above the title.
 *
 * @param string $eyebrow Small label.
 * @param string $title   Title.
 * @param string $text    Optional intro text.
 * @param string $tag     Heading tag.
 */
function bufan_section_head( $eyebrow, $title, $text = '', $tag = 'h2' ) {
	echo '<header class="section-head">';
	if ( $eyebrow ) {
		echo '<p class="eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	printf( '<%1$s class="section-title">%2$s</%1$s>', tag_escape( $tag ), esc_html( $title ) );
	if ( $text ) {
		echo '<p class="section-lead">' . esc_html( $text ) . '</p>';
	}
	echo '</header>';
}

/**
 * Numbered pagination for archives.
 */
function bufan_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => '←<span class="screen-reader-text"> ' . __( 'Previous page', 'bufan' ) . '</span>',
			'next_text'          => '<span class="screen-reader-text">' . __( 'Next page', 'bufan' ) . ' </span>→',
			'before_page_number' => '<span class="screen-reader-text">' . __( 'Page', 'bufan' ) . ' </span>',
		)
	);
}

/**
 * Product categories that have products, with the photo of their first product.
 *
 * @param int $limit Maximum number of categories.
 * @return array<int, array{term: WP_Term, image: string, count: int}>
 */
function bufan_category_cards( $limit = 6 ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'bufan_product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'number'     => $limit,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	$cards = array();
	foreach ( $terms as $term ) {
		$first = get_posts(
			array(
				'post_type'      => 'bufan_product',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'bufan_product_cat',
						'terms'    => $term->term_id,
					),
				),
			)
		);
		$cards[] = array(
			'term'  => $term,
			'image' => $first ? bufan_product_image( $first[0], 'bufan-card' ) : bufan_placeholder_image(),
			'count' => (int) $term->count,
		);
	}
	return $cards;
}

/**
 * The six steps from inquiry to delivery (homepage and Customization page).
 *
 * @return array<int, array{icon: string, title: string, text: string}>
 */
function bufan_process_steps() {
	return array(
		array(
			'icon'  => 'send',
			'title' => __( 'Share your idea', 'bufan' ),
			'text'  => __( 'Send your artwork, reference photos or just an idea, with the quantity you need.', 'bufan' ),
		),
		array(
			'icon'  => 'file-text',
			'title' => __( 'Get a quote', 'bufan' ),
			'text'  => __( 'We suggest materials and embroidery, then send a detailed quotation.', 'bufan' ),
		),
		array(
			'icon'  => 'clipboard-check',
			'title' => __( 'Approve the sample', 'bufan' ),
			'text'  => __( 'We make a real sample so you can check the size, colors and stitching.', 'bufan' ),
		),
		array(
			'icon'  => 'spool',
			'title' => __( 'Bulk production', 'bufan' ),
			'text'  => __( 'Cutting, embroidery and sewing in our factory, with checks at every step.', 'bufan' ),
		),
		array(
			'icon'  => 'shield-check',
			'title' => __( 'Inspection & packing', 'bufan' ),
			'text'  => __( 'Every bag is inspected, then packed the way you need it.', 'bufan' ),
		),
		array(
			'icon'  => 'ship',
			'title' => __( 'Delivery', 'bufan' ),
			'text'  => __( 'Shipped by express, air or sea — or handed to your forwarder.', 'bufan' ),
		),
	);
}

/**
 * Reasons to work with the factory (homepage).
 *
 * @return array<int, array{icon: string, title: string, text: string}>
 */
function bufan_advantages() {
	return array(
		array(
			'icon'  => 'factory',
			'title' => __( 'Factory direct', 'bufan' ),
			'text'  => __( 'No middlemen: talk directly to the people who make your bags, at factory prices.', 'bufan' ),
		),
		array(
			'icon'  => 'pen-tool',
			'title' => __( 'Embroidery know-how', 'bufan' ),
			'text'  => __( 'We digitize your artwork in-house and choose the right stitches for clean, durable results.', 'bufan' ),
		),
		array(
			'icon'  => 'palette',
			'title' => __( 'Made to your brief', 'bufan' ),
			'text'  => __( 'Size, canvas, colors, zippers, labels and packaging — all customizable.', 'bufan' ),
		),
		array(
			'icon'  => 'clipboard-check',
			'title' => __( 'Sample before bulk', 'bufan' ),
			'text'  => __( 'See and approve a real sample before production starts.', 'bufan' ),
		),
		array(
			'icon'  => 'shield-check',
			'title' => __( 'Checked at every step', 'bufan' ),
			'text'  => __( 'Materials, embroidery and finished bags are inspected throughout production.', 'bufan' ),
		),
		array(
			'icon'  => 'globe',
			'title' => __( 'Export ready', 'bufan' ),
			'text'  => __( 'Export packing and shipping to Europe and North America.', 'bufan' ),
		),
	);
}

/**
 * URL of the page currently being viewed (used to return visitors after the inquiry form).
 * Built from the site's own address rather than the request's Host header.
 */
function bufan_current_url() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- escaped below.
	$home = wp_parse_url( home_url() );
	if ( empty( $home['host'] ) ) {
		return bufan_home_url();
	}
	$base = ( isset( $home['scheme'] ) ? $home['scheme'] : 'https' ) . '://' . $home['host'] . ( isset( $home['port'] ) ? ':' . $home['port'] : '' );
	return remove_query_arg( array( 'inquiry', 'inquiry_token' ), esc_url_raw( $base . '/' . ltrim( $uri, '/' ) ) );
}

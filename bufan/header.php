<?php
/**
 * Site header.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

$bufan_email    = bufan_option( 'email' );
$bufan_whatsapp = bufan_option( 'whatsapp' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'bufan' ); ?></a>

<?php if ( $bufan_email || $bufan_whatsapp ) : ?>
	<div class="topbar">
		<div class="container topbar__inner">
			<p class="topbar__note"><?php esc_html_e( 'Factory direct · Custom orders welcome', 'bufan' ); ?></p>
			<ul class="topbar__contact">
				<?php if ( $bufan_email ) : ?>
					<li><a href="mailto:<?php echo esc_attr( $bufan_email ); ?>"><?php echo bufan_icon( 'mail', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $bufan_email ); ?></a></li>
				<?php endif; ?>
				<?php if ( $bufan_whatsapp ) : ?>
					<li><a href="<?php echo esc_url( bufan_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo bufan_icon( 'whatsapp', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $bufan_whatsapp ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>

<header class="site-header" data-header>
	<div class="container site-header__inner">
		<?php bufan_logo(); ?>

		<nav class="site-nav" id="site-nav" aria-label="<?php esc_attr_e( 'Main menu', 'bufan' ); ?>" data-nav>
			<?php bufan_nav( 'primary', 'menu' ); ?>
			<div class="site-nav__mobile-extra">
				<a class="btn btn--primary" href="<?php echo esc_url( bufan_quote_url() ); ?>"><?php esc_html_e( 'Get a quote', 'bufan' ); ?></a>
				<?php bufan_contact_list( 'contact-list contact-list--compact' ); ?>
			</div>
		</nav>

		<div class="site-header__actions">
			<?php bufan_language_switcher(); ?>
			<a class="btn btn--primary btn--sm site-header__cta" href="<?php echo esc_url( bufan_quote_url() ); ?>"><?php esc_html_e( 'Get a quote', 'bufan' ); ?></a>
			<button type="button" class="nav-toggle" aria-controls="site-nav" aria-expanded="false" data-nav-toggle>
				<span class="nav-toggle__icon nav-toggle__icon--open"><?php echo bufan_icon( 'menu', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="nav-toggle__icon nav-toggle__icon--close"><?php echo bufan_icon( 'x', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="screen-reader-text" data-nav-toggle-label><?php esc_html_e( 'Menu', 'bufan' ); ?></span>
			</button>
		</div>
	</div>
</header>

<main id="main" class="site-main">

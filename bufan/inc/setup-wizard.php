<?php
/**
 * "Bufan → One-click setup": languages, pages, categories, example products and menus.
 *
 * Safe to run more than once: anything that already exists is kept and only missing
 * pieces are created (for example the Chinese pages after Polylang is installed later).
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the setup screen under the Bufan menu.
 */
function bufan_setup_menu() {
	add_submenu_page( 'bufan', __( 'One-click setup', 'bufan' ), __( 'One-click setup', 'bufan' ), 'manage_options', 'bufan-setup', 'bufan_render_setup_page' );
}
add_action( 'admin_menu', 'bufan_setup_menu', 11 );

/**
 * Point new installs to the setup screen.
 */
function bufan_setup_notice() {
	if ( get_option( 'bufan_setup_done' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'plugins' ), true ) ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p><strong>%1$s</strong> %2$s</p><p><a class="button button-primary" href="%3$s">%4$s</a></p></div>',
		esc_html__( 'Bufan theme is active.', 'bufan' ),
		esc_html__( 'Run the one-click setup to create the pages, product categories, example products and menus in English and Chinese.', 'bufan' ),
		esc_url( admin_url( 'admin.php?page=bufan-setup' ) ),
		esc_html__( 'Open one-click setup', 'bufan' )
	);
}
add_action( 'admin_notices', 'bufan_setup_notice' );

/**
 * Render the setup screen.
 */
function bufan_render_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$has_pll = function_exists( 'pll_languages_list' );
	$log     = get_transient( 'bufan_setup_log' );
	$done    = get_option( 'bufan_setup_done' );
	?>
	<div class="wrap bufan-setup">
		<h1><?php esc_html_e( 'One-click setup', 'bufan' ); ?></h1>

		<?php if ( is_array( $log ) && isset( $_GET['done'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success">
				<p><strong><?php esc_html_e( 'Setup finished.', 'bufan' ); ?></strong> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View your website', 'bufan' ); ?> →</a></p>
				<ul class="bufan-setup__log">
					<?php foreach ( $log as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php delete_transient( 'bufan_setup_log' ); ?>
		<?php endif; ?>

		<div class="bufan-setup__card">
			<h2><?php esc_html_e( 'Before you start', 'bufan' ); ?></h2>
			<ul class="bufan-checklist">
				<li class="<?php echo $has_pll ? 'is-ok' : 'is-todo'; ?>">
					<?php if ( $has_pll ) : ?>
						<?php esc_html_e( 'Polylang is active — English and Chinese versions will be created.', 'bufan' ); ?>
					<?php else : ?>
						<?php esc_html_e( 'Polylang is not active. Install it first to get English and Chinese versions; otherwise only the English site is created.', 'bufan' ); ?>
						<a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=polylang&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Install Polylang', 'bufan' ); ?></a>
					<?php endif; ?>
				</li>
				<li class="<?php echo get_option( 'permalink_structure' ) ? 'is-ok' : 'is-todo'; ?>">
					<?php esc_html_e( 'Readable links (e.g. /products/canvas-pouch/) — setup turns them on.', 'bufan' ); ?>
				</li>
				<?php if ( $done ) : ?>
					<li class="is-ok">
						<?php
						/* translators: %s: date and time */
						echo esc_html( sprintf( __( 'Setup already ran on %s. Running it again only adds what is missing.', 'bufan' ), wp_date( 'Y-m-d H:i', (int) $done ) ) );
						?>
					</li>
				<?php endif; ?>
			</ul>

			<h2><?php esc_html_e( 'What setup creates', 'bufan' ); ?></h2>
			<ul class="ul-disc">
				<li><?php esc_html_e( 'Languages: English (main) and Chinese', 'bufan' ); ?></li>
				<li><?php esc_html_e( 'Pages: Home, Customization, About us, FAQ, Contact, Privacy Policy, Blog', 'bufan' ); ?></li>
				<li><?php esc_html_e( 'Product categories: Cosmetic Bags, Coin Purses, Pencil Cases, Tote Bags', 'bufan' ); ?></li>
				<li><?php esc_html_e( 'Main menu and footer menu for each language', 'bufan' ); ?></li>
			</ul>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="bufan_run_setup">
				<?php wp_nonce_field( 'bufan_run_setup' ); ?>
				<p>
					<label>
						<input type="checkbox" name="bufan_products" value="1" <?php checked( ! get_option( 'bufan_sample_products' ) ); ?>>
						<?php esc_html_e( 'Add 4 example products with illustrations (replace them with your real products later)', 'bufan' ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="bufan_cleanup" value="1" checked>
						<?php esc_html_e( 'Remove the WordPress sample post ("Hello world!") and sample page', 'bufan' ); ?>
					</label>
				</p>
				<?php submit_button( __( 'Run setup', 'bufan' ), 'primary hero', 'submit', false ); ?>
			</form>
		</div>

		<div class="bufan-setup__card">
			<h2><?php esc_html_e( 'After setup', 'bufan' ); ?></h2>
			<ol>
				<li>
					<?php
					printf(
						/* translators: %s: link to the settings page */
						esc_html__( 'Fill in your email, WhatsApp, address and logo in %s.', 'bufan' ),
						'<a href="' . esc_url( admin_url( 'admin.php?page=bufan' ) ) . '">' . esc_html__( 'Bufan → Settings', 'bufan' ) . '</a>'
					);
					?>
				</li>
				<li><?php esc_html_e( 'Edit Pages → Home to change the homepage banner, photos and key facts.', 'bufan' ); ?></li>
				<li><?php esc_html_e( 'Add your products under Products → Add product, then add the Chinese version with the "+" in the Languages box.', 'bufan' ); ?></li>
				<li><?php esc_html_e( 'Rewrite the About us, FAQ and Customization pages with your real story and numbers.', 'bufan' ); ?></li>
			</ol>
		</div>
	</div>
	<?php
}

/**
 * Handle the setup form.
 */
function bufan_handle_setup() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'bufan' ) );
	}
	check_admin_referer( 'bufan_run_setup' );
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged -- image processing can take a while on shared hosting.
	}
	$log = bufan_run_setup(
		array(
			'products' => ! empty( $_POST['bufan_products'] ),
			'cleanup'  => ! empty( $_POST['bufan_cleanup'] ),
		)
	);
	set_transient( 'bufan_setup_log', $log, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'admin.php?page=bufan-setup&done=1' ) );
	exit;
}
add_action( 'admin_post_bufan_run_setup', 'bufan_handle_setup' );

/**
 * Run every setup step.
 *
 * @param array $opts Options: products (bool), cleanup (bool).
 * @return string[] Log lines.
 */
function bufan_run_setup( $opts ) {
	$log = array();

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = __( 'Turned on readable links.', 'bufan' );
	}

	$default_names = array( '', 'WordPress', 'My WordPress Website', 'My WordPress Site', 'My Blog', '我的WordPress网站', '我的WordPress站点', '我的站点' );
	if ( in_array( get_option( 'blogname' ), $default_names, true ) ) {
		update_option( 'blogname', 'Bufan' );
		$log[] = __( 'Site name set to "Bufan".', 'bufan' );
	}
	$default_descriptions = array( '', 'Just another WordPress site', '又一个WordPress站点' );
	if ( in_array( get_option( 'blogdescription' ), $default_descriptions, true ) ) {
		update_option( 'blogdescription', 'Custom embroidered canvas bags manufacturer' );
	}

	$langs = bufan_setup_languages( $log );

	if ( ! empty( $opts['cleanup'] ) ) {
		bufan_setup_cleanup( $log );
	}

	$cats  = bufan_setup_categories( $langs, $log );
	$pages = bufan_setup_pages( $langs, $log );

	if ( ! empty( $pages['home']['en'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $pages['home']['en'] );
	}
	if ( ! empty( $pages['blog']['en'] ) ) {
		update_option( 'page_for_posts', $pages['blog']['en'] );
	}
	if ( ! empty( $pages['privacy']['en'] ) ) {
		update_option( 'wp_page_for_privacy_policy', $pages['privacy']['en'] );
	}

	if ( ! empty( $opts['products'] ) ) {
		bufan_setup_products( $langs, $cats, $log );
	}

	bufan_setup_menus( $langs, $pages, $cats, $log );

	$settings = get_option( 'bufan_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	foreach ( bufan_starter_tagline() as $code => $text ) {
		if ( empty( $settings[ 'tagline_' . $code ] ) ) {
			$settings[ 'tagline_' . $code ] = $text;
		}
	}
	if ( ! isset( $settings['whatsapp_button'] ) ) {
		$settings['whatsapp_button'] = '1';
	}
	update_option( 'bufan_settings', $settings );

	if ( ! empty( $langs['zh'] ) ) {
		bufan_setup_translate_strings( $langs['zh'] );
	}

	update_option( 'bufan_setup_done', time() );
	// Rebuild links on the next page load, when Polylang's language rules are registered.
	delete_option( 'rewrite_rules' );
	// Polylang caches each language's home URL. It was computed before readable links were
	// switched on in this request, so drop it now and again at the very end of the request.
	bufan_setup_clear_language_cache();
	add_action( 'shutdown', 'bufan_setup_clear_language_cache', 9999 );
	$log[] = __( 'All done.', 'bufan' );

	return $log;
}

/**
 * Forget Polylang's cached language list (it holds URLs built with the old link format).
 */
function bufan_setup_clear_language_cache() {
	delete_transient( 'pll_languages_list' );
}

/**
 * Create English and Chinese in Polylang (if missing).
 *
 * @param string[] $log Log lines.
 * @return array{en?: string, zh?: string} Language slugs, empty without Polylang.
 */
function bufan_setup_languages( &$log ) {
	if ( ! function_exists( 'PLL' ) || ! function_exists( 'pll_languages_list' ) || ! isset( PLL()->model ) ) {
		$log[] = __( 'Polylang is not active: created the English site only.', 'bufan' );
		return array();
	}

	$find = function () {
		$found   = array();
		$slugs   = pll_languages_list( array( 'fields' => 'slug' ) );
		$locales = pll_languages_list( array( 'fields' => 'locale' ) );
		foreach ( $slugs as $i => $slug ) {
			$locale = isset( $locales[ $i ] ) ? $locales[ $i ] : '';
			if ( 0 === strpos( $locale, 'zh' ) && empty( $found['zh'] ) ) {
				$found['zh'] = $slug;
			} elseif ( 0 === strpos( $locale, 'en' ) && empty( $found['en'] ) ) {
				$found['en'] = $slug;
			}
		}
		return $found;
	};

	$model = PLL()->model;
	$add   = function ( $args ) use ( $model ) {
		if ( isset( $model->languages ) && is_object( $model->languages ) && method_exists( $model->languages, 'add' ) ) {
			return $model->languages->add( $args );
		}
		if ( method_exists( $model, 'add_language' ) ) {
			return $model->add_language( $args );
		}
		return new WP_Error( 'bufan_pll', 'Unsupported Polylang version' );
	};

	$found   = $find();
	$created = false;
	if ( empty( $found['en'] ) ) {
		$result = $add(
			array(
				'name'       => 'English',
				'slug'       => 'en',
				'locale'     => 'en_US',
				'rtl'        => false,
				'term_group' => 0,
				'flag'       => 'us',
			)
		);
		if ( is_wp_error( $result ) ) {
			$log[] = __( 'Could not create the English language:', 'bufan' ) . ' ' . $result->get_error_message();
		} else {
			$created = true;
			$log[]   = __( 'Added language: English (main language).', 'bufan' );
		}
	}
	if ( empty( $found['zh'] ) ) {
		$result = $add(
			array(
				'name'       => '中文 (中国)',
				'slug'       => 'zh',
				'locale'     => 'zh_CN',
				'rtl'        => false,
				'term_group' => 1,
				'flag'       => 'cn',
			)
		);
		if ( is_wp_error( $result ) ) {
			$log[] = __( 'Could not create the Chinese language:', 'bufan' ) . ' ' . $result->get_error_message();
		} else {
			$created = true;
			$log[]   = __( 'Added language: Chinese.', 'bufan' );
		}
	}

	$found = $find();
	if ( empty( $found['en'] ) ) {
		return array();
	}

	if ( $created ) {
		bufan_pll_set_option( 'hide_default', true );
		bufan_pll_set_option( 'force_lang', 1 );
		bufan_pll_set_option( 'rewrite', true );
	}
	// Chinese homepage at /zh/ rather than /zh/shouye/.
	bufan_pll_set_option( 'redirect_lang', true );
	if ( function_exists( 'pll_default_language' ) && pll_default_language() !== $found['en'] ) {
		$log[] = __( 'Note: the main language in Polylang is not English. For customers in Europe and North America, set English as the default language in Languages → Languages.', 'bufan' );
	}

	// Languages are configured, so Polylang's own setup wizard prompt is no longer needed.
	if ( class_exists( 'PLL_Admin_Notices' ) && method_exists( 'PLL_Admin_Notices', 'dismiss' ) ) {
		PLL_Admin_Notices::dismiss( 'wizard' );
	}

	// Existing content without a language (e.g. from before Polylang) becomes English.
	if ( defined( 'POLYLANG_VERSION' ) && version_compare( POLYLANG_VERSION, '3.4', '>=' ) && method_exists( $model, 'set_language_in_mass' ) ) {
		$model->set_language_in_mass();
	}

	return $found;
}

/**
 * Change one Polylang option (Polylang 3.7+ options object, or the classic option array).
 *
 * @param string $key   Option key.
 * @param mixed  $value Value.
 */
function bufan_pll_set_option( $key, $value ) {
	$options = PLL()->options;
	if ( is_object( $options ) && method_exists( $options, 'set' ) ) {
		$options->set( $key, $value );
		if ( method_exists( $options, 'save' ) ) {
			$options->save();
		}
		return;
	}
	$stored         = get_option( 'polylang', array() );
	$stored[ $key ] = $value;
	update_option( 'polylang', $stored );
}

/**
 * Trash the untouched WordPress sample post and page.
 *
 * @param string[] $log Log lines.
 */
function bufan_setup_cleanup( &$log ) {
	$samples = array(
		array( 'hello-world', 'post' ),
		array( 'sample-page', 'page' ),
	);
	foreach ( $samples as $sample ) {
		$post = get_page_by_path( $sample[0], OBJECT, $sample[1] );
		if ( $post && 'trash' !== $post->post_status && $post->post_modified_gmt === $post->post_date_gmt ) {
			wp_trash_post( $post->ID );
			/* translators: %s: post title */
			$log[] = sprintf( __( 'Moved the sample "%s" to Trash.', 'bufan' ), $post->post_title );
		}
	}
	$privacy_draft = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( $privacy_draft && 'draft' === get_post_status( $privacy_draft ) ) {
		wp_trash_post( $privacy_draft );
	}
}

/**
 * Insert a post and set its language.
 *
 * @param array  $args Post data.
 * @param string $lang Language slug, or '' without Polylang.
 * @return int Post ID or 0.
 */
function bufan_setup_insert_post( $args, $lang ) {
	$id = wp_insert_post( wp_slash( $args ), true );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	if ( $lang && function_exists( 'pll_set_post_language' ) ) {
		pll_set_post_language( $id, $lang );
	}
	return (int) $id;
}

/**
 * Whether a post ID still points to a usable post.
 *
 * @param int $id Post ID.
 */
function bufan_setup_post_exists( $id ) {
	$status = $id ? get_post_status( $id ) : false;
	return $status && 'trash' !== $status;
}

/**
 * Create the starter pages in each language and link the translations.
 *
 * @param array    $langs Language slugs.
 * @param string[] $log   Log lines.
 * @return array<string, array{en?: int, zh?: int}>
 */
function bufan_setup_pages( $langs, &$log ) {
	$stored = get_option( 'bufan_pages', array() );
	$stored = is_array( $stored ) ? $stored : array();
	$result = array();

	foreach ( bufan_starter_pages() as $key => $page ) {
		$ids = array();

		$en_id = isset( $stored[ $key ] ) ? (int) $stored[ $key ] : 0;
		if ( ! bufan_setup_post_exists( $en_id ) ) {
			$en_id = bufan_setup_insert_post( bufan_setup_page_args( $page, 'en' ), isset( $langs['en'] ) ? $langs['en'] : '' );
			if ( $en_id ) {
				/* translators: %s: page title */
				$log[] = sprintf( __( 'Created page: %s', 'bufan' ), $page['en']['title'] );
			}
		}
		if ( ! $en_id ) {
			continue;
		}
		$ids['en']      = $en_id;
		$stored[ $key ] = $en_id;

		if ( ! empty( $langs['zh'] ) ) {
			$zh_id = function_exists( 'pll_get_post' ) ? (int) pll_get_post( $en_id, $langs['zh'] ) : 0;
			if ( ! bufan_setup_post_exists( $zh_id ) ) {
				$zh_id = bufan_setup_insert_post( bufan_setup_page_args( $page, 'zh' ), $langs['zh'] );
				if ( $zh_id ) {
					/* translators: %s: page title */
					$log[] = sprintf( __( 'Created page: %s', 'bufan' ), $page['zh']['title'] );
				}
			}
			if ( $zh_id ) {
				$ids['zh'] = $zh_id;
				pll_save_post_translations(
					array(
						$langs['en'] => $en_id,
						$langs['zh'] => $zh_id,
					)
				);
			}
		}
		$result[ $key ] = $ids;
	}

	update_option( 'bufan_pages', $stored );
	return $result;
}

/**
 * Post data for a starter page.
 *
 * @param array  $page Page definition.
 * @param string $code 'en' or 'zh'.
 */
function bufan_setup_page_args( $page, $code ) {
	$content = $page[ $code ]['content'];
	if ( ! empty( $page['classic'] ) ) {
		$content = bufan_strip_block_markup( $content );
	}
	$args = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $page[ $code ]['title'],
		'post_name'    => $page[ $code ]['slug'],
		'post_content' => $content,
	);
	if ( ! empty( $page['template'] ) ) {
		$args['page_template'] = $page['template'];
	}
	return $args;
}

/**
 * Remove block comments so content edited in the classic editor (products, homepage) stays clean.
 *
 * @param string $content Block markup.
 */
function bufan_strip_block_markup( $content ) {
	$content = preg_replace( '/<!-- \/?wp:[^>]*-->\n?/', '', $content );
	return trim( preg_replace( "/\n{3,}/", "\n\n", $content ) );
}

/**
 * Create the starter product categories in each language.
 *
 * @param array    $langs Language slugs.
 * @param string[] $log   Log lines.
 * @return array<string, array{en?: int, zh?: int}>
 */
function bufan_setup_categories( $langs, &$log ) {
	$stored = get_option( 'bufan_categories', array() );
	$stored = is_array( $stored ) ? $stored : array();
	$result = array();

	foreach ( bufan_starter_categories() as $key => $cat ) {
		$ids   = array();
		$en_id = isset( $stored[ $key ] ) ? (int) $stored[ $key ] : 0;
		if ( ! $en_id || ! get_term( $en_id, 'bufan_product_cat' ) ) {
			$en_id = bufan_setup_insert_term( $cat['en'][0], $cat['en'][1], isset( $langs['en'] ) ? $langs['en'] : '' );
			if ( $en_id ) {
				/* translators: %s: category name */
				$log[] = sprintf( __( 'Created product category: %s', 'bufan' ), $cat['en'][0] );
			}
		}
		if ( ! $en_id ) {
			continue;
		}
		$ids['en']      = $en_id;
		$stored[ $key ] = $en_id;

		if ( ! empty( $langs['zh'] ) ) {
			$zh_id = function_exists( 'pll_get_term' ) ? (int) pll_get_term( $en_id, $langs['zh'] ) : 0;
			if ( ! $zh_id || ! get_term( $zh_id, 'bufan_product_cat' ) ) {
				$zh_id = bufan_setup_insert_term( $cat['zh'][0], $cat['zh'][1], $langs['zh'] );
				if ( $zh_id ) {
					/* translators: %s: category name */
					$log[] = sprintf( __( 'Created product category: %s', 'bufan' ), $cat['zh'][0] );
				}
			}
			if ( $zh_id ) {
				$ids['zh'] = $zh_id;
				pll_save_term_translations(
					array(
						$langs['en'] => $en_id,
						$langs['zh'] => $zh_id,
					)
				);
			}
		}
		$result[ $key ] = $ids;
	}

	update_option( 'bufan_categories', $stored );
	return $result;
}

/**
 * Insert (or reuse) a product category and set its language.
 *
 * @param string $name Term name.
 * @param string $slug Term slug.
 * @param string $lang Language slug, or ''.
 * @return int Term ID or 0.
 */
function bufan_setup_insert_term( $name, $slug, $lang ) {
	$existing = get_term_by( 'slug', $slug, 'bufan_product_cat' );
	if ( $existing ) {
		$id = (int) $existing->term_id;
	} else {
		$result = wp_insert_term( $name, 'bufan_product_cat', array( 'slug' => $slug ) );
		if ( is_wp_error( $result ) ) {
			$id = (int) $result->get_error_data( 'term_exists' );
		} else {
			$id = (int) $result['term_id'];
		}
	}
	if ( $id && $lang && function_exists( 'pll_set_term_language' ) ) {
		pll_set_term_language( $id, $lang );
	}
	return $id;
}

/**
 * Create the example products.
 *
 * @param array    $langs Language slugs.
 * @param array    $cats  Category IDs per key and language.
 * @param string[] $log   Log lines.
 */
function bufan_setup_products( $langs, $cats, &$log ) {
	$existing = get_option( 'bufan_sample_products', array() );
	if ( is_array( $existing ) ) {
		foreach ( $existing as $id ) {
			if ( bufan_setup_post_exists( (int) $id ) ) {
				$log[] = __( 'Example products already exist, skipped.', 'bufan' );
				return;
			}
		}
	}

	$created = array();
	foreach ( bufan_starter_products() as $index => $product ) {
		$image_id = bufan_setup_import_image( $product['image'], $product['en']['title'] );
		$ids      = array();
		foreach ( array( 'en', 'zh' ) as $code ) {
			if ( 'zh' === $code && empty( $langs['zh'] ) ) {
				continue;
			}
			$data = $product[ $code ];
			$id   = bufan_setup_insert_post(
				array(
					'post_type'    => 'bufan_product',
					'post_status'  => 'publish',
					'post_title'   => $data['title'],
					'post_name'    => $data['slug'],
					'post_excerpt' => $data['excerpt'],
					'post_content' => bufan_strip_block_markup( $data['content'] ),
					'menu_order'   => $index,
				),
				isset( $langs[ $code ] ) ? $langs[ $code ] : ''
			);
			if ( ! $id ) {
				continue;
			}
			foreach ( array_merge( $product['meta'], $data['meta'] ) as $key => $value ) {
				update_post_meta( $id, '_bufan_' . $key, $value );
			}
			if ( $image_id ) {
				set_post_thumbnail( $id, $image_id );
			}
			if ( ! empty( $cats[ $product['category'] ][ $code ] ) ) {
				wp_set_object_terms( $id, array( (int) $cats[ $product['category'] ][ $code ] ), 'bufan_product_cat' );
			}
			$ids[ $code ] = $id;
			$created[]    = $id;
		}
		if ( count( $ids ) > 1 && function_exists( 'pll_save_post_translations' ) ) {
			pll_save_post_translations(
				array(
					$langs['en'] => $ids['en'],
					$langs['zh'] => $ids['zh'],
				)
			);
		}
	}
	update_option( 'bufan_sample_products', $created );
	/* translators: %d: number of products */
	$log[] = sprintf( __( 'Created %d example products.', 'bufan' ), count( $created ) );
}

/**
 * Copy an example illustration from the theme into the media library.
 *
 * @param string $file  File name in assets/img/samples.
 * @param string $title Image title and alt text.
 * @return int Attachment ID or 0.
 */
function bufan_setup_import_image( $file, $title ) {
	$source = BUFAN_DIR . '/assets/img/samples/' . $file;
	if ( ! file_exists( $source ) ) {
		return 0;
	}
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_bufan_sample_image', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'fields'         => 'ids',
			'posts_per_page' => 1,
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$upload = wp_upload_bits( 'bufan-' . $file, null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$type          = wp_check_filetype( $upload['file'] );
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $type['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
		return 0;
	}
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_bufan_sample_image', $file );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );
	return (int) $attachment_id;
}

/**
 * Create the main and footer menus for each language and assign them.
 *
 * @param array    $langs Language slugs.
 * @param array    $pages Page IDs per key and language.
 * @param array    $cats  Category IDs per key and language.
 * @param string[] $log   Log lines.
 */
function bufan_setup_menus( $langs, $pages, $cats, &$log ) {
	$codes     = empty( $langs['zh'] ) ? array( 'en' ) : array( 'en', 'zh' );
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations = is_array( $locations ) ? $locations : array();
	$theme     = get_option( 'stylesheet' );
	$pll_menus = array();
	if ( $langs && function_exists( 'PLL' ) ) {
		$options   = PLL()->options;
		$pll_menus = is_object( $options ) && method_exists( $options, 'get' ) ? $options->get( 'nav_menus' ) : ( isset( $options['nav_menus'] ) ? $options['nav_menus'] : array() );
		$pll_menus = is_array( $pll_menus ) ? $pll_menus : array();
	}

	$labels = array(
		'primary' => array(
			'en' => 'Main menu (English)',
			'zh' => 'Main menu (中文)',
		),
		'footer'  => array(
			'en' => 'Footer menu (English)',
			'zh' => 'Footer menu (中文)',
		),
	);

	foreach ( $codes as $code ) {
		foreach ( array( 'primary', 'footer' ) as $location ) {
			if ( $langs ) {
				$assigned = isset( $pll_menus[ $theme ][ $location ][ $langs[ $code ] ] ) ? (int) $pll_menus[ $theme ][ $location ][ $langs[ $code ] ] : 0;
			} else {
				$assigned = isset( $locations[ $location ] ) ? (int) $locations[ $location ] : 0;
			}
			if ( $assigned && wp_get_nav_menu_object( $assigned ) ) {
				continue;
			}

			$menu = wp_get_nav_menu_object( $labels[ $location ][ $code ] );
			if ( $menu ) {
				$menu_id = (int) $menu->term_id;
			} else {
				$menu_id = wp_create_nav_menu( $labels[ $location ][ $code ] );
				if ( is_wp_error( $menu_id ) ) {
					continue;
				}
				bufan_setup_menu_items( $menu_id, $location, $code, $pages, $cats );
				/* translators: %s: menu name */
				$log[] = sprintf( __( 'Created menu: %s', 'bufan' ), $labels[ $location ][ $code ] );
			}

			if ( $langs ) {
				$pll_menus[ $theme ][ $location ][ $langs[ $code ] ] = $menu_id;
				if ( 'en' === $code ) {
					$locations[ $location ] = $menu_id;
				}
			} else {
				$locations[ $location ] = $menu_id;
			}
		}
	}

	set_theme_mod( 'nav_menu_locations', $locations );
	if ( $langs ) {
		bufan_pll_set_option( 'nav_menus', $pll_menus );
	}
}

/**
 * Fill a new menu.
 *
 * @param int    $menu_id  Menu ID.
 * @param string $location 'primary' or 'footer'.
 * @param string $code     'en' or 'zh'.
 * @param array  $pages    Page IDs per key and language.
 * @param array  $cats     Category IDs per key and language.
 */
function bufan_setup_menu_items( $menu_id, $location, $code, $pages, $cats ) {
	$add_page = function ( $key, $parent = 0 ) use ( $menu_id, $code, $pages ) {
		if ( empty( $pages[ $key ][ $code ] ) ) {
			return 0;
		}
		return (int) wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-type'      => 'post_type',
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $pages[ $key ][ $code ],
				'menu-item-parent-id' => $parent,
				'menu-item-status'    => 'publish',
			)
		);
	};

	if ( 'footer' === $location ) {
		foreach ( array( 'about', 'customization', 'faq', 'blog', 'contact' ) as $key ) {
			$add_page( $key );
		}
		return;
	}

	$add_page( 'home' );
	$products = (int) wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-type'   => 'post_type_archive',
			'menu-item-object' => 'bufan_product',
			'menu-item-title'  => 'zh' === $code ? '产品中心' : 'Products',
			'menu-item-status' => 'publish',
		)
	);
	foreach ( $cats as $cat ) {
		if ( empty( $cat[ $code ] ) ) {
			continue;
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-type'      => 'taxonomy',
				'menu-item-object'    => 'bufan_product_cat',
				'menu-item-object-id' => $cat[ $code ],
				'menu-item-parent-id' => $products,
				'menu-item-status'    => 'publish',
			)
		);
	}
	foreach ( array( 'customization', 'about', 'blog', 'contact' ) as $key ) {
		$add_page( $key );
	}
}

/**
 * Chinese translation of the site tagline (Languages → Translations in Polylang).
 *
 * @param string $zh Chinese language slug.
 */
function bufan_setup_translate_strings( $zh ) {
	if ( ! class_exists( 'PLL_MO' ) || ! isset( PLL()->model ) ) {
		return;
	}
	$model    = PLL()->model;
	$language = isset( $model->languages ) && is_object( $model->languages ) ? $model->languages->get( $zh ) : $model->get_language( $zh );
	if ( ! $language ) {
		return;
	}
	$translations = array(
		'Custom embroidered canvas bags manufacturer' => '帆布刺绣包定制工厂',
	);
	$mo = new PLL_MO();
	$mo->import_from_db( $language );
	foreach ( $translations as $original => $translation ) {
		if ( get_option( 'blogdescription' ) === $original && empty( $mo->entries[ $original ] ) ) {
			$mo->add_entry( $mo->make_entry( $original, $translation ) );
		}
	}
	$mo->export_to_db( $language );
}

<?php
/**
 * Search form.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( bufan_home_url() ); ?>">
	<label class="screen-reader-text" for="bufan-search"><?php esc_html_e( 'Search', 'bufan' ); ?></label>
	<input type="search" id="bufan-search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search products and articles…', 'bufan' ); ?>">
	<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Search', 'bufan' ); ?></button>
</form>

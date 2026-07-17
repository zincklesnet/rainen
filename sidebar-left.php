<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$sidebar_id = reign_get_sidebar_id_to_show( 'secondary_sidebar' );

if ( ! $sidebar_id ) {
	global $post;

	if ( is_search() ) {
		$sidebar_id = get_theme_mod( 'reign_search_left_sidebar', '' );
	}
	if ( $post ) {
		$reign_post_type = get_post_type();
		if ( is_singular() ) {
			$sidebar_id = get_theme_mod( 'reign_' . $reign_post_type . '_single_left_sidebar', '' );
		} elseif ( ! is_search() ) {
			$sidebar_id = get_theme_mod( 'reign_' . $reign_post_type . '_archive_left_sidebar', '' );
		}
	}
}

if ( ! $sidebar_id ) {
	$sidebar_id = 'sidebar-left';
	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_shop() || is_post_type_archive( 'product' ) || is_product() || is_cart() || is_checkout() || is_product_category() ) {
			$sidebar_id = 'woocommerce-sidebar-left';
		}
	}

	if ( defined( 'FLUENTCART_PLUGIN_FILE_PATH' ) ) {
		$sidebar_id = 'fluentcart-sidebar-left';
	}
}

$sidebar_id = apply_filters( 'reign_set_left_sidebar_id', $sidebar_id );

if ( is_active_sidebar( $sidebar_id ) ) {
	ob_start();
	dynamic_sidebar( $sidebar_id );
	$sidebar_content = ob_get_clean();

	if ( ! empty( trim( $sidebar_content ) ) ) {
		?>
		<aside id="reign-sidebar-left" class="widget-area default" role="complementary">
			<div class="widget-area-inner">
				<?php echo $sidebar_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</aside>
		<?php
	}
}
?>

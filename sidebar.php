<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$sidebar_id = reign_get_sidebar_id_to_show( 'primary_sidebar' );

if ( ! $sidebar_id ) {
	global $post;

	if ( is_search() ) {
		$sidebar_id = get_theme_mod( 'reign_search_right_sidebar', '' );
	}

	if ( $post ) {
		$reign_post_type = get_post_type();
		if ( is_singular() ) {
			$sidebar_id = get_theme_mod( 'reign_' . $reign_post_type . '_single_right_sidebar', '' );
		} elseif ( ! is_search() ) {
			$sidebar_id = get_theme_mod( 'reign_' . $reign_post_type . '_archive_right_sidebar', '' );
		}
	}
}

if ( ! $sidebar_id ) {
	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_shop() || is_post_type_archive( 'product' ) || is_product() || is_cart() || is_checkout() || is_product_category() ) {
			$sidebar_id = 'woocommerce-sidebar-right';
		}
	}
	if ( class_exists( 'WC_Vendors' ) && WCV_Vendors::is_vendor_page() ) {
		$sidebar_id = 'wcvendors-store-sidebar';
	}
	if ( class_exists( 'Easy_Digital_Downloads' ) ) {
		if ( is_post_type_archive( 'download' ) || is_tax( 'download_category' ) || is_tax( 'download_tag' ) ) {
			$sidebar_id = 'edd-download-archive-sidebar';
		} elseif ( is_singular( 'download' ) ) {
			$sidebar_id = 'edd-single-download-sidebar';
		}
	}

	if ( defined( 'FLUENTCART_PLUGIN_FILE_PATH' ) ) {
		$sidebar_id = 'fluentcart-sidebar-right';
	}
}

if ( ! $sidebar_id ) {
	$sidebar_id = apply_filters( 'reign_set_sidebar_id', $sidebar_id );
	if ( ! $sidebar_id ) {
		$sidebar_id = 'sidebar-right';
	}
}

$display_sidebar = apply_filters( 'reign_alter_display_right_sidebar', $display = true );

if ( is_active_sidebar( $sidebar_id ) && $display_sidebar ) {
	ob_start();

	/* Check EDD Service plugin activate */
	if ( is_plugin_active( 'edd-sell-services/edd-sell-services.php' ) ) {
		$ess_general_settings = get_option( 'ess_general_settings' );
		/* Check EDD mnanager Order Page id and Current Page ID same then display EDD Service status widget*/
		if ( get_the_ID() === $ess_general_settings['ess_manage_order_page'] ) {
			the_widget(
				'edd_services_widget',
				array(),
				array(
					'before_title' => '<h4 class="widget-title"><span>',
					'after_title'  => '</span></h4>',
				)
			);
		}
	}

	dynamic_sidebar( $sidebar_id );

	$sidebar_content = ob_get_clean();

	if ( ! empty( trim( $sidebar_content ) ) ) {
		?>
		<aside id="reign-sidebar-right" class="widget-area default" role="complementary">
			<div class="widget-area-inner">
				<?php echo $sidebar_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</aside>
		<?php
	}
}
?>

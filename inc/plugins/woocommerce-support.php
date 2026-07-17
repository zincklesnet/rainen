<?php
/**
 * WooCommerce compatibility functions.
 *
 * @package Reign
 * @since 7.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Cart icon and count to header if WC is active.
 */
function my_wc_cart_count() {
	if ( is_admin() ) {
		return;
	}
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'WC' ) ) {
		return;
	}

	// Lesson #4 / Reign 8.0.0: guard against a deleted/orphaned WC cart
	// page. wc_get_cart_url() falls back to home_url() when the
	// woocommerce_cart_page_id option points at a trashed/missing page,
	// which would silently wire the cart icon to the homepage — confusing
	// for visitors and bad for analytics. If we cannot resolve a real cart
	// page distinct from home_url, suppress the icon rather than render
	// a misleading link.
	$cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '';
	$home_url = home_url( '/' );
	if ( '' === $cart_url || untrailingslashit( $cart_url ) === untrailingslashit( $home_url ) ) {
		return;
	}

	$cart  = WC()->cart;
	$count = $cart ? $cart->get_cart_contents_count() : 0;
	?>
	<div class="woo-cart-wrapper header-notifications-dropdown-toggle">
		<a class="rg-icon-wrap woo-cart-wrap dropdown-toggle" href="<?php echo esc_url( $cart_url ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'reign' ); ?>">
			<span class="far fa-shopping-cart"></span>
			<?php
			if ( $count > 0 ) {
				?>
				<span class="cart-contents-count rg-count"><?php echo esc_html( $count ); ?></span>
				<?php
			}
			?>
		</a>

		<div class="rg-woocommerce_mini_cart header-notifications-dropdown-menu">
			<?php woocommerce_mini_cart(); ?>
		</div>
	</div>
	<?php
}

/**
 * Ensure cart contents update when products are added to the cart via AJAX.
 *
 * Both filter callbacks below are registered only when WooCommerce is
 * active. The filter itself ('woocommerce_add_to_cart_fragments') only
 * fires when WC is active in practice, but gating the add_filter() call
 * means the function reference never even exists in the hook registry
 * on non-WC sites, and protects against activation-order races.
 */
if ( class_exists( 'WooCommerce' ) ) {
	add_filter( 'woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment' );

	/**
	 * Ensure mini cart contents update when products are added to the cart via AJAX.
	 */
	add_filter(
		'woocommerce_add_to_cart_fragments',
		function ( $fragments ) {

			ob_start();
			?>

			<div class="rg-woocommerce_mini_cart header-notifications-dropdown-menu">
				<?php woocommerce_mini_cart(); ?>
			</div>

			<?php
			$fragments['.rg-woocommerce_mini_cart.header-notifications-dropdown-menu'] = ob_get_clean();

			return $fragments;
		}
	);
}

if ( ! function_exists( 'my_header_add_to_cart_fragment' ) ) :
function my_header_add_to_cart_fragment( $fragments ) {
	// Defensive: only access WC()->cart when WooCommerce is loaded.
	if ( ! function_exists( 'WC' ) || ! WC() || ! WC()->cart ) {
		return $fragments;
	}
	$count = WC()->cart->get_cart_contents_count();
	ob_start();
	?>
	<a class="rg-icon-wrap woo-cart-wrap dropdown-toggle" href="#" title="<?php esc_attr_e( 'View your shopping cart', 'reign' ); ?>">
		<span class="far fa-shopping-cart"></span>
		<?php
		if ( $count > 0 ) {
			?>
			<span class="cart-contents-count rg-count"><?php echo esc_html( $count ); ?></span>
			<?php
		}
		?>
	</a>
	<?php
	$fragments['a.rg-icon-wrap.woo-cart-wrap.dropdown-toggle'] = ob_get_clean();
	return $fragments;
}
endif;

/**
 * WooCommerce mini cart shortcode render.
 */
function reign_woo_mini_cart_render() {
	ob_start();
	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_admin() ) {
			$count = '0';
		} else {
			$count = WC()->cart->get_cart_contents_count();
		}
		?>
		<div id="rg-mobile-icon-toggle" data-id="rg-slidebar-toggle">
			<a class="rg-icon-wrap woo-cart-wrap dropdown-toggle" href="#" title="<?php esc_attr_e( 'View your shopping cart', 'reign' ); ?>">
				<span class="far fa-shopping-cart"></span>
				<span class="cart-contents-count rg-count"><?php echo esc_html( $count ); ?></span>
			</a>
		</div>
		<?php
	}
	return ob_get_clean();
}

/**
 * Header v4 middle section search.
 */
add_action( 'reign_header_v4_middle_section_html', 'reign_header_v4_middle_section_search' );
function reign_header_v4_middle_section_search() {
	$choose_search = get_theme_mod( 'reign_header_search_option', 'product_search' );

	if ( class_exists( 'WooCommerce' ) ) {
		$check_search = $choose_search;
	} else {
		$check_search = 'default_search';
	}

	if ( 'product_search' === $check_search ) {
		the_widget( 'WC_Widget_Product_Search' );
	} elseif ( function_exists( 'get_search_form' ) ) {
			get_search_form();
	}
}

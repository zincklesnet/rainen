<?php
/**
 * Easy Digital Downloads compatibility functions.
 *
 * @package Reign
 * @since 7.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure EDD Cart Preview assets load on every frontend page so the header
 * cart icon can open the slide-out panel.
 */
add_filter(
	'edd_cart_preview_should_load',
	function ( $should_load ) {
		return ! is_admin();
	}
);

/**
 * EDD cart icon for header.
 *
 * Clicking the icon opens EDD Pro's Cart Preview slide-out panel (if available)
 * via the global `window.eddSlideoutCart.toggle()` API.
 */
function reign_edd_download_cart_render() {
	ob_start();
	if ( class_exists( 'Easy_Digital_Downloads' ) ) {
		$count       = is_admin() ? '0' : edd_get_cart_quantity();
		$checkout_url = edd_get_checkout_uri();
		?>
		<div class="rg-edd-cart-icon">
			<a
				class="rg-icon-wrap edd-cart-wrap"
				href="<?php echo esc_url( $checkout_url ); ?>"
				title="<?php esc_attr_e( 'View your shopping cart', 'reign' ); ?>"
				aria-label="<?php esc_attr_e( 'Shopping cart', 'reign' ); ?>"
				onclick="if(window.eddSlideoutCart){event.preventDefault();window.eddSlideoutCart.toggle();}"
			>
				<span class="far fa-shopping-cart" aria-hidden="true"></span>
				<span class="cart-contents-count rg-count edd-cart-quantity"><?php echo esc_html( $count ); ?></span>
			</a>
		</div>
		<?php
	}
	return ob_get_clean();
}

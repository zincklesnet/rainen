<?php
/**
 * Cart Icon
 *
 * Template part for displaying the cart count (WooCommerce or EDD)
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( class_exists( 'WooCommerce' ) ) {
	my_wc_cart_count();
} elseif ( class_exists( 'Easy_Digital_Downloads' ) ) {
	echo reign_edd_download_cart_render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- returns pre-escaped cart markup.
}

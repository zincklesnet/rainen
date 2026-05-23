<?php
/**
 * Dokan compatibility functions.
 *
 * @package Reign
 * @since 7.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add body class for Dokan product edit page.
 */
function wb_reign_manage_body_class( $classes ) {
	if ( function_exists( 'is_product' ) ) {
		if ( is_product() && get_query_var( 'edit' ) ) {
			$classes[] = 'rda-product-edit-screen';
		}
	}
	return $classes;
}

add_action( 'body_class', 'wb_reign_manage_body_class', 10, 1 );

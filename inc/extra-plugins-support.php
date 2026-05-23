<?php
/**
 * Extra plugins support - loader and general utilities.
 *
 * Plugin-specific code has been split into inc/plugins/ for maintainability.
 *
 * @package Reign
 * @since 7.9.6
 */

/* Resolving Changeset Related Issue In Theme Customizer */
add_filter(
	'get_post_status',
	function ( $post_status, $post ) {
		if ( ( $post->post_type == 'customize_changeset' ) && is_admin() ) {
			$post_status = '';
		}
		return $post_status;
	},
	10,
	2
);

/*
 * Support Added For WordPress Customizer API
 */
/**
 * Store current post ID.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'reigntm_post_id' ) ) {

	function reigntm_post_id() {

		// Default value
		$id = '';

		// If singular get_the_ID
		if ( is_singular() ) {
			$id = get_the_ID();
		}

		// Get ID of WooCommerce product archive
		elseif ( REIGN_WOOCOMMERCE_ACTIVE && is_shop() ) {
			$shop_id = wc_get_page_id( 'shop' );
			if ( isset( $shop_id ) ) {
				$id = $shop_id;
			}
		}

		// Posts page
		elseif ( is_home() && $page_for_posts = get_option( 'page_for_posts' ) ) {
			$id = $page_for_posts;
		}

		// Apply filters
		$id = apply_filters( 'wbcom_post_id', $id );

		// Sanitize
		$id = $id ? $id : '';

		// Return ID
		return $id;
	}
}

/**
 * Get attachment ID from URL.
 */
function reign_get_image_id_from_url( $image_url ) {
	global $wpdb;
	$attachment_id = '';
	$attachment    = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
	if ( $attachment ) {
		$attachment_id = $attachment[0];
	}
	return $attachment_id;
}

/*
 * Load modular plugin support files.
 */
require_once REIGN_INC_DIR . 'plugins/elementor-support.php';
require_once REIGN_INC_DIR . 'plugins/woocommerce-support.php';
require_once REIGN_INC_DIR . 'plugins/edd-support.php';
require_once REIGN_INC_DIR . 'plugins/buddypress-support.php';
require_once REIGN_INC_DIR . 'plugins/peepso-support.php';
require_once REIGN_INC_DIR . 'plugins/bbpress-support.php';
require_once REIGN_INC_DIR . 'plugins/dokan-support.php';

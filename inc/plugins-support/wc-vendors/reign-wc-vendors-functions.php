<?php
/**
 * Support For Sensei LMS
 *
 * @package reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'reign_wcvendors_render_store_header_on_top' ) ) {
	/**
	 * Displays the custom header on Sensei single course pages.
	 *
	 * @return void
	 */
	function reign_wcvendors_render_store_header_on_top() {

		// WCV_Vendors (the new namespace) is a different class from
		// WC_Vendors (the gate this file is loaded behind). On installs
		// where only the older class is present, direct static calls
		// to WCV_Vendors would fatal.
		if ( ! class_exists( 'WCV_Vendors' ) ) {
			return;
		}

		// Set header on single vendor page.
		if ( WCV_Vendors::is_vendor_page() ) {

			if ( class_exists( 'Reign_Theme_Structure' ) ) {
				$reign_theme_structure_obj = Reign_Theme_Structure::instance();
				remove_action( 'reign_before_content', array( $reign_theme_structure_obj, 'render_page_header' ) );
			}

			$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
			$vendor_id   = WCV_Vendors::get_vendor_id( $vendor_shop );
			$vendor_meta = array_map(
				function ( $a ) {
					return $a[0];
				},
				get_user_meta( $vendor_id )
			);

			do_action( 'reign_wc_vendors_before_main_header', $vendor_id );

			wc_get_template(
				'store-header-modern.php',
				array(
					'vendor_id'   => $vendor_id,
					'vendor_meta' => $vendor_meta,
				),
				'wc-vendors/store/',
				get_template_directory() . '/wc-vendors/store/'
			);

			do_action( 'reign_wc_vendors_after_main_header', $vendor_id );
		}
	}

	add_action( 'reign_before_content', 'reign_wcvendors_render_store_header_on_top', 9 );
}

/**
 * Format and print store address.
 *
 * @param  integer $vendor_id [description]
 * @return string
 */
if ( ! function_exists( 'reign_wc_vendors_format_store_address' ) ) {

	/**
	 * Formats and returns the store address of a given vendor.
	 *
	 * @param int $vendor_id The unique ID of the vendor whose store address is to be formatted.
	 * @return string The formatted store address for the vendor, or an empty string if no address is found.
	 */
	function reign_wc_vendors_format_store_address( $vendor_id ) {
		$store_address_args = apply_filters(
			'reign_wc_vendors_format_store_address_args',
			array(
				'address1' => get_user_meta( $vendor_id, '_wcv_store_address1', true ),
				'city'     => get_user_meta( $vendor_id, '_wcv_store_city', true ),
				'state'    => get_user_meta( $vendor_id, '_wcv_store_state', true ),
				'postcode' => get_user_meta( $vendor_id, '_wcv_store_postcode', true ),
				'country'  => ( function_exists( 'WC' ) && WC() && isset( WC()->countries ) && isset( WC()->countries->countries[ get_user_meta( $vendor_id, '_wcv_store_country', true ) ] ) ) ? WC()->countries->countries[ get_user_meta( $vendor_id, '_wcv_store_country', true ) ] : '',
			),
			$vendor_id
		);

		$store_address_args = array_filter( $store_address_args );

		return apply_filters( 'reign_wc_vendors_format_store_address_output', implode( ', ', $store_address_args ), $vendor_id );
	}
}

/**
 * Formate Store Url
 *
 * @var integer
 * @return string
 */
if ( 'reign_wc_vendors_format_store_url' ) {

	/**
	 * Formats the vendor's store URL based on the vendor ID.
	 *
	 * @param int $vendor_id The ID of the vendor.
	 * @return string The formatted vendor store URL.
	 */
	function reign_wc_vendors_format_store_url( $vendor_id ) {
		$store_url = get_user_meta( $vendor_id, '_wcv_company_url', true );
		if ( ! $store_url ) {
			return '';
		}

		return apply_filters(
			'reign_wc_vendors_format_store_url',
			sprintf( '<a href="%1$s">%1$s</a>', $store_url ),
			$vendor_id
		);
	}
}

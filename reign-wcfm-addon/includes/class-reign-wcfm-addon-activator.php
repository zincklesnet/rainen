<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/includes
 */
// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Reign_Wcfm_Addon_Activator {

	/**
	 * update setting's option on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$reign_wcfm_options = get_option( 'reign_options', array() );
		if ( ! array_key_exists( 'wcfm_option', $reign_wcfm_options ) ) {
			if ( empty( $reign_wcfm_options['wcfm_option'] ) ) {
				$reign_wcfm_options['wcfm_option'] = array(
					'vendor_store'            => 'on',
					'product_activity'        => 'on',
					'review_activity'         => 'on',
					'product_favourite'       => 'on',
					'reign_wcfm_store_layout' => 'layout_one',
				);
				update_option( 'reign_options', $reign_wcfm_options );
			}
		}
	}
}

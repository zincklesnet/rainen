<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Quotes_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$bpquotes_gnrl_settings = get_option( 'bpquotes_gnrl_settings' );
		if ( ! ( $bpquotes_gnrl_settings ) && empty( $bpquotes_gnrl_settings ) ) {
			$bpquotes_gnrl_settings                       = array();
			$bpquotes_gnrl_settings['bg_colors']          = array( '#dd3333', '#27aa32', '#1e73be', '#eeee22', '#20606d', '#8224e3', '#20d8d2', '#6ccc06' );
			$bpquotes_gnrl_settings['bg_inverted_colors'] = array( '#22cccc', '#d855cd', '#e18c41', '#1111dd', '#df9f92', '#7ddb1c', '#df272d', '#9333f9' );
			update_option( 'bpquotes_gnrl_settings', $bpquotes_gnrl_settings );
		}
	}

}

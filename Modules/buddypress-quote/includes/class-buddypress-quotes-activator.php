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
			$user_roles            = wp_roles()->get_names();
			unset($user_roles['administrator']);
			$user_roles = array_keys($user_roles);
			
			$bpquotes_gnrl_settings                       = array();
			$bpquotes_gnrl_settings['bg_colors']          = array( '#dd3333', '#27aa32', '#1e73be', '#eeee22', '#20606d', '#8224e3', '#20d8d2', '#6ccc06' );
			$bpquotes_gnrl_settings['bg_inverted_colors'] = array( '#ffffff', '#ffffff', '#ffffff', '#000000', '#ffffff', '#ffffff', '#ffffff', '#ffffff' );
			$bpquotes_gnrl_settings['bg_allow_quote_icon'] = 'yes';
			$bpquotes_gnrl_settings['user_role'] = $user_roles;
			
			$bpquotes_gnrl_settings['image_url'] = array(
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-01.jpg',
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-02.jpg',
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-03.jpg',
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-04.jpg',
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-05.jpg',
			);
			update_option( 'bpquotes_gnrl_settings', $bpquotes_gnrl_settings );
		}
	}

}

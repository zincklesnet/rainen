<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Reign_Tutorlms_Addon
 * @subpackage Reign_Tutorlms_Addon/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Reign_Tutorlms_Addon
 * @subpackage Reign_Tutorlms_Addon/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Reign_Tutorlms_Addon_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wbtm_reign_settings;

		// Check if functions file exists before requiring it (it's in the same directory)
		$functions_file = dirname( __FILE__ ) . '/reign-tutor-lms-functions.php';
		
		if ( ! file_exists( $functions_file ) ) {
			wp_die( 
				__( 'Required functions file missing. Please reinstall the plugin.', 'reign-tutorlms-addon' ),
				__( 'Plugin Activation Error', 'reign-tutorlms-addon' )
			);
		}

		if( ! isset( $wbtm_reign_settings['tutorlms'] ) ) {
			// Load theme compatibility defaults
			require_once $functions_file;
			
			// Check if function exists before calling it
			if ( function_exists( 'reign_get_tutorlms_theme_defaults' ) ) {
				$reign_tutorlms_settings = reign_get_tutorlms_theme_defaults();
			} else {
				// Fallback default settings
				$reign_tutorlms_settings = array(
					'profile' => array(
						'enable_profile_courses_tab' => 0
					)
				);
			}

			$wbtm_reign_settings['tutorlms'] = $reign_tutorlms_settings;
			update_option( 'reign_options', $wbtm_reign_settings );
		}

	}

}

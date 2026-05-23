<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Schedule_Activity
 * @subpackage Buddypress_Schedule_Activity/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Buddypress_Schedule_Activity
 * @subpackage Buddypress_Schedule_Activity/includes
 * @author     Wbcom Designs <contact@wbcomdesigns>
 */
class Buddypress_Schedule_Activity_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'buddypress-schedule-activity',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Schedule_Activity
 * @subpackage Buddypress_Schedule_Activity/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Buddypress_Schedule_Activity
 * @subpackage Buddypress_Schedule_Activity/includes
 * @author     Wbcom Designs <contact@wbcomdesigns>
 */
class Buddypress_Schedule_Activity_Deactivator {

	/**
	 * Plugin deactivation cleanup.
	 *
	 * Clears scheduled cron events and transients.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Clear the scheduled cron event.
		$timestamp = wp_next_scheduled( 'buddypress_schedule_activity_publish' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'buddypress_schedule_activity_publish' );
		}

		// Clear any lingering publish lock transient.
		delete_transient( 'bpsa_publish_lock' );
	}
}

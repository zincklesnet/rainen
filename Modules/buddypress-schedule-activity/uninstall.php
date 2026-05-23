<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Schedule_Activity
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Clear the scheduled cron event.
$timestamp = wp_next_scheduled( 'buddypress_schedule_activity_publish' );
if ( $timestamp ) {
	wp_unschedule_event( $timestamp, 'buddypress_schedule_activity_publish' );
}

// Clear transients.
delete_transient( 'bpsa_publish_lock' );

// Clean up activity meta created by this plugin.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
	"DELETE FROM {$wpdb->prefix}bp_activity_meta
	WHERE meta_key IN ('_bp_activity_status', '_bp_schedule_activity_title_for_youzify')"
);

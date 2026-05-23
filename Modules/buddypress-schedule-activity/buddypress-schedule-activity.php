<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com
 * @since             1.0.0
 * @package           Buddypress_Schedule_Activity
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Schedule Activity
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-schedule-activity/
 * Description:       BuddyPress Schedule Activity allows you to organize and manage your Activity planner, making it easier to plan and schedule activities.
 * Version:           1.4.3
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-schedule-activity
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BUDDYPRESS_SCHEDULE_ACTIVITY_VERSION', '1.4.3' );
define( 'BUDDYPRESS_SCHEDULE_ACTIVITY_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-schedule-activity-activator.php
 */
function activate_buddypress_schedule_activity() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-schedule-activity-activator.php';
	Buddypress_Schedule_Activity_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-schedule-activity-deactivator.php
 */
function deactivate_buddypress_schedule_activity() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-schedule-activity-deactivator.php';
	Buddypress_Schedule_Activity_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buddypress_schedule_activity' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_schedule_activity' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-schedule-activity.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_schedule_activity() {

	if ( class_exists( 'Buddypress' ) ) {
		$plugin = new Buddypress_Schedule_Activity();
		$plugin->run();
	}
}
add_action( 'plugins_loaded', 'run_buddypress_schedule_activity', 10 );

if ( ! function_exists( 'bp_nouveau_schedule_activity_current_user_can' ) ) {

	function bp_nouveau_schedule_activity_current_user_can( $capability = '' ) {
		/**
		 * Filters whether or not the current user can perform an action for BuddyPress Nouveau.
		 *
		 * @since 3.0.0
		 *
		 * @param bool   $value      Whether or not the user is logged in.
		 * @param string $capability Current capability being checked.
		 * @param int    $value      Current logged in user ID.
		 */
		return apply_filters( 'bp_nouveau_current_user_can', is_user_logged_in(), $capability, bp_loggedin_user_id() );
	}
}


/**
 *  Check if buddypress activate.
 */
function buddypress_schedule_activity_requires_buddypress() {
	$active_plugins = get_option( 'active_plugins' );
	if ( ! class_exists( 'Buddypress' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'buddypress_schedule_activity_required_plugin_admin_notice' );
	}
}

add_action( 'admin_init', 'buddypress_schedule_activity_requires_buddypress' );

/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  2.3.0
 */
function buddypress_schedule_activity_required_plugin_admin_notice() {
	$bsa_plugin = esc_html__( 'BuddyPress Schedule Activity', 'buddypress-schedule-activity' );
	$bp_plugin  = esc_html__( 'BuddyPress', 'buddypress-schedule-activity' );
	$bb_plugin  = esc_html__( 'BuddyBoss', 'buddypress-schedule-activity' );
	echo '<div class="error"><p>';
	// translators: %1$s is replaced with the BuddyPress Schedule Activity and %2$s is replaced with the BuddyPress or %3$s is replaced with the BuddyBoss.
	printf( esc_html__( '%1$s requires either %2$s or %3$s to be installed and active in order to function.', 'buddypress-schedule-activity' ), '<strong>' . esc_html( $bsa_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>', '<strong>' . esc_html( $bb_plugin ) . '</strong>' );
	echo '</p></div>';
}

add_filter( 'youzify_activity_tools', 'bp_schedule_activity_add_delete_activity_tool', 100, 2 );

function bp_schedule_activity_add_delete_activity_tool( $tools, $post_id ) {

	$_bp_activity_status = bp_activity_get_meta( $post_id, '_bp_activity_status', true );
	if ( $_bp_activity_status === 'scheduled' ) {
		foreach ( $tools as $key => $value ) {
			if ( $value['action'] == 'delete-activity' ) {
				$tools[ $key ]['class'] = array( 'youzify-delete-tool', 'delete-activity' );
			}
		}
	}
	return $tools;
}

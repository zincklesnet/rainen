<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Buddypress_Edit_Activity
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Edit Activity
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-edit-activity
 * Description:       Edit BuddyPress activity posts from the front-end with ease.
 * Version:           1.3.0
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-edit-activity
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
define( 'BUDDYPRESS_EDIT_ACTIVITY_VERSION', '1.3.0' );
define( 'BP_EDIT_ACTIVITIES_URL', plugin_dir_url( __FILE__ ) );
define( 'BP_EDIT_ACTIVITIES_PATH', plugin_dir_path( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-edit-activity-activator.php
 */
function activate_buddypress_edit_activity() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-edit-activity-activator.php';
	Buddypress_Edit_Activity_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-edit-activity-deactivator.php
 */
function deactivate_buddypress_edit_activity() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-edit-activity-deactivator.php';
	Buddypress_Edit_Activity_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buddypress_edit_activity' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_edit_activity' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-edit-activity.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_edit_activity() {
	$plugin = new Buddypress_Edit_Activity();
	$plugin->run();

}
add_action( 'bp_include', 'run_buddypress_edit_activity' );


/**
 *  Check if buddypress activate.
 */
function buddypress_edit_activity_requires_buddypress() {

	if ( ! class_exists( 'Buddypress' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'buddypress_edit_activity_required_plugin_admin_notice' );
	} 
}

add_action( 'admin_init', 'buddypress_edit_activity_requires_buddypress' );


/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  1.403.0
 */
function buddypress_edit_activity_required_plugin_admin_notice() {

	$bpbp_plugin = esc_html__( 'BuddyPress Edit Activity', 'buddypress-edit-activity' );
	$bp_plugin   = esc_html__( 'BuddyPress', 'buddypress-edit-activity' );
	echo '<div class="error"><p>';
	/* translators: %s: */
	echo sprintf( esc_html__( '%1$s is ineffective because it requires %2$s to be installed and active.', 'buddypress-edit-activity' ), '<strong>' . esc_html( $bpbp_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' );
	echo '</p></div>';
}

/**
 * redirect to plugin settings page after activated
 */

 add_action( 'activated_plugin', 'buddypress_edit_activity_activation_redirect_settings' );
 function buddypress_edit_activity_activation_redirect_settings( $plugin ) {
	$active_plugins = get_option( 'active_plugins' );
	 if ( $plugin == plugin_basename( __FILE__ ) && class_exists( 'Buddypress' ) ) {
		 if ( isset( $_REQUEST['action'] ) && $_REQUEST['action']  == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
			 wp_redirect( admin_url( 'admin.php?page=buddypress-edit-activity' ) );
			 exit;
		 }
	}
 }

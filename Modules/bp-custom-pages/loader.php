<?php
/*
Plugin Name: BP Custom Pages
Plugin URI: https://wordpress.org/plugins/bp-custom-pages
Description: Add's Admin defined custom pages to the users profile menu
Version: 1.2.0
Requires at least: WP 3.2.1, BuddyPress 1.5
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Venutius
Author URI: https://buddyuser.com
Network: true
Text Domain: bp-custom-pages
Domain Path: /languages
*/
// Load Text Domain
if ( file_exists( dirname( __FILE__ ) . '/languages/bp-custom-pages-' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-custom-pages', dirname( __FILE__ ) . '/languages/bp-custom-pages-' . get_locale() . '.mo' );


// Define a constant that can be checked to see if the component is installed or not.
define( 'BP_CUSTOM_PAGES_IS_INSTALLED', 1 );

// Define a constant that will hold the current version number of the component
// This can be useful if you need to run update scripts or do compatibility checks in the future
define( 'BP_CUSTOM_PAGES_VERSION', '1.1.1' );

// Define a constant that we can use to construct file paths throughout the component
define( 'BP_CUSTOM_PAGES_PLUGIN_DIR', dirname( __FILE__ ) );

/* Define a constant that will hold the database version number that can be used for upgrading the DB
 *
 * NOTE: When table defintions change and you need to upgrade,
 * make sure that you increment this constant so that it runs the install function again.
 *
 * Also, if you have errors when testing the component for the first time, make sure that you check to
 * see if the table(s) got created. If not, you'll most likely need to increment this constant as
 * BP_CUSTOM_PAGES_DB_VERSION was written to the wp_usermeta table and the install function will not be
 * triggered again unless you increment the version to a number higher than stored in the meta data.
 */
define ( 'BP_CUSTOM_PAGES_DB_VERSION', '1' );

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_custom_pages_init() {
	if ( version_compare( BP_VERSION, '1.9', '<' ) ) {
		return;
	}

	require( BP_CUSTOM_PAGES_PLUGIN_DIR . '/includes/bp-custom-pages-loader.php' );
}
add_action( 'bp_include', 'bp_custom_pages_init' );

/* Put setup procedures to be run when the plugin is activated in the following function */
function bp_custom_pages_activate() {
	if ( ! class_exists( 'BP_Component' ) ) {
		die('BuddyPress must be active before you can activate BP Custom Pages Pro' );
	}
	if ( function_exists( 'bp_custom_pages_pro_activate' ) ) {
			die( 'BP Custom PAges Pro is already active, no need to run BP Custom Pages' );
	}
	wp_reset_postdata();
}

register_activation_hook( __FILE__, 'bp_custom_pages_activate' );

/* On deacativation, clean up anything your component has added. */
function bp_custom_pages_deactivate() {

}
register_deactivation_hook( __FILE__, 'bp_custom_pages_deactivate' );

function bpcp_register_plugins_links( $links, $file ) {
	$base = plugin_basename( __FILE__ );
	if ( $file == $base ) {
		$links[] = '<a style="color: #cc0000 ; font-weight: bold;" href="https://buddyuser.com/pro-plugin-bp-custom-pages-pro" target="_blank">' . __( 'Upgrade to Pro Version', 'bp-custom-pages' ) . '</a>';
	}
	return $links;
}

add_filter( 'plugin_row_meta', 'bpcp_register_plugins_links', 10, 2 );

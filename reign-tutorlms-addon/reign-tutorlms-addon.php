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
 * @package           Reign_Tutorlms_Addon
 *
 * @wordpress-plugin
 * Plugin Name:       Reign TutorLMS Addon
 * Plugin URI:        https://wbcomdesigns.com/downloads/reign-tutor-lms-addon/
 * Description:       Provides Reign Theme styling and compatibility enhancements for TutorLMS.
 * Version:           2.0.0
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       reign-tutorlms-addon
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
if ( ! defined( 'REIGN_TUTORLMS_ADDON_VERSION' ) ) {
	define( 'REIGN_TUTORLMS_ADDON_VERSION', '2.0.0' );
}

if ( ! defined( 'REIGN_TUTORLMS_ADDON_FILE' ) ) {
	define( 'REIGN_TUTORLMS_ADDON_FILE', __FILE__ );
}

if ( ! defined( 'REIGN_TUTORLMS_ADDON_PLUGIN_DIR' ) ) {
	define( 'REIGN_TUTORLMS_ADDON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'REIGN_TUTORLMS_ADDON_PLUGIN_URL' ) ) {
	define( 'REIGN_TUTORLMS_ADDON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-reign-tutorlms-addon-activator.php
 */
function activate_reign_tutorlms_addon() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-reign-tutorlms-addon-activator.php';
	Reign_Tutorlms_Addon_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-reign-tutorlms-addon-deactivator.php
 */
function deactivate_reign_tutorlms_addon() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-reign-tutorlms-addon-deactivator.php';
	Reign_Tutorlms_Addon_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_reign_tutorlms_addon' );
register_deactivation_hook( __FILE__, 'deactivate_reign_tutorlms_addon' );

/**
 * Load core plugin functions
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/reign-tutor-lms-functions.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-reign-tutorlms-addon.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_reign_tutorlms_addon() {
	$plugin = new Reign_Tutorlms_Addon();
	$plugin->run();
}

// Initialize after plugins loaded to ensure dependencies are available
add_action( 'plugins_loaded', 'run_reign_tutorlms_addon', 20 );

/**
 *  Check if Reign theme activate.
 */
add_action( 'admin_init', 'reign_tutorlms_requires_reign_theme' );
function reign_tutorlms_requires_reign_theme() {

	$template = get_option( 'template' );

	if ( $template != 'reign-theme' ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'reign_tutorlms_required_theme_admin_notice' );
		unset( $_GET['activate'] );
	}

}

function reign_tutorlms_required_theme_admin_notice() {

	$plugin_name = esc_html__( 'Reign TutorLMS addon', 'reign-tutorlms-addon' );
	$theme_name  = 'Reign Theme';
	echo '<div class="error"><p>';
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s to be installed and active.', 'reign-tutorlms-addon', 'reign-tutorlms-addon' ), '<strong>' . esc_html( $plugin_name ) . '</strong>', '<strong>' . esc_html( $theme_name ) . '</strong>' );
	echo '</p></div>';
	if ( isset( $_GET['activate'] ) ) { //phpcs:ignore
		unset( $_GET['activate'] );
	}
}

/**
 *  Check if TutorLMS plugin is active (Required).
 */
add_action( 'admin_init', 'reign_tutorlms_addon_requires_tutor_lms' );
function reign_tutorlms_addon_requires_tutor_lms() {
	// Only check for TutorLMS, not BuddyPress/BuddyBoss
	if ( ! is_plugin_active( 'tutor/tutor.php' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'reign_tutorlms_addon_required_tutor_lms_admin_notice' );
		unset( $_GET['activate'] );
	}
}

/**
 * Admin notice for missing TutorLMS.
 */
function reign_tutorlms_addon_required_tutor_lms_admin_notice() {
	$plugin_name = esc_html__( 'Reign TutorLMS Addon', 'reign-tutorlms-addon' );
	?>
	<div class="notice notice-error">
		<p>
			<?php
			echo sprintf(
				esc_html__( '%1$s requires TutorLMS to be installed and active.', 'reign-tutorlms-addon' ),
				'<strong>' . esc_html( $plugin_name ) . '</strong>'
			);
			?>
		</p>
	</div>
	<?php
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}


add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'reign_tutorlms_plugin_links' );
/**
 * Function to add settings link to the plugin actions.
 */
function reign_tutorlms_plugin_links( $links ) {
    
    $admin_url   = admin_url( 'admin.php' );
    $admin_url   = add_query_arg(  
        array(
            'page' => 'reign-options',
            'tab' => 'tutorlms'
        ), $admin_url
    );
    $reign_tutor_links = array(
		'<a href="' . esc_url( $admin_url ) . '">' . __( 'Settings', 'reign-tutorlms-addon' ) . '</a>',
	);
	return array_merge( $links, $reign_tutor_links );
}



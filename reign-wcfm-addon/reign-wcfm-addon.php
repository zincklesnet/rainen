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
 * @package           Reign_Wcfm_Addon
 *
 * @wordpress-plugin
 * Plugin Name:       Reign WCFM Addon
 * Plugin URI:        https://wbcomdesigns.com/downloads/reign-wcfm-addon/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.8.3
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       reign-wcfm-addon
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
if ( ! defined( 'REIGN_WCFM_ADDON_VERSION' ) ) {
	define( 'REIGN_WCFM_ADDON_VERSION', '1.8.3' );
}

if ( ! defined( 'REIGN_WCFM_ADDON_FILE' ) ) {
	define( 'REIGN_WCFM_ADDON_FILE', __FILE__ );
}

if ( ! defined( 'REIGN_WCFM_ADDON_URL' ) ) {
	define( 'REIGN_WCFM_ADDON_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'REIGN_WCFM_ADDON_PATH' ) ) {
	define( 'REIGN_WCFM_ADDON_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-reign-wcfm-addon-activator.php
 */
function activate_reign_wcfm_addon() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-reign-wcfm-addon-activator.php';
	Reign_Wcfm_Addon_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-reign-wcfm-addon-deactivator.php
 */
function deactivate_reign_wcfm_addon() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-reign-wcfm-addon-deactivator.php';
	Reign_Wcfm_Addon_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_reign_wcfm_addon' );
register_deactivation_hook( __FILE__, 'deactivate_reign_wcfm_addon' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-reign-wcfm-addon.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_reign_wcfm_addon() {

	$plugin = new Reign_Wcfm_Addon();
	$plugin->run();

}
run_reign_wcfm_addon();


/**
 *  Checks if the required Reign Theme, WooCommerce, and WCFM plugins are active.
 * 
 * @return void
 */
add_action( 'admin_init', 'reign_wcfm_requires_reign_theme' );
function reign_wcfm_requires_reign_theme() {

	$template = get_option( 'template' );

	if ( $template != 'reign-theme' || ! class_exists( 'WCFM' ) || ! class_exists( 'WooCommerce' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'reign_wcfm_required_theme_admin_notice' );
		unset( $_GET['activate'] );
	}

}

/**
 * Displays an admin notice when required plugins or the Reign theme are not active.
 * 
* @return void
 */
function reign_wcfm_required_theme_admin_notice() {

	$plugin_name      = esc_html__( 'Reign WCFM Addon', 'reign-wcfm-addon' );
	$wcfm_plugin_name = esc_html__( 'WCFM - WooCommerce Frontend Manager', 'reign-wcfm-addon' );
	$woocommerce_plugin_name = esc_html__( 'WooCommerce', 'reign-wcfm-addon' );
	$theme_name       = 'Reign Theme';
	echo '<div class="error"><p>';
	// Translators: %s is the plugin name.
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s, %3$s and %4$s to be installed and active.', 'reign-wcfm-addon' ), '<strong>' . esc_html( $plugin_name ) . '</strong>', '<strong>' . esc_html( $theme_name ) . '</strong>', '<strong>' . esc_html( $wcfm_plugin_name ) . '</strong>', '<strong>' . esc_html( $woocommerce_plugin_name ) . '</strong>' );
	echo '</p></div>';
	if ( isset( $_GET['activate'] ) ) { //phpcs:ignore
		unset( $_GET['activate'] );
	}
}

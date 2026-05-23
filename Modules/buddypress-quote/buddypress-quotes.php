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
 * @since             1.1.0
 * @package           Buddypress_Quotes
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Quotes
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       This plugin lets users select background images and colors set by admin while posting  BuddyPress activity update.
 * Version:           1.9.1
 * Author:            wbcomdesigns
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-quotes
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
define( 'BPQUOTES_NAME_VERSION', '1.9.1' );

define( 'BPQUOTES_DIR', dirname( __FILE__ ) );
define( 'BPQUOTES_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BPQUOTES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BPQUOTES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'BPQUOTES_PLUGIN_FILE' ) ) {
	define( 'BPQUOTES_PLUGIN_FILE', __FILE__ );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-quotes-activator.php
 */
function activate_buddypress_quotes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-quotes-activator.php';
	Buddypress_Quotes_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-quotes-deactivator.php
 */
function deactivate_buddypress_quotes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-quotes-deactivator.php';
	Buddypress_Quotes_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buddypress_quotes' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_quotes' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-quotes.php';
require plugin_dir_path( __FILE__ ) . 'edd-license/edd-plugin-license.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_quotes() {
	$plugin = new Buddypress_Quotes();
	$plugin->run();
}

/**
 * Add Body Class.
 *
 * @param  array $classes Body Class.
 */
function bpquotes_add_body_class( $classes ) {
	return array_merge( $classes, array( 'wb-quote' ) );
}


add_action( 'plugins_loaded', 'bpquotes_add_plugin_backwords_compatibility' );
if ( ! function_exists( 'bpquotes_add_plugin_backwords_compatibility' ) ) {
	/**
	 * Check BuddyPress is avialable or not.
	 *
	 * @return void
	 */
	function bpquotes_add_plugin_backwords_compatibility() {
		if ( class_exists( 'BuddyPress' ) ) {
			run_buddypress_quotes();
			add_filter( 'body_class', 'bpquotes_add_body_class' );
		} else {
			add_action( 'admin_notices', 'bpquotes_required_notice' );
		}
	}
}


if ( ! function_exists( 'bpquotes_required_notice' ) ) {
	/**
	 * Display admin notice if BuddyPress is not installed or activated.
	 */
	function bpquotes_required_notice() {

		$addon_plugin = __( 'BuddyPress Quotes', 'buddypress-quotes' );
		$bp_plugin    = __( 'BuddyPress', 'buddypress-quotes' );
		echo '<div class="error"><p>'
		/* translators: %1$s: BuddyPress Quotes, %2$s: BuddyPress  */
		. sprintf( esc_html__( '%1$s is ineffective as it requires %2$s to be installed and active.', 'buddypress-quotes' ), '<strong>' . esc_html( $addon_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' )
		. '</p></div>';
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

/**
 * Redirect to plugin settings page after activated.
 *
 * @param array $plugin Activated Plugins.
 */
function bpquotesactivation_redirect_settings( $plugin ) {

	if ( plugin_basename( __FILE__ ) === $plugin && class_exists( 'BuddyPress' ) ) {
		wp_redirect( admin_url( 'admin.php?page=buddypress-quotes' ) );
		exit;
	}
}
add_action( 'activated_plugin', 'bpquotesactivation_redirect_settings' );

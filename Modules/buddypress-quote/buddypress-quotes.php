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
 * Plugin Name:       Wbcom Designs - BuddyPress Quotes
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-quotes/
 * Description:       This plugin lets users select background images and colors set by the admin when posting BuddyPress activity updates.
 * Version:           2.5.0
 * Author:            Wbcom Designs
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
define( 'BPQUOTES_NAME_VERSION', '2.5.0' );

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
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpquotes_plugin_links' );
		} else {
			add_action( 'admin_notices', 'bpquotes_required_notice' );
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}
}


if ( ! function_exists( 'bpquotes_required_notice' ) ) {
	/**
	 * Display admin notice if BuddyPress is not installed or activated.
	 */
	function bpquotes_required_notice() {

		$addon_plugin = esc_html__( 'BuddyPress Quotes', 'buddypress-quotes' );
		$bp_plugin    = esc_html__( 'BuddyPress/BuddyBoss Platform', 'buddypress-quotes' );
		echo '<div class="error"><p>'
		/* translators: %1$s: BuddyPress Quotes, %2$s: BuddyPress  */
		. sprintf( esc_html__( '%1$s is ineffective because it requires %2$s to be installed and active.', 'buddypress-quotes' ), '<strong>' . esc_html( $addon_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' )
		. '</p></div>';
		// phpcs:disable
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		// phpcs:enable
	}
}

/**
 * Redirect to plugin settings page after activated.
 *
 * @param array $plugin Activated Plugins.
 */
function bpquotesactivation_redirect_settings( $plugin ) {
	// phpcs:disable
	if ( ! isset( $_GET['plugin'] ) ) {
		return;
	}
	if ( plugin_basename( __FILE__ ) === $plugin && class_exists( 'BuddyPress' ) ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action']  == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) {
			wp_redirect( admin_url( 'admin.php?page=buddypress-quotes' ) );
			exit;
		}
	}
	// phpcs:enable
}
add_action( 'activated_plugin', 'bpquotesactivation_redirect_settings' );

/**
 * Function to set plugin actions links.
 *
 * @param array $links Plugin settings link array.
 * @since 1.0.0
 */
function bpquotes_plugin_links( $links ) {
	$bpquotes_links = array(
		'<a href="' . admin_url( 'admin.php?page=buddypress-quotes' ) . '">' . __( 'Settings', 'buddypress-quotes' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'buddypress-quotes' ) . '</a>',
	);
	return array_merge( $links, $bpquotes_links );
}

function bpqutoes_set_default_option() {
	
	global $pagenow;
	$admin_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : 'buddypress_status';
	
	if( false === get_option( 'bpqutes_version_flag_2_2_2' ) ) {
		$quotes_options = get_option( 'bpquotes_gnrl_settings' );

		if( ! empty( $quotes_options ) ) {
			$quotes_options['image_url'] = array(
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-01.jpg',
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-02.jpg',
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-03.jpg',
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-04.jpg',
				BPQUOTES_PLUGIN_URL . 'admin/images/quotes-bg-05.jpg',
			);

			update_option( 'bpquotes_gnrl_settings', $quotes_options );
		}

		update_option( 'bpqutes_version_flag_2_2_2', true, false );
	}
	
	
	
	if ( ! get_option( 'bpqutoes_update_2_3_1' ) && ( isset( $admin_page ) && 'buddypress-quotes' === $admin_page || 'plugins.php' === $pagenow ) ) {
		$bpquotes_gnrl_settings                     = get_option( 'bpquotes_gnrl_settings' );
		$user_roles            = wp_roles()->get_names();
		unset($user_roles['administrator']);
		$user_roles = array_keys($user_roles);
		$bpquotes_gnrl_settings['user_role'] = $user_roles;
		update_option( 'bpquotes_gnrl_settings', $bpquotes_gnrl_settings );
		update_option( 'bpqutoes_update_2_3_1', 1 );		
	}
}
add_action( 'admin_init', 'bpqutoes_set_default_option' );

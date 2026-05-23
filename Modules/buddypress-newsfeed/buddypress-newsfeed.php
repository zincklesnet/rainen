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
 * @package           Buddypress_Newsfeed
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Newsfeed
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       BuddyPress Newsfeed merges BuddyPress mentions, favorites, personal activities, groups, etc., in one place.
 * Version:           1.8.3
 * Author:            wbcomdesigns
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-newsfeed
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'BNEWS_PLUGIN_VERSION' ) ) {
	define( 'BNEWS_PLUGIN_VERSION', '1.8.3' );
}

if ( ! defined( 'BNEWS_PLUGIN_FILE' ) ) {
	define( 'BNEWS_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BNEWS_PLUGIN_BASENAME' ) ) {
	define( 'BNEWS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'BNEWS_PLUGIN_URL' ) ) {
	define( 'BNEWS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'BNEWS_PLUGIN_PATH' ) ) {
	define( 'BNEWS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-newsfeed.php';

/**
 * Require plugin license file.
 */
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
function bpnewsfeed_run_plugin() {
	if ( class_exists( 'Buddypress_Newsfeed' ) ) {
		$plugin = new Buddypress_Newsfeed();
		$plugin->run();
	}
}
add_action( 'bp_include', 'bpnewsfeed_run_plugin' );

/**
 *  Check if BuddyPress is activated..
 */
function bpnewsfeed_requires_buddypress() {
	if ( ! class_exists( 'Buddypress' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'bpnewsfeed_required_plugin_admin_notice' );
		if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
			$activate = filter_input( INPUT_GET, 'activate' );
			unset( $activate );
		}
	}
}

add_action( 'admin_init', 'bpnewsfeed_requires_buddypress' );
/**
 * Show an admin notice explaining why the plugin was not activated.
 *
 * @author wbcomdesigns
 * @since  1.1.0
 */
function bpnewsfeed_required_plugin_admin_notice() {
	$bpnewsfeed_plugin = esc_html__( 'BuddyPress Newsfeed', 'buddypress-newsfeed' );
	$bp_plugin       = esc_html__( 'BuddyPress', 'buddypress-newsfeed' );
	echo '<div class="error"><p>';
	/* translators: %1$s: BuddyPress Newsfeed, %2$s: BuddyPress */
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s to be installed and active.', 'buddypress-newsfeed' ), '<strong>' . esc_html( $bpnewsfeed_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' );
	echo '</p></div>';
	if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
		$activate = filter_input( INPUT_GET, 'activate' );
		unset( $activate );
	}
}


/**
 * Redirect to plugin settings page after activated.
 *
 * @since  1.0.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 */
function bpnewsfeed_activation_redirect_settings( $plugin ) {

	if ( plugin_basename( __FILE__ ) === $plugin && class_exists( 'Buddypress' ) ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action']  == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
			wp_safe_redirect( admin_url( 'admin.php?page=buddypress_newsfeed' ) );
			exit;
		}
	}
}
add_action( 'activated_plugin', 'bpnewsfeed_activation_redirect_settings' );


register_activation_hook( __FILE__, 'newsfeed_plugin_activation' );

// Activation hook callback
function newsfeed_plugin_activation() {
	$default_options = array(
		'disable_all'   => 'yes',
		'first_tab'        => 'newsfeed',
		'post_form_enable' => 'yes',
	);

	if ( false === get_option( 'bnews_general_settings' ) ) {

		update_option( 'bnews_general_settings', $default_options );
	}
}

/**
 * Post form not showing on newsfeed tab when activity tab is enabled in buddyboss .
 */
function bpnewsfeed_activity_post_form() {
	if ( ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) ) {
		if ( function_exists( 'bp_is_activity_tabs_active' ) && bp_is_activity_tabs_active() &&  bp_is_my_profile() && 'newsfeed' == bp_current_action() ) {
			?>
	<script>
		jQuery(document).ready(function(){
			jQuery("#item-body #bp-nouveau-activity-form").removeClass('bp-hide').removeClass('is-bp-hide');
		});
	</script>
			<?php
		}
	} 
}
	add_action( 'wp_footer', 'bpnewsfeed_activity_post_form' );

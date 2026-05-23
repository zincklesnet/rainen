<?php
/**
 * Plugin Name: BuddyPress Activity Bump
 * Plugin URI: http://wbcomdesigns.com
 * Description: Bumps an activity record to the top of the stream on activity comment replies and like
 * Author:     Wbcom Designs
 * Author URI: http://wbcomdesigns.com
 * Version: 1.2.4
 * Text Domain:   bp-activity-bump
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           bp-activity-bump
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
if ( ! defined( 'BP_ACTIVITY_BUMP_VERSION' ) ) {
	define( 'BP_ACTIVITY_BUMP_VERSION', '1.2.4' );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_DIR' ) ) {
	define( 'BP_ACTIVITY_BUMP_DIR', trailingslashit( dirname( __FILE__ ) ) );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_PLUGIN_PATH' ) ) {
	define( 'BP_ACTIVITY_BUMP_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_PLUGIN_URL' ) ) {
	define( 'BP_ACTIVITY_BUMP_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_PLUGIN_BASENAME' ) ) {
	define( 'BP_ACTIVITY_BUMP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_PLUGIN_FILE' ) ) {
	define( 'BP_ACTIVITY_BUMP_PLUGIN_FILE', __FILE__ );
}

if ( ! function_exists( 'wb_bp_activity_comment_posted' ) ) {

	/**
	 * Function to update activity template on comment posted
	 *
	 * @global type $bp
	 * @global type $wpdb
	 * @param type $comment_id comment_id.
	 * @param type $params params.
	 * @return boolean
	 */
	function wb_bp_activity_comment_posted( $comment_id, $params ) {
		global $bp, $wpdb;
		extract( $params, EXTR_SKIP );
		$activity_parent = bp_activity_get_specific( array( 'activity_ids' => $activity_id ) );

		if ( ! $activity_parent = $activity_parent['activities'][0] ) {
			return;
		}

		if ( ! bp_activity_get_meta( $activity_id, 'activity_bump_date' ) ) {
			bp_activity_update_meta( $activity_id, 'activity_bump_date', $activity_parent->date_recorded );
		}

		$activity                = new BP_Activity_Activity( $activity_id );
		$activity->date_recorded = gmdate( 'Y-m-d H:i:s' );
		$update                  = $wpdb->get_results( "UPDATE  {$wpdb->prefix}bp_activity SET date_recorded = '" . gmdate( 'Y-m-d H:i:s' ) . "'  WHERE  id=" . $activity_id );
		if ( ! $update ) {
			return false;
		}

		if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) {
			include locate_template( array( 'activity/entry.php' ), false );
		}
	}
	$bp_bump_genral_setting        = get_option( 'bp_bump_admin_general_options' );
	$bp_bump_get_comment_acitivity = isset( $bp_bump_genral_setting['bp_bump_activity_option'] ) ? $bp_bump_genral_setting['bp_bump_activity_option'] : '';
	if ( 'commented-activity' === $bp_bump_get_comment_acitivity || 'both-activity' === $bp_bump_get_comment_acitivity ) {
		add_action( 'bp_activity_comment_posted', 'wb_bp_activity_comment_posted', 10, 2 );

	}
}

if ( ! function_exists( 'wb_bp_activity_bump_time_since' ) ) {

	/**
	 * Function to update activity action on comment posted and favorite button click
	 *
	 * @global type $bp
	 * @param string $content content.
	 * @param type   $activity activity.
	 * @return string
	 */
	function wb_bp_activity_bump_time_since( $content, $activity ) {
		global $bp;
		if ( ! $date = bp_activity_get_meta( $activity->id, 'activity_bump_date' ) ) {
			return $content;
		}

		/* translators: %s: */
		$content = '<span class="time-since">' . sprintf( esc_html__( ' updated %s', 'bp-activity-bump' ), bp_core_time_since( $activity->date_recorded ) ) . '</span>';
		/* translators: %s: */
		return '<span class="time-since time-created">' . sprintf( __( ' %s', 'bp-activity-bump' ), bp_core_time_since( $date ) ) . '</span> &middot; ';
	}

	add_filter( 'bp_activity_time_since', 'wb_bp_activity_bump_time_since', 10, 2 );
}


if ( ! function_exists( 'wb_add_like_notification' ) ) {

	/**
	 * Function to update activity stream on activity like
	 *
	 * @global type $bp
	 * @global type $wpdb
	 * @param type $activity_id activity_id.
	 * @param type $user_id user_id.
	 * @return boolean
	 */
	function wb_add_like_notification( $activity_id, $user_id ) {
		global $bp, $wpdb;

		$activity_parent = bp_activity_get_specific( array( 'activity_ids' => $activity_id ) );

		if ( ! $activity_parent = $activity_parent['activities'][0] ) {
			return;
		}

		if ( ! bp_activity_get_meta( $activity_id, 'activity_bump_date' ) ) {
			bp_activity_update_meta( $activity_id, 'activity_bump_date', $activity_parent->date_recorded );
		}

		$activity                = new BP_Activity_Activity( $activity_id );
		$activity->date_recorded = gmdate( 'Y-m-d H:i:s' );
		$update                  = $wpdb->get_results( "UPDATE  {$wpdb->prefix}bp_activity SET date_recorded = '" . gmdate( 'Y-m-d H:i:s' ) . "'  WHERE  id=" . $activity_id );
		if ( ! $update ) {
			return false;
		}

		if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) {
			include locate_template( array( 'activity/entry.php' ), false );
		}
	}
	$bp_bump_genral_setting    = get_option( 'bp_bump_admin_general_options' );
	$bp_bump_get_like_activity = isset( $bp_bump_genral_setting['bp_bump_activity_option'] ) ? $bp_bump_genral_setting['bp_bump_activity_option'] : '';
	if ( 'favorite-activity' === $bp_bump_get_like_activity || 'both-activity' === $bp_bump_get_like_activity ) {
		add_action( 'bp_activity_add_user_favorite', 'wb_add_like_notification', 9, 2 );

	}
}

/**
 * Including file for wbcom admin setting.
 */
require plugin_dir_path( __FILE__ ) . 'admin/wbcom/wbcom-admin-settings.php';

/**
 * Including file for admin setting.
 */
require_once plugin_dir_path( __FILE__ ) . 'admin/bp-bump-admin.php';
/**
 * Redirect to plugin settings page after activated.
 */
if ( class_exists( 'BuddyPress' ) ) {
	add_action( 'activated_plugin', 'bp_bump_activation_redirect_settings' );
}
/**
 * Bp_bump_activation_redirect_settings
 *
 * @param  mixed $plugin plugin.
 * @return void
 */
function bp_bump_activation_redirect_settings( $plugin ) {

	if ( plugin_basename( __FILE__ ) === $plugin ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action']  == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin) { //phpcs:ignore
			wp_redirect( admin_url( 'admin.php?page=wbcomplugins' ) );
			exit;
		}
	}
}
require plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://demos.wbcomdesigns.com/exporter/free-plugins/bp-activity-bump.json',
	__FILE__, // Full path to the main plugin file or functions.php.
	'bp-activity-bump'
);

add_action( 'plugins_loaded', 'bp_bump_plugin_check_required_plugin' );
/**
 * Check plugin requirement on plugins loaded,this plugin requires BuddyPress to be installed and active.
 *
 * @since 1.0.0
 */
function bp_bump_plugin_check_required_plugin() {

	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'BuddyPress' ) ) {
		add_action( 'admin_notices', 'bp_bump_plugin_admin_notice' );
		add_action( 'admin_init', 'bp_bump_remove_existing_bp_bump_plugin' );
	} else {
		$bump_option = get_option( 'bp_bump_admin_general_options' );
		if ( empty( $bump_option ) ) {
			$bump_option['bp_bump_activity_option'] = 'both-activity';
			update_option( 'bp_bump_admin_general_options', $bump_option );
		}
	}
}

/**
 * Function to remove check-in plugin if already exist.
 *
 * @since 1.0.0
 */
function bp_bump_remove_existing_bp_bump_plugin() {
	$bp_bump_plugin = plugin_dir_path( __DIR__ ) . 'bp-activity-bump/bp-bump.php';
	// Check to see if plugin is already active.
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
}

/**
 * Function to through notice when buddypress plugin is not activated.
 *
 * @since 1.0.0
 */
function bp_bump_plugin_admin_notice() {
	$bp_bump_plugin = 'BuddyPress Activity Bump';
	$bp_plugin      = 'BuddyPress';

	echo '<div class="error"><p>'
	/* translators: %s: */
	. sprintf( esc_html__( '%1$s is ineffective as it requires %2$s to be installed and active.', 'bp-activity-bump' ), '<strong>' . esc_attr( $bp_bump_plugin ) . '</strong>', '<strong>' . esc_attr( $bp_plugin ) . '</strong>' )
	. '</p></div>';
	if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
		$activate = filter_input( INPUT_GET, 'activate' );
		unset( $activate );
	}
}

add_filter( 'bp_rest_activity_prepare_value', 'bp_activity_bump_data_embed_rest_api', 10, 3 );

/**
 * Embed bp activity bump data in rest api activity endpoint.
 *
 * @param  object $response get response data.
 * @param  object $request get request data.
 * @param  array  $activity get activity data.
 * @return $response
 */
function bp_activity_bump_data_embed_rest_api( $response, $request, $activity ) {
	$bp_activity_bump_date              = bp_activity_get_meta( $activity->id, 'activity_bump_date', true );
	$bp_activity_bump_fvt_count         = bp_activity_get_meta( $activity->id, 'favorite_count', true );
	$bp_activity_bump_data              = array(
		'activity_bump_date' => $bp_activity_bump_date,
		'favorite_count'     => $bp_activity_bump_fvt_count,
	);	
	$response->data['bp_activity_bump'] = $bp_activity_bump_data;
	return $response;
}

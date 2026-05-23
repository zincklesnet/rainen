<?php
/**
 * Plugin Name: BP Post Status
 * Plugin URI: https://buddyuser.com/plugin-bp-post-status
 * Author URI: https://buddyuser.com/
 * Description: Adds post support to BuddyPress, Group Posts, Site Members only, and friends only post shares.
 * Author: Venutius
 * Text Domain: bp-post-status
 * Domain Path: /languages
 * Version: 2.0.3
 * License: https://www.gnu.org/copyleft/gpl.html
*/

// Todo: Add uninstall script
// Todo: Add admin settings for pending statuses? 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bpps_check_buddypress() {
    
	if ( ! class_exists( 'buddypress' ) ) {
        
		add_action( 'admin_notices', 'bpps_no_bp_admin_notice' );
    
		return;
	
	}

}

add_action( 'plugins_loaded', 'bpps_check_buddypress' );


function bpps_no_bp_admin_notice() {
    ?>

    <div class="error fade notice-error6 is-dismissible">

		<p><?php 
		/*translators: Admin notification that plugin cannot work without BuddyPress */
		esc_attr_e( 'The BuddyPress needs to be active for BP Post Status to work.', 'bp-post-status' ); ?></p>
    
	</div>

	<?php
	return;
}



// Since 1.0.1 add plugin globals

//Component slug used in url, can be overridden in bp-custom.php
if ( ! defined( 'BPPS_GROUP_NAV_SLUG' ) ) {
	define( 'BPPS_GROUP_NAV_SLUG', 'group-posts' );
}

// Since 1.8.0 add group moderation slug

//Component slug used in url, can be overridden in bp-custom.php
if ( ! defined( 'BPPS_GROUP_MODERATION_SLUG' ) ) {
	define( 'BPPS_GROUP_MODERATION_SLUG', 'group-posts-moderation' );
}

// @since 1.7.4
// Define my posts, pending and my posts moderation slugs.
define( 'BPPS_MY_POSTS_NAV_SLUG', 'my-posts' );
define( 'BPPS_PROFILE_PENDING_POSTS_NAV_SLUG', 'my-posts-pending' );
define( 'BPPS_PROFILE_MODERATION_POSTS_NAV_SLUG', 'my-posts-moderation' );

// @since 1.1.0
// Define plugin directories
define( 'BPPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define	( 'BPPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Since 1.0.0 load plugin components

if ( ! class_exists( 'BP_Statuses' ) ) {
    
    include_once("includes/bp-statuses.php");
    
}

if ( ! class_exists( 'BPPS_Post_Status_Plugin' ) ) {

	include_once( "includes/core/bpps-post-status.php" );

}

// Since 1.6.3 including filters

include_once( 'includes/core/bpps-filters.php' );

// Since 1.7.0 including bp members functions

include_once( 'includes/buddypress/bp-members.php' );

include_once( 'includes/core/bpps-functions.php' );

if ( ! class_exists( 'BPPS_Activity' ) ) {
    
    include_once( "includes/buddypress/bp-activity.php" );
    
}

if ( ! class_exists( 'BPPS_Group_Admin' ) ) {
    
    include_once( "includes/buddypress/bp-group-admin.php" );
    
}


if ( ! class_exists( 'BPPS_Notifications' ) ) {
    
    include_once( "includes/buddypress/bp-notifications.php" );
    
}

if ( ! class_exists( 'BPPS_Admin_Options' ) ) {
    
    include_once( "includes/core/bpps-admin-options.php" );
    
}



// Since 1.1.0 load Groups functions

if ( ! class_exists( 'BPPS_Groups' ) ) {
    
    include_once( "includes/buddypress/bp-groups.php" );
    
}



include_once( 'includes/core/bpps-template-tags.php' );
include_once( 'includes/core/bpps-ajax.php' );
include_once( 'includes/buddypress/bp-emails.php' );

if ( ! class_exists( 'BP_Statuses_Core_Status' ) ) {
    
    require( "includes/inc/core/classes/class-bp-statuses-core-status.php" );

}

if ( ! class_exists( 'BP_Statuses_Admin' ) ) {
    
    require( "includes/inc/admin/classes/class-bp-statuses-admin.php" );
    
}

include_once( "includes/core/bpps-shortcodes.php" );

function bpps_load_bp() {
	
    include_once( "includes/buddypress/bp-admin.php" );
    
}

Add_action( 'bp_loaded', 'bpps_load_bp' );

// Add Settings Link

function bpps_add_action_links( $links ) {
	$review_link = '<a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/bp-post-status?filter=5#pages" title="' . esc_attr(__('If you like it, please review the plugin', 'bp-post-status')) . '">' . esc_attr(__('Review the plugin', 'bp-post-status')) . '</a>';
	$url = get_admin_url(null, 'options-general.php?page=bpps');
 
	$links[] = '<a href="'. $url .'">'.esc_attr(__('Settings','bp-post-status')).'</a>';
	$links[] = $review_link;
	return $links;

}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'bpps_add_action_links' );


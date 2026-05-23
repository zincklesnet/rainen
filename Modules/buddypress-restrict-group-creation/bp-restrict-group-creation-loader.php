<?php
/*
Plugin Name: BuddyPress Restrict Group Creation
Plugin URI: http://wordpress.org/plugins/buddypress-restrict-group-creation/
Description: Extend restricting group creation with mappings to WordPress Capabilities and various thresholds
Author: Venutius
Author URI: http://buddyuser.com
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Copyright: (c) 2007 - 2017 Rich @ etivite, 2018 onwards BuddyPress User.
Version: 1.2.0
Text Domain: buddypress-restrict-group-creation
Network: true
*/

//TODO - really really clean up the admin code page

function etivite_bp_restrictgroups_init() {

	if ( file_exists( dirname( __FILE__ ) . '/languages/buddypress-restrict-group-creation-' . get_locale() . '.mo' ) )
		load_textdomain( 'buddypress-restrict-group-creation', dirname( __FILE__ ) . '/languages/buddypress-restrict-group-creation-' . get_locale() . '.mo' );
		
	require( dirname( __FILE__ ) . '/bp-restrict-group-creation.php' );
	
	add_action( bp_core_admin_hook(), 'etivite_bp_restrictgroups_add_admin' );
	
}
add_action( 'bp_include', 'etivite_bp_restrictgroups_init', 88 );

//add admin_menu page
function etivite_bp_restrictgroups_add_admin() {
	global $bp;
	
	if ( !is_super_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-restrict-group-creation-admin.php' );

	add_submenu_page( 'options-general.php', __( 'Restrict Group Creation Admin', 'buddypress-restrict-group-creation' ), __( 'Restrict Group Creation', 'buddypress-restrict-group-creation' ), 'manage_options', 'buddypress-restrict-group-creation-settings', 'etivite_bp_restrictgroups_admin' );	
}


/* Stolen from Welcome Pack - thanks, Paul! then stolen from boone*/
function etivite_bp_restrictgroups_admin_add_action_link( $links, $file ) {
	if ( 'buddypress-restrict-group-creation/bp-restrict-group-creation-loader.php' != $file )
		return $links;

	if ( function_exists( 'bp_core_do_network_admin' ) ) {
		$settings_url = add_query_arg( 'page', 'buddypress-restrict-group-creation-settings', bp_core_do_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	} else {
		$settings_url = add_query_arg( 'page', 'buddypress-restrict-group-creation-settings', is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	}

	$settings_link = '<a href="' . $settings_url . '">' . __( 'Settings', 'buddypress-restrict-group-creation' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'etivite_bp_restrictgroups_admin_add_action_link', 10, 2 );
?>

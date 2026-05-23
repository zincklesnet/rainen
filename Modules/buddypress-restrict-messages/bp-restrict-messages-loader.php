<?php
/*
Plugin Name: BuddyPress Restrict Messages
Plugin URI: https://wordpress.org/plugins/buddypress-restrict-messages/
Description: Restrict private messaging - sitewide or member controlled
Author: venutius
Author URI: http://buddyuser.com
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 1.1.0
Text Domain: bp-restrict-messages
Network: false
Copyright: to 2018 Rich @etivite, 2019 onwards venutius @buddyuser
*/

function etivite_bp_restrict_messages_init() {

	//load if we care
	if ( !bp_is_active( 'messages' ) )
		return;

	load_textdomain( 'bp-restrict-messages', dirname( __FILE__ ) . '/languages/' );
	
	require( dirname( __FILE__ ) . '/bp-restrict-messages.php' );
	
	add_action( bp_core_admin_hook(), 'etivite_bp_restrict_messages_admin_add_admin_menu' );
	
}
add_action( 'bp_include', 'etivite_bp_restrict_messages_init', 88 );
//add_action( 'bp_init', 'etivite_bp_restrict_messages_init', 88 );

//add admin_menu page
function etivite_bp_restrict_messages_admin_add_admin_menu() {
	global $bp;
	
	if ( !current_user_can( 'manage_options' ) )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-restrict-messages-admin.php' );

	add_submenu_page( 'options-general.php', esc_attr__( 'Restrict Messages Admin', 'bp-restrict-messages' ), esc_attr__( 'Restrict Messages', 'bp-restrict-messages' ), 'manage_options', 'bp-restrict-messages-settings', 'etivite_bp_restrict_messages_admin' );


	$seed = Array();
	$seed['mode']['user']['enabled'] = false;
	$seed['mode']['sitewide']['enabled'] = false;
	$seed['pref']['daysregistered']['enabled'] = false;
	$seed['pref']['daysregistered']['count'] = 0;
	$seed['pref']['friends']['enabled'] = false;
	$seed['pref']['follow']['enabled'] = false;
	$seed['pref']['follow']['status'] = 'shared';
	$seed['pref']['samegroup']['enabled'] = false;
	$seed['pref']['samegroup']['status'] = 'public';
	add_option( 'etivite_bp_restrict_messages', $seed);

}

/* Stolen from Welcome Pack - thanks, Paul! then stolen from boone*/
function etivite_bp_restrict_messages_admin_add_action_link( $links, $file ) {
	if ( 'buddypress-restrict-messages/bp-restrict-messages-loader.php' != $file )
		return $links;

	if ( function_exists( 'bp_core_do_network_admin' ) ) {
		$settings_url = add_query_arg( 'page', 'bp-restrict-messages-settings', bp_core_do_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	} else {
		$settings_url = add_query_arg( 'page', 'bp-restrict-messages-settings', is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	}

	$settings_link = '<a href="' . $settings_url . '">' . esc_attr__( 'Settings', 'bp-restrict-messages' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'etivite_bp_restrict_messages_admin_add_action_link', 10, 2 );
?>

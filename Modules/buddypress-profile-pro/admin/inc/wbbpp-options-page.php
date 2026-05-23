<?php
/**
 *
 * This template file is used for fetching desired options page file at admin settings end.
 *
 * @package Buddypress_Profile_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$admin_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
if ( isset( $admin_tab ) ) {
	$bprm_tab = $admin_tab;
} else {
	$bprm_tab = 'welcome';
}

bprm_include_admin_setting_tabs( $bprm_tab );

/**
 * Include setting template.
 *
 * @param string $bprm_tab bprm_tab.
 */
function bprm_include_admin_setting_tabs( $bprm_tab ) {
	switch ( $bprm_tab ) {
		case 'welcome':
			include 'wbbpp-welcome-page.php';
			break;
		case 'general':
			include 'wbbpp-setting-general-tab.php';
			break;
		case 'support':
			include 'wbbpp-setting-support-tab.php';
			break;
		case 'gen_settings':
			include 'wbbpp-setting-fields-tab.php';
			break;
		case 'profile_search':
			include 'wbbpp-setting-profile-search-tab.php';
			break;
		case 'group_settings':
			include 'wbbpp-setting-groups-tab.php';
			break;
		default:
			include 'wbbpp-welcome-page.php';
			break;
	}
}

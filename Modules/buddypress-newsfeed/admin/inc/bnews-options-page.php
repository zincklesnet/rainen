<?php
/**
 * This template file is used for fetching desired options page file at admin settings end.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/inc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$admin_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
if ( isset( $admin_tab ) ) {
	$bpht_tab = $admin_tab;
} else {
	$bpht_tab = 'welcome';
}

bpht_include_admin_setting_tabs( $bpht_tab );

/**
 * Include setting template.
 *
 * @param string $bpht_tab Admin Tabs.
 */
function bpht_include_admin_setting_tabs( $bpht_tab ) {
	switch ( $bpht_tab ) {
		case 'welcome':
			include 'bnews-welcome-page.php';
			break;
		case 'general':
			include 'bnews-setting-general-tab.php';
			break;
		default:
			include 'bnews-welcome-page.php';
			break;
	}
}

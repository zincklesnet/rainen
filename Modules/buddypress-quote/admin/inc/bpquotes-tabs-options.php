<?php
/**
 * This template file is used for fetching desired options page file at admin settings end.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( isset( $_GET['tab'] ) ) {
	$bpsts_tab = sanitize_text_field( $_GET['tab'] );
} else {
	$bpsts_tab = 'welcome';
}

bpquotes_include_admin_setting_tabs( $bpsts_tab );

/**
 * Include setting template.
 *
 * @param string $bpsts_tab Admin tabs.
 */
function bpquotes_include_admin_setting_tabs( $bpsts_tab ) {
	switch ( $bpsts_tab ) {
		case 'welcome':
			include 'bpquotes-welcome-page.php';
			break;
		case 'general':
			include 'bpquotes-setting-general-tab.php';
			break;
		default:
			include 'bpquotes-welcome-page.php';
			break;
	}
}

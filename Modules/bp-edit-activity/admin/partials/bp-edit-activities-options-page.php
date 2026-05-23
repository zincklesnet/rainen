<?php
	/**
	 * This template file is used for fetching desired options page file at admin settings end.
	 *
	 * @link       https://wbcomdesigns.com/
	 *
	 * @since      1.0.0
	 *
	 * @package    Buddypress_Edit_Activities
	 * @subpackage Buddypress_Edit_Activities/admin/partials
	 */

	// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$bp_edit_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';

switch ( $bp_edit_tab ) {
	case 'welcome':
		include 'bp-edit-activities-welcome-page.php';
		break;        
	case 'faq':
		include 'bp-edit-activities-faq.php';
		break;
}

<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



function pp_events_profile() {
	add_action( 'bp_template_content', 'pp_events_profile_screen' );
	bp_core_load_template( 'members/single/plugins' );
}


function pp_events_profile_screen() {
	bp_get_template_part('members/single/profile-events-loop');
}


function pp_events_profile_create() {
	require( PP_EVENTS_DIR . '/inc/pp-events-create-class.php' );
	add_action( 'bp_template_title', 'pp_events_profile_create_title' );
	add_action( 'bp_template_content', 'pp_events_profile_create_screen' );
	bp_core_load_template( 'members/single/plugins' );
}

function pp_events_profile_create_title() {

	if ( isset( $_GET['eid'] ) ) {
		_e( 'Edit Event', 'bp-simple-events' );
	} else {
		_e( 'Create an Event', 'bp-simple-events' );
	}
}


function pp_events_profile_create_screen() {
	bp_get_template_part('members/single/profile-events-create');
}


function pp_events_profile_archive() {
	add_action( 'bp_template_content', 'pp_events_profile_archive_screen' );
	bp_core_load_template( 'members/single/plugins' );
}

function pp_events_profile_archive_screen() {
	bp_get_template_part('members/single/profile-events-archive');
}


function pp_events_profile_enqueue() {

	if ( ( bp_is_my_profile() || is_super_admin() ) && 'create' == bp_current_action() ) {

		$gapikey = get_site_option( 'pp_gapikey' );
		$skip_google = get_option( 'pp_skip_google' );

		if ( $gapikey != false && ! $skip_google ) {
			wp_register_script( 'google-places-api', '//maps.googleapis.com/maps/api/js?key=' . $gapikey . '&libraries=places' );
			wp_register_script( 'ppse_location_script', plugin_dir_url(__FILE__) . '/js/events_google_location.js', array('jquery'), '4.2' );
			wp_print_scripts( 'google-places-api' );
			wp_print_scripts( 'ppse_location_script' );
		} else {
			wp_deregister_script( 'google-places-api', '//maps.googleapis.com/maps/api/js?key=' . $gapikey . '&libraries=places' );
			wp_deregister_script( 'ppse_location_script', plugin_dir_url(__FILE__) . '/js/events_google_location.js' );
		}



		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-timepicker', plugins_url( '/inc/js/jquery.timepicker.min.js' , dirname(__FILE__) ) );
		
		wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', true);
		wp_enqueue_style( 'jquery-timepicker-style', plugins_url( '/inc/js/jquery.timepicker.min.css' , dirname(__FILE__), true) );

		wp_enqueue_script('ppse_script', plugin_dir_url(__FILE__) . '/js/events.js', array('jquery'), '4.2' );

		$ppse_date_format = 'DD, MM d, yy';
		$date_format = get_option( 'date_format' );

		if ( 'd/m/Y' == $date_format ) {
			$ppse_date_format = 'dd-mm-yy';	//'dd-mm-yy',  // for europe
		}
		//wp_localize_script( 'ppse_script', 'ppseScriptVars', array( 'dateformat' => $ppse_date_format ) );
		wp_localize_script( 'ppse_script', 'ppseScriptVars', array( 'dateformat' => $ppse_date_format ) );


	}
}
add_action('wp_enqueue_scripts', 'pp_events_profile_enqueue');


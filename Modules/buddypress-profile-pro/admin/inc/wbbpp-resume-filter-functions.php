<?php
/**
 *
 * This file is used for defining filter functions of the plugin.
 *
 * @package Buddypress_Profile_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resume field types array
 */
function bprm_resume_field_types() {
	$field_types = array(
		'textbox'            => 'Textbox',
		'textarea'           => 'Textarea',
		'dropdown'           => 'Select',
		'year_dropdown'      => 'Year Dropdown',
		'text_dropdown'      => 'Textbox & Dropdown Combination',
		'place_autocomplete' => 'Google Place Autocomplete',
		'calender_field'     => 'Calender',
		'selectize'          => 'Selectize',
		'checkbox'           => 'Checkbox',
		'radio_button'       => 'Radio Button',
		'email'              => 'Email',
		'phone_number'       => 'Phone Number',
		'url'                => 'URL',
		'image'              => 'Image',
	);

	return apply_filters( 'wbbpp_add_extra_field_types', $field_types );
}

/**
 * Resume data default display area.
 */
function bprm_groups_display_area() {
	$group_area = array(
		'bprm_content' => 'Content Area',
		'bprm_sidebar' => 'Sidebar',
	);
	return apply_filters( 'wbbpp_add_extra_grp_area', $group_area );
}

/**
 * Resume default groups array
 */
function bprm_existing_resume_groups() {

	$grp_args = array(
		'bprm_grp_edu'         => array(
			'g_name'          => __( 'Education', 'buddypress-profile-pro' ),
			'g_desc'          => __( 'This group contains fields which will appear in Education section.', 'buddypress-profile-pro' ),
			'g_key'           => 'bprm_grp_edu',
			'g_area'          => 'bprm_content',
			'profile_display' => 'yes',
			'resume_display'  => 'no',
			'repeater'        => 'yes',
		),
		'bprm_grp_prof_exprnc' => array(
			'g_name'          => __( 'Professional Experience', 'buddypress-profile-pro' ),
			'g_desc'          => __( 'This group contains fields which will appear in Professional Experience section.', 'buddypress-profile-pro' ),
			'g_key'           => 'bprm_grp_prof_exprnc',
			'g_area'          => 'bprm_content',
			'profile_display' => 'yes',
			'resume_display'  => 'no',
			'repeater'        => 'yes',
		),
		'bprm_contact_details' => array(
			'g_name'          => __( 'Contact Details', 'buddypress-profile-pro' ),
			'g_desc'          => __( 'This group contains fields which will appear in Personal Information section.', 'buddypress-profile-pro' ),
			'g_key'           => 'bprm_contact_details',
			'g_area'          => 'bprm_sidebar',
			'profile_display' => 'yes',
			'resume_display'  => 'no',
			'repeater'        => 'no',
		),
	);

	return apply_filters( 'wb_wbbpp_profile_groups', $grp_args );
}

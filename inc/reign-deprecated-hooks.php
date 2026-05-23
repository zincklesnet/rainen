<?php
/** 
 *
 * Add all reign theme deprecated hooks.
 * 
 *
 * @package Reign
 */
 
/*
 * deprecated "wbcom_before_content_section" action
 */
add_action( 'reign_before_content_section', '_deprecated_wbcom_before_content_section' );
function _deprecated_wbcom_before_content_section() {
	do_action( 'wbcom_before_content_section' );
}

/*
 * deprecated "wbcom_after_content_section" action
 */
add_action( 'reign_after_content_section', '_deprecated_wbcom_after_content_section' );
function _deprecated_wbcom_after_content_section() {
	do_action( 'wbcom_after_content_section' );
}


/*
 * deprecated "wbcom_content_bottom" action
 */
add_action( 'reign_content_bottom', '_deprecated_wbcom_content_bottom' );
function _deprecated_wbcom_content_bottom() {
	do_action( 'wbcom_content_bottom' );
}

/*
 * deprecated "wbcom_after_content" action
 */
add_action( 'reign_after_content', '_deprecated_wbcom_after_content' );
function _deprecated_wbcom_after_content() {
	do_action( 'wbcom_after_content' );
}

/*
 * deprecated "wbcom_before_footer" action
 */
add_action( 'reign_before_footer', '_deprecated_wbcom_before_footer' );
function _deprecated_wbcom_before_footer() {
	do_action( 'wbcom_before_footer' );
}

/*
 * deprecated "wbcom_footer" action
 */
add_action( 'reign_footer', '_deprecated_wbcom_footer' );
function _deprecated_wbcom_footer() {
	do_action( 'wbcom_footer' );
}

/*
 * deprecated "wbcom_after_footer" action
 */
add_action( 'reign_after_footer', '_deprecated_wbcom_after_footer' );
function _deprecated_wbcom_after_footer() {
	do_action( 'wbcom_after_footer' );
}

/*
 * deprecated "wbcom_after_page" action
 */
add_action( 'reign_after_page', '_deprecated_wbcom_after_page' );
function _deprecated_wbcom_after_page() {
	do_action( 'wbcom_after_page' );
}

/*
 * deprecated "wbcom_before_page" action
 */
add_action( 'reign_before_page', '_deprecated_wbcom_before_page' );
function _deprecated_wbcom_before_page() {
	do_action( 'wbcom_before_page' );
}

/*
 * deprecated "wbcom_before_masthead" action
 */
add_action( 'reign_before_masthead', '_deprecated_wbcom_before_masthead' );
function _deprecated_wbcom_before_masthead() {
	do_action( 'wbcom_before_masthead' );
}

/*
 * deprecated "wbcom_begin_masthead" action
 */
add_action( 'reign_begin_masthead', '_deprecated_wbcom_begin_masthead' );
function _deprecated_wbcom_begin_masthead() {
	do_action( 'wbcom_begin_masthead' );
}

/*
 * deprecated "wbcom_masthead" action
 */
add_action( 'reign_masthead', '_deprecated_wbcom_masthead' );
function _deprecated_wbcom_masthead() {
	do_action( 'wbcom_masthead' );
}

/*
 * deprecated "wbcom_end_masthead" action
 */
add_action( 'reign_end_masthead', '_deprecated_wbcom_end_masthead' );
function _deprecated_wbcom_end_masthead() {
	do_action( 'wbcom_end_masthead' );
}


/*
 * deprecated "wbcom_after_masthead" action
 */
add_action( 'reign_after_masthead', '_deprecated_wbcom_after_masthead' );
function _deprecated_wbcom_after_masthead() {
	do_action( 'wbcom_after_masthead' );
}

/*
 * deprecated "wbcom_before_content" action
 */
add_action( 'reign_before_content', '_deprecated_wbcom_before_content' );
function _deprecated_wbcom_before_content() {
	do_action( 'wbcom_before_content' );
}

/*
 * deprecated "wbcom_content_top" action
 */
add_action( 'reign_content_top', '_deprecated_wbcom_content_top' );
function _deprecated_wbcom_content_top() {
	do_action( 'wbcom_content_top' );
}

/*
 * deprecated "wbcom_geodirectory_addon_loaded" action
 */
add_action( 'reign_geodirectory_addon_loaded', '_deprecated_wbcom_geodirectory_addon_loaded' );
function _deprecated_wbcom_geodirectory_addon_loaded() {
	do_action( 'wbcom_geodirectory_addon_loaded' );
}

/*
 * deprecated "wbcom_kirki_theme_customizer_loaded" action
 */
add_action( 'reign_kirki_theme_customizer_loaded_deprecated', '_deprecated_wbcom_kirki_theme_customizer_loaded' );
function _deprecated_wbcom_kirki_theme_customizer_loaded() {
	do_action( 'wbcom_kirki_theme_customizer_loaded' );
}

/*
 * deprecated "wbcom_begin_activity_left_sidebar" action
 */
add_action( 'reign_begin_activity_left_sidebar', '_deprecated_wbcom_begin_activity_left_sidebar' );
function _deprecated_wbcom_begin_activity_left_sidebar() {
	do_action( 'wbcom_begin_activity_left_sidebar' );
}

/*
 * deprecated "wbcom_end_activity_left_sidebar" action
 */
add_action( 'reign_end_activity_left_sidebar', '_deprecated_wbcom_end_activity_left_sidebar' );
function _deprecated_wbcom_end_activity_left_sidebar() {
	do_action( 'wbcom_end_activity_left_sidebar' );
}

/*
 * deprecated "wbcom_begin_group_index_sidebar" action
 */
add_action( 'reign_begin_group_index_sidebar', '_deprecated_wbcom_begin_group_index_sidebar' );
function _deprecated_wbcom_begin_group_index_sidebar() {
	do_action( 'wbcom_begin_group_index_sidebar' );
}

/*
 * deprecated "wbcom_end_group_index_sidebar" action
 */
add_action( 'reign_end_group_index_sidebar', '_deprecated_wbcom_end_group_index_sidebar' );
function _deprecated_wbcom_end_group_index_sidebar() {
	do_action( 'wbcom_end_group_index_sidebar' );
}

/*
 * deprecated "wbcom_begin_member_index_sidebar" action
 */
add_action( 'reign_begin_member_index_sidebar', '_deprecated_wbcom_begin_member_index_sidebar' );
function _deprecated_wbcom_begin_member_index_sidebar() {
	do_action( 'wbcom_begin_member_index_sidebar' );
}

/*
 * deprecated "wbcom_end_member_index_sidebar" action
 */
add_action( 'reign_end_member_index_sidebar', '_deprecated_wbcom_end_member_index_sidebar' );
function _deprecated_wbcom_end_member_index_sidebar() {
	do_action( 'wbcom_end_member_index_sidebar' );
}

/*
 * deprecated "wbcom_begin_activity_index_sidebar" action
 */
add_action( 'reign_begin_activity_index_sidebar', '_deprecated_wbcom_begin_activity_index_sidebar' );
function _deprecated_wbcom_begin_activity_index_sidebar() {
	do_action( 'wbcom_begin_activity_index_sidebar' );
}

/*
 * deprecated "wbcom_end_activity_index_sidebar" action
 */
add_action( 'reign_end_activity_index_sidebar', '_deprecated_wbcom_end_activity_index_sidebar' );
function _deprecated_wbcom_end_activity_index_sidebar() {
	do_action( 'wbcom_end_activity_index_sidebar' );
}


/*
 * deprecated "wbcom_begin_group_single_sidebar" action
 */
add_action( 'reign_begin_group_single_sidebar', '_deprecated_wbcom_begin_group_single_sidebar' );
function _deprecated_wbcom_begin_group_single_sidebar() {
	do_action( 'wbcom_begin_group_single_sidebar' );
}

/*
 * deprecated "wbcom_end_group_single_sidebar" action
 */
add_action( 'reign_end_group_single_sidebar', '_deprecated_wbcom_end_group_single_sidebar' );
function _deprecated_wbcom_end_group_single_sidebar() {
	do_action( 'wbcom_end_group_single_sidebar' );
}

/*
 * deprecated "wbcom_begin_member_profile_sidebar" action
 */
add_action( 'reign_begin_member_profile_sidebar', '_deprecated_wbcom_begin_member_profile_sidebar' );
function _deprecated_wbcom_begin_member_profile_sidebar() {
	do_action( 'wbcom_begin_member_profile_sidebar' );
}

/*
 * deprecated "wbcom_end_member_profile_sidebar" action
 */
add_action( 'reign_end_member_profile_sidebar', '_deprecated_wbcom_end_member_profile_sidebar' );
function _deprecated_wbcom_end_member_profile_sidebar() {
	do_action( 'wbcom_end_member_profile_sidebar' );
}

/*
 * deprecated "wbcom_begin_group_left_sidebar" action
 */
add_action( 'reign_begin_group_left_sidebar', '_deprecated_wbcom_begin_group_left_sidebar' );
function _deprecated_wbcom_begin_group_left_sidebar() {
	do_action( 'wbcom_begin_group_left_sidebar' );
}

/*
 * deprecated "wbcom_end_group_left_sidebar" action
 */
add_action( 'wbcom_end_group_left_sidebar', '_deprecated_wbcom_end_group_left_sidebar' );
function _deprecated_wbcom_end_group_left_sidebar() {
	do_action( 'wbcom_end_group_left_sidebar' );
}

/*
 * deprecated "wbtm_member_extra_info_section" action
 */
add_action( 'reign_member_extra_info_section', '_deprecated_wbtm_member_extra_info_section' );
function _deprecated_wbtm_member_extra_info_section() {
	do_action( 'wbtm_member_extra_info_section' );
}

/*
 * deprecated "wbtm_before_group_avatar_group_directory" action
 */
add_action( 'reign_before_group_avatar_group_directory', '_deprecated_wbtm_before_group_avatar_group_directory' );
function _deprecated_wbtm_before_group_avatar_group_directory() {
	do_action( 'wbtm_before_group_avatar_group_directory' );
}

/*
 * deprecated "wbtm_bp_directory_groups_data" action
 */
add_action( 'reign_bp_directory_groups_data', '_deprecated_wbtm_bp_directory_groups_data' );
function _deprecated_wbtm_bp_directory_groups_data() {
	do_action( 'wbtm_bp_directory_groups_data' ); 
}


/*
 * deprecated "wbtm_group_extra_info_section" action
 */
add_action( 'reign_group_extra_info_section', '_deprecated_wbtm_group_extra_info_section' );
function _deprecated_wbtm_group_extra_info_section() {
	do_action( 'wbtm_group_extra_info_section' );
}

/*
 * deprecated "wbtm_before_member_avatar_member_directory" action
 */
add_action( 'reign_before_member_avatar_member_directory', '_deprecated_wbtm_before_member_avatar_member_directory' );
function _deprecated_wbtm_before_member_avatar_member_directory() {
	do_action( 'wbtm_before_member_avatar_member_directory' );
}


/*
 * deprecated "wbtm_bp_nouveau_directory_members_item" action
 */
add_action( 'reign_bp_nouveau_directory_members_item', '_deprecated_wbtm_bp_nouveau_directory_members_item' );
function _deprecated_wbtm_bp_nouveau_directory_members_item() {
	do_action( 'wbtm_bp_nouveau_directory_members_item' );
}

/*
 * deprecated "wbtm_bp_before_displayed_user_mentionname" action
 */
add_action( 'reign_bp_before_displayed_user_mentionname' , '_deprecated_wbtm_bp_before_displayed_user_mentionname' );
function _deprecated_wbtm_bp_before_displayed_user_mentionname() {
	do_action( 'wbtm_bp_before_displayed_user_mentionname' );
}

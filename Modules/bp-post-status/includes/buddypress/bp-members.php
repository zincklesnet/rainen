<?php

if(!defined('ABSPATH')) {
	exit;
}

// @package bp-post-status

// Since 1.7.0


// BuddyPress Members Function for BP Post Status

/**
 * Set up action hooks for setup nav.
 *
 * @since 1.7.0
 *
 * 
 */

add_action( 'bp_setup_nav', 'bpps_add_my_posts_tab', 50 );
add_action( 'bp_setup_admin_bar', 'bpps_admin_bar_add', 50 );


/**
 * Adds My Posts tab to the BuddyPress member profile.
 *
 * @since 1.7.0
 *
 * @return bool
 */


function bpps_add_my_posts_tab() {
	
	if ( ! bp_is_user_profile() && ! bp_is_user_activity() && ! bp_is_user() ) {
		return;
	}
	
	if ( class_exists( 'BP_Site_Post' ) ) {
		$site_post = true;
		$logged_in = is_user_logged_in();
		$bpsp_options = get_option('bpsp_site_post_settings');
		if ( isset( $bpsp_options['bpsp-allow-all-members-posts'] ) && $bpsp_options['bpsp-allow-all-members-posts'] != '' ) $members_allowed = true;
		if ( isset( $members_allowed ) && $logged_in ) $allowed_to_post = true;
	}
	
	global $bp;
	$has_posts = true;
	$is_own_view = false;
	$user_id = bp_displayed_user_id();
	$count = bpps_count_users_posts( $user_id );
	$mod_count = 0;
	
	if ( get_current_user_id() == $user_id ) $is_own_view = true;
	
	if ( current_user_can( 'edit_others_posts' ) && isset( $is_own_view ) ) {
		$mod_count = bpps_count_moderation_posts();
	}
	
	if ( ( current_user_can( 'edit_posts' ) || isset( $allowed_to_post ) ) && $is_own_view ) {
		$pending_count = bpps_count_pending_posts( $user_id );
	} else {
		$pending_count = 0;
	}
	
	if ( $is_own_view ) {
		$total = $count + $mod_count + $pending_count;
	} else {
		$total = $count;
	}
	
	if ( $total < 1 ) $has_posts = false;

	if ( $has_posts ) {
		bp_core_new_nav_item( array(
			'name'                  => esc_attr__( 'My Posts ', 'bp-post-status' ) . '<span class="count">' . $total . '</span>',
			'slug'                  => BPPS_MY_POSTS_NAV_SLUG,
			'parent_url'            => $bp->displayed_user->domain,
			'parent_slug'           => $bp->profile->slug,
			'screen_function'       => 'bpps_my_posts_screen',			
			'position'              => 10,
			'default_subnav_slug'   => BPPS_MY_POSTS_NAV_SLUG
		) );
	}

	if ( ( current_user_can( 'edit_posts' ) || isset( $allowed_to_post ) ) & $pending_count > 0 && $is_own_view) {
		bp_core_new_subnav_item( array(
			'name'                  => esc_attr__( 'Pending Posts ', 'bp-post-status' ) . '<span class="count">' . $pending_count . '</span>',
			'slug'                  => BPPS_PROFILE_PENDING_POSTS_NAV_SLUG,
			'parent_url'            => $bp->displayed_user->domain . 'my-posts/',
			'parent_slug'           => BPPS_MY_POSTS_NAV_SLUG,
			'screen_function'       => 'bpps_my_posts_pending_screen',			
			'position'              => 10,
			'default_subnav_slug'   => BPPS_PROFILE_PENDING_POSTS_NAV_SLUG
		) );
	}

	if ( current_user_can( 'edit_others_posts' ) & $mod_count > 0 && $is_own_view ) {
		bp_core_new_subnav_item( array(
			'name'                  => esc_attr__( 'Moderation ', 'bp-post-status' ) . '<span class="count">' . $mod_count . '</span>',
			'slug'                  => BPPS_PROFILE_MODERATION_POSTS_NAV_SLUG,
			'parent_url'            => $bp->displayed_user->domain . 'my-posts/',
			'parent_slug'           => BPPS_MY_POSTS_NAV_SLUG,
			'screen_function'       => 'bpps_my_posts_moderation_screen',			
			'position'              => 10,
			'default_subnav_slug'   => BPPS_PROFILE_MODERATION_POSTS_NAV_SLUG
		) );
	}

}


/**
 * Adds Moderation screen actions.
 *
 * @since 1.7.4
 *
 * @return actions
 */
function bpps_my_posts_moderation_screen() {

	add_action( 'bp_template_title', 'bpps_my_posts_moderation_screen_title' );
	add_action( 'bp_template_content', 'bpps_my_posts_moderation_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bpps_my_posts_moderation_screen_title() { 
	echo '<h3>' . esc_attr__( 'Moderation', 'bp-post-status' ) . '</h3>';
}

function bpps_my_posts_moderation_screen_content() { 
	bpps_load_template( 'my-posts-moderation.php' );
}

/**
 * Adds Pending Posts screen actions.
 *
 * @since 1.7.4
 *
 * @return actions
 */
function bpps_my_posts_pending_screen() {

	add_action( 'bp_template_title', 'bpps_my_posts_pending_screen_title' );
	add_action( 'bp_template_content', 'bpps_my_posts_pending_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bpps_my_posts_pending_screen_title() { 
	echo '<h3>' . esc_attr__( 'Pending Posts', 'bp-post-status' ) . '</h3>';
}

function bpps_my_posts_pending_screen_content() { 
	bpps_load_template( 'my-posts-pending.php' );
}

/**
 * Adds My Posts screen actions.
 *
 * @since 1.7.0
 *
 * @return actions
 */
function bpps_my_posts_screen() {

	add_action( 'bp_template_title', 'bpps_my_posts_screen_title' );
	add_action( 'bp_template_content', 'bpps_my_posts_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bpps_my_posts_screen_title() { 
	echo '<h3>' . esc_attr__( 'My Posts', 'bp-post-status' ) . '</h3>';
}

function bpps_my_posts_screen_content() { 
	bpps_load_template( 'my-posts.php' );
}



/**
 * Adds links to the BuddyPress member menu for our administrators.
 *
 * @since 1.7.0
 * @revised 1.7.4
 * @return bool
 */
function bpps_admin_bar_add() {
	
	global $wp_admin_bar, $bp;

	if (    defined( 'DOING_AJAX' ) ) {
		return false;
	}

	if ( class_exists( 'BP_Site_Post' ) ) {
		$site_post = true;
		$logged_in = is_user_logged_in();
		$bpsp_options = get_option('bpsp_site_post_settings');
		if ( isset( $bpsp_options['bpsp-allow-all-members-posts'] ) && $bpsp_options['bpsp-allow-all-members-posts'] != '' ) $members_allowed = true;
		if ( isset( $members_allowed ) && $logged_in ) $allowed_to_post = true;
	}

	$user_id = get_current_user_id();
	
	if ( ! $user_id || $user_id == 0 || ! is_numeric( $user_id ) ) {
		return;
	}
	
	$count = bpps_count_users_posts( $user_id );
	$has_posts = true;
	$my_posts_page = bp_members_get_user_url( $user_id ) . '/' . BPPS_MY_POSTS_NAV_SLUG;
	
	$mod_count = 0;
	if ( current_user_can( 'edit_others_posts' ) ) {
		$mod_count = bpps_count_moderation_posts();
		$moderation_posts_page = bp_members_get_user_url( $user_id ) . '/' . BPPS_MY_POSTS_NAV_SLUG . '/' . BPPS_PROFILE_MODERATION_POSTS_NAV_SLUG;
	}
	
	if ( current_user_can( 'edit_posts' ) || isset( $allowed_to_post ) ) {
		$pending_count = bpps_count_pending_posts( $user_id );
		$pending_posts_page = bp_members_get_user_url( $user_id ) . '/' . BPPS_MY_POSTS_NAV_SLUG . '/' . BPPS_PROFILE_PENDING_POSTS_NAV_SLUG;
	} else {
		$pending_count = 0;
	}
	
	if ( function_exists( 'bpsp_get_add_post_url' ) ) {
			$add_post_page = bpsp_get_add_post_url();
	}
	
	$total = $count + $mod_count + $pending_count;
	
	if ( $total < 1 ) $has_posts = false;
		
	if ( $has_posts ) {
		$wp_admin_bar->add_menu( array(
			'parent' => $bp->my_account_menu_id,
			'id'     => 'bp-post-status',
			'title'  => esc_attr__( 'My Posts', 'bp-post-status' ) . '<span class="count">' . $total . '</span>',
			'meta' => array( 'class' => 'menupop' ),
			'href'   => $my_posts_page,
		) );
	} else if ( ( current_user_can( 'edit_posts' ) || isset( $allowed_to_post ) ) && function_exists( 'bpsp_count_pending_posts' ) ) {
		$wp_admin_bar->add_menu( array(
			'parent' => $bp->my_account_menu_id,
			'id'     => 'bp-post-status',
			'title'  => esc_attr__( 'Add Post', 'bp-post-status' ),
			'meta' => array( 'class' => 'menupop' ),
			'href'   => $add_post_page,
		) );		
	}
	// Submenus.
	if ( current_user_can( 'edit_others_posts' ) && $mod_count > 0 ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'bp-post-status',
			'id'     => 'bp-post-status-my-posts-moderation',
			'title'  => esc_attr__( 'Moderation', 'bp-post-status' ) . '<span class="count">' . $mod_count . '</span>',
			'href'   => $moderation_posts_page,
		) );
	}
	// Submenus.
	if ( $pending_count > 0 ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'bp-post-status',
			'id'     => 'bp-post-status-my-posts-pending',
			'title'  => esc_attr__( 'Pending Posts', 'bp-post-status' ) . '<span class="count">' . $pending_count . '</span>',
			'href'   => $pending_posts_page,
		) );
	}
	if ( $count > 0 ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'bp-post-status',
			'id'     => 'bp-post-status-my-posts',
			'title'  => esc_attr__( 'My Posts', 'bp-post-status' ) . '<span class="count">' . $count . '</span>',
			'href'   => $my_posts_page,
		) );
	}
	if ( function_exists( 'bpsp_count_pending_posts' ) ) {
		$wp_admin_bar->add_menu( array(
			'parent' => 'bp-post-status',
			'id'     => 'bp-post-status-add-new',
			'title'  => esc_attr__( 'Add Post', 'bp-post-status' ),
			'href'   => $add_post_page,
		) );		
	}
	return true;
}


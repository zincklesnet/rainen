<?php
/*
* @package bp-post-status
*/

if(!defined('ABSPATH')) {
	exit;
}

// from 1.2.0
//AJAX set Homepage
function bpps_home_page() {
	
	check_ajax_referer( 'bpps-nonce', 'security' );
	global $bp;
	
	$post_id = esc_attr($_POST['post']);
	$group_id = $bp->groups->current_group->id;
	$post_status = get_post_status( $post_id );
	
	if ( $post_status == 'group_post' ) {
		
		$post_exists = 1;
		
	}
	
	if ( isset($post_id ) && isset( $group_id) && $post_exists == 1 ) {
		
		groups_update_groupmeta( $group_id, 'bpps_home_post_id', $post_id );
		echo 'Success';
	
	} else {
		
		echo 'Failed';
	}

	die();

}

add_action( 'wp_ajax_bpps_home_page', 'bpps_home_page');

//@since 1.7.1
// Delete authors posts
function bpps_delete_post() {
	
	check_ajax_referer( 'bpps-nonce', 'security' );

	$post_id = esc_attr( $_POST['postId'] );
	$user_id = get_current_user_id();
	$post = get_post( $post_id );
	$group_post_and_admin = false;
	
	if ( $post->post_status == 'group_post' ) {
		$group_id = get_post_meta( $post_id, 'bpgps_group' );
		if ( $group_id ) {
			$group_post_and_admin = groups_is_user_admin( $user_id, $group_id[0] );
		}
	}
	
	if ( $user_id == $post->post_author || current_user_can( 'manage_options' ) || $group_post_and_admin ) {
		wp_delete_post( $post_id );
		echo 1;
		die();
	} else {
		echo 0;
		die();
	}
	
}


add_action( 'wp_ajax_bpps_delete_post', 'bpps_delete_post' );


// From 1.7.7
// Publish authors posts
function bpps_publish_post() {
	
	check_ajax_referer( 'bpps-nonce', 'security' );

	$post_id = esc_attr( $_POST['postId'] );
	$user_id = get_current_user_id();
	$post = get_post( $post_id );
	$group_post_and_admin = false;
	
	if ( $post->post_status == 'group_post_pending' ) {
		$group_id = get_post_meta( $post_id, 'bpgps_group' );
		if ( $group_id ) {
			$group_post_and_admin = groups_is_user_admin( $user_id, $group_id[0] );
		}
	}
	
	if ( $post->post_status == 'pending' || $post->post_status == 'members_only_pending' ) {
		if ( current_user_can( 'edit_others_posts' ) ) {
			$publish_authorized = true;
		}
	}

	$post_update = array( 'ID' => $post->ID );
	
	if ( current_user_can( 'manage_options' ) || isset( $group_post_and_admin ) || isset( $publish_authorized ) ) {
		$post_update['post_date_gmt'] = current_time( 'mysql', 1 );
		$post_update['post_date'] = current_time( 'mysql' );
		$post_update['post_modified'] = current_time( 'mysql' );
		$post_update['post_modified_gmt'] = current_time( 'mysql', 1 );
		
		if ( $post->post_status == 'pending' ) {
			$post_update['post_status'] = 'publish';
			wp_update_post ( $post_update );
		} else if ( $post->post_status == 'members_only_pending' ) {
			$post_update['post_status'] = 'members_only';
			wp_update_post ( $post_update );
		} else if ( $post->post_status == 'group_post_pending' ) {
			$post_update['post_status'] = 'group_post';
			wp_update_post( $post_update );
		}
		echo 1;
		die();
	} else {
		echo 0;
		die();
	}
	
}


add_action( 'wp_ajax_bpps_publish_post', 'bpps_publish_post' );

// from 1.7.1
//AJAX make post sticky
function bpps_make_sticky() {
	
	check_ajax_referer( 'bpps-nonce', 'security' );
	global $bp;
	
	$post_id = esc_attr($_POST['postId']);
	$context = esc_attr($_POST['context']);
	$user_id = get_current_user_id();
	
	if ( $context == 'group-post' ) {
		$group_id = $bp->groups->current_group->id;
		$group_sticky_status = get_post_meta( $post_id, 'bpps_group_post_sticky', true );
		if ( groups_is_user_admin( $user_id, $group_id ) || current_user_can( 'manage_options' ) ) {
			if ( isset( $group_sticky_status ) && $group_sticky_status == 1 ) {
				$update = update_post_meta( $post_id, 'bpps_group_post_sticky', 0 );
			} else {
				$update = update_post_meta( $post_id, 'bpps_group_post_sticky', 1 );
			}
			echo 'Success';
			die();
		}
	} else if ( $context == 'my-posts' ) {
		$post = get_post( $post_id );
		$my_posts_sticky_status = get_post_meta( $post_id, 'bpps_my_posts_sticky', true );
		if ( $post->post_author == $user_id || current_user_can( 'manage_options' ) ) {
			if ( isset( $my_posts_sticky_status ) && $my_posts_sticky_status == 1 ) {
				$update = update_post_meta( $post_id, 'bpps_my_posts_sticky', 0 );
			} else {
				$update = update_post_meta( $post_id, 'bpps_my_posts_sticky', 1 );
			}
			echo 'Success';
			die();
		}
	}
	
	echo 'Failed';
	die();

}

add_action( 'wp_ajax_bpps_make_sticky', 'bpps_make_sticky');

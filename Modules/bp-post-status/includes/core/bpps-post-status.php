<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// @package bp-post-status


if (!class_exists('BPPS_post_status_plugin')) {

// Todo: Need to find a non intrusive method for adding the wp_query
// Todo: Add appropriate activity updates/notifications

class BPPS_post_status_plugin {


	var $groupstatusname = 'group_post';
	var $groupstatuslabel;
	var $membersstatusname = 'members_only';
	var $membersstatuslabel;
	var $grouppendingstatusname = 'group_post_pending';
	var $grouppendingstatuslabel;
	var $memberspendingstatusname = 'members_only_pending';
	var $memberspendingstatuslabel;
	var $friendsstatusname = 'friends_only';
	var $friendsstatuslabel;
	var $followingstatusname = 'following';
	var $followingstatuslabel;
	var $followedstatusname = 'followed';
	var $followedstatuslabel;


	public function __construct() {
		
		$this->groupstatuslabel = sanitize_text_field(esc_attr__( 'Group Post', 'bp-post-status' ) );
		$this->membersstatuslabel = sanitize_text_field(esc_attr__( 'Members only', 'bp-post-status' ) );
		$this->grouppendingstatuslabel = sanitize_text_field(esc_attr__( 'Group Post pending', 'bp-post-status' ) );
		$this->memberspendingstatuslabel = sanitize_text_field(esc_attr__( 'Members only pending', 'bp-post-status' ) );
		$this->friendsstatuslabel = sanitize_text_field(esc_attr__( 'Friends only', 'bp-post-status' ) );
		$this->followingstatuslabel = sanitize_text_field(esc_attr__( 'Following', 'bp-post-status' ) );
		$this->followedstatuslabel = sanitize_text_field(esc_attr__( 'Followed', 'bp-post-status' ) );
		
		//register the post statuses
		add_action( 'init', array($this, 'register_status' ), 1000 );
		
		//display a label for group and member only posts on the listing screen
		add_filter( 'display_post_states', array($this, 'display_post_state' ) );
		
		//filter to add the roles to the BP Posts Login statuses
		add_filter('bp-post-status_filter', array($this, 'bp-post-status_filter' ), 10, 1 );

		//run whatever on plugins loaded
		add_action( 'plugins_loaded', array($this, 'plugins_loaded' ) );

		// filter out posts for group, members and friends posts
		add_action( 'pre_get_posts', array($this, 'current_user_can_view_post' ), 999 );

		// filter out posts for followed and following posts
		add_action( 'pre_get_posts', array($this, 'current_user_can_view_follow' ) );
		
		// filter the permalink for group posts
		add_filter( 'the_permalink', array( $this, 'group_posts_link' ), 10, 2 );
	}

	public function current_user_can_view_follow($query) {
		
		if ( current_user_can( 'edit_others_posts' ) ) {
			
			return $query;
			
		}
		$logged_in = is_user_logged_in();
		
		if ( $logged_in ) {
			
			$member_id = get_current_user_id();
			
		} else {
			
			$member_id = 0;
			
		}

		if ( class_exists( 'BP_Follow_Component' ) ) {

			$following_excluded_posts = $this->following_excludes( $member_id );
			$followed_excluded_posts = $this->followed_excludes( $member_id );

		}
		
		
		// Combine both lists as appropriate
		
		if ( empty( $following_excluded_posts ) && empty( $followed_excluded_posts ) ) {
			
			return $query;
			
		} else if ( empty( $following_excluded_posts ) and count( $followed_excluded_posts ) >= 1 ) {
			
			$excluded_posts = $followed_excluded_posts;
		
		} else if ( count( $following_excluded_posts ) >= 1 and empty( $followed_excluded_posts ) ) {
			
			$excluded_posts = $following_excluded_posts;
		
		} else if ( count( $following_excluded_posts ) >= 1 && count( $followed_excluded_posts ) >= 1 ) {
			
			$excluded_posts = array_merge( $followed_excluded_posts, $following_excluded_posts );
		
		}
		
		// Save the current post__not_in setting so we can add it back later
		$current_query = $query->query_vars['post__not_in'];
		
		// Combine the list of excluded posts with the original post__not_in query.
		if ( ! empty( $current_query ) ) {
			
			$excluded_posts = array_merge( $excluded_posts, $current_query );
		
		}
		
		// Set the new post_not_in query into the WP_Query vars 
		if ( ! empty( $excluded_posts ) ) {
		
			$query->set( 'post__not_in', $excluded_posts );
		
		}
		
		return $query;
	
	}

	/*
	* Get the exluded post_ids for following posts
	* @since 1.2.0
	* @revised 1.8.0 Added heck for post author
	* @return array excluded posts list
	*/
	public function following_excludes( $member_id ) {
		
		global $wpdb;
		$logged_in = is_user_logged_in();
		$user_id = get_current_user_id();
		
		// get all posts with friends only status, and the author name.
		$following_query = "SELECT ID,post_author from $wpdb->posts WHERE post_status = 'following'";
		$following_ids = $wpdb->get_results($following_query, ARRAY_A);
		$follwing_data = array();
		
		// Restructure data into key=>value pairs
		foreach($following_ids as $item ) {
			
			$follwing_data[$item['ID']] = $item['post_author'];
		
		}
		

		// Get user's friends ids
		if ( $logged_in ) {
			
			$member_id = get_current_user_id();
			$users_following = bp_follow_get_following( $member_id );
			$users_following[] = $member_id;
		
		}
		
		$excluded = array();
		
		// Match friends with the author ids of friends only posts and pull out the excluded post list.
		foreach ( $follwing_data as $post_id => $user_id) {

			if ( $logged_in ) {
				
				$post = get_post( $post_id );
				$author = $post->post_author;
				if ( !in_array( $user_id, $users_following )  && $author != $user_id) {
				
					$excluded[] = $post_id;
			
				}
				
			} else {
				
				$excluded[] = $post_id;
				
			}
			
		}
	
		return; $excluded;
	
	}
	
	/*
	* Get the exluded post_ids for followed posts
	* @since 1.2.0
	* @revised 1.8.0 Added heck for post author
	* @return array excluded posts list
	*/

	public function followed_excludes() {
		
		global $wpdb;
		$logged_in = is_user_logged_in();
		$user_id = get_current_user_id();
		
		// get all posts with friends only status, and the author name.
		$followed_query = "SELECT ID,post_author from $wpdb->posts WHERE post_status = 'followed'";
		$followed_ids = $wpdb->get_results($followed_query, ARRAY_A);
		$follwed_data = array();
		
		// Restructure data into key=>value pairs
		foreach($followed_ids as $item ) {
			
			$follwed_data[$item['ID']] = $item['post_author'];
		
		}
		

		// Get user's followers ids
		if ( $logged_in ) {
			
			$member_id = get_current_user_id();
			$users_followed = bp_follow_get_followers( $member_id );
			$users_followed[] = $member_id;
		
		}
		
		$excluded = array();
		
		// Match followers with the author ids of friends only posts and pull out the excluded post list.
		foreach ( $follwed_data as $post_id => $user_id) {

			if ( $logged_in ) {
				$post = get_post( $post_id );
				$author = $post->post_author;
				
				if ( !in_array( $user_id, $users_followed ) && $author != $user_id ) {
				
					$excluded[] = $post_id;
			
				}
				
			} else {
				
				$excluded[] = $post_id;
				
			}
			
		}
	
		return; $excluded;
		
	}

	public function current_user_can_view_post($query) {
		
		$args = array(
			'public' => true,
		);

		$post_types = get_post_types( $args, 'objects' );
		$public_post_type = false;
		
		foreach ( $post_types as $type ) {
			if ( is_array( $query->post_type )) {
				if ( in_array( strtolower( $type->labels->singular_name ), $query->post_type )) {
					$public_post_type = true;
				}
			} else if ( is_string( $query->post_type )) {
				if ( strtolower( $type->labels->singular_name ) ==  $query->post_type ) {
					$public_post_type = true;
				}
			}
		}
		
		if ( current_user_can( 'edit_others_posts' ) || !$public_post_type ) {
			
			return $query;
			
		}
		$logged_in = is_user_logged_in();
		
		if ( $logged_in ) {
			
			$member_id = get_current_user_id();
			
		} else {
			
			$member_id = 0;
			
		}

		if ( bp_is_active( 'friends' ) ) {

			$friends_excluded_posts = $this->friends_excludes( $member_id );

		}
		
		if ( bp_is_active( 'groups' ) ) {
			
			$groups_excluded_posts = $this->groups_excludes( $member_id );
		
		}
		
		// Combine both lists as appropriate
		
		if ( empty( $friends_excluded_posts ) && empty( $groups_excluded_posts ) ) {
			
			return $query;
			
		} else if ( empty( $friends_excluded_posts ) and count( $groups_excluded_posts ) >= 1 ) {
			
			$excluded_posts = $groups_excluded_posts;
		
		} else if ( count( $friends_excluded_posts ) >= 1 and empty( $groups_excluded_posts ) ) {
			
			$excluded_posts = $friends_excluded_posts;
		
		} else if ( count( $friends_excluded_posts ) >= 1 && count( $groups_excluded_posts ) >= 1 ) {
			
			$excluded_posts = array_merge( $groups_excluded_posts, $friends_excluded_posts );
		
		}
		//$excluded_posts = $groups_excluded_posts;
		
		// Update $wp_query with the excluded posts list.
		$current_query = $query->query_vars['post__not_in'];
		
		if ( ! empty( $current_query ) ) {
			
			$excluded_posts = array_merge( $excluded_posts, $current_query );
		
		}
		
		if ( ! empty( $excluded_posts ) ) {
		
			$query->set('post__not_in', $excluded_posts );
		
		}
	
	}
	
	/*
	* Get the exluded post_ids for friends only posts
	* @since 1.1.0
	* @revised 1.8.0 Added heck for post author
	* @return array excluded posts list
	*/
	
	public function friends_excludes( $member_id ) {
		
		global $wpdb;
		$logged_in = is_user_logged_in();
		$user_id = get_current_user_id();
		
		// get all posts with friends only status, and the author name.
		$friends_query = "SELECT ID,post_author from $wpdb->posts WHERE post_status = 'friends_only'";
		$friends_only_ids = $wpdb->get_results($friends_query, ARRAY_A);
		$friends_data = array();
		
		// Restructure data into key=>value pairs
		foreach($friends_only_ids as $item ) {
			
			$friends_data[$item['ID']] = $item['post_author'];
		
		}
		

		// Get user's friends ids
		if ( $logged_in ) {
			
			$member_id = get_current_user_id();
			$users_friends = friends_get_friend_user_ids( $member_id );
			$users_friends[] = $member_id;
		
		}
		
		$excluded = array();
		
		// Match friends with the author ids of friends only posts and pull out the excluded post list.
		foreach ( $friends_data as $post_id => $user_id) {

			if ( $logged_in ) {
				
				$post = get_post( $post_id );
				$author = $post->post_author;
				if ( !in_array( $user_id, $users_friends ) && $author != $user_id ) {
				
					$excluded[] = $post_id;
			
				}
				
			} else {
				
				$excluded[] = $post_id;
				
			}
			
		}
	
		return $excluded;
	
	}
	
	
	/*
	* Get the exluded post_ids for group posts
	* @since 1.1.0
	* @revised 1.8.0 Added heck for post author
	* @return array excluded posts list
	*/
	
	
	public function groups_excludes( $member_id ) {

		global $wpdb;

		// Get IDs for Group only posts
		$group_query = "SELECT ID from $wpdb->posts WHERE post_status = 'group_post'";
		$group_only_ids = $wpdb->get_results($group_query, ARRAY_N);
		$group_data = array();
		$logged_in = is_user_logged_in();
		if ( $logged_in ) { 
			$user_id = get_current_user_id();
		}
		
		$member = false;
		// Get list of users group IDs
		$users_groups = array();
		if ( $logged_in ) {
			
			$users_groups = BP_Groups_Member::get_group_ids( $member_id );
			$users_groups = $users_groups['groups'];
			
		}
		
		//Get ids for all group home pages
		$group_homes = array();
		$all_groups = groups_get_groups( array( 'show_hidden' => true ) );
		$all_groups = $all_groups['groups'];
		
		foreach ( $all_groups as $group ) {
			
			$group_home = groups_get_groupmeta( $group->id, 'bpps_home_post_id' );
			
			if ( isset( $group_home ) ) {
				
				$group_homes[] = $group_home;
			
			}				
		
		}

		// Match group names with post id's
		foreach ( $group_only_ids as $group_post_id ) {
			
			foreach ( $group_post_id as $post_id ) {
				
				
				$post = get_post( $post_id );
				$author = $post->post_author;
				$group_status[$post_id] = get_post_meta( $post_id, 'bpgps_group_post_status', true );
				
				if ( $group_status[$post_id] == 'group_only' ) {
					
					if ( ! in_array( get_post_meta( $post_id, 'bpgps_group', true ), $users_groups ) && $author != $user_id ) {
						$group_data[] = $post_id;
					}
			
				} else if ( $group_status[$post_id] == 'members_only' && ! $logged_in && $author != $user_id) {
					
					$group_data[] = $post_id;
					
				} else if ( in_array( $post_id, $group_homes ) && $author != $user_id) {
					
					$group_data[] = $post_id;
					
				}
				
			}
			
		}
		
		if ( count( $group_data ) < 1 ) {
			
			return array();
			
		}
		
			
	return $group_data;
	
	}
	
	public function group_posts_link( $permalink, $post = array() ) {
		
		if ( !isset( $post ) || ( isset( $post ) && !is_object( $post) ) ) {
			return $permalink;
		} else if ( isset($post) && is_numeric( $post ) ) {
			$post = get_post( $post );
		}
		$settings = get_option( "bpps_groups_settings" );
		if ( isset( $settings['group_context_enable'] ) ) {
			$group_context_enable = $settings['group_context_enable'];
		} else {
			$group_context_enable = false;
		}
		$slug = '';
		if ( $post->post_status == 'group_post_pending' || $post->post_status == 'group_post' ) {
			$group_id = get_post_meta( $post->ID,  'bpgps_group', true );
			return bpps_get_post_permalink($post, $group_id) . $slug;
		} else {
			return $permalink;
		}
		
		
	}

	public function user_has_followers() {
		
		$followers = bp_follow_total_follow_counts();
		
		if ( $followers['followers'] == '0' ) {
			
			return false;
			
		} else {
			
			return true;
			
		}
		
	}
	
	public function user_is_following() {
		
		$following = bp_follow_total_follow_counts();
		
		if ( $following['following'] == '0') {
			
			return false;
			
		} else {
			
			return true;
			
		}
		
		
	}

	// function to look up the group post status from the $post_id
	public static function bpps_get_group_post_status( $post_id ) {
		
		$post_meta = get_post_meta( $post_id );
		
		if ( isset( $post_data['bpgps_group_post_status'] ) ) {
			
			$group_status = $post_meta['bpgps_group_post_status'];
			
			return $group_status;
			
		}
		
	}
	
	// Register the new BP statuses.
	public function register_status() {
		
		$posttypes = get_post_types( array('public'   => true ), 'names' );
		
		// if the user is not logged in, they can't see any of these post types
		if ( is_user_logged_in() ) {
			
			$public = true;
			
		} else {
			
			$public = false;
			
		}
		
		$members_settings = get_option( "bpps_members_settings" );
		$friends_settings = get_option( "bpps_friends_settings" );
		$groups_settings = get_option( "bpps_groups_settings" );
		$following_settings = get_option( "bpps_following_settings" );
		$followed_settings = get_option( "bpps_followed_settings" );
	
		
			// Members Only Status
		if ( ! isset( $members_settings['members_disable'] ) ) {
			register_post_status( $this->membersstatusname, array(
				'label'                     => sanitize_text_field(esc_attr_x( 'Members Only', 'post status label', 'bp-post-status' )),
				'public'                    => $public,
				'label_count'               => _n_noop( 'Members Only <span class="count">(%1s)</span>', 'Members Only <span class="count">(%2s)</span>', 'bp-post-status'  ),
				'post_type'                 => $posttypes, // Define one or more post types the status can be applied to.
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'show_in_metabox_dropdown'  => true,
				'show_in_inline_dropdown'   => true,
				'dashicon'                  => 'dashicons-yes',
			) );
			//since 1.3.0
			register_post_status( $this->memberspendingstatusname, array(
				'label'                     => sanitize_text_field(esc_attr_x( 'Members Only Pending', 'post status label', 'bp-post-status' )),
				'public'                    => false,
				'label_count'               => _n_noop( 'Members Only Pending <span class="count">(%1s)</span>', 'Members Only Pending <span class="count">(%2s)</span>', 'bp-post-status'  ),
				'post_type'                 => $posttypes, // Define one or more post types the status can be applied to.
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'show_in_metabox_dropdown'  => true,
				'show_in_inline_dropdown'   => true,
				'dashicon'                  => 'dashicons-flag',
			) );
		}
		
		// Group Only Status
		if ( class_exists( 'buddypress' ) && bp_is_active( 'groups' ) && ! isset( $groups_settings['groups_disable'] ) ) {
			
			register_post_status( $this->groupstatusname, array(
				'label'                     => sanitize_text_field(esc_attr_x( 'Group Post', 'post status label', 'bp-post-status' )),
				'public'                    => true,
				'label_count'               => _n_noop( 'Group Post <span class="count">(%1s)</span>', 'Group Post <span class="count">(%2s)</span>', 'bp-post-status'  ),
				'post_type'                 => $posttypes, // Define one or more post types the status can be applied to.
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'show_in_metabox_dropdown'  => true,
				'show_in_inline_dropdown'   => true,
				'dashicon'                  => 'dashicons-groups',
			) );
			// Since 1.3.0
			register_post_status( $this->grouppendingstatusname, array(
				'label'                     => sanitize_text_field(esc_attr_x( 'Group Post Pending', 'post status label', 'bp-post-status' )),
				'public'                    => false,
				'label_count'               => _n_noop( 'Group Post Pending <span class="count">(%1s)</span>', 'Group Post Pending <span class="count">(%2s)</span>', 'bp-post-status'  ),
				'post_type'                 => $posttypes, // Define one or more post types the status can be applied to.
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'show_in_metabox_dropdown'  => true,
				'show_in_inline_dropdown'   => true,
				'dashicon'                  => 'dashicons-flag',
			) );

		}
			
		// Friends Only Status
		
		if ( class_exists( 'buddypress' ) && bp_is_Active( 'friends' ) && ! isset( $friends_settings['friends_disable'] ) ) {
		
			register_post_status( $this->friendsstatusname, array(
				'label'                     => sanitize_text_field(esc_attr_x( 'Friends Only', 'post status label', 'bp-post-status' )),
				'public'                    => $public,
				'label_count'               => _n_noop( 'Friends Only <span class="count">(%1s)</span>', 'Friends Only <span class="count">(%2s)</span>', 'bp-post-status'  ),
				'post_type'                 => $posttypes, // Define one or more post types the status can be applied to.
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'show_in_metabox_dropdown'  => true,
				'show_in_inline_dropdown'   => true,
				'dashicon'                  => 'dashicons-groups',
			) );
		
		}

		if ( class_exists( 'BP_Follow_Component' ) && ! isset( $following_settings['following_disable'] ) && $this->user_is_following() ) {
		
			register_post_status( $this->followingstatusname, array(
				'label'                     => sanitize_text_field(esc_attr_x( 'Following', 'post status label', 'bp-post-status' )),
				'public'                    => $public,
				'label_count'               => _n_noop( 'Following <span class="count">(%1s)</span>', 'Following <span class="count">(%2s)</span>', 'bp-post-status'  ),
				'post_type'                 => $posttypes, // Define one or more post types the status can be applied to.
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'show_in_metabox_dropdown'  => true,
				'show_in_inline_dropdown'   => true,
				'dashicon'                  => 'dashicons-groups',
			) );
		
		}

		if ( class_exists( 'BP_Follow_Component' ) && ! isset( $followed_settings['followed_disable'] ) && $this->user_has_followers() ) {
		
			register_post_status( $this->followedstatusname, array(
				'label'                     => sanitize_text_field(esc_attr_x( 'Followed', 'post status label', 'bp-post-status' )),
				'public'                    => $public,
				'label_count'               => _n_noop( 'Followed <span class="count">(%1s)</span>', 'Followed <span class="count">(%2s)</span>', 'bp-post-status'  ),
				'post_type'                 => $posttypes, // Define one or more post types the status can be applied to.
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'show_in_metabox_dropdown'  => true,
				'show_in_inline_dropdown'   => true,
				'dashicon'                  => 'dashicons-groups',
			) );
		
		}

		
	}

	// Adds post status indication to the posts list in admin
	public function display_post_state( $states ) {
		 
		 global $post;
		 $arg = get_query_var( 'post_status' );
		 if ( !isset($post->post_status) ) return $states;
		 if($arg != $this->grouppendingstatusname){
			  
			  if($post->post_status == $this->grouppendingstatusname){
				   
				   return array(ucwords($this->grouppendingstatuslabel));
			  
			  }
		 
		 }
		 
		 if($arg != $this->memberspendingstatusname){
			  
			  if($post->post_status == $this->memberspendingstatusname){
				   
				   return array(ucwords($this->memberspendingstatuslabel));
			  
			  }
		 
		 }

		 if($arg != $this->groupstatusname){
			  
			  if($post->post_status == $this->groupstatusname){
				   
				   return array(ucwords($this->groupstatuslabel));
			  
			  }
		 
		 }
		 
		 if($arg != $this->membersstatusname){
			  
			  if($post->post_status == $this->membersstatusname){
				   
				   return array(ucwords($this->membersstatuslabel));
			  
			  }
		 
		 }
		 
		if($arg != $this->friendsstatusname){
			  
			  if($post->post_status == $this->friendsstatusname){
				   
				   return array(ucwords($this->friendsstatuslabel));
			  
			  }
		 
		 }

		if($arg != $this->followingstatusname){
			  
			  if($post->post_status == $this->followingstatusname){
				   
				   return array(ucwords($this->followingstatuslabel));
			  
			  }
		 
		 }

		if($arg != $this->followedstatusname){
			  
			  if($post->post_status == $this->followedstatusname){
				   
				   return array(ucwords($this->followedstatuslabel));
			  
			  }
		 
		 }

		return $states;
	}


	// Adds the BP post statuses 
	public function bp_post_status_filter($statuses){

	if (!in_array($this->grouppendingstatusname, $statuses)){
		
	$statuses[] = $this->grouppendingstatusname;
	
	}

	if (!in_array($this->memberspendingstatusname, $statuses)){
		
	$statuses[] = $this->memberspendingstatuslabel;
	
	}

	if (!in_array($this->groupstatusname, $statuses)){
		
	$statuses[] = $this->groupstatusname;
	
	}

	if (!in_array($this->membersstatusname, $statuses)){
		
	$statuses[] = $this->membersstatuslabel;
	
	}

	if (!in_array($this->friendsstatusname, $statuses)){
		
	$statuses[] = $this->friendsstatuslabel;
	
	}


	if (!in_array($this->followingstatusname, $statuses)){
		
	$statuses[] = $this->followingstatuslabel;
	
	}

	if (!in_array($this->followedstatusname, $statuses)){
		
	$statuses[] = $this->followedstatuslabel;
	
	}

	return $statuses;

	}
	

	public function plugins_loaded(){


	load_plugin_textdomain( 'bp-post-status', false, basename( dirname( __FILE__ ) ) . '/languages' ); 

	}



}


$bp_post_status_instance = new BPPS_post_status_plugin();


}




?>
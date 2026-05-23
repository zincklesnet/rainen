<?php
if(!defined('ABSPATH')) {
	exit;
}
/**
 * Get an option value by name
 *
 * @package bp-post-status
 * @param $option_name
 *
 * @return string
 */
function bpps_get_option( $option_name ) {

	$settings = bpps_get_options();

	if ( isset( $settings[ $option_name ] ) ) {
		return $settings[ $option_name ];
	}

	return '';

}
/**
 * If Co-Authors Plus is loaded, add group creator to group-post
 * Since 1.3.0
 * @package bp-post-status
 * @param $Post
 *
 * 
 */
 
 add_action('save_post', 'bpps_add_group_creator_as_coauthor', 300, 2 );
 
 Function bpps_add_group_creator_as_coauthor( $post_id, $post ) {
	 
	if ( 'group_post_pending' != $post->post_status && 'group_post' != $post->post_status ) {
		 
		return;
		 
	}
	
	$post_id = (int) $post_id;
	
	$stored_meta = get_post_meta( $post_id );
	if ( isset( $stored_meta['bpgps_group'] ) ) {
		$group_id = $stored_meta['bpgps_group'];
	} else {
		return false;
	}

	$group_data = groups_get_group( $group_id[0] );
	
	$creator_id = $group_data->creator_id;
	
	if ( $creator_id == $post->post_author ) {
		
		return;
		
	}
	
	$creator = get_userdata( $creator_id );
	$author = get_userdata( $post->post_author );
	
	global $coauthors_plus;
	
	if ( ! $coauthors_plus ) {
		
		return;
		
	}
	
	if ( $creator && $author ) {
		
		$coauthors_plus->add_coauthors( $post_id, array( $creator->user_nicename ), true ); 
	
	}

 }
 
/**
 * Set options - not relly used, but some ideas for suture controls
 * @return mixed
 */
function bpps_get_options() {
	$default = array(

		'post_type'				=> 'post',
		'post_status'			=> 'publish',
		'comment_status'		=> 'open',
		'show_comment_option'	=> 1,
		'custom_field_title'	=> '',
		'enable_taxonomy'		=> 1,
		'allowed_taxonomies'	=> 1,
		'enable_category'		=> 1,
		'enable_tags'			=> 1,
		'show_posts_on_profile' => 0,
		'limit_no_of_posts'		=> 0,
		'max_allowed_posts'		=> 20,
		'publish_cap'			=> 'read',
		'allow_unpublishing'	=> 1,//subscriber //see https://codex.wordpress.org/Roles_and_Capabilities
		'post_cap'				=> 'read',
		'allow_edit'			=> 1,
		'allow_delete'			=> 1,
		'allow_upload'			=> 1,
		//'enabled_tags'			=> 1,
		'taxonomies'		=> array( 'category' ),
		'allow_upload'		=> false,
		'max_upload_count'	=> 2,
		'post_update_redirect'	=> 'archive'
	);

	return $default;
}
/**

 * Check if group posts is disabled for the current group.

 *

 * @since 1.2.0

 * @param $group_id

 * @return bool|mixed|void

 */

function bpps_group_nav_is_disabled( $group_id ) {

	if ( empty( $group_id ) ) {
		return false; //if group id is empty, it is active
	}
	
	$is_disabled = groups_get_groupmeta( $group_id, 'bpps_group_nav_is_disabled' );
	
	return apply_filters( 'bpps_group_nav_is_disabled', intval( $is_disabled ), $group_id );
}

/**

 * Check if activity updates should be issued for all save events and they are outside of the minimum period allowed for post updates.

 *

 * @since 1.4.1

 * @param $post

 * @return bool

 */

function bpps_activity_update_allowed( $post ) {
	
	$general_settings = get_option( 'bpps_general_settings' );

	if ( isset( $general_settings['updates_disabled'] )) {
		
		return false;
		
	} else {
		
		if ( current_user_can('manage_options') ) {
			
			Return true;
			
		}

		$save_activity_disabled = $general_settings['updates_disabled'];
	
		$update_delay = $general_settings['update_delay'];
		
			if ( ! $update_delay ) {
			
			$update_delay = 20;

		}
		
		if ( $update_delay < 1 )  {
			
			$update_delay = 1;
		
		}
		
		$update_delay = $update_delay * 60;
		$current_time = current_time( 'timestamp', 1 );
		$last_updated = get_post_modified_time( 'G', 1 );
		$published = strtotime( $post->post_date_gmt );
		$time_from_last_update = $current_time - $last_updated;
		$time_from_creation = $current_time - $published;
		
		//Don't apply time checking if it's admin, but for others enforce the chosen time delay.
			
		if ( $time_from_last_update <= $update_delay || $time_from_creation <= $update_delay ) {
			
			return false;
		
		} else {
			
			return true;
			
		}
			
	}
	
}
/**
 * Are we dealing with bp post status pages?
 * @return boolean
 */
function bpps_is_component () {
	
	$bp = buddypress();

	if ( bp_is_current_component( $bp->groups->slug ) && bp_is_current_action( BPPS_GROUP_NAV_SLUG ) ) {
		return true;
	}

	return false;
}
/**
 * Are we looking at the blog categories landing page
 * 
 * @return boolean
 */
function bpps_is_home() {
	
	$bp = buddypress();

	if ( bpps_is_component() && empty( $bp->action_variables[0] ) ) {
		return true;
	}
	
	return false;
	
}
/**
 * Is it single post?
 * 
 * @return boolean
 */
function bpps_is_single_post() {
	$bp = buddypress();

	if ( bpps_is_component() && ! empty( $bp->action_variables[0] ) ) {
		return true;
	}
	return false;
}

/**
 * Check if we are on the single term screen
 *
 * @return boolean
 */
function bpps_is_term() {
	$bp = buddypress();

	if ( bpps_is_component() && ! empty( $bp->action_variables[1] ) && in_array( $bp->action_variables[0], bpps_get_taxonomies() ) ) {
		return true;
	}

	return false;
}

/**
 * Check if group posts are disabled for the current group
 *
 * @global type $bp
 * @return type 
 */
function bpps_is_disabled_for_group() {
	
	$bp = buddypress();
	
	$group_id = false;
	
	if ( bp_is_group_create() ) {
		if ( isset( $_COOKIE['bp_new_group_id'] ) ) {
			$group_id = $_COOKIE['bp_new_group_id'];
		}
	} elseif ( bp_is_group() ) {
		$group_id = bp_get_current_group_id();
	}
	
	return apply_filters( 'bpps_is_disabled_for_group', bpps_is_disabled( $group_id ) );
}

/**
 * Check if Blogging is disabled for the given group
 *
 * @param $group_id
 *
 * @return bool|mixed|void
 */
function bpps_is_disabled( $group_id ) {
	
	if ( empty( $group_id ) ) {
		
		return false; //if group id is empty, it is active
	
	}
	
	$settings = get_option( "bpps_groups_settings" );
	if ( isset( $settings["groups_disable"] ) ) {
		
		$posts_disable = $settings["groups_disable"];
		
	} else {
		
		$posts_disable = '';
	}
	
	if ( ! $posts_disable ) {
		
		$is_disabled = groups_get_groupmeta( $group_id, 'bpps_is_disabled' );
	
	} else {
		
		$is_disabled = 1;
		
	}
		
	return apply_filters( 'bpps_is_disabled', intval( $is_disabled ), $group_id );
}


/**
 * Get associated post type
 *
 * @return string
 */
function bpps_get_post_type() {

	$post_type  = ( bpps_get_option('post_type') ) ? bpps_get_option('post_type') : 'post';
	return apply_filters( 'bcg_get_post_type', $post_type );
}


/**
 * Get a post by slug name
 *
 * @param $slug
 *
 * @return WP_Post
 */
function bpps_get_post_by_slug( $slug ) {
	global $wpdb;
	
	$query = "SELECT * FROM $wpdb->posts WHERE post_name = %s AND post_type = %s LIMIT 1";
	$post = $wpdb->get_row( $wpdb->prepare( $query, $slug, bpps_get_post_type() ) );
	
	return $post;
}


function bpps_get_group_post_status( $user_id ) {

	$authority_to_publish   = bpps_get_option( 'publish_cap' );
	$group_id               = bp_get_current_group_id();
	$post_status            = 'draft';

	if ( bpps_can_user_publish_post( get_current_user_id() ) ) {
		$post_status = 'publish';
	}

	return $post_status;
}



/**
 * Get PS Group landing page url
 *
 * @param type $group_id
 * @return type
 */
function bpps_get_home_url ( $group_id = null ) {

	if ( ! empty( $group_id ) ) {
		$group = new BP_Groups_Group( $group_id );
	} else {
		$group = groups_get_current_group();
	}

	return apply_filters( 'bpps_home_url', bp_get_group_url( $group ) . BPPS_GROUP_NAV_SLUG );
}

/**
 * Get Post count for the group
 * @revised 1.8.2 revised SQL to account for 0 and 1 post instances. Aso corrected group post count to remove the homepage from the total.
 * @param type $group_id
 * @param status - group post status
 * @return int count
 */

 function bpps_get_group_post_count ( $group_id = null, $status = 'group_post' ) {

	global $wpdb, $bp;
	
	if ( $group_id == '' ) {
		$group_id = $bp->groups->current_group->id;
	}
	
	
	// Get Group Home page id if set
	$group_home = groups_get_groupmeta( $group_id, 'bpps_home_post_id' );

	if ( ! in_array( $status, array( 'group_post', 'group_post_pending' ) ) ) return;
	
	$logged_in = is_user_logged_in();
	$user_id = bp_loggedin_user_id();
	$group_member = groups_is_user_member( $user_id, $group_id ) || bp_current_user_can( 'bp_moderate' );
	$group_posts = array();
	
	if ( $status == 'group_post' ) {
		$group_query = "SELECT ID from $wpdb->posts WHERE post_status = 'group_post'";
	} else if ( $status == 'group_post_pending' ) {
		$group_query = "SELECT ID from $wpdb->posts WHERE post_status = 'group_post_pending'";
	}
	
	$group_only_ids = $wpdb->get_results($group_query, ARRAY_N);
	if ( !empty( $group_only_ids ) && count( $group_only_ids ) > 1 ) {
		foreach ( $group_only_ids as $group_post ) {
			$group_posts[] = $group_post[0];
		}
		$qs = join( ',', $group_posts );
		$qs = 'IN (' . $qs . ')';
	
	} else if ( !empty( $group_only_ids ) && count( $group_only_ids ) == 1 ) {
		
		$qs = '= ' . $group_only_ids[0][0];
	
	} else {
		
		$group_posts_count = 0;
	}
	
	if ( ! isset( $group_posts_count ) || $group_posts_count != 0 ) {
		$group_posts_refined = array();
		$group_posts_second = array();
		
		$group_query = "SELECT post_id from $wpdb->postmeta WHERE post_id $qs AND meta_key = 'bpgps_group' AND meta_value = $group_id";
		$group_post_array = $wpdb->get_results($group_query, ARRAY_N);
		
		foreach ( $group_post_array as $group_post ) {
			$group_posts_second[] = $group_post[0];
		}
		
		if( count($group_posts_second ) > 0 ) {
			$qs2 = join( ',', $group_posts_second );
			$group_query = "SELECT post_id,meta_value from $wpdb->postmeta WHERE post_id IN ($qs2) and meta_key = 'bpgps_group_post_status'";
			$group_post_array = $wpdb->get_results($group_query, ARRAY_N);
		
			foreach ( $group_post_array as $group_post ) {
				if ( $group_post[1] == 'public' ) {
					$group_posts_refined[] = $group_post[0];
				} else if ( $group_post[1] == 'members_only' && isset( $logged_in ) ) {
					$group_posts_refined[] = $group_post[0];
				} else if ( $group_post[1] == 'group_only' && isset( $group_member ) ) {
					$group_posts_refined[] = $group_post[0];
				}
			}
			$group_posts_count = count( $group_posts_refined );
		} else {
			$group_posts_count = 0;
		}

		//Remove the group home page from the count.
		if ( isset( $group_home ) && is_numeric( $group_home ) ) {
			$group_posts_count = $group_posts_count - 1;
		}
	}
	
	return apply_filters( 'bpps_get_group_post_count', $group_posts_count );
	
}

/**
 * count the total pending posts for a user
 *
 * @since 1.7.4
 * @revised 1.7.5 added cap check.
 * @return int pending posts count
 */

 function bpps_count_pending_posts() {
	
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}
	global $wpdb;
	$user_id = get_current_user_id();
	$pending_post_ids = array();
	$pending_posts_query = "SELECT ID from $wpdb->posts WHERE ( post_status = 'pending' OR post_status = 'members_only_pending' OR post_status = 'group_post_pending' ) && post_author = $user_id";
	$pending_post_ids = $wpdb->get_results($pending_posts_query, ARRAY_N);
	
	return count( $pending_post_ids );
	
}

/**
 * count the total moderation posts for a site
 *
 * @since 1.7.4
 * @revised 1.7.5 added cap check.
 * @return int pending posts count
 */

 function bpps_count_moderation_posts() {
	
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		return false;
	}
	global $wpdb;
	
	$mod_post_ids = array();
	$mod_posts_query = "SELECT ID from $wpdb->posts WHERE post_status = 'pending' OR post_status = 'members_only_pending' OR post_status = 'group_post_pending'";
	$mod_post_ids = $wpdb->get_results($mod_posts_query, ARRAY_N);
	
	return count( $mod_post_ids );
	
}

/**
 * count the users published posts
 *
 * @since 1.8.0
 * @revised 1.8.2 added check for current user id being not set.
 * @revised 1.8.4 revised SQL query where single post status and post type is selected
 * @revised 1.8.4 updated checks for current_user_id.
 * @return int users post count
 */

 function bpps_count_users_posts( $user_id = '' ) {
	
	global $wpdb;
	
	if ( ! isset( $user_id ) ) $user_id = bp_displayed_user_id;
	
	$current_user_id = get_current_user_id();
	$user_posts_query = '';
	
	// Members only query
	if ( is_user_logged_in() ) {
		$user_posts_query .= "'members_only',";
	}
	
	// Friends only query
	if ( isset( $current_user_id ) ) {
		if ( bp_is_active( 'friends' ) && ( BP_Friends_Friendship::check_is_friend( $user_id, $current_user_id ) || $current_user_id == $user_id ) ) {
			$user_posts_query .= "'friends_only',";
		}
	}
	
	// Following query
	if ( isset( $current_user_id ) ) {
		$args = array(
			'leader_id'   => $user_id,
			'follower_id' => $current_user_id
		);
	
		if ( function_exists( 'bp_follow_is_following' ) && ( bp_follow_is_following( $args ) || $current_user_id == $user_id ) ) {
			$user_posts_query .= "'following',";
		}
	}

	// Followed query
	if ( isset( $current_user_id ) ) {
		$args = array(
			'leader_id'   => $current_user_id,
			'follower_id' => $user_id
		);
	
		if ( function_exists( 'bp_follow_is_following' ) && ( bp_follow_is_following( $args ) || $current_user_id == $user_id ) ) {
			$user_posts_query .= "'followed',";
		}
	}

	//Groups query
	$group_count = 0;
	if ( bp_is_active( 'groups' ) && isset( $current_user_id ) && $current_user_id == $user_id ) {
		$user_posts_query .= "'group_post',";
	} else if ( bp_is_active( 'groups' ) && isset( $current_user_id ) ) {
		$group_count = bpps_get_users_visible_group_posts_count( $user_id, $current_user_id );
	}
	
	// Private query
	if ( isset( $current_user_id ) && $current_user_id == $user_id ) { 
		$user_posts_query .= "'private',";
	}

	// Public posts query
	$user_posts_query .= "'publish'";
	
	if ( $user_posts_query == "'publish'" ) {
		$user_posts_query = "= 'publish'";
	} else {
		$user_posts_query = "IN (" . $user_posts_query . ")";
	}
	
	// Post types
	$post_types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'names' );
	
	$post_type_query = '';
	foreach ( $post_types as $key => $post_type ) {
		if ( $post_type == 'post' || $post_type == 'attachment' ) continue;
		$post_type_query .= "$post_type,";
	}
	$post_type_query .= "'post'";
	
	if ( $post_type_query == "'post'" ) {
		$post_type_query = "= 'post'";
	} else {
		$post_type_query = "IN (" . $post_type_query . ")";
	}
	
	// Execute query
	$user_post_ids = array();
	$main_query = "SELECT ID from $wpdb->posts WHERE post_status $user_posts_query AND post_author = $user_id AND post_type = 'post'";
	$user_post_ids = $wpdb->get_results($main_query, ARRAY_N);
	
	return count( $user_post_ids ) + $group_count;
	
}

/**
 * get the users visible group posts count
 * @since 1.8.0
 * 
 * @return int count of visible posts
 */

function bpps_get_users_visible_group_posts_count( $user_id, $current_user_id ) {
	
	global $wpdb;
	$visitors_groups = BP_Groups_Member::get_group_ids( $current_user_id );
	$visitors_groups = $visitors_groups['groups'];
	
	$posts_query = "SELECT ID from $wpdb->posts WHERE post_status = 'group_post' AND post_author = $user_id";
	$users_group_only_ids = $wpdb->get_results( $posts_query, ARRAY_N );
	$count = 0;
	foreach ( $users_group_only_ids as $group_only_id ) {
		foreach ( $group_only_id as $group_id ) {
			$group = get_post_meta( $group_id, 'bpgps_group', true );
			$group_status = get_post_meta( $group_id, 'bpgps_group_post_status', true );
			if ( in_array( $group, $visitors_groups ) ) {
				$count = $count + 1;
			} else if ( $group_status == 'public' ) {
				$count = $count + 1;
			} else if ( $group_status == 'members_only' && is_user_logged_in() ) {
				$count = $count + 1;
			}
		}
	}
	
	return $count;
	
}
/**
 * get the appropriate arguments for wp_query post lookup
 *
 * 
 * @return type wp_query search args
 */

function bpps_get_group_posts_query( $group_id = '', $query_type = '', $query_string = '' ) {
	
	global $wpdb;
	$bp = buddypress();
	$slug = '';
	$return_no ='';
	
	if ( bpps_is_single_post() ) {
		$slug = bpps_get_page_slug();
		$return_no = 1;
	}

	if ( $group_id == '' ) {
		$group_id = $bp->groups->current_group->id;
	}

	$group_home = groups_get_groupmeta( $group_id, 'bpps_home_post_id' );	
	$group_query = "SELECT ID from $wpdb->posts WHERE post_status = 'group_post'";
	$group_only_ids = $wpdb->get_results($group_query, ARRAY_N);
	
	$group_data = array();
	$logged_in = is_user_logged_in();
	$user_id = bp_loggedin_user_id();
	
	// Get list of users group IDs
	$member = false;
	$users_groups = array();
	if ( $logged_in ) {
		
		$users_groups = BP_Groups_Member::get_group_ids( $user_id );
		$users_groups = $users_groups['groups'];
		if ( in_array( $group_id, $users_groups ) ) {
			$member = true;
		}
		
	}
	
	// Match group names with post id's
	foreach ( $group_only_ids as $group_post_id ) {
		
		foreach ( $group_post_id as $post_id ) {
			
			$group_status[$post_id] = get_post_meta( $post_id, 'bpgps_group_post_status', true );
			$sticky_status = get_post_meta( $post_id, 'bpps_group_post_sticky', true );
			if ( isset( $sticky_status ) && $sticky_status == 1 ) {
				$group_data[] = $post_id;
			} else if ( $group_status[$post_id] == 'group_only' && ! $member ) {
				
				if ( get_post_meta( $post_id, 'bpgps_group', true ) == $group_id ) {
					$group_data[] = $post_id;
				}
		
			} else if ( $group_status[$post_id] == 'members_only' && ! $logged_in ) {
				
				if ( get_post_meta( $post_id, 'bpgps_group', true ) == $group_id ) {
					$group_data[] = $post_id;
				}
			
			} 
			
		}
		
	}
	
	$group_data[] = $group_home;
	
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	
	$args = array(
		'meta_key' 		=> 'bpgps_group',
		'meta_value' 	=> $group_id,
		'post_type' 	=> 'any',
		'post_status' 	=> 'group_post',
		'name' 			=> $slug,
		'post__not_in' 	=> $group_data,
		'numberposts'	=> $return_no,
		'paged'			=> $paged
		);

	if ( $query_type == 'search' ) {
		$args['s'] = $query_string;
	}
		
	return $args;
	
}

/**
 * get the query string for the profile My Posts page
 *
 * @since 1.7.1
 * @revised 1.8.0
 *
 * @return type array WP_Query args
 */

function bpps_get_my_posts_query( $query_type = '', $query_string = '' ) {
	
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$current_user_id = get_current_user_id();
	$user_id = bp_displayed_user_id();
	$sticky_posts = bpps_get_users_sticky_post_ids( $user_id );

	$args = array(
		'author' 			=> $user_id,
		'paged' 			=> $paged,
		'posts_per_page' 	=> 10,
		'post_type' 		=> 'post'
	);


	if ( ! empty( $sticky_posts ) ) {
		$args['post__not_in'] = $sticky_posts;
	}

	if ( $query_type == 'search' ) {
		$args['s'] = $query_string;
	}
	
	$user_posts_query = array();
	// Public posts query
	$user_posts_query[] = 'publish';
	
	// Members only query
	if ( is_user_logged_in() && isset($current_user_id) ) {
		$user_posts_query[] = 'members_only';
	}
	
	// Friends only query
	if ( bp_is_active( 'friends' ) && (   isset($current_user_id) && BP_Friends_Friendship::check_is_friend( $user_id, $current_user_id ) ||  isset($current_user_id) && $current_user_id == $user_id ) ) {
		$user_posts_query[] = 'friends_only';
	}
	
	// Following query
	if (isset( $current_user_id) ) {
		$args2 = array(
			'leader_id'   => $user_id,
			'follower_id' => $current_user_id
		);
	}
	if ( function_exists( 'bp_follow_is_following' ) && ( bp_follow_is_following( $args2 ) ||  isset($current_user_id) && $current_user_id == $user_id ) ) {
		$user_posts_query[] = 'following';
	}

	// Followed query
	if (isset($current_user_id) ) {
		$args3 = array(
			'leader_id'   => $current_user_id,
			'follower_id' => $user_id
		);
	}
	
	if ( function_exists( 'bp_follow_is_following' ) && ( bp_follow_is_following( $args3 ) ||  isset($current_user_id) && $current_user_id == $user_id ) ) {
		$user_posts_query[] = 'followed';
	}

	//Groups query
	if ( bp_is_active( 'groups' ) ) {
		$user_posts_query[] = 'group_post';
	} 

	
	// Private query
	if ( $current_user_id == $user_id ) { 
		$user_posts_query[] = 'private';
	}
	
	$args['post_status'] = $user_posts_query;
	
	return $args;
	
}

/**
 * get the sticky posts query args
 *
 * @since 1.7.1
 * @revised 1.8.0
 *
 * @return type array WP_Query args
 */

function bpps_get_sticky_posts_query() {
	
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	if ( bp_is_group() ) {
		$meta_query = 'bpps_group_post_sticky';
		$is_group = true;
	} else if ( bp_is_user() ) {
		$meta_query = 'bpps_my_posts_sticky';
		$is_group = false;
	}

	if ( ! $is_group ) {
		$args = array(
			'paged' 			=> $paged,
			'posts_per_page' 	=> 10,
			'meta_key'			=> $meta_query,
			'meta_value'		=> 1
		);
		$args['author'] = bp_displayed_user_id();
		$context = 'my-posts';
		if ( ( $args['author'] == get_current_user_id() ) || current_user_can( 'manage_options' ) ) {
			$args['post_status'] = 'any';
		}
	} else {
		$group_id = bp_get_current_group_id();
		$args = array(
			'paged' 			=> $paged,
			'posts_per_page' 	=> 10,
			'post_status' 		=> 'group_post',
			'meta_query'		=> array(
				'relation'			=> 'AND',
				array(
					'key'		=> 'bpgps_group',
					'value'		=> $group_id
				),
				array(
					'key'		=> $meta_query,
					'value'		=> 1
				)
			)
		);
		if ( is_super_admin() || groups_is_user_admin( get_current_user_id(), $group_id ) ) {
			$args['post_status'] = array( 'group_post', 'group_post_pending' );
		}

	}
	
	return $args;
	
}

//add_filter('posts_results', 'bpps_update_results');

function bpps_update_results( $results ) {
	
	$newpost = get_post(1288);
	
	$test = get_post_status_object(get_post_status($results ) );
	var_dump($test);
	var_dump($test->public);
	$slug=bpps_get_page_slug();
	var_dump($slug);
	return $results;
}

/**
 * get the post ids for an authors sticky posts
 *
 * @version: since 1.7.1
 *
 * @return type array
 */

function bpps_get_users_sticky_post_ids( $user_id ) {
	
	global $wpdb;
	//get users posts
	$user_query = "SELECT ID from $wpdb->posts WHERE post_author = $user_id AND post_type = 'post'";
	$user_post_ids = $wpdb->get_results($user_query, ARRAY_N);
	
	$sticky_posts = array();
	
	if ( !empty( $user_post_ids ) ) {
		
		foreach( $user_post_ids as $post_array ) {
			
			foreach ( $post_array as $post_id ) {
				
				$sticky = get_post_meta( $post_id, 'bpps_my_posts_sticky', true );
				if ( isset( $sticky ) && $sticky == 1 ) {
					$sticky_posts[] = $post_id;
				}
			}
		}
		
	}
	
	return $sticky_posts;
}

/**
 * get the post info for the homepage
 *
 * @version: since 1.2.0 
 *
 * @return type wp_query search args
 */
//
function bpps_get_home_query() {
	
	global $wpdb;
	$bp = buddypress();
	
	$group_id = $bp->groups->current_group->id;
	$group_home = groups_get_groupmeta( $bp->groups->current_group->id, 'bpps_home_post_id' );	
	
	if ( empty( $group_home ) ) {
		
		return false;
		
	}
	
	$args = array(
		'p' => $group_home
		);
		
	return $args;
	
}
/**
 * check homepage is set for the group
 *
 * @version: since 1.2.0 
 *
 * @return type bool
 */
//
function bpps_home_page_set( $group_id = NULL ) {
	
	global $wpdb;
	$bp = buddypress();
	
	if ( ! $group_id ) {
		
		$group_id = $bp->groups->current_group->id;
	
	}
	
	$group_home = groups_get_groupmeta( $bp->groups->current_group->id, 'bpps_home_post_id' );	
	
	if ( $group_home ) {
		
		return true;
	
	} else {
		
		return false;
	
	}
	
	
}
//comment posting a lil bit better
add_action( 'comment_form', 'bpps_fix_comment_form' );

function bpps_fix_comment_form ( $post_id ) {
	
	if ( ! bpps_is_single_post() ) {
		return;
	}
	
	$post = get_post( $post_id );
	$permalink = bpps_get_post_permalink( $post );
	?>
	<input type='hidden' name='redirect_to' value="<?php echo esc_url( $permalink ); ?>" />
	<?php
}
//fix to disable/reenable buddypress comment open/close filter
function bpps_disable_bp_comment_filter() {
    
    if( has_filter( 'comments_open', 'bp_comments_open' ) ) {
        remove_filter( 'comments_open', 'bp_comments_open', 10, 2 );
	}	
}
add_action( 'bp_before_group_blog_post_content', 'bpps_disable_bp_comment_filter' );

function bpps_enable_bp_comment_filter() {
    
    if( function_exists( 'bp_comments_open' ) ) {
		add_filter( 'comments_open', 'bp_comments_open', 10, 2 );
	}	
}

add_action( 'bp_after_group_blog_content', 'bpps_enable_bp_comment_filter' );

/* fixing permalinks for posts/categories inside the bcg loop */



add_filter( 'wp_title', 'bpps_fix_page_title', 200, 3 );
//for title fix
function bpps_fix_page_title( $title, $sep, $seplocation ) {
	
	if ( ! bpps_is_single_post() ) {
		return $title;
	}
	
	$post = bpps_get_post_by_slug( bp_action_variable(0) );
	
	$post_title =  $post->post_title;
	
	if ( 'right' == $seplocation ) { // sep on right, so reverse the order
		$title       =  $post_title . " $sep " . $title;
	} else {
		$title =  $title . " $sep " . $post_title;
	}
	
	return $title;

}

/**

 * return the slug for the requested page.

 *

 * @since 1.6.0

 *

 * @return string

 */

function bpps_get_page_slug() {
 
	$urlbits = explode( "/", $_SERVER["REQUEST_URI"]);
	$length = count($urlbits);
	$page_slug = $urlbits[$length-2];
	
	return $page_slug;
	
}

/**

 * check current user can edit post.

 *

 * @since 1.6.1

 *

 * @return boolean

 */
 
 function bpps_current_user_can_edit( $post_id = '' ) {
	 
	if ( ! is_user_logged_in() ) {
		 
		return false;
	}
	 
	$user_id = get_current_user_id();
	 
	if ( $post_id = '' || ! is_numeric( $post_id ) ) {
		 
		 global $post;
	 
	} else {
		
		$post = get_post( $post_id );
	
	}
	 
	if ( isset( $post ) && ( $post->author_id == $user_id || current_user_can( 'edit_others_posts' ) ) ) {
		 
		return true;
		 
	}
	 
	if ( $post->post_status == 'group_post' ) {
		
		$group_id = get_post_meta( $post->ID, 'bpgps_group' );
		
		if ( isset( $group_id ) && groups_is_user_admin( $user_id, $group_id ) ) {
		
			return true;
			
		}
		
	 }
	 
	 return false;
 }
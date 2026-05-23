<?php

if(!defined('ABSPATH')) {
	exit;
}

// @package bp-post-status


if ( ! class_exists( 'BPPS_Activity' ) ) :


// BuddyPress Activity for BP Post Status

// Todo: rework Activity message for groups
// Done: Rework Group_post activity updates so that public and members_only show site-wide


class BPPS_Activity {

	/**

	 * Plugin's main instance

	 *

	 * @var object

	 */

	protected static $instance;



	
	function __construct() {
		
		//Register activities
		add_action( 'bp_register_activity_actions', array( $this, 'register_activity' ) );
		
		// Update Groups latest activity
		add_action( 'groups_create_group_post', array( $this, 'bpps_update_last_activity' ) );

		add_action('save_post', array( $this, 'bpps_buddypress_edit_post'), 10, 2 );
		add_action('save_post', array( $this, 'bpps_buddypress_edit_group_post'), 11, 2 );

		//run during post save to save the group name for group only viewing.
		add_action( 'save_post', array( $this,"bpps_meta_save" ) );
		
		// deletes the activity item related to a post deletion
		add_action( 'delete_post', array( $this, 'bpps_delete_activity' ) );		
	}
	

	/**

	 * Return an instance of this class.

	 *

	 * @since 1.0.0

	 *

	 * @return object A single instance of this class.

	 */

	public static function start() {



		// If the single instance hasn't been set, set it now.

		if ( null == self::$instance ) {

			self::$instance = new self;

		}



		return self::$instance;

	}

	/**

	 * Register activities for BP Post Status.

	 *

	 * @since 1.2.1

	 *

	 * 

	 */
	 
	function register_activity() {

		$bp = buddypress();

		if ( ! bp_is_active( 'activity' ) || ! bp_is_active( 'groups' ) ) {
			return false;
		}

		bp_activity_set_action(
			$bp->groups->id,
			'created_group_post',
			__( 'Added a group post', 'bp-post-status' ),
			'bpps_format_activity_action_created_post',
			__( 'New Group Posts', 'bp-post-status' ),
			array( 'activity', 'group', 'member', 'member_groups' )
		);
		
		do_action( 'groups_register_activity_actions' );
	}
	
/**
 * Format 'created_group_post' activity actions.
 *
 * @since 1.2.1
 *
 * @param string $action   Static activity action.
 * @param object $activity Activity data object.
 * @return string
 */
	function bpps_format_activity_action_created_post( $action, $activity ) {
		$user_link = bp_core_get_userlink( $activity->user_id );

		$group      = groups_get_group( $activity->item_id );
		$group_link = '<a href="' . esc_url( bp_get_group_url( $group ) ) . '">' . esc_html( $group->name ) . '</a>';

		$action = sprintf( esc_attr__( '%1$s created the group post %2$s', 'bp-post-status'), $user_link, $group_link );

		/**
		 * Filters the 'created_group_post' activity actions.
		 *
		 * @since 1.2.1
		 *
		 * @param string $action   The 'created_group' activity action.
		 * @param object $activity Activity data object.
		 */
		return apply_filters( 'groups_activity_created_group_post_action', $action, $activity );
	}
/**
 * Update the last_activity meta value for a given group.
 *
 * @since 1.2.1
 *
 * @param int $group_id Optional. The ID of the group whose last_activity is
 *                      being updated. Default: the current group's ID.
 * @return false|null False on failure.
 */
	function bpps_update_last_activity( $group_id = 0 ) {

		if ( empty( $group_id ) ) {
			if ( isset( buddypress()->groups->current_group->id ) ) {
				
				$group_id = buddypress()->groups->current_group->id;
				
			}
		}

		if ( empty( $group_id ) ) {
			
			return false;
		
		}

		groups_update_groupmeta( $group_id, 'last_activity', bp_core_current_time() );
	}


	//This section vets the type of save operation and publishes the activity update for qualifying posts.

/**
 * Function to create activity update when a group post is published or updated.
 *
 * @since 1.4.1
 *
 * @param int $post_id, $post The ID of the group post whose last_activity is
 *                      being updated.
 * @return false|null False on failure.
 */

	function bpps_buddypress_edit_group_post( $post_id, $post ) {
		
		// if activity module is not active, why bother.
		if ( ! bp_is_active( 'activity' ) ) {
			return false;
		}
		//Check for none supported post-statuses
		$post_status = $post->post_status;

		
		if ( $post_status != 'group_post' && $post_status != 'group_post_pending' ) {
			
			return;
			
		}
		
		
		//check it's a new post
		if( $post->post_modified_gmt == $post->post_date_gmt ){
			$new_post = true;
		} else {
			$new_post = false;
			if ( ! bpps_activity_update_allowed( $post ) ) {
				return;
			}
		}

		// Update Activity feed with post details
		global $bp, $user_id;
		$title = $post->post_title;
		$user_fullname  = bp_core_get_user_displayname($post->post_author);


		// Check that author name is valid.
		if ( $user_fullname == '' ) {
			return false;
		}
		 
		if ( $post_status == 'group_post' ) {
			$component = buddypress()->groups->id;
			$component_type = 'groups';
			$hide = 1;
			$post_link_fix = '';
			$primary_id = get_post_meta( $post->ID, 'bpgps_group', true );
			$group_post_status = get_post_meta( $post->ID, 'bpgps_group_post_status', true );
			$secondary_id = $post->ID;
			$group = groups_get_group( $primary_id );
			$group_title = $group->name;
			if ( $new_post ) {
				$message = esc_attr( esc_attr__( 'added to the ', 'bp-post-status' ) ) . '<a href="' . bp_get_group_url( $group ) . '">' . 
				$group_title . '</a>' .  esc_attr( esc_attr__( ' group a new post - ', 'bp-post-status' ) );
			} else {
				$message = esc_attr( esc_attr__( 'updated in the ', 'bp-post-status' ) ) . '<a href="' . bp_get_group_url( $group ) . '">' . 
				$group_title . '</a>' .  esc_attr( esc_attr__( ' group a post - ', 'bp-post-status' ) );
				
			}
			$type = 'created_group_post';
			$content = bpps_get_content( $post, 'activity-groups' );
			$post_link = bp_get_groups_directory_permalink() . bp_get_group_slug( $group ) . '/' . BPPS_GROUP_NAV_SLUG . '/' . $post->post_name . $post_link_fix;
			do_action( 'groups_create_group_post', $primary_id );

			if ( $group_post_status == 'public' || $group_post_status == 'members_only' ) {
				$hide = 0;
				$bp_activity_id = bp_activity_add(array(
					 
					'action' 				=> '<a href="' . $bp->loggedin_user->domain . '" >' . $user_fullname.'</a> ' .  $message . '<a href="'.	$post_link . '" >' . $post->post_title . '</a>',
					'content' 				=> $content,
					'component' 			=> $component,
					'type' 					=> $type,
					'primary_link' 			=> $post_link,
					'user_id' 				=> $post->post_author,
					'item_id' 				=> $primary_id,
					'secondary_id'			=> $secondary_id,
					'hide_sitewide'			=> $hide
					 ));

				// Add this update to the "latest update" usermeta so it can be fetched anywhere.
				bp_update_user_meta( bp_core_get_user_displayname($post->post_author), 'bp_latest_update', array(
					'id'      => $bp_activity_id,
					'content' => $content
				) );

				if ( isset( $_POST[ 'post_notify' ] ) ) {
					
					$notifications = BPPS_Notifications::bpps_add_notification( $post_id, $component_type, $bp_activity_id );

				}

			}
			if ( $group_post_status == 'group_only' ) {
				$args = array(
					'content'    	=> $content,
					'user_id'    	=> $post->post_author,
					'group_id'   	=> $primary_id,
				);
				$activity_id = groups_record_activity( array(
					'user_id'    	=> $post->post_author,
					'action'     	=> '<a href="' . $bp->loggedin_user->domain . '" >' . $user_fullname.'</a> ' .  $message . '<a href="'.	$post_link . '" >' . $post->post_title . '</a>',
					'content'    	=> $content,
					'type'       	=> 'activity_update',
					'item_id'    	=> $primary_id,
					'hide_sitewide' => $hide
				) );

				groups_update_groupmeta( $primary_id, 'last_activity', bp_core_current_time() );
				
				if ( isset( $_POST[ 'post_notify' ] ) ) {
					
					$notifications = BPPS_Notifications::bpps_add_notification( $post_id, $component_type, $activity_id );

				}
				return;
			}
		}

		if ( $post_status == 'group_post_pending' ) {
			$component_type = 'approval';
			$notifications = BPPS_Notifications::bpps_add_notification( $post->ID, $component_type );
			return;
		}

	}

	//Function to create activity update when a published post is updated.
	function bpps_buddypress_edit_post( $post_id, $post ) {
		
		// if activity module is not active, why bother.
		if ( ! bp_is_active( 'activity' ) ) {
			return false;
		}
		//Check for none supported post-statuses
		$post_status = $post->post_status;

		
		if ( $post_status != 'members_only' && $post_status != 'friends_only' && $post_status != 'members_only_pending' && $post_status != 'following' && $post_status != 'followed' ) {
			
			return;
			
		}
		
		//check it's a new post
		if( $post->post_modified_gmt == $post->post_date_gmt ){
			$new_post = true;
		} else {
			$new_post = false;
			if ( ! bpps_activity_update_allowed( $post ) ) {
				return;
			}
		}


		// Update Activity feed with post details
		global $bp, $user_id;
		$title = $post->post_title;
		$user_fullname  = bp_core_get_user_displayname($post->post_author);
		$post_link = get_permalink($post->ID);

		// Check that author name is valid.
		if ( $user_fullname == '' ) {
			return false;
		}
		 

		 
		if ( $post_status == 'friends_only' ) {
			if ( $new_post ) {
				$message = esc_attr( esc_attr__( 'added a new post for friends only - ', 'bp-post-status' ) );
			} else {
				$message = esc_attr( esc_attr__( 'updated a for friends only - ', 'bp-post-status' ) );
				
			}
			
			$message = 
			$component = 'blogs';
			$component_type = 'friends';
			$primary_id = $post->post_author;
//			$primary_id = get_current_blog_id();
			$secondary_id = $post->ID;
			$hide = 1;
			$type = 'new_blog_post';
			$content = bpps_get_content( $post, 'activity-friends' );
		}
			 
		if ( $post_status == 'members_only' ) {
		
			if ( $new_post ) {
				$message = esc_attr( esc_attr__( 'added a new post for members only - ', 'bp-post-status' ) );
			} else {
				$message = esc_attr( esc_attr__( 'updated a post for members only - ', 'bp-post-status' ) );
				
			}
			
			$component = 'posts';
			$component_type = 'members';
			$hide = false;
			$primary_id = get_current_blog_id();
			$secondary_id = $post->ID;
			$type = 'new_blog_post';
			$content = bpps_get_content( $post, 'activity-members' );
		}

		if ( $post_status == 'members_only_pending' ) {
			$component_type = 'approval';
			$notifications = BPPS_Notifications::bpps_add_notification( $post->ID, $component_type );
			return;
		}
		
		if ( $post_status == 'following' ) {
			$component_type = 'following';
			$notifications = BPPS_Notifications::bpps_add_notification( $post->ID, $component_type );
			return;
		}
		
		if ( $post_status == 'followed' ) {
			$component_type = 'followed';
			$notifications = BPPS_Notifications::bpps_add_notification( $post->ID, $component_type );
			return;
		}
		 //Create BP Activity

		$bp_activity_id = bp_activity_add(array(
			 
			'action' 				=> '<a href="' . $bp->loggedin_user->domain . '" >' . $user_fullname.'</a> ' .  $message . '<a href="'.	$post_link . '" >' . $post->post_title . '</a>',
			'content' 				=> $content,
			'component' 			=> $component,
			'type' 					=> $type,
			'primary_link' 			=> $post_link,
			'user_id' 				=> $post->post_author,
			'item_id' 				=> $primary_id,
			'secondary_id'			=> $secondary_id,
			'hide_sitewide'			=> $hide
			 ));

		// Add this update to the "latest update" usermeta so it can be fetched anywhere.
		bp_update_user_meta( bp_core_get_user_displayname($post->post_author), 'bp_latest_update', array(
			'id'      => $bp_activity_id,
			'content' => $content
		) );
		
		// Add Notification if requested
		if ( isset( $_POST[ 'post_notify' ] ) ) {
			
			$notifications = BPPS_Notifications::bpps_add_notification( $post_id, $component_type, $bp_activity_id );

		}
		
	}

	function bpps_delete_activity($post_id) {
		
		$post = get_post($post_id);
		
		if ( $post->post_status != 'members_only' && $post->post_status != 'group_post' && $post->post_status != 'friends_only' ) {
			
			return;
		
		}
		
		// Removes the activity on post deletion
		$primary_id = get_current_blog_id();
		$item_id = $post_id;
		$seconday_id = $post_id;
		
		// Get the Activity id
		$args = array(
			'item_id'           => $item_id,
			'secondary_item_id' => $secondary_id
		);

		$activity_id = bp_activity_get_activity_id( $args );


		//Delete Activity
		$args = array(
			'id'                => $activity_id,
			'item_id'           => $item_id,
			'secondary_item_id' => $secondary_id
		);

		bp_activity_delete( $args );
		

	}

		
	// Save the group id to the post meta data 
	function bpps_meta_save( $post_id ) {

	    $is_valid_nonce = ( isset( $_POST[ 'bpps_sites_nonce' ] ) && wp_verify_nonce( $_POST[ 'bpps_post_status_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status

		if ( !$is_valid_nonce ) {
				
				return;
		}
	
		if ( isset( $_POST[ 'post_group' ] ) ) {
			
			update_post_meta( $post_id, 'bpgps_group', sanitize_text_field ( htmlentities($_POST[ 'post_group' ] ) ) );

		}
		
		if ( isset( $_POST[ 'group_post_status' ] ) ) {
			
			update_post_meta( $post_id, 'bpgps_group_post_status', sanitize_text_field ( htmlentities($_POST[ 'group_post_status' ] ) ) );

		}



	}

}
endif;

/**

 * Boot the plugin.

 *

 * @since 1.0.0

 */

function bpps_activity() {

	return BPPS_Activity::start();

}

add_action( 'plugins_loaded', 'bpps_activity', 5 );	
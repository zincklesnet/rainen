<?php

if(!defined('ABSPATH')) {
	exit;
}

// @package bp-post-status


if ( ! class_exists( 'BPPS_Notifications' ) ) :


// BuddyPress Group Admin for BP Post Status

// Done: Add Notification Support for Friends and Group only posts
// Done: Add Notification Support for Members only posts
// Todo: Migrate group_only to group post with additional stored meta for security settings 
// Done: Rework group_post notifications so that members_olny and public notify site-wide.

class BPPS_Notifications {

	/**

	 * Plugin's main instance

	 *

	 * @var object

	 */

	protected static $instance;



	
	function __construct() {
	
	// Register BP Post Status with BP notifications
    add_filter( 'bp_notifications_get_registered_components', array( $this, 'bpps_filter_notifications_get_registered_components' ) );
    add_filter( 'bp_notifications_get_notifications_for_user', array( $this, 'bpps_post_format_buddypress_notifications' ), 11, 7 );
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

	public function bpps_filter_notifications_get_registered_components( $component_names = array() ) {
     
        // Force $component_names to be an array
        if ( ! is_array( $component_names ) ) {
            $component_names = array();
        }
     
        // Add 'custom' component to registered components array
        array_push( $component_names, 'bp-post-status' );
     
        // Return component's with 'custom' appended
        return $component_names;
    }


    public function bpps_post_format_buddypress_notifications( $action, $post_id, $secondary_item_id, $total_items, $format, $component_action_name, $component_name ) {
		
		// Prevent blank notifications - 17/04/2018
		if ($component_action_name !== 'bp_post_notif' && $component_action_name !== 'bp_post_approve' && $component_action_name !== 'bp_post_followed' && $component_action_name !== 'bp_post_following' ) {

			return $component_action_name;

		}
		
        // New custom notifications
        if ( 'bp_post_notif' === $component_action_name ) {
        
            $post = get_post( $post_id );
        
			if ( isset( $post->post_author ) ) {
				
				$post_title = bp_core_get_user_displayname( $post->post_author ) . esc_attr( esc_attr__( ' added the post ', 'bp-post-status' ) ) . $post->post_title;
				$post_link  = get_post_permalink( $post->ID );
				$post_text = bp_core_get_user_displayname( $post->post_author ) . esc_attr( esc_attr__( ' added the post ', 'bp-post-status' ) ) . $post->post_title;
			
			} else {
				
				return $component_action_name;
			}
	
            // WordPress Toolbar
            if ( 'string' === $format ) {
                $return = apply_filters( 'bp_post_notification_filter', '<a href="' . esc_url( $post_link ) . '" title="' . $post_title . '">' . $post_text . '</a>', $post_text, $post_link );
     
            // Deprecated BuddyBar
            } else {
                $return = apply_filters( 'bp_post_notification_filter', array(
                    'text' => $post_text,
                    'link' => $post_link
                ), $post_link, (int) $total_items, $post_text, $post_title );
            }
            
            return $return;
            
        }
        if ( 'bp_post_followed' === $component_action_name ) {
        
            $post = get_post( $post_id );
        
			if ( isset( $post->post_author ) ) {
				
				$post_title = bp_core_get_user_displayname( $post->post_author ) . esc_attr( esc_attr__( ' added the post ', 'bp-post-status' ) ) . $post->post_title;
				$post_link  = get_post_permalink( $post->ID );
				$post_text = bp_core_get_user_displayname( $post->post_author ) . esc_attr( esc_attr__( ' added the post ', 'bp-post-status' ) ) . $post->post_title;
			
			} else {
				
				return $component_action_name;
			}
	
            // WordPress Toolbar
            if ( 'string' === $format ) {
                $return = apply_filters( 'bp_post_notification_filter', '<a href="' . esc_url( $post_link ) . '" title="' . $post_title . '">' . $post_text . '</a>', $post_text, $post_link );
     
            // Deprecated BuddyBar
            } else {
                $return = apply_filters( 'bp_post_notification_filter', array(
                    'text' => $post_text,
                    'link' => $post_link
                ), $post_link, (int) $total_items, $post_text, $post_title );
            }
            
            return $return;
            
        }
        if ( 'bp_post_following' === $component_action_name ) {
        
            $post = get_post( $post_id );
        
			if ( isset( $post->post_author ) ) {
				
				$post_title = bp_core_get_user_displayname( $post->post_author ) . esc_attr( esc_attr__( ' added the post ', 'bp-post-status' ) ) . $post->post_title;
				$post_link  = get_post_permalink( $post->ID );
				$post_text = bp_core_get_user_displayname( $post->post_author ) . esc_attr( esc_attr__( ' added the post ', 'bp-post-status' ) ) . $post->post_title;
			
			} else {
				
				return $component_action_name;
			}
	
            // WordPress Toolbar
            if ( 'string' === $format ) {
                $return = apply_filters( 'bp_post_notification_filter', '<a href="' . esc_url( $post_link ) . '" title="' . $post_title . '">' . $post_text . '</a>', $post_text, $post_link );
     
            // Deprecated BuddyBar
            } else {
                $return = apply_filters( 'bp_post_notification_filter', array(
                    'text' => $post_text,
                    'link' => $post_link
                ), $post_link, (int) $total_items, $post_text, $post_title );
            }
            
            return $return;
            
        }
        // New custom notifications
        if ( 'bp_post_approve' === $component_action_name ) {
        
            $post = get_post( $post_id );
        
			if ( isset( $post->post_author ) ) {
				
				$post_title = bp_core_get_user_displayname( $post->post_author ) . esc_attr( esc_attr__( ' added a post for approval ', 'bp-post-status' ) ) . $post->post_title;
				if ( $post->post_status == 'members_only_pending' ) {
					$post_link  = admin_url() . 'edit.php?post_status=members_only_pending';
				} elseif ( $post->post_status == 'pending' ) {
					$post_link  = admin_url() . 'edit.php?post_status=pending';
				} else {
					$post_link  = admin_url() . 'edit.php?post_status=group_post_pending';
				}
				
				$post_text = bp_core_get_user_displayname( $post->post_author ) . esc_attr( esc_attr__( ' added a post for approval ', 'bp-post-status' ) ) . $post->post_title;
			
			} else {
				
				return $component_action_name;
			}
	
            // WordPress Toolbar
            if ( 'string' === $format ) {
                $return = apply_filters( 'bp_post_notification_filter', '<a href="' . esc_url( $post_link ) . '" title="' . $post_title . '">' . $post_text . '</a>', $post_text, $post_link );
     
            // Deprecated BuddyBar
            } else {
                $return = apply_filters( 'bp_post_notification_filter', array(
                    'text' => $post_text,
                    'link' => $post_link
                ), $post_link, (int) $total_items, $post_text, $post_title );
            }
            
            return $return;
            
        }
        
		return $component_action_name;
		
    }
	
    public static function bpps_add_notification( $post_id, $component, $activity_id = false ) {

		if ( ! bp_is_active( 'notifications' ) ) {
			
			return false;
			
		}
	
        $post = get_post( $post_id );
		$user_id = $post->post_author;
		
		if ( $component == 'groups' ) {
			
			if ( ! bp_is_active( 'groups' ) ) {
				
				return false;
				
			}
			
			$master_group_notif_enable = BP_Statuses_Admin::bpps_core_posts_notify_enabled( 'groups' );
			$user_can_notify = BP_Statuses_Admin::bpps_core_user_can_notify( 'groups' );

			if ( $user_can_notify && $master_group_notif_enable == 1 ) {


				$stored_meta = get_post_meta( $post_id );
				if ( isset( $stored_meta['bpgps_group'] ) ) {
					$group_id = $stored_meta['bpgps_group'];
				} else {
					return false;
				}
				
				
				// not working, not sure why??
				$current_group_lookup = BP_Statuses_Admin::bpps_core_group_lookup( $user_id, $group_id );
				
				$group_notiv_enable = 1;//$current_group_lookup[2];
				$notif_allowed = 1;//$current_group_lookup[3];
				
				
				if ( $group_notiv_enable && $notif_allowed ) {
					
					$stored_meta = get_post_meta( $post->ID );
					$post_status = $stored_meta['bpgps_group_post_status'];
					if ( $post_status == 'publish' || $post_status == 'members_only' ) {
						$members = bp_core_get_users();
						$members = $members['users'];
					} else {
						$args = array( 
							'group_id' => $group_id,
							'exclude_admins_mods' => false
						);
						$group_members = groups_get_group_members( $args );
						$members = $group_members['members'];
					}
					
					foreach(  $group_members as $member ) {
							
						if ( $member->ID != $user_id ) {

				bp_notifications_add_notification( array(
								'user_id' 			=> $member->ID,
								'item_id' 			=> $post->ID,
								'secondary_item_id' => $post->post_author,
								'component_name' 	=> 'bp-post-status',
								'component_action' 	=> 'bp_post_notif',
								'date_notified' 	=> bp_core_current_time(),
								'is_new' 			=> 1,
							));
						}
					}

					return true;

				} else {

					return false;
						
				}
				
			} else {
					
					return false;
					
			}			
			
		} else if ( $component == 'friends' ) {

			if ( ! bp_is_active( 'friends' ) ) {
				
				return false;
				
			}
			
			$friends_settings = get_option( "bpps_friends_settings" );

			if ( isset( $friends_settings['friends_notif_enable'] ) ) {
				
				$master_friends_notif_enable = $friends_settings['friends_notif_enable'];
			
			} else {
				
				$$master_friends_notif_enable = 0;
			
			}
			
					
			if ( isset( $friends_settings['friends_notif_cap'] ) ) {
				
				$friends_can_notify = current_user_can( $friends_settings['friends_notif_cap'] );
			
			} else {
				
				$friends_can_notify = 0;
			
			}
			
			if ( $friends_can_notify && $master_friends_notif_enable ) {


				$friends = friends_get_friend_user_ids( $user_id );
				
				foreach ( $friends as $friend ) {
						
					if ( $friend != $user_id ) {

						bp_notifications_add_notification(array(
							'user_id' 			=> $friend,
							'item_id' 			=> $post->ID,
							'secondary_item_id' => $post->post_author,
							'component_name' 	=> 'bp-post-status',
							'component_action' 	=> 'bp_post_notif',
							'date_notified' 	=> bp_core_current_time(),
							'is_new' 			=> 1,
						));
					}
				}		
					
					return true;
					
			} else {
				
				return false;
			}

			
		} else if ( $component == 'members' ) {
			
			$members_settings = get_option( "bpps_members_settings" );
			
			if ( isset( $members_settings['members_notif_enable'] ) ) {
				
				$master_members_notif_enable = $members_settings['members_notif_enable'];
			
			} else {
				
				$master_members_notif_enable = 0;
			
			}
			
					
			if ( isset( $members_settings['members_notif_cap'] ) ) {
				
				$members_can_notify = current_user_can( $members_settings['members_notif_cap'] );
			
			} else {
				
				$members_can_notify = 0;
			
			}
			
			if ( $members_can_notify && $master_members_notif_enable ) {
				
				$members = bp_core_get_users();
				$members = $members['users'];
				foreach ( $members as $member ) {
					if ( $member->ID != $user_id ) {

						bp_notifications_add_notification(array(
							'user_id' 			=> $member->ID,
							'item_id' 			=> $post->ID,
							'secondary_item_id' => $post->post_author,
							'component_name' 	=> 'bp-post-status',
							'component_action' 	=> 'bp_post_notif',
							'date_notified' 	=> bp_core_current_time(),
							'is_new' 		=> 1,
						));
					}
				}
					
			} else {
				
				return false;
				
			}
				
		} else if ( $component == 'following' ) {
			
			$following_settings = get_option( "bpps_following_settings" );
			
			if ( isset( $following_settings['following_notif_enable'] ) ) {
				
				$master_following_notif_enable = $following_settings['following_notif_enable'];
			
			} else {
				
				$master_following_notif_enable = 0;
			
			}
			
					
			if ( isset( $following_settings['following_notif_cap'] ) ) {
				
				$following_can_notify = current_user_can( $following_settings['following_notif_cap'] );
			
			} else {
				
				$following_can_notify = 0;
			
			}
			
			if ( $following_can_notify && $master_following_notif_enable ) {
				
				$following = bp_follow_get_following ( array('user_id' => $user_id) );

				foreach ( $following as $followee ) {
					if ( $followee != $user_id ) {

						bp_notifications_add_notification(array(
							'user_id' 			=> $followee,
							'item_id' 			=> $post->ID,
							'secondary_item_id' => $post->post_author,
							'component_name' 	=> 'bp-post-status',
							'component_action' 	=> 'bp_post_following',
							'date_notified' 	=> bp_core_current_time(),
							'is_new' 		=> 1,
						));
					}
				}
					
			} else {
				
				return false;
				
			}
				
		} else if ( $component == 'followed' ) {
			
			$followed_settings = get_option( "bpps_followed_settings" );
			
			if ( isset( $followed_settings['followed_notif_enable'] ) ) {
				
				$master_followed_notif_enable = $followed_settings['followed_notif_enable'];
			
			} else {
				
				$master_followed_notif_enable = 0;
			
			}
			
					
			if ( isset( $followed_settings['followed_notif_cap'] ) ) {
				
				$followed_can_notify = current_user_can( $followed_settings['followed_notif_cap'] );
			
			} else {
				
				$followed_can_notify = 0;
			
			}
			
			if ( $followed_can_notify && $master_followed_notif_enable ) {
				
				$followed = bp_follow_get_followers( $user_id );

				foreach ( $followed as $follow ) {
					if ( $follow != $user_id ) {

						bp_notifications_add_notification(array(
							'user_id' 			=> $follow,
							'item_id' 			=> $post->ID,
							'secondary_item_id' => $post->post_author,
							'component_name' 	=> 'bp-post-status',
							'component_action' 	=> 'bp_post_followed',
							'date_notified' 	=> bp_core_current_time(),
							'is_new' 		=> 1,
						));
					}
				}
					
			} else {
				
				return false;
				
			}
				
		} else if ( $component == 'approval' ) {
			
			if ( $post->post_status == 'group_post_pending' ) {
				
				if ( ! bp_is_active( 'notifications' ) ) {
					
					return false;
					
				}
				
				$stored_meta = get_post_meta( $post_id );
				if ( isset( $stored_meta['bpgps_group'] ) ) {
					$group_id = $stored_meta['bpgps_group'];
				} else {
					return false;
				}

				$group_data = groups_get_group( $group_id[0] );
				
				$approver_id = $group_data->creator_id;
				
				if ( empty( $approver_id ) ) {
					goto no_group_admin;
				}
				
				bp_notifications_add_notification(array(
					'user_id' 			=> $approver_id,
					'item_id' 			=> $post->ID,
					'secondary_item_id' => $post->post_author,
					'component_name' 	=> 'bp-post-status',
					'component_action' 	=> 'bp_post_approve',
					'date_notified' 	=> bp_core_current_time(),
					'is_new' 			=> 1,
				));
				
				no_group_admin:

				$approvers = get_users(array( 'role'=>'administrator'));
				
				foreach( $approvers as $user ) {
					
					if ( $approver_id == $user->ID ) {
						
						continue;
					
					}
					
					bp_notifications_add_notification(array(
						'user_id' 			=> $user->ID,
						'item_id' 			=> $post->ID,
						'secondary_item_id' => $post->post_author,
						'component_name' 	=> 'bp-post-status',
						'component_action' 	=> 'bp_post_approve',
						'date_notified' 	=> bp_core_current_time(),
						'is_new' 		=> 1,
					));
					
				}
				
			} else if ( $post->post_status == 'members_only_pending' ) {

				$approvers = get_users(array( 'role'=>'administrator'));
				
				foreach( $approvers as $user ) {

					bp_notifications_add_notification(array(
						'user_id' 			=> $user->ID,
						'item_id' 			=> $post->ID,
						'secondary_item_id' => $post->post_author,
						'component_name' 	=> 'bp-post-status',
						'component_action' 	=> 'bp_post_approve',
						'date_notified' 	=> bp_core_current_time(),
						'is_new' 		=> 1,
					));
					
				}
			
				
			} else if ( $post->post_status == 'pending' ) {

				$approvers = get_users(array( 'role'=>'administrator'));
				
				foreach( $approvers as $user ) {

					bp_notifications_add_notification(array(
						'user_id' 			=> $user->ID,
						'item_id' 			=> $post->ID,
						'secondary_item_id' => $post->post_author,
						'component_name' 	=> 'bp-post-status',
						'component_action' 	=> 'bp_post_approve',
						'date_notified' 	=> bp_core_current_time(),
						'is_new' 		=> 1,
					));
					
				}
				
			}

		}
		
	}		

}
endif;

/**

 * Boot the plugin.

 *

 * @since 1.0.0

 */

function bpps_notifications() {

	return BPPS_Notifications::start();

}

add_action( 'plugins_loaded', 'bpps_notifications', 5 );	
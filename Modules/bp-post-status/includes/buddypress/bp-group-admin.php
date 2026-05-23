<?php

if(!defined('ABSPATH')) {
	exit;
}

// @package bp-post-status


if ( ! class_exists( 'BPPS_Group_Admin' ) ) :


// BuddyPress Group Admin for BP Post Status

// Done: Create Group setup page/integrate into existing page.
// Done: Create Group management page/integrate into existing page.
// Todo: Blog Categories for Groups integration
// Done: Enable/Disable private group posts
// Done: Set post creation for groups authorization levels
// Done: Set post notification authorization levels for sending notifications
// Todo: Add check to see if Group options are enabled in sitewide settings
// Todo: Add option to disable Group Posts Nav

class BPPS_Group_Admin {

	/**

	 * Plugin's main instance

	 *

	 * @var object

	 */

	protected static $instance;



	
	function __construct() {
		
		add_action( 'groups_group_settings_edited', array(  $this, 'bpps_save_group_prefs') );
//		add_action( 'groups_create_group', array( $this, 'bpps_save_group_prefs' ) );
		add_action( 'groups_update_group', array( $this, 'bpps_save_group_prefs' ) );
		add_action( 'bp_before_group_settings_admin', array( $this, 'bpps_group_settings_form' ) );
//		add_action( 'bp_before_group_settings_creation_step', array( $this, 'bpps_group_settings_form' ) );

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

	 * Update and save group preference.

	 *

	 * @since 1.0.0

	 *

	 * @return object A single instance of this class.

	 */

	function bpps_save_group_prefs( $group_id ) {
		
		$disable = isset( $_POST['group-disable-bpps'] ) ? 1: 0;
		$disable = esc_attr( $disable );

		groups_update_groupmeta( $group_id, 'bpps_is_disabled', $disable ); //save preference
		

	}

	/**

	 * Add a setting to enable and assign group posts to the group.

	 *

	 * @since 1.0.0

	 *

	 * @return object A single instance of this class.

	 */

	public function bpps_group_settings_form () {
		
		?>
		
		<div class="bpps-disable">
			
			<label><input type="checkbox" name="group-disable-bpps" id="group-disable-bpps" value="1" <?php if ( bpps_is_disabled_for_group() ): ?> checked="checked"<?php endif; ?>> <?php esc_attr_e( 'Disable Group Only Posts', 'bp-post-status' ) ?></label>
		
		</div>
		
		<?php
	}

	/**

	 * Get Group email approver address.

	 *

	 * @since 1.3.0

	 * @global type $bp

	 * @return email address

	 */
	 
	public static function group_approver_is_selected() {
		
		$bp = buddypress();
		
		$group_id = false;
		
		if ( bp_is_group_create() ) {
			
			$group_id = $_COOKIE['bp_new_group_id'];
		
		} elseif ( bp_is_group() ) {
			
			$group_id = bp_get_current_group_id();
		
		}
		
		$group_data = groups_get_group( $group_id );
		$creator_id = $group_data->creator_id;
		$creator_details = get_userdata( $creator_id );
		$group_creator_email = $creator_details->user_email;
		$approver_email = esc_attr( groups_get_groupmeta( $group_id, 'bpps_approver_email' ) );
		
		if ( isset( $approver_email ) ) {
			
			return $approver_email;
		
		} else {
			
			return $group_creator_email;
		
		}
		
	}

	/**

	 * Get post content setting for current group.

	 *

	 * @since 1.2.2

	 * @global type $bp

	 * @return type

	 */
	 
	public static function post_content_is_selected() {
		
		$bp = buddypress();
		
		$group_id = false;
		
		if ( bp_is_group_create() ) {
			$group_id = $_COOKIE['bp_new_group_id'];
		} elseif ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		}
		
		return apply_filters( 'bpps_post_content_selection', BPPS_Group_Admin::post_is_set( $group_id ) );
	}


	/**

	 * Check if group only post content is set for the current group.

	 *

	 * @since 1.2.2

	 * @param $group_id

	 * @return string[content setting]

	 */

	public static function post_is_set( $group_id ) {
	
		$post_selection = groups_get_groupmeta( $group_id, 'bpps_post_content' );
		
		return apply_filters( 'bpps_post_set', $post_selection, $group_id );
	}

	/**

	 * Get activity content setting for current group.

	 *

	 * @since 1.2.2

	 * @global type $bp

	 * @return type

	 */
	 
	public static function activity_content_is_selected() {
		
		$bp = buddypress();
		
		$group_id = false;
		
		if ( bp_is_group_create() ) {
			$group_id = $_COOKIE['bp_new_group_id'];
		} elseif ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		}
		
		return apply_filters( 'bpps_activity_content_selection', BPPS_Group_Admin::activity_is_set( $group_id ) );
	}


	/**

	 * Check if group only post activity content is set for the current group.

	 *

	 * @since 1.2.2

	 * @param $group_id

	 * @return string[content setting]

	 */

	public static function activity_is_set( $group_id ) {
	
		$activity_selection = groups_get_groupmeta( $group_id, 'bpps_activity_content' );
		
		return apply_filters( 'bpps_activity_set', $activity_selection, $group_id );
	}

	/**

	 * Get group ID for post notifier role for current group.

	 *

	 * @since 1.0.0

	 * @global type $bp

	 * @return type

	 */
	 
	public static function notifier_type_is_selected() {
		
		$bp = buddypress();
		
		$group_id = false;
		
		if ( bp_is_group_create() ) {
			$group_id = $_COOKIE['bp_new_group_id'];
		} elseif ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		}
		
		return apply_filters( 'bpps_notifier_type_selection', BPPS_Group_Admin::notifier_is_set( $group_id ) );
	}

	/**

	 * Check if group only post notifier role is set for the current group.

	 *

	 * @since 1.0.0

	 * @param $group_id

	 * @return string[role]

	 */

	public static function notifier_is_set( $group_id ) {
	
		$notifier_selection = groups_get_groupmeta( $group_id, 'bpps_notifier' );
		
		return apply_filters( 'bpps_notifier_set', $notifier_selection, $group_id );
	}

	/**

	 * Get group ID for notifications settings check

	 *

	 * @since 1.0.0

	 * @global type $bp

	 * @return type

	 */
	 
	public static function bpps_notif_group_lookup() {
		
		$bp = buddypress();
		
		$group_id = false;
		
		if ( bp_is_group_create() ) {
			$group_id = $_COOKIE['bp_new_group_id'];
		} elseif ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		}
		
		return apply_filters( 'bpps_notif_disabled_for_group', BPPS_Group_Admin::bpps_notif_is_disabled( $group_id ) );
	}

	/**

	 * Check if group only post notifications are disabled for the current group.

	 *

	 * @since 1.0.0

	 * @param $group_id

	 * @return bool|mixed|void

	 */

	public static function bpps_notif_is_disabled( $group_id ) {
	
		if ( empty( $group_id ) ) {
			return false; //if group id is empty, it is active
		}
		
		$notif_disabled = groups_get_groupmeta( $group_id, 'bpps_notif_active' );
		
		return apply_filters( 'bpps_notif_is_disabled', intval( $notif_disabled ), $group_id );
	}

	/**

	 * Check if Group Posts nav is disabled for the current group.

	 *

	 * @since 1.0.1

	 * @global type $bp

	 * @return type

	 */
	 
	public static function bpps_nav_is_disabled_for_group() {
		
		$bp = buddypress();
		
		$group_id = false;
		
		if ( bp_is_group_create() ) {
			$group_id = $_COOKIE['bp_new_group_id'];
		} elseif ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		}
		
		return apply_filters( 'bpps_nav_is_disabled_for_group', BPPS_Group_Admin::bpps_nav_is_disabled( $group_id ) );
	}

	/**

	 * Check if group Home is enabled for the current group.

	 *

	 * @since 1.2.0

	 * @global type $bp

	 * @return type

	 */
	 
	public static function bpps_home_is_disabled_for_group() {
		
		$bp = buddypress();
		
		$group_id = false;
		
		if ( bp_is_group_create() ) {
			$group_id = $_COOKIE['bp_new_group_id'];
		} elseif ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		}
		
		return apply_filters( 'bpps_home_is_edisabled_for_group', BPPS_Group_Admin::bpps_home_is_disabled( $group_id ) );
	}

	/**

	 * Check if group Home is enabled for the current group.

	 *

	 * @since 1.2.0

	 * @param $group_id

	 * @return bool|mixed|void

	 */

	public static function bpps_home_is_disabled( $group_id ) {
	
		if ( empty( $group_id ) ) {
			return false; //if group id is empty, it is active
		}
		
		$home_disabled = groups_get_groupmeta( $group_id, 'bpps_home_disabled' );
		
		return apply_filters( 'bpps_home_is_disabled', intval( $home_disabled ), $group_id );
	}
	
	/**

	 * Check if group posts is disabled for the current group.

	 *

	 * @since 1.0.0

	 * @param $group_id

	 * @return bool|mixed|void

	 */

	public static function bpps_nav_is_disabled( $group_id ) {
	
		if ( empty( $group_id ) ) {
			return false; //if group id is empty, it is active
		}
		
		$is_disabled = groups_get_groupmeta( $group_id, 'bpps_group_nav_is_disabled' );
		
		return apply_filters( 'bpps_nav_is_disabled', intval( $is_disabled ), $group_id );
	}


	/**

	 * Get group ID for post creator role for current group.

	 *

	 * @since 1.0.0

	 * @global type $bp

	 * @return type

	 */
	 
	public static function creator_type_is_selected() {
		
		$bp = buddypress();
		
		$group_id = false;
		
		if ( bp_is_group_create() ) {
			$group_id = $_COOKIE['bp_new_group_id'];
		} elseif ( bp_is_group() ) {
			$group_id = bp_get_current_group_id();
		}
		
		return apply_filters( 'bpps_creator_type_selection', BPPS_Group_Admin::creator_is_set( $group_id ) );
	}

	/**

	 * Check if group only post creator role is set for the current group.

	 *

	 * @since 1.0.0

	 * @param $group_id

	 * @return string[role]

	 */

	public static function creator_is_set( $group_id ) {
	
		$creator_selection = groups_get_groupmeta( $group_id, 'bpps_creator' );
		
		return apply_filters( 'bpps_creator_set', $creator_selection, $group_id );
	}

}
endif;

/**

 * Boot the plugin.

 *

 * @since 1.0.0

 */

function bpps_group_admin() {

	return BPPS_Group_Admin::start();

}

$group_settings = get_option( "bpps_groups_settings" );

if ( isset( $group_settings["groups_disable"] ) ) {
	
	$group_posts_disable = $group_settings["groups_disable"];

} else {
	
	$group_posts_disable = '';
	
}

if ( ! $group_posts_disable ) {	
	
	add_action( 'plugins_loaded', 'bpps_group_admin', 5 );

}
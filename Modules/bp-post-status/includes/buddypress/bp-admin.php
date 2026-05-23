<?php
/*
 * @package bp-post-status
 *
 * BP Admin - set up group posts settings section in Group manage tab
 *
 * Since 1.2.3
 *
*/


if ( ! bp_is_active( 'groups' ) ) {
	return ;//do not load further
}

$bpps_disabled = bpps_is_disabled_for_group();

if ( $bpps_disabled == 1 ) {
	return;
}

//handle everything except the front end display

class BPPS_Group_Extension extends BP_Group_Extension {

	public $visibility = 'public'; // 'public' will show your extension to non-group members, 'private' means you have to be a member of the group to view your extension.
	public $enable_create_step = true; // enable create step
	public $enable_nav_item = false; //do not show in front end
	public $enable_edit_item = true;
	public $bpps_disabled;


	public function __construct () {
		$this->bpps_disabled = bpps_is_disabled_for_group();
		if ( $this->bpps_disabled == 1 ) {
			$this->enable_edit_item = false;
		}
		$this->name = esc_attr__( 'Group Posts', 'bp-post-status' );
		$this->slug = 'group-posts-admin';

		$this->create_step_position = 21;
		$this->nav_item_position = 31;
		
		do_action_ref_array( 'bpps_created_group_extension', array( &$this ) );
	}

//on group crate step
	public function create_screen( $group_id = null ) {

		if ( ! bp_is_group_creation_step( $this->slug ) ) {
			return false;
		}

		bpps_admin_form();

		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

//on group create save
	public function create_screen_save( $group_id = null ) {
		$bp = buddypress();

		check_admin_referer( 'groups_create_save_' . $this->slug );

		$group_id = $bp->groups->new_group_id;
		
		$nav_disable = isset( $_POST['group-disable-nav'] ) ? 1: 0;
		$nav_disable = esc_attr( $nav_disable );

		groups_update_groupmeta( $group_id, 'bpps_group_nav_is_disabled', $nav_disable ); //save preference

		$notif_enable = isset( $_POST['group-notif-bpps'] ) ? 1: 0;
		$notif_enable = esc_attr( $notif_enable );

		groups_update_groupmeta( $group_id, 'bpps_notif_active', $notif_enable ); //save preference
		
		$creator_role = $_POST['creator_role'];
		
		if (isset( $creator_role ) ) {
			
			$creator_role = esc_attr( $creator_role );
			groups_update_groupmeta( $group_id, 'bpps_creator', $creator_role );
		
		}
		
		$notifier_role = $_POST['notifier_role'];
		
		if (isset( $notifier_role ) ) {
			
			$creator_role = esc_attr( $notifier_role );
			groups_update_groupmeta( $group_id, 'bpps_notifier', $notifier_role );
		
		}
		
		$activity_content = $_POST['activity_content'];
		
		if (isset( $activity_content ) ) {
			
			$activity_content = esc_attr( $activity_content );
			groups_update_groupmeta( $group_id, 'bpps_activity_content', $activity_content );
		
		}

		$post_content = $_POST['post_content'];
		
		if (isset( $post_content ) ) {
			
			$post_content = esc_attr( $post_content );
			groups_update_groupmeta( $group_id, 'bpps_post_content', $post_content );
		
		}
		
		$approver_email = esc_attr( $_POST['group_approver_email'] );
		
		if (isset( $approver_email ) ) {
			
			groups_update_groupmeta( $group_id, 'bpps_approver_email', $approver_email );
		
		}

		$home_disable = isset( $_POST['group-disable-home'] ) ? 1: 0;
		$home_disable = esc_attr( $home_disable );

		groups_update_groupmeta( $group_id, 'bpps_home_disabled', $home_disable ); //save preference
	
		bp_core_add_message( esc_attr__( 'Group Post settings were successfully updated.', 'bp-post-status' ) );

	}

	public function edit_screen( $group_id = null ) {
		
		if ( ! bp_is_group_admin_screen( $this->slug ) ) {
			return false;
		}
		
		?>

		<h2><?php echo esc_attr( $this->name ) ?></h2>
		
		<?php
			bpps_admin_form();
			wp_nonce_field( 'groups_edit_save_' . $this->slug );
		?>
		<p><input type="submit" value="<?php esc_attr_e( 'Save Changes', 'bp-post-status' ) ?> &rarr;" id="save" name="save" /></p>
	<?php
	}

	public function edit_screen_save( $group_id = null ) {
		
		$bp = buddypress();
		
		if ( ! isset( $_POST['save'] ) ) {
			return false;
		}

		check_admin_referer( 'groups_edit_save_' . $this->slug );

		$nav_disable = isset( $_POST['group-disable-nav'] ) ? 1: 0;
		$nav_disable = esc_attr( $nav_disable );

		groups_update_groupmeta( $group_id, 'bpps_group_nav_is_disabled', $nav_disable ); //save preference

		$notif_enable = isset( $_POST['group-notif-bpps'] ) ? 1: 0;
		$notif_enable = esc_attr( $notif_enable );

		groups_update_groupmeta( $group_id, 'bpps_notif_active', $notif_enable ); //save preference
		
		$creator_role = $_POST['creator_role'];
		
		if (isset( $creator_role ) ) {
			
			$creator_role = esc_attr( $creator_role );
			groups_update_groupmeta( $group_id, 'bpps_creator', $creator_role );
		
		}
		
		$notifier_role = $_POST['notifier_role'];
		
		if (isset( $notifier_role ) ) {
			
			$creator_role = esc_attr( $notifier_role );
			groups_update_groupmeta( $group_id, 'bpps_notifier', $notifier_role );
		
		}
		
		$activity_content = $_POST['activity_content'];
		
		if (isset( $activity_content ) ) {
			
			$activity_content = esc_attr( $activity_content );
			groups_update_groupmeta( $group_id, 'bpps_activity_content', $activity_content );
		
		}

		$post_content = $_POST['post_content'];
		
		if (isset( $post_content ) ) {
			
			$post_content = esc_attr( $post_content );
			groups_update_groupmeta( $group_id, 'bpps_post_content', $post_content );
		
		}

		$approver_email = esc_attr( $_POST['group_approver_email'] );
		
		if (isset( $approver_email ) ) {
			
			groups_update_groupmeta( $group_id, 'bpps_approver_email', $approver_email );
		
		}

		$home_disable = isset( $_POST['group-disable-home'] ) ? 1: 0;
		$home_disable = esc_attr( $home_disable );

		groups_update_groupmeta( $group_id, 'bpps_home_disabled', $home_disable ); //save preference
	
		bp_core_add_message( esc_attr__( 'Group Posts settings were successfully updated.', 'bp-post-status' ) );

		bp_core_redirect( bp_get_group_url( $bp->groups->current_group ) . '/admin/' . $this->slug );
	}

	public function display ( $group_id = null ) {
		/* Use this function to display the actual content of your group extension when the nav item is selected */
	}

	public function widget_display ( $group_id = null ) {
		
	}

}

//if ( ! bpps_is_disabled_for_group() ) {
	bp_register_group_extension( 'BPPS_Group_Extension' );
//}
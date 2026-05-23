<?php

if ( ! class_exists( 'BP_Group_Extension' ) ) {
	return ;
}


class BP_Group_Customizer_Lite extends BP_Group_Extension {

	public $visibility = 'private';
	// 'public' will show your extension to non-group members,
	// 'private' means you have to be a member of the group to view your extension.

	public $enable_create_step = true; // enable create step
	public $enable_nav_item = false; //do not show in front end
	public $enable_edit_item = true; // If your extensi

	public function __construct() {

		$this->name = __( 'Appearances', 'bp-group-customizer-lite' );
		$this->slug = 'appearances';

		$this->create_step_position = 22;
		$this->nav_item_position    = 33;
	}

//on group crate step
	public function create_screen( $group_id = false ) {

		if ( ! bp_is_group_creation_step( $this->slug ) ) {
			return false;
		}

		$this->render_form();//render the form
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	//on group create save
	public function create_screen_save( $group_id = false ) {
		global $bp;

		check_admin_referer( 'groups_create_save_' . $this->slug );

		$group_id = $bp->groups->new_group_id;

		if ( ! $this->upload_save( $group_id ) ) {
			bp_core_add_message( __( 'There was an error updating the background, please try again.', 'bp-group-customizer-lite' ), 'error' );
		} else {
			bp_core_add_message( __( 'Background updated successfully.', 'bp-group-customizer-lite' ) );
		}
	}

	public function edit_screen( $group_id = false ) {

		if ( ! bp_is_group_admin_screen( $this->slug ) ) {
			return false;
		}
		?>

		<h2><?php echo esc_attr( $this->name ) ?></h2>
		<?php
		//show the current uploaded photo and allow to download
		$image = bgclite_get_image( false, 'thumbnail' );
		if ( ! empty( $image ) ): ?>
			<div id="bg-delete-wrapper">

				<div class="current-bg">
					<img src="<?php echo $image; ?>" alt="current background"/>
				</div>
				<a href='#' id='bpgclite-del-image'><?php _e( 'Delete', 'bp-group-customizer-lite' ); ?></a>
			</div>
		<?php endif; ?>
		<?php $this->render_form(); ?>

		<?php wp_nonce_field( 'groups_edit_save_' . $this->slug ); ?>
		<p><input type="submit" value="<?php _e( 'Save Changes', 'bp-group-customizer-lite' ) ?> &rarr;" id="save"
		          name="save"/></p>
		<?php
	}

	public function edit_screen_save( $group_id = false ) {
		global $bp;

		if ( ! isset( $_POST['save'] ) ) {
			return false;
		}

		check_admin_referer( 'groups_edit_save_' . $this->slug );


		$group_id = $bp->groups->current_group->id;

		if ( ! $this->upload_save( $group_id ) ) {
			bp_core_add_message( __( 'There was an error updating the background, please try again.', 'bp-group-customizer-lite' ), 'error' );
		} else {
			bp_core_add_message( __( 'Background updated successfully.', 'bp-group-customizer-lite' ) );
		}

		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
	}

	public function display( $group_id = false ) {
		/* Use this function to display the actual content of your group extension when the nav item is selected */
	}

	public function widget_display( $group_id = false ) {

	}

	public function upload_save( $group_id ) {
		$action  = 'groups_edit_save_' . $this->slug;//for upload noce check
		$success = false;
		//we need it
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );

		$post_data  = array();
		$override   = array( 'test_form' => false, 'action' => $action );
		$attachment = media_handle_upload( 'group_background', 0, $post_data, $override );

		if ( ! is_wp_error( $attachment ) ) {
			//delete older attachment
			$helper = bgclite_get_helper();
			$helper->delete_background( $group_id );
			groups_update_groupmeta( $group_id, 'background_attachment', $attachment );
			$success = true;
		}

		return $success;


	}

	//the actual form
	public function render_form() {
		?>
		<fieldset class="group_background-upload">

			<legend>
				<?php _e( 'Upload Background', 'bp-group-customizer-lite' ); ?>
			</legend>
			<p>
				<input type="file" name="group_background" id="group_background"/>
			</p>

		</fieldset>
		<?php
	}
}

bp_register_group_extension( 'BP_Group_Customizer_Lite' );


<?php

/**
 * Plugin Name: BP Group Cutomizer Lite
 * Plugin URI: https://buddydev.com/plugins/bp-group-customizer-lite/
 * Author: Brajesh Singh
 * Author URI: https://buddydev.com/members/sbrajesh/
 * Description: Allows you to customize/change the background of BuddyPress Groups
 * Version: 1.0.1
 * License: GPL
 *
 */
class BP_Group_Customizer_Lite_Helper {
	private static $instance;

	private $path;
	private $url ;


	private function __construct() {

		$this->path = plugin_dir_path( __FILE__ );
		$this->url = plugin_dir_url( __FILE__ );

		add_action( 'bp_enqueue_scripts', array( $this, 'inject_css' ) );
		add_action( 'bp_enqueue_scripts', array( $this, 'inject_js' ) );

		//modify body class to account for single group,since bp overwrites wp body classes,we need to hook to bp
		add_filter( 'bp_get_the_body_class', array( $this, 'add_body_class' ) );

		//load textdomain
		add_action( 'bp_init', array( $this, 'load_textdomain' ), 2 );
		//load group extension
		add_action( 'bp_init', array( $this, 'load_extension' ) );

		//ajax
		add_action( 'wp_ajax_bpgclite_del_image', array( $this, 'ajax_delete_image' ) );
	}

	/**
	 * Get singleton instance
	 * @return self instance
	 */
	public static function get_instance() {

		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	//load group extension
	public function load_extension() {

		if ( bp_is_active( 'groups' ) ) {
			include_once( $this->path . 'bp-customizer-extension.php' );
		}
	}

	//translation
	public function load_textdomain() {

	}

	//inject css in page header
	public function inject_css() {

		$group_id = bp_get_current_group_id();

		if ( empty( $group_id ) ) {
			return;
		}

		$image = bgclite_get_image( $group_id );

		if ( empty( $image ) || apply_filters( 'bp_group_customizer_iwilldo_it_myself', false ) ) {
			return;
		}
		$selectors = apply_filters( 'bpgclite-css-selectors', 'body.is-single-group');

		?>
		<style type="text/css">
			<?php echo $selectors;?>{
				background: url(<?php echo $image;?>);
			}
		</style>
		<?php

	}

	//inject js if I am viewing the group/admin/appearances tab
	public function inject_js() {

		$bp = buddypress();

		if ( bp_is_group() && bp_is_group_admin_page() && $bp->action_variables[0] == 'appearances' ) {
			wp_enqueue_script( 'bpgclite-js', $this->url . 'js/group-customizer-lite.js', array( 'jquery' ) );
		}
	}


	//inject custom class on body element for single group pages

	public function add_body_class( $classes ) {

		$group = groups_get_current_group();

		if ( ! empty( $group ) ) {
			$classes[] = 'is-single-group';
		}

		return $classes;


	}



	/**
	 * Delete attachment and clean the files
	 *
	 * @param type $group_id
	 *
	 * @return type
	 */
	public function delete_background( $group_id ) {

		$attachment = groups_get_groupmeta( $group_id, 'background_attachment' );
		if ( empty( $attachment ) ) {
			return false;
		}

		wp_delete_attachment( $attachment, true );

	}

	/**
	 * Delete via ajax
	 */

	public function ajax_delete_image() {

		//validate nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'groups_edit_save_appearances' ) ) {
			die( 'what!' );
		}
		$group = bp_get_current_group_id();
		self::delete_background( $group );
		$message = '<p>' . __( 'Background image deleted successfully!', 'bp-group-customizer-lite' ) . '</p>';//feedback but we don't do anything with it yet, should we do something
		echo $message;
		exit( 0 );

	}
}//end of class
//
/**
 *
 * @param type $group_id
 * @param type $type
 *
 * @return string, the absolute url of the uploaded image
 */
function bgclite_get_image( $group_id = false, $type = 'full' ) {

	if ( empty( $group_id ) ) {
		$group_id = bp_get_current_group_id();
	}

	$attachment_id = bgclite_get_attachment_id( $group_id );
	$image         = wp_get_attachment_image_src( $attachment_id, $type );

	if ( ! empty( $image ) ) {
		return $image[0];
	}

	return false;

}

/**
 *
 * @param int $group_id
 *
 * @return int : the attachment id
 */
function bgclite_get_attachment_id( $group_id = false ) {

	if ( empty( $group_id ) ) {
		$group_id = bp_get_current_group_id();
	}

	$attachment_id = groups_get_groupmeta( $group_id, 'background_attachment' );

	return $attachment_id;

}
/**
 *
 * @return BP_Group_Customizer_Lite_Helper
 */
function bgclite_get_helper() {
	return BP_Group_Customizer_Lite_Helper::get_instance();
}


//instantiate helper
bgclite_get_helper();


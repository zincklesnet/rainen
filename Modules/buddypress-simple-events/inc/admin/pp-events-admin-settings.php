<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings Page class
 */


class PP_Simple_Events_Admin_Settings {

	private $roles_message = '';
	private $settings_message = '';

    public function __construct() {

		if ( is_multisite() ) {

			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}

        }

        if ( is_multisite() && is_plugin_active_for_network( 'buddypress-simple-events/loader.php' ) ) {
			add_action('network_admin_menu', array( $this, 'multisite_admin_menu' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		add_action( 'admin_enqueue_scripts',  array( $this, 'ppse_admin_enqueue' ), 100 );
	}


	function admin_menu() {
		add_options_page(  __( 'BP Simple Events', 'bp-simple-events'), __( 'BP Simple Events', 'bp-simple-events' ), 'manage_options', 'bp-simple-events', array( $this, 'settings_admin_screen' ) );
	}

	function multisite_admin_menu() {
		add_submenu_page( 'settings.php', __( 'BP Simple Events', 'bp-simple-events'), __( 'BP Simple Events', 'bp-simple-events' ), 'manage_options', 'bp-simple-events', array( $this, 'settings_admin_screen' ) );
	}

	function ppse_admin_enqueue( $hook ) {

		if ( 'settings_page_bp-simple-events' != $hook ) {
			return;
		}

		$gapikey 		= get_site_option( 'pp_gapikey' );
		$skip_google 	= get_option( 'pp_skip_google' );

		if ( $gapikey != false && ! $skip_google ) {
			wp_register_script( 'google-places-api', '//maps.googleapis.com/maps/api/js?key=' . $gapikey . '&libraries=places' );
			wp_print_scripts( 'google-places-api' );
		}

		wp_enqueue_script( 'ppse_location_script', plugins_url( 'js/events_google_location.js', dirname(__FILE__) ), array('jquery'), '4.2' );

	}



	function settings_admin_screen() {
		global $wp_roles;

		if ( !is_super_admin() ) {
			return;
		}

		$this->roles_update();
		$this->settings_update();

		$all_roles = $wp_roles->roles;

		$gapikey = get_site_option( 'pp_gapikey' );
		if ( ! $gapikey ) {
			$gapikey = "Paste Your Key Here";
		}

		$display_image	= get_option( 'pp_events_display_image' );

		$skip_google	= get_option( 'pp_skip_google' );

		?>

		<h3>BP Simple Events Settings</h3>

		<table border="0" cellspacing="10" cellpadding="10">
		<tr>
		<td style="vertical-align:top; border: 1px solid #ccc;" >

			<h3><?php _e('Assign User Roles', 'bp-simple-events'); ?></h3>
			<?php echo $this->roles_message; ?>
			<em><?php _e('Which roles can create Events?', 'bp-simple-events'); ?></em><br>
			<form action="" name="access-form" id="access-form"  method="post" class="standard-form">

			<?php wp_nonce_field('allowedroles-action', 'allowedroles-field'); ?>

			<ul id="pp-user_roles">

			<?php foreach(  $all_roles as $key => $value ) {

				if ( $key == 'administrator' ) :
				?>

					<li><label><input type="checkbox" id="admin-preset-role" name="admin-preset" checked="checked" disabled /> <?php echo ucfirst($key); ?></label></li>

				<?php else:

					if ( array_key_exists('publish_events', $value["capabilities"]) )
						$checked = ' checked="checked"';
					else
						$checked = '';

				?>

					<li><label for="allow-roles-<?php echo $key ?>"><input id="allow-roles-<?php echo $key ?>" type="checkbox" name="allow-roles[]" value="<?php echo $key ?>" <?php echo  $checked ; ?> /> <?php echo ucfirst($key); ?></label></li>

				<?php endif;

			}?>

			</ul>

			<input type="hidden" name="role-access" value="1"/>
			<input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Roles', 'bp-simple-events'); ?>"/>
			</form>

		</td>

		<td style="vertical-align:top; border: 1px solid #ccc;" >
			<h3><?php _e('Settings', 'bp-simple-events'); ?></h3>
			<?php echo $this->settings_message; ?>

			<form action="" name="settings-form" id="settings-form"  method="post" class="standard-form">

				<?php wp_nonce_field('settings-action', 'settings-field'); ?>

				<h4><?php _e( "Do NOT Use the Google Maps API", "bp-simple-events"); ?></h4>
				<input type="checkbox" id="skip-google" name="skip-google" value="1" <?php checked( $skip_google, 1 ); ?>
				 &nbsp; <?php _e("Do NOT use Google Maps", "bp-member-maps");?>
				<br><?php _e("If selected, the Location field will NOT use Google Maps to create or validate addresses.", "bp-simple-events");?>

				<div id="google-div">

					<h4><?php _e( "Google Maps API Key", "bp-simple-events"); ?></h4>
					<?php _e("Your Key", "bp-member-maps");?> &nbsp;
					<input type="text" size="45" name="gapikey" value="<?php echo $gapikey; ?>" />
					<br><?php _e("A Key is required. If you do not have one, follow these instructions:", "bp-simple-events");?>
					<br><a href="http://www.philopress.com/google-maps-api-key/" target="_blank">Get a Google Maps API Key</a>

					<h4><?php _e( "Test Your Google API Key", "bp-simple-events"); ?></h4>
					<input type="text" size="40" id="event-location" name="event-location" placeholder="<?php _e( 'Start typing an address...', 'bp-member-maps' ); ?>" value="" />
					<br><?php _e("If Google displays a list of addresses as you type - your Google Maps API Key is probably <strong>valid</strong>.", "bp-member-maps");?>
					<br><?php _e("Otherwise there is a <strong>problem</strong> with your key. Open your browser's javascript console for error info supplied by Google.", "bp-member-maps");?>

				</div>

				<hr/>

				<h4><?php _e('Profile', 'bp-simple-events'); ?></h4>
				<?php $tab_position = get_option( 'pp_events_tab_position' ); ?>
				<input type="text" size="5" id="pp-tab-position" name="pp-tab-position" value="<?php echo $tab_position; ?>" />
				<label for="pp-tab-position"><?php _e( 'Tab Position <em>Numbers only.</em>', 'bp-simple-events' ); ?></label>
				<hr/>


				<h4><?php _e('Required Fields', 'bp-simple-events'); ?></h4>
				<?php _e('Select fields to be required when creating an Event.', 'bp-simple-events'); ?>
				<br>

				<ul id="pp-fielders">

					<li><label><input type="checkbox" name="event-dummy[]" checked="checked" disabled /> Title</label></li>

					<li><label><input type="checkbox" name="event-dummy[]" checked="checked" disabled /> Description</label></li>

					<?php
					$required_fields = get_option( 'pp_events_required' );
					$checked = ' checked';
					?>

					<li><label for="required-date"><input id="required-date" type="checkbox" name="pp-required[]" value="date" <?php if ( in_array( 'date', $required_fields ) ) echo $checked ; ?> /> <?php _e( 'Date', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-time"><input id="required-time" type="checkbox" name="pp-required[]" value="time" <?php if ( in_array( 'time', $required_fields ) ) echo $checked ; ?> /> <?php _e( 'Time', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-location"><input id="required-location" type="checkbox" name="pp-required[]" value="location" <?php if ( in_array( 'location', $required_fields ) ) echo $checked ; ?> /> <?php _e( 'Location', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-url"><input id="required-url" type="checkbox" name="pp-required[]" value="url" <?php if ( in_array( 'url', $required_fields ) ) echo $checked ; ?> /> <?php _e( 'Url', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-url"><input id="required-categories" type="checkbox" name="pp-required[]" value="categories" <?php if ( in_array( 'categories', $required_fields ) ) echo $checked ; ?> /> <?php _e( 'Categories', 'bp-simple-events' ); ?></label></li>

				</ul>

				<hr/>

				<p>
				<h4><?php _e('Single Event Display', 'bp-simple-events'); ?></h4>

				<input type="checkbox" id="display-image" name="display-image" value="1" <?php checked( $display_image, 1 ); ?> />&nbsp; &nbsp;<?php _e( 'Display featured image, if it exists, on Single Event', 'bp-simple-events' ); ?>

				</p>

				<hr/>

				<br>
				<input type="hidden" name="settings-access" value="1"/>
				<input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Settings', 'bp-simple-events'); ?>"/>
			</form>

		</td></tr></table>

		<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var skip_google = <?php echo $skip_google; ?>;
			if ( skip_google ) $("#google-div").hide();

			$("#skip-google").change(function () {
				if ($(this).is(":checked")) {
					$("#google-div").hide(400);
				} else {
					$("#google-div").show(400);
				}
			});
		});
		</script>

	<?php
	}


	//  save any changes to role access options
	private function roles_update() {
		global $wp_roles;

		if ( isset( $_POST['role-access'] ) ) {

			if ( !wp_verify_nonce($_POST['allowedroles-field'],'allowedroles-action') ) {
				die('Security check');
			}

			if ( !is_super_admin() ) {
				return;
			}

			$updated = false;

			$all_roles = $wp_roles->roles;

			if ( is_multisite() && is_network_admin() ) {
			    //apply_caps_to_blog
				global $current_site,$wpdb;
				$blog_ids = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs.' WHERE site_id='.$current_site->id);

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog($blog_id);
				    //normal blog role application
					foreach (  $all_roles as $key => $value ) {

						if ( 'administrator' != $key ) {

							$role = get_role( $key );

							$role->remove_cap( 'delete_published_events' );
							$role->remove_cap( 'delete_events' );
							$role->remove_cap( 'edit_published_events' );
							$role->remove_cap( 'edit_events' );
							$role->remove_cap( 'publish_events' );

							$updated = true;
						}
					}

					if ( isset( $_POST['allow-roles'] ) ) {

						foreach ( $_POST['allow-roles'] as $key => $value ) {

							if ( array_key_exists($value, $all_roles ) ) {

								if ( 'administrator' != $value ) {

									$role = get_role( $value );
									$role->add_cap( 'delete_published_events' );
									$role->add_cap( 'delete_events' );
									$role->add_cap( 'edit_published_events' );
									$role->add_cap( 'edit_events' );
									$role->add_cap( 'publish_events' );

								}
							}
						}

					}

					restore_current_blog();
				}

			} else {	// not multisite
				foreach(  $all_roles as $key => $value ) {

					if ( 'administrator' != $key ) {

						$role = get_role( $key );

						$role->remove_cap( 'delete_published_events' );
						$role->remove_cap( 'delete_events' );
						$role->remove_cap( 'edit_published_events' );
						$role->remove_cap( 'edit_events' );
						$role->remove_cap( 'publish_events' );

						$updated = true;
					}
				}


				if ( isset( $_POST['allow-roles'] ) ) {

					foreach ( $_POST['allow-roles'] as $key => $value ) {

						if ( array_key_exists($value, $all_roles ) ) {

							if ( 'administrator' != $value ) {

								$role = get_role( $value );
								$role->add_cap( 'delete_published_events' );
								$role->add_cap( 'delete_events' );
								$role->add_cap( 'edit_published_events' );
								$role->add_cap( 'edit_events' );
								$role->add_cap( 'publish_events' );

							}
						}
					}

				}
			}

			if ( $updated ) {
				$this->roles_message .=
					"<div class='updated below-h2'>" .
					__('User Roles have been updated.', 'bp-simple-events') .
					"</div>";
			} else {
				$this->roles_message .=
					"<div class='updated below-h2' style='color: red'>" .
					__('No changes were detected re User Roles.', 'bp-simple-events') .
					"</div>";
			}
		}
	}

	//  save any changes to settings options
	private function settings_update() {

		if ( isset( $_POST['settings-access'] ) ) {

			if ( !wp_verify_nonce($_POST['settings-field'],'settings-action') ) {
				die('Security check failed for BP Simple Events');
			}

			if ( !is_super_admin() ) {
				return;
			}

			if ( isset( $_POST["gapikey"] ) &&  $_POST["gapikey"] != "Paste Your Key Here"  ) {
				update_site_option( "pp_gapikey", $_POST["gapikey"] );
			}

			if ( ! empty( $_POST['pp-tab-position'] ) ) {

				if ( is_numeric( $_POST['pp-tab-position'] ) ) {
				    $tab_value = $_POST['pp-tab-position'];
				} else {
					$tab_value = 52;
				}

			} else {
				$tab_value = 52;
			}

			update_option( 'pp_events_tab_position', $tab_value );


			delete_option( 'pp_events_required' );
			$required_fields = array();
			if ( ! empty( $_POST['pp-required'] ) ) {
				foreach ( $_POST['pp-required'] as $value ) {
					$required_fields[] = $value;
				}
			}
			update_option( 'pp_events_required', $required_fields );


			// maybe update 'show featured image on single event'
			if ( isset( $_POST["display-image"] ) ) {
				update_option( 'pp_events_display_image', '1' );
			} else {
				update_option( 'pp_events_display_image', '0' );
			}

			// maybe use Google Maps for Location field
			if ( isset( $_POST["skip-google"] ) ) {
				update_option( 'pp_skip_google', '1' );
			} else {
				update_option( 'pp_skip_google', '0' );
			}

			$this->settings_message .=
				"<div class='updated below-h2'>" .
				__('Settings have been updated.', 'bp-simple-events') .
				"</div>";
		}
	}

} // end of PP_Simple_Events_Admin_Settings class

$pp_se_admin_settings_instance = new PP_Simple_Events_Admin_Settings();

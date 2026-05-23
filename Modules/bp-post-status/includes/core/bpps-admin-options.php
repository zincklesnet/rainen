<?php

if(!defined('ABSPATH')) {
	exit;
}

if ( ! class_exists( 'BPPS_Admin_Options' ) ) :

// @package bp-post-status

// WordPress Admin Options for BP Post Status


class BPPS_Admin_Options {

	/**

	 * Plugin's main instance

	 *

	 * @var object

	 */

	protected static $instance;



	
	function __construct() {
		
	add_action( 'admin_menu', array( $this, 'bpps_admin_add_page' ) );


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

	 * Create Menu Page.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 

	public function bpps_admin_add_page() {
		
		$options_page = sanitize_text_field(_x('BP Post Status','bp-post-status'));
		
		add_options_page( $options_page, $options_page, 'manage_options', 'bpps', array( $this, 'bpps_options_page' ) );
		add_action( 'admin_init', array( $this, 'bpps_admin_init' ) );

	}

	/**

	 * Construct the Save Changes button.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 

	// 
	
	public function bpps_options_page() {

	?>
		
		<div class="wrap">
			
			<h2><?php esc_attr__( 'BP Post Status', 'bp-post-status' ) ?></h2>
			
			<form action="options.php" method="post">
				
				<?php settings_fields( 'bpps' ); ?>
				
				<?php do_settings_sections( 'bpps' ); ?>
				
				<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes', 'bp-post-status' ); ?>" class="button button-primary" />
			
			</form>
		
		</div>
		
	<?php
	}

	/**

	 * Set up the Settings Sections and Fields.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 


	public function bpps_admin_init(){
		// Create Settings
		$option_group = 'bpps';
		$member_settings = 'bpps_members_settings';
		$group_settings = 'bpps_groups_settings';
		$friend_settings = 'bpps_friends_settings';
		$general_settings = 'bpps_general_settings';
		$following_settings = 'bpps_following_settings';
		$followed_settings = 'bpps_followed_settings';
		
		register_setting( $option_group, $member_settings );
		register_setting( $option_group, $group_settings );
		register_setting( $option_group, $friend_settings );
		register_setting( $option_group, $general_settings );
		register_setting( $option_group, $following_settings );
		register_setting( $option_group, $followed_settings );

		// Create Settings page Section for BP Post Status
		$settings_section = 'bpps_post_status_main';
		$page = 'bpps';
		
		add_settings_section( $settings_section, esc_attr__( 'BP Post Status ', 'bp-post-status' ), array( $this, 'bpps_main_section_text_output' ), $page );

		// Add Members settings fields.
		add_settings_field( $member_settings, sanitize_text_field(__('Members Only Posts', 'bp-post-status' )), array( $this, 'bpps_members_input_renderer' ), $page, $settings_section );

		// Add Group Only Posts fields.
		add_settings_field( $group_settings, sanitize_text_field(__('Group Only Posts', 'bp-post-status' )), array( $this, 'bpps_groups_input_renderer' ), $page, $settings_section );

		// Add Friends Only Posts fields.
		add_settings_field( $friend_settings, sanitize_text_field(__('Friends Only Posts', 'bp-post-status' )), array( $this, 'bpps_friends_input_renderer' ), $page, $settings_section );

		// Add Following Posts fields.
		add_settings_field( $following_settings, sanitize_text_field(__('Following Posts', 'bp-post-status' )), array( $this, 'bpps_following_input_renderer' ), $page, $settings_section );

		// Add Friends Only Posts fields.
		add_settings_field( $followed_settings, sanitize_text_field(__('Followed Posts', 'bp-post-status' )), array( $this, 'bpps_followed_input_renderer' ), $page, $settings_section );

		// Add General Posts fields.
		add_settings_field( $general_settings, sanitize_text_field(__('General Settings', 'bp-post-status' )), array( $this, 'bpps_general_input_renderer' ), $page, $settings_section );

	}

	/**

	 * Page Information for Post Types support

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 

	public function bpps_main_section_text_output() {
		
		sanitize_text_field(__( '<p>This page allows you to decide which features of BP Post Status to enable sitewide. post and notifications can be set as well as minimum capability levels.</p><p>Note that group admin have their own settings, however these cannot over-ride the settings here.</p>', 'bp-post-status' ));
		
	}

	/**

	 * create capability type array.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 


	public function bpps_selectable_capability_types($selectlist = FALSE, $name = FALSE) {
		
		global $wp_roles;
		
		$option = get_option($name);	
		
		if ($selectlist) {
			
			$cap = '<select name="' . $name .'">';
			$cap .= '<option value="do-everything">Nobody</option>';	
			
			foreach ($wp_roles->roles['administrator']['capabilities'] as $key=>$val) {
				
				$cap .= '<option value="' . $key . '"';
				
				if ($option == $key) $cap .= ' selected="yes"';
				
				$cap .= '>' . $key . '</option>';		
			
			}
			
			$cap .= '</select>';
			
			return $cap;
		
		} else {
			
			return $wp_roles->roles['administrator']['capabilities'];
		
		}
		
	}

	/**

	 * Function for looking up the BP Post Status admin settings.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 

	public function bpps_return_settings($part) {
		
		$group_context_enable = false;
		
		if ( $part ) {
			
			if( $part == 'members' ) {
				
				$settings = get_option( "bpps_members_settings" );
			
			} else if( $part == 'groups' ) {
				
				$settings = get_option( "bpps_groups_settings" );
				if ( isset( $settings['group_context_enable'] ) ) {
					$group_context_enable = $settings['group_context_enable'];
				} else {
					$group_context_enable = false;
				}
			
			} else if( $part == 'friends' ) {
				
				$settings = get_option( "bpps_friends_settings" );
			
			} else if( $part == 'following' ) {
				
				$settings = get_option( "bpps_following_settings" );
			
			} else if( $part == 'followed' ) {
				
				$settings = get_option( "bpps_followed_settings" );
			
			}
		
		} else {
			
			return false;
		
		}
		

		
		if ( isset($settings) ){
			
			if ( isset($settings["members_disable"] ) ) {
				
				$posts_disable = $settings["members_disable"];
			
			} else if ( isset($settings["groups_disable"] ) ) {
				
				$posts_disable = $settings["groups_disable"];
			
			} else if ( isset($settings["following_disable"] ) ) {
				
				$posts_disable = $settings["following_disable"];
			
			} else if ( isset($settings["followed_disable"] ) ) {
				
				$posts_disable = $settings["followed_disable"];
			
			} else if ( isset($settings["friends_disable"] ) ) {
				
				$posts_disable = $settings["friends_disable"];
			
			} else {
				
				$posts_disable = false;
			
			}
		} else {
				
			$posts_disable = false;
		}
		
		
		if (isset($settings) ) {
			
			if ( isset( $settings["members_cap"] ) ) {
				
				$post_cap = $settings["members_cap"];
			
			} else if ( isset( $settings["groups_cap"] ) ) {
				
				$post_cap = $settings["groups_cap"];
			
			} else if ( isset( $settings["following_cap"] ) ) {
				
				$post_cap = $settings["following_cap"];
			
			} else if ( isset( $settings["followed_cap"] ) ) {
				
				$post_cap = $settings["followed_cap"];
			
			} else if ( isset( $settings["friends_cap"] ) ) {
				
				$post_cap = $settings["friends_cap"];
			
			} else {
				
				$post_cap = 'edit_posts';
			
			}
		
		} else {
			
			$post_cap = 'edit_posts';
		}
		
		if ( isset( $settings ) ) {
			
			if ( isset($settings["members_notif_enable"] ) ) {
				
				$notif_enable = $settings["members_notif_enable"];
			
			} else if ( isset($settings["groups_notif_enable"] ) ) {
				
				$notif_enable = $settings["groups_notif_enable"];
			
			} else if ( isset($settings["following_notif_enable"] ) ) {
				
				$notif_enable = $settings["following_notif_enable"];
			
			} else if ( isset($settings["followed_notif_enable"] ) ) {
				
				$notif_enable = $settings["followed_notif_enable"];
			
			} else if ( isset($settings["friends_notif_enable"] ) ) {
				
				$notif_enable = $settings["friends_notif_enable"];
			
			} else {
				
				$notif_enable = true;
			}
		
		} else {

			$notif_enable = true;
		}
		
		if ( isset ( $settings ) ) {
			
			if ( isset( $settings["members_notif_cap"] ) ) {
				
				$notif_cap = $settings["members_notif_cap"];
			
			} else if ( isset( $settings["groups_notif_cap"] ) ) {
				
				$notif_cap = $settings["groups_notif_cap"];
			
			} else if ( isset( $settings["following_notif_cap"] ) ) {
				
				$notif_cap = $settings["following_notif_cap"];
			
			} else if ( isset( $settings["followed_notif_cap"] ) ) {
				
				$notif_cap = $settings["followed_notif_cap"];
			
			} else if ( isset( $settings["friends_notif_cap"] ) ) {
				
				$notif_cap = $settings["friends_notif_cap"];
			
			} else {
				
				$notif_cap = 'edit_posts';
			
			}
		
		} else {
			
			$notif_cap = 'edit_posts';
		
		}

		if ( isset ( $settings ) ) {
			
			if ( isset( $settings["members_activity_content"] ) ) {
				
				$activity_content = $settings["members_activity_content"];
			
			} else if ( isset( $settings["groups_activity_content"] ) ) {
				
				$activity_content = $settings["groups_activity_content"];
			
			} else if ( isset( $settings["friends_activity_content"] ) ) {
				
				$activity_content = $settings["friends_activity_content"];
			
			} else if ( isset( $settings["following_activity_content"] ) ) {
				
				$activity_content = $settings["following_activity_content"];
			
			} else if ( isset( $settings["followed_activity_content"] ) ) {
				
				$activity_content = $settings["followed_activity_content"];
			
			} else {
				
				$activity_content = 'excerpt';
			
			}
		
		} else {
			
			$notif_cap = 'edit_posts';
		
		}
		
		$option = array( $posts_disable, $post_cap, $notif_enable, $notif_cap, $activity_content, $group_context_enable );
		
		return apply_filters( 'bpps_settings', $option );
	}

	/**

	 * Function for rendering the members settings to the admin page.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 


	function bpps_members_input_renderer() {
		
		$settings = $this->bpps_output_render( 'members' );
	}
		

	/**

	 * Function for rendering the general settings to the admin page.

	 *

	 * @since 1.2.8

	 *

	 * 
	 
	 */ 


	function bpps_general_input_renderer() {
		
		$settings = get_option( 'bpps_general_settings' );
		
		if ( isset( $settings['comment_email'] ) ) {
		
			$comment_email_enable = $settings['comment_email'];
			
		} else {
			
			$comment_email_enable = false;
		}

		if ( isset( $settings['approve_email'] ) ) {
		
			$approve_email_enable = $settings['approve_email'];
			
		} else {
			
			$approve_email_enable = false;
		}

		if ( isset( $settings['admin_notification_emails'] ) ) {
		
			$approve_emails = esc_attr( $settings['admin_notification_emails'] );
			
		} else {
			
			$approve_emails = false;
		}
		
		if ( isset( $settings['updates_disabled'] ) ){
			
			$activity_updates_disabled = $settings['updates_disabled'];
			
		} else { 
			
			$activity_updates_disabled = false;
		
		}
		
		if ( isset( $settings['update_delay'] ) ) {
			
			$activity_updates_delay = $settings['update_delay'];
			
		} else { 
			
			$activity_updates_delay = 20;
		
		}
		
		$message = esc_attr__( 'Enable post comment notification emails', 'bp-post-status' );
		$approve_message = esc_attr__( 'Enable post approval notification emails', 'bp-post-status' );
		$choice = esc_attr__( 'Who should receive an email notification for a new submission?', 'bp-post-status' );
		$placeholder = esc_attr__( 'Enter email addresses here', 'bp-post-status' );
		$activity_disable = esc_attr__( 'Disable activity updates for edits (only create activity updates when the post is first created).', 'bp-post-status' );
		$activity_delay = esc_attr__( 'Minimum delay between activity updates (Mins).', 'bp-post-status' );

		echo '<div class="bpps-general-settings">
				<table class="form-table">
					<tbody>
						<tr valign="top">

							<td><input type="checkbox" name="bpps_general_settings[comment_email]" id="comment-email-enable-bpps" value="1" ';
									if ( $comment_email_enable ): echo 'checked="checked"'; endif; echo '>
								<label for="comment-email-enable-bpps">' . esc_attr($message) . '</label>
							</td>
						</tr>
						<tr valign="top">
							<td><input type="checkbox" name="bpps_general_settings[approve_email]" id="approve-email-enable-bpps" value="1" ';
									if ( $approve_email_enable ): echo 'checked="checked"'; endif; echo '>
								<label for="approve-email-enable-bpps">' . esc_attr($approve_message) . '</label>
							</td>
						</tr>
						<tr valign="top">
							<td><input type="text" name="bpps_general_settings[admin_notification_emails]" id="notification-emails" class="regular-text" placeholder="' . esc_attr($placeholder) . '" value="' . esc_attr($approve_emails) . '"/>
							<label for="notification-emails">' . esc_attr($choice) . '</label>
							</td>
						</tr>
						<tr valign="top">
							<td><input type="checkbox" name="bpps_general_settings[updates_disabled]" id="updates-disabled-bpps" value="0" ';
									if ( $activity_updates_disabled ): echo 'checked="checked"'; endif; echo '>
								<label for="updates-disabled-bpps">' . esc_attr($activity_disable) . '</label>
							</td>
						</tr>
						<tr>
							<td><input type="number" min=0 max=1000 name="bpps_general_settings[update_delay]", id="activity-update-delay" value="' . esc_attr($activity_updates_delay) .'"/>
							<label for="activity-update-delay">' . esc_attr($activity_delay) . '</label>
							</td?
						</tr>
					</tbody>
				</table>
			</div>';
	}

	/**

	 * Function for rendering the groups settings to the admin page.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 


	function bpps_groups_input_renderer() {
		
		$settings = $this->bpps_output_render( 'groups' );
	}
		
	/**

	 * Function for rendering the friends settings to the admin page.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 


	function bpps_friends_input_renderer() {
		
		$settings = $this->bpps_output_render( 'friends' );
	}
		
	/**

	 * Function for rendering the following settings to the admin page.

	 *

	 * @since 1.4.0

	 *

	 * 
	 
	 */ 


	function bpps_following_input_renderer() {
		
		$settings = $this->bpps_output_render( 'following' );
	}
		
	/**

	 * Function for rendering the followed settings to the admin page.

	 *

	 * @since 1.4.0

	 *

	 * 
	 
	 */ 


	function bpps_followed_input_renderer() {
		
		$settings = $this->bpps_output_render( 'followed' );
	}
		
	/**

	 * Function for rendering the settings to the admin page.

	 *

	 * @since 1.0.0

	 *

	 * 
	 
	 */ 
	
	public function bpps_output_render( $part ) {
		
		$settings = $this->bpps_return_settings( $part );
		
		$posts_disable = $settings[0];
		$posts_cap = $settings[1];
		$notif_enable = $settings[2];
		$notif_cap = $settings[3];
		$activity_content = $settings[4];
		$group_context = $settings[5];
		
		$group_context_setting = '';
		
		if ( $group_context ) $group_context_setting = 'checked="checked"';
		
		$activity_options = array(
			'content' 	=> esc_attr__('Full Content', 'bp-post-status' ),
			'summary' 	=> esc_attr__('Content Summary', 'bp-post-status' ),
			'excerpt' 	=> esc_attr__('Excerpt', 'bp-post-status' ),
			'none' 		=> esc_attr__('None', 'bp-post-status' )
			);
		
		if ( $part == 'members' ) {
			
			$message = esc_attr__( 'Disable Members Only Posts', 'bp-post-status' );
			$post_cap_message = esc_attr__( 'Select minimum capability required to send members only posts', 'bp-post-status' );
			$notif_message = esc_attr__( 'Enable Members Only Post notifications', 'bp-post-status' );
			$notif_cap_message = esc_attr__( 'Select minimum capability required to send members only post notifications', 'bp-post-status' );
			$activity_content_message = esc_attr__( 'Select the default content for members activity updates', 'bp-post-status' );
		
		} else if ( $part == 'groups' ) {
			
			$message = esc_attr__( 'Disable Group Posts', 'bp-post-status' );
			$post_cap_message = esc_attr__( 'Select minimum capability required to send group posts', 'bp-post-status' );
			$notif_message = esc_attr__( 'Enable Group Post notifications', 'bp-post-status' );
			$notif_cap_message = esc_attr__( 'Select minimum capability required to send group post notifications', 'bp-post-status' );
			$activity_content_message = esc_attr__( 'Select the default content for groups activity updates', 'bp-post-status' );
			$group_context_message = esc_attr__( 'Choose if Groups posts should be constrained within each group or if they should also be displayed as normal posts', 'bp-post-status' );
		
		} else if ( $part == 'friends' ) {
			
			$message = esc_attr__( 'Disable Friends Only Posts', 'bp-post-status' );
			$post_cap_message = esc_attr__( 'Select minimum capability required to send friends only posts', 'bp-post-status' );
			$notif_message = esc_attr__( 'Enable Friends Only Post notifications', 'bp-post-status' );
			$notif_cap_message = esc_attr__( 'Select minimum capability required to send friends only post notifications', 'bp-post-status' );
			$activity_content_message = esc_attr__( 'Select the default content for friends activity updates', 'bp-post-status' );
		
		} else if ( $part == 'following' ) {
			
			$message = esc_attr__( 'Disable Following Posts', 'bp-post-status' );
			$post_cap_message = esc_attr__( 'Select minimum capability required to send following posts', 'bp-post-status' );
			$notif_message = esc_attr__( 'Enable Following Post notifications', 'bp-post-status' );
			$notif_cap_message = esc_attr__( 'Select minimum capability required to send following post notifications', 'bp-post-status' );
			$activity_content_message = esc_attr__( 'Select the default content for following activity updates', 'bp-post-status' );
		
		} else if ( $part == 'followed' ) {
			
			$message = esc_attr__( 'Disable Followed Posts', 'bp-post-status' );
			$post_cap_message = esc_attr__( 'Select minimum capability required to send followed posts', 'bp-post-status' );
			$notif_message = esc_attr__( 'Enable Followed Post notifications', 'bp-post-status' );
			$notif_cap_message = esc_attr__( 'Select minimum capability required to send followed post notifications', 'bp-post-status' );
			$activity_content_message = esc_attr__( 'Select the default content for followed activity updates', 'bp-post-status' );
		
		}
		
		$capabilities = $this->bpps_selectable_capability_types();

		echo '<div class="bpps-' . esc_attr($part) . '-settings">
				<table class="form-table">
					<tbody>
						<tr valign="top">

							<td><input type="checkbox" name="bpps_' . esc_attr($part) . '_settings[' . esc_attr($part) . '_disable]" id="' . esc_attr($part) . '-disable-bpps" value="1" ';
									if ( $posts_disable ): echo 'checked="checked"'; endif; echo '>
								<label for="' . esc_attr($part) . '-disable-bpps">' . esc_attr($message) . '</label>
							</td>
						</tr>
						<tr>
							<td><select name="bpps_' . esc_attr($part) . '_settings[' . esc_attr($part) . '_cap]">';
			
								foreach ( $capabilities as $role => $cap) {
			
									echo '<option value="' . esc_attr($role) .'" name="bpps_' . esc_attr($part) . '_cap" ' . ( $posts_cap == $role ? 'selected="yes"' : '' ).'>'.esc_attr($role) .'</option>';
		
								}
		
								echo '</select>
							<label for ="bpps_' . esc_attr($part) . '_cap">' . esc_attr($post_cap_message) . '</label></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="bpps_' . esc_attr($part) . '_settings[' . esc_attr($part) . '_notif_enable]" id="' . esc_attr($part) . '-notif-enable-bpps" value="1" ';
									if ( $notif_enable ): echo 'checked="checked"'; endif; echo '>
							<label for="' . esc_attr($part) . '-notif-enable-bpps">' . esc_attr($notif_message) . '</label>
							</td>
						</tr>
						<tr>
							<td><select name="bpps_' . esc_attr($part) . '_settings[' . esc_attr($part) . '_notif_cap]">';
							
							foreach ( $capabilities as $role => $cap) {
			
								echo '<option value="' . esc_attr($role) .'" name="bpps_' . esc_attr($part) . '_notif_cap" ' . ( $notif_cap == $role ? 'selected="yes"' : '' ).'>'.esc_attr($role) .'</option>';
		
							}
		
							echo '</select>
							<label for ="bpps_' . esc_attr($part) . '_notif_cap">' . esc_attr($notif_cap_message) . '</label>
							</td>
						</tr>
						<tr>
							<td><select name="bpps_' . esc_attr($part) . '_settings[' . esc_attr($part) . '_activity_content]">';
							
							foreach ( $activity_options as $option => $label) {
			
								echo '<option value="' . esc_attr($option) .'" name="bpps_' . esc_attr($part) . '_activity_content" ' . ( $activity_content == $option ? 'selected="yes"' : '' ).'>'.esc_attr($option) .'</option>';
		
							}
		
							echo '</select>
							<label for ="bpps_' . esc_attr($part) . '_activity_content">' . esc_attr($activity_content_message) . '</label>
							</td>
						</tr>';
						if ( $part == 'groups' ) :
						echo '<tr>
							<td><input type="checkbox" name="bpps_' . esc_attr($part) . '_settings[group_context_enable]" ' . esc_attr($group_context_setting) . 'id="' . esc_attr($part) . '-group_context-bpps">';
							
							echo '<label for ="bpps_' . esc_attr($part) . '_group_context">' . esc_attr($group_context_message) . '</label>
							</td>
						</tr>';
						endif;
					echo '</tbody>
				</table>
			</div>';
	
	}


}
endif;

/**

 * Boot the plugin.

 *

 * @since 1.0.0

 */

function bpps_admin_options() {

	return BPPS_Admin_Options::start();

}

add_action( 'plugins_loaded', 'bpps_admin_options', 5 );	
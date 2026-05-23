<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Edit_Activity
 * @subpackage Buddypress_Edit_Activity/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Edit_Activity
 * @subpackage Buddypress_Edit_Activity/admin
 * @author     Wbcom Designs <info@wbcomdesign.com>
 */
class Buddypress_Edit_Activity_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Edit_Activity_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Edit_Activity_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$min 	= '';
			$path   = is_rtl() ? 'rtl/' : '';
		} else {
			$min	= '.min';
			$path   = is_rtl() ? 'rtl/' : 'min/';
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . "css/{$path}buddypress-edit-activity-admin{$min}.css", array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Edit_Activity_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Edit_Activity_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$min 	= '';
			$path   = '';
		} else {
			$min 	= '.min';
			$path   = 'min/';
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . "js/{$path}buddypress-edit-activity-admin{$min}.js", array( 'jquery' ), $this->version, false );

	}
	
	
	/**
	 * Edit activity register the settings.
	 *
	 * @since 1.0.0
	 */
	public function bp_edit_activity_register_admin_settings() {
		if ( bp_is_active( 'activity' ) ) {
			
			add_settings_field( '_bp_enable_edit_option', esc_html__( 'Enable Edit Option', 'buddypress-edit-activity' ), [$this, 'bp_admin_setting_callback_edit_option' ], 'buddypress', 'bp_activity' );
			register_setting( 'buddypress', '_bp_enable_edit_option', 'intval' );
			
			add_settings_field( '_bp_edit_activity_duration', esc_html__( 'Edit Duration', 'buddypress-edit-activity' ), '__return_true', 'buddypress', 'bp_activity', ['class' => 'hidden'] );
			register_setting( 'buddypress', '_bp_edit_activity_duration', 'sanitize_text_field' );
		}
	}
	
	/**
	 * Allow Activity edit to activity stream.
	 *
	 * @since 1.0.0
	 */
	public function bp_admin_setting_callback_edit_option() {
		
		$enable_edit_option 	= bp_get_option( '_bp_enable_edit_option', true );
		$edit_activity_duration = bp_get_option( '_bp_edit_activity_duration', true );
		?>
		<input id="_bp_enable_edit_option" name="_bp_enable_edit_option" type="checkbox" value="1" <?php checked( $enable_edit_option, 1 ); ?> />
		<label for="_bp_enable_edit_option"><?php esc_html_e( 'Allow members to edit their activity posts for a duration ', 'buddypress-edit-activity' ); ?></label>
		
		<select name="_bp_edit_activity_duration" id="_bp_edit_activity_duration" aria-describedby="_bp_theme_package_description">
			<option value="forever" <?php selected( $edit_activity_duration, 'forever' ); ?>><?php esc_html_e('Forever','buddypress-edit-activity');?></option>
			<option value="30_days" <?php selected( $edit_activity_duration, '30_days' ); ?>><?php esc_html_e('30 Days','buddypress-edit-activity');?></option>
			<option value="7_day" <?php selected( $edit_activity_duration, '7_day' ); ?>><?php esc_html_e('7 Days','buddypress-edit-activity');?></option>
			<option value="1_day" <?php selected( $edit_activity_duration, '1_day' ); ?>><?php esc_html_e('1 Day','buddypress-edit-activity');?></option>
			<option value="1_hour" <?php selected( $edit_activity_duration, '1_hour' ); ?>><?php esc_html_e('1 Hour','buddypress-edit-activity');?></option>
			<option value="10_minutes" <?php selected( $edit_activity_duration, '10_minutes' ); ?>><?php esc_html_e('10 Minutes','buddypress-edit-activity');?></option>
		</select>
		<?php
	}


	/**
	 * Add admin sub menu for plugin settings.
	 *
	 * @since 1.0.0
	 */
	public function bp_edit_activity_add_plugin_settings_page() {
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-edit-activity' ), esc_html__( 'WB Plugins', 'buddypress-edit-activity' ), 'manage_options', 'wbcomplugins', array( $this, 'bp_edit_activity_settings_page' ), 'dashicons-lightbulb', 59 );			
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Edit Activity Settings Page', 'buddypress-edit-activity' ), esc_html__( 'Edit Activity', 'buddypress-edit-activity' ), 'manage_options', 'buddypress-edit-activity', array( $this, 'bp_edit_activity_settings_page' ) );
	}
	public function bp_edit_activity_settings_page() {
		$current          = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
		$member_blog_tabs = apply_filters(
			'bp_member_blog_admin_setting_tabs',
			array(
				'welcome'                => __( 'Welcome', 'buddypress-edit-activity' ),
				'faq'        			 => __( 'FAQ', 'buddypress-edit-activity' ),
			)
		);
		?>
		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
				</div>
			</div>
			<div class="wbcom-wrap bp-member-blog-wrap">
				<div class="blpro-header">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">
							<?php
							esc_html_e( 'BuddyPress Edit Activity', 'buddypress-edit-activity' );
							?>
							<span>
							<?php
								// translators: %s is replaced with the plugin version
								printf( esc_html__( 'Version %s', 'buddypress-edit-activity' ), esc_html( BUDDYPRESS_EDIT_ACTIVITY_VERSION ) );
							?>
							</span>
						</div>
						<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
					</div>
				</div>
				<div class="wbcom-admin-settings-page">
					<div class="wbcom-tabs-section">
						<div class="nav-tab-wrapper">
							<div class="wb-responsive-menu">
								<span><?php esc_html_e( 'Menu', 'buddypress-edit-activity' ); ?></span>
								<input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn">
								<label class="wb-toggle-icon" for="wb-toggle-btn">
									<span class="wb-icon-bars"></span>
								</label>
							</div>
							<ul>
							<?php
							foreach ( $member_blog_tabs as $bmpro_tab => $bmpro_name ) {
								$class     = ( $bmpro_tab == $current ) ? 'nav-tab-active' : '';
								$bmb_nonce = wp_create_nonce( 'bmb_nonce' );
								echo '<li id="' . esc_attr( $bmpro_tab ) . '"><a class="nav-tab ' . esc_attr( $class ) . '" href="admin.php?page=buddypress-edit-activity&tab=' . esc_attr( $bmpro_tab ) . '&nonce=' . esc_attr( $bmb_nonce ) . '">' . esc_html( $bmpro_name ) . '</a></li>';
							}
							?>
							</ul>
						</div>
					</div>
					<?php
					include 'partials/bp-edit-activities-options-page.php';
					do_action( 'bp_member_blog_tab_contents' );
					?>
				</div>
			</div> <!-- closing div class wbcom-wrap -->
		</div> <!-- closing div class wrap -->
		<?php
	}

	/**
	 * Remove all notices from my settings page
	 *
	 * @since 1.0.0
	 */
	public function bp_edit_activity_hide_all_admin_notices_from_setting_page(){
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'buddypress-edit-activity' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}
}

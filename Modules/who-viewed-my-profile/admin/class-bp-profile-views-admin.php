<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Bp_Profile_Views
 * @subpackage Bp_Profile_Views/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bp_Profile_Views
 * @subpackage Bp_Profile_Views/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Bp_Profile_Views_Admin {

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
	 * For plugin setting tabs
	 *
	 * @var array
	 */
	private $plugin_settings_tabs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	
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
		 * defined in Bp_Profile_Views_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bp_Profile_Views_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$extension = is_rtl() ? '.rtl.css' : '.css';
			$path      = is_rtl() ? '/rtl' : '';
		} else {
			$extension = is_rtl() ? '.rtl.css' : '.min.css';
			$path      = is_rtl() ? '/rtl' : '/min';
		}

		wp_enqueue_style( 'ag-grid-style', plugin_dir_url( __FILE__ ) . 'css/vendor/ag-grid.css', array(), '30.2.1', 'all' );
		wp_enqueue_style( 'ag-grid-theme', plugin_dir_url( __FILE__ ) . 'css/vendor/ag-theme-alpine.css', array(), '30.2.1', 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css' . $path . '/bp-profile-views-admin' .$extension , array(), $this->version, 'all' );

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
		 * defined in Bp_Profile_Views_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bp_Profile_Views_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$extension = '.js';
			$path      = '';
		} else {
			$extension = '.min.js';
			$path      = '/min';
		}	


		if(  isset( $_GET['tab'] ) && 'bp-profile-views-members' == $_GET['tab'] ){
			wp_enqueue_script( 'ag-grid-script', plugin_dir_url( __FILE__ ) . 'js/vendor/ag-grid-community.min.js', array( 'jquery' ), '30.2.1', false );
		}
		if(  isset( $_GET['tab'] ) && ( 'bp-profile-views-members' == $_GET['tab'] || 'bp-profile-views-support' == $_GET['tab'] ) ){
			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js'. $path . '/bp-profile-views-admin' . $extension , array( 'jquery' ), $this->version, false );

			wp_enqueue_script( $this->plugin_name ); 

			wp_set_script_translations( $this->plugin_name, 'bp-profile-views' );
		}
		wp_localize_script(
			$this->plugin_name,
			'bpmv',
			array(
				'nonce' => wp_create_nonce( 'bp-member-view-nonce' ),
			)
		);

	}

	public function bp_profile_views_hide_all_admin_notices_from_setting_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'bp-profile-views-settings' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Actions performed to create a submenu page content.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function bp_profile_views_admin_options_page() {
		global $allowedposttags;
		$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bp-profile-views-welcome';
		?>
			<div class="wrap">
				<div class="wbcom-bb-plugins-offer-wrapper">
					<div id="wb_admin_logo">
						<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/?utm_source=pluginoffernotice&utm_medium=community_banner" target="_blank">
							<img src="<?php echo esc_url( BUDDYPRESS_PROFILE_VIEWS_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
						</a>
					</div>
				</div>
				<div class="wbcom-wrap">
					<div class="bupr-header">
						<div class="wbcom_admin_header-wrapper">
							<div id="wb_admin_plugin_name">								
								<?php esc_html_e( 'Who Viewed My Profile', 'bp-profile-views' ); ?>
								<?php /* translators: %s: */ ?>
								<span><?php printf( esc_html__( 'Version %s', 'bp-profile-views' ), esc_attr( BP_PROFILE_VIEWS_VERSION ) ); ?></span>
							</div>
								<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
							</div>
					</div>
					<div class="wbcom-admin-settings-page">
						<?php
						settings_errors();
						$this->bp_profile_views_plugin_settings_tabs();
						settings_fields( $tab );
						do_settings_sections( $tab );
						?>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Actions performed on loading plugin settings
	 *
	 * @since    1.0.9
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bp_stats_init_plugin_settings() {
		$this->plugin_settings_tabs['bp-profile-views-welcome'] = esc_html__( 'Welcome', 'bp-profile-views' );
		register_setting( 'bp_profile_views_admin_welcome_options', 'bp_profile_views_admin_welcome_options' );
		add_settings_section( 'bp-profile-views-welcome', ' ', array( $this, 'bp_profile_views_admin_welcome_content' ), 'bp-profile-views-welcome' );

		$this->plugin_settings_tabs['bp-profile-views-general'] = esc_html__( 'General', 'bp-profile-views' );
		register_setting( 'bp_profile_views_general_options', 'bp_profile_views_general_options' );
		add_settings_section( 'bp-profile-views-general', ' ', array( $this, 'bp_profile_views_general_options_content' ), 'bp-profile-views-general' );

		$this->plugin_settings_tabs['bp-profile-views-members'] = esc_html__( 'Members Views', 'bp-profile-views' );
		register_setting( 'bp_profile_views_members_options', 'bp_profile_views_members_options' );
		add_settings_section( 'bp-profile-views-members', ' ', array( $this, 'bp_profile_views_members_options_content' ), 'bp-profile-views-members' );

		$this->plugin_settings_tabs['bp-profile-views-support'] = esc_html__( 'FAQ', 'bp-profile-views' );
		register_setting( 'bp_profile_views_support_options', 'bp_profile_views_support_options' );
		add_settings_section( 'bp-profile-views-support', ' ', array( $this, 'bp_profile_views_support_options_content' ), 'bp-profile-views-support' );
	}

	/**
	 * Actions performed to create tabs on the sub menu page.
	 */
	public function bp_profile_views_plugin_settings_tabs() {
		$current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bp-profile-views-welcome';
		// xprofile setup tab.
		echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<li class="' . esc_attr( $tab_key ) . '"><a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=bp-profile-views-settings&tab=' . esc_attr( $tab_key ) . '">' . esc_attr( $tab_caption ) . '</a></li>';
		}
		echo '</div></ul></div>';
	}

	/**
	 * Bp_profile_views_admin_welcome_content
	 *
	 * @return void
	 */
	public function bp_profile_views_admin_welcome_content() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/bp-profile-views-welcome-page.php';
	}

	/**
	 * Bp_profile_views_general_options_content
	 *
	 * @return void
	 */
	public function bp_profile_views_general_options_content() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/bp-profile-views-general-page.php';
	}

	/**
	 * Bp_profile_views_general_options_content
	 *
	 * @return void
	 */
	public function bp_profile_views_members_options_content() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/bp-profile-views-members-page.php';
	}

	/**
	 * Bp_profile_views_support_options_content
	 *
	 * @return void
	 */
	public function bp_profile_views_support_options_content() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/bp-profile-views-support-page.php';
	}

	/**
	 * Actions performed on loading admin_menu.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bp_profile_views_add_admin_settings() {
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) && class_exists( 'BuddyPress' ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'bp-profile-views' ), esc_html__( 'WB Plugins', 'bp-profile-views' ), 'manage_options', 'wbcomplugins', array( $this, 'bp_profile_views_admin_options_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'bp-profile-views' ), esc_html__( 'General', 'bp-profile-views' ), 'manage_options', 'wbcomplugins' );

		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Views', 'bp-profile-views' ), esc_html__( 'BuddyPress Views', 'bp-profile-views' ), 'manage_options', 'bp-profile-views-settings', array( $this, 'bp_profile_views_admin_options_page' ) );
	}


	public function bp_profile_views_get_members_view() {	
		
		check_ajax_referer( 'bp-member-view-nonce', 'security' );
		global $wpdb;		
		$results      = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bp_profile_views" );
		$views        = array();
		$members      = array();
		$member_views = array();
		$count        = '';
		
		if ( ! empty( $results ) && empty( $wpdb->last_error ) ) {
			foreach ( $results as $id => $view ) {
				$members[] = $view->user_id;
				$count = array_count_values( $members );				
				if ( in_array( $view->user_id, $members ) ) {
					$user_data = get_userdata($view->user_id);
					$views[ $view->user_id ] = array(
						'name' => bp_core_get_user_displayname( $view->user_id ) . ' (' . $user_data->user_login . ')',
						'weekly' => $this->bp_profile_views_get_weekly_count($view->user_id),
						'monthly' => $this->bp_profile_views_get_monthly_count($view->user_id),
						'yearly' => $this->bp_profile_views_get_yearly_count($view->user_id),						
					);
				}				
			}
			foreach ( $count as $id => $each_count ) {
				$member_views[] = $views[ $id ];
			}
			
		}
		wp_send_json( $member_views );
	}

	/**
	 * Get weekly count
	 *
	 * @return count
	 */
	public function bp_profile_views_get_weekly_count( $user_id ){
		global $wpdb;
		$settings          = get_option( 'bp_profile_views_general_options');
		$table_name        = $wpdb->prefix . 'bp_profile_views';			
		$current_date      = gmdate( 'Y-m-d' );
		$from_str          = strtotime( '-' . ( 7 + 1 ) . ' days', strtotime( $current_date ) );
		$start_date        = gmdate( 'Y-m-d', $from_str ). ' 00:00:00';
		$end_date          = $current_date . ' 23:59:59';
		if ( isset( $settings['exclude_logout_user_count'] ) && 'yes' == $settings['exclude_logout_user_count'] ){			
			$weekly_results    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d AND created BETWEEN %s AND %s;", $user_id, $start_date, $end_date ), ARRAY_A );
		} else {
			$weekly_results    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d AND created BETWEEN %s AND %s AND viewer_id != 0;", $user_id, $start_date, $end_date ), ARRAY_A );
		}
		$count       	   = count( $weekly_results );		
		return $count;		
	}

	/**
	 * Get monthly count
	 *
	 * @return count
	 */
	public function bp_profile_views_get_monthly_count( $user_id ){
		global $wpdb;
		$settings           = get_option( 'bp_profile_views_general_options');
		$table_name         = $wpdb->prefix . 'bp_profile_views';			
		$current_date 		= gmdate( 'Y-m-d' );
		$from_str     		= strtotime( '-' . ( 30 + 1 ) . ' days', strtotime( $current_date ) );
		$start_date  	 	= gmdate( 'Y-m-d', $from_str ). ' 00:00:00';
		$end_date     		= $current_date . ' 23:59:59';
		if ( isset( $settings['exclude_logout_user_count'] ) && 'yes' == $settings['exclude_logout_user_count'] ){		    
			$weekly_results    	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d AND created BETWEEN %s AND %s;", $user_id, $start_date, $end_date ), ARRAY_A );
		} else {
			$weekly_results    	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d AND created BETWEEN %s AND %s AND viewer_id != 0;", $user_id, $start_date, $end_date ), ARRAY_A );
		}
		$count       		= count( $weekly_results );		
		return $count;		
	}

	/**
	 * Get yearly count
	 *
	 * @return count
	 */
	public function bp_profile_views_get_yearly_count( $user_id ){
		global $wpdb;
		$settings           = get_option( 'bp_profile_views_general_options');
		$table_name 		= $wpdb->prefix . 'bp_profile_views';			
		$current_date 		= gmdate( 'Y-m-d' );
		$from_str     		= strtotime( '-' . ( 365 + 1 ) . ' days', strtotime( $current_date ) );
		$start_date   		= gmdate( 'Y-m-d', $from_str ). ' 00:00:00';
		$end_date     		= $current_date . ' 23:59:59';
		if ( isset( $settings['exclude_logout_user_count'] ) && 'yes' == $settings['exclude_logout_user_count'] ){			
			$weekly_results    	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d AND created BETWEEN %s AND %s;", $user_id, $start_date, $end_date ), ARRAY_A );
		} else {
			$weekly_results    	= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d AND created BETWEEN %s AND %s AND viewer_id != 0;", $user_id, $start_date, $end_date ), ARRAY_A );
		}
		$count       		= count( $weekly_results );		
		return $count;		
	}
}

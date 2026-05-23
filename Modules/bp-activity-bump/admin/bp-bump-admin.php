<?php
/**
 * BP activity bump admin function class file.
 *
 * @package bp-activity-bump
 * @subpackage bp-activity-bump\admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Add admin page for importing Review(s).
if ( ! class_exists( 'BP_ACTIVITY_BUMP_ADMIN_SETIING' ) ) {
	/**
	 * The admin-facing functionality of the plugin.
	 *
	 * @package bp-activity-bump
	 * @subpackage bp-activity-bump\admin
	 * @author     wbcomdesigns <admin@wbcomdesigns.com>
	 */
	class BP_ACTIVITY_BUMP_ADMIN_SETIING {

		/**
		 * Plugin tabs settings
		 *
		 * @var mixed
		 */
		private $plugin_settings_tabs;

		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'bp_bump_add_submenu_page_admin_settings' ) );
			add_action( 'admin_init', array( $this, 'bp_bump_plugin_settings' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'bp_bump_admin_enqueue_styles' ) );
			add_action( 'in_admin_header', array( $this, 'bp_bump_hide_all_admin_notices_from_setting_page' ) );

		}

		/**
		 * Hide all notices from the setting page.
		 *
		 * @return void
		 */
		public function bp_bump_hide_all_admin_notices_from_setting_page() {
			$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'bp-activity-bump-settings' );
			$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

			if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );
			}

		}

		/**
		 * Actions performed on loading admin_menu.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bp_bump_add_submenu_page_admin_settings() {
			if ( class_exists( 'BuddyPress' ) ) {
				if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
					add_menu_page( esc_html__( 'WB Plugins', 'bp-activity-bump' ), esc_html__( 'WB Plugins', 'bp-activity-bump' ), 'manage_options', 'wbcomplugins', array( $this, 'bupr_admin_options_page' ), 'dashicons-lightbulb', 59 );
					add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'bp-activity-bump' ), esc_html__( 'General', 'bp-activity-bump' ), 'manage_options', 'wbcomplugins' );

				}
				add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Activity Bump', 'bp-activity-bump' ), esc_html__( 'BP Activity Bump', 'bp-activity-bump' ), 'manage_options', 'bp-activity-bump-settings', array( $this, 'bupr_admin_options_page' ) );
			}
		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since 1.0.0
		 */
		public function bp_bump_admin_enqueue_styles() {
			wp_enqueue_style( 'bp-activity-bump-admin', plugin_dir_url( __FILE__ ) . 'assets/css/bp-activity-bump-admin.css', array(), BP_ACTIVITY_BUMP_VERSION, 'all' );
		}

		/**
		 * Actions performed on loading plugin settings
		 *
		 * @since    1.0.9
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bp_bump_plugin_settings() {
			$this->plugin_settings_tabs['bp-bump-welcome'] = esc_html__( 'Welcome', 'bp-activity-bump' );
			register_setting( 'bp_bump_admin_welcome_options', 'bp_bump_admin_welcome_options' );
			add_settings_section( 'bp-bump-welcome', ' ', array( $this, 'bp_bump_admin_welcome_content' ), 'bp-bump-welcome' );

			$this->plugin_settings_tabs['bp-bump-genral'] = esc_html__( 'General', 'bp-activity-bump' );
			register_setting( 'bp_bump_admin_general_options', 'bp_bump_admin_general_options' );
			add_settings_section( 'bp-bump-genral', ' ', array( $this, 'bp_bump_admin_general_content' ), 'bp-bump-genral' );
		}

		/**
		 * Include buddypress activity bump admin welcome setting tab content file.
		 */
		public function bp_bump_admin_welcome_content() {
			include 'tab-templates/bp-bump-welcome-page.php';
		}

		/**
		 * Include buddypress activity bump admin genral setting tab content file.
		 */
		public function bp_bump_admin_general_content() {
			include 'tab-templates/bp-bump-setting-general-tab.php';
		}

		/**
		 * Actions performed to create a submenu page content.
		 *
		 * @since    1.0.0
		 * @access public
		 */
		public function bupr_admin_options_page() {
			global $allowedposttags;
			$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bp-bump-welcome';
			?>
			<div class="wrap">
				<div class="wbcom-bb-plugins-offer-wrapper">
					<div id="wb_admin_logo">
						<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/" target="_blank">
							<img src="<?php echo esc_url( BP_ACTIVITY_BUMP_PLUGIN_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
						</a>
					</div>
				</div>
				<div class="wbcom-wrap">
					<div class="blpro-header">
						<div class="wbcom_admin_header-wrapper">
							<div id="wb_admin_plugin_name">
								<?php esc_html_e( 'BuddyPress Activity Bump', 'bp-activity-bump' ); ?>
								<span>
								<?php
								/* translators: %s: */
								printf( esc_html__( 'Version %s', 'bp-activity-bump' ), esc_attr( BP_ACTIVITY_BUMP_VERSION ) );
								?>
								</span>
							</div>
							<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
						</div>
					</div>
					<div class="wbcom-admin-settings-page">
						<?php
						settings_errors();
						$this->bupr_plugin_settings_tabs();
						settings_fields( $tab );
						do_settings_sections( $tab );
						?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Actions performed to create tabs on the sub menu page.
		 */
		public function bupr_plugin_settings_tabs() {
			$current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bp-bump-welcome';
			// xprofile setup tab.
			?>
			<style>
				.nav-tab-wrapper ul .bp-bump-genral a:before {
				content: "\f111" !important;
				}
			</style>
			<?php
			echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
				echo '<li class="' . esc_attr( $tab_key ) . '"><a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=bp-activity-bump-settings&tab=' . esc_attr( $tab_key ) . '">' . esc_attr( $tab_caption ) . '</a></li>';
			}
			echo '</div></ul></div>';
		}
	}
	new BP_ACTIVITY_BUMP_ADMIN_SETIING();
}

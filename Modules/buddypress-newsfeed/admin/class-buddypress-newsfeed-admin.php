<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Buddypress_Newsfeed_Admin {

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
		$admin_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Newsfeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Newsfeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( isset( $admin_page ) && ( 'buddypress_newsfeed' === $admin_page || 'wbcomplugins' === $admin_page || 'wbcom-plugins-page' === $admin_page || 'wbcom-support-page' === $admin_page || 'wbcom-license-page' === $admin_page ) ) {
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$extension = is_rtl() ? '.rtl.css' : '.css';
				$path      = is_rtl() ? '/rtl' : '';
			} else {
				$extension = is_rtl() ? '.rtl.css' : '.min.css';
				$path      = is_rtl() ? '/rtl' : '/min';
			}

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css' . $path . '/buddypress-newsfeed-admin' . $extension, array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$admin_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Newsfeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Newsfeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( isset( $admin_page ) && 'buddypress_newsfeed' === $admin_page ) {
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$extension = '.js';
				$path      = '';
			} else {
				$extension = '.min.js';
				$path      = '/min';
			}

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js' . $path . '/buddypress-newsfeed-admin' . $extension, array( 'jquery' ), $this->version, false );
		}

	}

	public function wbcom_hide_all_admin_notices_from_setting_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'buddypress_newsfeed' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Admin Menu.
	 */
	public function bnews_add_menu_buddypress_newsfeed() {
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {

			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-newsfeed' ), esc_html__( 'WB Plugins', 'buddypress-newsfeed' ), 'manage_options', 'wbcomplugins', array( $this, 'bnews_settings_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-newsfeed' ), esc_html__( 'General', 'buddypress-newsfeed' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Newsfeed Setting Page', 'buddypress-newsfeed' ), esc_html__( 'Newsfeed', 'buddypress-newsfeed' ), 'manage_options', 'buddypress_newsfeed', array( $this, 'bnews_settings_page' ) );
	}

	/**
	 * Admin Setting.
	 */
	public function bnews_settings_page() {
		$current = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
		?>
		<div class="wrap">
		<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
				</div>
			</div>
				<div class="wbcom-wrap">
				<div class="blpro-header">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">
							<?php esc_html_e( 'BuddyPress Newsfeed', 'buddypress-newsfeed' ); ?>
							<span><?php printf( esc_html__( 'Version %s', 'buddypress-newsfeed' ), esc_html( BNEWS_PLUGIN_VERSION ) ); ?></span>
						</div>
						<?php echo wp_kses_post( do_shortcode( '[wbcom_admin_setting_header]' ) ); ?>
					</div>
				</div>
					<div class="wbcom-admin-settings-page">
		<?php

		$bpht_tabs = array(
			'welcome' => esc_html__( 'Welcome', 'buddypress-newsfeed' ),
			'general' => esc_html__( 'General', 'buddypress-newsfeed' ),
			);

		$tab_html = '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html__( 'Menu', 'buddypress-newsfeed' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		$tab_fragments = []; // Collect fragments for better performance.
		foreach ( $bpht_tabs as $bpht_tab => $bpht_name ) {
			$class = ( $bpht_tab === $current ) ? 'nav-tab-active' : '';
			$tab_fragments[] = '<li class="' . esc_attr( $bpht_name ) . '"><a class="nav-tab ' . esc_attr( $class ) . '" href="admin.php?page=buddypress_newsfeed&tab=' . esc_attr( $bpht_tab ) . '">' . esc_html( $bpht_name ) . '</a></li>';
		}
		$tab_html .= implode( '', $tab_fragments ) . '</ul></div></div>';
		echo wp_kses_post( $tab_html );

		include 'inc/bnews-options-page.php';
		echo '</div>'; /* closing of div class wbcom-admin-settings-page */
		echo '</div>'; /* closing div class wbcom-wrap */
		echo '</div>'; /* closing div class wrap */
	}

	/**
	 * Register Admin Setting.
	 */
	public function bnews_add_admin_register_setting() {
		register_setting( 'bnews_general_settings_section', 'bnews_general_settings' ); // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingMissing
	}

}

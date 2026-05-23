<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Edit_Activity
 * @subpackage Buddypress_Edit_Activity/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Buddypress_Edit_Activity
 * @subpackage Buddypress_Edit_Activity/includes
 * @author     Wbcom Designs <info@wbcomdesign.com>
 */
class Buddypress_Edit_Activity {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Edit_Activity_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BUDDYPRESS_EDIT_ACTIVITY_VERSION' ) ) {
			$this->version = BUDDYPRESS_EDIT_ACTIVITY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-edit-activity';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Buddypress_Edit_Activity_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Edit_Activity_i18n. Defines internationalization functionality.
	 * - Buddypress_Edit_Activity_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Edit_Activity_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-edit-activity-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-edit-activity-i18n.php';

		/**
		 * The class responsible for add edd licence.
		 */		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'edd-license/edd-plugin-license.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-edit-activity-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-edit-activity-public.php';

		/**
		 * The class responsible for handling comment editing functionality
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-edit-activity-comments.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue wbcom paud plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';


		$this->loader = new Buddypress_Edit_Activity_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Edit_Activity_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Edit_Activity_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Buddypress_Edit_Activity_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'bp_register_admin_settings', $plugin_admin, 'bp_edit_activity_register_admin_settings',11 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bp_edit_activity_add_plugin_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bp_edit_activity_hide_all_admin_notices_from_setting_page' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Buddypress_Edit_Activity_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_action( 'bp_activity_entry_meta', $plugin_public, 'bp_edit_activity_btn_edit_activity' );
		$this->loader->add_filter( 'bb_nouveau_get_activity_entry_dropdown_toggle_buttons', $plugin_public, 'bp_edit_activity_btn_edit_activity_buttons', 10, 2 );
		
		if ( ! is_admin() && ! is_network_admin() ){						
			$this->loader->add_action( 'wp_footer', $plugin_public, 'bp_edit_activity_template',99 );
		}
		
				
		$this->loader->add_action( 'wp_ajax_buddypress_edit_activity_get', $plugin_public, 'buddypress_edit_activity_get_content' );
		$this->loader->add_action( 'wp_ajax_buddypress_edit_activity_save', $plugin_public, 'buddypress_edit_activity_save_content' );
		$this->loader->add_filter( 'bp_activity_time_since', $plugin_public, 'buddypress_bp_edit_activity_action', 50, 2 );
		
		$this->loader->add_action( 'bp_activity_after_comment_button', $plugin_public, 'buddypress_edit_activity_button', 99 );
		$this->loader->add_filter( 'bp_editable_types_activity', $plugin_public, 'bp_editable_youzify_types_activity' );
		
		if( class_exists( 'BuddyPress' ) && isset( buddypress()->buddyboss ) && bp_is_activity_edit_enabled() ) {

			$this->loader->add_filter( 'bb_nouveau_get_activity_entry_bubble_buttons', $plugin_public, 'bp_edit_activity_btn_edit_activity_buttons', 10,2 );
			$this->loader->add_filter( 'bp_get_activity_action', $plugin_public, 'bp_edit_activity_modify_action', 10,2 );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Buddypress_Edit_Activity_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

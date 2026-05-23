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
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/includes
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
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Newsfeed {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Newsfeed_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'BNEWS_PLUGIN_VERSION' ) ) {
			$this->version = BNEWS_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-newsfeed';

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
	 * - Buddypress_Newsfeed_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Newsfeed_i18n. Defines internationalization functionality.
	 * - Buddypress_Newsfeed_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Newsfeed_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-newsfeed-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/bnews-general-functions.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-newsfeed-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-newsfeed-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-newsfeed-public.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue wbcom license file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		$this->loader = new Buddypress_Newsfeed_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Newsfeed_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Newsfeed_i18n();

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

		$plugin_admin = new Buddypress_Newsfeed_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bnews_add_menu_buddypress_newsfeed' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bnews_add_admin_register_setting' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wbcom_hide_all_admin_notices_from_setting_page' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Buddypress_Newsfeed_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'bnews_setup_submenu_activity_newsfeed', 999 );
		$this->loader->add_filter( 'bp_actions', $plugin_public, 'bnews_newsfeed_activity_feed' );
		$this->loader->add_filter( 'bp_before_has_activities_parse_args', $plugin_public, 'bnews_alter_activities_parse_args', 10, 1 );
		$this->loader->add_action( 'admin_bar_menu', $plugin_public, 'bnews_update_wp_menus', 10 );
		$this->loader->add_action( 'admin_bar_menu', $plugin_public, 'bnews_admin_bar_menu', 99, 1 );
		$this->loader->add_action( 'bp_before_member_activity_post_form', $plugin_public, 'bnews_post_form' );
		$this->loader->add_filter( 'bp_activity_single_at_mentions_notification', $plugin_public, 'bnews_change_mention_notification_link_on_merge', 10, 5 );
		$this->loader->add_filter( 'bp_activity_multiple_at_mentions_notification', $plugin_public, 'bnews_change_mention_notification_link_on_merge', 10, 5 );

		// $this->loader->add_filter( 'bp_core_get_js_strings', $plugin_public, 'bpnewsfeed_element_activity_localize_scripts', 10, 1 );
		if ( 'buddyboss-theme-child' !== get_option( 'stylesheet' ) ) {
			// $this->loader->add_action( 'bp_nouveau_enqueue_scripts', $plugin_public, 'bnews_nouveau_activity_enqueue_scripts', 0 );
		}
		// $this->loader->add_filter( 'bp_nouveau_register_scripts', $plugin_public, 'bpnewsfeed_bp_nouveau_register_scripts', 10 );
		if ( isset( buddypress()->buddyboss ) ) {
			$this->loader->add_filter( 'bp_get_template_part', $plugin_public, 'bpnewsfeed_get_template_part', 10, 4 );			
		}else{
			$this->loader->add_filter( 'bp_after_has_activities_parse_args', $plugin_public, 'bpnewsfeed_filter_relevant_activity' );
		}

		// $this->loader->add_filter( 'bp_current_component', $plugin_public, 'bpnewsfeed_bp_current_component', 5, 1 );
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
	 * @return    Buddypress_Newsfeed_Loader    Orchestrates the hooks of the plugin.
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

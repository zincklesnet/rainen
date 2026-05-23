<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Schedule_Activity
 * @subpackage Buddypress_Schedule_Activity/includes
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
 * @package    Buddypress_Schedule_Activity
 * @subpackage Buddypress_Schedule_Activity/includes
 * @author     Wbcom Designs <contact@wbcomdesigns>
 */
class Buddypress_Schedule_Activity {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Schedule_Activity_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'BUDDYPRESS_SCHEDULE_ACTIVITY_VERSION' ) ) {
			$this->version = BUDDYPRESS_SCHEDULE_ACTIVITY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-schedule-activity';

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
	 * - Buddypress_Schedule_Activity_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Schedule_Activity_i18n. Defines internationalization functionality.
	 * - Buddypress_Schedule_Activity_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Schedule_Activity_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-buddypress-schedule-activity-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-buddypress-schedule-activity-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-buddypress-schedule-activity-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-buddypress-schedule-activity-public.php';

		/**
		 * The class responsible for adding  Wbcom wrapper for license settings
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/wbcom/wbcom-admin-settings.php';

		require_once plugin_dir_path( __DIR__ ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		/**
		 * The file contain license related code.
		 * */
		require_once plugin_dir_path( __DIR__ ) . 'edd-license/edd-plugin-license.php';

		$this->loader = new Buddypress_Schedule_Activity_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Schedule_Activity_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Schedule_Activity_i18n();

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

		$plugin_admin = new Buddypress_Schedule_Activity_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Buddypress_Schedule_Activity_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 999 );

		$this->loader->add_filter( 'bp_core_get_js_strings', $plugin_public, 'buddypress_schedule_activity_get_js_strings', 10 );
		$this->loader->add_action( 'bp_activity_post_form_options', $plugin_public, 'buddypress_schedule_activity_update_icon', 10 );

		// Conditionally remove and reassign actions if Youzify is active.
		add_action(
			'plugins_loaded',
			function () use ( $plugin_public ) {
				if ( class_exists( 'Youzify' ) ) {
					// Remove existing actions from the 'bp_activity_post_form_options' hook.
					remove_action( 'bp_activity_post_form_options', array( $plugin_public, 'buddypress_schedule_activity_update_icon' ), 10 );

					// Add actions to new hooks as per requirements.
					add_action( 'bp_activity_post_form_tools', array( $plugin_public, 'buddypress_schedule_activity_update_icon' ), 10 );
				}
			},
			20
		);

		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			$this->loader->add_action( 'bp_activity_post_form_options', $plugin_public, 'buddypress_schedule_activity_update_html', 999 );
		}

		/* Update Activity*/
		$this->loader->add_action( 'bp_activity_posted_update', $plugin_public, 'buddypress_schedule_activity_posted_update', 9999, 3 );
		$this->loader->add_action( 'bp_groups_posted_update', $plugin_public, 'buddypress_schedule_activity_groups_posted_update', 10, 4 );

		$this->loader->add_filter( 'bp_after_activity_get_parse_args', $plugin_public, 'buddypress_schedule_activity_meta_query' );
		$this->loader->add_filter( 'bp_after_activity_get_specific_parse_args', $plugin_public, 'buddypress_schedule_activity_meta_query' );
		$this->loader->add_filter( 'bp_activity_get_where_conditions', $plugin_public, 'buddypress_schedule_activity_where_conditions', 10, 5 );
		$this->loader->add_filter( 'bp_activity_has_more_items', $plugin_public, 'buddypress_schedule_activity_has_more_items', 999, 1 );
		$this->loader->add_filter( 'cron_schedules', $plugin_public, 'buddypress_schedule_activity_cron_schedules', 99, 1 );
		$this->loader->add_action( 'init', $plugin_public, 'buddypress_schedule_activity_init' );

		$this->loader->add_filter( 'youzify_activity_new_post_action', $plugin_public, 'buddypress_schedule_activity_youzify_activity_new_post_action', 10, 2 );

		$this->loader->add_action( 'buddypress_schedule_activity_publish', $plugin_public, 'buddypress_check_schedule_activity_publish' );

		$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'buddypress_schedule_activity_setup_nav', 100 );
		$this->loader->add_action( 'wp_ajax_delete_schedule_activity', $plugin_public, 'wp_ajax_delete_schedule_activity', 100 );
		$this->loader->add_action( 'wp_ajax_get_schedule_activity', $plugin_public, 'buddypress_get_load_more_schedule_activity' );
		$this->loader->add_filter( 'bp_ajax_querystring', $plugin_public, 'bp_schedule_activity_ajax_filter', 999, 2 );

		// Clean up schedule metadata when activity is deleted.
		$this->loader->add_action( 'bp_activity_before_delete', $plugin_public, 'buddypress_schedule_activity_cleanup_on_delete', 10, 1 );
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
	 * @return    Buddypress_Schedule_Activity_Loader    Orchestrates the hooks of the plugin.
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

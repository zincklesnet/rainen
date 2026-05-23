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
 * @package    Reign_Tutorlms_Addon
 * @subpackage Reign_Tutorlms_Addon/includes
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
 * @package    Reign_Tutorlms_Addon
 * @subpackage Reign_Tutorlms_Addon/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Reign_Tutorlms_Addon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Reign_Tutorlms_Addon_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		$this->version     = REIGN_TUTORLMS_ADDON_VERSION;
		$this->plugin_name = 'reign-tutorlms-addon';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		add_action( 'reign_other_premium_addon_license_panel', array( $this, 'reign_tutorlms_license_panel' ) );
		add_filter( 'body_class', array( $this, 'reign_tutorlms_body_class_custom_color' ) );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Reign_Tutorlms_Addon_Loader. Orchestrates the hooks of the plugin.
	 * - Reign_Tutorlms_Addon_i18n. Defines internationalization functionality.
	 * - Reign_Tutorlms_Addon_Admin. Defines all hooks for the admin area.
	 * - Reign_Tutorlms_Addon_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-reign-tutorlms-addon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-reign-tutorlms-addon-i18n.php';

		/**
		 * The file responsible for defining general functions
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/reign-tutor-lms-functions.php';

		/**
		 * The class responsible for license page for easy update plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/edd-license/edd-plugin-license.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-reign-tutorlms-addon-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-reign-tutorlms-addon-public.php';

		$this->loader = new Reign_Tutorlms_Addon_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Reign_Tutorlms_Addon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Reign_Tutorlms_Addon_i18n();

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

		$plugin_admin = new Reign_Tutorlms_Addon_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'alter_reign_admin_tabs', $plugin_admin, 'reign_tutorlms_reign_settings_tab', 15 );
		$this->loader->add_action( 'render_theme_options_page_for_tutorlms', $plugin_admin, 'reign_tutorlms_render_reign_options' );
		$this->loader->add_action( 'render_theme_options_for_reign_tutorlms_shortcodes', $plugin_admin, 'reign_tutorlms_render_shortcodes_options' );
		$this->loader->add_action( 'render_theme_options_for_reign_tutorlms_profile', $plugin_admin, 'reign_tutorlms_render_profile_options' );
		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'reign_tutorlms_save_reign_theme_settings' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Reign_Tutorlms_Addon_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 11 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
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
	 * @return    Reign_Tutorlms_Addon_Loader    Orchestrates the hooks of the plugin.
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

	/**
	 * The license code file.
	 */
	public function reign_tutorlms_license_panel() {
		echo '<div class="reign_license_page">';
		echo '<div class="reign-license-page-inner">';
			reign_tutorlms_addon_edd_license_page();
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Adds a custom body class based on Tutor LMS color preset type.
	 *
	 * @param array $classes Array of existing body classes.
	 * @return array Modified array of body classes.
	 */
	public function reign_tutorlms_body_class_custom_color( $classes ) {

		if ( function_exists( 'tutor' ) ) {

			if ( function_exists( 'get_tutor_option' ) && 'default' == get_tutor_option( 'color_preset_type' ) ) {
				$classes[] = 'reign-tutor-custom-colors';
			}

			return $classes;
		}
	}
}

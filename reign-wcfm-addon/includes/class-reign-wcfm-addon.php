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
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/includes
 */
// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Reign_Wcfm_Addon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Reign_Wcfm_Addon_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'REIGN_WCFM_ADDON_VERSION' ) ) {
			$this->version = REIGN_WCFM_ADDON_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'reign-wcfm-addon';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		if ( class_exists( 'BuddyPress' ) ) {
			$this->define_buddypress_hooks();
		}
		add_action( 'reign_other_premium_addon_license_panel', array( $this, 'reign_wcfm_vendor_license_panel' ) );

		/**
		 * Add reign wcfm store layout class to the WCFM store wrapper.
		 */
		add_filter( 'wcfm_store_wrapper_class', array( $this, 'reign_wcfm_add_store_layout_class' ) );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Reign_Wcfm_Addon_Loader. Orchestrates the hooks of the plugin.
	 * - Reign_Wcfm_Addon_i18n. Defines internationalization functionality.
	 * - Reign_Wcfm_Addon_Admin. Defines all hooks for the admin area.
	 * - Reign_Wcfm_Addon_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-reign-wcfm-addon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-reign-wcfm-addon-i18n.php';

		/**
		 * The class responsible license panel of pluign for easy updation.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/edd-license/edd-plugin-license.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-reign-wcfm-addon-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-reign-wcfm-addon-public.php';

		if ( class_exists( 'BuddyPress' ) ) {
			/**
			* The class responsible for defining all actions that occur in the buddypress components
			*/
			require_once plugin_dir_path( __DIR__ ) . 'public/buddypress/class-reign-wcfm-buddypress.php';
			/**
			*  These functions handle the recording, deleting and formatting of activity
			*/
			require_once plugin_dir_path( __DIR__ ) . 'public/buddypress/reign-wcfm-buddypress-activity.php';
		}

		$this->loader = new Reign_Wcfm_Addon_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Reign_Wcfm_Addon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Reign_Wcfm_Addon_i18n();

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

		$plugin_admin = new Reign_Wcfm_Addon_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	
		$this->loader->add_filter( 'alter_reign_admin_tabs', $plugin_admin, 'reign_wcfm_add_reign_tab', 15, 1 );
		$this->loader->add_action( 'render_theme_options_page_for_reign_wcfm', $plugin_admin, 'reign_wcfm_render_theme_options' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'reign_wcfm_save_settings' );
		$this->loader->add_action( 'render_theme_options_for_wcfm_general', $plugin_admin, 'reign_wcfm_render_genral_options' );
	

		$this->loader->add_action( 'render_theme_options_for_wcfm_single_store', $plugin_admin, 'reign_wcfm_render_store_options' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Reign_Wcfm_Addon_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 99 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'reign_wcfm_favourite_product_icon' );
		$this->loader->add_action( 'woocommerce_before_shop_loop_item_title', $plugin_public, 'reign_wcfm_favourite_product_icon' );
		$this->loader->add_action( 'wp_ajax_product_mark_favuorite', $plugin_public, 'reign_wcfm_do_mark_favuorite' );

		if ( class_exists( 'BuddyPress' ) ) {
			
			$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'reign_wcfm_buddypress_profile_tabs' );
			$this->loader->add_filter( 'woocommerce_shortcode_products_query', $plugin_public, 'reign_wcfm_favourite_products_query', 10, 3 );
			$this->loader->add_filter( 'woocommerce_shortcode_products_query_results', $plugin_public, 'reign_wcfm_check_products_query_results' );
			$this->loader->add_action( 'woocommerce_before_shop_loop', $plugin_public, 'reign_wcfm_add_visit_store_link_on_product_tab', 50 );
			$this->loader->add_action( 'bp_get_activity_content_body', $plugin_public, 'reign_wcfm_activity_content_body', 10 );
			$this->loader->add_action( 'bp_activity_entry_content', $plugin_public, 'reign_wcfm_order_activity_content' );
			$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'reign_wcfm_make_order_activity' );
			$this->loader->add_filter( 'youzify_activity_new_post_action', $plugin_public, 'reign_wcfm_youzify_order_activity_action' , 10, 2);
		}

		$this->loader->add_filter( 'wcfm_locate_template', $plugin_public, 'reign_wcfm_locate_template', 10, 4 );
	}

	/**
	 * Register all of the hooks related to the buddypress
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_buddypress_hooks() {
		$plugin_buddypress = new REIGN_WCFM_Buddypress( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'transition_post_status', $plugin_buddypress, 'reign_wcfm_add_product_creation_activity', 10, 3 );
		$this->loader->add_action( 'comment_post', $plugin_buddypress, 'reign_wcfm_product_review_approved', 100, 2 );
		$this->loader->add_action( 'wp_set_comment_status', $plugin_buddypress, 'reign_wcfm_product_review', 100, 2 );// Backup in case comment moderation is on
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
	 * @return    Reign_Wcfm_Addon_Loader    Orchestrates the hooks of the plugin.
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
	public function reign_wcfm_vendor_license_panel() {
		echo '<div class="reign_license_page">';
		echo '<div class="reign-license-page-inner">';
			reign_wcfm_addon_edd_license_page();
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Add reign wcfm store layout class to the WCFM store wrapper.
	 *
	 * @param string $classes Current classes applied to the store wrapper.
	 * @return string Modified classes with custom store layout class added.
	 */
	public function reign_wcfm_add_store_layout_class( $classes ) {
		global $wbtm_reign_settings;
		// Get the layout mods.
		$rg_store_layout = isset( $wbtm_reign_settings['wcfm_option']['reign_wcfm_store_layout'] ) ? $wbtm_reign_settings['wcfm_option']['reign_wcfm_store_layout'] : 'layout_one';

		// Add the layout class to the existing classes.
		$classes .= ' ' . $rg_store_layout;

		return $classes;
	}
}

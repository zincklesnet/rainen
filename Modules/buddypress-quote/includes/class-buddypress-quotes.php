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
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/includes
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
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Quotes {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Buddypress_Quotes_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'BPQUOTES_NAME_VERSION' ) ) {
			$this->version = BPQUOTES_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'buddypress-quotes';

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
	 * - Buddypress_Quotes_Loader. Orchestrates the hooks of the plugin.
	 * - Buddypress_Quotes_i18n. Defines internationalization functionality.
	 * - Buddypress_Quotes_Admin. Defines all hooks for the admin area.
	 * - Buddypress_Quotes_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-quotes-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-buddypress-quotes-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-buddypress-quotes-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-buddypress-quotes-public.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/bpquotes-functions.php';

		$this->loader = new Buddypress_Quotes_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Buddypress_Quotes_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Buddypress_Quotes_i18n();

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

		$plugin_admin = new Buddypress_Quotes_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bpquotes_add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpquotes_add_admin_register_setting' );
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
		global $current_user, $allow_user_role;
		$current_user 			= wp_get_current_user();
		$current_user_roles 	= $current_user->roles;
		$active_template 		= get_option( '_bp_theme_package_id' );	
		$bpquotes_gnrl_settings = get_option( 'bpquotes_gnrl_settings' );
		$user_role				= (isset($bpquotes_gnrl_settings['user_role']))? $bpquotes_gnrl_settings['user_role'] : [];
		$user_role				= array_merge(['administrator'], $user_role);
		$allow_user_role		= array_intersect($user_role, $current_user_roles);		
		
		$plugin_public = new Buddypress_Quotes_Public( $this->get_plugin_name(), $this->get_version() );
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		
		if( !empty($allow_user_role) ){
			
			if ( 'legacy' == $active_template ) {
				$this->loader->add_action( 'bp_activity_post_form_options', $plugin_public, 'bpquotes_activity_post_form_options', 20 );
				$this->loader->add_action( 'bp_activity_post_form_options', $plugin_public, 'bpquotes_activity_post_form_option_panel', 60 );
			} elseif ( 'nouveau' == $active_template ) {
				$this->loader->add_action( 'bp_activity_post_form_options', $plugin_public, 'bpquotes_activity_post_form_options', 20 );
				$this->loader->add_action( 'bp_activity_post_form_options', $plugin_public, 'bpquotes_activity_post_form_option_panel', 60 );
			}
		

			// Conditionally remove and reassign actions if Youzify is active.
			add_action(
				'plugins_loaded',
				function () use ( $plugin_public ) {
					if ( class_exists( 'Youzify' ) ) {
						// Remove existing actions from the 'bp_activity_post_form_options' hook.
						remove_action( 'bp_activity_post_form_options', array( $plugin_public, 'bpquotes_activity_post_form_options' ), 20 );
						remove_action( 'bp_activity_post_form_options', array( $plugin_public, 'bpquotes_activity_post_form_option_panel' ), 60 );

						// Add actions to new hooks as per requirements.
						add_action( 'bp_activity_post_form_tools', array( $plugin_public, 'bpquotes_activity_post_form_options' ), 10 );
						add_action( 'bp_activity_post_form_after_actions', array( $plugin_public, 'bpquotes_activity_post_form_option_panel' ), 60 );
					}
				},
				20
			);
		}
		// $this->loader->add_action( 'bp_activity_entry_content', $plugin_public, 'bpquotes_activity_post_form_options'  );
		/* update poll activity meta */
		$this->loader->add_action( 'bp_activity_posted_update', $plugin_public, 'bpquotes_update_quotes_activity_meta', 10, 4 );
		/* update group poll activity meta */
		$this->loader->add_action( 'bp_groups_posted_update', $plugin_public, 'bpquotes_update_quotes_activity_meta', 10, 4 );
		/* ypuzer update activity meta */
		$this->loader->add_action( 'yz_activity_posted_update', $plugin_public, 'bpquotes_update_quotes_activity_meta', 10, 4 );
		$this->loader->add_action( 'yz_groups_posted_update', $plugin_public, 'bpquotes_update_quotes_activity_meta', 10, 4 );
		$this->loader->add_action( 'yzea_activity_content', $plugin_public, 'bpquotes_update_yzea_activity_quotes', 10, 2 );

		/* update poll activity content */
		$this->loader->add_filter( 'bp_get_activity_content_body', $plugin_public, 'bpquotes_update_quotes_activity_content', 10, 2 );
		$this->loader->add_filter( 'bp_activity_get_embed_excerpt', $plugin_public, 'bpquotes_update_quotes_activity_content', 10, 2 );

		/* Embed quotes activity data in rest api */
		$this->loader->add_filter( 'bp_rest_activity_prepare_value', $plugin_public, 'bpquotes_activity_data_embed_rest_api', 10, 3 );
		
		$this->loader->add_shortcode( 'bp_quotes', $plugin_public, 'bpquotes_rest_api_shortcode' );
		
		$this->loader->add_action( 'bp_get_addition_activity_content', $plugin_public, 'bpquotes_get_activity_content' );
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
	 * @return    Buddypress_Quotes_Loader    Orchestrates the hooks of the plugin.
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

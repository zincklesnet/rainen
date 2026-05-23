<?php

// Exit if accessed directly
// It's a good idea to include this in each of your plugin files, for increased security on
// improperly configured servers
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load Translations
 * @since 1.0.0
 */

if ( file_exists( BP_CUSTOM_PAGES_PLUGIN_DIR . '/languages/' . get_locale() . '.mo' ) ) {
	load_textdomain( 'bp-custom-pages', BP_CUSTOM_PAGES_PLUGIN_DIR . '/languages/bp-custom-pages-' . get_locale() . '.mo' );
}

/**
 * @since 1.0.0
 */
class BP_Custom_Pages_Component extends BP_Component {

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		global $bp;

		parent::start(
			'bp-custom-pages',
			__( 'BP Custom Pages', 'bp-custom-pages' ),
			BP_CUSTOM_PAGES_PLUGIN_DIR
		);

		/**
		 * BuddyPress-dependent plugins are loaded too late to depend on BP_Component's
		 * hooks, so we must call the function directly.
		 */
		 $this->includes();

		/**
		 * Put your component into the active components array, so that
		 *   bp_is_active( 'custom-pages' );
		 * returns true when appropriate. We have to do this manually, because non-core
		 * components are not saved as active components in the database.
		 */
		$bp->active_components[ $this->id ] = '1';

		/**
		 * Hook the register_post_types() method. If you're using custom post types to store
		 * data (which is recommended), you will need to hook your function manually to
		 * 'init'.
		 */
		add_action( 'init', array( &$this, 'register_post_types' ) );
	}

	/**

	 * @since 1.0.0
	 */
	public function includes( $includes = array() ) {


		// As an example of how you might do it manually, let's include the functions used
		// on the WordPress Dashboard conditionally:
		// Files to include
		$includes = array(
			'includes/bp-custom-pages-screens.php',
			'includes/bp-custom-pages-template.php',
			'includes/bp-custom-pages-functions.php',
		);

		parent::includes( $includes );

		// As an example of how you might do it manually, let's include the functions used
		// on the WordPress Dashboard conditionally:
		if ( is_admin() || is_network_admin() ) {
			include( BP_CUSTOM_PAGES_PLUGIN_DIR . '/includes/bp-custom-pages-admin.php' );
		}
	}

	/**

	 * @since 1.0.0
	 *
	 * @global obj $bp BuddyPress's global object
	 */
	public function setup_globals( $args = array() ) {
		global $bp;

		// Defining the slug in this way makes it possible for site admins to override it
		if ( ! defined( 'BP_CUSTOM_PAGES_SLUG' ) ) {
			define( 'BP_CUSTOM_PAGES_SLUG', $this->id );
		}

		// Global tables for the example component. Build your table names using
		// $bp->table_prefix (instead of hardcoding 'wp_') to ensure that your component
		// works with $wpdb, multisite, and custom table prefixes.
		$global_tables = array(
			'table_name' => $bp->table_prefix . 'bp_custom_pages',
		);

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'                  => BP_CUSTOM_PAGES_SLUG,
			'root_slug'             => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : BP_CUSTOM_PAGES_SLUG,
			'has_directory'         => false, // Set to false if not required
			'notification_callback' => 'bp_custom_pages_format_notifications',
			'search_string'         => __( 'Search Custom Pages...', 'bp-custom-pages' ),
			'global_tables'         => $global_tables,
		);

		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $globals );
	}

	/**
	 * @since 1.0.0
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {
		$page_names = bp_custom_pages_get_pagenames();
		if ( count( $page_names ) === 0 ) {
			return;
		}
		$menu_name = get_option( 'bp-custom-pages-menu-name' );
		if ( $menu_name === false ) {
			$menu_name = 'Custom Pages';
		}
		// Add 'Custom Pages' to the main navigation
		$main_nav = array(
			'name'                => $menu_name,
			'slug'                => bp_get_custom_pages_slug(),
			'position'            => 80,
			'screen_function'     => 'bp_custom_pages_user_screen',
			'default_subnav_slug' => 'screen-one',
		);
		$args = array(
		  'numberposts' => 4,
		  'post_type'   => 'bp-custom-pages'
		);
		 
		$pages = get_posts( $args );

		$bp_custom_pages_link = trailingslashit( bp_displayed_user_domain() . bp_get_custom_pages_slug() );
		foreach ( $pages as $page ) {
			// Add a few subnav items under the main Example tab
			$sub_nav[] = array(
				'name'            => $page->post_title,
				'slug'            => $page->post_name,
				'parent_url'      => $bp_custom_pages_link,
				'parent_slug'     => bp_get_custom_pages_slug(),
				'screen_function' => 'bp_custom_pages_subpage_screen',
				'position'        => 10,
			);
		}
		
		parent::setup_nav( $main_nav, $sub_nav );

	}

	/**
	 * Set up your component's actions.
	 *
	 * This is the global setup action. It creates all the needed sutup for this component.
	 * You can add custom global action filters here; see BP_Pages_Component::setup_actions().
	 *
	 * @global obj $bp
	 */
	public function setup_actions() {
		parent::setup_actions();

		// add the high five send button to the members actions bar
		//add_action( 'bp_member_header_actions', 'bp_custom_pages_send_high_five_button', 20 );
	}

	/**
	 * @since 1.0.0
	 * @see http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_types() {
		// Set up some labels for the post type
		$labels = array(
			'name'     => __( 'BP Custom Pages', 'bp-custom-pages' ),
			'singular' => __( 'BP Custom Page', 'bp-custom-pages' ),
		);

		// Set up the argument array for register_post_type()
		$args = array(
			'label'    => __( 'BP Custom Pages', 'bp-custom-pages' ),
			'labels'   => $labels,
			'public'   => current_user_can( 'manage_options' ),
			'publicly_queryable'  	=> true,
			'exclude_from_search' 	=> true,
			'show_in_nav_menus'   	=> true,
			'show_ui'             	=> true,
			'show_in_menu'        	=> current_user_can( 'manage_options' ),
			'show_in_nav_menus'	  	=> false,
			'show_in_admin_bar'   	=> false,
			'show_in_rest'			=> true,
			'menu_position'       	=> 20,
			'has_archive'         	=> true,
			'query_var'           	=> false,
			'publish_posts'       => 'manage_options',
			'edit_others_posts'   => 'manage_options',
			'delete_posts'        => 'manage_options',
			'delete_others_posts' => 'manage_options',
			'read_private_posts'  => 'manage_options',
			'edit_post'           => 'manage_options',
			'delete_post'         => 'manage_options',
			'read_post'           => 'read',
			'supports' => array( 'title', 'editor' ),
		);

		// Register the post type.
		// Here we are using $this->id ('custom-pages') as the name of the post type. You may
		// choose to use a different name for the post type; if you register more than one,
		// you will have to declare more names.
		register_post_type( $this->id, $args );

		parent::register_post_types();
	}

	public function register_taxonomies() {

	}

}

/**
 * @since 1.0.0
 */
function bp_custom_pages_load_core_component() {
	global $bp;

	$bp->bp_custom_pages = new BP_Custom_Pages_Component;
}
add_action( 'bp_loaded', 'bp_custom_pages_load_core_component' );

function bppa_add_custom_post_types($query) {
    if ( $query->is_home() && $query->is_main_query() ) {
        $query->set( 'post_type', array( 'post', 'page', 'bp-custom-pages' ) );
    }
    return $query;
}
add_action('pre_get_posts', 'bppa_add_custom_post_types');

// Add to bp toolbar menu
function bp_custom_pages_admin_bar_add() {
	$page_names = bp_custom_pages_get_pagenames();
	if ( !is_user_logged_in() || count( $page_names ) === 0 ) {
		return;
	}
	$menu_name = get_option( 'bp-custom-pages-menu-name' );
	if ( $menu_name === false ) {
		$menu_name = 'Custom Pages';
	}

	global $wp_admin_bar, $bp;
 
	if ( defined( 'DOING_AJAX' ) )
		return;
	$pages_slug = bp_get_custom_pages_slug();
	$username = bp_get_loggedin_user_username();
	$user_domain = bp_loggedin_user_domain();
	$item_link = trailingslashit( home_url() . '/members/' . $username . '/' . $pages_slug . '/' );
 
	// add submenu item
	$wp_admin_bar->add_menu( array(
		'parent'  => 'my-account-xprofile',
		'id'      => 'my-account-' . $pages_slug,
		'title'   => $menu_name,
		'href'    => trailingslashit( $item_link ),
	) ); 
}

add_action( 'bp_setup_admin_bar', 'bp_custom_pages_admin_bar_add', 300 );

function bp_custom_pages_published_limit() {
    $max_posts = 4; // change this or set it as an option that you can retrieve.
    $author = $post->post_author; // Post author ID.

    $num_posts = wp_count_posts('bp-custom-pages', 'readable');

	$count = $num_posts->published;

    if ( $count > $max_posts ) {
        // count too high, let's set it to draft.

        $post = array('post_status' => 'draft');
        wp_update_post( $post );
    }
}
add_action( 'publish_bp_custom_pages', 'bp_custom_pages_published_limit' );

// Exclude posts from WP Query

function bp_custom_pages_exclude_posts($query) {
    if ( ! is_admin() && $query->is_main_query() ) {
        $args = array(
			'post_type'   => 'bp-custom-pages'
		);
		$posts_array = array();
		// Save the current post__not_in setting so we can add it back later
		$current_query = $query->query_vars['post__not_in'];
		
		$posts = get_posts( $args );
		if ( ! empty ( $posts ) ) {
			foreach ( $posts as $post_object ) {
				$posts_array[] = $post_object->ID;
			}
		}
		// Combine the list of excluded posts with the original post__not_in query.
		if ( ! empty( $current_query ) ) {
			
			$posts_array = array_merge( $posts_array, $current_query );
		
		}
		$query->set( 'post__not_in', $posts_array );
    }
}
add_action( 'pre_get_posts', 'bp_custom_pages_exclude_posts' );

<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Require plugin.php to use is_plugin_active() below.
if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/* Search results UX helpers (count header, type badges, term highlighting). */
require_once get_template_directory() . '/inc/search-results.php';

/**
 * Reign functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Reign
 */
if ( ! function_exists( 'reign_setup' ) ) {

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function reign_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Reign, use a find and replace
		 * to change 'reign' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'reign', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		add_theme_support( 'custom-logo' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		if ( function_exists( 'add_image_size' ) ) {

			add_image_size( 'reign-featured-large', 1200, 675 );
			add_image_size( 'reign-thumb', 600, 300 );
		}

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1'                 => esc_html__( 'Primary - Logged in users', 'reign' ),
				'menu-1-logout'          => esc_html__( 'Primary - Logged out users', 'reign' ),
				'panel-menu'             => esc_html__( 'Left Panel - Logged in users', 'reign' ),
				'panel-menu-loggedout'   => esc_html__( 'Left Panel - Logged out users', 'reign' ),
				'mobile-menu-logged-in'  => esc_html__( 'Mobile Menu - Logged in', 'reign' ),
				'mobile-menu-logged-out' => esc_html__( 'Mobile Menu - Logged out', 'reign' ),
				'menu-2'                 => esc_html__( 'User Profile', 'reign' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 * See http://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'gallery',
				'video',
				'audio',
				'quote',
				'link',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'wbcom_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style-editor.css' );

		// Add custom editor font sizes.
		add_theme_support( 'editor-font-sizes', array() );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_theme_support( 'woocommerce' );

		// Native AMP Support.
		if ( true === apply_filters( 'reign_amp_support', true ) ) {

			add_theme_support(
				'amp',
				apply_filters(
					'reign_amp_theme_features',
					array(
						'paired'             => true,
						'comments_live_list' => true,
					)
				)
			);
		}
	}

	add_action( 'after_setup_theme', 'reign_setup' );
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
if ( ! function_exists( 'reign_content_width' ) ) {

	function reign_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'wbcom_content_width', 640 );
	}

	add_action( 'after_setup_theme', 'reign_content_width', 0 );
}

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
if ( ! function_exists( 'reign_widgets_init' ) ) {

	function reign_widgets_init() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Right Sidebar', 'reign' ),
				'id'            => 'sidebar-right',
				'description'   => esc_html__( 'Widgets in this area are used in the right sidebar region.', 'reign' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h4 class="widget-title"><span>',
				'after_title'   => '</span></h4>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'Left Sidebar', 'reign' ),
				'id'            => 'sidebar-left',
				'description'   => esc_html__( 'Widgets in this area are used in the left sidebar region.', 'reign' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h4 class="widget-title"><span>',
				'after_title'   => '</span></h4>',
			)
		);

		if ( class_exists( 'WooCommerce' ) ) {
			register_sidebar(
				array(
					'name'          => esc_html__( 'WooCommerce Right Sidebar', 'reign' ),
					'id'            => 'woocommerce-sidebar-right',
					'description'   => esc_html__( 'Widgets in this area are used in the woocommerce right sidebar region.', 'reign' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);

			register_sidebar(
				array(
					'name'          => esc_html__( 'WooCommerce Left Sidebar', 'reign' ),
					'id'            => 'woocommerce-sidebar-left',
					'description'   => esc_html__( 'Widgets in this area are used in the woocommerce left sidebar region.', 'reign' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);

			// Off Canvas Sidebar.
			if ( reign_is_truthy( get_theme_mod( 'reign_woo_off_canvas_filter', false ) ) ) {
				register_sidebar(
					array(
						'name'          => esc_html__( 'Off Canvas Sidebar', 'reign' ),
						'id'            => 'reign_off_canvas_sidebar',
						'description'   => esc_html__( 'Widgets in this area are used in the woocommerce Off Canvas sidebar region.', 'reign' ),
						'before_widget' => '<section id="%1$s" class="widget %2$s">',
						'after_widget'  => '</section>',
						'before_title'  => '<h4 class="widget-title"><span>',
						'after_title'   => '</span></h4>',
					)
				);
			}
		}

		// LearnDash Sidebar
		if ( ! class_exists( 'LearnMate_LearnDash_Addon' ) && class_exists( 'SFWD_LMS' ) ) {
			register_sidebar(
				array(
					'name'          => sprintf(esc_html_x('LD Single %s Sidebar', 'Single %s Sidebar Label', 'reign'), LearnDash_Custom_Label::get_label('course')),
					'id'            => 'ld-single-course-sidebar',
					'description'   => sprintf(esc_html_x('Widgets in this area are used in the learndash single %s page.', 'Single %s Sidebar Description Label', 'reign'), LearnDash_Custom_Label::get_label('course')),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);

			register_sidebar(
				array(
					'name'          => sprintf(esc_html_x('LD Single %s Sidebar', 'Single %s Sidebar Label', 'reign'), LearnDash_Custom_Label::get_label('group')),
					'id'            => 'ld-single-group-sidebar',
					'description'   => sprintf(esc_html_x('Widgets in this area are used in the learndash single %s page.', 'Single %s Sidebar Description Label', 'reign'), LearnDash_Custom_Label::get_label('group')),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);
		}

		if ( class_exists( 'WC_Vendors' ) ) {
			register_sidebar(
				array(
					'name'          => esc_html__( 'Single Store Sidebar', 'reign' ),
					'id'            => 'wcvendors-store-sidebar',
					'description'   => esc_html__( 'Widgets in this area are used in the wc vendors store sidebar region.', 'reign' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);
		}

		/* Dedicated widget support for BuddyPress */
		if ( class_exists( 'BuddyPress' ) ) {
			if ( ! class_exists( 'Youzify' ) ) {
				register_sidebar(
					array(
						'name'          => esc_html__( 'Groups Index', 'reign' ),
						'id'            => 'group-index',
						'description'   => esc_html__( 'Add widgets here.', 'reign' ),
						'before_widget' => '<section id="%1$s" class="widget %2$s">',
						'after_widget'  => '</section>',
						'before_title'  => '<h4 class="widget-title"><span>',
						'after_title'   => '</span></h4>',
					)
				);

				register_sidebar(
					array(
						'name'          => esc_html__( 'Member Index', 'reign' ),
						'id'            => 'member-index',
						'description'   => esc_html__( 'Add widgets here.', 'reign' ),
						'before_widget' => '<section id="%1$s" class="widget %2$s">',
						'after_widget'  => '</section>',
						'before_title'  => '<h4 class="widget-title"><span>',
						'after_title'   => '</span></h4>',
					)
				);

				register_sidebar(
					array(
						'name'          => esc_html__( 'Activity Index', 'reign' ),
						'id'            => 'activity-index',
						'description'   => esc_html__( 'Add widgets here.', 'reign' ),
						'before_widget' => '<section id="%1$s" class="widget %2$s">',
						'after_widget'  => '</section>',
						'before_title'  => '<h4 class="widget-title"><span>',
						'after_title'   => '</span></h4>',
					)
				);

				register_sidebar(
					array(
						'name'          => esc_html__( 'Group Single', 'reign' ),
						'id'            => 'group-single',
						'description'   => esc_html__( 'Add widgets here.', 'reign' ),
						'before_widget' => '<section id="%1$s" class="widget %2$s">',
						'after_widget'  => '</section>',
						'before_title'  => '<h4 class="widget-title"><span>',
						'after_title'   => '</span></h4>',
					)
				);

				register_sidebar(
					array(
						'name'          => esc_html__( 'Member Profile', 'reign' ),
						'id'            => 'member-profile',
						'description'   => esc_html__( 'Add widgets here.', 'reign' ),
						'before_widget' => '<section id="%1$s" class="widget %2$s">',
						'after_widget'  => '</section>',
						'before_title'  => '<h4 class="widget-title"><span>',
						'after_title'   => '</span></h4>',
					)
				);
			}
		}

		/* Dedicated widget support for EDD */
		if ( class_exists( 'Easy_Digital_Downloads' ) ) {

			register_sidebar(
				array(
					'name'          => esc_html__( 'Download Archive Sidebar', 'reign' ),
					'id'            => 'edd-download-archive-sidebar',
					'description'   => esc_html__( 'Widgets in this area are used in the EDD download archive page.', 'reign' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);

			register_sidebar(
				array(
					'name'          => esc_html__( 'Single Download Sidebar', 'reign' ),
					'id'            => 'edd-single-download-sidebar',
					'description'   => esc_html__( 'Widgets in this area are used in the EDD single download page.', 'reign' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);
		}

		if ( defined( 'FLUENTCART_PLUGIN_FILE_PATH' ) ) {
			register_sidebar(
				array(
					'name'          => esc_html__( 'Product Left Sidebar', 'reign' ),
					'id'            => 'fluentcart-sidebar-left',
					'description'   => esc_html__( 'Widgets in this area are used in the fluentcart left sidebar region.', 'reign' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);

			register_sidebar(
				array(
					'name'          => esc_html__( 'Product Right Sidebar', 'reign' ),
					'id'            => 'fluentcart-sidebar-right',
					'description'   => esc_html__( 'Widgets in this area are used in the fluentcart right sidebar region.', 'reign' ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);
		}

		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer Widget Area', 'reign' ),
				'id'            => 'footer-widget-area',
				'description'   => esc_html__( 'Add widgets here.', 'reign' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s wb-grid-cell">',
				'after_widget'  => '</section>',
				'before_title'  => '<h4 class="widget-title"><span>',
				'after_title'   => '</span></h4>',
			)
		);

		// For PeepSo notification icons.
		if ( class_exists( 'PeepSo' ) ) {
			register_sidebar(
				array(
					'name'          => esc_html__( 'Header Widget Area', 'reign' ),
					'id'            => 'reign-header-widget-area',
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);
		}
	}

	add_action( 'widgets_init', 'reign_widgets_init' );
}

// Flickity CSS Enqueue.
if ( ! function_exists( 'reign_flickity_load_slider_css' ) ) {
	function reign_flickity_load_slider_css() {
		if ( ! class_exists( 'BuddyPress' ) ) {
			return false;
		}

		$should_load = (
			( bp_is_user() ) ||
			( bp_is_group() && ! bp_is_group_create() && ! bp_is_groups_directory() )
		);

		return apply_filters( 'reign_flickity_load_slider_css', $should_load );
	}
}

// Flickity JS File.
if ( ! function_exists( 'reign_flickity_load_slider_js' ) ) {
	function reign_flickity_load_slider_js() {
		if ( ! class_exists( 'BuddyPress' ) ) {
			return false;
		}

		$should_load = (
			( bp_is_user() ) ||
			( bp_is_group() && ! bp_is_group_create() && ! bp_is_groups_directory() )
		);

		return apply_filters( 'reign_flickity_load_slider_js', $should_load );
	}
}

if ( ! function_exists( 'check_masonry_view_in_shortcode' ) ) {
	/**
	 * Checks if the content contains the 'reign_display_posts' shortcode with 'masonry-view' layout.
	 *
	 * @param string $content The content to check for the shortcode.
	 * @return bool True if the shortcode with 'masonry-view' is found, false otherwise.
	 */
	function check_masonry_view_in_shortcode( $content ) {
		$pattern = get_shortcode_regex( array( 'reign_display_posts' ) );
		if ( preg_match_all( '/' . $pattern . '/s', $content, $matches )
			&& array_key_exists( 2, $matches )
			&& in_array( 'reign_display_posts', $matches[2] ) ) {
			$key  = array_search( 'reign_display_posts', $matches[2] );
			$atts = shortcode_parse_atts( $matches[3][ $key ] );
			if ( isset( $atts['posts_view'] ) && $atts['posts_view'] === 'masonry-view' ) {
				return true;
			}
		}
		return false;
	}
}

/**
 * Enqueue scripts and styles.
 */
if ( ! function_exists( 'reign_scripts' ) ) {

	function reign_scripts() {

		$post    = get_post();
		$rtl_css = is_rtl() ? '-rtl' : '';

		// Styles.
		global $wbtm_reign_settings;

		wp_deregister_style( 'font-awesome' );
		wp_deregister_style( 'font-awesome-shims' );

		if ( reign_flickity_load_slider_css() ) {
			wp_enqueue_style( 'reign-flickity', get_template_directory_uri() . '/assets/css/vendors/flickity.css', '', REIGN_THEME_VERSION );
		}

		// Enqueue separate button CSS to avoid conflicts with third-party plugins like Dokan
		if ( ! class_exists( 'WeDevs_Dokan' ) && ! defined( 'FLUENTCART_PLUGIN_FILE_PATH' ) ) {
			wp_enqueue_style( 'reign-button', get_template_directory_uri() . '/assets/css' . $rtl_css . '/button.min.css', '', REIGN_THEME_VERSION );
		}

		// EDD Main CSS Enqueue - Only load on EDD-related pages.
		if ( function_exists( 'reign_should_load_edd_assets' ) && reign_should_load_edd_assets() ) {
			wp_enqueue_style( 'reign_edd_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/edd-main.min.css', '', REIGN_THEME_VERSION );
		}

		// PMPRO Main CSS Enqueue - Only load on PMPRO pages.
		if ( function_exists( 'reign_should_load_pmpro_assets' ) && reign_should_load_pmpro_assets() ) {
			wp_enqueue_style( 'reign-pmpro', get_template_directory_uri() . '/assets/css' . $rtl_css . '/pmpro-main.min.css', '', REIGN_THEME_VERSION );
		}

		// bbPress Main CSS Enqueue - Only load on forum-related pages.
		if ( function_exists( 'reign_should_load_bbpress_assets' ) && reign_should_load_bbpress_assets() ) {
			wp_enqueue_style( 'reign_bbpress_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/bbpress-main.min.css', '', REIGN_THEME_VERSION );
		}

		// wpForo Main CSS Enqueue - Only load on wpForo pages.
		if ( function_exists( 'reign_should_load_wpforo_assets' ) && reign_should_load_wpforo_assets() ) {
			wp_enqueue_style( 'reign_wpforo_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wpforo-main.min.css', '', REIGN_THEME_VERSION );
		}

		// PeepSo Main CSS Enqueue - Only load on PeepSo-related pages.
		if ( function_exists( 'reign_should_load_peepso_assets' ) && reign_should_load_peepso_assets() ) {
			wp_enqueue_style( 'reign_peepso_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/peepso-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Events Calendar Main CSS Enqueue - Only load on event-related pages.
		if ( function_exists( 'reign_should_load_events_calendar_assets' ) && reign_should_load_events_calendar_assets() ) {
			wp_enqueue_style( 'reign_eventscalendar_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/eventscalendar-main.min.css', '', REIGN_THEME_VERSION );
		}

		// rtMedia CSS Enqueue - Only load on relevant pages.
		if ( function_exists( 'reign_should_load_rtmedia_assets' ) && reign_should_load_rtmedia_assets() ) {
			wp_enqueue_style( 'reign-rtmedia-style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/rtmedia-main.min.css', '', REIGN_THEME_VERSION );
		}

		// BP Better Messages CSS Enqueue - Only load on relevant pages.
		if ( function_exists( 'reign_should_load_bp_better_messages_assets' ) && reign_should_load_bp_better_messages_assets() ) {
			wp_enqueue_style( 'reign-bp-better-messages-style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/bp-better-messages-main.min.css', '', REIGN_THEME_VERSION );
		}

		// BuddyPress Package Condition.
		if ( function_exists( 'bp_get_theme_package_id' ) ) {
			$theme_package_id = bp_get_theme_package_id();
		} else {
			$theme_package_id = 'legacy';
		}

		// Header Icons CSS Enqueue - Load globally for all header dropdowns (BP + WooCommerce + others).
		wp_enqueue_style( 'reign-header-icons', get_template_directory_uri() . '/assets/css' . $rtl_css . '/header-bp-icons.min.css', '', REIGN_THEME_VERSION );

		// BuddyPress Nouveau Main CSS Enqueue - Only load on BuddyPress pages.
		if ( function_exists( 'reign_should_load_buddypress_assets' ) && reign_should_load_buddypress_assets() ) {
			if ( 'nouveau' === $theme_package_id ) {
				wp_enqueue_style( 'reign_nouveau_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/nouveau-main.min.css', '', REIGN_THEME_VERSION );

				if ( ( function_exists( 'bp_is_user' ) && bp_is_user() ) || ( function_exists( 'bp_is_group' ) && bp_is_group() && bp_is_group_single() ) ) {
					wp_enqueue_style( 'reign-nouveau-menu-icons', get_template_directory_uri() . '/assets/css' . $rtl_css . '/nouveau-menu-icons.min.css', '', REIGN_THEME_VERSION );
				}
			}
		}

		// BB Platform Main CSS Enqueue - Only load on BuddyBoss pages.
		if ( function_exists( 'reign_should_load_bb_platform_assets' ) && reign_should_load_bb_platform_assets() ) {
			wp_enqueue_style( 'reign_bb_platform', get_template_directory_uri() . '/assets/css' . $rtl_css . '/bb-platform-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site Youzify CSS Enqueue - Only load on BuddyPress/profile pages.
		if ( function_exists( 'reign_should_load_youzify_assets' ) && reign_should_load_youzify_assets() ) {
			wp_enqueue_style( 'reign_youzify', get_template_directory_uri() . '/assets/css' . $rtl_css . '/youzify-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site WP_Event_Manager CSS Enqueue - Only load on event pages.
		if ( function_exists( 'reign_should_load_wp_event_manager_assets' ) && reign_should_load_wp_event_manager_assets() ) {
			wp_enqueue_style( 'wp_event_manager', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wp-event-manager.min.css', '', REIGN_THEME_VERSION );
		}

		// Site Directories CSS Enqueue - Only load on directory pages.
		if ( function_exists( 'reign_should_load_directorist_assets' ) && reign_should_load_directorist_assets() ) {
			wp_enqueue_style( 'reign_directorist', get_template_directory_uri() . '/assets/css' . $rtl_css . '/directorist-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site Dokan CSS Enqueue - Only load on vendor/WooCommerce pages.
		if ( function_exists( 'reign_should_load_dokan_assets' ) && reign_should_load_dokan_assets() ) {
			wp_enqueue_style( 'reign_dokan', get_template_directory_uri() . '/assets/css' . $rtl_css . '/dokan-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site LearnDash CSS Enqueue - Only load on LearnDash pages and pages with widgets.
		if ( function_exists( 'reign_should_load_learndash_assets' ) && reign_should_load_learndash_assets() ) {
			wp_enqueue_style( 'reign_learndash', get_template_directory_uri() . '/assets/css' . $rtl_css . '/learndash-main.min.css', '', REIGN_THEME_VERSION );
			wp_enqueue_style( 'reign_learndash_dark', get_template_directory_uri() . '/assets/css' . $rtl_css . '/learndash-dark.min.css', '', REIGN_THEME_VERSION );
		}

		// LearnDash Dashboard CSS - Only load on pages using [ld_dashboard].
		if ( function_exists( 'reign_should_load_ld_dashboard_assets' ) && reign_should_load_ld_dashboard_assets() ) {
			wp_enqueue_style( 'reign_ld_dashboard', get_template_directory_uri() . '/assets/css' . $rtl_css . '/ld-dashboard-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site Sensei LMS CSS Enqueue - Only load on Sensei pages.
		if ( function_exists( 'reign_should_load_sensei_assets' ) && reign_should_load_sensei_assets() ) {
			wp_enqueue_style( 'reign_sensei', get_template_directory_uri() . '/assets/css' . $rtl_css . '/sensei-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site WC Vendors CSS Enqueue - Only load on vendor/WooCommerce pages.
		if ( function_exists( 'reign_should_load_wc_vendors_assets' ) && reign_should_load_wc_vendors_assets() ) {
			wp_enqueue_style( 'reign_wcvendors', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wc-vendors-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site WCFM CSS Enqueue - Only load on WCFM/WooCommerce pages.
		if ( function_exists( 'reign_should_load_wcfm_assets' ) && reign_should_load_wcfm_assets() ) {
			wp_enqueue_style( 'reign_wcfm', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wcfm-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site MultivendorX CSS Enqueue - Only load on MultivendorX/WooCommerce pages.
		if ( function_exists( 'reign_should_load_multivendorx_assets' ) && reign_should_load_multivendorx_assets() ) {
			wp_enqueue_style( 'reign_multivendorx', get_template_directory_uri() . '/assets/css' . $rtl_css . '/multivendorx-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site LifterLMS Main CSS Enqueue - Only load on LifterLMS pages and pages with widgets.
		if ( function_exists( 'reign_should_load_lifterlms_assets' ) && reign_should_load_lifterlms_assets() && ! class_exists( 'Reign_LifterLMS_Addon' ) ) {
			wp_enqueue_style( 'reign_lifterlms', get_template_directory_uri() . '/assets/css' . $rtl_css . '/lifterlms-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site TutorLMS Main CSS Enqueue - Only load on TutorLMS pages.
		if ( function_exists( 'reign_should_load_tutorlms_assets' ) && reign_should_load_tutorlms_assets() ) {
			wp_enqueue_style( 'reign_tutorlms', get_template_directory_uri() . '/assets/css' . $rtl_css . '/tutorlms-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site GeoDirectory Main CSS Enqueue - Only load on GeoDirectory pages.
		if ( function_exists( 'reign_should_load_geodirectory_assets' ) && reign_should_load_geodirectory_assets() ) {
			wp_enqueue_style( 'reign_geodirectory', get_template_directory_uri() . '/assets/css' . $rtl_css . '/geodirectory-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Site WP Job Manager CSS Enqueue - Only load on job-related pages.
		if ( function_exists( 'reign_should_load_wp_job_manager_assets' ) && reign_should_load_wp_job_manager_assets() ) {

			wp_enqueue_style( 'reign-wp-job-manager', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wp-job-manager-main.min.css', '', REIGN_THEME_VERSION );

			if ( ! class_exists( 'Reign_WP_Job_Manager_Addon' ) ) {
				wp_enqueue_style( 'reign-wp-job-manager-layout', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wp-job-manager-layout-main.min.css', '', REIGN_THEME_VERSION );
			}
		}

		// WooCommerce Main CSS Enqueue - Only load on WooCommerce pages.
		if ( function_exists( 'reign_should_load_woocommerce_assets' ) && reign_should_load_woocommerce_assets( 'css' ) ) {
			wp_enqueue_style( 'reign_woocommerce_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/woocommerce-main.min.css', '', REIGN_THEME_VERSION );
		}

		// SureCart Main CSS Enqueue - Only load on SureCart pages.
		if ( function_exists( 'reign_should_load_surecart_assets' ) && reign_should_load_surecart_assets() ) {
			wp_enqueue_style( 'reign-surecart', get_template_directory_uri() . '/assets/css' . $rtl_css . '/surecart-main.min.css', '', REIGN_THEME_VERSION );
		}

		// FluentCart Main CSS Enqueue - Only load on FluentCart pages.
		if ( function_exists( 'reign_should_load_fluentcart_assets' ) && reign_should_load_fluentcart_assets() ) {
			wp_enqueue_style( 'reign-fluentcart', get_template_directory_uri() . '/assets/css' . $rtl_css . '/fluentcart-main.min.css', '', REIGN_THEME_VERSION );
		}

		// Roadmap PrductRoadmap Main CSS Enqueue - Only load on PrductRoadmap pages.
		if ( function_exists( 'reign_should_load_product_roadmap_assets' ) && reign_should_load_product_roadmap_assets() ) {
			wp_enqueue_style( 'reign-product-roadmap', get_template_directory_uri() . '/assets/css' . $rtl_css . '/product-roadmap.min.css', '', REIGN_THEME_VERSION );
		}

		// Shoplentor (WooLentor) Main CSS Enqueue - Only load on Shoplentor pages.
		if ( function_exists( 'reign_should_load_shoplentor_assets' ) && reign_should_load_shoplentor_assets() ) {
			wp_enqueue_style( 'reign-shoplentor', get_template_directory_uri() . '/assets/css' . $rtl_css . '/shoplentor.min.css', '', REIGN_THEME_VERSION );
		}

		// Stachethemes Event Calendar CSS - Only load plugin is active.
		if ( function_exists( 'reign_should_load_stachethemes_assets' ) && reign_should_load_stachethemes_assets() ) {
			wp_enqueue_style( 'reign-stachethemes-event-calendar', get_template_directory_uri() . '/assets/css' . $rtl_css . '/stachethemes-event-calendar.min.css', array(), REIGN_THEME_VERSION );
		}

		// Font Awesome Files Enqueue — split delivery:
		// 1. reign-icons-core: @font-face + base classes + every glyph the theme
		//    itself renders (header icons incl. BP notification/message dropdowns,
		//    template-parts, common nav/social picks). Tiny (~13KB), render-blocking
		//    so above-the-fold icons paint immediately.
		// 2. font-awesome (full 4,800-glyph map, ~180KB): still ships complete —
		//    the menu icon picker and the customizable header icon sets let site
		//    owners choose ANY glyph, so subsetting it would break customer icons.
		//    Loaded deferred (preload + onload swap via reign_defer_style_handles)
		//    since the core file already covers everything visible at first paint.
		wp_enqueue_style( 'reign-icons-core', get_template_directory_uri() . '/assets/font-awesome/css/reign-icons-core.min.css', '', '6.6.0' );
		wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/font-awesome/css/fontawesome-optimized.min.css', array( 'reign-icons-core' ), '6.6.0' );

		// Load theme font.
		wp_enqueue_style( 'theme-font', get_template_directory_uri() . '/assets/fonts/fonts.css', '', REIGN_THEME_VERSION );

		// Site Main CSS Enqueue — critical/deferred split when Smart
		// Performance Mode is ON: reign-critical (~109KB shell: base +
		// grid/layout/header/nav + dynamic colors, composed from the SAME
		// partials as main so rules are byte-identical) loads blocking;
		// the full main.min.css (292KB) is deferred via
		// reign_defer_style_tag. Toggle OFF = classic single blocking bundle.
		if ( reign_split_main_css() ) {
			wp_enqueue_style( 'reign-critical', get_template_directory_uri() . '/assets/css' . $rtl_css . '/critical.min.css', '', REIGN_THEME_VERSION );
		}
		wp_enqueue_style( 'reign_main_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/main.min.css', '', REIGN_THEME_VERSION );

		// Dark mode is now handled by the new Color_Mode_Toggle component
		// (inc/Color_Mode_Toggle/Component.php) and the Tokens system
		// (inc/Tokens/Component.php) emitting :root[data-bx-mode="dark"]
		// CSS variable overrides. The old wp-dark-mode.js enqueue + the
		// reign_dark_mode_option gate are retired in 8.0.0.
		//
		// Customer values are preserved via the v8_dark_mode_toggle
		// migration filter in inc/Customizer_Settings/migrations.php which
		// copies reign_dark_mode_option -> site_color_mode_toggle_show and
		// reign_dark_mode_default -> site_color_mode on init priority 1.

		if ( reign_flickity_load_slider_js() ) {
			wp_enqueue_script( 'reign-flickity', get_template_directory_uri() . '/assets/js/vendors/flickity.pkgd.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		/*
		 * Vendor JS — selectively loaded (8.0.0 perf pass).
		 *
		 * main.js now wraps every vendor-lib call in a `typeof` feature
		 * guard (e.g. `if ( typeof $.fn.fitVids === 'function' )`), so a
		 * lib that is not enqueued on a given page no longer produces a
		 * runtime TypeError. That makes it safe to gate the heavier /
		 * page-specific libs below.
		 *
		 * Globally enqueued (used on essentially every front-end page, or
		 * by multiple conditionally-loaded bundles):
		 *   - fitvids        : main.js runs `$('body').fitVids()` on every page.
		 *   - slick (43 KB)  : main.js gallery archives + the WooCommerce /
		 *                       BuddyPress / EDD / PeepSo / LifterLMS bundles
		 *                       all call `.slick()`. Too broadly used to gate
		 *                       reliably; the typeof guards make future gating
		 *                       safe without risking a slider regression.
		 *   - doubletaptogo  : main.js mobile menu wires it on every page.
		 *
		 * Conditionally enqueued (gated to where the consuming code runs):
		 *   - more-menu / jquery.cookie : only consumed by reign-buddypress.js
		 *                                 (BP subnav "more" + BP filter cookies),
		 *                                 which itself only loads on BP pages.
		 *   - sticky-sidebar : main.js `theiaStickySidebar()` only acts on
		 *                       `body.reign-sticky-sidebar` (the reign_sticky_sidebar
		 *                       switch). Gate on the same theme_mod.
		 *   - sticky-kit     : main.js `stick_in_parent()` only targets the
		 *                       single-post social-share box, which renders on
		 *                       singular content. Gate on is_singular().
		 */
		wp_enqueue_script( 'reign-fitvids', get_template_directory_uri() . '/assets/js/vendors/fitvids.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		wp_enqueue_script( 'reign-slick', get_template_directory_uri() . '/assets/js/vendors/slick.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		wp_enqueue_script( 'reign-doubletaptogo', get_template_directory_uri() . '/assets/js/vendors/jquery.doubletaptogo.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );

		// more-menu + jquery.cookie are only used by reign-buddypress.js, which
		// is enqueued on BuddyPress pages. Load them only there.
		if ( function_exists( 'reign_should_load_buddypress_assets' ) && reign_should_load_buddypress_assets() ) {
			wp_enqueue_script( 'reign-more-menu', get_template_directory_uri() . '/assets/js/vendors/more-menu.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
			wp_enqueue_script( 'reign-jquery-cookie', get_template_directory_uri() . '/assets/js/vendors/jquery.cookie.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// sticky-sidebar (theiaStickySidebar) is needed when the Sticky Sidebar
		// option is on (main.js sticks the widget area), or a WooCommerce page is
		// loaded (reign-woocommerce.js sticks the review form regardless of the
		// theme option). reign-woocommerce.min.js is not typeof-guarded, so keep
		// the lib present on WC pages.
		if (
			reign_is_truthy( get_theme_mod( 'reign_sticky_sidebar', true ) )
			|| ( function_exists( 'reign_should_load_woocommerce_assets' ) && reign_should_load_woocommerce_assets( 'js' ) )
		) {
			wp_enqueue_script( 'reign-sticky-sidebar', get_template_directory_uri() . '/assets/js/vendors/sticky-sidebar.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// sticky-kit (stick_in_parent) only floats the single-post social-share
		// box, which renders on singular content.
		if ( is_singular() ) {
			wp_enqueue_script( 'reign-sticky-kit', get_template_directory_uri() . '/assets/js/vendors/sticky-kit.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// Load mscrollbar JS file.
		if ( has_nav_menu( 'panel-menu' ) || class_exists( 'WooCommerce' ) ) {
			wp_enqueue_script( 'reign-mscrollbar', get_template_directory_uri() . '/assets/js/vendors/mscrollbar.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// Load GamiPress JS File - Only load on GamiPress pages.
		if ( function_exists( 'reign_should_load_gamipress_assets' ) && reign_should_load_gamipress_assets() ) {
			wp_enqueue_script( 'reign-gamipress', get_template_directory_uri() . '/assets/js/gamipress.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// Scroll Up. Read once and reuse below (was also read in
		// wp_localize_script payload — see $reign_enable_scrollup local).
		$reign_enable_scrollup = get_theme_mod( 'reign_enable_scrollup', false );
		if ( reign_is_truthy( $reign_enable_scrollup ) ) {
			wp_enqueue_script( 'scrollup', get_template_directory_uri() . '/assets/js/vendors/scrollup.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// Easy Digital Downloads JS File - Only load on EDD pages.
		if ( function_exists( 'reign_should_load_edd_assets' ) && reign_should_load_edd_assets() ) {
			wp_enqueue_script( 'reign-edd', get_template_directory_uri() . '/assets/js/reign-edd.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// PeepSo JS File - Only load on PeepSo pages.
		if ( function_exists( 'reign_should_load_peepso_assets' ) && reign_should_load_peepso_assets() ) {
			wp_enqueue_script( 'reign-peepso', get_template_directory_uri() . '/assets/js/reign-peepso.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// bbPress JS File - Only load on bbPress pages.
		if ( function_exists( 'reign_should_load_bbpress_assets' ) && reign_should_load_bbpress_assets() ) {
			wp_enqueue_script( 'reign-bbpress', get_template_directory_uri() . '/assets/js/reign-bbpress.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// BuddyPress Header Icons + BuddyPress JS - only load on BP pages.
		// password-strength-meter REMOVED from the dependency array: it
		// pulls in zxcvbn.min.js (~400 KB) and password-strength-meter.min.js
		// on every BP page (activity feeds, members, groups) even though
		// password validation is only needed on the register page. The
		// register popup, which DOES use password validation, conditionally
		// enqueues password-strength-meter inside template-parts/login-form
		// rendering. Savings: ~410 KB per BP page that isn't the register form.
		if ( function_exists( 'reign_should_load_buddypress_assets' ) && reign_should_load_buddypress_assets() ) {
			wp_enqueue_script( 'reign-bp-header-icons', get_template_directory_uri() . '/assets/js/reign-bp-header-icons.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
			wp_enqueue_script( 'reign-buddypress', get_template_directory_uri() . '/assets/js/reign-buddypress.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );

			// Conditionally enqueue password-strength-meter only when a
			// registration / lost-password / reset-password form is being
			// rendered. BP's bp_is_register_page() / bp_is_activation_page()
			// cover the BP routes; bp_is_user_settings() covers the
			// in-account password change page.
			if (
				( function_exists( 'bp_is_register_page' )      && bp_is_register_page() )
				|| ( function_exists( 'bp_is_activation_page' ) && bp_is_activation_page() )
				|| ( function_exists( 'bp_is_user_settings' )   && bp_is_user_settings() )
			) {
				wp_enqueue_script( 'password-strength-meter' );
			}
		}

		// LifterLMS JS File.
		if ( class_exists( 'LifterLMS' ) && ! class_exists( 'Reign_LifterLMS_Addon' ) ) {

			$reign_lifterlms_js = is_lifterlms() || is_llms_account_page() || is_llms_checkout() || is_tax( 'membership_cat' );

			// Apply filter to allow extra conditional logic for JS.
			$reign_lifterlms_js = apply_filters( 'reign_lifterlms_enqueue_js_condition', $reign_lifterlms_js );

			if ( $reign_lifterlms_js ) {
				wp_enqueue_script( 'reign-lifterlms', get_template_directory_uri() . '/assets/js/reign-lifterlms.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
			}
		}

		// WooCommerce JS File - Only load on WooCommerce pages.
		if ( function_exists( 'reign_should_load_woocommerce_assets' ) && reign_should_load_woocommerce_assets( 'js' ) ) {
			wp_enqueue_script( 'reign-woocommerce', get_template_directory_uri() . '/assets/js/reign-woocommerce.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );

			$reign_woo_layout_view_buttons   = get_theme_mod( 'reign_woo_layout_view_buttons', true );
			$reign_woo_myaccount_menu_toggle = get_theme_mod( 'reign_woo_myaccount_menu_toggle', false );

			wp_localize_script(
				'reign-woocommerce',
				'wp_woocommerce_js_obj',
				array(
					'enable_layout_view_buttons'   => reign_is_truthy( $reign_woo_layout_view_buttons ) ? 1 : 0, // reign-woocommerce.js compares `== true`; switch stores "on"/"off".
					'enable_myaccount_menu_toggle' => reign_is_truthy( $reign_woo_myaccount_menu_toggle ) ? 1 : 0, // reign-woocommerce.js compares `== 1`; switch stores "on"/"off".
				)
			);
		}

		// Read the blog-layout setting once and reuse across the three
		// downstream sites (masonry filter, masonry enqueue guard, and
		// the wp_localize_script payload below).
		$reign_blog_list_layout = get_theme_mod( 'reign_blog_list_layout', 'default-view' );

		// Filter to set the enable_masonry value.
		add_filter(
			'reign_enable_masonry',
			function () use ( $reign_blog_list_layout ) {
				global $post;
				if ( isset( $post->post_content ) && check_masonry_view_in_shortcode( $post->post_content ) ) {
					return 'masonry-view';
				}
				return $reign_blog_list_layout;
			}
		);

		// Masonry JS File.
		if ( apply_filters( 'reign_enable_masonry', $reign_blog_list_layout ) == 'masonry-view' ) {
			wp_enqueue_script( 'reign-masonry', get_template_directory_uri() . '/assets/js/vendors/masonry.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}

		// Theme JS File.
		wp_enqueue_script( 'wp-main', get_template_directory_uri() . '/assets/js/main.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );

		/*
		 * Reign REST API JavaScript client. Modern fetch + wp.apiFetch
		 * style — no jQuery dependency, no admin-ajax — talks to
		 * /wp-json/reign/v1/*. Loaded as a footer script so it never
		 * render-blocks. Configuration is localized into
		 * `window.reignApiConfig` immediately below this enqueue.
		 *
		 * @since 8.0.0
		 */
		wp_enqueue_script( 'reign-api', get_template_directory_uri() . '/assets/js/reign-api.min.js', array(), REIGN_THEME_VERSION, true );

		// Cache-safe localize: only site-wide settings. rest_nonce is
		// per-user — baking it into cached HTML would defeat P1's
		// cache-safety fix. The nonce is appended for logged-in
		// visitors via reign_emit_logged_in_nonces() (wp_footer:30).
		wp_localize_script(
			'reign-api',
			'reignApiConfig',
			array(
				'root'          => esc_url_raw( rest_url( 'reign/v1/' ) ),
				'rest_nonce'    => '',
				'ajax_fallback' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			)
		);

		// Add More Header Script.
		$more_menu_enable = get_theme_mod( 'reign_header_main_menu_more_enable', true );

		// Check if topbar is disabled in mobile view.
		$reign_header_topbar_mobile_view_disable = get_theme_mod( 'reign_header_topbar_mobile_view_disable', false );

		$rtl = false;
		if ( is_rtl() ) {
			$rtl = true;
		}

		$single_activity_page = false;

		if ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) {
			$single_activity_page = true;
		}
		$append_text = apply_filters( 'bp_activity_excerpt_append_text', __( '[Read more]', 'reign' ) );
		if ( function_exists( 'bp_activity_get_excerpt_length' ) ) {
			$excerpt_length = bp_activity_get_excerpt_length();
		} else {
			$excerpt_length = 200;
		}

		if ( function_exists( 'bp_get_theme_package_id' ) ) {
			$theme_package_id = bp_get_theme_package_id();
		} else {
			$theme_package_id = 'legacy';
		}

		// Cache-safe localize payload: only site-wide settings. logged_in flag
		// + 4 nonces removed because they vary per visitor and would bake
		// into cached HTML, breaking page caching site-wide. JS reads auth
		// state via `document.body.classList.contains('logged-in')` (WP core
		// emits the body class). Per-user nonces are emitted by
		// reign_emit_logged_in_nonces() — a wp_footer callback gated on
		// is_user_logged_in().
		wp_localize_script(
			'wp-main',
			'wp_main_js_obj',
			array(
				'ajaxurl'                => admin_url( 'admin-ajax.php' ),
				'reign_more_menu_enable' => reign_is_truthy( $more_menu_enable ) ? 1 : 0, // main.js:263 uses it truthy; switch stores "on"/"off".
				'topbar_mobile_disabled' => reign_is_truthy( $reign_header_topbar_mobile_view_disable ) ? 1 : 0, // switch stores "on"/"off"; raw 'off' is truthy in JS.
				'reign_rtl'              => $rtl,
				'single_activity_page'   => $single_activity_page,
				'append_text'            => $append_text,
				'excerpt_length'         => $excerpt_length,
				'theme_package_id'       => $theme_package_id,
				'reign_enable_scrollup'  => reign_is_truthy( $reign_enable_scrollup ) ? 1 : 0, // read once at line 745; normalized to int because main.min.js guards with `1 == ...` and switch stores "on".
				'reign_scrollup_style'   => get_theme_mod( 'reign_scrollup_style', 'style1' ),
				'bp_subnav_view_style'   => get_theme_mod( 'buddypress_main_subnav_view_style', 'default' ),
				'enable_masonry'         => apply_filters( 'reign_enable_masonry', $reign_blog_list_layout ), // read once at line 823
				'no_messages_text'       => __( 'No messages found.', 'reign' ),
				'mpp_empty_content_msg'  => __( 'Please enter some content to post.', 'reign' ),
				'scroll_to_top'          => __( 'Scroll to Top', 'reign' ),
			)
		);

		// wp_localize_script for 'wp-dark-mode' removed in 8.0.0 alongside
		// the wp-dark-mode.js retirement. Logo + image overrides for dark
		// mode will be re-introduced in Phase 4 as CSS-only swaps driven by
		// :root[data-bx-mode="dark"] - simpler and avoids the JS sync.

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Adds UI scripts.
		if ( ! is_admin() ) {
			if ( function_exists( 'bp_is_activity_heartbeat_active' ) && bp_is_activity_heartbeat_active() ) {
				// Heartbeat
				// wp_enqueue_script( 'heartbeat' );
			}
		}

		// Loads dynamic inline style — TRANSIENT-CACHED on the front end.
		// Without the transient, this regenerates ~60 get_theme_mod() reads
		// + ~15 KB string concat on EVERY front-end page render, even
		// though the output is identical until the admin saves the
		// customizer. The transient is busted via clear_inline_css_cache()
		// on customize_save_after.
		//
		// In the customizer PREVIEW the cache is bypassed (and never written)
		// so unsaved per-element / scheme colour changes render live - the
		// previewed theme_mod values flow straight through load_color_palette().
		// Without this, the live preview served the day-old cached CSS and
		// colour edits only appeared after Publish.
		$is_preview = function_exists( 'is_customize_preview' ) && is_customize_preview();
		$css        = $is_preview ? false : get_transient( 'reign_inline_palette_css' );
		if ( false === $css ) {
			$css = load_color_palette();
			if ( ! $is_preview ) {
				set_transient( 'reign_inline_palette_css', $css, DAY_IN_SECONDS );
			}
		}
		wp_add_inline_style( 'reign_main_style', $css );

		// Loads dynamic dark-mode inline style — TRANSIENT-CACHED (same
		// customizer-preview bypass as the light palette above).
		// Switch fields can store polymorphic values ('on' / 'off' / '1' /
		// '0' / true / false / ''). reign_is_truthy() normalises across
		// all storage shapes (the strict `=== true` check used before
		// 8.0.0 silently failed because sanitize_bool_int stores 1, not true).
		// Emitted unconditionally: the [data-bx-mode="dark"] block only takes
		// effect when the page is in dark mode, and carrying it on every page
		// is what lets the toggle switch instantly. Buyer overrides from the
		// "Dark Mode Colors" section win; otherwise the per-palette dark
		// defaults render (matching the SCSS, so no visible change until
		// overridden). !important + loaded after the compiled CSS so it beats
		// the SCSS dark defaults.
		$dark_css = $is_preview ? false : get_transient( 'reign_inline_dark_palette_css' );
		if ( false === $dark_css ) {
			$dark_css = reign_load_dark_mode_palette();
			if ( ! $is_preview ) {
				set_transient( 'reign_inline_dark_palette_css', $dark_css, DAY_IN_SECONDS );
			}
		}
		if ( $dark_css ) {
			wp_add_inline_style( 'reign_main_style', $dark_css );
		}

		// Loads dynamic border radius inline style — TRANSIENT-CACHED with the
		// same customizer-preview bypass as the palettes above. load_border_radius()
		// otherwise re-runs 3 get_theme_mod() + 3 get_option() reads + string
		// concat on every front-end render, though the output is identical until
		// the admin saves. Busted via clear_inline_css_cache() on customize_save_after.
		$radius_css = $is_preview ? false : get_transient( 'reign_inline_border_radius_css' );
		if ( false === $radius_css ) {
			$radius_css = load_border_radius();
			if ( ! $is_preview ) {
				set_transient( 'reign_inline_border_radius_css', $radius_css, DAY_IN_SECONDS );
			}
		}
		wp_add_inline_style( 'reign_main_style', $radius_css );
	}

	add_action( 'wp_enqueue_scripts', 'reign_scripts', 5001 );
}

/**
 * Defer non-critical stylesheets via the preload + onload swap pattern.
 *
 * Handles listed here render as <link rel="preload" as="style"> with an
 * onload swap to rel="stylesheet", plus a <noscript> fallback — the file
 * downloads at high priority but never blocks first paint. Only safe for
 * styles whose above-the-fold rules are covered elsewhere (e.g. the full
 * Font Awesome glyph map, whose visible glyphs ship in reign-icons-core).
 *
 * @param string[] $handles Style handles to defer (filter: reign_deferred_style_handles).
 */
/**
 * Whether the main stylesheet ships as a critical/deferred split.
 *
 * Rides the Smart Performance toggle (same switch that gates conditional
 * asset loading) so site owners have ONE safety valve: toggle OFF restores
 * the classic single render-blocking bundle. Filterable independently for
 * edge setups.
 *
 * @return bool
 */
function reign_split_main_css() {
	$split = ! function_exists( 'reign_bypass_conditional_assets' ) || ! reign_bypass_conditional_assets();
	return (bool) apply_filters( 'reign_split_main_css', $split );
}

function reign_defer_style_tag( $tag, $handle, $href, $media ) {
	$defaults = array( 'font-awesome' );
	if ( reign_split_main_css() ) {
		$defaults[] = 'reign_main_style';
	}
	$deferred = apply_filters( 'reign_deferred_style_handles', $defaults );

	if ( ! in_array( $handle, $deferred, true ) || is_admin() || is_customize_preview() ) {
		return $tag;
	}

	$preload = sprintf(
		'<link rel="preload" href="%1$s" as="style" id="%2$s-css" media="%3$s" onload="this.onload=null;this.rel=\'stylesheet\'" />' . "\n" .
		'<noscript><link rel="stylesheet" href="%1$s" media="%3$s" /></noscript>' . "\n",
		esc_url( $href ),
		esc_attr( $handle ),
		esc_attr( $media )
	);

	return $preload;
}
add_filter( 'style_loader_tag', 'reign_defer_style_tag', 10, 4 );

/**
 * Preload default font when no custom font-family is set.
 *
 * Front-end only: wp-admin doesn't use this preload (it never paints
 * with the front-end Inter stack), so emitting the link there is wasted
 * theme_mod read + one extra <head> element per wp-admin page view.
 */
add_action( 'wp_head', function () {
    if ( is_admin() ) {
        return;
    }
    // Default to an empty array so the offset access below is safe even
    // when no typography value has been saved yet (fresh install / wp-cli
    // / REST context). Without this default, get_theme_mod returns false
    // and `false['font-family']` triggers PHP 8 deprecation notice.
    $body_typography = get_theme_mod( 'reign_body_typography', array() );
    if ( ! is_array( $body_typography ) || empty( $body_typography['font-family'] ) ) {
        echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/fonts/inter/Inter-Regular.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
    }
}, 1 );

if ( ! function_exists( 'reign_enqueue_admin_style' ) ) {
	/**
	 * Register and enqueue a custom stylesheet in the WordPress admin.
	 */
	function reign_enqueue_admin_style() {
		$css_uri = get_theme_file_uri( '/assets/css/' );

		wp_enqueue_style( 'reign-admin', $css_uri . '/admin.min.css', '', REIGN_THEME_VERSION );
	}

	add_action( 'admin_enqueue_scripts', 'reign_enqueue_admin_style' );
}

if ( ! function_exists( 'register_reign_menu_page' ) ) {

	/**
	 * Register Reign Menu Page
	 */
	function register_reign_menu_page() {
		// Set position with odd number to avoid confict with other plugin/theme.
		add_menu_page( __( 'Reign Settings', 'reign' ), __( 'Reign Settings', 'reign' ), 'manage_options', 'reign-settings', '', '', 61.000329 );

		// To remove empty parent menu item.
		add_submenu_page( 'reign-settings', __( 'Reign Settings', 'reign' ), __( 'Reign Settings', 'reign' ), 'manage_options', 'reign-settings' );
		remove_submenu_page( 'reign-settings', 'reign-settings' );
	}

	add_action( 'admin_menu', 'register_reign_menu_page' );
}

/**
 * Remove Secondary Group Icon
 */
// `my_*` is a tutorial-boilerplate prefix used by thousands of WP
// snippets — extremely high collision risk with child themes, mu-plugins,
// and BP tutorials. Wrap so a redeclaration anywhere else doesn't fatal.
if ( ! function_exists( 'my_remove_secondary_avatars' ) ) {
	function my_remove_secondary_avatars( $bp_legacy ) {
		remove_filter( 'bp_get_activity_action_pre_meta', array( $bp_legacy, 'secondary_avatars' ), 10, 2 );
	}
}

add_action( 'bp_theme_compat_actions', 'my_remove_secondary_avatars' );

/**
 * Set the global variable for activated color scheme.
 *
 * @global string $color_scheme
 */
if ( ! function_exists( 'reign_color_scheme' ) ) {

	function reign_color_scheme() {
		// $GLOBALS export — runs on every request because downstream
		// code reads from the global. Cheap, autoloaded option.
		$GLOBALS['rtm_color_scheme'] = get_theme_mod( 'reign_color_scheme', 'reign_clean' );

		// Skip the migration walk + per-request DB writes once the
		// `update_reign_theme` flag has been set. Without this early
		// return the migration block at line ~1050 fires up to 2
		// update_option() DB writes per request (one for each
		// theme_mod scheme overwrite) on every page load, including
		// REST / AJAX / cron — wasted CPU + DB for the entire life
		// of a post-migrated site.
		if ( get_option( 'update_reign_theme' ) ) {
			return;
		}

		$theme_mods                  = $mods                             = get_theme_mods();

		if ( isset( $mods[0] ) && $mods[0] == '' ) {
			unset( $mods[0] );
		}

		$flg = true;
		if ( empty( $mods ) ) {
			$stylesheet                       = get_option( 'stylesheet' );
			$theme_mod                        = 'theme_mods_' . $stylesheet;
			$theme_mods['reign_color_scheme'] = 'reign_clean';
			update_option( $theme_mod, $theme_mods );
			$flg = false;
		}

		/* Update Color scheme with new version */
		$update_reign_theme = get_option( 'update_reign_theme' );
		if ( ! empty( $mods ) && ! $update_reign_theme && $flg == true ) {
			$reign_color_scheme = ( isset( $theme_mods['reign_color_scheme'] ) && $theme_mods['reign_color_scheme'] != '' ) ? $theme_mods['reign_color_scheme'] : 'reign_default';

			$update_theme_mode_colors = array(
				'reign_header_topbar_bg_color',
				'reign_header_topbar_text_color',
				'reign_header_topbar_text_hover_color',
				'reign_header_bg_color',
				'reign_header_main_menu_text_hover_color',
				'reign_header_main_menu_text_active_color',
				'reign_header_main_menu_bg_hover_color',
				'reign_header_main_menu_bg_active_color',
				'reign_header_sub_menu_bg_color',
				'reign_header_sub_menu_text_hover_color',
				'reign_header_sub_menu_bg_hover_color',
				'reign_header_icon_color',
				'reign_header_icon_hover_color',
				'reign_footer_widget_area_bg_color',
				'reign_footer_widget_title_color',
				'reign_footer_widget_text_color',
				'reign_footer_widget_link_color',
				'reign_footer_widget_link_hover_color',
				'reign_footer_copyright_bg_color',
				'reign_footer_copyright_text_color',
				'reign_footer_copyright_link_color',
				'reign_footer_copyright_link_hover_color',
			);

			foreach ( $update_theme_mode_colors as $colors ) {
				/* Reign reign_header_topbar_bg_color Update with new version */
				if ( isset( $theme_mods[ $colors ] ) && $theme_mods[ $colors ] != '' && ! isset( $theme_mods[ $reign_color_scheme . '-' . $colors ] ) ) {
					$theme_mods[ $reign_color_scheme . '-' . $colors ] = $theme_mods[ $colors ];
				}
			}
			$update_theme_mode_colors_garoup = array(
				'reign_title_tagline_typography',
				'reign_header_main_menu_font',
				'reign_header_sub_menu_font',
			);
			foreach ( $update_theme_mode_colors_garoup as $colors ) {
				/* Reign reign_header_topbar_bg_color Update with new version */
				if ( isset( $theme_mods[ $colors ]['color'] ) && $theme_mods[ $colors ]['color'] != '' && ! isset( $theme_mods[ $reign_color_scheme . '-' . $colors ] ) ) {
					$theme_mods[ $reign_color_scheme . '-' . $colors ] = $theme_mods[ $colors ]['color'];
				}
			}
			$theme_mods['reign_color_scheme'] = $reign_color_scheme;
			$stylesheet                       = get_option( 'stylesheet' );
			$theme_mod                        = 'theme_mods_' . $stylesheet;
			update_option( $theme_mod, $theme_mods );
			update_option( 'update_reign_theme', true );
		}
	}

	add_action( 'after_setup_theme', 'reign_color_scheme' );
}

add_filter( 'body_class', 'reign_header_v4_body_class', 10, 1 );

/**
 *
 * Function to add body class when reign header v4 is active.
 */
function reign_header_v4_body_class( $classes ) {
	if ( 'v4' === get_theme_mod( 'reign_header_layout', 'v2' ) ) {
		$classes[] = 'reign-header-v4';
	}

	// Removed: appending `logged-out` body class for anonymous visitors.
	// That made the HTML vary per auth state, forcing page caches into
	// vary-by-cookie mode (which most caching plugins handle poorly).
	// WordPress core already emits the `logged-in` body class for
	// authenticated visitors; CSS should use `:not(.logged-in)` for any
	// rule that previously targeted `.logged-out`.
	return $classes;
}

add_action( 'bp_init', 'reign_restrict_hearbeat_for_bp' );

/**
 *
 * Function to restrict heartbeat request being send on non required bp pages.
 */
function reign_restrict_hearbeat_for_bp() {
	if ( bp_is_activity_component() && ! bp_is_activity_directory() ) {
		remove_filter( 'heartbeat_received', 'bp_activity_heartbeat_last_recorded', 10, 2 );
		remove_filter( 'heartbeat_nopriv_received', 'bp_activity_heartbeat_last_recorded', 10, 2 );
	}
}

/*
 * Cartflow plugin compatibility with Canvas page builder.
 */
add_filter( 'cartflows_is_compatibility_theme', '__return_true' );

/**
 * Conver Hex to RGB
 *
 * @since 7.4.3
 *
 * @param string $color color.
 *
 * @return string
 */
// Generic un-prefixed helper. Common utility name used by many themes
// + plugins. Wrap so a collision can't fatal the theme load.
if ( ! function_exists( 'hex2rgb' ) ) :
function hex2rgb( string $color ) {

	$default = '0, 0, 0';

	if ( $color === '' ) {
		return '';
	}

	if ( strpos( $color, 'var(--' ) === 0 ) {
		return preg_replace( '/[^A-Za-z0-9_)(\-,.]/', '', $color );
	}

	// Convert hex to rgb.
	if ( $color[0] == '#' ) {
		$color = substr( $color, 1 );
	} else {
		return $default;
	}

	// Check if color has 6 or 3 characters and get values.
	if ( strlen( $color ) == 6 ) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}

	$rgb = array_map( 'hexdec', $hex );

	return implode( ', ', $rgb );
}
endif;


/**
 * Load color palette
 *
 * @since 6.9.2
 * @return string
 */
if ( ! function_exists( 'load_color_palette' ) ) :
function load_color_palette() {
	$colors = array(
		'reign_header_topbar_bg_color'             => '--reign-header-topbar-bg-color',
		'reign_header_topbar_text_color'           => '--reign-header-topbar-text-color',
		'reign_header_topbar_text_hover_color'     => '--reign-header-topbar-text-hover-color',
		'reign_header_bg_color'                    => '--reign-header-bg-color',
		'reign_header_nav_bg_color'                => '--reign-header-nav-bg-color',
		'reign_title_tagline_typography'           => '--reign-title-tagline-typography',
		'reign_header_main_menu_font'              => '--reign-header-main-menu-font',
		'reign_header_main_menu_text_hover_color'  => '--reign-header-main-menu-text-hover-color',
		'reign_header_main_menu_text_active_color' => '--reign-header-main-menu-text-active-color',
		'reign_header_main_menu_bg_hover_color'    => '--reign-header-main-menu-bg-hover-color',
		'reign_header_main_menu_bg_active_color'   => '--reign-header-main-menu-bg-active-color',
		'reign_header_sub_menu_bg_color'           => '--reign-header-sub-menu-bg-color',
		'reign_header_sub_menu_font'               => '--reign-header-sub-menu-font',
		'reign_header_sub_menu_text_hover_color'   => '--reign-header-sub-menu-text-hover-color',
		'reign_header_sub_menu_bg_hover_color'     => '--reign-header-sub-menu-bg-hover-color',
		'reign_header_icon_color'                  => '--reign-header-icon-color',
		'reign_header_icon_hover_color'            => '--reign-header-icon-hover-color',
		'reign_mobile_menu_bg_color'               => '--reign-mobile-menu-bg-color',
		'reign_mobile_menu_color'                  => '--reign-mobile-menu-color',
		'reign_mobile_menu_hover_color'            => '--reign-mobile-menu-hover-color',
		'reign_mobile_menu_active_color'           => '--reign-mobile-menu-active-color',
		'reign_mobile_menu_active_bg_color'        => '--reign-mobile-menu-active-bg-color',
		'reign_left_panel_bg_color'                => '--reign-left-panel-bg-color',
		'reign_left_panel_toggle_color'            => '--reign-left-panel-toggle-color',
		'reign_left_panel_menu_font_color'         => '--reign-left-panel-menu-font-color',
		'reign_left_panel_menu_hover_color'        => '--reign-left-panel-menu-hover-color',
		'reign_left_panel_menu_active_color'       => '--reign-left-panel-menu-active-color',
		'reign_left_panel_menu_bg_hover_color'     => '--reign-left-panel-menu-bg-hover-color',
		'reign_left_panel_menu_bg_active_color'    => '--reign-left-panel-menu-bg-active-color',
		'reign_left_panel_menu_icon_active_color'  => '--reign-left-panel-menu-icon-active-color',
		'reign_left_panel_tooltip_bg_color'        => '--reign-left-panel-tooltip-bg-color',
		'reign_left_panel_tooltip_color'           => '--reign-left-panel-tooltip-color',
		'reign_site_body_bg_color'                 => '--reign-site-body-bg-color',
		'reign_site_body_text_color'               => '--reign-site-body-text-color',
		'reign_site_alternate_text_color'          => '--reign-site-alternate-text-color',
		'reign_site_sections_bg_color'             => '--reign-site-sections-bg-color',
		'reign_site_secondary_bg_color'            => '--reign-site-secondary-bg-color',
		'reign_colors_theme'                       => '--reign-colors-theme',
		'reign_site_headings_color'                => '--reign-site-headings-color',
		'reign_site_link_color'                    => '--reign-site-link-color',
		'reign_site_link_hover_color'              => '--reign-site-link-hover-color',
		'reign_accent_color'                       => '--reign-accent-color',
		'reign_accent_hover_color'                 => '--reign-accent-hover-color',
		'reign_site_button_text_color'             => '--reign-site-button-text-color',
		'reign_site_button_text_hover_color'       => '--reign-site-button-text-hover-color',
		'reign_site_button_bg_color'               => '--reign-site-button-bg-color',
		'reign_site_button_bg_hover_color'         => '--reign-site-button-bg-hover-color',
		'reign_site_border_color'                  => '--reign-site-border-color',
		'reign_site_hr_color'                      => '--reign-site-hr-color',
		'reign_footer_widget_area_bg_color'        => '--reign-footer-widget-area-bg-color',
		'reign_footer_widget_title_color'          => '--reign-footer-widget-title-color',
		'reign_footer_widget_text_color'           => '--reign-footer-widget-text-color',
		'reign_footer_widget_link_color'           => '--reign-footer-widget-link-color',
		'reign_footer_widget_link_hover_color'     => '--reign-footer-widget-link-hover-color',
		'reign_footer_copyright_bg_color'          => '--reign-footer-copyright-bg-color',
		'reign_footer_copyright_text_color'        => '--reign-footer-copyright-text-color',
		'reign_footer_copyright_link_color'        => '--reign-footer-copyright-link-color',
		'reign_footer_copyright_link_hover_color'  => '--reign-footer-copyright-link-hover-color',

		'reign_form_text_color'                    => '--reign-form-text-color',
		'reign_form_background_color'              => '--reign-form-background-color',
		'reign_form_border_color'                  => '--reign-form-border-color',
		'reign_form_placeholder_color'             => '--reign-form-placeholder-color',
		'reign_form_focus_text_color'              => '--reign-form-focus-text-color',
		'reign_form_focus_background_color'        => '--reign-form-focus-background-color',
		'reign_form_focus_border_color'            => '--reign-form-focus-border-color',
		'reign_form_focus_placeholder_color'       => '--reign-form-focus-placeholder-color',

	);

	// Customizer colors. Defensive guards — load_color_palette() fires
	// from wp_enqueue_scripts:5001 which runs LATE, but the underlying
	// reign_color_scheme_set() + reign_forms_color_scheme_default_set()
	// functions live in customizer-defaults.php which is loaded via
	// the customizer settings loader. If that loader ever fails (file
	// missing, plugin conflict, error during include), this callback
	// would otherwise fatal mid-page-render — far worse than emitting
	// a default palette.
	$default_value_set      = function_exists( 'reign_color_scheme_set' )
		? reign_color_scheme_set()
		: array( 'reign_clean' => array() );
	$default_value_set_form = function_exists( 'reign_forms_color_scheme_default_set' )
		? reign_forms_color_scheme_default_set()
		: array();
	$reign_color_scheme     = get_theme_mod( 'reign_color_scheme', 'reign_clean' );

	$admin_colors = array(
		'--reign-header-topbar-bg-color'             => get_theme_mod( "{$reign_color_scheme}-reign_header_topbar_bg_color", $default_value_set[ $reign_color_scheme ]['reign_header_topbar_bg_color'] ),
		'--reign-header-topbar-text-color'           => get_theme_mod( "{$reign_color_scheme}-reign_header_topbar_text_color", $default_value_set[ $reign_color_scheme ]['reign_header_topbar_text_color'] ),
		'--reign-header-topbar-text-hover-color'     => get_theme_mod( "{$reign_color_scheme}-reign_header_topbar_text_hover_color", $default_value_set[ $reign_color_scheme ]['reign_header_topbar_text_hover_color'] ),
		'--reign-header-bg-color'                    => get_theme_mod( "{$reign_color_scheme}-reign_header_bg_color", $default_value_set[ $reign_color_scheme ]['reign_header_bg_color'] ),
		'--reign-header-nav-bg-color'                => get_theme_mod( "{$reign_color_scheme}-reign_header_nav_bg_color", $default_value_set[ $reign_color_scheme ]['reign_header_nav_bg_color'] ),
		'--reign-title-tagline-typography'           => get_theme_mod( "{$reign_color_scheme}-reign_title_tagline_typography", $default_value_set[ $reign_color_scheme ]['reign_title_tagline_typography'] ),
		'--reign-header-main-menu-font'              => get_theme_mod( "{$reign_color_scheme}-reign_header_main_menu_font", $default_value_set[ $reign_color_scheme ]['reign_header_main_menu_font'] ),
		'--reign-header-main-menu-text-hover-color'  => get_theme_mod( "{$reign_color_scheme}-reign_header_main_menu_text_hover_color", $default_value_set[ $reign_color_scheme ]['reign_header_main_menu_text_hover_color'] ),
		'--reign-header-main-menu-text-active-color' => get_theme_mod( "{$reign_color_scheme}-reign_header_main_menu_text_active_color", $default_value_set[ $reign_color_scheme ]['reign_header_main_menu_text_active_color'] ),
		'--reign-header-main-menu-bg-hover-color'    => get_theme_mod( "{$reign_color_scheme}-reign_header_main_menu_bg_hover_color", $default_value_set[ $reign_color_scheme ]['reign_header_main_menu_bg_hover_color'] ),
		'--reign-header-main-menu-bg-active-color'   => get_theme_mod( "{$reign_color_scheme}-reign_header_main_menu_bg_active_color", $default_value_set[ $reign_color_scheme ]['reign_header_main_menu_bg_active_color'] ),
		'--reign-header-sub-menu-bg-color'           => get_theme_mod( "{$reign_color_scheme}-reign_header_sub_menu_bg_color", $default_value_set[ $reign_color_scheme ]['reign_header_sub_menu_bg_color'] ),
		'--reign-header-sub-menu-font'               => get_theme_mod( "{$reign_color_scheme}-reign_header_sub_menu_font", $default_value_set[ $reign_color_scheme ]['reign_header_sub_menu_font'] ),
		'--reign-header-sub-menu-text-hover-color'   => get_theme_mod( "{$reign_color_scheme}-reign_header_sub_menu_text_hover_color", $default_value_set[ $reign_color_scheme ]['reign_header_sub_menu_text_hover_color'] ),
		'--reign-header-sub-menu-bg-hover-color'     => get_theme_mod( "{$reign_color_scheme}-reign_header_sub_menu_bg_hover_color", $default_value_set[ $reign_color_scheme ]['reign_header_sub_menu_bg_hover_color'] ),
		'--reign-header-icon-color'                  => get_theme_mod( "{$reign_color_scheme}-reign_header_icon_color", $default_value_set[ $reign_color_scheme ]['reign_header_icon_color'] ),
		'--reign-header-icon-hover-color'            => get_theme_mod( "{$reign_color_scheme}-reign_header_icon_hover_color", $default_value_set[ $reign_color_scheme ]['reign_header_icon_hover_color'] ),
		'--reign-mobile-menu-bg-color'               => get_theme_mod( "{$reign_color_scheme}-reign_mobile_menu_bg_color", $default_value_set[ $reign_color_scheme ]['reign_mobile_menu_bg_color'] ),
		'--reign-mobile-menu-color'                  => get_theme_mod( "{$reign_color_scheme}-reign_mobile_menu_color", $default_value_set[ $reign_color_scheme ]['reign_mobile_menu_color'] ),
		'--reign-mobile-menu-hover-color'            => get_theme_mod( "{$reign_color_scheme}-reign_mobile_menu_hover_color", $default_value_set[ $reign_color_scheme ]['reign_mobile_menu_hover_color'] ),
		'--reign-mobile-menu-active-color'           => get_theme_mod( "{$reign_color_scheme}-reign_mobile_menu_active_color", $default_value_set[ $reign_color_scheme ]['reign_mobile_menu_active_color'] ),
		'--reign-mobile-menu-active-bg-color'        => get_theme_mod( "{$reign_color_scheme}-reign_mobile_menu_active_bg_color", $default_value_set[ $reign_color_scheme ]['reign_mobile_menu_active_bg_color'] ),
		'--reign-left-panel-bg-color'                => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_bg_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_bg_color'] ),
		'--reign-left-panel-toggle-color'            => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_toggle_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_toggle_color'] ),
		'--reign-left-panel-menu-font-color'         => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_menu_font_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_menu_font_color'] ),
		'--reign-left-panel-menu-hover-color'        => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_menu_hover_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_menu_hover_color'] ),
		'--reign-left-panel-menu-active-color'       => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_menu_active_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_menu_active_color'] ),
		'--reign-left-panel-menu-bg-hover-color'     => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_menu_bg_hover_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_menu_bg_hover_color'] ),
		'--reign-left-panel-menu-bg-active-color'    => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_menu_bg_active_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_menu_bg_active_color'] ),
		'--reign-left-panel-menu-icon-active-color'  => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_menu_icon_active_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_menu_icon_active_color'] ),
		'--reign-left-panel-tooltip-bg-color'        => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_tooltip_bg_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_tooltip_bg_color'] ),
		'--reign-left-panel-tooltip-color'           => get_theme_mod( "{$reign_color_scheme}-reign_left_panel_tooltip_color", $default_value_set[ $reign_color_scheme ]['reign_left_panel_tooltip_color'] ),
		'--reign-site-body-bg-color'                 => get_theme_mod( "{$reign_color_scheme}-reign_site_body_bg_color", $default_value_set[ $reign_color_scheme ]['reign_site_body_bg_color'] ),
		'--reign-site-body-text-color'               => get_theme_mod( "{$reign_color_scheme}-reign_site_body_text_color", $default_value_set[ $reign_color_scheme ]['reign_site_body_text_color'] ),
		'--reign-site-alternate-text-color'          => get_theme_mod( "{$reign_color_scheme}-reign_site_alternate_text_color", $default_value_set[ $reign_color_scheme ]['reign_site_alternate_text_color'] ),
		'--reign-site-sections-bg-color'             => get_theme_mod( "{$reign_color_scheme}-reign_site_sections_bg_color", $default_value_set[ $reign_color_scheme ]['reign_site_sections_bg_color'] ),
		'--reign-site-secondary-bg-color'            => get_theme_mod( "{$reign_color_scheme}-reign_site_secondary_bg_color", $default_value_set[ $reign_color_scheme ]['reign_site_secondary_bg_color'] ),
		'--reign-colors-theme'                       => get_theme_mod( "{$reign_color_scheme}-reign_colors_theme", $default_value_set[ $reign_color_scheme ]['reign_colors_theme'] ),
		'--reign-site-headings-color'                => get_theme_mod( "{$reign_color_scheme}-reign_site_headings_color", $default_value_set[ $reign_color_scheme ]['reign_site_headings_color'] ),
		'--reign-site-link-color'                    => get_theme_mod( "{$reign_color_scheme}-reign_site_link_color", $default_value_set[ $reign_color_scheme ]['reign_site_link_color'] ),
		'--reign-site-link-hover-color'              => get_theme_mod( "{$reign_color_scheme}-reign_site_link_hover_color", $default_value_set[ $reign_color_scheme ]['reign_site_link_hover_color'] ),
		'--reign-accent-color'                       => get_theme_mod( "{$reign_color_scheme}-reign_accent_color", $default_value_set[ $reign_color_scheme ]['reign_accent_color'] ),
		'--reign-accent-hover-color'                 => get_theme_mod( "{$reign_color_scheme}-reign_accent_hover_color", $default_value_set[ $reign_color_scheme ]['reign_accent_hover_color'] ),
		'--reign-site-button-text-color'             => get_theme_mod( "{$reign_color_scheme}-reign_site_button_text_color", $default_value_set[ $reign_color_scheme ]['reign_site_button_text_color'] ),
		'--reign-site-button-text-hover-color'       => get_theme_mod( "{$reign_color_scheme}-reign_site_button_text_hover_color", $default_value_set[ $reign_color_scheme ]['reign_site_button_text_hover_color'] ),
		'--reign-site-button-bg-color'               => get_theme_mod( "{$reign_color_scheme}-reign_site_button_bg_color", $default_value_set[ $reign_color_scheme ]['reign_site_button_bg_color'] ),
		'--reign-site-button-bg-hover-color'         => get_theme_mod( "{$reign_color_scheme}-reign_site_button_bg_hover_color", $default_value_set[ $reign_color_scheme ]['reign_site_button_bg_hover_color'] ),
		'--reign-site-border-color'                  => get_theme_mod( "{$reign_color_scheme}-reign_site_border_color", $default_value_set[ $reign_color_scheme ]['reign_site_border_color'] ),
		'--reign-site-hr-color'                      => get_theme_mod( "{$reign_color_scheme}-reign_site_hr_color", $default_value_set[ $reign_color_scheme ]['reign_site_hr_color'] ),
		'--reign-footer-widget-area-bg-color'        => get_theme_mod( "{$reign_color_scheme}-reign_footer_widget_area_bg_color", $default_value_set[ $reign_color_scheme ]['reign_footer_widget_area_bg_color'] ),
		'--reign-footer-widget-title-color'          => get_theme_mod( "{$reign_color_scheme}-reign_footer_widget_title_color", $default_value_set[ $reign_color_scheme ]['reign_footer_widget_title_color'] ),
		'--reign-footer-widget-text-color'           => get_theme_mod( "{$reign_color_scheme}-reign_footer_widget_text_color", $default_value_set[ $reign_color_scheme ]['reign_footer_widget_text_color'] ),
		'--reign-footer-widget-link-color'           => get_theme_mod( "{$reign_color_scheme}-reign_footer_widget_link_color", $default_value_set[ $reign_color_scheme ]['reign_footer_widget_link_color'] ),
		'--reign-footer-widget-link-hover-color'     => get_theme_mod( "{$reign_color_scheme}-reign_footer_widget_link_hover_color", $default_value_set[ $reign_color_scheme ]['reign_footer_widget_link_hover_color'] ),
		'--reign-footer-copyright-bg-color'          => get_theme_mod( "{$reign_color_scheme}-reign_footer_copyright_bg_color", $default_value_set[ $reign_color_scheme ]['reign_footer_copyright_bg_color'] ),
		'--reign-footer-copyright-text-color'        => get_theme_mod( "{$reign_color_scheme}-reign_footer_copyright_text_color", $default_value_set[ $reign_color_scheme ]['reign_footer_copyright_text_color'] ),
		'--reign-footer-copyright-link-color'        => get_theme_mod( "{$reign_color_scheme}-reign_footer_copyright_link_color", $default_value_set[ $reign_color_scheme ]['reign_footer_copyright_link_color'] ),
		'--reign-footer-copyright-link-hover-color'  => get_theme_mod( "{$reign_color_scheme}-reign_footer_copyright_link_hover_color", $default_value_set[ $reign_color_scheme ]['reign_footer_copyright_link_hover_color'] ),

		'--reign-form-text-color'                    => get_theme_mod( "{$reign_color_scheme}-reign_form_text_color", $default_value_set_form[ $reign_color_scheme ]['reign_form_text_color'] ),
		'--reign-form-background-color'              => get_theme_mod( "{$reign_color_scheme}-reign_form_background_color", $default_value_set_form[ $reign_color_scheme ]['reign_form_background_color'] ),
		'--reign-form-border-color'                  => get_theme_mod( "{$reign_color_scheme}-reign_form_border_color", $default_value_set_form[ $reign_color_scheme ]['reign_form_border_color'] ),
		'--reign-form-placeholder-color'             => get_theme_mod( "{$reign_color_scheme}-reign_form_placeholder_color", $default_value_set_form[ $reign_color_scheme ]['reign_form_placeholder_color'] ),
		'--reign-form-focus-text-color'              => get_theme_mod( "{$reign_color_scheme}-reign_form_focus_text_color", $default_value_set_form[ $reign_color_scheme ]['reign_form_focus_text_color'] ),
		'--reign-form-focus-background-color'        => get_theme_mod( "{$reign_color_scheme}-reign_form_focus_background_color", $default_value_set_form[ $reign_color_scheme ]['reign_form_focus_background_color'] ),
		'--reign-form-focus-border-color'            => get_theme_mod( "{$reign_color_scheme}-reign_form_focus_border_color", $default_value_set_form[ $reign_color_scheme ]['reign_form_focus_border_color'] ),
		'--reign-form-focus-placeholder-color'       => get_theme_mod( "{$reign_color_scheme}-reign_form_focus_placeholder_color", $default_value_set_form[ $reign_color_scheme ]['reign_form_focus_placeholder_color'] ),
	);

	$fallback_colors = reign_color_scheme_set();

	$color_string = '';
	foreach ( $colors as $key => $property ) {
		$fallback_color = isset( $fallback_colors[ $key ] ) ? $fallback_colors[ $key ] : '';
		$color          = get_option( $key, $fallback_color );
		$color_rgb      = hex2rgb( $color );

		if ( isset( $admin_colors[ $property ] ) ) {
			$color     = $admin_colors[ $property ];
			$color_rgb = hex2rgb( $admin_colors[ $property ] );
		}

		if ( $color ) {
			$color_string .= $property . ':' . $color . ';';
		}

		if ( $color_rgb ) {
			$color_string .= $property . '-rgb:' . $color_rgb . ';';
		}
	}

	return ':root{' . $color_string . '}';
}
endif;

/**
 * Load dark color palette
 *
 * @since 6.9.2
 * @return string
 */
if ( ! function_exists( 'load_color_dark_palette' ) ) :
function load_color_dark_palette() {
	// Customizer colors. See note above caller in reign_scripts() — switch
	// fields need reign_is_truthy() not strict === true comparison.
	$reign_dark_mode_option        = get_theme_mod( 'reign_dark_mode_option', '' );
	$reign_custom_dark_mode_option = get_theme_mod( 'reign_custom_dark_mode_option', '' );

	if ( reign_is_truthy( $reign_dark_mode_option ) && reign_is_truthy( $reign_custom_dark_mode_option ) ) {
		// Preset defaults for the "reign_dark" scheme — used as the base when the
		// user has enabled custom dark mode but not yet overridden individual colors.
		// Without these, get_theme_mod() returns false for every unset key, the
		// override branch fires with falsy values, and the emitted CSS collapses
		// to an empty .dark-mode{} block (see Basecamp card 9788232909).
		$dark_defaults = array();
		if ( function_exists( 'reign_color_scheme_set' ) ) {
			$all_schemes   = reign_color_scheme_set();
			$dark_defaults = isset( $all_schemes['reign_dark'] ) && is_array( $all_schemes['reign_dark'] )
				? $all_schemes['reign_dark']
				: array();
		}
		$dark_default = static function ( $key ) use ( $dark_defaults ) {
			return isset( $dark_defaults[ $key ] ) ? $dark_defaults[ $key ] : '';
		};

		$colors = array(
			'reign_header_topbar_bg_color'             => '--reign-header-topbar-bg-color',
			'reign_header_topbar_text_color'           => '--reign-header-topbar-text-color',
			'reign_header_topbar_text_hover_color'     => '--reign-header-topbar-text-hover-color',
			'reign_header_bg_color'                    => '--reign-header-bg-color',
			'reign_header_nav_bg_color'                => '--reign-header-nav-bg-color',
			'reign_title_tagline_typography'           => '--reign-title-tagline-typography',
			'reign_header_main_menu_font'              => '--reign-header-main-menu-font',
			'reign_header_main_menu_text_hover_color'  => '--reign-header-main-menu-text-hover-color',
			'reign_header_main_menu_text_active_color' => '--reign-header-main-menu-text-active-color',
			'reign_header_main_menu_bg_hover_color'    => '--reign-header-main-menu-bg-hover-color',
			'reign_header_main_menu_bg_active_color'   => '--reign-header-main-menu-bg-active-color',
			'reign_header_sub_menu_bg_color'           => '--reign-header-sub-menu-bg-color',
			'reign_header_sub_menu_font'               => '--reign-header-sub-menu-font',
			'reign_header_sub_menu_text_hover_color'   => '--reign-header-sub-menu-text-hover-color',
			'reign_header_sub_menu_bg_hover_color'     => '--reign-header-sub-menu-bg-hover-color',
			'reign_header_icon_color'                  => '--reign-header-icon-color',
			'reign_header_icon_hover_color'            => '--reign-header-icon-hover-color',
			'reign_mobile_menu_bg_color'               => '--reign-mobile-menu-bg-color',
			'reign_mobile_menu_color'                  => '--reign-mobile-menu-color',
			'reign_mobile_menu_hover_color'            => '--reign-mobile-menu-hover-color',
			'reign_mobile_menu_active_color'           => '--reign-mobile-menu-active-color',
			'reign_mobile_menu_active_bg_color'        => '--reign-mobile-menu-active-bg-color',
			'reign_left_panel_bg_color'                => '--reign-left-panel-bg-color',
			'reign_left_panel_toggle_color'            => '--reign-left-panel-toggle-color',
			'reign_left_panel_menu_font_color'         => '--reign-left-panel-menu-font-color',
			'reign_left_panel_menu_hover_color'        => '--reign-left-panel-menu-hover-color',
			'reign_left_panel_menu_active_color'       => '--reign-left-panel-menu-active-color',
			'reign_left_panel_menu_bg_hover_color'     => '--reign-left-panel-menu-bg-hover-color',
			'reign_left_panel_menu_bg_active_color'    => '--reign-left-panel-menu-bg-active-color',
			'reign_left_panel_menu_icon_active_color'  => '--reign-left-panel-menu-icon-active-color',
			'reign_left_panel_tooltip_bg_color'        => '--reign-left-panel-tooltip-bg-color',
			'reign_left_panel_tooltip_color'           => '--reign-left-panel-tooltip-color',
			'reign_site_body_bg_color'                 => '--reign-site-body-bg-color',
			'reign_site_body_text_color'               => '--reign-site-body-text-color',
			'reign_site_alternate_text_color'          => '--reign-site-alternate-text-color',
			'reign_site_sections_bg_color'             => '--reign-site-sections-bg-color',
			'reign_site_secondary_bg_color'            => '--reign-site-secondary-bg-color',
			'reign_colors_theme'                       => '--reign-colors-theme',
			'reign_site_headings_color'                => '--reign-site-headings-color',
			'reign_site_link_color'                    => '--reign-site-link-color',
			'reign_site_link_hover_color'              => '--reign-site-link-hover-color',
			'reign_accent_color'                       => '--reign-accent-color',
			'reign_accent_hover_color'                 => '--reign-accent-hover-color',
			'reign_site_button_text_color'             => '--reign-site-button-text-color',
			'reign_site_button_text_hover_color'       => '--reign-site-button-text-hover-color',
			'reign_site_button_bg_color'               => '--reign-site-button-bg-color',
			'reign_site_button_bg_hover_color'         => '--reign-site-button-bg-hover-color',
			'reign_site_border_color'                  => '--reign-site-border-color',
			'reign_site_hr_color'                      => '--reign-site-hr-color',
			'reign_footer_widget_area_bg_color'        => '--reign-footer-widget-area-bg-color',
			'reign_footer_widget_title_color'          => '--reign-footer-widget-title-color',
			'reign_footer_widget_text_color'           => '--reign-footer-widget-text-color',
			'reign_footer_widget_link_color'           => '--reign-footer-widget-link-color',
			'reign_footer_widget_link_hover_color'     => '--reign-footer-widget-link-hover-color',
			'reign_footer_copyright_bg_color'          => '--reign-footer-copyright-bg-color',
			'reign_footer_copyright_text_color'        => '--reign-footer-copyright-text-color',
			'reign_footer_copyright_link_color'        => '--reign-footer-copyright-link-color',
			'reign_footer_copyright_link_hover_color'  => '--reign-footer-copyright-link-hover-color',

			'reign_form_text_color'                    => '--reign-form-text-color',
			'reign_form_background_color'              => '--reign-form-background-color',
			'reign_form_border_color'                  => '--reign-form-border-color',
			'reign_form_placeholder_color'             => '--reign-form-placeholder-color',
			'reign_form_focus_text_color'              => '--reign-form-focus-text-color',
			'reign_form_focus_background_color'        => '--reign-form-focus-background-color',
			'reign_form_focus_border_color'            => '--reign-form-focus-border-color',
			'reign_form_focus_placeholder_color'       => '--reign-form-focus-placeholder-color',
		);

		$admin_colors = array(
			'--reign-header-topbar-bg-color'             => get_theme_mod( 'reign_dark-reign_header_topbar_bg_color', $dark_default( 'reign_header_topbar_bg_color' ) ),
			'--reign-header-topbar-text-color'           => get_theme_mod( 'reign_dark-reign_header_topbar_text_color', $dark_default( 'reign_header_topbar_text_color' ) ),
			'--reign-header-topbar-text-hover-color'     => get_theme_mod( 'reign_dark-reign_header_topbar_text_hover_color', $dark_default( 'reign_header_topbar_text_hover_color' ) ),
			'--reign-header-bg-color'                    => get_theme_mod( 'reign_dark-reign_header_bg_color', $dark_default( 'reign_header_bg_color' ) ),
			'--reign-header-nav-bg-color'                => get_theme_mod( 'reign_dark-reign_header_nav_bg_color', $dark_default( 'reign_header_nav_bg_color' ) ),
			'--reign-title-tagline-typography'           => get_theme_mod( 'reign_dark-reign_title_tagline_typography', $dark_default( 'reign_title_tagline_typography' ) ),
			'--reign-header-main-menu-font'              => get_theme_mod( 'reign_dark-reign_header_main_menu_font', $dark_default( 'reign_header_main_menu_font' ) ),
			'--reign-header-main-menu-text-hover-color'  => get_theme_mod( 'reign_dark-reign_header_main_menu_text_hover_color', $dark_default( 'reign_header_main_menu_text_hover_color' ) ),
			'--reign-header-main-menu-text-active-color' => get_theme_mod( 'reign_dark-reign_header_main_menu_text_active_color', $dark_default( 'reign_header_main_menu_text_active_color' ) ),
			'--reign-header-main-menu-bg-hover-color'    => get_theme_mod( 'reign_dark-reign_header_main_menu_bg_hover_color', $dark_default( 'reign_header_main_menu_bg_hover_color' ) ),
			'--reign-header-main-menu-bg-active-color'   => get_theme_mod( 'reign_dark-reign_header_main_menu_bg_active_color', $dark_default( 'reign_header_main_menu_bg_active_color' ) ),
			'--reign-header-sub-menu-bg-color'           => get_theme_mod( 'reign_dark-reign_header_sub_menu_bg_color', $dark_default( 'reign_header_sub_menu_bg_color' ) ),
			'--reign-header-sub-menu-font'               => get_theme_mod( 'reign_dark-reign_header_sub_menu_font', $dark_default( 'reign_header_sub_menu_font' ) ),
			'--reign-header-sub-menu-text-hover-color'   => get_theme_mod( 'reign_dark-reign_header_sub_menu_text_hover_color', $dark_default( 'reign_header_sub_menu_text_hover_color' ) ),
			'--reign-header-sub-menu-bg-hover-color'     => get_theme_mod( 'reign_dark-reign_header_sub_menu_bg_hover_color', $dark_default( 'reign_header_sub_menu_bg_hover_color' ) ),
			'--reign-header-icon-color'                  => get_theme_mod( 'reign_dark-reign_header_icon_color', $dark_default( 'reign_header_icon_color' ) ),
			'--reign-header-icon-hover-color'            => get_theme_mod( 'reign_dark-reign_header_icon_hover_color', $dark_default( 'reign_header_icon_hover_color' ) ),
			'--reign-mobile-menu-bg-color'               => get_theme_mod( 'reign_dark-reign_mobile_menu_bg_color', $dark_default( 'reign_mobile_menu_bg_color' ) ),
			'--reign-mobile-menu-color'                  => get_theme_mod( 'reign_dark-reign_mobile_menu_color', $dark_default( 'reign_mobile_menu_color' ) ),
			'--reign-mobile-menu-hover-color'            => get_theme_mod( 'reign_dark-reign_mobile_menu_hover_color', $dark_default( 'reign_mobile_menu_hover_color' ) ),
			'--reign-mobile-menu-active-color'           => get_theme_mod( 'reign_dark-reign_mobile_menu_active_color', $dark_default( 'reign_mobile_menu_active_color' ) ),
			'--reign-mobile-menu-active-bg-color'        => get_theme_mod( 'reign_dark-reign_mobile_menu_active_bg_color', $dark_default( 'reign_mobile_menu_active_bg_color' ) ),
			'--reign-left-panel-bg-color'                => get_theme_mod( 'reign_dark-reign_left_panel_bg_color', $dark_default( 'reign_left_panel_bg_color' ) ),
			'--reign-left-panel-toggle-color'            => get_theme_mod( 'reign_dark-reign_left_panel_toggle_color', $dark_default( 'reign_left_panel_toggle_color' ) ),
			'--reign-left-panel-menu-font-color'         => get_theme_mod( 'reign_dark-reign_left_panel_menu_font_color', $dark_default( 'reign_left_panel_menu_font_color' ) ),
			'--reign-left-panel-menu-hover-color'        => get_theme_mod( 'reign_dark-reign_left_panel_menu_hover_color', $dark_default( 'reign_left_panel_menu_hover_color' ) ),
			'--reign-left-panel-menu-active-color'       => get_theme_mod( 'reign_dark-reign_left_panel_menu_active_color', $dark_default( 'reign_left_panel_menu_active_color' ) ),
			'--reign-left-panel-menu-bg-hover-color'     => get_theme_mod( 'reign_dark-reign_left_panel_menu_bg_hover_color', $dark_default( 'reign_left_panel_menu_bg_hover_color' ) ),
			'--reign-left-panel-menu-bg-active-color'    => get_theme_mod( 'reign_dark-reign_left_panel_menu_bg_active_color', $dark_default( 'reign_left_panel_menu_bg_active_color' ) ),
			'--reign-left-panel-menu-icon-active-color'  => get_theme_mod( 'reign_dark-reign_left_panel_menu_icon_active_color', $dark_default( 'reign_left_panel_menu_icon_active_color' ) ),
			'--reign-left-panel-tooltip-bg-color'        => get_theme_mod( 'reign_dark-reign_left_panel_tooltip_bg_color', $dark_default( 'reign_left_panel_tooltip_bg_color' ) ),
			'--reign-left-panel-tooltip-color'           => get_theme_mod( 'reign_dark-reign_left_panel_tooltip_color', $dark_default( 'reign_left_panel_tooltip_color' ) ),
			'--reign-site-body-bg-color'                 => get_theme_mod( 'reign_dark-reign_site_body_bg_color', $dark_default( 'reign_site_body_bg_color' ) ),
			'--reign-site-body-text-color'               => get_theme_mod( 'reign_dark-reign_site_body_text_color', $dark_default( 'reign_site_body_text_color' ) ),
			'--reign-site-alternate-text-color'          => get_theme_mod( 'reign_dark-reign_site_alternate_text_color', $dark_default( 'reign_site_alternate_text_color' ) ),
			'--reign-site-sections-bg-color'             => get_theme_mod( 'reign_dark-reign_site_sections_bg_color', $dark_default( 'reign_site_sections_bg_color' ) ),
			'--reign-site-secondary-bg-color'            => get_theme_mod( 'reign_dark-reign_site_secondary_bg_color', $dark_default( 'reign_site_secondary_bg_color' ) ),
			'--reign-colors-theme'                       => get_theme_mod( 'reign_dark-reign_colors_theme', $dark_default( 'reign_colors_theme' ) ),
			'--reign-site-headings-color'                => get_theme_mod( 'reign_dark-reign_site_headings_color', $dark_default( 'reign_site_headings_color' ) ),
			'--reign-site-link-color'                    => get_theme_mod( 'reign_dark-reign_site_link_color', $dark_default( 'reign_site_link_color' ) ),
			'--reign-site-link-hover-color'              => get_theme_mod( 'reign_dark-reign_site_link_hover_color', $dark_default( 'reign_site_link_hover_color' ) ),
			'--reign-accent-color'                       => get_theme_mod( 'reign_dark-reign_accent_color', $dark_default( 'reign_accent_color' ) ),
			'--reign-accent-hover-color'                 => get_theme_mod( 'reign_dark-reign_accent_hover_color', $dark_default( 'reign_accent_hover_color' ) ),
			'--reign-site-button-text-color'             => get_theme_mod( 'reign_dark-reign_site_button_text_color', $dark_default( 'reign_site_button_text_color' ) ),
			'--reign-site-button-text-hover-color'       => get_theme_mod( 'reign_dark-reign_site_button_text_hover_color', $dark_default( 'reign_site_button_text_hover_color' ) ),
			'--reign-site-button-bg-color'               => get_theme_mod( 'reign_dark-reign_site_button_bg_color', $dark_default( 'reign_site_button_bg_color' ) ),
			'--reign-site-button-bg-hover-color'         => get_theme_mod( 'reign_dark-reign_site_button_bg_hover_color', $dark_default( 'reign_site_button_bg_hover_color' ) ),
			'--reign-site-border-color'                  => get_theme_mod( 'reign_dark-reign_site_border_color', $dark_default( 'reign_site_border_color' ) ),
			'--reign-site-hr-color'                      => get_theme_mod( 'reign_dark-reign_site_hr_color', $dark_default( 'reign_site_hr_color' ) ),
			'--reign-footer-widget-area-bg-color'        => get_theme_mod( 'reign_dark-reign_footer_widget_area_bg_color', $dark_default( 'reign_footer_widget_area_bg_color' ) ),
			'--reign-footer-widget-title-color'          => get_theme_mod( 'reign_dark-reign_footer_widget_title_color', $dark_default( 'reign_footer_widget_title_color' ) ),
			'--reign-footer-widget-text-color'           => get_theme_mod( 'reign_dark-reign_footer_widget_text_color', $dark_default( 'reign_footer_widget_text_color' ) ),
			'--reign-footer-widget-link-color'           => get_theme_mod( 'reign_dark-reign_footer_widget_link_color', $dark_default( 'reign_footer_widget_link_color' ) ),
			'--reign-footer-widget-link-hover-color'     => get_theme_mod( 'reign_dark-reign_footer_widget_link_hover_color', $dark_default( 'reign_footer_widget_link_hover_color' ) ),
			'--reign-footer-copyright-bg-color'          => get_theme_mod( 'reign_dark-reign_footer_copyright_bg_color', $dark_default( 'reign_footer_copyright_bg_color' ) ),
			'--reign-footer-copyright-text-color'        => get_theme_mod( 'reign_dark-reign_footer_copyright_text_color', $dark_default( 'reign_footer_copyright_text_color' ) ),
			'--reign-footer-copyright-link-color'        => get_theme_mod( 'reign_dark-reign_footer_copyright_link_color', $dark_default( 'reign_footer_copyright_link_color' ) ),
			'--reign-footer-copyright-link-hover-color'  => get_theme_mod( 'reign_dark-reign_footer_copyright_link_hover_color', $dark_default( 'reign_footer_copyright_link_hover_color' ) ),

			'--reign-form-text-color'                    => get_theme_mod( 'reign_dark-reign_form_text_color', $dark_default( 'reign_form_text_color' ) ),
			'--reign-form-background-color'              => get_theme_mod( 'reign_dark-reign_form_background_color', $dark_default( 'reign_form_background_color' ) ),
			'--reign-form-border-color'                  => get_theme_mod( 'reign_dark-reign_form_border_color', $dark_default( 'reign_form_border_color' ) ),
			'--reign-form-placeholder-color'             => get_theme_mod( 'reign_dark-reign_form_placeholder_color', $dark_default( 'reign_form_placeholder_color' ) ),
			'--reign-form-focus-text-color'              => get_theme_mod( 'reign_dark-reign_form_focus_text_color', $dark_default( 'reign_form_focus_text_color' ) ),
			'--reign-form-focus-background-color'        => get_theme_mod( 'reign_dark-reign_form_focus_background_color', $dark_default( 'reign_form_focus_background_color' ) ),
			'--reign-form-focus-border-color'            => get_theme_mod( 'reign_dark-reign_form_focus_border_color', $dark_default( 'reign_form_focus_border_color' ) ),
			'--reign-form-focus-placeholder-color'       => get_theme_mod( 'reign_dark-reign_form_focus_placeholder_color', $dark_default( 'reign_form_focus_placeholder_color' ) ),
		);

		$color_string = '';
		foreach ( $colors as $key => $property ) {
			// Prefer user's explicit override; fall back to the reign_dark preset
			// (captured in $admin_colors with defaults set above). Empty-string
			// check guards against presets that omit a key.
			$color = isset( $admin_colors[ $property ] ) && ! empty( $admin_colors[ $property ] )
				? $admin_colors[ $property ]
				: '';

			if ( $color ) {
				$color_string .= $property . ':' . $color . '!important;';
			}

			$color_rgb = hex2rgb( (string) $color );
			if ( $color_rgb ) {
				$color_string .= $property . '-rgb:' . $color_rgb . '!important;';
			}
		}

		return '.dark-mode{' . $color_string . '}';
	}
}
endif;

/**
 * Build the modern per-palette dark-mode CSS.
 *
 * Emits a [data-bx-mode="dark"] block (the selector the 8.0.0 light/dark
 * toggle sets - the legacy load_color_dark_palette() above targets the old
 * .dark-mode class and no longer applies). For each --reign-* var it prefers
 * the buyer's "Dark Mode Colors" override ({scheme}-dark-{var}) and falls back
 * to the per-palette dark default from reign_dark_color_scheme_set(), which
 * matches the compiled [data-bx-mode="dark"] SCSS - so output is unchanged
 * until a value is overridden. !important + loaded after the compiled CSS so
 * overrides win over the SCSS defaults.
 *
 * @since 8.0.0
 * @return string
 */
if ( ! function_exists( 'reign_load_dark_mode_palette' ) ) :
function reign_load_dark_mode_palette() {
	$scheme       = get_theme_mod( 'reign_color_scheme', 'reign_clean' );
	$defaults_all = function_exists( 'reign_dark_color_scheme_set' ) ? reign_dark_color_scheme_set() : array();
	$defaults     = isset( $defaults_all[ $scheme ] )
		? $defaults_all[ $scheme ]
		: ( isset( $defaults_all['reign_clean'] ) ? $defaults_all['reign_clean'] : array() );

	// reign_* setting key => --reign-* CSS custom property.
	$map = array(
		'reign_header_topbar_bg_color'             => '--reign-header-topbar-bg-color',
		'reign_header_topbar_text_color'           => '--reign-header-topbar-text-color',
		'reign_header_topbar_text_hover_color'     => '--reign-header-topbar-text-hover-color',
		'reign_header_bg_color'                    => '--reign-header-bg-color',
		'reign_header_nav_bg_color'                => '--reign-header-nav-bg-color',
		'reign_title_tagline_typography'           => '--reign-title-tagline-typography',
		'reign_header_main_menu_font'              => '--reign-header-main-menu-font',
		'reign_header_main_menu_text_hover_color'  => '--reign-header-main-menu-text-hover-color',
		'reign_header_main_menu_text_active_color' => '--reign-header-main-menu-text-active-color',
		'reign_header_main_menu_bg_hover_color'    => '--reign-header-main-menu-bg-hover-color',
		'reign_header_main_menu_bg_active_color'   => '--reign-header-main-menu-bg-active-color',
		'reign_header_sub_menu_bg_color'           => '--reign-header-sub-menu-bg-color',
		'reign_header_sub_menu_font'               => '--reign-header-sub-menu-font',
		'reign_header_sub_menu_text_hover_color'   => '--reign-header-sub-menu-text-hover-color',
		'reign_header_sub_menu_bg_hover_color'     => '--reign-header-sub-menu-bg-hover-color',
		'reign_header_icon_color'                  => '--reign-header-icon-color',
		'reign_header_icon_hover_color'            => '--reign-header-icon-hover-color',
		'reign_mobile_menu_bg_color'               => '--reign-mobile-menu-bg-color',
		'reign_mobile_menu_color'                  => '--reign-mobile-menu-color',
		'reign_mobile_menu_hover_color'            => '--reign-mobile-menu-hover-color',
		'reign_mobile_menu_active_color'           => '--reign-mobile-menu-active-color',
		'reign_mobile_menu_active_bg_color'        => '--reign-mobile-menu-active-bg-color',
		'reign_left_panel_bg_color'                => '--reign-left-panel-bg-color',
		'reign_left_panel_toggle_color'            => '--reign-left-panel-toggle-color',
		'reign_left_panel_menu_font_color'         => '--reign-left-panel-menu-font-color',
		'reign_left_panel_menu_hover_color'        => '--reign-left-panel-menu-hover-color',
		'reign_left_panel_menu_active_color'       => '--reign-left-panel-menu-active-color',
		'reign_left_panel_menu_bg_hover_color'     => '--reign-left-panel-menu-bg-hover-color',
		'reign_left_panel_menu_bg_active_color'    => '--reign-left-panel-menu-bg-active-color',
		'reign_left_panel_menu_icon_active_color'  => '--reign-left-panel-menu-icon-active-color',
		'reign_left_panel_tooltip_bg_color'        => '--reign-left-panel-tooltip-bg-color',
		'reign_left_panel_tooltip_color'           => '--reign-left-panel-tooltip-color',
		'reign_site_body_bg_color'                 => '--reign-site-body-bg-color',
		'reign_site_body_text_color'               => '--reign-site-body-text-color',
		'reign_site_alternate_text_color'          => '--reign-site-alternate-text-color',
		'reign_site_sections_bg_color'             => '--reign-site-sections-bg-color',
		'reign_site_secondary_bg_color'            => '--reign-site-secondary-bg-color',
		'reign_colors_theme'                       => '--reign-colors-theme',
		'reign_site_headings_color'                => '--reign-site-headings-color',
		'reign_site_link_color'                    => '--reign-site-link-color',
		'reign_site_link_hover_color'              => '--reign-site-link-hover-color',
		'reign_accent_color'                       => '--reign-accent-color',
		'reign_accent_hover_color'                 => '--reign-accent-hover-color',
		'reign_site_button_text_color'             => '--reign-site-button-text-color',
		'reign_site_button_text_hover_color'       => '--reign-site-button-text-hover-color',
		'reign_site_button_bg_color'               => '--reign-site-button-bg-color',
		'reign_site_button_bg_hover_color'         => '--reign-site-button-bg-hover-color',
		'reign_site_border_color'                  => '--reign-site-border-color',
		'reign_site_hr_color'                      => '--reign-site-hr-color',
		'reign_footer_widget_area_bg_color'        => '--reign-footer-widget-area-bg-color',
		'reign_footer_widget_title_color'          => '--reign-footer-widget-title-color',
		'reign_footer_widget_text_color'           => '--reign-footer-widget-text-color',
		'reign_footer_widget_link_color'           => '--reign-footer-widget-link-color',
		'reign_footer_widget_link_hover_color'     => '--reign-footer-widget-link-hover-color',
		'reign_footer_copyright_bg_color'          => '--reign-footer-copyright-bg-color',
		'reign_footer_copyright_text_color'        => '--reign-footer-copyright-text-color',
		'reign_footer_copyright_link_color'        => '--reign-footer-copyright-link-color',
		'reign_footer_copyright_link_hover_color'  => '--reign-footer-copyright-link-hover-color',
		'reign_form_text_color'                    => '--reign-form-text-color',
		'reign_form_background_color'              => '--reign-form-background-color',
		'reign_form_border_color'                  => '--reign-form-border-color',
		'reign_form_placeholder_color'             => '--reign-form-placeholder-color',
		'reign_form_focus_text_color'              => '--reign-form-focus-text-color',
		'reign_form_focus_background_color'        => '--reign-form-focus-background-color',
		'reign_form_focus_border_color'            => '--reign-form-focus-border-color',
		'reign_form_focus_placeholder_color'       => '--reign-form-focus-placeholder-color',
	);

	$css = '';
	foreach ( $map as $modkey => $cssvar ) {
		$fallback = isset( $defaults[ $modkey ] ) ? $defaults[ $modkey ] : '';
		$val      = get_theme_mod( $scheme . '-dark-' . $modkey, $fallback );
		if ( '' === $val || false === $val ) {
			continue;
		}
		$css .= $cssvar . ':' . $val . '!important;';
		if ( function_exists( 'hex2rgb' ) ) {
			$rgb = hex2rgb( (string) $val );
			if ( $rgb ) {
				$css .= $cssvar . '-rgb:' . $rgb . '!important;';
			}
		}
	}

	return $css ? '[data-bx-mode="dark"]{' . $css . '}' : '';
}
endif;

/**
 * Load global border radius
 *
 * @since 6.9.2
 * @return string
 */
if ( ! function_exists( 'load_border_radius' ) ) :
function load_border_radius() {
	$global_radius = array(
		'reign_global_border_radius_option' => '--reign-global-border-radius',
		'reign_global_button_radius'        => '--reign-global-button-radius',
		'reign_global_form_radius'          => '--reign-global-form-radius',
	);

	// Global Border Radius.
	$reign_global_border_radius_option = get_theme_mod( 'reign_global_border_radius_option', '8px' );
	$reign_global_button_radius        = get_theme_mod( 'reign_global_button_radius', '6px' );
	$reign_global_form_radius          = get_theme_mod( 'reign_global_form_radius', '6px' );

	$admin_radius = array(
		'--reign-global-border-radius' => $reign_global_border_radius_option,
		'--reign-global-button-radius' => $reign_global_button_radius,
		'--reign-global-form-radius'   => $reign_global_form_radius,
	);

	$fallback_border_radius = array(
		'reign_global_border_radius_option' => '8px',
		'reign_global_button_radius'        => '6px',
		'reign_global_form_radius'          => '6px',
	);

	$radius_string = '';
	foreach ( $global_radius as $key => $property ) {
		$fallback_radius = isset( $fallback_border_radius[ $key ] ) ? $fallback_border_radius[ $key ] : '3px';
		$border_radius   = get_option( $key, $fallback_radius );

		if ( isset( $admin_radius[ $property ] ) ) {
			$border_radius = $admin_radius[ $property ];
		}

		if ( $border_radius ) {
			$radius_string .= $property . ':' . $border_radius . ';';
		}
	}

	return ':root{' . $radius_string . '}';
}
endif;

/**
 * Emit per-user nonces in a logged-in-only inline script.
 *
 * Page-cache plugins (WP Rocket, WP Super Cache, LiteSpeed Cache,
 * Cloudflare APO with the WP plugin, etc.) skip caching for
 * authenticated requests by default. So this inline `<script>` is only
 * ever served live — never baked into a cached HTML response. Anonymous
 * visitors get the cached HTML which contains NO nonces; they don't
 * need any because they can't perform the friendship / notification /
 * message actions these nonces protect. The login form itself emits a
 * fresh wp_nonce_field() inside its own POST request so authentication
 * is unaffected.
 *
 * Why wp_footer (not wp_head): the wp-main script that consumes these
 * values is enqueued in footer, so the nonces just need to exist before
 * that script runs.
 */
if ( ! function_exists( 'reign_emit_logged_in_nonces' ) ) :
function reign_emit_logged_in_nonces() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$nonces = array(
		'reign_login_nonce'        => wp_create_nonce( 'reign-sign-form' ),
		'reign_friendship_nonce'   => wp_create_nonce( 'reign_friendship_nonce' ),
		'reign_notification_nonce' => wp_create_nonce( 'reign_notification_nonce' ),
		'reign_message_nonce'      => wp_create_nonce( 'reign_message_nonce' ),
	);
	$rest_nonce = wp_create_nonce( 'wp_rest' );
	?>
	<script id="reign-per-user-nonces">
	window.wp_main_js_obj = window.wp_main_js_obj || {};
	Object.assign( window.wp_main_js_obj, <?php echo wp_json_encode( $nonces ); ?> );
	// REST nonce for the modern fetch-based client (reignApi).
	window.reignApiConfig = window.reignApiConfig || {};
	window.reignApiConfig.rest_nonce = <?php echo wp_json_encode( $rest_nonce ); ?>;
	</script>
	<?php
}
endif;
add_action( 'wp_footer', 'reign_emit_logged_in_nonces', 30 );

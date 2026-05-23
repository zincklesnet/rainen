<?php
/**
 * Plugin Name: Wbcom Designs - BuddyPress Member Reviews
 * Plugin URI: https://wbcomdesigns.com/downloads/buddypress-user-profile-reviews/
 * Description: Enhances the BuddyPress community by allowing registered users to post reviews on other members' profiles. This feature is available exclusively to registered members and ensures unbiased feedback by preventing users from reviewing their own profiles.
 * Version: 3.6.0
 * Author: Wbcom Designs
 * Author URI: https://wbcomdesigns.com
 * License: GPLv2+
 * Text Domain: bp-member-reviews
 * Domain Path: /languages
 *
 * @package BuddyPress_Member_Reviews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin version and path constants.
 */
define( 'BUPR_PLUGIN_VERSION', '3.6.0' );
define( 'BUPR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BUPR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('BUPR_PLUGIN_BASENAME', plugin_basename( __FILE__ ));

/**
 * Load plugin text-domain for translations.
 */
if ( ! function_exists( 'bupr_load_textdomain' ) ) {
	add_action( 'init', 'bupr_load_textdomain' );
	function bupr_load_textdomain() {
		$domain = 'bp-member-reviews';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, BUPR_PLUGIN_PATH . 'languages/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname(BUPR_PLUGIN_BASENAME) . '/languages' );
	}
}


/**
 * Check if BuddyPress is active and meets minimum requirements.
 *
 * @return bool True if BuddyPress meets requirements, false otherwise.
 */
function bupr_check_buddypress_compatibility() {
    // Check if BuddyPress is active
    if (!class_exists('BuddyPress')) {
        return false;
    }
	 if (!function_exists('bp_get_version')) {
        return false;
    }
    
    // Check BuddyPress version
   

	if ( defined( 'BP_PLATFORM_VERSION' ) ) {
		 // It's Buddyoss — compare Buddyoss version
        $min_bb_version = '1.8.0';
        if ( version_compare( BP_PLATFORM_VERSION, $min_bb_version, '<' ) ) {
            add_action( 'admin_notices', 'bupr_outdated_buddyboss_notice' );
            return false;
        }
    } else {
        // It's BuddyPress — compare BuddyPress version
		 $min_bp_version = '6.0.0';
        if ( version_compare( bp_get_version(), $min_bp_version, '<' ) ) {
            add_action( 'admin_notices', 'bupr_outdated_buddypress_notice' );
            return false;
        }
    }
    
    return true;
}


/**
 * Admin notice for outdated BuddyPress version.
 */
function bupr_outdated_buddypress_notice() {
    echo '<div class="error"><p>';
    printf(
        __('%1$s requires BuddyPress version %2$s or higher.', 'bp-member-reviews'),
        '<strong>BuddyPress Member Reviews</strong>',
        '6.0.0'
    );
    echo '</p></div>';
}
/**
 * Admin notice for outdated BuddyPress version.
 */
function bupr_outdated_buddyboss_notice() {
    echo '<div class="error"><p>';
    printf(
        __('%1$s requires BuddyBoss version %2$s or higher.', 'bp-member-reviews'),
        '<strong>BuddyPress Member Reviews</strong>',
        '1.8.0'
    );
    echo '</p></div>';
}

/**
 * Admin notice for inactive BuddyPress members component.
 */
function bupr_inactive_members_component_notice() {
    echo '<div class="error"><p>';
    printf(
        __('%1$s requires the BuddyPress Members component to be active.', 'bp-member-reviews'),
        '<strong>BuddyPress Member Reviews</strong>'
    );
    echo '</p></div>';
}
/**
 * Check whether the plugin is active or not.
 *
 * @return boolean
 */
function bupr_is_plugin_active() {
    // Simplified check for plugin status
    if ( is_multisite() ) {
        $active_plugins = get_site_option('active_sitewide_plugins');
        if (isset($active_plugins[BUPR_PLUGIN_BASENAME])) {
            return true;
        }
        
        $active_plugins = get_option('active_plugins');
        return in_array(BUPR_PLUGIN_BASENAME, (array) $active_plugins);
    } else {
        $active_plugins = get_option('active_plugins');
        return in_array(BUPR_PLUGIN_BASENAME, (array) $active_plugins);
    }
}

/**
 * Plugin activation hook.
 */
function bupr_plugin_activation() {
    // Check if BuddyPress is active before proceeding
    if (!class_exists('BuddyPress')) {
        deactivate_plugins(BUPR_PLUGIN_BASENAME);
        add_action('admin_notices', 'bupr_required_plugin_admin_notice');
        return false;
    }
    
    // Set default options
    $default_criteria = array(
        'profile_multi_rating_allowed' => '1',
        'profile_rating_fields' => array(
            __('Member Response', 'bp-member-reviews') => 'yes',
            __('Member Skills', 'bp-member-reviews') => 'yes',
        ),
    );
    
    if (!get_option('bupr_admin_settings')) {
        update_option('bupr_admin_settings', $default_criteria);
    }
    
    // Set plugin version in options
    update_option('bupr_plugin_version', BUPR_PLUGIN_VERSION);
    
    // Schedule review data recalculation
    if (!wp_next_scheduled('bupr_cron_recalculate_user_reviews_batch')) {
        wp_schedule_event(time(), 'daily', 'bupr_cron_recalculate_user_reviews_batch');
    }
    
    // Set transient for redirect to settings page
    set_transient('_bupr_activation_redirect', true, 30);
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'bupr_plugin_activation');

/**
 * Include required plugin files after BuddyPress is loaded.
 */
if ( ! function_exists( 'bupr_plugins_files' ) ) {
	add_action( 'plugin_loaded', 'bupr_plugins_files' );
	function bupr_plugins_files() {	

		// Only proceed if BuddyPress is compatible
		if (!bupr_check_buddypress_compatibility() || !bupr_is_plugin_active()) {
			return;
		}
		// Add plugin action links
    	add_filter('plugin_action_links_' . BUPR_PLUGIN_BASENAME, 'bupr_admin_page_link');

			
		// List of required files to include.
		$include_files = array(
			'includes/class-buprglobals.php',
			'admin/wbcom/wbcom-admin-settings.php',
			'includes/bupr-scripts.php',
			'admin/bupr-admin.php',
			'admin/class-bupr-admin-feedback.php',
			'includes/bupr-filters.php',
			'includes/bupr-shortcodes.php',
			'includes/widgets/display-review.php',
			'includes/widgets/member-rating.php',
			'includes/bupr-ajax.php',
			'includes/bupr-notification.php',
			'includes/bupr-general-functions.php',
		);

		foreach ($include_files as $file) {
			$file_path = BUPR_PLUGIN_PATH . $file;
			if (file_exists($file_path)) {
				include_once $file_path;
			} else {
				error_log(sprintf('BuddyPress Member Reviews: Required file %s not found', $file_path));
			}
		}
	}
}


/**
 * Add settings link to plugin action links.
 *
 * @param array $links The plugin setting links array.
 *
 * @return array
 */
function bupr_admin_page_link( $links ) {
	$settings_link = array(
		'<a href="' . admin_url( 'admin.php?page=bp-member-review-settings' ) . '">' . esc_html__( 'Settings', 'bp-member-reviews' ) . '</a>',
	);
	return array_merge( $links, $settings_link );
}

/**
 * Deactivate the plugin if BuddyPress is not active.
 */

 function bupr_requires_buddypress() {
		$network_active_plugins = get_site_option( 'active_sitewide_plugins' );
		if(empty($network_active_plugins)){
			$network_active_plugins = get_option('active_plugins');
			$network_active_plugins = array_flip($network_active_plugins);
		}
        if ( ! class_exists( 'BuddyPress' ) ) {
            deactivate_plugins( BUPR_PLUGIN_BASENAME );
            add_action( 'admin_notices', 'bupr_required_plugin_admin_notice' );
        }
		elseif( is_multisite() ){
			if ( !is_network_admin() ) {
				global $current_blog;
				$current_blog_id = (int) $current_blog->blog_id;
				switch_to_blog( $current_blog_id );
				if (!array_key_exists( BUPR_PLUGIN_BASENAME , $network_active_plugins )) {
					deactivate_plugins( BUPR_PLUGIN_BASENAME );	
				} 	
				restore_current_blog();
			}else{
				if(! array_key_exists( 'buddypress/bp-loader.php' , $network_active_plugins ) ){
					deactivate_plugins( BUPR_PLUGIN_BASENAME );	
					add_action( 'admin_notices', 'bupr_required_plugin_admin_notice' );
				}
			}
		}
		// Check if members component is active
		if (function_exists('bp_is_active') && !bp_is_active('members')) {
			deactivate_plugins( BUPR_PLUGIN_BASENAME );	
			add_action('admin_notices', 'bupr_inactive_members_component_notice');
		}
}
add_action( 'admin_init', 'bupr_requires_buddypress' );



/**
 * Show admin notice for plugin when active action is performed.
 */
function bp_member_review_show_admin_notice($admin_notice){
	if( isset($_GET['activate']) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		add_action( 'admin_notices', $admin_notice);
		unset($_GET['activate']);
	}
}

/**
 * Admin notice to indicate BuddyPress is required.
 */
function bupr_required_plugin_admin_notice() {
	$plugin_name = esc_html__( 'BuddyPress Member Reviews', 'bp-member-reviews' );
	$bp_plugin   = esc_html__( 'BuddyPress or BuddyBoss', 'bp-member-reviews' );
	echo '<div class="error"><p>';
	printf(
		 /* translators: %1$s: BuddyPress Member Reviews; %2$s: BuddyPress. */
		esc_html__( '%1$s requires %2$s to be installed and active.', 'bp-member-reviews' ),
		'<strong>' . esc_html( $plugin_name ) . '</strong>',
		'<strong>' . esc_html( $bp_plugin ) . '</strong>'
	);
	echo '</p></div>';
}


/**
 * Redirect to plugin settings page after activation.
 */
function bupr_activation_redirect_settings( $plugin ) {
	if ( $plugin === BUPR_PLUGIN_BASENAME && class_exists( 'BuddyPress' ) && ! is_multisite() && bupr_check_buddypress_compatibility()) {
		wp_safe_redirect( admin_url( 'admin.php?page=bp-member-review-settings' ) );
		exit;
	}
}
add_action( 'activated_plugin', 'bupr_activation_redirect_settings' );

/**
 * Perform plugin activation redirect.
 */
function bp_member_review_do_activation_redirect() {
	if ( get_transient( '_bupr_activation_redirect' ) && ! is_multisite() && bupr_check_buddypress_compatibility()) {
		delete_transient( '_bupr_activation_redirect' );
		wp_safe_redirect( admin_url( 'admin.php?page=bp-member-review-settings' ) );
	}
}
add_action( 'admin_init', 'bp_member_review_do_activation_redirect' );

/**
 * Translate site URL using WPML for frontend use.
 *
 * @param string $url The URL to translate.
 *
 * @return string Translated URL.
 */
function bupr_site_url( $url ) {
	if ( ! is_admin() && false === strpos( $url, 'wp-admin' ) ) {
		return untrailingslashit( apply_filters( 'wpml_home_url', $url ) );
	}
	return $url;
}

/**
 * Unschedule the cron event on plugin deactivation.
 */
function bupr_unschedule_review_recalculation() {
    $timestamp = wp_next_scheduled( 'bupr_cron_recalculate_user_reviews_batch' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'bupr_cron_recalculate_user_reviews_batch' );
    }
	delete_transient('_bupr_activation_redirect');
    delete_option( 'bupr_current_batch' ); // Clean up the stored batch number
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bupr_unschedule_review_recalculation' );


<?php
/*
  Plugin Name: BP Group Analytics
  Plugin URI: wordpress.org/plugins/bp-group-analytics/
  GitHub: https://github.com/erviveksharma/bp-group-analytics

  Description: BP Group Analytics.
  Version: 1.2
  Revision Date: November 17,2022
  Requires at least: WP 3.5.1, BuddyPress 1.6.5
  Tested up to: WP 6.1, BuddyPress 10.6.0
  License:  GNU General Public License 3.0 or newer (GPL) http://www.gnu.org/licenses/gpl.html
  Author: Vivek Sharma
  Author URI: http://ProvisTechnologies.com

 */


/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */

//some constants that can be checked when extending this plugin
define('BP_GROUP_ANALYTICS_IS_INSTALLED', 1.2);
define('BP_GROUP_ANALYTICS_VERSION', '1.2');


//allow override of URL slug
if (!defined('BP_GROUP_ANALYTICS_SLUG')) {
    define('BP_GROUP_ANALYTICS_SLUG', 'analytics');
    define('BP_GROUP_ANALYTICS_OPTIONS_META_TITLE', 'bp_group_analytics_xprofile_selected_fields');
}

/**
 * Nifty function to get the name of the directory bp_group_analytics plugin is installed in.
 * @author  Vivek Sharma
 * @version 1
 * @since 1
 */
function bp_group_analytics_dir() {
    if (stristr(__FILE__, '/'))
        $bp_gr_dir = explode('/plugins/', dirname(__FILE__));
    else
        $bp_gr_dir = explode('\\plugins\\', dirname(__FILE__));
    return str_replace('\\', '/', end($bp_gr_dir)); //takes care of MS slashes
}

$bp_gr_dir = bp_group_analytics_dir();

define('BP_GROUP_ANALYTICS_DIR', $bp_gr_dir); //the name of the directory that bp_group_analytics  files are located.

/**
 * @author Vivek Sharma
 * @global type $wpdb
 * @return type
 * @since 1
 * @version 1 added the bp_analytics_load_textdomain() call
 */
function bp_group_analytics_init() {
    global $wpdb;
    if ( is_multisite() && BP_ROOT_BLOG != $wpdb->blogid ) {
        return ;
    }
    if ( ! bp_is_active( 'groups' ) ) {
        return ;
    }
    // Because our loader file uses BP_Component, it requires BP 1.6.5 or greater.
    if (version_compare(BP_VERSION, '1.6.5', '>')) {
        require( dirname(__FILE__) . '/buddypress-group-analytics.php' );
        bp_group_analytics_include_files();
    }
    bp_group_analytics_load_textdomain();
}

add_action('bp_loaded', 'bp_group_analytics_init', 50);

/**
 * @author Vivek Sharma
 * @since 1.0
 * @version 1.0
 */
function bp_group_analytics_load_textdomain() {
    $domain = 'bp-group-analytics';
    // The "plugin_locale" filter is also used in load_plugin_textdomain()
    $locale = apply_filters('plugin_locale', get_locale(), $domain);
    load_textdomain($domain, WP_LANG_DIR . '/bp-group-analytics/' . $domain . '-' . $locale . '.mo');

    if (file_exists(dirname(__FILE__) . '/languages/bp-group-analytics-' . get_locale() . '.mo')) {
        load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
}

# check required wordpress plugins
register_activation_hook( __FILE__, 'bp_group_analytics_import_install' );
function bp_group_analytics_import_install() {
    global $bp;

    # Check whether BP is active and whether Groups component is loaded, and throw error if not
    if(!(function_exists('BuddyPress') || is_a($bp,'BuddyPress')) || !bp_is_active('groups')) {
       echo 'BuddyPress is not installed or the Groups component is not activated. Cannot continue.';
        exit;
    }
}

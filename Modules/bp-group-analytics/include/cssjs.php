<?php
/**
 * Created by PhpStorm.
 * User: anish ojha
 * Date: 14-04-2017
 * Time: 15:25
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * bp_group_analytics_front_cssjs()
 *
 * This function will enqueue the components css and javascript files
 * only when the front group documents page is displayed
 */
function bp_group_analytics_front_cssjs() {
    global $bp;

    //if we're on a group page
    if ($bp->current_component == $bp->groups->slug) {
        // JS
        wp_enqueue_script('google_chart_loader_js', '//www.gstatic.com/charts/loader.js');
        wp_enqueue_script('bp-group-analytics', WP_PLUGIN_URL . '/' . BP_GROUP_ANALYTICS_DIR . '/js/general.js', array('jquery'), BP_GROUP_ANALYTICS_VERSION);

        // CSS
        wp_register_style('bp-group-analytics', WP_PLUGIN_URL . '/' . BP_GROUP_ANALYTICS_DIR . '/css/style.css', false, BP_GROUP_ANALYTICS_VERSION);
        wp_enqueue_style('bp-group-analytics');
    }
}

add_action('wp_enqueue_scripts', 'bp_group_analytics_front_cssjs');

/**
 * bp_group_analytics_admin_cssjs()
 *
 * This function will enqueue the css and js files for the admin back-end
 */
function bp_group_analytics_admin_cssjs() {
    //wp_enqueue_style('bp-group-analytics-admin', WP_PLUGIN_URL . '/' . BP_GROUP_ANALYTICS_DIR . '/css/admin.css');
}

add_action('admin_head', 'bp_group_analytics_admin_cssjs');

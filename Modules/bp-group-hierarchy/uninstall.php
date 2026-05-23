<?php
/**
 * BP Group Hierarchy — Uninstall.
 *
 * Fired when the plugin is deleted via the WordPress admin.
 * Cleans up all options and group meta created by the plugin.
 *
 * @package BPGroupHierarchy
 * @since   1.1.0
 * @updated 2.0.0 — Cleans up all v2.0 meta keys and options.
 */

// Exit if not called by WordPress uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/* -----------------------------------------------------------
 * 1. Single-site options
 * ----------------------------------------------------------- */

$single_site_options = array(
    // v1.x options.
    'bpgh_show_parent_in_header',
    'bpgh_show_children_list',

    // v2.0 feature toggles.
    'bpgh_enable_tags',
    'bpgh_enable_categories',
    'bpgh_enable_premium',
    'bpgh_enable_tooltips',

    // v2.0 permissions.
    'bpgh_parent_creation_role',
    'bpgh_require_child_approval',
    'bpgh_visibility_inheritance',

    // v2.0 premium settings.
    'bpgh_premium_cost',
    'bpgh_zcred_point_type',

    // v2.0 tag moderation.
    'bpgh_tag_moderation',
    'bpgh_approved_tags',

    // v2.0 tooltip settings.
    'bpgh_tooltip_show_admins',
    'bpgh_tooltip_show_tags',
    'bpgh_tooltip_show_category',

    // v2.0 categories data.
    'bpgh_categories',
);

foreach ( $single_site_options as $option ) {
    delete_option( $option );
}

/* -----------------------------------------------------------
 * 2. Network options (multisite)
 * ----------------------------------------------------------- */
if ( is_multisite() ) {
    $network_options = array(
        'bpgh_network_cross_site',
        'bpgh_default_visibility',
        'bpgh_allow_subsite_widgets',
        'bpgh_network_browsing',
    );

    foreach ( $network_options as $option ) {
        delete_site_option( $option );
    }
}

/* -----------------------------------------------------------
 * 3. Group meta cleanup
 *
 * Remove all bpgh_* meta keys from groups.
 * Uses a direct DB query for efficiency — BuddyPress may not
 * be fully loaded during uninstall.
 * ----------------------------------------------------------- */
global $wpdb;

$bp_prefix  = $wpdb->base_prefix;
$meta_table = $bp_prefix . 'bp_groups_groupmeta';

if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $meta_table ) ) === $meta_table ) {

    // v1.x meta key.
    $meta_keys = array(
        'bpgh_parent_id',

        // v2.0 meta keys.
        'bpgh_tags',
        'bpgh_category',
        'bpgh_premium_tier',
        'bpgh_premium_bg_image',
        'bpgh_premium_bg_animated',
        'bpgh_premium_bg_video',
        'bpgh_premium_color_primary',
        'bpgh_premium_color_secondary',
        'bpgh_premium_color_accent',
        'bpgh_premium_typography',
        'bpgh_pending_approval',
        'bpgh_visibility',
    );

    foreach ( $meta_keys as $key ) {
        $wpdb->delete( $meta_table, array( 'meta_key' => $key ) );
    }
}

<?php
/**
 * BP Group Hierarchy — Bootstrap loader.
 *
 * Defines constants, loads core files after BuddyPress,
 * enqueues conditional assets, and registers widgets.
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 * @updated 2.0.0 — Loads new v2.0 modules (tags, categories, permissions,
 *                   premium, tooltips, group types, AJAX, shortcodes).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------
 * 1. Plugin Constants
 * ----------------------------------------------------------- */

if ( ! defined( 'BP_GROUP_HIERARCHY_PLUGIN_URL' ) ) {
    define( 'BP_GROUP_HIERARCHY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BP_GROUP_HIERARCHY_PLUGIN_DIR' ) ) {
    define( 'BP_GROUP_HIERARCHY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/* -----------------------------------------------------------
 * 2. Load Core Files AFTER BuddyPress Loads
 * ----------------------------------------------------------- */

add_action( 'plugins_loaded', function () {

    if ( ! function_exists( 'buddypress' ) ) {
        add_action( 'admin_notices', 'bpgh_buddypress_missing_notice' );
        return;
    }

    $dir = BP_GROUP_HIERARCHY_PLUGIN_DIR;

    // Core hierarchy (v1.1.0).
    require_once $dir . 'bp-group-hierarchy-classes.php';
    require_once $dir . 'bp-group-hierarchy-actions.php';
    require_once $dir . 'bp-group-hierarchy-filters.php';
    require_once $dir . 'bp-group-hierarchy-settings.php';
    require_once $dir . 'bp-group-hierarchy-compat-youzer.php';

    // v2.0.0 modules.
    require_once $dir . 'bp-group-hierarchy-tags.php';
    require_once $dir . 'bp-group-hierarchy-categories.php';
    require_once $dir . 'bp-group-hierarchy-permissions.php';
    require_once $dir . 'bp-group-hierarchy-premium.php';
    require_once $dir . 'bp-group-hierarchy-tooltips.php';
    require_once $dir . 'bp-group-hierarchy-group-types.php';
    require_once $dir . 'bp-group-hierarchy-ajax.php';
    require_once $dir . 'bp-group-hierarchy-shortcodes.php';

    // Multisite modules.
    if ( is_multisite() ) {
        require_once $dir . 'bp-group-hierarchy-network-settings.php';
    }
} );

/**
 * Admin notice when BuddyPress is not active.
 */
function bpgh_buddypress_missing_notice() {
    echo '<div class="notice notice-error"><p>';
    esc_html_e( 'BP Group Hierarchy requires BuddyPress to be installed and active.', 'bp-group-hierarchy' );
    echo '</p></div>';
}

/* -----------------------------------------------------------
 * 3. Shared Asset-Loading Check
 * ----------------------------------------------------------- */

/**
 * Determine whether hierarchy assets should be enqueued on the
 * current page. Loads on groups directory, single group pages,
 * activity pages, and member profile pages (v2.0).
 *
 * @return bool
 */
function bpgh_should_enqueue_assets() {

    if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'groups' ) ) {
        return false;
    }

    // Groups directory always gets assets.
    if ( bp_is_groups_directory() ) {
        return true;
    }

    // Single group page.
    if ( bp_is_group() ) {
        return true;
    }

    // Activity page — widgets and tooltips need assets here too.
    if ( function_exists( 'bp_is_activity_component' ) && bp_is_activity_component() ) {
        return true;
    }

    // Member profile pages — widgets need assets here too.
    if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
        return true;
    }

    return false;
}


/* -----------------------------------------------------------
 * Helper: BP 12+ directory URL with fallback
 * ----------------------------------------------------------- */

/**
 * Get the groups directory URL, compatible with BP 10–14+.
 *
 * BP 12.0.0 deprecated bp_get_groups_directory_permalink() in favour
 * of bp_get_groups_directory_url(). This wrapper tries the new
 * function first, then falls back to the old one, then home_url().
 *
 * @since 2.0.1
 * @return string
 */
function bpgh_get_directory_url() {
    if ( function_exists( 'bp_get_groups_directory_url' ) ) {
        return bp_get_groups_directory_url();
    }

    if ( function_exists( 'bp_get_groups_directory_permalink' ) ) {
        return bp_get_groups_directory_permalink();
    }

    return home_url( '/groups/' );
}

/* -----------------------------------------------------------
 * 4. Conditional CSS Loader
 * ----------------------------------------------------------- */

function bpgh_enqueue_conditional_styles() {

    if ( ! bpgh_should_enqueue_assets() ) {
        return;
    }

    wp_enqueue_style(
        'bpgh-hierarchy',
        BP_GROUP_HIERARCHY_PLUGIN_URL . 'assets/css/bpgh-hierarchy.css',
        array(),
        BPGH_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'bpgh_enqueue_conditional_styles' );

/* -----------------------------------------------------------
 * 5. Widget Registration
 *
 * Registers all BPGH widgets. v2.0 adds:
 *   - Parent Groups Widget
 *   - Child Groups Widget
 *   - Tag Search Widget
 *   - Tag Cloud Widget
 *   - Category Filter Widget
 * ----------------------------------------------------------- */

function bpgh_register_widgets() {

    if ( ! function_exists( 'buddypress' ) ) {
        return;
    }

    $dir = BP_GROUP_HIERARCHY_PLUGIN_DIR;

    // v1.x Widgets.
    $widgets = array(
        'class-bpgh-hierarchy-widget.php'       => 'BPGH_Hierarchy_Widget',
    );

    // Multisite widget.
    if ( is_multisite() ) {
        $widgets['class-bpgh-multisite-widget.php'] = 'BPGH_Multisite_Widget';
    }

    // v2.0 Widgets.
    $widgets['class-bpgh-parent-groups-widget.php']   = 'BPGH_Parent_Groups_Widget';
    $widgets['class-bpgh-child-groups-widget.php']    = 'BPGH_Child_Groups_Widget';
    $widgets['class-bpgh-tag-search-widget.php']      = 'BPGH_Tag_Search_Widget';
    $widgets['class-bpgh-tag-cloud-widget.php']       = 'BPGH_Tag_Cloud_Widget';
    $widgets['class-bpgh-category-filter-widget.php'] = 'BPGH_Category_Filter_Widget';

    foreach ( $widgets as $file => $class ) {
        $path = $dir . $file;
        if ( file_exists( $path ) ) {
            require_once $path;
            if ( class_exists( $class ) ) {
                register_widget( $class );
            }
        }
    }
}
add_action( 'widgets_init', 'bpgh_register_widgets' );

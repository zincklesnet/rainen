<?php
/**
 * Plugin Name:       BP Group Hierarchy
 * Plugin URI:        https://github.com/zincklesnet/orig-bp-group-hierarchy
 * Description:       Adds parent-child hierarchy, categories, tags, premium tiers, tooltips, shortcodes, and enhanced group management to BuddyPress groups.
 * Version:           2.0.5
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            Zabrina
 * Author URI:        https://zinckles.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bp-group-hierarchy
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin version — single source of truth for cache-busting and compat checks.
 */
define( 'BPGH_VERSION', '2.0.5' );

/* -----------------------------------------------------------
 * Activation (placeholder for future schema work)
 * ----------------------------------------------------------- */
function bpgh_install() {
    // Reserved for future activation tasks (e.g. creating DB tables).
}
register_activation_hook( __FILE__, 'bpgh_install' );

/* -----------------------------------------------------------
 * Load the plugin
 * ----------------------------------------------------------- */
require_once plugin_dir_path( __FILE__ ) . 'bp-group-hierarchy-loader.php';

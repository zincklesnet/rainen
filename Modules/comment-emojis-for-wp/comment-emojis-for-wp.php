<?php
/*
Plugin Name:        Comment Emojis for WP
Plugin URI:         https://profiles.wordpress.org/jayeshchopda/
Description:        Add a lightweight emoji picker to the default WordPress comment textarea so visitors can insert emojis into comments.
Version:            1.1.3
Requires at least:  5.0
Tested up to:       6.9.4
Requires PHP:       7.4
Author:             Jayeshkumar Chopda
Author URI:         https://profiles.wordpress.org/jayeshchopda/
License:            GPLv2 or later
License URI:        https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:        comment-emojis-for-wp
Domain Path:        /languages
*/



if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'CEFWJC_PLUGIN_BASE' ) ) {
	define( 'CEFWJC_PLUGIN_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'CEFWJC_PLUGIN_VERSION' ) ) {
	define( 'CEFWJC_PLUGIN_VERSION', '1.1.3' );
}

if ( ! defined( 'CEFWJC_PLUGIN_FILE' ) ) {
	define( 'CEFWJC_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'CEFWJC_PLUGIN_PATH' ) ) {
	define( 'CEFWJC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

require_once CEFWJC_PLUGIN_PATH . 'includes/class-cefwjc-plugin.php';

register_activation_hook( __FILE__, array( 'CEFWJC_Plugin', 'activate' ) );


/**
 * Bootstrap the plugin instance.
 *
 * @return CEFWJC_Plugin
 */
function cefwjc() {
	static $plugin = null;

	if ( null === $plugin ) {
		$plugin = new CEFWJC_Plugin();
	}

	return $plugin;
}

cefwjc();

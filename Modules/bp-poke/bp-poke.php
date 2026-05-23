<?php
/**
 * Plugin Name: (BuddyDev) BuddyPress Poke
 * Plugin URI: https://buddydev.com/plugins/bp-poke/
 * Version: 1.1.2
 * Author: Anu Sharma
 * Author URI: https://buddydev.com/
 * Description: Allow Users to poke each other on a BuddyPress site
 * Text Domain: bp-poke
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// define constants
// bp poke plugin dir url.
define( 'BP_POKE_DIR_URL', plugin_dir_url( __FILE__ ) );
// plugin dir path with trailing slash.
define( 'BP_POKE_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Main class
 */
class BP_Poke_Helper {
	/**
	 * Singleton instance
	 *
	 * @var $this
	 */
	private static $instance;

	/**
	 * Constructor
	 */
	private function __construct() {

		add_action( 'bp_loaded', array( $this, 'load_files' ), 0 );
		// add_action( 'bp_enqueue_scripts', array( $this, 'load_js' ) );
		// load translation files.
		add_action( 'bp_init', array( $this, 'load_textdomain' ), 2 );
	}

	/**
	 * Get the singleton instance
	 *
	 * @return BP_Poke_Helper
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Load plugin translation
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'bp-poke', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Load core files
	 */
	public function load_files() {

		$files = array(
			'bp-poke-functions.php',
			'bp-poke-component.php',
			'bp-poke-actions.php',
			'bp-poke-screens.php',
		);

		foreach ( $files as $file ) {
			require_once BP_POKE_DIR_PATH . $file;
		}
	}

}

BP_Poke_Helper::get_instance();

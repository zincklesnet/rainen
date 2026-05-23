<?php

if(!defined('ABSPATH')) {
	exit;
}

// @package bp-post-status


if ( ! class_exists( 'BPPS_Notifications' ) ) :


// BuddyPress Group Admin for BP Post Status

// Todo: Add Notification settings page (1) enable disable friends post notifications
// Todo: (2) per friend notification setting.
// Todo: (3) per group notification setting.

class BPPS_Notifications {

	/**

	 * Plugin's main instance

	 *

	 * @var object

	 */

	protected static $instance;



	
	function __construct() {
		


	}
	

	/**

	 * Return an instance of this class.

	 *

	 * @since 1.0.0

	 *

	 * @return object A single instance of this class.

	 */

	public static function start() {



		// If the single instance hasn't been set, set it now.

		if ( null == self::$instance ) {

			self::$instance = new self;

		}



		return self::$instance;

	}



}
endif;

/**

 * Boot the plugin.

 *

 * @since 1.0.0

 */

function bpps_notifications() {

	return BPPS_Notifications::start();

}

add_action( 'plugins_loaded', 'bpps_notifications', 5 );	
<?php
/**
 * Main Zombify Admin Class
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Zombify_Admin' ) ) :

    /**
     * Zombify Admin class
     */
    final class Zombify_Admin
    {

        /**
         * Zombify Admin instance
         *
         * @var Zombify_Admin
         */
        private static $instance;

        /**
         * Return the only existing instance of object
         *
         * @return Zombify_Admin
         */
        public static function get_instance()
        {
            if ( ! isset( self::$instance )) {
                self::$instance = new Zombify_Admin();
            }

            return self::$instance;
        }

        /**
         * Prevent object cloning
         */
        private function __clone() {}

        /**
         * Private constructor to prevent creating a new instance
         * via the 'new' operator from outside of this class.
         */
        private function __construct() {

            $this->setup_hooks();

        }

        /**
         * Setup the default actions and filters
         */
        public function setup_hooks()
        {
            do_action( 'zombify_admin_settings' );
        }

    }

    /**
     * The main function responsible for returning the Zombify_Admin instance.
     *
     * @return Zombify_Admin
     */
    function zombify_admin() {
        return Zombify_Admin::get_instance();
    }

    zombify_admin();

endif;
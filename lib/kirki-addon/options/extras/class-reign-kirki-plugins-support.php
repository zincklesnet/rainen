<?php
/**
 * Reign Kirki Plugins Support
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Plugins_Support' ) ) :

	/**
	 * @class Reign_Kirki_Plugins_Support
	 */
	class Reign_Kirki_Plugins_Support {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Plugins_Support
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Plugins_Support Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Plugins_Support is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Plugins_Support - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Plugins_Support Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'add_panels_and_sections' ) );
		}

		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {

			new \Kirki\Panel(
				'reign_plugin_support_panel',
				array(
					'priority'    => 200,
					'title'       => esc_html__( 'Plugins Support', 'reign' ),
					'description' => '',
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Plugins_Support.
 *
 * @return Reign_Kirki_Plugins_Support
 */
Reign_Kirki_Plugins_Support::instance();

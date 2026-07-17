<?php
/**
 * Reign Plugins Support Panel registration.
 *
 * Ported from lib/kirki-addon/options/extras/class-reign-kirki-plugins-support.php
 * during the Kirki removal migration (Phase 1 atomic sweep). Registers the top-
 * level "Plugins Support" panel that EDD / PMPro / MediaPress / etc. plugin-compat
 * shims hang sections off of.
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Plugins_Support_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Plugins_Support_Fields
	 */
	class Reign_Customizer_Plugins_Support_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Plugins_Support_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Plugins_Support_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Plugins_Support_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Plugins_Support_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Plugins_Support_Fields Constructor.
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

			// Every section that hangs off this panel is plugin-gated (EDD /
			// Paid Memberships Pro / MediaPress) using the SAME guards their
			// compat files use to load:
			//   - EDD       : class_exists( 'Easy_Digital_Downloads' )
			//   - PMPro     : defined( 'PMPRO_VERSION' )
			//   - MediaPress: class_exists( 'MediaPress' )
			// With none of those active the panel would render as an empty,
			// section-less entry, so only register it when at least one of
			// the compat plugins is active. Sections stay individually gated.
			$has_compat_plugin = class_exists( 'Easy_Digital_Downloads' )
				|| defined( 'PMPRO_VERSION' )
				|| class_exists( 'MediaPress' );

			if ( ! $has_compat_plugin ) {
				return;
			}

			\Reign\Customizer_Framework\Panel::add(
				'reign_plugin_support_panel',
				array(
					'priority'    => 210,
					'title'       => esc_html__( 'Plugins Support', 'reign' ),
					'description' => esc_html__( 'Compatibility options for the active plugins Reign supports, such as Easy Digital Downloads and MediaPress.', 'reign' ),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Plugins_Support_Fields.
 *
 * @return Reign_Customizer_Plugins_Support_Fields
 */
Reign_Customizer_Plugins_Support_Fields::instance();

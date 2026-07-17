<?php
/**
 * Reign Customizer Dark Mode
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Dark_Mode_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Dark_Mode_Fields
	 */
	class Reign_Customizer_Dark_Mode_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Dark_Mode_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Dark_Mode_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Dark_Mode_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Dark_Mode_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Dark_Mode_Fields Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'add_panels_and_sections' ) );
			add_action( 'init', array( $this, 'add_fields' ) );
		}

		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {
			// "Custom Dark Colors" section removed in 8.0.0. Dark mode now follows
			// the chosen palette automatically (per-palette dark), so the legacy
			// dark toggles are redundant. The one useful extra - an optional
			// dark-mode logo - is surfaced inside "Light & Dark Mode" (see
			// add_fields). The legacy settings (reign_dark_mode_option /
			// reign_custom_dark_mode_option) keep working for already-saved sites
			// but are no longer shown as a separate, confusing panel.
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			// Optional dark-mode logo, now surfaced inside the single
			// "Light & Dark Mode" section. Shown only when the light/dark
			// switch is enabled for visitors (otherwise a dark logo can never
			// appear). The legacy reign_dark_mode_option / custom-dark toggles
			// are no longer registered as UI - dark mode follows the chosen
			// palette automatically - but their saved values still work.
			\Reign\Customizer_Framework\Field::add( 'image',
				array(
					'settings'        => 'reign_dark_mode_logo',
					'label'           => esc_html__( 'Dark Mode Logo', 'reign' ),
					'description'     => esc_html__( 'Optional logo shown when the site is in dark mode. Set your main logo first in Site Identity > Logo.', 'reign' ),
					'section'         => 'reign_color_mode_toggle_section',
					'priority'        => 30,
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'site_color_mode_toggle_show',
							'operator' => '===',
							'value'    => 'on',
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Dark_Mode_Fields.
 *
 * @return Reign_Customizer_Dark_Mode_Fields
 */
Reign_Customizer_Dark_Mode_Fields::instance();

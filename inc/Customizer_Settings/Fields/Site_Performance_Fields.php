<?php
/**
 * Reign Customizer Site Performance
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Site_Performance_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Site_Performance_Fields
	 */
	class Reign_Customizer_Site_Performance_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Site_Performance_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Site_Performance_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Site_Performance_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Site_Performance_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Site_Performance_Fields Constructor.
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
			add_filter( 'reign_bypass_conditional_assets', array( $this, 'check_smart_performance_mode' ) );
		}

		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {
			\Reign\Customizer_Framework\Section::add(
				'reign_smart_performance_section',
				array(
					'title'       => esc_html__( 'Smart Performance', 'reign' ),
					'priority'    => 21,
					'panel'       => 'reign_general_panel',
					'description' => esc_html__( 'Speed up your site by loading only the styles each page actually needs.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_smart_performance',
					'label'       => esc_html__( 'Smart Performance Mode', 'reign' ),
					'description' => esc_attr__( 'Automatically loads only needed styles for faster site speed.', 'reign' ),
					'section'     => 'reign_smart_performance_section',
					'default'     => 'on',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);
		}

		/**
		 * Check Smart Performance mode setting for conditional asset loading
		 *
		 * @param bool $bypass Current bypass value.
		 * @return bool Whether to bypass conditional loading.
		 */
		public function check_smart_performance_mode( $bypass ) {
			// Smart Performance switch. reign_is_truthy() handles every
			// storage shape (legacy 'on'/'off', sanitize_bool_int 1/0, raw
			// true/false) so the toggle works on fresh installs AND on
			// sites upgraded from 7.x without a migration pass.
			// Default 'on' since 8.0.0: conditional asset loading is the
			// shipped behaviour; a site that explicitly saved the toggle OFF
			// keeps the load-everything bypass.
			$smart_performance = get_theme_mod( 'reign_smart_performance', 'on' );

			if ( ! reign_is_truthy( $smart_performance ) ) {
				return true; // Bypass conditional loading - load all assets.
			}

			return $bypass; // Keep the original value when Smart Performance is ON.
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Site_Performance_Fields.
 *
 * @return Reign_Customizer_Site_Performance_Fields
 */
Reign_Customizer_Site_Performance_Fields::instance();

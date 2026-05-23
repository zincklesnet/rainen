<?php
/**
 * Reign Kirki Site Performance
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Site_Performance' ) ) :

	/**
	 * @class Reign_Kirki_Site_Performance
	 */
	class Reign_Kirki_Site_Performance {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Site_Performance
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Site_Performance Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Site_Performance is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Site_Performance - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Site_Performance Constructor.
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
			new \Kirki\Section(
				'reign_smart_performance_section',
				array(
					'title'       => esc_html__( 'Smart Performance', 'reign' ),
					'priority'    => 21,
					'panel'       => 'reign_general_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_smart_performance',
					'label'       => esc_html__( 'Smart Performance Mode', 'reign' ),
					'description' => esc_attr__( 'Automatically loads only needed styles for faster site speed.', 'reign' ),
					'section'     => 'reign_smart_performance_section',
					'default'     => '',
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
			// Get the Smart Performance option value
			$smart_performance = get_theme_mod( 'reign_smart_performance', '' );
			
			// If Smart Performance is OFF (empty or 'off'), bypass conditional loading (load all assets)
			// If Smart Performance is ON ('on'), don't bypass (use conditional loading)
			if ( empty( $smart_performance ) || 'off' === $smart_performance ) {
				return true; // Bypass conditional loading - load all assets
			}
			
			return $bypass; // Keep the original value when Smart Performance is ON
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Site_Performance.
 *
 * @return Reign_Kirki_Site_Performance
 */
Reign_Kirki_Site_Performance::instance();

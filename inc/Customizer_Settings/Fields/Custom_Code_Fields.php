<?php
/**
 * Reign Customizer Custom Code
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Custom_Code_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Custom_Code_Fields
	 */
	class Reign_Customizer_Custom_Code_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Custom_Code_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Custom_Code_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Custom_Code_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Custom_Code_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Custom_Code_Fields Constructor.
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

			\Reign\Customizer_Framework\Section::add(
				'reign_custom_code',
				array(
					'title'       => esc_html__( 'Custom Code', 'reign' ),
					'priority'    => 21,
					'panel'       => 'reign_general_panel',
					'description' => esc_html__( 'Add your own tracking code and header or footer JavaScript across the whole site.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			\Reign\Customizer_Framework\Field::add( 'code',
				array(
					'settings'    => 'reign_tracking_code',
					'label'       => esc_html__( 'Tracking Code', 'reign' ),
					'description' => esc_html__( 'Enter your tracking codes here. This code is added to the site header. For example: Google tracking code, Facebook Pixel code, etc.', 'reign' ),
					'section'     => 'reign_custom_code',
					'priority'    => 10,
					'default'     => '',
					'transport'   => 'postMessage',
					'choices'     => array(
						'language' => 'html',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_tracking_code_divider',
					'section'  => 'reign_custom_code',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'code',
				array(
					'settings'    => 'reign_custom_js_header',
					'label'       => esc_html__( 'Custom JS : Header', 'reign' ),
					'description' => esc_html__( 'Just want to do some quick JS changes? Enter them here, they will be applied to your theme.', 'reign' ),
					'section'     => 'reign_custom_code',
					'priority'    => 10,
					'default'     => '',
					'transport'   => 'postMessage',
					'choices'     => array(
						'language' => 'js',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_custom_js_header_divider',
					'section'  => 'reign_custom_code',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'code',
				array(
					'settings'    => 'reign_custom_js_footer',
					'label'       => esc_html__( 'Custom JS : Footer', 'reign' ),
					'description' => esc_html__( 'Just want to do some quick JS changes? Enter them here, they will be applied to your theme.', 'reign' ),
					'section'     => 'reign_custom_code',
					'priority'    => 10,
					'default'     => '',
					'transport'   => 'postMessage',
					'choices'     => array(
						'language' => 'js',
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Custom_Code_Fields.
 *
 * @return Reign_Customizer_Custom_Code_Fields
 */
Reign_Customizer_Custom_Code_Fields::instance();

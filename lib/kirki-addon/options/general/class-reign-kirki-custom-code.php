<?php
/**
 * Reign Kirki Custom Code
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Custom_Code' ) ) :

	/**
	 * @class Reign_Kirki_Custom_Code
	 */
	class Reign_Kirki_Custom_Code {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Custom_Code
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Custom_Code Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Custom_Code is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Custom_Code - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Custom_Code Constructor.
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

			new \Kirki\Section(
				'reign_custom_code',
				array(
					'title'       => esc_html__( 'Custom Code', 'reign' ),
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

			new \Kirki\Field\Code(
				array(
					'settings'    => 'reign_tracking_code',
					'label'       => esc_html__( 'Tracking Code', 'reign' ),
					'description' => esc_html__( 'You can enter your tracking codes here. This code will be enqueued in site header. For example : Google Tacking Code, Facebook Pixel Code, etc.', 'reign' ),
					'section'     => 'reign_custom_code',
					'priority'    => 10,
					'default'     => '',
					'transport'   => 'postMessage',
					'choices'     => array(
						'language' => 'html',
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_tracking_code_divider',
					'section'  => 'reign_custom_code',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Code(
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

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_custom_js_header_divider',
					'section'  => 'reign_custom_code',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Code(
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
 * Main instance of Reign_Kirki_Custom_Code.
 *
 * @return Reign_Kirki_Custom_Code
 */
Reign_Kirki_Custom_Code::instance();

<?php
/**
 * Reign Kirki Page Mapping
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Page_Mapping' ) ) :

	/**
	 * @class Reign_Kirki_Page_Mapping
	 */
	class Reign_Kirki_Page_Mapping {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Page_Mapping
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Page_Mapping Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Page_Mapping is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Page_Mapping - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Page_Mapping Constructor.
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
				'reign_page_mapping',
				array(
					'title'       => esc_html__( 'Page Mapping', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_general_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			new \Kirki\Field\Dropdown_Pages(
				array(
					'settings'    => 'reign_login_page',
					'label'       => esc_html__( 'Login Page', 'reign' ),
					'description' => esc_html__( 'You can redirect user to custom login page.', 'reign' ),
					'section'     => 'reign_page_mapping',
					'priority'    => 10,
					'default'     => 0,
					'transport'   => 'postMessage',
					'placeholder' => '--- Select a Page ---',
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_login_page_divider',
					'section'  => 'reign_page_mapping',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Dropdown_Pages(
				array(
					'settings'    => 'reign_registration_page',
					'label'       => esc_html__( 'Registration Page', 'reign' ),
					'description' => esc_html__( 'You can redirect user to custom registration page.', 'reign' ),
					'section'     => 'reign_page_mapping',
					'priority'    => 10,
					'default'     => 0,
					'transport'   => 'postMessage',
					'placeholder' => '--- Select a Page ---',
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_registration_page_divider',
					'section'  => 'reign_page_mapping',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Dropdown_Pages(
				array(
					'settings'    => 'reign_404_page',
					'label'       => esc_html__( '404', 'reign' ),
					'description' => esc_html__( 'You can redirect user to custom 404 page.', 'reign' ),
					'section'     => 'reign_page_mapping',
					'priority'    => 10,
					'default'     => 0,
					'transport'   => 'postMessage',
					'placeholder' => '--- Select a Page ---',
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Page_Mapping.
 *
 * @return Reign_Kirki_Page_Mapping
 */
Reign_Kirki_Page_Mapping::instance();

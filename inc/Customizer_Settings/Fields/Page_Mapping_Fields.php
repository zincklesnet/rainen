<?php
/**
 * Reign Customizer Page Mapping
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Page_Mapping_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Page_Mapping_Fields
	 */
	class Reign_Customizer_Page_Mapping_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Page_Mapping_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Page_Mapping_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Page_Mapping_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Page_Mapping_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Page_Mapping_Fields Constructor.
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
				'reign_page_mapping',
				array(
					'title'       => esc_html__( 'Page Mapping', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_general_panel',
					'description' => esc_html__( 'Choose the pages the theme should use for login, registration and the 404 page.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			\Reign\Customizer_Framework\Field::add( 'dropdown-pages',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_login_page_divider',
					'section'  => 'reign_page_mapping',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'dropdown-pages',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_registration_page_divider',
					'section'  => 'reign_page_mapping',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'dropdown-pages',
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
 * Main instance of Reign_Customizer_Page_Mapping_Fields.
 *
 * @return Reign_Customizer_Page_Mapping_Fields
 */
Reign_Customizer_Page_Mapping_Fields::instance();

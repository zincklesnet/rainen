<?php
/**
 * Reign Customizer Site Logo
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Site_Logo_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Site_Logo_Fields
	 */
	class Reign_Customizer_Site_Logo_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Site_Logo_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Site_Logo_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Site_Logo_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Site_Logo_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Site_Logo_Fields Constructor.
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
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			\Reign\Customizer_Framework\Field::add( 'slider',
				array(
					'settings'    => 'reign_site_logo_size',
					'label'       => esc_html__( 'Logo Size', 'reign' ),
					'description' => esc_attr__( 'Change the logo size as it appears on your site (Desktop View).', 'reign' ),
					'section'     => 'title_tagline',
					'priority'    => 8,
					'choices'     => array(
						'min'  => 150,
						'max'  => 300,
						'step' => 1,
					),
					'output'      => array(
						array(
							'element'       => '.site-branding a img',
							'property'      => 'max-width',
							'units'         => 'px',
							'media_query'   => '@media screen and (min-width: 961px)',
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Site_Logo_Fields.
 *
 * @return Reign_Customizer_Site_Logo_Fields
 */
Reign_Customizer_Site_Logo_Fields::instance();

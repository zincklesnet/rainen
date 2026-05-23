<?php
/**
 * Reign Kirki Sub Header
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Site_Logo' ) ) :

	/**
	 * @class Reign_Kirki_Site_Logo
	 */
	class Reign_Kirki_Site_Logo {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Site_Logo
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Site_Logo Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Site_Logo is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Site_Logo - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Site_Logo Constructor.
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

			new \Kirki\Field\Slider(
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
 * Main instance of Reign_Kirki_Site_Logo.
 *
 * @return Reign_Kirki_Site_Logo
 */
Reign_Kirki_Site_Logo::instance();

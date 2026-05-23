<?php
/**
 * Reign Kirki Sub Header
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Sub_Header' ) ) :

	/**
	 * @class Reign_Kirki_Sub_Header
	 */
	class Reign_Kirki_Sub_Header {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Sub_Header
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Sub_Header Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Sub_Header is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Sub_Header - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Sub_Header Constructor.
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
				'reign_sub_header_options',
				array(
					'title'    => esc_html__( 'Sub Header', 'reign' ),
					'priority' => 22,
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			new \Kirki\Field\Number(
				array(
					'settings'    => 'reign_site_header_sub_header_height',
					'label'       => esc_html__( 'Sub Header Height (px)', 'reign' ),
					'description' => '',
					'section'     => 'reign_sub_header_options',
					'default'     => '286',
					'priority'    => 10,
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_site_header_sub_header_height_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'    => 'reign_site_header_image_bg_color',
					'label'       => esc_html__( 'Sub Header Image Background Color', 'reign' ),
					'description' => '',
					'section'     => 'reign_sub_header_options',
					'default'     => '#cccccc',
					'priority'    => 10,
					'choices'     => array( 'alpha' => true ),
					'output'      => array(
						array(
							'element'  => '.lm-site-header-section .lm-header-banner',
							'property' => 'background-color',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_site_header_image_bg_color_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'    => 'reign_site_header_image_overlay_color',
					'label'       => esc_html__( 'Sub Header Image Overlay Color', 'reign' ),
					'description' => '',
					'section'     => 'reign_sub_header_options',
					'default'     => 'rgba(38,38,38,0.6)',
					'priority'    => 10,
					'choices'     => array( 'alpha' => true ),
					'output'      => array(
						array(
							'function' => 'css',
							'element'  => '.lm-header-banner:after',
							'property' => 'background',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_site_header_image_overlay_color_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'    => 'reign_site_header_image_text_color',
					'label'       => esc_html__( 'Sub Header Text Color', 'reign' ),
					'description' => '',
					'section'     => 'reign_sub_header_options',
					'default'     => '#ffffff',
					'priority'    => 10,
					'choices'     => array( 'alpha' => true ),
					'output'      => array(
						array(
							'element'  => '.lm-site-header-section .lm-header-banner h1.lm-header-title, .lm-breadcrumbs-wrapper #breadcrumbs li i, .lm-breadcrumbs-wrapper #breadcrumbs span, .lm-breadcrumbs-wrapper #breadcrumbs li strong, .lm-site-header-section .lm-header-banner',
							'property' => 'color',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_site_header_image_text_color_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'    => 'reign_site_header_image_link_color',
					'label'       => esc_html__( 'Sub Header Link Color', 'reign' ),
					'description' => '',
					'section'     => 'reign_sub_header_options',
					'default'     => '#ffffff',
					'priority'    => 10,
					'choices'     => array( 'alpha' => true ),
					'output'      => array(
						array(
							'element'  => '.lm-breadcrumbs-wrapper #breadcrumbs li a, .lm-breadcrumbs-wrapper #breadcrumbs span a',
							'property' => 'color',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_site_header_image_link_color_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_site_enable_breadcrumb',
					'label'       => esc_html__( 'Enable Breadcrumb', 'reign' ),
					'description' => '',
					'section'     => 'reign_sub_header_options',
					'default'     => 1,
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_site_enable_breadcrumb_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_cpt_default_sub_header_switch',
					'label'       => esc_html__( 'Hide Sub Header', 'reign' ),
					'description' => esc_html__( 'hide sub header globally.', 'reign' ),
					'section'     => 'reign_sub_header_options',
					'default'     => 0,
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Sub_Header.
 *
 * @return Reign_Kirki_Sub_Header
 */
Reign_Kirki_Sub_Header::instance();

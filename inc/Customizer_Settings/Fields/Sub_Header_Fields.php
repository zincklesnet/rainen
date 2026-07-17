<?php
/**
 * Reign Customizer Sub Header
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Sub_Header_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Sub_Header_Fields
	 */
	class Reign_Customizer_Sub_Header_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Sub_Header_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Sub_Header_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Sub_Header_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Sub_Header_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Sub_Header_Fields Constructor.
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
				'reign_sub_header_options',
				array(
					'title'       => esc_html__( 'Sub Header', 'reign' ),
					'priority'    => 22,
					'description' => esc_html__( 'Style the page title and breadcrumb banner shown below the header. This applies site-wide; hide it for a specific content type under Content Layouts.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			\Reign\Customizer_Framework\Field::add( 'number',
				array(
					'settings'    => 'reign_site_header_sub_header_height',
					'label'       => esc_html__( 'Sub Header Height (px)', 'reign' ),
					'description' => esc_html__( 'Height of the title banner area. Default is 286px.', 'reign' ),
					'section'     => 'reign_sub_header_options',
					'default'     => '286',
					'priority'    => 10,
					'choices'     => array(
						'min'  => 80,
						'max'  => 600,
						'step' => 2,
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_site_header_sub_header_height_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'    => 'reign_site_header_image_bg_color',
					'label'       => esc_html__( 'Sub Header Image Background Color', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_site_header_image_bg_color_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'    => 'reign_site_header_image_overlay_color',
					'label'       => esc_html__( 'Sub Header Image Overlay Color', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_site_header_image_overlay_color_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'    => 'reign_site_header_image_text_color',
					'label'       => esc_html__( 'Sub Header Text Color', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_site_header_image_text_color_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'    => 'reign_site_header_image_link_color',
					'label'       => esc_html__( 'Sub Header Link Color', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_site_header_image_link_color_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_site_enable_breadcrumb_divider',
					'section'  => 'reign_sub_header_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_cpt_default_sub_header_switch',
					'label'       => esc_html__( 'Hide Sub Header', 'reign' ),
					'description' => esc_html__( 'Hide the sub header on the whole site. This overrides the per-page Sub Header settings.', 'reign' ),
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
 * Main instance of Reign_Customizer_Sub_Header_Fields.
 *
 * @return Reign_Customizer_Sub_Header_Fields
 */
Reign_Customizer_Sub_Header_Fields::instance();

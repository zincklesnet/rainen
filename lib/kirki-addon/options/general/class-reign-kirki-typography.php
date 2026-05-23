<?php
/**
 * Reign Kirki Typography
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Typography' ) ) :

	/**
	 * @class Reign_Kirki_Typography
	 */
	class Reign_Kirki_Typography {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Typography
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Typography Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Typography is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Typography - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Typography Constructor.
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
		 * Dynamically get body font family output based on active plugins.
		 *
		 * @return array
		 */
		protected function get_body_typography_output(): array {
			$selectors = array(
				'body',
			);

			// PeepSo.
			if ( class_exists( 'PeepSo' ) ) {
				$selectors[] = '.peepso *:not(.fa, .fab, .fad, .fal, .far, .fas)';
				$selectors[] = '.ps-lightbox *:not(.fa, .fab, .fad, .fal, .far, .fas)';
				$selectors[] = '.ps-dialog *:not(.fa, .fab, .fad, .fal, .far, .fas)';
				$selectors[] = '.ps-hovercard *:not(.fa, .fab, .fad, .fal, .far, .fas)';
			}

			return array(
				// Apply all typography properties to the body.
				array(
					'element' => 'body',
				),
				// Apply only font-family to other UI elements.
				array(
					'choice'   => 'font-family',
					'element'  => implode( ', ', $selectors ),
					'property' => 'font-family',
				),
			);
		}


		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {

			new \Kirki\Panel(
				'reign_general_panel',
				array(
					'priority'    => 21,
					'title'       => esc_html__( 'General', 'reign' ),
					'description' => esc_html__( 'Kirki integration for SitePoint demo', 'reign' ),
				)
			);

			new \Kirki\Section(
				'reign_typography',
				array(
					'title'       => esc_html__( 'Typography', 'reign' ),
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

			$default_value_set = reign_get_customizer_default_value_set();

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_custom_typography',
					'label'       => esc_html__( 'Custom Typography', 'reign' ),
					'description' => esc_html__( 'Enable or disable custom typography.', 'reign' ),
					'section'     => 'reign_typography',
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
					'settings'        => 'reign_custom_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_body_typography',
					'label'           => esc_html__( 'Body Font', 'reign' ),
					'description'     => esc_html__( 'Set font properties of body tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_body_typography'],
					'priority'        => 10,
					'output'          => $this->get_body_typography_output(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_body_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_title_tagline_typography',
					'label'           => esc_html__( 'Site Title', 'reign' ),
					'description'     => esc_html__( 'Set font properties of site title.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_title_tagline_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '.site-branding .site-title a',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_title_tagline_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'site_tagline_typography_option',
					'label'           => esc_html__( 'Site Tagline', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['site_tagline_typography_option'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '.site-description, body #masthead p.site-description',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_tagline_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_header_main_menu_font',
					'label'           => esc_html__( 'Header Main Menu', 'reign' ),
					'description'     => esc_html__( 'Set font properties for menu.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_header_main_menu_font'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '#masthead.site-header .main-navigation .primary-menu > li a, #masthead .user-link-wrap .user-link, #masthead .psw-userbar__name>a',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_main_menu_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_header_sub_menu_font',
					'label'           => esc_html__( 'Header Sub Menu', 'reign' ),
					'description'     => esc_html__( 'Set font properties for sub menu.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_header_sub_menu_font'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '#masthead.site-header .main-navigation .primary-menu > li .sub-menu li a, #masthead .user-profile-menu li > a',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sub_menu_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_h1_typography',
					'label'           => esc_html__( 'Heading 1', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H1 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h1_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h1',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_h1_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_h2_typography',
					'label'           => esc_html__( 'Heading 2', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H2 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h2_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h2',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_h2_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_h3_typography',
					'label'           => esc_html__( 'Heading 3', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H3 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h3_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h3, .buddypress-wrap .item-body .group-separator-block .screen-heading',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_h3_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_h4_typography',
					'label'           => esc_html__( 'Heading 4', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H4 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h4_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h4',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_h4_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_h5_typography',
					'label'           => esc_html__( 'Heading 5', 'reign' ),
					'description'     => esc_html__( 'Allows you to select all font properties of H5 tag for your site.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h5_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h5',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_h5_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_h6_typography',
					'label'           => esc_html__( 'Heading 6', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H6 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h6_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h6',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_h6_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_quote_typography',
					'label'           => esc_html__( 'Blockquote Typography', 'reign' ),
					'description'     => esc_html__( 'Set font properties of blockquote.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_quote_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'blockquote, .wp-block-quote, .wp-block-quote p',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Typography.
 *
 * @return Reign_Kirki_Typography
 */
Reign_Kirki_Typography::instance();

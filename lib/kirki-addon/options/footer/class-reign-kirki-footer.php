<?php
/**
 * Reign Kirki Footer
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Footer' ) ) :

	/**
	 * @class Reign_Kirki_Footer
	 */
	class Reign_Kirki_Footer {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Footer
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Footer Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Footer is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Footer - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Footer Constructor.
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

			new \Kirki\Panel(
				'reign_footer_panel',
				array(
					'priority'    => 200,
					'title'       => esc_html__( 'Footer', 'reign' ),
					'description' => '',
				)
			);

			new \Kirki\Section(
				'reign_footer_settings',
				array(
					'title'       => esc_html__( 'Settings', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_footer_panel',
					'description' => '',
				)
			);

			new \Kirki\Section(
				'reign_footer_typography',
				array(
					'title'       => esc_html__( 'Footer', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_footer_panel',
					'description' => '',
				)
			);

			new \Kirki\Section(
				'reign_footer_copyright',
				array(
					'title'       => esc_html__( 'Copyright', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_footer_panel',
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
					'settings'    => 'reign_footer_copyright_enable',
					'label'       => esc_html__( 'Enable Footer Copyright', 'reign' ),
					'description' => '',
					'section'     => 'reign_footer_copyright',
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
					'settings' => 'reign_footer_copyright_enable_divider',
					'section'  => 'reign_footer_copyright',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Textarea(
				array(
					'settings'        => 'reign_footer_copyright_text',
					'label'           => esc_html__( 'Copyright Text', 'reign' ),
					'description'     => esc_html__( 'Enter the text that displays in the copyright bar. HTML markup can be used.', 'reign' ),
					'section'         => 'reign_footer_copyright',
					'default'         => esc_html__( '&copy; [current_year] - [site_title] | Theme by [theme_author]', 'reign' ),
					'priority'        => 10,
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_copyright_enable',
							'operator' => '!=',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Select(
				array(
					'settings'        => 'reign_footer_copyright_alignment',
					'label'           => esc_html__( 'Alignment', 'reign' ),
					'description'     => '',
					'section'         => 'reign_footer_copyright',
					'default'         => 'center',
					'priority'        => 10,
					'choices'         => array(
						'left'   => esc_html__( 'Left', 'reign' ),
						'right'  => esc_html__( 'Right', 'reign' ),
						'center' => esc_html__( 'Center', 'reign' ),
					),
					'output'          => array(
						array(
							'function' => 'css',
							'element'  => 'footer div#reign-copyright-text .container',
							'property' => 'text-align',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_copyright_enable',
							'operator' => '!=',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_footer_copyright_alignment_divider',
					'section'         => 'reign_footer_copyright',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_copyright_enable',
							'operator' => '!=',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings' => 'reign_footer_copyright_border',
					'label'    => esc_html__( 'Copyright Border Color', 'reign' ),
					'section'  => 'reign_footer_copyright',
					'default'  => '',
					'choices'  => array( 'alpha' => true ),
					'output'   => array(
						array(
							'element'  => 'footer div#reign-copyright-text',
							'property' => 'border-color',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_footer_copyright_border_divider',
					'section'         => 'reign_footer_copyright',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_copyright_enable',
							'operator' => '!=',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Padding(
				array(
					'settings'        => 'reign_footer_copyright_spacing',
					'label'           => esc_html__( 'Padding (px)', 'reign' ),
					'description'     => '',
					'section'         => 'reign_footer_copyright',
					'default'         => array(
						'top'    => '20px',
						'right'  => '0px',
						'bottom' => '20px',
						'left'   => '0px',
					),
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'footer div#reign-copyright-text',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_copyright_enable',
							'operator' => '!=',
							'value'    => false,
						),
					),
				)
			);

			/**
			 *  Footer Typography
			 */
			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings' => 'reign_footer_customize_typography',
					'label'    => esc_html__( 'Customize Typography ?', 'reign' ),
					'section'  => 'reign_footer_typography',
					'default'  => 'off',
					'choices'  => array(
						'on'  => esc_html__( 'Yes', 'reign' ),
						'off' => esc_html__( 'No', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_footer_customize_typography_divider',
					'section'  => 'reign_footer_typography',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_footer_widget_title',
					'label'           => esc_html__( 'Widget Title Typography', 'reign' ),
					'section'         => 'reign_footer_typography',
					'default'         => array(
						'font-family'    => '',
						'variant'        => '',
						'font-size'      => '20px',
						'line-height'    => '',
						'letter-spacing' => '',
						'text-transform' => 'none',
					),
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '#footer-area .widget-title span',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_customize_typography',
							'operator' => '==',
							'value'    => '1',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_footer_widget_title_divider',
					'section'         => 'reign_footer_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_customize_typography',
							'operator' => '==',
							'value'    => '1',
						),
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'        => 'reign_footer_content_typo',
					'label'           => esc_html__( 'Content Typography', 'reign' ),
					'section'         => 'reign_footer_typography',
					'default'         => array(
						'font-family'    => '',
						'variant'        => '',
						'font-size'      => '16px',
						'line-height'    => '',
						'letter-spacing' => '',
						'text-transform' => 'none',
					),
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '#footer-area, #footer-area p, #footer-area a, #footer-area .widget-area a:not(.button), footer div#reign-copyright-text',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_customize_typography',
							'operator' => '==',
							'value'    => '1',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_footer_content_typo_divider',
					'section'         => 'reign_footer_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_footer_customize_typography',
							'operator' => '==',
							'value'    => '1',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Padding(
				array(
					'settings'    => 'reign_footer_spacing',
					'label'       => esc_html__( 'Footer Padding', 'reign' ),
					'description' => esc_html__( 'Set footer area padding (Default value is 70px, 0px).', 'reign' ),
					'section'     => 'reign_footer_typography',
					'default'     => array(
						'top'    => '70px',
						'right'  => '0px',
						'bottom' => '70px',
						'left'   => '0px',
					),
					'priority'    => 10,
					'output'      => array(
						array(
							'element' => 'footer .footer-wrap',
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Footer.
 *
 * @return Reign_Kirki_Footer
 */
Reign_Kirki_Footer::instance();

<?php
/**
 * Reign Customizer Footer
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Footer_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Footer_Fields
	 */
	class Reign_Customizer_Footer_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Footer_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Footer_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Footer_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Footer_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Footer_Fields Constructor.
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

			\Reign\Customizer_Framework\Panel::add(
				'reign_footer_panel',
				array(
					'priority'    => 200,
					'title'       => esc_html__( 'Footer', 'reign' ),
					'description' => esc_html__( 'Footer widget area and copyright bar. Footer colors are set with your palette under Color Options > Individual Colors.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_footer_typography',
				array(
					'title'       => esc_html__( 'Footer Area', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_footer_panel',
					'description' => esc_html__( 'Typography and spacing for the footer widget area.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_footer_copyright',
				array(
					'title'       => esc_html__( 'Copyright', 'reign' ),
					'priority'    => 20,
					'panel'       => 'reign_footer_panel',
					'description' => esc_html__( 'The copyright bar at the very bottom of every page.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_get_customizer_default_value_set();

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_footer_copyright_enable',
					'label'       => esc_html__( 'Enable Footer Copyright', 'reign' ),
					'description' => esc_html__( 'Show the copyright bar at the bottom of every page.', 'reign' ),
					'section'     => 'reign_footer_copyright',
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
					'settings' => 'reign_footer_copyright_enable_divider',
					'section'  => 'reign_footer_copyright',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'textarea',
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

			\Reign\Customizer_Framework\Field::add( 'select',
				array(
					'settings'        => 'reign_footer_copyright_alignment',
					'label'           => esc_html__( 'Alignment', 'reign' ),
					'description'     => esc_html__( 'How the copyright text is aligned in its bar.', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			// Padding decomposed from Kirki Pro\Field\Padding into 4 single-edge
			// dimension fields. Migration filter (inc/Customizer_Settings/migrations.php
			// :: reign_migrate_v8_padding_decomposition) copies the legacy
			// `reign_footer_copyright_spacing` composite array → these 4
			// sibling theme_mods so existing customer values carry over.
			$edges = array(
				'top'    => array( 'label' => esc_html__( 'Padding Top (px)', 'reign' ),    'default' => '20px' ),
				'right'  => array( 'label' => esc_html__( 'Padding Right (px)', 'reign' ),  'default' => '0px' ),
				'bottom' => array( 'label' => esc_html__( 'Padding Bottom (px)', 'reign' ), 'default' => '20px' ),
				'left'   => array( 'label' => esc_html__( 'Padding Left (px)', 'reign' ),   'default' => '0px' ),
			);
			foreach ( $edges as $edge => $edge_meta ) {
				\Reign\Customizer_Framework\Field::add(
					'dimension',
					array(
						'settings'        => 'reign_footer_copyright_spacing_' . $edge,
						'label'           => $edge_meta['label'],
						'description'     => '',
						'section'         => 'reign_footer_copyright',
						'default'         => $edge_meta['default'],
						'priority'        => 10,
						'output'          => array(
							array(
								'element'  => 'footer div#reign-copyright-text',
								'property' => 'padding-' . $edge,
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
			}
			unset( $edges );

			/**
			 *  Footer Typography
			 */
			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_footer_customize_typography',
					'label'       => esc_html__( 'Customize Footer Typography', 'reign' ),
					'description' => esc_html__( 'Use custom fonts for the footer instead of the site defaults.', 'reign' ),
					'section'     => 'reign_footer_typography',
					'default'     => 'off',
					'choices'  => array(
						'on'  => esc_html__( 'Yes', 'reign' ),
						'off' => esc_html__( 'No', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_footer_customize_typography_divider',
					'section'  => 'reign_footer_typography',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
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
							'element' => '#footer-area .widget-title span, #footer-area .widget-title span a',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'typography',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			// Padding decomposed from Kirki Pro\Field\Padding (see migration
			// filter for legacy reign_footer_spacing composite handling).
			$edges = array(
				'top'    => array( 'label' => esc_html__( 'Footer Padding Top (px)', 'reign' ),    'default' => '70px' ),
				'right'  => array( 'label' => esc_html__( 'Footer Padding Right (px)', 'reign' ),  'default' => '0px' ),
				'bottom' => array( 'label' => esc_html__( 'Footer Padding Bottom (px)', 'reign' ), 'default' => '70px' ),
				'left'   => array( 'label' => esc_html__( 'Footer Padding Left (px)', 'reign' ),   'default' => '0px' ),
			);
			foreach ( $edges as $edge => $edge_meta ) {
				\Reign\Customizer_Framework\Field::add(
					'dimension',
					array(
						'settings'    => 'reign_footer_spacing_' . $edge,
						'label'       => $edge_meta['label'],
						'description' => 'top' === $edge ? esc_html__( 'Set footer area padding (Default value is 70px, 0px).', 'reign' ) : '',
						'section'     => 'reign_footer_typography',
						'default'     => $edge_meta['default'],
						'priority'    => 10,
						'output'      => array(
							array(
								'element'  => 'footer .footer-wrap',
								'property' => 'padding-' . $edge,
							),
						),
					)
				);
			}
			unset( $edges );
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Footer_Fields.
 *
 * @return Reign_Customizer_Footer_Fields
 */
Reign_Customizer_Footer_Fields::instance();

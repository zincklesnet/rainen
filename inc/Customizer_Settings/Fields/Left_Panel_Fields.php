<?php
/**
 * Reign Customizer Left Panel
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Left_Panel_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Left_Panel_Fields
	 */
	class Reign_Customizer_Left_Panel_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Left_Panel_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Left_Panel_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Left_Panel_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Left_Panel_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Left_Panel_Fields Constructor.
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
				'reign_left_section',
				array(
					'priority'    => 80,
					'title'       => esc_html__( 'Left Panel', 'reign' ),
					'panel'       => 'reign_header_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_group_behavior',
					'section'  => 'reign_left_section',
					'default'  => '<h4 class="reign-cz-group reign-cz-group--first">' . esc_html__( 'Behavior', 'reign' ) . '</h4>',
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_left_panel_gloabl_setting',
					'label'       => esc_html__( 'Left Panel - Global Setting', 'reign' ),
					'description' => esc_html__( 'Turn on the slide-in Left Panel. This setting works globally for all pages; the Left Panel can also be shown on a specific page by editing that particular page. To show its contents, assign a menu to the Left Panel location under Appearance > Menus (logged-out visitors use the Left Panel (Logged Out) location).', 'reign' ),
					'section'     => 'reign_left_section',
					'default'     => 'on',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_gloabl_setting_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'radio_buttonset',
				array(
					'settings' => 'reign_left_panel_position',
					'label'    => esc_html__( 'Left Panel Position', 'reign' ),
					'section'  => 'reign_left_section',
					'default'  => 'left',
					'priority' => 10,
					'choices'  => array(
						'left'  => esc_html__( 'Left', 'reign' ),
						'right' => esc_html__( 'Right', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_position_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_left_panel_toggle',
					'label'       => esc_html__( 'Left Panel - Toggle', 'reign' ),
					'description' => esc_html__( 'Show a button visitors can use to collapse and expand the left panel.', 'reign' ),
					'section'     => 'reign_left_section',
					'default'     => 'on',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_toggle_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'radio_buttonset',
				array(
					'settings' => 'reign_left_panel_state',
					'label'    => esc_html__( 'Left Panel - Default State', 'reign' ),
					'section'  => 'reign_left_section',
					'default'  => 'closed',
					'priority' => 10,
					'choices'  => array(
						'open'   => esc_html__( 'Open', 'reign' ),
						'closed' => esc_html__( 'Closed', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_state_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_left_panel_shift_body',
					'label'       => esc_html__( 'Left Panel - Shift Body', 'reign' ),
					'description' => esc_html__( 'When enabled, the main content will shift when the panel opens or closes.', 'reign' ),
					'section'     => 'reign_left_section',
					'default'     => 'off',
					'tooltip'     => esc_html__( 'Not compatible with the Boxed site layout.', 'reign' ),
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_shift_body_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			// --- Size & Spacing ----------------------------------------------------
			// These sliders drive CSS custom properties on <body>. The panel SCSS
			// reads them via var( --reign-lp-*, <default> ), so every coupled offset
			// (panel width, body shift, sticky header, search bar, position-right
			// mirror, Elementor) follows a single control. Defaults match the
			// historical hardcoded values so existing sites do not shift on update.

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_group_size',
					'section'  => 'reign_left_section',
					'default'  => '<h4 class="reign-cz-group">' . esc_html__( 'Size & Spacing', 'reign' ) . '</h4>',
				)
			);

			\Reign\Customizer_Framework\Field::add( 'slider',
				array(
					'settings'    => 'reign_left_panel_open_width',
					'label'       => esc_html__( 'Left Panel - Open Width', 'reign' ),
					'description' => esc_html__( 'Width of the Left Panel when expanded (Default value is 230px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => 230,
					'transport'   => 'refresh',
					'choices'     => array(
						'min'  => 180,
						'max'  => 360,
						'step' => 1,
					),
					'output'      => array(
						array(
							'element'  => 'body',
							'property' => '--reign-lp-open-width',
							'units'    => 'px',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_open_width_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'slider',
				array(
					'settings'    => 'reign_left_panel_collapsed_width',
					'label'       => esc_html__( 'Left Panel - Collapsed Width', 'reign' ),
					'description' => esc_html__( 'Width of the collapsed icon strip when the panel is closed (Default value is 80px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => 80,
					'transport'   => 'refresh',
					'choices'     => array(
						'min'  => 56,
						'max'  => 120,
						'step' => 1,
					),
					'output'      => array(
						array(
							'element'  => 'body',
							'property' => '--reign-lp-collapsed-width',
							'units'    => 'px',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_collapsed_width_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'slider',
				array(
					'settings'    => 'reign_left_panel_header_height',
					'label'       => esc_html__( 'Left Panel - Header Height', 'reign' ),
					'description' => esc_html__( 'Height of the Left Panel header area that holds the toggle (Default value is 80px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => 80,
					'transport'   => 'refresh',
					'choices'     => array(
						'min'  => 50,
						'max'  => 120,
						'step' => 1,
					),
					'output'      => array(
						array(
							'element'  => 'body',
							'property' => '--reign-lp-header-height',
							'units'    => 'px',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_header_height_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'slider',
				array(
					'settings'    => 'reign_left_panel_item_spacing',
					'label'       => esc_html__( 'Left Panel - Menu Item Spacing', 'reign' ),
					'description' => esc_html__( 'Vertical spacing between Left Panel menu items (Default value is 8px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => 8,
					'transport'   => 'refresh',
					'choices'     => array(
						'min'  => 0,
						'max'  => 24,
						'step' => 1,
					),
					'output'      => array(
						array(
							'element'  => 'body',
							'property' => '--reign-lp-item-gap',
							'units'    => 'px',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_item_spacing_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_group_icon',
					'section'  => 'reign_left_section',
					'default'  => '<h4 class="reign-cz-group">' . esc_html__( 'Icon', 'reign' ) . '</h4>',
				)
			);

			\Reign\Customizer_Framework\Field::add( 'slider',
				array(
					'settings'    => 'reign_left_panel_icon_typography',
					'label'       => esc_html__( 'Left Panel - Icon Font Size', 'reign' ),
					'description' => esc_html__( 'Set left panel icon font size (Default value is 18px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => 18,
					'transport'   => 'refresh',
					'choices'     => array(
						'min'  => 10,
						'max'  => 40,
						'step' => 1,
					),
					'output'      => array(
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a i, ul.navbar-reign-panel li.menu-item a img._mi',
							'property'    => 'font-size',
							'units'       => 'px',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_icon_typography_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'slider',
				array(
					'settings'    => 'reign_left_panel_icon_height_width',
					'label'       => esc_html__( 'Left Panel - Icon Height/Width', 'reign' ),
					'description' => esc_html__( 'Set left panel icon height width (Default value is 47px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => 47,
					'transport'   => 'refresh',
					'choices'     => array(
						'min'  => 30,
						'max'  => 50,
						'step' => 1,
					),
					'output'      => array(
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a i, ul.navbar-reign-panel li.menu-item a img._mi',
							'property'    => 'width',
							'units'       => 'px',
						),
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a i, ul.navbar-reign-panel li.menu-item a img._mi',
							'property'    => 'min-width',
							'units'       => 'px',
						),
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a i, ul.navbar-reign-panel li.menu-item a img._mi',
							'property'    => 'height',
							'units'       => 'px',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_icon_height_width_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'dimension',
				array(
					'settings'    => 'reign_left_panel_icon_border_radius',
					'label'       => esc_html__( 'Left Panel - Icon Border Radius', 'reign' ),
					'description' => esc_html__( 'Set left panel icon border radius (Default value is 6px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => '6px',
					'transport'   => 'refresh',
					'output'      => array(
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a',
							'property'    => 'border-radius',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_icon_border_radius_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_group_text',
					'section'  => 'reign_left_section',
					'default'  => '<h4 class="reign-cz-group">' . esc_html__( 'Menu Text', 'reign' ) . '</h4>',
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'    => 'reign_left_panel_menu_typography',
					'label'       => esc_html__( 'Left Panel - Menu Text Typography', 'reign' ),
					'description' => esc_html__( 'Typography for the Left Panel menu label text. The menu icon is sized separately above (Icon Font Size).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => array(
						'font-family'    => '',
						'variant'        => '',
						'font-style'     => '',
						'font-size'      => '16px',
						'letter-spacing' => '0px',
						'text-transform' => '',
					),
					'output'      => array(
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_menu_typography_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'slider',
				array(
					'settings'    => 'reign_left_panel_section_font_size',
					'label'       => esc_html__( 'Left Panel - Section Header Font Size', 'reign' ),
					'description' => esc_html__( 'Font size of section header labels in the Left Panel menu (Default value is 12px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => 12,
					'transport'   => 'refresh',
					'choices'     => array(
						'min'  => 10,
						'max'  => 20,
						'step' => 1,
					),
					'output'      => array(
						array(
							'element'  => 'body',
							'property' => '--reign-lp-section-font-size',
							'units'    => 'px',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_left_panel_colors_note',
					'section'  => 'reign_left_section',
					'default'  => '<p class="reign-cz-note">' . wp_kses(
						__( 'Left Panel <strong>colors</strong> (background, toggle, menu text, hover and active) are set per color scheme under <strong>Customize &rarr; Colors</strong> (Site Skin).', 'reign' ),
						array( 'strong' => array() )
					) . '</p>',
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Left_Panel_Fields.
 *
 * @return Reign_Customizer_Left_Panel_Fields
 */
Reign_Customizer_Left_Panel_Fields::instance();

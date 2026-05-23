<?php
/**
 * Reign Kirki Header Sticky Menu
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Header_Sticky_Menu' ) ) :

	/**
	 * @class Reign_Kirki_Header_Sticky_Menu
	 */
	class Reign_Kirki_Header_Sticky_Menu {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Header_Sticky_Menu
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Header_Sticky_Menu Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Header_Sticky_Menu is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Header_Sticky_Menu - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Header_Sticky_Menu Constructor.
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
				'reign_header_sticky_menu',
				array(
					'title'       => esc_html__( 'Sticky Menu', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_header_panel',
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
					'settings'        => 'reign_header_sticky_menu_enable',
					'label'           => esc_html__( 'Sticky On Scroll', 'reign' ),
					'description'     => esc_html__( 'Enable or Disable sticky header menu.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => 1,
					'priority'        => 10,
					'choices'         => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sticky_menu_enable_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'        => 'reign_header_sticky_menu_custom_style_enable',
					'label'           => esc_html__( 'Config Sticky Menu', 'reign' ),
					'description'     => esc_html__( 'Config sticky menu style.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => 0,
					'priority'        => 10,
					'choices'         => array(
						'on'  => esc_html__( 'Custom', 'reign' ),
						'off' => esc_html__( 'Default', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sticky_menu_custom_style_enable_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_header_sticky_menu_bg_color',
					'label'           => esc_html__( 'Header Background Color', 'reign' ),
					'description'     => esc_html__( 'The background color of the sticky header.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_bg_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling',
							'property' => 'background-color',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sticky_menu_bg_color_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_sticky_header_logo_color',
					'label'           => esc_html__( 'Site Title', 'reign' ),
					'description'     => esc_html__( 'The color of site title.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_sticky_header_logo_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .site-branding .site-title a',
							'property' => 'color',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_sticky_header_logo_color_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Image(
				array(
					'settings'        => 'reign_sticky_header_menu_logo',
					'label'           => esc_html__( 'Sticky Header Logo', 'reign' ),
					'description'     => esc_html__( 'Add, Remove, Change sticky header logo. (Note: This setting not working with header layout 4)', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'priority'        => 10,
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_sticky_header_menu_logo_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_header_sticky_menu_text_color',
					'label'           => esc_html__( 'Menu Item Font Color', 'reign' ),
					'description'     => esc_html__( 'The color of sticky menu item.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_text_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .primary-menu > li > a',
							'property' => 'color',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sticky_menu_text_color_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_header_sticky_menu_text_hover_color',
					'label'           => esc_html__( 'Menu Item Font Color [Hover]', 'reign' ),
					'description'     => esc_html__( 'The color of sticky menu item hover.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_text_hover_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .primary-menu > li > a:hover',
							'property' => 'color',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sticky_menu_text_hover_color_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_header_sticky_menu_text_active_color',
					'label'           => esc_html__( 'Menu Item Font Color [Active]', 'reign' ),
					'description'     => esc_html__( 'The color of sticky menu item active.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_text_active_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .primary-menu > li.current-menu-item > a,
										.reign-sticky-header #masthead .header-desktop.nav-scrolling .primary-menu > li.current_page_item > a',
										
							'property' => 'color',
						)
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sticky_menu_text_active_color_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_header_sticky_menu_bg_hover_color',
					'label'           => esc_html__( 'Menu Item Font Background [Hover]', 'reign' ),
					'description'     => esc_html__( 'The background color of sticky menu item hover.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_bg_hover_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .primary-menu > li a:hover:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling.version-one .primary-menu > li a:hover:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling.version-two .primary-menu > li a:hover:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling.version-three .primary-menu > li a:hover:before',
							'property' => 'background',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sticky_menu_bg_hover_color_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_header_sticky_menu_bg_active_color',
					'label'           => esc_html__( 'Menu Item Font Background [Active]', 'reign' ),
					'description'     => esc_html__( 'The background color of sticky menu item active.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_bg_active_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .primary-menu > li.current-menu-item a:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling.version-one .primary-menu > li.current-menu-item a:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling.version-two .primary-menu > li.current-menu-item a:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling.version-three .primary-menu > li.current-menu-item a:before',
							'property' => 'background',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_sticky_menu_bg_active_color_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_sticky_header_icon_color',
					'label'           => esc_html__( 'Icon Color', 'reign' ),
					'description'     => esc_html__( 'The color of sticky header icon.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_sticky_header_icon_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .rg-search-icon:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling .rg-icon-wrap span:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling .rg-icon-wrap, .reign-sticky-header #masthead .header-desktop.nav-scrolling .user-link-wrap .user-link, .reign-sticky-header #masthead .header-desktop.nav-scrolling .ps-user-name, .reign-sticky-header #masthead .header-desktop.nav-scrolling .ps-dropdown--userbar .ps-dropdown__toggle, .reign-sticky-header #masthead .header-desktop.nav-scrolling .ps-widget--userbar__logout>a',
							'property' => 'color',
						),
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .wbcom-nav-menu-toggle span',
							'property' => 'background',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_sticky_header_icon_color_divider',
					'section'         => 'reign_header_sticky_menu',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_sticky_header_icon_hover_color',
					'label'           => esc_html__( 'Icon Color [Hover]', 'reign' ),
					'description'     => esc_html__( 'The color of sticky header icon hover.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_sticky_header_icon_hover_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => array(
						array(
							'element'  => '.reign-sticky-header #masthead .header-desktop.nav-scrolling .rg-search-icon:hover:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling .rg-icon-wrap span:hover:before, .reign-sticky-header #masthead .header-desktop.nav-scrolling .rg-icon-wrap:hover, .reign-sticky-header #masthead .header-desktop.nav-scrolling .user-link-wrap .user-link:hover, .reign-sticky-header #masthead .header-desktop.nav-scrolling .ps-user-name:hover, .reign-sticky-header #masthead .header-desktop.nav-scrolling .ps-dropdown--userbar .ps-dropdown__toggle:hover, .reign-sticky-header #masthead .header-desktop.nav-scrolling .ps-widget--userbar__logout>a:hover',
							'property' => 'color',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Header_Sticky_Menu.
 *
 * @return Reign_Kirki_Header_Sticky_Menu
 */
Reign_Kirki_Header_Sticky_Menu::instance();

<?php
/**
 * Reign Customizer Header Sticky Menu
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Header_Sticky_Menu_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Header_Sticky_Menu_Fields
	 */
	class Reign_Customizer_Header_Sticky_Menu_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Header_Sticky_Menu_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Header_Sticky_Menu_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Header_Sticky_Menu_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Header_Sticky_Menu_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Header_Sticky_Menu_Fields Constructor.
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
				'reign_header_sticky_menu',
				array(
					'title'       => esc_html__( 'Sticky Menu', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_header_panel',
					'description' => esc_html__( 'Style the header after it sticks to the top on scroll.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_get_customizer_default_value_set();

			// Card 9957300131: Output_Builder emits a field's CSS whenever it has
			// an 'output' + a non-empty value - it does NOT honour active_callback.
			// So gate the sticky colour outputs on the 'Customize Sticky Menu Style'
			// toggle here; when it's off we attach no output and no sticky colour
			// CSS is emitted (the sticky header inherits the normal header colours).
			$sticky_custom = reign_is_truthy( get_theme_mod( 'reign_header_sticky_menu_custom_style_enable', false ) );

			\Reign\Customizer_Framework\Field::add( 'switch',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'        => 'reign_header_sticky_menu_custom_style_enable',
					'label'           => esc_html__( 'Customize Sticky Menu Style', 'reign' ),
					'description'     => esc_html__( 'Override the sticky header colors and logo instead of inheriting the normal header.', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_header_sticky_menu_bg_color',
					'label'           => esc_html__( 'Header Background Color', 'reign' ),
					'description'     => esc_html__( 'The background color of the sticky header.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_bg_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop',
							'property' => 'background-color',
						),
					) : array(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_sticky_header_logo_color',
					'label'           => esc_html__( 'Site Title', 'reign' ),
					'description'     => esc_html__( 'The color of site title.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_sticky_header_logo_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .site-branding .site-title a',
							'property' => 'color',
						),
					) : array(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'image',
				array(
					'settings'        => 'reign_sticky_header_menu_logo',
					'label'           => esc_html__( 'Sticky Header Logo', 'reign' ),
					'description'     => esc_html__( 'Not applied when Header Layout 4 is active.', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_header_sticky_menu_text_color',
					'label'           => esc_html__( 'Menu Item Font Color', 'reign' ),
					'description'     => esc_html__( 'The color of sticky menu item.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_text_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .primary-menu > li > a',
							'property' => 'color',
						),
					) : array(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_header_sticky_menu_text_hover_color',
					'label'           => esc_html__( 'Menu Item Font Color [Hover]', 'reign' ),
					'description'     => esc_html__( 'The color of sticky menu item hover.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_text_hover_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .primary-menu > li > a:hover',
							'property' => 'color',
						),
					) : array(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_header_sticky_menu_text_active_color',
					'label'           => esc_html__( 'Menu Item Font Color [Active]', 'reign' ),
					'description'     => esc_html__( 'The color of sticky menu item active.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_text_active_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .primary-menu > li.current-menu-item > a,
										.reign-sticky-header #masthead.is-pinned .header-desktop .primary-menu > li.current_page_item > a',
										
							'property' => 'color',
						)
					) : array(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_header_sticky_menu_bg_hover_color',
					'label'           => esc_html__( 'Menu Item Background [Hover]', 'reign' ),
					'description'     => esc_html__( 'The background color of sticky menu item hover.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_bg_hover_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .primary-menu > li a:hover:before, .reign-sticky-header #masthead.is-pinned .header-desktop.version-one .primary-menu > li a:hover:before, .reign-sticky-header #masthead.is-pinned .header-desktop.version-two .primary-menu > li a:hover:before, .reign-sticky-header #masthead.is-pinned .header-desktop.version-three .primary-menu > li a:hover:before',
							'property' => 'background',
						),
					) : array(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_header_sticky_menu_bg_active_color',
					'label'           => esc_html__( 'Menu Item Background [Active]', 'reign' ),
					'description'     => esc_html__( 'The background color of sticky menu item active.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_header_sticky_menu_bg_active_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .primary-menu > li.current-menu-item a:before, .reign-sticky-header #masthead.is-pinned .header-desktop.version-one .primary-menu > li.current-menu-item a:before, .reign-sticky-header #masthead.is-pinned .header-desktop.version-two .primary-menu > li.current-menu-item a:before, .reign-sticky-header #masthead.is-pinned .header-desktop.version-three .primary-menu > li.current-menu-item a:before',
							'property' => 'background',
						),
					) : array(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_sticky_header_icon_color',
					'label'           => esc_html__( 'Icon Color', 'reign' ),
					'description'     => esc_html__( 'The color of sticky header icon.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_sticky_header_icon_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .rg-search-icon:before, .reign-sticky-header #masthead.is-pinned .header-desktop .rg-icon-wrap span:before, .reign-sticky-header #masthead.is-pinned .header-desktop .rg-icon-wrap, .reign-sticky-header #masthead.is-pinned .header-desktop .user-link-wrap .user-link, .reign-sticky-header #masthead.is-pinned .header-desktop .ps-user-name, .reign-sticky-header #masthead.is-pinned .header-desktop .ps-dropdown--userbar .ps-dropdown__toggle, .reign-sticky-header #masthead.is-pinned .header-desktop .ps-widget--userbar__logout>a',
							'property' => 'color',
						),
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .wbcom-nav-menu-toggle span',
							'property' => 'background',
						),
					) : array(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_sticky_menu_custom_style_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'color',
				array(
					'settings'        => 'reign_sticky_header_icon_hover_color',
					'label'           => esc_html__( 'Icon Color [Hover]', 'reign' ),
					'description'     => esc_html__( 'The color of sticky header icon hover.', 'reign' ),
					'section'         => 'reign_header_sticky_menu',
					'default'         => $default_value_set['reign_sticky_header_icon_hover_color'],
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'output'          => $sticky_custom ? array(
						array(
							'element'  => '.reign-sticky-header #masthead.is-pinned .header-desktop .rg-search-icon:hover:before, .reign-sticky-header #masthead.is-pinned .header-desktop .rg-icon-wrap span:hover:before, .reign-sticky-header #masthead.is-pinned .header-desktop .rg-icon-wrap:hover, .reign-sticky-header #masthead.is-pinned .header-desktop .user-link-wrap .user-link:hover, .reign-sticky-header #masthead.is-pinned .header-desktop .ps-user-name:hover, .reign-sticky-header #masthead.is-pinned .header-desktop .ps-dropdown--userbar .ps-dropdown__toggle:hover, .reign-sticky-header #masthead.is-pinned .header-desktop .ps-widget--userbar__logout>a:hover',
							'property' => 'color',
						),
					) : array(),
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
 * Main instance of Reign_Customizer_Header_Sticky_Menu_Fields.
 *
 * @return Reign_Customizer_Header_Sticky_Menu_Fields
 */
Reign_Customizer_Header_Sticky_Menu_Fields::instance();

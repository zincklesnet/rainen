<?php
/**
 * Reign Kirki Site Layout
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Site_Layout' ) ) :

	/**
	 * @class Reign_Kirki_Site_Layout
	 */
	class Reign_Kirki_Site_Layout {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Site_Layout
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Site_Layout Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Site_Layout is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Site_Layout - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Site_Layout Constructor.
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
				'reign_site_layout_options',
				array(
					'title'       => esc_html__( 'Layouts', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_general_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Adjusts the max-width value for the `.search-wrap .rg-search-form-wrap` selector.
		 *
		 * @param string $value The original max-width value from the Kirki setting (e.g., "1170px").
		 * @return string The adjusted max-width value (e.g., "1140px") or the original value.
		 */
		public function adjust_search_form_max_width( $value ) {
			// Only process if it's a pixel value
			if ( strpos( $value, 'px' ) !== false ) {
				$numeric = (int) filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
				$adjusted = max(0, $numeric - 30); // Prevent negative values
				return $adjusted . 'px';
			}

			// If it's percentage or other unit, return as-is (or handle differently)
			return $value;
		}

		/**
		 * Dynamically get container max-width output based on active plugins.
		 *
		 * @return array
		 */
		protected function get_container_max_width_output(): array {
			$selectors = array(
				'.container',
				'.container-fluid',
				'.reign-stretched_view .footer-wrap .container',
				'.reign-stretched_view_no_title .footer-wrap .container',
				'.reign-stretched_view .reign-header-top .container',
				'.reign-stretched_view_no_title .reign-header-top .container',
				'.reign-stretched_view .reign-fallback-header .container',
				'.reign-stretched_view_no_title .reign-fallback-header .container',
			);

			// PeepSo.
			if ( class_exists( 'PeepSo' ) ) {
				$selectors[] = '.reign-peepso-group.layout-full-width .ps-focus__cover-inner';
				$selectors[] = '.reign-peepso-group.layout-full-width .ps-focus__info';
				$selectors[] = '.reign-peepso-group.layout-full-width .ps-focus__menu-inner';
				$selectors[] = '.reign-peepso-profile.layout-full-width .ps-focus__cover-inner';
				$selectors[] = '.reign-peepso-profile.layout-full-width .ps-focus__info';
				$selectors[] = '.reign-peepso-profile.layout-full-width .ps-focus__menu-inner';
				$selectors[] = '.reign-peepso-profile.layout-full-width .ps-badgeos__list-wrapper';
			}

			return array(
				array(
					'element'  => implode( ', ', $selectors ),
					'property' => 'max-width',
					'function' => 'css',
				),
				array(
					'element'  => '.search-wrap .rg-search-form-wrap',
					'property' => 'max-width',
					'function' => 'callback',
					'callback' => [ $this, 'adjust_search_form_max_width' ],
				),
			);
		}

		/**
		 * Dynamically get sidebar max-width output based on active plugins.
		 *
		 * @return array
		 */
		protected function get_sidebar_max_width_output(): array {
			$selectors = array(
				'.site-content .widget-area',
				'.reign-both_sidebar aside#reign-sidebar-right',
				'.reign-both_sidebar aside#reign-sidebar-left',
			);

			// BuddyPress.
			if ( class_exists( 'BuddyPress' ) ) {
				$selectors[] = '.bp-user #secondary.group-single-widget-area';
				$selectors[] = '.bp-user #secondary.member-profile-widget-area';
				$selectors[] = '.single-item.groups #secondary.group-single-widget-area';
				$selectors[] = '.single-item.groups #secondary.member-profile-widget-area';
			}

			// Dokan.
			if ( class_exists( 'WeDevs_Dokan' ) ) {
				$selectors[] = '.dokan-store-wrap .dokan-store-sidebar';
			}

			return array(
				array(
					'element'  => implode( ', ', $selectors ),
					'property' => 'max-width',
					'function' => 'css',
				),
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			new \Kirki\Field\Checkbox_Switch(
				array(
					'type'        => 'switch',
					'settings'    => 'reign_enable_preloading',
					'label'       => esc_html__( 'Site Loader', 'reign' ),
					'description' => esc_html__( 'Shows a loader animation while the page content is loading.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'default'     => 0,
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Radio_Image(
				array(
					'settings'        => 'reign_preloading_icon',
					'label'           => esc_html__( 'Loader Icon', 'reign' ),
					'description'     => '',
					'section'         => 'reign_site_layout_options',
					'default'         => REIGN_THEME_URI . '/lib/images/loader-1.svg',
					'priority'        => 10,
					'choices'         => array(
						REIGN_THEME_URI . '/lib/images/loader-1.svg' => REIGN_THEME_URI . '/lib/images/loader-1.svg',
						REIGN_THEME_URI . '/lib/images/loader-2.svg' => REIGN_THEME_URI . '/lib/images/loader-2.svg',
						REIGN_THEME_URI . '/lib/images/loader-3.svg' => REIGN_THEME_URI . '/lib/images/loader-3.svg',
						REIGN_THEME_URI . '/lib/images/loader-4.svg' => REIGN_THEME_URI . '/lib/images/loader-4.svg',
						REIGN_THEME_URI . '/lib/images/loader-5.svg' => REIGN_THEME_URI . '/lib/images/loader-5.svg',
						REIGN_THEME_URI . '/lib/images/loader-6.svg' => REIGN_THEME_URI . '/lib/images/loader-6.svg',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_enable_preloading',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Color(
				array(
					'settings'        => 'reign_preloading_bg_color',
					'label'           => esc_html__( 'Loader Background Color', 'reign' ),
					'description'     => '',
					'section'         => 'reign_site_layout_options',
					'default'         => '#ffffff',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'active_callback' => array(
						array(
							'setting'  => 'reign_enable_preloading',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_preloading_bg_color_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_enable_scrollup',
					'label'       => esc_html__( 'Site Scroll Up', 'reign' ),
					'description' => '',
					'section'     => 'reign_site_layout_options',
					'default'     => '',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Select(
				array(
					'settings'        => 'reign_scrollup_style',
					'label'           => esc_html__( 'Site Scroll Up Style', 'reign' ),
					'description'     => esc_html__( 'Set the scrollup style.', 'reign' ),
					'section'         => 'reign_site_layout_options',
					'priority'        => 10,
					'default'         => 'style1',
					'choices'         => array(
						'style1' => esc_html__( 'Style 1', 'reign' ),
						'style2' => esc_html__( 'Style 2', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_enable_scrollup',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_scrollup_styl_divider',
					'section'         => 'reign_site_layout_options',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_enable_scrollup',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Radio(
				array(
					'settings'        => 'reign_scrollup_position',
					'label'           => esc_html__( 'Scroll Up Position', 'reign' ),
					'description'     => esc_html__( 'Select scroll up position left or right side.', 'reign' ),
					'section'         => 'reign_site_layout_options',
					'default'         => 'right',
					'priority'        => 10,
					'choices'         => array(
						'left'  => esc_html__( 'Left', 'reign' ),
						'right' => esc_html__( 'Right', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_enable_scrollup',
							'operator' => '===',
							'value'    => true,
						),
						array(
							'setting'  => 'reign_scrollup_style',
							'operator' => '===',
							'value'    => 'style1',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_scrollup_style_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Radio_Image(
				array(
					'settings'    => 'reign_site_layout',
					'label'       => esc_html__( 'Site Layout', 'reign' ),
					'description' => esc_html__( 'Select site layout.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'default'     => 'full_width',
					'priority'    => 10,
					'choices'     => array(
						'full_width' => REIGN_THEME_URI . '/lib/images/full-width.jpg',
						'box_width'  => REIGN_THEME_URI . '/lib/images/box-width.jpg',
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_site_layout_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_sticky_sidebar',
					'label'       => esc_html__( 'Sticky Sidebar', 'reign' ),
					'description' => '',
					'section'     => 'reign_site_layout_options',
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
					'settings' => 'reign_site_sidebar_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Dimension(
				array(
					'settings'    => 'site_container_width',
					'label'       => esc_html__( 'Site Container Width', 'reign' ),
					'description' => esc_html__( 'Set the width of the site container ( px or % ). Default is 1170px.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'default'     => '1170px',
					'priority'    => 10,
					'transport'   => 'auto',
					'output'      => $this->get_container_max_width_output(),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_container_width_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Dimension(
				array(
					'settings'        => 'site_box_layout_container_width',
					'label'           => esc_html__( 'Site Box Layout Container Width', 'reign' ),
					'description'     => esc_html__( 'Set the width of the site box layout ( px or % ). Default is 1170px.', 'reign' ),
					'section'         => 'reign_site_layout_options',
					'default'         => '1170px',
					'priority'        => 10,
					'transport'       => 'auto',
					'output'          => array(
						array(
							'element'  => '.rg-boxed-layout .site, .rg-boxed-layout #masthead, .reign-fallback-header.header-desktop.fixed-top',
							'function' => 'css',
							'property' => 'max-width',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_site_layout',
							'operator' => '==',
							'value'    => 'box_width',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_layout_container_width_divider',
					'section'         => 'reign_site_layout_options',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_site_layout',
							'operator' => '==',
							'value'    => 'box_width',
						),
					),
				)
			);

			new \Kirki\Field\Dimension(
				array(
					'settings'    => 'site_sidebar_width',
					'label'       => esc_html__( 'Site Sidebar Width', 'reign' ),
					'description' => esc_html__( 'Set the width of the sidebar ( px or % ). Default is 28.125%.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'default'     => '28.125%',
					'priority'    => 10,
					'transport'   => 'auto',
					'output'      => $this->get_sidebar_max_width_output(),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_sidebar_width_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_global_border_radius_toggle',
					'label'       => esc_html__( 'Global Border Radius', 'reign' ),
					'description' => '',
					'section'     => 'reign_site_layout_options',
					'priority'    => 10,
					'default'     => '',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Dimension(
				array(
					'settings'        => 'reign_global_border_radius_option',
					'label'           => esc_html__( 'Site Global Border Radius', 'reign' ),
					'description'     => esc_html__( 'Set the global border radius ( px ). Default is 8px.', 'reign' ),
					'section'         => 'reign_site_layout_options',
					'default'         => '8px',
					'priority'        => 10,
					'transport'       => 'auto',
					'active_callback' => array(
						array(
							'setting'  => 'reign_global_border_radius_toggle',
							'operator' => '!=',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_global_border_radius_option_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			// Global Button Radius.
			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_global_button_radius_toggle',
					'label'       => esc_html__( 'Global Button Radius', 'reign' ),
					'description' => 'Select the border radius of buttons.',
					'section'     => 'reign_site_layout_options',
					'priority'    => 10,
					'default'     => '',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Dimension(
				array(
					'settings'        => 'reign_global_button_radius',
					'label'           => esc_html__( 'Global Button Radius', 'reign' ),
					'description'     => esc_html__( 'Set the button border radius ( px ). Default is 6px.', 'reign' ),
					'section'         => 'reign_site_layout_options',
					'priority'        => 10,
					'default'         => '6px',
					'active_callback' => array(
						array(
							'setting'  => 'reign_global_button_radius_toggle',
							'operator' => '!=',
							'value'    => false,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_global_button_radius_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			// Form Radius.
			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_global_form_radius_toggle',
					'label'       => esc_html__( 'Global Form Radius', 'reign' ),
					'description' => 'Select the border radius of form elements.',
					'section'     => 'reign_site_layout_options',
					'priority'    => 10,
					'default'     => '',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Dimension(
				array(
					'settings'        => 'reign_global_form_radius',
					'label'           => esc_html__( 'Global Form Radius', 'reign' ),
					'description'     => esc_html__( 'Set the form elements border radius ( px ). Default is 6px.', 'reign' ),
					'section'         => 'reign_site_layout_options',
					'priority'        => 10,
					'default'         => '6px',
					'active_callback' => array(
						array(
							'setting'  => 'reign_global_form_radius_toggle',
							'operator' => '!=',
							'value'    => false,
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Site_Layout.
 *
 * @return Reign_Kirki_Site_Layout
 */
Reign_Kirki_Site_Layout::instance();

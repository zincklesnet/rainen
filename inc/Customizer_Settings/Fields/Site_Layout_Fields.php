<?php
/**
 * Reign Customizer Site Layout
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Site_Layout_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Site_Layout_Fields
	 */
	class Reign_Customizer_Site_Layout_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Site_Layout_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Site_Layout_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Site_Layout_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Site_Layout_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Site_Layout_Fields Constructor.
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
				'reign_site_layout_options',
				array(
					'title'       => esc_html__( 'Global Layout', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_general_panel',
					'description' => esc_html__( 'Site-wide layout: full width or boxed, container widths, sticky sidebar, page loader and scroll-to-top. Choose the layout and sidebar for each type of content under Content Layouts.', 'reign' ),
				)
			);
		}

		/**
		 * Adjusts the max-width value for the `.search-wrap .rg-search-form-wrap` selector.
		 *
		 * @param string $value The original max-width value from the Customizer setting (e.g., "1170px").
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

			\Reign\Customizer_Framework\Field::add( 'switch',
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

			\Reign\Customizer_Framework\Field::add( 'radio_image',
				array(
					'settings'        => 'reign_preloading_icon',
					'label'           => esc_html__( 'Loader Icon', 'reign' ),
					'description'     => esc_html__( 'Pick the loading animation shown while the page loads.', 'reign' ),
					'section'         => 'reign_site_layout_options',
					'default'         => REIGN_THEME_URI . '/lib/images/loader-1.svg',
					// Choices are full image URLs, not slugs; the radio_image default
					// sanitizer (sanitize_key) would strip ':' '/' '.' and mangle the
					// URL, so the saved icon never matched. Store the raw URL instead.
					'sanitize_callback' => 'esc_url_raw',
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

			\Reign\Customizer_Framework\Field::add( 'color',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_preloading_bg_color_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_enable_scrollup',
					'label'       => esc_html__( 'Site Scroll Up', 'reign' ),
					'description' => esc_html__( 'Show a button that scrolls visitors back to the top.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'default'     => 'off',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'select',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_scrollup_style_divider',
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

			\Reign\Customizer_Framework\Field::add( 'radio',
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

			\Reign\Customizer_Framework\Field::add( 'radio_image',
				array(
					'settings'    => 'reign_site_layout',
					'label'       => esc_html__( 'Site Layout', 'reign' ),
					'description' => esc_html__( 'Choose a full-width or boxed page container.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'default'     => 'full_width',
					'priority'    => 10,
					'choices'     => array(
						'full_width' => REIGN_THEME_URI . '/lib/images/full-width.jpg',
						'box_width'  => REIGN_THEME_URI . '/lib/images/box-width.jpg',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_site_layout_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_sticky_sidebar',
					'label'       => esc_html__( 'Sticky Sidebar', 'reign' ),
					'description' => esc_html__( 'Keep the sidebar in view as the visitor scrolls.', 'reign' ),
					'section'     => 'reign_site_layout_options',
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
					'settings' => 'reign_site_sidebar_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'dimension',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_container_width_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'dimension',
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
							// Every selector here MUST be scoped under
							// `.rg-boxed-layout`. Without that prefix the
							// rule applies in every layout mode — pre-fix
							// the sticky header was capped to the boxed
							// width on full-width / stretched layouts,
							// leaving a visible gap between the white
							// header and the right edge of the viewport
							// on any screen wider than ~1170 px.
							'element'  => '.rg-boxed-layout .site, .rg-boxed-layout #masthead, .rg-boxed-layout .reign-fallback-header.header-desktop.fixed-top',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
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

			\Reign\Customizer_Framework\Field::add( 'dimension',
				array(
					'settings'    => 'site_sidebar_width',
					'label'       => esc_html__( 'Site Sidebar Width', 'reign' ),
					'description' => esc_html__( 'Width of the sidebar ( px or % ). Default is 28.125%. Choose which side the sidebar appears on (or hide it) per content type under Content Layouts.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'default'     => '28.125%',
					'priority'    => 10,
					'transport'   => 'auto',
					'output'      => $this->get_sidebar_max_width_output(),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_sidebar_width_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_global_border_radius_toggle',
					'label'       => esc_html__( 'Global Border Radius', 'reign' ),
					'description' => esc_html__( 'Turn on a custom corner radius for cards and containers.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'priority'    => 10,
					'default'     => 'off',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'dimension',
				array(
					'settings'        => 'reign_global_border_radius_option',
					'label'           => esc_html__( 'Border Radius Value', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_global_border_radius_option_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			// Global Button Radius.
			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_global_button_radius_toggle',
					'label'       => esc_html__( 'Global Button Radius', 'reign' ),
					'description' => esc_html__( 'Turn on a custom corner radius for buttons.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'priority'    => 10,
					'default'     => 'off',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'dimension',
				array(
					'settings'        => 'reign_global_button_radius',
					'label'           => esc_html__( 'Button Radius Value', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_global_button_radius_divider',
					'section'  => 'reign_site_layout_options',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			// Form Radius.
			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_global_form_radius_toggle',
					'label'       => esc_html__( 'Global Form Radius', 'reign' ),
					'description' => esc_html__( 'Turn on a custom corner radius for form fields.', 'reign' ),
					'section'     => 'reign_site_layout_options',
					'priority'    => 10,
					'default'     => 'off',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'dimension',
				array(
					'settings'        => 'reign_global_form_radius',
					'label'           => esc_html__( 'Form Radius Value', 'reign' ),
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
 * Main instance of Reign_Customizer_Site_Layout_Fields.
 *
 * @return Reign_Customizer_Site_Layout_Fields
 */
Reign_Customizer_Site_Layout_Fields::instance();

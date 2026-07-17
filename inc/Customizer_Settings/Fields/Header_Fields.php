<?php
/**
 * Reign Customizer Header
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Header_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Header_Fields
	 */
	class Reign_Customizer_Header_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Header_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Header_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Header_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Header_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Header_Fields Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
			$this->includes();
		}

		public function includes() {
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
				'reign_header_panel',
				array(
					'priority'    => 21,
					'title'       => esc_html__( 'Header', 'reign' ),
					'description' => '',
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_header_style',
				array(
					'title'       => esc_html__( 'Layout', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add( 'radio_image',
				array(
					'settings'        => 'reign_header_layout',
					'label'           => esc_html__( 'Layout', 'reign' ),
					'description'     => esc_html__( 'Choose your header layout.', 'reign' ),
					'section'         => 'reign_header_style',
					'default'         => 'v2',
					'priority'        => 10,
					'choices'         => apply_filters(
						'reign_theme_header_choices',
						array(
							'v1' => REIGN_THEME_URI . '/lib/images/header-v1.jpg',
							'v2' => REIGN_THEME_URI . '/lib/images/header-v2.jpg',
							'v3' => REIGN_THEME_URI . '/lib/images/header-v3.jpg',
							'v4' => REIGN_THEME_URI . '/lib/images/header-v4.jpg',
						)
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_header_layout_divider',
					'section'         => 'reign_header_style',
					'choices'         => array(
						'color' => '#dcdcde',
					),
				)
			);

			// Gate on WooCommerce only — `WC_Widget_Product_Search` is registered
			// on `widgets_init` which fires AFTER `customize_register`, so
			// requiring its class would always evaluate false during
			// customizer registration on a fresh request and the 2-option
			// dropdown would never reach the customer even when WooCommerce
			// is fully loaded. The widget class ships in core WooCommerce
			// since v2.2 (2014) — present whenever the WooCommerce class is.
			if ( class_exists( 'WooCommerce' ) ) {
				$search_choices = array(
					'product_search' => esc_html__( 'Product Search', 'reign' ),
					'default_search' => esc_html__( 'Default Search', 'reign' ),
				);
				$search_default = 'product_search';
			} else {
				$search_choices = array(
					'default_search' => esc_html__( 'Default Search', 'reign' ),
				);
				$search_default = 'default_search';
			}

			\Reign\Customizer_Framework\Field::add( 'select',
				array(
					'settings'        => 'reign_header_search_option',
					'label'           => esc_html__( 'Header Search Style', 'reign' ),
					'description'     => esc_html__( 'Used by the Header Layout 4 style.', 'reign' ),
					'section'         => 'reign_header_style',
					'default'         => $search_default,
					'priority'        => 10,
					'choices'         => $search_choices,
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_layout',
							'operator' => '==',
							'value'    => 'v4',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_header_search_option_divider',
					'section'         => 'reign_header_style',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_layout',
							'operator' => '==',
							'value'    => 'v4',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'select',
				array(
					'settings'    => 'reign_header_main_menu_hover_style',
					'label'       => esc_html__( 'Main Menu Hover Style', 'reign' ),
					'description' => esc_html__( 'Select main menu hover style (Desktop View).', 'reign' ),
					'section'     => 'reign_header_style',
					'default'     => 'select_style',
					'priority'    => 10,
					'choices'     => array(
						'select_style' => esc_html__( '--- Select Style ---', 'reign' ),
						'style1'       => esc_html__( 'Hover Style 1', 'reign' ),
						'style2'       => esc_html__( 'Hover Style 2', 'reign' ),
						'style3'       => esc_html__( 'Hover Style 3', 'reign' ),
						'style4'       => esc_html__( 'Hover Style 4', 'reign' ),
						'style5'       => esc_html__( 'Hover Style 5', 'reign' ),
						'style6'       => esc_html__( 'Hover Style 6', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_header_main_menu_hover_style_divider',
					'section'  => 'reign_header_style',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			// BuddyNext and BuddyPress are mutually exclusive community plugins that
			// both provide the message + notification header icons, so they share
			// the same branches here. The only difference: Friends Request is a
			// BuddyPress-only feature, so that one extra control is removed below
			// when the community plugin is BuddyNext.
			$is_community = class_exists( 'BuddyPress' ) || defined( 'BUDDYNEXT_VERSION' );
			$has_store    = class_exists( 'WooCommerce' ) || class_exists( 'Easy_Digital_Downloads' );

			if ( $is_community && $has_store ) {

				$icons_choices = array(
					'search'          => esc_html__( 'Search', 'reign' ),
					'cart'            => esc_html__( 'Cart', 'reign' ),
					'friends-request' => esc_html__( 'Friends Request', 'reign' ),
					'message'         => esc_html__( 'Message', 'reign' ),
					'notification'    => esc_html__( 'Notification', 'reign' ),
					'user-menu'       => esc_html__( 'User Menu', 'reign' ),
					'login'           => esc_html__( 'Login', 'reign' ),
					'register-menu'   => esc_html__( 'Register', 'reign' ),
				);

			} elseif ( $has_store ) {

				$icons_choices = array(
					'search'        => esc_html__( 'Search', 'reign' ),
					'cart'          => esc_html__( 'Cart', 'reign' ),
					'user-menu'     => esc_html__( 'User Menu', 'reign' ),
					'login'         => esc_html__( 'Login', 'reign' ),
					'register-menu' => esc_html__( 'Register', 'reign' ),
				);

			} elseif ( $is_community ) {

				$icons_choices = array(
					'search'          => esc_html__( 'Search', 'reign' ),
					'message'         => esc_html__( 'Message', 'reign' ),
					'friends-request' => esc_html__( 'Friends Request', 'reign' ),
					'notification'    => esc_html__( 'Notification', 'reign' ),
					'user-menu'       => esc_html__( 'User Menu', 'reign' ),
					'login'           => esc_html__( 'Login', 'reign' ),
					'register-menu'   => esc_html__( 'Register', 'reign' ),
				);

			} else {

				$icons_choices = array(
					'search'        => esc_html__( 'Search', 'reign' ),
					'user-menu'     => esc_html__( 'User Menu', 'reign' ),
					'login'         => esc_html__( 'Login', 'reign' ),
					'register-menu' => esc_html__( 'Register', 'reign' ),
				);

			}

			// Friends Request is BuddyPress-only; BuddyNext uses connections and
			// has no header control for it, so hide that extra control.
			if ( ! class_exists( 'BuddyPress' ) ) {
				unset( $icons_choices['friends-request'] );
			}

			\Reign\Customizer_Framework\Field::add( 'sortable',
				array(
					'settings'        => 'reign_header_icons_set',
					'label'           => esc_html__( 'Header Icons Options', 'reign' ),
					'description'     => '',
					'section'         => 'reign_header_style',
					'priority'        => 10,
					'default'         => $default_value_set['reign_header_icons_set'],
					'choices'         => $icons_choices,
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_header_icons_set_divider',
					'section'  => 'reign_header_style',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_header_main_menu_more_enable',
					'label'       => esc_html__( 'Wrap overflow menu items into a More dropdown', 'reign' ),
					'description' => esc_html__( 'Move menu items that do not fit on one line into a More dropdown.', 'reign' ),
					'section'     => 'reign_header_style',
					'default'     => 1,
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
 * Main instance of Reign_Customizer_Header_Fields.
 *
 * @return Reign_Customizer_Header_Fields
 */
Reign_Customizer_Header_Fields::instance();

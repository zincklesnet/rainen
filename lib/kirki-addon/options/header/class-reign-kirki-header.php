<?php
/**
 * Reign Kirki Header
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Header' ) ) :

	/**
	 * @class Reign_Kirki_Header
	 */
	class Reign_Kirki_Header {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Header
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Header Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Header is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Header - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Header Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
			$this->includes();
		}

		public function includes() {
			include_once REIGN_THEME_DIR . '/lib/kirki-addon/options/header/class-reign-kirki-header-sticky-menu.php';
			include_once REIGN_THEME_DIR . '/lib/kirki-addon/options/header/class-reign-kirki-header-mobile-menu.php';
			include_once REIGN_THEME_DIR . '/lib/kirki-addon/options/header/class-reign-kirki-header-topbar.php';
			include_once REIGN_THEME_DIR . '/lib/kirki-addon/options/header/class-reign-kirki-left-panel.php';
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
				'reign_header_panel',
				array(
					'priority'    => 21,
					'title'       => esc_html__( 'Header', 'reign' ),
					'description' => '',
				)
			);

			new \Kirki\Section(
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

			new \Kirki\Field\Radio_Image(
				array(
					'settings'        => 'reign_header_layout',
					'label'           => esc_html__( 'Layout', 'reign' ),
					'description'     => esc_html__( 'Select header layout for header.', 'reign' ),
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

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_header_layout_divider',
					'section'         => 'reign_header_style',
					'choices'         => array(
						'color' => '#dcdcde',
					),
				)
			);

			if ( class_exists( 'WooCommerce' ) && class_exists( 'WC_Widget_Product_Search' ) ) {
				new \Kirki\Field\Select(
					array(
						'settings'        => 'reign_header_search_option',
						'label'           => esc_html__( 'Header Search Option', 'reign' ),
						'description'     => esc_html__( 'Select the search functionality type for header layout 4.', 'reign' ),
						'section'         => 'reign_header_style',
						'default'         => 'product_search',
						'priority'        => 10,
						'choices'         => array(
							'product_search' => esc_html__( 'Product Search', 'reign' ),
							'default_search' => esc_html__( 'Default Search', 'reign' ),
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
			} else {
				new \Kirki\Field\Select(
					array(
						'settings'        => 'reign_header_search_option',
						'label'           => esc_html__( 'Header Search Option', 'reign' ),
						'description'     => esc_html__( 'Select the search functionality type for header layout 4.', 'reign' ),
						'section'         => 'reign_header_style',
						'default'         => 'default_search',
						'priority'        => 10,
						'choices'         => array(
							'default_search' => esc_html__( 'Default Search', 'reign' ),
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
			}

			new \Kirki\Pro\Field\Divider(
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

			new \Kirki\Field\Select(
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

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_header_main_menu_hover_style_divider',
					'section'  => 'reign_header_style',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			if ( class_exists( 'BuddyPress' ) && class_exists( 'WooCommerce' ) || class_exists( 'BuddyPress' ) && class_exists( 'Easy_Digital_Downloads' ) ) {

				new \Kirki\Field\Sortable(
					array(
						'settings'        => 'reign_header_icons_set',
						'label'           => esc_html__( 'Header Icons Options', 'reign' ),
						'description'     => '',
						'section'         => 'reign_header_style',
						'priority'        => 10,
						'default'         => $default_value_set['reign_header_icons_set'],
						'choices'         => array(
							'search'          => esc_html__( 'Search', 'reign' ),
							'cart'            => esc_html__( 'Cart', 'reign' ),
							'friends-request' => esc_html__( 'Friends Request', 'reign' ),
							'message'         => esc_html__( 'Message', 'reign' ),
							'notification'    => esc_html__( 'Notification', 'reign' ),
							'user-menu'       => esc_html__( 'User Menu', 'reign' ),
							'login'           => esc_html__( 'Login', 'reign' ),
							'register-menu'   => esc_html__( 'Register', 'reign' ),
						),
					)
				);

			} elseif ( class_exists( 'WooCommerce' ) || class_exists( 'Easy_Digital_Downloads' ) ) {

				new \Kirki\Field\Sortable(
					array(
						'settings'        => 'reign_header_icons_set',
						'label'           => esc_html__( 'Header Icons Options', 'reign' ),
						'description'     => '',
						'section'         => 'reign_header_style',
						'priority'        => 10,
						'default'         => $default_value_set['reign_header_icons_set'],
						'choices'         => array(
							'search'        => esc_html__( 'Search', 'reign' ),
							'cart'          => esc_html__( 'Cart', 'reign' ),
							'user-menu'     => esc_html__( 'User Menu', 'reign' ),
							'login'         => esc_html__( 'Login', 'reign' ),
							'register-menu' => esc_html__( 'Register', 'reign' ),
						),
					)
				);

			} elseif ( class_exists( 'BuddyPress' ) ) {

				new \Kirki\Field\Sortable(
					array(
						'settings'        => 'reign_header_icons_set',
						'label'           => esc_html__( 'Header Icons Options', 'reign' ),
						'description'     => '',
						'section'         => 'reign_header_style',
						'priority'        => 10,
						'default'         => $default_value_set['reign_header_icons_set'],
						'choices'         => array(
							'search'          => esc_html__( 'Search', 'reign' ),
							'message'         => esc_html__( 'Message', 'reign' ),
							'friends-request' => esc_html__( 'Friends Request', 'reign' ),
							'notification'    => esc_html__( 'Notification', 'reign' ),
							'user-menu'       => esc_html__( 'User Menu', 'reign' ),
							'login'           => esc_html__( 'Login', 'reign' ),
							'register-menu'   => esc_html__( 'Register', 'reign' ),
						),
					)
				);

			} else {

				new \Kirki\Field\Sortable(
					array(
						'settings'        => 'reign_header_icons_set',
						'label'           => esc_html__( 'Header Icons Options', 'reign' ),
						'description'     => '',
						'section'         => 'reign_header_style',
						'priority'        => 10,
						'default'         => $default_value_set['reign_header_icons_set'],
						'choices'         => array(
							'search'        => esc_html__( 'Search', 'reign' ),
							'user-menu'     => esc_html__( 'User Menu', 'reign' ),
							'login'         => esc_html__( 'Login', 'reign' ),
							'register-menu' => esc_html__( 'Register', 'reign' ),
						),
					)
				);

			}

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_header_icons_set_divider',
					'section'  => 'reign_header_style',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_header_main_menu_more_enable',
					'label'       => esc_html__( 'Enable \'More\' menus wrap in header menu.', 'reign' ),
					'description' => esc_html__( 'Enable or Disable \'More\' menus option for header menu.', 'reign' ),
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
 * Main instance of Reign_Kirki_Header.
 *
 * @return Reign_Kirki_Header
 */
Reign_Kirki_Header::instance();

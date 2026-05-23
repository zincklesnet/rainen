<?php
/**
 * Reign Kirki Header Mobile Menu
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Header_Mobile_Menu' ) ) :

	/**
	 * @class Reign_Kirki_Header_Mobile_Menu
	 */
	class Reign_Kirki_Header_Mobile_Menu {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Header_Mobile_Menu
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Header_Mobile_Menu Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Header_Mobile_Menu is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Header_Mobile_Menu - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Header_Mobile_Menu Constructor.
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
				'reign_header_mobile_menu',
				array(
					'title'       => esc_html__( 'Mobile Menu', 'reign' ),
					'priority'    => 11,
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

			new \Kirki\Field\Radio(
				array(
					'settings'    => 'reign_mobile_header_layout',
					'label'       => esc_html__( 'Mobile Header Layout', 'reign' ),
					'description' => esc_html__( 'Allows you to change mobile header layout.', 'reign' ),
					'section'     => 'reign_header_mobile_menu',
					'default'     => 'header-v1',
					'priority'    => 10,
					'choices'     => array(
						'header-v1' => esc_html__( 'Layout One', 'reign' ),
						'header-v2' => esc_html__( 'Layout Two', 'reign' ),
						'header-v3' => esc_html__( 'Layout Three', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_mobile_header_layout_divider',
					'section'  => 'reign_header_mobile_menu',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_header_mobile_menu_logo_enable',
					'label'       => esc_html__( 'Config Mobile Logo', 'reign' ),
					'description' => esc_html__( 'Allows you to config logo for mobile menu.', 'reign' ),
					'section'     => 'reign_header_mobile_menu',
					'default'     => 0,
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Custom', 'reign' ),
						'off' => esc_html__( 'Default', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Image(
				array(
					'settings'        => 'reign_header_mobile_menu_logo',
					'label'           => esc_html__( 'Mobile Logo', 'reign' ),
					'description'     => esc_html__( 'Allows you to add, remove, change mobile logo on your site.', 'reign' ),
					'section'         => 'reign_header_mobile_menu',
					'priority'        => 10,
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_mobile_menu_logo_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_header_mobile_menu_logo_divider',
					'section'  => 'reign_header_mobile_menu',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Slider(
				array(
					'settings'    => 'reign_site_mobile_logo_size',
					'label'       => esc_html__( 'Logo Size', 'reign' ),
					'description' => esc_attr__( 'Change the logo size as it appears on your site (Tablet/Mobile View).', 'reign' ),
					'section'     => 'reign_header_mobile_menu',
					'priority'    => 10,
					'choices'     => array(
						'min'  => 45,
						'max'  => 75,
						'step' => 1,
					),
					'output'      => array(
						array(
							'element'     => '.reign-mobile-logo, .site-branding a img',
							'property'    => 'max-height',
							'units'       => 'px',
							'media_query' => '@media screen and (max-width: 960px)',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_header_mobile_logo_size_divider',
					'section'  => 'reign_header_mobile_menu',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			if ( class_exists( 'BuddyPress' ) && class_exists( 'WooCommerce' ) || class_exists( 'BuddyPress' ) && class_exists( 'Easy_Digital_Downloads' ) ) {

				new \Kirki\Field\Sortable(
					array(
						'settings'    => 'reign_mobile_header_icons_set',
						'label'       => esc_html__( 'Mobile Header Icons Options', 'reign' ),
						'description' => '',
						'section'     => 'reign_header_mobile_menu',
						'priority'    => 10,
						'default'     => $default_value_set['reign_mobile_header_icons_set'],
						'choices'     => array(
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
						'settings'    => 'reign_mobile_header_icons_set',
						'label'       => esc_html__( 'Mobile Header Icons Options', 'reign' ),
						'description' => '',
						'section'     => 'reign_header_mobile_menu',
						'priority'    => 10,
						'default'     => $default_value_set['reign_mobile_header_icons_set'],
						'choices'     => array(
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
						'settings'    => 'reign_mobile_header_icons_set',
						'label'       => esc_html__( 'Mobile Header Icons Options', 'reign' ),
						'description' => '',
						'section'     => 'reign_header_mobile_menu',
						'priority'    => 10,
						'default'     => $default_value_set['reign_mobile_header_icons_set'],
						'choices'     => array(
							'search'          => esc_html__( 'Search', 'reign' ),
							'friends-request' => esc_html__( 'Friends Request', 'reign' ),
							'message'         => esc_html__( 'Message', 'reign' ),
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
						'settings'    => 'reign_mobile_header_icons_set',
						'label'       => esc_html__( 'Mobile Header Icons Options', 'reign' ),
						'description' => '',
						'section'     => 'reign_header_mobile_menu',
						'priority'    => 10,
						'default'     => $default_value_set['reign_mobile_header_icons_set'],
						'choices'     => array(
							'search'        => esc_html__( 'Search', 'reign' ),
							'user-menu'     => esc_html__( 'User Menu', 'reign' ),
							'login'         => esc_html__( 'Login', 'reign' ),
							'register-menu' => esc_html__( 'Register', 'reign' ),
						),
					)
				);

			}
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Header_Mobile_Menu.
 *
 * @return Reign_Kirki_Header_Mobile_Menu
 */
Reign_Kirki_Header_Mobile_Menu::instance();

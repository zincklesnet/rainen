<?php
/**
 * Reign Customizer Header Mobile Menu
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Header_Mobile_Menu_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Header_Mobile_Menu_Fields
	 */
	class Reign_Customizer_Header_Mobile_Menu_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Header_Mobile_Menu_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Header_Mobile_Menu_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Header_Mobile_Menu_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Header_Mobile_Menu_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Header_Mobile_Menu_Fields Constructor.
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
				'reign_header_mobile_menu',
				array(
					'title'       => esc_html__( 'Mobile Menu', 'reign' ),
					'priority'    => 11,
					'panel'       => 'reign_header_panel',
					'description' => esc_html__( 'Control the header and menu on mobile devices.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_get_customizer_default_value_set();

			\Reign\Customizer_Framework\Field::add( 'radio',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_mobile_header_layout_divider',
					'section'  => 'reign_header_mobile_menu',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_header_mobile_menu_logo_enable',
					'label'       => esc_html__( 'Use a Custom Mobile Logo', 'reign' ),
					'description' => esc_html__( 'Show a different logo on mobile instead of the site logo.', 'reign' ),
					'section'     => 'reign_header_mobile_menu',
					'default'     => 0,
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Custom', 'reign' ),
						'off' => esc_html__( 'Default', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'image',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_header_mobile_menu_logo_divider',
					'section'  => 'reign_header_mobile_menu',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'slider',
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

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings' => 'reign_header_mobile_logo_size_divider',
					'section'  => 'reign_header_mobile_menu',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			// BuddyNext and BuddyPress both provide the message + notification header
			// icons, so they share these branches. Friends Request is BuddyPress-only
			// and is removed below when the community plugin is BuddyNext.
			$is_community = class_exists( 'BuddyPress' ) || defined( 'BUDDYNEXT_VERSION' );
			$has_store    = class_exists( 'WooCommerce' ) || class_exists( 'Easy_Digital_Downloads' );

			if ( $is_community && $has_store ) {

				$mobile_icons_choices = array(
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

				$mobile_icons_choices = array(
					'search'        => esc_html__( 'Search', 'reign' ),
					'cart'          => esc_html__( 'Cart', 'reign' ),
					'user-menu'     => esc_html__( 'User Menu', 'reign' ),
					'login'         => esc_html__( 'Login', 'reign' ),
					'register-menu' => esc_html__( 'Register', 'reign' ),
				);

			} elseif ( $is_community ) {

				$mobile_icons_choices = array(
					'search'          => esc_html__( 'Search', 'reign' ),
					'friends-request' => esc_html__( 'Friends Request', 'reign' ),
					'message'         => esc_html__( 'Message', 'reign' ),
					'notification'    => esc_html__( 'Notification', 'reign' ),
					'user-menu'       => esc_html__( 'User Menu', 'reign' ),
					'login'           => esc_html__( 'Login', 'reign' ),
					'register-menu'   => esc_html__( 'Register', 'reign' ),
				);

			} else {

				$mobile_icons_choices = array(
					'search'        => esc_html__( 'Search', 'reign' ),
					'user-menu'     => esc_html__( 'User Menu', 'reign' ),
					'login'         => esc_html__( 'Login', 'reign' ),
					'register-menu' => esc_html__( 'Register', 'reign' ),
				);

			}

			// Friends Request is BuddyPress-only; BuddyNext has no equivalent, so
			// hide that extra control under BuddyNext.
			if ( ! class_exists( 'BuddyPress' ) ) {
				unset( $mobile_icons_choices['friends-request'] );
			}

			\Reign\Customizer_Framework\Field::add( 'sortable',
				array(
					'settings'    => 'reign_mobile_header_icons_set',
					'label'       => esc_html__( 'Mobile Header Icons Options', 'reign' ),
					'description' => '',
					'section'     => 'reign_header_mobile_menu',
					'priority'    => 10,
					'default'     => $default_value_set['reign_mobile_header_icons_set'],
					'choices'     => $mobile_icons_choices,
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Header_Mobile_Menu_Fields.
 *
 * @return Reign_Customizer_Header_Mobile_Menu_Fields
 */
Reign_Customizer_Header_Mobile_Menu_Fields::instance();

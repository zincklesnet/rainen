<?php
/**
 * Reign Kirki BuddyPress
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_BuddyPress' ) ) :

	/**
	 * @class Reign_Kirki_BuddyPress
	 */
	class Reign_Kirki_BuddyPress {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_BuddyPress
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_BuddyPress Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_BuddyPress is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_BuddyPress - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_BuddyPress Constructor.
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

			new \Kirki\Panel(
				'reign_buddypress_panel',
				array(
					'priority'    => 140,
					'title'       => __( 'Community Settings', 'reign' ),
					'description' => '',
				)
			);

			new \Kirki\Section(
				'reign_buddypress_general_section',
				array(
					'title'       => esc_html__( 'General Setting', 'reign' ),
					'priority'    => 10,
					'description' => '',
					'panel'       => 'reign_buddypress_panel',
				)
			);

			if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) || class_exists( 'BP_Classic' ) ) {

				new \Kirki\Section(
					'reign_activity_directory_settings',
					array(
						'title'       => __( 'Activity Directory', 'reign' ),
						'priority'    => 10,
						'panel'       => 'reign_buddypress_panel',
						'description' => '',
					)
				);

				new \Kirki\Section(
					'reign_members_directory_settings',
					array(
						'title'       => __( 'Members Directory', 'reign' ),
						'priority'    => 10,
						'panel'       => 'reign_buddypress_panel',
						'description' => '',
					)
				);

				new \Kirki\Section(
					'reign_groups_directory_settings',
					array(
						'title'       => __( 'Groups Directory', 'reign' ),
						'priority'    => 10,
						'panel'       => 'reign_buddypress_panel',
						'description' => '',
					)
				);
			}

			new \Kirki\Section(
				'reign_member_single_settings',
				array(
					'title'       => __( 'Member Single', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_buddypress_panel',
					'description' => '',
				)
			);

			new \Kirki\Section(
				'reign_group_single_settings',
				array(
					'title'       => __( 'Group Single', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_buddypress_panel',
					'description' => '',
				)
			);

			new \Kirki\Section(
				'reign_user_nav_view_settings',
				array(
					'title'       => __( 'Main Navigation', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_buddypress_panel',
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
					'settings'    => 'reign_buddypress_avatar_style',
					'label'       => esc_html__( 'Avatar Style', 'reign' ),
					'description' => esc_html__( 'Set the round style for member and group avatars.', 'reign' ),
					'section'     => 'reign_buddypress_general_section',
					'default'     => '',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Yes', 'reign' ),
						'off' => esc_html__( 'No', 'reign' ),
					),
				)
			);

			if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) || class_exists( 'BP_Classic' ) ) {
				new \Kirki\Field\Radio_Image(
					array(
						'settings'    => 'reign_activity_directory_sidebar_layout',
						'label'       => esc_html__( 'Sidebar Layout', 'reign' ),
						'description' => esc_html__( 'Choose a layout to display for all single post pages.', 'reign' ),
						'section'     => 'reign_activity_directory_settings',
						'default'     => 'right_sidebar',
						'priority'    => 10,
						'choices'     => array(
							'left_sidebar'  => REIGN_THEME_URI . '/lib/images/sidebar-left.jpg',
							'right_sidebar' => REIGN_THEME_URI . '/lib/images/sidebar-right.jpg',
							'both_sidebar'  => REIGN_THEME_URI . '/lib/images/sidebar-both.jpg',
							'full_width'    => REIGN_THEME_URI . '/lib/images/sidebar-none.jpg',
						),
					)
				);

				new \Kirki\Field\Radio_Image(
					array(
						'settings'    => 'reign_members_directory_sidebar_layout',
						'label'       => esc_html__( 'Sidebar Layout', 'reign' ),
						'description' => esc_html__( 'Choose a layout to display for all single post pages.', 'reign' ),
						'section'     => 'reign_members_directory_settings',
						'default'     => 'right_sidebar',
						'priority'    => 10,
						'choices'     => array(
							'left_sidebar'  => REIGN_THEME_URI . '/lib/images/sidebar-left.jpg',
							'right_sidebar' => REIGN_THEME_URI . '/lib/images/sidebar-right.jpg',
							'both_sidebar'  => REIGN_THEME_URI . '/lib/images/sidebar-both.jpg',
							'full_width'    => REIGN_THEME_URI . '/lib/images/sidebar-none.jpg',
						),
					)
				);

				new \Kirki\Field\Radio_Image(
					array(
						'settings'    => 'reign_groups_directory_sidebar_layout',
						'label'       => esc_html__( 'Sidebar Layout', 'reign' ),
						'description' => esc_html__( 'Choose a layout to display for all single post pages.', 'reign' ),
						'section'     => 'reign_groups_directory_settings',
						'default'     => 'right_sidebar',
						'priority'    => 10,
						'choices'     => array(
							'left_sidebar'  => REIGN_THEME_URI . '/lib/images/sidebar-left.jpg',
							'right_sidebar' => REIGN_THEME_URI . '/lib/images/sidebar-right.jpg',
							'both_sidebar'  => REIGN_THEME_URI . '/lib/images/sidebar-both.jpg',
							'full_width'    => REIGN_THEME_URI . '/lib/images/sidebar-none.jpg',
						),
					)
				);
			}

			new \Kirki\Field\Select(
				array(
					'settings' => 'buddypress_single_member_nav_style',
					'label'    => esc_html__( 'Single Member Navigation Style', 'reign' ),
					'section'  => 'reign_member_single_settings',
					'default'  => 'iconic',
					'choices'  => array(
						'default' => esc_attr__( 'Default', 'reign' ),
						'iconic'  => esc_attr__( 'Icon + Label', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Select(
				array(
					'settings' => 'buddypress_single_member_nav_style',
					'label'    => esc_html__( 'Single Member Navigation Style', 'reign' ),
					'section'  => 'reign_member_single_settings',
					'default'  => 'iconic',
					'choices'  => array(
						'default' => esc_attr__( 'Default', 'reign' ),
						'iconic'  => esc_attr__( 'Icon + Label', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Select(
				array(
					'settings' => 'buddypress_single_group_nav_style',
					'label'    => esc_html__( 'Single Group Navigation Style', 'reign' ),
					'section'  => 'reign_group_single_settings',
					'default'  => 'iconic',
					'choices'  => array(
						'default' => esc_attr__( 'Default', 'reign' ),
						'iconic'  => esc_attr__( 'Icon + Label', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Radio(
				array(
					'settings'    => 'buddypress_main_nav_view_style',
					'label'       => esc_html__( 'Navigation View Style', 'reign' ),
					'description' => esc_html__( 'This setting applicable only horizontal navigation (main) in single member and group.', 'reign' ),
					'section'     => 'reign_user_nav_view_settings',
					'default'     => 'text_icon',
					'choices'     => array(
						'swipe'     => esc_attr__( 'Swipe', 'reign' ),
						'text_icon' => esc_attr__( 'Text + Icons (Swipe)', 'reign' ),
						'more'      => esc_attr__( 'More', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Radio(
				array(
					'settings'    => 'buddypress_main_subnav_view_style',
					'label'       => esc_html__( 'Submenu Navigation View Style', 'reign' ),
					'description' => esc_html__( 'This setting can be applied to both single member and group submenu navigation. (Swipe/More style will be applied only for small screens.)', 'reign' ),
					'section'     => 'reign_user_nav_view_settings',
					'default'     => 'default',
					'choices'     => array(
						'default' => esc_attr__( 'Default', 'reign' ),
						'swipe'   => esc_attr__( 'Swipe', 'reign' ),
						'more'    => esc_attr__( 'More', 'reign' ),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_BuddyPress.
 *
 * @return Reign_Kirki_BuddyPress
 */
Reign_Kirki_BuddyPress::instance();

<?php
/**
 * Reign Customizer BuddyPress Fields
 *
 * Ported from `lib/kirki-addon/options/buddypress/class-reign-kirki-buddypress.php`.
 * Kirki removal — Phase 1 atomic sweep. Args arrays preserved verbatim.
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_BuddyPress_Fields' ) ) :

	/**
	 * @class Reign_Customizer_BuddyPress_Fields
	 */
	class Reign_Customizer_BuddyPress_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_BuddyPress_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_BuddyPress_Fields Instance.
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_BuddyPress_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor.
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

			\Reign\Customizer_Framework\Panel::add(
				'reign_buddypress_panel',
				array(
					'priority'    => 140,
					'title'       => __( 'Community Settings', 'reign' ),
					'description' => esc_html__( 'Style your BuddyPress community - directories, member and group pages, and navigation. Community colors follow your palette under Color Options.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_buddypress_general_section',
				array(
					'title'       => esc_html__( 'General', 'reign' ),
					'priority'    => 10,
					'description' => esc_html__( 'Community-wide options such as avatar shape.', 'reign' ),
					'panel'       => 'reign_buddypress_panel',
				)
			);

			if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) || class_exists( 'BP_Classic' ) ) {

				\Reign\Customizer_Framework\Section::add(
					'reign_activity_directory_settings',
					array(
						'title'       => __( 'Activity Directory', 'reign' ),
						'priority'    => 20,
						'panel'       => 'reign_buddypress_panel',
						'description' => esc_html__( 'Sidebar layout for the activity directory.', 'reign' ),
					)
				);

				\Reign\Customizer_Framework\Section::add(
					'reign_members_directory_settings',
					array(
						'title'       => __( 'Members Directory', 'reign' ),
						'priority'    => 30,
						'panel'       => 'reign_buddypress_panel',
						'description' => esc_html__( 'Sidebar layout for the members directory.', 'reign' ),
					)
				);

				\Reign\Customizer_Framework\Section::add(
					'reign_groups_directory_settings',
					array(
						'title'       => __( 'Groups Directory', 'reign' ),
						'priority'    => 40,
						'panel'       => 'reign_buddypress_panel',
						'description' => esc_html__( 'Sidebar layout for the groups directory.', 'reign' ),
					)
				);
			}

			\Reign\Customizer_Framework\Section::add(
				'reign_member_single_settings',
				array(
					'title'       => __( 'Member Profile', 'reign' ),
					'priority'    => 50,
					'panel'       => 'reign_buddypress_panel',
					'description' => esc_html__( 'Navigation style on a single member profile.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_group_single_settings',
				array(
					'title'       => __( 'Single Group', 'reign' ),
					'priority'    => 60,
					'panel'       => 'reign_buddypress_panel',
					'description' => esc_html__( 'Navigation style on a single group page.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_user_nav_view_settings',
				array(
					'title'       => __( 'Navigation', 'reign' ),
					'priority'    => 70,
					'panel'       => 'reign_buddypress_panel',
					'description' => esc_html__( 'How the member and group navigation menus display.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_get_customizer_default_value_set();

			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'    => 'reign_buddypress_avatar_style',
					'label'       => esc_html__( 'Round Avatars', 'reign' ),
					'description' => esc_html__( 'Use circular avatars for members and groups.', 'reign' ),
					'section'     => 'reign_buddypress_general_section',
					'default'     => 'off',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Yes', 'reign' ),
						'off' => esc_html__( 'No', 'reign' ),
					),
				)
			);

			if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) || class_exists( 'BP_Classic' ) ) {
				\Reign\Customizer_Framework\Field::add(
					'radio_image',
					array(
						'settings'    => 'reign_activity_directory_sidebar_layout',
						'label'       => esc_html__( 'Sidebar Layout', 'reign' ),
						'description' => esc_html__( 'Choose the sidebar layout for the activity directory.', 'reign' ),
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

				\Reign\Customizer_Framework\Field::add(
					'radio_image',
					array(
						'settings'    => 'reign_members_directory_sidebar_layout',
						'label'       => esc_html__( 'Sidebar Layout', 'reign' ),
						'description' => esc_html__( 'Choose the sidebar layout for the members directory.', 'reign' ),
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

				\Reign\Customizer_Framework\Field::add(
					'radio_image',
					array(
						'settings'    => 'reign_groups_directory_sidebar_layout',
						'label'       => esc_html__( 'Sidebar Layout', 'reign' ),
						'description' => esc_html__( 'Choose the sidebar layout for the groups directory.', 'reign' ),
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

			\Reign\Customizer_Framework\Field::add(
				'select',
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

			\Reign\Customizer_Framework\Field::add(
				'select',
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

			\Reign\Customizer_Framework\Field::add(
				'radio',
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

			\Reign\Customizer_Framework\Field::add(
				'radio',
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
 * Main instance of Reign_Customizer_BuddyPress_Fields.
 *
 * @return Reign_Customizer_BuddyPress_Fields
 */
Reign_Customizer_BuddyPress_Fields::instance();

<?php
/**
 * Reign Kirki Post Types Support
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Post_Types_Support' ) ) :

	/**
	 * @class Reign_Kirki_Post_Types_Support
	 */
	class Reign_Kirki_Post_Types_Support {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Post_Types_Support
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Post_Types_Support Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Post_Types_Support is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Post_Types_Support - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Post_Types_Support Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'add_panels_and_sections' ) );
			add_action( 'customize_register', array( $this, 'remove_sections_panels' ), 20 );
			add_action( 'init', array( $this, 'add_fields' ) );
		}

		/**
		 * Get post types to support
		 */
		public function get_post_types_to_support() {
			global $wp_query;

			$post_types = array(
				array(
					'slug'        => 'post',
					'name'        => esc_html__( 'Blog', 'reign' ),
					'has_archive' => true,
					'is_single'   => true,
				),
				array(
					'slug'        => 'page',
					'name'        => esc_html__( 'Page', 'reign' ),
					'has_archive' => false,
					'is_single'   => true,
				),
			);

			if ( class_exists( 'bbPress' ) ) {
				$post_types[] = array(
					'slug'        => bbp_get_forum_post_type(),
					'name'        => esc_html__( 'Forums', 'reign' ),
					'has_archive' => true,
					'is_single'   => true,
				);
				$post_types[] = array(
					'slug'        => bbp_get_topic_post_type(),
					'name'        => esc_html__( 'Topics', 'reign' ),
					'has_archive' => false,
					'is_single'   => true,
				);
			}

			$args = array(
				'public'              => true,
				'_builtin'            => false,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'exclude_from_search' => false,
			);

			$output   = 'names'; // 'names' or 'objects' (default: 'names')
			$operator = 'and'; // 'and' or 'or' (default: 'and')

			$custom_post_types = get_post_types( $args, $output, $operator );

			if ( is_plugin_active( 'reign-tutorlms-addon/reign-tutorlms-addon.php' ) ) {
				$exclude_post_type = apply_filters( 'reign_exclude_post_types_to_support', array( 'e-landing-page', 'courses', 'announcements', 'withdrawals', 'e-floating-buttons', 'at_biz_dir' ) );
			} else {
				$exclude_post_type = apply_filters( 'reign_exclude_post_types_to_support', array( 'e-landing-page', 'announcements', 'withdrawals', 'e-floating-buttons', 'at_biz_dir' ) );
			}

			$temp        = array();
			$has_archive = false;
			if ( is_array( $custom_post_types ) && ! empty( $custom_post_types ) ) {
				foreach ( $custom_post_types as $key => $custom_post_type ) {
					if ( in_array( $custom_post_type, $exclude_post_type ) ) {
						continue;
					}

					$post_type_data = get_post_type_object( $custom_post_type );
					if ( false !== $post_type_data->has_archive ) {
						$has_archive = true;
					}

					if ( 'sfwd-courses' === $custom_post_type || 'sfwd-lessons' === $custom_post_type || 'sfwd-topic' === $custom_post_type || 'sfwd-quiz' === $custom_post_type ) {

						$custom_post_type = 'sfwd-courses' === $custom_post_type ? learndash_get_custom_label( 'course' ) : $custom_post_type;

						$custom_post_type = 'sfwd-lessons' === $custom_post_type ? learndash_get_custom_label( 'lessons' ) : $custom_post_type;

						$custom_post_type = 'sfwd-topic' === $custom_post_type ? learndash_get_custom_label( 'topic' ) : $custom_post_type;

						$custom_post_type = 'sfwd-quiz' === $custom_post_type ? learndash_get_custom_label( 'quiz' ) : $custom_post_type;

						$sfwd_has_archive = learndash_post_type_has_archive( $custom_post_type );
						if ( $sfwd_has_archive ) {
							$has_archive = true;
						} else {
							$has_archive = false;
						}
					}

					if ( 'job_listing' === $custom_post_type ) {
						$custom_post_type = esc_html__( 'Jobs', 'reign' );
					}
					if ( 'tribe_events' === $custom_post_type ) {
						$custom_post_type = esc_html__( 'Events', 'reign' );
					}

					$temp[] = array(
						'slug'        => $key,
						'name'        => ucwords( preg_replace( '/[_]+/', ' ', $custom_post_type ) ),
						'has_archive' => $has_archive,
						'is_single'   => apply_filters( 'reign_customizer_support_post_type_is_single', true ),
					);
				}

				$post_types = array_merge( $post_types, $temp );
			}

			$post_types = apply_filters( 'reign_customizer_supported_post_types', $post_types );

			return $post_types;
		}

		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {

			$post_types = $this->get_post_types_to_support();

			foreach ( $post_types as $post_type ) {
				new \Kirki\Panel(
					'reign_post_types_panel',
					array(
						'priority'    => 140,
						'title'       => esc_html__( 'Layout Settings', 'reign' ),
						'description' => 'reign',
						'type'        => 'nested',
					)
				);

				new \Kirki\Panel(
					'reign_' . $post_type['slug'] . '_panel',
					array(
						'priority'    => 100,
						'title'       => $post_type['name'],
						'description' => '',
						'panel'       => 'reign_post_types_panel',
					)
				);

				if ( isset( $post_type['has_archive'] ) && false !== $post_type['has_archive'] ) {
					new \Kirki\Section(
						'reign_' . $post_type['slug'] . '_archive',
						array(
							'title'       => esc_html__( 'Archive', 'reign' ),
							'priority'    => 10,
							'panel'       => 'reign_' . $post_type['slug'] . '_panel',
							'description' => '',
						)
					);
				}

				if ( isset( $post_type['is_single'] ) && false !== $post_type['is_single'] ) {
					new \Kirki\Section(
						'reign_' . $post_type['slug'] . '_single',
						array(
							'title'       => esc_html__( 'Single', 'reign' ),
							'priority'    => 10,
							'panel'       => 'reign_' . $post_type['slug'] . '_panel',
							'description' => '',
						)
					);
				}

				if ( 'page' === $post_type['slug'] ) {
					new \Kirki\Section(
						'reign_page_search',
						array(
							'title'       => esc_html__( 'Search', 'reign' ),
							'priority'    => 10,
							'panel'       => 'reign_page_panel',
							'description' => '',
						)
					);
				}
			}
		}

		/**
		 * Remove sections panels
		 *
		 * @param WP_Customize_Manager $wp_customize The customizer manager instance.
		 */
		public function remove_sections_panels( $wp_customize ) {
			$wp_customize->remove_section( 'reign_event_organizer_archive' );
			$wp_customize->remove_section( 'reign_event_venue_archive' );
			$wp_customize->remove_section( 'reign_fluent-products_archive' );
			$wp_customize->remove_section( 'reign_sfwd-courses_single' );
			$wp_customize->remove_section( 'reign_groups_single' );

			$remove_section = apply_filters( 'reign_remove_background_image_section', true );
			if ( $remove_section ) {
				$wp_customize->remove_section( 'background_image' );
			}
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$post_types = $this->get_post_types_to_support();

			global $wp_registered_sidebars;
			$widgets_areas    = array( '0' => __( 'Default', 'reign' ) );
			$get_widget_areas = $wp_registered_sidebars;
			if ( ! empty( $get_widget_areas ) ) {
				foreach ( $get_widget_areas as $widget_area ) {
					$name = isset( $widget_area['name'] ) ? $widget_area['name'] : '';
					$id   = isset( $widget_area['id'] ) ? $widget_area['id'] : '';
					if ( $name && $id ) {
						$widgets_areas[ $id ] = $name;
					}
				}
			}

			foreach ( $post_types as $post_type ) {
				new \Kirki\Field\Radio_Image(
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_archive_layout',
						'label'       => esc_html__( 'Layout', 'reign' ),
						'description' => esc_html__( 'Choose a layout for all archive pages.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_archive',
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

				new \Kirki\Pro\Field\Divider(
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_archive_layout_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_archive',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				new \Kirki\Field\Checkbox_Switch(
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_archive_header_enable',
						'label'       => esc_html__( 'Hide Archive Page Sub Header', 'reign' ),
						'description' => esc_html__( 'Hide page sub header for this post type.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_archive',
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
						'settings' => 'reign_' . $post_type['slug'] . '_archive_header_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_archive',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				new \Kirki\Field\Checkbox_Switch(
					array(
						'settings'        => 'reign_' . $post_type['slug'] . '_archive_enable_header_image',
						'label'           => esc_html__( 'Enable Sub Header Image', 'reign' ),
						'description'     => '',
						'section'         => 'reign_' . $post_type['slug'] . '_archive',
						'default'         => 1,
						'priority'        => 10,
						'choices'         => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_archive_header_enable',
								'operator' => '===',
								'value'    => false,
							),
						),
					)
				);

				new \Kirki\Field\Image(
					array(
						'settings'        => 'reign_' . $post_type['slug'] . '_archive_header_image',
						'label'           => esc_html__( 'Blog Sub Header Image', 'reign' ),
						'description'     => esc_html__( 'Set page sub header image for blog page.', 'reign' ),
						'section'         => 'reign_' . $post_type['slug'] . '_archive',
						'priority'        => 10,
						'default'         => reign_get_default_page_header_image(),
						'active_callback' => array(
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_archive_header_enable',
								'operator' => '===',
								'value'    => false,
							),
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_archive_enable_header_image',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				);

				new \Kirki\Pro\Field\Divider(
					array(
						'settings'        => 'reign_' . $post_type['slug'] . '_archive_header_image_divider',
						'section'         => 'reign_' . $post_type['slug'] . '_archive',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_archive_header_enable',
								'operator' => '===',
								'value'    => false,
							),
						),
					)
				);

				new \Kirki\Field\Select(
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_archive_left_sidebar',
						'label'       => esc_html__( 'Left Sidebar', 'reign' ),
						'description' => esc_html__( 'Set left sidebar.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_archive',
						'priority'    => 10,
						'default'     => '0',
						'priority'    => 10,
						'choices'     => $widgets_areas,
					)
				);

				new \Kirki\Pro\Field\Divider(
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_archive_left_sidebar_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_archive',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				new \Kirki\Field\Select(
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_archive_right_sidebar',
						'label'       => esc_html__( 'Right Sidebar', 'reign' ),
						'description' => esc_html__( 'Set right sidebar.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_archive',
						'priority'    => 10,
						'default'     => '0',
						'priority'    => 10,
						'choices'     => $widgets_areas,
					)
				);

				new \Kirki\Field\Radio_Image(
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_single_layout',
						'label'       => esc_html__( 'Layout', 'reign' ),
						'description' => esc_html__( 'Choose a layout to display for all single post pages.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_single',
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

				new \Kirki\Pro\Field\Divider(
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_single_layout_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_single',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				new \Kirki\Field\Radio_Image(
					array(
						'settings'    => 'reign_search_page_layout',
						'label'       => esc_html__( 'Layout', 'reign' ),
						'description' => esc_html__( 'Choose a layout to display for all search post pages.', 'reign' ),
						'section'     => 'reign_page_search',
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

				new \Kirki\Pro\Field\Divider(
					array(
						'settings' => 'reign_search_page_layout_divider',
						'section'  => 'reign_page_search',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				if ( 'page' !== $post_type['slug'] ) {
					new \Kirki\Field\Checkbox_Switch(
						array(
							'settings'    => 'reign_' . $post_type['slug'] . '_single_header_enable',
							'label'       => sprintf( esc_html__( 'Hide %s Sub Header', 'reign' ), $post_type['name'] ),
							'description' => esc_html__( 'Hide page sub header for this post type.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_single',
							// 'default'   => ( isset( $post_type['slug'] ) && $post_type['slug'] != 'post' ) ? 'on' : 0,
							'default'     => 1,
							'priority'    => 10,
							'choices'     => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
						)
					);
				} else {
					new \Kirki\Field\Checkbox_Switch(
						array(
							'settings'    => 'reign_' . $post_type['slug'] . '_single_header_enable',
							'label'       => esc_html__( 'Hide Page Sub Header', 'reign' ),
							'description' => esc_html__( 'Hide page sub headere.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_single',
							'default'     => 1,
							'priority'    => 10,
							'choices'     => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
						)
					);
				}

				new \Kirki\Pro\Field\Divider(
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_single_header_enable_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_single',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				if ( 'page' == $post_type['slug'] ) {
					new \Kirki\Field\Checkbox_Switch(
						array(
							'settings'        => 'reign_' . $post_type['slug'] . '_single_pagetitle_enable',
							'label'           => sprintf( esc_html__( 'Hide %s Title', 'reign' ), $post_type['name'] ),
							'description'     => esc_html__( 'Hide page title for this post type.', 'reign' ),
							'section'         => 'reign_' . $post_type['slug'] . '_single',
							'default'         => 0,
							'priority'        => 10,
							'choices'         => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
							'active_callback' => array(
								array(
									'setting'  => 'reign_' . $post_type['slug'] . '_single_header_enable',
									'operator' => '===',
									'value'    => true,
								),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings'        => 'reign_' . $post_type['slug'] . '_single_pagetitle_enable_divider',
							'section'         => 'reign_' . $post_type['slug'] . '_single',
							'choices'         => array(
								'color' => '#dcdcde',
							),
							'active_callback' => array(
								array(
									'setting'  => 'reign_' . $post_type['slug'] . '_single_header_enable',
									'operator' => '===',
									'value'    => true,
								),
							),
						)
					);
				}

				new \Kirki\Field\Checkbox_Switch(
					array(
						'settings'        => 'reign_' . $post_type['slug'] . '_single_enable_header_image',
						'label'           => esc_html__( 'Enable Sub Header Image', 'reign' ),
						'description'     => '',
						'section'         => 'reign_' . $post_type['slug'] . '_single',
						'default'         => 1,
						'priority'        => 10,
						'choices'         => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_header_enable',
								'operator' => '===',
								'value'    => false,
							),
						),
					)
				);

				new \Kirki\Field\Image(
					array(
						'settings'        => 'reign_' . $post_type['slug'] . '_single_header_image',
						'label'           => esc_html__( 'Page Sub Header Image', 'reign' ),
						'description'     => esc_html__( 'Set page sub header image for single post page.', 'reign' ),
						'section'         => 'reign_' . $post_type['slug'] . '_single',
						'priority'        => 10,
						'default'         => reign_get_default_page_header_image(),
						'active_callback' => array(
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_header_enable',
								'operator' => '===',
								'value'    => false,
							),
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_enable_header_image',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				);

				new \Kirki\Pro\Field\Divider(
					array(
						'settings'        => 'reign_' . $post_type['slug'] . '_single_header_image_divider',
						'section'         => 'reign_' . $post_type['slug'] . '_single',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_header_enable',
								'operator' => '===',
								'value'    => false,
							),
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_enable_header_image',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				);

				new \Kirki\Field\Checkbox_Switch(
					array(
						'settings'        => 'reign_single_' . $post_type['slug'] . '_switch_header_image',
						'label'           => esc_html__( 'Switch Sub Header Image With Featured Image', 'reign' ),
						'description'     => esc_html__( 'This will show post featured image on sub header section and featured image will be removed from post content.', 'reign' ),
						'section'         => 'reign_' . $post_type['slug'] . '_single',
						'default'         => 0,
						'priority'        => 10,
						'choices'         => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_header_enable',
								'operator' => '===',
								'value'    => false,
							),
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_enable_header_image',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				);

				new \Kirki\Pro\Field\Divider(
					array(
						'settings'        => 'reign_' . $post_type['slug'] . '_switch_header_image_divider',
						'section'         => 'reign_' . $post_type['slug'] . '_single',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_header_enable',
								'operator' => '===',
								'value'    => false,
							),
							array(
								'setting'  => 'reign_' . $post_type['slug'] . '_single_enable_header_image',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				);

				if ( 'post' === $post_type['slug'] ) {

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_blog_list_layout_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					new \Kirki\Field\Select(
						array(
							'settings'    => 'reign_blog_list_layout',
							'label'       => esc_html__( 'Blog Listing Layout', 'reign' ),
							'description' => esc_html__( 'Select your log listing layout here. We have option to choose from 4 different views.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_archive',
							'default'     => 'default-view',
							'priority'    => 10,
							'choices'     => array(
								'default-view'   => esc_html__( 'Default View', 'reign' ),
								'thumbnail-view' => esc_html__( 'Thumbnail View', 'reign' ),
								'wb-grid-view'   => esc_html__( 'Grid View', 'reign' ),
								'masonry-view'   => esc_html__( 'Masonry View', 'reign' ),
							),
						)
					);

					new \Kirki\Field\Number(
						array(
							'settings'        => 'reign_blog_per_row',
							'label'           => esc_html__( 'Blogs Per Row', 'reign' ),
							'description'     => '',
							'section'         => 'reign_' . $post_type['slug'] . '_archive',
							'default'         => '3',
							'priority'        => 10,
							'active_callback' => array(
								array(
									'setting'  => 'reign_blog_list_layout',
									'operator' => 'contains',
									'value'    => array( 'wb-grid-view', 'masonry-view' ),
								),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_blog_per_row_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					new \Kirki\Field\Checkbox_Switch(
						array(
							'settings'    => 'reign_advanced_excerpt',
							'label'       => esc_html__( 'Advance Excerpt', 'reign' ),
							'description' => esc_html__( 'Enable custom excerpt length.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_archive',
							'default'     => 1,
							'priority'    => 10,
							'choices'     => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
						)
					);

					new \Kirki\Field\Number(
						array(
							'settings'        => 'reign_blog_excerpt_length',
							'label'           => esc_html__( 'Excerpt Length (words)', 'reign' ),
							'description'     => '',
							'section'         => 'reign_' . $post_type['slug'] . '_archive',
							'default'         => '20',
							'priority'        => 10,
							'active_callback' => array(
								array(
									'setting'  => 'reign_advanced_excerpt',
									'operator' => '===',
									'value'    => true,
								),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_advanced_excerpt_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					// new \Kirki\Field\Checkbox_Switch(
					// array(
					// 'settings'    => 'reign_single_post_switch_header_image',
					// 'label'       => esc_html__( 'Switch Header Image With Featured Image', 'reign' ),
					// 'description' => esc_html__( 'This will show post featured image on top header section and featured image will be removed from post content.', 'reign' ),
					// 'section'     => 'reign_' . $post_type['slug'] . '_single',
					// 'default'     => 0,
					// 'priority'    => 10,
					// 'choices'     => array(
					// 'on'  => esc_html__( 'Enable', 'reign' ),
					// 'off' => esc_html__( 'Disable', 'reign' ),
					// ),
					// )
					// );

					new \Kirki\Field\Select(
						array(
							'settings'    => 'reign_single_post_layout',
							'label'       => esc_html__( 'Single Post Layout', 'reign' ),
							'description' => esc_html__( 'Select single post layout.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_single',
							'default'     => 'default',
							'priority'    => 10,
							'choices'     => array(
								'default'      => esc_html__( 'Default', 'reign' ),
								'wide'         => esc_html__( 'Wide Without Sidebar', 'reign' ),
								'wide_sidebar' => esc_html__( 'Wide With Sidebar', 'reign' ),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_single_post_layout_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					new \Kirki\Field\Select(
						array(
							'settings'        => 'reign_single_post_meta_alignment',
							'label'           => esc_html__( 'Post Meta Alignment', 'reign' ),
							'description'     => esc_html__( 'Select alignment for post-meta information on single post page.', 'reign' ),
							'section'         => 'reign_' . $post_type['slug'] . '_single',
							'default'         => 'left',
							'priority'        => 10,
							'choices'         => array(
								'left'   => esc_html__( 'Left', 'reign' ),
								'center' => esc_html__( 'Center', 'reign' ),
								'right'  => esc_html__( 'Right', 'reign' ),
							),
							'active_callback' => array(
								array(
									'setting'  => 'reign_single_post_layout',
									'operator' => '===',
									'value'    => 'default',
								),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_single_post_meta_alignment_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					new \Kirki\Field\Checkbox_Switch(
						array(
							'settings'    => 'reign_author_info',
							'label'       => esc_html__( 'Author Info', 'reign' ),
							'description' => esc_html__( 'Show author info.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_single',
							'default'     => 'on',
							'priority'    => 10,
							'choices'     => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
						)
					);

					if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
						new \Kirki\Field\Checkbox_Switch(
							array(
								'settings' => 'reign_author_info_link',
								'label'    => esc_html__( 'Author Profile Link', 'reign' ),
								'section'  => 'reign_' . $post_type['slug'] . '_single',
								'default'  => 'on',
								'priority' => 10,
								'tooltip'  => esc_html__( 'Link the post author\'s name to their BuddyBoss profile. When disabled or BuddyBoss platform plugin is not active, the post author\'s name will link to an archive displaying all their posts.', 'reign' ),
								'choices'  => array(
									'on'  => esc_html__( 'Enable', 'reign' ),
									'off' => esc_html__( 'Disable', 'reign' ),
								),
							)
						);
					} else {
						new \Kirki\Field\Checkbox_Switch(
							array(
								'settings' => 'reign_author_info_link',
								'label'    => esc_html__( 'Author Profile Link', 'reign' ),
								'section'  => 'reign_' . $post_type['slug'] . '_single',
								'default'  => 'on',
								'priority' => 10,
								'tooltip'  => esc_html__( 'Link the post author\'s name to their BuddPress profile. When disabled or when the BuddyPress plugin is not active, the post author\'s name will link to an archive displaying all their posts.', 'reign' ),
								'choices'  => array(
									'on'  => esc_html__( 'Enable', 'reign' ),
									'off' => esc_html__( 'Disable', 'reign' ),
								),
							)
						);
					}

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_author_info_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					new \Kirki\Field\Checkbox_Switch(
						array(
							'settings'    => 'single_post_social_box',
							'label'       => esc_html__( 'Social Box', 'reign' ),
							'description' => esc_html__( 'Show social box.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_single',
							'default'     => 'on',
							'priority'    => 10,
							'choices'     => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
						)
					);

					new \Kirki\Field\Sortable(
						array(
							'settings'        => 'single_post_social_link',
							'label'           => esc_html__( 'Social Links', 'reign' ),
							'section'         => 'reign_' . $post_type['slug'] . '_single',
							'default'         => array(
								'facebook',
								'twitter',
							),
							'choices'         => array(
								'facebook'  => esc_html__( 'Facebook', 'reign' ),
								'twitter'   => esc_html__( 'Twitter', 'reign' ),
								'pinterest' => esc_html__( 'Pinterest', 'reign' ),
								'linkedin'  => esc_html__( 'LinkedIn', 'reign' ),
								'whatsapp'  => esc_html__( 'WhatsApp', 'reign' ),
							),
							'active_callback' => array(
								array(
									'setting'  => 'single_post_social_box',
									'operator' => '==',
									'value'    => true,
								),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings'        => 'single_post_social_link_divider',
							'section'         => 'reign_' . $post_type['slug'] . '_single',
							'choices'         => array(
								'color' => '#dcdcde',
							),
							'active_callback' => array(
								array(
									'setting'  => 'single_post_social_box',
									'operator' => '==',
									'value'    => true,
								),
							),
						)
					);

					new \Kirki\Field\Checkbox_Switch(
						array(
							'settings'    => 'reign_show_related_post',
							'label'       => esc_html__( 'Related Post', 'reign' ),
							'description' => esc_html__( 'Show related posts.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_single',
							'default'     => 0,
							'priority'    => 10,
							'choices'     => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
						)
					);

					new \Kirki\Field\Text(
						array(
							'settings'        => 'reign_related_post_title',
							'label'           => esc_html__( 'Related Post Title', 'reign' ),
							'description'     => esc_html__( 'Set related post custom title.', 'reign' ),
							'section'         => 'reign_' . $post_type['slug'] . '_single',
							'default'         => esc_html__( 'Related Posts', 'reign' ),
							'priority'        => 10,
							'active_callback' => array(
								array(
									'setting'  => 'reign_show_related_post',
									'operator' => '===',
									'value'    => true,
								),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_show_related_post_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					new \Kirki\Field\Checkbox_Switch(
						array(
							'settings'    => 'reign_single_post_navigation',
							'label'       => esc_html__( 'Post Navigation', 'reign' ),
							'description' => esc_html__( 'Enable/Disable post navigation.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_single',
							'default'     => 'on',
							'priority'    => 10,
							'choices'     => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_single_post_navigation_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					new \Kirki\Field\Select(
						array(
							'settings'    => 'reign_blog_list_pagination',
							'label'       => esc_html__( 'Blog Listing Pagination Type', 'reign' ),
							'description' => esc_html__( 'Set pagination type on post page.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_archive',
							'priority'    => 10,
							'default'     => 'reign_blog_number_pagination',
							'choices'     => array(
								'reign_blog_number_pagination' => esc_html__( 'Numeric Pagination', 'reign' ),
								'reign_blog_infinite_scroll_pagination' => esc_html__( 'Infinite Scroll Pagination', 'reign' ),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'reign_blog_list_pagination_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					new \Kirki\Field\Select(
						array(
							'settings'    => 'rg_blog_category_color',
							'label'       => esc_html__( 'Blog Listing Category Label Color', 'reign' ),
							'description' => esc_html__( 'Set blog listing category label color default or random.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_archive',
							'priority'    => 10,
							'default'     => 'cat_color_random',
							'choices'     => array(
								'cat_color_default' => esc_html__( 'Default Color', 'reign' ),
								'cat_color_random'  => esc_html__( 'Random Color', 'reign' ),
							),
						)
					);

					// new \Kirki\Pro\Field\Divider(
					// array(
					// 'settings' => 'rg_blog_category_color_divider',
					// 'section'  => 'reign_' . $post_type['slug'] . '_archive',
					// 'choices'  => array(
					// 'color' => '#dcdcde',
					// ),
					// )
					// );
				}

				new \Kirki\Field\Select(
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_single_left_sidebar',
						'label'       => esc_html__( 'Left Sidebar', 'reign' ),
						'description' => esc_html__( 'Set left sidebar.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_single',
						'priority'    => 10,
						'default'     => '0',
						'priority'    => 10,
						'choices'     => $widgets_areas,
					)
				);

				new \Kirki\Pro\Field\Divider(
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_single_left_sidebar_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_single',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				new \Kirki\Field\Select(
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_single_right_sidebar',
						'label'       => esc_html__( 'Right Sidebar', 'reign' ),
						'description' => esc_html__( 'Set right sidebar.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_single',
						'priority'    => 10,
						'default'     => '0',
						'priority'    => 10,
						'choices'     => $widgets_areas,
					)
				);

				new \Kirki\Field\Checkbox_Switch(
					array(
						'settings'    => 'reign_search_header_enable',
						'label'       => esc_html__( 'Hide Page Sub Header', 'reign' ),
						'description' => esc_html__( 'Hide page sub header for this post type.', 'reign' ),
						'section'     => 'reign_page_search',
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
						'settings' => 'reign_search_header_enable_divider',
						'section'  => 'reign_page_search',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				new \Kirki\Field\Checkbox_Switch(
					array(
						'settings'        => 'reign_search_enable_header_image',
						'label'           => esc_html__( 'Enable Header Image', 'reign' ),
						'description'     => '',
						'section'         => 'reign_page_search',
						'default'         => 1,
						'priority'        => 10,
						'choices'         => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_search_header_enable',
								'operator' => '===',
								'value'    => false,
							),
						),
					)
				);

				new \Kirki\Field\Image(
					array(
						'settings'        => 'reign_search_header_image',
						'label'           => esc_html__( 'Page Header Image', 'reign' ),
						'description'     => esc_html__( 'Set page header image for single post page.', 'reign' ),
						'section'         => 'reign_page_search',
						'priority'        => 10,
						'default'         => reign_get_default_page_header_image(),
						'active_callback' => array(
							array(
								'setting'  => 'reign_search_header_enable',
								'operator' => '===',
								'value'    => false,
							),
							array(
								'setting'  => 'reign_search_enable_header_image',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				);

				new \Kirki\Pro\Field\Divider(
					array(
						'settings'        => 'reign_search_header_image_divider',
						'section'         => 'reign_page_search',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_search_header_enable',
								'operator' => '===',
								'value'    => false,
							),
							array(
								'setting'  => 'reign_search_enable_header_image',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				);

				new \Kirki\Field\Select(
					array(
						'settings'    => 'reign_search_left_sidebar',
						'label'       => esc_html__( 'Left Sidebar', 'reign' ),
						'description' => esc_html__( 'Set left sidebar.', 'reign' ),
						'section'     => 'reign_page_search',
						'priority'    => 10,
						'default'     => '0',
						'priority'    => 10,
						'choices'     => $widgets_areas,
					)
				);

				new \Kirki\Pro\Field\Divider(
					array(
						'settings' => 'reign_search_left_sidebar_divider',
						'section'  => 'reign_page_search',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				new \Kirki\Field\Select(
					array(
						'settings'    => 'reign_search_right_sidebar',
						'label'       => esc_html__( 'Right Sidebar', 'reign' ),
						'description' => esc_html__( 'Set right sidebar.', 'reign' ),
						'section'     => 'reign_page_search',
						'priority'    => 10,
						'default'     => '0',
						'priority'    => 10,
						'choices'     => $widgets_areas,
					)
				);

				if ( class_exists( 'bbPress' ) ) {
					new \Kirki\Field\Radio(
						array(
							'settings'    => 'forum_archive_layout',
							'label'       => esc_html__( 'Forums Listing Layout', 'reign' ),
							'description' => esc_html__( 'Set archive page forums listing layouts.', 'reign' ),
							'section'     => 'reign_forum_archive',
							'default'     => 'default',
							'priority'    => 9,
							'choices'     => array(
								'default' => esc_html__( 'Default', 'reign' ),
								'card'    => esc_html__( 'Card', 'reign' ),
								'cover'   => esc_html__( 'Cover', 'reign' ),
							),
						)
					);

					new \Kirki\Field\Select(
						array(
							'settings'        => 'forum_archive_layout_per_row',
							'label'           => esc_html__( 'Forums Per Row', 'reign' ),
							'description'     => esc_html__( 'Set archive page forums listing per row.', 'reign' ),
							'section'         => 'reign_forum_archive',
							'default'         => 'three',
							'priority'        => 9,
							'choices'         => array(
								'two'   => esc_html__( 'Two', 'reign' ),
								'three' => esc_html__( 'Three', 'reign' ),
								'four'  => esc_html__( 'Four', 'reign' ),
							),
							'active_callback' => array(
								array(
									'setting'  => 'forum_archive_layout',
									'operator' => 'in',
									'value'    => array( 'card', 'cover' ),
								),
							),
						)
					);

					new \Kirki\Pro\Field\Divider(
						array(
							'settings' => 'forum_archive_layout_divider',
							'section'  => 'reign_forum_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
							'priority' => 9,
						)
					);
				}
			}
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Post_Types_Support.
 *
 * @return Reign_Kirki_Post_Types_Support
 */
Reign_Kirki_Post_Types_Support::instance();

<?php
/**
 * Reign Customizer Post Types Fields
 *
 * Ported from `lib/kirki-addon/options/post-types/class-reign-kirki-post-types.php`.
 * Kirki removal — Phase 1 atomic sweep. Args arrays preserved verbatim.
 *
 * Contains a runtime loop over registered CPTs that emits ~7 settings per
 * post type (the dynamic settings). Loop structure is unchanged from the
 * Kirki source; only the registration calls are rewritten to the framework.
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Post_Types_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Post_Types_Fields
	 */
	class Reign_Customizer_Post_Types_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Post_Types_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Post_Types_Fields Instance.
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Post_Types_Fields - Main instance.
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

			// Single real panel. WordPress does not support panels nested inside
			// panels, so the previous 'nested' panel + per-CPT sub-panels rendered
			// empty. Instead we register ONE panel and group all per-CPT sections
			// directly under it.
			\Reign\Customizer_Framework\Panel::add(
				'reign_post_types_panel',
				array(
					'priority'    => 140,
					'title'       => esc_html__( 'Content Layouts', 'reign' ),
					'description' => esc_html__( 'Set the layout, sidebar and sub header for each type of content - posts, pages, and any others. Site-wide defaults live in Global Layout and Sub Header.', 'reign' ),
				)
			);

			// Per-CPT section priority base. Incremented by 10 per post type so
			// sections group by post type and order archive -> single -> search.
			$panel_base = 10;

			// Sections deliberately NOT created (previously removed after-the-fact
			// via remove_section(), which did not take effect). Keyed "slug|kind".
			$skip_sections = array(
				'sfwd-courses|single'      => true,
				'groups|single'            => true,
				'event_organizer|archive'  => true,
				'event_venue|archive'      => true,
				'fluent-products|archive'  => true,
			);

			foreach ( $post_types as $post_type ) {

				if ( isset( $post_type['has_archive'] ) && false !== $post_type['has_archive']
					&& ! isset( $skip_sections[ $post_type['slug'] . '|archive' ] ) ) {
					\Reign\Customizer_Framework\Section::add(
						'reign_' . $post_type['slug'] . '_archive',
						array(
							/* translators: %s: post type label. */
							'title'       => sprintf( esc_html__( '%s: Archive', 'reign' ), $post_type['name'] ),
							'priority'    => $panel_base,
							'panel'       => 'reign_post_types_panel',
							'description' => '',
						)
					);
				}

				if ( isset( $post_type['is_single'] ) && false !== $post_type['is_single']
					&& ! isset( $skip_sections[ $post_type['slug'] . '|single' ] ) ) {
					\Reign\Customizer_Framework\Section::add(
						'reign_' . $post_type['slug'] . '_single',
						array(
							/* translators: %s: post type label. */
							'title'       => sprintf( esc_html__( '%s: Single', 'reign' ), $post_type['name'] ),
							'priority'    => $panel_base + 1,
							'panel'       => 'reign_post_types_panel',
							'description' => '',
						)
					);
				}

				if ( 'page' === $post_type['slug'] ) {
					\Reign\Customizer_Framework\Section::add(
						'reign_page_search',
						array(
							'title'       => esc_html__( 'Pages: Search', 'reign' ),
							'priority'    => $panel_base + 2,
							'panel'       => 'reign_post_types_panel',
							'description' => '',
						)
					);
				}

				$panel_base += 10;
			}
		}

		/**
		 * Remove sections panels
		 *
		 * @param WP_Customize_Manager $wp_customize The customizer manager instance.
		 */
		public function remove_sections_panels( $wp_customize ) {
			// The per-CPT section removals that previously lived here
			// (reign_event_organizer_archive, reign_event_venue_archive,
			// reign_fluent-products_archive, reign_sfwd-courses_single,
			// reign_groups_single) never took effect. Those sections are now
			// skipped at registration time in add_panels_and_sections().

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
				\Reign\Customizer_Framework\Field::add(
					'radio_image',
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

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_archive_layout_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_archive',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'switch',
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_archive_header_enable',
						'label'       => esc_html__( 'Hide Archive Page Sub Header', 'reign' ),
						'description' => esc_html__( 'Toggle ON to hide the sub header on archive pages of this post type. It is ON by default; toggle OFF to show it. The global Sub Header > Hide Sub Header option overrides this.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_archive',
						'default'     => 1,
						'priority'    => 10,
						'choices'     => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_archive_header_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_archive',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'switch',
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

				\Reign\Customizer_Framework\Field::add(
					'image',
					array(
						'settings'        => 'reign_' . $post_type['slug'] . '_archive_header_image',
						/* translators: %s: post type label. */
						'label'           => sprintf( esc_html__( '%s Sub Header Image', 'reign' ), $post_type['name'] ),
						'description'     => esc_html__( 'Optional background image for this section\'s sub header.', 'reign' ),
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

				\Reign\Customizer_Framework\Field::add(
					'custom',
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

				\Reign\Customizer_Framework\Field::add(
					'select',
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_archive_left_sidebar',
						'label'       => esc_html__( 'Left Sidebar', 'reign' ),
						'description' => esc_html__( 'Set left sidebar.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_archive',
						'priority'    => 10,
						'default'     => '0',
						'choices'     => $widgets_areas,
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_archive_left_sidebar_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_archive',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'select',
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_archive_right_sidebar',
						'label'       => esc_html__( 'Right Sidebar', 'reign' ),
						'description' => esc_html__( 'Set right sidebar.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_archive',
						'priority'    => 10,
						'default'     => '0',
						'choices'     => $widgets_areas,
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'radio_image',
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

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_single_layout_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_single',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				if ( 'page' === $post_type['slug'] ) {
					\Reign\Customizer_Framework\Field::add(
						'radio_image',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_search_page_layout_divider',
							'section'  => 'reign_page_search',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);
				}

				if ( 'page' !== $post_type['slug'] ) {
					\Reign\Customizer_Framework\Field::add(
						'switch',
						array(
							'settings'    => 'reign_' . $post_type['slug'] . '_single_header_enable',
							'label'       => sprintf( esc_html__( 'Hide %s Sub Header', 'reign' ), $post_type['name'] ),
							'description' => esc_html__( 'Toggle ON to hide the sub header on single items of this post type. It is ON by default; toggle OFF to show it. The global Sub Header > Hide Sub Header option overrides this.', 'reign' ),
							'section'     => 'reign_' . $post_type['slug'] . '_single',
							'default'     => 1,
							'priority'    => 10,
							'choices'     => array(
								'on'  => esc_html__( 'Enable', 'reign' ),
								'off' => esc_html__( 'Disable', 'reign' ),
							),
						)
					);
				} else {
					\Reign\Customizer_Framework\Field::add(
						'switch',
						array(
							'settings'    => 'reign_' . $post_type['slug'] . '_single_header_enable',
							'label'       => esc_html__( 'Hide Page Sub Header', 'reign' ),
							'description' => esc_html__( 'Toggle ON to hide the page sub header (the page-title banner). It is ON by default, so the sub header is hidden until you toggle this OFF. The global Sub Header > Hide Sub Header option, when enabled, hides it everywhere regardless of this setting.', 'reign' ),
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

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_single_header_enable_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_single',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				if ( 'page' == $post_type['slug'] ) {
					\Reign\Customizer_Framework\Field::add(
						'switch',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
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

				\Reign\Customizer_Framework\Field::add(
					'switch',
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

				\Reign\Customizer_Framework\Field::add(
					'image',
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

				\Reign\Customizer_Framework\Field::add(
					'custom',
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

				\Reign\Customizer_Framework\Field::add(
					'switch',
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

				\Reign\Customizer_Framework\Field::add(
					'custom',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_blog_list_layout_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'select',
						array(
							'settings'    => 'reign_blog_list_layout',
							'label'       => esc_html__( 'Blog Listing Layout', 'reign' ),
							'description' => esc_html__( 'Pick a grid layout to show several posts per row and to unlock the Blogs Per Row option below.', 'reign' ),
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

					\Reign\Customizer_Framework\Field::add(
						'number',
						array(
							'settings'        => 'reign_blog_per_row',
							'label'           => esc_html__( 'Blogs Per Row', 'reign' ),
							'description'     => esc_html__( 'How many posts to show per row. Available when a grid Blog Listing Layout is selected.', 'reign' ),
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_blog_per_row_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'switch',
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

					\Reign\Customizer_Framework\Field::add(
						'number',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_advanced_excerpt_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'select',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_single_post_layout_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'select',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_single_post_meta_alignment_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'switch',
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
						\Reign\Customizer_Framework\Field::add(
							'switch',
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
						\Reign\Customizer_Framework\Field::add(
							'switch',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_author_info_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'switch',
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

					\Reign\Customizer_Framework\Field::add(
						'sortable',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
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

					\Reign\Customizer_Framework\Field::add(
						'switch',
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

					\Reign\Customizer_Framework\Field::add(
						'text',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_show_related_post_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'switch',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_single_post_navigation_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_single',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'select',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings' => 'reign_blog_list_pagination_divider',
							'section'  => 'reign_' . $post_type['slug'] . '_archive',
							'choices'  => array(
								'color' => '#dcdcde',
							),
						)
					);

					\Reign\Customizer_Framework\Field::add(
						'select',
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
				}

				\Reign\Customizer_Framework\Field::add(
					'select',
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_single_left_sidebar',
						'label'       => esc_html__( 'Left Sidebar', 'reign' ),
						'description' => esc_html__( 'Set left sidebar.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_single',
						'priority'    => 10,
						'default'     => '0',
						'choices'     => $widgets_areas,
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings' => 'reign_' . $post_type['slug'] . '_single_left_sidebar_divider',
						'section'  => 'reign_' . $post_type['slug'] . '_single',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'select',
					array(
						'settings'    => 'reign_' . $post_type['slug'] . '_single_right_sidebar',
						'label'       => esc_html__( 'Right Sidebar', 'reign' ),
						'description' => esc_html__( 'Set right sidebar.', 'reign' ),
						'section'     => 'reign_' . $post_type['slug'] . '_single',
						'priority'    => 10,
						'default'     => '0',
						'choices'     => $widgets_areas,
					)
				);

				// Search section + its controls only exist under the page panel.
				// Without this guard the whole block re-registers once per CPT.
				if ( 'page' === $post_type['slug'] ) {
				\Reign\Customizer_Framework\Field::add(
					'switch',
					array(
						'settings'    => 'reign_search_header_enable',
						'label'       => esc_html__( 'Hide Search Sub Header', 'reign' ),
						'description' => esc_html__( 'Hide the sub header on the search results page.', 'reign' ),
						'section'     => 'reign_page_search',
						'default'     => 1,
						'priority'    => 10,
						'choices'     => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings' => 'reign_search_header_enable_divider',
						'section'  => 'reign_page_search',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'switch',
					array(
						'settings'        => 'reign_search_enable_header_image',
						'label'           => esc_html__( 'Enable Sub Header Image', 'reign' ),
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

				\Reign\Customizer_Framework\Field::add(
					'image',
					array(
						'settings'        => 'reign_search_header_image',
						'label'           => esc_html__( 'Sub Header Image', 'reign' ),
						'description'     => esc_html__( 'Optional background image for the search results sub header.', 'reign' ),
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

				\Reign\Customizer_Framework\Field::add(
					'custom',
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

				\Reign\Customizer_Framework\Field::add(
					'select',
					array(
						'settings'    => 'reign_search_left_sidebar',
						'label'       => esc_html__( 'Left Sidebar', 'reign' ),
						'description' => esc_html__( 'Set left sidebar.', 'reign' ),
						'section'     => 'reign_page_search',
						'priority'    => 10,
						'default'     => '0',
						'choices'     => $widgets_areas,
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings' => 'reign_search_left_sidebar_divider',
						'section'  => 'reign_page_search',
						'choices'  => array(
							'color' => '#dcdcde',
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'select',
					array(
						'settings'    => 'reign_search_right_sidebar',
						'label'       => esc_html__( 'Right Sidebar', 'reign' ),
						'description' => esc_html__( 'Set right sidebar.', 'reign' ),
						'section'     => 'reign_page_search',
						'priority'    => 10,
						'default'     => '0',
						'choices'     => $widgets_areas,
					)
				);
				} // End page-only search controls guard.

				if ( class_exists( 'bbPress' ) ) {
					\Reign\Customizer_Framework\Field::add(
						'radio',
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

					\Reign\Customizer_Framework\Field::add(
						'select',
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

					\Reign\Customizer_Framework\Field::add(
						'custom',
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
 * Main instance of Reign_Customizer_Post_Types_Fields.
 *
 * @return Reign_Customizer_Post_Types_Fields
 */
Reign_Customizer_Post_Types_Fields::instance();

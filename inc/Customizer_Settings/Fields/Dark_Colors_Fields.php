<?php
/**
 * Dark Mode Colors - per-element colour overrides for dark mode.
 *
 * The light/dark counterpart of Colors_Fields ("Individual Colors"). It mirrors
 * the same per-element controls but writes to a dark namespace ({scheme}-dark-*)
 * and is consumed by reign_load_dark_mode_palette() which emits the
 * [data-bx-mode="dark"] block.
 *
 * Journey: a buyer picks a palette in "Color Palette"; that fills the LIGHT
 * controls (Individual Colors) AND these DARK controls at the same time, because
 * every control defaults from the palette - light defaults from
 * reign_color_scheme_set(), dark defaults from reign_dark_color_scheme_set()
 * (shared dark neutrals + the palette's accent, lifted for dark). So both modes
 * start from the chosen palette and either can be fine-tuned independently.
 *
 * Controls are scheme-gated (active_callback on reign_color_scheme) exactly like
 * the light section, so the visible dark set always matches the active palette.
 *
 * @package reign
 * @since 8.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Reign_Customizer_Dark_Colors_Fields' ) ) :

	class Reign_Customizer_Dark_Colors_Fields {

		/**
		 * @var Reign_Customizer_Dark_Colors_Fields|null
		 */
		protected static $_instance = null;

		public static function instance(): self {
			if ( null === self::$_instance ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			add_action( 'init', array( $this, 'add_panels_and_sections' ) );
			add_action( 'init', array( $this, 'add_fields' ) );
		}

		public function add_panels_and_sections(): void {
			\Reign\Customizer_Framework\Section::add(
				'reign_dark_colors',
				array(
					'title'       => esc_html__( 'Dark Mode Colors', 'reign' ),
					'priority'    => 12,
					'panel'       => 'reign_color_panel',
					'description' => esc_html__( 'The dark-mode twin of Individual Colors. These start from your chosen Color Palette (its accent, lifted for dark, on a tuned dark canvas) so you rarely need to touch them - but anything you set here applies only when the site is in dark mode. The light/dark switch above turns dark mode on for visitors.', 'reign' ),
				)
			);
		}

		/**
		 * The per-element colour controls, grouped. Mirrors Colors_Fields so the
		 * light and dark sections read identically.
		 *
		 * @return array<string,array<string,string>>
		 */
		private function field_groups(): array {
			return array(
				'topbar'      => array(
					'reign_header_topbar_bg_color'         => esc_html__( 'Top Bar Background Color', 'reign' ),
					'reign_header_topbar_text_color'       => esc_html__( 'Top Bar Text Color', 'reign' ),
					'reign_header_topbar_text_hover_color' => esc_html__( 'Top Bar Text Color [Hover]', 'reign' ),
				),
				'header'      => array(
					'reign_header_bg_color'                    => esc_html__( 'Header Background Color', 'reign' ),
					'reign_title_tagline_typography'           => esc_html__( 'Site Title Font Color', 'reign' ),
					'reign_header_main_menu_font'              => esc_html__( 'Main Menu Item Font Color', 'reign' ),
					'reign_header_main_menu_text_hover_color'  => esc_html__( 'Main Menu Item Font Color [Hover]', 'reign' ),
					'reign_header_main_menu_text_active_color' => esc_html__( 'Main Menu Item Font Color [Active]', 'reign' ),
					'reign_header_main_menu_bg_hover_color'    => esc_html__( 'Main Menu Border OR Background Color [Hover]', 'reign' ),
					'reign_header_main_menu_bg_active_color'   => esc_html__( 'Main Menu Border OR Background Color [Active]', 'reign' ),
					'reign_header_sub_menu_bg_color'           => esc_html__( 'Sub Menu Item Background Color', 'reign' ),
					'reign_header_sub_menu_font'               => esc_html__( 'Sub Menu Item Font Color', 'reign' ),
					'reign_header_sub_menu_text_hover_color'   => esc_html__( 'Sub Menu Item Font Color [Hover]', 'reign' ),
					'reign_header_sub_menu_bg_hover_color'     => esc_html__( 'Sub Menu Item Background Color [Hover]', 'reign' ),
					'reign_header_icon_color'                  => esc_html__( 'Header Icon Color', 'reign' ),
					'reign_header_icon_hover_color'            => esc_html__( 'Header Icon Color [Hover]', 'reign' ),
				),
				'mobile'      => array(
					'reign_mobile_menu_bg_color'        => esc_html__( 'Mobile Panel Background Color', 'reign' ),
					'reign_mobile_menu_color'           => esc_html__( 'Mobile Menu Color', 'reign' ),
					'reign_mobile_menu_hover_color'     => esc_html__( 'Mobile Menu Color [Hover]', 'reign' ),
					'reign_mobile_menu_active_color'    => esc_html__( 'Mobile Menu Color [Active]', 'reign' ),
					'reign_mobile_menu_active_bg_color' => esc_html__( 'Mobile Menu Background Color [Active]', 'reign' ),
				),
				'left_panel'  => array(
					'reign_left_panel_bg_color'               => esc_html__( 'Left Panel Background Color', 'reign' ),
					'reign_left_panel_toggle_color'           => esc_html__( 'Left Panel Toggle Color', 'reign' ),
					'reign_left_panel_menu_font_color'        => esc_html__( 'Left Panel Menu Color', 'reign' ),
					'reign_left_panel_menu_bg_active_color'   => esc_html__( 'Left Panel Background Color [Active]', 'reign' ),
					'reign_left_panel_menu_icon_active_color' => esc_html__( 'Left Panel Menu Color [Active]', 'reign' ),
					'reign_left_panel_menu_bg_hover_color'    => esc_html__( 'Left Panel Background Color [Hover]', 'reign' ),
					'reign_left_panel_menu_hover_color'       => esc_html__( 'Left Panel Font Color [Hover]', 'reign' ),
					'reign_left_panel_tooltip_bg_color'       => esc_html__( 'Left Panel Tooltip Background Color', 'reign' ),
					'reign_left_panel_tooltip_color'          => esc_html__( 'Left Panel Tooltip Color', 'reign' ),
				),
				'content'     => array(
					'reign_site_body_bg_color'           => esc_html__( 'Body Background Color', 'reign' ),
					'reign_site_body_text_color'         => esc_html__( 'Body Text Color', 'reign' ),
					'reign_site_alternate_text_color'    => esc_html__( 'Alternate Text Color', 'reign' ),
					'reign_site_sections_bg_color'       => esc_html__( 'Sections Background Color', 'reign' ),
					'reign_site_secondary_bg_color'      => esc_html__( 'Secondary Background Color', 'reign' ),
					'reign_colors_theme'                 => esc_html__( 'Theme Color', 'reign' ),
					'reign_site_headings_color'          => esc_html__( 'Headings Color', 'reign' ),
					'reign_site_link_color'              => esc_html__( 'Link Color', 'reign' ),
					'reign_site_link_hover_color'        => esc_html__( 'Link Color [Hover]', 'reign' ),
					'reign_accent_color'                 => esc_html__( 'Content Link Color', 'reign' ),
					'reign_accent_hover_color'           => esc_html__( 'Content Link Color [Hover]', 'reign' ),
					'reign_site_button_text_color'       => esc_html__( 'Button Text Color', 'reign' ),
					'reign_site_button_text_hover_color' => esc_html__( 'Button Text Color [Hover]', 'reign' ),
					'reign_site_button_bg_color'         => esc_html__( 'Button Background Color', 'reign' ),
					'reign_site_button_bg_hover_color'   => esc_html__( 'Button Background Color [Hover]', 'reign' ),
					'reign_site_border_color'            => esc_html__( 'Border Color', 'reign' ),
					'reign_site_hr_color'                => esc_html__( 'HR Color', 'reign' ),
				),
				'footer'      => array(
					'reign_footer_widget_area_bg_color'       => esc_html__( 'Footer Background Color', 'reign' ),
					'reign_footer_widget_title_color'         => esc_html__( 'Footer Widget Title Color', 'reign' ),
					'reign_footer_widget_text_color'          => esc_html__( 'Footer Text Color', 'reign' ),
					'reign_footer_widget_link_color'          => esc_html__( 'Footer Link Color', 'reign' ),
					'reign_footer_widget_link_hover_color'    => esc_html__( 'Footer Link Color [Hover]', 'reign' ),
					'reign_footer_copyright_bg_color'         => esc_html__( 'Copyright Background Color', 'reign' ),
					'reign_footer_copyright_text_color'       => esc_html__( 'Copyright Text Color', 'reign' ),
					'reign_footer_copyright_link_color'       => esc_html__( 'Copyright Link Color', 'reign' ),
					'reign_footer_copyright_link_hover_color' => esc_html__( 'Copyright Link Color [Hover]', 'reign' ),
				),
				'forms'       => array(
					'reign_form_text_color'              => esc_html__( 'Form Text Color', 'reign' ),
					'reign_form_background_color'        => esc_html__( 'Form Background Color', 'reign' ),
					'reign_form_border_color'            => esc_html__( 'Form Border Color', 'reign' ),
					'reign_form_placeholder_color'       => esc_html__( 'Form Placeholder Color', 'reign' ),
					'reign_form_focus_text_color'        => esc_html__( 'Form Text Color [Focus]', 'reign' ),
					'reign_form_focus_background_color'  => esc_html__( 'Form Background Color [Focus]', 'reign' ),
					'reign_form_focus_border_color'      => esc_html__( 'Form Border Color [Focus]', 'reign' ),
					'reign_form_focus_placeholder_color' => esc_html__( 'Form Placeholder Color [Focus]', 'reign' ),
				),
			);
		}

		public function add_fields(): void {
			$schemes = function_exists( 'reign_color_scheme_set' ) ? reign_color_scheme_set() : array();
			$darks   = function_exists( 'reign_dark_color_scheme_set' ) ? reign_dark_color_scheme_set() : array();
			if ( empty( $schemes ) ) {
				return;
			}

			$groups = $this->field_groups();

			foreach ( $schemes as $scheme_key => $unused ) {
				$dark_set = isset( $darks[ $scheme_key ] ) ? $darks[ $scheme_key ] : array();
				$priority = 10;

				foreach ( $groups as $group_key => $fields ) {
					foreach ( $fields as $var => $label ) {
						\Reign\Customizer_Framework\Field::add(
							'color',
							array(
								'settings'        => $scheme_key . '-dark-' . $var,
								'label'           => $label,
								'section'         => 'reign_dark_colors',
								'default'         => isset( $dark_set[ $var ] ) ? $dark_set[ $var ] : '',
								'priority'        => $priority,
								'transport'       => 'refresh',
								'choices'         => array( 'alpha' => true ),
								'active_callback' => array(
									array(
										'setting'  => 'reign_color_scheme',
										'operator' => '===',
										'value'    => $scheme_key,
									),
								),
							)
						);
					}

					// Divider between groups (scheme-gated like the controls).
					\Reign\Customizer_Framework\Field::add(
						'custom',
						array(
							'settings'        => $scheme_key . '-dark-' . $group_key . '_divider',
							'section'         => 'reign_dark_colors',
							'priority'        => $priority,
							'choices'         => array( 'color' => '#dcdcde' ),
							'active_callback' => array(
								array(
									'setting'  => 'reign_color_scheme',
									'operator' => '===',
									'value'    => $scheme_key,
								),
							),
						)
					);
					++$priority;
				}
			}
		}
	}

endif;

Reign_Customizer_Dark_Colors_Fields::instance();

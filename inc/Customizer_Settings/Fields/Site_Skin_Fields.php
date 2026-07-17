<?php
/**
 * Site Skin Fields (Style Variation + Color Mode Toggle).
 *
 * New in Reign 8.0.0. Adds:
 *   1. A "Site Skin" customizer section under the Color Options panel
 *      housing a radio picker for the 8 style variations shipped in
 *      `styles/*.json` (cool / dark / editorial / minimal / monochrome
 *      / pastel / vibrant / warm).
 *   2. Color-mode toggle fields consumed by
 *      inc/Color_Mode_Toggle/Component.php :
 *        - site_color_mode_toggle_show   (switch)
 *        - site_color_mode_toggle_position (select)
 *        - site_color_mode               (select: default mode for first visit)
 *
 * The variation picker writes to the standard WP-core theme_mod
 * `site_style_variation` that the new Tokens module reads at emit time.
 *
 * @package reign
 * @since 8.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Reign_Customizer_Site_Skin_Fields' ) ) :

	class Reign_Customizer_Site_Skin_Fields {

		/**
		 * @var Reign_Customizer_Site_Skin_Fields|null
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
			// Priority 20 so it runs AFTER the Customizer Framework enqueues the
			// controls JS this data feeds (the unified Color Palette swatches).
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_palette_swatches' ), 20 );
		}

		/**
		 * Localize the colour swatches for the unified Color Palette picker so
		 * customizer-controls.js can render a real colour preview next to each
		 * option (buyers choose by sight). One entry per preset choice:
		 * [ background, accent, text, surface ].
		 */
		public function enqueue_palette_swatches(): void {
			$map = array(
				'default' => array( '#ffffff', '#1d76da', '#404855', '#f5f5f5' ),
			);

			// Every palette in the picker is now a classic scheme (8.0.0), so the
			// swatches all come from the per-scheme colour set below - one source.
			if ( function_exists( 'reign_color_scheme_set' ) ) {
				foreach ( (array) reign_color_scheme_set() as $key => $colors ) {
					$map[ 'scheme-' . $key ] = array(
						$colors['reign_site_body_bg_color'] ?? '#ffffff',
						$colors['reign_colors_theme'] ?? '#000000',
						$colors['reign_site_body_text_color'] ?? '#000000',
						$colors['reign_header_bg_color'] ?? '#eeeeee',
					);
				}
			}

			wp_add_inline_script(
				'customize-controls',
				'window.reignPaletteSwatches = ' . wp_json_encode( $map ) . ';',
				'after'
			);
		}

		public function add_panels_and_sections(): void {
			\Reign\Customizer_Framework\Section::add(
				'reign_site_skin_section',
				array(
					'title'       => esc_html__( 'Color Palette', 'reign' ),
					'description' => esc_html__( 'Pick a starting palette for your whole site - the quickest way to set your colors. Every palette is just a starting point: fine-tune any individual element (header, menus, footer, forms) in the sections below, and your changes always win over the palette.', 'reign' ),
					'panel'       => 'reign_color_panel',
					'priority'    => 5,
				)
			);
			\Reign\Customizer_Framework\Section::add(
				'reign_color_mode_toggle_section',
				array(
					'title'       => esc_html__( 'Light & Dark Mode', 'reign' ),
					'description' => esc_html__( 'Turn on a light and dark switch for your visitors and choose the default mode. Dark mode automatically follows your chosen Color Palette - pick Rose and dark mode is a dark rose, pick Emerald and it is a dark emerald. Optionally add a dark-mode logo below.', 'reign' ),
					'panel'       => 'reign_color_panel',
					'priority'    => 15,
				)
			);
		}

		public function add_fields(): void {

			// ---- Unified Color Palette picker ------------------------------
			// One control lists ALL starting palettes - modern skins AND the
			// classic Reign schemes - so new and existing owners see one set.
			// customizer-controls.js maps each choice to the real settings
			// (skin-* -> site_style_variation, scheme-* -> reign_color_scheme)
			// keeping them mutually exclusive, and hides the two raw pickers
			// below. Default is derived from the current state so the right
			// palette is pre-selected without a spurious "unsaved" flag.
			$reign_skin_now   = (string) get_theme_mod( 'site_style_variation', '' );
			$reign_scheme_now = (string) get_theme_mod( 'reign_color_scheme', 'reign_clean' );
			// 8.0.0: the picker is scheme-based. Map a legacy pre-release skin
			// choice to its scheme equivalent so the right palette stays selected.
			$reign_skin_to_scheme = array(
				'emerald' => 'reign_emerald',
				'indigo'  => 'reign_indigo',
				'minimal' => 'reign_minimal',
			);
			$reign_preset_default = ( '' !== $reign_skin_now && isset( $reign_skin_to_scheme[ $reign_skin_now ] ) )
				? 'scheme-' . $reign_skin_to_scheme[ $reign_skin_now ]
				: 'scheme-' . $reign_scheme_now;

			\Reign\Customizer_Framework\Field::add(
				'radio',
				array(
					'settings'          => 'reign_color_preset',
					'label'             => esc_html__( 'Color Palette', 'reign' ),
					'description'       => esc_html__( 'Pick a starting palette - modern skins or a classic Reign palette, all in one place. It is only a starting point: fine-tune any element in the sections below and your changes always win.', 'reign' ),
					'section'           => 'reign_site_skin_section',
					'default'           => $reign_preset_default,
					'transport'         => 'postMessage',
					'priority'          => 1,
					'sanitize_callback' => 'sanitize_text_field',
					'choices'           => array(
						// One palette per colour family - distinct, no look-alikes.
						// All scheme-* now, so every palette fills Individual Colors.
						'default'                => esc_html__( 'Default (your current colors)', 'reign' ),
						'scheme-reign_minimal'   => esc_html__( 'Minimal', 'reign' ),    // mono / black + white
						'scheme-reign_clean'     => esc_html__( 'Clean', 'reign' ),      // blue
						'scheme-reign_emerald'   => esc_html__( 'Emerald', 'reign' ),    // green
						'scheme-reign_indigo'    => esc_html__( 'Indigo', 'reign' ),     // purple
						'scheme-reign_dating'    => esc_html__( 'Rose', 'reign' ),       // pink
						'scheme-reign_ectoplasm' => esc_html__( 'Amber', 'reign' ),      // warm orange
						'scheme-reign_dark'      => esc_html__( 'Midnight', 'reign' ),   // always-dark
					),
				)
			);

			// ---- Raw style-variation picker (hidden by JS; kept as the real
			//      setting the unified picker drives + the theme reads) -------

			\Reign\Customizer_Framework\Field::add(
				'radio',
				array(
					'settings'    => 'site_style_variation',
					'label'       => esc_html__( 'Site Skin', 'reign' ),
					'description' => esc_html__( 'Choose a starting palette. It only fills the elements you have not set yourself - any color you pick by hand always wins. Choose "Default" to keep your current colors.', 'reign' ),
					'section'     => 'reign_site_skin_section',
					'default'     => '',
					'transport'   => 'refresh',
					'priority'    => 2,
					'choices'     => array(
						''           => esc_html__( 'Default - Keep current colors', 'reign' ),
						'cool'       => esc_html__( 'Cool - Sky, cyan and indigo', 'reign' ),
						'dark'       => esc_html__( 'Dark - High-contrast night theme', 'reign' ),
						'editorial'  => esc_html__( 'Editorial - Calm, content-first', 'reign' ),
						'minimal'    => esc_html__( 'Minimal - Black and white restraint', 'reign' ),
						'monochrome' => esc_html__( 'Monochrome - Single hue, layered shades', 'reign' ),
						'pastel'     => esc_html__( 'Pastel - Soft, gentle tones', 'reign' ),
						'vibrant'    => esc_html__( 'Vibrant - Bold, saturated accents', 'reign' ),
						'warm'       => esc_html__( 'Warm - Sunset oranges and reds', 'reign' ),
					),
				)
			);

			// ---- Color mode toggle visibility ------------------------------

			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'    => 'site_color_mode_toggle_show',
					'label'       => esc_html__( 'Show Light / Dark Switch', 'reign' ),
					'description' => esc_html__( 'Adds a small sun / moon button to your header so visitors can pick light or dark. Their choice is remembered when they return.', 'reign' ),
					'section'     => 'reign_color_mode_toggle_section',
					'default'     => 'on',
					'transport'   => 'refresh',
					'choices'     => array(
						'on'  => esc_html__( 'Enabled', 'reign' ),
						'off' => esc_html__( 'Disabled', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'select',
				array(
					'settings'        => 'site_color_mode_toggle_position',
					'label'           => esc_html__( 'Switch Position', 'reign' ),
					'description'     => esc_html__( 'Where the switch appears for your visitors.', 'reign' ),
					'section'         => 'reign_color_mode_toggle_section',
					'default'         => 'both',
					'transport'       => 'refresh',
					'choices'         => array(
						'both'        => esc_html__( 'Header and mobile menu', 'reign' ),
						'header'      => esc_html__( 'Header only', 'reign' ),
						'mobile_only' => esc_html__( 'Mobile menu only', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'site_color_mode_toggle_show',
							'operator' => '==',
							'value'    => 'on',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'select',
				array(
					'settings'    => 'site_color_mode',
					'label'       => esc_html__( 'Default Mode', 'reign' ),
					'description' => esc_html__( 'Which theme new visitors see first. Choose Auto to match the visitor\'s device setting.', 'reign' ),
					'section'     => 'reign_color_mode_toggle_section',
					'default'     => 'light',
					'transport'   => 'refresh',
					'choices'     => array(
						'light' => esc_html__( 'Light', 'reign' ),
						'dark'  => esc_html__( 'Dark', 'reign' ),
						'auto'  => esc_html__( 'Auto - Match visitor\'s device', 'reign' ),
					),
				)
			);
		}
	}

endif;

Reign_Customizer_Site_Skin_Fields::instance();

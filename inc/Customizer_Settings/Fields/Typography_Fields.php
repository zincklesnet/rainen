<?php
/**
 * Reign Customizer Typography
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Typography_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Typography_Fields
	 */
	class Reign_Customizer_Typography_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Typography_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Typography_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Typography_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Typography_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Typography_Fields Constructor.
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
			// Priority 20: run after the Customizer Framework enqueues the
			// controls JS this preset data + preview fonts feed.
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_typography_presets' ), 20 );
		}

		/**
		 * Curated typographic systems (8.0.0) - one per real use case.
		 *
		 * STRAIGHT, NO-SURPRISES RULES (read this before editing):
		 *
		 *  1. A role's array is the EXACT set of CSS values written to the
		 *     matching settings - no unit math, no transforms in code. Store
		 *     CSS-ready strings: line-height unitless ('1.25'), letter-spacing
		 *     with a unit ('-0.011em' or '0').
		 *  2. Roles: body | heading | menu | quote. Each carries font-family,
		 *     font-weight, line-height, letter-spacing, text-transform,
		 *     font-style. NOTHING ELSE.
		 *  3. Presets NEVER set font-size. The theme scales every element
		 *     (h1-h6 AND body) responsively via media queries in
		 *     base/_typography.scss; the customizer's inline CSS has no media
		 *     query and loads last, so a pinned px size would override the
		 *     desktop/tablet/mobile steps and break mobile. The bridge clears
		 *     font-size to '' so sizing always stays on the responsive scale.
		 *     (Owners can still pin a size per element by hand if they want.)
		 *  4. QUOTE is deliberately its own voice (italic serif, slab, or a
		 *     lighter display weight) - never just the uniform heading font.
		 *  5. Family names are exact Google Fonts names so the value-driven
		 *     loader (inc/Fonts/Component.php) enqueues them. Inter is the
		 *     theme's self-hosted face (rendered locally, skipped for Google).
		 *
		 * @return array<string,mixed>
		 */
		public static function typography_presets(): array {
			// Tiny role builder so every preset reads the same way and we can
			// never forget a property. Args are CSS-ready strings.
			$role = static function ( $family, $weight, $line_height, $letter_spacing = '0', $transform = 'none', $style = 'normal' ): array {
				return array(
					'font-family'    => $family,
					'font-weight'    => (string) $weight,
					'line-height'    => (string) $line_height,
					'letter-spacing' => (string) $letter_spacing,
					'text-transform' => $transform,
					'font-style'     => $style,
				);
			};

			return array(
				// Multipurpose / SaaS / community - neutral, modern.
				'modern'    => array(
					'label' => esc_html__( 'Modern', 'reign' ),
					'pair'  => 'Inter + Inter',
					'roles' => array(
						'body'    => $role( 'Inter', '400', '1.7' ),
						'heading' => $role( 'Inter', '700', '1.25', '-0.011em' ),
						'menu'    => $role( 'Inter', '500', '1.4' ),
						'quote'   => $role( 'Inter', '400', '1.6', '0', 'none', 'italic' ),
					),
				),
				// Friendly community / membership - rounded, approachable.
				'friendly'  => array(
					'label' => esc_html__( 'Friendly', 'reign' ),
					'pair'  => 'Poppins + Inter',
					'roles' => array(
						'body'    => $role( 'Inter', '400', '1.7' ),
						'heading' => $role( 'Poppins', '600', '1.3', '-0.005em' ),
						'menu'    => $role( 'Poppins', '500', '1.4' ),
						'quote'   => $role( 'Poppins', '400', '1.55', '0', 'none', 'italic' ),
					),
				),
				// Startup / tech / product - geometric, confident.
				'bold'      => array(
					'label' => esc_html__( 'Bold', 'reign' ),
					'pair'  => 'Space Grotesk + Inter',
					'roles' => array(
						'body'    => $role( 'Inter', '400', '1.7' ),
						'heading' => $role( 'Space Grotesk', '700', '1.2', '-0.01em' ),
						'menu'    => $role( 'Space Grotesk', '500', '1.4', '0.04em', 'uppercase' ),
						'quote'   => $role( 'Space Grotesk', '500', '1.4' ),
					),
				),
				// Blog / publishing - serif display + clean reading sans.
				'editorial' => array(
					'label' => esc_html__( 'Editorial', 'reign' ),
					'pair'  => 'Playfair Display + Source Sans 3',
					'roles' => array(
						'body'    => $role( 'Source Sans 3', '400', '1.75' ),
						'heading' => $role( 'Playfair Display', '700', '1.2' ),
						'menu'    => $role( 'Source Sans 3', '600', '1.4', '0.02em' ),
						'quote'   => $role( 'Playfair Display', '500', '1.4', '0', 'none', 'italic' ),
					),
				),
				// News / magazine - condensed display + serif body, uppercase nav.
				'magazine'  => array(
					'label' => esc_html__( 'Magazine', 'reign' ),
					'pair'  => 'Oswald + Merriweather',
					'roles' => array(
						'body'    => $role( 'Merriweather', '400', '1.8' ),
						'heading' => $role( 'Oswald', '600', '1.25', '0.005em' ),
						'menu'    => $role( 'Oswald', '500', '1.4', '0.06em', 'uppercase' ),
						'quote'   => $role( 'Merriweather', '400', '1.7', '0', 'none', 'italic' ),
					),
				),
				// eCommerce / store - strong sans headings, uppercase retail nav.
				'commerce'  => array(
					'label' => esc_html__( 'Commerce', 'reign' ),
					'pair'  => 'Montserrat + Roboto',
					'roles' => array(
						'body'    => $role( 'Roboto', '400', '1.7' ),
						'heading' => $role( 'Montserrat', '700', '1.3', '-0.005em' ),
						'menu'    => $role( 'Montserrat', '600', '1.4', '0.03em', 'uppercase' ),
						'quote'   => $role( 'Roboto', '400', '1.6', '0', 'none', 'italic' ),
					),
				),
				// LMS / courses / academic - readable slab + roomy body.
				'learning'  => array(
					'label' => esc_html__( 'Learning', 'reign' ),
					'pair'  => 'Roboto Slab + Roboto',
					'roles' => array(
						'body'    => $role( 'Roboto', '400', '1.75' ),
						'heading' => $role( 'Roboto Slab', '700', '1.3' ),
						'menu'    => $role( 'Roboto', '500', '1.4', '0.01em' ),
						'quote'   => $role( 'Roboto Slab', '400', '1.5' ),
					),
				),
				// Directory / listings / local - utilitarian single-family.
				'directory' => array(
					'label' => esc_html__( 'Directory', 'reign' ),
					'pair'  => 'DM Sans + DM Sans',
					'roles' => array(
						'body'    => $role( 'DM Sans', '400', '1.7' ),
						'heading' => $role( 'DM Sans', '700', '1.3', '-0.01em' ),
						'menu'    => $role( 'DM Sans', '500', '1.4', '0.01em' ),
						'quote'   => $role( 'DM Sans', '400', '1.6', '0', 'none', 'italic' ),
					),
				),
				// Premium / portfolio / luxury - airy serif + humanist sans, small-caps nav.
				'elegant'   => array(
					'label' => esc_html__( 'Elegant', 'reign' ),
					'pair'  => 'Cormorant Garamond + Nunito Sans',
					'roles' => array(
						'body'    => $role( 'Nunito Sans', '400', '1.75' ),
						'heading' => $role( 'Cormorant Garamond', '600', '1.2' ),
						'menu'    => $role( 'Nunito Sans', '600', '1.4', '0.06em', 'uppercase' ),
						'quote'   => $role( 'Cormorant Garamond', '500', '1.4', '0', 'none', 'italic' ),
					),
				),
			);
		}

		/**
		 * Localize the preset map + load the preset fonts in the controls pane
		 * so each option previews in its real typeface.
		 */
		public function enqueue_typography_presets(): void {
			$presets = self::typography_presets();

			wp_add_inline_script(
				'customize-controls',
				'window.reignTypographyPresets = ' . wp_json_encode( $presets ) . ';'
					. 'window.reignTypographyDefaultNote = ' . wp_json_encode( esc_html__( 'Keeps the fonts you are using now - nothing changes.', 'reign' ) ) . ';',
				'after'
			);

			// One Google Fonts request covering every preset face (all roles),
			// controls-pane only (never shipped to visitors), so the picker
			// previews live. Italic axis included for the blockquote previews.
			$families = array();
			foreach ( $presets as $p ) {
				foreach ( $p['roles'] as $role ) {
					$fam = $role['font-family'];
					if ( 'Inter' === $fam || '' === $fam ) {
						continue; // self-hosted in the admin already.
					}
					$families[ $fam ] = true;
				}
			}
			if ( ! empty( $families ) ) {
				// Use the v1 css API (lenient): it silently ignores weights/styles
				// a family does not ship, so mixing italic + non-italic faces
				// (e.g. Oswald, Space Grotesk) never 400s the whole request.
				$spec = array();
				foreach ( array_keys( $families ) as $fam ) {
					$spec[] = str_replace( ' ', '+', $fam ) . ':400,400italic,500,600,700';
				}
				$url = add_query_arg(
					array(
						'family'  => implode( '|', $spec ),
						'display' => 'swap',
					),
					'https://fonts.googleapis.com/css'
				);
				wp_enqueue_style( 'reign-typo-preset-preview', $url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			}
		}

		/**
		 * Dynamically get body font family output based on active plugins.
		 *
		 * @return array
		 */
		protected function get_body_typography_output(): array {
			$selectors = array(
				'body',
			);

			// PeepSo.
			if ( class_exists( 'PeepSo' ) ) {
				$selectors[] = '.peepso *:not(.fa, .fab, .fad, .fal, .far, .fas)';
				$selectors[] = '.ps-lightbox *:not(.fa, .fab, .fad, .fal, .far, .fas)';
				$selectors[] = '.ps-dialog *:not(.fa, .fab, .fad, .fal, .far, .fas)';
				$selectors[] = '.ps-hovercard *:not(.fa, .fab, .fad, .fal, .far, .fas)';
			}

			return array(
				// Apply all typography properties to the body.
				array(
					'element' => 'body',
				),
				// Apply only font-family to other UI elements.
				array(
					'choice'   => 'font-family',
					'element'  => implode( ', ', $selectors ),
					'property' => 'font-family',
				),
			);
		}


		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {

			// General panel kept - Login Popup, Custom Code, Site Performance,
			// Site Layout and Page Mapping sections still live here.
			\Reign\Customizer_Framework\Panel::add(
				'reign_general_panel',
				array(
					'priority'    => 21,
					'title'       => esc_html__( 'General', 'reign' ),
					'description' => '',
				)
			);

			// Typography promoted to its own top-level panel in 8.0.0, mirroring
			// Color Options: a "Font Presets" picker that fills the per-element
			// fonts in "Individual Fonts" below.
			\Reign\Customizer_Framework\Panel::add(
				'reign_typography_panel',
				array(
					'priority'    => 22,
					'title'       => esc_html__( 'Typography', 'reign' ),
					'description' => esc_html__( 'Pick a font preset to style your whole site in one click, then fine-tune any element below.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_typography_presets',
				array(
					'title'       => esc_html__( 'Font Presets', 'reign' ),
					'priority'    => 5,
					'panel'       => 'reign_typography_panel',
					'description' => esc_html__( 'Best-in-class font pairings for communities, stores, courses, directories and blogs. Pick one and it sets every font below - choose Default to keep your current fonts. You can still fine-tune any element afterwards.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_typography',
				array(
					'title'       => esc_html__( 'Individual Fonts', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_typography_panel',
					'description' => esc_html__( 'Fine-tune the font of any element. A preset fills these in; anything you change here wins.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_get_customizer_default_value_set();

			// ---- Font Preset picker --------------------------------------
			// UI-only facade: customizer-controls.js applies the chosen pairing
			// to every typography setting below (and turns Custom Typography on).
			// Default is a no-op so existing sites are never touched.
			$preset_choices = array(
				'default' => esc_html__( 'Default (your current fonts)', 'reign' ),
			);
			foreach ( self::typography_presets() as $preset_key => $preset ) {
				// Label is just the use-case name; the live preview row below
				// each option shows the actual heading/body/quote typefaces.
				$preset_choices[ $preset_key ] = $preset['label'];
			}

			\Reign\Customizer_Framework\Field::add(
				'radio',
				array(
					'settings'          => 'reign_typography_preset',
					'label'             => esc_html__( 'Font Preset', 'reign' ),
					'description'       => esc_html__( 'Applies a curated heading and body pairing to every font option in Individual Fonts. It is only a starting point - fine-tune anything afterwards.', 'reign' ),
					'section'           => 'reign_typography_presets',
					'default'           => 'default',
					'transport'         => 'postMessage',
					'priority'          => 1,
					'sanitize_callback' => 'sanitize_text_field',
					'choices'           => $preset_choices,
				)
			);

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_custom_typography',
					'label'       => esc_html__( 'Custom Typography', 'reign' ),
					'description' => esc_html__( 'Enable or disable custom typography.', 'reign' ),
					'section'     => 'reign_typography',
					'default'     => 1,
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_custom_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_body_typography',
					'label'           => esc_html__( 'Body Font', 'reign' ),
					'description'     => esc_html__( 'Set font properties of body tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_body_typography'],
					'priority'        => 10,
					'output'          => $this->get_body_typography_output(),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_body_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_title_tagline_typography',
					'label'           => esc_html__( 'Site Title', 'reign' ),
					'description'     => esc_html__( 'Set font properties of site title.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_title_tagline_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '.site-branding .site-title a',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_title_tagline_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'site_tagline_typography_option',
					'label'           => esc_html__( 'Site Tagline', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['site_tagline_typography_option'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '.site-description, body #masthead p.site-description',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_tagline_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_header_main_menu_font',
					'label'           => esc_html__( 'Header Main Menu', 'reign' ),
					'description'     => esc_html__( 'Set font properties for menu.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_header_main_menu_font'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '#masthead.site-header .main-navigation .primary-menu > li a, #masthead .user-link-wrap .user-link, #masthead .psw-userbar__name>a',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_header_main_menu_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_header_sub_menu_font',
					'label'           => esc_html__( 'Header Sub Menu', 'reign' ),
					'description'     => esc_html__( 'Set font properties for sub menu.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_header_sub_menu_font'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => '#masthead.site-header .main-navigation .primary-menu > li .sub-menu li a, #masthead .user-profile-menu li > a',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_header_sub_menu_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_h1_typography',
					'label'           => esc_html__( 'Heading 1', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H1 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h1_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h1',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_h1_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_h2_typography',
					'label'           => esc_html__( 'Heading 2', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H2 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h2_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h2',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_h2_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_h3_typography',
					'label'           => esc_html__( 'Heading 3', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H3 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h3_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h3, .buddypress-wrap .item-body .group-separator-block .screen-heading',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_h3_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_h4_typography',
					'label'           => esc_html__( 'Heading 4', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H4 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h4_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h4',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_h4_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_h5_typography',
					'label'           => esc_html__( 'Heading 5', 'reign' ),
					'description'     => esc_html__( 'Allows you to select all font properties of H5 tag for your site.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h5_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h5',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_h5_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_h6_typography',
					'label'           => esc_html__( 'Heading 6', 'reign' ),
					'description'     => esc_html__( 'Set font properties of H6 tag.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_h6_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'h6',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_h6_typography_divider',
					'section'         => 'reign_typography',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'typography',
				array(
					'settings'        => 'reign_quote_typography',
					'label'           => esc_html__( 'Blockquote Typography', 'reign' ),
					'description'     => esc_html__( 'Set font properties of blockquote.', 'reign' ),
					'section'         => 'reign_typography',
					'default'         => $default_value_set['reign_quote_typography'],
					'priority'        => 10,
					'output'          => array(
						array(
							'element' => 'blockquote, .wp-block-quote, .wp-block-quote p',
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_custom_typography',
							'operator' => '!==',
							'value'    => false,
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Typography_Fields.
 *
 * @return Reign_Customizer_Typography_Fields
 */
Reign_Customizer_Typography_Fields::instance();

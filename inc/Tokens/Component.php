<?php
/**
 * Reign\Tokens\Component class
 *
 * Token emitter for the NON-colour parts of the theme. In 8.0.0 the colour
 * system was consolidated to a single source of truth - the `--reign-*` custom
 * properties (inc/init.php load_color_palette() + _dynamic-colors.scss + the
 * Site Skin overlay below). The old parallel `--bx-color-*` colour layer was
 * removed; this class no longer emits colour tokens.
 *
 * What it still emits:
 *
 *   1. The Site Skin overlay - paints each variation palette role into the
 *      canonical `--reign-*` vars (see $variation_palette_targets).
 *
 *   2. Legacy `--color-*` / `--global-*` / `--button-*` aliases - back-compat
 *      shim for 3rd-party CSS; they now resolve to `var(--reign-*)` (see
 *      $legacy_aliases) or to per-element saved values.
 *
 *   3. Non-colour framework tokens - `--bx-radius` / `--bx-space` / `--bx-shadow`
 *      / motion / z-index (the shadow ladder keeps two `--bx-color-shadow` bases).
 *
 *   4. Dark mode - the `--wp--preset--color--*` block-palette overrides via
 *      [data-bx-mode="dark"]; Reign colour itself flips through `--reign-*`.
 *
 * @package Reign */

namespace Reign\Tokens;

// Component_Interface dropped during BuddyX → Reign port. The module
// auto-boots via Component::boot() at end of file rather than going
// through a component registry.
use function add_action;
use function add_filter;
use function get_theme_mod;
use function set_theme_mod;
use function apply_filters;

defined( 'ABSPATH' ) || exit;

/**
 * Tokens
 */
class Component {

	/**
	 * Per-element alias map: theme_mod_key => array( 'aliases' => array( ... ) ).
	 *
	 * Emits the legacy --color- / --button- aliases from saved reign_* colour
	 * values (back-compat for pre-token and 3rd-party CSS). The --bx-color token
	 * these entries once also emitted was removed in 8.0.0 - colour renders
	 * through --reign-* (inc/init.php load_color_palette()).
	 *
	 * @var array<string, array{aliases:array<int,string>}>
	 */
	protected static array $simple_color_tokens = array(
		// Brand / accent (Reign's BASE colours — scheme-prefixed variants are
		// resolved by reign_color_scheme_set() in customizer-defaults.php and
		// emitted via inc/custom-styles.php inline-CSS pipeline; this map emits
		// their legacy --color-*/--button-* aliases from saved values).
		'reign_colors_theme'                       => array(
			'aliases' => array( '--color-theme-primary' ),
		),
		'reign_accent_color'                       => array(
			'aliases' => array(),
		),
		'reign_accent_hover_color'                 => array(
			'aliases' => array(),
		),

		// Buttons.
		'reign_site_button_bg_color'               => array(
			'aliases' => array( '--button-background-color' ),
		),
		'reign_site_button_bg_hover_color'         => array(
			'aliases' => array( '--button-background-hover-color' ),
		),
		'reign_site_button_text_color'             => array(
			'aliases' => array( '--button-text-color' ),
		),
		'reign_site_button_text_hover_color'       => array(
			'aliases' => array( '--button-text-hover-color' ),
		),

		// Body / surfaces.
		'reign_site_sections_bg_color'             => array(
			'aliases' => array( '--color-theme-body' ),
		),
		'reign_site_secondary_bg_color'            => array(
			'aliases' => array( '--global-body-lightcolor' ),
		),
		'reign_site_body_bg_color'                 => array(
			'aliases' => array( '--color-layout-boxed' ),
		),
		'reign_site_alternate_text_color'          => array(
			'aliases' => array(),
		),
		'reign_site_body_text_color'               => array(
			'aliases' => array( '--color-theme-text' ),
		),
		'reign_site_headings_color'                => array(
			'aliases' => array( '--color-headings' ),
		),
		'reign_site_border_color'                  => array(
			'aliases' => array( '--color-border' ),
		),
		'reign_site_hr_color'                      => array(
			'aliases' => array(),
		),

		// Links.
		'reign_site_link_color'                    => array(
			'aliases' => array( '--color-link' ),
		),
		'reign_site_link_hover_color'              => array(
			'aliases' => array( '--color-link-hover' ),
		),

		// Header — desktop main menu.
		'reign_header_bg_color'                    => array(
			'aliases' => array( '--color-header-bg' ),
		),
		'reign_header_nav_bg_color'                => array(
			'aliases' => array(),
		),
		'reign_header_bg_color_on_scroll'          => array(
			'aliases' => array(),
		),
		'reign_header_main_menu_bg_hover_color'    => array(
			'aliases' => array(),
		),
		'reign_header_main_menu_bg_active_color'   => array(
			'aliases' => array(),
		),
		'reign_header_main_menu_text_hover_color'  => array(
			'aliases' => array( '--color-menu-hover' ),
		),
		'reign_header_main_menu_text_active_color' => array(
			'aliases' => array( '--color-menu-active' ),
		),

		// Footer widget area + copyright.
		'reign_footer_widget_area_bg_color'        => array(
			'aliases' => array(),
		),
		'reign_footer_widget_title_color'          => array(
			'aliases' => array( '--color-footer-title' ),
		),
		'reign_footer_widget_text_color'           => array(
			'aliases' => array( '--color-footer-content' ),
		),
		'reign_footer_widget_link_color'           => array(
			'aliases' => array( '--color-footer-link' ),
		),
		'reign_footer_widget_link_hover_color'     => array(
			'aliases' => array( '--color-footer-link-hover' ),
		),
		'reign_footer_copyright_bg_color'          => array(
			'aliases' => array( '--color-copyright-bg' ),
		),
		'reign_footer_copyright_text_color'        => array(
			'aliases' => array( '--color-copyright-content' ),
		),
		'reign_footer_copyright_link_color'        => array(
			'aliases' => array( '--color-copyright-link' ),
		),
		'reign_footer_copyright_link_hover_color'  => array(
			'aliases' => array( '--color-copyright-link-hover' ),
		),

		// Loader (Reign uses reign_preloading_bg_color).
		'reign_preloading_bg_color'                => array(
			'aliases' => array( '--color-theme-loader' ),
		),
	);

	/**
	 * Typography sub-key 'color' tokens. Map theme_mod array key => token+aliases.
	 *
	 * @var array<string, array{token:string, aliases:array<int,string>}>
	 */
	protected static array $typography_color_tokens = array(
		'site_title_typography_option'   => array(
			'aliases' => array( '--color-site-title' ),
		),
		'site_tagline_typography_option' => array(
			'aliases' => array( '--color-site-tagline' ),
		),
		'menu_typography_option'         => array(
			'aliases' => array( '--color-menu' ),
		),
		'site_sub_header_typography'     => array(
			'aliases' => array( '--color-subheader-title' ),
		),
		'typography_option'              => array(
			'aliases' => array( '--global-font-color' ),
		),
		'h1_typography_option'           => array(
			'aliases' => array( '--color-h1' ),
		),
		'h2_typography_option'           => array(
			'aliases' => array( '--color-h2' ),
		),
		'h3_typography_option'           => array(
			'aliases' => array( '--color-h3' ),
		),
		'h4_typography_option'           => array(
			'aliases' => array( '--color-h4' ),
		),
		'h5_typography_option'           => array(
			'aliases' => array( '--color-h5' ),
		),
		'h6_typography_option'           => array(
			'aliases' => array( '--color-h6' ),
		),
	);

	/**
	 * Radius/dimension tokens. Map theme_mod key => token+aliases.
	 *
	 * @var array<string, array{token:string, aliases:array<int,string>}>
	 */
	protected static array $dimension_tokens = array(
		'site_global_border_radius' => array(
			'token'   => '--bx-radius-global',
			'aliases' => array( '--global-border-radius' ),
		),
		'site_button_border_radius' => array(
			'token'   => '--bx-radius-button',
			'aliases' => array( '--button-border-radius' ),
		),
		'site_form_border_radius'   => array(
			'token'   => '--bx-radius-form',
			'aliases' => array( '--form-border-radius' ),
		),
	);

	/**
	 * Framework-supplied derived tokens (no customizer field backing them).
	 * Phase 4 introduces these to support stylesheet cleanup — they cover
	 * neutral roles (mid-tone text, generic borders, dividers, shadows)
	 * that didn't have a dedicated customizer color in 5.0.x but show up
	 * dozens of times across the Tier A foundation stylesheets.
	 *
	 * Always emitted, regardless of the `site_custom_colors` master toggle,
	 * so consumers (theme CSS + plugin compat CSS) can rely on them being
	 * present. Dark-mode overrides live in $dark_defaults below.
	 *
	 * Future 5.2.x: expose to customizer if customers want override control.
	 *
	 * @var array<string, string>
	 */
	protected static array $framework_tokens = array(
		// Color role tokens removed in 8.0.0 - --reign-* is the single source of
		// truth for color (inc/init.php load_color_palette + _dynamic-colors.scss
		// :root + the Site Skin overlay). Only the shadow-rgba bases survive here
		// because the (kept, non-color) shadow ladder below references them.
		'--bx-color-shadow'               => 'rgba(0, 0, 0, 0.08)',  // Card / popover shadow base.
		'--bx-color-shadow-strong'        => 'rgba(0, 0, 0, 0.16)',  // Modal / floating panel shadow.

		// Dimension — radius extras.
		'--bx-radius-card'                => '12px',
		'--bx-radius-pill'                => '999px',

		// Dimension — spacing.
		'--bx-space-section'              => 'clamp(40px, 8vw, 80px)',     // Vertical rhythm between sections.
		'--bx-space-card'                 => '24px',                       // Card / panel padding.
		'--bx-space-inline'               => '8px',                        // Inline gap (icon + text, etc.).
		'--bx-space-stack'                => '12px',                       // Vertical gap between siblings.

		// Effect — shadow ladder.
		'--bx-shadow-card-sm'             => '0 1px 2px 0 var(--bx-color-shadow)',
		'--bx-shadow-card-md'             => '0 4px 12px -2px var(--bx-color-shadow)',
		'--bx-shadow-card-lg'             => '0 12px 32px -4px var(--bx-color-shadow-strong)',

		// Effect — motion.
		'--bx-duration-fast'              => '120ms',
		'--bx-duration-base'              => '200ms',
		'--bx-duration-slow'              => '400ms',
		'--bx-easing-base'                => 'cubic-bezier(0.4, 0, 0.2, 1)',
		'--bx-easing-bounce'              => 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',

		// Z-index ladder.
		'--bx-z-base'                     => '1',
		'--bx-z-dropdown'                 => '100',
		'--bx-z-sticky-header'            => '999',
		'--bx-z-overlay'                  => '9999',
		'--bx-z-loader'                   => '999991',
		'--bx-z-toast'                    => '999999',
	);

	/**
	 * Legacy custom-property aliases. Colour aliases point at the canonical
	 * --reign-* vars (8.0.0 single source of truth); the radius aliases still
	 * point at the kept --bx-radius- tokens.
	 *
	 * Audit on 5.1.0 surfaced ~830 legacy --color- / --global- var references in
	 * source CSS that the simple_color_tokens / typography_color_tokens alias
	 * mechanism doesn't cover (those only fire for customer-customized values).
	 * Emitting these as `--legacy: var(--reign-...)` makes that CSS follow the
	 * single colour system (and flip in dark mode) without per-file edits.
	 *
	 * Each entry maps a legacy var to a token. build_token_css emits
	 * each alias as `--legacy: var(--bx-token);` in both :root (light)
	 * and the dark block so the legacy var flips colour-mode through
	 * the canonical token. 3rd-party integrations and legacy
	 * stylesheets keep working without per-file edits.
	 *
	 * @var array<string, string> legacy alias => canonical bx token
	 */
	protected static array $legacy_aliases = array(
		// Structural. Radius stays on the (kept) non-color --bx-radius family.
		'--global-border-color'        => '--reign-site-border-color',
		'--global-border-radius'       => '--bx-radius-global',
		'--global-border-radius-inner' => '--bx-radius-global',
		// Type. Reign uses one canonical headings color for h1-h6.
		'--global-title-color'         => '--reign-site-headings-color',
		'--color-h1'                   => '--reign-site-headings-color',
		'--color-h2'                   => '--reign-site-headings-color',
		'--color-h3'                   => '--reign-site-headings-color',
		'--color-h4'                   => '--reign-site-headings-color',
		'--color-h5'                   => '--reign-site-headings-color',
		'--color-h6'                   => '--reign-site-headings-color',
		// Surface
		'--color-theme-grey'           => '--reign-site-secondary-bg-color',
		'--color-block-bg-subtle'      => '--reign-site-secondary-bg-color',
		'--color-secondary-bg'         => '--reign-site-secondary-bg-color',
		// Body / meta text
		'--color-body-text'            => '--reign-site-body-text-color',
		'--color-meta'                 => '--reign-site-alternate-text-color',
		'--color-neutral'              => '--reign-site-alternate-text-color',
		// HR / divider
		'--color-hr'                   => '--reign-site-hr-color',
		// Quote
		'--color-quote-border'         => '--reign-colors-theme',
		'--color-quote-citation'       => '--reign-site-alternate-text-color',
		// Menu / panel
		'--color-menu'                 => '--reign-header-main-menu-font',
		'--color-menu-hover'           => '--reign-header-main-menu-text-hover-color',
		'--color-menu-active'          => '--reign-header-main-menu-text-active-color',
		'--color-panel-bg'             => '--reign-left-panel-bg-color',
		'--color-panel-bg-hover'       => '--reign-left-panel-menu-bg-hover-color',
		'--color-panel-bg-active'      => '--reign-left-panel-menu-bg-active-color',
		'--color-panel-menu'           => '--reign-left-panel-menu-font-color',
		'--color-panel-menu-hover'     => '--reign-left-panel-menu-hover-color',
		'--color-panel-menu-active'    => '--reign-left-panel-menu-active-color',
		// Footer / copyright
		'--color-footer-bg'            => '--reign-footer-widget-area-bg-color',
		'--color-footer-content'       => '--reign-footer-widget-text-color',
		'--color-footer-link'          => '--reign-footer-widget-link-color',
		'--color-footer-link-hover'    => '--reign-footer-widget-link-hover-color',
		'--color-footer-title'         => '--reign-footer-widget-title-color',
		'--color-copyright-bg'         => '--reign-footer-copyright-bg-color',
		'--color-copyright-content'    => '--reign-footer-copyright-text-color',
		'--color-copyright-link'       => '--reign-footer-copyright-link-color',
		'--color-copyright-link-hover' => '--reign-footer-copyright-link-hover-color',
	);

	/**
	 * Dark-mode block-editor palette overrides. Emitted via
	 * [data-bx-mode="dark"] :root { ... } and @media (prefers-color-scheme: dark)
	 * for 'auto' mode.
	 *
	 * 8.0.0: the old --bx-color-* dark palette was removed - Reign color renders
	 * through --reign-* (the [data-bx-mode="dark"] block in _dynamic-colors.scss
	 * is the dark source of truth, a dark Site Skin's --reign-* overlay layers on
	 * top, and legacy --color- / --global- aliases follow because they resolve to
	 * var(--reign-...)). Only these --wp--preset--color--* overrides remain here:
	 * block patterns read them via .has-{slug}-background-color / .has-{slug}-color
	 * and they have no --reign- equivalent, so dark mode must still invert them or
	 * pattern pages render light brand blocks on a near-black page.
	 *
	 * @var array<string, string>
	 */
	protected static array $dark_defaults = array(

		// theme.json palette overrides — block patterns reference these via.
		// .has-{slug}-background-color / .has-{slug}-color helpers, so we have
		// to invert the base/contrast scales for dark mode to take effect on
		// rendered blocks. Accent colors stay similar (slightly brighter for
		// dark contrast).
		'--wp--preset--color--base'       => '#0a0a0a',
		'--wp--preset--color--base-2'     => '#161616',
		'--wp--preset--color--base-3'     => '#1f1f1f',
		'--wp--preset--color--contrast'   => '#f5f5f5',
		'--wp--preset--color--contrast-2' => '#d0d0d0',
		'--wp--preset--color--contrast-3' => '#a0a0a0',
		'--wp--preset--color--surface-1'  => '#1a1310',
		'--wp--preset--color--surface-2'  => '#101a1c',
		'--wp--preset--color--surface-3'  => '#0f1419',
		'--wp--preset--color--primary'    => 'var(--reign-colors-theme)',

		// Brand / accent slugs missing pre-5.1.0. theme.json declares
		// these (settings.color.palette) and patterns use accent
		// extensively (8 pattern files reference has-accent-color in
		// 5.1.0). Without dark counterparts, pattern-driven pages
		// (home/landing) painted with light brand colors while the
		// theme chrome flipped dark — visible as red/teal blocks sitting
		// on a near-black page. Values match the dark palette baseline:
		// brand-red lifted (#ff5350) for dark-bg contrast, teal lifted
		// (#5aa3ae / #4a96a3), yellow stays (works on both modes).
		'--wp--preset--color--accent'     => '#ff5350',
		'--wp--preset--color--accent-2'   => '#4a96a3',
		'--wp--preset--color--accent-3'   => '#f4d35e',
		'--wp--preset--color--secondary'  => '#5aa3ae',
	);

	/**
	 * Whether boot() has already run (idempotency guard).
	 *
	 * @var bool
	 */
	protected static bool $booted = false;

	/**
	 * Hook the three emission paths that drive design-token output:
	 * inline :root tokens on wp_head, the FOUC-prevention <html
	 * data-bx-mode> script in <head>, and the variation typography
	 * filters that inject defaults into typography_option settings.
	 *
	 * Idempotent — safe on second call.
	 */
	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		$instance = new self();
		// Emit on wp_head priority 101 — AFTER Reign's inc/custom-styles.php
		// inline-CSS pipeline (which fires at priority 100 and emits
		// --reign-* CSS vars driven by reign_color_scheme). Without this
		// ordering the scheme-driven --reign-* values would override our
		// variation-driven --reign-* values for the same vars, making style
		// variations invisible on Reign's existing chrome (buttons / header /
		// links all read --reign-*). Tokens emit LAST in <head> so the Site
		// Skin overlay wins.
		add_action( 'wp_head', array( $instance, 'emit_tokens' ), 101 );
		// FOUC-prevention script stays at priority 1 so <html data-bx-mode>
		// is set before the browser paints. Independent of token emission.
		add_action( 'wp_head', array( $instance, 'emit_mode_script' ), 1 );
		// Hooked after init priority 10 so customizer fields are registered
		// before we read their defaults; filter is a no-op when no variation
		// is active or the customer has actively saved the field.
		add_action( 'init', array( __CLASS__, 'register_variation_theme_mod_filters' ), 20 );
		// One-time migration: purge color theme_mods that the BuddyX 5.0.x
		// alpha-color control init bug spuriously persisted as rgb()-format
		// equivalents of their declared field defaults. Runs once per site.
		// Mostly a no-op on Reign since the bug was BuddyX-specific, but kept
		// for forward-compat in case Reign customers migrate from BuddyX.
		add_action( 'init', array( __CLASS__, 'maybe_purge_alpha_color_pollution' ), 5 );
	}

	/**
	 * Snapshot of declared field defaults for every alpha-color customizer
	 * field as of 5.1.0. Used by the one-time pollution-purge migration to
	 * detect saved values that match the field's pre-5.1.0 default — those
	 * are pollution artifacts, not real customer customizations.
	 *
	 * @var array<string, string>
	 */
	protected static array $alpha_color_field_defaults_5_1_0 = array(
		'site_loader_bg'                      => '#1d76da',
		'site_primary_color'                  => '#1d76da',
		'site_header_bg_color'                => '#ffffff',
		'site_title_hover_color'              => '#1d76da',
		'menu_hover_color'                    => '#1d76da',
		'menu_active_color'                   => '#1d76da',
		'body_background_color'               => '#f7f7f9',
		'content_background_color'            => '#f7f7f9',
		'box_background_color'                => '#ffffff',
		'secondary_background_color'          => '#fafafa',
		'site_links_color'                    => '#111111',
		'site_links_focus_hover_color'        => '#1d76da',
		'site_buttons_background_color'       => '#1d76da',
		'site_buttons_background_hover_color' => '#1659b3',
		'site_buttons_text_color'             => '#ffffff',
		'site_buttons_text_hover_color'       => '#ffffff',
		'site_buttons_border_color'           => '#1d76da',
		'site_buttons_border_hover_color'     => '#1659b3',
		'site_footer_title_color'             => '#111111',
		'site_footer_content_color'           => '#505050',
		'site_footer_links_color'             => '#111111',
		'site_footer_links_hover_color'       => '#1d76da',
		'site_copyright_background_color'     => '#ffffff',
		'site_copyright_border_color'         => '#e8e8e8',
		'site_copyright_content_color'        => '#505050',
		'site_copyright_links_color'          => '#111111',
		'site_copyright_links_hover_color'    => '#1d76da',
	);

	/**
	 * Same pollution pattern, but for structured-array typography settings
	 * where the `color` sub-key got polluted. setting => default-hex map.
	 *
	 * @var array<string, string>
	 */
	protected static array $alpha_color_typography_subkey_defaults_5_1_0 = array(
		'site_title_typography_option'   => '#111111',
		'site_tagline_typography_option' => '#757575',
		'menu_typography_option'         => '#111111',
		'site_sub_header_typography'     => '#111111',
		'typography_option'              => '#505050',
		'h1_typography_option'           => '#111111',
		'h2_typography_option'           => '#111111',
		'h3_typography_option'           => '#111111',
		'h4_typography_option'           => '#111111',
		'h5_typography_option'           => '#111111',
		'h6_typography_option'           => '#111111',
	);

	/**
	 * One-time migration — purge alpha-color theme_mods that match their
	 * declared field default. The pre-5.1.0 alpha-color control fired
	 * `syncToSetting()` on `ready()`, which reformatted a hex-default into
	 * `rgb()` and called `setting.set()` on the customizer setting — marking
	 * it dirty. On the next customizer save, the framework persisted that
	 * "value" to theme_mods even though the customer never touched the
	 * field. Result: those saves emit at the end of the :root cascade and
	 * override the style-variation overlay, breaking presets like Dark.
	 *
	 * This migration walks every known alpha-color setting, compares the
	 * saved value (any format) to the declared default (any format) using a
	 * canonical RGB-tuple comparison, and removes the theme_mod when they
	 * match. Customer-set values that genuinely differ from the default are
	 * preserved. Runs once per site (gated by `_reign_alpha_color_purged`).
	 */
	public static function maybe_purge_alpha_color_pollution(): void {
		if ( get_theme_mod( '_reign_alpha_color_purged', false ) ) {
			return;
		}
		$purged = 0;
		foreach ( self::$alpha_color_field_defaults_5_1_0 as $setting => $default ) {
			$saved = get_theme_mod( $setting, null );
			if ( null === $saved || ! is_string( $saved ) || '' === $saved ) {
				continue;
			}
			if ( self::colors_canonically_equal( $saved, $default ) ) {
				remove_theme_mod( $setting );
				++$purged;
			}
		}
		// Structured-array typography settings — the `color` sub-key was the
		// only thing the alpha picker controlled, so we purge JUST that sub-key
		// and keep any other typography keys (font-family / weight / size)
		// the customer set intentionally.
		foreach ( self::$alpha_color_typography_subkey_defaults_5_1_0 as $setting => $default ) {
			$saved = get_theme_mod( $setting, null );
			if ( ! is_array( $saved ) || empty( $saved['color'] ) || ! is_string( $saved['color'] ) ) {
				continue;
			}
			if ( self::colors_canonically_equal( $saved['color'], $default ) ) {
				unset( $saved['color'] );
				if ( empty( $saved ) ) {
					remove_theme_mod( $setting );
				} else {
					set_theme_mod( $setting, $saved );
				}
				++$purged;
			}
		}
		set_theme_mod( '_reign_alpha_color_purged', 1 );
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( sprintf( '[Reign] alpha-color pollution purge removed %d theme_mods', $purged ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Compare two color strings (hex, rgb(), rgba()) by canonical RGB tuple.
	 *
	 * @param string $a Color value (hex, rgb(), rgba()).
	 * @param string $b Color value (hex, rgb(), rgba()).
	 * @return bool True when the colors resolve to the same RGB triplet AND
	 *              alpha within float-equality tolerance.
	 */
	protected static function colors_canonically_equal( string $a, string $b ): bool {
		$rgb_a = self::color_to_rgba_tuple( $a );
		$rgb_b = self::color_to_rgba_tuple( $b );
		if ( null === $rgb_a || null === $rgb_b ) {
			return false;
		}
		return $rgb_a[0] === $rgb_b[0]
			&& $rgb_a[1] === $rgb_b[1]
			&& $rgb_a[2] === $rgb_b[2]
			&& abs( $rgb_a[3] - $rgb_b[3] ) < 0.01;
	}

	/**
	 * Parse a color string into [R, G, B, A] integers (0-255) + float alpha.
	 *
	 * @param string $color Color value.
	 * @return array{0:int,1:int,2:int,3:float}|null
	 */
	protected static function color_to_rgba_tuple( string $color ): ?array {
		$color = trim( $color );
		if ( preg_match( '/^#([0-9a-f]{6})$/i', $color, $m ) ) {
			return array(
				(int) hexdec( substr( $m[1], 0, 2 ) ),
				(int) hexdec( substr( $m[1], 2, 2 ) ),
				(int) hexdec( substr( $m[1], 4, 2 ) ),
				1.0,
			);
		}
		if ( preg_match( '/^#([0-9a-f])([0-9a-f])([0-9a-f])$/i', $color, $m ) ) {
			return array(
				(int) hexdec( $m[1] . $m[1] ),
				(int) hexdec( $m[2] . $m[2] ),
				(int) hexdec( $m[3] . $m[3] ),
				1.0,
			);
		}
		if ( preg_match( '/^rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*,\s*([\d.]+))?\s*\)$/i', $color, $m ) ) {
			return array(
				(int) $m[1],
				(int) $m[2],
				(int) $m[3],
				isset( $m[4] ) ? (float) $m[4] : 1.0,
			);
		}
		return null;
	}

	/**
	 * Build the CSS variable block and emit it as an inline <style> block
	 * directly on wp_head priority 5 — early enough that Reign's many
	 * feature-specific stylesheets that consume the emitted vars can read them.
	 *
	 * Direct emit (rather than wp_add_inline_style) because Reign has no
	 * single "global" stylesheet handle to attach to.
	 */
	public function emit_tokens() {
		$css = $this->build_token_css();
		if ( '' !== $css ) {
			printf( "<style id='reign-tokens-css'>%s</style>\n", $css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Collect theme_mods for token generation, preview-aware.
	 *
	 * `get_theme_mods()` reads the raw `theme_mods_{$stylesheet}` option and is
	 * NOT filtered by the customizer preview, so previewed-but-unsaved colour
	 * and radius changes never reached the token CSS — the live preview only
	 * updated after Publish. `get_theme_mod()` (singular) IS preview-filtered,
	 * so during a customizer preview we overlay the previewed value for every
	 * token-feeding setting.
	 *
	 * @return array<string, mixed> theme_mod values keyed by setting id.
	 */
	protected function collect_mods(): array {
		$mods = (array) \get_theme_mods();

		if ( ! \is_customize_preview() ) {
			return $mods;
		}

		$keys = array_merge(
			array_keys( self::$simple_color_tokens ),
			array_keys( self::$typography_color_tokens ),
			array_keys( self::$dimension_tokens ),
			array( 'site_custom_colors', 'site_style_variation' )
		);
		foreach ( $keys as $key ) {
			$mods[ $key ] = \get_theme_mod( $key, $mods[ $key ] ?? null );
		}

		return $mods;
	}

	/**
	 * Build the full :root { … } CSS string from theme_mods.
	 * Public so Dynamic_Style/test code can call it directly.
	 *
	 * @return string CSS text (already includes the :root selector).
	 */
	public function build_token_css(): string {
		$enabled = \get_theme_mod( 'site_custom_colors', true );
		$mods    = $this->collect_mods();
		$decls   = '';

		// Framework-derived tokens always emit — they back generic neutrals.
		// (borders, mid-tone text, shadows) that consumer CSS depends on
		// regardless of whether the customer enabled custom colors.
		foreach ( self::$framework_tokens as $token => $value ) {
			$decls .= $token . ':' . $value . ';';
		}

		// Style variation overlay (Phase 7). Route to the correct cascade
		// layer based on the variation's own luminance:
		//
		// Light / default variation -> :root { … }     (becomes the light default)
		// Dark variation            -> [data-bx-mode="dark"] { … } (becomes the dark default)
		//
		// Why: prior to this routing, the variation overlay always painted
		// into :root, which meant a Dark variation locked the site to dark
		// surfaces regardless of the visitor's color-mode toggle. Visitors
		// who picked "light" via the toggle still saw dark pages and the
		// dark logo - the toggle was effectively non-functional. Routing
		// the dark variation into the [data-bx-mode="dark"] selector makes
		// the variation the dark *default* while leaving :root as the light
		// surface a visitor's light toggle can fall back to.
		//
		// resolve_style_variation_tokens() paints each variation palette slug
		// into the canonical --reign-* vars it should recolor (accent -> brand /
		// buttons / links / accent hovers, base -> body / header / footer
		// surfaces, contrast -> text / headings / menu, etc.) and emits the
		// --wp--preset--color--<slug> overrides so blocks/patterns repaint
		// too. $variation_covered tracks bases the variation already
		// painted so the downstream derive_for fallback (lines below)
		// doesn't clobber them with default-color variants when the
		// customer hasn't saved a value.
		//
		// Variation typography is injected into the Customizer Framework's
		// typography_option theme_mod defaults via
		// register_variation_theme_mod_filters() so the framework's
		// Output_Builder emits typography from a single source.
		$variation_covered = array();
		$variation_slug    = (string) ( $mods['site_style_variation'] ?? '' );
		$variation_decls   = '';
		$variation_is_dark = false;
		if ( '' !== $variation_slug ) {
			$variation_decls = self::resolve_style_variation_tokens( $variation_slug, $variation_covered );
			if ( '' !== $variation_decls ) {
				$variation_is_dark = (bool) self::active_variation_is_dark_scheme();
				if ( ! $variation_is_dark ) {
					// Light / default / unknown-luminance variation paints into
					// :root as before; this is the light default. Same behavior
					// the theme had pre-5.1.0-beta.3.
					$decls .= $variation_decls;
					// Variation no longer covers light tokens for the dark
					// cascade, so $variation_covered is correct as-is.
				}
				// For a dark variation we emit $variation_decls into the dark
				// cascade below via build_dark_block($variation_decls). $variation_covered
				// is cleared so the customer-default light derives in :root
				// can still paint a coherent light palette - otherwise the
				// theme would render *no* light-mode colors when the admin
				// picks Dark variation, breaking the visitor light toggle.
				if ( $variation_is_dark ) {
					$variation_covered = array();
				}
			}
		}

		// Site Custom Colors master toggle gates the customizer-derived tokens
		// for parity with 5.0.3 behavior. Framework tokens + variation overlay
		// above still emit.
		if ( ! $enabled ) {
			$decls      .= self::legacy_alias_declarations();
			$light_block = ':root{' . $decls . '}';
			$dark_block  = $this->build_dark_block( $variation_is_dark ? $variation_decls : '' );
			return $light_block . $dark_block;
		}

		// Simple hex color tokens. The inline tokens emit is the last :root
		// rule in the cascade for every page render — global.min.css declares
		// defaults, then this inline block overrides for customer-saved
		// values. Values that canonically equal the registered field default
		// are skipped so the style-variation overlay remains the source of
		// truth for tokens the customer has not personalised.
		foreach ( self::$simple_color_tokens as $mod_key => $cfg ) {
			$value = $mods[ $mod_key ] ?? '';
			if ( '' !== $value && is_string( $value ) ) {
				$default = self::$alpha_color_field_defaults_5_1_0[ $mod_key ] ?? null;
				if ( null !== $default && self::colors_canonically_equal( $value, $default ) ) {
					$value = '';
				}
			}
			$color = '' !== $value ? self::normalize_color( $value ) : '';
			if ( '' !== $color ) {
				// --bx-color-* token emission removed in 8.0.0 (single source of
				// truth is --reign-*). The per-field --color-*/--button-* aliases
				// stay for legacy / 3rd-party CSS that still references them. The
				// --bx -rgb/-hover/-active variant ladder is dropped - nothing
				// consumes it (verified at code level).
				foreach ( $cfg['aliases'] as $alias ) {
					$decls .= $alias . ':' . $color . ';';
				}
			}
		}

		// Typography sub-key 'color' tokens. Same default-equals-skip as
		// simple_color_tokens — structured typography arrays carry a `color`
		// sub-key whose registered default would otherwise re-override the
		// variation overlay's site-title / heading values.
		foreach ( self::$typography_color_tokens as $mod_key => $cfg ) {
			$value     = $mods[ $mod_key ] ?? array();
			$color_val = is_array( $value ) ? ( $value['color'] ?? '' ) : '';
			if ( '' === $color_val ) {
				continue;
			}
			$default = self::$alpha_color_typography_subkey_defaults_5_1_0[ $mod_key ] ?? null;
			if ( null !== $default && self::colors_canonically_equal( $color_val, $default ) ) {
				continue;
			}
			$color = self::normalize_color( $color_val );
			if ( '' === $color ) {
				continue;
			}
			// --bx-color-* token emission removed in 8.0.0 (single source --reign-*);
			// per-field aliases kept for legacy / 3rd-party CSS.
			foreach ( $cfg['aliases'] as $alias ) {
				$decls .= $alias . ':' . $color . ';';
			}
		}

		// Radius/dimension tokens.
		foreach ( self::$dimension_tokens as $mod_key => $cfg ) {
			$value = $mods[ $mod_key ] ?? '';
			if ( '' === $value || ! is_string( $value ) ) {
				continue;
			}
			$value = \sanitize_text_field( $value );
			// Defense in depth: sanitize_text_field strips tags but NOT the
			// CSS structural chars `{` `}` `;` that would let a crafted
			// dimension value break out of its declaration. Apply the
			// framework's CSS-value sanitizer as the second-layer gate before
			// concatenation.
			$value = \Reign\Customizer_Framework\Output_Builder::sanitize_css_value( $value );
			if ( '' === $value ) {
				continue;
			}
			$decls .= $cfg['token'] . ':' . $value . ';';
			foreach ( $cfg['aliases'] as $alias ) {
				$decls .= $alias . ':' . $value . ';';
			}
		}

		// Legacy alias shim - emit `--legacy: var(--bx-token);` so 3rd-
		// party and pre-token CSS rules referencing legacy var names
		// flip colour-mode through the canonical token automatically.
		// See $legacy_aliases doc-block for context.
		$decls .= self::legacy_alias_declarations();

		$light_block = ':root{' . $decls . '}';
		$dark_block  = $this->build_dark_block( $variation_is_dark ? $variation_decls : '' );

		return $light_block . $dark_block;
	}

	/**
	 * Build the `--legacy: var(--bx-token);` declaration string from
	 * the $legacy_aliases map. Aliases re-resolve every paint - the
	 * alias is mode-agnostic; the referenced --bx-* token does the
	 * actual light/dark flip. The same string is therefore safe to
	 * emit in both :root and the dark block (build_dark_block already
	 * handles its own dark variants via the simple_color_tokens
	 * alias mechanism).
	 */
	protected static function legacy_alias_declarations(): string {
		$out = '';
		foreach ( self::$legacy_aliases as $alias => $token ) {
			$out .= $alias . ':var(' . $token . ');';
		}
		return $out;
	}

	/**
	 * Build the dark-mode override block. Two selectors share the same body:
	 *   :root[data-bx-mode="dark"]                 — explicit user choice
	 *
	 *   @media (prefers-color-scheme: dark) :root[data-bx-mode="auto"]
	 *
	 * Each dark-default token also overrides its legacy aliases so any third-
	 * party CSS hooked to `--color-theme-primary` etc. picks up the dark
	 * value (otherwise legacy CSS would render light-mode colors on dark
	 * surfaces — a contrast failure).
	 *
	 * @param string $extra_decls Additional declarations to append AFTER the
	 *                            dark defaults so they win. Used by
	 *                            build_token_css() to route a dark style
	 *                            variation's palette into the dark cascade
	 *                            instead of stamping it onto :root.
	 */
	protected function build_dark_block( string $extra_decls = '' ): string {
		// Dark color renders through --reign-* (_dynamic-colors.scss
		// [data-bx-mode="dark"]); legacy --color-*/--global-* aliases follow via
		// var(--reign-*). The only output here is the --wp--preset--color--* block
		// palette dark overrides ($dark_defaults) - blocks/patterns read those and
		// have no --reign- equivalent. A dark Site Skin's --reign-* + --wp--preset
		// overlay arrives in $extra_decls and wins for the slugs it covers.
		$dark_decls = '';
		foreach ( self::$dark_defaults as $token => $value ) {
			$dark_decls .= $token . ':' . $value . ';';
		}
		if ( '' !== $extra_decls ) {
			$dark_decls .= $extra_decls;
		}
		if ( '' === $dark_decls ) {
			return '';
		}
		return ':root[data-bx-mode="dark"]{' . $dark_decls . '}'
			. '@media (prefers-color-scheme:dark){:root[data-bx-mode="auto"]{' . $dark_decls . '}}';
	}

	/**
	 * Resolve the color mode the page should render in on first paint.
	 *
	 * Single server-side source of truth for the initial mode, consumed by the
	 * FOUC bootstrap (emit_mode_script). Precedence:
	 *   1. Admin "Default Mode" (site_color_mode): light | dark | auto.
	 *   2. A dark Site Skin variation forces 'dark' UNLESS the admin deliberately
	 *      chose 'auto' (system-driven). 'light' is the registered field default,
	 *      not a deliberate light intent, so a dark palette still wins on first
	 *      load. A visitor can still toggle to light (persisted in localStorage)
	 *      when the switch is enabled — see visitor_choice_enabled().
	 *
	 * @return string One of 'light' | 'dark' | 'auto'.
	 */
	public static function resolve_initial_mode(): string {
		$mode = (string) \get_theme_mod( 'site_color_mode', 'light' );
		if ( ! in_array( $mode, array( 'light', 'dark', 'auto' ), true ) ) {
			$mode = 'light';
		}
		if ( 'auto' !== $mode && true === self::active_variation_is_dark_scheme() ) {
			$mode = 'dark';
		}
		return $mode;
	}

	/**
	 * Whether the visitor's stored (localStorage) mode choice should be honored.
	 *
	 * Only when the light/dark switch is actually shown. If the switch is hidden
	 * the visitor has no way to choose a mode, so a stale localStorage value
	 * (from earlier testing or a previously-enabled switch) must NOT override the
	 * admin's chosen Default Mode / Site Skin. The toggle JS is only enqueued
	 * when the switch is shown, so this is the sole guard for the hidden case.
	 *
	 * @return bool
	 */
	public static function visitor_choice_enabled(): bool {
		return \reign_is_truthy( \get_theme_mod( 'site_color_mode_toggle_show', 'on' ) );
	}

	/**
	 * Emit the FOUC-prevention bootstrap: set <html data-bx-mode> before paint.
	 *
	 * The single client-side decision point for the initial mode. The server
	 * default (resolve_initial_mode) is authoritative; a visitor's stored choice
	 * overrides it only when the switch is enabled (visitor_choice_enabled).
	 */
	public function emit_mode_script() {
		$default = self::resolve_initial_mode();
		$honor   = self::visitor_choice_enabled();
		?>
		<script id="buddyx-color-mode-bootstrap">
		(function(){
			var def = <?php echo \wp_json_encode( $default ); ?>;
			var honorVisitor = <?php echo $honor ? 'true' : 'false'; ?>;
			try {
				var saved = honorVisitor ? localStorage.getItem('bx-color-mode') : null;
				var mode = ( saved === 'light' || saved === 'dark' || saved === 'auto' ) ? saved : def;
				document.documentElement.setAttribute('data-bx-mode', mode);
			} catch (e) {
				document.documentElement.setAttribute('data-bx-mode', def);
			}
		})();
		</script>
		<?php
	}

	/**
	 * Public accessor for whether the active Site Skin variation is dark.
	 *
	 * Lets non-namespaced theme code (e.g. inc/theme-json-bridge.php) ask the
	 * same question the bootstrap script uses, without exposing the internal
	 * tri-state. Returns a plain bool (true only when the active variation is
	 * positively dark; false for light / typography-only / no variation).
	 *
	 * @return bool True when a dark-scheme variation is active.
	 */
	public static function is_active_variation_dark(): bool {
		return true === self::active_variation_is_dark_scheme();
	}

	/**
	 * Whether the active style variation is a dark-scheme palette.
	 *
	 * Reads the variation's `base` palette color and tests luminance. The
	 * variation's own background color is the single source of truth — no
	 * redundant metadata flag in styles/*.json that could drift from the
	 * palette. Returns null for typography-only variations (no palette) and
	 * when no variation is active.
	 *
	 * Filterable via `buddyx_variation_is_dark_scheme` so authors of custom
	 * variations whose base color doesn't reflect their scheme intent (or
	 * mu-plugin overrides) can force the answer. Filter receives the slug
	 * as the 2nd argument.
	 *
	 * @return bool|null true = dark, false = light, null = unknown / none.
	 */
	protected static function active_variation_is_dark_scheme(): ?bool {
		$variation_slug = (string) \get_theme_mod( 'site_style_variation', '' );
		if ( '' === $variation_slug ) {
			return null;
		}
		$override = \apply_filters( 'reign_variation_is_dark_scheme', null, $variation_slug );
		if ( null !== $override ) {
			return (bool) $override;
		}
		$data = self::load_variation_data( $variation_slug );
		if ( null === $data ) {
			return null;
		}
		$palette = $data['settings']['color']['palette'] ?? array();
		if ( ! is_array( $palette ) || empty( $palette ) ) {
			return null;
		}
		foreach ( $palette as $entry ) {
			if ( is_array( $entry ) && 'base' === ( $entry['slug'] ?? '' ) ) {
				$base_hex = (string) ( $entry['color'] ?? '' );
				return '' === $base_hex ? null : self::hex_is_dark( $base_hex );
			}
		}
		return null;
	}

	/**
	 * Whether a hex color reads as "dark" to a sighted viewer.
	 *
	 * Uses WCAG 2.1 relative luminance against the 0.5 midpoint. Accepts
	 * 3- or 6-digit hex with or without leading '#'. Returns false on
	 * malformed input so dark-scheme inference fails safe to "treat as
	 * light" (preserves pre-5.1.1 bootstrap behavior).
	 *
	 * @param string $hex Hex color, e.g. "#0F0F0F", "0f0f0f", "fff".
	 * @return bool True when relative luminance is below 0.5.
	 */
	protected static function hex_is_dark( string $hex ): bool {
		$hex = ltrim( trim( $hex ), '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
			return false;
		}
		$channel = static function ( float $c ): float {
			return $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		};

		$red   = hexdec( substr( $hex, 0, 2 ) ) / 255;
		$green = hexdec( substr( $hex, 2, 2 ) ) / 255;
		$blue  = hexdec( substr( $hex, 4, 2 ) ) / 255;
		$lum   = 0.2126 * $channel( $red ) + 0.7152 * $channel( $green ) + 0.0722 * $channel( $blue );

		return $lum < 0.5;
	}

	/**
	 * theme.json palette slug → list of canonical --reign-* vars the Site Skin
	 * should paint for that role. One role fans out to every --reign-* var that
	 * must track the same colour so the preset visibly applies (accent → brand,
	 * buttons, links, accent hovers; contrast → text, headings, site-title, menu;
	 * base → body / header / footer surfaces). Customer customizer saves still
	 * win because they emit later in the cascade.
	 *
	 * Adding a new token to a variation overlay = one line here. No other
	 * call site needs to change.
	 *
	 * @var array<string, array<int, string>>
	 */
	protected static array $variation_palette_targets = array(
		'accent'     => array(
			// Site Skin paints each palette role into the canonical --reign-* vars
			// (the single source of truth - load_color_palette() + _dynamic-colors.scss
			// read these). One role fans out to every --reign-* var it should recolor.
			'--reign-colors-theme',
			'--reign-accent-color',
			'--reign-site-button-bg-color',
			'--reign-site-link-color',
			'--reign-header-main-menu-text-hover-color',
			'--reign-header-main-menu-text-active-color',
			// Region 1: header chrome hover/accent states.
			'--reign-header-topbar-text-hover-color',
			'--reign-header-icon-hover-color',
			'--reign-header-sub-menu-text-hover-color',
			// Region 2: left panel hover/active text + active icon.
			'--reign-left-panel-menu-hover-color',
			'--reign-left-panel-menu-active-color',
			'--reign-left-panel-menu-icon-active-color',
			// Region 3/4: mobile menu hover/active text + form focus ring.
			'--reign-mobile-menu-hover-color',
			'--reign-mobile-menu-active-color',
			'--reign-form-focus-border-color',
			'--reign-footer-widget-link-hover-color',
			'--reign-footer-copyright-link-hover-color',
		),
		'accent-2'   => array(
			'--reign-accent-hover-color',
			'--reign-site-button-bg-hover-color',
			'--reign-site-link-hover-color',
		),
		'base'       => array(
			'--reign-site-sections-bg-color',
			'--reign-site-body-bg-color',
			'--reign-header-bg-color',
			'--reign-header-topbar-bg-color',
			'--reign-header-nav-bg-color',
			'--reign-left-panel-bg-color',
			'--reign-mobile-menu-bg-color',
			'--reign-footer-widget-area-bg-color',
			'--reign-footer-copyright-bg-color',
			// Button TEXT inverts the accent button background (page surface
			// contrasts with the accent fill: light on a dark accent, dark on a
			// bright one). NOT contrast - that is page-text and matches the page.
			'--reign-site-button-text-color',
			'--reign-site-button-text-hover-color',
		),
		'base-2'     => array(
			'--reign-site-secondary-bg-color',
			'--reign-form-background-color',
			// Region 1: dropdown / sub-menu surface (elevated).
			'--reign-header-sub-menu-bg-color',
			// Region 2: left panel tooltip surface (elevated).
			'--reign-left-panel-tooltip-bg-color',
			// Region 4: focused input surface.
			'--reign-form-focus-background-color',
		),
		'base-3'     => array(
			'--reign-site-border-color',
			'--reign-form-border-color',
			// Region 1: subtle menu hover/active backgrounds (text stays readable -
			// menu text-hover/active are in the accent role, so bg must NOT be accent).
			'--reign-header-sub-menu-bg-hover-color',
			'--reign-header-main-menu-bg-hover-color',
			'--reign-header-main-menu-bg-active-color',
			// Region 2: left panel subtle hover/active backgrounds (accent text stays readable).
			'--reign-left-panel-menu-bg-hover-color',
			'--reign-left-panel-menu-bg-active-color',
			// Region 3/5: mobile active bg + horizontal rule / divider.
			'--reign-mobile-menu-active-bg-color',
			'--reign-site-hr-color',
		),
		'contrast'   => array(
			// Menu / submenu FONT vars are the menu TEXT colours (they were
			// leaking the active scheme value - white menu text on a near-white
			// skin header). Their hover/active siblings live in accent and the
			// matching bg-hover/active siblings in base-3, so text and its
			// background never share a family.
			'--reign-header-main-menu-font',
			'--reign-header-sub-menu-font',
			'--reign-site-body-text-color',
			'--reign-site-headings-color',
			// Site title + tagline colour. Named "typography" but it holds a
			// COLOUR - it was leaking the scheme value, so the site title went
			// invisible (near-white) on light skins.
			'--reign-title-tagline-typography',
			'--reign-header-topbar-text-color',
			'--reign-header-icon-color',
			// Region 2: left panel foreground (toggle, menu text, tooltip text).
			'--reign-left-panel-toggle-color',
			'--reign-left-panel-menu-font-color',
			'--reign-left-panel-tooltip-color',
			// Region 3/4: mobile menu text + form input text.
			'--reign-mobile-menu-color',
			'--reign-form-text-color',
			'--reign-form-focus-text-color',
			'--reign-footer-widget-text-color',
			'--reign-footer-widget-title-color',
			'--reign-footer-widget-link-color',
			'--reign-footer-copyright-text-color',
			'--reign-footer-copyright-link-color',
		),
		'contrast-2' => array(
			// Region 4/5: muted text - placeholders + alternate text.
			'--reign-form-placeholder-color',
			'--reign-form-focus-placeholder-color',
			'--reign-site-alternate-text-color',
		),
	);

	/**
	 * Variation typography overlay routes through the Customizer Framework's
	 * existing typography_option settings. theme.json element key → matching
	 * BuddyX customizer setting that the framework already turns into CSS.
	 * The variation injects values via `theme_mod_<setting>` filters when
	 * the customer hasn't actively saved that setting; the framework then
	 * emits typography from one place (no parallel rules, no specificity
	 * tricks, customer customizer saves naturally win).
	 *
	 * Adding a new element = add one line here.
	 *
	 * @var array<string, string>
	 */
	protected static array $variation_typography_settings = array(
		'@body' => 'typography_option',
		'h1'    => 'h1_typography_option',
		'h2'    => 'h2_typography_option',
		'h3'    => 'h3_typography_option',
		'h4'    => 'h4_typography_option',
		'h5'    => 'h5_typography_option',
		'h6'    => 'h6_typography_option',
	);

	/**
	 * Theme.json typography sub-key → typography_option array sub-key the
	 * Customizer Framework's Output_Builder consumes. The framework already
	 * knows how to render each of these as a CSS declaration.
	 *
	 * @var array<string, string>
	 */
	protected static array $variation_typography_props = array(
		'fontFamily'     => 'font-family',
		'fontWeight'     => 'font-weight',
		'fontStyle'      => 'font-style',
		'fontSize'       => 'font-size',
		'letterSpacing'  => 'letter-spacing',
		'lineHeight'     => 'line-height',
		'textTransform'  => 'text-transform',
		'textDecoration' => 'text-decoration',
	);

	/**
	 * Read + parse a styles/<slug>.json variation file. Single read path
	 * shared by every consumer (token overlay, typography overlay, future
	 * settings consumers) so slug validation and file-read error handling
	 * live in exactly one place.
	 *
	 * @param string $slug Variation slug.
	 * @return array<string, mixed>|null Parsed JSON, or null if the slug is
	 *                                   invalid / file is missing / JSON is
	 *                                   malformed.
	 */
	protected static function load_variation_data( string $slug ): ?array {
		// Whitelist + safety: slug must be a single hyphen-or-letter token.
		if ( ! preg_match( '/^[a-z][a-z0-9-]{0,30}$/', $slug ) ) {
			return null;
		}
		$path = \get_template_directory() . '/styles/' . $slug . '.json';
		if ( ! is_readable( $path ) ) {
			return null;
		}
		// Local theme file path — direct read is correct here (a remote HTTP
		// fetch would be inappropriate for a bundled JSON file).
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$json = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $json ) {
			return null;
		}
		$data = json_decode( $json, true );
		return is_array( $data ) ? $data : null;
	}

	/**
	 * Read styles/<slug>.json and emit --bx-* token declarations from the
	 * variation's palette. Customer customizer-saved values OVERRIDE these
	 * downstream because the variation overlay is emitted earlier in the
	 * inline declarations block — same selector, later declaration wins
	 * via standard CSS cascade.
	 *
	 * Two layers of output, both driven by data:
	 *   1. For each palette entry, every --bx-* token registered in
	 *      $variation_palette_targets[ slug ] gets painted (with legacy
	 *      aliases and derived variants where applicable). One palette slug
	 *      can fan out to multiple BuddyX semantic tokens, e.g. accent →
	 *      accent + button-bg + link, contrast → fg + h1..h6 + site-title +
	 *      menu-fg + tagline + subheader-fg, base → bg + header-bg.
	 *   2. Each palette entry also emits --wp--preset--color--<slug> so block
	 *      patterns using `has-<slug>-background-color` repaint to the
	 *      variation's palette (otherwise theme.json's static palette wins
	 *      and patterns visually ignore the preset).
	 *
	 * @param string             $slug    Variation slug (e.g. 'dark', 'vibrant').
	 * @param array<int, string> $covered Out-param: --bx-* base tokens this
	 *                                    overlay painted (so the downstream
	 *                                    derive_for fallback skips them).
	 *                                    Use a throwaway array if not needed.
	 * @return string CSS declarations (no selector wrapper) or empty if the
	 *                variation file doesn't exist or has no palette.
	 */
	protected static function resolve_style_variation_tokens( string $slug, array &$covered = array() ): string {
		$data = self::load_variation_data( $slug );
		if ( null === $data ) {
			return '';
		}
		$palette = $data['settings']['color']['palette'] ?? array();
		if ( ! is_array( $palette ) || empty( $palette ) ) {
			return '';
		}

		// Per-element overrides win over the Site Skin: the skin is a starting
		// point, so it only paints a --reign-* var when the owner has NOT
		// explicitly set that element. (See element_color_is_overridden().)
		$scheme     = (string) \get_theme_mod( 'reign_color_scheme', 'reign_clean' );
		$saved_mods = (array) \get_option( 'theme_mods_' . \get_stylesheet(), array() );

		$decls = '';
		foreach ( $palette as $entry ) {
			$pal_slug = $entry['slug'] ?? '';
			$color    = $entry['color'] ?? '';
			if ( '' === $pal_slug || '' === $color ) {
				continue;
			}
			$normalized = self::normalize_color( (string) $color );
			if ( '' === $normalized ) {
				continue;
			}

			// Always emit the WordPress preset alias so blocks/patterns using
			// `has-<slug>-background-color` (or any
			// `var(--wp--preset--color--<slug>)` reference) repaint to the
			// variation's palette instead of theme.json's static value.
			// --wp--preset--color--primary is reserved as the brand alias
			// (var(--reign-colors-theme), emitted by theme-json-bridge.php) so
			// blocks + plugins ALWAYS match the theme's main accent - the colour
			// the owner picked, by whatever route (default/scheme/custom/skin).
			// A variation's separate "primary" swatch must not override it. Every
			// OTHER slug still emits its preset alias so patterns using
			// has-<slug>-background-color repaint to the active variation.
			if ( 'primary' !== $pal_slug ) {
				$decls .= '--wp--preset--color--' . $pal_slug . ':' . $normalized . ';';
			}

			// Paint every --reign-* var registered for this slug (declared in
			// $variation_palette_targets) - one slug fans out to several vars -
			// EXCEPT vars whose per-element colour the owner has overridden, so
			// explicit per-element colours always win over the skin.
			foreach ( self::$variation_palette_targets[ $pal_slug ] ?? array() as $bx_token ) {
				if ( self::element_color_is_overridden( $bx_token, $scheme, $saved_mods ) ) {
					continue;
				}
				$decls    .= self::emit_variation_token( $bx_token, $normalized );
				$covered[] = $bx_token;
			}
		}

		return $decls;
	}

	/**
	 * Whether the owner has explicitly set the per-element colour that a
	 * --reign-* var maps to - in which case the Site Skin overlay must NOT
	 * paint it (per-element colours win; the skin only fills untouched
	 * elements). --reign-X maps to the scheme-namespaced theme_mod
	 * `{scheme}-reign_X` that load_color_palette() reads.
	 *
	 * Checks saved theme_mods (front end) AND the customizer changeset (live
	 * preview) so an unsaved per-element edit also wins in the preview.
	 *
	 * @param string               $var        Target var (e.g. --reign-colors-theme).
	 * @param string               $scheme     Active reign_color_scheme slug.
	 * @param array<string, mixed> $saved_mods Raw theme_mods option.
	 * @return bool
	 */
	protected static function element_color_is_overridden( string $var, string $scheme, array $saved_mods ): bool {
		// Only --reign-* vars have a per-element customizer control.
		if ( 0 !== strpos( $var, '--reign-' ) ) {
			return false;
		}
		// --reign-header-topbar-bg-color -> reign_header_topbar_bg_color.
		$element_key = str_replace( '-', '_', substr( $var, 2 ) );
		$namespaced  = $scheme . '-' . $element_key;

		// Saved value (front end + published state).
		if ( isset( $saved_mods[ $namespaced ] ) && '' !== $saved_mods[ $namespaced ] ) {
			return true;
		}
		// Unsaved customizer edit (live preview): the dirty changeset value.
		if ( isset( $GLOBALS['wp_customize'] ) && is_object( $GLOBALS['wp_customize'] ) ) {
			$setting = $GLOBALS['wp_customize']->get_setting( $namespaced );
			if ( $setting ) {
				$posted = $setting->post_value( null );
				if ( null !== $posted && '' !== $posted ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Emit a base --bx-* token + every legacy alias registered for it +
	 * (when the base is in $derive_bases) the full derived-variants set.
	 * Aliases come from both $simple_color_tokens and $typography_color_tokens
	 * so any legacy --color-* / --global-font-color / etc. hooked into either
	 * namespace gets the same color.
	 *
	 * @param string $token --bx-* base token name.
	 * @param string $color Normalized CSS color.
	 * @return string CSS declarations.
	 */
	protected static function emit_variation_token( string $token, string $color ): string {
		// --bx-* targets are no longer emitted (8.0.0: single source of truth is
		// --reign-*). Skip them; only --reign-* targets (and the caller's
		// --wp--preset--color--* block aliases) carry the variation palette now.
		if ( 0 === strpos( $token, '--bx-' ) ) {
			return '';
		}
		// --reign-* target: emit it directly. (Legacy per-field aliases + the
		// --bx variant ladder are gone in 8.0.0 - legacy --color-/--global- vars
		// follow the skin via the $legacy_aliases shim -> var(--reign-*).)
		return $token . ':' . $color . ';';
	}

	/**
	 * Hook `theme_mod_<setting>` filters so the active variation's typography
	 * appears as the EFFECTIVE default for each typography_option setting.
	 * Customer customizer saves still win because the filter only fires when
	 * the customer hasn't actively saved that setting (key absent in raw
	 * theme_mods option). Result: variation flows through Output_Builder's
	 * existing typography emission — single source of truth, no specificity
	 * hacks, no parallel CSS rule output.
	 */
	public static function register_variation_theme_mod_filters(): void {
		$variation_slug = (string) \get_theme_mod( 'site_style_variation', '' );
		if ( '' === $variation_slug ) {
			return;
		}

		$overrides = self::resolve_variation_typography_overrides( $variation_slug );
		if ( empty( $overrides ) ) {
			return;
		}

		$saved_mods = \get_option( 'theme_mods_' . \get_stylesheet(), array() );
		if ( ! is_array( $saved_mods ) ) {
			$saved_mods = array();
		}

		foreach ( $overrides as $setting => $variation_value ) {
			// Customer has actively saved this setting → respect their save,
			// do not override. Variation is a "starting point" only.
			if ( array_key_exists( $setting, $saved_mods ) ) {
				continue;
			}
			\add_filter(
				"theme_mod_{$setting}",
				static function ( $current ) use ( $variation_value ) {
					if ( is_array( $current ) && is_array( $variation_value ) ) {
						return array_merge( $current, $variation_value );
					}
					return $variation_value;
				},
				5
			);
		}
	}

	/**
	 * Read styles/<slug>.json and translate `styles.typography` and
	 * `styles.elements.<tag>.typography` into typography_option-format
	 * arrays keyed by the corresponding customizer setting name. The
	 * framework's Output_Builder consumes these natively — see
	 * inc/Customizer_Framework/Output_Builder.php :: typography_declarations().
	 *
	 * @param string $slug Variation slug.
	 * @return array<string, array<string, string>> setting => partial value array.
	 */
	protected static function resolve_variation_typography_overrides( string $slug ): array {
		$data = self::load_variation_data( $slug );
		if ( null === $data ) {
			return array();
		}

		$overrides = array();
		foreach ( self::$variation_typography_settings as $element_key => $setting ) {
			$typo = '@body' === $element_key
				? ( $data['styles']['typography'] ?? null )
				: ( $data['styles']['elements'][ $element_key ]['typography'] ?? null );
			if ( ! is_array( $typo ) ) {
				continue;
			}
			$converted = self::convert_variation_typography_to_field_format( $typo );
			if ( ! empty( $converted ) ) {
				$overrides[ $setting ] = $converted;
			}
		}

		return $overrides;
	}

	/**
	 * Translate a theme.json typography sub-object (camelCase keys, e.g.
	 * fontFamily) into the kebab-case keys the Customizer Framework's
	 * Output_Builder expects (e.g. font-family). Only known keys flow
	 * through; values pass a strict sanitizer that rejects semicolons /
	 * braces / backslashes to keep the boundary defensible even though
	 * variation files ship in the theme.
	 *
	 * @param array<string, mixed> $typo theme.json typography sub-object.
	 * @return array<string, string> Field-format value array.
	 */
	protected static function convert_variation_typography_to_field_format( array $typo ): array {
		$converted = array();
		foreach ( self::$variation_typography_props as $json_key => $field_key ) {
			if ( ! isset( $typo[ $json_key ] ) ) {
				continue;
			}
			$raw = $typo[ $json_key ];
			if ( ! is_string( $raw ) && ! is_int( $raw ) && ! is_float( $raw ) ) {
				continue;
			}
			$value = trim( (string) $raw );
			if ( '' === $value || preg_match( '/[;{}<>\\\\]/', $value ) ) {
				continue;
			}
			$converted[ $field_key ] = $value;
		}
		return $converted;
	}


	/**
	 * Normalize a color value (hex or rgb/rgba string) to a safe CSS string.
	 * Returns '' if the value is unrecognized.
	 *
	 * @param string $value Color string from a theme_mod or variation file.
	 * @return string Normalized CSS color, or '' on parse failure.
	 */
	protected static function normalize_color( string $value ): string {
		$value = trim( $value );
		// Hex shorthand or full form.
		if ( preg_match( '/^#([0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value ) ) {
			return strtolower( $value );
		}
		// rgb()/rgba()/hsl()/hsla() — pass through if syntactically reasonable.
		if ( preg_match( '/^(rgba?|hsla?)\s*\(\s*[\d\s,.\-%\/]+\s*\)$/i', $value ) ) {
			return $value;
		}
		// CSS keyword (transparent, currentColor, etc.).
		if ( preg_match( '/^[a-z]{3,32}$/i', $value ) ) {
			return $value;
		}
		return '';
	}

	/**
	 * Public accessor for $simple_color_tokens — used by tests and the
	 * docs-generator to enumerate the customizer color field set.
	 *
	 * @return array<string, array{token:string, aliases:array<int,string>}>
	 */
	public static function get_simple_color_tokens(): array {
		return self::$simple_color_tokens;
	}

	/**
	 * Public accessor for $typography_color_tokens.
	 *
	 * @return array<string, array{token:string, aliases:array<int,string>}>
	 */
	public static function get_typography_color_tokens(): array {
		return self::$typography_color_tokens;
	}

	/**
	 * Public accessor for $dimension_tokens.
	 *
	 * @return array<string, array{token:string, aliases:array<int,string>}>
	 */
	public static function get_dimension_tokens(): array {
		return self::$dimension_tokens;
	}
}

// Boot is called explicitly from inc/customizer-framework-bootstrap.php
// :: reign_boot_tokens_modules() on after_setup_theme priority 7.

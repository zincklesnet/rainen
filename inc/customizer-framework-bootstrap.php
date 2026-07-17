<?php
/**
 * Customizer Framework Bootstrap
 *
 * Replaces Kirki with the in-house framework ported from BuddyX 5.1.0.
 *
 * Provides:
 *   - PSR-4 autoloader for Reign\Customizer_Framework\* and Reign\Fonts\*
 *   - reign_is_truthy() helper (handles legacy 'on'/'off' switch theme_mods)
 *   - Component::boot() call
 *
 * @package reign
 * @since 8.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * PSR-4 autoloader scoped to Reign\* namespaces shipped under inc/.
 *
 * Maps:
 *   Reign\Customizer_Framework\Component        → inc/Customizer_Framework/Component.php
 *   Reign\Customizer_Framework\Controls\Color   → inc/Customizer_Framework/Controls/Color.php
 *   Reign\Fonts\Google_Fonts_Catalog            → inc/Fonts/Google_Fonts_Catalog.php
 *
 * Composer autoloader takes precedence if present.
 *
 * @param string $class Fully qualified class name.
 */
function reign_psr4_autoload( $class ) {
	$prefixes = array(
		'Reign\\Customizer_Framework\\' => REIGN_THEME_DIR . '/inc/Customizer_Framework/',
		'Reign\\Fonts\\'                => REIGN_THEME_DIR . '/inc/Fonts/',
		'Reign\\Tokens\\'               => REIGN_THEME_DIR . '/inc/Tokens/',
		'Reign\\Color_Mode_Toggle\\'    => REIGN_THEME_DIR . '/inc/Color_Mode_Toggle/',
		'Reign\\Starter_Content\\'      => REIGN_THEME_DIR . '/inc/Starter_Content/',
		'Reign\\REST\\'                 => REIGN_THEME_DIR . '/inc/REST/',
	);

	foreach ( $prefixes as $prefix => $base_dir ) {
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			continue;
		}
		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
		if ( is_readable( $file ) ) {
			require_once $file;
		}
		return;
	}
}

// Load Composer's autoloader when present (covers dev dependencies such as
// PHPCS). It does NOT map the theme's own Reign\* namespaces — composer.json
// ships no `autoload` section — so it cannot resolve Reign\Customizer_Framework\*.
if ( file_exists( REIGN_THEME_DIR . '/vendor/autoload.php' ) ) {
	require_once REIGN_THEME_DIR . '/vendor/autoload.php';
}

// Always register our PSR-4 loader for the theme's own Reign\* namespaces.
// This MUST run regardless of whether vendor/ exists: when Composer has been
// installed (e.g. for dev tooling) its autoloader has no Reign\* mapping, so
// skipping our loader caused a fatal — "Class Reign\Customizer_Framework\Section
// not found" — on the first framework call. Registering both is safe: spl_autoload
// chains loaders and each only handles its own prefixes.
spl_autoload_register( 'reign_psr4_autoload' );

/**
 * Coerce any of WP's truthiness representations to a real bool.
 *
 * Why: Kirki saved Switch/Checkbox field values as legacy strings 'on'/'off'
 * interchangeably with int 1/0 and bool true/false depending on its version.
 * `(bool) 'off' === true` and `(int) 'on' === 0` are both PHP gotchas that
 * silently flip the meaning of `if ( get_theme_mod( $key ) )`. Use this helper
 * everywhere a saved theme_mod feeds an `if` branch on a Switch/Checkbox field.
 *
 * Matches `Reign\Customizer_Framework\Active_Callback::values_equal()` so
 * customizer control visibility (admin) and template render (front-end) agree.
 *
 * @param mixed $value Raw value, typically `get_theme_mod( $key )` output.
 * @return bool
 */
function reign_is_truthy( $value ) {
	if ( is_bool( $value ) ) {
		return $value;
	}
	if ( is_numeric( $value ) ) {
		return (int) $value === 1;
	}
	if ( is_string( $value ) ) {
		$value = strtolower( trim( $value ) );
		return in_array( $value, array( '1', 'on', 'yes', 'true' ), true );
	}
	return (bool) $value;
}

/**
 * wp_kses_post() plus the inline-SVG icon vocabulary.
 *
 * Why: wp_kses_post() strips <svg>/<use>/<path> entirely. Plugin integrations
 * (WC Vendors star ratings + social icons via wcv_get_icon(), and any other
 * sprite-based icon system) emit exactly that markup, so escaping their
 * output with wp_kses_post() silently deletes the icons — the 8.0.0
 * "vendor rating and social icons missing" regression. Use this helper
 * instead of wp_kses_post() whenever third-party HTML may carry SVG icons.
 *
 * @param string $html HTML potentially containing inline SVG icons.
 * @return string Sanitized HTML with SVG icon elements preserved.
 */
function reign_kses_post_with_icons( $html ) {
	$svg_tags = array(
		'svg'     => array(
			'class'           => true,
			'xmlns'           => true,
			'xmlns:xlink'     => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'aria-hidden'     => true,
			'aria-labelledby' => true,
			'role'            => true,
			'focusable'       => true,
			'fill'            => true,
		),
		'use'     => array(
			'href'       => true,
			'xlink:href' => true,
		),
		'title'   => array( 'id' => true ),
		'path'    => array(
			'd'               => true,
			'fill'            => true,
			'fill-rule'       => true,
			'clip-rule'       => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
		),
		'circle'  => array(
			'cx'     => true,
			'cy'     => true,
			'r'      => true,
			'fill'   => true,
			'stroke' => true,
			'class'  => true,
		),
		'rect'    => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
			'fill'   => true,
			'class'  => true,
		),
		'line'    => array(
			'x1'     => true,
			'y1'     => true,
			'x2'     => true,
			'y2'     => true,
			'stroke' => true,
			'class'  => true,
		),
		'polygon' => array(
			'points' => true,
			'fill'   => true,
			'class'  => true,
		),
		'g'       => array(
			'fill'      => true,
			'transform' => true,
			'class'     => true,
		),
	);

	$allowed = array_merge( wp_kses_allowed_html( 'post' ), $svg_tags );

	/**
	 * Filters the allowed HTML for icon-carrying third-party output.
	 *
	 * @param array $allowed kses allowed-tags array (post set + SVG icons).
	 */
	$allowed = apply_filters( 'reign_kses_post_with_icons_allowed_html', $allowed );

	$html = wp_kses( $html, $allowed );

	// Defense in depth: kses protocol-filters href but (empirically) not
	// xlink:href. Browsers don't execute javascript: in <use> targets, but
	// strip bad protocols anyway so the attribute can only carry a real URL
	// or a #fragment sprite reference.
	$html = preg_replace_callback(
		'/xlink:href\s*=\s*("|\')(.*?)\1/i',
		function ( $m ) {
			return 'xlink:href=' . $m[1] . wp_kses_bad_protocol( $m[2], wp_allowed_protocols() ) . $m[1];
		},
		$html
	);

	return $html;
}

/**
 * Bridge the legacy "Classic Dark Mode" switch to the new color-mode toggle.
 *
 * In 8.0.0 the legacy renderer (old inc/extras.php markup + dark-mode.min.js)
 * was removed. The visitor-facing light/dark/auto toggle now lives in
 * Reign\Color_Mode_Toggle\Component, gated solely by the `site_color_mode_toggle_show`
 * theme_mod (see Component::is_enabled(), which runs
 * reign_is_truthy( get_theme_mod( 'site_color_mode_toggle_show', 'on' ) )).
 *
 * Sites that previously enabled `reign_dark_mode_option` ("Classic Dark Mode")
 * but never touched the new "Show color-mode toggle" setting would suddenly
 * show NO frontend toggle. To preserve their working toggle we bridge the two:
 * when the legacy switch is truthy, this filter makes the new gate read as
 * enabled. Non-destructive — it filters the value at read time via
 * `theme_mod_*`, writes nothing to the DB, and is fully reversible (turn the
 * legacy switch off, or remove this filter). If the owner has explicitly set
 * the new toggle, that saved value still flows through whenever the legacy
 * switch is off.
 *
 * Returns the string 'on' because reign_is_truthy() accepts it and it matches
 * the saved format of the Customizer_Framework `switch` field (choices on/off)
 * as well as the component's own get_theme_mod() default of 'on'.
 *
 * @since 8.0.0
 *
 * @param mixed $value Current `site_color_mode_toggle_show` theme_mod value.
 * @return mixed 'on' when the legacy dark-mode switch is enabled, else $value.
 */
add_filter(
	'theme_mod_site_color_mode_toggle_show',
	function ( $value ) {
		if ( reign_is_truthy( get_theme_mod( 'reign_dark_mode_option' ) ) ) {
			return 'on';
		}
		return $value;
	}
);

/**
 * Boot the Customizer Framework.
 *
 * Hooked to `after_setup_theme` priority 5 so it runs BEFORE the Kirki addon
 * and BEFORE any inc/Customizer_Settings/Fields/*.php files that will call
 * Field::add(). During Phase 1 of the migration, Kirki and the new framework
 * coexist; after the atomic sweep + vendor delete, Kirki is gone.
 *
 * @since 8.0.0
 */
function reign_boot_customizer_framework() {
	if ( ! class_exists( '\\Reign\\Customizer_Framework\\Component' ) ) {
		// Autoloader unable to find the class — log and bail.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Reign: Customizer_Framework\\Component not found via autoloader.' );
		}
		return;
	}

	\Reign\Customizer_Framework\Component::boot(
		array(
			'config_id'  => 'reign_customizer',
			'assets_url' => REIGN_THEME_URI,
		)
	);
}
add_action( 'after_setup_theme', 'reign_boot_customizer_framework', 5 );

/**
 * Boot the Design Tokens + Color Mode Toggle modules.
 *
 * These ride on after_setup_theme priority 7 (after the framework + field
 * loader at 5/6) so:
 *   - Tokens can read declared customizer field defaults when emitting CSS
 *   - Color_Mode_Toggle's enable check sees the registered setting
 *
 * Files are require()'d explicitly (not autoloaded) because each carries
 * its own add_action() registration at file scope that must execute before
 * after_setup_theme fires.
 */
function reign_boot_tokens_modules() {
	$tokens_file = REIGN_THEME_DIR . '/inc/Tokens/Component.php';
	if ( is_readable( $tokens_file ) ) {
		require_once $tokens_file;
		if ( class_exists( '\\Reign\\Tokens\\Component' ) ) {
			\Reign\Tokens\Component::boot();
		}
	}
	$toggle_file = REIGN_THEME_DIR . '/inc/Color_Mode_Toggle/Component.php';
	if ( is_readable( $toggle_file ) ) {
		require_once $toggle_file;
		if ( class_exists( '\\Reign\\Color_Mode_Toggle\\Component' ) ) {
			\Reign\Color_Mode_Toggle\Component::boot();
		}
	}
	$fonts_file = REIGN_THEME_DIR . '/inc/Fonts/Component.php';
	if ( is_readable( $fonts_file ) ) {
		require_once $fonts_file;
		if ( class_exists( '\\Reign\\Fonts\\Component' ) ) {
			\Reign\Fonts\Component::boot();
		}
	}
	$starter_file = REIGN_THEME_DIR . '/inc/Starter_Content/Component.php';
	if ( is_readable( $starter_file ) ) {
		require_once $starter_file;
		if ( class_exists( '\\Reign\\Starter_Content\\Component' ) ) {
			\Reign\Starter_Content\Component::boot();
		}
	}

	// Reign REST API ( /wp-json/reign/v1/* ). Standalone-WordPress
	// compatible — auth/signin, /signup, /magic-link, /nonces, /info,
	// /preferences/color-mode. See inc/REST/README.md.
	$rest_file = REIGN_THEME_DIR . '/inc/REST/Component.php';
	if ( is_readable( $rest_file ) ) {
		require_once $rest_file;
		if ( class_exists( '\\Reign\\REST\\Component' ) ) {
			\Reign\REST\Component::boot();
		}
	}
}
// Run on after_setup_theme priority 7, after the customizer framework boot
// (priority 5) and the field loader (priority 6) so the field registrations
// are accumulated and tokens can read declared field defaults.
add_action( 'after_setup_theme', 'reign_boot_tokens_modules', 7 );

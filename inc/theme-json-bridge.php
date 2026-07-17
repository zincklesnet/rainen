<?php
/**
 * Reign Theme JSON Bridge
 *
 * Syncs Reign's customizer color scheme to the WordPress block editor by
 * overriding --wp--preset--color--* CSS variables with the active scheme's
 * computed values. Reads `reign_color_scheme` theme_mod and resolves it
 * through `reign_color_scheme_set()` (defined in
 * inc/Customizer_Settings/customizer-defaults.php).
 *
 * Dark-mode overrides are now emitted by inc/Tokens/Component.php which
 * targets `:root[data-bx-mode="dark"]` and covers --wp--preset--color--*
 * vars in its $dark_defaults map. This bridge is light-mode only.
 *
 * @package reign
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Reign Theme JSON Bridge function.
 */
function reign_theme_json_bridge_init() {
	
	// Hook into WordPress head with high priority for reliable CSS injection
	add_action( 'wp_head', 'reign_theme_json_bridge_output_css', 1 );
	
	// Filter theme.json data for Gutenberg color palette
	add_filter( 'wp_theme_json_data_theme', 'reign_theme_json_bridge_filter_palette', 10 );
}

/**
 * Output CSS to WordPress head
 */
function reign_theme_json_bridge_output_css() {
	
	// Get current color scheme
	$current_scheme = get_theme_mod( 'reign_color_scheme', 'reign_clean' );
	
	// Get color schemes with fallback
	if ( function_exists( 'reign_color_scheme_set' ) ) {
		$color_schemes = reign_color_scheme_set();
		$scheme_colors = isset( $color_schemes[ $current_scheme ] ) 
			? $color_schemes[ $current_scheme ] 
			: $color_schemes['reign_clean'];
	} else {
		// Fallback colors if function doesn't exist
		$scheme_colors = array(
			'reign_site_sections_bg_color' => '#ffffff',
			'reign_site_body_text_color' => '#404855',
			'reign_colors_theme' => '#1d76da',
			'reign_accent_hover_color' => '#3c8ce6',
			'reign_site_secondary_bg_color' => '#f6f6f6',
		);
	}
	
	// Get actual colors from theme mods (user customizations)
	$base = get_theme_mod( $current_scheme . '-reign_site_sections_bg_color', $scheme_colors['reign_site_sections_bg_color'] );
	$contrast = get_theme_mod( $current_scheme . '-reign_site_body_text_color', $scheme_colors['reign_site_body_text_color'] );
	$primary = get_theme_mod( $current_scheme . '-reign_colors_theme', $scheme_colors['reign_colors_theme'] );
	$secondary = get_theme_mod( $current_scheme . '-reign_accent_hover_color', $scheme_colors['reign_accent_hover_color'] );
	$tertiary = get_theme_mod( $current_scheme . '-reign_site_secondary_bg_color', $scheme_colors['reign_site_secondary_bg_color'] );
	
	// Calculate neutral color for better text hierarchy
	$neutral = reign_theme_json_bridge_mix_colors( $contrast, $base, 0.7 );

	// When the admin has selected a dark Site Skin variation, the dark cascade
	// owns --wp--preset--color--* on the front end (Component.php emits them in
	// :root[data-bx-mode="dark"] from $dark_defaults). This bridge must NOT
	// stamp the LIGHT preset vars onto `body` in that case: `body` is a closer
	// ancestor than `<html>` to block/pattern content, so a light value set on
	// `body` shadows the dark value set on :root[data-bx-mode="dark"] for
	// everything inside <body> — blocks/patterns render light on a dark page.
	//
	// We still emit for `.editor-styles-wrapper` so the block EDITOR (which has
	// no front-end data-bx-mode dark cascade) keeps the correct palette. On the
	// front end we only suppress the `:root, body` light vars when a dark
	// variation is active; light/default variations are unchanged.
	$variation_is_dark = class_exists( '\\Reign\\Tokens\\Component' )
		? \Reign\Tokens\Component::is_active_variation_dark()
		: false;
	$frontend_selector = $variation_is_dark ? '.editor-styles-wrapper' : ':root, body, .editor-styles-wrapper';

	?>
	<style id="reign-theme-json-bridge">
	/* Reign Theme JSON Bridge v2.1.0 - block-support presets now ALIAS the
		   --reign-* token system (single source of truth: --reign-colors-theme),
		   so --wp--preset--color--* track the active Site Skin accent + dark mode
		   automatically; the per-scheme hexes are kept only as fallbacks.
		   light mode only. Dark mode is handled
	   by inc/Tokens/Component.php via :root[data-bx-mode="dark"] overrides.
	   When a dark Site Skin variation is active the front-end :root/body light
	   preset vars are suppressed so they don't shadow the dark cascade. */
	<?php echo esc_html( $frontend_selector ); ?> {
		--wp--preset--color--base: var(--reign-site-sections-bg-color, <?php echo esc_attr( $base ); ?>);
		--wp--preset--color--contrast: var(--reign-site-body-text-color, <?php echo esc_attr( $contrast ); ?>);
		--wp--preset--color--primary: var(--reign-colors-theme, <?php echo esc_attr( $primary ); ?>);
		--wp--preset--color--secondary: var(--reign-accent-hover-color, <?php echo esc_attr( $secondary ); ?>);
		--wp--preset--color--tertiary: var(--reign-site-secondary-bg-color, <?php echo esc_attr( $tertiary ); ?>);
		--wp--preset--color--neutral: <?php echo esc_attr( $neutral ); ?>;
		--wp--preset--color--white: #ffffff;
	}
	</style>
	<?php
}

/**
 * Filter theme.json color palette for Gutenberg
 */
function reign_theme_json_bridge_filter_palette( $theme_json ) {
	
	// Get current colors (reuse logic from output function)
	$current_scheme = get_theme_mod( 'reign_color_scheme', 'reign_clean' );
	
	if ( function_exists( 'reign_color_scheme_set' ) ) {
		$color_schemes = reign_color_scheme_set();
		$scheme_colors = isset( $color_schemes[ $current_scheme ] ) 
			? $color_schemes[ $current_scheme ] 
			: $color_schemes['reign_clean'];
	} else {
		$scheme_colors = array(
			'reign_site_sections_bg_color' => '#ffffff',
			'reign_site_body_text_color' => '#404855',
			'reign_colors_theme' => '#1d76da',
			'reign_accent_hover_color' => '#3c8ce6',
			'reign_site_secondary_bg_color' => '#f6f6f6',
		);
	}
	
	$base = get_theme_mod( $current_scheme . '-reign_site_sections_bg_color', $scheme_colors['reign_site_sections_bg_color'] );
	$contrast = get_theme_mod( $current_scheme . '-reign_site_body_text_color', $scheme_colors['reign_site_body_text_color'] );
	$primary = get_theme_mod( $current_scheme . '-reign_colors_theme', $scheme_colors['reign_colors_theme'] );
	$secondary = get_theme_mod( $current_scheme . '-reign_accent_hover_color', $scheme_colors['reign_accent_hover_color'] );
	$tertiary = get_theme_mod( $current_scheme . '-reign_site_secondary_bg_color', $scheme_colors['reign_site_secondary_bg_color'] );
	$neutral = reign_theme_json_bridge_mix_colors( $contrast, $base, 0.7 );
	
	// Build new palette array for Gutenberg
	$new_palette = array(
		array(
			'color' => $base,
			'name'  => 'Base',
			'slug'  => 'base',
		),
		array(
			'color' => $contrast,
			'name'  => 'Contrast',
			'slug'  => 'contrast',
		),
		array(
			'color' => $primary,
			'name'  => 'Primary',
			'slug'  => 'primary',
		),
		array(
			'color' => $secondary,
			'name'  => 'Secondary',
			'slug'  => 'secondary',
		),
		array(
			'color' => $tertiary,
			'name'  => 'Tertiary',
			'slug'  => 'tertiary',
		),
		array(
			'color' => $neutral,
			'name'  => 'Neutral',
			'slug'  => 'neutral',
		),
		array(
			'color' => 'color-mix(in srgb, currentColor 10%, transparent)',
			'name'  => 'Transparent Accent',
			'slug'  => 'transparent-accent',
		),
		array(
			'color' => '#ffffff',
			'name'  => 'White',
			'slug'  => 'white',
		),
	);
	
	// Update theme.json data
	$data = $theme_json->get_data();
	$data['settings']['color']['palette'] = $new_palette;
	
	return $theme_json->update_with( $data );
}

/**
 * Mix two hex colors for neutral color calculation
 */
function reign_theme_json_bridge_mix_colors( $color1, $color2, $weight = 0.5 ) {
	$color1 = ltrim( $color1, '#' );
	$color2 = ltrim( $color2, '#' );
	
	$r1 = hexdec( substr( $color1, 0, 2 ) );
	$g1 = hexdec( substr( $color1, 2, 2 ) );
	$b1 = hexdec( substr( $color1, 4, 2 ) );
	
	$r2 = hexdec( substr( $color2, 0, 2 ) );
	$g2 = hexdec( substr( $color2, 2, 2 ) );
	$b2 = hexdec( substr( $color2, 4, 2 ) );
	
	$r = round( $r1 * $weight + $r2 * ( 1 - $weight ) );
	$g = round( $g1 * $weight + $g2 * ( 1 - $weight ) );
	$b = round( $b1 * $weight + $b2 * ( 1 - $weight ) );
	
	return sprintf( '#%02x%02x%02x', $r, $g, $b );
}

// Initialize the component
add_action( 'after_setup_theme', 'reign_theme_json_bridge_init', 15 );
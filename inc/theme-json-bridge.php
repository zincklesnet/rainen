<?php
/**
 * Reign Theme JSON Bridge
 *
 * Syncs Kirki customizer colors to WordPress block editor by overriding
 * --wp--preset--color--* CSS variables with Kirki's color scheme values.
 *
 * @package reign
 * @version 1.0.0
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
	
	?>
	<style id="reign-theme-json-bridge">
	/* Reign Theme JSON Bridge v1.0.0 */
	:root, body, .editor-styles-wrapper {
		--wp--preset--color--base: <?php echo esc_attr( $base ); ?>;
		--wp--preset--color--contrast: <?php echo esc_attr( $contrast ); ?>;
		--wp--preset--color--primary: <?php echo esc_attr( $primary ); ?>;
		--wp--preset--color--secondary: <?php echo esc_attr( $secondary ); ?>;
		--wp--preset--color--tertiary: <?php echo esc_attr( $tertiary ); ?>;
		--wp--preset--color--neutral: <?php echo esc_attr( $neutral ); ?>;
		--wp--preset--color--white: #ffffff;
	}
	
	/* Dark mode overrides */
	.dark-mode body,
	body.dark-mode,
	html.dark-mode,
	html.dark-mode body,
	html.dark-mode .editor-styles-wrapper {
		--wp--preset--color--base: #1a2028;
		--wp--preset--color--contrast: #c5c8cd;
		--wp--preset--color--primary: #3772ff;
		--wp--preset--color--secondary: #2057d8;
		--wp--preset--color--tertiary: #2b323c;
		--wp--preset--color--neutral: color-mix(in srgb, #c5c8cd 70%, #1a2028);
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
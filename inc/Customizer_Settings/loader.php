<?php
/**
 * Reign Customizer Settings Loader
 *
 * Replaces the deleted lib/kirki-addon/kirki-addon.php as the loader for
 * Reign's customizer panel/section/field registrations. Each file in
 * inc/Customizer_Settings/Fields/ is a singleton with a trailing
 * ::instance() call, so requiring each file boots its registration.
 *
 * The conditional ones (BuddyPress + WooCommerce) gate on the same
 * plugin-active checks the old loader used, so customers without those
 * plugins active don't see panels they can't populate.
 *
 * Plus customizer-defaults.php (reset-to-default helper + color scheme
 * presets) loaded BEFORE any field file so the helper functions are
 * available during field default lookups.
 *
 * @package reign
 * @since 8.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load all field-registration files.
 *
 * Order is irrelevant - each file self-registers via singleton
 * ::instance() on require, then defers actual panel/section/field
 * registration to its own 'init' action hook.
 */
function reign_load_customizer_settings() {

	$dir = REIGN_THEME_DIR . '/inc/Customizer_Settings/Fields/';

	// Default-value helpers + color scheme presets. MUST load before field
	// files because Typography_Fields, Colors_Fields, etc. call these
	// helpers during their add_fields() hook to compute saved defaults.
	$defaults = REIGN_THEME_DIR . '/inc/Customizer_Settings/customizer-defaults.php';
	if ( is_readable( $defaults ) ) {
		require_once $defaults;
	}

	// One-time on-upgrade theme_mod migrations (e.g. Padding decomposition
	// from Kirki composite arrays → 4 sibling dimension fields). Each
	// migration is idempotent and tracks completion in a wp_option flag.
	$migrations = REIGN_THEME_DIR . '/inc/Customizer_Settings/migrations.php';
	if ( is_readable( $migrations ) ) {
		require_once $migrations;
	}

	// Universal fields — always loaded.
	$universal = array(
		// General
		'Site_Layout_Fields.php',
		'Site_Logo_Fields.php',
		'Typography_Fields.php',
		'Sub_Header_Fields.php',
		'Page_Mapping_Fields.php',
		'Custom_Code_Fields.php',
		'Login_Popup_Fields.php',
		'Site_Performance_Fields.php',
		// Header
		'Header_Fields.php',
		'Header_Sticky_Menu_Fields.php',
		'Header_Mobile_Menu_Fields.php',
		'Header_Topbar_Fields.php',
		'Left_Panel_Fields.php',
		// Footer
		'Footer_Fields.php',
		// Forms
		'Forms_Fields.php',
		// Colors / Dark mode
		'Colors_Fields.php',
		'Dark_Colors_Fields.php',
		'Dark_Mode_Fields.php',
		// Site Skin (style variation + visitor color-mode toggle, new in 8.0.0)
		'Site_Skin_Fields.php',
		// Plugins support panel
		'Plugins_Support_Fields.php',
		// Post types (registers BOTH static settings AND the dynamic per-CPT loop)
		'Post_Types_Fields.php',
		// WP login screen
		'WP_Login_Screen_Fields.php',
	);

	foreach ( $universal as $file ) {
		$path = $dir . $file;
		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}

	// Plugin-gated fields — only load if the parent plugin is active.
	if ( class_exists( 'BuddyPress' ) ) {
		$bp_path = $dir . 'BuddyPress_Fields.php';
		if ( is_readable( $bp_path ) ) {
			require_once $bp_path;
		}
	}

	if ( class_exists( 'WooCommerce' ) ) {
		$wc_path = $dir . 'WooCommerce_Fields.php';
		if ( is_readable( $wc_path ) ) {
			require_once $wc_path;
		}
	}
}

// Load on after_setup_theme priority 6 — runs AFTER the framework boot
// (priority 5 in customizer-framework-bootstrap.php) but BEFORE any
// 'init' action where field-registration hooks fire.
add_action( 'after_setup_theme', 'reign_load_customizer_settings', 6 );

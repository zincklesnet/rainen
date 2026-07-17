<?php
/**
 * Reign 8.0.0 colour setup + migration runner.
 *
 * Keeps the colour system in a coherent state for every site scenario:
 *
 *   - Upgrade from 7.x : existing colours are preserved into the 8.0.0
 *                        scheme-namespaced structure by reign_color_scheme()
 *                        (inc/init.php) — no colour is lost.
 *   - Fresh install    : the default colour scheme is set so the Customizer
 *                        palette picker shows a selected baseline and the site
 *                        matches the demo (instead of looking unconfigured).
 *   - Reset / restore  : the cached colour CSS is refreshed (see also
 *                        Reign_Theme_Structure::clear_inline_css_cache()).
 *
 * The colour-preservation walk is gated by the `update_reign_theme` option, so
 * it normally runs once. This module makes the whole flow RE-RUNNABLE on demand
 * — via theme activation, a dismissible admin notice, and a WP-CLI command —
 * because demo / staging installs reuse an already-active theme and never fire
 * `after_switch_theme`, so the one-shot migration would otherwise never run in
 * those environments.
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'reign_color_setup_default_scheme' ) ) :
	/**
	 * Canonical default colour scheme.
	 *
	 * Matches every read-time fallback in the theme
	 * (`get_theme_mod( 'reign_color_scheme', 'reign_clean' )`).
	 *
	 * @return string
	 */
	function reign_color_setup_default_scheme() {
		return (string) apply_filters( 'reign_color_setup_default_scheme', 'reign_clean' );
	}
endif;

if ( ! function_exists( 'reign_color_setup_is_complete' ) ) :
	/**
	 * Whether the colour setup/migration has already completed on this site.
	 *
	 * @return bool
	 */
	function reign_color_setup_is_complete() {
		return (bool) get_option( 'update_reign_theme' );
	}
endif;

if ( ! function_exists( 'reign_run_color_setup' ) ) :
	/**
	 * Run (or re-run) the colour migration + first-run defaults, then refresh
	 * the cached colour CSS. Idempotent.
	 *
	 * @param bool $force Re-open the one-shot migration gate and run again even
	 *                    when it has completed before (used by the manual
	 *                    admin-notice / CLI triggers).
	 * @return void
	 */
	function reign_run_color_setup( $force = false ) {
		if ( $force ) {
			delete_option( 'update_reign_theme' );
		}

		// First-run default: ensure a scheme is selected so the picker shows a
		// baseline and the site matches the demo. Read-time code already falls
		// back to this value; persisting it makes the selection explicit.
		if ( '' === (string) get_theme_mod( 'reign_color_scheme', '' ) ) {
			set_theme_mod( 'reign_color_scheme', reign_color_setup_default_scheme() );
		}

		// Run the proven colour-preservation walk (flat -> scheme-namespaced).
		// It self-gates on the `update_reign_theme` option, which we cleared
		// above when forcing.
		if ( function_exists( 'reign_color_scheme' ) ) {
			reign_color_scheme();
		}

		// reign_color_scheme()'s fresh-install branch can leave the flag unset;
		// finalise it here so the one-shot does not re-run on every request.
		if ( ! get_option( 'update_reign_theme' ) ) {
			update_option( 'update_reign_theme', true );
		}

		// Persist the remaining (structural) customizer defaults so a fresh /
		// demo site has explicit, demo-matching values instead of relying on
		// read-time fallbacks that leave customizer controls looking unset.
		reign_set_all_customizer_defaults();

		// Refresh cached colour CSS so the new state renders immediately.
		if ( class_exists( 'Reign_Theme_Structure' ) ) {
			Reign_Theme_Structure::instance()->clear_inline_css_cache();
		} else {
			foreach ( array(
				'reign_theme_color_css',
				'reign_custom_styles_css',
				'reign_inline_palette_css',
				'reign_inline_dark_palette_css',
				'reign_inline_border_radius_css',
			) as $reign_color_transient ) {
				delete_transient( $reign_color_transient );
			}
		}
	}
endif;

if ( ! function_exists( 'reign_set_all_customizer_defaults' ) ) :
	/**
	 * Persist every registered customizer SETTING's default as a theme_mod when
	 * it is currently unset — so a fresh / demo install carries explicit default
	 * values (the customizer reflects a real saved state, matching the demo)
	 * instead of relying only on read-time get_theme_mod() fallbacks.
	 *
	 * SAFE BY DESIGN:
	 *  - Only fills keys that are NOT already present in theme_mods, so a
	 *    customer's (or a demo importer's) saved value is never overwritten.
	 *  - The value written is the field's own registered default, so the
	 *    rendered output is identical to the prior read-time fallback — this is
	 *    a value-neutral "make the default explicit" pass.
	 *  - SKIPS colour + typography fields: those are scheme-driven (their
	 *    effective default comes from reign_color_scheme_set() at render time,
	 *    not the field's static default), so persisting a static default could
	 *    override the active colour scheme. Colours are defaulted separately via
	 *    the colour scheme + reign_run_color_setup().
	 *  - SKIPS the `custom` divider/HTML pseudo-fields (they have no setting).
	 *
	 * @return int Number of defaults newly persisted.
	 */
	function reign_set_all_customizer_defaults() {
		if ( ! class_exists( '\Reign\Customizer_Framework\Component' ) ) {
			return 0;
		}

		$fields   = \Reign\Customizer_Framework\Component::get_fields();
		$existing = get_theme_mods();
		$existing = is_array( $existing ) ? $existing : array();
		$skip     = array( 'color', 'typography', 'custom' );
		$count    = 0;

		foreach ( $fields as $field ) {
			$setting_id = isset( $field['settings'] ) ? (string) $field['settings'] : '';
			$type       = isset( $field['_type'] ) ? (string) $field['_type'] : '';

			if ( '' === $setting_id || in_array( $type, $skip, true ) ) {
				continue;
			}
			if ( ! array_key_exists( 'default', $field ) ) {
				continue;
			}
			if ( array_key_exists( $setting_id, $existing ) ) {
				continue; // Never overwrite a saved customer / demo value.
			}

			$default = $field['default'];
			// Skip empty defaults — nothing meaningful to persist.
			if ( '' === $default || null === $default || array() === $default ) {
				continue;
			}

			set_theme_mod( $setting_id, $default );
			$existing[ $setting_id ] = $default; // Keep local map in sync for dup setting ids.
			++$count;
		}

		return $count;
	}
endif;

// Trigger 1 — real theme activation (the normal production path).
add_action( 'after_switch_theme', 'reign_run_color_setup' );

if ( ! function_exists( 'reign_color_setup_admin_notice' ) ) :
	/**
	 * Trigger 2 — dismissible-by-running admin notice fallback.
	 *
	 * Shown to theme managers only while the colour setup has not completed
	 * (e.g. demo / staging installs that reused an already-active theme and so
	 * never fired after_switch_theme). Running the setup clears the flag and
	 * the notice disappears.
	 *
	 * @return void
	 */
	function reign_color_setup_admin_notice() {
		if ( ! current_user_can( 'edit_theme_options' ) || reign_color_setup_is_complete() ) {
			return;
		}
		$reign_setup_url = wp_nonce_url(
			admin_url( 'admin-post.php?action=reign_run_color_setup' ),
			'reign_run_color_setup'
		);
		printf(
			'<div class="notice notice-info"><p>%s</p><p><a class="button button-primary" href="%s">%s</a></p></div>',
			esc_html__( 'Reign: finish the colour setup to apply your colour scheme and default values.', 'reign' ),
			esc_url( $reign_setup_url ),
			esc_html__( 'Run colour setup', 'reign' )
		);
	}
	add_action( 'admin_notices', 'reign_color_setup_admin_notice' );
endif;

if ( ! function_exists( 'reign_handle_run_color_setup' ) ) :
	/**
	 * Handle the admin-notice "Run colour setup" button.
	 *
	 * @return void
	 */
	function reign_handle_run_color_setup() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to run the colour setup.', 'reign' ) );
		}
		check_admin_referer( 'reign_run_color_setup' );
		reign_run_color_setup( true );
		set_transient( 'reign_color_setup_ran_notice', 1, 30 );
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
		exit;
	}
	add_action( 'admin_post_reign_run_color_setup', 'reign_handle_run_color_setup' );
endif;

if ( ! function_exists( 'reign_color_setup_success_notice' ) ) :
	/**
	 * Confirmation notice shown once after the setup is (re-)run from the admin
	 * notice or the Get Started "Re-run setup" button.
	 *
	 * @return void
	 */
	function reign_color_setup_success_notice() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		if ( get_transient( 'reign_color_setup_ran_notice' ) ) {
			delete_transient( 'reign_color_setup_ran_notice' );
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html__( 'Reign 8.0.0 setup re-applied. Your saved settings were preserved; any missing defaults have been restored.', 'reign' )
			);
		}
	}
	add_action( 'admin_notices', 'reign_color_setup_success_notice' );
endif;

// Trigger 3 — WP-CLI: `wp reign color-setup [--force]`.
if ( defined( 'WP_CLI' ) && WP_CLI && ! function_exists( 'reign_cli_color_setup' ) ) {
	/**
	 * Run the Reign colour setup/migration from WP-CLI.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Re-run even if the setup already completed on this site.
	 *
	 * @param array $args       Positional args (unused).
	 * @param array $assoc_args Associative args.
	 * @return void
	 */
	function reign_cli_color_setup( $args, $assoc_args ) {
		$force = isset( $assoc_args['force'] );
		reign_run_color_setup( $force );
		WP_CLI::success(
			sprintf(
				'Reign colour setup complete%s. Active scheme: %s',
				$force ? ' (forced)' : '',
				get_theme_mod( 'reign_color_scheme', reign_color_setup_default_scheme() )
			)
		);
	}
	WP_CLI::add_command( 'reign color-setup', 'reign_cli_color_setup' );
}

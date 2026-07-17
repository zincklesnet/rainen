<?php
/**
 * Reign Customizer Migrations
 *
 * One-time, on-upgrade `theme_mod` migrations. Each migration runs once
 * per site and tracks completion in a single wp_option flag bitmap so
 * adding more migrations doesn't multiply the option-row pressure.
 *
 * Migrations registered:
 *   v8_padding_decomposition  Splits Kirki Pro\Field\Padding composite
 *                             `{top,right,bottom,left}` arrays into 4
 *                             sibling single-edge dimension theme_mods.
 *                             Affected settings:
 *                               - reign_footer_copyright_spacing
 *                               - reign_footer_spacing
 *
 * Migrations run on `init` priority 1, BEFORE field-registration hooks
 * (priority 10) so the new sub-setting values are visible to the field
 * defaults / customizer preview the first time the customizer loads.
 *
 * @package reign
 * @since 8.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option key tracking which migrations have completed on this site.
 * Stores an associative array keyed by migration id; value is the
 * timestamp the migration ran. New migrations check this map before
 * running; completed migrations skip immediately.
 */
const REIGN_CUSTOMIZER_MIGRATIONS_OPTION = 'reign_customizer_migrations_v8';

/**
 * Read the migration-completion map.
 *
 * Statically memoized + autoloaded option (see migration_complete below
 * for the autoload=yes choice) so this never costs more than one DB hit
 * per request even when all three migrations call it.
 *
 * @return array<string, int>
 */
if ( ! function_exists( 'reign_customizer_migrations_state' ) ) :
	function reign_customizer_migrations_state(): array {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}
		$state = get_option( REIGN_CUSTOMIZER_MIGRATIONS_OPTION, array() );
		$cache = is_array( $state ) ? $state : array();
		return $cache;
	}
endif;

/**
 * Mark a migration as complete (stores timestamp).
 *
 * autoload = 'yes' so the migration-completion check on the next request
 * uses the prefetched alloptions cache instead of a separate DB read.
 * The option is tiny (3-row associative array) so the autoload cost is
 * negligible vs. the saved DB queries.
 */
if ( ! function_exists( 'reign_customizer_migration_complete' ) ) :
	function reign_customizer_migration_complete( string $id ): void {
		$state        = reign_customizer_migrations_state();
		$state[ $id ] = time();
		update_option( REIGN_CUSTOMIZER_MIGRATIONS_OPTION, $state, true );
	}
endif;

/**
 * Whether a migration has already run on this site.
 */
if ( ! function_exists( 'reign_customizer_migration_done' ) ) :
	function reign_customizer_migration_done( string $id ): bool {
		$state = reign_customizer_migrations_state();
		return ! empty( $state[ $id ] );
	}
endif;

/**
 * v8_padding_decomposition — split composite Kirki Padding theme_mods into
 * 4 single-edge dimension siblings.
 *
 * For each affected setting (e.g. `reign_footer_copyright_spacing`):
 *   - If a saved composite value exists AND the new sub-settings are unset,
 *     copy each edge value to `<setting>_top`, `<setting>_right`, etc.
 *   - The composite key itself is NOT removed - customer rollback to 7.9.9
 *     would still find the old value intact. The field registration in
 *     8.0.0 onwards reads from the 4 sub-keys, so the composite is dormant.
 *
 * Migration is idempotent. Safe to re-run; the inner guards prevent
 * double-migration.
 */
if ( ! function_exists( 'reign_migrate_v8_padding_decomposition' ) ) :
	function reign_migrate_v8_padding_decomposition(): void {
		if ( reign_customizer_migration_done( 'v8_padding_decomposition' ) ) {
			return;
		}

		$settings = array(
			'reign_footer_copyright_spacing' => array(
				'top'    => '20px',
				'right'  => '0px',
				'bottom' => '20px',
				'left'   => '0px',
			),
			'reign_footer_spacing'           => array(
				'top'    => '70px',
				'right'  => '0px',
				'bottom' => '70px',
				'left'   => '0px',
			),
		);

		$edges = array( 'top', 'right', 'bottom', 'left' );

		foreach ( $settings as $base_key => $default_map ) {
			$composite = get_theme_mod( $base_key );

			// Nothing to migrate if the customer never customized this setting.
			if ( false === $composite || empty( $composite ) || ! is_array( $composite ) ) {
				continue;
			}

			foreach ( $edges as $edge ) {
				$sub_key = $base_key . '_' . $edge;

				// Don't clobber a value the customer already set on the new sub-key
				// (e.g. they opened customizer post-upgrade before migration ran).
				if ( false !== get_theme_mod( $sub_key, false ) ) {
					continue;
				}

				$value = isset( $composite[ $edge ] ) ? (string) $composite[ $edge ] : (string) ( $default_map[ $edge ] ?? '' );
				if ( '' !== $value ) {
					set_theme_mod( $sub_key, $value );
				}
			}
		}

		reign_customizer_migration_complete( 'v8_padding_decomposition' );
	}
endif;

// Run on init priority 1 - before any add_fields() hooks (priority 10) that
// register the new sub-settings as fields.
add_action( 'init', 'reign_migrate_v8_padding_decomposition', 1 );

/**
 * v8_dark_mode_toggle_migration — bridge Reign's existing
 * reign_dark_mode_option (boolean toggle) to BuddyX 5.1.0's new
 * Color_Mode_Toggle setting set:
 *
 *   reign_dark_mode_option = true  ->  site_color_mode_toggle_show = 'on'
 *   reign_dark_mode_default = 'dark' -> site_color_mode = 'dark'
 *                          = 'system'-> site_color_mode = 'auto'
 *                          = 'light' -> site_color_mode = 'light'
 *
 * The old reign_dark_mode_option theme_mod stays in place — we never
 * delete it — so customers rolling back to 7.9.9 keep their original
 * dark-mode preference. Reign 8.0.0 onwards reads the new
 * site_color_mode_toggle_show setting.
 *
 * Idempotent; flagged complete after first successful run.
 */
if ( ! function_exists( 'reign_migrate_v8_dark_mode_toggle' ) ) :
	function reign_migrate_v8_dark_mode_toggle(): void {
		if ( reign_customizer_migration_done( 'v8_dark_mode_toggle' ) ) {
			return;
		}

		$old_enabled = get_theme_mod( 'reign_dark_mode_option', null );
		$old_default = get_theme_mod( 'reign_dark_mode_default', null );

		if ( null === $old_enabled && null === $old_default ) {
			// Customer never customized dark mode. Mark migration complete
			// so we don't re-check on every page load — the new defaults
			// ('on' for toggle visibility, 'light' for initial mode) apply.
			reign_customizer_migration_complete( 'v8_dark_mode_toggle' );
			return;
		}

		// Migrate toggle visibility flag, but only if the new setting is unset
		// (don't clobber a value the customer may have set post-upgrade).
		if ( null !== $old_enabled && false === get_theme_mod( 'site_color_mode_toggle_show', false ) ) {
			set_theme_mod(
				'site_color_mode_toggle_show',
				reign_is_truthy( $old_enabled ) ? 'on' : 'off'
			);
		}

		// Migrate default mode.
		if ( null !== $old_default && false === get_theme_mod( 'site_color_mode', false ) ) {
			$mode_map = array(
				'light'  => 'light',
				'dark'   => 'dark',
				'system' => 'auto',
				'auto'   => 'auto',
			);
			$mapped   = $mode_map[ (string) $old_default ] ?? 'light';
			set_theme_mod( 'site_color_mode', $mapped );
		}

		reign_customizer_migration_complete( 'v8_dark_mode_toggle' );
	}
endif;
add_action( 'init', 'reign_migrate_v8_dark_mode_toggle', 1 );

/**
 * v8_color_scheme_to_variation — REMOVED in 8.0.0 for 7.9.9 → 8.0.0 demo
 * color parity (Basecamp card 9962070752).
 *
 * This migration used to map each legacy `reign_color_scheme` slug to a
 * BuddyX-family `site_style_variation` slug (reign_default/reign_clean → cool,
 * reign_dating → warm, reign_ectoplasm → vibrant, …). That looked additive on
 * paper, but it is NOT parity-preserving in 8.0.0's single-source model:
 *
 *   `--reign-*` is now the single source of truth for color
 *   (inc/init.php load_color_palette()). When `site_style_variation` is set,
 *   the Site Skin overlay in inc/Tokens/Component.php
 *   (resolve_style_variation_tokens() via $variation_palette_targets) REPAINTS
 *   the canonical `--reign-*` vars with the variation's palette. So silently
 *   populating `site_style_variation` on upgrade would override the very
 *   `--reign-*` values that 7.9.9 rendered from the legacy scheme — changing a
 *   demo site's colors on a "use the same" upgrade.
 *
 * The single-source decision is: 7.9.9 → 8.0.0 color is "use the same". The
 * legacy `reign_color_scheme` (+ its per-scheme namespaced `{scheme}-reign_*`
 * theme_mods) already drives `--reign-*` identically to 7.9.9, and the
 * reign_color_scheme() namespacing migration in inc/init.php preserves that.
 * Site Skin (`site_style_variation`) therefore stays OPT-IN — never auto-set
 * from a legacy scheme — so upgraded sites render byte-identically and only
 * change color when the owner explicitly picks a Skin.
 *
 * No data is touched: legacy keys were never deleted by this migration, and we
 * no longer write `site_style_variation`, so removal is safe and reversible.
 */

/**
 * v8_left_panel_icon_size — convert the legacy Kirki Typography composite
 * `reign_left_panel_icon_typography` value `{ 'font-size' => 'NNpx' }` into
 * the scalar pixel integer the new slider control + Output_Builder expect.
 *
 * In 7.9.9 this setting was a Kirki Typography field that rendered only a
 * font-size input and stored an array. In 8.0.0 it is a `slider` field that
 * stores a scalar int and emits `font-size: NNpx`. Without this migration a
 * site carrying the old array value hits Output_Builder::rule_to_css()'s
 * `! is_scalar()` guard, emits no CSS, and the left-panel icon size silently
 * resets until the customizer is re-saved.
 *
 * The legacy array is overwritten in place with the scalar (same key) so the
 * slider + output rule work immediately. Idempotent; scalar/unset values are
 * left untouched.
 */
if ( ! function_exists( 'reign_migrate_v8_left_panel_icon_size' ) ) :
	function reign_migrate_v8_left_panel_icon_size(): void {
		if ( reign_customizer_migration_done( 'v8_left_panel_icon_size' ) ) {
			return;
		}

		$value = get_theme_mod( 'reign_left_panel_icon_typography', null );

		// Only a legacy array value needs converting. Scalar (already migrated or
		// freshly set) and unset values need nothing.
		if ( is_array( $value ) ) {
			$raw  = isset( $value['font-size'] ) ? (string) $value['font-size'] : '';
			$size = (int) preg_replace( '/[^0-9.]/', '', $raw );
			if ( $size <= 0 ) {
				$size = 18; // Theme default.
			}
			set_theme_mod( 'reign_left_panel_icon_typography', $size );
		}

		reign_customizer_migration_complete( 'v8_left_panel_icon_size' );
	}
endif;
add_action( 'init', 'reign_migrate_v8_left_panel_icon_size', 1 );

<?php
/**
 * Reign\Color_Mode_Toggle\Component class
 *
 * Visitor-facing color-mode toggle (light / dark / auto) that pairs with
 * the dark-mode token plumbing in inc/Tokens/Component.php.
 *
 * Admin enables this in Customizer → Skin → "Show color-mode toggle".
 * When enabled, a button renders in the header (and/or mobile menu)
 * letting visitors cycle Light → Dark → Auto. Choice is persisted in
 * localStorage so it survives navigations and reloads. Works for guest
 * visitors and logged-in members alike — no auth dependency.
 *
 * @package Reign */

namespace Reign\Color_Mode_Toggle;

// Component_Interface dropped during BuddyX → Reign port; module auto-boots
// via Component::boot() at end of file.
use function add_action;
use function reign_is_truthy;
use function get_theme_mod;
use function wp_enqueue_script;

defined( 'ABSPATH' ) || exit;

/**
 * Color mode toggle component.
 */
class Component {

	/**
	 * Whether boot() has already run (idempotency guard).
	 *
	 * @var bool
	 */
	protected static bool $booted = false;

	/**
	 * Hook in render points + asset enqueue. Idempotent.
	 *
	 * Reign uses `reign_after_user_menu_item` for the header (preserving the
	 * exact mount point Reign's existing dark-mode style 4 used) and falls
	 * back to wp_footer for sites where the menu hook doesn't fire.
	 */
	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
		$instance     = new self();
		// ONE mount per context. Reign fires reign_after_header_icons after
		// the header's icon-set sortable renders — that's a natural neighbour
		// for the toggle (sun/moon visually lives with the search/cart icons).
		// reign_after_user_menu_item was redundant + fired multiple times on
		// pages with multiple nav menus, producing 3-4 toggle instances.
		add_action( 'reign_after_header_icons', array( $instance, 'render_header_toggle' ), 50 );
		// Mobile mount inside the mobile header icon row (next to search / cart /
		// notifications) so the toggle aligns with the other header icons instead
		// of dropping below them as a full-width row.
		add_action( 'reign_after_mobile_header_icons', array( $instance, 'render_mobile_toggle' ), 50 );
		add_action( 'wp_enqueue_scripts', array( $instance, 'enqueue_assets' ), 30 );
	}

	/**
	 * Per-request render guards. Without these, even a single hook can
	 * fire multiple times on a page (e.g. reign_after_header_icons fires
	 * once for the main header + once per icon-bearing widget area), so
	 * we'd render the same toggle twice or more.
	 *
	 * @var array<string, bool>
	 */
	protected static array $rendered = array(
		'header' => false,
		'mobile' => false,
	);

	/**
	 * Whether the toggle is enabled site-wide.
	 *
	 * Customizer_Framework's `switch` field sanitizes the saved value to
	 * int 1/0 via sanitize_bool_int(), but legacy 5.0.x DBs (and the
	 * fresh-install fallback default) carry the literal string 'on'. Route
	 * through buddyx_is_truthy() so both shapes resolve correctly — matches
	 * inc/extra.php:238 which gates the same setting for header rendering.
	 */
	protected function is_enabled(): bool {
		return reign_is_truthy( get_theme_mod( 'site_color_mode_toggle_show', 'on' ) );
	}

	/**
	 * Position setting (header | mobile_only | both).
	 */
	protected function position(): string {
		$pos = (string) get_theme_mod( 'site_color_mode_toggle_position', 'both' );
		return in_array( $pos, array( 'header', 'mobile_only', 'both' ), true ) ? $pos : 'both';
	}

	/**
	 * Server-side initial mode (matches site default; client may override
	 * from localStorage on bootstrap).
	 */
	protected function initial_mode(): string {
		$mode = (string) get_theme_mod( 'site_color_mode', 'light' );
		return in_array( $mode, array( 'light', 'dark', 'auto' ), true ) ? $mode : 'light';
	}

	/**
	 * Header render hook.
	 */
	public function render_header_toggle(): void {
		if ( self::$rendered['header'] ) {
			return; // Already rendered this request — Reign's hook may fire multiple times.
		}
		if ( ! $this->is_enabled() ) {
			return;
		}
		if ( 'mobile_only' === $this->position() ) {
			return;
		}
		self::$rendered['header'] = true;
		$this->render( 'header' );
	}

	/**
	 * Mobile-menu render hook.
	 */
	public function render_mobile_toggle(): void {
		if ( self::$rendered['mobile'] ) {
			return;
		}
		if ( ! $this->is_enabled() ) {
			return;
		}
		if ( 'header' === $this->position() ) {
			return;
		}
		self::$rendered['mobile'] = true;
		$this->render( 'mobile' );
	}

	/**
	 * Render the toggle markup.
	 *
	 * @param string $context 'header' or 'mobile'.
	 */
	protected function render( string $context ): void {
		$mode    = $this->initial_mode();
		$wrapper = 'mobile' === $context ? 'bx-color-mode-toggle-mobile' : 'bx-color-mode-toggle-header';

		$labels = array(
			'light' => __( 'Light mode (click to switch to dark)', 'reign' ),
			'dark'  => __( 'Dark mode (click to switch to system)', 'reign' ),
			'auto'  => __( 'System mode (click to switch to light)', 'reign' ),
		);
		$label  = $labels[ $mode ];
		?>
		<div class="bx-color-mode-toggle <?php echo esc_attr( $wrapper ); ?>">
			<button type="button"
				class="bx-color-mode-toggle__btn"
				data-mode="<?php echo esc_attr( $mode ); ?>"
				aria-label="<?php echo esc_attr( $label ); ?>"
				aria-pressed="<?php echo 'dark' === $mode ? 'true' : 'false'; ?>">
				<svg class="bx-icon bx-icon-sun" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32l1.41-1.41"/></svg>
				<svg class="bx-icon bx-icon-moon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
				<svg class="bx-icon bx-icon-monitor" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
				<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
			</button>
		</div>
		<?php
	}

	/**
	 * Enqueue toggle JS + CSS only when toggle is enabled.
	 *
	 * In 8.0.0 we ship _color-mode-toggle.css as a standalone asset because
	 * Reign's Grunt build does not yet concat partials. Phase 4 (stylesheet
	 * cleanup) will fold it into a build artifact.
	 */
	public function enqueue_assets(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$theme     = wp_get_theme();
		$theme_uri = get_template_directory_uri();
		$theme_dir = get_template_directory();

		// JS
		$js_src  = $theme_uri . '/assets/js/color-mode-toggle.min.js';
		$js_path = $theme_dir . '/assets/js/color-mode-toggle.min.js';
		$js_ver  = file_exists( $js_path ) ? filemtime( $js_path ) : $theme->get( 'Version' );
		wp_enqueue_script( 'reign-color-mode-toggle', $js_src, array(), $js_ver, true );

		// Optional dark-mode logo. The modern toggle is client-side
		// (localStorage + [data-bx-mode]), so the logo swap also has to be
		// client-side - the legacy server cookie swap never fires here. Pass
		// the light + dark logo URLs to the toggle script; it swaps the
		// <img> src when the page enters / leaves dark mode.
		$dark_logo_id  = (int) get_theme_mod( 'reign_dark_mode_logo', 0 );
		$dark_logo_url = $dark_logo_id ? wp_get_attachment_image_url( $dark_logo_id, 'full' ) : (string) get_theme_mod( 'reign_dark_mode_logo', '' );
		if ( $dark_logo_url ) {
			$light_logo_id  = (int) get_theme_mod( 'custom_logo', 0 );
			$light_logo_url = $light_logo_id ? wp_get_attachment_image_url( $light_logo_id, 'full' ) : '';
			wp_add_inline_script(
				'reign-color-mode-toggle',
				'window.reignDarkLogo = ' . wp_json_encode(
					array(
						'light' => $light_logo_url,
						'dark'  => $dark_logo_url,
					)
				) . ';',
				'before'
			);
		}

		// CSS — the partial copied from BuddyX. Served raw until Phase 4
		// build-time integration.
		$css_src  = $theme_uri . '/assets/css/src/_color-mode-toggle.css';
		$css_path = $theme_dir . '/assets/css/src/_color-mode-toggle.css';
		if ( file_exists( $css_path ) ) {
			$css_ver = filemtime( $css_path );
			wp_enqueue_style( 'reign-color-mode-toggle', $css_src, array(), $css_ver );
		}
	}
}

// Boot is called explicitly from inc/customizer-framework-bootstrap.php
// :: reign_boot_tokens_modules() on after_setup_theme priority 7.

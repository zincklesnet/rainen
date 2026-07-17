<?php
/**
 * Reign\Fonts\Component
 *
 * Value-driven Google Fonts loader. Scans Reign's typography theme_mods for
 * `font-family` values and enqueues a single Google Fonts URL with only the
 * families actually selected — no full-catalog fetch, no payload from
 * customers who haven't picked a custom font.
 *
 * Adapted from BuddyX 5.1.0's Fonts\Component.php. The local-font-download
 * infrastructure (download_all_google_fonts / download_google_fonts_to_local)
 * is omitted; can be added in a follow-up if customers want a "host Google
 * Fonts locally" toggle. The default behaviour fetches from fonts.googleapis.com
 * with a preconnect resource hint.
 *
 * @package reign
 * @since 8.0.0
 */

namespace Reign\Fonts;

defined( 'ABSPATH' ) || exit;

class Component {

	/**
	 * Idempotency guard.
	 *
	 * @var bool
	 */
	protected static bool $booted = false;

	/**
	 * Lazily-computed cache of $font_name => $variants[] pairs.
	 *
	 * @var array|null
	 */
	protected ?array $google_fonts = null;

	/**
	 * Reign typography theme_mod IDs that may carry a font-family. Hardcoded
	 * because Customizer_Framework field registrations are only available in
	 * the customizer admin context, not on front-end requests where we need
	 * to scan saved values.
	 *
	 * Keep in sync with inc/Customizer_Settings/Fields/Typography_Fields.php.
	 *
	 * @var array<int, string>
	 */
	protected static array $typography_settings = array(
		'reign_body_typography',
		'reign_title_tagline_typography',
		'site_tagline_typography_option',
		'reign_header_main_menu_font',
		'reign_header_sub_menu_font',
		'reign_h1_typography',
		'reign_h2_typography',
		'reign_h3_typography',
		'reign_h4_typography',
		'reign_h5_typography',
		'reign_h6_typography',
		'reign_quote_typography',
		'reign_left_panel_menu_typography',
		'reign_footer_widget_title',
		'reign_footer_content_typo',
	);

	/**
	 * Font family names that are self-hosted or CSS generics — never request
	 * from Google. Family-name compare is case-insensitive (we lowercase
	 * both sides at compare time), so list lowercase here.
	 *
	 * Keep `inherit` + CSS generics + theme.json fontFamilies (system, gt-
	 * walsheim-pro) AND any face the theme bundles in assets/fonts/.
	 *
	 * @var array<int, string>
	 */
	protected static array $skip = array(
		'',
		'inherit',
		'system',
		'system ui',
		'system-ui',
		'sans-serif',
		'serif',
		'monospace',
		'cursive',
		'fantasy',
		// Self-hosted theme face (assets/fonts/inter/Inter-*.woff2). The
		// customizer Typography picker exposes this via theme.json
		// fontFamilies as the "Theme Default" option. Inter does live on
		// Google Fonts, but loading the self-hosted woff2 we ship beats
		// a CDN round-trip and keeps us GDPR-clean.
		'inter',
	);

	/**
	 * Boot — hook front-end + editor enqueues + preconnect filter.
	 */
	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		$instance = new self();
		add_action( 'wp_enqueue_scripts', array( $instance, 'enqueue_fonts' ), 5 );
		add_action( 'after_setup_theme', array( $instance, 'add_editor_fonts' ), 20 );
		add_filter( 'wp_resource_hints', array( $instance, 'filter_resource_hints' ), 10, 2 );
	}

	/**
	 * Enqueue the Google Fonts stylesheet for the front-end.
	 */
	public function enqueue_fonts(): void {
		$url = $this->get_google_fonts_url();
		if ( '' === $url ) {
			return;
		}
		wp_enqueue_style( 'reign-google-fonts', $url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	/**
	 * Inject Google Fonts into the block editor so block content previews
	 * using the customer-selected typography.
	 */
	public function add_editor_fonts(): void {
		$url = $this->get_google_fonts_url();
		if ( '' !== $url ) {
			add_editor_style( $url );
		}
	}

	/**
	 * Add preconnect resource hint when Google Fonts is being loaded.
	 *
	 * @param array  $urls          URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed.
	 * @return array
	 */
	public function filter_resource_hints( array $urls, string $relation_type ): array {
		if ( 'preconnect' === $relation_type && wp_style_is( 'reign-google-fonts', 'queue' ) ) {
			$urls[] = array(
				'href' => 'https://fonts.gstatic.com',
				'crossorigin',
			);
		}
		return $urls;
	}

	/**
	 * Map of theme.json fontFamilies SLUGS (lowercased) → true. Lets
	 * get_google_fonts() skip any saved font-family value that's just
	 * a slug pointer — those resolve via theme.json fontFace blocks
	 * (self-hosted) or to CSS generic stacks, never to a Google Fonts
	 * family on the CDN.
	 *
	 * Cached statically for the request.
	 *
	 * @return array<string, true>
	 */
	protected static function theme_json_font_slugs(): array {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}
		$cache = array();
		if ( ! function_exists( 'wp_get_global_settings' ) ) {
			return $cache;
		}
		$settings = wp_get_global_settings();
		$families = $settings['typography']['fontFamilies']['theme'] ?? array();
		foreach ( $families as $f ) {
			if ( isset( $f['slug'] ) ) {
				$cache[ strtolower( (string) $f['slug'] ) ] = true;
			}
		}
		return $cache;
	}

	/**
	 * Scan typography theme_mods for font families to fetch.
	 *
	 * Returns associative array $font_name => $variants[]. Self-hosted fonts
	 * (Inter, Newsreader if present in theme.json) and CSS generics are
	 * skipped. Whatever the customer has saved (whether Kirki-era pick or
	 * post-8.0.0 framework pick) gets loaded so visual parity is preserved.
	 *
	 * Filter: `reign_google_fonts` — array of $font_name => $variants[].
	 *
	 * @return array<string, array<int, string>>
	 */
	protected function get_google_fonts(): array {
		if ( null !== $this->google_fonts ) {
			return $this->google_fonts;
		}

		$theme_json_slugs = self::theme_json_font_slugs();

		$collected = array();
		foreach ( self::$typography_settings as $setting_id ) {
			$value = get_theme_mod( $setting_id );
			if ( ! is_array( $value ) || empty( $value['font-family'] ) ) {
				continue;
			}
			$family = trim( (string) $value['font-family'] );
			if ( in_array( strtolower( $family ), self::$skip, true ) ) {
				continue;
			}
			// Skip theme.json fontFamilies slugs (e.g. `inter`, `system`,
			// `source-serif-4`). Those resolve to self-hosted faces
			// declared inline via theme.json's `fontFace` block, or to
			// CSS generic stacks — neither of which Google hosts.
			if ( isset( $theme_json_slugs[ strtolower( $family ) ] ) ) {
				continue;
			}
			// Skip slug-shaped values — kebab-case lowercase strings with
			// no spaces look like theme.json fontFamilies slugs that no
			// longer exist (e.g. a customer-saved `gt-walsheim-pro` from
			// before the family was removed from theme.json). Google
			// Fonts has no `gt-walsheim-pro` family — the request returns
			// 404 every page load. Real Google Fonts family names always
			// contain a capital letter or a space (e.g. `Inter`, `Open
			// Sans`, `Source Serif 4`).
			if ( '' !== $family
				&& strtolower( $family ) === $family
				&& false === strpos( $family, ' ' )
				&& false !== strpos( $family, '-' )
			) {
				continue;
			}

			// Pull a variant. Kirki used 'variant' (e.g. '400italic', '700');
			// the new framework uses 'font-weight' (e.g. '400', '700') and
			// 'font-style' separately. Accept either shape.
			$variant = '';
			if ( ! empty( $value['variant'] ) ) {
				$variant = (string) $value['variant'];
			} elseif ( ! empty( $value['font-weight'] ) ) {
				$variant = (string) $value['font-weight'];
			}

			if ( ! isset( $collected[ $family ] ) ) {
				$collected[ $family ] = array();
			}
			if ( '' !== $variant && ! in_array( $variant, $collected[ $family ], true ) ) {
				$collected[ $family ][] = $variant;
			}
		}

		/**
		 * Filter the list of Google Fonts that Reign loads.
		 *
		 * @param array $collected Associative array $font_name => $variants[].
		 */
		$this->google_fonts = (array) apply_filters( 'reign_google_fonts', $collected );

		return $this->google_fonts;
	}

	/**
	 * Build the single Google Fonts stylesheet URL covering every selected
	 * family. Returns '' when no custom families are selected — that path
	 * sends ZERO network request and zero bytes to visitors who use the
	 * theme's self-hosted defaults.
	 *
	 * @return string
	 */
	protected function get_google_fonts_url(): string {
		$fonts = $this->get_google_fonts();
		if ( array() === $fonts ) {
			return '';
		}

		$families = array();
		foreach ( $fonts as $name => $variants ) {
			if ( ! empty( $variants ) ) {
				if ( ! is_array( $variants ) ) {
					$variants = explode( ',', str_replace( ' ', '', (string) $variants ) );
				}
				$families[] = $name . ':' . implode( ',', $variants );
			} else {
				$families[] = $name;
			}
		}

		return add_query_arg(
			array(
				'family'  => implode( '|', $families ),
				'display' => 'swap',
			),
			'https://fonts.googleapis.com/css'
		);
	}
}

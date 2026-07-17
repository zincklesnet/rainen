<?php
/**
 * Reign\Customizer_Framework\Field — type→control dispatcher.
 *
 * @package Reign */

namespace Reign\Customizer_Framework;

defined( 'ABSPATH' ) || exit;

/**
 * Field
 *
 * Maps each of 20 field type strings to a (setting class, control class,
 * is_custom_control) triple. On register, instantiates the appropriate
 * setting and control with normalized args.
 *
 * Public API:
 *   Field::add( $type, $args ) — register a field for later instantiation
 *
 * Extensibility:
 *   apply_filters( 'reign_customizer_field_type_map', $type_map )
 *     lets BuddyX Pro / extensions register additional control types
 *     or override existing ones via a single add_filter() call.
 */
class Field {

	/**
	 * type => [ setting class, control class, is_custom_control ]
	 *
	 * - is_custom_control true means we instantiate the class directly;
	 *   false means we pass args to add_control() and let core build it.
	 *
	 * @var array<string, array{0:string,1:string,2:bool}>
	 */
	protected static $type_map = array(
		// 12 custom controls
		'color'           => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Color', true ),
		'typography'      => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Typography', true ),
		'radio_image'     => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Radio_Image', true ),
		'switch'          => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Toggle', true ),
		'dimension'       => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Dimension', true ),
		'custom'          => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Custom_HTML', true ),
		'checkbox'        => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Checkbox', true ),
		'slider'          => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Slider', true ),
		// Plain numeric input — bare integer/float value (e.g. 286, 3, 20).
		// Distinct from Slider/Dimension which store unit-suffixed strings.
		'number'          => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Number', true ),
		'radio_buttonset' => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Radio_Buttonset', true ),
		'repeater'        => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Repeater', true ),
		'upload'          => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Upload', true ),
		'sortable'        => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Sortable', true ),
		// 'code' — syntax-highlighted textarea via wp.codeEditor. Reign-specific
		// addition (BuddyX 5.1.0 has no code fields; Reign has 3).
		'code'            => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Code', true ),
		// 6 core types dispatched via add_control( id, args ) shortcut form.
		'text'            => array( '\\WP_Customize_Setting', '\\WP_Customize_Control', false ),
		'textarea'        => array( '\\WP_Customize_Setting', '\\WP_Customize_Control', false ),
		'url'             => array( '\\WP_Customize_Setting', '\\WP_Customize_Control', false ),
		'select'          => array( '\\WP_Customize_Setting', '\\WP_Customize_Control', false ),
		'radio'           => array( '\\WP_Customize_Setting', '\\WP_Customize_Control', false ),
		'dropdown-pages'  => array( '\\WP_Customize_Setting', '\\WP_Customize_Control', false ),
		// Image: instantiate WP core's image control directly (the add_control
		// shortcut form does not support 'image' as a type string — it would
		// render as a plain text input).
		'image'           => array( '\\WP_Customize_Setting', '\\WP_Customize_Image_Control', true ),
		// Background is a Kirki-shape composite (color/image/repeat/position/
		// size/attachment) — six sub-inputs, structured-array value.
		'background'      => array( '\\WP_Customize_Setting', '\\Reign\\Customizer_Framework\\Controls\\Background', true ),
	);

	/**
	 * Register a field for later instantiation on customize_register.
	 *
	 * @param string $type Field type string (one of the 20 supported keys).
	 * @param array  $args Field args; must include 'settings' and 'section'.
	 */
	public static function add( string $type, array $args ): void {
		Component::register_field( $type, $args );
	}

	/**
	 * Add the underlying setting + control to the customizer manager.
	 * Called from Component::register() during customize_register.
	 *
	 * @param \WP_Customize_Manager $wp_customize
	 * @param array                 $args Field args (must include '_type').
	 */
	public static function register_with_manager( \WP_Customize_Manager $wp_customize, array $args ): void {
		// Allow Pro / extensions to add or override control types.
		$type_map = apply_filters( 'reign_customizer_field_type_map', self::$type_map );

		$type = $args['_type'] ?? '';
		if ( ! isset( $type_map[ $type ] ) ) {
			return;
		}
		list( $setting_class, $control_class, $is_custom ) = $type_map[ $type ];

		if ( empty( $args['settings'] ) ) {
			return;
		}

		// Allow callers to mutate args (e.g. force postMessage on settings rendered via dynamic CSS).
		$args = apply_filters( 'reign_customizer_field_args', $args, $wp_customize );

		$setting_id  = $args['settings'];
		$transport   = self::resolve_transport( $args );
		$default     = $args['default'] ?? '';
		$sanitize_cb = self::resolve_sanitize_callback( $type, $args );

		// Normalize switch/checkbox defaults to 0/1 so the JS setting value
		// is a falsy/truthy integer. The WP customizer's checkbox synchronizer
		// passes the raw value to jQuery's .prop('checked', value) — any
		// non-empty string (including 'off') is truthy in JS and would render
		// the toggle as ON even when the PHP default is 'off'.
		if ( in_array( $type, array( 'switch', 'checkbox' ), true ) ) {
			$default = self::sanitize_bool_int( $default );
		}

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $default,
				'transport'         => $transport,
				'sanitize_callback' => $sanitize_cb,
				'capability'        => $args['capability'] ?? 'edit_theme_options',
			)
		);

		$control_args = self::build_control_args( $type, $args );

		if ( $is_custom ) {
			self::require_control( $control_class );
			$wp_customize->add_control( new $control_class( $wp_customize, $setting_id, $control_args ) );
		} else {
			$control_args['type'] = self::map_core_type( $type );
			$wp_customize->add_control( $setting_id, $control_args );
		}
	}

	/**
	 * Resolve Kirki-style 'auto' transport to postMessage if output is provided,
	 * else refresh. Pass-through for refresh/postMessage.
	 *
	 * Default is 'auto' (was 'refresh' pre-5.1.0): when a field declares
	 * `'output' => array(...)`, the customizer-preview.js handler can update
	 * inline CSS without a full preview reload. Defaulting to 'auto' makes
	 * every output-producing field live-updatable by default — only fields
	 * that explicitly set `'transport' => 'refresh'` (e.g. settings that
	 * change rendered MARKUP, not just CSS) trigger a full reload.
	 *
	 * Closes the live-preview UX bug found in 100% verification: 25 of 38
	 * fields with `output` were defaulting to refresh, causing a full
	 * preview reload on every keystroke during typography / color edits.
	 */
	protected static function resolve_transport( array $args ): string {
		$t = $args['transport'] ?? 'auto';
		if ( 'auto' === $t ) {
			return ! empty( $args['output'] ) ? 'postMessage' : 'refresh';
		}
		return in_array( $t, array( 'refresh', 'postMessage' ), true ) ? $t : 'refresh';
	}

	/**
	 * Pick a sane sanitize callback by type, unless the consumer overrode it.
	 */
	protected static function resolve_sanitize_callback( string $type, array $args ): callable {
		if ( isset( $args['sanitize_callback'] ) && is_callable( $args['sanitize_callback'] ) ) {
			return $args['sanitize_callback'];
		}
		switch ( $type ) {
			case 'color':
				// Alpha-aware fields ('choices' => array('alpha' => true)) accept
				// rgba / rgb / hex / hsla — matching master Kirki's color-alpha
				// sanitizer. WordPress core's sanitize_hex_color rejects rgba and
				// returns NULL, which would silently null out customer-saved
				// translucent colors on every customizer save (data loss).
				if ( ! empty( $args['choices']['alpha'] ) ) {
					return array( __CLASS__, 'sanitize_color_alpha' );
				}
				return 'sanitize_hex_color';
			case 'url':
				return 'esc_url_raw';
			case 'textarea':
				return 'sanitize_textarea_field';
			case 'switch':
			case 'checkbox':
				return array( __CLASS__, 'sanitize_bool_int' );
			case 'select':
			case 'radio':
			case 'radio_buttonset':
				return 'sanitize_key';
			case 'radio_image':
				// radio_image choices can be either slug strings ('simple') or full URLs
				// (gallery image paths). sanitize_key strips slashes and dots, corrupting
				// any URL value. esc_url_raw prepends http:// to bare words (no colon),
				// corrupting slug values like 'modern' into 'http://modern'. Detect which
				// case we have by checking for a scheme delimiter.
				return array( __CLASS__, 'sanitize_radio_image' );
			case 'image':
			case 'upload':
				return 'esc_url_raw';
			case 'background':
				return array( __CLASS__, 'sanitize_background' );
			case 'repeater':
				return array( __CLASS__, 'sanitize_json_array' );
			case 'sortable':
				// Sortable stores a flat array of slug strings in display
				// order. Decode incoming JSON (or accept already-decoded
				// arrays) so templates that do `foreach (theme_mod, ...)`
				// receive an actual array, not a JSON string. The control
				// only ever emits slugs from the manifest's `choices` map,
				// so the storage contract is intentionally narrower than
				// the repeater's multi-field row payload.
				return array( __CLASS__, 'sanitize_sortable_slugs' );
			case 'typography':
				return array( __CLASS__, 'sanitize_typography' );
			case 'dimension':
				return array( __CLASS__, 'sanitize_dimension' );
			case 'number':
				return array( __CLASS__, 'sanitize_number' );
			case 'code':
				// Raw text preservation — code fields hold CSS/HTML/JS that must
				// not be stripped of tags or line breaks. Sanitize against null
				// bytes and ensure UTF-8 only; the actual escaping happens at
				// emit time (wp_kses_post for HTML, wp_strip_all_tags for JS, etc.).
				return array( __CLASS__, 'sanitize_code' );
			default:
				return 'sanitize_text_field';
		}
	}

	/**
	 * Sanitize a color value that may include alpha. Used by all 'color' fields
	 * registered with 'choices' => array('alpha' => true).
	 *
	 * Accepts:
	 *   - Hex: #RGB / #RGBA / #RRGGBB / #RRGGBBAA (3, 4, 6, or 8 chars after #)
	 *   - Functional: rgb(R,G,B) / rgba(R,G,B,A) / hsl(H,S%,L%) / hsla(H,S%,L%,A)
	 *
	 * Returns the trimmed input on match, or '' on invalid/empty input. Matches
	 * the master Kirki ColorAlpha sanitizer's tolerance so customer-saved rgba
	 * values from 5.0.x survive 5.1.0 customizer saves intact (no data loss).
	 *
	 * @param mixed $value Customer-submitted color value.
	 * @return string Validated color string, or '' if input doesn't match a
	 *                supported color format.
	 */
	public static function sanitize_color_alpha( $value ): string {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		// Hex with optional alpha (3, 4, 6, or 8 chars after #).
		if ( preg_match( '/^#(?:[A-Fa-f0-9]{3}|[A-Fa-f0-9]{4}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $value ) ) {
			return $value;
		}
		// rgb(...) / rgba(...) — tolerate spaces, decimal alpha (0, 1, 0.x).
		if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(?:,\s*(?:0|1|0?\.\d+)\s*)?\)$/', $value ) ) {
			return $value;
		}
		// hsl(...) / hsla(...) with percentage S and L.
		if ( preg_match( '/^hsla?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*%\s*,\s*\d{1,3}\s*%\s*(?:,\s*(?:0|1|0?\.\d+)\s*)?\)$/', $value ) ) {
			return $value;
		}
		return '';
	}

	/**
	 * Coerce truthy values to 1, falsy to 0. Used for switch + checkbox.
	 *
	 * Accepts every shape Kirki produced + theme defaults:
	 *   - '1', 1, true  → 1
	 *   - 'on', 'yes', 'true', 'enable' → 1 (Kirki choices key, default values)
	 *   - '0', 0, false, '', null, 'off', 'no', 'disable' → 0
	 *
	 * This catches a customer-data-loss bug where switches with default 'on'
	 * (e.g. site_custom_colors, site_breadcrumbs, buddypress_avatar_style)
	 * would silently flip OFF on save because the strict comparison missed
	 * the 'on' string.
	 *
	 * @param mixed $value Customer-submitted value.
	 * @return int 1 for truthy, 0 for falsy.
	 */
	public static function sanitize_bool_int( $value ): int {
		if ( is_bool( $value ) ) {
			return $value ? 1 : 0;
		}
		if ( is_int( $value ) ) {
			return $value ? 1 : 0;
		}
		$truthy = array( '1', 'on', 'yes', 'true', 'enable' );
		return in_array( strtolower( (string) $value ), $truthy, true ) ? 1 : 0;
	}

	/**
	 * Sanitize a code-editor value (CSS / HTML / JS).
	 *
	 * Preserves line breaks, indentation and tags. Strips null bytes and
	 * coerces to UTF-8. The output-time emitter (custom-styles inline,
	 * wp_head priority, footer print, etc.) is responsible for the
	 * appropriate context-specific escape — this function only guards
	 * against invalid byte sequences at the save boundary.
	 *
	 * @param mixed $value Customer-submitted code string.
	 * @return string
	 */
	public static function sanitize_code( $value ): string {
		if ( ! is_string( $value ) ) {
			return '';
		}
		// Strip null bytes (defense-in-depth — they can confuse some emitters).
		$value = str_replace( "\0", '', $value );
		// Coerce to UTF-8; invalid sequences become '?' rather than fataling later.
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$value = mb_convert_encoding( $value, 'UTF-8', 'UTF-8' );
		}
		return $value;
	}

	/**
	 * Sanitize repeater/sortable JSON-or-array values to a JSON string.
	 * Always emits a valid JSON array string.
	 */
	public static function sanitize_json_array( $value ): string {
		if ( is_array( $value ) ) {
			return wp_json_encode( $value );
		}
		$decoded = json_decode( (string) $value, true );
		return is_array( $decoded ) ? wp_json_encode( $decoded ) : '[]';
	}

	/**
	 * Sanitize a sortable control value to a flat array of slug strings.
	 *
	 * Accepts both legacy shapes the JS may emit during the upgrade window:
	 *   - flat array of slug strings:  ['search', 'cart', 'login']
	 *   - array of {slug, enabled}:    [{slug:'search', enabled:true}, ...]
	 *   - JSON-encoded form of either (sortable's hidden input still
	 *     synchronises via JSON.stringify on the JS side).
	 *
	 * Output is always an array of slug strings in the order they should
	 * render in the header. Disabled items are dropped — templates iterate
	 * the array directly with `get_template_part('template-parts/header-
	 * icons/' . $slug)` and have no notion of an `enabled` flag.
	 *
	 * Empty / unparseable input collapses to an empty array, not `'[]'`.
	 * That lets template fallbacks (`get_theme_mod(setting, $default)`)
	 * fire normally instead of yielding a literal `'[]'` string.
	 *
	 * @param mixed $value Customer-submitted value, JSON string, or array.
	 * @return array<int, string> Ordered slug list.
	 */
	public static function sanitize_sortable_slugs( $value ): array {
		if ( ! is_array( $value ) ) {
			$decoded = json_decode( (string) $value, true );
			$value   = is_array( $decoded ) ? $decoded : array();
		}

		$out = array();
		foreach ( $value as $item ) {
			if ( is_string( $item ) && '' !== $item ) {
				$out[] = sanitize_key( $item );
				continue;
			}
			if ( is_array( $item ) && ! empty( $item['slug'] ) ) {
				// Only include if explicitly enabled, OR if no `enabled`
				// key is present (legacy / first-save records).
				$enabled = ! array_key_exists( 'enabled', $item ) || ! empty( $item['enabled'] );
				if ( $enabled ) {
					$out[] = sanitize_key( (string) $item['slug'] );
				}
			}
		}
		return array_values( array_unique( $out ) );
	}

	/**
	 * Sanitize a typography array. Whitelisted keys, plain text values.
	 * Tolerant of Kirki legacy 'variant' key which is normalized at read time
	 * by Output_Builder, not here.
	 */
	public static function sanitize_typography( $value ): array {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$out  = array();
		$keys = array(
			'font-family',
			'font-weight',
			'variant',
			'font-size',
			'line-height',
			'letter-spacing',
			'text-transform',
			'font-style',
			'text-align',
			'text-decoration',
			'color',
		);
		foreach ( $keys as $k ) {
			if ( isset( $value[ $k ] ) ) {
				$out[ $k ] = sanitize_text_field( (string) $value[ $k ] );
			}
		}
		return $out;
	}

	/**
	 * Sanitize a Background composite value — array with the 6 background-* keys.
	 * Whitelists keys; passes string values through sanitize_text_field; URL key
	 * goes through esc_url_raw.
	 */
	public static function sanitize_background( $value ): array {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$out  = array();
		$keys = array(
			'background-color',
			'background-image',
			'background-repeat',
			'background-position',
			'background-size',
			'background-attachment',
		);
		foreach ( $keys as $k ) {
			if ( ! isset( $value[ $k ] ) ) {
				continue;
			}
			$v         = (string) $value[ $k ];
			$out[ $k ] = ( 'background-image' === $k ) ? esc_url_raw( $v ) : sanitize_text_field( $v );
		}
		return $out;
	}

	/**
	 * Sanitize a dimension string like '120px' / '1.5rem'.
	 * Allows numeric + (px|em|rem|%|vh|vw); falls back to '' on invalid.
	 */
	public static function sanitize_dimension( $value ): string {
		$v = trim( (string) $value );
		if ( '' === $v ) {
			return '';
		}
		if ( preg_match( '/^-?\d+(\.\d+)?(px|em|rem|%|vh|vw)?$/i', $v ) ) {
			return $v;
		}
		return '';
	}

	/**
	 * Sanitize a bare numeric value for 'number' fields.
	 *
	 * Returns an integer when the value is a whole number (the common case:
	 * sub-header height, blogs-per-row, excerpt length), and a float when a
	 * fractional value is supplied. Non-numeric input collapses to 0 so a
	 * stray empty/garbage value never poisons a `* (int)` template calc.
	 *
	 * @param mixed $value Customer-submitted value.
	 * @return int|float
	 */
	public static function sanitize_number( $value ) {
		if ( ! is_numeric( $value ) ) {
			return 0;
		}
		$float = (float) $value;
		// Preserve integers as int; only return float when genuinely fractional.
		return ( floor( $float ) === $float ) ? (int) $float : $float;
	}

	/**
	 * Sanitize a radio_image value, handling both slug and URL choice keys.
	 *
	 * Slug-style choices (most layout/theme selectors) use bare keys like
	 * 'modern' or 'left_sidebar' — pass through sanitize_key.
	 * URL-style choices (gallery/loader pickers) use full image paths like
	 * 'http://example.com/img.svg' — pass through esc_url_raw.
	 *
	 * Detected by the presence of a scheme delimiter (://).
	 */
	public static function sanitize_radio_image( $value ): string {
		if ( strpos( $value, '://' ) !== false ) {
			return esc_url_raw( $value );
		}
		return sanitize_key( $value );
	}

	/**
	 * Build the args array passed to add_control() / control constructor.
	 *
	 * Strips framework-internal/setting keys; everything else passes through so
	 * Kirki-shape args (fields, row_label, multiple, mode, etc.) reach control
	 * classes whose public properties match. Compiles array-form active_callback
	 * to a closure.
	 */
	protected static function build_control_args( string $type, array $args ): array {
		// Strip keys consumed by Field::register_with_manager / add_setting.
		$internal = array( '_type', 'settings', 'default', 'transport', 'sanitize_callback', 'capability' );
		$out      = array_diff_key( $args, array_flip( $internal ) );

		$out['label'] = $args['label'] ?? '';
		// Render `tooltip` as inline description when no description is
		// set. The standalone popover variant conflicted with adjacent
		// toggle/switch controls (overlay blocked clicks and obscured
		// the next setting), so we collapse it to the customizer's
		// native description slot which doesn't take overlay space.
		$out['description'] = $args['description'] ?? ( $args['tooltip'] ?? '' );
		$out['section']     = $args['section'];
		$out['priority']    = $args['priority'] ?? 10;

		// Compile array-form active_callback (Kirki shape) to a closure.
		if ( isset( $out['active_callback'] ) && is_array( $out['active_callback'] ) ) {
			require_once __DIR__ . '/Active_Callback.php';
			$out['active_callback'] = Active_Callback::compile( $out['active_callback'] );
		}
		return $out;
	}

	/**
	 * Map our string field type to a core WP_Customize_Control 'type' attr
	 * for the core-dispatched types.
	 */
	protected static function map_core_type( string $type ): string {
		$map = array(
			'text'           => 'text',
			'textarea'       => 'textarea',
			'url'            => 'url',
			'select'         => 'select',
			'radio'          => 'radio',
			'dropdown-pages' => 'dropdown-pages',
		);
		return $map[ $type ] ?? 'text';
	}

	/**
	 * require_once the file housing a custom control class. Core WP control
	 * classes are already autoloaded; this only loads framework controls in
	 * Controls/ that follow PSR-4 (short class name + .php).
	 */
	protected static function require_control( string $class ): void {
		if ( class_exists( $class, false ) ) {
			return;
		}
		$short = substr( $class, strrpos( $class, '\\' ) + 1 );
		$file  = __DIR__ . '/Controls/' . $short . '.php';
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}

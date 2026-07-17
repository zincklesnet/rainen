<?php
/**
 * Reign\Customizer_Framework\Output_Builder — auto-CSS generator.
 *
 * @package Reign */

namespace Reign\Customizer_Framework;

defined( 'ABSPATH' ) || exit;

/**
 * Output_Builder
 *
 * Iterates every registered field with a non-empty 'output' arg, reads its
 * theme_mod value, and emits inline CSS. Replaces Kirki's auto-CSS feature.
 *
 * Supports:
 *   - element / property / prefix / suffix / units (scalar values)
 *   - typography multi-property declaration block (object values)
 *   - Kirki legacy 'variant' key normalization to 'font-weight'
 */
class Output_Builder {

	/**
	 * Strip CSS structural characters from a value about to be concatenated
	 * into an inline rule. Defense in depth for the auto-emit pipeline: even
	 * if a field's sanitize_callback fails to reject `}` / `;` / `<` etc.
	 * (or a developer adds a new field type whose callback is too permissive),
	 * the value can never break out of its declaration here.
	 *
	 * Allowed: hex, named colours, rgb()/rgba()/hsl(), numbers + units, font
	 * stacks with commas, the standard CSS keyword vocabulary. Blocked: the
	 * exact characters that would close a property (`;`) or a rule block
	 * (`{` / `}`), plus the characters that would let a value break out of
	 * the surrounding `<style>` element (`<` / `>`) or smuggle a newline
	 * into a single-line rule.
	 *
	 * Intentionally NOT a strict whitelist — that would reject valid future
	 * CSS like calc(), clamp(), env(), custom-property var(--x) references,
	 * gradients, etc. The blacklist is narrow + targeted.
	 *
	 * @param mixed $value Raw value (coerced to string).
	 * @return string Sanitised value safe to concatenate into an inline rule.
	 */
	public static function sanitize_css_value( $value ): string {
		return (string) preg_replace( '/[<>{};\r\n]/', '', (string) $value );
	}

	/**
	 * Build a single inline CSS string from accumulated fields.
	 *
	 * @param array $fields Field args list from Component::get_fields().
	 * @return string Concatenated CSS (no <style> wrapper).
	 */
	public static function collect( array $fields ): string {
		$css = '';
		foreach ( $fields as $f ) {
			if ( empty( $f['output'] ) || empty( $f['settings'] ) ) {
				continue;
			}
			$value = get_theme_mod( $f['settings'], $f['default'] ?? '' );
			if ( '' === $value || is_null( $value ) ) {
				continue;
			}
			foreach ( (array) $f['output'] as $rule ) {
				$css .= self::rule_to_css( $rule, $value, $f['_type'] ?? '' );
			}
		}
		return $css;
	}

	/**
	 * Render one output rule for one value into a CSS string.
	 *
	 * @param array  $rule  Output rule { element, property, prefix, suffix, units }.
	 * @param mixed  $value Setting value (string, number, or typography array).
	 * @param string $type  Field type (drives default property + special cases).
	 *
	 * RULE AUTHORING CONTRACT (read before adding output rules in Fields/*.php):
	 *
	 * 1. Use 'units' (NOT 'suffix') to append a CSS unit to slider/dimension
	 *    values. Sliders store their value WITH the unit ('230px'); 'units'
	 *    strips to the bare number before appending, 'suffix' concatenates raw
	 *    and double-appends into invalid CSS ('230pxpx') that browsers drop.
	 *    'suffix' is only for non-unit text (e.g. '!important').
	 *
	 * 2. This builder does NOT evaluate 'active_callback' — that gates the
	 *    Customizer UI control only. Output CSS for a gated/toggleable feature
	 *    must read the controlling theme_mod itself (skip the rule at
	 *    registration time, or scope the selector to a body class the toggle
	 *    controls), otherwise the CSS emits even when the feature is off.
	 */
	protected static function rule_to_css( array $rule, $value, string $type ): string {
		$element = $rule['element'] ?? '';
		if ( '' === $element ) {
			return '';
		}

		$css = '';

		// Typography: multi-property declaration block from a structured array.
		if ( 'typography' === $type && is_array( $value ) ) {
			$decls = self::typography_declarations( $value );
			$css   = $decls ? sprintf( '%s{%s}', $element, $decls ) : '';
		} elseif ( 'background' === $type && is_array( $value ) ) {
			// Background: expand the 6-key array into a multi-declaration block.
			$decls = self::background_declarations( $value );
			$css   = $decls ? sprintf( '%s{%s}', $element, $decls ) : '';
		} else {
			$property = $rule['property'] ?? self::default_property( $type );
			if ( '' === $property ) {
				return '';
			}

			// Defense in depth: simple-property path expects a scalar. If we got
			// here with an array (e.g. legacy Padding composite or a misrouted
			// typography/background), short-circuit instead of warning at the
			// concat below. The typography/background branches above are the
			// supported array paths.
			if ( ! is_scalar( $value ) ) {
				return '';
			}

			$prefix = $rule['prefix'] ?? '';
			$suffix = $rule['suffix'] ?? '';
			$units  = $rule['units'] ?? '';

			// Slider/dimension controls store their value WITH a unit suffix
			// (e.g. '200px'). When the output rule appends its OWN unit, the two
			// collide into invalid CSS - 'opacity: 81px%' or 'max-width: 200pxpx'
			// - which browsers silently drop, so the control appears to do
			// nothing. When the rule declares units, reduce the value to its bare
			// numeric portion first: '81px' + '%' => '81%', '200px' + 'px' =>
			// '200px'. Values that legitimately carry their own unit and omit the
			// 'units' key are left untouched.
			if ( '' !== $units && is_scalar( $value ) ) {
				$numeric = preg_replace( '/[^0-9.\-]/', '', (string) $value );
				if ( '' !== $numeric ) {
					$value = $numeric;
				}
			}

			// Defense in depth: strip CSS structural chars before concat so a
			// saved value can never break out of its declaration block.
			$rendered = self::sanitize_css_value( $prefix . $value . $suffix . $units );
			if ( '' === $rendered ) {
				return '';
			}
			$css = sprintf( '%s{%s:%s;}', $element, $property, $rendered );
		}

		if ( '' === $css ) {
			return '';
		}

		// Wrap the rule in its media query when one is declared. Without this,
		// responsive output rules (e.g. mobile/desktop logo sizing, left-panel
		// breakpoints) emitted their declaration unscoped and applied at every
		// viewport, so the breakpoint-specific value never took effect.
		if ( ! empty( $rule['media_query'] ) ) {
			$media_query = self::sanitize_css_value( (string) $rule['media_query'] );
			if ( '' !== $media_query ) {
				$css = sprintf( '%s{%s}', $media_query, $css );
			}
		}

		return $css;
	}

	/**
	 * Build typography CSS declarations from a structured value array.
	 * Accepts both modern keys ('font-weight', 'font-size', etc.) and Kirki
	 * legacy 'variant' (mapped to font-weight, with 'regular' → 400, 'bold' → 700).
	 */
	protected static function typography_declarations( array $value ): string {
		$decls = '';

		// Kirki legacy: 'variant' → font-weight (and possibly font-style for *italic combos).
		if ( ! empty( $value['variant'] ) && empty( $value['font-weight'] ) ) {
			$variant = strtolower( (string) $value['variant'] );
			$weight  = $variant;
			$style   = '';
			if ( false !== strpos( $variant, 'italic' ) ) {
				$style  = 'italic';
				$weight = trim( str_replace( 'italic', '', $variant ) );
			}
			if ( 'regular' === $weight || '' === $weight ) {
				$weight = '400';
			} elseif ( 'bold' === $weight ) {
				$weight = '700';
			}
			$value['font-weight'] = $weight;
			if ( '' !== $style && empty( $value['font-style'] ) ) {
				$value['font-style'] = $style;
			}
		}

		$key_map = array(
			'font-family'     => 'font-family',
			'font-size'       => 'font-size',
			'line-height'     => 'line-height',
			'letter-spacing'  => 'letter-spacing',
			'font-weight'     => 'font-weight',
			'text-transform'  => 'text-transform',
			'font-style'      => 'font-style',
			'text-align'      => 'text-align',
			'text-decoration' => 'text-decoration',
			'color'           => 'color',
		);
		foreach ( $key_map as $k => $css_prop ) {
			if ( ! empty( $value[ $k ] ) ) {
				$emit_value = $value[ $k ];
				// font-family stored by the Typography control is a
				// theme.json fontFamilies SLUG (e.g. `inter`,
				// `gt-walsheim-pro`) — emitting it raw produces
				// `font-family: inter;` which no browser can resolve.
				// Resolve the slug through theme.json. When the slug
				// references a fontFamily that no longer exists (e.g.
				// a customer saved `gt-walsheim-pro` before that family
				// was removed from theme.json), we DROP the declaration
				// entirely so the rest of the CSS cascade (theme
				// stylesheets defining the new default) wins instead of
				// pinning the page to a non-existent typeface.
				if ( 'font-family' === $k ) {
					$emit_value = self::resolve_font_family_slug( (string) $emit_value );
					if ( '' === $emit_value ) {
						continue;
					}
				}
				// Defense in depth: strip CSS structural chars from the value
				// (including font-family) before concat.
				$emit_value = self::sanitize_css_value( $emit_value );
				if ( '' === $emit_value ) {
					continue;
				}
				$decls .= sprintf( '%s:%s;', $css_prop, $emit_value );
			}
		}
		return $decls;
	}

	/**
	 * Resolve a font-family slug to the canonical CSS fontFamily string
	 * declared in theme.json. Returns:
	 *   - The fontFamily declaration when the slug exists in theme.json.
	 *   - The input unchanged when it already looks like a CSS declaration
	 *     (contains whitespace or a comma — e.g. older Kirki-era saves
	 *     that stored `Roboto, sans-serif` directly).
	 *   - An empty string when the slug doesn't match any current
	 *     theme.json fontFamily. Caller drops the declaration so the
	 *     rest of the CSS cascade defines the visible family. Without
	 *     this guard, a customer-saved slug from a removed font wedges
	 *     the page to a `font-family: <dead-slug>;` rule that no browser
	 *     can resolve and the visitor sees the system serif fallback.
	 *
	 * Cached statically because typography_declarations() may run dozens
	 * of times per request (one per registered typography field) and
	 * wp_get_global_settings() walks the merged theme.json tree.
	 *
	 * @param string $value Possibly-a-slug font-family value.
	 * @return string Canonical fontFamily declaration safe to emit, OR ''
	 *                to signal the caller to drop the declaration entirely.
	 */
	protected static function resolve_font_family_slug( string $value ): string {
		if ( '' === $value ) {
			return '';
		}
		// Already a CSS declaration (contains whitespace or commas).
		if ( preg_match( '/[\s,]/', $value ) ) {
			return $value;
		}
		static $cache = null;
		if ( null === $cache ) {
			$cache = array();
			if ( function_exists( 'wp_get_global_settings' ) ) {
				$settings = wp_get_global_settings();
				$families = $settings['typography']['fontFamilies']['theme'] ?? array();
				foreach ( $families as $f ) {
					if ( isset( $f['slug'], $f['fontFamily'] ) ) {
						$cache[ (string) $f['slug'] ] = (string) $f['fontFamily'];
					}
				}
			}
		}
		// Slug present in theme.json → emit the canonical declaration.
		if ( isset( $cache[ $value ] ) ) {
			return $cache[ $value ];
		}
		// Not a theme.json slug. The Typography control stores Google Fonts
		// by their family NAME (e.g. `Roboto`, `Poppins`) — see
		// Controls\Typography::available_font_families(), where the Google
		// group is `name => name`. Single-word family names ("Roboto") have
		// no space/comma, so they reach here looking just like a slug and
		// were previously DROPPED — which is why a selected Google font never
		// applied on the front-end (the font was enqueued, but no
		// `font-family` rule was ever emitted).
		//
		// Distinguish a real family name from a dead kebab-case slug
		// (e.g. `gt-walsheim-pro`, left over from a removed theme.json
		// family): real Google family names are Title Case — they contain an
		// uppercase letter — while dead slugs are all-lowercase with hyphens.
		// This mirrors the slug-shaped skip in Fonts\Component::get_google_fonts().
		// Emit a real family quoted with a generic fallback so the browser can
		// resolve it during/after the Google Fonts load; drop the dead slug so
		// the rest of the CSS cascade defines the visible family.
		if ( strtolower( $value ) !== $value ) {
			return sprintf( '"%s", sans-serif', $value );
		}
		// All-lowercase single token with no space — dead slug. Drop.
		return '';
	}

	/**
	 * Build background CSS declarations from a structured value array.
	 * background-image is wrapped in url(...); other keys passed through.
	 */
	protected static function background_declarations( array $value ): string {
		$decls = '';
		$keys  = array(
			'background-color',
			'background-image',
			'background-repeat',
			'background-position',
			'background-size',
			'background-attachment',
		);
		foreach ( $keys as $k ) {
			if ( empty( $value[ $k ] ) ) {
				continue;
			}
			$v = (string) $value[ $k ];
			if ( 'background-image' === $k ) {
				$v = sprintf( "url('%s')", esc_url( $v ) );
			} else {
				// Non-image background keys (color, repeat, position, size,
				// attachment) need defense-in-depth CSS-value sanitization.
				$v = self::sanitize_css_value( $v );
				if ( '' === $v ) {
					continue;
				}
			}
			$decls .= sprintf( '%s:%s;', $k, $v );
		}
		return $decls;
	}

	/**
	 * Default CSS property for types that have an obvious one when the rule omits 'property'.
	 */
	protected static function default_property( string $type ): string {
		switch ( $type ) {
			case 'color':
				return 'color';
			case 'dimension':
				return 'width';
			default:
				return '';
		}
	}
}

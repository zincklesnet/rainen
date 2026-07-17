/**
 * BuddyX Customizer Framework — live preview JS.
 *
 * Loaded on the customizer preview iframe. Reads window.reignCustomizerOutputs
 * (injected by Component::enqueue_preview()) and updates inline CSS in <head>
 * without a full refresh whenever a postMessage-transported setting changes.
 *
 * Output payload shape:
 *   {
 *     "site_primary_color": [
 *       { "element": ".site-primary", "property": "color", "units": "" }
 *     ],
 *     "body_typography": [
 *       { "element": "body", "property": "", "units": "" }
 *     ]
 *   }
 *
 * @package buddyx
 */
(function () {
	'use strict';

	if (typeof wp === 'undefined' || !wp.customize) {
		return;
	}

	function ensureStyle() {
		let el = document.getElementById('buddyx-customizer-preview-css');
		if (!el) {
			el = document.createElement('style');
			el.id = 'buddyx-customizer-preview-css';
			document.head.appendChild(el);
		}
		return el;
	}

	function setCss(settingId, css) {
		const el = ensureStyle();
		const re = new RegExp('/\\*\\s*' + settingId + '\\s*\\*/[\\s\\S]*?/\\*\\s*end\\s*\\*/');
		const block = '/* ' + settingId + ' */' + css + '/* end */';
		el.textContent = re.test(el.textContent) ? el.textContent.replace(re, block) : el.textContent + block;
	}

	/**
	 * Ensure a Google Fonts stylesheet for `family` is present in the preview
	 * iframe head, then return the CSS font-family value to emit.
	 *
	 * The front-end Fonts\Component only enqueues fonts for SAVED theme_mods,
	 * so a font picked live in the customizer is not yet loaded in the preview.
	 * Without injecting it here, the preview emits `font-family:Roboto` but the
	 * webfont never loads, so nothing visibly changes until publish + refresh
	 * (the reported "live preview does not update immediately" symptom).
	 *
	 * Mirrors PHP Output_Builder::resolve_font_family_slug():
	 *   - empty                     -> '' (drop)
	 *   - contains space/comma       -> already a CSS stack, emit as-is
	 *   - theme.json slug            -> resolve to canonical fontFamily (no load)
	 *   - Title-Case family name     -> load from Google, emit quoted + fallback
	 *   - all-lowercase kebab slug   -> dead slug, drop
	 *
	 * @param {string} family Raw font-family value from the control.
	 * @return {string} CSS font-family value, or '' to drop the declaration.
	 */
	const loadedGoogleFonts = {};
	const themeFontSlugs = (window.reignCustomizerFontSlugs || {});
	function resolveFontFamily(family) {
		family = String(family || '').trim();
		if (!family) {
			return '';
		}
		// Already a CSS declaration (stack with whitespace or comma).
		if (/[\s,]/.test(family)) {
			return family;
		}
		// theme.json slug -> canonical fontFamily (self-hosted; no Google load).
		if (Object.prototype.hasOwnProperty.call(themeFontSlugs, family)) {
			return themeFontSlugs[family];
		}
		// Title-Case name -> a real Google family. Load it and emit quoted.
		if (family.toLowerCase() !== family) {
			if (!loadedGoogleFonts[family]) {
				loadedGoogleFonts[family] = true;
				const link = document.createElement('link');
				link.rel = 'stylesheet';
				link.href =
					'https://fonts.googleapis.com/css?family=' +
					encodeURIComponent(family) +
					'&display=swap';
				document.head.appendChild(link);
			}
			return '"' + family + '", sans-serif';
		}
		// All-lowercase kebab slug with no space — dead slug. Drop.
		return '';
	}

	/**
	 * Build CSS for a Typography object value (matches PHP Output_Builder).
	 */
	function typographyCss(element, val) {
		// Kirki legacy 'variant' -> font-weight (+ font-style for italic combos)
		if (val.variant && !val['font-weight']) {
			let v = String(val.variant).toLowerCase();
			let style = '';
			if (v.indexOf('italic') !== -1) {
				style = 'italic';
				v = v.replace('italic', '').trim();
			}
			if (v === 'regular' || v === '') {
				v = '400';
			} else if (v === 'bold') {
				v = '700';
			}
			val['font-weight'] = v;
			if (style && !val['font-style']) {
				val['font-style'] = style;
			}
		}

		const map = {
			'font-family': 'font-family',
			'font-weight': 'font-weight',
			'font-size': 'font-size',
			'line-height': 'line-height',
			'letter-spacing': 'letter-spacing',
			'text-transform': 'text-transform',
			'font-style': 'font-style',
			'text-align': 'text-align',
			'text-decoration': 'text-decoration',
		};
		let decls = '';
		Object.entries(map).forEach(([k, p]) => {
			if (!val[k]) {
				return;
			}
			let emit = val[k];
			// font-family may be a theme.json slug or a Google family name —
			// resolve it (and lazy-load Google fonts) so the preview matches
			// the published front-end produced by PHP Output_Builder.
			if (k === 'font-family') {
				emit = resolveFontFamily(emit);
				if (!emit) {
					return;
				}
			}
			decls += p + ':' + emit + ';';
		});
		return decls ? element + '{' + decls + '}' : '';
	}

	/**
	 * Build CSS for a Background object value (matches PHP Output_Builder).
	 */
	function backgroundCss(element, val) {
		const keys = [
			'background-color',
			'background-image',
			'background-repeat',
			'background-position',
			'background-size',
			'background-attachment',
		];
		let decls = '';
		keys.forEach((k) => {
			if (!val[k]) {
				return;
			}
			const v = k === 'background-image' ? "url('" + val[k] + "')" : val[k];
			decls += k + ':' + v + ';';
		});
		return decls ? element + '{' + decls + '}' : '';
	}

	if (window.reignCustomizerOutputs) {
		Object.entries(window.reignCustomizerOutputs).forEach(([settingId, payload]) => {
			const type = payload && payload._type ? payload._type : '';
			const rules = (payload && payload.rules) || [];
			wp.customize(settingId, (value) => {
				value.bind((newVal) => {
					let css = '';
					rules.forEach((r) => {
						if (newVal && typeof newVal === 'object' && !Array.isArray(newVal)) {
							if (type === 'background') {
								css += backgroundCss(r.element, newVal);
							} else {
								css += typographyCss(r.element, newVal);
							}
						} else if (newVal !== '' && newVal !== null && typeof newVal !== 'undefined') {
							const property = r.property || 'color';
							const units = r.units || '';
							css += r.element + '{' + property + ':' + newVal + units + ';}';
						}
					});
					setCss(settingId, css);
				});
			});
		});
	}

	// Dark Mode Colors section: the controls side asks us to show the preview
	// in dark (or back to light) so the buyer can see their dark-mode edits.
	// wp.customize.preview only exists once the preview messenger is ready.
	wp.customize.bind('preview-ready', function () {
		wp.customize.preview.bind('reign-dark-preview', function (isOpen) {
			document.documentElement.setAttribute('data-bx-mode', isOpen ? 'dark' : 'light');
		});
	});
})();

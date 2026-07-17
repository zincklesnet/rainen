/**
 * BuddyX Customizer Framework — control JS bundle.
 *
 * Wires up dynamic UI for our custom controls. Loaded on customize.php only.
 *
 * Controls handled here:
 *   - buddyx-typography  6-input sync, JSON-encoded value
 *   - buddyx-switch      checkbox -> int sync
 *   - buddyx-dimension   number + unit -> '120px' string
 *   - buddyx-slider      range + number sync, unit suffix
 *   - buddyx-repeater    add/remove/reorder rows, JSON-encoded array
 *   - buddyx-sortable    drag-reorder + checkbox toggle, JSON-encoded array
 *
 * No JS needed for: buddyx-color (handled by parent class), buddyx-checkbox,
 * buddyx-radio-image, buddyx-radio-buttonset, buddyx-custom-html, buddyx-upload.
 *
 * @package buddyx
 */
(function () {
	'use strict';

	if (typeof wp === 'undefined' || !wp.customize) {
		return;
	}

	/**
	 * Live active_callback re-evaluation.
	 *
	 * The PHP framework compiles array-form active_callback conditions to a
	 * server-side closure that only runs on initial customizer load. This wires
	 * the same conditions (exported as window.reignCustomizerActiveCallbacks)
	 * to their dependency settings, so a control's `active` state updates the
	 * moment a parent toggle changes — e.g. "Set Custom Colors?" now shows/hides
	 * its dependent colour controls live, with no reload.
	 */
	(function () {
		var map = window.reignCustomizerActiveCallbacks;
		if (!map || typeof map !== 'object') {
			return;
		}

		var TRUTHY = [true, 1, '1', 'on', 'yes', 'true', 'enable'];
		var FALSY = [false, 0, '0', '', 'off', 'no', 'false', 'disable', null];

		function inList(list, v) {
			for (var i = 0; i < list.length; i++) {
				if (list[i] === v) {
					return true;
				}
			}
			return false;
		}

		// Boolean-equivalence compare — mirrors PHP Active_Callback::values_equal.
		function valuesEqual(a, b) {
			var aBool = inList(TRUTHY, a) || inList(FALSY, a);
			var bBool = inList(TRUTHY, b) || inList(FALSY, b);
			if (aBool && bBool) {
				return inList(TRUTHY, a) === inList(TRUTHY, b);
			}
			return a == b; // eslint-disable-line eqeqeq
		}

		function condHolds(cond) {
			var setting = wp.customize(cond.setting);
			var actual = setting ? setting.get() : undefined;
			var expected = cond.value;
			switch (cond.operator || '==') {
				case '!=':
				case '!==':
					return !valuesEqual(actual, expected);
				case 'in':
					return Array.isArray(expected) ? expected.indexOf(actual) !== -1 : false;
				case 'contains':
					return String(actual).indexOf(String(expected)) !== -1;
				case '>':
					return actual > expected;
				case '<':
					return actual < expected;
				case '>=':
					return actual >= expected;
				case '<=':
					return actual <= expected;
				default:
					return valuesEqual(actual, expected);
			}
		}

		wp.customize.bind('ready', function () {
			Object.keys(map).forEach(function (controlId) {
				var conditions = map[controlId];
				if (!conditions || !conditions.length) {
					return;
				}

				function evaluate() {
					var active = conditions.every(condHolds);
					wp.customize.control(controlId, function (control) {
						control.active.set(active);
					});
				}

				conditions.forEach(function (cond) {
					wp.customize(cond.setting, function (setting) {
						setting.bind(evaluate);
					});
				});

				evaluate();
			});
		});
	})();

	/**
	 * Typography control
	 */
	wp.customize.controlConstructor['buddyx-typography'] = wp.customize.Control.extend({
		ready: function () {
			const ctl = this;
			const root = ctl.container[0].querySelector('.buddyx-typography-controls');
			const hidden = ctl.container[0].querySelector('.buddyx-typo-value');
			if (!root || !hidden) {
				return;
			}
			const familyEl = root.querySelector('.buddyx-typo-family');
			const weightEl = root.querySelector('.buddyx-typo-weight');
			const styleEl = root.querySelector('.buddyx-typo-style');
			const sizeEl = root.querySelector('.buddyx-typo-size');
			const lhEl = root.querySelector('.buddyx-typo-line-height');
			const lsEl = root.querySelector('.buddyx-typo-letter-spacing');
			const ttEl = root.querySelector('.buddyx-typo-transform');
			const alignEl = root.querySelector('.buddyx-typo-align');
			const decorEl = root.querySelector('.buddyx-typo-decoration');

			// Build the family <select>: "Default (theme)" + grouped optgroups.
			const fontData = ctl.params.fontFamilies || {};
			while (familyEl.firstChild) {
				familyEl.removeChild(familyEl.firstChild);
			}
			const defOpt = document.createElement('option');
			defOpt.value = fontData.default || '';
			defOpt.textContent = fontData.default_label || 'Default (theme)';
			familyEl.appendChild(defOpt);
			(fontData.groups || []).forEach(function (group) {
				const og = document.createElement('optgroup');
				og.label = group.label;
				Object.keys(group.fonts || {}).forEach(function (val) {
					const opt = document.createElement('option');
					opt.value = val;
					opt.textContent = group.fonts[val];
					og.appendChild(opt);
				});
				familyEl.appendChild(og);
			});
			(ctl.params.weights || ['400']).forEach((w) => weightEl.add(new Option(w, w)));

			// Read current value from wp.customize state (WP doesn't auto-serialize
			// structured/object values to hidden inputs, so hidden.value is unreliable).
			// Merge over the field's PHP-declared default so partial saves (e.g.
			// only the [color] sub-key was set in another section) don't blank out
			// typography keys. Tolerant of Kirki legacy 'variant' key.
			const settingVal = ctl.setting.get();
			const defaultVal = ctl.params.default || {};
			const merged = Object.assign({}, defaultVal,
				(settingVal && typeof settingVal === 'object' && !Array.isArray(settingVal)) ? settingVal : {}
			);
			const initial = merged;
			// Kirki legacy 'variant' may bake italic into the value (e.g. '700italic')
			let rawVariant = String(initial['font-weight'] || initial.variant || '400').toLowerCase();
			let initialStyle = initial['font-style'] || 'normal';
			if (rawVariant.indexOf('italic') !== -1) {
				initialStyle = 'italic';
				rawVariant = rawVariant.replace('italic', '').trim() || '400';
			}
			const weightMap = { regular: '400', bold: '700', '': '400' };
			const initialWeight = weightMap[rawVariant] || rawVariant;

			// Reflect the saved value.
			familyEl.value = (initial && initial['font-family']) ? initial['font-family'] : '';
			// If the saved value isn't in the catalog (e.g. an older Kirki
			// pick), inject it so the control reflects reality.
			const savedFam = (initial && initial['font-family']) ? initial['font-family'] : '';
			if (savedFam && !familyEl.querySelector('option[value="' + window.CSS.escape(savedFam) + '"]')) {
				const customGroup = document.createElement('optgroup');
				customGroup.label = fontData.saved_label || 'Saved';
				const savedOpt = document.createElement('option');
				savedOpt.value = savedFam;
				savedOpt.textContent = savedFam;
				customGroup.appendChild(savedOpt);
				familyEl.insertBefore(customGroup, familyEl.children[1] || null);
				familyEl.value = savedFam;
			}
			// Searchable filter above the family select (insert once).
			if (!familyEl.parentNode.querySelector('.buddyx-typo-family-search')) {
				const familySearch = document.createElement('input');
				familySearch.type = 'search';
				familySearch.className = 'buddyx-typo-family-search';
				familySearch.placeholder = fontData.search_placeholder || 'Search fonts…';
				familyEl.parentNode.insertBefore(familySearch, familyEl);
				familySearch.addEventListener('input', function () {
					const q = familySearch.value.toLowerCase();
					[].forEach.call(familyEl.querySelectorAll('optgroup'), function (og) {
						let anyVisible = false;
						[].forEach.call(og.querySelectorAll('option'), function (opt) {
							const match = opt.textContent.toLowerCase().indexOf(q) !== -1;
							opt.hidden = !match;
							if (match) { anyVisible = true; }
						});
						og.hidden = !anyVisible;
					});
				});
			}
			weightEl.value = initialWeight;
			if (styleEl) styleEl.value = initialStyle;
			sizeEl.value = parseFloat(initial['font-size']) || 16;
			lhEl.value = parseFloat(initial['line-height']) || 1.5;
			lsEl.value = parseFloat(initial['letter-spacing']) || 0;
			ttEl.value = initial['text-transform'] || 'none';
			if (alignEl) alignEl.value = initial['text-align'] || '';
			if (decorEl) decorEl.value = initial['text-decoration'] || '';
			// Push the hydrated value back into the setting so saving without
			// changes still emits a coherent shape (Kirki preserved this).
			hidden.value = JSON.stringify(initial);

			const sync = () => {
				// Merge over the current setting so foreign sub-keys (e.g. [color])
				// that other sections may own — survive untouched.
				const current = ctl.setting.get();
				const base = (current && typeof current === 'object' && !Array.isArray(current)) ? current : {};
				const v = Object.assign({}, base, {
					'font-family':     familyEl.value,
					'font-weight':     weightEl.value,
					'font-style':      styleEl ? styleEl.value : (base['font-style'] || 'normal'),
					'font-size':       sizeEl.value + 'px',
					'line-height':     String(lhEl.value),
					'letter-spacing':  lsEl.value + 'em',
					'text-transform':  ttEl.value,
					'text-align':      alignEl ? alignEl.value : (base['text-align'] || ''),
					'text-decoration': decorEl ? decorEl.value : (base['text-decoration'] || ''),
				});
				hidden.value = JSON.stringify(v);
				ctl.setting.set(v);
			};
			[familyEl, weightEl, styleEl, sizeEl, lhEl, lsEl, ttEl, alignEl, decorEl]
				.filter(Boolean)
				.forEach((el) => el.addEventListener('change', sync));

			// Helper: ensure the family <select> has an option for an
			// externally-supplied value (e.g. a preset's font-family
			// stack). Reuses a single "Saved" optgroup with a clean
			// primary-family label so repeat preset picks don't pile up.
			function ensureFamilyOption(value) {
				if (!value) {
					return;
				}
				const escaped = window.CSS.escape(value);
				if (familyEl.querySelector('option[value="' + escaped + '"]')) {
					return;
				}
				const cleanLabel = String(value).split(',')[0].replace(/['"]/g, '').trim() || value;
				let savedGroup = familyEl.querySelector('optgroup[data-buddyx-saved="1"]');
				if (savedGroup) {
					while (savedGroup.firstChild) {
						savedGroup.removeChild(savedGroup.firstChild);
					}
				} else {
					savedGroup = document.createElement('optgroup');
					savedGroup.label = fontData.saved_label || 'Saved';
					savedGroup.setAttribute('data-buddyx-saved', '1');
					familyEl.insertBefore(savedGroup, familyEl.children[1] || null);
				}
				const opt = document.createElement('option');
				opt.value = value;
				opt.textContent = cleanLabel;
				savedGroup.appendChild(opt);
			}

			// External-setting-change sync: when another script writes
			// to this typography setting, mirror the new value into the
			// visible UI inputs so the customer sidebar stays in step
			// with the preview iframe.
			let suppressEcho = false;
			ctl.setting.bind(function (newValue) {
				if (suppressEcho) {
					return;
				}
				if (!newValue || typeof newValue !== 'object') {
					return;
				}
				suppressEcho = true;
				try {
					const newFam = newValue['font-family'] || '';
					ensureFamilyOption(newFam);
					familyEl.value = newFam;
					weightEl.value = newValue['variant'] || newValue['font-weight'] || weightEl.value;
					if (styleEl) {
						styleEl.value = newValue['font-style'] || styleEl.value;
					}
					sizeEl.value = parseFloat(newValue['font-size']) || sizeEl.value;
					lhEl.value = parseFloat(newValue['line-height']) || lhEl.value;
					lsEl.value = parseFloat(newValue['letter-spacing']) || 0;
					ttEl.value = newValue['text-transform'] || 'none';
					if (alignEl) {
						alignEl.value = newValue['text-align'] || '';
					}
					if (decorEl) {
						decorEl.value = newValue['text-decoration'] || '';
					}
					hidden.value = JSON.stringify(newValue);
				} finally {
					suppressEcho = false;
				}
			});
		},
	});

	/**
	 * Background composite control — 6 sub-inputs, structured-array value.
	 */
	wp.customize.controlConstructor['buddyx-background'] = wp.customize.Control.extend({
		ready: function () {
			const ctl = this;
			const root = ctl.container[0].querySelector('.buddyx-background-controls');
			const hidden = ctl.container[0].querySelector('.buddyx-bg-value');
			if (!root || !hidden) {
				return;
			}
			const colorEl = root.querySelector('.buddyx-bg-color');
			const imageEl = root.querySelector('.buddyx-bg-image');
			const pickBtn = root.querySelector('.buddyx-bg-image-pick');
			const repeatEl = root.querySelector('.buddyx-bg-repeat');
			const positionEl = root.querySelector('.buddyx-bg-position');
			const sizeEl = root.querySelector('.buddyx-bg-size');
			const attachmentEl = root.querySelector('.buddyx-bg-attachment');

			// Same as Typography: read from wp.customize state and merge over defaults
			// so partial saves don't blank out other sub-keys.
			const bgSettingVal = ctl.setting.get();
			const bgDefault = ctl.params.default || {};
			const initial = Object.assign({}, bgDefault,
				(bgSettingVal && typeof bgSettingVal === 'object' && !Array.isArray(bgSettingVal)) ? bgSettingVal : {}
			);
			colorEl.value = initial['background-color'] || '';
			imageEl.value = initial['background-image'] || '';
			repeatEl.value = initial['background-repeat'] || 'repeat';
			positionEl.value = initial['background-position'] || 'center center';
			sizeEl.value = initial['background-size'] || 'auto';
			attachmentEl.value = initial['background-attachment'] || 'scroll';
			hidden.value = JSON.stringify(initial);

			const sync = () => {
				const current = ctl.setting.get();
				const base = (current && typeof current === 'object' && !Array.isArray(current)) ? current : {};
				const v = Object.assign({}, base, {
					'background-color': colorEl.value,
					'background-image': imageEl.value,
					'background-repeat': repeatEl.value,
					'background-position': positionEl.value,
					'background-size': sizeEl.value,
					'background-attachment': attachmentEl.value,
				});
				hidden.value = JSON.stringify(v);
				ctl.setting.set(v);
			};
			[imageEl, repeatEl, positionEl, sizeEl, attachmentEl].forEach((el) =>
				el.addEventListener('change', sync)
			);

			// Premium UX: wire wp-color-picker (iris) onto the color sub-input
			// so it renders a swatch + popover picker, not a plain text input.
			if (jQuery.fn && jQuery.fn.wpColorPicker) {
				jQuery(colorEl).wpColorPicker({
					change: () => sync(),
					clear: () => sync(),
				});
			} else {
				colorEl.addEventListener('change', sync);
			}

			// Wire the WP media frame to the image URL input.
			if (pickBtn && wp.media) {
				let frame = null;
				pickBtn.addEventListener('click', (e) => {
					e.preventDefault();
					if (!frame) {
						frame = wp.media({
							title: 'Select Background Image',
							library: { type: 'image' },
							multiple: false,
						});
						frame.on('select', () => {
							const att = frame.state().get('selection').first().toJSON();
							imageEl.value = att.url || '';
							sync();
						});
					}
					frame.open();
				});
			}
		},
	});

	/**
	 * Switch / Toggle control
	 */
	wp.customize.controlConstructor['buddyx-switch'] = wp.customize.Control.extend({
		ready: function () {
			const ctl = this;
			const cb = ctl.container[0].querySelector('.buddyx-switch-input');
			if (!cb) {
				return;
			}
			cb.addEventListener('change', () => {
				ctl.setting.set(cb.checked ? 1 : 0);
			});
		},
	});

	/**
	 * Dimension control
	 */
	wp.customize.controlConstructor['buddyx-dimension'] = wp.customize.Control.extend({
		ready: function () {
			const ctl = this;
			const numEl = ctl.container[0].querySelector('.buddyx-dimension-number');
			const unitEl = ctl.container[0].querySelector('.buddyx-dimension-unit');
			const hidden = ctl.container[0].querySelector('.buddyx-dimension-value');
			if (!numEl || !unitEl || !hidden) {
				return;
			}
			const initial = String(hidden.value || '0px');
			const m = initial.match(/^(-?[\d.]+)(px|em|rem|%|vh|vw)?$/i);
			if (m) {
				numEl.value = m[1];
				unitEl.value = m[2] || 'px';
			}
			const sync = () => {
				const v = (numEl.value || '0') + (unitEl.value || 'px');
				hidden.value = v;
				ctl.setting.set(v);
			};
			numEl.addEventListener('change', sync);
			unitEl.addEventListener('change', sync);
		},
	});

	/**
	 * Slider control
	 */
	wp.customize.controlConstructor['buddyx-slider'] = wp.customize.Control.extend({
		ready: function () {
			const ctl = this;
			const wrap = ctl.container[0].querySelector('.buddyx-slider-controls');
			const rangeEl = ctl.container[0].querySelector('.buddyx-slider-range');
			const numberEl = ctl.container[0].querySelector('.buddyx-slider-number');
			const hidden = ctl.container[0].querySelector('.buddyx-slider-value');
			if (!wrap || !rangeEl || !numberEl || !hidden) {
				return;
			}
			const unit = wrap.dataset.unit || 'px';
			const settingVal = String(ctl.setting.get() || '');
			const m = settingVal.match(/^(-?[\d.]+)/);
			const initialNum = m ? m[1] : (rangeEl.min || '0');
			rangeEl.value = initialNum;
			numberEl.value = initialNum;
			hidden.value = initialNum + unit;

			// Premium UX: paint the gradient track to visually reflect the range value.
			const updateFill = () => {
				const min = parseFloat(rangeEl.min) || 0;
				const max = parseFloat(rangeEl.max) || 100;
				const val = parseFloat(rangeEl.value) || min;
				const pct = ((val - min) / (max - min)) * 100;
				rangeEl.style.setProperty('--reign-fill', pct + '%');
			};
			updateFill();

			const sync = (src) => {
				const n = src.value;
				rangeEl.value = n;
				numberEl.value = n;
				const v = n + unit;
				hidden.value = v;
				ctl.setting.set(v);
				updateFill();
			};
			rangeEl.addEventListener('input', () => sync(rangeEl));
			numberEl.addEventListener('change', () => sync(numberEl));
		},
	});

	/**
	 * Repeater control
	 */
	wp.customize.controlConstructor['buddyx-repeater'] = wp.customize.Control.extend({
		ready: function () {
			const ctl = this;
			const root = ctl.container[0].querySelector('.buddyx-repeater-rows');
			const hidden = ctl.container[0].querySelector('.buddyx-repeater-value');
			const addBtn = ctl.container[0].querySelector('.buddyx-repeater-add');
			if (!root || !hidden || !addBtn) {
				return;
			}
			const fields = ctl.params.fields || {};
			// Read array value from wp.customize state. Setting may hold either
			// an array (live) or a JSON-encoded string (post-save round-trip via
			// sanitize_json_array).
			let rows = ctl.setting.get();
			if (typeof rows === 'string') {
				try { rows = JSON.parse(rows); } catch (e) { rows = []; }
			}
			if (!Array.isArray(rows)) {
				rows = ctl.params.default || [];
				if (!Array.isArray(rows)) rows = [];
			}

			function renderRow(rowData) {
				const wrap = document.createElement('div');
				wrap.className = 'buddyx-repeater-row';
				const handle = document.createElement('span');
				handle.className = 'buddyx-repeater-handle';
				handle.setAttribute('aria-label', 'Drag to reorder');
				handle.textContent = '⋮⋮';
				wrap.appendChild(handle);

				Object.entries(fields).forEach(([key, fdef]) => {
					const label = document.createElement('label');
					const span = document.createElement('span');
					span.textContent = (fdef && fdef.label) || key;
					label.appendChild(span);
					const input = document.createElement('input');
					input.type = (fdef && fdef.type === 'url') ? 'url' : 'text';
					input.value = (rowData && rowData[key]) || '';
					input.dataset.key = key;
					input.addEventListener('change', sync);
					label.appendChild(input);
					wrap.appendChild(label);
				});

				const trash = document.createElement('button');
				trash.type = 'button';
				trash.className = 'buddyx-repeater-trash';
				trash.textContent = 'Remove';
				trash.addEventListener('click', () => {
					wrap.remove();
					sync();
				});
				wrap.appendChild(trash);

				return wrap;
			}

			function sync() {
				const out = [];
				root.querySelectorAll('.buddyx-repeater-row').forEach((rowEl) => {
					const o = {};
					rowEl.querySelectorAll('input[data-key]').forEach((i) => {
						o[i.dataset.key] = i.value;
					});
					out.push(o);
				});
				hidden.value = JSON.stringify(out);
				ctl.setting.set(out);
			}

			rows.forEach((r) => root.appendChild(renderRow(r)));
			addBtn.addEventListener('click', () => {
				root.appendChild(renderRow({}));
				sync();
			});

			if (window.jQuery && window.jQuery.fn.sortable) {
				window.jQuery(root).sortable({
					handle: '.buddyx-repeater-handle',
					update: sync,
				});
			}
		},
	});

	/**
	 * Sortable control
	 *
	 * Storage contract: a flat array of slug strings in display order.
	 * Templates iterate the value directly with
	 *   foreach ( get_theme_mod($id, $default) as $slug ) { ... }
	 * so this control must NEVER emit object rows ({slug, enabled}) or
	 * a JSON string into the setting — sanitize_sortable_slugs() coerces
	 * legacy shapes on save, but the JS should round-trip the canonical
	 * shape directly so live-preview matches what gets written.
	 *
	 * Render contract: every choice declared by PHP renders a row, in
	 * either user-saved order (saved slugs first, in their saved order)
	 * or default order (when nothing is saved). Disabled rows have an
	 * unchecked box and are excluded from the saved value. Without
	 * rendering disabled rows the user can never re-enable a disabled
	 * icon — there'd be no row to toggle.
	 */
	wp.customize.controlConstructor['buddyx-sortable'] = wp.customize.Control.extend({
		ready: function () {
			const ctl = this;
			const root = ctl.container[0].querySelector('.buddyx-sortable-list');
			const hidden = ctl.container[0].querySelector('.buddyx-sortable-value');
			if (!root || !hidden) {
				return;
			}
			const choices = ctl.params.choices || {};
			const choiceSlugs = Object.keys(choices);
			if (!choiceSlugs.length) {
				return;
			}

			// 1. Read whatever the setting currently holds. Tolerate every
			//    shape we have ever emitted across versions:
			//      - flat slug array  ['search', 'cart']
			//      - object array     [{slug:'search', enabled:true}, ...]
			//      - JSON-encoded form of either
			//      - null / undefined / non-array (falls back to defaults)
			function readEnabledSlugs() {
				let raw = ctl.setting.get();
				if (typeof raw === 'string' && raw !== '') {
					try { raw = JSON.parse(raw); } catch (e) { raw = null; }
				}
				if (!Array.isArray(raw)) {
					raw = Array.isArray(ctl.params.defaultSlugs) ? ctl.params.defaultSlugs : [];
				}
				const out = [];
				raw.forEach((item) => {
					if (typeof item === 'string' && item) {
						out.push(item);
					} else if (item && typeof item === 'object' && item.slug) {
						// Legacy {slug, enabled} shape — drop disabled rows.
						if (item.enabled !== false) {
							out.push(String(item.slug));
						}
					}
				});
				return out;
			}

			// 2. Compose the FULL row order: enabled-and-saved slugs first
			//    (in their saved order, filtered to keys that still exist
			//    in `choices`), then any remaining choice slugs as
			//    unchecked rows so the user can drag them in or toggle them
			//    back on. Without this branch a removed icon is gone forever.
			const enabled = new Set();
			const ordered = [];
			readEnabledSlugs().forEach((slug) => {
				if (choices[slug] && !enabled.has(slug)) {
					enabled.add(slug);
					ordered.push(slug);
				}
			});
			choiceSlugs.forEach((slug) => {
				if (!enabled.has(slug)) {
					ordered.push(slug);
				}
			});

			// 3. Paint rows.
			ordered.forEach((slug) => {
				const li = document.createElement('li');
				li.dataset.slug = slug;
				if (!enabled.has(slug)) {
					li.classList.add('is-disabled');
				}
				const handle = document.createElement('span');
				handle.className = 'buddyx-sortable-handle';
				handle.setAttribute('aria-label', 'Drag to reorder');
				handle.textContent = '⋮⋮';
				li.appendChild(handle);
				const lab = document.createElement('label');
				const cb = document.createElement('input');
				cb.type = 'checkbox';
				cb.checked = enabled.has(slug);
				cb.addEventListener('change', sync);
				lab.appendChild(cb);
				const txt = document.createElement('span');
				txt.textContent = choices[slug] || slug;
				lab.appendChild(txt);
				li.appendChild(lab);
				root.appendChild(li);
			});

			// 4. Write back the canonical shape on every change: an array
			//    of enabled slug strings in current visible order.
			function sync() {
				const out = [];
				root.querySelectorAll('li').forEach((li) => {
					const slug = li.dataset.slug;
					if (!slug) {
						return;
					}
					const cb = li.querySelector('input[type="checkbox"]');
					const isOn = !!(cb && cb.checked);
					li.classList.toggle('is-disabled', !isOn);
					if (isOn) {
						out.push(slug);
					}
				});
				hidden.value = JSON.stringify(out);
				ctl.setting.set(out);
			}

			// Initial hidden-input sync so the first preview reflects the
			// rendered state (user can drag-reorder before clicking any
			// checkbox, and the postMessage transport needs a payload).
			sync();

			if (window.jQuery && window.jQuery.fn.sortable) {
				window.jQuery(root).sortable({
					handle: '.buddyx-sortable-handle',
					update: sync,
				});
			}
		},
	});

	/**
	 * Color control — alpha (rgba) extension.
	 *
	 * WP's bundled Iris/wp-color-picker does not support an alpha channel. When
	 * a Customizer Framework Color field is registered with
	 * `'choices' => array('alpha' => true)`, params.alpha is true and we render
	 * an extra opacity slider next to the picker. The value emitted to the
	 * customize setting is `rgba(r, g, b, a)`. Without alpha, the original
	 * WP_Customize_Color_Control behavior is preserved unchanged.
	 *
	 * Pairs with PHP-side Field::sanitize_color_alpha which preserves the
	 * rgba string on save (commit d77f114).
	 */
	(function () {
		var OriginalColor = wp.customize.controlConstructor.color;
		if (!OriginalColor) {
			return;
		}
		wp.customize.controlConstructor.color = OriginalColor.extend({
			ready: function () {
				var ctl = this;
				if (!ctl.params || !ctl.params.alpha) {
					OriginalColor.prototype.ready.apply(ctl, arguments);
					return;
				}
				var $ = window.jQuery;
				var $input = ctl.container.find('.color-picker-hex');
				var initial = String(ctl.setting.get() || '');
				var initialHex = initial;
				var initialAlpha = 1;
				var rgba = initial.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*,\s*([\d.]+))?\s*\)$/i);
				if (rgba) {
					initialHex =
						'#' +
						[rgba[1], rgba[2], rgba[3]]
							.map(function (n) {
								return ('0' + parseInt(n, 10).toString(16)).slice(-2);
							})
							.join('');
					initialAlpha = rgba[4] !== undefined ? parseFloat(rgba[4]) : 1;
				}
				var $wrap = $('<div class="buddyx-color-alpha"></div>');
				var $label = $('<label class="buddyx-color-alpha-label">Opacity: <span class="buddyx-color-alpha-value"></span></label>');
				var $slider = $('<input type="range" min="0" max="1" step="0.01" class="buddyx-color-alpha-slider" />').val(initialAlpha);
				$wrap.append($label).append($slider);
				$input.closest('.customize-control-content').append($wrap);

				function hexToRgb(hex) {
					var h = String(hex || '').replace(/^#/, '');
					if (h.length === 3) {
						h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
					}
					if (!/^[0-9a-f]{6}$/i.test(h)) {
						return null;
					}
					return [parseInt(h.slice(0, 2), 16), parseInt(h.slice(2, 4), 16), parseInt(h.slice(4, 6), 16)];
				}

				function computeOutput() {
					var hexVal = $input.val() || initialHex || '#000000';
					var rgb = hexToRgb(hexVal);
					var alphaVal = parseFloat($slider.val());
					if (isNaN(alphaVal)) {
						alphaVal = 1;
					}
					$wrap.find('.buddyx-color-alpha-value').text(Math.round(alphaVal * 100) + '%');
					if (!rgb) {
						return null;
					}
					return alphaVal >= 1
						? 'rgb(' + rgb[0] + ', ' + rgb[1] + ', ' + rgb[2] + ')'
						: 'rgba(' + rgb[0] + ', ' + rgb[1] + ', ' + rgb[2] + ', ' + alphaVal + ')';
				}

				function syncToSetting() {
					var out = computeOutput();
					if (out === null) {
						return;
					}
					// Don't write the same value back — that marks the setting
					// dirty on init and persists the default to theme_mods on
					// save, clobbering the style-variation overlay downstream.
					if (out === String(ctl.setting.get() || '')) {
						return;
					}
					ctl.setting.set(out);
				}

				$input.val(initialHex).wpColorPicker({
					change: function () {
						setTimeout(syncToSetting, 0);
					},
					clear: function () {
						ctl.setting.set('');
					},
				});
				$slider.on('input change', syncToSetting);
				// Update the alpha-value indicator label only — no setting.set()
				// on initial render. The picker is a passive observer of the
				// saved value until the customer actually changes it.
				computeOutput();
			},
		});
	})();

	/**
	 * Dynamic per-control colour defaults.
	 *
	 * When the customer picks a Style preset, rewrite the relevant per-control
	 * colour fields' `params.default` to track the chosen palette so the
	 * built-in WP color picker reset button reverts to the palette's value
	 * rather than the theme's hard-coded default. Restores originals when
	 * the customer picks the empty "Default" preset.
	 *
	 * Reads window.reignStyleVariationDefaults exported by
	 * Customizer_Framework\Component::enqueue_controls. Saved theme_mod
	 * values are NOT touched — only the reset target moves.
	 */
	(function () {
		var map = window.reignStyleVariationDefaults;
		if ( ! map || typeof map !== 'object' ) {
			return;
		}

		var presetSetting = 'site_style_variation';
		// Authoritative theme-baseline defaults shipped from PHP - avoids
		// racing the customizer's async control-registration timing.
		var stash = ( window.reignOriginalColorDefaults && typeof window.reignOriginalColorDefaults === 'object' )
			? window.reignOriginalColorDefaults
			: {};

		// suppressNextChange guards the recursive case: setting().set()
		// fires the change event on every per-control setting we update,
		// and if one of those is bound back to the preset picker (none
		// are today but a future field might be), we don't want to
		// re-enter applyDefaults. The flag is also useful when a manual
		// programmatic init wants to seed defaults without retriggering
		// the preset listener.
		var suppressNextChange = false;

		/**
		 * Apply the preset's color values to BOTH the reset target
		 * (ctl.params.default) AND the live setting value (wp.customize
		 * (id).set()). Setting the value is what makes the per-control
		 * colour pickers visually update to the preset's palette so the
		 * site owner sees the new starting point and can tweak from there.
		 *
		 * Skips settings the customer has already customized away from
		 * the previous preset's value - we don't want to overwrite a
		 * deliberate per-control colour just because the preset changed.
		 * Tracked via the previous-preset values in the map.
		 */
		// Repaint a single colour control's wpColorPicker UI to match a
		// new value. wp.customize(id).set() updates the setting state
		// but the picker widget doesn't re-render its swatch + input
		// from setting changes, so we have to push the value into the
		// picker directly via .wpColorPicker('color', newColor). Without
		// this the underlying setting flips but the visible picker still
		// shows the old value, which reads as "preset has no effect".
		function repaintColorPicker( ctl, newColor ) {
			if ( ! ctl || ! ctl.container || ! jQuery.fn || ! jQuery.fn.wpColorPicker ) {
				return;
			}
			var $picker = ctl.container.find( '.wp-color-picker, input.color-picker-hex, input[type="text"].wp-color-picker' ).first();
			if ( ! $picker.length ) {
				$picker = ctl.container.find( 'input[type="text"]' ).first();
			}
			if ( $picker.length && $picker.data( 'a8c-iris' ) ) {
				$picker.wpColorPicker( 'color', newColor );
			}
		}

		function applyDefaults( slug, options ) {
			var defaults = map[ slug ];
			if ( ! defaults ) {
				return;
			}
			var alsoSetValue = ! ( options && options.targetOnly );
			suppressNextChange = true;
			Object.keys( defaults ).forEach( function ( settingId ) {
				var newColor = defaults[ settingId ];
				var ctl = wp.customize.control( settingId );
				if ( ctl && ctl.params ) {
					ctl.params.default = newColor;
				}
				if ( alsoSetValue && wp.customize( settingId ) ) {
					wp.customize( settingId ).set( newColor );
					repaintColorPicker( ctl, newColor );
				}
			} );
			// Release the suppress flag at the end of the current tick so
			// the per-setting change events have all fired before any
			// future preset listener runs.
			setTimeout( function () { suppressNextChange = false; }, 0 );
		}

		function restoreDefaults( options ) {
			var alsoSetValue = ! ( options && options.targetOnly );
			suppressNextChange = true;
			Object.keys( stash ).forEach( function ( settingId ) {
				var origDefault = stash[ settingId ];
				var ctl = wp.customize.control( settingId );
				if ( ctl && ctl.params ) {
					ctl.params.default = origDefault;
				}
				if ( alsoSetValue && wp.customize( settingId ) ) {
					wp.customize( settingId ).set( origDefault );
					repaintColorPicker( ctl, origDefault );
				}
			} );
			setTimeout( function () { suppressNextChange = false; }, 0 );
		}

		wp.customize.bind( 'ready', function () {
			var current = wp.customize( presetSetting ) ? wp.customize( presetSetting ).get() : '';
			// On initial load, only point the reset target at the saved
			// preset - DON'T overwrite the customer's current colour
			// values. They're already what the customer expects to see.
			if ( current && map[ current ] ) {
				applyDefaults( current, { targetOnly: true } );
			}

			// Catch controls that register after ready - apply the active
			// variation's default to any late arrivals so reset still
			// targets the picked palette.
			wp.customize.control.bind( 'add', function ( ctl ) {
				if ( ! ctl || ! ctl.id || ! ctl.params ) {
					return;
				}
				var preset = wp.customize( presetSetting ) ? wp.customize( presetSetting ).get() : '';
				if ( ! preset || ! map[ preset ] ) {
					return;
				}
				if ( typeof map[ preset ][ ctl.id ] !== 'undefined' ) {
					ctl.params.default = map[ preset ][ ctl.id ];
				}
			} );

			wp.customize( presetSetting, function ( setting ) {
				setting.bind( function ( newValue ) {
					// User actively picked a preset in this session -
					// apply the colours so per-control pickers reflect
					// the new palette as the starting point. They can
					// tweak any individual colour from there.
					if ( ! newValue ) {
						restoreDefaults();
						return;
					}
					applyDefaults( newValue );
				} );
			} );
		} );
	})();

	/**
	 * Tooltip rendering.
	 *
	 * Reads window.reignCustomizerTooltips (a setting-id -> text map exported
	 * by Customizer_Framework\Component::enqueue_controls) and injects a small
	 * info icon after each matching control's label. The icon is a button so
	 * keyboard users can tab to it; clicking toggles a popover with the text.
	 * Outside-click and Esc dismiss. Activates the existing `tooltip` field
	 * argument that field definitions across the codebase already declare.
	 */
	(function () {
		var map = window.reignCustomizerTooltips;
		if (!map || typeof map !== 'object') {
			return;
		}

		var openPopover = null;

		function closeOpen() {
			if (openPopover) {
				openPopover.trigger.attr('aria-expanded', 'false');
				openPopover.popover.remove();
				openPopover = null;
			}
		}

		jQuery(document).on('click.reignTooltip', function (event) {
			if (!openPopover) {
				return;
			}
			if (jQuery(event.target).closest('.buddyx-tooltip-trigger, .buddyx-tooltip-popover').length) {
				return;
			}
			closeOpen();
		});

		jQuery(document).on('keydown.reignTooltip', function (event) {
			if (event.key === 'Escape' && openPopover) {
				var $t = openPopover.trigger;
				closeOpen();
				$t.trigger('focus');
			}
		});

		function attach(controlId, text) {
			var ctl = wp.customize.control(controlId);
			if (!ctl) {
				return;
			}
			ctl.deferred.embedded.done(function () {
				var $container = ctl.container;
				var $title = $container.find('.customize-control-title').first();
				if (!$title.length || $title.find('.buddyx-tooltip-trigger').length) {
					return;
				}
				var $btn = jQuery('<button type="button" class="buddyx-tooltip-trigger" aria-label="More info" aria-expanded="false"><span aria-hidden="true">i</span></button>');
				$btn.on('click', function (event) {
					event.preventDefault();
					event.stopPropagation();
					if (openPopover && openPopover.trigger.is($btn)) {
						closeOpen();
						return;
					}
					closeOpen();
					var $pop = jQuery('<div class="buddyx-tooltip-popover" role="tooltip"></div>').text(text);
					$btn.attr('aria-expanded', 'true').after($pop);
					openPopover = { trigger: $btn, popover: $pop };
				});
				$title.append(' ').append($btn);
			});
		}

		wp.customize.bind('ready', function () {
			Object.keys(map).forEach(function (id) {
				attach(id, map[id]);
			});
		});
	})();

	/**
	 * Unified "Color Palette" picker (reign_color_preset).
	 *
	 * One control lists every starting palette - modern Site Skins AND the
	 * classic Reign schemes - and drives the two real, mutually-exclusive
	 * settings the theme actually reads:
	 *   skin-<slug>   -> site_style_variation = <slug>
	 *   scheme-<slug> -> reign_color_scheme   = <slug>, site_style_variation = ''
	 *   default       -> site_style_variation = '', reign_color_scheme = 'reign_clean'
	 * The raw site_style_variation + reign_color_scheme controls are hidden so
	 * this single picker is the one place a site owner chooses a palette.
	 */
	wp.customize('reign_color_preset', 'site_style_variation', 'reign_color_scheme', function (preset, skin, scheme) {
		// The scheme every PHP consumer falls back to - the registered default
		// of reign_color_scheme (see Colors_Fields.php). "Default (your current
		// colors)" maps to it: the customer's own hand-set Individual Colors
		// are saved in this scheme's namespace, so restoring it brings back
		// exactly the colors they had before trying a palette.
		var defaultScheme = 'reign_clean';

		// Reflect the real saved state in the picker (a skin wins if one is set).
		// A saved "default" preset and the default scheme are the same state, so
		// don't flip the picker (and dirty the setting) between the two labels.
		var current = skin.get() ? ('skin-' + skin.get())
			: (scheme.get() ? ('scheme-' + scheme.get()) : 'default');
		if (preset.get() !== current && !('default' === preset.get() && 'scheme-' + defaultScheme === current)) {
			preset.set(current);
		}

		preset.bind(function (val) {
			if (val.indexOf('skin-') === 0) {
				skin.set(val.slice(5));
			} else if (val.indexOf('scheme-') === 0) {
				scheme.set(val.slice(7));
				skin.set('');
			} else {
				// "Default (your current colors)": restore the default scheme.
				// Leaving reign_color_scheme untouched here kept the last
				// picked palette applied forever (Basecamp #9981580751).
				scheme.set(defaultScheme);
				skin.set('');
			}
		});

		wp.customize.control('site_style_variation', function (c) {
			c.container.hide();
		});
		wp.customize.control('reign_color_scheme', function (c) {
			c.container.hide();
		});
	});

	/**
	 * Render a real colour swatch next to each Color Palette option so buyers
	 * choose by sight. Colours come from window.reignPaletteSwatches (localized
	 * by Site_Skin_Fields::enqueue_palette_swatches): [bg, accent, text, surface].
	 */
	wp.customize.control('reign_color_preset', function (control) {
		control.deferred.embedded.done(function () {
			var sw = window.reignPaletteSwatches || {};
			var $ = window.jQuery;
			if (!$) { return; }
			control.container.find('input[type="radio"]').each(function () {
				var colors = sw[this.value];
				var $label = $(this).closest('label');
				if (!$label.length) { $label = $(this).parent(); }
				if (!colors || !$label.length || $label.find('.reign-pal-sw').length) {
					return;
				}
				var chips = '';
				for (var i = 0; i < colors.length; i++) {
					chips += '<i style="background:' + colors[i] + '"></i>';
				}
				$label.addClass('reign-pal-opt');
				$(this).after('<span class="reign-pal-sw">' + chips + '</span>');
			});

			// Make the WHOLE palette row clickable - the swatch, the name and the
			// gaps - not just the small radio dot. (Clicking the radio itself is
			// left to native handling to avoid a double toggle.)
			control.container.on('click', '.reign-pal-opt', function (e) {
				if (e.target && e.target.tagName === 'INPUT') {
					return;
				}
				var radio = this.querySelector('input[type="radio"]');
				if (radio && !radio.checked) {
					radio.checked = true;
					$(radio).trigger('change');
				}
			});
		});

		// When the "Dark Mode Colors" section is open, flip the live preview to
		// dark mode so the edits are visible; restore the prior mode on close.
		wp.customize.section('reign_dark_colors', function (section) {
			function sync(isOpen) {
				if (wp.customize.previewer) {
					wp.customize.previewer.send('reign-dark-preview', !!isOpen);
				}
			}
			section.expanded.bind(sync);
			// The dark controls use refresh transport, so each edit reloads the
			// preview iframe back to its default mode. Re-assert dark on every
			// preview reconnect while the section is open so the edit stays visible.
			if (wp.customize.previewer) {
				wp.customize.previewer.bind('ready', function () {
					if (section.expanded()) {
						sync(true);
					}
				});
			}
		});

		// ---- Typography Font Preset -------------------------------------
		// A preset is a complete typographic SYSTEM: each role (body / heading /
		// menu / quote) carries family, weight, size, line-height, tracking,
		// transform and style. Applying it writes those into the matching
		// settings (merging, so text-align etc. survive). Heading size is ''
		// on purpose - it clears any pinned px back to the theme's responsive
		// scale. Default is a no-op so existing sites are untouched.
		(function ($) {
			var ROLE_OF = {
				'reign_body_typography': 'body',
				'site_tagline_typography_option': 'body',
				'reign_title_tagline_typography': 'heading',
				'reign_h1_typography': 'heading',
				'reign_h2_typography': 'heading',
				'reign_h3_typography': 'heading',
				'reign_h4_typography': 'heading',
				'reign_h5_typography': 'heading',
				'reign_h6_typography': 'heading',
				'reign_header_main_menu_font': 'menu',
				'reign_header_sub_menu_font': 'menu',
				'reign_quote_typography': 'quote'
			};

			// A role is an object of CSS-ready values (font-family, font-weight,
			// line-height, letter-spacing, text-transform, font-style). Apply =
			// copy those onto the setting, keep 'variant' in sync for the font
			// loader, and ALWAYS clear font-size so sizing stays on the theme's
			// responsive scale. Everything else on the setting is preserved.
			function applyPreset(p) {
				Object.keys(ROLE_OF).forEach(function (id) {
					var role = p.roles[ROLE_OF[id]];
					if (!role) { return; }
					wp.customize(id, function (s) {
						var cur = s.get();
						var next = (cur && typeof cur === 'object') ? $.extend({}, cur) : {};
						Object.keys(role).forEach(function (k) { next[k] = role[k]; });
						next['variant'] = role['font-weight']; // font loader reads variant||font-weight
						next['font-size'] = '';                 // never pin size -> responsive scale
						s.set(next);
					});
				});
			}

			wp.customize('reign_typography_preset', function (setting) {
				setting.bind(function (val) {
					var presets = window.reignTypographyPresets || {};
					if (!val || val === 'default' || !presets[val] || !presets[val].roles) { return; }
					if (wp.customize('reign_custom_typography')) {
						var ct = wp.customize('reign_custom_typography').get();
						if (ct === 'off' || ct === false || ct === '0' || ct === 0) {
							wp.customize('reign_custom_typography').set('on');
						}
					}
					applyPreset(presets[val]);
				});
			});

			// Preview each preset in its real typefaces: heading (in its weight),
			// body, and the distinct italic/normal blockquote sample.
			wp.customize.control('reign_typography_preset', function (control) {
				control.deferred.embedded.done(function () {
					var presets = window.reignTypographyPresets || {};
					control.container.find('input[type="radio"]').each(function () {
						var p = presets[this.value];
						var $label = $(this).closest('label');
						if (!$label.length) { $label = $(this).parent(); }
						if (!$label.length || $label.hasClass('reign-typo-opt')) { return; }
						// Every option is a card (incl. Default) so the set reads
						// as one group. DOM order ends up radio, name, second-line.
						$label.addClass('reign-typo-opt');
						if (p && p.roles) {
							var h = p.roles.heading, b = p.roles.body, q = p.roles.quote;
							$label.append(
								'<span class="reign-typo-pair">' +
								'<b style="font-family:\'' + h['font-family'] + '\';font-weight:' + h['font-weight'] + '">' + h['font-family'] + '</b>' +
								'<i style="font-family:\'' + b['font-family'] + '\'">' + b['font-family'] + '</i>' +
								'<q style="font-family:\'' + q['font-family'] + '\';font-style:' + q['font-style'] + '">&ldquo;quote&rdquo;</q>' +
								'</span>'
							);
						} else {
							// Default (or any preset-less option): a short note so
							// the card has a matching second line and is not orphaned.
							$label.append('<span class="reign-typo-note">' + (window.reignTypographyDefaultNote || '') + '</span>');
						}
					});
				});
				// Whole row clickable, like the colour palette.
				control.container.on('click', '.reign-typo-opt', function (e) {
					if (e.target && e.target.tagName === 'INPUT') { return; }
					var radio = this.querySelector('input[type="radio"]');
					if (radio && !radio.checked) { radio.checked = true; $(radio).trigger('change'); }
				});
			});
		})(jQuery);
	});
})();

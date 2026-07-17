/**
 * Visitor color-mode toggle — light → dark → auto cycle.
 *
 * Pairs with the bootstrap script in inc/Tokens/Component.php which sets
 * <html data-bx-mode> from localStorage in <head> (FOUC-safe). This module
 * just handles the click cycle, syncs the button state, and writes back
 * to localStorage.
 *
 * @package
 */

(function () {
	'use strict';

	const STORAGE_KEY = 'bx-color-mode';
	const ORDER = ['light', 'dark', 'auto'];
	const LABELS = {
		light: 'Light mode (click to switch to dark)',
		dark: 'Dark mode (click to switch to system)',
		auto: 'System mode (click to switch to light)',
	};

	/**
	 * Read an EXPLICIT prior choice from localStorage. Returns the stored mode
	 * only when the visitor actually toggled before (a real value written by
	 * onClick). Returns null when nothing valid is stored so callers can fall
	 * back to the server-rendered intent instead of guessing.
	 *
	 * @return {string|null} Stored mode (light|dark|auto) or null if none.
	 */
	function readStored() {
		try {
			const v = window.localStorage.getItem(STORAGE_KEY);
			if (v && ORDER.indexOf(v) !== -1) {
				return v;
			}
		} catch (e) {
			/* private mode — treat as no stored choice */
		}
		return null;
	}

	/**
	 * Resolve the effective mode with the correct precedence:
	 *   1. A real prior user choice in localStorage (returning toggler) wins.
	 *   2. Otherwise the server-rendered <html data-bx-mode> attribute wins.
	 *      The bootstrap script in Component.php already set this from the
	 *      admin's chosen palette / Default Mode, so a fresh visitor (no
	 *      localStorage) sees the admin's dark skin on first load.
	 *   3. Final safety fallback is 'light'.
	 *
	 * @return {string} One of light|dark|auto.
	 */
	function readPersisted() {
		const stored = readStored();
		if (stored) {
			return stored;
		}
		const attr = document.documentElement.getAttribute('data-bx-mode');
		return ORDER.indexOf(attr) !== -1 ? attr : 'light';
	}

	/**
	 * Write mode to localStorage (best-effort; never throws).
	 * @param {string} mode One of light|dark|auto.
	 */
	function writePersisted(mode) {
		try {
			window.localStorage.setItem(STORAGE_KEY, mode);
		} catch (e) {
			/* private mode — accept ephemeral state */
		}
	}

	/**
	 * Swap the site logo for its dark-mode variant when the page is dark.
	 *
	 * The modern toggle is client-side, so the logo swap is too (the legacy
	 * server-cookie swap in PHP never fires for this toggle). Only runs when
	 * an admin actually set a dark logo (window.reignDarkLogo is localized).
	 * The original light src/srcset is cached on the element so we can restore
	 * it when returning to light. For "auto" we follow the OS preference.
	 *
	 * @param {string} mode One of light|dark|auto.
	 */
	function swapLogo(mode) {
		if (typeof window.reignDarkLogo === 'undefined' || !window.reignDarkLogo.dark) {
			return;
		}
		const isDark =
			mode === 'dark' ||
			(mode === 'auto' &&
				window.matchMedia &&
				window.matchMedia('(prefers-color-scheme: dark)').matches);
		const logos = document.querySelectorAll('.custom-logo');
		for (let i = 0; i < logos.length; i++) {
			const img = logos[i];
			if (typeof img.dataset.reignLightSrc === 'undefined') {
				img.dataset.reignLightSrc = img.getAttribute('src') || '';
				img.dataset.reignLightSrcset = img.getAttribute('srcset') || '';
			}
			if (isDark) {
				img.setAttribute('src', window.reignDarkLogo.dark);
				img.removeAttribute('srcset');
			} else {
				if (img.dataset.reignLightSrc) {
					img.setAttribute('src', img.dataset.reignLightSrc);
				}
				if (img.dataset.reignLightSrcset) {
					img.setAttribute('srcset', img.dataset.reignLightSrcset);
				}
			}
		}
	}

	/**
	 * Apply mode to <html> + sync all buttons + dispatch event.
	 * @param {string} mode One of light|dark|auto.
	 */
	function applyMode(mode) {
		document.documentElement.setAttribute('data-bx-mode', mode);
		swapLogo(mode);
		const buttons = document.querySelectorAll('.bx-color-mode-toggle__btn');
		for (let i = 0; i < buttons.length; i++) {
			const btn = buttons[i];
			btn.setAttribute('data-mode', mode);
			btn.setAttribute(
				'aria-pressed',
				mode === 'dark' ? 'true' : 'false'
			);
			btn.setAttribute('aria-label', LABELS[mode]);
			const sr = btn.querySelector('.screen-reader-text');
			if (sr) {
				sr.textContent = LABELS[mode];
			}
		}
		document.dispatchEvent(
			new CustomEvent('bx:color-mode-change', {
				detail: { mode },
			})
		);
	}

	/**
	 * Cycle to the next mode in the rotation.
	 * @param {string} current Current mode.
	 * @return {string} Next mode in the cycle.
	 */
	function cycle(current) {
		const idx = ORDER.indexOf(current);
		return ORDER[(idx + 1) % ORDER.length];
	}

	/**
	 * Click handler — delegated so dynamically-added buttons (mobile menu open) still work.
	 *
	 * This is the ONLY place that writes localStorage. A passive page load
	 * (init / pageshow / applyMode) never persists, so the server-rendered
	 * default is not masked on subsequent loads for a visitor who never
	 * explicitly toggled. Once the visitor clicks, their choice is stored and
	 * wins on every later load (see readPersisted precedence).
	 *
	 * @param {MouseEvent} e Click event.
	 */
	function onClick(e) {
		const btn =
			e.target.closest && e.target.closest('.bx-color-mode-toggle__btn');
		if (!btn) {
			return;
		}
		e.preventDefault();
		const current = btn.getAttribute('data-mode') || readPersisted();
		const next = cycle(current);
		writePersisted(next); // Real user choice — persist it.
		applyMode(next);
	}

	/**
	 * Re-sync on bfcache restore so back/forward shows current state.
	 * @param {PageTransitionEvent} e Page-show event.
	 */
	function onPageShow(e) {
		if (e.persisted) {
			applyMode(readPersisted());
		}
	}

	/**
	 * Boot: sync button state to the effective mode without persisting.
	 *
	 * When the visitor has a real stored choice we re-apply it (covers the
	 * rare case where the bootstrap attribute and storage diverge). When there
	 * is no stored choice we leave the server-rendered <html data-bx-mode>
	 * untouched and only sync the button UI to it — so the admin's first-load
	 * dark palette is preserved and nothing gets written to localStorage.
	 */
	function init() {
		const stored = readStored();
		if (stored) {
			applyMode(stored);
		} else {
			// No explicit choice: respect the server default already on <html>;
			// just mirror it onto the toggle buttons. Never writes localStorage.
			applyMode(readPersisted());
		}
		document.addEventListener('click', onClick);
		window.addEventListener('pageshow', onPageShow);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();

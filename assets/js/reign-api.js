/**
 * Reign REST API JavaScript client.
 *
 * A modern, fetch-based client for the /wp-json/reign/v1/* surface
 * shipped in 8.0.0.
 *
 *   window.reignApi.signup({ user_login, user_email, user_pass, first_name, last_name })
 *   window.reignApi.magicLink({ email })   // email OR username accepted
 *   window.reignApi.refreshNonces()
 *   window.reignApi.getColorMode()
 *   window.reignApi.setColorMode(mode)
 *   window.reignApi.getInfo()
 *
 * Note: there is intentionally NO window.reignApi.signin() method.
 * Login flows through wp-login.php (or the existing admin-ajax
 * `reign-signin-form` handler) so security plugins (iThemes
 * Security, Wordfence, Loginizer) can hook 2FA + brute-force
 * protection at the wp-login.php request lifecycle. See
 * inc/REST/README.md for the full rationale.
 *
 * Every method returns a Promise resolving to the JSON body, or
 * rejecting with `{ code, message, data, status }` for non-2xx
 * responses. Server-side WP_Error objects come back as JS rejections
 * automatically.
 *
 * The script is enqueued in the footer (no render-blocking) and reads
 * its configuration from window.reignApiConfig — populated server-side
 * via wp_localize_script in inc/init.php.
 *
 *   reignApiConfig = {
 *     root:           '/wp-json/reign/v1/',
 *     rest_nonce:     '<wp_create_nonce("wp_rest")>',
 *     ajax_fallback:  '/wp-admin/admin-ajax.php',  // for legacy fallback
 *     logged_in:      false  // detected from `logged-in` body class at runtime
 *   };
 *
 * Standalone WordPress compatible — no jQuery, no BP, no plugin
 * dependency.
 *
 * @package reign
 * @since 8.0.0
 */
( function ( window, document ) {
	'use strict';

	/**
	 * Read the REST root + nonce LIVE per request so the inline
	 * `<script id="reign-per-user-nonces">` that runs at wp_footer:30
	 * (after this script's IIFE) populates the values before the first
	 * fetch call fires.
	 */
	function cfg() {
		return window.reignApiConfig || {};
	}

	function root() {
		return ( cfg().root || '/wp-json/reign/v1/' );
	}

	function nonce() {
		return cfg().rest_nonce || '';
	}

	/**
	 * Detect logged-in state at call time (not at script load) so the
	 * value stays accurate after `wp.heartbeat` rotates the session.
	 *
	 * @return {boolean}
	 */
	function isLoggedIn() {
		return !! ( document.body && document.body.classList && document.body.classList.contains( 'logged-in' ) );
	}

	/**
	 * Issue a fetch() request to a Reign REST endpoint.
	 *
	 * @param {string} path   Relative to /wp-json/reign/v1/ (no leading slash).
	 * @param {Object} [opts] fetch() options ({ method, body, headers, ... }).
	 * @return {Promise<Object>}
	 */
	function call( path, opts ) {
		opts = opts || {};

		var url = root().replace( /\/$/, '' ) + '/' + path.replace( /^\//, '' );
		var headers = Object.assign(
			{
				'Accept': 'application/json',
				'Content-Type': 'application/json'
			},
			opts.headers || {}
		);

		// Only send X-WP-Nonce when a nonce is actually present.
		// Sending an empty nonce header to a cookie-authenticated WP
		// REST request triggers a 403 even on `permission_callback:
		// __return_true` endpoints (the rest_cookie_check_errors
		// filter fires before the endpoint permission check).
		var n = nonce();
		if ( n ) {
			headers['X-WP-Nonce'] = n;
		}

		var init = {
			method: opts.method || 'GET',
			credentials: 'same-origin',
			headers: headers
		};

		if ( opts.body && init.method !== 'GET' && init.method !== 'HEAD' ) {
			init.body = ( typeof opts.body === 'string' ) ? opts.body : JSON.stringify( opts.body );
		}

		return fetch( url, init ).then( function ( res ) {
			var contentType = res.headers.get( 'content-type' ) || '';
			var isJson = contentType.indexOf( 'application/json' ) !== -1;
			var bodyPromise = isJson ? res.json() : res.text();

			return bodyPromise.then( function ( body ) {
				if ( res.ok ) {
					return body;
				}
				// Match WP_Error shape: { code, message, data: { status } }.
				var err = ( typeof body === 'object' && body ) ? body : { message: body };
				err.status = err.status || ( err.data && err.data.status ) || res.status;
				return Promise.reject( err );
			} );
		} );
	}

	/**
	 * Convenience wrappers around the v1 endpoints.
	 */
	var api = {

		// POST /reign/v1/auth/signup
		signup: function ( payload ) {
			return call( 'auth/signup', { method: 'POST', body: payload } );
		},

		// POST /reign/v1/auth/magic-link
		magicLink: function ( payload ) {
			return call( 'auth/magic-link', { method: 'POST', body: payload } );
		},

		// GET /reign/v1/nonces — refreshes the per-user nonces removed
		// from cached HTML in 8.0.0. Logged-in only.
		refreshNonces: function () {
			return call( 'nonces', { method: 'GET' } ).then( function ( nonces ) {
				// Mirror the nonces into wp_main_js_obj so legacy AJAX
				// code can transparently use the refreshed values.
				if ( window.wp_main_js_obj && typeof window.wp_main_js_obj === 'object' ) {
					Object.assign( window.wp_main_js_obj, nonces );
				}
				return nonces;
			} );
		},

		// GET /reign/v1/preferences/color-mode
		getColorMode: function () {
			return call( 'preferences/color-mode', { method: 'GET' } );
		},

		// POST /reign/v1/preferences/color-mode
		setColorMode: function ( mode ) {
			return call( 'preferences/color-mode', { method: 'POST', body: { mode: mode } } );
		},

		// GET /reign/v1/info
		getInfo: function () {
			return call( 'info', { method: 'GET' } );
		},

		// Low-level escape hatch — call any /reign/v1/* endpoint directly.
		raw: call,

		// Helpers callers may want.
		isLoggedIn: isLoggedIn
	};

	window.reignApi = api;

	/**
	 * On DOM-ready, for logged-in visitors, refresh the per-user
	 * nonces in case the page was served from a long-lived cache
	 * where the inline `<script id="reign-per-user-nonces">` hadn't
	 * fired yet (or the nonces have expired since first render).
	 *
	 * The refresh is asynchronous and non-blocking — if it fails
	 * (e.g. user logged out in another tab), the existing inline
	 * nonces stay in place and legacy AJAX falls back to whatever
	 * was baked in.
	 */
	function refreshIfLoggedIn() {
		if ( ! isLoggedIn() ) {
			return;
		}
		api.refreshNonces().catch( function () {
			// Silent: user may have logged out, nonce endpoint may
			// be temporarily unreachable, etc. The existing inline
			// nonces are the fallback.
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', refreshIfLoggedIn );
	} else {
		refreshIfLoggedIn();
	}
} )( window, document );

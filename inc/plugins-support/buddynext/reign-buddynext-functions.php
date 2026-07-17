<?php
/**
 * Support For BuddyNext
 *
 * BuddyNext is the next-generation community engine (the successor to, and
 * runtime-exclusive with, BuddyPress). When it is active and BuddyPress is not,
 * this file wires BuddyNext into Reign's header so the logged-in user area
 * (notification bell, messages icon, avatar + profile dropdown) renders through
 * BuddyNext's own zero-JS section.
 *
 * The header-icon template parts already carry the BuddyNext branch; this
 * compatibility layer makes the integration robust and update-safe:
 *
 *   1. Guarantees the bell / messages / user-menu icons are present in Reign's
 *      header icon set (even on sites that trimmed the BuddyPress-era set).
 *   2. Feeds the site's assigned "User Profile" nav menu (the `menu-2` location)
 *      into the BuddyNext dropdown, so site owners present a real, fully
 *      admin-controlled menu — falling back to BuddyNext's own quick links when
 *      no menu is assigned. Log Out is always kept by BuddyNext.
 *
 * Loaded from functions.php only when BUDDYNEXT_VERSION is defined and
 * BuddyPress is inactive (the two are mutually exclusive at runtime).
 *
 * @package reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'reign_buddynext_ensure_header_icons' ) ) {
	/**
	 * Guarantee the BuddyNext-relevant icons are in Reign's header icon set.
	 *
	 * Reign renders header icons by iterating a sortable customizer set and
	 * loading `template-parts/header-icons/{slug}.php` for each slug. The
	 * BuddyNext branch lives inside the `message`, `notification` and
	 * `user-menu` parts, so those three slugs must be present for the section
	 * to appear. Reign's defaults already include them; this keeps them present
	 * (and ordered after search) even if a site removed the BuddyPress-era set.
	 *
	 * @param array $icons Default header icon slugs, in display order.
	 * @return array Filtered icon slugs.
	 */
	function reign_buddynext_ensure_header_icons( $icons ) {
		if ( ! is_array( $icons ) ) {
			$icons = array();
		}

		foreach ( array( 'message', 'notification', 'user-menu' ) as $required ) {
			if ( ! in_array( $required, $icons, true ) ) {
				$icons[] = $required;
			}
		}

		return $icons;
	}
	add_filter( 'reign_header_default_icons', 'reign_buddynext_ensure_header_icons' );
	add_filter( 'reign_mobile_header_default_icons', 'reign_buddynext_ensure_header_icons' );
}

if ( ! function_exists( 'reign_buddynext_user_profile_menu_items' ) ) {
	/**
	 * Read the site's assigned "User Profile" (menu-2) nav menu as a flat list
	 * of BuddyNext header-dropdown rows.
	 *
	 * Only top-level items are used (a header dropdown is single-level). Each
	 * row is `[ 'label' => string, 'url' => string, 'icon' => '' ]`; items carry
	 * no BuddyNext icon (the dropdown reserves the icon column so labels still
	 * align). Returns an empty array when no menu is assigned or it is empty —
	 * the caller then keeps BuddyNext's own default quick links.
	 *
	 * @return array<int,array{label:string,url:string,icon:string}>
	 */
	function reign_buddynext_user_profile_menu_items() {
		$locations = get_nav_menu_locations();
		if ( empty( $locations['menu-2'] ) ) {
			return array();
		}

		$menu_items = wp_get_nav_menu_items( (int) $locations['menu-2'] );
		if ( empty( $menu_items ) ) {
			return array();
		}

		$rows = array();
		foreach ( $menu_items as $item ) {
			// Top-level only — flatten nothing, skip child items.
			if ( ! empty( $item->menu_item_parent ) ) {
				continue;
			}
			$label = trim( (string) $item->title );
			$url   = trim( (string) $item->url );
			if ( '' === $label || '' === $url ) {
				continue;
			}
			$rows[] = array(
				'label' => $label,
				'url'   => $url,
				'icon'  => '',
			);
		}

		return $rows;
	}
}

if ( ! function_exists( 'reign_buddynext_filter_user_menu_links' ) ) {
	/**
	 * Present the site's "User Profile" menu inside the BuddyNext dropdown.
	 *
	 * When the admin has assigned a menu to Reign's "User Profile" (menu-2)
	 * location, those items become the dropdown's links — exactly the menu the
	 * site owner controls. Otherwise BuddyNext's own quick links are kept. Log
	 * Out is appended by BuddyNext regardless, so it is never lost.
	 *
	 * @param array $links   BuddyNext default quick links.
	 * @param int   $user_id Member ID (unused; the menu is global).
	 * @return array Filtered links.
	 */
	function reign_buddynext_filter_user_menu_links( $links, $user_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- signature required by the filter.
		$menu = reign_buddynext_user_profile_menu_items();

		return ! empty( $menu ) ? $menu : $links;
	}
	add_filter( 'buddynext_header_user_menu_links', 'reign_buddynext_filter_user_menu_links', 10, 2 );
}

if ( ! function_exists( 'reign_buddynext_auth_fullbleed_css' ) ) {
	/**
	 * Make BuddyNext's full-canvas auth pages sit flush with Reign's chrome.
	 *
	 * The login / signup / verify surfaces render edge-to-edge (a cover image
	 * beside the form). Reign's `.site-content` wrapper adds top/bottom padding
	 * that would show as a gap between the theme header/footer and that cover.
	 * Reign owns `.site-content`, so the fix belongs in the theme rather than as
	 * a cross-theme override inside the plugin. Scoped to the auth body class and
	 * the auth hub so no other BuddyNext or site page is affected.
	 *
	 * @return void
	 */
	function reign_buddynext_auth_fullbleed_css() {
		// Make BuddyNext's full-canvas auth fill the area between Reign's header
		// and footer EXACTLY — no magic numbers. A flex-column sticky-footer on
		// #page lets #content (and the wrapper chain down to .bn-app--auth) grow
		// to the precise remaining height, so the cover is full height with no
		// page scroll on short forms (login) and grows naturally on tall forms
		// (signup). Scoped to the auth body class so nothing else is touched.
		$css = 'body.bn-hub-auth #page.site{display:flex;flex-direction:column;min-height:100vh;}'
			. 'body.bn-hub-auth #content{flex:1 0 auto;display:flex;flex-direction:column;padding-top:0 !important;padding-bottom:0 !important;}'
			. 'body.bn-hub-auth #content > .container,'
			. 'body.bn-hub-auth .site-content-grid,'
			. 'body.bn-hub-auth .bn-app--auth{display:flex;flex-direction:column;flex:1 0 auto;}'
			. 'body.bn-hub-auth #colophon{flex:0 0 auto;}';

		wp_add_inline_style( 'reign_main_style', $css );
	}
	// Reign enqueues reign_main_style late, in reign_scripts at priority 5001;
	// this must run after that so the handle exists when the inline style attaches.
	add_action( 'wp_enqueue_scripts', 'reign_buddynext_auth_fullbleed_css', 6000 );
}

/**
 * --------------------------------------------------------------------------
 * Auth URL routing — point Reign's login / register / reset links at BuddyNext
 * --------------------------------------------------------------------------
 *
 * Reign models auth as three separate destinations (a mapped Login page, a
 * mapped Registration page, and wp-login.php's lost-password action). BuddyNext
 * instead serves a SINGLE auth hub page where signup and reset-password are
 * endpoints on that one page, not separate published pages. BuddyNext does not
 * filter WordPress core's login_url / register_url / lostpassword_url, so left
 * untouched Reign's links fall through to wp-login.php and bypass the BuddyNext
 * auth hub entirely.
 *
 * These filters close that gap. Every Reign auth link (header login/register
 * icons, masthead, the login form's "Register" + "Forgot password" links, the
 * login widget, and the BuddyPress-support shortcode) resolves its URL through
 * wp_login_url() / wp_registration_url() / wp_lostpassword_url() as the
 * FALLBACK, so a single set of core filters routes all of them to BuddyNext at
 * once. URLs are read straight from BuddyNext's public PageRouter API (no
 * hard-coded slugs), so a custom auth slug or the buddynext_*_url filters are
 * honoured.
 *
 * Two things still win over this fallback, by design:
 *   1. A mapped "Login Page" / "Registration Page" (reign_login_page /
 *      reign_registration_page) — the templates resolve the mapped page before
 *      the wp_*_url() fallback, so an explicit override is preserved.
 *   2. Reign's sign-in popup — when enabled the templates set the link href to
 *      '#' before the fallback, so the popup keeps opening.
 *
 * Scope is front-end only: wp-admin login, wp-login.php itself, AJAX, REST and
 * cron are left on WordPress core so administrator sign-in and the
 * reset-password-from-email flow keep working unchanged.
 */

if ( ! function_exists( 'reign_buddynext_is_frontend_auth_context' ) ) {
	/**
	 * Whether the current request is a front-end context where Reign's auth
	 * links should resolve to the BuddyNext auth hub.
	 *
	 * @return bool
	 */
	function reign_buddynext_is_frontend_auth_context() {
		if ( is_admin() ) {
			return false;
		}
		if ( isset( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) {
			return false;
		}
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return false;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'reign_buddynext_resolve_auth_url' ) ) {
	/**
	 * Resolve a BuddyNext auth URL from its public PageRouter API.
	 *
	 * @param string $type One of 'login', 'signup', 'reset'.
	 * @return string Resolved URL, or '' when BuddyNext is unavailable.
	 */
	function reign_buddynext_resolve_auth_url( $type ) {
		if ( ! class_exists( '\BuddyNext\Core\PageRouter' ) ) {
			return '';
		}

		switch ( $type ) {
			case 'signup':
				$url = \BuddyNext\Core\PageRouter::signup_url();
				break;
			case 'reset':
				$url = \BuddyNext\Core\PageRouter::reset_url();
				break;
			case 'login':
			default:
				$url = \BuddyNext\Core\PageRouter::auth_url();
				break;
		}

		return is_string( $url ) ? $url : '';
	}
}

if ( ! function_exists( 'reign_buddynext_filter_login_url' ) ) {
	/**
	 * Route WordPress's login URL to the BuddyNext auth hub on the front end.
	 *
	 * @param string $login_url The default login URL.
	 * @param string $redirect  Path to redirect to on login, if any.
	 * @return string
	 */
	function reign_buddynext_filter_login_url( $login_url, $redirect ) {
		if ( ! reign_buddynext_is_frontend_auth_context() ) {
			return $login_url;
		}

		$bn_url = reign_buddynext_resolve_auth_url( 'login' );
		if ( '' === $bn_url ) {
			return $login_url;
		}

		if ( ! empty( $redirect ) ) {
			$bn_url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $bn_url );
		}

		return $bn_url;
	}
	add_filter( 'login_url', 'reign_buddynext_filter_login_url', 20, 2 );
}

if ( ! function_exists( 'reign_buddynext_filter_register_url' ) ) {
	/**
	 * Route WordPress's registration URL to the BuddyNext signup endpoint on the
	 * front end.
	 *
	 * @param string $register_url The default registration URL.
	 * @return string
	 */
	function reign_buddynext_filter_register_url( $register_url ) {
		if ( ! reign_buddynext_is_frontend_auth_context() ) {
			return $register_url;
		}

		$bn_url = reign_buddynext_resolve_auth_url( 'signup' );

		return '' !== $bn_url ? $bn_url : $register_url;
	}
	add_filter( 'register_url', 'reign_buddynext_filter_register_url', 20, 1 );
}

if ( ! function_exists( 'reign_buddynext_filter_lostpassword_url' ) ) {
	/**
	 * Route WordPress's lost-password URL to the BuddyNext reset endpoint on the
	 * front end.
	 *
	 * @param string $lostpassword_url The default lost-password URL.
	 * @param string $redirect         Path to redirect to on reset, if any.
	 * @return string
	 */
	function reign_buddynext_filter_lostpassword_url( $lostpassword_url, $redirect ) {
		if ( ! reign_buddynext_is_frontend_auth_context() ) {
			return $lostpassword_url;
		}

		$bn_url = reign_buddynext_resolve_auth_url( 'reset' );
		if ( '' === $bn_url ) {
			return $lostpassword_url;
		}

		if ( ! empty( $redirect ) ) {
			$bn_url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $bn_url );
		}

		return $bn_url;
	}
	add_filter( 'lostpassword_url', 'reign_buddynext_filter_lostpassword_url', 20, 2 );
}

<?php
/**
 * Reign performance hooks — front-end-only resource trims.
 *
 * Each block in this file removes a WordPress-core asset that the theme
 * does not use. Every removal is conditional on `! is_admin()` so the
 * wp-admin UX (which relies on emoji + jquery-migrate + dashicons in
 * the toolbar) is untouched.
 *
 * @package reign
 * @since 8.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Disable WordPress emoji detection + replacement on the front-end.
 *
 * Reign is a community theme (BuddyPress / LearnDash); native emoji
 * rendering by the browser is universally supported on every device
 * Reign targets. The wp-emoji-release.min.js script (~10 KB) + its
 * inline detection script add 2 requests and a render-blocking
 * inline `<script>` to every front-end page with no functional gain.
 *
 * Removed only on front-end; the block editor and admin still need it
 * for the emoji button in TinyMCE / the post-editor.
 */
if ( ! function_exists( 'reign_disable_wp_emoji' ) ) :
	function reign_disable_wp_emoji() {
		if ( is_admin() ) {
			return;
		}
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		// Also remove the TinyMCE plugin for users who only edit on the front-end.
		add_filter(
			'tiny_mce_plugins',
			function ( $plugins ) {
				return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
			}
		);
	}
endif;
add_action( 'init', 'reign_disable_wp_emoji' );

/**
 * Dequeue jQuery Migrate on the front-end.
 *
 * jQuery Migrate is a compatibility shim for code written against
 * jQuery < 1.9 . Modern themes targeting WP 5.0+ have no use for it.
 * It adds ~9 KB and a separate HTTP request to every page. Reign 8.0.0
 * is verified to function on the modern jQuery 3.7 alone.
 *
 * Removed only on front-end so the back-end (which may depend on
 * legacy plugin JS) is unaffected.
 */
if ( ! function_exists( 'reign_dequeue_jquery_migrate' ) ) :
	function reign_dequeue_jquery_migrate( $scripts ) {
		if ( is_admin() ) {
			return;
		}
		if ( isset( $scripts->registered['jquery'] ) ) {
			$jquery_dependencies                       = $scripts->registered['jquery']->deps;
			$scripts->registered['jquery']->deps       = array_diff( $jquery_dependencies, array( 'jquery-migrate' ) );
		}
	}
endif;
add_action( 'wp_default_scripts', 'reign_dequeue_jquery_migrate' );

/**
 * Disable the wp-embed.min.js script + the two `<link rel="alternate"
 * type="application/json+oembed">` discovery links on the front-end.
 *
 * The wp-embed script is only needed when the page embeds external
 * WordPress posts via the legacy oEmbed embed-into-WP feature. Reign's
 * community templates render their own activity / post embeds through
 * BuddyPress and the Gutenberg block editor — neither of which require
 * wp-embed.min.js. Removing it saves 1 request + ~3 KB per page.
 */
if ( ! function_exists( 'reign_disable_oembed' ) ) :
	function reign_disable_oembed() {
		if ( is_admin() ) {
			return;
		}
		// The script.
		wp_dequeue_script( 'wp-embed' );
		// The two `<link>` discovery elements that WP emits on every page.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		// The REST endpoint that powers external embeds (keeps the
		// /wp-json/oembed/1.0/embed route registered — only removes the
		// auto-discovery HTML so other sites can't embed YOUR pages
		// via oEmbed unless they have the URL).
		// Intentionally NOT removed: the REST route stays available
		// because publishers may want their posts embeddable elsewhere.
	}
endif;
add_action( 'wp_enqueue_scripts', 'reign_disable_oembed', 100 );

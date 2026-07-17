<?php
/**
 * Reign Nonces REST Controller.
 *
 * Page-cache-safe nonce delivery. The P1 perf commit removed per-user
 * nonces from cached HTML (they used to be baked in via the `wp_localize_script()`
 * call, which made the cached HTML invalid for every visitor except the first).
 * This endpoint replaces them: a logged-in visitor's JS fetches fresh
 * nonces AFTER the page-cached HTML has rendered.
 *
 *   GET /wp-json/reign/v1/nonces
 *     Authenticated only. Sends Cache-Control: no-store.
 *     Returns the 4 nonces previously baked into the `wp_main_js_obj` object plus a
 *     generic _wpnonce for REST follow-ups.
 *
 * This endpoint is safe to call from a cached page: the page itself can
 * be edge-cached for everyone (no per-user data), and the nonce fetch
 * runs lazily for logged-in visitors only (page-cache plugins typically
 * skip caching auth-cookie'd requests anyway, so the back-end runs the
 * dynamic part live).
 *
 * @package reign
 * @since 8.0.0
 */

namespace Reign\REST;

defined( 'ABSPATH' ) || exit;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Nonces_Controller {

	const REST_NAMESPACE = Component::REST_NAMESPACE;

	public function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			'/nonces',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_nonces' ),
					'permission_callback' => array( $this, 'permission_check' ),
				),
			)
		);
	}

	/**
	 * Logged-in users only.
	 */
	public function permission_check(): bool {
		return is_user_logged_in();
	}

	/**
	 * GET /reign/v1/nonces
	 *
	 * @return WP_REST_Response
	 */
	public function get_nonces( WP_REST_Request $request ) {
		$response = rest_ensure_response(
			array(
				'reign_login_nonce'        => wp_create_nonce( 'reign-sign-form' ),
				'reign_friendship_nonce'   => wp_create_nonce( 'reign_friendship_nonce' ),
				'reign_notification_nonce' => wp_create_nonce( 'reign_notification_nonce' ),
				'reign_message_nonce'      => wp_create_nonce( 'reign_message_nonce' ),
				'rest_nonce'               => wp_create_nonce( 'wp_rest' ),
			)
		);

		// Strict no-cache: this is per-user, time-bound data.
		$response->header( 'Cache-Control', 'no-store, private' );

		return $response;
	}
}

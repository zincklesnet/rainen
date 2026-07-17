<?php
/**
 * Reign Preferences REST Controller.
 *
 * Persists per-user theme preferences. Currently exposes the color-mode
 * (light/dark/auto) chosen by the visitor — previously stored only in
 * localStorage, which meant a logged-in user who set "dark" on their
 * phone saw the default (light) when they opened the same account on
 * their laptop. This controller syncs the choice to user_meta.
 *
 *   GET  /wp-json/reign/v1/preferences/color-mode  (logged-in only)
 *   POST /wp-json/reign/v1/preferences/color-mode  (logged-in only)
 *
 * Anonymous visitors continue to use localStorage as before — same
 * client-side fallback when the user has no logged-in session.
 *
 * Standalone WP compatible — no BP / WC dependency.
 *
 * @package reign
 * @since 8.0.0
 */

namespace Reign\REST;

defined( 'ABSPATH' ) || exit;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Preferences_Controller {

	const REST_NAMESPACE = Component::REST_NAMESPACE;
	const COLOR_MODE_META_KEY = 'reign_color_mode_pref';

	public function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			'/preferences/color-mode',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_color_mode' ),
					'permission_callback' => array( $this, 'permission_check' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'set_color_mode' ),
					'permission_callback' => array( $this, 'permission_check' ),
					'args'                => array(
						'mode' => array(
							'description' => __( 'Color mode preference.', 'reign' ),
							'type'        => 'string',
							'enum'        => array( 'light', 'dark', 'auto' ),
							'required'    => true,
						),
					),
				),
			)
		);
	}

	public function permission_check(): bool {
		return is_user_logged_in();
	}

	/**
	 * GET /reign/v1/preferences/color-mode
	 */
	public function get_color_mode( WP_REST_Request $request ) {
		$user_id = get_current_user_id();
		$mode    = (string) get_user_meta( $user_id, self::COLOR_MODE_META_KEY, true );

		if ( '' === $mode ) {
			// No personal preference saved — fall back to the site default
			// configured in the customizer.
			$mode = (string) get_theme_mod( 'site_color_mode', 'light' );
		}

		$response = rest_ensure_response(
			array(
				'mode' => $mode,
			)
		);
		// Per-user, but stable until the user changes it. 5 minute lookaside
		// cache is safe for any client that uses ETag / 304 properly.
		$response->header( 'Cache-Control', 'private, max-age=300' );
		return $response;
	}

	/**
	 * POST /reign/v1/preferences/color-mode
	 */
	public function set_color_mode( WP_REST_Request $request ) {
		$mode = (string) $request->get_param( 'mode' );

		if ( ! in_array( $mode, array( 'light', 'dark', 'auto' ), true ) ) {
			return new WP_Error(
				'reign_invalid_color_mode',
				__( 'Color mode must be one of: light, dark, auto.', 'reign' ),
				array( 'status' => 400 )
			);
		}

		update_user_meta( get_current_user_id(), self::COLOR_MODE_META_KEY, $mode );

		return rest_ensure_response(
			array(
				'success' => true,
				'mode'    => $mode,
			)
		);
	}
}

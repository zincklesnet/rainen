<?php
/**
 * Reign Info REST Controller.
 *
 * Single read-only endpoint returning theme info + feature flags. Handy
 * for plugin / integration code that needs to detect Reign version,
 * active style variation, dark-mode availability, etc. without parsing
 * the theme's stylesheet header or duplicating customizer reads.
 *
 *   GET /wp-json/reign/v1/info
 *
 * Public endpoint (no auth required). Cacheable for everyone (no
 * per-user data leaked), so we set a 5-minute Cache-Control max-age
 * that any edge cache can honour.
 *
 * Standalone WP compatible — values come from theme.json / style.css
 * header / customizer state, never from BP / WC plugin APIs.
 *
 * @package reign
 * @since 8.0.0
 */

namespace Reign\REST;

defined( 'ABSPATH' ) || exit;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Info_Controller {

	const REST_NAMESPACE = Component::REST_NAMESPACE;

	public function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			'/info',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_info' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * GET /reign/v1/info
	 */
	public function get_info( WP_REST_Request $request ) {
		$theme = wp_get_theme();

		$response = rest_ensure_response(
			array(
				'name'        => (string) $theme->get( 'Name' ),
				'version'     => (string) $theme->get( 'Version' ),
				'text_domain' => (string) $theme->get( 'TextDomain' ),
				'features'    => array(
					'color_mode_toggle'   => reign_is_truthy( get_theme_mod( 'site_color_mode_toggle_show', true ) ),
					'active_variation'    => (string) get_theme_mod( 'site_style_variation', '' ),
					'default_color_mode'  => (string) get_theme_mod( 'site_color_mode', 'light' ),
					'dark_mode_available' => true,
					'rest_namespace'      => Component::REST_NAMESPACE,
				),
				'integrations' => array(
					'buddypress' => function_exists( 'buddypress' ),
					'woocommerce' => class_exists( 'WooCommerce' ),
					'bbpress'    => class_exists( 'bbPress' ),
					'learndash'  => class_exists( 'SFWD_LMS' ),
					'easy_digital_downloads' => class_exists( 'Easy_Digital_Downloads' ),
				),
			)
		);

		// Public, edge-cacheable. Theme version doesn't change often.
		$response->header( 'Cache-Control', 'public, max-age=300' );
		return $response;
	}
}

<?php
/**
 * Reign REST API — boot + controller coordinator.
 *
 * Public surface: `/wp-json/reign/v1/*` routes.
 *
 * All endpoints are designed to work on STANDALONE WordPress sites — no
 * BuddyPress, BBPress, WooCommerce dependency. Plugin-specific features
 * remain in their own integration files; this REST surface covers only
 * what Reign itself owns: auth, theme preferences, per-user nonces,
 * theme info.
 *
 * @package reign
 * @since 8.0.0
 */

namespace Reign\REST;

defined( 'ABSPATH' ) || exit;

/**
 * Component
 *
 * Registers every controller in the Reign REST namespace and forwards
 * the rest_api_init hook to each. Adding a new endpoint is a matter
 * of writing the controller, dropping it under inc/REST/, and adding
 * its class name to the $controllers list in boot().
 */
class Component {

	/**
	 * REST namespace shared by every Reign endpoint.
	 *
	 * @var string
	 */
	const REST_NAMESPACE = 'reign/v1';

	/**
	 * Boot the REST surface.
	 *
	 * Wires the `rest_api_init` action to each controller's
	 * `register_routes()` method. Called from
	 * customizer-framework-bootstrap.php.
	 */
	public static function boot(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Instantiate every controller and register its routes.
	 */
	public static function register_routes(): void {
		$controllers = array(
			Auth_Controller::class,
			Nonces_Controller::class,
			Preferences_Controller::class,
			Info_Controller::class,
		);

		foreach ( $controllers as $class ) {
			if ( ! class_exists( $class ) ) {
				continue;
			}
			$controller = new $class();
			if ( method_exists( $controller, 'register_routes' ) ) {
				$controller->register_routes();
			}
		}
	}
}

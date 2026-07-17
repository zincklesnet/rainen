<?php
/**
 * Reign Auth REST Controller.
 *
 * Two public endpoints — both fill genuine gaps in WP core's REST API
 * and both are standalone-WordPress compatible (no BuddyPress / WC
 * dependency):
 *
 *   POST /wp-json/reign/v1/auth/signup       (self-registration)
 *   POST /wp-json/reign/v1/auth/magic-link   (passwordless sign-in email)
 *
 * Why these two specifically:
 *
 *   - WP core has POST /wp/v2/users but it requires `create_users`
 *     capability — admin-only. Public self-registration via REST is
 *     not provided by core.
 *
 *   - WP core has no REST equivalent of `retrieve_password()` / magic-
 *     link / passwordless sign-in.
 *
 * What this controller intentionally does NOT provide:
 *
 *   /auth/signin (username + password login) was removed because:
 *     1. WP core's canonical login flow is `wp-login.php` (form POST,
 *        not REST). The existing Reign admin-ajax handler already
 *        wraps wp_signon() with the same behaviour.
 *     2. Security plugins (iThemes Security, Wordfence, Loginizer)
 *        hook into the wp-login.php request lifecycle to insert 2FA
 *        challenges and login-attempt monitoring. A custom REST
 *        endpoint that bypasses wp-login.php silently bypasses those
 *        protections — net security regression.
 *     3. Reinventing login over REST provides no functional gain over
 *        the existing AJAX flow that's already battle-tested.
 *
 *   Theme JS should continue to use the existing wp-login.php /
 *   admin-ajax signin flow. The new REST surface focuses on what's
 *   actually missing from core.
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

class Auth_Controller {

	const REST_NAMESPACE = Component::REST_NAMESPACE;

	/**
	 * Register the auth routes (signup + magic-link).
	 *
	 * Note: there is intentionally no /auth/signin route — see the
	 * file-level docblock above for the rationale. Login uses
	 * wp-login.php / the existing admin-ajax flow.
	 */
	public function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			'/auth/signup',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'signup' ),
					'permission_callback' => array( $this, 'signup_permission_check' ),
					'args'                => array(
						'user_login' => array(
							'description' => __( 'Desired username.', 'reign' ),
							'type'        => 'string',
							'required'    => true,
						),
						'user_email' => array(
							'description' => __( 'Email address.', 'reign' ),
							'type'        => 'string',
							'format'      => 'email',
							'required'    => true,
						),
						'user_pass'  => array(
							'description' => __( 'Password. Optional — when omitted, a random password is generated and emailed.', 'reign' ),
							'type'        => 'string',
						),
						'first_name' => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'last_name'  => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/auth/magic-link',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'magic_link' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'email' => array(
							'description'       => __( 'Email address OR username to send the magic sign-in link to. Param name is "email" for backwards compatibility; either form accepted.', 'reign' ),
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);
	}

	/**
	 * POST /reign/v1/auth/signup
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function signup( WP_REST_Request $request ) {
		$user_login = sanitize_user( $request->get_param( 'user_login' ), true );
		$user_email = sanitize_email( $request->get_param( 'user_email' ) );
		$user_pass  = $request->get_param( 'user_pass' );

		if ( empty( $user_login ) || empty( $user_email ) || ! is_email( $user_email ) ) {
			return new WP_Error(
				'reign_signup_invalid',
				__( 'A valid username and email are required.', 'reign' ),
				array( 'status' => 400 )
			);
		}

		if ( username_exists( $user_login ) ) {
			return new WP_Error(
				'reign_signup_username_taken',
				__( 'That username is already taken.', 'reign' ),
				array( 'status' => 409 )
			);
		}

		if ( email_exists( $user_email ) ) {
			return new WP_Error(
				'reign_signup_email_taken',
				__( 'That email is already registered.', 'reign' ),
				array( 'status' => 409 )
			);
		}

		// Use a random password when the caller didn't supply one — WP core
		// will email the credentials to the new user.
		$emit_password_email = false;
		if ( empty( $user_pass ) ) {
			$user_pass           = wp_generate_password( 16, true, true );
			$emit_password_email = true;
		}

		$user_id = wp_create_user( $user_login, $user_pass, $user_email );
		if ( is_wp_error( $user_id ) ) {
			return new WP_Error(
				'reign_signup_failed',
				$user_id->get_error_message(),
				array( 'status' => 500 )
			);
		}

		// Optional profile fields.
		$first_name = $request->get_param( 'first_name' );
		$last_name  = $request->get_param( 'last_name' );
		if ( $first_name || $last_name ) {
			wp_update_user(
				array(
					'ID'         => $user_id,
					'first_name' => (string) $first_name,
					'last_name'  => (string) $last_name,
				)
			);
		}

		if ( $emit_password_email ) {
			wp_new_user_notification( $user_id, null, 'user' );
		}

		/**
		 * Fires after a successful Reign REST signup so plugins can react
		 * (BuddyPress activity entry, Groundhogg contact creation, etc.).
		 *
		 * @param int             $user_id The new user's ID.
		 * @param WP_REST_Request $request The original REST request.
		 */
		do_action( 'reign_rest_after_signup', $user_id, $request );

		return rest_ensure_response(
			array(
				'success'    => true,
				'user_id'    => $user_id,
				'user_login' => $user_login,
				'message'    => $emit_password_email
					? __( 'Account created. Check your email for your password.', 'reign' )
					: __( 'Account created.', 'reign' ),
			)
		);
	}

	/**
	 * Permission gate for the signup endpoint.
	 *
	 * Honors the WordPress `users_can_register` option. The same gate the
	 * standard wp-login.php register form uses.
	 */
	public function signup_permission_check(): bool {
		return (bool) get_option( 'users_can_register' );
	}

	/**
	 * POST /reign/v1/auth/magic-link
	 *
	 * Sends a one-time sign-in email. Two strategies in priority order:
	 *
	 *   1. iThemes Security Passwordless Login — when the plugin is
	 *      active, delegate to `ITSEC_Passwordless_Login::send_magic_link`
	 *      so the customer-facing email body + signed URL match what
	 *      iThemes generates for the rest of their flows. Preserves the
	 *      pre-existing customer integration.
	 *
	 *   2. WP-native fallback — when no security plugin is present,
	 *      generate a password-reset key URL pointing at wp-login.php.
	 *      The link expires per WP's default password-reset key
	 *      lifetime (1 day) and ALLOWS sign-in via the reset form
	 *      (which sets a new password as part of the flow).
	 *
	 * Accepts either an email address OR a username (param name kept as
	 * `email` for backwards compatibility; if the value isn't an email,
	 * we treat it as a login slug — matches the AJAX handler's behaviour
	 * in inc/extras.php::reign_send_magic_link).
	 *
	 * @param WP_REST_Request $request The request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function magic_link( WP_REST_Request $request ) {
		$identifier = trim( (string) $request->get_param( 'email' ) );

		if ( '' === $identifier ) {
			return new WP_Error(
				'reign_magic_link_invalid',
				__( 'A username or email is required.', 'reign' ),
				array( 'status' => 400 )
			);
		}

		// Resolve username OR email -> user object.
		$user = is_email( $identifier )
			? get_user_by( 'email', sanitize_email( $identifier ) )
			: get_user_by( 'login', sanitize_user( $identifier ) );

		// Privacy-preserving: return the same success response whether
		// the user exists or not. Prevents email-enumeration.
		$generic_response = rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'If an account exists for that username or email, a sign-in link has been sent.', 'reign' ),
			)
		);

		if ( ! $user instanceof \WP_User ) {
			return $generic_response;
		}

		// (1) iThemes Security path — preserved verbatim from the
		//     legacy AJAX handler so customer sites running iThemes
		//     get the same branded magic-link email.
		if ( class_exists( 'ITSEC_Passwordless_Login' ) ) {
			try {
				$passwordless = \ITSEC_Passwordless_Login::get_instance();

				if ( method_exists( $passwordless, 'send_magic_link' ) ) {
					$result = $passwordless->send_magic_link( $user );
					if ( is_wp_error( $result ) ) {
						return new WP_Error(
							'reign_magic_link_failed',
							$result->get_error_message(),
							array( 'status' => 500 )
						);
					}
					return $generic_response;
				}
			} catch ( \Exception $e ) {
				// Fall through to the WP-native path.
			}
		}

		// (2) WP-native fallback — password-reset-style key URL.
		$key = get_password_reset_key( $user );
		if ( is_wp_error( $key ) ) {
			return $generic_response;
		}

		$login_url = add_query_arg(
			array(
				'action' => 'rp',
				'key'    => $key,
				'login'  => rawurlencode( $user->user_login ),
			),
			wp_login_url()
		);

		$subject = sprintf(
			/* translators: %s: site title */
			__( 'Sign in to %s', 'reign' ),
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
		);

		$message  = sprintf( __( 'Hi %s,', 'reign' ), $user->display_name ) . "\r\n\r\n";
		$message .= __( 'Click the link below to sign in. The link expires in 24 hours.', 'reign' ) . "\r\n\r\n";
		$message .= $login_url . "\r\n\r\n";
		$message .= __( 'If you did not request this sign-in link, you can safely ignore this email.', 'reign' ) . "\r\n";

		wp_mail( $user->user_email, $subject, $message );

		return $generic_response;
	}
}

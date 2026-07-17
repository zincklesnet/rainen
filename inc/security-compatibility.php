<?php
/**
 * Minimal Security Plugin Compatibility for Reign Login/Signup Forms
 *
 * ONLY essential fixes for compatibility - no extra features
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Whether the current AJAX request targets one of the Reign auth endpoints.
 *
 * Used inside security-plugin whitelist filters that fire BEFORE any nonce is
 * verified, so only the sanitized action name is read (read-only sniff); the
 * Reign AJAX handlers themselves verify their own nonces afterwards.
 *
 * @param string[]|null $actions Action names to match. Defaults to all Reign auth actions.
 * @return bool
 */
function reign_is_reign_auth_ajax_action( $actions = null ) {
	if ( ! wp_doing_ajax() || ! isset( $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only action-name sniff for security-plugin whitelisting; nonce is verified later by the AJAX handler itself.
		return false;
	}

	if ( null === $actions ) {
		$actions = array( 'reign-signin-form', 'reign-signup-form', 'reign-send-magic-link' );
	}

	$action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only action-name sniff for security-plugin whitelisting; nonce is verified later by the AJAX handler itself.

	return in_array( $action, $actions, true );
}

// 1. iThemes Security / Better WP Security
if ( defined( 'ITSEC_CORE' ) || class_exists( 'ITSEC_Core' ) ) {
	// Whitelist Reign AJAX from security filters
	add_filter(
		'itsec_filter_login_page_requests',
		function ( $filter, $uri ) {
			if ( reign_is_reign_auth_ajax_action() ) {
				return false;
			}
			return $filter;
		},
		10,
		2
	);

	// Bypass brute force for Reign forms
	add_filter(
		'itsec_brute_force_valid_credentials',
		function ( $valid, $username ) {
			if ( reign_is_reign_auth_ajax_action() ) {
				return true;
			}
			return $valid;
		},
		10,
		2
	);

	// Exclude from lockouts
	add_filter(
		'itsec_lockout_modules',
		function ( $modules ) {
			if ( reign_is_reign_auth_ajax_action() ) {
				unset( $modules['brute_force'] );
				unset( $modules['recaptcha'] );
			}
			return $modules;
		}
	);
}

// 2. Wordfence Security
if ( class_exists( 'wordfence' ) || defined( 'WORDFENCE_VERSION' ) ) {
	// Whitelist Reign forms from CAPTCHA
	add_filter(
		'wordfence_ls_require_captcha',
		function ( $required ) {
			if ( reign_is_reign_auth_ajax_action() ) {
				return false;
			}
			return $required;
		}
	);

	// Bypass rate limiting
	add_filter(
		'wordfence_rate_limit_check',
		function ( $block ) {
			if ( reign_is_reign_auth_ajax_action() ) {
				return false;
			}
			return $block;
		}
	);
}

// 3. All In One WP Security
if ( class_exists( 'AIO_WP_Security' ) || defined( 'AIO_WP_SECURITY_VERSION' ) ) {
	// Bypass CAPTCHA for Reign forms
	add_filter(
		'aiowps_captcha_form_check',
		function ( $check ) {
			if ( reign_is_reign_auth_ajax_action() ) {
				return false;
			}
			return $check;
		}
	);
}

// 4. Sucuri Security
if ( class_exists( 'SucuriScanInterface' ) || defined( 'SUCURISCAN_VERSION' ) ) {
	// Whitelist Reign AJAX endpoints
	add_filter(
		'sucuriscan_hardening_whitelist',
		function ( $whitelist ) {
			$whitelist[] = 'admin-ajax.php?action=reign-signin-form';
			$whitelist[] = 'admin-ajax.php?action=reign-signup-form';
			$whitelist[] = 'admin-ajax.php?action=reign-send-magic-link';
			return $whitelist;
		}
	);
}

// 5. Loginizer
if ( defined( 'LOGINIZER_VERSION' ) ) {
	// Bypass for Reign AJAX
	add_filter(
		'loginizer_bypass_check',
		function ( $bypass ) {
			if ( reign_is_reign_auth_ajax_action() ) {
				return true;
			}
			return $bypass;
		}
	);
}

// 6. Magic Link Support - Enqueue scripts and whitelist AJAX
if ( class_exists( 'ITSEC_Passwordless_Login' ) ||
	( class_exists( 'ITSEC_Modules' ) &&
		method_exists( 'ITSEC_Modules', 'is_active' ) &&
		ITSEC_Modules::is_active( 'passwordless-login' ) ) ) {

	// Enqueue magic link JavaScript only when needed
	add_action(
		'wp_enqueue_scripts',
		function () {
			// Double-check the plugin is still active and the file exists
			if ( ! class_exists( 'ITSEC_Passwordless_Login' ) &&
			! ( class_exists( 'ITSEC_Modules' ) &&
				method_exists( 'ITSEC_Modules', 'is_active' ) &&
				ITSEC_Modules::is_active( 'passwordless-login' ) ) ) {
				return;
			}

			$script_path = get_template_directory() . '/assets/js/reign-magic-link.min.js';
			if ( ! file_exists( $script_path ) ) {
				return;
			}

			wp_enqueue_script(
				'reign-magic-link',
				get_template_directory_uri() . '/assets/js/reign-magic-link.min.js',
				array( 'jquery', 'reign-api' ),
				REIGN_THEME_VERSION,
				true
			);

			wp_localize_script(
				'reign-magic-link',
				'reign_magic_link',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'i18n'     => array(
						'enter_username'       => __( 'Please enter your username or email address.', 'reign' ),
						'sending'              => __( 'Sending...', 'reign' ),
						'error'                => __( 'An error occurred. Please try again.', 'reign' ),
						'email_login_link'     => __( 'Email me a login link', 'reign' ),
						'use_password_instead' => __( 'Login with password instead', 'reign' ),
					),
				)
			);
		}
	);

	// Whitelist magic link AJAX from security filters
	add_filter(
		'itsec_filter_login_page_requests',
		function ( $filter, $uri ) {
			if ( reign_is_reign_auth_ajax_action( array( 'reign-send-magic-link' ) ) ) {
				return false;
			}
			return $filter;
		},
		10,
		2
	);
}

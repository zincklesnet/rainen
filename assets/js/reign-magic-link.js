/**
 * Reign Magic Link UI handler.
 *
 * Hooks the magic-link button click + the "use password instead" toggle
 * to the Reign REST API (window.reignApi.magicLink). The REST endpoint
 * detects iThemes Security Passwordless Login and delegates to its
 * email flow when the plugin is active, preserving the existing
 * customer-facing branded email. On standalone WP, it falls back to a
 * WordPress-native password-reset-style key URL.
 *
 * Migrated from jQuery $.ajax to window.reignApi in 8.0.0 (P10). The
 * legacy `wp_ajax_nopriv_reign-send-magic-link` handler in
 * inc/extras.php is NOT removed — any third-party JS that still
 * targets it continues to work.
 *
 * @package reign
 * @since   8.0.0
 */
jQuery( document ).ready( function ( $ ) {
	'use strict';

	// Handle magic link button click.
	$( document ).on( 'click', '[data-action="reign-send-magic-link"]', function ( e ) {
		e.preventDefault();

		var $button         = $( this );
		var $form           = $button.closest( '.reign-sign-form' );
		var $messages       = $form.find( '.reign-sign-form-messages' );
		var $usernameField  = $form.find( 'input[name="itsec_magic_link_username"]' );
		var username        = $usernameField.val();

		if ( ! username ) {
			$messages
				.removeClass( 'woocommerce-message' )
				.addClass( 'woocommerce-error' )
				.html( '<li>' + reign_magic_link.i18n.enter_username + '</li>' )
				.show();
			return;
		}

		// Clear previous messages.
		$messages.hide().empty();

		// Disable button + show loading state.
		$button.prop( 'disabled', true );
		var originalText = $button.text();
		$button.html( '<span class="icon-loader"></span> ' + reign_magic_link.i18n.sending );

		// Modern REST call. The `email` param accepts either an email
		// address or a username (the REST endpoint resolves either form
		// to a user object).
		window.reignApi.magicLink( { email: username } )
			.then( function ( res ) {
				if ( res.success ) {
					$messages
						.removeClass( 'woocommerce-error' )
						.addClass( 'woocommerce-message' )
						.html( '<li>' + res.message + '</li>' )
						.show();

					$usernameField.val( '' );

					setTimeout( function () {
						$messages.fadeOut();
					}, 10000 );
				} else {
					$messages
						.removeClass( 'woocommerce-message' )
						.addClass( 'woocommerce-error' )
						.html( '<li>' + ( res.message || reign_magic_link.i18n.error ) + '</li>' )
						.show();
				}
			} )
			.catch( function ( err ) {
				var msg = ( err && err.message ) ? err.message : reign_magic_link.i18n.error;
				$messages
					.removeClass( 'woocommerce-message' )
					.addClass( 'woocommerce-error' )
					.html( '<li>' + msg + '</li>' )
					.show();
			} )
			.finally( function () {
				$button.prop( 'disabled', false ).html( originalText );
			} );
	} );

	// Toggle between magic link and password login (UI only — no AJAX).
	$( document ).on( 'click', '.reign-toggle-login-method', function ( e ) {
		e.preventDefault();

		var $form            = $( this ).closest( '.reign-sign-form' );
		var $magicSection    = $form.find( '.reign-magic-link-section' );
		var $passwordSection = $form.find( '.reign-password-section' );
		var $allToggleLinks  = $form.find( '.reign-toggle-login-method' );

		if ( $magicSection.is( ':visible' ) ) {
			$magicSection.slideUp( 300 );
			$passwordSection.slideDown( 300 );
			$allToggleLinks.text( reign_magic_link.i18n.email_login_link );
		} else {
			$passwordSection.slideUp( 300 );
			$magicSection.slideDown( 300 );
			$allToggleLinks.text( reign_magic_link.i18n.use_password_instead );
		}
	} );
} );

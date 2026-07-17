/**
 * Reign sign-in/register popup - password show/hide eye.
 *
 * The Reign login form ships a themed Font Awesome eye (.password-eye), but the
 * BuddyPress-rendered register form only carries BuddyPress' own dashicons
 * .wp-hide-pw control on the main password field (and nothing on the confirm
 * field), so the show/hide icon was missing / inconsistent on the register
 * screen. This adds the same themed eye to every register password field and
 * hides the inconsistent native control.
 *
 * @package Reign
 */
( function ( $ ) {
	'use strict';

	/**
	 * Add a themed eye to each password input inside the register form.
	 *
	 * @param {Element|Document} scope Container to scan.
	 */
	function enhance( scope ) {
		// Give the BuddyPress-rendered register fields the same classes the Reign
		// login form uses, so they inherit identical input styling (border,
		// radius, padding) and visually match the login tab.
		$( scope )
			.find( '.reign-sign-form-register, #signup-form, #register-page' )
			.find(
				'input[type="text"], input[type="email"], input[type="url"], input[type="tel"], input[type="number"], input[type="password"], select, textarea'
			)
			.not( '[type="checkbox"], [type="radio"], [type="hidden"]' )
			.addClass( 'form-control simple-input' );

		$( scope )
			.find(
				'.reign-sign-form-register input[type="password"], #signup-form input[type="password"], #register-page input[type="password"]'
			)
			.each( function () {
				var $input = $( this );

				if ( $input.data( 'reignPwEye' ) ) {
					return;
				}
				$input.data( 'reignPwEye', true );

				// Hide BuddyPress' native dashicons toggle so we don't show two controls.
				$input.siblings( '.wp-hide-pw' ).attr( 'hidden', 'hidden' ).hide();

				// Wrap just the input so the eye can centre on it regardless of label markup.
				if ( ! $input.parent().hasClass( 'reign-pw-eye-wrap' ) ) {
					$input.wrap( '<span class="reign-pw-eye-wrap"></span>' );
				}

				var label = ( window.reignPwEye && window.reignPwEye.label ) || 'Toggle password visibility';
				$(
					'<button type="button" class="fa fa-fw fa-eye reign-pw-eye" aria-label="' +
						label +
						'"></button>'
				).insertAfter( $input );
			} );
	}

	$( function () {
		var popup = document.getElementById( 'registration-login-form-popup' ) || document;

		enhance( popup );

		// The popup markup is present at load, but tabs/forms can be re-rendered;
		// keep the eye in sync if the register panel changes.
		if ( popup && 1 === popup.nodeType && window.MutationObserver ) {
			new MutationObserver( function () {
				enhance( popup );
			} ).observe( popup, { childList: true, subtree: true } );
		}

		// Delegated toggle (dedicated class so it never collides with the login
		// form's own .password-eye handler in main.js).
		$( document ).on( 'click', '.reign-pw-eye', function ( e ) {
			e.preventDefault();
			var $btn = $( this );
			var $input = $btn.prev( 'input' );
			var show = 'password' === $input.attr( 'type' );

			$input.attr( 'type', show ? 'text' : 'password' );
			$btn.toggleClass( 'fa-eye-slash', show ).toggleClass( 'fa-eye', ! show );
		} );
	} );
} )( jQuery );

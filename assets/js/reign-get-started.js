/**
 * Reign - Getting Started one-click installer.
 *
 * Delegated handler for [.reign-gs-install] buttons. Posts the catalog slug to
 * admin-ajax (action=reign_install_plugin), then flips the button/card to its
 * installed state on success. Vanilla JS, no jQuery.
 */
( function () {
	'use strict';

	var cfg = window.ReignGetStarted || {};
	var i18n = cfg.i18n || {};

	function t( key, fallback ) {
		return i18n[ key ] || fallback;
	}

	/**
	 * Flip a family product card to its active state.
	 *
	 * @param {HTMLElement} button The install button inside the card.
	 */
	function markCardActive( button ) {
		var card = button.closest( '.reign-gs-product' );
		if ( ! card ) {
			button.replaceWith( doneBadge() );
			return;
		}
		card.classList.add( 'is-active' );

		var top = card.querySelector( '.reign-gs-product-top' );
		if ( top && ! top.querySelector( '.reign-gs-pill--active' ) ) {
			var pill = document.createElement( 'span' );
			pill.className = 'reign-gs-pill reign-gs-pill--active';
			pill.textContent = t( 'active', 'Active' );
			top.appendChild( pill );
		}
		button.remove();
	}

	/**
	 * The "installed" badge that replaces the hero button.
	 *
	 * @return {HTMLElement}
	 */
	function doneBadge() {
		var span = document.createElement( 'span' );
		span.className = 'reign-gs-btn reign-gs-btn--done';
		span.textContent = t( 'essentialInstalled', 'Wbcom Essential installed' );
		return span;
	}

	/**
	 * Surface an inline error next to the button, then restore it.
	 *
	 * @param {HTMLElement} button  The install button.
	 * @param {string}      message The error message.
	 * @param {string}      label   The original button label.
	 */
	function showError( button, message, label ) {
		button.disabled = false;
		button.classList.remove( 'is-loading' );
		button.textContent = label;

		var holder = button.parentNode;
		if ( ! holder ) {
			return;
		}
		var note = holder.querySelector( '.reign-gs-install-error' );
		if ( ! note ) {
			note = document.createElement( 'p' );
			note.className = 'reign-gs-install-error';
			button.insertAdjacentElement( 'afterend', note );
		}
		note.textContent = message || t( 'failed', 'Install failed. Please try again.' );
	}

	function install( button ) {
		var slug = button.getAttribute( 'data-slug' );
		if ( ! slug || button.disabled ) {
			return;
		}

		var label = button.textContent;
		var isHero = button.classList.contains( 'reign-gs-btn' );

		button.disabled = true;
		button.classList.add( 'is-loading' );
		button.textContent = t( 'installing', 'Installing…' );

		var body = new URLSearchParams();
		body.append( 'action', 'reign_install_plugin' );
		body.append( 'slug', slug );
		body.append( 'nonce', cfg.nonce || '' );

		fetch( cfg.ajaxurl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString(),
		} )
			.then( function ( res ) {
				return res.json();
			} )
			.then( function ( json ) {
				if ( json && json.success ) {
					if ( isHero ) {
						button.replaceWith( doneBadge() );
					} else {
						markCardActive( button );
					}
				} else {
					var msg = json && json.data && json.data.message ? json.data.message : '';
					showError( button, msg, label );
				}
			} )
			.catch( function () {
				showError( button, t( 'failed', 'Install failed. Please try again.' ), label );
			} );
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.reign-gs-install' );
		if ( ! button ) {
			return;
		}
		event.preventDefault();
		install( button );
	} );
} )();

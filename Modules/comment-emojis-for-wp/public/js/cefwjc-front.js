( function( $ ) {
	'use strict';

	function buildEmojiOptions() {
		return {
			pickerPosition: emojisData.position_emojis,
			filtersPosition: emojisData.filter_position,
			tones: 'yes' !== emojisData.skintone,
			tonesStyle: emojisData.skintone_style,
			search: 'yes' !== emojisData.search,
			searchPosition: emojisData.search_position,
			recentEmojis: 'yes' !== emojisData.recent_emojis
		};
	}

	function syncThemeAppearance( $textarea ) {
		var instance = $textarea.data( 'emojioneArea' );
		var computedStyle;
		var backgroundColor;
		var wrapper;
		var minHeight;

		if ( ! instance || ! instance.editor ) {
			return;
		}

		computedStyle = window.getComputedStyle( $textarea[ 0 ] );
		backgroundColor = computedStyle.backgroundColor;
		wrapper = instance.editor.closest( '.emojionearea' );
		minHeight = Math.max( $textarea.outerHeight() || 0, 120 );

		wrapper.css( {
			'--cefwjc-theme-radius': computedStyle.borderRadius || '14px',
			'--cefwjc-theme-border': computedStyle.borderColor || '#cbd5e1',
			'--cefwjc-theme-focus': computedStyle.borderColor || '#2271b1',
			'--cefwjc-theme-background': ( backgroundColor && 'rgba(0, 0, 0, 0)' !== backgroundColor ) ? backgroundColor : '#ffffff',
			'--cefwjc-theme-color': computedStyle.color || '#1f2937',
			'--cefwjc-theme-font-family': computedStyle.fontFamily || 'inherit',
			'--cefwjc-theme-font-size': computedStyle.fontSize || '16px',
			'--cefwjc-theme-line-height': computedStyle.lineHeight || '1.6',
			'--cefwjc-theme-min-height': minHeight + 'px'
		} );

		wrapper.addClass( 'cefwjc-theme-sync' );
	}

	function initEmojiPicker() {
		var selector;
		var emojiOptions;

		if ( 'undefined' === typeof emojisData || 'function' !== typeof $.fn.emojioneArea ) {
			return;
		}

		selector = emojisData.selector || '.comment-form-comment textarea, textarea#comment';
		emojiOptions = buildEmojiOptions();

		$( selector ).each( function() {
			var $textarea = $( this );

			if ( $textarea.data( 'cefwjcEmojiReady' ) || $textarea.closest( '.emojionearea' ).length ) {
				syncThemeAppearance( $textarea );
				return;
			}

			$textarea.data( 'cefwjcEmojiReady', true );
			$textarea.emojioneArea( emojiOptions );
			syncThemeAppearance( $textarea );
		} );
	}

	$( function() {
		initEmojiPicker();

		$( document ).on( 'focus', emojisData.selector || '.comment-form-comment textarea, textarea#comment', function() {
			initEmojiPicker();
		} );

		if ( 'function' === typeof window.MutationObserver ) {
			new window.MutationObserver( function() {
				initEmojiPicker();
			} ).observe( document.body, {
				childList: true,
				subtree: true
			} );
		}
	} );
}( jQuery ) );
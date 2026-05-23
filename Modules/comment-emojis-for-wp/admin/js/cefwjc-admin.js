( function( $ ) {
	'use strict';

	function toggleRow( checkboxSelector, rowSelector ) {
		$( rowSelector ).toggle( ! $( checkboxSelector ).is( ':checked' ) );
	}

	$( function() {
		$( '#cefwjc_skintone' ).on( 'change', function() {
			toggleRow( '#cefwjc_skintone', '#skintone_hide' );
		} );

		$( '#cefwjc_search' ).on( 'change', function() {
			toggleRow( '#cefwjc_search', '#search_hide' );
		} );

		toggleRow( '#cefwjc_skintone', '#skintone_hide' );
		toggleRow( '#cefwjc_search', '#search_hide' );
	} );
}( jQuery ) );
jQuery(function($){
	// define the bulk edit table row
	var $edit_row = $( 'tr#bulk-edit' );
	$('[name="_status"]',$edit_row).change(function(e) {
		var newStatus = $('[name="_status"]').val();
		e.stopPropagation();
		if ( newStatus === 'group_post' || newStatus === 'group_post_pending' ) {
			$('.bpps-groups-dropdown-inline', $edit_row).css('display', 'block');
			$('.bpps-groups-visibility', $edit_row).css('display', 'block');
		} else {
			$('.bpps-groups-dropdown-inline', $edit_row).css('display', 'none');
			$('.bpps-groups-visibility', $edit_row).css('display', 'none');
		}
		
	}); 
	$( 'body' ).on( 'click', 'input[name="bulk_edit"]', function() {
		var Status = $('[name="_status"]').val();
		if ( Status !== 'group_post' && Status !== 'group_post_pending' ) {
			return;
		}
		
		// let's add the WordPress default spinner just before the button
		$( this ).after('<span class="spinner is-active"></span>');
 
 
		// define: group id, group post visibility
		var post_ids = new Array();
		var groupID = document.getElementById( 'bpps-groups-dropdown' ).value;
		var groupVisibility = document.getElementById( 'bpps-groups-status-dropdown' ).value;
 
		// now we have to obtain the post IDs selected for bulk edit
		$edit_row.find( '#bulk-titles' ).children().each( function() {
			post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});
 
		// save the data with AJAX
		$.ajax({
			url: ajaxurl, // WordPress has already defined the AJAX url for us (at least in admin area)
			type: 'POST',
			data: {
				action: 'bpps_bulk_group_save', // wp_ajax action hook
				post_ids: post_ids, // array of post IDs
				groupID: groupID, // group id
				groupVisibility: groupVisibility, // group post visibility
				security: $('#bpps_bulk_edit_nonce').val() // I take the nonce from hidden #bpps_post_status_nonce field
			}
		});
	});
});
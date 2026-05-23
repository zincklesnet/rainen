

(function( $ ) {
	if ( typeof inlineEditPost === 'undefined' ) {
		return;
	}
	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;
	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {
 
		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );
 
		// now we take care of our business
 
		// get the post ID
		var $post_id = 0;
		var t = this, fields, editRow, rowData, status;
		
		if ( typeof( id ) === 'object' ) {
			$post_id = parseInt( this.getId( id ) );
			id = t.getId(id);
		}
		
		rowData = $('#inline_'+id);
		$post_status = $('._status', rowData).text();
		var $edit_row = $( '#edit-' + $post_id );
		var $post_row = $( '#post-' + $post_id );
		var $notify_check = {};
		
		$('[name="_status"]',$edit_row).change(function(e) {
			var newStatus = $('[name="_status"]', $edit_row).val();
			e.stopPropagation();
			if ( newStatus === 'group_post' || newStatus === 'group_post_pending' ) {
				$('.bpps-groups-dropdown-inline', $edit_row).css('display', 'block');
				$('.bpps-groups-visibility', $edit_row).css('display', 'block');
			} else {
				$('.bpps-groups-dropdown-inline', $edit_row).css('display', 'none');
				$('.bpps-groups-visibility', $edit_row).css('display', 'none');
			}
			
		}); 
		
		if ( $post_id > 0 && ( $post_status === 'group_post' || $post_status === 'group_post_pending' ) ) {
			
			$.ajax({
				url : ajax_object.ajaxurl,
				type : 'POST',
				dataType: 'html',
				data : {
					post_id : $post_id,
					security : ajax_object.check_nonce,
					action : 'bpps_selected_group'
				},
				success : function(data) {
					data=JSON.parse(data);
					$('#bpps-groups-dropdown option[value='+data[0]+']').attr('selected','selected');
					//$('#bpps-groups-dropdown', $edit_row).val(data[0]);
					$('#bpps-groups-status-dropdown', $edit_row).val(data[1]);
				},
				error : function(data){
					console.log(data);
					console.log('failed');
				}
			});
			$('.bpps-groups-dropdown-inline', $edit_row).css('display', 'block');
			$('.bpps-groups-visibility', $edit_row).css('display', 'block');
		}
		
		function statusChange (e) {
			console.log( $status_dropdown.value );
		}
	};				

	

})(jQuery);

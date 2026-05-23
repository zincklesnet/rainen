(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	$(document).ready(function($){		
		
		$( document ).on( 'click', '.buddypress_edit_activity, .bb_edit_activity', function( event ) {
			event.preventDefault();
			let activity_id 	= $( this ).data( 'activity_id' );
			let $form_wrapper 	= $('#bp-edit-activity-wrapper');
			
			var data = {
				'action': 'buddypress_edit_activity_get',
				'ajax_nonce': bp_edit_activity.ajax_nonce,
				'activity_id': activity_id,
			};
			
			
			$.ajax({
				type: "POST",
				url: bp_edit_activity.ajax_url,
				data: data,
				success: function(response) {					
					if (response.success) {						
						
						$form_wrapper.find('input[name="activity_id"]').val(data.activity_id);
						$form_wrapper.find('textarea').val(response.data.content);
						$form_wrapper.find('#bp-edit-additional-activity-content').html(response.data.bp_get_additional_content);
						$form_wrapper.show();
					} else {
						$('body').append(response.data.content);
						
						setTimeout(() => {
							$('.bp-edit-activity-error').remove();
						}, 5000);
					}
				},
			});
			
		});
		
		
		$( document).on( 'click', '#bp-edit-activity-wrapper .bp-model-close-button', function( e ) {
			e.preventDefault();
			$( this ).closest( '#bp-edit-activity-wrapper' ).hide();
		});
		
		$(document).on('click','.bp-activity-edit-model-wrap', function(event) {			
			if ($(event.target).hasClass( 'bp-activity-edit-model-wrap' )) {
				$('#bp-edit-activity-wrapper .bp-model-close-button').trigger('click');
			}
		});
		
		$(document).on("keydown", function(event) {			
			if (event.keyCode === 27) { // Check if 'Escape' key is pressed			
				$('#bp-edit-activity-wrapper .bp-model-close-button').trigger('click');				
			}
		});
		$( document ).on( 'keyup', '#frm-bp-edit-activity #whats-new', function( event ) {			
			if( $(this).val() == '' ) {
				$('input[name="update_activity"]').attr('disabled','disabled');				
			}else {
				$('input[name="update_activity"]').removeAttr('disabled');				
			}
		});
		$( document ).on( 'submit', '#frm-bp-edit-activity', function( event ) {
			event.preventDefault();
			
			let activity_id 	= $( this ).data( 'activity_id' );
			let $form_wrapper 	= $('#bp-edit-activity-wrapper');
			var formData 		= $(this).serialize();
			if( $('#frm-bp-edit-activity textarea[name="activity_content"]').val() == '' ) {
				$('input[name="update_activity"]').attr('disabled','disabled');
				return false;
			}
			var data = {
						'action': 'buddypress_edit_activity_save',
						'ajax_nonce': bp_edit_activity.ajax_nonce,
						'activity_id': $('#bp_edit_activity_id').val(),						
					};
			formData.split('&').forEach(function (pair) {
				var [key, value] = pair.split('=');
				key 	= decodeURIComponent(key);
				value 	= decodeURIComponent(value || '');

				// Check if key already exists (for array-like keys)
				if (key.endsWith('[]')) {
					// Normalize the key and append to array
					key = key.replace('[]', '');
					data[key] = data[key] || [];
					data[key].push(value);
				} else {
					data[key] = value;
				}
			});			
			$.ajax({
					type: "POST",
					url: ajaxurl,
					data: data,
					success: function(response) {						
						if (response.success) {
							let content = response.data.content;
							$form_wrapper.find('#bp-edit-additional-activity-content').append(content);
							$('ul.activity-list li#activity-' + data.activity_id + ' .activity-header' ).html( $('#bp-edit-additional-activity-content li#activity-' + data.activity_id + ' .activity-header').html());
							$('ul.activity-list li#activity-' + data.activity_id + ' .activity-inner' ).html( $('#bp-edit-additional-activity-content li#activity-' + data.activity_id + ' .activity-inner').html());
							$form_wrapper.hide();
							$form_wrapper.find('#bp-edit-additional-activity-content').html('');
							
							$('body').append(response.data.message);
							
							setTimeout(() => {
								$('.bp-edit-activity-success').remove();								
							}, 5000);
						} else {
							$('body').append(response.data.content);
							
							setTimeout(() => {
								$('.bp-edit-activity-error').remove();
							}, 5000);
						}
					},
				});
		});
		
	});

})( jQuery );

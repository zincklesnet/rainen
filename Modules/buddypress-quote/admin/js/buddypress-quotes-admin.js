(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
	 jQuery(document).ready(function($){
	 	$('#bpquotes-img-upload-btn').click(function(e) {
	 		e.preventDefault();
	 		var image = wp.media({ 
	 			title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: true
        }).open()
	 		.on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection');
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            uploaded_image.map( function( attachment ) {
            	attachment = attachment.toJSON();
            	$(".bpquotes-background-images").append("<div class='bpquotes-single-img'><a href='javascript:void(0)'' class='bpquotes-remove-img'><i class='fa fa-trash' aria-hidden='true'></i></a><img src=" +attachment.url+"><input type='hidden' name='bpquotes_gnrl_settings[image_url][]' class='regular-text bpquotes-hidden-input' value="+attachment.url+"></div>");
            });
        });
	 	});
		var myOptions = {
			change: function(event, ui){
				var color_id = $(this).attr('id');				
				var color_code = ui.color.toString();			
				if ( color_id === 'bpquotes-bg-color'){
					 console.log( color_code );
					 var original = dp_quote_convertHex(color_code), 
					channels = original.match(/\d+/g) /* [red, green, blue] */, 
					inverted_channels = channels.map(function(ch) {
						return 255 - ch;
					}) /* [255 - red, 255 - green, 255 - blue] */, 
					//inverted = 'rgb(' + inverted_channels.join(', ') + ')';
					inverted = dp_quote_rgbToHex(inverted_channels[0], inverted_channels[1], inverted_channels[2]) ;
					
					 $( '#bpquotes-inverted-color' ).val(inverted)
					 $( '#bpquotes-inverted-color' ).parent().parent().parent().find('.button.wp-color-result').css('background-color', inverted);					 
				}
				
				
			}
	    };
	 	$('.bpquotes-color-field').wpColorPicker(myOptions);

	 	jQuery(document).on('click','.bpquotes-add-bgcolor', function(){
	 		if( $('.bpquotes-color-field').val() != '' ){
	 			var color = $('#bpquotes-bg-color').val();
				var inverted = $('#bpquotes-inverted-color').val();
				/*
				var original = dp_quote_convertHex(color), 
					channels = original.match(/\d+/g) , 
					inverted_channels = channels.map(function(ch) {
						return 255 - ch;
					}) 					
					inverted = dp_quote_rgbToHex(inverted_channels[0], inverted_channels[1], inverted_channels[2]) ;
				*/
					
	 			$(".bpquotes-background-colors").append("<div class='bpquotes-single-color' style='background-color:"+color+"'><a href='javascript:void(0)'' class='bpquotes-remove-color'><i class='fa fa-trash' aria-hidden='true'></i></a><input type='hidden' name='bpquotes_gnrl_settings[bg_colors][]' class='regular-text bpquotes-hidden-input' value='"+color+"'><input type='hidden' name='bpquotes_gnrl_settings[bg_inverted_colors][]' class='regular-text bpquotes-hidden-input' value='"+inverted+"'></div>");
	 			$('.wp-picker-clear').trigger('click');
	 		}
	 	});

	 	$('.bpquotes-background-images').sortable();
	 	$('.bpquotes-background-images').disableSelection();
	 	$('.bpquotes-background-colors').sortable();
	 	$('.bpquotes-background-colors').disableSelection();

	 	$(document).on('click', '.bpquotes-remove-img', function(){
	 		$(this).addClass('loading');
	 		$(this).parent().remove();
	 	});

	 	$(document).on('click', '.bpquotes-remove-color', function(){
	 		$(this).addClass('loading');
	 		$(this).parent().remove();
	 	});
	 });
})( jQuery );

function dp_quote_convertHex(hex,opacity){
    hex = hex.replace('#','');
    r = parseInt(hex.substring(0,2), 16);
    g = parseInt(hex.substring(2,4), 16);
    b = parseInt(hex.substring(4,6), 16);

    result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
    return result;
}

function dp_quote_componentToHex(c) {
  let hex = c.toString(16);
  return hex.length == 1 ? "0" + hex : hex;
}
function dp_quote_rgbToHex(r, g, b) {
  return "#" + dp_quote_componentToHex(r) + dp_quote_componentToHex(g) + dp_quote_componentToHex(b);
}
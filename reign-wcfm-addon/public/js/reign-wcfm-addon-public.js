(function ($) {
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

	jQuery(document).on('click', '#rwcfma-product-mark-favuorite', function (e) {
		e.preventDefault();
		var btn = jQuery(this);
		var btnSrc = btn.attr('src');
		var iconFile = btnSrc.replace(/^.*[\\\/]/, '');
		var pid = jQuery(this).data('pid');
		var action = jQuery(this).data('action');
		var data = {
			'action': 'product_mark_favuorite',
			'product_id': pid,
			'product-action': action,
			'nonce': reign_wcfm_addon_js_params.nonce,
		};
		jQuery.ajax({
			dataType: "JSON",
			url: reign_wcfm_addon_js_params.ajax_url,
			type: 'POST',
			data: data,
			success: function (response) {
				var btn_action = response['data']['btn-action'];
				if ('remove-favuorite' == btn_action) {
					btn.attr('src', function (i, e) {
						return btn.attr('src').replace(iconFile, 'heart-fill.svg');
					});
					btn.data('action', btn_action);
					btn.addClass("animate");
				} else {
					btn.attr('src', function (i, e) {
						return btn.attr('src').replace(iconFile, 'heart.svg');
					});
					btn.data('action', btn_action);
					btn.removeClass("animate");
					$('.buddypress .post-' + pid + '').remove();
				}
			},
		});


	});
  
	$(document).on('ready', function () {
		setTimeout(function () {
			var obj_rtl;
			if ($('body').hasClass("rtl")) {
				obj_rtl = true;
			} else {
				obj_rtl = false;
			}
			
			$('.rwcfm-slider-wrapper').slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				nextArrow: '<button class="slick-next slick-arrow"><i class="far fa-angle-right"></i></button>',
				prevArrow: '<button class="slick-prev slick-arrow"><i class="far fa-angle-left"></i></button>',
				rtl: obj_rtl,
			});
		}, 1500);
	});

})(jQuery);

/**
 * Author: WP Zone
 * License: GNU General Public License version 3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

function hm_replace_image() {
	
	frame = wp.media({
		title: "Choose Replacement Image",
		button: {
			text: "Replace Image"
		},
		multiple: false
	});
	
	frame.on("select", function() {
		
		jQuery("#hm_replace_image_with_fld").val(frame.state().get("selection").first().toJSON().id);
		if (jQuery("#hm_replace_image_with_fld").closest('.media-modal').length) {
			jQuery("#hm_replace_image_with_fld").change();
			var saveStatusInterval = setInterval(function() {
				if (jQuery("#hm_replace_image_with_fld").closest('.attachment-details.save-ready').length) {
					clearInterval(saveStatusInterval);
					location.reload();
				}
			}, 250);
		} else {
			jQuery("#hm_replace_image_with_fld").closest("form").submit();
		}
		
	});
	
	var frameEl = jQuery(frame.open().el);
	frameEl.find('.media-router > a:first-child').click();
	
}
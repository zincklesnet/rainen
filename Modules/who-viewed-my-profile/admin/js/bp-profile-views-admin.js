if (typeof wp !== 'undefined' && wp.i18n) {
	const { __ } = wp.i18n;
}

(function ($) {
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
	jQuery(document).ready(
		function ($) {
			var acc = document.getElementsByClassName("wbcom-faq-accordion");
			var i;
			for (i = 0; i < acc.length; i++) {
				acc[i].onclick = function () {
					this.classList.toggle("active");
					var panel = this.nextElementSibling;
					if (panel.style.maxHeight) {
						panel.style.maxHeight = null;
					} else {
						panel.style.maxHeight = panel.scrollHeight + "px";
					}
				}
			}
		});

	

})(jQuery);


const memberViewsTable = () => {	
	// Grid Options are properties passed to the grid
	const gridOptions = {

		// each entry here represents one column
		columnDefs: [
			{ field: "name" },
			{ field: "weekly" },
			{ field: "monthly" },
			{ field: "yearly" },
		],

		// default col def properties get applied to all columns
		defaultColDef: { 
			sortable: true, 
			filter: true,
			resizable: true,
		 },

		rowSelection: 'multiple', // allow rows to be selected
		animateRows: true, // have rows animate to new positions when sorted
		pagination: true,
		paginationPageSize: 10
	};

	// get div to host the grid
	const eGridDiv = document.getElementById("bp-member-view-table");
	// new grid instance, passing in the hosting DIV and Grid Options
	if( agGrid != undefined ){
		new agGrid.Grid(eGridDiv, gridOptions);
	}


	// Fetch data from server
	fetch(ajaxurl, {
		method: "post",
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: 'action=get_views&security=' + bpmv.nonce  
	}).then(response => response.json())
		.then(data => {
			// load fetched data into grid
			gridOptions.api.setRowData(data);
		});


}


document.addEventListener("DOMContentLoaded", () => {
	memberViewsTable();
});



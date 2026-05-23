

jQuery(document).ready(function () {

	jQuery('#event-date').datepicker({
		//dateFormat: 'mm/dd/yy'
		//dateFormat: 'DD, MM d, yy',
		dateFormat: ppseScriptVars.dateformat,
		firstDay: 0
	});

	jQuery('#event-time').timepicker({ 'timeFormat': 'g:i a' });

});


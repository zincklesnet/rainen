/*
* @package bp-post-status
*/

//

// Setup variables for button images
jQuery(document).ready(function($){
	
	function selectHome(e){

		var clicked = e.target;
		var postID = clicked.value;
		var label = document.getElementById("home-select-label-" + postID);

		if ( label.innerHTML = bpps_translate.homeSelect ) {
			label.innerHTML = bpps_translate.update;
			$.ajax({
				url : ajax_object.ajaxurl,
				type : 'post',
				data : {
					post : postID,
					security : ajax_object.check_nonce,
					action : "bpps_home_page"
				},
				success : function(data) {
					if ( data == 'Success' ) {
						label.innerHTML = bpps_translate.homeSelected;
					} else {
						label.innerHTML = bpps_translate.error;
					}
					
				},
				error : function(data){
					label.innerHTML = bpps_translate.error;
				}
			});
			
		}
		
		// var path = tioButton.name;
		// var tdoButton = document.getElementById(e.target.name + "-tdoButton");
		// var tioToTdoButton = document.getElementById(path + "-tioToTdo");
		// var tdoToTioButton = document.getElementById(path + "-tdoToTio");
		// var tioCompButton = document.getElementById(path + '-tioCompButton');
		// var olStatus = document.getElementById(path + "-Status");
		// var olFilepath = document.getElementById(path + "-filePath");
		// tioButton.src=loadingBut.src;
		
		// });

	}

	$('.home-select').off().on('click', selectHome);


});
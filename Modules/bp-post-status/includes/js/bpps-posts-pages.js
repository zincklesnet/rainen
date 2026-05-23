/*
* @package bp-post-status
*/


jQuery(document).ready(function($){
	
	// Select Group Home Page
	function selectHome(e){

		var clicked = e.target;
		var postID = clicked.value;
		var label = document.getElementById("home-select-label-" + postID);

		if ( label.innerHTML == bpps_translate.homeSelected ) {
			var home = true;
		} else {
			var home = false;
		}
		
		label.innerHTML = bpps_translate.update;

		$.ajax({
			url : my_ajax_object.ajaxurl,
			type : 'post',
			data : {
				post : postID,
				security : my_ajax_object.check_nonce,
				action : "bpps_home_page"
			},
			success : function(data) {
				if ( data == 'Success' ) {
					if ( home ) {
						label.innerHTML = bpps_translate.homeSelect;
					} else {
						label.innerHTML = bpps_translate.homeSelected;
					}
				} else {
					label.innerHTML = bpps_translate.error;
				}
			},
			error : function(data){
				label.innerHTML = bpps_translate.error;
			}
		});
		
	}

	$('.home-select').off().on('click', selectHome);
	
	// Delete post
	function deletePost(e) {
		e.preventDefault();
		clicked = e.target;
		postId = clicked.dataset.post;
		postEntry = document.getElementById( 'post-' + postId );
		label = document.getElementById( 'post-label-' + postId );
		label.style.display = 'block';
		label.innerHTML = bpps_translate.deletingPost;
		$.ajax({
			url : my_ajax_object.ajaxurl,
			type : 'post',
			data : {
				postId,
				security : my_ajax_object.check_nonce,
				action : "bpps_delete_post"
			},
			success : function(data) {
				if ( data == 1 ) {
					label.innerHTML = bpps_translate.postDeleted;
					postEntry.innerHTML = '';
				} else {
					console.log( 'error: ', data );
					label.innerHTML = bpps_translate.postDeleteError;
				}
				
			},
			error : function(data){
				console.log( 'error: ', data );
				label.innerHTML = bpps_translate.postDeleteError;
			}
		});
	}

	$('.post-delete-link').off().on('click', deletePost);
	
	// @since 1.7.7
	// Publish post
	function publishPost(e) {
		e.preventDefault();
		clicked = e.target;
		postId = clicked.dataset.post;
		postEntry = document.getElementById( 'post-' + postId );
		label = document.getElementById( 'post-label-' + postId );
		label.style.display = 'block';
		label.innerHTML = bpps_translate.publishingPost;
		$.ajax({
			url : my_ajax_object.ajaxurl,
			type : 'post',
			data : {
				postId,
				security : my_ajax_object.check_nonce,
				action : "bpps_publish_post"
			},
			success : function(data) {
				if ( data == 1 ) {
					label.innerHTML = bpps_translate.postPublished;
					postEntry.innerHTML = '';
				} else {
					console.log( 'error: ', data );
					label.innerHTML = bpps_translate.postPublishError;
				}
				
			},
			error : function(data){
				console.log( 'error: ', data );
				label.innerHTML = bpps_translate.postPublishError;
			}
		});
	}

	$('.post-publish-link').off().on('click', publishPost);
	
	// Make post sticky
	function makeSticky(e){

		var clicked = e.target;
		var postId = clicked.value;
		var label = document.getElementById("sticky-select-label-" + postId);
		var context = clicked.dataset.context;
		
		if ( label.innerHTML == bpps_translate.makeSticky ) {
			var sticky = false;
		} else {
			var sticky = true;
		}
		
		label.innerHTML = bpps_translate.update;

		$.ajax({
			url : my_ajax_object.ajaxurl,
			type : 'post',
			data : {
				postId,
				context,
				security : my_ajax_object.check_nonce,
				action : "bpps_make_sticky"
			},
			success : function(data) {
				if ( data == 'Success' ) {
					if ( sticky ) {
						label.innerHTML = bpps_translate.makeSticky;
					} else {
						label.innerHTML = bpps_translate.unstick;
					}
				} else {
					console.log(data);
					label.innerHTML = bpps_translate.stickyError;
				}
				
			},
			error : function(data){
				console.log(data);
				label.innerHTML = bpps_translate.stickyError;
			}
		});
	}

	$('.sticky-select').off().on('click', makeSticky);
});
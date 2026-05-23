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

	/**
	 * Escape HTML to prevent XSS attacks.
	 *
	 * @param {string} str String to escape.
	 * @return {string} Escaped string.
	 */
	function escapeHtml( str ) {
		if ( typeof str !== 'string' ) {
			return '';
		}
		var div = document.createElement( 'div' );
		div.textContent = str;
		return div.innerHTML;
	}

	$( document ).ready( function() {

		// Schedule icon toggle
		$(document).on(
			'click',
			'.bp-activity-schedule-icon',
			function(e) {
				e.preventDefault();
				var current = $('.bp-activity-schedule-post-dropdown-list');
				current.siblings('.selected').removeClass('selected');
				current.toggleClass('selected');
			}
		);

		$('body').mouseup(
			function(e) {
				var container = $('.bp-activity-schedule-post-dropdown-list *');
				if (!container.is(e.target)) {
					$('.bp-activity-schedule-post-dropdown-list').removeClass('selected');
				}
			}
		);

		/* Show the Schedule post section */
		$( document).on( 'click', '.bp-activity-schedule-post-action', function( e ) {
			e.preventDefault();
			$('.bp-activity-schedule-post-dropdown-list').removeClass('selected');
			$( '.bp-activity-schedule-post-modal #bp-schedule-activity-form-modal').show();
			$( 'body').addClass( 'bp-schedule-model-open' );
			if ( 'undefined' !== typeof jQuery.fn.datetimepicker ) {
				var currentDate = new Date();
				$( '.bp-activity-schedule-action-popup .bp-schedule-activity-date-field' ).datetimepicker(
					{
						format: 'Y-m-d',
						timepicker: false,
						mask: false,
						minDate: 0,
						maxDate: new Date( currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() + 90 ),
						yearStart: currentDate.getFullYear(),
						defaultDate: currentDate,
						scrollMonth: false,
						scrollTime: false,
						scrollInput: false,
						className: 'bp-schedule-activity-date-wrap',
					}
				);

				$( '.bp-activity-schedule-action-popup .bp-schedule-activity-time-field' ).datetimepicker(
					{
						datepicker: false,
						format: 'h:i',
						formatTime: 'h:i',
						hours12: true,
						step: 5,
						className: 'bp-schedule-activity-time-picker',
					}
				);

			}

		});

		$( document).on( 'change', '#bp-schedule-activity-form-modal .bp-schedule-activity-date-field, #bp-schedule-activity-form-modal .bp-schedule-activity-time-field', function( e ) {

			var currentSelectedDate = $( '.bp-schedule-activity-date-field' ).val();
			var currentSelectedTime = $( '.bp-schedule-activity-time-field' ).val();
			if ( '' !== currentSelectedDate && '' !== currentSelectedTime ) {
				$( '.bp-schedule-activity' ).removeAttr( 'disabled' );
			} else {
				$( '.bp-schedule-activity' ).attr( 'disabled', 'disabled' );
			}
		});
		var submit_text     	  = $( '#aw-whats-new-submit' ).val();
		/* Show the Schedule post section */
		$( document).on( 'click', '#bp-schedule-activity-form-modal .bp-schedule-activity', function( e ) {
			e.preventDefault();
			submit_text     	      = $( '#aw-whats-new-submit' ).val();
			var schedulePost          = $( this ).parents( '.bp-activity-schedule-action-popup' );
			var schedulePost_date_raw = schedulePost.find( '.bp-schedule-activity-date-field' ).val();
			var schedulePost_time     = schedulePost.find( '.bp-schedule-activity-time-field' ).val();
			var schedulePost_meridian = schedulePost.find( 'input[name="bp_schedule_activity_meridian"]:checked' ).val();

			var UserDate          = new Date( schedulePost_date_raw );
			var monthName         = UserDate.toLocaleString( 'en-us', { month: 'short' } );
			var dateNumber        = UserDate.getDate();
			var schedulePost_date = monthName + ' ' + dateNumber;

			// Check if time has passed and trigger warning and revert to normal post button.
			var activity_schedule_datetime = schedulePost_date_raw + ' ' + schedulePost_time + ' ' + schedulePost_meridian;
			var activity_schedule_date     = new Date( activity_schedule_datetime );
			var current_date               = new Date( bpServerTime().currentServerTime );

			var threeMonthsAgo = new Date( current_date );
			threeMonthsAgo.setMonth( current_date.getMonth() + 3 );
			$('input[name="bp_schedule_activity_type"]').val( '' );
			$('#bp-schedule-activity-section').remove();
			$('body').removeClass('bp-schedule-model-open');
			if ( current_date > activity_schedule_date ) {
				$('#whats-new-submit #aw-whats-new-submit').val(  submit_text );
				$( '#whats-new-options').after('<div id="message" class="bp-messages bp-feedback error bp-schedule-feedback"><span class="bp-icon" aria-hidden="true"></span><p>' + bpsa_ajax_object.activity_schedule.strings.scheduleWarning + '</p></div>');

				setTimeout(
						function () {
							$('#message.bp-schedule-feedback').remove();
						},
						3000
					);
			} else if ( UserDate > threeMonthsAgo.getTime() ) {
				$('#whats-new-submit #aw-whats-new-submit').val(  submit_text );
				$( '#whats-new-options').after('<div id="message" class="bp-messages bp-feedback error bp-schedule-feedback"><span class="bp-icon" aria-hidden="true"></span><p>' + bpsa_ajax_object.activity_schedule.strings.scheduleWarning + '</p></div>');
				// Clear Feedback after 3 sec.
				setTimeout(
						function () {
							$('#message.bp-schedule-feedback').remove();
						},
						3000
					);
			} else {
				$('#message').remove();
				$('input[name="bp_schedule_activity_type"]').val( 'scheduled' );
				$('#whats-new-submit #aw-whats-new-submit').val(  bpsa_ajax_object.button_schedule_text );
				$( '#bp-schedule-activity-form-modal .bp-schedule-activity' ).removeAttr( 'disabled' );
				$( '.bp-schedule-activity-clear' ).show();

				$( '.bp-activity-schedule-icon' ).addClass( 'is_scheduled' );

				let	bp_schedule_posting = '<div id="bp-schedule-activity-section"><span class="bp-schedule-activity-details"><span class="dashicons dashicons-clock"></span><strong>' + escapeHtml( bpsa_ajax_object.schedule_activity_string ) + '</strong> ' + escapeHtml( schedulePost_date ) + ' '+ escapeHtml( bpsa_ajax_object.schedule_activity_timeat ) +' ' + escapeHtml( schedulePost_time ) +'<span class="activity-post-meridiem">' + escapeHtml( schedulePost_meridian ) + '</span></span></div>';
				if ( bpsa_ajax_object.buddyboss ) {
					$( '#whats-new-form #user-status-huddle').append(bp_schedule_posting);
				} else if( 'youzify' == bpsa_ajax_object.youzify ){
					$( '#whats-new-content').append(bp_schedule_posting);
				} else {
					$( '#whats-new-textarea').append(bp_schedule_posting);
				}
			}

			$( this ).closest( '#bp-schedule-activity-form-modal' ).hide();
		});

		/**
		 *  Get current Server Time
		 */
		function bpServerTime() {
			var localTime = new Date();
			var bpServerTimeDiff = new Date( bpsa_ajax_object.wpTime ).getTime() - new Date().getTime();
			var currentServerTime = new Date( localTime.getTime() + bpServerTimeDiff );

			// Extract date, year, and time components
			var date = currentServerTime.toLocaleDateString('en-US', { month: 'short', day: '2-digit' });
			var year = currentServerTime.getFullYear();
			var time = currentServerTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

			return {
				currentServerTime: currentServerTime,
				date: date,
				year: year,
				time: time
			};
		}
		$( document ).on( 'click', '.bp-schedule-activity-clear', function( e ) {

			e.preventDefault();
			$( this ).closest( '#bp-schedule-activity-form-modal' ).hide();
			$('input[name="bp_schedule_activity_type"]').val( '' );
			$('.bp-schedule-activity-date-field').val( '' );
			$('.bp-schedule-activity-time-field').val( '' );
			$('#whats-new-submit #aw-whats-new-submit').val(  submit_text );
			$( '.bp-activity-schedule-icon' ).removeClass( 'is_scheduled' );
			$('#bp-schedule-activity-section').remove();
			$('body').removeClass('bp-schedule-model-open');
		});


		/* Hide the Schedule post modal */
		$( document).on( 'click', '#bp-schedule-activity-form-modal .bp-model-close-button, #bp-schedule-activity-form-modal .bp-schedule-activity-cancel', function( e ) {
			e.preventDefault();
			$( this ).closest( '#bp-schedule-activity-form-modal' ).hide();
			$('input[name="bp_schedule_activity_type"]').val( '' );
			$('#bp-schedule-activity-section').remove();
			$('body').removeClass('bp-schedule-model-open');
		});

		$( document).on( 'click', '.buddypress-schedule-activity-lists .activity-item .delete-activity', function( event ) {
			var target = $( this ), activity_item = $( this ).parents( 'li.activity-item'),
				activity_id = activity_item.data( 'bp-activity-id' ), li_parent;
			event.preventDefault();
			var _wpnonce = getDeleteActivityLinkParams( target.prop( 'href' ), '_wpnonce' )
			if( typeof activity_id == 'undefined') {
				activity_id = $( this ).parents( 'li.activity-item').find('.youzify-activity-tools').data( 'activity-id' );
				_wpnonce = $( this ).data('nonce');
			}

			if ( undefined !== bpsa_ajax_object.confirm && false === window.confirm( bpsa_ajax_object.confirm ) ) {
				return false;
			}
			li_parent = activity_item;
			target.addClass( 'loading' );

			var ajaxData = {
				action      : 'delete_schedule_activity',
				'id'        : activity_id,
				'_wpnonce'  : _wpnonce,
				'is_single' : target.closest( '[data-bp-single]' ).length
			};

			$.ajax({
				url: bpsa_ajax_object.ajax_url,
				type: 'post',
				data: ajaxData,
				dataType: 'json',
				success: function( response ){
					target.removeClass( 'loading' );
					if ( false === response.success ) {
						li_parent.prepend( response.data.feedback );
						li_parent.find( '.bp-feedback' ).hide().fadeIn( 300 );
					} else {
						// Specific case of the single activity screen.
						if ( response.data.redirect ) {
							return window.location.href = response.data.redirect;
						}
						// Remove the entry
						li_parent.slideUp( 300, function() {
							li_parent.remove();
							$( 'li#bp-schedulde-activity-personal-li a span.count').text($( 'li.activity-item' ).length);
						} );

					}
				}
			});

		});

		function getDeleteActivityLinkParams( url, param) {
			var qs;
			if ( url ) {
				qs = ( -1 !== url.indexOf( '?' ) ) ? '?' + url.split( '?' )[1] : '';
			} else {
				qs = document.location.search;
			}

			if ( ! qs ) {
				return null;
			}

			var params = qs.replace( /(^\?)/, '' ).split( '&' ).map( function( n ) {
				return n = n.split( '=' ), this[n[0]] = n[1], this;
			}.bind( {} ) )[0];

			if ( param ) {
				return params[param];
			}

			return params;
		}

		/* Load More schedule activity*/
		$(document).on('click', '.bp-schedule-activty-load-more.load-more', function (event) {

			if ( ! bpsa_ajax_object.buddyboss ) {

				event.preventDefault();
				var target = $(this);
				var loadMoreLink = $(event.currentTarget).children().first();
				var next_page = loadMoreLink ? bp.Nouveau.getLinkParams(loadMoreLink.prop('href'), 'acpage') : 1;
				var offsetLower = loadMoreLink ? bp.Nouveau.getLinkParams(loadMoreLink.prop('href'), 'offset_lower') : 0;
				target.addClass('loading');
				var ajaxData = {
					'action': 'get_schedule_activity',
					'page': next_page,
					'offset_lower': offsetLower,
					'method': 'append',
					'scope': 'schedule-activity',
					'object': 'activity',
					'_wpnonce': bpsa_ajax_object.ajax_nonce,
				};

				$.ajax({
					url: bpsa_ajax_object.ajax_url,
					type: 'post',
					data: ajaxData,
					dataType: 'json',
					success: function (response) {
						target.removeClass('loading');
						target.remove();
						$('.buddypress-schedule-activity-lists .schedule-activity ul.activity-list').append(response.data.contents);
					}
				});
			}

		});

		/* jQuery Ajax prefilter*/
		$.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
			
			try {
				if ( originalOptions.data == null || typeof ( originalOptions.data ) == 'undefined' || typeof ( originalOptions.data.action ) == 'undefined' ) {					
					return true;
				}
			} catch ( e ) {				
				return true;
			}
			
			if (  ( originalOptions.data.action == 'post_update' ) || ( originalOptions.data.action == 'youzify_post_update' ) && originalOptions.data.bp_schedule_activity_type == 'scheduled' ) {				
				var myInterval  = setInterval(function ( jqXHR) {					
					if ( typeof jqXHR.responseJSON !== 'undefined') {
						$('#bp-schedule-activity-section').remove();
						$('body').removeClass('bp-schedule-model-open');
						$('body').append(jqXHR.responseJSON.data.message);
						setTimeout(() => {
							$('.bp-schedule-activity-posted').remove();
							if( originalOptions.data.action == 'youzify_post_update' ){
								location.reload();
							}
						}, 5000);
						clearInterval(myInterval);
					}

				}, 100, jqXHR);				
				
				
				options.success = function( response ) {						
					if ( typeof response.success != 'undefined' && response.data.is_schedule === true  ) {
						$('#bp-schedule-activity-section').remove();
						$('body').removeClass('bp-schedule-model-open');
						$('body').append(response.data.message);
						setTimeout(() => {
							$('.bp-schedule-activity-posted').remove();
							if( originalOptions.data.action == 'youzify_post_update' ){
								location.reload();
							}
						}, 5000);

					}					
				};
			}
		} );

		//Manage Schedule icon with Buddyboss Plateform
		$( document ).on(
			'click focus',
			'#whats-new',
			function(){
				if (bpsa_ajax_object.buddyboss ) {
					$( '#whats-new-toolbar' ).append( $( '#bp-activity-schedule-posts' ) );
					if ( $( '.whats-new-form-footer #whats-new-toolbar #bp-activity-schedule-posts' ).length == 0 ) {
						$( '#bp-activity-schedule-posts' ).appendTo( $( '.whats-new-form-footer #whats-new-toolbar' ) );
					}
				}
			}
		);


		if ( bpsa_ajax_object.buddyboss ) {
			$( document ).on( 'click', '.bp-activity-view-schedule-posts' , function() {
				window.location.href = $( this ).attr( 'href' );
			});

			$( document ).on( 'click', '.buddypress-schedule-activity-lists .bb-activity-more-options-action' , function( event ) {
				if ( $('script#bp-nouveau-js').length == 1 ) {
					return;
				}

				if ( $( event.target ).hasClass( 'bb-activity-more-options-action' ) || $( event.target ).parent().hasClass( 'bb-activity-more-options-action' ) ) {
					if ( $( event.target ).closest( '.bb-activity-more-options-wrap' ).find( '.bb-activity-more-options' ).hasClass( 'is_visible open' ) ) {
						$( '.bb-activity-more-options-wrap' ).find( '.bb-activity-more-options' ).removeClass( 'is_visible open' );
						$( 'body' ).removeClass( 'more_option_open' );
					} else {
						$( '.bb-activity-more-options-wrap' ).find( '.bb-activity-more-options' ).removeClass( 'is_visible open' );
						$( event.target ).closest( '.bb-activity-more-options-wrap' ).find( '.bb-activity-more-options' ).addClass( 'is_visible open' );
						$( 'body' ).addClass( 'more_option_open' );
					}

				} else {
					$( '.bb-activity-more-options-wrap' ).find( '.bb-activity-more-options' ).removeClass( 'is_visible open' );
					$( 'body' ).removeClass( 'more_option_open' );
				}
			});

			var bb_schedule_Interval;

			function bb_schedule_icon_push() {

				bb_schedule_Interval = setInterval(
					function() {
						if (bpsa_ajax_object.buddyboss && $( '#whats-new-form:not(.focus-in) #whats-new-toolbar #bp-new-activity-schedule-posts' ).length == 0 ) {
							$( '#whats-new-form:not(.focus-in) #whats-new-toolbar' ).append( '<div id="bp-new-activity-schedule-posts" class="bp-activity-schedule-posts post-elements-buttons-item"><div class="bp-activity-schedule-post_dropdown-html"><a href="javascript:void(0);" class="bp-activity-schedule-icon bp-tooltip" data-bp-tooltip-pos="up" data-bp-tooltip="' + escapeHtml( bpsa_ajax_object.add_schedule_text ) + '"><i class="dashicons dashicons-clock"></i></a></div></div>	' );
						}
					},
					10
				);

			}

			bb_schedule_icon_push();

			$( document ).on(
				'click',
				'.bb-model-close-button, .activity-update-form-overlay',
				function(){
					clearInterval( bb_schedule_Interval );
					bb_schedule_icon_push();
				}
			);

			/// jQuery Ajax prefilter
			$.ajaxPrefilter(
				function( options, originalOptions, jqXHR ) {
					try {
						if ( originalOptions.data == null || typeof ( originalOptions.data ) == 'undefined' || typeof ( originalOptions.data.action ) == 'undefined' ) {
							 return true;
						}
					} catch ( e ) {
						return true;
					}

					if ( originalOptions.data.action == 'post_update' ) {
						clearInterval( bb_schedule_Interval );
						bb_schedule_icon_push();
					}

				}
			);
		}

		if ( bpsa_ajax_object.buddyboss && ! bpsa_ajax_object.bb_theme ) {		
			var schedule_activity_count = bpsa_ajax_object.count || 0;
			var $label = $('#bp-schedulde-activity-personal-li a');
			if ($label.length && schedule_activity_count > 0) {
				$label.append(' <span class="count">' + schedule_activity_count + '</span>');
			}
		}

	});

})( jQuery );

( function ( $ ) {
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

    jQuery( document ).ready( function ( $ ) {
        var obj_rtl;
        if ( $( 'body' ).hasClass( "rtl" ) ) {
            obj_rtl = true;
        } else {
            obj_rtl = false;
        }

        $( '.bpquotes-bg-selection-div' ).slick( {
            dots: false,
            infinite: true,
            variableWidth: true,
            swipeToSlide: true,
            rtl: obj_rtl
        } );

        $( document ).on( 'click', '.bpquotes-selection', function () {

            if ( $( this ).hasClass( 'current' ) ) {
                remove_quotes( $( this ) );
                return;
            }
            $( '.bpquotes-selection' ).removeClass( 'current' );
            $( '.remove-bpquotes-selection' ).removeClass( 'current' );
            $( this ).addClass( 'current' );
            $( '.bg-type-input' ).val( $( this ).data( 'bg-type' ) );
            $( '.bg-type-value' ).val( $( this ).data( 'bg-value' ) );
            $( '.bg-inverted-type-value' ).val( $( this ).data( 'bg-inverted-value' ) );
            $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotesimg-bg-selected' );
            $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotescolors-bg-selected' );
            if ( $( this ).data( 'bg-type' ) == 'quotesimg' ) {
                $( "#whats-new, #bppfa-whats-new" ).css( "background-image", 'url(' + $( this ).data( 'bg-value' ) + ')' );
				$( "#whats-new, #bppfa-whats-new" ).css( "color", '' );
                $( '#whats-new, #bppfa-whats-new' ).addClass( 'quotesimg-bg-selected' );
            } else {
				$( '#whats-new, #bppfa-whats-new' ).addClass( 'quotescolors-bg-selected' );
				/*
				$( "#whats-new, #bppfa-whats-new" ).css( "color", $( this ).data( 'bg-inverted-value' ) + ' !important;' );
                $( "#whats-new, #bppfa-whats-new" ).css( "background", $( this ).data( 'bg-value' ) );				
				*/
				
                $("#whats-new, #bppfa-whats-new").css('cssText', "resize:vertical;height:auto;background: " +$( this ).data( 'bg-value' ) + ';' + "color: "+$( this ).data( 'bg-inverted-value' ) + ' !important;');
				
            }

        } );

        function remove_quotes( clicked_obj ) {
            clicked_obj.removeClass( 'current' );
            $( '.bg-type-input' ).val( '' );
            $( '.bg-type-value' ).val( '' );
            $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotesimg-bg-selected' );
            $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotescolors-bg-selected' );
            $( "#whats-new, #bppfa-whats-new" ).css( "background-image", '' );
            $( "#whats-new, #bppfa-whats-new" ).css( "background", '' );
            $( "#whats-new, #bppfa-whats-new" ).css( "color", '' );
        }

        $( document ).on( 'click', '.remove-bpquotes-selection', function () {
            $( this ).addClass( 'current' );
            $( '.bpquotes-selection' ).removeClass( 'current' );
            $( '.bg-type-input' ).val( '' );
            $( '.bg-type-value' ).val( '' );
            $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotesimg-bg-selected' );
            $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotescolors-bg-selected' );
            $( "#whats-new, #bppfa-whats-new" ).css( "background-image", '' );
            $( "#whats-new, #bppfa-whats-new" ).css( "background", '' );
            $( "#whats-new, #bppfa-whats-new" ).css( "color", '' );
        } );
        $( document ).on( 'click', '#aw-whats-new-reset', function () {
            $( '.bpquotes-selection' ).removeClass( 'current' );
            $( '.remove-bpquotes-selection' ).removeClass( 'current' );
            $( '.bg-type-input' ).val( '' );
            $( '.bg-type-value' ).val( '' );
            $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotesimg-bg-selected' );
            $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotescolors-bg-selected' );
            $( "#whats-new, #bppfa-whats-new" ).css( "background-image", '' );
            $( "#whats-new, #bppfa-whats-new" ).css( "background", '' );
            $( '.bpquotes-selection' ).css( 'pointerEvents', 'auto' );
        } );

        $( document ).on( 'click', '#rtmedia-add-media-button-post-update', function () {
            $( '.remove-bpquotes-selection' ).trigger( 'click' );
            $( '.remove-bpquotes-selection' ).addClass( 'current' );
            $( '.bpquotes-selection' ).css( 'pointerEvents', 'none' );
        } );


        $( document ).on( 'submit', '#aw-whats-new-submit', function () {
            $( '.bpquotes-selection' ).removeClass( 'current' );
            $( '.remove-bpquotes-selection' ).removeClass( 'current' );
            $( '.bg-type-input' ).val( '' );
            $( '.bg-type-value' ).val( '' );
            $( '.bpquotes-selection' ).css( 'pointerEvents', 'auto' );
        } );

        if ( bpquotes_obj.active_template == 'nouveau' ) {
            $( document ).on( 'focus', '#whats-new, #bppfa-whats-new', function () {
				if ($(".rtmedia-plupload-container .bpolls-html-container").length == 0){
					// $(".quote-btn").appendTo(".rtmedia-plupload-container");
				}
				
                $( '.bpquotes-bg-selection-div' ).not( '.slick-initialized' ).slick( {
                    dots: false,
                    infinite: true,
                    variableWidth: true,
                    swipeToSlide: true,
                    rtl: obj_rtl
                } );

            } );
        }


        jQuery( document ).ajaxComplete( function ( event, xhr, settings ) {
            if ( settings.type == 'POST' ) {
                var formdata = deParams( settings.data );
                var action = formdata['action'];
                if ( 'post_update' == action ) {
                    setTimeout( function () {
                        $( '.bpquotes-selection' ).removeClass( 'current' );
                        $( '.remove-bpquotes-selection' ).removeClass( 'current' );
                        $( '.bg-type-input' ).val( '' );
                        $( '.bg-type-value' ).val( '' );
                        $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotesimg-bg-selected' );
                        $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotescolors-bg-selected' );
                        $( "#whats-new, #bppfa-whats-new" ).css( "background-image", '' );
                        $( "#whats-new, #bppfa-whats-new" ).css( "background", '' );
                        $( "#whats-new, #bppfa-whats-new" ).css( "color", '' );
                        $( '.bpquotes-selection' ).css( 'pointerEvents', 'auto' );
                    }, 1000 );
                }
            }
        } );
        function deParams( str ) {
            return ( str ).replace( /(^\?)/, '' ).split( "&" ).map( function ( n ) {
                return n = n.split( "=" ), this[n[0]] = n[1], this
            }.bind( { } ) )[0];
        }
		
		$( document ).on( 'focus', '#whats-new,#bppfa-whats-new', function () {
			$( '.bpquotes-bg-selection-div' ).hide();
			if ( $(".rtmedia-plupload-container .quote-btn").length == 0 ) {
				// $('.quote-btn').remove();				
				// $( '.bpquotes-bg-selection-div' ).appendTo('#whats-new-options');
				// $( '.bpquotes-bg-selection-div' ).appendTo('#bppfa-whats-new-options');
				// $( '.bp-checkin-panel' ).appendTo('#whats-new-options');
				// $( '.bp-checkin-panel' ).appendTo('#bppfa-whats-new-options');
				// $('<div class="quote-btn"><span class="dashicons dashicons-editor-quote"></span></div>').insertBefore('.bg-type-input');
			}
			

			if ( $(".rtmedia-plupload-container .quote-btn").length == 0 && $(".rtmedia-plupload-container").length != 0){
				// $(".quote-btn").appendTo(".rtmedia-plupload-container");
				
			}
		});
		
		$( document ).on( 'click', '.quote-btn', function () {		
			$( '.bpquotes-bg-selection-div' ).slideToggle();
			$( '.bpolls-polls-option-html' ).hide();
			$( '.bp-checkin-panel' ).hide();
			if ( $( '.bpolls-input').legnth != 0 ) {
				$( '.bpolls-input').each(function(){
					$(this).val('');
				});
			}
			if ( $('.bpchk-allow-checkin').length != 0  ) {
				if (typeof bpchk_public_js_obj !== 'undefined' )  {
					var data = {
						'action': 'bpchk_cancel_checkin'
					}
					$.ajax({
						dataType: "JSON",
						url: bpchk_public_js_obj.ajaxurl,
						type: 'POST',
						data: data,
						success: function (response) {								
							$('.bpchk-checkin-temp-location').remove();								
						},
					});
				}
				$('#bpchk-autocomplete-place').val('');
				$('#bpchk-checkin-place-lat').val('');
				$('#bpchk-checkin-place-lng').val('');
				
				if ( typeof BPCHKPRO !== 'undefined' ){
					BPCHKPRO.delete_cookie( 'bpchkpro_lat' );
					BPCHKPRO.delete_cookie( 'bpchkpro_lng' );
					BPCHKPRO.delete_cookie( 'bpchkpro_place' );
					BPCHKPRO.delete_cookie( 'add_place' );
				}
			}

		});
                
        // Youzify plugin specific
        $( '.youzify-wall-actions' ).each( function () {
            $( '.bpquotes-bg-selection-div' ).hide();
            $(".quote-btn").appendTo(".youzify-form-tools");	
        });


        /**
         * Manage quote icone with BuddyBoss Plateform
         */
        $(document).on('click', '#whats-new' , function(){
            if (bpquotes_obj.buddyboss) { 
                $('.bp-quote-icon-wrapper').appendTo($('#whats-new-toolbar'));
                $('.bpquotes-bg-selection-div').appendTo($('#whats-new-attachments'));
            }
        });

    } );

} )( jQuery );

( function ( $ ) {

    "use strict";

    window.ReignHeaderIcons = {

        init: function () {
            this.markAllRead();
            this.addRemoveFriend();
        },
        markAllRead: function() {
            var notification_queue = [];
            $( document ).on( "click", ".header-notifications-dropdown-menu .action-unread", function ( e ) {
                var $action = $( this );
                var $notificationWidget = $action.closest( '.user-notifications' );
                var $dropdownMenu = $notificationWidget.find( '.header-notifications-dropdown-menu' );
                var $iconWrap = $notificationWidget.find( '.rg-icon-wrap' );
                var data = {
                    'action': 'reign_theme_unread_notification',
                    'notification_id': $action.data( 'notification-id' ),
                    '_ajax_nonce': wp_main_js_obj.reign_notification_nonce
                };
                if ( notification_queue.indexOf( $action.data( 'notification-id' ) ) !== -1 ) {
                    return false;
                }
                notification_queue.push( $action.data( 'notification-id' ) );
                var notif_icons = $iconWrap.children( '.rg-count, .count' );
                if ( notif_icons.length > 0 ) {
                    if ( $action.data( 'notification-id' ) !== 'all' ) {
                        notif_icons.html( parseInt( notif_icons.html() ) - 1 );
                    } else {
                        if ( parseInt( $dropdownMenu.find( '.dropdown-item-wrapper .dropdown-item' ).length ) < 10 ) {
                            notif_icons.fadeOut();
                        } else {
                            notif_icons.html( parseInt( notif_icons.html() ) - parseInt( $dropdownMenu.find( '.dropdown-item-wrapper .dropdown-item' ).length ) );
                        }
                    }
                }
                if ( ( $dropdownMenu.find( '.dropdown-item-wrapper .dropdown-item' ).length !== 'undefined' && $dropdownMenu.find( '.dropdown-item-wrapper .dropdown-item' ).length == 1 ) || $action.data( 'notification-id' ) === 'all' ) {
                    $dropdownMenu.find( '.dropdown-item-wrapper' ).html( '<p class="reign-header-loader"><i class="fa fa-spinner-third fa-spin"></i></p>' );
                }
                if ( $action.data( 'notification-id' ) !== 'all' ) {
                    $action.parent().parent().fadeOut();
                    $action.parent().parent().remove();
                }
                $.post(
                    wp_main_js_obj .ajaxurl,
                    data,
                    function ( response ) {
                        var notif_icons = $iconWrap.children( '.rg-count, .count' );
                        if ( notification_queue.length === 1 && response.success && typeof response.data !== 'undefined' && typeof response.data.contents !== 'undefined' && $dropdownMenu.find( '.dropdown-item-wrapper' ).length ) {
                            $dropdownMenu.find( '.dropdown-item-wrapper' ).html( response.data.contents );
                        }
                        if ( typeof response.data.total_notifications !== 'undefined' && response.data.total_notifications > 0 ) {
                            if ( notif_icons.length === 0 ) {
                                $iconWrap.append( '<sup class="count rg-count"></sup>' );
                                notif_icons = $iconWrap.children( '.rg-count' );
                            }

                            $( notif_icons ).text( response.data.total_notifications );
                            $( notif_icons ).show();
                            $dropdownMenu.find( '.mark-read-all' ).show();
                        } else {
                            $( notif_icons ).remove();
                            $dropdownMenu.find( '.mark-read-all' ).fadeOut();
                        }
                        var index = notification_queue.indexOf( $action.data( 'notification-id' ) );
                        notification_queue.splice( index, 1 );
                    }
                );
            });
        },
        addRemoveFriend: function() {
            // accept/reject friend request.
            $(".reign-friendship-btn").stop().on('click', function(e) {
                e.preventDefault();
                var $this = $(this),
                    friendshipId = $this.data("friendship-id"),
                    dataAction = $this.hasClass("accept") ? "friends_accept_friendship" : "friends_reject_friendship";
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ajaxurl,
                    data: {
                        action: "reign_ajax_addremove_friend",
                        friendship_id: friendshipId,
                        data_action: dataAction,
                        _ajax_nonce: wp_main_js_obj.reign_friendship_nonce
                    },
                    success: function (data) {
                        var response = data.data.feedback;
                        $this.closest(".reign-friend-request").find(".response").html(response);
                        if (data.success)
                            $this.closest(".request-button").remove();
                    }
                });
                return false;
            });
        }
    };

    jQuery(document).ready(function() {
        ReignHeaderIcons.init();
    });

} )( jQuery );

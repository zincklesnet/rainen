( function ( $ ) {

    "use strict";

    window.ReignHeaderIcons = {

        init: function () {
            this.markAllRead();
            this.markMessageRead();
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
        markMessageRead: function() {
            var message_queue = [];

            // Refresh dropdown content when opened if badge > 0 but list is stale/empty.
            $( document ).on( 'click', '.rg-msg.header-notifications-dropdown-toggle > .rg-icon-wrap', function() {
                var $widget   = $( this ).closest( '.rg-msg' );
                var $dropdown = $widget.find( '.header-notifications-dropdown-menu' );
                var badgeCount = parseInt( $widget.find( '.rg-icon-wrap .rg-count, .rg-icon-wrap .count' ).first().text(), 10 ) || 0;
                var hasItems   = $dropdown.find( '.dropdown-item' ).length > 0;

                if ( badgeCount > 0 && ! hasItems ) {
                    $dropdown.children( ':not(.dropdown-title)' ).remove();
                    $dropdown.append( '<p class="reign-header-loader"><i class="fa fa-spinner-third fa-spin"></i></p>' );

                    $.post( wp_main_js_obj.ajaxurl, {
                        action:      'reign_theme_get_messages_dropdown',
                        _ajax_nonce: wp_main_js_obj.reign_message_nonce
                    }, function( response ) {
                        if ( response.success && response.data.contents ) {
                            $dropdown.children( ':not(.dropdown-title)' ).remove();
                            $dropdown.append( response.data.contents );

                            var count = parseInt( response.data.unread_count, 10 ) || 0;
                            var $iw   = $widget.find( '.rg-icon-wrap' );
                            var $c    = $iw.children( '.rg-count, .count' );
                            if ( count <= 0 ) {
                                $c.remove();
                                $dropdown.find( '.mark-messages-read' ).hide();
                            } else {
                                if ( $c.length === 0 ) {
                                    $iw.append( '<sup class="count rg-count">' + ( count > 9 ? '9+' : count ) + '</sup>' );
                                } else {
                                    $c.text( count > 9 ? '9+' : count );
                                }
                                $dropdown.find( '.mark-messages-read' ).show();
                            }
                        }
                    } );
                }
            } );

            // Mark all messages as read.
            $( document ).on( 'click', '.rg-msg .mark-messages-read', function( e ) {
                e.preventDefault();
                var $msgWidget  = $( this ).closest( '.rg-msg' );
                var $iconWrap   = $msgWidget.find( '.rg-icon-wrap' );
                var $dropdown   = $msgWidget.find( '.header-notifications-dropdown-menu' );

                $( this ).hide();
                $iconWrap.children( '.rg-count, .count' ).remove();
                $dropdown.children( ':not(.dropdown-title)' ).remove();
                $dropdown.append( '<p class="reign-header-loader"><i class="fa fa-spinner-third fa-spin"></i></p>' );

                $.post( wp_main_js_obj.ajaxurl, {
                    action:      'reign_theme_mark_all_messages_read',
                    _ajax_nonce: wp_main_js_obj.reign_message_nonce
                }, function( response ) {
                    if ( response.success ) {
                        $dropdown.children( ':not(.dropdown-title)' ).remove();
                        $dropdown.append( '<div class="alert-message"><div class="alert alert-warning" role="alert">' + ( wp_main_js_obj.no_messages_text || 'No messages found.' ) + '</div></div>' );
                    }
                } );
            } );

            // Mark individual thread as read via the eye-slash button.
            $( document ).on( 'click', '.rg-msg .action-mark-message-read', function( e ) {
                e.preventDefault();
                var $btn     = $( this );
                var $item    = $btn.closest( '.dropdown-item' );
                var threadId = parseInt( $btn.data( 'thread-id' ) || $item.data( 'thread-id' ), 10 );
                var $widget  = $btn.closest( '.rg-msg' );
                var $dropdown = $widget.find( '.header-notifications-dropdown-menu' );
                var $iconWrap = $widget.find( '.rg-icon-wrap' );

                if ( ! threadId || message_queue.indexOf( threadId ) !== -1 ) {
                    return false;
                }
                message_queue.push( threadId );

                var $count  = $iconWrap.children( '.rg-count, .count' );
                var current = parseInt( $count.text(), 10 ) || 0;
                if ( current > 0 ) {
                    var next = current - 1;
                    if ( next <= 0 ) {
                        $count.remove();
                    } else {
                        $count.text( next > 9 ? '9+' : next );
                    }
                }

                var $wrapper   = $dropdown.find( '.dropdown-item-wrapper' );
                var isLastItem = $wrapper.find( '.dropdown-item' ).length === 1;
                $item.fadeOut( 300, function() {
                    $item.remove();
                    if ( isLastItem ) {
                        $wrapper.replaceWith( '<div class="alert-message"><div class="alert alert-warning" role="alert">' + ( wp_main_js_obj.no_messages_text || 'No messages found.' ) + '</div></div>' );
                        $dropdown.find( '.mark-messages-read' ).hide();
                    }
                } );

                $.post( wp_main_js_obj.ajaxurl, {
                    action:      'reign_theme_mark_message_thread_read',
                    thread_id:   threadId,
                    _ajax_nonce: wp_main_js_obj.reign_message_nonce
                }, function( response ) {
                    var idx = message_queue.indexOf( threadId );
                    if ( idx !== -1 ) {
                        message_queue.splice( idx, 1 );
                    }
                    if ( response.success && typeof response.data.unread_count !== 'undefined' ) {
                        var count = parseInt( response.data.unread_count, 10 );
                        var $c    = $iconWrap.children( '.rg-count, .count' );
                        if ( count <= 0 ) {
                            $c.remove();
                        } else {
                            if ( $c.length === 0 ) {
                                $iconWrap.append( '<sup class="count rg-count">' + ( count > 9 ? '9+' : count ) + '</sup>' );
                            } else {
                                $c.text( count > 9 ? '9+' : count );
                            }
                        }
                    }
                } );
                return false;
            } );

            // Mark individual thread as read on click.
            $( document ).on( 'click', '.rg-msg .header-notifications-dropdown-menu .dropdown-item a', function() {
                var $item     = $( this ).closest( '.dropdown-item' );
                var threadId  = parseInt( $item.data( 'thread-id' ), 10 );

                if ( ! threadId || message_queue.indexOf( threadId ) !== -1 ) {
                    return;
                }
                message_queue.push( threadId );

                var $iconWrap = $( this ).closest( '.rg-msg' ).find( '.rg-icon-wrap' );
                var $count    = $iconWrap.children( '.rg-count, .count' );
                var current   = parseInt( $count.text(), 10 ) || 0;

                if ( current > 0 ) {
                    var next = current - 1;
                    if ( next <= 0 ) {
                        $count.remove();
                    } else {
                        $count.text( next > 9 ? '9+' : next );
                    }
                }

                $.post( wp_main_js_obj.ajaxurl, {
                    action:      'reign_theme_mark_message_thread_read',
                    thread_id:   threadId,
                    _ajax_nonce: wp_main_js_obj.reign_message_nonce
                }, function( response ) {
                    var idx = message_queue.indexOf( threadId );
                    if ( idx !== -1 ) {
                        message_queue.splice( idx, 1 );
                    }
                    if ( response.success && typeof response.data.unread_count !== 'undefined' ) {
                        var count = parseInt( response.data.unread_count, 10 );
                        var $c    = $iconWrap.children( '.rg-count, .count' );
                        if ( count <= 0 ) {
                            $c.remove();
                        } else {
                            if ( $c.length === 0 ) {
                                $iconWrap.append( '<sup class="count rg-count">' + ( count > 9 ? '9+' : count ) + '</sup>' );
                            } else {
                                $c.text( count > 9 ? '9+' : count );
                            }
                        }
                    }
                } );
            } );
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

( function ( $ ) {

    "use strict";

    window.ReignBuddyPress = {

        init: function () {
            this.Slider();
            this.toggleMoreOption();
            this.rtmediaText();
            this.buddypressMenu();
            this.showActivity();
            this.profileButtons();
            this.setCounters();
            this.UpdateNotification();
            this.groupsMobile();
            this.imageResize();
            this.bpTabMenu();
            if (wp_main_js_obj.bp_subnav_view_style == 'swipe') {
                this.bpSubnavSlider();
            }
            if (wp_main_js_obj.bp_subnav_view_style == 'more') {
                this.bpSubnavMore();
            }
            this.checkPassStrength();
            this.memberActionItag();
            this.profileFilterPoint();
            this.mediaPress();
            this.activityCommentFormScroll();
            this.activityScrollFix();
            this.memberHeaderActions();
        },

        Slider: function() {
            if (wp_main_js_obj.reign_rtl) {
                var rt = true;
            } else {
                var rt = false;
            }

            $('aside #members-carousel-list, aside #members-carousel-list-widget, aside #groups-carousel-list, aside #groups-carousel-list-widget, .youzify-sidebar #members-carousel-list, .youzify-sidebar #members-carousel-list-widget, .youzify-sidebar #groups-carousel-list, .youzify-sidebar #groups-carousel-list-widget').not('.slick-initialized').slick({
                slidesToShow: 1
            });

            $('#members-carousel-list, #members-carousel-list-widget, #groups-carousel-list, #groups-carousel-list-widget').not('.slick-initialized').slick({
                dots: false,
                slidesToShow: 4,
                slidesToScroll: 4,
                rtl: rt,
                responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 543,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        },
        toggleMoreOption: function() {            
            
            $(document).on(
                'click',
                '.bp-activity-more-options-wrap .bp-activity-more-options-action',
                function(e) {
                    e.preventDefault();
            
                    var current = $(this).closest('.bp-activity-more-options-wrap');
            
                    // Remove 'selected' class from all others
                    $('.bp-activity-more-options-wrap').not(current).removeClass('selected');
            
                    // Toggle 'selected' on current one
                    current.toggleClass('selected');
                }
            );
            
            $('body').on('mouseup', function(e) {
                var container = $('.bp-activity-more-options-wrap');
                // Close dropdown if click is outside the container
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.removeClass('selected');
                }
            });
            
            
        },
        rtmediaText: function() {
            // rtmedia-activity-text
            $('.rtmedia-activity-text > span').each(function() {
                $(this).filter(function() {
                    return $.trim($(this).text()) === '' && $(this).children().length === 0
                }).remove();
            });
        },
        buddypressMenu: function() {

            $('.wbcom-nav-menu-toggle').on('click', function() {
                $(this).toggleClass('open');
            });

            // Filters
            var active = $('.rg-select-filter :selected').text();
            $('.rg-select-filter option').each(function() {
                var current = $(this).text();
                var activeClass = (current === active) ? 'current' : '';
                $('.rg-filters-wrap').append('<li class="' + activeClass + '"><a href="' + $(this).attr('value') + '">' + $(this).html() + '</a></li>');
            });

            $('.rg-filters-wrap').on('click', 'li a', function(e) {
                e.preventDefault();
                var value = $(this).attr('href');
                $('.rg-select-filter').val(value).change();
                $('.rg-filters-wrap li').removeClass('current');
                $('.rg-filters-wrap li').removeClass('selected');
                $(this).parent().addClass('current');
                return false;
            });

            // Member & Group front page widgets
            $(".member-front-page .bp-widget-area h4.widget-title").wrapInner('<span/>');
            $(".group-front-page .bp-widget-area h4.widget-title").wrapInner('<span/>');
        },
        showActivity: function() {
            $(document).on('click', '.widget_bp_reign_activity_widget div.pagination-links a', function(e) {
                e.preventDefault();
                var parent = $(this).parents('.widget_bp_reign_activity_widget').get(0);
                parent = $(parent); //cast as jquery object
                var page = get_var_in_url($(this).attr('href'), 'acpage');
                var scope = $('#reign_scope').val();
                fetch_and_show_activity(page, scope, parent);
            });

            function get_var_in_url(url, name) {
                var urla = url.split('?');
                var qvars = urla[1].split('&'); //so we have an arry of name=val,name=val
                for (var i = 0; i < qvars.length; i++) {
                    var qv = qvars[i].split('=');
                    if (qv[0] === name)
                        return qv[1];
                }
            }

            function fetch_and_show_activity(page, scope, local_scope) {
                local_scope = $(local_scope);
                var per_page = $("#reign_per_page", local_scope).val();
                var max_items = $("#reign_max_items", local_scope).val();
                var included_components = $("#reign_included_components", local_scope).val();
                var excluded_components = $("#reign_excluded_components", local_scope).val();
                var show_avatar = $("#reign_show_avatar", local_scope).val();
                var show_content = $("#reign_show_content", local_scope).val();
                var show_filters = $("#reign_show_filters", local_scope).val();
                var is_personal = $("#reign_is_personal", local_scope).val();
                var is_blog_admin_activity = $("#reign_is_blog_admin_activity", local_scope).val();
                var show_post_form = $("#reign_show_post_form", local_scope).val();
                var activity_words_count = $("#reign-activity-words-count", local_scope).val();
                $.post(wp_main_js_obj.ajaxurl, {
                        action: 'reign_fetch_content',
                        cookie: encodeURIComponent(document.cookie),
                        page: page,
                        scope: scope,
                        max: max_items,
                        per_page: per_page,
                        show_avatar: show_avatar,
                        show_content: show_content,
                        show_filters: show_filters,
                        is_personal: is_personal,
                        is_blog_admin_activity: is_blog_admin_activity,
                        included_components: included_components,
                        excluded_components: excluded_components,
                        show_post_form: show_post_form,
                        original_scope: $('#reign-original-scope').val(),
                        activity_words_count: activity_words_count,
                        allow_comment: $('#reign-activity-allow-comment').val()
                    },
                    function(response) {
                        $(".reign-wrap", local_scope).replaceWith(response);
                        $('form.reign-ac-form').hide();
                        $("#activity-filter-links li#afilter-" + scope, local_scope).addClass("selected");
                    });
            }

            //for filters
            $(document).on('click', '.widget_bp_reign_activity_widget #activity-filter-links li a', function() {
                var parent = $(this).parents('.widget_bp_reign_activity_widget').get(0);
                parent = $(parent);
                var page = 1;
                var scope = '';
                if ($(this).parent().attr('id') === 'afilter-clear') {
                    scope = $('#reign-original-scope', parent).val();
                } else {
                    scope = get_var_in_url($(this).attr('href'), 'afilter');
                }

                //update the dom scope
                $('#reign-scope').val(scope);
                fetch_and_show_activity(page, scope, parent);
                //make the current filter selected
                return false;
            });
        },
        profileButtons: function() {
            $('.rg-item-buttons').on("click", function(event) {
                event.stopPropagation();
                $(this).toggleClass('active');
            });

            $("body").on("click", function(event) {
                $('.rg-item-buttons').removeClass('active'); // or something...
            });
        },
        setCounters: function() {
            $('#wp-admin-bar-my-account-buddypress').find('li').each(function() {
                var $this = $(this),
                    $count = $this.children('a').children('.count'),
                    id,
                    $target;
                if ($count.length != 0) {
                    id = $this.attr('id');
                    $target = $('.bp-menu.bp-' + id.replace(/wp-admin-bar-my-account-/, '') + '-nav');
                    if ($target.find('.count').length == 0) {
                        $target.find('a').append('<span class="count">' + $count.html() + '</span>');
                    }
                }
            });
        },
        UpdateNotification: function() {

            //Notifications related updates
            $(document).on('heartbeat-tick.reign_notification_count', function(event, data) {

                if (data.hasOwnProperty('reign_notification_count')) {
                    data = data['reign_notification_count'];
                    /********notification type**********/
                    if (data.notification > 0) { //has count
                        jQuery("#ab-pending-notifications").text(data.notification).removeClass("no-alert");
                        jQuery("#ab-pending-notifications-mobile").text(data.notification).removeClass("no-alert");
                        jQuery("#wp-admin-bar-my-account-notifications .ab-item[href*='/notifications/']").each(function() {
                            jQuery(this).append("<span class='count'>" + data.notification + "</span>");
                            if (jQuery(this).find(".count").length > 1) {
                                jQuery(this).find(".count").first().remove(); //remove the old one.
                            }
                        });
                    } else {
                        jQuery("#ab-pending-notifications").text(data.notification).addClass("no-alert");
                        jQuery("#ab-pending-notifications-mobile").text(data.notification).addClass("no-alert");
                        jQuery("#wp-admin-bar-my-account-notifications .ab-item[href*='/notifications/']").each(function() {
                            jQuery(this).find(".count").remove();
                        });
                    }
                    //remove from read ..
                    jQuery(".mobile #wp-admin-bar-my-account-notifications-read, #adminbar-links #wp-admin-bar-my-account-notifications-read").each(function() {
                        $(this).find("a").find(".count").remove();
                    });
                    /**********messages type************/
                    if (data.unread_message > 0) { //has count
                        jQuery("#user-messages").find("span").text(data.unread_message);
                        jQuery(".ab-item[href*='/messages/']").each(function() {
                            jQuery(this).append("<span class='count'>" + data.unread_message + "</span>");
                            if (jQuery(this).find(".count").length > 1) {
                                jQuery(this).find(".count").first().remove(); //remove the old one.
                            }
                        });
                        jQuery(".rg-msg .rg-icon-wrap").each(function() {
                            var $iconWrap = jQuery(this);
                            var $count = $iconWrap.children(".rg-count");

                            if ($count.length === 0) {
                                $iconWrap.append("<span class='count rg-count'></span>");
                                $count = $iconWrap.children(".rg-count");
                            }

                            $count.text(data.unread_message).show();
                        });
                    } else {
                        jQuery("#user-messages").find("span").text(data.unread_message);
                        jQuery(".ab-item[href*='/messages/']").each(function() {
                            jQuery(this).find(".count").remove();
                        });
                        jQuery(".rg-msg .rg-icon-wrap .rg-count").remove();
                    }
                    //remove from unwanted place ..
                    jQuery(".mobile #wp-admin-bar-my-account-messages-default, #adminbar-links #wp-admin-bar-my-account-messages-default").find("li:not('#wp-admin-bar-my-account-messages-inbox')").each(function() {
                        jQuery(this).find("span").remove();
                    });
                    /**********messages type************/
                    if (data.friend_request > 0) { //has count
                        jQuery(".ab-item[href*='/friends/']").each(function() {
                            jQuery(this).append("<span class='count'>" + data.friend_request + "</span>");
                            if (jQuery(this).find(".count").length > 1) {
                                jQuery(this).find(".count").first().remove(); //remove the old one.
                            }
                        });
                    } else {
                        jQuery(".ab-item[href*='/friends/']").each(function() {
                            jQuery(this).find(".count").remove();
                        });
                    }
                    //remove from unwanted place ..
                    jQuery(".mobile #wp-admin-bar-my-account-friends-default, #adminbar-links #wp-admin-bar-my-account-friends-default").find("li:not('#wp-admin-bar-my-account-friends-requests')").each(function() {
                        jQuery(this).find("span").remove();
                    });

                    //notification content
                    //jQuery( ".user-notifications .rg-notify li" ).html( data.notification_content );
                    jQuery(".user-notifications .rg-count").html(data.notification);
                    if (data) {
                        jQuery('#wp-admin-bar-bp-notifications-default').empty();
                        jQuery('.user-notifications #rg-notify').empty();

                        jQuery.each(data.notification_content, function(i, value) {
                            jQuery('#wp-admin-bar-bp-notifications-default').append('<li>' + value + '</li>');
                            jQuery("#wp-admin-bar-bp-notifications-default a").each(function() {
                                jQuery(this).addClass('ab-item');
                            });
                        });

                        //jQuery('.user-notifications .rg-notify li:not(.rg-view-all)').remove();
                        jQuery.each(data.notification_content, function(i, value) {
                            jQuery('.user-notifications #rg-notify').append('<li>' + value + '</li>');
                        });
                    }

                }
            });

            if ( typeof wp !== "undefined" && wp.heartbeat && typeof wp.heartbeat.connectNow === "function" ) {
                $( window ).on( "focus.reign_notification_count", function() {
                    wp.heartbeat.connectNow();
                } );

                $( document ).on( "visibilitychange.reign_notification_count", function() {
                    if ( document.visibilityState === "visible" ) {
                        wp.heartbeat.connectNow();
                    }
                } );
            }
        },
        groupsMobile: function() {
            var win = $(window);
            var groupElem = $('.widget-groups-by, .widget-groups-orderby, .widget-groups-groupby');
            var activityElem = $('.widget-activity-nav, .widget-activity-subnav');
            var membersElem = $('.widget-members-nav, .widget-members-subnav');

            if (win.width() < 544) {
                if ($("#mobile-view-aside").length == 0) {
                    $('<aside id="mobile-view-aside"></aside>').insertBefore('.groups.dir-list');
                }

                if (win.width() < 544) {
                    groupElem.prependTo('#mobile-view-aside');
                    activityElem.insertAfter('.activity-content-area .entry-header');
                    membersElem.insertAfter('.members-content-area .entry-header');

                } else {
                    groupElem.prependTo('#left');
                    activityElem.prependTo('#left');
                    membersElem.prependTo('#left');
                }
            }

            $(window).on('resize', function() {
                var win = $(window); //this = window

                if (win.width() < 544) {
                    groupElem.prependTo('#mobile-view-aside');
                    activityElem.insertAfter('.activity-content-area .entry-header');
                    membersElem.insertAfter('.members-content-area .entry-header');

                } else {
                    groupElem.prependTo('#left');
                    activityElem.prependTo('#left');
                    membersElem.prependTo('#left');
                }
            });
        },
        imageResize: function() {

            var photoContainer = $(".grid .aspect-ratio .img-card");

            photoContainer.each(function() {
                var wrapperWidth = $(this).width();
                var wrapperHeight = $(this).height();
                var wrapperRatio = wrapperWidth / wrapperHeight;

                var imageWidth = $(this).find("img").width();
                var imageHeight = $(this).find("img").height();
                var imageRatio = imageWidth / imageHeight;

                /*if (wrapperWidth === 0 || wrapperHeight === 0) {
                 return false;
                 }*/

                if (imageRatio <= wrapperRatio) {
                    var newImageHeight = wrapperWidth / imageRatio;
                    var newImageWidth = wrapperWidth;
                    var semiImageHeight = newImageHeight / 2;

                    $(this).find("img").css({
                        width: newImageWidth + 1,
                        height: newImageHeight + 1,
                        marginTop: -semiImageHeight,
                        marginLeft: 0,
                        top: "50%",
                        left: "0"
                    });

                } else {
                    var newImageHeight = wrapperHeight;
                    var newImageWidth = wrapperHeight * imageRatio;
                    var semiImageWidth = newImageWidth / 2;

                    $(this).find("img").css({
                        width: newImageWidth + 1,
                        height: newImageHeight + 1,
                        marginTop: 0,
                        marginLeft: -semiImageWidth,
                        top: "0",
                        left: "50%"
                    });
                }

                $(this).css("opacity", "1");

            });
        },
        bpTabMenu: function() {

            if (wp_main_js_obj.reign_rtl) {
                var rt = true;
            } else {
                var rt = false;
            }

            $('.rg-nouveau-sidebar-menu.reign-nav-more .main-navs:not(.vertical) ul').BuddyPressMenu(35);

            // Add slider
            const navSlider = document.querySelector('.rg-nouveau-sidebar-menu.reign-nav-swipe .main-navs:not(.vertical) ul');

            if (navSlider) {

                // Check if the document is in RTL mode
                var isRTL = document.dir === 'rtl' || getComputedStyle(document.documentElement).direction === 'rtl';

                // Add Flickity class for styling
                navSlider.classList.add('flickity-enabled');

                // Initialize Flickity
                var flkty = new Flickity(navSlider, {
                    cellAlign: 'left',
                    contain: true,
                    wrapAround: false,
                    pageDots: false,
                    prevNextButtons: true,
                    groupCells: true, // can also use a number like 2
                    adaptiveHeight: false,
                    draggable: true,
                    rightToLeft: isRTL
                });
            }

            $(document).on('click', '.rg-nouveau-sidebar-menu .more-button', function(e) {
                e.preventDefault();
                $(this).toggleClass('active').next().toggleClass('active');
            });

            $(document).on('click', '.rg-nouveau-sidebar-menu .hideshow .sub-menu a', function(e) {
                //e.preventDefault();
                $('body').trigger('click');

                // add 'current' and 'selected' class
                var currentLI = $(this).parent();
                currentLI.parent('.rg-nouveau-sidebar-menu .sub-menu').find('li').removeClass('current selected');
                currentLI.addClass('current selected');
            });

            $(document).on('click', function(e) {
                var container = $('.rg-nouveau-sidebar-menu .more-button, .rg-nouveau-sidebar-menu .sub-menu');
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('.rg-nouveau-sidebar-menu .more-button').removeClass('active').next().removeClass('active');
                }
            });

        },
        bpSubnavSlider: function() {
            var rt = wp_main_js_obj.reign_rtl ? true : false; // Determine RTL setting

            // Function to initialize the slick slider
            function initializeSlick() {
                $('.item-body-inner-wrapper > .bp-subnavs .subnav').not('.slick-initialized').slick({
                    dots: false,
                    nextArrow: '<button class="slick-next slick-arrow reign-nav-swipe-arrow"><i class="far fa-angle-right"></i></button>',
                    prevArrow: '<button class="slick-prev slick-arrow reign-nav-swipe-arrow"><i class="far fa-angle-left"></i></button>',
                    infinite: false,
                    swipeToSlide: true,
                    variableWidth: true,
                    rtl: rt,
                });
            }

            // Function to unslick the slider
            function unslickSlider() {
                $('.item-body-inner-wrapper > .bp-subnavs .subnav.slick-initialized').slick('unslick');
            }

            // Initialize slick slider based on window width
            if (window.innerWidth < 748.8) {
                initializeSlick();
            }

            // Scroll function to check window width on scroll
            $(window).scroll(function() {
                // Check window width again when the user scrolls
                if (window.innerWidth < 748.8) {
                    if (!$('.item-body-inner-wrapper > .bp-subnavs .subnav').hasClass('slick-initialized')) {
                        initializeSlick();
                    }
                } else {
                    unslickSlider();
                }
            });
        },
        bpSubnavMore: function() {
            // Function to handle BuddyPressMenu initialization based on window width
            function handleBuddyPressMenu() {
                if (window.innerWidth < 748.8) {
                    $('.item-body-inner-wrapper > .bp-subnavs ul.subnav').addClass('more');
                    $('.item-body-inner-wrapper > .bp-subnavs ul.subnav.more').BuddyPressMenu(35);
                } else {
                    $('.item-body-inner-wrapper > .bp-subnavs ul.subnav').removeClass('more');
                    $('.item-body-inner-wrapper > .bp-subnavs ul.subnav.more').off('.BuddyPressMenu'); // Unbind BuddyPressMenu events
                }
            }

            // Initial execution on document ready
            $(document).ready(function() {
                handleBuddyPressMenu(); // Call the function initially
            });

            // Resize event handling
            $(window).on('resize', function() {
                // Execute the function whenever the window is resized
                handleBuddyPressMenu();
            });

            $(document).on('click', '.item-body-inner-wrapper .bp-subnavs ul.subnav .more-button', function(e) {
                e.preventDefault();
                $(this).toggleClass('active').next().toggleClass('active');
            });

            $(document).on('click', '.item-body-inner-wrapper .bp-subnavs ul.subnav .hideshow .sub-menu a', function(e) {
                //e.preventDefault();
                $('body').trigger('click');

                // add 'current' and 'selected' class
                var currentLI = $(this).parent();
                currentLI.parent('.item-body-inner-wrapper .bp-subnavs ul.subnav .sub-menu').find('li').removeClass('current selected');
                currentLI.addClass('current selected');
            });

            $(document).on('click', function(e) {
                var container = $('.item-body-inner-wrapper .bp-subnavs ul.subnav .more-button, .item-body-inner-wrapper .bp-subnavs ul.subnav .sub-menu');
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    $('.item-body-inner-wrapper .bp-subnavs ul.subnav .more-button').removeClass('active').next().removeClass('active');
                }
            });
        },
        checkPassStrength: function() {
            function check_pass_strength() {
                var pass1 = $( '.password-entry' ).val(),
                    pass2 = $( '.password-entry-confirm' ).val(),
                    strength;
        
                // Reset classes and result text
                $( '#pass-strength-result' ).removeClass( 'short bad good strong' );
                if ( ! pass1 ) {
                    $( '#pass-strength-result' ).html( pwsL10n.empty );
                    return;
                }
        
                strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputBlacklist(), pass2 );
        
                switch ( strength ) {
                    case 2:
                        $( '#pass-strength-result' ).addClass( 'bad' ).html( pwsL10n.bad );
                        break;
                    case 3:
                        $( '#pass-strength-result' ).addClass( 'good' ).html( pwsL10n.good );
                        break;
                    case 4:
                        $( '#pass-strength-result' ).addClass( 'strong' ).html( pwsL10n.strong );
                        break;
                    case 5:
                        $( '#pass-strength-result' ).addClass( 'short' ).html( pwsL10n.mismatch );
                        break;
                    default:
                        $( '#pass-strength-result' ).addClass( 'short' ).html( pwsL10n['short'] );
                        break;
                }
            }
        
            // Bind check_pass_strength to keyup events in the password fields
            $( document ).ready( function() {
                $( '.password-entry' ).val( '' ).on('keyup', check_pass_strength );
                $( '.password-entry-confirm' ).val( '' ).on('keyup', check_pass_strength );
            });
        },
        memberActionItag: function() {
            function addIconToButtons() {
                $('.wbtm-member-directory-type-3.grid .member-button-wrap>.generic-button>a, .wbtm-member-directory-type-4.grid .member-button-wrap>.generic-button>a').each(function() {
                    if ($(this).find('i').length === 0) {
                        $(this).prepend('<i></i>');
                    }
                });
            }
        
            function removeUnusedButtons() {
                // Remove friendship and message buttons
                $('.wbtm-member-directory-type-2.grid .bp-activity-more-options .friendship-button, .wbtm-member-directory-type-3.grid .bp-activity-more-options .friendship-button, .bp-activity-more-options .message-button, .wbtm-member-directory-type-4.grid .bp-activity-more-options .friendship-button').remove();
        
                // Remove empty '.bp-activity-more-options' divs
                $('.wbtm-member-directory-type-2.grid .bp-activity-more-options, .wbtm-member-directory-type-3.grid .bp-activity-more-options, .wbtm-member-directory-type-4.grid .bp-activity-more-options').each(function() {
                    if ($(this).find('.generic-button').length === 0) {
                        $(this).remove();
                    }
                });
        
                // Hide empty '.bp-activity-more-options-wrap' divs
                $('.wbtm-member-directory-type-2.grid .bp-activity-more-options-wrap, .wbtm-member-directory-type-3.grid .bp-activity-more-options-wrap, .wbtm-member-directory-type-4.grid .bp-activity-more-options-wrap').each(function() {
                    if ($(this).find('.bp-activity-more-options .generic-button').length === 0) {
                        $(this).hide();
                    }
                });
        
                // Remove empty '.footer-button-wrap' divs
                $('.wbtm-member-directory-type-2.grid .member-button-wrap.footer-button-wrap, .wbtm-member-directory-type-3.grid .member-button-wrap.footer-button-wrap, .wbtm-member-directory-type-4.grid .member-button-wrap.footer-button-wrap').each(function() {
                    if ($(this).children().length === 0 && $(this).text().trim() === '') {
                        $(this).remove();
                    }
                });
        
                // Remove '.action-wrap' divs containing only an empty <i> tag
                $('.action-wrap').each(function() {
                    if ($(this).children().length === 1 && $(this).children('i').length === 1 && $(this).text().trim() === '') {
                        $(this).remove();
                    }
                });
            }
        
            // Initial execution.
            addIconToButtons();
            removeUnusedButtons();
        
            // Re-run on AJAX completion.
            $(document).ajaxComplete(function() {
                setTimeout(function() {
                    addIconToButtons();
                    removeUnusedButtons();
                }, 500);
            });
        },
        profileFilterPoint: function () {

            if ($('#left').length === 0) {
                return;
            }
            
            var mediaQuery = '(max-width: 991px)';
            var mediaQuerySpot = window.matchMedia(mediaQuery);

            function profileSearchShift() {
                if (mediaQuerySpot.matches) {
                    $('#bp-profile-search-form-outer').prependTo('#buddypress');
                } else {
                    $('#bp-profile-search-form-outer').prependTo('#left .widget-area-inner');
                }
            }

            // Run on load
            profileSearchShift();

            // Run on media query change
            mediaQuerySpot.addEventListener('change', function () {
                profileSearchShift();
            });
        },
        mediaPress: function() {
            /**
             * Activity upload Form handling
             * Prepend the upload buttons to Activity form
             */
            if (wp_main_js_obj.theme_package_id === 'nouveau') {
                $('.activity-update-form #whats-new-form').append($('#mpp-activity-upload-buttons'));
            }

            /**
             * Handle activity post errors for MediaPress
             * Display error messages when posting activity with media but no content
             */
            
            var emptyContentMsg = wp_main_js_obj.mpp_empty_content_msg || 'Please enter some content to post.';

            // Run immediately and keep checking for empty error messages
            function checkEmptyErrorMessages() {
                $('#whats-new-form').each(function() {
                    var $form = $(this);
                    var $message = $form.find('#message.error');
                    
                    if ($message.length) {
                        var $p = $message.find('p');
                        var text = $p.text().trim();
                        
                        // If the error message is empty, this is likely the "Please enter some content" error
                        if (text === '') {
                            // Check if there's media uploaded
                            var hasMediaUpload = $form.find('#mpp-activity-upload-buttons').length > 0;
                            var textareaVal = $form.find('#whats-new').val();
                            
                            // If there's media buttons but no content in textarea, this is the content error
                            if (hasMediaUpload && (!textareaVal || textareaVal.trim() === '')) {
                                $p.text(emptyContentMsg);
                            }
                        }
                    }
                });
            }

            // Check immediately
            checkEmptyErrorMessages();

            // Also check periodically in case the message appears after AJAX
            setInterval(checkEmptyErrorMessages, 1000);

            // Also hook into BuddyPress custom event if available
            $(document).on('bp_activity_post_update_failed', function() {
                setTimeout(checkEmptyErrorMessages, 100);
            });
        },
        activityCommentFormScroll: function() {
            // Handle BuddyPress activity comment form scrolling with proper header offset
            $(document).on('click', '.acomment-reply', function(e) {
                e.preventDefault();
                var $form = $('#' + $(this).attr('href').split('#')[1]);
                if ($form.length) {
                    var headerHeight = 0;
                    
                    // Calculate total header height
                    if ($('.reign-header-top').length) {
                        headerHeight += $('.reign-header-top').outerHeight();
                    }
                    if ($('.reign-fallback-header').length) {
                        headerHeight += $('.reign-fallback-header').outerHeight();
                    }
                    
                    // Add admin bar height if present
                    if ($('body').hasClass('admin-bar')) {
                        headerHeight += $('#wpadminbar').outerHeight();
                    }
                    
                    // Add some extra padding for better visibility
                    var scrollOffset = headerHeight + 30;
                    
                    $('html, body').animate({
                        scrollTop: $form.offset().top - scrollOffset
                    }, 500);
                }
            });
        },
        activityScrollFix: function() {
            if (!$('body').hasClass('bp-nouveau') || !$('#buddypress').length || !$('#activity-stream').length) {
                return;
            }

            var scrollResetDone = false;
            var footerObserver = null;

            function resetScrollIfNeeded() {
                if (scrollResetDone) return;
                if ($(window).scrollTop() > 0) {
                    $(window).scrollTop(0);
                    scrollResetDone = true;
                    if (footerObserver) {
                        footerObserver.disconnect();
                        footerObserver = null;
                    }
                }
            }

            $(window).on('load', function() {
                setTimeout(resetScrollIfNeeded, 100);
            });

            var footer = $('#colophon, .site-footer, footer').first();
            if (footer.length && typeof MutationObserver !== 'undefined') {
                footerObserver = new MutationObserver(resetScrollIfNeeded);
                footerObserver.observe(footer[0], { childList: true, subtree: true });
            }

            setTimeout(function() {
                resetScrollIfNeeded();
                if (footerObserver) {
                    footerObserver.disconnect();
                    footerObserver = null;
                }
            }, 2000);

            if (window.location.hash) {
                var hash = window.location.hash;
                var problematicHashes = ['#reply-title', '#comments', '#respond'];
                var isActivityHash = hash.indexOf('activity') !== -1 || hash.indexOf('whats-new') !== -1;
                var isProblematic = problematicHashes.some(function(h) { return hash.indexOf(h) !== -1; });
                
                if (!isActivityHash && !isProblematic && history.replaceState) {
                    history.replaceState(null, null, ' ');
                }
            }
        },
        memberHeaderActions: function() {
            var overflowSelector = '.member-header-actions-wrap .rg-member-actions-overflow';

            function manageMemberHeaderActions($overflow) {
                var $wrap = $overflow.closest('.member-header-actions-wrap');
                var $actions = $wrap.find('#item-buttons .member-header-actions').first();
                var $toggle = $overflow.find('.bp_more_options_action');
                var $menu = $overflow.find('.bp_more_options_list');

                if (!$actions.length || !$toggle.length || !$menu.length) {
                    return;
                }

                var $items = $actions.children('.generic-button');
                var hasServerRenderedOverflow = $.trim($menu.html()).length > 0;

                if (hasServerRenderedOverflow) {
                    $items.removeClass('rg-primary-action');
                    $items.first().addClass('rg-primary-action');
                    $menu.attr('aria-hidden', 'true');
                    $toggle.attr('aria-expanded', 'false');
                    $overflow.removeClass('open').removeAttr('hidden');
                    return;
                }

                if ($items.length <= 1) {
                    $items.removeClass('rg-primary-action');
                    $menu.empty().attr('aria-hidden', 'true');
                    $toggle.attr('aria-expanded', 'false');
                    $overflow.removeClass('open').attr('hidden', true);
                    return;
                }

                $items.removeClass('rg-primary-action');
                $items.first().addClass('rg-primary-action');

                $menu.empty();
                $items.slice(1).appendTo($menu);

                $menu.attr('aria-hidden', 'true');
                $toggle.attr('aria-expanded', 'false');
                $overflow.removeClass('open').removeAttr('hidden');
            }

            $(overflowSelector).each(function() {
                manageMemberHeaderActions($(this));
            });

            $(document)
                .off('click.reignMemberHeaderActions', '.rg-member-actions-overflow .bp_more_options_action')
                .on('click.reignMemberHeaderActions', '.rg-member-actions-overflow .bp_more_options_action', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var $overflow = $(this).closest('.rg-member-actions-overflow');
                    var isOpen = $overflow.hasClass('open');

                    $(overflowSelector).not($overflow).removeClass('open')
                        .find('.bp_more_options_action').attr('aria-expanded', 'false').end()
                        .find('.bp_more_options_list').attr('aria-hidden', 'true');

                    $overflow.toggleClass('open', !isOpen);
                    $(this).attr('aria-expanded', String(!isOpen));
                    $overflow.find('.bp_more_options_list').attr('aria-hidden', String(isOpen));
                });

            $(document)
                .off('click.reignMemberHeaderActionsClose')
                .on('click.reignMemberHeaderActionsClose', function(event) {
                    if ($(event.target).closest('.rg-member-actions-overflow').length) {
                        return;
                    }

                    $(overflowSelector).removeClass('open')
                        .find('.bp_more_options_action').attr('aria-expanded', 'false').end()
                        .find('.bp_more_options_list').attr('aria-hidden', 'true');
                });
        },

    };

    jQuery(document).ready(function() {
        ReignBuddyPress.init();
    });

    // Elementor editor only: Call just the Slider method
    if (typeof elementorFrontend !== 'undefined') {
        $(window).on('elementor/frontend/init', function () {
            if (elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
                    // Exit early on live frontend
                    if (!elementorFrontend.isEditMode()) {
                        return;
                    }

                    try {
                        if (typeof ReignBuddyPress !== 'undefined' && typeof ReignBuddyPress.Slider === 'function') {
                            ReignBuddyPress.Slider();
                        }
                    } catch (error) {
                        console.warn('Error initializing ReignBuddyPress.Slider:', error);
                    }
                });
            }
        });
    }


} )( jQuery );


/* compatibilty with BP Create Type Plugin */
jQuery(document).ready(function() {
    jQuery('.wb-group-type-filters-wrap').on('click', 'li a', function(e) {
        e.preventDefault();
        var value = jQuery(this).attr('data-group-slug');
        jQuery('.wb-group-type-filters-wrap li').removeClass('current');
        jQuery(this).parent().addClass('current');

        var object = 'groups';
        bp_filter_request(
            object,
            jq.cookie('bp-' + object + '-filter'),
            jq.cookie('bp-' + object + '-scope'),
            'div.' + object,
            jQuery('#' + object + '_search').val(), //( '#bpgt-groups-search-text' ).val(),
            1,
            'group_type=' + value,
            '',
            ''
        );

        return false;
    });
});

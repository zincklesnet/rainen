/* global wc_add_to_cart_params, wp_main_js_obj, $reign_topbar_height */

(function($) {
    "use strict";
    window.Reign = {
        init: function() {
            this.stickyHeader();
            this.mobileMenu();
            this.panelMenu();
            this.iconsToggle();
            this.userMenuDropdown();
            this.responsiveMenu();
            this.headerSearch();
            if (wp_main_js_obj.enable_masonry == 'masonry-view' ) {
                this.postMasonry();
            }
            this.fitVids();
            this.stickyKit();
            this.pageLoader();
            this.addHeaderClass();
            this.addPageHeaderClass();
            this.galleryPostSlider();
            this.passwordEyeInit();
            this.singinPopupInit();
            this.registerPopupInit();
            this.singinTabInit();
            this.closePopupInit();
            this.LoginFormSubmit();
            this.TwoFactorHandlers();
            this.RegisterFormSubmit();
            this.scrollUp();
            this.postSocialShare();
            this.singlePostContentMargin();
        },
        stickyHeader: function() {

            if (!$('body').hasClass('reign-sticky-header')) { return; }

            // Force admin bar to fixed so it doesn't interfere with sticky positioning
            $('#wpadminbar').css('position', 'fixed');

            var $masthead = $('#masthead');
            if (!$masthead.length) { return; }

            // Top Bar sync: when the topbar is enabled it must stick together with
            // the header (card 9962867544). The topbar (.reign-header-top) and #masthead
            // are in-flow siblings, both made position:sticky in CSS so they stack.
            // We publish the topbar's real height as --reign-topbar-height so the masthead
            // pins directly below it. offsetHeight is 0 when the topbar is hidden
            // (e.g. .reign-topbar-hide-mobile), which keeps the masthead offset gap-free.
            var topbar = document.querySelector('.reign-header-top');
            function reignSyncTopbarHeight() {
                var h = topbar ? topbar.offsetHeight : 0;
                document.documentElement.style.setProperty('--reign-topbar-height', h + 'px');
            }
            if (topbar) {
                reignSyncTopbarHeight();
                $(window).on('resize.reignTopbar', reignSyncTopbarHeight);
            }

            // Sentinel: 1px in-flow element inserted before #masthead.
            // IntersectionObserver fires when the header pins.
            var sentinel = document.createElement('div');
            sentinel.className = 'reign-sticky-sentinel';
            $masthead[0].parentNode.insertBefore(sentinel, $masthead[0]);

            new IntersectionObserver(function(entries) {
                var pinned = !entries[0].isIntersecting;
                $masthead.toggleClass('is-pinned', pinned);
            }, { threshold: 0 }).observe(sentinel);

        },
        mobileMenu: function() {

            // Admin bar fixed
            $('#wpadminbar').css('position', 'fixed');

            // Menu toggle.
            // open the lateral panel
            $('.reign-toggler-left').on('click', function (event) {
                event.preventDefault();
                $('.reign-navbar-mobile .navbar-menu-container').addClass('is-visible');
                $('body').addClass('mobile-panel-open');
            });
            // clode the lateral panel
            $('.reign-navbar-mobile .navbar-menu-container').on('click', function (event) {
                if ($(event.target).is('.reign-navbar-mobile .navbar-menu-container') || $(event.target).is('.reign-panel-close') || $(event.target).is('.reign-panel-close i')) {
                    $('.reign-navbar-mobile .navbar-menu-container').removeClass('is-visible');
                    event.preventDefault();
                    $('body').removeClass('mobile-panel-open');
                }
            });

            // open the lateral panel
            $('.reign-user-toggler .user-link, .reign-user-toggler .ps-avatar').on('click', function (event) {
                event.preventDefault();
                $('.reign-user-toggler .user-profile-menu-wrapper').addClass('is-visible');
                $('body').addClass('mobile-panel-open');
            });
            // clode the lateral panel
            $('.reign-user-toggler .user-profile-menu-wrapper').on('click', function (event) {
                if ($(event.target).is('.reign-user-toggler .user-profile-menu-wrapper') || $(event.target).is('.reign-panel-close') || $(event.target).is('.reign-panel-close i')) {
                    $('.reign-user-toggler .user-profile-menu-wrapper').removeClass('is-visible');
                    event.preventDefault();
                    $('body').removeClass('mobile-panel-open');
                }
            });

            // Submenu toggle
            $('.reign-navbar-mobile .primary-menu .sub-menu, .reign-navbar-mobile .navbar-reign-panel .sub-menu').hide();

            $('.reign-navbar-mobile .primary-menu .menu-item-has-children, .reign-navbar-mobile .navbar-reign-panel .menu-item-has-children').each(function() {
                $(this).prepend('<i class="far submenu-btn fa-plus"></i>');
            });

            $('body').on('click', '.reign-navbar-mobile .primary-menu .submenu-btn, .reign-navbar-mobile .navbar-reign-panel .submenu-btn', function(e) {
                e.preventDefault();
                $("body").addClass('menu-active');
                $(this).toggleClass('fa-minus').parent().children('.sub-menu').slideToggle();
                $(this).toggleClass('fa-plus');
            });

            // doubleTapToGo (vendor: jquery.doubletaptogo) may be gated out on
            // some pages — guard so the mobile menu never throws if it's absent.
            if (typeof $.fn.doubleTapToGo === 'function') {
                $('.reign-navbar-mobile .primary-menu li:has(ul), .reign-navbar-mobile .navbar-reign-panel li:has(ul)').doubleTapToGo();
            }

            // Touch fallback for the DESKTOP nav (tablets/iPads render the
            // desktop menu but can't hover). The :hover reveal is gated behind
            // @media (hover:hover), and the unguarded `.main-navigation li.focus
            // > ul` selector is the deliberate touch path: first tap opens the
            // submenu via .focus, second tap follows the link. Tap outside (or
            // on another parent) closes.
            if (window.matchMedia && window.matchMedia('(hover: none)').matches) {
                $('.main-navigation').on('click', 'li.menu-item-has-children > a', function(e) {
                    var $li = $(this).parent();
                    if (!$li.hasClass('focus')) {
                        e.preventDefault();
                        $li.siblings('.focus').removeClass('focus').find('.focus').removeClass('focus');
                        $li.addClass('focus');
                    }
                });
                $(document).on('touchstart click', function(e) {
                    if (!$(e.target).closest('.main-navigation').length) {
                        $('.main-navigation li.focus').removeClass('focus');
                    }
                });
            }

        },
        panelMenu: function() {
            // Check if panel should be open by default
            if ($('.reign-menu-panel').hasClass('reign-panel-open')) {
                setCookie('reignpanel', 'open', 30, '/');
            }

            // Panel toggle
            $('.reign-menu-panel .reign-toggler').on('click', function(e) {
                e.preventDefault();

                $('.reign-menu-panel').toggleClass('reign-panel-open');

                // Save panel state to the cookie
                var panelStatus = $('.reign-menu-panel').hasClass('reign-panel-open') ? 'open' : 'closed';
                setCookie('reignpanel', panelStatus, 30, '/');
            });

            // Add mCustomScrollbar
            var $scrollElement = $('.reign-menu-panel-inner.reign-scrollbar');

            if ($scrollElement.length > 0) {
                $scrollElement.mCustomScrollbar({
                    theme: 'minimal-dark',
                    mouseWheel: { preventDefault: true },
                });
            }

            $('.reign-menu-panel .sub-menu').each(function() {
                $(this).closest('li.menu-item-has-children').find('a:first').append('<i class="far fa-angle-down rg-submenu-toggle"></i>');
            });

            $(document).on('click', '.rg-submenu-toggle', function(e) {
                e.preventDefault();
                $(this).toggleClass('rg-submenu-open').closest('a').next('.sub-menu').toggleClass('submenu-open');
            });

            function setCookie(key, value, expires, path, domain) {
                var cookie = key + '=' + escape(value) + ';';

                if (expires) {
                    if (expires instanceof Date) {
                        if (isNaN(expires.getTime())) {
                            expires = new Date();
                        }
                    } else {
                        expires = new Date(new Date().getTime() + parseInt(expires) * 1000 * 60 * 60 * 24);
                    }
                    cookie += 'expires=' + expires.toUTCString() + ';';
                }

                if (path) {
                    cookie += 'path=' + path + ';';
                }
                if (domain) {
                    cookie += 'domain=' + domain + ';';
                }

                document.cookie = cookie;
            }
        },
        iconsToggle: function() {
            // Main handler for all dropdown toggles
            $(document).on(
                'click',
                '.header-notifications-dropdown-toggle a.dropdown-toggle',
                function(e) {
                    e.preventDefault(); // Prevent anchor default (if needed)
                    e.stopPropagation(); // Stop event bubbling up

                    var current = $(this).closest('.header-notifications-dropdown-toggle');
                    current.siblings('.selected').removeClass('selected');
                    current.toggleClass('selected');
                }
            );

            // Secondary handler for cart wrapper — only toggles if not clicking an inner link or button
            $(document).on(
                'click',
                '.woo-cart-wrapper.header-notifications-dropdown-toggle',
                function(e) {
                    // Skip handling if clicking a link or form element inside the cart
                    if (
                        $(e.target).closest('a').length > 0 ||
                        $(e.target).is('button') ||
                        $(e.target).is('input') ||
                        $(e.target).is('select')
                    ) {
                        return; // Allow WooCommerce to handle it
                    }

                    e.preventDefault();
                    e.stopPropagation();

                    $(this).siblings('.selected').removeClass('selected');
                    $(this).toggleClass('selected');
                }
            );

            // Close all dropdowns when clicking outside
            $('body').on('mouseup', function(e) {
                var containers = $('.header-notifications-dropdown-toggle');
                var dropdownMenus = $('.header-notifications-dropdown-menu');
                var clickedInsideAny = false;

                containers.each(function() {
                    var $container = $(this);
                    var $dropdownMenu = $container.find('.header-notifications-dropdown-menu');
                    
                    // Check if click is on the toggle container OR inside its dropdown menu
                    if ($container.is(e.target) || 
                        $container.has(e.target).length > 0 ||
                        $dropdownMenu.is(e.target) || 
                        $dropdownMenu.has(e.target).length > 0) {
                        clickedInsideAny = true;
                        return false; // Break loop
                    }
                });

                if (!clickedInsideAny) {
                    containers.removeClass('selected');
                }
            });

            // Prevent dropdown from closing when clicking inside dropdown menu
            $(document).on('click', '.header-notifications-dropdown-menu', function(e) {
                e.stopPropagation();
            });

            // Close dropdown on ESC key
            $(document).on('keydown', function(e) {
                if ((e.key && e.key === 'Escape') || e.keyCode === 27) {
                    $('.header-notifications-dropdown-toggle.selected').removeClass('selected');
                }
            });
        },
        userMenuDropdown: function () {
            $('.user-dropdown-menu .menu-item-has-children').on('mouseenter', function () {
                var $menuItem = $(this),
                    $submenu = $menuItem.find('> .sub-menu');
        
                if ($submenu.length) {
                    var menuItemPos = $menuItem.position();
                    
                    // Set only the top position
                    $submenu.css({
                        top: menuItemPos.top
                    });
                }
            });
        },          
        responsiveMenu: function() {

            // More Menu
            if (wp_main_js_obj.reign_more_menu_enable) {
               // Toggle more menu
                $(document).on('click', '.more-button', function (e) {
                    e.preventDefault();
                    $(this).toggleClass('active open').next().toggleClass('active open');
                    $('body').toggleClass('nav_more_option_open');
                });
            
                // Close menu when clicking outside
                $(document).on('click', function(e) {
                    var container = $('.more-button, .sub-menu');
                    if (!container.is(e.target) && container.has(e.target).length === 0) {
                        $('.more-button').removeClass('active open').next().removeClass('active open');
                        $('body').removeClass('nav_more_option_open');
                    }
                });

                function toArray(arr) {
                    return Array.prototype.slice.call(arr);
                }
            
                var primaryWrap = document.getElementById('primary-navbar'),
                    primaryNav = document.getElementById('primary-menu'),
                    extendNav = document.getElementById('navbar-extend'),
                    navCollapse = document.getElementById('navbar-collapse');
            
                function navListOrder() {
                    // Move all extended items back to main menu
                    toArray(extendNav.children).forEach(function (item) {
                        primaryNav.appendChild(item);
                    });
            
                    var availableWidth = primaryWrap.offsetWidth - navCollapse.offsetWidth - 30;
                    var totalWidth = 0;
            
                    toArray(primaryNav.children).forEach(function (item) {
                        totalWidth += item.offsetWidth;
                        if (totalWidth > availableWidth) {
                            extendNav.appendChild(item);
                        }
                    });
            
                    navCollapse.classList.toggle('hasItems', extendNav.children.length > 0);
                    primaryNav.classList.remove('rg-primary-overflow');
                }
            
                // navListOrder() reads .children/offsetWidth on all four nodes,
                // so require every one before wiring it up - otherwise a page
                // without the extend/collapse containers throws
                // "Cannot read properties of null (reading 'children')".
                if (primaryNav && extendNav && primaryWrap && navCollapse) {
                    $(window).on('resize', function () {
                        setTimeout(navListOrder, 300);
                    });
                    // Initial call
                    navListOrder();
                }
            }

            $(document).on(
                'click',
                '.header-more-dropdown-toggle a.dropdown-toggle',
                function(e) {
                    e.preventDefault();
                    var current = $(this).closest('.header-more-dropdown-toggle');
                    current.siblings('.selected').removeClass('selected');
                    current.toggleClass('selected');
                }
            );

            $('body').on('mouseup', function(e) {
                var container = $('.header-more-dropdown-toggle *');
                if (!container.is(e.target)) {
                    $('.header-more-dropdown-toggle').removeClass('selected');
                }
            });

        },
        headerSearch: function() {
            var WRAP = '.search-wrap';

            function openSearch($wrap) {
                // Close any other open search first
                $('.search-wrap.search-active').not($wrap).each(function() {
                    closeSearch($(this), false);
                });
                $wrap.addClass('search-active');
                $wrap.find('.rg-search-icon').attr('aria-expanded', 'true');
                $wrap.find('.search-field').first().trigger('focus');
            }

            function closeSearch($wrap, returnFocus) {
                $wrap.removeClass('search-active');
                $wrap.find('.rg-search-icon').attr('aria-expanded', 'false');
                if (returnFocus) {
                    $wrap.find('.rg-search-icon').trigger('focus');
                }
            }

            function isActivationKey(e) {
                return e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32;
            }

            // Open / toggle on icon click or Enter / Space
            $(document).on('click keydown', WRAP + ' .rg-search-icon', function(e) {
                if (e.type === 'keydown' && !isActivationKey(e)) { return; }
                e.preventDefault();
                var $wrap = $(this).closest(WRAP);
                $wrap.hasClass('search-active') ? closeSearch($wrap, true) : openSearch($wrap);
            });

            // Close button — click or Enter / Space
            $(document).on('click keydown', WRAP + ' .rg-search-close', function(e) {
                if (e.type === 'keydown' && !isActivationKey(e)) { return; }
                e.preventDefault();
                closeSearch($(this).closest(WRAP), true);
            });

            // Escape closes and returns focus to icon
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    var $active = $('.search-wrap.search-active');
                    if ($active.length) {
                        e.preventDefault();
                        closeSearch($active, true);
                    }
                }
            });

            // Close when focus moves outside the search wrap
            $(document).on('focusout', WRAP, function() {
                var $wrap = $(this);
                setTimeout(function() {
                    if ($wrap.hasClass('search-active') && !$wrap.find(':focus').length) {
                        closeSearch($wrap, false);
                    }
                }, 0);
            });

            // Close on outside mouse click
            $(document).on('click', function(e) {
                if (!$(e.target).closest(WRAP).length) {
                    $('.search-wrap.search-active').each(function() {
                        closeSearch($(this), false);
                    });
                }
            });
        },
        postMasonry: function() {
            $('.masonry.wb-post-listing').masonry({
                itemSelector: '.masonry-view',
                columnWidth: '.reign-grid-sizer',
            });
        },
        fitVids: function() {
            // Embed fix
            $('.wp-block-embed').addClass('fitvidsignore');

            // LearnDash Player fix
            $('.ld-video iframe, .ld-tab-content .wp-block-file__embed').addClass('fitvidsignore');

            // Tutor Player fix
            $('.tutor-video-player iframe').addClass('fitvidsignore');

            // Target your .container, .wrapper, .post, etc.
            // fitVids (vendor: fitvids) is loaded globally today, but guard the
            // call so conditional loading stays safe.
            if (typeof $.fn.fitVids === 'function') {
                $("body").fitVids();
            }
        },
        stickyKit: function() {
            // theiaStickySidebar (vendor: sticky-sidebar) is now only enqueued
            // when the "Sticky Sidebar" theme option is on. Bail early if the
            // plugin isn't present so the call never throws.
            if (typeof $.fn.theiaStickySidebar !== 'function') { return; }

            var rgHeaderHeight = $('#masthead .reign-fallback-header').outerHeight();
            var offsetTop = 39;

            if ($('body').hasClass('reign-sticky-header') && $('body').hasClass('admin-bar')) {
                offsetTop = rgHeaderHeight + 71;
            } else if ($('body').hasClass('reign-sticky-header')) {
                offsetTop = rgHeaderHeight + 39;
            } else if ($('body').hasClass('admin-bar')) {
                offsetTop = 72;
            }

            if (window.innerWidth > 991) {
                $('body.reign-sticky-sidebar aside.widget-area').theiaStickySidebar({
                    additionalMarginTop: offsetTop,
                });
            }

            $(window).on('resize', function() {
                if (window.innerWidth > 991) {
                    $('body.reign-sticky-sidebar aside.widget-area').theiaStickySidebar({
                        additionalMarginTop: offsetTop
                    });
                }
            });
        },
        pageLoader: function() {
            $(window).on("load", function() {
                $('body').addClass('rg-page-loaded rg-remove-loader');
            });

            setTimeout(function() {
                if (!($('body').hasClass('rg-remove-loader'))) {
                    $('body').addClass('rg-remove-loader');
                }
            }, 3000);
        },
        addHeaderClass: function() {
            //header icon wrap remove spacing
            $('.rg-mobile-header-icon-wrap').each(function() {
                $(this).filter(function() {
                    return $.trim($(this).text()) === '' && $(this).children().length === 0
                }).remove();
            });
        },
        addPageHeaderClass: function() {
            if ($(".lm-site-header-section").length != 0) {
                $('body').addClass('lm-site-header-section-enabled');
            }
        },
        galleryPostSlider: function() {

            // slick (vendor: slick) stays globally enqueued (also used by the
            // WooCommerce/BuddyPress/EDD/PeepSo/LifterLMS bundles), but guard
            // the call so a future gating never breaks gallery archives.
            if (typeof $.fn.slick !== 'function') { return; }

            $('.archive-rg-gallery-post .gallery').each(function() {
                var obj_rtl;
                if ($('body').hasClass("rtl")) {
                    obj_rtl = true;
                } else {
                    obj_rtl = false;
                }

                $('.post_format-post-format-gallery:not(.thumbnail-view) .archive-rg-gallery-post .gallery').slick({
                    infinite: false,
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    nextArrow: '<button class="slick-next slick-arrow"><i class="far fa-angle-right"></i></button>',
                    prevArrow: '<button class="slick-prev slick-arrow"><i class="far fa-angle-left"></i></button>',
                    rtl: obj_rtl,
                    responsive: [{
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 2
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            });

            $('.thumbnail-view .archive-rg-gallery-post .gallery').each(function() {
                var obj_rtl;
                if ($('body').hasClass("rtl")) {
                    obj_rtl = true;
                } else {
                    obj_rtl = false;
                }

                $('.thumbnail-view .archive-rg-gallery-post .gallery').slick({
                    infinite: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    nextArrow: '<button class="slick-next slick-arrow"><i class="far fa-angle-right"></i></button>',
                    prevArrow: '<button class="slick-prev slick-arrow"><i class="far fa-angle-left"></i></button>',
                    rtl: obj_rtl
                });
            });
        },
        passwordEyeInit: function() {
            var $eye = $('.password-eye');

            $eye.on('click', function(event) {
                event.preventDefault();
                var $self = jQuery(this);

                var $input = $self.prev('input');

                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');

                    $self.addClass('fa-eye-slash');
                    $self.removeClass('fa-eye');
                } else {
                    $input.attr('type', 'password');
                    $self.removeClass('fa-eye-slash');
                    $self.addClass('fa-eye');
                }

            });

        },
        singinPopupInit: function() {
            $('.rg-login-btn-wrap .btn-login').on('click', function(event) {
                if (jQuery(this).attr('href') == '#') {
                    event.preventDefault();
                    var $popup = $('#registration-login-form-popup');
                    $popup.addClass('reign-window-popup-open');
                    $popup.find('.nav-tabs a.nav-link').removeClass('active');
                    $popup.find('.tab-content .tab-pane').removeClass('active');
                    $popup.find('.nav-tabs .nav-login-link').addClass('active');
                    var login_pannel = $popup.find('.nav-tabs .nav-login-link').attr('href');
                    if (typeof login_pannel !== 'undefined') {
                        $(login_pannel).addClass('active');
                    } else {
                        $('.tab-content .tab-pane').addClass('active');
                    }
                    $('.reign-overflow-x-wrapper').show();
                    $('body').addClass('modal-active');

                    // A11Y: Focus first visible input in popup.
                    setTimeout(function() {
                        $popup.find('input:visible:first').focus();
                    }, 300);

                }
            });
        },
        registerPopupInit: function() {
            $('.rg-register-btn-wrap .btn-register').on('click', function(event) {
                if (jQuery(this).attr('href') == '#') {
                    event.preventDefault();
                    $('#registration-login-form-popup').addClass('reign-window-popup-open');

                    $('#registration-login-form-popup .nav-tabs a.nav-link').removeClass('active');
                    $('#registration-login-form-popup .tab-content .tab-pane').removeClass('active');
                    $('#registration-login-form-popup .nav-tabs .nav-register-link').addClass('active');
                    var login_pannel = $('#registration-login-form-popup .nav-tabs .nav-register-link').attr('href');
                    if (typeof login_pannel !== 'undefined') {
                        $(login_pannel).addClass('active');
                    } else {
                        $('.tab-content .tab-pane').addClass('active');
                    }

                    $('.reign-overflow-x-wrapper').show();
                    $('body').addClass('modal-active');

                }
            });
        },
        singinTabInit: function() {
            $('#registration-login-form-popup .nav-tabs a.nav-link').on('click', function(event) {
                event.preventDefault();
                $('#registration-login-form-popup .nav-tabs a.nav-link').removeClass('active');
                $('#registration-login-form-popup .tab-content .tab-pane').removeClass('active');
                $(this).addClass('active');
                var login_pannel = $(this).attr('href');
                $(login_pannel).addClass('active');
            });
        },
        closePopupInit: function() {
            $(document).on('click keydown', '.reign-close-popup', function(event) {
                if (event.type === 'keydown' && (event.key !== 'Enter' && event.keyCode !== 13)) return;

                var id = $(event.target).data('id');
                if (id == 'registration-login-form-popup') {
                    event.preventDefault();
                    $('#' + id).removeClass('reign-window-popup-open');
                    $('.reign-overflow-x-wrapper').hide();
                    $('body').removeClass('modal-active');

                    // A11Y: Return focus to the trigger that opened the popup.
                    var $trigger = $('.rg-login-btn-wrap .btn-login, .rg-register-btn-wrap .btn-register').filter(':visible').first();
                    if ($trigger.length) {
                        $trigger.focus();
                    }
                }
            });

            // Close popup on ESC key
            $(document).on('keydown', function(e) {
                if ((e.key && e.key === 'Escape') || e.keyCode === 27) {
                    if ($('#registration-login-form-popup').hasClass('reign-window-popup-open')) {
                        e.preventDefault();
                        $('#registration-login-form-popup').removeClass('reign-window-popup-open');
                        $('.reign-overflow-x-wrapper').hide();
                        $('body').removeClass('modal-active');

                        var $trigger = $('.rg-login-btn-wrap .btn-login, .rg-register-btn-wrap .btn-register').filter(':visible').first();
                        if ($trigger.length) {
                            $trigger.focus();
                        }
                    }
                }
            });
        },
        LoginFormSubmit: function() {
            jQuery('.reign-sign-form-login.reign-sign-form').on('submit', function(event) {
                var $form = jQuery(this);

                // Check if we're already letting WordPress handle it
                if ($form.data('use-default')) {
                    return; // Let browser submit normally
                }

                event.preventDefault();

                var handler = $form.data('handler');
                var $messages = $form.find('.reign-sign-form-messages');

                if (!handler) {
                    return;
                }

                var prepared = { action: handler };
                var data = $form.serializeArray();

                jQuery.each(data, function(i, field) {
                    if (Array.isArray(prepared[field.name])) {
                        prepared[field.name].push(field.value);
                    } else if (typeof prepared[field.name] !== 'undefined') {
                        var val = prepared[field.name];
                        prepared[field.name] = [val, field.value];
                    } else {
                        prepared[field.name] = field.value;
                    }
                });

                // Use nonce from form if available, otherwise use the one from wp_main_js_obj
                if (!prepared['_ajax_nonce'] && wp_main_js_obj.reign_login_nonce) {
                    prepared['_ajax_nonce'] = wp_main_js_obj.reign_login_nonce;
                }


                jQuery.ajax({
                    url: wp_main_js_obj.ajaxurl,
                    type: 'POST',
                    data: prepared,
                    dataType: 'text', // Accept any response as text first
                    beforeSend: function() {
                        $form.addClass('loading');
                        $messages.empty();
                        $form.find('.invalid-feedback').remove();
                        $form.find('.is-invalid, .has-errors').removeClass('is-invalid has-errors');
                    },
                    success: function(responseText) {
                        // Detect password-related HTML
                        var lowerResp = responseText.toLowerCase();
                        if (
                            lowerResp.includes('strong password is required') ||
                            lowerResp.includes('force_password_change') ||
                            lowerResp.includes('reset your password') ||
                            lowerResp.includes('update your password')
                        ) {
                            // Let browser handle normal form submit
                            $form.data('use-default', true);
                            $form.off('submit'); // Remove this handler
                            $form.trigger('submit'); // Resubmit normally
                            return;
                        }

                        var response;
                        try {
                            response = JSON.parse(responseText);
                        } catch (e) {
                            $form.removeClass('loading');

                            // Check if this is a 2FA/security plugin response
                            var lowerResponse = responseText.toLowerCase();
                            if (lowerResponse.includes('two-factor') ||
                                lowerResponse.includes('two factor') ||
                                lowerResponse.includes('2fa') ||
                                lowerResponse.includes('authentication code') ||
                                lowerResponse.includes('check your email') ||
                                lowerResponse.includes('email has been sent') ||
                                lowerResponse.includes('magic link')) {

                                // This is likely a 2FA response
                                $messages.removeClass('woocommerce-error').addClass('woocommerce-message');
                                $messages.html('<li class="success">Two-Factor Authentication required. Please check your email for the authentication code.</li>');

                                // Check if it's 2FA (not magic link)
                                if (lowerResponse.includes('two-factor') || lowerResponse.includes('two factor') ||
                                    lowerResponse.includes('2fa') || lowerResponse.includes('authentication code')) {

                                    // Store username and password for 2FA verification
                                    var username = $form.find('input[name="log"]').val();
                                    var password = $form.find('input[name="pwd"]').val();
                                    $form.find('input[name="itsec_2fa_user"]').val(username);

                                    // Store password in data attribute
                                    $form.find('.reign-2fa-verify').data('pwd', password);

                                    // Clear password field for security after storing
                                    $form.find('input[name="pwd"]').val('');

                                    // Hide login fields and show 2FA section
                                    $form.find('.reign-login-fields').slideUp(300);
                                    $form.find('.reign-2fa-section').slideDown(300);

                                    // Focus on 2FA code input
                                    setTimeout(function() {
                                        $form.find('input[name="itsec_2fa_code"]').focus();
                                    }, 350);
                                } else {
                                    // For magic link, clear password and just show message
                                    $form.find('input[name="pwd"]').val('');
                                    setTimeout(function() {
                                        $messages.fadeOut();
                                    }, 10000);
                                }
                                return;
                            }

                            console.error('Non-JSON response:', responseText);
                            $messages.html('<li class="error">Login failed. Please refresh the page and try again.</li>');
                            return;
                        }

                        $form.removeClass('loading');

                        if (response.success) {
                            // Handle 2FA/Magic Link email sent scenario
                            if (response.data.email_sent) {
                                var message = response.data.message || 'Please check your email to complete the login process.';
                                $messages.removeClass('woocommerce-error').addClass('woocommerce-message');
                                $messages.html('<li class="success">' + message + '</li>');

                                // If it's 2FA, show the code input field
                                if (response.data.two_factor) {
                                    // Store username and password for 2FA verification
                                    var username = $form.find('input[name="log"]').val();
                                    var password = $form.find('input[name="pwd"]').val();
                                    $form.find('input[name="itsec_2fa_user"]').val(username);

                                    // Store 2FA token from server for transient-based verification
                                    if (response.data.reign_2fa_token) {
                                        $form.find('.reign-2fa-verify').data('reign_2fa_token', response.data.reign_2fa_token);
                                    }

                                    // Store password in data attribute (not visible in HTML)
                                    $form.find('.reign-2fa-verify').data('pwd', password);

                                    // Clear password field for security after storing
                                    $form.find('input[name="pwd"]').val('');

                                    // Hide login fields and show 2FA section
                                    $form.find('.reign-login-fields').slideUp(300);
                                    $form.find('.reign-2fa-section').slideDown(300);

                                    // Focus on 2FA code input
                                    setTimeout(function() {
                                        $form.find('input[name="itsec_2fa_code"]').focus();
                                    }, 350);
                                } else {
                                    // For magic link, clear password and just show message
                                    $form.find('input[name="pwd"]').val('');
                                    setTimeout(function() {
                                        $messages.fadeOut();
                                    }, 10000); // Hide message after 10 seconds
                                }
                                return;
                            }

                            if (response.data.redirect_to) {
                                location.replace(response.data.redirect_to);
                                return;
                            }

                            location.reload();
                            return;
                        }

                        if (response.data.message) {
                            var $msg = jQuery('<li class="error" />').html(response.data.message);
                            $msg.appendTo($messages);
                            return;
                        }

                        if (response.data.errors) {
                            var errors = response.data.errors;
                            $form.find('.invalid-feedback').remove();
                            $form.find('.is-invalid, .has-errors').removeClass('is-invalid has-errors');
                            $form.find('[aria-invalid]').removeAttr('aria-invalid');

                            for (var key in errors) {
                                var $field = jQuery('[name="' + key + '"]', $form);
                                var $group = $field.closest('.form-group');
                                var $error = jQuery('<div class="invalid-feedback" />').appendTo($field.parent());

                                $error.text(errors[key]);
                                $field.addClass('is-invalid').attr('aria-invalid', 'true');
                                $group.addClass('has-errors');
                            }
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $form.removeClass('loading');
                        var errorMsg = 'Login failed. Please try again.';
                        
                        if (jqXHR.status === 0) {
                            errorMsg = 'Network error. Please check your connection and try again.';
                        } else if (jqXHR.status === 403) {
                            errorMsg = 'Access denied. Please refresh the page and try again.';
                        } else if (jqXHR.status >= 500) {
                            errorMsg = 'Server error. Please try again later.';
                        } else if (textStatus === 'parsererror') {
                            errorMsg = 'Unexpected server response. Please try again.';
                        } else if (errorThrown) {
                            errorMsg = 'Error: ' + errorThrown;
                        }
                        
                        $messages.html('<li class="error">' + errorMsg + '</li>');
                    }
                });
            });
        },
        TwoFactorHandlers: function() {
            // Handle 2FA code verification
            jQuery(document).on('click', '.reign-2fa-verify', function(e) {
                e.preventDefault();

                var $button = jQuery(this);
                var $form = $button.closest('.reign-sign-form');
                var $messages = $form.find('.reign-sign-form-messages');

                var code = $form.find('input[name="itsec_2fa_code"]').val();
                var username = $form.find('input[name="itsec_2fa_user"]').val();
                var password = $button.data('pwd') || $form.find('input[name="pwd"]').val() || ''; // Get from data or field

                if (!code) {
                    $messages.removeClass('woocommerce-message').addClass('woocommerce-error');
                    $messages.html('<li class="error">Please enter the authentication code.</li>');
                    return;
                }

                // Submit with 2FA code
                jQuery.ajax({
                    url: wp_main_js_obj.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'reign-signin-form',
                        log: username,
                        pwd: password,
                        itsec_2fa_code: code,
                        reign_2fa_token: $button.data('reign_2fa_token') || '',
                        _ajax_nonce: $form.find('input[name="_ajax_nonce"]').val(),
                        redirect_to: $form.find('input[name="redirect_to"]').val(),
                        redirect: $form.find('input[name="redirect"]').val()
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $button.prop('disabled', true);
                        $form.addClass('loading');
                    },
                    success: function(response) {
                        $form.removeClass('loading');
                        $button.prop('disabled', false);

                        if (response.success) {
                            if (response.data.redirect_to) {
                                location.replace(response.data.redirect_to);
                            } else {
                                location.reload();
                            }
                        } else {
                            $messages.removeClass('woocommerce-message').addClass('woocommerce-error');
                            var errorMsg = response.data.message || 'Invalid authentication code. Please try again.';
                            $messages.html('<li class="error">' + errorMsg + '</li>');

                            // Clear the code field for retry
                            $form.find('input[name="itsec_2fa_code"]').val('').focus();
                        }
                    },
                    error: function() {
                        $form.removeClass('loading');
                        $button.prop('disabled', false);
                        $messages.removeClass('woocommerce-message').addClass('woocommerce-error');
                        $messages.html('<li class="error">An error occurred. Please try again.</li>');
                    }
                });
            });

            // Handle 2FA cancel
            jQuery(document).on('click', '.reign-2fa-cancel', function(e) {
                e.preventDefault();

                var $form = jQuery(this).closest('.reign-sign-form');
                var $messages = $form.find('.reign-sign-form-messages');

                // Hide 2FA section and show login fields
                $form.find('.reign-2fa-section').slideUp(300);
                $form.find('.reign-login-fields').slideDown(300);

                // Clear messages and 2FA code
                $messages.empty().hide();
                $form.find('input[name="itsec_2fa_code"]').val('');
                $form.find('input[name="itsec_2fa_user"]').val('');

                // Focus on username field
                setTimeout(function() {
                    $form.find('input[name="log"]').focus();
                }, 350);
            });
        },
        RegisterFormSubmit: function() {
            jQuery('.reign-sign-form-register.reign-sign-form').on('submit', function(event) {
                event.preventDefault();

                var _this = this;
                var $form = jQuery(this);

                var handler = $form.data('handler');
                var $messages = $form.find('.reign-sign-form-messages');

                if (!handler) {
                    return;
                }

                var prepared = {
                    action: handler
                };

                var data = $form.serializeArray();

                jQuery.each(data, function(i, field) {
                    if (Array.isArray(prepared[field.name])) {
                        prepared[field.name].push(field.value);
                    } else if (typeof prepared[field.name] !== 'undefined') {
                        var val = prepared[field.name];
                        prepared[field.name] = new Array();
                        prepared[field.name].push(val);
                        prepared[field.name].push(field.value);
                    } else {
                        prepared[field.name] = field.value;
                    }
                });

                // Use nonce from form if available, otherwise use the one from wp_main_js_obj
                if (!prepared['_ajax_nonce'] && wp_main_js_obj.reign_login_nonce) {
                    prepared['_ajax_nonce'] = wp_main_js_obj.reign_login_nonce;
                }


                jQuery.ajax({
                    url: wp_main_js_obj.ajaxurl,
                    dataType: 'json',
                    type: 'POST',
                    data: prepared,

                    beforeSend: function() {
                        $form.addClass('loading');

                        //Clear old errors
                        $messages.empty();
                        $form.find('.invalid-feedback').remove();
                        $form.find('.is-invalid, .has-errors').removeClass('is-invalid has-errors');
                    },
                    success: function(response) {

                        $form.removeClass('loading');
                        if (response.success) {
                            //Prevent double form submit during redirect							
                            if (response.data.redirect_to) {
                                location.replace(response.data.redirect_to);
                                return;
                            }

                            if (response.data.email_sent) {
                                $form.find('.reign-sign-form-register-fields').css('display', 'none');
                                $form.closest('.registration-login-form').css('min-height', '360px');
                                $form.closest('.registration-login-form').css('padding-left', '0');
                                $form.closest('.tab-pane').find('.title').css('display', 'none');
                                $form.closest('.tab-pane').find('.title').css('display', 'none');
                                $form.closest('.registration-login-form').find('.nav-tabs').css('display', 'none');
                                jQuery('html, body').animate({
                                    scrollTop: $form.offset().top - 140
                                }, 1000);
                                return;
                            }

                            location.reload();
                            return;
                        }

                        if (response.data.message) {
                            var $msg = jQuery('<li class="error" />');
                            $msg.html(response.data.message);
                            $msg.appendTo($messages);
                            return;
                        }

                        if (response.data.errors) {

                            var errors = response.data.errors;
                            $form.find('.invalid-feedback').remove();
                            $form.find('.is-invalid, .has-errors').removeClass('is-invalid has-errors');
                            $form.find('[aria-invalid]').removeAttr('aria-invalid');

                            for (var key in errors) {
                                var $field = jQuery('[name="' + key + '"]', $form);
                                var $group = $field.closest('.form-group');
                                var $error = jQuery('<div class="invalid-feedback" />').appendTo($field.parent());

                                $error.text(errors[key]);
                                $field.addClass('is-invalid').attr('aria-invalid', 'true');
                                $group.addClass('has-errors');
                            }
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $form.removeClass('loading');
                        var errorMsg = 'Registration failed. Please try again.';
                        
                        if (jqXHR.status === 0) {
                            errorMsg = 'Network error. Please check your connection and try again.';
                        } else if (jqXHR.status === 403) {
                            errorMsg = 'Access denied. Please refresh the page and try again.';
                        } else if (jqXHR.status >= 500) {
                            errorMsg = 'Server error. Please try again later.';
                        } else if (textStatus === 'parsererror') {
                            errorMsg = 'Unexpected server response. Please try again.';
                        } else if (errorThrown) {
                            errorMsg = 'Error: ' + errorThrown;
                        }
                        
                        $messages.html('<li class="error">' + errorMsg + '</li>');
                    }
                });

            });
        },
        scrollUp: function() {
            if (wp_main_js_obj.reign_enable_scrollup == true && wp_main_js_obj.reign_scrollup_style == 'style1') {
                $.scrollUp({
                    scrollName: 'scrollUp', // Element ID
                    scrollDistance: 300, // Distance from top/bottom before showing element (px)
                    scrollFrom: 'top', // 'top' or 'bottom'
                    scrollSpeed: 300, // Speed back to top (ms)
                    easingType: 'linear', // Scroll to top easing (see http://easings.net/)
                    animation: 'fade', // Fade, slide, none
                    animationInSpeed: 200, // Animation in speed (ms)
                    animationOutSpeed: 200, // Animation out speed (ms)
                    scrollText: '<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/></svg>', // Text for element, can contain HTML
                    scrollTitle: false, // Set a custom <a> title if required. Defaults to scrollText
                    scrollImg: false, // Set true to use image
                    activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
                    zIndex: 2147483647 // hahaha, that is some z index. Not required to have this value but nothing wrong to have it Z-Index for the overlay
                });

                // Scroll back to top animation.
                var progressPath = document.querySelector('#scrollUp path');
                var pathLength = progressPath.getTotalLength();
                progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
                progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
                progressPath.style.strokeDashoffset = pathLength;
                progressPath.getBoundingClientRect();
                progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';		
                var updateProgress = function () {
                    var scroll = $(window).scrollTop();
                    var height = $(document).height() - $(window).height();
                    var progress = pathLength - (scroll * pathLength / height);
                    progressPath.style.strokeDashoffset = progress;
                }
                updateProgress();
                $(window).scroll(updateProgress);
                var offset = 50;
                var duration = 550;
                $(window).on('scroll', function() {
                    if ($(this).scrollTop() > offset) {
                        $('#scrollUp').addClass('active-progress');
                    } else {
                        $('#scrollUp').removeClass('active-progress');
                    }
                });	
            }

            if (wp_main_js_obj.reign_enable_scrollup == true && wp_main_js_obj.reign_scrollup_style == 'style2') {
                $.scrollUp({
                    scrollName: 'scrollUp2', // Element ID
                    scrollDistance: 300, // Distance from top/bottom before showing element (px)
                    scrollFrom: 'top', // 'top' or 'bottom'
                    scrollSpeed: 300, // Speed back to top (ms)
                    easingType: 'linear', // Scroll to top easing (see http://easings.net/)
                    animation: 'fade', // Fade, slide, none
                    animationInSpeed: 200, // Animation in speed (ms)
                    animationOutSpeed: 200, // Animation out speed (ms)
                    scrollText: '<div class="scroll-top-bar-wrapper"><span class="scroll-text">' + ( wp_main_js_obj.scroll_to_top || 'Scroll to Top' ) + '</span><div class="scroll-top-bar"><div class="loading"></div></div></div>', // Text for element, can contain HTML
                    scrollTitle: false, // Set a custom <a> title if required. Defaults to scrollText
                    scrollImg: false, // Set true to use image
                    activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
                    zIndex: 2147483647 // hahaha, that is some z index. Not required to have this value but nothing wrong to have it Z-Index for the overlay
                });

                var offset = 0;
                $(document).scroll(function(){
                    offset = Math.floor($(window).scrollTop() / ($(document).height() - $(window).height())* 100) ;
                    $('.loading').css('height',offset + '%');
                });

            }
        },
        postSocialShare: function() {
            // stick_in_parent (vendor: sticky-kit) is now only enqueued on
            // singular content where the social-share box renders. Bail early
            // if the plugin isn't present so the call never throws.
            if (typeof $.fn.stick_in_parent !== 'function') { return; }

            var headerHeight = $( '#masthead' ).height();
			var headerHeightExt = headerHeight + 55;

			if ( $( window ).width() > 768 ) {
				$( '.content-wrapper > .reign-social-box-wrap' ).stick_in_parent( { offset_top: headerHeightExt, spacer: false } );
			} else {
                $( '.content-wrapper > .reign-social-box-wrap' ).trigger( "sticky_kit:detach" );
            }

            $(window).on('resize', function() {
				if ( $( window ).width() > 768 ) {
                    $( '.content-wrapper > .reign-social-box-wrap' ).stick_in_parent( { offset_top: headerHeightExt, spacer: false } );
                } else {
                    $( '.content-wrapper > .reign-social-box-wrap' ).trigger( "sticky_kit:detach" );
                }
			} );
              
        },
        singlePostContentMargin: function() {
            if ( !$( 'body' ).hasClass( 'single-post-default-layout' ) ) {
                return;
            }

            const container      = document.querySelector( '.single-post-default-layout .container' );
            const contentWrapper = document.querySelector( '.single-post-default-layout .content-wrapper' );

            if ( !container || !contentWrapper ) {
                return;
            }

            function applyMargin() {
                const maxWidth = getComputedStyle( container ).maxWidth;
                contentWrapper.style.marginLeft = ( maxWidth === '100%' && window.innerWidth >= 1400 ) ? '70px' : '';
            }

            applyMargin();
            $( window ).on( 'resize.singlePostContentMargin', applyMargin );
        },
    };

    jQuery(document).ready(function() {
        Reign.init();
    });

})(jQuery);


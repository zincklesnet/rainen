( function ( $ ) {

    "use strict";

    window.ReignWooCommerce = {

        init: function () {
            this.WooProductCategories();
            this.WooProductsSwipe();
            this.wooOffCanvas();
            this.wooCatSlider();
            if (wp_woocommerce_js_obj.enable_myaccount_menu_toggle == 1) {
                this.wooMyaccountMenuToggle();
            }
            if (wp_woocommerce_js_obj.enable_layout_view_buttons == true) {
                this.wooLayoutToggle();
                this.wooShopLoader();
            }
            this.wooSingleGallerySlider();
            this.wooSingleGallerySliderVertical();
            this.wooReviewScrollbar();
            this.wooReviewFormSticky();
        },
        WooProductCategories: function() {
            // WooCommerce product categories sidebar
            $(document).ready(function() {
                // Hide children elements initially
                $('.product-categories ul.children').hide();
        
                // Click event handler for list items
                $('.product-categories li').on('click', function(e) {
                    // Toggle active class on clicked item
                    $(this).toggleClass('active');
        
                    // Toggle visibility of children elements
                    $('ul', this).slideToggle();
        
                    // Prevent event propagation
                    e.stopPropagation();
                });
            });
        },
        WooProductsSwipe: function() {
            var rt = wp_main_js_obj.reign_rtl ? true : false;

            $(".wc-tabs-wrapper .wc-tabs").slick({
                arrows: true,
                dots: false,
                slidesToShow: 3,
                slidesToScroll: 1,
                variableWidth: true,
                infinite: false,
                swipeToSlide: true,
                rtl: rt,
                nextArrow: '<button class="slick-next slick-arrow"><i class="far fa-angle-right"></i></button>',
                prevArrow: '<button class="slick-prev slick-arrow"><i class="far fa-angle-left"></i></button>',
            });
        },
        wooOffCanvas: function() {
            var widget = $('.reign-woo-canvas-filter'),

            body = $('body');
            widget.on('click', function (e) {
                e.preventDefault();
                if (isOpened()) {
                    closeWidget();
                } else {
                    setTimeout(function () {
                        openWidget();
                    }, 10);
                }
            });

            widget.on('click', function (e) {
                e.preventDefault();
                if (isOpened()) {
                    closeWidget();
                } else {
                    setTimeout(function () {
                        openWidget();
                    }, 10);
                }
            });

            body.on("click touchstart", ".reign-woo-filter-close", function () {
                if (isOpened()) {
                    closeWidget();
                }
            });

            body.on("click", ".widget-close", function (e) {
                e.preventDefault();
                if (isOpened()) {
                    closeWidget();
                }
            });

            $(document).on('keyup', function (e) {
                if (e.keyCode === 27 && isOpened())
                    closeWidget();
            });

            var closeWidget = function () {
                $('body').removeClass('reign-woo-filter-opened');
            };

            var openWidget = function () {
                $('body').addClass('reign-woo-filter-opened');
            };

            var isOpened = function () {
                return $('body').hasClass('reign-woo-filter-opened');
            };

            $('body').addClass('document-ready');
        },
        wooMyaccountMenuToggle: function() {
            function toggleMenu() {
                var windowWidth = $(window).width();
                if (windowWidth <= 768) {
                    // My account page toggle menu.
                    $(document).off('click', 'a.rg-my-account-nav').on('click', 'a.rg-my-account-nav', function(event) {
                        event.preventDefault();
                        var self = $(this);
                        var navContainer = $(this).closest('.woocommerce-MyAccount-navigation');
                        var menu = navContainer.find('ul.woocommerce-MyAccount-menu');
        
                        menu.slideToggle();
                    });
                } else {
                    // Show the menu explicitly on larger screens.
                    $('ul.woocommerce-MyAccount-menu').css('display', 'block');
                    // Remove click event handler if window size is larger than 768px.
                    $(document).off('click', 'a.rg-my-account-nav');
                }
            }
        
            // Initial check on page load.
            toggleMenu();
        
            // Handle resize event
            $(window).on('resize', function() {
                toggleMenu();
            });
        },                  
        wooCatSlider: function() {
            /** category slick slider **/
            $('.rg-woo-category-slider-wrap').each(function() {
                if (wp_main_js_obj.reign_rtl) {
                    var rt = true;
                } else {
                    var rt = false;
                }

                $(this).slick({
                    rtl: rt,
                    responsive: [{
                            breakpoint: 991,
                            settings: {
                                slidesToShow: 2
                            }
                        },
                        {
                            breakpoint: 543,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            });
        },
        wooLayoutToggle: function() {
            function setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
                var expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
        
            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
        
            // Switch View mod
            $(document.body).on("click", ".rg-wc-view-switcher .wc-view-mod", function(e) {
                e.preventDefault();
                var mod = $(this).data("mod") || "grid-four";
                $(".rg-wc-view-switcher .wc-view-mod").removeClass("active");
                $(this).addClass("active");
                $(".rg-products.products").removeClass(
                    "columns-1 columns-2 columns-3 columns-4 columns-5 columns-6"
                );
                $(".rg-products.products").addClass("columns-" + mod);
                setCookie("reign_wc_pl_view_mod", mod, 360); // Set cookie with expiry of 360 days
            });
        
            // Apply active class based on cookie when page loads
            var cookieMod = getCookie("reign_wc_pl_view_mod");
            if (cookieMod) {
                $(".rg-wc-view-switcher .wc-view-mod").removeClass("active");
                $(".rg-wc-view-switcher .wc-view-mod[data-mod='" + cookieMod + "']").addClass("active");
                $(".rg-products.products").removeClass(
                    "columns-1 columns-2 columns-3 columns-4 columns-5 columns-6"
                );
                $(".rg-products.products").addClass("columns-" + cookieMod);
            }
        },
        wooShopLoader: function() {
            // Check if .rg-woocommerce-loading-overlay and ul.products exist in the DOM
            if ($('.rg-woocommerce-loading-overlay').length && $('ul.products').length) {                    
                setTimeout(function() {
                    $('.rg-woocommerce-loading-overlay').addClass('loaded');
                }, 300);
            }
        },      
        wooSingleGallerySlider: function() {
            $(document).on('wc-product-gallery-after-init', '.woocommerce-product-gallery', function(event, gallery) {
                var $gallery = $(gallery);
        
                // Check if the gallery parent is appropriate for slider
                if (!$gallery.parent().is('.gallery-default')) {
                    return;
                }
        
                var flexdata = $gallery.data('product_gallery');
        
                // Check if necessary data exists
                if (!flexdata || !flexdata.$images) {
                    return;
                }
        
                var $flexItems = flexdata.$images;
                var $flexThumbs = $gallery.find('.flex-control-thumbs');
        
                // If less than or equal to 5 items, no need for slider
                if ($flexItems.length <= 5) {
                    return;
                }
                if ($gallery.parent().is('.gallery-default')) {
                    // Setup default slider
                    $flexThumbs.addClass('reign-slides');
                    $flexThumbs.wrapAll('<div class="reign-flexslider"></div>');
                    var $slider = $gallery.find('.reign-flexslider');
                    var itemWidth = $gallery.parent().is('.gallery-quickview') ? 85 : 80;
        
                    // Initialize FlexSlider
                    $slider.flexslider({
                        namespace: 'reign-flex-',
                        selector: '.reign-slides > li',
                        animation: 'slide',
                        controlNav: false,
                        animationLoop: false,
                        slideshow: false,
                        itemWidth: itemWidth,
                        itemMargin: 20,
                        keyboard: false,
                        asNavFor: $gallery.get(0)
                    });
        
                    // Modify next/prev buttons
                    var next_text = $('.reign-flexslider .reign-flex-next').text();
                    $('.reign-flexslider .reign-flex-next').text('').append('<span>' + next_text + '</span>');
                    var prev_text = $('.reign-flexslider .reign-flex-prev').text();
                    $('.reign-flexslider .reign-flex-prev').text('').append('<span>' + prev_text + '</span>');
                }
            });
        },
        wooSingleGallerySliderVertical: function() {
            $(document).on('wc-product-gallery-after-init', '.woocommerce-product-gallery', function(event, gallery) {
        
                var $gallery = $(gallery);
        
                // Check if the gallery parent is appropriate for slider
                if (!$gallery.parent().is('.gallery-vertical')) {
                    return;
                }
        
                var $flexThumbs = $gallery.find('.flex-control-thumbs');
        
                if ($gallery.parent().is('.gallery-vertical')) {
                    // Setup vertical slider
                    $flexThumbs.addClass('slick-slider reign-slides');
                    $flexThumbs.wrap('<div class="reign-slick-slider"></div>');
        
                    var $slickSlider = $gallery.find('.reign-slick-slider .flex-control-nav');
        
                    $slickSlider.slick({
                        slidesToShow: 5,
                        slidesToScroll: 1,
                        vertical: true,
                        verticalSwiping: true,
                        arrows: true,
                        infinite: false,
                        prevArrow: '<div class="reign-slick-prev"></div>',
                        nextArrow: '<div class="reign-slick-next"></div>',
                        responsive: [
                            {
                                breakpoint: 992,
                                settings: {
                                    slidesToShow: 5, // Adjust this value as needed
                                    slidesToScroll: 1,
                                    vertical: false,
                                    verticalSwiping: false,
                                }
                            },
                            {
                                breakpoint: 767,
                                settings: {
                                    slidesToShow: 4, // Adjust this value as needed
                                    slidesToScroll: 1,
                                    vertical: false,
                                    verticalSwiping: false,
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: 3, // Adjust this value as needed
                                    slidesToScroll: 1,
                                    vertical: false,
                                    verticalSwiping: false,
                                }
                            },
                            {
                                breakpoint: 360,
                                settings: {
                                    slidesToShow: 2, // Adjust this value as needed
                                    slidesToScroll: 1,
                                    vertical: false,
                                    verticalSwiping: false,
                                }
                            }
                        ]
                    });
                }
            });        
        },   
        wooReviewScrollbar: function() {
            // Function to initialize scrollbar for all comment lists
            function initializeScrollbar() {
                $('.woocommerce-Reviews .commentlist').mCustomScrollbar({
                    theme: 'minimal-dark',
                    mouseWheel: { preventDefault: true },
                });
            }
        
            // Function to destroy scrollbar for layout2 on desktop view
            function destroyScrollbar() {
                $('.woo-single-product-layout2 .woocommerce-Reviews .commentlist').mCustomScrollbar('destroy');
            }
        
            // Adjust scrollbar on page load
            $(document).ready(function() {
                if ($('.woo-single-product-layout2').length > 0 && window.innerWidth > 768) {
                    destroyScrollbar(); // Destroy scrollbar if layout is layout2 and window width is greater than 768 pixels
                } else {
                    initializeScrollbar(); // Otherwise, initialize the scrollbar
                }
            });
        
            // Adjust scrollbar on window resize
            $(window).on('resize', function() {
                if ($('.woo-single-product-layout2').length > 0 && window.innerWidth > 768) {
                    destroyScrollbar(); // Destroy scrollbar if layout is layout2 and window width is greater than 768 pixels
                } else {
                    initializeScrollbar(); // Otherwise, initialize the scrollbar
                }
            });
        },                 
        wooReviewFormSticky: function() {
            var rgHeaderHeight = $('#masthead .reign-fallback-header').outerHeight();
            var offsetTop = 40;
        
            if ($('body').hasClass('reign-sticky-header') && $('body').hasClass('admin-bar')) {
                offsetTop = rgHeaderHeight + 72;
            } else if ($('body').hasClass('reign-sticky-header')) {
                offsetTop = rgHeaderHeight + 40;
            } else if ($('body').hasClass('admin-bar')) {
                offsetTop = 72;
            }

            if (window.innerWidth > 767) {
                $('.woocommerce-Reviews div#review_form_wrapper').theiaStickySidebar({
                    additionalMarginTop: offsetTop,
                });
            }
        },
    };

    jQuery(document).ready(function() {
        ReignWooCommerce.init();
    } );

    // Elementor editor only: Call just the wooCatSlider method
    if (typeof elementorFrontend !== 'undefined') {
        $(window).on('elementor/frontend/init', function () {
            if (elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
                    // Check if we're in edit mode, if not, exit early
                    if (!elementorFrontend.isEditMode()) {
                        return; // Prevent running on live frontend
                    }
                    
                    setTimeout(function() {
                        var $sliderElements = $scope.find('.rg-woo-category-slider-wrap');
                        
                        if ($sliderElements.length > 0) {
                            $sliderElements.each(function() {
                                if (!$(this).hasClass('slick-initialized')) {
                                    try {
                                        if (typeof ReignWooCommerce !== 'undefined' && typeof ReignWooCommerce.wooCatSlider === 'function') {
                                            ReignWooCommerce.wooCatSlider();
                                        }
                                    } catch (error) {
                                        console.warn('Error initializing wooCatSlider:', error);
                                    }
                                }
                            });
                        }
                    }, 100);
                });
            }
        });
    }

} )( jQuery );


/* WooCommerce quantity +/- managed */
jQuery(document).ready(function() {
    QtyChngMinus();
    QtyChngPlus();
});

// Make the code work after executing AJAX.
jQuery(document).ajaxComplete(function() {
    QtyChngMinus();
    QtyChngPlus();
});

function QtyChngMinus() {
    jQuery(document).off("click", ".product_quantity_minus").on("click", ".product_quantity_minus", function() {
        var qtyInput = jQuery(this).next('input.qty');
        var qty = parseInt(qtyInput.val());
        if (!isNaN(qty) && qty > 1) {
            qty -= 1;
            qtyInput.val(qty).trigger('change');
        }
    });
}

function QtyChngPlus() {
    jQuery(document).off("click", ".product_quantity_plus").on("click", ".product_quantity_plus", function() {
        var qtyInput = jQuery(this).prev('input.qty');
        var qty = parseInt(qtyInput.val());
        if (!isNaN(qty)) {
            qty += 1;
        } else {
            qty = 1; // If input is empty or not a number, default to 1
        }
        qtyInput.val(qty).trigger('change');
    });
}

( function ( $ ) {

    "use strict";

    window.ReignLifterlms = {

        init: function () {
            this.sideBarPosition();
            this.dashboardNavigation();
        },

        sideBarPosition: function() {
            // Single course sidebar position
            function sideBarPosition() {
                var courseBannerHeight = $('.rlla-llms-banner').height();
                var courseBannerVideo = $('.rlla-thumbnail-preview .rlla-preview-course-link-wrap');
                var thumbnailContainerHeight = courseBannerVideo.length ? courseBannerVideo.height() : 0;
            
                var sidebarOffset = (courseBannerHeight / 2) + (thumbnailContainerHeight / 2);
            
                if ($(window).width() > 820) {
                    $('.rlla-single-course-sidebar.rlla-preview-wrap').css({ 'margin-top': '-' + sidebarOffset + 'px' });
                }
            }
            
            function courseBanner() {
                var mainWidth = $('.content-wrapper').width();
                $('.rlla-llms-banner .rlla-course-banner-info.container').width(mainWidth);
            }
            
            // Call the functions initially
            sideBarPosition();
            courseBanner();
            
            // Attach resize event handler
            $(window).on('resize', function () {
                courseBanner();
                sideBarPosition();
            });
        },

        dashboardNavigation: function() {
            // Initialize slick slider
            function ColumnsMenuSlider(rt) {
                $('.llms-sd-layout-columns .llms-sd-items').slick({
                    dots: false,
                    touchMove: true,
                    slidesToShow: 5,
                    slidesToScroll: 1,
                    variableWidth: true,
                    rtl: rt
                });
            }
        
            // Set rtl variable
            var rt = wp_main_js_obj.reign_rtl ? true : false;
        
            // Initialize the slick slider if the screen size is less than 600px
            if (window.innerWidth < 600) {
                ColumnsMenuSlider(rt);
            }
        
            // Handle resize event to re-initialize slider or update settings
            $(window).on('resize', function() {
                if (window.innerWidth < 600 && !$('.llms-sd-layout-columns .llms-sd-items').hasClass('slick-initialized')) {
                    ColumnsMenuSlider(rt);
                } else if (window.innerWidth >= 600 && $('.llms-sd-layout-columns .llms-sd-items').hasClass('slick-initialized')) {
                    $('.llms-sd-layout-columns .llms-sd-items').slick('unslick');
                }
            });
        
            // Safely use slickGoTo (ensure it's a valid index)
            if (typeof slickGoTo !== 'undefined' && slickGoTo !== 0) {
                slickGoTo = parseInt(slickGoTo, 10);  // Ensure it's an integer
                $('.llms-sd-layout-columns .llms-sd-items').slick('slickGoTo', slickGoTo);
            }
        }        

    };

    jQuery(document).ready(function() {
        ReignLifterlms.init();
    } );

} )( jQuery );
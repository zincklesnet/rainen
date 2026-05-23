( function ( $ ) {

    "use strict";

    window.ReignEdd = {

        init: function () {
            this.eddSupport();
            this.eddTableWrap();
        },

        eddSupport: function() {
            $(window).on("load", function() {
                if (wp_main_js_obj.reign_rtl) {
                    var rt = true;
                } else {
                    var rt = false;
                }

                if ($(window).width() <= 767) {
                    $('.fes-vendor-menu > ul').addClass('edd-tabs');

                    var slickGoTo = $('.fes-vendor-menu-tab.active').index(0);
                    $(".fes-vendor-menu .edd-tabs").slick({
                        arrows: false,
                        dots: false,
                        touchMove: true,
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        variableWidth: true,
                        rtl: rt
                    });

                    if (slickGoTo !== 0) {
                        slickGoTo = slickGoTo - 0;
                    }

                    $('.fes-vendor-menu .edd-tabs').slick('slickGoTo', slickGoTo);
                }

                $(document).ready(function() {
                    var $rows = $("nav.fes-vendor-menu").addClass("wb-pageload");

                    setTimeout(function() {
                        $rows.removeClass("wb-pageload");
                    }, 800);
                });

            });

            $('.fes-vendor-vendor-feedback-tab > a > i').addClass('icon-user');

            /**
             * EDD Price Options Label Style
             */
            $('.edd_price_options, .modal-content').find('label').each(function() {
                $(this).removeAttr("for");
                if ($(this).find('input').is(':checked')) {
                    $(this).parent().addClass('checked');
                } else {
                    $(this).parent().removeClass('checked');
                }
                $(this).on('click', function(e) {
                    if ($(this).parents('.edd_price_options').hasClass('edd_multi_mode')) {
                        if ($(this).find('input[type="checkbox"]').is(':checked')) {
                            $(this).parent().addClass('checked');
                        } else {
                            $(this).parent().removeClass('checked');
                        }
                    } else {
                        if ($(this).find('input[type="radio"]').is(':checked')) {
                            $(this).parent().addClass('checked').siblings('li').removeClass('checked');
                        } else {
                            $(this).parent().removeClass('checked');
                        }
                    }
                });
            });
            $('label.selectit').each(function() {
                $(this).removeAttr("for");
                if ($(this).find('input').is(':checked')) {
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }
                $(this).on('click', function(e) {
                    if ($(this).find('input[type="checkbox"]').is(':checked')) {
                        $(this).addClass('checked');
                    } else {
                        $(this).removeClass('checked');
                    }
                });
            });
        },

        
        eddTableWrap: function () {
            $("table.edd_sl_table").wrap('<div class="touch-scroll-table"/>');
        },

    };

    jQuery(document).ready(function() {
        ReignEdd.init();
    });

} )( jQuery );

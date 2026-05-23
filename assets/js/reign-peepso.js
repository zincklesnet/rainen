( function ( $ ) {

    "use strict";

    window.ReignPeepSo = {

        init: function () {
            this.Slider();
        },

        Slider: function() {
            $('.ps-profile__edit-tabs').slick({
                dots: false,
                infinite: false,
                slidesToShow: 3,
                responsive: [{
                        breakpoint: 641,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 420,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]

            });
        },

    };

    jQuery(document).ready(function() {
        ReignPeepSo.init();
    } );

} )( jQuery );

(function($) {
    $(document).ready(function() {
        // PeepSo Activity Social Share
        jQuery(document).ajaxComplete(function(event, xhr, settings) {
            jQuery('.ps-socials__dropdown .peepso-share i').addClass('fab');
        });
    });
})(jQuery);
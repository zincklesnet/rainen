( function ( $ ) {

    "use strict";

    window.ReignbbPres = {

        init: function () {
            this.forumReplyScrollTop();
        },

        forumReplyScrollTop: function() {
            $('.bbp-reply-permalink-wrapper').on('click', function() {
                var targetElement = $(this);
                var offset = targetElement.offset().top - 350; // Adjust the top gap here
                $('html, body').animate({ scrollTop: offset }, 'slow');
            });

            $('.bbp-reply-permalink-wrapper').each(function() {
                if (window.location.hash) {
                    var targetElement = $(window.location.hash);
                    var offset = targetElement.offset().top - 350; // Adjust the top gap here
                    
                    $('html, body').stop().animate({ scrollTop: offset }, 'slow', function() {
                        // Animation complete
                    });
                }
            });
        }

    };

    jQuery(document).ready(function() {
        ReignbbPres.init();
    });

} )( jQuery );
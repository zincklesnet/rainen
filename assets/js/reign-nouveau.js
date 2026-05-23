/**
 * Prevents reign_nouveau_deParams TypeError while preserving other AJAX handlers
 */
(function($, window) {
    'use strict';
    
    // Safe version of the problematic function
    function safeReignNouveauDeParams(str) {
        // Handle null/undefined
        if (str == null) {
            str = window.location.search;
        }
        
        // Handle objects (like jQuery ajax settings)
        if (typeof str === 'object') {
            if (str.url) {
                var urlMatch = str.url.match(/\?(.*)$/);
                str = urlMatch ? urlMatch[1] : '';
            } else if (str.data && typeof str.data === 'string') {
                str = str.data;
            } else {
                str = '';
            }
        }
        
        // Ensure we have a string
        if (typeof str !== 'string') {
            str = String(str);
        }
        
        // If empty, return empty object
        if (!str.trim()) {
            return {};
        }
        
        // Parse query string safely
        var params = {};
        var pairs = str.replace(/^\?/, '').split('&');
        
        for (var i = 0; i < pairs.length; i++) {
            var pair = pairs[i].split('=');
            if (pair[0]) {
                var key = decodeURIComponent(pair[0].replace(/\+/g, ' '));
                var value = pair[1] ? decodeURIComponent(pair[1].replace(/\+/g, ' ')) : '';
                params[key] = value;
            }
        }
        
        return params;
    }
    
    // Override the global function
    window.reign_nouveau_deParams = safeReignNouveauDeParams;
    
    // Fix for the specific ajaxComplete handlers in Reign theme
    $(document).ready(function() {
        // Create a single consolidated handler for all theme functionality
        $(document).on('ajaxComplete.reignTheme', function(event, xhr, settings) {
            try {
                // Consolidated rtMedia fix
                $('.rtmedia-activity-text > span').each(function() {
                    $(this).filter(function() {
                        return $.trim($(this).text()) === '' && $(this).children().length === 0;
                    }).remove();
                });
                
                // Consolidated video player fixes
                if ($('.ld-video iframe').length > 0) {
                    $('.ld-video iframe').addClass('fitvidsignore');
                }
                
                if ($('.tutor-video-player iframe').length > 0) {
                    $('.tutor-video-player iframe').addClass('fitvidsignore');
                }
                
                $('body.buddypress').fitVids();

            } catch (error) {
                console.warn('Reign theme ajaxComplete safely handled:', error);
            }
        });
        
        // Initial tooltip setup
        setTimeout(function() {
            $('.wbtm-member-directory-type-4 .action .generic-button').find('a').contents().wrap('<span/>');
            $('.wbtm-member-directory-type-4 .action .generic-button').find('button').contents().wrap('<span/>');
            $('.wbtm-group-directory-type-4 .action .generic-button').find('a').contents().wrap('<span/>');
            $('.wbtm-group-directory-type-4 .action .generic-button').find('button').contents().wrap('<span/>');
        });
        
        // Friendship button click handler
        $('.reign-members-grid-widget li.friendship-button > .friendship-button, .reign-groups-grid-widget .generic-button .group-button').on('click', function() {
            var redirect_url = $(this).attr('data-bp-nonce');
            window.location.href = redirect_url;
        });
        
        // Single vertical nav check
        if ($('#buddypress').hasClass('bp-single-vert-nav')) {
            $('.rg-nouveau-sidebar-menu').removeClass('reign-nav-swipe');
            $('.rg-nouveau-sidebar-menu').removeClass('reign-nav-more');
        }
    });
    
})(jQuery, window);

jQuery(window).on("load", function() {
    jQuery('.rg-nouveau-sidebar-menu.reign-nav-more .main-navs:not(.vertical) ul').each(function() {
        jQuery('body').addClass('rg-more-loaded');
    });
});

// BP Better Messages
jQuery(document).ready(function() {
    jQuery('.bp-better-messages-mini').insertAfter('.bp-better-messages-list');
});

// Password Visibility Toggle Script For Login/Register Popup
document.addEventListener("DOMContentLoaded", function() {
    const togglePassword = document.querySelector(".registration-login-form .wp-hide-pw");
    const passwordInput = document.querySelector(".registration-login-form .password-entry");

    if (!togglePassword || !passwordInput) {
        return; // Stop execution if elements are not found
    }

    const icon = togglePassword.querySelector(".registration-login-form .wp-hide-pw .dashicons");

    togglePassword.addEventListener("click", function() {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("dashicons-hidden");
            icon.classList.add("dashicons-visibility");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("dashicons-visibility");
            icon.classList.add("dashicons-hidden");
        }
    });
});
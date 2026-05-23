jQuery(function() {
    jQuery(".wb-xprofile-social-links-wrapper").sortable();
    jQuery(".wb-xprofile-social-links-wrapper")
        .accordion({
            header: "h3",
            heightStyle: "content",
            collapsible: true,
            active: true
        })
        .sortable({
            axis: "y",
            handle: ".wbtm_social_link_section > h3",
            stop: function(event, ui) {
                // IE doesn't register the blur when sorting
                // so trigger focusout handlers to remove .ui-state-focus
                ui.item.children("span").triggerHandler("focusout");
                // Refresh accordion to handle new order
                jQuery(this).accordion("refresh");
            }
        });
});

jQuery(document).ready(function() {

    var ques_header_fix_html = '<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>';

    function updateSocialLinkHeader($input) {
        var $header = $input.parent().parent().prev();
        var value = $input.val() || reign_vertical_tabs_skeleton_js_params.wb_social_links_default;

        $header.empty();
        $header.append(jQuery(ques_header_fix_html));
        $header.append(document.createTextNode(value));
    }

    /* managing change in header as question changes */
    jQuery(document.body).on('change keyup paste', 'div.wb-xprofile-social-links-wrapper div.name_section input', function() {
        updateSocialLinkHeader(jQuery(this));
    });

    jQuery(document.body).on('click', 'div.del_section button', function(event) {
        event.preventDefault();
        event.stopPropagation();
        jQuery(this).parent().parent().parent().remove();
        var $accordion = jQuery(".wb-xprofile-social-links-wrapper").accordion();
        $accordion.accordion("refresh");

    });

    jQuery(document.body).on('click', 'div.wbtm_social_links_add_more button', function(event) {
        event.preventDefault();
        //event.stopPropagation();
        var date = new Date();
        var timestamp = date.getTime();
        var html_to_append = reign_vertical_tabs_skeleton_js_params.wb_social_links_html;
        html_to_append = html_to_append.replace(/{{unique_key}}/g, timestamp);
        // jQuery( this ).parent().prev().append( html_to_append );
        jQuery('.wb-xprofile-social-links-wrapper').append(html_to_append);
        jQuery('.wb-xprofile-social-links-wrapper').accordion("refresh");
        jQuery(".wb-xprofile-social-links-wrapper").sortable();
        //jQuery( ".wbtm_social_links_container" ).accordion('refresh');
    });

    jQuery(document).on('click', '#reign-theme-options-submit', function(e) {
        var $accordion = jQuery(".wb-xprofile-social-links-wrapper").accordion();
        if (jQuery('#xprofile_social_links').is(':visible')) {
            jQuery(".wbtm-social-link-inp").each(function(index, element) {
                if (jQuery(this).val() == '') {
                    var acc_handle = jQuery(this).parents().find('.wbtm_social_links_container');
                    $accordion.accordion("option", "active", $accordion.index(acc_handle));
                }
            });
        }
    });
});


function openSettingsTab(evt, settingSlug) {

    evt.preventDefault();

    // Get all elements with class="tablinks" and remove the class "active"
    jQuery('button.reign-tablinks').removeClass('active');
    jQuery('button.reign-tablinks.' + settingSlug).addClass('active');

    // Show the current tab, and add an "active" class to the link that opened the tab
    jQuery("div.reign-tabcontent").each(function(index, element) {
        if (jQuery(this).attr('id') == settingSlug) {
            jQuery('input#render_theme_setting_current_tab').val(index);
            jQuery(this).show();
        } else {
            jQuery(this).hide();
        }
    });

}

jQuery(document).ready(function($) {
    // Get all elements with class="tabcontent" and hide them
    var tab_to_open = $('input#render_theme_setting_current_tab').val();
    tab_to_open = parseInt(tab_to_open);
    $('div.reign-tabcontent').hide();
    $('div.reign-tabcontent').eq(tab_to_open).show();

    $('div#rg-poststuff form').show();
    $('div.reign-animation-container').hide();

    //rtm tooltip
    $(".rtm-tooltiptext").hide();
    $(".rtm-tooltip-wrap").on('click', function(e) {
        var $div = $(this).next('.rtm-tooltiptext');
        $(".rtm-tooltiptext").not($div).hide();
        if ($div.is(":visible")) {
            $div.hide();
        } else {
            $div.show();
        }
    });
});

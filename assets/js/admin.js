//Admin js

jQuery(document).ready(function($) {

    var reign_mediaUploader, reign_thisRef;

    $(document.body).on('click', '.reign-upload-button', function(event) {
        event.preventDefault();

        reign_thisRef = $(this);
        // If the uploader object has already been created, reopen the dialog
        if (reign_mediaUploader) {
            reign_mediaUploader.open();
            return;
        }
        // Extend the wp.media object
        reign_mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        // When a file is selected, grab the URL and set it as the text field's value
        reign_mediaUploader.on('select', function() {
            var attachment = reign_mediaUploader.state().get('selection').first().toJSON();
            reign_thisRef.siblings('.reign_default_cover_image_url').val(attachment.url);
            reign_thisRef.siblings('.reign_default_cover_image').attr('src', attachment.url);
            reign_thisRef.siblings('.reign_default_cover_image').show();
            reign_thisRef.siblings('.reign-remove-file-button').show();

            reign_thisRef.siblings('#avatar_default_image').val(attachment.url);
            reign_thisRef.siblings('#avatar_default_image_id').val(attachment.id);

            reign_thisRef.siblings('#group_default_image').val(attachment.url);
            reign_thisRef.siblings('#group_default_image_id').val(attachment.id);
        });
        // Open the uploader dialog
        reign_mediaUploader.open();
    });

    $(document.body).on('click', '.reign-remove-file-button', function(event) {
        event.preventDefault();
        $(this).siblings('.reign_default_cover_image_url').val('');
        $(this).siblings('.reign_default_cover_image').attr('src', '');
        $(this).siblings('.reign_default_cover_image').hide();
        $(this).hide();

        $(this).siblings('#avatar_default_image').val('');
        $(this).siblings('#avatar_default_image_id').val('');

        $(this).siblings('#group_default_image').val('');
        $(this).siblings('#group_default_image_id').val('');
    });

    $( '#reign-settings-tabs' ).each( function() {
        $("#reign-settings-tabs").tabs();
    } );

    $(document).on("click", ".reign_add_row_image", function(e) {
        e.preventDefault();

        const tr = $(this).closest("tr");
        const trHTML = tr.clone();
        trHTML.find("input").val("");
        trHTML.find("img").attr("src", "");
        trHTML.find(".reign_dark_mode_delete_img").addClass("hidden");

        tr.after(trHTML);
    });

    $(document).on("click", ".reign_remove_row_image", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
    });

    $(document).on("click", ".reign_dark_mode_select_img", function(e) {
        e.preventDefault();
        const input = $(this).siblings("input");
        const img = $(this).siblings("img");
        const delete_img = $(this).siblings(".reign_dark_mode_delete_img");

        // Create the media frame.
        const file_frame = (wp.media.frames.file_frame = wp.media({
            title: "Select image",
            button: {
                text: "Select this image",
            },
            multiple: false,
        }));

        file_frame.on("select", function() {
            const attachment = file_frame.state().get("selection").first().toJSON();
            input.val(attachment.url).change();
            img.attr("src", attachment.url);
            delete_img.removeClass("hidden");
        });

        // Finally, open the modal
        file_frame.open();
    });


    $(document).on("click", ".reign_dark_mode_delete_img", function(e) {
        e.preventDefault();

        const input = $(this).siblings("input");
        const img = $(this).siblings("img");

        input.val("").change();
        img.attr("src", "");

        $(this).addClass("hidden");
    });

    $(document).on("change paste keyup", ".image-settings-table input", function(e) {
        const img = $(this).siblings("img");
        const delete_img = $(this).siblings(".reign_dark_mode_delete_img");

        const val = $(this).val();
        if (val) {
            img.attr("src", val);
            delete_img.removeClass("hidden");
        } else {
            img.attr("src", "");
            delete_img.addClass("hidden");
        }
    });


    //Reign range slider
    const allRanges = document.querySelectorAll(".reign-slidecontainer");
    allRanges.forEach(wrap => {
        const range = wrap.querySelector(".reign-slider");
        const bubble = wrap.querySelector(".reign-slider-value");

        range.addEventListener("input", () => {
            setBubble(range, bubble);
        });
        setBubble(range, bubble);
    });

    function setBubble(range, bubble) {
        const val = range.value;
        const min = range.min ? range.min : 0;
        const max = range.max ? range.max : 100;
        const newVal = Number(((val - min) * 100) / (max - min));
        bubble.innerHTML = val;

        // Sorta magic numbers based on size of the native UI thumb
        bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
    }

});

// Reign setting loader JS
jQuery(window).on("load", function() {
    setTimeout(function(){
        jQuery('.reign-settings-loader').fadeOut('slow');
    },500);
});

// Reign settings menu toggle in mobile view JS
jQuery(document).ready(function($) {	
    var btn = document.querySelector('.reign-setting-menu-toggle');
    if (!btn) {
        return;
    }
    var navTabs = document.querySelector('.reign-setting-left-panel .reign-nav-tab-wrapper'); 
    var btnst = true;

    btn.onclick = function() {
        var span = document.querySelector('.reign-setting-menu-toggle span');
        
        if (btnst) {
            span.classList.add('toggle');
            navTabs.style.height = navTabs.scrollHeight + 'px';
            btnst = false;
        } else {
            span.classList.remove('toggle');
            navTabs.style.height = '0';
            btnst = true;
        }
    };
    
    navTabs.addEventListener('transitionend', function() {
        if (btnst) {
            navTabs.style.opacity = '0';
        } else {
            navTabs.style.opacity = '1';
        }
    });
});

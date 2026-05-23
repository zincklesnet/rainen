(function($){
// for making any tab as default tab for testing and working nicely after refresh.
//     setTimeout(function () {
//         $('a[href="#editTemplates"]').trigger('click');
//     }, 200);

    // send user to the right tab based on the query string or url
    const QS = window.location.search;
    if(QS.indexOf('allPages') >= 0){
        setTimeout(function () {
            $('a[href="#allPages"]').trigger('click');
        }, 150);
    }else if (QS.indexOf('editTemplates') >= 0) {
        setTimeout(function () {
            $('a[href="#editTemplates"]').trigger('click');
        }, 150);
    }else if (QS.indexOf('home') >= 0) {
        setTimeout(function () {
            $('a[href="#home"]').trigger('click');
        }, 150);
    }else if (QS.indexOf('createLegalPage') >= 0) {
        setTimeout(function () {
            $('a[href="#createLegalPage"]').trigger('click');
        }, 150);
    }else if (QS.indexOf('support') >= 0) {
        setTimeout(function () {
            $('a[href="#Support"]').trigger('click');
        }, 150);
    }

    // tooltips
    $(function(){
        $('a[title]').tooltip();
    });

    // home tab's tab
    $(".btn-pref .btn").click(function () {
        $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
        // $(".tab").addClass("active"); // instead of this do the below
        $(this).removeClass("btn-default").addClass("btn-primary");
    });

    // notification close button functionality
    $(document).on('click', '#adl_close_it', function (e) {
        $(this).closest('div').hide();
    });



    /*
     * GENERAL INFORMATION TAB
     *
     */
    // save general data of user site
    $('#adl_lp_g_settings').on('click', function(e){
        e.preventDefault();
        var form = $("#adl_lp_gs_form");
        var formData =  form.serialize();
        $("#successResult").remove();

        adlAjaxHandler( form, 'general_info_handler', formData, function(data){
            if(data === 'success') {
                autoCLoseMessage('Success: Data saved. <span id="adl_close_it">X</span>', 2000);
            }else{
                autoCLoseMessage('Error: Something went wrong. <span id="adl_close_it">X</span>', 2000);
                // for debugging only: add this : <pre>'+data+'</pre> below
                // $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(form);

            }
        });
    });


    // Reset general data in the FORM
    $('#adl_lp_g_settings_reset').on('click', function(e){
        e.preventDefault();
        resetForm($("#adl_lp_gs_form"));

    });


    // Reset general data in the DATABASE
    $('#adl_lp_g_settings_resetdb').on('click', function(e){
        e.preventDefault();
        var form = $("#adl_lp_gs_form");
        var formData =  form.serialize();

        $("#successResult").remove();

        // get submitted from data and serialize them and send them to the ajax handler

        var iconBindingElement = jQuery('#adl_ajax_loader');
        adlAjaxHandler( iconBindingElement, 'reset_general_info_handler', formData, function(data){
            resetForm(form);
            if(data === 'success') {
                autoCLoseMessage('Success: Data has been reset. <span id="adl_close_it">X</span>', 3000);
            }else{
                // for debugging only: add this : <pre>'+data+'</pre> below
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong</p><pre>'+data+'</pre></div>').insertAfter(form);

            }
        });
    });





    /*
     * SOCIAL INFORMATION
     *
     */

    // save social data into the database
    $('#social_info_submit').on('click', function(e){

        e.preventDefault();
        var form = $("#socialInfoForm");
        var formData =  form.serialize();
        $("#successResult").remove();
        // get submitted from data and serialize them and send them to the ajax handler
        adlAjaxHandler( form, 'social_info_handler', formData, function(data){

            if(data === 'success') {
                autoCLoseMessage('Success: Data saved. <span id="adl_close_it">X</span>', 2000);
            }else{
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(form);

            }
        });

    });


    // Reset social data in the FORM inputs
    $('#adl_lp_s_settings_reset').on('click', function(e){
        e.preventDefault();
        resetForm($("#socialInfoForm"));// reset the form inputs
        $("#successResult").remove();// remove if any messaging div is shown.

    });


    // Reset social data in the database
    $('#adl_lp_s_settings_resetdb').on('click', function(e){
        e.preventDefault();
        var form = $("#socialInfoForm");
        var formData =  form.serialize();

        $("#successResult").remove();

        adlAjaxHandler( form, 'reset_social_info_handler', formData, function(data){
            resetForm(form);
            if(data === 'success') {
                autoCLoseMessage('Success: Data saved. <span id="adl_close_it">X</span>', 2000);
            }else{
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong</p><pre>'+data+'</pre></div>').insertAfter(form);

            }
        });
    });


    /*
     * Popup Settings
     *
     */

    // save social data into the database
    $('#adl_lp_pop_settings').on('click', function(e){

        e.preventDefault();
        var form = $("#adl_lp_popup_form");
        var formData =  form.serialize();
        $("#successResult").remove();
        // get submitted from data and serialize them and send them to the ajax handler
        adlAjaxHandler( form, 'popup_opt_handler', formData, function(data){

            if(data === 'success') {
                autoCLoseMessage('Success: Data saved. <span id="adl_close_it">X</span>', 2000);
            }else{
                // for debugging only: add this : <pre>'+data+'</pre> below
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(form);

            }
        });

    });


    // Reset adl_lp_popup_form data in the FORM inputs
    $('#adl_lp_pop_settings_reset').on('click', function(e){
        e.preventDefault();
        resetForm($("#adl_lp_popup_form"));// reset the form inputs
        $("#successResult").remove();// remove if any messaging div is shown.

    });


    // Reset popup data in the database
    $('#adl_lp_pop_settings_resetdb').on('click', function(e){
        e.preventDefault();
        var form = $("#adl_lp_popup_form");
        var formData =  form.serialize();
        $("#successResult").remove();
        adlAjaxHandler( form, 'reset_popup_opt_handler', formData, function(data){

            if(data === 'success') {
                resetForm(form);
                autoCLoseMessage('Success: Popup options have been reset. <span id="adl_close_it">X</span>', 2000);
            }else{
                // for debugging only: add this : <pre>'+data+'</pre> below
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong</p><pre>'+data+'</pre></div>').insertAfter(form);

            }
        });
    });




    /*
     * Cookie Settings
     *
     */
    // save Miscellaneous data into the database
    $('#cookie_setting_submit').on('click', function(e){

        e.preventDefault();
        var form = $("#cookie_form");
        var formData =  form.serialize();
        $("#successResult").remove();
        // get submitted from data and serialize them and send them to the ajax handler
        adlAjaxHandler( form, 'cookie_info_handler', formData, function(data){

            if(data === 'success') {
                autoCLoseMessage('Success: Data saved. <span id="adl_close_it">X</span>', 2000)
            }else{
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(form);

            }
        });

    });




    /*
     * Miscellaneous
     *
     */
    // save Miscellaneous data into the database
    $('#misc_setting_submit').on('click', function(e){

        e.preventDefault();
        var form = $("#misc_form");
        var formData =  form.serialize();
        $("#successResult").remove();
        // get submitted from data and serialize them and send them to the ajax handler
        adlAjaxHandler( form, 'misc_info_handler', formData, function(data){

            if(data === 'success') {
                autoCLoseMessage('Success: Data saved. <span id="adl_close_it">X</span>', 2000)
            }else{
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(form);

            }
        });

    });

    $('.lp-color-picker').wpColorPicker();














    /*
     *
     * CREATE LEGAL PAGES TAB's JS CODES GO HERE
     *
     * */
    //Show template list based on selected types
    $('#adl_lp_type').change(function (e) {
        var chooseTemplate = $('#ChooseTemplate');
        var formData = $("#showTemplateTypeForm").serialize();
        $("#successResult").remove();

        adlAjaxHandler( chooseTemplate, 'showTemplate_type_handler', formData, function(data){

            if(data !== 'success') {
                autoCLoseMessage('Template list updated successfully. <span id="adl_close_it">X</span>', 2000);
                chooseTemplate.html('<p>Choose a Template:</p>'+data);
            }else{
                chooseTemplate.html('');
                autoCLoseMessage('<p class="notice notice-error">ERROR: something went wrong</p> <span id="adl_close_it"></span>', 3000);


            }
        });

    });



    // modify the tinyMCE content upon selection of the template
    $('#ChooseTemplate').on('click', 'a', function (e) {
        const form = $("#showTemplateTypeForm");
        e.preventDefault();
        // var type =  data +'&' + $(this).data('type');
        const id = 'template_id=' + $(this).data('id');
        const lp_title = $('#lp_title');
        // console.log(id);
        $("#successResult").remove();
        // get submitted from data and serialize them and send them to the ajax handler

        //var iconBindingElement = jQuery('#adl_ajax_loader');
        adlAjaxHandler( form, 'fetch_and_insert_template_data', id, function(data){
            if(!tinyMCE.activeEditor)jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

            if(data !== 'error') {
                var jsn = isJson(data);
                if(jsn){
                    var parsedData = JSON.parse(data);
                    lp_title.val(parsedData[0]);
                    tinyMCE.activeEditor.setContent(parsedData[1]);
                }
                autoCLoseMessage('Page Content Updated Successfully. <span id="adl_close_it">X</span>', 2000);
            } else {
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(form);

            }
        });
    });


    //Save Legal Page to the Database and modify the tinyMCE content to let user edit option.
    const cbtn = $('#addNewLegalPage');
    cbtn.on('submit', function(e){
        if(!tinyMCE.activeEditor) jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

        const data = $(this).serialize() +'&content=' +tinyMCE.activeEditor.getContent({format : 'html'}); // get all forms field and then get modified tinymce content and add that to the serialized strings.
        const lp_title = $('#lp_title');
        $('#successResult').remove();

        e.preventDefault();
        adlAjaxHandler(cbtn, 'addNewLegalPage', data, function (data) {
            if(!tinyMCE.activeEditor)jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

            if(data !== 'error') {
                var jsn = isJson(data);// check if the data is in JSON format.
                if(jsn){
                    var parsedData = JSON.parse(data); // parsed JSON Object retuned from the database.
                }
                const msg = 'Page Created Successfully. You can view and edit page as normal page under WordPress Pages menu</br>'+parsedData[0] +'   ||   ' +parsedData[1]+'<span id="adl_close_it">X</span>';
                autoCLoseMessage(msg, 8000);

            } else {
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(cbtn);

            }
        });

    });




    //Save Popup page to the Database and modify the tinyMCE content to let user edit option.
    const PopupBtn = $('#addNewPopup');
    PopupBtn.on('submit', function(e){
        if(!tinyMCE.activeEditor) jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

        const data = $(this).serialize() +'&content=' +tinyMCE.activeEditor.getContent({format : 'html'}); // get all forms field and then get modified tinymce content and add that to the serialized strings.
        const lp_title = $('#lp_title');
        $('#successResult').remove();

        e.preventDefault();
        adlAjaxHandler(PopupBtn, 'addNewPopup', data, function (data) {
            if(!tinyMCE.activeEditor)jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

            if(data !== 'error') {
                const msg = 'Popup created successfully. You can see all your popups under "All Popups" menu<span id="adl_close_it">X</span>';
                autoCLoseMessage(msg, 8000);

            } else {
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(PopupBtn);

            }
        });

    });

    // EDIT popup
    const editPopup = $('#editPopup');
    editPopup.on('submit', function(e){
        if(!tinyMCE.activeEditor) jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

        var data = $(this).serialize();
        data += '&content=' +tinyMCE.activeEditor.getContent({format : 'html'})
        data += '&id='+editPopup.data('id'); // get all forms field and then get modified tinymce adl_lp_template and add that to the serialized strings.
        const lp_title = $('#lp_title');
        $('#successResult').remove();

        e.preventDefault();
        adlAjaxHandler(editPopup, 'editPopup', data, function (data) {
            // if(!tinyMCE.activeEditor)jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

            if(data === 'success') {
                const msg = 'Popup has been updated Successfully. </br><span id="adl_close_it">X</span>';
                autoCLoseMessage(msg, 10000);
            } else {
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(editPopup);

            }
        });
    });

    // DELETE pop
    $(document).on('click', 'a.deleteLegalPopup', function (e) {
        e.preventDefault();
        const $this = $(this);
        const container = $('#adl_legal_popup_container');
        const postID = '&id='+$this.data('id'); // get the pop id from the link data-id
        // console.dir($this.closest('tr'));
        $("#successResult").remove();

        adlAjaxHandler( container, 'deleteLegalPopup', postID, function(data){
            if(data === 'success') {
                autoCLoseMessage('The Popup has been deleted Successfully<span id="adl_close_it">X</span>', 3000);
                $this.closest('tr').fadeOut();

            }else{
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(container);
            }
        });
    });


    /*
     * CODES FOR ALL LEGAL PAGES
     * 
     * */

    // Refresh the page on user click on refresh button
    $('#refreshPage').on('click', function (e) {
        e.preventDefault();
        location.reload();
    });



// move the page to the trash on user click on trash icon. NEXT ADD CONFIRM AND USE SWEET ALERT JS LIBRARY
    $(document).on('click', '.moveToTrash', function (e) {
        e.preventDefault();
        const $this = $(this);
        const container = $('#legalPageContainer');
        const postID = '&post_id='+$this.data('id');
        // console.dir($this.closest('tr'));
        $("#successResult").remove();

        adlAjaxHandler( container, 'moveToTrash', postID, function(data){

            if(data === 'success') {
                autoCLoseMessage('Page has been moved to the Trash Successfully<span id="adl_close_it">X</span>', 3000);
                $this.closest('tr').fadeOut();

            }else{
                // for debugging only: add this : <pre>'+data+'</pre> below
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(container);

            }
        });
    });




    // go to create legal page's tab on clicking on a button
    $('#CreateALegalPage').on('click', function (e) {
        e.preventDefault();
        $('a[href="#createLegalPage"]').trigger('click');
    });








    /*
    * CODES FOR CREATE LEGAL PAGE TEMPLATE PAGE
    *
    * */

    //Save Legal Page Template to the Database
    const create_lp_template = $('#addNewLegalTemplate');
    create_lp_template.on('submit', function(e){
        if(!tinyMCE.activeEditor) jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

        const data = $(this).serialize() +'&adl_lp_template=' +tinyMCE.activeEditor.getContent({format : 'html'}); // get all forms field and then get modified tinymce adl_lp_template and add that to the serialized strings.
        const lp_title = $('#lp_title');
        $('#successResult').remove();

        e.preventDefault();
        adlAjaxHandler(create_lp_template, 'addNewLegalTemplate', data, function (data) {
            // if(!tinyMCE.activeEditor)jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

            if(data === 'success') {
                const msg = 'Legal Page Template has been created Successfully. You can view and edit on All Legal Page Tab under Legal Pages menu</br><span id="adl_close_it">X</span>';
                autoCLoseMessage(msg, 10000);
            } else {
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(create_lp_template);

            }
        });

    }); 
    
    
    //UPDATE Legal Page Template to the Database
    const edit_lp_template = $('#editLegalTemplate');
    edit_lp_template.on('submit', function(e){
        if(!tinyMCE.activeEditor) jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

        var data = $(this).serialize();
            data += '&adl_lp_template=' +tinyMCE.activeEditor.getContent({format : 'html'})
            data += '&id='+edit_lp_template.data('id'); // get all forms field and then get modified tinymce adl_lp_template and add that to the serialized strings.
            data += '&type='+edit_lp_template.data('type');
        const lp_title = $('#lp_title');
        $('#successResult').remove();

        e.preventDefault();
        adlAjaxHandler(edit_lp_template, 'editLegalTemplate', data, function (data) {
            // if(!tinyMCE.activeEditor)jQuery('.wp-editor-wrap .switch-tmce').trigger('click');

            if(data === 'success') {
                const msg = 'Legal Page Template has been created Successfully. You can view and edit on All Legal Page Tab under Legal Pages menu</br><span id="adl_close_it">X</span>';
                autoCLoseMessage(msg, 10000);
            } else {
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(edit_lp_template);

            }
        });
    });



    //DELETE the Legal Page Template on user click on trash icon. @TODO; NEXT ADD CONFIRM AND USE SWEET ALERT JS LIBRARY
    $(document).on('click', 'a.deleteLegalTemplate', function (e) {
        e.preventDefault();
        const $this = $(this);
        const container = $('#adl_legal_template_container');
        const postID = '&template_id='+$this.data('id');
        // console.dir($this.closest('tr'));
        $("#successResult").remove();

        adlAjaxHandler( container, 'deleteLegalTemplate', postID, function(data){
            if(data === 'success') {
                autoCLoseMessage('The Page Template has been deleted Successfully<span id="adl_close_it">X</span>', 3000);
                $this.closest('tr').fadeOut();

            }else{
                $('<div class="notice notice-error is-dismissible" id="successResult"><p>Error: Something went wrong.<pre>'+data+'</pre></p></div>').insertAfter(container);
            }
        });
    });



    /*
     * HELPER FUNCTIONS
     * =====================================
     * */

    /*
     * It sets a cookie by adding together the cookie name, the cookie value, and the expires string.
     * @param string cname  Name of the cookie
     * @param string cvalue  Value of the cookie
     * @param integer exdays  Expiration of the cookie
     * */
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }


    /*
     * It gets a cookie by a name given as an argument
     * @param string cname  Name of the cookie
     * */
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
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

    /*
     * It returns true if a cookie exits and otherwise false.
     * @param string cname  Name of the cookie
     * */
    function checkCookie(cname) {
        var result = getCookie(cname);
        return (result !== "");

    }

    /*
     * It deletes a given cookie
     * @param string cname  Name of the cookie
     * */
    function deleteCookie(cname) {
        document.cookie = cname + "; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    }



    /*
     * It resets the form input fields
     * */
    function resetForm($form) {
        $form.find('input:text, input:password, input:file, select, textarea').val('');
        $form.find('input:radio, input:checkbox')
            .removeAttr('checked').removeAttr('selected');
    }

    /*
     * It returns true if a given argument is JSON and returns false otherwise
     * */
    function isJson(item) {
        item = typeof item !== "string"
            ? JSON.stringify(item)
            : item;

        try {
            item = JSON.parse(item);
        } catch (e) {
            return false;
        }

        return  (typeof item === "object" && item !== null); // return true or false based on the condition
    }

    /*
     * It sends Ajax Request to the WordPress while in the admin area
     * */
    function adlAjaxHandler( ElementToShowLoadingIconAfter, ActionName, arg, CallBackHandler){

        if(ActionName) data = "action=" + ActionName;
        if(arg)    data = arg + "&action=" + ActionName;
        if(arg && !ActionName) data = arg;
        data = data ;

        var n = data.search(adl_lp_obj.nonceAction);
        if(n<0){
            data = data + "&"+adl_lp_obj.nonceAction+"=" + adl_lp_obj.nonce;
        }

        jQuery.ajax({
            type: "post",
            url: ajaxurl,
            data: data,
            beforeSend: function() { jQuery("<span class='adl_lp_ajax_loading'></span>").insertAfter(ElementToShowLoadingIconAfter); },
            success: function( data ){
                jQuery(".adl_lp_ajax_loading").remove();
                CallBackHandler(data);
            }
        });
    }




    /*
     * It shows an auto closing notification message to the browser
     * */
    function autoCLoseMessage(msg,duration)
    {
        var el = document.createElement("div");
        // el.setAttribute('class', 'notice notice-success');
        el.setAttribute('id', 'adl-lp-notification');
        el.innerHTML = msg;
        setTimeout(function(){
            el.parentNode.removeChild(el);
        },duration);
        document.body.appendChild(el);
    }


})(jQuery);

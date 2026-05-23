/**
 * Reign Magic Link Support for iThemes Security Passwordless Login
 */
jQuery(document).ready(function($) {
    'use strict';

    // Handle magic link button click
    $(document).on('click', '[data-action="reign-send-magic-link"]', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $form = $button.closest('.reign-sign-form');
        var $messages = $form.find('.reign-sign-form-messages');
        var $usernameField = $form.find('input[name="itsec_magic_link_username"]');
        var nonce = $form.find('input[name="_ajax_nonce"]').val();

        // Get username/email
        var username = $usernameField.val();

        if (!username) {
            $messages.html('<li>' + reign_magic_link.i18n.enter_username + '</li>').show();
            return;
        }

        // Clear previous messages
        $messages.hide().empty();

        // Disable button and show loading
        $button.prop('disabled', true);
        var originalText = $button.text();
        $button.html('<span class="icon-loader"></span> ' + reign_magic_link.i18n.sending);

        // Send AJAX request
        $.ajax({
            url: reign_magic_link.ajax_url,
            type: 'POST',
            data: {
                action: 'reign-send-magic-link',
                itsec_magic_link_username: username,
                _ajax_nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $messages.removeClass('woocommerce-error').addClass('woocommerce-message');
                    $messages.html('<li>' + response.data.message + '</li>').show();

                    // Clear the field
                    $usernameField.val('');

                    // Hide the message after 10 seconds
                    setTimeout(function() {
                        $messages.fadeOut();
                    }, 10000);
                } else {
                    // Show error message
                    $messages.removeClass('woocommerce-message').addClass('woocommerce-error');
                    $messages.html('<li>' + response.data.message + '</li>').show();
                }
            },
            error: function() {
                $messages.removeClass('woocommerce-message').addClass('woocommerce-error');
                $messages.html('<li>' + reign_magic_link.i18n.error + '</li>').show();
            },
            complete: function() {
                // Re-enable button and restore text
                $button.prop('disabled', false).html(originalText);
            }
        });
    });

    // Toggle between magic link and password login
    $(document).on('click', '.reign-toggle-login-method', function(e) {
        e.preventDefault();

        var $form = $(this).closest('.reign-sign-form');
        var $magicSection = $form.find('.reign-magic-link-section');
        var $passwordSection = $form.find('.reign-password-section');
        var $allToggleLinks = $form.find('.reign-toggle-login-method');

        if ($magicSection.is(':visible')) {
            // Switch to password login
            $magicSection.slideUp(300);
            $passwordSection.slideDown(300);
            $allToggleLinks.text(reign_magic_link.i18n.email_login_link);
        } else {
            // Switch to magic link login
            $passwordSection.slideUp(300);
            $magicSection.slideDown(300);
            $allToggleLinks.text(reign_magic_link.i18n.use_password_instead);
        }
    });
});
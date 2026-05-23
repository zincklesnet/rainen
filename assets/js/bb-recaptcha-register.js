/* global bbRegisterRecaptcha, grecaptcha */
(function ($) {
	var BB_Register_Recaptcha = {

		init: function () {
			this.bbRegisterRecaptchaData = 'undefined' !== typeof bbRegisterRecaptcha && 'undefined' !== typeof bbRegisterRecaptcha.data ? bbRegisterRecaptcha.data : '';
			this.bbRegisterRecaptchaAction = this.bbRegisterRecaptchaData && 'undefined' !== typeof this.bbRegisterRecaptchaData.action ? this.bbRegisterRecaptchaData.action : '';
			this.bbRegisterRecaptchaVersion = this.bbRegisterRecaptchaData && 'undefined' !== typeof this.bbRegisterRecaptchaData.selected_version ? this.bbRegisterRecaptchaData.selected_version : '';
			this.setupGlobals();
		},

		setupGlobals: function () {
			if (this.bbRegisterRecaptchaAction) {
				var action = this.bbRegisterRecaptchaAction;
				var container = false;
				container = 'signup-form';

				if ('recaptcha_v3' === this.bbRegisterRecaptchaVersion) {
					grecaptcha.ready(
						function () {
							grecaptcha.execute(bbRegisterRecaptcha.data.site_key, { action: action }).then(
								function (token) {
									$('#bb_register_recaptcha_response_id').val(token);
								}
							);
						}
					);
				}
				if (
					'recaptcha_v2' === this.bbRegisterRecaptchaVersion &&
					'undefined' !== typeof this.bbRegisterRecaptchaData.v2_option
				) {
					if ('v2_checkbox' === this.bbRegisterRecaptchaData.v2_option) {
						grecaptcha.ready(
							function () {
								var params = {
									'sitekey': bbRegisterRecaptcha.data.site_key,
									'theme': bbRegisterRecaptcha.data.v2_theme,
									'size': bbRegisterRecaptcha.data.v2_size
								};

								grecaptcha.render('reign_recaptcha_v2_element', params);
							}
						);
					}
					if ('v2_invisible_badge' === this.bbRegisterRecaptchaData.v2_option) {
						grecaptcha.ready(function () {
							var form = $('#' + container);
							var params = {
								'sitekey': bbRegisterRecaptcha.data.site_key,
								'tabindex': 9999,
								'badge': bbRegisterRecaptcha.data.v2_badge_position,
								'size': 'invisible',
								'callback': function (token) {
									$('.g-recaptcha-response').val(token);
									if (container) {
										form.find('input[data-click]').trigger('click');
									}
								},
							};

							var loginV2 = grecaptcha.render('reign_recaptcha_v2_element', params);
							if (form.length) {
								form.on('submit', function (e) {
									if ('' == form.find('.g-recaptcha-response').val()) {
										e.preventDefault();
										e.stopImmediatePropagation();
										grecaptcha.execute(loginV2);
									}
								}).find('input:submit, button').on('click', function (e) {
									if ('' == form.find('.g-recaptcha-response').val()) {
										form.find('input:submit').attr('data-click', 'bb_recaptcha_submit');
										e.preventDefault();
										e.stopImmediatePropagation();
										grecaptcha.execute(loginV2);
									}
								});
							}
						});
					}
				}
			}
		},
	};

	$(
		function () {
			BB_Register_Recaptcha.init();
		}
	);
})(jQuery);

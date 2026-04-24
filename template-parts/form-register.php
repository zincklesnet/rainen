<?php
/**
 * @var int    $rand
 * @var string $redirect_to
 * @var string $redirect
 * @var string $forms
 * @var string $login_descr
 */
extract( $args );

?>
<div class="title h6" role="heading" aria-level="2"><?php esc_html_e( 'Register in', 'reign' ); ?>&nbsp;<?php echo get_bloginfo( 'name' ); ?></div>
<?php
if ( class_exists( 'BuddyPress' ) ) {

	bp_get_template_part( 'members/reign-register' );

} else {
	?>
	<form data-handler="reign-signup-form" name="registerform" class="content reign-sign-form-register reign-sign-form" action="<?php echo esc_url( site_url( 'wp-login.php?action=register&type=internal', 'login_post' ) ); ?>" method="post">

		<input class="simple-input" type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
		<input class="simple-input" type="hidden" name="redirect" value="<?php echo esc_attr( $redirect ); ?>" />

		<input class="simple-input" type="hidden" value="<?php echo wp_create_nonce( 'reign-sign-form' ); ?>" name="_ajax_nonce" />

		<div class="reign-sign-form-register-fields">       

			<ul class="reign-sign-form-messages" id="reign-register-messages" aria-live="polite" aria-atomic="true"></ul>

			<div class="row">
				<div class="col col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">

					<?php do_action( 'reign_register_form_top' ); ?>
					
					<div class="form-group label-floating">
						<label class="control-label" for="reign-reg-first-name"><?php esc_html_e( 'First Name', 'reign' ); ?></label>
						<input id="reign-reg-first-name" class="form-control simple-input" name="first_name" type="text">
					</div>

					<div class="form-group label-floating">
						<label class="control-label" for="reign-reg-last-name"><?php esc_html_e( 'Last Name', 'reign' ); ?></label>
						<input id="reign-reg-last-name" class="form-control simple-input" name="last_name" type="text">
					</div>

					<div class="form-group label-floating">
						<label class="control-label" for="reign-reg-username"><?php esc_html_e( 'Username', 'reign' ); ?></label>
						<input id="reign-reg-username" type="text" name="user_login" class="form-control simple-input" size="20" aria-required="true" aria-describedby="reign-register-messages" />
					</div>

					<div class="form-group label-floating">
						<label class="control-label" for="reign-reg-email"><?php esc_html_e( 'Your Email', 'reign' ); ?></label>
						<input id="reign-reg-email" type="email" name="user_email" class="form-control simple-input" size="25" aria-required="true" aria-describedby="reign-register-messages" />
					</div>

					<div class="form-group label-floating password-eye-wrap">
						<label class="control-label" for="reign-reg-password"><?php esc_html_e( 'Your Password', 'reign' ); ?></label>
						<input id="reign-reg-password" type="password" name="user_password" class="form-control simple-input sign-form-password-verify" size="25" aria-required="true" />
						<button type="button" class="fa fa-fw fa-eye password-eye" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'reign' ); ?>"></button>
						<div class="sign-form-pass-strength-result"></div>
					</div>

					<div class="form-group label-floating password-eye-wrap">
						<label class="control-label" for="reign-reg-password-confirm"><?php esc_html_e( 'Confirm Password', 'reign' ); ?></label>
						<input id="reign-reg-password-confirm" type="password" name="user_password_confirm" class="form-control sign-form-password-verify-confirm" size="25" aria-required="true" />
						<button type="button" class="fa fa-fw fa-eye password-eye" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'reign' ); ?>"></button>
					</div>

					<?php do_action( 'reign_recaptcha_after_register_form' ); ?>

					<button type="submit" class="btn full-width registration-login-submit">
						<?php if ( function_exists( 'bp_is_active' ) && bp_get_option( 'bp-enable-membership-requests' ) ) { ?>
							<span><?php esc_html_e( 'Submit Request', 'reign' ); ?></span>
						<?php } else { ?>
							<span><?php esc_html_e( 'Complete Registration!', 'reign' ); ?></span>
						<?php } ?>
						<span class="icon-loader"></span>
					</button>

					<?php do_action( 'reign_register_form_bottom' ); ?>
				</div>
			</div>
		</div>
	</form>
	<?php
}

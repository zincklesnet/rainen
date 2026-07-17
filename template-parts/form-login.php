<?php
/**
 * @var int $rand
 * @var string $redirect_to
 * @var string $redirect
 * @var string $forms
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- $args is a controlled array passed by reign's form loader.
extract( $args );
$can_register = get_option( 'users_can_register' );

global $wbtm_reign_settings;
$registration_page_url = wp_registration_url();
if ( isset( $wbtm_reign_settings['reign_pages']['reign_register_page'] ) && ( '-1' !== $wbtm_reign_settings['reign_pages']['reign_register_page'] && '' !== $wbtm_reign_settings['reign_pages']['reign_register_page'] ) ) {
	$registration_page_id  = $wbtm_reign_settings['reign_pages']['reign_register_page'];
	$registration_page_url = get_permalink( $registration_page_id );
}
?>
<div class="title h6" role="heading" aria-level="2"><?php echo ( ! empty( $login_title ) ) ? wp_kses_post( $login_title ) : esc_html__( 'Login to your Account', 'reign' ); ?></div>

<form data-handler="reign-signin-form" class="content reign-sign-form-login reign-sign-form" method="POST" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">

	<input class="simple-input" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'reign-sign-form' ) ); ?>" name="_ajax_nonce" />

	<input class="simple-input" type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>"/>
	<input class="simple-input" type="hidden" name="redirect" value="<?php echo esc_attr( $redirect ); ?>"/>

	<?php do_action( 'reign_login_form_top' ); ?>

	<ul class="reign-sign-form-messages woocommerce-error" id="reign-login-messages" aria-live="polite" aria-atomic="true"></ul>

	<!-- 2FA Code Input Section (hidden by default) -->
	<div class="reign-2fa-section" style="display: none;">
		<div class="row">
			<div class="col">
				<p class="reign-2fa-message"><?php esc_html_e( 'Please enter the authentication code from your email:', 'reign' ); ?></p>

				<div class="form-group label-floating">
					<label class="control-label" for="reign-2fa-code"><?php esc_html_e( '2FA Code', 'reign' ); ?></label>
					<input type="text" id="reign-2fa-code" name="itsec_2fa_code" class="form-control simple-input" autocomplete="off">
				</div>

				<input type="hidden" name="itsec_2fa_user" value="">

				<button type="button" class="btn full-width reign-2fa-verify">
					<span><?php esc_html_e( 'Verify Code', 'reign' ); ?></span>
					<span class="icon-loader"></span>
				</button>

				<div style="margin-top: 15px; text-align: center;">
					<a href="#" class="reign-2fa-cancel">
						<?php esc_html_e( 'Cancel and try again', 'reign' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="row reign-login-fields">
		<div class="col">
			<?php
			// Add iThemes Security Passwordless Login (Magic Link) support
			if ( class_exists( 'ITSEC_Passwordless_Login' ) ||
				 ( class_exists( 'ITSEC_Modules' ) &&
				   method_exists( 'ITSEC_Modules', 'is_active' ) &&
				   ITSEC_Modules::is_active( 'passwordless-login' ) ) ) :
				?>

				<!-- Magic Link Section (hidden by default) -->
				<div class="itsec-pwls-login-wrap reign-magic-link-section" style="display: none;">
					<p class="itsec-pwls-login__title"><?php esc_html_e( 'Get a magic link sent to your email that will sign you in instantly!', 'reign' ); ?></p>

					<div class="form-group label-floating">
						<label class="control-label" for="itsec_magic_link_username_<?php echo esc_attr( $rand ); ?>">
							<?php esc_html_e( 'Username or Email Address', 'reign' ); ?>
						</label>
						<input type="text"
							   name="itsec_magic_link_username"
							   id="itsec_magic_link_username_<?php echo esc_attr( $rand ); ?>"
							   class="form-control simple-input">
					</div>

					<button class="btn full-width itsec-pwls-login__submit itsec-pwls-login__submit-magic"
							name="itsec_pwls_magic_login"
							type="button"
							data-action="reign-send-magic-link">
						<span><?php esc_html_e( 'Email Magic Link', 'reign' ); ?></span>
						<span class="icon-loader"></span>
					</button>

					<div class="itsec-pwls-login-fallback" style="margin-top: 15px; text-align: center;">
						<a href="#" class="reign-toggle-login-method">
							<?php esc_html_e( 'Login with password instead', 'reign' ); ?>
						</a>
					</div>
				</div>
			<?php endif; ?>

			<!-- Password Login Section -->
			<div class="reign-password-section">
				<div class="form-group label-floating">
					<label class="control-label" for="reign-login-username"><?php esc_html_e( 'Username', 'reign' ); ?></label>
					<input id="reign-login-username" class="form-control simple-input" name="log" type="text" aria-required="true" aria-describedby="reign-login-messages">
				</div>
				<div class="form-group label-floating password-eye-wrap">
					<label class="control-label" for="reign-login-password"><?php esc_html_e( 'Your Password', 'reign' ); ?></label>
					<input id="reign-login-password" class="form-control simple-input" name="pwd" type="password" aria-required="true" aria-describedby="reign-login-messages">
					<button type="button" class="fa fa-fw fa-eye password-eye" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'reign' ); ?>"></button>
				</div>

				<div class="remember">

					<div class="checkbox">
						<label>
							<input name="rememberme" value="forever" type="checkbox">
							<?php esc_html_e( 'Remember Me', 'reign' ); ?>
						</label>
					</div>

					<div class="registar-lostpass-wrap">
						<?php
						if ( get_option( 'users_can_register' ) ) {
							?>
							<a href="<?php echo esc_url( $registration_page_url ); ?>" class="register"><?php esc_html_e( 'Register', 'reign' ); ?></a>
							<?php
						} elseif ( function_exists( 'bp_is_active' ) && bp_get_option( 'bp-enable-membership-requests' ) ) {
							?>
							<a href="<?php echo esc_url( $registration_page_url ); ?>" class="register"><?php esc_html_e( 'Register', 'reign' ); ?></a>
							<?php
						}
						?>

						<?php $lostpswd = apply_filters( 'reign_lostpassword_url', wp_lostpassword_url() ); ?>

						<a href="<?php echo esc_url( $lostpswd ); ?>" class="forgot"><?php esc_html_e( 'Forgot my Password', 'reign' ); ?></a>
					</div>

				</div>

				<?php
				// Add Magic Link toggle option if available
				if ( class_exists( 'ITSEC_Passwordless_Login' ) ||
					 ( class_exists( 'ITSEC_Modules' ) &&
					   method_exists( 'ITSEC_Modules', 'is_active' ) &&
					   ITSEC_Modules::is_active( 'passwordless-login' ) ) ) :
					?>
					<div class="itsec-pwls-login-option" style="margin: 15px 0; text-align: center;">
						<a href="#" class="reign-toggle-login-method">
							<?php esc_html_e( 'Email me a login link', 'reign' ); ?>
						</a>
					</div>
				<?php endif; ?>

				<?php do_action( 'reign_recaptcha_after_login_form' ); ?>

				<button type="submit" class="btn full-width registration-login-submit">
					<span><?php esc_html_e( 'Login', 'reign' ); ?></span>
					<span class="icon-loader"></span>
				</button>
			</div> <!-- End of reign-password-section -->

			<?php do_action( 'reign_login_form_bottom' ); ?>

			<?php
			if ( $can_register ) {
				if ( '' !== $login_description ) {
					echo wp_kses_post( wpautop( do_shortcode( $login_description ) ) );
				} else {
					printf(
						'<p>%s %s %s</p>',
						esc_html__( 'Don\'t have an account?', 'reign' ),
						esc_html__( 'Register Now', 'reign' ),
						esc_html__( 'It\'s simple and you can start enjoying all the benefits!', 'reign' )
					);
				}
			}
			?>
		</div>
	</div>
</form>

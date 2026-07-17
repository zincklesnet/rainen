<?php
/**
 * Login
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! is_user_logged_in() ) {
	// Login Page Redirect.
	$login_page_id  = get_theme_mod( 'reign_login_page', 0 );
	$login_page_url = ( $login_page_id ) ? get_permalink( $login_page_id ) : wp_login_url();

	$reign_signin_popup = get_theme_mod( 'reign_signin_popup', false );
	$form_type_login    = get_theme_mod( 'reign_sign_form_popup', 'default' );
	$forms              = get_theme_mod( 'reign_sign_form_display', 'login' );

	if ( ( 'custom' === $form_type_login || 'login' === $forms || 'both' === $forms ) && reign_is_truthy( $reign_signin_popup ) ) {
		$login_page_url = '#';
	}
	?>
	<div class="rg-icon-wrap rg-login-btn-wrap">
		<a href="<?php echo esc_url( $login_page_url ); ?>" class="btn-login button" title="<?php esc_attr_e( 'Login', 'reign' ); ?>">
			<?php esc_html_e( 'Login', 'reign' ); ?><span class="far fa-sign-in"></span>
		</a>
	</div>
	<?php
}

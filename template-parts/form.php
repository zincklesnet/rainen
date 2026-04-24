<?php
/**
 * Login/Register Form
 *
 * @package Reign
 */

extract( $args );
$rand = rand( 1000, 9999 );

if ( is_user_logged_in() ) {

	get_template_part(
		'template-parts/form',
		'vcard',
		array(
			'rand'        => $rand,
			'redirect_to' => $redirect_to,
			'redirect'    => $redirect,
			'forms'       => $forms,
		)
	);
	return;
}

$can_register = get_option( 'users_can_register' );

if ( ! $can_register && function_exists( 'bp_is_active' ) && bp_get_option( 'bp-enable-membership-requests' ) ) {
	$can_register = 1;
}

if ( function_exists( 'bp_current_component' ) ) {
	if ( bp_current_component() === 'register' ) {
		$can_register = 0;
	}
}

$classes   = array( 'registration-login-form', 'mb-0' );
$classes[] = 'formContainer';
$classes[] = "selected-forms-{$forms}";

if ( $forms !== 'both' || ! $can_register ) {
	$classes[] = 'selected-forms-single';
}
if ( $forms !== 'both' || ! $can_register ) {
	$classes[] = 'selected-forms-single';
}
?>
<div class="<?php echo implode( ' ', $classes ); ?>">
	<!-- Nav tabs -->
	<?php if ( $can_register && $forms === 'both' ) { ?>
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link nav-login-link active" data-toggle="tab" href="#login-panel-<?php echo esc_attr( $rand ); ?>" role="tab">
					<span class="icon-title"><?php esc_html_e( 'Login', 'reign' ); ?></span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link nav-register-link" data-toggle="tab" href="#register-panel-<?php echo esc_attr( $rand ); ?>" role="tab">
					<span class="icon-title"><?php esc_html_e( 'Registration', 'reign' ); ?></span>
				</a>
			</li>
		</ul>
	<?php } ?>

	<div class="tab-content">
		<?php if ( ( $forms === 'login' || $forms === 'both' ) ) { ?>
			<div class="tab-pane active" id="login-panel-<?php echo esc_attr( $rand ); ?>" role="tabpanel">
				<?php
				get_template_part(
					'template-parts/form',
					'login',
					array(
						'rand'              => $rand,
						'redirect_to'       => $redirect_to,
						'redirect'          => $redirect,
						'forms'             => $forms,
						'login_title'       => $login_title,
						'login_description' => $login_description,
					)
				);
				?>
			</div>
		<?php } ?>

		<?php if ( $can_register && ( $forms === 'register' || $forms === 'both' ) ) { ?>
			<div class="tab-pane active" id="register-panel-<?php echo esc_attr( $rand ); ?>" role="tabpanel">
				<?php
				get_template_part(
					'template-parts/form',
					'register',
					array(
						'rand'                 => $rand,
						'redirect_to'          => $register_redirect_to,
						'redirect'             => isset( $register_redirect ) ? $register_redirect : '',
						'forms'                => $forms,
						'register_fields_type' => $register_fields_type,
					)
				);
				?>
			</div>
		<?php } ?>
	</div>
</div>

<?php
/**
 * PeepSo navbar template.
 *
 * @package PeepSo
 * @subpackage Templates
 */

global $wbtm_reign_settings;

$navbar_sticky = '';

if ( 0 == PeepSo::get_option( 'disable_navbar', 0 ) ) {
	// PeepSoTemplate::exec_template('general', 'js-unavailable');
	$PeepSoGeneral       = PeepSoGeneral::get_instance();
	$peepso_url_segments = PeepSoUrlSegments::get_instance();

	$profile_layout = isset( $wbtm_reign_settings['reign_peepsoextender']['profile_layout'] ) ? $wbtm_reign_settings['reign_peepsoextender']['profile_layout'] : 'full-width';

	if ( ( 'full-width' === $profile_layout || 'wide' === $profile_layout ) && 'peepso_profile' === $peepso_url_segments->_shortcode ) {
		return;
	}
	?>

	<?php if ( is_user_logged_in() ) { ?>
		<!-- PeepSo Navbar -->
		<div class="pso-navbar <?php echo $navbar_sticky; ?> js-toolbar">
			<div class="pso-navbar__inner">
			<div class="pso-navbar__tabs"><?php echo $PeepSoGeneral->render_navigation( 'primary' ); ?></div>
			<div class="pso-navbar__tabs pso-navbar__tabs--mobile"><?php echo $PeepSoGeneral->render_navigation( 'mobile-secondary' ); ?></div>
			<div class="pso-navbar__user">
				<div class="pso-dropdown pso-navbar-user__dropdown ps-js-dropdown">
					<a href="#" class="pso-dropdown__toggle pso-navbar-user__toggle ps-js-dropdown-toggle">
						<span><?php echo PeepSoUser::get_instance()->get_firstname(); ?></span>
						<i class="pso-i-angle-small-down"></i>
						<div class="pso-avatar pso-avatar--sm">
							<img src="<?php echo PeepSoUser::get_instance()->get_avatar(); ?>" alt="<?php echo PeepSoUser::get_instance()->get_firstname(); ?> avatar">
						</div>
					</a>
					<div class="pso-dropdown__menu ps-js-dropdown-menu">
						<?php echo $PeepSoGeneral->render_navigation( 'user' ); ?>
					</div>
				</div>
				<div class="pso-navbar__notifs"><?php echo $PeepSoGeneral->render_navigation( 'secondary' ); ?></div>
			</div>
			<div class="pso-navbar-toggle__wrapper">
				<a href="#" class="pso-navbar__toggle ps-js-navbar-toggle" onclick="return false;">
				<i class="pso-i-menu-dots-vertical"></i>
				</a>
			</div>
			</div>
			<div id="ps-mobile-navbar" class="pso-navbar__submenu">
			<div class="pso-dropdown__label">
				<?php echo esc_attr__( 'Community', 'reign' ); ?>
			</div>
			<?php echo $PeepSoGeneral->render_navigation( 'mobile-primary' ); ?>
			</div>
		</div>
		<!-- end: PeepSo Navbar -->
	<?php }
}

do_action( 'peepso_action_render_navbar_after' );
?>

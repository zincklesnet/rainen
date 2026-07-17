<?php
/**
 * PeepSo navbar template.
 *
 * @package PeepSo
 * @subpackage Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- $PeepSoGeneral mirrors the PeepSo SDK object name; renaming would diverge from upstream PeepSo templates.
$navbar_sticky = '';
if ( 0 === (int) PeepSo::get_option( 'disable_navbar', 0 ) ) {
	$PeepSoGeneral = PeepSoGeneral::get_instance();
	?>

	<?php if ( is_user_logged_in() ) { ?>
		<!-- PeepSo Navbar -->
		<div class="pso-navbar <?php echo esc_attr( $navbar_sticky ); ?> js-toolbar">
			<div class="pso-navbar__inner">
			<div class="pso-navbar__tabs"><?php echo $PeepSoGeneral->render_navigation( 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- PeepSo returns pre-escaped navigation markup. ?></div>
			<div class="pso-navbar__tabs pso-navbar__tabs--mobile"><?php echo $PeepSoGeneral->render_navigation( 'mobile-secondary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- PeepSo returns pre-escaped navigation markup. ?></div>
			<div class="pso-navbar__user">
				<div class="pso-dropdown pso-navbar-user__dropdown ps-js-dropdown">
					<a href="#" class="pso-dropdown__toggle pso-navbar-user__toggle ps-js-dropdown-toggle">
						<span><?php echo esc_html( PeepSoUser::get_instance()->get_firstname() ); ?></span>
						<i class="pso-i-angle-small-down"></i>
						<div class="pso-avatar pso-avatar--sm">
							<img src="<?php echo esc_url( PeepSoUser::get_instance()->get_avatar() ); ?>" alt="<?php echo esc_attr( PeepSoUser::get_instance()->get_firstname() ); ?> avatar">
						</div>
					</a>
					<div class="pso-dropdown__menu ps-js-dropdown-menu">
						<?php echo $PeepSoGeneral->render_navigation( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- PeepSo returns pre-escaped navigation markup. ?>
					</div>
				</div>
				<div class="pso-navbar__notifs"><?php echo $PeepSoGeneral->render_navigation( 'secondary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- PeepSo returns pre-escaped navigation markup. ?></div>
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
			<?php echo $PeepSoGeneral->render_navigation( 'mobile-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- PeepSo returns pre-escaped navigation markup. ?>
			</div>
		</div>
		<!-- end: PeepSo Navbar -->
	<?php }
}

do_action( 'peepso_action_render_navbar_after' );
?>

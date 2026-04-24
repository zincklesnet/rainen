<?php
global $wbtm_reign_settings;
$user_id              = PeepSoProfileShortcode::get_instance()->get_view_user_id();
$PeepSoProfile        = PeepSoProfile::get_instance();
$PeepSoUser           = $PeepSoProfile->init( $user_id );
$current              = PeepSoUrlSegments::get_instance()->get( 2 );
$is_profile_segment   = ! empty( $current ) ? true : false;
$use_small_cover      = $is_profile_segment && 0 == PeepSo::get_option( 'always_full_cover', 0 );
$profile_avatar       = isset( $wbtm_reign_settings['reign_peepsoextender']['centered_profile_avatar'] ) ? $wbtm_reign_settings['reign_peepsoextender']['centered_profile_avatar'] : 'no';
$profile_avatar_class = 'yes' === $profile_avatar ? 'ps-focus--centered' : '';

$cover = reign_get_peepso_member_cover_image();

if ( empty( $cover ) ) {
	$cover            = reign_render_peepso_member_cover_image();
	$reposition_style = 'display:none;';
	$cover_class      = 'default';
} else {
	$reposition_style = '';
	$cover_class      = 'has-cover';
}

?>
<div class="ps-focus <?php echo esc_attr( $use_small_cover ? 'ps-focus--small' : '' ); ?> ps-js-focus ps-js-focus--profile ps-js-focus--<?php echo esc_attr( $PeepSoUser->get_id() ); ?> <?php echo esc_attr( $profile_avatar_class ); ?>">
	<div class="ps-focus__cover ps-js-cover">
		<div class="ps-focus__cover-image ps-js-cover-wrapper">
			<img class="ps-js-cover-image" src="<?php echo esc_url( $cover ); ?>"
				alt="<?php printf( esc_attr__( '%s cover photo', 'reign' ), $PeepSoUser->get_fullname() ); ?>"
				style="<?php echo esc_attr( $PeepSoUser->get_cover_position() ); ?>; opacity: 0;" />
			<div class="ps-focus__cover-loading ps-js-cover-loading">
				<i class="gcis gci-circle-notch gci-spin"></i>
			</div>
		</div>

		<?php
		$cover_box_attrs = '';
		if ( $PeepSoUser->has_cover() ) {
			$cover_box_attrs = ' style="cursor:pointer" data-cover-url="' . esc_url( $PeepSoUser->get_cover() ) . '"';
		}
		?>

		<?php if ( $PeepSoProfile->can_edit() ) { ?>

		<!-- Cover options dropdown -->
		<div class="ps-focus__cover-inner ps-js-cover-button-popup"<?php echo $cover_box_attrs; // phpcs:ignore ?>>
			<div class="ps-avatar ps-avatar--focus ps-focus__avatar ps-js-avatar">
				<img class="ps-js-avatar-image" src="<?php echo esc_url( $PeepSoUser->get_avatar( 'full' ) ); ?>" 
					alt="<?php printf( esc_attr__( '%s avatar', 'reign' ), $PeepSoUser->get_fullname() ); ?>" />
				<?php
				$avatar_box_attrs = ' style="cursor:default"';
				if ( $PeepSoUser->has_avatar() ) {
					$avatar_box_attrs = ' onclick="peepso.simple_lightbox(\'' . esc_js( $PeepSoUser->get_avatar( 'orig' ) ) . '\'); return false"';
				}
				?>

				<div class="ps-focus__avatar-change-wrapper ps-js-avatar-button-wrapper"<?php echo $avatar_box_attrs; // phpcs:ignore ?>>
					<?php if ( ( 1 != PeepSo::get_option( 'avatars_wordpress_only', 0 ) ) && $PeepSoProfile->can_edit() ) { ?>
						<a href="#" class="ps-focus__avatar-change ps-js-avatar-button">
							<i class="gcis gci-camera"></i><span><?php echo esc_html__( 'Avatar', 'reign' ); ?></span>
						</a>
					<?php } ?>
				</div>

				<?php if ( $PeepSoUser->get_online_status() ) { ?>
					<div class="ps-online ps-online--focus ps-tip ps-tip--inline"
						aria-label="<?php printf( esc_attr__( '%s is currently online', 'reign' ), $PeepSoUser->get_fullname() ); ?>">
						<div class="ps-online__inner"></div>
					</div>
				<?php } ?>
			</div>
			<div class="ps-focus__cover-actions ps-js-focus-actions">
				<?php $PeepSoProfile->profile_actions(); ?>
			</div>
			<div class="reign-social-icons">
				<?php reign_peepso_user_social_links( $PeepSoUser->get_id() ); ?>
			</div>
		</div>
		<div class="ps-focus__options ps-js-dropdown ps-js-cover-dropdown">
			<a href="#" class="ps-focus__options-toggle ps-js-dropdown-toggle"><span><?php echo esc_html__( 'Change cover image', 'reign' ); ?></span><i class="gcis gci-image"></i></a>
			<div class="ps-focus__options-menu ps-js-dropdown-menu">
				<a href="#" class="ps-js-cover-upload">
					<i class="gcis gci-paint-brush"></i>
					<?php echo esc_html__( 'Upload', 'reign' ); ?>
				</a>
				<a href="#" class="ps-js-cover-reposition">
					<i class="gcis gci-arrows-alt"></i>
					<?php echo esc_html__( 'Reposition', 'reign' ); ?>
				</a>
				<a href="#" class="ps-js-cover-rotate-left">
					<i class="gcis gci-arrow-rotate-left"></i>
					<?php echo esc_html__( 'Rotate left', 'reign' ); ?>
				</a>
				<a href="#" class="ps-js-cover-rotate-right">
					<i class="gcis gci-arrow-rotate-right"></i>
					<?php echo esc_html__( 'Rotate right', 'reign' ); ?>
				</a>
				<a href="#" class="ps-js-cover-remove">
					<i class="gcis gci-trash"></i>
					<?php echo esc_html__( 'Delete', 'reign' ); ?>
				</a>
			</div>
		</div>
		<!-- Reposition cover - buttons -->
		<div class="ps-focus__reposition ps-js-cover-reposition-actions" style="display:none">
			<div class="ps-focus__reposition-actions reposition-cover-actions">
				<a href="#" class="ps-focus__reposition-action ps-js-cover-reposition-cancel"><?php echo esc_html__( 'Cancel', 'reign' ); ?></a>
				<a href="#" class="ps-focus__reposition-action ps-js-cover-reposition-confirm"><i class="gcis gci-check"></i> <?php echo esc_html__( 'Save', 'reign' ); ?></a>
			</div>
		</div>

		<?php } else { ?>

		<div class="ps-focus__cover-inner ps-js-cover-button-popup"<?php echo $cover_box_attrs; // phpcs:ignore ?>>
			<div class="ps-avatar ps-avatar--focus ps-focus__avatar ps-js-avatar">
				<img class="ps-js-avatar-image" src="<?php echo esc_url( $PeepSoUser->get_avatar( 'full' ) ); ?>"
					alt="<?php printf( esc_attr__( '%s avatar', 'reign' ), $PeepSoUser->get_fullname() ); ?>" />

				<?php
				$avatar_box_attrs = ' style="cursor:default"';
				if ( $PeepSoUser->has_avatar() ) {
					$avatar_box_attrs = ' onclick="peepso.simple_lightbox(\'' . esc_js( $PeepSoUser->get_avatar( 'orig' ) ) . '\'); return false"';
				}
				?>

				<div class="ps-focus__avatar-change-wrapper ps-js-avatar-button-wrapper"<?php echo $avatar_box_attrs; // phpcs:ignore ?>>
					<?php if ( ( 1 != PeepSo::get_option( 'avatars_wordpress_only', 0 ) ) && $PeepSoProfile->can_edit() ) { ?>
						<a href="#" class="ps-focus__avatar-change ps-js-avatar-button">
							<i class="gcis gci-camera"></i><span><?php echo esc_html__( 'Change avatar', 'reign' ); ?></span>
						</a>
					<?php } ?>
				</div>

				<?php if ( $PeepSoUser->get_online_status() ) { ?>
					<div class="ps-online ps-online--focus ps-tip ps-tip--inline"
						aria-label="<?php printf( esc_attr__( '%s is currently online', 'reign' ), $PeepSoUser->get_fullname() ); ?>">
						<div class="ps-online__inner"></div>
					</div>
				<?php } ?>
			</div>
			<div class="ps-focus__cover-actions ps-js-focus-actions">
				<?php $PeepSoProfile->profile_actions(); ?>
			</div>
		</div>

		<?php } ?>
	</div>

	<div class="ps-focus__footer">
		<div class="ps-focus__info">
			<div class="ps-focus__title">
				<?php
				if ( ! $is_profile_segment || 1 == (int) PeepSo::get_option( 'always_full_cover', 0 ) ) {
					echo '<div class="ps-focus__title-before">', do_action( 'peepso_profile_cover_full_before_name', $PeepSoUser->get_id() ), '</div>';
				}
				?>
				<div class="ps-focus__name" data-hover-card="<?php echo esc_attr( $PeepSoUser->get_id() ); ?>">
				<?php
					do_action( 'peepso_action_render_user_name_before', $PeepSoUser->get_id() );
					echo esc_html( $PeepSoUser->get_fullname() );
					do_action( 'peepso_action_render_user_name_after', $PeepSoUser->get_id() );
				?>
				</div>
				<?php
				if ( ! $is_profile_segment || 1 == (int) PeepSo::get_option( 'always_full_cover', 0 ) ) {
					echo '<div class="ps-focus__title-after">', do_action( 'peepso_profile_cover_full_after_name', $PeepSoUser->get_id() ), '</div>';
				}
				?>
			</div>
			<div class="ps-focus__details ps-js-focus-interactions">
				<?php $PeepSoProfile->interactions(); ?>
			</div>
			<div class="ps-focus__mobile-actions ps-js-focus-actions"><?php $PeepSoProfile->profile_actions(); ?></div>
			<div class="ps-focus__actions ps-js-profile-actions-extra"><?php $PeepSoProfile->profile_actions_extra(); ?></div>
		</div>

		<?php
		do_action( 'peepso_action_render_user_menu_before', $PeepSoUser->get_id() );

		if ( PeepSo::is_admin() ) {

			$reports = ( new PeepSoReport() )->get_reports( $PeepSoUser->get_id(), 0, 0 );
			if ( is_array( $reports ) && count( $reports ) ) {
				$reported = count( $reports );
				update_user_meta( $PeepSoUser->get_id(), 'peepso_reported', 1 );
				PeepSoTemplate::exec_template(
					'activity',
					'post-reports',
					array(
						'post_id'     => $PeepSoUser->get_id(),
						'module_id'   => 0,
						'type'        => 'profile',
						'reported'    => $reports,
						'reports'     => $reports,
						'unpublished' => 0,
					)
				);
			}
		}
		?>
		<?php

		if ( ! $is_profile_segment ) {
			$current = array_key_first( apply_filters( 'peepso_navigation_profile', array( 'stream' => 'stream' ) ) );
		}

		?>
		<div class="ps-focus__menu ps-js-focus__menu">
			<div class="ps-focus__menu-inner ps-js-focus__menu-inner">
				<?php echo esc_html( $PeepSoProfile->profile_navigation( array( 'current' => $current ) ) ); ?>
			</div>
			<div class="ps-focus__menu-shadow ps-focus__menu-shadow--left ps-js-aid-left"></div>
			<div class="ps-focus__menu-shadow ps-focus__menu-shadow--right ps-js-aid-right"></div>
		</div>
	</div>
</div>

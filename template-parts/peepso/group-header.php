<?php

global $wbtm_reign_settings;
$url             = PeepSoUrlSegments::get_instance();
$group_id        = $url->get( 1 );
$group_segment   = $url->get( 2 );
$group           = new PeepSoGroup( $group_id );
$PeepSoGroupUser = new PeepSoGroupUser( $group->id );
$PeepSoGroup     = $group;
$coverUrl        = $PeepSoGroup->get_cover_url();
$has_cover       = false;

if ( false !== stripos( $coverUrl, 'peepso/groups/' ) ) {
	$has_cover = true;
}

if ( false === $PeepSoGroupUser->can( 'manage_group' ) || ( false === $has_cover ) ) {
	$reposition_style = 'display:none;';
	$cover_class      = 'default';
} else {
	$reposition_style = '';
	$cover_class      = 'has-cover';
}

$description           = str_replace( "\n", '<br/>', $group->description );
$description           = html_entity_decode( $description );
$group_categories      = PeepSoGroupCategoriesGroups::get_categories_for_group( $group->id );
$group_categories_html = array();
$group_cover_photo     = get_post_meta( $group->id, 'group_cover_photo', true );
$group_avatar          = isset( $wbtm_reign_settings['reign_peepsoextender']['centered_group_avatar'] ) ? $wbtm_reign_settings['reign_peepsoextender']['centered_group_avatar'] : 'no';
$group_avatar_class    = 'yes' === $group_avatar ? 'ps-focus--centered' : '';
if ( empty( $group_cover_photo ) ) {
	$group_cover_photo = reign_render_peepso_group_cover_image();
}

?>
<div class="ps-focus ps-focus--group ps-group__profile-focus ps-js-focus ps-js-focus--group ps-js-group-header <?php echo esc_attr( $group_avatar_class ); ?>">
	<div class="ps-focus__cover ps-js-cover">
		<div class="ps-focus__cover-image ps-js-cover-wrapper">
			<img class="ps-js-cover-image" src="<?php echo esc_url( $group_cover_photo ); ?>"
				alt="<?php printf( esc_attr__( '%s cover photo', 'reign' ), esc_attr( $PeepSoGroup->get( 'name' ) ) ); ?>"
				style="<?php echo esc_attr( $PeepSoGroup->cover_photo_position() ); ?>; opacity: 0;" />
			<div class="ps-focus__cover-loading ps-js-cover-loading">
				<i class="gcis gci-circle-notch gci-spin"></i>
			</div>
		</div>

		<?php
		$cover_box_attrs = '';
		if ( $PeepSoGroup->has_cover() ) {
			$cover_box_attrs = ' style="cursor:pointer" data-cover-url="' . esc_url( $PeepSoGroup->get_cover_url() ) . '"';
		}
		?>

		<div class="ps-focus__cover-inner ps-js-cover-button-popup"<?php echo $cover_box_attrs; // phpcs:ignore ?>>
			<div class="ps-avatar ps-avatar--focus ps-focus__avatar ps-group__profile-focus-avatar ps-js-avatar">
				<img class="ps-js-avatar-image" src="<?php echo esc_url( $PeepSoGroup->get_avatar_url_full() ); ?>"
					alt="<?php printf( esc_attr__( '%s avatar', 'reign' ), esc_attr( $PeepSoGroup->get( 'name' ) ) ); ?>" />

				<?php
				$avatar_box_attrs = ' style="cursor:default"';
				if ( $PeepSoGroup->has_avatar() ) {
					$avatar_box_attrs = ' onclick="peepso.simple_lightbox(\'' . esc_url( $PeepSoGroup->get_avatar_url_orig() ) . '\'); return false"';
				}
				?>

				<div class="ps-focus__avatar-change-wrapper ps-js-avatar-button-wrapper"<?php echo $avatar_box_attrs; // phpcs:ignore ?>>
					<?php if ( $PeepSoGroupUser->can( 'manage_group' ) ) { ?>
					<a href="#" class="ps-focus__avatar-change ps-js-avatar-button">
						<i class="gcis gci-camera"></i><span><?php echo esc_html__( 'Avatar', 'reign' ); ?></span>
					</a>
					<?php } ?>
				</div>
			</div>
			<div class="ps-focus__cover-actions ps-js-group-header-actions ps-js-loading">
				<button class="ps-focus__cover-action">
					<img src="<?php echo esc_url( PeepSo::get_asset( 'images/ajax-loader.gif' ) ); ?>" alt="" aria-hidden="true" />
				</button>
			</div>
		</div>

		<?php if ( $PeepSoGroupUser->can( 'manage_group' ) ) { ?>

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

		<div class="ps-focus__reposition ps-js-cover-reposition-actions" style="display:none">
			<div class="ps-focus__reposition-actions reposition-cover-actions">
				<a href="#" class="ps-focus__reposition-action ps-js-cover-reposition-cancel"><?php echo esc_html__( 'Cancel', 'reign' ); ?></a>
				<a href="#" class="ps-focus__reposition-action ps-js-cover-reposition-confirm"><i class="fas fa-check"></i> <?php echo esc_html__( 'Save', 'reign' ); ?></a>
			</div>
		</div>

		<?php } ?>
	</div>

	<div class="ps-focus__footer ps-group__profile-focus-footer">
		<div class="ps-focus__info">
			<div class="ps-focus__title">
				<div class="ps-focus__name">
					<?php echo esc_html( $group->name ); ?>
				</div>
				<div class="ps-focus__desc-toggle ps-tip ps-tip--absolute ps-tip--inline ps-tip--bottom ps-js-focus-box-toggle" aria-label="<?php echo esc_attr__( 'Show details', 'reign' ); ?>">
					<i class="gcis gci-info-circle"></i>
				</div>
			</div>

			<div class="ps-focus__desc ps-js-focus-desc">
				<!-- Description -->
				<?php
				$description = stripslashes( $description );
				if ( PeepSo::get_option_new( 'md_groups_about', 0 ) ) {
					$description = PeepSo::do_parsedown( $description );
				}

				echo wp_kses_post( $description );

				?>

				<!-- Categories -->
				<?php if ( PeepSo::get_option( 'groups_categories_enabled', false ) ) { ?>
				<div class="ps-focus__desc-details">
					<?php
					if ( count( $group_categories ) > 1 ) {
						?>
						<i class="gcis gci-tags"></i> <?php echo esc_html__( 'Group categories', 'reign' ); ?>:
						<?php
					} else {
						?>
						<i class="gcis gci-tag"></i> <?php echo esc_html__( 'Group category', 'reign' ); ?>:<?php } ?>
					<?php

					foreach ( $group_categories as $PeepSoGroupCategory ) {
						echo '<a href="' . esc_url( $PeepSoGroupCategory->get_url() ) . '">' . esc_html( $PeepSoGroupCategory->name ) . '</a>';
					}
					?>
				</div>
				<?php } ?>
			</div>

			<div class="ps-focus__details">
				<!-- DETAILS -->

				<!-- Privacy -->
				<div class="ps-focus__detail">
					<?php if ( $PeepSoGroupUser->can( 'manage_group' ) && strlen( $group_segment ) && 'settings' == $group_segment ) { ?>
						<div class="ps-group__profile-privacy ps-dropdown ps-dropdown--privacy ps-js-dropdown ps-js-privacy ps-js-privacy--<?php echo esc_attr( $group->id ); ?>">
							<a href="javascript:" data-value="" class="ps-btn ps-btn--sm ps-btn--dropdown ps-dropdown__toggle ps-js-dropdown-toggle">
								<span class="dropdown-value">
									<i class="<?php echo esc_attr( $group->privacy['icon'] ); ?>"></i><span><?php echo esc_html( $group->privacy['name'] ); ?></span>
								</span>
								<img class="ps-loading" src="<?php echo esc_url( PeepSo::get_asset( 'images/ajax-loader.gif' ) ); ?>" alt="" aria-hidden="true" />
								<div class="ps-btn__icon"><span class="gcis gci-chevron-down"></span></div>
							</a>

							<?php echo PeepSoGroupPrivacy::render_dropdown(); ?>
						</div>
					<?php } else { ?>
						<span class="ps-btn ps-btn--sm ps-btn--app ps-tip ps-tip--bottom ps-tip--md ps-tip--arrow ps-tip--left" aria-label="<?php echo esc_attr( $group->privacy['desc'] ); ?>">
							<i class="<?php echo esc_attr( $group->privacy['icon'] ); ?>"></i><?php printf( __( ' %s Group', 'reign' ), esc_html( $group->privacy['name'] ) ); ?>
						</span>
					<?php } ?>
				</div>

				<!-- Members -->
				<a class="ps-focus__detail" href="<?php echo esc_url( $group->get_url() . 'members/' ); ?>">
					<i class="pso-i-queue-alt"></i>
					<span class="ps-js-member-count"><?php printf( _n( '%s member', '%s members', $group->members_count, 'reign' ), number_format_i18n( $group->members_count ) ); ?></span>
				</a>

				<!-- Pending members -->
				<?php if ( $group->pending_admin_members_count > 0 && $PeepSoGroupUser->can( 'manage_users' ) ) { ?>
					<a class="ps-focus__detail ps-js-pending-label" href="<?php echo esc_url( $group->get_url() . 'members/pending' ); ?>">
						<i class="gcis gci-user-clock"></i>
						<?php printf( __( ' <span class="ps-js-pending-count" data-id="%1$d">%2$s</span> pending', 'reign' ), esc_attr( $group->id ), esc_html( $group->pending_admin_members_count ) ); ?>
					</a>
				<?php } ?>
			</div>
			<div class="ps-focus__mobile-actions ps-js-group-header-actions ps-js-loading">
				<button class="ps-focus__cover-action">
					<img src="<?php echo esc_url( PeepSo::get_asset( 'images/ajax-loader.gif' ) ); ?>" alt="" aria-hidden="true" />
				</button>
			</div>
		</div>

		<div class="ps-focus__menu ps-js-focus__menu">
			<div class="ps-focus__menu-inner ps-js-focus__menu-inner">
				<?php

				$segments      = array();
				$segments[0][] = array(
					'href'  => '',
					'title' => __( 'Stream', 'reign' ),
					'icon'  => 'pso-i-bars-staggered',
				);

				if ( $PeepSoGroupUser->can( 'manage_group' ) ) {
					$segments[0][] = array(
						'href'  => 'settings',
						'title' => __( 'Settings', 'reign' ),
						'icon'  => 'pso-i-settings-sliders',
					);
				}

				$title = __( 'Members', 'reign' );

				if ( $PeepSoGroupUser->can( 'manage_users' ) && $pending = $group->pending_admin_members_count ) {
						$title .= ' <span class="ps-js-pending-label">(' . sprintf( __( '<span class="ps-js-pending-count" data-id="%1$d">%2$s</span> pending', 'reign' ), $group->id, $pending ) . ')</span>';
				}

				if ( $PeepSoGroupUser->can( 'view_users' ) ) {
					$segments[0][] = array(
						'href'  => 'members',
						'title' => $title,
						'icon'  => 'pso-i-queue-alt',
					);
				}

				$segments['_PeepSoGroup']     = $PeepSoGroup;
				$segments['_PeepSoGroupUser'] = $PeepSoGroupUser;

				$segments = apply_filters( 'peepso_group_segment_menu_links', $segments );

				unset( $segments['_PeepSoGroup'] );
				unset( $segments['_PeepSoGroupUser'] );

				foreach ( $segments as $segment_group ) {
					foreach ( $segment_group as $segment ) {

						$can_access = $PeepSoGroupUser->can( 'access_segment', $segment['href'] );

						$href = $group->get_url();

						if ( strlen( $segment['href'] ) ) {
							$href .= $segment['href'] . '/';

							// If passing an external link, treat it as such
							if ( 'http' == substr( $segment['href'], 0, 4 ) ) {
								$href = $segment['href'];
							}
						}

						if ( $can_access ) {
							?>
							<a class="ps-focus__menu-item ps-js-item <?php echo ( $segment['href'] == $group_segment ) ? 'ps-focus__menu-item--active' : ''; ?>" href="<?php echo esc_url( $href ); ?>" aria-label="<?php echo $segment['label'] ?? $segment['title']; ?>">
								<div class="ps-focus__menu-item-inner">
									<i class="<?php echo esc_attr( $segment['icon'] ); ?>"></i>
									<span><?php echo esc_html( $segment['title'] ); ?></span>
								</div>
							</a>
							<?php
						}
					}
				}

				?>
				<a href="#" class="ps-focus__menu-item ps-focus__menu-item--more ps-tip ps-tip--arrow ps-js-item-more" aria-label="<?php echo esc_attr__( 'More', 'reign' ); ?>" style="display:none">
					<i class="gcis gci-ellipsis-h"></i>
				</a>
				<div class="ps-focus__menu-more ps-dropdown ps-dropdown--menu ps-js-focus-more">
					<div class="ps-dropdown__menu ps-js-focus-link-dropdown"></div>
				</div>
			</div>
			<div class="ps-focus__menu-shadow ps-focus__menu-shadow--left ps-js-aid-left"></div>
			<div class="ps-focus__menu-shadow ps-focus__menu-shadow--right ps-js-aid-right"></div>
		</div>
	</div>
</div>
<script>
jQuery(function() {
	peepsogroupsdata.group_id = +'<?php echo $group->id // phpcs:ignore ?>';
});
</script>

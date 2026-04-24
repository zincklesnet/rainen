<?php
/**
 * BuddyPress - Users Cover Image Header
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>
<?php
global $wbtm_reign_settings;
$member_header_class = isset( $wbtm_reign_settings['reign_buddyextender']['member_header_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['member_header_type'] : 'wbtm-cover-header-type-1';
$member_header_class = apply_filters( 'wbtm_rth_manage_member_header_class', $member_header_class );

$bp_displayed_user_id     = bp_displayed_user_id();
$has_cover_image          = '';
$has_cover_image_position = '';
$has_default_cover        = bb_attachment_get_cover_image_class( bp_displayed_user_id(), 'user' );
$profile_cover_width      = bb_get_profile_cover_image_width();
$profile_cover_height     = bb_get_profile_cover_image_height();
$cover_image_url          = bp_attachments_get_attachment(
	'url',
	array(
		'object_dir' => 'members',
		'item_id'    => $bp_displayed_user_id,
	)
);

if ( ! empty( $cover_image_url ) ) {
	$cover_image_position = bp_get_user_meta( $bp_displayed_user_id, 'bp_cover_position', true );
	$has_cover_image      = ' has-cover-image';
	if ( '' !== $cover_image_position ) {
		$has_cover_image_position = ' has-position';
	}
}

$profile_header_layout_style = bb_get_profile_header_layout_style();
$is_enabled_online_status    = bb_enabled_profile_header_layout_element( 'online-status' );
$is_enabled_profile_type     = bb_enabled_profile_header_layout_element( 'profile-type' );
$is_enabled_member_handle    = bb_enabled_profile_header_layout_element( 'member-handle' );
$is_enabled_joined_date      = bb_enabled_profile_header_layout_element( 'joined-date' );
$is_enabled_last_active      = bb_enabled_profile_header_layout_element( 'last-active' );
$is_enabled_followers        = bb_enabled_profile_header_layout_element( 'followers' );
$is_enabled_following        = bb_enabled_profile_header_layout_element( 'following' );
$is_enabled_social_networks  = bb_enabled_profile_header_layout_element( 'social-networks' ) && function_exists( 'bb_enabled_member_social_networks' ) && bb_enabled_member_social_networks();

$override_member_header_layout = $wbtm_reign_settings['reign_buddyextender']['override_member_header_layout'] ?? '';

if ( function_exists( 'bb_platform_pro' ) && 'on' !== $override_member_header_layout ) {
	if ( 'centered' === $profile_header_layout_style ) {
		$member_header_class = 'wbtm-cover-header-type-4';
	} elseif ( 'left' === $profile_header_layout_style ) {
		$member_header_class = 'wbtm-cover-header-type-3';
	}
}

$my_profile                     = '';
$user_social_networks_urls      = '';
$social_networks_urls_div_class = 'social-networks-hide';
if ( $is_enabled_social_networks ) {

	add_filter( 'bp_get_user_social_networks_urls', 'bb_get_user_social_networks_urls_with_visibility', 10, 3 );
	$user_social_networks_urls = bp_get_user_social_networks_urls();
	remove_filter( 'bp_get_user_social_networks_urls', 'bb_get_user_social_networks_urls_with_visibility', 10, 3 );

	if ( ! empty( $user_social_networks_urls ) ) {
		$social_networks_urls_div_class = 'network_profiles';
	}
}

$bp_is_my_profile = bp_is_my_profile();
if ( $bp_is_my_profile ) {
	$my_profile = 'my_profile';
}
?>

<?php if ( ! bp_is_user_messages() && ! bp_is_user_settings() && ! bp_is_user_notifications() && ! bp_is_user_profile_edit() && ! bp_is_user_change_avatar() && ! bp_is_user_change_cover_image() && ! apply_filters( 'reign_bp_is_current_action', false ) ) :
	$is_activity_enabled       = bp_is_active( 'activity' );
	$bp_activity_do_mentions   = $is_activity_enabled && bp_activity_do_mentions();
	$bp_get_last_activity      = bp_get_last_activity();
	$bb_get_member_joined_date = bb_get_member_joined_date();

	$member_type = '';
	if ( true === bp_member_type_enable_disable() && true === bp_member_type_display_on_profile() && $is_enabled_profile_type ) {
		$member_type = bp_get_user_member_type( $bp_displayed_user_id );
	}
	?>

	<div id="cover-image-container" class="wbtm-member-cover-image-container <?php echo esc_attr( $member_header_class . ' ' . $social_networks_urls_div_class . ' ' . $my_profile ); ?> bb-cover-image-container">

		<div id="header-cover-image" class="<?php echo esc_attr( 'cover-' . $profile_cover_height . ' width-' . $profile_cover_width . $has_cover_image_position . $has_cover_image . $has_default_cover ); ?>">
			<?php
			if ( ! empty( $cover_image_url ) ) {
				?>
				<img class="header-cover-img"
						src="<?php echo esc_url( $cover_image_url ); ?>"
						<?php
						echo ( '' !== $cover_image_position ) ? ' data-top="' . esc_attr( $cover_image_position ) . '"' : '';
						echo ( '' !== $cover_image_position ) ? ' style="top: ' . esc_attr( $cover_image_position ) . 'px"' : '';
						?>
						alt=""
				/>
				<?php
			}
			if ( $bp_is_my_profile ) {
				?>
				<a href="<?php echo esc_url( bp_get_members_component_link( 'profile', 'change-cover-image' ) ); ?>" class="link-change-cover-image bp-tooltip" data-bp-tooltip-pos="right" data-bp-tooltip="<?php esc_attr_e( 'Change Cover Photo', 'reign' ); ?>" aria-label="<?php esc_attr_e( 'Change Cover Photo', 'reign' ); ?>">
					<i class="bb-icon-bf bb-icon-camera"></i>
				</a>

				<?php
				if ( ! empty( $cover_image_url ) && bp_attachments_get_user_has_cover_image( bp_displayed_user_id() ) ) {
					?>
					<a href="#" class="position-change-cover-image bp-tooltip" data-bp-tooltip-pos="right" data-bp-tooltip="<?php esc_attr_e( 'Reposition Cover Photo', 'reign' ); ?>" aria-label="<?php esc_attr_e( 'Reposition Cover Photo', 'reign' ); ?>">
						<i class="bb-icon-bf bb-icon-arrows"></i>
					</a>
					<div class="header-cover-reposition-wrap">
						<a href="#" class="button small cover-image-cancel"><?php esc_html_e( 'Cancel', 'reign' ); ?></a>
						<a href="#" class="button small cover-image-save"><?php esc_html_e( 'Save Changes', 'reign' ); ?></a>
						<span class="drag-element-helper"><i class="bb-icon-l bb-icon-bars"></i><?php esc_html_e( 'Drag to move cover photo', 'reign' ); ?></span>
						<img src="<?php echo esc_url( $cover_image_url ); ?>" alt="<?php esc_attr_e( 'Cover photo', 'reign' ); ?>"/>
					</div>
					<?php
				}
			}
			?>
		</div>

		<div id="item-header-cover-image" class="item-header-wrap <?php echo esc_attr( bp_disable_cover_image_uploads() ? 'bb-disable-cover-img' : 'bb-enable-cover-img' ); ?>">

			<div class="wbtm-member-info-section"><!-- custom wrapper for main content :: start -->

				<?php
				$moderation_class = function_exists( 'bp_moderation_is_user_suspended' ) && bp_moderation_is_user_suspended( bp_displayed_user_id() ) ? 'bp-user-suspended' : '';
				$moderation_class = function_exists( 'bp_moderation_is_user_blocked' ) && bp_moderation_is_user_blocked( bp_displayed_user_id() ) ? $moderation_class . ' bp-user-blocked' : $moderation_class;
				?>
				<div id="item-header-avatar" class="<?php echo esc_attr( $moderation_class ); ?>">
					<?php
					if ( $is_enabled_online_status ) {
						bb_user_presence_html( bp_displayed_user_id() );
					}

					if ( $bp_is_my_profile && ! bp_disable_avatar_uploads() ) {
						?>
						<a href="<?php bp_members_component_link( 'profile', 'change-avatar' ); ?>" class="link-change-profile-image bp-tooltip" data-bp-tooltip-pos="down" data-bp-tooltip="<?php esc_attr_e( 'Change Profile Photo', 'reign' ); ?>" aria-label="<?php esc_attr_e( 'Change Profile Photo', 'reign' ); ?>">
							<i class="bb-icon-rf bb-icon-camera"></i>
						</a>
						<span class="link-change-overlay"></span>
						<?php
					}
					bp_displayed_user_avatar( 'type=full' );
					if ( ! empty( $member_type ) ) {
						echo wp_kses_post( $member_type );
					}
					?>
				</div><!-- #item-header-avatar -->

				<div id="item-header-content">

					<?php
					/**
					 * Fires before the bp_displayed_user_mentionname.
					 * Added by Reign Theme
					 *
					 * @since 1.0.7
					 */
					do_action( 'reign_bp_before_displayed_user_mentionname' );
					?>

					<h2 class="user-nicename"><?php echo wp_kses_post( bp_core_get_user_displayname( bp_displayed_user_id() ) ); ?></h2>

					<?php
					if ( ! empty( $member_type ) ) {
						echo wp_kses_post( $member_type );
					}
					?>

					<div class="wbtm-item-buttons-wrapper member-header-actions-wrap">
						<div id="item-buttons">
							<?php
							bp_nouveau_member_header_buttons(
								array(
									'container'         => 'div',
									'button_element'    => 'button',
									'container_classes' => array( 'member-header-actions' ),
									'prefix_link_text'  => '<i></i>',
									'is_tooltips'       => false,
									'button_attr'       => array(
										'hover_type' => 'hover',
									),
								)
							);
							?>
						</div><!-- #item-buttons -->
						<?php
						bp_nouveau_member_header_bubble_buttons(
							array(
								'container'         => 'div',
								'button_element'    => 'button',
								'container_classes' => array( 'bb_more_options', 'header-dropdown' ),
								'is_tooltips'       => false,
								'button_attr'       => array(
									'hover_type' => 'static',
								),
							)
						);
						?>
					</div>

					<?php
					bp_nouveau_member_hook( 'before', 'header_meta' );
					if ( ( $is_activity_enabled && $bp_activity_do_mentions ) || $bp_get_last_activity || $bb_get_member_joined_date ) :
						?>
						<div class="item-meta">
							<?php
							$nickname_field_id = bp_xprofile_nickname_field_id();
							$hidden_fields     = bp_xprofile_get_hidden_fields_for_user();
							if ( $is_activity_enabled && $bp_activity_do_mentions && ! in_array( $nickname_field_id, $hidden_fields, true ) && $is_enabled_member_handle ) :
								?>
								<span class="mention-name">@<?php bp_displayed_user_mentionname(); ?></span>
								<?php
							endif;
							if ( $is_activity_enabled && $bp_activity_do_mentions && $is_enabled_member_handle && $is_enabled_joined_date ) :
								?>
								<span class="separator">&bull;</span>
								<?php
							endif;
							if ( $bb_get_member_joined_date && $is_enabled_joined_date ) :
								echo wp_kses_post( $bb_get_member_joined_date );
							endif;
							if ( ( ( $is_activity_enabled && $bp_activity_do_mentions ) || $bb_get_member_joined_date ) && $bp_get_last_activity && $is_enabled_last_active && ( $is_enabled_member_handle || $is_enabled_joined_date ) ) :
								?>
								<span class="separator">&bull;</span>
								<?php
							endif;
							bp_nouveau_member_hook( 'before', 'in_header_meta' );
							if ( $bp_get_last_activity && $is_enabled_last_active ) :
								echo wp_kses_post( $bp_get_last_activity );
							endif;
							?>
						</div><!-- #item-meta -->
						<?php
					endif;

					bp_nouveau_member_hook( '', 'header_profile_meta' );

					$additional_class = '';
					if ( function_exists( 'bb_get_user_social_networks_field_value' ) ) {
						$networks_field_value = bb_get_user_social_networks_field_value();
						if ( is_array( $networks_field_value ) && count( array_filter( $networks_field_value ) ) > 6 ) {
							$additional_class = 'left-align';
						}
					}

					if ( ! empty( $user_social_networks_urls ) ) {
						?>
						<div class="flex align-items-center member-social-links <?php echo esc_attr( $additional_class ); ?>">
							<?php echo wp_kses( $user_social_networks_urls, bb_members_allow_html_tags() ); ?>
						</div>
						<?php
					}
					?>

					<?php if ( class_exists( 'BadgeOS' ) ) : ?>
						<div class="wbtm-badge"> 
							<?php
							if ( function_exists( 'reign_profile_achievements' ) ) :
								reign_profile_achievements();
							endif;
							?>
						</div>
					<?php endif; ?>

				</div><!-- #item-header-content -->

			</div><!-- custom wrapper for main content :: end -->

			<!-- custom section for extra content :: start -->
			<div class="wbtm-cover-extra-info-section">
				<?php
				/**
				 * Fires after main content to show extra information.
				 *
				 * @since 1.0.7
				 */
				do_action( 'reign_member_extra_info_section' );
				?>
			</div>
			<!-- custom section for extra content :: start -->

		</div><!-- #item-header-cover-image -->
	</div><!-- #cover-image-container -->
	<?php do_action( 'wbtm_after_cover_imager_container' ); ?>
<?php endif; ?>

<?php add_filter( 'bp_get_add_follow_button', 'bb_bp_get_add_follow_button' ); ?>

<!-- Remove Connection confirmation popup -->
<div class="bb-remove-connection bb-action-popup" style="display: none">
	<transition name="modal">
		<div class="modal-mask bb-white bbm-model-wrap">
			<div class="modal-wrapper">
				<div class="modal-container">
					<header class="bb-model-header">
						<h4><span class="target_name"><?php echo esc_html__( 'Remove Connection', 'reign' ); ?></span></h4>
						<a class="bb-close-remove-connection bb-model-close-button" href="#">
							<span class="bb-icon-l bb-icon-times"></span>
						</a>
					</header>
					<div class="bb-remove-connection-content bb-action-popup-content">
						<p>
							<?php
							echo sprintf(
								/* translators: %s: The member name with HTML tags. */
								esc_html__( 'Are you sure you want to remove %s from your connections?', 'reign' ),
								'<span class="bb-user-name"></span>'
							);
							?>
						</p>
					</div>
					<footer class="bb-model-footer flex align-items-center">
						<a class="bb-close-remove-connection bb-close-action-popup" href="#"><?php echo esc_html__( 'Cancel', 'reign' ); ?></a>
						<a class="button push-right bb-confirm-remove-connection" href="#"><?php echo esc_html__( 'Confirm', 'reign' ); ?></a>
					</footer>
				</div>
			</div>
		</div>
	</transition>
</div> <!-- .bb-remove-connection -->

<?php
/**
 * BuddyPress - `new_avatar` activity type content part.
 *
 * This template is only used to display the `new_avatar` activity type content.
 *
 * @since 10.0.0
 * @version 10.0.0
 */

$avatar_url = bp_activity_get_meta( bp_get_activity_id(), 'member_avatar_image', true );
$itme_name  = bp_core_get_user_displayname( bp_get_activity_user_id() );

?>
<div class="bp-member-activity-preview">

	<?php if ( bp_activity_has_generated_content_part( 'user_cover_image' ) ) : ?>
		<div class="bp-member-preview-cover">
			<a href="<?php bp_activity_generated_content_part( 'user_url' ); ?>">
				<img src="<?php bp_activity_generated_content_part( 'user_cover_image' ); ?>" alt=""/>
			</a>
		</div>
	<?php else : ?>
		<div class="bp-member-preview-cover">
			<a href="<?php bp_activity_generated_content_part( 'user_url' ); ?>">
				<?php
				global $wbtm_reign_settings;
				$cover_img_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
				if ( empty( $cover_img_url ) ) {
					$cover_img_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
				}
				?>
				<img src="<?php echo esc_url( $cover_img_url ); ?>" alt=""/>
			</a>
		</div>
	<?php endif; ?>

	<div class="bp-member-short-description">
		<?php if ( bp_activity_has_generated_content_part( 'user_profile_photo' ) ) : ?>
			<div class="bp-member-avatar-content <?php echo bp_activity_has_generated_content_part( 'user_cover_image' ) ? 'has-cover-image' : 'has-cover-image'; ?>">
				<a href="<?php bp_activity_generated_content_part( 'user_url' ); ?>">
					<img src="<?php bp_activity_generated_content_part( 'user_profile_photo' ); ?>" class="profile-photo avatar" alt=""/>
				</a>
			</div>
		<?php elseif ( ! empty( $avatar_url ) ) : ?>
			<div class="bp-member-avatar-content <?php echo bp_activity_has_generated_content_part( 'user_cover_image' ) ? 'has-cover-image' : 'has-cover-image'; ?>">
				<a href="<?php bp_activity_generated_content_part( 'user_url' ); ?>">
					<img src="<?php echo esc_url( $avatar_url, array( 'data', 'http', 'https' ) ); ?>" class="profile-photo avatar" alt=""/>
				</a>
			</div>
			<?php
		elseif ( empty( $avatar_url ) ) :
			$avatar_url = bp_core_fetch_avatar(
				array(
					'item_id' => bp_get_activity_user_id(),
					'type'    => 'full',
					'width'   => 150,
					'height'  => 150,
					'class'   => 'avatar',
					'id'      => false,
					'html'    => false,
					'alt'     => sprintf( __( 'Profile picture of %s', 'reign' ), $itme_name ),
				)
			);

			?>
		
			<div class="bp-member-avatar-content <?php echo bp_activity_has_generated_content_part( 'user_cover_image' ) ? 'has-cover-image' : ''; ?>">
				<a href="<?php bp_activity_generated_content_part( 'user_url' ); ?>">
					<img src="<?php echo esc_url( $avatar_url, array( 'data', 'http', 'https' ) ); ?>" class="profile-photo avatar" alt=""/>
				</a>
			</div>
			
		<?php endif; ?>

		<p class="bp-member-short-description-title">
			<a href="<?php bp_activity_generated_content_part( 'user_url' ); ?>"><?php bp_activity_generated_content_part( 'user_display_name' ); ?></a>
		</p>

		<p class="bp-member-nickname">
			<a href="<?php is_user_logged_in() ? bp_activity_generated_content_part( 'user_mention_url' ) : bp_activity_generated_content_part( 'user_url' ); ?>">@<?php bp_activity_generated_content_part( 'user_mention_name' ); ?></a>
		</p>

		<div class="bp-profile-button">
			<a href="<?php bp_activity_generated_content_part( 'user_url' ); ?>" class="button large primary button-primary" role="button"><?php esc_html_e( 'View Profile', 'reign' ); ?></a>
		</div>
	</div>
</div>

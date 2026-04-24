<?php
/**
 * BuddyPress - `created_group` activity type content part.
 *
 * This template is only used to display the `created_group` activity type content.
 *
 * @since 10.0.0
 * @version 12.0.0
 */

// Get the group from the activity item_id
$group = bp_get_group( bp_get_activity_item_id() );
$group_url = bp_get_group_url( $group );
$group_name = bp_get_group_name( $group );
$group_cover = bp_get_group_cover_url( $group );
$group_avatar = bp_core_fetch_avatar(
	array(
		'item_id' => $group->id,
		'object'  => 'group',
		'type'    => 'full',
		'html'    => false,
	)
);
?>
<div class="bp-group-activity-preview reign-user-preview">

	<?php if ( ! empty( $group_cover ) ) : ?>
		<div class="bp-group-preview-cover">
			<a href="<?php echo esc_url( $group_url ); ?>">
				<img src="<?php echo esc_url( $group_cover ); ?>" alt=""/>
			</a>
		</div>
	<?php else : ?>
		<div class="bp-group-preview-cover">
			<a href="<?php echo esc_url( $group_url ); ?>">
				<?php
				global $wbtm_reign_settings;
				$cover_img_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
				if ( empty( $cover_img_url ) ) {
					$cover_img_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
				}
				?>
				<img src="<?php echo esc_url( $cover_img_url ); ?>" alt=""/>
			</a>
		</div>
	<?php endif; ?>

	<div class="bp-group-short-description">
		<?php if ( ! empty( $group_avatar ) ) : ?>
			<div class="bp-group-avatar-content <?php echo ! empty( $group_cover ) ? 'has-cover-image' : 'has-cover-image'; ?>">
				<a href="<?php echo esc_url( $group_url ); ?>">
					<img src="<?php echo esc_url( $group_avatar ); ?>" class="profile-photo avatar aligncenter" alt=""/>
				</a>
			</div>
		<?php endif; ?>

		<p class="bp-group-short-description-title">
			<a href="<?php echo esc_url( $group_url ); ?>"><?php echo esc_html( $group_name ); ?></a>
		</p>

		<div class="bp-profile-button">
			<a href="<?php echo esc_url( $group_url ); ?>" class="button large primary button-primary" role="button"><?php esc_html_e( 'Visit group', 'reign' ); ?></a>
		</div>
	</div>

	<?php if ( function_exists( 'groups_get_total_member_count' ) ) : ?>
	<div class="reign-user-preview-footer">
		<div class="reign-user-stats">
			<div class="reign-user-stat">
				<p class="reign-user-stat-title"><?php echo esc_html( groups_get_total_member_count( bp_get_activity_item_id() ) ); ?></p>
				<p class="reign-user-stat-text"><?php esc_html_e( 'Members', 'reign' ); ?></p>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>

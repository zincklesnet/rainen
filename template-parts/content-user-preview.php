<?php
/**
 * Content User Preview
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- $args is a controlled, internally-built array passed by the activity preview loader.
extract( $args );
$url                   = '';
$cover_url             = '';
$avatar_url            = '';
$itme_name             = '';
$item_slug             = '';
$display_action_button = false;
$action_button_text    = '';

global $wbtm_reign_settings;

if ( 'groups' === $component ) {
	$group = groups_get_group( array( 'group_id' => $item_id ) );
	if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
		$url = bp_get_group_url( $group );
	} else {
		$url = bp_get_group_permalink( $group );
	}

	if ( 'new_group_cover_photo' === $type ) {
		$cover_url = bp_activity_get_meta( $activity_id, 'group_cover_image', true );
	} else {
		$cover_url             = bp_get_group_cover_url( $group );
		$display_action_button = true;
		$action_button_text    = esc_html__( 'Visit Group', 'reign' );
	}
	$itme_name = $group->name;
	$itme_slug = $group->slug;

	if ( empty( $cover_url ) ) {
		$cover_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
	}

	$avatar_url = bp_activity_get_meta( $activity_id, 'group_avatar_image', true );
	if ( 'new_group_avatar' === $type && '' !== $avatar_url ) {
		$avatar_url = '<img loading="lazy" src="' . esc_url( $avatar_url, array( 'data', 'http', 'https' ) ) . '" class="avatar group-' . esc_attr( $item_id ) . '-avatar avatar-150 photo" width="150" height="150" alt="' . esc_attr( $itme_name ) . '">';
	} else {
		$avatar_url = bp_core_fetch_avatar(
			array(
				'item_id'    => $item_id,
				'type'       => 'full',
				'avatar_dir' => 'group-avatars',
				'object'     => 'group',
				'width'      => 150,
				'height'     => 150,
			)
		);
	}
} elseif ( 'friends' === $component ) {
	if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
		$url = bp_members_get_user_url( $secondary_item_id );
	} else {
		$url = bp_core_get_user_domain( $secondary_item_id );
	}
	$itme_name             = bp_core_get_user_displayname( $secondary_item_id );
	$itme_slug             = bp_activity_get_user_mentionname( $secondary_item_id );
	$display_action_button = true;
	$action_button_text    = esc_html__( 'View Profile', 'reign' );
	$cover_url             = bp_attachments_get_attachment(
		'url',
		array(
			'object_dir' => 'members',
			'item_id'    => $secondary_item_id,
		)
	);

	if ( empty( $cover_url ) ) {
		$cover_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
	}

	$avatar_url = bp_core_fetch_avatar(
		array(
			'item_id' => $secondary_item_id,
			'type'    => 'full',
			'width'   => 150,
			'height'  => 150,
			'class'   => 'avatar',
			'id'      => false,
			/* translators: %s: Member display name. */
			'alt'     => sprintf( __( 'Profile picture of %s', 'reign' ), $itme_name ),
		)
	);

} elseif ( 'members' === $component ) {

	if ( 'new_avatar' === $type ) {
		$secondary_item_id     = $user_id;
		$display_action_button = true;
		$action_button_text    = esc_html__( 'View Profile', 'reign' );
	}

	if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
		$url = bp_members_get_user_url( $secondary_item_id );
	} else {
		$url = bp_core_get_user_domain( $secondary_item_id );
	}
	$itme_name = bp_core_get_user_displayname( $secondary_item_id );
	$itme_slug = bp_activity_get_user_mentionname( $secondary_item_id );

	if ( 'new_cover_photo' === $type ) {
		$cover_url = bp_activity_get_meta( $activity_id, 'cover_image', true );
	} else {
		$cover_url = bp_attachments_get_attachment(
			'url',
			array(
				'object_dir' => 'members',
				'item_id'    => $secondary_item_id,
			)
		);
	}

	if ( empty( $cover_url ) ) {
		$cover_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
	}

	$avatar_url = bp_activity_get_meta( $activity_id, 'member_avatar_image', true );
	if ( 'new_avatar' === $type && '' !== $avatar_url ) {
		$avatar_url = '<img loading="lazy" src="' . esc_url( $avatar_url, array( 'data', 'http', 'https' ) ) . '" class="avatar user-' . esc_attr( $item_id ) . '-avatar avatar-150 photo" width="150" height="150" alt="' . esc_attr( $itme_name ) . '">';
	} elseif ( 'new_avatar' === $type && ! $avatar_url ) {
			return;
	} else {
		$avatar_url = bp_core_fetch_avatar(
			array(
				'item_id' => $secondary_item_id,
				'type'    => 'full',
				'width'   => 150,
				'height'  => 150,
				'class'   => 'avatar',
				'id'      => false,
				/* translators: %s: Member display name. */
				'alt'     => sprintf( __( 'Profile picture of %s', 'reign' ), $itme_name ),
			)
		);
	}
}
?>
<div class="reign-user-preview">
	<a href="<?php echo esc_url( $url ); ?>">
		<div class="reign-user-preview-cover">
			<img src="<?php echo esc_url( $cover_url, array( 'data', 'http', 'https' ) ); ?>" alt="<?php echo esc_attr( 'cover-image' ); ?>" loading="lazy" decoding="async"/>
		</div>
	</a>
	<?php if ( 'new_cover_photo' !== $type && 'new_group_cover_photo' !== $type ) : ?>
		<div class="reign-user-short-description">
			<a href="<?php echo esc_url( $url ); ?>" class="item-avatar-group reign-user-avatar reign-user-short-description-avatar">
				<div class="reign-user-avatar-content">
					<?php echo $avatar_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</a>
			<p class="reign-user-short-description-title">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $itme_name ); ?></a>
			</p>
			<?php if ( 'new_avatar' === $type ) : ?>
			<p>
				<a href="<?php echo esc_url( $url ); ?>">@<?php echo esc_html( $itme_slug ); ?></a>
			</p>
				<?php
			endif;

			if ( $display_action_button ) {
				printf(
					'<div class="bp-profile-button">
						<a href="%1$s" class="button large primary button-primary" role="button">%2$s</a>
					</div>',
					esc_url( $url ),
					esc_html( $action_button_text )
				);
			}
			?>
		</div>
		<?php
	endif;

	if ( 'groups' === $component && 'new_group_cover_photo' !== $type && 'new_group_avatar' !== $type ) {
		?>
		<div class="reign-user-preview-footer">
			<div class="reign-user-stats">
				<div class="reign-user-stat">
					<p class="reign-user-stat-title"><?php echo esc_html( groups_get_total_member_count( $item_id ) ); ?></p>
					<p class="reign-user-stat-text"><?php esc_html_e( 'Members', 'reign' ); ?></p>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

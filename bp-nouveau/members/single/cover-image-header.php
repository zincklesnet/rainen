<?php
/**
 * BuddyPress - Users Cover Image Header
 *
 * @since 3.0.0
 * @version 12.0.0
 */

global $wbtm_reign_settings;

// Determine if the header should be displayed
$is_cover_header_displayed = ! bp_is_user_messages()
	&& ! bp_is_user_settings()
	&& ! bp_is_user_notifications()
	&& ! bp_is_user_profile_edit()
	&& ! bp_is_user_change_avatar()
	&& ! bp_is_user_change_cover_image()
	&& ! apply_filters( 'reign_bp_is_current_action', false );

// Member header type class
$member_header_class = isset( $wbtm_reign_settings['reign_buddyextender']['member_header_type'] )
	? $wbtm_reign_settings['reign_buddyextender']['member_header_type']
	: 'wbtm-cover-header-type-1';

$member_header_class = apply_filters( 'wbtm_rth_manage_member_header_class', $member_header_class );

if ( ! $is_cover_header_displayed ) {
	return;
}
?>

<div id="cover-image-container" class="wbtm-member-cover-image-container <?php echo esc_attr( $member_header_class ); ?>">

	<div id="header-cover-image">
		<?php if ( bp_is_my_profile() ) : ?>
			<a href="<?php echo esc_url( bp_get_members_component_link( 'profile', 'change-cover-image' ) ); ?>"
				class="link-change-cover-image bp-tooltip"
				data-bp-tooltip-pos="right"
				data-bp-tooltip="<?php esc_attr_e( 'Change Cover Photo', 'reign' ); ?>">
				<i class="far fa-edit"></i>
			</a>
		<?php endif; ?>
	</div>

	<div id="item-header-cover-image">

		<div class="wbtm-member-info-section"><!-- Main Content -->

			<div id="item-header-avatar">
				<?php if ( bp_is_my_profile() && ! bp_disable_avatar_uploads() ) : ?>
					<a href="<?php bp_members_component_link( 'profile', 'change-avatar' ); ?>"
						class="link-change-profile-image bp-tooltip"
						data-bp-tooltip-pos="up"
						data-bp-tooltip="<?php esc_attr_e( 'Change Profile Photo', 'reign' ); ?>">
						<i class="far fa-edit"></i>
					</a>
				<?php endif; ?>
				<?php bp_displayed_user_avatar( 'type=full' ); ?>
			</div>

			<div id="item-header-content">

				<?php do_action( 'reign_bp_before_displayed_user_mentionname' ); ?>

				<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
					<h2 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h2>
				<?php endif; ?>

				<?php
				ob_start();
				bp_nouveau_member_header_buttons(
					array(
						'container'         => 'ul',
						'button_element'    => 'button',
						'container_classes' => array( 'member-header-actions' ),
					)
				);
				$member_header_buttons_markup = trim( ob_get_clean() );
				$primary_header_button_markup = $member_header_buttons_markup;
				$overflow_header_buttons      = '';

				if ( preg_match( '/<ul\b[^>]*class="[^"]*member-header-actions[^"]*"[^>]*>(.*)<\/ul>/is', $member_header_buttons_markup, $button_list_match ) ) {
					preg_match_all( '/<(li|div)\b[^>]*class="[^"]*generic-button[^"]*"[^>]*>.*?<\/\1>/is', $button_list_match[1], $header_button_matches );

					if ( ! empty( $header_button_matches[0] ) ) {
						$primary_header_button_markup = '<ul class="member-header-actions">' . $header_button_matches[0][0] . '</ul>';

						if ( count( $header_button_matches[0] ) > 1 ) {
							$overflow_header_buttons = implode( '', array_slice( $header_button_matches[0], 1 ) );
						}
					}
				}
				?>

				<div class="wbtm-item-buttons-wrapper member-header-actions-wrap">
					<div id="item-buttons">
						<?php echo wp_kses_post( $primary_header_button_markup ); ?>
					</div>
					<div class="bp_more_options header-dropdown rg-member-actions-overflow" <?php echo empty( $overflow_header_buttons ) ? 'hidden' : ''; ?>>
						<button type="button"
							class="bp_more_options_action"
							aria-label="<?php esc_attr_e( 'More options', 'reign' ); ?>"
							aria-expanded="false">
							<i class="far fa-ellipsis-h" aria-hidden="true"></i>
						</button>
						<div class="bp_more_options_list bp_more_dropdown" aria-hidden="true"><?php echo wp_kses_post( $overflow_header_buttons ); ?></div>
					</div>
				</div>

				<?php bp_nouveau_member_hook( 'before', 'header_meta' ); ?>

				<?php if ( bp_nouveau_member_has_meta() ) : ?>
					<div class="item-meta">
						<?php bp_nouveau_member_meta(); ?>
					</div>

					<?php if ( class_exists( 'BadgeOS' ) && function_exists( 'reign_profile_achievements' ) ) : ?>
						<div class="wbtm-badge">
							<?php reign_profile_achievements(); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php
				if ( function_exists( 'bp_member_type_list' ) ) :
					bp_member_type_list(
						bp_displayed_user_id(),
						array(
							'label'        => array(
								'plural'   => __( 'Member Types', 'reign' ),
								'singular' => __( 'Member Type', 'reign' ),
							),
							'list_element' => 'span',
						)
					);
				endif;
				?>

			</div><!-- #item-header-content -->

		</div><!-- .wbtm-member-info-section -->

		<div class="wbtm-cover-extra-info-section">
			<?php do_action( 'reign_member_extra_info_section' ); ?>
		</div>

	</div><!-- #item-header-cover-image -->

</div><!-- #cover-image-container -->

<?php do_action( 'wbtm_after_cover_imager_container' ); ?>

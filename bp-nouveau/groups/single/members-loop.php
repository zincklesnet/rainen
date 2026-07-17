<?php
/**
 * Group Members Loop template
 *
 * @since 3.0.0
 * @version 3.2.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<?php
$loops_layout = 1;
// Customizer preview data is injected by BuddyPress inside the preview iframe; no form processing occurs here.
// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Customizer live-preview read; value cast with intval(), no state change.
if ( isset( $_POST['customized']['bp_nouveau_appearance_members_group_layout'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Customizer live-preview read; value cast with intval(), no state change.
	$loops_layout = intval( $_POST['customized']['bp_nouveau_appearance_members_group_layout'] );
} else {
	$bp_nouveau_appearance = bp_get_option( 'bp_nouveau_appearance', array() );
	if ( ! isset( $bp_nouveau_appearance['members_group_layout'] ) ) {
		$loops_layout = 1;
	} else {
		$loops_layout = $bp_nouveau_appearance['members_group_layout'];
	}
}
?>

<?php if ( 1 === $loops_layout ) : ?>

	<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) ) ) : ?>

		<?php bp_nouveau_group_hook( 'before', 'members_content' ); ?>

		<?php bp_nouveau_pagination( 'top' ); ?>

		<?php bp_nouveau_group_hook( 'before', 'members_list' ); ?>

		<ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?>">

			<?php
			while ( bp_group_members() ) :
				bp_group_the_member();
				?>

				<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr( bp_get_group_member_id() ); ?>" data-bp-item-component="members">

					<div class="list-wrap">

						<div class="item-avatar">
							<a href="<?php bp_group_member_domain(); ?>">
								<?php bp_group_member_avatar(); ?>
							</a>
						</div>

						<div class="item">

							<div class="item-block">
								<h3 class="list-title member-name"><?php bp_group_member_link(); ?></h3>

								<p class="joined item-meta">
									<?php bp_group_member_joined_since(); ?>
								</p>

								<?php bp_nouveau_group_hook( '', 'members_list_item' ); ?>

								<?php bp_nouveau_members_loop_buttons(); ?>
							</div>

						</div>

					</div><!-- // .list-wrap -->

				</li>

			<?php endwhile; ?>

		</ul>

		<?php bp_nouveau_group_hook( 'after', 'members_list' ); ?>

		<?php bp_nouveau_pagination( 'bottom' ); ?>

		<?php bp_nouveau_group_hook( 'after', 'members_content' ); ?>

		<?php
	else :

		bp_nouveau_user_feedback( 'group-members-none' );

	endif;
	?>

<?php else : ?>

	<?php
	global $wbtm_reign_settings;
	$member_directory_type = $wbtm_reign_settings['reign_buddyextender']['member_directory_type'] ?? 'wbtm-member-directory-type-2';

	$img_class = ( 'wbtm-member-directory-type-4' === $member_directory_type ) ? 'img-card' : '';
	?>

	<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) ) ) : ?>

		<?php bp_nouveau_group_hook( 'before', 'members_content' ); ?>

		<?php bp_nouveau_pagination( 'top' ); ?>

		<?php bp_nouveau_group_hook( 'before', 'members_list' ); ?>

		<ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?> <?php echo esc_attr( $member_directory_type ); ?> rg-member-list">

			<?php
			while ( bp_group_members() ) :
				bp_group_the_member();
				?>
				<?php $user_id = bp_get_member_user_id(); ?>

				<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr( bp_get_group_member_id() ); ?>" data-bp-item-component="members">

					<div class="list-wrap">

						<?php do_action( 'reign_before_member_avatar_member_directory' ); ?>

						<div class="item-avatar">
							<?php
							if ( 'wbtm-member-directory-type-4' === $member_directory_type ) {
								echo '<figure class="img-dynamic aspect-ratio avatar">';
							}
							?>
							<a class="<?php echo esc_attr( $img_class ); ?>" href="<?php bp_group_member_domain(); ?>">
								<?php bp_group_member_avatar(); ?>
							</a>
							<?php
							if ( 'wbtm-member-directory-type-4' === $member_directory_type ) {
								echo '</figure>';
							}
							?>
						</div>

						<?php
						if ( 'wbtm-member-directory-type-4' === $member_directory_type ) {
							echo '<div class="item-wrapper">';
						}
						?>
						<div class="item">

							<div class="item-block">
								<h3 class="list-title member-name"><?php bp_group_member_link(); ?></h3>

								<p class="joined item-meta">
									<?php bp_group_member_joined_since(); ?>
								</p>

								<?php bp_nouveau_group_hook( '', 'members_list_item' ); ?>

								<?php do_action( 'reign_bp_nouveau_directory_members_item' ); ?>

							</div>

						</div>

						<?php
						if ( 'wbtm-member-directory-type-2' === $member_directory_type || 'wbtm-member-directory-type-3' === $member_directory_type || 'wbtm-member-directory-type-4' === $member_directory_type ) {
							reign_buddypress_member_primary_actions();
						}
						?>
						<!-- Added actions buttons outside "item" section :: Start  -->
						<div class="action-wrap">
							<i class="far fa-plus-circle"></i>
							<?php
							if ( 'wbtm-member-directory-type-1' === $member_directory_type ) {
								bp_nouveau_members_loop_buttons(
									array(
										'container'      => 'ul',
										'button_element' => 'button',
									)
								);
							}
							?>
						</div>
						<!-- Added actions buttons outside "item" section :: End  -->
						<?php
						if ( 'wbtm-member-directory-type-4' === $member_directory_type ) {
							echo '</div>';
						}
						?>

						<?php if ( 'wbtm-member-directory-type-2' === $member_directory_type || 'wbtm-member-directory-type-3' === $member_directory_type || 'wbtm-member-directory-type-4' === $member_directory_type ) : ?>
							<div class="bp-activity-more-options-wrap">
								<a href="#" class="bp-activity-more-options-action bp-members-more-options-action bp-tooltip" data-bp-tooltip-pos="<?php echo ( 'wbtm-member-directory-type-4' === $member_directory_type ) ? 'left' : 'up'; ?>" data-bp-tooltip="<?php esc_attr_e( 'More Options', 'reign' ); ?>" aria-label="<?php esc_attr_e( 'More Options', 'reign' ); ?>" aria-haspopup="true" aria-expanded="false">
									<i class="fa fa-ellipsis-h" aria-hidden="true"></i>
								</a>
								<div class="bp-activity-more-options">
									<?php
									reign_additional_member_actions()
									?>
								</div>
							</div><!-- .bb_more_options -->
						<?php endif; ?>

					</div><!-- // .list-wrap -->

				</li>

			<?php endwhile; ?>

		</ul>

		<?php bp_nouveau_group_hook( 'after', 'members_list' ); ?>

		<?php bp_nouveau_pagination( 'bottom' ); ?>

		<?php bp_nouveau_group_hook( 'after', 'members_content' ); ?>

		<?php
	else :

		bp_nouveau_user_feedback( 'group-members-none' );

	endif;
	?>

<?php endif; ?>

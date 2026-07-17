<?php
/**
 * BuddyPress - Groups Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<?php add_filter( 'bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2 ); ?>

<?php bp_nouveau_before_loop(); ?>

<?php if ( bp_get_current_group_directory_type() ) : ?>
	<div class="bp-feedback info">
		<span class="bp-icon" aria-hidden="true"></span>
		<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
	</div>
<?php endif; ?>

<?php
global $wbtm_reign_settings;
$group_directory_type = $wbtm_reign_settings['reign_buddyextender']['group_directory_type'] ?? 'wbtm-group-directory-type-2';
$img_class            = ( 'wbtm-group-directory-type-4' === $group_directory_type ) ? 'img-card' : '';

$cover_class        = ! bb_platform_group_element_enable( 'cover-images' ) ? 'bb-cover-disabled' : 'bb-cover-enabled';
$meta_privacy       = ! bb_platform_group_element_enable( 'group-privacy' ) ? 'meta-privacy-hidden' : '';
$meta_group_type    = ! bb_platform_group_element_enable( 'group-type' ) ? 'meta-group-type-hidden' : '';
$group_members      = ! bb_platform_group_element_enable( 'members' ) ? 'group-members-hidden' : '';
$join_button        = ! bb_platform_group_element_enable( 'join-buttons' ) ? 'group-join-button-hidden' : '';
$group_alignment    = bb_platform_group_grid_style( 'left' );
$group_cover_height = function_exists( 'bb_get_group_cover_image_height' ) ? bb_get_group_cover_image_height() : 'small';

?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<ul id="groups-list" class="
	<?php
	bp_nouveau_loop_classes();
	echo esc_attr( ' ' . $cover_class . ' ' . $group_alignment );
	?>
	<?php echo esc_attr( $group_directory_type ); ?> rg-group-list groups-dir-list">

		<?php
		while ( bp_groups() ) :
			bp_the_group();

			$bp_group_id = bp_get_group_id();
			?>

			<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr( $bp_group_id ); ?>" data-bp-item-component="groups">
				<div class="list-wrap">

					<?php
					/**
					 * Fires inside the listing of an individual group listing item.
					 * Added by Reign Theme
					 *
					 * @since 1.0.7
					 */
					do_action( 'reign_before_group_avatar_group_directory' );
					?>

					<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
						<div class="item-avatar">
							<?php
							if ( 'wbtm-group-directory-type-4' === $group_directory_type ) {
								echo '<figure class="img-dynamic aspect-ratio avatar">';
							}
							?>
							<?php
							if ( ! bp_disable_group_avatar_uploads() && bb_platform_group_element_enable( 'avatars' ) ) :
								?>
								<a href="<?php bp_group_permalink(); ?>" class="group-avatar-wrap <?php echo esc_attr( $img_class ); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="group-content-wrap">
						<div class="item <?php echo esc_attr( $group_members . ' ' . $join_button ); ?>">

							<div class="item-block">

								<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>

								<div class="item-meta-wrap <?php echo esc_attr( bb_platform_group_element_enable( 'last-activity' ) || empty( $meta_privacy ) || empty( $meta_group_type ) ? 'has-meta' : 'meta-hidden' ); ?> ">

									<?php if ( bp_nouveau_group_has_meta() ) : ?>

										<p class="item-meta group-details <?php echo esc_attr( $meta_privacy . ' ' . $meta_group_type ); ?>">
											<?php
											$meta = bp_nouveau_get_group_meta();
											echo wp_kses_post( $meta['status'] );
											?>
										</p>
										<?php
									endif;

									if ( bb_platform_group_element_enable( 'last-activity' ) ) {
										echo '<p class="last-activity item-meta">' .
										sprintf(
												/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
											esc_attr__( 'Active %s', 'reign' ),
											wp_kses_post( bp_get_group_last_active() )
										) .
										'</p>';
									}
									?>

								</div>

							</div>

							<?php if ( bb_platform_group_element_enable( 'group-descriptions' ) ) { ?>
								<div class="item-desc group-item-desc only-list-view"><?php bp_group_description_excerpt( false, 150 ); ?></div>
							<?php } ?>

							<?php bp_nouveau_groups_loop_item(); ?>

						</div>

						<?php do_action( 'reign_bp_directory_groups_data' ); ?>

						<div class="group-admins-wrap">
							<?php reign_bp_group_list_admins(); ?>
						</div>

						<div class="group-members-wrap">
							<?php bb_groups_loop_members(); ?>
						</div>

						<!-- Added action buttons here -->
						<?php
						if ( 'wbtm-group-directory-type-3' === $group_directory_type ) {
							echo '<div class="action-wrap"><i class="far fa-plus-circle"></i>';
						}
						?>
						<div class="group-footer-wrap <?php echo esc_attr( $group_members . ' ' . $join_button ); ?>">
							<?php if ( bb_platform_group_element_enable( 'join-buttons' ) ) { ?>
								<div class="groups-loop-buttons footer-button-wrap"><?php bp_nouveau_groups_loop_buttons(); ?></div>
							<?php } ?>
						</div>
						<?php
						if ( 'wbtm-group-directory-type-3' === $group_directory_type ) {
							echo '</div>';
						}
						?>
					</div>

				</div>
			</li>

		<?php endwhile; ?>

	</ul>

	<!-- Leave Group confirmation popup -->
	<div class="bb-leave-group-popup bb-action-popup" style="display: none">
		<transition name="modal">
			<div class="modal-mask bb-white bbm-model-wrap">
				<div class="modal-wrapper">
					<div class="modal-container">
						<header class="bb-model-header">
							<h4><span class="target_name"><?php esc_html_e( 'Leave Group', 'reign' ); ?></span></h4>
							<a class="bb-close-leave-group bb-model-close-button" href="#">
								<span class="bb-icon-l bb-icon-times"></span>
							</a>
						</header>
						<div class="bb-leave-group-content bb-action-popup-content">
							<p><?php esc_html_e( 'Are you sure you want to leave ', 'reign' ); ?><span class="bb-group-name"></span>?</p>
						</div>
						<footer class="bb-model-footer flex align-items-center">
							<a class="bb-close-leave-group bb-close-action-popup" href="#"><?php esc_html_e( 'Cancel', 'reign' ); ?></a>
							<a class="button push-right bb-confirm-leave-group" href="#"><?php esc_html_e( 'Confirm', 'reign' ); ?></a>
						</footer>

					</div>
				</div>
			</div>
		</transition>
	</div> <!-- .bb-leave-group-popup -->

	<?php bp_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php bp_nouveau_user_feedback( 'groups-loop-none' ); ?>

<?php endif; ?>

<?php
bp_nouveau_after_loop();

remove_filter( 'bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2 );
?>

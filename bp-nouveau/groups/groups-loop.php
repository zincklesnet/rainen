<?php
/**
 * BuddyPress - Groups Loop
 *
 * @since 3.0.0
 * @version 12.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<?php
$loops_layout = 1;
// Customizer preview data is injected by BuddyPress inside the preview iframe; no form processing occurs here.
// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Customizer live-preview read; value cast with intval(), no state change.
if ( isset( $_POST['customized']['bp_nouveau_appearance_groups_layout'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Customizer live-preview read; value cast with intval(), no state change.
	$loops_layout = intval( $_POST['customized']['bp_nouveau_appearance_groups_layout'] );
} else {
	$bp_nouveau_appearance = bp_get_option( 'bp_nouveau_appearance', array() );
	if ( ! isset( $bp_nouveau_appearance['groups_layout'] ) ) {
		$loops_layout = 1;
	} else {
		$loops_layout = $bp_nouveau_appearance['groups_layout'];
	}
}
?>

<?php if ( 1 === $loops_layout ) : ?>

	<?php bp_nouveau_before_loop(); ?>

	<?php if ( bp_get_current_group_directory_type() ) : ?>
		<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
	<?php endif; ?>

	<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

		<?php bp_nouveau_pagination( 'top' ); ?>

		<ul id="groups-list" class="<?php bp_nouveau_loop_classes(); ?>">

			<?php
			while ( bp_groups() ) :
				bp_the_group();
				?>

				<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups">
					<div class="list-wrap">

						<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
							<div class="item-avatar"> 
								<?php if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) : ?>
									<a href="<?php bp_group_url(); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
								<?php else : ?>
									<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<div class="item">

							<div class="item-block">

								<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>

								<?php if ( bp_nouveau_group_has_meta() ) : ?>

									<p class="item-meta group-details"><?php bp_nouveau_the_group_meta( array( 'keys' => array( 'status', 'count' ) ) ); ?></p>

								<?php endif; ?>

								<p class="last-activity item-meta">
									<?php
										printf(
											/* translators: %s: last activity timestamp (e.g. "Active 1 hour ago") */
											esc_html__( 'Active %s', 'reign' ),
											sprintf(
												'<span data-livestamp="%1$s">%2$s</span>',
												esc_attr( bp_core_get_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ) ),
												esc_html( bp_get_group_last_active() )
											)
										);
									?>
								</p>

							</div>

							<div class="group-desc"><p><?php bp_nouveau_group_description_excerpt(); ?></p></div>

							<?php bp_nouveau_groups_loop_item(); ?>

							<?php bp_nouveau_groups_loop_buttons(); ?>

						</div>


					</div>
				</li>

			<?php endwhile; ?>

		</ul>

		<?php bp_nouveau_pagination( 'bottom' ); ?>

	<?php else : ?>

		<?php bp_nouveau_user_feedback( 'groups-loop-none' ); ?>

	<?php endif; ?>

	<?php
	bp_nouveau_after_loop();
	?>

<?php else : ?>

	<?php
	bp_nouveau_before_loop();

	global $wbtm_reign_settings;
	$group_directory_type = $wbtm_reign_settings['reign_buddyextender']['group_directory_type'] ?? 'wbtm-group-directory-type-2';
	$img_class            = ( 'wbtm-group-directory-type-4' === $group_directory_type ) ? 'img-card' : '';
	?>

	<?php if ( bp_get_current_group_directory_type() ) : ?>
		<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
	<?php endif; ?>

	<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

		<?php bp_nouveau_pagination( 'top' ); ?>

		<ul id="groups-list" class="<?php bp_nouveau_loop_classes(); ?> <?php echo esc_attr( $group_directory_type ); ?> rg-group-list">

			<?php
			while ( bp_groups() ) :
				bp_the_group();
				?>

				<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups">
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
								<?php if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) : ?>
									<a class="item-avatar-group <?php echo esc_attr( $img_class ); ?>" href="<?php bp_group_url(); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
								<?php else : ?>
									<a class="item-avatar-group <?php echo esc_attr( $img_class ); ?>" href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
								<?php endif; ?>
								<?php
								if ( 'wbtm-group-directory-type-4' === $group_directory_type ) {
									echo '</figure>';
								}
								?>
							</div>
						<?php endif; ?>

						<div class="group-content-wrap">
							<div class="item">

								<div class="item-block">

									<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>

									<?php if ( bp_nouveau_group_has_meta() ) : ?>

										<p class="item-meta group-details"><?php bp_nouveau_the_group_meta( array( 'keys' => array( 'status', 'count' ) ) ); ?></p>

									<?php endif; ?>

									<p class="last-activity item-meta">
										<?php
											printf(
												/* translators: %s: last activity timestamp (e.g. "Active 1 hour ago") */
												esc_html__( 'Active %s', 'reign' ),
												sprintf(
													'<span data-livestamp="%1$s">%2$s</span>',
													esc_attr( bp_core_get_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ) ),
													esc_html( bp_get_group_last_active() )
												)
											);
										?>
									</p>

								</div>

								<div class="group-desc"><p><?php bp_nouveau_group_description_excerpt(); ?></p></div>

								<?php bp_nouveau_groups_loop_item(); ?>

							</div>

							<?php do_action( 'reign_bp_directory_groups_data' ); ?>

							<div class="group-admins-wrap">
								<?php
								if ( function_exists( 'reign_bp_group_list_admins' ) ) :
									reign_bp_group_list_admins();
								endif;
								?>
							</div>

							<!-- Added action buttons here -->
							<?php
							if ( 'wbtm-group-directory-type-3' === $group_directory_type ) {
								echo '<div class="action-wrap"><i class="far fa-plus-circle"></i>';
							}
							bp_nouveau_groups_loop_buttons();
							if ( 'wbtm-group-directory-type-3' === $group_directory_type ) {
								echo '</div>';
							}
							?>
						</div>

					</div>
				</li>

			<?php endwhile; ?>

		</ul>

		<?php bp_nouveau_pagination( 'bottom' ); ?>

	<?php else : ?>

		<?php bp_nouveau_user_feedback( 'groups-loop-none' ); ?>

	<?php endif; ?>

	<?php
	bp_nouveau_after_loop();
	?>

<?php endif; ?>

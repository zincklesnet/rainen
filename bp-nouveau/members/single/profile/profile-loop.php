<?php
/**
 * BuddyPress - Members Profile Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$edit_profile_link = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() . '/edit/group/' );
?>

<?php bp_nouveau_xprofile_hook( 'before', 'loop_content' ); ?>

<?php if ( bp_has_profile() ) : ?>

	<?php
	while ( bp_profile_groups() ) :
		bp_the_profile_group();
		?>

		<?php
		if ( bp_profile_group_has_fields() ) :

			bp_nouveau_xprofile_hook( 'before', 'field_content' );
			?>
			<div class="group-separator-block">
				<header class="profile-loop-header profile-header flex align-items-center">
					<h3 class="entry-title rg-profile-title"><?php bp_the_profile_group_name(); ?></h3>

					<?php
					if ( bp_is_my_profile() ) {
						?>
						<a href="<?php echo esc_url( $edit_profile_link . bp_get_the_profile_group_id() ); ?>" class="push-right button outline small"><?php esc_html_e( 'Edit', 'reign' ); ?></a>
						<?php
					}
					?>
				</header>

				<div class="bp-widget <?php bp_the_profile_group_slug(); ?>">

					<table class="profile-fields bp-tables-user">

						<?php
						while ( bp_profile_fields() ) :
							bp_the_profile_field();
							?>

							<?php if ( bp_field_has_data() ) : ?>

								<tr<?php bp_field_css_class(); ?>>

									<td class="label"><?php bp_the_profile_field_name(); ?></td>

									<td class="data"><?php bp_the_profile_field_value(); ?></td>

								</tr>

							<?php endif; ?>

							<?php bp_nouveau_xprofile_hook( '', 'field_item' ); ?>

						<?php endwhile; ?>

					</table>
				</div>
			</div>

			<?php bp_nouveau_xprofile_hook( 'after', 'field_content' ); ?>

		<?php endif; ?>

	<?php endwhile; ?>

	<?php bp_nouveau_xprofile_hook( '', 'field_buttons' ); ?>

	<?php
else :
	?>

	<div class="info bp-feedback">
		<span class="bp-icon" aria-hidden="true"></span>
		<p>
			<?php
			if ( bp_is_my_profile() ) {
				esc_html_e( 'You have not yet added details to your profile.', 'reign' );
			} else {
				esc_html_e( 'This member has not yet added details to their profile.', 'reign' );
			}
			?>
		</p>
	</div>

	<?php
	if ( bp_is_my_profile() ) :
		if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) :
			?>
		<div class="rg-buddypress-profile-edit-link">
			<nav class="<?php bp_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Profile menu', 'reign' ); ?>">
				<ul class="subnav">
					<?php bp_get_template_part( 'members/single/parts/item-subnav' ); ?>
				</ul>
			</nav><!-- .item-list-tabs -->
		</div>
			<?php
		endif;
	endif;
	?>

	<?php
endif;
bp_nouveau_xprofile_hook( 'after', 'loop_content' );


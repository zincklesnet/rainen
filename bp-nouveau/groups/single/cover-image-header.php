<?php
/**
 * BuddyPress - Groups Cover Image Header.
 *
 * @since 3.0.0
 * @version 12.0.0
 */
?>
<?php
global $wbtm_reign_settings;
$group_header_class = isset( $wbtm_reign_settings['reign_buddyextender']['group_header_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['group_header_type'] : 'wbtm-cover-header-type-1';
$group_header_class = apply_filters( 'wbtm_rth_manage_group_header_class', $group_header_class );

if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
	$group_link = bp_get_group_url();
} else {
	$group_link = bp_get_group_permalink();
}
$admin_link       = trailingslashit( $group_link . 'admin' );
$group_avatar     = trailingslashit( $admin_link . 'group-avatar' );
$group_cover_link = trailingslashit( $admin_link . 'group-cover-image' );

?>
<div id="cover-image-container" class="wbtm-group-cover-image-container <?php echo esc_attr( $group_header_class ); ?>">

	<div id="header-cover-image">
		<?php
		if ( bp_group_use_cover_image_header() ) {
			?>
			<?php if ( bp_is_item_admin() ) { ?>
				<a href="<?php echo esc_url( $group_cover_link ); ?>" class="link-change-cover-image bp-tooltip" data-bp-tooltip-pos="right" data-bp-tooltip="<?php esc_attr_e( 'Change Cover Photo', 'reign' ); ?>">
					<i class="far fa-edit"></i>
				</a>
				<?php
			}
		}
		?>
	</div>

	<div id="item-header-cover-image">

		<div class="wbtm-member-info-section"><!-- custom wrapper for main content :: start -->

			<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
				<div id="item-header-avatar">
					<?php if ( bp_is_item_admin() ) { ?>
						<a href="<?php echo esc_url( $group_avatar ); ?>" class="link-change-profile-image bp-tooltip" data-bp-tooltip-pos="up" data-bp-tooltip="<?php esc_attr_e( 'Change Group Photo', 'reign' ); ?>">
							<i class="far fa-edit"></i>
						</a>
					<?php } ?>
					<?php bp_group_avatar(); ?>
				</div><!-- #item-header-avatar -->
			<?php endif; ?>

			<?php if ( ! bp_nouveau_groups_front_page_description() ) : ?>
				<div id="item-header-content">

					<?php bp_nouveau_group_hook( 'before', 'header_meta' ); ?>

					<?php if ( bp_nouveau_group_has_meta( 'status' ) ) : ?>
						<p class="highlight group-status"><strong><?php echo esc_html( bp_nouveau_the_group_meta( array( 'keys' => 'status' ) ) ); ?></strong></p>
					<?php endif; ?>

					<p class="activity">
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

					<?php
					bp_group_type_list(
						bp_get_group_id(),
						array(
							'label'        => array(
								'plural'   => __( 'Group Types', 'reign' ),
								'singular' => __( 'Group Type', 'reign' ),
							),
							'list_element' => 'span',
						)
					);
					?>

					<?php if ( bp_nouveau_group_has_meta_extra() ) : ?>
						<div class="item-meta">

							<?php bp_nouveau_the_group_meta( array( 'keys' => 'extra' ) ); ?>

						</div><!-- .item-meta -->
					<?php endif; ?>

					<div class="wbtm-item-buttons-wrapper">
						<div id="item-buttons">
							<?php bp_nouveau_group_header_buttons(); ?>
						</div><!-- #item-buttons -->
					</div>

				</div><!-- #item-header-content -->
			<?php endif; ?>

			<?php if ( bp_nouveau_groups_front_page_description() ) : ?>
				<div id="item-header-content">
					<div class="item-title"><h2 class="bp-group-title"><?php echo esc_html( bp_get_group_name() ); ?></h2></div>
				</div>
			<?php endif; ?>

			<?php bp_get_template_part( 'groups/single/parts/header-item-actions' ); ?>

		</div><!-- custom wrapper for main content :: end -->

		<!-- custom section for extra content :: start -->
		<div class="wbtm-cover-extra-info-section">
			<?php
			/**
			 * Fires after main content to show extra information.
			 *
			 * @since 1.0.7
			 */
			do_action( 'reign_group_extra_info_section' );
			?>
		</div>
		<!-- custom section for extra content :: start -->

	</div><!-- #item-header-cover-image -->

</div><!-- #cover-image-container -->

<?php if ( ! bp_nouveau_groups_front_page_description() && bp_nouveau_group_has_meta( 'description' ) ) : ?>
	<div class="desc-wrap">
		<div class="group-description">
			<?php bp_group_description(); ?>
		</div><!-- //.group_description -->
	</div>
<?php endif; ?>

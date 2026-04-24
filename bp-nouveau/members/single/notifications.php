<?php
/**
 * BuddyPress - Users Notifications
 *
 * @since 3.0.0
 * @version 12.0.0
 */

if ( class_exists( 'Youzify' ) ) {
	?>
	<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'reign' ); ?>" role="navigation">
		<ul>
			<?php bp_get_options_nav(); ?>

			<li id="members-order-select" class="last filter">
				<?php bp_notifications_sort_order_form(); ?>
			</li>
		</ul>
	</div>

	<?php
	switch ( bp_current_action() ) :

		case 'unread':
			bp_get_template_part( 'members/single/notifications/unread' );
			break;

		case 'read':
			bp_get_template_part( 'members/single/notifications/read' );
			break;

		// Any other actions.
		default:
			bp_get_template_part( 'members/single/plugins' );
			break;
	endswitch;
} else {
	$profile_link = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() );
	?>

	<header class="entry-header notifications-header">
		<h1 class="entry-title rg-profile-title"><?php esc_html_e( 'Notifications', 'reign' ); ?></h1>
		<?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>
		<a href="<?php echo esc_url( $profile_link ); ?>" class="push-right button outline small"><i class="far fa-user"></i> <?php esc_attr_e( 'View Profile', 'reign' ); ?></a>
	</header>

	<nav class="<?php bp_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Notification Menu', 'reign' ); ?>">
		<ul id="member-secondary-nav" class="subnav">
			<?php bp_get_template_part( 'members/single/parts/item-subnav' ); ?>
		</ul>

		<?php bp_nouveau_member_hook( '', 'secondary_nav' ); ?>
	</nav>

	<?php
	switch ( bp_current_action() ) :

		case 'unread':
		case 'read':
			?>

			<div id="notifications-user-list" class="notifications dir-list" data-bp-list="notifications">
				<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'member-notifications-loading' ); ?></div>
			</div><!-- #groups-dir-list -->

			<?php
			break;

		// Any other actions.
		default:
			bp_get_template_part( 'members/single/plugins' );
			break;
	endswitch;
}

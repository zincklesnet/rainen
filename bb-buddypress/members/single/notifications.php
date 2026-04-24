<?php
/**
 * BuddyPress - Users Notifications
 *
 * @since 3.0.0
 * @version 3.0.0
 */

$profile_link         = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() );
$is_send_ajax_request = bb_is_send_ajax_request();
?>

<header class="entry-header notifications-header">
	<h1 class="entry-title rg-profile-title"><?php esc_html_e( 'Notifications', 'reign' ); ?></h1>
	<?php bp_get_template_part( 'common/search-and-filters-bar' ); ?>
	<a href="<?php echo esc_url( $profile_link ); ?>" class="push-right button outline small"><i class="far fa-user"></i> <?php esc_attr_e( 'View Profile', 'reign' ); ?></a>
</header>

<?php bp_get_template_part( 'members/single/parts/item-subnav' ); ?>

<?php
switch ( bp_current_action() ) :

	case 'unread':
	case 'read':
		?>

		<div id="notifications-user-list" class="notifications dir-list" data-bp-list="notifications">
			<?php
			if ( $is_send_ajax_request ) {
				echo '<div id="bp-ajax-loader">';
				bp_nouveau_user_feedback( 'member-notifications-loading' );
				echo '</div>';
			} else {
				bp_get_template_part( 'members/single/notifications/notifications-loop' );
			}
			?>
		</div><!-- #groups-dir-list -->

		<?php
		break;

	// Any other actions.
	default:
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;

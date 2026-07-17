<?php
/**
 * BuddyPress - Users Profile
 *
 * @since 3.0.0
 * @version 12.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$profile_link = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() );

// Determine if we're in an edit context
$is_edit_context = bp_is_user_profile_edit()
	|| bp_is_user_change_avatar()
	|| bp_is_user_change_cover_image()
	|| apply_filters( 'reign_bp_is_current_action', false );
?>

<?php if ( $is_edit_context ) : ?>
	<header class="profile-header flex align-items-center">
		<h1 class="entry-title rg-profile-title"><?php esc_html_e( 'Edit Profile', 'reign' ); ?></h1>
		<a href="<?php echo esc_url( $profile_link ); ?>" class="push-right button outline small">
			<i class="far fa-user"></i> <?php esc_html_e( 'View Profile', 'reign' ); ?>
		</a>
	</header>
<?php endif; ?>

<div class="bp-profile-wrapper <?php echo ( bp_is_user_profile() && ! $is_edit_context ) ? esc_attr( 'need-separator' ) : ''; ?>">

	<?php if ( $is_edit_context ) : ?>
		<nav class="<?php bp_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Profile menu', 'reign' ); ?>">
			<ul id="member-secondary-nav" class="subnav">
				<?php bp_get_template_part( 'members/single/parts/item-subnav' ); ?>
			</ul>
			<?php bp_nouveau_member_hook( '', 'secondary_nav' ); ?>
		</nav>
	<?php endif; ?>

	<div class="bp-profile-content">

		<?php bp_nouveau_member_hook( 'before', 'profile_content' ); ?>

		<div class="profile <?php echo esc_attr( bp_current_action() ); ?>">

			<?php
			switch ( bp_current_action() ) :
				case 'edit':
					bp_get_template_part( 'members/single/profile/edit' );
					break;

				case 'change-avatar':
					bp_get_template_part( 'members/single/profile/change-avatar' );
					break;

				case 'change-cover-image':
					bp_get_template_part( 'members/single/profile/change-cover-image' );
					break;

				case 'public':
					if ( bp_is_active( 'xprofile' ) ) {
						bp_get_template_part( 'members/single/profile/profile-loop' );
					} else {
						bp_get_template_part( 'members/single/profile/profile-wp' );
					}
					break;

				default:
					bp_get_template_part( 'members/single/plugins' );
					break;
			endswitch;
			?>

		</div><!-- .profile -->

		<?php bp_nouveau_member_hook( 'after', 'profile_content' ); ?>

	</div><!-- .bp-profile-content -->
</div><!-- .bp-profile-wrapper -->

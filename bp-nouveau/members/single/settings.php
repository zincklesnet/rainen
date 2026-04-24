<?php
/**
 * BuddyPress - Users Settings
 *
 * @version 12.0.0
 */

$profile_link = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() );

?>

<header class="profile-header settings-header">
	<h1 class="entry-title settings-title rg-profile-title"><?php esc_html_e( 'Account Settings', 'reign' ); ?></h1>
	<a href="<?php echo esc_url( $profile_link ); ?>" class="push-right button outline small"><i class="far fa-user"></i> <?php esc_attr_e( 'View Profile', 'reign' ); ?></a>
</header>

<div class="bp-profile-wrapper bp-settings-container">
	<?php if ( bp_core_can_edit_settings() ) : ?>
		<nav class="<?php bp_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Settings menu', 'reign' ); ?>">
			<ul id="member-secondary-nav" class="subnav">
				<?php bp_get_template_part( 'members/single/parts/item-subnav' ); ?>
			</ul>

			<?php bp_nouveau_member_hook( '', 'secondary_nav' ); ?>
		</nav>
		<?php
	endif;
	?>
	<div class="bp-profile-content bp-settings-content">
		<?php
		switch ( bp_current_action() ) :
			case 'notifications':
				bp_get_template_part( 'members/single/settings/notifications' );
				break;
			case 'capabilities':
				bp_get_template_part( 'members/single/settings/capabilities' );
				break;
			case 'delete-account':
				bp_get_template_part( 'members/single/settings/delete-account' );
				break;
			case 'general':
				bp_get_template_part( 'members/single/settings/general' );
				break;
			case 'profile':
				bp_get_template_part( 'members/single/settings/profile' );
				break;
			case 'invites':
				bp_get_template_part( 'members/single/settings/group-invites' );
				break;
			case 'data':
				bp_get_template_part( 'members/single/settings/data' );
				break;
			default:
				bp_get_template_part( 'members/single/plugins' );
				break;
		endswitch;
		?>
	</div>
</div>

<?php
/**
 * Profile Screen
 *
 * @package Reign
 */

$user_ID = get_current_user_id();

if ( ! $user_ID ) {
	return;
}

$use_buddypress = reign_BuddyPress();

$author_name = wp_get_current_user()->display_name;

if ( $use_buddypress ) {

	if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
		$author_url = bp_members_get_user_url( $user_ID );
	} else {
		$author_url = bp_core_get_user_domain( $user_ID );
	}
	$author_cover_image = bp_attachments_get_attachment(
		'url',
		array(
			'object_dir' => 'members',
			'item_id'    => $user_ID,
		)
	);
} else {
	$author_url         = get_author_posts_url( $user_ID );
	$author_cover_image = '';
}

$author_cover_image = $author_cover_image ? "background-image: url({$author_cover_image})" : '';
?>

<div class="reign-module reign-login-form">
	<div class="user-welcomeback">
		<div class="featured-background" style="<?php echo esc_attr( $author_cover_image ); ?>"></div>
		<div class="user-active">
			<a href="<?php echo esc_url( $author_url ); ?>" class="author-thumb">
				<?php echo get_avatar( $user_ID, 90 ); ?>
			</a>
			<div class="author-content">			
				<?php esc_html_e( 'Welcome Back', 'reign' ); ?>
				<a href="<?php echo esc_url( $author_url ); ?>" class="author-name"><?php echo $author_name; ?></a>!
			</div>
		</div>
		<?php if ( $use_buddypress ) { ?>
			<div class="links">
			<?php if ( bp_is_active( 'activity' ) ) { ?>
				<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_activity_slug() ); ?>" class="link-item">
					<i class="link-item-icon far fa-comments"></i>
					<div class="title"><?php esc_html_e( 'Activity', 'reign' ); ?></div>
					<div class="sup-title"><?php esc_html_e( 'Review your activity!', 'reign' ); ?></div>
				</a>
			<?php } ?>

			<?php if ( bp_is_active( 'settings' ) ) { ?>
				<a href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_settings_slug() ); ?>" class="link-item">
					<i class="link-item-icon far fa-user-cog"></i>
					<div class="title"><?php esc_html_e( 'Settings', 'reign' ); ?></div>
					<div class="sup-title"><?php esc_html_e( 'Manage your preferences!', 'reign' ); ?></div>
				</a>
			<?php } ?>
			</div>
		<?php } ?>
		<div class="reign-block-content">
			<a href="<?php echo esc_url( $author_url ); ?>" class="btn rg-action button full-width">
				<?php esc_html_e( 'Go to your Profile Page', 'reign' ); ?>
			</a>
		</div>
	</div>
</div>
<?php
/**
 * BuddyPress - Members Home
 *
 * @since 1.0.0
 * @version 3.0.0
 */

global $wbtm_reign_settings;

// Member header position
$member_header_position = isset( $wbtm_reign_settings['reign_buddyextender']['member_header_position'] )
	? $wbtm_reign_settings['reign_buddyextender']['member_header_position']
	: 'inside';

$member_header_position = apply_filters( 'wbtm_rth_manage_member_header_position', $member_header_position );

// BuddyPress appearance & nav style settings
$appearance = bp_get_option( 'bp_nouveau_appearance', array( 'user_nav_display' => false ) );
$nav_display = ! empty( $appearance['user_nav_display'] );

$nav_style = get_theme_mod( 'buddypress_single_member_nav_style', 'iconic' );
$class     = $nav_style === 'iconic' ? 'reign-nav-iconic' : 'reign-default';

$nav_view = get_theme_mod( 'buddypress_main_nav_view_style', 'text_icon' );
$nav_view_style = match ( $nav_view ) {
	'swipe'     => 'reign-nav-swipe',
	'text_icon' => 'reign-nav-swipe text-icon',
	default     => 'reign-nav-more',
};

// Determine if we're in a context that should *not* display nav/sidebar/header
$is_restricted_context = bp_is_user_messages()
	|| bp_is_user_settings()
	|| bp_is_user_notifications()
	|| bp_is_user_profile_edit()
	|| bp_is_user_change_avatar()
	|| bp_is_user_change_cover_image()
	|| apply_filters( 'reign_bp_is_current_action', false );

// Used multiple times
$profile_link = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() );
?>

<?php bp_nouveau_member_hook( 'before', 'home_content' ); ?>

<?php if ( $member_header_position === 'top' ) : ?>
	<div id="item-header"
		role="complementary"
		data-bp-item-id="<?php echo esc_attr( bp_displayed_user_id() ); ?>"
		data-bp-item-component="members"
		class="users-header single-headers">
		<?php bp_nouveau_member_header_template_part(); ?>
	</div>
<?php endif; ?>

<div class="bp-wrap <?php echo esc_attr( $class ); ?>">

	<?php if ( $nav_display && ! bp_nouveau_is_object_nav_in_sidebar() && ! $is_restricted_context ) : ?>
		<div class="rg-nouveau-sidebar-menu">
			<?php bp_get_template_part( 'members/single/parts/item-nav' ); ?>
		</div>
	<?php endif; ?>

	<div id="item-body" class="item-body">
		<div class="wb-grid">

			<?php do_action( 'reign_bp_nouveau_before_content' ); ?>

			<div class="wb-grid-cell">

				<?php if ( bp_is_user_messages() ) : ?>
					<header class="entry-header notifications-header messages-header">
						<h1 class="entry-title rg-profile-title"><?php esc_html_e( 'Messages', 'reign' ); ?></h1>
						<a href="<?php echo esc_url( $profile_link ); ?>" class="push-right button profile-view-button outline small">
							<i class="far fa-user"></i> <?php esc_html_e( 'View Profile', 'reign' ); ?>
						</a>
					</header>
				<?php endif; ?>

				<div class="item-body-inner-wrapper">

					<?php if ( $member_header_position === 'inside' ) : ?>
						<div id="item-header"
							role="complementary"
							data-bp-item-id="<?php echo esc_attr( bp_displayed_user_id() ); ?>"
							data-bp-item-component="members"
							class="users-header single-headers">
							<?php bp_nouveau_member_header_template_part(); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! $nav_display && ! bp_nouveau_is_object_nav_in_sidebar() && ! $is_restricted_context ) : ?>
						<div class="rg-nouveau-sidebar-menu <?php echo esc_attr( $nav_view_style ); ?>">
							<?php bp_get_template_part( 'members/single/parts/item-nav' ); ?>
						</div>
					<?php endif; ?>

					<?php bp_nouveau_member_template_part(); ?>

				</div>
			</div>

			<?php if ( is_active_sidebar( 'member-profile' ) && bp_is_user() && ! $is_restricted_context ) : ?>
				<?php ob_start(); ?>
				<?php dynamic_sidebar( 'member-profile' ); ?>
				<?php $sidebar_content = ob_get_clean(); ?>
				<?php if ( ! empty( trim( $sidebar_content ) ) ) : ?>
					<aside id="secondary" class="widget-area member-profile-widget-area sm-wb-grid-1-1 md-wb-grid-1-1 lg-wb-grid-1-3" role="complementary">
						<div class="widget-area-inner">
							<?php do_action( 'reign_begin_member_profile_sidebar' ); ?>
							<?php echo $sidebar_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php do_action( 'reign_end_member_profile_sidebar' ); ?>
						</div>
					</aside>
				<?php endif; ?>
			<?php endif; ?>

		</div><!-- .wb-grid -->
	</div><!-- #item-body -->

</div><!-- .bp-wrap -->

<?php bp_nouveau_member_hook( 'after', 'home_content' ); ?>

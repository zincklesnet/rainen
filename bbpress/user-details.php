<?php
/**
 * User Details
 *
 * @package bbPress
 * @subpackage Theme
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

do_action( 'bbp_template_before_user_details' );
?>

<div id="bbp-single-user-details">
	<div id="bbp-user-avatar">
		<span class='vcard'>
			<a class="url fn n" href="<?php bbp_user_profile_url(); ?>" title="<?php bbp_displayed_user_field( 'display_name' ); ?>" rel="me">
				<?php echo get_avatar( bbp_get_displayed_user_field( 'user_email', 'raw' ), apply_filters( 'bbp_single_user_details_avatar_size', 150 ) ); ?>
			</a>
			<span class="rg-user-name"><?php bbp_displayed_user_field( 'display_name' ); ?></span>
		</span>
	</div>

	<?php do_action( 'bbp_template_before_user_details_menu_items' ); ?>

	<div id="bbp-user-navigation">
		<ul>
			<?php if ( ! class_exists( 'WBPBI_PeepSo_BbPress_Integration' ) ) : ?>
			<li class="<?php if ( bbp_is_single_user_profile() ) : ?>current<?php endif; ?>">
				<span class="vcard bbp-user-profile-link">
					<a class="url fn n" href="<?php bbp_user_profile_url(); ?>" title="<?php printf( esc_attr__( "%s's Profile", 'reign' ), bbp_get_displayed_user_field( 'display_name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" rel="me"><?php esc_html_e( 'Profile', 'reign' ); ?></a>
				</span>
			</li
			<?php endif; ?>

			<li class="<?php if ( bbp_is_single_user_topics() ) : ?>current<?php endif; ?>">
				<span class='bbp-user-topics-created-link'>
					<a href="<?php bbp_user_topics_created_url(); ?>" title="<?php printf( esc_attr__( "%s's Topics Started", 'reign' ), bbp_get_displayed_user_field( 'display_name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Topics Started', 'reign' ); ?></a>
				</span>
			</li>

			<li class="<?php if ( bbp_is_single_user_replies() ) : ?>current<?php endif; ?>">
				<span class='bbp-user-replies-created-link'>
					<a href="<?php bbp_user_replies_created_url(); ?>" title="<?php printf( esc_attr__( "%s's Replies Created", 'reign' ), bbp_get_displayed_user_field( 'display_name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Replies Created', 'reign' ); ?></a>
				</span>
			</li>

			<?php if ( bbp_is_engagements_active() ) : ?>
				<li class="<?php if ( bbp_is_single_user_engagements() ) : ?>current<?php endif; ?>">
					<span class='bbp-user-engagements-created-link'>
						<a href="<?php bbp_user_engagements_url(); ?>" title="<?php printf( esc_attr__( "%s's Engagements", 'reign' ), bbp_get_displayed_user_field( 'display_name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Engagements', 'reign' ); ?></a>
					</span>
				</li>
			<?php endif; ?>

			<?php if ( bbp_is_favorites_active() ) : ?>
				<li class="<?php if ( bbp_is_favorites() ) : ?>current<?php endif; ?>">
					<span class="bbp-user-favorites-link">
						<a href="<?php bbp_favorites_permalink(); ?>" title="<?php printf( esc_attr__( "%s's Favorites", 'reign' ), bbp_get_displayed_user_field( 'display_name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Favorites', 'reign' ); ?></a>
					</span>
				</li>
			<?php endif; ?>

			<?php if ( bbp_is_user_home() || current_user_can( 'edit_user', bbp_get_displayed_user_id() ) ) : ?>

				<?php if ( bbp_is_subscriptions_active() ) : ?>
					<li class="<?php if ( bbp_is_subscriptions() ) : ?>current<?php endif; ?>">
						<span class="bbp-user-subscriptions-link">
							<a href="<?php bbp_subscriptions_permalink(); ?>" title="<?php printf( esc_attr__( "%s's Subscriptions", 'reign' ), bbp_get_displayed_user_field( 'display_name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Subscriptions', 'reign' ); ?></a>
						</span>
					</li>
				<?php endif; ?>

				<li class="<?php if ( bbp_is_single_user_edit() ) : ?>current<?php endif; ?>">
					<span class="bbp-user-edit-link">
						<a href="<?php bbp_user_profile_edit_url(); ?>" title="<?php printf( esc_attr__( "Edit %s's Profile", 'reign' ), bbp_get_displayed_user_field( 'display_name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Edit', 'reign' ); ?></a>
					</span>
				</li>

			<?php endif; ?>

		</ul>

		<?php do_action( 'bbp_template_after_user_details_menu_items' ); ?>

	</div>
</div>

<?php
do_action( 'bbp_template_after_user_details' );

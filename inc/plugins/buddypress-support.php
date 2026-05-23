<?php
/**
 * BuddyPress compatibility functions.
 *
 * @package Reign
 * @since 7.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compatibility with BP Create Group Type plugin.
 * Return default group search form html.
 */
add_filter(
	'bpgt_modified_group_search_form',
	function ( $altered_search_form_html, $search_form_html ) {
		return $search_form_html;
	},
	10,
	2
);

/**
 * BuddyPress user menu toggle shortcode.
 */
add_shortcode( 'reign_bp_user_menu', 'reign_bp_user_menu_toggle_render' );

function reign_bp_user_menu_toggle_render() {
	ob_start();
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		if ( ( $current_user instanceof WP_User ) ) {
			if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
				$user_link = function_exists( 'bp_members_get_user_url' ) ? bp_members_get_user_url( get_current_user_id() ) : '#';
			} else {
				$user_link = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( get_current_user_id() ) : '#';
			}
			echo '<div id="rg-mobile-icon-toggle" data-id="rg-slidebar-toggle">';
			echo '<div class="user-link">';
			echo get_avatar( $current_user->user_email, 200 );
			echo '</div>';
			echo '</div>';
		}
	} else {
		// Login Page Redirect.
		$login_page_id  = get_theme_mod( 'reign_login_page', 0 );
		$login_page_url = ( $login_page_id ) ? get_permalink( $login_page_id ) : wp_login_url();

		// Register Page Redirect.
		$registration_page_id  = get_theme_mod( 'reign_registration_page', 0 );
		$registration_page_url = ( $registration_page_id ) ? get_permalink( $registration_page_id ) : wp_registration_url();
		?>
		<div class="rg-icon-wrap">
			<a href="<?php echo esc_url( $login_page_url ); ?>" class="btn-login" title="<?php esc_attr_e( 'Login', 'reign' ); ?>">
				<span class="far fa-sign-in"></span>
			</a>
		</div>
		<?php
		if ( get_option( 'users_can_register' ) ) {
			?>
			<span class="sep">|</span>
			<div class="rg-icon-wrap">
				<a href="<?php echo esc_url( $registration_page_url ); ?>" class="btn-register" title="<?php esc_attr_e( 'Register', 'reign' ); ?>">
					<span class="far fa-address-book"></span>
				</a>
			</div>
			<?php
		}
	}
	return ob_get_clean();
}

/**
 * Shortcode for BuddyPress Member Carousel.
 *
 * @param array $atts Shortcode attributes.
 */
function reign_bp_memeber_carousel( $atts ) {
	global $members_template, $wbtm_reign_settings;
	// Attributes
	$atts = shortcode_atts(
		array(
			'max_members' => '5',
			'member_sort' => 'active',
			'member_name' => 'show',
		),
		$atts
	);

	// Setup args for querying members.
	$members_args = array(
		'user_id'         => 0,
		'type'            => $atts['member_sort'],
		'per_page'        => $atts['max_members'],
		'max'             => $atts['max_members'],
		'populate_extras' => true,
		'search_terms'    => false,
	);

	$args = array(
		'object_dir' => 'members',
		'item_id'    => $user_id = bp_get_member_user_id(),
		'type'       => 'cover-image',
	);
	$cover_img_url = bp_attachments_get_attachment( 'url', $args );
	if ( empty( $cover_img_url ) ) {
		$cover_img_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
		if ( empty( $cover_img_url ) ) {
			$cover_img_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
		}
	}

	ob_start();

	// Back up the global.
	$old_members_template = $members_template;
	?>
	<div id="rg-member-section" class="rg-members-section rg-home-section rg-slick-list-wrapper">
		<div id="members-carousel-list" class="rg-slick-list-container container">
	<?php if ( bp_has_members( $members_args ) ) : ?>
		<?php
		while ( bp_members() ) :
			bp_the_member();
			?>
			<?php $user_id = bp_get_member_user_id(); ?>
				<div class="rg-member rg-image-box">
					<div class="wbtm-mem-cover-img"><img src="<?php echo esc_url( $cover_img_url ); ?>" alt="" /></div>
					<div class="item-avatar">
						<a href="<?php bp_member_permalink(); ?>"><?php echo reign_get_online_status( $user_id ); ?><?php echo get_avatar( bp_get_member_user_id() ); ?></a>
					</div>
					<?php if ( 'show' === $atts['member_name'] ) : ?>
						<div class="rg-member-decription">
							<h3><a class="name fn rg-member-title" href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></h3>
						</div>
					<?php endif; ?>
				</div>

		<?php endwhile; ?>
			<?php else : ?>
				<div class="widget-error">
				<?php esc_html_e( 'No one has signed up yet!', 'reign' ); ?>
				</div>

			<?php endif; ?>
		</div>
	</div>

	<?php
	// Restore the global.
	$members_template = $old_members_template;
	return ob_get_clean();
}
add_shortcode( 'bp_member_carousel', 'reign_bp_memeber_carousel' );

/**
 * Shortcode for BuddyPress Group Carousel.
 *
 * @param array $atts Shortcode attributes.
 */
function reign_bp_group_carousel( $atts ) {
	global $groups_template, $wbtm_reign_settings;
	// Attributes
	$atts = shortcode_atts(
		array(
			'max_groups' => 10,
			'group_sort' => 'active',
		),
		$atts
	);

	if ( empty( $atts['group_sort'] ) ) {
		$atts['group_sort'] = 'popular';
	}

	/**
	 * Filters the user ID to use with the widget atts.
	 *
	 * @since 1.5.0
	 *
	 * @param string $value Empty user ID.
	 */
	$user_id = apply_filters( 'bp_group_carousel_user_id', '0' );

	$max_groups = ! empty( $atts['max_groups'] ) ? (int) $atts['max_groups'] : 10;

	// Setup args for querying groups.
	$group_args = array(
		'user_id'  => $user_id,
		'type'     => $atts['group_sort'],
		'per_page' => $max_groups,
		'max'      => $max_groups,
	);
	ob_start();
	// Back up the global.
	$old_groups_template = $groups_template;
	?>
	<div id="rg-group-carousel-section" class="rg-group-carousel-section rg-group">
		<?php if ( bp_has_groups( $group_args ) ) : ?>
			<ul id="groups-carousel-list" class="groups-carousel-container container" aria-live="assertive" aria-atomic="true" aria-relevant="all">

				<?php
				while ( bp_groups() ) :
					bp_the_group();
						$args = array(
							'object_dir' => 'groups',
							'item_id' => $group_id = bp_get_group_id(),
							'type'    => 'cover-image',
						);

						$cover_img_url = bp_attachments_get_attachment( 'url', $args );

						if ( empty( $cover_img_url ) ) {
							$cover_img_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
							if ( empty( $cover_img_url ) ) {
								$cover_img_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
							}
						}
					?>
					<li <?php bp_group_class(); ?> >
						<div class="bp-group-inner-wrap">
							<div class="wbtm-group-cover-img"><img src="<?php echo esc_url( $cover_img_url ); ?>" alt="" /></div>
							<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
								<?php if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) : ?>
									<a class="item-avatar-group" href="<?php bp_group_url(); ?>"><?php bp_group_avatar( '' ); ?></a>
								<?php else : ?>
									<a class="item-avatar-group" href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( '' ); ?></a>
								<?php endif; ?>
							<?php endif; ?>
							<div class="group-content-wrap">
								<div class="item">
									<h3 class="item-title"><?php bp_group_link(); ?></h3>
									<?php
									/**
									 * Fires inside the listing of an individual group listing item.
									 *
									 * @since 1.1.0
									 */
									do_action( 'bp_directory_groups_item' );
									?>
								</div>
							</div>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<div class="widget-error">
				<?php esc_html_e( 'There are no groups to display.', 'reign' ); ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	// Restore the global.
	$groups_template = $old_groups_template;

	return ob_get_clean();
}
add_shortcode( 'bp_group_carousel', 'reign_bp_group_carousel' );

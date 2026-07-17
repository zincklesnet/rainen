<?php
/**
 * Mobile menu
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$mobile_menu_logged_in_exists  = has_nav_menu( 'mobile-menu-logged-in' );
$mobile_menu_logged_out_exists = has_nav_menu( 'mobile-menu-logged-out' );
?>

<div class="reign-fallback-header header-mobile<?php echo reign_is_truthy( get_theme_mod( 'reign_header_sticky_menu_enable', true ) ) ? esc_attr( ' fixed-top' ) : ''; ?>">
	<nav id="site-navigation" class="main-navigation reign-navbar-mobile" role="navigation" aria-label="<?php esc_attr_e( 'Main menu', 'reign' ); ?>">
		<div class="container">
			<?php do_action( 'reign_before_reign_mobile_nav_top' ); ?>
			<div class="reign-nav-top-bar">
				<div class="site-branding">
					<div class="logo">
						<?php
						$mobile_menu_logo_enable       = reign_is_truthy( get_theme_mod( 'reign_header_mobile_menu_logo_enable', false ) );
						$reign_header_mobile_menu_logo = get_theme_mod( 'reign_header_mobile_menu_logo', '' );
						if ( $mobile_menu_logo_enable && ! empty( $reign_header_mobile_menu_logo ) ) {
							// A configured mobile logo wins, even without a main Site Logo.
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img class="reign-mobile-logo" src="<?php echo esc_url( $reign_header_mobile_menu_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" fetchpriority="high" /></a>
							<?php
						} elseif ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
							the_custom_logo();
						} else {
							reign_display_site_title_description();
						}
						?>
					</div><!-- .logo -->
				</div><!-- .site-branding -->

				<button class="reign-toggler reign-toggler-left" type="button" aria-label="<?php esc_attr_e( 'Open navigation menu', 'reign' ); ?>" aria-expanded="false">
					<span class="icon-bar bar1" aria-hidden="true"></span>
					<span class="icon-bar bar2" aria-hidden="true"></span>
					<span class="icon-bar bar3" aria-hidden="true"></span>
				</button>

				<div class="navbar-menu-container">
					<?php do_action( 'reign_before_reign_mobile_main_menu' ); ?>
					<?php if ( is_user_logged_in() ) { ?>
						<div class="reign-mobile-user reign-mobile-user-header">
							<?php
								// Output the current user's profile block (avatar, name, profile/settings link).
								echo render_reign_user_profile_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- returns pre-escaped theme markup.
							?>
							<a href="javascript:void(0);" class="reign-panel-close" aria-label="<?php esc_attr_e( 'Close navigation menu', 'reign' ); ?>"><i class="far fa-times" aria-hidden="true"></i></a>
						</div>
					<?php } else { ?>
						<div class="reign-mobile-user reign-mobile-user-header">
							<div class="site-branding">
								<div class="logo">
									<?php
									$mobile_menu_logo_enable       = reign_is_truthy( get_theme_mod( 'reign_header_mobile_menu_logo_enable', false ) );
									$reign_header_mobile_menu_logo = get_theme_mod( 'reign_header_mobile_menu_logo', '' );
									if ( $mobile_menu_logo_enable && ! empty( $reign_header_mobile_menu_logo ) ) {
										// A configured mobile logo wins, even without a main Site Logo.
										?>
										<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
											<img class="reign-mobile-logo" src="<?php echo esc_url( $reign_header_mobile_menu_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" fetchpriority="high" />
										</a>
										<?php
									} elseif ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
										the_custom_logo();
									} else {
										reign_display_site_title_description();
									}
									?>
								</div><!-- .logo -->
							</div><!-- .site-branding -->
							<a href="javascript:void(0);" class="reign-panel-close" aria-label="<?php esc_attr_e( 'Close navigation menu', 'reign' ); ?>"><i class="far fa-times" aria-hidden="true"></i></a>
						</div>
					<?php } ?>
					<?php
					if ( is_user_logged_in() ) {
						wp_nav_menu(
							array(
								'theme_location' => 'mobile-menu-logged-in',
								'menu_id'        => 'primary-mobile-menu',
								'fallback_cb'    => 'fallback_primary_mobile_menu',
								'container'      => false,
								'walker'         => new Reign_Left_Panel_Menu_Walker(),
								'menu_class'     => 'primary-menu navbar-nav',
							)
						);
					} else {
						wp_nav_menu(
							array(
								'theme_location' => 'mobile-menu-logged-out',
								'menu_id'        => 'primary-mobile-menu',
								'fallback_cb'    => 'fallback_primary_mobile_menu',
								'container'      => false,
								'walker'         => new Reign_Left_Panel_Menu_Walker(),
								'menu_class'     => 'primary-menu navbar-nav',
							)
						);
					}

					?>
					<?php do_action( 'reign_after_reign_mobile_main_menu' ); ?>

					<?php
					do_action( 'reign_before_reign_mobile_panel_menu' );
					if ( ! $mobile_menu_logged_in_exists && ! $mobile_menu_logged_out_exists ) {
						wp_nav_menu(
							array(
								'theme_location' => 'panel-menu',
								'menu_id'        => 'reign-panel',
								'fallback_cb'    => '',
								'container'      => false,
								'walker'         => new Reign_Left_Panel_Menu_Walker(),
								'menu_class'     => 'navbar-nav navbar-reign-panel',
							)
						);
					}
					do_action( 'reign_before_reign_mobile_panel_menu' );
					?>
				</div>

				<div class="reign-user-toggler">
					<?php
					do_action( 'reign_before_header_icons' );

					$reign_mobile_header_default_icons_set = reign_mobile_header_default_icons_set();
					$reign_mobile_header_icons_set         = reign_get_sortable_setting( 'reign_mobile_header_icons_set', reign_mobile_header_default_icons_set() );

					if ( is_array( $reign_mobile_header_icons_set ) && in_array( 'user-menu', $reign_mobile_header_icons_set ) ) :
						if ( apply_filters( 'reign_user_profile_menu_toggler', is_user_logged_in() ) ) {
							// BuddyNext active (mutually exclusive with BuddyPress) —
							// render the BN avatar + profile dropdown (zero JS).
							if ( function_exists( 'buddynext_header_user_menu' ) ) {
								buddynext_header_user_menu();
							} elseif ( class_exists( 'PeepSo' ) ) {
								// For PeepSo notification icons.

								if ( is_active_sidebar( 'reign-header-widget-area' ) ) :
									echo '<div class="reign-peepso-menu-toggle">';
									dynamic_sidebar( 'reign-header-widget-area' );
									echo '</div>';
								endif;

								echo '<div class="user-profile-menu-wrapper">';
								echo '<div class="reign-mobile-user reign-mobile-user-header">';
								echo render_reign_user_profile_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- returns pre-escaped theme markup. // Output the current user's profile block (avatar, name, profile/settings link).
								echo '<a href="javascript:void(0);" class="reign-panel-close" aria-label="' . esc_attr__( 'Close navigation menu', 'reign' ) . '"><i class="far fa-times" aria-hidden="true"></i></a>';
								echo '</div>';
								if ( has_nav_menu( 'menu-2' ) ) {
									// Use menu-2 location (User Profile menu)
									wp_nav_menu(
										array(
											'theme_location' => 'menu-2',
											'menu_id' => 'user-profile-menu',
											'fallback_cb' => '',
											'container' => false,
											'menu_class' => 'user-profile-menu',
										)
									);
								} else {
									dynamic_sidebar( 'reign-header-widget-area' );
								}
								echo '</div>';

							} else {
								$current_user = wp_get_current_user(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- local copy mirrors the WP global; not mutating shared state.
								if ( ( $current_user instanceof WP_User ) ) {
									if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
										$user_link = function_exists( 'bp_members_get_user_url' ) ? bp_members_get_user_url( get_current_user_id() ) : '#';
									} else {
										$user_link = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( get_current_user_id() ) : '#';
									}
									echo '<div class="user-link-wrap">';
									echo '<a class="user-link" href="' . esc_url( $user_link ) . '">';
									?>
									<?php
									echo get_avatar( $current_user->user_email, 200 );
									echo '</a>';
									echo '<div class="user-profile-menu-wrapper">';
									echo '<div class="reign-mobile-user reign-mobile-user-header">';
									echo render_reign_user_profile_block(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- returns pre-escaped theme markup. // Output the current user's profile block (avatar, name, profile/settings link).
									echo '<a href="javascript:void(0);" class="reign-panel-close" aria-label="' . esc_attr__( 'Close navigation menu', 'reign' ) . '"><i class="far fa-times" aria-hidden="true"></i></a>';
									echo '</div>';
									// Use menu-2 location (User Profile menu)
									if ( has_nav_menu( 'menu-2' ) ) {
										wp_nav_menu(
											array(
												'theme_location' => 'menu-2',
												'menu_id' => 'user-profile-menu',
												'fallback_cb' => '',
												'container' => 'user-mobile-menu',
												'menu_class' => 'user-profile-menu',
											)
										);
									} else {
										do_action( 'reign_user_profile_menu' );
									}
									echo '</div>';
									echo '</div>';
								}
							}
						}
					endif;
					if ( ! class_exists( 'PeepSo' ) ) {
						do_action( 'reign_after_header_icons' );
					}
					?>
				</div>

				<?php
				$reign_mobile_header_layout = get_theme_mod( 'reign_mobile_header_layout', 'header-v1' );
				if ( 'header-v2' === $reign_mobile_header_layout ) {
					?>
					<div class="reign-navbar-user">
						<?php
						$reign_mobile_header_default_icons_set = reign_mobile_header_default_icons_set();
						$reign_mobile_header_icons_set         = reign_get_sortable_setting( 'reign_mobile_header_icons_set', reign_mobile_header_default_icons_set() );
						foreach ( $reign_mobile_header_icons_set as $header_icon ) {
							if ( 'user-menu' !== $header_icon ) {
								get_template_part( 'template-parts/header-icons/' . $header_icon, '' );
							}
						}

						do_action( 'reign_after_header_icons' );
						do_action( 'reign_after_mobile_header_icons' );
						?>
					</div><!-- .reign-navbar-user -->
					<?php
				}
				?>

			</div><!-- .reign-nav-top-bar -->
			<?php if ( 'header-v2' !== $reign_mobile_header_layout ) { ?>
				<div class="reign-navbar-user">
					<?php
					$reign_mobile_header_default_icons_set = reign_mobile_header_default_icons_set();
					$reign_mobile_header_icons_set         = reign_get_sortable_setting( 'reign_mobile_header_icons_set', reign_mobile_header_default_icons_set() );
					foreach ( $reign_mobile_header_icons_set as $header_icon ) {
						if ( 'user-menu' !== $header_icon ) {
							get_template_part( 'template-parts/header-icons/' . $header_icon, '' );
						}
					}
					do_action( 'reign_after_mobile_header_icons' );
					?>
				</div><!-- .reign-navbar-user -->
			<?php } ?>
			<?php do_action( 'reign_after_reign_mobile_nav_top' ); ?>
		</div><!-- .container -->
	</nav><!-- #site-navigation -->
</div><!-- .header-mobile -->

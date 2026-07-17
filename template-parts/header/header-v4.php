<?php
/**
 * The header for our theme.
 *
 * This is the template that displays header vesion two
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$menu_class = ! has_nav_menu( 'menu-1' ) ? 'no-nav-menu' : '';
?>
<div class="reign-fallback-header header-desktop version-four<?php echo reign_is_truthy( get_theme_mod( 'reign_header_sticky_menu_enable', true ) ) ? esc_attr( ' fixed-top' ) : ''; ?>">
	<div class="container">
		<div class="rg-hdr-v4-row-1 <?php echo esc_attr( $menu_class ); ?>">
			<div class="rg-hdr-v4-row-1-col">
				<div class="site-branding">
					<div class="logo">
					<?php
					/**
					 * Custom logo
					 *
					 * @package reign
					 */
					if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
						the_custom_logo();
					} else {
						reign_display_site_title_description();
					}

					$reign_header_sticky_menu_enable              = get_theme_mod( 'reign_header_sticky_menu_enable', true );
					$reign_header_sticky_menu_custom_style_enable = get_theme_mod( 'reign_header_sticky_menu_custom_style_enable', false );
					$sticky_menu_logo                             = get_theme_mod( 'reign_sticky_header_menu_logo', '' );

					if ( reign_is_truthy( $reign_header_sticky_menu_enable ) && reign_is_truthy( $reign_header_sticky_menu_custom_style_enable ) && $sticky_menu_logo ) {
						?>
						<a href="<?php echo esc_url( get_home_url() ); ?>" class="sticky-menu-logo custom-logo-link" rel="home" itemprop="url">
							<img src="<?php echo esc_url( $sticky_menu_logo ); ?>" class="custom-logo" alt="<?php bloginfo( 'name' ); ?>" itemprop="logo" fetchpriority="high">
						</a>
						<?php
					}
					?>
					</div>
				</div>
			</div>

			<?php
			// (default sourced via helper)
			$reign_header_icons_set         = reign_get_sortable_setting( 'reign_header_icons_set', reign_header_default_icons_set() );
			if ( in_array( 'search', $reign_header_icons_set ) ) :
				?>
				<div class="rg-hdr-v4-row-2-col">
					<?php do_action( 'reign_header_v4_middle_section_html' ); ?>
				</div>
			<?php endif; ?>
			<div class="rg-hdr-v4-row-3-col wb-grid-flex">
				<div class="header-right no-gutter wb-grid-flex wb-grid-center">
				<?php
				do_action( 'reign_before_header_icons' );

				foreach ( $reign_header_icons_set as $header_icon ) {
					get_template_part( 'template-parts/header-icons/' . $header_icon, '' );
				}

				do_action( 'reign_after_header_icons' );
				?>
				</div>
			</div>
		</div>
	</div>

	<div class="rg-hdr-v4-row-2">
		<div class="container">
			<div class="wb-grid">
				<div class="header-right no-gutter wb-grid-flex wb-grid-center">
					<nav id="site-navigation" class="main-navigation" role="navigation">
						<?php
						// Display the primary navigation.
						reign_display_primary_navigation();
						?>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>

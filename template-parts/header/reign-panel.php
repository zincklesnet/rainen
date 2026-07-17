<?php
/**
 * Left Menu Panel
 *
 * It's displayed left menu panel layout template
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $post;
$bp_pages = get_option( 'bp-pages' );

if ( class_exists( 'BuddyPress' ) ) {
	$components = array(
		'groups'   => 'groups',
		'members'  => array( 'members', 'is_user' ),
		'activity' => 'activity',
		'document' => 'document',
		'media'    => 'media',
		'register' => 'register',
		'forums'   => 'forums',
	);

	foreach ( $components as $component => $component_page ) {
		if ( is_array( $component_page ) ) {
			if ( bp_is_current_component( $component_page[0] ) || call_user_func( 'bp_' . $component_page[1] ) ) {
				$bp_page = get_post( $bp_pages[ $component ] );
				break;
			}
		} elseif ( bp_is_current_component( $component ) || bp_is_register_page() ) {
			$bp_page = get_post( $bp_pages[ $component ] );
			break;
		}
	}
}

// Fallback for Forums page via title, if not found above.
if ( empty( $bp_page ) && function_exists( 'bbp_is_forum_archive' ) && bbp_is_forum_archive() ) {
	$forum_root_page = get_page_by_title( 'Forums' ); // phpcs:ignore WordPress.WP.DeprecatedFunctions.get_page_by_titleFound -- lightweight title fallback for the forum root; acceptable for a one-off lookup.
	if ( $forum_root_page ) {
		$bp_page = $forum_root_page;
	}
}

if ( ! empty( $bp_page ) ) {
	$wbcom_metabox_data = get_post_meta( $bp_page->ID, 'reign_wbcom_metabox_data', true );
} elseif ( is_singular() && isset( $post->ID ) ) {
	$wbcom_metabox_data = get_post_meta( $post->ID, 'reign_wbcom_metabox_data', true );
} else {
	$wbcom_metabox_data = array();
}

$display_left_panel_menu         = isset( $wbcom_metabox_data['layout']['display_left_panel_menu'] ) ? $wbcom_metabox_data['layout']['display_left_panel_menu'] : '';
$reign_left_panel_gloabl_setting = get_theme_mod( 'reign_left_panel_gloabl_setting', true );

// Check whether to display the left panel based on other conditions.
// Strict `false === ...` would silently break for saved values 'off' / '0'
// which are NOT === false. reign_is_truthy() normalises across all storage
// shapes so disabling the global setting actually hides the panel.
if ( 'no' !== $display_left_panel_menu && ! reign_is_truthy( $reign_left_panel_gloabl_setting ) ) {
	return;
}

if ( 'yes' === $display_left_panel_menu ) {
	return;
}

$reign_left_panel_state  = get_theme_mod( 'reign_left_panel_state', 'closed' );
$reign_left_panel_toggle = get_theme_mod( 'reign_left_panel_toggle', true );

$state_class = '';
// Check if the 'reignpanel' cookie exists and has the value 'open'.
if ( isset( $_COOKIE['reignpanel'] ) ) {
	// Unexpected cookie values fall through and use the default state.
	if ( 'open' === sanitize_key( wp_unslash( $_COOKIE['reignpanel'] ) ) ) {
		$state_class = 'reign-panel-open';
	}
} elseif ( 'open' === $reign_left_panel_state ) {
	// If the cookie doesn't exist, fall back to the theme mod setting.
	$state_class = 'reign-panel-open';
}

$panel_menu_exists = has_nav_menu( 'panel-menu' );
// Check if the panel menu for logged-in users exists.
if ( is_user_logged_in() && ! $panel_menu_exists ) {
	// If the panel menu for logged-in users doesn't exist, don't display the left panel.
	return;
}

// Check if the panel menu for logged-out users exists.
if ( ! is_user_logged_in() ) {
	$panel_menu_logged_out_exists = has_nav_menu( 'panel-menu-loggedout' );
	if ( ! $panel_menu_logged_out_exists ) {
		// If the panel menu for logged-out users doesn't exist, don't display the left panel.
		return;
	}
}
?>

<div id="reign-menu-panel" class="reign-menu-panel <?php echo esc_attr( $state_class ); ?>">
	<div class="reign-menu-panel-inner reign-scrollbar">
		<?php if ( reign_is_truthy( $reign_left_panel_toggle ) ) : ?>
			<div class="reign-menu-panel-header">
				<button class="reign-toggler" type="button">
					<span class="icon-bar bar1"></span>
					<span class="icon-bar bar2"></span>
					<span class="icon-bar bar3"></span>
				</button>
			</div>
		<?php endif; ?>
		<div class="reign-inner-panel">
			<?php
			ob_start();

			wp_nav_menu(
				array(
					'theme_location' => is_user_logged_in() ? 'panel-menu' : 'panel-menu-loggedout',
					'menu_id'        => 'reign-panel',
					'fallback_cb'    => is_user_logged_in() ? '' : 'fallback_panel_menu',
					'container'      => false,
					'walker'         => new Reign_Left_Panel_Menu_Walker(),
					'menu_class'     => 'navbar-nav navbar-reign-panel',
				)
			);

			$left_panel_menu = ob_get_clean();

			if ( false !== strpos( $left_panel_menu, 'reign-menu-section' ) ) {
				$left_panel_menu = str_replace(
					'navbar-nav navbar-reign-panel',
					'navbar-nav navbar-reign-panel has-section-menu',
					$left_panel_menu
				);
			}

			echo $left_panel_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>

	</div>
</div>

<?php

/***
 * This file is used to add site administration menus to the WordPress backend.
 *
 * If you need to provide configuration options for your component that can only
 * be modified by a site administrator, this is the best place to do it.
 *
 * However, if your component has settings that need to be configured on a user
 * by user basis - it's best to hook into the front end "Settings" menu.
 */

/**
 * bp_custom_pages_add_admin_menu()
 *
 * This function will add a WordPress wp-admin admin menu for your component under the
 * "BuddyPress" menu.
 */
function bp_custom_pages_add_admin_menu() {
	global $bp;

	if ( ! is_super_admin() ) {
		return false;
	}

	add_submenu_page( 'edit.php?post_type=bp-custom-pages', __( 'Settings', 'bp-custom-pages' ), __( 'Settings', 'bp-custom-pages' ), 'manage_options', 'bp-custom-pages-settings', 'bp_custom_pages_admin' );
}
// The bp_core_admin_hook() function returns the correct hook (admin_menu or network_admin_menu),
// depending on how WordPress and BuddyPress are configured
add_action( bp_core_admin_hook(), 'bp_custom_pages_add_admin_menu' );

/**
 * bp_custom_pages_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 */
function bp_custom_pages_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer( 'bp-custom-pages-settings' ) ) {
		update_option( 'bp-custom-pages-menu-name', sanitize_text_field( $_POST['bp-custom-pages-menu-name'] ) );

		$updated = true;
	}

	$menu_name = esc_attr( get_option( 'bp-custom-pages-menu-name' ) );
	if ( $menu_name === '' ) {
		$menu_name = 'Custom Pages';
	}
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'BP Custom Pages Settings', 'bp-custom-pages' ); ?></h2>
		<br />

		<?php if ( isset( $updated ) ) : ?>
			<div id='message' class='updated fade'>
				<p><?php esc_html_e( 'Settings Updated.', 'bp-custom-pages' ); ?></p>
			</div>
		<?php endif; ?>

		<form action="<?php echo esc_attr( site_url() . '/wp-admin/edit.php?post_type=bp-custom-pages&page=bp-custom-pages-settings' ); ?>" name="bp-custom-pages-settings-form" id="bp-custom-pages-settings-form" method="post">

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php esc_html_e( 'Menu Name', 'bp-custom-pages' ); ?></label></th>
					<td>
						<input name="bp-custom-pages-menu-name" type="text" id="bp-custom-pages-menu-name" value="<?php echo esc_attr( $menu_name ); ?>" size="60" />
					</td>
				</tr>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Settings', 'bp-custom-pages' ); ?>"/>
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-custom-pages-settings' );
			?>
		</form>
	</div>
	<?php
}


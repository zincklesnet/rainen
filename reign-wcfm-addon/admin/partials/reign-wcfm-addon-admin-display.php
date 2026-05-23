<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/admin/partials
 */
// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$current_active_tab = 0;
if ( isset( $_POST['render_theme_setting_current_tab'] ) ) { //phpcs:ignore
	$current_active_tab = intval( $_POST['render_theme_setting_current_tab'] ); //phpcs:ignore
}
?>
<input type="hidden" id="render_theme_setting_current_tab" name="render_theme_setting_current_tab" value="<?php echo esc_attr( $current_active_tab ); ?>" />

<?php
$vertical_tabs;
if ( isset( $vertical_tabs ) && is_array( $vertical_tabs ) ) {
	echo '<div class="reign-tab">';
	$counter = 0;
	foreach ( $vertical_tabs as $key => $value ) {
		$active = ( $current_active_tab == $counter ) ? ' active' : '';
		echo '<button class="reign-tablinks ' . esc_attr( $key ) . ' ' . esc_attr( $active ) . '" onclick="openSettingsTab(event, \'' . esc_js( $key ) . '\')">' . esc_html( $value ) . '</button>';
		$counter++;
	}
	echo '</div>';
}

if ( isset( $vertical_tabs ) && is_array( $vertical_tabs ) ) {
	foreach ( $vertical_tabs as $key => $value ) {
		echo '<div id="' . esc_attr( $key ) . '" class="reign-tabcontent">';
		do_action( 'render_theme_options_for_' . $key );
		?>
		<p class="submit" style="clear: both;">
			<input id="reign-theme-options-submit" type="submit" name="Submit"  class="button-primary" value="<?php esc_html_e( 'Update Settings',  'reign-wcfm-addon' ); ?>" />
			<input type="hidden" name="reign-wcfm-settings-submit" value="Y" />
		</p>
		<?php
		echo '</div>';
	}
}

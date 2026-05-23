<?php
/**
 * Vertical Tab.
 *
 * @package Reign SenseiLMS Addon
 * @subpackage admin
 */

$current_active_tab = 0;
//phpcs:disable
if ( isset( $_POST['render_theme_setting_current_tab'] ) ) { 
	$current_active_tab = intval( $_POST['render_theme_setting_current_tab'] );
}
//phpcs:enable
?>
<input type="hidden" id="render_theme_setting_current_tab" name="render_theme_setting_current_tab" value="<?php echo esc_attr( $current_active_tab ); ?>" />

<?php
$vertical_tabs;
if ( isset( $vertical_tabs ) && is_array( $vertical_tabs ) ) {
	echo '<div class="reign-tab">';
	$counter = 0;
	foreach ( $vertical_tabs as $key => $value ) {
		$active = ( $current_active_tab == $counter ) ? ' active' : '';
		echo '<button class="reign-tablinks ' . esc_attr( $key ) . ' ' . esc_attr( $active ) . '" onclick="openSettingsTab(event, \'' . esc_attr( $key ) . '\')">' . esc_attr( $value ) . '</button>';
		$counter++;
	}
	echo '</div>';
}

if ( isset( $vertical_tabs ) && is_array( $vertical_tabs ) ) {

	foreach ( $vertical_tabs as $key => $value ) {
		echo '<div id="' . esc_attr( $key ) . '" class="reign-tabcontent">';
		
		// Render theme compatibility options
		do_action( 'render_theme_options_for_' . $key );
		
		// Only show save button for tabs that have actual settings (not shortcodes)
		if ( $key !== 'reign_tutorlms_shortcodes' ) {
			?>
			<p class="submit" style="clear: both;">
				<input type="submit" name="Submit" id="reign-theme-options-submit" class="button-primary" value="Update Settings" />
				<input type="hidden" name="reign-settings-submit" value="Y" />
			</p>
			<?php
		}
		
		echo '</div>';
	}
}

?>

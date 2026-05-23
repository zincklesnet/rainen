<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Initialize current active tab.
$current_active_tab = isset( $_POST['render_theme_setting_current_tab'] ) ? intval( $_POST['render_theme_setting_current_tab'] ) : 0;
?>
<input type="hidden" id="render_theme_setting_current_tab" name="render_theme_setting_current_tab" value="<?php echo esc_attr( $current_active_tab ); ?>" />

<?php
if ( ! empty( $vertical_tabs ) && is_array( $vertical_tabs ) ) :
	?>
	<div class="reign-tab">
		<?php
		$counter = 0;
		foreach ( $vertical_tabs as $key => $value ) :
			$active = $current_active_tab === $counter ? ' active' : '';
			?>
			<button class="reign-tablinks <?php echo esc_attr( $key . $active ); ?>" onclick="openSettingsTab(event, '<?php echo esc_js( $key ); ?>')">
				<?php echo esc_html( $value ); ?>
			</button>
			<?php
			++$counter;
		endforeach;
		?>
	</div>

	<?php
	foreach ( $vertical_tabs as $key => $value ) :
		?>
		<div id="<?php echo esc_attr( $key ); ?>" class="reign-tabcontent" style="<?php echo $current_active_tab === $counter ? 'display: block;' : ''; ?>">
			<?php do_action( 'render_theme_options_for_' . $key ); ?>
			<p class="submit" style="clear: both;">
				<input id="reign-theme-options-submit" type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Update Settings', 'reign' ); ?>" />
				<input type="hidden" name="reign-settings-submit" value="Y" />
			</p>
		</div>
	<?php endforeach; ?>
<?php endif; ?>

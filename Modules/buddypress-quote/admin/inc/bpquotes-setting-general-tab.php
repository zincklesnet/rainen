<?php
/**
 * This file is called for general settings section at admin settings.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bpquotes_gnrl_settings = get_option( 'bpquotes_gnrl_settings' );
$user_roles            = wp_roles()->get_names();
unset($user_roles['administrator']);
?>
<div class="wbcom-tab-content">
	<div class="wbcom-wrapper-admin">
	<div class="wbcom-admin-title-section"><h3><?php esc_html_e( 'General Setting', 'buddypress-quotes' ); ?></h3>
	<form method="post" action="options.php">
		<div class="wbcom-admin-option-wrap">
		<?php
		settings_fields( 'bpquotes_gnrl_settings_section' );
		do_settings_sections( 'bpquotes_gnrl_settings_section' );
		?>
		<div class="container">
			<div class="bpquotes-setting-wrapper">
				<div class="wbcom-settings-section-wrap bpquotes-section">
					<div class="wbcom-settings-section-options-heading bpquotes-heading">
						<h4><?php esc_html_e( 'Add background image', 'buddypress-quotes' ); ?></h4>
						<p class="description"><?php esc_html_e( '( Use 1024 x 512 px images for better experience )', 'buddypress-quotes' ); ?></p>
					</div>
					<div class="quotes-bg-images">
						<div class="bpquotes-img-upload-div">
							<a name="upload-btn" id="bpquotes-img-upload-btn"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</div>
						<div class="bpquotes-background-images">
							<?php
							if ( isset( $bpquotes_gnrl_settings['image_url'] ) && ! empty( $bpquotes_gnrl_settings['image_url'] ) ) {
								foreach ( $bpquotes_gnrl_settings['image_url'] as $key => $url ) {
									$text_color = isset( $bpquotes_gnrl_settings['image_text_color'][ $key ] ) ? $bpquotes_gnrl_settings['image_text_color'][ $key ] : '';
									?>
									<div class="bpquotes-single-img-section">
										<div class="bpquotes-single-img">
											<a href="javascript:void(0)" class="bpquotes-remove-img" data-id="bpquotes-bg-image-text-color-<?php echo esc_attr( $key ); ?>"><i class="fa fa-times" aria-hidden="true"></i></a>
											<img src="<?php echo esc_url( $url ); ?>">
											<input type="hidden" name="bpquotes_gnrl_settings[image_url][]" class="regular-text bpquotes-hidden-input" value="<?php echo esc_url( $url ); ?>">
										</div>
										<div id="bpquotes-bg-image-text-color-<?php echo esc_attr( $key ); ?>" class="bpquotes-bg-image-text-color" >
											<label>
												<?php esc_html_e( 'Text Color', 'buddypress-quotes' ); ?>
											</label>
											<input type="hidden" name="bpquotes_gnrl_settings[image_text_color][]" class="regular-text bpquotes-hidden-input bpquotes-color-field" value="<?php echo esc_url( $text_color ); ?>">
										</div>
									</div>
									<?php
								}
							}
							?>
						</div>
					</div>
				</div>
				
				<div class="wbcom-settings-section-wrap bpquotes-section">
						<div class="bpquotes-color-section">
							<div class="wbcom-settings-section-options-heading bpquotes-heading bpquotes-heading">
								<h4><?php esc_html_e( 'Background color', 'buddypress-quotes' ); ?></h4>
							</div>
							<div class="bpquotes-color-columns">
								<div class="wbcom-flex-wrap">
									<div class="bpquotes-colorpicker">
										<span><?php esc_html_e( 'Background Color', 'buddypress-quotes' ); ?></span>
										<input type="text" value="" id="bpquotes-bg-color" class="bpquotes-color-field" />
									</div>
									<div class="bpquotes-colorpicker">
										<span><?php esc_html_e( 'Quote Text Color', 'buddypress-quotes' ); ?></span>
										<input type="text" value="" id="bpquotes-inverted-color" class="bpquotes-color-field" />						
									</div>
								</div>
								<a class="bpquotes-add-bgcolor"><?php esc_html_e( 'Add', 'buddypress-quotes' ); ?></a>
							</div>
						</div>
						<div class="bpquotes-background-colors">
							<?php
							if ( isset( $bpquotes_gnrl_settings['bg_colors'] ) && ! empty( $bpquotes_gnrl_settings['bg_colors'] ) ) {
								foreach ( $bpquotes_gnrl_settings['bg_colors'] as $_key => $color ) {
									$bg_inverted_colors = ( isset( $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] ) && '' != $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] ) ? $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] : '';
									?>
									<div class="bpquotes-single-color-section">
										<div id="bpquotes-edit-background-color-<?php echo esc_attr( $_key ); ?>" class="bpquotes-single-color" style="background-color:<?php echo esc_attr( $color ); ?>">
											<a href="javascript:void(0)" class="bpquotes-remove-color" data-id="bpquotes-edit-color-<?php echo esc_attr( $_key ); ?>"><i class="fa fa-times" aria-hidden="true"></i></a>
										</div>
										<div id="bpquotes-edit-color-<?php echo esc_attr( $_key ); ?>" class="bpquotes-edit-color-section" >
											<input type="hidden" name="bpquotes_gnrl_settings[bg_colors][]" class="regular-text bpquotes-hidden-input " value="<?php echo esc_attr( $color ); ?>" data-id="bpquotes-edit-background-color-<?php echo esc_attr( $_key ); ?>">
											<label>
												<?php esc_html_e( 'Text Color', 'buddypress-quotes' ); ?>
											</label>
											<label>
												<input type="text" name="bpquotes_gnrl_settings[bg_inverted_colors][]" class="regular-text bpquotes-hidden-input bpquotes-color-field" value="<?php echo esc_attr( $bg_inverted_colors ); ?>">
											</label>
										</div>
									</div>
									<?php
								}
							}
							?>
						</div>
				</div>

				<div class="wbcom-settings-section-wrap bpquotes-section">
					<div class="wbcom-settings-section-options-heading bpquotes-heading">
						<h4><?php esc_html_e( 'Disable Quote Icon on Post Activity', 'buddypress-quotes' ); ?></h4>
						<p class="description">
							<?php esc_html_e( 'Enable this option to hide the quote icon from activity posts.', 'buddypress-quotes' ); ?>
						</p>
					</div>
					<div>
						<div class="wbcom-settings-section-options">
							<label class="wb-switch">
								<input name='bpquotes_gnrl_settings[bg_allow_quote_icon]' type='checkbox' id="bg_quote_icon" class="bg_quote_icon" value='yes' <?php ( isset($bpquotes_gnrl_settings['bg_allow_quote_icon']) ) ? checked($bpquotes_gnrl_settings['bg_allow_quote_icon'], 'yes') : ''; ?>/>
								<div class="wb-slider wb-round"></div>
							</label>
						</div>
					</div>
				</div>
				
				<div class="wbcom-settings-section-wrap bpquotes-section" id="bpquotes_user_role">
					<div class="wbcom-settings-section-options-heading bpquotes-heading bpquotes-heading">
						<h4><?php esc_html_e( 'Select User Roles', 'buddypress-quotes' ); ?></h4>
						<p class="description">
							<?php esc_html_e( 'Select the user roles that are allowed to publish quote-type activities.', 'buddypress-quotes' ); ?>
						</p>
					</div>
					<div class="wbcom-settings-section-options">
						<select class="bpquotes-multi-selectize" name="bpquotes_gnrl_settings[user_role][]" multiple>
							<?php foreach ( $user_roles as $role => $rname ) {
								$selected = ( ! empty( $bpquotes_gnrl_settings['user_role'] ) && in_array( $role, $bpquotes_gnrl_settings['user_role'], true ) ) ? 'selected' : '';
								?>
							<option value="<?php echo esc_attr( $role ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_attr( $rname ); ?></option>
							<?php } ?>
						</select>				
					</div>		
				</div>
			</div>
		</div>
		<?php submit_button(); ?>
	</div>
	</form>
</div>
</div>
</div>

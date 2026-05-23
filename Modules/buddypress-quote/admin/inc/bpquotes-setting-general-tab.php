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

?>
<div class="wbcom-tab-content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bpquotes_gnrl_settings_section' );
		do_settings_sections( 'bpquotes_gnrl_settings_section' );
		?>
		<div class="container">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="blogname"><?php esc_html_e( 'Quotes background images', 'buddypress-quotes' ); ?></label><p class="description"><?php esc_html_e( '( Use 1024 x 512 px images for better experience )', 'buddypress-quotes' ); ?></p>
					</th>
					<td class="quotes-bg-images">
						<div class="bpquotes-img-upload-div">
							<a name="upload-btn" id="bpquotes-img-upload-btn"><i class="fa fa-plus" aria-hidden="true"></i></a>
						</div>
						<div class="bpquotes-background-images">

							<?php
							if ( isset( $bpquotes_gnrl_settings['image_url'] ) && ! empty( $bpquotes_gnrl_settings['image_url'] ) ) {
								foreach ( $bpquotes_gnrl_settings['image_url'] as $key => $url ) {
									echo '<div class="bpquotes-single-img">';
									echo '<a href="javascript:void(0)" class="bpquotes-remove-img"><i class="fa fa-trash" aria-hidden="true"></i></a>';
									echo '<img src="' . esc_url( $url ) . '">';
									echo '<input type="hidden" name="bpquotes_gnrl_settings[image_url][]" class="regular-text bpquotes-hidden-input" value="' . esc_url( $url ) . '">';
									echo '</div>';
								}
							}
							?>
						</div>
					</td>
					<tr>
						<th scope="row"><label for="blogname"><?php esc_html_e( 'Quotes background color', 'buddypress-quotes' ); ?></label>
						</th>
						<td>
							<span class="bpquotes-colorpicker">
								<input type="text" value="" id="bpquotes-bg-color" class="bpquotes-color-field" />
								<span><?php esc_html_e( 'Background Color', 'buddypress-quotes' ); ?></span>
							</span>
							<span class="bpquotes-colorpicker">
								<input type="text" value="" id="bpquotes-inverted-color" class="bpquotes-color-field" />								
								<span><?php esc_html_e( 'Quote Text Color', 'buddypress-quotes' ); ?></span>
							</span>
							<a class="bpquotes-add-bgcolor"><?php esc_html_e( 'Add', 'buddypress-quotes' ); ?></a>
							<div class="bpquotes-background-colors">
								<?php
								if ( isset( $bpquotes_gnrl_settings['bg_colors'] ) && ! empty( $bpquotes_gnrl_settings['bg_colors'] ) ) {
									foreach ( $bpquotes_gnrl_settings['bg_colors'] as $_key => $color ) {
										$bg_inverted_colors = ( isset( $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] ) && '' != $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] ) ? $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] : '';
										echo '<div class="bpquotes-single-color" style="background-color:' . esc_attr( $color ) . '">';
										echo '<a href="javascript:void(0)" class="bpquotes-remove-color"><i class="fa fa-trash" aria-hidden="true"></i></a>';
										echo '<input type="hidden" name="bpquotes_gnrl_settings[bg_colors][]" class="regular-text bpquotes-hidden-input" value="' . esc_attr( $color ) . '"><input type="hidden" name="bpquotes_gnrl_settings[bg_inverted_colors][]" class="regular-text bpquotes-hidden-input" value="' . esc_attr( $bg_inverted_colors ) . '">';
										echo '</div>';
									}
								}
								?>
							</div>
						</td>
					</tr>
				</tr>
			</table>
		</div>
		<?php submit_button(); ?>
	</form>
</div>

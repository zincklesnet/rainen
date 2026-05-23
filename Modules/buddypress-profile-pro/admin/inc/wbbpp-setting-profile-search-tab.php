<?php
/**
 *
 * This template is used for field settings at admin end.
 *
 * @package Buddypress_Profile_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$bprm_settings = get_site_option( 'wbbpp_profile_fields_settings' );
} else {
	$bprm_settings = get_option( 'wbbpp_profile_fields_settings' );
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$grp_args = get_site_option( 'wbbpp_profile_groups_settings' );
} else {
	$grp_args = get_option( 'wbbpp_profile_groups_settings' );
}

if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$profile_search_fields = get_site_option( 'wbbpp_profile_search_fields' );
} else {
	$profile_search_fields = get_option( 'wbbpp_profile_search_fields' );
}



$bprm_fields_type = bprm_resume_field_types();
?>
<div class="wbcom-tab-content">
	<div class="wbcom-wrapper-admin bprm-gen-settings-wrap">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'BuddyPress Profile Search Fields', 'buddypress-profile-pro' ); ?></h3>
		</div>
		<div class="wbcom-wrapper-section wbcom-admin-option-wrap bprm-gen-settings-container bprm-profile-search-fields-wrap">		
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wbbpp_profile_search_fields_section' );
				do_settings_sections( 'wbbpp_profile_search_fields_section' );
			?>
			<div class="form-table">
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Enable profile search', 'buddypress-profile-pro' ); ?></label>
					<p class="description"><?php esc_html_e( 'Enable this option if you want display member search form on member directory. ', 'buddypress-profile-pro' ); ?></p>
					</div>
					<div class="wbcom-settings-section-options">				
						<input type='checkbox' id="bprm_enable_profile_search" name='wbbpp_profile_search_fields[enable_profile_search]'  class="regular-text" value='1' 
						<?php
						if ( isset( $profile_search_fields['enable_profile_search'] ) && 1 == $profile_search_fields['enable_profile_search'] ) :
							?>
							checked <?php endif; ?> />
					</div>
				</div>
			</div>
			<div id="bprm-profile-search-fields" class="bprm-profile-search-fields" 
			<?php
			if ( ! isset( $profile_search_fields['enable_profile_search'] ) || '' === $profile_search_fields['enable_profile_search'] ) :
				?>
				style="display:none;"<?php endif; ?>>
			<div class="wbcom-settings-section-wrap">	
				<div class="bprm-field-header">
					<span class="bprm-col1">&nbsp;</span>
					<span class="bprm-col2"><strong><?php esc_html_e( 'Search Field', 'buddypress-profile-pro' ); ?></strong></span>
					<span class="bprm-col3"><strong><?php esc_html_e( 'Field Label', 'buddypress-profile-pro' ); ?></strong></span>
					<span class="bprm-col4"><strong><?php esc_html_e( 'Field Description', 'buddypress-profile-pro' ); ?></strong></span>
					<span class="bprm-col5"><strong><?php esc_html_e( 'Search Mode', 'buddypress-profile-pro' ); ?></strong></span>
					<span class="bprm-col6">&nbsp;</span>
				</div>
				<div id="bprm-field-content" class="bprm-field-content">
				<?php
				if ( ! empty( $profile_search_fields['field_name'] ) && isset( $profile_search_fields['field_name'] ) ) :
					$count = count( $profile_search_fields['field_name'] );
					for ( $j = 0; $j < $count; $j++ ) :
						$field_label = (isset($profile_search_fields['field_label'][ $j ])) ? $profile_search_fields['field_label'][ $j ] : '';
						$field_description = (isset($profile_search_fields['field_description'][ $j ])) ? $profile_search_fields['field_description'][ $j ] : '';
						?>
					<div class="search_field">
						<span class="bprm-col1">&nbsp;&#x21C5;</span>
						<span class="bprm-col2">
							<label class="responsive"><?php esc_html_e( 'Search Field', 'buddypress-profile-pro' ); ?></label>
							<?php echo wbbpp_bprm_profile_fields_dropdown( $profile_search_fields['field_name'][ $j ] );  // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</span>
						<span class="bprm-col3">
							<label class="responsive"><?php esc_html_e( 'Field Label', 'buddypress-profile-pro' ); ?></label>
							<input type="text" name="wbbpp_profile_search_fields[field_label][]" class="text-field" value="<?php echo esc_attr( $field_label ); ?>" />
						</span>
						<span class="bprm-col4">
							<label class="responsive"><?php esc_html_e( 'Field Description', 'buddypress-profile-pro' ); ?></label>
							<input type="text" name="wbbpp_profile_search_fields[field_description][]" class="text-field" value="<?php echo esc_attr( $field_description ); ?>" />
						</span>
						<span class="bprm-col5">
							<label class="responsive"><?php esc_html_e( 'Search Mode', 'buddypress-profile-pro' ); ?></label>
							<?php
							$filters = wbbpp_get_bprm_search_filters( $profile_search_fields['field_name'][ $j ] );
							?>
							<select class='select-field' name="wbbpp_profile_search_fields[field_mode][]">
							<?php
							foreach ( $filters as $key => $label ) {
								$selected = ( isset($profile_search_fields['field_mode'][ $j ]) && $profile_search_fields['field_mode'][ $j ] === $key ) ? " selected='selected'" : '';
								echo "<option value='$key'$selected>$label</option>\n";  // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
							</select>

						</span>
						<span class="bprm-col6"><a href="javascript:void(0)" class="delete_bprm_field"><?php esc_html_e( 'Delete', 'buddypress-profile-pro' ); ?></a></span>
						<span class="bprm_spinner"></span>
					</div>
					<?php endfor; ?>
				<?php endif; ?>
				</div>
				<div class="bprm-add-field">
					<a href="javascript:void(0)" id="add-bprm-search-field" class="add-bprm-search-field" ><?php esc_html_e( 'Add Field', 'buddypress-profile-pro' ); ?></a>
				</div>
			</div>
			</div>

			<?php submit_button(); ?>
		</form>
		</div>
	</div>
</div>

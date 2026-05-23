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


$bprm_fields_type = bprm_resume_field_types();
?>
<div class="wbcom-tab-content">
<div class="bprm-gen-settings-wrap">
	<div class="bprm-gen-settings-container bprm-profile-fields-wrap">
		<div class="wbcom-admin-option-wrap">
		<div class="bprm-group-field-container wbcom-settings-section-wrap pro-fields">
			<div class="wbcom-admin-title-section">
				<h3><?php esc_html_e( 'BuddyPress Profile Fields', 'buddypress-profile-pro' ); ?></h3>
			</div>
		<form method="post" action="options.php">			
			<?php
			settings_fields( 'wbbpp_profile_fields_settings_section' );
				do_settings_sections( 'wbbpp_profile_fields_settings_section' );
			?>
			<div class="bprm-group-tabs">
				<?php
				if ( ! empty( $grp_args ) && is_array( $grp_args ) ) {

					foreach ( $grp_args as $grp_key => $group_info ) {
						?>
						<div class="bprm-group-tab-link-container">
						<span class="bprm-tab-description">
							<i class="fa fa-question-circle"></i>
							<?php echo esc_html( $group_info['g_desc'] ); ?>
						</span>
						<div class="bprm-group-tabs-link"><?php echo esc_attr( $group_info['g_name'] ); ?>
						</div>
						<div class="bprm-group-tabs-content <?php echo esc_attr( $grp_key ); ?>">
							<ul class="ui-sortable" id="<?php echo esc_attr( $grp_key ); ?>">
						<?php
						if ( isset( $bprm_settings[ $grp_key ] ) ) {
							$fields = $bprm_settings[ $grp_key ];
						} else {
							$fields = '';
						}


						if ( ! empty( $fields ) && is_array( $fields ) ) {
							?>

								<li class="bprm-field-li ui-sortable-handle" style="position: relative;left: 0px;top: 0px;display: none;">
										<input type="hidden" class="bprm-field-title-text" name="wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][bprm_identifier][field_tile]" value="to restrict group from getting deleted">
								</li>
								<?php foreach ( $fields as $field_name => $field_detail ) { ?>
									<?php if ( 'bprm_identifier' !== $field_name ) { ?>
									<li class="bprm-field-li ui-sortable-handle" style="position: relative;left: 0px;top: 0px;">
										<span class="bprm-tab-field-title">
										<?php
										if ( ! empty( $field_detail['field_tile'] ) ) {
												echo esc_attr( $field_detail['field_tile'] );
										}
										?>
										</span>
									<div class="bprm-field-zone">
										<table class="form-table">
											<tr>
												<th scope="row"><label><?php esc_html_e( 'Field Title', 'buddypress-profile-pro' ); ?></label>
												</th>
												<td>
													<input type="text" class="bprm-field-title-text" name="wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][<?php echo esc_attr( $field_name ); ?>][field_tile]" value="<?php echo ( $field_detail['field_tile'] ) ? esc_attr( $field_detail['field_tile'] ) : ''; ?>">
												</td>
											</tr>
											<tr>
												<th scope="row"><label><?php esc_html_e( 'Field Type', 'buddypress-profile-pro' ); ?></label>
												</th>
												<td>
													<select name="wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][<?php echo esc_attr( $field_name ); ?>][field_type][type]" class="bprm_rs_type_change">
														<?php foreach ( $bprm_fields_type as $field_type => $field_text ) { ?>
																<option value="<?php echo esc_attr( $field_type ); ?>" <?php selected( $field_detail['field_type']['type'], $field_type ); ?>><?php echo esc_attr( $field_text ); ?></option>
														<?php } ?>
													</select>
													<span class="bprm-onchange-type-loader"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
												</td>
											</tr>
											<tr class="bprm_rs_type_change_options_html">
											</tr>
											<?php
											if ( 'selectize' === $field_detail['field_type']['type'] || 'dropdown' === $field_detail['field_type']['type'] || 'checkbox' === $field_detail['field_type']['type'] || 'radio_button' === $field_detail['field_type']['type'] || 'text_dropdown' === $field_detail['field_type']['type'] ) {

												if ( is_array( $field_detail['field_type']['options'] ) && ! empty( $field_detail['field_type']['options'] ) ) {
													?>

												<tr class='bprm-fld-existing-optn-html'>
													<th scope='row'><label><?php esc_html_e( 'Field Options', 'buddypress-profile-pro' ); ?></label>
													</th>
													<td>
													<?php
													foreach ( $field_detail['field_type']['options'] as $key => $option_value ) {
														?>
														<div class='bprm-fld-option-html'>
															<input type='text' name='wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][<?php echo esc_attr( $field_name ); ?>][field_type][options][]'  value='<?php echo esc_attr( $option_value ); ?>'>
															<span>
																<a href='JavaScript:void(0)' data-id='bprm-fld-option-html' class='bprm-add-option'><i class='fa fa-plus-circle' aria-hidden='true'></i></a>
																<a href='JavaScript:void(0)' data-id='bprm-fld-option-html' class='bprm-remove-option'><i class='fa fa-trash-o' aria-hidden='true'></i></a>
															</span>
														</div>
													<?php } ?>
													</td>
												</tr>
													<?php
												}
											}
											?>
											<tr style="display: none;">
												<th scope="row"><label><?php esc_html_e( 'Field Group', 'buddypress-profile-pro' ); ?></label>
												</th>
												<td>
													<select name="wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][<?php echo esc_attr( $field_name ); ?>][field_grp]">
														<?php foreach ( $grp_args as $grp_key_index => $grp_infos ) { ?>
																<option value="<?php echo esc_attr( $grp_key_index ); ?>"<?php selected( $field_detail['field_grp'], $grp_key_index ); ?> ><?php echo esc_attr( $grp_infos['g_name'] ); ?>
																</option>
														<?php } ?>
													</select>
												</td>
											</tr>
											<tr>
												<th scope="row"><label><?php esc_html_e( 'Display', 'buddypress-profile-pro' ); ?></label>
												</th>
												<td>
													<input type="checkbox" name="wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][<?php echo esc_attr( $field_name ); ?>][display]" value="yes" 
													<?php
													if ( isset( $field_detail['display'] ) ) {
														checked( $field_detail['display'], 'yes' );}
													?>
														>
												</td>
											</tr>
											<tr>
												<th scope="row"><label><?php esc_html_e( 'Required', 'buddypress-profile-pro' ); ?></label>
												</th>
												<td>
													<input type="checkbox" name="wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][<?php echo esc_attr( $field_name ); ?>][required]" value="yes"  
													<?php
													if ( isset( $field_detail['required'] ) ) {
														checked( $field_detail['required'], 'yes' );}
													?>
														>
												</td>
											</tr>
											<tr>
												<th scope="row"><label><?php esc_html_e( 'Repeater' , 'buddypress-profile-pro' ); ?></label>
												</th>
												<td>
													<input data-group-repeater="<?php echo (isset($group_info['repeater']))? esc_attr( $group_info['repeater'] ) : 'no';?>" type="checkbox" class="bprm-repeater-form-input" name="wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][<?php echo esc_attr( $field_name ); ?>][repeater]" value="yes"  
																															<?php
																															if ( isset( $field_detail['repeater'] ) ) {
																																checked( $field_detail['repeater'], 'yes' );}
																															?>
														>
												</td>
											</tr>

											<tr class="bprm_nf_show_on_register_page" <?php if(isset( $field_detail['repeater'] ) || ( isset($group_info['repeater']) && $group_info['repeater'] == 'yes') ):?> style="display:none" <?php endif;?>>
												<th scope="row"><label><?php esc_html_e( 'Show on register page' , 'buddypress-profile-pro' ); ?></label>
												</th>
												<td>
													<input type="checkbox" class="bprm_nf_show_on_register" name="wbbpp_profile_fields_settings[<?php echo esc_attr( $grp_key ); ?>][<?php echo esc_attr( $field_name ); ?>][bprm_nf_show_on]" value="register"  
																<?php ( isset( $field_detail['bprm_nf_show_on'] ) ) ? 
																checked( $field_detail['bprm_nf_show_on'], 'register' ) : '' ?>
														>
												</td>
											</tr>												
											<tr class="bprm-show-meta-field-info">
												<th scope="row"><label><?php esc_html_e( 'Group Key' , 'buddypress-profile-pro' ); ?></label>
												</th>
												<td class="textToCopy">
													<input type="text" value="<?php echo esc_attr($grp_key); ?>" readonly>
													<button class="copywbbpText"><?php esc_html_e( 'Copy' , 'buddypress-profile-pro' ); ?></button>
													
												</td>
											</tr>
											<tr class="bprm-show-meta-field-info">
												<th scope="row"><label><?php esc_html_e( 'Field Key' , 'buddypress-profile-pro' ); ?></label>
												</th>
												<td class="textToCopy">
													<input type="text" value="<?php echo esc_attr($field_name); ?>" readonly>
													<button class="copywbbpText"><?php esc_html_e( 'Copy' , 'buddypress-profile-pro' ); ?></button>
												</td>
											</tr>										
											<tr class="bprm-show-meta-field-info">
												<th scope="row"><label><?php esc_html_e( 'Meta Key' , 'buddypress-profile-pro' ); ?></label>
												</th>
												<td class="textToCopy">
													<input type="text" value="<?php echo 'wbbpp_' .esc_attr($grp_key). '_' . esc_attr($field_name); ?>" readonly>
													<button class="copywbbpText"><?php esc_html_e( 'Copy' , 'buddypress-profile-pro' ); ?></button>
												</td>
											</tr>
										</table>
									</div>
									<div class="bprm-field-actions">
										<a href="javascript:void(0)" class="bprm-remove-field-zone">
											<i class="fa fa-trash-o" aria-hidden="true"></i>
										</a>
										<a href="javascript:void(0)" class="bprm-show-field-zone">
											<i class="fa fa-cog" aria-hidden="true"></i>
										</a>
										<a href="javascript:void(0)" class="bprm-show-meta-field-zone">
											<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
										</a>
									</div>
								</li>
								<?php } ?>
								<?php } ?>
				<?php } //end check condition for empty fields ?>
						</ul>
						</div>
					</div>
						<?php
					}// end foreach of group args
				} //end if condition check for empty group array and bprm settings array
				?>
			</div>
			<?php submit_button(); ?>
		</form>
		</div>
		<div class="wbcom-settings-section-wrap bprm-add-new-field-container">
			<div class="wbcom-admin-title-section wbcom-heading-title-content">
				<h3><?php esc_html_e( 'Add New Field', 'buddypress-profile-pro' ); ?></h3>
			<p><?php esc_html_e( 'You can add any field to your BuddyPress profile group such as City, Address, or anything else with below form.', 'buddypress-profile-pro' ); ?></p>
			<p>
				<a href="javascript:void(0)" style="display: none;" class="bprm-add-new-field-link bprm-settings-field-btn"><?php esc_html_e( 'Add New Field', 'buddypress-profile-pro' ); ?></a>
			</p>
			</div>
			<div class="bprm-add-new-form-container">
			<form id="bprm-add-new-form" method="post" action="">
				<div class="form-table">
						<div class="bprm-groups-key-field-item">
							<label><?php esc_html_e( 'Field Title', 'buddypress-profile-pro' ); ?></label>
								<span class="bprm-description"><?php esc_html_e( 'Enter title for this field.', 'buddypress-profile-pro' ); ?></span>
								<span class="bprm_nf_error"><?php esc_html_e( 'Please enter field name.', 'buddypress-profile-pro' ); ?></span>
								<input name="bprm_nf_title" type="text" id="bprm_nf_title" value="" class="bprm-new-form-input">
							</div>
						<div class="bprm-groups-key-field-item">
						<label><?php esc_html_e( 'Field Type', 'buddypress-profile-pro' ); ?></label>
						<span class="bprm-description bprm-description-full"><?php esc_html_e( 'The type of field, a user is going to enter this data (via text, selecting a choice from dropdown, etc)', 'buddypress-profile-pro' ); ?></span>
							<span class="bprm-nf-loader"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
							<span class="bprm_nf_error"><?php esc_html_e( 'Please select field type.', 'buddypress-profile-pro' ); ?></span>
								<select name="bprm_nf_type" type="text" id="bprm_nf_type" class="bprm-new-form-input">
									<?php foreach ( $bprm_fields_type as $field_type => $field_text ) { ?>
									<option value="<?php echo esc_attr( $field_type ); ?>"><?php echo esc_attr( $field_text ); ?></option>
									<?php } ?>
								</select>							
						</div>
						<div class="bprm-groups-key-field-item bprm-field-type-html">
						</div>
						<div class="bprm-groups-key-field-item">
							<label><?php esc_html_e( 'Field Group', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Select the group which the field belongs to.', 'buddypress-profile-pro' ); ?></span>
							<span class="bprm-nf-oth-loader"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
							<span class="bprm_nf_error"><?php esc_html_e( 'Please select field group.', 'buddypress-profile-pro' ); ?></span>
								<select name="bprm_nf_group" type="text" id="bprm_nf_group" class="bprm-new-form-input">
									<?php foreach ( $grp_args as $grp_key_index => $grp_infos ) { ?>
										<option value="<?php echo esc_attr( $grp_key_index ); ?>" data-repeater="<?php echo (isset($grp_infos['repeater'])) ? esc_attr( $grp_infos['repeater'] ) : 'no'?>"><?php echo esc_attr( $grp_infos['g_name'] ); ?></option>
									<?php } ?>
								</select>
						</div>
						<div class="bprm-groups-key-field-item bprm_field_section_title" valign="top">
							<label><?php esc_html_e( 'Field Section Title', 'buddypress-profile-pro' ); ?></label>
								<span class="bprm-description"><?php esc_html_e( 'Enter title for section of this field.', 'buddypress-profile-pro' ); ?></span>
								<span class="bprm_nf_error"><?php esc_html_e( 'Please enter section title for this field.', 'buddypress-profile-pro' ); ?></span>
								<input name="bprm_nf_sec_title" type="text" id="bprm_nf_sec_title" value="" class="bprm-new-form-input">
							</div>
						<div class="bprm-groups-key-field-item bprm_apprnc_section_field" valign="top">
							<label><?php esc_html_e( 'Appearence Section', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Select field appearence section.', 'buddypress-profile-pro' ); ?></span>
								<input name="bprm_nf_appr_sec" type="radio" id="bprm_nf_appr_sec1" value="bprm_content_area" class="bprm-new-form-input"><span class="bprm-radio-text"><?php esc_html_e( 'Content Area', 'buddypress-profile-pro' ); ?></span>	
								<input name="bprm_nf_appr_sec" type="radio" id="bprm_nf_appr_sec2" value="bprm_sidebar_area" class="bprm-new-form-input"><span class="bprm-radio-text"><?php esc_html_e( 'Sidebar', 'buddypress-profile-pro' ); ?></span>
							</div>
						<div class="bprm-groups-key-field-item bprm_field_section_icon" valign="top">
							<label><?php esc_html_e( 'Field Section Icon', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Enter font awesome icon class for this field section(eg. fa fa-graduation-cap).', 'buddypress-profile-pro' ); ?></span>
								<input name="bprm_nf_sec_icon" type="text" id="bprm_nf_sec_icon" value="" class="bprm-new-form-input">
								
							</div>
						<div class="bprm-groups-key-field-item">
							<label><?php esc_html_e( 'Display', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Check this option if you want to make this field available in extended fields form.', 'buddypress-profile-pro' ); ?></span></td>
							<input name="bprm_nf_display" type="checkbox" id="bprm_nf_display" value="yes" class="bprm-new-form-input" checked="checked">							
						</div>
						<div class="bprm-groups-key-field-item">
							<label><?php esc_html_e( 'Required', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Check this option if you want to make this field required in extended fields form.', 'buddypress-profile-pro' ); ?></span>
							<input name="bprm_nf_required" type="checkbox" id="bprm_nf_required" value="yes" class="bprm-new-form-input">
							
						</div>
						<div class="bprm-groups-key-field-item">
							<label><?php esc_html_e( 'Repeater', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Check this option if you want to make this field as repeater field in extended fields form.', 'buddypress-profile-pro' ); ?></span>
							<input name="bprm_nf_repeater" type="checkbox" id="bprm_nf_repeater" value="yes" class="bprm-new-form-input">
						</div>
						
						<div id="bprm-field-item-on-register" class="bprm-groups-key-field-item">
							<label><?php esc_html_e( 'Show on register page', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Check this option if you want to show this field on the register page.', 'buddypress-profile-pro' ); ?></span>
							<input name="bprm_nf_show_on" type="checkbox" id="bprm_nf_show_on" value="register" class="bprm-new-form-input">
						</div>
						
						<div class="bprm-groups-key-field-item bprm-groups-add-field-button">
							<a href="javascript:void(0)" class="bprm-settings-field-btn wbbpp_save_new_field"><?php esc_html_e( 'Add', 'buddypress-profile-pro' ); ?></a>
							<a href="#" class="bprm-settings-field-btn bprm-cancel-new-field-link"><?php esc_html_e( 'Cancel', 'buddypress-profile-pro' ); ?></a>
						</div>
				</div>
			</form>
			</div>
		</div>
		</div>
	</div>
</div>
</div> <!-- closing of div class wbcom-tab-content -->

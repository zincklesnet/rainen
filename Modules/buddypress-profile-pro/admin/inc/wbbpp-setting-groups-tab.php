<?php
/**
 *
 * This template is used for group settings at admin end.
 *
 * @package Buddypress_Profile_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$bprm_grp_stngs = get_site_option( 'wbbpp_profile_groups_settings' );
} else {
	$bprm_grp_stngs = get_option( 'wbbpp_profile_groups_settings' );
}
$bprm_group_area = bprm_groups_display_area();

global $wp_roles;

$user_roles = $wp_roles->get_names();
$all_index  = array( 'all' => __( 'All', 'buddypress-profile-pro' ) );
$user_roles = array_merge( $all_index + $user_roles );

$member_types_exist = false;
$args               = array();
$member_types       = bp_get_member_types( $args, $output = 'object' );
if ( ! empty( $member_types ) ) {
	$member_types_exist = true;
}
$all_mt_index = array( 'all' => (object) array( 'labels' => array( 'name' => 'All' ) ) );
$member_types = array_merge( $all_mt_index + $member_types );
?>
<div class="wbcom-tab-content">
<div class="bprm-gen-settings-wrap">
	<div class="wbcom-admin-option-wrap">
	<div class="bprm-gen-settings-container bprm-profile-fields-wrap">
		<div class="wbcom-settings-section-wrap bprm-group-field-container">
			<div class="wbcom-admin-title-section">
				<h3><?php esc_html_e( 'BuddyPress Profile Groups', 'buddypress-profile-pro' ); ?></h3>
			</div>
			<form method="post" action="options.php">				
				<?php
					settings_fields( 'wbbpp_profile_groups_settings_section' );
					do_settings_sections( 'wbbpp_profile_groups_settings_section' );
				?>
				<div class="bprm-group-tabs">
					<?php
					if ( ! empty( $bprm_grp_stngs ) && is_array( $bprm_grp_stngs ) ) {
						foreach ( $bprm_grp_stngs as $grp_key => $group_info ) {
							?>
								<div class="bprm-group-tab-link-container">
									<div class="bprm-gp-tabs-link"><span class="brpm_grp_name"><?php echo esc_attr( $group_info['g_name'] ); ?></span>
										<span class="bprm-group-actions">
										<a href="javascript:void(0)" class="bprm-remove-group-zone"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
										<a href="javascript:void(0)" class="bprm-show-group-zone"><i class="fa fa-cog" aria-hidden="true"></i></a>
									</span>
									</div>

									<div class="bprm-group-tabs-content <?php echo esc_attr( $grp_key ); ?>">
										<div class="bprm-groups-zone">
											<div class="form-table">
												<div class="bprm-groups-key-field-item">
													<label><?php esc_html_e( 'Group Title', 'buddypress-profile-pro' ); ?></label>
														<input type="text" class="bprm-group-title-text" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][g_name]" value="<?php echo ( $group_info['g_name'] ) ? esc_attr( $group_info['g_name'] ) : ''; ?>">
													</div>
												<div class="bprm-groups-key-field-item">
													<label><?php esc_html_e( 'Group Description', 'buddypress-profile-pro' ); ?></label>
													<textarea name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][g_desc]"><?php echo ( $group_info['g_desc'] ) ? esc_html( $group_info['g_desc'] ) : ''; ?></textarea>
												</div>
												<div class="bprm-groups-key-field-item" style="display: none;">
													<label><?php esc_html_e( 'Group Key', 'buddypress-profile-pro' ); ?></label>
														<input type="text" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][g_key]" value="<?php echo ( $group_info['g_key'] ) ? esc_attr( $group_info['g_key'] ) : ''; ?>">		
												</div>	
												<div class="bprm-groups-key-field-item wbbpp-group-display-area-tr">
												<label><?php esc_html_e( 'Group Display Area', 'buddypress-profile-pro' ); ?></label>
														<select name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][g_area]">
															<?php foreach ( $bprm_group_area as $area => $area_text ) { ?>
															<option value="<?php echo esc_attr( $area ); ?>" <?php selected( $group_info['g_area'], $area ); ?>><?php echo esc_attr( $area_text ); ?></option>
															<?php } ?>
														</select>													
												</div>
												<div class="bprm-groups-key-field-item">
												   <label><?php esc_html_e( 'Display Group at BuddyPress Profile', 'buddypress-profile-pro' ); ?></label>													
														<input type="checkbox" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][profile_display]" value="yes" 
														<?php
														if ( isset( $group_info['profile_display'] ) ) {
															checked( $group_info['profile_display'], 'yes' );}
														?>
														>													
												</div>
												<div class="bprm-groups-key-field-item wbbpp-resume-display-tr">
													<label><?php esc_html_e( 'Display Group at Resume', 'buddypress-profile-pro' ); ?></label>													
														<input type="checkbox" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][resume_display]" value="yes" 
														<?php
														if ( isset( $group_info['resume_display'] ) ) {
															checked( $group_info['resume_display'], 'yes' );}
														?>
														>													
												</div>
												<div class="bprm-groups-key-field-item tr-repeater-group">
												<label><?php esc_html_e( 'Repeater', 'buddypress-profile-pro' ); ?></label>
														<input type="checkbox" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][repeater]" value="yes" 
														<?php
														if ( isset( $group_info['repeater'] ) ) {
															checked( $group_info['repeater'], 'yes' );}
														?>
														>													
												</div>
												<div class="bprm-groups-key-field-item">
													<label><?php esc_html_e( 'Group Availability', 'buddypress-profile-pro' ); ?></label>
													<input class="wbbpp-grp-avail" data-id="wbbpp-user-roles-list" type="radio" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][grp_avail]" value="user_roles" 
														<?php
														if ( isset( $group_info['grp_avail'] ) ) {
															checked( $group_info['grp_avail'], 'user_roles' );}
														?>
														>
														<span><?php esc_html_e( 'User roles', 'buddypress-profile-pro' ); ?></span>
														<input class="wbbpp-grp-avail" data-id="wbbpp-mem-typ-list" type="radio" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][grp_avail]" value="mem_type" 
														<?php
														if ( isset( $group_info['grp_avail'] ) ) {
															checked( $group_info['grp_avail'], 'mem_type' );}
														?>
														>
														<span><?php esc_html_e( 'Member type', 'buddypress-profile-pro' ); ?></span>
													</div>
												<?php
												if ( isset( $group_info['grp_avail'] ) && 'user_roles' === $group_info['grp_avail'] ) {
													$style = '';
												} else {
													$style = 'display:none';
												}
												?>
												<div class="bprm-groups-key-field-item wbbpp-grp-avail-class" style="<?php echo esc_attr( $style ); ?>" id="wbbpp-user-roles-list">
													<label><?php esc_html_e( 'Select user roles', 'buddypress-profile-pro' ); ?></label>												
														<select class="wbbpp-user-roles-list-select" id="wbbpp-user-roles-list" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][roles][]" multiple data-placeholder="<?php esc_html_e( 'Select user role', 'buddypress-profile-pro' ); ?>">
														<?php foreach ( $user_roles as $slug => $role_name ) { ?>
																<?php $selected = ( ! empty( $group_info['roles'] ) && in_array( $slug, $group_info['roles'], true ) ) ? 'selected' : ''; ?>
																<option value="<?php echo esc_attr( $slug ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $role_name ); ?></option>
															<?php } ?>
														</select>													
												</div>
												<?php
												if ( isset( $group_info['grp_avail'] ) && 'mem_type' === $group_info['grp_avail'] ) {
													$style = '';
												} else {
													$style = 'display:none';
												}
												?>

												<div class="bprm-groups-key-field-item wbbpp-grp-avail-class" style="<?php echo esc_attr( $style ); ?>" id="wbbpp-mem-typ-list">
													<label><?php esc_html_e( 'Select member types', 'buddypress-profile-pro' ); ?></label>	
														<select class="wbbpp-mem-typ-list-select"  id="wbbpp-mem-typ-list" name="wbbpp_profile_groups_settings[<?php echo esc_attr( $grp_key ); ?>][mtypes][]" multiple data-placeholder="<?php esc_html_e( 'Select member type', 'buddypress-profile-pro' ); ?>">
														<?php foreach ( $member_types as $slug => $type_obj ) { ?>
																<?php $selected = ( ! empty( $group_info['mtypes'] ) && in_array( $slug, $group_info['mtypes'], true ) ) ? 'selected' : ''; ?>
																<option value="<?php echo esc_attr( $slug ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $type_obj->labels['name'] ); ?></option>
															<?php } ?>
														</select>
													</div>
											</div>
										</div>
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

		<div class="bprm-add-new-field-container">
			<div class="wbcom-settings-section-wrap">
			<div class="wbcom-admin-title-section wbcom-heading-title-content">
				<h3><?php esc_html_e( 'Add New Group', 'buddypress-profile-pro' ); ?></h3>
				<p><?php esc_html_e( 'You can add new group to your extended fileds such as Sports Experience, Certifications, or anything else with below form.', 'buddypress-profile-pro' ); ?></p>
			</div>			
			
			<div class="bprm-add-new-form-container">
				<form id="bprm-add-new-group-form" method="post" action="">
					<div class="form-table">
							<div class="bprm-groups-key-field-item">
								<label><?php esc_html_e( 'Group Title', 'buddypress-profile-pro' ); ?></label>
								<span class="bprm-description"><?php esc_html_e( 'Enter title for this group.', 'buddypress-profile-pro' ); ?></span>
									<span class="bprm_gp_error"><?php esc_html_e( 'Please enter group title.', 'buddypress-profile-pro' ); ?></span>
									<input name="bprm_gp_title" type="text" id="bprm_gp_title" value="" class="bprm-new-form-input">
								</div>
							<div class="bprm-groups-key-field-item">
								<label><?php esc_html_e( 'Group Description', 'buddypress-profile-pro' ); ?></label>
								<span class="bprm-description"><?php esc_html_e( 'Enter group description.', 'buddypress-profile-pro' ); ?></span>
									<span class="bprm_gp_error"><?php esc_html_e( 'Please enter group description.', 'buddypress-profile-pro' ); ?></span>
									<textarea name="bprm_gp_desc" id="bprm_gp_desc" class="bprm-new-form-input"></textarea>		
							</div>
							<div class="bprm-groups-key-field-item wbbpp-group-display-area-tr">
							<label><?php esc_html_e( 'Group Display Area', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Enter group description.', 'buddypress-profile-pro' ); ?></span>
							<span class="bprm_gp_error"><?php esc_html_e( 'Please select group display area in resume.', 'buddypress-profile-pro' ); ?></span>
									<select name="bprm_gp_display_area">
										<?php foreach ( $bprm_group_area as $area => $area_text ) { ?>
										<option value="<?php echo esc_attr( $area ); ?>"><?php echo esc_attr( $area_text ); ?></option>
										<?php } ?>
									</select>									
								</div>
							<div class="bprm-groups-key-field-item">
							<label><?php esc_html_e( 'Display Group at BuddyPress Profile', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Check this option if you want to make this group available at BuddyPress Profile view.', 'buddypress-profile-pro' ); ?></span>
									<input name="bprm_gp_profile_display" type="checkbox" id="bprm_gp_profile_display" value="yes" class="bprm-new-form-input" checked="checked">									
								</div>
							<div class="bprm-groups-key-field-item wbbpp-resume-display-tr">
							<label><?php esc_html_e( 'Display Group at Resume', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm-description"><?php esc_html_e( 'Check this option if you want to make this group available in Resume.', 'buddypress-profile-pro' ); ?></span>
									<input name="bprm_gp_resume_display" type="checkbox" id="bprm_gp_resume_display" value="yes" class="bprm-new-form-input" checked="checked">									
								</div>
							<div class="bprm-groups-key-field-item tr-repeater-group">
								<label><?php esc_html_e( 'Repeater', 'buddypress-profile-pro' ); ?></label>
								<span class="bprm-description"><?php esc_html_e( 'Check this option if you want to make this group as repeater group.', 'buddypress-profile-pro' ); ?></span>
									<input type="checkbox" name="bprm_gp_repeater" value="yes" checked="checked">									
								</div>
							<div class="bprm-groups-key-field-item bprm-group-availability tr-group-availability">
								<label><?php esc_html_e( 'Group Availability', 'buddypress-profile-pro' ); ?></label>
									<input class="wbbpp-grp-avail" data-id="wbbpp-user-roles-list" type="radio" name="bprm_gp_avail" value="user_roles">
									<span class="bprm-description"><?php esc_html_e( 'User roles', 'buddypress-profile-pro' ); ?></span>
									<input class="wbbpp-grp-avail" data-id="wbbpp-mem-typ-list" type="radio" name="bprm_gp_avail" value="mem_type">
									<span class="bprm-description"><?php esc_html_e( 'Member type', 'buddypress-profile-pro' ); ?></span>
								</div>
							<div class="bprm-groups-key-field-item bprm-group-availability wbbpp-grp-avail-class" id="wbbpp-user-roles-list" style="display: none;">
							<label><?php esc_html_e( 'Select user roles', 'buddypress-profile-pro' ); ?></label>
							<span class="bprm_gp_error"><?php esc_html_e( 'Please select atleast one user role.', 'buddypress-profile-pro' ); ?></span>
									<select class="wbbpp-user-roles-list-select" id="wbbpp-user-roles-list" name="wbbpp_grp_avail_user_roles[]" multiple required data-placeholder="<?php esc_html_e( 'Select user role', 'buddypress-profile-pro' ); ?>">
										<?php foreach ( $user_roles as $slug => $role_name ) { ?>
											<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $role_name ); ?></option>
										<?php } ?>
									</select>									
								</div>
							<div id="wbbpp-mem-typ-list" class="bprm-groups-key-field-item bprm-group-availability wbbpp-grp-avail-class" style="display: none;">
								<label><?php esc_html_e( 'Select member types', 'buddypress-profile-pro' ); ?></label>
								<span class="bprm_gp_error"><?php esc_html_e( 'Please select atleast one member type.', 'buddypress-profile-pro' ); ?></span>
									<select class="wbbpp-mem-typ-list-select" id="wbbpp-mem-typ-list" name="wbbpp_grp_avail_mem_type[]" multiple required data-placeholder="<?php esc_html_e( 'Select member type', 'buddypress-profile-pro' ); ?>">
										<?php foreach ( $member_types as $slug => $type_obj ) { ?>
											<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $type_obj->labels['name'] ); ?></option>
										<?php } ?>
									</select>									
								</div>
							<div class="bprm-groups-key-field-item bprm-groups-add-field-button">								
								<a href="javascript:void(0)" class="bprm-settings-field-btn wbbpp_save_new_group "><?php esc_html_e( 'Add', 'buddypress-profile-pro' ); ?></a>
								<a href="#" class="bprm-settings-field-btn bprm-cancel-new-group-link"><?php esc_html_e( 'Cancel', 'buddypress-profile-pro' ); ?></a>
							</div>
						</div>
				</form>
			</div>
		</div>
		</div>
	</div>
	</div>
</div>
</div> <!-- closing of div class wbcom-tab-content -->

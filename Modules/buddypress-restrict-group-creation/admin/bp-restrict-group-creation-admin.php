<?php 

/**
 * Gets an array of capabilities according to each user role.  Each role will return its caps, 
 * which are then added to the overall $capabilities array.
 *
 * Note that if no role has the capability, it technically no longer exists.  Since this could be 
 * a problem with folks accidentally deleting the default WordPress capabilities, the 
 * members_default_capabilities() will return those all the defaults.
 *
 * @since 0.1
 * @return $capabilities array All the capabilities of all the user roles.
 * @global $wp_roles array Holds all the roles for the installation.
 */
function etivite_bp_restrictgroups_admin_get_role_capabilities() {
	global $wp_roles;

	$capabilities = array();

	/* Loop through each role object because we need to get the caps. */
	foreach ( $wp_roles->role_objects as $key => $role ) {

		/* Roles without capabilities will cause an error, so we need to check if $role->capabilities is an array. */
		if ( is_array( $role->capabilities ) ) {

			/* Loop through the role's capabilities and add them to the $capabilities array. */
			foreach ( $role->capabilities as $cap => $grant )
				$capabilities[$cap] = $cap;
		}
	}

	/* Return the capabilities array. */
	return $capabilities;
}

/**
 * Checks if a specific capability has been given to at least one role. If it has,
 * return true. Else, return false.
 *
 * @since 0.1
 * @uses members_get_role_capabilities() Checks for capability in array of role caps.
 * @param $cap string Name of the capability to check for.
 * @return true|false bool Whether the capability has been given to a role.
 */
function etivite_bp_restrictgroups_admin_check_for_cap( $cap = '' ) {

	/* Without a capability, we have nothing to check for.  Just return false. */
	if ( !$cap )
		return false;

	/* Gets capabilities that are currently mapped to a role. */
	$caps = etivite_bp_restrictgroups_admin_get_role_capabilities();

	/* If the capability has been given to at least one role, return true. */
	if ( in_array( $cap, $caps ) )
		return true;

	/* If no role has been given the capability, return false. */
	return false;
}

function etivite_bp_restrictgroups_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['remove'] ) && check_admin_referer('etivite_bp_restrictgroups_admin_remove') ) {
		
		$data = maybe_unserialize( get_option( 'etivite_bp_restrictgroups' ) );
		
		if ($data) {
			
			if( isset($_POST['cap_remove'] ) && !empty($_POST['cap_remove']) ) {
				
				foreach ( (array) $_POST['cap_remove'] as $id ) {
					unset( $data[sanitize_text_field( $id )] );
				}
			}
			//$data = array_values($data);
			update_option( 'etivite_bp_restrictgroups', $data );
			$removed = true;
		}
		
	} elseif ( isset( $_POST['submit'] ) && check_admin_referer('etivite_bp_restrictgroups_admin_new') ) {
			
		$data = maybe_unserialize( get_option( 'etivite_bp_restrictgroups' ) );

		$newrule = Array();	
	
		//check for valid cap and update - if not keep old.
		if( isset($_POST['cap_low'] ) && !empty($_POST['cap_low']) ) {
			if ( etivite_bp_restrictgroups_admin_check_for_cap( sanitize_text_field( $_POST['cap_low'] ) ) ) {
		
		
//redo,stack n loop it
		
				if( isset($_POST['rg_post_count'] ) && !empty($_POST['rg_post_count']) && (int)$_POST['rg_post_count'] == 1 ) {
					
					$p = (int) sanitize_text_field( $_POST['rg_post_count_threshold'] );
					
					$enabled = true;
					if ( !$p || $p < 1 ) {
						$p = 0;
						$enabled = false;
					}
					
					$newrule['bp_restrictgroups_post_count'] = array( 'enabled' => $enabled, 'count' => $p);
				} else {
					$newrule['bp_restrictgroups_post_count'] = array( 'enabled' => false, 'count' => 0 );
				}
				
				if( isset($_POST['rg_status_count'] ) && !empty($_POST['rg_status_count']) && (int)$_POST['rg_status_count'] == 1 ) {
		
					$s = (int) sanitize_text_field( $_POST['rg_status_count_threshold'] );
					
					$enabled = true;
					if ( !$s || $s < 1 ) {
						$s = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_status_count'] = array( 'enabled' => $enabled, 'count' => $s);
				} else {
					$newrule['bp_restrictgroups_status_count'] = array( 'enabled' => false, 'count' => 0 );
				}
				
				if( isset($_POST['rg_friends_count'] ) && !empty($_POST['rg_friends_count']) && (int)$_POST['rg_friends_count'] == 1 ) {
		
					$f = (int) sanitize_text_field( $_POST['rg_friends_count_threshold'] );
					
					$enabled = true;
					if ( !$f || $f < 1 ) {
						$f = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_friends_count'] = array( 'enabled' => $enabled, 'count' => $f);
				} else {
					$newrule['bp_restrictgroups_friends_count'] = array( 'enabled' => false, 'count' => 0 );
				}
				
				if( isset($_POST['rg_days_count'] ) && !empty($_POST['rg_days_count']) && (int)$_POST['rg_days_count'] == 1 ) {
		
					$d = (int) sanitize_text_field( $_POST['rg_days_count_threshold'] );
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_days_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_days_count'] = array( 'enabled' => false, 'count' => 0 );
				}



				if( isset($_POST['rg_admin_count'] ) && !empty($_POST['rg_admin_count']) && (int)$_POST['rg_admin_count'] == 1 ) {
		
					$d = (int)sanitize_text_field( $_POST['rg_admin_count_threshold'] );
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_admin_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_admin_count'] = array( 'enabled' => false, 'count' => 0 );
				}


				if( isset($_POST['rg_mod_count'] ) && !empty($_POST['rg_mod_count']) && (int)$_POST['rg_mod_count'] == 1 ) {
		
					$d = (int)sanitize_text_field( $_POST['rg_mod_count_threshold'] );
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_mod_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_mod_count'] = array( 'enabled' => false, 'count' => 0 );
				}

				if( isset($_POST['rg_membership_count'] ) && !empty($_POST['rg_membership_count']) && (int)$_POST['rg_membership_count'] == 1 ) {
		
					$d = (int)sanitize_text_field( $_POST['rg_membership_count_threshold'] );
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_membership_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_membership_count'] = array( 'enabled' => false, 'count' => 0 );
				}

				if( isset($_POST['rg_created_count'] ) && !empty($_POST['rg_created_count']) && (int)$_POST['rg_created_count'] == 1 ) {
		
					$d = (int) sanitize_text_field( $_POST['rg_created_count_threshold'] );
					
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
					$newrule['bp_restrictgroups_created_count'] = array( 'enabled' => $enabled, 'count' => $d);
				} else {
					$newrule['bp_restrictgroups_created_count'] = array( 'enabled' => false, 'count' => 0 );
				}

				//if achievements is installed
				//if ( ACHIEVEMENTS_IS_INSTALLED ) {
					if( isset($_POST['rg_dpa_count'] ) && !empty($_POST['rg_dpa_count']) && (int)$_POST['rg_dpa_count'] == 1 ) {
		
						$d = (int) sanitize_text_field( $_POST['rg_dpa_count_threshold'] );
						
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
						$newrule['bp_restrictgroups_dpa_count'] = array( 'enabled' => $enabled, 'count' => $d);
					} else {
						$newrule['bp_restrictgroups_dpa_count'] = array( 'enabled' => false, 'count' => 0 );
					}
					
					if( isset($_POST['rg_dpa_score'] ) && !empty($_POST['rg_dpa_score']) && (int)$_POST['rg_dpa_score'] == 1 ) {
		
						$d = (int) sanitize_text_field( $_POST['rg_dpa_score_threshold'] );
						
					$enabled = true;
					if ( !$d || $d < 1 ) {
						$d = 0;
						$enabled = false;
					}
		
						$newrule['bp_restrictgroups_dpa_score'] = array( 'enabled' => $enabled, 'count' => $d);
					} else {
						$newrule['bp_restrictgroups_dpa_score'] = array( 'enabled' => false, 'count' => 0 );
					}
				//}
				
				if( isset($_POST['rg_display_error'] ) && !empty($_POST['rg_display_error']) && (int)$_POST['rg_display_error'] == 1 ) {
					$newrule['display_error'] = true;
				} else {
					$newrule['display_error'] = false;
				}
				
				$newrule['date_created'] = bp_core_current_time();
				
				unset( $data[ $_POST['cap_low'] ] );
				$data[ $_POST['cap_low'] ] = $newrule;
				
				update_option( 'etivite_bp_restrictgroups', $data );
		
				$updated = true;
				
			} else {
				$error[] = '<div id="message" class="error"><p>Invalid user wp capability - please see <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">WP Roles and Capabilities</a>.</p></div>';
			}
		} else {
			$error[] = '<div id="message" class="updated fade"><p>User capability was left blank - this is required.</p></div>';
		}
	}
	
	// Get the proper URL for submitting the settings form. (Settings API workaround) - boone
	$url_base = function_exists( 'is_network_admin' ) && is_network_admin() ? network_admin_url( 'admin.php?page=buddypress-restrict-grouo-creation-settings' ) : admin_url( 'admin.php?page=buddypress-restrict-grouo-creation-settings' );
	
?>	
	<div class="wrap">
		<h2><?php esc_attr_e( 'Restrict Group Creation', 'buddypress-restrict-grouo-creation' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . esc_attr__( 'Rule Added.', 'buddypress-restrict-grouo-creation' ) . "</p></div>"; endif;
		if ( isset($removed) && !isset($error) ) : echo "<div id='message' class='updated fade'><p>" . esc_attr__( 'Removed rule.', 'buddypress-restrict-grouo-creation' ) . "</p></div>"; endif;
		if ( isset($error) ) { 
			foreach( $error as $err) { 
				echo esc_attr($err);
			} 
		}
		if ( bp_get_option( 'bp_restrict_group_creation', '0' ) == 0 ) : echo "<div id='error' class='error fade'><p>" . esc_attr__("Warning: Please enable the \"Restrict group creation to Site Admins?\" ", "buddypress-restrict-grouo-creation") . "<a href=\"". esc_url( network_admin_url('/admin.php?page=bp-settings')) ."\">" . esc_attr__("setting", "buddypress-restrict-grouo-creation") . "</a>" . esc_attr__( "; otherwise the options below will be ignored.", "buddypress-restrict-grouo-creation") . "</p></div>"; endif; ?>

		<form action="<?php echo esc_attr($url_base) ?>" name="restrictgroups-rules-form" id="restrictgroups-rules-form" method="post">

			<div class="tablenav">
				<div class="alignleft actions">
					<input type="submit" class="button-secondary action" id="remove" name="remove" value="Remove Selected">
				</div>
				<br class="clear">
			</div>

			<table cellspacing="0" class="widefat fixed">			
				<thead>
				<tr class="thead">
					<th class="manage-column column-cb check-column" id="cb" scope="col"></th>
					<th class="manage-column column-wpcap" id="wpcap" scope="col" style="width:10%"><?php esc_attr__('Capabilities', 'bp_restrict_group_creation') ?></th>
					<th class="manage-column column-restrictions" id="restrictions" scope="col"><?php esc_attr__('Restrictions', 'bp_restrict_group_creation') ?></th>
					<th class="manage-column column-date" id="date" scope="col" style="width:10%"><?php esc_attr__('Display Error', 'bp_restrict_group_creation') ?></th>
					<th class="manage-column column-date" id="date" scope="col" style="width:15%"><?php esc_attr__('Date Created', 'bp_restrict_group_creation') ?></th>
				</tr>
				</thead>
	
				<tbody class="list:user user-list" id="users">
				<?php
				$rules = maybe_unserialize( get_option( 'etivite_bp_restrictgroups' ) );
				if ($rules) {
					foreach ($rules as $key => $value) { ?>
						<tr class="alternate">
						 <th class="check-column" scope="row"><input type="checkbox" value="<?php echo esc_attr($key); ?>"  name="cap_remove[]"></th>
						 <td class="username column-wpcap"><?php echo esc_attr($key); ?></td>
						 <td class="username column-restrictions">
							<?php
							echo "<p>Days member registered: ", ($value['bp_restrictgroups_days_count']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," Days: ". esc_attr($value['bp_restrictgroups_days_count']['count']) ."</p>";
							
							echo "<p>Min Number of friends: ", ($value['bp_restrictgroups_friends_count']['enabled']? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_friends_count']['count']) ."</p>";
							
							echo "<p>Min Number of activity updates: ", ($value['bp_restrictgroups_status_count']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_status_count']['count']) ."</p>";
							
							echo "<p>Min Number of group forum posts: ", ($value['bp_restrictgroups_post_count']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_post_count']['count']) ."</p>";

							echo "<p>Max Groups Admin: ", ($value['bp_restrictgroups_admin_count']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_admin_count']['count']) ."</p>";
							
							echo "<p>Max Group Memberships: ", ($value['bp_restrictgroups_membership_count']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_membership_count']['count']) ."</p>";

							echo "<p>Max Group Moderator: ", ($value['bp_restrictgroups_mod_count']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_mod_count']['count']) ."</p>";
							
							echo "<p>Max Groups Created: ", ($value['bp_restrictgroups_created_count']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_created_count']['count']) ."</p>";

							//if achievements is installed
							if ( defined('ACHIEVEMENTS_IS_INSTALLED') && ACHIEVEMENTS_IS_INSTALLED == 1 ) {
								echo "<p>Achievement Count: ", ($value['bp_restrictgroups_dpa_count']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_dpa_count']['count']) ."</p>";
								
								echo "<p>Achievement Score: ", ($value['bp_restrictgroups_dpa_score']['enabled'] ? '<span style="color:green">enabled</span>' : '<span style="color:red">disabled</span>') ," : ". esc_attr($value['bp_restrictgroups_dpa_score']['count']) ."</p>";
							} ?>
						 </td>
						 <?php echo "<td class=\"date column-date\">", ($value['display_error'] ? "<span style=\"color:green\">enabled</span>" : "<span style=\"color:red\">disabled</span>") ,"</td>"; ?>
						 <td class="date column-date"><?php echo esc_attr($value['date_created']); ?></td>
						</tr>
					<?php } 
				} else {?>
					<tr>
						<th></th>
						<td colspan="3"><?php esc_attr_e('no rules found', 'buddypress-restrict-grouo-creation'); ?></td>
					</tr>
				<?php } ?>
				</tbody>

			</table>
		
		<?php wp_nonce_field( 'etivite_bp_restrictgroups_admin_remove' ); ?>

		</form>

		<h3><?php esc_attr_e('Add New', 'buddypress-restrict-grouo-creation'); ?></h3>

		<form action="<?php echo esc_url(network_admin_url('/admin.php?page=buddypress-restrict-grouo-creation-settings'))?>" name="restrictgroups-settings-form" id="restrictgroups-settings-form" method="post">

		<h4><?php esc_attr_e( 'WP User Capabilities', 'buddypress-restrict-grouo-creation' ); ?></h4>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="cap_low"><?php esc_attr_e( 'User capability level', 'buddypress-restrict-grouo-creation' ) ?></label></th>
					<td><input type="text" name="cap_low" id="cap_low" value="" /></td>
				</tr>
			</table>
			
			<div class="description">
				<p><?php esc_attr_e('*User capability is required (ie: edit_posts).', 'buddypress-restrict-grouo-creation'); ?></p>
				<p><?php esc_attr_e('Please refer to the ', 'buddypress-restrict-grouo-creation'); ?><a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table"><?php esc_attr_e('Codex for WP Caps', 'buddypress-restrict-grouo-creation'); ?></a></p>
			</div>
			
			
		<h4><?php esc_attr_e( 'Member Registered Since (Days)', 'buddypress-restrict-grouo-creation' ); ?></h4>	
	
			<table class="form-table">
				<tr>
					<th><label for="rg_days_count"><?php esc_attr_e('Enable Days Since Threshold','buddypress-restrict-grouo-creation') ?></label></th>
					<td><input type="checkbox" name="rg_days_count" id="rg_days_count" value="1" /><?php esc_attr_e(' Days: ', 'buddypress-restrict-grouo-creation'); ?><input type="text" name="rg_days_count_threshold" id="rg_days_count_threshold" /> </td>
				</tr>			
			</table>
			
			<div class="description">
				<p><?php esc_attr_e('Number of ', 'buddypress-restrict-grouo-creation'); ?><strong><?php esc_attr_e('days', 'buddypress-restrict-grouo-creation'); ?></strong><?php esc_attr_e(' registered.', 'buddypress-restrict-grouo-creation'); ?></p>
			</div>
			
		<h4><?php esc_attr_e( 'Count Threshold Settings', 'buddypress-restrict-grouo-creation' ); ?></h4>

			<table class="form-table">

				<?php if ( bp_is_active( 'friends' ) ) { ?>
				<tr>
					<th><label for="rg_friends_count"><?php esc_attr_e('Enable Min Friends Count','buddypress-restrict-grouo-creation') ?></label></th>
					<td><input type="checkbox" name="rg_friends_count" id="rg_friends_count" value="1" /><?php esc_attr_e(' Count: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_friends_count_threshold" id="rg_friends_count_threshold" value="" /> </td>
				</tr>
				<?php } ?>

				<?php if ( bp_is_active( 'activity' ) ) { ?>
				<tr>
					<th><label for="rg_status_count"><?php esc_attr_e('Enable Min Status Count','buddypress-restrict-grouo-creation') ?></label></th>
					<td><input type="checkbox" name="rg_status_count" id="rg_status_count" value="1" /><?php esc_attr_e(' Count: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_status_count_threshold" id="rg_status_count_threshold" value="" /> </td>
				</tr>
				<?php } ?>

				<?php if ( function_exists('bp_forums_is_installed_correctly') && bp_forums_is_installed_correctly() ) { ?>
				<tr>
					<th><label for="rg_post_count"><?php esc_attr_e('Enable Min Forum Post Count','buddypress-restrict-grouo-creation') ?></label></th>
					<td><input type="checkbox" name="rg_post_count" id="rg_post_count" value="1" /><?php esc_attr_e(' Count: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_post_count_threshold" id="rg_post_count_threshold" value="" /> </td>
				</tr>
				<?php } ?>
				
				
				<tr>
					<th><label for="rg_membership_count"><?php esc_attr_e('Enable Max Groups Memberships','buddypress-restrict-grouo-creation') ?></label></th>
					<td><input type="checkbox" name="rg_membership_count" id="rg_membership_count" value="1" /><?php esc_attr_e(' Count: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_membership_count_threshold" id="rg_membership_count_threshold" value="" /> </td>
				</tr>

				<tr>
					<th><label for="rg_admin_count"><?php esc_attr_e('Enable Max Groups Admin','buddypress-restrict-grouo-creation') ?></label></th>
					<td><input type="checkbox" name="rg_admin_count" id="rg_admin_count" value="1" /><?php esc_attr_e(' Count: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_admin_count_threshold" id="rg_admin_count_threshold" value="" /> </td>
				</tr>

				<tr>
					<th><label for="rg_mod_count"><?php esc_attr_e('Enable Max Groups Mod','buddypress-restrict-grouo-creation') ?></label></th>
					<td><input type="checkbox" name="rg_mod_count" id="rg_mod_count" value="1" /><?php esc_attr_e(' Count: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_mod_count_threshold" id="rg_mod_count_threshold" value="" /> </td>
				</tr>

				<tr>
					<th><label for="rg_created_count"><?php esc_attr_e('Enable Max Groups Created','buddypress-restrict-grouo-creation') ?></label></th>
					<td><input type="checkbox" name="rg_created_count" id="rg_created_count" value="1" /><?php esc_attr_e(' Count: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_created_count_threshold" id="rg_created_count_threshold" value="" /> </td>
				</tr>				
				
				<?php		
				//if achievements is installed
				if ( defined('ACHIEVEMENTS_IS_INSTALLED') && ACHIEVEMENTS_IS_INSTALLED == 1 ) { ?>
					<tr>
						<th><label for="rg_dpa_count"><?php esc_attr_e('Enable Achievement Member Count Threshold','buddypress-restrict-grouo-creation') ?></label></th>
						<td><input type="checkbox" name="rg_dpa_count" id="rg_dpa_count" value="1" /><?php esc_attr_e(' Count: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_dpa_count_threshold" id="rg_dpa_count_threshold" value="" /> </td>
					</tr>
					
					<tr>
						<th><label for="rg_dpa_score"><?php esc_attr_e('Enable Achievement Member Score Threshold','buddypress-restrict-grouo-creation') ?></label></th>
						<td><input type="checkbox" name="rg_dpa_score" id="rg_dpa_score" value="1" /><?php esc_attr_e(' Score: ', 'buddypress-restrict-grouo-creation' ); ?><input type="text" name="rg_dpa_score_threshold" id="rg_dpa_score_threshold" value="" /> </td>
					</tr>
				<?php } ?>
				
			</table>
			
			<div class="description">
				<p>*<strong><?php esc_attr_e('Thresholds', 'buddypress-restrict-grouo-creation' ); ?></strong><?php esc_attr_e(': applicable to members who meet the user capability level.', 'buddypress-restrict-grouo-creation' ); ?></p>
			</div>
			
			<tr>
				<th><label for="rg_display_error"><?php esc_attr_e('Display error message/reason on group directory page','buddypress-restrict-grouo-creation') ?></label></th>
				<td><input type="checkbox" name="rg_display_error" id="rg_display_error" value="1" /></td>
			</tr>
			
			<?php wp_nonce_field( 'etivite_bp_restrictgroups_admin_new' ); ?>
			
			<p class="submit"><input type="submit" name="submit" value="Add New Rule"/></p>
			
		</form>
		
	</div>
<?php
}

?>

<?php
/**
 * Manage single member header
 *
 * @package Reign
 */

if ( ! function_exists( 'reign_rth_manage_member_header_pos_from_frontend' ) ) {

	/**
	 * Modify member header position based on user meta setting.
	 *
	 * This function checks the cached user meta for a custom header position. If not available,
	 * it retrieves the value from the database, caches it for future use, and returns the
	 * custom position if set. Otherwise, it returns the default position.
	 *
	 * @param string $member_header_position The default position of the member header.
	 *
	 * @return string The position of the member header, either custom from user meta or default.
	 */
	function reign_rth_manage_member_header_pos_from_frontend( $member_header_position ) {
		$user_id = bp_displayed_user_id();

		// Cache the user meta retrieval to reduce database queries.
		$header_view = wp_cache_get( 'wbtm_user_header_view_' . $user_id );

		if ( false === $header_view ) {
			$header_view = get_user_meta( $user_id, 'wbtm_user_header_view', true );
			wp_cache_set( 'wbtm_user_header_view_' . $user_id, $header_view, '', 600 ); // Cache for 10 minutes.
		}

		if ( isset( $header_view['position'] ) ) {
			return $header_view['position'];
		}

		return $member_header_position;
	}

	add_filter( 'wbtm_rth_manage_member_header_position', 'reign_rth_manage_member_header_pos_from_frontend' );
}

if ( ! function_exists( 'reign_rth_manage_member_header_type_from_frontend' ) ) {

	/**
	 * Modify member header type based on user meta setting.
	 *
	 * This function checks the cached user meta for a custom header type. If not available,
	 * it retrieves the value from the database, caches it for future use, and returns the
	 * custom type if set. Otherwise, it returns the default type.
	 *
	 * @param string $member_header_class The default CSS class for the member header type.
	 *
	 * @return string The CSS class for the member header type, either custom from user meta or default.
	 */
	function reign_rth_manage_member_header_type_from_frontend( $member_header_class ) {
		$user_id = bp_displayed_user_id();

		// Cache the user meta retrieval to reduce database queries.
		$header_view = wp_cache_get( 'wbtm_user_header_view_' . $user_id );

		if ( false === $header_view ) {
			$header_view = get_user_meta( $user_id, 'wbtm_user_header_view', true );
			wp_cache_set( 'wbtm_user_header_view_' . $user_id, $header_view, '', 600 ); // Cache for 10 minutes.
		}

		if ( isset( $header_view['type'] ) ) {
			return $header_view['type'];
		}

		return $member_header_class;
	}

	add_filter( 'wbtm_rth_manage_member_header_class', 'reign_rth_manage_member_header_type_from_frontend' );
}

/**
 * Save the user social fields data
 *
 * @since Reign 1.0.0
 * */
if ( ! function_exists( 'reign_rtm_save_header_view_info' ) ) {

	/**
	 * Save user-specific header view information and clear cache.
	 *
	 * This function saves the user's header view preferences from the profile update form
	 * to the user meta and clears the corresponding cache to ensure the updated settings
	 * are immediately reflected.
	 *
	 * @param int   $user_id          The ID of the user whose profile is being updated.
	 * @param array $posted_field_ids An array of field IDs that were updated.
	 * @param array $errors           An array of error messages if any errors occurred during the profile update.
	 */
	function reign_rtm_save_header_view_info( $user_id, $posted_field_ids, $errors ) {
		if ( empty( $user_id ) ) {
			return;
		}

		if ( isset( $_POST['wbtm_user_header_view']['identifier'] ) && '1' === $_POST['wbtm_user_header_view']['identifier'] ) {
			update_user_meta( $user_id, 'wbtm_user_header_view', $_POST['wbtm_user_header_view'] );

			// Invalidate the cache after updating user meta.
			wp_cache_delete( 'wbtm_user_header_view_' . $user_id );
		}
	}

	add_action( 'xprofile_updated_profile', 'reign_rtm_save_header_view_info', 1, 3 );
}

if ( ! function_exists( 'reign_rtm_header_view_mgmt_section' ) ) {

	/**
	 * Display user profile header view management section in the frontend.
	 *
	 * This function adds a section to the BuddyPress user profile edit screen
	 * where users can select their preferred header position and layout. It checks
	 * if the profile group ID is valid and if the profile header view feature is enabled
	 * in the theme settings. The selected options are saved in user meta and can be
	 * dynamically updated.
	 *
	 * @param int|bool $user_id Optional. The ID of the user. If not provided, the displayed user ID is used.
	 */
	function reign_rtm_header_view_mgmt_section( $user_id = false ) {
		if ( ! $user_id ) {
			$user_id = bp_displayed_user_id();
		}

		if ( ! function_exists( 'bp_get_the_profile_group_id' ) || ( function_exists( 'bp_get_the_profile_group_id' ) && bp_get_the_profile_group_id() != 1 ) ) {
			return;
		}

		global $wbtm_reign_settings;

		$header_view = (array) get_user_meta( $user_id, 'wbtm_user_header_view', true );
		$header_view = apply_filters( 'reign_get_user_social_array', $header_view, $user_id );

		if ( 'edit' == bp_current_action() && isset( $wbtm_reign_settings['reign_buddyextender']['enable_profile_header_view'] ) && $wbtm_reign_settings['reign_buddyextender']['enable_profile_header_view'] == 'on' ) {
			?>
			<div class="editfield field_name required-field visibility-public field_type_textbox">
				<fieldset>
					<legend><?php esc_html_e( 'Profile Header View', 'reign' ); ?></legend>
					<div class="wbtm-rtm-header-view">
						<input type="hidden" name="wbtm_user_header_view[identifier]" value="1">
						<div class="bp-profile-field editfield">
							<label>
								<?php esc_html_e( 'Select Member Header Position', 'reign' ); ?>
								<div class="rtm-tooltip">?
									<span class="rtm-tooltiptext">
										<?php esc_html_e( 'Select Member Header Position', 'reign' ); ?>
									</span>
								</div>
							</label>
							<?php
							$header_view = get_user_meta( $user_id, 'wbtm_user_header_view', true );

							if ( isset( $header_view['position'] ) ) {
								$member_header_position = $header_view['position'];
							} else {
								$member_header_position = isset( $wbtm_reign_settings['reign_buddyextender']['member_header_position'] ) ? $wbtm_reign_settings['reign_buddyextender']['member_header_position'] : 'inside';
							}

							$member_header_positions = array(
								'inside' => array(
									'name'    => __( 'Inside', 'reign' ),
									'img_url' => '',
								),
								'top'    => array(
									'name'    => __( 'Top', 'reign' ),
									'img_url' => '',
								),
							);

							echo '<select name="wbtm_user_header_view[position]">';
							foreach ( $member_header_positions as $slug => $position ) {
								echo '<option value="' . esc_attr( $slug ) . '" ' . selected( $member_header_position, $slug, false ) . '>' . esc_html( $position['name'] ) . '</option>';
							}
							echo '</select>';
							?>
						</div>
						<hr/>
						<div class="bp-profile-field editfield">
							<label>
								<?php esc_html_e( 'Select Member Header Layout', 'reign' ); ?>
								<div class="rtm-tooltip">?
									<span class="rtm-tooltiptext">
										<?php esc_html_e( 'Select Member Header Layout', 'reign' ); ?>
									</span>
								</div>
							</label>
							<?php
							if ( isset( $header_view['type'] ) ) {
								$member_header_type = $header_view['type'];
							} else {
								$member_header_type = isset( $wbtm_reign_settings['reign_buddyextender']['member_header_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['member_header_type'] : 'wbtm-cover-header-type-1';
							}

							$member_header_types = array(
								'wbtm-cover-header-type-1' => array(
									'name'    => __( 'Layout #1', 'reign' ),
									'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-1.jpg',
								),
								'wbtm-cover-header-type-2' => array(
									'name'    => __( 'Layout #2', 'reign' ),
									'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-2.jpg',
								),
								'wbtm-cover-header-type-3' => array(
									'name'    => __( 'Layout #3', 'reign' ),
									'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-3.jpg',
								),
								'wbtm-cover-header-type-4' => array(
									'name'    => __( 'Layout #4', 'reign' ),
									'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-4.jpg',
								),
							);

							echo '<div class="wbtm-radio-img-selector-sec">';
							echo '<ul>';
							foreach ( $member_header_types as $slug => $header ) {
								echo '<li>';
								echo '<input type="radio" name="wbtm_user_header_view[type]" value="' . esc_attr( $slug ) . '" id="member-' . esc_attr( $slug ) . '" ' . checked( $member_header_type, $slug, false ) . ' />';
								echo '<label for="member-' . esc_attr( $slug ) . '"><img src="' . esc_url( $header['img_url'] ) . '" alt="' . esc_attr( $header['name'] ) . '" /><span>' . esc_html( $header['name'] ) . '</span></label>';
								echo '</li>';
							}
							echo '</ul>';
							echo '</div>';
							?>
						</div>
					</div>
				</fieldset>
			</div>
			<?php
		}
	}

	add_action( 'bp_after_profile_field_content', 'reign_rtm_header_view_mgmt_section' );
}

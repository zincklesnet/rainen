<?php
/**
 * Social fields
 *
 * @package Reign
 */

if ( ! function_exists( 'reign_user_social_fields' ) ) {
	add_action( 'after_switch_theme', 'reign_rtm_set_default_social_fields' );

	/**
	 * Sets default values for social link fields in the theme options.
	 *
	 * This function checks if the 'wbtm_social_links' settings are present and not empty.
	 * If they are missing or empty, it initializes them with default values for Facebook,
	 * Twitter, and LinkedIn. The settings are then updated in the WordPress options table.
	 *
	 * @return void
	 */
	function reign_rtm_set_default_social_fields() {
		global $wbtm_reign_settings;

		// Ensure settings are properly initialized.
		if ( ! isset( $wbtm_reign_settings['reign_buddyextender']['wbtm_social_links'] ) || empty( $wbtm_reign_settings['reign_buddyextender']['wbtm_social_links'] ) ) {
			$wbtm_reign_settings['reign_buddyextender']['wbtm_social_links'] = array(
				'facebook' => array(
					'img_url' => '',
					'name'    => __( 'Facebook', 'reign' ),
				),
				'twitter'  => array(
					'img_url' => '',
					'name'    => __( 'Twitter', 'reign' ),
				),
				'linkedin' => array(
					'img_url' => '',
					'name'    => __( 'LinkedIn', 'reign' ),
				),
			);
			update_option( 'reign_options', $wbtm_reign_settings );
		}
	}
}

/**
 * Save the user social fields data
 *
 * @since Reign 1.0.0
 * */
if ( ! function_exists( 'reign_user_social_fields_save' ) ) {
	add_action( 'xprofile_updated_profile', 'reign_user_social_fields_save', 1, 3 );

	/**
	 * Processes and saves social link data from the profile edit form.
	 *
	 * @param int   $user_id           The ID of the user whose profile is being updated.
	 * @param array $posted_field_ids Array of field IDs that were updated.
	 * @param array $errors          Array of errors encountered during profile update.
	 *
	 * @return void
	 */
	function reign_user_social_fields_save( $user_id, $posted_field_ids, $errors ) {
		if ( empty( $user_id ) ) {
			return;
		}

		$socials = get_user_meta( $user_id, 'wbtm_user_social_links', true );
		if ( ! is_array( $socials ) ) {
			$socials = array();
		}

		if ( isset( $_POST['wbtm_user_social_links'] ) && '1' === $_POST['wbtm_user_social_links'] ) {
			foreach ( reign_get_user_social_array() as $field_slug => $social ) {
				$url                    = isset( $_POST[ 'wbcom_social_' . $field_slug ] ) ? reign_sanitize_social_link_url( $_POST[ 'wbcom_social_' . $field_slug ] ) : '';
				$socials[ $field_slug ] = $url;
				update_user_meta( $user_id, $field_slug, $url );
			}
			update_user_meta( $user_id, 'wbtm_user_social_links', $socials );
			
			// Clear caches to ensure social links display immediately
			wp_cache_delete( "wbtm_user_social_links_{$user_id}", 'user_meta' );
			wp_cache_delete( $user_id, 'user_meta' );
			
			// Clear BuddyPress cache if available
			if ( function_exists( 'bp_core_reset_cache' ) ) {
				bp_core_reset_cache( $user_id );
			}
		}
	}
}

if ( ! function_exists( 'reign_user_social_fields' ) ) {
	add_action( 'bp_after_profile_field_content', 'reign_user_social_fields' );

	/**
	 * Displays user social fields based on profile context.
	 *
	 * This function handles the display of user social fields in the BuddyPress profile. It conditionally renders
	 * these fields based on whether the user is in edit mode or view mode and ensures that social links are
	 * only shown if enabled in the settings. The function also makes sure that the fields are displayed only
	 * in the base profile group.
	 *
	 * @param int|bool $user_id User ID. If false, defaults to the displayed user ID.
	 */
	function reign_user_social_fields( $user_id = false ) {
		// Default to the displayed user ID if none is provided.
		if ( ! $user_id ) {
			$user_id = bp_displayed_user_id();
		}

		// Ensure we're in the base profile group (ID = 1).
		if ( ! function_exists( 'bp_get_the_profile_group_id' ) || bp_get_the_profile_group_id() != 1 ) {
			return;
		}

		global $wbtm_reign_settings;

		// Retrieve and filter user social links.
		$socials = (array) get_user_meta( $user_id, 'wbtm_user_social_links', true );
		$socials = apply_filters( 'reign_get_user_social_array', $socials, $user_id );

		// Check if profile social links are enabled in the settings.
		if ( isset( $wbtm_reign_settings['reign_buddyextender']['enable_profile_social_links'] ) &&
			'on' === $wbtm_reign_settings['reign_buddyextender']['enable_profile_social_links'] ) {

			if ( 'edit' === bp_current_action() ) {
				// Display fields for editing the user's social links.
				?>
				<div class="editfield field_name required-field visibility-public field_type_textbox">
					<fieldset>
						<legend><?php esc_html_e( 'Social', 'reign' ); ?></legend>
						<div class="wbcom-user-social">
							<input type="hidden" name="wbtm_user_social_links" value="1">
							<?php foreach ( reign_get_user_social_array() as $field_slug => $social ) { ?>
								<div class="bp-profile-field editfield field_type_textbox field_<?php echo esc_attr( $field_slug ); ?>">
									<label for="wbcom_social_<?php echo esc_attr( $field_slug ); ?>"><?php echo esc_html( $social['name'] ); ?></label>
									<input id="wbcom_social_<?php echo esc_attr( $field_slug ); ?>" name="wbcom_social_<?php echo esc_attr( $field_slug ); ?>" type="url" value="<?php echo esc_attr( $socials[ $field_slug ] ?? '' ); ?>" />
								</div>
							<?php } ?>
						</div>
					</fieldset>
				</div>
				<?php
			} else {
				// Display fields for viewing social links.
				$social_links_array = reign_get_user_social_array();

				if ( reign_array_not_all_empty( $socials, $social_links_array ) && ! empty( $social_links_array ) ) {
					$edit_profile_link = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() . '/edit/group/' );
					?>
					<div class="bp-widget group-separator-block social">
						<header class="profile-loop-header profile-header flex align-items-center">
							<h3 class="entry-title rg-profile-title"><?php esc_html_e( 'Social', 'reign' ); ?></h3>
							<?php if ( bp_is_my_profile() ) { ?>
								<a href="<?php echo esc_url( $edit_profile_link . bp_get_the_profile_group_id() ); ?>" class="push-right button outline small"><?php esc_html_e( 'Edit', 'reign' ); ?></a>
							<?php } ?>
						</header>
						<table class="profile-fields">
							<tbody>
								<?php foreach ( $social_links_array as $field_slug => $social ) : ?>
									<?php
									if ( empty( $socials[ $field_slug ] ) ) {
										continue;
									}
									$field_value = esc_url( $socials[ $field_slug ] );
									if ( $field_value !== $socials[ $field_slug ] ) {
										continue;
									}
									?>
									<tr class="field_type_textbox field_<?php echo esc_attr( $field_slug ); ?>">
										<td class="label"><?php echo esc_html( $social['name'] ); ?></td>
										<td class="data"><a href="<?php echo esc_url( $field_value ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $socials[ $field_slug ] ); ?></a></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php
				}
			}
		}
	}
}

if ( ! function_exists( 'reign_get_user_social_array' ) ) {
	/**
	 * Retrieves the default social links array from the global settings.
	 *
	 * This function accesses the global `$wbtm_reign_settings` array to get the default
	 * social links configured for user profiles. If no social links are set, it returns an empty array.
	 *
	 * @return array The array of default social links from the global settings, or an empty array if none are set.
	 */
	function reign_get_user_social_array() {
		global $wbtm_reign_settings;

		// Ensure the social links are set and return them.
		return reign_sanitize_social_links_config( $wbtm_reign_settings['reign_buddyextender']['wbtm_social_links'] ?? array() );
	}
}

if ( ! function_exists( 'reign_array_not_all_empty' ) ) {
	/**
	 * Checks if not all fields in an associative array are empty.
	 *
	 * This function iterates over a set of backend keys and checks if at least one
	 * corresponding value in the user profile links array is not empty.
	 *
	 * @param array $user_social_links Array of user social profile links.
	 * @param array $backend_keys Array of backend key-value pairs.
	 *
	 * @return bool True if at least one link is not empty, false otherwise.
	 */
	function reign_array_not_all_empty( $user_social_links, $backend_keys ) {
		foreach ( $backend_keys as $key => $value ) {
			if ( isset( $user_social_links[ $key ] ) && ! empty( $user_social_links[ $key ] ) ) {
				return true;
			}
		}

		return false;
	}
}

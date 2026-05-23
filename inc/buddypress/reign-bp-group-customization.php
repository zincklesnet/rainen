<?php
/**
 * BuddyPress Groups Customization File
 *
 * @package Reign
 */

/**
 * GROUP DIRECTORY CUSTOMIZATION
 */

/**
 * Showing group cover image
 *
 * @since 1.0.7
 */
if ( ! function_exists( 'reign_render_group_cover_image' ) ) {
	add_action( 'reign_before_group_avatar_group_directory', 'reign_render_group_cover_image' );

	/**
	 * Renders the group cover image in the group directory.
	 *
	 * This function checks the settings to determine whether the cover image should be displayed
	 * for the group directory. It retrieves the group cover image URL using BuddyPress functions
	 * and displays it. If a specific cover image is not set for a group, a default image is used.
	 * The image URL is cached to improve performance and is set to be lazy-loaded.
	 *
	 * The cover image is only rendered if the group directory type is not set to 'wbtm-group-directory-type-1'.
	 * The cover image URL is cached for 10 minutes to reduce the number of times it needs to be fetched.
	 *
	 * @global array $wbtm_reign_settings Global settings array for the Reign theme.
	 *
	 * @return void
	 */
	function reign_render_group_cover_image() {
		global $wbtm_reign_settings;

		// Determine the group directory type, defaulting to 'wbtm-group-directory-type-2'.
		$group_directory_type = isset( $wbtm_reign_settings['reign_buddyextender']['group_directory_type'] )
			? $wbtm_reign_settings['reign_buddyextender']['group_directory_type']
			: 'wbtm-group-directory-type-2';

		// Only render cover image if the group directory type is not 'wbtm-group-directory-type-1'.
		if ( $group_directory_type !== 'wbtm-group-directory-type-1' ) {
			$group_id = bp_get_group_id(); // Get the current group ID.

			// Try to get the cover image URL from the cache.
			$cover_img_url = wp_cache_get( 'group_cover_image_' . $group_id );

			if ( ! $cover_img_url ) {
				// If not cached, fetch the cover image URL.
				$args          = array(
					'object_dir' => 'groups',
					'item_id'    => $group_id,
					'type'       => 'cover-image',
				);
				$cover_img_url = bp_attachments_get_attachment( 'url', $args );

				// If no custom cover image, use the default cover image.
				if ( empty( $cover_img_url ) ) {
					$cover_img_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] )
						? $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url']
						: REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
				}

				// Cache the cover image URL for 10 minutes.
				wp_cache_set( 'group_cover_image_' . $group_id, $cover_img_url, '', 600 );
			}

			// Output the cover image HTML with lazy loading.
			echo '<div class="wbtm-group-cover-img"><img src="' . esc_url( $cover_img_url ) . '" loading="lazy" alt="" /></div>'; // Decorative cover image — alt intentionally empty.
		}
	}
}

/**
* Showing group type icon
*
* @since 1.0.7
*/
if ( ! function_exists( 'reign_bp_directory_groups_item_show_grp_type' ) ) {
	add_action( 'bp_directory_groups_item', 'reign_bp_directory_groups_item_show_grp_type' );

	/**
	 * Outputs the group type and status icon in the BuddyPress group directory.
	 *
	 * This function hooks into the `bp_directory_groups_item` action to display the group type
	 * and an icon representing the group's status (public, hidden, private). If the group status
	 * does not match any predefined type, a default icon is shown. The icon and group type
	 * are wrapped in a container that uses a class based on the group status.
	 *
	 * @global object $groups_template The global BuddyPress groups template object containing
	 *                                 information about the current group being displayed.
	 *
	 * @return void
	 */
	function reign_bp_directory_groups_item_show_grp_type() {
		global $groups_template;
		$group_status = $groups_template->group->status;

		// Predefined icons for group status.
		$icons = array(
			'public'  => '<i class="far fa-globe"></i>',
			'hidden'  => '<i class="far fa-user-secret"></i>',
			'private' => '<i class="far fa-lock"></i>',
			'default' => '<i class="far fa-cog"></i>',
		);

		// Use the appropriate icon or default.
		$icon_html = isset( $icons[ $group_status ] ) ? $icons[ $group_status ] : $icons['default'];

		?>
		<div class="wbtm-bp-grp-type-<?php echo esc_attr( $group_status ); ?>">
		<?php echo wp_kses_post( $icon_html ); // Output the HTML with allowed tags. ?>
			<?php bp_group_type(); ?>
		</div>
		<?php
	}
}

/**
* Showing group type statistics
*
* @since 1.0.7
*/
if ( ! function_exists( 'reign_render_bp_directory_groups_items' ) ) {
	add_action( 'reign_bp_directory_groups_data', 'reign_render_bp_directory_groups_items' );

	/**
	 * Renders meta information for groups in the BuddyPress group directory.
	 *
	 * This function outputs a set of metadata for each group in the BuddyPress group directory,
	 * including last active time, activity post count, and member count. It checks the version
	 * of BuddyPress to determine the appropriate URL functions to use. The data is displayed with
	 * tooltips and icons, and caching is used for group activity counts to optimize performance.
	 *
	 * The function hooks into the 'reign_bp_directory_groups_data' action to display the group meta
	 * information in the directory layout.
	 *
	 * @global object $groups_template The global BuddyPress groups template object that contains
	 *                                 information about the current group being displayed.
	 *
	 * @return void
	 */
	function reign_render_bp_directory_groups_items() {
		$info_array = array();
		$group_id   = bp_get_group_id();
		$url_to_use = function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' )
			? bp_get_group_url()
			: bp_get_group_permalink();

		// Last active data.
		$info_array['last_active'] = array(
			'tooltip_text' => bp_get_group_last_active(),
			'url'          => $url_to_use,
			'icon_class'   => 'far fa-clock',
			'color'        => '#EC7063',
			'extra_class'  => 'last-active-section',
		);

		// Cache the activity post count.
		if ( bp_is_active( 'activity' ) ) {
			$total_activity_in_grp = wp_cache_get( 'group_activity_count_' . $group_id );

			if ( false === $total_activity_in_grp ) {
				global $bp, $wpdb;
				$total_activity_in_grp = $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id = '$group_id'" );
				wp_cache_set( 'group_activity_count_' . $group_id, $total_activity_in_grp, '', 600 ); // Cache for 10 minutes.
			}

			// Manage singular and plural for activities.
			$activity_label = ( $total_activity_in_grp == 1 ) ? __( '1 Activity', 'reign' ) : sprintf( __( '%s Activities', 'reign' ), number_format_i18n( $total_activity_in_grp ) );

			$info_array['activity_post_count'] = array(
				'tooltip_text' => $activity_label,
				'url'          => $url_to_use,
				'icon_class'   => 'far fa-pencil',
				'color'        => '#5DADE2',
				'extra_class'  => 'activity-count-section',
			);
		}

		// Member count.
		global $groups_template;
		$member_count = isset( $groups_template->group->total_member_count )
			? (int) $groups_template->group->total_member_count
			: 0;

		// Manage singular and plural for members.
		$member_label = sprintf( _n( '%s Member', '%s Members', $member_count, 'reign' ), number_format_i18n( $member_count ) );

		$info_array['member_count'] = array(
			'tooltip_text' => $member_label,
			'url'          => esc_url( $url_to_use . bp_get_members_slug() ),
			'icon_class'   => 'far fa-users',
			'color'        => '#F5B041',
			'extra_class'  => 'members-section',
		);

		// Output the group meta information.
		echo '<div class="wbtm-member-directory-meta">';
		foreach ( $info_array as $key => $info ) {
			$extra_class = isset( $info['extra_class'] ) ? ' ' . esc_attr( $info['extra_class'] ) : '';
			?>
			<div class="rtm-tooltip<?php echo esc_attr( $extra_class ); ?>" style="background: <?php echo esc_attr( $info['color'] ); ?>">
				<a href="<?php echo esc_url( $info['url'] ); ?>"><i class="<?php echo esc_attr( $info['icon_class'] ); ?>"></i></a>
				<span class="rtm-tooltiptext">
					<?php echo esc_html( $info['tooltip_text'] ); ?>
				</span>
			</div>
			<?php
		}
		echo '</div>';
	}
}

/**
 * GROUP SINGLE PAGE CUSTOMIZATION
 */

/**
* Showing group name on member cover image
*
* @since 1.0.7
*/
if ( ! function_exists( 'reign_bp_group_header_render_name_and_rating' ) ) {
	add_action( 'bp_before_group_header_meta', 'reign_bp_group_header_render_name_and_rating', 5 );

	/**
	 * Renders the group name in the BuddyPress group header.
	 *
	 * This function adds the group name to the BuddyPress group header by hooking into the
	 * 'bp_before_group_header_meta' action. It outputs the group's name in an `h2` tag
	 * wrapped in a `div` with a class of `item-title`. The group name is properly escaped
	 * using `esc_html()` to ensure security.
	 *
	 * @return void
	 */
	function reign_bp_group_header_render_name_and_rating() {
		?>
		<div class="item-title"><h2 class="user-nicename"><?php echo esc_html( bp_get_group_name() ); ?></h2></div>
		<?php
	}
}

/**
* Showing group statistics on member cover image
*
* @since 1.0.7
*/
if ( ! function_exists( 'reign_render_extra_group_info' ) ) {
	add_action( 'reign_group_extra_info_section', 'reign_render_extra_group_info' );

	/**
	 * Renders additional group information in the BuddyPress group extra info section.
	 *
	 * This function adds extra group information, such as the total number of activity posts
	 * and the member count, to a specified section in the BuddyPress group page. It fetches the
	 * data dynamically and caches the activity post count for performance optimization. The output
	 * includes the count and corresponding labels, properly escaped for security.
	 *
	 * @return void
	 */
	function reign_render_extra_group_info() {
		$info_array = array();
		$group_id   = bp_get_group_id();

		if ( bp_is_active( 'activity' ) ) {
			$total_activity_in_grp = wp_cache_get( 'group_activity_count_' . $group_id );

			if ( false === $total_activity_in_grp ) {
				global $bp, $wpdb;
				$total_activity_in_grp = $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id = '$group_id'" );
				wp_cache_set( 'group_activity_count_' . $group_id, $total_activity_in_grp, '', 600 ); // Cache for 10 minutes
			}

			$info_array['activity_post_count'] = array(
				'value' => $total_activity_in_grp,
				'label' => __( 'Posts', 'reign' ),
			);
		}

		global $groups_template;
		$member_count = isset( $groups_template->group->total_member_count )
			? (int) $groups_template->group->total_member_count
			: 0;

		$info_array['member_count'] = array(
			'value' => $member_count,
			'label' => __( 'Members', 'reign' ),
		);

		foreach ( $info_array as $key => $info ) {
			?>
			<div class="rtm-usermeta-box">
				<span class="rtm-usermeta-count">
					<?php echo esc_html( $info['value'] ); ?>
				</span>
				<span class="rtm-usermeta-text">
					<?php echo esc_html( $info['label'] ); ?>
				</span>
			</div>
			<?php
		}
	}
}

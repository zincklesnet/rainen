<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName -- Legacy filename referenced by theme includes; renaming would break load paths.
// phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed -- Legacy mixed file kept intact to preserve include behavior.
/**
 * BuddyPress Functions File
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Show avatars of user who liked a particular activity on activity directory
 * feature like facebook
 *
 * @since 1.0.7
 */
add_action( 'bp_activity_entry_content', 'reign_show_activity_like_avatars' );

if ( ! function_exists( 'reign_show_activity_like_avatars' ) ) {
	/**
	 * Display avatars of users who liked an activity, with tooltip.
	 */
	function reign_show_activity_like_avatars() {
		// Avoid conflicts with other reaction plugins.
		if ( is_plugin_active( 'buddypress-status/buddypress-status.php' ) || class_exists( 'Buddypress_Reactions' ) || ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) ) {
			return;
		}

		// Check if feature is enabled in settings.
		global $wbtm_reign_settings;
		if ( ! isset( $wbtm_reign_settings['reign_buddyextender']['enable_activity_like_avatars'] ) || 'on' !== $wbtm_reign_settings['reign_buddyextender']['enable_activity_like_avatars'] ) {
			return;
		}

		$activity_id = bp_get_activity_id();
		if ( ! $activity_id ) {
			return;
		}

		global $wpdb;
		$users = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id FROM {$wpdb->base_prefix}usermeta WHERE meta_key = %s AND (meta_value LIKE %s OR meta_value LIKE %s)",
				'bp_favorite_activities',
				'%i:' . $wpdb->esc_like( $activity_id ) . ';%',
				'%"' . $wpdb->esc_like( $activity_id ) . '";%'
			),
			ARRAY_A
		);

		if ( empty( $users ) || ! is_array( $users ) ) {
			return;
		}

		$num_of_avatar_count  = (int) apply_filters( 'reign_show_activity_like_avatars_count', 3 );
		$num_of_listing_count = (int) apply_filters( 'reign_show_activity_like_listing_count', 5 );
		$total_users          = count( $users );
		$shown_avatars        = min( $num_of_avatar_count, $total_users );
		$remaining_users      = max( 0, $total_users - $shown_avatars );
		$remaining_to_show    = min( $num_of_listing_count, $remaining_users );

		echo '<div class="wbtm_fav_avatar_listing">';

		// Show avatar circles.
		for ( $i = 0; $i < $shown_avatars; $i++ ) {
			$user_id = $users[ $i ]['user_id'];
			$avatar  = bp_core_fetch_avatar(
				array(
					'item_id' => $user_id,
					'object'  => 'user',
					'type'    => 'thumb',
					'html'    => true,
				)
			);
			?>
			<div class="rtm-tooltip">
				<?php echo wp_kses_post( $avatar ); ?>
				<span class="rtm-tooltiptext">
					<?php echo wp_kses_post( bp_core_get_userlink( $user_id ) ); ?>
				</span>
			</div>
			<?php
		}

		// Show remaining users count with tooltip.
		if ( $remaining_users > 0 ) {
			?>
			<div class="rtm-tooltip">
				<span class="round-fav-counter">+<?php echo esc_html( $remaining_users ); ?></span>
				<span class="rtm-tooltiptext">
					<ul class="wbtm-rest-member-list">
						<?php
						for ( $i = $shown_avatars; $i < ( $shown_avatars + $remaining_to_show ); $i++ ) {
							if ( isset( $users[ $i ] ) ) {
								$user_id = $users[ $i ]['user_id'];
								echo '<li>' . wp_kses_post( bp_core_get_userlink( $user_id ) ) . '</li>';
							}
						}

						$more_count = $remaining_users - $remaining_to_show;
						if ( $more_count > 0 ) {
							echo '<li>+' . esc_html( $more_count ) . ' ' . esc_html__( 'others', 'reign' ) . '</li>';
						}
						?>
					</ul>
				</span>
			</div>
			<?php
		}

		// Like count text.
		printf(
			'<span class="wbtm-likes-this">%s</span>',
			esc_html(
				sprintf(
					/* translators: %d: number of users who liked the activity. */
					_n( '%d like', '%d likes', $total_users, 'reign' ),
					$total_users
				)
			)
		);

		echo '</div>';
	}
}

/* Get User online */
if ( ! function_exists( 'reign_get_online_status' ) ) {

	/**
	 * Get the online status HTML for a specific user.
	 *
	 * @param int $user_id The ID of the user to check.
	 *
	 * @return string HTML for the user's online status.
	 */
	function reign_get_online_status( $user_id ) {
		// Check if BuddyPress function exists.
		if ( ! function_exists( 'bp_get_user_last_activity' ) ) {
			return '<span class="reign-status offline"></span>';
		}

		// Attempt to get the cached last activity.
		$cache_key     = 'reign_user_online_' . $user_id;
		$last_activity = wp_cache_get( $cache_key, 'reign_user_online' );

		if ( false === $last_activity ) {
			// Fetch from the database if not cached.
			$last_activity = strtotime( bp_get_user_last_activity( $user_id ) );
			wp_cache_set( $cache_key, $last_activity, 'reign_user_online', 5 * MINUTE_IN_SECONDS );
		}

		if ( empty( $last_activity ) ) {
			return '<span class="reign-status offline"></span>';
		}

		$current_time     = time();
		$diff             = $current_time - $last_activity;
		$online_timeframe = 5 * MINUTE_IN_SECONDS;
		$away_timeframe   = 30 * MINUTE_IN_SECONDS;

		if ( $diff <= $online_timeframe ) {
			return '<span class="reign-status online"></span>';
		} elseif ( $diff <= $away_timeframe ) {
			return '<span class="reign-status away"></span>';
		} else {
			return '<span class="reign-status offline"></span>';
		}
	}
}

if ( ! function_exists( 'reign_get_group_member_count' ) ) {

	/**
	 * Retrieve and optionally display the total member count for a BuddyPress group.
	 *
	 * This function fetches the total number of members in a specified BuddyPress group.
	 * It uses caching to improve performance by storing the member count for each group.
	 * If no group is provided, the function attempts to use the global group object.
	 *
	 * @param object|int|null $group Optional. The BuddyPress group object or group ID. Default null to use the global group object.
	 * @param bool            $echo  Optional. Whether to echo the member count or return it. Default true.
	 * @return int|string     The total member count if $echo is false, otherwise outputs the count wrapped in a <span> element.
	 */
	function reign_get_group_member_count( $group = null, $echo = true ) {
		// Use global group template if no group object is passed.
		if ( ! $group ) {
			global $groups_template;
			if ( isset( $groups_template->group ) ) {
				$group = $groups_template->group;
			} else {
				return 0; // Return 0 if no group is passed and global is not set.
			}
		}

		// If group is passed as an ID, retrieve the group object.
		if ( is_numeric( $group ) ) {
			$group = groups_get_group( $group );
		}

		if ( isset( $group->id ) ) {
			// Generate a cache key based on the group ID.
			$cache_key    = 'reign_group_member_count_' . $group->id;
			$member_count = wp_cache_get( $cache_key, 'buddypress' );

			if ( false === $member_count ) {
				$member_count = (int) $group->total_member_count;
				wp_cache_set( $cache_key, $member_count, 'buddypress' );
			}

			if ( $echo ) {
				echo '<span class="group-count">' . esc_html( $member_count ) . '</span>';
			} else {
				return $member_count;
			}
		}

		return 0; // Return 0 if group ID or object is not valid.
	}
}

if ( ! function_exists( 'reign_bp_get_group_type' ) ) {

	/**
	 * Retrieve and display the type of a BuddyPress group.
	 *
	 * This function determines the group type (e.g., Public, Hidden, Private)
	 * based on the group's status and caches the result for improved performance.
	 * It returns the group type wrapped in a `<span>` element with a class
	 * corresponding to the group's status.
	 *
	 * @param object|bool $group Optional. The BuddyPress group object. Default false to use the global group object.
	 * @return string The HTML span element containing the group type.
	 */
	function reign_bp_get_group_type( $group = false ) {
		global $groups_template;

		// Use the group from the template if not provided.
		if ( empty( $group ) ) {
			$group = & $groups_template->group;
		}

		// Generate a unique cache key for this group.
		$cache_key = 'reign_group_type_' . $group->id;
		$type      = wp_cache_get( $cache_key, 'buddypress' );

		if ( false === $type ) {
			// Determine the group type based on the status.
			if ( 'public' === $group->status ) {
				$type = __( 'Public', 'reign' );
			} elseif ( 'hidden' === $group->status ) {
				$type = __( 'Hidden', 'reign' );
			} elseif ( 'private' === $group->status ) {
				$type = __( 'Private', 'reign' );
			} else {
				$type = ucwords( $group->status ) . ' ' . __( 'Group', 'reign' );
			}

			// Store the type in cache.
			wp_cache_set( $cache_key, $type, 'buddypress' );
		}

		return '<span class="group-type ' . esc_attr( $group->status ) . '">' . esc_html( $type ) . '</span>';
	}
}

if ( ! function_exists( 'reign_bp_group_list_admins' ) ) {

	/**
	 * Display or return a list of admins for a BuddyPress group.
	 *
	 * This function retrieves and optionally displays a list of administrators for a specified BuddyPress group.
	 * If no group is provided, it attempts to use the global group object. If a group ID is provided, it fetches
	 * the group object corresponding to the ID. The function caches the generated admin list HTML to improve performance.
	 *
	 * @param object|int|bool $group Optional. The BuddyPress group object or group ID. Default false to use the global group object.
	 * @param bool            $echo  Optional. Whether to echo the list of admins or return it. Default true.
	 * @return string|void    The HTML list of group admins if $echo is false. Otherwise, outputs the HTML.
	 */
	function reign_bp_group_list_admins( $group = false, $echo = true ) {
		// Use global group template if no group object is passed.
		if ( ! $group ) {
			global $groups_template;
			if ( isset( $groups_template->group ) ) {
				$group = $groups_template->group;
			} else {
				if ( $echo ) {
					echo '<span class="activity">' . esc_html__( 'No Admins', 'reign' ) . '</span>';
				}
				return;
			}
		}

		// If group is passed as an ID, retrieve the group object.
		if ( is_numeric( $group ) ) {
			$group = groups_get_group( $group );
		}

		if ( ! empty( $group->admins ) ) {
			// Cache key for admins list.
			$cache_key       = 'reign_group_admins_' . $group->id;
			$admin_list_html = wp_cache_get( $cache_key, 'buddypress' );

			if ( false === $admin_list_html ) {
				ob_start();

				?>
				<ul id="group-admins">
					<?php foreach ( (array) $group->admins as $admin ) : ?>
						<li class="group-admin-item">
							<?php
							// Determine the user URL based on the BuddyPress version.
							$user_url = function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' )
								? bp_members_get_user_url( $admin->user_id )
								: bp_core_get_user_domain( $admin->user_id, $admin->user_nicename, $admin->user_login );
							?>
							<a href="<?php echo esc_url( $user_url ); ?>">
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput
								echo bp_core_fetch_avatar(
									array(
										'item_id' => $admin->user_id,
										/* translators: %s: member display name. */
										'alt'     => sprintf( __( 'Profile picture of %s', 'reign' ), bp_core_get_user_displayname( $admin->user_id ) ),
									)
								);
								?>
							</a>
							<div class="group-admin-meta">
								<span class="group-by"><?php esc_html_e( 'Created by', 'reign' ); ?></span>
								<span class="admin-name">
									<a href="<?php echo esc_url( $user_url ); ?>">
										<?php echo esc_html( bp_core_get_user_displayname( $admin->user_id ) ); ?>
									</a>
								</span>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php

				$admin_list_html = ob_get_clean();
				wp_cache_set( $cache_key, $admin_list_html, 'buddypress' );
			}

			if ( $echo ) {
				// $admin_list_html is assembled above from escaped values inside an output buffer.
				echo $admin_list_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped buffered HTML.
			} else {
				return $admin_list_html;
			}
		} elseif ( $echo ) {
				echo '<span class="activity">' . esc_html__( 'No Admins', 'reign' ) . '</span>';
		} else {
			return '<span class="activity">' . esc_html__( 'No Admins', 'reign' ) . '</span>';
		}
	}
}

if ( ! function_exists( 'reign_group_index_widgets' ) ) {

	function reign_group_index_widgets() {
		get_template_part( 'template-parts/groups-widgets' );
	}

	add_action( 'reign_begin_group_index_sidebar', 'reign_group_index_widgets' );
}

if ( ! function_exists( 'reign_member_index_widgets' ) ) {

	function reign_member_index_widgets() {
		get_template_part( 'template-parts/members-widgets' );
	}

	add_action( 'reign_begin_member_index_sidebar', 'reign_member_index_widgets' );
}

/*
 * Hide bp dir vertical nav related class on activity page
 */
add_filter( 'bp_nouveau_get_container_classes', 'reign_bp_nouveau_get_container_classes', 99, 2 );
function reign_bp_nouveau_get_container_classes( $class, $classes ) {
	$component = bp_current_component();

	if ( 'activity' === $component && bp_is_directory() && ! bp_is_user() && ! bp_is_group() ) {
		$class  = str_replace( 'bp-dir-vert-nav', '', $class );
		$class  = str_replace( 'bp-vertical-navs', '', $class );
		$class .= ' bp-dir-hori-nav ';
	}
	return $class;
}

/**
 * Function to filter activity content to format some types of activity
 *
 * @var void
 */
add_filter( 'bp_activity_entry_content', 'reign_filter_activity_content', 9 );
add_action( 'bp_activity_embed_after_media', 'reign_filter_activity_content' );
function reign_filter_activity_content() {
	if ( ! bp_activity_has_content() ) {
		global $activities_template;

		$activity_id   = $activities_template->activity->id;
		$activity_type = $activities_template->activity->type;

		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) && 'new_avatar' === $activity_type ) {
			return;
		}

		if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '10.0.0', '>=' ) && ! isset( buddypress()->buddyboss ) && 'new_group_avatar' !== $activity_type && 'new_group_cover_photo' !== $activity_type && 'new_cover_photo' !== $activity_type ) {
			return;
		}

		switch ( $activity_type ) {
			case 'joined_group':
				reign_joined_group_activity_content( $activities_template->activity );
				break;
			case 'friendship_created':
				reign_friendship_created_activity_content( $activities_template->activity );
				break;
			case 'new_group_avatar':
			case 'new_group_cover_photo':
				reign_new_group_avatar_activity_content( $activities_template->activity );
				break;
			case 'new_avatar':
			case 'new_cover_photo':
				reign_new_avatar_activity_content( $activities_template->activity );
				break;
			default:
				break;
		}
	}
}

/**
 * Format join group activity.
 *
 * @param  object $activity
 *
 * @return void
 */
function reign_joined_group_activity_content( $activity ) {
	$args = array(
		'activity_id' => $activity->id,
		'user_id'     => $activity->user_id,
		'item_id'     => $activity->item_id,
		'component'   => $activity->component,
		'type'        => $activity->type,
	);

	if ( ! class_exists( 'Youzify' ) ) {
		get_template_part( 'template-parts/content', 'user-preview', $args );
	}
}

/**
 * Format friendship created activity
 *
 * @param  object $activity
 *
 * @return void
 */
function reign_friendship_created_activity_content( $activity ) {

	$args = array(
		'activity_id'       => $activity->id,
		'user_id'           => $activity->user_id,
		'item_id'           => $activity->item_id,
		'secondary_item_id' => $activity->secondary_item_id,
		'component'         => $activity->component,
		'type'              => $activity->type,
	);

	if ( ! class_exists( 'Youzify' ) ) {
		get_template_part( 'template-parts/content', 'user-preview', $args );
	}
}

function reign_new_avatar_activity_content( $activity ) {

	$args = array(
		'activity_id'       => $activity->id,
		'user_id'           => $activity->user_id,
		'item_id'           => $activity->item_id,
		'secondary_item_id' => $activity->secondary_item_id,
		'component'         => $activity->component,
		'type'              => $activity->type,
	);

	if ( ! class_exists( 'Youzify' ) ) {
		get_template_part( 'template-parts/content', 'user-preview', $args );
	}
}

function reign_new_group_avatar_activity_content( $activity ) {
	$args = array(
		'activity_id'       => $activity->id,
		'user_id'           => $activity->user_id,
		'item_id'           => $activity->item_id,
		'secondary_item_id' => $activity->secondary_item_id,
		'component'         => $activity->component,
		'type'              => $activity->type,
	);

	if ( ! class_exists( 'Youzify' ) ) {
		get_template_part( 'template-parts/content', 'user-preview', $args );
	}
}

/**
 * Registers custom BuddyPress activity actions for member and group activities.
 *
 * This function adds custom activity actions for member cover photo changes and group avatar/cover photo changes,
 * based on the settings configured in the Reign theme or plugin.
 *
 * @global array $wbtm_reign_settings Global settings array for Reign BuddyExtender.
 */
function reign_register_activity_actions() {
	// Ensure that $wbtm_reign_settings is available globally.
	global $wbtm_reign_settings;

	// Check if the member cover image feature is enabled.
	if ( isset( $wbtm_reign_settings['reign_buddyextender']['member_cover_image'] ) && $wbtm_reign_settings['reign_buddyextender']['member_cover_image'] ) {
		bp_activity_set_action(
			buddypress()->members->id,
			'new_cover_photo',
			__( 'Member updated cover photo', 'reign' ),
			'bp_members_format_activity_action_new_cover_photo',
			__( 'Updated Cover Photo', 'reign' )
		);
	}

	// Check if the groups component is active before registering group actions.
	if ( bp_is_active( 'groups' ) ) {

		// Check if the group avatar feature is enabled.
		if ( isset( $wbtm_reign_settings['reign_buddyextender']['group_image'] ) && $wbtm_reign_settings['reign_buddyextender']['group_image'] ) {
			bp_activity_set_action(
				buddypress()->groups->id,
				'new_group_avatar',
				__( 'Member updated group picture', 'reign' ),
				'bp_groups_format_activity_action_new_group_avatar',
				__( 'Updated Group Photos', 'reign' )
			);
		}

		// Check if the group cover image feature is enabled.
		if ( isset( $wbtm_reign_settings['reign_buddyextender']['group_cover_image'] ) && $wbtm_reign_settings['reign_buddyextender']['group_cover_image'] ) {
			bp_activity_set_action(
				buddypress()->groups->id,
				'new_group_cover_photo',
				__( 'Member updated group cover photo', 'reign' ),
				'bp_groups_format_activity_action_new_group_cover_photo',
				__( 'Updated Group Cover Photos', 'reign' )
			);
		}
	}
}
add_action( 'bp_register_activity_actions', 'reign_register_activity_actions' );

/**
 * Format 'new_cover_photo' activity actions.
 *
 * @since 5.8.4
 *
 * @param string $action   Static activity action.
 * @param object $activity Activity object.
 * @return string
 */
// `bp_members_format_activity_action_new_cover_photo` matches BuddyPress's
// own activity-action-formatter naming. function_exists() guard so a
// future BP version (or BuddyBoss) that adds the same symbol coexists.
if ( ! function_exists( 'bp_members_format_activity_action_new_cover_photo' ) ) :
	function bp_members_format_activity_action_new_cover_photo( $action, $activity ) {
		$userlink = bp_core_get_userlink( $activity->user_id );

		/* translators: %s: user link */
		$action = sprintf( esc_html__( '%s updated their cover photo', 'reign' ), $userlink );

		// Legacy filter - pass $user_id instead of $activity.
		if ( has_filter( 'bp_xprofile_new_avatar_action' ) ) {
			$action = apply_filters( 'bp_xprofile_new_avatar_action', $action, $activity->user_id );
		}

		return apply_filters( 'bp_members_format_activity_action_new_cover_photo', $action, $activity );
	}
endif;


/**
 * Format 'new_group_avatar' activity actions.
 *
 * @since 5.8.4
 *
 * @param string $action   Static activity action.
 * @param object $activity Activity object.
 * @return string
 */


// Same BP-namespace collision risk as the cover-photo formatter above.
if ( ! function_exists( 'bp_groups_format_activity_action_new_group_avatar' ) ) :
	function bp_groups_format_activity_action_new_group_avatar( $action, $activity ) {
		$userlink = bp_core_get_userlink( $activity->user_id );

		$group = groups_get_group( array( 'group_id' => $activity->item_id ) );
		if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
			$group_link = bp_get_group_url( $group );
		} else {
			$group_link = bp_get_group_permalink( $group );
		}
		$grouplink = '<a href="' . esc_url( $group_link ) . '">' . $group->name . '</a>';

		/* translators: 1: user link, 2: group link. */
		$action = sprintf( esc_html__( '%1$s updated %2$s group photo', 'reign' ), $userlink, $grouplink );

		return apply_filters( 'bp_groups_format_activity_action_new_group_avatar', $action, $activity );
	}
endif;


/**
 * Format 'new_group_cover_photo' activity actions.
 *
 * @since 5.8.4
 *
 * @param string $action   Static activity action.
 * @param object $activity Activity object.
 * @return string
 */
if ( ! function_exists( 'bp_groups_format_activity_action_new_group_cover_photo' ) ) :
	function bp_groups_format_activity_action_new_group_cover_photo( $action, $activity ) {
		$userlink = bp_core_get_userlink( $activity->user_id );

		$group = groups_get_group( array( 'group_id' => $activity->item_id ) );
		if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
			$group_link = bp_get_group_url( $group );
		} else {
			$group_link = bp_get_group_permalink( $group );
		}
		$grouplink = '<a href="' . esc_url( $group_link ) . '">' . $group->name . '</a>';

		/* translators: 1: user link, 2: group link. */
		$action = sprintf( esc_html__( '%1$s changed %2$s group cover photo', 'reign' ), $userlink, $grouplink );

		return apply_filters( 'bp_groups_format_activity_action_new_group_cover_photo', $action, $activity );
	}
endif;

/**
 * Handles the event of a member uploading a new cover image and creates an activity entry in BuddyPress.
 *
 * This function is triggered when a member uploads a new cover image. It checks if the feature is enabled
 * and if the BuddyPress activity component is active. The function downloads the cover image, saves it to a
 * specified path, and adds a new activity entry with the cover image details to the activity stream.
 *
 * @global array $wbtm_reign_settings Global settings array for Reign BuddyExtender.
 *
 * @param int    $item_id       The ID of the item (usually the member ID) associated with the cover image upload.
 * @param string $name          The original name of the uploaded cover image.
 * @param string $cover_url     The URL where the uploaded cover image is temporarily stored.
 * @param int    $feedback_code Feedback code indicating the status of the cover image upload (not used in this function).
 *
 * @return bool False if the cover image upload process fails at any stage, otherwise nothing.
 */
function reign_members_cover_image_uploaded( $item_id, $name, $cover_url, $feedback_code ) {
	global $wbtm_reign_settings;

	// Bail if the feature is not enabled or if the activity component is not active.
	if ( ! isset( $wbtm_reign_settings['reign_buddyextender']['member_cover_image'] ) || ! bp_is_active( 'activity' ) ) {
		return false;
	}

	$user_id = bp_displayed_user_id();

	// Generate a unique filename for the cover image.
	$file_extension = pathinfo( $cover_url, PATHINFO_EXTENSION );
	$date           = gmdate( 'Y-m-d' );
	$unique_id      = uniqid();
	$file_name      = "cover-image-{$date}-{$unique_id}.{$file_extension}";

	// Define the upload path for the cover image.
	$upload_dir_array = bp_attachments_cover_image_upload_dir(
		array(
			'object_dir' => 'members',
			'item_id'    => $user_id,
		)
	);
	$upload_dir       = isset( $upload_dir_array['path'] ) ? $upload_dir_array['path'] : $upload_dir_array['basedir'] . '/' . $user_id . '/cover-image';

	// Ensure the directory exists.
	if ( ! file_exists( $upload_dir ) ) {
		wp_mkdir_p( $upload_dir );
	}

	// Set the full file path for the cover image.
	$file_path = $upload_dir . '/' . $file_name;

	// Download the cover image from the provided URL and save it to the specified path.
	$image_data = wp_remote_get( $cover_url, array( 'timeout' => 15 ) );

	if ( is_wp_error( $image_data ) ) {
		return false; // Return if the download failed.
	}

	$image_data = wp_remote_retrieve_body( $image_data );

	if ( empty( $image_data ) ) {
		return false; // Return if the image data is empty.
	}

	// Save the image to the file path.
	if ( file_put_contents( $file_path, $image_data ) === false ) {
		return false; // Return if the file could not be written.
	}

	// Generate the file URL.
	$file_url = str_replace( wp_upload_dir()['basedir'], wp_upload_dir()['baseurl'], $upload_dir ) . '/' . $file_name;

	// Add the activity stream item for the new cover image.
	$activity_id = bp_activity_add(
		array(
			'user_id'           => $user_id,
			'component'         => buddypress()->members->id,
			'type'              => 'new_cover_photo',
			'item_id'           => $item_id,
			'secondary_item_id' => $item_id,
		)
	);

	// Update the activity meta with the cover image URL.
	bp_activity_update_meta( $activity_id, 'cover_image', $file_url );
	bp_activity_update_meta( $activity_id, 'cover_image_name', $name );

	// Optionally, you can delete the temporary image from the original URL to save space.
	// If you choose to do so, add appropriate checks and error handling.
}

// Add action hook for BuddyPress or BuddyBoss to handle member cover image uploads.
if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) {
	add_action( 'members_cover_image_uploaded', 'reign_members_cover_image_uploaded', 10, 4 );
}

if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
	add_action( 'xprofile_cover_image_uploaded', 'reign_members_cover_image_uploaded', 10, 4 );
}

/**
 * Adds an activity stream item when a user has uploaded a new group cover image.
 *
 * @since 5.8.4
 */
function reign_groups_cover_image_uploaded( $item_id, $name, $cover_url, $feedback_code ) {
	// Bail if activity component is not active.

	global $wbtm_reign_settings;

	if ( ! isset( $wbtm_reign_settings['reign_buddyextender']['group_cover_image'] ) ) {
		return false;
	}

	if ( ! bp_is_active( 'activity' ) ) {
		return false;
	}

	// Get group details.
	$group = groups_get_group( $item_id );

	// Allow filtering of group types to exclude from activity.
	$excluded_group_types = apply_filters( 'reign_excluded_group_types_for_cover_activity', array( 'hidden' ), $group );

	if ( ! $group || in_array( $group->status, $excluded_group_types, true ) ) {
		// Do not generate activity for excluded group types.
		return false;
	}

	if ( empty( $user_id ) ) {
		$user_id = bp_displayed_user_id();
	}

	$user_id = get_current_user_id();

	// Add the activity.
	$activity_id = bp_activity_add(
		array(
			'user_id'   => $user_id,
			'component' => buddypress()->groups->id,
			'type'      => 'new_group_cover_photo',
			'item_id'   => $item_id,
		)
	);

	$type                = pathinfo( $cover_url, PATHINFO_EXTENSION );
	$data                = wp_remote_get( $cover_url, array( 'timeout' => 15 ) );
	$data                = wp_remote_retrieve_body( $data );
	$avatar_image_base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );
	bp_activity_update_meta( $activity_id, 'group_cover_image', $avatar_image_base64 );
	bp_activity_update_meta( $activity_id, 'group_cover_image_name', $name );

	/*
	Upload Profile Cover Image into WP Media Library */
	/*
	$title = basename( $cover_url );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	$cover_image_url = media_sideload_image( $cover_url, 0, $title, 'src' );

	bp_activity_update_meta( $activity_id, 'group_cover_image', $cover_image_url );
	bp_activity_update_meta( $activity_id, 'group_cover_image_name', $name );
	*/
}
add_action( 'groups_cover_image_uploaded', 'reign_groups_cover_image_uploaded', 10, 4 );

/**
 * Adds an activity stream item when a user has uploaded a new group avatar.
 *
 * @since 5.8.4
 */
function reign_groups_avatar_uploaded( $item_id, $type ) {

	$bmpro_group_photo_stngs = get_option( 'bmpro_group_photo_stngs' );
	if ( isset( $bmpro_group_photo_stngs['group_photo_mod_on'] ) && 'yes' === $bmpro_group_photo_stngs['group_photo_mod_on'] ) {
		return false;
	}

	global $wbtm_reign_settings;

	if ( ! isset( $wbtm_reign_settings['reign_buddyextender']['group_image'] ) ) {
		return false;
	}
	// Bail if activity component is not active.
	if ( ! bp_is_active( 'activity' ) ) {
		return false;
	}

	// Get group details.
	$group = groups_get_group( $item_id );

	// Allow filtering of group types to exclude from activity.
	$excluded_group_types = apply_filters( 'reign_excluded_group_types_for_avatar_activity', array( 'hidden' ), $group );

	if ( ! $group || in_array( $group->status, $excluded_group_types, true ) ) {
		// Do not generate activity for excluded group types.
		return false;
	}

	$user_id = get_current_user_id();

	// Add the activity.
	$activity_id = bp_activity_add(
		array(
			'user_id'   => $user_id,
			'component' => buddypress()->groups->id,
			'type'      => 'new_group_avatar',
			'item_id'   => $item_id,
		)
	);

	$avatar_url          = bp_core_fetch_avatar(
		array(
			'item_id'    => $item_id,
			'type'       => 'full',
			'avatar_dir' => 'group-avatars',
			'object'     => 'group',
			'width'      => 400,
			'height'     => 400,
			'html'       => false,
		)
	);
	$title               = basename( $avatar_url );
	$type                = pathinfo( $avatar_url, PATHINFO_EXTENSION );
	$data                = wp_remote_get( $avatar_url, array( 'timeout' => 15 ) );
	$data                = wp_remote_retrieve_body( $data );
	$avatar_image_base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );
	bp_activity_update_meta( $activity_id, 'group_avatar_image', $avatar_image_base64 );
	bp_activity_update_meta( $activity_id, 'group_avatar_image_name', $title );
}
add_action( 'groups_avatar_uploaded', 'reign_groups_avatar_uploaded', 20, 2 );



add_filter( 'bp_get_activity_action_pre_meta', 'reign_group_activity_secondary_avatars', 20, 2 );
function reign_group_activity_secondary_avatars( $action, $activity ) {

	if ( 'new_group_avatar' === $activity->type || 'new_group_cover_photo' === $activity->type ) {
		switch ( $activity->component ) {
			case 'groups':
				global $activities_template;
				$action = $activities_template->activity->action;
				break;
		}
	}
	return $action;
}

/**
 * Handle the addition of a new avatar in BuddyPress activities.
 *
 * This function intercepts the 'new_avatar' activity type for members or profiles,
 * fetches the current avatar URL, and saves it to a custom path within the
 * WordPress uploads directory. It then updates the activity metadata with the
 * new avatar URL and file name.
 *
 * @param array $args Arguments passed to the activity update.
 * @param int   $activity_id The ID of the activity being updated.
 */
function reign_bp_avatar_activity_add( $args, $activity_id ) {
	$bmpro_profile_avatar_stngs = get_option( 'bmpro_profile_avatar_stngs' );
	if ( isset( $bmpro_profile_avatar_stngs['profile_avatar_mod_on'] ) && 'yes' === $bmpro_profile_avatar_stngs['profile_avatar_mod_on'] ) {
		return;
	}

	if ( 'new_avatar' === $args['type'] && ( 'members' === $args['component'] || 'profile' === $args['component'] ) ) {

		// Fetch the current avatar URL.
		$avatar_url = bp_core_fetch_avatar(
			array(
				'item_id' => $args['user_id'],
				'type'    => 'full',
				'width'   => 400,
				'height'  => 400,
				'html'    => false,
			)
		);

		// Generate a unique identifier for the avatar file.
		$date           = gmdate( 'm-d' );
		$unique_id      = uniqid();
		$file_extension = pathinfo( $avatar_url, PATHINFO_EXTENSION );
		$file_name      = "avatar-{$date}-{$unique_id}.{$file_extension}";

		// Set the base path using the existing BuddyPress avatar directory.
		$upload_dir = wp_upload_dir();
		$base_path  = $upload_dir['basedir'] . '/avatars/' . $args['user_id'];

		// Ensure the user-specific directory exists.
		if ( ! file_exists( $base_path ) ) {
			wp_mkdir_p( $base_path );
		}

		// Full path to save the avatar.
		$file_path = "{$base_path}/{$file_name}";

		// Fetch the avatar image data.
		$image_data = wp_remote_get( $avatar_url, array( 'timeout' => 15 ) );
		$image_data = wp_remote_retrieve_body( $image_data );

		// Save the avatar image to the custom path.
		file_put_contents( $file_path, $image_data );

		// Generate the URL for the saved avatar.
		$avatar_url = $upload_dir['baseurl'] . '/avatars/' . $args['user_id'] . '/' . $file_name;

		// Update activity meta with the new avatar URL.
		bp_activity_update_meta( $activity_id, 'member_avatar_image_url', $avatar_url );
		bp_activity_update_meta( $activity_id, 'member_avatar_image_name', $file_name );
	}
}

if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '10.0.0', '<' ) && ! isset( buddypress()->buddyboss ) ) {
	add_action( 'bp_activity_add', 'reign_bp_avatar_activity_add', 20, 2 );
}

/**
 * Function will add feature image for blog post in the activity feed content.
 *
 * @param string $content
 * @param int    $blog_post_id
 *
 * @return string $content
 *
 * @since 3.4.0
 */
function reign_add_feature_image_blog_post_as_activity_content_callback( $content, $blog_post_id ) {
	if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) {
		if ( ! empty( $blog_post_id ) && ! empty( get_post_thumbnail_id( $blog_post_id ) ) ) {
			$content .= sprintf( ' <a class="reign-post-img-link" href="%s"><img src="%s" alt="%s" /></a>', esc_url( get_permalink( $blog_post_id ) ), esc_url( wp_get_attachment_image_url( get_post_thumbnail_id( $blog_post_id ), 'full' ) ), esc_attr( get_the_title( $blog_post_id ) ) );
		}
	}

	return $content;
}

add_filter( 'reign_add_feature_image_blog_post_as_activity_content', 'reign_add_feature_image_blog_post_as_activity_content_callback', 10, 2 );

add_action( 'bp_before_activity_activity_content', 'reign_bp_blogs_activity_content_set_temp_content' );

/**
 * Function which set the temporary content on the blog post activity.
 *
 * @since 3.4.0
 */
function reign_bp_blogs_activity_content_set_temp_content() {

	if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) {

		global $activities_template;

		$activity = $activities_template->activity;
		if ( ( 'blogs' === $activity->component ) && isset( $activity->secondary_item_id ) && 'new_blog_' . get_post_type( $activity->secondary_item_id ) === $activity->type ) {
			$content = get_post( $activity->secondary_item_id );
			// If we converted $content to an object earlier, flip it back to a string.
			if ( is_a( $content, 'WP_Post' ) ) {
				$activities_template->activity->content = '&#8203;';
			}
		} elseif ( 'blogs' === $activity->component && 'new_blog_comment' === $activity->type && $activity->secondary_item_id && $activity->secondary_item_id > 0 ) {
			$activities_template->activity->content = '&#8203;';
		}
	}
}

add_filter( 'bp_get_activity_content_body', 'reign_bp_blogs_activity_content_with_read_more', 9999, 2 );

/**
 * Function which set the content on activity blog post.
 *
 * @param $content
 * @param $activity
 *
 * @return string
 *
 * @since 3.4.0
 */
function reign_bp_blogs_activity_content_with_read_more( $content, $activity = null ) {

	if ( ! is_object( $activity ) || empty( $activity->id ) ) {
		return $content;
	}

	if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) {

		if ( ( 'blogs' === $activity->component ) && isset( $activity->secondary_item_id ) && 'new_blog_' . get_post_type( $activity->secondary_item_id ) === $activity->type ) {
			$blog_post = get_post( $activity->secondary_item_id );
			// If we converted $content to an object earlier, flip it back to a string.
			if ( is_a( $blog_post, 'WP_Post' ) ) {
				$content_img = apply_filters( 'reign_add_feature_image_blog_post_as_activity_content', '', $blog_post->ID );
				$post_title  = sprintf( '<a class="reign-post-title-link" href="%s"><span class="reign-post-title">%s</span></a>', esc_url( get_permalink( $blog_post->ID ) ), esc_html( $blog_post->post_title ) );
				$content     = bp_create_excerpt( bp_strip_script_and_style_tags( html_entity_decode( get_the_excerpt( $blog_post->ID ) ) ) );
				if ( false !== strrpos( $content, __( '&hellip;', 'reign' ) ) ) {
					$content = str_replace( ' [&hellip;]', '&hellip;', $content );
					$content = apply_filters_ref_array( 'bp_get_activity_content', array( $content, $activity ) );
					preg_match( '/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $content, $matches );
					if ( isset( $matches ) && array_key_exists( 0, $matches ) && ! empty( $matches[0] ) ) {
						$iframe  = $matches[0];
						$content = strip_tags( preg_replace( '/<iframe.*?\/iframe>/i', '', $content ), '<a>' );

						$content .= $iframe;
					}
					$content = sprintf( '%1$s <div class="reign-content-wrp">%2$s %3$s</div>', $content_img, $post_title, wpautop( $content ) );
				} else {
					$content = apply_filters_ref_array( 'bp_get_activity_content', array( $content, $activity ) );
					$content = strip_tags( $content, '<a><iframe><img><span><div>' );
					preg_match( '/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $content, $matches );
					if ( isset( $matches ) && array_key_exists( 0, $matches ) && ! empty( $matches[0] ) ) {
						$content = $content;
					}
					$content = sprintf( '%1$s <div class="reign-content-wrp">%2$s %3$s</div>', $content_img, $post_title, wpautop( $content ) );
				}
			}
		} elseif ( 'blogs' === $activity->component && 'new_blog_comment' === $activity->type && $activity->secondary_item_id && $activity->secondary_item_id > 0 ) {
			$comment = get_comment( $activity->secondary_item_id );
			$content = bp_create_excerpt( html_entity_decode( $comment->comment_content ) );
			if ( false !== strrpos( $content, __( '&hellip;', 'reign' ) ) ) {
				$content     = str_replace( ' [&hellip;]', '&hellip;', $content );
				$append_text = apply_filters( 'bp_activity_excerpt_append_text', __( ' Read more', 'reign' ) );
				$content     = wpautop( sprintf( '%1$s<span class="activity-blog-post-link"><a href="%2$s" rel="nofollow">%3$s</a></span>', $content, get_comment_link( $activity->secondary_item_id ), $append_text ) );
			}
		}
	}

	return $content;
}

/**
 * Added buddypress activity types activity embed css.
 *
 * @param $content
 * @param $activity
 *
 * @return string
 *
 * @since 6.0.0
 */
function reign_activity_embed_add_inline_styles() {
	?>
	<style type="text/css">
			.reign-user-preview {
				position: relative;
				margin-top: 28px;
				margin-bottom: 15px;
			}

			.reign-user-preview .reign-user-preview-cover img {
				width: 100%;
				height: 132px;
				object-fit: cover;
			}

			.reign-preview-widget .user-short-description {
				padding-top: 44px;
			}

			.reign-short-description {
				position: relative;
				padding-top: 62px;
			}

			.reign-user-avatar-content {
				margin-top: -75px;
				margin-bottom: 15px;
			}

			.reign-user-short-description {
				text-align: center;
			}

			.reign-user-avatar-content img {
				background: rgba(218, 218, 218, 0.5);
				padding: 5px;
			}

			@media(min-width: 544px) {
				.reign-user-stats {
					position: absolute;
					left: 0;
					bottom: 0;
				}
			}

			@media(max-width: 543px) {
				.reign-user-stats {
					text-align: center;
					margin-top: 10px;
				}
			}

			.bp-nouveau .activity-list .activity-item .activity-content .activity-inner .reign-user-stats .reign-user-stat-title,
			.bp-nouveau .activity-list .activity-item .activity-content .activity-inner .reign-user-stats .reign-user-stat-text {
				font-size: 12px;
				line-height: normal;
			}

			.bp-nouveau .activity-list .activity-item .activity-content .activity-inner .reign-user-stats .reign-user-stat-text {
				text-transform: uppercase;
				margin-top: 5px;
			}

			#buddypress .activity-content .activity-inner .reign-user-short-description a {
				color: inherit;
				background: transparent;
				font-size: inherit;
			}

			@media(min-width: 544px) {
				.bp-profile-button {
					text-align: right;
					margin-top: -24px;
				}
				.bp-profile-button a {
					padding: 0 !important;
				}
			}
	</style>
	<?php
}

add_action( 'embed_head', 'reign_activity_embed_add_inline_styles', 20 );

// `bp_nouveau_*` is the BuddyPress Nouveau template-function namespace.
// BuddyBoss Platform / other Nouveau-based plugins may define the same.
if ( ! function_exists( 'bp_nouveau_reign_signup_terms_privacy' ) ) :
	function bp_nouveau_reign_signup_terms_privacy() {
		$page_ids             = bp_core_get_directory_page_ids();
		$show_legal_agreement = bb_register_legal_agreement();

		$terms   = isset( $page_ids['terms'] ) ? $page_ids['terms'] : false;
		$privacy = isset( $page_ids['privacy'] ) ? $page_ids['privacy'] : (int) get_option( 'wp_page_for_privacy_policy' );

		// Do not show the page if page is not published.
		if ( false !== $terms && 'publish' !== get_post_status( $terms ) ) {
			$terms = false;
		}

		// Do not show the page if page is not published.
		if ( false !== $privacy && 'publish' !== get_post_status( $privacy ) ) {
			$privacy = false;
		}

		if ( ! $terms && ! $privacy ) {
			return false;
		}

		if ( ! empty( $terms ) && ! empty( $privacy ) ) {
			$terms_link   = '<a class="popup-modal-register popup-terms" href="' . esc_url( get_permalink( $terms ) ) . '" target="_blank">' . esc_html( get_the_title( $terms ) ) . '</a>';
			$privacy_link = '<a class="popup-modal-register popup-privacy" href="' . esc_url( get_permalink( $privacy ) ) . '" target="_blank">' . esc_html( get_the_title( $privacy ) ) . '</a>';
			?>
			<?php if ( $show_legal_agreement ) { ?>
			<div class="input-options checkbox-options">
				<div class="bp-checkbox-wrap">
					<input type="checkbox" name="legal_agreement" id="legal_agreement" value="1" class="bs-styled-checkbox">
					<label for="legal_agreement" class="option-label">
						<?php
						/* translators: 1: link to the terms of service page, 2: link to the privacy policy page. */
						echo wp_kses_post( sprintf( __( 'I agree to the %1$s and %2$s.', 'reign' ), $terms_link, $privacy_link ) );
						?>
					</label>
				</div>
			</div>
		<?php } else { ?>
			<p class="register-privacy-info">
				<?php
					/* translators: 1: link to the terms of service page, 2: link to the privacy policy page. */
					echo wp_kses_post( sprintf( __( 'By creating an account, you agree to the %1$s and %2$s.', 'reign' ), $terms_link, $privacy_link ) );
				?>
			</p>
				<?php
		}
		} elseif ( empty( $terms ) && ! empty( $privacy ) ) {
			$privacy_link = '<a class="popup-modal-register popup-privacy" href="' . esc_url( get_permalink( $privacy ) ) . '" target="_blank">' . esc_html( get_the_title( $privacy ) ) . '</a>';
			?>
			<?php if ( $show_legal_agreement ) { ?>
			<div class="input-options checkbox-options">
				<div class="bp-checkbox-wrap">
					<input type="checkbox" name="legal_agreement" id="legal_agreement" value="1" class="bs-styled-checkbox">
					<label for="legal_agreement" class="option-label">
					<?php
						/* translators: %s: link to the privacy policy page. */
						echo wp_kses_post( sprintf( __( 'I agree to the %s.', 'reign' ), $privacy_link ) );
					?>
						</label>
				</div>
			</div>
		<?php } else { ?>
			<p class="register-privacy-info">
				<?php
					/* translators: %s: link to the privacy policy page. */
					echo wp_kses_post( sprintf( __( 'By creating an account, you agree to the %s.', 'reign' ), $privacy_link ) );
				?>
			</p>
				<?php
		}
		} elseif ( ! empty( $terms ) && empty( $privacy ) ) {
			$terms_link = '<a class="popup-modal-register popup-terms" href="' . esc_url( get_permalink( $terms ) ) . '" target="_blank">' . esc_html( get_the_title( $terms ) ) . '</a>';
			?>
			<?php if ( $show_legal_agreement ) { ?>
			<div class="input-options checkbox-options">
				<div class="bp-checkbox-wrap">
					<input type="checkbox" name="legal_agreement" id="legal_agreement" value="1" class="bs-styled-checkbox">
					<label for="legal_agreement" class="option-label">
					<?php
						/* translators: %s: link to the terms of service page. */
						echo wp_kses_post( sprintf( __( 'I agree to the %s.', 'reign' ), $terms_link ) );
					?>
						</label>
				</div>
			</div>
		<?php } else { ?>
			<p class="register-privacy-info">
				<?php
					/* translators: %s: link to the terms of service page. */
					echo wp_kses_post( sprintf( __( 'By creating an account, you agree to the %s.', 'reign' ), $terms_link ) );
				?>
			</p>
				<?php
		}
		}

		if ( $show_legal_agreement ) {
			do_action( 'bp_legal_agreement_errors' );
		}
	}
endif;

if ( ! function_exists( 'reign_notifications_avatar' ) ) {

	/**
	 * Added buddypress notification avatar
	 *
	 * @since 6.3.6
	 */
	function reign_notifications_avatar() {
		$bp      = buddypress();
		$user_id = $bp->notifications->query_loop->notification->secondary_item_id;
		if ( empty( $user_id ) ) {
			$user_id = $bp->notifications->query_loop->notification->item_id;
		}
		echo bp_core_fetch_avatar( /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			array(
				'item_id' => $user_id,
				'type'    => 'thumb',
			)
		);
	}
}


add_filter( 'bp_nouveau_get_activity_entry_buttons', 'reign_theme_bp_nouveau_get_activity_entry_buttons', 99, 2 );
function reign_theme_bp_nouveau_get_activity_entry_buttons( $buttons, $activity_id ) {
	if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) {
		unset( $buttons['activity_delete'] );
	}

	return $buttons;
}

/*
 * Replace Mark as Favirite to Like and Rrmove Favirite to Unlike
 */

add_filter( 'gettext', 'reign_bp_string_translate', 10, 3 );
function reign_bp_string_translate( $translation, $text, $domain ) {

	if ( 'buddypress' === $domain ) {
		if ( 'Remove Favorite' === $text ) {
			$translation = esc_html__( 'Unlike', 'reign' );
		}

		if ( 'Mark as Favorite' === $text ) {
			$translation = esc_html__( 'Like', 'reign' );
		}

		if ( 'My Favorites' === $text ) {
			$translation = esc_html__( 'Likes', 'reign' );
		}
	}

	return $translation;
}


// `bp_nouveau_*` namespace collision — see sibling guards above.
if ( ! function_exists( 'bp_nouveau_activity_entry_dropdown_toggle_buttons' ) ) :
	function bp_nouveau_activity_entry_dropdown_toggle_buttons( $args = array() ) {
		$output = join( ' ', bb_nouveau_get_activity_entry_dropdown_toggle_buttons( $args ) );

		ob_start();

		do_action( 'bp_activity_entry_dropdown_toggle_meta' );

		$output .= ob_get_clean();

		$has_content = trim( $output, ' ' );
		if ( ! $has_content ) {
			return;
		}

		if ( ! $args ) {
			$args = array( 'container_classes' => array( 'bp-activity-more-options-wrap', 'activity-meta' ) );
		}

		$output = sprintf( '<span class="bp-activity-more-options-action activity-meta action" data-balloon-pos="up" data-balloon="%s"><i class="fa fa-ellipsis-h"></i></span><div class="bp-activity-more-options">%s</div>', esc_html__( 'More Options', 'reign' ), $output );

		bp_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
	}
endif;

// `bb_*` is BuddyBoss Platform's own global namespace. Any Reign site
// with BuddyBoss Platform active would fatal at theme-load when the
// platform pre-declared the same name. function_exists() guard makes
// the two coexist: if BuddyBoss already defines the function, we leave
// it alone (our renderer was a Nouveau-targeted helper anyway).
if ( ! function_exists( 'bb_nouveau_get_activity_entry_dropdown_toggle_buttons' ) ) :
	function bb_nouveau_get_activity_entry_dropdown_toggle_buttons( $args ) {
		$buttons = array();
		if ( ! isset( $GLOBALS['activities_template'] ) ) {
			return $buttons;
		}

		$activity_id    = bp_get_activity_id();
		$activity_type  = bp_get_activity_type();
		$parent_element = '';
		$button_element = 'a';

		if ( ! $activity_id ) {
			return $buttons;
		}

		/*
		 * If the container is set to 'ul' force the $parent_element to 'li',
		 * else use parent_element args if set.
		 *
		 * This will render li elements around anchors/buttons.
		 */
		if ( isset( $args['container'] ) && 'ul' === $args['container'] ) {
			$parent_element = 'li';
		} elseif ( ! empty( $args['parent_element'] ) ) {
			$parent_element = $args['parent_element'];
		}

		$parent_attr = ( ! empty( $args['parent_attr'] ) ) ? $args['parent_attr'] : array();

		/*
		 * If we have an arg value for $button_element passed through
		 * use it to default all the $buttons['button_element'] values
		 * otherwise default to 'a' (anchor)
		 * Or override & hardcode the 'element' string on $buttons array.
		 *
		 */
		if ( ! empty( $args['button_element'] ) ) {
			$button_element = $args['button_element'];
		}

		// The delete button is always created, and removed later on if needed.
		$delete_args = array();

		/*
		 * As the delete link is filterable we need this workaround
		 * to try to intercept the edits the filter made and build
		 * a button out of it.
		 */
		if ( has_filter( 'bp_get_activity_delete_link' ) ) {
			preg_match( '/<a\s[^>]*>(.*)<\/a>/siU', bp_get_activity_delete_link(), $link );

			if ( ! empty( $link[0] ) && ! empty( $link[1] ) ) {
				$delete_args['link_text'] = $link[1];
				$subject                  = str_replace( $delete_args['link_text'], '', $link[0] );
			}

			preg_match_all( '/([\w\-]+)=([^"\'> ]+|([\'"]?)(?:[^\3]|\3+)+?\3)/', $subject, $attrs );

			if ( ! empty( $attrs[1] ) && ! empty( $attrs[2] ) ) {
				foreach ( $attrs[1] as $key_attr => $key_value ) {
					$delete_args[ 'link_' . $key_value ] = trim( $attrs[2][ $key_attr ], '"' );
				}
			}

			$delete_args = bp_parse_args(
				$delete_args,
				array(
					'link_text'   => '',
					'button_attr' => array(
						'link_id'         => '',
						'link_href'       => '',
						'link_class'      => '',
						'link_rel'        => 'nofollow',
						'data_bp_tooltip' => '',
					),
				),
				'nouveau_get_activity_entry_buttons'
			);
		}

		if ( empty( $delete_args['link_href'] ) ) {
			$delete_args = array(
				'button_element'  => $button_element,
				'link_id'         => '',
				'link_class'      => 'button item-button bp-secondary-action bp-tooltip delete-activity confirm',
				'link_rel'        => 'nofollow',
				'data_bp_tooltip' => _x( 'Delete', 'button', 'reign' ),
				'link_text'       => _x( 'Delete', 'button', 'reign' ),
				'link_href'       => bp_get_activity_delete_url(),
			);

			// If button element set add nonce link to data-attr attr
			if ( 'button' === $button_element ) {
				$delete_args['data-attr'] = bp_get_activity_delete_url();
				$delete_args['link_href'] = '';
			} else {
				$delete_args['link_href'] = bp_get_activity_delete_url();
				$delete_args['data-attr'] = '';
			}
		}

		$buttons['activity_delete'] = array(
			'id'                => 'activity_delete',
			'position'          => 35,
			'component'         => 'activity',
			'parent_element'    => $parent_element,
			'parent_attr'       => $parent_attr,
			'must_be_logged_in' => true,
			'button_element'    => $button_element,
			'button_attr'       => array(
				'id'              => $delete_args['link_id'],
				'href'            => $delete_args['link_href'],
				'class'           => $delete_args['link_class'],
				'data-bp-tooltip' => $delete_args['data_bp_tooltip'],
				'data-bp-nonce'   => $delete_args['data-attr'],
			),
			'link_text'         => sprintf( '<span class="bp-screen-reader-text">%s</span>', esc_html( $delete_args['data_bp_tooltip'] ) ),
		);

		// Add the Spam Button if supported
		if ( bp_is_akismet_active() && isset( buddypress()->activity->akismet ) && bp_activity_user_can_mark_spam() ) {
			$buttons['activity_spam'] = array(
				'id'                => 'activity_spam',
				'position'          => 45,
				'component'         => 'activity',
				'parent_element'    => $parent_element,
				'parent_attr'       => $parent_attr,
				'must_be_logged_in' => true,
				'button_element'    => $button_element,
				'button_attr'       => array(
					'class'           => 'bp-secondary-action spam-activity confirm button item-button bp-tooltip',
					'id'              => 'activity_make_spam_' . $activity_id,
					'data-bp-tooltip' => _x( 'Spam', 'button', 'reign' ),
				),
				'link_text'         => sprintf(
					/** @todo: use a specific css rule for this */
					'<span class="dashicons dashicons-flag" style="color:#a00;vertical-align:baseline;width:18px;height:18px" aria-hidden="true"></span><span class="bp-screen-reader-text">%s</span>',
					esc_html_x( 'Spam', 'button', 'reign' )
				),
			);

			// If button element, add nonce link to data attribute.
			if ( 'button' === $button_element ) {
				$data_element = 'data-bp-nonce';
			} else {
				$data_element = 'href';
			}

			if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
				$buttons['activity_spam']['button_attr'][ $data_element ] = wp_nonce_url(
					bp_get_root_url() . '/' . bp_nouveau_get_component_slug( 'activity' ) . '/spam/' . $activity_id . '/',
					'bp_activity_akismet_spam_' . $activity_id
				);
			} else {
				$buttons['activity_spam']['button_attr'][ $data_element ] = wp_nonce_url(
					bp_get_root_domain() . '/' . bp_nouveau_get_component_slug( 'activity' ) . '/spam/' . $activity_id . '/',
					'bp_activity_akismet_spam_' . $activity_id
				);
			}
		}
		/**
		 * Filter to add your buttons, use the position argument to choose where to insert it.
		 *
		 * @since BuddyBoss 1.7.2
		 *
		 * @param array $buttons     The list of buttons.
		 * @param int   $activity_id The current activity ID.
		 */
		$buttons_group = apply_filters( 'bb_nouveau_get_activity_entry_dropdown_toggle_buttons', $buttons, $activity_id );

		if ( ! $buttons_group ) {
			return $buttons;
		}

		// It's the first entry of the loop, so build the Group and sort it.
		if ( ! isset( bp_nouveau()->activity->entry_buttons ) || ! is_a( bp_nouveau()->activity->entry_buttons, 'BP_Buttons_Reign_Group' ) ) {
			$sort                                 = true;
			bp_nouveau()->activity->entry_buttons = new BP_Buttons_Reign_Group( $buttons_group );

			// It's not the first entry, the order is set, we simply need to update the Buttons Group.
		} else {
			$sort = false;
			bp_nouveau()->activity->entry_buttons->update( $buttons_group );
		}

		$return = bp_nouveau()->activity->entry_buttons->get( $sort );

		if ( ! $return ) {
			return array();
		}

		// Remove the Delete button if the user can't delete.
		if ( ! bp_activity_user_can_delete() ) {
			unset( $return['activity_delete'] );
		}

		do_action_ref_array( 'bb_nouveau_return_activity_entry_dropdown_toggle_buttons', array( &$return, $activity_id ) );

		return $return;
	}
endif;

class BP_Buttons_Reign_Group {

	protected $group = array();

	public function __construct( $args = array() ) {
		foreach ( $args as $arg ) {
			$this->add( $arg );
		}
	}

	public function sort( $buttons ) {
		$sorted = array();

		foreach ( $buttons as $button ) {
			$position = 99;

			if ( isset( $button['position'] ) ) {
				$position = (int) $button['position'];
			}

			// If position is already taken, move to the first next available
			if ( isset( $sorted[ $position ] ) ) {
				$sorted_keys = array_keys( $sorted );

				do {
					++$position;
				} while ( in_array( $position, $sorted_keys, true ) );
			}

			$sorted[ $position ] = $button;
		}

		ksort( $sorted );
		return $sorted;
	}


	public function get( $sort = true ) {
		$buttons = array();

		if ( empty( $this->group ) ) {
			return $buttons;
		}

		if ( true === $sort ) {
			$this->group = $this->sort( $this->group );
		}

		foreach ( $this->group as $key_button => $button ) {
			// Reindex with ids.
			if ( true === $sort ) {
				$this->group[ $button['id'] ] = $button;
				unset( $this->group[ $key_button ] );
			}

			$buttons[ $button['id'] ] = bp_get_button( $button );
		}

		return $buttons;
	}


	public function update( $args = array() ) {
		$this->group = array();
		foreach ( $args as $id => $params ) {
			$this->set( $params );
		}
	}


	private function add( $args ) {
		$r = bp_parse_args(
			(array) $args,
			array(
				'id'                => '',
				'position'          => 99,
				'component'         => '',
				'must_be_logged_in' => true,
				'block_self'        => false,
				'parent_element'    => false,
				'parent_attr'       => array(),
				'button_element'    => 'a',
				'button_attr'       => array(),
				'link_text'         => '',
			),
			'buttons_group_constructor'
		);

		// Just don't set the button if a param is missing
		if ( empty( $r['id'] ) || empty( $r['component'] ) || empty( $r['link_text'] ) ) {
			return false;
		}

		$r['id'] = sanitize_key( $r['id'] );

		// If the button already exist don't add it
		if ( isset( $this->group[ $r['id'] ] ) ) {
			return false;
		}

		/*
		 * If, in bp_nouveau_get_*_buttons(), we pass through a false value for 'parent_element'
		 * but we have attributtes for it in the array, let's default to setting a div.
		 *
		 * Otherwise, the original false value will be passed through to BP buttons.
		 * @todo: this needs review, probably trying to be too clever
		 */
		if ( ( ! empty( $r['parent_attr'] ) ) && false === $r['parent_element'] ) {
			$r['parent_element'] = 'div';
		}

		$this->group[ $r['id'] ] = $r;
		return true;
	}
}

// `bp_activity_get_*` is a well-known BuddyPress naming pattern.
// function_exists() guards in case a future BP/BB update declares
// these names first.
if ( ! function_exists( 'bp_activity_get_share_count' ) ) :
	function bp_activity_get_share_count( $post_id, $is_bp = false ) {
		$share_count = 0;

		if ( empty( $post_id ) ) {
			return $share_count;
		}

		if ( ( function_exists( 'bp_is_activity_directory' ) && bp_is_activity_directory() ) || true === $is_bp ) {
			$share_count = bp_activity_get_meta( $post_id, 'share_count', true );
		} elseif ( is_single() ) {
			$share_count = bp_activity_get_meta( $post_id, 'share_count', true );
		}

		if ( empty( $share_count ) ) {
			$share_count = 0;
		}

		return $share_count;
	}
endif;

if ( ! function_exists( 'bp_activity_get_post_comment_count' ) ) :
	function bp_activity_get_post_comment_count( $post_id ) {
		$comment_count = get_comments_number( $post_id );
		if ( empty( $comment_count ) ) {
			$comment_count = 0;
		}
		return $comment_count;
	}
endif;

if ( ! function_exists( 'reign_theme_set_unread_notification' ) ) {

	/**
	 * Added new function to unread notification from header
	 *
	 * @since 6.6.0
	 */
	function reign_theme_set_unread_notification() {
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'reign_notification_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Security check failed. Please refresh the page and try again.', 'reign' ),
				)
			);
			return;
		}

		if ( ! function_exists( 'buddypress' ) || ! bp_is_active( 'notifications' ) ) {
			return;
		}

		$notif_id = isset( $_POST['notification_id'] ) ? sanitize_text_field( wp_unslash( $_POST['notification_id'] ) ) : '';
		if ( 'all' !== $notif_id ) {
			$notif_id = isset( $_POST['notification_id'] ) ? sanitize_text_field( wp_unslash( $_POST['notification_id'] ) ) : '';
			$notif_id = preg_replace( '/[^0-9]/', '', $notif_id );
		}
		if ( ! empty( $notif_id ) && 'all' !== $notif_id ) {
			BP_Notifications_Notification::update(
				array( 'is_new' => 0 ),
				array( 'id' => $notif_id )
			);
		} elseif ( 'all' === $notif_id ) {
			$user_id          = bp_loggedin_user_id();
			$notification_ids = BP_Notifications_Notification::get(
				array(
					'user_id'           => $user_id,
					'order_by'          => 'date_notified',
					'sort_order'        => 'DESC',
					'page'              => 1,
					'per_page'          => 10,
					'update_meta_cache' => false,
				)
			);
			if ( $notification_ids ) {
				foreach ( $notification_ids as $notification_id ) {
					BP_Notifications_Notification::update(
						array( 'is_new' => 0 ),
						array( 'id' => $notification_id->id )
					);
				}
			}
		}
		$response = array();
		ob_start();

		if ( bp_has_notifications( bp_ajax_querystring( 'notifications' ) . '&user_id=' . get_current_user_id() . '&is_new=1' ) ) :
			?>
		<div class="dropdown-item-wrapper">
			<?php
			while ( bp_the_notifications() ) :
				bp_the_notification();
				?>
				<div class="dropdown-item read-item <?php echo isset( buddypress()->notifications->query_loop->notification->is_new ) && buddypress()->notifications->query_loop->notification->is_new ? 'unread' : ''; ?>">
					<div class="notification-item-content">
						<div class="item-avatar">
							<?php reign_notifications_avatar(); ?>
						</div>
						<div class="item-info">
							<div class="dropdown-item-title notification ellipsis"><?php bp_the_notification_description(); ?></div>
							<p class="mute"><?php bp_the_notification_time_since(); ?></p>
						</div>
					</div>
					<div class="actions">
						<a class="mark-read action-unread primary" data-bp-tooltip-pos="left" data-bp-tooltip="<?php esc_attr_e( 'Mark as Read', 'reign' ); ?>" data-notification-id="<?php bp_the_notification_id(); ?>">
							<i class="fa-regular fa-eye-slash"></i>
						</a>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
		<?php else : ?>
			<div class="alert-message">
				<div class="alert alert-warning" role="alert"><?php esc_html_e( 'No notifications found.', 'reign' ); ?></div>
			</div>
			<?php
		endif;

		$response['contents']            = ob_get_clean();
		$response['total_notifications'] = bp_notifications_get_unread_notification_count( bp_displayed_user_id() );
		wp_send_json_success( $response );
	}

	add_action( 'wp_ajax_reign_theme_unread_notification', 'reign_theme_set_unread_notification' );
}

// Add a filter to modify the HTML elements of a BuddyPress xProfile field when editing.
add_filter( 'bp_xprofile_field_edit_html_elements', 'bp_xprofile_field_edit_checkbox_acceptance', 10, 2 );

/**
 * Modify the HTML elements of a BuddyPress xProfile field when editing.
 *
 * @param array $r    An array containing HTML elements for the xProfile field being edited.
 *
 * @return array Modified HTML elements for the xProfile field editing form.
 */
function bp_xprofile_field_edit_checkbox_acceptance( $r ) {
	// Get the field ID from the HTML elements.
	$field_id = $r['name'];

	// Check if the 'class' key exists in the $r array.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only registration-form prefill from URL parameters.
	if ( isset( $r['class'] ) && ! empty( $_GET[ $field_id ] ) && strpos( $r['class'], 'checkbox-acceptance' ) !== false ) {
		// Convert the GET parameter to an integer.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only registration-form prefill from URL parameters.
		$new_checkbox_acceptance = absint( wp_unslash( $_GET[ $field_id ] ) );

		// If the value is 1, mark the checkbox as checked, make it readonly, and prevent onclick events.
		if ( 1 === $new_checkbox_acceptance ) {
			$r['checked']  = 'checked';
			$r['readonly'] = 'readonly';
			$r['onclick']  = 'return false;';
		}
	}

	// Return the modified HTML elements.
	return $r;
}

if ( ! function_exists( 'reign_add_recaptcha_after_login_form' ) ) {
	/**
	 * Adds a reCAPTCHA widget for login popup.
	 *
	 * @return void
	 */
	function reign_add_recaptcha_after_login_form() {
		// Check if reCAPTCHA is enabled for the login form.
		if ( function_exists( 'bb_recaptcha_is_enabled' ) && bb_recaptcha_is_enabled( 'bb_login' ) ) {
			// Display the reCAPTCHA for the login form.
			if ( function_exists( 'bb_recaptcha_display' ) ) {
				bb_recaptcha_display( 'bb_login' );
			}

			// Ensure reCAPTCHA scripts are enqueued.
			if ( function_exists( 'bb_recaptcha_add_scripts_login_footer' ) ) {
				bb_recaptcha_add_scripts_login_footer();
			}
		}
	}

	// Hook into the theme's action for displaying the reCAPTCHA.
	add_action( 'reign_recaptcha_after_login_form', 'reign_add_recaptcha_after_login_form' );
}

if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
	remove_action( 'bp_before_registration_submit_buttons', 'bb_recaptcha_registration' );
	add_action( 'bp_before_registration_submit_buttons', 'reign_recaptcha_registration' );
}

/**
 * Adds a reCAPTCHA widget for register popup.
 *
 * @return void
 */
function reign_recaptcha_registration() {

	$enable_for_register = bb_recaptcha_is_enabled( 'bb_register' );
	if ( $enable_for_register ) {
		do_action( 'bp_recaptcha_register_errors' );

		reign_recaptcha_display( 'bb_register' );

		add_action( 'wp_footer', 'reign_recaptcha_add_scripts_login_footer' );
	}
}

/**
 * Display the reCAPTCHA widget based on the configured settings.
 * This function checks the connection status with the reCAPTCHA service and displays the appropriate reCAPTCHA widget
 * based on the enabled version (v2 or v3) and the configured actions.
 *
 * @since BuddyBoss 2.5.60
 *
 * @param string $action Current action ( i.e - bb_login, bb_register etc. ). Default will be blank.
 *
 * @return void
 */
function reign_recaptcha_display( $action = '' ) {

	if ( '' === $action ) {
		return;
	}

	$verified = bb_recaptcha_connection_status();
	if ( ! empty( $verified ) && 'connected' === $verified ) {
		$site_key    = bb_recaptcha_site_key();
		$enabled_for = bb_recaptcha_recaptcha_versions();
		$lang        = bb_recaptcha_setting( 'language_code', 'en' );

		// If ip address excluded to non validate captcha then don't display recaptcha.
		if ( bb_recaptcha_allow_ip() ) {
			return;
		}
		// Recaptcha api url.
		$api_url    = 'https://www.google.com/recaptcha/api.js';
		$query_args = array();
		if ( 'en' !== $lang ) {
			$query_args['hl'] = $lang;
		}

		if ( 'recaptcha_v3' === $enabled_for ) {
			?>
			<input type="hidden" id="bb_register_recaptcha_response_id" name="g-recaptcha-response"/>
			<?php
			$query_args['render'] = $site_key;
			$api_url              = add_query_arg( $query_args, $api_url );
		} elseif ( 'recaptcha_v2' === $enabled_for ) {
			$query_args['render'] = 'explicit';
			$api_url              = add_query_arg( $query_args, $api_url );
			$v2_option            = bb_recaptcha_recaptcha_v2_option();
			$v2_class             = '';
			if ( 'v2_invisible_badge' === $v2_option ) {
				$badge_position = bb_recaptcha_v2_badge();
				if ( 'inline' === $badge_position ) {
					$v2_class = 'v2_invisible_badge';
				}
			}
			?>
			<div id="reign_recaptcha_v2_element" class="bb_recaptcha_v2_element_content <?php echo esc_attr( $v2_class ); ?>" data-sitekey="<?php echo esc_attr( $site_key ); ?>"></div>
			<?php
		}

		if ( ! wp_script_is( 'bb-recaptcha-api', 'registered' ) ) {
			if ( 'recaptcha_v3' === $enabled_for ) {
				wp_register_script( 'bb-recaptcha-api', $api_url, false, buddypress()->version, false );
			} elseif ( 'recaptcha_v2' === $enabled_for ) {
				wp_register_script( 'bb-recaptcha-api', $api_url, false, buddypress()->version, true );
			}
		}
		$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$rtl_css = is_rtl() ? '-rtl' : '';
		wp_enqueue_style( 'bb-recaptcha', bb_recaptcha_integration_url( '/assets/css/bb-recaptcha' . $rtl_css . $min . '.css' ), false, buddypress()->version );
		wp_register_script(
			'bb-recaptcha-register',
			get_template_directory_uri() . '/assets/js/bb-recaptcha-register.min.js',
			array(
				'jquery',
				'bb-recaptcha-api',
			),
			buddypress()->version
		);

		if ( bb_recaptcha_conflict_mode() ) {
			bb_recaptcha_remove_duplicate_scripts();
		}

		$enabled_for   = bb_recaptcha_recaptcha_versions();
		$localize_data = array(
			'selected_version' => $enabled_for,
			'site_key'         => bb_recaptcha_site_key(),
			'action'           => $action,
		);
		if ( 'recaptcha_v2' === $enabled_for ) {
			$localize_data['v2_option']         = bb_recaptcha_recaptcha_v2_option();
			$localize_data['v2_theme']          = bb_recaptcha_v2_theme();
			$localize_data['v2_size']           = bb_recaptcha_v2_size();
			$localize_data['v2_badge_position'] = bb_recaptcha_v2_badge();
		}

		wp_localize_script( 'bb-recaptcha-register', 'bbRegisterRecaptcha', array( 'data' => $localize_data ) );
	}
}

function reign_recaptcha_add_scripts_login_footer() {
	wp_enqueue_script( 'bb-recaptcha-register' );
}

/* Member Directory Action Buttons For Layout 3 */
add_action( 'reign_buddypress_member_send_message_button_call', 'reign_buddypress_member_send_message_button', 10 );

if ( ! function_exists( 'reign_buddypress_member_send_message_button' ) ) :
	/**
	 * Print the HTML for the BP button for the private messaging form.
	 *
	 * @since 7.6.6
	 */
	function reign_buddypress_member_send_message_button() {
		if ( is_user_logged_in() ) :
			$user_id = bp_get_member_user_id();

			if ( false !== $user_id && bp_loggedin_user_id() !== $user_id ) :
				bp_button( reign_buddypress_member_get_send_message_button_args() );
			endif;
		endif;
	}
endif;

if ( ! function_exists( 'reign_buddypress_member_get_send_message_button_args' ) ) :
	/**
	 * Get the arguments for the BP send message button.
	 *
	 * @since 7.6.6
	 */
	function reign_buddypress_member_get_send_message_button_args() {
		$user_id = bp_get_member_user_id();
		if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
			$link_href = apply_filters( 'reign_buddypress_member_send_message_button_url', wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_members_get_user_slug( $user_id ) ), $user_id );
		} else {
			$link_href = apply_filters( 'reign_buddypress_member_send_message_button_url', wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $user_id ) ), $user_id );
		}
		return apply_filters(
			'bp_get_send_message_button_args',
			array(
				'id'                => "private_message-{$user_id}",
				'component'         => 'messages',
				'must_be_logged_in' => true,
				'block_self'        => true,
				'wrapper_id'        => "send-private-message-{$user_id}",
				'wrapper_class'     => 'send-private-message',
				'link_href'         => $link_href,
				'link_text'         => esc_html__( 'Private Message', 'reign' ),
				'link_class'        => 'send-message',
				'button_attr'       => array(
					'title'               => esc_html__( 'Send Message', 'reign' ),
					'data-bp-tooltip'     => esc_html__( 'Send Message', 'reign' ),
					'data-bp-tooltip-pos' => 'up',
				),
			)
		);
	}
endif;

if ( ! function_exists( 'reign_buddypress_member_primary_actions' ) ) {
	/**
	 * Set member directory layout 3 primary action buttons.
	 *
	 * @since 7.6.6
	 */
	function reign_buddypress_member_primary_actions() {
		?>
		<div class="member-buttons-wrap">
			<div class="flex only-grid-view button-wrap member-button-wrap footer-button-wrap">
				<?php
				// Add Friend button.
				if ( bp_is_active( 'friends' ) ) {
					echo wp_kses_post( bp_get_add_friend_button( bp_get_member_user_id() ) );
				}

				// Message button.
				if ( bp_is_active( 'messages' ) ) {
					do_action( 'reign_buddypress_member_send_message_button_call' );
				}
				?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'reign_additional_member_actions' ) ) {
	/**
	 * Set member directory layout 3 more menu buttons.
	 *
	 * @since 7.6.6
	 */
	function reign_additional_member_actions() {
		bp_nouveau_members_loop_buttons(
			array(
				'container'      => 'ul',
				'button_element' => 'button',
			)
		);
	}
}

if ( ! function_exists( 'reign_profile_achievements' ) ) {

	/**
	 * Output badges on profile
	 */
	function reign_profile_achievements() {
		if ( class_exists( 'BadgeOS' ) ) {
			global $blog_id, $post;

			$type       = 'all';
			$limit      = apply_filters( 'reign_user_badges_limit', 10 );
			$offset     = 0;
			$filter     = 'completed';
			$search     = false;
			$orderby    = 'menu_order';
			$order      = 'ASC';
			$wpms       = false;
			$include    = array();
			$exclude    = array();
			$meta_key   = '';
			$meta_value = '';
			$old_post   = $post;
			$user_id    = bp_displayed_user_id();

			// Convert $type to properly support multiple achievement types.
			if ( 'all' === $type ) {
				$type = badgeos_get_achievement_types_slugs();

				// Drop steps from our list of "all" achievements.
				$step_key = array_search( 'step', $type, true );
				if ( false !== $step_key ) {
					unset( $type[ $step_key ] );
				}
			} else {
				$type = explode( ',', $type );
			}

			// Build $include array.
			if ( ! is_array( $include ) ) {
				$include = explode( ',', $include );
			}

			// Build $exclude array.
			if ( ! is_array( $exclude ) ) {
				$exclude = explode( ',', $exclude );
			}

			// Initialize output and counters.
			$achievements      = '';
			$achievement_count = 0;
			$query_count       = 0;

			// Grab hidden badges (used to filter the query).
			$hidden = badgeos_get_hidden_achievement_ids( $type );

			// If we're polling all sites, grab an array of site IDs.
			if ( $wpms && 'false' !== $wpms ) {
				$sites = badgeos_get_network_site_ids();
			} else {
				// Otherwise, use only the current site.
				$sites = array( $blog_id );
			}

			// Loop through each site (default is current site only).
			foreach ( $sites as $site_blog_id ) {

				// If we're not polling the current site, switch to the site we're polling.
				if ( $blog_id !== $site_blog_id ) {
					switch_to_blog( $site_blog_id );
				}

				// Grab earned badges.
				$earned_ids = badgeos_get_user_earned_achievement_ids( $user_id, $type );

				// Query args.
				$args = array(
					'post_type'      => $type,
					'orderby'        => $orderby,
					'order'          => $order,
					'posts_per_page' => $limit,
					'offset'         => $offset,
					'post_status'    => 'publish',
					'post__not_in'   => array_diff( $hidden, $earned_ids ),
				);

				// Filter by completed / not completed.
				if ( 'completed' === $filter ) {
					$args['post__in'] = array_merge( array( 0 ), $earned_ids );
				} elseif ( 'not-completed' === $filter ) {
					$args['post__not_in'] = array_merge( $hidden, $earned_ids );
				}

				// Meta filter.
				if ( '' !== $meta_key && '' !== $meta_value ) {
					$args['meta_key']   = $meta_key;
					$args['meta_value'] = $meta_value;
				}

				// Include certain achievements.
				if ( ! empty( $include ) ) {
					$args['post__not_in'] = array_diff( $args['post__not_in'], $include );
					$args['post__in']     = array_merge( array( 0 ), array_diff( $include, $args['post__in'] ?? array() ) );
				}

				// Exclude certain achievements.
				if ( ! empty( $exclude ) ) {
					$args['post__not_in'] = array_merge( $args['post__not_in'], $exclude );
				}

				// Search.
				if ( $search ) {
					$args['s'] = $search;
				}

				/**
				 * Allow developers to filter BadgeOS achievements query args.
				 *
				 * @param array $args    WP_Query args.
				 * @param int   $user_id Displayed user ID.
				 * @param array $type    Achievement types being queried.
				 */
				$args = apply_filters( 'reign_profile_achievements_query_args', $args, $user_id, $type );

				// Run the query.
				$achievement_posts = new WP_Query( $args );
				$query_count      += $achievement_posts->found_posts;

				while ( $achievement_posts->have_posts() ) :
					$achievement_posts->the_post();

					$achievement_id = get_the_ID();

					$item  = '<div class="ps-badgeos__item ps-badgeos__item--focus">';
					$item .= '<a href="' . esc_url( get_permalink( $achievement_id ) ) . '">';
					$item .= badgeos_get_achievement_post_thumbnail( $achievement_id );
					$item .= '</a>';
					$item .= '</div>';

					/**
					 * Filter each achievement item markup.
					 *
					 * @param string $item        HTML for the achievement item.
					 * @param object $achievement WP_Post achievement object.
					 * @param int    $user_id     Displayed user ID.
					 */
					$achievements .= apply_filters( 'reign_profile_achievements_item_html', $item, $achievement, $user_id );

					++$achievement_count;

				endwhile;

				wp_reset_postdata();
				// Restore the original global $post after the custom achievement loop.
				$post = $old_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Intentional restoration of the saved global.
			}

			$output  = '<div class="ps-badgeos__list-wrapper">';
			$output .= '<div class="ps-badgeos__list-title">' . _n( 'Recently earned badge', 'Recently earned badges', $achievement_count, 'reign' ) . '</div>';
			$output .= '<div class="ps-badgeos__list">' . $achievements . '</div>';
			$output .= '</div>';

			/**
			 * Filter the final achievements output markup.
			 *
			 * @param string $output            Final HTML output.
			 * @param int    $achievement_count Number of achievements found.
			 * @param int    $user_id           Displayed user ID.
			 */
			echo wp_kses_post( apply_filters( 'reign_profile_achievements_output', $output, $achievement_count, $user_id ) );
		}
	}
}

/**
 * Reign ajax add remove friend.
 *
 * @since 7.0.3
 */
function reign_ajax_addremove_friend() {
	if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'reign_friendship_nonce' ) ) {
		wp_send_json_error(
			array(
				'feedback' => sprintf(
					'<div class="bp-feedback error bp-ajax-message"><p>%s</p></div>',
					esc_html__( 'Security check failed. Please refresh the page and try again.', 'reign' )
				),
			)
		);
		return;
	}

	if ( function_exists( 'BuddyPress' ) ) {
		$response = array(
			'feedback' => sprintf(
				'<div class="bp-feedback error bp-ajax-message"><p>%s</p></div>',
				esc_html__( 'Something went wrong. Please try again.', 'reign' )
			),
		);
		// Bail if not a POST action.
		if ( ! bp_is_post_request() ) {
			wp_send_json_error( $response );
		}

		if ( empty( $_POST['friendship_id'] ) || ! bp_is_active( 'friends' ) ) {
			wp_send_json_error( $response );
		}

		// Cast fid as an integer.
		$friend_id = (int) $_POST['friendship_id'];

		// Check if the user exists only when the Friend ID is not a Frienship ID.
		if ( isset( $_POST['data_action'] ) && 'friends_accept_friendship' !== $_POST['data_action'] && 'friends_reject_friendship' !== $_POST['data_action'] ) {
			$user = get_user_by( 'id', $friend_id );
			if ( ! $user ) {
				wp_send_json_error(
					array(
						'feedback' => sprintf(
							'<div class="bp-feedback error">%s</div>',
							esc_html__( 'No member found by that ID.', 'reign' )
						),
					)
				);
			}
		}

		// In the 2 first cases the $friend_id is a friendship id.
		if ( ! empty( $_POST['data_action'] ) && 'friends_accept_friendship' === $_POST['data_action'] ) {
			if ( ! friends_accept_friendship( $friend_id ) ) {
				wp_send_json_error(
					array(
						'feedback' => sprintf(
							'<div class="bp-feedback error">%s</div>',
							esc_html__( 'There was a problem accepting this request. Please try again.', 'reign' )
						),
					)
				);
			} else {
				wp_send_json_success(
					array(
						'feedback' => sprintf(
							'<div class="bp-feedback success">%s</div>',
							esc_html__( 'Friendship accepted.', 'reign' )
						),
						'type'     => 'success',
						'is_user'  => true,
					)
				);
			}

			// Rejecting a friendship.
		} elseif ( ! empty( $_POST['data_action'] ) && 'friends_reject_friendship' === $_POST['data_action'] ) {
			if ( ! friends_reject_friendship( $friend_id ) ) {
				wp_send_json_error(
					array(
						'feedback' => sprintf(
							'<div class="bp-feedback error">%s</div>',
							esc_html__( 'There was a problem rejecting this request. Please try again.', 'reign' )
						),
					)
				);
			} else {
				wp_send_json_success(
					array(
						'feedback' => sprintf(
							'<div class="bp-feedback success">%s</div>',
							esc_html__( 'Friendship rejected.', 'reign' )
						),
						'type'     => 'success',
						'is_user'  => true,
					)
				);
			}
		}
	}
}

// custom ajax friend request accept/reject.
// Friendship actions require an authenticated user (BuddyPress relies on
// bp_loggedin_user_id()), so this handler is registered for logged-in
// users only. The nopriv variant was removed - it exposed the endpoint to
// unauthenticated visitors with no legitimate use case.
add_action( 'wp_ajax_reign_ajax_addremove_friend', 'reign_ajax_addremove_friend' );

/* remove action for bb platform to show the current page content on register popup*/
remove_action( 'bp_before_register_page', 'bp_register_page_content' );

if ( ! function_exists( 'reign_override_bp_legal_modals' ) ) {
	function reign_override_bp_legal_modals() {
		$reign_signin_popup = get_theme_mod( 'reign_signin_popup', false );

		if ( reign_is_truthy( $reign_signin_popup ) && function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {

			add_filter(
				'bp_term_of_service_content',
				function ( $content, $original ) {
					return $original;
				},
				10,
				2
			);

			add_filter(
				'bp_privacy_policy_content',
				function ( $content, $original ) {
					return $original;
				},
				10,
				2
			);
		}
	}

	add_action( 'init', 'reign_override_bp_legal_modals' );
}

/**
 * Check if user avatar is under moderation and return default avatar for get_avatar().
 *
 * @param string $avatar         The avatar HTML.
 * @param int|string             $id_or_email The user ID or email.
 * @param int                   $size        Avatar size.
 * @param string                $default     Default avatar URL.
 * @param string                $alt         Alt text.
 * @param array                 $args        Avatar arguments.
 *
 * @return string Modified avatar or original.
 */
function reign_check_get_avatar_moderation( $avatar, $id_or_email, $size, $default, $alt, $args ) {
	if ( is_object( $id_or_email ) || '' === $id_or_email ) {
		return $avatar;
	}

	if ( ! class_exists( 'Buddypress_Moderation_Pro_Avatar' ) ) {
		return $avatar;
	}

	$bmpro_profile_avatar_stngs = get_option( 'bmpro_profile_avatar_stngs' );
	if ( ! isset( $bmpro_profile_avatar_stngs['profile_avatar_mod_on'] ) || 'yes' !== $bmpro_profile_avatar_stngs['profile_avatar_mod_on'] ) {
		return $avatar;
	}

	$user_id = 0;
	if ( is_int( $id_or_email ) ) {
		$user_id = $id_or_email;
	} elseif ( is_string( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
		if ( $user ) {
			$user_id = $user->ID;
		}
	}

	if ( empty( $user_id ) || $user_id <= 0 ) {
		return $avatar;
	}

	$post_args = array(
		'post_author'    => $user_id,
		'post_type'      => 'bmpro_avatar_spam',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_bp_user_id',
				'value'   => $user_id,
				'compare' => '=',
			),
			array(
				'key'     => '_bp_user_avatar_flag',
				'value'   => 0,
				'compare' => '=',
			),
		),
	);

	$avatar_query = new WP_Query( $post_args );

	if ( $avatar_query->have_posts() ) {
		remove_filter( 'get_avatar', 'reign_check_get_avatar_moderation', 1000, 6 );

		global $wbtm_reign_settings;
		$avatar_default_image = isset( $wbtm_reign_settings['reign_buddyextender']['avatar_default_image'] ) ? $wbtm_reign_settings['reign_buddyextender']['avatar_default_image'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-mem-avatar.png';

		$default_avatar = '<img src="' . esc_url( $avatar_default_image ) . '" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" class="avatar" alt="' . esc_attr( $alt ) . '" />';

		add_filter( 'get_avatar', 'reign_check_get_avatar_moderation', 1000, 6 );

		return $default_avatar;
	}

	return $avatar;
}

add_filter( 'get_avatar', 'reign_check_get_avatar_moderation', 1000, 6 );

/**
 * Heartbeat: push unread counts so the header badge stays current without a page reload.
 */
if ( ! function_exists( 'reign_theme_heartbeat_notification_count' ) ) {
	function reign_theme_heartbeat_notification_count( $response, $data ) {
		if ( ! is_user_logged_in() || ! function_exists( 'buddypress' ) ) {
			return $response;
		}

		$count_data = array(
			'notification'         => 0,
			'unread_message'       => 0,
			'friend_request'       => 0,
			'notification_content' => array(),
		);

		if ( bp_is_active( 'notifications' ) ) {
			$count_data['notification'] = (int) bp_notifications_get_unread_notification_count( bp_loggedin_user_id() );
		}

		if ( bp_is_active( 'messages' ) ) {
			if ( class_exists( 'BP_Better_Messages' ) && function_exists( 'Better_Messages' ) ) {
				$count_data['unread_message'] = (int) Better_Messages()->functions->get_total_threads_for_user( get_current_user_id(), 'unread' );
			} elseif ( function_exists( 'bp_get_total_unread_messages_count' ) ) {
				$count_data['unread_message'] = (int) bp_get_total_unread_messages_count( bp_loggedin_user_id() );
			}
		}

		if ( bp_is_active( 'friends' ) && function_exists( 'friends_get_requests_for_user' ) ) {
			$requests                     = friends_get_requests_for_user( bp_loggedin_user_id() );
			$count_data['friend_request'] = is_array( $requests ) ? count( $requests ) : 0;
		}

		$response['reign_notification_count'] = $count_data;
		return $response;
	}
	add_filter( 'heartbeat_received', 'reign_theme_heartbeat_notification_count', 11, 2 );
}

/**
 * AJAX: mark a single BP message thread as read, return updated unread count.
 */
if ( ! function_exists( 'reign_theme_mark_message_thread_read' ) ) {
	function reign_theme_mark_message_thread_read() {
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'reign_message_nonce' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Security check failed. Please refresh the page.', 'reign' ) ) );
			return;
		}

		if ( ! is_user_logged_in() || ! function_exists( 'buddypress' ) || ! bp_is_active( 'messages' ) ) {
			wp_send_json_error();
			return;
		}

		$thread_id = isset( $_POST['thread_id'] ) ? absint( $_POST['thread_id'] ) : 0;

		if ( $thread_id > 0 && class_exists( 'BP_Messages_Thread' ) ) {
			BP_Messages_Thread::mark_as_read( $thread_id );
		}

		$unread_count = function_exists( 'bp_get_total_unread_messages_count' )
			? (int) bp_get_total_unread_messages_count( bp_loggedin_user_id() )
			: 0;

		wp_send_json_success( array( 'unread_count' => $unread_count ) );
	}
	add_action( 'wp_ajax_reign_theme_mark_message_thread_read', 'reign_theme_mark_message_thread_read' );
}

/**
 * AJAX: mark all unread BP message threads as read, return zero count.
 */
if ( ! function_exists( 'reign_theme_mark_all_messages_read' ) ) {
	function reign_theme_mark_all_messages_read() {
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'reign_message_nonce' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Security check failed. Please refresh the page.', 'reign' ) ) );
			return;
		}

		if ( ! is_user_logged_in() || ! function_exists( 'buddypress' ) || ! bp_is_active( 'messages' ) ) {
			wp_send_json_error();
			return;
		}

		$user_id = bp_loggedin_user_id();

		if ( bp_has_message_threads(
			array(
				'user_id'  => $user_id,
				'type'     => 'unread',
				'per_page' => 100,
				'max'      => 100,
			)
		) ) {
			global $messages_template;
			while ( bp_message_threads() ) {
				bp_message_thread();
				$thread_id = bp_get_message_thread_id();
				if ( $thread_id > 0 && class_exists( 'BP_Messages_Thread' ) ) {
					BP_Messages_Thread::mark_as_read( $thread_id );
				}
			}
		}

		wp_send_json_success( array( 'unread_count' => 0 ) );
	}
	add_action( 'wp_ajax_reign_theme_mark_all_messages_read', 'reign_theme_mark_all_messages_read' );
}

/**
 * AJAX: return fresh messages dropdown inner HTML (used when badge count and DOM are out of sync).
 */
if ( ! function_exists( 'reign_theme_get_messages_dropdown' ) ) {
	function reign_theme_get_messages_dropdown() {
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'reign_message_nonce' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Security check failed. Please refresh the page.', 'reign' ) ) );
			return;
		}

		if ( ! is_user_logged_in() || ! function_exists( 'buddypress' ) || ! bp_is_active( 'messages' ) ) {
			wp_send_json_error();
			return;
		}

		ob_start();
		get_template_part( 'template-parts/header-icons/messages-dropdown-inner' );
		$contents = ob_get_clean();

		$unread_count = 0;
		if ( class_exists( 'BP_Better_Messages' ) && function_exists( 'Better_Messages' ) ) {
			$unread_count = (int) Better_Messages()->functions->get_total_threads_for_user( get_current_user_id(), 'unread' );
		} elseif ( function_exists( 'bp_get_total_unread_messages_count' ) ) {
			$unread_count = (int) bp_get_total_unread_messages_count( bp_loggedin_user_id() );
		}

		wp_send_json_success(
			array(
				'contents'     => $contents,
				'unread_count' => $unread_count,
			)
		);
	}
	add_action( 'wp_ajax_reign_theme_get_messages_dropdown', 'reign_theme_get_messages_dropdown' );
}

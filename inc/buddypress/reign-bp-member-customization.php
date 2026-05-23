<?php
/**
 * BuddyPress Members Customization File
 *
 * @package Reign
 */

/**
 * MEMBER DIRECTORY CUSTOMIZATION
 */

/**
 * Showing member cover image on member directory page
 *
 * @since 1.0.7
 */
if ( ! function_exists( 'reign_render_member_cover_image' ) ) {
	add_action( 'reign_before_member_avatar_member_directory', 'reign_render_member_cover_image', 10 );

	/**
	 * Renders the cover image for a member in the BuddyPress member directory.
	 *
	 * This function checks the member directory type from the theme settings and displays the
	 * member's cover image if the directory type supports it. It attempts to retrieve the cover
	 * image from the cache for performance, and if not found, it fetches the image using
	 * BuddyPress functions. If no cover image is set, a default cover image is displayed.
	 * The image is lazy-loaded for performance optimization.
	 *
	 * @global array $wbtm_reign_settings Theme settings array containing various configuration options.
	 *
	 * @return void
	 */
	function reign_render_member_cover_image() {
		global $wbtm_reign_settings;
		$member_directory_type = isset( $wbtm_reign_settings['reign_buddyextender']['member_directory_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['member_directory_type'] : 'wbtm-member-directory-type-2';

		if ( in_array( $member_directory_type, array( 'wbtm-member-directory-type-2', 'wbtm-member-directory-type-3' ), true ) ) {
			$user_id       = bp_get_member_user_id();
			$cover_img_url = wp_cache_get( 'member_cover_image_' . $user_id );

			if ( false === $cover_img_url ) {
				$args          = array(
					'object_dir' => 'members',
					'item_id'    => $user_id,
					'type'       => 'cover-image',
				);
				$cover_img_url = bp_attachments_get_attachment( 'url', $args );

				if ( empty( $cover_img_url ) ) {
					$cover_img_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] ) ?
						$wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] :
						REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
				}
				wp_cache_set( 'member_cover_image_' . $user_id, $cover_img_url, '', 600 ); // Cache for 10 minutes.
			}

			echo '<div class="wbtm-mem-cover-img"><img src="' . esc_url( $cover_img_url ) . '" loading="lazy" alt="" /></div>'; // Decorative cover image — alt intentionally empty.
		}
	}
}

/**
 * Showing member statistics on member directory page
 *
 * @since 1.0.7
 */
if ( ! function_exists( 'reign_render_bp_directory_members_item' ) ) {

	add_action( 'reign_bp_nouveau_directory_members_item', 'reign_render_bp_directory_members_item', 50 );

	/**
	 * Renders additional member information in the BuddyPress member directory.
	 *
	 * This function checks the selected member directory type from the theme settings and, based on the type, renders various information like friends count, followers, following, BadgeOS points, and myCRED points. Each data point is cached to optimize performance, reducing the number of database queries. The function only executes if the member directory type is set to display the extended information.
	 *
	 * @global array $wbtm_reign_settings Global theme settings array containing configuration options for BuddyPress extensions.
	 *
	 * @return void
	 */
	function reign_render_bp_directory_members_item() {
		global $wbtm_reign_settings;
		$member_directory_type = isset( $wbtm_reign_settings['reign_buddyextender']['member_directory_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['member_directory_type'] : 'wbtm-member-directory-type-1';

		// Exit if the member directory type is 1.
		if ( 'wbtm-member-directory-type-1' === $member_directory_type ) {
			return;
		}

		$info_array = array();
		$user_id    = bp_get_member_user_id();

		// Handle Friends Count.
		if ( bp_is_active( 'friends' ) ) {
			$friends_count = wp_cache_get( 'friends_count_' . $user_id );

			if ( false === $friends_count ) {
				$friends_count = friends_get_total_friend_count( $user_id );
				wp_cache_set( 'friends_count_' . $user_id, $friends_count, '', 600 ); // Cache for 10 minutes.
			}

			// Determine the URL based on BuddyPress version.
			$url_to_use = function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' )
			? bp_members_get_user_url( $user_id ) . bp_get_friends_slug()
			: bp_core_get_user_domain( $user_id ) . bp_get_friends_slug();

			$url_to_use = esc_url( $url_to_use );

			// Check if BuddyBoss is active to use 'Connections' instead of 'Friends'.
			if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
				// BuddyBoss Platform: 'Connections'.
				$friends_label = ( $friends_count == 1 ) ? __( '1 Connection', 'reign' ) : sprintf( __( '%s Connections', 'reign' ), number_format_i18n( $friends_count ) );
			} else {
				// Default BuddyPress: 'Friends'.
				$friends_label = ( $friends_count == 1 ) ? __( '1 Friend', 'reign' ) : sprintf( __( '%s Friends', 'reign' ), number_format_i18n( $friends_count ) );
			}

			// Add to the info array.
			$info_array['friends'] = array(
				'tooltip_text' => $friends_label,
				'url'          => $url_to_use,
				'icon_class'   => 'far fa-user',
				'color'        => '#EC7063',
				'extra_class'  => 'friends-section',
			);
		}

		// Handle Followers and Following if BP Follow is active.
		if ( class_exists( 'BP_Follow_Component' ) ) {
			// Initialize counts.
			$followers_count = 0;
			$following_count = 0;

			$followers_count = wp_cache_get( 'followers_count_' . $user_id );
			$following_count = wp_cache_get( 'following_count_' . $user_id );

			if ( false === $followers_count ) {
				$followers_count = count( bp_follow_get_followers( array( 'user_id' => $user_id ) ) );
				wp_cache_set( 'followers_count_' . $user_id, $followers_count, '', 600 ); // Cache for 10 minutes.
			}

			if ( false === $following_count ) {
				$following_count = count( bp_follow_get_following( array( 'user_id' => $user_id ) ) );
				wp_cache_set( 'following_count_' . $user_id, $following_count, '', 600 ); // Cache for 10 minutes.
			}

			// Determine the base URL based on BuddyPress version.
			$base_url = function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' )
			? bp_members_get_user_url( $user_id )
			: bp_core_get_user_domain( $user_id );

			// Create the followers URL.
			$url_to_followers = esc_url( $base_url . 'followers' );
			$url_to_following = esc_url( $base_url . 'following' );

			// Handle singular/plural for followers and following.
			$followers_label = ( $followers_count === 1 ) ? __( '1 Follower', 'reign' ) : sprintf( __( '%s Followers', 'reign' ), $followers_count );
			$following_label = ( $following_count === 1 ) ? __( '1 Following', 'reign' ) : sprintf( __( '%s Following', 'reign' ), $following_count );

			$info_array['followers'] = array(
				'tooltip_text' => $followers_label,
				'url'          => $url_to_followers,
				'icon_class'   => 'far fa-users',
				'color'        => '#5DADE2',
			);

			$info_array['following'] = array(
				'tooltip_text' => $following_label,
				'url'          => $url_to_following,
				'icon_class'   => 'fab fa-weixin',
				'color'        => '#F5B041',
			);
		}

		// Handle Followers and Following if BB Platform is active.
		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			// Check if Activity Follow is enabled.
			if ( bp_is_activity_follow_active() ) {
				// Initialize counts.
				$followers_count = 0;
				$following_count = 0;

				$followers_count = wp_cache_get( 'bb_followers_count_' . $user_id );
				$following_count = wp_cache_get( 'bb_following_count_' . $user_id );

				if ( false === $followers_count ) {
					$followers_count = count( bp_get_followers( array( 'user_id' => $user_id ) ) );
					wp_cache_set( 'bb_followers_count_' . $user_id, $followers_count, '', 600 ); // Cache for 10 minutes.
				}

				if ( false === $following_count ) {
					$following_count = count( bp_get_following( array( 'user_id' => $user_id ) ) );
					wp_cache_set( 'bb_following_count_' . $user_id, $following_count, '', 600 ); // Cache for 10 minutes.
				}

				// Determine the base URL based on BuddyPress version.
				$base_url = function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' )
				? bp_members_get_user_url( $user_id )
				: bp_core_get_user_domain( $user_id );

				// Create the followers URL.
				$url_to_followers = esc_url( $base_url . 'followers' );
				$url_to_following = esc_url( $base_url . 'following' );

				// Handle singular/plural for followers and following.
				$followers_label = ( $followers_count === 1 ) ? __( '1 Follower', 'reign' ) : sprintf( __( '%s Followers', 'reign' ), $followers_count );
				$following_label = ( $following_count === 1 ) ? __( '1 Following', 'reign' ) : sprintf( __( '%s Followings', 'reign' ), $following_count );

				$info_array['followers'] = array(
					'tooltip_text' => $followers_label,
					'url'          => $url_to_followers,
					'icon_class'   => 'far fa-users',
					'color'        => '#5DADE2',
				);

				$info_array['following'] = array(
					'tooltip_text' => $following_label,
					'url'          => $url_to_following,
					'icon_class'   => 'fab fa-weixin',
					'color'        => '#F5B041',
				);
			}
		}

		// Handle BadgeOS Points if BadgeOS is active.
		if ( class_exists( 'BadgeOS' ) && class_exists( 'BadgeOS_Community' ) ) {
			$user_points = wp_cache_get( 'badgeos_points_' . $user_id );

			if ( false === $user_points ) {
				$user_points = get_user_meta( $user_id, '_badgeos_points', true );
				$user_points = ! empty( $user_points ) ? $user_points : 0;
				wp_cache_set( 'badgeos_points_' . $user_id, $user_points, '', 600 ); // Cache for 10 minutes.
			}

			// Determine the base URL based on BuddyPress version.
			$base_url = function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' )
			? bp_members_get_user_url( $user_id )
			: bp_core_get_user_domain( $user_id );

			// Create the achievements URL.
			$url_to_points = esc_url( $base_url . 'achievements' );

			$info_array['badgeos_points'] = array(
				'tooltip_text' => sprintf( '%s Points', $user_points ),
				'url'          => $url_to_points,
				'icon_class'   => 'far fa-trophy',
				'color'        => '#99A3A4',
				'extra_class'  => 'badgeos-section',
			);
		}

		// Handle myCRED Points if myCRED is active.
		if ( class_exists( 'myCRED_Core' ) ) {
			$mycred_points = wp_cache_get( 'mycred_points_' . $user_id );

			if ( false === $mycred_points ) {
				global $mycred;
				$mycred_points = $mycred->get_users_balance( $user_id );
				$mycred_points = $mycred->format_creds( $mycred_points );
				wp_cache_set( 'mycred_points_' . $user_id, $mycred_points, '', 600 ); // Cache for 10 minutes.
			}

			$url_to_mycred = esc_url( bp_members_get_user_url( $user_id ) . 'mycred-history' );

			// Determine the base URL based on BuddyPress version.
			$base_url = ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) )
			? bp_members_get_user_url( $user_id )
			: bp_core_get_user_domain( $user_id );

			// Create the myCRED URL.
			$url_to_mycred = esc_url( $base_url . 'mycred-history' );

			$info_array['mycred_points'] = array(
				'tooltip_text' => sprintf( '%s Points', $mycred_points ),
				'url'          => $url_to_mycred,
				'icon_class'   => 'fa fa-tag',
				'color'        => '#DC7633',
			);
		}

		// Output the information.
		echo '<div class="wbtm-member-directory-meta">';
		foreach ( $info_array as $info ) {
			$extra_class = isset( $info['extra_class'] ) ? ' ' . esc_attr( $info['extra_class'] ) : '';
			echo '<div class="rtm-tooltip' . esc_attr( $extra_class ) . '" style="background: ' . esc_attr( $info['color'] ) . '">';
			echo '<a href="' . esc_url( $info['url'] ) . '"><i class="' . esc_attr( $info['icon_class'] ) . '"></i></a>';
			echo '<span class="rtm-tooltiptext">' . esc_html( $info['tooltip_text'] ) . '</span>';
			echo '</div>';
		}
		echo '</div>';
	}
}

/**
 * Manging action buttons on member directory page
 *
 * @since 1.0.7
 */
if ( ! function_exists( 'reign_bp_dir_mem_send_private_message_button' ) ) {
	add_action( 'bp_directory_members_actions', 'reign_bp_dir_mem_send_private_message_button' );

	/**
	 * Displays a private message button for a user in the BuddyPress member directory.
	 *
	 * The button is shown only if the following conditions are met:
	 * - BuddyPress Messages component is active.
	 * - The user is logged in.
	 * - The profile being viewed is not the current user's profile.
	 * - There are no moderation issues that would prevent sending messages.
	 * - The Youzify plugin is not active.
	 *
	 * If moderation is in place (e.g., the user has flagged content or has a spam status), the button will not be displayed.
	 * If the Youzify plugin is active, this function will not run.
	 *
	 * Outputs an HTML button that links to the private message compose page with a pre-filled recipient.
	 *
	 * @return void
	 */
	function reign_bp_dir_mem_send_private_message_button() {
		// Check if messaging is active, user is logged in, and it's not the current user's profile.
		if ( ! bp_is_active( 'messages' ) || ! is_user_logged_in() || bp_is_my_profile() ) {
			return;
		}

		$user_id  = bp_get_member_user_id();
		$cuser_id = get_current_user_id();

		// Check if there are any moderation issues.
		if ( function_exists( 'bmpro_get_activity_post_status' ) && bmpro_check_related_actions( $cuser_id, 'bmpro_member_actions', 'remove_pm' ) ) {
			$bmpro_actmeta   = get_user_meta( $cuser_id, 'bmpro_flagged_member_meta', true );
			$act_post_status = bmpro_get_activity_post_status( $cuser_id, 'bmpro_spam_status' );
			$auto_mod_limit  = bmpro_auto_mod_limit_count( 'bmpro_member_stngs' );

			if ( isset( $bmpro_actmeta['reporters'] ) ) {
				$total_flags = count( $bmpro_actmeta['reporters'] );

				$remove = ( is_bmpro_auto_mod( 'bmpro_member_stngs' ) && $total_flags >= $auto_mod_limit )
						|| ( 'awaiting_moderation' === $act_post_status )
						|| ( 'spam' === $act_post_status );

				if ( $remove || ( ! empty( $bmpro_actmeta ) && in_array( 'remove_pm', $bmpro_actmeta, true ) ) ) {
					return;
				}
			}
		}

		// Load only if BOTH Youzify and BP Better Messages are NOT active.
		if ( ! class_exists( 'Youzify' ) && ! class_exists( 'BP_Better_Messages' ) ) {
			// Generate the URL for composing a private message to the user.
			if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
				$private_msg_url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_members_get_user_slug( $user_id ) );
			} else {
				$private_msg_url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $user_id ) );
			}

			// Default settings for the private message button.
			$defaults = array(
				'block_self'        => true,
				'must_be_logged_in' => true,
				'component'         => 'messages',
				'wrapper_class'     => 'message-button',
				'link_class'        => 'wbtm-send-message',
				'link_text'         => __( 'Message', 'reign' ),
				'id'                => 'private_message-' . $user_id,
				'wrapper_id'        => 'send-private-message-' . $user_id,
				'link_href'         => $private_msg_url,
				'link_title'        => __( 'Send a private message to this user.', 'reign' ),
			);

			// Output the private message button.
			echo bp_get_button( $defaults ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/**
 * MEMBER SINGLE PAGE CUSTOMIZATION
 */

/**
 * Showing social media links on member cover image
 *
 * @since 1.0.7
 */
if ( ! function_exists( 'rth_bp_member_header_meta_social_links' ) ) {
	add_action( 'bp_before_member_header_meta', 'rth_bp_member_header_meta_social_links', 15 );

	/**
	 * Outputs social media links in the BuddyPress member header meta area.
	 *
	 * This function is hooked to the `bp_before_member_header_meta` action. It checks if social media links are enabled in the theme settings
	 * and retrieves the user's social media links from user meta. If there are social media links available, it renders them as a list of
	 * icons or images inside a `<div>` element.
	 *
	 * The function performs the following steps:
	 * 1. Checks if social media links are enabled in the theme settings.
	 * 2. Retrieves the user's social media links from user meta.
	 * 3. Constructs the HTML output by iterating over the available social media links.
	 * 4. Outputs the constructed HTML.
	 *
	 * @global array $wbtm_reign_settings Global theme settings array.
	 */
	function rth_bp_member_header_meta_social_links() {
		global $wbtm_reign_settings;
		if ( ! isset( $wbtm_reign_settings['reign_buddyextender']['enable_profile_social_links'] ) || 'on' !== $wbtm_reign_settings['reign_buddyextender']['enable_profile_social_links'] ) {
			return;
		}

		$socials = get_user_meta( bp_displayed_user_id(), 'wbtm_user_social_links', true );
		if ( empty( $socials ) ) {
			return;
		}

		$html_to_render = '<div class="wbtm-social-media-links"><ul>';
		foreach ( reign_get_user_social_array() as $field_slug => $social ) {
			if ( empty( $socials[ $field_slug ] ) ) {
				continue;
			}

			$social_icon_or_image = empty( $social['img_url'] )
				? '<i class="fab fa-' . strtolower( esc_attr( trim( $social['name'] ) ) ) . '"></i>'
				: '<img src="' . esc_url( $social['img_url'] ) . '" alt="' . esc_attr( $social['name'] ) . '" />';

			$html_to_render .= '<li><a href="' . esc_url( $socials[ $field_slug ] ) . '" title="' . esc_attr( $social['name'] ) . '" target="_blank">' . $social_icon_or_image . '</a></li>';
		}
		$html_to_render .= '</ul></div>';

		echo $html_to_render; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Managing the positin of social media links on member cover image
 * For Header Type #3 only
 *
 * @since 1.0.7
 */
if ( ! function_exists( 'rth_bp_member_header_meta_top_social_links' ) ) {
	add_action( 'reign_bp_before_displayed_user_mentionname', 'rth_bp_member_header_meta_top_social_links', 10 );

	/**
	 * Adds social links to the member header meta section based on the header type setting.
	 *
	 * This function hooks into 'reign_bp_before_displayed_user_mentionname' to conditionally display
	 * social links in the member header based on the configured header type. If the header type is
	 * 'wbtm-cover-header-type-3', it removes the default social links action and directly renders
	 * the social links for that specific header type.
	 *
	 * @since 1.0.0
	 */
	function rth_bp_member_header_meta_top_social_links() {
		global $wbtm_reign_settings;

		// Retrieve the member header class from the settings, default to 'wbtm-cover-header-type-1'.
		$member_header_class = isset( $wbtm_reign_settings['reign_buddyextender']['member_header_type'] )
			? $wbtm_reign_settings['reign_buddyextender']['member_header_type']
			: 'wbtm-cover-header-type-1';

		// Apply any customizations to the member header class via filters.
		$member_header_class = apply_filters( 'wbtm_rth_manage_member_header_class', $member_header_class );

		// Check if the current member header type is 'wbtm-cover-header-type-3'.
		if ( 'wbtm-cover-header-type-3' === $member_header_class ) {
			// Remove the default action for member header meta social links.
			remove_action( 'bp_before_member_header_meta', 'rth_bp_member_header_meta_social_links', 15 );

			// Directly render social links for the specific header type.
			rth_bp_member_header_meta_social_links();
		}
	}
}

/**
 * showing member statistics on member cover image
 *
 * @since 1.0.7
 */
if ( ! function_exists( 'reign_render_extra_usermeta_info' ) ) {
	add_action( 'reign_member_extra_info_section', 'reign_render_extra_usermeta_info' );

	function reign_render_extra_usermeta_info() {
		$info_array = array();
		$user_id    = bp_displayed_user_id();

		if ( bp_is_active( 'friends' ) ) {
			$friends_count         = friends_get_total_friend_count( $user_id );
			$info_array['friends'] = array(
				'value' => $friends_count,
				'label' => __( 'Friends', 'reign' ),
			);
		}

		if ( class_exists( 'BP_Follow_Component' ) ) {
			$followers               = bp_follow_get_followers();
			$info_array['followers'] = array(
				'value' => count( $followers ),
				'label' => __( 'Followers', 'reign' ),
			);
			$following               = bp_follow_get_following();
			$info_array['following'] = array(
				'value' => count( $following ),
				'label' => __( 'Following', 'reign' ),
			);
		}

		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			$is_enabled_followers = bb_enabled_profile_header_layout_element( 'followers' );
			$is_enabled_following = bb_enabled_profile_header_layout_element( 'following' );

			if ( function_exists( 'bp_is_activity_follow_active' ) && bp_is_activity_follow_active() ) {
				if ( $is_enabled_followers ) {
					$followers = bp_get_followers();
					$info_array['followers'] = array(
						'value' => count( $followers ),
						'label' => __( 'Followers', 'reign' ),
					);
				}

				if ( $is_enabled_following ) {
					$following = bp_get_following();
					$info_array['following'] = array(
						'value' => count( $following ),
						'label' => __( 'Following', 'reign' ),
					);
				}
			}
		}

		if ( class_exists( 'BadgeOS' ) && class_exists( 'BadgeOS_Community' ) ) {
			$user_points = get_user_meta( $user_id, $meta_key    = '_badgeos_points', true );
			if ( empty( $user_points ) ) {
				$user_points = 0;
			}
			$info_array['badgeos_points'] = array(
				'value' => $user_points,
				'label' => __( 'Points', 'reign' ),
			);
		}

		if ( class_exists( 'myCRED_Core' ) ) {
			global $mycred, $mycred_modules;
			$myCRED_BuddyPress_Module_Obj = $mycred_modules['type']['mycred_default']['buddypress'];
			$users_balance                = $mycred->get_users_balance( $user_id );
			$users_balance                = $mycred->format_creds( $users_balance );
			$info_array['mycred_points']  = array(
				'value' => $users_balance,
				'label' => __( 'Points', 'reign' ),
			);
		}

		// Add Visitors count.
		if ( class_exists( 'Bp_Profile_Views' ) ) {
			global $wpdb;
			$visitor_count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(DISTINCT `viewer_id`) FROM `{$wpdb->prefix}bp_profile_views` WHERE `user_id` = %d AND `viewer_id` != 0",
					$user_id
				)
			);

			if ( ! empty( $visitor_count ) ) {
				$visitor_label = $visitor_count == 1 ? __( 'Visitor', 'reign' ) : __( 'Visitors', 'reign' );

				$info_array['visitors'] = array(
					'value' => $visitor_count,
					'label' => $visitor_label,
				);
			}
		}

		foreach ( $info_array as $key => $info ) {
			?>
			<div class="rtm-usermeta-box">
				<span class="rtm-usermeta-count">
					<?php echo $info['value']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
				<span class="rtm-usermeta-text">
					<?php echo $info['label']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</div>
			<?php
		}
	}
}

/**
 * Compatibility with myCRED plugin
 * removes the default value render by myCRED plugin
 *
 * @since   1.0.7
 */
if ( ! function_exists( 'reign_remove_mycred_bp_profile_header' ) ) {
	add_filter( 'mycred_bp_profile_header', 'reign_remove_mycred_bp_profile_header', 10, 3 );

	function reign_remove_mycred_bp_profile_header( $output, $myCRED_buddypress_bal_template, $myCRED_BuddyPress_Module_Obj ) {
		$output = '';
		return $output;
	}
}

/**
********************************************************
********************************************************
******** MEMBER SINGLE PAGE CUSTOMIZATION :: END ********
********************************************************
********************************************************
*/

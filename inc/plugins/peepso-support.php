<?php
/**
 * PeepSo compatibility functions.
 *
 * @package Reign
 * @since 7.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', 'reign_peepso_set_default_social_fields' );

function reign_peepso_set_default_social_fields() {
	global $wbtm_reign_settings;

	$wbtm_peepso_social_links = isset( $wbtm_reign_settings['reign_peepsoextender']['wbtm_social_links'] ) ? $wbtm_reign_settings['reign_peepsoextender']['wbtm_social_links'] : array();
	if ( empty( $wbtm_peepso_social_links ) ) {
		$wbtm_peepso_social_links = array(
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
				'name'    => __( 'Linkedin', 'reign' ),
			),
		);
		$wbtm_reign_settings['reign_peepsoextender']['wbtm_social_links'] = $wbtm_peepso_social_links;
		update_option( 'reign_options', $wbtm_reign_settings );
		$wbtm_reign_settings = get_option( 'reign_options', array() );
	}

	/*
	* Set Default value when activate reign theme
	*/
	if ( empty( $wbtm_reign_settings['reign_buddyextender'] ) ) {
		$wbtm_reign_settings['reign_buddyextender']['member_header_position'] = 'top';
		$wbtm_reign_settings['reign_buddyextender']['member_header_type']     = 'wbtm-cover-header-type-3';
		$wbtm_reign_settings['reign_buddyextender']['group_header_type']      = 'wbtm-cover-header-type-3';
		$wbtm_reign_settings['reign_buddyextender']['member_directory_type']  = 'wbtm-member-directory-type-2';
		$wbtm_reign_settings['reign_buddyextender']['group_directory_type']   = 'wbtm-group-directory-type-2';

		$wbtm_reign_settings['reign_buddyextender']['member_cover_image'] = 'on';
		$wbtm_reign_settings['reign_buddyextender']['group_image']        = 'on';
		$wbtm_reign_settings['reign_buddyextender']['group_cover_image']  = 'on';
		update_option( 'reign_options', $wbtm_reign_settings );
		$wbtm_reign_settings = get_option( 'reign_options', array() );
	}
}

/**
 * Showing PeepSo group cover image.
 */
if ( ! function_exists( 'reign_render_peepso_group_cover_image' ) ) {

	function reign_render_peepso_group_cover_image() {
		global $wbtm_reign_settings;
		$cover_img_url = isset( $wbtm_reign_settings['reign_peepsoextender']['default_group_cover_image_url'] ) ? $wbtm_reign_settings['reign_peepsoextender']['default_group_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
		if ( empty( $cover_img_url ) ) {
			$cover_img_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
		}
		return $cover_img_url;
	}
}

/**
 * Showing PeepSo member cover image.
 */
if ( ! function_exists( 'reign_render_peepso_member_cover_image' ) ) {

	function reign_render_peepso_member_cover_image() {
		global $wbtm_reign_settings;
		$cover_img_url = isset( $wbtm_reign_settings['reign_peepsoextender']['default_profile_cover_image_url'] ) ? $wbtm_reign_settings['reign_peepsoextender']['default_profile_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
		if ( empty( $cover_img_url ) ) {
			$cover_img_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
		}
		return $cover_img_url;
	}
}

/**
 * Get PeepSo member cover image.
 */
if ( ! function_exists( 'reign_get_peepso_member_cover_image' ) ) {

	function reign_get_peepso_member_cover_image( $size = 0 ) {
		// PeepSoProfile is a separate class from PeepSo; guard against the
		// case where PeepSo loads partially (custom installs, partial
		// activation) where the main PeepSo class exists but the Profile
		// module hasn't been included.
		if ( ! class_exists( 'PeepSoProfile' ) ) {
			return null;
		}
		$cover         = null;
		$PeepSoProfile = PeepSoProfile::get_instance();
		$PeepSoUser    = $PeepSoProfile->user;
		$cover_hash    = get_user_meta( $PeepSoUser->get_id(), 'peepso_cover_hash', true );

		if ( $cover_hash ) {
			$cover_hash = $cover_hash . '-';
		}
		$filename = $cover_hash . 'cover.jpg';
		if ( file_exists( $PeepSoUser->get_image_dir() . $filename ) ) {
			$cover = $PeepSoUser->get_image_url() . $filename;

			if ( is_int( $size ) && $size > 0 ) {
				$filename_scaled = $cover_hash . 'cover-' . $size . '.jpg';
				if ( ! file_exists( $PeepSoUser->get_image_dir() . $filename_scaled ) ) {
					$si = new PeepSoSimpleImage();
					$si->png_to_jpeg( $PeepSoUser->get_image_dir() . $filename );
					$si->load( $PeepSoUser->get_image_dir() . $filename );
					$si->resizeToWidth( $size );
					$si->save( $PeepSoUser->get_image_dir() . $filename_scaled, IMAGETYPE_JPEG );
				}

				$cover = $PeepSoUser->get_image_url() . $filename_scaled;
			}
		}

		return $cover;
	}
}

/**
 * Get all social fields added in backend.
 */
function reign_get_peepso_user_social_array() {
	global $wbtm_reign_settings;
	$wbtm_social_links = isset( $wbtm_reign_settings['reign_peepsoextender']['wbtm_social_links'] ) ? $wbtm_reign_settings['reign_peepsoextender']['wbtm_social_links'] : array();
	return reign_sanitize_social_links_config( $wbtm_social_links );
}

/**
 * Added a class for group directory in body class.
 */
add_filter( 'body_class', 'reign_peepso_body_class', 999, 2 );

function reign_peepso_body_class( $classes, $class ) {
	if ( class_exists( 'PeepSo' ) ) {
		array_push( $classes, 'reign_peepso_active' );
		// PeepSoUrlSegments is a separate module from PeepSo's main class.
		if ( ! class_exists( 'PeepSoUrlSegments' ) ) {
			return $classes;
		}
		$peepso_url_segments = PeepSoUrlSegments::get_instance();
		if ( ( 'peepso_groups' === $peepso_url_segments->_shortcode ) && ( sizeof( $peepso_url_segments->_segments ) == 1 ) ) {
			if ( is_array( $classes ) ) {
				array_push( $classes, 'reign_peepso_group_directory_page' );
			}
		}
	}
	return $classes;
}

/**
 * Added social links fields in user profile.
 */
add_filter( 'peepso_profile_edit_form', 'reign_peepso_profile_edit_form', 10, 1 );

function reign_peepso_profile_edit_form( $form ) {
	$user_id = get_current_user_id();
	if ( ! empty( $form ) ) {
		if ( isset( $form['fields'] ) ) {
			$fields        = $form['fields'];
			$social_fields = reign_get_peepso_user_social_array();
			if ( ! empty( $social_fields ) ) {
				foreach ( $social_fields as $field_slug => $social ) {
					$social_link = get_user_meta( $user_id, 'wbcom_social_' . $field_slug, true );
					if ( empty( $social_link ) ) {
						$social_link = '';
					}
					$val    = array(
						'section' => esc_html__( 'Your Account', 'reign' ),
						'label'   => $social['name'],
						'type'    => 'text',
						'value'   => $social_link,
						'html'    => $social_link,
					);
					$fields = array_slice( $fields, 0, count( $fields ) - 2, true ) +
					array( 'wbcom_social_' . $field_slug => $val ) +
					array_slice( $fields, count( $fields ) - 2, count( $fields ) - ( count( $fields ) - 2 ), true );
				}
				$form['fields'] = $fields;
			}
		}
	}
	return $form;
}

add_action( 'peepso_save_profile_form', 'reign_peepso_profile_after_save', 10, 1 );

function reign_peepso_profile_after_save( $userid ) {
	$form_arr = filter_input_array( INPUT_POST, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	if ( filter_input( INPUT_POST, 'account' ) ) {
		foreach ( $form_arr as $key => $value ) {
			if ( strpos( $key, 'wbcom_social_' ) !== false ) {
				$social_link = reign_sanitize_social_link_url( $form_arr[ $key ] );
				update_user_meta( $userid, $key, $social_link );
			}
		}
	}
}

function reign_peepso_social_not_all_empty( $userid ) {
	$social_fields = reign_get_peepso_user_social_array();
	if ( ! empty( $social_fields ) ) {
		foreach ( $social_fields as $field_slug => $social ) {
			$social_link = get_user_meta( $userid, 'wbcom_social_' . $field_slug, true );
			if ( ! empty( $social_link ) ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Display social links for a PeepSo user.
 */
function reign_peepso_user_social_links( $userid ) {
	$social_fields = reign_get_peepso_user_social_array();
	if ( ! empty( $social_fields ) && reign_peepso_social_not_all_empty( $userid ) ) {

		$html_to_render = '';
		$counter        = 0;
		$first_time     = true;

		foreach ( $social_fields as $field_slug => $social ) {
			++$counter;
			$social_link = get_user_meta( $userid, 'wbcom_social_' . $field_slug, true );

			if ( ! isset( $social_link ) || empty( $social_link ) ) {
				continue;
			}

			if ( $first_time ) {
				$html_to_render .= '<ul>';
				$first_time      = false;
			}

			$html_to_render .= '<li>';
			$html_to_render .= '<a href="' . esc_url( $social_link ) . '" title="' . esc_attr( $social['name'] ) . '">';

			if ( empty( $social['img_url'] ) ) {
				$html_to_render .= '<i class="fab fa-' . esc_attr( strtolower( trim( $social['name'] ) ) ) . '"></i>';
			} else {
				$html_to_render .= '<img src="' . esc_url( $social['img_url'] ) . '" alt="' . esc_attr( $social['name'] ) . '" />';
			}

			$html_to_render .= '</a>';
			$html_to_render .= '</li>';

			if ( $counter == count( $social_fields ) ) {
				$html_to_render .= '</ul>';
			}
		}
		echo wp_kses_post( $html_to_render );
	}
}

add_action( 'init', 'reign_peepso_default_widgets', 15 );

/**
 * Set default widgets in Left, Right sidebars and Header Widget area.
 */
function reign_peepso_default_widgets() {
	$active_widgets       = get_option( 'sidebars_widgets' );
	$default_reign_widget = get_option( 'set_default_peepso_reign_widgets' );

	if ( empty( $default_reign_widget ) ) {
		$default_reign_widget = array();
	}
	if ( class_exists( 'PeepSo' ) ) {

		// Set default widgets in Header Area.
		if ( ! array_key_exists( 'peepso_reign_header_widget', $default_reign_widget ) ) {
			$default_widget_content = array();
			$counter                = ! empty( $active_widgets['reign-header-widget-area'] ) ? count( $active_widgets['reign-header-widget-area'] ) + 1 : '';
			if ( empty( $active_widgets['reign-header-widget-area'] ) ) {
				$active_widgets['reign-header-widget-area'][0] = 'peepsowidgetuserbar-' . $counter;
			} else {
				array_push( $active_widgets['reign-header-widget-area'], 'peepsowidgetuserbar-' . $counter );
			}
			$default_widget_content[ $counter ] = array(
				'content_position'   => 'left',
				'show_avatar'        => 1,
				'show_name'          => 1,
				'show_notifications' => 1,
				'show_usermenu'      => 1,
				'show_logout'        => 1,
			);
			update_option( 'widget_peepsowidgetuserbar', $default_widget_content );
			$default_reign_widget['peepso_reign_header_widget'] = 1;
		}

		// Set default widgets in left sidebar.
		if ( ! array_key_exists( 'peepso_reign_sidebar_left_profile', $default_reign_widget ) ) {
			$default_widget_content = array();
			$counter                = ! empty( $active_widgets['sidebar-left'] ) ? count( $active_widgets['sidebar-left'] ) + 1 : '';
			if ( empty( $active_widgets['sidebar-left'] ) ) {
				$active_widgets['sidebar-left'][0] = 'peepsowidgetme-' . $counter;
			} else {
				array_unshift( $active_widgets['sidebar-left'], 'peepsowidgetme-' . $counter );
			}
			$default_widget_content[ $counter ] = array(
				'show_notifications'   => 1,
				'show_community_links' => 1,
				'show_cover'           => 1,
			);
			update_option( 'widget_peepsowidgetme', $default_widget_content );
			$default_reign_widget['peepso_reign_sidebar_left_profile'] = 1;
		}

		// Set default widget in right sidebar.
		// 1. Set online members widget.
		if ( ! array_key_exists( 'peepso_reign_sidebar_right_online_members', $default_reign_widget ) ) {
			$default_widget_content = array();
			$counter                = 1;
			if ( empty( $active_widgets['sidebar-right'] ) ) {
				$active_widgets['sidebar-right'][0] = 'peepsowidgetonlinemembers-' . $counter;
			} else {
				array_unshift( $active_widgets['sidebar-right'], 'peepsowidgetonlinemembers-' . $counter );
			}
			$default_widget_content[ $counter ] = array( 'limit' => 12 );
			update_option( 'widget_peepsowidgetonlinemembers', $default_widget_content );
			$default_reign_widget['peepso_reign_sidebar_right_online_members'] = 1;
		}

		// 2. Set community audio and video widget.
		if ( class_exists( 'PeepSoVideos' ) ) {
			if ( ! array_key_exists( 'peepso_reign_sidebar_right_community_videos', $default_reign_widget ) ) {
				$default_widget_content = array();
				$counter                = 1;
				if ( empty( $active_widgets['sidebar-right'] ) ) {
					$active_widgets['sidebar-right'][0] = 'peepsowidgetcommunityvideos-' . $counter;
				} else {
					array_unshift( $active_widgets['sidebar-right'], 'peepsowidgetcommunityvideos-' . $counter );
				}
				$default_widget_content[ $counter ] = array(
					'limit'      => 12,
					'media_type' => 'video',
					'hideempty'  => 0,
				);
				update_option( 'widget_peepsowidgetcommunityvideos', $default_widget_content );
				$default_reign_widget['peepso_reign_sidebar_right_community_videos'] = 1;
			}
		}

		// 3. Set photos widget.
		if ( class_exists( 'PeepSoSharePhotos' ) ) {
			if ( ! array_key_exists( 'peepso_reign_sidebar_right_photos', $default_reign_widget ) ) {
				$default_widget_content = array();
				$counter                = 1;
				if ( empty( $active_widgets['sidebar-right'] ) ) {
					$active_widgets['sidebar-right'][0] = 'peepsowidgetphotos-' . $counter;
				} else {
					array_unshift( $active_widgets['sidebar-right'], 'peepsowidgetphotos-' . $counter );
				}
				$default_widget_content[ $counter ] = array(
					'limit'     => 12,
					'hideempty' => 0,
				);
				update_option( 'widget_peepsowidgetphotos', $default_widget_content );
				$default_reign_widget['peepso_reign_sidebar_right_photos'] = 1;
			}
		}

		// 4. Set hashtag widget.
		if ( class_exists( 'PeepSoWidgetHashtags' ) ) {
			if ( ! array_key_exists( 'peepso_reign_sidebar_right_hashtags', $default_reign_widget ) ) {
				$default_widget_content = array();
				$counter                = 1;
				if ( empty( $active_widgets['sidebar-right'] ) ) {
					$active_widgets['sidebar-right'][0] = 'peepsowidgethashtags-' . $counter;
				} else {
					array_unshift( $active_widgets['sidebar-right'], 'peepsowidgethashtags-' . $counter );
				}
				$default_widget_content[ $counter ] = array( 'limit' => 12 );
				update_option( 'widget_peepsowidgethashtags', $default_widget_content );
				$default_reign_widget['peepso_reign_sidebar_right_hashtags'] = 1;
			}
		}

		update_option( 'sidebars_widgets', $active_widgets );
		update_option( 'set_default_peepso_reign_widgets', $default_reign_widget );
	}
}

add_action( 'peepso_init', 'reign_peepso_page_default_sidebar', 15 );

/**
 * Set default sidebar and page template in PeepSo pages.
 */
function reign_peepso_page_default_sidebar() {
	// peepso_init hook fires only when PeepSo is active, but guard
	// anyway in case the hook is dispatched defensively or the
	// PeepSo class is partially loaded.
	if ( ! class_exists( 'PeepSo' ) ) {
		return;
	}
	$pages         = array(
		'page_activity'  => PeepSo::get_option( 'page_activity' ),
		'page_members'   => PeepSo::get_option( 'page_members' ),
		'page_profile'   => PeepSo::get_option( 'page_profile' ),
		'page_groups'    => PeepSo::get_option( 'page_groups' ),
		'page_messages'  => PeepSo::get_option( 'page_messages' ),
		'page_wpadverts' => 'wpadverts',
	);
	$updated_pages = get_option( 'set_default_peepso_reign_page_sidebar' );
	$theme_slug    = apply_filters( 'wbcom_essential_theme_slug', 'reign' );

	if ( empty( $updated_pages ) ) {
		$updated_pages = array();
	}

	foreach ( $pages as $key => $slug ) {
		$page_url = PeepSo::get_page( $slug );
		if ( $page_url ) {
			$post_id = url_to_postid( $page_url );
			if ( ! $post_id ) {
				continue;
			}

			if ( ! isset( $updated_pages[ $slug ] ) ) {
				$wbcom_metabox_data = get_post_meta( $post_id, $theme_slug . '_wbcom_metabox_data', true );
				if ( empty( $wbcom_metabox_data ) ) {
					$wbcom_metabox_data = array();
				}
				$wbcom_metabox_data['layout'] = array(
					'site_layout'       => 'both_sidebar',
					'primary_sidebar'   => 'sidebar-right',
					'secondary_sidebar' => 'sidebar-left',
				);
				update_post_meta( $post_id, $theme_slug . '_wbcom_metabox_data', $wbcom_metabox_data );
				$updated_pages[ $slug ] = 1;
			}

			// Set Page template.
			if ( 'page_profile' === $key || 'page_groups' === $key ) {
				if ( ! isset( $updated_pages[ $key . '_template' ] ) ) {
					update_post_meta( $post_id, '_wp_page_template', 'page-peepso-single-layout.php' );
					$updated_pages[ $key . '_template' ] = 1;
				}
			}
		}
	}
	update_option( 'set_default_peepso_reign_page_sidebar', $updated_pages );
}

add_filter( 'reign_alter_display_right_sidebar', 'reign_peepso_display_right_sidebar_for_woo', 11, 1 );

/**
 * Display Right sidebar for cart and checkout tab in PeepSo pages.
 */
function reign_peepso_display_right_sidebar_for_woo( $display ) {
	if ( class_exists( 'PeepSo' ) && class_exists( 'PeepSoUrlSegments' ) ) {
		$peepso_url_segments = PeepSoUrlSegments::get_instance();
		if ( ( 'peepso_profile' === $peepso_url_segments->_shortcode ) ) {
			if ( class_exists( 'WooCommerce' ) ) {
				if ( is_cart() || is_checkout() ) {
					$display = true;
				}
			}
		}
	}
	return $display;
}

add_filter( 'peepso_hovercard', 'reign_peepso_member_hovercard', 10, 2 );

function reign_peepso_member_hovercard( $res, $uid ) {
	// Same module-availability guard as the cover-image helper above.
	if ( ! class_exists( 'PeepSoUser' ) ) {
		return $res;
	}
	$cover      = null;
	$size       = 750;
	$PeepSoUser = PeepSoUser::get_instance( $uid );
	$cover_hash = get_user_meta( $uid, 'peepso_cover_hash', true );

	if ( $cover_hash ) {
		$cover_hash = $cover_hash . '-';
	}
	$filename = $cover_hash . 'cover.jpg';
	if ( file_exists( $PeepSoUser->get_image_dir() . $filename ) ) {
		$cover = $PeepSoUser->get_image_url() . $filename;

		if ( is_int( $size ) && $size > 0 ) {
			$filename_scaled = $cover_hash . 'cover-' . $size . '.jpg';
			if ( ! file_exists( $PeepSoUser->get_image_dir() . $filename_scaled ) ) {
				$si = new PeepSoSimpleImage();
				$si->png_to_jpeg( $PeepSoUser->get_image_dir() . $filename );
				$si->load( $PeepSoUser->get_image_dir() . $filename );
				$si->resizeToWidth( $size );
				$si->save( $PeepSoUser->get_image_dir() . $filename_scaled, IMAGETYPE_JPEG );
			}

			$cover = $PeepSoUser->get_image_url() . $filename_scaled;
		}
	}
	if ( empty( $cover ) ) {
		$cover = reign_render_peepso_member_cover_image();
	}
	$res['cover'] = $cover;
	return $res;
}

<?php
/**
 * bbPress compatibility functions.
 *
 * @package Reign
 * @since 7.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function reign_bbp_get_reply_avtar( $topic_id = 0 ) {
	if ( class_exists( 'bbPress' ) ) {

		$topic_id = bbp_get_topic_id( $topic_id );

		$r = array(
			'post_type'      => 'reply',
			'post_parent'    => $topic_id,
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$replies = new WP_Query( $r );
		if ( isset( $replies->posts ) ) {
			$reply_author_avtar = '';
			foreach ( $replies->posts as $key => $reply ) {
				echo get_avatar( $reply->post_author );
			}
		}
	}
	wp_reset_postdata();
}

if ( class_exists( 'bbPress' ) ) {

	add_post_type_support( bbp_get_forum_post_type(), 'thumbnail' );

	/**
	 * Get the forum thumbnail's image tag.
	 *
	 * @since reign 7.2.1
	 */
	if ( ! function_exists( 'bbp_get_forum_thumbnail_image' ) ) {
		function bbp_get_forum_thumbnail_image( $forum_id = null, $size = null, $type = null ) {
			$thumbnail_id = get_post_thumbnail_id( $forum_id );
			if ( $thumbnail_id ) {
				return wp_get_attachment_image( $thumbnail_id, $size );
			}

			$group_ids = array();

			if ( function_exists( 'bbp_get_forum_group_ids' ) ) {
				$group_ids = bbp_get_forum_group_ids( $forum_id );
			}

			if ( ! empty( $group_ids ) ) {
				$group_id = $group_ids[0];

				// BuddyPress group functions only exist when BP is active.
				// bbPress can be installed without BP, so guard before use.
				if ( ! function_exists( 'bp_is_active' ) ) {
					return '';
				}

				// Group cover image check.
				if ( bp_is_active( 'groups' ) && ! bp_disable_group_cover_image_uploads() && bp_attachments_get_group_has_cover_image( $group_id ) ) {
					$group_cover_image = bp_attachments_get_attachment(
						'url',
						array(
							'object_dir' => 'groups',
							'item_id'    => $group_id,
						)
					);

					if ( ! empty( $group_cover_image ) ) {
						return '<img src="' . esc_url( $group_cover_image ) . '" alt="' . esc_attr( bbp_get_forum_title( $forum_id ) ) . '" />';
					}
				}

				// Group avatar fallback.
				if ( bp_is_active( 'groups' ) && ! bp_disable_group_avatar_uploads() && bp_get_group_has_avatar( $group_id ) ) {
					return bp_core_fetch_avatar(
						array(
							'item_id'       => $group_id,
							'object'        => 'group',
							'type'          => $type,
							'force_default' => false,
						)
					);
				}
			}

			return '';
		}
	}
}

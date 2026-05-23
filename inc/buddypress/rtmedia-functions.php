<?php
/**
 * This is rtMedia functions file.
 *
 * @package Reign
 */

if ( class_exists( 'RTMediaActivity' ) ) {

	class reign_RTMediaActivity extends RTMediaActivity {

		var $media         = array();
		var $activity_text = '';
		var $privacy;

		/**
		 * @param $media
		 * @param int   $privacy
		 * @param bool  $activity_text
		 */
		function __construct( $media, $privacy = 0, $activity_text = false ) {
			if ( ! isset( $media ) ) {
				return false;
			}
			if ( ! is_array( $media ) ) {
				$media = array( $media );
			}

			$this->media         = $media;
			$this->activity_text = bp_activity_filter_kses( $activity_text );
			$this->privacy       = $privacy;
		}

		function create_activity_html( $type = 'activity' ) {
			$activity_container_start = sprintf( '<div class="rtmedia-%s-container">', esc_attr( $type ) );
			$activity_container_end   = '</div>';

			$activity_text = '';

			// Activity text content markup.
			if ( ! empty( $this->activity_text ) && '&nbsp;' !== $this->activity_text ) {
				$activity_text .= sprintf(
					'<div class="rtmedia-%s-text">
					<span>%s</span>
					</div>',
					esc_attr( $type ),
					$this->activity_text
				);
			}

			global $rtmedia;
			if ( isset( $rtmedia->options['buddypress_limitOnActivity'] ) ) {
				$limit_activity_feed = $rtmedia->options['buddypress_limitOnActivity'];
			} else {
				$limit_activity_feed = 0;
			}

			$rtmedia_model = new RTMediaModel();
			$media_details = $rtmedia_model->get( array( 'id' => $this->media ) );

			if ( intval( $limit_activity_feed ) > 0 ) {
				$media_details = array_slice( $media_details, 0, $limit_activity_feed, true );
			}
			$rtmedia_activity_ul_class = apply_filters( 'rtmedia_' . $type . '_ul_class', 'rtm-activity-media-list' );

			$media_content = '';
			$count         = 0;
			$count_attr    = count( $media_details );

			foreach ( $media_details as $media ) {
				$add_class         = '';
				$remain_count_span = '';
				if ( 4 == $count && $count_attr > 5 ) {
					$add_class         = 'rtm-media-plus4';
					$remain_count      = $count_attr - 5;
					$remain_count_span = '<div class="rtmedia-remain-count">+' . $remain_count . '</div>';
				}

				if ( $count > 4 ) {
					$add_class = 'rtm-media-after4';
				}

				$media_content .= sprintf( '<li class="rtmedia-list-item media-type-%1s %2s">', esc_attr( $media->media_type ), esc_attr( $add_class ) );

				if ( 'photo' === $media->media_type ) {
					// Markup for photo media type with anchor tag only on image.
					$media_content .= sprintf(
						'<a href ="%s">
						<div class="rtmedia-item-thumbnail">
						%s
						</div>
						<div class="rtmedia-item-title">
						<h4 title="%s">
						%s
						</h4>
						</div>
						%s
						</a>',
						esc_url( get_rtmedia_permalink( $media->id ) ),
						$this->media( $media ),
						esc_attr( $media->media_title ),
						$media->media_title,
						$remain_count_span
					);
				} elseif ( 'music' === $media->media_type || 'video' === $media->media_type ) {
					// Markup for audio and video media type with link only on media (title).
					$media_content .= sprintf(
						'<div class="rtmedia-item-thumbnail">
						%s
						</div>
						<div class="rtmedia-item-title">
						<h4 title="%s">
						<a href="%s">
						%s
						</a>
						</h4>
						</div>',
						$this->media( $media ),
						esc_attr( $media->media_title ),
						esc_url( get_rtmedia_permalink( $media->id ) ),
						esc_html( $media->media_title )
					);
				} else {
					// Markup for all the other media linke docs and other files where anchor tag the markup is comming from add-on itself.
					$media_content .= sprintf(
						'<div class="rtmedia-item-thumbnail">
						%s
						</div>
						<div class="rtmedia-item-title">
						<h4 title="%s">
						%s
						</h4>
						</div>',
						$this->media( $media ),
						esc_attr( $media->media_title ),
						esc_html( $media->media_title )
					);
				}

				$media_content .= '</li>';
				++$count;
			}

			$media_container_start_class = 'rtmedia-list';
			if ( 'activity' !== $type ) {
				$media_container_start_class = sprintf( 'rtmedia-%s-list', $type );
			}

			$media_container_start = sprintf(
				'<ul class="%s %s rtmedia-activity-media-length-%s">',
				esc_attr( $media_container_start_class ),
				esc_attr( $rtmedia_activity_ul_class ),
				esc_attr( $count )
			);

			$media_container_end = '</ul>';

			$media_list  = $media_container_start;
			$media_list .= $media_content;
			$media_list .= $media_container_end;

			/**
			 * Filters the output of the activity contents before save.
			 *
			 * @param string $activity_content Concatination of $activity_text and $media_list.
			 * @param string $activity_text    HTML markup of activity text.
			 * @param string $media_list       HTML markup of media in ul.
			 */
			$activity_content = apply_filters( 'rtmedia_activity_content_html', $media_list . $activity_text, $activity_text, $media_list );

			$activity  = $activity_container_start;
			$activity .= $activity_content;
			$activity .= $activity_container_end;

			// Bypass comment links limit.
			add_filter(
				'option_comment_max_links',
				function ( $values ) {
					$rtmedia_attached_files = filter_input( INPUT_POST, 'rtMedia_attached_files', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
					// Check  if files available.
					if ( is_array( $rtmedia_attached_files ) && ! empty( $rtmedia_attached_files[0] ) ) {
						// One url of image and other for anchor tag.
						$values = count( $rtmedia_attached_files ) * 3;
					}
					return $values;
				}
			);

			return bp_activity_filter_kses( $activity );
		}
	}

}

global $rtmedia_buddypress_activity;
remove_filter( 'bp_activity_content_before_save', array( $rtmedia_buddypress_activity, 'bp_activity_content_before_save' ) );
add_filter( 'bp_activity_content_before_save', 'rtm_rtmedia_bp_activity_content_before_save' );

/**
 * This function will check for the media file attached to the activity and accordingly will set content.
 *
 * @param string $content Content of the Activity.
 *
 * @return string Filtered value of the activity content.
 */
function rtm_rtmedia_bp_activity_content_before_save( $content ) {

	$rtmedia_attached_files = filter_input( INPUT_POST, 'rtMedia_attached_files', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
	if ( ( ! empty( $rtmedia_attached_files ) ) && is_array( $rtmedia_attached_files ) ) {
		$obj_activity = new reign_RTMediaActivity( $rtmedia_attached_files, 0, $content );

		// Remove action to fix duplication issue of comment content.
		remove_action( 'bp_activity_content_before_save', 'rtmedia_bp_activity_comment_content_callback', 1001, 1 );

		$content = $obj_activity->create_activity_html();
	}
	return $content;
}

/**
 * Customizes the HTML output for rtMedia photos based on specific criteria.
 *
 * This function modifies the HTML of rtMedia media items if certain conditions are met:
 * - The custom filter ('reign_rtmedia_filter_enabled') must be enabled.
 * - The media type must be 'photo'.
 * - The context must be either the activity component or a group context within BuddyPress.
 *
 * When these conditions are met, the function replaces the default image URL with a custom-sized image URL.
 *
 * @param string $html  The original HTML output for the media item.
 * @param object $media The media object containing details about the media item.
 *
 * @return string The modified HTML output if conditions are met, otherwise the original HTML.
 */
function reign_custom_rtmedia_filter( $html, $media ) {
	// Apply filter to check if the custom filter is enabled.
	if ( ! apply_filters( 'reign_rtmedia_filter_enabled', true ) ) {
		return $html;
	}

	// Check if the media type is 'photo' and the context is correct.
	if ( $media->media_type === 'photo' && ( bp_is_activity_component() || ( function_exists( 'bp_is_group' ) && bp_is_group() ) ) ) {
		$image_src = wp_get_attachment_image_src( $media->media_id, 'custom_rtmedia_size' );

		// Replace image URL if valid.
		if ( ! empty( $image_src[0] ) ) {
			$html = preg_replace( '/src="[^"]*"/', 'src="' . esc_url( $image_src[0] ) . '"', $html );
		}
	}

	return $html;
}

add_filter( 'rtmedia_single_activity_filter', 'reign_custom_rtmedia_filter', 10, 2 );


/**
 * Determines whether to truncate the content of an activity entry in BuddyPress.
 *
 * This function checks the global `$activities_template` object to determine if the current activity
 * entry's content should be truncated. If the activity content contains the keyword 'photo',
 * it indicates that the activity is related to a photo, and the function returns `false` to prevent truncation.
 * Otherwise, it returns the original value of `$maybe_truncate`.
 *
 * @param bool $maybe_truncate Whether to truncate the activity entry. This value is passed from a filter hook.
 *
 * @return bool `false` if the activity is related to a photo and should not be truncated; otherwise, the original `$maybe_truncate` value.
 */
function reign_bp_activity_maybe_truncate_entry( $maybe_truncate ) {
	global $activities_template;

	// Ensure we have activity content to work with.
	if ( ! empty( $activities_template->activity->content ) ) {
		// Check if the activity content or metadata indicates it's a photo activity.
		if ( strpos( $activities_template->activity->content, 'photo' ) !== false ) {
			return false;
		}
	}

	// Default behavior for activities not containing photos.
	return $maybe_truncate;
}

add_filter( 'bp_activity_maybe_truncate_entry', 'reign_bp_activity_maybe_truncate_entry' );

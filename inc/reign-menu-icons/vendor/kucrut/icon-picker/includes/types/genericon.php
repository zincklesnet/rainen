<?php
/**
 * Genericons
 *
 * @package Icon_Picker
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */
class Icon_Picker_Type_Genericons extends Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $id = 'genericon';

	/**
	 * Icon type name
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'Genericons';

	/**
	 * Icon type version
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '3.4';

	/**
	 * Stylesheet ID
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $stylesheet_id = 'genericons';


	/**
	 * Get icon groups
	 *
	 * @since Menu Icons 0.1.0
	 * @return array
	 */
	public function get_groups() {
		$groups = array(
			array(
				'id'   => 'actions',
				'name' => __( 'Actions', 'reign' ),
			),
			array(
				'id'   => 'media-player',
				'name' => __( 'Media Player', 'reign' ),
			),
			array(
				'id'   => 'meta',
				'name' => __( 'Meta', 'reign' ),
			),
			array(
				'id'   => 'misc',
				'name' => __( 'Misc.', 'reign' ),
			),
			array(
				'id'   => 'places',
				'name' => __( 'Places', 'reign' ),
			),
			array(
				'id'   => 'post-formats',
				'name' => __( 'Post Formats', 'reign' ),
			),
			array(
				'id'   => 'text-editor',
				'name' => __( 'Text Editor', 'reign' ),
			),
			array(
				'id'   => 'social',
				'name' => __( 'Social', 'reign' ),
			),
		);

		/**
		 * Filter genericon groups
		 *
		 * @since 0.1.0
		 * @param array $groups Icon groups.
		 */
		$groups = apply_filters( 'icon_picker_genericon_groups', $groups );

		return $groups;
	}


	/**
	 * Get icon names
	 *
	 * @since Menu Icons 0.1.0
	 * @return array
	 */
	public function get_items() {
		$items = array(
			array(
				'group' => 'actions',
				'id'    => 'genericon-checkmark',
				'name'  => __( 'Checkmark', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-close',
				'name'  => __( 'Close', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-close-alt',
				'name'  => __( 'Close', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-dropdown',
				'name'  => __( 'Dropdown', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-dropdown-left',
				'name'  => __( 'Dropdown left', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-collapse',
				'name'  => __( 'Collapse', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-expand',
				'name'  => __( 'Expand', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-help',
				'name'  => __( 'Help', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-info',
				'name'  => __( 'Info', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-lock',
				'name'  => __( 'Lock', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-maximize',
				'name'  => __( 'Maximize', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-minimize',
				'name'  => __( 'Minimize', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-plus',
				'name'  => __( 'Plus', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-minus',
				'name'  => __( 'Minus', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-previous',
				'name'  => __( 'Previous', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-next',
				'name'  => __( 'Next', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-move',
				'name'  => __( 'Move', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-hide',
				'name'  => __( 'Hide', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-show',
				'name'  => __( 'Show', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-print',
				'name'  => __( 'Print', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-rating-empty',
				'name'  => __( 'Rating: Empty', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-rating-half',
				'name'  => __( 'Rating: Half', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-rating-full',
				'name'  => __( 'Rating: Full', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-refresh',
				'name'  => __( 'Refresh', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-reply',
				'name'  => __( 'Reply', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-reply-alt',
				'name'  => __( 'Reply alt', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-reply-single',
				'name'  => __( 'Reply single', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-search',
				'name'  => __( 'Search', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-send-to-phone',
				'name'  => __( 'Send to', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-send-to-tablet',
				'name'  => __( 'Send to', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-share',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-shuffle',
				'name'  => __( 'Shuffle', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-spam',
				'name'  => __( 'Spam', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-subscribe',
				'name'  => __( 'Subscribe', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-subscribed',
				'name'  => __( 'Subscribed', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-unsubscribe',
				'name'  => __( 'Unsubscribe', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-top',
				'name'  => __( 'Top', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-unapprove',
				'name'  => __( 'Unapprove', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-zoom',
				'name'  => __( 'Zoom', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-unzoom',
				'name'  => __( 'Unzoom', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'genericon-xpost',
				'name'  => __( 'X-Post', 'reign' ),
			),
			array(
				'group' => 'media-player',
				'id'    => 'genericon-skip-back',
				'name'  => __( 'Skip back', 'reign' ),
			),
			array(
				'group' => 'media-player',
				'id'    => 'genericon-rewind',
				'name'  => __( 'Rewind', 'reign' ),
			),
			array(
				'group' => 'media-player',
				'id'    => 'genericon-play',
				'name'  => __( 'Play', 'reign' ),
			),
			array(
				'group' => 'media-player',
				'id'    => 'genericon-pause',
				'name'  => __( 'Pause', 'reign' ),
			),
			array(
				'group' => 'media-player',
				'id'    => 'genericon-stop',
				'name'  => __( 'Stop', 'reign' ),
			),
			array(
				'group' => 'media-player',
				'id'    => 'genericon-fastforward',
				'name'  => __( 'Fast Forward', 'reign' ),
			),
			array(
				'group' => 'media-player',
				'id'    => 'genericon-skip-ahead',
				'name'  => __( 'Skip ahead', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-comment',
				'name'  => __( 'Comment', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-category',
				'name'  => __( 'Category', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-hierarchy',
				'name'  => __( 'Hierarchy', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-tag',
				'name'  => __( 'Tag', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-time',
				'name'  => __( 'Time', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-user',
				'name'  => __( 'User', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-day',
				'name'  => __( 'Day', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-week',
				'name'  => __( 'Week', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-month',
				'name'  => __( 'Month', 'reign' ),
			),
			array(
				'group' => 'meta',
				'id'    => 'genericon-pinned',
				'name'  => __( 'Pinned', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-uparrow',
				'name'  => __( 'Arrow Up', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-downarrow',
				'name'  => __( 'Arrow Down', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-leftarrow',
				'name'  => __( 'Arrow Left', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-rightarrow',
				'name'  => __( 'Arrow Right', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-activity',
				'name'  => __( 'Activity', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-bug',
				'name'  => __( 'Bug', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-book',
				'name'  => __( 'Book', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-cart',
				'name'  => __( 'Cart', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-cloud-download',
				'name'  => __( 'Cloud Download', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-cloud-upload',
				'name'  => __( 'Cloud Upload', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-cog',
				'name'  => __( 'Cog', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-document',
				'name'  => __( 'Document', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-dot',
				'name'  => __( 'Dot', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-download',
				'name'  => __( 'Download', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-draggable',
				'name'  => __( 'Draggable', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-ellipsis',
				'name'  => __( 'Ellipsis', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-external',
				'name'  => __( 'External', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-feed',
				'name'  => __( 'Feed', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-flag',
				'name'  => __( 'Flag', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-fullscreen',
				'name'  => __( 'Fullscreen', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-handset',
				'name'  => __( 'Handset', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-heart',
				'name'  => __( 'Heart', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-key',
				'name'  => __( 'Key', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-mail',
				'name'  => __( 'Mail', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-menu',
				'name'  => __( 'Menu', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-microphone',
				'name'  => __( 'Microphone', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-notice',
				'name'  => __( 'Notice', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-paintbrush',
				'name'  => __( 'Paint Brush', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-phone',
				'name'  => __( 'Phone', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-picture',
				'name'  => __( 'Picture', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-plugin',
				'name'  => __( 'Plugin', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-portfolio',
				'name'  => __( 'Portfolio', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-star',
				'name'  => __( 'Star', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-summary',
				'name'  => __( 'Summary', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-tablet',
				'name'  => __( 'Tablet', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-videocamera',
				'name'  => __( 'Video Camera', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'genericon-warning',
				'name'  => __( 'Warning', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'genericon-404',
				'name'  => __( '404', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'genericon-trash',
				'name'  => __( 'Trash', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'genericon-cloud',
				'name'  => __( 'Cloud', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'genericon-home',
				'name'  => __( 'Home', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'genericon-location',
				'name'  => __( 'Location', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'genericon-sitemap',
				'name'  => __( 'Sitemap', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'genericon-website',
				'name'  => __( 'Website', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-standard',
				'name'  => __( 'Standard', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-aside',
				'name'  => __( 'Aside', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-image',
				'name'  => __( 'Image', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-gallery',
				'name'  => __( 'Gallery', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-video',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-status',
				'name'  => __( 'Status', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-quote',
				'name'  => __( 'Quote', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-link',
				'name'  => __( 'Link', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-chat',
				'name'  => __( 'Chat', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'genericon-audio',
				'name'  => __( 'Audio', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'genericon-anchor',
				'name'  => __( 'Anchor', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'genericon-attachment',
				'name'  => __( 'Attachment', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'genericon-edit',
				'name'  => __( 'Edit', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'genericon-code',
				'name'  => __( 'Code', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'genericon-bold',
				'name'  => __( 'Bold', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'genericon-italic',
				'name'  => __( 'Italic', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-codepen',
				'name'  => 'CodePen',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-digg',
				'name'  => 'Digg',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-dribbble',
				'name'  => 'Dribbble',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-dropbox',
				'name'  => 'DropBox',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-facebook',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-facebook-alt',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-flickr',
				'name'  => 'Flickr',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-foursquare',
				'name'  => 'Foursquare',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-github',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-googleplus',
				'name'  => 'Google+',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-googleplus-alt',
				'name'  => 'Google+',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-instagram',
				'name'  => 'Instagram',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-linkedin',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-linkedin-alt',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-path',
				'name'  => 'Path',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-pinterest',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-pinterest-alt',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-pocket',
				'name'  => 'Pocket',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-polldaddy',
				'name'  => 'PollDaddy',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-reddit',
				'name'  => 'Reddit',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-skype',
				'name'  => 'Skype',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-spotify',
				'name'  => 'Spotify',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-stumbleupon',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-tumblr',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-twitch',
				'name'  => 'Twitch',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-twitter',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-vimeo',
				'name'  => 'Vimeo',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-wordpress',
				'name'  => 'WordPress',
			),
			array(
				'group' => 'social',
				'id'    => 'genericon-youtube',
				'name'  => 'Youtube',
			),
		);

		/**
		 * Filter genericon items
		 *
		 * @since 0.1.0
		 * @param array $items Icon names.
		 */
		$items = apply_filters( 'icon_picker_genericon_items', $items );

		return $items;
	}
}

<?php
/**
 * Elusive Icons
 *
 * @package Icon_Picker
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */
class Icon_Picker_Type_Elusive extends Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $id = 'elusive';

	/**
	 * Icon type name
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'Elusive';

	/**
	 * Icon type version
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '2.0';


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
				'id'   => 'currency',
				'name' => __( 'Currency', 'reign' ),
			),
			array(
				'id'   => 'media',
				'name' => __( 'Media', 'reign' ),
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
				'id'    => 'el-icon-adjust',
				'name'  => __( 'Adjust', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-adjust-alt',
				'name'  => __( 'Adjust', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-align-left',
				'name'  => __( 'Align Left', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-align-center',
				'name'  => __( 'Align Center', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-align-right',
				'name'  => __( 'Align Right', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-align-justify',
				'name'  => __( 'Justify', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-arrow-up',
				'name'  => __( 'Arrow Up', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-arrow-down',
				'name'  => __( 'Arrow Down', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-arrow-left',
				'name'  => __( 'Arrow Left', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-arrow-right',
				'name'  => __( 'Arrow Right', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-fast-backward',
				'name'  => __( 'Fast Backward', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-step-backward',
				'name'  => __( 'Step Backward', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-backward',
				'name'  => __( 'Backward', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-forward',
				'name'  => __( 'Forward', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-forward-alt',
				'name'  => __( 'Forward', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-step-forward',
				'name'  => __( 'Step Forward', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-fast-forward',
				'name'  => __( 'Fast Forward', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-bold',
				'name'  => __( 'Bold', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-italic',
				'name'  => __( 'Italic', 'reign' ),
			),
			array(
				'group' => 'c',
				'id'    => 'el-icon-link',
				'name'  => __( 'Link', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-caret-up',
				'name'  => __( 'Caret Up', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-caret-down',
				'name'  => __( 'Caret Down', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-caret-left',
				'name'  => __( 'Caret Left', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-caret-right',
				'name'  => __( 'Caret Right', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-check',
				'name'  => __( 'Check', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-check-empty',
				'name'  => __( 'Check Empty', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-chevron-up',
				'name'  => __( 'Chevron Up', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-chevron-down',
				'name'  => __( 'Chevron Down', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-chevron-left',
				'name'  => __( 'Chevron Left', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-chevron-right',
				'name'  => __( 'Chevron Right', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-circle-arrow-up',
				'name'  => __( 'Circle Arrow Up', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-circle-arrow-down',
				'name'  => __( 'Circle Arrow Down', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-circle-arrow-left',
				'name'  => __( 'Circle Arrow Left', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-circle-arrow-right',
				'name'  => __( 'Circle Arrow Right', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-download',
				'name'  => __( 'Download', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-download-alt',
				'name'  => __( 'Download', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-edit',
				'name'  => __( 'Edit', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-eject',
				'name'  => __( 'Eject', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-file-new',
				'name'  => __( 'File New', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-file-new-alt',
				'name'  => __( 'File New', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-file-edit',
				'name'  => __( 'File Edit', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-file-edit-alt',
				'name'  => __( 'File Edit', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-fork',
				'name'  => __( 'Fork', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-fullscreen',
				'name'  => __( 'Fullscreen', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-indent-left',
				'name'  => __( 'Indent Left', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-indent-right',
				'name'  => __( 'Indent Right', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-list',
				'name'  => __( 'List', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-list-alt',
				'name'  => __( 'List', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-lock',
				'name'  => __( 'Lock', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-lock-alt',
				'name'  => __( 'Lock', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-unlock',
				'name'  => __( 'Unlock', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-unlock-alt',
				'name'  => __( 'Unlock', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-map-marker',
				'name'  => __( 'Map Marker', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-map-marker-alt',
				'name'  => __( 'Map Marker', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-minus',
				'name'  => __( 'Minus', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-minus-sign',
				'name'  => __( 'Minus Sign', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-move',
				'name'  => __( 'Move', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-off',
				'name'  => __( 'Off', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-ok',
				'name'  => __( 'OK', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-ok-circle',
				'name'  => __( 'OK Circle', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-ok-sign',
				'name'  => __( 'OK Sign', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-play',
				'name'  => __( 'Play', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-play-alt',
				'name'  => __( 'Play', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-pause',
				'name'  => __( 'Pause', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-pause-alt',
				'name'  => __( 'Pause', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-stop',
				'name'  => __( 'Stop', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-stop-alt',
				'name'  => __( 'Stop', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-plus',
				'name'  => __( 'Plus', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-plus-sign',
				'name'  => __( 'Plus Sign', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-print',
				'name'  => __( 'Print', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-question',
				'name'  => __( 'Question', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-question-sign',
				'name'  => __( 'Question Sign', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-record',
				'name'  => __( 'Record', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-refresh',
				'name'  => __( 'Refresh', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-remove',
				'name'  => __( 'Remove', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-repeat',
				'name'  => __( 'Repeat', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-repeat-alt',
				'name'  => __( 'Repeat', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-resize-vertical',
				'name'  => __( 'Resize Vertical', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-resize-horizontal',
				'name'  => __( 'Resize Horizontal', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-resize-full',
				'name'  => __( 'Resize Full', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-resize-small',
				'name'  => __( 'Resize Small', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-return-key',
				'name'  => __( 'Return', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-retweet',
				'name'  => __( 'Retweet', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-reverse-alt',
				'name'  => __( 'Reverse', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-search',
				'name'  => __( 'Search', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-search-alt',
				'name'  => __( 'Search', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-share',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-share-alt',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-tag',
				'name'  => __( 'Tag', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-tasks',
				'name'  => __( 'Tasks', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-text-height',
				'name'  => __( 'Text Height', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-text-width',
				'name'  => __( 'Text Width', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-thumbs-up',
				'name'  => __( 'Thumbs Up', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-thumbs-down',
				'name'  => __( 'Thumbs Down', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-tint',
				'name'  => __( 'Tint', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-trash',
				'name'  => __( 'Trash', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-trash-alt',
				'name'  => __( 'Trash', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-upload',
				'name'  => __( 'Upload', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-view-mode',
				'name'  => __( 'View Mode', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-volume-up',
				'name'  => __( 'Volume Up', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-volume-down',
				'name'  => __( 'Volume Down', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-volume-off',
				'name'  => __( 'Mute', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-warning-sign',
				'name'  => __( 'Warning Sign', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-zoom-in',
				'name'  => __( 'Zoom In', 'reign' ),
			),
			array(
				'group' => 'actions',
				'id'    => 'el-icon-zoom-out',
				'name'  => __( 'Zoom Out', 'reign' ),
			),
			array(
				'group' => 'currency',
				'id'    => 'el-icon-eur',
				'name'  => 'EUR',
			),
			array(
				'group' => 'currency',
				'id'    => 'el-icon-gbp',
				'name'  => 'GBP',
			),
			array(
				'group' => 'currency',
				'id'    => 'el-icon-usd',
				'name'  => 'USD',
			),
			array(
				'group' => 'media',
				'id'    => 'el-icon-video',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'el-icon-video-alt',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-adult',
				'name'  => __( 'Adult', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-address-book',
				'name'  => __( 'Address Book', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-address-book-alt',
				'name'  => __( 'Address Book', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-asl',
				'name'  => __( 'ASL', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-asterisk',
				'name'  => __( 'Asterisk', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-ban-circle',
				'name'  => __( 'Ban Circle', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-barcode',
				'name'  => __( 'Barcode', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-bell',
				'name'  => __( 'Bell', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-blind',
				'name'  => __( 'Blind', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-book',
				'name'  => __( 'Book', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-braille',
				'name'  => __( 'Braille', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-briefcase',
				'name'  => __( 'Briefcase', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-broom',
				'name'  => __( 'Broom', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-brush',
				'name'  => __( 'Brush', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-bulb',
				'name'  => __( 'Bulb', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-bullhorn',
				'name'  => __( 'Bullhorn', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-calendar',
				'name'  => __( 'Calendar', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-calendar-sign',
				'name'  => __( 'Calendar Sign', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-camera',
				'name'  => __( 'Camera', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-car',
				'name'  => __( 'Car', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-cc',
				'name'  => __( 'CC', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-certificate',
				'name'  => __( 'Certificate', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-child',
				'name'  => __( 'Child', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-cog',
				'name'  => __( 'Cog', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-cog-alt',
				'name'  => __( 'Cog', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-cogs',
				'name'  => __( 'Cogs', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-comment',
				'name'  => __( 'Comment', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-comment-alt',
				'name'  => __( 'Comment', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-compass',
				'name'  => __( 'Compass', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-compass-alt',
				'name'  => __( 'Compass', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-credit-card',
				'name'  => __( 'Credit Card', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-css',
				'name'  => 'CSS',
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-envelope',
				'name'  => __( 'Envelope', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-envelope-alt',
				'name'  => __( 'Envelope', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-error',
				'name'  => __( 'Error', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-error-alt',
				'name'  => __( 'Error', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-exclamation-sign',
				'name'  => __( 'Exclamation Sign', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-eye-close',
				'name'  => __( 'Eye Close', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-eye-open',
				'name'  => __( 'Eye Open', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-male',
				'name'  => __( 'Male', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-female',
				'name'  => __( 'Female', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-file',
				'name'  => __( 'File', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-file-alt',
				'name'  => __( 'File', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-film',
				'name'  => __( 'Film', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-filter',
				'name'  => __( 'Filter', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-fire',
				'name'  => __( 'Fire', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-flag',
				'name'  => __( 'Flag', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-flag-alt',
				'name'  => __( 'Flag', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-folder',
				'name'  => __( 'Folder', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-folder-open',
				'name'  => __( 'Folder Open', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-folder-close',
				'name'  => __( 'Folder Close', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-folder-sign',
				'name'  => __( 'Folder Sign', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-font',
				'name'  => __( 'Font', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-fontsize',
				'name'  => __( 'Font Size', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-gift',
				'name'  => __( 'Gift', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-glass',
				'name'  => __( 'Glass', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-glasses',
				'name'  => __( 'Glasses', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-globe',
				'name'  => __( 'Globe', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-globe-alt',
				'name'  => __( 'Globe', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-graph',
				'name'  => __( 'Graph', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-graph-alt',
				'name'  => __( 'Graph', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-group',
				'name'  => __( 'Group', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-group-alt',
				'name'  => __( 'Group', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-guidedog',
				'name'  => __( 'Guide Dog', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hand-up',
				'name'  => __( 'Hand Up', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hand-down',
				'name'  => __( 'Hand Down', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hand-left',
				'name'  => __( 'Hand Left', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hand-right',
				'name'  => __( 'Hand Right', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hdd',
				'name'  => __( 'HDD', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-headphones',
				'name'  => __( 'Headphones', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hearing-impaired',
				'name'  => __( 'Hearing Impaired', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-heart',
				'name'  => __( 'Heart', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-heart-alt',
				'name'  => __( 'Heart', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-heart-empty',
				'name'  => __( 'Heart Empty', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-hourglass',
				'name'  => __( 'Hourglass', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-idea',
				'name'  => __( 'Idea', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-idea-alt',
				'name'  => __( 'Idea', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-inbox',
				'name'  => __( 'Inbox', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-inbox-alt',
				'name'  => __( 'Inbox', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-inbox-box',
				'name'  => __( 'Inbox', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-info-sign',
				'name'  => __( 'Info', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-key',
				'name'  => __( 'Key', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-laptop',
				'name'  => __( 'Laptop', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-laptop-alt',
				'name'  => __( 'Laptop', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-leaf',
				'name'  => __( 'Leaf', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-lines',
				'name'  => __( 'Lines', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-magic',
				'name'  => __( 'Magic', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-magnet',
				'name'  => __( 'Magnet', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-mic',
				'name'  => __( 'Mic', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-music',
				'name'  => __( 'Music', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-paper-clip',
				'name'  => __( 'Paper Clip', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-paper-clip-alt',
				'name'  => __( 'Paper Clip', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-pencil',
				'name'  => __( 'Pencil', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-pencil-alt',
				'name'  => __( 'Pencil', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-person',
				'name'  => __( 'Person', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-phone',
				'name'  => __( 'Phone', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-phone-alt',
				'name'  => __( 'Phone', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-photo',
				'name'  => __( 'Photo', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-photo-alt',
				'name'  => __( 'Photo', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-picture',
				'name'  => __( 'Picture', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-plane',
				'name'  => __( 'Plane', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-podcast',
				'name'  => __( 'Podcast', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-puzzle',
				'name'  => __( 'Puzzle', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-qrcode',
				'name'  => __( 'QR Code', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-quotes',
				'name'  => __( 'Quotes', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-quotes-alt',
				'name'  => __( 'Quotes', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-random',
				'name'  => __( 'Random', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-scissors',
				'name'  => __( 'Scissors', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-screen',
				'name'  => __( 'Screen', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-screen-alt',
				'name'  => __( 'Screen', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-screenshot',
				'name'  => __( 'Screenshot', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-shopping-cart',
				'name'  => __( 'Shopping Cart', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-shopping-cart-sign',
				'name'  => __( 'Shopping Cart Sign', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-signal',
				'name'  => __( 'Signal', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-smiley',
				'name'  => __( 'Smiley', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-smiley-alt',
				'name'  => __( 'Smiley', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-speaker',
				'name'  => __( 'Speaker', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-user',
				'name'  => __( 'User', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-th',
				'name'  => __( 'Thumbnails', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-th-large',
				'name'  => __( 'Thumbnails (Large)', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-th-list',
				'name'  => __( 'Thumbnails (List)', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-time',
				'name'  => __( 'Time', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-time-alt',
				'name'  => __( 'Time', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-torso',
				'name'  => __( 'Torso', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-wheelchair',
				'name'  => __( 'Wheelchair', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-wrench',
				'name'  => __( 'Wrench', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-wrench-alt',
				'name'  => __( 'Wrench', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'el-icon-universal-access',
				'name'  => __( 'Universal Access', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-bookmark',
				'name'  => __( 'Bookmark', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-bookmark-empty',
				'name'  => __( 'Bookmark Empty', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-dashboard',
				'name'  => __( 'Dashboard', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-home',
				'name'  => __( 'Home', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-home-alt',
				'name'  => __( 'Home', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-iphone-home',
				'name'  => __( 'Home (iPhone)', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-network',
				'name'  => __( 'Network', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-tags',
				'name'  => __( 'Tags', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-website',
				'name'  => __( 'Website', 'reign' ),
			),
			array(
				'group' => 'places',
				'id'    => 'el-icon-website-alt',
				'name'  => __( 'Website', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-behance',
				'name'  => 'Behance',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-blogger',
				'name'  => 'Blogger',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-cloud',
				'name'  => __( 'Cloud', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-cloud-alt',
				'name'  => __( 'Cloud', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-delicious',
				'name'  => 'Delicious',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-deviantart',
				'name'  => 'DeviantArt',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-digg',
				'name'  => 'Digg',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-dribbble',
				'name'  => 'Dribbble',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-facebook',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-facetime-video',
				'name'  => 'Facetime Video',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-flickr',
				'name'  => 'Flickr',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-foursquare',
				'name'  => 'Foursquare',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-friendfeed',
				'name'  => 'FriendFeed',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-friendfeed-rect',
				'name'  => 'FriendFeed',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-github',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-github-text',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-googleplus',
				'name'  => 'Google+',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-instagram',
				'name'  => 'Instagram',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-lastfm',
				'name'  => 'Last.fm',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-linkedin',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-livejournal',
				'name'  => 'LiveJournal',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-myspace',
				'name'  => 'MySpace',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-opensource',
				'name'  => __( 'Open Source', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-path',
				'name'  => 'path',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-picasa',
				'name'  => 'Picasa',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-pinterest',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-rss',
				'name'  => 'RSS',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-reddit',
				'name'  => 'Reddit',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-skype',
				'name'  => 'Skype',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-slideshare',
				'name'  => 'Slideshare',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-soundcloud',
				'name'  => 'SoundCloud',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-spotify',
				'name'  => 'Spotify',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-stackoverflow',
				'name'  => 'Stack Overflow',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-stumbleupon',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-twitter',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-tumblr',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-viadeo',
				'name'  => 'Viadeo',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-vimeo',
				'name'  => 'Vimeo',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-vkontakte',
				'name'  => 'VKontakte',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-w3c',
				'name'  => 'W3C',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-wordpress',
				'name'  => 'WordPress',
			),
			array(
				'group' => 'social',
				'id'    => 'el-icon-youtube',
				'name'  => 'YouTube',
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

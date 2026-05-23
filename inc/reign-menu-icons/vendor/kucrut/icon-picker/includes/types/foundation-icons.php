<?php
/**
 * Foundation Icons
 *
 * @package Icon_Picker
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */
class Icon_Picker_Type_Foundation extends Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $id = 'foundation-icons';

	/**
	 * Icon type name
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'Foundation';

	/**
	 * Icon type version
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '3.0';


	/**
	 * Get icon groups
	 *
	 * @since Menu Icons 0.1.0
	 * @return array
	 */
	public function get_groups() {
		$groups = array(
			array(
				'id'   => 'accessibility',
				'name' => __( 'Accessibility', 'reign' ),
			),
			array(
				'id'   => 'arrows',
				'name' => __( 'Arrows', 'reign' ),
			),
			array(
				'id'   => 'devices',
				'name' => __( 'Devices', 'reign' ),
			),
			array(
				'id'   => 'ecommerce',
				'name' => __( 'Ecommerce', 'reign' ),
			),
			array(
				'id'   => 'editor',
				'name' => __( 'Editor', 'reign' ),
			),
			array(
				'id'   => 'file-types',
				'name' => __( 'File Types', 'reign' ),
			),
			array(
				'id'   => 'general',
				'name' => __( 'General', 'reign' ),
			),
			array(
				'id'   => 'media-control',
				'name' => __( 'Media Controls', 'reign' ),
			),
			array(
				'id'   => 'misc',
				'name' => __( 'Miscellaneous', 'reign' ),
			),
			array(
				'id'   => 'people',
				'name' => __( 'People', 'reign' ),
			),
			array(
				'id'   => 'social',
				'name' => __( 'Social/Brand', 'reign' ),
			),
		);
		/**
		 * Filter genericon groups
		 *
		 * @since 0.1.0
		 * @param array $groups Icon groups.
		 */
		$groups = apply_filters( 'icon_picker_foundations_groups', $groups );

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
				'group' => 'accessibility',
				'id'    => 'fi-asl',
				'name'  => __( 'ASL', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-blind',
				'name'  => __( 'Blind', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-braille',
				'name'  => __( 'Braille', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-closed-caption',
				'name'  => __( 'Closed Caption', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-elevator',
				'name'  => __( 'Elevator', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-guide-dog',
				'name'  => __( 'Guide Dog', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-hearing-aid',
				'name'  => __( 'Hearing Aid', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-universal-access',
				'name'  => __( 'Universal Access', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-male',
				'name'  => __( 'Male', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-female',
				'name'  => __( 'Female', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-male-female',
				'name'  => __( 'Male & Female', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-male-symbol',
				'name'  => __( 'Male Symbol', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-female-symbol',
				'name'  => __( 'Female Symbol', 'reign' ),
			),
			array(
				'group' => 'accessibility',
				'id'    => 'fi-wheelchair',
				'name'  => __( 'Wheelchair', 'reign' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrow-up',
				'name'  => __( 'Arrow: Up', 'reign' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrow-down',
				'name'  => __( 'Arrow: Down', 'reign' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrow-left',
				'name'  => __( 'Arrow: Left', 'reign' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrow-right',
				'name'  => __( 'Arrow: Right', 'reign' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrows-out',
				'name'  => __( 'Arrows: Out', 'reign' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrows-in',
				'name'  => __( 'Arrows: In', 'reign' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrows-expand',
				'name'  => __( 'Arrows: Expand', 'reign' ),
			),
			array(
				'group' => 'arrows',
				'id'    => 'fi-arrows-compress',
				'name'  => __( 'Arrows: Compress', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-bluetooth',
				'name'  => __( 'Bluetooth', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-camera',
				'name'  => __( 'Camera', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-compass',
				'name'  => __( 'Compass', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-laptop',
				'name'  => __( 'Laptop', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-megaphone',
				'name'  => __( 'Megaphone', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-microphone',
				'name'  => __( 'Microphone', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-mobile',
				'name'  => __( 'Mobile', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-mobile-signal',
				'name'  => __( 'Mobile Signal', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-monitor',
				'name'  => __( 'Monitor', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-tablet-portrait',
				'name'  => __( 'Tablet: Portrait', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-tablet-landscape',
				'name'  => __( 'Tablet: Landscape', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-telephone',
				'name'  => __( 'Telephone', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-usb',
				'name'  => __( 'USB', 'reign' ),
			),
			array(
				'group' => 'devices',
				'id'    => 'fi-video',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-bitcoin',
				'name'  => __( 'Bitcoin', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-bitcoin-circle',
				'name'  => __( 'Bitcoin', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-dollar',
				'name'  => __( 'Dollar', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-euro',
				'name'  => __( 'EURO', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-pound',
				'name'  => __( 'Pound', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-yen',
				'name'  => __( 'Yen', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-burst',
				'name'  => __( 'Burst', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-burst-new',
				'name'  => __( 'Burst: New', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-burst-sale',
				'name'  => __( 'Burst: Sale', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-credit-card',
				'name'  => __( 'Credit Card', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-dollar-bill',
				'name'  => __( 'Dollar Bill', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-paypal',
				'name'  => 'PayPal',
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-price-tag',
				'name'  => __( 'Price Tag', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-pricetag-multiple',
				'name'  => __( 'Price Tag: Multiple', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-shopping-bag',
				'name'  => __( 'Shopping Bag', 'reign' ),
			),
			array(
				'group' => 'ecommerce',
				'id'    => 'fi-shopping-cart',
				'name'  => __( 'Shopping Cart', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-bold',
				'name'  => __( 'Bold', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-italic',
				'name'  => __( 'Italic', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-underline',
				'name'  => __( 'Underline', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-strikethrough',
				'name'  => __( 'Strikethrough', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-text-color',
				'name'  => __( 'Text Color', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-background-color',
				'name'  => __( 'Background Color', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-superscript',
				'name'  => __( 'Superscript', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-subscript',
				'name'  => __( 'Subscript', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-align-left',
				'name'  => __( 'Align Left', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-align-center',
				'name'  => __( 'Align Center', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-align-right',
				'name'  => __( 'Align Right', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-align-justify',
				'name'  => __( 'Justify', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-list-number',
				'name'  => __( 'List: Number', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-list-bullet',
				'name'  => __( 'List: Bullet', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-indent-more',
				'name'  => __( 'Indent', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-indent-less',
				'name'  => __( 'Outdent', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-add',
				'name'  => __( 'Add Page', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-copy',
				'name'  => __( 'Copy Page', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-multiple',
				'name'  => __( 'Duplicate Page', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-delete',
				'name'  => __( 'Delete Page', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-remove',
				'name'  => __( 'Remove Page', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-edit',
				'name'  => __( 'Edit Page', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-export',
				'name'  => __( 'Export', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-export-csv',
				'name'  => __( 'Export to CSV', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-export-pdf',
				'name'  => __( 'Export to PDF', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-filled',
				'name'  => __( 'Fill Page', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-crop',
				'name'  => __( 'Crop', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-filter',
				'name'  => __( 'Filter', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-paint-bucket',
				'name'  => __( 'Paint Bucket', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-photo',
				'name'  => __( 'Photo', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-print',
				'name'  => __( 'Print', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-save',
				'name'  => __( 'Save', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-link',
				'name'  => __( 'Link', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-unlink',
				'name'  => __( 'Unlink', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-quote',
				'name'  => __( 'Quote', 'reign' ),
			),
			array(
				'group' => 'editor',
				'id'    => 'fi-page-search',
				'name'  => __( 'Search in Page', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fi-page',
				'name'  => __( 'File', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fi-page-csv',
				'name'  => __( 'CSV', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fi-page-doc',
				'name'  => __( 'Doc', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fi-page-pdf',
				'name'  => __( 'PDF', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-address-book',
				'name'  => __( 'Addressbook', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-alert',
				'name'  => __( 'Alert', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-annotate',
				'name'  => __( 'Annotate', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-archive',
				'name'  => __( 'Archive', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-bookmark',
				'name'  => __( 'Bookmark', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-calendar',
				'name'  => __( 'Calendar', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-clock',
				'name'  => __( 'Clock', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-cloud',
				'name'  => __( 'Cloud', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comment',
				'name'  => __( 'Comment', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comment-minus',
				'name'  => __( 'Comment: Minus', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comment-quotes',
				'name'  => __( 'Comment: Quotes', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comment-video',
				'name'  => __( 'Comment: Video', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-comments',
				'name'  => __( 'Comments', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-contrast',
				'name'  => __( 'Contrast', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-database',
				'name'  => __( 'Database', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-folder',
				'name'  => __( 'Folder', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-folder-add',
				'name'  => __( 'Folder: Add', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-folder-lock',
				'name'  => __( 'Folder: Lock', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-eye',
				'name'  => __( 'Eye', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-heart',
				'name'  => __( 'Heart', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-plus',
				'name'  => __( 'Plus', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-minus',
				'name'  => __( 'Minus', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-minus-circle',
				'name'  => __( 'Minus', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-x',
				'name'  => __( 'X', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-x-circle',
				'name'  => __( 'X', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-check',
				'name'  => __( 'Check', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-checkbox',
				'name'  => __( 'Check', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-download',
				'name'  => __( 'Download', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-upload',
				'name'  => __( 'Upload', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-upload-cloud',
				'name'  => __( 'Upload to Cloud', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-flag',
				'name'  => __( 'Flag', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-foundation',
				'name'  => __( 'Foundation', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-graph-bar',
				'name'  => __( 'Graph: Bar', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-graph-horizontal',
				'name'  => __( 'Graph: Horizontal', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-graph-pie',
				'name'  => __( 'Graph: Pie', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-graph-trend',
				'name'  => __( 'Graph: Trend', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-home',
				'name'  => __( 'Home', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-layout',
				'name'  => __( 'Layout', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-like',
				'name'  => __( 'Like', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-dislike',
				'name'  => __( 'Dislike', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-list',
				'name'  => __( 'List', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-list-thumbnails',
				'name'  => __( 'List: Thumbnails', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-lock',
				'name'  => __( 'Lock', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-unlock',
				'name'  => __( 'Unlock', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-marker',
				'name'  => __( 'Marker', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-magnifying-glass',
				'name'  => __( 'Magnifying Glass', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-refresh',
				'name'  => __( 'Refresh', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-paperclip',
				'name'  => __( 'Paperclip', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-pencil',
				'name'  => __( 'Pencil', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-play-video',
				'name'  => __( 'Play Video', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-results',
				'name'  => __( 'Results', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-results-demographics',
				'name'  => __( 'Results: Demographics', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-rss',
				'name'  => __( 'RSS', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-share',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-sound',
				'name'  => __( 'Sound', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-star',
				'name'  => __( 'Star', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-thumbnails',
				'name'  => __( 'Thumbnails', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-trash',
				'name'  => __( 'Trash', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-web',
				'name'  => __( 'Web', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-widget',
				'name'  => __( 'Widget', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-wrench',
				'name'  => __( 'Wrench', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-zoom-out',
				'name'  => __( 'Zoom Out', 'reign' ),
			),
			array(
				'group' => 'general',
				'id'    => 'fi-zoom-in',
				'name'  => __( 'Zoom In', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-record',
				'name'  => __( 'Record', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-play-circle',
				'name'  => __( 'Play', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-play',
				'name'  => __( 'Play', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-pause',
				'name'  => __( 'Pause', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-stop',
				'name'  => __( 'Stop', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-previous',
				'name'  => __( 'Previous', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-rewind',
				'name'  => __( 'Rewind', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-fast-forward',
				'name'  => __( 'Fast Forward', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-next',
				'name'  => __( 'Next', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-volume',
				'name'  => __( 'Volume', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-volume-none',
				'name'  => __( 'Volume: Low', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-volume-strike',
				'name'  => __( 'Volume: Mute', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-loop',
				'name'  => __( 'Loop', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-shuffle',
				'name'  => __( 'Shuffle', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-eject',
				'name'  => __( 'Eject', 'reign' ),
			),
			array(
				'group' => 'media-control',
				'id'    => 'fi-rewind-ten',
				'name'  => __( 'Rewind 10', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-anchor',
				'name'  => __( 'Anchor', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-asterisk',
				'name'  => __( 'Asterisk', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-at-sign',
				'name'  => __( '@', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-battery-full',
				'name'  => __( 'Battery: Full', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-battery-half',
				'name'  => __( 'Battery: Half', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-battery-empty',
				'name'  => __( 'Battery: Empty', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-book',
				'name'  => __( 'Book', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-book-bookmark',
				'name'  => __( 'Bookmark', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-clipboard',
				'name'  => __( 'Clipboard', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-clipboard-pencil',
				'name'  => __( 'Clipboard: Pencil', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-clipboard-notes',
				'name'  => __( 'Clipboard: Notes', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-crown',
				'name'  => __( 'Crown', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-one',
				'name'  => __( 'Dice: 1', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-two',
				'name'  => __( 'Dice: 2', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-three',
				'name'  => __( 'Dice: 3', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-four',
				'name'  => __( 'Dice: 4', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-five',
				'name'  => __( 'Dice: 5', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-die-six',
				'name'  => __( 'Dice: 6', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-safety-cone',
				'name'  => __( 'Cone', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-first-aid',
				'name'  => __( 'Firs Aid', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-foot',
				'name'  => __( 'Foot', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-info',
				'name'  => __( 'Info', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-key',
				'name'  => __( 'Key', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-lightbulb',
				'name'  => __( 'Lightbulb', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-map',
				'name'  => __( 'Map', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-mountains',
				'name'  => __( 'Mountains', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-music',
				'name'  => __( 'Music', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-no-dogs',
				'name'  => __( 'No Dogs', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-no-smoking',
				'name'  => __( 'No Smoking', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-paw',
				'name'  => __( 'Paw', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-power',
				'name'  => __( 'Power', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-prohibited',
				'name'  => __( 'Prohibited', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-projection-screen',
				'name'  => __( 'Projection Screen', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-puzzle',
				'name'  => __( 'Puzzle', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-sheriff-badge',
				'name'  => __( 'Sheriff Badge', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-shield',
				'name'  => __( 'Shield', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-skull',
				'name'  => __( 'Skull', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-target',
				'name'  => __( 'Target', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-target-two',
				'name'  => __( 'Target', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-ticket',
				'name'  => __( 'Ticket', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-trees',
				'name'  => __( 'Trees', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fi-trophy',
				'name'  => __( 'Trophy', 'reign' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torso',
				'name'  => __( 'Torso', 'reign' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torso-business',
				'name'  => __( 'Torso: Business', 'reign' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torso-female',
				'name'  => __( 'Torso: Female', 'reign' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos',
				'name'  => __( 'Torsos', 'reign' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos-all',
				'name'  => __( 'Torsos: All', 'reign' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos-all-female',
				'name'  => __( 'Torsos: All Female', 'reign' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos-male-female',
				'name'  => __( 'Torsos: Male & Female', 'reign' ),
			),
			array(
				'group' => 'people',
				'id'    => 'fi-torsos-female-male',
				'name'  => __( 'Torsos: Female & Male', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-500px',
				'name'  => '500px',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-adobe',
				'name'  => 'Adobe',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-amazon',
				'name'  => 'Amazon',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-android',
				'name'  => 'Android',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-apple',
				'name'  => 'Apple',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-behance',
				'name'  => 'Behance',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-bing',
				'name'  => 'bing',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-blogger',
				'name'  => 'Blogger',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-css3',
				'name'  => 'CSS3',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-delicious',
				'name'  => 'Delicious',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-designer-news',
				'name'  => 'Designer News',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-deviant-art',
				'name'  => 'deviantArt',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-digg',
				'name'  => 'Digg',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-dribbble',
				'name'  => 'dribbble',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-drive',
				'name'  => 'Drive',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-dropbox',
				'name'  => 'DropBox',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-evernote',
				'name'  => 'Evernote',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-facebook',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-flickr',
				'name'  => 'flickr',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-forrst',
				'name'  => 'forrst',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-foursquare',
				'name'  => 'Foursquare',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-game-center',
				'name'  => 'Game Center',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-github',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-google-plus',
				'name'  => 'Google+',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-hacker-news',
				'name'  => 'Hacker News',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-hi5',
				'name'  => 'hi5',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-html5',
				'name'  => 'HTML5',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-instagram',
				'name'  => 'Instagram',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-joomla',
				'name'  => 'Joomla!',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-lastfm',
				'name'  => 'last.fm',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-linkedin',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-medium',
				'name'  => 'Medium',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-myspace',
				'name'  => 'My Space',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-orkut',
				'name'  => 'Orkut',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-path',
				'name'  => 'path',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-picasa',
				'name'  => 'Picasa',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-pinterest',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-rdio',
				'name'  => 'rdio',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-reddit',
				'name'  => 'reddit',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-skype',
				'name'  => 'Skype',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-skillshare',
				'name'  => 'SkillShare',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-smashing-mag',
				'name'  => 'Smashing Mag.',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-snapchat',
				'name'  => 'Snapchat',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-spotify',
				'name'  => 'Spotify',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-squidoo',
				'name'  => 'Squidoo',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-stack-overflow',
				'name'  => 'StackOverflow',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-steam',
				'name'  => 'Steam',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-stumbleupon',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-treehouse',
				'name'  => 'TreeHouse',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-tumblr',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-twitter',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-windows',
				'name'  => 'Windows',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-xbox',
				'name'  => 'XBox',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-yahoo',
				'name'  => 'Yahoo!',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-yelp',
				'name'  => 'Yelp',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-youtube',
				'name'  => 'YouTube',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-zerply',
				'name'  => 'Zerply',
			),
			array(
				'group' => 'social',
				'id'    => 'fi-social-zurb',
				'name'  => 'Zurb',
			),
		);

		/**
		 * Filter genericon items
		 *
		 * @since 0.1.0
		 * @param array $items Icon names.
		 */
		$items = apply_filters( 'icon_picker_foundations_items', $items );

		return $items;
	}
}

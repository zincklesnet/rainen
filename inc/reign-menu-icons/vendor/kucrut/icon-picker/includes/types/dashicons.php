<?php
/**
 * Dashicons
 *
 * @package Icon_Picker
 */


require_once dirname( __FILE__ ) . '/font.php';

/**
 * Icon type: Dashicons
 *
 * @since 0.1.0
 */
class Icon_Picker_Type_Dashicons extends Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $id = 'dashicons';

	/**
	 * Icon type name
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'Dashicons';

	/**
	 * Icon type version
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '4.3.1';

	/**
	 * Stylesheet URI
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $stylesheet_uri = '';


	/**
	 * Register assets
	 *
	 * @since Menu Icons  0.1.0
	 * @wp_hook action icon_picker_loader_init
	 *
	 * @param  Icon_Picker_Loader  $loader Icon_Picker_Loader instance.
	 *
	 * @return void
	 */
	public function register_assets( Icon_Picker_Loader $loader ) {
		$loader->add_style( $this->stylesheet_id );
	}


	/**
	 * Get icon groups
	 *
	 * @since Menu Icons 0.1.0
	 * @return array
	 */
	public function get_groups() {
		$groups = array(
			array(
				'id'   => 'admin',
				'name' => __( 'Admin', 'reign' ),
			),
			array(
				'id'   => 'post-formats',
				'name' => __( 'Post Formats', 'reign' ),
			),
			array(
				'id'   => 'welcome-screen',
				'name' => __( 'Welcome Screen', 'reign' ),
			),
			array(
				'id'   => 'image-editor',
				'name' => __( 'Image Editor', 'reign' ),
			),
			array(
				'id'   => 'text-editor',
				'name' => __( 'Text Editor', 'reign' ),
			),
			array(
				'id'   => 'post',
				'name' => __( 'Post', 'reign' ),
			),
			array(
				'id'   => 'sorting',
				'name' => __( 'Sorting', 'reign' ),
			),
			array(
				'id'   => 'social',
				'name' => __( 'Social', 'reign' ),
			),
			array(
				'id'   => 'jobs',
				'name' => __( 'Jobs', 'reign' ),
			),
			array(
				'id'   => 'products',
				'name' => __( 'Internal/Products', 'reign' ),
			),
			array(
				'id'   => 'taxonomies',
				'name' => __( 'Taxonomies', 'reign' ),
			),
			array(
				'id'   => 'alerts',
				'name' => __( 'Alerts/Notifications', 'reign' ),
			),
			array(
				'id'   => 'media',
				'name' => __( 'Media', 'reign' ),
			),
			array(
				'id'   => 'misc',
				'name' => __( 'Misc./Post Types', 'reign' ),
			),
		);

		/**
		 * Filter dashicon groups
		 *
		 * @since 0.1.0
		 * @param array $groups Icon groups.
		 */
		$groups = apply_filters( 'icon_picker_dashicons_groups', $groups );

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
				'group' => 'admin',
				'id'    => 'dashicons-admin-appearance',
				'name'  => __( 'Appearance', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-collapse',
				'name'  => __( 'Collapse', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-comments',
				'name'  => __( 'Comments', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-customizer',
				'name'  => __( 'Customizer', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-dashboard',
				'name'  => __( 'Dashboard', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-generic',
				'name'  => __( 'Generic', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-filter',
				'name'  => __( 'Filter', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-home',
				'name'  => __( 'Home', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-media',
				'name'  => __( 'Media', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-menu',
				'name'  => __( 'Menu', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-multisite',
				'name'  => __( 'Multisite', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-network',
				'name'  => __( 'Network', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-page',
				'name'  => __( 'Page', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-plugins',
				'name'  => __( 'Plugins', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-settings',
				'name'  => __( 'Settings', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-site',
				'name'  => __( 'Site', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-tools',
				'name'  => __( 'Tools', 'reign' ),
			),
			array(
				'group' => 'admin',
				'id'    => 'dashicons-admin-users',
				'name'  => __( 'Users', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-standard',
				'name'  => __( 'Standard', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-aside',
				'name'  => __( 'Aside', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-image',
				'name'  => __( 'Image', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-video',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-audio',
				'name'  => __( 'Audio', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-quote',
				'name'  => __( 'Quote', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-gallery',
				'name'  => __( 'Gallery', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-links',
				'name'  => __( 'Links', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-status',
				'name'  => __( 'Status', 'reign' ),
			),
			array(
				'group' => 'post-formats',
				'id'    => 'dashicons-format-chat',
				'name'  => __( 'Chat', 'reign' ),
			),
			array(
				'group' => 'welcome-screen',
				'id'    => 'dashicons-welcome-add-page',
				'name'  => __( 'Add page', 'reign' ),
			),
			array(
				'group' => 'welcome-screen',
				'id'    => 'dashicons-welcome-comments',
				'name'  => __( 'Comments', 'reign' ),
			),
			array(
				'group' => 'welcome-screen',
				'id'    => 'dashicons-welcome-edit-page',
				'name'  => __( 'Edit page', 'reign' ),
			),
			array(
				'group' => 'welcome-screen',
				'id'    => 'dashicons-welcome-learn-more',
				'name'  => __( 'Learn More', 'reign' ),
			),
			array(
				'group' => 'welcome-screen',
				'id'    => 'dashicons-welcome-view-site',
				'name'  => __( 'View Site', 'reign' ),
			),
			array(
				'group' => 'welcome-screen',
				'id'    => 'dashicons-welcome-widgets-menus',
				'name'  => __( 'Widgets', 'reign' ),
			),
			array(
				'group' => 'welcome-screen',
				'id'    => 'dashicons-welcome-write-blog',
				'name'  => __( 'Write Blog', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-image-crop',
				'name'  => __( 'Crop', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-image-filter',
				'name'  => __( 'Filter', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-image-rotate',
				'name'  => __( 'Rotate', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-image-rotate-left',
				'name'  => __( 'Rotate Left', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-image-rotate-right',
				'name'  => __( 'Rotate Right', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-image-flip-vertical',
				'name'  => __( 'Flip Vertical', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-image-flip-horizontal',
				'name'  => __( 'Flip Horizontal', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-undo',
				'name'  => __( 'Undo', 'reign' ),
			),
			array(
				'group' => 'image-editor',
				'id'    => 'dashicons-redo',
				'name'  => __( 'Redo', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-bold',
				'name'  => __( 'Bold', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-italic',
				'name'  => __( 'Italic', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-ul',
				'name'  => __( 'Unordered List', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-ol',
				'name'  => __( 'Ordered List', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-quote',
				'name'  => __( 'Quote', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-alignleft',
				'name'  => __( 'Align Left', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-aligncenter',
				'name'  => __( 'Align Center', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-alignright',
				'name'  => __( 'Align Right', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-insertmore',
				'name'  => __( 'Insert More', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-spellcheck',
				'name'  => __( 'Spell Check', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-distractionfree',
				'name'  => __( 'Distraction-free', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-kitchensink',
				'name'  => __( 'Kitchensink', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-underline',
				'name'  => __( 'Underline', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-justify',
				'name'  => __( 'Justify', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-textcolor',
				'name'  => __( 'Text Color', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-paste-word',
				'name'  => __( 'Paste Word', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-paste-text',
				'name'  => __( 'Paste Text', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-removeformatting',
				'name'  => __( 'Clear Formatting', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-video',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-customchar',
				'name'  => __( 'Custom Characters', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-indent',
				'name'  => __( 'Indent', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-outdent',
				'name'  => __( 'Outdent', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-help',
				'name'  => __( 'Help', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-strikethrough',
				'name'  => __( 'Strikethrough', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-unlink',
				'name'  => __( 'Unlink', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'dashicons-editor-rtl',
				'name'  => __( 'RTL', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-align-left',
				'name'  => __( 'Align Left', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-align-right',
				'name'  => __( 'Align Right', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-align-center',
				'name'  => __( 'Align Center', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-align-none',
				'name'  => __( 'Align None', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-lock',
				'name'  => __( 'Lock', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-calendar',
				'name'  => __( 'Calendar', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-calendar-alt',
				'name'  => __( 'Calendar', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-hidden',
				'name'  => __( 'Hidden', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-visibility',
				'name'  => __( 'Visibility', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-post-status',
				'name'  => __( 'Post Status', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-post-trash',
				'name'  => __( 'Post Trash', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-edit',
				'name'  => __( 'Edit', 'reign' ),
			),
			array(
				'group' => 'post',
				'id'    => 'dashicons-trash',
				'name'  => __( 'Trash', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-up',
				'name'  => __( 'Arrow: Up', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-down',
				'name'  => __( 'Arrow: Down', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-left',
				'name'  => __( 'Arrow: Left', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-right',
				'name'  => __( 'Arrow: Right', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-up-alt',
				'name'  => __( 'Arrow: Up', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-down-alt',
				'name'  => __( 'Arrow: Down', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-left-alt',
				'name'  => __( 'Arrow: Left', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-right-alt',
				'name'  => __( 'Arrow: Right', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-up-alt2',
				'name'  => __( 'Arrow: Up', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-down-alt2',
				'name'  => __( 'Arrow: Down', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-left-alt2',
				'name'  => __( 'Arrow: Left', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-arrow-right-alt2',
				'name'  => __( 'Arrow: Right', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-leftright',
				'name'  => __( 'Left-Right', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-sort',
				'name'  => __( 'Sort', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-list-view',
				'name'  => __( 'List View', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-exerpt-view',
				'name'  => __( 'Excerpt View', 'reign' ),
			),
			array(
				'group' => 'sorting',
				'id'    => 'dashicons-grid-view',
				'name'  => __( 'Grid View', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-share',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-share-alt',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-share-alt2',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-twitter',
				'name'  => __( 'Twitter', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-rss',
				'name'  => __( 'RSS', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-email',
				'name'  => __( 'Email', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-email-alt',
				'name'  => __( 'Email', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-facebook',
				'name'  => __( 'Facebook', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-facebook-alt',
				'name'  => __( 'Facebook', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-googleplus',
				'name'  => __( 'Google+', 'reign' ),
			),
			array(
				'group' => 'social',
				'id'    => 'dashicons-networking',
				'name'  => __( 'Networking', 'reign' ),
			),
			array(
				'group' => 'jobs',
				'id'    => 'dashicons-art',
				'name'  => __( 'Art', 'reign' ),
			),
			array(
				'group' => 'jobs',
				'id'    => 'dashicons-hammer',
				'name'  => __( 'Hammer', 'reign' ),
			),
			array(
				'group' => 'jobs',
				'id'    => 'dashicons-migrate',
				'name'  => __( 'Migrate', 'reign' ),
			),
			array(
				'group' => 'jobs',
				'id'    => 'dashicons-performance',
				'name'  => __( 'Performance', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-wordpress',
				'name'  => __( 'WordPress', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-wordpress-alt',
				'name'  => __( 'WordPress', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-pressthis',
				'name'  => __( 'PressThis', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-update',
				'name'  => __( 'Update', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-screenoptions',
				'name'  => __( 'Screen Options', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-info',
				'name'  => __( 'Info', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-cart',
				'name'  => __( 'Cart', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-feedback',
				'name'  => __( 'Feedback', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-cloud',
				'name'  => __( 'Cloud', 'reign' ),
			),
			array(
				'group' => 'products',
				'id'    => 'dashicons-translation',
				'name'  => __( 'Translation', 'reign' ),
			),
			array(
				'group' => 'taxonomies',
				'id'    => 'dashicons-tag',
				'name'  => __( 'Tag', 'reign' ),
			),
			array(
				'group' => 'taxonomies',
				'id'    => 'dashicons-category',
				'name'  => __( 'Category', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-yes',
				'name'  => __( 'Yes', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-no',
				'name'  => __( 'No', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-no-alt',
				'name'  => __( 'No', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-plus',
				'name'  => __( 'Plus', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-minus',
				'name'  => __( 'Minus', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-dismiss',
				'name'  => __( 'Dismiss', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-marker',
				'name'  => __( 'Marker', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-star-filled',
				'name'  => __( 'Star: Filled', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-star-half',
				'name'  => __( 'Star: Half', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-star-empty',
				'name'  => __( 'Star: Empty', 'reign' ),
			),
			array(
				'group' => 'alerts',
				'id'    => 'dashicons-flag',
				'name'  => __( 'Flag', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-skipback',
				'name'  => __( 'Skip Back', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-back',
				'name'  => __( 'Back', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-play',
				'name'  => __( 'Play', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-pause',
				'name'  => __( 'Pause', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-forward',
				'name'  => __( 'Forward', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-skipforward',
				'name'  => __( 'Skip Forward', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-repeat',
				'name'  => __( 'Repeat', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-volumeon',
				'name'  => __( 'Volume: On', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-controls-volumeoff',
				'name'  => __( 'Volume: Off', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-archive',
				'name'  => __( 'Archive', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-audio',
				'name'  => __( 'Audio', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-code',
				'name'  => __( 'Code', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-default',
				'name'  => __( 'Default', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-document',
				'name'  => __( 'Document', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-interactive',
				'name'  => __( 'Interactive', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-spreadsheet',
				'name'  => __( 'Spreadsheet', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-text',
				'name'  => __( 'Text', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-media-video',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-playlist-audio',
				'name'  => __( 'Audio Playlist', 'reign' ),
			),
			array(
				'group' => 'media',
				'id'    => 'dashicons-playlist-video',
				'name'  => __( 'Video Playlist', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-album',
				'name'  => __( 'Album', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-analytics',
				'name'  => __( 'Analytics', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-awards',
				'name'  => __( 'Awards', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-backup',
				'name'  => __( 'Backup', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-building',
				'name'  => __( 'Building', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-businessman',
				'name'  => __( 'Businessman', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-camera',
				'name'  => __( 'Camera', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-carrot',
				'name'  => __( 'Carrot', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-chart-pie',
				'name'  => __( 'Chart: Pie', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-chart-bar',
				'name'  => __( 'Chart: Bar', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-chart-line',
				'name'  => __( 'Chart: Line', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-chart-area',
				'name'  => __( 'Chart: Area', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-desktop',
				'name'  => __( 'Desktop', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-forms',
				'name'  => __( 'Forms', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-groups',
				'name'  => __( 'Groups', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-id',
				'name'  => __( 'ID', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-id-alt',
				'name'  => __( 'ID', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-images-alt',
				'name'  => __( 'Images', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-images-alt2',
				'name'  => __( 'Images', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-index-card',
				'name'  => __( 'Index Card', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-layout',
				'name'  => __( 'Layout', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-location',
				'name'  => __( 'Location', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-location-alt',
				'name'  => __( 'Location', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-products',
				'name'  => __( 'Products', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-portfolio',
				'name'  => __( 'Portfolio', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-book',
				'name'  => __( 'Book', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-book-alt',
				'name'  => __( 'Book', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-download',
				'name'  => __( 'Download', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-upload',
				'name'  => __( 'Upload', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-clock',
				'name'  => __( 'Clock', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-lightbulb',
				'name'  => __( 'Lightbulb', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-money',
				'name'  => __( 'Money', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-palmtree',
				'name'  => __( 'Palm Tree', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-phone',
				'name'  => __( 'Phone', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-search',
				'name'  => __( 'Search', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-shield',
				'name'  => __( 'Shield', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-shield-alt',
				'name'  => __( 'Shield', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-slides',
				'name'  => __( 'Slides', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-smartphone',
				'name'  => __( 'Smartphone', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-smiley',
				'name'  => __( 'Smiley', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-sos',
				'name'  => __( 'S.O.S.', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-sticky',
				'name'  => __( 'Sticky', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-store',
				'name'  => __( 'Store', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-tablet',
				'name'  => __( 'Tablet', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-testimonial',
				'name'  => __( 'Testimonial', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-tickets-alt',
				'name'  => __( 'Tickets', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-thumbs-up',
				'name'  => __( 'Thumbs Up', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-thumbs-down',
				'name'  => __( 'Thumbs Down', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-unlock',
				'name'  => __( 'Unlock', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-vault',
				'name'  => __( 'Vault', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-video-alt',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-video-alt2',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-video-alt3',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'dashicons-warning',
				'name'  => __( 'Warning', 'reign' ),
			),
		);

		/**
		 * Filter dashicon items
		 *
		 * @since 0.1.0
		 * @param array $items Icon names.
		 */
		$items = apply_filters( 'icon_picker_dashicons_items', $items );

		return $items;
	}
}

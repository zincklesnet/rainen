<?php

require_once dirname( __FILE__ ) . '/font.php';

/**
 * Reign Icons
 *
 * @package Icon_Picker
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */
class Icon_Picker_Type_Reign extends Icon_Picker_Type_Font {

	/**
	 * Icon type ID
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $id = 'reign';

	/**
	 * Icon type name
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $name = 'Reign';

	/**
	 * Icon type version
	 *
	 * @since Menu Icons 0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version = '1.0';

	/**
	 * Get icon groups
	 *
	 * @since Menu Icons 0.1.0
	 * @return array
	 */
	public function get_groups() {
		$groups = array(
			array(
				'id'   => 'a11y',
				'name' => __( 'Accessibility', 'reign' ),
			),
			array(
				'id'   => 'a11y',
				'name' => __( 'Accessibility', 'reign' ),
			),
			array(
				'id'   => 'brand',
				'name' => __( 'Brand', 'reign' ),
			),
			array(
				'id'   => 'chart',
				'name' => __( 'Charts', 'reign' ),
			),
			array(
				'id'   => 'cloud',
				'name' => __( 'Cloud', 'reign' ),
			),
			array(
				'id'   => 'currency',
				'name' => __( 'Currency', 'reign' ),
			),
			array(
				'id'   => 'directional',
				'name' => __( 'Directional', 'reign' ),
			),
			array(
				'id'   => 'file-types',
				'name' => __( 'File Types', 'reign' ),
			),
			array(
				'id'   => 'form-control',
				'name' => __( 'Form Controls', 'reign' ),
			),
			array(
				'id'   => 'gender',
				'name' => __( 'Genders', 'reign' ),
			),
			array(
				'id'   => 'medical',
				'name' => __( 'Medical', 'reign' ),
			),
			array(
				'id'   => 'misc',
				'name' => __( 'Misc.', 'reign' ),
			),
			array(
				'id'   => 'payment',
				'name' => __( 'Payment', 'reign' ),
			),
			array(
				'id'   => 'phone',
				'name' => __( 'Phone Calls', 'reign' ),
			),
			array(
				'id'   => 'spinner',
				'name' => __( 'Spinners', 'reign' ),
			),
			array(
				'id'   => 'transportation',
				'name' => __( 'Transportation', 'reign' ),
			),
			array(
				'id'   => 'text-editor',
				'name' => __( 'Text Editor', 'reign' ),
			),
			array(
				'id'   => 'video-player',
				'name' => __( 'Video Player', 'reign' ),
			),
			array(
				'id'   => 'web-application',
				'name' => __( 'Web Application', 'reign' ),
			),
		);

		/**
		 * Filter reign groups
		 *
		 * @since 0.1.0
		 *
		 * @param array $groups Icon groups.
		 */
		$groups = apply_filters( 'icon_picker_reign_groups', $groups );

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
				'group' => 'directional',
				'id'    => 'fa fa-chevron-square-up',
				'name'  => __( 'Arrow Up: Square', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-square-down',
				'name'  => __( 'Arrow Down: Square', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-alt-circle-up',
				'name'  => __( 'Arrow Up: Circle', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-alt-circle-down',
				'name'  => __( 'Arrow Down: Circle', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-award',
				'name'  => __( 'Award', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-badge',
				'name'  => __( 'Badge', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-ribbon',
				'name'  => __( 'Ribbon', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-battery-three-quarters',
				'name'  => __( 'Battery', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bell-plus',
				'name'  => __( 'Bell: Plus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-book-open',
				'name'  => __( 'Book: Open', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-box',
				'name'  => __( 'Box', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-lightbulb',
				'name'  => __( 'Bulb', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-comment-dots',
				'name'  => __( 'Chat', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-double-down',
				'name'  => __( 'Chevrons: Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-double-up',
				'name'  => __( 'Chevrons: Up', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-double-left',
				'name'  => __( 'Chevrons: Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-double-right',
				'name'  => __( 'Chevrons: Right', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-clock',
				'name'  => __( 'Clock', 'reign' ),
			),
			array(
				'group' => 'cloud',
				'id'    => 'fa fa-cloud-drizzle',
				'name'  => __( 'Cloud: Drizzle', 'reign' ),
			),
			array(
				'group' => 'cloud',
				'id'    => 'fa fa-cloud-rain',
				'name'  => __( 'Cloud: Rain', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-compact-disc',
				'name'  => __( 'Disc', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa fa-file-code',
				'name'  => __( 'File: Code', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa fa-file-minus',
				'name'  => __( 'File: Minus', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa fa-file-pdf',
				'name'  => __( 'File: PDF', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa fa-file-plus',
				'name'  => __( 'File: Plus', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa fa-file-video',
				'name'  => __( 'File: Video', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa fa-image',
				'name'  => __( 'Image', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-laugh',
				'name'  => __( 'Laugh', 'reign' ),
			),
			array(
				'group' => 'phone',
				'id'    => 'fa fa-microphone-alt',
				'name'  => __( 'Mic', 'reign' ),
			),
			array(
				'group' => 'phone',
				'id'    => 'fa fa-microphone-alt-slash',
				'name'  => __( 'Mic: Off', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-moon',
				'name'  => __( 'Moon', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-pizza-slice',
				'name'  => __( 'Pizza Slice', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-radio',
				'name'  => __( 'Radio', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-smile',
				'name'  => __( 'Smile', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sun',
				'name'  => __( 'Sun', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-text',
				'name'  => __( 'Text', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-thermometer',
				'name'  => __( 'Thermometer', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-triangle',
				'name'  => __( 'Triangle', 'reign' ),
			),
			array(
				'group' => 'mics',
				'id'    => 'fa fa-tv',
				'name'  => __( 'TV', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-user-alt',
				'name'  => __( 'User: Alt', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-user-check',
				'name'  => __( 'User: Check', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-user-minus',
				'name'  => __( 'User: Minus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-video',
				'name'  => __( 'Video', 'reign' ),
			),
			array(
				'group' => 'phone',
				'id'    => 'fa fa-voicemail',
				'name'  => __( 'Voicemail', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-volume-mute',
				'name'  => __( 'Volume: Mute', 'reign' ),
			),
			array(
				'group' => 'misc',
				'id'    => 'fa fa-watch',
				'name'  => __( 'Watch', 'reign' ),
			),
			/* Accessibility (a11y) */
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-american-sign-language-interpreting',
				'name'  => __( 'American Sign Language', 'reign' ),
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-audio-description',
				'name'  => __( 'Audio Description', 'reign' ),
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-assistive-listening-systems',
				'name'  => __( 'Assistive Listening Systems', 'reign' ),
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-blind',
				'name'  => __( 'Blind', 'reign' ),
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-braille',
				'name'  => __( 'Braille', 'reign' ),
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-deaf',
				'name'  => __( 'Deaf', 'reign' ),
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-low-vision',
				'name'  => __( 'Low Vision', 'reign' ),
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-sign-language',
				'name'  => __( 'Sign Language', 'reign' ),
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa fa-universal-access',
				'name'  => __( 'Universal Access', 'reign' ),
			),
			/* Brand (brand) */
			array(
				'group' => 'brand',
				'id'    => 'fab fa-500px',
				'name'  => '500px',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-adn',
				'name'  => 'ADN',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-amazon',
				'name'  => 'Amazon',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-android',
				'name'  => 'Android',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-angellist',
				'name'  => 'AngelList',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-apple',
				'name'  => 'Apple',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-black-tie',
				'name'  => 'BlackTie',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-bandcamp',
				'name'  => 'Bandcamp',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-behance',
				'name'  => 'Behance',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-behance-square',
				'name'  => 'Behance',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa fa-bitbucket',
				'name'  => 'Bitbucket',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-bluetooth',
				'name'  => 'Bluetooth',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-bluetooth-b',
				'name'  => 'Bluetooth',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-buysellads',
				'name'  => 'BuySellAds',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-chrome',
				'name'  => 'Chrome',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-codepen',
				'name'  => 'CodePen',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-codiepie',
				'name'  => 'Codie Pie',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-connectdevelop',
				'name'  => 'Connect + Develop',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-contao',
				'name'  => 'Contao',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-creative-commons',
				'name'  => 'Creative Commons',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-css3',
				'name'  => 'CSS3',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-dashcube',
				'name'  => 'Dashcube',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-delicious',
				'name'  => 'Delicious',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-deviantart',
				'name'  => 'deviantART',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-digg',
				'name'  => 'Digg',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-dribbble',
				'name'  => 'Dribbble',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-dropbox',
				'name'  => 'DropBox',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-drupal',
				'name'  => 'Drupal',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-empire',
				'name'  => 'Empire',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-edge',
				'name'  => 'Edge',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-envira',
				'name'  => 'Envira',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-etsy',
				'name'  => 'Etsy',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-expeditedssl',
				'name'  => 'ExpeditedSSL',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-facebook-f',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-facebook-square',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-facebook',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-firefox',
				'name'  => 'Firefox',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-flickr',
				'name'  => 'Flickr',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-fonticons',
				'name'  => 'FontIcons',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-fort-awesome',
				'name'  => 'Fort Awesome',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-forumbee',
				'name'  => 'Forumbee',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-foursquare',
				'name'  => 'Foursquare',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-free-code-camp',
				'name'  => 'Free Code Camp',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-get-pocket',
				'name'  => 'Pocket',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-git',
				'name'  => 'Git',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-git-square',
				'name'  => 'Git',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-github',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-gitlab',
				'name'  => 'Gitlab',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-github-alt',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-github-square',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-glide',
				'name'  => 'Glide',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-glide-g',
				'name'  => 'Glide',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-google',
				'name'  => 'Google',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-google-plus',
				'name'  => 'Google+',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-google-plus-square',
				'name'  => 'Google+',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-grav',
				'name'  => 'Grav',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-hacker-news',
				'name'  => 'Hacker News',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-houzz',
				'name'  => 'Houzz',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-html5',
				'name'  => 'HTML5',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-imdb',
				'name'  => 'IMDb',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-instagram',
				'name'  => 'Instagram',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-internet-explorer',
				'name'  => 'Internet Explorer',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-ioxhost',
				'name'  => 'IoxHost',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-joomla',
				'name'  => 'Joomla',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-jsfiddle',
				'name'  => 'JSFiddle',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-lastfm',
				'name'  => 'Last.fm',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-lastfm-square',
				'name'  => 'Last.fm',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-leanpub',
				'name'  => 'Leanpub',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-linkedin',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-linode',
				'name'  => 'Linode',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-linux',
				'name'  => 'Linux',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-maxcdn',
				'name'  => 'MaxCDN',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-medium',
				'name'  => 'Medium',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-meetup',
				'name'  => 'Meetup',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-mixcloud',
				'name'  => 'Mixcloud',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-modx',
				'name'  => 'MODX',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-odnoklassniki',
				'name'  => 'Odnoklassniki',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-odnoklassniki-square',
				'name'  => 'Odnoklassniki',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-opencart',
				'name'  => 'OpenCart',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-openid',
				'name'  => 'OpenID',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-opera',
				'name'  => 'Opera',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-optin-monster',
				'name'  => 'OptinMonster',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-pagelines',
				'name'  => 'Pagelines',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-pied-piper',
				'name'  => 'Pied Piper',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-pied-piper-alt',
				'name'  => 'Pied Piper',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-pinterest',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-pinterest-p',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-pinterest-square',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-product-hunt',
				'name'  => 'Product Hunt',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-quora',
				'name'  => 'Quora',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-qq',
				'name'  => 'QQ',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-reddit',
				'name'  => 'reddit',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-ravelry',
				'name'  => 'Ravelry',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-reddit-alien',
				'name'  => 'reddit',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-reddit-square',
				'name'  => 'reddit',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-renren',
				'name'  => 'Renren',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-safari',
				'name'  => 'Safari',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-scribd',
				'name'  => 'Scribd',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-sellsy',
				'name'  => 'SELLSY',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-shirtsinbulk',
				'name'  => 'Shirts In Bulk',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-simplybuilt',
				'name'  => 'SimplyBuilt',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-skyatlas',
				'name'  => 'Skyatlas',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-skype',
				'name'  => 'Skype',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-slack',
				'name'  => 'Slack',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-slideshare',
				'name'  => 'SlideShare',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-soundcloud',
				'name'  => 'SoundCloud',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-snapchat',
				'name'  => 'Snapchat',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-snapchat-ghost',
				'name'  => 'Snapchat',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-snapchat-square',
				'name'  => 'Snapchat',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-spotify',
				'name'  => 'Spotify',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-stack-exchange',
				'name'  => 'Stack Exchange',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-stack-overflow',
				'name'  => 'Stack Overflow',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-steam',
				'name'  => 'Steam',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-steam-square',
				'name'  => 'Steam',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-stumbleupon',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-stumbleupon-circle',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-superpowers',
				'name'  => 'Superpowers',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-telegram',
				'name'  => 'Telegram',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-tencent-weibo',
				'name'  => 'Tencent Weibo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-trello',
				'name'  => 'Trello',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-tripadvisor',
				'name'  => 'TripAdvisor',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-tumblr',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-tumblr-square',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-twitch',
				'name'  => 'Twitch',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-twitter',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-twitter-square',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-x-twitter',
				'name'  => 'X-twitter',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-usb',
				'name'  => 'USB',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-vimeo',
				'name'  => 'Vimeo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-viadeo',
				'name'  => 'Viadeo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-viadeo-square',
				'name'  => 'Viadeo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-vimeo-square',
				'name'  => 'Vimeo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-viacoin',
				'name'  => 'Viacoin',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-vine',
				'name'  => 'Vine',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-vk',
				'name'  => 'VK',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-weixin',
				'name'  => 'Weixin',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-weibo',
				'name'  => 'Wibo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-whatsapp',
				'name'  => 'WhatsApp',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-wikipedia-w',
				'name'  => 'Wikipedia',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-windows',
				'name'  => 'Windows',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-wordpress',
				'name'  => 'WordPress',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-wpbeginner',
				'name'  => 'WP Beginner',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-wpexplorer',
				'name'  => 'WP Explorer',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-wpforms',
				'name'  => 'WP Forms',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-xing',
				'name'  => 'Xing',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-xing-square',
				'name'  => 'Xing',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-y-combinator',
				'name'  => 'Y Combinator',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-yahoo',
				'name'  => 'Yahoo!',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-yelp',
				'name'  => 'Yelp',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-youtube',
				'name'  => 'YouTube',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-youtube-square',
				'name'  => 'YouTube',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-tiktok',
				'name'  => 'TikTok',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-discord',
				'name'  => 'Discord',
			),
			array(
				'group' => 'brand',
				'id'    => 'fab fa-facebook-messenger',
				'name'  => 'Messenger',
			),
			/* Chart (chart) */
			array(
				'group' => 'chart',
				'id'    => 'fa fa-chart-bar',
				'name'  => __( 'Bar Chart', 'reign' ),
			),
			array(
				'group' => 'chart',
				'id'    => 'fa fa-chart-area',
				'name'  => __( 'Bar Chart: Area', 'reign' ),
			),
			/* Currency (currency) */
			array(
				'group' => 'currency',
				'id'    => 'fab fa-bitcoin',
				'name'  => __( 'Bitcoin', 'reign' ),
			),
			array(
				'group' => 'currency',
				'id'    => 'fa fa-dollar-sign',
				'name'  => __( 'Dollar', 'reign' ),
			),
			array(
				'group' => 'currency',
				'id'    => 'fa fa-euro-sign',
				'name'  => __( 'Euro', 'reign' ),
			),
			array(
				'group' => 'currency',
				'id'    => 'fab fa-gg',
				'name'  => __( 'GBP', 'reign' ),
			),
			array(
				'group' => 'currency',
				'id'    => 'fab fa-gg-circle',
				'name'  => __( 'GG', 'reign' ),
			),
			array(
				'group' => 'currency',
				'id'    => 'fa fa-money-bill',
				'name'  => __( 'Money', 'reign' ),
			),

			/* Directional (directional) */
			array(
				'group' => 'directional',
				'id'    => 'fa fa-angle-down',
				'name'  => __( 'Angle Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-angle-left',
				'name'  => __( 'Angle Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-angle-right',
				'name'  => __( 'Angle Right', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-angle-up',
				'name'  => __( 'Angle Up', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-angle-double-down',
				'name'  => __( 'Angle Double Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-angle-double-left',
				'name'  => __( 'Angle Double Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-angle-double-right',
				'name'  => __( 'Angle Double Right', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-angle-double-up',
				'name'  => __( 'Angle Double Up', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-circle-down',
				'name'  => __( 'Arrow Circle Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-circle-left',
				'name'  => __( 'Arrow Circle Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-circle-right',
				'name'  => __( 'Arrow Circle Right', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-circle-up',
				'name'  => __( 'Arrow Circle Up', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-down',
				'name'  => __( 'Arrow Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-left',
				'name'  => __( 'Arrow Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-right',
				'name'  => __( 'Arrow Right', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrow-up',
				'name'  => __( 'Arrow Up', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrows',
				'name'  => __( 'Arrows', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrows-alt',
				'name'  => __( 'Arrows', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrows-h',
				'name'  => __( 'Arrows', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-arrows-v',
				'name'  => __( 'Arrows', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-caret-down',
				'name'  => __( 'Caret Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-caret-left',
				'name'  => __( 'Caret Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-caret-right',
				'name'  => __( 'Caret Right', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-caret-up',
				'name'  => __( 'Caret Up', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-circle-down',
				'name'  => __( 'Chevron Circle Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-circle-left',
				'name'  => __( 'Chevron Circle Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-circle-right',
				'name'  => __( 'Chevron Circle Right', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-circle-up',
				'name'  => __( 'Chevron Circle Up', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-down',
				'name'  => __( 'Chevron Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-left',
				'name'  => __( 'Chevron Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-right',
				'name'  => __( 'Chevron Right', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-chevron-up',
				'name'  => __( 'Chevron Up', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-long-arrow-down',
				'name'  => __( 'Long Arrow Down', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-long-arrow-left',
				'name'  => __( 'Long Arrow Left', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-long-arrow-right',
				'name'  => __( 'Long Arrow Right', 'reign' ),
			),
			array(
				'group' => 'directional',
				'id'    => 'fa fa-long-arrow-up',
				'name'  => __( 'Long Arrow Up', 'reign' ),
			),

			/* File Types (file-types) */
			array(
				'group' => 'file-types',
				'id'    => 'fa fa-file',
				'name'  => __( 'File', 'reign' ),
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa fa-file-alt',
				'name'  => __( 'File: Text', 'reign' ),
			),

			/* Form Control (form-control) */
			array(
				'group' => 'form-control',
				'id'    => 'fa fa-check-square',
				'name'  => __( 'Check', 'reign' ),
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa fa-circle',
				'name'  => __( 'Circle', 'reign' ),
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa fa-minus-square',
				'name'  => __( 'Minus', 'reign' ),
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa fa-plus-square',
				'name'  => __( 'Plus', 'reign' ),
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa fa-square',
				'name'  => __( 'Square', 'reign' ),
			),

			/* Gender (gender) */
			array(
				'group' => 'gender',
				'id'    => 'fa fa-genderless',
				'name'  => __( 'Genderless', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-mars',
				'name'  => __( 'Mars', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-mars-double',
				'name'  => __( 'Mars', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-mars-stroke',
				'name'  => __( 'Mars', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-mars-stroke-h',
				'name'  => __( 'Mars', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-mars-stroke-v',
				'name'  => __( 'Mars', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-mercury',
				'name'  => __( 'Mercury', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-neuter',
				'name'  => __( 'Neuter', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-transgender',
				'name'  => __( 'Transgender', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-transgender-alt',
				'name'  => __( 'Transgender', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-venus',
				'name'  => __( 'Venus', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-venus-double',
				'name'  => __( 'Venus', 'reign' ),
			),
			array(
				'group' => 'gender',
				'id'    => 'fa fa-venus-mars',
				'name'  => __( 'Venus + Mars', 'reign' ),
			),

			/* Medical (medical) */
			array(
				'group' => 'medical',
				'id'    => 'fa fa-heart',
				'name'  => __( 'Heart', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-heartbeat',
				'name'  => __( 'Heartbeat', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-h-square',
				'name'  => __( 'Hospital', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-medkit',
				'name'  => __( 'Medkit', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-stethoscope',
				'name'  => __( 'Stethoscope', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-thermometer-empty',
				'name'  => __( 'Thermometer', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-thermometer-quarter',
				'name'  => __( 'Thermometer', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-thermometer-half',
				'name'  => __( 'Thermometer', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-thermometer-three-quarters',
				'name'  => __( 'Thermometer', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-thermometer-full',
				'name'  => __( 'Thermometer', 'reign' ),
			),
			array(
				'group' => 'medical',
				'id'    => 'fa fa-user-md',
				'name'  => __( 'User MD', 'reign' ),
			),

			/* Payment (payment) */
			array(
				'group' => 'payment',
				'id'    => 'fab fa-cc-amex',
				'name'  => 'American Express',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa fa-credit-card',
				'name'  => __( 'Credit Card', 'reign' ),
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-cc-diners-club',
				'name'  => 'Diners Club',
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-cc-discover',
				'name'  => 'Discover',
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-google-wallet',
				'name'  => 'Google Wallet',
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-cc-jcb',
				'name'  => 'JCB',
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-cc-mastercard',
				'name'  => 'MasterCard',
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-cc-paypal',
				'name'  => 'PayPal',
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-paypal',
				'name'  => 'PayPal',
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-cc-stripe',
				'name'  => 'Stripe',
			),
			array(
				'group' => 'payment',
				'id'    => 'fab fa-cc-visa',
				'name'  => 'Visa',
			),

			/* Spinner (spinner) */

			array(
				'group' => 'spinner',
				'id'    => 'fa fa-cog',
				'name'  => __( 'Cog', 'reign' ),
			),
			array(
				'group' => 'spinner',
				'id'    => 'fa fa-spinner',
				'name'  => __( 'Spinner', 'reign' ),
			),

			/* Transportation (transportation) */
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-ambulance',
				'name'  => __( 'Ambulance', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-bicycle',
				'name'  => __( 'Bicycle', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-bus',
				'name'  => __( 'Bus', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-car',
				'name'  => __( 'Car', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-fighter-jet',
				'name'  => __( 'Fighter Jet', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-motorcycle',
				'name'  => __( 'Motorcycle', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-plane',
				'name'  => __( 'Plane', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-rocket',
				'name'  => __( 'Rocket', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-ship',
				'name'  => __( 'Ship', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-space-shuttle',
				'name'  => __( 'Space Shuttle', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-subway',
				'name'  => __( 'Subway', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-taxi',
				'name'  => __( 'Taxi', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-train',
				'name'  => __( 'Train', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-truck',
				'name'  => __( 'Truck', 'reign' ),
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa fa-wheelchair',
				'name'  => __( 'Wheelchair', 'reign' ),
			),
			/* Text Editor (text-editor) */
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-align-left',
				'name'  => __( 'Align Left', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-align-center',
				'name'  => __( 'Align Center', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-align-justify',
				'name'  => __( 'Justify', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-align-right',
				'name'  => __( 'Align Right', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-bold',
				'name'  => __( 'Bold', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-clipboard',
				'name'  => __( 'Clipboard', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-columns',
				'name'  => __( 'Columns', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-copy',
				'name'  => __( 'Copy', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-cut',
				'name'  => __( 'Cut', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-paste',
				'name'  => __( 'Paste', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-eraser',
				'name'  => __( 'Eraser', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-font',
				'name'  => __( 'Font', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-indent',
				'name'  => __( 'Indent', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-outdent',
				'name'  => __( 'Outdent', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-italic',
				'name'  => __( 'Italic', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-link',
				'name'  => __( 'Link', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-unlink',
				'name'  => __( 'Unlink', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-list',
				'name'  => __( 'List', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-list-alt',
				'name'  => __( 'List', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-list-ol',
				'name'  => __( 'Ordered List', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-list-ul',
				'name'  => __( 'Unordered List', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-paperclip',
				'name'  => __( 'Paperclip', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-paragraph',
				'name'  => __( 'Paragraph', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-repeat',
				'name'  => __( 'Repeat', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-undo',
				'name'  => __( 'Undo', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-save',
				'name'  => __( 'Save', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-strikethrough',
				'name'  => __( 'Strikethrough', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-subscript',
				'name'  => __( 'Subscript', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-superscript',
				'name'  => __( 'Superscript', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-table',
				'name'  => __( 'Table', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-text-height',
				'name'  => __( 'Text Height', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-text-width',
				'name'  => __( 'Text Width', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-th',
				'name'  => __( 'Table Header', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-th-large',
				'name'  => __( 'TH Large', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-th-list',
				'name'  => __( 'TH List', 'reign' ),
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa fa-underline',
				'name'  => __( 'Underline', 'reign' ),
			),

			/* Video Player (video-player) */
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-backward',
				'name'  => __( 'Backward', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-compress',
				'name'  => __( 'Compress', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-eject',
				'name'  => __( 'Eject', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-expand',
				'name'  => __( 'Expand', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-fast-backward',
				'name'  => __( 'Fast Backward', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-fast-forward',
				'name'  => __( 'Fast Forward', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-forward',
				'name'  => __( 'Forward', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-pause',
				'name'  => __( 'Pause', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-pause-circle',
				'name'  => __( 'Pause', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-play',
				'name'  => __( 'Play', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-play-circle',
				'name'  => __( 'Play', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-step-backward',
				'name'  => __( 'Step Backward', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-step-forward',
				'name'  => __( 'Step Forward', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-stop',
				'name'  => __( 'Stop', 'reign' ),
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa fa-stop-circle',
				'name'  => __( 'Stop', 'reign' ),
			),

			/* Web Application (web-application) */
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-address-book',
				'name'  => __( 'Address Book', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-address-card',
				'name'  => __( 'Address Card', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-adjust',
				'name'  => __( 'Adjust', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-anchor',
				'name'  => __( 'Anchor', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-archive',
				'name'  => __( 'Archive', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-arrows',
				'name'  => __( 'Arrows', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-arrows-h',
				'name'  => __( 'Arrows', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-arrows-v',
				'name'  => __( 'Arrows', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-asterisk',
				'name'  => __( 'Asterisk', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-at',
				'name'  => __( 'At', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-balance-scale',
				'name'  => __( 'Balance', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-ban',
				'name'  => __( 'Ban', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-barcode',
				'name'  => __( 'Barcode', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bars',
				'name'  => __( 'Bars', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-battery-empty',
				'name'  => __( 'Battery', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-battery-quarter',
				'name'  => __( 'Battery', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-battery-half',
				'name'  => __( 'Battery', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-battery-full',
				'name'  => __( 'Battery', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bed',
				'name'  => __( 'Bed', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-beer',
				'name'  => __( 'Beer', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bell',
				'name'  => __( 'Bell', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bell-slash',
				'name'  => __( 'Bell', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-binoculars',
				'name'  => __( 'Binoculars', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-birthday-cake',
				'name'  => __( 'Birthday Cake', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bolt',
				'name'  => __( 'Bolt', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-book',
				'name'  => __( 'Book', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bookmark',
				'name'  => __( 'Bookmark', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bomb',
				'name'  => __( 'Bomb', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-briefcase',
				'name'  => __( 'Briefcase', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bug',
				'name'  => __( 'Bug', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-building',
				'name'  => __( 'Building', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bullhorn',
				'name'  => __( 'Bullhorn', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-bullseye',
				'name'  => __( 'Bullseye', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-calculator',
				'name'  => __( 'Calculator', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-calendar',
				'name'  => __( 'Calendar', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-camera',
				'name'  => __( 'Camera', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-camera-retro',
				'name'  => __( 'Camera Retro', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-cart-arrow-down',
				'name'  => __( 'Cart Arrow Down', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-cart-plus',
				'name'  => __( 'Cart Plus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-certificate',
				'name'  => __( 'Certificate', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-check',
				'name'  => __( 'Check', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-check-circle',
				'name'  => __( 'Check', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-child',
				'name'  => __( 'Child', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-clone',
				'name'  => __( 'Clone', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-cloud',
				'name'  => __( 'Cloud', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-cloud-download',
				'name'  => __( 'Cloud Download', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-cloud-upload',
				'name'  => __( 'Cloud Upload', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-code',
				'name'  => __( 'Code', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-coffee',
				'name'  => __( 'Coffee', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-cogs',
				'name'  => __( 'Cogs', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-comment',
				'name'  => __( 'Comment', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-comments',
				'name'  => __( 'Comments', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-compass',
				'name'  => __( 'Compass', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-copyright',
				'name'  => __( 'Copyright', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-credit-card',
				'name'  => __( 'Credit Card', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-crop',
				'name'  => __( 'Crop', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-crosshairs',
				'name'  => __( 'Crosshairs', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-cube',
				'name'  => __( 'Cube', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-cubes',
				'name'  => __( 'Cubes', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-i-cursor',
				'name'  => __( 'Cursor', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-database',
				'name'  => __( 'Database', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-desktop',
				'name'  => __( 'Desktop', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-diamond',
				'name'  => __( 'Diamond', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-download',
				'name'  => __( 'Download', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-edit',
				'name'  => __( 'Edit', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-ellipsis-h',
				'name'  => __( 'Ellipsis', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-ellipsis-v',
				'name'  => __( 'Ellipsis', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-envelope',
				'name'  => __( 'Envelope', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-envelope-square',
				'name'  => __( 'Envelope', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-envelope-open',
				'name'  => __( 'Envelope', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-eraser',
				'name'  => __( 'Eraser', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-exchange',
				'name'  => __( 'Exchange', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-exclamation',
				'name'  => __( 'Exclamation', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-exclamation-circle',
				'name'  => __( 'Exclamation', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-exclamation-triangle',
				'name'  => __( 'Exclamation', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-external-link',
				'name'  => __( 'External Link', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-external-link-square',
				'name'  => __( 'External Link', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-eye',
				'name'  => __( 'Eye', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-eye-slash',
				'name'  => __( 'Eye', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-eye-dropper',
				'name'  => __( 'Eye Dropper', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-fax',
				'name'  => __( 'Fax', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-female',
				'name'  => __( 'Female', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-film',
				'name'  => __( 'Film', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-filter',
				'name'  => __( 'Filter', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-fire',
				'name'  => __( 'Fire', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-fire-extinguisher',
				'name'  => __( 'Fire Extinguisher', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-flag',
				'name'  => __( 'Flag', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-flag-checkered',
				'name'  => __( 'Flag', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-flask',
				'name'  => __( 'Flask', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-folder',
				'name'  => __( 'Folder', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-folder-open',
				'name'  => __( 'Folder Open', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-gamepad',
				'name'  => __( 'Gamepad', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-gavel',
				'name'  => __( 'Gavel', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-gift',
				'name'  => __( 'Gift', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-wine-glass',
				'name'  => __( 'Glass', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-globe',
				'name'  => __( 'Globe', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-graduation-cap',
				'name'  => __( 'Graduation Cap', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-hashtag',
				'name'  => __( 'Hash Tag', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-headphones',
				'name'  => __( 'Headphones', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-home',
				'name'  => __( 'Home', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-hourglass-start',
				'name'  => __( 'Hourglass', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-hourglass-half',
				'name'  => __( 'Hourglass', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-hourglass-end',
				'name'  => __( 'Hourglass', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-hourglass',
				'name'  => __( 'Hourglass', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-history',
				'name'  => __( 'History', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-inbox',
				'name'  => __( 'Inbox', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-id-badge',
				'name'  => __( 'ID Badge', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-id-card',
				'name'  => __( 'ID Card', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-industry',
				'name'  => __( 'Industry', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-info',
				'name'  => __( 'Info', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-info-circle',
				'name'  => __( 'Info', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-key',
				'name'  => __( 'Key', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-language',
				'name'  => __( 'Language', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-laptop',
				'name'  => __( 'Laptop', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-leaf',
				'name'  => __( 'Leaf', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-level-down',
				'name'  => __( 'Level Down', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-level-up',
				'name'  => __( 'Level Up', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-life-ring',
				'name'  => __( 'Life Buoy', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-location-arrow',
				'name'  => __( 'Location Arrow', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-lock',
				'name'  => __( 'Lock', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-magic',
				'name'  => __( 'Magic', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-magnet',
				'name'  => __( 'Magnet', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-male',
				'name'  => __( 'Male', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-map',
				'name'  => __( 'Map', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-map-marker',
				'name'  => __( 'Map Marker', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-map-pin',
				'name'  => __( 'Map Pin', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-map-signs',
				'name'  => __( 'Map Signs', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-microchip',
				'name'  => __( 'Microchip', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-microphone',
				'name'  => __( 'Microphone', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-microphone-slash',
				'name'  => __( 'Microphone', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-minus',
				'name'  => __( 'Minus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-minus-circle',
				'name'  => __( 'Minus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-mobile',
				'name'  => __( 'Mobile', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-mouse-pointer',
				'name'  => __( 'Mouse Pointer', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-music',
				'name'  => __( 'Music', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-object-group',
				'name'  => __( 'Object Group', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-object-ungroup',
				'name'  => __( 'Object Ungroup', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-paint-brush',
				'name'  => __( 'Paint Brush', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-paper-plane',
				'name'  => __( 'Paper Plane', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-paw',
				'name'  => __( 'Paw', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-pencil',
				'name'  => __( 'Pencil', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-pencil-alt',
				'name'  => __( 'Pencil', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-phone',
				'name'  => __( 'Phone', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-percent',
				'name'  => __( 'Percent', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-phone-square',
				'name'  => __( 'Phone', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-plug',
				'name'  => __( 'Plug', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-plus',
				'name'  => __( 'Plus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-plus-circle',
				'name'  => __( 'Plus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-power-off',
				'name'  => __( 'Power Off', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-podcast',
				'name'  => __( 'Podcast', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-print',
				'name'  => __( 'Print', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-puzzle-piece',
				'name'  => __( 'Puzzle Piece', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-qrcode',
				'name'  => __( 'QR Code', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-question',
				'name'  => __( 'Question', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-question-circle',
				'name'  => __( 'Question', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-quote-left',
				'name'  => __( 'Quote Left', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-quote-right',
				'name'  => __( 'Quote Right', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-random',
				'name'  => __( 'Random', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-rebel',
				'name'  => __( 'Rebel', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-recycle',
				'name'  => __( 'Recycle', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-registered',
				'name'  => __( 'Registered', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-reply',
				'name'  => __( 'Reply', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-reply-all',
				'name'  => __( 'Reply All', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-retweet',
				'name'  => __( 'Retweet', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-road',
				'name'  => __( 'Road', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-rss',
				'name'  => __( 'RSS', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-rss-square',
				'name'  => __( 'RSS Square', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-search',
				'name'  => __( 'Search', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-search-minus',
				'name'  => __( 'Search Minus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-search-plus',
				'name'  => __( 'Search Plus', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-server',
				'name'  => __( 'Server', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-share',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-share-alt',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-share-alt-square',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-share-square',
				'name'  => __( 'Share', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-shield',
				'name'  => __( 'Shield', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-shopping-cart',
				'name'  => __( 'Shopping Cart', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-shopping-bag',
				'name'  => __( 'Shopping Bag', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-shopping-basket',
				'name'  => __( 'Shopping Basket', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-shower',
				'name'  => __( 'Shower', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sign-in',
				'name'  => __( 'Sign In', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sign-out',
				'name'  => __( 'Sign Out', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-signal',
				'name'  => __( 'Signal', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sitemap',
				'name'  => __( 'Sitemap', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sliders-h',
				'name'  => __( 'Sliders', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-snowflake',
				'name'  => __( 'Snowflake', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort',
				'name'  => __( 'Sort', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort-down',
				'name'  => __( 'Sort Down', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort-up',
				'name'  => __( 'Sort Up', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort-alpha-down-alt',
				'name'  => __( 'Sort Alpha ASC', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort-alpha-up-alt',
				'name'  => __( 'Sort Alpha DESC', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort-amount-down-alt',
				'name'  => __( 'Sort Amount ASC', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort-amount-up-alt',
				'name'  => __( 'Sort Amount DESC', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort-numeric-down-alt',
				'name'  => __( 'Sort Numeric ASC', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sort-numeric-up-alt',
				'name'  => __( 'Sort Numeric DESC', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-star',
				'name'  => __( 'Star', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-star-half',
				'name'  => __( 'Star Half', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-sticky-note',
				'name'  => __( 'Sticky Note', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-street-view',
				'name'  => __( 'Street View', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-suitcase',
				'name'  => __( 'Suitcase', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tablet',
				'name'  => __( 'Tablet', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tachometer',
				'name'  => __( 'Tachometer', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tag',
				'name'  => __( 'Tag', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tags',
				'name'  => __( 'Tags', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tasks',
				'name'  => __( 'Tasks', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tv',
				'name'  => __( 'Television', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-terminal',
				'name'  => __( 'Terminal', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-thumbtack',
				'name'  => __( 'Thumb Tack', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-thumbs-down',
				'name'  => __( 'Thumbs Down', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-thumbs-up',
				'name'  => __( 'Thumbs Up', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-ticket',
				'name'  => __( 'Ticket', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-times',
				'name'  => __( 'Times', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-times-circle',
				'name'  => __( 'Times', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tint',
				'name'  => __( 'Tint', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-toggle-off',
				'name'  => __( 'Toggle Off', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-toggle-on',
				'name'  => __( 'Toggle On', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-trademark',
				'name'  => __( 'Trademark', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-trash',
				'name'  => __( 'Trash', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tree',
				'name'  => __( 'Tree', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-trophy',
				'name'  => __( 'Trophy', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-tty',
				'name'  => __( 'TTY', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-umbrella',
				'name'  => __( 'Umbrella', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-university',
				'name'  => __( 'University', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-unlock',
				'name'  => __( 'Unlock', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-unlock-alt',
				'name'  => __( 'Unlock', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-upload',
				'name'  => __( 'Upload', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-user',
				'name'  => __( 'User', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-user-circle',
				'name'  => __( 'User', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-users',
				'name'  => __( 'Users', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-user-plus',
				'name'  => __( 'User: Add', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-user-times',
				'name'  => __( 'User: Remove', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-user-secret',
				'name'  => __( 'User: Password', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-volume-down',
				'name'  => __( 'Volume Down', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-volume-off',
				'name'  => __( 'Volume Of', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-volume-up',
				'name'  => __( 'Volume Up', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-wifi',
				'name'  => __( 'WiFi', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-window-close',
				'name'  => __( 'Window Close', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-window-maximize',
				'name'  => __( 'Window Maximize', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-window-minimize',
				'name'  => __( 'Window Minimize', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-window-restore',
				'name'  => __( 'Window Restore', 'reign' ),
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa fa-wrench',
				'name'  => __( 'Wrench', 'reign' ),
			),
		);

		/**
		 * Filter Reign items
		 *
		 * @since 0.1.0
		 *
		 * @param array $items Icon names.
		 */
		$items = apply_filters( 'icon_picker_reign_items', $items );

		return $items;
	}
}

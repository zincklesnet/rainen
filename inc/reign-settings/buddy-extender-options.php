<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'Reign_Buddy_Extender_Options' ) ) :

	/**
	 * @class Reign_Buddy_Extender_Options
	 */
	class Reign_Buddy_Extender_Options {


		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Buddy_Extender_Options
		 */
		protected static $_instance = null;
		protected static $_slug     = 'buddy_extender';

		/**
		 * Main Reign_Buddy_Extender_Options Instance.
		 *
		 * Ensures only one instance of Reign_Buddy_Extender_Options is loaded or can be loaded.
		 *
		 * @return Reign_Buddy_Extender_Options - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Buddy_Extender_Options Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_filter( 'alter_reign_admin_tabs', array( $this, 'alter_reign_admin_tabs' ), 10, 1 );
			add_action( 'render_theme_options_page_for_' . self::$_slug, array( $this, 'render_theme_options' ) );

			add_action( 'render_theme_options_for_avatar_settings', array( $this, 'render_theme_options_for_avatar_settings' ) );
			add_action( 'render_theme_options_for_advanced_settings', array( $this, 'render_theme_options_for_advanced_settings' ) );

			// add_action( 'render_theme_options_for_group_cover_image', array( $this, 'render_theme_options_for_group_cover_image' ) );
			// add_action( 'render_theme_options_for_xprofile_cover_image', array( $this, 'render_theme_options_for_xprofile_cover_image' ) );

			// add_action( 'render_theme_options_for_activity_action_control', array( $this, 'render_theme_options_for_activity_action_control' ) );
			//add_action( 'render_theme_options_for_xprofile_social_links', array( $this, 'render_theme_options_for_xprofile_social_links' ) );

			add_action( 'render_theme_options_for_bp_layout_mgmt', array( $this, 'render_theme_options_for_bp_layout_mgmt' ) );

			add_action( 'wp_loaded', array( $this, 'save_reign_theme_settings' ) );
		}

		public function alter_reign_admin_tabs( $tabs ) {
			$tabs[ self::$_slug ] = __( 'Community Settings', 'reign' );
			return $tabs;
		}

		public function render_theme_options() {

			$vertical_tabs = array(
				'avatar_settings'   => __( 'Avatar Settings', 'reign' ),
				'bp_layout_mgmt'    => __( 'Member/Group Header Layout', 'reign' ),
				'advanced_settings' => __( 'Advanced Settings', 'reign' ),
				// 'group_cover_image'       => __( 'Default Group Cover Image', 'reign' ),
				// 'xprofile_cover_image'    => __( 'Default Profile Cover Image', 'reign' ),
				// 'activity_action_control' => __( 'Activity Control', 'reign' ),
				// 'xprofile_social_links'   => __( 'Social Media Links', 'reign' ),
			);

			if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
				// Remove 'avatar_settings' if bb platform is active.
				unset( $vertical_tabs['avatar_settings'] );

				// Apply filter to conditionally re-enable the 'avatar_settings' tab.
				$vertical_tabs = apply_filters( 'wbtm_restore_avatar_settings_tab', $vertical_tabs );
			}

			$vertical_tabs = apply_filters( 'wbtm_' . self::$_slug . '_vertical_tabs', $vertical_tabs );
			include REIGN_INC_DIR . 'reign-settings/vertical-tabs-skeleton.php';
		}

		/**
		 * Renders the theme options for BuddyPress layout management.
		 */
		public function render_theme_options_for_bp_layout_mgmt() {
			global $wbtm_reign_settings;
			$member_header_position = isset( $wbtm_reign_settings['reign_buddyextender']['member_header_position'] ) ? $wbtm_reign_settings['reign_buddyextender']['member_header_position'] : 'inside';
			$member_header_type     = isset( $wbtm_reign_settings['reign_buddyextender']['member_header_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['member_header_type'] : 'wbtm-cover-header-type-1';
			$group_header_type      = isset( $wbtm_reign_settings['reign_buddyextender']['group_header_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['group_header_type'] : 'wbtm-cover-header-type-1';
			$member_directory_type  = isset( $wbtm_reign_settings['reign_buddyextender']['member_directory_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['member_directory_type'] : 'wbtm-member-directory-type-2';
			$group_directory_type   = isset( $wbtm_reign_settings['reign_buddyextender']['group_directory_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['group_directory_type'] : 'wbtm-group-directory-type-2';
			?>
			<table class="form-table">
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Header Position', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Select single member and group page header position', 'reign' ); ?>
						</div>
					</td>
					<td>
						<?php
						$member_header_positions = array(
							'inside' => array(
								'name'    => 'Inside',
								'img_url' => '',
							),
							'top'    => array(
								'name'    => 'Top',
								'img_url' => '',
							),
						);
						echo '<select name="reign_buddyextender[member_header_position]">';
						foreach ( $member_header_positions as $slug => $position ) {
							echo '<option value="' . esc_attr( $slug ) . '" ' . selected( $member_header_position, $slug ) . '>' . esc_html( $position['name'] ) . '</option>';
						}
						echo '</select>';
						?>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="enable_profile_header_view" class="rtm-tooltip-label">
								<?php esc_html_e( 'Allow Members To Switch Header Options', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Enable profile header position options in the frontend for members', 'reign' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="reign_buddyextender[enable_profile_header_view]" value="on" <?php isset( $wbtm_reign_settings['reign_buddyextender']['enable_profile_header_view'] ) ? checked( $wbtm_reign_settings['reign_buddyextender']['enable_profile_header_view'], 'on' ) : ''; ?>>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Member Header Layout', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Select single member page header layout', 'reign' ); ?>
						</div>
					</td>
					<td>
						<?php
						$member_header_types = array(
							'wbtm-cover-header-type-1' => array(
								'name'    => __( 'Layout #1', 'reign' ),
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-1.jpg',
							),
							'wbtm-cover-header-type-2' => array(
								'name'    => __( 'Layout #2', 'reign' ),
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-2.jpg',
							),
							'wbtm-cover-header-type-3' => array(
								'name'    => __( 'Layout #3', 'reign' ),
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-3.jpg',
							),
							'wbtm-cover-header-type-4' => array(
								'name'    => __( 'Layout #4', 'reign' ),
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-4.jpg',
							),
						);
						$member_header_types = apply_filters( 'reign_member_header_types_layout_options', $member_header_types );

						echo '<div class="wbtm-radio-img-selector-sec">';
						echo '<ul>';
						foreach ( $member_header_types as $slug => $header ) {
							echo '<li>';
							echo '<input type="radio" name="reign_buddyextender[member_header_type]" value="' . esc_attr( $slug ) . '" id="member-' . esc_attr( $slug ) . '" ' . checked( $member_header_type, $slug, false ) . ' />';
							echo '<label for="member-' . esc_attr( $slug ) . '"><img src="' . esc_url( $header['img_url'] ) . '" alt="" /><span>' . esc_html( $header['name'] ) . '</span></label>';
							echo '</li>';
						}
						echo '</ul>';
						echo '</div>';
						?>
						<?php if ( function_exists( 'bb_platform_pro' ) ) : ?>
							<div class="rtm-profile-layout-override-wrapper">
								<div class="rtm-tooltip-wrap">
									<label class="rtm-tooltip-label">
										<?php esc_html_e( 'Override Member Header Layout', 'reign' ); ?>
									</label>
								</div>
								<div class="rtm-tooltiptext">
									<?php esc_html_e( 'Override the BuddyBoss Platform Pro \'Header Style\' setting to apply the theme\'s member layouts.', 'reign' ); ?>
								</div>
								<div class="rtm-profile-layout-override">
									<input type="checkbox" name="reign_buddyextender[override_member_header_layout]" value="on" <?php isset( $wbtm_reign_settings['reign_buddyextender']['override_member_header_layout'] ) ? checked( $wbtm_reign_settings['reign_buddyextender']['override_member_header_layout'], 'on' ) : ''; ?>>
								</div>
							</div>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Group Header Layout', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Select single group page header layout', 'reign' ); ?>
						</div>
					</td>
					<td>
						<?php
						$group_header_types = array(
							'wbtm-cover-header-type-1' => array(
								'name'    => __( 'Layout #1', 'reign' ),
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-1.jpg',
							),
							'wbtm-cover-header-type-2' => array(
								'name'    => __( 'Layout #2', 'reign' ),
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-2.jpg',
							),
							'wbtm-cover-header-type-3' => array(
								'name'    => __( 'Layout #3', 'reign' ),
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-3.jpg',
							),
							'wbtm-cover-header-type-4' => array(
								'name'    => __( 'Layout #4', 'reign' ),
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/header-design-4.jpg',
							),
						);
						$group_header_types = apply_filters( 'reign_group_header_types_layout_options', $group_header_types );

						echo '<div class="wbtm-radio-img-selector-sec">';
						echo '<ul>';
						foreach ( $group_header_types as $slug => $header ) {
							echo '<li>';
							echo '<input type="radio" name="reign_buddyextender[group_header_type]" value="' . esc_attr( $slug ) . '" id="group-' . esc_attr( $slug ) . '" ' . checked( $group_header_type, $slug, false ) . ' />';
							echo '<label for="group-' . esc_attr( $slug ) . '"><img src="' . esc_url( $header['img_url'] ) . '" alt="" /><span>' . esc_html( $header['name'] ) . '</span></label>';
							echo '</li>';
						}
						echo '</ul>';
						echo '</div>';
						?>
						<?php if ( function_exists( 'bb_platform_pro' ) ) : ?>
							<div class="rtm-profile-layout-override-wrapper">
								<div class="rtm-tooltip-wrap">
									<label class="rtm-tooltip-label">
										<?php esc_html_e( 'Override Group Header Layout', 'reign' ); ?>
									</label>
								</div>
								<div class="rtm-tooltiptext">
									<?php esc_html_e( 'Override the BuddyBoss Platform Pro \'Header Style\' setting to apply the theme\'s group layouts.', 'reign' ); ?>
								</div>
								<div class="rtm-profile-layout-override">
									<input type="checkbox" name="reign_buddyextender[override_group_header_layout]" value="on" <?php isset( $wbtm_reign_settings['reign_buddyextender']['override_group_header_layout'] ) ? checked( $wbtm_reign_settings['reign_buddyextender']['override_group_header_layout'], 'on' ) : ''; ?>>
								</div>
							</div>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Member\'s Directory Layout', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Choose how members appear in the directory', 'reign' ); ?>
						</div>
					</td>
					<td>
						<?php
						$member_directory_types = array(
							'wbtm-member-directory-type-1' => array(
								'name'    => 'Layout #1',
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/member-layout-1.jpg',
							),
							'wbtm-member-directory-type-2' => array(
								'name'    => 'Layout #2',
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/member-layout-2.jpg',
							),
							'wbtm-member-directory-type-3' => array(
								'name'    => 'Layout #3',
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/member-layout-3.jpg',
							),
							'wbtm-member-directory-type-4' => array(
								'name'    => 'Layout #4',
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/member-layout-4.jpg',
							),
						);

						echo '<div class="wbtm-radio-img-selector-sec">';
						echo '<ul>';
						foreach ( $member_directory_types as $slug => $directory ) {
							echo '<li>';
							echo '<input type="radio" name="reign_buddyextender[member_directory_type]" value="' . esc_attr( $slug ) . '" id="member-dir-' . esc_attr( $slug ) . '" ' . checked( $member_directory_type, $slug, false ) . ' />';
							echo '<label for="member-dir-' . esc_attr( $slug ) . '"><img src="' . esc_url( $directory['img_url'] ) . '" class="rtm-tooltip-label" alt="" /><span>' . esc_html( $directory['name'] ) . '</span></label>';
							echo '</li>';
						}
						echo '</ul>';
						echo '</div>';
						?>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Group\'s Directory Layout', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Choose how groups appear in the directory', 'reign' ); ?>
						</div>
					</td>
					<td>
						<?php
						$group_directory_types = array(
							'wbtm-group-directory-type-1' => array(
								'name'    => 'Layout #1',
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/group-layout-1.jpg',
							),
							'wbtm-group-directory-type-2' => array(
								'name'    => 'Layout #2',
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/group-layout-2.jpg',
							),
							'wbtm-group-directory-type-3' => array(
								'name'    => 'Layout #3',
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/group-layout-3.jpg',
							),
							'wbtm-group-directory-type-4' => array(
								'name'    => 'Layout #4',
								'img_url' => REIGN_INC_DIR_URI . 'reign-settings/imgs/group-layout-4.jpg',
							),
						);

						echo '<div class="wbtm-radio-img-selector-sec">';
						echo '<ul>';
						foreach ( $group_directory_types as $slug => $directory ) {
							echo '<li>';
							echo '<input type="radio" name="reign_buddyextender[group_directory_type]" value="' . esc_attr( $slug ) . '" id="group-dir-' . esc_attr( $slug ) . '" ' . checked( $group_directory_type, $slug, false ) . ' />';
							echo '<label for="group-dir-' . esc_attr( $slug ) . '"><img src="' . esc_url( $directory['img_url'] ) . '" class="rtm-tooltip-label" alt="" /><span>' . esc_html( $directory['name'] ) . '</span></label>';
							echo '</li>';
						}
						echo '</ul>';
						echo '</div>';
						?>
					</td>
				</tr>

				
			</table>
			<?php
		}

		public function render_theme_options_for_avatar_settings() {
			global $wbtm_reign_settings;
			?>
			<table class="form-table">             
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="avatar_default_image" class="rtm-tooltip-label">
								<?php esc_html_e( 'Default User Avatar', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Upload an image that displays before a user has added a custom image.', 'reign' ); ?>
						</div>
					</td>
					<td>
						<?php
						$img_id  = isset( $wbtm_reign_settings['reign_buddyextender']['avatar_default_image_id'] ) ? $wbtm_reign_settings['reign_buddyextender']['avatar_default_image_id'] : '';
						$img_src = isset( $wbtm_reign_settings['reign_buddyextender']['avatar_default_image'] ) ? $wbtm_reign_settings['reign_buddyextender']['avatar_default_image'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-mem-avatar.png';
						if ( empty( $img_src ) ) {
							$img_src = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-mem-avatar.png';
						}

						$image_inline_style  = 'width:150px;height:150px;object-fit-cover;';
						$remove_inline_style = '';
						if ( empty( $img_src ) ) {
							$image_inline_style  .= 'display:none;';
							$remove_inline_style .= 'display:none;';
						}
						echo '<p>';
						echo '<input type="hidden" class="reign-upload-file" name="reign_buddyextender[avatar_default_image]" id="avatar_default_image" value="' . esc_attr( $img_src ) . '" size="45" data-previewsize="[350,350]">';
						echo '<input type="hidden" class="reign-upload-file-id" name="reign_buddyextender[avatar_default_image_id]" id="avatar_default_image_id" value="' . esc_attr( $img_id ) . '">';
						echo '<img class="reign-default-thumb reign_default_cover_image reign_default_avatar_image" src="' . esc_url( $img_src ) . '" style="' . esc_attr( $image_inline_style ) . '" alt="" />';
						echo '<a href="#" class="button button-link-delete reign-remove-file-button" rel="avatar_default_image" style="' . esc_attr( $remove_inline_style ) . '">' . esc_html__( 'Remove Image', 'reign' ) . '</a>';
						echo '<input id="reign-upload-button" type="button" class="button reign-upload-button" value="' . esc_attr__( 'Upload Image', 'reign' ) . '" />';
						echo '</p>';

						?>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="group_default_image" class="rtm-tooltip-label">
								<?php esc_html_e( 'Default Group Avatar', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Upload an image that displays before a custom image is added for a group.', 'reign' ); ?>
						</div>
					</td>
					<td>
						<?php
						$img_id  = isset( $wbtm_reign_settings['reign_buddyextender']['group_default_image_id'] ) ? $wbtm_reign_settings['reign_buddyextender']['group_default_image_id'] : '';
						$img_src = isset( $wbtm_reign_settings['reign_buddyextender']['group_default_image'] ) ? $wbtm_reign_settings['reign_buddyextender']['group_default_image'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-grp-avatar.png';
						if ( empty( $img_src ) ) {
							$img_src = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-grp-avatar.png';
						}

						$image_inline_style  = 'width:150px;height:150px;object-fit-cover;';
						$remove_inline_style = '';
						if ( empty( $img_src ) ) {
							$image_inline_style  .= 'display:none;';
							$remove_inline_style .= 'display:none;';
						}

						echo '<p>';
						echo '<input type="hidden" class="reign-upload-file" name="reign_buddyextender[group_default_image]" id="group_default_image" value="' . esc_attr( $img_src ) . '" size="45" data-previewsize="[350,350]">';
						echo '<input type="hidden" class="reign-upload-file-id" name="reign_buddyextender[group_default_image_id]" id="group_default_image_id" value="' . esc_attr( $img_id ) . '">';
						echo '<img class="reign-default-thumb reign_default_cover_image reign_default_avatar_image" src="' . esc_url( $img_src ) . '" style="' . esc_attr( $image_inline_style ) . '" alt="" />';
						echo '<a href="#" class="button button-link-delete reign-remove-file-button" rel="group_default_image" style="' . esc_attr( $remove_inline_style ) . '">' . esc_html__( 'Remove Image', 'reign' ) . '</a>';
						echo '<input id="reign-upload-button" type="button" class="button reign-upload-button" value="' . esc_attr__( 'Upload Image', 'reign' ) . '" />';
						echo '</p>';

						?>
					</td>
				</tr>
			</table>
			
			<?php $this->render_theme_options_for_xprofile_cover_image(); ?>
			<?php $this->render_theme_options_for_group_cover_image(); ?>

			<?php
		}

		public function render_theme_options_for_advanced_settings() {
			global $wbtm_reign_settings;
			?>
			<table class="form-table">
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="members_per_page" class="rtm-tooltip-label">
								<?php esc_html_e( 'Members per page', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Here you can manage the number of members to show per page.', 'reign' ); ?>
						</div>
					</td>
					<td>
						<input type="number" name="reign_buddyextender[members_per_page]" value="<?php echo esc_attr( isset( $wbtm_reign_settings['reign_buddyextender']['members_per_page'] ) ? $wbtm_reign_settings['reign_buddyextender']['members_per_page'] : '21' ); ?>">
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="groups_per_page" class="rtm-tooltip-label">
								<?php esc_html_e( 'Groups per page', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Here you can manage the number of groups to show per page.', 'reign' ); ?>
						</div>
					</td>
					<td>
						<input type="number" name="reign_buddyextender[groups_per_page]" value="<?php echo esc_attr( isset( $wbtm_reign_settings['reign_buddyextender']['groups_per_page'] ) ? $wbtm_reign_settings['reign_buddyextender']['groups_per_page'] : '21' ); ?>">
					</td>
				</tr>
			</table>

			<?php $this->render_theme_options_for_activity_action_control(); ?>
			<?php $this->render_theme_options_for_xprofile_social_links(); ?>
			
			<?php
		}

		public function render_theme_options_for_group_cover_image() {
			global $wbtm_reign_settings;
			$default_group_cover_image_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';

			if ( empty( $default_group_cover_image_url ) ) {
				$default_group_cover_image_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
			}

			$image_inline_style  = 'width:150px;height:100px;object-fit-cover;';
			$remove_inline_style = '';
			if ( empty( $default_group_cover_image_url ) ) {
				$image_inline_style  .= 'display:none;';
				$remove_inline_style .= 'display:none;';
			}

			echo '<table class="form-table">';
			echo '<tr>';
			echo '<td class="rtm-left-side">';
			echo '<div class="rtm-tooltip-wrap">';
			echo '<img src="' . esc_url( get_template_directory_uri() . '/assets/img/question.png' ) . '" class="rtm-tooltip-image" alt="tooltip-images" />';
			echo '<label class="rtm-tooltip-label">' . esc_html__( 'Default Group Cover Image', 'reign' ) . '</label>';
			echo '</div>';
			?>
			<div class="rtm-tooltiptext">
				<?php esc_html_e( 'Select an image to set as the Default Group Cover Image.', 'reign' ); ?>
			</div>
			<?php
			echo '</td>';
			echo '<td>';
			echo '<input class="reign_default_cover_image_url" type="hidden" name="reign_buddyextender[default_group_cover_image_url]" value="' . esc_url( $default_group_cover_image_url ) . '" />';
			echo '<img class="reign-default-thumb reign_default_cover_image" src="' . esc_url( $default_group_cover_image_url ) . '" style="' . esc_attr( $image_inline_style ) . '" alt="" />';
			echo '<a href="#" class="button button-link-delete reign-remove-file-button" rel="avatar_default_image" style="' . esc_attr( $remove_inline_style ) . '">' . esc_html__( 'Remove Image', 'reign' ) . '</a>';
			echo '<input id="reign-upload-button" type="button" class="button reign-upload-button" value="' . esc_attr__( 'Upload Image', 'reign' ) . '" />';
			echo '</td>';

			echo '</tr>';
			echo '</table>';
		}

		public function render_theme_options_for_xprofile_cover_image() {
			global $wbtm_reign_settings;
			$default_xprofile_cover_image_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
			if ( empty( $default_xprofile_cover_image_url ) ) {
				$default_xprofile_cover_image_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
			}

			$image_inline_style  = 'width:150px;height:100px;object-fit-cover;';
			$remove_inline_style = '';
			if ( empty( $default_xprofile_cover_image_url ) ) {
				$image_inline_style  .= 'display:none;';
				$remove_inline_style .= 'display:none;';
			}

			echo '<table class="form-table">';
			echo '<tr>';
			echo '<td class="rtm-left-side">';
			echo '<div class="rtm-tooltip-wrap">';
			echo '<img src="' . esc_url( get_template_directory_uri() . '/assets/img/question.png' ) . '" class="rtm-tooltip-image" alt="tooltip-images" />';
			echo '<label class="rtm-tooltip-label">' . esc_html__( 'Default Profile Cover Image', 'reign' ) . '</label>';
			echo '</div>';
			?>
			<div class="rtm-tooltiptext">
				<?php esc_html_e( 'Select an image to set as the Default Profile Cover Image.', 'reign' ); ?>
			</div>
			<?php
			echo '</td>';
			echo '<td>';
			echo '<input class="reign_default_cover_image_url" type="hidden" name="reign_buddyextender[default_xprofile_cover_image_url]" value="' . esc_url( $default_xprofile_cover_image_url ) . '" />';
			echo '<img class="reign-default-thumb reign_default_cover_image" src="' . esc_url( $default_xprofile_cover_image_url ) . '" style="' . esc_attr( $image_inline_style ) . '" alt="" />';
			echo '<a href="#" class="button button-link-delete reign-remove-file-button" rel="avatar_default_image" style="' . esc_attr( $remove_inline_style ) . '">' . esc_html__( 'Remove Image', 'reign' ) . '</a>';
			echo '<input id="reign-upload-button" type="button" class="button reign-upload-button" value="' . esc_attr__( 'Upload Image', 'reign' ) . '" />';
			echo '</td>';
			echo '</tr>';
			echo '</table>';
		}

		public function render_theme_options_for_activity_action_control() {
			global $wbtm_reign_settings;
			?>
			<table class="form-table">               
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="member_cover_image" class="rtm-tooltip-label">
								<?php esc_html_e( 'Enable member cover image activity', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Enable member cover image activity.', 'reign' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="reign_buddyextender[member_cover_image]" value="on" <?php isset( $wbtm_reign_settings['reign_buddyextender']['member_cover_image'] ) ? checked( $wbtm_reign_settings['reign_buddyextender']['member_cover_image'], 'on' ) : ''; ?>>
						<span class="description"><?php esc_html_e( 'Enable member cover image activity.', 'reign' ); ?></span>
					</td>
				</tr>
				
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="group_image" class="rtm-tooltip-label">
								<?php esc_html_e( 'Enable group image activity', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Enable group image activity.', 'reign' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="reign_buddyextender[group_image]" value="on" <?php isset( $wbtm_reign_settings['reign_buddyextender']['group_image'] ) ? checked( $wbtm_reign_settings['reign_buddyextender']['group_image'], 'on' ) : ''; ?>>
						<span class="description"><?php esc_html_e( 'Enable group image activity.', 'reign' ); ?></span>
					</td>
				</tr>
				
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="group_cover_image" class="rtm-tooltip-label">
								<?php esc_html_e( 'Enable group cover image activity', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Enable group cover image activity.', 'reign' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="reign_buddyextender[group_cover_image]" value="on" <?php isset( $wbtm_reign_settings['reign_buddyextender']['group_cover_image'] ) ? checked( $wbtm_reign_settings['reign_buddyextender']['group_cover_image'], 'on' ) : ''; ?>>
						<span class="description"><?php esc_html_e( 'Enable group cover image activity.', 'reign' ); ?></span>
					</td>
				</tr>
			</table>
			<?php
		}

		public function render_theme_options_for_xprofile_social_links() {
			global $wbtm_reign_settings;
			$wbtm_social_links = isset( $wbtm_reign_settings['reign_buddyextender']['wbtm_social_links'] ) ? $wbtm_reign_settings['reign_buddyextender']['wbtm_social_links'] : array();
			$unique_key        = time();
			$social_filed_name = array_column( $wbtm_social_links, 'name' );
			?>
			
			<table class="form-table">
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="enable_profile_social_links" class="rtm-tooltip-label">
								<?php esc_html_e( 'Enable Member Social Media Links', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Enable social media links options in the frontend for members', 'reign' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="reign_buddyextender[enable_profile_social_links]" value="on" <?php isset( $wbtm_reign_settings['reign_buddyextender']['enable_profile_social_links'] ) ? checked( $wbtm_reign_settings['reign_buddyextender']['enable_profile_social_links'], 'on' ) : ''; ?>>
					</td>
				</tr>
				<?php if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) : ?>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label for="enable_activity_like_avatars" class="rtm-tooltip-label">
								<?php esc_html_e( 'Enable Activity Like Avatars', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Show avatars of users who liked an activity in the activity stream', 'reign' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="reign_buddyextender[enable_activity_like_avatars]" value="on" <?php isset( $wbtm_reign_settings['reign_buddyextender']['enable_activity_like_avatars'] ) ? checked( $wbtm_reign_settings['reign_buddyextender']['enable_activity_like_avatars'], 'on' ) : ''; ?>>
					</td>
				</tr>
				<?php endif; ?>
			</table>

			<?php
			if ( ! empty( $wbtm_social_links ) && is_array( $wbtm_social_links ) && ! empty( $social_filed_name[0] ) ) {
				echo '<div class="wb-xprofile-social-links-wrapper-outer">';
				echo '<div class="wb-xprofile-social-links-wrapper">';
				foreach ( $wbtm_social_links as $unique_key => $social_link ) {
					$display_none = '';
					$image_link   = '';
					if ( empty( $social_link['img_url'] ) ) {
						$display_none = 'display: none;';
					} else {
						$image_link = $social_link['img_url'];
					}
					?>
					<div class="wbtm_social_links_container">
						<div class="wbtm_social_link_section">
							<h3 class="wbtm_social_link_toggle_head">
								<?php echo esc_html( $social_link['name'] ); ?>
							</h3>
							<div class="wbtm_social_link_info_box">
								<div class="img_section">
									<input class="reign_default_cover_image_url" type="hidden" name="reign_buddyextender[wbtm_social_links][<?php echo esc_attr( $unique_key ); ?>][img_url]" value="<?php echo esc_url( $image_link ); ?>" required="required" />
									<img class="reign_default_cover_image" src="<?php echo esc_url( $image_link ); ?>" style="<?php echo esc_attr( $display_none ); ?>" alt="" />
									<input id="reign-upload-button" type="button" class="button reign-upload-button" value="<?php esc_attr_e( 'Upload Icon', 'reign' ); ?>" />
									<a href="#" class="reign-remove-file-button" rel="avatar_default_image" style="<?php echo esc_attr( $display_none ); ?>" >
										<?php esc_html_e( 'Remove Icon', 'reign' ); ?>
									</a>
								</div>
								<div class="name_section">
									<input type="text" class="wbtm-social-link-inp" name="reign_buddyextender[wbtm_social_links][<?php echo esc_attr( $unique_key ); ?>][name]" placeholder="<?php esc_attr_e( 'New Site', 'reign' ); ?>" value="<?php echo esc_attr( $social_link['name'] ); ?>" required="required" />
								</div>
								<div class="del_section">
									<button><?php esc_html_e( 'Delete', 'reign' ); ?></button>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				echo '</div>';
				echo '<div class="wbtm_social_links_add_more">';
				echo '<button>' . esc_html__( 'Add New Site', 'reign' ) . '</button>';
				echo '</div>';
				echo '</div>';
			} else {
				?>
				<div class="wb-xprofile-social-links-wrapper-outer">
					<div class="wb-xprofile-social-links-wrapper">
						<div class="wbtm_social_links_container">
							<div class="wbtm_social_link_section">
								<h3 class="wbtm_social_link_toggle_head">
									<?php esc_html_e( 'New Site', 'reign' ); ?>
								</h3>
								<div class="wbtm_social_link_info_box">
									<div class="img_section">
										<input class="reign_default_cover_image_url" type="hidden" name="reign_buddyextender[wbtm_social_links][<?php echo esc_attr( $unique_key ); ?>][img_url]" value="" />
										<img class="reign_default_cover_image" src="" style="display: none;" alt="" />
										<input id="reign-upload-button" type="button" class="button reign-upload-button" value="<?php esc_attr_e( 'Upload Image', 'reign' ); ?>" />
										<a href="#" class="reign-remove-file-button" rel="avatar_default_image" style="display: none;" >
											<?php esc_html_e( 'Remove Image', 'reign' ); ?>
										</a>
									</div>
									<div class="name_section">
										<input type="text" name="reign_buddyextender[wbtm_social_links][<?php echo esc_attr( $unique_key ); ?>][name]" placeholder="<?php esc_attr_e( 'New Site', 'reign' ); ?>" />
									</div>
									<div class="del_section">
										<button><?php esc_html_e( 'Delete', 'reign' ); ?></button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="wbtm_social_links_add_more">
						<button><?php esc_html_e( 'Add New Site', 'reign' ); ?></button>
					</div>
				</div>
				<?php
			}
			?>
			
			<?php
		}

		public function save_reign_theme_settings() {
			if ( isset( $_POST['reign-settings-submit'] ) && $_POST['reign-settings-submit'] == 'Y' ) {
				check_admin_referer( 'reign-options' );
				global $wbtm_reign_settings;
				if ( isset( $_POST['reign_buddyextender'] ) ) {
					$wbtm_reign_settings['reign_buddyextender'] = reign_sanitize_extender_settings( $_POST['reign_buddyextender'] );
				}
				update_option( 'reign_options', $wbtm_reign_settings );
				$wbtm_reign_settings = get_option( 'reign_options', array() );
			}
		}
	}

endif;

/**
 * Main instance of Reign_Buddy_Extender_Options.
 *
 * @return Reign_Buddy_Extender_Options
 */
if ( class_exists( 'BuddyPress' ) ) {
	Reign_Buddy_Extender_Options::instance();
}

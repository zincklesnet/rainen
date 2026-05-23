<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'Reign_PeepSo_Extender_Options' ) ) :

	/**
	 * @class Reign_PeepSo_Extender_Options
	 */
	class Reign_PeepSo_Extender_Options {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_PeepSo_Extender_Options
		 */
		protected static $_instance = null;
		protected static $_slug     = 'peepso_extender';

		/**
		 * Main Reign_PeepSo_Extender_Options Instance.
		 *
		 * Ensures only one instance of Reign_PeepSo_Extender_Options is loaded or can be loaded.
		 *
		 * @return Reign_PeepSo_Extender_Options - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_PeepSo_Extender_Options Constructor.
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

			add_action( 'render_theme_options_for_peepso_profile_cover_image', array( $this, 'render_theme_options_peepso_profile_cover_image' ) );

			add_action( 'render_theme_options_for_peepso_profile_social_links', array( $this, 'render_theme_options_for_profile_social_links' ) );

			add_action( 'render_theme_options_for_peepso_profile_page_settings', array( $this, 'render_theme_options_for_peepso_profile_page_settings' ) );
			add_action( 'render_theme_options_for_peepso_group_page_settings', array( $this, 'render_theme_options_for_peepso_group_page_settings' ) );

			add_action( 'wp_loaded', array( $this, 'save_reign_theme_settings' ) );
		}

		public function alter_reign_admin_tabs( $tabs ) {
			$tabs[ self::$_slug ] = esc_html__( 'PeepSo Settings', 'reign' );
			return $tabs;
		}

		public function render_theme_options() {
			$vertical_tabs = array(
				'peepso_profile_cover_image'   => esc_html__( 'Profile/Group Cover Image', 'reign' ),
				'peepso_profile_social_links'  => esc_html__( 'Social Media Links', 'reign' ),
				'peepso_profile_page_settings' => esc_html__( 'PeepSo Profile Settings', 'reign' ),
				'peepso_group_page_settings'   => esc_html__( 'PeepSo Group Settings', 'reign' ),
			);
			$vertical_tabs = apply_filters( 'wbtm_' . self::$_slug . '_vertical_tabs', $vertical_tabs );
			include REIGN_INC_DIR . 'reign-settings/vertical-tabs-skeleton.php';
		}

		public function render_theme_options_for_peepso_group_page_settings() {
			global $wbtm_reign_settings;
			$group_layout = isset( $wbtm_reign_settings['reign_peepsoextender']['group_layout'] ) ? $wbtm_reign_settings['reign_peepsoextender']['group_layout'] : 'full-width';
			$cover_height = isset( $wbtm_reign_settings['reign_peepsoextender']['group_cover_height'] ) ? $wbtm_reign_settings['reign_peepsoextender']['group_cover_height'] : '24';
			$group_avatar = isset( $wbtm_reign_settings['reign_peepsoextender']['centered_group_avatar'] ) ? $wbtm_reign_settings['reign_peepsoextender']['centered_group_avatar'] : 'no';
			?>
			<table class="form-table">
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'PeepSo Group Layout', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Select PeepSo group page layout', 'reign' ); ?>
						</div>
					</td>
					<td>
						<select name="reign_peepsoextender[group_layout]" id="peepso-gruop-layout">
							<option value="default" <?php selected( $group_layout, 'default', true ); ?>><?php esc_html_e( 'Default', 'reign' ); ?></option>
							<option value="wide" <?php selected( $group_layout, 'wide', true ); ?> ><?php esc_html_e( 'Wide Cover', 'reign' ); ?></option>
							<option value="full-width" <?php selected( $group_layout, 'full-width', true ); ?> ><?php esc_html_e( 'Full Width', 'reign' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Group Cover Height', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Set group cover height. By default it will be 50%', 'reign' ); ?>
						</div>
					</td>
					<td>
						<div class="reign-slidecontainer">
							<div class="reign-slider-value"><?php echo esc_html( $cover_height ); ?></div>
							<input type="range" min="1" max="50" value="<?php echo esc_attr( $cover_height ); ?>" class="reign-slider" name="reign_peepsoextender[group_cover_height]">							
						</div>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Enable Center Avatar', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'This option will center profile avatars and details with action buttons', 'reign' ); ?>
						</div>
					</td>
					<td>
						<label class="wb-switch">
							<input type="checkbox" name="reign_peepsoextender[centered_group_avatar]" value="yes" <?php echo checked( $group_avatar, 'yes', true ); ?>>
							<div class="wb-slider wb-round"></div>
						</label>
					</td>
				</tr>
			</table>			
			<?php
		}

		public function render_theme_options_for_peepso_profile_page_settings() {
			global $wbtm_reign_settings;

			$profile_layout = isset( $wbtm_reign_settings['reign_peepsoextender']['profile_layout'] ) ? $wbtm_reign_settings['reign_peepsoextender']['profile_layout'] : 'full-width';
			$cover_height   = isset( $wbtm_reign_settings['reign_peepsoextender']['profile_cover_height'] ) ? $wbtm_reign_settings['reign_peepsoextender']['profile_cover_height'] : '24';
			$profile_avatar = isset( $wbtm_reign_settings['reign_peepsoextender']['centered_profile_avatar'] ) ? $wbtm_reign_settings['reign_peepsoextender']['centered_profile_avatar'] : 'no';
			?>
			<table class="form-table">
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Peepso Profile Page Layout', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Select profile page layout', 'reign' ); ?>
						</div>
					</td>
					<td>
						<select name="reign_peepsoextender[profile_layout]" id="peepso-profile-layout">
							<option value="default" <?php selected( $profile_layout, 'default', true ); ?>><?php esc_html_e( 'Default', 'reign' ); ?></option>
							<option value="wide" <?php selected( $profile_layout, 'wide', true ); ?> ><?php esc_html_e( 'Wide Cover', 'reign' ); ?></option>
							<option value="full-width" <?php selected( $profile_layout, 'full-width', true ); ?> ><?php esc_html_e( 'Full Width', 'reign' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Profile Cover Height', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Set profile cover height. By default it will be 50%', 'reign' ); ?>
						</div>
					</td>
					<td>
						<div class="reign-slidecontainer">
							<div class="reign-slider-value"><?php echo esc_html( $cover_height ); ?></div>
							<input type="range" min="1" max="50" value="<?php echo esc_attr( $cover_height ); ?>" class="reign-slider" name="reign_peepsoextender[profile_cover_height]">							
						</div>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'Tooltip Image', 'reign' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Enable Center Avatar', 'reign' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'This option will center profile avatars and details with action buttons', 'reign' ); ?>
						</div>
					</td>
					<td>
						<label class="wb-switch">
							<input type="checkbox" name="reign_peepsoextender[centered_profile_avatar]" value="yes" <?php echo checked( $profile_avatar, 'yes', true ); ?>>
							<div class="wb-slider wb-round"></div>
						</label>
					</td>
				</tr>
			</table>			
			<?php
		}

		/**
		 * Renders the theme options for profile social links in the admin settings.
		 *
		 * @global array $wbtm_reign_settings The global theme settings array.
		 */
		public function render_theme_options_for_profile_social_links() {
			global $wbtm_reign_settings;
			$wbtm_social_links = isset( $wbtm_reign_settings['reign_peepsoextender']['wbtm_social_links'] ) ? $wbtm_reign_settings['reign_peepsoextender']['wbtm_social_links'] : array();
			$unique_key        = time();
			if ( ! empty( $wbtm_social_links ) && is_array( $wbtm_social_links ) ) {
				echo '<div class="wb-xprofile-social-links-wrapper-outer">';
				echo '<div class="wb-xprofile-social-links-wrapper">';
				foreach ( $wbtm_social_links as $unique_key => $social_link ) {
					$display_none = empty( $social_link['img_url'] ) ? 'display: none;' : '';
					?>
					<div class="wbtm_social_links_container">
						<div class="wbtm_social_link_section">
							<h3 class="wbtm_social_link_toggle_head">
								<?php echo esc_html( $social_link['name'] ); ?>
							</h3>
							<div class="wbtm_social_link_info_box">
								<div class="img_section">
									<input class="reign_default_cover_image_url" type="hidden" name="reign_peepsoextender[wbtm_social_links][<?php echo esc_attr( $unique_key ); ?>][img_url]" value="<?php echo esc_attr( $social_link['img_url'] ); ?>" required="required" />
									<img class="reign_default_cover_image" src="<?php echo esc_url( $social_link['img_url'] ); ?>" alt="<?php esc_attr_e( 'Social link icon preview', 'reign' ); ?>" style="<?php echo esc_attr( $display_none ); ?>" />
									<input id="reign-upload-button" type="button" class="button reign-upload-button" value="<?php esc_attr_e( 'Upload Icon', 'reign' ); ?>" />
									<a href="#" class="reign-remove-file-button" rel="avatar_default_image" style="<?php echo esc_attr( $display_none ); ?>" >
										<?php esc_html_e( 'Remove Icon', 'reign' ); ?>
									</a>
								</div>
								<div class="name_section">
									<input type="text" name="reign_peepsoextender[wbtm_social_links][<?php echo esc_attr( $unique_key ); ?>][name]" placeholder="<?php esc_attr_e( 'New Site', 'reign' ); ?>" value="<?php echo esc_attr( $social_link['name'] ); ?>" required="required" />
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
						</div>
					</div>
					<div class="wbtm_social_links_add_more">
						<button><?php esc_html_e( 'Add New Site', 'reign' ); ?></button>
					</div>
				</div>	
				<?php
			}
		}

		public function render_theme_options_peepso_group_cover_image() {
			global $wbtm_reign_settings;
			$default_group_cover_image_url = isset( $wbtm_reign_settings['reign_peepsoextender']['default_group_cover_image_url'] ) ? $wbtm_reign_settings['reign_peepsoextender']['default_group_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';

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
				<?php esc_html_e( 'Choose the default cover image for group pages', 'reign' ); ?>
			</div>
			<?php
			echo '</td>';
			echo '<td>';
			echo '<input class="reign_default_cover_image_url" type="hidden" name="reign_peepsoextender[default_group_cover_image_url]" value="' . esc_url( $default_group_cover_image_url ) . '" />';
			echo '<img class="reign_default_cover_image" src="' . esc_url( $default_group_cover_image_url ) . '" alt="' . esc_attr__( 'Default group cover image preview', 'reign' ) . '" style="' . esc_attr( $image_inline_style ) . '" />';
			echo '<a href="#" class="reign-remove-file-button" rel="avatar_default_image" style="' . esc_attr( $remove_inline_style ) . '" >' . esc_html__( 'Remove Image', 'reign' ) . '</a>';
			echo '<input id="reign-upload-button" type="button" class="button reign-upload-button" value="' . esc_attr__( 'Upload Image', 'reign' ) . '" />';
			echo '</td>';
			echo '</tr>';
			echo '</table>';
		}

		public function render_theme_options_peepso_profile_cover_image() {
			global $wbtm_reign_settings;
			$default_profile_cover_image_url = isset( $wbtm_reign_settings['reign_peepsoextender']['default_profile_cover_image_url'] ) ? $wbtm_reign_settings['reign_peepsoextender']['default_profile_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
			if ( empty( $default_profile_cover_image_url ) ) {
				$default_profile_cover_image_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
			}
			$default_profile_cover_image_size = isset( $wbtm_reign_settings['reign_peepsoextender']['default_profile_cover_image_size'] ) ? $wbtm_reign_settings['reign_peepsoextender']['default_profile_cover_image_size'] : '';
			$image_inline_style               = 'width:150px;height:100px;object-fit-cover;';
			$remove_inline_style              = '';
			if ( empty( $default_profile_cover_image_url ) ) {
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
				<?php esc_html_e( 'Select image to set as Default Profile Cover Image for single member.', 'reign' ); ?>
			</div>
			<?php
			echo '</td>';
			echo '<td>';
			echo '<input class="reign_default_cover_image_url" type="hidden" name="reign_peepsoextender[default_profile_cover_image_url]" value="' . esc_url( $default_profile_cover_image_url ) . '" />';
			echo '<img class="reign_default_cover_image" src="' . esc_url( $default_profile_cover_image_url ) . '" alt="' . esc_attr__( 'Default profile cover image preview', 'reign' ) . '" style="' . esc_attr( $image_inline_style ) . '" />';
			echo '<a href="#" class="reign-remove-file-button" rel="avatar_default_image" style="' . esc_attr( $remove_inline_style ) . '" >' . esc_html__( 'Remove Image', 'reign' ) . '</a>';
			echo '<input id="reign-upload-button" type="button" class="button reign-upload-button" value="' . esc_attr__( 'Upload Image', 'reign' ) . '" />';
			echo '</td>';
			echo '</tr>';
			echo '</table>';

			$this->render_theme_options_peepso_group_cover_image();
		}

		/**
		 * Saves the Reign theme settings.
		 *
		 * This function handles the saving of theme settings for the Reign theme, specifically for PeepSo layout options.
		 */
		public function save_reign_theme_settings() {
			if ( isset( $_POST['reign-settings-submit'] ) && $_POST['reign-settings-submit'] == 'Y' ) {
				check_admin_referer( 'reign-options' );
				global $wbtm_reign_settings;
				if ( isset( $_POST['reign_peepsoextender'] ) ) {
					$wbtm_reign_settings['reign_peepsoextender'] = reign_sanitize_extender_settings( $_POST['reign_peepsoextender'] );
				}
				update_option( 'reign_options', $wbtm_reign_settings );
				$wbtm_reign_settings = get_option( 'reign_options', array() );
			}
		}

	}

	endif;

/**
 * Main instance of Reign_PeepSo_Extender_Options.
 *
 * @return Reign_PeepSo_Extender_Options
 */
if ( class_exists( 'PeepSo' ) ) {
	Reign_PeepSo_Extender_Options::instance();
}

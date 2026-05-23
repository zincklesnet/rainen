<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Reign_Tutorlms_Addon
 * @subpackage Reign_Tutorlms_Addon/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Reign_Tutorlms_Addon
 * @subpackage Reign_Tutorlms_Addon/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Reign_Tutorlms_Addon_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Plugin Slug.
	 *
	 * @var Reign_Tutorlms_Addon_Admin
	 */
	protected static $_slug = 'tutorlms';

	private $buddypress_group_settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Reign_Tutorlms_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Reign_Tutorlms_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$css_extension = '.css';
		} else {
			$css_extension = '.min.css';
		}

		$css_path = is_rtl() ? 'css/rtl' : 'css';

		if ( ! wp_style_is( 'wp-jquery-ui-dialog', 'enqueued' ) ) {
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . $css_path . '/reign-tutorlms-addon-admin' . $css_extension, array(), $this->version, 'all' );		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Reign_Tutorlms_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Reign_Tutorlms_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$js_extension = '.js';
		} else {
			$js_extension = '.min.js';
		}

		if ( ! wp_script_is( 'jquery-ui-dialog', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery-ui-dialog' );
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/reign-tutorlms-addon-admin' . $js_extension, array( 'jquery', 'jquery-ui-dialog' ), $this->version, true );
		$settings = get_option( 'reign_options', array() );
		if ( isset( $settings['tutorlms'] ) && ! array_key_exists( 'enable_group_integration', $settings['tutorlms'] ) ) {
			$reign_sensei_course_option = 'reign-tutor-course-activity';
		} else {
			$reign_sensei_course_option = '';
		}

		wp_localize_script(
			$this->plugin_name,
			'reignTutorLMSVars',
			array(
				'ajaxurl'                    => admin_url( 'admin-ajax.php', is_ssl() ? 'admin' : 'http' ),
				'dialog_ok_text'             => esc_html__( 'Proceed', 'reign-tutorlms-addon' ),
				'dialog_cancel_text'         => esc_html__( 'Cancel', 'reign-tutorlms-addon' ),
				'sync_text'                  => esc_html__( 'Sync', 'reign-tutorlms-addon' ),
				'completed_text'             => esc_html__( 'Completed', 'reign-tutorlms-addon' ),
				'select_course_placeholder'  => __( 'Start typing a course name to associate with this group.', 'reign-tutorlms-addon' ),
				'reign_sensei_course_option' => $reign_sensei_course_option,
				'ajaxnonce'                  => wp_create_nonce( 'reign_tutorlms_groups_course_admin_nonce' )
			)
		);

	}

	/**
	 * Add tutorlms tab on REIGN Setting tabs..
	 *
	 * @param array $tabs REIGN Setting tabs.
	 */
	public function reign_tutorlms_reign_settings_tab( $tabs ) {
		// Show TutorLMS tab if any social platform is available or if TutorLMS is active
		$show_tab = false;
		
		// Check for social platforms
		if ( class_exists( 'BuddyPress' ) || function_exists( 'bp_is_active' ) || class_exists( 'PeepSo' ) ) {
			$show_tab = true;
		}
		
		// Also show if TutorLMS is active (for shortcodes)
		if ( is_plugin_active( 'tutor/tutor.php' ) ) {
			$show_tab = true;
		}
		
		if ( $show_tab ) {
			$tabs[ self::$_slug ] = __( 'TutorLMS', 'reign-tutorlms-addon' );
		}

		return $tabs;
	}

	/**
	 * Render Theme Options.
	 */
	public function reign_tutorlms_render_reign_options() {
		global $wbtm_reign_settings;
		$vertical_tabs = array(
			'reign_tutorlms_shortcodes' => __( 'Shortcodes', 'reign-tutorlms-addon' ),
			'reign_tutorlms_profile'    => __( 'Profile Integration', 'reign-tutorlms-addon' ),
		);

		require_once __DIR__ . '/vertical-tabs-skeleton.php';
	}


	/**
	 * Render Shortcodes tab content
	 */
	public function reign_tutorlms_render_shortcodes_options() {
		?>
		<div class="reign-tutorlms-shortcodes-info">
			<div class="shortcode-section">
				<h3><?php _e( 'Enhanced TutorLMS Course Shortcode', 'reign-tutorlms-addon' ); ?></h3>
				<div class="shortcode-example">
					<code>[reign_tutor_course]</code>
				</div>
				<p><?php _e( 'This shortcode extends TutorLMS\'s native [tutor_course] shortcode with additional features for enrolled courses and profile integration.', 'reign-tutorlms-addon' ); ?></p>
				
				<h4><?php _e( 'Key Parameters:', 'reign-tutorlms-addon' ); ?></h4>
				<ul>
					<li><strong>my_courses="yes"</strong> - <?php _e( 'Show enrolled courses for current/profile user', 'reign-tutorlms-addon' ); ?></li>
					<li><strong>show_progress="yes"</strong> - <?php _e( 'Display course progress bars (only with my_courses)', 'reign-tutorlms-addon' ); ?></li>
					<li><strong>course_status="completed"</strong> - <?php _e( 'Filter by: all, completed, in-progress, not-started', 'reign-tutorlms-addon' ); ?></li>
					<li><strong>user_id="123"</strong> - <?php _e( 'Specific user ID (optional - auto-detects profile user)', 'reign-tutorlms-addon' ); ?></li>
				</ul>
				
				<h4><?php _e( 'Examples:', 'reign-tutorlms-addon' ); ?></h4>
				<div class="shortcode-examples">
					<p><strong><?php _e( 'My enrolled courses with progress:', 'reign-tutorlms-addon' ); ?></strong></p>
					<code>[reign_tutor_course my_courses="yes" show_progress="yes" count="4"]</code>
					
					<p><strong><?php _e( 'Completed courses on profile page:', 'reign-tutorlms-addon' ); ?></strong></p>
					<code>[reign_tutor_course my_courses="yes" course_status="completed"]</code>
					
					<p><strong><?php _e( 'Regular course listing:', 'reign-tutorlms-addon' ); ?></strong></p>
					<code>[reign_tutor_course count="6" category="web-development" course_filter="on"]</code>
				</div>
			</div>
			
			<div class="shortcode-section">
				<h3><?php _e( 'Course Categories Shortcode', 'reign-tutorlms-addon' ); ?></h3>
				<div class="shortcode-example">
					<code>[reign_course_categories]</code>
				</div>
				<p><?php _e( 'Display course categories in a grid layout with course counts.', 'reign-tutorlms-addon' ); ?></p>
				
				<h4><?php _e( 'Parameters:', 'reign-tutorlms-addon' ); ?></h4>
				<ul>
					<li><strong>count="8"</strong> - <?php _e( 'Number of categories to show', 'reign-tutorlms-addon' ); ?></li>
					<li><strong>columns="4"</strong> - <?php _e( 'Number of columns (1, 2, 3, 4, or 6)', 'reign-tutorlms-addon' ); ?></li>
					<li><strong>orderby="name"</strong> - <?php _e( 'Order by: name, count', 'reign-tutorlms-addon' ); ?></li>
					<li><strong>show_count="yes"</strong> - <?php _e( 'Show course count per category', 'reign-tutorlms-addon' ); ?></li>
					<li><strong>show_image="yes"</strong> - <?php _e( 'Show category thumbnail or sample course image', 'reign-tutorlms-addon' ); ?></li>
				</ul>
				
				<div class="shortcode-examples">
					<p><strong><?php _e( 'Example:', 'reign-tutorlms-addon' ); ?></strong></p>
					<code>[reign_course_categories count="6" columns="3" orderby="count" order="DESC"]</code>
				</div>
			</div>
		</div>
		
		<style>
			.reign-tutorlms-shortcodes-info { margin: 20px 0; }
			.shortcode-section { background: #f9f9f9; padding: 20px; margin-bottom: 20px; border: 1px solid #ddd; }
			.shortcode-section h3 { margin-top: 0; color: #23282d; }
			.shortcode-section h4 { color: #555; margin: 15px 0 10px 0; }
			.shortcode-example { background: #fff; border: 1px solid #ccc; padding: 10px; margin: 10px 0; }
			.shortcode-example code { background: transparent; padding: 0; }
			.shortcode-examples { margin-top: 15px; }
			.shortcode-examples p { margin: 10px 0 5px 0; }
			.shortcode-examples code { background: #fff; border: 1px solid #ddd; padding: 8px; display: block; margin-bottom: 10px; }
			.shortcode-section ul li { margin-bottom: 5px; }
		</style>
		<?php
	}

	/**
	 * Render Profile Integration tab content
	 */
	public function reign_tutorlms_render_profile_options() {
		global $wbtm_reign_settings;
		$settings = get_option( 'reign_options', array() );
		?>
		<div class="reign-tutorlms-profile-options">
			<h3><?php _e( 'My Courses Profile Tab Integration', 'reign-tutorlms-addon' ); ?></h3>
			<p><?php _e( 'Add a "My Courses" tab to user profiles showing their enrolled TutorLMS courses. The tab will automatically appear on the active social platform (BuddyPress, BuddyBoss, or PeepSo).', 'reign-tutorlms-addon' ); ?></p>
			
			<table class="form-table">
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'tooltip-image', 'reign-tutorlms-addon' ); ?>" />
							<label class="rtm-tooltip-label">
								<?php _e( 'Enable My Courses Tab', 'reign-tutorlms-addon' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php _e( 'Add a "My Courses" tab to user profiles. Automatically detects and integrates with BuddyPress, BuddyBoss, or PeepSo. Shows enrolled courses with progress bars and completion status.', 'reign-tutorlms-addon' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" id="enable_profile_courses_tab" name="tutorlms[enable_profile_courses_tab]" value="1" <?php isset( $wbtm_reign_settings['tutorlms']['enable_profile_courses_tab'] ) ? checked( $wbtm_reign_settings['tutorlms']['enable_profile_courses_tab'], 1 ) : ''; ?>>
					</td>
				</tr>
			</table>
			
			<div class="platform-status">
				<h4><?php _e( 'Detected Social Platforms:', 'reign-tutorlms-addon' ); ?></h4>
				<ul>
					<?php if ( class_exists( 'BuddyPress' ) || function_exists( 'bp_is_active' ) ) : ?>
						<li style="color: #00a32a;">✓ <?php _e( 'BuddyPress/BuddyBoss detected', 'reign-tutorlms-addon' ); ?></li>
					<?php endif; ?>
					<?php if ( class_exists( 'PeepSo' ) ) : ?>
						<li style="color: #00a32a;">✓ <?php _e( 'PeepSo detected', 'reign-tutorlms-addon' ); ?></li>
					<?php endif; ?>
					<?php if ( ! class_exists( 'BuddyPress' ) && ! function_exists( 'bp_is_active' ) && ! class_exists( 'PeepSo' ) ) : ?>
						<li style="color: #d63638;">⚠ <?php _e( 'No social platform detected. Install BuddyPress, BuddyBoss, or PeepSo to use profile integration.', 'reign-tutorlms-addon' ); ?></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		
		<style>
			.reign-tutorlms-profile-options { margin: 20px 0; }
			.platform-status { background: #f9f9f9; padding: 15px; margin: 20px 0; border: 1px solid #ddd; }
			.platform-status h4 { margin-top: 0; }
			.platform-status ul { margin: 10px 0; }
			.platform-status li { margin-bottom: 5px; }
		</style>
		<?php
	}


	public function reign_tutorlms_render_activity_options() {
		global $wbtm_reign_settings;

		$fields = reign_tutorlms_settings_fields();
		?>
		<table class="form-table">
		<?php foreach ( $fields['activity'] as $key => $field ) { ?>
			<tr class="reign-tutorlms-group-activity">
				<td class="rtm-left-side">
					<div class="rtm-tooltip-wrap">
						<?php if ( isset( $field['type'] ) && ! empty( $field['type'] ) ) : ?>
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>"
							class="rtm-tooltip-image" alt="<?php esc_attr_e( 'tooltip-image', 'reign-tutorlms-addon' ); ?>" />
						<?php endif; ?>
						<label class="rtm-tooltip-label">
							<?php echo esc_html( $field['label'] ); ?>
						</label>
					</div>
					<?php if ( isset( $field['type'] ) && ! empty( $field['type'] ) ) : ?>
						<div class="rtm-tooltiptext">
							<?php echo wp_kses_post( $field['description'] ); ?>
						</div>
					<?php endif; ?>
				</td>				
				<td>
					<?php if ( isset( $field['type'] ) && ! empty( $field['type'] ) ) : ?>
						<input type="<?php echo esc_attr( $field['type'] ); ?>" id="<?php echo esc_attr( $key ); ?>" name="tutorlms[<?php echo esc_attr( $key ); ?>]" value="true" <?php echo isset( $field['disabled'] ) ?  esc_attr( 'disabled="'. $field['disabled'] .'"' ) : ''; ?> <?php isset( $wbtm_reign_settings['tutorlms'][ $key ] ) ? checked( $wbtm_reign_settings['tutorlms'][ $key ], 1 ) : ''; ?>>
					<?php else : ?>
						<?php echo wp_kses_post( $field['description'] ); ?>
					<?php endif; ?>
				</td>				
			</tr>
			<?php } ?>
		</table>
		<?php
	}

	/**
	 * Save the REIGN Setting.
	 */
	public function reign_tutorlms_save_reign_theme_settings() {
		if ( isset( $_POST['reign-settings-submit'] ) && $_POST['reign-settings-submit'] == 'Y' ) {
			check_admin_referer( 'reign-options' );
			global $wbtm_reign_settings;

				$fields            = reign_tutorlms_settings_fields();
				
				$tutorlms_settings = array();
				foreach ( $fields as $field ) {
					foreach ( $field as $key => $each_field ) {
						// Check if the key exists in the submitted data
						if ( isset( $_POST[ self::$_slug ] ) && array_key_exists( $key, $_POST[ self::$_slug ] ) ) {
							// Sanitize and save the value based on the field type
							$value = sanitize_text_field( wp_unslash( $_POST[ self::$_slug ][ $key ] ) );
							// Perform additional sanitization if needed
							if ( $each_field['type'] === 'checkbox' ) {
								$value = ! empty( $value );
							}
							// Save the value to the settings array
							$tutorlms_settings[ $key ] = $value;
						}
						if ( isset( $_POST[ self::$_slug ] ) && array_key_exists( $key . '-content', $_POST[ self::$_slug ] ) ) {
							// Sanitize and save the value based on the field type
							$value = sanitize_text_field( wp_unslash( $_POST[ self::$_slug ][ $key . '-content' ] ) );
							// Save the value to the settings array
							$tutorlms_settings[ $key . '-content' ] = $value;
						}
					}
				}
				$wbtm_reign_settings[ self::$_slug ] = $tutorlms_settings;
				update_option( 'reign_options', $wbtm_reign_settings );
				$wbtm_reign_settings = get_option( 'reign_options', array() );
		}
	}





}

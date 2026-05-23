<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/admin
 */
// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Reign_Wcfm_Addon_Admin {

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
	 * The admin options slug of plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private static $_slug = 'reign_wcfm';

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

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$css_extension = '.css';
		} else {
			$css_extension = '.min.css';
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Reign_Wcfm_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Reign_Wcfm_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$css_path = is_rtl() ? 'css/rtl' : 'css';

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . $css_path . '/reign-wcfm-addon-admin' . $css_extension, array(), $this->version, 'all' );

		if ( isset( $_GET['page'], $_GET['tab'] ) && 
         $_GET['page'] === 'reign-options' && 
         $_GET['tab'] === 'reign_wcfm' ) {

			wp_enqueue_style( $this->plugin_name );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$js_extension = '.js';
		} else {
			$js_extension = '.min.js';
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Reign_Wcfm_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Reign_Wcfm_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/reign-wcfm-addon-admin' . $js_extension, array( 'jquery' ), $this->version, false );

		if ( isset( $_GET['page'], $_GET['tab'] ) && 
         $_GET['page'] === 'reign-options' && 
         $_GET['tab'] === 'reign_wcfm' ) {

			wp_enqueue_script( $this->plugin_name );
		}
	}

	/**
	 * Add plugin tab in reign settings.
	 *
	 *   @since 1.1.0
	 */
	public function reign_wcfm_add_reign_tab( $tabs ) {
			$tabs[ self::$_slug ] = __( 'WCFM Marketplace', 'reign-wcfm-addon' );
		return $tabs;
	}

	/**
	 * Create options tab on reign theme settingd.
	 *
	 *  @since 1.1.0
	 */
	public function reign_wcfm_render_theme_options() {
		global $wbtm_reign_settings;
		$vertical_tabs = array(
			'wcfm_general'      => __( 'General', 'reign-wcfm-addon' ),
			'wcfm_single_store' => __( 'Single Store', 'reign-wcfm-addon' ),
		);

		$vertical_tabs = apply_filters( 'wbtm_' . self::$_slug . '_vertical_tabs', $vertical_tabs );
		include 'partials/reign-wcfm-addon-admin-display.php';
	}

	/**
	 * Save WCFM option values with proper sanitization.
	 *
	 * @since 1.1.0
	 */
	public function reign_wcfm_save_settings() {
		if ( isset( $_POST['reign-wcfm-settings-submit'] ) && $_POST['reign-wcfm-settings-submit'] === 'Y' ) {
			check_admin_referer( 'reign-options' );
			global $wbtm_reign_settings;
			if ( isset( $_POST['wcfm_option'] ) && is_array( $_POST['wcfm_option'] ) ) {
				// Safely handle the $_POST['wcfm_option'] input
				$unslashed_options = wp_unslash( $_POST['wcfm_option'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				// Define a schema for sanitization
				$wcfm_option_schema = array(
					'product_favourite'       => 'boolean',
					'vendor_store'            => 'boolean',
					'product_activity'        => 'boolean',
					'review_activity'         => 'boolean',
					'order_activity'          => 'boolean',
					'reign_wcfm_store_layout' => 'string',
				);
				$sanitized_options = array();
				foreach ( $unslashed_options as $key => $value ) {
					$sanitized_key = sanitize_key( $key );
					if ( isset( $wcfm_option_schema[ $sanitized_key ] ) ) {
						switch ( $wcfm_option_schema[ $sanitized_key ] ) {
							case 'boolean':
								$sanitized_options[ $sanitized_key ] = $value === 'on' ? 'on' : '';
								break;
							case 'string':
								$sanitized_options[ $sanitized_key ] = sanitize_text_field( $value );
								break;
							default:
								$sanitized_options[ $sanitized_key ] = sanitize_text_field( $value );
								break;
						}
					}
				}
				// Assign sanitized options to the global settings
				$wbtm_reign_settings['wcfm_option'] = $sanitized_options;
				// Update the settings in the database
				update_option( 'reign_options', $wbtm_reign_settings );
			} else {
				// Set default values if the input is missing or invalid
				$wbtm_reign_settings['wcfm_option'] = '';
				update_option( 'reign_options', $wbtm_reign_settings );
			}
		}
	}

	/**
	 * Renser wcfm options on theme options
	 *
	 * @since 1.9.0
	 */
	public function reign_wcfm_render_genral_options() {
		global $wbtm_reign_settings;

		$wcfm_option = isset( $wbtm_reign_settings['wcfm_option'] ) ? $wbtm_reign_settings['wcfm_option'] : '';
		if(function_exists('buddypress') && buddypress()->buddyboss){
			$get_buddyboss_product_enbl_opt = bp_get_option('bp-feed-custom-post-type-product');
			$disabled_prdct_activity = '';
			if(isset($get_buddyboss_product_enbl_opt) && $get_buddyboss_product_enbl_opt == 1){
				$wcfm_options = get_option( 'reign_options', array() );

				if ( isset( $wcfm_options['wcfm_option'] ) && $wcfm_options['wcfm_option']['product_activity'] === 'on' ) {
					$wcfm_options['wcfm_option']['product_activity'] = 'off';
					update_option( 'reign_options', $wcfm_options );
				}
				$disabled_prdct_activity = 'disabled';
			}
		}
		?>
		<table class="form-table">
			<tr>
				<td class="rtm-left-side">
					<div class="rtm-tooltip-wrap">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'tooltip-image', 'reign-wcfm-addon' ); ?>" />
						<label class="rtm-tooltip-label">
							<?php esc_html_e( 'Mark Products As Favourite', 'reign-wcfm-addon' ); ?>
						</label>
					</div>
					<div class="rtm-tooltiptext">
						<?php esc_html_e( 'Setting to enable/disable product as favourite.', 'reign-wcfm-addon' ); ?>
					</div>
				</td>
				<td>
					<input type="checkbox" name="wcfm_option[product_favourite]" value="on" <?php isset( $wcfm_option['product_favourite'] ) ? checked( $wcfm_option['product_favourite'], 'on' ) : ''; ?>>
				</td>
			</tr>
			<?php
			if ( class_exists( 'buddypress' ) ) {
				if ( function_exists( 'bp_is_active' ) && ! bp_is_active( 'activity' ) ) {
					$disabled = 'disabled';
					?>
					<div class="reignwcfm-flex-container">
						<div><span class="dashicons dashicons-info-outline reignwcfm-fa-icon"></span></div>
						<p><?php echo esc_html__( 'Activity creation settings requires BuddyPress Activity component to be enabled.', 'reign-wcfm-addon' ); ?></p>
					</div>					
					<?php
				} else {
					$disabled = '';
				}
				?>
				<?php if ( class_exists( 'WCFMmp' ) ) { ?> 
					<tr>
						<td class="rtm-left-side">
							<div class="rtm-tooltip-wrap">
								<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'tooltip-image', 'reign-wcfm-addon' ); ?>" />
								<label class="rtm-tooltip-label">
								<?php esc_html_e( 'Display Store Tab', 'reign-wcfm-addon' ); ?>
								</label>
							</div>
							<div class="rtm-tooltiptext">
								<?php esc_html_e( 'Setting to enable/disable store tab on buddypress member profile of vendor.', 'reign-wcfm-addon' ); ?>
							</div>
						</td>
						<td>
							<input type="checkbox" name="wcfm_option[vendor_store]" value="on" <?php isset( $wcfm_option['vendor_store'] ) ? checked( $wcfm_option['vendor_store'], 'on' ) : ''; ?>>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'tooltip-image', 'reign-wcfm-addon' ); ?>" />
							<label class="rtm-tooltip-label">
						<?php esc_html_e( 'Create Product Activity', 'reign-wcfm-addon' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
					<?php esc_html_e( 'Setting to enable/disable product creation activity.', 'reign-wcfm-addon' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="wcfm_option[product_activity]" value="on" 
						<?php
						( isset( $wcfm_option['product_activity'] ) && bp_is_active( 'activity' ) ) ? checked( $wcfm_option['product_activity'], 'on' ) : '';
						echo esc_attr( $disabled );
						if ( ! empty( $disabled_prdct_activity ) ) : 
							echo esc_attr( $disabled_prdct_activity );
							?> >
							<p style="color: #a00; display: inline; margin-top: 5px;" class="dashicons dashicons-info-outline">
								<?php esc_html_e( 'This option is disabled because the product activity feed is turned on in BuddyBoss settings.', 'reign-wcfm-addon' ); ?>
							</p> 
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'tooltip-image', 'reign-wcfm-addon' ); ?>" />
							<label class="rtm-tooltip-label">
						<?php esc_html_e( 'Add Review Activity', 'reign-wcfm-addon' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Setting to enable/disable product review activity.', 'reign-wcfm-addon' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="wcfm_option[review_activity]" value="on" 
						<?php
						( isset( $wcfm_option['review_activity'] ) && bp_is_active( 'activity' ) ) ? checked( $wcfm_option['review_activity'], 'on' ) : '';
						echo esc_attr( $disabled );
						?>
						>
					</td>
				</tr>
				<tr>
					<td class="rtm-left-side">
						<div class="rtm-tooltip-wrap">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'tooltip-image', 'reign-wcfm-addon' ); ?>" />
							<label class="rtm-tooltip-label">
							<?php esc_html_e( 'Order Activity', 'reign-wcfm-addon' ); ?>
							</label>
						</div>
						<div class="rtm-tooltiptext">
							<?php esc_html_e( 'Setting to enable/disable product order activity.', 'reign-wcfm-addon' ); ?>
						</div>
					</td>
					<td>
						<input type="checkbox" name="wcfm_option[order_activity]" value="on" 
						<?php
						( isset( $wbtm_reign_settings['wcfm_option']['order_activity'] ) && bp_is_active( 'activity' ) ) ? checked( $wbtm_reign_settings['wcfm_option']['order_activity'], 'on' ) : '';
						echo esc_attr( $disabled );
						?>
						>
					</td>
				</tr>
			<?php } ?>
		</table>
		<?php
	}

	/**
	 * Display single layout settings
	 *
	 * @return void
	 */
	public function reign_wcfm_render_store_options() {
		global $wbtm_reign_settings;

		$wcfm_option = isset( $wbtm_reign_settings['wcfm_option'] ) ? $wbtm_reign_settings['wcfm_option'] : '';
		?>
		<table class="form-table">
			<tr>
				<td class="rtm-left-side">
					<div class="rtm-tooltip-wrap">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/question.png' ); ?>" class="rtm-tooltip-image" alt="<?php esc_attr_e( 'tooltip-image', 'reign-wcfm-addon' ); ?>" />
						<label for="curriculum_layout" class="rtm-tooltip-label">
							<?php esc_html_e( 'Single Store Layout', 'reign-wcfm-addon' ); ?>
						</label>
					</div>
					<div class="rtm-tooltiptext">
						<?php esc_html_e( 'Allows you to set the single store layout.', 'reign-wcfm-addon' ); ?>
					</div>
				</td>
				<td>
				<?php
				$reign_wcfm_store_layout_options = array(
					'layout_one' => __( 'Layout 1', 'reign-wcfm-addon' ),
					'layout_two' => __( 'Layout 2', 'reign-wcfm-addon' ),
				);

				$reign_wcfm_store_layout = isset( $wcfm_option['reign_wcfm_store_layout'] ) ? $wcfm_option['reign_wcfm_store_layout'] : 'layout_one';

				echo '<select name="wcfm_option[reign_wcfm_store_layout]">';
				foreach ( $reign_wcfm_store_layout_options as $key => $value ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $reign_wcfm_store_layout, $key, false ) . '>' . esc_html( $value ) . '</option>';
				}
				echo '</select>';
				?>
			</td>

			</tr>
		</table>
		<?php
	}
}

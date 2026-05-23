<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Profile_Pro
 * @subpackage Buddypress_Profile_Pro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Profile_Pro
 * @subpackage Buddypress_Profile_Pro/admin
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Profile_Pro_Admin {

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

		global $wp_styles;
		$srcs                = array_map( 'basename', (array) wp_list_pluck( $wp_styles->registered, 'src' ) );
		$bp_profile_pro_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';
		

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Profile_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Profile_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( isset( $bp_profile_pro_page ) && ( 'buddypress_profile_pro' === $bp_profile_pro_page || 'wbcom-plugins-page' === $bp_profile_pro_page || 'wbcom-support-page' == $bp_profile_pro_page || 'wbcom-license-page' == $bp_profile_pro_page ) ) {
			if ( in_array( 'font-awesome.css', $srcs, true ) || in_array( 'font-awesome.min.css', $srcs, true ) ) {
				/* echo 'font-awesome.css registered'; */
			} else {
				wp_enqueue_style( 'wbbpp-font-awesome-admin', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), $this->version, 'all' );
			}
			wp_enqueue_style( 'selectize', plugin_dir_url( __FILE__ ) . 'css/selectize.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-profile-pro-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$bp_profile_pro_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Profile_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Profile_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( isset( $bp_profile_pro_page ) && 'buddypress_profile_pro' === $bp_profile_pro_page ) {

			wp_enqueue_script( 'selectize', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-profile-pro-admin.js', array( 'jquery' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name,
				'bprm_admin_ajax_object',
				array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'bprm_admin_ajax_security' ),
					'text_copied' => esc_html__('Text copied to clipboard!', 'buddypress-profile-pro' ),
					'failed_copy' => esc_html__('Failed to copy text.', 'buddypress-profile-pro' ),
					
				)
			);

			if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
		}

	}

	/**
	 * Hide all notices from the setting page.
	 *
	 * @return void
	 */
	public function wbcom_hide_all_admin_notices_from_setting_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'buddypress_profile_pro', 'wbcom-license-page' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Function to add bp resume manager settings page in admin menu.
	 *
	 * @since    1.0.0
	 */
	public function bprm_add_menu_buddypress_profile_pro() {

		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {

			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-profile-pro' ), esc_html__( 'WB Plugins', 'buddypress-profile-pro' ), 'manage_options', 'wbcomplugins', array( $this, 'wbbpp_profile_pro_settings_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-profile-pro' ), esc_html__( 'General', 'buddypress-profile-pro' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Profile Pro Setting Page', 'buddypress-profile-pro' ), esc_html__( 'Profile Pro', 'buddypress-profile-pro' ), 'manage_options', 'buddypress_profile_pro', array( $this, 'wbbpp_profile_pro_settings_page' ) );

	}

	/**
	 * Callback function for bp resume manager settings page.
	 *
	 * @since    1.0.0
	 * @param    string $current       The current tab.
	 */
	public function wbbpp_profile_pro_settings_page( $current = 'general' ) {
		$current = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
		?>
		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
					<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/?utm_source=pluginoffernotice&utm_medium=community_banner" target="_blank">
						<img src="<?php echo esc_url( WBBPP_PLUGIN_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
					</a>
				</div>
			</div>
		<div class="wbcom-wrap">
			<div class="blpro-header">
				<div class="wbcom_admin_header-wrapper">
		            <div id="wb_admin_plugin_name">
						<?php esc_html_e( 'BuddyPress Profile Pro', 'buddypress-profile-pro' ); ?>
                        <?php  /* translators: %s: */ ?>
						<span><?php printf( esc_html__( 'Version %s', 'buddypress-profile-pro' ), WBBPP_PLUGIN_VERSION ); //phpcs:ignore?></span>
					</div>
		            <?php echo do_shortcode('[wbcom_admin_setting_header]'); ?>
		        </div>
			</div>
		<div class="wbcom-admin-settings-page">
		<?php

		$bprm_tabs = array(
			'welcome'        => __( 'Welcome', 'buddypress-profile-pro' ),
			'general'        => __( 'General', 'buddypress-profile-pro' ),
			'group_settings' => __( 'Group Settings', 'buddypress-profile-pro' ),
			'gen_settings'   => __( 'Field Settings', 'buddypress-profile-pro' ),
			'profile_search' => __( 'Profile Search', 'buddypress-profile-pro' ),
			'support'        => __( 'Support', 'buddypress-profile-pro' ),
		);

		$tab_html = '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $bprm_tabs as $bprm_tab => $bprm_name ) {
			$class     = ( $bprm_tab === $current ) ? 'nav-tab-active' : '';
			$tab_html .= '<li class="' . $bprm_tab . '"><a class="nav-tab ' . $class . '" href="admin.php?page=buddypress_profile_pro&tab=' . $bprm_tab . '">' . $bprm_name . '</a></li>';
		}
		$tab_html .= '</div></ul></div>';
		echo $tab_html; // phpcs:ignore

		include 'inc/wbbpp-options-page.php';
		echo '</div>'; /* closing of div class wbcom-admin-settings-page */
		echo '</div>'; /* closing div class wbcom-wrap */
		echo '</div>'; /* closing div class wrap */
	}

	/**
	 * Function to add admin register settings for plugin.
	 *
	 * @since    1.0.0
	 */
	public function wbbpp_add_admin_register_setting() {
		register_setting( 'wbbpp_general_settings_section', 'wbbpp_general_settings' );
		register_setting( 'wbbpp_profile_fields_settings_section', 'wbbpp_profile_fields_settings', array( $this, 'wbbpp_sanitize_profile_setting_callback' ) );
		register_setting( 'wbbpp_profile_groups_settings_section', 'wbbpp_profile_groups_settings' );
		register_setting( 'wbbpp_profile_search_fields_section', 'wbbpp_profile_search_fields' );
	}

	/**
	 * Function to sanitize register settings array.
	 *
	 * @since    1.0.0
	 * @param    array $input       The register setting input array.
	 */
	public function wbbpp_sanitize_profile_setting_callback( $input ) {
		$input = $this->wbbpp_remove_empty_keys( $input );
		return $input;
	}

	/**
	 * Function used in sanitizing reister setting input array.
	 *
	 * @since    1.0.0
	 * @param bprm_settings_one $bprm_settings_one bprm_settings_one.
	 */
	public function wbbpp_remove_empty_keys( $bprm_settings_one ) {
		foreach ( $bprm_settings_one as &$value ) {
			if ( is_array( $value ) ) {
				$value = $this->wbbpp_remove_empty_keys( $value );
			}
		}

		return array_filter(
			$bprm_settings_one,
			function( $item ) {
				return null !== $item && '' !== $item;
			}
		);
	}

	/**
	 * Ajax request to save new field.
	 *
	 * @since    1.0.2
	 */
	public function wbbpp_save_new_group() {
		if ( isset( $_POST['action'] ) && 'wbbpp_save_new_group' === $_POST['action'] ) {
			check_ajax_referer( 'bprm_admin_ajax_security', 'ajax_nonce' );
			$saved_data = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			parse_str( $saved_data, $nf_formdata );// This will convert the string to array.
			$nf_form_fields  = filter_var_array( $nf_formdata, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$nm_key          = time();
			$gp_title        = $nf_form_fields['bprm_gp_title'];
			$gp_desc         = $nf_form_fields['bprm_gp_desc'];
			$gp_disp_area    = $nf_form_fields['bprm_gp_display_area'];
			$gp_profile_disp = ( isset( $nf_form_fields['bprm_gp_profile_display'] ) ) ? $nf_form_fields['bprm_gp_profile_display'] : '';
			$gp_resume_disp  = ( isset( $nf_form_fields['bprm_gp_resume_display'] ) ) ? $nf_form_fields['bprm_gp_resume_display'] : '';
			$gp_repeater     = ( isset( $nf_form_fields['bprm_gp_repeater'] ) ) ? $nf_form_fields['bprm_gp_repeater'] : '';

			// code to get the groups listing for the type.
			$gp_avail    = ( isset( $nf_form_fields['bprm_gp_avail'] ) ) ? $nf_form_fields['bprm_gp_avail'] : '';
			$roles_style = 'display:none';
			$mtype_style = 'display:none';
			if ( $gp_avail ) {
				$gp_avail_user_roles = $gp_avail_mem_type = array();
				if ( 'user_roles' === $gp_avail ) {
					$roles_style         = '';
					$gp_avail_user_roles = $nf_form_fields['wbbpp_grp_avail_user_roles'];
					if ( ! $gp_avail_user_roles ) {
						$gp_avail_user_roles[] = 'all';
					}
				} elseif ( 'mem_type' === $gp_avail ) {
					$mtype_style       = '';
					$gp_avail_mem_type = $nf_form_fields['wbbpp_grp_avail_mem_type'];
					if ( ! $gp_avail_mem_type ) {
						$gp_avail_mem_type[] = 'all';
					}
				} else {
					$roles_style = 'display:none';
					$mtype_style = 'display:none';
				}
			}
			global $wp_roles;
			$roles_option_html = '';
			$user_roles        = $wp_roles->get_names();
			$all_index         = array( 'all' => __( 'All', 'buddypress-profile-pro' ) );
			$user_roles        = array_merge( $all_index + $user_roles );
			foreach ( $user_roles as $slug => $role_name ) {
				$selected           = ( ! empty( $gp_avail_user_roles ) && in_array( $slug, $gp_avail_user_roles, true ) ) ? 'selected' : '';
				$roles_option_html .= '<option value="' . $slug . '" ' . $selected . '>' . $role_name . '</option>';
			}

			$mtypes_option_html = '';
			$args               = array();
			$member_types       = bp_get_member_types( $args, $output = 'object' );
			$all_mt_index       = array( 'all' => (object) array( 'labels' => array( 'name' => 'All' ) ) );
			$member_types       = array_merge( $all_mt_index + $member_types );
			foreach ( $member_types as $slug => $type_obj ) {
				$selected            = ( ! empty( $gp_avail_mem_type ) && in_array( $slug, $gp_avail_mem_type, true ) ) ? 'selected' : '';
				$mtypes_option_html .= '<option value="' . $slug . '" ' . $selected . '>' . $type_obj->labels['name'] . '</option>';
			}

			$grp_area            = bprm_groups_display_area();
			$grp_display_options = '';
			foreach ( $grp_area as $grp_area_key => $grp_area_text ) {

				$grp_display_options .= "<option value='" . $grp_area_key . "' " . selected( $gp_disp_area, $grp_area_key, false ) . '>' . $grp_area_text . '</option>';
			}

			$group_html  = '';
			$group_html .= '<div class="bprm-group-tab-link-container ui-sortable-handle">
								<div class="bprm-group-tabs-link">
									<span class="brpm_grp_name">' . $gp_title . '</span>
									<span class="bprm-group-actions">
										<a href="javascript:void(0)" class="bprm-remove-group-zone"><i class="fa fa-trash-o" aria-hidden="true"></i></a><a href="javascript:void(0)" class="bprm-show-group-zone"><i class="fa fa-cog" aria-hidden="true"></i></a>
									</span>
								</div>
								<div class="bprm-group-tabs-content ' . $nm_key . '">
									<div class="bprm-groups-zone">
										<div class="form-table">
											<div class="bprm-groups-key-field-item">
												<label>' . __( 'Group Title', 'buddypress-profile-pro' ) . '</label>
													<input type="text" class="bprm-group-title-text" name="wbbpp_profile_groups_settings[' . $nm_key . '][g_name]" value="' . $gp_title . '">
												</div>
											<div class="bprm-groups-key-field-item">
											<label>' . __( 'Group Description', 'buddypress-profile-pro' ) . '</label>
													<textarea name="wbbpp_profile_groups_settings[' . $nm_key . '][g_desc]">' . $gp_desc . '</textarea>
												</div>
											<div class="bprm-groups-key-field-item" style="display: none;">
												<label>' . __( 'Group Key', 'buddypress-profile-pro' ) . '</label>
													<input type="text" name="wbbpp_profile_groups_settings[' . $nm_key . '][g_key]" value="' . $nm_key . '">
												</div>
											<div class="bprm-groups-key-field-item wbbpp-group-display-area-tr">
											<label>' . __( 'Group Display Area', 'buddypress-profile-pro' ) . '</label>
													<select name="wbbpp_profile_groups_settings[' . $nm_key . '][g_area]">
															' . $grp_display_options . '
													</select>
												</div>
											<div class="bprm-groups-key-field-item">
											<label>' . __( 'Display Group at BuddyPress Profile', 'buddypress-profile-pro' ) . '</label>
													<input type="checkbox" name="wbbpp_profile_groups_settings[' . $nm_key . '][profile_display]" value="yes" ' . checked( $gp_profile_disp, 'yes', false ) . '>
												</div>
											<div class="bprm-groups-key-field-item wbbpp-resume-display-tr">
												<label>' . __( 'Display Group at Resume', 'buddypress-profile-pro' ) . '</label>
												<input type="checkbox" name="wbbpp_profile_groups_settings[' . $nm_key . '][resume_display]" value="yes" ' . checked( $gp_resume_disp, 'yes', false ) . '>
											</div>
											<div class="bprm-groups-key-field-item">
												<label>' . __( 'Repeater', 'buddypress-profile-pro' ) . '</label>
												</div>
													<input type="checkbox" name="wbbpp_profile_groups_settings[' . $nm_key . '][repeater]" value="yes" ' . checked( $gp_repeater, 'yes', false ) . '>
												</div>
											<div class="bprm-groups-key-field-item">
												<label>' . __( 'Group Availability', 'buddypress-profile-pro' ) . '</label>
													<input class="wbbpp-grp-avail" data-id="wbbpp-user-roles-list" type="radio" name="wbbpp_profile_groups_settings[' . $nm_key . '][grp_avail]" value="user_roles" ' . checked( $gp_avail, 'user_roles', false ) . '>
													<span>' . __( 'User roles', 'buddypress-profile-pro' ) . '</span>
													<input class="wbbpp-grp-avail" data-id="wbbpp-mem-typ-list" type="radio" name="wbbpp_profile_groups_settings[' . $nm_key . '][grp_avail]" value="mem_type" ' . checked( $gp_avail, 'mem_type', false ) . '>
													<span>' . __( 'Member Type', 'buddypress-profile-pro' ) . '</span>
												</div>
											<div class="bprm-groups-key-field-item wbbpp-grp-avail-class" style="' . $roles_style . '" id="wbbpp-user-roles-list">
												<label>' . __( 'Select user roles', 'buddypress-profile-pro' ) . '</label>
												 <select class="wbbpp-user-roles-list-select" id="wbbpp-user-roles-list" name="wbbpp_profile_groups_settings[' . $nm_key . '][roles][]" multiple data-placeholder="' . esc_html__( 'Select user role', 'buddypress-profile-pro' ) . '">
													' . $roles_option_html . '
													</select>
												</div>
											<div class="bprm-groups-key-field-item wbbpp-grp-avail-class" style="' . $mtype_style . '" id="wbbpp-mem-typ-list">
												<label>' . __( 'Select member types', 'buddypress-profile-pro' ) . '</label>
												<select class="wbbpp-mem-typ-list-select" id="wbbpp-mem-typ-list" name="wbbpp_profile_groups_settings[' . $nm_key . '][mtypes][]" multiple data-placeholder="' . esc_html__( 'Select member type', 'buddypress-profile-pro' ) . '">
													' . $mtypes_option_html . '
													</select>
											</div>
										</div>
									</div>
								</div>
							</div>';
			echo $group_html; // phpcs:ignore WordPress.Security.EscapeOutput
			die;
		}
	}

	/**
	 * Ajax request to save new field.
	 *
	 * @since    1.0.0
	 */
	public function wbbpp_save_new_field() {
		if ( isset( $_POST['action'] ) && 'wbbpp_save_new_field' === $_POST['action'] ) {
			check_ajax_referer( 'bprm_admin_ajax_security', 'ajax_nonce' );
			$saved_data = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			parse_str( $saved_data, $nf_formdata );// This will convert the string to array.

			$nf_form_fields = filter_var_array( $nf_formdata, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			$nm_key         = time();

			$bprm_nf_group = $nf_form_fields['bprm_nf_group'];

			$bprm_nf_type = $nf_form_fields['bprm_nf_type'];

			$nf_form_fields['bprm_nf_required'] = ( isset( $nf_form_fields['bprm_nf_required'] ) ) ? $nf_form_fields['bprm_nf_required'] : '';
			$nf_form_fields['bprm_nf_repeater'] = ( isset( $nf_form_fields['bprm_nf_repeater'] ) ) ? $nf_form_fields['bprm_nf_repeater'] : '';
			$nf_form_fields['bprm_nf_show_on'] = ( isset( $nf_form_fields['bprm_nf_show_on'] ) ) ? $nf_form_fields['bprm_nf_show_on'] : '';
			$nf_form_fields['bprm_nf_display']  = ( isset( $nf_form_fields['bprm_nf_display'] ) ) ? $nf_form_fields['bprm_nf_display'] : '';

			if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				$grp_args = get_site_option( 'wbbpp_profile_groups_settings' );
			} else {
				$grp_args = get_option( 'wbbpp_profile_groups_settings' );
			}

			$bprm_field_types_optn = bprm_resume_field_types();

			$field_type_options = '';

			foreach ( $bprm_field_types_optn as $field_type => $field_text ) {
				$field_type_options .= "<option value='" . $field_type . "' " . selected( $nf_form_fields['bprm_nf_type'], $field_type, false ) . '>' . $field_text . '</option>';
			}

			$field_grp_options = '';
			
			$field_group_repeater = 'no';
			foreach ($grp_args as $grp_key_index => $grp_infos) {
				if ($nf_form_fields['bprm_nf_group'] == $grp_key_index) {
					$field_group_repeater = isset($grp_infos['repeater']) ? $grp_infos['repeater'] : 'no'; // Add a fallback
				}
				$field_grp_options .= "<option value='" . $grp_key_index . "' " . selected($nf_form_fields['bprm_nf_group'], $grp_key_index, false) . '>' . $grp_infos['g_name'] . '</option>';
			}

			if ( 'selectize' === $bprm_nf_type || 'dropdown' === $bprm_nf_type || 'checkbox' === $bprm_nf_type || 'radio_button' === $bprm_nf_type || 'text_dropdown' === $bprm_nf_type ) {
				$field_typ_html = "<tr><th>
									<label>" . __( 'Field Options', 'buddypress-profile-pro' ) . '</label></th><td>';
				foreach ( $nf_form_fields['bprm_nf_field_type_option'] as $key => $option_value ) {

					$field_typ_html .= "<div class='bprm-fld-option-html'>
											<input type='text' name='wbbpp_profile_fields_settings[" . $bprm_nf_group . '][' . $nm_key . "][field_type][options][]'  value='" . $option_value . "'>
											<span>
												<a href='JavaScript:void(0)' data-id='bprm-fld-option-html' class='bprm-add-option'><i class='fa fa-plus-circle' aria-hidden='true'></i></a>
												<a href='JavaScript:void(0)' data-id='bprm-fld-option-html' class='bprm-remove-option'><i class='fa fa-trash-o' aria-hidden='true'></i></a>
												<a href='javascript:void(0)' class='bprm-show-meta-field-zone'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a>
											</span>
										</div>";
				}

				$field_typ_html .= '';
			} else {
				$field_typ_html = '</td></tr>';
			}

			$sec_html = '';
			$li_html  = "<li class='bprm-field-li ui-sortable-handle' style='position: relative;left: 0px;top: 0px;'>" . $nf_form_fields['bprm_nf_title'] . "
				<div class='bprm-field-zone'>
					<table class='form-table'>
						<tr>
							<th scope='row'><label>" . __( 'Field Title', 'buddypress-profile-pro' ) . "</label>
							</th>
							<td>
								<input type='text' name='wbbpp_profile_fields_settings[" . $bprm_nf_group . '][' . $nm_key . "][field_tile]' value='" . $nf_form_fields['bprm_nf_title'] . "'>
							</td>
						</tr>
						<tr>
							<th scope='row'><label>" . __( 'Field Type', 'buddypress-profile-pro' ) . "</label>
							</th>
							<td>
								<select name='wbbpp_profile_fields_settings[" . $bprm_nf_group . '][' . $nm_key . "][field_type][type]' class='bprm_rs_type_change'>
									" . $field_type_options . '
								</select>
							</td>
						</tr>
						' . $field_typ_html . "
						<tr style='display:none'>
							<th scope='row'><label>" . __( 'Field Group', 'buddypress-profile-pro' ) . "</label>
							</th>
							<td>
								<select name='wbbpp_profile_fields_settings[" . $bprm_nf_group . '][' . $nm_key . "][field_grp]'>
									" . $field_grp_options . "
								</select>
							</td>
						</tr>
						<tr>
							<th scope='row'><label>" . __( 'Display', 'buddypress-profile-pro' ) . "</label>
							</th>
							<td>
								<input type='checkbox' name='wbbpp_profile_fields_settings[" . $bprm_nf_group . '][' . $nm_key . "][display]' value='yes' " . checked( $nf_form_fields['bprm_nf_display'], 'yes', false ) . ">
							</td>
						</tr>
						<tr>
							<th scope='row'><label>" . __( 'Required', 'buddypress-profile-pro' ) . "</label>
							</th>
							<td>
								<input type='checkbox' name='wbbpp_profile_fields_settings[" . $bprm_nf_group . '][' . $nm_key . "][required]' value='yes' " . checked( $nf_form_fields['bprm_nf_required'], 'yes', false ) . ">
							</td>
						</tr>
						<tr>
							<th scope='row'><label>" . __( 'Repeater', 'buddypress-profile-pro' ) . "</label>
							</th>
							<td>
								<input type='checkbox' name='wbbpp_profile_fields_settings[" . $bprm_nf_group . '][' . $nm_key . "][repeater]' value='yes' " . checked( $nf_form_fields['bprm_nf_repeater'], 'yes', false ) . ">
							</td>
						</tr>
						<tr id='bprm-field-item-on-register-field' " . ( $field_group_repeater == 'yes' ? 'style="display:none;"' : '' ) . ">
							<th scope='row'><label>" . __( 'Show on register page', 'buddypress-profile-pro' ) . "</label>
							</th>
							<td>
								<input type='checkbox' name='wbbpp_profile_fields_settings[" . $bprm_nf_group . '][' . $nm_key . "][bprm_nf_show_on]' value='register' " . checked( $nf_form_fields['bprm_nf_show_on'], 'register', false ) . '>
							</td>
						</tr>' . $sec_html . "
						<tr class='bprm-show-meta-field-info'>
							<th scope='row'><label>". esc_html__( 'Group Key' , 'buddypress-profile-pro' ). "</label>
							</th>
							<td class='textToCopy'>
								<input type='text' value='". esc_attr($bprm_nf_group) ."' readonly>
								<button class='copywbbpText'>". esc_html__( 'Copy' , 'buddypress-profile-pro' )."</button>
							</td>
						</tr>
						<tr class='bprm-show-meta-field-info'>
							<th scope='row'><label>".  esc_html__( 'Field Key' , 'buddypress-profile-pro' )."</label>
							</th>
							<td class='textToCopy'>
								<input type='text' value='". esc_attr($nm_key) ."' readonly>
								<button class='copywbbpText'>". esc_html__( 'Copy' , 'buddypress-profile-pro' )."</button>
							</td>
						</tr>						
						<tr class='bprm-show-meta-field-info'>
							<th scope='row'><label>".  esc_html__( 'Meta Key' , 'buddypress-profile-pro' )."</label>
							</th>
							<td class='textToCopy'>
								<input type='text' value='wbbpp_". esc_attr($bprm_nf_group) ."_". esc_attr($nm_key)  ."' readonly>
								<button class='copywbbpText'>". esc_html__( 'Copy' , 'buddypress-profile-pro' )."</button>
							</td>
						</tr>
					</table>
				</div>
				<div class='bprm-field-actions'>
					<a href='javascript:void(0)' class='bprm-remove-field-zone'>
						<i class='fa fa-trash-o' aria-hidden='true'></i>
					</a>
					<a href='javascript:void(0)' class='bprm-show-field-zone'>
						<i class='fa fa-cog' aria-hidden='true'></i>
					</a>
				</div>
			</li>";

			echo $li_html; // phpcs:ignore WordPress.Security.EscapeOutput
			die;
		}
	}

	/**
	 * Ajax request to serve field type html whilie adding new field.
	 *
	 * @since    1.0.0
	 */
	public function wbbpp_new_field_type_html() {
		if ( isset( $_POST['action'] ) && 'wbbpp_new_field_type_html' === $_POST['action'] ) {
			check_ajax_referer( 'bprm_admin_ajax_security', 'ajax_nonce' );
			$bprm_field_type = ( isset( $_POST['ftype'] ) ) ? sanitize_text_field( wp_unslash( $_POST['ftype'] ) ) : '';

			$rendr_html = '';
			if ( $bprm_field_type ) {
				$rendr_html = $this->wbbpp_get_field_type_html_settings();
			}
			echo $rendr_html; // phpcs:ignore WordPress.Security.EscapeOutput
			die;
		}
	}

	/**
	 * Function used in creating field type html.
	 *
	 * @since    1.0.0
	 */
	public function wbbpp_get_field_type_html_settings() {
		?>
		<div class="bprm-groups-key-field-item">
		<label><?php esc_html_e( 'Field Options', 'buddypress-profile-pro' ); ?></label>
			<div class="bprm-get-field-type-html">
				<input type="text" name="bprm_nf_field_type_option[]"  value="">
				<span>
					<a href="JavaScript:void(0)" data-id="bprm-get-field-type-html" class="bprm-add-option"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
					<a href="JavaScript:void(0)" data-id="bprm-get-field-type-html" class="bprm-remove-option"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
				</span>
			</div>
		</div>
		<?php
	}

	/**
	 * Ajax request to detect field type html in register setting form.
	 *
	 * @since    1.0.0
	 */
	public function wbbpp_setting_form_fld_typ_html() {
		if ( isset( $_POST['action'] ) && 'wbbpp_setting_form_fld_typ_html' === $_POST['action'] ) {
			check_ajax_referer( 'bprm_admin_ajax_security', 'ajax_nonce' );
			$bprm_field_type = ( isset( $_POST['ftype'] ) ) ? sanitize_text_field( wp_unslash( $_POST['ftype'] ) ) : '';
			$bprm_field_name = ( isset( $_POST['fname'] ) ) ? sanitize_text_field( wp_unslash( $_POST['fname'] ) ) : '';

			$field_html = '';
			if ( $bprm_field_type && $bprm_field_name ) {
				$field_html = $this->wbbpp_field_options_html( $bprm_field_name );
			}
			echo $field_html; // phpcs:ignore WordPress.Security.EscapeOutput
			die;
		}
	}

	/**
	 * Function used in creating field type html in register settings section.
	 *
	 * @since    1.0.0
	 * @param bprm_field_name $bprm_field_name bprm_field_name.
	 */
	public function wbbpp_field_options_html( $bprm_field_name ) {
		?>
		<th scope='row'>
			<label><?php esc_html_e( 'Field Options', 'buddypress-profile-pro' ); ?></label>
		</th>
		<td>
			<div class='bprm-fld-option-html'>
				<input type='text' name='<?php echo esc_attr( $bprm_field_name ); ?>' value=''>
				<span>
					<a href='JavaScript:void(0)' data-id='bprm-fld-option-html' class='bprm-add-option'><i class='fa fa-plus-circle' aria-hidden='true'></i></a>
					<a href='JavaScript:void(0)' data-id='bprm-fld-option-html' class='bprm-remove-option'><i class='fa fa-trash-o' aria-hidden='true'></i></a>
				</span>
			</div>
		</td>
		<?php
	}
	/**
	 * Function used to gert the profile fields
	 *
	 * @since    2.0.0
	 */
	public function bprm_get_search_field() {
		if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
			$bprm_settings = get_site_option( 'wbbpp_profile_fields_settings' );
		} else {
			$bprm_settings = get_option( 'wbbpp_profile_fields_settings' );
		}

		if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
			$grp_args = get_site_option( 'wbbpp_profile_groups_settings' );
		} else {
			$grp_args = get_option( 'wbbpp_profile_groups_settings' );
		}
		?>

		<div class="search_field">
			<span class="bprm-col1">&nbsp;&#x21C5;</span>
			<span class="bprm-col2">
				<?php echo wbbpp_bprm_profile_fields_dropdown(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</span>			
			<span class="bprm-col6"><a href="javascript:void(0)" class="delete_bprm_field"><?php esc_html_e( 'Delete', 'buddypress-profile-pro' ); ?></a></span>
			<span class="bprm_spinner"></span>
		</div>
		<?php

		wp_die();
	}

	/**
	 * Function used to gert the profile fields
	 *
	 * @since    2.0.0
	 */
	public function bprm_search_field_row() {
		$nonce = isset( $_POST['profile_pro_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['profile_pro_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'bprm_admin_ajax_security' ) ) {
			$error = new WP_Error( '001', 'No user information was retrieved.', 'Some information' );
			wp_send_json_error( $error );
		}
		if ( isset( $_REQUEST['field'] ) && '' !== $_REQUEST['field'] ) {
			?>
			<span class="bprm-col1">&nbsp;&#x21C5;</span>
			<span class="bprm-col2">
				<label class="responsive"><?php esc_html_e( 'Search Field', 'buddypress-profile-pro' ); ?></label>
				<?php echo wbbpp_bprm_profile_fields_dropdown( wp_unslash( $_REQUEST['field'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized ?>
			</span>
			<span class="bprm-col3">
				<label class="responsive"><?php esc_html_e( 'Field Label', 'buddypress-profile-pro' ); ?></label>
				<input type="text" name="wbbpp_profile_search_fields[field_label][]" class="text-field" value="<?php echo ( isset( $_REQUEST['field_name'] ) ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['field_name'] ) ) ) : ''; ?>" />
			</span>
			<span class="bprm-col4">
				<label class="responsive"><?php esc_html_e( 'Field Description', 'buddypress-profile-pro' ); ?></label>
				<input type="text" name="wbbpp_profile_search_fields[field_description][]" class="text-field" />
			</span>
			<span class="bprm-col5">
				<label class="responsive"><?php esc_html_e( 'Search Mode', 'buddypress-profile-pro' ); ?></label>
				<?php
				$filters = wbbpp_get_bprm_search_filters( $_REQUEST['field'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				?>
				<select class='select-field' name="wbbpp_profile_search_fields[field_mode][]">
				<?php
				$value = '';
				foreach ( $filters as $key => $label ) {
					$selected = $value === $key ? " selected='selected'" : '';
					echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>\n';

				}
				?>
				</select>
			</span>
			<span class="bprm-col6"><a href="javascript:void(0)" class="delete_bprm_field"><?php esc_html_e( 'Delete', 'buddypress-profile-pro' ); ?></a></span>
			<span class="bprm_spinner"></span>
			<?php
		}
		wp_die();
	}
	
	public function wp_ajax_wbbpp_verify_apikey() {
		check_ajax_referer( 'bprm_admin_ajax_security', 'ajax_nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			exit();
		}
		if ( filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) && filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) === 'wbbpp_verify_apikey' ) {
			$apikey    = filter_input( INPUT_POST, 'apikey', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$latitude  = filter_input( INPUT_POST, 'latitude', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$longitude = filter_input( INPUT_POST, 'longitude', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$radius    = 10000;

			$response        = $this->bprm_google_places( $apikey, $latitude, $longitude, $radius );
			$code            = wp_remote_retrieve_response_code( $response );
			$response_body   = wp_remote_retrieve_body( $response );
			$response_status = json_decode( $response_body, true );
			if ( 200 !== $code ) {
				$message = 'not-verified';
				update_option( 'wbbpp_googlemapapi_status', $message );
				bp_update_option( 'wbbpp_apikey_verified', 'no' );
			} elseif ( 'REQUEST_DENIED' === $response_status['status'] ) {
				$message = 'not-verified';
				update_option( 'wbbpp_googlemapapi_status', $message );
				bp_update_option( 'wbbpp_apikey_verified', 'no' );
			} else {
				$message = 'verified';
				update_option( 'wbbpp_googlemapapi_status', $message );
				bp_update_option( 'wbbpp_apikey_verified', 'yes' );
			}

			$response = array( 'message' => $message );
			wp_send_json_success( $response );
			die;
		}
	}
	
	/**
	 * Function to fetch google places.
	 *
	 * @since     1.0.0
	 * @param     string $apikey     Api key.
	 * @param     string $lat        Latitude.
	 * @param     string $lon        Longitude.
	 * @param     string $radius     Radius.
	 * @return    array  $response   Response array.
	 */
	public static function bprm_google_places( $apikey, $lat = '', $lon = '', $radius = '' ) {
		$places_url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
		$parameters = array(
			'location' => "$lat,$lon",
			'radius'   => $radius,
			'key'      => $apikey,
		);
		$url        = add_query_arg( $parameters, esc_url_raw( $places_url ) );
		$response   = wp_remote_get( esc_url_raw( $url ) );
		return $response;
	
	}
}

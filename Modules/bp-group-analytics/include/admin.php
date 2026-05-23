<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * bp_group_analytics_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 *
 */
function bp_group_analytics_admin() {

    do_action('bp_group_analytics_admin');

    /* If the form has been submitted and the admin referrer checks out, save the settings */
    if (!empty($_POST) && check_admin_referer('bpgroup-analytics-settings-save', 'bpgroup-analytics-settings-nonce_field')) {
        bp_group_analytics_import_install();
        if (isset($_POST['xprofile_selected_fields'])) {
            $post_xprofile_selected_fields = sanitize_option(BP_GROUP_ANALYTICS_OPTIONS_META_TITLE, $_POST['xprofile_selected_fields']);
            update_option('BP_GROUP_ANALYTICS_OPTIONS_META_TITLE', $post_xprofile_selected_fields);
        } else {
            update_option('BP_GROUP_ANALYTICS_OPTIONS_META_TITLE', '');
        }

        $updated = true;
    }

    $xprofile_selected_fields_value = get_option('BP_GROUP_ANALYTICS_OPTIONS_META_TITLE');

    $xprofile_selected_fields = array();
    if(!empty($xprofile_selected_fields_value))
        $xprofile_selected_fields = explode(",",$xprofile_selected_fields_value);
    $profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );

    ?>
        <div class="wrap">
            <h2>Buddypress Group Analytics: <?php _e('Settings'); ?></h2>
            <br/>

            <?php
            if (isset($updated))
                echo "<div id='message' class='updated fade'><p>" . __('Settings Updated.', 'bp-group-analytics') . "</p></div>";
            ?>

                <form action="" name="group-analytics-settings-form" id="group-analytics-settings-form" method="post">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="xprofile_selected_fields"><?php _e('Select xprofile fields for charting', 'bp-group-analytics') ?>:</label></th>
                        <td>
                            <select id="xprofile_selected_fields" name="xprofile_selected_fields[]" multiple="multiple" size="10" >
                            <?php
                            if ( !empty( $profile_groups ) ) {
                                foreach ( $profile_groups as $profile_group ) {
                                    if ( !empty( $profile_group->fields ) ) {
                                        foreach ( $profile_group->fields as $field ) {
                                            if(in_array($field->id.'|'.$field->name, $xprofile_selected_fields)){
                                                echo '<option selected="selected" value="'.$field->id.'|'.$field->name. '">' . $field->name . '</option> ';
                                            } else {
                                                echo '<option value="'.$field->id.'|'.$field->name. '">' . $field->name . '</option> ';
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" value="<?php _e('Save Settings', 'bp-group-analytics') ?>"/>
                </p>
                    <?php wp_nonce_field('bpgroup-analytics-settings-save', 'bpgroup-analytics-settings-nonce_field'); ?>

                </form>
            </div><!-- .wrap -->
            <?php
        }

/**
 * Finds the url of settings page
 * @global type $wpdb
 * @global type $bp
 * @return string
 *
 */
function bp_group_analytics_find_admin_location() {
    global $wpdb, $bp;
    if (!is_super_admin())
        return false;
    // test for BP1.6+ (truncated to allow testing on beta versions)
    if (version_compare(substr(BP_VERSION, 0, 3), '1.6', '>=')) {
        // BuddyPress 1.6 moves its admin pages elsewhere, so use Settings menu
        $locationMu = 'settings.php';
    } else {
        // versions prior to 1.6 have a BuddyPress top-level menu
        $locationMu = 'bp-general-settings';
    }
    $location = bp_core_do_network_admin() ? $locationMu : 'options-general.php';
    return $location;
}

/**
 *
 * @global type $wpdb
 * @global type $bp
 * @return boolean
 *
 */
function bp_group_analytics_group_add_admin_menu() {
    global $wpdb, $bp;
    /* Add the administration tab under the "Site Admin" tab for site administrators */
    $page = add_submenu_page(
            bp_group_analytics_find_admin_location(), 'Buddypress Group Analytics ' . __('Settings'), '<span class="bp-group-analytics-admin-menu-header">' . __('Buddypress Group Analytics', 'bp-group-analytics') . '</span>', 'manage_options', 'bp-group-analytics-settings', 'bp_group_analytics_admin');
}

add_action(bp_core_admin_hook(), 'bp_group_analytics_group_add_admin_menu', 10);

/**
 * Add settings link on plugin page
 * @param type $links
 * @param type $file
 * @return array
 *
 */
function bp_group_analytics_settings_link($links, $file) {
    $this_plugin = BP_GROUP_ANALYTICS_DIR . '/loader.php';
    if ($file == $this_plugin) {
        return array_merge($links, array(
            'settings' => '<a href="' . add_query_arg(array('page' => 'bp-group-analytics-settings'), bp_group_analytics_find_admin_location()) . '">' . esc_html__('Settings', 'bp-group-analytics') . '</a>',
        ));
    }

    return $links;
}

/*
 * validate and sanitize the posted values by applying filter on options
 * todo put a proper naming method to it.
 */
function bp_group_analytics_xprofile_selected_fields($posted_value, $option){
    $option_value = "";

    //get possible profile fields values
    $profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
    $profile_fields = array();
    if ( !empty( $profile_groups ) ) {
        foreach ( $profile_groups as $profile_group ) {
            if ( !empty( $profile_group->fields ) ) {
                foreach ( $profile_group->fields as $field ) {
                    $profile_fields[] = $field->id.'|'.$field->name;
                }
            }
        }
    }

    //validate posted values if exists in the possible values.
    if(is_array($posted_value)){
        foreach($posted_value as $value) {
            if(!in_array($value, $profile_fields)){
                wp_die("Invalid data, Please check the values you have selected.");
            }
        }
        $option_value = sanitize_text_field(implode(",",$posted_value));
    } else {
        if(!in_array($posted_value, $profile_fields)){
            wp_die("Invalid data, Please check the values you have selected.");
        }
        $option_value = sanitize_text_field($posted_value);
    }

    return $option_value;
}

add_filter( 'sanitize_option_bp_group_analytics_xprofile_selected_fields', 'bp_group_analytics_xprofile_selected_fields', 10, 2 );
/// Add link to settings page
add_filter('plugin_action_links', 'bp_group_analytics_settings_link', 10, 2);
add_filter('network_admin_plugin_action_links', 'bp_group_analytics_settings_link', 10, 2);

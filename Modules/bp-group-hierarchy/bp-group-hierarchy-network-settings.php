<?php
/**
 * BP Group Hierarchy — Network (Multisite) settings.
 *
 * Provides a settings page under Network Admin → Settings for
 * controlling hierarchy behaviour across the network.
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 * @updated 2.0.0 — Added visibility scope controls.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------
 * 1. Register Network Settings Page
 * ----------------------------------------------------------- */

function bpgh_add_network_settings_page() {
    add_submenu_page(
        'settings.php',
        __( 'Group Hierarchy Network Settings', 'bp-group-hierarchy' ),
        __( 'Group Hierarchy', 'bp-group-hierarchy' ),
        'manage_network_options',
        'bpgh-network-settings',
        'bpgh_render_network_settings_page'
    );
}
add_action( 'network_admin_menu', 'bpgh_add_network_settings_page' );

/* -----------------------------------------------------------
 * 2. Render Network Settings Page
 * ----------------------------------------------------------- */

function bpgh_render_network_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'BP Group Hierarchy — Network Settings', 'bp-group-hierarchy' ); ?></h1>

        <?php
        if ( isset( $_GET['updated'] ) && 'true' === $_GET['updated'] ) {
            echo '<div class="notice notice-success is-dismissible"><p>';
            esc_html_e( 'Settings saved.', 'bp-group-hierarchy' );
            echo '</p></div>';
        }
        ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'bpgh_network_settings', 'bpgh_network_nonce' ); ?>
            <input type="hidden" name="action" value="bpgh_save_network_settings" />

            <h2><?php esc_html_e( 'Cross-Site Hierarchy', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable cross-site hierarchy', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_network_cross_site">
                            <option value="yes" <?php selected( get_site_option( 'bpgh_network_cross_site', 'no' ), 'yes' ); ?>>
                                <?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?>
                            </option>
                            <option value="no" <?php selected( get_site_option( 'bpgh_network_cross_site', 'no' ), 'no' ); ?>>
                                <?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php esc_html_e( 'When enabled, groups on different sites can participate in the same hierarchy.', 'bp-group-hierarchy' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <!-- v2.0 Visibility Scope Settings -->
            <h2><?php esc_html_e( 'Visibility Scopes', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Default visibility for new groups', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_default_visibility">
                            <option value="network" <?php selected( get_site_option( 'bpgh_default_visibility', 'network' ), 'network' ); ?>>
                                <?php esc_html_e( 'Network-wide', 'bp-group-hierarchy' ); ?>
                            </option>
                            <option value="site" <?php selected( get_site_option( 'bpgh_default_visibility', 'network' ), 'site' ); ?>>
                                <?php esc_html_e( 'Site-only', 'bp-group-hierarchy' ); ?>
                            </option>
                            <option value="hidden" <?php selected( get_site_option( 'bpgh_default_visibility', 'network' ), 'hidden' ); ?>>
                                <?php esc_html_e( 'Hidden', 'bp-group-hierarchy' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php esc_html_e( 'Default visibility scope for newly created groups across the network.', 'bp-group-hierarchy' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Allow subsite widgets', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_allow_subsite_widgets">
                            <option value="yes" <?php selected( get_site_option( 'bpgh_allow_subsite_widgets', 'yes' ), 'yes' ); ?>>
                                <?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?>
                            </option>
                            <option value="no" <?php selected( get_site_option( 'bpgh_allow_subsite_widgets', 'yes' ), 'no' ); ?>>
                                <?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php esc_html_e( 'Allow subsites without BuddyPress to display group hierarchy widgets from the main site.', 'bp-group-hierarchy' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Network group browsing', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_network_browsing">
                            <option value="yes" <?php selected( get_site_option( 'bpgh_network_browsing', 'yes' ), 'yes' ); ?>>
                                <?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?>
                            </option>
                            <option value="no" <?php selected( get_site_option( 'bpgh_network_browsing', 'yes' ), 'no' ); ?>>
                                <?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php esc_html_e( 'Allow users on subsites to browse groups from the main BuddyPress site.', 'bp-group-hierarchy' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/* -----------------------------------------------------------
 * 3. Save Network Settings
 * ----------------------------------------------------------- */

function bpgh_save_network_settings() {

    if ( ! current_user_can( 'manage_network_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to save these settings.', 'bp-group-hierarchy' ) );
    }

    if (
        ! isset( $_POST['bpgh_network_nonce'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bpgh_network_nonce'] ) ), 'bpgh_network_settings' )
    ) {
        wp_die( esc_html__( 'Security check failed.', 'bp-group-hierarchy' ) );
    }

    // v1.x setting.
    $cross_site = isset( $_POST['bpgh_network_cross_site'] )
        ? sanitize_text_field( wp_unslash( $_POST['bpgh_network_cross_site'] ) )
        : 'no';
    update_site_option( 'bpgh_network_cross_site', $cross_site );

    // v2.0 settings.
    $fields = array(
        'bpgh_default_visibility'    => 'network',
        'bpgh_allow_subsite_widgets' => 'yes',
        'bpgh_network_browsing'      => 'yes',
    );

    foreach ( $fields as $key => $default ) {
        $value = isset( $_POST[ $key ] )
            ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) )
            : $default;
        update_site_option( $key, $value );
    }

    wp_safe_redirect(
        add_query_arg(
            array(
                'page'    => 'bpgh-network-settings',
                'updated' => 'true',
            ),
            network_admin_url( 'settings.php' )
        )
    );
    exit;
}
add_action( 'admin_post_bpgh_save_network_settings', 'bpgh_save_network_settings' );

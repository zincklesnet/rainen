<?php
/**
 * BP Group Hierarchy — Settings page.
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 * @updated 2.0.5 — Fixed menu registration to work across all BP versions.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------
 * 1. Register Settings Page
 *
 * BP 12+ removed the `bp-settings` parent slug. We now try
 * multiple parent slugs, falling back to a top-level menu.
 * ----------------------------------------------------------- */

function bpgh_add_settings_page() {

    if ( ! function_exists( 'buddypress' ) ) {
        return;
    }

    $bp_parent_slugs = array( 'buddypress', 'bp-general-settings', 'bp-settings' );
    $parent_found    = false;

    global $menu;
    if ( is_array( $menu ) ) {
        foreach ( $bp_parent_slugs as $slug ) {
            foreach ( $menu as $item ) {
                if ( isset( $item[2] ) && $item[2] === $slug ) {
                    $parent_found = $slug;
                    break 2;
                }
            }
        }
    }

    if ( $parent_found ) {
        add_submenu_page(
            $parent_found,
            __( 'Group Hierarchy Settings', 'bp-group-hierarchy' ),
            __( 'Group Hierarchy', 'bp-group-hierarchy' ),
            'manage_options',
            'bpgh-settings',
            'bpgh_render_settings_page'
        );
    } else {
        add_menu_page(
            __( 'Group Hierarchy Settings', 'bp-group-hierarchy' ),
            __( 'Group Hierarchy', 'bp-group-hierarchy' ),
            'manage_options',
            'bpgh-settings',
            'bpgh_render_settings_page',
            'dashicons-networking',
            56
        );
    }
}
add_action( 'admin_menu', 'bpgh_add_settings_page', 99 );

/* -----------------------------------------------------------
 * 2. Register Settings
 * ----------------------------------------------------------- */

function bpgh_register_settings() {

    $settings = array(
        'bpgh_show_parent_in_header' => 'yes',
        'bpgh_show_children_list'    => 'yes',

        // v2.0 feature toggles.
        'bpgh_enable_tags'           => 'yes',
        'bpgh_enable_categories'     => 'yes',
        'bpgh_enable_premium'        => 'no',
        'bpgh_enable_tooltips'       => 'yes',

        // v2.0 permissions.
        'bpgh_parent_creation_role'  => 'admin',
        'bpgh_require_child_approval'=> 'yes',

        // v2.0 premium settings.
        'bpgh_premium_cost'          => '100',
        'bpgh_zcred_point_type'      => 'mycred_default',

        // v2.0 tag moderation.
        'bpgh_tag_moderation'        => 'no',

        // v2.0 tooltip content.
        'bpgh_tooltip_show_admins'   => 'yes',
        'bpgh_tooltip_show_tags'     => 'yes',
        'bpgh_tooltip_show_category' => 'yes',

        // v2.0 visibility inheritance.
        'bpgh_visibility_inheritance'=> 'yes',
    );

    foreach ( $settings as $key => $default ) {
        register_setting( 'bpgh_settings_group', $key, array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => $default,
        ) );
    }
}
add_action( 'admin_init', 'bpgh_register_settings' );

/* -----------------------------------------------------------
 * 3. Render Settings Page
 * ----------------------------------------------------------- */

function bpgh_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'BP Group Hierarchy Settings', 'bp-group-hierarchy' ); ?></h1>

        <form method="post" action="options.php">
            <?php settings_fields( 'bpgh_settings_group' ); ?>
            <?php do_settings_sections( 'bpgh_settings_group' ); ?>

            <!-- Display Settings -->
            <h2><?php esc_html_e( 'Display', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show parent label in group header', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_show_parent_in_header">
                            <option value="yes" <?php selected( get_option( 'bpgh_show_parent_in_header', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_show_parent_in_header', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show child groups list on group pages', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_show_children_list">
                            <option value="yes" <?php selected( get_option( 'bpgh_show_children_list', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_show_children_list', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>

            <!-- Feature Toggles -->
            <h2><?php esc_html_e( 'Features', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable group tags', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_enable_tags">
                            <option value="yes" <?php selected( get_option( 'bpgh_enable_tags', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_enable_tags', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable group categories', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_enable_categories">
                            <option value="yes" <?php selected( get_option( 'bpgh_enable_categories', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_enable_categories', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable premium group tiers', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_enable_premium">
                            <option value="yes" <?php selected( get_option( 'bpgh_enable_premium', 'no' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_enable_premium', 'no' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable hover tooltips', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_enable_tooltips">
                            <option value="yes" <?php selected( get_option( 'bpgh_enable_tooltips', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_enable_tooltips', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>

            <!-- Permissions -->
            <h2><?php esc_html_e( 'Permissions', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Who can create parent (top-level) groups?', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_parent_creation_role">
                            <option value="admin" <?php selected( get_option( 'bpgh_parent_creation_role', 'admin' ), 'admin' ); ?>><?php esc_html_e( 'Admins only', 'bp-group-hierarchy' ); ?></option>
                            <option value="any" <?php selected( get_option( 'bpgh_parent_creation_role', 'admin' ), 'any' ); ?>><?php esc_html_e( 'Any logged-in user', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Require admin approval for child groups?', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_require_child_approval">
                            <option value="yes" <?php selected( get_option( 'bpgh_require_child_approval', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_require_child_approval', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Children inherit parent visibility?', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_visibility_inheritance">
                            <option value="yes" <?php selected( get_option( 'bpgh_visibility_inheritance', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_visibility_inheritance', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>

            <!-- Tooltip Content -->
            <h2><?php esc_html_e( 'Tooltip Content', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show group admins in tooltip', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_tooltip_show_admins">
                            <option value="yes" <?php selected( get_option( 'bpgh_tooltip_show_admins', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_tooltip_show_admins', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show tags in tooltip', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_tooltip_show_tags">
                            <option value="yes" <?php selected( get_option( 'bpgh_tooltip_show_tags', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_tooltip_show_tags', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show category in tooltip', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_tooltip_show_category">
                            <option value="yes" <?php selected( get_option( 'bpgh_tooltip_show_category', 'yes' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_tooltip_show_category', 'yes' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>

            <!-- Tag Moderation -->
            <h2><?php esc_html_e( 'Tag Moderation', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable tag moderation (admin-approved tags only)', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <select name="bpgh_tag_moderation">
                            <option value="yes" <?php selected( get_option( 'bpgh_tag_moderation', 'no' ), 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                            <option value="no" <?php selected( get_option( 'bpgh_tag_moderation', 'no' ), 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>

            <!-- Premium & ZCreds -->
            <h2><?php esc_html_e( 'Premium & ZCreds', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Premium upgrade cost (ZCreds)', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <input type="number" name="bpgh_premium_cost" value="<?php echo esc_attr( get_option( 'bpgh_premium_cost', '100' ) ); ?>" min="0" step="1" class="small-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'myCred point type', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <input type="text" name="bpgh_zcred_point_type" value="<?php echo esc_attr( get_option( 'bpgh_zcred_point_type', 'mycred_default' ) ); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e( 'The myCred point type slug used for group upgrades.', 'bp-group-hierarchy' ); ?></p>
                    </td>
                </tr>
            </table>

            <!-- Category Management -->
            <h2><?php esc_html_e( 'Category Management', 'bp-group-hierarchy' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Categories', 'bp-group-hierarchy' ); ?></th>
                    <td>
                        <?php
                        $cats = array();
                        if ( class_exists( 'BPGH_Categories' ) ) {
                            $cats = BPGH_Categories::get_categories();
                        }
                        $cats_text = '';
                        if ( ! empty( $cats ) ) {
                            foreach ( $cats as $slug => $label ) {
                                $cats_text .= $slug . ':' . $label . "\n";
                            }
                        }
                        ?>
                        <textarea name="bpgh_categories_raw" rows="6" cols="50" class="large-text code"><?php echo esc_textarea( trim( $cats_text ) ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'One category per line in slug:Label format. Example: sports:Sports', 'bp-group-hierarchy' ); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/* -----------------------------------------------------------
 * 4. Save Categories from Settings Page
 * ----------------------------------------------------------- */

add_action( 'update_option_bpgh_categories_raw', 'bpgh_save_categories_from_settings', 10, 2 );

/**
 * Parse the raw categories textarea and save via BPGH_Categories.
 */
function bpgh_save_categories_from_settings( $old_value, $new_value ) {
    if ( ! class_exists( 'BPGH_Categories' ) ) {
        return;
    }

    $lines = explode( "\n", $new_value );
    $cats  = array();

    foreach ( $lines as $line ) {
        $line = trim( $line );
        if ( empty( $line ) ) {
            continue;
        }

        $parts = explode( ':', $line, 2 );
        if ( count( $parts ) === 2 ) {
            $slug  = sanitize_title( trim( $parts[0] ) );
            $label = sanitize_text_field( trim( $parts[1] ) );
            if ( ! empty( $slug ) && ! empty( $label ) ) {
                $cats[ $slug ] = $label;
            }
        }
    }

    BPGH_Categories::save_categories( $cats );
}

// Register the raw categories field so WP processes it.
add_action( 'admin_init', function () {
    register_setting( 'bpgh_settings_group', 'bpgh_categories_raw', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default'           => '',
    ) );
} );

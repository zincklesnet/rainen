<?php
/**
 * BP Group Hierarchy — Actions (save / render).
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 * @updated 2.0.0 — Category/tag/permission/premium integration in group create/edit.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------
 * 1. Render Parent Group Dropdown (Create + Edit)
 *
 * v2.0: Respects BPGH_Permissions — hides "No Parent" for non-admins
 *       when parent creation is restricted. Also renders category,
 *       tag, and premium tier fields.
 * ----------------------------------------------------------- */

function bpgh_render_parent_group_dropdown() {

    if ( ! function_exists( 'bp_is_group_creation_step' ) || ! function_exists( 'bp_is_group_admin_screen' ) ) {
        return;
    }

    $is_creation      = bp_is_group_creation_step( 'group-details' );
    $is_frontend_edit = bp_is_group_admin_screen( 'edit-details' );

    if ( ! $is_creation && ! $is_frontend_edit ) {
        return;
    }

    if ( ! function_exists( 'bp_get_current_group_id' ) ) {
        return;
    }

    $current_group_id = bp_get_current_group_id();
    $parent_id        = BP_Group_Hierarchy::get_parent_id( $current_group_id );
    $user_id          = get_current_user_id();

    // Permission check: can user create parent groups?
    $can_create_parent = true;
    if ( class_exists( 'BPGH_Permissions' ) ) {
        $can_create_parent = BPGH_Permissions::can_create_parent_group( $user_id );
    }

    $groups = groups_get_groups( array(
        'show_hidden' => true,
        'per_page'    => 500,
    ) );

    ?>
    <div class="bpgh-parent-group-wrap" style="margin: 20px 0;">
        <label for="bpgh-parent-group" style="font-weight: bold; display: block; margin-bottom: 6px;">
            <?php esc_html_e( 'Parent Group', 'bp-group-hierarchy' ); ?>
        </label>

        <select name="bpgh_parent_group" id="bpgh-parent-group" style="min-width: 260px;">
            <?php if ( $can_create_parent ) : ?>
                <option value="0"><?php esc_html_e( '— No Parent (Top Level) —', 'bp-group-hierarchy' ); ?></option>
            <?php endif; ?>

            <?php foreach ( $groups['groups'] as $group ) : ?>
                <?php if ( (int) $group->id === (int) $current_group_id ) continue; ?>
                <option value="<?php echo esc_attr( $group->id ); ?>"
                    <?php selected( $parent_id, $group->id ); ?>>
                    <?php echo esc_html( $group->name ); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p class="description">
            <?php if ( ! $can_create_parent ) : ?>
                <?php esc_html_e( 'You must select a parent group. Only admins can create top-level groups.', 'bp-group-hierarchy' ); ?>
            <?php else : ?>
                <?php esc_html_e( 'Select a parent group to place this group inside a hierarchy.', 'bp-group-hierarchy' ); ?>
            <?php endif; ?>
        </p>

        <?php wp_nonce_field( 'bpgh_save_parent_group', 'bpgh_parent_nonce' ); ?>
    </div>

    <?php
    // Render category selector (v2.0).
    bpgh_render_category_field( $current_group_id );

    // Render tag input (v2.0).
    bpgh_render_tag_fields( $current_group_id );

    // Show premium tier info (v2.0).
    bpgh_render_premium_info( $current_group_id );
}

/* Frontend hooks */
add_action( 'bp_after_group_details_creation_step', 'bpgh_render_parent_group_dropdown' );
add_action( 'bp_group_admin_edit_after', 'bpgh_render_parent_group_dropdown' );


/* -----------------------------------------------------------
 * 1b. Category Selector Field (v2.0)
 * ----------------------------------------------------------- */

function bpgh_render_category_field( $group_id ) {

    if ( ! class_exists( 'BPGH_Categories' ) ) {
        return;
    }

    if ( 'yes' !== get_option( 'bpgh_enable_categories', 'yes' ) ) {
        return;
    }

    $categories       = BPGH_Categories::get_categories();
    $current_category = BPGH_Categories::get_group_category( $group_id );

    if ( empty( $categories ) ) {
        return;
    }

    ?>
    <div class="bpgh-category-wrap" style="margin: 20px 0;">
        <label for="bpgh-category" style="font-weight: bold; display: block; margin-bottom: 6px;">
            <?php esc_html_e( 'Group Category', 'bp-group-hierarchy' ); ?>
        </label>

        <select name="bpgh_category" id="bpgh-category" style="min-width: 260px;">
            <option value=""><?php esc_html_e( '— Select Category —', 'bp-group-hierarchy' ); ?></option>
            <?php foreach ( $categories as $slug => $label ) : ?>
                <option value="<?php echo esc_attr( $slug ); ?>"
                    <?php selected( $current_category, $slug ); ?>>
                    <?php echo esc_html( $label ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}


/* -----------------------------------------------------------
 * 1c. Tag Input Fields (v2.0)
 * ----------------------------------------------------------- */

function bpgh_render_tag_fields( $group_id ) {

    if ( ! class_exists( 'BPGH_Tags' ) ) {
        return;
    }

    if ( 'yes' !== get_option( 'bpgh_enable_tags', 'yes' ) ) {
        return;
    }

    $tags = BPGH_Tags::get_tags( $group_id );

    ?>
    <div class="bpgh-tags-wrap" style="margin: 20px 0;">
        <label style="font-weight: bold; display: block; margin-bottom: 6px;">
            <?php esc_html_e( 'Group Tags (max 3)', 'bp-group-hierarchy' ); ?>
        </label>

        <?php for ( $i = 0; $i < 3; $i++ ) : ?>
            <input type="text"
                   name="bpgh_tags[]"
                   value="<?php echo esc_attr( isset( $tags[ $i ] ) ? $tags[ $i ] : '' ); ?>"
                   placeholder="<?php printf( esc_attr__( 'Tag %d', 'bp-group-hierarchy' ), $i + 1 ); ?>"
                   class="bpgh-tag-input"
                   style="display: block; margin-bottom: 4px; min-width: 200px;"
                   maxlength="50" />
        <?php endfor; ?>

        <p class="description"><?php esc_html_e( 'Add up to 3 tags to help users find your group.', 'bp-group-hierarchy' ); ?></p>
    </div>
    <?php
}


/* -----------------------------------------------------------
 * 1d. Premium Tier Info (v2.0)
 * ----------------------------------------------------------- */

function bpgh_render_premium_info( $group_id ) {

    if ( ! class_exists( 'BPGH_Premium' ) ) {
        return;
    }

    if ( 'yes' !== get_option( 'bpgh_enable_premium', 'no' ) ) {
        return;
    }

    $tier = BPGH_Premium::get_tier( $group_id );

    ?>
    <div class="bpgh-premium-wrap" style="margin: 20px 0;">
        <label style="font-weight: bold; display: block; margin-bottom: 6px;">
            <?php esc_html_e( 'Group Tier', 'bp-group-hierarchy' ); ?>
        </label>

        <?php if ( 'premium' === $tier ) : ?>
            <p><span class="bpgh-badge bpgh-badge--premium">&#9733; <?php esc_html_e( 'Premium', 'bp-group-hierarchy' ); ?></span></p>
            <p class="description"><?php esc_html_e( 'This group has premium features enabled. Manage premium options in the group settings.', 'bp-group-hierarchy' ); ?></p>
        <?php else : ?>
            <p><?php esc_html_e( 'Free tier — upgrade with ZCreds to unlock premium features.', 'bp-group-hierarchy' ); ?></p>
            <?php
            $cost = (int) get_option( 'bpgh_premium_cost', 100 );
            if ( $cost > 0 ) :
            ?>
                <p class="description">
                    <?php printf(
                        /* translators: %d: ZCred cost */
                        esc_html__( 'Premium upgrade cost: %d ZCreds', 'bp-group-hierarchy' ),
                        $cost
                    ); ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}


/* -----------------------------------------------------------
 * 2. Save Parent Group (New + Existing Groups)
 * ----------------------------------------------------------- */

function bpgh_save_parent_group( $group ) {

    if ( ! isset( $_POST['bpgh_parent_group'] ) ) {
        return;
    }

    // CSRF protection.
    if (
        ! isset( $_POST['bpgh_parent_nonce'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bpgh_parent_nonce'] ) ), 'bpgh_save_parent_group' )
    ) {
        return;
    }

    $group_id  = is_object( $group ) ? (int) $group->id : (int) $group;
    $parent_id = absint( $_POST['bpgh_parent_group'] );

    // Prevent circular reference.
    if ( $parent_id && BP_Group_Hierarchy::is_descendant_of( $parent_id, $group_id ) ) {
        return;
    }

    BP_Group_Hierarchy::set_parent_id( $group_id, $parent_id );

    // v2.0: If child group created by non-admin, mark as pending approval.
    if ( $parent_id > 0 && class_exists( 'BPGH_Permissions' ) ) {
        $user_id = get_current_user_id();
        if ( BPGH_Permissions::requires_approval( $user_id, $parent_id ) ) {
            BPGH_Permissions::set_pending( $group_id );
        }
    }

    // v2.0: Save category.
    if ( isset( $_POST['bpgh_category'] ) && class_exists( 'BPGH_Categories' ) ) {
        $category = sanitize_text_field( wp_unslash( $_POST['bpgh_category'] ) );
        BPGH_Categories::set_group_category( $group_id, $category );
    }

    // v2.0: Save tags.
    if ( isset( $_POST['bpgh_tags'] ) && is_array( $_POST['bpgh_tags'] ) && class_exists( 'BPGH_Tags' ) ) {
        $tags = array_map( 'sanitize_text_field', wp_unslash( $_POST['bpgh_tags'] ) );
        $tags = array_filter( $tags );
        $tags = array_slice( $tags, 0, 3 ); // Max 3 tags.
        BPGH_Tags::set_tags( $group_id, $tags );
    }
}

// Creation save — fires when a new group is fully created.
add_action( 'groups_group_create_complete', 'bpgh_save_parent_group' );

// Edit save — fires when group details are updated from the front-end admin.
add_action( 'groups_group_details_edited', 'bpgh_save_parent_group' );

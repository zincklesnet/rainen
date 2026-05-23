<?php
/**
 * BP Group Hierarchy — Permissions & Role-Based Creation.
 *
 * Controls who can create parent groups vs. child groups,
 * enforces network-admin approval for child groups, and
 * manages category-restricted creation.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Permissions {

    /**
     * Group meta key for pending-approval status.
     */
    const PENDING_META = 'bpgh_pending_approval';

    /**
     * Group meta key for group visibility scope.
     */
    const VISIBILITY_META = 'bpgh_visibility';

    /* =============================================================
     * Parent / Child Creation Rules
     * ============================================================= */

    /**
     * Check whether the current user can create a parent (top-level) group.
     *
     * Only admins/network admins can create parent groups.
     *
     * @param int $user_id Optional. Defaults to current user.
     * @return bool
     */
    public static function can_create_parent_group( $user_id = 0 ) {
        if ( 0 === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( 0 === $user_id ) {
            return false;
        }

        // Network admin or site admin can always create parent groups.
        if ( is_super_admin( $user_id ) || user_can( $user_id, 'manage_options' ) ) {
            return true;
        }

        // Check option: who can create parents?
        $allowed = get_option( 'bpgh_parent_creation_role', 'admin' );

        if ( 'any' === $allowed ) {
            return true;
        }

        return false; // Default: admin-only.
    }

    /**
     * Check whether the current user can create a child group.
     *
     * All logged-in users can create child groups, but may require
     * network admin approval depending on settings.
     *
     * @param int $user_id  Optional. Defaults to current user.
     * @param int $parent_id Parent group ID.
     * @return bool
     */
    public static function can_create_child_group( $user_id = 0, $parent_id = 0 ) {
        if ( 0 === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( 0 === $user_id ) {
            return false;
        }

        // Admins always can.
        if ( is_super_admin( $user_id ) || user_can( $user_id, 'manage_options' ) ) {
            return true;
        }

        // Must be logged in.
        if ( ! is_user_logged_in() ) {
            return false;
        }

        // If parent is specified, check membership (optional).
        if ( $parent_id > 0 && 'yes' === get_option( 'bpgh_require_parent_membership', 'no' ) ) {
            if ( function_exists( 'groups_is_user_member' ) && ! groups_is_user_member( $user_id, $parent_id ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether child group creation requires network admin approval.
     *
     * @return bool
     */
    public static function requires_approval() {
        return 'yes' === get_option( 'bpgh_child_requires_approval', 'yes' );
    }

    /**
     * Mark a group as pending approval.
     *
     * @param int $group_id Group ID.
     * @return bool|int
     */
    public static function set_pending( $group_id ) {
        return groups_update_groupmeta( absint( $group_id ), self::PENDING_META, 'yes' );
    }

    /**
     * Check if a group is pending approval.
     *
     * @param int $group_id Group ID.
     * @return bool
     */
    public static function is_pending( $group_id ) {
        return 'yes' === groups_get_groupmeta( absint( $group_id ), self::PENDING_META, true );
    }

    /**
     * Approve a pending group.
     *
     * @param int $group_id Group ID.
     * @return bool
     */
    public static function approve_group( $group_id ) {
        $group_id = absint( $group_id );
        groups_delete_groupmeta( $group_id, self::PENDING_META );

        /**
         * Fires when a child group is approved.
         *
         * @param int $group_id The approved group ID.
         */
        do_action( 'bpgh_group_approved', $group_id );

        return true;
    }

    /**
     * Reject (delete) a pending group.
     *
     * @param int $group_id Group ID.
     * @return bool
     */
    public static function reject_group( $group_id ) {
        $group_id = absint( $group_id );

        /**
         * Fires before a child group is rejected/deleted.
         *
         * @param int $group_id The rejected group ID.
         */
        do_action( 'bpgh_group_rejected', $group_id );

        if ( function_exists( 'groups_delete_group' ) ) {
            return groups_delete_group( $group_id );
        }

        return false;
    }

    /**
     * Get all groups pending approval.
     *
     * @return array Group objects.
     */
    public static function get_pending_groups() {
        $args = array(
            'show_hidden' => true,
            'per_page'    => 0,
            'meta_query'  => array(
                array(
                    'key'     => self::PENDING_META,
                    'value'   => 'yes',
                    'compare' => '=',
                ),
            ),
        );

        $result = groups_get_groups( $args );

        return ! empty( $result['groups'] ) ? $result['groups'] : array();
    }

    /* =============================================================
     * Group Visibility (Multisite)
     * ============================================================= */

    /**
     * Set group visibility scope.
     *
     * @param int    $group_id   Group ID.
     * @param string $visibility 'network', 'site', or 'hidden'.
     * @return bool|int
     */
    public static function set_visibility( $group_id, $visibility ) {
        $allowed = array( 'network', 'site', 'hidden' );
        $visibility = in_array( $visibility, $allowed, true ) ? $visibility : 'site';

        return groups_update_groupmeta( absint( $group_id ), self::VISIBILITY_META, $visibility );
    }

    /**
     * Get group visibility scope.
     *
     * @param int $group_id Group ID.
     * @return string 'network', 'site', or 'hidden'.
     */
    public static function get_visibility( $group_id ) {
        $vis = groups_get_groupmeta( absint( $group_id ), self::VISIBILITY_META, true );
        $allowed = array( 'network', 'site', 'hidden' );

        return in_array( $vis, $allowed, true ) ? $vis : 'site';
    }

    /**
     * Check if a group is visible network-wide.
     *
     * @param int $group_id Group ID.
     * @return bool
     */
    public static function is_network_visible( $group_id ) {
        return 'network' === self::get_visibility( $group_id );
    }

    /* =============================================================
     * Hierarchy Rules
     * ============================================================= */

    /**
     * Check if children inherit visibility from parent.
     *
     * @return bool
     */
    public static function children_inherit_visibility() {
        return 'yes' === get_option( 'bpgh_inherit_visibility', 'yes' );
    }

    /**
     * Enforce visibility inheritance when a child group is saved.
     *
     * @param int $group_id Group ID.
     */
    public static function maybe_inherit_visibility( $group_id ) {
        if ( ! self::children_inherit_visibility() ) {
            return;
        }

        $parent_id = BP_Group_Hierarchy::get_parent_id( $group_id );

        if ( 0 === $parent_id ) {
            return;
        }

        $parent_vis = self::get_visibility( $parent_id );
        self::set_visibility( $group_id, $parent_vis );
    }
}

/* -----------------------------------------------------------
 * Hook: enforce parent-only creation for non-admins
 * ----------------------------------------------------------- */

add_action( 'bp_before_group_creation_step', function () {
    // If user cannot create parent groups, auto-hide the "No Parent" option.
    // This is handled in the actions file dropdown render.
}, 10 );

/* -----------------------------------------------------------
 * Hook: set pending status on child group creation
 * ----------------------------------------------------------- */

add_action( 'groups_group_after_save', function ( $group ) {
    if ( ! isset( $group->id ) ) {
        return;
    }

    $parent_id = BP_Group_Hierarchy::get_parent_id( $group->id );

    // Only auto-pend child groups created by non-admins.
    if ( $parent_id > 0 && BPGH_Permissions::requires_approval() ) {
        $user_id = get_current_user_id();
        if ( ! is_super_admin( $user_id ) && ! user_can( $user_id, 'manage_options' ) ) {
            BPGH_Permissions::set_pending( $group->id );
        }
    }

    // Inherit visibility.
    BPGH_Permissions::maybe_inherit_visibility( $group->id );
}, 20 );

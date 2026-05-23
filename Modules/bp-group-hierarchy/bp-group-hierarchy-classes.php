<?php
/**
 * BP Group Hierarchy — Core hierarchy class.
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Manages parent–child relationships between BuddyPress groups
 * using the `bpgh_parent_id` group-meta key.
 */
class BP_Group_Hierarchy {

    /**
     * Hard ceiling on ancestor / descendant traversal depth.
     * Prevents run-away recursion if group-meta is corrupted.
     *
     * @var int
     */
    const MAX_DEPTH = 50;

    /**
     * Per-request cache for parent IDs.
     *
     * @var array<int, int>
     */
    private static $parent_cache = array();

    /* =============================================================
     * Core getters / setters
     * ============================================================= */

    /**
     * Get the parent group ID for a given group.
     *
     * @param int $group_id Group ID.
     * @return int Parent group ID, or 0 if top-level.
     */
    public static function get_parent_id( $group_id ) {
        $group_id = absint( $group_id );

        if ( isset( self::$parent_cache[ $group_id ] ) ) {
            return self::$parent_cache[ $group_id ];
        }

        $parent = (int) groups_get_groupmeta( $group_id, 'bpgh_parent_id', true );
        self::$parent_cache[ $group_id ] = $parent;

        return $parent;
    }

    /**
     * Set the parent group ID for a given group.
     *
     * @param int $group_id  Group ID.
     * @param int $parent_id Parent group ID (0 = top-level).
     * @return bool|int Meta ID on success, false on failure.
     */
    public static function set_parent_id( $group_id, $parent_id ) {
        $group_id  = absint( $group_id );
        $parent_id = absint( $parent_id );

        // Bust the per-request cache.
        unset( self::$parent_cache[ $group_id ] );

        return groups_update_groupmeta( $group_id, 'bpgh_parent_id', $parent_id );
    }

    /* =============================================================
     * Hierarchy traversal
     * ============================================================= */

    /**
     * Check whether $group_id is a descendant of $ancestor_id.
     *
     * Walks up the parent chain with a visited-set guard to detect
     * circular references and a hard depth limit.
     *
     * @param int $group_id    The potential descendant.
     * @param int $ancestor_id The potential ancestor.
     * @return bool
     */
    public static function is_descendant_of( $group_id, $ancestor_id ) {
        $group_id    = absint( $group_id );
        $ancestor_id = absint( $ancestor_id );

        if ( 0 === $group_id || 0 === $ancestor_id ) {
            return false;
        }

        $current = $group_id;
        $visited = array();

        for ( $i = 0; $i < self::MAX_DEPTH; $i++ ) {
            $parent = self::get_parent_id( $current );

            if ( 0 === $parent ) {
                return false;
            }

            if ( $parent === $ancestor_id ) {
                return true;
            }

            // Circular-reference guard.
            if ( isset( $visited[ $current ] ) ) {
                return false;
            }
            $visited[ $current ] = true;

            $current = $parent;
        }

        return false;
    }

    /**
     * Get the immediate children of a group.
     *
     * Uses a BuddyPress meta_query instead of loading every group and
     * filtering in PHP, which caused full-table scans on large installs.
     *
     * @param int $group_id Parent group ID (0 = top-level roots).
     * @return array Array of group objects.
     */
    public static function get_children( $group_id ) {
        $group_id = absint( $group_id );

        $args = array(
            'show_hidden' => true,
            'per_page'    => 0,
            'meta_query'  => array(
                array(
                    'key'     => 'bpgh_parent_id',
                    'value'   => $group_id,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            ),
        );

        $result = groups_get_groups( $args );

        return ! empty( $result['groups'] ) ? $result['groups'] : array();
    }

    /* =============================================================
     * Extended API (v1.1.0)
     * ============================================================= */

    /**
     * Get all ancestors of a group, ordered root-first.
     *
     * Useful for breadcrumb rendering. Includes circular-reference
     * protection and a hard depth limit.
     *
     * @param int $group_id Group ID.
     * @return array Array of group objects, root-first.
     */
    public static function get_ancestors( $group_id ) {
        $group_id  = absint( $group_id );
        $ancestors = array();
        $current   = $group_id;
        $visited   = array();

        for ( $i = 0; $i < self::MAX_DEPTH; $i++ ) {
            $parent_id = self::get_parent_id( $current );

            if ( 0 === $parent_id ) {
                break;
            }

            // Circular-reference guard.
            if ( isset( $visited[ $parent_id ] ) ) {
                break;
            }
            $visited[ $parent_id ] = true;

            $group = groups_get_group( $parent_id );

            if ( ! empty( $group->id ) ) {
                array_unshift( $ancestors, $group );
            }

            $current = $parent_id;
        }

        return $ancestors;
    }

    /**
     * Build a nested tree structure starting from a parent ID.
     *
     * Each node is an associative array:
     *   [ 'group' => WP_Group_Object|null, 'children' => [ ... ] ]
     *
     * @param int $parent_id Root parent ID (0 = full tree).
     * @param int $max_depth Maximum nesting depth (default MAX_DEPTH).
     * @return array Nested tree array.
     */
    public static function get_tree( $parent_id = 0, $max_depth = 0 ) {
        $parent_id = absint( $parent_id );
        $max_depth = $max_depth > 0 ? $max_depth : self::MAX_DEPTH;

        return self::build_tree_recursive( $parent_id, 1, $max_depth );
    }

    /**
     * Recursive helper for get_tree().
     *
     * @param int $parent_id Current parent.
     * @param int $depth     Current depth.
     * @param int $max_depth Ceiling.
     * @return array
     */
    private static function build_tree_recursive( $parent_id, $depth, $max_depth ) {
        if ( $depth > $max_depth ) {
            return array();
        }

        $children = self::get_children( $parent_id );
        $tree     = array();

        foreach ( $children as $child ) {
            $tree[] = array(
                'group'    => $child,
                'children' => self::build_tree_recursive( $child->id, $depth + 1, $max_depth ),
            );
        }

        return $tree;
    }

    /**
     * Get siblings of a group (other groups sharing the same parent).
     *
     * @param int $group_id Group ID.
     * @return array Array of group objects (excludes the group itself).
     */
    public static function get_siblings( $group_id ) {
        $group_id  = absint( $group_id );
        $parent_id = self::get_parent_id( $group_id );

        if ( 0 === $parent_id ) {
            return array();
        }

        $siblings = self::get_children( $parent_id );

        return array_filter( $siblings, function ( $g ) use ( $group_id ) {
            return absint( $g->id ) !== $group_id;
        } );
    }

    /**
     * Get the nesting depth of a group (0 = top-level).
     *
     * @param int $group_id Group ID.
     * @return int Depth.
     */
    public static function get_depth( $group_id ) {
        return count( self::get_ancestors( absint( $group_id ) ) );
    }

    /**
     * Flush the per-request parent cache.
     *
     * @return void
     */
    public static function flush_cache() {
        self::$parent_cache = array();
    }
}

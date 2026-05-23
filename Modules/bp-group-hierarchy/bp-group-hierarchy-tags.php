<?php
/**
 * BP Group Hierarchy — Group Tag System.
 *
 * Provides tag CRUD, querying, and moderation for BuddyPress groups.
 * Each group can have up to 3 tags. Tags are stored as group meta.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Manages group tags via BuddyPress group meta.
 */
class BPGH_Tags {

    /**
     * Maximum number of tags per group.
     */
    const MAX_TAGS = 3;

    /**
     * Meta key for storing tags on a group.
     */
    const META_KEY = 'bpgh_tags';

    /* =============================================================
     * Tag CRUD
     * ============================================================= */

    /**
     * Get tags for a group.
     *
     * @param int $group_id Group ID.
     * @return array Array of tag strings.
     */
    public static function get_tags( $group_id ) {
        $group_id = absint( $group_id );
        $tags     = groups_get_groupmeta( $group_id, self::META_KEY, true );

        if ( ! is_array( $tags ) ) {
            return array();
        }

        return array_map( 'sanitize_text_field', $tags );
    }

    /**
     * Set tags for a group. Enforces max tag limit and sanitisation.
     *
     * @param int   $group_id Group ID.
     * @param array $tags     Array of tag strings.
     * @return bool|int Meta ID on success, false on failure.
     */
    public static function set_tags( $group_id, $tags ) {
        $group_id = absint( $group_id );

        if ( ! is_array( $tags ) ) {
            $tags = array();
        }

        // Sanitise, de-duplicate, and enforce limit.
        $tags = array_map( 'sanitize_text_field', $tags );
        $tags = array_map( 'strtolower', $tags );
        $tags = array_unique( array_filter( $tags ) );
        $tags = array_slice( $tags, 0, self::MAX_TAGS );
        $tags = array_values( $tags );

        return groups_update_groupmeta( $group_id, self::META_KEY, $tags );
    }

    /**
     * Add a single tag to a group (if under limit).
     *
     * @param int    $group_id Group ID.
     * @param string $tag      Tag to add.
     * @return bool True on success, false if at limit or duplicate.
     */
    public static function add_tag( $group_id, $tag ) {
        $tags = self::get_tags( $group_id );
        $tag  = sanitize_text_field( strtolower( $tag ) );

        if ( empty( $tag ) || in_array( $tag, $tags, true ) ) {
            return false;
        }

        if ( count( $tags ) >= self::MAX_TAGS ) {
            return false;
        }

        $tags[] = $tag;

        return (bool) self::set_tags( $group_id, $tags );
    }

    /**
     * Remove a single tag from a group.
     *
     * @param int    $group_id Group ID.
     * @param string $tag      Tag to remove.
     * @return bool True on success.
     */
    public static function remove_tag( $group_id, $tag ) {
        $tags = self::get_tags( $group_id );
        $tag  = sanitize_text_field( strtolower( $tag ) );
        $key  = array_search( $tag, $tags, true );

        if ( false === $key ) {
            return false;
        }

        unset( $tags[ $key ] );

        return (bool) self::set_tags( $group_id, array_values( $tags ) );
    }

    /* =============================================================
     * Tag Queries
     * ============================================================= */

    /**
     * Get all groups that have a specific tag.
     *
     * @param string $tag      Tag to search for.
     * @param int    $per_page Results per page (0 = all).
     * @param int    $page     Page number (1-indexed).
     * @return array Array of group objects.
     */
    public static function get_groups_by_tag( $tag, $per_page = 20, $page = 1 ) {
        $tag = sanitize_text_field( strtolower( $tag ) );

        if ( empty( $tag ) ) {
            return array();
        }

        // BuddyPress meta_query with LIKE for serialised array matching.
        $args = array(
            'show_hidden' => false,
            'per_page'    => $per_page,
            'page'        => $page,
            'meta_query'  => array(
                array(
                    'key'     => self::META_KEY,
                    'value'   => $tag,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $result = groups_get_groups( $args );

        return ! empty( $result['groups'] ) ? $result['groups'] : array();
    }

    /**
     * Get all unique tags across all groups with counts.
     *
     * @param int $limit Maximum number of tags to return.
     * @return array Associative array of tag => count, sorted by count descending.
     */
    public static function get_tag_cloud_data( $limit = 50 ) {
        global $wpdb;

        $bp_prefix  = $wpdb->base_prefix;
        $meta_table = $bp_prefix . 'bp_groups_groupmeta';

        // Check table exists.
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $meta_table ) ) !== $meta_table ) {
            return array();
        }

        $rows = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT meta_value FROM {$meta_table} WHERE meta_key = %s AND meta_value != ''",
                self::META_KEY
            )
        );

        $tag_counts = array();

        foreach ( $rows as $serialised ) {
            $tags = maybe_unserialize( $serialised );

            if ( ! is_array( $tags ) ) {
                continue;
            }

            foreach ( $tags as $tag ) {
                $tag = sanitize_text_field( strtolower( $tag ) );
                if ( empty( $tag ) ) {
                    continue;
                }
                if ( ! isset( $tag_counts[ $tag ] ) ) {
                    $tag_counts[ $tag ] = 0;
                }
                $tag_counts[ $tag ]++;
            }
        }

        arsort( $tag_counts );

        return array_slice( $tag_counts, 0, $limit, true );
    }

    /**
     * Search tags matching a query string.
     *
     * @param string $query Search string.
     * @param int    $limit Maximum results.
     * @return array Matching tag strings.
     */
    public static function search_tags( $query, $limit = 10 ) {
        $query     = sanitize_text_field( strtolower( $query ) );
        $all_tags  = self::get_tag_cloud_data( 200 );
        $matches   = array();

        foreach ( $all_tags as $tag => $count ) {
            if ( false !== strpos( $tag, $query ) ) {
                $matches[] = $tag;
            }
            if ( count( $matches ) >= $limit ) {
                break;
            }
        }

        return $matches;
    }

    /* =============================================================
     * Admin Helpers
     * ============================================================= */

    /**
     * Check whether the tagging feature is enabled by admin.
     *
     * @return bool
     */
    public static function is_enabled() {
        return 'yes' === get_option( 'bpgh_enable_tags', 'yes' );
    }

    /**
     * Check whether tag moderation is enabled.
     * When enabled, new tags must be from the approved list.
     *
     * @return bool
     */
    public static function is_moderation_enabled() {
        return 'yes' === get_option( 'bpgh_tag_moderation', 'no' );
    }

    /**
     * Get the admin-approved tag list (when moderation is on).
     *
     * @return array Approved tags.
     */
    public static function get_approved_tags() {
        $raw = get_option( 'bpgh_approved_tags', '' );

        if ( empty( $raw ) ) {
            return array();
        }

        $tags = array_map( 'trim', explode( ',', $raw ) );
        $tags = array_map( 'strtolower', $tags );

        return array_filter( $tags );
    }

    /**
     * Validate tags against approved list (when moderation is active).
     *
     * @param array $tags Tags to validate.
     * @return array Only approved tags.
     */
    public static function validate_tags( $tags ) {
        if ( ! self::is_moderation_enabled() ) {
            return $tags;
        }

        $approved = self::get_approved_tags();

        if ( empty( $approved ) ) {
            return $tags;
        }

        return array_intersect( $tags, $approved );
    }
}

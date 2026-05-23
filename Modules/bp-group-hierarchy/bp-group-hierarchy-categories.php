<?php
/**
 * BP Group Hierarchy — Group Category System.
 *
 * Categories are admin-defined and stored as a WordPress option.
 * Groups are assigned to categories via group meta.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Categories {

    const META_KEY = 'bpgh_category';

    /* =============================================================
     * Category Definition (Admin)
     * ============================================================= */

    /**
     * Get all defined categories.
     *
     * @return array Associative: slug => label.
     */
    public static function get_categories() {
        $cats = get_option( 'bpgh_categories', array() );
        return is_array( $cats ) ? $cats : array();
    }

    /**
     * Save the full category list (admin only).
     *
     * @param array $categories Associative: slug => label.
     * @return bool
     */
    public static function save_categories( $categories ) {
        if ( ! is_array( $categories ) ) {
            return false;
        }

        $clean = array();
        foreach ( $categories as $slug => $label ) {
            $slug  = sanitize_title( $slug );
            $label = sanitize_text_field( $label );
            if ( ! empty( $slug ) && ! empty( $label ) ) {
                $clean[ $slug ] = $label;
            }
        }

        return update_option( 'bpgh_categories', $clean );
    }

    /**
     * Add a single category.
     *
     * @param string $label Category label (slug auto-generated).
     * @return string|false Slug on success, false on failure.
     */
    public static function add_category( $label ) {
        $label = sanitize_text_field( $label );
        $slug  = sanitize_title( $label );

        if ( empty( $slug ) || empty( $label ) ) {
            return false;
        }

        $cats = self::get_categories();

        if ( isset( $cats[ $slug ] ) ) {
            return false;
        }

        $cats[ $slug ] = $label;
        self::save_categories( $cats );

        return $slug;
    }

    /**
     * Remove a category by slug.
     *
     * @param string $slug Category slug.
     * @return bool
     */
    public static function remove_category( $slug ) {
        $cats = self::get_categories();

        if ( ! isset( $cats[ $slug ] ) ) {
            return false;
        }

        unset( $cats[ $slug ] );

        return self::save_categories( $cats );
    }

    /* =============================================================
     * Group ↔ Category Assignment
     * ============================================================= */

    /**
     * Get the category slug for a group.
     *
     * @param int $group_id Group ID.
     * @return string Category slug or empty string.
     */
    public static function get_group_category( $group_id ) {
        $cat = groups_get_groupmeta( absint( $group_id ), self::META_KEY, true );
        return ! empty( $cat ) ? sanitize_title( $cat ) : '';
    }

    /**
     * Get the category label for a group.
     *
     * @param int $group_id Group ID.
     * @return string Category label or empty string.
     */
    public static function get_group_category_label( $group_id ) {
        $slug = self::get_group_category( $group_id );

        if ( empty( $slug ) ) {
            return '';
        }

        $cats = self::get_categories();

        return isset( $cats[ $slug ] ) ? $cats[ $slug ] : '';
    }

    /**
     * Set a group's category.
     *
     * @param int    $group_id Group ID.
     * @param string $slug     Category slug (empty to unset).
     * @return bool|int
     */
    public static function set_group_category( $group_id, $slug ) {
        $group_id = absint( $group_id );
        $slug     = sanitize_title( $slug );

        if ( empty( $slug ) ) {
            return groups_delete_groupmeta( $group_id, self::META_KEY );
        }

        return groups_update_groupmeta( $group_id, self::META_KEY, $slug );
    }

    /* =============================================================
     * Category Queries
     * ============================================================= */

    /**
     * Get all groups in a specific category.
     *
     * @param string $slug     Category slug.
     * @param int    $per_page Results per page.
     * @param int    $page     Page number.
     * @return array Group objects.
     */
    public static function get_groups_by_category( $slug, $per_page = 20, $page = 1 ) {
        $slug = sanitize_title( $slug );

        if ( empty( $slug ) ) {
            return array();
        }

        $args = array(
            'show_hidden' => false,
            'per_page'    => $per_page,
            'page'        => $page,
            'meta_query'  => array(
                array(
                    'key'     => self::META_KEY,
                    'value'   => $slug,
                    'compare' => '=',
                ),
            ),
        );

        $result = groups_get_groups( $args );

        return ! empty( $result['groups'] ) ? $result['groups'] : array();
    }

    /**
     * Get category counts (how many groups per category).
     *
     * @return array slug => count.
     */
    public static function get_category_counts() {
        global $wpdb;

        $bp_prefix  = $wpdb->base_prefix;
        $meta_table = $bp_prefix . 'bp_groups_groupmeta';

        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $meta_table ) ) !== $meta_table ) {
            return array();
        }

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_value AS slug, COUNT(*) AS cnt
                 FROM {$meta_table}
                 WHERE meta_key = %s AND meta_value != ''
                 GROUP BY meta_value
                 ORDER BY cnt DESC",
                self::META_KEY
            )
        );

        $counts = array();
        foreach ( $rows as $row ) {
            $counts[ $row->slug ] = (int) $row->cnt;
        }

        return $counts;
    }

    /**
     * Check if category-restricted group creation is on.
     *
     * @return bool
     */
    public static function is_category_restricted_creation() {
        return 'yes' === get_option( 'bpgh_category_restricted_creation', 'no' );
    }

    /**
     * Check whether categories feature is enabled.
     *
     * @return bool
     */
    public static function is_enabled() {
        return 'yes' === get_option( 'bpgh_enable_categories', 'yes' );
    }
}

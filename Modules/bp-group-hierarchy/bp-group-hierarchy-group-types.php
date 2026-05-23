<?php
/**
 * BP Group Hierarchy — BuddyPress Group Types Integration.
 *
 * Extends BuddyPress group types with hierarchy-aware filtering,
 * sorting, and shortcode support.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Group_Types {

    /**
     * Initialise hooks.
     */
    public static function init() {
        // Add hierarchy columns to group type admin screens.
        add_filter( 'bp_groups_list_table_get_columns', array( __CLASS__, 'add_admin_columns' ) );

        // Filter groups directory by group type + hierarchy.
        add_action( 'bp_groups_directory_group_types', array( __CLASS__, 'render_type_filter_tabs' ) );

        // Extend group type query to support hierarchy filtering.
        add_filter( 'bp_after_has_groups_parse_args', array( __CLASS__, 'inject_hierarchy_args' ) );
    }

    /**
     * Get all registered BuddyPress group types.
     *
     * @return array Associative: type_name => type_object.
     */
    public static function get_group_types() {
        if ( ! function_exists( 'bp_groups_get_group_types' ) ) {
            return array();
        }

        $types = bp_groups_get_group_types( array(), 'objects' );

        return is_array( $types ) ? $types : array();
    }

    /**
     * Get the group type for a specific group.
     *
     * @param int  $group_id Group ID.
     * @param bool $single   Return single type or array.
     * @return string|array
     */
    public static function get_group_type( $group_id, $single = true ) {
        if ( ! function_exists( 'bp_groups_get_group_type' ) ) {
            return $single ? '' : array();
        }

        return bp_groups_get_group_type( absint( $group_id ), $single );
    }

    /**
     * Get groups by type with optional hierarchy filtering.
     *
     * @param string $type       Group type slug.
     * @param int    $parent_id  Filter to children of this parent (0 = any).
     * @param string $sort       Sort by: 'alphabetical', 'newest', 'active', 'popular'.
     * @param int    $per_page   Results per page.
     * @param int    $page       Page number.
     * @return array Group objects.
     */
    public static function get_groups_by_type( $type, $parent_id = 0, $sort = 'active', $per_page = 20, $page = 1 ) {
        $args = array(
            'show_hidden' => false,
            'per_page'    => $per_page,
            'page'        => $page,
            'type'        => $sort,
            'group_type'  => sanitize_text_field( $type ),
        );

        // Add hierarchy filter.
        if ( $parent_id > 0 ) {
            $args['meta_query'] = array(
                array(
                    'key'     => 'bpgh_parent_id',
                    'value'   => absint( $parent_id ),
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            );
        }

        $result = groups_get_groups( $args );

        return ! empty( $result['groups'] ) ? $result['groups'] : array();
    }

    /**
     * Get parent groups filtered by group type.
     *
     * @param string $type     Group type slug.
     * @param string $sort     Sort method.
     * @param int    $per_page Per page.
     * @return array Group objects that are parents (have children).
     */
    public static function get_parent_groups_by_type( $type, $sort = 'active', $per_page = 20 ) {
        $groups = self::get_groups_by_type( $type, 0, $sort, $per_page );

        // Filter to only groups that have children or have no parent.
        return array_filter( $groups, function ( $group ) {
            $parent_id = BP_Group_Hierarchy::get_parent_id( $group->id );
            return 0 === $parent_id;
        } );
    }

    /**
     * Render group type filter tabs on the groups directory.
     */
    public static function render_type_filter_tabs() {
        $types = self::get_group_types();

        if ( empty( $types ) ) {
            return;
        }

        echo '<div class="bpgh-type-filters">';

        foreach ( $types as $slug => $type_obj ) {
            $label = isset( $type_obj->labels['name'] ) ? $type_obj->labels['name'] : $slug;
            $active_class = '';

            if ( isset( $_GET['bpgh_type'] ) && sanitize_text_field( $_GET['bpgh_type'] ) === $slug ) {
                $active_class = ' class="bpgh-active"';
            }

            echo '<a href="' . esc_url( add_query_arg( 'bpgh_type', $slug ) ) . '"' . $active_class . '>';
            echo esc_html( $label );
            echo '</a> ';
        }

        echo '</div>';
    }

    /**
     * Inject hierarchy-aware arguments into BuddyPress group queries.
     *
     * @param array $args BP group query args.
     * @return array Modified args.
     */
    public static function inject_hierarchy_args( $args ) {
        // Filter by group type from URL.
        if ( isset( $_GET['bpgh_type'] ) && ! empty( $_GET['bpgh_type'] ) ) {
            $args['group_type'] = sanitize_text_field( wp_unslash( $_GET['bpgh_type'] ) );
        }

        // Filter by parent from URL.
        if ( isset( $_GET['bpgh_parent'] ) && '' !== $_GET['bpgh_parent'] ) {
            $parent_id = absint( $_GET['bpgh_parent'] );

            if ( ! isset( $args['meta_query'] ) ) {
                $args['meta_query'] = array();
            }

            $args['meta_query'][] = array(
                'key'     => 'bpgh_parent_id',
                'value'   => $parent_id,
                'compare' => '=',
                'type'    => 'NUMERIC',
            );
        }

        // Sort override from URL.
        if ( isset( $_GET['bpgh_sort'] ) ) {
            $allowed_sorts = array( 'active', 'newest', 'alphabetical', 'popular' );
            $sort = sanitize_text_field( wp_unslash( $_GET['bpgh_sort'] ) );

            if ( in_array( $sort, $allowed_sorts, true ) ) {
                $args['type'] = $sort;
            }
        }

        return $args;
    }

    /**
     * Add hierarchy columns to admin group list.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public static function add_admin_columns( $columns ) {
        $columns['bpgh_parent']   = __( 'Parent Group', 'bp-group-hierarchy' );
        $columns['bpgh_children'] = __( 'Children', 'bp-group-hierarchy' );
        return $columns;
    }

    /**
     * Get group type counts with hierarchy awareness.
     *
     * @return array type_slug => array( 'total', 'parents', 'children' ).
     */
    public static function get_type_hierarchy_stats() {
        $types = self::get_group_types();
        $stats = array();

        foreach ( $types as $slug => $type_obj ) {
            $all = self::get_groups_by_type( $slug, 0, 'newest', 0 );
            $parents = 0;
            $children = 0;

            foreach ( $all as $group ) {
                if ( BP_Group_Hierarchy::get_parent_id( $group->id ) > 0 ) {
                    $children++;
                } else {
                    $parents++;
                }
            }

            $stats[ $slug ] = array(
                'total'    => count( $all ),
                'parents'  => $parents,
                'children' => $children,
            );
        }

        return $stats;
    }
}

// Initialise after BuddyPress.
add_action( 'bp_init', array( 'BPGH_Group_Types', 'init' ) );

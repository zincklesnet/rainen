<?php
/**
 * BP Group Hierarchy — Filters.
 *
 * Hooks into BuddyPress template and display filters to inject
 * hierarchy information (parent labels, breadcrumbs, child lists).
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 * @updated 2.0.0 — Category/tag labels in directory, URL-based filtering.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------
 * 1. Append "Sub-group of …" label to group headers
 * ----------------------------------------------------------- */

function bpgh_show_parent_in_header( $description ) {

    if ( ! bp_is_group() ) {
        return $description;
    }

    if ( 'yes' !== get_option( 'bpgh_show_parent_in_header', 'yes' ) ) {
        return $description;
    }

    $group_id  = bp_get_current_group_id();
    $parent_id = BP_Group_Hierarchy::get_parent_id( $group_id );

    if ( ! $parent_id ) {
        return $description;
    }

    $parent = groups_get_group( $parent_id );

    if ( empty( $parent->id ) ) {
        return $description;
    }

    if ( function_exists( 'bp_get_group_url' ) ) {
        $parent_url = bp_get_group_url( $parent );
    } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
        $parent_url = bp_get_group_permalink( $parent );
    } else {
        $parent_url = '#';
    }

    $label = sprintf(
        '<p class="bpgh-parent-label">' . esc_html__( 'Sub-group of %s', 'bp-group-hierarchy' ) . '</p>',
        '<a href="' . esc_url( $parent_url ) . '">' . esc_html( $parent->name ) . '</a>'
    );

    return $label . $description;
}
add_filter( 'bp_get_group_description', 'bpgh_show_parent_in_header' );


/* -----------------------------------------------------------
 * 2. Show child groups list on single group pages
 * ----------------------------------------------------------- */

function bpgh_show_children_list() {

    if ( ! bp_is_group() ) {
        return;
    }

    if ( 'yes' !== get_option( 'bpgh_show_children_list', 'yes' ) ) {
        return;
    }

    $group_id = bp_get_current_group_id();
    $children = BP_Group_Hierarchy::get_children( $group_id );

    if ( empty( $children ) ) {
        return;
    }

    echo '<div class="bpgh-children-list">';
    echo '<h4>' . esc_html__( 'Sub-groups', 'bp-group-hierarchy' ) . '</h4>';
    echo '<ul>';

    foreach ( $children as $child ) {
        if ( function_exists( 'bp_get_group_url' ) ) {
            $child_url = bp_get_group_url( $child );
        } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
            $child_url = bp_get_group_permalink( $child );
        } else {
            $child_url = '#';
        }

        echo '<li>';
        echo '<a href="' . esc_url( $child_url ) . '">' . esc_html( $child->name ) . '</a>';

        // v2.0: Show category label.
        if ( class_exists( 'BPGH_Categories' ) ) {
            $cat = BPGH_Categories::get_group_category( $child->id );
            if ( $cat ) {
                $all_cats = BPGH_Categories::get_categories();
                $cat_label = isset( $all_cats[ $cat ] ) ? $all_cats[ $cat ] : $cat;
                echo ' <span class="bpgh-cat-badge">' . esc_html( $cat_label ) . '</span>';
            }
        }

        // v2.0: Show tags.
        if ( class_exists( 'BPGH_Tags' ) ) {
            $tags = BPGH_Tags::get_tags( $child->id );
            if ( ! empty( $tags ) ) {
                foreach ( $tags as $tag ) {
                    echo ' <span class="bpgh-tag">' . esc_html( $tag ) . '</span>';
                }
            }
        }

        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
}
add_action( 'bp_after_group_header', 'bpgh_show_children_list' );


/* -----------------------------------------------------------
 * 3. URL-based Category/Tag Filtering (v2.0)
 *
 * Intercepts ?bpgh_category=slug and ?bpgh_tag=slug on the
 * groups directory and injects meta-query args.
 * ----------------------------------------------------------- */

function bpgh_inject_directory_meta_query( $args ) {

    if ( ! bp_is_groups_directory() ) {
        return $args;
    }

    if ( ! isset( $args['meta_query'] ) ) {
        $args['meta_query'] = array();
    }

    // Category filter.
    if ( ! empty( $_GET['bpgh_category'] ) ) {
        $category = sanitize_text_field( wp_unslash( $_GET['bpgh_category'] ) );
        $args['meta_query'][] = array(
            'key'   => 'bpgh_category',
            'value' => $category,
        );
    }

    // Tag filter.
    if ( ! empty( $_GET['bpgh_tag'] ) ) {
        $tag = sanitize_text_field( wp_unslash( $_GET['bpgh_tag'] ) );
        $args['meta_query'][] = array(
            'key'     => 'bpgh_tags',
            'value'   => $tag,
            'compare' => 'LIKE',
        );
    }

    // Parent filter — show only children of a specific parent.
    if ( isset( $_GET['bpgh_parent'] ) ) {
        $parent = absint( $_GET['bpgh_parent'] );
        $args['meta_query'][] = array(
            'key'   => 'bpgh_parent_id',
            'value' => $parent,
        );
    }

    return $args;
}
add_filter( 'bp_after_has_groups_parse_args', 'bpgh_inject_directory_meta_query' );


/* -----------------------------------------------------------
 * 4. Breadcrumb on Single Group Pages (v2.0)
 * ----------------------------------------------------------- */

function bpgh_render_group_breadcrumb() {

    if ( ! bp_is_group() ) {
        return;
    }

    $group_id  = bp_get_current_group_id();
    $ancestors = array();

    if ( class_exists( 'BP_Group_Hierarchy' ) && method_exists( 'BP_Group_Hierarchy', 'get_ancestors' ) ) {
        $ancestors = BP_Group_Hierarchy::get_ancestors( $group_id );
    }

    if ( empty( $ancestors ) ) {
        return;
    }

    echo '<nav class="bpgh-breadcrumbs" aria-label="' . esc_attr__( 'Group hierarchy', 'bp-group-hierarchy' ) . '">';
    echo '<ol>';

    foreach ( $ancestors as $ancestor ) {
        if ( function_exists( 'bp_get_group_url' ) ) {
            $url = bp_get_group_url( $ancestor );
        } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
            $url = bp_get_group_permalink( $ancestor );
        } else {
            $url = '#';
        }
        echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $ancestor->name ) . '</a> &rsaquo; </li>';
    }

    $current = groups_get_group( $group_id );
    if ( ! empty( $current->id ) ) {
        echo '<li class="bpgh-current">' . esc_html( $current->name ) . '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}
add_action( 'bp_before_group_header', 'bpgh_render_group_breadcrumb' );

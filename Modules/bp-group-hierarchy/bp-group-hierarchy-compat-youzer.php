<?php
/**
 * BP Group Hierarchy — Youzer compatibility layer.
 *
 * When the Youzer theme-plugin is active, BuddyPress templates are
 * overridden. This file hooks into Youzer to inject hierarchy
 * breadcrumbs, category/tag data, and preserves Youzer widgets.
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 * @updated 2.0.0 — Adds hierarchy data to Youzer group cards,
 *                   preserves Youzer widgets, profile page support.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------
 * 1. Only load when Youzer is active
 * ----------------------------------------------------------- */

add_action( 'bp_init', function () {

    if ( ! function_exists( 'yz_init' ) ) {
        return;
    }

    // Inject breadcrumbs into Youzer group headers.
    add_action( 'bp_before_group_header', 'bpgh_youzer_breadcrumbs' );

    // Prevent BP from loading its own template when Youzer handles it.
    add_filter( 'bp_locate_template', 'bpgh_youzer_disable_bp_templates', 10, 3 );

    // v2.0: Add hierarchy data to Youzer group cards.
    add_action( 'yz_after_group_card_data', 'bpgh_youzer_group_card_data' );
    add_action( 'yz_after_group_card', 'bpgh_youzer_group_card_data' );

    // v2.0: Add hierarchy info to Youzer profile groups tab.
    add_action( 'bp_group_header_meta', 'bpgh_youzer_header_meta' );
} );

/* -----------------------------------------------------------
 * 2. Breadcrumb rendering for Youzer group pages
 * ----------------------------------------------------------- */

function bpgh_youzer_breadcrumbs() {

    if ( ! bp_is_group() ) {
        return;
    }

    $group_id  = bp_get_current_group_id();

    if ( ! method_exists( 'BP_Group_Hierarchy', 'get_ancestors' ) ) {
        return;
    }

    $ancestors = BP_Group_Hierarchy::get_ancestors( $group_id );

    if ( empty( $ancestors ) ) {
        return;
    }

    echo '<nav class="bpgh-breadcrumbs bpgh-youzer-breadcrumbs" aria-label="' . esc_attr__( 'Group hierarchy', 'bp-group-hierarchy' ) . '">';
    echo '<ol>';

    foreach ( $ancestors as $ancestor ) {
        if ( function_exists( 'bp_get_group_url' ) ) {
            $url = bp_get_group_url( $ancestor );
        } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
            $url = bp_get_group_permalink( $ancestor );
        } else {
            $url = '#';
        }

        echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $ancestor->name ) . '</a></li>';
    }

    // Current group (no link).
    $current = groups_get_group( $group_id );
    if ( ! empty( $current->id ) ) {
        echo '<li class="bpgh-current">' . esc_html( $current->name ) . '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

/* -----------------------------------------------------------
 * 3. Disable conflicting BP template when Youzer handles it
 * ----------------------------------------------------------- */

function bpgh_youzer_disable_bp_templates( $template, $templates = array(), $load = false ) {

    if ( ! bp_is_group() ) {
        return $template;
    }

    // Only intercept group-specific templates.
    $group_templates = array(
        'groups/single/home.php',
        'groups/single/group-header.php',
    );

    if ( ! empty( $templates ) && is_array( $templates ) ) {
        foreach ( $templates as $tpl ) {
            if ( in_array( $tpl, $group_templates, true ) ) {
                // Return empty string to let Youzer handle it.
                return '';
            }
        }
    }

    return $template;
}

/* -----------------------------------------------------------
 * 4. Add hierarchy data to Youzer group cards (v2.0)
 * ----------------------------------------------------------- */

function bpgh_youzer_group_card_data() {

    if ( ! function_exists( 'bp_get_group_id' ) ) {
        return;
    }

    $group_id = bp_get_group_id();
    if ( ! $group_id ) {
        return;
    }

    $parent_id = BP_Group_Hierarchy::get_parent_id( $group_id );
    $children  = BP_Group_Hierarchy::get_children( $group_id );

    if ( ! $parent_id && empty( $children ) ) {
        return;
    }

    echo '<div class="bpgh-youzer-card-data">';

    // Parent link.
    if ( $parent_id ) {
        $parent = groups_get_group( $parent_id );
        if ( ! empty( $parent->id ) ) {
            if ( function_exists( 'bp_get_group_url' ) ) {
                $url = bp_get_group_url( $parent );
            } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
                $url = bp_get_group_permalink( $parent );
            } else {
                $url = '#';
            }
            echo '<span class="bpgh-card-parent">';
            echo esc_html__( 'Parent:', 'bp-group-hierarchy' ) . ' ';
            echo '<a href="' . esc_url( $url ) . '">' . esc_html( $parent->name ) . '</a>';
            echo '</span>';
        }
    }

    // Children count.
    if ( ! empty( $children ) ) {
        echo '<span class="bpgh-card-children">';
        printf(
            esc_html( _n( '%d sub-group', '%d sub-groups', count( $children ), 'bp-group-hierarchy' ) ),
            count( $children )
        );
        echo '</span>';
    }

    // Category badge.
    if ( class_exists( 'BPGH_Categories' ) ) {
        $cat = BPGH_Categories::get_group_category( $group_id );
        if ( $cat ) {
            $all = BPGH_Categories::get_categories();
            $label = isset( $all[ $cat ] ) ? $all[ $cat ] : $cat;
            echo ' <span class="bpgh-cat-badge">' . esc_html( $label ) . '</span>';
        }
    }

    // Tags.
    if ( class_exists( 'BPGH_Tags' ) ) {
        $tags = BPGH_Tags::get_tags( $group_id );
        if ( ! empty( $tags ) ) {
            echo '<span class="bpgh-card-tags">';
            foreach ( $tags as $tag ) {
                echo ' <span class="bpgh-tag">' . esc_html( $tag ) . '</span>';
            }
            echo '</span>';
        }
    }

    echo '</div>';
}

/* -----------------------------------------------------------
 * 5. Youzer Profile Header Meta (v2.0)
 *
 * Adds parent/child info to the group meta area in Youzer.
 * ----------------------------------------------------------- */

function bpgh_youzer_header_meta() {

    if ( ! bp_is_group() ) {
        return;
    }

    $group_id = bp_get_current_group_id();
    $children = BP_Group_Hierarchy::get_children( $group_id );

    if ( ! empty( $children ) ) {
        echo '<span class="bpgh-meta-children">';
        printf(
            esc_html( _n( '%d sub-group', '%d sub-groups', count( $children ), 'bp-group-hierarchy' ) ),
            count( $children )
        );
        echo '</span>';
    }
}

<?php
/**
 * BP Group Hierarchy — Shortcodes.
 *
 * Provides shortcodes for embedding hierarchy views, category lists,
 * tag clouds, and network group trees in pages and posts.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Shortcodes {

    /**
     * Register all shortcodes.
     */
    public static function init() {
        add_shortcode( 'bpgh_parent_groups',    array( __CLASS__, 'parent_groups' ) );
        add_shortcode( 'bpgh_child_groups',     array( __CLASS__, 'child_groups' ) );
        add_shortcode( 'bpgh_group_categories', array( __CLASS__, 'group_categories' ) );
        add_shortcode( 'bpgh_group_tags',       array( __CLASS__, 'group_tags' ) );
        add_shortcode( 'bpgh_network_groups',   array( __CLASS__, 'network_groups' ) );
        add_shortcode( 'bpgh_group_directory',  array( __CLASS__, 'enhanced_directory' ) );
    }

    /* =============================================================
     * [bpgh_parent_groups]
     *
     * Attributes:
     *   per_page  — groups per page (default 20)
     *   sort      — alphabetical|newest|active|popular (default active)
     *   type      — filter by BP group type slug
     *   columns   — grid columns 1-4 (default 3)
     *   avatars   — show avatars yes|no (default yes)
     *   tooltips  — show tooltips yes|no (default yes)
     *   layout    — grid|list (default grid)
     * ============================================================= */

    public static function parent_groups( $atts ) {
        $atts = shortcode_atts( array(
            'per_page' => 20,
            'sort'     => 'active',
            'type'     => '',
            'columns'  => 3,
            'avatars'  => 'yes',
            'tooltips' => 'yes',
            'layout'   => 'grid',
        ), $atts, 'bpgh_parent_groups' );

        if ( ! function_exists( 'groups_get_groups' ) ) {
            return '<p>' . esc_html__( 'BuddyPress is required.', 'bp-group-hierarchy' ) . '</p>';
        }

        $page = isset( $_GET['bpgh_page'] ) ? max( 1, absint( $_GET['bpgh_page'] ) ) : 1;

        $args = array(
            'show_hidden' => false,
            'per_page'    => absint( $atts['per_page'] ),
            'page'        => $page,
            'type'        => sanitize_text_field( $atts['sort'] ),
        );

        if ( ! empty( $atts['type'] ) ) {
            $args['group_type'] = sanitize_text_field( $atts['type'] );
        }

        $result = groups_get_groups( $args );
        $groups = ! empty( $result['groups'] ) ? $result['groups'] : array();

        // Filter to parent groups only (no parent ID set).
        $parents = array_filter( $groups, function ( $g ) {
            return 0 === BP_Group_Hierarchy::get_parent_id( $g->id );
        } );

        ob_start();

        $layout_class = 'list' === $atts['layout'] ? 'bpgh-sc-list' : 'bpgh-sc-grid';
        $columns      = max( 1, min( 4, absint( $atts['columns'] ) ) );

        echo '<div class="bpgh-shortcode bpgh-parent-groups ' . esc_attr( $layout_class ) . '" style="--bpgh-cols:' . esc_attr( $columns ) . ';">';

        if ( empty( $parents ) ) {
            echo '<p>' . esc_html__( 'No parent groups found.', 'bp-group-hierarchy' ) . '</p>';
        } else {
            foreach ( $parents as $group ) {
                self::render_group_item( $group, $atts );

                // Show children indented under parent.
                $children = BP_Group_Hierarchy::get_children( $group->id );
                if ( ! empty( $children ) ) {
                    echo '<div class="bpgh-sc-children">';
                    foreach ( $children as $child ) {
                        self::render_group_item( $child, $atts, true );
                    }
                    echo '</div>';
                }
            }
        }

        // Pagination.
        $total = ! empty( $result['total'] ) ? (int) $result['total'] : 0;
        self::render_pagination( $total, absint( $atts['per_page'] ), $page );

        echo '</div>';

        return ob_get_clean();
    }

    /* =============================================================
     * [bpgh_child_groups parent="123"]
     *
     * Attributes:
     *   parent    — parent group ID (required)
     *   per_page  — groups per page (default 20)
     *   sort      — alphabetical|newest|active|popular
     *   avatars   — yes|no
     *   tooltips  — yes|no
     * ============================================================= */

    public static function child_groups( $atts ) {
        $atts = shortcode_atts( array(
            'parent'   => 0,
            'per_page' => 20,
            'sort'     => 'active',
            'avatars'  => 'yes',
            'tooltips' => 'yes',
        ), $atts, 'bpgh_child_groups' );

        $parent_id = absint( $atts['parent'] );

        if ( 0 === $parent_id ) {
            return '<p>' . esc_html__( 'Please specify a parent group ID.', 'bp-group-hierarchy' ) . '</p>';
        }

        $children = BP_Group_Hierarchy::get_children( $parent_id );

        ob_start();

        echo '<div class="bpgh-shortcode bpgh-child-groups">';

        if ( empty( $children ) ) {
            echo '<p>' . esc_html__( 'No sub-groups found.', 'bp-group-hierarchy' ) . '</p>';
        } else {
            echo '<ul>';
            foreach ( $children as $child ) {
                echo '<li>';
                self::render_group_item( $child, $atts );

                // Recursive sub-children.
                $sub_children = BP_Group_Hierarchy::get_children( $child->id );
                if ( ! empty( $sub_children ) ) {
                    echo '<ul class="bpgh-sc-sub">';
                    foreach ( $sub_children as $sub ) {
                        echo '<li>';
                        self::render_group_item( $sub, $atts );
                        echo '</li>';
                    }
                    echo '</ul>';
                }

                echo '</li>';
            }
            echo '</ul>';
        }

        echo '</div>';

        return ob_get_clean();
    }

    /* =============================================================
     * [bpgh_group_categories]
     *
     * Displays category list with group counts and filter links.
     * ============================================================= */

    public static function group_categories( $atts ) {
        $atts = shortcode_atts( array(
            'show_counts' => 'yes',
            'layout'      => 'list',
        ), $atts, 'bpgh_group_categories' );

        if ( ! class_exists( 'BPGH_Categories' ) || ! BPGH_Categories::is_enabled() ) {
            return '';
        }

        $categories = BPGH_Categories::get_categories();
        $counts     = BPGH_Categories::get_category_counts();

        ob_start();

        echo '<div class="bpgh-shortcode bpgh-categories">';

        if ( empty( $categories ) ) {
            echo '<p>' . esc_html__( 'No categories defined.', 'bp-group-hierarchy' ) . '</p>';
        } else {
            echo '<ul class="bpgh-category-list">';
            foreach ( $categories as $slug => $label ) {
                $count = isset( $counts[ $slug ] ) ? $counts[ $slug ] : 0;
                $url   = add_query_arg( 'bpgh_category', $slug, bpgh_get_directory_url() );

                echo '<li>';
                echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
                if ( 'yes' === $atts['show_counts'] ) {
                    echo ' <span class="bpgh-count">(' . esc_html( $count ) . ')</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }

        echo '</div>';

        return ob_get_clean();
    }

    /* =============================================================
     * [bpgh_group_tags]
     *
     * Displays a tag cloud or tag list.
     * Attributes:
     *   limit    — max tags (default 30)
     *   layout   — cloud|list (default cloud)
     * ============================================================= */

    public static function group_tags( $atts ) {
        $atts = shortcode_atts( array(
            'limit'  => 30,
            'layout' => 'cloud',
        ), $atts, 'bpgh_group_tags' );

        if ( ! class_exists( 'BPGH_Tags' ) || ! BPGH_Tags::is_enabled() ) {
            return '';
        }

        $tag_data = BPGH_Tags::get_tag_cloud_data( absint( $atts['limit'] ) );

        ob_start();

        echo '<div class="bpgh-shortcode bpgh-tag-cloud">';

        if ( empty( $tag_data ) ) {
            echo '<p>' . esc_html__( 'No tags found.', 'bp-group-hierarchy' ) . '</p>';
        } else {
            $max_count = max( $tag_data );
            $min_count = min( $tag_data );

            foreach ( $tag_data as $tag => $count ) {
                // Calculate font size for cloud (1em - 2.5em range).
                $size = $max_count > $min_count
                    ? 1 + ( ( $count - $min_count ) / ( $max_count - $min_count ) ) * 1.5
                    : 1.5;

                $url = add_query_arg( 'bpgh_tag', urlencode( $tag ), bpgh_get_directory_url() );

                echo '<a href="' . esc_url( $url ) . '" class="bpgh-cloud-tag" style="font-size:' . esc_attr( round( $size, 2 ) ) . 'em;" title="' . esc_attr( $count . ' groups' ) . '">';
                echo esc_html( $tag );
                echo '</a> ';
            }
        }

        echo '</div>';

        return ob_get_clean();
    }

    /* =============================================================
     * [bpgh_network_groups]
     *
     * Shows groups from across the multisite network.
     * Attributes:
     *   site_id   — specific site ID (0 = all sites)
     *   per_page  — groups per page
     *   max_depth — tree depth
     * ============================================================= */

    public static function network_groups( $atts ) {
        $atts = shortcode_atts( array(
            'site_id'   => 0,
            'per_page'  => 20,
            'max_depth' => 3,
        ), $atts, 'bpgh_network_groups' );

        if ( ! is_multisite() ) {
            return '<p>' . esc_html__( 'This shortcode requires a multisite network.', 'bp-group-hierarchy' ) . '</p>';
        }

        ob_start();

        echo '<div class="bpgh-shortcode bpgh-network-groups">';

        $site_id = absint( $atts['site_id'] );

        if ( $site_id > 0 ) {
            // Single site.
            self::render_site_groups( $site_id, $atts );
        } else {
            // All sites.
            $sites = get_sites( array( 'number' => 20 ) );
            foreach ( $sites as $site ) {
                echo '<h3>' . esc_html( $site->blogname ) . '</h3>';
                self::render_site_groups( $site->blog_id, $atts );
            }
        }

        echo '</div>';

        return ob_get_clean();
    }

    /* =============================================================
     * [bpgh_group_directory]
     *
     * Enhanced group directory with hierarchy, categories, tags,
     * and AJAX filtering built in.
     * ============================================================= */

    public static function enhanced_directory( $atts ) {
        $atts = shortcode_atts( array(
            'per_page'   => 20,
            'show_filters' => 'yes',
            'show_search'  => 'yes',
        ), $atts, 'bpgh_group_directory' );

        ob_start();

        echo '<div class="bpgh-shortcode bpgh-enhanced-directory" id="bpgh-directory">';

        // Filter bar.
        if ( 'yes' === $atts['show_filters'] ) {
            echo '<div class="bpgh-directory-filters">';

            // Category dropdown.
            if ( class_exists( 'BPGH_Categories' ) && BPGH_Categories::is_enabled() ) {
                $categories = BPGH_Categories::get_categories();
                if ( ! empty( $categories ) ) {
                    echo '<select class="bpgh-filter-category" data-filter="category">';
                    echo '<option value="">' . esc_html__( 'All Categories', 'bp-group-hierarchy' ) . '</option>';
                    foreach ( $categories as $slug => $label ) {
                        echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $label ) . '</option>';
                    }
                    echo '</select>';
                }
            }

            // Sort dropdown.
            echo '<select class="bpgh-filter-sort" data-filter="sort">';
            echo '<option value="active">' . esc_html__( 'Most Active', 'bp-group-hierarchy' ) . '</option>';
            echo '<option value="newest">' . esc_html__( 'Newest', 'bp-group-hierarchy' ) . '</option>';
            echo '<option value="alphabetical">' . esc_html__( 'A-Z', 'bp-group-hierarchy' ) . '</option>';
            echo '<option value="popular">' . esc_html__( 'Most Members', 'bp-group-hierarchy' ) . '</option>';
            echo '</select>';

            // Group type tabs.
            if ( class_exists( 'BPGH_Group_Types' ) ) {
                $types = BPGH_Group_Types::get_group_types();
                if ( ! empty( $types ) ) {
                    echo '<div class="bpgh-type-tabs">';
                    echo '<a href="#" class="bpgh-type-tab bpgh-active" data-type="">' . esc_html__( 'All', 'bp-group-hierarchy' ) . '</a>';
                    foreach ( $types as $slug => $type_obj ) {
                        $label = isset( $type_obj->labels['name'] ) ? $type_obj->labels['name'] : $slug;
                        echo '<a href="#" class="bpgh-type-tab" data-type="' . esc_attr( $slug ) . '">' . esc_html( $label ) . '</a>';
                    }
                    echo '</div>';
                }
            }

            echo '</div>'; // .bpgh-directory-filters
        }

        // Tag search.
        if ( 'yes' === $atts['show_search'] && class_exists( 'BPGH_Tags' ) && BPGH_Tags::is_enabled() ) {
            echo '<div class="bpgh-directory-search">';
            echo '<input type="text" class="bpgh-tag-search" placeholder="' . esc_attr__( 'Search by tag...', 'bp-group-hierarchy' ) . '" />';
            echo '</div>';
        }

        // Results container (populated via AJAX or initial load).
        echo '<div class="bpgh-directory-results" data-per-page="' . esc_attr( $atts['per_page'] ) . '">';
        echo '<p class="bpgh-loading">' . esc_html__( 'Loading groups...', 'bp-group-hierarchy' ) . '</p>';
        echo '</div>';

        echo '</div>'; // .bpgh-enhanced-directory

        return ob_get_clean();
    }

    /* =============================================================
     * Helpers
     * ============================================================= */

    /**
     * Render a single group item for shortcode output.
     */
    private static function render_group_item( $group, $atts, $is_child = false ) {
        $url = '#';
        if ( function_exists( 'bp_get_group_url' ) ) {
            $url = bp_get_group_url( $group );
        } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
            $url = bp_get_group_permalink( $group );
        }

        $tooltip_attr = ( 'yes' === ( $atts['tooltips'] ?? 'yes' ) )
            ? ' data-bpgh-tooltip="' . esc_attr( $group->id ) . '"'
            : '';

        $child_class = $is_child ? ' bpgh-sc-child' : '';

        echo '<div class="bpgh-sc-item' . esc_attr( $child_class ) . '"' . $tooltip_attr . '>';

        // Avatar.
        if ( 'yes' === ( $atts['avatars'] ?? 'yes' ) && function_exists( 'bp_core_fetch_avatar' ) ) {
            echo '<div class="bpgh-sc-avatar">';
            echo bp_core_fetch_avatar( array(
                'item_id' => $group->id,
                'object'  => 'group',
                'type'    => 'thumb',
                'width'   => 50,
                'height'  => 50,
            ) );
            echo '</div>';
        }

        echo '<div class="bpgh-sc-info">';
        echo '<a href="' . esc_url( $url ) . '" class="bpgh-sc-name">' . esc_html( $group->name ) . '</a>';

        // Premium badge.
        if ( class_exists( 'BPGH_Premium' ) && BPGH_Premium::is_premium( $group->id ) ) {
            echo ' <span class="bpgh-badge bpgh-badge--premium">&#9733;</span>';
        }

        // Member count.
        if ( isset( $group->total_member_count ) ) {
            echo ' <span class="bpgh-sc-members">' . esc_html(
                sprintf( _n( '%d member', '%d members', $group->total_member_count, 'bp-group-hierarchy' ), $group->total_member_count )
            ) . '</span>';
        }

        echo '</div>';
        echo '</div>';
    }

    /**
     * Render pagination links.
     */
    private static function render_pagination( $total, $per_page, $current_page ) {
        $total_pages = ceil( $total / $per_page );

        if ( $total_pages <= 1 ) {
            return;
        }

        echo '<nav class="bpgh-sc-pagination">';
        for ( $i = 1; $i <= min( $total_pages, 10 ); $i++ ) {
            $class = ( $i === $current_page ) ? ' class="bpgh-active"' : '';
            $url   = add_query_arg( 'bpgh_page', $i );
            echo '<a href="' . esc_url( $url ) . '"' . $class . '>' . esc_html( $i ) . '</a> ';
        }
        echo '</nav>';
    }

    /**
     * Render groups from a specific site (for network shortcode).
     */
    private static function render_site_groups( $site_id, $atts ) {
        switch_to_blog( $site_id );

        if ( class_exists( 'BP_Group_Hierarchy' ) ) {
            $tree = BP_Group_Hierarchy::get_tree( 0, absint( $atts['max_depth'] ) );

            if ( ! empty( $tree ) ) {
                echo '<ul class="bpgh-network-tree">';
                self::render_tree_nodes( $tree );
                echo '</ul>';
            } else {
                echo '<p>' . esc_html__( 'No groups found on this site.', 'bp-group-hierarchy' ) . '</p>';
            }
        }

        restore_current_blog();
    }

    /**
     * Recursively render tree nodes.
     */
    private static function render_tree_nodes( $nodes ) {
        foreach ( $nodes as $node ) {
            $group = $node['group'];
            if ( empty( $group->id ) ) {
                continue;
            }

            $url = '#';
            if ( function_exists( 'bp_get_group_url' ) ) {
                $url = bp_get_group_url( $group );
            } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
                $url = bp_get_group_permalink( $group );
            }

            echo '<li>';
            echo '<a href="' . esc_url( $url ) . '">' . esc_html( $group->name ) . '</a>';

            if ( ! empty( $node['children'] ) ) {
                echo '<ul>';
                self::render_tree_nodes( $node['children'] );
                echo '</ul>';
            }

            echo '</li>';
        }
    }
}

// Register shortcodes.
add_action( 'init', array( 'BPGH_Shortcodes', 'init' ) );

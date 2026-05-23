<?php
/**
 * BP Group Hierarchy — AJAX Handlers.
 *
 * Provides AJAX endpoints for category/tag filtering, group sorting,
 * premium upgrade, and pending group approval.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Ajax {

    /**
     * Register all AJAX actions.
     */
    public static function init() {
        $actions = array(
            'bpgh_filter_groups',
            'bpgh_search_tags',
            'bpgh_upgrade_group',
            'bpgh_approve_group',
            'bpgh_reject_group',
        );

        foreach ( $actions as $action ) {
            add_action( 'wp_ajax_' . $action, array( __CLASS__, $action ) );
        }

        // Public (no-priv) actions.
        add_action( 'wp_ajax_nopriv_bpgh_filter_groups', array( __CLASS__, 'bpgh_filter_groups' ) );
        add_action( 'wp_ajax_nopriv_bpgh_search_tags', array( __CLASS__, 'bpgh_search_tags' ) );

        // Enqueue filter script.
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_filter_assets' ) );
    }

    /* =============================================================
     * AJAX: Filter Groups (Category + Tag + Type + Sort)
     * ============================================================= */

    /**
     * Filter groups by category, tag, group type, parent, and sort order.
     * Returns rendered HTML for the groups list.
     */
    public static function bpgh_filter_groups() {
        check_ajax_referer( 'bpgh_ajax', 'nonce' );

        $category  = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( $_POST['category'] ) ) : '';
        $tag       = isset( $_POST['tag'] ) ? sanitize_text_field( wp_unslash( $_POST['tag'] ) ) : '';
        $type      = isset( $_POST['group_type'] ) ? sanitize_text_field( wp_unslash( $_POST['group_type'] ) ) : '';
        $parent_id = isset( $_POST['parent_id'] ) ? absint( $_POST['parent_id'] ) : 0;
        $sort      = isset( $_POST['sort'] ) ? sanitize_text_field( wp_unslash( $_POST['sort'] ) ) : 'active';
        $page      = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;
        $per_page  = isset( $_POST['per_page'] ) ? min( 50, absint( $_POST['per_page'] ) ) : 20;

        $args = array(
            'show_hidden' => false,
            'per_page'    => $per_page,
            'page'        => $page,
            'type'        => $sort,
            'meta_query'  => array( 'relation' => 'AND' ),
        );

        // Category filter.
        if ( ! empty( $category ) ) {
            $args['meta_query'][] = array(
                'key'     => BPGH_Categories::META_KEY,
                'value'   => $category,
                'compare' => '=',
            );
        }

        // Tag filter.
        if ( ! empty( $tag ) ) {
            $args['meta_query'][] = array(
                'key'     => BPGH_Tags::META_KEY,
                'value'   => $tag,
                'compare' => 'LIKE',
            );
        }

        // Parent filter.
        if ( $parent_id > 0 ) {
            $args['meta_query'][] = array(
                'key'     => 'bpgh_parent_id',
                'value'   => $parent_id,
                'compare' => '=',
                'type'    => 'NUMERIC',
            );
        }

        // Group type filter.
        if ( ! empty( $type ) && function_exists( 'bp_groups_get_group_types' ) ) {
            $args['group_type'] = $type;
        }

        // Clean up empty meta_query.
        if ( count( $args['meta_query'] ) <= 1 ) {
            unset( $args['meta_query'] );
        }

        $result = groups_get_groups( $args );
        $groups = ! empty( $result['groups'] ) ? $result['groups'] : array();
        $total  = ! empty( $result['total'] ) ? (int) $result['total'] : 0;

        // Build HTML output.
        ob_start();

        if ( empty( $groups ) ) {
            echo '<p class="bpgh-no-results">' . esc_html__( 'No groups found matching your filters.', 'bp-group-hierarchy' ) . '</p>';
        } else {
            echo '<ul class="bpgh-filtered-groups">';
            foreach ( $groups as $group ) {
                self::render_group_card( $group );
            }
            echo '</ul>';

            // Pagination.
            $total_pages = ceil( $total / $per_page );
            if ( $total_pages > 1 ) {
                echo '<div class="bpgh-pagination">';
                for ( $i = 1; $i <= $total_pages; $i++ ) {
                    $active = ( $i === $page ) ? ' class="bpgh-active"' : '';
                    echo '<a href="#" data-page="' . esc_attr( $i ) . '"' . $active . '>' . esc_html( $i ) . '</a> ';
                }
                echo '</div>';
            }
        }

        $html = ob_get_clean();

        wp_send_json_success( array(
            'html'  => $html,
            'total' => $total,
            'pages' => ceil( $total / $per_page ),
        ) );
    }

    /**
     * Render a single group card (used in AJAX results).
     *
     * @param object $group Group object.
     */
    private static function render_group_card( $group ) {
        $url = '#';
        if ( function_exists( 'bp_get_group_url' ) ) {
            $url = bp_get_group_url( $group );
        } elseif ( function_exists( 'bp_get_group_permalink' ) ) {
            $url = bp_get_group_permalink( $group );
        }

        $avatar = '';
        if ( function_exists( 'bp_core_fetch_avatar' ) ) {
            $avatar = bp_core_fetch_avatar( array(
                'item_id' => $group->id,
                'object'  => 'group',
                'type'    => 'thumb',
            ) );
        }

        $parent_id = BP_Group_Hierarchy::get_parent_id( $group->id );
        $category  = class_exists( 'BPGH_Categories' ) ? BPGH_Categories::get_group_category_label( $group->id ) : '';
        $tags      = class_exists( 'BPGH_Tags' ) ? BPGH_Tags::get_tags( $group->id ) : array();
        $is_premium = class_exists( 'BPGH_Premium' ) && BPGH_Premium::is_premium( $group->id );

        echo '<li class="bpgh-group-card" data-bpgh-tooltip="' . esc_attr( $group->id ) . '">';

        // Avatar.
        if ( $avatar ) {
            echo '<div class="bpgh-card__avatar">';
            echo '<a href="' . esc_url( $url ) . '">' . $avatar . '</a>';
            echo '</div>';
        }

        // Info.
        echo '<div class="bpgh-card__info">';
        echo '<h4 class="bpgh-card__name"><a href="' . esc_url( $url ) . '">' . esc_html( $group->name ) . '</a>';
        if ( $is_premium ) {
            echo ' <span class="bpgh-badge bpgh-badge--premium" title="' . esc_attr__( 'Premium Group', 'bp-group-hierarchy' ) . '">&#9733;</span>';
        }
        echo '</h4>';

        // Category label.
        if ( $category ) {
            echo '<span class="bpgh-card__category">' . esc_html( $category ) . '</span>';
        }

        // Description excerpt.
        if ( ! empty( $group->description ) ) {
            echo '<p class="bpgh-card__desc">' . esc_html( wp_trim_words( $group->description, 15 ) ) . '</p>';
        }

        // Tags.
        if ( ! empty( $tags ) ) {
            echo '<div class="bpgh-card__tags">';
            foreach ( $tags as $tag ) {
                echo '<span class="bpgh-tag">' . esc_html( $tag ) . '</span> ';
            }
            echo '</div>';
        }

        // Meta: member count.
        echo '<div class="bpgh-card__meta">';
        if ( isset( $group->total_member_count ) ) {
            echo '<span>' . esc_html(
                sprintf(
                    _n( '%d member', '%d members', $group->total_member_count, 'bp-group-hierarchy' ),
                    $group->total_member_count
                )
            ) . '</span>';
        }

        // Sub-group indicator.
        if ( $parent_id > 0 ) {
            $parent = groups_get_group( $parent_id );
            if ( ! empty( $parent->id ) ) {
                echo ' &middot; <span class="bpgh-card__parent">' . esc_html(
                    sprintf( __( 'Sub-group of %s', 'bp-group-hierarchy' ), $parent->name )
                ) . '</span>';
            }
        }
        echo '</div>';

        echo '</div>'; // .bpgh-card__info
        echo '</li>';
    }

    /* =============================================================
     * AJAX: Tag Search (autocomplete)
     * ============================================================= */

    public static function bpgh_search_tags() {
        check_ajax_referer( 'bpgh_ajax', 'nonce' );

        $query = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

        if ( strlen( $query ) < 2 ) {
            wp_send_json_success( array() );
        }

        $matches = BPGH_Tags::search_tags( $query, 10 );

        wp_send_json_success( $matches );
    }

    /* =============================================================
     * AJAX: Premium Upgrade
     * ============================================================= */

    public static function bpgh_upgrade_group() {
        check_ajax_referer( 'bpgh_ajax', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in.', 'bp-group-hierarchy' ) );
        }

        $group_id = isset( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : 0;

        if ( 0 === $group_id ) {
            wp_send_json_error( __( 'Invalid group.', 'bp-group-hierarchy' ) );
        }

        // Check user is group admin.
        if ( ! groups_is_user_admin( get_current_user_id(), $group_id ) && ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Only group admins can upgrade.', 'bp-group-hierarchy' ) );
        }

        $result = BPGH_Premium::upgrade_with_zcreds( $group_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        wp_send_json_success( __( 'Group upgraded to premium!', 'bp-group-hierarchy' ) );
    }

    /* =============================================================
     * AJAX: Approve / Reject Pending Groups (Admin)
     * ============================================================= */

    public static function bpgh_approve_group() {
        check_ajax_referer( 'bpgh_ajax', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) && ! is_super_admin() ) {
            wp_send_json_error( __( 'Insufficient permissions.', 'bp-group-hierarchy' ) );
        }

        $group_id = isset( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : 0;

        if ( 0 === $group_id ) {
            wp_send_json_error( __( 'Invalid group.', 'bp-group-hierarchy' ) );
        }

        BPGH_Permissions::approve_group( $group_id );

        wp_send_json_success( __( 'Group approved.', 'bp-group-hierarchy' ) );
    }

    public static function bpgh_reject_group() {
        check_ajax_referer( 'bpgh_ajax', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) && ! is_super_admin() ) {
            wp_send_json_error( __( 'Insufficient permissions.', 'bp-group-hierarchy' ) );
        }

        $group_id = isset( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : 0;

        if ( 0 === $group_id ) {
            wp_send_json_error( __( 'Invalid group.', 'bp-group-hierarchy' ) );
        }

        BPGH_Permissions::reject_group( $group_id );

        wp_send_json_success( __( 'Group rejected and deleted.', 'bp-group-hierarchy' ) );
    }

    /* =============================================================
     * Enqueue Filter Assets
     * ============================================================= */

    public static function enqueue_filter_assets() {
        if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'groups' ) ) {
            return;
        }

        // Load on groups directory, activity, and member pages.
        $should_load = bp_is_groups_directory()
            || ( function_exists( 'bp_is_activity_component' ) && bp_is_activity_component() )
            || ( function_exists( 'bp_is_user' ) && bp_is_user() );

        if ( ! $should_load ) {
            return;
        }

        wp_enqueue_script(
            'bpgh-ajax-filter',
            BP_GROUP_HIERARCHY_PLUGIN_URL . 'assets/js/bpgh-ajax-filter.js',
            array( 'jquery' ),
            BPGH_VERSION,
            true
        );

        wp_localize_script( 'bpgh-ajax-filter', 'bpghFilter', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'bpgh_ajax' ),
        ) );
    }
}

// Initialise.
add_action( 'init', array( 'BPGH_Ajax', 'init' ) );

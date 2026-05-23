<?php
/**
 * BP Group Hierarchy — Avatar-Styled Hover Tooltips.
 *
 * Renders rich tooltip popups on group avatars showing group admins,
 * description, member count, category, tags, and other chosen meta.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Tooltips {

    /**
     * Initialise tooltip hooks.
     */
    public static function init() {
        // Inject tooltip data attributes on group avatar containers.
        add_filter( 'bp_get_group_avatar', array( __CLASS__, 'add_tooltip_data' ), 20, 2 );

        // Add tooltip markup to group directory items.
        add_action( 'bp_directory_groups_item', array( __CLASS__, 'render_tooltip_container' ), 5 );

        // Enqueue tooltip assets.
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

        // AJAX endpoint for tooltip data.
        add_action( 'wp_ajax_bpgh_tooltip_data', array( __CLASS__, 'ajax_tooltip_data' ) );
        add_action( 'wp_ajax_nopriv_bpgh_tooltip_data', array( __CLASS__, 'ajax_tooltip_data' ) );
    }

    /**
     * Build tooltip data for a group.
     *
     * @param int $group_id Group ID.
     * @return array Tooltip data.
     */
    public static function get_tooltip_data( $group_id ) {
        $group_id = absint( $group_id );
        $group    = groups_get_group( $group_id );

        if ( empty( $group->id ) ) {
            return array();
        }

        $data = array(
            'id'          => $group->id,
            'name'        => $group->name,
            'description' => wp_trim_words( $group->description, 20, '...' ),
            'member_count' => 0,
            'admins'      => array(),
            'avatar'      => '',
            'category'    => '',
            'tags'        => array(),
            'is_premium'  => false,
            'parent'      => '',
            'children_count' => 0,
        );

        // Member count.
        if ( isset( $group->total_member_count ) ) {
            $data['member_count'] = (int) $group->total_member_count;
        }

        // Avatar.
        if ( function_exists( 'bp_core_fetch_avatar' ) ) {
            $data['avatar'] = bp_core_fetch_avatar( array(
                'item_id' => $group_id,
                'object'  => 'group',
                'type'    => 'thumb',
                'html'    => false,
            ) );
        }

        // Group admins.
        if ( function_exists( 'groups_get_group_admins' ) ) {
            $admins = groups_get_group_admins( $group_id );
            foreach ( $admins as $admin ) {
                $user = get_userdata( $admin->user_id );
                if ( $user ) {
                    $data['admins'][] = $user->display_name;
                }
            }
        }

        // Category.
        if ( class_exists( 'BPGH_Categories' ) && BPGH_Categories::is_enabled() ) {
            $data['category'] = BPGH_Categories::get_group_category_label( $group_id );
        }

        // Tags.
        if ( class_exists( 'BPGH_Tags' ) && BPGH_Tags::is_enabled() ) {
            $data['tags'] = BPGH_Tags::get_tags( $group_id );
        }

        // Premium status.
        if ( class_exists( 'BPGH_Premium' ) && BPGH_Premium::is_enabled() ) {
            $data['is_premium'] = BPGH_Premium::is_premium( $group_id );
        }

        // Parent group name.
        $parent_id = BP_Group_Hierarchy::get_parent_id( $group_id );
        if ( $parent_id > 0 ) {
            $parent = groups_get_group( $parent_id );
            if ( ! empty( $parent->id ) ) {
                $data['parent'] = $parent->name;
            }
        }

        // Children count.
        $children = BP_Group_Hierarchy::get_children( $group_id );
        $data['children_count'] = count( $children );

        return $data;
    }

    /**
     * Add tooltip data attributes to group avatar HTML.
     *
     * @param string $avatar Avatar HTML.
     * @param array  $r      Avatar arguments.
     * @return string Modified HTML.
     */
    public static function add_tooltip_data( $avatar, $r = array() ) {
        if ( empty( $r['item_id'] ) || 'group' !== ( $r['object'] ?? '' ) ) {
            return $avatar;
        }

        $group_id = absint( $r['item_id'] );

        // Add data attribute for JS to pick up.
        $avatar = str_replace(
            'class="',
            'data-bpgh-tooltip="' . esc_attr( $group_id ) . '" class="bpgh-has-tooltip ',
            $avatar
        );

        return $avatar;
    }

    /**
     * Render tooltip container markup in group directory items.
     */
    public static function render_tooltip_container() {
        if ( ! function_exists( 'bp_get_group_id' ) ) {
            return;
        }

        $group_id = bp_get_group_id();

        if ( ! $group_id ) {
            return;
        }

        echo '<div class="bpgh-tooltip-anchor" data-bpgh-tooltip="' . esc_attr( $group_id ) . '">';
        echo '<div class="bpgh-tooltip-popup" style="display:none;"></div>';
        echo '</div>';
    }

    /**
     * AJAX handler for tooltip data.
     */
    public static function ajax_tooltip_data() {
        $group_id = isset( $_GET['group_id'] ) ? absint( $_GET['group_id'] ) : 0;

        if ( 0 === $group_id ) {
            wp_send_json_error( 'Invalid group ID' );
        }

        $data = self::get_tooltip_data( $group_id );

        wp_send_json_success( $data );
    }

    /**
     * Enqueue tooltip CSS and JS.
     */
    public static function enqueue_assets() {
        if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'groups' ) ) {
            return;
        }

        // Load on groups directory, single group, activity, and member pages.
        $should_load = bp_is_groups_directory()
            || bp_is_group()
            || ( function_exists( 'bp_is_activity_component' ) && bp_is_activity_component() )
            || ( function_exists( 'bp_is_user' ) && bp_is_user() );

        if ( ! $should_load ) {
            return;
        }

        wp_enqueue_style(
            'bpgh-tooltips',
            BP_GROUP_HIERARCHY_PLUGIN_URL . 'assets/css/bpgh-tooltips.css',
            array(),
            BPGH_VERSION
        );

        wp_enqueue_script(
            'bpgh-tooltips',
            BP_GROUP_HIERARCHY_PLUGIN_URL . 'assets/js/bpgh-tooltips.js',
            array( 'jquery' ),
            BPGH_VERSION,
            true
        );

        wp_localize_script( 'bpgh-tooltips', 'bpghTooltips', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'bpgh_tooltip' ),
        ) );
    }

    /**
     * Build tooltip HTML from data (for server-side rendering).
     *
     * @param array $data Tooltip data from get_tooltip_data().
     * @return string HTML.
     */
    public static function render_tooltip_html( $data ) {
        if ( empty( $data ) ) {
            return '';
        }

        $html = '<div class="bpgh-tooltip">';

        // Avatar + Name header.
        if ( ! empty( $data['avatar'] ) ) {
            $html .= '<div class="bpgh-tooltip__header">';
            $html .= '<img src="' . esc_url( $data['avatar'] ) . '" class="bpgh-tooltip__avatar" alt="" />';
            $html .= '<div class="bpgh-tooltip__title">';
            $html .= '<strong>' . esc_html( $data['name'] ) . '</strong>';
            if ( ! empty( $data['is_premium'] ) ) {
                $html .= ' <span class="bpgh-badge bpgh-badge--premium">&#9733;</span>';
            }
            $html .= '</div>';
            $html .= '</div>';
        }

        // Description.
        if ( ! empty( $data['description'] ) ) {
            $html .= '<p class="bpgh-tooltip__desc">' . esc_html( $data['description'] ) . '</p>';
        }

        // Meta row.
        $html .= '<div class="bpgh-tooltip__meta">';
        $html .= '<span>' . esc_html(
            sprintf(
                /* translators: %d: member count */
                _n( '%d member', '%d members', $data['member_count'], 'bp-group-hierarchy' ),
                $data['member_count']
            )
        ) . '</span>';

        if ( $data['children_count'] > 0 ) {
            $html .= ' &middot; <span>' . esc_html(
                sprintf(
                    /* translators: %d: sub-group count */
                    _n( '%d sub-group', '%d sub-groups', $data['children_count'], 'bp-group-hierarchy' ),
                    $data['children_count']
                )
            ) . '</span>';
        }
        $html .= '</div>';

        // Parent breadcrumb.
        if ( ! empty( $data['parent'] ) ) {
            $html .= '<p class="bpgh-tooltip__parent">';
            $html .= esc_html__( 'Parent:', 'bp-group-hierarchy' ) . ' ' . esc_html( $data['parent'] );
            $html .= '</p>';
        }

        // Category.
        if ( ! empty( $data['category'] ) ) {
            $html .= '<p class="bpgh-tooltip__cat">';
            $html .= '<span class="bpgh-tooltip__label">' . esc_html__( 'Category:', 'bp-group-hierarchy' ) . '</span> ';
            $html .= esc_html( $data['category'] );
            $html .= '</p>';
        }

        // Tags.
        if ( ! empty( $data['tags'] ) ) {
            $html .= '<p class="bpgh-tooltip__tags">';
            foreach ( $data['tags'] as $tag ) {
                $html .= '<span class="bpgh-tag">' . esc_html( $tag ) . '</span> ';
            }
            $html .= '</p>';
        }

        // Admins.
        if ( ! empty( $data['admins'] ) ) {
            $html .= '<p class="bpgh-tooltip__admins">';
            $html .= '<span class="bpgh-tooltip__label">' . esc_html__( 'Admins:', 'bp-group-hierarchy' ) . '</span> ';
            $html .= esc_html( implode( ', ', $data['admins'] ) );
            $html .= '</p>';
        }

        $html .= '</div>';

        return $html;
    }
}

// Initialise.
add_action( 'bp_init', array( 'BPGH_Tooltips', 'init' ) );

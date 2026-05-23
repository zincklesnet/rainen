<?php
/**
 * BP Group Hierarchy — Multisite Widget.
 *
 * Displays a group hierarchy tree from a specific site in the network.
 * Only loaded on multisite installs.
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 * @updated 2.0.0 — Handles external WP sites (subsites without BP active),
 *                   avatar display, tooltip data, category/tag badges.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Widget: Multisite Group Hierarchy Tree.
 */
class BPGH_Multisite_Widget extends WP_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(
            'bpgh_multisite_widget',
            __( 'BP Group Hierarchy (Multisite)', 'bp-group-hierarchy' ),
            array(
                'description' => __( 'Displays group hierarchy from a specific network site.', 'bp-group-hierarchy' ),
            )
        );
    }

    /**
     * Front-end output.
     *
     * v2.0: Works on subsites without BuddyPress active by switching
     * to the main BuddyPress site to fetch group data, then linking
     * back to the main site's group URLs.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {

        if ( ! is_multisite() ) {
            return;
        }

        // v2.0: Check if network admin allows subsite widgets.
        if ( 'yes' !== get_site_option( 'bpgh_allow_subsite_widgets', 'yes' ) ) {
            return;
        }

        $site_id = ! empty( $instance['site_id'] ) ? absint( $instance['site_id'] ) : $this->get_bp_site_id();

        $site = get_site( $site_id );
        if ( ! $site ) {
            return;
        }

        $show_avatars = ! empty( $instance['show_avatars'] ) && 'yes' === $instance['show_avatars'];
        $max_depth    = ! empty( $instance['max_depth'] ) ? absint( $instance['max_depth'] ) : 5;

        $title = ! empty( $instance['title'] )
            ? apply_filters( 'widget_title', $instance['title'] )
            : sprintf(
                /* translators: %s: site name */
                __( 'Groups on %s', 'bp-group-hierarchy' ),
                esc_html( $site->blogname )
            );

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

        // Switch to target site and build the tree.
        switch_to_blog( $site_id );

        $bp_available = class_exists( 'BP_Group_Hierarchy' );

        if ( $bp_available ) {
            $tree = BP_Group_Hierarchy::get_tree( 0, $max_depth );

            if ( ! empty( $tree ) ) {
                echo '<ul class="bpgh-multisite-tree">';
                $this->render_tree_nodes( $tree, $show_avatars, $site_id );
                echo '</ul>';
            } else {
                echo '<p>' . esc_html__( 'No group hierarchy found.', 'bp-group-hierarchy' ) . '</p>';
            }
        } else {
            // v2.0: BuddyPress not active on this site — try the main site.
            $main_site_id = $this->get_bp_site_id();

            if ( $main_site_id && $main_site_id !== $site_id ) {
                restore_current_blog();
                switch_to_blog( $main_site_id );

                if ( class_exists( 'BP_Group_Hierarchy' ) ) {
                    $tree = BP_Group_Hierarchy::get_tree( 0, $max_depth );

                    if ( ! empty( $tree ) ) {
                        echo '<ul class="bpgh-multisite-tree">';
                        $this->render_tree_nodes( $tree, $show_avatars, $main_site_id );
                        echo '</ul>';
                    } else {
                        echo '<p>' . esc_html__( 'No group hierarchy found.', 'bp-group-hierarchy' ) . '</p>';
                    }
                } else {
                    echo '<p>' . esc_html__( 'BuddyPress is not available on this network.', 'bp-group-hierarchy' ) . '</p>';
                }
            } else {
                echo '<p>' . esc_html__( 'BuddyPress is not active on the selected site.', 'bp-group-hierarchy' ) . '</p>';
            }
        }

        restore_current_blog();

        echo $args['after_widget'];
    }

    /**
     * Recursively render tree nodes as nested lists.
     *
     * @param array $nodes        Tree nodes from BP_Group_Hierarchy::get_tree().
     * @param bool  $show_avatars Whether to display group avatars.
     * @param int   $source_site  Site ID where groups live (for URL construction).
     */
    private function render_tree_nodes( $nodes, $show_avatars = false, $source_site = 0 ) {
        foreach ( $nodes as $node ) {
            $group = $node['group'];

            if ( empty( $group->id ) ) {
                continue;
            }

            $url = self::get_group_url( $group );

            echo '<li>';

            // v2.0: Avatar support.
            if ( $show_avatars && function_exists( 'bp_get_group_avatar' ) ) {
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo bp_get_group_avatar( array(
                    'item_id' => $group->id,
                    'type'    => 'thumb',
                    'width'   => 24,
                    'height'  => 24,
                ) ) . ' ';
            }

            echo '<a href="' . esc_url( $url ) . '"';

            // v2.0: Tooltip data attribute.
            if ( 'yes' === get_option( 'bpgh_enable_tooltips', 'yes' ) ) {
                echo ' data-bpgh-tooltip="' . esc_attr( $group->id ) . '"';
            }

            echo '>' . esc_html( $group->name ) . '</a>';

            // v2.0: Member count.
            if ( isset( $group->total_member_count ) && $group->total_member_count > 0 ) {
                echo ' <span class="bpgh-member-count">(' . absint( $group->total_member_count ) . ')</span>';
            }

            // v2.0: Category badge.
            if ( class_exists( 'BPGH_Categories' ) ) {
                $cat = BPGH_Categories::get_group_category( $group->id );
                if ( $cat ) {
                    $all = BPGH_Categories::get_categories();
                    $label = isset( $all[ $cat ] ) ? $all[ $cat ] : $cat;
                    echo ' <span class="bpgh-cat-badge">' . esc_html( $label ) . '</span>';
                }
            }

            if ( ! empty( $node['children'] ) ) {
                echo '<ul>';
                $this->render_tree_nodes( $node['children'], $show_avatars, $source_site );
                echo '</ul>';
            }

            echo '</li>';
        }
    }

    /**
     * Back-end form.
     *
     * @param array $instance Previously saved values.
     */
    public function form( $instance ) {
        $title        = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $site_id      = ! empty( $instance['site_id'] ) ? absint( $instance['site_id'] ) : '';
        $show_avatars = ! empty( $instance['show_avatars'] ) ? $instance['show_avatars'] : 'no';
        $max_depth    = ! empty( $instance['max_depth'] ) ? absint( $instance['max_depth'] ) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_html_e( 'Title:', 'bp-group-hierarchy' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                   type="text"
                   value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'site_id' ) ); ?>">
                <?php esc_html_e( 'Site ID:', 'bp-group-hierarchy' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'site_id' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'site_id' ) ); ?>"
                   type="number"
                   value="<?php echo esc_attr( $site_id ); ?>"
                   min="1" />
            <small><?php esc_html_e( 'Leave empty to auto-detect the BuddyPress main site.', 'bp-group-hierarchy' ); ?></small>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_avatars' ) ); ?>">
                <?php esc_html_e( 'Show avatars:', 'bp-group-hierarchy' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'show_avatars' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'show_avatars' ) ); ?>">
                <option value="no" <?php selected( $show_avatars, 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                <option value="yes" <?php selected( $show_avatars, 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'max_depth' ) ); ?>">
                <?php esc_html_e( 'Max depth:', 'bp-group-hierarchy' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'max_depth' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'max_depth' ) ); ?>"
                   type="number"
                   value="<?php echo esc_attr( $max_depth ); ?>"
                   min="1" max="20" />
        </p>
        <?php
    }

    /**
     * Save widget settings.
     *
     * @param array $new_instance New values.
     * @param array $old_instance Old values.
     * @return array Sanitized values.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']        = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['site_id']      = absint( $new_instance['site_id'] ?? 0 );
        $instance['show_avatars'] = sanitize_text_field( $new_instance['show_avatars'] ?? 'no' );
        $instance['max_depth']    = absint( $new_instance['max_depth'] ?? 5 );
        return $instance;
    }

    /* =============================================================
     * Helpers
     * ============================================================= */

    /**
     * Get the site ID where BuddyPress is the primary installation.
     *
     * @return int Site ID (defaults to 1).
     */
    private function get_bp_site_id() {
        if ( defined( 'BP_ROOT_BLOG' ) ) {
            return BP_ROOT_BLOG;
        }
        return get_main_site_id();
    }

    /**
     * Get group URL with BP version fallback.
     *
     * @param object $group Group object.
     * @return string
     */
    private static function get_group_url( $group ) {
        if ( function_exists( 'bp_get_group_url' ) ) {
            return bp_get_group_url( $group );
        }
        if ( function_exists( 'bp_get_group_permalink' ) ) {
            return bp_get_group_permalink( $group );
        }
        return '#';
    }
}

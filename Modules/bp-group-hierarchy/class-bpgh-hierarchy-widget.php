<?php
/**
 * BP Group Hierarchy — Hierarchy Widget.
 *
 * Displays the parent/child tree for the current group in a sidebar widget.
 *
 * @package BPGroupHierarchy
 * @since   1.0.0
 * @updated 2.0.0 — Works on activity + member pages, not just group pages.
 *                   Adds category/tag display, tooltip data attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Widget: Group Hierarchy Tree.
 */
class BPGH_Hierarchy_Widget extends WP_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(
            'bpgh_hierarchy_widget',
            __( 'BP Group Hierarchy', 'bp-group-hierarchy' ),
            array(
                'description' => __( 'Displays group parent/child hierarchy tree.', 'bp-group-hierarchy' ),
            )
        );
    }

    /**
     * Front-end output.
     *
     * v2.0: Expanded beyond bp_is_group() to also render on activity
     * pages and member profile pages when a group context is available.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {

        $group_id = $this->resolve_group_id( $instance );

        if ( ! $group_id ) {
            return;
        }

        $parent_id = BP_Group_Hierarchy::get_parent_id( $group_id );
        $children  = BP_Group_Hierarchy::get_children( $group_id );

        // Nothing to show if the group has no hierarchy.
        if ( ! $parent_id && empty( $children ) ) {
            return;
        }

        $title = ! empty( $instance['title'] )
            ? apply_filters( 'widget_title', $instance['title'] )
            : __( 'Group Hierarchy', 'bp-group-hierarchy' );

        $show_avatars = ! empty( $instance['show_avatars'] ) && 'yes' === $instance['show_avatars'];
        $show_meta    = ! empty( $instance['show_meta'] ) && 'yes' === $instance['show_meta'];

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

        echo '<div class="bpgh-widget-tree">';

        // Parent link.
        if ( $parent_id ) {
            $parent = groups_get_group( $parent_id );

            if ( ! empty( $parent->id ) ) {
                $parent_url = self::get_group_url( $parent );

                echo '<p class="bpgh-widget-parent">';

                if ( $show_avatars && function_exists( 'bp_get_group_avatar' ) ) {
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo bp_get_group_avatar( array(
                        'item_id' => $parent->id,
                        'type'    => 'thumb',
                        'width'   => 30,
                        'height'  => 30,
                    ) ) . ' ';
                }

                echo esc_html__( 'Parent:', 'bp-group-hierarchy' ) . ' ';
                echo '<a href="' . esc_url( $parent_url ) . '"';

                // v2.0: Tooltip data attribute.
                if ( 'yes' === get_option( 'bpgh_enable_tooltips', 'yes' ) ) {
                    echo ' data-bpgh-tooltip="' . esc_attr( $parent->id ) . '"';
                }

                echo '>' . esc_html( $parent->name ) . '</a>';
                echo '</p>';
            }
        }

        // Children list.
        if ( ! empty( $children ) ) {
            echo '<p class="bpgh-widget-children-label">' . esc_html__( 'Sub-groups:', 'bp-group-hierarchy' ) . '</p>';
            echo '<ul class="bpgh-widget-children">';

            foreach ( $children as $child ) {
                $child_url = self::get_group_url( $child );

                echo '<li>';

                if ( $show_avatars && function_exists( 'bp_get_group_avatar' ) ) {
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo bp_get_group_avatar( array(
                        'item_id' => $child->id,
                        'type'    => 'thumb',
                        'width'   => 24,
                        'height'  => 24,
                    ) ) . ' ';
                }

                echo '<a href="' . esc_url( $child_url ) . '"';

                if ( 'yes' === get_option( 'bpgh_enable_tooltips', 'yes' ) ) {
                    echo ' data-bpgh-tooltip="' . esc_attr( $child->id ) . '"';
                }

                echo '>' . esc_html( $child->name ) . '</a>';

                // v2.0: Show category + tags.
                if ( $show_meta ) {
                    $this->render_group_meta( $child->id );
                }

                echo '</li>';
            }

            echo '</ul>';
        }

        echo '</div>';
        echo $args['after_widget'];
    }

    /**
     * Back-end form.
     *
     * @param array $instance Previously saved values.
     */
    public function form( $instance ) {
        $title        = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Group Hierarchy', 'bp-group-hierarchy' );
        $show_avatars = ! empty( $instance['show_avatars'] ) ? $instance['show_avatars'] : 'no';
        $show_meta    = ! empty( $instance['show_meta'] ) ? $instance['show_meta'] : 'no';
        $fallback_id  = ! empty( $instance['fallback_group_id'] ) ? $instance['fallback_group_id'] : '';
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
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_avatars' ) ); ?>">
                <?php esc_html_e( 'Show group avatars:', 'bp-group-hierarchy' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'show_avatars' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'show_avatars' ) ); ?>">
                <option value="no" <?php selected( $show_avatars, 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                <option value="yes" <?php selected( $show_avatars, 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_meta' ) ); ?>">
                <?php esc_html_e( 'Show category & tags:', 'bp-group-hierarchy' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'show_meta' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'show_meta' ) ); ?>">
                <option value="no" <?php selected( $show_meta, 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
                <option value="yes" <?php selected( $show_meta, 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'fallback_group_id' ) ); ?>">
                <?php esc_html_e( 'Fallback group ID (for non-group pages):', 'bp-group-hierarchy' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'fallback_group_id' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'fallback_group_id' ) ); ?>"
                   type="number"
                   value="<?php echo esc_attr( $fallback_id ); ?>"
                   min="0" />
            <small><?php esc_html_e( 'Optional. Show hierarchy for this group on activity/member pages.', 'bp-group-hierarchy' ); ?></small>
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
        $instance['title']             = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['show_avatars']      = sanitize_text_field( $new_instance['show_avatars'] ?? 'no' );
        $instance['show_meta']         = sanitize_text_field( $new_instance['show_meta'] ?? 'no' );
        $instance['fallback_group_id'] = absint( $new_instance['fallback_group_id'] ?? 0 );
        return $instance;
    }

    /* =============================================================
     * Helpers
     * ============================================================= */

    /**
     * Resolve group ID from context.
     *
     * v2.0: On group pages uses current group. On activity/member pages
     * falls back to the widget's configured fallback group ID.
     *
     * @param array $instance Widget instance.
     * @return int|false Group ID or false if unavailable.
     */
    private function resolve_group_id( $instance ) {

        // On a group page — use current group.
        if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
            return bp_get_current_group_id();
        }

        // v2.0: Activity page — try to resolve a group from activity context.
        // NOTE: bp_get_activity_item_id() reads $activities_template->activity->item_id
        // which is NULL before the activity loop starts. Widgets render in the sidebar
        // before the loop, so we must check the global directly.
        if ( function_exists( 'bp_is_activity_component' ) && bp_is_activity_component() ) {
            global $activities_template;
            if (
                ! empty( $activities_template )
                && isset( $activities_template->activity )
                && ! empty( $activities_template->activity->item_id )
                && 'groups' === ( $activities_template->activity->component ?? '' )
            ) {
                $item_id = (int) $activities_template->activity->item_id;
                if ( $item_id > 0 && function_exists( 'groups_get_group' ) ) {
                    $group = groups_get_group( $item_id );
                    if ( ! empty( $group->id ) ) {
                        return (int) $group->id;
                    }
                }
            }

            // Activity page but no loop context yet — use fallback.
            if ( ! empty( $instance['fallback_group_id'] ) ) {
                return absint( $instance['fallback_group_id'] );
            }

            return false;
        }

        // v2.0: Member profile page — use fallback group.
        if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
            if ( ! empty( $instance['fallback_group_id'] ) ) {
                return absint( $instance['fallback_group_id'] );
            }
        }

        // Fallback from widget config.
        if ( ! empty( $instance['fallback_group_id'] ) ) {
            return absint( $instance['fallback_group_id'] );
        }

        return false;
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

    /**
     * Render category + tag badges for a group.
     *
     * @param int $group_id Group ID.
     */
    private function render_group_meta( $group_id ) {

        if ( class_exists( 'BPGH_Categories' ) ) {
            $cat = BPGH_Categories::get_group_category( $group_id );
            if ( $cat ) {
                $all = BPGH_Categories::get_categories();
                $label = isset( $all[ $cat ] ) ? $all[ $cat ] : $cat;
                echo ' <span class="bpgh-cat-badge">' . esc_html( $label ) . '</span>';
            }
        }

        if ( class_exists( 'BPGH_Tags' ) ) {
            $tags = BPGH_Tags::get_tags( $group_id );
            if ( ! empty( $tags ) ) {
                foreach ( $tags as $tag ) {
                    echo ' <span class="bpgh-tag">' . esc_html( $tag ) . '</span>';
                }
            }
        }
    }
}

<?php
/**
 * BP Group Hierarchy — Child Groups Widget.
 *
 * Displays the child groups (sub-groups) for a specified or current
 * parent group, with avatars, tooltips, and member counts.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Child_Groups_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'bpgh_child_groups_widget',
            __( 'BP Child Groups', 'bp-group-hierarchy' ),
            array( 'description' => __( 'Displays sub-groups of the current or a specified parent group.', 'bp-group-hierarchy' ) )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! function_exists( 'groups_get_groups' ) ) {
            return;
        }

        $show_avatar = ! empty( $instance['show_avatar'] ) && 'yes' === $instance['show_avatar'];
        $show_count  = ! empty( $instance['show_count'] ) && 'yes' === $instance['show_count'];
        $max_depth   = ! empty( $instance['max_depth'] ) ? absint( $instance['max_depth'] ) : 2;

        // Determine parent group ID.
        $parent_id = 0;

        if ( ! empty( $instance['parent_id'] ) ) {
            // Admin-specified parent.
            $parent_id = absint( $instance['parent_id'] );
        } elseif ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
            // Current group page.
            $parent_id = bp_get_current_group_id();
        }

        if ( 0 === $parent_id ) {
            return;
        }

        $children = BP_Group_Hierarchy::get_children( $parent_id );

        if ( empty( $children ) ) {
            return;
        }

        $title = ! empty( $instance['title'] )
            ? apply_filters( 'widget_title', $instance['title'] )
            : __( 'Sub-groups', 'bp-group-hierarchy' );

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

        echo '<ul class="bpgh-child-groups-widget">';
        self::render_children( $children, $show_avatar, $show_count, 1, $max_depth );
        echo '</ul>';

        echo $args['after_widget'];
    }

    /**
     * Recursively render children as nested lists.
     */
    private static function render_children( $children, $show_avatar, $show_count, $depth, $max_depth ) {
        foreach ( $children as $child ) {
            $url = self::get_group_url( $child );

            echo '<li class="bpgh-cw-item" data-bpgh-tooltip="' . esc_attr( $child->id ) . '">';

            if ( $show_avatar && function_exists( 'bp_core_fetch_avatar' ) ) {
                echo bp_core_fetch_avatar( array(
                    'item_id' => $child->id,
                    'object'  => 'group',
                    'type'    => 'thumb',
                    'width'   => 32,
                    'height'  => 32,
                ) );
            }

            echo '<a href="' . esc_url( $url ) . '">' . esc_html( $child->name ) . '</a>';

            if ( $show_count && isset( $child->total_member_count ) ) {
                echo ' <span class="bpgh-cw-count">(' . esc_html( $child->total_member_count ) . ')</span>';
            }

            // Recursive sub-children.
            if ( $depth < $max_depth ) {
                $sub_children = BP_Group_Hierarchy::get_children( $child->id );
                if ( ! empty( $sub_children ) ) {
                    echo '<ul class="bpgh-cw-sub">';
                    self::render_children( $sub_children, $show_avatar, $show_count, $depth + 1, $max_depth );
                    echo '</ul>';
                }
            }

            echo '</li>';
        }
    }

    public function form( $instance ) {
        $title       = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Sub-groups', 'bp-group-hierarchy' );
        $parent_id   = ! empty( $instance['parent_id'] ) ? absint( $instance['parent_id'] ) : 0;
        $show_avatar = ! empty( $instance['show_avatar'] ) ? $instance['show_avatar'] : 'yes';
        $show_count  = ! empty( $instance['show_count'] ) ? $instance['show_count'] : 'yes';
        $max_depth   = ! empty( $instance['max_depth'] ) ? absint( $instance['max_depth'] ) : 2;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bp-group-hierarchy' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'parent_id' ) ); ?>"><?php esc_html_e( 'Parent Group ID (0 = current page):', 'bp-group-hierarchy' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'parent_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'parent_id' ) ); ?>" type="number" min="0" value="<?php echo esc_attr( $parent_id ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'max_depth' ) ); ?>"><?php esc_html_e( 'Max depth:', 'bp-group-hierarchy' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max_depth' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_depth' ) ); ?>" type="number" min="1" max="10" value="<?php echo esc_attr( $max_depth ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_avatar' ) ); ?>"><?php esc_html_e( 'Show avatars:', 'bp-group-hierarchy' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_avatar' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_avatar' ) ); ?>">
                <option value="yes" <?php selected( $show_avatar, 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                <option value="no" <?php selected( $show_avatar, 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>"><?php esc_html_e( 'Show member counts:', 'bp-group-hierarchy' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>">
                <option value="yes" <?php selected( $show_count, 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                <option value="no" <?php selected( $show_count, 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array(
            'title'       => sanitize_text_field( $new_instance['title'] ),
            'parent_id'   => absint( $new_instance['parent_id'] ),
            'max_depth'   => max( 1, absint( $new_instance['max_depth'] ) ),
            'show_avatar' => 'yes' === $new_instance['show_avatar'] ? 'yes' : 'no',
            'show_count'  => 'yes' === $new_instance['show_count'] ? 'yes' : 'no',
        );
    }

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

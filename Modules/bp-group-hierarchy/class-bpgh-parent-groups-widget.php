<?php
/**
 * BP Group Hierarchy — Parent Groups Widget.
 *
 * Displays a list of all top-level (parent) groups with avatars,
 * tooltips, member counts, and optional child count indicators.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Parent_Groups_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'bpgh_parent_groups_widget',
            __( 'BP Parent Groups', 'bp-group-hierarchy' ),
            array( 'description' => __( 'Displays all top-level parent groups.', 'bp-group-hierarchy' ) )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! function_exists( 'groups_get_groups' ) ) {
            return;
        }

        $limit       = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 10;
        $show_avatar = ! empty( $instance['show_avatar'] ) && 'yes' === $instance['show_avatar'];
        $show_count  = ! empty( $instance['show_count'] ) && 'yes' === $instance['show_count'];
        $sort        = ! empty( $instance['sort'] ) ? $instance['sort'] : 'active';

        // Fetch groups with no parent.
        $all = groups_get_groups( array(
            'show_hidden' => false,
            'per_page'    => 200,
            'type'        => $sort,
        ) );

        $parents = array();
        if ( ! empty( $all['groups'] ) ) {
            foreach ( $all['groups'] as $group ) {
                if ( 0 === BP_Group_Hierarchy::get_parent_id( $group->id ) ) {
                    $parents[] = $group;
                }
                if ( count( $parents ) >= $limit ) {
                    break;
                }
            }
        }

        if ( empty( $parents ) ) {
            return;
        }

        $title = ! empty( $instance['title'] )
            ? apply_filters( 'widget_title', $instance['title'] )
            : __( 'Parent Groups', 'bp-group-hierarchy' );

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

        echo '<ul class="bpgh-parent-groups-widget">';

        foreach ( $parents as $group ) {
            $url = self::get_group_url( $group );
            $children_count = count( BP_Group_Hierarchy::get_children( $group->id ) );

            echo '<li class="bpgh-pw-item" data-bpgh-tooltip="' . esc_attr( $group->id ) . '">';

            if ( $show_avatar && function_exists( 'bp_core_fetch_avatar' ) ) {
                echo '<div class="bpgh-pw-avatar">';
                echo bp_core_fetch_avatar( array(
                    'item_id' => $group->id,
                    'object'  => 'group',
                    'type'    => 'thumb',
                    'width'   => 40,
                    'height'  => 40,
                ) );
                echo '</div>';
            }

            echo '<div class="bpgh-pw-info">';
            echo '<a href="' . esc_url( $url ) . '">' . esc_html( $group->name ) . '</a>';

            if ( $show_count ) {
                echo '<span class="bpgh-pw-meta">';
                if ( isset( $group->total_member_count ) ) {
                    echo esc_html( sprintf(
                        _n( '%d member', '%d members', $group->total_member_count, 'bp-group-hierarchy' ),
                        $group->total_member_count
                    ) );
                }
                if ( $children_count > 0 ) {
                    echo ' &middot; ' . esc_html( sprintf(
                        _n( '%d sub-group', '%d sub-groups', $children_count, 'bp-group-hierarchy' ),
                        $children_count
                    ) );
                }
                echo '</span>';
            }

            echo '</div>';
            echo '</li>';
        }

        echo '</ul>';
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title       = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Parent Groups', 'bp-group-hierarchy' );
        $limit       = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 10;
        $show_avatar = ! empty( $instance['show_avatar'] ) ? $instance['show_avatar'] : 'yes';
        $show_count  = ! empty( $instance['show_count'] ) ? $instance['show_count'] : 'yes';
        $sort        = ! empty( $instance['sort'] ) ? $instance['sort'] : 'active';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bp-group-hierarchy' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Max groups:', 'bp-group-hierarchy' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" min="1" max="50" value="<?php echo esc_attr( $limit ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>"><?php esc_html_e( 'Sort by:', 'bp-group-hierarchy' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort' ) ); ?>">
                <option value="active" <?php selected( $sort, 'active' ); ?>><?php esc_html_e( 'Most Active', 'bp-group-hierarchy' ); ?></option>
                <option value="newest" <?php selected( $sort, 'newest' ); ?>><?php esc_html_e( 'Newest', 'bp-group-hierarchy' ); ?></option>
                <option value="alphabetical" <?php selected( $sort, 'alphabetical' ); ?>><?php esc_html_e( 'Alphabetical', 'bp-group-hierarchy' ); ?></option>
                <option value="popular" <?php selected( $sort, 'popular' ); ?>><?php esc_html_e( 'Most Members', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_avatar' ) ); ?>"><?php esc_html_e( 'Show avatars:', 'bp-group-hierarchy' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_avatar' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_avatar' ) ); ?>">
                <option value="yes" <?php selected( $show_avatar, 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                <option value="no" <?php selected( $show_avatar, 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>"><?php esc_html_e( 'Show counts:', 'bp-group-hierarchy' ); ?></label>
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
            'limit'       => absint( $new_instance['limit'] ),
            'show_avatar' => 'yes' === $new_instance['show_avatar'] ? 'yes' : 'no',
            'show_count'  => 'yes' === $new_instance['show_count'] ? 'yes' : 'no',
            'sort'        => sanitize_text_field( $new_instance['sort'] ),
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

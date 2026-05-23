<?php
/**
 * BP Group Hierarchy — Category Filter Widget.
 *
 * Displays a list or dropdown of group categories for filtering.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Category_Filter_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'bpgh_category_filter_widget',
            __( 'BP Group Category Filter', 'bp-group-hierarchy' ),
            array( 'description' => __( 'Filter groups by category via links or dropdown.', 'bp-group-hierarchy' ) )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! class_exists( 'BPGH_Categories' ) || ! BPGH_Categories::is_enabled() ) {
            return;
        }

        $categories = BPGH_Categories::get_categories();

        if ( empty( $categories ) ) {
            return;
        }

        $counts      = BPGH_Categories::get_category_counts();
        $show_counts = ! empty( $instance['show_counts'] ) && 'yes' === $instance['show_counts'];
        $display     = ! empty( $instance['display'] ) ? $instance['display'] : 'list';
        $active_cat  = isset( $_GET['bpgh_category'] ) ? sanitize_title( wp_unslash( $_GET['bpgh_category'] ) ) : '';

        $title = ! empty( $instance['title'] )
            ? apply_filters( 'widget_title', $instance['title'] )
            : __( 'Group Categories', 'bp-group-hierarchy' );

        $directory_url = bpgh_get_directory_url();

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

        if ( 'dropdown' === $display ) {
            // Dropdown mode.
            echo '<div class="bpgh-cat-filter-widget">';
            echo '<select class="bpgh-cat-dropdown" onchange="if(this.value){window.location=this.value;}">';
            echo '<option value="' . esc_url( $directory_url ) . '">' . esc_html__( 'All Categories', 'bp-group-hierarchy' ) . '</option>';

            foreach ( $categories as $slug => $label ) {
                $url      = add_query_arg( 'bpgh_category', $slug, $directory_url );
                $count    = isset( $counts[ $slug ] ) ? $counts[ $slug ] : 0;
                $selected = selected( $active_cat, $slug, false );
                $text     = $show_counts ? sprintf( '%s (%d)', $label, $count ) : $label;

                echo '<option value="' . esc_url( $url ) . '"' . $selected . '>' . esc_html( $text ) . '</option>';
            }

            echo '</select>';
            echo '</div>';
        } else {
            // List mode.
            echo '<ul class="bpgh-cat-filter-list">';

            // "All" link.
            $all_class = empty( $active_cat ) ? ' class="bpgh-active"' : '';
            echo '<li' . $all_class . '><a href="' . esc_url( $directory_url ) . '">' . esc_html__( 'All', 'bp-group-hierarchy' ) . '</a></li>';

            foreach ( $categories as $slug => $label ) {
                $url   = add_query_arg( 'bpgh_category', $slug, $directory_url );
                $count = isset( $counts[ $slug ] ) ? $counts[ $slug ] : 0;
                $class = ( $active_cat === $slug ) ? ' class="bpgh-active"' : '';

                echo '<li' . $class . '>';
                echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
                if ( $show_counts ) {
                    echo ' <span class="bpgh-count">(' . esc_html( $count ) . ')</span>';
                }
                echo '</li>';
            }

            echo '</ul>';
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title       = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Group Categories', 'bp-group-hierarchy' );
        $show_counts = ! empty( $instance['show_counts'] ) ? $instance['show_counts'] : 'yes';
        $display     = ! empty( $instance['display'] ) ? $instance['display'] : 'list';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bp-group-hierarchy' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>"><?php esc_html_e( 'Display as:', 'bp-group-hierarchy' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>">
                <option value="list" <?php selected( $display, 'list' ); ?>><?php esc_html_e( 'List', 'bp-group-hierarchy' ); ?></option>
                <option value="dropdown" <?php selected( $display, 'dropdown' ); ?>><?php esc_html_e( 'Dropdown', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>"><?php esc_html_e( 'Show counts:', 'bp-group-hierarchy' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_counts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_counts' ) ); ?>">
                <option value="yes" <?php selected( $show_counts, 'yes' ); ?>><?php esc_html_e( 'Yes', 'bp-group-hierarchy' ); ?></option>
                <option value="no" <?php selected( $show_counts, 'no' ); ?>><?php esc_html_e( 'No', 'bp-group-hierarchy' ); ?></option>
            </select>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array(
            'title'       => sanitize_text_field( $new_instance['title'] ),
            'display'     => in_array( $new_instance['display'], array( 'list', 'dropdown' ), true ) ? $new_instance['display'] : 'list',
            'show_counts' => 'yes' === $new_instance['show_counts'] ? 'yes' : 'no',
        );
    }
}

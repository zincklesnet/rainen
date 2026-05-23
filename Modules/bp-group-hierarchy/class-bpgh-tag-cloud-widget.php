<?php
/**
 * BP Group Hierarchy — Tag Cloud Widget.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Tag_Cloud_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'bpgh_tag_cloud_widget',
            __( 'BP Group Tag Cloud', 'bp-group-hierarchy' ),
            array( 'description' => __( 'Displays a weighted tag cloud of group tags.', 'bp-group-hierarchy' ) )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! class_exists( 'BPGH_Tags' ) || ! BPGH_Tags::is_enabled() ) {
            return;
        }

        $limit    = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 30;
        $tag_data = BPGH_Tags::get_tag_cloud_data( $limit );

        if ( empty( $tag_data ) ) {
            return;
        }

        $title = ! empty( $instance['title'] )
            ? apply_filters( 'widget_title', $instance['title'] )
            : __( 'Group Tags', 'bp-group-hierarchy' );

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

        $max = max( $tag_data );
        $min = min( $tag_data );

        $directory_url = bpgh_get_directory_url();

        echo '<div class="bpgh-tag-cloud-widget">';

        foreach ( $tag_data as $tag => $count ) {
            $size = $max > $min
                ? 0.8 + ( ( $count - $min ) / ( $max - $min ) ) * 1.4
                : 1.2;

            $url = add_query_arg( 'bpgh_tag', urlencode( $tag ), $directory_url );

            echo '<a href="' . esc_url( $url ) . '" class="bpgh-cloud-tag" style="font-size:' . esc_attr( round( $size, 2 ) ) . 'em;" title="' . esc_attr( sprintf( _n( '%d group', '%d groups', $count, 'bp-group-hierarchy' ), $count ) ) . '">';
            echo esc_html( $tag );
            echo '</a> ';
        }

        echo '</div>';
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Group Tags', 'bp-group-hierarchy' );
        $limit = ! empty( $instance['limit'] ) ? absint( $instance['limit'] ) : 30;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bp-group-hierarchy' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Max tags:', 'bp-group-hierarchy' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" min="5" max="100" value="<?php echo esc_attr( $limit ); ?>" />
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array(
            'title' => sanitize_text_field( $new_instance['title'] ),
            'limit' => absint( $new_instance['limit'] ),
        );
    }
}

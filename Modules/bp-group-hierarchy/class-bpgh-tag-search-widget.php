<?php
/**
 * BP Group Hierarchy — Tag Search Widget.
 *
 * Provides a front-end search box for group tags with autocomplete.
 *
 * @package BPGroupHierarchy
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BPGH_Tag_Search_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'bpgh_tag_search_widget',
            __( 'BP Group Tag Search', 'bp-group-hierarchy' ),
            array(
                'description' => __( 'Search groups by tag with autocomplete.', 'bp-group-hierarchy' ),
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! class_exists( 'BPGH_Tags' ) || ! BPGH_Tags::is_enabled() ) {
            return;
        }

        $title = ! empty( $instance['title'] )
            ? apply_filters( 'widget_title', $instance['title'] )
            : __( 'Search by Tag', 'bp-group-hierarchy' );

        echo $args['before_widget'];
        echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

        $directory_url = bpgh_get_directory_url();

        echo '<div class="bpgh-tag-search-widget">';
        echo '<form method="get" action="' . esc_url( $directory_url ) . '">';
        echo '<input type="text" name="bpgh_tag" class="bpgh-tag-search-input" placeholder="' . esc_attr__( 'Search tags...', 'bp-group-hierarchy' ) . '" autocomplete="off" />';
        echo '<div class="bpgh-tag-suggestions" style="display:none;"></div>';
        echo '<button type="submit" class="bpgh-tag-search-btn">' . esc_html__( 'Search', 'bp-group-hierarchy' ) . '</button>';
        echo '</form>';
        echo '</div>';

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Search by Tag', 'bp-group-hierarchy' );
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
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array( 'title' => sanitize_text_field( $new_instance['title'] ) );
    }
}

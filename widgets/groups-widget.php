<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Required by a fixed path in functions.php; renaming would break that out-of-scope reference.
/**
 * BuddyPress Groups Widget.
 *
 * @package BuddyPress
 * @subpackage GroupsWidgets
 * @since 1.0.0
 */
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Groups widget.
 *
 * @since 1.0.3
 */
class BP_REIGN_Groups_Widget extends WP_Widget {

	/**
	 * Working as a group, we get things done better.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => esc_html__( 'A slider for a list of recently active, popular, newest, or alphabetical groups', 'reign' ),
			'classname'                   => 'widget_rign_groups_widget buddypress widget',
			'customize_selective_refresh' => true,
		);
		parent::__construct( false, esc_html_x( 'Reign - Groups', 'widget name', 'reign' ), $widget_ops );
	}

	/**
	 * Extends our front-end output method.
	 *
	 * @since 1.0.3
	 *
	 * @param array $args     Array of arguments for the widget.
	 * @param array $instance Widget instance data.
	 */
	public function widget( $args, $instance ) {

		$defaults = array(
			'title'            => esc_html__( 'Groups', 'reign' ),
			'max_groups'       => 4,
			'columns'          => 2,
			'group_default'    => 'active',
			'display_view_all' => 'show',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		global $groups_template;

		/**
		 * Filters the user ID to use with the widget instance.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value Empty user ID.
		 */
		$user_id = apply_filters( 'bp_group_widget_user_id', '0' );

		$before_widget = isset( $args['before_widget'] ) ? $args['before_widget'] : '';
		$after_widget  = isset( $args['after_widget'] ) ? $args['after_widget'] : '';
		$before_title  = isset( $args['before_title'] ) ? $args['before_title'] : '';
		$after_title   = isset( $args['after_title'] ) ? $args['after_title'] : '';

		if ( empty( $instance['group_default'] ) ) {
			$instance['group_default'] = 'popular';
		}

		/**
		 * Filters the title of the Groups widget.
		 *
		 * @since 1.8.0
		 * @since 2.3.0 Added 'instance' and 'id_base' to arguments passed to filter.
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		/**
		 * Filters the separator of the group widget links.
		 *
		 * @since 2.4.0
		 *
		 * @param string $separator Separator string. Default '|'.
		 */
		$separator = apply_filters( 'bp_groups_widget_separator', '|' );

		echo isset( $before_widget ) ? $before_widget : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$max_groups = ! empty( $instance['max_groups'] ) ? (int) $instance['max_groups'] : 4;
		$columns    = ! empty( $instance['columns'] ) ? (int) $instance['columns'] : 2;

		$group_args = array(
			'user_id'  => $user_id,
			'type'     => $instance['group_default'],
			'per_page' => $max_groups,
			'max'      => $max_groups,
		);

		// Back up the global.
		$old_groups_template = $groups_template;
		?>
		<div id="rg-group-section" class="rg-group-section rg-group">
			<div class="rg-group-heading aligncenter rg-heading">
				<?php
				if ( isset( $before_title ) && isset( $after_title ) ) {
					echo $before_title . esc_html( $title ) . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo esc_html( $title );
				}

				?>
			</div>
			<div class="container">
				<?php if ( bp_has_groups( $group_args ) ) : ?>
					<ul id="groups-list" class="item-list wb-grid rg-group-list" aria-live="assertive" aria-atomic="true" aria-relevant="all">

						<?php
						while ( bp_groups() ) :
							bp_the_group();
							?>

							<li <?php bp_group_class( array( 'wb-grid-cell', 'sm-wb-grid-1-1', 'md-wb-grid-1-2', 'lg-wb-grid-1-' . $columns ) ); ?>>
								<div class="bp-group-inner-wrap">
									<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
										<?php if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) : ?>
											<a class="item-avatar-group" href="<?php bp_group_url(); ?>"><?php bp_group_avatar( '' ); ?></a>
										<?php else : ?>
											<a class="item-avatar-group" href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( '' ); ?></a>
										<?php endif; ?>
									<?php endif; ?>

									<div class="group-content-wrap">

										<div class="item">
											<h3 class="item-title"><?php bp_group_link(); ?></h3>
											<div class="meta group-meta"><?php bp_group_member_count(); ?></div>
											<?php /* translators: %s: Human-readable last-active time. */ ?>
										<div class="item-meta"><span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( esc_html__( 'active %s', 'reign' ), esc_html( bp_get_group_last_active() ) ); ?></span></div>
											<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>

											<?php
											/**
											 * Fires inside the listing of an individual group listing item.
											 *
											 * @since 1.1.0
											 */
											do_action( 'bp_directory_groups_item' );
											?>
										</div>

										<div class="group-admins-wrap">
											<?php reign_bp_group_list_admins(); ?>
										</div>

									</div>
								</div>
							</li>
						<?php endwhile; ?>
					</ul>

					<?php if ( 'show' === $instance['display_view_all'] ) : ?>
						<div class="group-more aligncenter">
							<a class="rg-action button" href="<?php echo esc_attr( home_url( '/' ) . bp_get_groups_slug() ); ?>"><?php esc_html_e( 'View All', 'reign' ); ?></a>
						</div>
					<?php endif; ?>

					<?php wp_nonce_field( 'groups_widget_groups_list', '_wpnonce-groups' ); ?>
					<input type="hidden" name="groups_widget_max" id="groups_widget_max" value="<?php echo esc_attr( $max_groups ); ?>" />

				<?php else : ?>
					<?php
					$groups_url = function_exists( 'bp_get_groups_directory_url' ) ? bp_get_groups_directory_url() : home_url( '/' . bp_get_groups_slug() );
					reign_render_empty_state(
						'<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
						__( 'There are no groups to display.', 'reign' ),
						esc_url( $groups_url ),
						__( 'Browse Groups', 'reign' )
					);
					?>
				<?php endif; ?>
			</div>
		</div>

		<?php

		echo isset( $after_widget ) ? $after_widget : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Restore the global.
		$groups_template = $old_groups_template;
	}

	/**
	 * Extends our update method.
	 *
	 * @since 1.0.3
	 *
	 * @param array $new_instance New instance data.
	 * @param array $old_instance Original instance data.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']            = sanitize_text_field( $new_instance['title'] );
		$instance['max_groups']       = absint( $new_instance['max_groups'] );
		$instance['columns']          = absint( $new_instance['columns'] );
		$instance['group_default']    = sanitize_text_field( $new_instance['group_default'] );
		$instance['display_view_all'] = isset( $new_instance['display_view_all'] ) ? sanitize_text_field( $new_instance['display_view_all'] ) : 'show';

		return $instance;
	}

	/**
	 * Extends our form method.
	 *
	 * @since 1.0.3
	 *
	 * @param array $instance Current instance.
	 * @return mixed
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'            => esc_html__( 'Groups', 'reign' ),
			'max_groups'       => 4,
			'columns'          => 2,
			'group_default'    => 'active',
			'display_view_all' => 'show',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title            = strip_tags( $instance['title'] );
		$max_groups       = strip_tags( $instance['max_groups'] );
		$columns          = strip_tags( $instance['columns'] );
		$group_default    = strip_tags( $instance['group_default'] );
		$display_view_all = strip_tags( $instance['display_view_all'] );
		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'reign' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'max_groups' ) ); ?>"><?php esc_html_e( 'Max groups to show:', 'reign' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max_groups' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_groups' ) ); ?>" type="text" value="<?php echo esc_attr( $max_groups ); ?>" style="width: 30%" /></label></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'No Of Columns:', 'reign' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>" type="number" value="<?php echo esc_attr( $columns ); ?>" style="width: 30%" /></label></p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'group_default' ) ); ?>"><?php esc_html_e( 'Default groups to show:', 'reign' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'group_default' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'group_default' ) ); ?>">
				<option value="newest" <?php selected( $group_default, 'newest' ); ?>><?php esc_html_e( 'Newest', 'reign' ); ?></option>
				<option value="active" <?php selected( $group_default, 'active' ); ?>><?php esc_html_e( 'Active', 'reign' ); ?></option>
				<option value="popular"  <?php selected( $group_default, 'popular' ); ?>><?php esc_html_e( 'Popular', 'reign' ); ?></option>
				<option value="alphabetical" <?php selected( $group_default, 'alphabetical' ); ?>><?php esc_html_e( 'Alphabetical', 'reign' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_view_all' ) ); ?>"><?php esc_html_e( 'Display View All Button:', 'reign' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'display_view_all' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'display_view_all' ) ); ?>">
				<option value="show" <?php selected( $display_view_all, 'show' ); ?>><?php esc_html_e( 'Show', 'reign' ); ?></option>
				<option value="hide" <?php selected( $display_view_all, 'hide' ); ?>><?php esc_html_e( 'Hide', 'reign' ); ?></option>
			</select>
		</p>
		<?php
	}
}

/**
 * Register the widget
 */
// phpcs:ignore Universal.Files.SeparateFunctionsFromOO.Mixed -- Widget class is paired with its registration callback in one file by design.
function reign_register_groups_widget() {
	if ( bp_is_active( 'groups' ) ) {
		register_widget( 'BP_REIGN_Groups_Widget' );
	}
}

add_action( 'bp_widgets_init', 'reign_register_groups_widget' );

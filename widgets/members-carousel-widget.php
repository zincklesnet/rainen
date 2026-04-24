<?php
/**
 * BuddyPress Members Widget.
 *
 * @package BuddyPress
 * @subpackage MembersWidgets
 * @since 1.0.3
 */
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Members Widget.
 *
 * @since 1.0.3
 */
class BP_REIGN_Members_Widget extends WP_Widget {

	/**
	 * Constructor method.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		// Setup widget name & description.
		$name        = esc_html_x( 'Reign - Members Carousel', 'widget name', 'reign' );
		$description = esc_html__( 'A slider for displaying active, popular, and newest members', 'reign' );

		// Call WP_Widget constructor.
		parent::__construct(
			false,
			$name,
			array(
				'description'                 => $description,
				'classname'                   => 'widget_bp_core_members_widget buddypress widget',
				'customize_selective_refresh' => false,
			)
		);
	}

	/**
	 * Display the Members widget.
	 *
	 * @since 1.0.3
	 *
	 * @see WP_Widget::widget() for description of parameters.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings, as saved by the user.
	 */
	public function widget( $args, $instance ) {
		global $members_template;

		// Get widget settings.
		$settings = $this->parse_settings( $instance );

		/**
		 * Filters the title of the Members widget.
		 *
		 * @since 1.8.0
		 * @since 2.3.0 Added 'instance' and 'id_base' to arguments passed to filter.
		 *
		 * @param string $title    The widget title.
		 * @param array  $settings The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $settings['title'], $settings, $this->id_base );

		/**
		 * Filters the separator of the member widget links.
		 *
		 * @since 2.4.0
		 *
		 * @param string $separator Separator string. Default '|'.
		 */
		$separator = apply_filters( 'bp_members_widget_separator', '|' );

		// Setup args for querying members.
		$members_args = array(
			'user_id'         => 0,
			'type'            => $settings['member_default'],
			'per_page'        => $settings['max_members'],
			'max'             => $settings['max_members'],
			'populate_extras' => true,
			'search_terms'    => false,
		);

		// Back up the global.
		$old_members_template = $members_template;

		echo isset( $args['before_widget'] ) ? $args['before_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<div id="rg-member-section" class="rg-members-section rg-home-section rg-slick-list-wrapper">
			<div class="rg-slider-heading aligncenter rg-heading">
			<?php
			if ( isset( $args['before_title'] ) || isset( $args['after_title'] ) ) {
				// Output before widget HTMl, title (and maybe content before & after it).
				echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo esc_html( $title );
			}
			?>
			</div>
			<div id="members-carousel-list-widget" class="rg-slick-list-container container">
			<?php if ( bp_has_members( $members_args ) ) : ?>
					<?php
					while ( bp_members() ) :
						bp_the_member();
						?>
						<?php $user_id = bp_get_member_user_id(); ?>
						<div class="rg-member rg-image-box">
							<?php $this->reign_bp_member_cover(); ?>
							<div class="item-avatar">
							<a href="<?php bp_member_permalink(); ?>"><?php echo reign_get_online_status( $user_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo get_avatar( bp_get_member_user_id(), 100, '', '', array( 'loading' => 'lazy' ) ); ?></a>
							</div>
							<?php if ( $settings['display_member_name'] == 'show' ) : ?>
								<div class="rg-member-decription">
									<h3 class="item-title"><a class="name fn rg-member-title" href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></h3>
								</div>
							<?php endif; ?>
						</div>

					<?php endwhile; ?>

					<?php wp_nonce_field( 'bp_core_widget_members', '_wpnonce-members', false ); ?>

					<input type="hidden" name="members_widget_max" id="members_widget_max" value="<?php echo esc_attr( $settings['max_members'] ); ?>" />

				<?php else : ?>

					<?php
					$members_url = function_exists( 'bp_get_members_directory_url' ) ? bp_get_members_directory_url() : home_url( '/' . bp_get_members_slug() );
					reign_render_empty_state(
						'<svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
						__( 'No members to display yet.', 'reign' ),
						esc_url( $members_url ),
						__( 'Browse Members', 'reign' )
					);
					?>

				<?php endif; ?>
			</div>
		</div>

			<?php
			echo isset( $args['after_widget'] ) ? $args['after_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Restore the global.
			$members_template = $old_members_template;
	}

	/**
	 * Update the Members widget options.
	 *
	 * @since 1.0.3
	 *
	 * @param array $new_instance The new instance options.
	 * @param array $old_instance The old instance options.
	 * @return array $instance The parsed options to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

        $instance['title']               = sanitize_text_field( $new_instance['title'] );
        $instance['max_members']         = absint( $new_instance['max_members'] );
        $instance['member_default']      = sanitize_text_field( $new_instance['member_default'] );
        $instance['display_member_name'] = isset( $new_instance['display_member_name'] ) ? sanitize_text_field( $new_instance['display_member_name'] ) : 'show';

		return $instance;
	}

	/**
	 * Output the Members widget options form.
	 *
	 * @since 1.0.3
	 *
	 * @param array $instance Widget instance settings.
	 * @return void
	 */
	public function form( $instance ) {

		// Get widget settings.
		$settings            = $this->parse_settings( $instance );
		$title               = wp_strip_all_tags( $settings['title'] );
		$max_members         = wp_strip_all_tags( $settings['max_members'] );
		$member_default      = wp_strip_all_tags( $settings['member_default'] );
		$display_member_name = wp_strip_all_tags( $settings['display_member_name'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
			<?php esc_html_e( 'Title:', 'reign' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'max_members' ) ); ?>">
			<?php esc_html_e( 'Max members to show:', 'reign' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max_members' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_members' ) ); ?>" type="text" value="<?php echo esc_attr( $max_members ); ?>" style="width: 30%" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'member_default' ) ); ?>"><?php esc_html_e( 'Default members to show:', 'reign' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'member_default' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'member_default' ) ); ?>">
				<option value="newest"
			<?php
			if ( 'newest' === $member_default ) :
				?>
					selected="selected"<?php endif; ?>><?php esc_html_e( 'Newest', 'reign' ); ?></option>
				<option value="active"
				<?php
				if ( 'active' === $member_default ) :
					?>
					selected="selected"<?php endif; ?>><?php esc_html_e( 'Active', 'reign' ); ?></option>
				<option value="popular"
					<?php
					if ( 'popular' === $member_default ) :
						?>
					selected="selected"<?php endif; ?>><?php esc_html_e( 'Popular', 'reign' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_member_name' ) ); ?>"><?php esc_html_e( 'Display Member Name:', 'reign' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'display_member_name' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'display_member_name' ) ); ?>">
				<option value="show"
					<?php
					if ( 'show' === $display_member_name ) :
						?>
					selected="selected"<?php endif; ?>><?php esc_html_e( 'Show', 'reign' ); ?></option>
				<option value="hide"
					<?php
					if ( 'hide' === $display_member_name ) :
						?>
					selected="selected"<?php endif; ?>><?php esc_html_e( 'Hide', 'reign' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Merge the widget settings into defaults array.
	 *
	 * @since 2.3.0
	 *
	 * @param array $instance Widget instance settings.
	 * @return array
	 */
	public function parse_settings( $instance = array() ) {
		return bp_parse_args(
			$instance,
			array(
				'title'               => esc_html__( 'Members', 'reign' ),
				'max_members'         => 5,
				'member_default'      => 'active',
				'display_member_name' => 'show',
			),
			'members_widget_settings'
		);
	}


	public function reign_bp_member_cover() {
		global $wbtm_reign_settings;
		$args = array(
			'object_dir' => 'members',
			'item_id'    => $user_id = bp_get_member_user_id(),
			'type'       => 'cover-image',
		);
		$cover_img_url = bp_attachments_get_attachment( 'url', $args );
		if ( empty( $cover_img_url ) ) {
			$cover_img_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_xprofile_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
			if ( empty( $cover_img_url ) ) {
				$cover_img_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
			}
		}
        echo '<div class="wbtm-mem-cover-img"><img loading="lazy" src="' . esc_url( $cover_img_url ) . '" alt="" /></div>';
	}
}

/**
 * Register the widget
 */
function reign_members_register_widget() {
	register_widget( 'BP_REIGN_Members_Widget' );
}

add_action( 'bp_widgets_init', 'reign_members_register_widget' );

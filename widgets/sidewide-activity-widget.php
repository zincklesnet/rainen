<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Required by a fixed path in functions.php; renaming would break that out-of-scope reference.

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * The Sitewide Activity Widget Class
 */
class BP_REIGN_ACTIVITY_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct( false, $name = esc_html__( 'Reign - Site Wide Activity', 'reign' ) );

		if ( is_customize_preview() || is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'bp_enqueue_scripts', array( $this, 'reign_enqueue_scripts' ) );
		}
	}

	/**
	 * Load Js on front end
	 */
	public function reign_enqueue_scripts() {
		// Reserved for future front-end asset enqueue for this widget.
	}

	public function widget( $args, $instance ) {

		if ( isset( $instance['is_personal'] ) ) {
			if ( 'yes' === $instance['is_personal'] && ! is_user_logged_in() ) {
				return; // do  not show anything if the widget is set to be displayed for the logged in users activity only
			}
		}

		$included_components = reign_normalize_activity_components(
			isset( $instance['included_components'] ) ? $instance['included_components'] : array()
		);
		$excluded_components = reign_normalize_activity_components(
			isset( $instance['excluded_components'] ) ? $instance['excluded_components'] : array()
		);

		if ( empty( $included_components ) ) {
			$included_components = reign_get_recorded_components();
		}

		// let us assume that the scope is selected components
		$scope = $included_components;

		// if the user has excluded some of the components , let us remove it from scope
		if ( ! empty( $scope ) ) {
			$scope = array_diff( $scope, $excluded_components );
		}

		$scope                      = implode( ',', $scope );
		$included_components_string = implode( ',', $included_components );
		$excluded_components_string = implode( ',', $excluded_components );

		// find scope
		if ( isset( $args['before_widget'] ) ) {
			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$safe_title = isset( $instance['title'] ) && '' !== $instance['title'] ? $instance['title'] : esc_html__( 'Site Wide Activities', 'reign' );
		if ( isset( $args['before_title'] ) ) {
			echo $args['before_title'] . $safe_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo $safe_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( isset( $instance['show_feed_link'] ) ) {
			if ( 'yes' === $instance['show_feed_link'] ) {
				echo ' <a class="reign-rss" href="' . bp_get_sitewide_activity_feed_link() . '" title="' . esc_html__( 'Sitewide Activity RSS Feed', 'reign' ) . '">' . __( '[RSS]', 'reign' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( isset( $args['after_title'] ) ) {
			echo $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$activity_args = $instance;

		$activity_args['page']  = 1;
		$activity_args['scope'] = $scope;
		$activity_args['max']   = isset( $instance['max_items'] ) ? $instance['max_items'] : '';

		$activity_args['show_filters'] = isset( $instance['show_activity_filters'] ) ? $instance['show_activity_filters'] : '';

		$activity_args['included'] = $included_components_string;
		$activity_args['excluded'] = $excluded_components_string;
		// is_personal, is_blog_admin activity etc are set in the

		bp_reign_list_activities( $activity_args );

		$max_item               = isset( $instance['max_items'] ) ? $instance['max_items'] : '';
		$per_page               = isset( $instance['per_page'] ) ? $instance['per_page'] : '';
		$show_avatar            = isset( $instance['show_avatar'] ) ? $instance['show_avatar'] : '';
		$show_activity_content  = isset( $instance['show_activity_content'] ) ? $instance['show_activity_content'] : '';
		$show_activity_filters  = isset( $instance['show_activity_filters'] ) ? $instance['show_activity_filters'] : '';
		$is_personal            = isset( $instance['is_personal'] ) ? $instance['is_personal'] : '';
		$is_blog_admin_activity = isset( $instance['is_blog_admin_activity'] ) ? $instance['is_blog_admin_activity'] : '';
		$activity_words_count   = isset( $instance['activity_words_count'] ) ? $instance['activity_words_count'] : '';
		?>
		<div class="aligncenter site-activity-view-all-button">
			<a class="rg-action button large" href="<?php echo esc_url( home_url( '/' ) . bp_get_activity_slug() ); ?>">
				<?php esc_html_e( 'View All', 'reign' ); ?>
			</a>
		</div>
		<input type='hidden' name='reign_activity_nonce' id='reign_activity_nonce' value="<?php echo esc_attr( wp_create_nonce( 'reign_activity' ) ); ?>" />
		<input type='hidden' name='max' id='reign_max_items' value="<?php echo esc_attr( $max_item ); ?>" />
		<input type='hidden' name='max' id='reign_per_page' value="<?php echo esc_attr( $per_page ); ?>" />
		<input type='hidden' name='show_avatar' id='reign_show_avatar' value="<?php echo esc_attr( $show_avatar ); ?>" />
		<input type='hidden' name='show_content' id='reign_show_content' value="<?php echo esc_attr( $show_activity_content ); ?>" />
		<input type='hidden' name='show_filters' id='reign_show_filters' value="<?php echo esc_attr( $show_activity_filters ); ?>" />
		<input type='hidden' name='included_components' id='reign_included_components' value="<?php echo esc_attr( $included_components_string ); ?>" />
		<input type='hidden' name='excluded_components' id='reign_excluded_components' value="<?php echo esc_attr( $excluded_components_string ); ?>" />
		<input type='hidden' name='is_personal' id='reign_is_personal' value="<?php echo esc_attr( $is_personal ); ?>" />
		<input type='hidden' name='is_blog_admin_activity' id='reign_is_blog_admin_activity' value="<?php echo esc_attr( $is_blog_admin_activity ); ?>" />
		<!-- <input type='hidden' name='show_post_form' id='reign_show_post_form' value="<?php // echo esc_attr( $instance['show_post_form'] ); ?>" /> -->
		<input type='hidden' name='reign_scope' id='reign_scope' value="<?php echo esc_attr( $scope ); ?>" />
		<input type='hidden' name='reign-original-scope' id='reign-original-scope' value="<?php echo esc_attr( $scope ); ?>" />
		<input type='hidden' name='reign-activity-words-count' id='reign-activity-words-count' value="<?php echo esc_attr( $activity_words_count ); ?>" />
		<!-- <input type='hidden' name='reign-activity-allow-comment' id='reign-activity-allow-comment' value="<?php // echo esc_attr( $instance['allow_comment'] ); ?>" /> -->

		<?php echo isset( $args['after_widget'] ) ? $args['after_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                          = $old_instance;
		$instance['title']                 = sanitize_text_field( $new_instance['title'] );
		$instance['max_items']             = absint( $new_instance['max_items'] );
		$instance['per_page']              = absint( $new_instance['per_page'] );
		$instance['show_avatar']           = sanitize_text_field( $new_instance['show_avatar'] ); // avatar should be visible or not.
		$instance['allow_reply']           = isset( $new_instance['allow_reply'] ) ? sanitize_text_field( $new_instance['allow_reply'] ) : 'no'; // allow reply inside widget or not.
		$instance['show_feed_link']        = isset( $new_instance['show_feed_link'] ) ? sanitize_text_field( $new_instance['show_feed_link'] ) : 'yes'; // feed link should be visible or not.
		$instance['show_activity_filters'] = isset( $new_instance['show_activity_filters'] ) ? sanitize_text_field( $new_instance['show_activity_filters'] ) : 'yes'; // activity filters should be visible or not.
		// $instance[ 'show_post_form' ]        = $new_instance[ 'show_post_form' ]; //should we show the post form or not.
		$instance['show_activity_content'] = isset( $new_instance['show_activity_content'] ) ? absint( $new_instance['show_activity_content'] ) : 0;
		// $instance[ 'allow_comment' ]       = absint( $new_instance[ 'allow_comment' ] );
		$instance['included_components']    = reign_normalize_activity_components(
			isset( $new_instance['included_components'] ) ? $new_instance['included_components'] : array()
		);
		$instance['excluded_components']    = reign_normalize_activity_components(
			isset( $new_instance['excluded_components'] ) ? $new_instance['excluded_components'] : array()
		);
		$instance['is_blog_admin_activity'] = isset( $new_instance['is_blog_admin_activity'] ) ? sanitize_text_field( $new_instance['is_blog_admin_activity'] ) : 'no';
		$instance['is_personal']            = isset( $new_instance['is_personal'] ) ? sanitize_text_field( $new_instance['is_personal'] ) : 'no';
		$instance['activity_words_count']   = absint( $new_instance['activity_words_count'] );
		return $instance;
	}

	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'                  => esc_html__( 'Site Wide Activities', 'reign' ),
				'max_items'              => 200,
				'per_page'               => 25,
				'is_personal'            => 'no',
				'is_blog_admin_activity' => 'no',
				'show_avatar'            => 'yes',
				'show_activity_content'  => 0,
				// 'allow_comment'            => 0,
				'show_feed_link'         => 'yes',
				// 'show_post_form'       => 'no',
				'allow_reply'            => 'no',
				'show_activity_filters'  => 'yes',
				'included_components'    => false,
				'excluded_components'    => false,
				'activity_words_count'   => 0,
			)
		);

		$per_page            = absint( $instance['per_page'] );
		$max_items           = absint( $instance['max_items'] );
		$title               = strip_tags( $instance['title'] );
		$included_components = reign_normalize_activity_components( $instance['included_components'] );
		$excluded_components = reign_normalize_activity_components( $instance['excluded_components'] );

		// extract( $instance );
		?>

		<div class="reign-widget-block">
			<p><label for="bp-reign-title"><strong><?php esc_html_e( 'Title:', 'reign' ); ?> </strong><input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>
			<p><label for="bp-reign-per-page"><?php esc_html_e( 'Number of items Per Page:', 'reign' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'per_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'per_page' ) ); ?>" type="text" value="<?php echo esc_attr( $per_page ); ?>" style="width: 30%" /></label></p>
			<p><label for="bp-reign-max"><?php esc_html_e( 'Max items to show:', 'reign' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max_items' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_items' ) ); ?>" type="text" value="<?php echo esc_attr( $max_items ); ?>" style="width: 30%" /></label></p>
		</div>
		<div class="reign-widget-block">
			<p>
				<label for="bp-reign-is-personal"><strong><?php esc_html_e( "Limit to Logged In user's activity:", 'reign' ); ?></strong>
					<label for="<?php echo esc_attr( $this->get_field_id( 'is_personal' ) ); ?>_yes" > <input id="<?php echo esc_attr( $this->get_field_id( 'is_personal' ) ); ?>_yes" name="<?php echo esc_attr( $this->get_field_name( 'is_personal' ) ); ?>" type="radio" <?php checked( $instance['is_personal'], 'yes' ); ?> value="yes" /><?php esc_html_e( 'Yes', 'reign' ); ?></label>
					<label for="<?php echo esc_attr( $this->get_field_id( 'is_personal' ) ); ?>_no" > <input  id="<?php echo esc_attr( $this->get_field_id( 'is_personal' ) ); ?>_no" name="<?php echo esc_attr( $this->get_field_name( 'is_personal' ) ); ?>" type="radio" <?php checked( $instance['is_personal'], 'no' ); ?> value="no"  /><?php esc_html_e( 'No', 'reign' ); ?></label>

				</label>
			</p>
			<p>
				<label for="bp-reign-is-blog-admin-activity"><strong><?php esc_html_e( 'List My Activity Only:', 'reign' ); ?></strong>
					<label for="<?php echo esc_attr( $this->get_field_id( 'is_blog_admin_activity' ) ); ?>_yes" > <input id="<?php echo esc_attr( $this->get_field_id( 'is_blog_admin_activity' ) ); ?>_yes" name="<?php echo esc_attr( $this->get_field_name( 'is_blog_admin_activity' ) ); ?>" type="radio" <?php checked( $instance['is_blog_admin_activity'], 'yes' ); ?> value="yes"  /><?php esc_html_e( 'Yes', 'reign' ); ?></label>
					<label for="<?php echo esc_attr( $this->get_field_id( 'is_blog_admin_activity' ) ); ?>_no" > <input  id="<?php echo esc_attr( $this->get_field_id( 'is_blog_admin_activity' ) ); ?>_no" name="<?php echo esc_attr( $this->get_field_name( 'is_blog_admin_activity' ) ); ?>" type="radio" <?php checked( $instance['is_blog_admin_activity'], 'no' ); ?> value="no"  /><?php esc_html_e( 'No', 'reign' ); ?></label>
				</label>
			</p>
		</div>
		<div class="reign-widget-block">
			<p>
				<label for="bp-reign-show-avatar"><strong><?php esc_html_e( 'Show Avatar:', 'reign' ); ?></strong>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_avatar' ) ); ?>_yes" > <input id="<?php echo esc_attr( $this->get_field_id( 'show_avatar' ) ); ?>_yes" name="<?php echo esc_attr( $this->get_field_name( 'show_avatar' ) ); ?>" type="radio" <?php checked( $instance['show_avatar'], 'yes' ); ?> value="yes"  /><?php esc_html_e( 'Yes', 'reign' ); ?></label>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_avatar' ) ); ?>_no" > <input  id="<?php echo esc_attr( $this->get_field_id( 'show_avatar' ) ); ?>_no" name="<?php echo esc_attr( $this->get_field_name( 'show_avatar' ) ); ?>" type="radio" <?php checked( $instance['show_avatar'], 'no' ); ?> value="no"  /><?php esc_html_e( 'No', 'reign' ); ?></label>
				</label>
			</p>
			<p>
				<label for="bp-reign-show-feed-link"><?php esc_html_e( 'Show Feed Link:', 'reign' ); ?>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_feed_link' ) ); ?>_yes" > <input id="<?php echo esc_attr( $this->get_field_id( 'show_feed_link' ) ); ?>_yes" name="<?php echo esc_attr( $this->get_field_name( 'show_feed_link' ) ); ?>" type="radio" <?php checked( $instance['show_feed_link'], 'yes' ); ?> value="yes"  /><?php esc_html_e( 'Yes', 'reign' ); ?></label>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_feed_link' ) ); ?>_no" > <input  id="<?php echo esc_attr( $this->get_field_id( 'show_feed_link' ) ); ?>_no" name="<?php echo esc_attr( $this->get_field_name( 'show_feed_link' ) ); ?>" type="radio" <?php checked( $instance['show_feed_link'], 'no' ); ?> value="no"  /><?php esc_html_e( 'No', 'reign' ); ?></label>
				</label>
			</p>
			<p>
				<label for="bp-reign-show-activity-content"><?php esc_html_e( 'Show Activity Content:', 'reign' ); ?>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_activity_content' ) ); ?>_yes" > <input id="<?php echo esc_attr( $this->get_field_id( 'show_activity_content' ) ); ?>_yes" name="<?php echo esc_attr( $this->get_field_name( 'show_activity_content' ) ); ?>" type="radio" <?php echo checked( $instance['show_activity_content'], 1 ); ?> value="1"  /><?php esc_html_e( 'Yes', 'reign' ); ?></label>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_activity_content' ) ); ?>_no" > <input  id="<?php echo esc_attr( $this->get_field_id( 'show_activity_content' ) ); ?>_no" name="<?php echo esc_attr( $this->get_field_name( 'show_activity_content' ) ); ?>" type="radio" <?php echo checked( $instance['show_activity_content'], 0 ); ?> value="0" /><?php esc_html_e( 'No', 'reign' ); ?></label>
				</label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'activity_words_count' ) ); ?>"><?php esc_html_e( 'Limit activity content to:', 'reign' ); ?>
					<input id="<?php echo esc_attr( $this->get_field_id( 'activity_words_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'activity_words_count' ) ); ?>" type="text" value="<?php echo absint( $instance['activity_words_count'] ); ?>" class="widefat" /> <?php esc_html_e( 'words. Zero means no limit.', 'reign' ); ?>
				</label>
			</p>

			<?php
			/*
			<p>
				<label for="bp-reign-show-post-form"><strong><?php // esc_html_e( 'Show Post Form', 'reign' ); ?></strong>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_post_form' ) ); ?>_yes" > <input id="<?php echo esc_attr( $this->get_field_id( 'show_post_form' ) ); ?>_yes" name="<?php echo esc_attr( $this->get_field_name( 'show_post_form' ) ); ?>" type="radio" <?php checked( $instance['show_post_form'], 'yes' ); ?> value="yes"  /><?php esc_html_e( 'Yes', 'reign' ); ?></label>
					<label for="<?php echo $this->get_field_id( 'show_post_form' ); ?>_no" > <input  id="<?php echo $this->get_field_id( 'show_post_form' ); ?>_no" name="<?php echo $this->get_field_name( 'show_post_form' ); ?>" type="radio" <?php checked( $instance['show_post_form'], 'no' ); ?> value="no"  /><?php esc_html_e( 'No', 'reign' ); ?></label>

				</label>
			</p>
			<p>
				<label for="bp-reign-show-activity-content"><?php esc_html_e( 'Show/Allow Activity Replies:', 'reign' ); ?>
					<label for="<?php echo esc_attr( $this->get_field_id( 'allow_comment' ) ); ?>_yes" > <input id="<?php echo esc_attr( $this->get_field_id( 'allow_comment' ) ); ?>_yes" name="<?php echo esc_attr( $this->get_field_name( 'allow_comment' ) ); ?>" type="radio" <?php echo checked( $instance['allow_comment'], 1 ); ?> value="1"  /><?php esc_html_e( 'Yes', 'reign' ); ?></label>
					<label for="<?php echo esc_attr( $this->get_field_id( 'allow_comment' ) ); ?>_no" > <input  id="<?php echo esc_attr( $this->get_field_id( 'allow_comment' ) ); ?>_no" name="<?php echo esc_attr( $this->get_field_name( 'allow_comment' ) ); ?>" type="radio" <?php echo checked( $instance['allow_comment'], 0 ); ?> value="0" /><?php esc_html_e( 'No', 'reign' ); ?></label>
				</label>
			</p>
			*/
			?>

			<p>
				<label for="bp-reign-show-activity-filters"><strong><?php esc_html_e( 'Show Activity Filters:', 'reign' ); ?></strong>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_activity_filters' ) ); ?>_yes" > <input id="<?php echo esc_attr( $this->get_field_id( 'show_activity_filters' ) ); ?>_yes" name="<?php echo esc_attr( $this->get_field_name( 'show_activity_filters' ) ); ?>" type="radio" <?php checked( $instance['show_activity_filters'], 'yes' ); ?> value="yes" /><?php esc_html_e( 'Yes', 'reign' ); ?></label>
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_activity_filters' ) ); ?>_no" > <input  id="<?php echo esc_attr( $this->get_field_id( 'show_activity_filters' ) ); ?>_no" name="<?php echo esc_attr( $this->get_field_name( 'show_activity_filters' ) ); ?>" type="radio" <?php checked( $instance['show_activity_filters'], 'no' ); ?> value="no"  /><?php esc_html_e( 'No', 'reign' ); ?></label>
				</label>
			</p>

		</div>
		<div class="reign-widget-block">
			<p><label for="bp-reign-included-filters"><strong><?php esc_html_e( 'Include only following Filters:', 'reign' ); ?></strong></label></p>
			<p>
				<?php $recorded_components = reign_get_recorded_components(); ?>
				<?php foreach ( (array) $recorded_components as $component ) : ?>
					<label for="<?php echo esc_attr( $this->get_field_id( 'included_components' ) . '_' . $component ); ?>" ><?php echo esc_html( ucwords( $component ) ); ?> <input id="<?php echo esc_attr( $this->get_field_id( 'included_components' ) . '_' . $component ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'included_components' ) ); ?>[]" type="checkbox"
											<?php
											if ( is_array( $included_components ) && in_array( $component, $included_components ) ) {
												echo "checked='checked'";}
											?>
					value="<?php echo esc_attr( $component ); ?>" /></label>
				<?php endforeach; ?>
			</p>
		</div>
		<div class="reign-widget-block">

			<p><label for="bp-reign-included-filters"><strong><?php esc_html_e( 'Exclude following Components activity', 'reign' ); ?></strong></label></p>
			<p>
				<?php $recorded_components = BP_Activity_Activity::get_recorded_components(); ?>
				<?php foreach ( (array) $recorded_components as $component ) : ?>
					<label for="<?php echo esc_attr( $this->get_field_id( 'excluded_components' ) . '_' . $component ); ?>" ><?php echo esc_html( ucwords( $component ) ); ?> <input id="<?php echo esc_attr( $this->get_field_id( 'excluded_components' ) . '_' . $component ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'excluded_components' ) ); ?>[]" type="checkbox"
											<?php
											if ( is_array( $excluded_components ) && in_array( $component, $excluded_components ) ) {
												echo "checked='checked'";}
											?>
					value="<?php echo esc_attr( $component ); ?>"  /></label>
				<?php endforeach; ?>
			</p>
		</div>
		<?php
	}
}

// end of class

/**
 * Register the widget
 */
// phpcs:ignore Universal.Files.SeparateFunctionsFromOO.Mixed -- Widget class is paired with its registration + render helpers in one file by design.
function reign_register_widget() {
	if ( bp_is_active( 'activity' ) ) {
		register_widget( 'BP_REIGN_ACTIVITY_Widget' );
	}
}

add_action( 'bp_widgets_init', 'reign_register_widget' );

// ajax action handling for the filters(blogs/profile/groups).
function reign_ajax_list_activity() {
	// Nonce check: mandatory. Accept either the default _wpnonce or the explicit reign_activity_nonce field.
	if ( isset( $_POST['reign_activity_nonce'] ) ) {
		check_ajax_referer( 'reign_activity', 'reign_activity_nonce' );
	} else {
		check_ajax_referer( 'reign_activity' );
	}
	$page = isset( $_POST['page'] ) ? absint( wp_unslash( $_POST['page'] ) ) : 1;

	$scope = isset( $_POST['scope'] ) ? sanitize_text_field( wp_unslash( $_POST['scope'] ) ) : '';

	$per_page = isset( $_POST['per_page'] ) ? absint( wp_unslash( $_POST['per_page'] ) ) : 10;

	$max = isset( $_POST['max'] ) ? absint( wp_unslash( $_POST['max'] ) ) : 200;

	$show_avatar = isset( $_POST['show_avatar'] ) ? sanitize_text_field( wp_unslash( $_POST['show_avatar'] ) ) : 1;

	$show_filters = isset( $_POST['show_filters'] ) ? sanitize_text_field( wp_unslash( $_POST['show_filters'] ) ) : 1;

	$show_content = isset( $_POST['show_content'] ) ? sanitize_text_field( wp_unslash( $_POST['show_content'] ) ) : 1;

	$activity_words_count = isset( $_POST['activity_words_count'] ) ? sanitize_text_field( wp_unslash( $_POST['activity_words_count'] ) ) : 0;

	$included = isset( $_POST['included_components'] ) ? sanitize_text_field( wp_unslash( $_POST['included_components'] ) ) : false;

	$excluded = isset( $_POST['excluded_components'] ) ? sanitize_text_field( wp_unslash( $_POST['excluded_components'] ) ) : false;

	$is_personal = isset( $_POST['is_personal'] ) ? sanitize_text_field( wp_unslash( $_POST['is_personal'] ) ) : 0;

	$is_blog_admin_activity = isset( $_POST['is_blog_admin_activity'] ) ? sanitize_text_field( wp_unslash( $_POST['is_blog_admin_activity'] ) ) : 0;

	// $show_post_form         = sanitize_text_field( $_POST['show_post_form'] );
	// $show_post_form           = isset( $show_post_form ) ? $show_post_form : 0;
	// $allow_comment          = sanitize_text_field( $_POST['allow_comment'] );
	// $allow_comment            = isset( $allow_comment ) ? absint( $allow_comment ) : 0;
	// $show_filters=true,$included=false,$excluded=false
	bp_reign_list_activities(
		array(
			'per_page'               => $per_page,
			'page'                   => $page,
			'scope'                  => $scope,
			'max'                    => $max,
			'show_avatar'            => $show_avatar,
			'show_filters'           => $show_filters,
			'included'               => $included,
			'excluded'               => $excluded,
			'is_personal'            => $is_personal,
			'is_blog_admin_activity' => $is_blog_admin_activity,
			'show_activity_content'  => $show_content,
			'activity_words_count'   => $activity_words_count,
		// 'show_post_form'       => $show_post_form,
		// 'allow_comment'            => $allow_comment,
		)
	);

	exit( 0 );
}

add_action( 'wp_ajax_reign_fetch_content', 'reign_ajax_list_activity' );
add_action( 'wp_ajax_nopriv_reign_fetch_content', 'reign_ajax_list_activity' );

/**
 * Display Filterable links for activity.
 *
 * based on bp_activity_filter_link
 *
 * @param array $args
 */
function reign_activity_filter_links( $args = array() ) {
	echo reign_get_activity_filter_links( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Get filter links for activity.
 *
 * @param array $args
 * @return bool|mixed|void
 */
function reign_get_activity_filter_links( $args = array() ) {

	$link     = '';
	$defaults = array(
		'style'  => 'list',
		'before' => '<li>',
		'after'  => '</li>',
	);
	// check scope, if not single entry

	$r      = wp_parse_args( $args, $defaults );
	$before = $r['before'];
	$after  = $r['after'];

	$components = reign_get_base_component_scope( $args['include'], $args['exclude'] );

	if ( ! $components ) {
		return false;
	}

	foreach ( (array) $components as $key => $component ) {
		/* Skip the activity comment filter */
		if ( 'activity' == $component ) {
			continue;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only activity-filter state from the URL; output fully sanitized.
		if ( isset( $_GET['afilter'] ) && sanitize_text_field( wp_unslash( $_GET['afilter'] ) ) == $component ) {
			$selected = ' class="selected"';
		} else {
			$selected = '';
			if ( 0 === $key ) {
				$selected = ' class="selected"';
			}
		}

		$component = esc_attr( $component );

		switch ( $r['style'] ) {
			case 'list':
				$tag    = 'li';
				$before = '<li id="afilter-' . $component . '"' . $selected . '>';
				$after  = '</li>';
				break;
			case 'paragraph':
				$tag    = 'p';
				$before = '<p id="afilter-' . $component . '"' . $selected . '>';
				$after  = '</p>';
				break;
			case 'span':
				$tag    = 'span';
				$before = '<span id="afilter-' . $component . '"' . $selected . '>';
				$after  = '</span>';
				break;
		}

		$link = add_query_arg( 'afilter', $component );
		$link = esc_url( remove_query_arg( 'acpage', $link ) );

		$link = apply_filters( 'bp_get_activity_filter_link_href', $link, $component );

		/* Make sure all core internal component names are translatable */
		$translatable_components = array(
			'profile'  => __( 'Profile', 'reign' ),
			'xprofile' => __( 'Profile', 'reign' ),
			'friends'  => __( 'Friends', 'reign' ),
			'groups'   => __( 'Groups', 'reign' ),
			'status'   => __( 'Status', 'reign' ),
			'blogs'    => __( 'Blogs', 'reign' ),
			'forums'   => __( 'Forums', 'reign' ),
			'bbpress'  => __( 'Forums', 'reign' ),
		);

		$label = isset( $translatable_components[ $component ] ) ? $translatable_components[ $component ] : ucwords( $component );

		// $component_links[] = $r[ 'before' ] . '<a href="' . esc_attr( $link ) . '">' . $label . '</a>' . $r[ 'after' ];
		$component_links[] = $before . '<a href="' . esc_attr( $link ) . '">' . $label . '</a>' . $after;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public activity-feed scope parameter; output fully sanitized.
	if ( ! empty( $_REQUEST['original_scope'] ) ) {
		$scope = sanitize_text_field( wp_unslash( $_REQUEST['original_scope'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public activity-feed scope parameter; output fully sanitized.
	}
	if ( ! empty( $scope ) && reign_scope_has_changed( $scope ) ) {

		$link = esc_url( remove_query_arg( 'afilter', $link ) );
		$link = $link . '?afilter=';

		// commented previously
		// $component_links[] = "<{$tag} id='afilter-clear'><a href='" . esc_attr( $link ) . "'>" . __( 'Clear Filter', 'reign' ) . "</a></{$tag}>";
		// commented on 20-08-2019 activity filter clear
		// $component_links[] = "<{$tag} id='afilter-clear'><a href='" . esc_attr( $link ) . "' title='" . __( 'Clear Filter', 'reign' ) . "'><i class='fa fa-times'></i></a></{$tag}>";
	}

	if ( ! empty( $component_links ) ) {
		return apply_filters( 'wbcom_get_activity_filter_links', implode( "\n", $component_links ), $component_links );
	}

	return false;
}

/**
 * List Activities
 */
function bp_reign_list_activities( $args ) {

	$defaults = array(
		'per_page'               => 10,
		'page'                   => 1,
		'scope'                  => '',
		'max'                    => 20,
		'show_avatar'            => 'yes',
		'show_filters'           => 'yes',
		'included'               => false,
		'excluded'               => false,
		'is_personal'            => 'no',
		'is_blog_admin_activity' => 'no',
	// 'show_post_form'       => 'no',
	// 'allow_comment'            => 0,
	);

	$args = wp_parse_args( $args, $defaults );
	// extract( $args );
	// check for the scope of activity
	// is it the activity of logged in user/blog admin
	// logged in user over rides blog admin

	$bp = buddypress();

	$primary_id = '';

	if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
		$primary_id = null;
	}

	$user_id = false; // for limiting to users

	if ( 'yes' === $args['is_personal'] ) {
		$user_id = get_current_user_id();
	} elseif ( 'yes' === $args['is_blog_admin_activity'] ) {
		$user_id = reign_get_blog_admin_id();
	} elseif ( bp_is_user() ) {
		$user_id = null;
	}

	$components_scope = reign_get_base_component_scope( $args['included'], $args['excluded'] );

	$components_base_scope = '';

	if ( ! empty( $components_scope ) ) {
		$components_base_scope = join( ',', $components_scope );
	}
	?>

	<div class='reign-wrap'>

		<?php if ( 'yes' === $args['show_filters'] ) : ?>

			<ul id="activity-filter-links" class="reign-clearfix">
				<?php
				$filter_args = array(
					'scope'   => $args['scope'],
					'include' => $args['included'],
					'exclude' => $args['excluded'],
				);

				reign_activity_filter_links( $filter_args );
				?>
			</ul>

			<div class="clear"></div>

		<?php endif; ?>
		<?php
		$params = array(
			'type'        => 'sitewide',
			'max'         => $args['max'],
			'page'        => $args['page'],
			'per_page'    => $args['per_page'],
			'object'      => $args['scope'],
			'user_id'     => $user_id,
			'primary_id'  => $primary_id,
			'scope'       => 0,
			'count_total' => true, // always count total
		);
		?>
		<?php if ( bp_has_activities( $params ) ) : ?>

			<div class="reign-pagination reign-clearfix">
				<div class="pag-count" id="activity-count">
					<?php bp_activity_pagination_count(); ?>
				</div>

				<div class="pagination-links" id="activity-pag">
					&nbsp; <?php bp_activity_pagination_links(); ?>
				</div>
				<div class="clear" ></div>
			</div>

			<div class="clear" ></div>

			<ul  class="site-wide-stream reign-activity-list reign-clearfix">

				<?php
				while ( bp_activities() ) :
					bp_the_activity();
					?>
					<?php reign_activity_entry( $args ); ?>
				<?php endwhile; ?>

			</ul>

		<?php else : ?>

			<?php
			if ( 'yes' === $args['is_personal'] ) {
				/* translators: %s: activity scope */
				$empty_msg = sprintf( __( 'You have no recent %s activity.', 'reign' ), $scope );
			} else {
				$empty_msg = __( 'There has been no recent site activity.', 'reign' );
			}
			$activity_url = function_exists( 'bp_get_activity_directory_permalink' ) ? bp_get_activity_directory_permalink() : '';
			reign_render_empty_state(
				'<svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
				$empty_msg,
				$activity_url,
				__( 'View Activity', 'reign' )
			);
			?>

		<?php endif; ?>
	</div>

	<?php
}

/**
 * Individual activity entry
 *
 * @param array $args
 */
function reign_activity_entry( $args ) {

	$args = wp_parse_args(
		$args,
		array(
			'allow_comment' => false,
		)
	);

	$show_avatar           = isset( $args['show_avatar'] ) ? $args['show_avatar'] : '';
	$show_activity_content = isset( $args['show_activity_content'] ) ? $args['show_activity_content'] : '';
	// $args['allow_comment'] = false;//we can provide an option in future to allow commenting
	$args['show_activity_content'] = absint( $show_activity_content );
	?>

	<?php do_action( 'bp_before_activity_entry' ); ?>

	<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>">

		<?php if ( ! empty( $show_avatar ) && 'yes' === $show_avatar ) : ?>

			<div class="reign-activity-avatar">
				<a href="<?php bp_activity_user_link(); ?>">
					<?php bp_activity_avatar( 'type=thumb&width=50&height=50' ); ?>
				</a>

			</div>

		<?php endif; ?>

		<div class="reign-activity-content reign-clearfix">

			<div class="reign-activity-header">
				<?php bp_activity_action(); ?>
			</div>

			<?php if ( bp_activity_has_content() && ! empty( $args['show_activity_content'] ) ) : ?>

				<div class="reign-activity-inner">
					<?php reign_activity_content_body( $args['activity_words_count'] ); ?>
				</div>

			<?php endif; ?>

			<?php do_action( 'bp_activity_entry_content' ); ?>

			<div class="clear" ></div>
		</div>
	</li>
	<?php do_action( 'bp_after_reign_activity_entry' ); ?>

	<?php
}

/**
 * Get an array of recorded components
 *
 * @return array
 */
function reign_get_recorded_components() {

	$components = BP_Activity_Activity::get_recorded_components();

	return array_diff( (array) $components, array( 'members' ) );
}

/**
 * Normalize activity component selections from widget settings or requests.
 *
 * @param mixed $components Component selection as an array or comma-separated string.
 * @return array
 */
function reign_normalize_activity_components( $components ) {
	$normalized = array();

	foreach ( wp_parse_list( $components ) as $component ) {
		if ( ! is_scalar( $component ) ) {
			continue;
		}

		$component = sanitize_key( wp_unslash( (string) $component ) );

		if ( '' !== $component ) {
			$normalized[] = $component;
		}
	}

	return array_values( array_unique( $normalized ) );
}

/**
 * Get an array of recorded components which contain $include and do not contain the components from $exclude
 *
 * @param array $include
 * @param array $exclude
 * @return array
 */
function reign_get_base_component_scope( $include, $exclude ) {
	$components = reign_get_recorded_components();

	if ( ! empty( $include ) ) {
		$components = explode( ',', $include ); // array of component names.
	}

	if ( ! empty( $exclude ) ) {  // exclude all the
		$components = array_diff( (array) $components, explode( ',', $exclude ) ); // diff of exclude/recorded components.
	}

	return $components;
}

/**
 * Check if the given request has scope changed?
 *
 * @param $new_scopes
 *
 * @return bool
 */
function reign_scope_has_changed( $new_scopes ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public activity-feed scope parameter; output fully sanitized.
	if ( isset( $_REQUEST['original_scope'] ) ) {
		$old_scope = sanitize_text_field( wp_unslash( $_REQUEST['original_scope'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public activity-feed scope parameter; output fully sanitized.
		if ( ! $old_scope ) {
			return false;
		}

		if ( $old_scope == $new_scopes ) {
			return false;
		}
	}

	return true;
}

/**
 * Output the content body of an activity.
 *
 * @param int $word_count how may words to limit.
 */
function reign_activity_content_body( $word_count = 0 ) {

	if ( ! $word_count ) {
		echo bp_get_activity_content_body(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	$content = strip_tags( strip_shortcodes( bp_get_activity_content_body() ) );

	$content = wp_trim_words( $content, $word_count );

	echo wpautop( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Get the Id of the admin(Any one of the admin) of current blog
 *
 * @return int
 */
function reign_get_blog_admin_id() {

	$blog_id = get_current_blog_id();
	$users   = get_admin_users_for_blog( $blog_id );

	if ( ! empty( $users ) ) {
		$users = $users[0]; // just the first user
	}

	return $users;
}

/**
 * Get all admin users of the given blog
 *
 * @param int $blog_id
 *
 * @return array of user IDs
 */
function get_admin_users_for_blog( $blog_id ) {

	$users = get_users(
		array(
			'role'    => 'administrator',
			'blog_id' => $blog_id,
			'fields'  => 'ID',
		)
	);

	return $users;
}

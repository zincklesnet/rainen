<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Required by a fixed path in functions.php; renaming would break that out-of-scope reference.
/**
 * BuddyPress Login Widget.
 *
 * @package BuddyPress
 * @subpackage Login Widget
 * @since 5.6.6
 */
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Login widget.
 *
 * @since 5.6.6
 */

class BP_REIGN_BP_Login_Widget extends WP_Widget {

	/**
	 * Working as a group, we get things done better.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => esc_html__( 'Display BuddyPress login widget.', 'reign' ),
			'classname'                   => 'widget_rign_bp_login_widget buddypress widget',
			'customize_selective_refresh' => true,
		);
		parent::__construct( false, esc_html_x( 'Reign - Login', 'widget name', 'reign' ), $widget_ops );
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

		$defaults           = array(
			'title'              => esc_html__( 'Login', 'reign' ),
			'login_redirect'     => 'current',
			'login_redirect_url' => '',
			'login_description'  => '',
		);
		$instance           = wp_parse_args( (array) $instance, $defaults );
		$title              = isset( $instance['title'] ) ? $instance['title'] : '';
		$login_redirect     = isset( $instance['login_redirect'] ) ? $instance['login_redirect'] : 'current';
		$login_redirect_url = isset( $instance['login_redirect_url'] ) ? $instance['login_redirect_url'] : '';
		$login_description  = isset( $instance['login_description'] ) ? $instance['login_description'] : '';
		$rand               = rand( 1000, 9999 );
		echo isset( $args['before_widget'] ) ? $args['before_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped		

		if ( '' === $login_description ) {
			global $wbtm_reign_settings;
			$registration_page_url = wp_registration_url();
			if ( isset( $wbtm_reign_settings['reign_pages']['reign_register_page'] ) && ( '-1' !== $wbtm_reign_settings['reign_pages']['reign_register_page'] && '' !== $wbtm_reign_settings['reign_pages']['reign_register_page'] ) ) {
				$registration_page_id  = $wbtm_reign_settings['reign_pages']['reign_register_page'];
				$registration_page_url = get_permalink( $registration_page_id );
			}

			$login_description = sprintf(
				'<p>%s %s %s %s %s</p>',
				esc_html__( 'Don\'t have an account?', 'reign' ),
				'<a href="' . $registration_page_url . '" title="' . esc_attr__( 'Register', 'reign' ) . '">',
				esc_html__( 'Register Now!', 'reign' ),
				'</a>',
				esc_html__( 'it\'s simple and you\'ll enjoy all the benefits!', 'reign' )
			);
		}

		$attr = array(
			'forms'                => 'login',
			'login_title'          => $title,
			'redirect'             => $login_redirect,
			'redirect_to'          => $login_redirect_url,
			'login_description'    => $login_description,
			'register_redirect'    => '',
			'register_redirect_to' => '',
			'register_fields_type' => 'simple',
		);
		get_template_part( 'template-parts/form', '', $attr );

		echo isset( $args['after_widget'] ) ? $args['after_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

		$instance['title']              = sanitize_text_field( $new_instance['title'] );
		$instance['login_redirect']     = sanitize_text_field( $new_instance['login_redirect'] );
		$instance['login_redirect_url'] = esc_url_raw( $new_instance['login_redirect_url'] );
		$instance['login_description']  = wp_kses_post( $new_instance['login_description'] );

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
			'title'              => esc_html__( 'Login', 'reign' ),
			'login_redirect'     => 'current',
			'login_redirect_url' => '',
			'login_description'  => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title              = sanitize_text_field( $instance['title'] );
		$login_redirect     = sanitize_text_field( $instance['login_redirect'] );
		$login_redirect_url = esc_url_raw( $instance['login_redirect_url'] );
		$login_description  = wp_kses_post( $instance['login_description'] );
		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'reign' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'login_redirect' ) ); ?>"><?php esc_html_e( 'Login Redirect', 'reign' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'login_redirect' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'login_redirect' ) ); ?>">
				<option value="current" <?php selected( $login_redirect, 'current' ); ?>><?php esc_html_e( 'Current page', 'reign' ); ?></option>
				<option value="profile" <?php selected( $login_redirect, 'profile' ); ?>><?php esc_html_e( 'Profile page', 'reign' ); ?></option>
				<option value="activity"  <?php selected( $login_redirect, 'activity' ); ?>><?php esc_html_e( 'Activity page', 'reign' ); ?></option>
				<option value="custom" <?php selected( $login_redirect, 'custom' ); ?>><?php esc_html_e( 'Custom page', 'reign' ); ?></option>
			</select>
		</p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'login_redirect_url' ) ); ?>"><?php esc_html_e( 'Login Custom URL', 'reign' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'login_redirect_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'login_redirect_url' ) ); ?>" type="text" value="<?php echo esc_attr( $login_redirect_url ); ?>" style="width: 100%" /></label></p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'login_description' ) ); ?>"><?php esc_html_e( 'Login description', 'reign' ); ?></label>
			<textarea class="widefat text " id="<?php echo esc_attr( $this->get_field_id( 'login_description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'login_description' ) ); ?>" rows="6" cols="20"><?php echo esc_html( $login_description ); ?></textarea>				
			
		</p>
		<?php
	}
}

/**
 * Register the widget
 */
// phpcs:ignore Universal.Files.SeparateFunctionsFromOO.Mixed -- Widget class is paired with its registration callback in one file by design.
function reign_register_bp_login_widget() {
	register_widget( 'BP_REIGN_BP_Login_Widget' );
}

add_action( 'bp_widgets_init', 'reign_register_bp_login_widget' );
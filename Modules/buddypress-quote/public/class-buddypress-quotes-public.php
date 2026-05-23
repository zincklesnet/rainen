<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Quotes_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Quotes_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Quotes_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $post;

		$rtl_css = is_rtl() ? '-rtl' : '';

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$css_extension = '.css';
		} else {
			$css_extension = '.min.css';
		}

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css' . $rtl_css . '/buddypress-quotes-public' . $css_extension, array(), $this->version, 'all' );

		wp_register_style( 'bpquotes-slick-css', plugin_dir_url( __FILE__ ) . 'css/vendor/slick.css', array(), $this->version, 'all' );

		if ( ! wp_style_is( 'wb-font-awesome', 'enqueued' ) ) {
			wp_register_style( 'wb-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), $this->version, 'all' );
		}

		wp_register_style( 'wb-icons', plugin_dir_url( __FILE__ ) . 'css' . $rtl_css . '/wb-icons' . $css_extension, array(), $this->version, 'all');

		$current_component = '';
		if ( isset( $post->ID ) && '' !== $post->ID && '0' !== $post->ID ) {
			$_elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
			if ( $_elementor_data != '' && str_contains($_elementor_data, 'bp_newsfeed_element_widget') || str_contains($_elementor_data, 'buddypress_shortcode_activity_widget') || str_contains($_elementor_data, 'bbp-activity')) {
				$current_component = 'activity';
			}
		}
		if ( is_buddypress()
			|| ( $this->bpquotes_check_shortcode('activity-listing') )
			|| ( $this->bpquotes_check_shortcode( 'bppfa_postform' ) )
			|| ( $this->bpquotes_check_shortcode( 'bp_quotes' ) )
			|| ( is_single() && get_post_type() == 'business' )
			|| 'activity' === $current_component ) {

			wp_enqueue_style( $this->plugin_name );

			wp_enqueue_style( 'bpquotes-slick-css' );

			if ( ! wp_style_is( 'wb-font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-font-awesome' );
			}

			if ( ! wp_style_is( 'wb-icons', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-icons' );
			}
		}
	}

	/**
	 * Check if the current page has any shortcode or not
	 *
	 * @param [string] $shortcode
	 * @return void
	 */
	private function bpquotes_check_shortcode( $shortcode ){
		global $post;
		$response = isset( $post->post_content ) && has_shortcode( $post->post_content, $shortcode ) ? true : false;
		return $response;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Quotes_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Quotes_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $post, $allow_user_role;

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$js_extension = '.js';
		} else {
			$js_extension = '.min.js';
		}

		wp_register_script( 'bpquotes-slick-js', plugin_dir_url( __FILE__ ) . 'js/vendor/slick.min.js', array( 'jquery' ), $this->version, true );

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-quotes-public' . $js_extension, array( 'jquery' ), $this->version, true );
		$active_template = get_option( '_bp_theme_package_id' );
		wp_localize_script(
			$this->plugin_name,
			'bpquotes_obj',
			array(
				'active_template' => $active_template,
				'buddyboss'       => buddypress()->buddyboss,
				'quote_text'      => esc_html__( 'Add a quote', 'buddypress-quotes' ),
				'allow_user'      => (!empty($allow_user_role)) ? true : false,
			)
		);

		$current_component = '';
		if ( isset( $post->ID ) && '' !== $post->ID && '0' !== $post->ID ) {
			$_elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
			if ( $_elementor_data != '' && str_contains($_elementor_data, 'bp_newsfeed_element_widget') || str_contains($_elementor_data, 'buddypress_shortcode_activity_widget') || str_contains($_elementor_data, 'bbp-activity')) {
				$current_component = 'activity';
			}
		}

		if ( is_buddypress()
			|| ( $this->bpquotes_check_shortcode('activity-listing') )
			|| ( $this->bpquotes_check_shortcode( 'bppfa_postform' ) )
			|| ( $this->bpquotes_check_shortcode( 'bp_quotes' ) )
			|| ( is_single() && get_post_type() == 'business' )
			|| 'activity' === $current_component ) {

			wp_enqueue_script( 'bpquotes-slick-js' );
			wp_enqueue_script( $this->plugin_name );
		}

	}

	/**
	 * Activity Posts form options
	 */
	public function bpquotes_activity_post_form_options() { 
		do_action( 'bpquotes_activity_post_form_options_before' );
		?>
		<div class="post-elements-buttons-item bp-quote-icon-wrapper">
			<div class='quote-btn bp-tooltip' data-bp-tooltip-pos='up' data-bp-tooltip="<?php esc_attr_e( 'Add a quote', 'buddypress-quotes' ); ?>"><i class='wb-icons wb-icon-quote-left'></i></div>
			<input type='hidden' class='bg-type-input' name='bg-type'>
			<input type='hidden' class='bg-type-value' name='bg-value'>
			<input type='hidden' class='bg-inverted-type-value' name='bg-inverted-value'>
			<input type='hidden' name='bp-quote-add-quote-nonce' value="<?php echo esc_attr( wp_create_nonce( 'bp-quote-add-quote-nonce' ) ); ?>">
		</div>
		<?php
		do_action( 'bpquotes_activity_post_form_options_after' );
	}

	/**
	 * Activity option panle
	 */
	public function bpquotes_activity_post_form_option_panel( $activity = [] ) {
		$bg_type = $bg_value = $bg_inverted_value = '';
		if( !empty( $activity ) ) {
			$activity_id 		= isset( $activity->id ) ? $activity->id : 0;
			$bpquotes_meta 		= bp_activity_get_meta( $activity_id, 'bpquotes_meta', true );
			$bg_type 			= ( isset( $bpquotes_meta['bg-type'] ) ) ? $bpquotes_meta['bg-type'] : '';
			$bg_value 			= ( isset( $bpquotes_meta['bg-value'] ) ) ? $bpquotes_meta['bg-value'] : '';
			$bg_inverted_value 	= ( isset( $bpquotes_meta['bg-inverted-value'] ) ) ? $bpquotes_meta['bg-inverted-value'] : '';
		}
			
		$bpquotes_gnrl_settings = get_option( 'bpquotes_gnrl_settings' );
		$quote_bg_array         = array();
		if ( isset( $bpquotes_gnrl_settings['image_url'] ) && ! empty( $bpquotes_gnrl_settings['image_url'] ) ) {
			foreach ( $bpquotes_gnrl_settings['image_url'] as $key => $url ) {
				$text_color = isset( $bpquotes_gnrl_settings['image_text_color'][$key] ) ? $bpquotes_gnrl_settings['image_text_color'][$key] : '';
				$bpquotes_class = ($bg_value == $url) ? 'bpquotes-active' : '';
				$quote_bg_array[] = '<div class="bpquotes-selection bpquotes-img '. esc_attr($bpquotes_class).'" data-bg-type="quotesimg" data-bg-value="' . esc_url( $url ) . '" data-bg-inverted-value="' . $text_color . '"><img src="' . $url . '"></div>';
			}
		}
		if ( isset( $bpquotes_gnrl_settings['bg_colors'] ) && ! empty( $bpquotes_gnrl_settings['bg_colors'] ) ) {
			foreach ( $bpquotes_gnrl_settings['bg_colors'] as $_key => $color ) {
				$bg_inverted_colors = ( isset( $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] ) && '' !== $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] ) ? $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] : '';
				
				$bpquotes_class = ($bg_value == $color) ? 'bpquotes-active' : '';				
				$quote_bg_array[]   = '<div class="bpquotes-selection bpquotes-color '. esc_attr($bpquotes_class).'" data-bg-type="quotescolor" data-bg-value="' . $color . '" data-bg-inverted-value="' . $bg_inverted_colors . '"><div style="background-color:' . $color . '"></div></div>';
			}
		}
		
		if ( ! empty( $quote_bg_array ) ) {
			shuffle( $quote_bg_array );
			$_blank = '<div class="remove-bpquotes-selection"><div style="background-color:#ffffff"></div></div>';
			array_unshift( $quote_bg_array, $_blank );
			$quote_html = '<div class="bpquotes-bg-selection-div" style="display:none;">';
			foreach ( $quote_bg_array as $key => $quote_arr ) {
				$quote_html .= $quote_arr ;
			}
			$quote_html .= '</div>';
			$quote_html = apply_filters( 'bpquotes_activity_post_form_option_panel', $quote_html, $bpquotes_gnrl_settings );
			echo wp_kses_post( $quote_html );
		}
	}

	/**
	 * Fires at the end of an activity post update, before returning the updated activity item ID.
	 *
	 * @param string $content     Content of the activity post update.
	 * @param int    $user_id     ID of the user posting the activity update.
	 * @param int    $activity_id ID of the activity item being updated.
	 * @param int    $g_activity_id  Activity item.
	 * @return void
	 */
	public function bpquotes_update_quotes_activity_meta( $content, $user_id, $activity_id, $g_activity_id = null ) {
		if ( isset( $g_activity_id ) ) {
			$activity_id = $g_activity_id;
		}
		if ( isset( $_POST['bp-quote-add-quote-nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bp-quote-add-quote-nonce'] ) ), 'bp-quote-add-quote-nonce' ) ) {
			return;
		}
		if ( ( isset( $_POST['bg-type'] ) && ! empty( $_POST['bg-type'] ) ) && isset( $_POST['bg-value'] ) && ! empty( $_POST['bg-value'] ) ) {

			$quotes_array = array(
				'bg-type'           => ( isset( $_POST['bg-type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-type'] ) ) : '',
				'bg-value'          => ( isset( $_POST['bg-value'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-value'] ) ) : '',
				'bg-inverted-value' => ( isset( $_POST['bg-inverted-value'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-inverted-value'] ) ) : '',
			);

			bp_activity_update_meta( $activity_id, 'bpquotes_meta', $quotes_array );
			do_action( 'bpquotes_update_quotes_activity_meta', $content, $user_id, $activity_id, $g_activity_id );
		}
	}
	
	/*
	 *
	 * Function for updating activity meta with the flag when Youzify.
	 * 
	*/
	public function bpquotes_update_yzea_activity_quotes($content, $activity_id) {
		
		if ( isset( $_POST['bp-quote-add-quote-nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bp-quote-add-quote-nonce'] ) ), 'bp-quote-add-quote-nonce' ) ) {
			return;
		}
		if ( ( isset( $_POST['bg-type'] ) && ! empty( $_POST['bg-type'] ) ) && isset( $_POST['bg-value'] ) && ! empty( $_POST['bg-value'] ) ) {

			$quotes_array = array(
				'bg-type'           => ( isset( $_POST['bg-type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-type'] ) ) : '',
				'bg-value'          => ( isset( $_POST['bg-value'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-value'] ) ) : '',
				'bg-inverted-value' => ( isset( $_POST['bg-inverted-value'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-inverted-value'] ) ) : '',
			);

			bp_activity_update_meta( $activity_id, 'bpquotes_meta', $quotes_array );
			do_action( 'bpquotes_update_yzea_activity_quotes', $content, $activity_id );
		}
		
		return $content;
	}

	/**
	 * Filters the activity content body.
	 *
	 * @since BuddyPress 1.2.0
	 *
	 * @param string $activity_content  Content body.
	 * @param object $activity_obj Activity object. Passed by reference.
	 */
	public function bpquotes_update_quotes_activity_content( $activity_content, $activity_obj ) {
		if ( isset( $activity_obj->id ) ) {
			$activity_id = $activity_obj->id;
		} else {
			$activity_obj = (isset( $GLOBALS['activities_template'] ) && isset( $GLOBALS['activities_template']->activity ) && !empty( $GLOBALS['activities_template']->activity ) ) ? $GLOBALS['activities_template']->activity : '';
			$activity_id = isset( $activity_obj->id ) ? $activity_obj->id : '';
		}
		if ( ! empty( $activity_id ) && bpquotes_is_quoted_activity( $activity_id ) ) {
			$quote_class       = bpquotes_get_quote_class( $activity_id );
			$bg_value          = bpquotes_get_quote_bg_value( $activity_id );
			$bg_inverted_value = bpquotes_get_quote_bg_inverted_value( $activity_id );
			$style             = '';
			if ( 'quotesimg' === $quote_class ) {
				$style = 'background-image:url(' . $bg_value . ');';
			} elseif ( 'quotescolor' === $quote_class ) {
				$style = 'background-color:' . $bg_value . ';';
			}

			if ( '' !== $bg_inverted_value ) {
				$style .= 'color:' . $bg_inverted_value . ';';
			} else {
				$style .= 'color: white';
			}
		$bpquotes_gnrl_settings = get_option( 'bpquotes_gnrl_settings' );
		$_class = '';
		if( ! isset( $bpquotes_gnrl_settings['bg_allow_quote_icon'] ) || 'yes' != $bpquotes_gnrl_settings['bg_allow_quote_icon']){
			$_class = 'bpquotes-content-icon';
		}
			$_activity_content  = "<div class='bpquotes-content-wrapper ". esc_attr( $_class ) ."'>";			
			$_activity_content .= "<div class='" . $quote_class . "' style='" . $style . "'>";
			$_activity_content .= '<div class="bpquotes-content">' . wpautop( $activity_content ) . '</div>';
			$_activity_content .= '</div>';
			$_activity_content .= '</div>';
			$activity_content   = $_activity_content;
		}
		$activity_content = apply_filters( 'bpquotes_update_quotes_activity_content', $activity_content, $activity_obj );
		return $activity_content;
	}

	/**
	 * Embed quotes activity data in rest api activity endpoint.
	 *
	 * @param  object $response get response data.
	 * @param  object $request get request data.
	 * @param  array  $activity get activity data.
	 * @return $response
	 */
	public function bpquotes_activity_data_embed_rest_api( $response, $request, $activity ) {
		if( empty( $activity ) ){
			return $response; 
		}
		$data                        = bp_activity_get_meta( $activity->id, 'bpquotes_meta', true );
		$response->data['bp_quotes'] = $data;
		$response = apply_filters( 'bpquotes_activity_data_embed_rest_api', $response, $request, $activity );
		return $response;
	}

	/**
	 * Callback for the shortcode rest api.
	 *
	 * @param [array] $atts
	 * @param [string] $content
	 * @return void
	 */
	public function bpquotes_rest_api_shortcode( $atts, $content = null ) {
		global $wpdb;
		$default_atts = array(
			'activity_id' => null,
		);
		$atts         = shortcode_atts( $default_atts, $atts );
		$activity_id = isset( $atts[ 'activity_id' ] ) ? $atts[ 'activity_id' ] : 0;

		ob_start();
		if ( function_exists( 'bp_is_active' ) ) {
			wp_enqueue_style( $this->plugin_name );
			wp_enqueue_style( 'bpquotes-slick-css' );
			if ( ! wp_style_is( 'wb-font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-font-awesome' );
			}

			if ( ! wp_style_is( 'wb-icons', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-icons' );
			}

			wp_enqueue_script( $this->plugin_name );

			$activity_data = bp_activity_get_specific(
				array(
					'activity_ids' => $activity_id,
					'show_hidden'  => true,
					'spam'         => 'all',
				)
			);
			echo "<div class='bpquptes-options-attach-shortcode-wrapper'>";
			if ( bpquotes_is_quoted_activity( $activity_id ) ) {
				$activity_content = stripslashes_deep( $activity_data['activities'][0]->content );
				echo wp_kses_post( $this->bpquotes_update_quotes_activity_content( $activity_content, $activity_data['activities'][0] ) );
			} else {
				esc_html_e( 'This is not a quote activity.', 'buddypress-quotes' );
			}
			echo '</div>';
		}
		$html_response = apply_filters( 'bpquotes_rest_api_shortcode', ob_get_clean(), $atts, $activity_data );
		return $html_response;
	}
	
	/**
	 * Add quote required field and data to the activity posting pages.
	 *
	 * @param [object] $activity
	 * @return void
	 */
	public function bpquotes_get_activity_content( $activity ) {
		$activity_id 	= $activity->id;			
		$bpquotes_meta 	= bp_activity_get_meta( $activity_id, 'bpquotes_meta', true );			
		if( !empty( $bpquotes_meta ) ) {
			$bg_type 			= ( isset( $bpquotes_meta['bg-type'] ) ) ? $bpquotes_meta['bg-type'] : '';
			$bg_value 			= ( isset( $bpquotes_meta['bg-value'] ) ) ? $bpquotes_meta['bg-value'] : '';
			$bg_inverted_value 	= ( isset( $bpquotes_meta['bg-inverted-value'] ) ) ? $bpquotes_meta['bg-inverted-value'] : '';
			?>
			<div id="whats-new-attachments" class="whats-new-attachments bpquotes_edit_activity_wrapper">
				<div class="post-elements-buttons-item bp-quote-icon-wrapper">					
					<input type='hidden' class='bg-type-input' name='bg-type' value="<?php echo esc_attr($bg_type);?>" >
					<input type='hidden' class='bg-type-value' name='bg-value' value="<?php echo esc_attr($bg_value);?>" >
					<input type='hidden' class='bg-inverted-type-value' name='bg-inverted-value' value="<?php echo esc_attr($bg_inverted_value);?>" >
					<input type='hidden' name='bp-quote-add-quote-nonce' value="<?php echo esc_attr( wp_create_nonce( 'bp-quote-add-quote-nonce' ) ); ?>">
				</div>
				<?php $this->bpquotes_activity_post_form_option_panel( $activity );?>
			</div>
			<script>
				(function($) {
				'use strict';
					$(document).ready(function($){		
						var obj_rtl;
						if ($('body').hasClass("rtl")) {
							obj_rtl = true;
						} else {
							obj_rtl = false;
						}
					
						if ($('body').hasClass("rtl")) {
							$('.bpquotes-bg-selection-div').not('.slick-initialized').slick({
								dots: false,
								infinite: true,
								variableWidth: true,
								swipeToSlide: true,
								centerMode: true,
								rtl: obj_rtl
							});
						} else {
							$('.bpquotes-bg-selection-div').not('.slick-initialized').slick({
								dots: false,
								infinite: true,
								variableWidth: true,
								swipeToSlide: true
							});
						}
						$( '.bpquotes-selection.bpquotes-active').trigger( 'click');
					});
				})( jQuery );
			</script>
			<?php
		}
	}
}

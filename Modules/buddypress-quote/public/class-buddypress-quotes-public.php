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
		$current_component = '';
		if ( isset( $post->ID ) && '' !== $post->ID && '0' !== $post->ID ) {
			$_elementor_controls_usage = get_post_meta( $post->ID, '_elementor_controls_usage', true );
			if ( ! empty( $_elementor_controls_usage ) ) {
				foreach ( $_elementor_controls_usage as $key => $value ) {
					if ( 'buddypress_shortcode_activity_widget' === $key || 'bp_newsfeed_element_widget' === $key ) {
						$current_component = 'activity';
						break;
					}
				}
			}
		}
		if ( is_buddypress()
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'activity-listing' ) ) )
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'bppfa_postform' ) ) )
			|| 'activity' === $current_component ) {

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-quotes-public.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'bpquotes-slick-css', plugin_dir_url( __FILE__ ) . 'css/slick.css', array(), $this->version, 'all' );

			if ( ! wp_style_is( 'wb-font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'wb-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
			}
		}

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

		global $post;
		$current_component = '';
		if ( isset( $post->ID ) && '' !== $post->ID && '0' !== $post->ID ) {
			$_elementor_controls_usage = get_post_meta( $post->ID, '_elementor_controls_usage', true );
			if ( ! empty( $_elementor_controls_usage ) ) {
				foreach ( $_elementor_controls_usage as $key => $value ) {
					if ( 'buddypress_shortcode_activity_widget' === $key || 'bp_newsfeed_element_widget' === $key ) {
						$current_component = 'activity';
						break;
					}
				}
			}
		}
		if ( is_buddypress()
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'activity-listing' ) ) )
			|| ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'bppfa_postform' ) ) )
			|| 'activity' === $current_component ) {

			wp_enqueue_script( 'bpquotes-slick-js', plugin_dir_url( __FILE__ ) . 'js/slick.min.js', array( 'jquery' ), time(), true );

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-quotes-public.js', array( 'jquery' ), $this->version, false );
			$active_template = get_option( '_bp_theme_package_id' );
			wp_localize_script(
				$this->plugin_name,
				'bpquotes_obj',
				array(
					'active_template' => $active_template,
					'buddyboss'       => buddypress()->buddyboss,
				)
			);
		}

	}

	/**
	 * Activity Posts form options
	 */
	public function bpquotes_activity_post_form_options() {
		echo '<div class="post-elements-buttons-item bp-quote-icon-wrapper">';
		echo "<div class='quote-btn'><span class='dashicons dashicons-editor-quote'></span></div>";
		echo "<input type='hidden' class='bg-type-input' name='bg-type'>";
		echo "<input type='hidden' class='bg-type-value' name='bg-value'>";
		echo "<input type='hidden' class='bg-inverted-type-value' name='bg-inverted-value'>";
		echo '</div>';
	}

	public function bpquotes_activity_post_form_option_panle() {

		$bpquotes_gnrl_settings = get_option( 'bpquotes_gnrl_settings' );
		$quote_bg_array         = array();
		if ( isset( $bpquotes_gnrl_settings['image_url'] ) && ! empty( $bpquotes_gnrl_settings['image_url'] ) ) {
			foreach ( $bpquotes_gnrl_settings['image_url'] as $key => $url ) {
				$quote_bg_array[] = '<div class="bpquotes-selection bpquotes-img" data-bg-type="quotesimg" data-bg-value="' . esc_url( $url ) . '"><img src="' . $url . '"></div>';
			}
		}
		if ( isset( $bpquotes_gnrl_settings['bg_colors'] ) && ! empty( $bpquotes_gnrl_settings['bg_colors'] ) ) {
			foreach ( $bpquotes_gnrl_settings['bg_colors'] as $_key => $color ) {
				$bg_inverted_colors = ( isset( $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] ) && '' !== $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] ) ? $bpquotes_gnrl_settings['bg_inverted_colors'][ $_key ] : '';
				$quote_bg_array[]   = '<div class="bpquotes-selection bpquotes-color" data-bg-type="quotescolor" data-bg-value="' . $color . '" data-bg-inverted-value="' . $bg_inverted_colors . '"><div style="background-color:' . $color . '"></div></div>';
			}
		}
		if ( ! empty( $quote_bg_array ) ) {
			shuffle( $quote_bg_array );
			$_blank = '<div class="remove-bpquotes-selection"><div style="background-color:#ffffff"></div></div>';
			array_unshift( $quote_bg_array, $_blank );
			echo '<div class="bpquotes-bg-selection-div">';
			foreach ( $quote_bg_array as $key => $quote_arr ) {
				echo $quote_arr;
			}
			echo '</div>';
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

		if ( ( isset( $_POST['bg-type'] ) && ! empty( $_POST['bg-type'] ) ) && isset( $_POST['bg-value'] ) && ! empty( $_POST['bg-value'] ) ) {

			$quotes_array = array(
				'bg-type'           => ( isset( $_POST['bg-type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-type'] ) ) : '',
				'bg-value'          => ( isset( $_POST['bg-value'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-value'] ) ) : '',
				'bg-inverted-value' => ( isset( $_POST['bg-inverted-value'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bg-inverted-value'] ) ) : '',
			);

			bp_activity_update_meta( $activity_id, 'bpquotes_meta', $quotes_array );
		}
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
		if ( isset($activity_obj->id)) {
			$activity_id = $activity_obj->id;
		} else {
			$activity_obj = $GLOBALS['activities_template']->activity;;
			$activity_id = $activity_obj->id;
		}

		$activity_id = $activity_obj->id;
		if ( is_quoted_activity( $activity_id ) ) {
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

			$_activity_content  = "<div class='bpquotes-content-wrapper'>";
			$_activity_content .= "<div class='" . $quote_class . "' style='" . $style . "'>";
			$_activity_content .= '<div class="bpquotes-content">' . wpautop( $activity_content ) . '</div>';
			$_activity_content .= '</div>';
			$_activity_content .= '</div>';
			$activity_content   = $_activity_content;
		}

		return $activity_content;
	}

	/**
	 * Embedded activity content.
	 *
	 * @since BuddyPress 9.1.1
	 *
	 * @param string $activity_content  Embedded activity
	 */
	public function bpquotes_activity_embed_add_inline_styles() {
		?>
                <style type="text/css">
                    .bpquotes-content-wrapper {
                        width: 100%;
                        position: relative;
                        overflow: hidden;
                    }

                    .bpquotes-content-wrapper .quotescolor {
                        position: relative;
                        overflow: hidden;
                        padding: 25px;
                    }

                    .bpquotes-content-wrapper .quotesimg {
                        padding: 25px;
                        background-size: cover;
                        vertical-align: middle;
                        background-position: 50% 50%;
                        background-repeat: no-repeat;
                        display: -webkit-box;
                        display: -ms-flexbox;
                        display: flex;
                        -webkit-box-align: center;
                        -ms-flex-align: center;
                        align-items: center;
                        position: relative;
                    }

                    .bpquotes-content-wrapper .quotesimg:after {
                        content: "";
                        width: 100%;
                        height: 100%;
                        position: absolute;
                        background: rgba(0,0,0,.3);
                        top: 0;
                        left: 0;
                        z-index: 0;
                    }

                    .bpquotes-content-wrapper .bpquotes-content,
                    .activity-list .activity-item .activity-content .bpquotes-content-wrapper .bpquotes-content,
                    .bp-nouveau .activity-list .activity-item .activity-content .activity-inner .bpquotes-content-wrapper .bpquotes-content {
                        padding: 70px 0 70px;
                        border: none;
                        text-align: left;
                        font-style: inherit;
                        width: 75%;
                        font-size: 18px;
                        line-height: 1.5;
                        font-weight: 500;
                        color: inherit;
                        margin: 0 auto !important;
                        z-index: 1;
                    }

                    .bpquotes-content-wrapper .bpquotes-content p,
                    .activity-list .activity-item .activity-content .bpquotes-content-wrapper .bpquotes-content p,
                    .bp-nouveau .activity-list .activity-item .activity-content .activity-inner .bpquotes-content-wrapper .bpquotes-content p {
                        border: none;
                        text-align: left;
                        font-style: inherit;
                        font-size: 18px;
                        line-height: 1.5;
                        font-weight: 500;
                        color: inherit;
                        margin: 0 auto !important;
                        z-index: 1;
                    }

                    .bpquotes-content .activity-read-more a {
                        background: transparent !important;
                        color: inherit !important;
                    }

                    .bpquotes-content-wrapper .bpquotes-content {
                        position: relative;
                    }
                    
                    .bpquotes-content-wrapper::before {
                        content: '';
                        background: url(<?php echo BPQUOTES_PLUGIN_URL?>/public/images/quote.svg);
                        position: absolute;
                        left: 25px;
                        top: 25px;
                        font-size: 24px;
                        height: 40px;
                        width: 40px;
                        display: -webkit-box;
                        display: -ms-flexbox;
                        display: flex;
                        -webkit-box-align: center;
                        -ms-flex-align: center;
                        align-items: center;
                        -webkit-box-pack: center;
                        -ms-flex-pack: center;
                        justify-content: center;
                        color: #fff;
                        border-radius: 4px;
                        z-index: 9;
                    }

                </style>
            <?php
        }
}

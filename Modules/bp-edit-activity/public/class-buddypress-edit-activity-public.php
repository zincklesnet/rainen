<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Edit_Activity
 * @subpackage Buddypress_Edit_Activity/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Edit_Activity
 * @subpackage Buddypress_Edit_Activity/public
 * @author     Wbcom Designs <info@wbcomdesign.com>
 */
class Buddypress_Edit_Activity_Public {

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
	
	private $enable_edit_option;
	private $edit_activity_duration;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name 				= $plugin_name;
		$this->version 					= $version;
		$this->enable_edit_option 		= bp_get_option( '_bp_enable_edit_option', true );
		$this->edit_activity_duration 	= ( function_exists('buddypress') && buddypress()->buddyboss ) ?  bp_get_option( '_bp_activity_edit_time', true ) : bp_get_option( '_bp_edit_activity_duration', true );
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
		 * defined in Buddypress_Edit_Activity_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Edit_Activity_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$min 	= '';
			$path   = is_rtl() ? 'rtl/' : '';
		} else {
			$min	= '.min';
			$path   = is_rtl() ? 'rtl/' : 'min/';
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . "css/{$path}buddypress-edit-activity-public{$min}.css", array(), $this->version, 'all' );

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
		 * defined in Buddypress_Edit_Activity_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Edit_Activity_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$min = '';
			$path      = '';
		} else {
			$min = '.min';
			$path      = 'min/';
		}
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . "js/{$path}buddypress-edit-activity-public{$min}.js", array( 'jquery' ), $this->version, false );
		
		wp_localize_script(
			$this->plugin_name,
			'bp_edit_activity',
			array(
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'         => wp_create_nonce( 'buddypress-edit-activity' ),
			)
		);

	}
	
	/**
	 * Edit Activity Duration
	 * Return $duration
	 *
	 * @since    1.0.0
	 */
	public function buddypress_edit_activity_duration() {
		$edit_activity_duration = $this->edit_activity_duration;
		
		switch( $edit_activity_duration ) {	
			case 'thirty_days':		
			case '30_days':
				$duration = 30 * DAY_IN_SECONDS;
				break;
			case 'seven_days':
			case '7_day':
				$duration = WEEK_IN_SECONDS;
				break;
			case 'one_day':
			case '1_day':
				$duration = DAY_IN_SECONDS;
				break;
			case 'one_hour':
			case '1_hour':
				$duration = HOUR_IN_SECONDS;
				break;
			case 'ten_minutes':
			case '10_minutes':			
				$duration = 10 * MINUTE_IN_SECONDS;
				break;
			case 'forever':			
			default:
				$duration = 0;
				break;
		}		
		return apply_filters('buddypress_edit_activity_duration', $duration );
	}
	
	/**
	 * Check if current user can edit given activity.
	 * 
	 * @global type $activities_template
	 * @param object $activity
	 * @return boolean
	 */
	public function can_bp_edit_activity( $activity = false ) {
		if( !is_user_logged_in() || $this->enable_edit_option == 0 )
			return false;
		
		global $activities_template;
		
		$editable_types = apply_filters('bp_editable_types_activity', ['activity_update']);

		// Try to use current activity if none was passed
		if ( empty( $activity ) && ! empty( $activities_template->activity ) ) {
			$activity = $activities_template->activity;
		}
			
		$can_edit = false;
		
		/**
		 * User must be either an admin or the author of activity himself/herself, to be able to edit it.
		 */
		if( current_user_can( 'level_10' ) ){
			$can_edit = true;
		} else {
			
			if ( isset( $activity->user_id ) && ( (int) $activity->user_id === bp_loggedin_user_id() ) ) {
				$can_edit = true;
			}
			
		}
		
		/**
		 * Activity must be of type 'activity_update', 'activity_comment', 
		 * whatever is selected in plugin settings.
		 */		
		if( $can_edit === true ){
			if( !in_array( $activity->type, $editable_types ) ){
				$can_edit = false;
			}

            /**
             * Do not let edit an activity with an empty content,
             * usually such activity has been added by some 3rd party plugin
             */
            if ( empty( $activity->content ) ) {
                $can_edit = false;
            }
		}
		
		/**
		 * is a timeout defined and has the current activity passed the timeout?
		 * Timeout is not applicable for admins by default ( unless overridden in settings)
		 */
		
		if( $can_edit === true && !current_user_can( 'level_10' ) ){
			
			if( ( $timeout = (int)$this->buddypress_edit_activity_duration() ) != 0 ){                
				$activity_time = strtotime( $activity->date_recorded );
				$current_time = time();
				
				$diff = (int) abs( $current_time - $activity_time );
				if( $diff >= $timeout ){
					// We're now comparing seconds to seconds
					$can_edit = false;
				}
			}
		}		
		return apply_filters( 'buddypress_can_edit_activity', $can_edit, $activity );
	}
	
	/**
	 * Display Edit Activity button
	 *
	 * @since    1.0.0
	 */
	public function bp_edit_activity_btn_edit_activity() {
		global $activities_template, $dropdown_toggle_buttons;
		if( $dropdown_toggle_buttons == true ) {
			return;
		}
		if( class_exists( 'youzify' ) ) {		
			return;
		}
		$content = $activity = $activities_template->activity->content;
		preg_match_all( '/<!-- wp:([a-zA-Z0-9\-\/]+)/', $content, $matches );
		
		if ( $this->can_bp_edit_activity() && empty( $matches[0] ) ) {
			if ( class_exists( 'BuddyPress' ) && isset( buddypress()->buddyboss ) ) {
				?>
				<div class="generic-button">
					<a href="#" class="button bp-secondary-action bp-action-edit bb_edit_activity" data-activity_id="<?php esc_attr( bp_activity_id() ); ?>">
						<span class="bp-screen-reader-text"><?php esc_html_e( 'Edit', 'buddypress-edit-activity' ); ?></span>
						<i class="bb-icon-edit"></i>
						<span class="edit-label"><?php esc_html_e( 'Edit', 'buddypress-edit-activity' ); ?></span>
					</a>
				</div>
				<?php 
			} else {
				?>
				<div class="generic-button">
					<a href="#" class="button bp-secondary-action bp-action-edit buddypress_edit_activity bp-tooltip" data-bp-tooltip="<?php esc_html_e( 'Edit', 'buddypress-edit-activity' ); ?>" data-activity_id="<?php esc_attr( bp_activity_id() ); ?>">
						<span class="bp-screen-reader-text"><?php esc_html_e( 'Edit', 'buddypress-edit-activity' ); ?></span>
					</a>
				</div>
				<?php
			}
		}
	}
	
	public function bp_edit_activity_btn_edit_activity_buttons( $buttons, $activity_id ) {
		global $activities_template, $dropdown_toggle_buttons;		
		
		if( class_exists( 'youzify' ) ) {		
			return $buttons;
		}
		if( ! $this->can_bp_edit_activity() ){
			unset( $buttons['activity_edit'] );
			return $buttons;
		}
		$content = $activity = $activities_template->activity->content;
		preg_match_all( '/<!-- wp:([a-zA-Z0-9\-\/]+)/', $content, $matches );
		if ( empty( $matches[0] ) ) {
			$buttons['activity_bp_edit'] = array(
				'id'                => 'activity_bp_edit',
				'position'          => 35,
				'component'         => 'activity',
				'parent_element'    => '',
				'parent_attr'       => [],
				'must_be_logged_in' => true,
				'button_element'    => 'a',
				'button_attr'       => array(
					'href'  => '#',
					'class'           => 'bp-secondary-action button item-button bp-action-edit buddypress_edit_activity bp-tooltip',						
					'data-bp-tooltip' => ( class_exists( 'BuddyPress' ) && !isset( buddypress()->buddyboss ) ) ? _x( 'Edit', 'button', 'buddypress-edit-activity' ) : '',
					'data-bp-nonce'		=> '',
					'data-activity_id'=> esc_attr( $activity_id ),
					
				)
			);
		}
		if( class_exists( 'BuddyPress' ) && isset( buddypress()->buddyboss ) ) {
			unset( $buttons['activity_edit'] );
			$buttons['activity_bp_edit']['link_text'] = sprintf(
				'<span class="bp-screen-reader-text">%1$s</span><span class="edit-label">%2$s</span>',
				__( 'Edit Activity', 'buddypress-edit-activity' ),
				__( 'Edit', 'buddypress-edit-activity' )
			);
		} else {
			$buttons['activity_bp_edit']['link_text'] = sprintf( '<span class="bp-screen-reader-text">%s</span>', _x( 'Edit', 'button', 'buddypress-edit-activity' ) );
		}
		$dropdown_toggle_buttons = true;
		
		return $buttons;
	}
	
	/**
	 * Edit Activity Template
	 *
	 * @since    1.0.0
	 */
	public function bp_edit_activity_template() {
		if ( is_user_logged_in() ): ?>
		
		<div id="bp-edit-activity-wrapper" style="display:none">
			<div class="bp-activity-edit-model-wrap">
				<div class="modal-wrapper">
					<div class="modal-container">
						<header class="bp-model-header">
							<h4>
								<span class="target_name"><?php echo esc_html__( 'Edit activity', 'buddypress-edit-activity' ); ?></span>
							</h4>
							<a class="bp-model-close-button" href="#">
								<span class="dashicons dashicons-no-alt"></span>
							</a>
						</header>
	
						<?php do_action( 'bp_before_edit_activity_template' ); ?>
						
						<form id="frm-bp-edit-activity" method="POST">				
							<input type="hidden" name="bp_edit_activity_nonce" value="<?php echo esc_attr( wp_create_nonce( 'bp-edit-activity') ); ?>">
							<input type="hidden" id="bp_edit_activity_id" name="activity_id" value="">
							<div class="field ac-textarea">
								<textarea class="bp-suggestions" id="whats-new" cols="50" rows="4" style="resize: vertical;" name="activity_content"></textarea>
							</div>
							<div id="bp-edit-additional-activity-content" class="bp_edit_additiona_activity_content">
								<?php do_action( 'bp_edit_activity_fields' ); ?>
							</div>
							
							<input type="submit" name="update_activity" value="<?php esc_html_e( 'Update activity', 'buddypress-edit-activity' ); ?>" />
						</form>
						
						<?php do_action( 'bp_after_edit_activity_template' ); ?>
					</div>
				</div>
			</div>
		</div>
		
		<?php
		endif;
	}
	
	/**
	 * Get Edit Activity content
	 *
	 * @since    1.0.0
	 */
	public function buddypress_edit_activity_get_content() {
		
		check_ajax_referer( 'buddypress-edit-activity', 'ajax_nonce', true );
		
		$retval = array(
			'status'	=> false,			
			'content'	=> '<div class="bp-edit-activity-error"><span class="title">'. esc_html__( 'Nothing found!', 'buddypress-edit-activity' ) . '</span></div>',
		);
		
		$activity_id = isset( $_POST['activity_id'] ) ? (int)$_POST['activity_id'] : false;
		if( !$activity_id ){
			wp_send_json_error( $retval );
		}
		
		
		$activity = new BP_Activity_Activity( $activity_id );
		if( !$activity || is_wp_error( $activity ) ) {
			wp_send_json_error( $retval );
		}		
		
		if( !$this->can_bp_edit_activity( $activity ) ) {
			wp_send_json_error( $retval );
		}
		
		$content = stripslashes( $activity->content );
		
		//convert @mention anchor tags into plain text
		$content = $this->buddypress_edit_activity_strip_mention_tags( $content );
		
		//remove surrounding <p> tags
		if( substr( $content, 0, strlen( "<p>" ) ) == "<p>" ){
			$content = substr( $content, strlen( "<p>" ) );
		} 
		if( substr( $content,-strlen( "</p>" ) )=== "</p>" ){
			$content = substr( $content, 0, strlen( $content )-strlen( "</p>" ) );
		}

		if( function_exists('buddypress') && buddypress()->buddyboss ){
			$content = str_replace( array( '<p>', '</p>' ), array( '', "\n" ), $content );
		
			// Clean up multiple line breaks
			$content = preg_replace( '/\n+/', "\n", $content );
			
			// Convert remaining line breaks to <br> tags
			$content = nl2br( $content );
		}
	

        $retval['status'] = true;
        $retval['content'] = apply_filters('buddypress_get_edit_activity_content', wp_strip_all_tags( $content ), $activity  );
		
		ob_start();

		do_action('bp_get_addition_activity_content', $activity );

		$additional_content = ob_get_clean();
		
        $retval['bp_get_additional_content'] = $additional_content;

		wp_send_json_success( $retval );
	}
	
	
	/**
	 * Strip Activity content
	 *
	 * @since    1.0.0
	 */
	public function buddypress_edit_activity_strip_mention_tags( $content ){
		if( empty( $content ) )
			return '';
		
		$dom = new DOMDocument();	
		// Suppress warnings temporarily
		libxml_use_internal_errors(true);		
		$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		// Clear the errors
		
		$anchors = $dom->getElementsByTagName('a');
		$len = $anchors->length;

		if ( $len > 0 ) {
			$i = $len-1;
			while ( $i > -1 ) {
				$anchor = $anchors->item( $i );

				if ( $anchor->hasAttribute('href') ) {
					$href = $anchor->getAttribute('href');
					$regex = '/^http/';

					if ( !preg_match ( $regex, $href ) ) { 
						$i--;
						continue;
					}

					$text = $anchor->nodeValue;
					$pos_attherate = strpos( $text, '@' );
					if( $pos_attherate===0 ){
						$textNode = $dom->createTextNode( $text );
						$anchor->parentNode->replaceChild( $textNode, $anchor );
					}
				}
				$i--;
			}
		
			$new_content = utf8_decode( $dom->saveHTML( $dom->documentElement ) );
			$html_fragment = preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content ));
			libxml_clear_errors();
			return trim( $html_fragment );
		} else {			
			libxml_clear_errors();
			return $content;
		}
	}
	
	/**
	 * Save Edit Activity content
	 *
	 * @since    1.0.0
	 */
	public function buddypress_edit_activity_save_content() {
		

		check_ajax_referer( 'buddypress-edit-activity', 'ajax_nonce', true );
		
		$retval = array(
			'status'	=> false,
			'content'	=> '<div class="bp-edit-activity-error"><div class="bp-edit-item-list pull-animation"><span class="title">'. esc_html__( 'Error!', 'buddypress-edit-activity' ) . '</span></div></div>',
		);
		
		$activity_id = isset( $_POST['activity_id'] ) ? (int)$_POST['activity_id'] : false;
		if( !$activity_id ){
			wp_send_json_error( $retval );
		}
		
		$args = array(
			'activity_id'	=> $activity_id,
			'content'		=> isset( $_POST['activity_content'] ) ? wp_kses_post( wp_unslash( $_POST['activity_content'] ) ) : '',
		);
		
		$activity = new BP_Activity_Activity( $args['activity_id'] );
		if( !$activity || is_wp_error( $activity ) ) {
			wp_send_json_error( $retval );
		}
		
		if( !$this->can_bp_edit_activity( $activity ) ) {
			wp_send_json_error( $retval );
		}

        do_action( 'buddypress_edit_activity_before_save_activity_content', $activity->id );
		
		$args['content'] 	= apply_filters( 'bp_activity_new_update_content', $args['content'] );
        $activity->content 	= apply_filters( 'buddypress_edit_activity_content',  $args['content'], $activity->id );
		$activity->save();
		
		do_action( 'bp_activity_posted_update', $args['content'], bp_loggedin_user_id(), $args['activity_id'] );
		
		do_action( 'buddypress_edit_activity_after_save_activity_content', $activity->id, $activity );
		
		bp_activity_update_meta( $args['activity_id'], '_bp_edit_activity', true );
		
		ob_start();
		if( bp_has_activities( array( 'include' => $args['activity_id'], 'show_hidden'=> true ) ) ){
			while ( bp_activities() ) { 
				bp_the_activity();				
				bp_get_template_part( 'activity/entry' );
			}
		}
		
		$activity_updated_html_content = ob_get_clean();
		$retval['content'] 	= $activity_updated_html_content;
		$retval['status'] 	= true;
		$retval['message']  = '<div class="bp-edit-activity-success"><div class="bp-edit-item-list pull-animation"><span class="title">' . esc_html__( 'Successfully edited activity', 'buddypress-edit-activity' ) . '</span></div></div>';
						
		
		wp_send_json_success( $retval );
	}
	

	/**
	 * Display edited text after Activity content
	 *
	 * @since    1.0.0
	 */
	public function buddypress_bp_edit_activity_action( $action, $activity ) {
		
		$activity_id 		= $activity->id;
		$_bp_edit_activity 	= bp_activity_get_meta( $activity_id, '_bp_edit_activity', true );
		
		if( $_bp_edit_activity ){
			return apply_filters('buddypress_bp_edit_activity_action', $action . ' <span class="bp-activity-edited-text">' .esc_html__('(edited)','buddypress-edit-activity') . '</span>', $activity );
		} else {
			return apply_filters('buddypress_bp_edit_activity_action', $action, $activity );
		}
	}
	
	public function buddypress_edit_activity_button() {
		if( ! class_exists( 'youzify' ) ) {		
			return;
		}
		global $activities_template;
		$content = $activity = $activities_template->activity->content;
		preg_match_all( '/<!-- wp:([a-zA-Z0-9\-\/]+)/', $content, $matches );
		if ( $this->can_bp_edit_activity() && empty( $matches[0] ) ) {?>
			<a href="#" class="button bp-secondary-action bp-action-edit buddypress_edit_activity" data-activity_id="<?php bp_activity_id(); ?>">
				<?php esc_html_e( 'Edit', 'buddypress-edit-activity' ); ?>
			</a>
		<?php
		}
	}
	
	public function bp_editable_youzify_types_activity( $editable_types ) {
		if( class_exists( 'youzify' ) ) {
			$editable_types[] = 'activity_status';			
		}
		return $editable_types;
	}

	/**
	 * Function to add edited string on edited activities.
	 * 
	 * @param string $action Activity action
	 * @param object $activity Activity object
	 * @since 1.0.1
	 */
	public function bp_edit_activity_modify_action( $action, $activity ){

		$activity_id 		= $activity->id;
		$_bp_edit_activity 	= bp_activity_get_meta( $activity_id, '_bp_edit_activity', true );
		
		if( $_bp_edit_activity ){
			return apply_filters('buddypress_bp_edit_activity_action', $action.'<span class="bb-activity-edited-text"> ' . __( '(edited)', 'buddypress-edit-activity' ) . ' </span>', $activity);
		} else {
			return apply_filters('buddypress_bp_edit_activity_action', $action, $activity );
		}
   		
		
	}
}

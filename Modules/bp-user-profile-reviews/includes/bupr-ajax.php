<?php
/**
 * Class to serve AJAX Calls
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package BuddyPress_Member_Reviews
 */

defined( 'ABSPATH' ) || exit;

/**
* Class to serve AJAX Calls
*
* @since    1.0.0
* @author   Wbcom Designs
*/
if ( ! class_exists( 'BUPR_AJAX' ) ) {
	/**
	 * The ajax functionality of the plugin.
	 *
	 * @package    BuddyPress_Member_Reviews
	 * @author     wbcomdesigns <admin@wbcomdesigns.com>
	 */
	class BUPR_AJAX {

		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {

			/* add action for approving reviews */
			add_action( 'wp_ajax_bupr_approve_review', array( $this, 'bupr_approve_review' ) );
			add_action( 'wp_ajax_nopriv_bupr_approve_review', array( $this, 'bupr_approve_review' ) );

			add_action( 'wp_ajax_allow_bupr_member_review_update', array( $this, 'wp_allow_bupr_my_member' ) );
			add_action( 'wp_ajax_nopriv_allow_bupr_member_review_update', array( $this, 'wp_allow_bupr_my_member' ) );

			/*** Filter post_date_gmt for prevent update post date on update_post_data */
			add_filter( 'wp_insert_post_data', array( $this, 'bupr_filter_review_post' ), 10, 1 );
			
			add_action( 'wp_ajax_bupr_edit_review', array( $this, 'bupr_edit_review' ) );
			add_action( 'wp_ajax_bupr_update_review', array( $this, 'bupr_update_review' ) );
		}

		/**
		 * Actions performed on inserting post data.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $data Post data array.
		 * @author   Wbcom Designs
		 */
		public function bupr_filter_review_post( $data ) {
			if ( $data['post_type'] === 'review' ) {
				$post_date             = $data['post_date'];
				$post_date_gmt         = get_gmt_from_date( $post_date );
				$data['post_date_gmt'] = $post_date_gmt;
			}
			return $data;
		}

		/**
		 * Actions performed to approve review at admin end.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_approve_review() {
			if (!check_ajax_referer('bupr_member_review_ajax', 'nonce', false)) {
                wp_send_json_error(array(
                    'message' => __('Security verification failed.', 'bp-member-reviews'),
                    'code' => 'security_error'
                ));
            }

			// Check if user can approve reviews
            if (!current_user_can('edit_posts')) {
                wp_send_json_error(array(
                    'message' => __('You do not have permission to approve reviews.', 'bp-member-reviews'),
                    'code' => 'permission_error'
                ));
            }

			// Check review ID.
			$review_id = isset($_POST['review_id']) ? absint($_POST['review_id']) : 0;
            if (empty($review_id)) {
                wp_send_json_error(array(
                    'message' => __('Invalid review ID.', 'bp-member-reviews'),
                    'code' => 'invalid_review'
                ));
            }

			// Verify review exists and is a draft
            $review = get_post($review_id);
            if (!$review || 'review' !== $review->post_type || 'draft' !== $review->post_status) {
                wp_send_json_error(array(
                    'message' => __('Review not found.', 'bp-member-reviews'),
                    'code' => 'not_found'
                ));
            }
				
			$args = array(
                'ID' => $review_id,
                'post_status' => 'publish',
            );
			$update_result = wp_update_post($args, true);
			if (is_wp_error($update_result)) {
                wp_send_json_error(array(
                    'message' => $update_result->get_error_message(),
                    'code' => 'update_failed'
                ));
            }
			 // Get author ID and trigger GamiPress integration if available
            $author_id = get_post_field('post_author', $review_id);
            if ($author_id) {
                do_action('gamipress_bp_member_review', $author_id);
            }
			// Send success response
            wp_send_json_success(array(
                'message' => __('Review approved successfully.', 'bp-member-reviews'),
                'code' => 'review-approved-successfully'
            ));
		}

		/**
		 * Add review to member's profile.
		 *
		 * @since    1.0.0
		 * @author   Wbcom Designs
		 */
		public function wp_allow_bupr_my_member() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'review-nonce' ) ) {
				wp_send_json_error(array(
                    'message' => __('Security verification failed.', 'bp-member-reviews'),
                    'code' => 'security_error'
                ));
                return;
			}
			// Check if this is the right AJAX action
            if (!isset($_POST['action']) || 'allow_bupr_member_review_update' !== $_POST['action']) {
                wp_send_json_error(array(
                    'message' => __('Invalid request.', 'bp-member-reviews'),
                    'code' => 'invalid_request'
                ));
                return;
            }
			global $bupr;
			
			$bupr_rating_criteria = array();
			if ( ! empty( $bupr['active_rating_fields'] ) ) {
				foreach ( $bupr['active_rating_fields'] as $bupr_keys => $bupr_fields ) {
						$bupr_rating_criteria[] = $bupr_keys;
				}
			}
			
			$bupr_reviews_status = 'yes' === $bupr['auto_approve_reviews'] ? 'publish' : 'draft';

			$bupr_multi_reviews         = $bupr['multi_reviews'];
			$bupr_current_user          = filter_input( INPUT_POST, 'bupr_current_user', FILTER_VALIDATE_INT );
			$current_user         		= wp_get_current_user();				
			$review_desc                = filter_input( INPUT_POST, 'bupr_review_desc', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$bupr_member_id             = filter_input( INPUT_POST, 'bupr_member_id', FILTER_VALIDATE_INT );
			$review_count               = filter_input( INPUT_POST, 'bupr_field_counter', FILTER_VALIDATE_INT );
			$anonymous_review           = filter_input( INPUT_POST, 'bupr_anonymous_review' );
			$profile_rated_field_values = isset( $_POST['bupr_review_rating'] ) ? array_map( 'absint', wp_unslash( $_POST['bupr_review_rating'] ) ) : array();
			$bupr_admin_general         = get_option( 'bupr_admin_general_options' );
			$site_name                  = get_bloginfo( 'name' );
			$site_admins                = get_users( array( 'role' => 'administrator' ) );
			$site_admin = !empty($site_admins) ? $site_admins[0]->display_name : '';
			if (empty($bupr_member_id)) {
				wp_send_json_error('<p class="bupr-error">' . esc_html__('Please select a member.', 'bp-member-reviews') . '</p>');
				return;
			}

				// Prevent self-reviews
			if ($bupr_current_user == $bupr_member_id) {
				wp_send_json_error('<p class="bupr-error">' . esc_html__('You cannot review your own profile.', 'bp-member-reviews') . '</p>');
				return;
			}

				// Get member info
			$bupr_reviewed_user = get_user_by('id', $bupr_member_id);
			if (!$bupr_reviewed_user) {
				wp_send_json_error('<p class="bupr-error">' . esc_html__('Selected member does not exist.', 'bp-member-reviews') . '</p>');
				return;
			}
			// Set user name (anonymous or actual)
			$user_name = ('yes' === $anonymous_review) ? __("An anonymous user", 'bp-member-reviews') : $current_user->display_name;
			
			$bupr_reviewed_user_name = $bupr_reviewed_user->display_name;

			$review_subject = sprintf(
			__('%s received a %s', 'bp-member-reviews'),
			$bupr_reviewed_user_name,
			bupr_profile_review_tab_singular_slug());

			// Process rating values
			$bupr_count = 0;
			$bupr_member_star = array();

			$member_args      = array(
				'post_type'      => 'review',
				'posts_per_page' => -1,
				'post_status'    => array(
					'draft',
					'publish',
				),
				'author'         => $bupr_current_user,
				'category'       => 'bp-member',
				'meta_query'     => array(
					array(
						'key'     => 'linked_bp_member',
						'value'   => $bupr_member_id,
						'compare' => '=',
					),
				),
			);
			$reviews_args     = new WP_Query( $member_args );

			if ( 'no' === $bupr['multi_reviews'] ) {
				$user_post_count = $reviews_args->post_count;
			} else {
				$user_post_count = 0;
			}

			if ( $user_post_count === 0 ) {
				if ( ! empty( $profile_rated_field_values ) ) {
					foreach ( $profile_rated_field_values as $bupr_stars_rate ) {
						if ( $bupr_count === $review_count ) {
							break;
						} else {
							$bupr_stars_rate = max(1, min(5, (int)$bupr_stars_rate));
							$bupr_member_star[] = $bupr_stars_rate;
						}
						$bupr_count++;
					}
				}

				$bupr_rated_stars = array();
				if ( $bupr['multi_criteria_allowed'] && ( count( $bupr_rating_criteria ) === count( $bupr_member_star ) ) )  {
					$bupr_rated_stars = array_combine( $bupr_rating_criteria, $bupr_member_star );
				} else {
					$bupr_rated_stars = $bupr_member_star;
				}

				$add_review_args = array(
					'post_type'    => 'review',
					'post_title'   => $review_subject,
					'post_content' => $review_desc,
					'post_status'  => $bupr_reviews_status,
					'post_author'  => $bupr_current_user,
				);

				$review_id = wp_insert_post( $add_review_args );
				// Handle insertion error
				if (is_wp_error($review_id)) {
					wp_send_json_error('<p class="bupr-error">' . esc_html__('Error creating review. Please try again.', 'bp-member-reviews') . '</p>');
					return;
				}

				// GamiPress integration for published reviews
				if ( $bupr_reviews_status == 'publish' ) {
					do_action( 'gamipress_bp_member_review', $bupr_current_user );
				}

				wp_set_object_terms( $review_id, 'BP Member', 'review_category' );
				update_post_meta( $review_id, 'linked_bp_member', $bupr_member_id );
				if ( 'yes' === $bupr['anonymous_reviews'] ) {
					update_post_meta( $review_id, 'bupr_anonymous_review_post', $anonymous_review );
				}
				if ( ! empty( $bupr_rated_stars ) ) :
					update_post_meta( $review_id, 'profile_star_rating', $bupr_rated_stars );
					
						// Recalculate reviews for the reviewed user after meta data is updated
						if ( $bupr_reviews_status == 'publish' ) {
						do_action( 'bupr_member_review_after_review_insert', $review_id, $bupr_member_id );
					}
				endif;

					
				// Handle notifications and emails
				$this->send_review_notifications($review_id, $bupr_current_user, $bupr_member_id, $bupr_reviews_status, $anonymous_review, $user_name);

					// Return success message
				if ('draft' === $bupr_reviews_status) {
					wp_send_json_success(sprintf(
						__('Thank you for sharing your %s! After admin approval, it will be displayed on members\' profiles.', 'bp-member-reviews'),
						esc_html($bupr['review_label'])
					));
				} else {
					wp_send_json_success(sprintf(
						__('Thank you for sharing your thoughts in this %s!', 'bp-member-reviews'),
						esc_html(strtolower($bupr['review_label']))
					));
				}

			} else {
				/* translators: %s: */
				wp_send_json_error( sprintf( esc_html__( 'You already posted a %1$s for this member.', 'bp-member-reviews' ), esc_html( strtolower( $bupr['review_label'] ) ) ) );
			}
			
		}

		  /**
         * Send notifications and emails for new reviews.
         *
         * @param int $review_id The review ID.
         * @param int $current_user_id The current user ID.
         * @param int $member_id The member ID being reviewed.
         * @param string $review_status Review status (publish/draft).
         * @param string $anonymous Whether review is anonymous.
         * @param string $user_name The reviewer's name.
         */
        private function send_review_notifications($review_id, $current_user_id, $member_id, $review_status, $anonymous, $user_name) {
            global $bupr;
            
            // Get necessary data
            $bupr_admin_general = get_option('bupr_admin_general_options');
            $site_name = get_bloginfo('name');
            $bupr_reciever_data = get_userdata($member_id);
            $site_admins = get_users(array('role' => 'administrator'));
            $site_admin = !empty($site_admins) ? $site_admins[0]->display_name : '';
            
            // Skip if receiver data doesn't exist
            if (!$bupr_reciever_data) {
                return;
            }
            
            $bupr_reciever_email = $bupr_reciever_data->user_email;
            $bupr_reciever_name = $bupr_reciever_data->display_name;
            
            // BuddyPress notification for published reviews
            if ('publish' === $review_status && 'yes' === $bupr['allow_notification']) {
                do_action('bupr_sent_review_notification', $member_id, $review_id);
            }
            
            // Email notifications for published reviews
            if ('publish' === $review_status && 'yes' === $bupr['allow_email']) {
                // Generate review URL
                if (function_exists('bp_members_get_user_url')) {
                    $bupr_review_url = bp_members_get_user_url($member_id) . strtolower($bupr['review_label_plural']) . '/view/' . $review_id;
                } else {
                    $bupr_review_url = bp_core_get_user_domain($member_id) . strtolower($bupr['review_label_plural']) . '/view/' . $review_id;
                }
                
                $review_subject = $bupr_reciever_name . " received a " . bupr_profile_review_tab_singular_slug();
                $review_link = '<a href="' . esc_url($bupr_review_url) . '">' . esc_html($review_subject) . '</a>';
                
                // Set email subject
                $bupr_subject = isset($bupr_admin_general['review_email_subject']) && !empty($bupr_admin_general['review_email_subject']) 
                    ? $bupr_admin_general['review_email_subject'] 
                    : __('New Review on Your Profile at [site-name]', 'bp-member-reviews');
                
                $bupr_subject = str_replace('[site-name]', $site_name, $bupr_subject);
                
                // Set email message
                $default_message = 'Hello [user-name], <br><br>We are pleased to inform you that [reviewer-name] has recently reviewed your profile.<br><br>To view the review, simply click on the link below:<br>[review-link]<br><br>Best regards,<br>The [site-name] Team';
                
                $bupr_message = isset($bupr_admin_general['review_email_message']) && !empty($bupr_admin_general['review_email_message'])
                    ? $bupr_admin_general['review_email_message']
                    : $default_message;
                
                $bupr_message = str_replace(
                    array('[user-name]', '[reviewer-name]', '[review-link]', '[site-name]'),
                    array($bupr_reciever_name, $user_name, $review_link, $site_name),
                    $bupr_message
                );
                
                $bupr_header = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($bupr_reciever_email, $bupr_subject, nl2br($bupr_message), $bupr_header);
            }
            
            // Admin notification for reviews needing approval
            if ('draft' === $review_status && 'yes' === $bupr['allow_email']) {
                $bupr_to = get_option('admin_email');
                $bupr_subject = isset($bupr_admin_general['review_approve_email_subject']) 
                    ? $bupr_admin_general['review_approve_email_subject'] 
                    : __('A New Review Requires Your Approval on [site-name]', 'bp-member-reviews');
                
                $bupr_subject = str_replace('[site-name]', $site_name, $bupr_subject);
                
                $bupr_review_url = add_query_arg(
                    array('post_type' => 'review'),
                    admin_url('edit.php')
                );
                
                $approve_review_link = '<a href="' . esc_url($bupr_review_url) . '">' . 
                    sprintf(__('Review from %s', 'bp-member-reviews'), $user_name) . '</a>';
                
                $default_approve_message = 'Hello [site-admin],

				Greetings!

				You have received a new review on [site-name] that is pending approval. To review and approve the submission, please click on the link below:

				[review-aproval-link]

				Thank you for helping us maintain a vibrant and engaged community.

				Best regards,  
				The [site-name] Team';
                
                $bupr_approve_message = isset($bupr_admin_general['review_approve_email_message']) 
                    ? $bupr_admin_general['review_approve_email_message'] 
                    : $default_approve_message;
                
                $bupr_approve_message = str_replace(
                    array('[review-aproval-link]', '[site-admin]', '[site-name]'),
                    array($approve_review_link, $site_admin, $site_name),
                    $bupr_approve_message
                );
                
                $bupr_header = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($bupr_to, $bupr_subject, nl2br($bupr_approve_message), $bupr_header);
                
                // Send BuddyPress notification to admin if available
                $site_admin_id = !empty($site_admins) ? $site_admins[0]->ID : '';
                if ($site_admin_id) {
                    do_action('bupr_sent_review_notification', $site_admin_id, $review_id);
                }
            }
        }

		/**
         * Handle review edit request via AJAX.
         *
         * @since    1.0.0
         * @access   public
         */
		public function bupr_edit_review() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'review-nonce' ) ) {
				  wp_send_json_error(array(
                    'message' => __('Security verification failed.', 'bp-member-reviews'),
                    'code' => 'security_error'
                ));
				return;
			}

			 // Validate request
            if (!isset($_POST['action']) || 'bupr_edit_review' !== $_POST['action']) {
                wp_send_json_error(array(
                    'message' => __('Invalid request.', 'bp-member-reviews'),
                    'code' => 'invalid_request'
                ));
                return;
            }
			global $bupr;

			$review_id             = ( isset( $_POST['review'] ) ) ?  absint( $_POST['review'] ) : 0;
			if (empty($review_id)) {
				wp_send_json_error(array(
					'message' => __('Invalid review ID.', 'bp-member-reviews'),
					'code' => 'invalid_review'
				));
				return;
			}
			$review                = get_post( $review_id );

			// Check if current user can edit this review
			if (get_current_user_id() != $review->post_author && !current_user_can('edit_others_posts')) {
				wp_send_json_error(array(
					'message' => __('You do not have permission to edit this review.', 'bp-member-reviews'),
					'code' => 'permission_error'
				));
				return;
			}
			
			$member_review_ratings = get_post_meta( $review_id, 'profile_star_rating', true );

			$return_review         = array();
			$review_output         = '';
			$field_counter         = 1;

			// Get active rating fields
			if ( ! empty( $bupr['active_rating_fields'] ) ) {
				$member_review_rating_fields = $bupr['active_rating_fields'];
			}

			// Create rating criteria array
			$bupr_rating_criteria = array();
			if ( ! empty( $member_review_rating_fields ) ) {
				foreach ( $member_review_rating_fields as $bupr_keys => $bupr_fields ) {
						$bupr_rating_criteria[] = $bupr_keys;
				}
			}

			$review_output .= '<div id="bupr-edit-review-field-wrapper" data-review="' . esc_attr( $review_id ) . '">';
			$review_output .= '<textarea name="bupr-review-description" id="review_desc" rows="4" cols="50">' . esc_textarea($review->post_content) . '</textarea>';

			if ( ! empty( $member_review_rating_fields ) && ! empty( $member_review_ratings ) ) {
				foreach ( $member_review_ratings as $field => $bupr_value ) {
					if ( in_array( $field, $bupr_rating_criteria, true ) ) {
						$review_output .= '<div class="multi-review"><div class="bupr-col-4 bupr-criteria-label">' . esc_attr( $field ) . '</div>';
						$review_output .= '<div id="member-review-' . $field_counter . '" class="bupr-col-4 bupr-criteria-content">';
						$review_output .= '<input type="hidden" id="clicked' . esc_attr( $field_counter ) . '" value="not_clicked">';
						$review_output .= '<input type="hidden" name="member_rated_stars[]" class="member_rated_stars bupr-star-member-rating" id="member_rated_stars' . esc_attr( $field_counter ) . '" data-critaria="' . esc_attr( $field ) . '" value="0" >';
						/*** Star rating Ratings */
						$stars_on  = (int)$bupr_value;
						$stars_off = 5 - $stars_on;
						$count     = 0;
						for ( $i = 1; $i <= $stars_on; $i++ ) {
							$review_output .= '<span id="' . esc_attr( $field_counter . $i ) . '" class="fas fa-star bupr-star-rate member-edit-stars bupr-star ' . esc_attr( $i ) . '" data-attr="' . esc_attr( $i ) . '"></span>';
							$count++;
						}

						for ( $i = 1; $i <= 5; $i++ ) {
							if ( $i > $count ) {
								$review_output .= '<span id="' . esc_attr( $field_counter . $i ) . '" class="far fa-star stars bupr-star-rate member-edit-stars bupr-star ' . esc_attr( $i ) . '" data-attr="' . esc_attr( $i ) . '"></span>';
							}
						}
						/*star rating end */
						$review_output .= '</div></div>';
					}else {
						$stars_on  = $bupr_value;
						$field_counter = 1;
						$count     = 0;
						ob_start();
						?>
						<div class="multi-review">
								<div class="bupr-col-4 bupr-criteria-label">
									<label><?php esc_html_e( 'Rating : ', 'bp-member-reviews' ); ?><small class="rating">*</small></label>
								</div>
								<div class="bupr-col-4 bupr-criteria-content" id="member_review<?php echo esc_attr( $field_counter ); ?>">
									<input type="hidden" id="<?php echo 'clicked' . esc_attr( $field_counter ); ?>" value="<?php echo 'not_clicked'; ?>">
									<input type="hidden" name="member_rated_stars[]" id="member_rated_stars" class="member_rated_stars bupr-star-member-rating" id="<?php echo 'member_rated_stars' . esc_attr( $field_counter ); ?>" data-critaria="<?php echo esc_attr( $field ); ?>" value="0"  value="0">
											<?php	
											for( $i = 1; $i <= $stars_on; $i++ ) {
												echo '<span id="' . esc_attr( $field_counter . $i ) . '" class="fas fa-star bupr-star-rate member-edit-stars bupr-star ' . esc_attr( $i ) . '" data-attr="' . esc_attr( $i ) . '"></span>';
												$count++;
											}
											for( $i = 1; $i <= 5; $i++ ) { 
												if ( $i > $count ) { ?>
										<span class="far member_stars <?php echo esc_attr( $i ); ?> fa-star bupr-stars bupr-star-rate member-edit-stars <?php echo esc_attr( $i ); ?>" id="<?php echo esc_attr( $field_counter ) . esc_attr( $i ); ?>" data-attr="<?php echo esc_attr( $i ); ?>" ></span>
									<?php }
								} ?>
								</div>
								<div class="bupr-col-12 bupr-error-fields">*<?php esc_html_e( 'This field is required.', 'bp-member-reviews' ); ?></div>
							</div>
							<input type="hidden" id="member_rating_field_counter" value="1">
						<?php 
						$review_output .= ob_get_contents();
						ob_end_clean();
					} 
					$field_counter++;
				}
			}
				
			/* translators: %s: */
			$review_output .= '<button type="button" class="button btn btn-default" id="bupr_upodate_review" name="update-review">' . sprintf( esc_html__( 'Update %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ) . '</button>';
			$review_output .= '</div>';

			if ( ! empty( $review ) ) {
				$return_review = array(
					'review' => $review_output,
				);
				wp_send_json_success( $return_review );
			}else {
				wp_send_json_error(array(
					'message' => __('Unable to load review form.', 'bp-member-reviews'),
					'code' => 'form_error'
				));
			}
		}

		/**
         * Handle review update via AJAX.
         *
         * @since    1.0.0
         * @access   public
         */
		public function bupr_update_review() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'review-nonce' ) ) {
				wp_send_json_error(array(
                    'message' => __('Security verification failed.', 'bp-member-reviews'),
                    'code' => 'security_error'
                ));
				return;
			}
			 // Validate request
            if (!isset($_POST['action']) || 'bupr_update_review' !== $_POST['action']) {
                wp_send_json_error(array(
                    'message' => __('Invalid request.', 'bp-member-reviews'),
                    'code' => 'invalid_request'
                ));
                return;
            }
			global $bupr;

			$review_id       = isset( $_POST['review_id'] ) ? absint( $_POST['review_id'] ) : 0;
			if (empty($review_id)) {
				wp_send_json_error(array(
					'message' => __('Invalid review ID.', 'bp-member-reviews'),
					'code' => 'invalid_review'
				));
				return;
			}
			$review_content  = isset( $_POST['bupr_review_desc'] ) ? sanitize_text_field( wp_unslash( $_POST['bupr_review_desc'] ) ) : '';
			$critaria_rating = isset( $_POST['bupr_review_rating'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['bupr_review_rating'] ) ) : array();
			$old_ratings     = get_post_meta( $review_id, 'profile_star_rating', true );

			if (!is_array($old_ratings)) {
				$old_ratings = array();
			}

			$review_args = array(
				'ID'           => esc_sql( $review_id ),
				'post_content' => wp_kses_post( $review_content ),
				'post_status'  => 'publish',
			);

			$update_result = wp_update_post( $review_args, true );

			// Handle update error
			if (is_wp_error($update_result)) {
				wp_send_json_error(array(
					'message' => $update_result->get_error_message(),
					'code' => 'update_failed'
				));
				return;
			}

			if ( ! empty( $critaria_rating ) ) {
				foreach ( $critaria_rating as $critaria => $rating ) {
					if ( array_key_exists( $critaria, $old_ratings ) && '0' !== $rating ) {
						$old_ratings[ $critaria ] = $rating;
					}
				}

				update_post_meta( $review_id, 'profile_star_rating', $old_ratings );
				// Recalculate member ratings
				$member_id = get_post_meta($review_id, 'linked_bp_member', true);
				if ($member_id) {
					do_action('bupr_member_review_after_review_insert', $review_id, $member_id);
				}
			}

			// Send success response
			wp_send_json_success(array(
				'message' => sprintf(__('%s updated successfully.', 'bp-member-reviews'), $bupr['review_label']),
				'code' => 'success'
			));

		}
	}
	new BUPR_AJAX();
}

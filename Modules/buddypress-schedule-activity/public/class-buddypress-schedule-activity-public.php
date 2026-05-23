<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Schedule_Activity
 * @subpackage Buddypress_Schedule_Activity/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Schedule_Activity
 * @subpackage Buddypress_Schedule_Activity/public
 * @author     Wbcom Designs <contact@wbcomdesigns>
 */
class Buddypress_Schedule_Activity_Public {

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
		 * defined in Buddypress_Schedule_Activity_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Schedule_Activity_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $bp;

		if ( bp_nouveau_schedule_activity_current_user_can( 'publish_activity' ) || ( ! empty( $bp ) && $bp->current_component === 'schedule-activity' ) ) {
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$extension = is_rtl() ? '.rtl.css' : '.css';
				$path      = is_rtl() ? '/rtl' : '';
			} else {
				$extension = is_rtl() ? '.rtl.css' : '.min.css';
				$path      = is_rtl() ? '/rtl' : '/min';
			}
			wp_enqueue_style( 'jquery-datetimepicker', plugin_dir_url( __FILE__ ) . 'css/vendor/jquery.datetimepicker.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css' . $path . '/buddypress-schedule-activity-public' . $extension, array(), $this->version, 'all' );
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
		 * defined in Buddypress_Schedule_Activity_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Schedule_Activity_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $bp;

		if ( bp_nouveau_schedule_activity_current_user_can( 'publish_activity' ) || ( ! empty( $bp ) && $bp->current_component === 'schedule-activity' ) ) {

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$extension = '.js';
				$path      = '';
			} else {
				$extension = '.min.js';
				$path      = '/min';
			}

			wp_enqueue_script( 'jquery-datetimepicker', plugin_dir_url( __FILE__ ) . 'js/vendor/jquery.datetimepicker.full.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js' . $path . '/buddypress-schedule-activity-public' . $extension, array( 'jquery' ), $this->version, true );

			if ( ! empty( $bp ) && $bp->current_component === 'schedule-activity' ) {
				if ( ! wp_script_is( 'bp-nouveau-activity', 'enqueued' ) ) {
					wp_enqueue_script( 'bp-nouveau-activity' );
				}
				if ( function_exists( 'buddypress' ) && buddypress()->buddyboss ) {
					wp_dequeue_script( 'bp-livestamp' );
				}
			}

			$active_template = get_option( '_bp_theme_package_id' );
			$nouveau         = '';
			if ( 'legacy' === $active_template ) {
				$nouveau = false;
			} elseif ( 'nouveau' === $active_template ) {
				$nouveau = true;
			}

			$user_id     = bp_displayed_user_id();
			$total_posts = $this->bp_get_total_schedule_activity( $user_id );

			$args_localize = $this->buddypress_schedule_activity_get_js_strings(
				array(
					'ajax_url'                 => admin_url( 'admin-ajax.php' ),
					'ajax_nonce'               => wp_create_nonce( 'bpsa_ajax_security' ),
					'nouveau'                  => $nouveau,
					'confirm'                  => esc_html__( 'Are you sure you want to delete this scheduled activity?', 'buddypress-schedule-activity' ),
					'buddyboss'                => ( class_exists( 'buddypress' ) ) ? buddypress()->buddyboss : '',
					'youzify'                  => ( class_exists( 'Youzify' ) ) ? 'youzify' : '',
					'add_schedule_text'        => esc_html__( 'Schedule an activity', 'buddypress-schedule-activity' ),
					'button_schedule_text'     => esc_html__( 'Schedule', 'buddypress-schedule-activity' ),
					'schedule_activity_string' => esc_html__( 'Scheduled For:', 'buddypress-schedule-activity' ),
					'schedule_activity_timeat' => esc_html__( 'at', 'buddypress-schedule-activity' ),
					'count'                    => $total_posts,
					'bb_theme'                 => ( wp_get_theme()->get( 'Name' ) === 'BuddyBoss Theme' || wp_get_theme()->get( 'Name' ) === 'BuddyBoss Child' ),
				)
			);
			wp_localize_script(
				$this->plugin_name,
				'bpsa_ajax_object',
				$args_localize
			);
		}
	}

	/**
	 * Load localizations for topic script.
	 *
	 * These localizations require information that may not be loaded even by init.
	 *
	 * @since 1.0.0
	 */
	function buddypress_schedule_activity_get_js_strings( $params ) {

		$params['wpTime']     = $params['wpTime'] ?? current_time( 'Y-m-d H:i:s' );
		$params['wpTimezone'] = $params['wpTimezone'] ?? bp_get_option( 'timezone_string' );

		$activity_params = array(
			'scheduled_post_nonce'   => wp_create_nonce( 'scheduled_post_nonce' ),
			'scheduled_post_enabled' => true,
			// 'can_schedule_in_feed'   => bb_can_user_schedule_activity(),
		);

		$activity_strings = array(
			'schedulePostButton'        => esc_html__( 'Schedule', 'buddypress-schedule-activity' ),
			'confirmDeletePost'         => esc_html__( 'Are you sure you want to delete that permanently?', 'buddypress-schedule-activity' ),
			'scheduleWarning'           => esc_html__( 'Schedule time is in the past', 'buddypress-schedule-activity' ),
			'successDeletionTitle'      => esc_html__( 'Scheduled Activity Deleted', 'buddypress-schedule-activity' ),
			'successDeletionDesc'       => esc_html__( 'Your scheduled activity has been deleted.', 'buddypress-schedule-activity' ),
			'successScheduleTitle'      => esc_html__( 'Activity scheduled successfully', 'buddypress-schedule-activity' ),
			'successScheduleDesc'       => esc_html__( 'Your activity has been scheduled.', 'buddypress-schedule-activity' ),
			'EditSuccessScheduleTitle'  => esc_html__( 'Activity Updated Successfully', 'buddypress-schedule-activity' ),
			'EditSuccessScheduleDesc'   => esc_html__( 'Your activity schedule has been updated.', 'buddypress-schedule-activity' ),
			'EditViewSchedulePost'      => esc_html__( 'View now', 'buddypress-schedule-activity' ),
			'viewSchedulePosts'         => esc_html__( 'View all activities', 'buddypress-schedule-activity' ),
			'activity_schedule_enabled' => true,
			'notAllowScheduleWarning'   => esc_html__( 'Unable to schedule activity. You must be the group owner or a moderator to schedule activities.', 'buddypress-schedule-activity' ),
		);

		if ( ! empty( $params['activity_schedule']['params'] ) ) {
			$params['activity_schedule']['params'] = array_merge( $params['activity_schedule']['params'], $activity_params );
		} else {
			$params['activity_schedule']['params'] = $activity_params;
		}

		if ( ! empty( $params['activity_schedule']['strings'] ) ) {
			$params['activity_schedule']['strings'] = array_merge( $params['activity_schedule']['strings'], $activity_strings );
		} else {
			$params['activity_schedule']['strings'] = $activity_strings;
		}

		return $params;
	}

	/**
	 * Function to render schedule activity html.
	 *
	 * @since    1.0.0
	 */
	public function buddypress_schedule_activity_update_icon() {
		$current_user_id = get_current_user_id();
		$member_link     = function_exists( 'bp_members_get_user_url' ) ? bp_members_get_user_url( $current_user_id ) : bp_core_get_user_domain( $current_user_id );
		?>
		<div id="bp-activity-schedule-posts" class="bp-activity-schedule-posts post-elements-buttons-item">
			<div class="bp-activity-schedule-post_dropdown-html">
				<a href="javascript:void(0);" class="bp-activity-schedule-icon bp-tooltip" data-bp-tooltip-pos="up" data-bp-tooltip="<?php esc_attr_e( 'Add a scheduled activity', 'buddypress-schedule-activity' ); ?>"><i class="dashicons dashicons-clock"></i></a>
				
				<div class="bp-activity-schedule-post-dropdown-list">
					<ul>
						<li>
							<a href="#" class="bp-activity-schedule-post-action"><i class="dashicons dashicons-calendar"></i><?php echo esc_html__( 'Schedule Activity', 'buddypress-schedule-activity' ); ?>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( $member_link . 'schedule-activity/' ); ?>" id="bp-activity-view-schedule-posts" class="bp-activity-view-schedule-posts"><i class="dashicons dashicons-edit"></i><?php echo esc_html__( 'View Scheduled Activities', 'buddypress-schedule-activity' ); ?>
							</a>
						</li>
					</ul>
				</div>				
			</div>
			<?php
			if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) {
				$this->buddypress_schedule_activity_update_html();
			}
			?>
		</div>		
		<?php
	}

	/**
	 * Function to render schedule activity html.
	 *
	 * @since    1.0.0
	 */
	public function buddypress_schedule_activity_update_html() {
		$formatted_date = wp_date( get_option( 'date_format' ) );
		$formatted_time = wp_date( get_option( 'time_format' ) );
		?>
				
		<div class="bp-activity-schedule-post-modal">
			<div class="bp-activity-schedule-action-popup" id="bp-schedule-activity-form-modal" style="display: none">
				<div class="bp-activity-schedule-model-wrap">
					<div class="modal-wrapper">
						<div class="modal-container">
							<header class="bp-model-header">
								<h4>
									<span class="target_name"><?php echo esc_html__( 'Schedule activity', 'buddypress-schedule-activity' ); ?></span>
								</h4>
								<a class="bp-model-close-button" href="#">
									<span class="dashicons dashicons-no-alt"></span>
								</a>
							</header>
							<div class="bp-modal-content">
								<p class="schedule-date">
									<?php echo esc_html( $formatted_date ); ?> <?php echo esc_html__( 'at', 'buddypress-schedule-activity' ); ?>
									<span class="bp-server-time"><?php echo esc_html( $formatted_time ); ?></span>
								</p>
								<input type="hidden" name="bp_schedule_activity_type" value="">
								<label><?php echo esc_html__( 'Date', 'buddypress-schedule-activity' ); ?></label>
								<div class="input-field">
									<input type="text" name="bp_schedule_activity_date" class="bp-schedule-activity-date-field" placeholder="yyyy-mm-dd" value="">
									<i class="dashicons dashicons-calendar"></i>
								</div>
								
								<label><?php echo esc_html__( 'Time', 'buddypress-schedule-activity' ); ?></label>
								<div class="input-field-inline">
									<div class="input-field bp-schedule-activity-time-wrap">
										<input type="text" name="bp_schedule_activity_time" class="bp-schedule-activity-time-field" placeholder="hh:mm" value="">
										<i class="dashicons dashicons-clock"></i>
									</div>
									<div class="input-field bp-schedule-activity-meridian-wrap">
										<label for="bp-schedule-activity-meridian-am">
											<input type="radio" value="am" id="bp-schedule-activity-meridian-am" name="bp_schedule_activity_meridian">
											<span class="bp-time-meridian"><?php echo esc_html__( 'AM', 'buddypress-schedule-activity' ); ?></span>
										</label>
										<label for="bp-schedule-activity-meridian-pm">
											<input type="radio" value="pm" id="bp-schedule-activity-meridian-pm" name="bp_schedule_activity_meridian" checked >
											<span class="bp-time-meridian"><?php echo esc_html__( 'PM', 'buddypress-schedule-activity' ); ?></span>
										</label>
										<?php wp_nonce_field( 'buddypress-schedule-activity', 'buddypress-schedule-activity' ); ?>
									</div>
								</div>
							</div>
							
							<footer class="bp-model-footer">										
								<div>
									<a style="display:none;" href="#" class="bp-schedule-activity-clear"><?php echo esc_html__( 'Clear Schedule', 'buddypress-schedule-activity' ); ?></a>
								</div>
								
								<div>
									<button type="button" class="button button-outline bp-schedule-activity-cancel"><?php echo esc_html__( 'Back', 'buddypress-schedule-activity' ); ?></button>
									<button type="button" class="button bp-schedule-activity" disabled><?php echo esc_html__( 'Next', 'buddypress-schedule-activity' ); ?></button>
								</div>
							</footer>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Action performed to save the activity meta for schedule activity
	 *
	 * @param array  $args The actvity content.
	 * @param string $content The actvity content.
	 * @param int    $user_id User id.
	 * @param int    $activity_id Activity id.
	 * @param int    $group_id Group id.
	 * @since 1.0.0
	 */
	public function buddypress_schedule_activity_post( $args ) {
		$activity_id = isset( $args['activity_id'] ) ? absint( $args['activity_id'] ) : 0;
		if ( ! isset( $_POST['buddypress-schedule-activity'] ) || ! wp_verify_nonce( $_POST['buddypress-schedule-activity'], 'buddypress-schedule-activity' ) ) {	// phpcs:ignore
			return;
		}

		// Security check: Verify current user owns this activity.
		if ( $activity_id > 0 ) {
			$activity = new BP_Activity_Activity( $activity_id );
			if ( empty( $activity->id ) || (int) $activity->user_id !== get_current_user_id() ) {
				return; // Activity doesn't exist or user doesn't own it.
			}
		}

		$activity_action_type = ( isset( $_POST['bp_schedule_activity_type'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bp_schedule_activity_type'] ) ) : '';
		if ( ! empty( $activity_action_type ) && 'scheduled' === $activity_action_type ) {
			global $wpdb;
			$activity_status            = 'scheduled';
			$activity_schedule_date_raw = ( isset( $_POST['bp_schedule_activity_date'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bp_schedule_activity_date'] ) ) : '';
			$activity_schedule_time     = ( isset( $_POST['bp_schedule_activity_time'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bp_schedule_activity_time'] ) ) : '';

			$activity_schedule_meridiem = ( isset( $_POST['bp_schedule_activity_meridian'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bp_schedule_activity_meridian'] ) ) : '';

			if ( ! empty( $activity_schedule_date_raw ) && ! empty( $activity_schedule_time ) && ! empty( $activity_schedule_meridiem ) ) {

				// Validate date format (YYYY-MM-DD).
				if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $activity_schedule_date_raw ) ) {
					return; // Invalid date format.
				}

				// Validate the date is a real date.
				$date_parts = explode( '-', $activity_schedule_date_raw );
				if ( ! checkdate( (int) $date_parts[1], (int) $date_parts[2], (int) $date_parts[0] ) ) {
					return; // Invalid date.
				}

				// Validate time format (H:MM or HH:MM).
				if ( ! preg_match( '/^\d{1,2}:\d{2}$/', $activity_schedule_time ) ) {
					return; // Invalid time format.
				}

				// Validate meridiem.
				if ( ! in_array( strtolower( $activity_schedule_meridiem ), array( 'am', 'pm' ), true ) ) {
					return; // Invalid meridiem.
				}

				// Convert 12-hour time format to 24-hour time format using WordPress timezone.
				$time_string = $activity_schedule_time . ' ' . $activity_schedule_meridiem;
				$parsed_time = strtotime( $time_string );
				if ( false === $parsed_time ) {
					return; // Invalid time format.
				}
				$activity_schedule_time_24hr = wp_date( 'H:i', $parsed_time );

				// Combine date and time (stored in WordPress site timezone).
				$activity_datetime = $activity_schedule_date_raw . ' ' . $activity_schedule_time_24hr . ':00';

				$update = $wpdb->get_results(
					$wpdb->prepare(
						"UPDATE {$wpdb->prefix}bp_activity SET date_recorded = %s WHERE id = %d",
						$activity_datetime,
						$activity_id
					)
				);

				bp_activity_update_meta( $activity_id, '_bp_activity_status', $activity_status );
				if ( class_exists( 'Youzify' ) ) {
					bp_activity_update_meta( $activity_id, '_bp_schedule_activity_title_for_youzify', true );
				}

				if ( ! function_exists( 'bp_members_get_user_url' ) ) {
					$member_link = bp_core_get_user_domain( get_current_user_id() );
				} else {
					$member_link = bp_members_get_user_url( get_current_user_id() );
				}

				do_action( 'youzify_after_adding_wall_post', $activity_id );

				wp_send_json_success(
					array(
						'id'          => $activity_id,
						'message'     => sprintf(
							'<div class="bp-schedule-activity-posted"><div class="bp-schedule-item-list pull-animation"><div class="bp-schedule-messages-icon"><span class="dashicons dashicons-yes-alt"></span></div><div class="bp-schedule-messages-content"><span class="title">%s</span><span>%s <a href="%s" class="just-posted">%s</a></span></div></div></div>',
							esc_html__( 'Activity scheduled successfully', 'buddypress-schedule-activity' ),
							esc_html__( 'Your activity has been scheduled successfully.', 'buddypress-schedule-activity' ),
							esc_url( $member_link . 'schedule-activity/' ),
							esc_html__( 'View all scheduled activities.', 'buddypress-schedule-activity' )
						),
						'is_schedule' => true,
					)
				);
			}
		}
	}

	/**
	 * Action performed to save the activity meta on schedule post.
	 *
	 * @param string $content The actvity content.
	 * @param int    $user_id User id.
	 * @param int    $activity_id Activity id.
	 * @since 1.0.0
	 */
	public function buddypress_schedule_activity_posted_update( $content, $user_id, $activity_id ) {
		global $wpdb;
		if ( function_exists( 'buddypress' ) && buddypress()->buddyboss ) {
			$privacy = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT privacy FROM {$wpdb->prefix}bp_activity WHERE id = %d",
					$activity_id
				)
			);
		} else {
			$privacy = 'activity_update';
		}
		if ( ! empty( $privacy ) && 'media' !== $privacy ) {
			$args = array(
				'content'     => $content,
				'user_id'     => $user_id,
				'activity_id' => $activity_id,
			);
			$this->buddypress_schedule_activity_post( $args );
		}
	}


	/**
	 * Action performed to save the group activity meta on schedule post.
	 *
	 * @param string $content The actvity content.
	 * @param int    $user_id User id.
	 * @param int    $group_id Group id.
	 * @param int    $activity_id Activity id.
	 * @since 1.0.0
	 */
	public function buddypress_schedule_activity_groups_posted_update( $content, $user_id, $group_id, $activity_id ) {
		global $wpdb;

		// Security check: Verify user can post to this group.
		if ( ! empty( $group_id ) ) {
			// Check if user is a member of the group.
			if ( function_exists( 'groups_is_user_member' ) && ! groups_is_user_member( $user_id, $group_id ) ) {
				return;
			}

			// For BuddyBoss, check if user is allowed to post in the group.
			if ( function_exists( 'bp_group_is_user_allowed_posting' ) && ! bp_group_is_user_allowed_posting( $user_id, $group_id ) ) {
				return;
			}
		}

		if ( function_exists( 'buddypress' ) && buddypress()->buddyboss ) {
			$privacy = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT privacy FROM {$wpdb->prefix}bp_activity WHERE id = %d",
					$activity_id
				)
			);
		} else {
			$privacy = 'activity_update';
		}
		if ( ! empty( $privacy ) && 'media' !== $privacy ) {
			$args = array(
				'content'     => $content,
				'user_id'     => $user_id,
				'activity_id' => $activity_id,
				'group_id'    => $group_id,
			);
			$this->buddypress_schedule_activity_post( $args );
		}
	}


	/**
	 * Filters the arguments  pass to check activity status exisit or not.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of parsed arguments.
	 */
	public function buddypress_schedule_activity_meta_query( $args ) {

		if ( isset( $_POST['scope'] ) && $_POST['scope'] === 'schedule-activity' ) { //phpcs:ignore
			return $args;
		}

		if ( isset( $args['scope'] ) && $args['scope'] === 'schedule-activity' ) {
			return $args;
		}

		// Check if privacy is disabled rtmedia
		$privacy_savedSettings = $this->bp_schedule_activity_get_rtmedia_privacy_setting();
		if ( ! $privacy_savedSettings ) {
			if ( isset( $args['meta_query'] ) && $args['meta_query'] == false ) {
				$args['meta_query'] = array();
			}
			if ( ! isset( $args['meta_query'] ) ) {
				$args['meta_query'] = array();
			}

			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][]           = array(
				'key'     => '_bp_activity_status',
				'compare' => 'NOT EXISTS',
			);
		}

		/* Set activbity ids 0 for specific activity */
		if ( isset( $_POST['bp_schedule_activity_type'] ) && $_POST['bp_schedule_activity_type'] == 'scheduled' ) {//phpcs:ignore
			$args['activity_ids'] = array( 0 );
		}

		return $args;
	}

	/**
	 * Add WHERE condition to exclude scheduled activities.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $where_conditions Array of WHERE conditions.
	 * @param array  $r Array of parsed arguments.
	 * @param string $select_sql SELECT clause.
	 * @param string $from_sql FROM clause.
	 * @param string $join_sql JOIN clause.
	 *
	 * @return array Modified WHERE conditions.
	 */
	public function buddypress_schedule_activity_where_conditions( $where_conditions, $r, $select_sql, $from_sql, $join_sql ) {

		global $wpdb;

		// Check if rtMedia is active and privacy is enabled
		$privacy_savedSettings = $this->bp_schedule_activity_get_rtmedia_privacy_setting();
		if ( $privacy_savedSettings ) {
			// Check if we're viewing scheduled activities
			$is_scheduled_scope = false;

			if ( isset( $_POST['scope'] ) && $_POST['scope'] === 'schedule-activity' ) { //phpcs:ignore
				$is_scheduled_scope = true;
			}

			if ( isset( $r['scope'] ) && $r['scope'] === 'schedule-activity' ) {
				$is_scheduled_scope = true;
			}

			if ( $is_scheduled_scope ) {
				// Show only scheduled activities
				$where_conditions[] = "EXISTS (
					SELECT 1 FROM {$wpdb->prefix}bp_activity_meta am
					WHERE am.activity_id = a.id
					AND am.meta_key = '_bp_activity_status'
					AND am.meta_value = 'scheduled'
				)";
				return $where_conditions;
			}

			// For all other pages: hide scheduled activities
			$where_conditions[] = "NOT EXISTS (
				SELECT 1 FROM {$wpdb->prefix}bp_activity_meta am
				WHERE am.activity_id = a.id
				AND am.meta_key = '_bp_activity_status'
			)";

		}

		return $where_conditions;
	}

	/**
	 * Fix Load More button for scheduled activities by overriding RTMedia's forced true value.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $has_more_items Whether there are more items (potentially overridden by RTMedia).
	 *
	 * @return bool
	 */
	public function buddypress_schedule_activity_has_more_items( $has_more_items ) {
		global $activities_template;

		// Check if rtMedia is active and privacy is enabled true
		$privacy_savedSettings = $this->bp_schedule_activity_get_rtmedia_privacy_setting();
		if ( $privacy_savedSettings ) {
			// Check if we're in scheduled activity context
			$is_scheduled_context = false;

			// Check scope in template
			if ( isset( $activities_template->scope ) && $activities_template->scope === 'schedule-activity' ) {
				$is_scheduled_context = true;
			}

			// Check POST scope
			if ( isset( $_POST['scope'] ) && $_POST['scope'] === 'schedule-activity' ) { //phpcs:ignore
				$is_scheduled_context = true;
			}

			// Check if we're on the scheduled posts page
			if ( function_exists( 'bp_current_action' ) && bp_current_action() === 'schedule-activity' ) {
				$is_scheduled_context = true;
			}

			// Only override for scheduled activity scope
			if ( $is_scheduled_context ) {
				// If template exists and has the property, use it
				if ( isset( $activities_template->has_more_items ) ) {
					return $activities_template->has_more_items;
				} else {
					// Template doesn't exist yet, return false (no more items)
					return false;
				}
			}
		}

		return $has_more_items;
	}

	/**
	 * Add schedule to cron schedules.
	 *
	 * @param array $schedules Array of schedules for cron.
	 *
	 * @return array $schedules Array of schedules from cron.
	 * @since 1.0.0
	 */
	public function buddypress_schedule_activity_cron_schedules( $schedules ) {
		$bp_schedules = array(
			'bp_every_min' => array(
				'interval' => MINUTE_IN_SECONDS,
				'display'  => __( 'Every minute', 'buddypress-schedule-activity' ),
			),
		);

		$bp_schedules = apply_filters( 'bp_core_cron_schedules', $bp_schedules );

		foreach ( $bp_schedules as $k => $bp_schedule ) {
			if ( ! isset( $schedules[ $k ] ) ) {
				$schedules[ $k ] = array(
					'interval' => $bp_schedule['interval'],
					'display'  => $bp_schedule['display'],
				);
			}
		}

		return $schedules;
	}

	/**
	 * Create activity schedule cron event if not exists.
	 *
	 * @since 1.0.0
	 */
	public function buddypress_schedule_activity_init() {

		if ( ! wp_next_scheduled( 'buddypress_schedule_activity_publish' ) ) {
			$frequency = apply_filters( 'buddypress_schedule_activity_frequency', 'bp_every_min' );
			wp_schedule_event( time(), $frequency, 'buddypress_schedule_activity_publish' );
		}
	}

	/**
	 * Get all the scheduled activities and publish it.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function buddypress_check_schedule_activity_publish() {
		global $wpdb;

		// Prevent concurrent execution using a transient lock.
		$lock_key = 'bpsa_publish_lock';
		if ( get_transient( $lock_key ) ) {
			return; // Another process is already running.
		}

		// Set lock for 60 seconds (should be enough for processing).
		set_transient( $lock_key, true, 60 );

		try {
			$current_time = current_time( 'Y-m-d H:i:s' );

			$activities = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT a.id
					 FROM {$wpdb->prefix}bp_activity a
					 LEFT JOIN {$wpdb->prefix}bp_activity_meta am ON ( a.id = am.activity_id )
					 WHERE date_recorded <= %s AND am.meta_key = '_bp_activity_status' AND am.meta_value = %s",
					$current_time,
					'scheduled'
				)
			);

			// Check for database errors.
			if ( $wpdb->last_error ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'BPSA Cron Error: Failed to fetch scheduled activities - ' . $wpdb->last_error );
				return;
			}

			if ( ! empty( $activities ) ) {
				foreach ( $activities as $scheduled_activity ) {
					// Atomically mark as publishing to prevent race condition.
					$updated = $wpdb->update(
						"{$wpdb->prefix}bp_activity_meta",
						array( 'meta_value' => 'publishing' ),
						array(
							'activity_id' => $scheduled_activity->id,
							'meta_key'    => '_bp_activity_status',
							'meta_value'  => 'scheduled',
						),
						array( '%s' ),
						array( '%d', '%s', '%s' )
					);

					// Check for update errors.
					if ( false === $updated ) {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
						error_log( 'BPSA Cron Error: Failed to mark activity ' . $scheduled_activity->id . ' as publishing - ' . $wpdb->last_error );
						continue;
					}

					// Only process if we successfully marked it (prevents duplicate processing).
					if ( $updated > 0 ) {
						$activity = new BP_Activity_Activity( $scheduled_activity->id );
						if ( ! empty( $activity->id ) ) {
							// Remove the status meta entirely.
							$meta_deleted = bp_activity_delete_meta( $activity->id, '_bp_activity_status' );
							if ( ! $meta_deleted ) {
								// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
								error_log( 'BPSA Cron Warning: Failed to delete status meta for activity ' . $activity->id );
							}

							// Update date_recorded to current time.
							$result = $wpdb->update(
								"{$wpdb->prefix}bp_activity",
								array( 'date_recorded' => current_time( 'mysql' ) ),
								array( 'id' => $activity->id ),
								array( '%s' ),
								array( '%d' )
							);

							if ( false === $result ) {
								// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
								error_log( 'BPSA Cron Error: Failed to update date_recorded for activity ' . $activity->id . ' - ' . $wpdb->last_error );
							}
						} else {
							// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
							error_log( 'BPSA Cron Warning: Activity ' . $scheduled_activity->id . ' not found when publishing' );
						}
					}
				}
			}
		} finally {
			// Always release the lock.
			delete_transient( $lock_key );
		}
	}

	/**
	 * Get total no. of Posts  posted by a user
	 *
	 * @param int  $user_id user id.
	 * @param bool $is_my_profile Is user profile.
	 *
	 * @return int
	 */
	public function bp_get_total_schedule_activity( $user_id = 0 ) {
		// Needs revisit.
		global $wpdb;

		if ( ! $user_id ) {
			$user_id = bp_displayed_user_id();
		}

		$count = $wpdb->get_var(
			$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT count(a.id) 
					FROM {$wpdb->prefix}bp_activity a
					LEFT JOIN {$wpdb->prefix}bp_activity_meta am ON ( a.id = am.activity_id )
					WHERE user_id = %d AND am.meta_key = '_bp_activity_status' AND am.meta_value= %s",
				$user_id,
				'scheduled',
			)
		);

		return intval( $count );
	}

	/**
	 * Setup BuddyPress navigation
	 * Sets up user tabs
	 *
	 * @since 1.0.0
	 */
	public function buddypress_schedule_activity_setup_nav() {
		global $bp, $current_user;

		$user_id       = bp_displayed_user_id();
		$is_my_profile = bp_is_my_profile();

		// Only show on own profile.
		if ( ! $is_my_profile ) {
			return;
		}

		$total_posts = $this->bp_get_total_schedule_activity( $user_id );

		// translators: %s is replaced with a count of total posts.
		$schedule_activity_label = apply_filters(
			'buddypress_schedule_activity_label',
			sprintf(
				esc_html__( 'Scheduled Posts %s', 'buddypress-schedule-activity' ),
				'<span class="count">' . esc_html( $total_posts ) . '</span>'
			)
		);

		// Removed <span> tag for buddyboss as it strips out <span> tag from navigation label.
		if ( function_exists( 'buddypress' ) && ( isset( buddypress()->buddyboss ) ) ) {
			$schedule_activity_label = apply_filters( 'buddypress_schedule_activity_label', esc_html__( 'Scheduled Posts', 'buddypress-schedule-activity' ) );
		}

		add_filter( 'bp_activity_time_since', array( $this, 'buddypress_schedule_activity_time_since' ), 100, 2 );

		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			add_filter( 'bb_nouveau_get_activity_entry_bubble_buttons', array( $this, 'buddypress_schedule_activity_show_delete_bubble_buttons' ), 100, 2 );
			add_filter( 'bp_core_time_since', array( $this, 'buddyboss_schedule_activity_time_since' ), 100, 3 );
		} else {
			add_filter( 'bp_nouveau_get_activity_entry_buttons', array( $this, 'buddypress_schedule_activity_show_delete_button' ), 100, 2 );
		}

		if ( class_exists( 'REIGN_Theme_Class' ) ) {
			add_filter( 'bp_core_time_since', array( $this, 'buddyboss_schedule_activity_time_since' ), 100, 3 );
		}

		$schedule_activity_slug = apply_filters( 'buddypress_schedule_activity_slug', 'schedule-activity' );

		if ( is_user_logged_in() ) {
			bp_core_new_nav_item(
				array(
					'name'                => $schedule_activity_label,
					'slug'                => $schedule_activity_slug,
					'screen_function'     => array( $this, 'buddypress_schedule_activity_view_lists' ),
					'default_subnav_slug' => $schedule_activity_slug,
					'position'            => 80,
					'item_css_id'         => 'bp-schedule-activity',
				)
			);

			// Add subnav for proper page title.
			bp_core_new_subnav_item(
				array(
					'name'            => __( 'Scheduled Posts', 'buddypress-schedule-activity' ),
					'slug'            => $schedule_activity_slug,
					'parent_url'      => trailingslashit( bp_loggedin_user_domain() . $schedule_activity_slug ),
					'parent_slug'     => $schedule_activity_slug,
					'screen_function' => array( $this, 'buddypress_schedule_activity_view_lists' ),
					'position'        => 10,
				)
			);
		}
	}

	/**
	 * Handles user schedule activity screen
	 *
	 * @since 1.0.0
	 */
	public function buddypress_schedule_activity_view_lists() {
		add_action( 'bp_template_content', array( $this, 'load_buddypress_schedule_activity_view_lists_nav_content' ) );
		bp_core_load_template( 'members/single/plugins' );
	}

	/**
	 * Display schedule activity template layout
	 *
	 * @since 1.0.0
	 */
	public function load_buddypress_schedule_activity_view_lists_nav_content() {

		$page                           = ( isset( $_POST['page'] ) && ! empty( $_POST['page'] ) ) ? absint( $_POST['page'] ) : 1;//phpcs:ignore
		$args['page']    = $page;
		$args['scope']   = 'schedule-activity';
		$args['user_id'] = get_current_user_id();

		// Check if privacy key exists and is disabled (value = '0') false
		$privacy_savedSettings = $this->bp_schedule_activity_get_rtmedia_privacy_setting();
		if ( ! $privacy_savedSettings ) {
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][]           = array(
				'key'     => '_bp_activity_status',
				'value'   => 'scheduled',
				'compare' => '=',
			);
		}

		$args['show_hidden'] = true;

		add_filter( 'bp_activity_time_since', array( $this, 'buddypress_schedule_activity_time_since' ), 100, 2 );
		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {

			add_filter( 'bb_nouveau_get_activity_entry_bubble_buttons', array( $this, 'buddypress_schedule_activity_show_delete_bubble_buttons' ), 100, 2 );
			add_filter( 'bp_core_time_since', array( $this, 'buddyboss_schedule_activity_time_since' ), 100, 3 );
		} else {
			add_filter( 'bp_nouveau_get_activity_entry_buttons', array( $this, 'buddypress_schedule_activity_show_delete_button' ), 100, 2 );
		}
		if ( class_exists( 'REIGN_Theme_Class' ) ) {
			add_filter( 'bp_core_time_since', array( $this, 'buddyboss_schedule_activity_time_since' ), 100, 3 );
		}

		$bp_current_component = static function ( $current_component ) {
			return $current_component = 'activity';
		};

		add_filter( 'bp_current_component', $bp_current_component, 99, 2 );

		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			$class[] = 'bb-platform';
		} else {
			$class[] = 'buddypress';
		}

		$user_id     = bp_displayed_user_id();
		$total_posts = $this->bp_get_total_schedule_activity( $user_id );

		?>

		<?php if ( wp_get_theme()->get( 'Name' ) === 'BuddyBoss Theme' || wp_get_theme()->get( 'Name' ) === 'BuddyBoss Child' ) { ?>
		<div class="bb-item-count">
			<span class="bb-count"><?php echo esc_html( $total_posts ); ?></span>
			<?php esc_html_e( 'Scheduled posts', 'buddypress-schedule-activity' ); ?>
		</div>
		<?php } ?>


		<div class="buddypress-schedule-activity-lists <?php echo esc_attr( join( ' ', $class ) ); ?>">		
			<?php
			if ( bp_has_activities( $args ) ) {
				echo '<div id="activity-stream" class="schedule-activity" data-bp-list="activity" data-ajax="false">';
				echo '<ul class="activity-list item-list bp-list">';
				while ( bp_activities() ) {
					bp_the_activity();
					bp_get_template_part( 'activity/entry' );
				}

				if ( bp_activity_has_more_items() ) :
					?>

					<li class="bp-schedule-activty-load-more load-more">
						<a href="<?php bp_activity_load_more_link(); ?>" class="button outline"><?php echo esc_html_x( 'Load More', 'button', 'buddypress-schedule-activity' ); ?></a>
					</li>

					<?php
				endif;

				echo '</ul>';
				echo '</div>';
			} else {
				?>
				<div class="bp-feedback bp-messages info">
					<span class="bp-icon" aria-hidden="true"></span>
					<p><?php esc_html_e( 'You don\'t have any scheduled activities at the moment.', 'buddypress-schedule-activity' ); ?></p>
				</div>
			<?php } ?>
		</div>
		<?php
		remove_filter( 'bp_current_component', $bp_current_component, 99, 2 );
	}

	public function buddypress_get_load_more_schedule_activity() {

		// Verify nonce for CSRF protection.
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'bpsa_ajax_security' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'buddypress-schedule-activity' ) ) );
		}

		// Verify user is logged in.
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in.', 'buddypress-schedule-activity' ) ) );
		}

		$page            = ( isset( $_POST['page'] ) && ! empty( $_POST['page'] ) ) ? absint( $_POST['page'] ) : 1;
		$args['page']    = $page;
		$args['scope']   = 'schedule-activity';
		$args['user_id'] = get_current_user_id();

		// Check if privacy key exists and is disabled (value = '0') false
		$privacy_savedSettings = $this->bp_schedule_activity_get_rtmedia_privacy_setting();

		if ( ! $privacy_savedSettings ) {
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][]           = array(
				'key'     => '_bp_activity_status',
				'value'   => 'scheduled',
				'compare' => '=',
			);
		}

		$args['show_hidden'] = true;
		add_filter( 'bp_activity_time_since', array( $this, 'buddypress_schedule_activity_time_since' ), 100, 2 );
		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {

			add_filter( 'bb_nouveau_get_activity_entry_bubble_buttons', array( $this, 'buddypress_schedule_activity_show_delete_bubble_buttons' ), 100, 2 );
			add_filter( 'bp_core_time_since', array( $this, 'buddyboss_schedule_activity_time_since' ), 100, 3 );
		} else {
			add_filter( 'bp_nouveau_get_activity_entry_buttons', array( $this, 'buddypress_schedule_activity_show_delete_button' ), 100, 2 );
		}

		if ( class_exists( 'REIGN_Theme_Class' ) ) {
			add_filter( 'bp_core_time_since', array( $this, 'buddyboss_schedule_activity_time_since' ), 100, 3 );
		}

		$bp_current_component = static function ( $current_component ) {
			return $current_component = 'activity';
		};

		add_filter( 'bp_current_component', $bp_current_component, 99, 2 );

		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			$class[] = 'bb-platform';
		} else {
			$class[] = 'buddypress';
		}

		ob_start();
		?>

		<div class="buddypress-schedule-activity-lists <?php echo esc_attr( join( ' ', $class ) ); ?>">		
			<?php
			if ( bp_has_activities( $args ) ) {
				echo '<div id="activity-stream" class="schedule-activity" data-bp-list="activity" data-ajax="false">';
				echo '<ul class="activity-list item-list bp-list">';
				while ( bp_activities() ) {
					bp_the_activity();
					bp_get_template_part( 'activity/entry' );
				}

				if ( bp_activity_has_more_items() ) :
					?>

					<li class="bp-schedule-activty-load-more load-more">
						<a href="<?php bp_activity_load_more_link(); ?>"><?php echo esc_html_x( 'Load More', 'button', 'buddypress-schedule-activity' ); ?></a>
					</li>

					<?php
				endif;

				echo '</ul>';
				echo '</div>';
			} else {
				?>
				<div class="bp-feedback bp-messages info">
					<span class="bp-icon" aria-hidden="true"></span>
					<p><?php esc_html_e( 'You don\'t have any scheduled activities at the moment.', 'buddypress-schedule-activity' ); ?></p>
				</div>
			<?php } ?>
		</div>
		
		<?php
		$result['contents'] = ob_get_contents();
		ob_end_clean();
		remove_filter( 'bp_current_component', $bp_current_component, 99, 2 );
		wp_send_json_success( $result );
	}

	public function buddypress_schedule_activity_show_delete_bubble_buttons( $buttons, $activity_id ) {
		if ( ! empty( $buttons ) ) {
			foreach ( $buttons as $key => $button ) {
				if ( $key != 'activity_delete' ) {
					unset( $buttons[ $key ] );
				}
			}
		}
		return $buttons;
	}
	public function buddypress_schedule_activity_show_delete_button( $buttons, $activity_id ) {
		if ( ! empty( $buttons ) ) {
			foreach ( $buttons as $key => $button ) {
				if ( $key != 'activity_delete' && ! in_array( get_option( 'template' ), array( 'reign-theme', 'BuddyxPro' ) ) ) {
					unset( $buttons[ $key ] );
				}
			}
		}
		return $buttons;
	}

	/**
	 * Display schedule activity time
	 *
	 * @since 1.0.0
	 */
	public function buddyboss_schedule_activity_time_since( $output, $older_date, $newer_date ) {

		$args = array(
			'older_date' => $older_date,
		);

		if ( $newer_date ) {
			$args['newer_date'] = $newer_date;
		}

		// Calculate the time difference.
		$time_diff = $this->bp_schedule_activity_core_time_diff( $args );

		if ( false === $time_diff ) {
			$local_time_wp = $older_date;
			$date_format   = get_option( 'date_format' );
			$time_format   = get_option( 'time_format' );

			// Format the local time according to the WordPress settings.
			if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
				$formatted_local_time_wp = trim( date_i18n( $date_format . ' \a\t ' . $time_format, $local_time_wp ), ' at ' );
			} else {
				$formatted_local_time_wp = trim( date_i18n( $date_format . ' \a\t ' . $time_format, strtotime( $local_time_wp ) ), ' at ' );
			}
			// Translators: %s is the formated date.
			$output = sprintf( __( 'Scheduled for: %s', 'buddypress-schedule-activity' ), $formatted_local_time_wp );
		}

		return $output;
	}
	/**
	 * Display schedule activity time
	 *
	 * @since 1.0.0
	 */
	public function buddypress_schedule_activity_time_since( $time_since, $activity ) {
		$older_date = $activity->date_recorded;
		$args       = array(
			'older_date' => $older_date,
		);

		// Calculate the time difference.
		$time_diff = $this->bp_schedule_activity_core_time_diff( $args );
		if ( false === $time_diff ) {
			$local_time_wp = $older_date;
			$date_format   = get_option( 'date_format' );
			$time_format   = get_option( 'time_format' );

			// Format the local time according to the WordPress settings.
			$formatted_local_time_wp = trim( date_i18n( $date_format . ' \a\t ' . $time_format, strtotime( $local_time_wp ) ), ' at ' );
			// Translators: %s is the formated date.
			$output     = sprintf( __( 'Schedule for: %s', 'buddypress-schedule-activity' ), $formatted_local_time_wp );
			$time_since = sprintf(
				'<span class="time-since">%1$s</span>',
				$output,
			);
		}
		return $time_since;
	}

	public function bp_schedule_activity_core_time_diff( $args = array() ) {
		$retval = null;
		$r      = bp_parse_args(
			$args,
			array(
				'older_date'  => 0,
				'newer_date'  => bp_core_current_time( true, 'timestamp' ),
				'time_chunks' => 2,
			)
		);

		// Array of time period chunks.
		$chunks = array(
			YEAR_IN_SECONDS,
			30 * DAY_IN_SECONDS,
			WEEK_IN_SECONDS,
			DAY_IN_SECONDS,
			HOUR_IN_SECONDS,
			MINUTE_IN_SECONDS,
			1,
		);

		foreach ( array( 'older_date', 'newer_date' ) as $date ) {
			if ( ! $r[ $date ] ) {
				$r[ $date ] = 0;
				continue;
			}

			if ( preg_match( '/^\d{4}-\d{2}-\d{2}[ ]\d{2}:\d{2}:\d{2}$/', $r[ $date ] ) ) {
				$time_chunks = explode( ':', str_replace( ' ', ':', $r[ $date ] ) );
				$date_chunks = explode( '-', str_replace( ' ', '-', $r[ $date ] ) );
				$r[ $date ]  = gmmktime(
					(int) $time_chunks[1],
					(int) $time_chunks[2],
					(int) $time_chunks[3],
					(int) $date_chunks[1],
					(int) $date_chunks[2],
					(int) $date_chunks[0]
				);
			} elseif ( ! is_int( $r[ $date ] ) ) {
				$r[ $date ] = 0;
			}
		}

		// Difference in seconds.
		$diff = $r['newer_date'] - $r['older_date'];

		/**
		 * We only want to return one or two chunks of time here, eg:
		 * - `array( 'x years', 'xx months' )`,
		 * - `array( 'x days', 'xx hours' )`.
		 * So there's only two bits of calculation below.
		 */
		if ( 0 <= $diff && (int) $r['time_chunks'] ) {
			// Step one: the first chunk.
			for ( $i = 0, $j = count( $chunks ); $i < $j; ++$i ) {
				$seconds = $chunks[ $i ];

				// Finding the biggest chunk (if the chunk fits, break).
				$count = floor( $diff / $seconds );
				if ( 0 != $count ) {
					break;
				}
			}

			// Add the first chunk of time diff.
			if ( isset( $chunks[ $i ] ) ) {
				$retval = array();

				switch ( $seconds ) {
					case YEAR_IN_SECONDS:
						/* translators: %s: the number of years. */
						$retval[] = sprintf( _n( '%s year', '%s years', $count, 'buddypress-schedule-activity' ), $count );
						break;
					case 30 * DAY_IN_SECONDS:
						/* translators: %s: the number of months. */
						$retval[] = sprintf( _n( '%s month', '%s months', $count, 'buddypress-schedule-activity' ), $count );
						break;
					case WEEK_IN_SECONDS:
						/* translators: %s: the number of weeks. */
						$retval[] = sprintf( _n( '%s week', '%s weeks', $count, 'buddypress-schedule-activity' ), $count );
						break;
					case DAY_IN_SECONDS:
						/* translators: %s: the number of days. */
						$retval[] = sprintf( _n( '%s day', '%s days', $count, 'buddypress-schedule-activity' ), $count );
						break;
					case HOUR_IN_SECONDS:
						/* translators: %s: the number of hours. */
						$retval[] = sprintf( _n( '%s hour', '%s hours', $count, 'buddypress-schedule-activity' ), $count );
						break;
					case MINUTE_IN_SECONDS:
						/* translators: %s: the number of minutes. */
						$retval[] = sprintf( _n( '%s minute', '%s minutes', $count, 'buddypress-schedule-activity' ), $count );
						break;
					default:
						/* translators: %s: the number of seconds. */
						$retval[] = sprintf( _n( '%s second', '%s seconds', $count, 'buddypress-schedule-activity' ), $count );
				}

				/**
				 * Step two: the second chunk.
				 *
				 * A quirk in the implementation means that this condition fails in the case of minutes and seconds.
				 * We've left the quirk in place, since fractions of a minute are not a useful piece of information
				 * for our purposes.
				 */
				if ( 2 === (int) $r['time_chunks'] && $i + 2 < $j ) {
					$seconds2 = $chunks[ $i + 1 ];
					$count2   = floor( ( $diff - ( $seconds * $count ) ) / $seconds2 );

					// Add the second chunk of time diff.
					if ( 0 !== (int) $count2 ) {

						switch ( $seconds2 ) {
							case 30 * DAY_IN_SECONDS:
								/* translators: %s: the number of months. */
								$retval[] = sprintf( _n( '%s month', '%s months', $count2, 'buddypress-schedule-activity' ), $count2 );
								break;
							case WEEK_IN_SECONDS:
								/* translators: %s: the number of weeks. */
								$retval[] = sprintf( _n( '%s week', '%s weeks', $count2, 'buddypress-schedule-activity' ), $count2 );
								break;
							case DAY_IN_SECONDS:
								/* translators: %s: the number of days. */
								$retval[] = sprintf( _n( '%s day', '%s days', $count2, 'buddypress-schedule-activity' ), $count2 );
								break;
							case HOUR_IN_SECONDS:
								/* translators: %s: the number of hours. */
								$retval[] = sprintf( _n( '%s hour', '%s hours', $count2, 'buddypress-schedule-activity' ), $count2 );
								break;
							case MINUTE_IN_SECONDS:
								/* translators: %s: the number of minutes. */
								$retval[] = sprintf( _n( '%s minute', '%s minutes', $count2, 'buddypress-schedule-activity' ), $count2 );
								break;
							default:
								/* translators: %s: the number of seconds. */
								$retval[] = sprintf( _n( '%s second', '%s seconds', $count2, 'buddypress-schedule-activity' ), $count2 );
						}
					}
				}
			}
		} else {
			// Something went wrong with date calculation and we ended up with a negative date.
			$retval = false;
		}

		return $retval;
	}

	public function wp_ajax_delete_schedule_activity() {

		$response = array(
			'feedback' => sprintf(
				'<div class="bp-feedback bp-messages error">%s</div>',
				esc_html__( 'There was a problem deleting your scheduled activity. Please try again.', 'buddypress-schedule-activity' )
			),
		);

		// Bail if not a POST action.
		if ( ! bp_is_post_request() ) {
			wp_send_json_error( $response );
		}

		// Nonce check!
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'bp_activity_delete_link' ) ) {	// phpcs:ignore
			wp_send_json_error( $response );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( $response );
		}

		if ( empty( $_POST['id'] ) || ! is_numeric( $_POST['id'] ) ) {
			wp_send_json_error( $response );
		}

		$activity = new BP_Activity_Activity( (int) $_POST['id'] );

		// Verify activity exists before checking permissions.
		if ( empty( $activity->id ) ) {
			wp_send_json_error( $response );
		}

		// Check access.
		if ( ! bp_activity_user_can_delete( $activity ) ) {
			wp_send_json_error( $response );
		}

		/** This action is documented in bp-activity/bp-activity-actions.php */
		do_action( 'bp_activity_before_action_delete_activity', $activity->id, $activity->user_id );

		// Deleting an activity comment.
		if ( ! empty( $_POST['is_comment'] ) ) {
			// Get replies before they are deleted.
			$replies   = (array) BP_Activity_Activity::get_child_comments( $activity->id );
			$reply_ids = wp_list_pluck( $replies, 'id' );

			if ( ! bp_activity_delete_comment( $activity->item_id, $activity->id ) ) {
				wp_send_json_error( $response );

				// The comment and its replies has been deleted successfully.
			} else {
				$response = array(
					'deleted' => array_merge(
						array( $activity->id ),
						$reply_ids
					),
				);
			}

			// Deleting an activity.
		} elseif ( ! bp_activity_delete(
			array(
				'id'      => $activity->id,
				'user_id' => $activity->user_id,
			)
		) ) {
				wp_send_json_error( $response );

				// The activity has been deleted successfully.
		} else {
			$response = array(
				'deleted' => array( $activity->id ),
			);
		}

		/** This action is documented in bp-activity/bp-activity-actions.php */
		do_action( 'bp_activity_action_delete_activity', $activity->id, $activity->user_id );

		// If on a single activity redirect to user's home.
		if ( ! empty( $_POST['is_single'] ) ) {
			$response['redirect'] = bp_members_get_user_url( $activity->user_id );
			bp_core_add_message( __( 'Activity deleted successfully', 'buddypress-schedule-activity' ) );
		}

		wp_send_json_success( $response );
	}

	/**
	 * Added for youzify related action only.
	 *
	 * @since 1.0.2
	 */
	public function buddypress_schedule_activity_youzify_activity_new_post_action( $action, $activity ) {
		if ( empty( $activity ) ) {
			return $action;
		}
		if ( class_exists( 'Youzify' ) ) {
			$scheduled_meta_data = bp_activity_get_meta( $activity->id, '_bp_schedule_activity_title_for_youzify', true );
			if ( 'activity' === $activity->component && filter_var( $scheduled_meta_data, FILTER_VALIDATE_BOOLEAN ) ) {
				$action = sprintf(
					/* translators: %s: the activity author user link */
					esc_html__( '%s posted an update', 'buddypress-schedule-activity' ),
					bp_core_get_userlink( $activity->user_id )
				);
			}
		}
		return $action;
	}

	/**
	 * Function to modify query strings to filter scheduled activity results.
	 *
	 * @param string $querystring for activity filter.
	 * @param string $object Component name.
	 *
	 * @return string $modified_querystring to include schedule activities in schedule posts tab.
	 * @since 1.4.1
	 */
	public function bp_schedule_activity_ajax_filter( $querystring, $object ) {
		if ( 'activity' === $object && 'schedule-activity' === bp_current_action() ) {

			$querystring = bp_parse_args( $querystring );

			// Validate and sanitize page parameter.
			$page = isset( $querystring['page'] ) ? absint( $querystring['page'] ) : 1;
			$page = max( 1, min( $page, 1000 ) ); // Limit to reasonable range (1-1000).

			// Force filtering to schedule activity.
			$args['page']                   = $page;
			$args['scope']                  = 'schedule-activity';
			$args['user_id']                = get_current_user_id();
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][]           = array(
				'key'     => '_bp_activity_status',
				'value'   => 'scheduled',
				'compare' => '=',
			);
			$args['show_hidden']            = true;
			$args['per_page']               = 20;

			$modified_querystring = $args;
			return http_build_query( $modified_querystring );

		} else {
			return $querystring;
		}
	}

	/**
	 * Function to get rtmedia privacy setting.
	 *
	 * @return bool $privacy_enabled
	 * @since 1.4.1
	 */
	public function bp_schedule_activity_get_rtmedia_privacy_setting() {

		if ( class_exists( 'RTMedia' ) ) {

			$rtmedia_options = get_option( 'rtmedia-options', array() );

			if ( is_array( $rtmedia_options ) && isset( $rtmedia_options['privacy_enabled'] ) ) {
				return (bool) $rtmedia_options['privacy_enabled'];
			}
		}
		return null;
	}

	/**
	 * Clean up schedule metadata when an activity is deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of arguments for the activity deletion.
	 * @return void
	 */
	public function buddypress_schedule_activity_cleanup_on_delete( $args ) {
		if ( empty( $args['id'] ) ) {
			return;
		}

		$activity_id = absint( $args['id'] );

		// Delete schedule-related meta.
		bp_activity_delete_meta( $activity_id, '_bp_activity_status' );
		bp_activity_delete_meta( $activity_id, '_bp_schedule_activity_title_for_youzify' );
	}
}

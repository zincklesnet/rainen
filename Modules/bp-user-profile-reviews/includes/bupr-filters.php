<?php

/**
 * Class to serve filter Calls.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BUPR_Custom_Hooks' ) ) {

	/**
	 * Class to add custom hooks for this plugin
	 *
	 * @since    1.0.0
	 * @author   Wbcom Designs
	 */
	class BUPR_Custom_Hooks {

		 /**
		 * Holds user role cache.
		 *
		 * @var array
		 */
		public $user_role_cache = array();


		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {

			// Init hooks with proper conditional checks
            $this->init_hooks();
            
		}

		  /**
         * Initialize hooks based on BuddyPress theme and configuration
         */
        private function init_hooks() {

			if( function_exists( 'buddypress' ) && ! buddypress()->buddyboss ) {
				add_action( 'bp_setup_nav', array( $this, 'bupr_member_profile_reviews_tab' ), 11 );
			}
			if ( function_exists( 'buddypress' ) && buddypress()->buddyboss ) {
				add_action( 'bp_before_member_in_header_meta', array( $this, 'bupr_member_average_rating' ) );
				add_action( 'wp', array( $this, 'bupr_member_profile_reviews_tab' ), 11 );
			} else {
				add_action( 'bp_before_member_header_meta', array( $this, 'bupr_member_average_rating' ) );
			}
			add_action( 'youzify_after_profile_header_user_meta', array( $this, 'bupr_member_average_rating' ) );
			add_action( 'bp_setup_admin_bar', array( $this, 'bupr_setup_admin_bar' ), 10 );

			add_action( 'init', array( $this, 'bupr_add_bp_member_reviews_taxonomy_term' ) );
			add_filter( 'post_row_actions', array( $this, 'bupr_bp_member_reviews_row_actions' ), 10, 2 );
			add_filter( 'bulk_actions-edit-review', array( $this, 'bupr_remove_edit_bulk_actions' ), 10, 1 );
			// add_action('bp_screens', array($this, 'bupr_view_review_tab_function_to_show_screen'));
			add_action( 'bp_member_header_actions', array( $this, 'bupr_add_review_button_on_member_header' ) );
			add_action( 'youzify_after_profile_header_user_meta', array( $this, 'bupr_add_review_button_on_member_header' ) );
			add_action( 'bp_activity_after_save', array( $this, 'bupr_add_activity_meta' ) );
			add_filter( 'bp_get_activity_action', array( $this, 'bupr_hide_username_in_activity' ), 10, 2 );
			add_filter( 'bp_get_activity_user_link', array( $this, 'bupr_change_user_link' ) );
			add_filter( 'bp_get_activity_avatar', array( $this, 'bupr_change_avatar_image' ));

			/*
			 * Add review link at member's directory if option admin setting is enabled.
			 */

			  // Directory integration with theme compatibility
            $this->setup_directory_hooks();
			add_action( 'init', array( $this, 'bupr_set_default_rating_criteria' ) );
			add_action( 'bupr_after_member_review_list', array( $this, 'bupr_edit_review_form_modal' ) );

			if ( in_array( 'bp-rewrites/class-bp-rewrites.php', get_option( 'active_plugins' ) ) ) {
				add_filter( 'bp_nouveau_get_nav_link', array( $this, 'bupr_nouveau_link_fix' ), 10, 2 );
			}

			add_action( 'bupr_member_review_after_review_insert', array( $this, 'bupr_create_review_activity' ), 10, 2 );
			
			add_action( 'bp_get_activity_content_body', array( $this, 'bupr_added_activity_star_rating' ), 10, 2 );
		}

		/**
		 * Set up directory integration hooks based on active theme.
		 */
        private function setup_directory_hooks() {
            // Skip if BuddyPress is not active
            if (!function_exists('buddypress')) {
                return;
            }
            
            // Check if BuddyBoss is active first
            if (buddypress()->buddyboss) {
                // Use BuddyBoss specific action
                add_action('bp_member_members_list_item', array($this, 'bupr_rating_directory'), 50);
            } else {
                // Check the theme package ID to distinguish between BuddyPress Legacy and Nouveau
                $theme_package = bp_get_option('_bp_theme_package_id');

                if ('nouveau' === $theme_package) {
                    // If BuddyPress Nouveau is active, use the 'bp_directory_members_item_meta' hook
                    add_action('bp_directory_members_item_meta', array($this, 'bupr_rating_directory'), 50);
                } else {
                    // If BuddyPress Legacy is active, use the 'bp_directory_members_item' hook
                    add_action('bp_directory_members_item', array($this, 'bupr_rating_directory'), 50);
                }
            }
        }

		/**
		 * @bupr_nouveau_link_fix() nav item links.
		 *
		 * @param string $link     The URL for the nav item.
		 * @param object $nav_item The nav item object.
		 * @return string The URL for the nav item.
		 */
		public function bupr_nouveau_link_fix( $link, $nav_item ) {
			$bp_nouveau = bp_nouveau();
			$nav_item   = $bp_nouveau->current_nav_item;

			$link = '#';
			if ( ! empty( $nav_item->link ) ) {
				$link = $nav_item->link;
			}

			if ( 'personal' === $bp_nouveau->displayed_nav && ! empty( $nav_item->primary ) ) {
				if ( bp_loggedin_user_domain() ) {
					$link = str_replace( bp_loggedin_user_domain(), bp_displayed_user_domain(), $link );
				} else {
					$link = trailingslashit( bp_displayed_user_domain() . $link );
				}
			}
			return $link;
		}

		/**
		 * Get displayed user role.
		 *
		 * @since    2.3.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_get_current_user_roles( $user_id ) {
			 // Return empty array if not logged in
            if (!is_user_logged_in() || empty($user_id)) {
                return array();
            }

			$user_id = absint($user_id);
			 // Check cache first
            if (isset($this->user_role_cache[$user_id])) {
                return $this->user_role_cache[$user_id];
            }

			if (!get_userdata($user_id)) {
				return array();
			}
			 // Get user data
            $user = get_userdata($user_id);
            $roles = array();

			if (is_object($user) && isset($user->roles)) {
                $roles = $user->roles;
            }

			 // Cache the result
            $this->user_role_cache[$user_id] = $roles;

		
			return $roles; // This returns an array.
		}

		/**
		 * To add default criteria review settings.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_set_default_rating_criteria() {
			$bupr_admin_settings = get_option( 'bupr_admin_settings', true );
			if ( empty( $bupr_admin_settings ) || ! is_array( $bupr_admin_settings ) ) {
				$default_admin_criteria = array(
					'profile_multi_rating_allowed' => '1',
					'profile_rating_fields'        => array(
						esc_html__( 'Member Response', 'bp-member-reviews' ) => 'yes',
						esc_html__( 'Member Skills', 'bp-member-reviews' ) => 'yes',
					),
				);
				update_option( 'bupr_admin_settings', $default_admin_criteria );
			}
		}

		/**
		 * Display average rating in the BuddyPress directory.
		 *
		 * @since 1.0.0
		 */
		public function bupr_rating_directory() {
			global $members_template, $bupr;
			// Check if we are on the members directory and if the setting is enabled
			if ( ! bp_is_members_directory() || 'yes' !== $bupr['dir_view_ratings'] ) {
				return;
			}
			$user_id = $members_template->member->id;
			 // Get cached rating data
            $bupr_avg_rating = get_user_meta($user_id, 'bupr_aggregate_rating', true);
            $bupr_reviews_count = get_user_meta($user_id, 'bupr_review_count', true);
            
            // If no cached data, skip display
            if (empty($bupr_avg_rating) && empty($bupr_reviews_count)) {
                return;
            }

			 // Convert to proper numeric values
            $bupr_avg_rating = (float) $bupr_avg_rating;
            $bupr_reviews_count = (int) $bupr_reviews_count;

			// Only display if we have reviews
            if ($bupr_reviews_count > 0) {
                $this->render_rating_stars($bupr_avg_rating, $bupr_reviews_count ,$user_id, 'directory');
            } else {
                // Display just the review count if no rating
                $review_string = $bupr_reviews_count > 1 ? esc_html__(' reviews )', 'bp-member-reviews') : esc_html__(' review )', 'bp-member-reviews');
                echo '<div><span class="bupr-directory-rating-text">' . 
                    esc_html($bupr_reviews_count . ' ' . $review_string) . 
                    '</span></div>';
            }

		}


		 /**
         * Render rating stars with proper HTML.
         * 
         * @param float $rating Average rating value
         * @param int $count Number of reviews
         * @param string $context Where this is being displayed (profile, directory, etc.)
         */
        private function render_rating_stars($rating, $count ,$user_id, $context = 'profile') {
            // Calculate star display data
            $stars_on = floor($rating);
            $stars_half = ($rating - $stars_on >= 0.5) ? 1 : 0;
            $stars_off = 5 - ($stars_on + $stars_half);
            
            // Format rating to 2 decimal places
            $rating = number_format($rating, 2);
            
            // Generate CSS class based on context
            $wrapper_class = 'bupr-' . $context . '-review-wrapper';
            $stars_class = 'bupr-' . $context . '-review-stars';
            $tooltip_class = 'bupr-' . $context . '-tooltip';
            
            // Determine the appropriate text for the rating count
            $rating_string = $count > 1 ? 'ratings' : 'rating';
            $review_string = $count > 1 ? esc_html__(' reviews )', 'bp-member-reviews') : esc_html__(' review )', 'bp-member-reviews');
            
            // Start output buffering for better performance
            ob_start();
			?>
            <div class="<?php echo esc_attr($wrapper_class); ?>">
                <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                    <span itemprop="ratingValue" content="<?php echo esc_attr($rating); ?>"></span>
                    <span itemprop="bestRating" content="5"></span>
                    <span itemprop="ratingCount" content="<?php echo esc_attr($count); ?>"></span>
                    <span itemprop="reviewCount" content="<?php echo esc_attr($count); ?>"></span>
                    <span itemprop="itemReviewed" content="Person"></span>
                    <span itemprop="name" content="<?php 
                        // Use the appropriate function based on BP version
                        if (function_exists('bp_get_version') && version_compare(bp_get_version(), '12.0.0', '>=')) {
                            echo esc_attr(bp_members_get_user_slug($context === 'profile' ? bp_displayed_user_id() : $user_id));
                        } else {
                            echo esc_attr(bp_core_get_username($context === 'profile' ? bp_displayed_user_id() : $user_id));
                        }
                    ?>"></span>
                    <span itemprop="url" content="<?php echo esc_url(bp_core_get_userlink($context === 'profile' ? bp_displayed_user_id() : $user_id, false, true)); ?>"></span>
                    
                    <div class="<?php echo esc_attr($stars_class); ?> <?php echo esc_attr($tooltip_class); ?>" 
                         data-tooltip="Average rating: <?php echo esc_attr($rating); ?> based on <?php echo esc_attr($count . ' ' . $rating_string); ?>">
                        <?php
                        // Display the stars
                        for ($i = 1; $i <= $stars_on; $i++) {
                            echo '<span class="fas fa-star bupr-star-rate"></span>';
                        }
                        for ($i = 1; $i <= $stars_half; $i++) {
                            echo '<span class="fas fa-star-half-alt bupr-star-rate"></span>';
                        }
                        for ($i = 1; $i <= $stars_off; $i++) {
                            echo '<span class="far fa-star bupr-star-rate"></span>';
                        }
                        ?>
                        <span class="bupr-<?php echo esc_attr($context); ?>-rating-text">
                            <?php echo esc_html($rating) . '/5 ( ' . esc_html($count) . esc_html($review_string); ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php
            
            // Output the generated HTML
            echo ob_get_clean();
		}




		/**
		 * Actions performed to add a review button on member header.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_add_review_button_on_member_header() {
			global $bupr;
			if ( ! empty( $bupr['hide_review_button'] ) && 'yes' === $bupr['hide_review_button'] ) {
				if ( is_user_logged_in() ) {
					if ( bp_displayed_user_id() === bp_loggedin_user_id() ) {
						$this->bupr_members_right_to_review();
					} else {
						$this->bupr_members_right_to_take_review();
					}
				}
			}
		}

		/**
		 * Map members who can give review by member role.
		 */
		public function bupr_members_right_to_review() {
			 // Add capability checks
			if (!current_user_can('read') || !is_user_logged_in()) {
				return;
			}
			global $bp, $bupr;
			$review_div = 'form';
			$user_id    = bp_loggedin_user_id();
			$user_role  = ( !empty( $this->bupr_get_current_user_roles( $user_id ) ) ) ? $this->bupr_get_current_user_roles( $user_id )[0] : '';
			// If user role is excluded from giving reviews, exit early
			if ( in_array( $user_role, $bupr['exclude_given_members'], true ) ) {
				return;
			}

			// Check if the displayed user is not the logged-in user
			if ( bp_displayed_user_id() !== bp_loggedin_user_id() ) {

				// If exclude_given_members is not empty, process logic
				if ( ! empty( $bupr['exclude_given_members'] ) ) {

					// Check if the logged-in user is not excluded from giving reviews
					if ( ! in_array( $user_role[0], $bupr['exclude_given_members'], true ) ) {

						// Construct the review URL
						$review_url         = bp_core_get_userlink( $user_id, false, true ) . bupr_profile_review_tab_plural_slug() . '/add-' . bupr_profile_review_tab_singular_slug();
						$bp_template_option = bp_get_option( '_bp_theme_package_id' );

						// BuddyPress Nouveau template
						if ( 'nouveau' === $bp_template_option ) {
							?>
							<li id="bupr-add-review-btn" class="generic-button">
								<a href="<?php echo esc_url( $review_url ); ?>" class="button add-review"> <!-- Standard button class -->
									<?php
									/* translators: %s: Review label; */
									echo sprintf( esc_html__( 'Add %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ); ?>
								</a>
							</li>
							<?php
						} else { // BuddyPress legacy or other template
							?>
							<div id="bupr-add-review-btn" class="generic-button">
								<a href="<?php echo esc_url( $review_url ); ?>" class="button add-review"> <!-- Standard button class -->
									<?php
									/* translators: %s: review label. */
									echo sprintf( esc_html__( 'Add %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ); ?>
								</a>
							</div>
							<?php
						}
					}

				} else { // If exclude_given_members is empty, allow review submission for all members

					// Construct the review URL
					$review_url         = bp_core_get_userlink( $user_id, false, true ) . bupr_profile_review_tab_plural_slug() . '/add-' . bupr_profile_review_tab_singular_slug();
					$bp_template_option = bp_get_option( '_bp_theme_package_id' );

					// BuddyPress Nouveau template
					if ( 'nouveau' === $bp_template_option ) {
						?>
						<li id="bupr-add-review-btn" class="generic-button">
							<a href="<?php echo esc_url( $review_url ); ?>" class="button add-review"> <!-- Standard button class -->
								<?php
								/* translators: %s: review label. */
								echo sprintf( esc_html__( 'Add %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ); ?>
							</a>
						</li>
						<?php
					} else { // BuddyPress legacy or other template
						?>
						<div id="bupr-add-review-btn" class="generic-button">
							<a href="<?php echo esc_url( $review_url ); ?>" class="button add-review"> <!-- Standard button class -->
								<?php
								/* translators: %s: review label. */
								echo sprintf( esc_html__( 'Add %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ); ?>
							</a>
						</div>
						<?php
					}
				}
			}
		}

		/**
		 * Members who can only take reviews
		 */
		public function bupr_members_right_to_take_review() {
			global $bp, $bupr;
			$review_div = 'form';
			$user_id    = bp_loggedin_user_id();
			$user_role  = ( !empty( $this->bupr_get_current_user_roles( $user_id ) ) ) ? $this->bupr_get_current_user_roles( $user_id )[0] : '';
			// Exit early if the user's role is excluded from taking reviews
			if ( ! in_array( $user_role, $bupr['exclude_given_members'], true ) ) {
				return;
			}

			// Check if the displayed user is not the logged-in user
			if ( bp_displayed_user_id() !== bp_loggedin_user_id() ) {

				// If add_taken_members is set, process the review logic
				if ( ! empty( $bupr['add_taken_members'] ) ) {
					$user_id   = bp_displayed_user_id();
					$user_role = $this->bupr_get_current_user_roles( $user_id );
					// Check if the displayed user has the role to take reviews
					if ( in_array( $user_role[0], $bupr['add_taken_members'], true ) ) {

						// Construct the review URL
						$review_url         = bp_core_get_userlink( $user_id, false, true ) . bupr_profile_review_tab_plural_slug() . '/add-' . bupr_profile_review_tab_singular_slug();
						$bp_template_option = bp_get_option( '_bp_theme_package_id' );

						// BuddyPress Nouveau template
						if ( 'nouveau' === $bp_template_option ) {
							?>
							<li id="bupr-add-review-btn" class="generic-button">
								<a href="<?php echo esc_url( $review_url ); ?>" class="button add-review" show="<?php echo esc_attr( $review_div ); ?>">
									<?php
									/* translators: %s: review label. */
									echo sprintf( esc_html__( 'Add %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ); ?>
								</a>
							</li>
							<?php
						} else { // BuddyPress legacy or other template
							?>
							<div id="bupr-add-review-btn" class="generic-button">
								<a href="<?php echo esc_url( $review_url ); ?>" class="button add-review" show="<?php echo esc_attr( $review_div ); ?>">
									<?php
									/* translators: %s: review label. */
									echo sprintf( esc_html__( 'Add %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ); ?>
								</a>
							</div>
							<?php
						}
					}
				} else { // If add_taken_members is empty, allow reviews for all

					// Construct the review URL
					$review_url         = bp_core_get_userlink( $user_id, false, true ) . bupr_profile_review_tab_plural_slug() . '/add-' . bupr_profile_review_tab_singular_slug();
					$bp_template_option = bp_get_option( '_bp_theme_package_id' );

					// BuddyPress Nouveau template
					if ( 'nouveau' === $bp_template_option ) {
						?>
						<li id="bupr-add-review-btn" class="generic-button">
							<a href="<?php echo esc_url( $review_url ); ?>" class="button add-review" show="<?php echo esc_attr( $review_div ); ?>">
								<?php
								/* translators: %s: review label. */
								echo sprintf( esc_html__( 'Add %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ); ?>
							</a>
						</li>
						<?php
					} else { // BuddyPress legacy or other template
						?>
						<div id="bupr-add-review-btn" class="generic-button">
							<a href="<?php echo esc_url( $review_url ); ?>" class="button add-review" show="<?php echo esc_attr( $review_div ); ?>">
								<?php
								/* translators: %s: review label. */
								echo sprintf( esc_html__( 'Add %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ); ?>
							</a>
						</div>
						<?php
					}
				}
			}
		}

		/**
		 * Setup Reviews link in admin bar.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $wp_admin_nav Member Review add menu array.
		 * @author   Wbcom Designs
		 */
		public function bupr_setup_admin_bar( $wp_admin_nav = array() ) {
			global $wp_admin_bar;
			global $bupr;
			$bupr_args = array(
				'post_type'      => 'review',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'category'       => 'bp-member',
				'meta_query'     => array(
					array(
						'key'     => 'linked_bp_member',
						'value'   => get_current_user_id(),
						'compare' => '=',
					),
				),
			);

			$reviews       = get_posts( $bupr_args );
			$reviews_count = count( $reviews );

			$profile_menu_slug = isset( $bupr['review_label_plural'] ) ? sanitize_title( $bupr['review_label_plural'] ) : esc_html( 'reviews' );

			$base_url = bp_loggedin_user_domain() . $profile_menu_slug;
			if ( is_user_logged_in() ) {
				$wp_admin_bar->add_menu(
					array(
						'parent' => 'my-account-buddypress',
						'id'     => 'my-account-' . $profile_menu_slug,
						'title'  => $profile_menu_slug . ' <span class="count">' . $reviews_count . '</span>',
						'href'   => trailingslashit( $base_url ),
					)
				);
			}
		}

		/**
		 * Display average rating on a BuddyPress member's profile.
		 *
		 * @since 1.0.0
		 */
		public function bupr_member_average_rating() {
			
			global $bupr;
			// Only proceed on user profiles
            if (!bp_is_user()) {
                return;
            }
			 $user_id = bp_displayed_user_id();

			      // Get cached rating data
            $bupr_avg_rating = get_user_meta($user_id, 'bupr_aggregate_rating', true);
            $bupr_reviews_count = get_user_meta($user_id, 'bupr_review_count', true);

			  // Calculate if necessary or use cached values
            if ('' === $bupr_avg_rating || '' === $bupr_reviews_count) {
                $rating_data = $this->calculate_user_rating($user_id);
                $bupr_avg_rating = $rating_data['average'];
                $bupr_reviews_count = $rating_data['count'];
                
                // Cache the values
                update_user_meta($user_id, 'bupr_aggregate_rating', $bupr_avg_rating);
                update_user_meta($user_id, 'bupr_review_count', $bupr_reviews_count);
            }

			// Display the rating if there are reviews
            if ($bupr_reviews_count > 0) {
                $this->render_rating_stars($bupr_avg_rating, $bupr_reviews_count,$user_id, 'member');
            } else {
                $review_string = ' ' . esc_html__('reviews', 'bp-member-reviews');
                echo '<div><span class="bupr-member-rating-text">' . 
                    esc_html($bupr_reviews_count . $review_string) . 
                    '</span></div>';
            }

		}

		/**
         * Calculate user rating with optimized database queries.
         *
         * @param int $user_id User ID to calculate rating for
         * @return array Rating data with average and count
         */
        private function calculate_user_rating($user_id) {
            global $wpdb, $bupr;
            
			// Validate and sanitize input
			$user_id = absint($user_id);
			if (!$user_id) {
				return array('average' => 0, 'count' => 0);
			}
            // Use direct SQL for better performance
            $reviews = $wpdb->get_results($wpdb->prepare(
                "SELECT p.ID 
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE p.post_type = 'review'
                AND p.post_status = 'publish'
                AND pm.meta_key = 'linked_bp_member'
                AND pm.meta_value = %d",
                $user_id
            ));
            
            $total_rating = 0;
            $review_count = count($reviews);
            $total_review_count = 0;
            
            if ($review_count > 0) {
                // Get all review IDs
                $review_ids = wp_list_pluck($reviews, 'ID');
                
                // Get all ratings in a single query
                $review_ratings = array();
                foreach ($review_ids as $review_id) {
                    $ratings = get_post_meta($review_id, 'profile_star_rating', true);
                    if (!empty($ratings) && is_array($ratings)) {
                        $review_ratings[] = $ratings;
                    }
                }
                
                // Calculate average rating
                foreach ($review_ratings as $ratings) {
                    $rate = 0;
                    $field_count = 0;
                    
                    foreach ($ratings as $field => $value) {
                        if (0 == $value) {
                            continue;
                        }
                        $rate += $value;
                        $field_count++;
                    }
                    
                    if ($field_count > 0) {
                        $total_rating += $rate / $field_count;
                        $total_review_count++;
                    }
                }
            }
            
            $average = ($total_review_count > 0) ? $total_rating / $total_review_count : 0;
            
            return array(
                'average' => $average,
                'count' => $review_count
            );
        }



		/**
		 * Actions performed to remove edit from bulk options
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $actions Actions array.
		 * @author   Wbcom Designs
		 */
		public function bupr_remove_edit_bulk_actions( $actions ) {
			unset( $actions['edit'] );
			return $actions;
		}

		/**
		 * Actions performed to hide row actions
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $actions Actions array.
		 * @param    array $post    Posts array.
		 * @author   Wbcom Designs
		 */
		public function bupr_bp_member_reviews_row_actions( $actions, $post ) {
			global $bp;
			global $bupr;
			if ( 'review' === $post->post_type ) {
				unset( $actions['edit'] );
				unset( $actions['view'] );
				unset( $actions['inline hide-if-no-js'] );
				$review_term = isset( wp_get_object_terms( $post->ID, 'review_category' )[0]->name ) ? wp_get_object_terms( $post->ID, 'review_category' )[0]->name : '';
				if ( 'BP Member' === $review_term ) {
					// Add a link to view the review.
					$review_title     = $post->post_title;
					$linked_bp_member = get_post_meta( $post->ID, 'linked_bp_member', true );

					$review_url = bp_core_get_userlink( $linked_bp_member, false, true ) . strtolower( $bupr['review_label_plural'] ) . '/view/' . $post->ID;
					/* translators: %s: */
					$actions['view_review'] = '<a href="' . $review_url . '" title="' . $review_title . '">' . sprintf( esc_html__( 'View %s', 'bp-member-reviews' ), esc_html( $bupr['review_label'] ) ) . '</a>';

					// Add Approve Link for draft reviews.
					if ( 'draft' === $post->post_status ) {
						$actions['approve_review'] = '<a href="javascript:void(0);" title="' . $review_title . '" class="bupr-approve-review" data-rid="' . $post->ID . '">' . esc_html__( 'Approve', 'bp-member-reviews' ) . '</a>';
					}
				}
			}
			return $actions;
		}

		/**
		 * Action performed to add taxonomy term for group reviews
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_add_bp_member_reviews_taxonomy_term() {
			$termexists = term_exists( 'BP Member', 'review_category' );
			if ( 0 === $termexists || null === $termexists ) {
				wp_insert_term( 'BP Member', 'review_category' );
			}
		}

		/**
		 * Action performed to add a tab for member profile reviews
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_member_profile_reviews_tab() {
			global $bp, $bupr;

			// Default from plugin settings
			$exclude_given_members = $bupr['exclude_given_members'] ?? array();
			$add_taken_members     = $bupr['add_taken_members'] ?? array();
			$review_label          = $bupr['review_label'] ?? esc_html__( 'Review', 'bp-member-reviews' );

			 if ( bupr_bp_classic_activate() ) {
				global $wp_roles;
				// Ensure $wp_roles is set correctly.
				if ( ! isset( $wp_roles ) || ! is_object( $wp_roles ) || ! isset( $wp_roles->roles ) ) {
					$wp_roles = wp_roles();
				}
				// Get all available roles for pre-population.
				$available_roles = array_keys( $wp_roles->roles );
				$bupr_general_settings   = get_option( 'bupr_admin_general_options', array() );
				$exclude_given_members   = $bupr_general_settings['bupr_exc_member'] ?? $available_roles;
				$add_taken_members       = $bupr_general_settings['bupr_add_member'] ?? $available_roles;				
			}

			$bp_pages = bp_core_get_directory_pages();
			add_filter( 'site_url', 'bupr_site_url', 99 );
			$member_slug = ( isset( $bp_pages->members ) && isset( $bp_pages->members->slug ) ) ? $bp_pages->members->slug : 'members';

			/* count member's review */
			$bupr_args = array(
				'post_type'      => 'review',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'category'       => 'bp-member',
				'meta_query'     => array(
					array(
						'key'     => 'linked_bp_member',
						'value'   => bp_displayed_user_id(),
						'compare' => '=',
					),
				),
			);

			$bupr_reviews = new WP_Query( $bupr_args );
			if ( ! empty( $bupr_reviews->posts ) ) {
				$bupr_reviews = count( $bupr_reviews->posts );
				if ( ! empty( $bupr_reviews ) ) {
					$bupr_notification = '<span class="count">' . $bupr_reviews . '</span>';
				} else {
					$bupr_notification = '<span class="count">' . 0 . '</span>';
				}
			} else {
				$bupr_notification = '<span class="count">' . 0 . '</span>';
			}

			$name = bp_get_displayed_user_username();
			
			if ( apply_filters( 'bupr_show_profile_review_tab', true ) ) {
				$tab_args = array(
					'name'                    => bupr_profile_review_tab_name() . 's' . $bupr_notification,
					'slug'                    => bupr_profile_review_tab_plural_slug(),
					'screen_function'         => array( $this, 'bupr_reviews_tab_function_to_show_screen' ),
					'position'                => 75,
					'default_subnav_slug'     => 'view',
					'show_for_displayed_user' => true,
				);
				bp_core_new_nav_item( $tab_args );
			}

			$parent_slug = bupr_profile_review_tab_plural_slug();

			// Add subnav to view a review.
			bp_core_new_subnav_item(
				array(
					'name'            => bupr_profile_review_tab_name(),
					'slug'            => 'view',
					'parent_url'      => $bp->loggedin_user->domain . $parent_slug . '/',
					'parent_slug'     => $parent_slug,
					'screen_function' => array( $this, 'bupr_view_review_tab_function_to_show_screen' ),
					'position'        => 100,
					'link'            => site_url() . "/$member_slug/$name/$parent_slug/",
				)
			);
			
			// Add subnav to add a review.
			if ( bp_displayed_user_id() === bp_loggedin_user_id() ) {
				if ( ! empty( $exclude_given_members ) ) {
					$user_role = $this->bupr_get_current_user_roles( bp_loggedin_user_id() );
					if ( ! empty( $user_role ) && in_array( $user_role[0], $exclude_given_members, true ) && ! bp_loggedin_user_id() ) {
						bp_core_new_subnav_item(
							array(
								/* translators: Review Label */
								'name'            => sprintf( esc_html__( 'Add %1$s', 'bp-member-reviews' ), esc_html( $review_label ) ),
								'slug'            => 'add-' . bupr_profile_review_tab_singular_slug(),
								'parent_url'      => $bp->loggedin_user->domain . $parent_slug . '/',
								'parent_slug'     => $parent_slug,
								'screen_function' => array( $this, 'bupr_reviews_form_tab_function_to_show_screen' ),
								'position'        => 200,
								'link'            => site_url() . "/$member_slug/$name/$parent_slug/" . 'add-' . bupr_profile_review_tab_singular_slug(),
							)
						);
					}
				}
			} else {
				$user_role = $this->bupr_get_current_user_roles( bp_loggedin_user_id() );
				if ( ! empty( $user_role ) &&
					! array_intersect(
						(array) $user_role,
						isset( $exclude_given_members ) && is_array( $exclude_given_members ) ? $exclude_given_members : []
					)
				) {
					return ; 
				}

				if ( ! empty( $add_taken_members ) && ! empty( $user_role ) ) {
					$user_role = $this->bupr_get_current_user_roles( bp_displayed_user_id() );
					$user_role = ! empty( $user_role ) ? $user_role : array();

					if ( array_intersect( $user_role, $add_taken_members) ) {
						bp_core_new_subnav_item(
							array(
								/* translators: %s: */
								'name'            => sprintf( esc_html__( 'Add %1$s', 'bp-member-reviews' ), esc_html( bupr_profile_review_singular_tab_name() ) ),
								'slug'            => 'add-' . bupr_profile_review_tab_singular_slug(),
								'parent_url'      => $bp->loggedin_user->domain . $parent_slug . '/',
								'parent_slug'     => $parent_slug,
								'screen_function' => array( $this, 'bupr_reviews_form_tab_function_to_show_screen' ),
								'position'        => 200,
								'link'            => site_url() . "/$member_slug/$name/$parent_slug/" . 'add-' . bupr_profile_review_tab_singular_slug(),
							)
						);
					}
				} else {

					bp_core_new_subnav_item(
						array(
							/* translators: %s: */
							'name'            => sprintf( esc_html__( 'Add %1$s', 'bp-member-reviews' ), esc_html( bupr_profile_review_singular_tab_name() ) ),
							'slug'            => 'add-' . bupr_profile_review_tab_singular_slug(),
							'parent_url'      => $bp->loggedin_user->domain . $parent_slug . '/',
							'parent_slug'     => $parent_slug,
							'screen_function' => array( $this, 'bupr_reviews_form_tab_function_to_show_screen' ),
							'position'        => 200,
							'link'            => site_url() . "/$member_slug/$name/$parent_slug/" . 'add-' . bupr_profile_review_tab_singular_slug(),
						)
					);
				}
			}
			remove_filter( 'site_url', 'bupr_site_url', 99 );
		}

		/**
		 * Action performed to show screen of reviews listing tab.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_reviews_tab_function_to_show_screen() {
			add_action( 'bp_template_content', array( $this, 'bupr_reviews_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Action performed to show screen of reviews form tab.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_reviews_form_tab_function_to_show_screen() {
			add_action( 'bp_template_content', array( $this, 'bupr_reviews_form_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Actions performed to show the content of reviews list tab
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_reviews_tab_function_to_show_content() {
			bupr_get_template( 'bupr-reviews-tab-template.php' );
		}

		/**
		 * Action performed to show the content of add review tab
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_reviews_form_to_show_content() {
			global $bupr;
			?>
			<div class="bupr-bp-member-review-no-popup-add-block">
			<?php
			if ( is_user_logged_in() ) {
				do_action( 'bupr_member_review_form' );
			} else {
				$bp_template_option = bp_get_option( '_bp_theme_package_id' );
				if ( 'nouveau' === $bp_template_option ) {
					?>
						<div id="message" class="info bp-feedback bp-messages bp-template-notice">
							<span class="bp-icon" aria-hidden="true"></span>
				<?php } else { ?>
							<div id="message" class="info">
							<?php } ?>
							<p><?php 
							printf(
						/* translators: %1$s: Review user link; %2$s: review label; %3$s: reviewed user link. */
						'You must <a href="%1$s">login</a> to add a %2$s.'
						,esc_url( wp_login_url( get_permalink() ) ),
						esc_html( strtolower( $bupr['review_label'] ) ),
						); ?></p></div>
						<?php 
					} ?>
					</div>
			<?php
		}

		/**
		 * Action performed to show screen of single review view tab.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_view_review_tab_function_to_show_screen() {
			add_action( 'bp_template_content', array( $this, 'bupr_view_review_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Action performed to show the content of reviews list tab.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_view_review_tab_function_to_show_content() {
			bupr_get_template( 'bupr-single-review-template.php' );
		}

		public function bupr_edit_review_form_modal() {
			if ( is_user_logged_in() ) {
				bupr_get_template( 'bupr-edit-review-form.php' );
			}
		}

		/**
		 * Create activity on new review creation.
		 *
		 * @since 2.8.1
		 * @access public
		 * @author Wbcom Designs
		 *
		 * @param int $review_id        The ID of the review post.
		 * @param int $reviewed_user_id The ID of the user being reviewed.
		 */
		public function bupr_create_review_activity( $review_id, $reviewed_user_id ) {
			global $bupr, $bp;

			if(isset($bupr['review_activity']) && 'yes' !== $bupr['review_activity'] ){
				return;
			}
			
			// Apply filter to allow disabling activity creation.
			$allow_activity = apply_filters( 'bupr_allow_activity_posting', true, $review_id, $reviewed_user_id );

			// If the filter returns false, do not proceed with activity creation.
			if ( false === $allow_activity ) {
				return;
			}

			// Check if the review activity should be created (based on settings).
			// Get the review post data.
			$review = get_post( $review_id );
			if ( ! empty( $review ) ) {
				// Get the reviewer's (author's) and reviewed user's profile URLs.
				$reviewer_user_id   = $review->post_author;
				$reviewer_user_link = bp_core_get_userlink( $reviewer_user_id );
				$reviewed_user_link = bp_core_get_userlink( $reviewed_user_id );

				// Get the review content.
				$review_content = $review->post_content;
					// Check for anonymous review
				$is_anonymous = get_post_meta($review_id, 'bupr_anonymous_review_post', true) === 'yes';
				if ($is_anonymous) {
					$reviewer_user_link = esc_html__('An anonymous user', 'bp-member-reviews');
				}

				// Construct the action text (reviewer to reviewed user).
				$action = sprintf(
					apply_filters( 'bupr_member_review_activity_action',
					/* translators: %1$s: Review user link; %2$s: review label; %3$s: reviewed user link. */
					__( '%1$s posted a new %2$s to %3$s', 'bp-member-reviews' )
					),
					$reviewer_user_link,
					strtolower( esc_html( $bupr['review_label'] ) ),
					$reviewed_user_link
				);

				// Ensure the content is safe.
				$escaped_content = wp_kses_post( $review_content );
				
				if(function_exists('bp_activity_add')){
					// Add activity to BuddyPress.
					bp_activity_add( array(
						'action'        => $action,
						'content'       => !empty( $escaped_content ) ? $escaped_content : 'Ratings', // No links or ratings, just review content.
						'component'     => $bp->members->id,
						'type'          => defined( 'YOUZIFY_VERSION' ) ? 'activity_status' : 'member_review',
						'user_id'       => $reviewer_user_id,
						'item_id'       => $review_id,
						'secondary_item_id' => $reviewed_user_id,
						'hide_sitewide' => false,
						'is_spam'       => false,
						'privacy'       => 'public',
					) );
				}else{
					return;
				}
				
			}
		}

		/**
		 * Added member star rating in activity.
		 *
		 * @param  string $activity_content Activity Content.
		 * @param  object $activity Activity Object.
		 */
		public function bupr_added_activity_star_rating( $activity_content, $activity ) {
			$post_id              = $activity->item_id;
			$review_rating_fields = get_option( 'bupr_admin_settings', true );
			$review_ratings       = get_post_meta( $post_id, 'profile_star_rating', false );		
			$review_start         = '';	 
			if( ! empty( $review_ratings[0] ) && is_array( $review_ratings[0] ) ) {
				$review_start .= '<div class="bupr-multi-review">';
				foreach ( $review_ratings[0] as $rating_field_name => $ratings ) {
					if( empty( $ratings ) ) {
						continue;
					}
					$review_start .= '<div class="multi-review">';
					if ( ! empty( $rating_field_name ) ) {
						$review_start .= '<div class="bupr-col-6">' . esc_html( $rating_field_name ) . '</div>';
					}
					$review_start .= '<div class="bupr-col-6">';
					
					/*** Star rating Ratings */
					$stars_on  = $ratings;
					$stars_off = 5 - $stars_on;
					for ( $i = 1; $i <= $stars_on; $i++ ) {
						$review_start .= '<span class="fas fa-star stars bupr-star-rate"></span>';

					}
					for ( $i = 1; $i <= $stars_off; $i++ ) {
						$review_start .= '<span class="far fa-star stars bupr-star-rate"></span>';
					}
					$review_start .= '</div>';
					$review_start .= '</div>';
				}
				$review_start .= '</div>';
			}
			return $activity_content . $review_start;
		}

		/**
		* Saved the user name as anonymous if meta in review.
		 *
		* @since    3.2.2
		* @access   public
		* @author   Wbcom Designs
		 */
		public function bupr_add_activity_meta($activity){
			$is_anonymous = get_post_meta($activity->item_id, 'bupr_anonymous_review_post',true);
			$anonymous_user = ( $is_anonymous == 'yes' ) ? esc_html__( 'An anonymous user', 'bp-member-reviews' ) : '';
			if ( isset( $activity->id ) && !empty( $anonymous_user ) ) {
				bp_activity_add_meta( $activity->id, 'bupr_user_string', $anonymous_user );
			}
		}

		/**
		* Hide the user name if anonymous text available.
		 *
		* @since    3.2.2
		* @access   public
		* @author   Wbcom Designs
		 */
		public function bupr_hide_username_in_activity( $action, $activity ) {
			$bupr_user_string = bp_activity_get_meta( $activity->id, 'bupr_user_string' );
			$user_id = $activity->user_id; 
			if( ! empty( $bupr_user_string ) ){
				$action = str_replace( bp_core_get_userlink( $user_id ), $bupr_user_string , $action );
			}
			return apply_filters( 'bupr_hide_username_in_activity', $action );
		}

		/**
		* Change the user url if the activity is of anonymous user.
		 *
		* @since    3.2.2
		* @access   public
		* @author   Wbcom Designs
		 */
		public function bupr_change_user_link( $user_link_activity ) {
			global $activities_template;
			$activity = $activities_template->activity;
			$is_anonymous = get_post_meta($activity->item_id, 'bupr_anonymous_review_post',true);
			if( 'yes' === $is_anonymous ){
			$user_link_activity = '#';
			}
			return apply_filters( 'bupr_change_user_link', $user_link_activity );
		}

		/**
		* Change the user gravatar if the activity is of anonymous user.
		 *
		* @since    3.2.2
		* @access   public
		* @author   Wbcom Designs
		 */
		public function bupr_change_avatar_image( $gravatar_image ) {
			global $activities_template;
			
			if( isset( $activities_template->activity) && isset( $activities_template->activity->item_id )  ) {
				$activity = $activities_template->activity;
				$is_anonymous = get_post_meta( $activity->item_id, 'bupr_anonymous_review_post',true);
				
				if( 'yes' === $is_anonymous ){
					
					$size = apply_filters( 'bupr_default_avatar_size', 150 );
					$default_avatar = apply_filters( 'bupr_change_default_avatar', 'mystery' );  // Could also use identicon, monsterid, retro, etc.
					$gravatar_url = 'https://www.gravatar.com/avatar/?s=' . $size . '&d=' . $default_avatar;
				
					$gravatar_image =  '<img src="' . esc_url( $gravatar_url ) . '" class="avatar photo" alt="Custom Default Gravatar" />';
				}
				
			}
			
			return apply_filters( 'bupr_change_avatar_image', $gravatar_image );
		}
	}
			new BUPR_Custom_Hooks();
}

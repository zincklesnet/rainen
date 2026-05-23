<?php
/**
 * Core functions for Reign TutorLMS Addon
 *
 * @package Reign_TutorLMS_Addon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Settings fields for TutorLMS
 */
function reign_tutorlms_settings_fields() {
	$fields = array(
		'profile' => array(
			'enable_profile_courses_tab' => array(
				'label'       => __( 'Enable My Courses Tab', 'reign-tutorlms-addon' ),
				'description' => __( 'Add a "My Courses" tab to user profiles showing their enrolled courses.', 'reign-tutorlms-addon' ),
				'type'        => 'checkbox',
				'default'     => 0,
			),
		),
	);
	
	return apply_filters( 'reign_tutorlms_settings_fields', $fields );
}

/**
 * Enhanced TutorLMS course shortcode with additional features
 */
function reign_tutorlms_tutor_course_shortcode( $atts ) {
	// Detect if we're in a profile context
	$is_profile_context = false;
	$default_columns = '3';
	
	// Check BuddyPress profile
	if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
		$is_profile_context = true;
		$default_columns = '2';
	}
	
	// Check PeepSo profile - multiple detection methods
	if ( class_exists( 'PeepSo' ) ) {
		// Method 1: Check URL for profile
		$current_url = $_SERVER['REQUEST_URI'] ?? '';
		if ( strpos( $current_url, '/profile/' ) !== false ) {
			$is_profile_context = true;
			$default_columns = '2';
		}
		
		// Method 2: Check PeepSo page segments if available
		if ( function_exists( 'peepso' ) && ! $is_profile_context ) {
			try {
				$page = peepso('page');
				if ( $page && property_exists( $page, 'url_segments' ) ) {
					$peepso_url_segments = $page->url_segments;
					if ( is_array( $peepso_url_segments ) && in_array( 'profile', $peepso_url_segments ) ) {
						$is_profile_context = true;
						$default_columns = '2';
					}
				}
			} catch ( Exception $e ) {
				// Silently handle any PeepSo errors
			}
		}
		
		// Method 3: Check for PeepSo profile shortcode
		global $post;
		if ( $post && has_shortcode( $post->post_content, 'peepso_profile' ) ) {
			$is_profile_context = true;
			$default_columns = '2';
		}
	}
	
	// Check standard WordPress author page
	if ( is_author() ) {
		$is_profile_context = true;
		$default_columns = '2';
	}
	
	/**
	 * Filter the default column count for course grids
	 * 
	 * @param string $default_columns Default column count
	 * @param bool   $is_profile_context Whether in profile context
	 * @param array  $atts Shortcode attributes
	 */
	$default_columns = apply_filters( 'reign_tutorlms_default_columns', $default_columns, $is_profile_context, $atts );
	
	$atts = shortcode_atts( array(
		// TutorLMS Original Parameters (pass through)
		'id'                => '',
		'exclude_ids'       => '',
		'category'          => '',
		'orderby'           => 'post_date',
		'order'             => 'DESC',
		'count'             => '12',
		'column_per_row'    => $default_columns,
		'show_pagination'   => 'on',
		'course_filter'     => 'off',
		
		// Reign Enhanced Parameters
		'my_courses'        => 'no',
		'user_id'           => '',
		'show_progress'     => 'no',
		'course_status'     => 'all',
		'layout_style'      => 'default',
		'show_instructor'   => 'yes',
		'show_category'     => 'yes',
		'show_duration'     => 'yes',
		'show_students'     => 'yes',
		'show_lessons'      => 'yes',
		'columns'           => '',
	), $atts, 'reign_tutor_course' );
	
	// Handle columns parameter (map to column_per_row for backward compatibility)
	if ( ! empty( $atts['columns'] ) ) {
		$atts['column_per_row'] = $atts['columns'];
	}
	
	// If showing enrolled courses
	if ( $atts['my_courses'] === 'yes' ) {
		// Auto-detect user context
		$user_id = reign_tutorlms_get_profile_user_id( $atts['user_id'] );

		if ( ! $user_id ) {
			return '<aside class="bp-feedback bp-messages info tutor-no-courses-found"><span class="bp-icon" aria-hidden="true"></span>' . __( 'Please log in to view your courses.', 'reign-tutorlms-addon' ) . '</aside>';
		}

		// Get enrolled course IDs
		$enrolled_course_ids = reign_tutorlms_get_enrolled_course_ids( $user_id, $atts['course_status'] );
		$total_courses = count( $enrolled_course_ids );

		if ( empty( $enrolled_course_ids ) ) {
			// Provide specific message based on status filter
			$message = __( 'No courses found.', 'reign-tutorlms-addon' );
			if ( $atts['course_status'] === 'completed' ) {
				$message = __( 'No completed courses yet.', 'reign-tutorlms-addon' );
			} elseif ( $atts['course_status'] === 'in_progress' || $atts['course_status'] === 'in-progress' ) {
				$message = __( 'No courses in progress.', 'reign-tutorlms-addon' );
			} elseif ( $atts['course_status'] === 'not_started' || $atts['course_status'] === 'not-started' ) {
				$message = __( 'No courses waiting to be started.', 'reign-tutorlms-addon' );
			}

			// Still wrap in our container for consistency
			return '<div class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '"><aside class="bp-feedback bp-messages info tutor-no-courses-found"><span class="bp-icon" aria-hidden="true"></span>' . $message . '</aside></div>';
		}

		// Override the id parameter with enrolled courses
		$atts['id'] = implode( ',', $enrolled_course_ids );

		// Enable pagination for large course lists
		if ( $total_courses > 12 && $atts['show_pagination'] !== 'on' ) {
			$atts['show_pagination'] = 'on';
		}
	}
	
	// Add wrapper classes
	$wrapper_classes = array( 'reign-tutor-courses' );
	$wrapper_classes[] = 'layout-' . esc_attr( $atts['layout_style'] );
	
	if ( $atts['show_progress'] === 'yes' && $atts['my_courses'] === 'yes' ) {
		$wrapper_classes[] = 'show-progress';
	}
	
	// Filter visibility classes
	if ( $atts['show_instructor'] === 'no' ) $wrapper_classes[] = 'hide-instructor';
	if ( $atts['show_category'] === 'no' ) $wrapper_classes[] = 'hide-category';
	if ( $atts['show_duration'] === 'no' ) $wrapper_classes[] = 'hide-duration';
	if ( $atts['show_students'] === 'no' ) $wrapper_classes[] = 'hide-students';
	if ( $atts['show_lessons'] === 'no' ) $wrapper_classes[] = 'hide-lessons';
	
	// Build TutorLMS shortcode parameters
	$tutor_params = array();
	
	// Pass through original TutorLMS parameters
	$pass_through = array( 'id', 'exclude_ids', 'category', 'orderby', 'order', 'count', 'column_per_row', 'show_pagination', 'course_filter' );
	foreach ( $pass_through as $param ) {
		// Always pass through these parameters, even if they have default values
		if ( isset( $atts[ $param ] ) && $atts[ $param ] !== '' ) {
			$tutor_params[] = $param . '="' . esc_attr( $atts[ $param ] ) . '"';
		}
	}
	
	// Generate the shortcode
	$shortcode_string = '[tutor_course ' . implode( ' ', $tutor_params ) . ']';
	
	// Set global flag for progress display
	global $reign_show_progress, $reign_enrolled_user_id;
	$reign_show_progress = ( $atts['show_progress'] === 'yes' && $atts['my_courses'] === 'yes' && ! empty( $user_id ) );
	$reign_enrolled_user_id = $reign_show_progress ? $user_id : 0;

	// Add hook for progress display if needed
	if ( $reign_show_progress ) {
		add_action( 'tutor_course/loop/before_footer', 'reign_tutorlms_add_progress_to_course_loop', 15 );
	}

	// Start output buffering
	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
		<?php
		// Show course count for my courses
		if ( $atts['my_courses'] === 'yes' && isset( $total_courses ) ) {
			echo '<div class="reign-courses-count">';
			printf(
				_n( 'Showing %d course', 'Showing %d courses', $total_courses, 'reign-tutorlms-addon' ),
				$total_courses
			);
			echo '</div>';
		}

		// Execute the TutorLMS shortcode
		echo do_shortcode( $shortcode_string );

		// Add missing filter fields to prevent JSON parse errors
		if ( $atts['course_filter'] === 'on' || strpos( $shortcode_string, 'course_filter="on"' ) !== false ) {
			?>
			<script type="text/javascript">
			// Ensure filter fields exist to prevent JSON.parse errors
			if (typeof jQuery !== 'undefined') {
				jQuery(document).ready(function($) {
					if ($('#course_filter_categories').length === 0) {
						$('body').append('<input type="hidden" id="course_filter_categories" value="[]" />');
					}
					if ($('#course_filter_exclude_ids').length === 0) {
						$('body').append('<input type="hidden" id="course_filter_exclude_ids" value="[]" />');
					}
					if ($('#course_filter_post_ids').length === 0) {
						$('body').append('<input type="hidden" id="course_filter_post_ids" value="[]" />');
					}
				});
			}
			</script>
			<?php
		}

		// Add JavaScript for hiding specific elements
		if ( $atts['show_category'] === 'no' || $atts['show_instructor'] === 'no' ) {
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				<?php if ( $atts['show_category'] === 'no' ) : ?>
				// Hide category text and links
				$('.reign-tutor-courses .tutor-meta').each(function() {
					var $metaDiv = $(this).find('div:last-child');
					if ($metaDiv.length) {
						var html = $metaDiv.html();
						if (html && html.includes('In')) {
							// Remove "In" text and everything after it (categories)
							var newHtml = html.split('In')[0].trim();
							$metaDiv.html(newHtml);
						}
					}
				});
				// Also hide in the older template structure
				$('.reign-tutor-courses .tutor-meta-course-cat').each(function() {
					var html = $(this).html();
					if (html && html.includes('In')) {
						var newHtml = html.split('In')[0].trim();
						$(this).html(newHtml);
					}
				});
				<?php endif; ?>

				<?php if ( $atts['show_instructor'] === 'no' && $atts['show_category'] === 'yes' ) : ?>
				// If hiding instructor but showing category, reconstruct to show only categories
				$('.reign-tutor-courses .tutor-meta').each(function() {
					var $metaDiv = $(this).find('div:last-child');
					if ($metaDiv.length) {
						var html = $metaDiv.html();
						if (html && html.includes('In')) {
							// Keep only the category part
							var parts = html.split('In');
							if (parts.length > 1) {
								$metaDiv.html('<div>In ' + parts[1] + '</div>');
							}
						}
					}
				});
				// Also handle the older template structure
				$('.reign-tutor-courses .tutor-meta-course-cat').each(function() {
					var html = $(this).html();
					if (html && html.includes('In')) {
						var parts = html.split('In');
						if (parts.length > 1) {
							$(this).html('In ' + parts[1]);
							// Hide the author avatar/link part
							$(this).siblings('.tutor-meta-course-by').hide();
						}
					}
				});
				<?php endif; ?>
			});
			</script>
			<?php
		}
		?>
	</div>
	<style>
		/* Course count styling */
		.reign-courses-count {
			margin-bottom: 15px;
			font-weight: 600;
			color: #666;
		}
		
		/* Element visibility controls */
		/* Hide instructor - hides the entire author/category meta section */
		.reign-tutor-courses.hide-instructor .tutor-meta-course-by-cat { display: none !important; }
		.reign-tutor-courses.hide-instructor .tutor-meta:has(a[href*="/profile/"]) { display: none !important; }
		/* For newer templates that have author in last meta div */
		.reign-tutor-courses.hide-instructor .tutor-course-listing .tutor-meta:last-of-type { display: none !important; }

		/* Hide duration - using parent div selector for cleaner hiding */
		.reign-tutor-courses.hide-duration .tutor-meta > div:has(.tutor-icon-clock-line) { display: none !important; }

		/* Hide students count - using parent div selector */
		.reign-tutor-courses.hide-students .tutor-meta > div:has(.tutor-icon-user-line) { display: none !important; }

		/* Hide lessons count */
		.reign-tutor-courses.hide-lessons .tutor-meta > div:has(.tutor-icon-document-text) { display: none !important; }

		/* Fallback for browsers that don't support :has() selector */
		@supports not selector(:has(*)) {
			.reign-tutor-courses.hide-duration .tutor-icon-clock-line,
			.reign-tutor-courses.hide-duration .tutor-icon-clock-line + .tutor-meta-value { display: none !important; }

			.reign-tutor-courses.hide-students .tutor-icon-user-line,
			.reign-tutor-courses.hide-students .tutor-icon-user-line + .tutor-meta-value { display: none !important; }

			.reign-tutor-courses.hide-lessons .tutor-icon-document-text,
			.reign-tutor-courses.hide-lessons .tutor-icon-document-text + span { display: none !important; }
		}
		
		/* Layout controls */
		.reign-tutor-courses.layout-list .tutor-courses,
		.reign-tutor-courses.layout-list .tutor-courses-wrap {
			display: block !important;
		}
		.reign-tutor-courses.layout-list .tutor-course,
		.reign-tutor-courses.layout-list .tutor-course-card {
			display: flex !important;
			margin-bottom: 20px;
			align-items: flex-start;
			width: 100% !important;
		}
		.reign-tutor-courses.layout-list .tutor-course .tutor-course-thumbnail,
		.reign-tutor-courses.layout-list .tutor-course-card .course-card-image {
			flex: 0 0 200px;
			margin-right: 20px;
		}
		.reign-tutor-courses.layout-list .tutor-course .tutor-course-details,
		.reign-tutor-courses.layout-list .tutor-course-card .tutor-course-card-body {
			flex: 1;
		}
		
		/* Grid layout (default) */
		.reign-tutor-courses.layout-grid .tutor-courses,
		.reign-tutor-courses.layout-grid .tutor-courses-wrap {
			display: flex !important;
			flex-wrap: wrap;
		}
		
		/* Progress styling */
		.reign-course-progress {
			padding: 10px 15px;
			margin: 10px 0;
			background: #f7f7f7;
			border-radius: 4px;
		}

		.reign-course-progress .tutor-fs-6 {
			font-size: 13px;
		}

		.reign-course-progress .tutor-d-flex {
			display: flex;
		}

		.reign-course-progress .tutor-align-center {
			align-items: center;
		}

		.reign-course-progress .tutor-justify-between {
			justify-content: space-between;
		}

		.reign-course-progress .tutor-color-secondary {
			color: #6b7280;
		}

		.reign-course-progress .tutor-mt-12 {
			margin-top: 12px;
		}

		.reign-course-progress .tutor-progress-bar {
			position: relative;
			width: 100%;
			height: 8px;
			background-color: #e5e7eb;
			border-radius: 4px;
			overflow: hidden;
		}

		.reign-course-progress .tutor-progress-value {
			position: absolute;
			top: 0;
			left: 0;
			height: 100%;
			background-color: #3b82f6;
			border-radius: 4px;
			width: var(--tutor-progress-value, 0%);
			transition: width 0.3s ease;
		}

		/* Responsive adjustments */
		@media (max-width: 768px) {
			.reign-tutor-courses.layout-grid .tutor-courses,
			.reign-tutor-courses.layout-grid .tutor-course-list {
				grid-template-columns: 1fr !important;
			}

			.reign-tutor-courses.layout-list .tutor-course .tutor-course-thumbnail,
			.reign-tutor-courses.layout-list .tutor-course-card .course-card-image {
				flex: 0 0 100px;
				margin-right: 10px;
			}

			.reign-courses-count {
				font-size: 14px;
			}
		}

		@media (min-width: 769px) and (max-width: 1024px) {
			.reign-tutor-courses.layout-grid .tutor-courses,
			.reign-tutor-courses.layout-grid .tutor-course-list {
				grid-template-columns: repeat(2, 1fr) !important;
			}
		}

		/* Ensure course cards are clickable */
		.reign-tutor-courses .tutor-course-card,
		.reign-tutor-courses .tutor-course {
			cursor: pointer;
			transition: transform 0.2s ease;
		}

		.reign-tutor-courses .tutor-course-card:hover,
		.reign-tutor-courses .tutor-course:hover {
			transform: translateY(-2px);
		}

		/* Fix for filter dropdowns */
		.reign-tutor-courses select.tutor-form-select {
			appearance: auto;
			-webkit-appearance: menulist;
			-moz-appearance: menulist;
		}
	</style>
	<?php

	// Remove hook after use
	if ( $reign_show_progress ) {
		remove_action( 'tutor_course/loop/before_footer', 'reign_tutorlms_add_progress_to_course_loop', 15 );
	}

	// Clear global flags
	$reign_show_progress = false;
	$reign_enrolled_user_id = 0;

	return ob_get_clean();
}
add_shortcode( 'reign_tutor_course', 'reign_tutorlms_tutor_course_shortcode' );

/**
 * Get profile user ID based on context
 */
function reign_tutorlms_get_profile_user_id( $specified_user_id = '' ) {
	// If specific user ID provided
	if ( ! empty( $specified_user_id ) ) {
		if ( $specified_user_id === 'current' ) {
			return get_current_user_id();
		}
		return intval( $specified_user_id );
	}
	
	// Auto-detect based on platform
	
	// BuddyPress/BuddyBoss
	if ( function_exists( 'bp_displayed_user_id' ) && bp_displayed_user_id() ) {
		return bp_displayed_user_id();
	}
	
	// PeepSo - using native PeepSo method
	if ( class_exists( 'PeepSoProfileShortcode' ) ) {
		$peepso_profile = PeepSoProfileShortcode::get_instance();
		if ( $peepso_profile && method_exists( $peepso_profile, 'get_view_user_id' ) ) {
			$peepso_user_id = $peepso_profile->get_view_user_id();
			if ( $peepso_user_id ) {
				return $peepso_user_id;
			}
		}
	}
	
	// Default to current user
	return get_current_user_id();
}

/**
 * Get enrolled course IDs for a user
 */
function reign_tutorlms_get_enrolled_course_ids( $user_id, $status = 'all' ) {
	if ( ! function_exists( 'tutor_utils' ) ) {
		return array();
	}
	
	$enrolled_courses = tutor_utils()->get_enrolled_courses_by_user( $user_id );
	
	if ( ! $enrolled_courses || ! $enrolled_courses->have_posts() ) {
		return array();
	}
	
	$course_ids = array();
	
	while ( $enrolled_courses->have_posts() ) {
		$enrolled_courses->the_post();
		$course_id = get_the_ID();
		
		// Filter by status if specified
		if ( $status !== 'all' ) {
			$progress = tutor_utils()->get_course_completed_percent( $course_id, $user_id );
			
			switch ( $status ) {
				case 'completed':
					if ( $progress >= 100 ) {
						$course_ids[] = $course_id;
					}
					break;
				case 'in_progress':
				case 'in-progress':
					if ( $progress > 0 && $progress < 100 ) {
						$course_ids[] = $course_id;
					}
					break;
				case 'not_started':
				case 'not-started':
					if ( $progress == 0 ) {
						$course_ids[] = $course_id;
					}
					break;
				default:
					$course_ids[] = $course_id;
			}
		} else {
			$course_ids[] = $course_id;
		}
	}
	
	wp_reset_postdata();
	
	return $course_ids;
}

/**
 * Course Categories Shortcode
 */
function reign_tutorlms_course_categories_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'count'       => '8',
		'columns'     => '4',
		'orderby'     => 'name',
		'order'       => 'ASC',
		'show_count'  => 'yes',
		'show_image'  => 'yes',
		'hide_empty'  => 'yes',
	), $atts, 'reign_course_categories' );
	
	$args = array(
		'taxonomy'   => 'course-category',
		'orderby'    => $atts['orderby'],
		'order'      => $atts['order'],
		'number'     => intval( $atts['count'] ),
		'hide_empty' => ( $atts['hide_empty'] === 'yes' ),
	);
	
	$categories = get_terms( $args );
	
	if ( empty( $categories ) || is_wp_error( $categories ) ) {
		return '<div class="reign-no-categories">' . __( 'No categories found.', 'reign-tutorlms-addon' ) . '</div>';
	}
	
	ob_start();
	?>
	<div class="reign-course-categories columns-<?php echo esc_attr( $atts['columns'] ); ?>">
		<?php foreach ( $categories as $category ) : ?>
			<div class="reign-category-item">
				<a href="<?php echo esc_url( get_term_link( $category ) ); ?>">
					<?php if ( $atts['show_image'] === 'yes' ) : ?>
						<div class="category-image">
							<?php
							// Get category image if available
							$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
							if ( $thumbnail_id ) {
								echo wp_get_attachment_image( $thumbnail_id, 'medium' );
							} else {
								// Show placeholder or first course image
								echo '<div class="placeholder-image"></div>';
							}
							?>
						</div>
					<?php endif; ?>
					<h3 class="category-title"><?php echo esc_html( $category->name ); ?></h3>
					<?php if ( $atts['show_count'] === 'yes' ) : ?>
						<span class="category-count"><?php echo esc_html( $category->count ); ?> <?php _e( 'Courses', 'reign-tutorlms-addon' ); ?></span>
					<?php endif; ?>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
	<style>
		.reign-course-categories {
			display: grid;
			gap: 20px;
			margin: 20px 0;
		}
		.reign-course-categories.columns-1 { grid-template-columns: 1fr; }
		.reign-course-categories.columns-2 { grid-template-columns: repeat(2, 1fr); }
		.reign-course-categories.columns-3 { grid-template-columns: repeat(3, 1fr); }
		.reign-course-categories.columns-4 { grid-template-columns: repeat(4, 1fr); }
		.reign-course-categories.columns-6 { grid-template-columns: repeat(6, 1fr); }
		.reign-category-item {
			border: 1px solid var(--reign-site-border-color, #e5e5e5);
			border-radius: 8px;
			padding: 20px;
			text-align: center;
			transition: all 0.3s ease;
		}
		.reign-category-item {
			background-color: var(--reign-site-sections-bg-color, #fff);
		}
		.reign-category-item:hover {
			box-shadow: var(--reign-more-options-box-shadow), 0 4px 15px rgba(0,0,0,0.1);
			transform: translateY(-2px);
		}
		.reign-category-item a {
			text-decoration: none;
			color: inherit;
		}
		.category-image {
			margin-bottom: 15px;
			height: 150px;
			background: #f5f5f5;
			border-radius: 4px;
			overflow: hidden;
		}
		.category-image img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}
		.placeholder-image {
			width: 100%;
			height: 100%;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		}
		.category-title {
			margin: 10px 0;
			font-size: 18px;
		}
		.category-count {
			color: var(--reign-site-body-text-color, #666);
			font-size: 14px;
		}
		@media (max-width: 991px) {
			.reign-course-categories.columns-4,
			.reign-course-categories.columns-6 {
				grid-template-columns: repeat(3, 1fr);
			}
		}
		@media (max-width: 768px) {
			.reign-course-categories.columns-3,
			.reign-course-categories.columns-4,
			.reign-course-categories.columns-6 {
				grid-template-columns: repeat(2, 1fr);
			}
		}
		@media (max-width: 480px) {
			.reign-course-categories {
				grid-template-columns: 1fr !important;
			}
		}
	</style>
	<?php
	return ob_get_clean();
}
add_shortcode( 'reign_course_categories', 'reign_tutorlms_course_categories_shortcode' );

/**
 * Add My Courses tab to BuddyPress/BuddyBoss profiles
 */
function reign_tutorlms_add_profile_courses_tab() {
	$settings = get_option( 'reign_options', array() );
	
	// Check if tab is enabled
	if ( ! isset( $settings['tutorlms']['enable_profile_courses_tab'] ) || ! $settings['tutorlms']['enable_profile_courses_tab'] ) {
		return;
	}
	
	// Only proceed if BuddyPress is active
	if ( ! function_exists( 'bp_core_new_nav_item' ) ) {
		return;
	}
	
	bp_core_new_nav_item( array(
		'name'                => __( 'My Courses', 'reign-tutorlms-addon' ),
		'slug'                => 'courses',
		'position'            => 75,
		'screen_function'     => 'reign_tutorlms_courses_tab_screen',
		'default_subnav_slug' => 'courses',
		'item_css_id'         => 'tutorlms-courses'
	) );
}
add_action( 'bp_setup_nav', 'reign_tutorlms_add_profile_courses_tab' );

/**
 * BuddyPress courses tab screen function
 */
function reign_tutorlms_courses_tab_screen() {
	add_action( 'bp_template_content', 'reign_tutorlms_courses_tab_content' );
	bp_core_load_template( 'members/single/plugins' );
}

/**
 * BuddyPress courses tab content
 */
function reign_tutorlms_courses_tab_content() {
	$user_id = bp_displayed_user_id();
	echo do_shortcode( '[reign_tutor_course my_courses="yes" user_id="' . $user_id . '" show_progress="yes" count="12" show_pagination="on"]' );
}

/**
 * Add My Courses tab to PeepSo profiles
 */
function reign_tutorlms_peepso_profile_tabs( $tabs ) {
	$settings = get_option( 'reign_options', array() );
	
	// Check if tab is enabled
	if ( ! isset( $settings['tutorlms']['enable_profile_courses_tab'] ) || ! $settings['tutorlms']['enable_profile_courses_tab'] ) {
		return $tabs;
	}
	
	$tabs['tutorlms_courses'] = array(
		'href'  => 'tutorlms_courses',
		'label' => __( 'My Courses', 'reign-tutorlms-addon' ),
		'icon'  => 'gcis gci-graduation-cap',
	);
	
	return $tabs;
}
add_filter( 'peepso_navigation_profile', 'reign_tutorlms_peepso_profile_tabs' );

/**
 * PeepSo courses tab content
 */
function reign_tutorlms_peepso_profile_segment_tutorlms_courses() {
	$peepso_profile = PeepSoProfileShortcode::get_instance();
	if ( $peepso_profile && method_exists( $peepso_profile, 'get_view_user_id' ) ) {
		$user_id = $peepso_profile->get_view_user_id();
		echo do_shortcode( '[reign_tutor_course my_courses="yes" user_id="' . $user_id . '" show_progress="yes" count="12" show_pagination="on"]' );
	}
}
add_action( 'peepso_profile_segment_tutorlms_courses', 'reign_tutorlms_peepso_profile_segment_tutorlms_courses' );

/**
 * Add CSS for BuddyPress navigation icon
 */
function reign_tutorlms_bp_nav_icon_css() {
	$settings = get_option( 'reign_options', array() );
	
	if ( ! isset( $settings['tutorlms']['enable_profile_courses_tab'] ) || ! $settings['tutorlms']['enable_profile_courses_tab'] ) {
		return;
	}
	
	if ( ! function_exists( 'bp_is_active' ) ) {
		return;
	}
	?>
	<style>
		/* BuddyPress/BuddyBoss icon support */
		.bp-navs li#tutorlms-courses-personal-li a:before,
		.bp-navs li#courses-personal-li a:before,
		#buddypress .item-list-tabs li#courses-personal-li a:before {
			content: "\f19d"; /* Font Awesome graduation cap */
			font-family: FontAwesome;
		}
		
		/* Reign theme specific */
		.reign-nav-iconic #tutorlms-courses-personal-li > a:before {
			content: "\f19d"; /* Graduation cap icon */
		}
	</style>
	<?php
}
add_action( 'wp_head', 'reign_tutorlms_bp_nav_icon_css' );

/**
 * AJAX handler to get course progress
 */
function reign_tutorlms_ajax_get_course_progress() {
	// Verify nonce for security
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'reign_tutorlms_nonce' ) ) {
		wp_send_json_error( 'Invalid nonce' );
	}

	// Get course IDs and user ID
	$course_ids = isset( $_POST['course_ids'] ) ? array_map( 'intval', $_POST['course_ids'] ) : array();
	$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : get_current_user_id();

	if ( empty( $course_ids ) || ! $user_id ) {
		wp_send_json_error( 'Invalid parameters' );
	}

	$progress_data = array();

	foreach ( $course_ids as $course_id ) {
		// Get progress for this course
		if ( function_exists( 'tutor_utils' ) ) {
			$course_progress = tutor_utils()->get_course_completed_percent( $course_id, $user_id, true );
			$progress_data[ $course_id ] = array(
				'completed_percent' => isset( $course_progress['completed_percent'] ) ? $course_progress['completed_percent'] : 0,
				'completed_count'   => isset( $course_progress['completed_count'] ) ? $course_progress['completed_count'] : 0,
				'total_count'       => isset( $course_progress['total_count'] ) ? $course_progress['total_count'] : 0,
			);
		}
	}

	wp_send_json_success( $progress_data );
}
add_action( 'wp_ajax_reign_tutorlms_get_progress', 'reign_tutorlms_ajax_get_course_progress' );
add_action( 'wp_ajax_nopriv_reign_tutorlms_get_progress', 'reign_tutorlms_ajax_get_course_progress' );

/**
 * Enqueue scripts for AJAX progress loading and filter fixes
 */
function reign_tutorlms_enqueue_scripts() {
	// Localize AJAX data
	wp_localize_script( 'jquery', 'reign_tutorlms_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'reign_tutorlms_nonce' ),
	) );

	// Enqueue filter fix for TutorLMS
	if ( function_exists( 'tutor' ) ) {
		// Check if we're on a page with TutorLMS content
		if ( is_archive() || is_singular( 'courses' ) || has_shortcode( get_post_field( 'post_content', get_the_ID() ), 'tutor_course' ) || has_shortcode( get_post_field( 'post_content', get_the_ID() ), 'reign_tutor_course' ) ) {
			// First check if the fix file exists in TutorLMS plugin
			$tutor_fix_path = WP_PLUGIN_DIR . '/tutor/assets/js/tutor-filter-fix.js';
			if ( file_exists( $tutor_fix_path ) ) {
				wp_enqueue_script(
					'tutor-filter-fix',
					plugins_url( 'tutor/assets/js/tutor-filter-fix.js' ),
					array( 'jquery' ),
					'1.0.0',
					true
				);
			} else {
				// Use inline script as fallback
				wp_add_inline_script( 'tutor-front', '
					jQuery(document).ready(function($) {
						// Add missing hidden fields if they don\'t exist
						function ensureFilterFields() {
							// Check and add course_filter_categories if missing
							if ($("#course_filter_categories").length === 0) {
								$("body").append(\'<input type="hidden" id="course_filter_categories" value="[]" />\');
							} else {
								var catVal = $("#course_filter_categories").val();
								if (!catVal || catVal === "undefined" || catVal === "") {
									$("#course_filter_categories").val("[]");
								}
							}

							// Check and add course_filter_exclude_ids if missing
							if ($("#course_filter_exclude_ids").length === 0) {
								$("body").append(\'<input type="hidden" id="course_filter_exclude_ids" value="[]" />\');
							} else {
								var exclVal = $("#course_filter_exclude_ids").val();
								if (!exclVal || exclVal === "undefined" || exclVal === "") {
									$("#course_filter_exclude_ids").val("[]");
								}
							}

							// Check and add course_filter_post_ids if missing
							if ($("#course_filter_post_ids").length === 0) {
								$("body").append(\'<input type="hidden" id="course_filter_post_ids" value="[]" />\');
							} else {
								var postVal = $("#course_filter_post_ids").val();
								if (!postVal || postVal === "undefined" || postVal === "") {
									$("#course_filter_post_ids").val("[]");
								}
							}
						}

						// Run the fix
						ensureFilterFields();
					});
				' );
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'reign_tutorlms_enqueue_scripts', 20 );

/**
 * Add progress to course cards via filter
 */
function reign_tutorlms_add_progress_to_course_loop() {
	// Check if we're in our shortcode context
	global $reign_show_progress, $reign_enrolled_user_id;

	if ( ! $reign_show_progress ) {
		return;
	}

	$course_id = get_the_ID();
	$user_id = $reign_enrolled_user_id ? $reign_enrolled_user_id : get_current_user_id();

	if ( ! $user_id ) {
		return;
	}

	// Check if user is enrolled
	$is_enrolled = tutor_utils()->is_enrolled( $course_id, $user_id );

	if ( ! $is_enrolled ) {
		return;
	}

	// Get progress
	$course_progress = tutor_utils()->get_course_completed_percent( $course_id, $user_id, true );
	$progress_percent = isset( $course_progress['completed_percent'] ) ? $course_progress['completed_percent'] : 0;
	$completed_count = isset( $course_progress['completed_count'] ) ? $course_progress['completed_count'] : 0;
	$total_count = isset( $course_progress['total_count'] ) ? $course_progress['total_count'] : 0;

	?>
	<div class="tutor-course-progress reign-course-progress">
		<div class="tutor-fs-6 tutor-color-secondary tutor-d-flex tutor-align-center tutor-justify-between">
			<span><?php echo esc_html( $completed_count ); ?>/<?php echo esc_html( $total_count ); ?></span>
			<span><?php echo esc_html( $progress_percent ); ?>% <?php esc_html_e( 'Complete', 'reign-tutorlms-addon' ); ?></span>
		</div>
		<div class="tutor-progress-bar tutor-mt-12" style="--tutor-progress-value:<?php echo esc_attr( $progress_percent ); ?>%;">
			<span class="tutor-progress-value" aria-hidden="true"></span>
		</div>
	</div>
	<?php
}

/**
 * Get default TutorLMS theme settings
 */
function reign_get_tutorlms_theme_defaults() {
	return array(
		'profile' => array(
			'enable_profile_courses_tab' => 0
		)
	);
}


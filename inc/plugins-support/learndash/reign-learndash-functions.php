<?php
/**
 * Support For LearnDash
 *
 * @package reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'reign_learndash_get_single_template' ) ) {
	/**
	 * Filters the single template used for LearnDash groups.
	 *
	 * @param string $single_template The path to the single template.
	 * @return string The modified path to the single template.
	 */
	function reign_learndash_get_single_template( $single_template ) {

		// LearnDash_Theme_Register is a separate class from SFWD_LMS
		// (the gate this file is loaded behind). On certain stripped
		// LD builds it isn't shipped, so a direct static call would fatal.
		if ( ! class_exists( 'LearnDash_Theme_Register' ) ) {
			return $single_template;
		}

		if ( LearnDash_Theme_Register::get_active_theme_key() != 'ld30' ) {
			return $single_template;
		}

		if ( get_post_type() === 'groups' ) {
			$template = 'single-sfwd-groups.php';

			if ( file_exists( get_stylesheet_directory() . '/' . $template ) ) {
				$single_template = get_stylesheet_directory() . '/' . $template;
			} elseif ( file_exists( get_template_directory() . '/' . $template ) ) {
				$single_template = get_template_directory() . '/' . $template;
			}
		}

		return $single_template;
	}

	add_filter( 'single_template', 'reign_learndash_get_single_template', 10 );
}

/**
 * Adds a body class for LearnDash courses when the Reign LearnDash Addon is not active.
 *
 * @param array $classes An array of existing body classes.
 * @return array The modified array of body classes.
 */
function reign_learndash_body_class( $classes ) {

	if ( is_singular( 'sfwd-courses' ) ) {
		$classes[] = 'learndash-course-layout-udemy';
	}

	if ( is_singular( 'groups' ) ) {
		$classes[] = 'learndash-group-layout-udemy';
	}

	return $classes;
}

add_filter( 'body_class', 'reign_learndash_body_class' );

if ( ! function_exists( 'lm_filter_course_author_url' ) ) {
	/**
	 * Filters and modifies the URL of the course author.
	 *
	 * @param string $author_url The default URL of the course author.
	 * @return string $author_url The modified course author URL.
	 */
	function lm_filter_course_author_url( $author_url ) {
		$author_url .= '?post_type=sfwd-courses';
		return $author_url;
	}
}

add_filter( 'lm_filter_course_author_url', 'lm_filter_course_author_url', 10, 1 );

/**
 * Retrieves the URL of the default course image.
 *
 * @return string The URL of the default course image.
 */
if ( ! function_exists( 'get_reign_ld_default_course_img_url' ) ) {
	function get_reign_ld_default_course_img_url() {
		return get_template_directory_uri() . '/assets/img/default-course-image.png';
	}
}

if ( ! function_exists( 'get_reign_ld_default_course_img_html' ) ) {
	/**
	 * Generates the HTML for the default course image.
	 *
	 * @return string The HTML string for the default course image.
	 */
	function get_reign_ld_default_course_img_html() {
		$default_course_img_url = get_reign_ld_default_course_img_url();

		$image_html = '<img src="' . esc_url( $default_course_img_url ) . '" alt="' . esc_attr__( 'Default Course Image', 'reign' ) . '" />';

		return apply_filters( 'reign_ld_default_course_image_html', $image_html );
	}
}

if ( ! function_exists( 'reign_get_course_user_ids' ) ) {
	/**
	 * Retrieves the user IDs of students enrolled in a specific course.
	 *
	 * @param int $course_id The ID of the course.
	 * @param int $chunk_size The number of users to retrieve per query (default is 500).
	 * @return array An array of user IDs enrolled in the course.
	 */
	function reign_get_course_user_ids( $course_id, $chunk_size = 500 ) {
		$course_user_ids = array();
		$offset          = 0;

		do {
			$user_query_args = array(
				'meta_key' => 'course_' . $course_id . '_access_from',
				'number'   => $chunk_size,
				'offset'   => $offset,
				'fields'   => 'ID',
			);

			$students_chunk = get_users( $user_query_args );

			if ( ! empty( $students_chunk ) ) {
				$course_user_ids = array_merge( $course_user_ids, $students_chunk );
				$offset         += $chunk_size;
			} else {
				break;
			}
			$students_chunk_count = count( $students_chunk );
		} while ( $students_chunk_count === $chunk_size );

		return $course_user_ids;
	}
}

if ( ! function_exists( 'reign_get_group_user_ids' ) ) {
	/**
	 * Retrieves user IDs of students enrolled in a specific LearnDash group.
	 *
	 * @param int $group_id The ID of the LearnDash group.
	 * @param int $chunk_size The number of users to retrieve per query (default is 500).
	 * @return array An array of user IDs enrolled in the LearnDash group.
	 */
	function reign_get_group_user_ids( $group_id, $chunk_size = 500 ) {
		$group_user_ids = array();
		$offset         = 0;

		do {
			$user_query_args = array(
				'meta_key' => 'learndash_group_users_' . $group_id,
				'number'   => $chunk_size,
				'offset'   => $offset,
				'fields'   => 'ID',
			);

			$students_chunk = get_users( $user_query_args );

			if ( ! empty( $students_chunk ) ) {
				$group_user_ids = array_merge( $group_user_ids, $students_chunk );
				$offset        += $chunk_size;
			} else {
				break;
			}
			$students_chunk_count = count( $students_chunk );
		} while ( $students_chunk_count === $chunk_size );

		return $group_user_ids;
	}
}

if ( ! function_exists( 'reign_learndash_single_course_header' ) ) {
	/**
	 * Displays the header for the single LearnDash course page.
	 *
	 * This function renders the course title, description, instructors, and additional information such as reviews and student count.
	 */
	function reign_learndash_single_course_header() {
		$breadcrumb = get_theme_mod( 'reign_site_enable_breadcrumb', true );
		$args       = array(
			'post_id' => get_the_ID(),
			'status'  => 1,
		);
		$comments   = get_comments( $args );

		$course_id = get_the_ID();

		$description = get_post_meta( $course_id, '_learndash_course_grid_short_description', true );

		$author_id          = array( get_post_field( 'post_author', $course_id ) );
		$_ld_instructor_ids = get_post_meta( $course_id, '_ld_instructor_ids', true );
		if ( empty( $_ld_instructor_ids ) ) {
			$_ld_instructor_ids = array();
		}
		$ir_shared_instructor_ids = get_post_meta( $course_id, 'ir_shared_instructor_ids', true );
		if ( '' !== $ir_shared_instructor_ids ) {
			$ir_shared_instructor_ids = explode( ',', $ir_shared_instructor_ids );
		} else {
			$ir_shared_instructor_ids = array();
		}

		$author_ids = array_merge( $author_id, $_ld_instructor_ids, $ir_shared_instructor_ids );
		$author_ids = array_unique( $author_ids );

		$show_cover_image  = '';
		$cover_image_class = '';
		$image_id          = get_post_meta( $course_id, '_course_image_id', true );
		if ( $image_id && get_post( $image_id ) ) {
			$_course_image     = wp_get_attachment_image_src( $image_id, 'large' );
			$show_cover_image  = 1;
			$cover_image_class = 'course-cover-image';
		}

		?>
		<div class="learndash-single-course-header <?php echo esc_attr( $cover_image_class ); ?>"
			<?php
			if ( 1 == $show_cover_image ) :
				?>
			style="background-image:url('<?php echo esc_url( $_course_image[0] ); ?>')" <?php endif; ?>>
			<div class="container">
				<div class="learndash-single-course-header-inner-wrap">
					<?php if ( reign_is_truthy( $breadcrumb ) && function_exists( 'reign_breadcrumbs' ) ) : ?>
						<div class="lm-breadcrumbs-wrapper">
							<div class="container"><?php reign_breadcrumbs(); ?></div>
						</div>
					<?php endif; ?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<p class="course-header-short-description">
						<?php
						if ( '' !== $description ) {
							echo wp_kses_post( $description );
						} else {
							the_excerpt();
						}
						?>
					</p>
					<div class="learndash-course-instructor">
						<?php
						if ( ! empty( $author_ids ) ) {
							$instructor_image = '';
							$instructor_name  = '';
							$i                = 0;

							remove_filter( 'author_link', 'wpforo_change_author_default_page' );
							foreach ( $author_ids as $insttuctor_id ) {
								$author_avatar_url = get_avatar_url( $insttuctor_id );
								$author_url        = apply_filters( 'lm_filter_course_author_url', get_author_posts_url( $insttuctor_id ), $insttuctor_id );
								$first_name        = get_the_author_meta( 'user_firstname', $insttuctor_id );
								$last_name         = get_the_author_meta( 'user_lastname', $insttuctor_id );
								$author_name       = get_the_author_meta( 'display_name', $insttuctor_id );
								if ( ! empty( $first_name ) && ! empty( $last_name ) && '' === $author_name ) {
									$author_name = $first_name . ' ' . $last_name;
								}
								if ( $i < 3 ) {
									$instructor_image .= '<img alt="instructor avatar" src="' . esc_url( $author_avatar_url ) . '" class="lm-author-avatar" width="40" height="40">';
								}
								$instructor_name .= '<a href="' . esc_url( $author_url ) . '" target="_blank">' . esc_html( $author_name ) . '</a>, ';
								++$i;
							}
							?>
							<div class="instructor-avatar">
								<?php echo wp_kses_post( $instructor_image ); ?>
							</div>
							<div class="instructor-name">
								<?php echo wp_kses_post( substr( $instructor_name, 0, -2 ) ); ?>
							</div>
							<?php
						}
						?>
					</div>
					<div class="last-update-date">
						<span class="last-update-date_icon">
							<i class="far fa-calendar"></i>
						</span>
						<?php /* translators: %s: Last modified date. */ ?>
						<span><?php printf( esc_html__( 'Last updated on %s', 'reign' ), esc_html( get_the_modified_date() ) ); ?> </span>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

add_action(
	'template_redirect',
	function () {
		if ( is_singular( 'sfwd-courses' ) ) {
			remove_action( 'reign_post_content_begins', array( Reign_Theme_Structure::instance(), 'render_post_meta_section' ) );
			add_action( 'reign_before_content', 'reign_learndash_single_course_header' );
		}
	}
);

if ( ! function_exists( 'reign_learndash_single_group_header' ) ) {
	/**
	 * Displays the header for the single LearnDash group page.
	 *
	 * This function renders the course title, description, instructors, and additional information.
	 */
	function reign_learndash_single_group_header() {
		$breadcrumb = get_theme_mod( 'reign_site_enable_breadcrumb', true );

		$args     = array(
			'post_id' => get_the_ID(),
			'status'  => 1,
		);
		$comments = get_comments( $args );

		$users_enrolled = reign_get_group_user_ids( get_the_ID() );

		$description = get_post_meta( get_the_ID(), '_learndash_group_grid_short_description', true );

		$author_id         = array( get_post_field( 'post_author', get_the_ID() ) );
		$group_id          = get_the_ID();
		$leader_meta_key   = 'learndash_group_leaders_' . absint( $group_id );
		$ld_gp_leaders_ids = get_post_meta( $group_id, $leader_meta_key, true );
		$gp_leader_ids     = array();

		if ( ! empty( $ld_gp_leaders_ids ) ) {
			$gp_leader_ids = maybe_unserialize( $ld_gp_leaders_ids );
		} else {
			global $wpdb;
			$gp_leader_ids = $wpdb->get_col(
				$wpdb->prepare(
					"
                SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %d
            ",
					$leader_meta_key,
					$group_id
				)
			);
		}

		$author_ids = array_merge( $author_id, $gp_leader_ids );
		$author_ids = array_unique( $author_ids );

		$show_cover_image  = '';
		$cover_image_class = '';
		$image_id          = get_post_meta( $group_id, '_group_image_id', true );
		if ( $image_id && get_post( $image_id ) ) {
			$_course_image     = wp_get_attachment_image_src( $image_id, 'large' );
			$show_cover_image  = 1;
			$cover_image_class = 'course-cover-image';
		}

		?>
		<div class="learndash-single-course-header learndash-single-group-header <?php echo esc_attr( $cover_image_class ); ?>"
			<?php if ( 1 == $show_cover_image ) : ?>
			style="background-image:url('<?php echo esc_url( $_course_image[0] ); ?>')" <?php endif; ?>>
			<div class="container">
				<div class="learndash-single-course-header-inner-wrap learndash-single-group-header-inner-wrap">
					<?php if ( reign_is_truthy( $breadcrumb ) && function_exists( 'reign_breadcrumbs' ) ) : ?>
						<div class="lm-breadcrumbs-wrapper">
							<div class="container"><?php reign_breadcrumbs(); ?></div>
						</div>
					<?php endif; ?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<p class="course-header-short-description">
						<?php
						if ( '' !== $description ) {
							echo wp_kses_post( $description );
						} else {
							the_excerpt();
						}
						?>
					</p>
					<div class="learndash-course-info">
						<div class="learndash-course-student-enrollment">
							<?php
							$enrolled_count = count( $users_enrolled );
							/* translators: %d: Number of enrolled users. */
							printf( esc_html( _n( '%d user', '%d users', $enrolled_count, 'reign' ) ), absint( $enrolled_count ) );
							?>
						</div>
					</div>
					<div class="learndash-course-instructor">
						<?php
						if ( ! empty( $author_ids ) ) {
							$instructor_image = '';
							$instructor_name  = '';
							$i                = 0;

							remove_filter( 'author_link', 'wpforo_change_author_default_page' );
							foreach ( $author_ids as $insttuctor_id ) {
								$author_avatar_url = get_avatar_url( $insttuctor_id );
								$author_url        = apply_filters( 'lm_filter_course_author_url', get_author_posts_url( $insttuctor_id ), $insttuctor_id );
								$first_name        = get_the_author_meta( 'user_firstname', $insttuctor_id );
								$last_name         = get_the_author_meta( 'user_lastname', $insttuctor_id );
								$author_name       = get_the_author_meta( 'display_name', $insttuctor_id );
								if ( ! empty( $first_name ) && ! empty( $last_name ) && '' === $author_name ) {
									$author_name = $first_name . ' ' . $last_name;
								}
								if ( $i < 3 ) {
									$instructor_image .= '<img alt="instructor avatar" src="' . $author_avatar_url . '" class="lm-author-avatar" width="40" height="40">';
								}
								$instructor_name .= $author_name . ', ';
								++$i;
							}
							?>
							<div class="instructor-avatar">
								<?php echo wp_kses_post( $instructor_image ); ?>
							</div>
							<div class="instructor-name">
								<?php
								echo esc_html( substr( $instructor_name, 0, -2 ) );
								?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

add_action(
	'template_redirect',
	function () {
		if ( is_singular( 'groups' ) ) {
			remove_action( 'reign_post_content_begins', array( Reign_Theme_Structure::instance(), 'render_post_meta_section' ) );
			add_action( 'reign_before_content', 'reign_learndash_single_group_header' );
		}
	}
);

if ( ! function_exists( 'remove_learndash_course_grid_course_list' ) ) {
	/**
	 * Override the LearnDash course grid template.
	 *
	 * @param string $filepath The original template file path.
	 * @param string $name The template name.
	 * @param array  $args The arguments passed to the template.
	 * @param bool   $echo Whether to echo the output.
	 * @param string $return_file_path Whether to return the file path.
	 *
	 * @return string The modified template file path.
	 */
	function remove_learndash_course_grid_course_list( $filepath, $name, $args, $echo, $return_file_path ) {
		global $post, $wp_query;

		// Check if the queried object contains the 'ld_course_list' shortcode.
		if ( is_a( $wp_query->queried_object, 'WP_Post' ) && has_shortcode( $wp_query->queried_object->post_content, 'ld_course_list' ) ) {

			// Remove the default course grid template filter.
			remove_filter( 'learndash_template', 'learndash_course_grid_course_list', 99, 5 );

			// Check if it's the course list template.
			if ( 'course_list_template' === $name ) {
				// Define the custom template file.
				$custom_template = 'learndash/ld30/ld-course-list-template.php';

				// Locate the template in the theme or child theme.
				$located_template = locate_template( $custom_template );

				// If the custom template exists, use it.
				if ( $located_template ) {
					$filepath = $located_template;
				}
			}
		}

		// Return the default or overridden template file path.
		return $filepath;
	}
}

if ( ! is_plugin_active( 'learndash-course-grid/learndash_course_grid.php' ) ) {
	add_filter( 'learndash_template', 'remove_learndash_course_grid_course_list', 50, 5 );
}

if ( ! function_exists( 'add_post_type' ) ) {
	/**
	 * Adds custom post types for LearnDash integration.
	 *
	 * @param array $post_types Existing post types.
	 * @return array Modified array of post types including custom ones.
	 */
	function add_post_type( $post_types ) {
		$post_types[] = array(
			'slug'        => 'sfwd-courses',
			'name'        => LearnDash_Custom_Label::get_label( 'courses' ),
			'has_archive' => true,
		);
		$post_types[] = array(
			'slug' => 'sfwd-lessons',
			/* translators: %s: LearnDash lessons label. */
			'name' => sprintf( __( 'Course %s', 'reign' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
		);
		$post_types[] = array(
			'slug' => 'sfwd-topic',
			/* translators: %s: LearnDash topics label. */
			'name' => sprintf( __( 'Course %s', 'reign' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
		);
		$post_types[] = array(
			'slug'      => 'sfwd-quiz',
			/* translators: %s: LearnDash quiz label. */
			'name'      => sprintf( __( 'Course %s', 'reign' ), LearnDash_Custom_Label::get_label( 'quiz' ) ),
			'is_single' => true,
		);
		return $post_types;
	}
}

add_filter( 'reign_customizer_supported_post_types', 'add_post_type', 10, 1 );

if ( ! function_exists( 'reign_learndash_course_comments' ) ) {
	/**
	 * Render the comment section on single course pages.
	 *
	 * Hooked to `reign_single_post_comment_section`, which is fired by both the
	 * theme's single-sfwd-courses.php and the Reign LearnDash Addon's equivalent
	 * template. The parent theme's original handler for this action was left
	 * commented out, causing courses to silently skip comments while lessons
	 * (which fall back to single.php) rendered them correctly.
	 */
	function reign_learndash_course_comments() {
		if ( ! is_singular( 'sfwd-courses' ) ) {
			return;
		}

		if ( post_password_required() ) {
			return;
		}

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}
}
add_action( 'reign_single_post_comment_section', 'reign_learndash_course_comments' );
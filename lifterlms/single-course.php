<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package     LifterLMS/Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

get_header();
?>

<?php do_action( 'reign_before_content_section' ); ?>

<div class="content-wrapper">
	<?php
	while ( have_posts() ) :
		the_post();
		$course_id             = get_the_ID();
		$course                = class_exists( 'LLMS_Course' ) ? new LLMS_Course( $course_id ) : null;
		$course_sales_page_url = $course ? $course->get_sales_page_url() : '';

		global $post;
		$product     = class_exists( 'LLMS_Product' ) ? new LLMS_Product( $post->ID ) : null;
		$is_enrolled = $product ? llms_is_user_enrolled( get_current_user_id(), $product->get( 'id' ) ) : false;

		if ( class_exists( 'Reign_LifterLMS_Addon' ) ) {
			$course_reviews_analytics = get_rlla_llms_course_reviews_analytics( get_the_ID() );
			$total_reviews            = $course_reviews_analytics['total_reviews'];
			$reviews_percentage       = $course_reviews_analytics['reviews_percentage'];
		}

		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'rlla-ld-course-item-single rlla-course-item-wrapper rlla-course-item-' . $course_id ); ?> >

			<?php if ( is_single() ) { ?>
			<div class="rlla-vw-container rlla-llms-banner">

				<?php
				$course_cover_photo = false;
				$image_id           = get_post_meta( get_the_ID(), '_custom_cover_photo_id', true );

				if ( $image_id && get_post( $image_id ) ) {
					$_course_image = wp_get_attachment_image_src( $image_id, 'large' );
					if ( $_course_image ) {
						$course_cover_photo = $_course_image[0];
					}
				}

				if ( ! empty( $course_cover_photo ) ) {
					?>
					<img src="<?php echo esc_url( $course_cover_photo ); ?>" alt="<?php echo esc_attr( get_the_title( get_the_ID() ) ); ?>" class="banner-img wp-post-image" />
				<?php } ?>

				<div class="rlla-course-banner-info container">

					<div class="flex flex-wrap">

						<div class="rlla-course-banner-inner">

							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

							<?php if ( has_excerpt() ) { ?>
								<div class="rlla-course-excerpt">
									<?php echo wp_kses_post( get_the_excerpt() ); ?>
								</div>
							<?php } ?>
							<div class="rlla-course-points">
								<a class="anchor-course-points" href="#lifter-course-content">
									<?php echo esc_html__( 'View Course Details', 'reign' ); ?>
									<i class="far fa-angle-down"></i>
								</a>
							</div>

							<div class="rlla-course-single-meta flex align-items-center">
								<?php
								$lifterlms_course_author = ( isset( $wbtm_reign_settings['lifterlms']['lifterlms_course_author'] ) ) ? $wbtm_reign_settings['lifterlms']['lifterlms_course_author'] : '';
								$lifterlms_course_date   = ( isset( $wbtm_reign_settings['lifterlms']['lifterlms_course_date'] ) ) ? $wbtm_reign_settings['lifterlms']['lifterlms_course_date'] : '';

								if ( ! $lifterlms_course_author ) :
									$author_link = get_author_posts_url( get_the_author_meta( 'ID' ) );
									?>

										<a href="<?php echo esc_url( $author_link . '?post_type=course' ); ?>">
										<?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
											<span class="author-name"><?php the_author(); ?></span>
										</a>

									<?php
								endif;

								if ( ! $lifterlms_course_date ) :
									?>

									<span class="meta-saperator">&middot;</span>

									<?php
								endif;

								if ( ! $lifterlms_course_date ) :
									?>
									<span class="course-date"><?php echo get_the_date(); ?></span>
									<?php
								endif;
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
				<?php
			}
			?>

			<div class="wb-grid">
				<div id="lifter-course-content" class="rlla-llms-content-wrap">
					<?php if ( class_exists( 'Reign_LifterLMS_Addon' ) ) : ?>
						<div class="rlla-course-list-view">
							<div class="llms-loop-list"> 
								<?php
								$lifterlms_course_meta = ( isset( $wbtm_reign_settings['lifterlms']['lifterlms_course_meta'] ) ) ? $wbtm_reign_settings['lifterlms']['lifterlms_course_meta'] : '';

								if ( ! $lifterlms_course_meta ) :
									?>
									<div class="rlla-course-meta">
										<div class="rlla-course-author rlla-course-list-view-data" itemscope="" itemtype="http://schema.org/Person">
											<?php
											echo '<div class="rlla-llms-author">';
											echo wp_kses_post(
												rlla_llms_get_author(
													array(
														'avatar_size' => 50,
														'label' => __( 'Instructor', 'reign' ),
													)
												)
											);
											echo '</div>';
											?>
										</div>

										<?php
										$categories_string = '';
										$categories        = get_the_terms( $course_id, 'course_cat' );
										if ( ! empty( $categories ) && is_array( $categories ) ) {
											foreach ( $categories as $value ) {
												$categories_string .= '<a href="' . get_category_link( $value->term_id ) . '">' . $value->name . '</a>,';
												break;
											}
										}
										if ( ! empty( $categories_string ) ) {
											$categories_string = trim( $categories_string, ',' );
											?>
											<div class="rlla-course-students">
												<label><?php esc_html_e( 'Categories', 'reign' ); ?></label>
												<div class="rlla-value">
													<?php echo wp_kses_post( $categories_string ); ?>
												</div>
											</div>
											<?php
										}
										?>

										<?php
										$_llms_reviews_enabled = get_post_meta( $post->ID, '_llms_reviews_enabled', true );
										if ( $_llms_reviews_enabled ) {
											?>
											<div class="rlla-course-review rlla-course-list-view-data">
												<label><?php esc_html_e( 'Review', 'reign' ); ?></label>
												<div class="rlla-value">
													<div class="rlla-review-stars-rated">
														<ul class="rlla-review-stars">
															<li><span class="far fa-star"></span></li>
															<li><span class="far fa-star"></span></li>
															<li><span class="far fa-star"></span></li>
															<li><span class="far fa-star"></span></li>
															<li><span class="far fa-star"></span></li>
														</ul>
														<ul class="rlla-review-stars rlla-filled" style="width: <?php echo esc_attr( $reviews_percentage ); ?>%">
															<li><span class="fa fa-star"></span></li>
															<li><span class="fa fa-star"></span></li>
															<li><span class="fa fa-star"></span></li>
															<li><span class="fa fa-star"></span></li>
															<li><span class="fa fa-star"></span></li>
														</ul>
													</div>
													<span>(<?php echo esc_html( $total_reviews ) . ' ' . esc_html_e( 'reviews', 'reign' ); ?>)</span>
												</div>
											</div>
											<?php
										}
										?>

										<?php if ( ! $is_enrolled ) { ?>
											<div class="rlla-course-price">
												<?php
													echo wp_kses_post( get_rlla_llms_course_price( $course_id ) );
												?>
											</div>
										<?php } ?>	

										<?php if ( ! $is_enrolled ) : ?>
											<div class="rlla-course-buy-now">
												<div class="rtm-ld-common-buy-now">
													<?php
													$direct_checkout_class = '';
													$enroll_text           = esc_html__( 'Take this course', 'reign' );
													$display_btn           = false;

													if ( ! isset( $course_id ) ) {
														global $post;
														$course_id = $post->ID;
													}
													$wc_product_ids = array();
													$product        = class_exists( 'LLMS_Product' ) ? new LLMS_Product( $course_id ) : null;
													$access_plans   = $product ? $product->get_access_plans() : array();
													foreach ( $access_plans as $i => $plan ) {
														if ( class_exists( 'LLMS_Integration_WooCommerce' ) ) {
															if ( 'yes' === get_option( 'lifterlms_woocommerce_enabled', 'no' ) ) {
																if ( llms_wc_plan_has_wc_product( $plan ) ) {
																	array_push( $wc_product_ids, $plan->get( 'wc_pid' ) );
																}
															}
														}
														$enroll_text = $plan->get_enroll_text();
														$display_btn = true;
													}
													$price = '';

													if ( is_array( $wc_product_ids ) && ! empty( $wc_product_ids ) && ( 1 === count( $wc_product_ids ) ) ) {
														$course_sales_page_url = wc_get_checkout_url();
														$course_sales_page_url = add_query_arg(
															array(
																'add-to-cart' => $wc_product_ids[0],
																'rlld-direct-buy' => 'yes',
															),
															$course_sales_page_url
														);
														$direct_checkout_class = 'rlla-wc-direct-checkout';
														$display_btn           = true;

													}
													if ( $display_btn ) {
														?>
														<a class="btn-join <?php echo esc_attr( $direct_checkout_class ); ?>" href="<?php echo esc_url( $course_sales_page_url ); ?>" id="btn-join"><?php echo esc_html( $enroll_text ); ?></a>
													<?php } ?>
												</div>
											</div>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<?php
						$lifterlms_course_thumbnail = ( isset( $wbtm_reign_settings['lifterlms']['lifterlms_course_thumbnail'] ) ) ? $wbtm_reign_settings['lifterlms']['lifterlms_course_thumbnail'] : '';

						if ( ! $lifterlms_course_thumbnail ) :
							?>
							<div class="rlla-course-image">
								<?php
								if ( $is_enrolled ) {
									$student = class_exists( 'LLMS_Student' ) ? new LLMS_Student() : null;
									if ( $student && $student->is_complete( $post->ID, $post->post_type ) ) {
										$flash_text = __( 'Completed', 'reign' );
									} else {
										$flash_text = __( 'Enrolled', 'reign' );
									}
									?>
									<div class="rlla-is-enrolled">
										<p><?php echo esc_html( $flash_text ); ?></p>
									</div>
									<?php
								}

								if ( has_post_thumbnail() ) {
									the_post_thumbnail();
								} else {
									echo wp_kses_post( get_rlla_llms_default_course_img_html() );
								}
								?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php
					if ( class_exists( 'Reign_LifterLMS_Addon' ) ) {
						do_action( 'rlla_llms_single_course_content' );
					} else {
						the_content();
					}
					?>

				</div>
				<?php
				// Single course sidebar.
				llms_get_template( 'course/template-single-course-sidebar.php' );
				?>
			</div>
		</article>
		<?php
		do_action( 'reign_single_post_comment_section' );

	endwhile; // End of the loop.
	?>
</div>

<?php
if ( class_exists( 'Reign_LifterLMS_Addon' ) ) {
	do_action( 'reign_after_content_section' );
}

get_footer();

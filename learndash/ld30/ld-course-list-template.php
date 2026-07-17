<?php
/**
 * Template part for displaying course list shortcode
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$col   = empty( $shortcode_atts['col'] ) ? ( defined( 'LEARNDASH_COURSE_GRID_COLUMNS' ) ? LEARNDASH_COURSE_GRID_COLUMNS : 3 ) : intval( $shortcode_atts['col'] );
$col   = $col > 6 ? 6 : $col;
$smcol = 1 === $col ? 1 : $col / 2;
$col   = 12 / $col;
$smcol = intval( ceil( 12 / $smcol ) );
$col   = is_float( $col ) ? number_format( $col, 1 ) : $col;
$col   = str_replace( '.', '-', $col );

global $post;
$current_post_id = $post->ID;

$course_id = $current_post_id;
$user_id   = get_current_user_id();

$enable_video = get_post_meta( $post->ID, '_learndash_course_grid_enable_video_preview', true );
$embed_code   = get_post_meta( $post->ID, '_learndash_course_grid_video_embed_code', true );
$button_text  = get_post_meta( $post->ID, '_learndash_course_grid_custom_button_text', true );

// Retrive oembed HTML if URL provided
if ( preg_match( '/^http/', $embed_code ) ) {
	$embed_code = wp_oembed_get(
		$embed_code,
		array(
			'height' => 600,
			'width'  => 400,
		)
	);
}

if ( isset( $shortcode_atts['course_id'] ) ) {
	$button_link = learndash_get_step_permalink( get_the_ID(), $shortcode_atts['course_id'] );
} else {
	$button_link = get_permalink();
}

$button_link = apply_filters( 'learndash_course_grid_custom_button_link', $button_link, $current_post_id );

$button_text = isset( $button_text ) && ! empty( $button_text ) ? $button_text : __( 'See more...', 'reign' );
$button_text = apply_filters( 'learndash_course_grid_custom_button_text', $button_text, $current_post_id );

$options          = get_option( 'sfwd_cpt_options' );
$currency_setting = class_exists( 'LearnDash_Settings_Section' ) ? LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_PayPal', 'paypal_currency' ) : null;
$currency         = '';

$currency = null;
if ( ! is_null( $options ) ) {
	if ( isset( $options['modules'] ) && isset( $options['modules']['sfwd-courses_options'] ) && isset( $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'] ) ) {
		$currency = $options['modules']['sfwd-courses_options']['sfwd-courses_paypal_currency'];
	}
}

if ( is_null( $currency ) ) {
	$currency = ( version_compare( LEARNDASH_VERSION, '4.1.0', '<' ) ) ? learndash_30_get_currency_symbol() : learndash_get_currency_symbol();
}

/**
 * Currency symbol filter hook
 *
 * @param string $currency Currency symbol
 * @param int    $course_id
 */
$currency       = apply_filters( 'learndash_course_grid_currency', $currency, $course_id );
$course_pricing = learndash_get_course_price( $course_id );

$course_options    = get_post_meta( $current_post_id, '_sfwd-courses', true );
$price             = $course_pricing && isset( $course_pricing['price'] ) && '' !== $course_pricing['price'] ? wp_kses_post( $course_pricing['price'] ) : esc_html__( 'Free', 'reign' );
$price_type        = $course_options && isset( $course_options['sfwd-courses_course_price_type'] ) ? $course_options['sfwd-courses_course_price_type'] : '';
$short_description = ( isset( $course_options['sfwd-courses_course_short_description'] ) ) ? $course_options['sfwd-courses_course_short_description'] : '';

/**
 * Filter: individual grid class
 *
 * @param int   $course_id Course ID
 * @param array $course_options Course options
 * @var string
 */
$grid_class = apply_filters( 'learndash_course_grid_class', '', $course_id, $course_options );

$has_access   = sfwd_lms_has_access( $course_id, $user_id );
$is_completed = learndash_course_completed( $user_id, $course_id );

$price_text = '';

if ( is_numeric( $price ) && ! empty( $price ) ) {
	$price_format = apply_filters( 'learndash_course_grid_price_text_format', '{currency}{price}' );

	$price_text = str_replace( array( '{currency}', '{price}' ), array( $currency, $price ), $price_format );
} elseif ( is_string( $price ) && ! empty( $price ) ) {
	$price_text = $price;
} elseif ( empty( $price ) ) {
	$price_text = __( 'Free', 'reign' );
}

$class           = 'ld_course_grid_price';
$ribbon_text     = get_post_meta( $post->ID, '_learndash_course_grid_custom_ribbon_text', true );
$custom_btn_text = get_post_meta( $post->ID, '_learndash_course_grid_custom_button_text', true );
$ribbon_text     = isset( $ribbon_text ) && ! empty( $ribbon_text ) ? $ribbon_text : '';

if ( $has_access && ! $is_completed && 'open' !== $price_type && empty( $ribbon_text ) ) {
	$class      .= ' ribbon-enrolled';
	$ribbon_text = __( 'Enrolled', 'reign' );
} elseif ( $has_access && $is_completed && 'open' !== $price_type && empty( $ribbon_text ) ) {
	$class      .= '';
	$ribbon_text = __( 'Completed', 'reign' );
} elseif ( 'open' === $price_type && empty( $ribbon_text ) ) {
	if ( is_user_logged_in() && ! $is_completed ) {
		$class      .= ' ribbon-enrolled';
		$ribbon_text = __( 'Enrolled', 'reign' );
	} elseif ( is_user_logged_in() && $is_completed ) {
		$class      .= '';
		$ribbon_text = __( 'Completed', 'reign' );
	} else {
		$class      .= ' ribbon-enrolled';
		$ribbon_text = $price_text;
	}
} elseif ( 'closed' === $price_type && empty( $price ) ) {
	$class .= ' ribbon-enrolled';

	if ( is_numeric( $price ) ) {
		$ribbon_text = $price_text;
	} else {
		$ribbon_text = '';
	}
} elseif ( empty( $ribbon_text ) ) {
	$class      .= ! empty( $course_options['sfwd-courses_course_price'] ) ? ' price_' . $currency : ' free';
	$ribbon_text = $price_text;
} else {
	$class .= ' custom';
}

/**
 * Filter: individual course ribbon text
 *
 * @param string $ribbon_text Returned ribbon text
 * @param int    $course_id   Course ID
 * @param string $price_type  Course price type
 */
$ribbon_text = apply_filters( 'learndash_course_grid_ribbon_text', $ribbon_text, $course_id, $price_type );

if ( '' == $ribbon_text ) {
	$class = '';
}

/**
 * Filter: individual course ribbon class names
 *
 * @param string $class          Returned class names
 * @param int    $course_id      Course ID
 * @param array  $course_options Course's options
 * @var string
 */
$class = apply_filters( 'learndash_course_grid_ribbon_class', $class, $course_id, $course_options );

$thumb_size = isset( $shortcode_atts['thumb_size'] ) && ! empty( $shortcode_atts['thumb_size'] ) ? $shortcode_atts['thumb_size'] : 'course-thumb';
$course_id  = learndash_get_course_id( get_the_ID() );

$thumb_act_inact = ( 'true' === $shortcode_atts['show_thumbnail'] ) ? 'thumb_active' : 'thumb_inactive';

?>
<div class="ld_course_grid col-sm-<?php echo esc_attr( $smcol ); ?> col-md-<?php echo esc_attr( $col ); ?> <?php echo esc_attr( $grid_class ); ?>">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'thumbnail course lm-course-item-wrapper lm-course-item-layout2' ); ?>>
		<div class="lm-course-item <?php echo esc_attr( $thumb_act_inact ); ?>">
			<?php if ( 'true' === $shortcode_atts['show_thumbnail'] ) : ?>

				<?php if ( 'sfwd-courses' === $post->post_type ) : ?>
					<div class="<?php echo esc_attr( $class ); ?>">
						<?php echo esc_attr( $ribbon_text ); ?>
					</div>
				<?php endif; ?>

				<?php if ( 1 == $enable_video && ! empty( $embed_code ) ) : ?>
					<div class="ld_course_grid_video_embed">
						<?php
						echo $embed_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?>
					</div>
				<?php elseif ( has_post_thumbnail() ) : ?>
					<div class="lm-course-thumbnail rg-lm-course-thumbnail">
						<a href="<?php the_permalink(); ?>" rel="bookmark">
							<?php the_post_thumbnail( $thumb_size ); ?>
						</a>
						<!-- Read More Link Added :: Start -->
						<a class="button lm-course-readmore-button lm-course-grid-view-data" href="<?php echo esc_url( learndash_get_step_permalink( get_the_ID(), $course_id ) ); ?>" title="<?php echo esc_attr( the_title_attribute( array( 'echo' => 0 ) ) ); ?>" rel="bookmark">
							<?php echo ( '' !== $custom_btn_text ) ? esc_html( $custom_btn_text ) : esc_html__( 'Read More', 'reign' ); ?>
						</a>
						<!-- Read More Link Added :: End -->
					</div>
				<?php else : ?>
					<div class="lm-course-thumbnail rg-lm-course-thumbnail">
						<a href="<?php the_permalink(); ?>" rel="bookmark">
							<?php echo wp_kses_post( get_reign_ld_default_course_img_html() ); ?>
							<!-- Read More Link Added :: Start -->
							<a class="button lm-course-readmore-button lm-course-grid-view-data" href="<?php echo esc_url( learndash_get_step_permalink( get_the_ID(), $course_id ) ); ?>" title="<?php echo the_title_attribute( 'echo=0' ); ?>" rel="bookmark">
								<?php echo ( '' !== $custom_btn_text ) ? esc_html( $custom_btn_text ) : esc_html__( 'Read More', 'reign' ); ?>
							</a>
							<!-- Read More Link Added :: End -->
						</a>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="lm-course-content rg-lm-course-content">
				<?php if ( 'true' === $shortcode_atts['show_content'] ) : ?>
					<div class="caption">
						<?php the_title( '<h2 class="lm-course-title"><a href="' . learndash_get_step_permalink( get_the_ID(), $course_id ) . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>
						<p class="entry-content">
							<?php
							if ( ! empty( $short_description ) ) {
								echo wp_kses_post( do_shortcode( $short_description ) );
							} else {
								echo esc_html( get_the_excerpt( get_the_ID() ) );
							}
							?>
						</p>
						<?php if ( isset( $shortcode_atts['progress_bar'] ) && 'true' === $shortcode_atts['progress_bar'] ) : ?>
							<p><?php echo do_shortcode( '[learndash_course_progress course_id="' . get_the_ID() . '" user_id="' . get_current_user_id() . '"]' ); ?></p>
						<?php endif; ?>
					</div><!-- .entry-header -->
				<?php endif; ?>
			</div>
		</div>
	</article><!-- #post-## -->
</div>
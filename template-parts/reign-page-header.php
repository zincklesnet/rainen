<?php
/**
 * Reign Page Header
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/* setting up the title */
$reign_header_title = single_post_title( '', false );

if ( empty( $reign_header_title ) ) {
	$reign_header_title = get_the_title();
}

$kirki_post_types_support_class = new Reign_Customizer_Post_Types_Fields();
$supported_post_types           = $kirki_post_types_support_class->get_post_types_to_support();

if ( is_tag() || is_tax() ) {
	$reign_header_title = single_term_title( '', false );
} elseif ( is_post_type_archive() ) {
	$reign_post_type = get_query_var( 'post_type' );
	if ( is_array( $reign_post_type ) ) {
		$reign_post_type = reset( $reign_post_type );
	}
	$post_type_obj = get_post_type_object( $reign_post_type );
	if ( isset( $post_type_obj->labels->name ) ) {
		$reign_header_title = $post_type_obj->labels->name;
	}
} elseif ( is_category() ) {
	$reign_header_title = single_cat_title( '', false );
} elseif ( is_author() ) {
	$author_id = get_query_var( 'author' );
	if ( $author_id ) {
		$author = get_user_by( 'id', $author_id );
		if ( ! empty( get_user_meta( $author_id, 'first_name', true ) ) ) {
			$author_name = get_user_meta( $author_id, 'first_name', true ) . ' ' . get_user_meta( $author_id, 'last_name', true );
		} else {
			$author_info = get_userdata( $author_id );
			$author_name = $author_info->data->user_login;
		}
		$reign_header_title = $author_name;
	}
}

if ( ! $reign_header_title && is_single() ) {
	$reign_header_title = get_the_title( get_queried_object_id() );
}

if ( ! is_front_page() && is_home() ) {
	$reign_header_title = single_post_title( '', false );
}
if ( is_search() ) {
	$reign_header_title = __( 'Search results for ', 'reign' ) . esc_html( get_search_query() );
}

$reign_header_title = apply_filters( 'reign_page_header_section_title', $reign_header_title );

$reign_post_type = get_post_type();
if ( is_singular() ) {
	$reign_post_type = get_post_type();
	if ( ! in_array( $reign_post_type, array_column( $supported_post_types, 'slug' ) ) && ! is_search() ) {
		$banner_header = get_theme_mod( 'reign_cpt_default_single_enable_header_image', true );
	} else {
		$banner_header = get_theme_mod( 'reign_' . $reign_post_type . '_single_enable_header_image', true );
	}
	// $banner_header = get_theme_mod( 'reign_' . $reign_post_type . '_single_enable_header_image', true );
} elseif ( is_search() ) {
	$banner_header = get_theme_mod( 'reign_search_enable_header_image', true );
} else {
	$banner_header = get_theme_mod( 'reign_' . $reign_post_type . '_archive_enable_header_image', true );
}

$breadcrumb = get_theme_mod( 'reign_site_enable_breadcrumb', true );
if ( ! reign_is_truthy( $banner_header ) ) :
	?>
	<div class="lm-site-header-section without-img-header">
		<div class="lm-header-banner">
			<div class="rg-sub-header-inner-section">
				<div class="container">
					<?php
					if ( $reign_header_title ) {
						// $reign_header_title may carry search query / post title / filter
						// output. wp_kses_post strips scripts and event
						// handlers but preserves safe inline markup.
						echo '<h1 class="lm-header-title">' . wp_kses_post( $reign_header_title ) . '</h1>';
					}
					if ( reign_is_truthy( $breadcrumb ) ) {
						?>
						<div class="lm-breadcrumbs-wrapper">
							<div class="container"><?php reign_breadcrumbs(); ?></div>
						</div>
						<?php
					}
					?>
				</div>
				<?php do_action( 'reign_page_header_extra' ); ?>
			</div>
		</div>
	</div>
	<?php
else :
	$reign_post_type = get_post_type();

	if ( is_singular() ) {

		$wbcom_metabox_data   = get_post_meta( get_the_ID(), 'reign_wbcom_metabox_data', true );
		$_subheader_overwrite = get_post_meta( get_the_ID(), '_subheader_overwrite', true );

		$display_page_header_image = isset( $wbcom_metabox_data['layout']['display_page_header_image'] ) ? $wbcom_metabox_data['layout']['display_page_header_image'] : '';

		if ( ! in_array( $reign_post_type, array_column( $supported_post_types, 'slug' ) ) ) {
			$header_banner_image_url = get_theme_mod( 'reign_cpt_default_sub_header_image', '' );
		} else {
			$header_banner_image_url = get_theme_mod( 'reign_' . $reign_post_type . '_single_header_image', '' );
		}

		if ( empty( $header_banner_image_url ) ) {
			$header_banner_image_url = reign_get_default_page_header_image();
		}

		if ( 'post' === $reign_post_type ) {
			$switch_header_image = get_theme_mod( 'reign_single_post_switch_header_image', false );
			if ( reign_is_truthy( $switch_header_image ) && has_post_thumbnail() ) {
				$header_banner_image_url = get_the_post_thumbnail_url();
			}
		} elseif ( in_array( $reign_post_type, array_column( $supported_post_types, 'slug' ) ) ) {
			$switch_header_image = get_theme_mod( 'reign_single_' . $reign_post_type . '_switch_header_image', false );
			if ( reign_is_truthy( $switch_header_image ) && has_post_thumbnail() ) {
				$header_banner_image_url = get_the_post_thumbnail_url();
			}
		} elseif ( ! in_array( $reign_post_type, array_column( $supported_post_types, 'slug' ) ) ) {
			$switch_header_image = get_theme_mod( 'reign_single_' . $reign_post_type . '_switch_header_image', false );
			if ( reign_is_truthy( $switch_header_image ) && has_post_thumbnail() ) {
				$header_banner_image_url = get_the_post_thumbnail_url();
			}
		}

		if ( 'page' === $reign_post_type && has_post_thumbnail() && 'on' === $display_page_header_image ) {
			$header_banner_image_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		}


		$header_banner_image_url = apply_filters( 'reign_' . $reign_post_type . '_single_header_image', $header_banner_image_url, $reign_post_type );

		if ( 'yes' === $_subheader_overwrite ) {
			$breadcrumb = ( isset( $wbcom_metabox_data['subheader']['sub_header_breadcrumbs'] ) && '' !== $wbcom_metabox_data['subheader']['sub_header_breadcrumbs'] ) ? true : false;
			if ( 'on' !== $display_page_header_image ) {
				$header_banner_image_url = ( isset( $wbcom_metabox_data['subheader']['sub_header_banner_image'] ) && '' !== $wbcom_metabox_data['subheader']['sub_header_banner_image'] ) ? $wbcom_metabox_data['subheader']['sub_header_banner_image'] : '';
			}

			// Defensive sanitization of per-post sub-header overrides before
			// emitting into inline CSS. Each value is allowed to be a hex,
			// rgb(), rgba(), hsl(), or named color, but never the structural
			// CSS chars `{`, `}`, `;`, `<`, `>` or a newline — those would
			// let a saved value break out of its declaration and inject rules
			// or HTML into the page.
			$subheader_data   = isset( $wbcom_metabox_data['subheader'] ) && is_array( $wbcom_metabox_data['subheader'] ) ? $wbcom_metabox_data['subheader'] : array();
			$sh_strip_css     = static function ( $value ) {
				return preg_replace( '/[<>{};\r\n]/', '', (string) $value );
			};
			$sh_bg_color      = $sh_strip_css( $subheader_data['sub_header_bg_color'] ?? '' );
			$sh_height        = absint( $subheader_data['sub_header_height'] ?? 0 );
			$sh_overlay_color = $sh_strip_css( $subheader_data['sub_header_overlay_color'] ?? '' );
			$sh_text_color    = $sh_strip_css( $subheader_data['sub_header_text_color'] ?? '' );
			$sh_link_color    = $sh_strip_css( $subheader_data['sub_header_link_color'] ?? '' );
			?>
			<style>
			.lm-site-header-section .lm-header-banner{
				background-color : <?php echo esc_attr( $sh_bg_color ); ?>;
				height: <?php echo absint( $sh_height ); ?>px;
			}
			.lm-header-banner:after{
				background : <?php echo esc_attr( $sh_overlay_color ); ?>;
			}
			.lm-site-header-section .lm-header-banner h1.lm-header-title, .lm-breadcrumbs-wrapper #breadcrumbs li i, .lm-breadcrumbs-wrapper #breadcrumbs span, .lm-breadcrumbs-wrapper #breadcrumbs li strong, .lm-site-header-section .lm-header-banner{
				color : <?php echo esc_attr( $sh_text_color ); ?>;
			}
			.lm-breadcrumbs-wrapper #breadcrumbs li a, .lm-breadcrumbs-wrapper #breadcrumbs span a{
				color : <?php echo esc_attr( $sh_link_color ); ?>;
			}
			</style>
			<?php
		}
	} else {

		if ( ! in_array( $reign_post_type, array_column( $supported_post_types, 'slug' ) ) && ! is_search() ) {
			$header_banner_image_url = get_theme_mod( 'reign_cpt_default_sub_header_image', '' );
		} elseif ( is_search() ) {
			$header_banner_image_url = get_theme_mod( 'reign_search_header_image', '' );
		} else {
			$header_banner_image_url = get_theme_mod( 'reign_' . $reign_post_type . '_archive_header_image', '' );
		}
		if ( empty( $header_banner_image_url ) ) {
			$header_banner_image_url = reign_get_default_page_header_image();
		}
		$header_banner_image_url = apply_filters( 'reign_' . $reign_post_type . '_archive_header_image', $header_banner_image_url, $reign_post_type );
	}

	// Preload the banner background image as a high-priority resource
	// hint. The image is the LCP candidate on every non-home page, but
	// because it's served via inline `background-image:url(...)` the
	// browser can't discover it until the CSS for `.lm-header-banner-overlay`
	// is parsed. Without the preload, the image fetch starts 1-2 RTTs late
	// on cold mobile/4G visits (300-800ms LCP penalty). With it, the
	// browser starts the fetch the instant it sees the preload link in
	// the head.
	if ( ! empty( $header_banner_image_url ) ) {
		printf(
			'<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
			esc_url( $header_banner_image_url )
		);
	}
	?>
	<div class="lm-site-header-section">
		<div class="lm-header-banner">
			<div class="lm-header-banner-overlay" style="background-image:url(<?php echo esc_url( $header_banner_image_url ); ?>);">
			</div>
			<div class="rg-sub-header-inner-section">
				<div class="lm-header-title-wrapper container">
					<?php
					if ( $reign_header_title ) {
						echo '<h1 class="lm-header-title">' . wp_kses_post( $reign_header_title ) . '</h1>';
					}
					?>
				</div>
				<?php
				if ( reign_is_truthy( $breadcrumb ) ) {
					?>
					<div class="lm-breadcrumbs-wrapper">
						<div class="container"><?php reign_breadcrumbs(); ?></div>
					</div>
					<?php
				}
				?>
				<?php do_action( 'reign_page_header_extra' ); ?>
			</div>
		</div>
	</div>
	<?php
endif;

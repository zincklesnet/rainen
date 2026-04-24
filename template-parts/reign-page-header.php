<?php
/**
 * Reign Page Header
 *
 * @package Reign
 */

/* setting up the title */
$title = single_post_title( '', false );

if ( empty( $title ) ) {
	$title = get_the_title();
}

$kirki_post_types_support_class = new Reign_Kirki_Post_Types_Support();
$supported_post_types           = $kirki_post_types_support_class->get_post_types_to_support();

if ( is_tag() || is_tax() ) {
	$title = single_term_title( '', false );
} elseif ( is_post_type_archive() ) {
	$post_type = get_query_var( 'post_type' );
	if ( is_array( $post_type ) ) {
		$post_type = reset( $post_type );
	}
	$post_type_obj = get_post_type_object( $post_type );
	if ( isset( $post_type_obj->labels->name ) ) {
		$title = $post_type_obj->labels->name;
	}
} elseif ( is_category() ) {
	$title = single_cat_title( '', false );
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
		$title = $author_name;
	}
}

if ( ! $title && is_single() ) {
	$title = get_the_title( get_queried_object_id() );
}

if ( ! is_front_page() && is_home() ) {
	$title = single_post_title( '', false );
}
if ( is_search() ) {
	$title = __( 'Search results for ', 'reign' ) . get_search_query();
}

$title = apply_filters( 'reign_page_header_section_title', $title );

$post_type = get_post_type();
if ( is_singular() ) {
	$post_type = get_post_type();
	if ( ! in_array( $post_type, array_column( $supported_post_types, 'slug' ) ) && ! is_search() ) {
		$banner_header = get_theme_mod( 'reign_cpt_default_single_enable_header_image', true );
	} else {
		$banner_header = get_theme_mod( 'reign_' . $post_type . '_single_enable_header_image', true );
	}
	// $banner_header = get_theme_mod( 'reign_' . $post_type . '_single_enable_header_image', true );
} elseif ( is_search() ) {
	$banner_header = get_theme_mod( 'reign_search_enable_header_image', true );
} else {
	$banner_header = get_theme_mod( 'reign_' . $post_type . '_archive_enable_header_image', true );
}

$breadcrumb = get_theme_mod( 'reign_site_enable_breadcrumb', true );
if ( ! $banner_header ) :
	?>
	<div class="lm-site-header-section without-img-header">
		<div class="lm-header-banner">
			<div class="rg-sub-header-inner-section">
				<div class="container">
					<?php
					if ( $title ) {
						echo '<h1 class="lm-header-title">' . $title . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					if ( $breadcrumb ) {
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
	$post_type = get_post_type();

	if ( is_singular() ) {

		$wbcom_metabox_data   = get_post_meta( get_the_ID(), 'reign_wbcom_metabox_data', true );
		$_subheader_overwrite = get_post_meta( get_the_ID(), '_subheader_overwrite', true );

		$display_page_header_image = isset( $wbcom_metabox_data['layout']['display_page_header_image'] ) ? $wbcom_metabox_data['layout']['display_page_header_image'] : '';

		if ( ! in_array( $post_type, array_column( $supported_post_types, 'slug' ) ) ) {
			$header_banner_image_url = get_theme_mod( 'reign_cpt_default_sub_header_image', '' );
		} else {
			$header_banner_image_url = get_theme_mod( 'reign_' . $post_type . '_single_header_image', '' );
		}

		if ( empty( $header_banner_image_url ) ) {
			$header_banner_image_url = reign_get_default_page_header_image();
		}

		if ( 'post' === $post_type ) {
			$switch_header_image = get_theme_mod( 'reign_single_post_switch_header_image', false );
			if ( $switch_header_image && has_post_thumbnail() ) {
				$header_banner_image_url = get_the_post_thumbnail_url();
			}
		} elseif ( in_array( $post_type, array_column( $supported_post_types, 'slug' ) ) ) {
			$switch_header_image = get_theme_mod( 'reign_single_' . $post_type . '_switch_header_image', false );
			if ( $switch_header_image && has_post_thumbnail() ) {
				$header_banner_image_url = get_the_post_thumbnail_url();
			}
		} elseif ( ! in_array( $post_type, array_column( $supported_post_types, 'slug' ) ) ) {
			$switch_header_image = get_theme_mod( 'reign_single_' . $post_type . '_switch_header_image', false );
			if ( $switch_header_image && has_post_thumbnail() ) {
				$header_banner_image_url = get_the_post_thumbnail_url();
			}
		}

		if ( $post_type == 'page' && has_post_thumbnail() && $display_page_header_image == 'on' ) {
			$header_banner_image_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		}


		$header_banner_image_url = apply_filters( 'reign_' . $post_type . '_single_header_image', $header_banner_image_url, $post_type );

		if ( $_subheader_overwrite == 'yes' ) {
			$breadcrumb = ( isset( $wbcom_metabox_data['subheader']['sub_header_breadcrumbs'] ) && $wbcom_metabox_data['subheader']['sub_header_breadcrumbs'] != '' ) ? true : false;
			if ( $display_page_header_image != 'on' ) {
				$header_banner_image_url = ( isset( $wbcom_metabox_data['subheader']['sub_header_banner_image'] ) && $wbcom_metabox_data['subheader']['sub_header_banner_image'] != '' ) ? $wbcom_metabox_data['subheader']['sub_header_banner_image'] : '';
			}
			?>
			<style>
			.lm-site-header-section .lm-header-banner{
				background-color : <?php echo $wbcom_metabox_data['subheader']['sub_header_bg_color']; ?>;
				height: <?php echo $wbcom_metabox_data['subheader']['sub_header_height']; ?>px;
			}
			.lm-header-banner:after{
				background : <?php echo $wbcom_metabox_data['subheader']['sub_header_overlay_color']; ?>;
			}
			.lm-site-header-section .lm-header-banner h1.lm-header-title, .lm-breadcrumbs-wrapper #breadcrumbs li i, .lm-breadcrumbs-wrapper #breadcrumbs span, .lm-breadcrumbs-wrapper #breadcrumbs li strong, .lm-site-header-section .lm-header-banner{
				color : <?php echo $wbcom_metabox_data['subheader']['sub_header_text_color']; ?>;
			}
			.lm-breadcrumbs-wrapper #breadcrumbs li a, .lm-breadcrumbs-wrapper #breadcrumbs span a{
				color : <?php echo $wbcom_metabox_data['subheader']['sub_header_link_color']; ?>;
			}
			</style>
			<?php
		}
	} else {

		if ( ! in_array( $post_type, array_column( $supported_post_types, 'slug' ) ) && ! is_search() ) {
			$header_banner_image_url = get_theme_mod( 'reign_cpt_default_sub_header_image', '' );
		} elseif ( is_search() ) {
			$header_banner_image_url = get_theme_mod( 'reign_search_header_image', '' );
		} else {
			$header_banner_image_url = get_theme_mod( 'reign_' . $post_type . '_archive_header_image', '' );
		}
		if ( empty( $header_banner_image_url ) ) {
			$header_banner_image_url = reign_get_default_page_header_image();
		}
		$header_banner_image_url = apply_filters( 'reign_' . $post_type . '_archive_header_image', $header_banner_image_url, $post_type );
	}
	?>
	<div class="lm-site-header-section">
		<div class="lm-header-banner">
			<div class="lm-header-banner-overlay" style="background-image:url(<?php echo $header_banner_image_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>);">
			</div>
			<div class="rg-sub-header-inner-section">
				<div class="lm-header-title-wrapper container">
					<?php
					if ( $title ) {
						echo '<h1 class="lm-header-title">' . $title . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<?php
				if ( $breadcrumb ) {
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

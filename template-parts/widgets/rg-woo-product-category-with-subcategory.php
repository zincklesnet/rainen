<?php
/**
 * WooCommerce Product Category with Subcategory Widget
 *
 * @package Reign
 */

$cat_args = apply_filters(
	'widget_rg_woo_product_category_with_subcategory_args',
	array(
		'orderby'    => 'name',
		'hide_empty' => true,
		'parent'     => 0,
	)
);

if ( ! empty( $atts['count'] ) ) {
	$cat_args['number'] = $atts['count'];
}

$selected_categories = $atts['selected_categories'];
if ( ! empty( $selected_categories ) ) {
	$selected_categories = trim( $selected_categories );
	$selected_categories = explode( ',', $selected_categories );
	if ( is_array( $selected_categories ) ) {
		$cat_args['include'] = $selected_categories;
	}
}

$categories = get_terms( 'product_cat', $cat_args );

$ul_wrapper_class   = ( $atts['enable_slider'] ) ? 'rg-woo-category-slider-wrap' : '';
$li_wb_grid_classes = 'wb-grid-cell sm-wb-grid-1-2 md-wb-grid-1-' . $atts['per_row'];
$data_slick         = '{"slidesToShow": ' . $atts['per_row'] . ', "slidesToScroll": 1}';

// Error handling for categories fetching.
if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
	echo '<ul class="wb-grid woocommerce rg-woo-category-wrap rg-woo-category-subcategory-wrap ' . esc_attr( $ul_wrapper_class ) . '" data-slick=\'{"slidesToShow": ' . esc_attr( $atts['per_row'] ) . ', "slidesToScroll": 1}\'>';

	foreach ( $categories as $category ) {

		if ( 'uncategorized' === $category->slug ) {
			continue;
		}

		// Pre-fetching subcategories for better performance.
		$subcat_args = array(
			'orderby'    => 'name',
			'hide_empty' => true,
			'parent'     => $category->term_id,
		);

		if ( ! empty( $atts['subcat_count'] ) ) {
			$subcat_args['number'] = $atts['subcat_count'];
		}

		// Fetch subcategories.
		$subcats = get_terms( 'product_cat', $subcat_args );

		if ( is_wp_error( $subcats ) || empty( $subcats ) ) {
			continue;
		}

		$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
		$cat_image    = wp_get_attachment_url( $thumbnail_id );

		if ( ! $cat_image ) {
			$cat_image = wc_placeholder_img_src(); // Use placeholder image if no thumbnail.
		}

		$style = "background-image: url('" . esc_url( $cat_image ) . "');";

		// Directly echoing category HTML without output buffering.
		echo '<li class="rg-woo-category-item-wrap ' . esc_attr( $atts['layout'] ) . ' ' . esc_attr( $li_wb_grid_classes ) . '">';
		echo '<a class="rg-woo-category-data" style="' . esc_attr( $style ) . '" href="' . esc_url( get_term_link( $category ) ) . '"></a>';
		echo '<div class="rg-woo-sub-category-data">';
		echo '<div class="rg-woo-category-name">';
		echo '<a href="' . esc_url( get_term_link( $category ) ) . '">';
		echo '<h3 class="category-name">' . esc_html( $category->name ) . '</h3>';
		echo '</a></div>';

		echo '<ul class="rg-woo-sub-category-data">';
		foreach ( $subcats as $subcat ) {
			echo '<li><a href="' . esc_url( get_term_link( $subcat ) ) . '">' . esc_html( $subcat->name ) . '</a></li>';
		}
		echo '</ul></div></li>';
	}

	echo '</ul>';
}

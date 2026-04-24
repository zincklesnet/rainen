<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$columns = intval( wc_get_loop_prop( 'columns', wc_get_default_products_per_row() ) );

$view       = function_exists( 'reign_get_default_catalog_view_mod' ) ? reign_get_default_catalog_view_mod() : 'grid-four';
$class      = ''; // Initialize $class.
$page_class = '';
if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
	$page_class = 'rg-products';
}
$class .= ! empty( $view ) ? $columns : '4';

?>
<ul class="products columns-<?php echo esc_attr( $class ); ?> <?php echo esc_attr( $page_class ); ?>">

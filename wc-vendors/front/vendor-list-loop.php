<?php
/**
 * Vendor List Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/front/vendors-list-loop.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Front
 * @since         2.4.2
 * @version       2.4.2 - More responsive
 *
 *  Template Variables available
 *  $shop_name : pv_shop_name
 *  $shop_description : pv_shop_description (completely sanitized)
 *  $shop_link : the vendor shop link
 *  $vendor_id  : current vendor id for customization
 *  $avatar : the vendor avatar
 *  $phone : the vendor store phone number
 *  $address : the vendor store address
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reign_wcvendors_vendor_rating  = get_theme_mod( 'reign_wcvendors_vendor_rating', true );
$reign_wcvendors_vendor_contact = get_theme_mod( 'reign_wcvendors_vendor_contact', true );
$reign_wcvendors_vendor_address = get_theme_mod( 'reign_wcvendors_vendor_address', true );
$reign_wcvendors_vendor_product = get_theme_mod( 'reign_wcvendors_vendor_product', true );
$store_icon                     = '';
$store_icon_src                 = wp_get_attachment_image_src(
	get_user_meta( $vendor_id, '_wcv_store_icon_id', true ),
	array( 150, 150 )
);

// see if the array is valid.
if ( is_array( $store_icon_src ) ) {
	$store_icon = '<img src="' . $store_icon_src[0] . '" alt="" class="vendor_store_image_single" />';
} else {
	$store_icon = '<img src="' . get_avatar_url( $vendor_id, array( 'size' => 150 ) ) . '" alt="" class="vendor_store_image_single" />';
}

// vendor banner image.
$store_bg = '';
if ( class_exists( 'WCVendors_Pro' ) ) {
	$store_icon_src = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_banner_id', true ), 'full' );
	if ( is_array( $store_icon_src ) ) {
		$store_bg = $store_icon_src[0];
	}
	if ( empty( $store_bg ) ) {
		$store_bg = get_option( 'wcvendors_default_store_banner_src', wcv_get_default_store_banner_src() );
	}
}

?>
<div class="vendor_list reign_wc_vendors_list">
	<div class="vendor-inner-list-wrap">
		<?php if ( ! empty( $store_bg ) ) : ?>
			<a class="reign-wc-vendor-image" href="<?php echo esc_url( $shop_link ); ?>">
				<span class="cover_logo" style="background-image: url('<?php echo esc_url( $store_bg ); ?>');"></span>
			</a>
		<?php else : ?>
			<a class="reign-wc-vendor-image" href="<?php echo esc_url( $shop_link ); ?>">
				<span class="cover_logo"></span>
			</a>
		<?php endif; ?>
		<div class="vendor_list_info">
			<div class="reign-wc-vendor-avatar-wrap">
				<div class="item-avatar">
					<a href="<?php echo esc_url( $shop_link ); ?>">					
						<?php echo wp_kses_post( $store_icon ); ?>
					</a>
				</div>
				<div class="reign-wc-vendor-name-wrap">
					<h3 class="vendor_list--shop-name">
						<a href="<?php echo esc_url( $shop_link ); ?>" class="wcv-grid-shop-name"><?php echo esc_html( $shop_name ); ?></a>
					</h3>
					<?php if ( class_exists( 'Reign_Wcvendors_Addon' ) && reign_is_truthy( $reign_wcvendors_vendor_rating ) ) : ?>
						<div class="store-rating">
							<?php echo esc_html( reign_wc_vendors_shop_rating( $vendor_id ) ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php do_action( 'reign_wc_vendor_after_rating' ); ?>
			<?php if ( reign_is_truthy( $reign_wcvendors_vendor_contact ) ) : ?>
				<small class="vendors_list--shop-phone"><span class="dashicons dashicons-smartphone"></span><span><?php echo esc_html( $phone ); ?></span></small><br/>
			<?php endif; ?>
			<?php if ( reign_is_truthy( $reign_wcvendors_vendor_address ) ) : ?>
				<small class="vendors_list--shop-address"><span class="dashicons dashicons-location"></span><span><?php echo esc_html( $address ); ?></span></small><br/>
			<?php endif; ?>
			<a class="button vendors_list--shop-link" href="<?php echo esc_url( $shop_link ); ?>"><?php esc_html_e( 'Visit Store', 'reign' ); ?></a>
			<?php if ( class_exists( 'Reign_Wcvendors_Addon' ) && reign_is_truthy( $reign_wcvendors_vendor_product ) ) : ?>
				<div class="vendor-products">
					<?php echo esc_html( reign_wc_vendors_vendor_products( $vendor_id ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
/**
 * The Template for displaying the modern store header
 *
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.7.9
 * @version    1.8.6 - Added new actions for plugins to hook into.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $post;
$store_icon             = '';
$store_icon_src         = wp_get_attachment_image_src(
	get_user_meta( $vendor_id, '_wcv_store_icon_id', true ),
	array( 150, 150 )
);
$store_banner_src       = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_banner_id', true ), 'full' );
$store_banner_image_url = get_option( 'wcvendors_default_store_banner_src' );

// see if the array is valid.
if ( is_array( $store_icon_src ) ) {
	$store_icon = '<img src="' . $store_icon_src[0] . '" alt="" class="store-icon" />';
} else {
	$store_icon = '<img src="' . get_avatar_url( $vendor_id, array( 'size' => 150 ) ) . '" alt="" class="store-icon" />';
}

if ( is_array( $store_banner_src ) ) {
	$store_banner_image_url = $store_banner_src[0];
}

// Verified vendor.
$verified_vendor       = 'yes' === get_user_meta( $vendor_id, '_wcv_verified_vendor', true );
$verified_vendor_label = get_option( 'wcvendors_verified_vendor_label', __( 'Verified Vendor', 'reign' ) );

// Trusted vendor.
$trusted_vendor       = 'yes' === get_user_meta( $vendor_id, '_wcv_trusted_vendor', true );
$untrusted_vendor     = 'yes' === get_user_meta( $vendor_id, '_wcv_untrusted_vendor', true );
$trusted_vendor_label = get_option( 'wcvendors_trusted_vendor_label', __( 'Trusted Vendor', 'reign' ) );

// If both trusted and untrusted are checked, don't show trusted badge.
if ( $trusted_vendor && $untrusted_vendor ) {
	$trusted_vendor = false;
}

// Store title.
$_store_title      = isset( $vendor_meta['pv_shop_name'] ) ? $vendor_meta['pv_shop_name'] : '';
$store_title       = ( is_product() ) ? '<a href="' . WCV_Vendors::get_vendor_shop_page( $post->post_author ) . '">' . $_store_title . '</a>' : $_store_title;
$store_description = ( array_key_exists( 'pv_shop_description', $vendor_meta ) ) ? $vendor_meta['pv_shop_description'] : '';

$phone = ( array_key_exists( '_wcv_store_phone', $vendor_meta ) ) ? $vendor_meta['_wcv_store_phone'] : '';

if ( class_exists( 'WCVendors_Pro' ) ) {
	$address      = wcv_format_store_address( $vendor_id );
	$social_icons = wcv_format_store_social_icons( $vendor_id );
}
// This is where you would load your own custom meta fields if you stored any in the settings page for the dashboard.
?>

<div class="wcv-store-header header-modern layout_one">
	<div class="upper">
		<div class="cover" style="background-image: url(<?php echo esc_url( $store_banner_image_url ); ?>);"></div>
		<div class="container">
			<div class="info-wrapper">
				<div class="info">
					<div class="avatar">
						<?php echo wp_kses_post( $store_icon ); ?>
					</div>
					<div class="about">
						<div class="name">
							<?php do_action( 'wcv_before_vendor_store_title' ); ?>
							<div class="txt"><?php echo esc_html( $store_title ); ?></div>
								<?php if ( class_exists( 'WCVendors_Pro' ) && ( $verified_vendor || $trusted_vendor ) ) : ?>
									<div class="wcv-vendor-badges">
										<?php if ( $verified_vendor ) : ?>
											<div class="wcv-vendor-badge wcv-verified-vendor">
												<?php wcvp_get_icon( 'wcv-icon wcv-icon-xs', 'wcv-icon-verified' ); ?>
												<span class="badge-text"><?php echo esc_html( $verified_vendor_label ); ?></span>
											</div>
										<?php endif; ?>
										<?php if ( $trusted_vendor ) : ?>
											<div class="wcv-vendor-badge wcv-trusted-vendor">
												<?php wcvp_get_icon( 'wcv-icon wcv-icon-xs', 'wcv-icon-trusted' ); ?>
												<span class="badge-text"><?php echo esc_html( $trusted_vendor_label ); ?></span>
											</div>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							<?php do_action( 'wcv_after_vendor_store_title' ); ?>
						</div>
						<?php do_action( 'wcv_before_vendor_store_description' ); ?>
						<p class="desc"><?php echo esc_html( $store_description ); ?></p>
						<?php if ( reign_wc_vendors_format_store_url( $vendor_id ) ) : ?>
							<p class="url"><?php echo reign_wc_vendors_format_store_url( $vendor_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
						<?php endif; ?>
						<?php do_action( 'wcv_after_vendor_store_description' ); ?>
					</div>
				</div><!-- info -->
			</div>
		</div>
	</div>	
	<div class="meta">
		<?php if ( class_exists( 'WCVendors_Pro' ) ) : ?>
			<?php do_action( 'wcv_before_vendor_store_rating' ); ?>
			<?php if ( ! wc_string_to_bool( get_option( 'wcvendors_ratings_management_cap', 'no' ) ) ) : ?>
				<div class="rating block">
					<div class="label">
						<?php echo esc_html__( 'Rating', 'reign' ); ?>
					</div>
					<div class="stars">
						<?php echo reign_kses_post_with_icons( WCVendors_Pro_Ratings_Controller::ratings_link( $vendor_id, true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses'd with SVG icon vocabulary; wp_kses_post strips the star SVGs. ?>
					</div>
				</div>
			<?php endif; ?>
			<?php do_action( 'wcv_after_vendor_store_rating' ); ?>
		<?php endif; ?>

		<?php do_action( 'wcvendors_before_header_meta_phone' ); ?>
		<?php if ( $phone ) : ?>
			<div class="phone block">
				<div class="label">
					<?php echo esc_html__( 'Phone', 'reign' ); ?>
				</div>
				<a href="tel:<?php echo esc_url( $phone ); ?>">
					<i class="fa fa-phone" aria-hidden="true"></i>
					<?php echo esc_html( $phone ); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php do_action( 'wcvendors_after_header_meta_phone' ); ?>
	
		<?php do_action( 'wcvendors_before_header_meta_address' ); ?>
		<?php $address = reign_wc_vendors_format_store_address( $vendor_id ); ?>
		<?php if ( $address ) : ?>
			<div class="address block">
				<div class="label">
					<?php echo esc_html__( 'Address', 'reign' ); ?>
				</div>
				<a href="http://maps.google.com/maps?&q=<?php echo esc_url( $address ); ?>">
					<address>
						<i class="fa fa-home" aria-hidden="true"></i>
						<?php echo esc_html( $address ); ?>
					</address>
				</a>
			</div>
		<?php endif; ?>
		<?php do_action( 'wcvendors_after_header_meta_address' ); ?>
	
		<?php if ( class_exists( 'WCVendors_Pro' ) ) : ?>
			<?php do_action( 'wcvendors_before_header_store_icon' ); ?>
				<?php $social_icons = function_exists( 'wcv_format_store_social_icons' ) ? wcv_format_store_social_icons( $vendor_id ) : ''; ?>
			<?php if ( $social_icons ) : ?>
				<div class="social block">
					<div class="label">
						<?php echo esc_html__( 'Social', 'reign' ); ?>
					</div>
					<?php echo reign_kses_post_with_icons( $social_icons ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses'd with SVG icon vocabulary; wp_kses_post strips the wcv_get_icon() SVGs. ?>
				</div>
			<?php endif; ?>
			<?php do_action( 'wcvendors_after_header_store_icon' ); ?>
		<?php endif; ?>
	
		<?php
		if ( class_exists( 'WCVendors_Pro' ) ) :
			?>
			<?php do_action( 'wcvendors_before_header_meta_sales' ); ?>
			<?php if ( wc_string_to_bool( get_option( 'wcvendors_show_store_total_sales' ) ) ) : ?>
				<div class="sales block">
					<div class="label">
						<?php echo esc_html__( 'Total Sales', 'reign' ); ?>
					</div>
					<div class="value">
						<i class="fa fa-info-circle" aria-hidden="true"></i>
						<?php
						$label = WCVendors_Pro_Vendor_Controller::get_total_sales_label( $vendor_id, 'store' );
						echo do_shortcode( '[wcv_pro_vendor_totalsales vendor_id="' . $vendor_id . '" position="none"]' );
						?>
					</div>
				</div>
			<?php endif; ?>
			<?php do_action( 'wcvendors_after_header_meta_sales' ); ?>
		<?php endif; ?>
	</div>
</div>


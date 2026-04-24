<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$reign_woo_myaccount_menu_toggle = get_theme_mod( 'reign_woo_myaccount_menu_toggle', false );

?>

<nav class="woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e( 'Account pages', 'reign' ); ?>">
	<?php if ( $reign_woo_myaccount_menu_toggle ) : ?>
		<div class="rg-MyAccount-navigation-heading">
			<h5>
				<?php esc_html_e( 'Menu', 'reign' ); ?>
				<a href="#" class="rg-my-account-nav" aria-label="<?php esc_attr_e( 'Toggle account navigation', 'reign' ); ?>" aria-expanded="false"><i class="far fa-bars" aria-hidden="true"></i></a>
			</h5>
		</div>
	<?php endif; ?>
	<ul class="woocommerce-MyAccount-menu">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
					<?php echo esc_html( $label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>

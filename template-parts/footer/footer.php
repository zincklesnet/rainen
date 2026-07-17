<?php
/**
 * Footer
 *
 * This is the template that displays footer content
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>

<footer itemscope="itemscope" itemtype="http://schema.org/WPFooter">
	<?php
	if ( is_active_sidebar( 'footer-widget-area' ) ) {
		?>
		<div class="footer-wrap">
			<div class="container">
				<aside id="footer-area" class="widget-area footer-widget-area" role="complementary">
					<div class="widget-area-inner">
						<div class="wb-grid">
							<?php dynamic_sidebar( 'footer-widget-area' ); ?>
						</div>
					</div>
				</aside>
			</div>
		</div>
		<?php
	}
	?>
	<?php
	$reign_footer_bottom = get_theme_mod( 'reign_footer_copyright_enable', true );
	if ( reign_is_truthy( $reign_footer_bottom ) ) {
		?>
		<div id="reign-copyright-text">
			<div class="container">
				<?php echo wp_kses_post( reign_footer_custom_copyright_text() ); ?>
			</div>	
		</div>
		<?php
	}
	?>
</footer>

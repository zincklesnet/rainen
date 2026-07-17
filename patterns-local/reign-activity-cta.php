<?php
/**
 * Title: Reign Activity Stream CTA
 * Slug: reign/activity-cta
 * Categories: reign-cta, call-to-action
 * Description: Calls visitors to share their first activity update in the community.
 * Keywords: activity, buddypress, share, cta
 * Viewport Width: 1440
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"contrast","layout":{"type":"constrained","contentSize":"900px"}} -->
<div class="wp-block-group alignfull has-contrast-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--40)">

	<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(1.875rem, 4vw, 2.75rem)","fontWeight":"700","lineHeight":"1.15"}},"textColor":"base"} -->
	<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color" style="font-size:clamp(1.875rem, 4vw, 2.75rem);font-weight:700;line-height:1.15"><?php esc_html_e( 'Share what you are working on. Get answers in minutes.', 'reign' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.125rem","lineHeight":"1.6"}},"textColor":"contrast-3"} -->
	<p class="has-text-align-center has-contrast-3-color has-text-color" style="font-size:1.125rem;line-height:1.6"><?php esc_html_e( 'The activity stream is where our members swap ideas, ask for feedback, and celebrate wins. Your first post is one click away.', 'reign' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
		<!-- wp:button {"backgroundColor":"accent","textColor":"base","style":{"border":{"radius":"8px"},"spacing":{"padding":{"left":"1.75rem","right":"1.75rem","top":"0.9rem","bottom":"0.9rem"}}}} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-base-color has-accent-background-color has-text-color has-background wp-element-button" style="border-radius:8px;padding-top:0.9rem;padding-right:1.75rem;padding-bottom:0.9rem;padding-left:1.75rem"><?php esc_html_e( 'Share your first update', 'reign' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->

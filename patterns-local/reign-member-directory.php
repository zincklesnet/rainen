<?php
/**
 * Title: Reign Member Directory Promo
 * Slug: reign/member-directory
 * Categories: reign-features, reign-social-proof, featured
 * Description: A members-directory promo block: stat callouts plus a CTA to browse members.
 * Keywords: members, directory, buddypress, community
 * Viewport Width: 1440
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--40)">

	<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 4vw, 3rem)","fontWeight":"700"}}} -->
	<h2 class="wp-block-heading has-text-align-center" style="font-size:clamp(2rem, 4vw, 3rem);font-weight:700"><?php esc_html_e( 'Meet the people behind the conversations.', 'reign' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.125rem"}},"textColor":"contrast-2"} -->
	<p class="has-text-align-center has-contrast-2-color has-text-color" style="font-size:1.125rem"><?php esc_html_e( 'Browse, search, and follow members across topics and locations.', 'reign' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--50)">

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"radius":"12px"}},"backgroundColor":"surface-1","layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-surface-1-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
				<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"}},"textColor":"accent"} -->
				<h3 class="wp-block-heading has-accent-color has-text-color" style="font-size:2.5rem;font-weight:700">10k+</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem"}}} -->
				<p style="font-size:1rem"><?php esc_html_e( 'Active members', 'reign' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"radius":"12px"}},"backgroundColor":"surface-1","layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-surface-1-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
				<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"}},"textColor":"accent"} -->
				<h3 class="wp-block-heading has-accent-color has-text-color" style="font-size:2.5rem;font-weight:700">2k+</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem"}}} -->
				<p style="font-size:1rem"><?php esc_html_e( 'Groups across topics', 'reign' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"radius":"12px"}},"backgroundColor":"surface-1","layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-surface-1-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
				<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"}},"textColor":"accent"} -->
				<h3 class="wp-block-heading has-accent-color has-text-color" style="font-size:2.5rem;font-weight:700">50k+</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem"}}} -->
				<p style="font-size:1rem"><?php esc_html_e( 'Conversations started', 'reign' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
		<!-- wp:button {"backgroundColor":"accent","textColor":"base","style":{"border":{"radius":"8px"},"spacing":{"padding":{"left":"1.5rem","right":"1.5rem","top":"0.8rem","bottom":"0.8rem"}}}} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-base-color has-accent-background-color has-text-color has-background wp-element-button" style="border-radius:8px;padding-top:0.8rem;padding-right:1.5rem;padding-bottom:0.8rem;padding-left:1.5rem"><?php esc_html_e( 'Browse all members', 'reign' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->

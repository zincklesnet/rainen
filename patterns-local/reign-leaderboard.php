<?php
/**
 * Title: Reign Community Leaderboard
 * Slug: reign/leaderboard
 * Categories: reign-features, reign-social-proof
 * Description: Top contributors list with avatars and contribution counts.
 * Keywords: leaderboard, members, top contributors
 * Viewport Width: 1440
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"900px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--40)">

	<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(1.875rem, 4vw, 2.75rem)","fontWeight":"700"}}} -->
	<h2 class="wp-block-heading has-text-align-center" style="font-size:clamp(1.875rem, 4vw, 2.75rem);font-weight:700"><?php esc_html_e( 'This week’s top contributors', 'reign' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1rem"}},"textColor":"contrast-2"} -->
	<p class="has-text-align-center has-contrast-2-color has-text-color" style="font-size:1rem"><?php esc_html_e( 'Recognising members who replied, shared, and welcomed others.', 'reign' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"surface-1","layout":{"type":"constrained"}} -->
	<div class="wp-block-group has-surface-1-background-color has-background" style="border-radius:12px;margin-top:var(--wp--preset--spacing--50);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">

		<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
		<div class="wp-block-columns are-vertically-aligned-center">
			<!-- wp:column {"width":"50px"} -->
			<div class="wp-block-column" style="flex-basis:50px"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.5rem","fontWeight":"700"}},"textColor":"accent"} --><p class="has-accent-color has-text-color" style="font-size:1.5rem;font-weight:700">1.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.125rem","fontWeight":"600"}}} --><p style="font-size:1.125rem;font-weight:600"><?php esc_html_e( 'Sarah Chen', 'reign' ); ?></p><!-- /wp:paragraph --><!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"contrast-2"} --><p class="has-contrast-2-color has-text-color" style="font-size:0.875rem"><?php esc_html_e( 'Replied to 32 threads', 'reign' ); ?></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:separator {"backgroundColor":"contrast-3","style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} -->
		<hr class="wp-block-separator has-text-color has-contrast-3-color has-alpha-channel-opacity has-contrast-3-background-color has-background" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)"/>
		<!-- /wp:separator -->

		<!-- wp:columns {"verticalAlignment":"center"} -->
		<div class="wp-block-columns are-vertically-aligned-center">
			<!-- wp:column {"width":"50px"} -->
			<div class="wp-block-column" style="flex-basis:50px"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.5rem","fontWeight":"700"}},"textColor":"accent"} --><p class="has-accent-color has-text-color" style="font-size:1.5rem;font-weight:700">2.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.125rem","fontWeight":"600"}}} --><p style="font-size:1.125rem;font-weight:600"><?php esc_html_e( 'Marcus Lee', 'reign' ); ?></p><!-- /wp:paragraph --><!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"contrast-2"} --><p class="has-contrast-2-color has-text-color" style="font-size:0.875rem"><?php esc_html_e( 'Started 8 popular discussions', 'reign' ); ?></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:separator {"backgroundColor":"contrast-3","style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} -->
		<hr class="wp-block-separator has-text-color has-contrast-3-color has-alpha-channel-opacity has-contrast-3-background-color has-background" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)"/>
		<!-- /wp:separator -->

		<!-- wp:columns {"verticalAlignment":"center"} -->
		<div class="wp-block-columns are-vertically-aligned-center">
			<!-- wp:column {"width":"50px"} -->
			<div class="wp-block-column" style="flex-basis:50px"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.5rem","fontWeight":"700"}},"textColor":"accent"} --><p class="has-accent-color has-text-color" style="font-size:1.5rem;font-weight:700">3.</p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":"1.125rem","fontWeight":"600"}}} --><p style="font-size:1.125rem;font-weight:600"><?php esc_html_e( 'Priya Sharma', 'reign' ); ?></p><!-- /wp:paragraph --><!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"contrast-2"} --><p class="has-contrast-2-color has-text-color" style="font-size:0.875rem"><?php esc_html_e( 'Welcomed 12 new members', 'reign' ); ?></p><!-- /wp:paragraph --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->

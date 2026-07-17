<?php
/**
 * Title: Reign Community Hero
 * Slug: reign/community-hero
 * Categories: reign-hero, header, featured
 * Description: Hero section emphasising community/membership with a CTA pair for joining and exploring.
 * Keywords: community, hero, members, buddypress
 * Viewport Width: 1440
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"surface-1","layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull has-surface-1-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--40)">

	<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|60","left":"var:preset|spacing|60"}}}} -->
	<div class="wp-block-columns are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">

			<!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.12em","fontSize":"0.8125rem"}},"textColor":"accent","className":"reign-hero-eyebrow"} -->
			<p class="has-accent-color has-text-color reign-hero-eyebrow" style="font-size:0.8125rem;letter-spacing:0.12em;text-transform:uppercase"><?php esc_html_e( 'Join the community', 'reign' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"clamp(2.5rem, 5vw, 4rem)","lineHeight":"1.05","fontWeight":"700"}}} -->
			<h1 class="wp-block-heading" style="font-size:clamp(2.5rem, 5vw, 4rem);font-weight:700;line-height:1.05"><?php esc_html_e( 'A place to connect, share, and grow together.', 'reign' ); ?></h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.125rem","lineHeight":"1.6"}},"textColor":"contrast-2"} -->
			<p class="has-contrast-2-color has-text-color" style="font-size:1.125rem;line-height:1.6"><?php esc_html_e( 'Thousands of members already discussing the topics that matter. Sign up free, browse groups, and start your first conversation in under a minute.', 'reign' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20","left":"var:preset|spacing|20"}}}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"backgroundColor":"accent","textColor":"base","style":{"border":{"radius":"8px"},"spacing":{"padding":{"left":"1.75rem","right":"1.75rem","top":"0.9rem","bottom":"0.9rem"}}}} -->
				<div class="wp-block-button"><a class="wp-block-button__link has-base-color has-accent-background-color has-text-color has-background wp-element-button" style="border-radius:8px;padding-top:0.9rem;padding-right:1.75rem;padding-bottom:0.9rem;padding-left:1.75rem"><?php esc_html_e( 'Join free', 'reign' ); ?></a></div>
				<!-- /wp:button -->

				<!-- wp:button {"textColor":"contrast","style":{"border":{"radius":"8px","color":"currentColor","width":"1px"},"spacing":{"padding":{"left":"1.75rem","right":"1.75rem","top":"0.9rem","bottom":"0.9rem"}}},"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-contrast-color has-text-color has-border-color wp-element-button" style="border-color:currentColor;border-width:1px;border-radius:8px;padding-top:0.9rem;padding-right:1.75rem;padding-bottom:0.9rem;padding-left:1.75rem"><?php esc_html_e( 'Explore groups', 'reign' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">

			<!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
			<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/community-hero-fallback.svg' ); ?>" alt="<?php esc_attr_e( 'A friendly online community', 'reign' ); ?>" style="border-radius:16px"/></figure>
			<!-- /wp:image -->

		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->

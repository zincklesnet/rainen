<?php
/**
 * BuddyPress Zombify Submissions Component 'All Submissions' tab content.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; ?>

<div id="zombify-main-section-front" class="zombify-main-section-front zf-submission">
    <div class="zf-post-list">
        <?php
        if( have_posts() ) {
            $template_path = zf_submissions_get_loop_item_template_path();
            while (have_posts()) {
                the_post();
                include $template_path;
            }

            zf_submissions_pagination();
        }
        ?>
    </div>
</div>

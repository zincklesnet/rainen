<?php
/**
 * View: Default Template for Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/default-template.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

use Tribe\Events\Views\V2\Template_Bootstrap;

get_header();

do_action( 'reign_before_content_section' );

echo tribe( Template_Bootstrap::class )->get_view_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

do_action( 'reign_after_content_section' );

get_footer();

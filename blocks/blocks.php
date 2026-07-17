<?php
/**
 * Reign Theme Blocks
 *
 * Include all registered blocks to appear in the backend.
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Include the reign news widget block.
require_once __DIR__ . '/reign-news-widget/reign-news-widget.php';

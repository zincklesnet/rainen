<?php
/**
 * Backward-compatible loader for the EDD theme updater admin class.
 *
 * The class now lives in class-edd-reign-theme-updater-admin.php (WPCS
 * file-naming convention). This shim is kept because consumers outside this
 * directory still include `theme-updater-admin.php` by path.
 *
 * @package EDD Sample Theme
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

require_once __DIR__ . '/class-edd-reign-theme-updater-admin.php';

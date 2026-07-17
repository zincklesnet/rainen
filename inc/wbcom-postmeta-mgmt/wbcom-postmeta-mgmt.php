<?php
/**
 * Bootstrap for the Wbcom Postmeta Management module.
 *
 * The Wbcom_Postmeta_Management class lives in
 * class-wbcom-postmeta-management.php (WPCS file-naming convention). This file
 * is the module entry point loaded from functions.php and simply boots the
 * single instance.
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

require_once __DIR__ . '/class-wbcom-postmeta-management.php';

/**
 * Main instance of Wbcom_Postmeta_Management.
 */
Wbcom_Postmeta_Management::instance();

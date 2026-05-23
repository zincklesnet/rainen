<?php
/*
Plugin Name: Formidable MyCRED
Description: Create a new formidable field to add or subtract from MyCRED credits.
Version: 1.2
Plugin URI: http://extend.bt4.me/downloads/formidable-mycred/
Author: Bento4Extend
Author URI: http://extend.bt4.me/
*/
// Settings

define('VERSION',1.22);
 

require_once(dirname( __FILE__ ) .'/models/FrmMCSettings.php');


//Controllers
require_once(dirname( __FILE__ ) .'/controllers/FrmMCAppController.php');
require_once(dirname( __FILE__ ) .'/controllers/FrmMCSettingsController.php');
 
$obj = new FrmMCAppController();
$obj = new FrmMCSettingsController();
 


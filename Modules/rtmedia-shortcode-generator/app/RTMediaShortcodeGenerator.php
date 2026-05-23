<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RTMediaShortcodeGenerator
 *
 * @author sanket
 */
if( !defined( 'ABSPATH' ) ) {
    exit;
}

class RTMediaShortcodeGenerator {

    public function __construct() {
        $this->load_translation();
        
        new RTMediaShortcodeGeneratorAdmin();
    }

    public function load_translation() {
        load_plugin_textdomain( 'rtmedia', false, basename( RTMEDIA_SHORTCODE_GENERATOR_PATH ) . '/languages/' );
    }

}

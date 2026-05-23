<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RTMediaShortcodeGeneratorAdmin
 *
 * @author sanket
 */
class RTMediaShortcodeGeneratorAdmin {

    public function __construct() {
        add_action( 'wp_ajax_rtmedia_shortcode_editor', array( 'RTMediaEditorLoader', 'content' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'rtmedia_pro_admin_script' ) );
        add_action( 'init', array( &$this, 'rtmedia_pro_short_code_button' ) );
    }

    public function rtmedia_pro_admin_script() {
        wp_localize_script( 'editor', 'rtmedia_pro_ajax_url', admin_url( 'admin-ajax.php' ) );
    }

    public function rtmedia_pro_short_code_button() {
        add_filter( "mce_external_plugins", array( &$this, "rtmedia_pro_short_code_add_buttons" ) );
        add_filter( 'mce_buttons', array( &$this, 'rtmedia_pro_short_code_register_buttons' ) );
    }

    public function rtmedia_pro_short_code_add_buttons( $plugin_array ) {
        $plugin_array[ 'rtmedia_pro_short_code' ] = RTMEDIA_SHORTCODE_GENERATOR_URL . '/app/assets/js/rtmedia_pro_short_codes.js';

        return $plugin_array;
    }

    public function rtmedia_pro_short_code_register_buttons( $buttons ) {
        array_push( $buttons, 'rtmedia_pro_short_code' );

        return $buttons;
    }

}

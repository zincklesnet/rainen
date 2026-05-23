<?php

class FrmMCUpdate{

    function FrmMCUpdate(){


        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            include( FrmMCAppController::path() . '/models/EDD_SL_Plugin_Updater.php' );
        }

        $license_key = trim( get_option( 'frm_mc_edd_licence_key' ) );
        $plugin_file = FrmMCAppController::path() . '/formidable-mycred.php';
        $edd_updater = new EDD_SL_Plugin_Updater( 'http://extend.bt4.me/', $plugin_file, array(
                'version' 	=> '1.2', 		// current version number
                'license' 	=> $license_key, 	// license key (used get_option above to retrieve from DB)
                'item_name'     => 'Formidable MyCRED', 	// name of this plugin
                'author' 	=> 'Bento4Extend',  // author of this plugin
                'url'           => home_url()
            )
        );

    }

}
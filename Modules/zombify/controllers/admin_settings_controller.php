<?php
/**
 * Zombify Admin Settings Controller
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_Admin_Settings_Controller") ){

    class Zombify_Admin_Settings_Controller extends Zombify_BaseController{

        /**
         * The view path
         */
        protected $view_path = 'admin';

        public function actionIndex(){

            echo $this->render("sections", array(
                "active_tab" => "index"
            ));
            echo $this->render("index");

        }

        public function actionFormats(){

            echo $this->render("sections", array(
                "active_tab" => "formats"
            ));
            echo $this->render("formats");

        }

        public function actionFormats_story(){

            echo $this->render("sections", array(
                "active_tab" => "formats_story"
            ));
            echo $this->render("formats_story");

        }

        public function actionBranding(){

            echo $this->render("sections", array(
                "active_tab" => "branding"
            ));
            echo $this->render("branding");

        }

        public function actionCloudconvert(){

            echo $this->render("sections", array(
                "active_tab" => "cloudconvert"
            ));
            echo $this->render("cloudconvert");

        }

    }

    function zombify_admin_settings_controller()
    {
        return Zombify_Admin_Settings_Controller::get_instance()->action();
    }

}
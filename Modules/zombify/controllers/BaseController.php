<?php
/**
 * Zombify Base Controller
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_BaseController") ){

    abstract class Zombify_BaseController{

        /**
         * The object instance
         *
         * @var Zombify_BaseController
         */
        protected static $instance;

        /**
         * The view path
         */
        protected $view_path = '';

        /**
         * Return the only existing instance of object
         *
         * @return Zombify_Admin_Settings_Controller
         */
        public static function get_instance()
        {
            if (!isset(self::$instance)) {
                $className = get_called_class();
                self::$instance = new $className();
            }

            return self::$instance;
        }

        /**
         * Prevent object cloning
         */
        protected function __clone() {}

        /**
         * Zombify_BaseController constructor.
         */
        protected function __construct()
        {
            //$this->action();
        }

        /**
         * Zombify_BaseController constructor.
         */
        public function action( $action_param = '', $default_action = '' )
        {
            $action = "action".ucfirst( $action_param == '' ? ( isset($_GET["action"]) ? $_GET["action"] : ( $default_action == "" ? "index" : $default_action ) ) : $action_param );

            if( method_exists( $this, $action ) ) {

                try{
                    return $this->$action();
                } catch(Exception $e){

                    $this->view_path = '';

                    return $this->render("error", array(
                        "error_title" => esc_html__("Notification", "zombify"),
                        "error_msg" => $e->getMessage(),
                    ));
                }

            } else
                return $this->render("error", array(
                    "error_title" => esc_html__("404 Not Found", "zombify"),
                    "error_msg" => esc_html__("Action not found. Please check the URL.", "zombify"),
                ));
        }

        /**
         * Zombify_View generator.
         */
        protected function render( $template, $args = array() )
        {

            $template_file = zombify()->locate_template( $this->view_path.'/'.$template.'.php' );

            if( file_exists($template_file) )
            {
                extract($args);

                ob_start();
                include $template_file;
                $output = ob_get_contents();
                ob_end_clean();

                return $output;

            }
            else
            {
                $this->view_path = '';

                return $this->render("error", array(
                    "error_title" => esc_html__("View file not found", "zombify"),
                    "error_msg" => esc_html__("The view file doesn't exist.", "zombify"),
                ));
            }
        }

    }

}
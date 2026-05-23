<?php
/**
Plugin Name:    Zombify
Plugin URI:     https://px-lab.com/
Description:    Frontend Uploader
Author:         Px-Lab
Version:        1.7.8
Author URI:     https://px-lab.com/
Text Domain:    zombify
Domain Path:    /languages

 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Zombify' ) ) :

    /**
     * Main Zombify class
     */
    final class Zombify
    {

        /**
         * The Zombify object instance
         *
         * @var Zombify
         */
        private static $instance;

	    /**
	     * Core loaded status
	     * @var bool
	     */
        private $core_loaded = false;

	    /**
	     * The Zombify config
	     *
	     * @var Zombify
	     */
	    private $config;

        /**
         * Plugin filename
         *
         * @var string
         */
        public $file;

        /**
         * Plugin basename
         *
         * @var string
         */
        public $basename;

        /**
         * Plugin dir path
         *
         * @var string
         */
        public $plugin_dir;

        /**
         * Plugin dir url
         *
         * @var string
         */
        public $plugin_url;

	    /**
	     * Theme dir path
	     * @var
	     */
        public $theme_dir;

	    /**
	     * Theme dir url
	     * @var
	     */
        public $theme_url;

        /**
         * Plugin includes dir path
         *
         * @var string
         */
        public $includes_dir;

        /**
         * Plugin includes dir url
         *
         * @var string
         */
        public $includes_url;

        /**
         * Plugin embeds dir path
         *
         * @var string
         */
        public $embeds_dir;

        /**
         * Plugin views dir path
         *
         * @var string
         */
        public $views_dir;

        /**
         * Plugin views dir url
         *
         * @var string
         */
        public $views_url;

        /**
         * Plugin quiz dir path
         *
         * @var string
         */
        public $quiz_dir;

        /**
         * Plugin assets dir path
         *
         * @var string
         */
        public $assets_dir;

        /**
         * Plugin assets dir url
         *
         * @var string
         */
        public $assets_url;

        /**
         * Plugin controllers dir path
         *
         * @var string
         */
        public $controllers_dir;

		/**
         * Plugin data
         *
         * @var
         */
        public $plugin_data;

        /**
         * Plugin admin dir url
         *
         * @var string
         */
        public $admin_url;

        public $options_defaults = array();

        public $post_main_fields = array(
            "image" => array(
                "image" => "hidden",
                "subtitle" => "hidden",
                "description" => "hidden",
            ),
            "gif" => array(
                "gif" => "hidden",
                "subtitle" => "hidden",
                "description" => "hidden",
            ),
            "audio" => array(
                "image" => "show",
                "subtitle" => "hidden",
                "description" => "hidden",
            ),
            "video" => array(
                "subtitle" => "hidden",
                "description" => "hidden",
            ),
            "list" => array(
                "subtitle" => "hidden",
                "description" => "hidden",
            ),
            "meme" => array(
                "image" => "hidden",
                "subtitle" => "hidden",
                "description" => "hidden",
            ),
            "openlist" => array(
                "description" => "hidden",
            ),
            "personality" => array(
                "description" => "hidden",
            ),
            "poll" => array(
                "description" => "hidden",
            ),
            "rankedlist" => array(
                "description" => "hidden",
            ),
            "story" => array(
                "description" => "hidden",
            ),
            "trivia" => array(
                "description" => "hidden",
            ),
        );

        public $postsave_types = array(
            "personality" => "shortcode",
            "trivia" => "shortcode",
            "poll" => "shortcode",
            "story" => "shortcode",
            "list" => "shortcode",
            "openlist" => "shortcode",
            "rankedlist" => "shortcode",
            "video" => "shortcode",
            "audio" => "shortcode",
            "image" => "shortcode",
            "gif" => "shortcode",
            "meme" => "shortcode",
        );

        public $sub_posts_loop = true;

		/**

         * Manually selected categories count limit
         *
         * @var int
         */
        public $categories_limit = 3;

	    /**
	     * Get plugin data
	     * @return object
	     */
	    function get_plugin_data() {
		    if( ! $this->plugin_data ) {
			    $this->plugin_data = (object)array_change_key_case( get_plugin_data( __FILE__, false, false ) );
		    }

		    return $this->plugin_data;

	    }

	    /**
	     * Get allowed video mime types
	     * @return array
	     */
        function get_allowed_video_extensions(){
            $disable_mp4 = zf_get_option('zombify_disable_mp4_upload', 0);
            return $disable_mp4 ? array() : array( 'mp4', 'webm' );
        }

	    /**
	     * Get allowed audio mime types
	     * @return array
	     */
        function get_allowed_audio_extensions(){
            $disable_mp3 = zf_get_option('zombify_disable_mp3_upload', 0);
	        return $disable_mp3 ? array() : array( 'mp3' );
        }

	    /**
	     * Get audio upload max size
	     * @return int
	     */
        function get_audio_upload_max_size(){
            return (int)zf_get_option('zombify_max_upload_mp3_size', 8388608);
        }

	    /**
	     * Get video upload max size
	     * @return int
	     */
        function get_video_upload_max_size(){
            return (int)zf_get_option('zombify_max_upload_mp4_size', 33554432);
        }

	    /**
	     * Get chunk file size
	     * @return mixed
	     */
        function get_chunk_file_size(){

            $size = $this->get_file_upload_max_size();

            $optimal_size = 2*1024*1024;

            $max_size = floor($size / 2) - 10240; // Because the last chunk will be bigger than the chunk size, but less than 2*chunkSize

            return min($max_size, $optimal_size);

        }

	    /**
	     * Get file max upload size
	     * @return int
	     */
        function get_file_upload_max_size() {
            return wp_max_upload_size();
        }

        /**
         * Return the only existing instance of Zombify object
         *
         * @return Zombify
         */
        public static function get_instance() {
            if (!isset(self::$instance)) {
                self::$instance = new Zombify();
            }

            return self::$instance;
        }

        /**
         * Zombify constructor.
         */
        private function __construct() {

        	$this->setup();
            /** Standard plugin hooks ************************** */

            register_activation_hook( __FILE__, array( $this, 'activate' ) );
            register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

	        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
            add_action( 'after_setup_theme', array( $this, 'includes' ) );

        }

	    /**
	     * Setup
	     */
        private function setup() {
	        $this->setup_constants();
	        $this->setup_third_part_capability();
        }

        /**
         * Prevent object cloning
         */
        private function __clone() {}

        /**
         * Define plugin constants
         */
        private function setup_constants() {

            // Base.
            $this->file       = __FILE__;
            $this->basename   = apply_filters( 'zombify_plugin_basename', plugin_basename( $this->file ) );
            $this->plugin_dir = apply_filters( 'zombify_plugin_dir_path', plugin_dir_path( $this->file ) );
            $this->plugin_url = apply_filters( 'zombify_plugin_dir_url', plugin_dir_url( $this->file ) );
            $this->theme_dir  = apply_filters( 'zombify_theme_dir_path', get_stylesheet_directory() );
            $this->theme_url  = apply_filters( 'zombify_theme_dir_url', get_stylesheet_directory_uri() );

            // Includes.
            $this->includes_dir = apply_filters( 'zombify_includes_dir', trailingslashit( $this->plugin_dir . 'includes' ) );
            $this->includes_url = apply_filters( 'zombify_includes_url', trailingslashit( $this->plugin_url . 'includes' ) );

            // Embeds
            $this->embeds_dir = apply_filters( 'zombify_embeds_dir', trailingslashit( $this->plugin_dir . 'includes/embeds' ) );

            // Quiz.
            $this->quiz_dir = apply_filters( 'zombify_quiz_dir', trailingslashit( $this->plugin_dir . 'quiz' ) );

            // Views.
            $this->views_dir = apply_filters( 'zombify_views_dir', trailingslashit( $this->plugin_dir . 'views' ) );
            $this->views_url = apply_filters( 'zombify_views_url', trailingslashit( $this->plugin_url . 'views' ) );

            // Assets.
            $this->assets_dir = apply_filters( 'zombify_assets_dir', trailingslashit( $this->plugin_dir . 'assets' ) );
            $this->assets_url = apply_filters( 'zombify_assets_url', trailingslashit( $this->plugin_url . 'assets' ) );

            // Controllers.
            $this->controllers_dir = apply_filters( 'zombify_controllers_dir', trailingslashit( $this->plugin_dir . 'controllers' ) );

            $this->options_defaults = array(
                'zombify_branding_color'    => '#49c793',
                'zombify_max_upload_size'   => '2097152',
	            'zombify_logo'              => $this->assets_url.'images/zombify-logo.png',
            );
        }

	    /**
	     * Load plugin text domain
	     */
        public function load_plugin_textdomain() {
	        load_plugin_textdomain( 'zombify', false, dirname( plugin_basename( $this->file ) ) . '/languages' );
        }

	    /**
	     * Get plugin configuration
	     * @return Zombify
	     */
	    public function get_config() {
		    if( ! $this->config ) {
			    $this->set_config();
		    }

		    return $this->config;
	    }

	    /**
	     * Set plugin configuration
	     */
	    private function set_config() {

		    require( $this->plugin_dir . 'config/config.php' );
		    $config = $zf_config;

		    // $zf_config variable comes from included file
		    $local_config_path = $this->theme_dir . '/zombify/config/config.php';

		    if( is_file( $local_config_path ) ) {

			    require( $local_config_path );
			    $local_config = $zf_config;

			    $config = array_replace_recursive( $config, $local_config );

			    $new_order_tmp      = array();
			    foreach ( $config['zf_post_types'] as $key => $type) {
				    $new_order_tmp[$key] = $type['order'];
			    }
			    array_multisort( $new_order_tmp, SORT_ASC, $config['zf_post_types'] );

			    $new_sub_order_tmp  = array();
			    foreach ( $config['post_sub_types'] as $key => $type) {
				    $new_sub_order_tmp[$key] = $type['order'];
			    }
			    array_multisort( $new_sub_order_tmp, SORT_ASC, $config['post_sub_types'] );

			    $new_order          = array();
			    foreach ($new_order_tmp as $key => $type) {
				    $new_order[$key] = array(
					    'name'          => $config['zf_post_types'][$key]['name'],
					    'description'   => $config['zf_post_types'][$key]['description'],
					    'order'         => $config['zf_post_types'][$key]['order'],
					    'excerpt'       => $config['zf_post_types'][$key]['excerpt'],
					    'preface'       => $config['zf_post_types'][$key]['preface'],
					    'show'          => $config['zf_post_types'][$key]['show'],
				    );
			    }

			    $new_sub_order      = array();
			    foreach ($new_sub_order_tmp as $key => $type) {
				    $new_sub_order[$key] = array(
					    'name'          => $config['post_sub_types'][$key]['name'],
					    'description'   => $config['post_sub_types'][$key]['description'],
					    'formats'       => $config['post_sub_types'][$key]['formats'],
					    'icon'          => $config['post_sub_types'][$key]['icon'],
					    'excerpt'       => $config['post_sub_types'][$key]['excerpt'],
					    'preface'       => $config['post_sub_types'][$key]['preface'],
					    'show'          => $config['post_sub_types'][$key]['show'],
					    'first_group'   => $config['post_sub_types'][$key]['first_group'],
					    'order'         => $config['post_sub_types'][$key]['order'],
				    );
			    }

			    $config['zf_post_types']     = $new_order;
			    $config['post_sub_types']    = $new_sub_order;
		    }

		    $this->config = apply_filters( 'zf_config', $config );
	    }

	    /**
	     * Get possible post types
	     * @param bool $show
	     *
	     * @return array
	     */
        public function get_post_types( $show = false ) {

	        $zf_config = $this->get_config();

            if( $show ){

                $types = array();
                foreach( $zf_config[ 'zf_post_types' ] as $type_index=>$type ){
                    if( isset( $type[ 'show' ] ) && $type[ 'show' ] ){
                        $types[ $type_index ] = $type;
                    }
                }

            } else {
                $types = $zf_config[ 'zf_post_types' ];

            }

            return  $types ;
        }

	    /**
	     * Get possible post subtypes
	     * @param bool $show
	     *
	     * @return array
	     */
        public function get_post_subtypes( $show = false ) {
	        $zf_config = $this->get_config();

            if( $show ){

                $types = array();

                foreach( $zf_config['post_sub_types'] as $type_index=>$type ){
                    if( isset( $type[ 'show' ] ) && $type[ 'show' ] ){
                        $types[ $type_index ] = $type;
                    }
                }

            } else {
                $types = $zf_config['post_sub_types'];
            }

            return  $types ;
        }

        /**
         * Get active formats
         *
         * @param bool|false $filter
         * @param int $type_level
         * @param bool|true $per_role
         * @return array|mixed|void
         */
        public function get_active_formats( $filter = false, $type_level = 0, $per_role = true ) {

	        $current_user_info = wp_get_current_user();
            $formats           = (array) zf_get_option( 'zombify_active_formats', array() );

			/*Activate some formats for particular user roles*/
	        if ( $per_role == true && ! empty( $current_user_info->roles ) ) {
		        $role             = $current_user_info->roles[0];
		        $active_for_roles = array_fill_keys( $current_user_info->roles, array_keys( $formats ) );
		        $active_for_roles = apply_filters( 'zf_activate_for_roles', $active_for_roles );

		        if ( isset( $active_for_roles[ $role ] ) ) {
			        $active_formats = array();
			        foreach ( (array) $active_for_roles[ $role ] as $format ) {
				        if ( isset( $formats[ $format ] ) ) {
					        $active_formats[ $format ] = $formats[ $format ];
				        }
			        }
			        $formats = $active_formats;
		        }
	        }

            /* Figure out whether saved formats are default (numerically indexed) or saved by user (associatively indexed) */
            $is_defaults = true;
            if( count( array_filter( array_keys( $formats ), 'is_string' ) ) > 0 ) {
                $is_defaults = false;
            }

            /* If saved formats are defaults we need to check for new post formats and add them in `formats` */
            if( true === $is_defaults ) {
                $all_types  = zombify()->get_post_types();
                $formats    = array_keys( $all_types );
            }

            if( $filter ) {
                switch( $type_level ){

                    case 1:
                        $formats = array_filter( $formats, function( $v ) {
                        	return ( ( $v !== '' ) && ( substr($v, 0, strlen( 'subtype_' ) ) != 'subtype_' ) );
                        } );
                        break;

                    case 2:
                        $formats = array_filter( $formats, function( $v ) {
                        	return ( ( $v !== '' ) && ( substr($v, 0, strlen( 'subtype_' ) ) == 'subtype_' ) );
                        } );
                        $formats = array_map( function( $v ){
                        	return substr($v, strlen( 'subtype_' ) );
                        }, $formats );

                        break;

                    default:
                        $formats = array_filter( $formats, function( $v ) {
                            return ( $v !== '' );
                    } );

                }
            }

            return $formats;
        }

        public function get_active_post_types() {

        	$zombify_active_formats = zombify()->get_active_formats();
	        $zombify_active_formats = array_filter($zombify_active_formats);

	        $post_types = [];
	        $zombify_types_order = [];
	        $zombify_subtypes_order = [];

	        if( zf_get_option('zombify_types_order') ) {
		        $zombify_types_order = json_decode( zf_get_option('zombify_types_order') );
	        }

	        if( zf_get_option('zombify_subtypes_order') ) {
		        $zombify_subtypes_order = json_decode( zf_get_option('zombify_subtypes_order') );
	        }

	        foreach( zombify()->get_post_types(true) as $post_type_slug => $post_type_data ){
		        $post_type = $post_type_data;
		        $post_type["post_type_slug"] = $post_type_slug;
		        $post_type["post_type_level"] = 1;

		        if( zf_get_option('zombify_types_order') ) {
			        if( isset($zombify_types_order->$post_type_slug) ) {
				        $post_type["order"] = $zombify_types_order->$post_type_slug;
			        }
		        }

		        $post_types[$post_type_slug] = $post_type;
	        }

	        foreach( zombify()->get_post_subtypes(true) as $post_type_slug => $post_type_data ){
		        $post_type = $post_type_data;
		        $post_type["post_type_slug"] = $post_type_slug;
		        $post_type["post_type_level"] = 2;

		        if( zf_get_option('zombify_subtypes_order') ) {
			        if( $post_type_slug === 'main' ) {
				        $post_type_slug === 'story';
			        }

			        if( isset($zombify_subtypes_order->$post_type_slug) ) {
				        $post_type_slug = 'subtype_' . $post_type_slug;
				        $post_type["order"] = $zombify_subtypes_order->$post_type_slug;
			        }
		        }

		        $post_types[$post_type_slug] = $post_type;
	        }

	        uasort($post_types,function($a, $b) {
		        return $a['order'] - $b['order'];
	        });

	        $return = array();

	        foreach( $post_types as $post_type_key => $post_type_data ) {

		        $post_type_slug = $post_type_data['post_type_slug'];

		        if (in_array(($post_type_data['post_type_level'] == 1 ? $post_type_slug : 'subtype_' . $post_type_slug), $zombify_active_formats)) {
			        $return[ $post_type_key ] = $post_type_data;
		        }

	        }

	        return apply_filters( 'zf_active_post_types', $return );
        }

        /**
         * Include required files
         */
        public function includes() {

        	if( $this->core_loaded ) {
        		return;
	        }

            /** Activation, Deactivation, Uninstall ****************************** */

            require_once( $this->includes_dir . 'activation.php' );
            //require_once( $this->includes_dir . 'uninstall.php' );

            /** Controllers ******************************************************** */

            require_once( $this->controllers_dir . 'BaseController.php' );
            require_once( $this->controllers_dir . 'public_frontend_controller.php' );

            /** Embeds ******************************************************** */

            require_once( $this->embeds_dir . 'embed.php' );

            /** Quiz **************************************************************** */

            require_once( $this->quiz_dir . 'BaseQuiz.php' );

            foreach( $this->get_post_types() as $ptype => $ptype_data ) {
                require_once( $this->quiz_dir . ucfirst( strtolower( $ptype ) ).'Quiz.php' );
            }

            /** Public ************************************************************* */

	        require_once( $this->includes_dir . 'classes/class-data-provider.php' );
            require_once( $this->includes_dir . 'public/functions.php' );
            require_once( $this->includes_dir . 'public/hooks.php' );
            require_once( $this->includes_dir . 'public/public.php' );

            /** Admin ************************************************************ */

            if ( is_admin() ) {
                require_once( $this->controllers_dir . 'admin_settings_controller.php' );

                require_once( $this->includes_dir . 'admin/upgrade.php' );
                require_once( $this->includes_dir . 'admin/hooks.php' );
                require_once( $this->includes_dir . 'admin/settings.php' );
                require_once( $this->includes_dir . 'admin/admin.php' );
            }
        }

        /**
         * Other plugins integrations
         */
        private function setup_third_part_capability() {
            if( ! function_exists( 'is_plugin_active' ) ) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            require_once ( $this->includes_dir . 'public/w3-total-cache.php' );
            require_once ( $this->includes_dir . 'public/wp-rocket.php' );
            require_once( $this->includes_dir . 'public/buddypress/loader.php' );
            require_once( $this->includes_dir . 'public/fb-instant-articles/fb-instant-articles.php' );
	        require_once( $this->includes_dir . 'public/amp.php' );
        }

        /**
         * Run during plugin activation
         */
        public function activate() {
        	$this->includes();
            do_action( 'zombify_activation' );
        }

        /**
         * Run during plugin deactivation
         */
        public function deactivate() {
	        $this->includes();
            do_action( 'zombify_deactivation' );
        }

	    /**
	     * Get required options for quiz type
	     * @param bool $quiz_slug
	     *
	     * @return array|mixed
	     */
        public function getRequiredOptions( $quiz_slug = false ){

            $options = (array)apply_filters( 'zombify_required_options', array() );

            if( $quiz_slug ){
	            $options = isset( $options[ $quiz_slug ] ) ? $options[ $quiz_slug ] : array();
            }

            return $options;

        }

        /**
         * Locate view ( gives possibility to overwrite template from active theme - including child theme )
         * @param $template
         * @return string
         */
        public function locate_template( $template ) {
            $default = zombify()->views_dir . $template;
            $located = locate_template( 'zombify/' . $template );

            return $located ? $located : $default;
        }

        /**
         * Return permissions for user roles
         */
        public function user_roles_permissions(){

            $permissions = array(
                'create' => array( 'contributor', 'author', 'editor', 'administrator' ),
                'edit'   => array( 'editor', 'administrator' ),
                'edit_own' => array( 'author' ),
            );

            return apply_filters( 'zombify_user_permissions', $permissions );
        }

	    /**
	     * Get default option from plugin configuration file
	     * @param bool $option_name
	     *
	     * @return array|bool|mixed
	     */
        public function get_default_options( $option_name = false ){

	        $options_file_path = $this->plugin_dir . 'config/options.php';
	        $zf_options = is_file( $options_file_path ) ? require( $options_file_path ) : array();

            if( $option_name ){
                $value = isset( $zf_options[ $option_name ] ) ? $zf_options[ $option_name ] : false;
            } else {
	            $value = $zf_options;
            }

            return $value;

        }

	    /**
	     * Get quiz view directory
	     * @param $template
	     *
	     * @return string
	     */
        public function quiz_view_dir( $temp ){

	        $template = sprintf( 'quiz_view/%s', $temp );
	        if( zombify()->amp()->is_amp_endpoint() ){
		        $amp_template = sprintf( 'quiz_view_amp/%s', $temp );
		        if( file_exists( zombify()->views_dir . $amp_template ) ){
			        $template = $amp_template;
		        }
	        }

	        return $template;

        }

	    /**
	     * @return Zombify_AMP
	     */
	    public function amp() {
        	return Zombify_AMP::get_instance();
        }

    }

    /**
     * The main function responsible for returning the Zombify instance.
     *
     * @return Zombify
     */
    function zombify() {
        return Zombify::get_instance();
    }

    zombify();

endif;

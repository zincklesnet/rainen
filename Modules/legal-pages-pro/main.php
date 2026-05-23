<?php
if ( ! defined('ABSPATH') ) { die( ADL_LP_ALERT_MSG ); } // die if the page is accessed directly

if ( ! class_exists('Adl_Legal_Pages') ) :
    final class Adl_Legal_Pages {

        private $req_wp_version = '4.0';
        public $objects = array(); // all objects of our plugins will be stored here
        public $template_table_name;
        public $popups_table_name;


        /**
         * Load all classes and instantiate them and flush rewrite rules
         */
        public function __construct( ){
        global $wpdb;
            // Don't let the class/plugin instantiate outside of WordPress
            if ( ! defined('ABSPATH') ) { die( ADL_LP_ALERT_MSG ); }
            $this->template_table_name = $wpdb->prefix .'adl_lp_templates';
            $this->popups_table_name = $wpdb->prefix .'adl_lp_popups';
            // load all classes and its object
            $this->load_classes(ADL_LP_CLASS_DIR);

        }


        /**
         * It removes plugin data
         */
        public static function remove_plugin_data(  ) {
            global $wpdb;
            if ( get_option('adl_lp_misc')['delete_adl_lp_data'] ) {
                delete_option('adl_lp_excludePage');
                delete_option('adl_lp_general');
                delete_option('adl_lp_accept_term');
                delete_option('adl_lp_eu_cookie_title');
                delete_option('adl_lp_eu_cookie_message');
                delete_option('adl_lp_eu_cookie_enable');
                $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}adl_lp_templates");
                $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}adl_lp_popups");
                delete_option('adl_demo_inserted');
                delete_option('demo_data_inserted_by_lp_pro');
                delete_option('adl_lp_misc');

            }

            
        }


        /**
         * Prepare plugin to work by creating custom table to store plugin data and set some default options
         */
        public function prepare_plugin() {
            global $wpdb;

            $terms_forced = file_get_contents(dirname(__FILE__) . '/templates/Terms-latest.html');
            $ad_disclosure = file_get_contents(dirname(__FILE__) . '/templates/Advertising-disclosures.html');
            $conf_disclosure = file_get_contents(dirname(__FILE__) . '/templates/Confidentiality-Disclosures.html');
            $EULA = file_get_contents(dirname(__FILE__) . '/templates/End-user-license-agreement.html');
            $terms = file_get_contents(dirname(__FILE__) . '/templates/Terms.html');
            $privacyCalifornia = file_get_contents(dirname(__FILE__) . '/templates/privacyCalifornia.html');
            $privacy = file_get_contents(dirname(__FILE__) . '/templates/privacy.html');
            $eu_privacy = file_get_contents(dirname(__FILE__) . '/templates/eu-privacy.html');
            $earnings = file_get_contents(dirname(__FILE__) . '/templates/earnings.html');

            $disclaimer = file_get_contents(dirname(__FILE__) . '/templates/disclaimer.html');
            $testimonials = file_get_contents(dirname(__FILE__) . '/templates/testimonial-disclosure.html');
            $linking = file_get_contents(dirname(__FILE__) . '/templates/linking-policy.html');
            $refund = file_get_contents(dirname(__FILE__) . '/templates/refund-policy.html');
            $affiliate = file_get_contents(dirname(__FILE__) . '/templates/Affiliate-agreement.html');
            $disclosure = file_get_contents(dirname(__FILE__) . '/templates/affiliate-disclosure.html');
            $antispam = file_get_contents(dirname(__FILE__) . '/templates/antispam.html');
            $ftc = file_get_contents(dirname(__FILE__) . '/templates/ftcstatement.html');
            $medical = file_get_contents(dirname(__FILE__) . '/templates/medical-disclaimer.html');
            $amazon = file_get_contents(dirname(__FILE__) . '/templates/amazon-affiliate.html');
            $dart = file_get_contents(dirname(__FILE__) . '/templates/double-dart-cookie.html');
            $external = file_get_contents(dirname(__FILE__) . '/templates/external-links.html');
            $fbpolicy = file_get_contents(dirname(__FILE__) . '/templates/fbpolicy.html');
            $about_us = file_get_contents(dirname(__FILE__) . '/templates/about-us.html');
            $dmca = file_get_contents(dirname(__FILE__) . '/templates/dmca.html');
            $gigitalGoods = file_get_contents(dirname(__FILE__) . '/templates/digital-goods-refund-policy.html');
            $coppa = file_get_contents(dirname(__FILE__) . '/templates/COPPA.html');
            $pcp = file_get_contents(dirname(__FILE__) . '/templates/privacy-cookie-policy.html');


            add_option('adl_lp_excludePage', 'true');
            add_option('adl_lp_general', array()); 
            add_option('adl_lp_social', array()); 
            add_option('adl_lp_popup', array());
            add_option('adl_lp_accept_term', 0); // show first time warning to the users to accept terms and set this value to 1 later
            add_option('adl_lp_eu_cookie_title', 'We use Cookie to provide best user experience!');
            $message_body="This website has updated its privacy policy in compliance with EU Cookie legislation. Please read this to review the updates about which cookies we use and what information we collect on our site. By continuing to use this site, you are agreeing to our updated privacy policy.";
            add_option('adl_lp_eu_cookie_message', sanitize_text_field($message_body));
            add_option('adl_lp_eu_cookie_enable', 0);

            $adl_lp_misc = array(
                'hide_lp_in_search' =>  0,
                'show_affiliate_disclosure' => 0,
                'show_cookie_warning' => 0,
                'delete_adl_lp_data' =>  0,
                'disabled_pop_js_css' =>  0,

            );
            update_option('adl_lp_misc', $adl_lp_misc);

            // delete old tables of free version and starts fresh
            if ( !get_option('demo_data_inserted_by_lp_pro') ) {
                $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}adl_lp_templates");
            }
            // prepare sql statements for creating two tables. one for template and one for popups message
            $charset_collate = $wpdb->get_charset_collate();
            $wp_adl_lp_template = "CREATE TABLE IF NOT EXISTS $this->template_table_name (
                          id int(11) unsigned NOT NULL AUTO_INCREMENT,
                          name text COLLATE utf8mb4_unicode_ci NOT NULL,
                          content longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                          type varchar(50) DEFAULT '',
                          PRIMARY KEY  (id)
                        ) ENGINE=InnoDB $charset_collate;";

            $wp_adl_lp_popups = "CREATE TABLE IF NOT EXISTS $this->popups_table_name (
                          id int(11) unsigned NOT NULL AUTO_INCREMENT,
                          name text COLLATE utf8mb4_unicode_ci NOT NULL,
                          content longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                          PRIMARY KEY  (id)
                        ) ENGINE=InnoDB $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); // include upgrade.php to access dbDelta()
            dbDelta( $wp_adl_lp_template ); // create wp_adl_lp_templates table
            dbDelta( $wp_adl_lp_popups ); // create wp_adl_lp_popups table
            if ( !get_option('demo_data_inserted_by_lp_pro') ) { //if demo data has not been inserted then insert.
                $wpdb->insert($this->template_table_name, array('name'=> 'Advertising Disclosures', 'content'=> $ad_disclosure, 'type'=> '4d'), array('%s', '%s', '%s'));
                $wpdb->insert($this->template_table_name, array('name'=> 'Confidentiality Disclosure', 'content'=> $conf_disclosure, 'type'=> '4d'), array('%s', '%s', '%s'));
                $wpdb->insert($this->template_table_name, array('name'=> 'End-user License Agreement', 'content'=> $EULA, 'type'=> '4d'), array('%s', '%s', '%s'));
                $wpdb->insert($this->template_table_name, array('name'=> 'Forced Agreement to the Terms', 'content'=> $terms_forced, 'type'=> 'abcdefghij'), array('%s', '%s', '%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Terms of Use','content'=>$terms,'type'=>'1a2b3c4d5e6f7g8h9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'California Privacy Rights','content'=>$privacyCalifornia,'type'=>'1a2b3c4d5e6f7g8h9i10j'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Privacy Policy','content'=>$privacy,'type'=>'1a2b3c4d5e6f7g8h9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'EU Privacy Policy','content'=>$eu_privacy,'type'=>'1a2b3c4d5e6f7g8h9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Cookie Privacy Policy','content'=>$pcp,'type'=>'1a2b3c4d5e6f7g8h9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Earnings Disclaimer','content'=>$earnings,'type'=>'4d5e6f7g8h'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Disclaimer','content'=>$disclaimer,'type'=>'1a2b3c4d5e6f7g8h9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Testimonials Disclosure','content'=>$testimonials,'type'=>'1a2b3c4d5e6f7g8h9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Linking Policy','content'=>$linking,'type'=>'1a2b3c9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Refund-Policy','content'=>$refund,'type'=>'2b3c9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Affiliate Agreement','content'=>$affiliate,'type'=>'3c'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Antispam','content'=>$antispam,'type'=>'3c'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'FTC Statement','content'=>$ftc,'type'=>'4d5e7g'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Medical Disclaimer','content'=>$medical,'type'=>'6f'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Amazon Affiliate','content'=>$amazon,'type'=>'7g'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Double Dart Cookie','content'=>$dart,'type'=>'8h'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'External Links Policy','content'=>$external,'type'=>'4d5e6f7g8h9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Affiliate Disclosure','content'=>$disclosure,'type'=>'4d'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'FB Policy','content'=>$fbpolicy,'type'=>'4d'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'About Us','content'=>$about_us,'type'=>'1a2b3c4d5e6f7g8h9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'DMCA','content'=>$dmca,'type'=>'10j'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'Digital Goods Refund Policy','content'=>$gigitalGoods,'type'=>'2b3c9i'), array('%s','%s','%s'));
                $wpdb->insert($this->template_table_name, array('name'=>'COPPA - Children’s Online Privacy Policy','content'=>$coppa,'type'=>'1a2b3c4d5e'), array('%s','%s','%s'));

                update_option('adl_demo_inserted', true); // to prevent inserting the save data again if user activate the plugin again.
                update_option('demo_data_inserted_by_lp_pro', true); // to prevent inserting the save data again if user activate the plugin again.
            }


            // flush rewrite rules
            $this->plugin_activated();

        }
        

        /**
         * Flush rewrite rules on activation so that our custom post type and its custom url works better.
         * @return void
         */
        public function plugin_activated() {
            flush_rewrite_rules();
        }

        /**
         *  Flush rewrite rules on deactivation
         * @return void
         */
        public function plugin_deactivated() {
            flush_rewrite_rules();
        }

        /**
         * Load all classes from a given directory and store objects to the $this->$objects property
         * @param string $dir The name of the directory where all classes resides
         * @return void
         */
        public function load_classes($dir){
            if (!file_exists($dir)) return;

            $objects = array();

            foreach (scandir($dir) as $file) {
                // if any file(eg.class files) found in the given dir then require it once and then create an object and add it to the objects array.
                if( preg_match( "/.php$/i" , $file ) ) {
                    require_once( $dir . $file );
                    $singleClass = str_replace( ".php", "", $file );
                    $objects[] = new $singleClass; // File name must match Class names in order to dynamically instantiate the class
                }
            }

            if($objects) {
                foreach( $objects as $object )
                    $this->objects[] = $object;
            }
        }

        /**
         * Dynamically calls a method from this class if it is not public or from a subclass
         * @param   String  $name The Name of the Method to invoke on this class or subclass
         * @param   Mixed $args Dynamic list of arguments that will be passed to the method when it is called.
         *
         * @return mixed|bool
         */
        public function __call( $name, $args ){
            if( !is_array($this->objects) ) return false;
            foreach($this->objects as $object){
                if(method_exists($object, $name)){
                    return call_user_func_array(array($object, $name), $args);
                }
            }
            return false;
        }


        /**
         * Initialize the plugin by hooking all actions and filters
         */
        public function init() {
            add_action('admin_init', array($this, 'warn_if_unsupported_wp'));

            // admin hooks and filter
            if ( is_admin() ) {
                //actions
                add_action('plugins_loaded', array($this, 'load_textdomain' ) );
                //filters
                add_filter( 'plugin_action_links_' . ADL_LP_BASE, array($this, 'add_plugin_action_link') );
            }

            // Enables shortcode for Widget
            add_filter('widget_text', 'do_shortcode');



        }

        /**
         * It loads html view
         * @param string $name Name of the view to be loaded
         * @param array $args The array of arguments to be passed to the view
         * @return void
         */
        public function loadView( $name, $args = array() ) {
            global $ADL_LP, $post;
            include(ADL_LP_VIEWS_DIR.$name.'.php');
        }

        /**
         * It includes any files from the themes directory.
         * @param string $name  Name of the file from the Themes directory eg. 'style1/index'
         * @param array $args   Optional Values passed to the views to be used there.
         */
        public function loadTheme( $name, $args = array() ) {
            $name = "themes/{$name}";
            $this->loadView($name, $args);
        }


        /**
         * It adds links to the plugin activation page
         * @param array $links The array of all default links of a plugin
         *
         * @return array The modified array of all links of a plugin
         */
        public function add_plugin_action_link($links) {
            unset($links['edit']); // protect editing the plugin
            $links[] = sprintf( '<a href="%s" title="%s">%s</a>', 'post-new.php?post_type='.ADL_LP_POST_TYPE, 'Add New Legal Pages', __( 'Add New', ADL_LP_TEXTDOMAIN ) );
            $links[] = sprintf( '<a href="%s" title="%s">%s</a>', 'edit.php?post_type='.ADL_LP_POST_TYPE, 'View All Legal Pages', __( 'View All', ADL_LP_TEXTDOMAIN ) );
            return $links;

        }


        /**
         *  It loads the text domain of the plugin
         * @return void
         */
        public function load_textdomain( ){
            load_plugin_textdomain(ADL_LP_TEXTDOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . '/languages/');
        }


        /**
         * It shows a warning to the user if they use older WordPress Version.
         * @return mixed
         */
        public function warn_if_unsupported_wp() {
            if ( $this->check_minimum_required_wp_version() ) {
                $wp_ver = ! empty( $GLOBALS['wp_version'] ) ? $GLOBALS['wp_version'] : '(undefined)';
                ?>
                <div class="error notice is-dismissible"><p>
                        <?php

                        printf( __( ADL_LP_PLUGIN_NAME. 'requires WordPress version %1$s or newer. It appears that you are running %2$s. The plugin may not work properly.', ADL_LP_TEXTDOMAIN ),
                            $this->req_wp_version,
                            esc_html( $wp_ver )
                        );

                        echo '<br>';

                        printf( __( 'Please upgrade your WordPress installation or download latest version from <a href="%s" target="_blank" title="Download Latest WordPress">here</a>.', ADL_LP_TEXTDOMAIN ),
                            'https://wordpress.org/download/'
                        );

                        ?>
                    </p></div>
                <?php

                return false;
            }
            return false;
        }

        /**
         * It checks minimum required version of WordPress we defined in $this->req_wp_version
         * @return mixed
         */
        private function check_minimum_required_wp_version() {
            include( ABSPATH . WPINC . '/version.php' ); // get an unmodified $wp_version
            return ( version_compare( $wp_version, $this->req_wp_version, '<' ) );
        }


    }


endif;


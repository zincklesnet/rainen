<?php

if (!class_exists('ADL_LP_enqueue')):
/**
 * Class ADL_team_enqueue.
 * It enqueue all scripts and styles needed by the plugin
 */
class ADL_LP_enqueue {


    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 999);
        // best hook to enqueue scripts for front-end is 'template_redirect'
        // 'Professional WordPress Plugin Development' by Brad Williams
        add_action('template_redirect', array($this, 'front_end_enqueue_scripts'));
    }


    public function admin_enqueue_scripts($page) {
        global $pagenow, $typenow, $ADL_LP;
        if ( 'admin.php' == $pagenow ) {
          wp_enqueue_style( 'adl-lp-bootstrap', ADL_LP_ADMIN_ASSETS . 'css/bootstrap.min.css', false, ADL_LP_VERSION );
            wp_enqueue_style('adl-tabs', ADL_LP_ADMIN_ASSETS . 'css/tabs.css', array('adl-lp-bootstrap'), ADL_LP_VERSION);
            wp_enqueue_style('adl-main', ADL_LP_ADMIN_ASSETS . 'css/adl-lp-main.css', array('adl-lp-bootstrap', 'adl-tabs'), ADL_LP_VERSION);
            wp_enqueue_script( 'adl-bootstrap-js', ADL_LP_ADMIN_ASSETS . 'js/bootstrap.min.js', array( 'jquery' ), ADL_LP_VERSION, true );

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'adl-lp-main-js', ADL_LP_ADMIN_ASSETS . 'js/adl-lp-main.js', array(
                'jquery',
                'adl-bootstrap-js',
                'wp-color-picker'
            ), ADL_LP_VERSION, true );

            $adl_lp_obj = array(
                'nonceAction' => $ADL_LP->nonceAction(),
                'nonce'       => wp_create_nonce( $ADL_LP->nonceText() ),
                'adminAsset'  => ADL_LP_ADMIN_ASSETS,
            );
            wp_localize_script( 'adl-lp-main-js', 'adl_lp_obj', $adl_lp_obj );
            wp_enqueue_media();

        }

    }


    public function front_end_enqueue_scripts() {

        //styles and scripts for cookie functionality
        wp_register_script('adl-cookie-consent-js', ADL_LP_PUBLIC_ASSETS.'js/cookieconsent.min.js');
        wp_register_style('adl-cookie-style', ADL_LP_PUBLIC_ASSETS.'css/cookieconsent.min.css');








        if ( @get_option('adl_lp_cookie')['show_cookie_warning'] ) { // enqueue cooke related stuff only when user enable cookie warning.
            wp_enqueue_script('adl-cookie-consent-js');
            add_action('wp_print_footer_scripts', array($this, 'print_dynamic_js'));
            wp_enqueue_style('adl-cookie-style');
        }else{
            wp_dequeue_script('adl-cookie-consent-js');
            wp_dequeue_script('adl-cookie-js');
            wp_dequeue_style('adl-cookie-style');
        }
	    if ( @get_option('adl_lp_popup')['disabled_pop_js_css'] ) { // disable style of plugin if the theme already has bootstrap js and css
		    wp_deregister_style( 'bootstrap-modal-style');
		    wp_deregister_script( 'bootstrap-modal-script');
	    }else{
		    // styles and scripts for modal popup functionality
		    wp_register_style( 'bootstrap-modal-style', ADL_LP_PUBLIC_ASSETS.'css/bootstrap-modal.css');
		    wp_register_script( 'bootstrap-modal-script', ADL_LP_PUBLIC_ASSETS.'js/bootstrap-modal.min.js', array('jquery'));
	    }


    }


    public function print_dynamic_js()
    {
        $cookie_settings = get_option('adl_lp_cookie');

        extract($cookie_settings);
        ?>
        <script>
            window.addEventListener("load", function(){
                window.cookieconsent.initialise({
                    "palette": {
                        "popup": {
                            "background": '<?php echo !empty($cookie_message_bg) ? esc_js($cookie_message_bg) : '#000000';?>',
                            "text" : '<?php echo !empty($cookie_message_color) ? esc_js($cookie_message_color) : '#ffffff';?>'
                        },
                        "button": {
                            "background": '<?php echo !empty($cookie_read_more_link) ? esc_js($cookie_read_more_link) : '#f1d600';?>',
                            "text" : '<?php echo !empty($cookie_button_text_color) ? esc_js($cookie_button_text_color) : '#000000';?>'

                        }
                    },
                    // "theme": "edgeless", // classic, wire, block and edgeless. block is the default
                    "theme": '<?php echo !empty($cookie_layout) ? esc_js($cookie_layout) : 'block';?>',
                    "position": '<?php echo !empty($cookie_display_position) ? esc_js($cookie_display_position) : 'bottom';?>', //bottom, bottom-right, bottom-left, top, top-left, top-right, top with static to true.
                     "content": {
                         "message": '<?php echo !empty($cookie_message) ? esc_js($cookie_message) : esc_js('This website uses cookies to ensure you get the best experience on our website'); ?>',
                         "href": '<?php echo !empty($cookie_policy_link) ? esc_url($cookie_policy_link) : esc_url('http://cookiesandyou.com'); ?>', // enter custom cookie policy url
                         "dismiss": '<?php echo !empty($cookie_button) ? esc_js($cookie_button) : 'Got it!';?>'
                     }
                })});
        </script>

    <?php

    }
}



endif;
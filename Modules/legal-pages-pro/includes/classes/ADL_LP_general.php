<?php
if ( ! defined('ABSPATH') ) { die( ADL_LP_ALERT_MSG ); }
if(!class_exists('ADL_LP_general')):
class ADL_LP_general {


    public function __construct(){

        add_action('admin_menu', array($this, 'show_admin_menu'));
    }

    public function show_admin_menu() {
        add_menu_page(
            __('Legal Page Settings', ADL_LP_TEXTDOMAIN),
            __('Legal Pages', ADL_LP_TEXTDOMAIN),
            'manage_options',
            'adl-legal-pages',
            array($this, 'general_setting'),
            'dashicons-welcome-add-page',
            20
            );
        add_submenu_page('adl-legal-pages',
            __('Create Legal Page', ADL_LP_TEXTDOMAIN),
            __('Create Legal Page', ADL_LP_TEXTDOMAIN),
            'manage_options',
            'adl-legal-pages&tab=createLegalPage',
            array($this, 'show_create_legal_page')
        );


        add_submenu_page('adl-legal-pages',
            __('All Legal Pages', ADL_LP_TEXTDOMAIN),
            __('All Legal Pages', ADL_LP_TEXTDOMAIN),
            'manage_options',
            'adl-legal-pages&tab=allPages',
            array($this, 'show_create_legal_page')
        );

        add_submenu_page('adl-legal-pages',
            __('Create | Edit Legal Page Template', ADL_LP_TEXTDOMAIN),
            __('Create Legal Page Template', ADL_LP_TEXTDOMAIN),
            'manage_options',
            'adl-create-template',
            array($this, 'show_create_template')
        );
        add_submenu_page('adl-legal-pages',
            __('All Templates', ADL_LP_TEXTDOMAIN),
            __('All Templates', ADL_LP_TEXTDOMAIN),
            'manage_options',
            'adl-legal-pages&tab=editTemplates',
            array($this, 'show_create_legal_page')
        );
	    add_submenu_page('adl-legal-pages',
		    __('Create Popup', ADL_LP_TEXTDOMAIN),
		    __('Create Popup', ADL_LP_TEXTDOMAIN),
		    'manage_options',
		    'adl-create-popup',
		    array($this, 'show_create_popup')
	    );
	    add_submenu_page('adl-legal-pages',
		    __('All Popups', ADL_LP_TEXTDOMAIN),
		    __('All Popups', ADL_LP_TEXTDOMAIN),
		    'manage_options',
		    'adl-all-popups',
		    array($this, 'show_all_popups')
	    );

        add_submenu_page('adl-legal-pages',
            __('Get Support', ADL_LP_TEXTDOMAIN),
            __('Get Support', ADL_LP_TEXTDOMAIN),
            'manage_options',
            'adl-legal-pages&tab=support',
            array($this, 'show_create_legal_page')
        );

    }

    // we need this empty function.
    public function show_create_legal_page(  ) {

    }


	public function show_all_popups(  ) {
		global $ADL_LP, $wpdb;

		//@TODO; add pagination later
		$sql = 'SELECT * FROM '.$ADL_LP->popups_table_name .' LIMIT 40';
		$popups = $wpdb->get_results($sql);
		$ADL_LP->loadView('list-popups', array('popups'=>$popups));

	}

    public function show_create_popup(  ) {
	    global $ADL_LP;
	    $ADL_LP->loadView('create-edit-popup');
    }




    public function show_create_template(  ) {
        global $ADL_LP, $wpdb;
        $sql1 = 'SELECT * FROM '.$ADL_LP->template_table_name .' LIMIT 40';
        $adl_lp_templates = $wpdb->get_results($sql1); // get all legal templates
        $ADL_LP->loadView('settings/tab-content/create-edit-templates', $adl_lp_templates);
     }


    public function general_setting() {
        global $ADL_LP, $wpdb;
        $sql1 = array(
            'post_type'  => 'page',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key'     => 'is_adl_legal_page',
                    'value'   => true,
                    'compare' => '=',
                ),

            ),
        );
        $sql2 = 'SELECT * FROM '.$ADL_LP->template_table_name .' LIMIT 40';
        $adl_legal_pages = new WP_Query( $sql1 ); // get all legal pages
        $adl_lp_templates = $wpdb->get_results($sql2); // get all legal templates
        $data = array(
                'adl_legal_pages' => $adl_legal_pages,
                'adl_lp_templates' => $adl_lp_templates,
        );
        // if terms already accepted then show the setting else tell the users to accept terms
        if ( get_option('adl_lp_accept_term') ) {
            $ADL_LP->loadView('settings/general', $data);

            //update_option('adl_lp_accept_term', 0);// test Disclaimer page uncommenting this.
        }else {
            $this->acceptTermsAndCondition();
        }


    }


    public function acceptTermsAndCondition() {
        global $ADL_LP;
        // set adl_lp_accept_term when user use the plugin for the first time and them load the general  setting. else show the terms page.
        if ( isset($_POST['adl_lp_submit']) && 'Accept' == $_POST['adl_lp_submit'] && isset($_POST['adl_accept_terms'])) {
            update_option('adl_lp_accept_term', $_POST['adl_accept_terms']);
            $this->general_setting();
        }else{
            $ADL_LP->loadView('terms');
        }
    }

}

endif;
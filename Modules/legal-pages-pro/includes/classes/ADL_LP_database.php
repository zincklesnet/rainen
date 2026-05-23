<?php
defined('ABSPATH') || die('You can not access this file directly');

if ( !class_exists('ADL_LP_database') ):
class ADL_LP_database {

	/**
	 * ADL_LP_database constructor.
	 */
	public function __construct() {
        add_action( 'pre_get_posts', array($this, 'remove_legal_page_from_search') );
    }

	/**
	 * It removes the legal pages from search result if the hiding option is enabled in the settings
	 * @param WP_Query $query
	 * @return void
	 */
	function remove_legal_page_from_search(WP_Query $query ) {
        if ( get_option('adl_lp_misc')['hide_lp_in_search'] ) { // remove legal pages from search.
            if ( ! $query->is_admin && $query->is_search && $query->is_main_query() ) {
                $query->set( 'post__not_in', $this->get_ids() );
            }
        }

    }

	/**
	 * It returns the ids of all the legal pages
	 * @return array
	 */
	public function get_ids(  ) {
        $ids = [];
        foreach ( $this->get_lp_pages()->posts as $post) { $ids[] = $post->ID; }
        return $ids;
    }


	/**
	 * @param int $limit
	 *
	 * @return WP_Query
	 */
	public function get_lp_pages( $limit = -1 ) {
        return new WP_Query( array(
                            'post_type'  => 'page',
                            'posts_per_page' => (!empty($limit)) ? $limit : -1 ,
                            'meta_query' => array(
                                array(
                                    'key'     => 'is_adl_legal_page',
                                    'value'   => true,
                                    'compare' => '=',
                                ),
                            ),
                        )
                );

    }

	/**
	 *It fetches all the legal page templates from the database and returns them.
	 * @return object it returns all the legal pages templates in an object
	 */
	public function get_lp_templates(  ) {
		global $wpdb, $ADL_LP ;
		return $wpdb->get_results("SELECT * FROM {$ADL_LP->template_table_name}"); // get the template from the database to output
    }

	/**
	 *Get a single legal page template and return it
	 * @param int $id The id of the template to get from the database
	 * @return string|null It returns a legal page template on success and null on failure
	 */
	public function get_lp_template( $id  ) {
			global $wpdb, $ADL_LP ;
		return $wpdb->get_row($wpdb->prepare( 'SELECT * FROM '.$ADL_LP->template_table_name .' WHERE id=%d', $id)); // get the template from the database to output
	}


	/**
	 *Get a single popup and return it
	 * @param int $id The id of the popup to get from the database
	 * @return string|null It returns a popup on success and null on failure
	 */
	public function get_popup( $id  ) {
		global $wpdb, $ADL_LP ;
		return $wpdb->get_row($wpdb->prepare( 'SELECT * FROM '.$ADL_LP->popups_table_name .' WHERE id=%d', $id)); // get the template from the database to output
	}
	/**
	 * It replaces all the shortcodes of this plugin with its value in the given content
	 * @param string $content the content where all the default shortcodes of this plugin should be replaced
	 *@return string It returns the modified content where all shortcodes have been replaced by their content
	 */
	public function replace_shortcode_with_content( $content ) {
		// get settings from database, general and social
		$adl_lp_general = get_option('adl_lp_general');
		$adl_lp_social = get_option('adl_lp_social');
		// prepare social fields array of links
		$fb= $adl_lp_social['facebookUrl'];
		$gp= $adl_lp_social['googlePlusUrl'];
		$li= $adl_lp_social['linkedInUrl'];
		$tt= $adl_lp_social['twitterUrl'];
		$social_array = array(
			"<a href='{$fb}' target='_blank'> Find Us on Facebook</a>",
			"<a href='{$gp}' target='_blank'> Connect us on Google Plus</a>",
			"<a href='{$li}' target='_blank'> Connect us on LinkedIn</a>",
			"<a href='{$tt}' target='_blank'> Follow Us on Twitter</a>",
		);
		//Now prepare the general shortcodes array and social shortcodes array to replace those shortcode with appropriate value
		$adl_lp_general_find = array('[siteUrl]', '[siteName]','[businessNiche]','[phoneNumber]','[emailAddress]','[streetName]','[countryName]','[cityName]','[stateName]','[zipCode]','[mailingAddress]',);

		$adl_lp_social_find = array('[facebookUrl]','[googlePlusUrl]','[linkedInUrl]','[twitterUrl]',);



		$mo_content = str_replace($adl_lp_general_find, $adl_lp_general, $content);
		$mo_content = str_replace($adl_lp_social_find, $social_array, $mo_content); // modified data.
		return $mo_content;





	}

}

endif;
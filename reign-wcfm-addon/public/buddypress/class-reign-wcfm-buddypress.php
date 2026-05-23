<?php

/**
 * The class responsiale for buddypress integration with wcfm.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/public
 */
// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class REIGN_WCFM_Buddypress {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	public function reign_wcfm_add_product_creation_activity( $new_status, $old_status, $post ) {

		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) && function_exists( 'bp_activity_add' ) ) {
			
			if ( ( 'publish' === $new_status && 'publish' !== $old_status ) && 'product' === $post->post_type ) {
				global $bp, $wbtm_reign_settings;

				if ( isset( $wbtm_reign_settings['wcfm_option']['product_activity'] ) && 'on' === $wbtm_reign_settings['wcfm_option']['product_activity'] ) {

					$product_id        = $post->ID;
					$user_id           = $post->post_author;
					$user_link         = bp_core_get_userlink( $user_id );
					$poduct_title      = get_the_title( $product_id );
					$product_link      = get_permalink( $product_id );
					$product_link_html = '<a href="' . esc_url( $product_link ) . '">' . $poduct_title . '</a>';
					$action            = apply_filters(
						'reirn_wcfm_format_activity_action_product_create',
						sprintf(
							// Translators: %s
							__( '%2$s added a new product ', 'reign-wcfm-addon' ),
							$product_link_html,
							$user_link
						)
					);

					$args = array(
						'action'            => $action,
						'content'           => $product_link,
						'component'         => $bp->members->id,
						'type'              => 'wcfm_product_create',
						'user_id'           => $user_id,
						'item_id'           => $product_id,
						'secondary_item_id' => $product_id,
						'hide_sitewide'     => false,
						'is_spam'           => false,
						'privacy'           => 'public',
						'error_type'        => 'bool',
					);

					bp_activity_add( $args );
				}
			}
		}
	}


	/**
	 * Add buddyboss activity on new review on product.
	 *
	 * @param int    $comment_ID
	 * @param string $commentdata
	 *
	 * @since 1.1.0
	 */
	public function reign_wcfm_product_review_approved( $comment_ID, $commentdata ) {
		global $bp, $wbtm_reign_settings;
		
		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) && function_exists( 'bp_activity_add' ) ) {
			
			if ( isset( $wbtm_reign_settings['wcfm_option']['review_activity'] ) && 'on' === $wbtm_reign_settings['wcfm_option']['review_activity'] && is_user_logged_in() ) {

				$comment_obj = get_comment( $comment_ID );
				$product_id  = $comment_obj->comment_post_ID;
				$post_type   = get_post_type( $product_id );
				$review_link = get_comment_link( $comment_ID );
	
				if ( 'product' === $post_type && $commentdata ) {
	
					$user_link         = bp_core_get_userlink( $comment_obj->user_id );
					$product_title     = get_the_title( $product_id );
					$product_link      = get_permalink( $product_id );
					$product_link_html = '<a href="' . esc_url( $product_link ) . '">' . $product_title . '</a>';
					$args              = array(
						'type'              => 'wcfm_product_review',
						'action'            => apply_filters(
							'reign_wcfm_format_activity_action_product_review',
							sprintf(
								// Translators: %s
								__( '%2$s wrote a review ', 'reign-wcfm-addon' ),
								$product_link_html,
								$user_link
							)
						),
						'item_id'           => $product_id,
						'secondary_item_id' => $comment_ID,
						'component'         => $bp->members->id,
						'content'           => $review_link,
					);
					bp_activity_add( $args );
				}
			}
		}
	}

	/**
	 * Record product review.
	 *
	 * @global type $bp
	 * @param type $comment_ID
	 * @param type $comment_status
	 *
	 * @since 1.1.0
	 */
	public function reign_wcfm_product_review( $comment_ID, $comment_status ) {
		global $bp, $wbtm_reign_settings;

		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) && function_exists( 'bp_activity_add' ) ) {

			if ( isset( $wbtm_reign_settings['wcfm_option']['review_activity'] ) && 'on' === $wbtm_reign_settings['wcfm_option']['review_activity'] && is_user_logged_in() ) {
				$comment_obj = get_comment( $comment_ID );
				$post_id     = $comment_obj->comment_post_ID;
				$post_type   = get_post_type( $post_id );
				$review_link = get_comment_link( $comment_ID );
				if ( 'product' === $post_type && 'approve' === $comment_status ) {
					$user_link         = bp_core_get_userlink( $comment_obj->user_id );
					$product_title     = get_the_title( $post_id );
					$product_link      = get_permalink( $post_id );
					$product_link_html = '<a href="' . esc_url( $product_link ) . '">' . $product_title . '</a>';
					$args              = array(
						'type'              => 'wcfm_product_review',
						'action'            =>
							sprintf(
								// Translators: %s is the
								__(
									'%2$s wrote a review ',
									'reign-wcfm-addon'
								),
								$user_link,
								$product_link_html
							),
						'user_id'           => $comment_obj->user_id,
						'item_id'           => $post_id,
						'secondary_item_id' => $comment_ID,
						'component'         => $bp->members->id,
						'content'           => $review_link,
					);
					bp_activity_add( $args );
				}
			}
		}
	}

}

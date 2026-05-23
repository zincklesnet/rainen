<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CEFWJC_General_Hooks' ) ) {

	class CEFWJC_General_Hooks {

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_resources' ) );
		}

		/**
		 * Enqueue scripts and styles used on the front end.
		 *
		 * @return void
		 */
		public function enqueue_resources() {
			if ( ! $this->should_enqueue_resources() ) {
				return;
			}

			wp_enqueue_style( 'cefwjc-emoji', plugins_url( 'css/emoji.min.css', __FILE__ ), array(), CEFWJC_PLUGIN_VERSION );
			wp_enqueue_style( 'cefwjc-front', plugins_url( 'css/cefwjc-front.css', __FILE__ ), array( 'cefwjc-emoji' ), CEFWJC_PLUGIN_VERSION );

			wp_enqueue_script( 'cefwjc-emoji', plugins_url( 'js/emojionearea.js', __FILE__ ), array( 'jquery' ), CEFWJC_PLUGIN_VERSION, true );
			wp_enqueue_script( 'cefwjc-front', plugins_url( 'js/cefwjc-front.js', __FILE__ ), array( 'jquery', 'cefwjc-emoji' ), CEFWJC_PLUGIN_VERSION, true );

			$settings             = CEFWJC_Plugin::get_frontend_settings();
			$settings['selector'] = (string) apply_filters( 'cefwjc_comment_field_selector', '.comment-form-comment textarea, textarea#comment' );

			wp_localize_script( 'cefwjc-front', 'emojisData', $settings );
		}

		/**
		 * Determine whether assets should load on the current request.
		 *
		 * @return bool
		 */
		private function should_enqueue_resources() {
			$should_load = is_singular() && comments_open() && post_type_supports( get_post_type(), 'comments' );

			return (bool) apply_filters( 'cefwjc_should_load_assets', $should_load );
		}
	}
}

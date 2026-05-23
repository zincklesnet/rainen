<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CEFWJC_Plugin' ) ) {
	final class CEFWJC_Plugin {
		public const OPTION_GROUP = 'cefwjc_comment_emojis_options';

		/**
		 * Register plugin defaults on activation.
		 *
		 * @return void
		 */
		public static function activate() {
			foreach ( self::get_defaults() as $option_name => $default_value ) {
				add_option( $option_name, $default_value );
			}
		}

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_filter( 'plugin_action_links_' . CEFWJC_PLUGIN_BASE, array( $this, 'add_plugin_action_links' ) );

			if ( is_admin() ) {
				require_once CEFWJC_PLUGIN_PATH . 'admin/cefwjc-setting.php';
				new CEFWJC_COMMENT_SETTING();
				return;
			}

			require_once CEFWJC_PLUGIN_PATH . 'public/cefwjc-rendering.php';
			new CEFWJC_General_Hooks();
		}

		/**
		 * Load translations.
		 *
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'comment-emojis-for-wp', false, dirname( CEFWJC_PLUGIN_BASE ) . '/languages' );
		}

		/**
		 * Return the plugin donation URL.
		 *
		 * @return string
		 */
		public static function get_donate_url() {
			return 'https://paypal.me/chopdajayesh';
		}

		/**
		 * Add a donate link to the plugin action links row.
		 *
		 * @param array<int, string> $links Existing action links.
		 * @return array<int, string>
		 */
		public function add_plugin_action_links( $links ) {
			$links[] = sprintf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
				esc_url( self::get_donate_url() ),
				esc_html__( 'Donate', 'comment-emojis-for-wp' )
			);

			return $links;
		}

		/**
		 * Return default option values.
		 *
		 * @return array<string, string>
		 */
		public static function get_defaults() {
			return array(
				'cefwjc_position_emojis'  => 'bottom',
				'cefwjc_filter_position' => 'top',
				'cefwjc_skintone'        => 'no',
				'cefwjc_skintone_style'  => 'bullet',
				'cefwjc_search'          => 'no',
				'cefwjc_search_position' => 'top',
				'cefwjc_recent_emojis'   => 'no',
			);
		}

		/**
		 * Return a single option with fallback to defaults.
		 *
		 * @param string $option_name Option name.
		 * @return string
		 */
		public static function get_option( $option_name ) {
			$defaults = self::get_defaults();

			if ( ! array_key_exists( $option_name, $defaults ) ) {
				return '';
			}

			return (string) get_option( $option_name, $defaults[ $option_name ] );
		}

		/**
		 * Return settings passed to the front-end script.
		 *
		 * @return array<string, string>
		 */
		public static function get_frontend_settings() {
			return array(
				'position_emojis' => self::get_option( 'cefwjc_position_emojis' ),
				'filter_position' => self::get_option( 'cefwjc_filter_position' ),
				'skintone'        => self::get_option( 'cefwjc_skintone' ),
				'skintone_style'  => self::get_option( 'cefwjc_skintone_style' ),
				'search'          => self::get_option( 'cefwjc_search' ),
				'search_position' => self::get_option( 'cefwjc_search_position' ),
				'recent_emojis'   => self::get_option( 'cefwjc_recent_emojis' ),
			);
		}

		/**
		 * Sanitize toggle settings that store yes/no.
		 *
		 * @param mixed $value Submitted value.
		 * @return string
		 */
		public static function sanitize_toggle( $value ) {
			return 'yes' === $value ? 'yes' : 'no';
		}

		/**
		 * Sanitize a setting against allowed values.
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $value Submitted value.
		 * @return string
		 */
		public static function sanitize_choice( $option_name, $value ) {
			$allowed = array(
				'cefwjc_position_emojis'  => array( 'top', 'right', 'bottom' ),
				'cefwjc_filter_position' => array( 'top', 'bottom' ),
				'cefwjc_skintone_style'  => array( 'bullet', 'radio', 'square', 'checkbox' ),
				'cefwjc_search_position' => array( 'top', 'bottom' ),
			);

			$defaults = self::get_defaults();
			$value    = is_string( $value ) ? sanitize_key( $value ) : '';

			if ( isset( $allowed[ $option_name ] ) && in_array( $value, $allowed[ $option_name ], true ) ) {
				return $value;
			}

			return $defaults[ $option_name ];
		}
	}
}

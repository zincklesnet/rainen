<?php
/**
 * Reign companion plugin installer.
 *
 * Replaces TGM Plugin Activation with a one-click installer for the Wbcom
 * plugin family. It speaks the same EDD delivery channel the companions use for
 * updates: POST the store with edd_action=activate_license to authorize this
 * domain, then edd_action=get_version to get a signed package URL, and hand that
 * to WP core's Plugin_Upgrader. No wordpress.org plugins are installed here.
 *
 * Each catalog entry ships the item_id + free distribution key baked into that
 * plugin's own EDD SL SDK, so a one-click free install uses the exact channel
 * the plugin already trusts. The installer only ever talks to wbcomdesigns.com.
 *
 * Security: install_plugins capability + nonce required; only catalog slugs are
 * installable; the download URL is resolved through EDD, never client input; the
 * package host is locked to wbcomdesigns.com.
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Reign_Plugin_Installer' ) ) :

	/**
	 * @class Reign_Plugin_Installer
	 */
	class Reign_Plugin_Installer {

		/**
		 * The only host the installer will ever talk to or download from.
		 */
		const STORE_URL = 'https://wbcomdesigns.com';

		/**
		 * HTTP timeout (seconds) for store calls.
		 */
		const TIMEOUT = 30;

		/**
		 * Single instance.
		 *
		 * @var Reign_Plugin_Installer
		 */
		protected static $instance = null;

		/**
		 * Main instance.
		 *
		 * @return Reign_Plugin_Installer
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'wp_ajax_reign_install_plugin', array( $this, 'ajax_install_plugin' ) );
		}

		/**
		 * The Wbcom plugin family Reign can install one-click.
		 *
		 * Each entry:
		 *   label          string Display name.
		 *   item_id        int    EDD store product id.
		 *   key            string Free distribution license key.
		 *   basename       string plugin_basename once installed (folder/file.php).
		 *   detect         callable Returns true when the plugin's capability is live.
		 *   store_url      string Product page (wbcomdesigns.com only).
		 *   license_option string Optional. When set, the installer also stores the
		 *                         license under this option so the plugin self-updates.
		 *
		 * @return array<string, array<string, mixed>>
		 */
		public function catalog() {
			$catalog = array(
				'wbcom-essential' => array(
					'label'          => __( 'Wbcom Essential', 'reign' ),
					'item_id'        => 1545975,
					'key'            => '1cf3c1cf9ea987039b97194ae9c64755',
					'basename'       => 'wbcom-essential/loader.php',
					'detect'         => static function () {
						return function_exists( 'wbcom_essential' ) || defined( 'WBCOM_ESSENTIAL_VERSION' );
					},
					'store_url'      => 'https://wbcomdesigns.com/downloads/wbcom-essential/',
					'license_option' => 'wbcom_essential_license_key',
				),
				'wpmediaverse'    => array(
					'label'     => __( 'MediaVerse', 'reign' ),
					'item_id'   => 1660826,
					'key'       => 'wbcomfree7a9c2e5d1f8b4c6a3e0d9b2f7c1a8e44',
					'basename'  => 'wpmediaverse/wpmediaverse.php',
					'detect'    => static function () {
						return class_exists( '\\WPMediaVerse\\Core\\Plugin' ) || defined( 'MVS_VERSION' );
					},
					'store_url' => 'https://wbcomdesigns.com/downloads/wpmediaverse/',
				),
				'jetonomy'        => array(
					'label'     => __( 'Jetonomy', 'reign' ),
					'item_id'   => 1660320,
					'key'       => 'wbcomfreec7e2a9b45d8f1c3e6a0b9d2f7c4e8a11',
					'basename'  => 'jetonomy/jetonomy.php',
					'detect'    => static function () {
						return class_exists( '\\Jetonomy\\Plugin' ) || function_exists( 'jetonomy' );
					},
					'store_url' => 'https://wbcomdesigns.com/downloads/jetonomy/',
				),
				'wb-gamification' => array(
					'label'     => __( 'WB Gamification', 'reign' ),
					'item_id'   => 1662147,
					'key'       => 'wbcomfree6e2a9c1d7b4f3c8a0e5d9b2f1a7c6e11',
					'basename'  => 'wb-gamification/wb-gamification.php',
					'detect'    => static function () {
						return function_exists( 'wb_gam_submit_event' ) || defined( 'WB_GAM_VERSION' );
					},
					'store_url' => 'https://wbcomdesigns.com/downloads/wb-gamification/',
				),
				'wp-career-board' => array(
					'label'     => __( 'WP Career Board', 'reign' ),
					'item_id'   => 1659888,
					'key'       => 'wbcomfree5b8c1e7a9d3f2a4c6e0d1b7f9c2a6e00',
					'basename'  => 'wp-career-board/wp-career-board.php',
					'detect'    => static function () {
						return defined( 'WCB_VERSION' ) || class_exists( '\\WCB\\Core\\Plugin' );
					},
					'store_url' => 'https://wbcomdesigns.com/downloads/wp-career-board/',
				),
				'learnomy'        => array(
					'label'     => __( 'Learnomy', 'reign' ),
					'item_id'   => 1662698,
					'key'       => 'wbcomfree5d8a1f3c7b2e9a4c6f0d1e8b3c9a7f25',
					'basename'  => 'learnomy/learnomy.php',
					'detect'    => static function () {
						return defined( 'LEARNOMY_VERSION' ) || function_exists( 'learnomy' );
					},
					'store_url' => 'https://wbcomdesigns.com/downloads/learnomy/',
				),
				'wb-listora'      => array(
					'label'     => __( 'Listora', 'reign' ),
					'item_id'   => 1662779,
					'key'       => 'wbcomfree8a5d1c7e3f2b9a4c6e0d1b7f9c2a6e55',
					'basename'  => 'wb-listora/wb-listora.php',
					'detect'    => static function () {
						return defined( 'WB_LISTORA_VERSION' );
					},
					'store_url' => 'https://wbcomdesigns.com/downloads/wb-listora/',
				),
			);

			/**
			 * Filter the Reign one-click install catalog.
			 *
			 * @param array $catalog Slug => entry.
			 */
			return (array) apply_filters( 'reign_install_catalog', $catalog );
		}

		/**
		 * Get one catalog entry, or null.
		 *
		 * @param string $slug Plugin slug.
		 * @return array<string, mixed>|null
		 */
		public function get( $slug ) {
			$catalog = $this->catalog();
			return isset( $catalog[ $slug ] ) ? $catalog[ $slug ] : null;
		}

		/**
		 * Whether the plugin's capability is live.
		 *
		 * @param string $slug Plugin slug.
		 * @return bool
		 */
		public function is_active( $slug ) {
			$entry = $this->get( $slug );
			if ( null === $entry || ! is_callable( $entry['detect'] ) ) {
				return false;
			}
			return (bool) call_user_func( $entry['detect'] );
		}

		/**
		 * Whether the plugin file exists on disk (installed, maybe inactive).
		 *
		 * @param string $slug Plugin slug.
		 * @return bool
		 */
		public function is_installed( $slug ) {
			$entry = $this->get( $slug );
			if ( null === $entry || empty( $entry['basename'] ) ) {
				return false;
			}
			return file_exists( trailingslashit( WP_PLUGIN_DIR ) . $entry['basename'] );
		}

		/**
		 * Lifecycle state for the UI.
		 *
		 * @param string $slug Plugin slug.
		 * @return string 'active' | 'inactive' | 'not_installed' | 'unknown'.
		 */
		public function status( $slug ) {
			if ( null === $this->get( $slug ) ) {
				return 'unknown';
			}
			if ( $this->is_active( $slug ) ) {
				return 'active';
			}
			if ( $this->is_installed( $slug ) ) {
				return 'inactive';
			}
			return 'not_installed';
		}

		/**
		 * AJAX: install (and activate) a catalog plugin.
		 *
		 * @return void
		 */
		public function ajax_install_plugin() {
			check_ajax_referer( 'reign_install_plugin', 'nonce' );

			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( array( 'message' => __( 'You do not have permission to install plugins.', 'reign' ) ), 403 );
			}

			$slug  = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
			$entry = $this->get( $slug );
			if ( null === $entry ) {
				wp_send_json_error( array( 'message' => __( 'Unknown plugin.', 'reign' ) ), 404 );
			}

			$result = $this->install( $slug );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}

			wp_send_json_success(
				array(
					'message' => sprintf(
						/* translators: %s: plugin name. */
						__( '%s installed and activated.', 'reign' ),
						$entry['label']
					),
					'status'  => $this->status( $slug ),
				)
			);
		}

		/**
		 * Install (and activate) a plugin from the catalog.
		 *
		 * @param string $slug Plugin slug (must be in the catalog).
		 * @return true|WP_Error
		 */
		public function install( $slug ) {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return new WP_Error( 'reign_cap', __( 'You do not have permission to install plugins.', 'reign' ) );
			}

			$entry = $this->get( $slug );
			if ( null === $entry ) {
				return new WP_Error( 'reign_unknown_plugin', __( 'Unknown plugin.', 'reign' ) );
			}

			// Already live — nothing to do.
			if ( $this->is_active( $slug ) ) {
				return true;
			}

			$item_id  = (int) $entry['item_id'];
			$key      = (string) $entry['key'];
			$basename = (string) $entry['basename'];
			if ( $item_id <= 0 || '' === $key ) {
				return new WP_Error( 'reign_no_item', __( 'This plugin cannot be installed automatically. Visit the store.', 'reign' ) );
			}

			// Installed but inactive → just activate it, no store call, never re-download.
			if ( '' !== $basename && file_exists( trailingslashit( WP_PLUGIN_DIR ) . $basename ) ) {
				return $this->activate( $basename );
			}

			// Fresh install. EDD authorizes the download only after the license is
			// activated for this domain, so activate first and surface the store's
			// reason on failure.
			$activation = $this->activate_license( $item_id, $key );
			if ( is_wp_error( $activation ) ) {
				return $activation;
			}

			// Persist the license for plugins whose own SDK uses a stored option
			// (e.g. Wbcom Essential), so they keep self-updating after install.
			if ( ! empty( $entry['license_option'] ) && is_object( $activation ) ) {
				update_option( $entry['license_option'], $key );
				update_option( $entry['license_option'] . '_license', $activation );
			}

			$package = $this->resolve_package_url( $item_id, $key );
			if ( is_wp_error( $package ) ) {
				return $package;
			}

			$installed = $this->install_package( $package );
			if ( is_wp_error( $installed ) ) {
				return $installed;
			}

			return $this->activate( '' !== $basename ? $basename : (string) $installed );
		}

		/**
		 * Activate the free license for this domain. EDD authorizes the download
		 * only after this. Returns the decoded license object on success.
		 *
		 * @param int    $item_id Store product id.
		 * @param string $key     Free distribution key.
		 * @return object|WP_Error
		 */
		private function activate_license( $item_id, $key ) {
			$response = wp_remote_post(
				self::STORE_URL,
				array(
					'timeout' => self::TIMEOUT,
					'body'    => array(
						'edd_action'  => 'activate_license',
						'item_id'     => $item_id,
						'license'     => $key,
						'url'         => home_url(),
						'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
					),
				)
			);

			if ( is_wp_error( $response ) ) {
				return new WP_Error( 'reign_store_unreachable', __( 'Could not reach the store to activate the license. Please try again.', 'reign' ) );
			}

			$data = json_decode( (string) wp_remote_retrieve_body( $response ) );
			if ( ! is_object( $data ) ) {
				return new WP_Error( 'reign_store_bad_response', __( 'The store returned an unexpected response while activating the license.', 'reign' ) );
			}

			$status = isset( $data->license ) ? (string) $data->license : '';
			if ( in_array( $status, array( 'valid', 'active' ), true ) || ( 'invalid' === $status && ! empty( $data->success ) ) ) {
				return $data;
			}

			$reason = isset( $data->error ) ? (string) $data->error : ( '' !== $status ? $status : 'unknown' );
			return new WP_Error(
				'reign_license_activation_failed',
				sprintf(
					/* translators: %s: the store's activation error reason. */
					__( 'The store would not activate this free license for your site (reason: %s). This is a store-side license configuration issue.', 'reign' ),
					$reason
				)
			);
		}

		/**
		 * Ask the store for the signed package URL for an item.
		 *
		 * @param int    $item_id Store product id.
		 * @param string $key     Free distribution key.
		 * @return string|WP_Error
		 */
		private function resolve_package_url( $item_id, $key ) {
			$response = wp_remote_post(
				self::STORE_URL,
				array(
					'timeout' => self::TIMEOUT,
					'body'    => array(
						'edd_action' => 'get_version',
						'item_id'    => $item_id,
						'license'    => $key,
						'url'        => home_url(),
					),
				)
			);

			if ( is_wp_error( $response ) ) {
				return new WP_Error( 'reign_store_unreachable', __( 'Could not reach the store. Please try again.', 'reign' ) );
			}

			$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
			if ( ! is_array( $body ) ) {
				return new WP_Error( 'reign_store_bad_response', __( 'The store returned an unexpected response.', 'reign' ) );
			}

			$package = '';
			if ( ! empty( $body['download_link'] ) ) {
				$package = (string) $body['download_link'];
			} elseif ( ! empty( $body['package'] ) ) {
				$package = (string) $body['package'];
			}
			if ( '' === $package ) {
				return new WP_Error( 'reign_no_package', __( 'The store did not return a download for this plugin.', 'reign' ) );
			}

			// Lock the download to the store host — never follow a redirect off-domain.
			$host = (string) wp_parse_url( $package, PHP_URL_HOST );
			if ( '' === $host || ! ( 'wbcomdesigns.com' === $host || $this->str_ends_with( $host, '.wbcomdesigns.com' ) ) ) {
				return new WP_Error( 'reign_bad_package_host', __( 'The download URL was not on the trusted store host.', 'reign' ) );
			}

			return $package;
		}

		/**
		 * Download + unpack a plugin zip via WP core's Plugin_Upgrader.
		 *
		 * @param string $package Signed package URL.
		 * @return string|WP_Error Installed plugin basename/destination, or WP_Error.
		 */
		private function install_package( $package ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/misc.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

			$creds = request_filesystem_credentials( '', '', false, false, null );
			if ( false === $creds || ! WP_Filesystem( $creds ) ) {
				return new WP_Error( 'reign_fs', __( 'WordPress needs filesystem access to install plugins. Configure direct file access or install from the Plugins screen.', 'reign' ) );
			}

			$skin     = new \WP_Ajax_Upgrader_Skin();
			$upgrader = new \Plugin_Upgrader( $skin );
			$result   = $upgrader->install( $package );

			if ( is_wp_error( $result ) ) {
				// WP's generic "download_failed" hides WHY. Probe the package once so
				// the message carries the store's real reason (e.g. a 401).
				if ( 'download_failed' === $result->get_error_code() ) {
					$probe  = wp_remote_get( $package, array( 'timeout' => self::TIMEOUT ) );
					$code   = is_wp_error( $probe ) ? 0 : (int) wp_remote_retrieve_response_code( $probe );
					$reason = is_wp_error( $probe ) ? $probe->get_error_message() : trim( wp_strip_all_tags( (string) wp_remote_retrieve_body( $probe ) ) );
					if ( $code >= 400 ) {
						return new WP_Error(
							'reign_download_rejected',
							sprintf(
								/* translators: 1: HTTP status, 2: store reason text. */
								__( 'The store rejected the download (HTTP %1$d: %2$s). This is a store-side license/entitlement issue.', 'reign' ),
								$code,
								'' !== $reason ? mb_substr( $reason, 0, 120 ) : __( 'no reason given', 'reign' )
							)
						);
					}
				}
				return $result;
			}
			if ( true !== $result ) {
				$errors = $skin->get_errors();
				if ( is_wp_error( $errors ) && $errors->has_errors() ) {
					return $errors;
				}
				return new WP_Error( 'reign_install_failed', __( 'The plugin could not be installed.', 'reign' ) );
			}

			return (string) $upgrader->plugin_info();
		}

		/**
		 * Activate an installed plugin by basename.
		 *
		 * @param string $basename e.g. "jetonomy/jetonomy.php".
		 * @return true|WP_Error
		 */
		private function activate( $basename ) {
			if ( '' === $basename ) {
				return new WP_Error( 'reign_activate', __( 'Installed, but the plugin could not be activated automatically. Activate it from the Plugins screen.', 'reign' ) );
			}
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$activated = activate_plugin( $basename );
			if ( is_wp_error( $activated ) ) {
				return $activated;
			}
			return true;
		}

		/**
		 * str_ends_with() shim for PHP < 8.0.
		 *
		 * @param string $haystack Haystack.
		 * @param string $needle   Needle.
		 * @return bool
		 */
		private function str_ends_with( $haystack, $needle ) {
			if ( function_exists( 'str_ends_with' ) ) {
				return str_ends_with( $haystack, $needle );
			}
			$len = strlen( $needle );
			return 0 === $len || ( strlen( $haystack ) >= $len && substr( $haystack, -$len ) === $needle );
		}
	}

	endif;

Reign_Plugin_Installer::instance();

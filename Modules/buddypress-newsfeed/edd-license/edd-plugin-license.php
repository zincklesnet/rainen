<?php
/**
 * This is the URL our updater / license checker pings. This should be the URL of the site with EDD installed.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/edd-license
 */

if ( ! defined( 'EDD_BNEWS_STORE_URL' ) ) {
	define( 'EDD_BNEWS_STORE_URL', 'https://wbcomdesigns.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
}

if ( ! defined( 'EDD_BNEWS_ITEM_NAME' ) ) {
	define( 'EDD_BNEWS_ITEM_NAME', 'BuddyPress Newsfeed' );
}

if ( ! defined( 'EDD_BNEWS_PLUGIN_LICENSE_PAGE' ) ) {
	define( 'EDD_BNEWS_PLUGIN_LICENSE_PAGE', 'wbcom-license-page' );
}

if ( ! class_exists( 'EDD_BNEWS_Plugin_Updater' ) ) {
	// load our custom updater.
	include dirname( __FILE__ ) . '/EDD_BNEWS_Plugin_Updater.php';
}

/**
 * Plugin Updater.
 *
 * @return void
 */
function edd_BNEWS_plugin_updater() {
	// retrieve our license key from the DB.
	$license_key = trim( get_option( 'edd_wbcom_BNEWS_license_key' ) );

	// setup the updater.
	$edd_updater = new EDD_BNEWS_Plugin_Updater(
		EDD_BNEWS_STORE_URL,
		BNEWS_PLUGIN_FILE,
		array(
			'version'   => BNEWS_PLUGIN_VERSION,             // current version number.
			'license'   => $license_key,        // license key (used get_option above to retrieve from DB).
			'item_name' => EDD_BNEWS_ITEM_NAME,  // name of this plugin.
			'author'    => 'wbcomdesigns',  // author of this plugin.
			'url'       => home_url(),
		)
	);
}
add_action( 'admin_init', 'edd_BNEWS_plugin_updater', 0 );


/**
 * Added admin license tab.
 *
 * @return void
 */
function edd_wbcom_BNEWS_register_option() {
	// creates our settings in the options table.
	register_setting( 'edd_wbcom_BNEWS_license', 'edd_wbcom_BNEWS_license_key', 'edd_BNEWS_sanitize_license' );
}
add_action( 'admin_init', 'edd_wbcom_BNEWS_register_option' );

/**
 * Sanitize License.
 *
 * @param  int $new New Key.
 */
function edd_BNEWS_sanitize_license( $new ) {
	$old = get_option( 'edd_wbcom_BNEWS_license_key' );
	if ( $old && $old != $new ) {
		delete_option( 'edd_wbcom_BNEWS_license_status' ); // new license has been entered, so must reactivate.
	}
	return $new;
}

/**
 * This illustrates how to activate a license key
 *
 * @return void
 */
function edd_wbcom_BNEWS_activate_license() {
	// listen for our activate button to be clicked.
	if ( isset( $_POST['edd_BNEWS_license_activate'] ) ) {
		// run a quick security check.
		if ( ! check_admin_referer( 'edd_wbcom_BNEWS_nonce', 'edd_wbcom_BNEWS_nonce' ) ) {
			return; // get out if we didn't click the Activate button.
		}

		// retrieve the license from the database.
		$license = isset( $_POST['edd_wbcom_BNEWS_license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['edd_wbcom_BNEWS_license_key'] ) ) : '';

		// data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_BNEWS_ITEM_NAME ), // the name of our product in EDD.
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post(
			EDD_BNEWS_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'buddypress-newsfeed' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired':
						$message = sprintf(
							/* translators: %1$s: Expire Time */
							__( 'Your license key expired on %s.', 'buddypress-newsfeed' ),
							esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) )
						);
						break;

					case 'revoked':
						$message = __( 'Your license key has been disabled.', 'buddypress-newsfeed' );
						break;

					case 'missing':
						$message = __( 'Invalid license.', 'buddypress-newsfeed' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = __( 'Your license is not active for this URL.', 'buddypress-newsfeed' );
						break;

					case 'item_name_mismatch':
						/* translators: %1$s: Item Name*/
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'buddypress-newsfeed' ), EDD_BNEWS_ITEM_NAME );
						break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'buddypress-newsfeed' );
						break;

					default:
						$message = __( 'An error occurred, please try again.', 'buddypress-newsfeed' );
						break;
				}
			} else {
				set_transient( 'edd_wbcom_BNEWS_license_key_data', $license_data, 12 * HOUR_IN_SECONDS );
			}
		}

		// Check if anything passed on a message constituting a failure.
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . EDD_BNEWS_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg(
				array(
					'BNEWS_activation' => 'false',
					'message'          => urlencode( $message ),
				),
				$base_url
			);
			$license  = trim( $license );
			update_option( 'edd_wbcom_BNEWS_license_key', $license );
			update_option( 'edd_wbcom_BNEWS_license_status', $license_data->license );
			wp_safe_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"
		$license = trim( $license );
		update_option( 'edd_wbcom_BNEWS_license_key', $license );
		update_option( 'edd_wbcom_BNEWS_license_status', $license_data->license );
		wp_safe_redirect( admin_url( 'admin.php?page=' . EDD_BNEWS_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action( 'admin_init', 'edd_wbcom_BNEWS_activate_license' );

/**
 * Illustrates how to deactivate a license key.
 * This will decrease the site count
 *
 * @return void
 */
function edd_wbcom_BNEWS_deactivate_license() {
	// listen for our activate button to be clicked.
	if ( isset( $_POST['edd_BNEWS_license_deactivate'] ) ) {

		// run a quick security check.
		if ( ! check_admin_referer( 'edd_wbcom_BNEWS_nonce', 'edd_wbcom_BNEWS_nonce' ) ) {
			return; // get out if we didn't click the Activate button.
		}

		// retrieve the license from the database.
		$license = trim( get_option( 'edd_wbcom_BNEWS_license_key' ) );

		// data to send in our API request.
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_BNEWS_ITEM_NAME ), // the name of our product in EDD.
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post(
			EDD_BNEWS_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'buddypress-newsfeed' );
			}

			$base_url = admin_url( 'admin.php?page=' . EDD_BNEWS_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg(
				array(
					'BNEWS_activation' => 'false',
					'message'          => urlencode( $message ),
				),
				$base_url
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		// decode the license data.
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		delete_transient( 'edd_wbcom_BNEWS_license_key_data' );

		// $license_data->license will be either "deactivated" or "failed"
		if ( 'deactivated' === $license_data->license || 'failed' === $license_data->license ) {
			delete_option( 'edd_wbcom_BNEWS_license_status' );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . EDD_BNEWS_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action( 'admin_init', 'edd_wbcom_BNEWS_deactivate_license' );

/**
 * This illustrates how to check if  a license key is still valid the updater does this for you, so this is only needed if you  want to do something custom
 */
add_action( 'admin_init', 'edd_wbcom_BNEWS_check_license' );
function edd_wbcom_BNEWS_check_license() {
	global $wp_version, $pagenow;

	if ( $pagenow === 'plugins.php' || $pagenow === 'index.php' || ( isset( $_GET['page'] ) && $_GET['page'] === 'wbcom-license-page' ) ) {		// phpcs:ignore

		$license_data = get_transient( 'edd_wbcom_BNEWS_license_key_data' );
		$license = trim( get_option( 'edd_wbcom_BNEWS_license_key' ) );

		if ( empty( $license_data ) && $license != '' ) {

			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => $license,
				'item_name'  => urlencode( EDD_BNEWS_ITEM_NAME ),
				'url'        => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post(
				EDD_BNEWS_STORE_URL,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $license_data ) ) {
				set_transient( 'edd_wbcom_BNEWS_license_key_data', $license_data, 12 * HOUR_IN_SECONDS );
			}
		}
	}
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function edd_wbcom_BNEWS_admin_notices() {

	$license_activation = filter_input( INPUT_GET, 'BNEWS_activation' ) ? filter_input( INPUT_GET, 'BNEWS_activation' ) : '';
	$error_message      = filter_input( INPUT_GET, 'message' ) ? filter_input( INPUT_GET, 'message' ) : '';
	$license_data       = get_transient( 'edd_wbcom_BNEWS_license_key_data' );
	$license            = trim( get_option( 'edd_wbcom_BNEWS_license_key' ) );

	if ( isset( $license_activation ) && ! empty( $error_message ) || ( ! empty( $license_data ) && $license_data->license == 'expired' ) ) {
		if ( $license_activation === '' && !empty( $license_data ) ) {
			$license_activation = $license_data->license;
		}
		switch ( $license_activation ) {
			case 'expired':
				?>
				<div class="notice notice-error is-dismissible">
				<p>
				<?php
				$message = sprintf(//phpcs:ignore
							/* translators: %1$s: Expire Time*/
					__( 'Your BuddyPress Newsfeed plugin license key expired on %s.', 'buddypress-newsfeed' ),
					esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) )
				); 
				echo esc_html( $message );
				?>
				</p>
				</div>
				<?php
				break;
			case 'false':
				$message = urldecode( $error_message );
				?>
				<div class="error">
					<p><?php echo esc_html( $message ); ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;
		}
	}

	if ( $license === '' ) {
		?>
		<div class="notice notice-error is-dismissible">
			<p>
			<?php
			echo esc_html__( 'Please activate your BuddyPress Newsfeed plugin license key.', 'buddypress-newsfeed' );
			?>
			</p>			
		</div>
		<?php
	}

}
add_action( 'admin_notices', 'edd_wbcom_BNEWS_admin_notices' );

/**
 * Display License tab Content.
 *
 * @return void
 */
function wbcom_BNEWS_render_license_section() {

	$license = get_option( 'edd_wbcom_BNEWS_license_key', true );
	$status  = get_option( 'edd_wbcom_BNEWS_license_status' );

	$plugin_data = get_plugin_data( BNEWS_PLUGIN_PATH . 'buddypress-newsfeed.php', $markup = true, $translate = true );

	$license_output = edd_BNEWS_active_license_message();

	if ( false !== $status && 'valid' === $status && ! empty( $license_output ) && $license_output['license_data']->license == 'valid' ) {
		$status_class = 'active';
		$status_text  = 'Active';
	} else if ( ! empty( $license_output ) && $license_output['license_data']->license != '' && $license_output['license_data']->license == 'expired' ) {
		$status_class = 'expired';
		$status_text  = ucfirst( str_replace( '_', ' ', $license_output['license_data']->license ) );

	} else if ( ! empty( $license_output ) && $license_output['license_data']->license != '' && $license_output['license_data']->license == 'invalid' ) {
		$status_class = 'invalid';
		$status_text  = ucfirst( str_replace( '_', ' ', $license_output['license_data']->license ) );

	} else {
		$status_class = 'inactive';
		$status_text  = 'Inactive';
	}
	$plugin_name    = $plugin_data['Name'];
	$plugin_version = $plugin_data['Version'];
	?>
	<table class="form-table wb-license-form-table mobile-license-headings">
		<thead>
			<tr>
				<th class="wb-product-th"><?php esc_html_e( 'Product', 'buddypress-newsfeed' ); ?></th>
				<th class="wb-version-th"><?php esc_html_e( 'Version', 'buddypress-newsfeed' ); ?></th>
				<th class="wb-key-th"><?php esc_html_e( 'Key', 'buddypress-newsfeed' ); ?></th>
				<th class="wb-status-th"><?php esc_html_e( 'Status', 'buddypress-newsfeed' ); ?></th>
				<th class="wb-action-th"><?php esc_html_e( 'Action', 'buddypress-newsfeed' ); ?></th>
				<th></th>
			</tr>
		</thead>
	</table>
	<form method="post" action="options.php">
		<?php settings_fields( 'edd_wbcom_BNEWS_license' ); ?>
		<table class="form-table wb-license-form-table">
			<tr>
				<td class="wb-plugin-name"><?php echo esc_html( $plugin_name ); ?></td>
				<td class="wb-plugin-version"><?php echo esc_html( $plugin_version ); ?></td>
				<td class="wb-plugin-license-key">
					<input id="edd_wbcom_BNEWS_license_key" name="edd_wbcom_BNEWS_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license, 'buddypress-newsfeed' ); ?>" />
					<p><?php echo esc_html( $license_output['message'] ); ?></p>
				</td>
				<td class="wb-license-status <?php echo esc_attr( $status_class ); ?>"><?php esc_attr_e( $status_text, 'buddypress-newsfeed' ); ?></td>
				<td class="wb-license-action">
					<?php
					if ( false !== $status && 'valid' === $status ) {
						wp_nonce_field( 'edd_wbcom_BNEWS_nonce', 'edd_wbcom_BNEWS_nonce' );
						?>
						<input type="submit" class="button-secondary" name="edd_BNEWS_license_deactivate" value="<?php esc_html_e( 'Deactivate License', 'buddypress-newsfeed' ); ?>"/>
						<?php
					} else {
						wp_nonce_field( 'edd_wbcom_BNEWS_nonce', 'edd_wbcom_BNEWS_nonce' );
						?>
						<input type="submit" class="button-secondary" name="edd_BNEWS_license_activate" value="<?php esc_html_e( 'Activate License', 'buddypress-newsfeed' ); ?>"/>
					<?php } ?>
				</td>
			</tr>
		</table>
	</form>

	<?php
}
add_action( 'wbcom_add_plugin_license_code', 'wbcom_BNEWS_render_license_section' );

function edd_BNEWS_active_license_message() {
	global $wp_version, $pagenow;

	if ( $pagenow === 'plugins.php' || $pagenow === 'index.php' || ( isset( $_GET['page'] ) && $_GET['page'] === 'wbcom-license-page' ) ) {		// phpcs:ignore

		$license_data = get_transient( 'edd_wbcom_BNEWS_license_key_data' );
		$license      = trim( get_option( 'edd_wbcom_BNEWS_license_key' ) );

			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => $license,
				'item_name'  => urlencode( EDD_BNEWS_ITEM_NAME ),
				'url'        => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post(
				EDD_BNEWS_STORE_URL,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

		if ( is_wp_error( $response ) ) {
			return false;
		}

			$output = array();
			$output['license_data'] = json_decode( wp_remote_retrieve_body( $response ) );
			$message = '';
			// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'buddypress-newsfeed' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			// Get expire date
			$expires = false;
			if ( isset( $license_data->expires ) && 'lifetime' != $license_data->expires ) {
				$expires    = date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) );
			} elseif ( isset( $license_data->expires ) && 'lifetime' == $license_data->expires ) {
				$expires = 'lifetime';
			}

			if ( $license_data->license == 'valid' ) {
				// Get site counts
				$site_count    = $license_data->site_count;
				$license_limit = $license_data->license_limit;
				$message = 'License key is active.';
				if ( isset( $expires ) && 'lifetime' != $expires ) {
					$message .= sprintf( __( ' Expires %s.', 'buddypress-newsfeed' ), $expires ) . ' ';
				}
				if ( $license_limit ) {
					$message .= sprintf( __( 'You have %1$s/%2$s-sites activated.', 'buddypress-newsfeed' ), $site_count, $license_limit );
				}
			}
		}
			$output['message'] = $message;
			return $output;
	}
}

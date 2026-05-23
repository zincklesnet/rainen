<?php
/**
* For further details please visit http://docs.easydigitaldownloads.com/article/383-automatic-upgrades-for-wordpress-plugins
 */

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'EDD_BPSA_STORE_URL', 'https://wbcomdesigns.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the download ID for the product in Easy Digital Downloads
define( 'EDD_BPSA_ITEM_ID', 1593862 ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of the product in Easy Digital Downloads
define( 'EDD_BPSA_ITEM_NAME', 'Buddypress Schedule Activity' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of the settings page for the license input to be displayed
define( 'EDD_BPSA_PLUGIN_LICENSE_PAGE', 'wbcom-license-page' );

if ( ! class_exists( 'EDD_BPSA_Plugin_Updater' ) ) {
	// load our custom updater
	include dirname( __FILE__ ) . '/edd_bpsa_plugin_updater.php';
}

/**
 * Initialize the updater. Hooked into `init` to work with the
 * wp_version_check cron job, which allows auto-updates.
 */
function edd_bpsa_plugin_updater() {

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'edd_bpsa_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_BPSA_Plugin_Updater(
		EDD_BPSA_STORE_URL,
		__FILE__,
		array(
			'version' => BUDDYPRESS_SCHEDULE_ACTIVITY_VERSION,                    // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => EDD_BPSA_ITEM_ID,       // ID of the product
			'author'  => 'wbcomdesigns', // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'init', 'edd_bpsa_plugin_updater' );


/**
 * Registers the license key setting in the options table.
 *
 * @return void
 */
function edd_bpsa_register_option() {
	register_setting( 'edd_bpsa_license', 'edd_bpsa_license_key', 'edd_bpsa_sanitize_license' );
}
add_action( 'admin_init', 'edd_bpsa_register_option' );

/**
 * Sanitizes the license key.
 *
 * @param string  $new The license key.
 * @return string
 */
function edd_bpsa_sanitize_license( $new ) {
	$old = get_option( 'edd_bpsa_license_key' );
	if ( $old && $old !== $new ) {
		delete_option( 'edd_bpsa_license_status' ); // new license has been entered, so must reactivate
	}

	return sanitize_text_field( $new );
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function edd_bpsa_admin_notices() {
	$license_activation = filter_input( INPUT_GET, 'WPLI_activation' ) ? filter_input( INPUT_GET, 'WPLI_activation' ) : '';
	$error_message      = filter_input( INPUT_GET, 'message' ) ? filter_input( INPUT_GET, 'message' ) : '';
	$license_data       = get_transient( 'edd_bpsa_license_key_data' );
	$license            = trim( get_option( 'edd_bpsa_license_key' ) );

	if ( isset( $license_activation ) && ! empty( $error_message ) || ( ! empty( $license_data ) && $license_data->license == 'expired' ) ) {
		if ( $license_activation === '' && ! empty( $license_data ) ) {
			$license_activation = $license_data->license;
		}
		switch ( $license_activation ) {
			case 'expired':
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<?php
						$message = sprintf(
									/* translators: %1$s: Expire Time*/
							__( 'Your BuddyPress Schedule Activity plugin license key expired on %s.', 'buddypress-schedule-activity' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						echo wp_kses_post( $message );
						?>
					</p>
				</div>
				<?php
				break;
			break;
			case 'false':
				$message = urldecode( $error_message );
				?>
				<div class="notice notice-error is-dismissible">
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
			echo esc_html__( 'Please activate your BuddyPress Schedule Activity plugin license key.', 'buddypress-schedule-activity' );
			?>
			</p>			
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'edd_bpsa_admin_notices' );

/**
 * Display License tab Content.
 *
 * @return void
 */
function wbcom_bpsa_render_license_section() {

	$license        = get_option( 'edd_bpsa_license_key', true );
	$status         = get_option( 'edd_bpsa_license_status' );
	$license_output = edd_bpsa_active_license_message();

	if ( false !== $status && 'valid' === $status && ! empty( $license_output ) && $license_output['license_data']->license == 'valid' ) {
		$status_class = 'active';
		$status_text  = 'Active';
	} elseif ( ! empty( $license_output ) && $license_output['license_data']->license != '' && $license_output['license_data']->license == 'expired' ) {
		$status_class = 'expired';
		$status_text  = ucfirst( str_replace( '_', ' ', $license_output['license_data']->license ) );

	} elseif ( ! empty( $license_output ) && $license_output['license_data']->license != '' && $license_output['license_data']->license == 'invalid' ) {
		$status_class = 'invalid';
		$status_text  = ucfirst( str_replace( '_', ' ', $license_output['license_data']->license ) );

	} else {
		$status_class = 'inactive';
		$status_text  = 'Inactive';
	}
	?>
	<table class="form-table wb-license-form-table mobile-license-headings">
		<thead>
			<tr>
				<th class="wb-product-th"><?php esc_html_e( 'Product', 'buddypress-schedule-activity' ); ?></th>
				<th class="wb-version-th"><?php esc_html_e( 'Version', 'buddypress-schedule-activity' ); ?></th>
				<th class="wb-key-th"><?php esc_html_e( 'Key', 'buddypress-schedule-activity' ); ?></th>
				<th class="wb-status-th"><?php esc_html_e( 'Status', 'buddypress-schedule-activity' ); ?></th>
				<th class="wb-action-th"><?php esc_html_e( 'Action', 'buddypress-schedule-activity' ); ?></th>
			</tr>
		</thead>
	</table>
	<form method="post" action="options.php">
		<?php settings_fields( 'edd_bpsa_license' ); ?>
		<table class="form-table wb-license-form-table">
			<tr>
				<td class="wb-plugin-name"><?php echo esc_html( EDD_BPSA_ITEM_NAME ); ?></td>
				<td class="wb-plugin-version"><?php echo esc_html( BUDDYPRESS_SCHEDULE_ACTIVITY_VERSION ); ?></td>
				<td class="wb-plugin-license-key">
					<input id="edd_bpsa_license_key" name="edd_bpsa_license_key" type="text" value="<?php esc_attr_e( $license, 'buddypress-schedule-activity' ); ?>" />
					<p><?php echo isset( $license_output['message'] ) ? esc_html( $license_output['message'] ) : ''; ?></p>
				</td>
				<td class="wb-license-status <?php echo esc_attr( $status_class ); ?>"><?php esc_attr_e( $status_text, 'buddypress-schedule-activity' ); ?></td>
				<td class="wb-license-action">
					<?php
					if ( false !== $status && 'valid' === $status ) {
						wp_nonce_field( 'edd_bpsa_nonce', 'edd_bpsa_nonce' );
						?>
						<input type="submit" class="button-secondary" name="edd_bpsa_license_deactivate" value="<?php esc_html_e( 'Deactivate License', 'buddypress-schedule-activity' ); ?>"/>
						<?php
					} else {
						$other_attributes = array( 'class' => 'button-secondary' );
						wp_nonce_field( 'edd_bpsa_nonce', 'edd_bpsa_nonce' );
						?>
						<input type="submit" class="button-secondary" name="edd_bpsa_license_activate" value="<?php esc_html_e( 'Activate License', 'buddypress-schedule-activity' ); ?>"/>
					<?php } ?>
				</td>				
			</tr>
		</table>
	</form>
	<?php
}
add_action( 'wbcom_add_plugin_license_code', 'wbcom_bpsa_render_license_section' );



/**
 * Activates the license key.
 *
 * @return void
 */
function edd_bpsa_activate_license() {

	// listen for our activate button to be clicked
	if ( ! isset( $_POST['edd_bpsa_license_activate'] ) ) {
		return;
	}

	// run a quick security check
	if ( ! check_admin_referer( 'edd_bpsa_nonce', 'edd_bpsa_nonce' ) ) {
		return; // get out if we didn't click the Activate button
	}

	// retrieve the license from the database
	$license = trim( get_option( 'edd_bpsa_license_key' ) );
	if ( ! $license ) {
		$license = ! empty( $_POST['edd_bpsa_license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['edd_bpsa_license_key'] ) ) : '';
	}

	if ( ! $license ) {
		return;
	}

	// data to send in our API request
	$api_params = array(
		'edd_action'  => 'activate_license',
		'license'     => $license,
		'item_id'     => EDD_BPSA_ITEM_ID,
		'item_name'   => rawurlencode( EDD_BPSA_ITEM_NAME ), // the name of our product in EDD
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		EDD_BPSA_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

		// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$message = __( 'An error occurred, please try again.' , 'buddypress-schedule-activity');
		}
	} else {

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success ) {

			switch ( $license_data->error ) {

				case 'expired':
					$message = sprintf(
						/* translators: the license key expiration date */
						__( 'Your license key expired on %s.', 'buddypress-schedule-activity' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'disabled':
				case 'revoked':
					$message = __( 'Your license key has been disabled.', 'buddypress-schedule-activity' );
					break;

				case 'missing':
					$message = __( 'Invalid license.', 'buddypress-schedule-activity' );
					break;

				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', 'buddypress-schedule-activity' );
					break;

				case 'item_name_mismatch':
					/* translators: the plugin name */
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'buddypress-schedule-activity' ), EDD_BPSA_ITEM_NAME );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'buddypress-schedule-activity' );
					break;

				default:
					$message = __( 'An error occurred, please try again.', 'buddypress-schedule-activity' );
					break;
			}
		} else {
				set_transient( 'edd_bpsa_license_key_data', $license_data, 12 * HOUR_IN_SECONDS );
		}
	}

	// Check if anything passed on a message constituting a failure
	if ( ! empty( $message ) ) {
		$redirect = add_query_arg(
			array(
				'page'            => EDD_BPSA_PLUGIN_LICENSE_PAGE,
				'bpas_activation' => 'false',
				'message'         => rawurlencode( $message ),
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect );
		exit();
	}

	// $license_data->license will be either "valid" or "invalid"
	if ( 'valid' === $license_data->license ) {
		update_option( 'edd_bpsa_license_key', $license );
	}
	update_option( 'edd_bpsa_license_status', $license_data->license );

	wp_safe_redirect( admin_url( 'admin.php?page=' . EDD_BPSA_PLUGIN_LICENSE_PAGE ) );
	exit();
}
add_action( 'admin_init', 'edd_bpsa_activate_license' );

/**
 * Deactivates the license key.
 * This will decrease the site count.
 *
 * @return void
 */
function edd_bpsa_deactivate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_bpsa_license_deactivate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'edd_bpsa_nonce', 'edd_bpsa_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		// retrieve the license from the database
		$license = trim( get_option( 'edd_bpsa_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'  => 'deactivate_license',
			'license'     => $license,
			'item_id'     => EDD_BPSA_ITEM_ID,
			'item_name'   => rawurlencode( EDD_BPSA_ITEM_NAME ), // the name of our product in EDD
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post(
			EDD_BPSA_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' , 'buddypress-schedule-activity' );
			}

			$redirect = add_query_arg(
				array(
					'page'            => EDD_BPSA_PLUGIN_LICENSE_PAGE,
					'bpas_activation' => 'false',
					'message'         => rawurlencode( $message ),
				),
				admin_url( 'admin.php' )
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( 'deactivated' === $license_data->license ) {
			delete_option( 'edd_bpsa_license_status' );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . EDD_BPSA_PLUGIN_LICENSE_PAGE ) );
		exit();

	}
}
add_action( 'admin_init', 'edd_bpsa_deactivate_license' );

/**
 * Checks if a license key is still valid.
 * The updater does this for you, so this is only needed if you want
 * to do something custom.
 *
 * @return void
 */
function edd_bpsa_check_license() {

	$license = trim( get_option( 'edd_bpsa_license_key' ) );

	$api_params = array(
		'edd_action'  => 'check_license',
		'license'     => $license,
		'item_id'     => EDD_BPSA_ITEM_ID,
		'item_name'   => rawurlencode( EDD_BPSA_ITEM_NAME ),
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		EDD_BPSA_STORE_URL,
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

	if ( 'valid' === $license_data->license ) {
		echo 'valid';
		exit;
		// this license is still valid
	} else {
		echo 'invalid';
		exit;
		// this license is no longer valid
	}
}

/**
 * Notice for activate license.
 *
 * @return void
 */
function edd_bpsa_active_license_message() {
	global $wp_version, $pagenow;

	if ( $pagenow === 'plugins.php' || $pagenow === 'index.php' || ( isset( $_GET['page'] ) && $_GET['page'] === 'wbcom-license-page' ) ) {	// phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$license      = trim( get_option( 'edd_bpsa_license_key' ) );
		$license_data = get_transient( 'edd_bpsa_license_key_data' );

			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => $license,
				'item_name'  => urlencode( EDD_BPSA_ITEM_NAME ),
				'url'        => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post(
				EDD_BPSA_STORE_URL,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

		if ( is_wp_error( $response ) ) {
			return false;
		}

			$output                 = array();
			$output['license_data'] = json_decode( wp_remote_retrieve_body( $response ) );
			$message                = '';
			// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'buddypress-schedule-activity' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			// Get expire date
			$expires = false;
			if ( isset( $license_data->expires ) && 'lifetime' != $license_data->expires ) {
				$expires = date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) );
			} elseif ( isset( $license_data->expires ) && 'lifetime' == $license_data->expires ) {
				$expires = 'lifetime';
			}

			if ( $license_data->license == 'valid' ) {
				// Get site counts
				$site_count    = $license_data->site_count;
				$license_limit = $license_data->license_limit;
				$message       = 'License key is active.';
				if ( isset( $expires ) && 'lifetime' != $expires ) {
					// Translators: %s is the date expire
					$message .= sprintf( __( ' Expires %s.', 'buddypress-schedule-activity' ), $expires ) . ' ';
				}
				if ( $license_limit ) {
					// Translators: %s is the site count and license
					$message .= sprintf( __( 'You have %1$s/%2$s-sites activated.', 'buddypress-schedule-activity' ), $site_count, $license_limit );
				}
			}
		}
			$output['message'] = $message;
			return $output;
	}
}


<?php
/**
 * Exit if accessed directly.
 *
 * @package Buddypress_Profile_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wbcom-tab-content">
<div class="wbcom-faq-adming-setting">
	<div class="wbcom-admin-title-section">
		<h3><?php esc_html_e( 'Have some questions?', 'buddypress-profile-pro' ); ?></h3>
	</div>

	<div class="wbcom-faq-admin-settings-block">
		<div id="wbcom-faq-settings-section" class="bprm-table">
			<div class="wbcom-faq-admin-row">
				<div class="wbcom-faq-section-row">
				<button class="wbcom-faq-accordion">
					<?php esc_html_e( 'Does BuddyPress Profile Pro plugin require BuddyPress?', 'buddypress-profile-pro' ); ?>
				</button>
				<div class="wbcom-faq-panel">
					<p>
						<?php esc_html_e( 'Yes, BuddyPress is a mandatory plugin, and It needs you to have BuddyPress installed and activated.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="wbcom-faq-admin-row">
			<div class="wbcom-faq-section-row">
				<button class="wbcom-faq-accordion">
					<?php esc_html_e( 'Where can I view and edit members extended fields?', 'buddypress-profile-pro' ); ?>
				</button>
				<div class="wbcom-faq-panel">
					<p>
						<?php esc_html_e( 'You can edit and view saved extended fields at BuddyPress profile under Profile menu tab.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="wbcom-faq-admin-row">
			<div class="wbcom-faq-section-row">
				<button class="wbcom-faq-accordion">
					<?php esc_html_e( 'What is the use of API Key option provided in general settings section?', 'buddypress-profile-pro' ); ?>
				</button>
				<div class="wbcom-faq-panel">
					<p>
						<?php esc_html_e( 'Inside Profile Pro we have options to add location fields and these fields can be filled with an auto-suggestion. Google Places API key is used to fill places with google autocomplete. ', 'buddypress-profile-pro' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'To use the Google Autocomplete Field Type in the Profile field, you must register your app project on the Google API Console and get a Google API key which you can add there.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="wbcom-faq-admin-row">
			<div class="wbcom-faq-section-row">
				<button class="wbcom-faq-accordion">
					<?php esc_html_e( 'How to keep only desired fields in the form?', 'buddypress-profile-pro' ); ?>
				</button>
				<div class="wbcom-faq-panel">
					<p>
						<?php esc_html_e( 'You can set fields in the form by selecting desired fields in admin settings under Field Settings tab.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="wbcom-faq-admin-row">
			<div class="wbcom-faq-section-row">
				<button class="wbcom-faq-accordion">
					<?php esc_html_e( 'How can I create a new extended field the form ?', 'buddypress-profile-pro' ); ?>
				</button>
				<div class="wbcom-faq-panel">
					<p>
						<?php esc_html_e( 'You can create a new field in form with help of the form provided under Field Settings tab.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="wbcom-faq-admin-row">
			<div class="wbcom-faq-section-row">
				<button class="wbcom-faq-accordion">
					<?php esc_html_e( 'How can I keep some field required for the profile field?', 'buddypress-profile-pro' ); ?>
				</button>
				<div class="wbcom-faq-panel">
					<p>
						<?php esc_html_e( 'You can set required fields in form by checking Required setting for any invidual fields under Field Settings section.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="wbcom-faq-admin-row">
			<div class="wbcom-faq-section-row">
				<button class="wbcom-faq-accordion">
					<?php esc_html_e( 'What is the use of Repeater setting provided for fields under Field Settings section?', 'buddypress-profile-pro' ); ?>
				</button>
				<div class="wbcom-faq-panel">
					<p>
						<?php esc_html_e( 'Fields which are saved with Repeater option checked, provides option to users to fill data for that particular field multiple times.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div> <!-- closing of div class wbcom-tab-content -->

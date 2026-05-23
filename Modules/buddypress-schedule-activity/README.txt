=== BuddyPress Schedule Activity ===
Contributors: wbcomdesigns
Donate link: https://https://wbcomdesigns.com/
Tags: schedule, activity, buddypress
Requires at least: 3.0.1
Tested up to: 6.8.1
Stable tag: 1.4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

 BuddyPress Schedule Activity allows you to organize and manage your Activity planner, making planning and scheduling activities easier.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `buddypress-schedule-activity.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Changelog ==

= 1.4.3 =
* Security: Fixed SQL injection vulnerabilities with proper escaping in database queries.
* Security: Added race condition protection with transient lock for cron publishing.
* Security: Improved nonce verification in AJAX handlers.
* Security: Added proper data sanitization and validation throughout.
* Security: Added cleanup of activity meta on plugin uninstall.
* Fixed: Scheduled Posts navigation tab not appearing in BuddyPress member profile.
* Fixed: Page title showing "Activity" instead of "Scheduled Posts" in content area.
* Fixed: Added subnav item for proper BuddyPress navigation structure.
* Improved: Deactivator now properly clears cron events and transients.
* Improved: Cron publisher with atomic operations and proper error handling.
* Cleaned: Removed debug console.log statement from JavaScript.
* Cleaned: Removed unused commented-out code.
* Dev: Regenerated minified JavaScript file.

= 1.4.2 =
* Version bump to 1.4.2

= 1.4.1 =
* Improved: UI compatibility fixes with BuddyBoss Theme.
* Improved: Codebase updated and build process optimized (Grunt update).
* Fixed: Duplicate scheduled posts issue.
* Fixed: Activity buttons issue on scheduled posts tab.
* Fixed: Load more button issue with scheduled posts.
* Fixed: Removed unnecessary action button on load more.

= 1.4.0 =
* Fixed: Issue with switching scheduled posts between global, private, and public tabs.
* Fixed: Scheduled activities not displaying correctly in user profiles for private and hidden groups.
* Fixed: Schedule activity time not showing correctly with default and BuddyBoss themes.
* Fixed: Nonce validation issues, including bypass vulnerability with Youzify integration.
* Improved: Compatibility with BuddyBoss Platform.
* Improved: Group activity scheduling behavior and time visibility.
* Improved: Code performance, formatting, and localization readiness.
* Improved: Security with added data sanitization, escaping, and safer condition handling (Yoda conditions).
* Updated: Replaced use of `extract()` for better readability and security.
* Added: New helper functions like `buddypress_schedule_activity_update_icon` and `buddypress_schedule_activity_get_js_strings`.
* Fixed: PHP warnings due to missing `isset()` checks.
* Updated: Readme file and core template structure.

= 1.3.1 =
* Fixed: Scheduled activity time not displaying correctly with default themes.
* Improved: Time display compatibility for BuddyBoss theme.
* Fixed: Minor typos and text updates for better clarity.
* Cleaned: Resolved PHPCS issues in public-facing files.

= 1.3.0 =
* Added: Schedule count UI compatibility with BuddyBoss platform.
* Fixed: Schedule timing not working correctly on activity posts.
* Fixed: Scheduled posts count issue in the BuddyBoss nav menu.
* Fixed: No notification received when scheduling an activity post.
* Fixed: Post count inconsistencies with BuddyBoss.
* Fixed: Two-image issue during scheduled activity creation.
* Improved: String updates for better clarity.
* Improved: Minified CSS, JS, and RTL stylesheet handling for better performance.
* Dev: Resolved PHPCS issues in EDD license files.
* Dev: Removed unused and outdated files from the `wbcom` folder.

= 1.2.0 =
* Fix: Resolved double popup issue with Youzify after scheduling an activity.
* Fix: Addressed scheduling activity issues in Youzify.
* Fix: Resolved error handling when Youzify is not active.
* Fix: Corrected image-related issue in the Youzify plugin.
* Fix: Ensured scheduled time displays correctly with Youzify.
* Fix: Resolved issue where alert was not generated after scheduling a post.
* Fix: Fixed animation on the "What's New" submit button.
* Fix: Corrected notice animation issue in Youzify.
* Enhancement: Added support for scheduling activity posts with images.
* Enhancement: Integrated display count functionality with rtMedia + Youzify.
* Enhancement: Managed UI for schedule post icons in Youzify.
* Enhancement: Added a flag to display activity titles only in Youzify.

= 1.1.0 =
* Fix: Updated condition to address activity issue with Youzify.
* Fix: Resolved title display issue on activity page with Youzify.
* Fix: Corrected activity title handling for compatibility with Youzify.
* Fix: Fixed issue where scheduled alerts were not triggering with Youzify.
* Fix: Updated count handling for improved Youzify compatibility.
* Improvement: General compatibility fixes with Youzify integration.

= 1.0.1 =
* Fix: Translate and nonce verification issues
* Fix: Issue in the JS
* Added: Display Notice and deactivate plugin when the Youzify plugin is activated

= 1.0.0 =
* Initial Release

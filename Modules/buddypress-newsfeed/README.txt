=== Wbcom Designs - BuddyPress Newsfeed ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com/
Tags: buddypress, newsfeed, wall, activity, mentions, favorites, personal, groups
Requires at least: 3.0.1
Tested up to: 6.7.2
Stable tag: 1.8.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BuddyPress Newsfeed merges BuddyPress mentions, favorites, personal, groups, etc, in one place.

== Description ==

BuddyPress Newsfeed merges BuddyPress mentions, favorites, personal, groups, etc, in one place.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `buddypress-newsfeed.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= Does this plugin require BuddyPress? =

Yes, it requires BuddyPress to be installed and activated.

== Changelog ==
= 1.8.3 =
* Added: Setting and functionality to display relevant activity, similar to BuddyBoss.  
* Added: Warning message display when used alongside the Youzify plugin to improve compatibility clarity.  
* Updated: Documentation links for plugins and themes.  
* Updated: Minified CSS and JS files for optimized performance.  
* Updated: Improved behavior when the newsfeed option is enabled with Youzify.  
* Updated: Display logic for newsfeed tab to avoid showing only personal activity with BuddyBoss.  
* Fixed: Error display for logged-out users when the plugin is activated.  
* Fixed: Typo corrections and minor code cleanup.  
* Fixed: PHP warnings during topic creation in bbPress.  
* Fixed: PHPCS issues in admin files and license handler.  
* Removed: Unused files and old code from the Wbcom folder.

= 1.8.2 =
* Fixed an issue with an extra `div` being displayed in forms.
* Resolved the BuddyBoss Activity sitewide issue.

= 1.8.1 =
* Fixed active tab issue when Newsfeed is selected as the first tab.
* Resolved submenu compatibility issue with Youzify.
* Addressed "Which tab to show" issue with BuddyBoss.

= 1.8.0 =
* Added FontAwesome icons (fa-rss, fa-user) to Newsfeed and Personal tabs in BuddyPress navigation using custom CSS
* Improved `bpnewsfeed_run_plugin()` function with class existence check
* Removed HardG from the code
* Fixed fatal error related to BuddyBoss compatibility
* Managed backend options and fixed responsive issues
* Fixed PHPCS coding standards issues
* Removed unused Elementor widget

= 1.7.7 =
* Fix: Broken UI with Elementor widget
* Fix: PHP warning and activity pagination not working
* Fix: UI break with BuddyBoss.
* Fix: BuddyBoss pin not working.
* Fix: Comment button and allow posting issue.
* Fix: Warnings
* Fix: Rewrite Elementor widget.
* Added: legacy template support
* Added: Support with bb media component

= 1.7.6 =
* Fix: Fixed issue with license is not deactivated if the response fails

= 1.7.5 =
* Fix: (#73) Fixed issue in activity posting with buddypress
* Fix: (#72) Fixed Posting form is not being displayed or Posting form not showing on the newsfeed tab
* Fix: (#70) Fixed issue with activity types

= 1.7.4 =
* Fix: (#67) Warning
* Fix: Updated admin label and description
* Fix: (#66)Newsfeed enabled by default and relevant activity
* Fix: (#66)Added Single option for activity tab merge
* Fix: Text domain issue
* Fix: BP v12 fixes
* Fix: License issue
* Fix: PHPCS fixes

= 1.7.3 =
* Fix: (#62) Deprecated notice

= 1.7.2 =
* Fix: (#58)Issue with BB in the personal tab
* Fix: (#58)Issue with the Buddyboss platform
* Fix: (#57)Plugin name and version not visible on the license page
* Fix: (#56)The issue with the BB theme
* Fix: Plugin redirect issue when multiple plugins activate at the same time
* Fix: BuddyBoss admin notice issue

= 1.7.1 =
* Fix: Fixed buddyboss admin notice issue

= 1.7.0 =
* Fix: updated admin wrapper
* Fix: Added phpcs ignore comments
* Fix: Fixed nonce issue
* Fix: Fixed phpcs errors

= 1.6.0 =
* Fix: #39 - Issue in form Posting Elementor widget
* Fix: Fixed redirect issue on bulk plugin activation 
* Fix: Plugin author and title updated
* Fix: Removed install plugin button from wrapper and phpcs fixes

= 1.5.3 =
* Fix: (#19) - Fixed phpcs-errors.
* Fix: Fixed dependent plugin issue.

= 1.5.2 =
* Fix: Managed newsfeed icons with olympus theme
* Fix: Fixed BuddyBoss Platform related issue like format not working with GIF (all caps — abbreviation for Graphics Interchange Format)
* Fix: Fixed newest load string issue in elementor widget

= 1.5.1 =
* Update: Added language file
* Enhancement: Added welcome screen page with support video

= 1.5.0 =
* Fix: Button issue after update post status
* Fix: Newsfeed posting issue
* Fix: Return script argument when buddyboss platform plugin activate
* Fix: glitch while creating activity (BuddyPress)
* Fix: (#26) Hide edit activity button when not allow posting
* Fix: Data ajax false to load activity after page load using ajax
* Enhancement: Added Newsfeed elementor widget

= 1.4.2 =
* Fix: #12 Support with BB Platform following activity

= 1.4.1 =
* Fix: #9 Fixed License issue.

= 1.4.0 =
* Fix: BuddyPress condition checks
* Add: German Translation
* Fix: CSS and JS load conditions

= 1.3.1 =
* Fix: Mention notification link (no capitalization for “link”) does not work. (#5)
* Fix: Version update issue. (#4)

= 1.3.0 =
* Fix: Translation Fixes

= 1.2.0 =
* Enhancement - bp 4.4.0 compatibility.
* Fix - Single activity page redirection issue fix.

= 1.1.0 =
* Enhancement - bp 4.3.0 compatibility.
* Fix - bp site wide activity page goes blank issue #1.
* Fix - personal activities doesn't get merged in the newsfeed issue #1.
* Fix - added fontawesome 4.7.0.

= 1.0.0 =
* Initial release.

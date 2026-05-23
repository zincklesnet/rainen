=== Reign WCFM Addon ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com/
Requires at least: 3.0.1
Tested up to: 6.8.2
Stable tag: 1.8.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Here is a short description of the plugin.  This should be no more than 150 characters.  No markup here.

== Description ==

Reign WCFM Addon is a premium Wordpress plugin that works with Reign Theme. This plugin modifies the functionality of WCFM and complements Reign theme.

== Installation ==

1. Upload `reign-wcfm-addon.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Copyright ==

Reign WCFM Addon, Copyright 2020, Wbcom Designs
Reign WCFM Addon is distributed under the terms of the GNU GPL

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see < http://www.gnu.org/licenses/ >

== Changelog ==
= 1.8.3 =
* Enhancement: Manage store layout 2 tab UI.
* Fix: Favorite icon not displaying properly.
* Enhancement: Add a check to disable product creation option with BuddyBoss (Admin end).
* Enhancement: Register scripts and styles properly after that enqueue.
* Enhancement: Improve function prefixing and add documentation.
* Security: Added ABSPATH for file protection.
* Fix: Restrict "Mark as Favorite" option for logged-out users.
* Cleanup: Remove vendor folder files.
* Cleanup: Remove redundant common CSS.

= 1.8.2 =
* Improved: License verification process for better reliability.
* Fixed: Issue with product activity not updating correctly after admin approval.
* Fixed: Favorite icon not displaying properly when a product is out of stock.
* Resolved: PHP warning that appeared when license information was missing.
* Enhanced: Code quality improvements for better performance and stability.

= 1.8.1 =
* Fix: Save WCFM option values with proper sanitization

= 1.8.0 =
* Fix: Resolved PHPCS issues in multiple files
* Update: Removed Hard-G dependency from plugin files for better compatibility.
* Enhancement: Added checks for WCFM Multivendor Marketplace-related settings.
* Update: Improved user notice CSS for better visual consistency.
* Enhancement: Managed store button UI with Youzify integration.
* Fix: Resolved WCFM Marketplace tab display issue with BuddyPress.
* Fix: Resolved order activity creation issue with Youzify.
* Fix: Replaced `filter_input` with `sanitize_text_field` to address PHP deprecation warnings.
* Fix: Added user notices and disabled settings when the BuddyPress activity component is deactivated.
* Fix: Addressed fatal error during activity creation when the activity component is disabled.
* Fix: Escaping function warnings resolved.
* Update: Upgraded Font Awesome version.

= 1.7.1 =
* Managed banner video UI in store listing.
* Fixed escaping function warnings in override template.
* Resolved translation issues.
* Applied dark mode fixes.
* Improved filter UI on store listing.
* Fixed issue with favorite icon not showing on related products.
* Fixed deprecated warning issues.

= 1.7.0 =
* Enhancement: Set single store default layout (#50).
* Update: Updated description for clarity (#50).
* Enhancement: Moved single store layout setting from customizer to backend settings for better management (#50).
* Fix: Applied UI fixes to the default layout (#50).
* Enhancement: Added single store layout setting and updated the default layout (#50).
* Update: Adjusted favorite product icon position on the product listing page (#50).
* Update: Renamed template file for better organization (#50).
* Enhancement: Added template override functionality (#50).
* Enhancement: Introduced icons for WooCommerce tabs (#50).
* Enhancement: Managed product listing UI with WCFM integration (#50).
* Fix: Resolved issue where licenses did not deactivate if the response failed.

= 1.6.6 =
* Fix: BP v12 fixes
* Fix: License issue

= 1.6.5 =
* Fix: (#49) Added condition for buddypress
* Fix: Fixed emdeb activity clickable
* Fix: (#49) Fixed review activity will only be genrate for login users
* Fix: (#49) Added order activity with backend option and UI
* Fix: (#49) Update review and new product activities label

= 1.6.4 =
* Fix: Dark mode fixes
* Fix: (#47) Loaded theme dynamic colors via variables

= 1.6.3 =
* Fix: (#46) Fixed managed favorite icon spacing
* Fix: (#46) Fixed UI with WCFM marketplace latest version
* Fix: (#46) Fixed WCFM marketplace widget UI

= 1.6.2 =
* Fix: Update activity action on product create activity
* Fix: Update activity action on product review activity
 
= 1.6.1 =
* Fix: Removed color scheme filters, new scheme applied via Reign theme

= 1.6.0 =
* Fix: fixed warning issue on favorite tab
* Fix: (#45) UI fixes and grunt fixes
* Fix: Fixed error issue when BP is not activated
* Fix: (#44) Fixed Error displaying on shop page

= 1.5.0 =
* Fix: changed priority of store link button
* Fix: (#37) Managed visit store button UI
* Fix: changed visit store link display position
* Fix: (#37) Added store icon on vendors profile tab link

= 1.4.0 =
* Fix: (#36) Fixed WCFM setting got reset on saving community setting
* Fix: Managed dashboard count
* Fix: Update mark-favuorite icons UI
* Fix: (#32) Fixed Favourite products remove issue on Profile tab
* Fix: (#31) Fixed plugin activation issue if wcfm not activate
* Fix: (#33) UI issue on store tab in frontend Issue fixed
* Fix: Managed dark mode UI

= 1.3.0 =
* Fix: Managed shop page fav icon UI and fixes
* New Update : Added store tab for vendor
* New Update: Added favorite product functionality
* Fix: Fixed - Reign theme Condition

= 1.2.0 =
* Fix: (#30) wcfm options saving warning issue

= 1.1.0 =
* Fix: (#21) Added activity on product creation and reviewed
* Fix: (#15) Managed store listing sidebar UI

= 1.0.0 =
* Initial release

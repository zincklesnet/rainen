=== BuddyPress Simple Events ===
Contributors: shanebp
Donate link: https://www.philopress.com/donate/
Tags: buddypress, events, event, buddyboss
Author: PhiloPress
Author URI: https://philopress.com/contact/
Plugin URI: https://philopress.com
Requires at least: 4.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 6.1
License: GPLv2 or later


== Description ==

A simple Events plugin for BuddyPress or the BuddyBoss Platform.
This plugin allows members to create, edit and delete Events from their profile.

It:

* provides a tab on each members' profile for front-end creation, editing and deletion
* has an option to use the Google Places API for creating locations
* has an option ro use Google Maps to show Event location
* creates a custom post type called 'event'
* uses WP and BP templates that can be overloaded
* includes a widget


It does NOT have:

* ticketing
* calendars - BUT should work with any WP Calendar that supports assigning custom post types
* recurring events

If you would like support for...

* search
* a map showing all Events
* a Settings screen for Map options
* an end Date
* Images
* an Attending button
* an option for assignment to a Group

... then you may be interested in [BuddyPress Simple Events Pro](https://www.philopress.com/products/buddypress-simple-events-pro/ "BuddyPress Simple Events Pro")

For more plugins, please visit [PhiloPress](https://www.philopress.com/ "PhiloPress")

== Installation ==

1. Upload the zip on the Plugins > Add screen in wp-admin

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Go to Settings -> BP Simple Events and enter your Google Maps API Key. If you don't have a Key - See the FAQ


== Frequently Asked Questions ==

= Do I need a Google Maps API Key?
Yes, if you select to use that option. If you need help, read this tutorial [Google Maps API Key](https://www.philopress.com/google-maps-api-key/ "Google Maps API Key")

= I created a future event but it shows only in Archive folder =
This is due to a difference between English and European preference re date format.

The fix is simple.
Open this file in a text editor:
bp-simple-events-pro/inc/js/events.js

Find: dateFormat: 'DD, MM d, yy'

For Europe, change it to: dateFormat: 'dd-mm-yy'  Or whatever you prefer

You will then need to :
* upload the edited events.js file
* clear your browser cache ( just the files ) to insure that the js file reloads
* edit any existing Events and reset the date

= MultiSite support? =

No.

= Calendar support? =

Yes - if the Calendar supports assigning custom post types


= Can I remove the option to assign a Category ? =

Yes, by using the filter hook.
Place this function in your theme > functions.php
`
	function pp_events_category_filter( $args ) {
		$args = array( "child_of" => -1 );
		return $args;
	}
	add_filter( "event_cat_args_filter", "pp_events_category_filter", 1, 1 );
`


== Screenshots ==
1. Shows the front-end Create an Event screen on a member profile
2. Shows the Dashboard > Settings screen


== Changelog ==

= 6.1 =
* improved templates

= 5.2 =
* improved time-picker

= 5.1 =
* improve Activity entry for a new Event


= 5.0 =
* add support for the BuddyBoss Platform
* add an option to NOT use Google Maps and thereby avoid the required Google key
* include missing gettext strings

= 4.2 =
* fix bug: do not show Event tab on user profile if the user role cannot create Events

= 4.1 =
* Fixes bug re removal of all assigned categories
* Adds a filter hook for listing categories:  'event_cat_args_filter'
* Remove 'Categories' label from the screen if there are no assigned categories

= 4.0 =
* Add support for Gutenberg

= 3.3 =
* Tested with WP 5.0

= 3.2 =
* Fix bug re the_content filter  prevent conflicts in some themes.

= 3.1 =
* Tweak the_content filter so that it does not conflict with some other plugins.

= 3.0 =
* Change template loading process. Use filter on the_content rather than load full template. This should be more compat with themes.

= 2.2.5 =
* Fix status when Event is restored from Trash, set to 'publish'

= 2.2.3 =
* Fix PHP Warning re incorrect function name in filter hook

= 2.2 =
*
= 2.1 =
* fixed bug re timestampSave post_date as the Event Start date so that Calendar plugins can be used

= 2.0 =
* Added requirement for Google Maps API Key

= 1.4.4 =
* tested in WP 4.3

= 1.4.3 =
* close the recent XSS vulnerability found in add_query_arg

= 1.4.2 =
* fix bug in WP templates filter

= 1.4.1 =
* typo in single template filter

= 1.4 =
* Tested in WP 4.2 & BP 2.2.3.1, tweak subnav creation, replace template_redirect with template_include

= 1.3.4 =
* Check if BP is activated

= 1.3.2 =
* Use trash hook instead of delete hook for cleanup on Event deletion

= 1.3 =
* Add multisite support, improved cleanup on trash

= 1.2 =
* Add file missing from last release.

= 1.1 =
* Refactored as a component.

= 1.0 =
* Initial release.



== Upgrade Notice ==

= 5.1 =
* improve Activity entry for a new Event

= 5.0 =
* add support for the BuddyBoss Platform
* add an option to NOT use Google Maps and thereby avoid the required Google key
* include missing gettext strings

= 4.2 =
* fix bug: do not show Event tab on user profile if the user role cannot create Events

= 4.1 =
* Fixes bug re removal of all assigned categories
* Adds a filter hook for listing categories:  'event_cat_args_filter'
* Remove 'Categories' label from the screen if there are no assigned categories

= 4.0 =
* Add support for Gutenberg

= 3.3 =
* Tested with WP 5.0

= 3.2 =
* Fix bug re the_content filter  prevent conflicts in some themes.

= 3.1 =
* Tweak the_content filter so that it does not conflict with some other plugins.

= 3.0 =
* Change template loading process. If you have overloaded or customized these templates - PLEASE make a backup of all template files before updating this plugin. And you may need to tweak these templates for your theme.

= 2.2.5 =
* Fix status when Event is restored from Trash, set to 'publish'

= 2.2.3 =
* Fix PHP Warning re incorrect function name in filter hook

= 2.2 =
* Save post_date as the Event Start date so that Calendar plugins can be used

= 2.1 =
* fixed bug re timestamp

= 2.0 =
* Added requirement for Google Maps API Key. If you are already using this plugin, you don't need this update.

= 1.4.4 =
* tested in WP 4.3

= 1.4.3 =
* close the recent XSS vulnerability found in add_query_arg

= 1.4.2 =
* fix bug in WP templates filter

= 1.4.1 =
* typo in single template filter

= 1.4 =
* Tested in WP 4.2 & BP 2.2.3.1, tweak subnav creation, replace template_redirect with template_include

= 1.3.4 =
* Check if BP is activated

= 1.3.2 =
* Use trash hook instead of delete hook for cleanup on Event deletion

= 1.3 =
* Add multisite support, improved cleanup on trash

= 1.2 =
* Add file missing from last release.

= 1.1 =
* Refactored as a component. Pagination fixed.


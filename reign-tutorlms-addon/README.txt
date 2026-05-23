=== Reign TutorLMS Addon ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com/
Requires at least: 5.0
Tested up to: 6.8.2
Stable tag: 2.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Reign TutorLMS Addon provides styling and theme compatibility enhancements for TutorLMS when used with the Reign Theme. This plugin focuses on visual design, layout options, and typography to ensure TutorLMS content looks perfect with your Reign theme.

= Key Features =
* Theme Styling - Applies Reign theme colors and design to TutorLMS
* Layout Options - Multiple layout styles for courses and instructors
* Typography Control - Custom font settings for TutorLMS content
* Mobile Optimization - Enhanced mobile responsive design
* Dark Mode Support - Compatible with Reign theme's dark mode

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/reign-tutorlms-addon/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Appearance > Reign Options > TutorLMS to configure styling options

== Shortcodes ==

= [reign_tutor_course] =
Enhanced version of TutorLMS's `[tutor_course]` shortcode with all original parameters PLUS additional features for enrolled courses and Reign theme styling.

All TutorLMS Parameters (same as [tutor_course]):
* `id` - Specific course IDs (comma-separated)
* `exclude_ids` - Course IDs to exclude (comma-separated)  
* `category` - Filter by category ID or slug (comma-separated)
* `count` - Number of courses to show
* `orderby` - Order by: ID, date, title, menu_order, rand
* `order` - ASC or DESC
* `course_filter` - Enable filtering: on/off
* `column_per_row` - Number of columns
* `show_pagination` - Show pagination: on/off

Enhanced Reign Parameters:
* `my_courses` - Show enrolled courses: yes/no (default: no)
* `user_id` - Specific user ID (optional - auto-detects from profile context)
* `show_progress` - Show course progress bars: yes/no (default: no, only for my_courses=yes)
* `course_status` - Filter enrolled courses: all, completed, in-progress, not-started (default: all)
* `layout_style` - Reign theme layout: default, card, minimal (default: default)

Auto-Detection of Profile User:
When `my_courses="yes"` is used, the shortcode automatically detects which user's courses to show:
1. BuddyPress/BuddyBoss: Automatically detects profile user from `bp_displayed_user_id()`
2. PeepSo: Uses PeepSo's native `get_view_user_id()` method for accurate profile detection
3. WordPress Author Pages: Uses author ID from `is_author()`
4. Custom Profile URLs: Detects from /profile/username, /member/username patterns
5. Manual Override: Use `user_id="123"` to specify a specific user
6. Fallback: Shows current logged-in user's courses

Examples:

Regular course listing (same as TutorLMS):
`[reign_tutor_course count="6" category="web-development" course_filter="on"]`

My enrolled courses (current user):
`[reign_tutor_course my_courses="yes" show_progress="yes" count="4"]`

Profile page courses (auto-detects user):
`[reign_tutor_course my_courses="yes" show_progress="yes" course_status="completed"]`

Specific user's courses (manual override):
`[reign_tutor_course my_courses="yes" user_id="123" course_status="all"]`

Enhanced styling:
`[reign_tutor_course count="8" layout_style="card" column_per_row="4"]`

= [reign_course_categories] =
Display course categories in a grid using TutorLMS native design.

Attributes:
* `count` - Number of categories to show (default: 8)
* `columns` - Number of columns: 1, 2, 3, 4, or 6 (default: 4)
* `orderby` - Order by: name, count (default: name)
* `order` - ASC or DESC (default: ASC)
* `hide_empty` - Hide empty categories: yes/no (default: yes)
* `show_count` - Show course count: yes/no (default: yes)
* `show_image` - Show category thumbnail: yes/no (default: yes)

Example:
`[reign_course_categories count="6" columns="3" orderby="count" order="DESC"]`

== Copyright ==

Reign Tutor LMS Addon, Copyright 2020, Wbcom Designs
Reign Tutor LMS Addon is distributed under the terms of the GNU GPL

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
= 2.0.0 =
* Major Update: Complete theme integration overhaul
* New: Beautiful theme styling with Reign color scheme integration
* New: Multiple layout options for courses and instructors
* New: Typography controls for perfect text styling
* New: Dark mode support throughout TutorLMS
* New: Enhanced mobile responsive design
* New: Platform independence - works with or without BuddyPress/BuddyBoss
* New: Smart platform auto-detection
* New: Powerful shortcodes for course display
* Improved: Cleaner, faster codebase focused on theme integration
* Improved: Better compatibility with TutorLMS Pro features
* Improved: Unified BuddyPress/BuddyBoss implementation
* Updated: Minimum PHP version to 7.4 for better performance
* Updated: Minimum WordPress version to 5.0

= 1.6.1 =  
* Fix: Resolved fatal error on BuddyPress/BuddyBoss deactivation.  
* Update: Updated `select2` CSS/JS path.  
* Enhancement: Minified CSS and JS for optimized performance.  

= 1.6.0 =  
* Fixed: PHP warnings when no post data is present while adding course activity content.  
* Fixed: Issue with activity creation when other activity settings are enabled.  
* Fixed: Issue generating course activity when a course gets approved by the admin.  
* Added: Course featured image and course description to course activity for enhanced visibility.

= 1.5.0 =
* Fixed: Tab not visible on the front end for Course Manage page with Youzify.
* Fixed: UI issue on Course Manage page in Groups with Youzify.
* Fixed: Fatal error when adding a course to a group.
* Fixed: Disabling Plugins Group integration disables Tutor LMS Pro settings.
* Fixed: Reign Settings for Tutor LMS not visible with BuddyBoss.
* Fixed: PHP warning on Reign settings page.
* Fixed: Tutor LMS posts in activity feed issue.
* Fixed: Youzify creates its own activity for course creation.
* Fixed: Issue with sitewide activity creation for courses.
* Fixed: Lesson and course creation activities not created with Youzify.
* Fixed: Duplicate lesson added activity in groups.
* Fixed: PHP fatal error when activating Elementor.
* Fixed: Fatal error when activating BuddyPress.
* Fixed: Duplicate activity posts for Tutor LMS in activity feed.
* Fixed: Issue when no option is selected in "Display Course Activity."
* Fixed: Assignment activity creation now dependent on Tutor LMS Pro.
* Fixed: Navigation icons issue in dark mode.
* Fixed: Course Assignment UI select issue.
* Removed: Hard-coded folder for course activity options.
* Removed: Course settings option on Group creation page.
* Improved: Managed UI compatibility with Youzify.
* Improved: Added backend Tutor LMS settings tab icon.
* Improved: Integration of BuddyBoss options for generating activities for courses, lessons, quizzes, and assignments.
* Improved: PHPCS compliance across multiple files (`edd-plugin-license.php`, `class-reign-tutorlms-addon-admin.php`, `class-reign-tutorlms-addon-public.php`, `edit-courses.php`).
* Updated: Notice for BuddyBoss Pro and Tutor LMS Pro compatibility.

= 1.4.5 =
* Fix: Course activity issue with BuddyBoss.
* Fix: Manage quiz UI fixes
* Fix: HTML tag in the description.
* Fix: PHP warning
* Fix: Issue with instructor role.
* Fix: PHP Warning after course completion.
* Fix: Multiple course creation activities are generated on course update
* Fix: Fatal error with WP6.5.5 on plugin activation
* Fix: UI/Dark mode
* Fix: Deprecated warnings fixes

= 1.4.4 =
* Fix: Fixed compability with lates PHP version 
* Fix: Fixed UI with dark mode
* Fix: Fixed PHP fatal error on plugin activation
* Fix: Fixed multiple course creation activities are generating on course update

= 1.4.3 =
* Fix: Issue with Tutor Instructor role(BB Platform)
* Fix: Lesson activity issue
* Fix: Quiz creating double activity
* Fix: Issue with BB Platform
* Fix: Warnings
* Fix: Update setting description
* Fix: UI fixes
* Fix: Issue with BB Platform Pro
* Fix: Hide all related settings if group sync is disabled
* Fix: Course visibility issue
* Fix: Individual course activity display will be based on global activities
* Fix: Managed default color scheme
* Fix: Remove unused css
* Fix: Fatal error issue
* Fix: Managed course activities setting UI
* Fix: Text domain issue
* Fix: Course, lesson, quiz, assignment creation activity enhancement
* Fix: Added posts in activity feed enhancement
* Fix: BP group course integration
* Fix: Issue with license is not deactivated if response is failed
* Fix: (#54) Update code to save group integration data
* Fix: (#54) Group extension is not loading
* Fix: (#54)Added meta boxes with BuddyBoss and BuddyPress
* Fix: (#54)Manage and save admin options
* Fix: (#54)Added options and managed files
* Fix: (#54) Managed single course and lesson UI
* Fix: (#54) Managed dashboard page UI

= 1.4.2 =
* Fix: Text domain issue
* Fix: BP v12 fixes
* Fix: License issue
* Fix: (#50) Managed quiz UI
* Fix: (#52) The user is not enrolled in a course still he can see the course tab on the assigned BP Group
* Fix: (#53) User profile course tab
* Fix: (#50) Dark mode and UI fixes
* Fix: (#49) Warning issue
* Fix: (#47,48) Plugin activation and profile tab issue
* Fix: (#46) Show users enrolled courses on their BuddyPress profile
* Fix: (#45) Added course tab in the assigned group
* Fix: (#44) Warnings on the BP Group after it is assigned to a course

= 1.4.1 =
* Fix: Reign Compatibility Fixes

= 1.4.0 =
* Fix: (#41) Text domain fixes
* Fix: (#41) Warning
* Fix: (#42) BuddyPress deactive error.
* Fix: (#42) Hide the related settings
* Fix: (#35) Show warning message if group component disable
* Fix: (#41) Text domain fixes
* Fix: Set Only Inactive when license key deactivate
* Fix: (#37)Fixed join leave group button
* Fix: (#40)Fixed issue in earn certificate activity
* Fix: Embed group activities
* Fix: (#34) Group Integration

= 1.3.1 =
* Fix: (#33) Loaded theme colors dynamically using CSS variables
* Fix: Added compability with TutorLMS least version

= 1.3.0 =
* Fix: (#32) Managed UI fixes with latest update
* Fix: (#31) UI fixes with tutorlms pro 2.0.10
* Fix: (#30) Managed course archive access issue
* Fix: Removed color scheme filters, scheme apply via theme

= 1.2.0 =
* Fix: (#14) Managed dashboard page UI and addon fixes
* Fix: Improved UI Courses, Dashboard, Archive Courses Filter

= 1.1.0 =
* Fix: (#11) Managed zoom integration UI fixes with bb platform pro
* Fix: (#10) Update instructor single page UI

= 1.0 =
* Initial release.

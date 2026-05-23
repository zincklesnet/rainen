=== BP Post Status ===
Contributors: Venutius
Tags: BuddyPress, members only posts, group posts, friends only posts, groups, follow
Tested up to: 6.6.1
Stable tag: 2.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate Link: https://paypal.me/GeorgeChaplin
Plugin URI: www.wordpress.org/plugins/bp-post-status/

Adds BuddyPress status options for posts - Group posts (public, site members only and group only, Members Only, Followers, Following and Friends only post statuses.

Adds a new "Group Post" tab to the group pages showing all posts assigned to the group. Group Admins can also set one of the group posts to be the group homepage.

Adds a "My Posts" tab to the users profile which displays any posts they have created, a Pending tab displays any posts waiting moderation and for Editor a Moderation tab shows all site posts in pending status..

Comes with group admin options to control exactly who can post and create notifications. Site settings allow full control over the way the features are used.


== Description ==

New: Allows hidden and Private groups to have public and site members only posts without compromising the group privacy.

A feature I've always thought was missing from BuddyPress was integration with WordPresses biggest asset - blog posts. This plugin is my attempt to do just that. First of all it implements BuddyPress Post Statuses, so a post can be shared just to friends, site members followers or those the user follows. In addition groups can have their own posts and these can be made public, site members only or only available to group members regardless of the group status.

Once you have posts linked to groups an immediate thought is to use a group based post as the groups home-page - this plugin enables that with group admin able to easily choose from the groups list of posts which one is to be used as the homepage.

Groups and users get a dedicated page for their posts, this page is intended not only to display these blogs to other users but also to help the user manage their posts from the front-end, so posts can be made sticky, deleted and if BP Site Post is installed, created and edited, all from the front end.

Simply install the plugin, edit your post and select Members or Friends Only, or choose to publish into a group you are a member of. In addition you can turn on notifications for these new post types and control exactly who can post or send notifications.

Note that users will need to have WordPress edit_posts as a capability to create new pending posts and publish_posts capability in order to select the publish statuses.

If you have groups that would like to assign posts to the group, and share either publicly, only to site members or only to group membes then this plugin should interest you.
Also you may have a need to allow site members the opportunity to create posts only to share amongst friends, or only to logged in site members, then this plugin adds that functionality.

Groups can also choose to use a group post as a homepage, using this option group activity is moved to an activity tab and the home tab is used for the selected post. This means group managers have full flexibility as to the content they choose to display in the homepage; shortcodes can be used to display summaries of group forums and activity for example.

It's integrated with BuddyPress Activity and Notifications and supports BP Follow.

If you want to allow front end posting I've also created <a href="https://wordpress.org/plugins/bp-site-post/">BP Site Post</a> which works with BP Post Status and allows users to create and edit their posts from the front-end.

The activity posts try to be appropriate to the security level assigned to the post - private group posts only post their activity into the group the post is assigned to.
Site and group managers can choose to enable the ability to trigger a notification to the target group with the posts publication.

Groups with group posts enabled have an optional "Group Posts" tab showing all posts assigned to the group. If a private or hidden group chooses to publish public posts then this directory will be visible to non group members, but it will only display posts that have been made public. Similarly if a private group makes their homepage public then the landing page will be displayed for visiting non-members.

There's extensive manageability:

Group managers can choose what membership level to allow group post creation, and also the membership level required to be able to trigger post notifications. These options are available in the groups management pages in the settings tab. They can also choose to display a "Group Posts" tab in the group.

In addition, the site admin has a settings page at Dashboard>>Settings>>BP Post Status. Here the five different status types can be disabled site-wide, and the site-wide controls for the minimum user role required to create posts and trigger notifications is set.

There is also an option to have post revisions create activity updates, as opposed to the activity only being updated when the post is first created. When it is enabled you can set the minimum time allowed between posting updates.

A new My Posts menu option has been added to users profiles, this displays their posts and enabled posts to be made sticky ( in My Posts ) or deleted. If BP Site Post is loaded then an edit link can also be added to this view. For guests viewing My Posts only published posts will be displayed but for the user all posts (including pending) will be displayed.  

Editors ( members with the 'edit_others_posts' capability ) get a Moderation page in their profile ( if there are pending posts to approve ) for easy access to the approvals queue. This page shows the full content of the post to be approved and have links to delete, publish or edit the post.

Members with pending posts and with the 'edit_posts' capability get a Pending Posts page in their profile ( if they have pending posts ) so that pending posts can be reviewed and edited.

Note regarding group_only_pending posts

When a group_post_pending post is selected, a notification email is sent to the admin of the site so they can authorize the post. However, I felt that this was a less than optimal solution since ideally the group creator should have a say in if a post gets published to their group. One of the issues is that it's not possible to give group creators the ability to manage only posts in their group - they either edit_others_posts for all posts on the site or they simply do not have this capability.

I found a solution to this using Automattics Co-Authors Plus plugin, since this allows multiple authors to be assigned to posts. I've therefore added a check to see if Co-Authors Plus is active and if so the group creator will be automatically added as an author of all posts set for their group. This being the case they will be send a notification email and they will be able to edit the post content and if they have publish_posts capability they will be able to publish the post to their group. However, if they only have edit_posts, they will need to contact the site admin to have the post published. However, at the time of writng this causes an error when viewing the profile my-posts page (10/03/2019), this has been reported and is being worked on.

The template pages can be overloaded by copying them to themes/your-child-theme/bpps/ and editing them as suits.

There is a shortcode - [bppss_group_posts group_id="34" ] which allows the group posts list to be displayed on any page.

The only downside of this plugin is with regards to Gutenberg - the new text editor in WordPress, sadly Gutenberg does not support custom post statuses so if you want to use this you will have to install the Classic Editor plugin and stick with the classic for now, hopefully this situation will be rectified soon.

== Installation ==

Option 1.

1. From the Admin>>Plugins>>Add New page, search for BP Post Status.
2. When you have located the plugin, click on "Install" and then "Activate".
3. Go to posts and you will see the new status options.
4. Enable notifications in the Dashboard>>Settings>>BP Post Status page.
5. Go to group management and choose which group will have posts and notifications.

With the zip file:

Option 2

1. Upzip the plugin into it's directory/file structure
2. Upload BP Post Status structure to the /wp-content/plugins/ directory.
3. Activate the plugin through the Admin>>Plugins menu.

Option 3

1. Go to Admin>>Plugins>>Add New>>Upload page.
2. Select the zip file and choose upload.
3. Activate the plugin.

== Screenshots ==

1. screenshot-1.png - The new post statuses drop-down.
2. screenshot-2.png - Group settings page.
3. screenshot-3.png - Site settings page.

== Frequently Asked Questions ==

Q. Can I assign a post to multiple groups?

A. No, only one group can be assigned to a post.

Q. Can group managers over-ride the site settings? 

A. No, the site settings take precedence, site admin will need to work with group admin to make sure each group has the features it needs.

== Props ==

* This plugin was created by merging some features from three other plugins - Peter Shaw's LH Logged in Post Status, BuddyDev's Blog Categories for Groups and Imath's WP Statuses. This made the creation of this plugin a lot easier than it would have been if built from scratch. Props to these great developers!

== Changelog ==

= 2.0.3 = 

* Fix: Fixes potential crash when accessing the admin menu page.

= 2.0.2 =

* Fix: Corrected error with admin post list.

= 2.0.2 =

* Fix: Corrected conflict with BuddyPress Group Email Subscription.
* Fix: Corrected conflict with some non-public post types (Noptin).

= 2.0.1 =

* Fix: Compatibility with PHP 8.2
* Update: continued updating of escaping and sanitization.

= 2.0.0

* Updates: Numerous updates to update escaping of inputs and outputs
* Fix: Group pages now working.

= 1.9.0 =

* Updates: Numerous updates to correct depricated functions and improved escaping of inputs and outputs.

= 1.8.14 =

* 13/04/2024

* Fix: Replaced depricated functions

= 1.8.13 =

*Fix: Replaced depricated functions.

= 1.8.12 =

* 15/01/2021

* Update: Bumping wp version supported.

= 1.8.11 =

* 13/05/2019

* Fix: Corrected undefined variable notice in bp-members.php.

= 1.8.10 =

* 13/05/2019

* Fix: Now site admin can automatically post to groups.
* Fix: Group Posts Add New post button now only displays when the user has the ability to post to the group.

= 1.8.9 =

* 12/05/2019

* Fix: Further updates for site post all members posting.

= 1.8.8 =

* 11/05/2019

* Fix: Allow profile Add Post menu item when Site Post is set to allow posting for all members.

= 1.8.7 =

* 16/04/2019

* Fix: Corrected error when groups are not active.

= 1.8.6 =

* 14/04/2019

* Fix: Made Ajax call unique.
* Fix: Corrected error when only one group post is there to be counted.
* Fix: Corrected php variable error in sticky-posts.php/

= 1.8.5 =

* 20/03/2019

* Fix: Corrected function not found notice in Group Posts single post view.
* Fix: Corrected Undefined variable error in bpps_get_user_post_count.

= 1.8.4 =

* 17/03/2019

* Fix: Updated checks for Current_user_id in bpps_get_user_posts_count
* Fix: Corrected SQL for single post types and post status in bpps_get_user_post_count.

= 1.8.3 =

* 15/03/2019

* Fix: corrected php error in myposts.php.
* Fix: reworked SQL to avoid syntax errors where there are no group-posts for a group.
* Fix: Corrected group posts count by removing any home page as this is not displayed in the group-posts page.
* Fix: Added checks in bpps_get_user_posts_count() to handle instances where a not logged in user is viewing the my posts tab.

= 1.8.2 =

* 13/03/2019

* Fix: Corrected error preventing friends and group posts being counted correctly.

= 1.8.1 =

* 13/03/2019

* Fix: Corrected count to only count search-able post types. This was exaggerating the post count for admin users in particular

= 1.8.0 =

* 13/03/2019

* New: Revised my posts query to find all authors posts.
* New: Improved user post count algorithm.
* New: Added Post Moderation tab in groups for group admin to moderate submitted posts from group members.
* Fix: corrected error displaying pending group posts outside of the group
* Fix: Corrected edge case where authors who had left a group were excluded from viewing their own posts/

= 1.7.7 =

* 12/03/2019

* New: Pending group posts now display the group name in the My Posts Moderation page.
* New: The my Posts Moderation page now shows the full content of the post and includes a Publish button.
* Fix: corrected missing Pending Posts toolbar menu item for standard users.

= 1.7.6 =

* 11/03/2019

* Fix: Refactored bp_members.php.

= 1.7.5 =

* 11/03/2019

* Fix: Corrected bp toolbar menu loading issues.
* New: Created separate Pending Posts and Moderation profile pages.

= 1.7.4 =

* 10/03/2019

* New: Added Pending Posts page for users who can edit_others_posts, so that editors have and easy approvals queue.
* Fix: Corrected my posts count for Nouveau.
* Fix: Corrected error caused by BP Profile nav trying to load in admin view.
* New: Added post count to group posts nav for both Legacy and Nouveau themes.
* New: Added bpps_get_group_post_count( $group_id ) function. 

= 1.7.3 =

* 09/03/2019

* New: Added posts count to My Posts tab link.
* Fix: Corrected not found error with My Posts.
* Fix: Corrected duplicate My Posts Menu error with BP Site Post loaded.
* Fix: Corrected isset() for $approve_email in bp-emails.php.
* Fix: Corrected Sticky posts delete link positioning error.

= 1.7.2 =

* 07/03/2019

* Fix: corrected invalid function call error in single-post.php

= 1.7.1 =

* 07/03/2019

* Fix: corrected BP Nav setup timing.
* Fix: Corrected error passing existing post__not_in queries forward.
* New: My Posts and Group Posts page now gives authors and group admin the option to delete posts straight from the front end.
* New: My Posts and Group Posts now have a sticky posts section.
* Fix: Adjusted plugin text-domain to wp standards.

= 1.7.0 =

* 05/03/2019

* New: Added My Posts tab to BP Profile.
* New: added search bar to My Posts and Group Posts pages.
* New: modified My Posts so that drafts are displayed when author is viewing.
* New: Added post status view to My Posts authors view.

= 1.6.4 =

* 02/03/2019

* Fix: corrected settings issue for post content display.
* New: Added filter to display correct link for group posts.

= 1.6.3 =

* 01/03/2019

* Imp: Refactored bpps_get_content().
* New: Added bpps_the_excerpt() function.
* Imp: Refactored bpps_the_summary().

= 1.6.2 =

* 01/03/2019

* Fix: corrected bpps_get_content() to remove excerpt default read more link.

= 1.6.1 =

* 26/02/2019

* Fix: Corrected redundant code in bp-activity.php.
* Fix: Corrected issue where bpps_get_content would send no content;
* Fix: Corrected issue with bpps_update_activity() where group_id is not found.
* Fix: Corrected missing variable error for group members viewing posts in group.
* Fix: Group Post page pagination fixed.
* Fix: Corrected error in returning posts query in user_can_view() functions.
* New: Group home directory will now only be the default tab if a post has been assigned to it.
* New: Refactored bpps_get_post_permalink() to allow it to be used for any post status.
* New: Added My Posts Template in support of BP Site Post.
* New: Admins now see posts of any status in the group post page.

= 1.6.0 =

* 25/02/2019

* New: Group posts can now be constrained within the group pages, it's an admin option in NP Post Status settings.
* New: Private and hidden groups can choose to make posts and their homepage public whilst being constrained in the groups directory.
* Fix: Numerous functions have been repurposed and updated. 

= 1.5.1 =

* 24/02/2019

* Fix: Corrected error causing site pages to load incorrectly.

= 1.5.0 =

* 24/02/2019

* New: Added Admin option to constrain group posts to be within the group, meaning that the permalink shown in the posts loop will be within the group, not as any other post would be. What this means is that with this setting enabled public and members only posts for a private group will not be visible to non group members as they would need membership to view the post within the group.
* Fix: Corrected error preventing the group home from being disabled.
* Fix: Corrected error with translation text domain.

= 1.4.6 ==

* 17/02/2019

* Fix: Corrected error getting group id in group creation screen.

= 1.4.5 =

* 03/07/2018

* Update: added shortcode specific template - shortcode-posts.php to allow for differing views to be produced to the group posts posts.php template file.

= 1.4.4 =

* 03/07/2018

* New: Added Settings and Review link to plugins directory view.
* New: Added shortcode to display group posts list.
* New: Added edit link in the group posts areas (Home and single view).
* Update: Removed group home from the group posts list in groups.
* Update: Removed redundant author info from single-post.php.

= 1.4.3 =

* 01/07/2018

* Feature Change ! Group Home now enabled in all groups by default.
* Fix: removed disable Group Posts from the group creation screen to prevent group corruption, Group Posts can be disabled once the group has been created.

= 1.4.2 =

* 26/06/2018

* New: Added Notifications support for Followed and Following posts.
* New: Made page templates overloadable.
* New: Added activity updates for revisions, with settings to disable and set a minimum delay between updates.
* Fix: Revised conditional formula for excluded posts to minimise errors with count().
* Fix: revised group home so that only the post content is displayed.
* Fix: Removed redundant menu links.
* Fix: Revised Group Only posts activity updates.
* Fix: Corrected blank notifications issue when bbPress is loaded.

= 1.4.1 =

* 26/06/2018

* Fix: Revised conditional formula for excluded posts to minimise errors with count().
* Fix: revised group home so that only the post content is displayed.
* Fix: Removed redundant menu links

= 1.4.0 =

* 25/06/2018

* New: Added support for BP Follow; Now you can set posts to be viewed only by Followers or Following. Notifications not enabled yet.

= 1.3.2 =

* 25/06/2018

* Fix: Corrected error causing Group Only posts to be public.

= 1.3.1 =

* 24/06/2018

* Fix: corrected code causing count() error.
* Fix: Removed PHP undeclared variable warning.
* Enhancement: Revised group posts display to any post type, not just posts.

= 1.3.0 =

* 19/05/2018

* New: Added pending post statuses to Group and Members only posts, users with 'edit_posts' capability will only see pending options for those post types.
* New: Added pending notifications: When a post is saved as group post pending or members only pending an options notification will be sent to the site admin and the group creator (for group posts).
* New: Optional sending of approval request emails to site admin and Group creators.
* New: Added support for Co-Authors plus - when active, group creators are automatically assigned as co-authors for group-only posts assigned to their group.

= 1.2.12 =

* 16/05/2018

* Fix: More elegant checks for BP active.

= 1.2.11 =

* 16/05/2018

* Fix: Corrected white screen error if BP is not loaded.

= 1.2.10 =

* 09/05/2018

* Fix: updated translation textdomain used in bp-statuses classes

= 1.2.9 =

* 09/05/2018

* Fix: refined admin settings html.

= 1.2.8 =

* New: Added option to email post author by email when a post gets a comment.

= 1.2.7 =

* 06/05/2018

* Fix: Revised code for get_group_permalink and related functions.

= 1.2.6 =

* 06/07/2018

* Fix: Corrected taxonomy lookup in is_single function.

= 1.2.5 =

* 03/05/2018

* Fix: Group creation save error resolved.


= 1.2.4 =

* 27/04/2018

* Fix: Corrected undefined function error in group posts tab.

= 1.2.3 =

* 27/04/2018

* New: Moved groups settings into separate tab.
* Fix: Group menus now honour sitewide and group settings.

= 1.2.2 =

* 26/04/2018

* New: added content summary function to allow for alternative activity feed and posts loop content.
* New: Added site admin setting to choose default activity content type.
* New: Added group admin setting to choose activity content type.
* Fix: Updated translation for status labels.
* Fix: Corrected single view logic error.
* Fix: Corrected undefined function error in single view.
* Fix: Corrected undefined function error in posts loop.
* Fix: Removed date class from category posts element.
* Fix: Prevented random homepage display when no group homepage is set.

= 1.2.1 =

* 22/04/2018

* Fix: Group posts now update last activity time.
* Fix: Group posts activity link now points to the group based page, not the post permalink.
* Fix: Activity entries now displaying excerpt correctly.
* Fix: Amended group activity text.

= 1.2.0 =

* 22/04/2018

* New: Added group homepage option - groups can now choose a post to use as the group homepage.

= 1.1.1 =

* 21/04/2018

* Fix: Correcting BP Statuses Core Status load error

= 1.1.0 =

* 21/04/2018

* Fix: Public and Members only group_post now show activity site-wide.
* Fix: Public and Members only group_post now notify to all members.
* New: Added group Posts tab 1n group page showing all posts assigned to the group.
* New: Added group setting to enable/disable Group Posts tab per group.

= 1.0.0 =

* 20/04/2018

* Initial Release.

= Upgrade Notice =

= 2.0.3 = 

Fixes potential crash when accessing the admin menu page.


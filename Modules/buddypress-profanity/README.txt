=== BuddyPress Profanity ===
Contributors: wbcomdesigns, vapvarun
Donate link: https://wbcomdesigns.com
Tags: buddypress, profanity, filter, community, activity, comments, messages, content-moderation, family-friendly
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Keep your BuddyPress community family-friendly by filtering inappropriate language from posts, comments, and messages with advanced profanity detection.

== Description ==

**BuddyPress Profanity** is a comprehensive content filtering plugin designed to maintain a safe and family-friendly environment in your BuddyPress community. The plugin intelligently filters offensive language from various content types while preserving the integrity of your community discussions.

= Key Features =

* **Smart Content Filtering** - Filter profanity from activity updates, comments, private messages, and bbPress forums
* **Advanced Word Management** - Easy keyword management with bulk import functionality and 200+ default inappropriate terms
* **Flexible Replacement Options** - Multiple rendering modes with various replacement characters (*, $, ?, etc.)
* **Privacy Protection** - Automatic email address and phone number masking to prevent harvesting
* **Real-time Filtering** - Content filtering without affecting database storage using advanced regex patterns
* **Developer-Friendly** - Extensive filter hooks, clean code architecture, and translation-ready
* **Multisite Compatible** - Full support for WordPress multisite networks
* **Performance Optimized** - Cached regex patterns and conditional asset loading for minimal overhead

= Supported Content Types =

* BuddyPress activity streams, comments, and private messages
* bbPress forum topics and replies  
* BuddyPress notifications and user-generated content
* Email addresses and phone numbers (privacy masking)

= Professional Configuration =

* Toggle between whole-word and partial word matching
* Configure case sensitivity and custom replacement characters
* Intuitive admin dashboard with real-time preview
* Bulk keyword management and comprehensive help documentation

= Community & Support =

Join our thriving community of developers and administrators:
* [Documentation](https://docs.wbcomdesigns.com/doc_category/buddypress-profanity/)
* [Support Forum](https://wbcomdesigns.com/support/)
* [Feature Requests](https://wbcomdesigns.com/contact/)
* [GitHub Repository](https://github.com/wbcomdesigns/buddypress-profanity)

= Why Choose BuddyPress Profanity? =

1. **Proven Track Record**: Trusted by 10,000+ active installations
2. **Regular Updates**: Consistent updates with new features and bug fixes
3. **Expert Support**: Dedicated support team with BuddyPress expertise
4. **Community Driven**: Feature development based on user feedback
5. **Performance First**: Optimized for speed and minimal resource usage

== Installation ==

= Automatic Installation =

1. Go to your WordPress admin dashboard
2. Navigate to Plugins → Add New
3. Search for "BuddyPress Profanity"
4. Click "Install Now" then "Activate"
5. Configure the plugin at BuddyPress → Profanity Settings

= Manual Installation =

1. Download the plugin zip file
2. Extract the contents to `/wp-content/plugins/buddypress-profanity/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to the settings page to configure filtering options

= Requirements =

* WordPress 5.0 or higher
* BuddyPress 6.0 or higher (required)
* PHP 7.4 or higher (PHP 8.0+ recommended)
* MySQL 5.6 or higher

= Post-Installation Setup =

1. **Configure Keywords**: Add your custom profanity list or use defaults
2. **Set Filtering Scope**: Choose which content types to filter
3. **Customize Replacement**: Select preferred masking characters and styles
4. **Test Filtering**: Create test content to verify functionality
5. **Enable Privacy Features**: Configure email/phone masking if desired

== Frequently Asked Questions ==

= Does this plugin require BuddyPress to function? =

Yes, BuddyPress must be installed and activated for this plugin to work properly. The plugin is specifically designed to integrate with BuddyPress components.

= Will this plugin affect my existing content in the database? =

No, the plugin only filters content when it's displayed to users. Your original content remains unchanged in the database, ensuring data integrity.

= Can I add custom words to the profanity filter? =

Absolutely! You can add, edit, or remove keywords through the admin interface. The plugin also supports bulk import of custom word lists.

= Does the plugin work with bbPress forums? =

Yes, the plugin includes full bbPress integration, filtering content in forum topics, replies, and other bbPress-generated content.

= How does the email/phone masking feature work? =

When enabled, the plugin automatically detects email addresses and phone numbers in content and replaces them with masked versions (e.g., u***r@e****e.com) to protect user privacy.

= Is the plugin compatible with caching plugins? =

Yes, the plugin is designed to work seamlessly with popular caching solutions. Since filtering happens at the display level, cached content remains properly filtered.

= Can I customize the replacement characters? =

Yes, you can choose from several predefined characters or use the developer filter hooks to implement custom replacement patterns.

= Does the plugin support multisite installations? =

Yes, the plugin is fully compatible with WordPress multisite networks and can be activated network-wide or on individual sites.

= How do I handle false positives in the filter? =

You can remove specific words from the filter list or adjust the case sensitivity and strict filtering options to reduce false positives.

= Is technical support available? =

Yes, we provide support through our [helpdesk](https://wbcomdesigns.com/support/). Premium support is available for Pro version users.

== Screenshots ==

1. General Settings - Configure profanity filtering options, keyword management, and replacement settings
2. Keyword Management - Easy interface for adding, editing, and bulk importing profanity terms
3. Content Type Selection - Choose which BuddyPress components should be filtered
4. Rendering Options - Customize how filtered words appear to users
5. Privacy Settings - Configure email and phone number masking options
6. Activity Stream - Example of filtered content in BuddyPress activity feed
7. Messages Interface - Profanity filtering in private messages
8. bbPress Integration - Forum content filtering in action

== Changelog ==

= 2.0.1 =
**Fixed**
* Resolved fatal error on activity pages in certain configurations
* Fixed bbPress-related options visibility when bbPress is inactive
* Corrected email input masking in activity and messages
* Fixed blocked keywords filtering for forum activities
* Resolved settings save notifications and backend UI issues with BB platform
* Fixed various PHPCS coding standard violations
* Corrected license page handling and reading errors

**Improved**
* Enhanced admin script loading for better performance
* Optimized and minified CSS/JS files with RTL support
* Improved email and phone masking algorithms
* Refactored content filtering into unified methods for consistency
* Enhanced multisite compatibility and network activation support

**Updated**
* Refreshed admin interface with better responsive design
* Updated documentation links and licensing information
* Improved language strings for consistency and grammar
* Enhanced error handling and validation throughout

**Removed**
* Cleaned up unused files and redundant code
* Removed deprecated function calls and hardcoded dependencies

= 2.0.0 =
**Added**
* Complete admin interface redesign with modern UI components
* Advanced email and phone number masking capabilities
* bbPress forum integration for comprehensive content filtering
* RTL language support with dedicated stylesheets
* Enhanced bulk keyword import functionality
* Improved caching system for better performance

**Enhanced**
* Optimized filtering algorithms for faster processing
* Better multisite support with network-level configuration
* Improved mobile responsiveness across all admin pages
* Enhanced security with better input validation and sanitization

**Fixed**
* Resolved compatibility issues with latest BuddyPress versions
* Fixed several edge cases in content filtering logic
* Improved handling of special characters in keywords
* Better error handling for malformed content

= 1.9.9 =
**Improved**
* Removed dependency on hardG library for better consistency
* Updated language translation files with new strings
* Enhanced backend options with responsive design fixes
* Optimized database queries for improved performance

**Fixed**
* Resolved conflicts with certain theme frameworks
* Fixed translation loading issues in some environments
* Corrected minor UI inconsistencies in admin dashboard

= 1.9.8 =
**Enhanced**
* Streamlined `filter_content` method for better performance
* Improved content sanitization mechanisms throughout
* Enhanced multisite support with better network management
* Upgraded admin interface components for consistency

**Fixed**
* Resolved caching conflicts with optimization plugins
* Fixed edge cases in regex pattern matching
* Improved compatibility with custom BuddyPress themes

= 1.9.7 =
**Fixed**
* Resolved license deactivation issues when API response fails
* Improved error handling for network connectivity problems
* Enhanced fallback mechanisms for license validation

= 1.9.6 =
**Updated**
* Refreshed plugin promotional banners and assets
* Updated external documentation and support links
* Enhanced compatibility with PHP 8.2 environments

**Fixed**
* Resolved deprecated function warnings in newer PHP versions
* Fixed minor CSS conflicts with certain admin themes

= 1.9.5 =
**Fixed**
* Resolved filtering issues in BuddyPress notification system
* Fixed plugin redirect conflicts during bulk plugin activation
* Improved activation process for multisite installations

= 1.9.4 =
**Improved**
* Enhanced bulk save functionality with better visual feedback
* Optimized admin interface for improved user experience
* Added progress indicators for bulk operations

= 1.9.3 =
**Fixed**
* Resolved keyword import functionality issues
* Fixed file upload validation and processing
* Improved error messages for failed operations

= 1.9.2 =
**Fixed**
* Resolved admin notice conflicts with BuddyBoss platform
* Improved compatibility with BuddyBoss theme frameworks
* Fixed styling conflicts in admin dashboard

= 1.9.0 =
**Enhanced**
* Complete admin UI redesign with modern iconography
* Improved navigation and user experience
* Enhanced visual hierarchy and accessibility features

= 1.8.0 =
**Added**
* Full bbPress forums, topics, and replies support
* Enhanced backend administration interface
* Improved performance monitoring and diagnostics

= 1.7.0 =
**Added**
* BP Better Messages plugin integration support
* Enhanced bulk import options for custom word lists
* Advanced filtering options with pattern recognition

= 1.5.0 =
**Added**
* Dynamic keyword addition interface
* Real-time filtering preview capabilities
* Enhanced user permission management

= 1.0.0 =
**Initial Release**
* Core profanity filtering functionality
* BuddyPress activity and message filtering
* Basic keyword management system
* Configurable replacement options

== Upgrade Notice ==

= 2.0.1 =
This update includes critical bug fixes and performance improvements. Update immediately to resolve activity page errors and enhance overall stability.

= 2.0.0 =
Major update with significant UI improvements, new filtering capabilities, and enhanced performance. Backup your settings before upgrading.

= 1.9.7 =
Important fix for license management issues. Users experiencing license validation problems should update immediately.

= 1.8.0 =
Adds comprehensive bbPress support. If you use bbPress forums, this update is highly recommended for complete content filtering coverage.
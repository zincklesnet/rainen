=== Reign ===

Requires at least: 4.5
Tested up to: 6.8.3
Version: 8.0.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Reign is a premium WordPress theme perfect for a BuddyPress-based community site. It is responsive, clean, modern, flat, and minimal.

* Mobile-first, Responsive Layout
* Custom Colors
* Post Formats

== Installation ==

1. Go to Appearance > Themes in your admin panel and click the Add New button.
2. Click Upload and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.

== Copyright ==

Reign WordPress Theme, Copyright 2016, Wbcom Designs
Reign is distributed under the terms of the GNU GPL.

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

= 8.0.3 - June 2026 =

Maintenance release for 8.0.0. Wider dark mode coverage, conditional asset-loading fixes, opt-in button styling so plugin and component controls keep their own look, and tighter BuddyNext integration.

* Improve  - Dark mode styling now covers the BuddyBoss Platform and Shortcodes for BuddyPress Pro interfaces.
* Improve  - Adjusted the footer widget heading font size for link titles.
* Improve  - Refined the header light and dark mode toggle presentation.
* Improve  - When BuddyNext is active, the login, register, and reset password links point to the BuddyNext authentication hub.
* Fix      - Auto (System) color mode now follows the visitor's operating-system light or dark preference.
* Fix      - Block editor and plugin colors now match your active Reign brand color in both light and dark mode.
* Fix      - Menu icons no longer leak onto post and widget titles, and the fallback link icon stays inside the left panel instead of the header and mobile menus.
* Fix      - Integration stylesheets and scripts, including the LearnDash dashboard, now load only on the pages that need them.
* Fix      - Corrected the BuddyBoss reactions position in activity listings.
* Fix      - Fixed header icon alignment on mobile when BuddyNext is active.
* Fix      - Theme button styling is now opt-in, so plugin and component controls keep their own look: WPMediaVerse controls, the BuddyX light and dark toggle, and the BuddyNext header menu no longer pick up the theme accent fill.
* Fix      - Easy Digital Downloads price-option checkbox styling no longer affects unrelated checkboxes elsewhere on the site.

= 8.0.1 - June 2026 =

Maintenance release for 8.0.0. Rebuilt Get Started onboarding and a header-icon fix for BuddyNext communities.

* New      - Rebuilt Get Started screen as a Wbcom onboarding hub with a one-click installer for companion plugins.
* Improve  - Get Started and theme options now share the companion-family layout, with a Build More page.
* Fix      - Restored the message and notification header icons that were dropped on Customizer save when BuddyNext is active without BuddyPress.
* Dev      - Replaced the bundled TGMPA library with a lightweight in-house companion installer.

= 8.0.0 - June 2026 =

Major release. The Customizer is rebuilt in-house and loads faster, with a new visitor light and dark mode toggle, ready-made Site Skins, and lighter front-end loading. Your saved settings carry over unchanged.

* New      - Rebuilt Customizer that loads faster and is simpler to navigate.
* New      - Light, dark, and auto color mode toggle in the header and mobile menu, with each visitor's choice remembered as they browse.
* New      - 8 ready-made Site Skins (Cool, Dark, Editorial, Minimal, Monochrome, Pastel, Vibrant, Warm) under Color Options > Site Skin.
* New      - Mobile Topbar Content control that keeps the top bar on a single tidy row on phones by showing either your contact info or your social links, under Header > Top Bar.
* New      - Full set of Left Panel size controls for open width, collapsed width, header height, item spacing, and section heading size; defaults match the previous look so nothing shifts.
* New      - 27 ready-to-use block patterns (hero, about, features, social proof, pricing and FAQ, call to action, footer, and post listings) for the block editor.
* New      - Larger Google Fonts library in the typography picker, with only the fonts you choose loaded on your site.
* New      - Separate top, right, bottom, and left spacing controls for the footer and copyright areas.
* New      - Block editor color palette now matches your active Reign color scheme.
* New      - Get Started > Theme Setup adds a "Re-run 8.0.0 Setup" button that safely re-applies migration defaults for sites that skipped the one-time update, without overwriting any saved value.
* Improve  - Font picker groups your theme fonts and Google Fonts with a search box, and previously selected fonts keep working.
* Improve  - Left Panel settings reorganized into clear groups (Behavior, Size & Spacing, Icon, and Menu Text) for easier setup.
* Improve  - Sub Header controls now clearly explain when a sub header shows or hides and which setting takes priority.
* Improve  - Reign blue restored as the default accent color, with a brighter accent in dark mode.
* Fix      - On and off toggles across the theme now reliably take effect, including sticky header, top bar, scroll-up button, site loader, left panel, sub header, mobile logo, related posts, social share, breadcrumb, sign-in popup, WooCommerce options, and the login screen.
* Fix      - Restored Customizer controls that had stopped showing: Sub Header Height, Blogs Per Row, Blog Excerpt Length, and the Forms color options.
* Fix      - Layout Settings is now one clear panel grouping each content type's Archive, Single, and Search options.
* Fix      - Removed a duplicate dark mode control so your default mode lives in one place under Color Options > Light & Dark Mode.
* Fix      - Top bar no longer breaks into two rows or clips text on phones, and the sign-in popup close button is now easier to tap.
* Fix      - Dark mode styling now applies consistently across all theme areas.
* Fix      - Resolved a PHP notice that could appear in some configurations.
* Compat   - Your saved Customizer settings, colors, and dark mode preference carry over unchanged after updating.
* Compat   - Your existing color scheme is preserved exactly; the new Site Skins are optional and only change your colors if you choose one.
* Compat   - Because several options now take effect that previously did not, review your settings after updating (especially Header > Top Bar, Site Loader, and Scroll Up) to confirm they match your intent.

= 7.9.9 =
* New: Added Customizer fields for login and register form labels with HTML/link support.
* Fix: Dark mode logo swap now applied to BuddyPress register page.
* Fix: "Scroll to Top" JS string is now translatable.
* Fix: Resolved dark mode issue in Access Plan description for LifterLMS.
* Fix: Registered `sfwd-quiz` with the Reign Customizer layout system.
* Fix: Login page logo no longer appears twice.
* Fix: Added missing course-level comment section for LearnDash.
* Fix: Friends connections icon toggle now works with BP Activity Share Pro plugin.
* Fix: Resolved logo visibility issue in Firefox browser.
* Update: Updated recommended plugins list.

= 7.9.8 =
* Security: Patched stored XSS vulnerability in social profile fields.
* Security: Removed unauthenticated theme customizer reset via `?devmod_remove_theme_mod`.
* Security: Added nonce, capability, and sanitization checks to `reign_save_post_meta()`.
* Security: Sanitized social link settings and profile URLs.
* Security: Escaped previously unescaped meta values in post format meta box output.
* Security: Hardened `login.php` and fixed duplicate site name on the login page.
* New: Added dark mode and color scheme support for Stachethemes Event Calendar (dashboard + frontend).
* New: Added store manager tab UI styling for WCFM.
* Fix: Notifications and messages count now clears at zero without requiring a page refresh.
* Fix: WooCommerce - guarded the additional information tab when product context is missing.
* Fix: Restored native select appearance in WP admin forms.
* Fix: `[reign_display_posts]` now bounds the default `posts_per_page`.
* Fix: `reign_login_title()` correctly returns the site name instead of an empty option.
* Fix: `load_color_dark_palette()` now emits the full dark palette.
* Fix: PeepSo redirect URLs now use `home_url()` instead of `site_url()` for multisite compatibility.
* Fix: Removed deprecated reflection access from theme slug lookup.
* Improvement: PHPCS fixes across multiple files.
* Improvement: Added `Tested up to` and `Requires PHP` headers; bumped composer PHP requirement to 7.4.
* Improvement: Updated `reign.pot` translation template.

= 7.9.7 =
* Fix: Avatar moderation issue with latest BP Moderation Pro.
* Fix: Dark mode fixes with BP Better Messages.
* Fix: Tabs active UI with BP Better Messages.
* Fix: Activity like avatars not showing and UI improvements.
* Fix: Font preload for primary GT Walsheim weight.
* New: Show/hide setting for Activity Like Avatars feature.
* Accessibility: Add :focus-visible styles for button accessibility.

= 7.9.6 =
* Update: Updated buddypress-activity-share-pro version.
* Update: Updated wbcom-essential version.
* Accessibility: Various accessibility improvements.
* Performance: Replaced Font Awesome all.min.css with minimal 3-weight subset.
* New: EDD Cart Preview slide-out integration and cart icon improvements.
* Fix: Image alt attribute added.
* Fix: FluentCart cart icon click and live count issues.

= 7.9.5 =
* New: Added icons for BuddyPress Member Reviews tabs.
* New: Embedded videos and content now display responsively across all screen sizes.
* New: Improved styling for all WordPress core blocks (headings, lists, tables, quotes, and more).
* New: Added compatibility with the Shoplentor plugin.
* New: Site logo and title now appear on the registration and activation pages.
* New: Online status indicator color for members.
* Improved: Better Messages now uses the theme color scheme by default.
* Improved: Better dark mode support for BuddyPress Member Reviews and skills cloud.
* Improved: Cleaner appearance for Gutenberg editor blocks.
* Improved: Better heading sizes and spacing for improved readability.
* Improved: Visual improvements for BuddyPress stats, media popups, and BuddyBoss photos page.
* Fixed: Avatar moderation not working correctly with BuddyPress Moderation Pro.
* Fixed: rtMedia display issues on mobile devices.
* Fixed: Sticky posts not pinning correctly.
* Fixed: Profile header overlapping user information in Header Layout 4.
* Fixed: Member status badge alignment issue.
* Fixed: Duplicate message action buttons showing on the BP Better Messages directory page.
* Fixed: LearnDash course layout breaking when lessons contain PDF or audio files.
* Fixed: Performance improvement by removing unnecessary cache clearing on every page load.

= 7.9.4 =
* New: Added Theme JSON Bridge for WordPress block editor integration.
* New: Added BP Better Messages light and dark mode support.
* Improved: WP Job Manager listing widget UI and image size handling.
* Improved: Dark mode UI fixes with WC Vendors.
* Improved: Dokan store list UI.
* Improved: Review and Nouveau sidebar styling updates.
* Improved: Product Roadmap UI fixes.
* Improved: Posts navigation (prev/next) UI.
* Improved: PeepSo override templates and UI fixes.
* Improved: Dark mode fixes with Advanced Username Manager.
* Improved: BB Platform toast messages notice dark mode styling.
* Improved: Scheduled posts popup dark mode with BB Platform.
* Fixed: PeepSo hamburger icon not working over activity stream.
* Fixed: Fatal Error with undefined function reign_get_default_catalog_view_mod() in WooCommerce loop template.
* Updated: Recommended plugins versions.

= 7.9.3 =
* New: Added Product Roadmap plugin support with full styling.
* Improved: Dark mode compatibility across multiple plugins including BuddyPress Reactions, BP Member Blog, rtMedia, WP Job Manager, and Business Profile.
* Improved: BuddyPress activity link preview styling in dark mode.
* Improved: Member social media links now include proper URL validation and protocol handling.
* Improved: Groups and members directory layout for better visual consistency.
* Improved: BB Platform activity feature image and reaction count display.
* Improved: WP Job Manager widgets and calendar UI styling.
* Improved: BP Groups Review button alignment and UI.
* Fixed: BuddyPress cover image path displaying incorrect directory structure.
* Fixed: Activity reply button causing unexpected scroll/jump behavior.
* Fixed: Multiple theme check warnings including translation functions, escaping, and deprecated constants.
* Fixed: Missing theme_location parameter in navigation menu.
* Updated: WC Vendors Pro single vendor template for latest version compatibility.
* Updated: Required plugin versions for Wbcom Essential and BuddyPress Reactions.

= 7.9.2 =
* Updated: Kirki framework to version 5.1.1.
* Improved: Build process and asset optimization using Grunt.
* Fixed: Dark mode preference not saving for logged-out users.
* Fixed: Horizontal scroll appearing on split login screen.
* Updated: Override templates to match the latest BuddyBoss Platform version.

= 7.9.1 =
* Improvement: Added matching percentage display on member profiles and directory listings.
* Improvement: Updated UI compatibility with BuddyBoss Sharing and BP Activity Share Pro.
* Improvement: Improved store sidebar layout for WCFM for a cleaner shop experience.
* Improvement: Prevented empty sidebars from appearing on pages for a more polished layout.
* Improvement: Updated header notification dropdown styling for better readability.
* Improvement: Enhanced activity profile photo UI for a better visual experience.
* Improvement: Improved profile completion widget UI and resolved conflicts with third-party plugins.
* Improvement: Registered and improved LearnDash course and group single sidebars.
* Improvement: Updated LearnDash modern layout UI for consistency.
* Improvement: Applied multiple dark mode refinements across WPForo, BP Check-ins Pro, and BuddyPress Status.
* Fix: Resolved dropdown closing issue when interacting with WooCommerce cart content.
* Fix: Corrected Reign News widget block not working.
* Fix: Fixed cover image not displaying correctly on group activity pages.
* Fix: Addressed deprecation warnings related to jQuery event shorthand functions.
* Cleanup: Removed unused sidebar templates and duplicate function calls to improve theme performance.

= 7.9.0 =
* Improvement: Added FluentCart integration for smoother eCommerce and checkout experience.
* Improvement: Updated product brands list UI for better organization and visibility.
* Improvement: Refined sidebar layout and position for improved page balance.
* Improvement: Enhanced edit profile fields UI for a cleaner and more intuitive user experience.
* Improvement: Improved accessibility with better color contrast and readability throughout the theme.
* Improvement: Updated WordPress core button styling for consistency across pages.
* Improvement: Separated single member and groups menu icons for clearer navigation.
* Improvement: Enhanced dark mode styling across BuddyPress Private Community and various theme sections.
* Improvement: General UI refinements and layout improvements across multiple components.
* Fix: Resolved security issues related to insecure REST API endpoints.
* Fix: Addressed BuddyPress signup prefill security concerns.
* Fix: Hidden product archive section from the Customizer to simplify options.

= 7.8.8 =
- Fixed: Event Tickets UI display issues
- Fixed: Unable to click on generating tickets or RSVPs on events
- Fixed: Dark mode compatibility with BuddyPress User Todo List
- Fixed: Dark mode issues with BuddyPress Moderation Pro
- Fixed: Gutenberg editor cover title alignment in block view
- Fixed: UI issues with WCFM plugin
- Fixed: Text domain consistency across theme files
- Improved: LearnDash terminology now uses dynamic label functions instead of hardcoded text
- Updated: SASS files documentation and comments
- Maintenance: Build process optimization with Grunt

= 7.8.7 =
- Improved: UI fixes with BuddyPress Moderation Pro
- Fixed: Dark mode related issues
- Added: Profile display tab icon
- Fixed: General PHP warnings
- Improved: Dark mode compatibility with Better Messages
- Removed: Unused CSS files for optimization
- Improved: Dark mode fixes with BuddyPress Hashtags
- Updated: Activity title handling with BuddyBoss Platform
- Improved: Action buttons UI with WC Vendors
- Improved: Review feedback UI with WC Vendors
- Fixed: Extra white space in navigation without icons
- Updated: Override templates of BuddyBoss Platform with latest update
- Optimized: kirki-addon.php for better performance
- Updated: Customizer color options and added divider
- Improved: WP Job Manager bookmarks form UI
- Updated: Course progress position for better layout
- Improved: UI fixes with Dokan and WP Job Manager
- Improved: Social icons UI on resume preview
- Improved: UI fixes for latest LearnDash version

= 7.8.6 =
* Enhancement: Manage UI and call minified JS file.
* Enhancement: Add complete security plugin compatibility for Reign login/signup forms.
* Enhancement: Manage activity comment "Read More" button UI.
* Enhancement: Manage single LearnDash group layout UI with Udemy-style layout.
* Fix: Dark mode compatibility issues with the latest BuddyBoss Platform update.

= 7.8.5 =
* Fix: Left sidebar issue with BuddyBoss Platform
* Fix: Directory pages sidebar issue with BuddyPress
* Tweak: Managed membership listing spacing with LifterLMS
* Enhancement: Added reusable user profile block with PeepSo, BuddyPress, and WordPress support
* Tweak: Updated function name to `render_reign_user_profile_block` for theme consistency
* Tweak: Added filters for show/hide toggler
* Tweak: Managed toggler avatar style for same user
* Fix: PeepSo userbar not showing on mobile view
* Tweak: Managed PeepSo user profile navigation for mobile view
* Fix: PeepSo mobile header display issues
* Fix: Dark mode compatibility with BuddyPress group review

= 7.8.4 =
* Tweak: Adjusted search box width to align with the site container for consistent layout.
* Tweak: Added new setting to control logo size on tablet and mobile views.
* Tweak: Managed header layout 4 spacing when no navigation menu is present.
* Fix: Removed animation glitch while opening the cart dropdown.
* Fix: Prevented duplicate product images from showing inside Elementor editor.
* Fix: Corrected sidebar visibility issue when searching Elementor keywords.
* Fix: Restricted Elementor slider initialization strictly to editor mode.
* Fix: Resolved WooCommerce category slider issue on Elementor pages.

= 7.8.3 =
* Enhancement: Revolutionary smart widget detection - assets load only for widgets actually displayed on each page
* Enhancement: Intelligent asset loading for 22+ plugin types including BuddyPress, WooCommerce, LearnDash, and more
* Enhancement: Improved header dropdown functionality with better cart, notifications, and messages display
* Enhancement: Enhanced WooCommerce mini cart with better product alignment and variation display
* Enhancement: Optimized asset loading with smart conditional loading for all third-party plugins
* Enhancement: Improved LearnDash review forms with consistent styling and better user experience
* Enhancement: Enhanced course and group review display with cleaner designs and better spacing
* Enhancement: Added smart tab memory - tabs now remember your position when you reload pages
* Enhancement: Faster site loading with optimized performance improvements
* Enhancement: Better WooCommerce integration to prevent page conflicts
* Performance: Ultra-precise asset loading - only loads CSS/JS for widgets active on current page
* Performance: Eliminated unnecessary database operations for better speed
* Performance: Pattern-based widget detection with zero performance overhead
* Fix: Resolved header dropdown display issues across all notification types
* Fix: Fixed mini cart product information alignment and variation display
* Fix: Resolved UI inconsistencies in review systems
* Fix: Corrected compatibility issues between WooCommerce and LearnDash
* Fix: Improved 404 page mapping to prevent performance issues with missing assets
* Fix: Resolved login and registration popup errors with better security handling
* Fix: Enhanced error messages in login system for better user experience
* Modernization: Removed outdated AMP support for improved site speed
* Modernization: Removed debug console logging for cleaner production code
* Modernization: Advanced widget-to-plugin mapping with automatic pattern recognition

= 7.8.2 =
* Tweak: Improved members listing design for Layout 4 for a more modern look.
* Tweak: Updated UI compatibility with the latest BuddyBoss Platform changes.
* Tweak: Enhanced Directorist sidebar widgets styling for better consistency.
* Tweak: Refined single product group pricing display for clearer presentation.
* Tweak: Adjusted Elementor editor behavior for Members and Groups carousel widgets.
* Tweak: Updated WooCommerce template overrides to match the latest version.
* Tweak: Optimized Directorist integration, excluding "At Biz Dir" post type where unnecessary.
* Cleanup: Removed unused and outdated Wbcom Essentials-related code.
* Cleanup: Removed BuddyPress legacy template files.
* Cleanup: Removed duplicate code from Divi Theme Builder and Beaver Builder themer layouts.
* Fix: Resolved undefined array key error during recommended plugin installation.
* Fix: Corrected display issues in Reign News widget when used inside Elementor pages.
* Fix: Fixed visibility issue with user menu links when using default color scheme.
* Fix: Addressed login form styling conflict with Solid Security Pro plugin.
* Fix: Corrected warnings in Sitewide Activity widget.
* Fix: Fixed carousel widgets not working properly in Elementor editor.
* Fix: Managed outdated WooCommerce template file warnings.
* Fix: Addressed flickity slider CSS loading error.

= 7.8.1 =
* Added: Theme location registration for flexible template assignment
* Added: Custom header and footer functionality
* Added: Modern image format support for better performance
* Added: Image compression for faster loading
* Added: Essential-only functionality mode for streamlined installs
* Improved: Elementor Pro products widget UI handling
* Improved: Directorist plugin UI compatibility with latest version
* Improved: Settings tab UI with BuddyPress Resume Manager
* Optimized: WP_Query calls in Latest News widget with caching mechanism

= 7.8.0 =
* New: Extended support for major page builders – Elementor, WPBakery (Visual Composer), Beaver Builder, Thrive Architect, Divi.
* New: Created a unified CSS file to ensure consistent styling across all supported page builders.
* New: SureCart support implemented for enhanced eCommerce compatibility.
* Enhancement: Updated Wbcom Demo Installer and Wbcom Essential plugin versions.
* Enhancement: Improved UI of the cart icon in mobile view, including accurate item count.
* Enhancement: Added subheader display on single product pages.
* Enhancement: Improved sticky menu color options and managed shop page layout for full-width display.
* Enhancement: Better dark mode handling for shop prices.
* Enhancement: Updated Grunt build process and added `composer.json`.
* Fix: Fixed Elementor-related CSS issues.
* Fix: Resolved private group request avatar UI inconsistencies.
* Fix: Managed cart icon UI and color for “Buy Now” button.
* Fix: Corrected Z-index issue in Kirki select options.
* Fix: Improved display of WooCommerce product price in footer.
* Fix: Display notice when addon shortcodes are missing.
* Fix: Added condition for loading Flickity slider and optimized `mscrollbar.min.js`.
* Fix: Removed unused `select2.min.js` file and redundant functions.

= 7.7.7 =
* New: LearnDash CSS now loads only on relevant pages for improved performance.
* New: WooCommerce cart block UI adjustments for better frontend experience.
* New: Separate Paid Memberships Pro CSS for improved compatibility and styling.
* Enhancement: Elementor-based search widget UI refined.
* Enhancement: Improved BuddyPress login/register form button styling.
* Enhancement: Forum archive card and cover layout warning fixes.
* Enhancement: Improved dark mode toggle behavior—applies class only when enabled.
* Enhancement: Updated outdated WooCommerce template files for compatibility.
* Optimization: Removed unused font icon library to reduce bloat.
* Optimization: Reduced unused CSS code and files.
* Optimization: Sass deprecation warnings fixed.
* Optimization: Updated escaping functions and CSS files.
* Optimization: Removed BuddyPress Legacy support for cleaner codebase.

= 7.7.6 =
* Fixed: Global instance memory leak for improved performance and stability.
* Fixed: BuddyPress Status compatibility issues with dark mode.
* Fixed: Product filter block rendering issue on the WooCommerce shop page.
* Fixed: Member review layout and post link accessibility.
* Fixed: Left sidebar not rendering properly on forum pages.
* Improved: Site logo switching behavior now responds instantly in dark mode.
* Improved: UI for action buttons on member layout 3 and LearnDash overrides.
* Improved: Max Mega Menu behavior on mobile view.
* Improved: RTL support for user menu navigation slider.
* Improved: Group nav icons for to-do lists.
* Updated: BuddyBoss and Dokan template overrides for better integration.
* Added: WooCommerce product layouts shortcode.
* Added: Separate button styling file to prevent conflict with Dokan.
* Added: Activity topics support when using BB Platform.

= 7.7.5 =
* Improved: Easy Digital Downloads checkout page UI and removed unused files/functions.
* Fixed: Deprecated function warnings and demo importer issues.
* Enhanced: Group messaging UI, including textarea and tab layouts.
* Updated: BuddyBoss Platform template overrides and related styling.
* Added: Icons and UI updates for Membership Requests and Mutual Connections tabs.
* Refined: Styling for LearnDash, LifterLMS pagination, and advanced profile search.
* Fixed: Elementor content showing in Terms and Privacy pages.
* Improved: Layout logic for container, sidebar, and body fonts based on active plugins.
* Fixed: Visibility of horizontal navigation slides.
* Updated: WooCommerce template files to avoid outdated copies.
* Cleaned: Removed duplicate CSS, unused selectors, and images.
* Improved: Forum panel toggle setting and UI fixes for date fields.
* Updated: Various language strings and script loading behaviors.

= 7.7.4 =
* Added: Contact tab icon and improved styling for a consistent design.
* Fixed: Grammar issues and updated various text strings for clarity.
* Fixed: Dark mode styling issues with WP Stories integration.
* Updated: Job dashboard UI for better usability and consistency.
* Improved: Comment section styling with Sensei LMS integration.
* Added: Contact icon to user profile for quicker communication.
* Removed: Single course sidebar and applied layout improvements.
* Improved: Member description spacing for better readability.
* Updated: “More” menu design in member and group navigation.
* Fixed: Various UI glitches when using the BuddyBoss Platform.
* Fixed: Additional dark mode adjustments for consistent appearance.
* Optimized: LifterLMS assets now load only on relevant pages.
* Fixed: Login form nonce verification issue for secure access.
* Cleaned: Removed unused and redundant code.
* Updated: Post comments section with a cleaner, modern look.
* Improved: Content filtering to allow safe HTML when using LifterLMS.
* Fixed: Header “More” menu breaking into two lines on smaller screens.
* Updated: Template formatting and added inline documentation.
* Enhanced: Vertical navigation UI for BuddyBoss users.
* Improved: “Send Invites” group tab design for better usability.
* Updated: Template overrides for Dokan compatibility.
* Fixed: Dark mode UI across more sections for visual consistency.
* Updated: Required plugins list to ensure compatibility.
* Improved: “What’s New” dark mode styling with BuddyBoss.
* Updated: WP Job Manager Addon CSS loading via theme for better control.
* Updated: Scroll-up behavior for specific style layout.
* Improved: Job application logic for layout three of the addon.
* Enhanced: Job listing and resume UI with WP Job Manager Addon.
* Updated: Reign WC Vendors, Sensei, and TutorLMS addon CSS loading through theme.
* Updated: Reign WCFM addon CSS loading for better integration.
* Improved: WooCommerce checkout form styling in dark mode.
* Updated: Sidebar layout for Easy Digital Downloads category and tag archives.
* Fixed: LearnDash single course header displaying raw HTML content.

= 7.7.3 =
* Updated: Improved language strings and text corrections throughout the theme for better clarity and localization.  
* Updated: Escaping functions and translation string formatting to ensure compatibility with multilingual setups.  
* Fixed: Text domain issues for more consistent translations.  
* Fixed: Header notification double-tap and avatar display issues on mobile (especially iPhones).  
* Fixed: “Mark as Read” button alignment in header notifications on mobile devices.  
* Updated: Notification eye icon for a more polished UI.  
* Updated: Integrated Reign GeoDirectory Addon CSS at the theme level for better styling consistency.

= 7.7.2 =
* Added: Support for Beaver Builder header and footer integration.  
* Added: Title support for header icons.  
* Added: Modern login default text for improved UX.  
* Added: BuddyLists dark mode compatibility.  
* Fix: Followers and following count not displaying with BB Platform.  
* Fix: UI inconsistencies with BB Platform latest update.  
* Fix: Console deprecated warnings and JS-related issues.  
* Fix: Fatal error on Beaver Builder deactivation.  
* Fix: Reset password issue with Paid Memberships Pro.  
* Fix: PHP warning when incorrect license key is added.  
* Fix: Console warning related to password strength script.  
* Fix: Extra border appearing in the search UI.  
* Updated: `entry.php` and PeepSo override templates.  
* Updated: Password strength UI for clarity.  
* Updated: Vendor JS file loading via separate folder for better organization.  
* Updated: Reaction system to display emoji name instead of ID.  
* Improved: Page layout compatibility with Divi Builder and WPBakery.  
* Improved: UI consistency with BP Shortcode Pro and Youzify.  
* Improved: Form element placeholder colors for better accessibility.  
* Improved: Default profile/group avatar and cover settings with BB Platform.  
* Improved: Custom heading display on initial render.  
* Improved: General theme UI across multiple integrations.  

= 7.7.1 =
* Added: Advanced Username Manager tab icon for better navigation.  
* Added: BuddyLists tab icon for improved user experience.  
* Fix: Dark mode issues with the latest WooCommerce version.  
* Fix: UI issues with BP Better Messages latest version.  
* Fix: Date not visible in BP Better Messages conversations.  
* Fix: Mobile menu dropdown toggle not clickable on iPhones.  
* Updated: WooCommerce checkout Select2 fields UI for a smoother checkout process.  
* Updated: Reign LearnDash addon to resolve template override issues.  
* Updated: Password strength UI for better usability.  

= 7.7.0 =
* Added: Apply filters for excluded group types.  
* Added: Group review icon for better group functionality.  
* Added: View stories icon for improved user navigation.  
* Added: WP Job Manager, GeoDirectory, TutorLMS, and LifterLMS support.  
* Added: BuddyWoo cart, checkout, and order tab icons.  
* Fix: Avatar and cover image activity generation for hidden groups.  
* Fix: Reign popup issue displaying Elementor content.  
* Fix: Sidebar not working on LearnDash archive pages.  
* Fix: Deprecated jQuery(window).load() function for compatibility.  
* Fix: Fatal error when BB components are disabled.  
* Fix: Outdated WooCommerce template files issue.  
* Updated: Demo installer version for better setup.  
* Updated: EDD checkout customization function.  
* Updated: BuddyPress override templates for better customization.  
* Updated: BP Nouveau override template styling.  
* Updated: Friendship request JS and loader animations.  
* Updated: WooCommerce product details form spacing.  
* Updated: Tooltip calls and member layout UI in BB Platform.  
* Improved: Dark mode compatibility with BP Business Profile, BP Member Blog, BuddyPress Polls, BuddyPress Check-Ins Pro, BuddyPress Hashtag, and Dokan.  
* Improved: MediaPress photos listing UI for better media management.  
* Improved: Default course template calls in Reign LearnDash addon.  
* Improved: User menu dropdown UI when adding multiple menu items.  
* Improved: BP Edit Activity UI and text handling.  
* Improved: BuddyWoo UI and integration with Youzify.  
* Improved: BuddyWoo order tab pagination.  
* Improved: Vendor biography media images for better display.  
* Improved: JavaScript files and calls for better performance.  
* Improved: Excluded e-floating-buttons post type for optimization.  
* Improved: Apply filters to show background images dynamically.  

= 7.6.6 =
* Added: LearnDash Dashboard dark mode support for improved user experience.
* Fix: UI issues with Youzify integration.
* Updated: Members layout 3 image for better visual presentation.
* Improved: Member directory layout 1 UI for cleaner navigation.
* Added: Functionality to manage shortcode layouts with Wbcom Essential plugin.
* Improved: Friend request tab UI with layout 3 for better usability.
* Added: Functions comments for better code maintainability.
* Fix: Warnings while changing profile actions for smoother user interaction.
* Improved: Member directory layout 3 for BuddyPress compatibility.
* Improved: Member directory layout 3 for BuddyBoss Platform compatibility.
* Updated: BP profile content with status UI enhancements.

= 7.6.5 =
* Manage header style with BuddyBoss Platform Pro.
* Update `bp-plugin-support.scss`.
* Fix issues with dark mode.
* Add spacing to profile with status content.
* Fix backend warnings.
* Manage edit profile screen data with BuddyPress plugins.
* Remove total subscribe and update checkout form fields UI.
* Manage group action buttons UI with BuddyBoss Platform.
* Manage edit profile screen data with BP Status plugin.
* Manage EDD checkout fields.
* Manage BP Member Blog tag list UI.
* Remove extra border on tabbed links view.
* Register sidebar for WC Vendor store and UI fixes.
* Fix BuddyPress Status template to match profile edit.
* Update EDD price options UI.
* Show EDD cart item notice.

= 7.6.4 =
* Managed Easy Digital Downloads (EDD) price options and shortcodes UI.
* Improved EDD loading and card saving button UI.
* Fixed media issues with 404 page mapping.
* Resolved Slick Slider issue when three menu items appear.
* Fixed dark mode issues for LearnDash focus mode.
* Added renewal and promotion functionality improvements.
* Enhanced checkout page UI for EDD.
* Added support for WCFM plugin.
* Added support for WC Vendors plugin.
* Updated postmeta select2 CSS/JS for better functionality.
* Improved dark mode compatibility with "Who Viewed My Profile" and BP Statistics tab.
* Fixed button UI issues with BP Moderation Pro.
* Resolved issue where custom login page logo did not display with WordPress 6.7.

= 7.6.2 =
* Fixed WooCommerce preloader issue to prevent endless loading.
* Resolved outdated copies of WooCommerce template files.
* Added light/dark default mode setting for better theme customization.
* Loaded dark mode class based on apply filters for enhanced flexibility.
* Removed custom font size settings for Font Awesome icons.
* Added support for Sensei LMS integration.
* Managed Dokan single store profile frame border radius for improved appearance.
* Improved layouts for Dokan single store pages.
* Updated WooCommerce tabs slider arrow for better navigation.
* Reverted support for WP Job Manager in specific scenarios.
* Managed visibility of the course progress bar for better user experience.
* Added support for Dokan marketplace integration.
* Updated plugin support file call paths for better compatibility.
* Added support for LearnDash LMS Integration.
* Managed existing layout setup compatibility with the Reign Addon.
* Removed duplicate sections when the Reign Addon is activated.
* Added WP Job Manager support for enhanced job listing functionality.

= 7.6.1 =
* Fix: Message icon issue with BP Better Messages.
* Fix: Removed custom script when sticky sidebar is enabled.

= 7.6.0 =
* Managed visibility of icons for children menu items.  
* Removed unwanted CSS related to the Wbcom notification area.  
* Improved UI with the Member Review plugin integration.  
* Updated BuddyPress Newsfeed plugin tab icons.  
* Updated `pmpro.scss` with latest enhancements.  
* Applied UI fixes and removed override files for PMPro update.  
* Added BuddyVendor preferences icon.  
* Introduced forum archive listing per row setting and fixed escaping functions.  
* Improved UI for the store coming soon screen.  
* Updated single member and group sidebar calls for BuddyPress legacy.  
* Fixed sidebar issues and applied template overrides for rtMedia.  
* Fixed escaping functions in `single-event.php`.  
* Updated PeepSo override files with escaping function fixes.  
* Removed deprecated `reign_comment_form_defaults` function.  
* Removed unnecessary EDD override files.  
* Fixed single group sidebar and improved escaping functions.  
* Managed breadcrumbs on search result pages.  
* Updated and optimized `content-audio.php`.  
* Enhanced `activity-widgets.php` performance.  
* Improved `rg-woo-product-category.php` with optimizations.  
* Improved PeepSo templates including `focus.php`, `navbar.php`, and `group-header.php`.  
* Enhanced `user-menu.php`, `search.php`, and `register-menu.php`.  
* Improved `notification.php`, `message.php`, and `login.php`.  
* Applied UI improvements for `friends-request.php` and `reign-panel.php`.  
* Updated mobile header templates and managed site title/description.  
* Updated video location and removed target="_blank" from Customizer links.  

= 7.5.8 =
* Update: WooCommerce tabs tooltip direction in mobile view.
* Update: Script and style enqueue handle for optimization.
* Fix: Warnings related to WooCommerce tabs.
* Enhancement: Managed WooCommerce tabs UI in mobile view for improved user experience.
* Enhancement: Breadcrumbs for posts page.
* Update: Backend settings logo, display name, and general fixes.
* Enhancement: Added Sensei backend option menu icon.
* Fix: Managed PeepSo profile/group cover settings and backend UI improvements.
* Update: Reign theme setting backend UI updates.
* New Feature: Dokan support for better multi-vendor functionality.
* Update: Improved message UI for BuddyBoss platform integration.
* Enhancement: Improved EDD download layouts.
* Enhancement: Improved reign_breadcrumbs() function for better navigation control.
* Enhancement: Improved header icon management functions (reign_mobile_header_default_icons_set() and reign_header_default_icons()).
* Enhancement: Improved top bar default social links and info links for better customization options.
* Optimization: Removed reign_heartbeat_settings() and optimized notification count heartbeat.
* Fix: Resolved single activity call being triggered multiple times.
* Fix: Managed swipe navigation for selected menu location.
* Enhancement: Improved group invites search submit button UI.
* Optimization: Improved functionality of Reign_Shortcodes class and various theme functions.
* Fix: Improved and reverted theme settings functionality where necessary.
* Update: Managed outdated WooCommerce template files for compatibility.
* UI Fix: Removed post comment title cursor pointer for improved UI consistency.
* Enhancement: Managed blog activity integration with BP Activity Share Pro.
* Update: Set max height on blog activity images for consistent layout.

= 7.5.7 =
* Enhancement: Managed widget title UI with link title and updated forum statistics widget UI.
* Enhancement: Managed video visibility for LearnDash and Tutor LMS.
* Enhancement: Updated and added Reign icons, updated dark mode switch icons, and font awesome version.
* Enhancement: Managed purchase button in the EDD download details widget.
* Fix: Removed extra spacing in activity listing when logged out.
* Fix: Managed sticky sidebar issue when the admin bar is hidden.
* Fix: Managed share count display and share popup handling with BuddyPress share icon.
* Fix: Addressed instructor layout issues and fixed the single 404 page.
* Fix: Managed bp attachments button UI with shortcode for BuddyPress Pro.
* Fix: Fixed location field issues and bulk delete functionality.
* Enhancement: Improved overall Global Versioning, script, and styles enqueue functionalities for better performance and efficiency.
* Enhancement: Removed mega menu customization functionality and enhanced various WooCommerce product and category rendering functionalities.
* Fix: Improved UI for bbPress, BuddyPress Sticky Post, and managed dark mode fixes with BuddyPress Contact Me.
* Fix: Managed responsive fixes for backend options.
* Fix: Various improvements related to Reign theme, including improvements in BuddyPress group listings, member cover image functionality, and Elementor compatibility.

= 7.5.6 =
* Update: Improved "more" menu dropdown UI.
* Fix: BuddyPress widgets backend UI issues.
* Fix: rtMedia image size to full width in group activity.
* Cleanup: Removed unwanted override files.
* Cleanup: Separated rtMedia functions, removed commented code, applied fixes.
* Fix: Dark mode issues with BP member blog and BP Activity Share Pro.
* Enhancement: Added hookable positions after cover image.
* Update: Updated override files with the latest BB platform.
* Enhancement: Managed group activity listing actions UI.
* Fix: Content background in dark mode with BP business profile.
* Enhancement: Managed font and font size in Gutenberg editor.
* Update: Matched font size with BP Resume Manager and BP Profile Pro.

= 7.5.5 =
* Fix: Manage theme CSS not working with WP 6.6
* Fix: Uncaught TypeError: count() with bb platform
* Fix: Update override templates of the bb platform latest version
* Enhancement: Add dating color scheme
* Update: Update required-plugins.php
* Fix: Dark mode fixes with buddypress member blog
* Fix: Manage schedule activity popup button UI with youzify
* Fix: Manage remove and update border 0 on button hover
* Fix: Manage dark mode with scheduled activity and post
* Fix: Manage profile layout when disabling bb cover

= 7.5.4 =
* Fix: Managed featured event list UI (The Event Calendar).
* Fix: Improved product category dropdown with WooCommerce filter.
* Update: Updated customizer setting labels and sequence (WP Login to Branded Login & Register ).
* Fix: Removed extra border from other post types and search results.
* Update: Updated outdated WooCommerce template files.
* Fix: Managed WCFM store rating UI.
* Fix: Managed registration split layout with boxed layout.
* Fix: Addressed dark mode logo issue when reloading the page.
* Update: Updated PeepSo override templates.
* Fix: Applied dark mode fixes to the group directory list view with BB platform.
* Enhancement: Enhanced masonry & grid view with Reign post shortcode.
* Update: Updated override templates and UI fixes with the latest BB platform version.
* Update: Adjusted header dropdown menu direction in "more" menu.
* Fix: Improved Reign login form spacing with WC Vendor addon.
* Fix: Managed product categories widget UI in the footer.
* Update: Managed "more" menu for header layout 4.
* Update: Merged post types and page options into one section (Layout Settings).
* Fix: Applied UI fixes with PeepSo.
* Fix: Managed left sidebar appearance on the activity page.
* Update: Improved product listing button UI with WooCommerce product inquiry & quote.
* Fix: Added condition to display login popup form content.
* Fix: Managed sub header product breadcrumbs show parent category.
* Fix: Addressed registration popup fatal error with Youzify.
* Enhancement: Added left panel backend and text hover color.
* Fix: Fix author box HTML structure.

= 7.5.3 =
* Update: Enhanced bb-platform.scss to improve styling across the platform.
* Update: Updated woocommerce.scss for better styling consistency in WooCommerce components (#1307).
* Fix: Addressed system warnings to enhance code stability and reduce potential errors (#1308).
* Update: Refined the default layout of WooCommerce by updating color and font values to improve visual appeal and readability (#1307).

= 7.5.2 =
* Enhancement: Improved mobile tooltip visibility for WooCommerce tabs (#1302).
* Enhancement: Enhanced the masonry view for blog posts for better content presentation (#1306).
* Enhancement: Updated the product categories widget UI to improve clarity and usability (#1302).
* Update: Refined default input and button padding and height for consistency across the site.
* Update: Upgraded WooCommerce widgets UI for enhanced functionality and aesthetics (#1302).
* Enhancement: Introduced new checkout page layouts to offer more customization options (#1302).
* Enhancement: Added new card page layouts to enhance user engagement and visual appeal (#1302).
* Enhancement: Introduced new layouts for the 'Update My Account' page, providing a more user-friendly experience (#1302).
* Update: Updated the default layout for the 'My Account' page to improve user navigation and interface coherence (#1302).
* Update: Standardized global border and button radius settings to maintain a consistent design language.
* Enhancement: Revamped the single product review section to provide a more intuitive user experience (#1302).
* Enhancement: Added a reviews summary section on single product pages to aid purchasing decisions (#1302).
* Enhancement: Launched a new single product layout (Layout 2) to provide alternative viewing options (#1302).
* Update: Updated the UI for the default and first layout of single products to align with modern design standards (#1302).
* Enhancement: Introduced a new gallery slider for single products, with options for vertical or horizontal orientation (#1302).
* Enhancement: Added a product listing column switcher to allow users to customize their view dynamically (#1302).
* Fix: Corrected the Dokan sidebar class in the sidebar width setting to ensure proper alignment and functionality (#1302).
* Enhancement: Introduced additional shop layouts (Layouts 3 and 4) to provide more options for storefront customization (#1302).
* Update: Enhanced the UI for the default and first layout of the shop archive to improve aesthetic appeal and user interaction (#1302).

= 7.5.1 =
* Fix: Addressed undefined variable warnings to enhance code quality and reduce runtime errors (#1304).
* Enhancement: Improved the user interface for customizer options images, providing a more intuitive and visually appealing configuration experience (#1303).
* Enhancement: Managed notification avatars within the BB platform for better consistency and user experience (#1298).
* Update: Updated groups-loop.php to integrate seamlessly with the BB platform, enhancing group management functionalities (#1298).

= 7.5.0 =
* Update: Updated bb-platform.scss for enhanced styles (#1298).
* Enhancement: Managed customizer setting options spacing for better layout control (#1300).
* Enhancement: Added activity modal border-radius to improve UI aesthetics (#1298).
* Update: Updated required-plugins.php to ensure compatibility.
* Enhancement: Managed single event button hover icon color for better user interaction (#1298).
* Enhancement: Managed empty cart UI with Easy Digital Downloads (EDD) for a cleaner look (#1298).
* Fix: Fixed deprecated issue to maintain system stability (#1297).
* Update: Various updates to override templates for platforms like bbPress, BuddyPress, WooCommerce, and more for seamless integration and UI consistency (#1298).
* Update: Updated Easy Digital Downloads mini cart UI and override templates for enhanced user experience (#1298).
* Update: Updated rtMedia edit media UI to improve media handling features (#1298).
* Update: Updated override templates with the latest Peepso version to ensure theme compatibility and refreshed UI for messages and notifications (#1300).
* Enhancement: Managed theme UI with the latest update from Peepso for a more integrated experience (#1300).
* Update: Updated user menu user link for better navigation and user experience (#1298).
* Setting: Set default color mode after theme activation to establish a standard visual baseline.
* Update: Updated BP attachments UI for better management (#1298).
* Enhancement: Added navigation icon and updated header icon font size for improved UI clarity (#1298).
* Update: Updated BuddyPress activity post uploader UI to streamline user interactions (#1298).
* Development Tool: Executed Grunt tasks for maintaining project build quality (#1299).
* Fix Managed group cover image activity to ensure proper image handling in groups (Fixed #1299).
* Enhancement: Managed activity listing with bb platform version 2.5.80 to align with platform updates (#1298).
* Update: Removed comment and share count from activity listings to simplify the interface (#1298).

= 7.4.8 =
* Update: Removed announcements and withdrawals post type options from customizer setting.
* Fix: Customizer issue fixes (#1296).
* Enhancement: Managed active login sessions with tutorlms (#1295).
* Update: Grunt updates (#1294).
* Fix: Fixed warning in #1294
* Enhancement: Updated register popup fields UI and fixes (#1274).
* Update: Updated paid memberships pro page templates with the latest plugin version (#1293).
* Update: Kirki library updated to 5.1.0 (#1291).
* Fix: UI fixes with bb platform.
* Fix: Fixed issue with cover image activity (#1290).
* Enhancement: Removed # after click on toggle (#1288) and added dark mode option in user menu.
* Enhancement: Updated sticky sidebar js (#1283).
* Enhancement: Updated dark mode on WooCommerce components (#1282).
* Enhancement: Managed menu UI when display location unset (#1285).
* Fix: UI fixes with Youzify addons (#1284) and managed shortcodes UI with Youzify.
* Enhancement: Managed sidebar widget UI with Youzify and activity load more scrolling issue with sticky sidebar (#1283).
* Fix: Managed activity more options UI fixes with shortcode pro plugin.
* Fix: Dark mode fixes with bb platform (#1282) and updated create group upload group avatar size (#1281).

= 7.4.7 =
* Fix: (#1261) Managed single header meta color via theme
* Fix: (#1277) Managed community event UI
* Fix: (#1276) Managed Activity comments with bb platform
* Fix: (#1276) Managed Activity comments with bp latest version
* Fix: (#1275) Managed header avatar size with learnpress
* Fix: (#1273) BuddyPress docs pages UI fixes
* Fix: (#1272) Update create group page UI
* Fix: (#1269) Managed single profile bp submenu swipe
* Fix: Update bs-styled checkbox UI in register popup
* Fix: (#1271) Managed textbox auto zooming issue on IOS devices
* Enhancement: (#1270) Added color scheme for mobile menu

= 7.4.6 =
* Fix: Managed member profile sidebar width on mobile view. (#1268)
* Update: Updated required-plugins.php file.
* Fix: Updated swipe navigation UI with bb platform. (#1267)
* Fix: Managed wb-grid break UI when adding content in parallel in Elementor full width page. (#1267)
* Fix: Managed wpforo forum spacing and UI fixes. (#1266)
* Update: Added reign theme body class.
* Update: Updated register page fields UI with bb platform.
* Update: Updated reign login form UI.
* Fix: Removed "more" word from excerpt when using more tag. (#1263)
* Fix: Managed subheader posts page title dynamic. (#1264)
* Fix: Managed menu item and count alignment. (#1262)
* Fix: Managed mobile menu panel smooth toggle and full width. (#1262)
* Update: Added alternate text color setting. (#1261)
* Fix: Managed left panel icons height and width on update value. (#1260)

= 7.4.5 =
* Fix: (#1258) Update Twitter icon
* Fix: (#1257) Managed default cover image for members and groups activities with bb platform
* Fix: (#1246) Update left panel menu hover color
* Fix: Managed left panel hide when menu not set
* Fix: (#1256) Managed dark mode with BP business profile
* Fix: (#1246) Hide left panel unused color options
* Enhancement: (#1255) Managed checkbox acceptance field in registration popup
* Fix: (#1254) Removed activity comments content word wrap
* Enhancement: (#1253) Added dark mode toggle style 3
* Fix: (#1252) Fixed issue with bp profile search plugin
* Fix: (#1250) Managed single profile/group sidebar custom width
* Enhancement: (#1214) Managed single post navigation enable/disable customizer setting
* Fix: (#1246) Managed login popup smooth animation
* Fix: (#1246) Update desktop sticky header scroll position
* Fix: (#1246) Update submenu item border-radius
* Fix: (#1248) Managed to compose tab style
* Fix: (#1249) Update single headers spacing
* Enhancement: (#1246) Added reign news widget block
* Fix: Managed bp activity social share icons UI
* Fix: Managed letter avatar

= 7.4.4 =
* Fix: (#1244) Update tab style UI for vertical menu
* Fix: (#1244) Update conditions to hide/show user profile menu in mobile
* Fix: (#1244) Removed unwanted and commented code
* Fix: (#1245) - Add apply_filter for gravatar
* Fix: (#1244) Update message action button UI
* Fix: (#1242) Update scroll icon position when setting mobile header layout 3
* Fix: (#1242) Added sections for the mobile menu
* Fix: (#1242) Managed mobile view spacing and close button
* Fix: (#1242) Set default icon for logged-out menu
* Fix: (#1242) Update message section UI with the bb platform
* Fix: (#1242) Update header dropdown UI in mobile view
* Fix: (#1242) Managed menu item current color in mobile view

= 7.4.3 =
* Fix: (#1239) Added rounded avatar setting
* Fix: (#1238) Managed latest activities widget UI
* Fix: (#1236, #1237) Managed recent active and online members widget UI
* Fix: (#1235) Added post author link and redirected to buddypress profile
* Fix: (#1224) Removed permalink from single blog post thumbnail
* Fix: Added condition to show BuddyPress left sidebar
* Fix: Added condition to show BuddyPress sidebar
* Fix: Added sidebar settings for BuddyPress directory pages

= 7.4.2 =
* Fix: (#1232) Dark mode fixes
* Fix: (#1227) Managed social icons with the single post-wide layout
* Fix: (#1231) Warning fixes
* Fix: (#1229) Managed my account page user avatar image UI
* Fix: (#1226) Managed scroll top icon
* Fix: BuddyPress 12.0.0 fixes
* Fix: Single post strings translation fixes
* Fix: Added hookable position in news widget
* Fix: (#1221) Managed header layout of four mobile navigation color
* Enhancement: (#1220) Added woocommerce product vendors basic support
* Fix: Managed bp activity share pro button show/hide and color
* Fix: Managed header top spacing with Geo directory plugin
* Enhancement: (#1219) Added more view style for buddypress single member and group submenu
* Enhancement: (#1219) Added swipe view for buddypress single member and group submenu
* Fix: Managed blog post quote UI
* Fix: (#1218) Managed member and Group single action button position with bb platform
* Enhancement: (#1217) Added wordpress business directory basic support

= 7.4.1 =
* Fix: (#1216) Managed forum content translation with the BB platform
* Fix: (#1213) Warnings fixes when commenting on blog posts
* Fix: (#1213) Added condition for header message component with bb platform

= 7.4.0 =
* Fix: (#1215) Update link preview activity with the bb platform
* Fix: Managed mobile user menu with the PeepSo

= 7.3.10 =
* Fix: Added mobile header default icon set

= 7.3.9 =
* Enhancement: (#1212) Added option for hide user menu in mobile
* Fix: (#1211) Managed mediapress gallery navigation arrows
* Fix: Create dynamic activity URL
* Fix: Fixed redirection issue at register form popup
* Enhancement: Removed unnecessary code
* Fix: (#1210) Managed color picker alpha not working
* Enhancement: (#1208) Added sign-in link on register page
* Fix: (#1209) Managed register popup not working with youzify
* Enhancement: (#1208) Managed register split view page notice
* Enhancement: (#1207) Managed default cover image for members and group activities
* Enhancement: (#1208) Managed register page scroll when extra fields and fixes

= 7.3.8 =
* Fix: (#1199) Managed sticky header and sticky topbar style
* Fix: (#1204) Update Peepso header bar spacing
* Fix: (#1205) Managed page spacing with Divi and Beaver page builders
* Fix: (#1200) Managed mobile off-canvas fixes
* Fix: (#1204) Managed Peepso icons in the mobile header
* Enhancement: (#1201) Managed EDD shortcode download layouts
* Enhancement: (#1201) Managed EDD downloads item columns spacing
* Enhancement: (#1201) Added EDD downloads layouts and dark mode
* Fix: (#1202) Managed dark mode logo path not set when changing the Logo
* Enhancement: (#1200) Managed header navigation dropdown animation
* Enhancement: (#1200) Managed sticky sidebar smooth scroll in logged-out mode
* Enhancement: (#1200) Update theme header layouts and scrolling effect

= 7.3.7 =
* Fix: Managed Reign community setting default Avatar and cover image

= 7.3.6 =
* Fix: (#198) Managed social links section UI in single member
* Enhancement: (#1197) Backend options UI fixes

= 7.3.5 =
* Fix: (#1196) Update reign groups widget UI
* Fix: (#1195) Update login text custom position and fixes
* Fix: Update the sticky sidebar position
* Fix: (#1196) Update members and group carousel sidebar widgets UI
* Fix: (#1196) Managed members, groups, carousel widgets, customizer preview load
* Fix: (#1196) Reign bp activity widget customizer warning fixes
* Fix: (#1134) Removed embedded content invert
* Fix: (#1195) Managed custom login and registration customizer option
* Fix: (#1195) Update wp-login language-switcher position
* Fix: Managed login screen option when demo import

= 7.3.4 =
* Fix: (#1191) Fixed deprecated warning with easy digital downloads 3.0
* Fix: (#1189) Managed woocommerce mini cart UI with Elementor Pro
* Fix: (#1188) Fixed Uncaught Error with Youzify
* Fix: (#1186) Managed box layout container width and UI fixes
* Fix: (#1183) Update options title and description esc_attr to esc_html
* Fix: (#1183) Update theme files with Kirki 5.0.0

= 7.3.3 =
* Fix: (#1180) Single member and group action button UI fixes
* Fix: (#1181) Managed Buddypress register page split view
* Fix: (#1182) Managed activity link preview UI
* Fix: (#1176) Fixed translation issues
* Fix: (#1179) Managed footer widgets spacing UI
* Fix: (#1180) Managed groups organizers' members' UI with the bb platform

= 7.3.2 =
* Fix: Fixed undefined variable warnings
* Fix: (#1175) Dark mode fixes
* Fix: Warning fixes with php 8.2
* Fix: (#1171) Added edit profile link when no data and UI fixes
* Fix: (#1173) Activity link preview for Buddypress dark mode fixes
* Enhancement: (#1172) Managed friendship action buttons with members layout 4
* Enhancement: (#1171) Managed single profile settings screen UI
* Enhancement: (#1171) Managed single profile messages screen UI
* Enhancement: (#1171) Managed single profile notifications screen UI
* Enhancement: (#1171) Managed single profile edit screen UI
* Enhancement: (#1170) Added more options icons with the bb platform
* Fix: (#1169) Managed outdated copies of WooCommerce template files
* Fix: (#1168) Managed sticky sidebar scroll to the bottom widget
* Enhancement: (#1162) Update single post navigation UI
* Enhancement: (#1162) Update single post comment form logout mode UI
* Enhancement: (#1162) Update related post structure and UI
* Enhancement: (#1161) Added footer area padding customizer setting
* Fix: (#1161) Added left panel icon border radius and button radius fixes
* Enhancement: (#1165) Managed post activity content UI
* Enhancement: (#1164) Managed page featured image without subheader

= 7.3.1 =
* Fix: #1160 - Fixed Light and Dark Logo
* Fix: (#1155) Managed topic links mobile view UI
* Fix: (#1155) Managed social share loading on page load
* Fix: (#1155) Managed discussions content with the bb platform
* Fix: (#1155) Show subscribe and favorite button in a single topic
* Fix: (#1158) Fixed notification distortion in the French version
* Fix: Managed 404-page content color in dark mode
* Fix: (#1154) Managed reply #scroll when entering URL
* Fix: (#1154) Fixed reply #scroll effect issue
* Fix: (#1156) Managed message subject title UI
* Fix: (#1155) Update social links UI
* Fix: (#1155) Managed forum pages subheader
* Fix: (#1155) Managed left panel icon active and tooltip text color
* Fix: (#1155) Update rtMedia override file
* Fix: (#1153) Managed client site js warnings

= 7.3.0 =
* Fix: Fixed bulk selection plugins activation issue with the TGM plugin
* Fix: (#1152) Managed bbpress single topic lead with bb platform
* Fix: (#1149) The Forum archive featured image support with Buddypress
* Fix: (#1147) Paid Membership Pro levels layout UI fixes
* Fix: (#1148) Managed header notification mark as read functionality
* Fix: (#1145) Managed left panel global show/hide setting
* Fix: (#1143) WooCommerce page UI fixes
* Fix: (#1144) Managed activity comments UI in mobile view
* Fix: Update the latest posts listing UI
* Fix: Update reset variations button position
* Fix: (#1142) Fixed page setting loader issue with Firefox
* Fix: (#1141) Outdated copies of some WooCommerce template files
* Enhancement: (#1138) Added setting for blog category label default or random color

= 7.2.0 =
* Fix: Removed extra followers and following count data with BuddyBoss
* Fix: (#1135) Update product layout1 UI with BuddyPress pages
* Enhancement: (#1135) Update My Account page layout UI
* Fix: (#1137) Update Reign panel menu item bg color
* Enhancement: (#1137) Added sections menu for the left panel
* Enhancement: (#1137) Show submenu with reign left panel
* Enhancement: (#1136) Update tablet footer widget UI
* Fix: (#1136) Update tablet view menu container width
* Enhancement: (#1135) Added woocommerce new single product layout
* Fix: (#1135) Added product thumbnail hover effect
* Enhancement: (#1135) Added woocommerce my account page layouts
* Fix: (#1133) Update notification settings UI with bb platform
* Fix: (#1133) Message component UI with bb platform
* Fix: Update 'BuuddyPress' to 'Community Settings' label in the customizer
* Enhancement: (#1131) Added forums listing layouts
* Enhancement: Remove sticky sidebar on mobile view
* Fix: Fixed sticky both sidebar not working properly when page scroll up

= 7.1.1 =
* Fix: Added new icons in the iconic menu list
* Fix: (#1129) Managed peepso registration page bg colour as colour scheme
* Fix: (#1127) Managed dark mode fixes with peepso latest version
* Fix: Update reign custom css backend UI and fixes
* Fix: (#1126) Managed print style UI
* Fix: wbcom essential update

= 7.1.0 =
* Fix: Managed woocommerce product type simple button UI
* Fix: (#1122) UI fixes with Germanized for woocommerce
* Fix: (#1123) Update Kirki, tutor plugin activation issue
* Fix: (#1119) Friend request component hidden by default
* Fix: (#1120) Managed product quantity input style with the hidden type
* Fix: More options for dark mode fixes with the bb platform
* Fix: (#1119) Fixed accept reject friendship message issue
* Enhancement: (#1119) Added friend request component in header icons section

= 7.0.2 =
* Fix: (#1118) Fixed activity auto load issue with left sidebar
* Fix: Fixed #1115 - registration fields do not populate
* Fix: Fixed topbar warnings with demo importer
* Fix: Update dark mode default style
* Fix: (#1116) Youzify widgets dark mode fixes
* Fix: (#1112) Added fallback user menu

= 7.0.1 =
* Fix: (#1111) Managed dark mode colour with box layout
* Fix: (#1110) Added bp attachments plugin support
* Fix: (#1109) Update kirki 4.1.0
* Fix: (#1108) Managed header layout 4 product search in mobile view
* Fix: (#1106) Managed members rating in single profile with bb platform 

= 7.0.0 =
* Enhancement: (#1107) Managed bp better messages dark mode
* Enhancement: (#1107) Managed message notification issue with bp better messages
* Enhancement: Added condition to hide subheader in single-member and group
* Enhancement: (#1094) Update activity share modal colours
* Enhancement: (#1105) Added left panel typography settings
* Enhancement: (#1094) Managed colour scheme with Buddypress plugins and fixes
* Enhancement: (#1104) Managed scroll-up style
* Enhancement: (#1104) Added dark mode toggle style 2
* Enhancement: (#1103) Fixed woocommerce outdated copies warning
* Enhancement: (#1094) Color options fixes
* Enhancement: (#1094) Managed EDD UI and fixes
* Enhancement: (#1094) Managed wp event manager UI and fixes
* Enhancement: (#1094) Update element's color and fixes
* Enhancement: (#1094) Update border-radius and fixes
* Enhancement: (#1101) Added theme.json
* Enhancement: (#1101) Added theme support for Gutenberg editor custom dimensions
* Enhancement: (#1100) Managed the event calendar and latest past events UI
* Enhancement: (#1094) Update bb-platform-specific color options
* Enhancement: (#1099) Managed dark mode image setting not working
* Enhancement: (#1094) Dark mode colours apply dynamically via variables
* Enhancement: (#1097) Managed box layout sticky header UI
* Enhancement: (#1094) Managed event calendar colours apply via variables
* Enhancement: (#1094) Managed Buddypress plugins colours apply via variables
* Enhancement: (#1094) Managed paid membership pro colours apply via variables
* Enhancement: (#1094) Managed wp event manager colours apply via variables
* Enhancement: (#1094) Managed edd colours, border radius applied via variables
* Enhancement: (#1094) Added global button and form elements border-radius settings
* Enhancement: (#1094) Managed peepso colours apply via variables
* Enhancement: (#1094) Managed woocommerce colours, border radius applied via variables
* Enhancement: (#1094) Managed bbpress colours, border radius applied via variables
* Enhancement: (#1094) Managed buddypress content and formed colors loads dynamically
* Enhancement: (#1094) Theme colours load dynamically via variables

= 6.9.1 =
* Fix: #195 - error on user role restrictions
* Fix: (#1091) Managed login popup register redirect not working
* Fix: (#1089) Update quantity-input.php with woocommerce 7.2.1
* Fix: Managed reign wc vendors' dark mode fixes
* Fix: (#1087) Fixed both sticky sidebar issue
* Fix: (#1088) Managed border radius and update output fields
* Fix: Managed reign dokan addon color scheme fixes

= 6.9.0 =
* Fix: (#1076) Managed forum comment bb platform toolbar UI
* Fix: Managed Reign peepso dark mode fixes
* Fix: Managed Reign geodirectory addon dark mode fixes
* Fix: Managed Reign tutorlms addon dark mode fixes
* Fix: (#1082) Managed author page breadcrumb error with sensei lms
* Fix: Managed Reign sensei addon dark mode fixes
* Fix: Managed Reign lifterlms addon dark mode fixes
* Fix: Managed Reign wp job manager addon dark mode fixes
* Fix: (#1081) Managed peepso navbar not showing in notifications
* Fix: Managed Reign bb platform dark mode fixes
* Fix: Managed Reign wcfm addon dark mode fixes
* Fix: (#1079) Managed peepso giphy icon show in activity form
* Fix: (#1078) Fixed global border radius setting not working when demo import
* Fix: Managed Reign wcfm addon dark mode fixes
* Fix: (#1076) Managed forum comment box UI and fixes with bb platform
* Fix: (#1077) Managed gamipress UI with widgets
* Fix: Managed Reign wc vendors addon dark mode fixes

= 6.8.9 =
* Fix:  (#1072) Added condition for profile photo restriction with bp private community pro plugin
* Fix:  (#1073) Managed gamipress progress map listing UI
* Fix:  (#1073) Managed activity, members directory gamipress UI
* Fix:  (#1067) Managed reign dokan dark mode fixes
* Fix:  Updated woocommerce product category widget listing UI
* Fix:  (#1071) Compatibility with WooCommerce latest version
* Fix:  (#1067) Managed dark mode fixes

= 6.8.8 =
* Fix: (#1055) Fixed kirki sortable invisible using tutorlms plugin
* Fix: (#1066) Managed activity media length 1 spacing with bb platform
* Fix: (#1062) Removed extra body class
* Fix: (#1062, #1063) Elementor footer text, dokan dark mode fixes
* Fix: (#1062) Managed elementor footer text color get from elementor setting
* Fix: (#1061) Managed wp login theme two custom border color
* Fix: (#1060) Fixed overlap menu notifications, messages
* Fix: (#1059) Header notification count disable when 0 count
* Fix: Updated required-plugins.php

= 6.8.7 =
* Fix: (#1052) Managed youzify UI fixes
* Fix: Update following, followers filter UI
* Enhancement:  (#1054) Added left panel enable/disable option for logout mode
* Enhancement:  (#1056) Added new options in custom login form

= 6.8.6 =
* Fix: (#1046) Update author avatar size
* Fix: #1049 - Default profile and group cover image is not working
* Fix: #1049 - Group Avatar is not working with BuddyBoss Platform Plugin
* Fix: #1048 - Issue in default group avatar setting
* Fix: (#1047) Fixed php notices with legacy templates
* Fix: BuddyPress user todo list dark mode fixes
* Fix: BuddyPress plugins dark mode fixes
* Fix: Update whats new heading UI with bb platform
* Fix: Update single profile navigation menu icons with buddywoo
* Fix: Update edd vendor dashboard icon and navigation
* Fix: Update mce-container iframe height
* Fix: Added user menu default icons
* Fix: (#1044) Managed vendor button UI in my account page
* Fix: Color scheme fixes regarding reign wc vendor addon

= 6.8.5 =
* Fix: #1043 - Add request membership button in header and signup popup
* Fix: #1042 - Managed sticky header with elementor global footer

= 6.8.4 =
* Fix: Fixed single profile more menu visibility in mobile view
* Fix: (#1040) Managed activity listing activity header avatar clickable
* Fix: (#1039) Fixed follow count issue with bb platform

= 6.8.3 =
* Fix: (#1038) Update sub navigation UI in mobile
* Fix: (#1038) Update bp messages, feedback UI
* Fix: (#1038) Update custom scheme and dark scheme
* Fix: (#1038) Update mobile logo height
* Fix: (#1038) Update buddypress user subnav UI and fixes
* Fix: (#1038) Fixed activity popup and mobile logo spacing
* Fix: (#1038) Update form input border radius
* Fix: (#1038) Update mobile view notifications dropdown width
* Fix: (#1038) Managed mobile header layout 3 dark mode
* New Feature: (#1038) Managed mobile header layout 3
* New Feature: (#1038) Managed mobile header layout 2
* Fix: (#1038) Managed user menu clickable
* New Feature: (#1038) Added mobile menu customizer settings
* New Feature: (#1038) Added mobile header icons settings
* New Feature: (#1038) Added left panel position setting
* Fix: (#1038) Managed left panel dark mode and fixes
* New Feature: (#1038) Added separate color scheme for left panel
* Fix: (#1038) Update activity types UI
* Fix: (#1038) Separate header user menu logout option
* Fix: (#1038) Managed header more menu convert to three dots and clickable
* New Feature: (#1038) Added typography enable/disable option to support GDPR policies
* Fix: (#1028) Added condition to display header icons options
* Fix: (#1037) Update reign news widget title p tag to heading tag
* Fix: (#1034) Show topbar in mobile view when screen resize
* Fix: (#1033) Managed sticky topbar UI with box layout
* Fix: (#1029) Update phone icon and topbar default color

= 6.8.2 =
* Fix: Removed edd-sell-services theme specific style
* Fix: Managed reign wc vendor addon color scheme
* Fix: Managed peepso multivendor color scheme
* Fix: Managed reign dokan addon color scheme
* Fix: Updated Addon plugin version

= 6.8.1 =
* Fix: (#1027) Fixed PHP warning error on theme update
* Fix: (#1026) Hide gamipress data from widget area
* Fix: (#1024) Managed single event title font size and font family
* Fix: (1023) Fixed left panel issue
* Fix: (#1022) Managed elementor background overlay alignment
* Fix: (#1021) Managed failed to load resource (RTL fixes)
* Fix: Fixed php 8.0 warring with function parameter and load buddypress 

= 6.8.0 =
* Fix: (#1017) Managed dark mode fixes
* Fix: Update whats new area form UI with bb platform
* Fix: (#1020) Managed profile completion widget UI with bb platform
* Fix: (#1017) Managed dark mode with peepso header userbar
* Fix: (#1017) Managed current page item color set
* Fix: (#1017) Update default and dark mode color options and fixes
* Fix: (#1017) Update dark mode border color
* Fix: (#1017) Update user login widget UI
* Fix: Update pinned post UI
* Fix: Update flag icon with bb platform
* Fix: (#1016) Fixed member directory notice with youzify
* Fix: (#810) Managed UI with youzify
* Fix: (#992) Update avatar style with peepso and better messages
* Fix: (#1014) Managed elementor mobile header fixes
* Fix: (#1014) Fixed elementor topbar render issue
* Fix: (#1013) Removed content left side white space with box layout

= 6.7.0 =
* Fix: (#1009) Managed activity post form UI in dark mode with bb platform 2.0
* Fix: (#1012) Managed mobile menu submenu button color
* Fix: (#1009) Managed network search UI with bb platform 2.0
* Fix: (#1009) Warning fixes with kirki 4.0
* Fix: (#1010) Managed forums lead topic dark mode
* Fix: (#1010) Fixed blog layout customizer setting not showing with reign
* Fix: (#1010) Managed paid membership pro multicolor layout UI
* Fix: (#1010) Managed global border radius with paid membership pro plugin
* Fix: Dark mode fixes with reign wc vendor addon

= 6.6.0 =
* Fix: Dark mode fixes with reign wc vendor addon
* Fix: (#934) Update mark read all notification text UI
* Fix: #1009 - Hide What's new activity lightbox setting when bb
* Fix: #934 - Mark all read at notification bell
* Fix: #1009 - Fatal error with bb platform single member media tab
* Fix: Set default reign menu icons
* Fix: reign geodirectory addon fixes with GeoDirectory 2.2.5
* Fix:  Trying to get property 'ID' of non-object
* Fix: fatal error with youzify plugin
* Fix: (#1006) Update header layout 4 social icons UI
* Fix: Added product and favourite product tab icon
* Fix: (#1006) Managed UI with buddypress display xprofile plugin
* Fix: (#1002) Fixed wcfm store setup styling issue
* Fix: (#1006) Added center view layout image
* Fix: (#1006) Managed center view layout for single member, group and other
* Fix: (#902) Managed automatic update of copyright year
* Fix: Activity page UI fixes in log out mode

= 6.5.0 =
* New Feature: Reactions for BuddyPress
* New Feature: Reactions for blog post
* New Feature: Share post and reshare activities
* New Feature: New updated UI for BuddyPress activity loop
* Fix: Managed peepso bbpress replies created tab UI
* Fix: (#995) Hide wbtm fav avatar listing and reshare count
* Fix: Managed peepso woocommerce buttons UI
* Fix: Fixed mobile menu issue with TutorLMS plugin

= 6.4.0 =
* Enhancement: (#988) Fixed reign setting upload default image issue
* New Feature: (#987) Added user info in mobile panel
* Fix: (#990) Managed single profile buttons with bb platform and fixes
* Enhancement: (#987) Managed header elements border radius and fixes
* Fix: (#989) Update login widget UI
* Enhancement: (#980) Removed extra option from left panel post meta setting
* Enhancement: (#980) Removed loading glitch after page refresh
* Enhancement: (#980) Managed left panel menu with elementor page and theme fixes
* Enhancement: (#980) Update backend post meta UI
* Enhancement: (#980) Removed fallback menu from panel menu
* Enhancement: (#980) Update activity control icon
* Enhancement: (#980) Managed reign tab content UI with reign addons
* Enhancement: (#980) Update backend setting mobile view UI
* Enhancement: (#980) Update backend setting navigation UI
* Enhancement: (#980) Added theme version in welcome page and UI fixes
* Enhancement: (#982) Fixed page title does not appear when using peepso
* Enhancement: (#980) Managed icon with text alignment
* Enhancement: (#980) Managed left panel menu active color and fixes
* Enhancement: (#980) Added scrollbar for left panel and set default menu icons
* New Feature: (#980) Managed header woocommerce cart, navigation UI
* Enhancement: (#980) Managed header edd cart UI
* Enhancement: (#980) Managed mobile menu, removed shiftnav code
* Fix: (#978) Managed mediapress group navigation UI
* Fix: (#978) Managed media popup comment section UI with mediapress
* Fix: (#997) Fixed profile forum tab header view with peepso bbpress

= 6.3.5 =
* New Feature: Managed legal agreement checkbox UI with bb platform
* New Feature: Added Privacy Policy Page checkbox on registration page
* Enhancement: Added store icon for store tab

= 6.3.4 =
* New Feature: Added hookable positions for recaptcha in login and register forms
* New Feature: (#972) Managed PeepSo Single Profile Full Width
* New Feature: (#972) Managed PeepSo group layout
* New Feature: (#972) Added cover height for PeepSo
* New Feature: (#972) Managed PeepSo backend setting UI
* New Feature: (#972) Fixed member, group default cover, group wide with and UI
* New Feature: (#972) Managed focus centered badges alignment

= 6.3.3 =
* Fix: (#955) Fixed Member cover image
* Fix: (#955) Fixed Group Cover Image
* Fix: (#955) Managed activity types UI with buddypress 10.0.0
* Fix: #955 - Avatar image display before 10.0.0 version
* Fix: #955 - Added Legacy new avatar backward compatibility
* Fix: #955 - BuddyPress v10.0.0 compatibility

= 6.3.2 =
* Fix: Fixed hide single profile page action buttons issue
* Fix: Update user nav view settings and UI

= 6.3.1 =
* Fix: (#954) Added email icon
* Fix: (#950) Fixed - buddypress group email subscription with bb platform
* Fix: (#954) Managed group e-mail notification UI email options tab on single group page
* Fix: (#953) Managed group e-mail notification UI on single group page
* Fix: (#952) Managed group e-mail notification UI on group directory page
* Fix: Managed peepso userbar dropdown menu position and fixes
* Fix: rtmedia activity url preview UI fixes
* Fix: Managed bp status activity mood alignment

= 6.3.0 =
* Fix: (#946) Managed tribe events widget UI
* Fix: (#945) Fixed - footer widgets UI with event calendar archive page
* Fix: (#944) Managed rtmedia activity url preview UI and fixes
* Fix: Fixed fatal error - Call to undefined function is_plugin_active
* Fix: (#939) Managed music and video type media UI
* Fix: Removed extra border showing in activity page
* Fix: (#939) Managed UI with rtmedia latest version

= 6.2.0 =
* Fix: (#937) Updated mobile menu (shiftnav) version
* Fix: (#937) Missing text domain fixes
* Fix: Update #938 - Convert Activity Types Images in data image base64
* Fix: (#938) Managed activity types action button UI
* Fix: Managed woocommerce my account page become a vendor button UI with dokan
* Fix: Added moderation tab icon and hide sidebar
* Fix: Fixed undefined variable notice with bb platform
* Fix: (#938) Fixed new avatar update issue with bb platform
* Fix: (#938) Update view member, visit group alignment
* Fix: Managed quick view button UI with quick view for woocommerce plugin


= 6.1.0 =
* New Feature: (#929) Added swipe navigation text + icons customizer option
* New Feature: (#928)Added embeded activity of buddypress blog create/update activity
* Fix: (#936) Fixed unable to translate forum string
* Fix: Fixed social icons not showing on single member header
* Fix: Youzify specific fixes
* Fix: (#925) Fixed - Do not have to Subscribe and Favorite on topics with bb
* Fix: Added condition activity user preview with youzify
* Fix: (#923) Managed peepso elements dark mode

= 6.0.0 =
* Fix: Updated News Widget
* Fix: Update activity forum discussion UI
* Fix: Added checkin icons in single profile and group navigation
* New Feature: (#909) Added WPML for customizer options
* Fix: (#908) Update event calendar filter UI in mobile view
* New Feature: (#908) Dedicated support for event manager
* New Feature: Add Option to Disable/Enable Activity Action For Member Cover Image
* Fix: (#905) Managed cover image size in activity
* Fix: Set Default Avatar Image size
* Fix: New Profile Avatar exclude for BuddyBoss Platform plugin
* Fix: (#905) Managed profile photo, cover image create activity type UI
* Fix: Hide Group Name and Member Name when user change profile avatar
* New Feature: Added Member Cover Image, Group Photo and Group Cover Photo
* Fix: Managed button UI with peepso woocommerce
* Fix: Update kirki 3.1.9
* Fix: (#907) Added delete comment option with bb platform
* Fix: (#906) dark mode fixes
* Fix: (#906) Managed wp event manager pages UI and dark mode
* Fix: Deprecated: Required parameter $args follows optional parameter
* Fix: (#906) Added wp event manager support
* Fix: Update paid memberships pro peepso profile action links UI
* Fix: (#904) Fixed - whats new section on dark mode
* New Feature: (#905) Managed friends and group join activity type UI
* New Feature: Update single member and group swipe navigation UI
* New Feature: New group join and friendship create activity
* Fix: Hide post navigation for other cpts
* Fix: (#667) Managed profile edit fields UI with bb platform
* Fix: Update reign-login_form_top to reign_login_form_top
* Fix: (#917, #919) Managed mobile header UI with peepso
* Fix: (#915) Fixed topic tags with bb platform
* Fix: Fixed Warning in News Widget
* Fix:(#916) Managed private group join button UI

= 5.8.3 =
* New Feature: Added arrows in single member swipe menu
* New Feature: #891 - Added Sub Header backend setting for post and pages
* New Feature: Added do_action hook in rg-post-content before, after positions

= 5.8.2 =
* Fix: (#886) Managed uploaded size same with grid view
* Fix: (#867) Managed dark mode with mediapress
* Fix: Show author info only singular blog post
* Fix: Login popup setting disabled by default
* Fix: (#886) Managed gallery page, member page gallery UI - MediaPress
* Fix: (#886) Managed media player buttons
* New Feature: (#886) Added mediapress support

= 5.8.1 =
* Fix: (#883) Managed iframe embedded content in dark mode
* Fix: (#880) Fixed hide profile, activate page from login popup setting
* Fix: (#879) Managed buddypress latest update widget with buddypress 9.0
* Fix: (#878) Managed dropdown color in dark mode with wcfm
* Fix: Fixed Display widget title inside login box
* Fix: Hide Tiltle when set Login widget title
* Fix: Update login widget content font size
* Fix: Fixed activity permalink showing two times activity time since

= 5.8.0 =
* Fix: (#866) Managed dark mode wp-picker-container with wcfm
* Fix: single post layout wide with cpt post UI
* Fix: (#866) Managed reign dark mode with wcfm dashboard
* Fix: (#876) Fixed sub menu issue with more menu enabled
* Fix: Managed select options background in dark mode
* New Feature: (#875) Update scroll up font family
* New Feature: (#875) Update scroll up icon font size
* Fix: Fixed Undefined variable rand
* Fix: (#874) Managed navigation style with rtmedia tab
* Fix: Added preloader in login popup and widget
* Fix: Undefined variable: reign_login_shortcode
* Fix: Managed member directory following count icon
* Fix: (#874) Managed media (rtmedia) tab icons in iconic view
* New Feature: Added condition for peepso
* Fix: fata error function not define 'bp_is_active'
* Fix: Display fields value when user set extended registration form

= 5.7.0 =
* New Feature: Login & Register Popup Options
* New Feature: BuddyPress ByPass Activation Disable/Enable Option
* New Feature: New Widget for BuddyPress Login 
* New Feature: Login and Register Redirect options
* New Feature: Elementor Sections for Login

= 5.6.5 =
* Fix: Updated kirki version
* Fix: Update more menu js for BuddyPress nav

= 5.6.4 =
* Enhancement: Managed activity edit and delete action buttons with bb platform
* Enhancement: Update bp main navigation view style UI and fixes 
* Fix: Added bp main navigation view style
* Enhancement: (#856) Managed change cover image and change avatar image links
* Fix: Fixed php warning (undefined constant)
* Fix: (#854) Managed more menu in header and single profile tab
* Fix: Fixed post formats setting switch with gutenberg
* Fix: (#852) Fixed media uploading in comment section with bb platform

= 5.6.3 =
* Fix: Update gamipress UI in single profile header
* Fix: Update edit activity privacy UI
* Fix: Removed sidebar from user message component
* Fix: (#850) Managed bp better messages UI
* Fix: Fixed BP Condition error

= 5.6.2 =
* Fix: Hide reign author info and pagination in single attachment post
* Fix: Added single profile status tab icon
* Fix: Update view conversation icon with buddypress 8.0.0
* Fix: Fixed# Add ALT text for loggedin/logout user
* Fix: Managed sub header title visibility
* Enhancement: CLS Issue fixes
* Fix: (#847) Managed event calendar shortcodes UI with elementor pages
* Fix: (#845) Managed wp-block-table UI
* Fix: Fixed deprecated error
* Fix: Fixed Profile completion issue wityh "Extended Profiles" component
* Fix: Added blog post tab icon
* Fix: (#844) Added body line height customizer setting
Fixed font awesome icons size with elementor pages
* Enhancement: Managed EDD sell services dark mode
* Enhancement: Managed woo sell services dark mode
* Enhancement: Managed buddypress private community pro dark mode
* Enhancement: Managed resume manager dark mode
* Fix: Show unflag button in activity comments area

= 5.6.1 =
* Enhancement: Replace dark mode logo when user set dark mode from fronted
* Enhancement: Display Dark and Light logo after save mode
* Enhancement: Managed buddypress members and groups listing grid mobile view spacing

= 5.6.0 =
* Enhancement: Added setting to set dark mode custom colors
* Enhancement: Updated theme headings and body font sizes as per desktop, tablet and
* Fix: (#841) Managed elementor page font awesome icons font size
* Fix: (#840) Managed UI with buddypress follow plugin
* Enhancement: Update font awesome close icon and edd specific fixes
* Enhancement: (#839) Added related post section in single post
* Enhancement: Update dashboard count UI
* Enhancement: (#838) Added author info
* Enhancement: (#837) Added single blog post layouts and fixes
* Fix: Managed woocommerce product rating count UI
* Fix: Learndash notes dark mode fixes
* Enhancement: Update woocommerce pages UI
* Enhancement: Update search page UI
* Fix: Managed woocommerce product rating count UI

= 5.5.0 =
* Enhancement: (#833) Managed single event content font family and UI
* Fix: (#834) Show post meta info on single post
* Enhancement: (#827) Added Dark Mode Option for Theme
* Enhancement: Update activity meta icons set and comments UI
* Enhancement: (#831) Added custom login page
* Enhancement: (#828) Update border radius default value
* Enhancement: (#828) Added global border radius option in customizer settings
* Enhancement: Update members, groups directory lists spacing
* Enhancement: (#827) Managed dark mode for reign theme addons
* Fix: #829 - Display Page Title issue
* Fix: Removed post thumbnail hover effect
* Fix: Update activity meta icons set with bb platform 

= 5.4.0 =
* Fix: Managed learndash courses read more button color
* Enhancement: Added review, sales icons and review dark mode
* Fix: Managed mobile view header and content spacing
* Enhancement: Added filters for single customizer settings
* Fix: (#811) Update theme files regarding font awesome 5 pro
* Enhancement: Added - Excerpt length filter to get the limited content
* Fix: (#814, #815, #816, #817) Show header icons
* Fix: (#809) Managed peepso profile product tab button style
* Fix: (#809) Managed dokan dashboard add new product tab UI with peepso
* Fix: (#809) Managed peepso profile about tab save button alignment

= 5.3.1 =
* Fix: (#339) - customizer settings - fixed Left Sidebar issue
* Enhancement: (#808) Added youzify support
* Fixed (#794) - Deprecated Version on a single group Header 
* Fixed (#806) - excessive load times with REIGN Child Theme 
* Fixed (#804) - Display page Title Setting is not working

= 5.3.0 =
* Fix: (#800) Managed mobile header logo and icons (overlap) UI
* Fix: Fixed mobheader_height is not defined error
* Enhancement: (#802) Added main menu hover styles
* Enhancement: Added Reign documentation in support tab
* Fix: Customizer reset - set clean view color scheme as default
* Enhancement: (#798) Added customizer option for sticky topbar
* Fix: Update widgets title REIGN to Reign
* Fix: Enable more menu by default
* Enhancement: (#797) Managed activity UI with Verified Member for BuddyPress plugin
* Fix: Fixed fatal error in mobile view if peepso deactivated after peepso deactivation
* Enhancement: (#796) Added backend option to hide social media links at frontend


= 5.2.3 =
* Fix: Fixed user online offline status
* Fix: (#793) Managed register page sidebar options
* Fix: (#792) - Customizer default value set not working

= 5.2.2 =
* Fix: (#788) Managed topbar position for logged-out users
* Fix: (#781) - Subheader options removed from other CPTs too
* Enhancement: (#790) - Add option to remove Member Header View options from frontend
* Fix: (#791) - Set header layout 4 search hide/show
* Fix: Updated language terms

= 5.2.1 =
* Fix: (#786) Managed buddypress primary navigation widget UI
* Fix: Fixed cart slidebar toggle in mobile

= 5.2.0 =
* Enhancement: (#776) Added sidebar options for single topic
* Enhancement: (#776) Added sidebar options for forum pages 
* Fix: Managed activity comments link text out of content area
* Fix: Managed mobile panel logo position
* Fix: Fixed theme location url for right panel and removed default content
* Fix: Added condition for buddypress status in activity
* Fix: (#773) Managed media options UI with bb platform
* Fix: (#772) Added member type with buddypress 
* Fix: (#766) Update members-loop.php and cover-image-header.php

= 5.1.0 =
*  Fix: Reign Options are not getting saved when we are not inloduing social fields 

= 5.0.0 =
* Enhancement: Added Advance Excerpt option in customizer settings
* Enhancement: #768 - Hide customizer setting for Elementor (CPT)
* Enhancement: #767 - Hide subheader by default for CPT
* Enhancement: #766 Managed Profile Types with bb platform
* Enhancement: #763 - Hide meta values on CPT's (Archive, Single)
* Enhancement: Added PHPCS fixes

= 4.9.2 =
* Fix: (#749) Managed reign mobile header icons alignments
* Fix: (#760) Managed friends request action buttons alignment with bb platform
* Fix: Update wbtm-cover-header-type-3 moderators-lists alignment
* Fix: Update cover-image-header.php, flag icon.
* Fix: Remove reign_bp_dir_mem_manage_profile_buttons function
* Fix: Added private message, view as button icons with bb platform

= 4.9.1 =
* Fix: Managed bp group type list UI
* Fix: Text domain error fixes
* Fix: Move Group Type Position in reign theme
* Fix: Display Group Type in Single Group Header
* Fix: (#750) Added media attachments on topic form content area
* Fix: updated deprecated function bp_nouveau_the_group_meta
* Enhancement: (#747) AMP Support for Google AMP plugin
* Fix: Fixed PHP notices and warnings for PeepSo

= 4.9.0 =
* Fix: (#745) Fixed deprecated function notice with buddypress 7.0.0
* Enhancement: UI improvement for BuddyPress Moderation
* Enhancement: UI imporovment for BuddyPress Quote
* Enhancement: UI imporovment for BuddyPress Poll
* Enhancement: UI imporovment for BuddyPress Social Share
* Enhancement: Update the name pattern of Reign Function and added deprecated functions

= 4.8.1 =
* Fix : BadgeOS conflict with BP header
* Fix : BadgeOS Achievement alignment

= 4.8.0 =
* Fix: Fixed text domain
* Fix: Updated reign_profile_achievements function
* Fix: Fixed php deprecated notice
* Fix: (#734) Update function prefix
* Fix: (#734) Added support for elementor locations
* Fix: Managed wcvendors_sold_by UI on shop page
* Fix: Hide sidebar option for reign tutorlms addon

= 4.7.1 =
* Fix: invalid licenses fix

= 4.7.0 =
* Fix: (#722)Changed reign customizer setting label
* Enhancement: Updated German Translations
* Enhancement: Added buddyPress Group Carousel Shortcode
* Enhancement: Added buddyPress Member Carousel Shortcode

= 4.6.0 =
* Fix: (#721) Managed peepso profile badges tab UI
* Fix: Added geo directory places icon
* Fix: (#692) Managed vertical navigation tabs hover
* Fix: (#693, #694, #697, #698, #699) Managed peepso pages dark mode UI
* Fix: (#704) Managed activity post form textarea dark mode UI
* Fix: (#714) Managed profile dropdown link color
* Fix: (#714) Managed header layout 4 navigation background color set
* Fix: (#718) Manage cart tab UI with PeepSo Monetization: WooCommerce
* Fix: (#710) Managed postbox icon and progress bar color as per theme color
* Fix: (#715) Managed text is not readable on vendor listing page
* Fix: (#709) Managed peepso Learndash monetization course tab UI
* Fix: Update about field static checkbox listing UI
* Fix: Managed peepso activity content link color
* Fix: Update peepso single member tab style

= 4.5.0 =
* Fix: (#682, #684) Fixed archive page sidebar issue
* Fix: (#686) Managed create group invites members UI with bb platform
* Fix: Update sub header label
* Fix: Add dependency to show sub header image
* Fix: (#682) Managed sub header global, archive, single post settings
* Fix: Fixed Shiftnav issue when login designer page design from customizer
* Fix: (#616) Managed peepso 3.0.0 pages UI
* Fix: (#688) Managed peepso pages UI
* Fix: (#688) Managed both sidebar width
* Fix: (#688) Managed peepso single group page setting tab style
* Fix: Added Video, Audio and Quotes Post format template for Post
* Fix: Fixed Issue with gutenderg
* Fix: Add Post Link and Post Gallery option in admin side
* Fix: Fixed #668 - unable to edit activity
* Fix: (#668) Managed edit activity with bb platform
* Fix: Add Plus icon to upload Video and Audio file  and add Post link and Gallery page template
* Fix: set Post Link Format
* Enhancement: - Added Post Audio Format
* Enhancement: - Added Post Quote format
* Enhancement: - Added gallery post format
* Enhancement: - Added Video Post format
* Enhancement: - (#672) Added audio post format
* Enhancement: - (#672) Added quote post format
* Enhancement: - (#672) Added link post format
* Fix: Fixed LearnDash course archive page relayed fixed in left side function
* Fix: Removed unwanted code from woocommerce.scss
* Fix: (#668) Update edit activity with bb platform, fixed error with buddypress
* Fix: (#672) Update audio post format UI with soundcloud
* Fix: (#676, #677) Update groups list view with bb platform and update groups icon


= 4.4.0 =
* Fix: (#666) Managed reign member widget UI with buddypress
* Fix: (#665) Managed single post comment reply title alignment
* Fix: (#652) Fixed Profile and Group page multiple layout elemenotor widget issues
* Fix: #612 - Forced add bp-dir-hori-nav class at activity directory
* Fix: (#664) Update sticky header default color scheme option
* Fix: Managed bb platform show formating toolbar alignment
* Fix: (#645) Managed reign backend peepso tab setting tooltip UI
* Fix: Remove deprecated function for BP Nouveau Group description excerpt function
* Fix: (#645) Added lifterlms related courses icon
* Fix: (#645) Managed reign backend setting tooltip UI

= 4.3.1 =
* Fix: BuddyPress Register page displaying error message
* Fix: Update theme select box style
* Fix: (#652) Update footer buddypress item option UI
* Fix: #612 - Warnings on theme update

= 4.3.0 =
* Fix: (#643) Hide profile tab from peepso bbpress user navigation
* Fix: Added buddypress pages navigation links border
* Enhancement: (#631) Managed form reply section UI with bb platform
* Enhancement: (#631) Managed bbp threaded replies border color
* Enhancement: (#631) Managed bbp threaded replies style
* Enhancement: updated editor area for forums
* Enhancement: (#631) Update wbtm-member-directory-type-2 action button style
* Enhancement: (#631) Added member navigation tabs icon
* Enhancement: (#631) Update color scheme option with bb platform
* Enhancement: (#638) Updated license UI
* Enhancement: (#631) Added sidebar on bb media and bb document
* Enhancement: Removed Comment Box customizer setting option
* Enhancement: Removed some reign backend Advanced Setting options and error fixes
* Fix: (#631) Fixed HTML and group feed load issue in BuddyBoss Platform
* Fix: Update cart.php

= 4.2.0 =
* Fix:  Follow button alignment
* Fix: Customizer fixes for color scheme selection.

= 4.1.0 =
* Fix: Update Kirki customizer Plugin
* Fix: Fixed customize-control-kirki-sortable invisible issue
* Fix: Update kirki reign color scheme buttonset UI
* Fix: Fixed group single iconic view setting issue
* Fix: Fixed enable footer copyright option issue

= 4.0.0 =
* Fix: (#579) Fixed tanslation issue
* Fix: (#615) Managed CPT post UI like blog post
* Fix: (#518)Fixed php notice on bp profile completion widget
* Fix: (#505) Fixed deprecated version notice
* Fix: (#611) Added active callback for header layout 4 navigation background
* Fix: (#613) Update class-reign-kirki-sub-header.php
* Fix: (#613) Added sub header option for search page
* Fix: (#614) Update extra-plugins-support.php
* Fix: (#613) Added sub header link color option
* Fix: (#611) Header layout 4 set background color for navigation
* Fix: (#602) Update buddypress customizer option title name
* Fix: (#605)Fixed sidebar not showing on events listing page
* Fix: (#603) Update search results page UI
* Fix: (#603) add search form on search page
* Enhancement: Updated Typograhy options
* Enhancement: Added new options for BlockQuote Fonts
* Enhancement: Updated colors, sub-header, sticky-menu customizer fields description
* Enhancement: Updated forms, post types customizer fields description

= 3.9.0 =
* Fix: Removed extra white space from wbtm-cover-header-type-3
* Fix: (#600) Managed activity form buttons alignment
* Enhancement: (#602) Set iconic view defaults values
* Enhancement: Custom post type archive page support
* Enhancement: (#602) Added Member and Group Single Navigation Iconic View
* Enhancement: (#601) Managed custom CPT archive post UI
* Enhancement: (#601) Added custom post type support with sidebar and header options
* Enhancement: Media press integration for BuddyPress Legacy
* Enhancement: The Event Calendar UI improvement

= 3.8.0 =
* Enhancement: (#582) Managed bbPress forum, topic, reply search results UI
* Enhancement: Added German Translation

= 3.7.0 =
* Fix: Update sticky header menu logo description
* Fix: Update sticky header menu logo label
* Fix: (#522) If header layout 4 set managed user name color
* Enhancement: (#521) Update myCred UI
* Enhancement: (#521) Update BadgeOS UI
* Enhancement: (#520) Update nouveau friends request list button style
* Enhancement: (#514) Added GamiPress support
* Fix: (#515) unset mapped pages inside customizer setting
* Enhancement: Removed WPForms Lite plugin from plugins list
* Enhancement: (#517) Managed UI buddypress docs comments on single doc
* Enhancement: (#518) Updated EDD Shortcode image size to full

= 3.6.0 =
* Enhancement: (#509) Added buddypress main nav more menu option
* Enhancement: Added Cartflow plugin for Canvas page builder template
* Enhancement: (#512) PeepSo multivendor woocommerce support
* Enhancement: PeepSo multivendor product listing improvement
* Enhancement: Replaced theme font sizes into variable sizes
* Enhancement: (#511) Added content link color, content link hover color option
* Enhancement: BuddyPress Member & group layout improvement
* Enhancement: Updated mobile panel names
* Fix: Fixed BuddyPress activity font size

= 3.5.1 =
* Enhancement: Managed both sidebar order in mobile view
* Enhancement: (#503) User menu link style like header menu
* Enhancement: (#504) Added search icon in mobile header
* Enhancement: (#502) Update buddypress profile completion widget UI

= 3.5.0 =
* Fix: (#503) Managed BuddyPress user sub menu style like site sub menu
* Fix: Fixed Reign Default Avatar don't count in Profile Completion
* Fix: Fixed Infinite scroll issue in the last page
* Enhancement: Added Profile Completion Widget

= 3.4.0 =
* Fix: (#500) Update peepso widget style
* Fix: (#499) Update sign in and register button UI
* Fix: Update version quantity-input.php file
* Fix: When enable hide page hader then enable page header image option
* Fix: (#427) Hide page title setting working reversely
* Fix: (#497) Added loader animation in loading button
* Fix: Fixed Grid View, masnory View With Infinate Scroll and Set in Author
* Enhancement: Added option for number pagination and infinate scroll pagination for blog
* Fix: (#495) Fixed group navigation disappear issue with Essential Addons
* Enhancement: (#496) Managed wordpress pagination UI

= 3.3.1 =
* Fix: #493 Single group issue

= 3.3.0 =
* Fix: (#473) Update news widget thumbnail function
* Fix: (#492) Managed member directory layout 4 action button UI
* Fix: (#491) Managed group directory column layout issue with bbpress css
* Fix: (#487) Update site wide activities widget UI
* Fix: (#490) Managed header icons title language translation
* Fix: (#462) Update post navigation and woocommerce pagination UI
* Fix: Update wbcom-support-tab UI
* Fix: (#470) Update activity comment button font size
* Fix: (#482) Manage activity share icon spacing
* Fix: (#485) Live update of mobile header icon color
* Fix: (#474) Update footer typography customizer options
* Fix: german language translation update
* Fix: (#478) Managed user menu UI like sub menu
* Fix: (#473) Update sidebar widgets style
* Fix: (#477) Sub header UI mobile view and update css files
* Fix: (#476) Update members and groups directory button and username font size
* Fix: (#473) Update news widget thumbnail size and entry date font size
* Fix: (#468) seperate admin css files with condition and update theme files
* Fix: (#472) Managed rtm-tooltiptext UI
* Fix: (#472) Update bp tooltip text UI and widgets style
* Fix: (#466) Show activity form error message
* Fix: removed and managed extra-plugins.css
* Fix: (#468) seperated peepso css files with condition
* Fix: (#468) seperated EDD css files with condition
* Fix: (#468) seperate bbpress css files with condition
* Fix: (#468) seperate woocommerce css files with condition
* Fix: Update color options regarding dokan pro plugin
* Fix: (#468) seperate buddypress css files with condition
* Fix: (#464) whats new submit input button style

= 3.2.0 =
* Fix: Mobile panel color scheme sync with Desktop Color Scheme.
* Fix: BP Legacy: set icons for to do's and check in
* Fix: Update default menu font weight 400 to 500
* Fix: (#461) Manage footer customizer option
* Fix: (#461) Manage top bar info links and social links options
* Fix: (#453) Update vertical navigation style
* Fix: Fixed Customizer Import not working
* Fix: (#459) Manage site sidebar width both sidebar option selection from backend
* Enhancement: German translation included, thanks to Thorsten Wollenhoefer

= 3.1.0 =
Fix: (#455) Manage style comment meta text overlap
Enhancement: Added new option to enable comment on speciifc custom post type
Fix: Fixed Sidebar Issue on Single Profile Page
Fix: (#446) Update sidebar style on mobile view

= 3.0.0 =
* Fix: Fixed #452 Error in logs
* Fix: Fixed Color Scheme issue with updated theme version
* Fix: Fixed Clean Color Scheme option on Fresh setup
* Fix: Fixed #450 Set reign setting default values
* Fix: (#449) Update directory pages UI
* Fix: (#449) Update blog post UI
* Fix: (#448) Group single nav default display vertically
* Fix: (#448) Remove social fields border
* Fix: Update class-reign-kirki-site-layout.php
* Fix: Update members and groups default images
* Fix: (#448) Member single nav default display vertically and manage UI.
* Fix: (#447) Fixed blog post "read more" button on click hide issue

= 2.9.8 =
* Fix: (#405) Removed google from default social icons array.
* Fix:(#405) Fixed Undefined variable notice.
* Fix: Set Buddypress Default Option when reign Theme activate.
* Fix: (#439) Dokan Dashboard Edit Product Page: Reign Specific Error
* Fix: (#438) Elementor stretched view page and default footer UI
* Fix: #436 Warnings on plugin install and activation process
* Fix: Peepso font awesome 5 icons style override fixes
* Fix: Update required-plugins.php
* Fix: (#432) Added buddypress resume manager plugin menu tab icons
* FIx: (#446) Added sidebar width option in customizer setting option
* Fix: Update buddypress action button line height
* Fix: Fixed Buddypress Sidebar Layout issue for Left Sidebar
* Fix: Fixed Fatal error when woocommerce plugin not activate
* Fix: Fixed Default Sidebar when set Default layout
* Fix: Fixed WooCommerce Page Sidebar issue
* Fix : Fixed Fontwesome issue when GEO Directory Plugin issue
* Fix: (#440) RTL - edd-sell-services (reign specific) style update

= 2.9.7 =
* Enhancement: (#425) Updated kirki framework
* Fix: (#426) Profile header view setting not working
* Fix: (#431) Fixed reign backend setting and cannot read property ‘replace’

= 2.9.6 =
* Enhancement: Added New Color Scheme for theme
* Enhancement: Added Contact Form by WPForms in suggestion list
* Enhancement: Updated WooCommerce Template Files
* Fix: Select box UI style updated
* Enhancement: Header and Footer color will be managed from Color Scheme Options


= 2.9.5 =
* Fix: BuddyPress job manager plugin specifice icons added in profile page
* Enhancement: EDD FES product submission form styling

= 2.9.4 =
* Fix: Read more for BuddyPress Activities
* Fix: Checkout count after updating products in cart
* Enahnacement: Dedicated support for EDD Sell Services

= 2.9.3 =
* Fix : (#402) Removed buddypress read more text from activity content.
* Enhancement : ($403) rtMedia media posts with plus counter.
* Fix : (#406) CPT titles, breadcrumbs missing from custom post type archive.
* Enhancement : (#407) Added Font Awesome 5 Support.
* Fix : (#408) notification select style updated.
* Fix : (#409) widget area wbtm fav avatar listing style managed.

= 2.9.2.1 =
* Fix : Error Notice  Non-static method REIGN_Theme_Class::reign_theme_upload_mimes() should not be called statically

= 2.9.2 =
* Fix: (#381) WP Adverts integration create button style managed.
* Enhancement: (#382) Added support for peepso addons and default sidebar & widget.
* Fix: (#384) peepso Lifterlms Tab UI style managed.
* Fix: (#387) peepso about page cancel save button style updated.
* Fix: (#388) Peepso profile UI and text domain error.
* Fix: (#390) Fixed hovercard image issue.
* Fix: (#392) rtMedia - edit media fields style managed.
* Fix: (#393) WooCommerce cart template file version updated.
* Fix: (#398) Reduce Submenus height.
* Fix: (#399) Notification popup overlaps with submenus managed.
* Enhancement: (#401) Added SVG File type support.

= 2.9.1 =

* Enhancement: changed default sub header background image
* Enhancement: site container width layout updated
* Fix: (#351) member directory layout 2 action button style updated
* Fix: (#352) reduce font size member group review
* Fix: (#353) group directory layout 2,3 group-admins alignment managed
* Fix: Peepso multivendor plugin template override.
* Fix: Override peepso lifterlms template file.
* Fix: Fixed peepso wpforo main tab active issue.
* Fix: changed default sub header image
* Fix: fixed sticky sidebar issue on shop page.
* Fix: (#354) groups page groups navigation style managed
* Fix: Override woocommerce page.
* Fix: Added PeepSo WooCommerce plugin compatibility.
* Fix: removed glass icon from activity action buttons
* Fix: nouveau activity meta flag button style updated
* Fix: (#365) BuddyPress Nouveau create group page style updated
* Fix: (#357) Reign site wide activity widgets style managed
* Fix: (#368) Activity media photo list style updated
* Fix: (#358) news widget title updated
* Fix: (#287) - Glitch while activity update and cancel action.
* Fix: #287 - Glitch while activity update and cancel action.
* Fix: (#350) - Backend reign save settings doesn't work if social media
* Fix: (#356) - Reign site wide activity widgets
* Fix: (#359) - Reign Settings : BuddyPress Settings
* Fix: (#364) - reign site wide activity widgets actions not working
* Fix: #346 - Errors and Warnings
* Fix: #377 - Bp legacy group manage members page notice.
* Enhancement: gutenberg scss file added
* Enhancement: (#379) Blog - Grid view columns style managed
* Enhancement: (#374) Woo shop page, 'Read More' button style managed

= 2.9.0 =
* Enhancement: shiftnav toggle content style update
* Enhancement: added customizer setting for mobile icons color and hover color
* Enhancement: managed header icons style for safari browser
* Enhancement: Added hook after cart form and add woocommerce_cross_sell_display
* Enhancement: mobile header icons style udpate
* Fix: mobile header icons removed unwanted style
* Fix: subheader mobile view safari fixes
* Enhancement: my account navigation style update

= 2.8.5 =
* Enhancement: PHPCS Fixes
* Enhancement: Page title on/off setting
* Enhancement: Widget title style update
* Enhancement: Removed wp login customization setting ( added login-designer )
* Enhancement: Mobile header icons style manage
* Fix: Renamed content option name.
* Fix: Activity Stream badgeos image style managed


= 2.8.4 =
* Enhancement: bp nouveau single activity from course style
* Fix: Sidebar widgets font size update
* Fix: Manage font sizes in bp legacy from theme customizer
* Enhancement: Added login and register in default icon list
* Enhancement: Removed extra plugin install
* Enhancement: Activity page badgeos item image type svg style update
* Fix: group widget style update
* Enhancement: Added condition for hide thumbnail in LD3.0 single course page.


= 2.8.3 =
* Enhancement: Personal Profile Menu for PeepSo mobile
* Fix: Username text color for desktop header

= 2.8.2 =
* Fix: Translation Fixes
* Fix: Blog Thumbnail size
* Fix: Blog Features image Size
* Fix: Reduced Image zoom by 10%

= 2.8.1 =
* Fix: Remove woocommerce deprecated functions.
* Fix: Flag issue fixed - buddypress-moderation-pro plugin

= 2.8.0 =
* Enhancement: style up forum activities
* Enhancement: (#323) Added support for myCRED
* Enhancement: BadgeOS (#322) Show badges with svg images
* Fix: (#321) wbtm-social-media-links set target blank
* Enhancement: Elementors widgets for BuddyPress member loop and group loop
* Fix: Fixed page header issue. #318
* Fix: (#319)Online icon position for members widget managed
* Fix: Fix BuddyPress groups directory tooltip
* Enhancement: (#316) Single post layout style update
* Enhancement: (#317) Blog Post Categories Style Update
* Enhancement: woocommerce version sync
* Enhancement: (#315) Archive Page Layouts Style Update
* Fix: translation fixes
* Fix: (#314) woocommerce products listing style update
* Enhancement:  (#309) Group Directory Third Layout Style
* Enhancement:  (#299) New BuddyPress Member Directory Layout 4
* Enhancement: (#300) New BuddyPress Group Directory Layout 4
* Fix: (#292) Reign PeepSo - Default Font Family Set
* Enhancement: Added default Peepso profile social fields.


= 2.7.0 =
* Fix: (#290) Config Sticky Menu - icons & user-link color style updated
* Fix: (#289) ie header search alignment managed
* Fix: (#288) ie sub header content alignment managed
* Fix: (#285)ie browser nouveau member & group listing layout type 1 managed
* Fix: (#285) ie - Member directory listing layout 1 style updated
* Fix: (#285) header type 1 ie fixes
* Fix: #286 - To fix header icons visibility
* Fix: (#284) ie - Member Navigation Style Managed
* Fix: (#283) restrict font awesome from getting enqueued multiple times.
* Fix: reign-dokan rg-woo-category-slider-wrap rtl view managed
* Fix: (#276) PeepSo reposition cover image layout type 3 managed
* Fix: BuddyPress User Menu allocation issue.
* Fix: (#270) deleted unwanted svg icons
* Fix: (#265) avatar style managed on mobile view
* Fix: (#270) home page member listing slider style updated
* Fix: comment-body style updated
* Fix: peepso my profile widget ps popover style managed
* Enhancement: removed open-sans font in init.php file
* Enhancement: (#274) header version four style updated
* Enhancement: RTL Support
* Enhancement: PeepSo Social Media URL for member profiles
* Enhancement: Added dedicated Support for EDD and EDD FES



= 2.6.0 =
* Enhancement: (#267) WooCommerce product form input style updated - Auction plugin
* Enhancement: Added peepso templates for bbPress and wpForo.
* Enhancement: (#267) WooCommerce Auction Time Style Managed
* Enhancement: wpforo account tab style updated(#266) EDD Vendor Menu Style Updated
* Enhancement: (#266) EDD Vendor Menu Style Updated
* Enhancement: (#265) PeepSo avatar style managed on mobile view
* Fix: comment-body style updated
* Fix: peepso my profile widget ps popover style managed

= 2.5.0 =
* Enhancement: Added dedicated support for PeepSo
* Fix: (#255) Remove right sidebar from woocommerce cart and checkout page.
* Enhancement: (#254) WooCommerce cart and checkout page designs managed.
* Enhancement: (#256) Woocommerce checkout page breadcrumbs
* Enhancement: (#257) WooCommerce Order Complete Mobile View Style Managed
* Enhancement: (#257) WooCommerce Sub Order Mobile View Style Managed
* Enhancement: (#259) woo my account page user avatar
* Enhancement: (#259) Woocommerce My Account Page Style Managed
* Enhancement: (#262) WooCommerce Booking Plugin Datepicker UI Style Managed
* Enhancement: (#261) Woocommerce Product Single Page Style Updated
* Enhancement: (#259) Woocommerce My Account Order Tab Mobile View Managed


= 2.4.0 =
* Fix: elementor-text-editor style
* Fix: (#233) WooCommerce Pages Remove Unwanted Font Size
* Fix: (#232) rtMedia Uploaded Image Size style updated
* Fix: (#231) WooCommerce Pages Border Color Implementation updated
* Fix: ( #230 ) author image in posts listing and single page style managed
* Fix: Border color implementation in Learndash addon.
* Fix: kirki colors and forms file updated
* Fix: Undefined index: menu-1 issue
* Fix :(#235) WooCommerce mini cart on safari style managed
* Fix: class-reign-kirki-colors file updated for LifterLMS addon

= 2.3.1 =
* Fix: Learndash Addon Customizer fixes
* Fix: Action button for members and group
* Fix: bp followers condition check for nouveau loop class_exists

= 2.3.0 =
* Enhancement: Added reign customizer color schemes set.
* Enhancement: Added woocommerce mini cart support in header.
* Enhancement: Added toggle menu for mobile view.
* Enhancement: (#149) woocommerce mini cart mobile view count managed
* Enhancement: Added edd mini cart in header.
* Enhancement: Added buddypress user mennu option in mobile toggle menu.
* Enhancement: Added more menu in elementor header.
* Enhancement: Added setting to enable/disable elementor header in mobile view.
* Enhancement: (#143) desktop view header style updated.
* Enhancement: (#177) Added customizer control for color schemes.
* Enhancement: (#182) hide color value from typography settings and added in colors section.
* Enhancement: ( #186 ) Bring Sub Header Section out of general section.
* Enhancement: (#187) Sub Header Image setting for cpts ( not supported by reign ) in the site.
* Enhancement: (#188) changes in css regarding color schemes
* Enhancement: (#189) Create customizer setting to disable sub header.
* Enhancement: (#191) Create customizer setting to switch header image with featured image in sub header section.
* Enhancement: Added customizer settings to select left and right sidebar for supported post types.
* Enhancement: Login page style updated.
* Enhancement: Updated login screen image.
* Fix: (#161) top bar style upated
* Fix: Header top bar style updated.
* Fix: (#162) header scroll for screen size below 960 px reign topbar
* Fix: (#163) elementor topbar manage on scroll
* Fix: (#161) comment top bar fix.
* Fix: (#165) site content style managed on mobile
* Fix: (#167) manage header when topbar disabled for mobile view
* Fix: (#170) Make more setting disable by default.
* Fix: (#143) spacing between user name and avatar managed.
* Fix: (#165) site content gap on mobile view managed.
* Fix: (#202) bp group email subscription style managed.
* Fix: (#198) post archive header working for CPT.
* Fix: (#217) Achievements field set background color
* Fix: (#223) Fatal error when EDD fes is not activated.
* Fix: (#224) EDD download archive page layout issue fix.
* Fix: rt-media member and group header position inside-top issue with legacy
* Fix: (#217) Fixed achievements field set background color.

= 2.2.0 =
* Enhancement: (#119) badges support added
* Enhancement: (#120) pending invites style managed
* Enhancement: Added filter for group type direct layout.
* Fix: Group header link color manged
* Fix: (#109) more menu sub menu style updated
* Enhancement: ()#98) gutenberg support added
* Enhancement: (#100) reign page header file updated
* Enhancement: (#99) woocommerce product category page showing sidebar
* Fix: toggle icon style updated
* Fix: reign legacy subnav current a color style updated
* Fix: member home & group home widgets in two column layout managed
* Fix: messages content star unstar style managed
* Fix: group directory type 1 button style updated
* Fix: elementor masthead style updated
* Enhancement: customizer to make layout menu icon section available for elementor
* Enhancement: group mods style managed
* Enhancement: nouveau sidebar style updated
* Enhancement: members order select style managed
* Enhancement: BuddyPress follow member layout
* Enhancement: Woocommerce tab panel h2 style updated

= 2.1.3 =
* Fix: Member and Group Grid view with Safari & Firefox
* Fix: Member loop call for legacy BuddyPress version within Group member tab, manage member tab
* Fix: Send Invite style improvement
* Enhancement: Dedicated Compatibility with BuddyPress Polls plugins
* Enhancement: Added option to toggle what's new model popup

= 2.1.2 =
* Enhancement: Added customizer option of content link and Hover options
* Enhancement: Added support BuddyPress Todo List plugin
* Enhancement: BuddyPress Live Notification using heartbeat application
* Enhancement: Message Layout Improvement
* Fix: Mobile Menu Toggle issue
* Fix: Group Search Fixes
* Fix: WooCommerce Product Quantify Style
* Fix: Blog Post Default View style updated
* Fix: Empty Description inside Kirki
* Fix: Sticky Header style
* Enhancement: Added French translation files – credits to Jean Pierre Michaud

= 2.1.1 =
* Sub Header height option added.
* Sticky header glitch fixed.
* BuddyPress design improvements.
* Blog design improved for meta information.
* Customizer support added for BuddyPress Nouveau layout changes.

= 2.1.0 =
* Support addded for BuddyPress Nouveau.
* Footer BuddyPress widgets fixed.
* Mobile view header code fixed.
* Suport added for WP Job Manager.

= 2.0.7 =
* Default customizer values updated.
* Post meta options to manage page layout fixed.
* Sticky header options improved.

= 2.0.6 =
* Header v4 added to support Max Mega Menu.
* BuddyPress widgets code improved.
* Option to hide topbar on mobile view added.

= 2.0.5 =
* Support Added for Reign Dokan Addon.
* Support Added for Reign LifterLMS Addon.
* Support Added for Co-Author Plus.
* Improved customizer settings

= 2.0.4 =
* Managed buddypress buttons css design.
* Improved friend-list and friend-request listing design.

= 2.0.3 =
* License sub-menu corrected.
* Tags design corrected.
* Resolved fake update message while child theme active.

= 2.0.2 =
* Theme customizer issue with child theme resolved.
* bbPress Forum and Topic page header image options added.
* Header design conflict with default theme header and Elementor header resolved.
* Page header image issue resolved.
* Blog and comment section improved.

= 2.0.1 =
* Blog layout improved.
* More customization options added for theme header.

= 2.0.0 =
* Easy Digital Download Support Added.
* PMPRO Support Added.
* WooCommerce Support Added.
* Design Improvement.
* Customizer Setting using Kirki.

= 1.1.8 =
* Enhancement: Profile Header can be managed by users from frontend
* Enhancement: Search and Notification included inside mobile menu
* Enhancement: Style improved for BuddyPress section
* Enhancement: Support for GeoDirecotry Plugin
* Enhancement: Support for BP Profile Search with dedicated template layout
* Fix : Default set of social icons and values are added
* Fix : layout 3 for member and group header

= 1.0.7 =
* Enhancement : Multiple BuddyPress Member Header options
* Enhancement : Multiple BuddyPress Member directory Layout
* Enhancement : Multiple BuddyPress Group Layout
* Enhancement : Multiple BuddyPress Group Directory Layout
* Enhancement : BuddyPress Likes with members Avatar
* Enhancement : Activity Page with enhanced layout
* Enhancement : Social Icons for member's xprofile

= 1.0.6 =
* Fix: Default header setup with wbcom essential.

= 1.0.5 =
* Fix: Sidebar fixes for custom post type
* Fix: Fixes for default user avatar
* Fix: WooCommerce Shop page fixes for product wb-grid
* Exhancement - updated addon plugin management
* Fix: post content title adjustment
* Fix: Post comment title with h3 tag
* Fix: Comment Avatar fixes
* Fix: activity box placeholder
* Fix: fallback header fixed without elementor pagebuilder
* Fix: box layout header fixes

= 1.0.4 =
* Enhancement - Added support for global content layout
* Enhancement - Added meta box for page and post to select topbar, header and page specific layouts
* Enhancement - Added default headers and footers without Elementor
* Fix: Toggle Menu icons
* Fix: Default BuddyPress avatar
* Fix: BuddyPress Follow count display at member header
* Fix: Static Mobile Menu
* Fix: Added support to disable static Loader

= 1.0.3 =
* Enhancement - Added Multiple header support, which will allow to keep different header at different pages.
* Enhancement - Added Topbar support
* Enhancement - Added Multiple layout for Blog page Grid Layout, Thumbnail Layout, Masonry Layout
* Enhancement - Added support for BadgeOS plugin
* Enhancement - Added support for MyCred Plugin
* Enhancement - Added support for BuddyPress Member Review plugin
* Enhancement - Added support for BuddyPress Group Review plugin
* Enhancement - Added support for BuddyPress TODO List Plugin
* Enhancement - Added support for BuddyPress Check-ins plugin
* Enhancement - Added number of member display for BuddyPress member directory page
* Enhancement - Added number of group display for BuddyPress group directory page
* Enahncement - Added option to display xprofile field at member header
* Enhancement - Add icon color picker for header icons
* Fixes - Import process for default headers
* Fixes - Import process for elementor page templates

= 1.0.2 =
* Licence and auto updater integration

= 1.0.1 =
* Updated theme options
* UI changes for the Header and Footer
* Integrated Licence Module
* WooCommerce cart icon added to mobile view.
* WordPress login screen customization options added in Reign Settings.
* XML import/ export of Elementor Header/Footer support added.
* XML import of Elemetor Pages support added.

= 1.0.0 =
* Initial release

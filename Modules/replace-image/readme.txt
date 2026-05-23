=== Replace Image ===
Contributors: aspengrovestudios, annaqq
Tags: replace, overwrite, image, images, media, attachment, attachments
Requires at least: 3.5
Tested up to: 6.6.1
Stable tag: 1.1.11
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Upload a new version of an image without deleting the old image attachment, so that references to the image remain intact.

== Description ==

The Replace Image plugin adds a button to the Attachment Details screen which allows you to upload or select an image to replace the current image while retaining the URL and attachment ID. This allows you to easily swap in an updated version of an image without having to re-select it in theme settings pages, post/page content, or anywhere else where it might be referenced.

**Important:** Disable your browser's cache and any WordPress caching plugins before use; otherwise, the plugin may appear not to work. See Tools > Replace Image for instructions.

If you like this plugin, please consider leaving a comment or review.

## You may also like these plugins
[WP Zone](https://wpzone.co/) has built a bunch of plugins, add-ons, and themes. Check out other favorites here on the repository and don’t forget to leave a 5-star review to help others in the community decide.

* [Product Sales Report for WooCommerce](https://wordpress.org/plugins/product-sales-report-for-woocommerce/) - setup a custom sales report for the products in your WooCommerce store with toggle sorting options. Including or excluding items based on date range, sale status, product category and id, define display order, choose what fields to include, and generate your report with a click.
* [Export Order Items for WooCommerce](https://wordpress.org/plugins/export-order-items-for-woocommerce/) - export the order details for each sale in your WooCommerce store. Simplify order fulfillment, generate accounting reports in a few clicks, and download into CSV format for readability and universal compatibility with Export Order Items.
* [Force Update Check for Plugins and Themes](https://wordpress.org/plugins/force-update-check-for-plugins-and-themes/) -force Update Check for Plugins and Themes forces WordPress to run a theme and plugin update check whenever you visit the WordPress updates page
* [Connect SendGrid for Emails](https://wordpress.org/plugins/connect-sendgrid-for-emails/) -  connect SendGrid for Emails is a third-party fork of (and a drop-in replacement for) the official SendGrid plugin
* [Custom CSS and JavaScript](https://wordpress.org/plugins/custom-css-and-javascript/) - allows you to add custom site-wide CSS styles and JavaScript code to your WordPress site. Useful for overriding your theme’s styles and adding client-side functionality.
* [Disable User Registration Notification Emails](https://wordpress.org/plugins/disable-user-registration-notification-emails/) - when this plugin is activated, it disables the notification sent to the admin email when a new user account is registered.
* [Inline Image Upload for BBPress](https://wordpress.org/plugins/image-upload-for-bbpress/) - enables the TinyMCE WYSIWYG editor for BBPress forum topics and replies and adds a button to the editor’s “Insert/edit image” dialog that allows forum users to upload images from their computer and insert them inline into their posts.
* [Password Strength for WooCommerce](https://wordpress.org/plugins/password-strength-for-woocommerce/) - disables password strength enforcement in WooCommerce.
* [Potent Donations for WooCommerce](https://wordpress.org/plugins/donations-for-woocommerce/) – acceptance donations through your WooCommerce store
* [Shortcodes for Divi](https://wordpress.org/plugins/shortcodes-for-divi/) - allows to use Divi Library layouts as shortcodes everywhere where text comes.
* [Stock Export and Import for WooCommerce](https://wordpress.org/plugins/stock-export-and-import-for-woocommerce/) - generates reports on the stock status (in stock / out of stock) and quantity of individual WooCommerce products.
* [Random Quiz Generator for LifterLMS](https://wordpress.org/plugins/random-quiz-addon-for-lifterlms/) - pull a random set of questions from your quiz so users never get the same question twice when retaking or setting up a practice quiz.
* [WP and Divi Icons](https://wordpress.org/plugins/wp-and-divi-icons/) - adds over 660 custom outline SVG icons to your website. SVG icons are vector icons, so they are sharp and look good on any screen at any size.
* [WP Layouts](https://wordpress.org/plugins/wp-layouts/) - the best way to organize, import, and export your layouts, especially if you have multiple websites.
* [WP Squish](https://wordpress.org/plugins/wp-squish/) - reduce the amount of storage space consumed by your WordPress installation through the application of user-definable JPEG compression levels and image resolution limits to uploaded images.

To view WP Zone's premium WordPress plugins and themes, visit our [WordPress products catalog page](https://wpzone.co/product/).


== Installation ==

1. Click "Plugins" > "Add New" in the WordPress admin menu.
2. Search for "Replace Image".
3. Click "Install Now".
4. Click "Activate Plugin".

Alternatively, you can manually upload the plugin to your wp-content/plugins directory.

== Frequently Asked Questions ==

= I tried to replace an image but nothing happened. =

Your browser is likely still caching the old image. Try doing a hard refresh (Ctrl + F5 on Windows, Apple + R / Command + R on Mac) while viewing the page on the frontend. Note that the backend seems to retain cached image thumbnails even after a hard refresh.

== Screenshots ==

1. The Replace Image button in the Attachment Details screen (opened by clicking on an image in the Media Library).

== Changelog ==

= 1.1.11
* Add permissions check on image to be replaced
* Don't show Replace Image button on images that the user isn't allowed to replace

= 1.1.10
* Fix Missing Version Parameter in Asset URLs: This oversight could potentially lead to caching problems in browsers upon plugin updates.

= 1.1.9
* Fix: Image replacement may not work in some browsers

= 1.1.8 =
* Add "Replace With AI Image" button

= 1.1.7 =
* Updated links, author, changed branding to WP Zone
* Updated tested up to
* Removed donation links,
* Added aspengrovestudios as contributor
* Updated banner and icon

= 1.1.6 =
* Plugin now deletes files associated with the old image prior to replacement

= 1.1.5 =
* Fixed non-critical PHP warning

= 1.1.3 =
* Fixed undefined offset error per https://wordpress.org/support/topic/undefined-index-0-on-line-100-mainfile?replies=1

= 1.1.1 =
* Added support for image replacement where the image file being replaced doesn't exist
* Removed anonymous functions

= 1.1 =
* Added functionality to prevent image caching in the Media Library

= 1.0.3 =
* Fixed a bug where the upload UI would temporarily stop working properly after replacing an image.

= 1.0 =
* Initial release


== Upgrade Notice ==

=== BuddyPress Restrict Messages ===
Contributors: nuprn1, etivite
Donate link: paypal.me/GeorgeChaplin
Tags: buddypress, messages, messaging, private message, pm
Requires at least: PHP 5.2, WordPress 3.2.1 BuddyPress 1.5.1
Tested up to: 6.6
Stable tag: 1.1.0

This plugin allows the site admin to restrict who can send private messages or to enable the users to choose themselves.

== Description ==

This plugin is perfect for private message spam prevention. It allows site admin and users themselves to limit exactly who can send messages to the user.

This plugin creates an admin settings page called Restrict Messages. Here the site admin can choose to impose global restrictions over who can send messages to the user.

If message restrictions are delegated to the user, a user settings page is created at Profile>>Settings>>Messages.

Each of these settings pages, depending on which is active, allows private messaging to be restricted to:

* Friend Connections
* Follow Connections
* Groups Connections - with further selection of public, private or hidden status levels.

The public setting allows connections from all groups to send messages to the user, otherwise private or public stipulate only messages from these types of groups.

Once a restriction has been implements, the plugin removes the private message from the users profile, removes the usename from the compose message send to auto-complete and rejects any messages send where the send to includes an excluded user.

This plugin was originally created by Rich @etivite and is now supported by Venutius.

== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Activate the plugin at the plugin administration page
3. Adjust settings via the Restrict Messages admin page
4. If user mode select: Members adjust settings via profile > settings > messages

== Frequently Asked Questions ==

== Screenshots ==

1. screenshot-1.png - Admin Settings screen.
2. screenshot-2.png - User Settings screen.


== Changelog ==

= 1.1.0 =

* 20/07/2024

* Fix: Corrected error that prevented the user based options from displaying.
* Update: Improved translateability.

= 1.0.5 =

* 16/12/2020

* Fix: Corrected further error in member suggestion filter.

= 1.0.4 =

* 15/12/2020

* Fix: Corrected error in member suggestion filter.

= 1.0.3 =

* 06/05/2019

* Fix: Enabled for multisite individual sites.

= 1.0.2 =

* 21/03/2019

* Fix: Corrected error caused by zero recipients set for message.

= 1.0.1 =

* 20/03/2019

* Fix: Corrected undefined variable error when no settings have been set.

= 1.0.0 =

* Plugin taken over from Rich @etivite
* New: Now supports BP Nouveau.
* Fix: Corrected error with send message filtering.
* New: added BP admin bar setting option for the user.
* New: added admin settings nav item.

= 0.2.0 =

* FEATURE: Add BP Follow support

= 0.1.0 =

* First version


== Upgrade Notice ==


== Extra Configuration ==

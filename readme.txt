=== Plugin Name ===
Contributors: nielsvanrenselaar
Donate link: 
Tags: open graph, facebook, meta
Requires at least: 3.0.1
Tested up to: 4.4
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin lets you set the Open Graph meta tags per post, page or custom post type and set default start values for new posts. It's a simple plugin to get you started with the Open Graph meta tags and will get extended features soon.

== Installation ==

1. Upload the folder `open-graph-metabox` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The settings are accessible trough the posts edit pages as a custom meta box

== Screenshots ==

1. The meta box on the edit screen

== Changelog ==

= 1.3.1 =
* Added support for global translations

= 1.3 =
* Support WordPress 4.4
* Fixed issue with wrong labeling at Open Graph Type selection dropdown
* Fixed issue with not saving the default image
* Added support for frontpage settings

= 1.2.5 =
* Minor corrections
* Supports WordPress 3.9.2

= 1.2.4 = 
* Supports WordPress 3.8

= 1.2.3 =
* Added "fb:app_id" and "fb:admins" as settings

= 1.2.2 =
* Fixed bug in the media selector on WordPress 3.5 and 3.5.1

= 1.2.1 =
* Added link to Facebook Open Graph debugger
* Minor bugfixes

= 1.2 =
* Added a default settings page

= 1.1 =
* Added support voor language files

= 1.0 =
* Added the meta boxes
* Added support for all custom post types

== Upgrade Notice ==

= 1.0 =
* First version

== Frequently Asked Questions ==

= Why should I enter my Facebook App ID? =

Including the fb:app_id tag in your HTML HEAD will allow the Facebook scraper to associate the Open Graph entity for that URL with an application. This will allow any admins of that app to view Insights about that URL and any social plugins connected with it. Open Graph Metaboxes let you set this setting trough the settings panel.

= Why should I set Facebook Admins? =

The fb:admins tag is similar to the fb:app_id, but allows you to just specify each user ID that you would like to give the permission to do the above.
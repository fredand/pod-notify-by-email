Pod Notify By Mail
===========

A starter plugin for extending Pods' functionality.

Requires Pods 2.3.18 or later. (Please keep this notice in your plugin and set the appropriate version.)

Instructions
============

* Naming

  You will need to change the name of the plugin in a few simple steps:

    * Rename the base plugin folder name and primary PHP file named 'pod-notify-by-email.php' to your plugin's name. Make sure the folder name matches the name of the PHP file (without .php on the end).

    * Search for 'pod-notify-by-email' and replace with your desired plugin name in all lowercase with dashes between words (if desired).

    * Search for 'Pod Notify By Mail' and replace with your plugin's proper name.

    * Search for 'Pod_Notify_By_Mail' and replace with your plugin's proper name with underscores instead of spaces between words.

    * Search for 'POD_NOTIFY_BY_MAIL' and replace with your plugin's proper name in all caps. This is for constants.

    * Be sure to set the plugin header meta data. See [https://codex.wordpress.org/Writing_a_Plugin#Names.2C_Files.2C_and_Locations](https://codex.wordpress.org/Writing_a_Plugin#Names.2C_Files.2C_and_Locations).

* Usage
 
<em>Tutorials, like winter are coming.</em>

  * Adding options to Pods Admin:

     Check out the example filters in __construct(). Be sure to note the complete example that has example callbacks.

  * Adding a new Pods field:

    See the example class. Don't forget to include the file and initialize the class.

When Not To Use This Plugin
===========================
* If you are not comfortable with object-oriented PHP.

* When this plugin is overkill.

If you just need to add some code to help implement Pods in your site, and do not need what this plugin provides, you may be better served by [this plugin](https://gist.github.com/Shelob9/9979551). It simply includes a file for your custom code, if Pods is active.

Notes and License
==================

This plugin is based on [Base WP Plugin](https://github.com/tareq1988/Base-WP-Plugin) and like Pods and WordPress is licensed under the terms of the GNU General Public License (GPL) version 2.

Pod Notify By Mail
===========

This plugin will send an email when a new pod-item is submitted.

Requires Pods 2.3.18 or later. (Please keep this notice in your plugin and set the appropriate version.)

Instructions
============

Put in wordpress plugin directory, and enable plugin.

* Usage

  * You enable the notification on the edit pods option and the "Notify by email" tab of each pod, look for "Notification updated pod."

  
Magic-tags
==========

You can use all standard pods-fields as magic tags in the same manner as for standard pod.

Standard wordpress fields
	{@permalink}
	{@author}
	{@post_date}
	{@post_id}
  
In the body of the email you can use "{@pod_notify_by_email_full_pod_content}"  for a ascii formatted detailed view of all submitted fields.
  
  
Issues
======
	Magic-tags to related fields is untested.
	Some magic-tags fields like checkboxes don't work.
  
  
Notes and License
==================

This plugin is based on [Base WP Plugin](https://github.com/tareq1988/Base-WP-Plugin) and like Pods and WordPress is licensed under the terms of the GNU General Public License (GPL) version 2.

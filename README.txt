=== Plugin Name ===
Contributors: emerson@emersonbroga.com
Donate link: http://emersonbroga.com/
Tags: notifications, broswer notifications, web notifications
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sends browser notifications on new Posts.

== Description ==

n/a

== Installation ==

Copy `wp-content/plugins/bnotifications/public/js/bnotifications-sw.js` to `/bnotifications-sw.js` (same folder as wp-config.php)
Check if this file is accessible by visiting your site url `/bnotifications-sw.js`

Generate your VAPID keys at https://vapidkeys.com/
Add your VAPID keys to wp-content/plugins/bnotifications/includes/class-bnotifications.php lines 36, 37, 38.

After this, by visiting the site it will prompt you to enable browser notifications.
When a new post is published, the notications should be sent to every subscriber.


== Changelog ==

= 1.0 =
* initial release


### Reference Links
https://github.com/DevinVinson/WordPress-Plugin-Boilerplate
https://github.com/aishwarya-art/FIREBASE-WEB-PUSH-NOTIFICATION/blob/main/app.js
https://stackoverflow.com/questions/41144151/firebaseerror-we-are-unable-to-register-the-default-service-worker
https://stackoverflow.com/questions/37427709/firebase-messaging-where-to-get-server-key
https://firebase.google.com/docs/cloud-messaging/auth-server
https://www.youtube.com/watch?v=0-bSQ14H_PY
https://www.youtube.com/watch?v=Bm0JjR4kP8w&t=259s
https://www.youtube.com/watch?v=Tn6vEDrZjAU
https://github.com/appfeel/node-pushnotifications/blob/master/src/sendWeb.js
https://github.com/web-push-libs/web-push-php
https://packagist.org/packages/minishlink/web-push
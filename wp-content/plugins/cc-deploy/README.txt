=== CC-Deploy ===
Contributors: ClearcodeHQ, PiotrPress
Tags: git, deploy, repository, deployment, github, bitbucket, stash, clearcode, piotrpress
Requires at least: 4.8.2
Tested up to: 4.8.2
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

This plugin allows you to deploy your WordPress site source code from git repository using webhooks.

== Description ==

This plugin allows you to deploy your WordPress site source code from git repository using webhooks.

Automatically pull from a repository to a web server. You can configure which branch is triggering the pull action.
After each deployment plugin saves the status to logs which are available through wp-admin and also are sent by email.

Supported git repository hosting services:
* [Github](https://github.com/)
* [Bitbucket](https://bitbucket.org/)
* Stash

== Installation ==

= From your WordPress Dashboard =

1. Go to 'Plugins > Add New'
2. Search for 'CC-Deploy'
3. Activate the plugin from the Plugin section on your WordPress Dashboard.

= From WordPress.org =

1. Download 'CC-Deploy'.
2. Upload the 'cc-deploy' directory to your '/wp-content/plugins/' directory using your favorite method (ftp, sftp, scp, etc...)
3. Activate the plugin from the Plugin section in your WordPress Dashboard.

= Once Activated =

1. Visit the 'Deploy> Settings' page, select your preferred options and save them.

= Multisite =

The plugin can be activated and used for just about any use case.

* Activate at the site level to load the plugin on that site only.
* Activate at the network level for full integration with all sites in your network (this is the most common type of multisite installation).

== Screenshots ==

1. **CC-Deploy Settings** - Visit the 'Deploy > Settings' page, select your preferred options and save them.
2. **CC-Deploy Logs** - Visit the 'Deploy > Logs' page to review latest deployment logs.

== Changelog ==

= 1.0.0 =
*Release date: 10.10.2017*

* First stable version of the plugin.
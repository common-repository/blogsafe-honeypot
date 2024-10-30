=== Plugin Name ===
Contributors: (blogsafe)
Donate link: www.blogsafe.org
Tags: honeypot, research, hacking, malware
Requires at least: 5.6
Tested up to: 5.8.0
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BlogSafe Honeypot is a 'lite' version our private research tool. Instead of tracking where visitors go, it tracks where they want to go.

== Description ==

BlogSafe Honeypot is a 'lite' version of our private research tool. It's purpose is to track two types of information.

*   Failed URL requests.
*   Failed login attempts.

By analyzing data collected by BlogSafe Honeypot, you will gain an insight into attempts to access known vulnerabilities as well as emerging
threats. You'll also be able to analyze which usernames and passwords brute-force agents are using in attempts to force their way into your site.

**Main Features**

*   Records information about connections incoming to your WordPress site that have no valid destination.
*   Records failed username and password attempts. Useful for analyzing brute-force attacks.

*Other Features*

*   IP and URL White-listing to prevent known requests and logins from being recorded.

*Options*

*   Can be configured to detect incoming The Onion Router (TOR) connections.
*   Can be configured to automatically delete requests and login attempts after 1 year.

*Third Party Notice*
If the optional TOR detection is enabled, BlogSafe Honeypot will make a 3rd party connection to the TOR exit node list maintained by TOR.
You can manually view this list here: [TOR Exit List](https://check.torproject.org/exit-addresses)

== Installation ==

1. Upload the contents of the .zip file `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. BlogSafe Honeypot showing the stats view.

== Changelog ==

= 1.0.1 = 
 * Deactivating now removed all databases.
 * Double underscore bug not allowing settings message to be displayed fixed.
 * Added version to admin header.

= 1.0 =
 * Initial WordPress release


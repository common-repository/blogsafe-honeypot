<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.blogsafe.org
 * @since             1.0.0
 * @package           Blogsafe_Honeypot
 *
 * @wordpress-plugin
 * Plugin Name:       BlogSafe Honeypot
 * Plugin URI:        www.blogsafe.org
 * Description:       BlogSafe honeypot is a 'lite' version our private research tool. Instead of tracking where visitors go, it tracks where they want to go.
 * Version:           1.0.1
 * Author:            BlogSafe.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       blogsafe-honeypot
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('BLOGSAFE_HONEYPOT_VERSION', '1.0.1' );
define('BLOGSAFE_HONEYPOT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('BLOGSAFE_HONEYPOT_NAME', 'BlogSafe Honeypot');
define("BLOGSAFE_HONEYPOT_HELP_URL", 'https://blogsafe.org/blogsafe-honeypot-help/');
define('BLOGSAFE_TOR_EXIT', 'https://check.torproject.org/exit-addresses');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-blogsafe-honeypot-activator.php
 */
function activate_blogsafe_honeypot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-blogsafe-honeypot-activator.php';
	Blogsafe_Honeypot_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-blogsafe-honeypot-deactivator.php
 */
function deactivate_blogsafe_honeypot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-blogsafe-honeypot-deactivator.php';
	Blogsafe_Honeypot_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_blogsafe_honeypot' );
register_deactivation_hook( __FILE__, 'deactivate_blogsafe_honeypot' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-blogsafe-honeypot.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_blogsafe_honeypot() {

	$plugin = new Blogsafe_Honeypot();
	$plugin->run();

}
run_blogsafe_honeypot();

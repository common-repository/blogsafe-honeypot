<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       www.blogsafe.org
 * @since      1.0.0
 *
 * @package    Blogsafe_Honeypot
 * @subpackage Blogsafe_Honeypot/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Blogsafe_Honeypot
 * @subpackage Blogsafe_Honeypot/includes
 * @author     BlogSafe.org <support@blogsafe.org>
 */
class Blogsafe_Honeypot_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'blogsafe-honeypot',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

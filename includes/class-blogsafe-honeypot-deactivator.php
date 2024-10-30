<?php

/**
 * Fired during plugin deactivation
 *
 * @link       www.blogsafe.org
 * @since      1.0.0
 *
 * @package    Blogsafe_Honeypot
 * @subpackage Blogsafe_Honeypot/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Blogsafe_Honeypot
 * @subpackage Blogsafe_Honeypot/includes
 * @author     BlogSafe.org <support@blogsafe.org>
 */
class Blogsafe_Honeypot_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . "BS_Honeypot_Logins";
        $sql = "DROP TABLE $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . "BS_Honeypot_TORList";
        $sql = "DROP TABLE $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . "BS_Honeypot_Requests";
        $sql = "DROP TABLE $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . "BS_Honeypot_IP_Ignores";
        $sql = "DROP TABLE $table_name";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . "BS_Honeypot_URL_Ignores";
        $sql = "DROP TABLE $table_name";
        $wpdb->query($sql);
        
	}

}

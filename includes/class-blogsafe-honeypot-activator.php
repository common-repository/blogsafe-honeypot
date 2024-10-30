<?php

/**
 * Fired during plugin activation
 *
 * @link       www.blogsafe.org
 * @since      1.0.0
 *
 * @package    Blogsafe_Honeypot
 * @subpackage Blogsafe_Honeypot/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Blogsafe_Honeypot
 * @subpackage Blogsafe_Honeypot/includes
 * @author     BlogSafe.org <support@blogsafe.org>
 */
class Blogsafe_Honeypot_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;

        update_option('BSHoneypot_TORActive', false, 'no');

        $table_name = $wpdb->prefix . "BS_Honeypot_Logins";
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `userName` varchar(100) NOT NULL,
            `password` varchar(100) NOT NULL,
            `ip` char(15) DEFAULT NULL,
            `remoteHost` varchar(255) NOT NULL,
            `isTOR` tinyint(1) NOT NULL DEFAULT 0,
            `urlrequested` varchar(250) DEFAULT NULL,
            `agent` varchar(250) DEFAULT NULL,
            `referrer` varchar(250) DEFAULT NULL,
            `method` varchar(5) NOT NULL,
            `data` varchar(255) NOT NULL,
            `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
            `sent` tinyint(1) NOT NULL DEFAULT 0,
            `whitelisted` tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `id` (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
          ";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . "BS_Honeypot_TORList";
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `IP` varchar(50) NOT NULL,
            PRIMARY KEY (`ID`)
          ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1018 ;";
        $wpdb->query($sql);

        $table_name = $wpdb->prefix . "BS_Honeypot_Requests";
        $sql = "
        CREATE TABLE IF NOT EXISTS $table_name (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ip` char(50) DEFAULT NULL,
            `remoteHost` varchar(255) NOT NULL,
            `isTOR` tinyint(1) NOT NULL DEFAULT 0,
            `urlrequested` varchar(250) DEFAULT NULL,
            `agent` varchar(250) DEFAULT NULL,
            `referrer` varchar(250) DEFAULT NULL,
            `Method` varchar(10) NOT NULL,
            `Data` varchar(255) NOT NULL,
            `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `Sent` tinyint(1) NOT NULL DEFAULT 0,
            `Performance` decimal(4,4) NOT NULL,
            `isLogin` tinyint(1) NOT NULL DEFAULT 0,
            `userName` varchar(50) NOT NULL,
            `password` varchar(50) NOT NULL,
            `ipwhitelisted` tinyint(1) NOT NULL DEFAULT 0,
            `urlwhitelisted` tinyint(1) NOT NULL DEFAULT 0,
            UNIQUE KEY `id` (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=latin1;    
        ";
        $wpdb->query($sql);
        
        $table_name = $wpdb->prefix . "BS_Honeypot_IP_Ignores";
        $sql = "
        CREATE TABLE IF NOT EXISTS $table_name (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `ipaddress` varchar(100) NOT NULL,
            `notes` varchar(255) NOT NULL,
            `addedby` varchar(50) NOT NULL,
            PRIMARY KEY (`ID`)
          ) ENGINE=MyISAM DEFAULT CHARSET=latin1;         
        ";
        $wpdb->query($sql);
        
        $table_name = $wpdb->prefix . "BS_Honeypot_URL_Ignores";
        $sql = "
        CREATE TABLE IF NOT EXISTS $table_name (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `url` varchar(255) NOT NULL,
            `notes` varchar(255) NOT NULL,
            `addedby` varchar(50) NOT NULL,
            PRIMARY KEY (`ID`)
          ) ENGINE=MyISAM DEFAULT CHARSET=latin1;      
        ";
        $wpdb->query($sql);                
    }
}
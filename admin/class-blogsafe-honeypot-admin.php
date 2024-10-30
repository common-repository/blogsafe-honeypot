<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.blogsafe.org
 * @since      1.0.0
 *
 * @package    Blogsafe_Honeypot
 * @subpackage Blogsafe_Honeypot/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Blogsafe_Honeypot
 * @subpackage Blogsafe_Honeypot/admin
 * @author     BlogSafe.org <support@blogsafe.org>
 */
class Blogsafe_Honeypot_Admin {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function BSHoneypot_Login_Check() {
        include_once('BlogSafe_Honeypot_Request_Logger.php');
        $logger = new BlogSafe_Honeypot_Request_Logger();
        $logger->LoginCheck();
    }

    public function BSHoneypot_plugin_main_menu() {
        $icon_url = plugin_dir_url(__FILE__) . 'images/BSHoneypotIcon.png';
        add_menu_page('BlogSafeHoneypot', BLOGSAFE_HONEYPOT_NAME, 'edit_posts', 'BlogSafeHoneypot', array($this, 'BSHoneypot_main_menu'), $icon_url);
    }

    public function BSHoneypot_main_menu() {
        include_once( 'BlogSafe_Honeypot_Menu.php' );
        $menu = new BSHoneypot_Main_Menu();
    }

    function BSHoneypot_Check_TOR() {
        if (get_option('BSHoneypot_TORActive', 'none') == 'TRUE') {
            include_once 'BlogSafe_Honeypot_Utils.php';
            $utils = new BlogSafe_Honeypot_Utils;
            $utils->getTORExitList();
        }
    }

    public function BSHoneypot_Handle_Request() {
        include_once ('BlogSafe_Honeypot_Request_Logger.php');
        $logger = new BlogSafe_Honeypot_Request_Logger();
        $logger->Process_Request();
    }

    
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('wp_authenticate', array($this, 'BSHoneypot_Login_Check'));
        add_action('wp', array($this, 'BSHoneypot_Handle_Request'));
        add_action('admin_menu', array($this, 'BSHoneypot_plugin_main_menu'));
        add_action('BSHoneypot_Check_TOR', array($this, 'BSHoneypot_Check_TOR'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Blogsafe_Honeypot_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Blogsafe_Honeypot_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/blogsafe-honeypot-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Blogsafe_Honeypot_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Blogsafe_Honeypot_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/blogsafe-honeypot-admin.js', array('jquery'), $this->version, false);
    }
}
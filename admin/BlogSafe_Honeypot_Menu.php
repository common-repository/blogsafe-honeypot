<?php

if (!defined('WPINC')) {
    die();
}

class BSHoneypot_Main_Menu {

    public function BSHoneypot_Load_Menu_Buttons($args) {
        foreach ($args as $arg) {
            echo '<button class="button-primary" type="submit" name="action" value="' . $arg['value'] . '">' . $arg['text'] . '</button>&nbsp;';
        }
    }

    public function BSHoneypot_Load_Sub_Menu_Buttons($args) {
        foreach ($args as $arg) {
            echo '<button class="button" type="submit" name="action" value="' . $arg['value'] . '">' . $arg['text'] . '</button>&nbsp;';
        }
    }

//assign the menu actions
    public function BSHoneypot_Set_Menu_Actions($args) {
        foreach ($args as $arg) {
            add_action('BSHoneypot_Action_' . $arg['action'], array($this, $arg["callback"]));
        }
    }

    public function BSHoneypot_Action_Stats() {
        include_once('BlogSafe_Honeypot_Stats.php');
        do_action('BSHoneypot_Add_JS');
        $this->BSHoneypot_Show_Menu($this->stats_submenu);
        $stats = new BlogSafe_Honeypot_Stats();
        $stats->Show_Stats_Pre();
        $stats->Show_Stats();
    }

    public function BSHoneypot_Action_UpdateSettings() {
        include_once('BlogSafe_Honeypot_Settings.php');
        $settings = new BlogSafe_Honeypot_Settings;
        $settings->UpdateSettings();
        $this->BSHoneypot_Show_Menu();
        $settings->ShowForm();
    }

    public function BSHoneypot_Action_Settings() {
        $this->BSHoneypot_Show_Menu();
        include_once('BlogSafe_Honeypot_Settings.php');
        $settings = new BlogSafe_Honeypot_Settings;
        $settings->ShowForm();
    }

    public function BSHoneypot_Action_Help() {
        $this->BSHoneypot_Show_Menu();
        echo '<h1>Help</h1>';
    }

    public function BSHoneypot_Action_ViewRequests() {
        $this->BSHoneypot_Show_Menu($this->stats_submenu);
        include_once('BlogSafe_Honeypot_Request_Viewer.php');
        BSHoneypot_Show_Logs();
    }

    public function BSHoneypot_Action_ViewLogins() {
        $this->BSHoneypot_Show_Menu($this->stats_submenu);
        include_once('BlogSafe_Honeypot_Login_Viewer.php');
        BSHoneypot_Show_Logins();
    }

    private function BSHoneypot_Do_Delete_Logins($thisid) {
        global $wpdb;
        $table_name = $wpdb->prefix . "BS_Honeypot_Logins";
        $wpdb->delete($table_name, array(
            'id' => $thisid
        ));
    }

    public function BSHoneypot_Action_View_URLWhitelist() {
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        include_once('BlogSafe_Honeypot_URL_Viewer.php');
        BSHoneypot_Show_URL_Whitelist();
    }

    public function BSHoneypot_Action_insertURL() {
        include_once('BlogSafe_Honeypot_URL_Viewer.php');
        BSHoneypot_Insert_Whitelist_URL(sanitize_text_field($_GET['URL']), sanitize_text_field($_GET['Notes']));
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        BSHoneypot_Show_URL_Whitelist();
    }

    public function BSHoneypot_Action_deleteurl() {
        include_once('BlogSafe_Honeypot_URL_Viewer.php');
        BSHoneypot_Delete_Whitelist_URL();
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        BSHoneypot_Show_URL_Whitelist();
    }

    public function BSHoneypot_Action_addignoreurl() {
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        include_once('BlogSafe_Honeypot_URL_Viewer.php');
        BSHoneypot_Show_Add_URL();
    }

    public function BSHoneypot_Action_View_Whitelist() {
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        include_once('BlogSafe_Honeypot_IP_Viewer.php');
        BSHoneypot_Show_IP_Whitelist();
    }

    public function BSHoneypot_Action_deleteip() {
        include_once('BlogSafe_Honeypot_IP_Viewer.php');
        BSHoneypot_Delete_Whitelist_IP();
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        BSHoneypot_Show_IP_Whitelist();
    }

    public function BSHoneypot_Action_insertIP() {
        include_once('BlogSafe_Honeypot_IP_Viewer.php');
        BSHoneypot_Insert_Whitelist_IP(sanitize_text_field($_GET['IP']), sanitize_text_field($_GET['Notes']));
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        BSHoneypot_Show_IP_Whitelist();
    }

    public function BSHoneypot_Action_addip() {
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        include_once('BlogSafe_Honeypot_IP_Viewer.php');
        BSHoneypot_Show_Add_IP();
    }

    public function BSHoneypot_Action_insertcurrentip() {
        include_once('BlogSafe_Honeypot_IP_Viewer.php');
        BSHoneypot_Insert_Whitelist_IP(sanitize_text_field($_GET['currentip']), __('Current IP Address', 'blogsafe-honeypot'));
        $this->BSHoneypot_Show_Menu($this->whitelist_sumbenu);
        BSHoneypot_Show_IP_Whitelist();
    }

    private function BSHoneypot_Do_Delete_Requests($thisid) {
        global $wpdb;
        $table_name = $wpdb->prefix . "BS_Honeypot_Requests";
        $wpdb->delete($table_name, array(
            'id' => $thisid
        ));
    }

    public function BSHoneypot_Action_deletelog() {
        global $wpdb;
        if (is_array($_GET['id'])) {
            $count = 0;
            foreach (@sanitize_text_field($_GET['id']) as $id) {
                $this->BSHoneypot_Do_Delete_Requests($id);
                $count++;
            }
            if ($count > 1) {
                $this->BSHoneypot_Show_Message(serialize(array('message' => $count . __(' Request entries deleted!', 'blogsafe-honeypot'), 'type' => 1)));
            }
        } elseif (isset($_GET['id'])) {
            $this->BSHoneypot_Do_Delete_Requests(sanitize_text_field($_GET['id']));
            $this->BSHoneypot_Show_Message(serialize(array('message' => __('Request entry deleted.', 'blogsafe-honeypot'), 'type' => 1)));
        } else {
            $this->BSHoneypot_Show_Message(serialize(array('message' => __('No request entry selected.', 'blogsafe-honeypot'), 'type' => 2)));
        }
        $this->BSHoneypot_Action_ViewRequests();
    }

    public function BSHoneypot_Action_DeleteLogins() {
        global $wpdb;
        if (is_array($_GET['id'])) {
            $count = 0;
            foreach (sanitize_text_field($_GET['id']) as $id) {
                $this->BSHoneypot_Do_Delete_Logins($id);
                $count++;
            }
            if ($count > 1) {
                $this->BSHoneypot_Show_Message(serialize(array('message' => $count . __(' Log Entries Deleted!', 'blogsafe-honeypot'), 'type' => 1)));
            }
        } elseif (isset($_GET['id'])) {
            $this->BSHoneypot_Do_Delete_Logins(sanitize_text_field($_GET['id']));
            $this->BSHoneypot_Show_Message(serialize(array('message' => __('Log Entry Deleted.', 'blogsafe-honeypot'), 'type' => 1)));
        } else {
            $this->BSHoneypot_Show_Message(serialize(array('message' => __('No log entry selected.', 'blogsafe-honeypot'), 'type' => 2)));
        }
        $this->BSHoneypot_Action_ViewLogins();
    }

    public function BSHoneypot_Show_Menu($submenu = NULL) {
        global $wpdb;

        echo '<div class="wrap">';
        echo '<table width="100%" cellpadding="5">';
        echo '<tr><td width="100px"><img src="' . plugin_dir_url(__FILE__) . 'images/BSHoneypotLogo.png" width="300px" align="bottom" hspace="3"/></td>';
        echo '<td><h1>' . BLOGSAFE_HONEYPOT_NAME . '</h1>'. __(' Version ', 'blogsafe-honeypot').BLOGSAFE_HONEYPOT_VERSION. ' ©copyright '.date("Y") .' BlogSafe.org';
        echo '<p>' . __('For more information and instructions please visit our website at: ', 'blogsafe-honeypot') . '<a href="http://www.blogsafe.org" target="_blank">http://www.blogsafe.org</a></td></tr></table><hr />';

        echo '<form action="" method="get">';
        do_action('BSHoneypot_Load_Menu_Buttons', $this->main_menu_buttons);
        echo '<button class="button-primary" onclick=" window.open(\'' . BLOGSAFE_HONEYPOT_HELP_URL . '\',\'_blank\')" value="Help">' . __('Help', 'blogsafe-honeypot') . '</button>&nbsp;';
        
        if ($submenu != NULL) {
            echo '<hr>';
            do_action('BSHoneypot_Load_Sub_Menu_Buttons', $submenu);
        }

        echo '<input name="page" type="hidden" value="BlogSafeHoneypot" />';
        $nonce = wp_create_nonce('BSHnonce');
        echo '<input name="BSHnonce" type="hidden" value="' . $nonce . '" />';
        echo '</form>';
        if ($submenu != -1) {
            echo '<hr>';
        }
        $BSHoneypot_errormsg = get_option('BSHoneypot_error_message', 'none');
        if ($BSHoneypot_errormsg != 'none') {
            $this->BSHoneypot_Show_Message($BSHoneypot_errormsg);
        }
    }

    public function BSHoneypot_Show_Message($message) {
        $msg = unserialize($message);
        switch ($msg['type']) {
            case 0:
                echo '<div class="notice notice-error is-dismissable"><p>' . $msg['message'] . '</p> </div>';
                break;
            case 1:
                echo '<div class="notice notice-success is-dismissible"><p>' . $msg['message'] . '</p></div>';
                break;
            case 2:
                echo '<div class="notice notice-warning is-dismissible"><p>' . $msg['message'] . '</p></div>';
                break;
        }
        update_option('BSHoneypot_error_message', 'none');
    }

    private function BSHoneypot_Show_Default_Menu() {
        $this->BSHoneypot_Action_Stats();
    }

    public function __construct() {

        //set up the menu actions
        $menu_actions = array(
            array('action' => 'Stats', 'callback' => 'BSHoneypot_Action_Stats', 10, 1),
            array('action' => 'Settings', 'callback' => 'BSHoneypot_Action_Settings', 10, 1),
            array('action' => 'UpdateSettings', 'callback' => 'BSHoneypot_Action_UpdateSettings', 10, 1),
            array('action' => 'ViewRequests', 'callback' => 'BSHoneypot_Action_ViewRequests', 10, 1),
            array('action' => 'ViewLogins', 'callback' => 'BSHoneypot_Action_ViewLogins', 10, 1),
            array('action' => 'DeleteLogins', 'callback' => 'BSHoneypot_Action_DeleteLogins', 10, 1),
            array('action' => 'View_URLWhitelist', 'callback' => 'BSHoneypot_Action_View_URLWhitelist', 10, 1),
            array('action' => 'addip', 'callback' => 'BSHoneypot_Action_addip', 10, 1),
            array('action' => 'deleteip', 'callback' => 'BSHoneypot_Action_deleteip', 10, 1),
            array('action' => 'deletelog', 'callback' => 'BSHoneypot_Action_deletelog', 10, 1),
            array('action' => 'insertIP', 'callback' => 'BSHoneypot_Action_insertIP', 10, 1),
            array('action' => 'insertURL', 'callback' => 'BSHoneypot_Action_insertURL', 10, 1),
            array('action' => 'insertcurrentIP', 'callback' => 'BSHoneypot_Action_insertcurrentIP', 10, 1),
            array('action' => 'addignoreurl', 'callback' => 'BSHoneypot_Action_addignoreurl', 10, 1),
            array('action' => 'deleteurl', 'callback' => 'BSHoneypot_Action_deleteurl', 10, 1),
            array('action' => 'View_Whitelist', 'callback' => 'BSHoneypot_Action_View_Whitelist', 10, 1)
        );

        $this->main_menu_buttons = array(
            array('value' => 'Stats', 'text' => __('Stats', 'blogsafe-honeypot')),
            array('value' => 'View_Whitelist', 'text' => __('Whitelists', 'blogsafe-honeypot')),
            array('value' => 'Settings', 'text' => __('Settings', 'blogsafe-honeypot'))
        );

        $this->stats_submenu = array(
            array('value' => 'ViewRequests', 'text' => __('View Requests', 'blogsafe-honeypot')),
            array('value' => 'ViewLogins', 'text' => __('View Logins', 'blogsafe-honeypot'))
        );

        $this->whitelist_sumbenu = array(
            array('value' => 'View_Whitelist', 'text' => __('IP Whitelist', 'blogsafe-honeypot')),
            array('value' => 'View_URLWhitelist', 'text' => __('URL Whitelist', 'blogsafe-honeypot'))
        );

        add_action('BSHoneypot_Load_Menu_Buttons', array($this, 'BSHoneypot_Load_Menu_Buttons'));
        add_action('BSHoneypot_Load_Sub_Menu_Buttons', array($this, 'BSHoneypot_Load_Sub_Menu_Buttons'));
        add_action('BSHoneypot_Set_Menu_Actions', array($this, 'BSHoneypot_Set_Menu_Actions'));
        do_action('BSHoneypot_Set_Menu_Actions', $menu_actions);

        //process the action request
        if (!empty($_GET['action'])) {
            $action = sanitize_text_field($_GET['action']);
            $nonce = sanitize_text_field($_REQUEST['BSHnonce']);
            if (!wp_verify_nonce($nonce, 'BSHnonce')) {
                die(__('Security Check', 'blogsafe-honeypot'));
            }
            if (method_exists($this, 'BSHoneypot_Action_' . $action)) {
                do_action('BSHoneypot_Action_' . $action);
            } else {
                if ($action == '-1') {
                    if (isset($_GET['requestsearch'])) {
                        $this->BSHoneypot_Action_ViewRequests();
                    } elseif (isset($_GET['loginsearch'])) {
                        $this->BSHoneypot_Action_ViewLogins();
                    } else {
                        $this->BSHoneypot_Show_Default_Menu();
                    }
                } else {
                    $this->BSHoneypot_Show_Default_Menu();
                }
            }
        } else {
            $this->BSHoneypot_Show_Default_Menu();
        }
    }
}
?>
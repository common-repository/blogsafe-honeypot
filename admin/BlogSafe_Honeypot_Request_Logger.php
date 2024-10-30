<?php

class BlogSafe_Honeypot_Request_Logger {

    public function __construct() {
        
    }

    public function LoginCheck() {
        global $wpdb;

        include_once('BlogSafe_Honeypot_Utils.php');
        $utils = new BlogSafe_Honeypot_Utils;

        if (!isset($_POST['log']) || !isset($_POST['pwd'])) {
            return;
        }
        $userName = sanitize_text_field($_POST['log']);
        $password = sanitize_text_field($_POST['pwd']);

        $remotehost = $utils->get_remote_host();
        $methodarray = $utils->get_method();
        $Method = $methodarray[0];

        if (@$_POST['log'] && @$_POST['pwd'] && @$_POST['wp-submit'] == "Log In") {
            if (is_email($userName)) {
                $user = wp_authenticate_email_password(NULL, $userName, $password);
            } else {
                $user = wp_authenticate_username_password(NULL, $userName, $password);
            }
            if (is_wp_error($user)) {
                $ipclass = new BlogSafe_Honeypot_RemoteAddress();
                $ip = $ipclass->getIpAddress();
                $table_name = $wpdb->prefix . "BS_Honeypot_Logins";
                $isTOR = FALSE;
                if (get_option('BSHoneypot_TORActive') == 'TRUE') {
                    $table_name = $wpdb->prefix . "BS_Honeypot_TORList";
                    $torcount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE IP = '%s'", $ip));
                    if ($torcount > 0) {
                        $isTOR = TRUE;
                    }
                }

                if (get_option("BSHoneypot_AutoDelete")) {
                    $thisquery = "DELETE FROM $table_name WHERE timestamp < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 YEAR))";
                    $wpdb->query($thisquery);
                }

                $url = rtrim($_SERVER['REQUEST_URI'], '/\\');
                $ua = $utils->get_UA();
                $referrer = $utils->get_referrer();
                $table_name = $wpdb->prefix . "BS_Honeypot_Logins";
                $wpdb->query($wpdb->prepare("INSERT INTO " . $table_name .
                                "(ip, userName, password, remoteHost, isTOR, urlrequested, agent, referrer, method) VALUES ('%s', '%s', '%s', '%s', '%s' , '%s' , '%s' , '%s' , '%s' )", $ip, $userName, $password, $remotehost, $isTOR, $url, $ua, $referrer, $Method));
            }
        }
    }

    public function Process_Request() {
        global $wpdb;

        include_once('BlogSafe_Honeypot_Utils.php');
        $utils = new BlogSafe_Honeypot_Utils;
        $time_start = microtime(true);
        $ipclass = new BlogSafe_Honeypot_RemoteAddress();
        $ip = $ipclass->getIpAddress();
        $url = sanitize_text_field(rtrim($_SERVER['REQUEST_URI'], '/\\'));
        $referrer = $utils->get_referrer();
        $userAgent = $utils->get_UA();
        $remoteHost = $utils->get_remote_host();
        $methodarray = $utils->get_method();
        $Method = $methodarray[0];
        $the_request = $methodarray[1];

        if (get_current_user_id() > 0) {
            return;
        }

        if (strlen($url) == 0) {
            return;
        }

        if (url_to_postid($url) != 0) {
            return;
        }

        if (get_page_by_path(untrailingslashit($url)) != null || get_page_by_path($url) != null) {
            return;
        }

        $isTOR = FALSE;
        if (get_option('BSHoneypot_TORActive') == true) {
            $table_name = $wpdb->prefix . "BS_Honeypot_TORList";
            $torcount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE IP = '%s'", $ip));
            if ($torcount > 0) {
                $isTOR = TRUE;
            }
        }

        $ipignore = 0;
        $table_name = $wpdb->prefix . "BS_Honeypot_IP_Ignores";
        $sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name where ipaddress = %s", $ip);
        $ipcount = $wpdb->get_var($sql);
        if ($ipcount > 0) {
            $ipignore = 1;
        }

        $urlignore = 0;
        $table_name = $wpdb->prefix . "BS_Honeypot_URL_Ignores";
        $sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name where url = %s", $url);
        $urlcount = $wpdb->get_var($sql);
        if ($urlcount > 0) {
            $urlignore = 1;
        }

        $time_end = microtime(true);
        $executionTime = $time_end - $time_start;
        $table_name = $wpdb->prefix . "BS_Honeypot_Requests";
        $thisquery = $wpdb->prepare("
		INSERT INTO " . $table_name . " 
		(
		 ip,
                 remoteHost,
                 isTOR,
		 urlrequested,
		 agent,
		 referrer,
		 Method,
		 Data,
                 Performance,
                 ipwhitelisted,
                 urlwhitelisted
		 )
		VALUES 
		( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
	", $ip, $remoteHost, $isTOR, $url, $userAgent, $referrer, $Method, $the_request, $executionTime, $ipignore, $urlignore);
        $wpdb->query($thisquery);
        if (get_option("BSHoneypot_AutoDelete")) {
            $thisquery = "DELETE FROM $table_name WHERE timestamp < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 YEAR))";
            $wpdb->query($thisquery);
        }
        
    }
}
?>
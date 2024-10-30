<?php

class BlogSafe_Honeypot_Stats {

    private function showTable($results, $head, $name) {
        //echo '<hr>';       
        echo '<table width="100%" cellpadding="10">';
        echo '<tr><td colspan="2"><h1 style="margin-bottom: 3px">' . $head . '</h1><hr></td></tr>';
        echo '</table>';
        echo '<table id="bshtable">';
        echo '<tr><td style="padding-left:10px;"><h3>' . $name . '</h3></td><td><h3>Count</h3></td></tr>';
        foreach ($results as $result) {
            foreach ($result as $key => $value) {
                if ($key == 'occurrences') {
                    echo '<td>' . $value . '</td></tr>';
                } else {
                    echo '<tr style="border-bottom: 1px solid lightgray"><td id="bshleft"><strong>' . $value . '</strong></td>';
                }
            }
        }
    }

    private function showLatestLogins($results) {
        global $wpdb;

        //echo '<hr>';
        echo '<br><br>';

        echo '<table id="bshtable" cellpadding="10">';
        echo '<tr><td colspan="5"><h1 style="margin-bottom: 3px">' . __('Latest Password Attempts', 'blogsafe-honeypot') . '</h1><hr></td></tr>';
        echo '<tr style="border-bottom: 1px solid lightgray">';
        echo '<td><strong>' . __('Timestamp', 'blogsafe-honeypot') . '</strong></td>';
        echo '<td><strong>' . __('IP', 'blogsafe-honeypot') . '</strong></td>';
        echo '<td><strong>' . __('User Name', 'blogsafe-honeypot') . '</strong></td>';
        echo '<td><strong>' . __('Password', 'blogsafe-honeypot') . '</strong></td>';
        echo '<td><strong>' . __('User Agent', 'blogsafe-honeypot') . '</strong></td>';
        echo '</tr>';
        foreach ($results as $result) {
            echo '<tr style="border-bottom: 1px solid lightgray">';
            echo '<td>' . $result->timestamp . '</td>';
            echo '<td>' . $result->ip . '</td>';
            echo '<td>' . $result->userName . '</td>';
            echo '<td>' . $result->password . '</td>';
            echo '<td>' . $result->agent . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    private function showLatest($results) {
        global $wpdb;

        //echo '<hr>';
        echo '<table id="bshtable" cellpadding="10">';
        echo '<tr><td colspan="5"><h1 style="margin-bottom: 3px">' . __('Latest Requests', 'blogsafe-honeypot') . '</h1><hr></td></tr>';
        echo '<tr style="border-bottom: 1px solid lightgray">';
        echo '<td><strong>' . __('Timestamp', 'blogsafe-honeypot') . '</strong></td>';
        echo '<td><strong>' . __('IP', 'blogsafe-honeypot') . '</strong></td>';
        echo '<td><strong>' . __('Remote Host', 'blogsafe-honeypot') . '</strong></td>';
        echo '<td><strong>' . __('URL Requested', 'blogsafe-honeypot') . '</strong></td>';
        echo '<td><strong>' . __('User Agent', 'blogsafe-honeypot') . '</strong></td>';
        echo '</tr>';
        $maxlen = 50;

        foreach ($results as $result) {
            if (strlen($result->urlrequested) > $maxlen) {
                $urlrequested = substr($result->urlrequested, 0, $maxlen) . '...';
            } else {
                $urlrequested = $result->urlrequested;
            }
            echo '<tr style="border-bottom: 1px solid lightgray">';
            echo '<td>' . $result->timestamp . '</td>';
            echo '<td>' . $result->ip . '</td>';
            echo '<td>' . $result->remoteHost . '</td>';
            echo '<td>' . $urlrequested . '</td>';
            echo '<td>' . $result->agent . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function Show_Count() {
        global $wpdb;

        echo '<table id="bshtable">';
        $table_name = $wpdb->prefix . 'BS_Honeypot_Requests';
        $requestcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $table_name = $wpdb->prefix . 'BS_Honeypot_Logins';
        $logincount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($requestcount > 0 || $logincount > 0) {
        echo '<tr><td style="padding-left:10px;"><h3>Type</h3></td><td><h3>Count</h3></td></tr>';
        }
        if ($requestcount > 0) {
        echo '<tr style="border-bottom: 1px solid lightgray"><td id="bshleft"><strong>Requests</strong></td>';
        echo '<td>' . $requestcount . '</td></tr>';
        }
        if ($logincount > 0) {
        echo '<tr style="border-bottom: 1px solid lightgray"><td id="bshleft"><strong>Password Attempts</strong></td>';
        echo '<td>' . $logincount . '</td></tr>';
        }
        echo '<tr><td>&nbsp</td></tr>';
        echo '<tr><td>&nbsp</td></tr>';
    }

    private function Show_Top() {
        echo '<div id="col-container" class="wp-clearfix">
              <div id="col-left">
              <div class="col-wrap" style="background: white;
              margin: 5px;">';
    }

    private function Show_Bottom() {
        echo '<br><br></div></div></div>';
    }

    public function Show_Stats_Pre() {
        echo '<div class="excontainer"><div id="count1">';
    }

    public function Show_Stats() {
        $acount = 0;
        $bcount = 0;
        global $wpdb;

        echo '<style>
            #bshtable {border-collapse:collapse; table-layout:auto; width:100%;}
            #bshtable td {word-wrap:break-word}
            #bshleft {min-width: 300px; max-width: 300px; padding-left: 10px}
            #bshhr {border-bottom: none}
            </style>';

        $this->Show_Top();
        $ts = wp_next_scheduled('BSHoneypot_Check_TOR');
        if ($ts != false) {
            $d = new DateTime("@$ts");
            $date = $d->format('m-d-Y H:i:s');
        }

        echo '<table width="100%" id="bshtable">';
        if ($ts != false) {
            echo '<tr><td id="bshleft">' . __('Next TOR Check: ', 'blogsafe-honeypot') . $date . '</td></tr>';
        }
        echo '</table>';

        echo '<table width="100%" cellpadding="10">';
        echo '<tr><td colspan="2"><h1 style="margin-bottom: 3px">Totals</h1><hr></td></tr>';
        echo '</table>';
        $this->Show_Count();

        $table_name = $wpdb->prefix . 'BS_Honeypot_Requests';
        $SQL = "SELECT ip, COUNT(*) AS occurrences 
            FROM $table_name where ipwhitelisted = 0 and urlwhitelisted = 0
            GROUP BY ip 
            ORDER BY occurrences DESC 
            LIMIT 5";
        $results = $wpdb->get_results($SQL);
        if ($wpdb->num_rows > 0) {
            $this->showTable($results, __('IP Addresses', 'blogsafe-honeypot'), __('IP', 'blogsafe-honeypot'));
            $acount++;
            echo '<tr><td>&nbsp</td></tr>';
            echo '<tr><td>&nbsp</td></tr>';
        }

        $SQL = "SELECT remoteHost, COUNT(*) AS occurrences 
            FROM $table_name where ipwhitelisted = 0 and urlwhitelisted = 0
            GROUP BY remoteHost
            ORDER BY occurrences DESC 
            LIMIT 5";
        $results = $wpdb->get_results($SQL);
        if ($wpdb->num_rows > 0) {
            $this->showTable($results, __('Remote Hosts', 'blogsafe-honeypot'), __('Host', 'blogsafe-honeypot'));
            $acount++;
            echo '<tr><td>&nbsp</td></tr>';
            echo '<tr><td>&nbsp</td></tr>';
        }

        $SQL = "SELECT urlrequested, COUNT(*) AS occurrences 
            FROM $table_name where ipwhitelisted = 0 and urlwhitelisted = 0
            GROUP BY urlrequested 
            ORDER BY occurrences DESC 
            LIMIT 5";
        $results = $wpdb->get_results($SQL);
        if ($wpdb->num_rows > 0) {
            $this->showTable($results, __('URL Requested', 'blogsafe-honeypot'), __('URL', 'blogsafe-honeypot'));
            $acount++;
            echo '<tr><td>&nbsp</td></tr>';
            echo '<tr><td>&nbsp</td></tr>';
        }

        $SQL = "SELECT referrer, COUNT(*) AS occurrences 
            FROM $table_name where ipwhitelisted = 0 and urlwhitelisted = 0
            GROUP BY referrer 
            ORDER BY occurrences DESC 
            LIMIT 5";
        $results = $wpdb->get_results($SQL);
        if ($wpdb->num_rows > 0) {
            $this->showTable($results, __('Referrer', 'blogsafe-honeypot'), __('URL', 'blogsafe-honeypot'));
            $acount++;
            echo '<tr><td>&nbsp</td></tr>';
            echo '<tr><td>&nbsp</td></tr>';
        }

        $SQL = "SELECT agent, COUNT(*) AS occurrences 
            FROM $table_name where ipwhitelisted = 0 and urlwhitelisted = 0
            GROUP BY agent 
            ORDER BY occurrences DESC 
            LIMIT 5";
        $results = $wpdb->get_results($SQL);
        if ($wpdb->num_rows > 0) {
            $this->showTable($results, __('User Agent', 'blogsafe-honeypot'), __('Agent', 'blogsafe-honeypot'));
            $acount++;
            echo '</table>';
        }

        if ($acount == 0) {
            echo '<table width="100%" cellpadding="10">';
            echo '<tr><td colspan="2"><h3 style="margin-bottom: 3px">' . __('No statistics collected yet', 'blogsafe-honeypot') . '</h3><hr></td></tr>';
            echo '</table>';
        }
        echo '<br><br></div></div>';
        echo '<div id="col-right""><div class="col-wrap" style="background: white; margin: 5px;">';

        $SQL = "Select * from $table_name where ipwhitelisted = 0 and urlwhitelisted = 0 order by timestamp desc limit 10";
        $results = $wpdb->get_results($SQL);
        if ($wpdb->num_rows > 0) {
            $this->showLatest($results);
            $bcount++;
        }

        $table_name = $wpdb->prefix . 'BS_Honeypot_Logins';
        $SQL = "Select * from $table_name where whitelisted = 0 order by timestamp desc limit 10";
        $results = $wpdb->get_results($SQL);
        if ($wpdb->num_rows > 0) {
            $this->showLatestLogins($results);
            $bcount++;
        }
        if ($bcount == 0) {
            echo '<table width="100%" cellpadding="10">';
            echo '<tr><td colspan="2"><h3 style="margin-bottom: 3px">' . __('No statistics collected yet', 'blogsafe-honeypot') . '</h3><hr></td></tr>';
            echo '</table>';
        }
        $this->Show_Bottom();
        echo '</div></div>';
    }
}
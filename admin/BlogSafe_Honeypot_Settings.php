<?php

class BlogSafe_Honeypot_Settings {

    public function __construct() {
        $this->TorActive = get_option("BSHoneypot_TORActive");
        $this->AutoDelete = get_option("BSHoneypot_AutoDelete");
    }

    public function UpdateSettings() {
        if (isset($_GET['toractive'])) {
            $this->TorActive = update_option("BSHoneypot_TORActive", true);
            if (!wp_next_scheduled('BSHoneypot_Check_TOR')) {
                do_action('BSHoneypot_Check_TOR');
                wp_schedule_event(time(), 'hourly', 'BSHoneypot_Check_TOR');
            }
        } else {
            $this->TorActive = update_option("BSHoneypot_TORActive", false);
            if (wp_next_scheduled('BSHoneypot_Check_TOR')) {
                wp_clear_scheduled_hook('BSHoneypot_Check_TOR');
            }
        }
        if (isset($_GET['autodelete'])) {
            update_option("BSHoneypot_AutoDelete", true);
        } else {
            update_option("BSHoneypot_AutoDelete", false);
        }
        $this->AutoDelete = get_option("BSHoneypot_AutoDelete");

        $updatemsg = array('message' => __('Settings Updated', 'blogsafe-honeypot'), 'type' => 1);
        update_option('BSHoneypot_error_message', serialize($updatemsg));
        $this->TorActive = get_option("BSHoneypot_TORActive");
    }

    public function ShowForm() {
        $toractive = '';
        $autodelete = '';
        if ($this->AutoDelete) {
            $autodelete = ' checked';
        }
        if ($this->TorActive) {
            $toractive = ' checked';
        }
        echo '<style>
            #bshtable {border-collapse:collapse; table-layout:auto; width:100%; }
            #bshtable td {word-wrap:break-word; padding: 10px}
            #bshleft {min-width: 300px; max-width: 300px; padding-left: 10px}
            #bshhr {border-bottom: none}
            </style>';
        echo '<h1>Settings</h1>';
        echo '<form action="" method="GET">';
        echo '<table id="bshtable">';

        echo '<tr><td id="bshleft" width="25%"><h2>' . __(' TOR', 'blogsafe-honeypot') . '</h2>' . __('If TOR Active is checked, BlogSafe Honeypot will download the latest exit list for The Onion Router every hour.  It will then examine any incoming requests and compare them to the TOR exit list and record this in requests database.', 'blogsafe-honeypot')
        . '</td>';
        echo '<td colspan="2"></td></tr>';
        echo '<tr><td id="bshleft" colspan="3">' . '<input type="checkbox" id="toractive" name="toractive" value="" ' . $toractive . '>';
        echo '<label for="toractive">' . __(' TOR Active', 'blogsafe-honeypot') . '</label>'
        . '</td>';
        echo '</tr>';


        echo '<tr><td id="bshleft" width="25%"><h2>' . __(' Auto Delete', 'blogsafe-honeypot') . '</h2>' . __('If checked, BlogSafe Honeypot will automatically delete any requests or login attempts older than 1 year.', 'blogsafe-honeypot')
        . '</td>';
        echo '<td colspan="2"></td></tr>';
        echo '<tr><td id="bshleft" colspan="3">' . '<input type="checkbox" id="autodelete" name="autodelete" value="" ' . $autodelete . '>';
        echo '<label for="toractive">' . __(' Auto Delete', 'blogsafe-honeypot') . '</label>'
        . '</td>';
        echo '</tr>';
        
        echo '<tr><td id="bshleft" colspan="3">' . '<button class="button-primary" type="submit" name="action" value="UpdateSettings">' . __(' Submit', 'blogsafe-honeypot') . '</button></td>';
        echo '</tr>';
        echo '</table>';
        echo '<input name="page" type="hidden" value="BlogSafeHoneypot" />';
        $nonce = wp_create_nonce('BSHnonce');
        echo '<input name="BSHnonce" type="hidden" value="' . $nonce . '" />';
        echo '</form>';
    }
}
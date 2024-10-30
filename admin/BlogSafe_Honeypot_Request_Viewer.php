<?php

if (!defined('WPINC')) {
    die;
}

class BS_Honeypot_Request_Table extends WP_List_Table {
    var $example_data = array();

    function __construct() {
        global $status, $page;
        parent::__construct(array(
            'singular' => __('Log Entry', 'mylisttable'), //singular name of the listed records
            'plural' => __('Log Entries', 'mylisttable'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));
    }
    
    function display_tablenav($which) {
        ?>
        <div class="tablenav <?php echo esc_attr($which); ?>">

            <div class="alignleft actions">
        <?php $this->bulk_actions(); ?>
            </div>
                <?php
                $this->extra_tablenav($which);
                $this->pagination($which);
                ?>
            <br class="clear" />
        </div>
        <?php
    }    

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'ip':
            case 'remoteHost':
            case 'isTOR':
            case 'urlrequested':
            case 'Data':
            case 'agent':
            case 'referrer':
            case 'Method':
            case 'timestamp':
            case 'Performance':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    public function search_box($text, $input_id) {
            ?>
        <p class="search-box">
            <label class="screen-reader-text" for="
                <?php echo esc_attr($input_id); ?>
                   ">
                    <?php echo $text; ?>
                :</label>
            <input type="search" id="
                <?php echo esc_attr($input_id); ?>
                   " name="requestsearch" value="
                       <?php _admin_search_query(); ?>
                   " />
        <?php submit_button($text, '', '', false, array('id' => 'search-submit')); ?>
        </p>
            <?php
    }

    function column_ip($item) {
        $nonce = wp_create_nonce('BSHnonce');
        $actions = array(
            'deletelog' => sprintf('<a href="?page=%s&action=%s&id=%s&BSHnonce=' . $nonce . '" onclick="return confirm(\'' . __('Are you sure you want to delete this Log Entry?', 'blogsafe-honeypot') . '\')">' . __('Delete', 'blogsafe-honeypot') . '</a>', 'BlogSafeHoneypot', 'deletelog', $item['id']),
            'whitelog' => sprintf('<a href="?page=%s&action=%s&IP=%s&BSHnonce=' . $nonce . '" onclick="return confirm(\'' . __('Are you sure you want to whitelist this IP Address?', 'blogsafe-honeypot') . '\')">' . __('Whitelist IP', 'blogsafe-honeypot') . '</a>', 'BlogSafeHoneypot', 'insertIP', $item['ip']),
        );
        return sprintf('%1$s %2$s', $item['ip'], $this->row_actions($actions));
    }

    function column_urlrequested($item) {
        $nonce = wp_create_nonce('BSHnonce');
        $actions = array(
            'whiteurl' => sprintf('<a href="?page=%s&action=%s&URL=%s&BSHnonce=' . $nonce . '" onclick="return confirm(\'' . __('Are you sure you want to whitelist this URL?', 'blogsafe-honeypot') . '\')">' . __('Whitelist URL', 'blogsafe-honeypot') . '</a>', 'BlogSafeHoneypot', 'insertURL', $item['urlrequested'])
        );
        return sprintf('%1$s %2$s', $item['urlrequested'], $this->row_actions($actions));
    }

    function column_isTOR($item) {
        if ($item['isTOR'] == 1) {
            $item['isTOR'] = 'Yes';
        } else {
            $item['isTOR'] = 'No';
        }
        return $item['isTOR'];
    }

    function column_Performance($item) {
        return ($item['Performance'] * 1000) . ' ms';
    }

    function get_bulk_actions() {
        $actions = array(
            'deletelog' => __('Delete', 'blogsafe-honeypot'),
            'whitelog' => __('Whitelist IP', 'blogsafe-honeypot'),
            'whiteurl' => __('Whitelist URL', 'blogsafe-honeypot')
        );
        return $actions;
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="id[]" value="%s" />', $item['id']);
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'ip' => __('IP', 'mylisttable'),
            'remoteHost' => __('Remote Host', 'mylisttable'),
            'isTOR' => __('TOR', 'mylisttable'),
            'urlrequested' => __('URL Requested', 'mylisttable'),
            'agent' => __('User Agent', 'mylisttable'),
            'referrer' => __('Referrer', 'mylisttable'),
            'Method' => __('Method', 'mylisttable'),
            'Data' => __('Query', 'mylisttable'),
            'timestamp' => __('Date/Time', 'mylisttable'),
            'Performance' => __('Performance', 'mylisttable')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'ip' => array(
                'ip',
                false
            ),
            'remoteHost' => array(
                'remoteHost',
                false
            ),
            'isTOR' => array(
                'isTOR',
                false
            ),
            'urlrequested' => array(
                'urlrequested',
                false
            ),
            'Data' => array(
                'Data',
                false
            ),
            'agent' => array(
                'agent',
                false
            ),
            'referrer' => array(
                'referrer',
                false
            ),
            'Method' => array(
                'Method',
                false
            ),
            'timestamp' => array(
                'timestamp',
                false
            ),
            'Performance' => array(
                'Performance',
                false
            )
        );
        return $sortable_columns;
    }

    function get_items($column = 'timestamp', $order = 'DESC') {
        global $wpdb;
        $table_name = $wpdb->prefix . "BS_Honeypot_Requests";
        switch ($column) {
            case 'ip':
                $column = 'ip';
                break;
            case 'remoteHost':
                $column = 'remoteHost';
                break;
            case 'isTOR':
                $column = 'isTOR';
                break;
            case 'urlrequested':
                $column = 'urlrequested';
                break;
            case 'Data':
                $column = 'Data';
                break;
            case 'agent':
                $column = 'agent';
                break;
            case 'referrer':
                $column = 'referrer';
                break;
            case 'method':
                $column = 'method';
                break;
            case 'timestamp':
                $column = 'timestamp';
                break;
            case 'Performance':
                $column = 'Performance';
                break;
        }
        if (isset($_GET['requestsearch']) && !empty($_GET['requestsearch'])) {
            $z = sanitize_text_field($_GET['requestsearch']);                 
            $SQL = $wpdb->remove_placeholder_escape($wpdb->prepare("SELECT * FROM $table_name where ip LIKE '%%%s%%' or referrer LIKE '%%%s%%' or urlrequested LIKE '%%%s%%' or data LIKE '%%%s%%' or remoteHost LIKE '%%%s%%' or agent LIKE '%%%s%%' and ipwhitelisted = 0 and urlwhitelisted = 0 order by %1s %1s", $z, $z, $z, $z, $z, $z, $column, $order));
            } else {
            $SQL = $wpdb->prepare("SELECT * FROM $table_name where ipwhitelisted = 0 and urlwhitelisted = 0 order by %1s %1s", $column, $order);
        }
        $mylink = $wpdb->get_results($SQL);
        return $mylink;
    }

    function prepare_items() {
        if (isset($_GET['orderby']) && isset($_GET['order'])) {
            $orderby = sanitize_text_field($_GET['orderby']);
            $order = sanitize_text_field($_GET['order']);
        } else {
            $orderby = 'timestamp';
            $order = 'desc';
        }
        $mylink = $this->get_items($orderby, $order);
        $example_data = array();
        foreach ($mylink as $link) {
            $example_data[] = array(
                'id' => $link->id,
                'ip' => $link->ip,
                'remoteHost' => $link->remoteHost,
                'isTOR' => $link->isTOR,
                'urlrequested' => $link->urlrequested,
                'Data' => $link->Data,
                'agent' => $link->agent,
                'referrer' => $link->referrer,
                'Method' => $link->Method,
                'timestamp' => $link->timestamp,
                'Performance' => $link->Performance
            );
        }
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($example_data);
        // only ncessary because we have sample data
        if ($total_items > 0) {
            $example_data = array_slice($example_data, (($current_page - 1) * $per_page), $per_page);
        }
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ));
        $this->items = $example_data;
    }
}

//class

function BSHoneypot_Render_Request_Page() {
    $myListTable = new BS_Honeypot_Request_Table();
    $myListTable->prepare_items();
    $nonce = wp_create_nonce('BSHnonce');
    echo '</pre><div class="wrap"><h3>' . __('Incoming Request Logs', 'blogsafe-honeypot') . '</h3>';
    echo '<p>' . __('This is a list of incoming request that BlogSafe Honeypot has detected.  This list is not a list of all traffic to this site. Rather, it is a list of requests that are considered unusual or were directed to pages or files that don\'t exist on this server.', 'blogsafe-honeypot');
    echo '<form id="events-filter" method="get">';
    echo '<input type="hidden" name="page" value="BlogSafeHoneypot" />' . $myListTable->search_box('search', 'search_id');
    $myListTable->display();
    echo '<input name="BSHnonce" type="hidden" value="' . $nonce . '" />';
    echo '</form>';
    echo '</div>';
}

function BSHoneypot_Show_Logs() {
    BSHoneypot_Render_Request_Page();
}


?>
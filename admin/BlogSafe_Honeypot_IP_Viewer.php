<?php
if (!defined('WPINC')) {
    die;
}

class BSHoneypot_IP_List_Table extends WP_List_Table {
    var $example_data = array();

    function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular' => __('IP Address', 'mylisttable'), //singular name of the listed records
            'plural' => __('IP Addresses', 'mylisttable'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));
    }

    public function search_box($text, $input_id) {

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
            case 'ipaddress':
            case 'addedby':
            case 'notes':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_ipaddress($item) {
        $nonce = wp_create_nonce('BSHnonce');
        $actions = array(
            'deleteip' => sprintf('<a href="?page=%s&action=%s&id=%s&BSHnonce=' . $nonce . '" onclick="return confirm(\'' . __('Are you sure you want to delete this IP Address?', 'blogsafe-honeypot') . '\')">' . __('Delete', 'blogsafe-honeypot') . '</a>', 'BlogSafeHoneypot', 'deleteip', $item['ID'])
        );

        return sprintf('%1$s %2$s', $item['ipaddress'], $this->row_actions($actions));
    }

    function get_bulk_actions() {
        $actions = array(
            'deleteip' => __('Delete', 'blogsafe-honeypot')
        );
        return $actions;
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="id[]" value="%s" />', $item['ID']);
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'ipaddress' => __('IP Address', 'mylisttable'),
            'addedby' => __('Added by', 'mylisttable'),
            'notes' => __('Notes', 'mylisttable')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'ipaddress' => array(
                'ipaddress',
                false
            ),
            'addedby' => array(
                'addedby',
                false
            ),
            'notes' => array(
                'notes',
                false
            )
        );
        return $sortable_columns;
    }

    function get_items($column = 'ipaddress', $order = 'DESC') {
        global $wpdb;
        $table_name = $wpdb->prefix . "BS_Honeypot_IP_Ignores";
        switch ($column) {
            case 'ipaddress':
                $column = 'ipaddress';
                break;
            case 'addedby':
                $column = 'addedby';
                break;
            case 'notes':
                $column = 'notes';
                break;
        }
        if (isset($_GET['ipsearch']) && !empty($_GET['ipsearch'])) {
            $z = sanitize_text_field($_GET['ipsearch']);
            $myfilter = ' where notes LIKE "%%%s%%" or addedby LIKE "%%%s%%" or ipaddress LIKE "%%%s%%"';
            $SQL = $wpdb->prepare("SELECT * FROM $table_name" . $myfilter . " order by %1s %1s", $z, $z, $z, $column, $order);
        } else {
            $SQL = $wpdb->prepare("SELECT * FROM $table_name order by %1s %1s", $column, $order);
        }
        $mylink = $wpdb->get_results($SQL);
        return $mylink;
    }

    function prepare_items() {
        if (isset($_GET['orderby']) && isset($_GET['order'])) {
            $orderby = sanitize_text_field($_GET['orderby']);
            $order = sanitize_text_field($_GET['order']);
        } else {
            $orderby = 'ipaddress';
            $order = 'asc';
        }
        $mylink = $this->get_items($orderby, $order);
        $example_data = array();
        foreach ($mylink as $link) {
            $example_data[] = array(
                'ID' => $link->ID,
                'ipaddress' => $link->ipaddress,
                'addedby' => $link->addedby,
                'notes' => $link->notes
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

function BSHoneypot_Render_IP_List_Page() {
    $myListTable = new BSHoneypot_IP_List_Table();
    $myListTable->prepare_items();
    $nonce = wp_create_nonce('BSHnonce');

    echo '</pre><div class="wrap"><h3>' . __('IP Whitelist', 'blogsafe-honeypot') . '</h3>';
    echo '<p>This is a list of IP addresses that BlogSafe Honeypot will ignore should a request come in from them. Users logged into the admin panel are automatically ignored.</p>';
    echo '<form method="get">';
    echo '<input type="hidden" name="page" value="BlogSafeHoneypot" />' . $myListTable->search_box('search', 'search_id');
    echo '<button class="button-primary" type="submit" name="action" value="addip">' . __('Add an IP Address', 'blogsafe-honeypot') . '</button>&nbsp;';
    echo '<input name="BSHnonce" type="hidden" value="' . $nonce . '" />';
    echo '</form>';

    echo '<form id="events-filter" method="get">';
    $myListTable->display();
    echo '<input type="hidden" name="page" value="BlogSafeHoneypot" />';
    echo '<input name="BSHnonce" type="hidden" value="' . $nonce . '" />';
    echo '</form>';
    echo '</div>';
}

function BSHoneypot_Show_IP_Whitelist() {
    BSHoneypot_Render_IP_List_Page();
}

function BSHoneypot_Delete_IP($thisid) {
    global $wpdb;
    $table_name = $wpdb->prefix . "BS_Honeypot_IP_Ignores";
    $SQL = $wpdb->prepare("Select ipaddress from $table_name where ID = %s ", $thisid);
    if (($row = $wpdb->get_row($SQL)) !== false) {  
        $ip = $row->ipaddress;
        $wpdb->delete($table_name, array(
            'ID' => $thisid
        ));
        $table_name = $wpdb->prefix . 'BS_Honeypot_Requests';
        $SQL = $wpdb->prepare("UPDATE $table_name set ipwhitelisted = 0 where ip = %s", $ip);
        $wpdb->query($SQL);
        $table_name = $wpdb->prefix . 'BS_Honeypot_Logins';
        $SQL = $wpdb->prepare("UPDATE $table_name set whitelisted = 0 where ip = %s", $ip);
        $wpdb->query($SQL);
    }
}

function BSHoneypot_Delete_Whitelist_IP() {

    if (is_array($_GET['id'])) {
        $count = 0;
        foreach (sanitize_text_field($_GET['id']) as $id) {
            BSHoneypot_Delete_IP($id);
            $count++;
        }
        if ($count > 1) {
            $msgarray = array('message' => $count . __(' IP\'s Deleted!', 'blogsafe-honeypot'), 'type' => 1);
        } else {
            $msgarray = array('message' => __('IP Deleted!', 'blogsafe-honeypot'), 'type' => 1);
        }
    } elseif (isset($_GET['id'])) {
        BSHoneypot_Delete_IP(sanitize_text_field($_GET['id']));
        $msgarray = array('message' => __('IP Address Deleted!', 'blogsafe-honeypot'), 'type' => 1);
    } else {
        $msgarray = array('message' => __('No IP addresses selected.', 'blogsafe-honeypot'), 'type' => 1);
    }
    update_option('BSHoneypot_error_message', serialize($msgarray));
}

function BSHoneypot_Insert_Whitelist_IP($ip, $notes, $user = '') {
    global $wpdb;

    $table_name = $wpdb->prefix . "BS_Honeypot_IP_Ignores";
    if ($ip == '') {
        return;
    }
    $sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name where ipaddress = %s", $ip);
    $ipcount = $wpdb->get_var($sql);
    if ($ipcount == 0) {
        if (empty($user)) {
            $current_user = wp_get_current_user();
            $user = $current_user->display_name;
        }
        $myquery = $wpdb->prepare("INSERT INTO " . $table_name . " 
	(
	ipaddress,
	addedby,
	notes
	)
	VALUES 
	( %s, %s, %s )
	", $ip, $user, $notes);
        $wpdb->query($myquery);
        $table_name = $wpdb->prefix . 'BS_Honeypot_Requests';
        $SQL = $wpdb->prepare("UPDATE $table_name set ipwhitelisted = 1 where ip = %s", $ip);
        $wpdb->query($SQL);
        $table_name = $wpdb->prefix . 'BS_Honeypot_Logins';
        $SQL = $wpdb->prepare("UPDATE $table_name set whitelisted = 1 where ip = %s", $ip);
        $wpdb->query($SQL);
    }
}

function BSHoneypot_Show_Add_IP() {
    $nonce = wp_create_nonce('BSHnonce');
    echo '
		<h3>Whitelist an IP Address</h3>
		<p>Add an IP address that BSHoneypot should ignore when requests are received from that address.</p>
		<form id="form1" name="form1" method="get" action="">
		  <table width="100%" border="0" cellpadding="0" cellspacing="5">
			<tr>
			  <td width="19%">IP Address</td>
			  <td>Notes</td>
			</tr>
			<tr>
			  <td><label for="IP"></label>
			  <input name="IP" type="text" id="IP" size="35" maxlength="100" /></td>
			  <td><label for="Notes"></label>
			  <input name="Notes" type="text" id="Notes" size="35" maxlength="255" /></td>
			</tr>
			<tr>
			  <td colspan="2">
				<button class="button-primary" type="submit" name="action" value="insertIP">' . __('Add to IP Whitelist', 'blogsafe-honeypot') . '</button>
			  </td>
			</tr>
			<tr>
			  <td colspan="2">&nbsp;</td>
		    </tr>
			<tr>
			  <td colspan="2">Add your current IP address to the whitelist.</td>
		    </tr>

		  </table>
		  <input name="action" type="hidden" value="insertIP" />
		  <input name="page" type="hidden" value="BlogSafeHoneypot" />
		  <input name="BSHnonce" type="hidden" value="' . $nonce . '" />
  		  </form>';


    echo '<form>
		  <table width="100%" border="0" cellpadding="0" cellspacing="5">
		  <tr>
		  <td colspan="2"><button class="button-primary" type="submit" name="action" value="insertcurrentIP">' . __('Add Current IP', 'blogsafe-honeypot') . '</button>
		  ' . $_SERVER['REMOTE_ADDR'] . '
		  <input name="currentip" type="hidden" value="' . $_SERVER['REMOTE_ADDR'] . '" />
		  </td>
		  </tr>
		  </table>
		  <input name="BSHnonce" type="hidden" value="' . $nonce . '" />
		  <input name="page" type="hidden" value="BlogSafeHoneypot" />
		  </form>';
}
?>
<?php
if (!defined('WPINC')) {
    die;
}

class BSHoneypot_URL_List_Table extends WP_List_Table {
    var $example_data = array();

    function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular' => __('URL', 'mylisttable'), //singular name of the listed records
            'plural' => __('URL\'s', 'mylisttable'), //plural name of the listed records
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
            case 'url':
            case 'addedby':
            case 'notes':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_url($item) {
        $nonce = wp_create_nonce('BSHnonce');
        $actions = array(
            'deleteurl' => sprintf('<a href="?page=%s&action=%s&id=%s&BSHnonce=' . $nonce . '" onclick="return confirm(\'' . __('Are you sure you want to delete this URL?', 'blogsafe-honeypot') . '\')">' . __('Delete', 'blogsafe-honeypot') . '</a>', 'BlogSafeHoneypot', 'deleteurl', $item['ID'])
        );

        return sprintf('%1$s %2$s', $item['url'], $this->row_actions($actions));
    }

    function get_bulk_actions() {
        $actions = array(
            'deleteurl' => __('Delete', 'blogsafe-honeypot')
        );
        return $actions;
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="id[]" value="%s" />', $item['ID']);
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'url' => __('URL', 'mylisttable'),
            'addedby' => __('Added by', 'mylisttable'),
            'notes' => __('Notes', 'mylisttable')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'url' => array(
                'url',
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

    function get_items($column = 'url', $order = 'DESC') {
        global $wpdb;
        $table_name = $wpdb->prefix . "BS_Honeypot_URL_Ignores";
        switch ($column) {
            case 'url':
                $column = 'url';
                break;
            case 'addedby':
                $column = 'addedby';
                break;
            case 'notes':
                $column = 'notes';
                break;
        }
        if (isset($_GET['urlsearch']) && !empty($_GET['urlsearch'])) {
            $z = sanitize_text_field($_GET['urlsearch']);
            $myfilter = ' where notes LIKE "%%%s%%" or addedby LIKE "%%%s%%" or url LIKE "%%%s%%"';
            $SQL = $wpdb->prepare("SELECT * FROM $table_name" . $myfilter . " order by %1s %1s", $z, $z, $z, $column, $order);
        } else {
            $SQL = $wpdb->prepare("SELECT * FROM $table_name order by %1s %1s", $column, $order);
        }
        $mylink = $wpdb->get_results($SQL);
        return $mylink;
    }

    function prepare_items() {
        $_SERVER['REQUEST_URI'] = remove_query_arg('_wp_http_referer', $_SERVER['REQUEST_URI']);
        $_SERVER['REQUEST_URI'] = remove_query_arg('action', $_SERVER['REQUEST_URI']);
        $_SERVER['REQUEST_URI'] = add_query_arg('action', 'URLWhitelist', $_SERVER['REQUEST_URI']);
        if (isset($_GET['orderby']) && isset($_GET['order'])) {
            $orderby = sanitize_text_field($_GET['orderby']);
            $order = sanitize_text_field($_GET['order']);
        } else {
            $orderby = 'url';
            $order = 'asc';
        }
        $mylink = $this->get_items($orderby, $order);
        $example_data = array();
        foreach ($mylink as $link) {
            $example_data[] = array(
                'ID' => $link->ID,
                'url' => $link->url,
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

function BSHoneypot_Render_URL_List_Page() {
    $myListTable = new BSHoneypot_URL_List_Table();
    $myListTable->prepare_items();
    $nonce = wp_create_nonce('BSHnonce');

    echo '</pre><div class="wrap"><h3>' . __('URL Whitelist', 'blogsafe-honeypot') . '</h3>';
    echo '<p>This is a list of partial URL\'s that BSHoneypot will ignore should a request come in from them.  If any part of the text in the URL field is found in the incoming URL, that request is ignored.';
    echo '<form method="get">';
    echo '<input type="hidden" name="page" value="BlogSafeHoneypot" />' . $myListTable->search_box('search', 'search_id');
    echo '<button class="button-primary" type="submit" name="action" value="addignoreurl">' . __('Add a string to ignore.', 'blogsafe-honeypot') . '</button>&nbsp;';
    echo '<input name="BSHnonce" type="hidden" value="' . $nonce . '" />';
    echo '</form>';

    echo '<form id="events-filter" method="get">';
    echo '<input type="hidden" name="page" value="BlogSafeHoneypot" />';
    $myListTable->display();
    echo '<input name="BSHnonce" type="hidden" value="' . $nonce . '" />';
    echo '</form>';
    echo '</div>';
}

function BSHoneypot_Show_URL_Whitelist() {
    BSHoneypot_Render_URL_List_Page();
}

function BSHoneypot_Delete_URL($thisid) {
    global $wpdb;
    $table_name = $wpdb->prefix . "BS_Honeypot_URL_Ignores";
    $SQL = $wpdb->prepare("Select url from $table_name where ID = %s ", $thisid);
    if (($row = $wpdb->get_row($SQL)) !== false) {
        $wpdb->delete($table_name, array(
            'ID' => $thisid
        ));
        $table_name = $wpdb->prefix . 'BS_Honeypot_Requests';
        $SQL = $wpdb->prepare("UPDATE $table_name set urlwhitelisted = 0 where urlrequested like %s", $wpdb->esc_like($row->url));
        $wpdb->query($SQL);
    }
}

function BSHoneypot_Delete_Whitelist_URL() {
    if (is_array($_GET['id'])) {
        $count = 0;
        foreach (sanitize_text_field($_GET['id']) as $id) {
            BSHoneypot_Delete_URL($id);
            $count++;
        }
        if ($count > 1) {
            $msgarray = array('message' => $count . __(' URL\'s Deleted!', 'blogsafe-honeypot'), 'type' => 1);
        } else {
            $msgarray = array('message' => __('URL Deleted!', 'blogsafe-honeypot'), 'type' => 1);
        }
    } elseif (isset($_GET['id'])) {
        BSHoneypot_Delete_URL(sanitize_text_field($_GET['id']));
        $msgarray = array('message' => __('URL Deleted!', 'blogsafe-honeypot'), 'type' => 1);
    } else {
        $msgarray = array('message' => __('No URL\'s selected.', 'blogsafe-honeypot'), 'type' => 2);
    }
    update_option('BSHoneypot_error_message', serialize($msgarray));
}

function BSHoneypot_Insert_Whitelist_URL($url, $notes) {

    global $wpdb;

    $table_name = $wpdb->prefix . "BS_Honeypot_URL_Ignores";
    if ($url == '') {
        $msgarray = array('message' => __('Invalid URL!', 'blogsafe-honeypot'), 'type' => 2);
        return;
    }
    $sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name where url = %s", $url);
    $urlcount = $wpdb->get_var($sql);
    if ($urlcount == 0) {
        $current_user = wp_get_current_user();
        $myquery = $wpdb->prepare("INSERT INTO " . $table_name . " 
				(
					url,
					addedby,
					notes
				)
				VALUES 
				( %s, %s, %s )
				", $url, $current_user->display_name, $notes);
        $wpdb->query($myquery);
        $table_name = $wpdb->prefix . 'BS_Honeypot_Requests';
        $SQL = $wpdb->prepare("UPDATE $table_name set urlwhitelisted = 1 where urlrequested like %s", $wpdb->esc_like($url));
        $wpdb->query($SQL);
        $msgarray = array('message' => __('URL Added!', 'blogsafe-honeypot'), 'type' => 1);
    } else {
        $msgarray = array('message' => __('That URL: ' . $url . ', already exists.', 'blogsafe-honeypot'), 'type' => 1);
    }
    update_option('BSHoneypot_error_message', serialize($msgarray));
}

function BSHoneypot_Show_Add_URL() {
    $nonce = wp_create_nonce('BSHnonce');
    echo '<h3>' . __('Whitelist a URL', 'blogsafe-honeypot') . '</h3>
		<p>' . __('Add a URL request that BSHoneypot should ignore when requests are received.', 'blogsafe-honeypot') . '</p>
		<form id="form1" name="form1" method="get" action="">
		  <table width="100%" border="0" cellpadding="0" cellspacing="5">
			<tr>
			  <td width="19%">' . __('URL', 'blogsafe-honeypot') . '</td>
			  <td>Notes</td>
			</tr>
			<tr>
			  <td><label for="URL"></label>
			  <input name="URL" type="text" id="IP" size="35" maxlength="100" /></td>
			  <td><label for="' . __('Notes', 'blogsafe-honeypot') . '"></label>
			  <input name="Notes" type="text" id="Notes" size="35" maxlength="255" /></td>
			</tr>
			<tr>
			  <td colspan="2">
				<button class="button-primary" type="submit" name="action" value="insertURL">' . __('Add to URL Whitelist', 'blogsafe-honeypot') . '</button>
			  </td>
			</tr>
			<tr>
			  <td colspan="2">&nbsp;</td>
		    </tr>

		  </table>
		  <input name="action" type="hidden" value="insertURL" />
		  <input name="page" type="hidden" value="BlogSafeHoneypot" />
		  <input name="BSHnonce" type="hidden" value="' . $nonce . '" />
  		  </form>';
}
?>
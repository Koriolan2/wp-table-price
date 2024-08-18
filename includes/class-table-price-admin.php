<?php

class TablePriceAdmin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_menu'));
        add_action('admin_post_save_table_price_query', array($this, 'save_table_price_query'));
        add_action('admin_post_delete_table_price_query', array($this, 'delete_table_price_query'));
    }

    // Function to add plugin menu
    public function add_plugin_menu() {
        add_menu_page('Table Price Plugin', 'Table Price', 'manage_options', 'table-price-plugin', array($this, 'plugin_settings_page'));
    }

    // Function to render plugin settings page
    public function plugin_settings_page() {
        ?>
        <div class="wrap">
            <h1>Table Price Plugin</h1>
            <form method="post" action="admin-post.php">
                <input type="hidden" name="action" value="save_table_price_query">
                <?php wp_nonce_field('save_table_price_query_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="query">SQL Query</label></th>
                        <td><textarea name="query" id="query" class="large-text code" rows="10"></textarea></td>
                    </tr>
                </table>
                <?php submit_button('Save Query'); ?>
            </form>
            <h2>Existing Queries</h2>
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th scope="col">Query ID</th>
                        <th scope="col">SQL Query</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $wpdb;
                    $query_ids = $wpdb->get_col("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'table_price_query_%'");
                    foreach ($query_ids as $query_id) {
                        $id = str_replace('table_price_query_', '', $query_id);
                        $query = get_option($query_id);
                        echo "<tr>
                                <td>" . esc_html($id) . "</td>
                                <td><code>" . esc_html($query) . "</code></td>
                                <td>
                                    <a href='" . admin_url('admin.php?page=table-price-plugin&edit=' . $id) . "'>Edit</a> | 
                                    <a href='" . admin_url('admin-post.php?action=delete_table_price_query&id=' . $id) . "'>Delete</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php if (isset($_GET['edit'])): 
                $edit_id = intval($_GET['edit']);
                $edit_query = get_option('table_price_query_' . $edit_id);
            ?>
                <h2>Edit Query ID: <?php echo $edit_id; ?></h2>
                <form method="post" action="admin-post.php">
                    <input type="hidden" name="action" value="save_table_price_query">
                    <input type="hidden" name="query_id" value="<?php echo $edit_id; ?>">
                    <?php wp_nonce_field('save_table_price_query_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="query">SQL Query</label></th>
                            <td><textarea name="query" id="query" class="large-text code" rows="10"><?php echo esc_textarea($edit_query); ?></textarea></td>
                        </tr>
                    </table>
                    <?php submit_button('Update Query'); ?>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }

    // Function to save custom table query
    public function save_table_price_query() {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'save_table_price_query_nonce')) {
            wp_die('Nonce verification failed.');
        }

        if (isset($_POST['query'])) {
            $query = wp_unslash($_POST['query']); // Remove escaping added by sanitize_textarea_field
            if (isset($_POST['query_id'])) {
                $query_id = intval($_POST['query_id']);
            } else {
                $query_id = $this->generate_query_id();
            }
            update_option('table_price_query_' . $query_id, $query);
        }

        wp_redirect(admin_url('admin.php?page=table-price-plugin'));
        exit;
    }

    // Function to delete custom table query
    public function delete_table_price_query() {
        if (!isset($_GET['id'])) {
            wp_die('Invalid query ID.');
        }

        $query_id = intval($_GET['id']);
        delete_option('table_price_query_' . $query_id);

        wp_redirect(admin_url('admin.php?page=table-price-plugin'));
        exit;
    }

    // Function to generate unique query ID
    private function generate_query_id() {
        global $wpdb;
        $query_ids = $wpdb->get_col("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'table_price_query_%'");
        $max_id = 0;
        foreach ($query_ids as $query_id) {
            $id = intval(str_replace('table_price_query_', '', $query_id));
            if ($id > $max_id) {
                $max_id = $id;
            }
        }
        return $max_id + 1;
    }
}

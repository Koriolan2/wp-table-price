<?php

class TablePricePlugin {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('table_price', array($this, 'render_table_shortcode'));
    }

    // Registering and connecting scripts and DataTables styles
    public function enqueue_scripts() {
        wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
        wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'), null, true);
        wp_enqueue_style('custom-plugin-styles', plugin_dir_url(__FILE__) . 'custom-table-styles.css', array());
    }

    // Function to get the access level of the current user from Access Level
    private function get_user_access_level() {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            return get_user_meta($user_id, 'access_level', true);
        }
        return false;
    }

    // Service method that generates a table header
    private function generate_table_header($columns, $userRole) {
        $header = "<thead><tr>";
        foreach ($columns as $column) {
            $header .= "<th>" . esc_html($column) . "</th>";
        }
        $header .= "</tr></thead>";
        return $header;
    }

    // Service method that generates the body of the table
    private function generate_table_body($results) {
        $body = "<tbody>";
        foreach ($results as $row) {
            $body .= "<tr>";
            foreach ($row as $cell) {
                $body .= "<td>" . esc_html($cell) . "</td>";
            }
            $body .= "</tr>";
        }
        $body .= "</tbody>";
        return $body;
    }

    // Service method for setting up DataTables
    private function initialize_datatables_script() {
        return "<script>
            jQuery(document).ready(function($) {
                $('#table_price').DataTable({
                    order: [
                        [0, 'asc'],
                        [1, 'asc']
                    ],
                    paging: true,
                    pageLength: 25,
                    searching: true,
                });
            });
        </script>";
    }

    // Generation of a table with data
    private function generate_table($results, $columns, $userRole) {
        $table = "<table id='table_price' class='display'>";
        $table .= $this->generate_table_header($columns, $userRole);
        $table .= $this->generate_table_body($results);
        $table .= "</table>";
        $table .= $this->initialize_datatables_script();
        return $table;
    }

    // Function for creating an HTML table with data from a database
    public function render_table_shortcode($atts) {
        global $wpdb;
        $atts = shortcode_atts(array('id' => ''), $atts);
        $query_id = intval($atts['id']);
        $query = get_option('table_price_query_' . $query_id);

        if (!$query) {
            return "Invalid query ID.";
        }

        $results = $wpdb->get_results($query);
        if (!$results) {
            return "Query execution failed: " . $wpdb->last_error;
        }
        $columns = array_keys((array)$wpdb->get_row($query, ARRAY_A));
        $userRole = $this->get_user_access_level();

        if ($results) {
            return $this->generate_table($results, $columns, $userRole);
        } else {
            return "No results found.";
        }
    }
}

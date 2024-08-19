<?php

class TablePricePlugin {

    private $tableGenerator;

    public function __construct() {
        $this->tableGenerator = new TableGenerator();
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('table_price', array($this, 'render_table_shortcode'));
    }

    // Registering and connecting scripts and DataTables styles
    public function enqueue_scripts() {
        wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
        wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'), null, true);
        wp_enqueue_style('custom-plugin-styles', plugin_dir_url(__FILE__) . 'custom-table-styles.css', array());
    }

    // Function for creating an HTML table with data from a database
    public function render_table_shortcode($atts) {
        global $wpdb;
        $atts = shortcode_atts(array('id' => ''), $atts);
        $query_id = intval($atts['id']);
        $query = get_post_meta($query_id, '_table_price_query', true);

        if (!$query) {
            return "Invalid query ID.";
        }

        $results = $wpdb->get_results($query);
        if (!$results) {
            return "Query execution failed: " . $wpdb->last_error;
        }
        $columns = array_keys((array)$wpdb->get_row($query, ARRAY_A));
        $userRole = $this->tableGenerator->get_user_access_level();
        $settings = get_post_meta($query_id, '_table_price_settings', true);

        if ($results) {
            return $this->tableGenerator->generate_table($results, $columns, $userRole, $settings);
        } else {
            return "No results found.";
        }
    }
}

<?php

class TableGenerator {

    // Function to get the access level of the current user from Access Level
    public function get_user_access_level() {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            return get_user_meta($user_id, 'access_level', true);
        }
        return false;
    }

    // Service method that generates a table header
    public function generate_table_header($columns, $userRole) {
        $header = "<thead><tr>";
        foreach ($columns as $column) {
            $header .= "<th>" . esc_html($column) . "</th>";
        }
        $header .= "</tr></thead>";
        return $header;
    }

    // Service method that generates the body of the table
    public function generate_table_body($results) {
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
    public function initialize_datatables_script($settings) {
        $ordering = isset($settings['ordering']) && $settings['ordering'] ? 'true' : 'false';
        $paging = isset($settings['paging']) && $settings['paging'] ? 'true' : 'false';
        $searching = isset($settings['searching']) && $settings['searching'] ? 'true' : 'false';
        $pageLength = isset($settings['pageLength']) ? intval($settings['pageLength']) : 25;

        return "<script>
            jQuery(document).ready(function($) {
                $('#table_price').DataTable({
                    order: [
                        [0, 'asc'],
                        [1, 'asc']
                    ],
                    paging: $paging,
                    pageLength: $pageLength,
                    searching: $searching,
                    ordering: $ordering,
                });
            });
        </script>";
    }

    // Generation of a table with data
    public function generate_table($results, $columns, $userRole, $settings) {
        $table = "<table id='table_price' class='display'>";
        $table .= $this->generate_table_header($columns, $userRole);
        $table .= $this->generate_table_body($results);
        $table .= "</table>";
        $table .= $this->initialize_datatables_script($settings);
        return $table;
    }
}

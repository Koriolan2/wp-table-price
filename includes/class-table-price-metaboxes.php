<?php

class TablePriceMetaboxes {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post'));
        add_action('edit_form_after_title', array($this, 'display_shortcode_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    // Add meta boxes
    public function add_meta_boxes() {
        add_meta_box(
            'table_price_query_meta_box',
            'SQL Query',
            array($this, 'render_query_meta_box'),
            'table_price_query',
            'normal',
            'high'
        );

        add_meta_box(
            'table_price_settings_meta_box',
            'DataTable Settings',
            array($this, 'render_settings_meta_box'),
            'table_price_query',
            'side',
            'default'
        );
    }

    // Метабокс для відображення шорткоду
    public function display_shortcode_meta_box($post) {
        if ($post->post_type === 'table_price_query') {
            $shortcode = '[table_price id="' . esc_attr($post->ID) . '"]';
            echo '<div class="postbox" style="margin-top:20px;"><h2>Shortcode</h2>';
            echo '<div class="inside">';
            echo '<p>Use the following shortcode to display the table:</p>';
            echo '<div style="display:flex; align-items:center;">';
            echo '<input type="text" id="table_price_shortcode" readonly value="' . esc_attr($shortcode) . '" style="width:100%;" />';
            echo '<button type="button" class="button" id="copy_shortcode_button" style="margin-left:10px;">Copy</button>';
            echo '</div>';
            echo '</div></div>';
        }
    }

    // Render query meta box
    public function render_query_meta_box($post) {
        wp_nonce_field('save_table_price_query', 'table_price_query_nonce');
        $query = get_post_meta($post->ID, '_table_price_query', true);
        echo '<textarea name="table_price_query" class="large-text code" rows="10">' . esc_textarea($query) . '</textarea>';
    }

    // Render settings meta box
    public function render_settings_meta_box($post) {
        $settings = get_post_meta($post->ID, '_table_price_settings', true);
        $ordering = isset($settings['ordering']) ? (bool) $settings['ordering'] : true;
        $paging = isset($settings['paging']) ? (bool) $settings['paging'] : true;
        $searching = isset($settings['searching']) ? (bool) $settings['searching'] : true;
        $pageLength = isset($settings['pageLength']) ? intval($settings['pageLength']) : 25;

        echo '<p><label><input type="checkbox" id="table_price_settings_ordering" name="table_price_settings[ordering]" value="1"' . checked(1, $ordering, false) . '> Enable Column Sorting</label></p>';
        echo '<p><label><input type="checkbox" id="table_price_settings_paging" name="table_price_settings[paging]" value="1"' . checked(1, $paging, false) . '> Enable Pagination</label></p>';
        echo '<p id="rows_per_page_wrapper" style="' . ($paging ? '' : 'display: none;') . '"><label>Rows per Page: <input type="number" id="table_price_settings_pageLength" name="table_price_settings[pageLength]" value="' . esc_attr($pageLength) . '" class="small-text"></label></p>';
        echo '<p><label><input type="checkbox" id="table_price_settings_searching" name="table_price_settings[searching]" value="1"' . checked(1, $searching, false) . '> Enable Search</label></p>';
    }

    // Save post
    public function save_post($post_id) {
        if (!isset($_POST['table_price_query_nonce']) || !wp_verify_nonce($_POST['table_price_query_nonce'], 'save_table_price_query')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['table_price_query'])) {
            $query = sanitize_text_field(wp_unslash($_POST['table_price_query']));
            update_post_meta($post_id, '_table_price_query', $query);
        }

        if (isset($_POST['table_price_settings'])) {
            $settings = array(
                'ordering' => isset($_POST['table_price_settings']['ordering']) ? 1 : 0,
                'paging' => isset($_POST['table_price_settings']['paging']) ? 1 : 0,
                'searching' => isset($_POST['table_price_settings']['searching']) ? 1 : 0,
                'pageLength' => isset($_POST['table_price_settings']['pageLength']) ? intval($_POST['table_price_settings']['pageLength']) : 25
            );

            update_post_meta($post_id, '_table_price_settings', $settings);
        }
    }

    // Enqueue admin scripts and styles
    public function enqueue_admin_scripts($hook_suffix) {
        global $post_type;
        if ($post_type == 'table_price_query') {
            wp_enqueue_style('custom-plugin-styles', plugin_dir_url(__FILE__) . 'custom-table-styles.css', array());
            wp_enqueue_script('custom-admin-scripts', plugin_dir_url(__FILE__) . 'custom-admin-scripts.js', array('jquery'), null, true);
        }
    }
}

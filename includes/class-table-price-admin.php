<?php

class TablePriceAdmin {

    public function __construct() {
        add_action('init', array($this, 'register_custom_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post'));
        add_filter('the_title', array($this, 'filter_title'), 10, 2);
        add_action('admin_footer', array($this, 'hide_title_input'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_filter('manage_edit-table_price_query_columns', array($this, 'set_custom_columns'));
        add_filter('manage_table_price_query_posts_custom_column', array($this, 'custom_column'), 10, 2);
        add_filter('manage_edit-table_price_query_sortable_columns', array($this, 'set_sortable_columns'));
        add_filter('default_hidden_columns', array($this, 'hide_default_columns'), 10, 2);
    }

    // Register custom post type
    public function register_custom_post_type() {
        register_post_type('table_price_query', array(
            'labels' => array(
                'name' => 'Table Price Queries',
                'singular_name' => 'Table Price Query',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Query',
                'edit_item' => 'Edit Query',
                'new_item' => 'New Query',
                'view_item' => 'View Query',
                'search_items' => 'Search Queries',
                'not_found' => 'No Queries found',
                'not_found_in_trash' => 'No Queries found in Trash',
            ),
            'public' => true,
            'has_archive' => true,
            'menu_position' => 20,
            'supports' => array('title', 'author'),
            'show_in_rest' => false,
        ));
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

    // Render query meta box
    public function render_query_meta_box($post) {
        wp_nonce_field('save_table_price_query', 'table_price_query_nonce');
        $query = get_post_meta($post->ID, '_table_price_query', true);
        echo '<textarea name="table_price_query" class="large-text code" rows="10">' . esc_textarea($query) . '</textarea>';
        echo '<p>Shortcode: [table_price id="' . esc_html($post->ID) . '"]</p>';
    }

    // Render settings meta box
    public function render_settings_meta_box($post) {
        // Отримання збережених налаштувань
        $settings = get_post_meta($post->ID, '_table_price_settings', true);
        
        // Якщо налаштування відсутні, встановлюємо значення за замовчуванням
        $ordering = isset($settings['ordering']) ? (bool) $settings['ordering'] : true;  
        $paging = isset($settings['paging']) ? (bool) $settings['paging'] : true;       
        $searching = isset($settings['searching']) ? (bool) $settings['searching'] : true;
        $pageLength = isset($settings['pageLength']) ? intval($settings['pageLength']) : 25;
    
        // Виведення елементів управління з використанням збережених значень
        echo '<p><label><input type="checkbox" id="table_price_settings_ordering" name="table_price_settings[ordering]" value="1"' . checked(1, $ordering, false) . '> Enable Column Sorting</label></p>';
        echo '<p><label><input type="checkbox" id="table_price_settings_paging" name="table_price_settings[paging]" value="1"' . checked(1, $paging, false) . '> Enable Pagination</label></p>';
        echo '<p id="rows_per_page_wrapper" style="' . ($paging ? '' : 'display: none;') . '"><label>Rows per Page: <input type="number" id="table_price_settings_pageLength" name="table_price_settings[pageLength]" value="' . esc_attr($pageLength) . '" class="small-text"></label></p>';
        echo '<p><label><input type="checkbox" id="table_price_settings_searching" name="table_price_settings[searching]" value="1"' . checked(1, $searching, false) . '> Enable Search</label></p>';
    }
    
    

    // Save post
    public function save_post($post_id) {
        // Перевірка nonce
        if (!isset($_POST['table_price_query_nonce']) || !wp_verify_nonce($_POST['table_price_query_nonce'], 'save_table_price_query')) {
            return;
        }
    
        // Перевірка автозбереження
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        // Перевірка прав користувача
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    
        // Збереження SQL-запиту
        if (isset($_POST['table_price_query'])) {
            $query = sanitize_text_field(wp_unslash($_POST['table_price_query']));
            update_post_meta($post_id, '_table_price_query', $query);
            remove_action('save_post', array($this, 'save_post')); // Запобігання циклу
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => '[table_price id="' . $post_id . '"]'
            ));
            add_action('save_post', array($this, 'save_post'));
        }
    
        // Збереження налаштувань DataTable
        if (isset($_POST['table_price_settings'])) {
            $settings = array(
                'ordering' => isset($_POST['table_price_settings']['ordering']) ? 1 : 0,
                'paging' => isset($_POST['table_price_settings']['paging']) ? 1 : 0,
                'searching' => isset($_POST['table_price_settings']['searching']) ? 1 : 0,
                'pageLength' => isset($_POST['table_price_settings']['pageLength']) ? intval($_POST['table_price_settings']['pageLength']) : 25
            );
    
            // Збереження налаштувань
            update_post_meta($post_id, '_table_price_settings', $settings);
        }
    }
    

    // Filter the title
    public function filter_title($title, $post_id) {
        $post = get_post($post_id);
        if ($post->post_type == 'table_price_query') {
            return '[table_price id="' . $post_id . '"]';
        }
        return $title;
    }

    // Hide the title input field using JavaScript
    public function hide_title_input() {
        global $post_type;
        if ($post_type == 'table_price_query') {
            echo '<style>#titlediv { display: none; }</style>';
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

    // Set custom columns for the Table Price Queries list table
    public function set_custom_columns($columns) {
        $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Title',
            'author' => 'Author',
            'date' => 'Date'
        );
        return $new_columns;
    }

    // Populate custom columns for the Table Price Queries list table
    public function custom_column($column, $post_id) {
        // No need to do anything here since the default columns are handled by WordPress
    }

    // Set sortable columns
    public function set_sortable_columns($columns) {
        $columns['title'] = 'title';
        $columns['author'] = 'author';
        $columns['date'] = 'date';
        return $columns;
    }

    // Hide default columns
    public function hide_default_columns($hidden, $screen) {
        if ($screen->post_type === 'table_price_query') {
            $hidden[] = 'wpseo-score'; // Yoast SEO Score
            $hidden[] = 'wpseo-score-readability'; // Yoast SEO Readability Score
            $hidden[] = 'wpseo-title'; // Yoast SEO Title
            $hidden[] = 'wpseo-metadesc'; // Yoast SEO Meta Description
            $hidden[] = 'wpseo-focuskw'; // Yoast SEO Focus Keyword
            $hidden[] = 'wpseo-links'; // Yoast SEO Links
            $hidden[] = 'wpseo-linked'; // Yoast SEO Linked
            $hidden[] = 'sidebars'; // Custom Sidebars
        }
        return $hidden;
    }
}

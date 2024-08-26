<?php

class TablePriceAdmin {

    public function __construct() {
        add_action('init', array($this, 'register_custom_post_type'));
        add_filter('manage_table_price_query_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_table_price_query_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
        add_filter('hidden_columns', array($this, 'hide_unwanted_columns'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
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

    // Налаштування колонок для таблиці
    public function set_custom_columns($columns) {
        // Прибираємо всі зайві колонки, залишаємо тільки ті, які потрібні
        $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Title',
            'author' => 'Author',
            'date' => 'Date',
            'shortcode' => 'Shortcode'
        );
        return $new_columns;
    }

    // Заповнюємо колонки даними
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'shortcode':
                $shortcode = '[table_price id="' . $post_id . '"]';
                echo '<div style="display:flex; align-items:center;">';
                echo '<input type="text" value="' . esc_attr($shortcode) . '" readonly id="shortcode_' . $post_id . '" style="width:80%;" />';
                echo '<button type="button" class="button copy_shortcode_button" data-post-id="' . $post_id . '" style="margin-left:10px;">Copy</button>';
                echo '</div>';
                break;
        }
    }
    

    // Приховуємо зайві колонки
    public function hide_unwanted_columns($hidden, $screen) {
        if ($screen->post_type === 'table_price_query') {
            // Додаємо всі колонки, які хочемо приховати
            $hidden = array_merge($hidden, array(
                'wpseo-score',                // Yoast SEO Score
                'wpseo-score-readability',    // Yoast Readability Score
                'wpseo-links',                // Yoast Outgoing Internal Links
                'wpseo-linked',               // Yoast Received Internal Links
                'wpseo-title',                // Yoast SEO Title
                'wpseo-metadesc',             // Yoast SEO Meta Description
                'cs_replacement',             // Custom Sidebars
            ));
        }
        return $hidden;
    }

    // Підключаємо скрипти для кнопки копіювання
    public function enqueue_admin_scripts($hook_suffix) {
        global $post_type;
        if ($post_type == 'table_price_query') {
            // Додаємо параметр з версією або часом для уникнення кешування
            wp_enqueue_script('custom-admin-scripts', plugin_dir_url(__FILE__) . 'custom-admin-scripts.js', array('jquery'), time(), true);
        }
    }
    
}

add_action('admin_enqueue_scripts', 'enqueue_admin_scripts'); // Пріоритет 999, щоб завантажити пізніше за інші


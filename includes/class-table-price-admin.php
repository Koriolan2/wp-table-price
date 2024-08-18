<?php

class TablePriceAdmin {

    public function __construct() {
        add_action('init', array($this, 'register_custom_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post'));
        add_filter('the_title', array($this, 'filter_title'), 10, 2);
        add_action('admin_footer', array($this, 'hide_title_input'));
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
            'supports' => array('title'),
            'show_in_rest' => false,
        ));
    }

    // Add meta boxes
    public function add_meta_boxes() {
        add_meta_box(
            'table_price_query_meta_box',
            'SQL Query',
            array($this, 'render_meta_box'),
            'table_price_query',
            'normal',
            'high'
        );
    }

    // Render meta box
    public function render_meta_box($post) {
        wp_nonce_field('save_table_price_query', 'table_price_query_nonce');
        $query = get_post_meta($post->ID, '_table_price_query', true);
        echo '<textarea name="table_price_query" class="large-text code" rows="10">' . esc_textarea($query) . '</textarea>';
        echo '<p>Shortcode: [table_price id="' . esc_html($post->ID) . '"]</p>';
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
            $query = wp_unslash($_POST['table_price_query']);
            update_post_meta($post_id, '_table_price_query', $query);
            remove_action('save_post', array($this, 'save_post')); // Avoid infinite loop
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => '[table_price id="' . $post_id . '"]'
            ));
            add_action('save_post', array($this, 'save_post'));
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
}

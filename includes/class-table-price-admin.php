<?php

class TablePriceAdmin {

    public function __construct() {
        add_action('init', array($this, 'register_custom_post_type'));
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
}

<?php
/*
Plugin Name: WP Table Price Plugin
Description: A plugin for displaying data from custom database queries in merged HTML tables. WARNING! To work, you need the Access level plugin, which determines the level of access of users.
Version: 2.1.4
Author: Yuriy Kozmin aka Yuriy Knysh 
Author Email: koriolan2@gmail.com 
License: GPL2
*/

// Define the plugin directory
if (!defined('TABLE_PRICE_PLUGIN_DIR')) {
    define('TABLE_PRICE_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Include necessary files
require_once TABLE_PRICE_PLUGIN_DIR . 'includes/class-table-price-plugin.php';
require_once TABLE_PRICE_PLUGIN_DIR . 'includes/class-table-price-admin.php';
require_once TABLE_PRICE_PLUGIN_DIR . 'includes/class-table-generator.php';

// Initialize the plugin classes
function table_price_plugin_init() {
    new TablePricePlugin();
    new TablePriceAdmin();
}
add_action('plugins_loaded', 'table_price_plugin_init');

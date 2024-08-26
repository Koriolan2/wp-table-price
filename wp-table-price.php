<?php
/*
Plugin Name: WP Table Price Plugin
Description: A plugin for displaying data from custom database queries in merged HTML tables. WARNING! To work, you need the Access level plugin, which determines the level of access of users.
Version: 2.5.1
Author: Yuriy Kozmin aka Yuriy Knysh 
Author Email: koriolan2@gmail.com 
License: GPL2
*/

// Define the plugin directory
if (!defined('TABLE_PRICE_PLUGIN_DIR')) {
    define('TABLE_PRICE_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Include necessary files
require_once TABLE_PRICE_PLUGIN_DIR . 'includes/class-table-price-plugin.php';  // Основна логіка плагіна
require_once TABLE_PRICE_PLUGIN_DIR . 'includes/class-table-price-admin.php';  // Реєстрація користувацького типу запису
require_once TABLE_PRICE_PLUGIN_DIR . 'includes/class-table-price-metaboxes.php';  // Метабокси
require_once TABLE_PRICE_PLUGIN_DIR . 'includes/class-table-generator.php';  // Додатковий клас для генерації таблиць (який був важливим)


// Initialize the plugin classes
function table_price_plugin_init() {
    // Перевіряємо, чи існують класи перед їх ініціалізацією
    if (class_exists('TablePricePlugin')) {
        new TablePricePlugin();
    }
    
    if (class_exists('TablePriceAdmin')) {
        new TablePriceAdmin();
    }
    
    if (class_exists('TablePriceMetaboxes')) {
        new TablePriceMetaboxes();
    }
}
add_action('plugins_loaded', 'table_price_plugin_init');

<?php

/**
 * Plugin Name:       Dev200 - wc-equipment-trader-plugin
 * Description:       Change product building flow.
 * Version:           1.0.0
 * Author:            Dev200
 * Author URI:        https://dev200.co.za
 * Requires at least: 6.3.2
 * Tested up to:      6.3.2
 * PHP:               8.3.3
 * Bootstrap:         4.3.1
 *
 * @package Dev200_Equipment_Trader
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wpdb;

$PLUGIN_ABSPATH = __DIR__;

require_once __DIR__ . '/utils/cmb2/init.php';

require __DIR__ . '/actions/index.php';
require __DIR__ . '/settings/index.php';
require __DIR__ . '/shortcodes/index.php';
require __DIR__ . '/templates/index.php';
require __DIR__ . '/utils/index.php';
require __DIR__ . '/wc-product/index.php';

function et_enqueue_custom_script()
{
    wp_enqueue_script("jquery-typeahead-script", plugin_dir_url(__FILE__) . "js/typeahead.bundle.js", array("jquery"), false, true);
    wp_enqueue_script("wc-equipment-trader-plugin-select2", plugin_dir_url(__FILE__) . "js/select2.min.js", array("jquery"), false, true);
    wp_enqueue_style("jquery-typeahead-style", "https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.min.css");

    wp_enqueue_script("wc-equipment-trader-plugin-script", plugin_dir_url(__FILE__) . "js/script.min.js", array("jquery", "bootstrap"), false, true);
    wp_enqueue_style("wc-equipment-trader-plugin-select2", plugin_dir_url(__FILE__) . "css/dist/select2.min.css");
    wp_enqueue_style("wc-equipment-trader-plugin-style", plugin_dir_url(__FILE__) . "css/dist/style.min.css");
}
add_action("wp_enqueue_scripts", "et_enqueue_custom_script", 100);


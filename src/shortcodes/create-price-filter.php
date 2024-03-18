<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function create_price_filter()
{
    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/prices-filter.php');

    return ob_get_clean();
}

add_shortcode('et-create-price-filter', 'create_price_filter');
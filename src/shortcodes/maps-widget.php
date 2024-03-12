<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function maps_widget()
{
    ob_start();

    include(plugin_dir_path(__DIR__) . 'includes/maps-widget.php');

    return ob_get_clean();
}

add_shortcode('et-maps-widget', 'maps_widget');
<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function brands_widget()
{
    ob_start();

    include(plugin_dir_path(__DIR__) . 'includes/brands-widget.php');

    return ob_get_clean();
}

add_shortcode('et-brands-widget', 'brands_widget');
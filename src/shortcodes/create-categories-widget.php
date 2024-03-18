<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function create_categories_widget()
{
    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/categories-widget.php');

    return ob_get_clean();
}

add_shortcode('et-create-categories-widget', 'create_categories_widget');
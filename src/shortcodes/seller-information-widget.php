<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function seller_information_widget()
{
    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/seller-information-widget.php');

    return ob_get_clean();
}

add_shortcode('et-seller-information-widget', 'seller_information_widget');
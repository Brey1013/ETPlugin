<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function sales_fine_print()
{
    $finePrint = get_option(SettingsConstants::get_setting_name(SettingsConstants::$sales_fine_print));

    if (isset ($finePrint)) {
        echo "* " . $finePrint;
    }
}

add_shortcode('et-sales-fine-print', 'sales_fine_print');
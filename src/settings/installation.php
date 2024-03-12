<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_filter('plugin_action_links_et_plugin_integration\functions.php', 'et_plugin_integration_settings_link');
function et_plugin_integration_settings_link($links)
{
    $url = esc_url(
        add_query_arg(
            'page',
            'wc-settings',
            'tab',
            'advanced',
            'section',
            'et_integration',
            get_admin_url() . 'admin.php'
        )
    );

    $settings_link = "<a href='$url'>" . __('Settings', 'equipmenttrader') . '</a>';

    array_unshift(
        $links,
        $settings_link
    );

    return $links;
}
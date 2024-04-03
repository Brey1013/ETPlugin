<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function search_form_widget()
{
    $postType = 'listing_ad';

    $listOfCategories = get_categories(
        array(
            'taxonomy' => 'ad_category',
            'orderby' => 'name',
            'show_count' => 0,
            'pad_counts' => 0,
            'hierarchical' => 0,
            'title_li' => '',
            'hide_empty' => 1,
        )
    );

    $categories = build_category_hierarchy($listOfCategories);

    $brands = getAllMetaValues('brand');

    $availability_options = get_option(SettingsConstants::get_setting_name(SettingsConstants::$availability));
    $availability_options = array_map('trim', explode("\n", $availability_options));

    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/search-form.php');

    return ob_get_clean();
}

add_shortcode('et-search-form', 'search_form_widget');
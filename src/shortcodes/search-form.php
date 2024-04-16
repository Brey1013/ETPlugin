<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function search_form_widget($args)
{
    $postType = 'listing_ad';

    $defaults = array(
        'simple-form' => 'false',
    );

    $args = shortcode_atts($defaults, $args);

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
    $models = getAllMetaValues('model');

    sort($brands, SORT_STRING | SORT_FLAG_CASE | SORT_ASC);
    sort($models, SORT_STRING | SORT_FLAG_CASE | SORT_ASC);

    $availability_options = get_option(SettingsConstants::get_setting_name(SettingsConstants::$availability));
    $availability_options = array_map('trim', explode("\n", $availability_options));

    $countries_obj = new WC_Countries();

    $locations = array();

    $states = $countries_obj->get_states();

    foreach ($states["ZA"] as $key => $name) {
        array_push($locations, $name);
    }

    sort($locations, SORT_STRING | SORT_FLAG_CASE | SORT_ASC);

    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/search-form.php');

    return ob_get_clean();
}

add_shortcode('et-search-form', 'search_form_widget');
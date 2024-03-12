<?php

/**
 * All helper functions related to the plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function load_ads_template($template)
{
    global $post;

    if ('listing_ad' === $post->post_type && locate_template(array('single-listing_ad.php')) !== $template) {
        return plugin_dir_path(__FILE__) . '/single-listing_ad.php';
    }

    return $template;
}
add_filter('single_template', 'load_ads_template');

// Custom Post type Archive page
function listing_ad_archive_template($template)
{
    if (is_post_type_archive('listing_ad')) {
        $template = plugin_dir_path(__FILE__) . '/archive-listing_ad.php';
    }
    return $template;
}
add_filter('template_include', 'listing_ad_archive_template');

// Custom Post type Taxonomy page
function ad_category_template($template)
{
    if (is_tax('ad_category')) {
        $template = plugin_dir_path(__FILE__) . '/ad_category_taxonomy.php';
    }
    return $template;
}
add_filter('template_include', 'ad_category_template');
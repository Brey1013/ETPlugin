<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function brands_widget()
{
    $product_id = get_the_ID();
    $post = get_post();

    $brand_array = wp_get_post_terms($product_id, 'brand', array('orderby' => 'term_order'));
    $brand_str = array();

    foreach ($brand_array as $cat) {
        $brand_str[] = $cat->name;
    }

    $model_array = wp_get_post_terms($product_id, 'model', array('orderby' => 'term_order'));
    $model_str = array();

    foreach ($model_array as $cat) {
        $model_str[] = $cat->name;
    }

    $brandName = implode(', ', $brand_str);
    $modelName = implode(', ', $model_str);

    $brandLogo = get_post_meta($product_id, 'brand_logo', true);

    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/brands-widget.php');

    return ob_get_clean();
}

add_shortcode('et-brands-widget', 'brands_widget');
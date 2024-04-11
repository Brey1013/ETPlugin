<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function maps_widget()
{
    $full_address = '';

    $post = get_post();

    $author_id = $post->post_author;

    $billing_address_1 = get_the_author_meta('billing_address_1', $author_id);
    $billing_address_2 = get_the_author_meta('billing_address_2', $author_id);
    $billing_city = get_the_author_meta('billing_city', $author_id);
    $billing_postcode = get_the_author_meta('billing_postcode', $author_id);
    $billing_state = get_the_author_meta('billing_state', $author_id);
    $billing_country = get_the_author_meta('billing_country', $author_id);

    $full_address_array = array_filter(array($billing_address_1, $billing_address_2, $billing_city, $billing_postcode, $billing_state, $billing_country));

    $full_address = join(', ', $full_address_array);

    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/maps-widget.php');

    return ob_get_clean();
}

add_shortcode('et-maps-widget', 'maps_widget');
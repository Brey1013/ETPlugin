<?php

function et_related_products($related_posts, $product_id, $args)
{
    $categoryIds = array();

    $taxonomy = 'ad_category'; //Choose the taxonomy

    foreach (get_terms($taxonomy) as $term) {
        array_push($categoryIds, $term->term_id);
    }

    $related_posts = get_posts(
        array(
            'category' => join(',', $categoryIds),
            'post_type' => 'listing_ad',
            'post_status' => 'publish',
            'fields' => 'ids',
            'posts_per_page' => 4,
            'exclude' => array($product_id),
        )
    );

    return $related_posts;
}
add_filter('woocommerce_related_products', 'et_related_products', 9999, 3);
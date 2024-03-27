<?php

function et_related_products($related_posts, $product_id, $args)
{
    print_r(`<pre><code>et_related_products: $product_id</code></pre>`);

    $categoryIds = array();

    $taxonomy = 'ad_category'; //Choose the taxonomy

    foreach (get_terms($taxonomy) as $term) {
        array_push($categoryIds, $term->term_id);
    }

    $related_posts = get_posts(
        array(
            'post_type' => 'listing_ad',
            'post_status' => 'publish',
            // 'title' => $title,
            'fields' => 'ids',
            'posts_per_page' => 4,
            'exclude' => array($product_id),

            // 'columns' => 4,
            'orderby' => 'rand',

            // 'category' => join(',', $categoryIds),
        )
    );

    print_r(`<pre><code>` . json_encode($related_posts) . `</code></pre>`);

    return $related_posts;
}
add_filter('woocommerce_related_products', 'et_related_products', 10, 3);

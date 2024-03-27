<?php

function et_related_products($related_posts, $product_id, $args)
{
    $postsPerPage = 4;
    $categoryIds = array();
    $postType = 'listing_ad';

    $taxonomy = 'ad_category'; //Choose the taxonomy

    foreach (get_the_terms($product_id, $taxonomy) as $term) {
        array_push($categoryIds, $term->term_id);
    }

    $related_posts = get_posts(
        array(
            'exclude' => array($product_id),
            'fields' => 'ids',
            'post_status' => 'publish',
            'post_type' => $postType,
            'posts_per_page' => $postsPerPage,

            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'terms' => $categoryIds,
                    'include_children' => true
                ],
            ],

            'orderby' => 'rand',
        )
    );

    if (count($related_posts) < $postsPerPage) {
        $related_posts = array_merge(
            $related_posts,
            get_posts(
                array(
                    'exclude' => array_merge($related_posts, array($product_id)),
                    'fields' => 'ids',
                    'post_status' => 'publish',
                    'post_type' => $postType,
                    'posts_per_page' => $postsPerPage - count($related_posts),

                    'orderby' => 'rand',
                )
            )
        );
    }

    return $related_posts;
}

add_action('woocommerce_related_products', 'et_related_products', 9999, 3);

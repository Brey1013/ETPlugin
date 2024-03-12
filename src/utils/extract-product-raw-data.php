<?php

function et_extract_product_raw_data($productId)
{
    global $wpdb;

    $data = array();

    try {
        $wpdb->query("UPDATE {$wpdb->posts}
            SET post_type = 'product'
            WHERE ID = $productId");

        $temp = wc_get_product($productId);

        print_r("<pre><code>" . json_encode($temp->get_data()) . "</code></pre>");

        if (!$temp->exists())
            return $data;

        $temp['images'] = $temp->get_gallery_attachment_ids();

    } finally {
        $wpdb->query("UPDATE {$wpdb->posts}
            SET post_type = 'listing-ad'
            WHERE ID = $productId");
    }

    print_r("<pre><code>" . json_encode($data) . "</code></pre>");

    return $data;
}
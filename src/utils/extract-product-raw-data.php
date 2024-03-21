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
        $temp_post = get_post($productId);
        $tempData = $temp->get_data();

        if (!$temp->exists())
            return $data;

        $prod_images = array();
        $spec_sheet = array();

        $temp_prod_images = get_post_meta($productId, 'prod_images', true);
        $temp_spec_sheet = get_post_meta($productId, 'spec_sheet', true);
        $temp_other_category = get_post_meta($productId, 'other-category', true);
        $temp_other_subcategory = get_post_meta($productId, 'other-subcategory', true);

        if (is_array($temp_prod_images) === 1) {
            foreach ($temp_prod_images as $key => $value) {
                array_push($prod_images, $key);
            }
        }

        if (is_array($temp_spec_sheet) === 1) {
            foreach ($temp_spec_sheet as $key => $value) {
                array_push($spec_sheet, $key);
            }
        }

        $data['images'] = $prod_images;
        $data['title'] = $temp_post->post_title;

        $taxonomy = 'ad_category'; //Choose the taxonomy
        $terms = get_the_terms($productId, $taxonomy); //Get all the terms

        if (is_array($terms) === 1) {
            foreach ($terms as $term) { //Cycle through terms, one at a time
                $parent = $term->parent;

                if ($parent == '0') {
                    $data['category'] = $term->term_id;
                } else {
                    $data['subcategory'] = $term->term_id;
                }
            }
        }

        if (isset ($temp_other_category) && $temp_other_category != '') {
            $data['category'] = 'Other';
            $data['other-category'] = $temp_other_category;
        }

        if (isset ($temp_other_subcategory) && $temp_other_subcategory != '') {
            $data['subcategory'] = 'Other';
            $data['other-subcategory'] = $temp_other_subcategory;
        }

    } finally {
        $wpdb->query("UPDATE {$wpdb->posts}
            SET post_type = 'listing-ad'
            WHERE ID = $productId");
    }

    return $data;
}

function filterProdImages($x)
{
    return $x->key === "prod_images";
}
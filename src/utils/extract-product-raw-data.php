<?php

function et_extract_product_raw_data($productId)
{
    global $wpdb;

    $data = array();

    $post = get_post($productId);

    $spec_sheet = array();

    $all_meta = get_post_meta($productId);

    if (is_array($all_meta))
        foreach ($all_meta as $key => $value) {
            $temp = maybe_unserialize($value[0]);

            $data[$key] = $temp;
        }

    $data["featured"] = $data["featured_ads"];
    $data["images"] = $data["prod_images"];

    $temp_spec_sheet = get_post_meta($productId, 'spec_sheet', true);

    if (is_array($temp_spec_sheet))
        foreach ($temp_spec_sheet as $key => $value) {
            array_push($spec_sheet, $key);
        }

    $data["specsheets"] = $spec_sheet;

    $data['title'] = $post->post_title;
    $data['description'] = $post->post_content;

    $taxonomy = 'ad_category'; //Choose the taxonomy
    $terms = get_the_terms($productId, $taxonomy); //Get all the terms

    if (is_array($terms))
        foreach ($terms as $term) {
            $parent = $term->parent;

            if ($parent == '0') {
                $data['category'] = $term->term_id;
            } else {
                $data['category'] = $term->parent;
                $data['subcategory'] = $term->term_id;
            }
        }

    $temp_other_category = $data['other-category'];

    if (isset($temp_other_category) && $temp_other_category != '') {
        $data['category'] = 'Other';
    }

    $temp_other_subcategory = $data['other-subcategory'];

    if (isset($temp_other_subcategory) && $temp_other_subcategory != '') {
        $data['subcategory'] = 'Other';
    }

    $brandTerms = get_the_terms($productId, "brand");
    $modelTerms = get_the_terms($productId, "model");

    if (is_array($brandTerms))
        foreach ($brandTerms as $term) {
            $parent = $term->parent;

            $data['brand'] = $term->name;
        }

    if (is_array($modelTerms))
        foreach ($modelTerms as $term) {
            $data['model'] = $term->name;
        }

    return $data;
}
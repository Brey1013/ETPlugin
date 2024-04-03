<?php

function getAllMetaValues($taxonomy)
{
    // Get all terms from the taxonomy
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false
    ]);

    $meta_values = [];
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $meta_values[] = $term->name;
        }
    }

    return $meta_values;
}

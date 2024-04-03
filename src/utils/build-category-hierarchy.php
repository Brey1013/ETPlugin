<?php

function build_category_hierarchy($terms)
{
    $categories = array();

    foreach ($terms as $term) {
        if ($term->parent == 0) {
            $category_id = $term->term_id;
            $categories[$category_id]['name'] = $term->name;
        } else {
            $parent_id = $term->parent;
            $categories[$parent_id]['children'][] = array(
                'term_id' => $term->term_id,
                'name' => $term->name,
            );
        }
    }

    return $categories;
}

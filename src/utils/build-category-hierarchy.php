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

    usort($categories, 'sortTerms');

    for ($i = 0; $i < count($categories); $i++) {
        $parent_category = $categories[$i];

        if (isset($parent_category['children']) && count($parent_category['children']) > 0) {
            $children = $parent_category['children'];

            usort($children, 'sortTerms');

            $categories[$i]['children'] = $children;
        }
    }

    return $categories;
}

function sortTerms($a, $b)
{
    $comparison = strcasecmp($a['name'], $b['name']);

    if ($comparison < 0) {
        return -1;
    } else if ($comparison > 0) {
        return 1;
    }

    return 0;
}
<?php
// Register Custom Taxonomies
function register_listing_ads_taxonomies()
{
    // Ad Category Taxonomy
    $category_labels = array(
        'name' => _x('Ad Categories', 'Taxonomy General Name', 'equipmenttrader'),
        'singular_name' => _x('Ad Category', 'Taxonomy Singular Name', 'equipmenttrader'),
        'menu_name' => __('Ad Category', 'equipmenttrader'),
        'all_items' => __('All Ad Categories', 'equipmenttrader'),
        'parent_item' => __('Parent Ad Category', 'equipmenttrader'),
        'parent_item_colon' => __('Parent Ad Category:', 'equipmenttrader'),
        'new_item_name' => __('New Ad Category Name', 'equipmenttrader'),
        'add_new_item' => __('Add New Ad Category', 'equipmenttrader'),
        'edit_item' => __('Edit Ad Category', 'equipmenttrader'),
        'update_item' => __('Update Ad Category', 'equipmenttrader'),
        'view_item' => __('View Ad Category', 'equipmenttrader'),
        'separate_items_with_commas' => __('Separate categories with commas', 'equipmenttrader'),
        'add_or_remove_items' => __('Add or remove categories', 'equipmenttrader'),
        'choose_from_most_used' => __('Choose from the most used', 'equipmenttrader'),
        'popular_items' => __('Popular Categories', 'equipmenttrader'),
        'search_items' => __('Search Categories', 'equipmenttrader'),
        'not_found' => __('Not Found', 'equipmenttrader'),
        'no_terms' => __('No categories', 'equipmenttrader'),
        'items_list' => __('Categories list', 'equipmenttrader'),
        'items_list_navigation' => __('Categories list navigation', 'equipmenttrader'),
    );
    $category_args = array(
        'labels' => $category_labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('ad_category', array('listing_ad'), $category_args);

    // Brand Taxonomy
    $brand_labels = array(
        'name' => _x('Brands', 'Taxonomy General Name', 'equipmenttrader'),
        'singular_name' => _x('Brand', 'Taxonomy Singular Name', 'equipmenttrader'),
        'menu_name' => __('Brands', 'equipmenttrader'),
        // Add other labels as needed
    );
    $brand_args = array(
        'labels' => $brand_labels,
        'hierarchical' => false, // Change to false if Brand is not hierarchical
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('brand', array('listing_ad'), $brand_args);

    // Model Taxonomy
    $model_labels = array(
        'name' => _x('Product Codes/Models', 'Taxonomy General Name', 'equipmenttrader'),
        'singular_name' => _x('Product Code/Model', 'Taxonomy Singular Name', 'equipmenttrader'),
        'menu_name' => __('Product Codes/Models', 'equipmenttrader'),
        // Add other labels as needed
    );
    $model_args = array(
        'labels' => $model_labels,
        'hierarchical' => false, // Change to false if Model is not hierarchical
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('model', array('listing_ad'), $model_args);
}
add_action('init', 'register_listing_ads_taxonomies', 0);

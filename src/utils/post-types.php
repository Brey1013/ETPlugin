<?php

// Register Custom Post Type
function register_listing_ads_post_type()
{
    $labels = array(
        'name' => _x('Listing Ads', 'Post Type General Name', 'equipmenttrader'),
        'singular_name' => _x('Listing Ad', 'Post Type Singular Name', 'equipmenttrader'),
        'menu_name' => __('Listing Ads', 'equipmenttrader'),
        'name_admin_bar' => __('Listing Ad', 'equipmenttrader'),
        'archives' => __('Listing Ad Archives', 'equipmenttrader'),
        'attributes' => __('Listing Ad Attributes', 'equipmenttrader'),
        'parent_item_colon' => __('Parent Listing Ad:', 'equipmenttrader'),
        'all_items' => __('All Listing Ads', 'equipmenttrader'),
        'add_new_item' => __('Add New Listing Ad', 'equipmenttrader'),
        'add_new' => __('Add New', 'equipmenttrader'),
        'new_item' => __('New Listing Ad', 'equipmenttrader'),
        'edit_item' => __('Edit Listing Ad', 'equipmenttrader'),
        'update_item' => __('Update Listing Ad', 'equipmenttrader'),
        'view_item' => __('View Listing Ad', 'equipmenttrader'),
        'view_items' => __('View Listing Ads', 'equipmenttrader'),
        'search_items' => __('Search Listing Ad', 'equipmenttrader'),
        'not_found' => __('Not found', 'equipmenttrader'),
        'not_found_in_trash' => __('Not found in Trash', 'equipmenttrader'),
        'featured_image' => __('Featured Image', 'equipmenttrader'),
        'set_featured_image' => __('Set featured image', 'equipmenttrader'),
        'remove_featured_image' => __('Remove featured image', 'equipmenttrader'),
        'use_featured_image' => __('Use as featured image', 'equipmenttrader'),
        'insert_into_item' => __('Insert into listing ad', 'equipmenttrader'),
        'uploaded_to_this_item' => __('Uploaded to this listing ad', 'equipmenttrader'),
        'items_list' => __('Listing Ads list', 'equipmenttrader'),
        'items_list_navigation' => __('Listing Ads list navigation', 'equipmenttrader'),
        'filter_items_list' => __('Filter listing ads list', 'equipmenttrader'),
    );
    $args = array(
        'label' => __('Listing Ad', 'equipmenttrader'),
        'description' => __('Listing Ads', 'equipmenttrader'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'excerpt', 'author'),
        'taxonomies' => array('ad_category'), // Assign taxonomies
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-cart',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type('listing_ad', $args);
}
add_action('init', 'register_listing_ads_post_type', 0);
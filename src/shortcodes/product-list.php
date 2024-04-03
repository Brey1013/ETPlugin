<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function product_list($args)
{
    $defaults = array(
        'product_ids' => '',
        'hide_filters' => false,
    );

    $args = shortcode_atts($defaults, $args);

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $today = date('Y-m-d');

    global $wpdb;
    $query = "
        SELECT p.ID
        FROM {$wpdb->posts} AS p
            LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id AND pm.meta_key = 'featured_ads'
            LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
        WHERE p.post_type = 'listing_ad'
            AND p.post_status = 'publish'
            AND (pm.meta_key IS NULL OR pm.meta_key = 'featured_ads')
            AND (pm_endate.meta_value >= date(NOW()) AND pm_endate.meta_value IS NOT NULL) " . getShortcodeFilters($args) . "
        ORDER BY
            CASE
                WHEN pm.meta_value IS NOT NULL THEN pm.meta_value
                ELSE '0'
            END DESC,
            p.post_date DESC
    ";

    $query_featured = $wpdb->get_col($query);
    if ($query_featured) {
        $featured_posts_18 = array(); // For featured ads with value 18
        $featured_posts_25 = array(); // For featured ads with value 25
        $normal_posts = array();

        foreach ($query_featured as $featured_id) {
            $featured = get_post_meta($featured_id, 'featured_ads', true);
            $end_listing_date = get_post_meta($featured_id, 'end_listing_date', true);
            $duration = get_post_meta($featured_id, 'duration', true);
            $publish_date = get_the_date('Y-m-d', $featured_id);
            $today = time();

            $featured_option_1_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_duration));
            $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
            $featured_option_2_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_duration));
            $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));

            $ad_arr = array(
                'id' => $featured_id,
                'date' => $publish_date
            );

            if ($featured == $featured_option_1_price) {
                $remaining_days = $duration - $featured_option_1_duration;
                $feature_expiry_date = strtotime('-' . $remaining_days . ' days', strtotime($end_listing_date));
                if ($feature_expiry_date >= $today) {
                    $featured_posts_18[] = $ad_arr;
                } else {
                    $normal_posts[] = $ad_arr;
                }
            } elseif ($featured == $featured_option_2_price) {
                $remaining_days = $duration - $featured_option_2_duration;
                $feature_expiry_date = strtotime('-' . $remaining_days . ' days', strtotime($end_listing_date));
                if ($feature_expiry_date >= $today) {
                    $featured_posts_25[] = $ad_arr;
                } else {
                    $normal_posts[] = $ad_arr;
                }
            } else {
                $normal_posts[] = $ad_arr;
            }
        }

        // Set up pagination
        $total = count($query_featured);
        $posts_per_page = 20;
        $total_pages = ceil($total / $posts_per_page);

        // Merge featured and normal posts
        $all_posts = array_merge($featured_posts_25, $featured_posts_18);

        // Custom comparison function to sort by date
        function sortByDate($a, $b)
        {
            return strtotime($b['date']) - strtotime($a['date']);
        }

        // Sort the array by date
        usort($all_posts, 'sortByDate');
        usort($normal_posts, 'sortByDate');

        $all_posts = array_merge($all_posts, $normal_posts);

        // Create an array of just the IDs
        $idsArray = array_map(function ($item) {
            return $item['id'];
        }, $all_posts);

        $args_all = array(
            'post_type' => 'listing_ad',
            'post__in' => $idsArray,
            'orderby' => 'post__in',
            'paged' => $paged,
        );

        $query_all = new WP_Query($args_all);
    } else {
        $query_all = [];
    }

    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/product-list.php');

    return ob_get_clean();
}

add_shortcode('et-product-list', 'product_list');

function getShortcodeFilters($args)
{
    $result = '';

    if (isset($args['product_ids']) && strlen($args['product_ids']) > 0)
        $result .= ' AND p.ID IN (' . $args['product_ids'] . ') ';

    return $result;
}
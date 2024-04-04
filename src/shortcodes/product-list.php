<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function product_list($args)
{
    global $wpdb;

    $defaults = array(
        'product_ids' => '',
        'hide_filters' => false,
    );

    $args = shortcode_atts($defaults, $args);

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $today = date('Y-m-d');

    $query = "
    SELECT p.ID
    FROM {$wpdb->posts} AS p
        LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id AND pm.meta_key = 'featured_ads'
        LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
        " . getAdditionalJoins($args) . "
    WHERE p.post_type = 'listing_ad'
        AND p.post_status = 'publish'
        AND (pm.meta_key IS NULL OR pm.meta_key = 'featured_ads')
        AND (pm_endate.meta_value >= date(NOW()) AND pm_endate.meta_value IS NOT NULL)
        " . getAdditionalFilters($args) . "
    ORDER BY
        CASE
            WHEN pm.meta_value IS NOT NULL THEN pm.meta_value
            ELSE '0'
        END DESC,
        p.post_date DESC";

    echo "<pre><code>$query</code></pre>";

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


        // Get posts for the current page
        $current_page_posts = array_slice($idsArray, ($paged - 1) * $posts_per_page, $posts_per_page);

        $args_all = array(
            'post_type' => 'listing_ad',
            'post__in' => $current_page_posts,
            'orderby' => 'post__in',
            'paged' => $paged,
            'posts_per_page' => $posts_per_page,
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

function getAdditionalJoins($args)
{
    global $wpdb;

    $term = get_queried_object();

    $result = array();

    $categoryFilter = '';

    if (isset($args['product_ids']) && strlen($args['product_ids']) > 0)
        array_push($result, 'AND p.ID IN (' . $args['product_ids'] . ')');

    if ($term->term_id != null && $term->taxonomy == "ad_category")
        $categoryFilter = $term->term_id;
    else if (isset($_GET['sub_category']) && strlen($_GET['sub_category']) > 0)
        $categoryFilter = $_GET['sub_category'];
    else if (isset($_GET['category']) && strlen($_GET['category']) > 0)
        $categoryFilter = $_GET['category'];

    if (strlen($categoryFilter) > 0) {
        array_push($result, "LEFT JOIN {$wpdb->term_relationships} AS tr_category ON p.ID = tr_category.object_id");
        array_push($result, "LEFT JOIN {$wpdb->term_taxonomy} AS tt_category ON tr_category.term_taxonomy_id = tt_category.term_taxonomy_id AND tt_category.taxonomy = 'ad_category'");
    }

    if (isset($_GET['quality']) && strlen($_GET['quality']) > 0)
        array_push($result, "LEFT JOIN {$wpdb->postmeta} AS pm_quality ON p.ID = pm_quality.post_id AND pm_quality.meta_key = 'quality'");

    if ((isset($_GET['min_price']) && strlen($_GET['min_price']) > 0) || (isset($_GET['max_price']) && strlen($_GET['max_price']) > 0))
        array_push($result, "LEFT JOIN {$wpdb->postmeta} AS pm_price ON p.ID = pm_price.post_id AND pm_price.meta_key = 'price-value'");

    if (isset($_GET['brand']) && strlen($_GET['brand']) > 0) {
        array_push($result, "LEFT JOIN {$wpdb->term_relationships} AS tr_brand ON p.ID = tr_brand.object_id");
        array_push($result, "LEFT JOIN {$wpdb->term_taxonomy} AS tt_brand_lookup ON tr_brand.term_taxonomy_id = tt_brand_lookup.term_taxonomy_id AND tt_brand_lookup.taxonomy = 'brand'");
        array_push($result, "LEFT JOIN {$wpdb->terms} AS tt_brand ON tt_brand_lookup.term_id = tt_brand.term_id");
    }

    if (isset($_GET['product_code']) && strlen($_GET['product_code']) > 0) {
        array_push($result, "LEFT JOIN {$wpdb->term_relationships} AS tr_model ON p.ID = tr_model.object_id");
        array_push($result, "LEFT JOIN {$wpdb->term_taxonomy} AS tt_model_lookup ON tr_model.term_taxonomy_id = tt_model_lookup.term_taxonomy_id AND tt_model_lookup.taxonomy = 'model'");
        array_push($result, "LEFT JOIN {$wpdb->terms} AS tt_model ON tt_model_lookup.term_id = tt_model.term_id");
    }

    if (isset($_GET['availability']) && strlen($_GET['availability']) > 0)
        array_push($result, "LEFT JOIN {$wpdb->postmeta} AS pm_availability ON p.ID = pm_availability.post_id AND pm_availability.meta_key = 'availability'");

    return join("\r\n\t", $result);
}

function getAdditionalFilters($args)
{
    $term = get_queried_object();

    $result = array();
    $categoryFilter = '';

    if (isset($args['product_ids']) && strlen($args['product_ids']) > 0)
        array_push($result, 'AND p.ID IN (' . $args['product_ids'] . ')');

    if ($term->term_id != null && $term->taxonomy == "ad_category")
        $categoryFilter = $term->term_id;
    else if (isset($_GET['sub_category']) && strlen($_GET['sub_category']) > 0)
        $categoryFilter = $_GET['sub_category'];
    else if (isset($_GET['category']) && strlen($_GET['category']) > 0)
        $categoryFilter = $_GET['category'];

    if (strlen($categoryFilter) > 0)
        array_push($result, "AND tt_category.term_id = {$categoryFilter}");

    if (isset($_GET['quality']) && strlen($_GET['quality']) > 0)
        array_push($result, "AND pm_quality.meta_value = '" . $_GET['quality'] . "'");

    if (isset($_GET['min_price']) && strlen($_GET['min_price']) > 0)
        array_push($result, "AND CAST(pm_price.meta_value AS INTEGER) >= " . $_GET['min_price']);

    if (isset($_GET['max_price']) && strlen($_GET['max_price']) > 0)
        array_push($result, "AND CAST(pm_price.meta_value AS INTEGER) <= " . $_GET['max_price']);

    if (isset($_GET['brand']) && strlen($_GET['brand']) > 0)
        array_push($result, "AND tt_brand.name LIKE '%" . $_GET['brand'] . "%'");

    if (isset($_GET['product_code']) && strlen($_GET['product_code']) > 0)
        array_push($result, "AND tt_model.name LIKE '%" . $_GET['product_code'] . "%'");

    if (isset($_GET['availability']) && strlen($_GET['availability']) > 0)
        array_push($result, "AND pm_availability.meta_value = '" . $_GET['availability'] . "'");

    if (isset($_GET['search']) && strlen($_GET['search']) > 0) {
        $general_filter = $_GET['search'];
        array_push($result, "AND (p.post_title LIKE '%$general_filter%' OR p.post_content LIKE '%$general_filter%' OR p.post_excerpt LIKE '%$general_filter%')");
    }

    if (isset($_GET['location']) && strlen($_GET['location']) > 0) {
        $billing_locations = [
            "billing_address_1",
            "billing_address_2",
            "billing_address_3",
            "billing_city",
            "billing_country",
            "billing_postcode",
            "billing_state"
        ];

        array_push(
            $result,
            "AND (SELECT COUNT(*) FROM etr24_usermeta AS um WHERE p.post_author = um.user_id AND um.meta_key IN ('" .
            join("','", $billing_locations) . "') AND um.meta_value like '%" . $_GET['location'] . "%') > 0"
        );
    }

    return join("\r\n\t", $result);
}

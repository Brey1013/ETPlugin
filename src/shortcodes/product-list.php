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
        'only_featured' => false,
        'posts_per_page' => 20,
        'disable_pagination' => false,
        'brand' => '',
        'category' => '',
    );

    $args = shortcode_atts($defaults, $args);

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $today = time();

    $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
    $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));
    $featured_option_1_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_duration));
    $featured_option_2_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_duration));

    $feature_options[1]["price"] = $featured_option_1_price;
    $feature_options[1]["duration"] = $featured_option_1_duration;
    $feature_options[2]["price"] = $featured_option_2_price;
    $feature_options[2]["duration"] = $featured_option_2_duration;

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
        " . getAdditionalFilters($args, $feature_options) . "
    ORDER BY
        CASE
            WHEN pm.meta_value IS NOT NULL THEN pm.meta_value
            ELSE '0'
        END DESC,
        p.post_date DESC";

    $adverts = $wpdb->get_col($query);

    if ($adverts) {
        $featured_option_1 = array();
        $featured_option_2 = array();
        $normal_posts = array();

        foreach ($adverts as $advert_id) {
            $featured = get_post_meta($advert_id, 'featured_ads', true);
            $end_listing_date = get_post_meta($advert_id, 'end_listing_date', true);
            $duration = get_post_meta($advert_id, 'duration', true);
            $publish_date = get_the_date('Y-m-d', $advert_id);

            $ad_arr = array(
                'id' => $advert_id,
                'date' => $publish_date
            );

            if ($featured == $featured_option_1_price) {
                $remaining_days = $duration - $featured_option_1_duration;
                $feature_expiry_date = strtotime('-' . $remaining_days . ' days', strtotime($end_listing_date));
                if ($feature_expiry_date >= $today) {
                    $featured_option_1[] = $ad_arr;
                } else {
                    $normal_posts[] = $ad_arr;
                }
            } elseif ($featured == $featured_option_2_price) {
                $remaining_days = $duration - $featured_option_2_duration;
                $feature_expiry_date = strtotime('-' . $remaining_days . ' days', strtotime($end_listing_date));
                if ($feature_expiry_date >= $today) {
                    $featured_option_2[] = $ad_arr;
                } else {
                    $normal_posts[] = $ad_arr;
                }
            } else {
                $normal_posts[] = $ad_arr;
            }
        }

        // Set up pagination
        $total = count($adverts);
        $posts_per_page = $args['posts_per_page'];
        $total_pages = ceil($total / $posts_per_page);

        // Merge featured and normal posts
        $all_posts = array_merge($featured_option_2, $featured_option_1);

        if ($args['only_featured'] == false)
            $all_posts = array_merge($all_posts, $normal_posts);

        usort($all_posts, 'sortByDate');

        // Create an array of just the IDs
        $idsArray = array_map(function ($item) {
            return $item['id'];
        }, $all_posts);

        $args_all = array(
            'post_type' => 'listing_ad',
            'post__in' => $idsArray,
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
    else if (isset($args['category']) && strlen($args['category']) > 0)
        $categoryFilter = $args['category'];
    else if (isset($_GET['sub_category']) && strlen($_GET['sub_category']) > 0)
        $categoryFilter = $_GET['sub_category'];
    else if (isset($_GET['category']) && strlen($_GET['category']) > 0)
        $categoryFilter = $_GET['category'];

    if (strlen($categoryFilter) > 0) {
        array_push($result, "LEFT JOIN {$wpdb->term_relationships} AS tr_category ON p.ID = tr_category.object_id");
        array_push($result, "LEFT JOIN {$wpdb->term_taxonomy} AS tt_category_lookup ON tr_category.term_taxonomy_id = tt_category_lookup.term_taxonomy_id AND tt_category_lookup.taxonomy = 'ad_category'");
        array_push($result, "LEFT JOIN {$wpdb->terms} AS tt_category ON tt_category_lookup.term_id = tt_category.term_id");
    }

    if (isset($_GET['quality']) && strlen($_GET['quality']) > 0)
        array_push($result, "LEFT JOIN {$wpdb->postmeta} AS pm_quality ON p.ID = pm_quality.post_id AND pm_quality.meta_key = 'quality'");

    if ((isset($_GET['min_price']) && strlen($_GET['min_price']) > 0) || (isset($_GET['max_price']) && strlen($_GET['max_price']) > 0))
        array_push($result, "LEFT JOIN {$wpdb->postmeta} AS pm_price ON p.ID = pm_price.post_id AND pm_price.meta_key = 'price-value'");

    if ((isset($_GET['brand']) && strlen($_GET['brand']) > 0) || (isset($args['brand']) && strlen($args['brand']) > 0)) {
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

    if ($args['only_featured'] == true) {
        array_push($result, "LEFT JOIN {$wpdb->postmeta} AS pm_featured ON p.ID = pm_featured.post_id AND pm_featured.meta_key = 'featured_ads'");
    }

    return join("\r\n\t", $result);
}

function getAdditionalFilters($args, $feature_options)
{
    $term = get_queried_object();

    $result = array();

    $categoryFilter = '';
    $brandFilter = '';

    if (isset($args['product_ids']) && strlen($args['product_ids']) > 0)
        array_push($result, 'AND p.ID IN (' . $args['product_ids'] . ')');

    if ($term->term_id != null && $term->taxonomy == "ad_category")
        $categoryFilter = $term->term_id;
    else if (isset($_GET['sub_category']) && strlen($_GET['sub_category']) > 0)
        $categoryFilter = $_GET['sub_category'];
    else if (isset($_GET['category']) && strlen($_GET['category']) > 0)
        $categoryFilter = $_GET['category'];

    if (strlen($categoryFilter) > 0)
        array_push($result, "AND (tt_category_lookup.term_id = {$categoryFilter} OR tt_category_lookup.parent = {$categoryFilter})");
    else if (isset($args['category']) && strlen($args['category']) > 0)
        array_push($result, "AND tt_category.name LIKE '%" . $args['category'] . "%'");

    if (isset($_GET['quality']) && strlen($_GET['quality']) > 0)
        array_push($result, "AND pm_quality.meta_value = '" . $_GET['quality'] . "'");

    if (isset($_GET['min_price']) && strlen($_GET['min_price']) > 0)
        array_push($result, "AND CAST(pm_price.meta_value AS INTEGER) >= " . $_GET['min_price']);

    if (isset($_GET['max_price']) && strlen($_GET['max_price']) > 0)
        array_push($result, "AND CAST(pm_price.meta_value AS INTEGER) <= " . $_GET['max_price']);

    if (isset($args['brand']) && strlen($args['brand']) > 0)
        $brandFilter = $args['brand'];
    else if (isset($_GET['brand']) && strlen($_GET['brand']) > 0)
        $brandFilter = $_GET['brand'];

    if (strlen($brandFilter) > 0)
        array_push($result, "AND tt_brand.name LIKE '%$brandFilter%'");

    if (isset($_GET['product_code']) && strlen($_GET['product_code']) > 0)
        array_push($result, "AND tt_model.name LIKE '%" . $_GET['product_code'] . "%'");

    if (isset($_GET['availability']) && strlen($_GET['availability']) > 0)
        array_push($result, "AND pm_availability.meta_value = '" . $_GET['availability'] . "'");

    if (isset($_GET['search']) && strlen($_GET['search']) > 0) {
        $general_filter = $_GET['search'];
        array_push($result, "AND (p.post_title LIKE '%$general_filter%' OR p.post_content LIKE '%$general_filter%' OR p.post_excerpt LIKE '%$general_filter%')");
    }

    if (isset($_GET['location']) && strlen($_GET['location']) > 0) {
        $location = strtolower($_GET['location']);

        $billing_locations = [
            "billing_address_1",
            "billing_address_2",
            "billing_address_3",
            "billing_city",
            "billing_country",
            "billing_postcode",
            "billing_state"
        ];

        $location_filter = "AND (SELECT COUNT(*) FROM etr24_usermeta AS um WHERE p.post_author = um.user_id AND um.meta_key IN ('" .
            join("','", $billing_locations) . "') AND (um.meta_value LIKE '%$location%'";

        $countries_obj = new WC_Countries();

        $countries = $countries_obj->get_countries();
        $states = $countries_obj->get_states();

        $country_codes = array();
        $state_codes = array();

        foreach ($countries as $key => $name) {
            if (str_contains(strtolower($name), $location))
                array_push($country_codes, $key);
        }

        foreach ($states as $country_code => $country) {
            foreach ($states[$country_code] as $key => $name) {
                if (str_contains(strtolower($name), $location))
                    array_push($state_codes, $key);
            }
        }

        if (count($country_codes) > 0)
            $location_filter .= " OR um.meta_value IN ('" . join("', '", $country_codes) . "') ";

        if (count($state_codes) > 0)
            $location_filter .= " OR um.meta_value IN ('" . join("', '", $state_codes) . "') ";

        $location_filter .= ") > 0)";

        array_push($result, $location_filter);
    }

    if ($args['only_featured'] == true) {
        $featured_filters = array();

        foreach ($feature_options as $number => $settings) {
            $featured_filters[] = "(pm_featured.meta_value = '" . $settings['price'] . "' AND NOW() <= DATE_ADD(p.post_date, INTERVAL " . $settings["duration"] . " DAY))";
        }

        array_push($result, "AND " . join(" OR ", $featured_filters));
    }

    return join("\r\n\t", $result);
}

// Custom comparison function to sort by date
function sortByDate($a, $b)
{
    return strtotime($b['date']) - strtotime($a['date']);
}

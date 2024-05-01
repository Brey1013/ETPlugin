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
        'order_by' => 'high-to-low'
    );

    $sortOrders = array();

    $sortOrders["high-to-low"] = "Price: High to Low";
    $sortOrders["low-to-high"] = "Price: Low to High";
    $sortOrders["new-to-old"] = "Age: New to Old";
    $sortOrders["old-to-new"] = "Age: Old to New";

    $args = shortcode_atts($defaults, $args);

    $order_by = $_GET["order_by"] ?? $args["order_by"] ?? 'high-to-low';

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

    $joins = getAdditionalJoins($args, $order_by);
    $filters = getAdditionalFilters($args, $feature_options);
    $ordering = getOrderByClause($args, $order_by);

    $query = "SELECT DISTINCT p.ID
    FROM {$wpdb->posts} AS p
        LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id AND pm.meta_key = 'featured_ads'
        LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
        $joins
    WHERE p.post_type = 'listing_ad'
        AND p.post_status = 'publish'
        AND (pm.meta_key IS NULL OR pm.meta_key = 'featured_ads')
        AND (pm_endate.meta_value >= date(NOW()) AND pm_endate.meta_value IS NOT NULL)
        $filters
    ORDER BY $ordering";

    $adverts = $wpdb->get_col($query);

    if ($adverts) {
        // Set up pagination
        $total = count($adverts);
        $posts_per_page = $args['posts_per_page'];
        $total_pages = ceil($total / $posts_per_page);

        $args_all = array(
            'post_type' => 'listing_ad',
            'post__in' => $adverts,
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

function getAdditionalJoins($args, $order_by)
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

    $orderByPriceOptions = ["high-to-low", "low-to-high"];

    $orderByPrice = in_array($order_by, $orderByPriceOptions);

    if ($orderByPrice || (isset($_GET['min_price']) && strlen($_GET['min_price']) > 0) || (isset($_GET['max_price']) && strlen($_GET['max_price']) > 0)) {
        array_push($result, "LEFT JOIN {$wpdb->postmeta} AS pm_price_type ON p.ID = pm_price_type.post_id AND pm_price_type.meta_key = 'priceType'");
        array_push($result, "LEFT JOIN {$wpdb->postmeta} AS pm_price ON p.ID = pm_price.post_id AND pm_price.meta_key = 'price-value'");
    }

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

function getOrderByClause($args, $order_by)
{
    if ($args["only_featured"] == true && $args["disable_pagination"] == true)
        $result = "RAND()";
    else {
        switch ($order_by) {
            default:
            case "high-to-low":
                $result = "CASE WHEN pm_price_type.meta_value = 'POA' THEN 1 ELSE 0 END DESC, CAST(REPLACE(pm_price.meta_value, ' ', '') AS DECIMAL(10,2)) DESC";
                break;
            case "low-to-high":
                $result = "CASE WHEN pm_price_type.meta_value = 'POA' THEN 1 ELSE 0 END ASC, CAST(REPLACE(pm_price.meta_value, ' ', '') AS DECIMAL(10,2)) ASC";
                break;
            case "new-to-old":
                $result = "p.post_date DESC";
                break;
            case "old-to-new":
                $result = "p.post_date ASC";
                break;
        }
    }

    return " $result ";
}
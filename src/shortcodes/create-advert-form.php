<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function create_advert_form($atts)
{
    $product_id = intval(wc_get_product_id_by_sku('master'));

    if (!$product_id) {
        ob_start();

        echo 'No master product found in setup.';

        return ob_get_clean();
    }

    if (is_admin()) {
        return false;
    }

    if (!is_user_logged_in()) {
        ob_start();
        echo 'Please login to continue.<br><br>';
        echo '<a href="' . wc_get_page_permalink('myaccount') . '" class="btn btn-primary">Login</a>';
        return ob_get_clean();

    }

    if (class_exists('WooCommerce')) {

        $discount_tier_1 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_1));
        $discount_tier_2 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_2));
        $discount_tier_3 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3));

        $discount_tier_1_percentage = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_1_discount));
        $discount_tier_2_percentage = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_2_discount));
        $discount_tier_3_percentage = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3_discount));

        $subTotal = 0;
        $discounts = 0;
        $total = 0;

        // Get regular price
        $regular_price = get_post_meta($product_id, '_regular_price', true);

        // Get sale price
        $sale_price = get_post_meta($product_id, '_sale_price', true);

        // If the product is on sale, use the sale price, otherwise use the regular price
        $price = intval($sale_price ? $sale_price : $regular_price);

        $listOfCategories = get_categories(
            array(
                'taxonomy' => 'ad_category',
                'orderby' => 'name',
                'show_count' => 0,
                'pad_counts' => 0,
                'hierarchical' => 1,
                'title_li' => '',
                'hide_empty' => 0,
            )
        );

        $categories = build_category_hierarchy($listOfCategories);

        $current_key = isset($_GET['key']) ? $_GET['key'] : null;

        $cart = WC()->cart->get_cart();
        $count = 0;

        foreach ($cart as $cart_item_key => $cart_item) {
            $cart_product_id = $cart_item['product_id'];

            if (has_term('listing_ad', 'product_type', $cart_product_id)) {
                $count++;

                $featured = $cart_item['featured'] ?? false;
                $featured = intval($featured);

                $subTotal += $price + $featured;
            }
        }

        $discount = 0;

        if ($count >= $discount_tier_3) {
            $discount = $discount_tier_3_percentage;
        } elseif ($count >= $discount_tier_2) {
            $discount = $discount_tier_2_percentage;
        } elseif ($count >= $discount_tier_1) {
            $discount = $discount_tier_1_percentage;
        }

        $discounts = $price * $count * ($discount / 100);
        $total = $subTotal - $discounts;

        if (isset($_GET['draft_id'])) {
            $post = get_post($_GET['draft_id']);
            if ($post) {
                $adData = get_post_meta($_GET['draft_id'], 'cart_items');
                $adData = $adData[0] ?? [];
            }
        } else if (isset($_GET['listing_id'])) {
            $tempId = $_GET['listing_id'];

            $adData = et_extract_product_raw_data($tempId);
        } else {
            $adData = [];
            if (isset($cart[$current_key])) {
                $adData = $cart[$current_key];
            }

        }

        $brands = getAllMetaValues('brand');
        $models = getAllMetaValues('model');

        sort($brands, SORT_STRING | SORT_FLAG_CASE | SORT_ASC);
        sort($models, SORT_STRING | SORT_FLAG_CASE | SORT_ASC);

        $availability_options = get_option(SettingsConstants::get_setting_name(SettingsConstants::$availability));
        $availability_options = array_map('trim', explode("\n", $availability_options));

        $featured_option_1_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_title));
        $featured_option_1_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_duration));
        $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
        $featured_option_2_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_title));
        $featured_option_2_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_duration));
        $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));

        ob_start();

        include (plugin_dir_path(__DIR__) . 'includes/advert-form.php');

        return ob_get_clean();
    }
}
add_shortcode('et-create-advert-form', 'create_advert_form');

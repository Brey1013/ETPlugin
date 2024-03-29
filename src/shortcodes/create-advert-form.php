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

        $listOfCategories = get_categories(
            array(
                'taxonomy' => 'ad_category',
                'orderby' => 'name',
                'show_count' => 0,
                'pad_counts' => 0,
                'hierarchical' => 0,
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
            if (has_term('listing-ad', 'product_type', $cart_product_id)) {
                $count++;
            }
        }

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

            $discount = 0;
            if ($count > 2 && $count < 7) {
                $discount = 5;
            } elseif ($count > 6 && $count < 10) {
                $discount = 10;
            } elseif ($count > 9) {
                $discount = 15;
            }
        }

        // Get regular price
        $regular_price = get_post_meta($product_id, '_regular_price', true);

        // Get sale price
        $sale_price = get_post_meta($product_id, '_sale_price', true);

        // If the product is on sale, use the sale price, otherwise use the regular price
        $price = $sale_price ? $sale_price : $regular_price;

        $brands = getAllMetaValues('brand');
        $models = getAllMetaValues('model');
        $availability_options = get_option(SettingsConstants::get_setting_name(SettingsConstants::$availability));
        $availability_options = explode("\n", $availability_options);

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

function only_parents($category)
{
    return !isset($category->category_parent) || $category->category_parent == 0;
}

function only_children($category)
{
    return $category->category_parent > 0;
}

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

function paginate_array($data)
{
    $keys = array_keys($data);
    $currentKey = isset($_GET['key']) ? $_GET['key'] : '';

    // Get the current key index
    $currentIndex = array_search($currentKey, $keys);
    $totalKeys = count($keys);

    // Display pagination links
    echo '<div class="pagination">';
    // Previous page link
    if ($currentIndex !== false && $currentIndex > 0) {
        echo '<span class="page-item"><a href="?key=' . $keys[$currentIndex - 1] . '" class="page-link">Previous</a></span> ';
    }
    if ($currentIndex === false && $totalKeys > 0) {
        // If no key found in URL, show a link to the last element
        echo '<span class="page-item"><a href="?key=' . end($keys) . '" class="page-link">Previous</a></span>';
    }
    // Page number links
    for ($i = 0; $i < $totalKeys; $i++) {
        $isActive = ($keys[$i] === $currentKey) ? 'active' : '';
        echo '<span class="page-item ' . $isActive . '"><a href="?key=' . $keys[$i] . '" class="page-link">' . ($i + 1) . '</a></span> ';
    }
    // Next page link
    if ($currentIndex !== false && $currentIndex < $totalKeys - 1) {
        echo '<span class="page-item"><a href="?key=' . $keys[$currentIndex + 1] . '" class="page-link">Next</a></span>';
    } elseif ($currentIndex === false && $totalKeys > 0) {
        // do nothing
    } else {
        // Show a custom link if needed
        echo '<span class="page-item"><a href="' . get_permalink() . '" class="page-link">Next</a></span>';
    }
    echo '</div>';
}
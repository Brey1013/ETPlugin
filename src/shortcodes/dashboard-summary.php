<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function et_dashboard_summary()
{
    global $wpdb;
    global $woocommerce;

    $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
    $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));
    $featured_option_1_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_duration));
    $featured_option_2_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_duration));

    $feature_options[1]["price"] = $featured_option_1_price;
    $feature_options[1]["duration"] = $featured_option_1_duration;
    $feature_options[2]["price"] = $featured_option_2_price;
    $feature_options[2]["duration"] = $featured_option_2_duration;

    $inCart = count(WC()->cart->get_cart());
    $published = 0;
    $drafts = 0;
    $featured = 0;
    $expired = 0;
    $current_user_id = get_current_user_id();

    foreach ($feature_options as $number => $settings) {
        $featured_filters[] = "(pm_featured.meta_value = '" . $settings['price'] . "' AND NOW() <= DATE_ADD(p.post_date, INTERVAL " . $settings["duration"] . " DAY))";
    }

    $query = "SELECT
        (SELECT DISTINCT COUNT(*)
        FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS wo_itemmeta ON wo_itemmeta.meta_key = 'listing_ad_id' AND wo_itemmeta.meta_value = p.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_item ON wo_itemmeta.order_item_id = order_item.order_item_id
            LEFT JOIN {$wpdb->posts} AS wc_order ON wc_order.ID = order_item.order_id
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'publish' AND p.post_author = {$current_user_id}
            AND pm_endate.meta_value >= date(NOW()) AND p.post_status <> 'trash' AND wc_order.post_status <> 'trash'
        ) AS published,

        (SELECT DISTINCT COUNT(*)
        FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS wo_itemmeta ON wo_itemmeta.meta_key = 'listing_ad_id' AND wo_itemmeta.meta_value = p.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_item ON wo_itemmeta.order_item_id = order_item.order_item_id
            LEFT JOIN {$wpdb->posts} AS wc_order ON wc_order.ID = order_item.order_id
            LEFT JOIN {$wpdb->postmeta} AS wo_payment_type ON order_item.order_id = wo_payment_type.post_id AND wo_payment_type.meta_key = '_payment_method'
        WHERE p.post_type = 'listing_ad' AND p.post_author = {$current_user_id}
            AND pm_endate.meta_value IS NULL AND p.post_status <> 'trash' AND wc_order.post_status = 'wc-on-hold' AND wo_payment_type.meta_value = 'bacs'
        ) AS pending,

        (SELECT DISTINCT COUNT(*)
        FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS wo_itemmeta ON wo_itemmeta.meta_key = 'listing_ad_id' AND wo_itemmeta.meta_value = p.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_item ON wo_itemmeta.order_item_id = order_item.order_item_id
            LEFT JOIN {$wpdb->posts} AS wc_order ON wc_order.ID = order_item.order_id
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'publish' AND p.post_author = {$current_user_id}
            AND pm_endate.meta_value < date(NOW()) AND p.post_status <> 'trash' AND wc_order.post_status <> 'trash'
        ) AS expired,

        (SELECT DISTINCT COUNT(*)
        FROM {$wpdb->posts} p
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'temp-draft' AND p.post_author = {$current_user_id}
        ) AS draft,

        (SELECT DISTINCT COUNT(*)
        FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} AS pm_featured ON p.ID = pm_featured.post_id AND pm_featured.meta_key = 'featured_ads'
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS wo_itemmeta ON wo_itemmeta.meta_key = 'listing_ad_id' AND wo_itemmeta.meta_value = p.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_item ON wo_itemmeta.order_item_id = order_item.order_item_id
            LEFT JOIN {$wpdb->posts} AS wc_order ON wc_order.ID = order_item.order_id
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'publish' AND p.post_author = {$current_user_id}
            AND (" . join(" OR ", $featured_filters) . ") AND p.post_status <> 'trash' AND wc_order.post_status <> 'trash'
        ) AS featured;";

    $result = $wpdb->get_row($query);

    $published = $result->published;
    $pending = $result->pending;
    $expired = $result->expired;
    $drafts = $result->draft;
    $featured = $result->featured;

    ?>

    <div class="row">
        <div class="col-12">
            <h1 class="et-dashboard-user-info">
                <?php global $current_user;
                wp_get_current_user(); ?>
                <?php
                if (is_user_logged_in()) {
                    echo '<span>Account information for: </span>' . $current_user->user_login . "\n";
                } else {
                    wp_loginout();
                } ?>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h3>Your listings summary</h3>
        </div>
        <div class="et-dashboard-tile drafts col-md-6 col-12"><a href="../my-listings/?status=temp-draft">
                <span class="et-dashboard-tile-number"><?php echo $drafts ?></span>
                <?php _e('Saved in Drafts', 'equipmenttrader'); ?>
            </a></div>
        <div class="et-dashboard-tile cart col-md-6 col-12"><a href="<?php echo $woocommerce->cart->get_cart_url(); ?>">
                <span class="et-dashboard-tile-number"><?php echo $inCart ?></span>
                <?php _e('Saved in Cart', 'equipmenttrader'); ?>
            </a></div>
        <div class="et-dashboard-tile published col-md-6 col-12"><a href="../my-listings/?status=pending">
                <span class="et-dashboard-tile-number"><?php echo $pending ?></span>
                <?php _e('Pending Payment Listings', 'equipmenttrader'); ?>
            </a></div>
        <div class="et-dashboard-tile published col-md-6 col-12"><a href="../my-listings/?status=publish">
                <span class="et-dashboard-tile-number"><?php echo $published ?></span>
                <?php _e('Published Listings', 'equipmenttrader'); ?>
            </a></div>
        <div class="et-dashboard-tile featured col-md-6 col-12"><a href="../my-listings/?status=featured">
                <span class="et-dashboard-tile-number"><?php echo $featured ?></span>
                <?php _e('Featured Listings', 'equipmenttrader'); ?>
            </a></div>
        <div class="et-dashboard-tile expired col-md-6 col-12"><a href="../my-listings/?status=expired">
                <span class="et-dashboard-tile-number"><?php echo $expired ?></span>
                <?php _e('Expired Listings', 'equipmenttrader'); ?>
            </a></div>
    </div>

    <?php
}

add_shortcode('et-dashboard-summary', 'et_dashboard_summary');

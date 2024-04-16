<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function et_dashboard_summary()
{
    global $wpdb;
    global $woocommerce;

    $inCart = count(WC()->cart->get_cart());
    $published = 0;
    $drafts = 0;
    $featured = 0;
    $expired = 0;
    $current_user_id = get_current_user_id();

    $query = "SELECT
        (SELECT COUNT(*)
        FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'publish' AND p.post_author = {$current_user_id}
            AND (pm_endate.meta_value >= date(NOW()))
        ) AS published,

        (SELECT COUNT(*)
        FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'publish' AND p.post_author = {$current_user_id}
            AND (pm_endate.meta_value IS NULL)
        ) AS pending,

        (SELECT COUNT(*)
        FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
            LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id AND pm.meta_key = 'featured_ads'
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'publish' AND p.post_author = {$current_user_id}
            AND (pm_endate.meta_value >= date(NOW()))
            AND (pm.meta_key IS NOT NULL AND pm.meta_value IS NOT NULL)
           ) AS featured,

        (SELECT COUNT(*)
        FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'publish' AND p.post_author = {$current_user_id}
            AND (pm_endate.meta_value < date(NOW()))
        ) AS expired,

        (SELECT COUNT(*)
        FROM {$wpdb->posts} p
        WHERE p.post_type = 'listing_ad' AND p.post_status = 'temp-draft' AND p.post_author = {$current_user_id}
        ) AS draft;";

    $result = $wpdb->get_row($query);

    $drafts = $result->draft;
    $published = $result->published;
    $pending = $result->pending;
    $featured = $result->featured;
    $expired = $result->expired;

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

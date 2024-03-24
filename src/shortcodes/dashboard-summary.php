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

    $featured = $result->featured;

    $expired = $result->expired;



    ?>



    <div class="row">

        <div class="col-12">

            <h3>Your listings summary</h3>

        </div>

        <div class="et-dashboard-tile drafts col-md-6 col-12"><a href="../my-listings/?status=temp-draft">

                <?php echo $drafts ?>

                <?php _e('Saved in Drafts', 'equipmenttrader'); ?>

            </a></div>

        <div class="et-dashboard-tile cart col-md-6 col-12"><a href="<?php echo $woocommerce->cart->get_cart_url(); ?>">

                <?php echo $inCart ?>

                <?php _e('Saved in Cart', 'equipmenttrader'); ?>

            </a></div>

        <div class="et-dashboard-tile published col-md-6 col-12"><a href="../my-listings/?status=publish">

                <?php echo $published ?>

                <?php _e('Published Listings', 'equipmenttrader'); ?>

            </a></div>

        <div class="et-dashboard-tile featured col-md-6 col-12"><a href="../my-listings/?status=featured">

                <?php echo $featured ?>

                <?php _e('Featured Listings', 'equipmenttrader'); ?>

            </a></div>

        <div class="et-dashboard-tile expired col-md-6 col-12"><a href="../my-listings/?status=expired">

                <?php echo $expired ?>

                <?php _e('Expired Listings', 'equipmenttrader'); ?>

            </a></div>

    </div>



    <?php

}



add_shortcode('et-dashboard-summary', 'et_dashboard_summary');


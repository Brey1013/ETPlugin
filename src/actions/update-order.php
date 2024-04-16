<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!function_exists('et_update_order')) {
    function et_update_order(int $order_id, WC_Order $order)
    {
        $today = time();
        $start_date = get_option(SettingsConstants::get_setting_name(SettingsConstants::$start_date));

        $status = $order->get_status();

        switch ($status) {
            default:
                $items = $order->get_items();

                foreach ($items as $item) {
                    $listing_id = $item->get_meta('listing_ad_id', true);

                    delete_metadata('post', $listing_id, 'end_listing_date');

                    $item->delete_meta_data('end_listing_date');
                }

                break;
            case "completed":
                $items = $order->get_items();

                foreach ($items as $item) {
                    $duration = $item->get_meta("duration", true);
                    $listing_id = $item->get_meta('listing_ad_id', true);

                    if (strtotime($start_date) < $today) {
                        $end_listing_date = date('Y-m-d', strtotime('+' . $duration . ' days', $today));
                    } else {
                        $end_listing_date = date('Y-m-d', strtotime('+' . $duration . ' days', strtotime($start_date)));
                    }

                    add_metadata('post', $listing_id, 'end_listing_date', $end_listing_date, true);

                    $item->add_meta_data('end_listing_date', $end_listing_date, true);
                }

                break;
        }
    }
}
add_action('woocommerce_update_order', 'et_update_order', 100, 2);
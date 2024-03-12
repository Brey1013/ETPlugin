<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('woocommerce_update_order', 'et_update_order', 100, 2);

if (!function_exists('et_update_order')) {
    function et_update_order(int $order_id, WC_Order $order)
    {
        $status = $order->get_status();

        switch ($status) {
            default:
            case "refunded":
            case "cancelled":
            case "failed":
                break;
            case "completed":
                break;
        }
    }
}
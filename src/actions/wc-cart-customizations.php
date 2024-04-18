<?php

require $PLUGIN_ABSPATH . '/constants/index.php';

function et_empty_cart_redirect_url()
{
    $link = get_option(SettingsConstants::get_setting_name(SettingsConstants::$relative_listing_path));

    return $link;
}
add_filter('woocommerce_return_to_shop_redirect', 'et_empty_cart_redirect_url');

function et_woocommerce_return_to_shop_text($translated_text, $text, $domain)
{
    switch ($translated_text) {
        case 'Return to shop':

            $translated_text = __('Add a new listing', 'woocommerce');

            break;

    }

    return $translated_text;
}
add_filter('gettext', 'et_woocommerce_return_to_shop_text', 20, 3);

function et_cart_item_removed_title($product, $cart_item)
{
    return '"' . $cart_item["title"] . '"';
}
add_filter('woocommerce_cart_item_removed_title', 'et_cart_item_removed_title', 20, 2);

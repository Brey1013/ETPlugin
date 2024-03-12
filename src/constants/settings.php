<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class SettingsConstants
{
    public static string $availability = "availability";
    public static string $availability_title = "Availability Settings";
    public static string $discount_tier_title = "Discount Tiers Options";
    public static string $featured_option_1_duration = "featured_option_1_duration";
    public static string $featured_option_1_price = "featured_option_1_price";
    public static string $featured_option_1_title = "featured_option_1_title";
    public static string $featured_option_2_duration = "featured_option_2_duration";
    public static string $featured_option_2_price = "featured_option_2_price";
    public static string $featured_option_2_title = "featured_option_2_title";
    public static string $featured_title = "Featured Options";
    public static string $metadata_advert_type = "advert";
    public static string $metadata_end_date = "metadata_end_date";
    public static string $metadata_start_date = "metadata_start_date";
    public static string $metadata_type = "product_sub_type";
    public static string $plugin_menu_title = 'Equipment Trader';
    public static string $plugin_prefix = 'et_plugin';
    public static string $plugin_section_title = 'Equipment Trader Settings';
    public static string $relative_listing_path = "relative_listing_path";
    public static string $sales_fine_print = "sales_fine_print";
    public static string $start_date = "start_date";
    public static string $tier_1 = "tier_1";
    public static string $tier_1_days = "tier_1_days";
    public static string $tier_1_discount = "tier_1_discount";
    public static string $tier_1_title = "tier_1_title";
    public static string $tier_2 = "tier_2";
    public static string $tier_2_days = "tier_2_days";
    public static string $tier_2_discount = "tier_2_discount";
    public static string $tier_2_title = "tier_2_title";
    public static string $tier_3 = "tier_3";
    public static string $tier_3_days = "tier_3_days";
    public static string $tier_3_discount = "tier_3_discount";
    public static string $tier_3_title = "tier_3_title";

    public static function get_setting_name(string $setting)
    {
        return self::$plugin_prefix . '_' . $setting;
    }
}
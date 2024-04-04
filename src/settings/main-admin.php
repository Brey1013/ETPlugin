<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

/**
 * Create the section beneath the integration tab
 **/
add_filter('woocommerce_get_sections_advanced', 'et_integration_add_section');
function et_integration_add_section($sections)
{
    $sections[SettingsConstants::$plugin_prefix] = __(SettingsConstants::$plugin_menu_title, 'equipmenttrader');

    return $sections;
}

/**
 * Add settings to the specific section
 */
add_filter('woocommerce_get_settings_advanced', 'et_integration_all_settings', 10, 2);

function et_integration_all_settings($settings, $current_section)
{
    if ($current_section == SettingsConstants::$plugin_prefix) {
        $settings_slider = array();

        // Add Title to the Settings
        $settings_slider[] = array(
            'name' => __(SettingsConstants::$plugin_section_title, 'equipmenttrader'),
            'type' => 'title',
            'desc' => __("The following settings are used by Equipment Trader's plugin", 'equipmenttrader'),
            'id' => SettingsConstants::$plugin_prefix
        );

        $settings_slider[] = array(
            'name' => __('Start Date', 'equipmenttrader'),
            'type' => 'date',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$start_date),
            'desc' => __('This setting will dictate the earliest date all dates will be calculated from', 'equipmenttrader'),
        );

        $settings_slider[] = array(
            'name' => __('Relative Create Listing Path', 'equipmenttrader'),
            'type' => 'text',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$relative_listing_path),
            'desc' => __('This is the relative path to the page where listings are created/edited. e.g. /create-listing', 'equipmenttrader'),
        );

        $settings_slider[] = array(
            'name' => __('Sales Fine Print', 'equipmenttrader'),
            'type' => 'text',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$sales_fine_print),
            'desc' => __('This detail will show at the bottom of the add/edit advert screen', 'equipmenttrader'),
        );

        $settings_slider[] = array(
            'type' => 'sectionend',
            'id' => SettingsConstants::$plugin_prefix
        );

        $settings_slider[] = array(
            'name' => __(SettingsConstants::$discount_tier_title, 'equipmenttrader'),
            'type' => 'title',
            'desc' => __('Maximum adverts you can add in tier in 10', 'equipmenttrader'),
            'id' => SettingsConstants::$plugin_prefix
        );

        $settings_slider[] = array(
            'name' => __('Tier 1', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_1),
            'desc' => __('Please enter adverts to reach tier 1', 'equipmenttrader'),
            'default' => '3',
        );

        $settings_slider[] = array(
            'name' => __('Tier 1 Title', 'equipmenttrader'),
            'type' => 'text',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_1_title),
            'desc' => __('Please enter title for tier 1', 'equipmenttrader'),
            'default' => '5% Discount',
        );

        $settings_slider[] = array(
            'name' => __('Tier 1 Discount', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_1_discount),
            'desc' => __('Enter the discount for Tier 1 (percentage)', 'equipmenttrader'),
            'default' => '5',
        );

        $settings_slider[] = array(
            'name' => __('Tier 1 Discount as additional days', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_1_days),
            'desc' => __('Enter the duration for Tier 1 discount (in days). Leave blank if days will be added after reaching thie tier.', 'equipmenttrader'),
            'default' => '0',
        );

        $settings_slider[] = array(
            'name' => __('Tier 2', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_2),
            'desc' => __('Please enter adverts to reach tier 2', 'equipmenttrader'),
            'default' => '7',
        );

        $settings_slider[] = array(
            'name' => __('Tier 2 Title', 'equipmenttrader'),
            'type' => 'text',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_2_title),
            'desc' => __('Please enter title for tier 2', 'equipmenttrader'),
            'default' => '10% Discount',
        );

        $settings_slider[] = array(
            'name' => __('Tier 2 Discount', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_2_discount),
            'desc' => __('Enter the discount for Tier 2 (percentage)', 'equipmenttrader'),
            'default' => '10',
        );

        $settings_slider[] = array(
            'name' => __('Tier 2 Discount as additional days', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_2_days),
            'desc' => __('Enter the duration for Tier 2 discount (in days). Leave blank if days will be added after reaching thie tier.', 'equipmenttrader'),
            'default' => '0',
        );

        $settings_slider[] = array(
            'name' => __('Tier 3', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_3),
            'desc' => __('Please enter adverts to reach tier 3.', 'equipmenttrader'),
            'default' => '10',
        );

        $settings_slider[] = array(
            'name' => __('Tier 3 Title', 'equipmenttrader'),
            'type' => 'text',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_3_title),
            'desc' => __('Please enter title for tier 3', 'equipmenttrader'),
            'default' => '15% Discount + 1 Free Month',
        );

        $settings_slider[] = array(
            'name' => __('Tier 3 Discount', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_3_discount),
            'desc' => __('Enter the discount for Tier 3 (percentage)', 'equipmenttrader'),
            'default' => '15',
        );

        $settings_slider[] = array(
            'name' => __('Tier 3 Discount as additional days', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$tier_3_days),
            'desc' => __('Enter the duration for Tier 3 discount (in days). Leave blank if days will be added after reaching thie tier.', 'equipmenttrader'),
            'default' => '30',
        );

        $settings_slider[] = array(
            'type' => 'sectionend',
            'id' => SettingsConstants::$plugin_prefix
        );

        $settings_slider[] = array(
            'name' => __(SettingsConstants::$availability_title, 'equipmenttrader'),
            'type' => 'title',
            'desc' => '',
            'id' => SettingsConstants::$plugin_prefix
        );

        $settings_slider[] = array(
            'name' => __('Add options for availability dropdown', 'equipmenttrader'),
            'type' => 'textarea',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$availability),
            'desc' => __('Add each option in new line.', 'equipmenttrader'),
            'default' => '30',
        );

        $settings_slider[] = array(
            'type' => 'sectionend',
            'id' => SettingsConstants::$plugin_prefix
        );

        $settings_slider[] = array(
            'name' => __(SettingsConstants::$featured_title, 'equipmenttrader'),
            'type' => 'title',
            'desc' => __('Setup options to make advert featured', 'equipmenttrader'),
            'id' => SettingsConstants::$plugin_prefix
        );

        $settings_slider[] = array(
            'name' => __('Option 1 Label', 'equipmenttrader'),
            'type' => 'text',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_title),
            'desc' => __('Please enter title', 'equipmenttrader'),
            'default' => 'Feature this ad for 14 days for an additional R18 inc VAT',
        );

        $settings_slider[] = array(
            'name' => __('Option 1 Duration', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_duration),
            'desc' => __('Duration of making it featured', 'equipmenttrader'),
            'default' => '14',
        );

        $settings_slider[] = array(
            'name' => __('Option 1 Price', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price),
            'desc' => __('Price for option 1', 'equipmenttrader'),
            'default' => '18',
        );

        $settings_slider[] = array(
            'name' => __('Option 2 Label', 'equipmenttrader'),
            'type' => 'text',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_title),
            'desc' => __('Please enter title', 'equipmenttrader'),
            'default' => 'Feature this ad for 30 days for an additional R25 inc VAT',
        );

        $settings_slider[] = array(
            'name' => __('Option 2 Duration', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_duration),
            'desc' => __('Duration of making it featured', 'equipmenttrader'),
            'default' => '30',
        );

        $settings_slider[] = array(
            'name' => __('Option 2 Price', 'equipmenttrader'),
            'type' => 'number',
            'id' => SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price),
            'desc' => __('Price for option 2', 'equipmenttrader'),
            'default' => '25',
        );

        $settings_slider[] = array(
            'type' => 'sectionend',
            'id' => SettingsConstants::$plugin_prefix
        );

        return $settings_slider;
    } else {
        return $settings;
    }
}

/**
 * Define the metabox and field configurations.
 */
function cmb2_sample_metaboxes()
{
    /**
     * Initiate the metabox
     */
    $cmb = new_cmb2_box(
        array(
            'id' => 'product_info',
            'title' => __('Product Information', 'equipmenttrader'),
            'object_types' => array('listing_ad', ), // Post type
            'context' => 'normal',
            'priority' => 'high',
            'show_names' => true, // Show field names on the left
            // 'cmb_styles' => false, // false to disable the CMB stylesheet
            // 'closed'     => true, // Keep the metabox closed by default
        )
    );

    // Price text field
    $cmb->add_field(
        array(
            'name' => __('Price', 'equipmenttrader'),
            'desc' => __('Add Product Price', 'equipmenttrader'),
            'id' => 'price-value',
            'type' => 'text',
        )
    );

    // Price Type text field
    $cmb->add_field(
        array(
            'name' => 'Price Type',
            'desc' => 'Select an option',
            'id' => 'priceType',
            'type' => 'select',
            'show_option_none' => true,
            'default' => 'Entered Price',
            'options' => array(
                'Entered Price' => __('Entered Price', 'equipmenttrader'),
                'POA' => __('POA', 'equipmenttrader')
            ),
        )
    );

    $cmb->add_field(
        array(
            'name' => 'Quality',
            'desc' => 'Select an option',
            'id' => 'quality',
            'type' => 'select',
            'show_option_none' => true,
            'default' => 'New',
            'options' => array(
                'New' => __('New', 'equipmenttrader'),
                'Used' => __('Used', 'equipmenttrader'),
                'Refurbished' => __('Refurbished', 'equipmenttrader'),
            ),
        )
    );

    // Brand Logo file field
    $cmb->add_field(
        array(
            'name' => 'Brand Logo',
            'desc' => 'Upload an image or enter an URL.',
            'id' => 'brand_logo',
            'type' => 'file',
            // Optional:
            'options' => array(
                'url' => false, // Hide the text input for the url
            ),
            'text' => array(
                'add_upload_file_text' => 'Add File' // Change upload button text. Default: "Add or Upload File"
            ),
            'preview_size' => 'large', // Image size to use when previewing in the admin.
        )
    );

    // Spec Sheet File Field
    $cmb->add_field(
        array(
            'name' => 'Product Spec Sheet',
            'desc' => 'Upload an Spec Sheet',
            'id' => 'spec_sheet',
            'type' => 'file_list',
            'preview_size' => 'small', // Image size to use when previewing in the admin.
        )
    );

    // Images File Field
    $cmb->add_field(
        array(
            'name' => 'Product Images',
            'desc' => 'Upload an Images',
            'id' => 'prod_images',
            'type' => 'file_list',
            'preview_size' => 'small', // Image size to use when previewing in the admin.
        )
    );

    // Availability Drop Down Field
    $availability_options = get_option(SettingsConstants::get_setting_name(SettingsConstants::$availability));
    $availability_options = array_map('trim', explode("\n", $availability_options));
    $multi_array = [];
    foreach ($availability_options as $value) {
        $multi_array[trim($value)] = trim($value);
    }
    $cmb->add_field(
        array(
            'name' => 'Availability',
            'desc' => 'Select an option',
            'id' => 'availability',
            'type' => 'select',
            'show_option_none' => true,
            'default' => 'ex stock',
            'options' => $multi_array,
        )
    );

    // Availability Drop Down Field

    $featured_option_1_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_title));
    $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
    $featured_option_2_title = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_title));
    $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));

    $cmb->add_field(
        array(
            'name' => 'Featured Ads',
            'id' => 'featured_ads',
            'type' => 'radio',
            'show_option_none' => true,
            'options' => array(
                $featured_option_1_price => $featured_option_1_title,
                $featured_option_2_price => $featured_option_2_title
            ),
        )
    );

    // Other category text field
    $cmb->add_field(
        array(
            'name' => __('Other Category', 'equipmenttrader'),
            'desc' => __('Add Other Category', 'equipmenttrader'),
            'id' => 'other-category',
            'type' => 'text',
        )
    );

    // Other sub category text field
    $cmb->add_field(
        array(
            'name' => __('Other Sub-Category', 'equipmenttrader'),
            'desc' => __('Add Other Sub-Category', 'equipmenttrader'),
            'id' => 'other-subcategory',
            'type' => 'text',
        )
    );

    // Duration text field
    $cmb->add_field(
        array(
            'name' => __('Duration', 'equipmenttrader'),
            'desc' => __('Duration of ad listing', 'equipmenttrader'),
            'id' => 'duration',
            'type' => 'text',
        )
    );

    // Expiry date text field
    $cmb->add_field(
        array(
            'name' => __('Expiry Date', 'equipmenttrader'),
            'desc' => __('Expiry date of ad listing. Example format: yyyy-mm-dd', 'equipmenttrader'),
            'id' => 'end_listing_date',
            'type' => 'text',
        )
    );
}

add_action('cmb2_admin_init', 'cmb2_sample_metaboxes');
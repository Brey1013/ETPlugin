<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

include_once (ABSPATH . 'wp-admin/includes/image.php');

function handle_advert_form_submission()
{
    $redirect_location = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['submit']) || isset($_POST['update']) || isset($_POST['go-to-cart']) || isset($_POST['submit-draft']))) {

        global $woocommerce;

        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_text_field($_POST['description']);
        $category = sanitize_text_field($_POST['category']);
        $otherCategory = $category === "Other" ? sanitize_text_field($_POST['other-category']) : null;
        $subcategory = sanitize_text_field($_POST['sub-category']);
        $otherSubcategory = $subcategory === "Other" ? sanitize_text_field($_POST['other-subcategory']) : null;
        $brand = sanitize_text_field($_POST['brand']);
        $model = sanitize_text_field($_POST['model']);
        $quality = sanitize_text_field($_POST['quality']);
        $priceType = sanitize_text_field($_POST['price-type']);
        $priceValue = sanitize_text_field($_POST['price-value']);
        $availability = sanitize_text_field($_POST['availability']);
        $featured = isset($_POST['featured']) ? $_POST['featured'] : false;

        $itemData = array(
            'title' => $title, // Add your custom meta data here
            'description' => $description,
            'category' => $category,
            'other-category' => $otherCategory,
            'subcategory' => $subcategory,
            'other-subcategory' => $otherSubcategory,
            'brand' => $brand,
            'model' => $model,
            'quality' => $quality,
            'price-type' => $priceType,
            'price' => $priceValue,
            'price-value' => $priceValue,
            'availability' => $availability,
            'featured' => $featured,
            "images" => array(),
            "specsheets" => array(),
            "brand_logo" => null
        );

        $original_images = array();
        $original_specsheets = array();
        $original_brand_logo = '';

        if (isset($_GET['listing_id'])) {
            $original_images = $_POST['original-product-image'];
            $original_specsheets = $_POST['original-specsheets'];
            $original_brand_logo = $_POST['original-brand-logo-image'];

            $itemData['images'] = copy_attachments('images', $original_images);
            $itemData['specsheets'] = copy_attachments('specsheets', $original_specsheets);
            $itemData['brand_logo'] = copy_attachments('brand_logo', [$original_brand_logo]);

            if (is_array($itemData['brand_logo']) && count($itemData['brand_logo']) > 0)
                $itemData['brand_logo'] = $itemData['brand_logo'][0];
        }

        if (isset($_GET['draft_id'])) {
            $post = get_post($_GET['draft_id']);
            $adData = get_post_meta($_GET['draft_id'], 'cart_items');
            $adData = $adData[0] ?? [];
        }

        if (isset($_POST['submit']) || isset($_POST['update']) || isset($_POST['go-to-cart'])) {
            $product_id = wc_get_product_id_by_sku('master'); // Get the product ID
            $quantity = 1; // Set the quantity to 1

            // Handle media uploads
            if (isset($_GET['draft_id'])) {
                $images = $adData['images'];
                $specsheets = $adData['specsheets'];
                $brand_logo = $adData['brand_logo'];
            } elseif ($_GET['key']) {
                $images = $woocommerce->cart->cart_contents[$_GET['key']]['images'];
                $specsheets = $woocommerce->cart->cart_contents[$_GET['key']]['specsheets'];
                $brand_logo = $woocommerce->cart->cart_contents[$_GET['key']]['brand_logo'];
            }

            $itemData['images'] = array_merge($itemData['images'], upload_base64_attachments('images', $images ?? []));
            $itemData['specsheets'] = array_merge($itemData['specsheets'], upload_base64_attachments('specsheets', $specsheets ?? []));
            $itemData['brand_logo'] = upload_file('brand_logo', $brand_logo ?? $itemData['brand_logo']);

            if (isset($_POST['update'])) {
                foreach ($itemData as $metaKey => $metaValue) {
                    $woocommerce->cart->cart_contents[$_GET['key']][$metaKey] = $metaValue;
                }
                // Save the changes to the cart
                $woocommerce->cart->set_session();
            } elseif (isset($_POST['submit'])) {
                if (isset($_GET['draft_id'])) {
                    wp_delete_post($_GET['draft_id'], true);
                }

                $cart_item_key = $woocommerce->cart->add_to_cart($product_id, $quantity, 0, array(), $itemData);
            } elseif (isset($_POST['go-to-cart'])) {
                if (isset($_GET['draft_id'])) {
                    wp_delete_post($_GET['draft_id'], true);
                }
                if (isset($_GET['key']) && array_key_exists($_GET['key'], $woocommerce->cart->cart_contents)) {
                    $woocommerce->cart->remove_cart_item($_GET['key']);
                }
                $cart_item_key = $woocommerce->cart->add_to_cart($product_id, $quantity, 0, array(), $itemData);

                $redirect_location = $woocommerce->cart->get_cart_url();
            }
        } elseif (isset($_POST['submit-draft'])) {

            if (isset($_GET['draft_id'])) {
                // Handle media uploads
                $itemData['images'] = array_merge($itemData['images'], upload_base64_attachments('images', $adData['images'] ?? []));
                $itemData['specsheets'] = array_merge($itemData['specsheets'], upload_base64_attachments('specsheets', $adData['specsheets'] ?? []));
                $itemData['brand_logo'] = upload_file('brand_logo', $adData['brand_logo'] ?? false);

                update_post_meta($post->ID, 'cart_items', $itemData);
            } else {
                // Handle media uploads
                $itemData['images'] = array_merge($itemData['images'] ?? [], upload_base64_attachments('images', []));
                $itemData['specsheets'] = array_merge($itemData['specsheets'] ?? [], upload_base64_attachments('specsheets', []));
                $itemData['brand_logo'] = upload_file('brand_logo', $itemData['brand_logo'] ?? false);

                $post_arr = array(
                    'post_type' => 'listing_ad',
                    'post_title' => uniqid(),
                    'post_status' => 'temp-draft',
                    'post_author' => get_current_user_id(),
                    'meta_input' => array(
                        'cart_items' => $itemData
                    ),
                );

                $post_id = wp_insert_post($post_arr);
            }
        }

        if ($redirect_location === '' && str_contains($_SERVER['HTTP_REFERER'], "?")) {
            $redirect_location = $_SERVER['HTTP_REFERER'];
        }
    }

    if ($redirect_location !== '') {
        $redirect_location = explode("?", $redirect_location)[0];

        header('Location:' . $redirect_location);
    }
}
add_action('template_redirect', 'handle_advert_form_submission');

function copy_attachments($key, $attachments = [])
{
    $attachment_ids = array();

    foreach ($attachments as $image_path) {
        if (!$image_path)
            continue;

        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_path);
        $filename = basename($image_path);

        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => 'image/jpeg',
            'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $image_path);
        $attachment_ids[] = $attach_id;

        // Generate attachment metadata and update the attachment
        $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
    }

    return $attachment_ids;
}

function upload_base64_attachments($key, $attachments = [])
{
    global $woocommerce;
    if (isset($_POST[$key])) {
        if (isset($_POST['update']) && isset($woocommerce->cart->cart_contents[$_GET['key']][$key])) {
            $images = $woocommerce->cart->cart_contents[$_GET['key']][$key];
            foreach ($images as $imgId) {
                $result = wp_delete_attachment($imgId, true); // Set the second parameter to true to permanently delete the attachment
            }
        }
        $attachments[] = handle_raw_file_uploads($_POST[$key]);
    }

    return $attachments;
}

function upload_file($key, $file = false)
{
    $friendly_name = str_ireplace("_", "-", $key);

    global $woocommerce;
    if (isset($_FILES[$friendly_name]) && $_FILES[$friendly_name]['name']) {
        if (isset($_POST['update']) && isset($woocommerce->cart->cart_contents[$_GET['key']][$key])) {
            $brand_logo = $woocommerce->cart->cart_contents[$_GET['key']][$key];
            wp_delete_attachment($brand_logo, true); // Set the second parameter to true to permanently delete the attachment
        }
        $file = handle_single_file_upload($_FILES[$friendly_name]);
    }

    return $file;
}

function build_cart_items_meta_array()
{
    global $woocommerce;
    $prev_items = [];
    foreach ($woocommerce->cart->cart_contents as $cart) {
        $itemData = array(
            'title' => $cart['title'], // Add your custom meta data here
            'description' => $cart['description'],
            'category' => $cart['category'],
            'other-category' => $cart['other-category'],
            'subcategory' => $cart['subcategory'],
            'other-subcategory' => $cart['other-subcategory'],
            'brand' => $cart['brand'],
            'model' => $cart['model'],
            'quality' => $cart['quality'],
            'price-type' => $cart['price-type'],
            'price-value' => $cart['price-value'],
            'availability' => $cart['availability'],
            'featured' => $cart['featured'],
            'images' => $cart['images'],
            'brand_logo' => $cart['brand_logo'],
            'specsheets' => $cart['specsheets']
        );
        $prev_items[] = $itemData;
    }
    return $prev_items;
}

// Handle file uploads and insert as attachments
function handle_raw_file_uploads($uploaded_files)
{
    $attached_files = array();

    foreach ($uploaded_files as $base64Data) {
        // Decode the base64-encoded data
        $fileData = explode(',', $base64Data);
        $fileContent = base64_decode($fileData[1]);

        // Determine the MIME type of the file
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->buffer($fileContent);

        // Get file extension from MIME type
        $mime_parts = explode('/', $mime_type);
        $fileExtension = end($mime_parts);

        // Generate a unique filename with appropriate file extension
        $fileName = uniqid() . '.' . $fileExtension;

        // Save the file to the WordPress uploads directory
        $uploadDir = wp_upload_dir();
        $uploadPath = $uploadDir['path'] . '/' . $fileName;

        file_put_contents($uploadPath, $fileContent);

        // Attach the file to the WordPress media library
        $attachment = array(
            'post_title' => sanitize_file_name($fileName),
            'post_content' => '',
            'post_status' => 'inherit',
            'post_mime_type' => $mime_type // Adjust the MIME type based on the actual image format
        );
        $attached_files[] = wp_insert_attachment($attachment, $uploadPath);
    }

    return $attached_files;
}

// Handle file uploads and insert as attachments
function handle_single_file_upload($file)
{
    $uploaded_files = array();
    $file_type = $file['type'];

    // Handle the file upload
    $upload_overrides = array('test_form' => false);
    $movefile = wp_handle_upload($file, $upload_overrides);

    if ($movefile && !isset($movefile['error'])) {
        // File uploaded successfully, insert as attachment
        $attachment = array(
            'post_mime_type' => $file_type,
            'post_title' => sanitize_file_name($file_name),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $movefile['file']);

        if (!is_wp_error($attach_id)) {
            return $attach_id;
        }
    }

    return false;
}

// Apply discounts
function apply_discount_based_on_quantity($cart)
{
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    $cart_items = $cart->get_cart();

    $quantity = 0;
    foreach ($cart_items as $cart_item_key => $cart_item) {
        $cart_product_id = $cart_item['product_id'];
        if (has_term('listing_ad', 'product_type', $cart_product_id)) {
            $quantity++;
        }
    }

    $tier_1 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_1));
    $tier_1_discount = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_1_discount));
    $tier_2 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_2));
    $tier_2_discount = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_2_discount));
    $tier_3 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3));
    $tier_3_discount = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3_discount));

    // Iterate through cart items
    foreach ($cart_items as $cart_item_key => $cart_item) {
        // Get product ID and quantity
        $product_id = $cart_item['product_id'];
        if (has_term('listing_ad', 'product_type', $product_id)) {
            $featured = $cart_item['featured'] ?? false;
            $featured = intval($featured);

            $discount = 0;

            // Check if quantity exceeds threshold
            if ($quantity >= $tier_3) {
                // Calculate discount
                $discount = $cart_item['data']->get_price() * $tier_3_discount / 100;
            } elseif ($quantity >= $tier_2) {
                // Calculate discount
                $discount = $cart_item['data']->get_price() * $tier_2_discount / 100;
            } elseif ($quantity >= $tier_1) {
                // Calculate discount
                $discount = $cart_item['data']->get_price() * $tier_1_discount / 100;
            }

            $cart_item['data']->set_price($cart_item['data']->get_price() - $discount + $featured);
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'apply_discount_based_on_quantity');

// Modify product title in cart for specific product type
function custom_cart_item_name($product_name, $cart_item, $cart_item_key)
{
    // Get the product ID
    $product_id = $cart_item['product_id'];
    $cart = WC()->cart->get_cart();

    $quantity = 0;
    foreach ($cart as $cart_item_key => $item) {
        $cart_product_id = $item['product_id'];
        if (has_term('listing_ad', 'product_type', $cart_product_id)) {
            $quantity++;
        }
    }

    // Check if the product is of a specific product type (e.g., 'book')
    if (get_post_type($product_id) === 'product' && has_term('listing_ad', 'product_type', $product_id)) {
        // Get the meta value stored in the cart for this product
        $meta_value = isset($cart_item['title']) ? $cart_item['title'] : '';
        $featured = isset($cart_item['featured']) ? $cart_item['featured'] : '';

        // If the meta value exists, use it as the product title
        if (!empty($meta_value)) {
            $product_name = $meta_value;
        }

        $featured_option_1_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_duration));
        $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
        $featured_option_2_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_duration));
        $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));

        $tier_3 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3));
        $tier_3_days = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3_days));

        $duration = get_post_meta($product_id, '_ad_duration', true);
        if ($quantity >= $tier_3) {
            $duration = $duration + $tier_3_days;
        }

        if ($featured && $featured == $featured_option_1_price) {
            $product_name .= '<br><small class="product-meta">Duration ' . $duration . ' days and featured for ' . $featured_option_1_duration . ' days</small>';
        } elseif ($featured && $featured == $featured_option_2_price) {
            $product_name .= '<br><small class="product-meta">Duration ' . $duration . ' days and featured for ' . $featured_option_2_duration . ' days</small>';
        } else {
            $product_name .= '<br><small class="product-meta">Duration ' . $duration . ' days</small>';
        }
    }

    return $product_name;
}
add_filter('woocommerce_cart_item_name', 'custom_cart_item_name', 10, 3);

function disable_quantity_change_for_product_type($product_quantity, $cart_item_key, $cart_item)
{
    // Get the product ID from the cart item
    $product_id = $cart_item['product_id'];

    // Get the product object
    $product = wc_get_product($product_id);

    // Check if the product belongs to the desired product type
    if ($product && $product->get_type() === 'listing_ad') {
        // Disable quantity change by setting the input field to read-only
        $product_quantity = '<input type="number" name="cart[' . $cart_item_key . '][qty]" value="' . $cart_item['quantity'] . '" size="4" inputmode="numeric" aria-labelledby="input_0" readonly>';
    }

    return $product_quantity;
}
add_filter('woocommerce_cart_item_quantity', 'disable_quantity_change_for_product_type', 10, 3);

function save_custom_data_to_order_meta($item, $cart_item_key, $values, $order)
{
    $cart = WC()->cart->get_cart();
    $quantity = 0;
    foreach ($cart as $cart_item_key => $cart_item) {
        $cart_product_id = $cart_item['product_id'];
        if (has_term('listing_ad', 'product_type', $cart_product_id)) {
            $quantity++;
        }
    }

    // Get the product ID from the cart item
    $product_id = $values['product_id'];

    // Get the product object
    $product = wc_get_product($product_id);

    // Get the product type
    $product_type = $product->get_type();

    if ($product_type === 'listing_ad') {
        $tier_3 = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3));
        $tier_3_days = get_option(SettingsConstants::get_setting_name(SettingsConstants::$tier_3_days));

        $duration = get_post_meta($item->get_product_id(), '_ad_duration', true);
        if ($quantity >= $tier_3) {
            $duration = $duration + $tier_3_days;
        }

        if (isset($item->legacy_values)) {
            $meta_values = $item->legacy_values;
            $status = 'pending';
            $item->add_meta_data('title', $meta_values['title'], true);
            $item->add_meta_data('description', $meta_values['description'], true);
            $item->add_meta_data('category', absint($meta_values['category']), true);
            $item->add_meta_data('other-category', $meta_values['other-category'], true);
            $item->add_meta_data('subcategory', absint($meta_values['subcategory']), true);
            $item->add_meta_data('other-subcategory', $meta_values['other-subcategory'], true);
            $item->add_meta_data('quality', $meta_values['quality'], true);
            $item->add_meta_data('price-type', $meta_values['price-type'], true);
            $item->add_meta_data('price-value', $meta_values['price-value'], true);
            $item->add_meta_data('availability', $meta_values['availability'], true);
            $item->add_meta_data('featured_ads', $meta_values['featured'], true);
            $item->add_meta_data('duration', $duration, true);

            $post_arr = array(
                'post_type' => 'listing_ad',
                'post_title' => $meta_values['title'] ?? '',
                'post_content' => $meta_values['description'] ?? '',
                'post_status' => $status,
                'post_author' => get_current_user_id(),
                'tax_input' => array(
                    'ad_category' => array($meta_values['category'], $meta_values['subcategory'])
                ),
                'meta_input' => array(
                    'other-category' => $meta_values['other-category'],
                    'other-subcategory' => $meta_values['other-subcategory'],
                    'quality' => $meta_values['quality'],
                    'priceType' => $meta_values['price-type'],
                    'price-value' => $meta_values['price-value'],
                    'availability' => $meta_values['availability'],
                    'featured_ads' => $meta_values['featured'] ?? false,
                    'duration' => $duration ?? 0
                ),
            );

            if (isset($meta_values['specsheets']) && $meta_values['specsheets']) {
                $specsheets = [];
                foreach ($meta_values['specsheets'] as $document) {
                    if (is_array($document)) {
                        foreach ($document as $document_id) {
                            $specsheets[$document_id] = wp_get_attachment_url($document_id);
                        }
                    } else {
                        $specsheets[$document] = wp_get_attachment_url($document);
                    }
                }
                $item->add_meta_data('spec_sheet', $specsheets, true);
                $post_arr['meta_input']['spec_sheet'] = $specsheets;
            }

            if (isset($meta_values['images']) && $meta_values['images']) {
                $images = [];
                foreach ($meta_values['images'] as $image) {
                    if (is_array($image)) {
                        foreach ($image as $document_id) {
                            $images[$document_id] = wp_get_attachment_url($document_id);
                        }
                    } else {
                        $images[$image] = wp_get_attachment_url($image);
                    }
                }
                $item->add_meta_data('prod_images', $images, true);
                $post_arr['meta_input']['prod_images'] = $images;
            }

            if (isset($meta_values['brand_logo']) && $meta_values['brand_logo']) {
                $brand_logo = wp_get_attachment_url($meta_values['brand_logo']);
                $item->add_meta_data('brand_logo', $brand_logo, true);
                $brand_logo_id = $meta_values['brand_logo'];
                $item->add_meta_data('brand_logo_id', $brand_logo_id, true);

                $post_arr['meta_input']['brand_logo'] = wp_get_attachment_url($cart_item['brand_logo']);
                $post_arr['meta_input']['brand_logo_id'] = $cart_item['brand_logo'];
            }

            $post_id = wp_insert_post($post_arr, $wp_error);

            // Check if the brand already exists
            $item->add_meta_data('brand', $meta_values['brand'], true);
            $brand = term_exists($meta_values['brand'], 'brand');

            // If the brand doesn't exist, create it
            if (!$brand) {
                $brand = wp_insert_term($meta_values['brand'], 'brand');
            }

            // Assign the brand to the post
            if (!is_wp_error($brand)) {
                wp_set_post_terms($post_id, array(intval($brand['term_id'])), 'brand', true);
            }

            // Check if the model already exists
            $item->add_meta_data('model', $meta_values['model'], true);
            $model = term_exists($meta_values['model'], 'model');

            // If the model doesn't exist, create it
            if (!$model) {
                $model = wp_insert_term($meta_values['model'], 'model');
            }

            // Assign the model to the post
            if (!is_wp_error($model)) {
                wp_set_post_terms($post_id, array(intval($model['term_id'])), 'model', true);
            }

            $item->add_meta_data('listing_ad_id', $post_id, true);
        }
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'save_custom_data_to_order_meta', 10, 4);

function display_custom_data_on_order_received_page($item_id, $item, $bool = false)
{
    // Get the product ID from the order item
    $product_id = $item->get_product_id();

    // Get the product object
    $product = wc_get_product($product_id);

    // Get the product type
    $product_type = $product->get_type();
    if ($product_type === 'listing_ad') {
        $featured = $item->get_meta('featured_ads', true);
        $listing_id = $item->get_meta('listing_ad_id', true);

        $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
        $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));

        if ($featured && $featured == $featured_option_1_price) {
            echo '<a href="' . get_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a>';
        } elseif ($featured && $featured == $featured_option_2_price) {
            echo '<a href="' . get_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a>';
        } else {
            echo '<a href="' . get_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a>';
        }
    }
}
add_action('woocommerce_order_item_name', 'display_custom_data_on_order_received_page', 10, 2);

function plugin_republic_order_item_thumbnail($output_html, $item, $bool = false)
{
    // Get the product ID from the order item
    $product_id = $item->get_product_id();

    // Get the product object
    $product = wc_get_product($product_id);

    // Get the product type
    $product_type = $product->get_type();
    if ($product_type === 'listing_ad') {
        $output_html = "test thumbnail override";

        $featured = $item->get_meta('featured_ads', true);
        $listing_id = $item->get_meta('listing_ad_id', true);

        $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
        $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));

        if ($featured && $featured == $featured_option_1_price) {
            echo '<a href="' . get_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a>';
        } elseif ($featured && $featured == $featured_option_2_price) {
            echo '<a href="' . get_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a>';
        } else {
            echo '<a href="' . get_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a>';
        }
    }

    return $output_html;
}
add_filter('woocommerce_order_item_thumbnail', 'plugin_republic_order_item_thumbnail', 10, 2);

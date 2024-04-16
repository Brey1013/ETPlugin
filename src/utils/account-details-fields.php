<?php

function et_add_account_details_fields_to_start()
{
    $user = wp_get_current_user();

    ?>

    <?php
}
add_action('woocommerce_edit_account_form_start', 'et_add_account_details_fields_to_start');


function et_add_account_details_fields()
{
    $user = wp_get_current_user();

    if (!isset($user->billing_company) && isset($user->user_registration_company)) {
        $user->billing_company = $user->user_registration_company;

        update_user_meta($user->id, 'billing_company', sanitize_text_field($user->user_registration_company));
    }

    $countries_obj = new WC_Countries();

    $countries = $countries_obj->get_countries();
    $states = $countries_obj->get_states();

    $provinces = $states["ZA"];

    ?>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="billing_company">
            <?php esc_html_e('Company name', 'your-text-domain'); ?>
        </label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_company"
            id="billing_company" value="<?php echo esc_attr($user->billing_company); ?>" />
    </p>

    <div class="row">
        <div class="col-auto">

            <?php if (isset($user->image)) { ?>
                <img src="<?php echo wp_get_attachment_image_url($user->image); ?>" alt="Company logo" class="mb-3">
            <?php } ?>

        </div>
        <div class="col">

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="image"><?php esc_html_e('Upload company logo', 'woocommerce'); ?><br />
                    <sub>(optional but we should strongly suggest it is done to add credibility to a vendor
                        listing)</sub></label>
                <input type="file" class="woocommerce-Input" name="image" accept="image/x-png,image/gif,image/jpeg">
            </p>

        </div>
    </div>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="url">
            <?php esc_html_e('Website', 'your-text-domain'); ?>
        </label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="url" id="url"
            value="<?php echo esc_attr($user->url); ?>" />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="vat_number">
            <?php esc_html_e('VAT Number', 'your-text-domain'); ?>
        </label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="vat_number" id="vat_number"
            value="<?php echo esc_attr($user->vat_number); ?>" />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
        <label for="billing_phone">
            <?php esc_html_e('Mobile number', 'your-text-domain'); ?>
        </label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_phone"
            id="billing_phone" value="<?php echo esc_attr($user->billing_phone); ?>" />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-last">
        <label for="billing_landline">
            <?php esc_html_e('Landline number', 'your-text-domain'); ?>
        </label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_landline"
            id="billing_landline" value="<?php echo esc_attr($user->billing_landline); ?>" />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="billing_country">
            <?php esc_html_e('Country *', 'your-text-domain'); ?>
        </label>

        <select name="billing_country" id="billing_country" class="postform et-searchable-dropdown" autocomplete="country"
            data-placeholder="Select a country / region…" data-label="Country *">
            <option>Select a country / region…</option>

            <?php foreach ($countries as $key => $province) {
                echo '<option value="' . $key . '"' . (esc_attr($user->billing_country) == $key ? 'selected' : '') . '>' . $province . '</option>';
            } ?>

        </select>
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="billing_address_1" class="">Street address *</label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_address_1"
            id="billing_address_1" placeholder="House number and street name"
            value="<?php echo esc_attr($user->billing_address_1); ?>">
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_address_2"
            id="billing_address_2" placeholder="Apartment, suite, unit, etc. (optional)"
            value="<?php echo esc_attr($user->billing_address_2); ?>">
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="billing_city" class="">Town / City *</label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_city"
            id="billing_city" placeholder="" value="<?php echo esc_attr($user->billing_city); ?>">
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="billing_state" class="">Province *</label>
        <select name="billing_state" id="billing_state" class="postform et-searchable-dropdown" autocomplete="province"
            data-placeholder="Select a province..." data-label="Province *">
            <option>Select a province...</option>

            <?php foreach ($provinces as $key => $province) {
                echo '<option value="' . $key . '"' . (esc_attr($user->billing_state) == $key ? 'selected' : '') . '>' . $province . '</option>';
            } ?>

        </select>
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="billing_postcode" class="">Postcode / ZIP *</label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_postcode"
            id="billing_postcode" placeholder="" value="<?php echo esc_attr($user->billing_postcode); ?>">
    </p>

    <?php
}
add_action('woocommerce_edit_account_form', 'et_add_account_details_fields');

function et_save_account_details_fields($user_id)
{
    if (isset($_POST['billing_company'])) {
        update_user_meta($user_id, 'billing_company', sanitize_text_field($_POST['billing_company']));
    }

    if (isset($_POST['url'])) {
        update_user_meta($user_id, 'url', sanitize_text_field($_POST['url']));
    }

    if (isset($_POST['vat_number'])) {
        update_user_meta($user_id, 'vat_number', sanitize_text_field($_POST['vat_number']));
    }

    if (isset($_POST['billing_phone'])) {
        update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']));
    }

    if (isset($_POST['billing_landline'])) {
        update_user_meta($user_id, 'billing_landline', sanitize_text_field($_POST['billing_landline']));
    }

    if (isset($_POST['billing_country'])) {
        update_user_meta($user_id, 'billing_country', sanitize_text_field($_POST['billing_country']));
    }

    if (isset($_POST['billing_address_1'])) {
        update_user_meta($user_id, 'billing_address_1', sanitize_text_field($_POST['billing_address_1']));
    }

    if (isset($_POST['billing_address_2'])) {
        update_user_meta($user_id, 'billing_address_2', sanitize_text_field($_POST['billing_address_2']));
    }

    if (isset($_POST['billing_city'])) {
        update_user_meta($user_id, 'billing_city', sanitize_text_field($_POST['billing_city']));
    }

    if (isset($_POST['billing_state'])) {
        update_user_meta($user_id, 'billing_state', sanitize_text_field($_POST['billing_state']));
    }

    if (isset($_POST['billing_postcode'])) {
        update_user_meta($user_id, 'billing_postcode', sanitize_text_field($_POST['billing_postcode']));
    }

    if (isset($_FILES['image'])) {
        require_once (ABSPATH . 'wp-admin/includes/image.php');
        require_once (ABSPATH . 'wp-admin/includes/file.php');
        require_once (ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload('image', 0);

        if (!is_wp_error($attachment_id)) {
            update_user_meta($user_id, 'image', $attachment_id);
        }
    }
}
add_action('woocommerce_save_account_details', 'et_save_account_details_fields');

function action_woocommerce_edit_account_form_tag()
{
    echo 'enctype="multipart/form-data"';
}
add_action('woocommerce_edit_account_form_tag', 'action_woocommerce_edit_account_form_tag');
<?php

add_action('woocommerce_edit_account_form', 'et_add_account_details_fields');
function et_add_account_details_fields()
{
    $user = wp_get_current_user();

    if (!isset ($user->billing_company) && isset ($user->user_registration_company)) {
        $user->billing_company = $user->user_registration_company;

        update_user_meta($user->id, 'billing_company', sanitize_text_field($user->user_registration_company));
    }

    ?>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="billing_company">
            <?php esc_html_e('Company Name', 'your-text-domain'); ?>
        </label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_company"
            id="billing_company" value="<?php echo esc_attr($user->billing_company); ?>" />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="vat_number">
            <?php esc_html_e('VAT Number', 'your-text-domain'); ?>
        </label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="vat_number" id="vat_number"
            value="<?php echo esc_attr($user->vat_number); ?>" />
    </p>

    <?php
}

add_action('woocommerce_save_account_details', 'et_save_account_details_fields');
function et_save_account_details_fields($user_id)
{
    if (isset ($_POST['billing_company'])) {
        update_user_meta($user_id, 'billing_company', sanitize_text_field($_POST['billing_company']));
    }

    if (isset ($_POST['vat_number'])) {
        update_user_meta($user_id, 'vat_number', sanitize_text_field($_POST['vat_number']));
    }
}
<?php

function custom_user_profile_fields($user)
{
}

add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');
add_action('user_new_form', 'custom_user_profile_fields');

function save_custom_user_profile_fields($user_id)
{
    if (current_user_can('edit_user', $user_id)) {
        $user_registration_company = $_POST['user_registration_company'];

        $user = wp_get_current_user();

        if (!isset($user->billing_company) && isset($user_registration_company)) {
            update_user_meta($user_id, 'billing_company', sanitize_text_field($user_registration_company));
        }
    }
}

add_action('personal_options_update', 'save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');
add_action('user_register', 'save_custom_user_profile_fields');
add_action('profile_update', 'save_custom_user_profile_fields');

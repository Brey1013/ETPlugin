<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function registration_terms_acceptance()
{
    $post = get_post();

    $author_id = $post->post_author;

    $display_name = get_the_author_meta('billing_company', $author_id);

    if (!isset($display_name) && !$display_name)
        $display_name = get_the_author_meta('display_name', $author_id);

    $account_email = get_the_author_meta('email', $author_id);
    $billing_phone = get_the_author_meta('billing_phone', $author_id);
    $logo = get_the_author_meta('image', $author_id);

    ob_start();

    include (plugin_dir_path(__DIR__) . 'includes/registration-terms-acceptance.php');

    return ob_get_clean();
}

add_shortcode('et-registration-terms-acceptance', 'registration_terms_acceptance');
<?php

/**
 * All actions registered by the plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/custom-profile-fields.php';
require_once __DIR__ . '/related-products.php';
require_once __DIR__ . '/save-order.php';
require_once __DIR__ . '/update-order.php';
require_once __DIR__ . '/wc-cart-customizations.php';
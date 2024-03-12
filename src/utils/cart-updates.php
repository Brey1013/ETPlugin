<?php

function et_remove_quantity_column_from_cart($return, $product)
{
    if (is_cart())
        return true;
}
add_filter('woocommerce_is_sold_individually', 'et_remove_quantity_column_from_cart', 10, 2);

function et_add_custom_cart_css()
{
    if (is_cart()) {
        echo '<style>
    /* Change number 4 with the number of the column you want to remove */
    .woocommerce table.cart td:nth-of-type(5), .woocommerce table.cart th:nth-of-type(5) {
            display: none;
        }

        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce button.button[name="update_cart"] {
            display: none;
        }
    </style>';
    }
}
add_action('wp_footer', 'et_add_custom_cart_css');
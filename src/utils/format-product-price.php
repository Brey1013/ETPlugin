<?php

function format_product_price($priceValue)
{
    $priceValue = str_replace(" ", '', $priceValue);

    echo number_format(floatval($priceValue), 0, ".", " ") . " Ex VAT";
}
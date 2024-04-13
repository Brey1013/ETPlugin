<?php
/**
 * Listing Ad Product Type
 */
class WC_Product_Listing_Ad extends WC_Product
{

    public function __construct($product)
    {
        $this->product_type = 'listing_ad';
        parent::__construct($product);
    }
}
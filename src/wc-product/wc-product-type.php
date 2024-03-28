<?php

/**
 * New WooCommerce Product Type
 */

if (!defined('ABSPATH')) {
    return;
}

class WC_Product_Type_Plugin
{

    /**
     * Build the instance
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'install'));
        add_action('init', array($this, 'load_plugin'));
        add_filter('product_type_selector', array($this, 'add_type'));
        add_action('woocommerce_product_options_pricing', array($this, 'add_advanced_pricing'));
        add_action('woocommerce_product_options_general_product_data', function () {
            echo '<div class="options_group show_if_listing-ad clear"></div>';
        });
        add_action('admin_footer', array($this, 'enable_js_on_wc_product'));
        add_filter('woocommerce_product_data_tabs', array($this, 'add_product_tab'), 50);
        add_action('woocommerce_process_product_meta_listing-ad', array($this, 'save_advanced_settings'));
    }

    /**
     * Listing Ad Type
     *
     * @param array $types
     * @return void
     */
    public function add_type($types)
    {
        $types['listing-ad'] = __('Listing Ad', 'equipmenttrader');

        return $types;
    }

    /**
     * Installing on activation
     *
     * @return void
     */
    public function install()
    {
        // If there is no advanced product type taxonomy, add it.
        if (!get_term_by('slug', 'listing-ad', 'product_type')) {
            wp_insert_term('listing-ad', 'product_type');
        }
    }

    /**
     * Load WC Dependencies
     *
     * @return void
     */
    public function load_plugin()
    {
        require_once __DIR__ . '/includes/class-wc-product-listing-ad.php';
    }

    /**
     * Add the pricing
     * @return void
     */
    public function add_advanced_pricing()
    {
        global $product_object;
        ?>
        <div class='options_group show_if_listing-ad'>
            <?php

            woocommerce_wp_text_input(
                array(
                    'id' => '_ad_duration',
                    'label' => __('Duration for Advert', 'equipmenttrader'),
                    'value' => $product_object->get_meta('_ad_duration', true),
                    'default' => '',
                    'placeholder' => 'Ad Duration',
                    'data_type' => 'number',
                )
            );
            ?>
        </div>

        <?php
    }

    public function enable_js_on_wc_product()
    {
        global $post, $product_object;

        if (!$post) {
            return;
        }

        if ('product' != $post->post_type):
            return;
        endif;
        // echo "<pre>";print_r($product_object);exit;
        $is_advanced = $product_object && 'listing-ad' === $product_object->get_type() ? true : false;

        ?>
        <script type='text/javascript'>
            jQuery(document).ready(function () {
                //for Price tab
                jQuery('#general_product_data .pricing').addClass('show_if_listing-ad');

                <?php if ($is_advanced) { ?>
                    jQuery('#general_product_data .pricing').show();
                <?php } ?>
            });
        </script>
        <?php
    }

    /**
     * Add Experience Product Tab.
     *
     * @param array $tabs
     *
     * @return mixed
     */
    public function add_product_tab($tabs)
    {

        $tabs['advanced_type'] = array(
            'label' => __('Listing Ad', 'equipmenttrader'),
            'target' => 'listing_ad_type_product_options',
            'class' => 'show_if_listing-ad',
        );

        return $tabs;
    }

    /**
     * @param $post_id
     */
    public function save_advanced_settings($post_id)
    {
        $price = isset($_POST['_ad_duration']) ? sanitize_text_field($_POST['_ad_duration']) : '';
        update_post_meta($post_id, '_ad_duration', $price);
    }
}

new WC_Product_Type_Plugin();

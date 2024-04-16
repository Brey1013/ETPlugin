<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require $PLUGIN_ABSPATH . '/constants/index.php';

function et_my_listings()
{
    global $wpdb;

    $current_user_id = get_current_user_id();
    $status_filter = isset($_GET["status"]) ? strtolower($_GET["status"]) : null;

    $query = "SELECT p.ID, p.post_title, p.post_status, pm_featured.meta_value AS featured, pm_end_date.meta_value AS end_date
        FROM {$wpdb->posts} p
            LEFT OUTER JOIN {$wpdb->postmeta} AS pm_featured ON p.ID = pm_featured.post_id AND pm_featured.meta_key = 'featured_ads'
            LEFT OUTER JOIN {$wpdb->postmeta} AS pm_end_date ON p.ID = pm_end_date.post_id AND pm_end_date.meta_key = 'end_listing_date'
        WHERE p.post_type = 'listing_ad' AND p.post_author = {$current_user_id} AND p.post_status <> 'auto-draft' ";

    if ($status_filter === 'temp-draft' || $status_filter === 'publish')
        $query .= " AND p.post_status = '$status_filter' ";
    else if ($status_filter === 'featured')
        $query .= " AND pm_featured.meta_key = 'featured_ads' ";
    else if ($status_filter === 'expired')
        $query .= " AND (pm_end_date.meta_value < date(NOW())) ";
    else if ($status_filter === 'pending')
        $query .= " AND (pm_end_date.meta_value IS NULL) ";

    $query .= " ORDER BY p.post_date DESC";

    $results = $wpdb->get_results($query);

    ?>

    <form method="post">
        <div class="text-right mb-2">
            <input type="submit" name="my-listings-form-submit"
                value="<?php _e('Add Selected To Cart', 'equipmenttrader'); ?>" class="btn btn-primary" disabled />
        </div>
        <table
            class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
            <thead>
                <tr>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><span
                            class="nobr"></span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-id"><span
                            class="nobr">
                            <?php _e('Listing ID', 'equipmenttrader'); ?>
                        </span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-title"><span
                            class="nobr">
                            <?php _e('Title', 'equipmenttrader'); ?>
                        </span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-category"><span
                            class="nobr">
                            <?php _e('Category', 'equipmenttrader'); ?>
                        </span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span
                            class="nobr">
                            <?php _e('Expiry date', 'equipmenttrader'); ?>
                        </span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-is-order-featured"><span
                            class="nobr">
                            <?php _e('Featured', 'equipmenttrader'); ?>
                        </span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><span
                            class="nobr"></span></th>
                </tr>
            </thead>

            <tbody>
                <?php
                $taxonomy = 'ad_category';

                foreach ($results as $result) {
                    $product_id = $result->ID;
                    $link = get_edit_link($result);
                    $categories = array();

                    $terms = wp_get_post_terms($product_id, $taxonomy, array('orderby' => 'term_order'));

                    $term_name = '';
                    $term_name_second = '';

                    foreach ($terms as $term) {
                        $parent = $term->parent;
                        if ($parent == '0') {
                            $term_name = $term->name;
                        } else {
                            $parent_term = get_term_by('id', $parent, $taxonomy);
                            $term_name_second = $term->name;
                            $term_name = $parent_term->name;
                        }
                    }

                    $other_category_term = get_post_meta($product_id, 'other-category', true);

                    if (strlen($other_category_term) > 0)
                        $term_name = $other_category_term;

                    $other_sub_category_term = get_post_meta($product_id, 'other-subcategory', true);

                    if (strlen($other_sub_category_term) > 0)
                        $term_name_second = $other_sub_category_term;

                    if ($result->post_status === 'temp-draft') {
                        $adData = get_post_meta($product_id, 'cart_items');

                        if (count($adData) > 0) {
                            $draft_data = $adData[0];

                            $result->post_title = $draft_data['title'];

                            $term_name = strlen($draft_data["other-category"]) > 0 ? $draft_data["other-category"] : get_term_by('id', $draft_data["category"], $taxonomy)->name;
                            $term_name_second = strlen($draft_data["other-subcategory"]) > 0 ? $draft_data["other-subcategory"] : get_term_by('id', $draft_data["subcategory"], $taxonomy)->name;

                            $result->featured = $draft_data["featured"];
                        }
                    }

                    if (isset($term_name))
                        $categories[] = $term_name;

                    if (isset($term_name_second))
                        $categories[] = $term_name_second;

                    ?>

                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-on-hold order">
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="">
                            <input type="checkbox" name="add-to-cart[]" />
                            <input type="hidden" name="add-to-cart-ids[]" value="<?php echo $product_id; ?>" />
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-id"
                            data-title="Listing ID">
                            <a href="<?php echo $link ?>"> #
                                <?php echo $product_id; ?>
                            </a>
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-title"
                            data-title="Title">
                            <span class="woocommerce-Title title">
                                <?php echo $result->post_title; ?>
                            </span>
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-category"
                            data-title="Category">
                            <span class="woocommerce-Category category">
                                <?php echo join(" > ", $categories); ?>
                            </span>
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
                            data-title="Expiry Date">
                            <time datetime="<?php echo $result->end_date; ?>">
                                <?php echo $result->end_date; ?>
                            </time>
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-is-order-featured"
                            data-title="Is Featured">
                            <input disabled type="checkbox" class="woocommerce-Price-amount amount" <?php echo $result->featured ? 'checked' : ''; ?> />
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="">
                            <a href="<?php echo $link ?>" class="wcmtx_custom_action woocommerce-button button "><span
                                    class="wcmtx_action_name">
                                    <?php _e('View', 'equipmenttrader'); ?>
                                </span><span class="wcmtx_action_html"><i class="fa fa-eye"></i></span></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </form>

    <?php
}

add_shortcode('et-my-listings', 'et_my_listings');

function get_edit_link($result)
{
    $product_id = $result->ID;

    $link = get_option(SettingsConstants::get_setting_name(SettingsConstants::$relative_listing_path));

    if ($result->post_status === 'temp-draft')
        $link .= '?draft_id=' . $product_id;
    else
        $link .= '?listing_id=' . $product_id;

    return $link;
}
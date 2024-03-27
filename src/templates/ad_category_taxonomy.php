<?php
// Template Name: Custom Taxonomy Archive

get_header();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$today = date('Y-m-d');
$term = get_queried_object();

global $wpdb;
$query = "
    SELECT p.ID
    FROM {$wpdb->posts} AS p
        LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id AND pm.meta_key = 'featured_ads'
        LEFT JOIN {$wpdb->postmeta} AS pm_endate ON p.ID = pm_endate.post_id AND pm_endate.meta_key = 'end_listing_date'
        LEFT JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
    WHERE p.post_type = 'listing_ad'
        AND p.post_status = 'publish'
        AND (pm.meta_key IS NULL OR pm.meta_key = 'featured_ads')
        AND (pm_endate.meta_value >= date(NOW()) AND pm_endate.meta_value IS NOT NULL)
        AND tt.taxonomy = 'ad_category'
        AND tt.term_id = {$term->term_id}
    ORDER BY
        CASE
            WHEN pm.meta_value IS NOT NULL THEN pm.meta_value
            ELSE '0'
        END DESC,
        p.post_date DESC
";

$query_featured = $wpdb->get_col($query);
if ($query_featured) {
    $featured_posts_18 = array(); // For featured ads with value 18
    $featured_posts_25 = array(); // For featured ads with value 25
    $normal_posts = array();

    foreach ($query_featured as $featured_id) {
        $featured = get_post_meta($featured_id, 'featured_ads', true);
        $end_listing_date = get_post_meta($featured_id, 'end_listing_date', true);
        $duration = get_post_meta($featured_id, 'duration', true);
        $publish_date = get_the_date('Y-m-d', $featured_id);
        $today = time();

        $featured_option_1_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_duration));
        $featured_option_1_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_1_price));
        $featured_option_2_duration = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_duration));
        $featured_option_2_price = get_option(SettingsConstants::get_setting_name(SettingsConstants::$featured_option_2_price));

        $ad_arr = array(
            'id' => $featured_id,
            'date' => $publish_date
        );

        if ($featured == $featured_option_1_price) {
            $remaining_days = $duration - $featured_option_1_duration;
            $feature_expiry_date = strtotime('-' . $remaining_days . ' days', strtotime($end_listing_date));
            if ($feature_expiry_date >= $today) {
                $featured_posts_18[] = $ad_arr;
            } else {
                $normal_posts[] = $ad_arr;
            }
        } elseif ($featured == $featured_option_2_price) {
            $remaining_days = $duration - $featured_option_2_duration;
            $feature_expiry_date = strtotime('-' . $remaining_days . ' days', strtotime($end_listing_date));
            if ($feature_expiry_date >= $today) {
                $featured_posts_25[] = $ad_arr;
            } else {
                $normal_posts[] = $ad_arr;
            }
        } else {
            $normal_posts[] = $ad_arr;
        }
    }

    // Set up pagination
    $total = count($query_featured);
    $posts_per_page = get_option('posts_per_page');
    $total_pages = ceil($total / $posts_per_page);


    // Merge featured and normal posts
    $all_posts = array_merge($featured_posts_25, $featured_posts_18);

    // Custom comparison function to sort by date
    function sortByDate($a, $b)
    {
        return strtotime($b['date']) - strtotime($a['date']);
    }

    // Sort the array by date
    usort($all_posts, 'sortByDate');
    usort($normal_posts, 'sortByDate');

    $all_posts = array_merge($all_posts, $normal_posts);

    // Create an array of just the IDs
    $idsArray = array_map(function ($item) {
        return $item['id'];
    }, $all_posts);


    // Get posts for the current page
    $current_page_posts = array_slice($idsArray, ($paged - 1) * $posts_per_page, $posts_per_page);

    $args_all = array(
        'post_type' => 'listing_ad',
        'post__in' => $current_page_posts,
        'orderby' => 'post__in',
        // 'paged' => $paged,
        'posts_per_page' => -1,
    );

    $query_all = new WP_Query($args_all);
} else {
    $query_all = [];
}
?>
<div id="primary" class="content-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-12">
                <div class="site-content-block">
                    <div class="main-content">
                        <div class="row">
                            <div class="col-4">
                                <form>
                                    <?php include (plugin_dir_path(__DIR__) . 'includes/categories-widget.php'); ?>
                                    <?php include (plugin_dir_path(__DIR__) . 'includes/prices-filter.php'); ?>
                                    <input type="submit" value="Apply Filter" class="btn btn-primary" />
                                </form>
                            </div>
                            <div class="col-8">
                                <div class="rtcl rtcl-listings rtcl-listings-list">
                                    <div class="rtcl-list-view">
                                        <?php
                                        if ($query_all && $query_all->have_posts()) {
                                            while ($query_all->have_posts()) {
                                                $query_all->the_post();
                                                $product_id = get_the_ID();
                                                $brand_logo = get_post_meta($product_id, 'brand_logo', true);
                                                $prod_images = get_post_meta($product_id, 'prod_images', true);
                                                $brand_array = wp_get_post_terms($product_id, 'brand', array('orderby' => 'term_order'));
                                                $brand_str = array();

                                                foreach ($brand_array as $cat) {
                                                    $brand_str[] = $cat->name;
                                                }

                                                $model_array = wp_get_post_terms($product_id, 'model', array('orderby' => 'term_order'));
                                                $model_str = array();

                                                foreach ($model_array as $cat) {
                                                    $model_str[] = $cat->name;
                                                }

                                                $priceType = get_post_meta($product_id, 'priceType', true);
                                                $priceValue = get_post_meta($product_id, 'price-value', true);
                                                $featured_ads = get_post_meta($product_id, 'featured_ads', true);
                                                $cat_array = wp_get_post_terms($product_id, 'ad_category', array('orderby' => 'term_order'));
                                                $cat_str = array();

                                                foreach ($cat_array as $cat) {
                                                    $cat_str[] = $cat->name;
                                                }

                                                $publish_date = get_the_date('Y-m-d');

                                                $isFeatured = ($featured_ads == 18 && strtotime($publish_date) >= strtotime('-14 days', strtotime($today))) ||
                                                    ($featured_ads == 25 && strtotime($publish_date) >= strtotime('-30 days', strtotime($today)));
                                                ?>
                                                <div
                                                    class="listing-list-each listing-list-each-2 rtcl-listing-item <?php echo $isFeatured ? 'featured-listing' : ''; ?>">
                                                    <div class="rtin-item">
                                                        <div class="rtin-thumb">
                                                            <a class="rtin-thumb-inner rtcl-media"
                                                                href="<?php echo esc_url(get_permalink($product_id)); ?>">
                                                                <?php if ($prod_images) { ?>
                                                                    <?php foreach ($prod_images as $img): ?>
                                                                        <img src="<?php echo $img; ?>"></br>
                                                                        <?php break; ?>
                                                                    <?php endforeach; ?>
                                                                <?php } ?>
                                                            </a>
                                                        </div>
                                                        <div class="rtin-content-area">
                                                            <div class="rtin-content">
                                                                <span class="rtin-cat">
                                                                    <?php echo implode(' > ', $cat_str); ?>
                                                                </span>
                                                                <h3 class="rtin-title listing-title">
                                                                    <a
                                                                        href="<?php echo esc_url(get_permalink($product_id)); ?>">
                                                                        <?php echo get_the_title(); ?>
                                                                    </a>
                                                                </h3>
                                                                <div class="rtcl-listing-badge-wrap">
                                                                    <ul class="rtin-meta">
                                                                        <li><i class="far fa-clock" aria-hidden="true"></i>
                                                                            <?php echo get_the_date('d-m-Y h:i A'); ?>
                                                                        </li>
                                                                        <?php if ($brand_str || $model_str) { ?>
                                                                            <li>
                                                                                <?php echo implode(', ', $brand_str); ?> >
                                                                                <?php echo implode(', ', $model_str); ?>
                                                                            </li>
                                                                        <?php } ?>
                                                                        <?php if ($isFeatured) { ?>
                                                                            <li>
                                                                                <span class="badge rtcl-badge-featured">
                                                                                    <?php echo ($featured_ads == 18 || $featured_ads == 25) ? 'Featured' : ''; ?>
                                                                                </span>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>

                                                                </div>
                                                            </div>
                                                            <div class="rtin-right">
                                                                <div class="rtin-price rtin-right-meta">
                                                                    <div class="rtcl-price price-type-fixed">
                                                                        <span class="rtcl-price-amount amount">
                                                                            <?php if ($priceType == 'POA') { ?>
                                                                                POA
                                                                            <?php } elseif ($priceValue) { ?>
                                                                                <span class="rtcl-price-currencySymbol">R</span>
                                                                                <?php echo $priceValue; ?>
                                                                            <?php } ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="rtin-details">
                                                                    <a
                                                                        href="<?php echo esc_url(get_permalink($product_id)); ?>">
                                                                        <?php _e('Details', 'equipmenttrader'); ?>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                            // Generate pagination links
                                            $big = 999999999; // Need an unlikely integer
                                            $pagination = paginate_links(
                                                array(
                                                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                                    'format' => '?paged=%#%',
                                                    'current' => $paged,
                                                    'total' => $total_pages,
                                                    'prev_text' => __('&laquo; Previous'),
                                                    'next_text' => __('Next &raquo;'),
                                                )
                                            );
                                            if ($pagination) {
                                                echo '<div class="pagination">';
                                                echo $pagination;
                                                echo '</div>';
                                            }
                                        } else { ?>
                                            <h2>
                                                <?php _e('No ads found', 'equipmenttrader'); ?>
                                            </h2>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
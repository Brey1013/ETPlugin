<div class="ads-listing">
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
                                                <a href="<?php echo esc_url(get_permalink($product_id)); ?>">
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
                                                <a href="<?php echo esc_url(get_permalink($product_id)); ?>">Details</a>
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
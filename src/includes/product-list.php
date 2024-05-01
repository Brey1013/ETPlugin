<div class="ads-listing">
    <div class="row">
        <?php if ($args['hide_filters'] == false) { ?>
            <div class="col-4 et-search-form">
                <?php echo do_shortcode('[et-search-form simple-form="true"]') ?>
            </div>
        <?php } ?>
        <div class="col">
            <?php if ($args['hide_filters'] == false) { ?>
                <div class="row">
                    <div class="col">
                        <form method="get" autocomplete="off" class="form-inline justify-content-end">
                            <label class="sr-only" for="order_by">Sort By</label>
                            <select class="form-control mb-2" name="order_by">
                                <?php foreach ($sortOrders as $key => $label) { ?>
                                    <option value="<?php echo $key; ?>" <?php echo $order_by == $key ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </form>
                    </div>
                </div>
            <?php } ?>
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

                            if (count($cat_array) === 1 && $cat_array[0]->parent != 0) {
                                $parent = get_term($cat_array[0]->parent);

                                $cat_str[] = $parent->name;
                            }

                            foreach ($cat_array as $cat) {
                                $cat_str[] = $cat->name;
                            }

                            $publish_date = get_post_field('post_date', $product_id);

                            $isFeatured = ($featured_ads == $featured_option_1_price && $today <= strtotime("$featured_option_1_duration days", strtotime($publish_date))) ||
                                ($featured_ads == $featured_option_2_price && $today <= strtotime("$featured_option_2_duration days", strtotime($publish_date)));

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
                                                                <?php echo 'Featured'; ?>
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
                                                            <?php format_product_price($priceValue); ?>
                                                        <?php } ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="rtin-details">
                                                <a href="<?php echo esc_url(get_permalink($product_id)); ?>">
                                                    <?php _e('Details', 'equipmenttrader'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }

                        if ($args['disable_pagination'] == false) {

                            // Generate pagination links
                            $big = 999999999; // Need an unlikely integer
                            $pagination = paginate_links(
                                array(
                                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                    'format' => '?paged=%#%',
                                    'current' => $paged,
                                    'total' => $total_pages,
                                    'posts_per_page' => $posts_per_page,
                                    'prev_text' => __('&lt; Previous'),
                                    'next_text' => __('Next &gt;'),
                                )
                            );
                            if ($pagination) {
                                echo '<div class="pagination">';
                                echo str_replace('page-numbers', 'page-link', $pagination);
                                echo '</div>';
                            }
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
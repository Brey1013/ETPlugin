<?php get_header(); ?>
<?php
$postType = 'listing_ad';
$product_id = get_the_ID();

$prod_images = get_post_meta($product_id, 'prod_images', true);
$spec_sheet = get_post_meta($product_id, 'spec_sheet', true);
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

$quality = get_post_meta($product_id, 'quality', true);
$priceType = get_post_meta($product_id, 'priceType', true);
$priceValue = get_post_meta($product_id, 'price-value', true);
$availability = get_post_meta($product_id, 'availability', true);
$featured_ads = get_post_meta($product_id, 'featured_ads', true);

$taxonomy = 'ad_category'; //Choose the taxonomy
$terms = get_terms($taxonomy); //Get all the terms
foreach ($terms as $term) { //Cycle through terms, one at a time

    // Check and see if the term is a top-level parent. If so, display it.
    $parent = $term->parent;
    if ($parent == '0') {

        $term_id = $term->term_id; //Define the term ID
        $term_link = get_term_link($term, $taxonomy); //Get the link to the archive page for that term
        $term_name = $term->name;
    } else {
        $term_id_second = $term->term_id; //Define the term ID
        $term_link_second = get_term_link($term, $taxonomy); //Get the link to the archive page for that term
        $term_name_second = $term->name;
    }
}
?>

<div id="primary" class="content-area classima-listing-single">
    <div class="et-product-breadcrumb">
        <i class="fas fa-chevron-left"></i> <a href="javascript:history.back()">Back</a>
        <b>|</b> <a href="<?php echo $term_link ?>" title="<?php echo $term_name ?>">
            <?php echo $term_name ?>
        </a> <i class="fas fa-chevron-right"></i> <a href="<?php echo $term_link_second ?>"
            title="<?php echo $term_name_second ?>">
            <?php echo $term_name_second ?>
        </a> <i class="fas fa-chevron-right"></i> <a href=".">
            <?php echo get_the_title(); ?>
        </a>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-12">
                <div class="site-content-block">
                    <div class="main-content">
                        <section class="product-header">
                            <div class="et-product-header-area">
                            </div>
                            <!-- <hr class="et-product-hr"> -->
                        </section>
                        <section class="product-content">
                            <div class="rtin-content-area">
                                <div class="row">
                                    <div class="col-12 col-md-9">
                                        <div class="rtin-content">
                                            <?php if ($prod_images) { ?>
                                                <section id="product-gallery" class="rtin-slider-box">
                                                    <div id="product-carousel" class="carousel slide rtcl-slider"
                                                        data-ride="carousel">
                                                        <div class="carousel-inner">
                                                            <?php $i = 1; ?>
                                                            <?php foreach ($prod_images as $img) { ?>
                                                                <?php if ($i == 1) {
                                                                    $class = 'active';
                                                                } else {
                                                                    $class = '';
                                                                }
                                                                ?>
                                                                <div class="carousel-item <?php echo $class; ?>"
                                                                    style="text-align: center;">
                                                                    <img src="<?php echo $img; ?>" style="max-height: inherit;"
                                                                        alt="<?php echo $term_name; ?> <?php echo $i; ?>" />
                                                                </div>
                                                                <?php $i++; ?>
                                                            <?php } ?>
                                                        </div>

                                                        <ol class="carousel-indicators">
                                                            <?php $j = 0; ?>
                                                            <?php foreach ($prod_images as $img1) {
                                                                if ($j == 0) {
                                                                    $class = 'active';
                                                                } else {
                                                                    $class = '';
                                                                }
                                                                ?>
                                                                <li data-target="#product-carousel"
                                                                    data-slide-to="<?php echo $j; ?>"
                                                                    class="<?php echo $class; ?>">
                                                                    <img src="<?php echo $img1; ?>" class="d-block w-100"
                                                                        alt="one" />
                                                                </li>
                                                                <?php $j++; ?>
                                                            <?php } ?>
                                                        </ol>

                                                        <a class="carousel-control-prev" href="#product-carousel"
                                                            role="button" data-slide="prev">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </a>
                                                        <a class="carousel-control-next" href="#product-carousel"
                                                            role="button" data-slide="next">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </div>
                                                </section>
                                            <?php } ?>
                                            <div class="et-product-title">
                                                <h1>
                                                    <?php echo get_the_title(); ?>
                                                </h1>
                                            </div>
                                            <div class="et-product-price">
                                                <span class="rtin-label">R </span> <span class="rtin-title">
                                                    <?php echo $priceValue; ?>
                                                </span>
                                            </div>

                                            <div class="single-listing-meta-wrap">
                                                <?php if ($brand_str || $model_str) { ?>
                                                    <ul class="single-listing-meta">
                                                        <li>
                                                            <?php echo implode(', ', $brand_str); ?> >
                                                            <?php echo implode(', ', $model_str); ?>
                                                        </li>
                                                    </ul>
                                                <?php } ?>
                                                <div class="rtcl-listing-badge-wrap">
                                                    <?php if ($featured_ads) { ?>
                                                        <span class="badge rtcl-badge-featured">
                                                            <?php echo 'Featured'; ?>
                                                        </span>
                                                    <?php } ?>
                                                    <span class="badge rtcl-badge-_top">
                                                        <?php echo $quality; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="et-product-seller-info">
                                            <?php echo do_shortcode('[et-seller-information-widget]'); ?>
                                        </div>
                                        <div class="et-product-seller-map">
                                            <?php echo do_shortcode('[et-maps-widget]'); ?>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <hr class="et-product-hr">
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <div class="et-product-overview">
                                            <h3 class="rtin-specs-title">Overview</h3>
                                            <hr class="et-styled-hr">
                                            <div class="classima-custom-fields clearfix">
                                                <ul>
                                                    <li> <span class="rtin-label">Brand: </span> <span
                                                            class="rtin-title">
                                                            <?php echo implode(', ', $brand_str); ?>
                                                        </span></li>
                                                    <li> <span class="rtin-label">Product Code/Model : </span> <span
                                                            class="rtin-title">
                                                            <?php echo implode(', ', $model_str); ?>
                                                        </span></li>
                                                    <li> <span class="rtin-label">Category: </span> <span
                                                            class="rtin-title">
                                                            <?php echo $term_name; ?>
                                                        </span></li>
                                                    <li> <span class="rtin-label">Product Type: </span> <span
                                                            class="rtin-title">
                                                            <?php echo $term_name_second; ?>
                                                        </span></li>
                                                    <li> <span class="rtin-label">Availability: </span> <span
                                                            class="rtin-title">
                                                            <?php echo $availability; ?>
                                                        </span></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php if ($spec_sheet) { ?>
                                            <div>
                                                <p>Spec Sheets:</p>
                                                <?php foreach ($spec_sheet as $sheet) {
                                                    $file_parts = explode('/', $sheet);
                                                    $fileName = end($file_parts);
                                                    ?>
                                                    <span class="et-spec-sheet-button badge badge-pill badge-secondary"><a
                                                            href="<?php echo $sheet; ?>" target="_blank">
                                                            <?php echo $fileName; ?> <i class="doc-icon fas fa-angle-down"></i>
                                                        </a></span>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <h3 class="rtin-specs-title" style="margin-top: 25px">Description</h3>
                                        <hr class="et-styled-hr">
                                        <div class="et-product-content">
                                            <?php echo get_the_content(); ?>
                                        </div>

                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="et-product-seller-brand">
                                            <?php echo do_shortcode('[et-brands-widget]'); ?>
                                        </div>
                                    </div>
                                </div>
                                <hr class="et-product-hr">
                            </div>
                            <div class="et-product-related et-ad-listing">
                                <h3>Related Products</h3>
                                <hr class="et-styled-hr">
                                <?php

                                $related_ids = wc_get_related_products($product_id, 4, array($product_id));

                                echo do_shortcode('[et-product-list product_ids="' . join(',', $related_ids) . '" hide_filters="true" ]');

                                ?>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
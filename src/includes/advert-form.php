<?php

global $woocommerce;

?>

<form method="post" enctype="multipart/form-data" class="et-submit-advert-form">
    <input type="hidden" name="action" value="handle_advert_form_submission">

    <div class="row">
        <div class="col-6">
            <div class="et-listing-ad-form-help-area">
                <div class="et-ad-listing-help-area">
                    <h4>Adding a product listing</h4>
                    <p>Write a good title thinking about what your customer would search for.</p>
                    <p>Try to populate all the fields requested, including the brand logo of the product you want to
                        sell. Potential customers may recognise the brand you are selling before you.</p>
                    <p>The search on this website draws information from all these fields so the more you add the
                        higher the quality of your listing will be and the better the sales opportunity you are creating
                        for your company.</p>
                    <p style="margin-bottom: 10px; float: right">
                        <a id="" href="#et-account-help-popup" class="et-top-cut-button"><i
                                class="fa fa-solid fa-info"></i>Get help</a>
                        <a id="et-account-faq-popup" class="et-top-cut-button"><i
                                class="fa fa-solid fa-question"></i>FAQs</a>
                    </p>
                    <hr>
                    <h4>Getting stuck?</h4>
                    <p>
                        <?php echo do_shortcode('[et-sales-fine-print]') ?>
                    </p>
                </div>
                <div class="et-ad-listing-progress-bar">
                    <?php include (plugin_dir_path(__DIR__) . 'includes/discount-progress-bar.php'); ?>
                </div>
            </div>

        </div>
        <div class="col-6">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="title">
                    <?php _e('Title', 'equipmenttrader'); ?>:*
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Think of what your customer would search for when deciding your title.
                        </div>
                    </i>
                </label>

                <div class="col-sm-8">
                    <input type="text" class="form-control" id="title" name="title"
                        value="<?php echo $adData['title'] ?? ''; ?>"
                        placeholder="<?php _e('Title', 'equipmenttrader'); ?>" required />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="category">
                    <?php _e('Category', 'equipmenttrader'); ?>:*
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Select the best category you want your product advertised in</div>
                    </i>
                </label>
                <div class="col-sm-8">
                    <select class="form-control et-searchable-dropdown" id="category" name="category" required>
                        <option value="">
                            <?php _e('Select Category', 'equipmenttrader'); ?>
                        </option>
                        <?php foreach ($categories as $key => $value) { ?>
                            <option value="<?php echo $value["term_id"]; ?>" <?php if (isset($adData['category']) && $adData['category'] == $value["term_id"]) {
                                   echo 'selected';
                               } ?>     <?php
                                     if (isset($value['children'])) {
                                         echo "data-options='" . transform_object_for_frontend($value['children']) . "'";
                                     } ?>>
                                <?php echo $value['name']; ?>
                            </option>
                        <?php } ?>
                        <option value="Other" <?php if (isset($adData['category']) && $adData['category'] == 'Other') {
                            echo 'selected';
                        } ?>><?php _e('Other', 'equipmenttrader'); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row <?php if (!isset($adData['category']) || $adData['category'] != 'Other') {
                echo 'd-none';
            } ?>" id="other_cat_wrap">
                <label class="col-sm-4 col-form-label" for="other-category">&nbsp; <i
                        class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Please type in the name of your own category</div>
                    </i></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="other-category" name="other-category"
                        placeholder="<?php _e('Type your own category', 'equipmenttrader'); ?>"
                        value="<?php echo $adData['other-category'] ?? ''; ?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="sub-category">
                    <?php _e('Select Product Type', 'equipmenttrader'); ?>:*
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Please select the most appropriate product type for your listing</div>
                    </i>
                </label>
                <div class="col-sm-8">
                    <select class="form-control et-searchable-dropdown" id="sub-category" name="sub-category"
                        data-first="<?php _e('Select Product Type', 'equipmenttrader'); ?>" data-show-other="true">
                        <option value="">
                            <?php _e('Select Product Type', 'equipmenttrader'); ?>
                        </option>
                        <?php if (isset($adData['category'])) {
                            foreach ($categories as $key => $value) {
                                if ($adData['category'] == $value["term_id"]) {
                                    foreach ($value['children'] as $term) { ?>
                                        <option value="<?php echo $term['term_id']; ?>" <?php if (isset($adData['subcategory']) && $adData['subcategory'] == $term['term_id']) {
                                               echo 'selected';
                                           } ?>>
                                            <?php echo $term['name']; ?>
                                        </option>
                                    <?php }
                                }
                            }
                        } ?>
                        <option value="Other" <?php if (isset($adData['subcategory']) && $adData['subcategory'] == 'Other') {
                            echo 'selected';
                        } ?>><?php _e('Other', 'equipmenttrader'); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row <?php if (!isset($adData['subcategory']) || $adData['subcategory'] != 'Other') {
                echo 'd-none';
            } ?>" id="other_subcat_wrap">
                <label class="col-sm-4 col-form-label" for="other-subcategory">&nbsp; <i
                        class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Please type in the name of your own product type</div>
                    </i></label></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="other-subcategory" name="other-subcategory"
                        placeholder="<?php _e('Type your own sub-category', 'equipmenttrader'); ?>"
                        value="<?php echo $adData['other-subcategory'] ?? ''; ?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="brand">
                    <?php _e('Brand', 'equipmenttrader'); ?>:
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Brand is important – customers may search/recognize this before your
                            company name</div>
                    </i>
                </label>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control js-typeahead tt-query" id="brand" name="brand"
                        placeholder="Brand" value="<?php echo $adData['brand'] ?? ''; ?>"
                        data-options="<?php echo transform_object_for_frontend($brands) ?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="brand-logo">
                    <?php _e('Brand Logo', 'equipmenttrader'); ?>:
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">The logo of the brand you are selling is a great way to add credibility
                            to your listing</div>
                    </i>
                </label>
                <div class="align-items-center col-sm-8 d-flex flex-column gap-2">
                    <input type="file" class="form-control-file mb-1" id="brand-logo" name="brand-logo"
                        accept="image/*" />
                    <div class="d-flex align-items-center brand-logo-preview w-100">
                        <div class="col-10 p-0 position-relative d-flex">
                            <?php if (isset($adData['brand_logo']) && $adData['brand_logo']) { ?>
                                <?php $imgURL = is_int($adData['brand_logo']) ? wp_get_attachment_image_url($adData['brand_logo']) : $adData['brand_logo']; ?>
                                <img id="brand-logo-image" src="<?php echo $imgURL; ?>" alt="your brand logo"
                                    class="img-fluid" />
                                <input type="hidden" name="original-brand-logo-image" value="<?php echo $imgURL; ?>" />
                            <?php } else { ?>
                                <img id="brand-logo-image" src="#" alt="your brand logo" class="d-none img-fluid" />
                            <?php } ?>
                            <button id="clear-brand-logo" type="button"
                                class=" btn btn-secondary clear-brand-logo position-absolute px-2 py-0 d-none"
                                style="line-height: 18px; height: 20px; width: 20px; display: flex; justify-content: center;">
                                <p class="mb-0"><i class="fas fa-times fa-sm"></i></p>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="model">
                    <?php _e('Product Code/Model', 'equipmenttrader'); ?>:
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">A great way to help potential customers quickly search and find the item
                            they want to buy from you</div>
                    </i>
                </label>
                </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control js-typeahead tt-query" id="model" name="model"
                        placeholder="Product Code/Model" value="<?php echo $adData['model'] ?? ''; ?>"
                        data-options="<?php echo transform_object_for_frontend($models) ?>" />
                </div>
            </div>
            <div class="form-group">
                <label for="description">
                    <?php _e('Description', 'equipmenttrader'); ?>: *
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Knowing your product, provide the information you know will be important
                            to your customer.</div>
                    </i>
                </label>
                </label>
                <textarea class="form-control" id="description" name="description" required
                    rows="5"><?php echo $adData['description'] ?? ''; ?></textarea>
            </div>
            <label for="gallery-images">
                <?php _e('Add Images (Max 10):', 'equipmenttrader'); ?>
                <?php if (!isset($adData['images']) || !$adData['images']) { ?>*
                <?php } ?>
                <i class="fas fa-question-circle et-tooltip-trigger">
                    <div class="et-tooltip">First image will be the main image displayed</div>
                </i>
            </label>
            <input type="file" class="form-control-file" id="gallery-images" name="gallery-images" accept="image/*"
                multiple <?php if (!isset($adData['images'])) { ?>required<?php } ?> />
            <div id="gallery-preview" class="row my-3">
                <?php if (isset($adData['images']) && $adData['images']) { ?>
                    <?php foreach ($adData['images'] as $key => $image) { ?>
                        <?php $imgURL = is_int($image) ? wp_get_attachment_image_url($image) : $image; ?>
                        <img src="<?php echo $imgURL; ?> " class="col-3 p-1" data-linked-index="<?php echo $key; ?>" />
                        <input type="hidden" name="original-product-image[]" value="<?php echo $imgURL; ?>"
                            data-linked-index="<?php echo $key; ?>" />
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="quality">
                    <?php _e('Quality', 'equipmenttrader'); ?>: *
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Please select the option below that best describes the physical
                            condition of the product being advertised</div>
                    </i>
                </label>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" id="quality" name="quality" required>
                        <option value="New" <?php if (isset($adData['quality']) && $adData['quality'] == 'New') {
                            echo 'selected';
                        } ?>><?php _e('New', 'equipmenttrader'); ?>
                        </option>
                        <option value="Used" <?php if (isset($adData['quality']) && $adData['quality'] == 'Used') {
                            echo 'selected';
                        } ?>><?php _e('Used', 'equipmenttrader'); ?>
                        </option>
                        <option value="Refurbished" <?php if (isset($adData['quality']) && $adData['quality'] == 'Refurbished') {
                            echo 'selected';
                        } ?>><?php _e('Refurbished', 'equipmenttrader'); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="price-type">
                    <?php _e('Price', 'equipmenttrader'); ?>: *
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Enter a fixed price (good for search) or select POA (Price on
                            application) and discuss with you client when they call you.</div>
                    </i>
                </label>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" id="price-type" name="price-type" required>
                        <option value="Entered Price" <?php if (isset($adData['price-type']) && $adData['price-type'] == 'Entered Price') {
                            echo 'selected';
                        } ?>><?php _e('Entered Price', 'equipmenttrader'); ?>
                        </option>
                        <option value="POA" <?php if (isset($adData['price-type']) && $adData['price-type'] == 'POA') {
                            echo 'selected';
                        } ?>><?php _e('POA', 'equipmenttrader'); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row <?php if (isset($adData['price-type']) && $adData['price-type'] == 'POA') {
                echo 'd-none';
            } ?>" id="price-value-block">
                <label class="col-sm-4 col-form-label" for="price-value"><sub>Ex VAT</sub></label>
                <div class="col-sm-8">
                    <input class="form-control" id="price-value" name="price-value" type="currency" placeholder="0.00"
                        value="<?php echo $adData['price-value'] ?? ''; ?>" />
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="spec-sheets">
                    <?php _e('Spec Sheet (Max 3)', 'equipmenttrader'); ?>:
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Please upload a maximum of 3 spec sheets or brochures related to the
                            product, if applicable</div>
                    </i>
                </label>
                </label>
                <div class="col-sm-8">
                    <input type="file" class="form-control-file" id="spec-sheets" name="spec-sheets"
                        accept="image/jpg, image/jpeg, .pdf" multiple />
                    <div class="d-flex flex-wrap mw-100">
                        <?php if (isset($adData['specsheets']) && is_array($adData['specsheets'])) { ?>
                            <?php foreach ($adData['specsheets'] as $document) { ?>
                                <?php $attachment_url = is_int($document) ? wp_get_attachment_url($document) : $document;
                                $file_parts = explode('/', $attachment_url);
                                $fileName = end($file_parts);
                                ?>
                                <span
                                    class="align-items-center badge badge-light badge-pill d-flex gap-2 justify-content-center m-2 mw-100 p-0">
                                    <span class="mb-0 pl-3 spec-sheet-label"> <?php echo $fileName; ?> </span>
                                    <button class="btn border-0 pr-3 original-specsheets-button" type="button"><i
                                            class="fas fa-times fa-xs"></i></button>
                                    <input type="hidden" name="original-specsheets[]" value="<?php echo $attachment_url; ?>" />
                                </span>
                            <?php } ?>
                        <?php } ?>
                        <span id="file-tags" class="mw-100"></span>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="availability">
                    <?php _e('Availability', 'equipmenttrader'); ?>: *
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Please select the availability of the product being advertised</div>
                    </i>
                </label>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" id="availability" name="availability" required>
                        <?php foreach ($availability_options as $option) { ?>
                            <option value="<?php echo $option; ?>" <?php if (isset($adData['availability']) && $adData['availability'] == $option) {
                                   echo 'selected';
                               } ?>><?php echo $option; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group row et-form-featured">
                <label class="col-sm-4 col-form-label" for="featured">
                    <?php _e('Featured', 'equipmenttrader'); ?>:
                    <i class="fas fa-question-circle et-tooltip-trigger">
                        <div class="et-tooltip">Featured ads get more attention via special placement on the site, and
                            enhanced visual styling</div>
                    </i>
                    <br><sub>* costs of featuring are not discounted</sub>
                </label>
                </label>
                </label>
                <div class="col-sm-8">
                    <label for="featured-14" class="et-form-featured-option">
                        <input type="radio" name="featured" value="<?php echo $featured_option_1_price; ?>"
                            id="featured-14" <?php if (isset($adData['featured']) && $adData['featured'] == $featured_option_1_price) {
                                echo 'checked';
                            } ?>> <?php echo $featured_option_1_title; ?>
                    </label>
                    <label for="featured-30" class="et-form-featured-option">
                        <input type="radio" name="featured" value="<?php echo $featured_option_2_price; ?>"
                            id="featured-30" <?php if (isset($adData['featured']) && $adData['featured'] == $featured_option_2_price) {
                                echo 'checked';
                            } ?>> <?php echo $featured_option_2_title; ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col">
                    <span class="sub-total">
                        <?php

                        echo wc_price($price * ((100 - $discount) / 100));

                        ?>
                        <?php _e('inc VAT', 'equipmenttrader'); ?>
                    </span>
                </div>
                <div class="col text-right">
                    <?php _e('Max Discount', 'equipmenttrader'); ?>
                </div>
            </div>
        </div>
        <div class="col-12 et-ad-listing-progress-bar et-progress-bar-outer">
            <?php include (plugin_dir_path(__DIR__) . 'includes/discount-progress-bar.php'); ?>
        </div>

        <div class="col-6"></div>
        <div class="col-6">
            <div class="cart_totals">
                <h3>Cart totals</h3>
                <table cellspacing="0" class="shop_table shop_table_responsive">
                    <tbody>
                        <tr class="cart-subtotal">
                            <th>Subtotal</th>
                            <td data-title="Subtotal">
                                <?php echo wc_price($subTotal); ?>
                            </td>
                        </tr>
                        <tr class="cart-subtotal">
                            <th>Discount</th>
                            <td data-title="Discount">
                                <?php echo wc_price($discounts); ?>
                            </td>
                        </tr>
                        <tr class="order-total">
                            <th>Total</th>
                            <td data-title="Total">
                                <strong> <?php echo wc_price($total); ?> </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-6">
            <div class="et-ad-listing-form-bottom-help-area">
                <h4>Getting stuck?</h4>
            </div>
        </div>
        <div class="col-6 text-right">
            <?php if (count($cart) > 0) {
                paginate_array($cart);
            } ?>
        </div>

        <div class="col-6">
            <div class="et-ad-listing-form-bottom-help-area">
                <?php echo do_shortcode('[et-sales-fine-print]') ?>
            </div>
        </div>
        <div class="col-6 text-right">
            <input type="submit" name="submit-draft" value="<?php _e('Save Draft', 'equipmenttrader'); ?>"
                class="btn btn-secondary">
            <?php if (isset($cart[$current_key])) { ?>
                <input type="submit" name="update" value="<?php _e('Save', 'equipmenttrader'); ?>" class="btn btn-primary">
            <?php } else { ?>
                <input type="submit" name="submit" value="<?php _e('Save and Next >', 'equipmenttrader'); ?>"
                    class="btn btn-primary">
            <?php } ?>
            <input type="submit" name="go-to-cart" value="<?php _e('Go To Cart >>', 'equipmenttrader'); ?>"
                data-cart-url="<?php echo $woocommerce->cart->get_cart_url(); ?>" class="btn btn-secondary">
        </div>

    </div>
</form>
<form action="<?php echo get_permalink(wc_get_page_id('shop')); ?>" method="get"
    class="<?php echo $args["simple-form"] === "false" ? 'searchandfilter' : '' ?>" autocomplete="off">
    <input autocomplete="false" type="text" style="display:none;">
    <input type="hidden" name="order_by" value="<?php echo $order_by; ?>">
    <div>
        <ul>
            <?php if ($args["simple-form"] === "true") { ?>
                <li class="filterholder">
                    <div class="border-0 main-title-block pt-0 px-0">
                        <h3 class="m-0 rtin-specs-title">Filters:</h3>
                        <a class="et-filter-trigger"><i class="fas fa-chevron-right"></i></a>
                        <hr class="et-styled-hr">

                    </div>
                </li>
            <?php } ?>
            <div class="fieldsholder">
                <li>
                    <select id="category" name="category" class="postform et-searchable-dropdown" autocomplete="off">
                        <option value="">Product Categories</option>
                        <?php foreach ($categories as $key => $value) { ?>
                            <option value="<?php echo $value["term_id"]; ?>" <?php if (isset($value['children'])) {
                                   echo "data-options='" . transform_object_for_frontend($value['children']) . "'";
                               }
                               echo ($_GET["category"] == $value["term_id"]) ? " selected" : ''; ?>>
                                <?php echo $value['name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </li>
                <li>
                    <select id="sub-category" name="sub_category" class="postform et-searchable-dropdown"
                        data-first="Product Type" autocomplete="off">
                        <option value="">Product Type</option>
                    </select>
                </li>
                <li>
                    <select name="quality" class="postform et-searchable-dropdown" autocomplete="off">
                        <option value="">New/Used/Refurbished</option>
                        <option value="New" <?php if ($_GET["quality"] == "New") {
                            echo "selected";
                        } ?>>
                            <?php _e('New', 'equipmenttrader'); ?>
                        </option>
                        <option value="Used" <?php if ($_GET["quality"] == "Used") {
                            echo "selected";
                        } ?>>
                            <?php _e('Used', 'equipmenttrader'); ?>
                        </option>
                        <option value="Refurbished" <?php if ($_GET["quality"] == "Refurbished") {
                            echo "selected";
                        } ?>>
                            <?php _e('Refurbished', 'equipmenttrader'); ?>
                        </option>
                    </select>
                </li>
                <li>
                    <div class="row">
                        <div class="col-6">
                            <input type="number" class="postform" name="min_price" placeholder="Min Price"
                                value="<?php echo $_GET["min_price"]; ?>" autocomplete="off" />
                        </div>
                        <div class="col-6">
                            <input type="number" class="postform" name="max_price" placeholder="Max Price"
                                value="<?php echo $_GET["max_price"]; ?>" autocomplete="off" />
                        </div>
                    </div>
                </li>
                <li>
                    <input type="text" name="search" placeholder="What are you looking for?"
                        value="<?php echo $_GET["search"]; ?>" autocomplete="off">
                </li>
            </div>
        </ul>
    </div>
    <ul class="<?php echo $args["simple-form"] === "false" ? 'et-expanded-search' : '' ?>">
        <div class="fieldsholder">
            <li>
                <input type="text" class="postform js-typeahead tt-query" name="brand" placeholder="Brand"
                    data-options="<?php echo transform_object_for_frontend($brands) ?>"
                    value="<?php echo $_GET["brand"]; ?>" autocomplete="off" />
            </li>
            <li>
                <input type="text" class="postform js-typeahead tt-query" name="product_code"
                    placeholder="Product Code/Model" data-options="<?php echo transform_object_for_frontend($models) ?>"
                    value="<?php echo $_GET["product_code"]; ?>" />
            </li>
            <li>
                <select class="form-control" class="postform" name="availability" autocomplete="off">
                    <option value="">Availability</option>
                    <?php foreach ($availability_options as $option) { ?>
                        <option value="<?php echo $option; ?>" <?php if ($_GET["availability"] == $option) {
                               echo "selected";
                           } ?>>
                            <?php echo $option; ?>
                        </option>
                    <?php } ?>
                </select>
            </li>
            <li>
                <input class="postform js-typeahead tt-query" name="location" placeholder="Location"
                    data-options="<?php echo transform_object_for_frontend($locations) ?>"
                    value="<?php echo $_GET["location"]; ?>" autocomplete="off" />
            </li>
        </div>
    </ul>
    <ul class="et-search-bottom">
        <div class="fieldsholder">
            <li>

                <?php if ($args["simple-form"] === "false") { ?>
                    <a class="et-search-more-info more">
                        <span class="more">More options <i class="fas fa-chevron-right"></i></span>
                        <span class="less">Less options <i class="fas fa-chevron-left"></i></span>
                    </a>
                <?php } ?>

                <input type="submit" value="Find it!" />
                <input type="reset" value="Clear all filters">
            </li>
        </div>
    </ul>
</form>
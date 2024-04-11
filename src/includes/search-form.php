<form action="<?php echo get_post_type_archive_link($postType); ?>" method="get" class="searchandfilter">
    <div>
        <ul>
            <li>
                <select id="category" name="category" class="postform et-searchable-dropdown">
                    <option value="">All Product Categories</option>
                    <?php foreach ($categories as $key => $value) { ?>
                        <option value="<?php echo $key; ?>" <?php if (isset($value['children'])) {
                               echo "data-options='" . transform_object_for_frontend($value['children']) . "'";
                           } ?>     <?php if ($_GET["category"] === $key) {
                                     echo "selected";
                                 } ?>>
                            <?php echo $value['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </li>
            <li>
                <select id="sub-category" name="sub_category" class="postform et-searchable-dropdown"
                    data-first="Product Type">
                    <option value="">Product Type</option>
                </select>
            </li>
            <li>
                <select name="quality" class="postform et-searchable-dropdown">
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
                            value="<?php echo $_GET["min_price"]; ?>" />
                    </div>
                    <div class="col-6">
                        <input type="number" class="postform" name="max_price" placeholder="Max Price"
                            value="<?php echo $_GET["max_price"]; ?>" />
                    </div>
                </div>
            </li>
            <li>
                <input type="text" name="search" placeholder="What are you looking for?"
                    value="<?php echo $_GET["search"]; ?>">
            </li>
        </ul>
    </div>
    <ul class="et-expanded-search">
        <li>
            <input type="text" class="postform js-typeahead tt-query" name="brand" placeholder="Brand"
                data-options="<?php echo transform_object_for_frontend($brands) ?>"
                value="<?php echo $_GET["brand"]; ?>" />
        </li>
        <li>
            <input type="text" class="postform js-typeahead tt-query" name="product_code"
                placeholder="Product Code/Model" data-options="<?php echo transform_object_for_frontend($models) ?>"
                value="<?php echo $_GET["product_code"]; ?>" />
        </li>
        <li>
            <select class="form-control" class="postform" name="availability">
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
                value="<?php echo $_GET["location"]; ?>" />
        </li>
    </ul>
    <ul class="et-search-bottom">
        <li>
            <a class="et-search-more-info more">
                <span class="more">More options <i class="fas fa-chevron-right"></i></span>
                <span class="less">Less options <i class="fas fa-chevron-left"></i></span>
            </a>
            <input type="submit" value="Find it!" />
            <input type="reset" value="Clear all filters">
        </li>
    </ul>
</form>
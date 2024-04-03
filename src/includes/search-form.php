<form action="<?php echo get_post_type_archive_link($postType); ?>" method="get" class="searchandfilter">
    <div>
        <ul>
            <li>
                <select id="category" name="category" class="postform et-searchable-dropdown">
                    <option value="" selected="selected">All Ad Categories</option>
                    <?php foreach ($categories as $key => $value) { ?>
                        <option value="<?php echo $key; ?>" <?php if (isset($value['children'])) {
                               echo "data-options='" . transform_object_for_frontend($value['children']) . "'";
                           } ?>>
                            <?php echo $value['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </li>
            <li>
                <select id="sub-category" name="sub_category" class="postform et-searchable-dropdown"
                    data-first="All Sub Categories">
                    <option value="" selected="selected">All Sub Categories</option>
                </select>
            </li>
            <li>
                <select name="quality" class="postform et-searchable-dropdown">
                    <option value="" selected="selected">Any Quality</option>
                    <option value="New">
                        <?php _e('New', 'equipmenttrader'); ?>
                    </option>
                    <option value="Used">
                        <?php _e('Used', 'equipmenttrader'); ?>
                    </option>
                    <option value="Refurbished">
                        <?php _e('Refurbished', 'equipmenttrader'); ?>
                    </option>
                </select>
            </li>
            <li>
                <div class="row">
                    <div class="col-6">
                        <input type="number" class="postform" name="min_price" placeholder="Min Price" />
                    </div>
                    <div class="col-6">
                        <input type="number" class="postform" name="max_price" placeholder="Max Price" />
                    </div>
                </div>
            </li>
            <li>
                <input type="text" name="s" placeholder="What are you looking for?" value="">
            </li>
        </ul>
    </div>
    <ul class="et-expanded-search">
        <li>
            <input type="text" class="postform js-typeahead tt-query" name="brand" placeholder="Brand"
                data-options="<?php echo transform_object_for_frontend($brands) ?>" />
        </li>
        <li>
            <input class="postform" name="product_code" placeholder="Product Code" />
        </li>
        <li>
            <select class="form-control" class="postform" name="availability">
                <?php foreach ($availability_options as $option) { ?>
                    <option value="<?php echo $option; ?>">
                        <?php echo $option; ?>
                    </option>
                <?php } ?>
            </select>
        </li>
        <li>
            <input class="postform" name="location" placeholder="Location" />
        </li>
    </ul>
    <ul class="et-search-bottom">
        <li>
            <a class="et-search-more-info more"><span class="more">More options <i
                        class="fas fa-chevron-right"></i></span>
                <span class="less">Less options <i class="fas fa-chevron-left"></i></span></a>
            <input type="hidden" name="ofsubmitted" value="1"><input type="submit" value="Find it!">
        </li>
    </ul>
</form>
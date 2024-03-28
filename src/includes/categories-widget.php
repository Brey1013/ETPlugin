<?php
$listOfCategories = get_categories(
    array(
        'taxonomy' => 'ad_category',
        'orderby' => 'name',
        'show_count' => 0,
        'pad_counts' => 0,
        'hierarchical' => 0,
        'title_li' => '',
        'hide_empty' => 1
    )
);
$categories = build_category_hierarchy($listOfCategories);
$current_category_id = get_queried_object_id(); // Get the ID of the current category
?>

<div class="card category-card">
    <div class="card-header bg-white border-0 rounded-lg category-main-header" id="categoryHeading"
        data-toggle="collapse" data-target="#categoryCollapse" aria-expanded="true" aria-controls="categoryCollapse">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="mt-2 mb-0">
                    <?php _e('Category', 'equipmenttrader'); ?>
                </p>
                <div class="category-underline"></div>
            </div>
            <i class="fas fa-solid fa-chevron-down collapse-icon" aria-hidden="true"></i>
        </div>
    </div>

    <div id="categoryCollapse" class="card-body category-body collapse show" aria-labelledby="categoryHeading">
        <div class="category-filter-outer">
            <input class="form-control category-filter" type="text" placeholder="Filter Categories">
        </div>
        <div class="categories-container-outer">
            <div class="categories-container">
                <?php $i = 1; ?>
                <?php foreach ($categories as $key => $category) {
                    // Check if 'term_id' key exists in the current category
                    if (isset($category['term_id'])) {
                        $class = ($current_category_id == $category['term_id']) ? 'current-category' : '';
                        $show = ($current_category_id == $category['term_id']) ? 'show' : '';
                        $non = ($current_category_id !== $category['term_id']) ? 'd-none' : '';
                    } else {
                        // Handle the case where 'term_id' key is not present
                        $class = '';
                        $show = '';
                    }
                    ?>
                    <div class="accordion" id="mainCategoryAccordion<?php echo $i; ?>">
                        <div class="card bg-white border-0 main-category">
                            <div class="card-header category-header" id="mainCategory<?php echo $i; ?>Heading">
                                <h5 class="mb-0">
                                    <a class="btn btn-link btn-block text-left collapsed pl-0 pb-0 <?php echo $class; ?>"
                                        type="button" data-toggle="collapse"
                                        data-target="#mainCategory<?php echo $i; ?>Collapse" aria-expanded="false"
                                        aria-controls="mainCategory<?php echo $i; ?>Collapse">
                                        <p class="mb-0">
                                            <?php echo $category['name']; ?>
                                        </p>
                                    </a>
                                </h5>
                            </div>
                            <div id="mainCategory<?php echo $i; ?>Collapse" class="collapse <?php echo $class; ?>"
                                aria-labelledby="mainCategory<?php echo $i; ?>Heading"
                                data-parent="#mainCategoryAccordion<?php echo $i; ?>">
                                <div class="card-body sub-categories">
                                    <?php if (isset($category['children'])) { ?>
                                        <?php foreach ($category['children'] as $child) {
                                            $term_link = get_term_link($child['term_id']);
                                            ?>
                                            <a href="<?php echo $term_link; ?>"
                                                class="<?php echo ($current_category_id == $child['term_id']) ? 'current-category' : ''; ?>">
                                                <p>
                                                    <?php echo $child['name']; ?>
                                                </p>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $i++; ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
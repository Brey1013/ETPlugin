<div class="card category-card">
    <div class="card-header bg-white border-0 rounded-lg category-main-header" id="priceFilterHeading"
        data-toggle="collapse" data-target="#priceFilterCollapse" aria-expanded="true"
        aria-controls="priceFilterCollapse">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <p class="mt-2 mb-0">
                    <?php _e('Price Range', 'equipmenttrader'); ?>
                </p>
                <div class="category-underline"></div>
            </div>
            <i class="fas fa-solid fa-chevron-down collapse-icon" aria-hidden="true"></i>
        </div>
    </div>

    <div id="priceFilterCollapse" class="card-body category-body collapse show" aria-labelledby="priceFilterHeading">
        <div class="row">
            <div class="col-6">
                <input type="number" class="form-control" name="min_price" placeholder="min"
                    value="<?php echo $_GET["min_price"]; ?>" />
            </div>
            <div class="col-6">
                <input type="number" class="form-control" name="max_price" placeholder="max"
                    value="<?php echo $_GET["max_price"]; ?>" />
            </div>
        </div>
    </div>
</div>
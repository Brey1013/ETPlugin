<section class="classified-seller-info widget">
    <h3 class="widget-title">Seller Information</h3>
    <h4 class="rtin-author">
        <?php echo $display_name ?>
    </h4>

    <?php if ($billing_phone || $account_email) { ?>

        <div class="rtin-phone">

            <?php if ($billing_phone) { ?>

                <div class="numbers">
                    <i class="fa-solid fa-phone-flip"></i>
                    <a href="#" class="btn btn-link" data-reveal="<?php echo base64_encode('tel:' . $billing_phone) ?>">Show
                        Contact Number</p>
                </div>

            <?php } ?>

            <?php if ($account_email) { ?>

                <div class="emails">
                    <i class="fa-solid fa-envelope"></i>
                    <a href="#" class="btn btn-link" data-reveal="<?php echo base64_encode('mailto:' . $account_email) ?>">Show
                        Contact Email</a>
                </div>

            <?php } ?>

        </div>
    <?php } ?>

</section>
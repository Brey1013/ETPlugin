<section class="et-seller-widget classified-seller-info widget">
    <h3 class="widget-title">Seller Information</h3>
    <h4 class="rtin-author">
        <?php echo $display_name ?>
    </h4>

    <?php if ($billing_phone || $account_email) { ?>

        <div class="rtin-phone">

            <?php if ($billing_phone) { ?>

                <div class="numbers">
                    <i class="fas fa-phone"></i>
                    <a href="#" class="btn btn-link" data-reveal="<?php echo base64_encode('tel:' . $billing_phone) ?>">Show
                        Contact Number</a>
                </div>

            <?php } ?>

            <?php if ($account_email) { ?>

                <div class="emails">
                    <i class="far fa-envelope"></i>
                    <a href="#" class="btn btn-link" data-reveal="<?php echo base64_encode('mailto:' . $account_email) ?>">Show
                        Contact Email</a>
                </div>

            <?php } ?>

            <div class="contact">
                <i class="fas fa-building"></i>
                <a href="#" class="btn btn-link" data-toggle="modal" data-target="#contact-modal">Contact Seller</a>
            </div>

            <div class="modal fade" id="contact-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Contact
                                <?php echo $display_name; ?> now!
                            </h3>
                        </div>
                        <div class="modal-body">
                            <?php

                            echo do_shortcode('[wpforms id="1856" title="false"]');

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?>

</section>
<?php get_header(); ?>

<div id="primary" class="content-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-12">
                <div class="site-content-block">
                    <div class="main-content">

                        <?php

                        echo do_shortcode('[et-product-list]');

                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
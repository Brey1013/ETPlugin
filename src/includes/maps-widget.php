<?php
if ($full_address) { ?>

      <section class="border-0 classima-single-map shadow-none site-content-block">
            <div class="main-title-block">
                  <h3 class="rtin-specs-title">Location</h3>
                  <hr class="et-styled-hr">
            </div>
            <div class="main-content">
                  <iframe src="https://www.google.com/maps?q=<?php echo $full_address; ?>&output=embed" width="200"
                        height="250" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                  <p> <?php echo $full_address; ?> </p>
            </div>
      </section>

<?php }

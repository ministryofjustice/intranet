<?php

$regions = get_categories('taxonomy=region&type=regional_page');

if ($regions) :
    foreach ($regions as $region) :
        ?>

    <!-- c-headed-link-list-region-directory starts here -->
    <section class="c-headed-link-list c-headed-link-list--region-directory">
      <h2><a href="<?php echo get_permalink() . $region->slug; ?>"><?php echo $region->name; ?></a></h2>
    </section>
    <!-- c-headed-link-list-region-directory ends here -->

        <?php
    endforeach;
else :
    echo 'No regions available.';
endif;

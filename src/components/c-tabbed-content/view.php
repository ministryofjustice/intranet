<?php
  // NOTE: The 'data-tab-title current pulls from the $data variable but this will need to be modified a little to accomodate the fact that the $data variable needs to also bring in the wp content.'
?>
<section class="c-tabbed-content js-tabbed-content" data-tab-title="<?php echo slugify($data); ?>">
  <?php get_component('c-rich-text-block'); ?>
</section>

<?php
use MOJ\Intranet\HomepageBanners;
$sidebarBanner = HomepageBanners::getSidebarBanner(get_intranet_code());
?>
<!-- c-home-page-secondary starts here -->
<section class="c-home-page-secondary l-secondary" role="complementary">
  <?php get_component('c-my-moj'); ?>
  <?php if ($sidebarBanner && $sidebarBanner['visible']) { ?>
      <?php get_component('c-full-width-banner', $sidebarBanner); ?>
  <?php } ?>
  <?php get_component('c-my-work'); ?>
  <?php get_component('c-quick-links'); ?>
  <?php get_component('c-blog-feed'); ?>
  <?php get_component('c-social-links'); ?>
</section>
<!-- c-home-page-secondary ends here -->

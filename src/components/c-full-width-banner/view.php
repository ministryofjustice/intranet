<?php
use MOJ\Intranet\HomepageBanners;

$homepageTopBanner = HomepageBanners::getTopBanner(get_intranet_code());

if ($homepageTopBanner && $homepageTopBanner['visible']) { ?>

<!-- c-full-width-banner starts here -->
<section class="c-full-width-banner">
  <a href="<?php echo $homepageTopBanner['url'];?>">
      <img src="<?php echo $homepageTopBanner['image_url'];?>" alt="<?php echo $homepageTopBanner['alt'];?>">
  </a>
</section>
<!-- c-full-width-banner ends here -->
<?php }
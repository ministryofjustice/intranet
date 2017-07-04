<?php
use MOJ\Intranet\EmergencyBanner;

$emergencyBanner = EmergencyBanner::getEmergencyBanner(get_intranet_code());

if ($emergencyBanner && $emergencyBanner['visible']) { ?>

<!-- c-emergency-banner starts here -->
<section class="c-emergency-banner c-emergency-banner--<?php echo $emergencyBanner['type']; ?>">
  <div class="c-emergency-banner__meta">
    <h1><?php echo $emergencyBanner['title'];?></h1>
    <time datetime="2016-12-22"><?php echo $emergencyBanner['date'];?></time>
  </div>
  <div class="c-emergency-banner__content">
    <p><?php echo $emergencyBanner['message'];?></p>
  </div>
</section>
<!-- c-emergency-banner ends here -->
<?php }

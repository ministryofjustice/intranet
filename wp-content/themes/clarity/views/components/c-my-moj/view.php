<?php
use MOJ\Intranet\Agency;
$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
?>
<section class="c-my-moj">
  <h1 class="o-title o-title--section">My <?php echo $activeAgency['abbreviation'];?></h1>
  <?php get_component('c-agency-link-list'); ?>
  <?php get_component('c-app-list'); ?>
</section>

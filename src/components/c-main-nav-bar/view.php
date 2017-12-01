<?php 
  use MOJ\Intranet\Agency;

  $oAgency = new Agency();
  $activeAgency = $oAgency->getCurrentAgency();
  $headermenu = $activeAgency['shortcode'] . '-menu';

?>

<nav class="c-main-nav-bar">
  <div class="u-wrapper">
    <?php
      wp_nav_menu([ 'theme_location' => $headermenu ]);
    ?>
  </div>
</nav>

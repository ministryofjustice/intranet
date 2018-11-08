<?php 
  use MOJ\Intranet\Agency;

  $oAgency = new Agency();
  $activeAgency = $oAgency->getCurrentAgency();
  $headermenu = $activeAgency['shortcode'] . '-menu';

?>

<nav class="c-main-nav-bar">
  <div class="u-wrapper">
    <?php
      if ( has_nav_menu( $headermenu ) ) {
        wp_nav_menu([ 'theme_location' => $headermenu ]);
      }else{
        wp_nav_menu([ 'theme_location' => 'header-menu' ]);
      }
      
    ?>
  </div>
</nav>

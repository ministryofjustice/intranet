<div class="c-footer-nav">
  <div class="u-wrapper">
    <?php
    if ( has_nav_menu( 'footer-menu' ) ) {
        wp_nav_menu(['theme_location' => 'footer-menu']);
    }
    ?>
  </div>
</div

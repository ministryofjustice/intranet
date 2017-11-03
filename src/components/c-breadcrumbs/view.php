<!-- c-breadcrumbs starts here -->
<?php // obviously this is currently hard coded and will need to be made dynamic ?>
<section class="c-breadcrumbs">
  
  <?php
  /**
   * bcn_display() requires a third party WP plugin Breadcrumb NavXT.
   * Breadcrumbs are not a standard WP feature.
   * For info available at https://mtekk.us/code/breadcrumb-navxt/
   */
   ?>
   <?php if(function_exists('bcn_display'))
    {
        bcn_display();
    }
  ?>
</section>
<!-- c-breadcrumbs ends here -->

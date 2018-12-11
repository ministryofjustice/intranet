<?php
/**
*
* This pulls in the components needed for the homepage primary area.
* These componets are filtered on and off depending on agency via $activeAgency
* This ensures we can have a custom homepage.
*
*/
?>
<!-- c-home-page-primary starts here -->
<section class="c-home-page-primary l-full-page" role="main">

  <?php

  get_template_part('src/components/c-featured-news-list/view');
  get_template_part('src/components/c-news-list/view','home');
  get_template_part('src/components/c-blog-feed/view', 'home');
  get_template_part('src/components/c-popular-content/view');

  ?>

</section>
<!-- c-home-page-primary ends here -->

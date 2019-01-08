<!-- c-home-page-primary starts here -->
<section class="c-home-page-primary l-full-page" role="main">

  <?php

  get_template_part('src/components/c-featured-article-widget/view');
  get_template_part('src/components/c-news-list/view','home');
  get_template_part('src/components/c-blog-feed/view', 'home');
  get_template_part('src/components/c-popular-content/view');

  ?>

</section>
<!-- c-home-page-primary ends here -->

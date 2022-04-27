<!-- c-news-article starts here -->
<article class="c-news-article l-main">

  <section class="l-primary">
    <h1 class="o-title o-title--headline"><?php echo get_the_title(); ?></h1>

    <?php

    get_template_part('src/components/c-article-byline/view', 'news');
    get_template_part('src/components/c-article-featured-image/view', 'news');

    if (has_excerpt()) {
        get_template_part('src/components/c-article-excerpt/view');
    }
    get_template_part('src/components/c-rich-text-block/view');

    ?>
  </section>

  <aside class="l-secondary">
    <h1 class="o-title">Recent news</h1>
    <?php get_template_part('src/components/c-news-list/view', 'regional_news'); ?>
  </aside>

</article>
<!-- c-news-article ends here -->

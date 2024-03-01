<!-- c-news-article starts here -->
<article class="c-news-article l-main">

  <section class="l-primary">
    <h1 class="o-title o-title--headline"><?php echo get_the_title(); ?></h1>
    <?php
    get_template_part('src/components/c-article-byline/view', 'news');
    get_template_part('src/components/c-article-excerpt/view');
    get_template_part('src/components/c-rich-text-block/view');
    ?>

  </section>

</article>
<!-- c-news-article ends here -->

<?php

/*
* Genertic blog body
*
*/

?>
<!-- c-article starts here -->
<article class="c-article">

    <h1 class="o-title o-title--page"><?= get_the_title() ?></h1>
  
    <div class="l-primary">
        <?php
        get_template_part('src/components/c-article-byline/view');
        get_template_part('src/components/c-rich-text-block/view');
        ?>
            
    </div>

    <aside class="l-secondary">
    <?php
        echo '<h2 class="o-title">Recent blog posts</h2>';
        $blog_posts_per_page = '5';
        get_post_api($blog_posts_per_page);
    ?>
    </aside>

</article>
<!-- c-article ends here -->

<?php

/*
* Single team blog article section
*
*/

?>
<!-- c-team-blog-article starts here -->
<article class="c-team-blog-article l-main">

    <h1 class="o-title o-title--headline">
        <?= get_the_title() ?>
    </h1>

    <?php

    get_template_part('src/components/c-article-byline/view');

    if (has_excerpt()) {
        get_template_part('src/components/c-article-excerpt/view');
    }

    get_template_part('src/components/c-rich-text-block/view');

    ?>

</article>
<!-- c-team-blog-article ends here -->

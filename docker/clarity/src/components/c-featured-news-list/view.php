<?php
use MOJ\Intranet\News;

$oNews = new News();
$featuredNews = $oNews->getFeaturedNews(get_intranet_code());

if (!empty ($featuredNews) )
{
    ?>
    <section class="c-featured-news-list">
        <?php
        foreach ($featuredNews['results'] as $post) {
            get_component('c-article-item', $post, 'show_date_and_excerpt');
        }
        ?>
    </section>

    <?php
}

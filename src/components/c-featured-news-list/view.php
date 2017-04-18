<?php
use MOJ\Intranet\News;

$featuredNews = News::getFeaturedNews(get_intranet_code());

if ($featuredNews->have_posts() )
{
    $posts = $featuredNews->get_posts();
    ?>
    <section class="c-featured-news-list">
        <?php
        foreach ($posts as $post) {

            get_component('c-article-item', array ('post' => $post), 'featured_news');
        }
        ?>
    </section>

    <?
}

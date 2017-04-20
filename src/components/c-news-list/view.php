<?php
use MOJ\Intranet\News;

$latestNews = News::getLatestNews(get_intranet_code());

if ($latestNews->have_posts() )
{
    $posts = $latestNews->get_posts();
    ?>
    <div class="c-news-list">
        <?php
        foreach ($posts as $post) {

            get_component('c-article-item', array ('post' => $post));
        }
        ?>
    </div>

    <?php
}


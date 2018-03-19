<?php
use MOJ\Intranet\News;

$oNews = new News();
//Todo: Pass it as part of $data from the container
$options = array (
    'page' => 1,
    'per_page' => MAX_HOMEPAGE_NEWS,
);

$latestNews = $oNews->getNews($options, true);

if (!empty($latestNews) )
{
    ?>
    <div class="c-news-list">
        <?php
        foreach ($latestNews['results'] as $postItem) {
            get_component('c-article-item', $postItem, 'show_date');
        }
        ?>
    </div>
    <?php
}


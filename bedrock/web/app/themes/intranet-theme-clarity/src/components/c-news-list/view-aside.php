<?php
use MOJ\Intranet\News;

$oNews = new News();

$options = [
    'page' => 1,
    'per_page' => 6,
];

$post_id = get_the_ID();
$latestNews = $oNews->getNews($options, true);

if (!empty($latestNews)): ?>
    <div class="c-news-list">
        <?php
        foreach ($latestNews['results'] as $postItem) {
          // Remove listed item if we are actuatlly on that news item (we dont' want the story duplicated)
          if ($postItem["id"] === $post_id) {
            $postItem = '';
          } else {
            get_component('c-article-item', $postItem, 'show_date');
          }
        }
        ?>
    </div>
<?php endif; ?>

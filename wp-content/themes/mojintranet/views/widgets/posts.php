<?php if (!defined('ABSPATH')) die();

/* Blog posts widget
 * Requires $posts array
 */

?>

<div class="posts-widget">
  <h2 class="posts-heading">Blog</h2>
  <ul class="posts-list">
    <?php foreach($posts as $post): ?>
      <li class="results-item">
        <div class="post-thumbnail-container">
          <a href="<?=$post['url']?>">
            <img class="post-thumbnail" src="<?=$post['thumbnail_url']?>" alt="<?=$post['thumbnail_alt_text']?>" />
          </a>
        </div>
        <div class="post-content">
          <p class="post-meta">
            <time class="post-date"><?=$post['human_date']?></time> by <?=$post['authors'][0]['name']?>
          </p>
          <h3 class="post-title">
            <a href="<?=$post['url']?>"><?=$post['title']?></a>
          </h3>
        </div>
      </li>
    <?php endforeach ?>
  </ul>

  <p class="see-all-container">
    <a href="<?=$see_all_posts_url?>">See all posts</a>
  </p>
</div>

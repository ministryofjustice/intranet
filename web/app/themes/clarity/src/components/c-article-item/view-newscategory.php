<?php
  use MOJ\Intranet\NewsCategory;
?>
  <!-- c-news-category-item-byline starts here -->
  <article class="c-article-item js-article-item">
    <?php if ($featured_img_url) { ?>
    <a href="<?php echo $news_link; ?>" class="thumbnail">
        <img src="<?php echo $featured_img_url; ?>" alt="">
    </a>
    <?php } elseif (! empty($author_image)) { ?>
    <a tabindex="-1" aria-hidden="true" href="<?php echo $news_link; ?>" class="thumbnail">
        <img src="<?php echo $author_image; ?>" alt>
    </a>
    <?php } ?>
      <div class="content">
          <h1>
              <a href="<?php echo $news_link; ?>"><?php echo $news_title; ?></a>
          </h1>
          <div class="meta">
              <span class="c-article-item__dateline"><?php echo get_gmt_from_date($news_date, 'j M Y'); ?></span>
          </div>
          <div class="c-article-excerpt">
              <p><?php echo $news_excerpt; ?></p>
          </div>
      </div>
  </article>
<?php

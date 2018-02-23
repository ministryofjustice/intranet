<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container"
     data-page-id="<?=$id?>"
     data-region="<?=$region?>">

  <div class="grid content-container">
    <div class="col-lg-3 col-md-4 col-sm-12">
      <nav class="menu-list-container">
        <ul class="menu-list"></ul>
      </nav>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-12">
      <h1 class="page-title"><?=$title?></h1>

      <?php $this->view('widgets/news_list/main-news', $news_widget) ?>
      <?php $this->view('widgets/events/main', $events_widget) ?>
    </div>
  </div>

  <?php $this->view('modules/side_navigation') ?>
</div>
<style>
/* Add css Clarity overrides here. */

h1 {
  margin-top: 0;
  line-height: 1;
}

p + p {
  margin-top: 5px;
}

.c-article-item h1 {
    font-size: 1.2rem;
    font-family: nta,sans-serif;
    font-weight: 700;
}

.c-article-item__dateline, .c-article-list .c-article-item__byline, .c-blog-feed .c-article-item__byline {
    font-size: 1rem;
    color: #6f777b;
}

.c-article-item .c-article-exceprt p {
    font-size: 1rem;
    margin-bottom: 0;
}

.category-name {
    margin-top: 0;
}

.c-article-item {
    padding: 0.5rem 0;
    margin-bottom: 1.4rem;
    border-bottom: 1px solid hsla(0,0%,59%,.561);
    position: relative;
    display: inline-block;
    width: 100%;
}

.c-article-exceprt p {
    line-height: 1;
}

.c-article-item .content {
    overflow: hidden;
}

.c-article-item .thumbnail {
    width: 100px;
    height: 100px;
    margin-right: 20px;
    float: left;
}

.c-app-list a, .c-article-item a {
    text-decoration: none;
}

.c-article-item img {
    width: 100%;
    max-width: 100%;
    height: auto;
}
</style>

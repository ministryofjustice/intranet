<?php if (!defined('ABSPATH')) die(); ?>
<?php
$id = get_the_ID();
$terms = get_the_terms($id, 'region');

if (is_array($terms)){
  foreach ($terms as $term) {
      $region_id = $term->term_id;
  }
}

?>
<div class="template-container"
  data-page-id="<?=$id?>"
  data-template-uri="<?=$template_uri?>"
  data-page-base-url="<?=$page_base_url?>"
  data-region="<?=$region?>">

  <div class="grid">
    <div class="col-lg-3 col-md-4 col-sm-12">
      <nav class="menu-list-container">
        <ul class="menu-list"></ul>
      </nav>
    </div>

    <div class="col-lg-9 col-md-8 col-sm-12">
      <ul class="results"></ul>

      <div class="posts-widget">
        <h2 class="category-name">Updates</h2>
        <div id="content">
          <?php get_region_news_api($region_id); ?>
        </div>
      </div>

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

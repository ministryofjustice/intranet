<?php

use MOJ\Intranet\Agency;

/*
* Template Name: News archive
*/

defined('ABSPATH') || exit;

get_header();

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

// TODO: Add this to a function later
get_template_part('src/components/c-article-item/view-news-feed.tpl');
// TODO: Add this to a function later
require_once 'inc/pagination.tpl.php';

?>
  <main role="main" id="maincontent" class="u-wrapper l-main t-article-list">
    <h1 class="o-title o-title--page"><?php the_title(); ?></h1>
    <div class="l-secondary">
      <?php get_template_part('src/components/c-content-filter/view', null, ['post_type' => 'news', 'template' => 'view-news-feed-template']); ?>
    </div>
    <div role="status" class="l-primary">
      <h2 class="o-title o-title--section" id="title-section">Latest</h2>
      <div id="content">
        <?php get_news_api('news'); ?>
      </div>
      <?php get_pagination('news'); ?>
    </div>
  </main>

<?php
get_footer();

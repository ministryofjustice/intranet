<?php if (!defined('ABSPATH')) die(); ?>

<div class="news-single">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title"><?=$title?></h1>

      <ul class="info-list">
        <li>
          <span>Content owner:</span>
          <span><?=$author?></span>
        </li>
        <li>
          <span>History:</span>
          <span>Published <?=$human_date?></span>
        </li>
        <li>
          <span>Department:</span>
          <span>MOJ</span>
        </li>
      </ul>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="excerpt">
        <?=$excerpt?>
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4">
      <img src="<?=$thumbnail[0]?>" class="img img-responsive" alt="<?=$title?>" />
    </div>

    <div class="col-lg-8">
      <div class="content editable">
        <?=$content?>
      </div>

      <ul class="content-nav">
        <li class="previous">
          <span>
            <? if($prev_news_exists): ?>
              <a href="<?=$prev_news_url?>">
                Previous
              </a>
            <? else: ?>
              Previous
            <? endif ?>
          </span>
        </li>

        <li class="next">
          <span>
            <? if($next_news_exists): ?>
              <a href="<?=$next_news_url?>">
                Next
              </a>
            <? else: ?>
              Next
            <? endif ?>
          </span>
        </li>
      </ul>

      <div class="content-info">
        <p>
          <a href="#" class="print">Print news</a>
          <a href="#" class="share">Share this page by email</a>
        </p>

        <p class="report-issue">
          <a href="#">Is there anything wrong with this page?</a>
        </p>

        <p class="last-updated">Last updated: 14 October 2014</p>
      </div>
    </div>
  </div>
</div>

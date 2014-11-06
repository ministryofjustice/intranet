<?php if (!defined('ABSPATH')) die(); ?>

<div class="news-single">
  <h1 class="page-title"><?=$title?></h1>

  <ul class="info-list">
    <li>
      <span>From:</span>
      <span><?=$author?></span>
    </li>
    <li>
      <span>History:</span>
      <span>Published <?=$human_date?></span>
    </li>
    <li>
      <span>Department:</span>
      <span>HMCTS</span>
    </li>
  </ul>

  <div class="row">
    <div class="col-md-12">
      <div class="excerpt">
        <?=$excerpt?>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <img src="<?=$thumbnail[0]?>" class="img img-responsive" alt="<?=$title?>" />
    </div>

    <div class="col-md-8">
      <div class="content">
        <?=$content?>
      </div>

      <ul class="news-nav">
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
    </div>
  </div>
</div>

<?php if (!defined('ABSPATH')) die(); ?>

<div class="news-single">
  <?php $this->view('pages/news_single/election_banner', $election_banner) ?>

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
          <span>MoJ</span>
        </li>
      </ul>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-11 col-md-11 col-sm-12">
      <div class="excerpt">
        <?=$excerpt?>
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="story-image">
        <img src="<?=$thumbnail[0]?>" class="img img-responsive" alt="<?=$title?>" />
      </div>
    </div>

    <div class="col-lg-8 col-md-4 col-sm-12">
      <div class="content editable">
        <?=$content?>
      </div>

      <ul class="content-nav grid">
        <li class="previous col-lg-6 col-md-6 col-sm-6">
          <? if($prev_news_exists): ?>
            <a href="<?=$prev_news_url?>" aria-labelledby="prev-page-label">
              <span class="nav-label" id="prev-page-label">
                Previous
              </span>
            </a>
          <? else: ?>
            <span class="nav-label">
              Previous
            </span>
          <? endif ?>
        </li>

        <li class="next col-lg-6 col-md-6 col-sm-6">
          <? if($next_news_exists): ?>
            <a href="<?=$next_news_url?>" aria-labelledby="next-page-label">
              <span class="nav-label" id="next-page-label">
                Next
              </span>
            </a>
          <? else: ?>
            <span class="nav-label">
              Next
            </span>
          <? endif ?>
        </li>
      </ul>

<!--      <div class="content-info">
        <p>
          <a href="#" class="print">Print news</a>
          <a href="#" class="share">Share this page by email</a>
        </p>

        <p class="report-issue">
          <a href="#">Is there anything wrong with this page?</a>
        </p>

        <p class="last-updated">Last updated: 14 October 2014</p>
      </div>-->
    </div>
  </div>
</div>

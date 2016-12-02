<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container" data-post-id="<?=$id?>">
  <?php $this->view('pages/news_single/election_banner', $election_banner) ?>

  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title"><?=$title?></h1>

      <div class="byline">
        <?php if (!empty($author_thumbnail_url)): ?>
          <img class="author-thumbnail" src="<?=$author_thumbnail_url?>" alt="" />
        <?php endif ?>
        <ul class="info-list">
          <li>
            <span><?=$author?></span>
            <?php if ($job_title): ?>
              <span>, <?=$job_title?></span>
            <?php endif ?>
          </li>
          <li>
            <span><time><?=$human_date?></time></span>
          </li>
        </ul>
      </div>
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
    <?php if($thumbnail): ?>
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="story-image">
          <img src="<?=$thumbnail?>" class="img img-responsive" alt="<?=$thumbnail_alt_text?>" />
        </div>
      </div>
    <?php endif ?>

    <div class="col-lg-8 col-md-8 col-sm-12">
      <div class="content editable">
        <?=$content?>
      </div>

      <?php $this->view('modules/single_page_pagination') ?>
    </div>
  </div>
</div>

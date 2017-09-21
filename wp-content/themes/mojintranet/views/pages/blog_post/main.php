<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container" data-post-id="<?=$id?>">
  <div class="main-screen">
    <div class="grid">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <h1 class="page-title"><?=$title?></h1>

        <div class="validation-summary-container"></div>

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
      <div class="col-lg-8 col-md-8 col-sm-12">
        <div class="content editable">
          <?=$content?>
        </div>
      </div>
    </div>

    <?php $this->view('modules/social_media_bar', $media_bar) ?>
    </div>

    <?php $this->view('modules/comments/main') ?>
  </div>

  <?php $this->view('pages/user/activate/request_activation_link_confirmation') ?>
</div>

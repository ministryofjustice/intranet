<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
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
          <span>Published <time><?=$human_date?></time></span>
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
    <?php if($thumbnail): ?>
      <!--
      <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="story-image">
          <img src="<?=$thumbnail?>" class="img img-responsive" alt="<?=$thumbnail_alt_text?>" />
        </div>
      </div>
      -->
    <?php endif ?>

    <div class="col-lg-8 col-md-8 col-sm-12">
      <div class="content editable">
        <?=$content?>
      </div>

      <ul class="content-nav grid">
        <li class="previous col-lg-6 col-md-6 col-sm-6">
          <?php if($prev_post_exists): ?>
            <a href="<?=$prev_post_url?>" aria-labelledby="prev-page-label">
              <span class="nav-label" id="prev-page-label">
                Previous
              </span>
            </a>
          <?php else: ?>
            <span class="nav-label">
              Previous
            </span>
          <?php endif ?>
        </li>

        <li class="next col-lg-6 col-md-6 col-sm-6">
          <?php if($next_post_exists): ?>
            <a href="<?=$next_post_url?>" aria-labelledby="next-page-label">
              <span class="nav-label" id="next-page-label">
                Next
              </span>
            </a>
          <?php else: ?>
            <span class="nav-label">
              Next
            </span>
          <?php endif ?>
        </li>
      </ul>
    </div>
  </div>
</div>

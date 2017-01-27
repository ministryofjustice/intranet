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

        <div class="item-row"></div>

        <?php $this->view('modules/single_page_pagination') ?>
      </div>
    </div>

    <div class="grid">
      <div class="col-lg-8 col-md-12 col-sm-12">
        <ul class="social-actions post-social-actions">
          <?php if ($comments_on): ?>
            <li class="comments-count">
              <span class="icon"></span>
              <span class="count"></span>
            </li>
          <?php endif ?>

          <li class="like-container" data-likes-count="<?=$likes_count?>" data-post-type="post" data-post-id="<?=$id?>">
            <a class="like-link" href="#">
              <span class="like-icon icon"></span>
              <span class="like-description"></span>
            </a>
          </li>

          <li class="share-container">
            <span class="share-via-email-icon"></span>
            <a class="share-via-email"
               href="mailto:"
               data-title="<?=htmlspecialchars($title)?>"
               data-date="<?=htmlspecialchars($human_date)?>"
               data-body="<?=htmlspecialchars($share_email_body)?>">Share this post by email</a>
          </li>
        </ul>
      </div>
    </div>

    <div id="comments-box">
      <?php if ($comments_on): ?>
        <?php if ($comments_open): ?>
          <?php $this->view('modules/request_activation_link/form') ?>
        <?php else: ?>
          <div class="grid">
            <div class="col-lg-8 col-md-12 col-sm-12">
              <h3>Comments are now closed</h3>
              <p class="secondary-action">
                <a href="<?=$commenting_policy_url?>">MoJ commenting policy</a>
              </p>
            </div>
          </div>
        <?php endif ?>

        <div class="grid">
          <div class="col-lg-8 col-md-12 col-sm-12">
            <div class="comments-container">
              <h3 class="leave-a-comment">
                <span class="logged-in-only">Comment on this article</span>
                <span class="not-logged-in-only">
                  <a class="sign-in-link" href="">Sign in</a> to leave a comment
                </span>
              </h3>

              <p class="posting-as logged-in-only">
                Posting as
                <span class="display-name"></span> |
                <a href="<?=wp_logout_url($_SERVER['REQUEST_URI'])?>#comments-box">Not you?</a>
              </p>

              <div class="comment-form-container logged-in-only"></div>

              <h3>Comments</h3>
              <ul class="comments-list"></ul>

              <div class="load-more-container loading">
                <input type="button" class="load-more cta cta-plain" value="Load more comments" />
                <span class="loading-msg">Loading...</span>
              </div>

              <?php $this->view('pages/blog_post/partials/bad_words_error') ?>
              <?php $this->view('pages/blog_post/partials/comment', $comment_data) ?>
              <?php if ($comments_open): ?>
                <?php $this->view('pages/blog_post/partials/comment_form'); ?>
              <?php endif ?>
              <?php $this->view('modules/validation/validation') ?>
            </div>
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>

  <?php $this->view('pages/user/activate/request_activation_link_confirmation') ?>
</div>

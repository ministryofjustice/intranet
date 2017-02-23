<div id="comments">
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
          <?php if ($comments_open): ?>
            <h3 class="leave-a-comment">
              <span class="logged-in-only">Comment on this page</span>
            </h3>

            <p class="posting-as logged-in-only">
              You're posting as
              <span class="display-name"></span> |
              <a href="<?=$logout_url?>#comments">Not you?</a>
            </p>

            <div class="comment-form-container logged-in-only"></div>
          <?php endif ?>

          <h3>Comments</h3>
          <ul class="comments-list"></ul>

          <div class="load-more-container loading">
            <input type="button" class="load-more cta cta-plain" value="Load more comments" />
            <span class="loading-msg">Loading...</span>
          </div>

          <p class="no-comments-msg">There are no comments yet. Be the first to leave a comment.</p>

          <?php $this->view('modules/comments/comment') ?>
          <?php if ($comments_open): ?>
            <?php $this->view('modules/comments/comment_form'); ?>
          <?php endif ?>
          <?php $this->view('modules/validation/validation') ?>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>

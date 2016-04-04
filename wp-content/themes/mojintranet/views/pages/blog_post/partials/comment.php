<?php if (!defined('ABSPATH')) die(); ?>

<script data-name="comment-item" type="text/x-partial-template">
  <li class="comment">
    <div class="top">
      <span class="author"></span>
      <span class="dash">&mdash;</span>
      <time class="datetime"></time>
    </div>

    <div class="content-box">
      <p class="content"></p>

      <ul class="social-actions">
        <li class="reply-info">
          <span class="icon"></span>
          <a class="reply-btn" href="">Reply</a>
        </li>
        <li class="like-container" data-likes-count="" data-post-type="comment" data-post-id="">
          <a class="like-link" href="#">
            <span class="like-icon icon"></span>
            <span class="like-description"></span>
          </a>
        </li>
      </div>
    </ul>

    <div class="reply-form-container"></div>

    <a href="" class="toggle-replies">View all replies</a>
    <span class="loading-replies">Loading...</span>

    <ul class="replies-list"></ul>
  </li>
</script>

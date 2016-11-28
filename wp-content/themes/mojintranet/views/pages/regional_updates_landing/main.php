<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container"
  data-page-id="<?=$id?>"
  data-template-uri="<?=$template_uri?>"
  data-page-base-url="<?=$page_base_url?>">
  <div class="grid">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <h1 class="page-title"><?=$title?></h1>
      <?=$content?>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-3 col-md-4 col-sm-12">
      <nav class="menu-list-container">
        <ul class="menu-list"></ul>
      </nav>
    </div>

    <div class="col-lg-9 col-md-8 col-sm-12">
      <ul class="results"></ul>

      <ul class="content-nav grid">
        <li class="previous disabled col-lg-6 col-md-6 col-sm-6">
          <a href="" aria-labelledby="prev-page-label">
            <span class="nav-label" id="prev-page-label">Previous page</span>
            <span class="page-info">
              <span class="prev-page"></span>
              of
              <span class="total-pages"></span>
            </span>
          </a>

        </li>

        <li class="next disabled col-lg-6 col-md-6 col-sm-6">
          <a href="" aria-labelledby="next-page-label">
            <span class="nav-label" id="next-page-label">Next page</span>
            <span class="page-info">
              <span class="next-page"></span>
              of
              <span class="total-pages"></span>
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <?php $this->view('modules/news_landing') ?>
  <?php $this->view('modules/side_navigation') ?>
</div>

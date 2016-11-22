<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container"
     data-page-id="<?=$id?>"
     data-children-data="<?=$children_data?>"
     data-region="<?=$region?>">

  <div class="grid content-container">
    <div class="col-lg-3 col-md-4 col-sm-12">
      <nav class="menu-list-container">
        <ul class="menu-list"></ul>
      </nav>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-12">
      <h1 class="page-title"><?=$title?></h1>

      <h2 class="category-name">Latest</h2>

      <div class="news-list-widget news-widget" data-news-type="regional">
        <div class="grid">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <ul class="news-list"
                data-skeleton-screen-count="2"
                data-skeleton-screen-type="standard"
                ></ul>
          </div>

          <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="no-news-message">
              No news found
            </p>
            <p class="see-all-container">
              <a href="">See all updates</a>
            </p>
          </div>
        </div>

        <?php $this->view('widgets/news_list/news_item') ?>
      </div>

      <div class="events-widget" data-events-type="regional">
        <h2 class="category-name">Events</h2>

        <ul class="events-list grid"
            data-skeleton-screen-count="2"
            data-skeleton-screen-type="standard"
            data-skeleton-screen-classes="col-lg-6 col-md-12 col-sm-12"></ul>

        <p class="no-events-message">
          No events found
        </p>
        <p class="see-all-container">
          <a href="">See all events</a>
        </p>

        <?php $this->view('widgets/events/event_item') ?>
      </div>
    </div>
  </div>

  <div class="template-partial" data-name="menu-item">
    <li class="menu-item">
      <div class="menu-item-container">
        <a href="" class="menu-item-link"></a>
        <button href="#" class="dropdown-button">Expand</button>
      </div>
      <ul class="children-list">
      </ul>
    </li>
  </div>

  <div class="template-partial" data-name="child-item">
    <li class="child-item">
      <a href="" class="child-item-link"></a>
    </li>
  </div>
</div>

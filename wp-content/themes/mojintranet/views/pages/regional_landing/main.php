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

      <?php $this->view('widgets/news_list/main', $news_widget) ?>
      <?php $this->view('widgets/events/main', $events_widget) ?>
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
